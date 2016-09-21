<?php

namespace sie\document;

class CP437Encoding
{

    static public function convertFromUTF8ToCP437($text)
    {
        $encoded = iconv("UTF-8", 'CP437//TRANSLIT', $text);
        return $encoded;
    }

    static public function convertFromCP437ToUTF8($text)
    {
        $encoded = iconv("CP437", 'UTF-8', $text);
        return $encoded;
    }

}
