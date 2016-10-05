<?php

namespace sie\parser;

class LineParser
{

    protected $line;
    protected $lenient;

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

    protected function tokenize($line)
    {
        $tokenizer = new Tokenizer($line);
        return $tokenizer->tokenize();
    }

    protected function build_entry($first_token, $tokens)
    {
        $build_entry = new BuildEntry($this->line, $first_token, $tokens, $this->lenient);
        return $build_entry->call();
    }

}
