PHP Library: SIE
================

Parser and generator for SIE files (http://sie.se/).

PHP port of https://github.com/barsoom/sie

Current implementation requires PHP 5.6.5 or greater.

## Installation

Via composer:

    composer require neam/php-sie

## Generating a SIE file

To generate a SIE document you first need to define a class that implements \sie\document\IDataSource or extends abstract class \sie\document\DataSource.

```php
$mySieFileDataSource = new MySieFileDataSource($this);
$sieFileDocument = $mySieFileDataSource->generateSieDocument();
$sieFileContents = $sieFileDocument->render();
file_put_contents('sie_file.se', $sieFileContents);
```

Check the files in tests/unit/*Test.php for more examples.

## Parsing a SIE file

You can parse sie data from anything that responds to `each_line` like a file or a string.

```php
$file_contents = file_get_contents('sie_file.se');
$parser = new \sie\Parser();
$sie_file = $parser->parseSieFileContents($file_contents);
return $this->parse($data);

// The company name
puts sie_file.entries_with_label("fnamn").first.attributes["foretagsnamn"]

// The first account number
puts sie_file.entries_with_label("konto").first.attributes["kontonr"]
```

The parser expects file contents in CP437 encoding (the official encoding of the SIE file format). If you want to parse UTF8 strings, use the "parse" method:  

```php
$sie_file = $parser->parse($utf8_string);
```

By default the parser will raise an error if it encounters unknown entry types. Use the `lenient` option to avoid this:

```php
$parser = new \sie\Parser(["lenient" => true]);
```

Check the files in tests/integration/*Test.php for more examples.

## Developing

First time setup:

    composer install

Running tests:

    vendor/bin/codecept build
    vendor/bin/codecept run

Tests sample output:

    # vendor/bin/codecept run --debug
    Codeception PHP Testing Framework v2.2.4
    Powered by PHPUnit 5.4.8 by Sebastian Bergmann and contributors.
    
    Integration Tests (8) ----------------------------------------------------------------------------------------------
    Modules: \Helper\Integration
    --------------------------------------------------------------------------------------------------------------------
    ✔ FileParserTest: Parses a file (0.07s)
    ✔ FileParserTest: Parses a file with unknown entries using a lenient parser (0.01s)
    ✔ FileParserTest: Parses a file with unknown entries using a strict parser (0.05s)
    ✔ Fortnox21FileParserTest: Parses sie1 se file (0.09s)
    ✔ Fortnox21FileParserTest: Parses sie2 se file (0.25s)
    ✔ Fortnox21FileParserTest: Parses sie3 se file (0.27s)
    ✔ Fortnox21FileParserTest: Parses sie4 se file (0.51s)
    ✔ Fortnox21FileParserTest: Parses sie4 si file (0.29s)
    --------------------------------------------------------------------------------------------------------------------
    
    Unit Tests (39) ----------------------------------------------------------------------------------------------------
    Modules: \Helper\Unit
    --------------------------------------------------------------------------------------------------------------------
    ✔ BuildEntryTest: Call (0.05s)
    ✔ BuildEntryTest: Call with an unquoted zero string (0.00s)
    ✔ BuildEntryTest: Call with short dimensions array (0.00s)
    ✔ BuildEntryTest: Call with long dimensions array (0.00s)
    ✔ BuildEntryTest: Call with simple attribute (0.00s)
    ✔ BuildEntryTest: Call with an unexpected token at start of array (0.00s)
    ✔ DocumentTest: Adds a header (0.02s)
    ✔ DocumentTest: Has accounting years (0.01s)
    ✔ DocumentTest: Has accounts (0.01s)
    ✔ DocumentTest: Has dimensions (0.01s)
    ✔ DocumentTest: Has objects (0.01s)
    ✔ DocumentTest: Has balances brought forward (0.01s)
    ✔ DocumentTest: Has balances carried forward (0.01s)
    ✔ DocumentTest: Has closing account balances (0.01s)
    ✔ DocumentTest: Has vouchers (0.01s)
    ✔ DocumentTest: Truncates really long descriptions (0.01s)
    ✔ DocumentTest: Ensures there are at least two lines with a zeroed single voucher line (0.01s)
    ✔ DocumentTest: Reads the series from the voucher with a series defined (0.01s)
    ✔ ParserTest: Parses sie data that includes arrays (0.00s)
    ✔ ParserTest: Handles leading whitespace (0.00s)
    ✔ RendererTest: Replaces input of the wrong encoding with questionmark (0.00s)
    ✔ VoucherSeriesTest: Self for | #0 (0.00s)
    ✔ VoucherSeriesTest: Self for | #1 (0.00s)
    ✔ VoucherSeriesTest: Self for | #2 (0.00s)
    ✔ VoucherSeriesTest: Self for | #3 (0.00s)
    ✔ VoucherSeriesTest: Self for | #4 (0.00s)
    ✔ LineParserTest: It parses lines from a sie file (0.00s)
    ✔ LineParserTest: Using a lenient parser it raises no error when encountering unknown entries (0.00s)
    ✔ LineParserTest: Using a strict parser it raises error when encountering unknown entries (0.00s)
    ✔ TokenizerTest: Tokenizes the given line (0.00s)
    ✔ TokenizerTest: Can parse metadata arrays (0.00s)
    ✔ TokenizerTest: Handles escaped quotes in quoted strings (0.00s)
    ✔ TokenizerTest: Handles escaped quotes in non quoted strings (0.00s)
    ✔ TokenizerTest: Handles escaped backslash in strings (0.00s)
    ✔ TokenizerTest: Has reasonable behavior for consecutive escape characters (0.00s)
    ✔ TokenizerTest: Handles tab character as field separator (0.00s)
    ✔ TokenizerTest: Handles unquoted zeros (0.01s)
    ✔ TokenizerTest: Handles transaction with dimension array (0.00s)
    ✔ TokenizerTest: Rejects control characters (0.00s)
    --------------------------------------------------------------------------------------------------------------------
    
    
    Time: 2.84 seconds, Memory: 12.00MB
    
    OK (47 tests, 133 assertions)
