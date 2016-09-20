<?php

namespace sie\parser;

class LineParser
{

    private $line;
    private $lenient;

    public function __construct($line, $lenient = null)
    {
        $this->line = $line;
        $this->lenient = $lenient;
    }

    public function parse()
    {
        $tokens = $this->tokenize($this->line);
        $first_token = array_shift($tokens);
        return $this->build_entry($first_token, $tokens);
    }

    private function tokenize($line)
    {
        $tokenizer = new Tokenizer($line);
        return $tokenizer->tokenize();
    }

    private function build_entry($first_token, $tokens)
    {
        $build_entry = new BuildEntry($this->line, $first_token, $tokens, $this->lenient);
        return $build_entry->call();
    }
}
