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

    // tests
    public function testMe()
    {

    }

    /*
  subject(:series) { Sie::Document::VoucherSeries.for(creditor, type) }

  let(:type) { :invoice }

  context "when on the creditor side" do
    let(:creditor) { true }

    context "with an invoice" do
      let(:type) { :invoice }
      it { should == "LF" }
    end

    context "with a payment" do
      let(:type) { :payment }
      it { should == "KB" }
    end
  end

  context "when on the debtor side" do
    let(:creditor) { false }

    context "with an invoice" do
      let(:type) { :invoice }
      it { should == "KF" }
    end

    context "with a payment" do
      let(:type) { :payment }
      it { should == "KI" }
    end
  end

  context "when neither a payment or invoice" do
    let(:creditor) { false }
    let(:type) { :manual_bookable }
    it { should == "LV" }
  end
end
    */

}