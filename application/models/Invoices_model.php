<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Invoices_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function trans_number()
	{
		$query = $this->get_last_invoice();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->trans_number, 4));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("INV-%05d", $nextNumericPart);
	}
	// get all invoices
	public function all()
	{
		return $this->db->get("ms_finance_invoices");
	}

	public function get($id)
	{
		$this->db->where('invoice_id', $id);
		return $this->db->get("ms_finance_invoices");
	}

	public function get_last_invoice()
	{
		return $this->db->select('*')
			->from('ms_finance_invoices')
			->order_by('trans_number', 'desc')
			->limit(1)->get()->row();
	}

	public function init_trans()
	{
		$last = $this->get_last_invoice();

		if (is_null($last) or !in_array(null, [$last->client_id], true)) {
			$trans_number = $this->trans_number();
			$this->db->insert('ms_finance_invoices', [
				'trans_number' => $trans_number
			]);
			$last_id = $this->db->insert_id();

			return $this->get($last_id)->row();
		} else {
			return $last;
		}
	}

	public function update_with_items_and_files($id, $data, array $items = null, array $files = null)
	{
		$this->db->trans_start();
		$this->db->update('ms_finance_invoices', $data, ['invoice_id' => $id]);

		if (!is_null($items)) {
			if (count($items) > 0) {
				$this->db->insert_batch('ms_finance_invoice_trans', $items);
			}
		}

		if (!is_null($files)) {
			if (count($files) > 0) {
				$this->db->insert_batch('ms_files', $files);
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function get_by_number_doc($id)
	{
		$this->db->where("trans_number", $id);
		$res = $this->db->get("ms_finance_invoices");

		if ($res->num_rows() > 0) {
			return $res->row();
		} else {
			return null;
		}
	}


	/// OLD FUNCTION ??

	public function get_invoices()
	{
		return $this->db->get("xin_hrsale_invoices");
	}

	public function get_taxes()
	{
		return $this->db->get("xin_tax_types");
	}

	public function get_employee_project_invoices($id)
	{

		$sql = 'SELECT * FROM xin_hrsale_invoices WHERE project_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);
		return $query;
	}
	public function get_completed_invoices()
	{

		$sql = 'SELECT * FROM xin_hrsale_invoices WHERE status = ?';
		$binds = array(1);
		$query = $this->db->query($sql, $binds);
		return $query->result();
	}
	public function get_pending_invoices()
	{

		$sql = 'SELECT * FROM xin_hrsale_invoices WHERE status = ?';
		$binds = array(0);
		$query = $this->db->query($sql, $binds);
		return $query->result();
	}
	public function read_invoice_info($id)
	{

		$condition = "invoice_id =" . "'" . $id . "'";
		$this->db->select('*');
		$this->db->from('xin_hrsale_invoices');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->result();
		} else {
			return null;
		}
	}

	public function read_invoice_items_info($id)
	{

		$condition = "invoice_item_id =" . "'" . $id . "'";
		$this->db->select('*');
		$this->db->from('xin_hrsale_invoices_items');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->result();
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
			return null;
		}
	}

	public function get_invoice_items($id)
	{

		$sql = 'SELECT * FROM xin_hrsale_invoices_items WHERE invoice_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);

		return $query->result();
	}
	public function get_client_invoices($id)
	{

		$sql = 'SELECT * FROM xin_hrsale_invoices WHERE client_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);
		return $query;
	}
	public function get_client_payment_invoices($id)
	{

		$sql = 'SELECT * FROM xin_finance_transaction WHERE client_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);
		return $query;
	}
	public function get_client_invoice_payments_all()
	{

		$sql = 'SELECT * FROM xin_finance_transaction WHERE invoice_id != ""';
		$query = $this->db->query($sql);
		return $query;
	}
	// last 4 projects
	public function last_five_client_invoices($id)
	{
		$sql = 'SELECT * FROM xin_hrsale_invoices where client_id = ? order by invoice_id desc limit ?';
		$binds = array($id, 5);
		$query = $this->db->query($sql, $binds);

		return $query->result();
	}

	// Function to add record in table
	public function add_invoice_record($data)
	{
		$this->db->insert('xin_hrsale_invoices', $data);
		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return false;
		}
	}

	// Function to add record in table
	public function add_invoice_items_record($data)
	{
		$this->db->insert('xin_hrsale_invoices_items', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
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

	// Function to Delete selected record from table
	public function delete_record($id)
	{
		$this->db->where('invoice_id', $id);
		$this->db->delete('xin_hrsale_invoices');
	}

	// Function to Delete selected record from table
	public function delete_invoice_items($id)
	{
		$this->db->where('invoice_id', $id);
		$this->db->delete('xin_hrsale_invoices_items');
	}

	// Function to Delete selected record from table
	public function delete_invoice_items_record($id)
	{
		$this->db->where('invoice_item_id', $id);
		$this->db->delete('xin_hrsale_invoices_items');
	}

	// Function to Delete selected record from table
	public function delete_tax_record($id)
	{
		$this->db->where('tax_id', $id);
		$this->db->delete('xin_tax_types');
	}

	// Function to update record in table
	public function update_invoice_record($data, $id)
	{
		$this->db->where('invoice_id', $id);
		if ($this->db->update('xin_hrsale_invoices', $data)) {
			return true;
		} else {
			return false;
		}
	}

	// Function to update record in table
	public function update_invoice_items_record($data, $id)
	{
		$this->db->where('invoice_item_id', $id);
		if ($this->db->update('xin_hrsale_invoices_items', $data)) {
			return true;
		} else {
			return false;
		}
	}
}
