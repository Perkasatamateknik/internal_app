<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Receivables_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function trans_number()
	{
		$query = $this->get_last_data();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->trans_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("RN-%05d", $nextNumericPart);
	}
	// get all receivables
	public function all()
	{
		return $this->db->order_by('trans_number', 'asc')->where('status !=', "draft")->get("ms_receivables");
	}

	public function get($id)
	{
		$this->db->where('receivable_id', $id);
		return $this->db->get("ms_receivables");
	}

	public function get_last_data()
	{
		return $this->db->select('*')
			->from('ms_receivables')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function init_trans()
	{
		$last = $this->get_last_data();

		if (is_null($last) or !in_array(null, [$last->contact_id], true)) {
			$trans_number = $this->trans_number();
			$this->db->insert('ms_receivables', [
				'trans_number' => $trans_number
			]);
			$last_id = $this->db->insert_id();

			return $this->get($last_id)->row();
		} else {
			return $last;
		}
	}

	public function update_with_items_and_files($id, $data, $trans, array $items = null, array $files = null)
	{
		$this->db->trans_start();
		$this->db->update('ms_receivables', $data, ['trans_number' => $id]);
		$this->db->insert_batch('ms_finance_account_transactions', $trans);

		if (!is_null($items)) {
			if (count($items) > 0) {
				$this->db->insert_batch('ms_receivable_trans', $items);
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

	public function get_by_number_doc($id)
	{
		$this->db->where("trans_number", $id);

		$res = $this->db->select(['ms_contacts.contact_name', 'ms_receivables.*'])
			->from('ms_receivables')->join('ms_contacts', 'ms_receivables.contact_id=ms_contacts.contact_id')->get();

		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
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
			// ->where_not_in('ms_finance_accounts.account_id', [34])
			->where('ms_finance_accounts.category_id', 1)
			->where('ms_finance_account_transactions.account_trans_cat_id', $account_trans_cat_id)->where('ms_finance_account_transactions.join_id', $join_id)->where('ms_finance_account_transactions.type', 'credit')->get()->result();

		$data = new stdClass;
		$data->sisa_tagihan = $record_tagihan->amount - $records_pembayaran;
		$data->jumlah_tagihan = $record_tagihan->amount;
		$data->jumlah_dibayar = $records_pembayaran;
		$data->log_payments = $log;

		return $data;
	}

	public function get_sisa_tagihan($join_id)
	{
		// cari data tagihan di akun 34 trade payable
		$record_tagihan = $this->db->where('account_id', 34)->where('account_trans_cat_id', 4)->where('join_id', $join_id)->where('type', 'credit')->get('ms_finance_account_transactions')->row();
		$records_pembayaran = $this->db->select_sum('amount')->where('account_id', 34)->where('account_trans_cat_id', 4)->where('join_id', $join_id)->where('type', 'debit')->get('ms_finance_account_transactions')->row()->amount;

		$records_pembayaran ?? 0;
		return $record_tagihan->amount - $records_pembayaran;
	}

	public function update_by_trans_number($id, $data)
	{
		$this->db->where('trans_number', $id);
		return $this->db->update('ms_receivables', $data);
	}

	public function delete($id, $del_file = false)
	{
		// delete items
		$this->db->trans_start();
		$this->db->where('trans_number', $id)->delete('ms_receivables');
		$this->db->where('trans_number', $id)->delete('ms_receivable_trans');
		$this->db->where('join_id', $id)->delete('ms_finance_account_transactions');

		if ($del_file) {

			// get data
			$files = $this->db->where('access_id', $id)->get('ms_files')->result();

			foreach ($files as $file) {
				// check file in dir
				if (file_exists("./uploads/receivable/" . $file->file_name)) {
					// Delete the file
					unlink("./uploads/receivable/" . $file->file_name);
				}
			}

			$this->db->where('access_id', $id)->delete('ms_files');
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function get_list_data($id)
	{
		$record = $this->db->order_by('trans_number', 'asc')->where('status !=', "draft")->where('contact_id', $id)->get("ms_receivables")->result();

		foreach ($record as $i => $r) {
			$record[$i]->amount_total = $this->get_total_amount($r->trans_number);
		}

		return $record;
	}

	public function get_total_amount($trans_number)
	{
		$res = $this->db->select('sum(amount) as amount_total')->where('trans_number', $trans_number)->get('ms_receivable_trans')->row()->amount_total;

		if (!is_null($res)) {
			return $res;
		} else {
			return 0;
		}
	}

	public function get_items_by_trans_number($id)
	{
		$this->db->where("trans_number", $id);
		$res = $this->db->select(['ms_receivable_trans.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name'])
			->from('ms_receivable_trans')
			->join('ms_finance_accounts', 'ms_receivable_trans.account_id=ms_finance_accounts.account_id', 'INNER')
			->get();

		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function counts($id)
	{
		$records = $this->db->where('contact_id', $id)->get('ms_receivables')->result();
		$count = count($records);

		$std = new stdClass();
		$std->count = $count;
		$std->amount_bill = 0;

		// get total semua tagihan receivables
		// ! belum dikurangi duit yang di bayar
		foreach ($records as $r) {
			$std->amount_bill += $this->db->select_sum('amount')->from('ms_receivable_trans')->where('trans_number', $r->trans_number)->get()->row()->amount;
		}


		// Late //
		$records_late = $this->db->where('contact_id', $id)->where('due_date <', date('Y-m-d'))->get('ms_receivables')->result();
		// dd($records_late);
		$std->count_late = count($records_late);
		$std->amount_bill_late = 0;

		// get total semua tagihan receivables
		// ! belum dikurangi duit yang di bayar
		foreach ($records_late as $r) {
			$std->amount_bill_late += $this->db->select_sum('amount')->from('ms_receivable_trans')->where('trans_number', $r->trans_number)->get()->row()->amount;
		}

		return $std;
	}
}
