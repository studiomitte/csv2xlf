# TYPO3 Extension `csv2xlf`


[![TYPO3 12](https://img.shields.io/badge/TYPO3-12-orange.svg)](https://get.typo3.org/version/12)

This extension provides a command to generate a XLF file and its translations from a CSV file.

The typical use case is to provide an online Excel/Google Docs for clients to provide translations which then are converted to XLF files.

## Installation

```bash
composer require studiomitte/csv2xlf
```

## Usage

### CSV to XLF

CSV looks like this
```csv
"key","en","de"
"example","This is an example NEW","Das ist ein Beispiel","Ceci est un exemple"
"example2","<![CDATA[<h3>Datenschutzhinweis (bs)</h3>","Das ist ein Beispiel"
```

With the following requirements:
- The first row is the header 
- The header starts with `key`, followed by `en` and afterward the language codes
- Default is always `en`


```bash
./bin/typo3 csv2xlf:csv2xlf packages/csv2xlf/Resources/Private/Examples/csv2xlf/in.csv packages/csv2xlf/Resources/Private/Examples/csv2xlf/out.xlf
```

### XLF to CSV

```bash
./bin/typo3 csv2xlf:xlf2csv packages/csv2xlf/Resources/Private/Examples/xlf2csv/in.xlf  packages/csv2xlf/Resources/Private/Examples/xlf2csv/out.csv de,fr,es
```

## Credits

This extension was created by [Studio Mitte](https://studiomitte.com) with ♥.

[Find more TYPO3 extensions we have developed](https://www.studiomitte.com/loesungen/typo3) that provide additional features for TYPO3 sites.
