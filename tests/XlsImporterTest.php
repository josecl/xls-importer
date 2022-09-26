<?php

declare(strict_types=1);


use Josecl\XlsImporter\InvalidExtensionException;
use Josecl\XlsImporter\OpenSpoutCsvWriter;
use Josecl\XlsImporter\OpenSpoutXlsxWriter;
use Josecl\XlsImporter\SheetNotFoundException;
use Josecl\XlsImporter\XlsImporter;

test('Export', closure: function (string $filename, mixed $destination) {
    $importer = new XlsImporter(__DIR__ . '/spreadsheet.xls');
    $importer->import('Sheet 1', $destination);

    $this->assertFileExists($filename);
    unlink($filename);
})->with([
    //                                  output                   input
    //                                 =======================   ==============================================
    'xlsx string' => /*             */ [__DIR__ . '/out.Xlsx',   __DIR__ . '/out.Xlsx'],
    'xlsx OpenSpoutXlsxWriter' /**/ => [__DIR__ . '/out.Xlsx',   new OpenSpoutXlsxWriter(__DIR__ . '/out.Xlsx')],
    'csv string' =>  /*             */ [__DIR__ . '/out.csv',    __DIR__ . '/out.csv'],
    'csv OpenSpoutCsvWriter' => /*  */ [__DIR__ . '/out.csv',    new OpenSpoutCsvWriter(__DIR__ . '/out.csv')],
]);


test('Invalid extension', closure: function () {
    $importer = new XlsImporter(__DIR__ . '/spreadsheet.xls');
    $this->expectException(InvalidExtensionException::class);
    $importer->import('Sheet 1', __DIR__ . '/out.InValId');
});


test('Invalid sheet name', closure: function () {
    $importer = new XlsImporter(__DIR__ . '/spreadsheet.xls');
    $this->expectException(SheetNotFoundException::class);
    $importer->import('Invalid Sheet name', __DIR__ . '/out.csv');
});
