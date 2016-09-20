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
        $this->markTestIncomplete();
    }

    /*
describe Sie::Parser, "parse" do
  it "parses sie data that includes arrays" do
    data = <<-DATA
#VER "LF" 2222 20130101 "Foocorp expense"
{
#TRANS 2400 {} -200 20130101 "Foocorp expense"
#TRANS 4100 {} 180 20130101 "Widgets from foocorp"
#TRANS 2611 {} -20 20130101 "VAT"
}
    DATA

    parser = Sie::Parser.new
    sie_file = parser.parse(data)

    voucher_entry = sie_file.entries.first
    expect(sie_file.entries.size).to eq(1)
    expect(voucher_entry.attributes["verdatum"]).to eq("20130101")
    expect(voucher_entry.entries.size).to eq(3)
    expect(voucher_entry.entries.first.attributes["kontonr"]).to eq("2400")
  end

    */

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

        codecept_debug($voucher_entry->entries);

        $this->assertInstanceOf('\\sie\\parser\\Entry', $voucher_entry);
        $this->assertEquals(3, count($voucher_entry->entries));

    }

}