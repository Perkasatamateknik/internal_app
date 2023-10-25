<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vendor_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function gel_all_vendor()
	{
		return $this->db->get("ms_vendors");
	}

	public function read_vendor_information($id)
	{

		$sql = 'SELECT * FROM ms_vendors WHERE vendor_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return null;
		}
	}

	public function find_vendor($query)
	{

		$this->db->like('vendor_name', $query);
		return $this->db->get('ms_vendors')->result();
	}

	// Function to add record in table
	public function add($data)
	{
		$this->db->insert('xin_employee_travels', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Function to Delete selected record from table
	public function delete_record($id)
	{
		$this->db->where('travel_id', $id);
		$this->db->delete('xin_employee_travels');
	}

	// Function to update record in table
	public function update_record($data, $id)
	{
		$this->db->where('vendor_id', $id);
		if ($this->db->update('ms_vendors', $data)) {
			return true;
		} else {
			return false;
		}
	}
}
