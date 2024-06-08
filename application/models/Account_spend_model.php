<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_spend_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function trans_number()
	{
		$query = $this->get_last_spend();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->trans_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("BS-%05d", $nextNumericPart);
	}

	public function all()
	{
		return $this->db->where('status !=', 'draft')->get("ms_finance_account_spends");
	}
	public function draft()
	{
		return $this->db->where('status =', 'draft')->get("ms_finance_account_spends");
	}

	public function get($trans_number)
	{
		$this->db->where("trans_number", $trans_number);
		$res = $this->db->get("ms_finance_account_spends");

		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
	}

	public function get_by_id($id = false)
	{
		if ($id) {
			$this->db->where("spend_id", $id);
		}

		$res = $this->db->get("ms_finance_account_spends");


		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_by_number_doc($id = false)
	{
		$this->db->select(['ms_finance_account_spends.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'ms_finance_accounts.account_id'])
			->from('ms_finance_account_spends')
			->join('ms_finance_accounts', 'ms_finance_account_spends.account_id=ms_finance_accounts.account_id');

		if ($id) {
			$this->db->where("trans_number", $id);
		}

		$res = $this->db->get();

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

		$res = $this->db->get("ms_finance_account_spends");

		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_last_spend()
	{
		return $this->db->select('*')
			->from('ms_finance_account_spends')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function init_trans()
	{
		$last = $this->get_last_spend();

		if (is_null($last) or !in_array(null, [$last->beneficiary], true)) {
			$trans_number = $this->trans_number();
			$this->db->insert('ms_finance_account_spends', [
				'trans_number' => $trans_number
			]);

			return $this->get($trans_number);
		} else {
			return $last;
		}
	}

	public function get_all_trans($id)
	{
		return $this->db->where('account_id', $id)
			->get('ms_finance_account_spends');
	}

	public function update($id, $data)
	{
		$this->db->where('spend_id', $id);
		return $this->db->update('ms_finance_account_spends', $data);
	}

	public function update_by_trans_number($id, $data)
	{
		$this->db->where('trans_number', $id);
		return $this->db->update('ms_finance_account_spends', $data);
	}

	public function update_with_items_and_files($id, $data, $trans, array $items = null, array $files = null)
	{
		$this->db->trans_start();
		$this->db->update('ms_finance_account_spends', $data, ['spend_id' => $id]);
		$this->db->insert_batch('ms_finance_account_transactions', $trans);

		if (!is_null($items)) {
			if (count($items) > 0) {
				$this->db->insert_batch('ms_finance_account_spend_trans', $items);
			}
		}

		if (!is_null($files)) {
			if (count($files) > 0) {
				$this->db->insert_batch('ms_files', $files);
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	// public function get_trans_payment($id)
	// {
	// 	// $record = $this->db->where('spend_id', $id)->get('ms_finance_account_spends')->row();
	// 	$record = $this->db->select('sum(tax_rate + amount) as total_amount')->where('spend_id', $id)->get('ms_finance_account_spend_trans')->row();
	// 	// dd($items);
	// 	$get_tagihan_dibayar = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'xin_employees.first_name', 'xin_employees.last_name'])
	// 		->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
	// 		->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id')
	// 		->where('ms_finance_account_transactions.account_trans_cat_id', 2) // 2 = spend
	// 		->where('ms_finance_account_transactions.type', 'debit')
	// 		->where('ms_finance_account_transactions.join_id', $id)->get()->result();

	// 	$tagihan_dibayar = 0;
	// 	foreach ($get_tagihan_dibayar as $val) {
	// 		$tagihan_dibayar += $val->amount;
	// 	}

	// 	$data = new stdClass;
	// 	$data->sisa_tagihan = $record->total_amount - $tagihan_dibayar;
	// 	$data->jumlah_tagihan = $record->total_amount;
	// 	$data->jumlah_dibayar = $tagihan_dibayar;
	// 	$data->log_payments = $get_tagihan_dibayar;

	// 	return $data;
	// }

	// public function get_list_tagihan($id)
	// {
	// 	$spend_trans = $this->db->select(['spend_trans_id', 'account_id', 'tax_rate', 'tax_withholding', 'amount'])->where('trans_number', $id)->get('ms_finance_account_spend_trans')->result();

	// 	$data = [];

	// 	foreach ($spend_trans as $key => $r) {
	// 		// check apakah sudah di bayar di tabel transaksi
	// 		$paid_trans = $this->db->select(['sum(amount) as amount'])->from('ms_finance_account_transactions')
	// 			->where('account_trans_cat_id', 2)
	// 			->where('join_id', $id) 
	// 			->where('type', 'debit')
	// 			->get()->row();

	// 		$amount_bill = $r->tax_rate + $r->amount;
	// 		$paid = $paid_trans->amount;

	// 		if ($paid < $amount_bill) {
	// 			$res = new stdClass;
	// 			$res->spend_trans_id = $r->spend_trans_id;
	// 			$res->account_id = $r->account_id;
	// 			$res->bill_remaining = $amount_bill - $paid;

	// 			$data[] = $res;
	// 		} else {
	// 			continue;
	// 		}
	// 	}

	// 	return $data;
	// }

	public function get_tagihan_terkecil($id)
	{
		$this->db->select('*')->from('ms_finance_account_spend_trans')->where('sudah_dibayar < jumlah_tagihan')->order_by('jumlah_tagihan', 'asc')->limit(1);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_spend_item($trans_number)
	{
		$this->db->where('trans_number', $trans_number)->get();
		$query = $this->db->get();
		return $query->row();
	}

	public function update_tagihan($id_tagihan, $jumlah_pembayaran)
	{
		$this->db->where('id_tagihan', $id_tagihan)->update('tagihan', array('sudah_dibayar' => $jumlah_pembayaran));
	}

	public function get_payment($account_trans_cat_id, $join_id)
	{
		// cari data tagihan di akun 34 trade payable
		$record_tagihan = $this->db->where('account_id', 34)->where('account_trans_cat_id', $account_trans_cat_id)->where('join_id', $join_id)->where('type', 'credit')->get('ms_finance_account_transactions')->row();
		$records_pembayaran = $this->db->select_sum('amount')->where('account_id', 34)->where('account_trans_cat_id', $account_trans_cat_id)->where('join_id', $join_id)->where('type', 'debit')->get('ms_finance_account_transactions')->row()->amount;

		// log pembayaran dari akun
		$log = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'ms_finance_accounts.category_id', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id', 'inner')
			->where('ms_finance_accounts.category_id', 1)
			->where('ms_finance_account_transactions.account_trans_cat_id', $account_trans_cat_id)->where('ms_finance_account_transactions.join_id', $join_id)->where('ms_finance_account_transactions.type', 'credit')->get()->result();

		$data = new stdClass;
		$data->sisa_tagihan = $record_tagihan->amount - $records_pembayaran;
		$data->jumlah_tagihan = $record_tagihan->amount;
		$data->jumlah_dibayar = $records_pembayaran;
		$data->log_payments = $log;

		return $data;
	}

	// public function get_item_bill($account_trans_cat_id, $trans_number)
	// {
	// 	$items = $this->db->where('trans_number', $trans_number)->get('ms_finance_account_spend_trans')->result();

	// 	$data = [];
	// 	foreach ($items as $r) {
	// 		$paided = $this->db->select_sum('amount')
	// 			->where('account_id', $r->account_id)
	// 			->where('account_trans_cat_id', $account_trans_cat_id)
	// 			->where('join_id', $trans_number)
	// 			->where('ms_finance_account_transactions.type', 'debit')
	// 			->get('ms_finance_account_transactions')->row()->amount;

	// 		$tagihan = new stdClass();
	// 		$tagihan->account_id = $r->account_id;
	// 		$tagihan->trans_number = $r->trans_number;

	// 		if ($r->tax_withholding == 1) {
	// 			$tagihan->total_tagihan_akun = $r->amount - $r->tax_rate;
	// 			$tagihan->sisa_tagihan_akun = $r->amount - $r->tax_rate + $paided;
	// 		} else {
	// 			$tagihan->total_tagihan_akun = $r->amount + $r->tax_rate;
	// 			$tagihan->sisa_tagihan_akun = $r->amount + $r->tax_rate + $paided;
	// 		}

	// 		$data[] = $tagihan;
	// 	}

	// 	return $data;
	// }

	public function delete($id, $del_file = false)
	{
		// delete items
		$this->db->trans_start();
		$this->db->where('trans_number', $id)->delete('ms_finance_account_spends');
		$this->db->where('trans_number', $id)->delete('ms_finance_account_spend_trans');
		$this->db->where('join_id', $id)->delete('ms_finance_account_transactions');

		if ($del_file) {

			// get data
			$files = $this->db->where('access_id', $id)->get('ms_files')->result();

			foreach ($files as $file) {
				// check file in dir
				if (file_exists("./uploads/finance/account_spend/" . $file->file_name)) {
					// Delete the file
					unlink("./uploads/finance/account_spend/" . $file->file_name);
				}
			}

			$this->db->where('access_id', $id)->delete('ms_files');
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
