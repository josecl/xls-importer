<?php

declare(strict_types=1);


use Josecl\XlsImporter\Reader\XlsReader;

test('Sheet names', function () {
    $reader = new XlsReader(__DIR__ . '/spreadsheet.xls');

    expect($reader->getSheetNames())
        ->toMatchArray(['Sheet 1', 'Empty']);

    $reader->close();
});

test('Iterate rows', function () {
    $reader = new XlsReader(__DIR__ . '/spreadsheet.xls');

    $reader->open('Sheet 1');
    $rows = [];
    foreach ($reader->fetchRow() as $key => $row) {
        $rows[$key] = $row;
    }
    $reader->close();

    expect($rows)
        ->{'1'}->toMatchArray(['Table title with colspan', null, null, null, null, null])
        ->{'4'}->toMatchArray([
            'José',
            'Müller',
            null,
            "Musterstraße 17\n12345 Berlin\nDeutschland",
            67,
            21409,
        ])
        ->{'8'}->{'5'}->toMatchFloat(44830.5659259, 6)
    ;
});

test('Iterate rows range', function () {
    $reader = new XlsReader(__DIR__ . '/spreadsheet.xls');

    $reader->open('Sheet 1', 3, 6);

    $rows = [];
    foreach ($reader->fetchRow() as $key => $row) {
        $rows[$key] = $row;
    }
    $reader->close();

    expect($rows)
        ->toHaveCount(4) // [3-6]
        ->{'3'}->toMatchArray(['John', 'Smith'])
        ->{'6'}->toMatchArray(['Float', null])
    ;
});


test('Big file range', function () {
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
    $filename = 'compress.bzip2://' . __DIR__ . '/big.xls.bz2';
    $tempFile = __DIR__ . '/' . random_int(10_000_000, 99_999_999) . '-big.xls';
    copy($filename, $tempFile);

    $reader = new XlsReader($tempFile);

    $baseMemoryUsage = memory_get_usage(true);
    $reader->open('Sheet-10000');
    $rowCount = 0;
    foreach ($reader->fetchRow() as $row) {
        $rowCount++;
    }
    $memoryUsed = memory_get_usage(true) - $baseMemoryUsage;
    unlink($tempFile);

    expect($rowCount)->toBe(10000);

    // Without chunks, it will be around 61mb
    // With chunks set to 5000 it takes around 20mb ~ 26mb
    expect($memoryUsed)->toBeLessThan(28_000_000);

    $reader->close();
});
