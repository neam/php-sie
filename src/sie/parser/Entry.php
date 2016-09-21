<?php

namespace sie\parser;

class Entry
{

    public $label;
    public $attributes;
    public $entries;

    public function __construct($label)
    {
        $this->label = $label;
        $this->attributes = (object) [];
        $this->entries = [];
    }

}
