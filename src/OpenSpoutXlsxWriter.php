<?php

declare(strict_types=1);

namespace Josecl\XlsImporter;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\AbstractWriter;

class OpenSpoutXlsxWriter implements Writer
{
    private AbstractWriter $writer;

    public function __construct(
        private string $filename,
    ) {
        $this->writer = new \OpenSpout\Writer\XLSX\Writer();
    }

    public function open(): void
    {
        $this->writer->openToFile($this->filename);
    }

    public function close(): void
    {
        $this->writer->close();
    }

    public function write(array $row): void
    {
        $this->writer->addRow(Row::fromValues($row));
    }
}
