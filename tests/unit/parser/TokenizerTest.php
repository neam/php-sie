<?php
namespace parser;

use sie\parser\Tokenizer;

class TokenizerTest extends \Codeception\Test\Unit
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
    public function testTokenizesTheGivenLine()
    {
        $tokenizer = new Tokenizer('#TRANS 2400 {} -200 20130101 "Foocorp expense"');
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["EntryToken", "TRANS"],
                ["StringToken", "2400"],
                ["BeginArrayToken", ""],
                ["EndArrayToken", ""],
                ["StringToken", "-200"],
                ["StringToken", "20130101"],
                ["StringToken", "Foocorp expense"]
            ],
            $this->token_table_for($tokens)
        );

    }

    public function testCanParseMetadataArrays()
    {
        $tokenizer = new Tokenizer('#TRANS 2400 { 1 "2" } -200 20130101 "Foocorp expense"');
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["EntryToken", "TRANS"],
                ["StringToken", "2400"],
                ["BeginArrayToken", ""],
                ["StringToken", "1"],
                ["StringToken", "2"],
                ["EndArrayToken", ""],
                ["StringToken", "-200"],
                ["StringToken", "20130101"],
                ["StringToken", "Foocorp expense"]
            ],
            $this->token_table_for($tokens)
        );

    }

    public function testHandlesEscapedQuotesInQuotedStrings()
    {
        $tokenizer = new Tokenizer('"String with \\" quote"');
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["StringToken", 'String with " quote']
            ],
            $this->token_table_for($tokens)
        );
    }

    public function testHandlesEscapedQuotesInNonQuotedStrings()
    {
        $tokenizer = new Tokenizer('String_with_\\"_quote');
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["StringToken", 'String_with_"_quote']
            ],
            $this->token_table_for($tokens)
        );
    }

    public function testHandlesEscapedBackslashInStrings()
    {
        $tokenizer = new Tokenizer('"String with \\\\ backslash"');
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["StringToken", 'String with \\ backslash']
            ],
            $this->token_table_for($tokens)
        );
    }

    public function testHasReasonableBehaviorForConsecutiveEscapeCharacters()
    {
        $tokenizer = new Tokenizer('"\\\\\\"\\\\"');
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["StringToken", '\\"\\']
            ],
            $this->token_table_for($tokens)
        );
    }

    public function testHandlesTabCharacterAsFieldSeparator()
    {
        $tokenizer = new Tokenizer("#TRANS\t2400");
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["EntryToken", "TRANS"],
                ["StringToken", "2400"]
            ],
            $this->token_table_for($tokens)
        );
    }

    public function testHandlesUnquotedZeros()
    {
        $tokenizer = new Tokenizer("#FLAGGA 0");
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["EntryToken", "FLAGGA"],
                ["StringToken", "0"]
            ],
            $this->token_table_for($tokens)
        );
    }

    public function testHandlesTransactionWithDimensionArray() {
        $tokenizer = new Tokenizer('#TRANS 1500 {6 1} "512" 20110903 "Item 1"');
        $tokens = $tokenizer->tokenize();

        $this->assertEquals(
            [
                ["EntryToken", "TRANS"],
                ["StringToken", "1500"],
                ["BeginArrayToken", ""],
                ["StringToken", "6"],
                ["StringToken", "1"],
                ["EndArrayToken", ""],
                ["StringToken", "512"],
                ["StringToken", "20110903"],
                ["StringToken", "Item 1"],
            ],
            $this->token_table_for($tokens)
        );

    }

    public function testRejectsControlCharacters()
    {
        $codes_not_allowed = range(0, 8) + range(10, 31) + [127];
        foreach ($codes_not_allowed as $x) {
            $str = pack("C", $x); // [x].pack("C")
            $tokenizer = new Tokenizer($str);
            //expect{tokenizer.tokenize}.to raise_error /Unhandled character/
        }
    }

    protected function token_table_for($tokens)
    {
        $table = [];
        foreach ($tokens as $token) {
            $table[] = [str_replace('sie\\parser\\tokenizer\\', '', get_class($token)), $token->value];
        }
        return $table;
    }

}