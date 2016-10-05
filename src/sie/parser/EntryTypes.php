<?php

namespace sie\parser;

class EntryTypes
{

    static public $ENTRY_TYPES = [
        "adress" => ['kontakt', 'utdelningsadr', 'postadr', 'tel'],
        "bkod" => ['SNI-kod'],
        "dim" => ['dimensionsnr', 'namn'],
        "enhet" => ['kontonr', 'enhet'],
        "flagga" => ['x'],
        "fnamn" => ['foretagsnamn'],
        "fnr" => ['foretagsid'],
        "format" => ['PC8'],
        "ftyp" => ['foretagstyp'],
        "gen" => ['datum', 'sign'],
        "ib" => ['arsnr', 'konto', 'saldo', 'kvantitet'],
        "konto" => ['kontonr', 'kontonamn'],
        "kptyp" => ['typ'],
        "ktyp" => ['kontonr', 'kontotyp'],
        "momskod" => ['kontonr', 'momskod'], // momskod is not part of the official SIE standard, but enough software vendors use it to be considered part of the de facto SIE standard
        "objekt" => ['dimensionsnr', 'objektnr', 'objektnamn'],
        "oib" => [
            'arsnr',
            'konto',
            ['name' => 'objekt', 'type' => ['dimensionsnr', 'objektnr']],
            'saldo',
            'kvantitet'
        ],
        "omfattn" => ['datum'],
        "orgnr" => ['orgnr', 'forvnr', 'verknr'],
        "oub" => [
            'arsnr',
            'konto',
            ['name' => 'objekt', 'type' => ['dimensionsnr', 'objektnr']],
            'saldo',
            'kvantitet'
        ],
        "pbudget" => [
            'arsnr',
            'period',
            'konto',
            ['name' => 'objekt', 'type' => ['dimensionsnr', 'objektnr']],
            'saldo',
            'kvantitet'
        ],
        "program" => ['programnamn', 'version'],
        "prosa" => ['text'],
        "psaldo" => [
            'arsnr',
            'period',
            'konto',
            ['name' => 'objekt', 'type' => ['dimensionsnr', 'objektnr']],
            'saldo',
            'kvantitet'
        ],
        "rar" => ['arsnr', 'start', 'slut'],
        "res" => ['ars', 'konto', 'saldo', 'kvantitet'],
        "sietyp" => ['typnr'],
        "sru" => ['konto', 'SRU-kod'],
        "taxar" => ['ar'],
        "trans" => [
            'kontonr',
            ['name' => 'objektlista', 'type' => ['dimensionsnr', 'objektnr'], 'many' => true],
            'belopp',
            'transdat',
            'transtext',
            'kvantitet',
            'sign'
        ],
        "rtrans" => [
            'kontonr',
            ['name' => 'objektlista', 'type' => ['dimensionsnr', 'objektnr'], 'many' => true],
            'belopp',
            'transdat',
            'transtext',
            'kvantitet',
            'sign'
        ],
        "btrans" => [
            'kontonr',
            ['name' => 'objektlista', 'type' => ['dimensionsnr', 'objektnr'], 'many' => true],
            'belopp',
            'transdat',
            'transtext',
            'kvantitet',
            'sign'
        ],
        "ub" => ['arsnr', 'konto', 'saldo', 'kvantitet'],
        "underdim" => ['dimensionsnr', 'namn', 'superdimension'],
        "valuta" => ['valutakod'],
        "ver" => ['serie', 'vernr', 'verdatum', 'vertext', 'regdatum', 'sign']
    ];

}
