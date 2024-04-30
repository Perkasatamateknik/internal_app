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
		$this->db->where("trans_number", $id);
		$res = $this->db->get("ms_finance_account_receive_trans");


		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return null;
		}
	}

	public function get_total_amount($trans_number)
	{
		$res = $this->db->where('trans_number', $trans_number)
			->get('ms_finance_account_receive_trans')->result();

		$amount = 0;
		foreach ($res as $r) {
			if ($r->tax_withholding == 1) {
				$amount += $r->amount - $r->tax_rate;
			} else {
				$amount += $r->amount + $r->tax_rate;
			}
		}

		return $amount;
	}

	public function get_account_from_receive($trans_number)
	{

		// $item = $this->db->where('receive_trans_number', $id)->get()->result();

		// join tabel ms_finance_account_receive_trans and ms_finnace_accounts
		return $this->db->select(["ms_finance_account_receive_trans.*", "ms_finance_accounts.account_name", "ms_finance_accounts.account_code"])->from('ms_finance_account_receive_trans')
			->join('ms_finance_accounts', 'ms_finance_account_receive_trans.account_id=ms_finance_accounts.account_id', 'LEFT')
			->where('trans_number', $trans_number)->get()->result();

		// dd($result);
		// foreach ($item as $key => $value) {
		// 	$account = $this->db->where('account_id', $value->account_id)->get()->row();
		// 	$item[$key]->account_data .= "<b>$account->account_name</b>" . "  " . $account->account_code . ",";
		// }
	}
}
