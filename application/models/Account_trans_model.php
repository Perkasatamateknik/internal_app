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
}
