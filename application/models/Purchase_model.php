<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}


	//! Role Purchase Requisition 
	public function get_all_pr()
	{
		return $this->db->order_by('pr_number', 'DESC')->get("ms_purchase_requisitions");
	}

	public function read_pr($id, $field = false)
	{
		if ($field) {
			$this->db->select($field);
		} else {
			$this->db->select("*");
		}
		$this->db->from("ms_purchase_requisitions");
		return $this->db->where('pr_id', $id)->get()->row();
	}

	public function read_pr_by_pr_number($id, $field = false)
	{
		if ($field) {
			$this->db->select($field);
		} else {
			$this->db->select("*");
		}
		$this->db->from("ms_purchase_requisitions");
		$res = $this->db->where('pr_number', $id)->get()->row();

		if ($res) {
			$res->amount = $this->get_amount_pr($id)->amount;
			return $res;
		} else {
			return null;
		}
	}

	public function get_last_pr_number()
	{
		return $this->db->select('pr_number')
			->from('ms_purchase_requisitions')
			->order_by('pr_number', 'desc')
			->limit(1)->get()->row();
	}

	public function insert_pr($data, $items)
	{
		$this->db->trans_start();
		$this->db->insert('ms_purchase_requisitions', $data);
		$this->db->insert_batch('ms_items_purchase_requisition', $items);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	// public function insert_items_pr($data)
	// {
	// 	$this->db->insert('ms_items_purchase_requisition', $data);
	// 	if ($this->db->affected_rows() > 0) {
	// 		return true;
	// 	} else {
	// 		return false;
	// 	}
	// }

	public function delete_pr($id)
	{
		// delete items
		$this->db->trans_start();
		$this->db->where('pr_number', $id)->delete('ms_items_purchase_requisition');
		$this->db->where('pr_number', $id)->delete('ms_purchase_requisitions');
		return $this->db->trans_complete();
	}

	public function reject_pr($id)
	{
		$this->db->set('purchase_status', 0);
		$this->db->where('pr_number', $id);
		$this->db->update('ms_purchase_requisitions');
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function closed_pr($id)
	{
		$this->db->set('purchase_status', 2);
		$this->db->where('pr_number', $id);
		$this->db->update('ms_purchase_requisitions');
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_amount_pr($id)
	{
		$this->db->select_sum('amount');
		$this->db->from('ms_items_purchase_requisition');
		$this->db->where('pr_number', $id);
		return $this->db->get()->row();
	}

	public function update_pr($id, $data, $item_update = false, array $item_insert = null)
	{
		$this->db->trans_start();
		$this->db->where('pr_number', $id)->update('ms_purchase_requisitions', $data);
		if ($item_update) {
			$this->db->update_batch('ms_items_purchase_requisition', $item_update, 'item_pr_id');
		}

		if (!is_null($item_insert)) {
			if (count($item_insert) > 0) {
				$this->db->insert_batch('ms_items_purchase_requisition', $item_insert);
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}



	//! Role Pyrchase Orders
	public function get_all_po()
	{
		return $this->db->order_by('po_number', 'DESC')->get("ms_purchase_orders");
	}

	public function get_last_po_number()
	{
		return $this->db->select('po_number')
			->from('ms_purchase_orders')
			->order_by('po_number', 'desc')
			->limit(1)->get()->row();
	}


	public function insert_po($data, $item, $trans, $pr_number = false)
	{
		$this->db->trans_start();
		$this->db->insert('ms_purchase_orders', $data);
		$this->db->insert_batch('ms_items_purchase_order', $item);

		// jika ada dp
		if (count($trans) > 0) {
			$this->db->insert_batch('ms_finance_account_transactions', $trans);
		}

		$this->db->trans_complete();
		$res = $this->db->trans_status();

		if ($pr_number) {
			// tutup status jadi closed
			$this->closed_pr($pr_number);
		}

		return $res;
	}

	public function read_po_by_po_number($id)
	{
		// return object or null
		$res = $this->db->select("*")
			->from("ms_purchase_orders")
			->where('po_number', $id)
			->get()->row();

		if ($res) {
			$res->amount = $this->get_amount_po($id)->amount;
			return $res;
		} else {
			return null;
		}
	}

	public function update_status_po($id, $status)
	{
		$this->db->set('status', $status);
		$this->db->where('po_number', $id);
		$this->db->update('ms_purchase_orders');
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_amount_po($id)
	{
		$this->db->select_sum('amount');
		$this->db->from('ms_items_purchase_order');
		$this->db->where('po_number', $id);
		return $this->db->get()->row();
	}

	public function delete_po($id)
	{
		// delete items
		$this->db->trans_start();
		$this->db->where('po_number', $id)->delete('ms_items_purchase_order');
		$this->db->where('po_number', $id)->delete('ms_purchase_orders');
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function update_po($id, $data, $item_update = false, array $item_insert = null)
	{
		$this->db->trans_start();
		$this->db->where('po_number', $id)->update('ms_purchase_orders', $data);
		if ($item_update) {
			$this->db->update_batch('ms_items_purchase_order', $item_update, 'item_po_id');
		}

		if (!is_null($item_insert)) {
			if (count($item_insert) > 0) {
				$this->db->insert_batch('ms_items_purchase_order', $item_insert);
			}
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}


	//! Role Purchase Deliveriies
	public function insert_pd($data, $item, $trans)
	{
		$this->db->trans_start();
		$this->db->insert('ms_purchase_deliveries', $data);
		$this->db->insert_batch('ms_items_purchase_delivery', $item);
		$this->db->insert_batch('ms_finance_account_transactions', $trans);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function closed_pd($id)
	{
		$this->db->set('status', 2);
		$this->db->where('pd_number', $id);
		$this->db->update('ms_purchase_deliveries');
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function read_pd_by_pd_number($id)
	{
		return $this->db->select("*")
			->from("ms_purchase_deliveries")
			->where('pd_number', $id)
			->get()->row();
	}

	public function get_last_pd_number()
	{
		return $this->db->select('pd_number')
			->from('ms_purchase_deliveries')
			->order_by('pd_number', 'desc')
			->limit(1)->get()->row();
	}


	public function get_all_pd()
	{
		return $this->db->order_by('pd_number', 'DESC')->get("ms_purchase_deliveries");
	}

	public function get_amount_pd($id)
	{
		$this->db->select_sum('amount');
		$this->db->from('ms_items_purchase_delivery');
		$this->db->where('pd_number', $id);
		return $this->db->get()->row();
	}

	public function delete_pd($id)
	{
		// delete items
		$this->db->trans_start();
		$this->db->where('pd_number', $id)->delete('ms_items_purchase_delivery');
		$this->db->where('pd_number', $id)->delete('ms_purchase_deliveries');
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function update_pd($id, $data)
	{
		$this->db->trans_start();
		$this->db->where('pd_number', $id)->update('ms_purchase_deliveries', $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function update_status_pd($id)
	{
		$this->db->set('status', 2);
		$this->db->where('pd_number', $id);
		$this->db->update('ms_purchase_deliveries');
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}


	//! Role Purchase Invoices
	public function insert_pi($data, $item, $trans)
	{
		$this->db->trans_start();
		$this->db->insert('ms_purchase_invoices', $data);
		$this->db->insert_batch('ms_items_purchase_invoice', $item);
		$this->db->insert_batch('ms_finance_account_transactions', $trans);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function insert_batch_pi($data)
	{
		$this->db->insert_batch('ms_purchase_invoices', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function read_pi_by_pi_number($id)
	{
		return $this->db->select("*")
			->from("ms_purchase_invoices")
			->where('pi_number', $id)
			->get()->row();
	}

	public function get_last_pi_number()
	{
		return $this->db->select('pi_number')
			->from('ms_purchase_invoices')
			->order_by('pi_number', 'desc')
			->limit(1)->get()->row();
	}

	public function get_all_pi()
	{
		return $this->db->order_by('pi_number', 'DESC')->get("ms_purchase_invoices");
	}

	public function get_all_pi_24()
	{
		return $this->db->select('pi_number')->where('YEAR(date)', date('Y'))->get("ms_purchase_invoices");
	}

	public function get_all_pi_unpaid()
	{
		return $this->db->where('status !=', 2)->get("ms_purchase_invoices");
	}

	public function get_amount_pi($id)
	{
		$this->db->select_sum('amount');
		// $this->db->from('ms_purchase_invoices');
		$this->db->from('ms_items_purchase_invoice');
		$this->db->where('pi_number', $id);
		return $this->db->get()->row();
	}

	public function delete_pi($id)
	{
		$this->db->trans_start();
		$this->db->where('pi_number', $id)->delete('ms_items_purchase_invoice');
		$this->db->where('pi_number', $id)->delete('ms_purchase_invoices');
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function update_pi($id, $data, $item_update = false, array $item_insert = null)
	{
		$this->db->trans_start();
		$this->db->where('pi_number', $id)->update('ms_purchase_invoices', $data);
		if ($item_update) {
			$this->db->update_batch('ms_items_purchase_invoice', $item_update, 'item_pi_id');
		}

		if (!is_null($item_insert)) {
			if (count($item_insert) > 0) {
				$this->db->insert_batch('ms_items_purchase_invoice', $item_insert);
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}


	//! Role Purchase Log
	public function insert_pl($data)
	{
		$this->db->trans_start();
		$this->db->insert('ms_purchase_logs', $data);

		// if ($trans) {
		// 	$this->db->insert_batch('ms_finance_account_transactions', $trans);
		// }

		$this->db->trans_complete();
		return $this->db->trans_status();
	}


	public function update_pl($id, $where, $data)
	{
		$this->db->where($where, $id);
		$this->db->update('ms_purchase_logs', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function read_pl($id, $where)
	{
		$this->db->select("*");
		$this->db->from("ms_purchase_logs");
		return $this->db->where($where, $id)->get()->row();
	}

	public function get_list_tagihan_pi($id)
	{
		$record = $this->db->select('*')->where('pi_number', $id)->get('ms_purchase_invoices')->row();

		$records = $this->db->select("*")->where('pi_number', $id)->get('ms_items_purchase_invoice')->result();

		$discount = 0;
		$tax = 0;
		$subtotal = 0;

		foreach ($records as $key => $r) {
			// count discount
			$discount += $r->discount_rate;

			// count tax
			$tax += $r->tax_rate;

			// count subtotal
			$subtotal += ($r->price * $r->quantity);
		}

		// tambahin data yang sudah di bayarkan

		return $subtotal - $discount + $tax + $record->delivery_fee;
	}

	public function get_trans_payment($id)
	{
		$record = $this->db->select('*')->where('pi_number', $id)->get('ms_purchase_invoices')->row();

		$records = $this->db->select("*")->where('pi_number', $id)->get('ms_items_purchase_invoice')->result();

		$discount = 0;
		$tax = 0;
		$subtotal = 0;

		foreach ($records as $key => $r) {
			// count discount
			$discount += $r->discount_rate;

			// count tax
			$tax += $r->tax_rate;

			// count subtotal
			$subtotal += ($r->price * $r->quantity);
		}

		// tambahin data yang sudah di bayarkan
		$jumlah_tagihan =  $subtotal - $discount + $tax + $record->delivery_fee + $record->service_fee;

		$get_tagihan_dibayar = $this->db->select(['ms_finance_account_transactions.*', 'COALESCE(ms_finance_accounts.account_code, "--") as account_code', 'COALESCE(ms_finance_accounts.account_name, "--") as account_name', 'xin_employees.first_name', 'xin_employees.last_name'])
			->from('ms_finance_account_transactions')->join('ms_finance_accounts', 'ms_finance_account_transactions.account_id=ms_finance_accounts.account_id', 'LEFT')
			->join('xin_employees', 'ms_finance_account_transactions.user_id=xin_employees.user_id')
			->where('ms_finance_account_transactions.account_trans_cat_id', 4) // 4 = purchase invoices
			->where('ms_finance_account_transactions.type', 'credit')
			->where('ms_finance_account_transactions.join_id', $id)->get()->result();

		$tagihan_dibayar = 0;
		foreach ($get_tagihan_dibayar as $val) {
			$tagihan_dibayar += $val->amount;
		}

		$data = new stdClass;
		$data->sisa_tagihan = $jumlah_tagihan - $tagihan_dibayar;
		$data->jumlah_tagihan = $jumlah_tagihan;
		$data->jumlah_dibayar = $tagihan_dibayar;
		$data->log_payments = $get_tagihan_dibayar;

		return $data;
	}

	public function get_tagihan_dibayar_pi($id)
	{
		$tagihan_dibayar = $this->db->select('SUM(amount) as amount_paid')
			->where('account_trans_cat_id', 4) // 4 = purchase invoice
			->where('join_id', $id)
			->where('type', 'debit')
			->group_by('account_trans_id')
			->get('ms_finance_account_transactions')->result();

		if (!is_null($tagihan_dibayar)) {
			return $tagihan_dibayar;
		} else {
			return null;
		}
	}

	public function calculate_purchase($trans)
	{
		return $this->db->insert_batch('ms_finance_account_transactions', $trans);
	}
}
