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

        $this->assertEquals(2400, $entry->attributes->kontonr);
        $this->assertEquals(-200, $entry->attributes->belopp);
        $this->assertEquals(20130101, $entry->attributes->transdat);
        $this->assertEquals("Foocorp expense", $entry->attributes->transtext);
        $this->assertFalse(isset($entry->attributes->kvantitet));
    }

    public function testCallWithAnUnquotedZeroString()
    {
        $line = '#RAR 0 20100101 20101231';
        $tokenizer = new Tokenizer($line);
        $tokens = $tokenizer->tokenize();
        $first_token = array_shift($tokens);
        $buildEntry = new BuildEntry($line, $first_token, $tokens, false);
        $entry = $buildEntry->call();

        $this->assertEquals(0, $entry->attributes->arsnr);
        $this->assertEquals(20100101, $entry->attributes->start);
        $this->assertEquals(20101231, $entry->attributes->slut);
    }

    public function testCallWithShortDimensionsArray()
    {
        $line = '#TRANS 3311 {"1" "1"} -387.00';
        $tokenizer = new Tokenizer($line);
        $tokens = $tokenizer->tokenize();
        $first_token = array_shift($tokens);
        $buildEntry = new BuildEntry($line, $first_token, $tokens, false);
        $entry = $buildEntry->call();

        $this->assertEquals(3311, $entry->attributes->kontonr);
        $this->assertEquals(
            [
                [
                    "dimensionsnr" => 1,
                    "objektnr" => 1,
                ],
            ],
            $entry->attributes->objektlista
        );
        $this->assertEquals("-387.00", $entry->attributes->belopp);
    }

    public function testCallWithLongDimensionsArray()
    {
        $line = '#TRANS 3311 {"1" "1" "6" "1"} -387.00';
        $tokenizer = new Tokenizer($line);
        $tokens = $tokenizer->tokenize();
        $first_token = array_shift($tokens);
        $buildEntry = new BuildEntry($line, $first_token, $tokens, false);
        $entry = $buildEntry->call();

        $this->assertEquals(3311, $entry->attributes->kontonr);
        $this->assertEquals(
            [
                [
                    "dimensionsnr" => 1,
                    "objektnr" => 1,
                ],
                [
                    "dimensionsnr" => 1,
                    "objektnr" => 6,
                ],
            ],
            $entry->attributes->objektlista
        );
        $this->assertEquals("-387.00", $entry->attributes->belopp);
    }

    public function testCallWithSimpleAttribute()
    {
        $line = '#FLAGGA 0';
        $tokenizer = new Tokenizer($line);
        $tokens = $tokenizer->tokenize();
        $first_token = array_shift($tokens);
        $buildEntry = new BuildEntry($line, $first_token, $tokens, false);
        $entry = $buildEntry->call();

        $this->assertEquals(0, $entry->attributes->x);
    }

    public function testCallWithAnUnexpectedTokenAtStartOfArray()
    {
        $this->expectException('sie\\parser\\InvalidEntryError');
        $line = '#TRANS 2400 [] -200 20130101 "Foocorp expense"';
        $tokenizer = new Tokenizer($line);
        $tokens = $tokenizer->tokenize();
        $first_token = array_shift($tokens);
        $buildEntry = new BuildEntry($line, $first_token, $tokens, false);
        $buildEntry->call();
    }

}