<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accounts_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_accounts");
	}

	public function get($id)
	{
		$this->db->where('account_id', $id);
		return $this->db->get("ms_finance_accounts");
	}

	public function get_account_by_id($id)
	{
		$this->db->where('account_id', $id);
		return $this->db->get("ms_finance_accounts")->row();
	}

	public function get_account_by_account_code($id)
	{
		$this->db->where('account_code', $id);
		return $this->db->get("ms_finance_accounts")->row();
	}

	public function get_account_by_cat($id)
	{
		$this->db->where('category_id', $id);
		return $this->db->get("ms_finance_accounts")->row();
	}

	public function get_all_bank()
	{
		$this->db->where('category_id', 1); // 1 = bank
		$result = $this->db->get("ms_finance_accounts")->result();

		// dd($result);
		foreach ($result as $key => $row) {
			$result[$key]->saldo = $this->get_saldo($row->account_id);
		}

		return $result;
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

	public function insert($data, $saldo_awal = false)
	{

		if ($saldo_awal) {
			$this->db->trans_start();
			$this->db->insert('ms_finance_accounts', $data);

			$this->db->trans_complete();
			return $this->db->trans_status();
		} else {
			return $this->db->insert("ms_finance_accounts", $data);
		}
	}

	public function insert_with_saldo($data, $receive_doc, $trans_saldo_awal)
	{
		$this->db->trans_start();
		$this->db->insert('ms_finance_accounts', $data);
		$this->db->insert('ms_finance_account_receives', $receive_doc['data']);
		$this->db->insert('ms_finance_account_receive_trans', $receive_doc['items']);
		$this->db->insert_batch('ms_finance_account_transactions', $trans_saldo_awal);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function update($data)
	{
		return $this->db->update("ms_finance_accounts", $data, array('account_id' => $data['account_id']));
	}

	public function gel_all_account($query = false)
	{
		if ($query) {
			$this->db->like('account_name', $query);
			$this->db->or_like('account_code', $query);
		}

		// limit record to 10
		$this->db->limit(10);
		// order by account_name
		$this->db->order_by('account_name', 'ASC');
		return $this->db->get("ms_finance_accounts");
	}

	public function get_bank_account($query = false)
	{
		$this->db->where('category_id', 1);
		if ($query) {
			$this->db->like('account_name', $query);
			$this->db->or_like('account_code', $query);
		}

		// $this->db->limit(10);

		$this->db->order_by('account_name', 'ASC');
		return $this->db->get("ms_finance_accounts");
	}

	public function get_expenses_account($query = false)
	{
		$this->db->where_in('category_id', [14, 16]);
		if ($query) {
			$this->db->like('account_name', $query);
			$this->db->or_like('account_code', $query);
		}

		// $this->db->limit(10);

		$this->db->order_by('account_name', 'ASC');
		return $this->db->get("ms_finance_accounts");
	}

	public function get_tax_account($query = false)
	{
		$this->db->where('category_id', 12);
		if ($query) {
			$this->db->like('account_name', $query);
			$this->db->or_like('account_code', $query);
		}

		$this->db->limit(10);

		$this->db->order_by('account_name', 'ASC');
		return $this->db->get("ms_finance_accounts")->result();
	}

	public function get_last_account()
	{
		return $this->db->select('account_id')
			->from('ms_finance_accounts')
			->order_by('account_id', 'desc')
			->limit(1)->get()->row();
	}
}
