<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_spend_items_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_account_spend_trans");
	}

	public function get($trans_number)
	{
		$this->db->where("trans_number", $trans_number);
		$res = $this->db->get("ms_finance_account_spend_trans");

		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_by_trans_number($id)
	{
		$this->db->where("trans_number", $id);
		$res = $this->db->get("ms_finance_account_spend_trans");

		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_total_amount($trans_number)
	{
		$res = $this->db->where('trans_number', $trans_number)
			->get('ms_finance_account_spend_trans')->result();

		$amount = 0;
		foreach ($res as $r) {
			if ($r->tax_withholding == 1) {
				$amount += $r->amount - $r->tax_rate;
			} else {
				$amount += $r->amount + $r->tax_rate;
			}
		}

		return $amount;
	}
}
