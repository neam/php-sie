<?php
namespace document;

use sie\document\VoucherSeries;

class VoucherSeriesTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitGuy
     */
    protected $guy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    protected function series($creditor, $type)
    {
        return (new VoucherSeries())->self_for($creditor, $type);
    }

    public function testSelfForProvider()
    {
        return [
            // when on the creditor side with an invoice
            [true, 'invoice', 'LF'],
            // when on the creditor side with an payment
            [true, 'payment', 'KB'],
            // when on the debtor side with an invoice
            [false, 'invoice', 'KF'],
            // when on the debtor side with an payment
            [false, 'payment', 'KI'],
            // when when neither a payment or invoice
            [true, 'manual_bookable', 'LV'],
        ];
    }

    // tests

    /**
     * @dataProvider testSelfForProvider
     */
    public function testSelfFor($creditor, $type, $expected)
    {
        $result = $this->series($creditor, $type);
        $this->assertEquals($expected, $result);
    }

}