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
		return $this->db->get("ms_finance_account_transfers");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("id_trans", $id);
		}
		// $res = $this->db->get("ms_finance_account_transfers");
		$res = $this->db->get("ms_finance_account_transfers");


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
		// $res = $this->db->get("ms_finance_account_transfers");
		$res = $this->db->get("ms_finance_account_transfers");

		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_last_trans_number()
	{
		return $this->db->select('trans_number')
			->from('ms_finance_account_transfers')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function init_trans($data)
	{
		// $this->db->trans_start();
		$this->db->insert('ms_finance_account_transfers', $data);
		return $this->db->insert_id();
		// $this->db->insert_batch('ms_items_purchase_requisition', $items);
		// $this->db->trans_complete();
		// return $this->db->trans_status();
	}

	public function get_all_trans($id)
	{
		$transfers = $this->db->select('SUM(transfers.amount) as total_amount')
			->from('ms_finance_account_transfers transfers')
			->where('transfers.account_id', $id)
			->get()->row();

		$receives = $this->db->select('SUM(receives_trans.amount) as total_amount')
			->from('ms_finance_account_receives receives')
			->join('ms_finance_account_receive_trans receives_trans', 'receives.receive_id = receives_trans.receive_id', 'left')
			->get()->row();

		$spends = $this->db->select('SUM(spend_trans.amount) as total_amount')
			->from('ms_finance_account_spends spends')
			->join('ms_finance_account_spend_trans spend_trans', 'spends.spend_id = spend_trans.spend_id', 'left')
			->get()->row();

		$sum = array_sum([
			$transfers->total_amount,
			$receives->total_amount,
			$spends->total_amount
		]);

		return $sum;
	}
}
