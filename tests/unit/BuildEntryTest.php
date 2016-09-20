<?php

use sie\parser\BuildEntry;
use sie\parser\Tokenizer;

class BuildEntryTest extends \Codeception\Test\Unit
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

    public function testCall()
    {
        $line = '#TRANS 2400 {} -200 20130101 "Foocorp expense"';
        $tokenizer = new Tokenizer($line);
        $tokens = $tokenizer->tokenize();
        $first_token = array_shift($tokens);
        $buildEntry = new BuildEntry($line, $first_token, $tokens, false);
        $entry = $buildEntry->call();

        codecept_debug(__METHOD__);
        codecept_debug($entry);

        $this->assertEquals(2400, $entry->attributes->kontonr);
        $this->assertEquals(-200, $entry->attributes->belopp);
        $this->assertEquals(20130101, $entry->attributes->transdat);
        $this->assertEquals("Foocorp expense", $entry->attributes->transtext);
        $this->assertFalse(isset($entry->attributes->kvantitet));

    }

    public function testCallWithAnUnexpectedTokenAtStartOfArray()
    {

        $this->expectException('sie\parser\InvalidEntryError');
        $line = '#TRANS 2400 [] -200 20130101 "Foocorp expense"';
        $tokenizer = new Tokenizer($line);
        $tokens = $tokenizer->tokenize();
        $first_token = array_shift($tokens);
        $buildEntry = new BuildEntry($line, $first_token, $tokens, false);
        $buildEntry->call();

    }
}