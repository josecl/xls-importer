# Xls importer

[![phpstan](https://github.com/josecl/xls-importer/actions/workflows/phpstan.yml/badge.svg)](https://github.com/josecl/xls-importer/actions/workflows/phpstan.yml)
[![tests](https://github.com/josecl/xls-importer/actions/workflows/tests.yml/badge.svg)](https://github.com/josecl/xls-importer/actions/workflows/tests.yml)

Converts old XLS (Excel Binary File Format) files to CSV or XLSX (Office Open XML)
preserving only the values and discarting all the rest of metadata (styles, formulas, etc.)
with the goal of generate a smaller and portable file.

### Caveats

- Imports only one worksheet
- Dates will be saved as numbers, ej: `2022/09/26 13:34:56` becomes `44830.565925926` (see roadmap)


## Usage

To convert to a CSV file:

```php
$importer = new \Josecl\XlsImporter\XlsImporter('/path/to/input.xls');
$importer->import('Sheet 1', '/path/to/output.csv');

```

If you don´t know the sheet name, you can use `getSheetNames()` 
to import the first one:

```php
// ...
$importer->import($importer->getSheetNames()[0], '/path/to/output.xlsx');
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

## Roadmap

- Autodetect dates and datetimes and convert them to ISO 8601 format (eg: `2005-08-15`, `2005-08-15T15:52:01+0000`)
- Customize field delimiter and field enclosure for CSV
- Import XLSX files (for cleanup and only keep the values)
- Use a wrapper as destination
