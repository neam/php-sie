<?php
namespace parser;

use sie\parser\LineParser;

class LineParserTest extends \Codeception\Test\Unit
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
    public function testItParsesLinesFromASieFile()
    {
        $parser = new LineParser('#TRANS 2400 {"3" "5"} -200 20130101 "Foocorp expense"');
        $entry = $parser->parse();
        $this->assertEquals("trans", $entry->label);
        $this->assertEquals(
            (object) [
                "kontonr" => "2400",
                "belopp" => "-200",
                "transdat" => "20130101",
                "transtext" => "Foocorp expense",
                "objektlista" => [
                    ["dimensionsnr" => "3", "objektnr" => "5"]
                ],
            ],
            $entry->attributes
        );
    }

    protected function context_unknown_entry_line()
    {
        return "#MOMSKOD 2611 10";
    }

    public function testUsingALenientParserItRaisesNoErrorWhenEncounteringUnknownEntries()
    {
        $line = $this->context_unknown_entry_line();
        $parser = new LineParser($line, $lenient = true);
        $parser->parse();
    }

    public function testUsingAStrictParserItRaisesErrorWhenEncounteringUnknownEntries()
    {
        $line = $this->context_unknown_entry_line();
        $parser = new LineParser($line);

        $this->expectException('sie\\parser\\InvalidEntryError');
        $this->expectExceptionMessageRegExp('/Unknown entry type/');

        $parser->parse();
    }

}