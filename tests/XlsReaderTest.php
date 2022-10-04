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
