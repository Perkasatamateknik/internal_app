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

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("spend_id", $id);
		}

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
		if ($id) {
			$this->db->where("trans_number", $id);
		}

		$res = $this->db->get("ms_finance_account_spends");

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
				// 'account_id' => $id,
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
			->get('ms_finance_account_spends');
	}

	public function update($id, $data)
	{
		$this->db->where('spend_id', $id);
		return $this->db->update('ms_finance_account_spends', $data);
	}

	public function update_with_items_and_files($id, $data, array $items = null, array $files = null)
	{
		$this->db->trans_start();
		$this->db->update('ms_finance_account_spends', $data, ['spend_id' => $id]);

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

	public function get_trans_payment($id)
	{
		// $record = $this->db->where('spend_id', $id)->get('ms_finance_account_spends')->row();
		$record = $this->db->select('sum(tax_rate + amount) as total_amount')->where('spend_id', $id)->get('ms_finance_account_spend_trans')->row();
		// dd($items);
		$get_tagihan_dibayar = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id')
			->where('ms_finance_account_transactions.account_trans_cat_id', 2) // 2 = spend
			->where('ms_finance_account_transactions.type', 'debit')
			->where('ms_finance_account_transactions.join_id', $id)->get()->result();

		$tagihan_dibayar = 0;
		foreach ($get_tagihan_dibayar as $val) {
			$tagihan_dibayar += $val->amount;
		}

		$data = new stdClass;
		$data->sisa_tagihan = $record->total_amount - $tagihan_dibayar;
		$data->jumlah_tagihan = $record->total_amount;
		$data->jumlah_dibayar = $tagihan_dibayar;
		$data->log_payments = $get_tagihan_dibayar;

		return $data;
	}

	public function get_list_tagihan($id)
	{
		$spend_trans = $this->db->select(['spend_trans_id', 'account_id', 'tax_rate', 'amount'])->where('spend_id', $id)->get('ms_finance_account_spend_trans')->result();

		$data = [];

		foreach ($spend_trans as $key => $r) {
			// check apakah sudah di bayar di tabel transaksi
			$paid_trans = $this->db->select(['sum(amount) as amount'])->from('ms_finance_account_transactions')
				->where('account_trans_cat_id', 2)->where('join_id', $id) // 2 = spend // aslinya ga kepake, soalnya udh ke filter sama ref_trans_id
				->where('ref_trans_id', $r->spend_trans_id)->where('type', 'debit')
				->get()->row();

			$amount_bill = $r->tax_rate + $r->amount;
			$paid = $paid_trans->amount;

			if ($paid < $amount_bill) {
				$res = new stdClass;
				$res->spend_trans_id = $r->spend_trans_id;
				$res->account_id = $r->account_id;
				$res->bill_remaining = $amount_bill - $paid;

				$data[] = $res;
			} else {
				continue;
			}

			// $spend_trans[$key]->bill_paid = $paid_trans->amount ?? 0;
			// $spend_trans[$key]->bill_remaining = $paid_trans->amount ?? 0;
		}

		return $data;
	}

	public function get_tagihan_terkecil($id)
	{
		$this->db->select('*')->from('ms_finance_account_spend_trans')->where('sudah_dibayar < jumlah_tagihan')->order_by('jumlah_tagihan', 'asc')->limit(1);
		$query = $this->db->get();
		return $query->row();
	}

	public function update_tagihan($id_tagihan, $jumlah_pembayaran)
	{
		$this->db->where('id_tagihan', $id_tagihan)->update('tagihan', array('sudah_dibayar' => $jumlah_pembayaran));
	}
}
