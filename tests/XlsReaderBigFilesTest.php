<?php

declare(strict_types=1);

use Josecl\XlsImporter\Reader\XlsReader;

beforeAll(function () {
    $filename = __DIR__ . '/big.xls';
    $compressedFilename = "compress.bzip2://{$filename}.bz2";
    copy($compressedFilename, $filename);
});

afterAll(function () {
    $filename = __DIR__ . '/big.xls';
    if (file_exists($filename)) {
        unlink($filename);
    }
});


test('Range from big file', function () {
    $reader = new XlsReader(__DIR__ . '/big.xls');
    $reader->open('Sheet-10000', 2998, 3002);
    $rowCount = 0;
    foreach ($reader->fetchRow() as $key => $row) {
        $rowCount++;
    }
    $reader->close();

    expect($rowCount)->toBe(5);
});


test('Big file with constrained memory usage', function () {
    $reader = new XlsReader(__DIR__ . '/big.xls');

    $baseMemoryUsage = memory_get_usage(true);
    $reader->open('Sheet-10000');
    $rowCount = 0;
    foreach ($reader->fetchRow() as $row) {
        $rowCount++;
    }
    $memoryUsed = memory_get_usage(true) - $baseMemoryUsage;

    expect($rowCount)->toBe(10000);

    // Without chunks, it will be around 61mb
    // With chunks set to 5000 it takes around 20mb ~ 26mb
    expect($memoryUsed)->toBeLessThan(28_000_000);

    $reader->close();
});
