<?php

namespace sie\document;

class Renderer
{
    public function add_line($label, $values)
    {
        $this->append("#$label " . implode(" ", $this->format_values($values)));
    }

    public function add_beginning_of_array()
    {
        $this->append("{");
    }

    public function add_end_of_array()
    {
        $this->append("}");
    }

    public function render()
    {
        $this->lines[] = "";
        return implode("\n", $this->lines);
    }

    private $lines = [];

    private function append($text)
    {
        $this->lines[] = $this->encoded($text);
    }

    private function format_values($values)
    {
        return array_map(
            function ($value) {
                return $this->format_value($value);
            },
            $values
        );
    }

    private function encoded($text)
    {
        $current_ctype_locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'POSIX');
        $encoded = CP437Encoding::convertFromUTF8ToCP437($text);
        setlocale(LC_CTYPE, $current_ctype_locale);
        return $encoded;
    }

    private function format_value($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format("Ymd");
        } elseif (is_array($value)) {
            $subvalues = [];
            foreach ($value as $key => $subvalue) {
                $subvalues[] = $this->format_value($key);
                $subvalues[] = $this->format_value($subvalue);
            }
            return "{" . implode(" ", $subvalues) . "}";
        } elseif (is_int($value) || ctype_digit($value)) {
            return (string) $value;
        } else {
            return '"' . (string) str_replace('"', '\"', $value) . '"';
        }
    }

}
