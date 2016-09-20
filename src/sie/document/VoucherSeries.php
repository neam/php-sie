<?php

namespace sie\document;

class VoucherSeries
{
    const DEBTOR_INVOICE = "KF";
    const DEBTOR_PAYMENT = "KI";
    const SUPPLIER_INVOICE = "LF";
    const SUPPLIER_PAYMENT = "KB";
    const OTHER = "LV";

    public function self_for($creditor, $type)
    {
        switch ($type) {
            case "invoice":
                return $creditor ? static::SUPPLIER_INVOICE : static::DEBTOR_INVOICE;
            case "payment":
                return $creditor ? static::SUPPLIER_PAYMENT : static::DEBTOR_PAYMENT;
            default:
                return static::OTHER;

        }
    }
}
