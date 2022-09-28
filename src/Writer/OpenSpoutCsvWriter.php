<?php

declare(strict_types=1);

namespace Josecl\XlsImporter\Writer;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Writer as CsvWriter;

class OpenSpoutCsvWriter implements Writer
{
    private CsvWriter $writer;

    public function __construct(
        private string $filename,
    ) {
        $this->writer = new CsvWriter();
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
