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
		$this->db->where('trans_number', $id)->delete('ms_finance_expense_trans');
		$records = $this->db->where('trans_number', $id)->get('ms_finance_expense_trans')->result();

		$data = count_tax_expenses($records);

		// update trade payable
		$this->db->where('account_id', 34)->where('join_id', $id)->update('ms_finance_account_transactions', ['amount' => $data->amount_item_total]);

		if ($data->tax_withholding > 0) {
			// update tax total ke akun VAT Out - withholding
			$this->db->where('account_id', 45)->where('join_id', $id)->update('ms_finance_account_transactions', ['amount' => $data->tax_withholding]);
		}

		if ($data->tax_no_withholding > 0) {
			// update tax total ke akun VAT In - no withholding
			$this->db->where('account_id', 14)->where('join_id', $id)->update('ms_finance_account_transactions', ['amount' => $$data->tax_no_withholding]);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function delete_item_by_id($id)
	{
		$this->db->trans_start();

		// simpan dulu data trans_number nya
		$data = $this->db->where('expense_trans_id', $id)->get('ms_finance_expense_trans')->row();
		$trans_number = $data->trans_number;

		// hapus data
		$this->db->where('expense_trans_id', $id)->delete('ms_finance_expense_trans');

		// hitung ulang tagihan
		$this->calculate_expense_payment($trans_number);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function calculate_expense_payment($trans_number)
	{
		$records = $this->db->where('trans_number', $trans_number)->get('ms_finance_expense_trans')->result();
		$data = count_tax_expenses($records);

		// update trade payable
		$this->db->where('account_id', 34)->where('join_id', $trans_number)->update('ms_finance_account_transactions', ['amount' => $data->amount_item_total]);

		if ($data->tax_withholding > 0) {
			// update tax total ke akun VAT Out - withholding
			$this->db->where('account_id', 45)->where('join_id', $trans_number)->update('ms_finance_account_transactions', ['amount' => $data->tax_withholding]);
		}

		if ($data->tax_no_withholding > 0) {
			// update tax total ke akun VAT In - no withholding
			$this->db->where('account_id', 14)->where('join_id', $trans_number)->update('ms_finance_account_transactions', ['amount' => $$data->tax_no_withholding]);
		}
	}
}
