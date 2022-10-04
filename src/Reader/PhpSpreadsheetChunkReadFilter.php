<?php

declare(strict_types=1);

namespace Josecl\XlsImporter\Reader;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class PhpSpreadsheetChunkReadFilter implements IReadFilter
{
    public function __construct(public int $from, public int $to)
    {
    }

    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        return $row >= $this->from && $row <= $this->to;
    }
}
