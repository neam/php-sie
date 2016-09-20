<?php

namespace sie\parser;

use StrScan\StringScanner;
use sie\Parser;
use sie\parser\tokenizer\EntryToken;
use sie\parser\tokenizer\BeginArrayToken;
use sie\parser\tokenizer\EndArrayToken;
use sie\parser\tokenizer\StringToken;
use Exception;

class Tokenizer
{

    private $line;
    private $scanner;

    public function __construct($line)
    {
        $this->line = $line;
        $this->scanner = new StringScanner($line);
    }

    public function tokenize()
    {
        $tokens = [];
        $this->check_for_control_characters();

        while (!$this->scanner->hasTerminated()) {

            switch (true) {
                case $this->whitespace():
                    continue;
                case $match = $this->find_entry():
                    $tokens[] = new EntryToken($match);
                    continue;
                case $this->begin_array():
                    $tokens[] = new BeginArrayToken();
                    continue;
                case $this->end_array():
                    $tokens[] = new EndArrayToken();
                    continue;
                case $match = $this->find_string():
                    $tokens[] = new StringToken($match);
                    continue;
                case $this->end_of_string():
                    codecept_debug("end of string");
                    return $tokens;
                default:
                    # We shouldn't get here, but if we do we need to bail out, otherwise we get an infinite loop.
                    throw new Exception(
                        "Unhandled character in line at position #"
                        . $this->scanner->getPosition()
                        . ": '" . $this->scanner->getSource() . "' at '" . $this->scanner->getRemainder() . "'"
                    );
            }

        }

        return $tokens;
    }

    private function check_for_control_characters()
    {
        if ($match = preg_match('/(.*?)([\\x00-\\x08\\x0a-\\x1f\\x7f])/', $this->line)) {
            throw new Exception(
                "Unhandled control character in line at position #"
                . (strlen($match) + 1)
                . ": " . $this->scanner->getRemainder()
            );
        }
    }

    private function whitespace()
    {
        return $this->scanner->scan('/[ \t]+/');
    }

    private function find_entry()
    {
        $match = $this->scanner->scan('/#\S+/');

        if ($match) {
            return preg_replace('/#/', "", $match);
        } else {
            return null;
        }
    }

    private function begin_array()
    {
        return $this->scanner->scan('/' . Parser::BEGINNING_OF_ARRAY . '/');
    }


    private function end_array()
    {
        return $this->scanner->scan('/' . Parser::END_OF_ARRAY . '/');
    }

    private function find_string()
    {
        $match = $this->find_quoted_string();
        if (!$match) {
            $match = $this->find_unquoted_string();
        }

        if ($match) {
            return $this->remove_unnecessary_escapes($match);
        } else {
            return null;
        }
    }

    private function end_of_string()
    {
        return $this->scanner->hasTerminated();
    }

    private function find_quoted_string()
    {
        $match = $this->scanner->scan('/"(\\\\"|[^"])*"/');

        if ($match) {
            return preg_replace('/"$/', '', preg_replace('/^"/', '', $match));
        } else {
            return null;
        }
    }

    private function find_unquoted_string()
    {
        return $this->scanner->scan('/\S+/');
    }

    private function remove_unnecessary_escapes($match)
    {
        return preg_replace('/\\\\([\\\\"])/', "$1", $match);
    }

}
