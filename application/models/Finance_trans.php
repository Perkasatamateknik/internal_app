<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Finance_trans extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// bank and cash
	public function get_trans($id)
	{
		$this->db->where("account_id", $id);
		return $this->db->get("ms_finance_transactions");
	}
}
