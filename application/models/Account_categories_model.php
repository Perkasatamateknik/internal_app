<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_categories_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_account_categories");
	}

	public function get($id = false)
	{

		if ($id) {
			$this->db->where("category_id", $id);
		}
		$res = $this->db->get("ms_finance_account_categories");

		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}

		// return $this->db->result();
	}
}
