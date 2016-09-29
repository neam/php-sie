<?php

namespace sie\document;

class DataSource
{

    public $program;
    public $program_version;
    public $generated_on;
    public $company_name;

    public $accounts = [];
    public $balance_account_numbers = [];
    public $closing_account_numbers = [];

    public $balance_before;
    public $vouchers = [];

    public $financial_years = [];

    public $dimensions = [];

    function __construct($hash = [])
    {
        foreach ($hash as $k => $v) {
            $this->$k = $v;
        }
    }

    function balance_before($account_number, \DateTime $date)
    {
        return 99999;
    }

}
