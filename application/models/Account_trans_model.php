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

	/**
	 * Ambil data transaksi berdasarkan join id
	 *
	 * @return	object|null
	 */
	public function get_by_join_id($join_id, $type = 'credit')
	{

		if ($join_id) {
			$this->db->where("join_id", $join_id);
		}
		$this->db->where("type", $type);
		return $this->db->get("ms_finance_account_transactions")->row();
	}

	/**
	 * Ambil data transaksi down payment (DP)
	 *
	 * @return	object|null
	 */
	public function get_dp_purchase($join_id)
	{

		$this->db->where("account_id", 12); // akun 
		if ($join_id) {
			$this->db->where("join_id", $join_id);
		}
		$res = $this->db->get("ms_finance_account_transactions")->row();
		if ($res) {
			return $res;
		} else {
			return null;
		}
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

	public function get_trans_by_join_id($id, $account_id = false)
	{
		$res = $this->db->select(['ms_finance_account_transactions.*', 'ms_finance_account_trans_categories.trans_type', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id', 'inner')
			->join('ms_finance_account_trans_categories', 'ms_finance_account_transactions.account_trans_cat_id=ms_finance_account_trans_categories.trans_cat_id', 'inner');

		if ($account_id) {
			$res->where('ms_finance_account_transactions.account_id', $account_id);
		}

		return $res->where('ms_finance_account_transactions.join_id', $id)->get()->row();
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

	// new
	public function get_purchase_payment($account_trans_cat_id, $join_id, $account_id_tagihan = false)
	{
		// cari data tagihan di akun 34 trade payable
		$record = $this->db->where('account_id', $account_id_tagihan)->where('account_trans_cat_id', $account_trans_cat_id)->where('join_id', $join_id)->where('type', 'debit')->get('ms_finance_account_transactions')->row();

		// cari akun pembayaran dari akun cash an bank
		$records = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'ms_finance_accounts.category_id', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id', 'inner')
			->where('ms_finance_accounts.category_id', 1)
			->where('ms_finance_account_transactions.account_trans_cat_id', $account_trans_cat_id)->where('ms_finance_account_transactions.join_id', $join_id)->where('ms_finance_account_transactions.type', 'credit')->get()->result();

		$tagihan_dibayar = 0;
		foreach ($records as $val) {
			$tagihan_dibayar += $val->amount;
		}

		$data = new stdClass;
		$data->sisa_tagihan = $record->amount - $tagihan_dibayar;
		$data->jumlah_tagihan = $record->amount;
		$data->jumlah_dibayar = $tagihan_dibayar;
		$data->log_payments = $records;

		// dd($data);
		return $data;
	}

	public function get_payment($account_trans_cat_id, $join_id, $account_id_tagihan = false)
	{
		// cari data tagihan di akun 34 trade payable
		$record = $this->db->where('account_id', $account_id_tagihan)->where('account_trans_cat_id', $account_trans_cat_id)->where('join_id', $join_id)->where('type', 'credit')->get('ms_finance_account_transactions')->row();

		// cari akun pembayaran dari akun cash an bank
		$records = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'ms_finance_accounts.category_id', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id', 'inner')
			->where('ms_finance_accounts.category_id', 1)
			->where('ms_finance_account_transactions.account_trans_cat_id', $account_trans_cat_id)->where('ms_finance_account_transactions.join_id', $join_id)->where('ms_finance_account_transactions.type', 'credit')->get()->result();

		$tagihan_dibayar = 0;
		foreach ($records as $val) {
			$tagihan_dibayar += $val->amount;
		}

		$data = new stdClass;
		$data->sisa_tagihan = $record->amount - $tagihan_dibayar;
		$data->jumlah_tagihan = $record->amount;
		$data->jumlah_dibayar = $tagihan_dibayar;
		$data->log_payments = $records;

		return $data;
	}

	public function get_purchasing($join_id)
	{
		// cari data tagihan di akun 34 trade payable
		$record = $this->db->where('account_id', 34)->where('account_trans_cat_id', 6)->where('join_id', $join_id)->where('type', 'credit')->get('ms_finance_account_transactions')->row();


		// cari akun pembayaran dari akun cash an bank
		$records = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'ms_finance_accounts.category_id', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')
			->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id', 'inner')
			->where('ms_finance_accounts.category_id', 1) // lock ke bank account
			->where('ms_finance_account_transactions.account_trans_cat_id', 6)->where('ms_finance_account_transactions.join_id', $join_id)->where('ms_finance_account_transactions.type', 'credit')->get()->result();

		$tagihan_dibayar = 0;
		foreach ($records as $val) {
			$tagihan_dibayar += $val->amount;
		}

		$data = new stdClass;
		$data->sisa_tagihan = $record->amount - $tagihan_dibayar;
		$data->jumlah_tagihan = $record->amount;
		$data->jumlah_dibayar = $tagihan_dibayar;
		$data->log_payments = $records;

		// dd($data);

		return $data;
	}

	public function get_purchasing_by_log($log)
	{
		// cari data tagihan di akun 34 trade payable
		$record = $this->db->select_sum('amount')->where('account_id', 34)->where('account_trans_cat_id', 6)->where('join_id', $log->payment_number)->where('type', 'credit')->get('ms_finance_account_transactions')->row();
		$total_tagihan = $record->amount ?? 0;

		// dd($record)

		// cari akun pembayaran dari akun cash an bank
		$records = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'ms_finance_accounts.category_id', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')
			->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id', 'inner')
			->where('ms_finance_accounts.category_id', 1) // lock ke bank account
			->where('ms_finance_account_transactions.join_id', $log->payment_number)
			// ->where('ms_finance_account_transactions.account_trans_cat_id', 6)
			->where('ms_finance_account_transactions.type', 'credit')
			->get()->result();

		$tagihan_dibayar = 0;
		foreach ($records as $val) {
			$tagihan_dibayar += $val->amount;
		}

		$data = new stdClass;

		if ($total_tagihan == 0) {
			$data->sisa_tagihan = 0;
		} else {
			$data->sisa_tagihan = $total_tagihan - $tagihan_dibayar;
		}

		$data->jumlah_tagihan = $total_tagihan;
		$data->jumlah_dibayar = $tagihan_dibayar;
		$data->log_payments = $records;

		// dd($data);
		return $data;
	}

	public function get_purchasing_dp_by_log($log)
	{
		return $this->db->select_sum('amount')
			->from('ms_finance_account_transactions')
			->where('join_id', $log->payment_number)
			->where('type', 'credit')
			->get()->row()->amount;
	}
}
