<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/admin/Purchasing.php';

class Purchase_orders extends Purchasing
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
		$this->load->model("Account_transactions_model");
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

	public function po_number()
	{
		$query = $this->Purchase_model->get_last_po_number();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->po_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("PO-%05d", $nextNumericPart);
	}

	public function index()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$selected_pr = $this->input->get('id') ?? "PR";
		$pr_data = $this->Purchase_model->read_pr_by_pr_number($selected_pr);
		if ($pr_data) {
			if ($pr_data->purchase_status == 1) {
				$data['pr_number'] = $pr_data->pr_number;
				$data['ref_expedition_name'] = $pr_data->ref_expedition_name;
			} else {
				redirect('admin/purchase_orders');
			}
			$select = ' | ' . $pr_data->pr_number;
		} else {
			$data['pr_number'] = false;
			$data['ref_expedition_name'] = null;
			$select = '';
		}

		$data['title'] = $this->lang->line('ms_purchase_orders') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_orders') . $select;
		$data['path_url'] = 'purchase_order';
		$data['po_number'] = $this->po_number();

		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('507', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_orders/order_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_pr()
	{
		$pr_number = $this->input->get('pr_number');
		$pr_data = $this->Purchase_model->read_pr_by_pr_number($pr_number);
		$pr_items = $this->Purchase_items_model->read_items_pr_by_pr_number($pr_number)->result();

		// dd($pr_data);
		if (!is_null($pr_data) && !is_null($pr_items)) {
			$output = [
				'data' => $pr_data,
				'items' => $pr_items
			];
		} else {
			$output = false;
		}
		echo json_encode($output);
		exit();
	}

	public function insert()
	{
		// Get the input data
		$pr_number = $this->input->post('pr_number');
		$po_number = $this->input->post('po_number');

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
				// } else if ($this->input->post('faktur_number') === '') {
				// 	$Return['error'] = $this->lang->line('ms_error_faktur_number_field');
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

			// inisialisasi var trans
			$trans = [];

			$read_pl = $this->Purchase_model->read_pl($pr_number, 'pr_number');
			if (!is_null($read_pl)) {
				$payment_number = $read_pl->payment_number;
			} else {
				$payment_number = strtoupper(uniqid());
			}

			$data_po = array(
				'vendor_id'			=> $this->input->post('vendor'),
				'po_number'			=> $po_number,
				'added_by'			=> $user_id,
				'department_id'		=> $department_id,
				'warehouse_assign'	=> $this->input->post('warehouse_assign'),
				'faktur_number'		=> $this->input->post('faktur_number') ?? "--",
				'date'				=> $this->input->post('date') ?? date("Y-m-d"),
				'due_date'			=> $this->input->post('due_date') ?? date("Y-m-d"),
				'termin'			=> $this->input->post('select_due_date'),
				'delivery_name'		=> $this->input->post('delivery_name'),
				'delivery_fee'		=> $this->input->post('delivery_fee'),
				'service_fee'		=> $this->input->post('service_fee'),
				'status'			=> 0,
				'reference'			=> $this->input->post('reference'),
				'attachment'		=> $this->upload_file('order'),
				'notes'				=> $this->input->post('notes'),
			);

			if (!is_null($this->input->post('down_payment_account')) and !is_null($this->input->post('down_payment'))) {

				// kredit akun yang digunakan untuk dp
				$trans[] =
					[
						'account_id' => $this->input->post('down_payment_account'), // account_id trade payable
						'user_id' => $user_id,
						'account_trans_cat_id' => 6, // = purchase_order
						'amount' => $this->input->post('down_payment'),
						'date' => date('Y-m-d'),
						'type' => 'credit',
						'join_id' => $payment_number, // po number
						'ref' => "Purchase Order",
						'note' => "DP Purchase Order",
						'attachment' => null,
					];

				// debit akun yang digunakan untuk menampung dp
				$trans[] =
					[
						'account_id' => 12, // account_id prepaid expenses
						'user_id' => $user_id,
						'account_trans_cat_id' => 6, // = purchase_order
						'amount' => $this->input->post('down_payment'),
						'date' => date('Y-m-d'),
						'type' => 'debit',
						'join_id' => $payment_number, // po number
						'ref' => "Purchase Order",
						'note' => "DP Purchase Order",
						'attachment' => null,
					];
			}

			$item_insert = [];

			$sub_total = 0;
			$tax = 0;
			$discount = 0;
			$delivery_fee = $this->input->post('delivery_fee');
			$service_fee = $this->input->post('service_fee');
			$amount_item = 0;

			for ($i = 0; $i < count($this->input->post('row_item_id')); $i++) {

				$sub_total += $this->input->post('row_amount')[$i] ?? 0;
				$tax += $this->input->post('row_tax_rate')[$i] ?? 0;
				$discount += $this->input->post('row_discount_rate')[$i] ?? 0;

				$tax_id = $this->input->post('row_tax_id')[$i] ?? null;
				$product_name = $this->input->post('row_item_name')[$i];

				$amount_item += $this->input->post('row_qty')[$i] * $this->input->post('row_item_price')[$i];

				$item_insert[] = [
					'po_number' 		=> $po_number,
					'product_id' 		=> $this->input->post('row_item_id')[$i],
					'product_name' 		=> $product_name,
					'product_number'	=> $this->input->post('row_product_number')[$i],

					'sub_category_id' 	=> $this->input->post('row_sub_category_id')[$i] ?? null, // override to null
					'sub_category_name' => $this->input->post('row_sub_category_name')[$i] ?? null, // override to null
					'category_id' 		=> $this->input->post('row_category_id')[$i] ?? null, // override to null
					'category_name' 	=> $this->input->post('row_category_name')[$i] ?? null, // override to null
					'uom_id' 			=> $this->input->post('row_uom_id')[$i] ?? null, // override to null
					'uom_name' 			=> $this->input->post('row_uom_name')[$i] ?? null, // override to null
					'project_id' 		=> $this->input->post('row_project_id')[$i] ?? null, // override to null
					'tax_id' 			=> $tax_id, //override to null
					'tax_type' 			=> $this->input->post('data_tax_type')[$i] ?? 'fixed',
					'tax_rate' 			=> $this->input->post('row_tax_rate')[$i] ?? 0,
					'discount_id'		=> $this->input->post('row_discount_id')[$i] ?? null, //override to null
					'discount_type'		=> $this->input->post('data_discount_type')[$i] ?? 0,
					'discount_rate'		=> $this->input->post('row_discount_rate')[$i] ?? 0,
					'quantity'			=> $this->input->post('row_qty')[$i],
					'price'				=> $this->input->post('row_item_price')[$i],
					'amount'			=> $this->input->post('row_amount')[$i],
				];
			}

			// // masukan semua total purchasing ke akun Trade Payble = credit
			// $trans[] =
			// 	[
			// 		'account_id' => 34, // account_id trade payable
			// 		'user_id' => $user_id,
			// 		'account_trans_cat_id' => 6, // = purchase_order
			// 		'amount' => $sub_total + $tax - $discount + $delivery_fee + $service_fee,
			// 		'date' => date('Y-m-d'),
			// 		'type' => 'credit',
			// 		'join_id' => $po_number, // po number
			// 		'ref' => "Purchase Order",
			// 		'note' => "Begin Purchase Order",
			// 		'attachment' => null,
			// 	];

			// // masukan semua total only item ke akun cost of sales
			// $trans[] =
			// 	[
			// 		'account_id' => 64, // account_id cost of sales
			// 		'user_id' => $user_id,
			// 		'account_trans_cat_id' => 6, // = purchase_order
			// 		'amount' => $amount_item,
			// 		'date' => date('Y-m-d'),
			// 		'type' => 'debit',
			// 		'join_id' => $po_number, // po number
			// 		'ref' => "Purchase Order",
			// 		'note' => "Begin Purchase Order",
			// 		'attachment' => null,
			// 	];

			// // otomatis tercatat di akun Inventory
			// $trans[] =
			// 	[
			// 		'account_id' => 7,
			// 		'user_id' => $user_id,
			// 		'account_trans_cat_id' => 6, // = purchase_order
			// 		'amount' => $sub_total + $tax - $discount + $delivery_fee + $service_fee,
			// 		'date' => date('Y-m-d'),
			// 		'type' => 'debit',
			// 		'join_id' => $po_number, // po number
			// 		'ref' => "Purchase Order",
			// 		'note' => "Begin Purchase Order",
			// 		'attachment' => null,
			// 	];

			// if ($tax > 0) {
			// 	// masukan tax total ke akun VAT In
			// 	$trans[] =
			// 		[
			// 			'account_id' => 14,
			// 			'user_id' => $user_id,
			// 			'account_trans_cat_id' => 6, // = purchase_order
			// 			'amount' => $tax,
			// 			'date' => date('Y-m-d'),
			// 			'type' => 'debit',
			// 			'join_id' => $po_number, // po number
			// 			'ref' => "Tax from",
			// 			'note' => "Tax from Purchase Order",
			// 			'attachment' => null,
			// 		];
			// }

			// if ($discount > 0) {
			// 	// masukan discount total ke akun Purchase Discounts => credit
			// 	$trans[] =
			// 		[
			// 			'account_id' => 65,
			// 			'user_id' => $user_id,
			// 			'account_trans_cat_id' => 6, // = purchase_order
			// 			'amount' => $discount,
			// 			'date' => date('Y-m-d'),
			// 			'type' => 'credit',
			// 			'join_id' => $po_number, // po number
			// 			'ref' => "Discount from",
			// 			'note' => "Discount from Purchase Order",
			// 			'attachment' => null,
			// 		];
			// }

			// // masukan biaya layanan ke akun Commision and Fee
			// if ($service_fee > 0) {
			// 	$trans[] =
			// 		[
			// 			'account_id' => 71,
			// 			'user_id' => $user_id,
			// 			'account_trans_cat_id' => 6, // = purchase_order
			// 			'amount' => $service_fee,
			// 			'date' => date('Y-m-d'),
			// 			'type' => 'debit',
			// 			'join_id' => $po_number, // po number
			// 			'ref' => "Service fee",
			// 			'note' => "Service fee from Purchase Order",
			// 			'attachment' => null,
			// 		];
			// }

			// // masukan biaya delivery ke akun Shipping/Freight & Delivery
			// if ($delivery_fee > 0) {
			// 	$trans[] =
			// 		[
			// 			'account_id' => 67,
			// 			'user_id' => $user_id,
			// 			'account_trans_cat_id' => 6, // = purchase_order
			// 			'amount' => $delivery_fee,
			// 			'date' => date('Y-m-d'),
			// 			'type' => 'debit',
			// 			'join_id' => $po_number, // po number
			// 			'ref' => "Delivery fee",
			// 			'note' => "Delivery fee from Purchase Order",
			// 			'attachment' => null,
			// 		];
			// }

			// insert data
			$insert_po = $this->Purchase_model->insert_po($data_po, $item_insert, $trans);

			if (!is_null($read_pl)) {
				//update logs
				$this->Purchase_model->update_pl($pr_number, 'pr_number', ['po_number' => $po_number, 'updated_at' => date("Y-m-d H:i:s")]);
			} else {
				# add logs
				$pl = ['po_number' => $po_number, 'payment_number' => $payment_number];
				$this->Purchase_model->insert_pl($pl);
			}

			if ($insert_po) {
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

		$records = $this->Purchase_model->get_all_po();

		$data = array();

		foreach ($records->result() as $r) {

			$po_number = '<a href="' . site_url() . 'admin/purchase_orders/view/' . $r->po_number . '/">' . $r->po_number . '</a>';

			if (in_array('511', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/purchase_orders/edit/' . $r->po_number  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('512', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->po_number . '" data-token_type="purchase_orders"><span class="fas fa-trash-restore"></span></button></span>';
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

			//get amount
			$amount = $this->Purchase_model->get_amount_po($r->po_number)->amount;

			$combhr = $edit . $delete;

			$data[] = array(
				$combhr,
				$po_number,
				$vendor,
				$this->Xin_model->set_date_format($r->date),
				po_stats($r->status),
				strlen($r->reference) >= 20 ? substr($r->reference, 0, 20) . '...' : $r->reference ?? '--',
				$this->Xin_model->currency_sign($amount + $r->delivery_fee),
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
		$record = $this->Purchase_model->read_po_by_po_number($id);
		$log = $this->Purchase_model->read_po_by_po_number($id);
		if ($record) {
			$data['po_number'] = $record->po_number;
			$data['record'] = $record;
			$select = ' | ' . $record->po_number;

			// $data['payment'] = $this->Account_trans_model->get_payment(6, $record->po_number, 34); // 34 adalah akun trade payable
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
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_orders') . $select;
		$data['path_url'] = 'purchase_order';
		$data['po_number'] = $id;
		$data['log'] = $this->logs();

		//status button
		$log = $this->get_log('po_number', $id);
		$data['has_pd'] = is_null($log->pd_number);
		$data['has_pi'] = is_null($log->pi_number);

		$data['payment'] = $this->Account_trans_model->get_purchasing_by_log($log);
		// $as = $this->Account_trans_model->get_purchasing_dp_by_log($log);

		$records = $this->Purchase_items_model->read_items_po_by_po_number($id);
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

			// $satuan_diskon = "";
			if ($r->discount_type == 1) { //1 = percentage, 0 = fixed
				$satuan_diskon = "<br><small style='font-size:10px'>@" . $this->Xin_model->currency_sign($r->discount_rate / $r->quantity) . "</small>";
			} else {
				$satuan_diskon = "";
			}

			if ($r->tax_type == 'percentage') {
				$satuan_tax = "<br><small style='font-size:10px'>@" . $this->Xin_model->currency_sign($r->tax_rate / $r->quantity) . "</small>";
			} else {
				$satuan_tax = "";
			}

			$item_name = $r->product_name . '<br><b style="font-size:10px">' . $r->product_number . '</b>';
			$item[] = array(
				$item_name,
				$project_name,
				$r->quantity,
				$this->Xin_model->currency_sign($r->price),
				$this->Xin_model->currency_sign($r->discount_rate) . $satuan_diskon,
				$this->Xin_model->currency_sign($r->tax_rate) . $satuan_tax,
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
		$record->total = ($subtotal - $discount + $tax + $record->delivery_fee + $record->service_fee) - $data['payment']->jumlah_dibayar;

		$data['records'] = $item;
		$role_resources_ids = $this->Xin_model->user_role_resource();

		if (in_array('510', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_orders/view", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
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

		foreach ($records->result() as $r) {
			$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
			if (!is_null($project)) {
				$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
			} else {
				$project_name = '--';
			}

			// get product
			$product = $this->Xin_model->get_field('ms_products', ['product_name', 'product_number'], 'product_id', $r->product_id)->row();
			if (!is_null($product)) {
				$item_name = $r->product_name . '<br><b style="font-size:10px">' . $product->product_number . '</b>';
			} else {
				$item_name = $r->product_name;
			}

			$data[] = array(
				// $r->product_name . '<br><b style="font-size:10px">' . $r->product_number . '</b>',
				$item_name,
				$project_name,
				$this->Xin_model->currency_sign($r->tax_rate),
				$this->Xin_model->currency_sign($r->discount_rate),
				$r->quantity,
				$this->Xin_model->currency_sign($r->price),
				$this->Xin_model->currency_sign($r->amount),
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

	public function print()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_po_by_po_number($id);
		if ($record) {
			$data['po_number'] = $record->po_number;
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
		$data['po_number'] = $id;

		// define var
		$discount = 0;
		$tax = 0;
		$subtotal = 0;

		$records = $this->Purchase_items_model->read_items_po_by_po_number($id);
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
		}

		$record->discount = $discount;
		$record->tax = $tax;
		$record->subtotal = $subtotal;
		$record->total = $subtotal - $discount + $tax + $record->delivery_fee;

		$data['records'] = $item;
		if (!empty($session)) {
			return $this->load->view("admin/purchase_orders/print", $data);
		} else {
			redirect('admin/');
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

			$get = $this->Purchase_model->read_po_by_po_number($id);
			$this->delete_file($get->attachment, 'order');
			$result = $this->Purchase_model->delete_po($id);
			if ($result) {
				//update logs
				$this->Purchase_model->update_pl($id, 'po_number', ['po_number' => NULL, 'updated_at' => date("Y-m-d H:i:s")]);
				$Return['result'] = $this->lang->line('ms_success_po_deleted');
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
		$record = $this->Purchase_model->read_po_by_po_number($id);
		if ($record) {
			$data['po_number'] = $record->po_number;
			$data['record'] = $record;
			$select = ' | ' . $record->po_number;
		} else {
			redirect('admin/purchase_orders');
		}

		$data['title'] = $this->lang->line('ms_purchase_orders') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_orders') . $select;
		$data['path_url'] = 'purchase_order';
		$data['po_number'] = $id;

		$records = $this->Purchase_items_model->read_items_po_by_po_number($id);
		$item = array();

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

		$data['records'] = $item;
		$role_resources_ids = $this->Xin_model->user_role_resource();

		if (in_array('511', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_orders/edit", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_po()
	{
		$po_number = $this->input->get('po_number');
		$data = $this->Purchase_model->read_po_by_po_number($po_number);
		$items = $this->Purchase_items_model->read_items_po_by_po_number($po_number)->result();

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
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('vendor_id') === '') {
				$Return['error'] = $this->lang->z('ms_error_vendor_field');
			} else if ($this->input->post('warehouse_assign') === '') {
				$Return['error'] = $this->lang->line('ms_error_warehouse_assign_field');
			} else if ($this->input->post('faktur_number') === '') {
				$Return['error'] = $this->lang->line('ms_error_faktur_number_field');
			} else if ($this->input->post('date') === '') {
				$Return['error'] = $this->lang->line('ms_error_date_field');
			} else if ($this->input->post('termin') === '') {
				$Return['error'] = $this->lang->line('ms_error_field');
			}

			//item 
			if ($Return['error'] != '') {
				$this->output($Return);
				exit();
			}

			$id = $this->input->post('po_number');
			$data = array(
				'vendor_id'			=> $this->input->post('vendor'),
				'warehouse_assign'	=> $this->input->post('warehouse_assign'),
				'faktur_number'		=> $this->input->post('faktur_number') ?? "--",
				'date'				=> $this->input->post('date') ?? date("Y-m-d"),
				'due_date'			=> $this->input->post('due_date') ?? date("Y-m-d"),
				'termin'			=> $this->input->post('select_due_date'),
				'delivery_name'		=> $this->input->post('delivery_name'),
				'delivery_fee'		=> $this->input->post('delivery_fee'),
				'reference'			=> $this->input->post('reference'),
				'notes'				=> $this->input->post('notes'),
				'updated_at' 		=> date("Y-m-d H:i:s")
			);
			// dd($this->input->post());

			$item_update = [];
			$item_insert = [];
			if ($this->input->post('row_item_po_id')) {
				for ($i = 0; $i < count($this->input->post('row_item_po_id')); $i++) {

					$type = $this->input->post('row_type');

					if ($type[$i] == "UPDATE") {
						$item_update[] = [
							'item_po_id' 		=> $this->input->post('row_item_po_id')[$i],
							'product_id' 		=> $this->input->post('row_item_id')[$i],
							'product_name' 		=> $this->input->post('row_item_name')[$i],

							'product_number'	=> $this->input->post('row_product_number')[$i],

							'sub_category_id' 	=> $this->input->post('row_sub_category_id')[$i] ?? null, // override to null
							'sub_category_name' => $this->input->post('row_sub_category_name')[$i] ?? null, // override to null
							'category_id' 		=> $this->input->post('row_category_id')[$i] ?? null, // override to null
							'category_name' 	=> $this->input->post('row_category_name')[$i] ?? null, // override to null
							'uom_id' 			=> $this->input->post('row_uom_id')[$i] ?? null, // override to null
							'uom_name' 			=> $this->input->post('row_uom_name')[$i] ?? null, // override to null

							'project_id' 		=> $this->input->post('row_project_id')[$i] ?? null, // override to null
							'tax_id' 			=> $this->input->post('row_tax_id')[$i] ?? null, //override to null
							'tax_rate' 			=> $this->input->post('row_tax_rate')[$i],
							'discount_id'		=> $this->input->post('row_discount_id')[$i] ?? null, //override to null
							'discount_rate'		=> $this->input->post('row_discount_rate')[$i],
							'quantity'			=> $this->input->post('row_qty')[$i],
							'price'				=> $this->input->post('row_item_price')[$i],
							'amount'			=> $this->input->post('row_amount')[$i],
							'updated_at' 		=> date("Y-m-d H:i:s")

						];
					} else if ($type[$i] == "INSERT") {
						$item_insert[] = [
							'po_number' 		=> $id,
							'product_id' 		=> $this->input->post('row_item_id')[$i],
							'product_name' 		=> $this->input->post('row_item_name')[$i],
							'product_number'	=> $this->input->post('row_product_number')[$i],

							'sub_category_id' 	=> $this->input->post('row_sub_category_id')[$i] ?? null, // override to null
							'sub_category_name' => $this->input->post('row_sub_category_name')[$i] ?? null, // override to null
							'category_id' 		=> $this->input->post('row_category_id')[$i] ?? null, // override to null
							'category_name' 	=> $this->input->post('row_category_name')[$i] ?? null, // override to null
							'uom_id' 			=> $this->input->post('row_uom_id')[$i] ?? null, // override to null
							'uom_name' 			=> $this->input->post('row_uom_name')[$i] ?? null, // override to null

							'project_id' 		=> $this->input->post('row_project_id')[$i] ?? null, // override to null
							'tax_id' 			=> $this->input->post('row_tax_id')[$i] ?? null, //override to null
							'tax_rate' 			=> $this->input->post('row_tax_rate')[$i],
							'discount_id'		=> $this->input->post('row_discount_id')[$i] ?? null, //override to null
							'discount_rate'		=> $this->input->post('row_discount_rate')[$i],
							'quantity'			=> $this->input->post('row_qty')[$i],
							'price'				=> $this->input->post('row_item_price')[$i],
							'amount'			=> $this->input->post('row_amount')[$i],
						];
					}
				}
				$update = $this->Purchase_model->update_po($id, $data, $item_update, $item_insert);
			} else {
				$update = $this->Purchase_model->update_po($id, $data);
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
