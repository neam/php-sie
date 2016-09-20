<?php

namespace sie;

use document/Renderer;
/*
require "attr_extras"
require "sie/document/voucher_series"
require "sie/document/renderer"
require "active_support/core_ext/module/delegation"
*/

  class Document
  {

      # Because some accounting software have limits
      #  - Fortnox should handle 200
      #  - Visma etc -> 100
      const DESCRIPTION_LENGTH_MAX = 100;

      //pattr_initialize :data_source

      public function render()
      {
          $this->add_header();
          $this->add_financial_years();
          $this->add_accounts();
          $this->add_dimensions();
          $this->add_balances();
          $this->add_vouchers();

          $this->renderer->render();
      }

      /*
        delegate :program, :program_version, :generated_on, :company_name,
          :accounts, :balance_account_numbers, :closing_account_numbers,
          :balance_before, :each_voucher, :dimensions,
          to: :data_source
      */

      private function add_header()
      {
          $this->add_line("FLAGGA", 0);
          $this->add_line("PROGRAM", program, program_version);
          $this->add_line("FORMAT", "PC8");
          $this->add_line("GEN", generated_on);
          $this->add_line("SIETYP", 4);
          $this->add_line("FNAMN", company_name);
      }

      private function add_financial_years()
      {
          financial_years . each_with_index do |date_range, index |
      $this->add_line("RAR", -index, date_range . begin, date_range . end)
      }
  }

    private function add_accounts()
{
    accounts . each do |account |
number = account . fetch(:number)
        description = account . fetch(:description).slice(0, DESCRIPTION_LENGTH_MAX)

        $this->add_line("KONTO", number, description);
      }
    }

    private function add_balances()
{
    financial_years . each_with_index do |date_range, index |
add_balance_rows("IB", -index, balance_account_numbers, date_range . begin)
        add_balance_rows("UB", -index, balance_account_numbers, date_range . end)
        add_balance_rows("RES", -index, closing_account_numbers, date_range . end)
      }
    }

    private function add_balance_rows(label, year_index, account_numbers, date, &block)
      account_numbers . each do |account_number |
balance = balance_before(account_number, date)

        # Accounts with no balance should not be in the SIE-file.
        # See paragraph 5.17 in the SIE file format guide (Rev. 4B).
        next unless balance

        $this->add_line(label, year_index, account_number, balance);
      }
    }

    private function add_dimensions()
{
    dimensions . each do |dimension |
dimension_number = dimension . fetch(:number)
        dimension_description = dimension . fetch(:description)
        $this->add_line("DIM", dimension_number, dimension_description);

        dimension . fetch(:objects).each do |object |
object_number = object . fetch(:number)
          object_description = object . fetch(:description)
          $this->add_line("OBJEKT", dimension_number, object_number, object_description);
        }
      }
    }

    private function add_vouchers()
{
    each_voucher do |voucher |
$this->add_voucher(voucher);
      }
    }

    private function add_voucher(opts) {
      number = opts . fetch(:number)
      booked_on = opts . fetch(:booked_on)
      description = opts . fetch(:description).slice(0, DESCRIPTION_LENGTH_MAX)
      voucher_lines = opts . fetch(:voucher_lines)
      voucher_series = opts . fetch(:series) {
    creditor = opts . fetch(:creditor)
        type = opts . fetch(:type)
        VoucherSeries .for(creditor, type)
      }

      $this->add_line("VER", voucher_series, number, booked_on, description);

      add_array do
voucher_lines . each do |line |
account_number = line . fetch(:account_number)
          amount = line . fetch(:amount)
          booked_on = line . fetch(:booked_on)
          dimensions = line . fetch(:dimensions, {
}).flatten
          # Some SIE-importers (fortnox) cannot handle descriptions longer than 200 characters,
          # but the specification has no limit.
          description = line . fetch(:description).slice(0, DESCRIPTION_LENGTH_MAX)

          $this->add_line("TRANS", account_number, dimensions, amount, booked_on, description);

          # Some consumers of SIE cannot handle single voucher lines (fortnox), so add another empty one to make
          # it balance. The spec just requires the sum of lines to be 0, so single lines with zero amount would conform,
          # but break for these implementations.
          if voucher_lines . size < 2 && amount . zero ?
              $this->add_line("TRANS", account_number, dimensions, amount, booked_on, description){;}
          }
        }
      }
    }

    delegate :add_line, :add_array, to: :renderer
  private $renderer;


    private function renderer()
{
    if (!$this->renderer) {
        $this->renderer = new Renderer();
    }
    return $this->renderer;
}

    private function financial_years()
{
    data_source . financial_years . sort_by {
    |
    date_range | date_range . first }.reverse
    }
}