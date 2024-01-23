<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/admin/Purchasing.php';

class Purchase_invoices extends Purchasing
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		$this->load->model("Tax_model");
		$this->load->model("Exin_model");

		$this->load->model("Vendor_model");
		$this->load->model("Product_model");
		$this->load->model("Project_model");
		$this->load->model("Department_model");
		$this->load->model("Purchase_items_model");
		$this->load->model("Purchase_model");
	}

	/*Function to set JSON output*/
	public function output($Return = array())
	{
		/*Set respinse header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function pi_number()
	{
		$query = $this->Purchase_model->get_last_pi_number();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->pi_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("PI-%05d", $nextNumericPart);
	}

	public function index()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$selected = $this->input->get('id') ?? "PI-0";

		$split = explode('-', $selected);
		if ($split[0] == "PO") {
			$p_data = $this->Purchase_model->read_po_by_po_number($selected);

			if (is_null($p_data)) {
				redirect('admin/purchase_orders');
			}
		} elseif ($split[0] == "PD") {
			$log = $this->Purchase_model->read_pl($selected, 'pd_number');
			$data['pd'] = $this->Purchase_model->read_pd_by_pd_number($log->pd_number); //get data purchase delivery
			$p_data = $this->Purchase_model->read_po_by_po_number($log->po_number);
			if (is_null($p_data)) {
				redirect('admin/purchase_orders');
			}
		} else {
			$p_data = false;
		}

		if ($p_data) {
			$data['po_number'] = $p_data->po_number;
			$select = ' | ' . $p_data->po_number;
			$data['record'] = $p_data;
		} else {
			$data['po_number'] = 0;
			$data['pd_number'] = 0;
			$data['record'] = false;
			$select = '';
		}

		$data['pi_number'] = $this->pi_number();
		$data['title'] = $this->lang->line('ms_purchase_invoices') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_invoices') . $select;
		$data['path_url'] = 'purchase_invoice';

		$role_resources_ids = $this->Xin_model->user_role_resource();


		if (in_array('519', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_invoices/invoice_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}


	public function insert()
	{
		// Get the input data
		$po_number = $this->input->post('po_number');
		$pi_number = $this->input->post('pi_number');

		// if ($this->input->is_ajax_request()) {
		if (true) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('vendor_id') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_field');
			} else if ($this->input->post('warehouse_assign') === '') {
				$Return['error'] = $this->lang->line('ms_error_warehouse_assign_field');
			} else if ($this->input->post('faktur_number') === '') {
				$Return['error'] = $this->lang->line('ms_error_faktur_number_field');
			} else if ($this->input->post('date') === '') {
				$Return['error'] = $this->lang->line('ms_error_date_field');
			} else if ($this->input->post('termin') === '') {
				$Return['error'] = $this->lang->line('ms_error_field');
			}


			if (is_null($this->input->post('row_item_name'))) {
				$Return['error'] = $this->lang->line('ms_error_item_empty_data');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
				exit();
			}

			$user_id = $this->session->userdata()['username']['user_id'] ?? 0;
			$department_id = $this->session->userdata()['username']['department_id'] ?? 0;

			$data_pi = array(
				'vendor_id'			=> $this->input->post('vendor'),
				'pi_number'			=> $pi_number,
				'added_by'			=> $user_id,
				'department_id'		=> $department_id,
				'warehouse_assign'	=> $this->input->post('warehouse_assign'),
				'faktur_number'		=> $this->input->post('faktur_number'),
				'date'				=> $this->input->post('date') ?? date("Y-m-d"),
				'due_date'			=> $this->input->post('due_date') ?? date("Y-m-d"),
				'termin'			=> $this->input->post('select_due_date'),
				'delivery_name'		=> $this->input->post('delivery_name'),
				'delivery_date'		=> $this->input->post('delivery_date'),
				'delivery_number'	=> $this->input->post('delivery_number'),
				'delivery_fee'		=> $this->input->post('delivery_fee') ?? 0,
				'status'			=> 0,
				'reference'			=> $this->input->post('reference'),
				'notes'				=> $this->input->post('notes'),
				'attachment'		=> $this->upload_file('invoice'),
				'created_at'		=> date("Y-m-d H:i:s"),

			);

			$item_insert = [];

			for ($i = 0; $i < count($this->input->post('row_item_id')); $i++) {
				$item_insert[] = [
					'pi_number' 		=> $pi_number,
					'product_id' 		=> $this->input->post('row_item_id')[$i],
					'product_name' 		=> $this->input->post('row_item_name')[$i],
					'product_number'	=> $this->input->post('row_product_number')[$i],

					'sub_category_id' 	=> $this->input->post('row_sub_category_id')[$i] ?? null, // override to null
					'sub_category_name' => $this->input->post('row_sub_category_name')[$i] ?? null, // override to null
					'category_id' 		=> $this->input->post('row_category_id')[$i] ?? null, // override to null
					'category_name' 	=> $this->input->post('row_category_name')[$i] ?? null, // override to null
					'uom_id' 			=> $this->input->post('row_uom_id')[$i] ?? null, // override to null
					'uom_name' 			=> $this->input->post('row_uom_name')[$i] ?? null, // override to null

					'project_id' 		=> $this->input->post('row_project_id')[$i] ?? null, // override to 0
					'tax_id' 			=> $this->input->post('row_tax_id')[$i] ?? null, //override to 0
					'tax_rate' 			=> $this->input->post('row_tax_rate')[$i],
					'discount_id'		=> $this->input->post('row_discount_id')[$i] ?? null, //override to 0
					'discount_rate'		=> $this->input->post('row_discount_rate')[$i],
					'quantity'			=> $this->input->post('row_qty')[$i],
					'price'				=> $this->input->post('row_item_price')[$i],
					'amount'			=> $this->input->post('row_amount')[$i],
				];
			}

			// insert data
			$insert_pi = $this->Purchase_model->insert_pi($data_pi, $item_insert);

			// tutup status jadi closed pd
			$this->Purchase_model->closed_pr($po_number);

			$read_pl = $this->Purchase_model->read_pl($po_number, 'po_number');
			if (!is_null($read_pl)) {
				//update logs
				$this->Purchase_model->update_pl($po_number, 'po_number', ['pi_number' => $pi_number, 'updated_at' => date("Y-m-d H:i:s")]);
			} else {
				# add logs
				$this->Purchase_model->insert_pl(['pi_number' => $pi_number, 'created_at' => date("Y-m-d H:i:s")]);
			}

			if ($insert_pi) {
				$Return['result'] = $this->lang->line('ms_pi_added');
				$this->output($Return);
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
				$this->output($Return);
			}
		}
	}

	public function get_ajax_table()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));


		$records = $this->Purchase_model->get_all_pi();

		$data = array();

		foreach ($records->result() as $r) {
			$pi_number = '<a href="' . site_url() . 'admin/purchase_invoices/view/' . $r->pi_number . '/">' . $r->pi_number . '</a>';

			if (in_array('523', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/purchase_invoices/edit/' . $r->pi_number  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('524', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->pi_number . '" data-token_type="purchase_invoices"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			/// get vendor
			$vendor = $this->Vendor_model->read_vendor_information($r->vendor_id);
			if (!is_null($vendor)) {
				$vendor = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
			} else {
				$vendor = '--';
			}

			$combhr = $edit . $delete;

			$amount = $this->Purchase_model->get_amount_pi($r->pi_number)->amount;

			$data[] = array(
				$combhr,
				$pi_number,
				$vendor,
				$this->Xin_model->set_date_format($r->date),
				pi_stats($r->status),
				$this->Xin_model->set_date_format($r->date), // blm ganti
				dateDiff($r->date, $r->due_date),
				$this->Xin_model->currency_sign($amount),
			);
		}


		$output = array(
			"draw" => $draw,
			"recordsTotal" => $records->num_rows(),
			"recordsFiltered" => $records->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function view()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_pi_by_pi_number($id);
		if ($record) {
			$data['pi_number'] = $record->pi_number;
			$data['record'] = $record;
			$select = ' | ' . $record->pi_number;
		} else {
			redirect('admin/purchase_orders');
		}

		$vendor = $this->Vendor_model->read_vendor_information($record->vendor_id);
		if (!is_null($vendor)) {
			$record->vendor = $vendor[0]->vendor_name;
		} else {
			$record->vendor = "--";
		}

		$data['title'] = $this->lang->line('ms_purchase_invoices') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_invoices') . $select;
		$data['path_url'] = 'purchase_invoice';
		$data['pi_number'] = $id;
		$data['log'] = $this->logs();

		$records = $this->Purchase_items_model->read_items_pi_by_pi_number($id);
		$item = array();

		// define var
		$discount = 0;
		$tax = 0;
		$subtotal = 0;
		foreach ($records->result() as $r) {
			$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
			if (!is_null($project)) {
				$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
			} else {
				$project_name = '--';
			}

			$item_name = $r->product_name . '<br><b style="font-size:10px">' . $r->product_number . '</b>';
			$item[] = array(
				$item_name,
				$project_name,
				$this->Xin_model->currency_sign($r->tax_rate),
				$this->Xin_model->currency_sign($r->discount_rate),
				$r->quantity,
				$this->Xin_model->currency_sign($r->price),
				$this->Xin_model->currency_sign($r->amount),
			);

			// count discount
			$discount += $r->discount_rate;

			// count tax
			$tax += $r->tax_rate;

			// count subtotal
			$subtotal += ($r->price * $r->quantity);
		}

		$record->discount = $discount;
		$record->tax = $tax;
		$record->subtotal = $subtotal;

		// get tagihan dibayar
		$get_trans_payment = $this->Purchase_model->get_trans_payment($id);
		$record->jumlah_dibayar = $get_trans_payment->jumlah_dibayar;
		$record->sisa_tagihan = $get_trans_payment->sisa_tagihan;
		$record->log_payments = $get_trans_payment->log_payments;

		// dd($get_trans_payment);
		$data['records'] = $item;
		$role_resources_ids = $this->Xin_model->user_role_resource();

		if (in_array('522', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_invoices/view", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function print()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_pi_by_pi_number($id);
		if ($record) {
			$data['pi_number'] = $record->pi_number;
			$data['record'] = $record;
		} else {
			redirect('admin/purchase_orders');
		}
		$vendor = $this->Vendor_model->read_vendor_information($record->vendor_id);
		if (!is_null($vendor)) {
			$record->vendor = $vendor[0]->vendor_name;
		} else {
			$record->vendor = "--";
		}

		$data['title'] = $this->lang->line('ms_purchase_orders') . ' | ' . $this->Xin_model->site_title();
		$data['pi_number'] = $id;


		// define var
		$item = [];
		$discount = 0;
		$tax = 0;
		$subtotal = 0;
		$total = 0;

		$records = $this->Purchase_items_model->read_items_pi_by_pi_number($id);
		foreach ($records->result() as $r) {
			$item_name = $r->product_name . '<br><b style="font-size:10px">' . $r->product_number . '</b>';
			$item[] = array(
				$item_name,
				$this->Xin_model->currency_sign($r->tax_rate),
				$this->Xin_model->currency_sign($r->discount_rate),
				$r->quantity,
				$this->Xin_model->currency_sign($r->price),
				$this->Xin_model->currency_sign($r->amount),
			);

			// count discount
			$discount += $r->discount_rate;

			// count tax
			$tax += $r->tax_rate;

			// count subtotal
			$subtotal += ($r->price * $r->quantity);

			// count total
			$total += $r->amount;
		}

		$record->discount = $discount;
		$record->tax = $tax;
		$record->subtotal = $subtotal;
		$record->total = $subtotal - $discount + $tax + $record->delivery_fee;

		$data['records'] = $item;
		if (!empty($session)) {
			return $this->load->view("admin/purchase_invoices/print", $data);
		} else {
			redirect('admin/');
		}
	}

	public function get_ajax_po()
	{
		$po_number = $this->input->get('po_number');
		$po_data = $this->Purchase_model->read_po_by_po_number($po_number);
		$po_items = $this->Purchase_items_model->read_items_po_by_po_number($po_number)->result();

		if (!is_null($po_data) && !is_null($po_items)) {
			$output = [
				'data' => $po_data,
				'items' => $po_items
			];
		} else {
			$output = false;
		}
		echo json_encode($output);
		exit();
	}

	public function delete()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		$id = $this->input->post('_token');
		$get = $this->Purchase_model->read_pi_by_pi_number($id);
		$this->delete_file($get->attachment, 'invoice');
		$result = $this->Purchase_model->delete_pi($id);
		if ($result) {
			//update logs
			$this->Purchase_model->update_pl($id, 'pi_number', ['pi_number' => NULL, 'updated_at' => date("Y-m-d H:i:s")]);
			$Return['result'] = $this->lang->line('ms_success_pi_deleted');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}
		$this->output($Return);
	}

	public function edit()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_pi_by_pi_number($id);
		if ($record) {
			$select = ' | ' . $record->pi_number;
		} else {
			redirect('admin/purchase_invoices');
		}

		$data['title'] = $this->lang->line('ms_purchase_invoices') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('xin_edit') . " " . $this->lang->line('ms_purchase_invoices') . $select;
		$data['path_url'] = 'purchase_invoice';
		$data['pi_number'] = $id;

		$records = $this->Purchase_items_model->read_items_po_by_po_number($id);

		// define var
		$discount = 0;
		$tax = 0;
		$subtotal = 0;

		foreach ($records->result() as $r) {
			// count discount
			$discount += $r->discount_rate;

			// count tax
			$tax += $r->tax_rate;

			// count subtotal
			$subtotal += ($r->price * $r->quantity);
		}

		$record->discount = $discount;
		$record->tax = $tax;
		$record->subtotal = $subtotal;
		$record->total = $subtotal - $discount + $tax + $record->delivery_fee;

		$role_resources_ids = $this->Xin_model->user_role_resource();

		if (in_array('511', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_invoices/edit", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_pi()
	{
		$pi_number = $this->input->get('pi_number');
		$data = $this->Purchase_model->read_pi_by_pi_number($pi_number);
		$items = $this->Purchase_items_model->read_items_pi_by_pi_number($pi_number)->result();

		if (!is_null($data) && !is_null($items)) {
			$output = [
				'data' => $data,
				'items' => $items
			];
		} else {
			$output = false;
		}
		echo json_encode($output);
		exit();
	}

	public function update()
	{
		// if ($this->input->is_ajax_request()) {
		if (true) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('vendor_id') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_field');
			} else if ($this->input->post('warehouse_assign') === '') {
				$Return['error'] = $this->lang->line('ms_error_warehouse_assign_field');
			} else if ($this->input->post('faktur_number') === '') {
				$Return['error'] = $this->lang->line('ms_error_faktur_number_field');
			} else if ($this->input->post('date') === '') {
				$Return['error'] = $this->lang->line('ms_error_date_field');
			} else if ($this->input->post('termin') === '') {
				$Return['error'] = $this->lang->line('ms_error_field');
			}


			if (is_null($this->input->post('row_item_name'))) {
				$Return['error'] = $this->lang->line('ms_error_item_empty_data');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
				exit();
			}

			$id = $this->input->post('pi_number');
			$data = array(
				'vendor_id'			=> $this->input->post('vendor'),
				'warehouse_assign'	=> $this->input->post('warehouse_assign'),
				'faktur_number'		=> $this->input->post('faktur_number'),
				'date'				=> $this->input->post('date') ?? date("Y-m-d"),
				'due_date'			=> $this->input->post('due_date') ?? date("Y-m-d"),
				'termin'			=> $this->input->post('select_due_date'),
				'delivery_name'		=> $this->input->post('delivery_name'),
				'delivery_date'		=> $this->input->post('delivery_date'),
				'delivery_number'	=> $this->input->post('delivery_number'),
				'delivery_fee'		=> $this->input->post('delivery_fee') ?? 0,
				'reference'			=> $this->input->post('reference'),
				'notes'				=> $this->input->post('notes'),
				'updated_at'		=> date("Y-m-d H:i:s"),
			);

			$item_update = [];
			$item_insert = [];
			if ($this->input->post('row_item_pi_id')) {
				for ($i = 0; $i < count($this->input->post('row_item_id')); $i++) {
					$type = $this->input->post('row_type');

					if ($type[$i] == "UPDATE") {
						$item_update[] = [
							'item_pi_id'		=> $this->input->post('row_item_pi_id')[$i],
							'product_id' 		=> $this->input->post('row_item_id')[$i],
							'product_name' 		=> $this->input->post('row_item_name')[$i],
							'project_id' 		=> $this->input->post('row_project_id')[$i] ?? null, // override to 0
							'tax_id' 			=> $this->input->post('row_tax_id')[$i] ?? null, //override to 0
							'tax_rate' 			=> $this->input->post('row_tax_rate')[$i],
							'discount_id'		=> $this->input->post('row_discount_id')[$i] ?? null, //override to 0
							'discount_rate'		=> $this->input->post('row_discount_rate')[$i],
							'quantity'			=> $this->input->post('row_qty')[$i],
							'price'				=> $this->input->post('row_item_price')[$i],
							'amount'			=> $this->input->post('row_amount')[$i],
							'updated_at'		=> date("Y-m-d H:i:s"),
						];
						#
					} else if ($type[$i] == "INSERT") {
						$item_insert[] = [
							'pi_number' 		=> $id,
							'product_id' 		=> $this->input->post('row_item_id')[$i],
							'product_name' 		=> $this->input->post('row_item_name')[$i],
							'project_id' 		=> $this->input->post('row_project_id')[$i] ?? null, // override to 0
							'tax_id' 			=> $this->input->post('row_tax_id')[$i] ?? null, //override to 0
							'tax_rate' 			=> $this->input->post('row_tax_rate')[$i],
							'discount_id'		=> $this->input->post('row_discount_id')[$i] ?? null, //override to 0
							'discount_rate'		=> $this->input->post('row_discount_rate')[$i],
							'quantity'			=> $this->input->post('row_qty')[$i],
							'price'				=> $this->input->post('row_item_price')[$i],
							'amount'			=> $this->input->post('row_amount')[$i],
						];
					}
				}
				$update = $this->Purchase_model->update_pi($id, $data, $item_update, $item_insert);
			} else {
				$update = $this->Purchase_model->update_pi($id, $data);
			}

			if ($update) {
				$Return['result'] = $this->lang->line('xin_edited_msg');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}
}
