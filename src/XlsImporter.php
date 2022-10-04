<?php

declare(strict_types=1);

namespace Josecl\XlsImporter;

use Josecl\XlsImporter\Reader\Reader;
use Josecl\XlsImporter\Reader\XlsReader;
use Josecl\XlsImporter\Writer\OpenSpoutCsvWriter;
use Josecl\XlsImporter\Writer\OpenSpoutXlsxWriter;
use Josecl\XlsImporter\Writer\Writer;

class XlsImporter
{
    private Reader $reader;

    private Writer $writer;

    public function __construct(string $filename)
    {
        $this->reader = new XlsReader($filename);
    }

    /**
     * @return array<array-key, string>
     */
    public function getSheetNames(): array
    {
        return $this->reader->getSheetNames();
    }

    public function import(string $sheetname, string|Writer $destination): void
    {
        $this->writer = $this->makeWriter($destination, $sheetname);

        $this->reader->open($sheetname);
        $this->writer->open();

        foreach ($this->reader->fetchRow() as $cells) {
            $this->writer->write($cells);
        }

        $this->reader->close();
        $this->writer->close();
    }

    private function makeWriter(Writer|string $destination, string $sheet): Writer
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
