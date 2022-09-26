<?php

declare(strict_types=1);

namespace Josecl\XlsImporter;

interface Writer
{
    public function open(): void;

    public function close(): void;

    public function write(array $row): void;
}
