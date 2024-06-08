<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_receive_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function trans_number()
	{
		$query = $this->get_last_receive();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->trans_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("BR-%05d", $nextNumericPart);
	}

	public function all()
	{
		return $this->db->where('status !=', 'draft')->get("ms_finance_account_receives");
	}

	public function draft()
	{
		return $this->db->where('status =', 'draft')->get("ms_finance_account_receives");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("receive_id", $id);
		}

		$res = $this->db->get("ms_finance_account_receives");


		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
	}

	public function get_by_id($id = false)
	{
		if ($id) {
			$this->db->where("receive_id", $id);
		}

		$res = $this->db->get("ms_finance_account_receives");


		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_by_number_doc($id = false)
	{
		$this->db->select(['ms_finance_account_receives.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'ms_finance_accounts.account_id'])
			->from('ms_finance_account_receives')
			->join('ms_finance_accounts', 'ms_finance_account_receives.receive_account_id=ms_finance_accounts.account_id');

		if ($id) {
			$this->db->where("ms_finance_account_receives.trans_number", $id);
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

		$res = $this->db->get("ms_finance_account_receives");

		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_last_receive()
	{
		return $this->db->select('*')
			->from('ms_finance_account_receives')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function init_trans()
	{
		$last = $this->get_last_receive();
		if (is_null($last) or !in_array(null, [$last->contact_id], true)) {
			$trans_number = $this->trans_number();
			$this->db->insert('ms_finance_account_receives', [
				'trans_number' => $trans_number
			]);
			$last_id = $this->db->insert_id();

			return $this->get($last_id);
		} else {
			return $last;
		}
	}

	public function get_all_trans($id)
	{
		return $this->db->where('account_id', $id)
			->get('ms_finance_account_receives');
	}

	public function update($id, $data)
	{
		$this->db->where('receive_id', $id);
		return $this->db->update('ms_finance_account_receives', $data);
	}

	public function update_with_items_and_files($id, $data, $trans, array $items = null, array $files = null)
	{
		$this->db->trans_start();
		$this->db->update('ms_finance_account_receives', $data, ['receive_id' => $id]);
		$this->db->insert_batch('ms_finance_account_transactions', $trans);

		if (!is_null($items)) {
			if (count($items) > 0) {
				$this->db->insert_batch('ms_finance_account_receive_trans', $items);
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

	public function get_trans_payment($id)
	{
		$record = $this->db->select('sum(tax_rate + amount) as total_amount')->where('receive_id', $id)->get('ms_finance_account_receive_trans')->row();

		// dd($record);
		$get_tagihan_dibayar = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')
			->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id')
			->where('ms_finance_account_transactions.account_trans_cat_id', 3) // 3 = receive
			->where('ms_finance_account_transactions.type', 'credit')
			->where('ms_finance_account_transactions.join_id', $id)->get()->result();

		// dd($get_tagihan_dibayar);
		$tagihan_dibayar = 0;
		foreach ($get_tagihan_dibayar as $val) {
			$tagihan_dibayar += $val->amount;
		}

		$data = new stdClass;
		$data->sisa_tagihan = $record->total_amount - $tagihan_dibayar;
		$data->jumlah_tagihan = $record->total_amount;
		$data->jumlah_dibayar = $tagihan_dibayar;
		$data->log_payments = $get_tagihan_dibayar;

		// dd($data);
		return $data;
	}

	public function get_list_tagihan($id)
	{
		$receive_trans = $this->db->select(['receive_trans_id', 'account_id', 'tax_rate', 'amount'])->where('receive_id', $id)->get('ms_finance_account_receive_trans')->result();

		// dd($receive_trans);
		$data = [];

		foreach ($receive_trans as $key => $r) {
			// check apakah sudah di bayar di tabel transaksi
			$paid_trans = $this->db->select(['sum(amount) as amount'])->from('ms_finance_account_transactions')
				// ->where('account_trans_cat_id', 3)->where('join_id', $id) // 3 = receive // aslinya ga kepake, soalnya udh ke filter sama ref_trans_id
				->where('ref_trans_id', $r->receive_trans_id)->where('type', 'debit')
				->get()->row();

			$amount_bill = $r->tax_rate + $r->amount;
			$paid = $paid_trans->amount;

			if ($paid < $amount_bill) {
				$res = new stdClass;
				$res->receive_trans_id = $r->receive_trans_id;
				$res->account_id = $r->account_id;
				$res->bill_remaining = $amount_bill - $paid;

				$data[] = $res;
			} else {
				continue;
			}
		}

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

	public function delete($id, $del_file = false)
	{
		// delete items
		$this->db->trans_start();
		$this->db->where('trans_number', $id)->delete('ms_finance_account_receives');
		$this->db->where('trans_number', $id)->delete('ms_finance_account_receive_trans');
		// $this->db->where('join_id', $id)->delete('ms_finance_account_transactions');

		if ($del_file) {

			// get data
			$files = $this->db->where('access_id', $id)->get('ms_files')->result();

			foreach ($files as $file) {
				// check file in dir
				if (file_exists("./uploads/finance/account_receive/" . $file->file_name)) {
					// Delete the file
					unlink("./uploads/finance/account_receive/" . $file->file_name);
				}
			}

			$this->db->where('access_id', $id)->delete('ms_files');
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
