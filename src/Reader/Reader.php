<?php

declare(strict_types=1);

namespace Josecl\XlsImporter\Reader;

use Generator;

interface Reader
{
    public function __construct(string $filename);

    public function open(string $sheet): void;

    public function close(): void;

    /**
     * @return array<array-key, string>
     */
    public function getSheetNames(): array;

    public function fetchRow(): Generator;
}
