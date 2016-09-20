<?php


class DocumentTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitGuy
     */
    protected $guy;

    protected function _before()
    {
        date_default_timezone_set('UTC');
    }

    protected function _after()
    {
    }

    protected function financial_years()
    {
        $interval = new DateInterval('P1D');
        return [
            new DatePeriod(new DateTime('2011-01-01'), $interval, new DateTime('2011-12-31')),
            new DatePeriod(new DateTime('2012-01-01'), $interval, new DateTime('2012-12-31')),
            new DatePeriod(new DateTime('2013-01-01'), $interval, new DateTime('2013-12-31')),
        ];
    }

    protected function generated_on()
    {
        return (new DateTime())->modify('-1 day'); // Date.yesterday
    }

    protected function accounts()
    {
        return [
            [
                "number" => 1500,
                "description" => "Customer ledger",
            ]
        ];
    }

    protected function vouchers()
    {
        return [
            [
                "creditor" => false,
                "type" => "invoice",
                "number" => 1,
                "booked_on" => (new DateTime())->setDate(2011, 9, 3),
                "description" => "Invoice 1",
                "voucher_lines" => [
                    [
                        "account_number" => 1500,
                        "amount" => 512.0,
                        "booked_on" => (new DateTime())->setDate(2011, 9, 3),
                        "description" => "Item 1",
                        "dimensions" => [6 => 1]
                    ],
                    [
                        "account_number" => 3100,
                        "amount" => -512.0,
                        "booked_on" => (new DateTime())->setDate(2011, 9, 3),
                        "description" => "Item 1",
                        "dimensions" => [6 => 1]
                    ],
                ]
            ],
            [
                "creditor" => true,
                "type" => "payment",
                "number" => 2,
                "booked_on" => (new DateTime())->setDate(2012, 8, 31),
                "description" => "Payout 1",
                "voucher_lines" => [
                    [
                        "account_number" => 2400,
                        "amount" => 256.0,
                        "booked_on" => (new DateTime())->setDate(2012, 8, 31),
                        "description" => "Payout line 1"
                    ],
                    [
                        "account_number" => 1970,
                        "amount" => -256.0,
                        "booked_on" => (new DateTime())->setDate(2012, 8, 31),
                        "description" => "Payout line 2"
                    ],
                ]
            ]
        ];
    }

    protected function dimensions()
    {
        return [
            [
                "number" => 6,
                "description" => "Project",
                "objects" => [
                    ["number" => 1, "description" => "Education"]
                ]
            ]
        ];
    }

    protected function doc()
    {
        $data_source = new TestDataSource(
            [
                "accounts" => $this->accounts(),
                "vouchers" => $this->vouchers(),
                "program" => "Foonomic",
                "program_version" => "3.11",
                "generated_on" => $this->generated_on(),
                "company_name" => "Foocorp",
                "financial_years" => $this->financial_years(),
                "balance_account_numbers" => [1500, 2400, 9999],
                "closing_account_numbers" => [3100, 9999],
                "dimensions" => $this->dimensions(),
            ]
        );

        return (new \sie\Document($data_source));
    }

    protected function sie_file()
    {
        codecept_debug(__METHOD__);
        codecept_debug($this->doc());
        codecept_debug("render result: '" . $this->doc()->render() . "'");
        codecept_debug((new \sie\Parser())->parse($this->doc()->render()));

        return (new \sie\Parser())->parse($this->doc()->render());
    }

    // tests

    public function testAddsAHeader()
    {
        return;
        $this->sie_file();
        $this->assertEquals("0", $this->entry_attribute("flagga", "x"));
        $this->assertEquals("Foonomic", $this->entry_attribute("program", "programnamn"));
        $this->assertEquals("3.11", $this->entry_attribute("program", "version"));
        $this->assertEquals("PC8", $this->entry_attribute("format", "PC8"));
        $this->assertEquals($this->generated_on()->format("Ymd"), $this->entry_attribute("gen", "datum"));
        $this->assertEquals("4", $this->entry_attribute("sietyp", "typnr"));
        $this->assertEquals("Foocorp", $this->entry_attribute("fnamn", "foretagsnamn"));

    }

    /*
      it "has accounting years" do
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 0, "arsnr")).to eq("0;
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 0, "start")) . to eq(
        "20130101;
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 0, "slut"))20131231;
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 1, "arsnr")).to eq(" - 1;
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 1, "start")) . to eq(
        "20120101;
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 1, "slut"))20121231;
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 2, "arsnr")).to eq(" - 2;
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 2, "start")) . to eq(
        "20110101;
        $this->assertEquals("", $this->indexed_entry_attribute("rar", 2, "slut"))20111231;
      }

      it "has accounts" do
        $this->assertEquals("", $this->indexed_entry_attributes("konto", 0)).to eq("kontonr" => "1500", "kontonamn" => "Customer ledger;
      }

it "has dimensions" do
$this->assertEquals("", $this->indexed_entry_attributes("dim", 0)).to eq("dimensionsnr" => "6", "namn" => "Project;
      }

      it "has objects" do
        $this->assertEquals("", $this->indexed_entry_attributes("objekt", 0)).to eq("dimensionsnr" => "6", "objektnr" => "1", "objektnamn" => "Education;
}

it "has balances brought forward (ingående balans)" do
$this->assertEquals("", $this->indexed_entry_attributes("ib", 0)) . not_to eq("arsnr" =>  "0", "konto" => "9999", "saldo" => ";
        $this->assertEquals("", $this->indexed_entry_attributes("ib", 0))arsnr" =>  "0", "konto" => "1500", "saldo" => "1600.0;
        $this->assertEquals("", $this->indexed_entry_attributes("ib", 1))arsnr" =>  "0", "konto" => "2400", "saldo" => "2500.0;
        $this->assertEquals("", $this->indexed_entry_attributes("ib", 2))arsnr" => "-1", "konto" => "1500", "saldo" => "1600.0;
        $this->assertEquals("", $this->indexed_entry_attributes("ib", 3))arsnr" => "-1", "konto" => "2400", "saldo" => "2500.0;
        $this->assertEquals("", $this->indexed_entry_attributes("ib", 4))arsnr" => "-2", "konto" => "1500", "saldo" => "1600.0;
        $this->assertEquals("", $this->indexed_entry_attributes("ib", 5))arsnr" => "-2", "konto" => "2400", "saldo" => "2500.0;
      }

      it "has balances carried forward(utgående balans)" do
        $this->assertEquals("", $this->indexed_entry_attributes("ub", 0)).not_to eq("arsnr" =>  "0", "konto" => "9999", "saldo" => ";
        $this->assertEquals(
            "",
            $this->indexed_entry_attributes("ub", 0)
        )arsnr" =>  "0", "konto" => "1500", "saldo" => "4600.0;
        $this->assertEquals(
            "",
            $this->indexed_entry_attributes("ub", 1)
        )arsnr" =>  "0", "konto" => "2400", "saldo" => "5500.0;
        $this->assertEquals(
            "",
            $this->indexed_entry_attributes("ub", 2)
        )arsnr" => " - 1", "konto" => "1500", "saldo" => "4600.0;
        $this->assertEquals(
            "",
            $this->indexed_entry_attributes("ub", 3)
        )arsnr" => " - 1", "konto" => "2400", "saldo" => "5500.0;
        $this->assertEquals(
            "",
            $this->indexed_entry_attributes("ub", 4)
        )arsnr" => " - 2", "konto" => "1500", "saldo" => "4600.0;
        $this->assertEquals(
            "",
            $this->indexed_entry_attributes("ub", 5)
        )arsnr" => " - 2", "konto" => "2400", "saldo" => "5500.0;
      }

      it "has closing account balances (saldo för resultatkonto)" do
$this->assertEquals("", $this->indexed_entry_attributes("res", 0)) . not_to eq("ars" =>  "0", "konto" => "9999", "saldo" =>  ";
        $this->assertEquals("", $this->indexed_entry_attributes("res", 0))ars" =>  "0", "konto" => "3100", "saldo" =>  "6200.0;
        $this->assertEquals("", $this->indexed_entry_attributes("res", 1))ars" => "-1", "konto" => "3100", "saldo" =>  "6200.0;
        $this->assertEquals("", $this->indexed_entry_attributes("res", 2))ars" => "-2", "konto" => "3100", "saldo" =>  "6200.0;
      }

      it "has vouchers" do
        $this->assertEquals("", $this->indexed_entry("ver", 0).attributes).to eq(
          "serie" => "KF", "vernr" => "1",
          "verdatum" => "20110903", "vertext" => "Invoice 1"
        )
        $this->assertEquals("", $this->indexed_voucher_entries(0)[0].attributes).to eq(
          "kontonr" => "1500", "belopp" =>  "512.0",
          "transdat" => "20110903", "transtext" => "Item 1",
          "objektlista" => [{"dimensionsnr" => "6", "objektnr" => "1"}]
        )
        $this->assertEquals("", $this->indexed_voucher_entries(0)[1].attributes).to eq(
          "kontonr" => "3100", "belopp" => " - 512.0",
          "transdat" => "20110903", "transtext" => "Item 1",
          "objektlista" => [{"dimensionsnr" => "6", "objektnr" => "1"}]
        )

        $this->assertEquals("", $this->indexed_entry("ver", 1).attributes).to eq(
          "serie" => "KB", "vernr" => "2",
          "verdatum" => "20120831", "vertext" => "Payout 1"
        )
        $this->assertEquals("", $this->indexed_voucher_entries(1)[0].attributes).to eq(
          "kontonr" => "2400", "belopp" =>  "256.0",
          "transdat" => "20120831", "transtext" => "Payout line 1",
          "objektlista" => []
        )
        $this->assertEquals("", $this->indexed_voucher_entries(1)[1].attributes).to eq(
          "kontonr" => "1970", "belopp" => " - 256.0",
          "transdat" => "20120831", "transtext" => "Payout line 2",
          "objektlista" => []
        )
      }

      context "with really long descriptions" do
        let(:accounts) {
          [
            number" =>1500, description" =>"k" * 101  # Make sure that the description exceeds the limit (100 chars).
          ]
        }
        let(:vouchers) {
          [
            build_voucher(
              description ="d" * 101,
              voucher_lines =[
                build_voucher_line(description ="v" * 101),
                build_voucher_line(description ="Payout line 2"),
              ]
            )
          ]
        }

        it "truncates the descriptions" do
          $this->assertEquals("", $this->indexed_entry_attributes("konto", 0)).to eq("kontonr" => "1500", "kontonamn" => "k" * 100)
          $this->assertEquals("", $this->indexed_entry("ver", 0).attributes["vertext"]).to eq("d" * 100)
          $this->assertEquals("", $this->indexed_voucher_entries(0)[0].attributes["transtext"]).to eq("v" * 100)
        }
      }

      context "with a zeroed single voucher line" do
        let(:vouchers) {
          [
            build_voucher(voucher_lines =[ build_voucher_line(amount =0) ])
          ]
        }

        it "ensures there are at least two lines" do
          $this->assertEquals("", $this->indexed_voucher_entries(0).size).to eq(2)
        }
      }

      context "with a series defined" do
        let(:vouchers) {
          [
            build_voucher(series ="X"),
          ]
        }

        it "reads the series from the voucher" do
          $this->assertEquals("", $this->indexed_entry("ver", 0).attributes["serie"]).to eq("X;
        }
      }
      */

    protected function build_voucher($attributes)
    {
        $defaults = [
            "creditor" => true,
            "type" => "payment",
            "number" => 1,
            "booked_on" => new DateTime(),
            "description" => "A voucher",
            "voucher_lines" => [
                $this->build_voucher_line(),
                $this->build_voucher_line(),
            ],
        ];
        return array_merge($attributes, $defaults);
    }

    protected function build_voucher_line($attributes = [])
    {
        $defaults = [
            "account_number" => 1234,
            "amount" => 1,
            "booked_on" => DateTime::createFromFormat("Ymd", (new DateTime())->format("Ymd")),
            "description" => "A voucher line"
        ];
        return array_merge($attributes, $defaults);
    }

    protected function entry_attribute($label, $attribute)
    {
        return $this->indexed_entry_attribute($label, 0, $attribute);
    }

    protected function indexed_entry_attribute($label, $index, $attribute)
    {
        $attributes = $this->indexed_entry_attributes($label, $index);
        if (!array_key_exists($attribute, $attributes)) {
            throw new Exception("Unknown attribute $attribute in " . print_r(array_keys($attributes), true));
        }
        return $attributes[$attribute];
    }

    protected function indexed_entry_attributes($label, $index)
    {
        return $this->indexed_entry($label, $index)->attributes;
    }

    protected function indexed_voucher_entries($index)
    {
        return $this->indexed_entry("ver", $index)->entries;
    }

    protected function indexed_entry($label, $index)
    {
        $entries_with_label = $this->sie_file()->entries_with_label($label);
        if (!array_key_exists($index, $entries_with_label)) {
            throw new Exception("No entry with label " . print_r($label, true) . " found!");
        }
        return $entries_with_label[$index];
    }


}

class TestDataSource extends \sie\document\DataSource
{

    # vouchers is not part of the expected interface so making it private.
    #
    # Sie::Document uses #each_voucher so that you can build documents for huge sets of vouchers
    # by loading them in batches.
    private $vouchers;

    function __construct($hash = [])
    {
        foreach ($hash as $k => $v) {
            $this->$k = $v;
        }
    }

    function each_voucher($callback)
    {
        foreach ($this->vouchers as $voucher) {
            $callback($voucher);
        }
    }

    function balance_before($account_number, \DateTime $date)
    {
        if ($account_number == 9999) {
            # So we can test empty balances.
            return null;
        } else {
            # Faking a fetch based on date and number.
            return (int) $account_number + ($date->format("j") * 100);
        }
    }
}
