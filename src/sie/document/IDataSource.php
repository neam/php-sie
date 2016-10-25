<?php

namespace sie\document;

interface IDataSource
{

    /**
     * @return mixed
     */
    public function program();

    /**
     * @return mixed
     */
    public function program_version();

    /**
     * @return mixed
     */
    public function generated_on();

    /**
     * @return mixed
     */
    public function company_name();

    /**
     * @return array
     */
    public function accounts();

    /**
     * @return array
     */
    public function balance_account_numbers();

    /**
     * @return array
     */
    public function closing_account_numbers();

    /**
     * Used to calculate balance before (and on) the given date for an account.
     * @param $account_number
     * @param \DateTime $date
     * @return mixed
     */
    public function balance_before($account_number, \DateTime $date, $label = null, $year_index = null);

    /**
     * @return array
     */
    public function vouchers();

    /**
     * @return array
     */
    public function financial_years();

    /**
     * @return array
     */
    public function dimensions();

}
