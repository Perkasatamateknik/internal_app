<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_spend_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_account_spends");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("spend_id", $id);
		}

		$res = $this->db->get("ms_finance_account_spends");


		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
	}

	public function get_by_number_doc($id = false)
	{
		if ($id) {
			$this->db->where("trans_number", $id);
		}

		$res = $this->db->get("ms_finance_account_spends");

		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
	}

	public function get_by_account($id = false)
	{
		if ($id) {
			$this->db->where("account_id", $id);
		}

		$res = $this->db->get("ms_finance_account_spends");

		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_last_trans_number()
	{
		return $this->db->select('trans_number')
			->from('ms_finance_account_spends')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function init_trans($data)
	{
		$this->db->insert('ms_finance_account_spends', $data);
		return $this->db->insert_id();
	}

	public function get_all_trans($id)
	{
		return $this->db->where('account_id', $id)
			->get('ms_finance_account_transactions');
	}

	public function update($id, $data)
	{
		$this->db->where('spend_id', $id);
		return $this->db->update('ms_finance_account_transfers', $data);
	}
}
