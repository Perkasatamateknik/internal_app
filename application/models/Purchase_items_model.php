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

	public function read_items_pi_by_project_id($id)
	{
		return $this->db->select(['ms_items_purchase_invoice.*', 'xin_projects.project_id', 'COALESCE(xin_projects.title, "--") as title'])
			->from('ms_items_purchase_invoice')
			->join('xin_projects', 'ms_items_purchase_invoice.project_id=xin_projects.project_id')
			->where('ms_items_purchase_invoice.project_id', $id)
			->get();
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

	public function calculate_payment($id)
	{

		$record = $this->db->where('pi_number', $id)->get('ms_purchase_invoices')->row();

		$log = $this->db->where('pi_number', $id)->get('ms_purchase_logs')->row();

		if ($log->payment_number == 0) {
			$payment_number = "PRC" . strtoupper(uniqid());

			$this->db->update('ms_purchase_logs', ['payment_number' => $payment_number], ['pi_number' => $id]);
		} else {
			$payment_number = $log->payment_number;
		}

		$records = $this->db->where('pi_number', $id)->get('ms_items_purchase_invoice')->result();
		// $rec = $this->db->select(['sum(tax_rate) as tax_amount', 'sum(discount_rate) as discount_amount', 'sum(price*quantity) as item_amount', 'sum(amount) as amount'])->where('pi_number', $id)->get('ms_items_purchase_invoice')->row();
		// $rec->payment_number = $payment_number;

		$res = new stdClass();
		$res->tax_amount = 0;
		$res->discount_amount = 0;
		$res->item_amount = 0;
		$res->total_amount = 0;
		$res->payment_number = $payment_number;
		$res->service_fee = $record->service_fee;
		$res->delivery_fee = $record->delivery_fee;

		$rand = uniqid();
		foreach ($records as $i => $r) {

			$res->tax_amount += $r->tax_rate;
			$res->discount_amount += $r->discount_rate;

			$res->item_amount += $r->price * $r->quantity;
			$res->total_amount += $r->amount;
		}

		$res->total_amount += $record->service_fee + $record->delivery_fee;
		return $res;
	}
}
