<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/admin/Purchasing.php';

class Purchase_deliveries extends Purchasing
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		$this->load->model("Tax_model");
		$this->load->model("Exin_model");

		$this->load->model("Contact_model");
		$this->load->model("Product_model");
		$this->load->model("Project_model");
		$this->load->model("Department_model");
		$this->load->model("Purchase_items_model");
		$this->load->model("Purchase_model");
		$this->load->model("Account_trans_model");
	}

	/*Function to set JSON output*/
	public function output($Return = array())
	{
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function pd_number()
	{
		$query = $this->Purchase_model->get_last_pd_number();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->pd_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("PD-%05d", $nextNumericPart);
	}

	public function index()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$selected_po = $this->input->get('id') ?? "PO";
		$po_data = $this->Purchase_model->read_po_by_po_number($selected_po);

		if ($po_data) {
			// jika status po == draft
			if ($po_data->status == 0) {
				$data['po_number'] = $po_data->po_number;
				$record_items = $this->Purchase_items_model->read_items_po_by_po_number($selected_po)->result();
				$data['record_items'] = $record_items;

				foreach ($record_items as $i => $r) {

					$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
					if (!is_null($project)) {
						$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
					} else {
						$project_name = null; // set null to input field
					}
					$product = $this->Xin_model->query("SELECT * FROM ms_products LEFT JOIN ms_measurement_units ON ms_products.uom_id=ms_measurement_units.uom_id WHERE product_id=" . $r->product_id)->row();
					if (!is_null($product)) {
						$uom_name = $product->uom_name;
					} else {
						$uom_name = null; // set null to input field
					}
					$record_items[$i]->project_name = $project_name;
					$record_items[$i]->uom_name = $uom_name;
				}
			} else {
				redirect('admin/purchase_deliveries');
			}
			$select = ' | ' . $po_data->po_number;
			$data['pd_number'] = $this->pd_number();
			$data['record'] = $po_data;
		} else {
			$data['record'] = false;
			$data['po_number'] = null;
			$select = '';
		}

		$data['title'] = $this->lang->line('ms_purchase_deliveries') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_deliveries') . $select;
		$data['path_url'] = 'purchase_delivery';

		$role_resources_ids = $this->Xin_model->user_role_resource();
		// dd($data);
		if (in_array('514', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_deliveries/delivery_list", $data, TRUE);
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
		$user_id = $this->session->userdata()['username']['user_id'] ?? 0;

		// if ($this->input->is_ajax_request()) {
		if (true) {
			$po_number = $this->input->post('po_number');
			$pd_number = $this->input->post('pd_number');

			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('contact_id') === '') {
				$Return['error'] = $this->lang->line('ms_error_contact_field');
			} else if ($this->input->post('warehouse_assign') === '') {
				$Return['error'] = $this->lang->line('ms_error_warehouse_assign_field');
			} else if ($this->input->post('faktur_number') === '') {
				$Return['error'] = $this->lang->line('ms_error_faktur_number_field');
			} else if ($this->input->post('delivery_date') === '') {
				$Return['error'] = $this->lang->line('ms_error_delivery_date_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
				exit();
			}

			$read_pl = $this->Purchase_model->read_pl($po_number, 'po_number');
			if (!is_null($read_pl)) {
				$payment_number = $read_pl->payment_number;
			} else {
				$payment_number = strtoupper(uniqid());
			}

			// inisialisai var trans
			$trans = [];

			$user_id = $this->session->userdata()['username']['user_id'] ?? 0;
			$department_id = $this->session->userdata()['username']['department_id'] ?? 0;

			$delivery_fee = $this->input->post('delivery_fee');
			$data_pd = array(
				'contact_id'		=> $this->input->post('contact_id'),
				'po_number'			=> $po_number,
				'pd_number'			=> $pd_number,
				'added_by'			=> $user_id,
				'department_id'		=> $department_id,
				'warehouse_assign'	=> $this->input->post('warehouse_assign'),
				'date'				=> $this->input->post('date'),
				'faktur_number'		=> $this->input->post('faktur_number'),
				'delivery_date'		=> $this->input->post('delivery_date'),
				'delivery_name'		=> $this->input->post('delivery_name'),
				'delivery_number'	=> $this->input->post('delivery_number'),
				'delivery_fee'		=> $delivery_fee,
				'status'			=> 1,
				'reference'			=> $this->input->post('reference'),
				'attachment'		=> $this->upload_file('delivery'),
				'notes'				=> $this->input->post('notes'),
			);

			$item_insert = [];

			for ($i = 0; $i < count($this->input->post('row_product_id')); $i++) {
				// cari selisih di item product
				// $quantity_reject = $this->input->post('row_original_quantity')[$i] - $this->input->post('row_quantity')[$i];

				$item_insert[] = [
					'pd_number' 		=> $pd_number,
					'product_id' 		=> $this->input->post('row_product_id')[$i],
					'product_name' 		=> $this->input->post('row_product_name')[$i] ?? null,
					'product_number'	=> $this->input->post('row_product_number')[$i] ?? null,
					'project_id' 		=> $this->input->post('row_project_id')[$i],
					'project_name' 		=> $this->input->post('row_project_name')[$i] ?? null,
					'quantity'			=> $this->input->post('row_quantity')[$i],
					'uom_id'			=> $this->input->post('row_uom_id')[$i],
					'uom_name'			=> $this->input->post('row_uom_name')[$i] ?? null,
					// 'quantity_reject'	=> $quantity_reject,
				];

				// $price_total = $this->input->post('row_price')[$i] * $this->input->post('row_quantity')[$i];

				// $tax_type = $this->input->post('row_tax_type')[$i];
				// if ($tax_type == 'percentage') {
				// 	$tax_rate = $tax_type 
				// }

				// $tax_rate = $this->input->post('row_tax_rate')[$i];

				// $discount_type = $this->input->post('row_discount_type')[$i];
				// $discount_rate = $this->input->post('row_discount_rate')[$i];


				// hitung keseluruhan total dan masuk ke inventory
				// $total = $this->input->post('row_uom_name')[$i] * $this->input->post('row_uom_name')[$i];
			}

			$data_po = $this->Purchase_model->read_po_by_po_number($po_number);
			$dp = $this->Account_trans_model->get_purchasing_dp_by_log($read_pl);

			// masukan total item price ke akun inventory
			$trans[] =
				[
					'account_id' => 7, // inventory
					'user_id' => $user_id,
					'account_trans_cat_id' => 6, // = purchase_order
					'amount' => $data_po->amount, //
					'date' => date('Y-m-d'),
					'type' => 'debit',
					'join_id' => $payment_number,
					'ref' => "--",
					'note' => "--",
					'attachment' => null,
				];

			// masukan sisa tagihan ke akun 
			$trans[] =
				[
					'account_id' => 35, // unbiled account payable
					'user_id' => $user_id,
					'account_trans_cat_id' => 6,
					'amount' => $data_po->amount - $dp,
					'date' => date('Y-m-d'),
					'type' => 'credit',
					'join_id' => $payment_number,
					'ref' => "--",
					'note' => "--",
					'attachment' => null,
				];

			// insert data pd
			$insert_pd = $this->Purchase_model->insert_pd($data_pd, $item_insert, $trans);

			// tutup status jadi end
			$this->Purchase_model->update_status_po($po_number, 2);

			//update logs
			$this->Purchase_model->update_pl($po_number, 'po_number', ['pd_number' => $pd_number, 'updated_at' => date("Y-m-d H:i:s")]);
			if ($insert_pd) {

				// tutup pd
				$this->Purchase_model->update_status_pd($po_number, 2);

				$Return['result'] = $this->lang->line('ms_trans_added');
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


		$records = $this->Purchase_model->get_all_pd();

		$data = array();

		foreach ($records->result() as $r) {

			$pd_number = '<a href="' . site_url() . 'admin/purchase_deliveries/view/' . $r->pd_number . '/">' . $r->pd_number . '</a>';

			if (in_array('517', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/purchase_deliveries/edit/' . $r->pd_number  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('518', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->pd_number . '" data-token_type="purchase_deliveries"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			/// get contact
			$contact = $this->Contact_model->get_contact($r->contact_id);
			if (!is_null($contact)) {
				$contact = "<a href='" . site_url() . 'admin/contacts/view/' . $r->contact_id  . "' class='text-md font-weight-bold'>" . $contact->contact_name . "</a><br><small>" . $contact->billing_address . "</small>";
			} else {
				$contact = '--';
			}

			$combhr = $edit . $delete;

			$data[] = array(
				$combhr,
				$pd_number,
				$contact,
				$this->Xin_model->set_date_format($r->date),
				pd_stats($r->status),
				strlen($r->reference) >= 20 ? substr($r->reference, 0, 20) . '...' : $r->reference ?? '--',
				$this->Xin_model->currency_sign($r->delivery_fee),
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

	public function get_ajax_table_items()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$id = $this->uri->segment(4);

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));


		$records = $this->Purchase_items_model->read_items_po_by_po_number($id);
		$data = array();
		foreach ($records->result() as $i => $r) {

			$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
			if (!is_null($project)) {
				$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
			} else {
				$project_name = '--';
			}

			// get product
			$product = $this->Xin_model->query("SELECT * FROM ms_products LEFT JOIN ms_measurement_units ON ms_products.uom_id=ms_measurement_units.uom_id WHERE product_id=" . $r->product_id)->row();
			if (!is_null($product)) {
				$uom_name = $product->uom_name;
			} else {
				$uom_name = '--';
			}
			$data[] = [
				$r->product_name,
				$project_name,
				$r->quantity,
				$uom_name,
			];
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
		$record = $this->Purchase_model->read_pd_by_pd_number($id);
		$data['title'] = $this->lang->line('ms_purchase_deliveries') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_detail') . $this->lang->line('ms_purchase_deliveries') . " " . $record->pd_number;
		if ($record) {
			$data['record'] = $record;
		} else {
			redirect('admin/purchase_deliveries');
		}

		$data['log'] = $this->logs();
		$data['path_url'] = 'purchase_delivery';

		//status button
		$log = $this->get_log('pd_number', $id);
		$data['has_pi'] = is_null($log->pi_number);

		$contact = $this->Contact_model->get_contact($record->contact_id);
		if (!is_null($contact)) {
			$record->contact = "<a href='" . site_url() . 'admin/contacts/view/' . $contact->contact_id  . "' class='text-md font-weight-bold'>" . $contact->contact_name . "</a>";
		} else {
			$record->contact = "--";
		}

		$records = $this->Purchase_items_model->read_items_pd_by_pd_number($id);
		$item = array();

		// define var
		$total_qty = 0;
		foreach ($records->result() as $i => $r) {

			$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
			if (!is_null($project)) {
				$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
			} else {
				$project_name = '--';
			}

			// get product
			$product = $this->Xin_model->query("SELECT * FROM ms_products LEFT JOIN ms_measurement_units ON ms_products.uom_id=ms_measurement_units.uom_id WHERE product_id=" . $r->product_id)->row();
			if (!is_null($product)) {
				$uom_name = $product->uom_name;
			} else {
				$uom_name = '--';
			}
			$item[] = [
				$r->product_name,
				$project_name,
				$r->quantity,
				$uom_name,
			];

			// count total qty
			$total_qty += $r->quantity;
		}

		$record->total_qty = $total_qty;
		$data['records'] = $item;

		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('516', $role_resources_ids) and !is_null($record)) {
			$data['subview'] = $this->load->view("admin/purchase_deliveries/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function print()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_pd_by_pd_number($id);
		$data['title'] = $this->lang->line('ms_purchase_deliveries') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_detail') . $this->lang->line('ms_purchase_deliveries') . " " . $record->pd_number;
		if ($record) {
			$data['record'] = $record;
		} else {
			redirect('admin/purchase_deliveries');
		}

		$contact = $this->Contact_model->get_contact($record->contact_id);
		if (!is_null($contact)) {
			$record->contact = $contact->contact_name;
		} else {
			$record->contact = "--";
		}

		$records = $this->Purchase_items_model->read_items_po_by_po_number($id);
		$item = array();

		// define var
		$total_qty = 0;
		foreach ($records->result() as $i => $r) {

			$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
			if (!is_null($project)) {
				$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
			} else {
				$project_name = '--';
			}

			// get product
			$product = $this->Xin_model->query("SELECT * FROM ms_products LEFT JOIN ms_measurement_units ON ms_products.uom_id=ms_measurement_units.uom_id WHERE product_id=" . $r->product_id)->row();
			if (!is_null($product)) {
				$uom_name = $product->uom_name;
			} else {
				$uom_name = '--';
			}
			$item[] = [
				$r->product_name,
				$project_name,
				$r->quantity,
				$uom_name,
			];

			// count total qty
			$total_qty += $r->quantity;
		}

		$record->total_qty = $total_qty;
		$data['records'] = $item;

		if (!is_null($record)) {
			return $this->load->view("admin/purchase_deliveries/print", $data);
		} else {
			redirect('admin/dashboard');
		}
	}

	public function delete()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		if (in_array(512, $role_resources_ids)) {
			$id = $this->input->post('_token');
			$get = $this->Purchase_model->read_pd_by_pd_number($id);
			$this->delete_file($get->attachment, 'delivery');
			$result = $this->Purchase_model->delete_pd($id);
			if ($result) {
				//update logs
				$this->Purchase_model->update_pl($id, 'pd_number', ['pd_number' => NULL, 'updated_at' => date("Y-m-d H:i:s")]);

				$Return['result'] = $this->lang->line('ms_success_pd_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function edit()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		// $record = $this->Purchase_model->read_pd_by_pd_number($id);
		$data['title'] = $this->lang->line('ms_purchase_deliveries') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('xin_edit') . $this->lang->line('ms_purchase_deliveries') . " " . $id;

		$data['pd_number'] = $id;
		$data['path_url'] = 'purchase_delivery';

		$records = $this->Purchase_items_model->read_items_pd_by_pd_number($id);
		$item = array();

		// define var
		$total_qty = 0;
		foreach ($records->result() as $i => $r) {

			$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
			if (!is_null($project)) {
				$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
			} else {
				$project_name = '--';
			}

			// get product
			$product = $this->Xin_model->query("SELECT * FROM ms_products LEFT JOIN ms_measurement_units ON ms_products.uom_id=ms_measurement_units.uom_id WHERE product_id=" . $r->product_id)->row();
			if (!is_null($product)) {
				$uom_name = $product->uom_name;
			} else {
				$uom_name = '--';
			}
			$item[] = [
				$r->product_name,
				$project_name,
				$r->quantity,
				$uom_name,
			];

			// count total qty
			$total_qty += $r->quantity;
		}

		$data['total_qty'] = $total_qty;
		$data['records'] = $item;



		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('516', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/purchase_deliveries/edit", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_pd()
	{
		$pd_number = $this->input->get('pd_number');
		$data = $this->Purchase_model->read_pd_by_pd_number($pd_number);

		if (!is_null($data)) {
			$output = [
				'data' => $data
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
			$pd_number = $this->input->post('pd_number');

			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('contact_id') === '') {
				$Return['error'] = $this->lang->line('ms_error_contact_field');
			} else if ($this->input->post('warehouse_assign') === '') {
				$Return['error'] = $this->lang->line('ms_error_warehouse_assign_field');
			} else if ($this->input->post('faktur_number') === '') {
				$Return['error'] = $this->lang->line('ms_error_faktur_number_field');
			} else if ($this->input->post('delivery_date') === '') {
				$Return['error'] = $this->lang->line('ms_error_delivery_date_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
				exit();
			}

			$data = array(
				'contact_id'			=> $this->input->post('contact_id'),
				'faktur_number'		=> $this->input->post('faktur_number'),
				'warehouse_assign'	=> $this->input->post('warehouse_assign'),
				'date'				=> $this->input->post('date'),
				'delivery_date'		=> $this->input->post('delivery_date'),
				'delivery_name'		=> $this->input->post('delivery_name'),
				'delivery_number'	=> $this->input->post('delivery_number'),
				'delivery_fee'		=> $this->input->post('delivery_fee'),
				'reference'			=> $this->input->post('reference'),
				'notes'				=> $this->input->post('notes'),
				'updated_at'		=> date("Y-m-d H:i:s"),
			);

			//update logs
			$update = $this->Purchase_model->update_pd($pd_number, $data);
			if ($update) {
				$Return['result'] = $this->lang->line('ms_success_pd_edited');
				$this->output($Return);
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
				$this->output($Return);
			}
		}
	}
}
