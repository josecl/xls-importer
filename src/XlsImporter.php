<?php

declare(strict_types=1);

namespace Josecl\XlsImporter;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;

class XlsImporter
{
    private string $filename;

    private Writer $writer;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function import(string $sheet, string|Writer $destination): void
    {
        $this->writer = $this->getWriter($destination, $sheet);

        $reader = IOFactory::createReader('Xls');
        // TODO: Check of setReadDataOnly() reduces memory usage
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly($sheet);
        $spreadsheet = $reader->load($this->filename);

        if (! in_array($sheet, $spreadsheet->getSheetNames(), true)) {
            throw new SheetNotFoundException("Sheet not found: {$sheet}");
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $this->writer->open();
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = $this->getRowValues($cellIterator);
            $this->writer->write($cells);
        }

        $this->writer->close();
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

    private function getWriter(Writer|string $destination, string $sheet): Writer
    {
        if ($destination instanceof Writer) {
            return $destination;
        }

        $extension = strtolower(pathinfo($destination, PATHINFO_EXTENSION));

        return match ($extension) {
            'xlsx' => new OpenSpoutXlsxWriter($destination, $sheet),
            'csv' => new OpenSpoutCsvWriter($destination),
            default => throw new InvalidExtensionException("Invalid extension: {$extension}"),
        };
    }
}
