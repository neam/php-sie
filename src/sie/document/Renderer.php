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

    protected $lines = [];

    protected function append($text)
    {
        $this->lines[] = $this->encoded($text);
    }

    protected function format_values($values)
    {
        return array_map(
            function ($value) {
                return $this->format_value($value);
            },
            $values
        );
    }

    protected function encoded($text)
    {
        $current_ctype_locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'POSIX');
        $encoded = CP437Encoding::convertFromUTF8ToCP437($text);
        setlocale(LC_CTYPE, $current_ctype_locale);
        return $encoded;
    }

    protected function format_value($value)
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
        } elseif (!preg_match('/\\s/', $value) && $value !== "" && $value !== null) {
            return (string) $value;
        } else {
            return '"' . (string) str_replace('"', '\"', $value) . '"';
        }
    }

}
