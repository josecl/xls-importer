<?php

declare(strict_types=1);

namespace Josecl\XlsImporter\Reader;

use Generator;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class XlsReader implements Reader
{
    private string $sheetname;

    private Spreadsheet|null $spreadsheet = null;

    private Worksheet|null $worksheet = null;

    public function __construct(private string $filename)
    {
    }

    public function open(string $sheetname): void
    {
        $this->close();
        $this->sheetname = $sheetname;
        $this->worksheet = $this->getSpreadsheet()->getActiveSheet();
    }

    public function close(): void
    {
        $this->worksheet = null;
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

        // Filter to avoid read data into memory
        $emptyReader = new class() implements IReadFilter {
            public function readCell($columnAddress, $row, $worksheetName = '')
            {
                return false;
            }
        };
        $reader->setReadFilter($emptyReader);
        $reader->setReadDataOnly(true);

        return $reader->load($this->filename)->getSheetNames();
    }

    public function getSpreadsheet(): Spreadsheet
    {
        if ($this->spreadsheet) {
            return $this->spreadsheet;
        }

        $reader = IOFactory::createReader('Xls');
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly($this->sheetname);

        $this->spreadsheet = $reader->load($this->filename);

        return $this->spreadsheet;
    }

    public function fetchRow(): Generator
    {
        $worksheet = $this->getSpreadsheet()->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = $this->getRowValues($cellIterator);
            yield $cells;
        }

//        $this->writer->close();
//        $spreadsheet->__destruct();

        return [];
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
