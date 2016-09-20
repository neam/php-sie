<?php

namespace sie\document;

class DataSource {

    public $program;
    public $program_version;
    public $generated_on;
    public $company_name;

    public $accounts = [];
    public $balance_account_numbers = [];
    public $closing_account_numbers = [];

    public $balance_before;
    public $each_voucher = [];

    public $financial_years = [];

    public $dimensions = [];

}