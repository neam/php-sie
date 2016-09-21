<?php

namespace sie\parser;

class SieFile
{
    public $entries = [];

    public function entries_with_label($label)
    {
        $entries_with_label = [];
        foreach ($this->entries as $entry) {
            if ($entry->label === $label) {
                $entries_with_label[] = $entry;
            }
        }
        return $entries_with_label;
    }

}
