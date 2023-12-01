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

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("spend_id", $id);
		}

		$res = $this->db->get("ms_finance_account_spend_trans");


		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_total_amount($id)
	{
		return $this->db->select_sum('amount')
			->where('spend_id', $id)
			->get('ms_finance_account_spend_trans')->row()->amount;
	}
}
