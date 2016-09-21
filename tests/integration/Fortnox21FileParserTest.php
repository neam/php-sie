<?php


class Fortnox21FileParserTest extends \Codeception\Test\Unit
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
    public function testParsesSie1SeFile()
    {

        $parser = new \sie\Parser();
        $file_contents = $this->open_file('Sie1.se');
        $sie_file = $parser->parseSieFileContents($file_contents);

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(505, count($sie_file->entries));

        $this->assertEquals("Testföretaget AB", $sie_file->entries_with_label("fnamn")[0]->attributes->foretagsnamn);

        $account = $sie_file->entries_with_label("konto")[0];
        $this->assertEquals("1111", $account->attributes->kontonr);
        $this->assertEquals("Byggnader på egen mark", $account->attributes->kontonamn);
        $this->assertEquals(0, count($sie_file->entries_with_label("ver")));

    }

    public function testParsesSie2SeFile()
    {

        $parser = new \sie\Parser();
        $file_contents = $this->open_file('Sie2.se');
        $sie_file = $parser->parseSieFileContents($file_contents);

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(1040, count($sie_file->entries));

        $this->assertEquals("Testföretaget AB", $sie_file->entries_with_label("fnamn")[0]->attributes->foretagsnamn);

        $account = $sie_file->entries_with_label("konto")[0];
        $this->assertEquals("1111", $account->attributes->kontonr);
        $this->assertEquals("Byggnader på egen mark", $account->attributes->kontonamn);
        $this->assertEquals(0, count($sie_file->entries_with_label("ver")));

    }

    public function testParsesSie3SeFile()
    {

        $parser = new \sie\Parser();
        $file_contents = $this->open_file('Sie3.se');
        $sie_file = $parser->parseSieFileContents($file_contents);

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(1047, count($sie_file->entries));

        $this->assertEquals("Testföretaget AB", $sie_file->entries_with_label("fnamn")[0]->attributes->foretagsnamn);

        $account = $sie_file->entries_with_label("konto")[0];
        $this->assertEquals("1111", $account->attributes->kontonr);
        $this->assertEquals("Byggnader på egen mark", $account->attributes->kontonamn);
        $this->assertEquals(0, count($sie_file->entries_with_label("ver")));

    }

    public function testParsesSie4SeFile()
    {

        $parser = new \sie\Parser();
        $file_contents = $this->open_file('Sie4.se');
        $sie_file = $parser->parseSieFileContents($file_contents);

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(1242, count($sie_file->entries));

        $this->assertEquals("Testföretaget AB", $sie_file->entries_with_label("fnamn")[0]->attributes->foretagsnamn);

        $account = $sie_file->entries_with_label("konto")[0];
        $this->assertEquals("1111", $account->attributes->kontonr);
        $this->assertEquals("Byggnader på egen mark", $account->attributes->kontonamn);
        $this->assertEquals(194, count($sie_file->entries_with_label("ver")));

    }

    public function testParsesSie4SiFile()
    {

        $parser = new \sie\Parser();
        $file_contents = $this->open_file('Sie4.si');
        $sie_file = $parser->parseSieFileContents($file_contents);

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(544, count($sie_file->entries));

        $this->assertEquals("Testföretaget AB", $sie_file->entries_with_label("fnamn")[0]->attributes->foretagsnamn);

        $account = $sie_file->entries_with_label("konto")[0];
        $this->assertEquals("1111", $account->attributes->kontonr);
        $this->assertEquals("Byggnader på egen mark", $account->attributes->kontonamn);
        $this->assertEquals(165, count($sie_file->entries_with_label("ver")));

    }

    protected function open_file($fixture_file)
    {
        return file_get_contents(codecept_data_dir('fixtures/fortnox2.1/' . $fixture_file));
    }

}