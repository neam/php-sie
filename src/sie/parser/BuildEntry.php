<?php

namespace sie\parser;

use Exception;
use sie\parser\tokenizer\BeginArrayToken;
use sie\parser\tokenizer\EndArrayToken;

class BuildEntry
{

    public $line;
    public $first_token;
    public $tokens;
    public $lenient;

    public function __construct($line, tokenizer\Token $first_token, $tokens, $lenient)
    {
        $this->line = $line;
        $this->first_token = $first_token;
        $this->tokens = $tokens;
        $this->lenient = $lenient;
    }


    public function call()
    {
        if ($this->first_token->known_entry_type()) {
            return $this->build_complete_entry();
        } elseif ($this->lenient) {
            return $this->build_empty_entry();
        } else {
            $this->raise_invalid_entry_error();
        }
    }

    private function build_complete_entry()
    {
        $entry = $this->build_empty_entry();

            codecept_debug(__METHOD__);
            codecept_debug(["awt"=>$this->attributes_with_tokens()]);


        foreach ($this->attributes_with_tokens() as $attr => $attr_tokens) {

            codecept_debug(__METHOD__);
            codecept_debug(compact("attr"));
            codecept_debug(compact("attr_tokens"));

            $label = is_array($attr) ? $attr["name"] : $attr;
            if (is_array($attr_tokens) && count($attr_tokens) === 1) {
                $entry->attributes[$label] = reset($attr_tokens);
            } else {
                $type = $attr["type"];
                $values = [];
                foreach ($attr_tokens as $attr_token) {

                    throw new Exception("TODO");

                }
                /*
                    values = attr_tokens.
                      each_slice(type.size).
                      map { |slice| Hash[type.zip(slice)] }

                    entry.attributes[label] = values
                */
            }
        }

        return $entry;
    }

    private function attributes_with_tokens()
    {
        $return = [];
        foreach ($this->line_entry_type() as $attr_entry_type) {

            $token = array_shift($this->tokens);

            if (!$token) {
                continue;
            }

            if (is_string($attr_entry_type)) {
                $return[] = [$attr_entry_type, $token->value];
            } else {
                if (!($token instanceof BeginArrayToken)) {
                    throw new InvalidEntryError("Unexpected token: #" . $token->inspect());
                }

                $hash_tokens = [];
                while ($token = array_shift($this->tokens)) {
                    if ($token instanceof EndArrayToken) {
                        break;
                    }
                    $hash_tokens[] = $token->value;
                }
                $return[] = [$attr_entry_type, $hash_tokens];
            }

        }
        return $return;

    }

    private function build_empty_entry()
    {
        $entry = new Entry($this->first_token->label());
        return $entry;
    }

    private function line_entry_type()
    {
        return $this->first_token->entry_type();
    }

    private function raise_invalid_entry_error()
    {
        throw new InvalidEntryError("Unknown entry type: #" . $this->first_token->label() . "");
    }
}

class InvalidEntryError extends Exception
{

}
