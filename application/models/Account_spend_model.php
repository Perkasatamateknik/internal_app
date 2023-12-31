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
		return $this->db->get("ms_finance_account_spends");
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

	public function init_trans($id)
	{
		$last = $this->get_last_spend();

		if (is_null($last) or !in_array(null, [$last->beneficiary], true)) {
			$trans_number = $this->trans_number();
			$this->db->insert('ms_finance_account_spends', [
				'account_id' => $id,
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
}
