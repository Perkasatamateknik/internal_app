<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Expense_ms_model extends CI_Model
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
	public function all()
	{
		return $this->db->get("ms_finance_expenses");
	}

	public function get($id)
	{
		$this->db->where('expense_id', $id);
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

	public function update_with_items_and_files($id, $data, array $items = null, array $files = null)
	{
		$this->db->trans_start();
		$this->db->update('ms_finance_expenses', $data, ['expense_id' => $id]);

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
		$this->db->where("trans_number", $id);
		$res = $this->db->get("ms_finance_expenses");

		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
	}
}
