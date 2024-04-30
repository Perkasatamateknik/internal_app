<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Budgeting_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function trans_number()
	{
		$query = $this->get_last_spend();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->trans_number, 4));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("EXP-%05d", $nextNumericPart);
	}
	// get all expenses
	public function all()
	{
		return $this->db->order_by('year', 'desc')->get("ms_finance_budgeting");
	}

	public function get($id)
	{
		$this->db->where('budget_id', $id);
		return $this->db->get("ms_finance_budgeting")->row();
	}

	public function get_items_budget($id) //id budgeting
	{
		$this->db->where('budget_id', $id);
		$budget_dep = $this->db->get("ms_finance_budgeting_department")->result();
		foreach ($budget_dep as $key => $r) {

			$dep = $this->db->where('department_id', $r->department_id)->get('xin_departments')->row();
			if (!is_null($dep)) {
				$budget_dep[$key]->department_name = $dep->department_name;
			} else {
				$budget_dep[$key]->department_name = "--";
			}

			$budget_data = $this->db->where('budget_dep_id', $r->budget_dep_id)->get('ms_finance_budgeting_data')->result();
			foreach ($budget_data as $i => $bd) {
				$account = $this->db->where('account_id', $bd->account_id)->get('ms_finance_accounts')->row();
				if (!is_null($account)) {
					$budget_data[$i]->account_name = "<b>$account->account_name</b>" . "  " . $account->account_code;
				} else {
					$budget_data[$i]->account_name = "--";
				}
			}

			$budget_dep[$key]->budget_data = $budget_data;
		}

		return $budget_dep;
	}

	public function add_budget($data)
	{
		$this->db->trans_start();
		$this->db->update("ms_finance_budgeting", ['status' => 0, 'updated_at' => date("y-m-d H:i:s")]);
		$this->db->insert("ms_finance_budgeting", $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function add_budget_data($department, $data)
	{
		$this->db->trans_start();
		$this->db->insert("ms_finance_budgeting_department", $department);

		// Get the last insert ID
		$department_id = $this->db->insert_id();
		foreach ($data as $key => $val) {
			$data[$key]['budget_dep_id'] = $department_id;
		}

		$this->db->insert_batch("ms_finance_budgeting_data", $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}



	public function get_used_year()
	{
		$query = $this->db->select('year')
			->from('ms_finance_budgeting')
			->order_by('year', 'desc')
			->get();

		$year = [];
		foreach ($query->result() as $q) {
			$year[] = $q->year;
		}

		return $year;
	}
}
