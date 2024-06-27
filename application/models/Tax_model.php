<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tax_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_taxes()
	{
		return $this->db->get("xin_tax_types");
	}

	public function find_best_match($tax_name, $threshold = 90)
	{
		$taxes = $this->get_taxes()->result();
		$bestMatch = null;
		$highestSimilarity = 0;

		foreach ($taxes as $tax) {
			$distance = levenshtein($tax_name, $tax->name);
			$max_len = max(strlen($tax_name), strlen($tax->name));
			$similarity = (1 - $distance / $max_len) * 100;

			if ($similarity > $highestSimilarity) {
				$highestSimilarity = $similarity;
				$bestMatch = $tax;
			}
		}

		if ($highestSimilarity >= $threshold) {
			return $bestMatch;
		} else {
			return null;
		}
	}


	public function read_tax_information($id)
	{

		$condition = "tax_id =" . "'" . $id . "'";
		$this->db->select('*');
		$this->db->from('xin_tax_types');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() == 1) {
			return $query->result();
		} else {
			return false;
		}
	}

	// Function to add record in table
	public function add_tax_record($data)
	{
		$this->db->insert('xin_tax_types', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	// get all product taxes
	public function get_all_taxes()
	{
		$query = $this->db->query("SELECT * from xin_tax_types");
		return $query->result();
	}

	// Function to Delete selected record from table
	public function delete_tax_record($id)
	{
		$this->db->where('tax_id', $id);
		$this->db->delete('xin_tax_types');
	}

	// Function to update record in table
	public function update_tax_record($data, $id)
	{
		$this->db->where('tax_id', $id);
		if ($this->db->update('xin_tax_types', $data)) {
			return true;
		} else {
			return false;
		}
	}
}
