<?php

namespace sie;

use sie\document\DataSource;
use sie\document\VoucherSeries;
use sie\document\Renderer;
use NumberFormatter;

class Document
{

    # Because some accounting software have limits
    #  - Fortnox should handle 200
    #  - Visma etc -> 100
    const DESCRIPTION_LENGTH_MAX = 100;

    /** @var DataSource */
    public $data_source;

    public function __construct(DataSource $data_source)
    {
        $this->data_source = $data_source;
    }

    public function render()
    {
        $this->renderer = null;
        $this->add_header();
        $this->add_financial_years();
        $this->add_accounts();
        $this->add_dimensions();
        $this->add_balances();
        $this->add_vouchers();
        return $this->renderer()->render();
    }

    protected function add_header()
    {
        $this->renderer()->add_line("FLAGGA", [0]);
        $this->renderer()->add_line("PROGRAM", [$this->data_source->program(), $this->data_source->program_version()]);
        $this->renderer()->add_line("FORMAT", ["PC8"]);
        $this->renderer()->add_line("GEN", [$this->data_source->generated_on()]);
        $this->renderer()->add_line("SIETYP", [4]);
        $this->renderer()->add_line("FNAMN", [$this->data_source->company_name()]);
        if (is_callable([$this->data_source, "company_orgnr"])) {
            $this->renderer()->add_line("ORGNR", [$this->data_source->company_orgnr()]);
        }
        if (is_callable([$this->data_source, "company_address"])) {
            $this->renderer()->add_line("ADRESS", [$this->data_source->company_address()]);
        }
    }

    protected function add_financial_years()
    {
        /**
         * @var \DatePeriod $date_range
         */
        foreach ($this->financial_years() as $index => $date_range) {
            $this->renderer()->add_line("RAR", [-$index, $date_range->getStartDate(), $date_range->getEndDate()]);
        }
    }

    protected function add_accounts()
    {
        foreach ($this->data_source->accounts() as $account) {
            $number = $account["number"];
            $description = iconv_substr($account["description"], 0, static::DESCRIPTION_LENGTH_MAX, "UTF-8");
            $this->renderer()->add_line("KONTO", [$number, $description]);
            if (array_key_exists("ktyp", $account)) {
                $this->renderer()->add_line("KTYP", [$number, $account["ktyp"]]);
            }
            if (array_key_exists("sru", $account)) {
                $this->renderer()->add_line("SRU", [$number, $account["sru"]]);
            }
            if (array_key_exists("momskod", $account)) {
                $this->renderer()->add_line("MOMSKOD", [$number, $account["momskod"]]);
            }
        }
    }

    protected function add_balances()
    {
        /**
         * @var \DatePeriod $date_range
         */
        foreach ($this->financial_years() as $index => $date_range) {
            $this->add_balance_rows(
                "IB",
                -$index,
                $this->data_source->balance_account_numbers(),
                $date_range->getStartDate()
            );
            $this->add_balance_rows(
                "UB",
                -$index,
                $this->data_source->balance_account_numbers(),
                $date_range->getEndDate()
            );
            $this->add_balance_rows(
                "RES",
                -$index,
                $this->data_source->closing_account_numbers(),
                $date_range->getEndDate()
            );
        }
    }

    protected function add_balance_rows($label, $year_index, $account_numbers, $date)
    {
        foreach ($account_numbers as $account_number) {
            $balance = $this->data_source->balance_before($account_number, $date, $label, $year_index);

            # Accounts with no balance should not be in the SIE-file.
            # See paragraph 5.17 in the SIE file format guide (Rev. 4B).
            if (!$balance) {
                continue;
            }

            $balance = $this->formatAmount($balance);

            $this->renderer()->add_line($label, [$year_index, $account_number, $balance]);
        }
    }

    protected function add_dimensions()
    {
        foreach ($this->data_source->dimensions() as $dimension) {

            $dimension_number = $dimension["number"];
            $dimension_description = $dimension["description"];
            $this->renderer()->add_line("DIM", [$dimension_number, $dimension_description]);

            foreach ($dimension["objects"] as $object) {
                $object_number = $object["number"];
                $object_description = $object["description"];
                $this->renderer()->add_line("OBJEKT", [$dimension_number, $object_number, $object_description]);
            }
        }
    }

