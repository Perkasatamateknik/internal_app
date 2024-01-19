<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_transfer_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function trans_number()
	{
		$query = $this->get_last_trans_number();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->trans_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("TR-%05d", $nextNumericPart);
	}

	public function all()
	{
		return $this->db->where('status !=', 'draft')->get("ms_finance_account_transfers");
	}

	public function draft()
	{
		return $this->db->where('status =', 'draft')->get("ms_finance_account_transfers");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("transfer_id", $id);
		}
		// $res = $this->db->get("ms_finance_account_transfers");
		$res = $this->db->get("ms_finance_account_transfers");


		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
	}

	public function get_by_number_doc($id = false)
	{
		if ($id) {
			$this->db->where("trans_number", $id);
		}

		$res = $this->db->where('status !=', 'draft')->get("ms_finance_account_transfers");

		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
	}

	public function get_by_account($id = false)
	{
		if ($id) {
			$this->db->where("account_id", $id);
		}
		// $res = $this->db->get("ms_finance_account_transfers");
		$res = $this->db->get("ms_finance_account_transfers");

		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_last_trans_number()
	{
		return $this->db->select('trans_number')
			->from('ms_finance_account_transfers')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function get_last_transfer()
	{
		return $this->db->select('*')
			->from('ms_finance_account_transfers')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function init_trans()
	{
		$last = $this->get_last_transfer();

		if (is_null($last) or !in_array(null, [$last->account_id, $last->terget_account_id], true)) {
			$trans_number = $this->trans_number();
			$this->db->insert('ms_finance_account_transfers', ['trans_number' => $trans_number]);
			$last_id = $this->db->insert_id();

			return $this->get($last_id);
		} else {
			return $last;
		}
	}

	// public function get_all_trans_by_trans_number($id)
	// {
	// 	// $transfers = $this->db->select('SUM(transfers.amount) as total_amount')
	// 	// 	->from('ms_finance_account_transfers transfers')
	// 	// 	->where('transfers.account_id', $id)
	// 	// 	->get()->row();

	// 	// $receives = $this->db->select('SUM(receives_trans.amount) as total_amount')
	// 	// 	->from('ms_finance_account_receives receives')
	// 	// 	->join('ms_finance_account_receive_trans receives_trans', 'receives.receive_id = receives_trans.receive_id', 'left')
	// 	// 	->get()->row();

	// 	// $spends = $this->db->select('SUM(spend_trans.amount) as total_amount')
	// 	// 	->from('ms_finance_account_spends spends')
	// 	// 	->join('ms_finance_account_spend_trans spend_trans', 'spends.spend_id = spend_trans.spend_id', 'left')
	// 	// 	->get()->row();

	// 	// $sum = array_sum([
	// 	// 	$transfers->total_amount,
	// 	// 	$receives->total_amount,
	// 	// 	$spends->total_amount
	// 	// ]);

	// 	return $this->db->where('account_id', $id)
	// 		->get('ms_finance_account_transactions');
	// 	// return $sum;	
	// }
	public function get_all_trans($id)
	{
		return $this->db->where('account_id', $id)
			->get('ms_finance_account_transactions');
	}

	public function update($id, $data)
	{
		$this->db->where('transfer_id', $id);
		return $this->db->update('ms_finance_account_transfers', $data);
	}

	public function update_with_files($id, $data, array $file_data = null)
	{
		$this->db->trans_start();
		$this->db->update('ms_finance_account_transfers', $data, ['transfer_id' => $id]);

		if (!is_null($file_data)) {
			if (count($file_data) > 0) {
				$this->db->insert_batch('ms_files', $file_data);
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function get_tagihan($id)
	{
		$record = $this->db->where('transfer_id', $id)->get('ms_finance_account_transfers')->row();
		// dd($record);
		$tagihan_dibayar = $this->db->select('SUM(amount) as total_amount')->where('type', 'debit')->where('join_id', $id)->get('ms_finance_account_transactions')->row();

		// var_dump($tagihan_dibayar);
		return $record->amount - $tagihan_dibayar->total_amount;
	}

	public function get_trans_payment($id)
	{
		$record = $this->db->where('transfer_id', $id)->get('ms_finance_account_transfers')->row();

		$get_tagihan_dibayar = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name'])
			->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->where('ms_finance_account_transactions.account_trans_cat_id', 1) // 1 = transfer
			->where('ms_finance_account_transactions.type', 'credit')
			->where('ms_finance_account_transactions.join_id', $id)->get()->result();

		$tagihan_dibayar = 0;
		foreach ($get_tagihan_dibayar as $val) {
			$tagihan_dibayar += $val->amount;
		}

		// dd($tagihan_dibayar);
		$data = new stdClass;
		$data->sisa_tagihan = $record->amount - $tagihan_dibayar;
		$data->jumlah_tagihan = $record->amount;
		$data->jumlah_dibayar = $tagihan_dibayar;
		$data->log_payments = $get_tagihan_dibayar;

		return $data;
	}
}
