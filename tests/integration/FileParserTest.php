<?php


class FileParserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitGuy
     */
    protected $guy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testParsesAFile()
    {

        $parser = new \sie\Parser();
        $file_contents = $this->open_file('sie_file.se');
        $sie_file = $parser->parseSieFileContents($file_contents);

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(19, count($sie_file->entries));

        $this->assertEquals("Foocorp", $sie_file->entries_with_label("fnamn")[0]->attributes->foretagsnamn);

        $account = $sie_file->entries_with_label("konto")[0];
        $this->assertEquals("1510", $account->attributes->kontonr);
        $this->assertEquals("Accounts receivable", $account->attributes->kontonamn);
        $this->assertEquals(2, count($sie_file->entries_with_label("ver")));

    }

    public function testParsesAFileWithUnknownEntriesUsingALenientParser()
    {

        $parser = new \sie\Parser(["lenient" => true]);
        $file_contents = $this->open_file('sie_file_with_unknown_entries.se');
        $sie_file = $parser->parseSieFileContents($file_contents);

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(2, count($sie_file->entries_with_label("ver")));

    }

    public function testParsesAFileWithUnknownEntriesUsingAStrictParser()
    {

        $parser = new \sie\Parser();
        $file_contents = $this->open_file('sie_file_with_unknown_entries.se');

        $this->expectException('Exception');
        $this->expectExceptionMessageRegExp('/Unknown entry type: momskod\. Pass \'lenient: true\'/');

        $sie_file = $parser->parseSieFileContents($file_contents);

    }

    protected function open_file($fixture_file)
    {
        return file_get_contents(codecept_data_dir('fixtures/' . $fixture_file));
    }

}