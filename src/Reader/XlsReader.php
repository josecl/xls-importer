<?php

declare(strict_types=1);

namespace Josecl\XlsImporter\Reader;

use Generator;
use Josecl\XlsImporter\SheetNotFoundException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;

class XlsReader implements Reader
{
    private string $sheetname;

    private int $chunkSize = 5000;

    private Spreadsheet|null $spreadsheet = null;

    private int $from = 1;

    private ?int $to = null;

    public function __construct(private string $filename)
    {
    }

    public function open(string $sheetname, int $from = 1, int $to = null): void
    {
        $this->close();
        $this->sheetname = $sheetname;
        $this->from = $from;
        $this->to = $to;

        try {
            $this->getSpreadsheet($from)->getActiveSheet();
        } catch (Exception $exception) {
            throw new SheetNotFoundException("Sheet not found: {$this->sheetname}", 0, $exception);
        }
    }

    public function close(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->__destruct();
            $this->spreadsheet = null;
        }
    }

    public function getSheetNames(): array
    {
        if ($this->spreadsheet) {
            return $this->spreadsheet->getSheetNames();
        }

        $reader = IOFactory::createReader('Xls');
        $reader->setReadDataOnly(true);
        // Empty ReadFilter to avoid read data into memory
        $reader->setReadFilter(new PhpSpreadsheetChunkReadFilter(0, 0));

        return $reader->load($this->filename)->getSheetNames();
    }

    public function fetchRow(): Generator
    {
        $rowIndex = $this->from;
        $rowIndexInChunk = 1;

        $worksheet = $this->getSpreadsheet($rowIndex)->getActiveSheet();

        $maxRowIndex = $worksheet->getHighestDataRow();

        while ($rowIndex <= $maxRowIndex || $rowIndexInChunk > $this->chunkSize) {
            if ($rowIndexInChunk > $this->chunkSize) {
                $rowIndexInChunk = 1;
                $this->close();
                $worksheet = $this->getSpreadsheet($rowIndex)->getActiveSheet();
                $maxRowIndex = $worksheet->getHighestDataRow();

                if ($maxRowIndex < $rowIndex) {
                    return null;
                }
            }


            $cells = $this->getRowValues(new RowCellIterator($worksheet, $rowIndex, 'A', null));

            yield $rowIndex => $cells;

            $rowIndex++;
            $rowIndexInChunk++;
        }
    }

    private function getSpreadsheet(int $from = 1): Spreadsheet
    {
        if ($this->spreadsheet) {
            return $this->spreadsheet;
        }


        $to = $from + $this->chunkSize - 1;
        if ($this->to) {
            $to = min($to, $this->to);
        }

        $reader = IOFactory::createReader('Xls');
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly($this->sheetname);

        // Filter to avoid read data into memory
        $reader->setReadFilter(new PhpSpreadsheetChunkReadFilter($from, $to));

        $this->spreadsheet = $reader->load($this->filename);

        return $this->spreadsheet;
    }

    private function getRowValues(RowCellIterator $cellIterator): array
    {
        $cells = [];
        foreach ($cellIterator as $cell) {
            $value = $cell->getValue();
            $type = $cell->getDataType();

            $value = match ($type) {
                DataType::TYPE_BOOL => (bool) $value,
                DataType::TYPE_FORMULA => $cell->getCalculatedValue(),
                DataType::TYPE_NULL => null,
                DataType::TYPE_ERROR => null,
                // DataType::TYPE_NUMERIC => $value,
                // DataType::TYPE_STRING => $value,
                // DataType::TYPE_STRING2 => $value,
                // TYPE_INLINE = 'inlineStr';
                // TYPE_ISO_DATE = 'd';
                default => $value,
            };

            $cells[] = $value;
        }

        return $cells;
    }
}
