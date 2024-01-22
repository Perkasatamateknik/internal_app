<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_trans_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_account_trans");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("account_id", $id);
		}
		return $this->db->get("ms_finance_account_transactions");
	}

	public function get_trans($id)
	{
		return $this->db->select(['ms_finance_account_transactions.*', 'ms_finance_account_trans_categories.trans_type', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id', 'inner')
			->join('ms_finance_account_trans_categories', 'ms_finance_account_transactions.account_trans_cat_id=ms_finance_account_trans_categories.trans_cat_id', 'inner')
			->where('ms_finance_account_transactions.account_trans_id', $id)->get()->row();

		return $this->db->select(["ms_finance_account_transactions.*, '"])->from('ms_finance_account_transactions')->get("ms_finance_account_transactions");
	}

	public function insert_payment($data)
	{
		return $this->db->insert_batch("ms_finance_account_transactions", $data);
	}

	public function get_saldo($id)
	{
		$this->db->select_sum('amount');
		$this->db->select('type');
		$this->db->group_by('type');
		$this->db->where('account_id', $id);
		$result =  $this->db->get("ms_finance_account_transactions")->result();

		$saldo = 0;

		foreach ($result as $key => $value) {
			if ($value->type == 'debit') {
				$saldo += $value->amount;
			} else {
				$saldo -= $value->amount;
			}
		}

		return $saldo;
	}

	public function open_payment($id)
	{
		$record = $this->db->where('account_id', $id)->get('ms_finance_accounts')->row();
		$records = $this->db->where('account_id', $id)->get('ms_finance_account_transactions')->row();
		return $records;
	}

	public function get_trans_type($id)
	{
		return $this->db->where('trans_cat_id', $id)->get('ms_finance_account_trans_categories')->row();
	}
}
