<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Finance_model_v2 extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_last_expense_number()
	{
		$query = $this->db->query("SELECT * FROM ms_finance_expenses ORDER BY expense_id DESC LIMIT 1")->row();
		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->expense_number, 4));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("EXP-%07d", $nextNumericPart);
	}
}
