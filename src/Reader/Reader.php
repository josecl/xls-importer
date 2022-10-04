<?php

declare(strict_types=1);

namespace Josecl\XlsImporter\Reader;

use Generator;
use Josecl\XlsImporter\SheetNotFoundException;

interface Reader
{
    public function __construct(string $filename);

    /**
     * Opens the worksheet
     *
     * @param  string  $sheet Worksheet name
     * @param  int  $from Start reading from row number
     * @param  int|null  $to Ending row to read
     * @throws SheetNotFoundException
     */
    public function open(string $sheet, int $from = 1, ?int $to = null): void;

    public function close(): void;

    /**
     * @return array<array-key, string>
     */
    public function getSheetNames(): array;

    public function fetchRow(): Generator;
}
