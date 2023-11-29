<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_trans_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_account_trans");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("account_trans_id", $id);
		}
		return $this->db->get("ms_finance_account_transactions");
	}
}
