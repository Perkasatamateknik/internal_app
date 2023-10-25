<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accounts_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_accounts");
	}

	public function insert($data)
	{
		return $this->db->insert("ms_finance_accounts", $data);
	}

	public function update($data)
	{
		return $this->db->update("ms_finance_accounts", $data, array('account_id' => $data['account_id']));
	}
}
