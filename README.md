# Xls importer

[![phpstan](https://github.com/josecl/xls-importer/actions/workflows/phpstan.yml/badge.svg)](https://github.com/josecl/xls-importer/actions/workflows/phpstan.yml)
[![tests](https://github.com/josecl/xls-importer/actions/workflows/tests.yml/badge.svg)](https://github.com/josecl/xls-importer/actions/workflows/tests.yml)



## Usage

To convert to a CSV file:

```php
$importer = new \Josecl\XlsImporter\XlsImporter('/path/to/input.xls');
$importer->import('Sheet 1', '/path/to/output.csv');
```

To convert to a XLSX file:

```php
// ...
$importer->import('Sheet 1', '/path/to/output.xlsx');
```

To convert to a XLSX file giving the `Writer` instance.

```php
// ...
$importer->import('Sheet 1', new \Josecl\XlsImporter\Writer\OpenSpoutXlsxWriter('output.xlsx', 'Sheet Name'));
```

You can also use a custom implementation of the `Josecl\XlsImporter\Writer\Writer` interface:

```php
// ...
$importer->import('Sheet 1',  new MyOwnXlsxWriter());
```
Look at `Josecl\XlsImporter\OpenSpoutXlsxWriter` for inspiration.



## Development

```shell 
# php-cs-fixer
composer lint
```

```shell 
# phpstan
composer analyze
```

```shell 
# pest
composer test
```
