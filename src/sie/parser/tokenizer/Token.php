<?php

namespace sie\parser\tokenizer;

use sie\parser\EntryTypes;

class Token
{

    public $value;

    public function __construct($value = "")
    {
        $this->value = $value;
    }

    public function known_entry_type()
    {
        return array_key_exists($this->label(), EntryTypes::$ENTRY_TYPES);
    }

    public function entry_type()
    {
        return EntryTypes::$ENTRY_TYPES[$this->label()];
    }

    public function label()
    {
        return strtolower(preg_replace('/^#/', '', $this->value));
    }

}
