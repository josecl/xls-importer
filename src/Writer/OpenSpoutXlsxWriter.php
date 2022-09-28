<?php

declare(strict_types=1);

namespace Josecl\XlsImporter\Writer;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class OpenSpoutXlsxWriter implements Writer
{
    private XlsxWriter $writer;

    public function __construct(
        private string $filename,
        private string $sheet = 'Sheet1',
    ) {
        $this->writer = new XlsxWriter();
    }

    public function open(): void
    {
        $this->writer->openToFile($this->filename);
        $this->writer->getCurrentSheet()->setName($this->sheet);
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
