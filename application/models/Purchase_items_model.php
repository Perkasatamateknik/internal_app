<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_items_model extends CI_Model
{
	// role Purchase Requisition
	public function insert_items_pr($data, $batch = false)
	{
		if ($batch) {
			$this->db->insert_batch('ms_items_purchase_requisition', $data);
		} else {
			$this->db->insert('ms_items_purchase_requisition', $data);
		}

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function read_items_pr_by_pr_number($id)
	{
		return $this->db->select("*")->from("ms_items_purchase_requisition")->where('pr_number', $id)->get();
	}

	public function delete_item_pr_by_id($id)
	{
		return $this->db->where('item_pr_id', $id)->delete('ms_items_purchase_requisition');
	}

	public function delete_item_pr_by_pr_number($id)
	{
		return $this->db->where('pr_number', $id)->delete('ms_items_purchase_requisition');
	}




	// role Purchase Order
	public function insert_items_po($data, $batch = false)
	{
		if ($batch) {
			$this->db->insert_batch('ms_items_purchase_order', $data);
		} else {
			$this->db->insert('ms_items_purchase_order', $data);
		}

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function read_items_po_by_po_number($id)
	{
		return $this->db->select("*")->from("ms_items_purchase_order")->where('po_number', $id)->get();
	}

	public function delete_item_po_by_id($id)
	{
		return $this->db->where('item_po_id', $id)->delete('ms_items_purchase_order');
	}

	public function delete_item_po_by_po_number($id)
	{
		return $this->db->where('po_number', $id)->delete('ms_items_purchase_order');
	}


	# Purchase Delivery

	public function insert_items_pd($data, $batch = false)
	{
		if ($batch) {
			$this->db->insert_batch('ms_items_purchase_delivery', $data);
		} else {
			$this->db->insert('ms_items_purchase_delivery', $data);
		}

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function read_items_pd_by_pd_number($id)
	{
		return $this->db->select("*")->from("ms_items_purchase_delivery")->where('pd_number', $id)->get();
	}


	# Purchase Invoice
	public function insert_items_pi($data, $batch = false)
	{
		if ($batch) {
			$this->db->insert_batch('ms_items_purchase_invoice', $data);
		} else {
			$this->db->insert('ms_items_purchase_invoice', $data);
		}

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function read_items_pi_by_pi_number($id)
	{
		return $this->db->select("*")->from("ms_items_purchase_invoice")->where('pi_number', $id)->get();
	}

	public function delete_item_pi_by_id($id)
	{
		return $this->db->where('item_pi_id', $id)->delete('ms_items_purchase_invoice');
	}

	public function delete_item_pi_by_pi_number($id)
	{
		return $this->db->where('pi_number', $id)->delete('ms_items_purchase_invoice');
	}


	public function get_trans_by_vendor()
	{
		$this->db->select('vendor_id, SUM(amount) as amount');
		$this->db->from('ms_purchase_invoices');
		$this->db->where('MONTH(date)', date('m'));
		$this->db->where('YEAR(date)', date('Y'));
		$this->db->group_by('vendor_id');
		return $this->db->group_by('vendor_id')->get();
	}
}
