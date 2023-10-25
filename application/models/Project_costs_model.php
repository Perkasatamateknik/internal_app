<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_costs_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_all()
	{
		return $this->db->get("ms_project_costs");
	}

	public function read_info($id)
	{

		$sql = 'SELECT * FROM ms_project_costs WHERE project_cost_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}


	// public function get_like_product($id)
	// {

	// 	$sql = "SELECT * FROM ms_products WHERE product_name LIKE %$id%";
	// 	$query = $this->db->query($sql);
	// 	return $query;
	// }

	// public function get_employee_travel($id)
	// {

	// 	$sql = 'SELECT * FROM xin_employee_travels WHERE employee_id = ?';
	// 	$binds = array($id);
	// 	$query = $this->db->query($sql, $binds);
	// 	return $query;
	// }
	// // get company travel
	// public function get_company_travel($company_id)
	// {

	// 	$sql = 'SELECT * FROM xin_employee_travels WHERE company_id = ?';
	// 	$binds = array($company_id);
	// 	$query = $this->db->query($sql, $binds);
	// 	return $query;
	// }

	// // get all travel arrangement types
	// public function travel_arrangement_types()
	// {
	// 	$query = $this->db->query("SELECT * from xin_travel_arrangement_type");
	// 	return $query->result();
	// }

	// Function to add record in table
	public function insert($data)
	{
		$add = $this->db->insert('ms_project_costs', $data);
		if ($add) {
			return $this->db->insert_id();
		} else {
			return false;
		}
	}

	// Function to Delete selected record from table
	public function delete_record($id)
	{
		$this->db->where('project_cost_id', $id);
		return $this->db->delete('ms_project_costs');
	}

	// Function to update record in table
	public function update_record($data, $id)
	{
		$this->db->where('project_cost_id', $id);
		if ($this->db->update('ms_project_costs', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function get_trans_last_month()
	{
		$sql = "SELECT SUM(amount) AS total_amount FROM ms_recently_products";
		$query = $this->db->query($sql);
		return $query->row()->total_amount;
	}


	public function get_trans_remaining_payment()
	{
		$sql = "SELECT SUM(prepayment) AS prepayment FROM ms_project_costs";
		$query = $this->db->query($sql);
		return $query->row()->prepayment;
	}

	public function get_trans_prepayment()
	{

		$sql = "SELECT SUM(prepayment) AS prepayment FROM ms_project_costs";
		$query = $this->db->query($sql);
		return $query->row()->prepayment;
	}
	// public function get_latest_month_trans_vendor()
	// {
	// 	$sql = "SELECT v.vendor_name, SUM(pc.amount) AS total FROM ms_project_costs pc INNER JOIN ms_vendors v ON pc.vendor_id = v.vendor_id WHERE MONTH(pc.invoice_date) = MONTH(CURRENT_DATE()) AND YEAR(pc.invoice_date) = YEAR(CURRENT_DATE()) GROUP BY v.vendor_name";
	// 	return $this->db->query($sql);
	// }
	public function get_latest_month_trans_vendor()
	{
		// $sql = "SELECT project_cost_id, vendor_id, sum(amount) FROM ms_project_costs mpc WHERE MONTH(invoice_date) = MONTH(CURDATE())";
		// // $sql = "SELECT v.vendor_name, SUM(pc.amount) AS total FROM ms_project_costs pc INNER JOIN ms_vendors v ON pc.vendor_id = v.vendor_id WHERE MONTH(pc.invoice_date) = MONTH(CURRENT_DATE()) AND YEAR(pc.invoice_date) = YEAR(CURRENT_DATE()) GROUP BY v.vendor_name";
		// return $this->db->query($sql);

		$this->db->select('vendor_id, SUM(amount) as amount');
		$this->db->from('ms_project_costs');
		$this->db->where('MONTH(invoice_date)', date('m'));
		$this->db->where('YEAR(invoice_date)', date('Y'));
		$this->db->group_by('vendor_id');
		return $this->db->group_by('vendor_id')->get();
	}

	public function get_latest_month_trans($type = 'product')
	{
		// $pc = $this->db->get('ms_project_costs')->result();
		// if(!is_null($pc)){

		// 	$data = [];

		// 	foreach ($pc as $r) {
		// 		$rp = $this->
		// 		$data[] = [
		// 			'title' => $
		// 		]
		// 	}
		// 	if ($type == 'product') {
		// 		$this->db->select('rp.product_name title, pc.invoice_date');
		// 	}
		// // }
		// $sql = "SELECT rp.product_name AS title, SUM(rp.qty) AS qty, SUM(rp.amount) AS total FROM ms_project_costs pc INNER JOIN ms_recently_products rp ON pc.project_cost_id = rp.project_cost_id GROUP BY rp.product_name";
		// dd($this->db->query($sql)->result());
		// // $this->db->from('ms_recently_products rp');
		// // $this->db->join('ms_project_costs pc', 'rp.project_cost_id = pc.project_cost_id');
		// // // $this->db->where_in('rp.project_cost', $id);
		// // // $this->db->group_by('rp.product_id');
		// // return $this->db->get()->result();

		$this->db->select('rp.product_name AS title, SUM(rp.qty) AS qty, SUM(rp.amount) AS total');
		$this->db->from('ms_project_costs pc');
		$this->db->join('ms_recently_products rp', 'pc.project_cost_id = rp.project_cost_id');
		$this->db->group_by('rp.product_name');
		$this->db->order_by('total', 'desc');
		$this->db->limit(5); // Mengatur batasan menjadi 10 rekaman
		// Mendapatkan tanggal awal dan akhir bulan ini
		$currentMonthStart = date('Y-m-01');
		$currentMonthEnd = date('Y-m-t');

		$this->db->where("pc.invoice_date >= '$currentMonthStart' AND pc.invoice_date <= '$currentMonthEnd'");
		return $this->db->get();
	}

	public function get_recently_category_name($id)
	{
		$this->db->select('pc.category_name, COUNT(pc.category_name) as total_count');
		$this->db->from('ms_recently_products rp');
		$this->db->join('ms_product_categories pc', 'rp.category_id = pc.category_id');
		$this->db->where_in('rp.category_id', $id);
		$this->db->group_by('pc.category_name');
		return $this->db->get()->result();
	}
	public function get_recently_sub_category_name($id)
	{
		$this->db->select('pc.sub_category_name, COUNT(pc.sub_category_name) as total_count');
		$this->db->from('ms_recently_products rp');
		// $this->db->join('ms_product_categories pc', 'rp.sub_category_id = pc.sub_category_id');
		$this->db->where_in('rp.sub_category_id', $id);
		$this->db->group_by('pc.sub_category_name');
		return $this->db->get()->result();
	}

	public function get_only_field($field, $table, $id = false, $field_where = false)
	{
		$this->db->select($field);
		$this->db->from($table);
		if ($id && $field_where) {
			$this->db->where($field_where, $id);
		}
		return $this->db->get()->result();
	}


	public function get_trans_vendor(){
		
	}
}
