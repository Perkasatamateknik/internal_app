<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contact_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_all_contact()
	{
		return $this->db->get("ms_contacts");
	}
	public function get_contact($id)
	{
		return $this->db->select(["ms_contacts.*", "xin_countries.country_id", "xin_countries.country_name", "ms_contact_types.type_id", "ms_contact_types.contact_type"])
			->from('ms_contacts')
			->join('ms_contact_types', 'ms_contacts.contact_type_id=ms_contact_types.type_id', 'LEFT')
			->join('xin_countries', 'ms_contacts.country=xin_countries.country_id', 'LEFT')
			->where('ms_contacts.contact_id', $id)
			->get()->row();
	}

	public function count_contacts()
	{
		$query = $this->db->select('COUNT(ms_contacts.contact_id) as count')->get('ms_contacts')->row()->count;
		return sprintf("%03d", $query);
	}

	// Function to update selected record from table
	public function update($id, $data)
	{
		$this->db->where('contact_id', $id);
		return $this->db->update('ms_contacts', $data);
	}



	public function get_all_contact_type()
	{
		return $this->db->get("ms_contact_types");
	}

	public function get_contact_type($id)
	{
		return $this->db->get_where("ms_contact_types", ['type_id' => $id])->row();
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

	public function store($data)
	{
		return $this->db->insert('ms_contacts', $data);
	}

	public function store_type($data)
	{
		$this->db->insert('ms_contact_types', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_type($id)
	{
		$this->db->where('type_id', $id);
		return $this->db->delete('ms_contact_types');
	}

	public function update_type($id, $data)
	{
		$this->db->where('type_id', $id);
		return $this->db->update('ms_contact_types', $data);
	}

	// new row
	public function find_contact_type($query = false)
	{
		if ($query) {
			$this->db->like('contact_type', $query);
		}

		// limit record to 10
		$this->db->limit(10);
		// order by account_name
		$this->db->order_by('contact_type', 'ASC');
		return $this->db->get("ms_contact_types")->result();
	}
}
