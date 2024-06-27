<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Expense_model extends CI_Model
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
			$numericPart = intval(substr($query->trans_number, 4));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("EXP-%05d", $nextNumericPart);
	}
	// get all expenses
	public function all($filter = false)
	{
		if ($filter) {
			$this->db->where('status', $filter);
		} else {
			$this->db->where('status !=', "draft");
		}
		return $this->db->order_by('trans_number', 'asc')->get("ms_finance_expenses");
	}

	public function get($id)
	{
		$this->db->where('expense_id', $id);
		return $this->db->get("ms_finance_expenses");
	}

	public function get_in_trans_number($id)
	{
		$this->db->where_in('trans_number', $id);
		return $this->db->get("ms_finance_expenses");
	}

	public function get_last_spend()
	{
		return $this->db->select('*')
			->from('ms_finance_expenses')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function init_trans()
	{
		$last = $this->get_last_spend();

		if (is_null($last) or !in_array(null, [$last->beneficiary], true)) {
			$trans_number = $this->trans_number();
			$this->db->insert('ms_finance_expenses', [
				'trans_number' => $trans_number
			]);
			$last_id = $this->db->insert_id();

			return $this->get($last_id)->row();
		} else {
			return $last;
		}
	}

	public function import_batch($insert)
	{

		// if (count($insert['data']) == 0 || count($insert['items']) == 0 || count($insert['trans'])) {
		// 	return false;
		// }

		$this->db->trans_start();
		$this->db->insert_batch('ms_finance_expenses', $insert['data']);
		$this->db->insert_batch('ms_finance_expense_trans', $insert['items']);
		$this->db->insert_batch('ms_finance_account_transactions', $insert['trans']);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}


	public function update_with_items_and_files($id, $data, $trans, array $items = null, array $files = null)
	{
		$this->db->trans_start();
		$this->db->update('ms_finance_expenses', $data, ['trans_number' => $id]);
		$this->db->insert_batch('ms_finance_account_transactions', $trans);

		if (!is_null($items)) {
			if (count($items) > 0) {
				$this->db->insert_batch('ms_finance_expense_trans', $items);
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
		$res = $this->db->select(['ms_finance_expenses.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'ms_finance_accounts.account_id'])
			->from('ms_finance_expenses')
			->join('ms_finance_accounts', 'ms_finance_expenses.account_id=ms_finance_accounts.account_id')
			->where("trans_number", $id)->get();

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
		return $this->db->update('ms_finance_expenses', $data);
	}

	public function delete($id, $del_file = false)
	{
		// delete items
		$this->db->trans_start();
		$this->db->where('trans_number', $id)->delete('ms_finance_expenses');
		$this->db->where('trans_number', $id)->delete('ms_finance_expense_trans');
		$this->db->where('join_id', $id)->delete('ms_finance_account_transactions');

		if ($del_file) {

			// get data
			$files = $this->db->where('access_id', $id)->get('ms_files')->result();

			foreach ($files as $file) {
				// check file in dir
				if (file_exists("./uploads/finance/expense/" . $file->file_name)) {
					// Delete the file
					unlink("./uploads/finance/expense/" . $file->file_name);
				}
			}

			$this->db->where('access_id', $id)->delete('ms_files');
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function bulk_paymnet($ids)
	{

		// $exp = $this->db->select('trans_number')->get('ms_finance_expenses')->result_array();
		// $ids = [];
		// foreach ($exp as $e) {
		// 	$ids[] = $e['trans_number'];
		// }

		$tagihan = [];
		$can_pay = [];
		$cant_pay = [];

		$amount = [
			'can_pay' => 0,
			'cant_pay' => 0
		];

		foreach ($this->get_in_trans_number($ids)->result() as $row) {
			$tagihan = $this->get_payment(4, $row->trans_number);

			$contact = $this->db->where('contact_id', $row->beneficiary)->get('ms_contacts')->row();
			if ($tagihan->sisa_tagihan > 0) {
				$can_pay[] = [
					'sisa_tagihan' => $tagihan->sisa_tagihan,
					'trans_number' => $row->trans_number,
					'account_id' => $row->account_id,
					'contact' => $contact->contact_name,
				];

				$amount['can_pay'] += $tagihan->sisa_tagihan;
			} else {
				$cant_pay[] = [
					'sisa_tagihan' => $tagihan->sisa_tagihan,
					'trans_number' => $row->trans_number,
					'account_id' => $row->account_id,
					'contact' => $contact->contact_name,
				];
				$amount['cant_pay'] += $tagihan->jumlah_tagihan;
			}
		}
		return [
			'cant_pay' => $cant_pay,
			'can_pay' => $can_pay,
			'amount' => $amount
		];
	}
}
