<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Expense_items_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_expense_trans");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("expense_id", $id);
		}

		$res = $this->db->get("ms_finance_expense_trans");


		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_by_trans_number($id)
	{
		$this->db->where("trans_number", $id);
		$res = $this->db->select(['ms_finance_expense_trans.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'xin_tax_types.name as tax_name'])
			->from('ms_finance_expense_trans')
			->join('ms_finance_accounts', 'ms_finance_expense_trans.account_id=ms_finance_accounts.account_id', 'INNER')
			->join('xin_tax_types', 'ms_finance_expense_trans.tax_id=xin_tax_types.tax_id', 'LEFT')->get();


		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function delete_item_by_trans_number($id)
	{
		$this->db->trans_start();
		$this->db->where('pr_number', $id)->delete('ms_finance_expense_trans');
		$records = $this->db->where('pr_number', $id)->get('ms_finance_expense_trans')->result();

		$tax_withholding = 0;
		$tax_no_withholding = 0;
		$amount_item = 0;

		foreach ($records as $r) {

			// hitung total amount_item
			$amount_item += $r->amount_item;

			// jika tax tanpa withholding
			if ($r->tax_withholding == 1) {
				//hitung total tax no withholding
				$tax_withholding += $r->tax_rate;
			} else {
				$tax_no_withholding += $r->tax_rate;
			}
		}

		// update trade payable
		$this->db->where('account_id', 34)->where('join_id', $id)->update('ms_finance_account_transactions', ['amount' => $amount_item + ($tax_no_withholding - $tax_withholding)]);

		if ($tax_withholding > 0) {
			// update tax total ke akun VAT Out - withholding
			$this->db->where('account_id', 45)->where('join_id', $id)->update('ms_finance_account_transactions', ['amount' => $tax_withholding]);
		}

		if ($tax_no_withholding > 0) {
			// update tax total ke akun VAT In - no withholding
			$this->db->where('account_id', 14)->where('join_id', $id)->update('ms_finance_account_transactions', ['amount' => $tax_no_withholding]);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
