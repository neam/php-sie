<?php


class ParserTest extends \Codeception\Test\Unit
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
    public function testParsesSieDataThatIncludesArrays()
    {

        $data = <<<DATA
#VER "LF" 2222 20130101 "Foocorp expense"
{
#TRANS 2400 {} -200 20130101 "Foocorp expense"
#TRANS 4100 {} 180 20130101 "Widgets from foocorp"
#TRANS 2611 {} -20 20130101 "VAT"
DATA;

        $parser = new \sie\Parser();
        $sie_file = $parser->parse($data);

        $voucher_entry = $sie_file->entries[0];

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(1, count($sie_file->entries));

        codecept_debug($voucher_entry);

        $this->assertEquals("20130101", $voucher_entry->attributes->verdatum);
        $this->assertInstanceOf('\\sie\\parser\\Entry', $voucher_entry);
        $this->assertEquals("2400", $voucher_entry->entries[0]->attributes->kontonr);
        $this->assertEquals(3, count($voucher_entry->entries));

    }

    public function testHandlesLeadingWhitespace()
    {

        $data = <<<DATA
#VER "LF" 2222 20130101 "Foocorp expense"
{
    #TRANS 2400 {} -200 20130101 "Foocorp expense"
    #TRANS 4100 {} 180 20130101 "Widgets from foocorp"
    #TRANS 2611 {} -20 20130101 "VAT"
}
DATA;

        $parser = new \sie\Parser();
        $sie_file = $parser->parse($data);

        $voucher_entry = $sie_file->entries[0];

        $this->assertInstanceOf('\\sie\\parser\\SieFile', $sie_file);
        $this->assertEquals(1, count($sie_file->entries));
        $this->assertInstanceOf('\\sie\\parser\\Entry', $voucher_entry);
        $this->assertEquals(3, count($voucher_entry->entries));

    }

}