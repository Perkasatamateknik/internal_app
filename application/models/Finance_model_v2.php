<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Finance_model_v2 extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_last_expense_number()
	{
		$query = $this->db->query("SELECT * FROM ms_finance_expenses ORDER BY expense_id DESC LIMIT 1")->row();
		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->expense_number, 4));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("EXP-%07d", $nextNumericPart);
	}

	public function get_paid_payments($month = false)
	{

		// bisa ambil dari debit(uang keluar) akun hutang lainnya | kode 2-2020001

		$this->db->select('SUM(amount) as paid')
			->from('ms_finance_account_transactions')
			->where('type', 'credit')
			->where_in('account_trans_cat_id', [1, 2, 3, 4]);

		if ($month) {
			$this->db->where('MONTH(date)', $month);
		} else {
			$this->db->where('MONTH(date)', date('m'));
		}
		return $this->db->get()->row()->paid;
	}

	public function get_unpaid_payments($month = false)
	{
		// seharusnya bisa ambil dari kredit(uang masuk) akun hutang lainnya | kode 2-20200
		$this->db->select('SUM(amount) as unpaid')
			->from('ms_finance_account_transactions')
			->where('type', 'credit')
			->where('account_id', 34);

		if ($month) {
			$this->db->where('MONTH(date)', $month);
		} else {
			$this->db->where('MONTH(date)', date('m'));
		}
		return $this->db->get()->row()->unpaid;
	}
}
