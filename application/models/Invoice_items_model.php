<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Invoice_items_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_invoice_trans");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("invoice_id", $id);
		}

		$res = $this->db->get("ms_finance_invoice_trans");


		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}
}
