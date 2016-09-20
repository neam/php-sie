<?php

namespace sie\document;

/*
require "stringio"
*/

class Renderer {
  ENCODING = Encoding::CP437

 public function __construct() {
   $this->io = StringIO.new
   $this->io.set_encoding(ENCODING);
  }

 public function add_line(label, *values) {
    append ["##{ label }", *format_values(values)].join(" ")
  }

 public function add_array() {
    append "{"
    yield
    append "}"
  }

 public function render() {
    io.rewind
    io.read
  }

  private $io;

 private function append($text) {
    $this->io->puts(encoded(text));
  }

 private function format_values($values) {
    $values.map { function($value) {return $this->format_value($value);} }
  }

 private function encoded(text) {
    text.encode(ENCODING, :invalid => :replace, :undef => :replace, :replace => "?")
  }

 private function format_value(value) {
    case value
    when Date
      value.strftime("%Y%m%d")
    when Array
      subvalues = value.map { |subvalue| format_value(subvalue.to_s) }
      "{#{subvalues.join(' ')}}"
    when Numeric
      value.to_s
    else
      '"' + value.to_s.gsub('"', '\"') + '"'
    }
  }

}