    protected function add_vouchers()
    {
        foreach ($this->data_source->vouchers() as $voucher) {
            $this->add_voucher($voucher);
        }
    }

    protected function add_voucher($opts)
    {

        $number = $opts["number"];
        $booked_on = $opts["booked_on"];
        $description = iconv_substr($opts["description"], 0, static::DESCRIPTION_LENGTH_MAX, "UTF-8");
        $voucher_lines = $opts["voucher_lines"];
        if (array_key_exists("series", $opts)) {
            $voucher_series = $opts["series"];
        } else {
            $creditor = $opts["creditor"];
            $type = $opts["type"];
            $voucher_series = (new VoucherSeries())->self_for($creditor, $type);
        }

        $this->renderer()->add_line("VER", [$voucher_series, $number, $booked_on, $description]);

        $this->renderer()->add_beginning_of_array();

        foreach ($voucher_lines as $line) {
            $account_number = $line["account_number"];
            $amount = $this->formatAmount($line["amount"]);
            if (array_key_exists("booked_on", $line)) {
                $booked_on = $line["booked_on"];
            } else {
                $booked_on = null;
            }
            if (array_key_exists("dimensions", $line)) {
                $dimensions = $line["dimensions"];
            } else {
                $dimensions = [];
            }

            if (array_key_exists("description", $line)) {
                # Some SIE-importers (fortnox) cannot handle descriptions longer than 200 characters,
                # but the specification has no limit.
                $description = iconv_substr($line["description"], 0, static::DESCRIPTION_LENGTH_MAX, "UTF-8");
            } else {
                $description = null;
            }

            if (array_key_exists("type", $line)) {
                $type = strtoupper($line["type"]);
            } else {
                $type = "TRANS";
            }

            if ($type === "BTRANS" || $type === "RTRANS") {

                if (array_key_exists("changed_by", $line)) {
                    $changed_by = $line["changed_by"];
                } else {
                    $changed_by = null;
                }

                if (array_key_exists("changed_on", $line)) {
                    $changed_on = $line["changed_on"];
                } else {
                    $changed_on = null;
                }

                $this->renderer()->add_line(
                    $type,
                    [$account_number, $dimensions, $amount, $changed_on, $description, null, $changed_by]
                );
            }
            $this->renderer()->add_line("TRANS", [$account_number, $dimensions, $amount, $booked_on, $description]);

            # Some consumers of SIE cannot handle single voucher lines (fortnox), so add another empty one to make
            # it balance. The spec just requires the sum of lines to be 0, so single lines with zero amount would conform,
            # but break for these implementations.
            if (count($voucher_lines) < 2 && $amount == 0) {
                $this->renderer()->add_line("TRANS", [$account_number, $dimensions, $amount, $booked_on, $description]);
            }

        }

        $this->renderer()->add_end_of_array();

    }

    /** @var NumberFormatter */
    protected $numberFormatter;

    protected function formatAmount($amount)
    {
        $formatter = $this->numberFormatter();
        return $formatter->format((double) $amount, NumberFormatter::TYPE_DOUBLE);
    }

    /*
     * Dot as decimal separator, no thousands separator and maximally 2 decimal places
     * See paragraph 5.9 in the SIE file format guide (Rev. 4B).
     */
    protected function numberFormatter()
    {
        if (!$this->numberFormatter) {
            $this->numberFormatter = new NumberFormatter('en_US', NumberFormatter::PATTERN_DECIMAL, '0.00');
        }
        return $this->numberFormatter;
    }

    /** @var Renderer */
    protected $renderer;

    protected function renderer()
    {
        if (!$this->renderer) {
            $this->renderer = new Renderer();
        }
        return $this->renderer;
    }

    protected function financial_years()
    {
        $financial_years = $this->data_source->financial_years();

        if (empty($financial_years)) {
            return [];
        }

        usort(
            $financial_years,
            function (\DatePeriod $fy1, \DatePeriod $fy2) {
                return $fy1->getStartDate() > $fy2->getStartDate();
            }
        );

        return array_reverse($financial_years);
    }

}
