<?php

namespace sie\document;

abstract class DataSource implements IDataSource
{

    public $program;
    public $program_version;
    public $generated_on;
    public $company_name;
    public $accounts = [];
    public $balance_account_numbers = [];
    public $closing_account_numbers = [];
    public $vouchers = [];
    public $financial_years = [];
    public $dimensions = [];

    function __construct($hash = [])
    {
        foreach ($hash as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * @return mixed
     */
    public function program()
    {
        return $this->program;
    }

    /**
     * @return mixed
     */
    public function program_version()
    {
        return $this->program_version;
    }

    /**
     * @return mixed
     */
    public function generated_on()
    {
        return $this->generated_on;
    }

    /**
     * @return mixed
     */
    public function company_name()
    {
        return $this->company_name;
    }

    /**
     * @return array
     */
    public function accounts()
    {
        return $this->accounts;
    }

    /**
     * @return array
     */
    public function balance_account_numbers()
    {
        return $this->balance_account_numbers;
    }

    /**
     * @return array
     */
    public function closing_account_numbers()
    {
        return $this->closing_account_numbers;
    }

    /**
     * Used to calculate balance before (and on) the given date for an account.
     * @param $account_number
     * @param \DateTime $date
     * @return mixed
     */
    abstract public function balance_before($account_number, \DateTime $date);

    /**
     * @return array
     */
    public function vouchers()
    {
        return $this->vouchers;
    }

    /**
     * @return array
     */
    public function financial_years()
    {
        return $this->financial_years;
    }

    /**
     * @return array
     */
    public function dimensions()
    {
        return $this->dimensions;
    }

}
