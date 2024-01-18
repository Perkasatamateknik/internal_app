<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_receive_items_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function all()
	{
		return $this->db->get("ms_finance_account_receive_trans");
	}

	public function get($id = false)
	{
		if ($id) {
			$this->db->where("receive_id", $id);
		}

		$res = $this->db->get("ms_finance_account_receive_trans");


		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_total_amount($id)
	{
		return $this->db->select_sum('amount')
			->where('receive_id', $id)
			->get('ms_finance_account_receive_trans')->row()->amount;
	}

	public function get_account_from_receive($id)
	{

		// $item = $this->db->where('receive_id', $id)->get()->result();

		// join tabel ms_finance_account_receive_trans and ms_finnace_accounts
		return $this->db->select(["ms_finance_account_receive_trans.*", "ms_finance_accounts.account_name", "ms_finance_accounts.account_code"])->from('ms_finance_account_receive_trans')
			->join('ms_finance_accounts', 'ms_finance_account_receive_trans.account_id=ms_finance_accounts.account_id', 'LEFT')
			->where('receive_id', $id)->get()->result();

		// dd($result);
		// foreach ($item as $key => $value) {
		// 	$account = $this->db->where('account_id', $value->account_id)->get()->row();
		// 	$item[$key]->account_data .= "<b>$account->account_name</b>" . "  " . $account->account_code . ",";
		// }
	}
}
