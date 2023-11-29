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

	public function get($id)
	{
		$this->db->where('account_id', $id);
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

	public function gel_all_account($query = false)
	{
		if ($query) {
			$this->db->like('account_name', $query);
			$this->db->or_like('account_code', $query);
		}

		// limit record to 10
		$this->db->limit(10);
		// order by account_name
		$this->db->order_by('account_name', 'ASC');
		return $this->db->get("ms_finance_accounts");
	}
}
