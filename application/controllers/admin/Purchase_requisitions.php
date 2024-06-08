<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/admin/Purchasing.php';

class Purchase_requisitions extends Purchasing
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

	public function pr_number()
	{
		$query = $this->Purchase_model->get_last_pr_number();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->pr_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("PR-%05d", $nextNumericPart);
	}

	public function index()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_purchase_requisitions') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_requisitions');
		$data['path_url'] = 'purchase_requisition';
		$data['pr_number'] = $this->pr_number();
		$role_resources_ids = $this->Xin_model->user_role_resource();
		// dd($data);
		if (in_array('501', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_requisitions/requisition_list", $data, TRUE);
				// $data['modal_product'] = $this->load->view("admin/products/add_product");
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
		// if ($this->input->is_ajax_request()) {
		if (true) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('priority_status') === '') {
				$Return['error'] = $this->lang->line('ms_error_priority_status_field');
			} else if ($this->input->post('issue_date') === '') {
				$Return['error'] = $this->lang->line('ms_error_issue_date_field');
			} else if ($this->input->post('due_approval_date') === '') {
				$Return['error'] = $this->lang->line('ms_error_due_approval_date_field');
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

			$pr_number = $this->input->post('pr_number');
			$data_pr = array(
				'pr_number'				=> $pr_number,
				'added_by' 				=> $user_id,
				'department_id'			=> $department_id,
				'issue_date'			=> $this->input->post('issue_date'),
				'due_approval_date'		=> $this->input->post('due_approval_date'),
				'purpose' 				=> $this->input->post('purpose'),
				'priority_status'		=> $this->input->post('priority_status'),
				'purchase_status' 		=> 1, // open
				'attachment'			=> $this->upload_file('requisition'),
				'ref_expedition_name'	=> $this->input->post('ref_expedition_name'),
				'ref_delivery_fee'		=> $this->input->post('ref_delivery_fee'),
				'notes'					=> $this->input->post('notes'),
				'created_at'			=> date("Y-m-d H:i:s"),
			);

			$item_insert = [];

			for ($i = 0; $i < count($this->input->post('row_ref_item')); $i++) {
				$ref_item 			= $this->input->post('row_ref_item');
				$item_name 			= $this->input->post('row_item_name');
				$product_number		= $this->input->post('row_product_number');

				$sub_category_id	= $this->input->post('row_sub_category_id');
				$sub_category_name	= $this->input->post('row_sub_category_name');
				$category_id		= $this->input->post('row_category_id');
				$category_name		= $this->input->post('row_category_name');
				$uom_id				= $this->input->post('row_uom_id');
				$uom_name			= $this->input->post('row_uom_name');

				$project 			= $this->input->post('row_project_id');
				$qty 				= $this->input->post('row_qty');
				$ref_price			= $this->input->post('row_ref_price');
				$amount				= $this->input->post('row_amount');

				$item_insert[] = [
					'item_name' 		=> $item_name[$i],
					'ref_item' 			=> $ref_item[$i],
					'product_number' 	=> $product_number[$i] ?? null,

					'category_id' 		=> $category_id[$i] ?? null,
					'category_name' 	=> $category_name[$i] ?? null,
					'sub_category_id' 	=> $sub_category_id[$i] ?? null,
					'sub_category_name' => $sub_category_name[$i] ?? null,
					'uom_id' 			=> $uom_id[$i] ?? null,
					'uom_name' 			=> $uom_name[$i] ?? null,

					'pr_number' 		=> $pr_number,
					'project_id'		=> $project[$i] ?? null,
					'quantity'			=> $qty[$i],
					'ref_price'			=> $ref_price[$i],
					'amount' 			=> $amount[$i],
					'created_at'		=> date("Y-m-d H:i:s"),
				];
			}

			$insert_pr = $this->Purchase_model->insert_pr($data_pr, $item_insert);

			# add logs
			$payment_number = "PRC" . strtoupper(uniqid());
			$this->Purchase_model->insert_pl(['pr_number' => $pr_number, 'created_at' => $this->input->post('issue_date'), 'payment_number' => $payment_number]);

			if ($insert_pr) {
				$Return['result'] = $this->lang->line('ms_trans_added');
				$this->output($Return);
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
				$this->output($Return);
			}
		}
	}

	public function view()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_pr_by_pr_number($id);
		$data['title'] = $this->lang->line('ms_purchase_requisitions') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_detail') . $this->lang->line('ms_purchase_requisitions') . " " . $record->pr_number;
		if ($record) {

			//get amount
			$amount = $this->Purchase_model->get_amount_pr($record->pr_number)->amount;

			$data['record'] = $record;
		} else {
			redirect('admin/purchase_requisitions');
		}

		$user = $this->Xin_model->read_user_info($record->added_by);
		if (!is_null($user)) {
			$record->added_by = $user[0]->first_name . ' ' . $user[0]->last_name;
			$dep = $this->Department_model->read_department_information($user[0]->department_id);

			if (!is_null($dep)) {
				$record->department = $dep[0]->department_name;
			} else {
				$record->department = '--';
			}
		} else {
			$record->added_by = '--';
			$record->department = '--';
		}

		$records = $this->Purchase_items_model->read_items_pr_by_pr_number($id);
		$item = array();

		if (!is_null($records)) {

			foreach ($records->result() as $r) {

				$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
				if (!is_null($project)) {
					$project_name = $project->title;
				} else {
					$project_name = '--';
				}

				$item_name = $r->item_name . '<br><b style="font-size:10px">' . $r->product_number . '</b>';
				$item[] = array(
					$item_name,
					$project_name,
					$r->quantity,
					$this->Xin_model->currency_sign($r->ref_price),
					$this->Xin_model->currency_sign($r->amount),
				);
			}
		}

		$data['records'] = $item;
		$data['log'] = $this->logs();
		$data['path_url'] = 'purchase_requisition';

		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('503', $role_resources_ids) and !is_null($record)) {
			$data['subview'] = $this->load->view("admin/purchase_requisitions/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
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


		$records = $this->Purchase_model->get_all_pr();

		$data = array();

		foreach ($records->result() as $r) {

			$pr_number = '<a href="' . site_url() . 'admin/purchase_requisitions/view/' . $r->pr_number . '/">' . $r->pr_number . '</a>';

			if (in_array('504', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/purchase_requisitions/edit/' . $r->pr_number  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}

			if (in_array('505', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->pr_number . '" data-token_type="purchase_requisitions"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$user = $this->Xin_model->read_user_info($r->added_by);
			// user full name
			if (!is_null($user)) {
				$full_name = $user[0]->first_name . ' ' . $user[0]->last_name;
				$dep = $this->Department_model->read_department_information($user[0]->department_id);

				if (!is_null($dep)) {
					$department = $dep[0]->department_name;
				} else {
					$department = '--';
				}
			} else {
				$full_name = '--';
				$department = '--';
			}

			//get amount
			$amount = $this->Purchase_model->get_amount_pr($r->pr_number)->amount;

			$combhr = $edit . $delete;

			$data[] = array(
				$combhr,
				$pr_number,
				$full_name, //added by
				$department,
				$this->Xin_model->set_date_format($r->issue_date),
				$r->purpose ?? "--",
				priority_stats($r->priority_status),
				purchase_stats($r->purchase_status),
				"<strong>" . $this->Xin_model->currency_sign($amount + $r->ref_delivery_fee) . "</strong>"
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
		$role_resources_ids = $this->Xin_model->user_role_resource();

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


		$records = $this->Purchase_items_model->read_items_pr_by_pr_number($id);
		// dd($records);
		$data = array();

		if (!is_null($records)) {

			foreach ($records->result() as $i =>  $r) {

				$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
				if (!is_null($project)) {
					$project_name = "<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>";
				} else {
					$project_name = '--';
				}
				// get product
				$product = $this->Xin_model->get_field('ms_products', ['product_name', 'product_number'], 'product_id', $r->ref_item)->row();
				if (!is_null($product)) {
					$item_name = $product->product_name . '<br><b style="font-size:10px">' . $product->product_number . '</b>';
				} else {
					$item_name = $r->item_name;
				}

				$data[] = array(
					$i += 1,
					$item_name,
					$project_name,
					$this->Xin_model->currency_sign($r->ref_price),
					$r->quantity,
					$this->Xin_model->currency_sign($r->amount),
				);
				// dd($this->Xin_model->currency_sign($r->amount));
			}
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

	public function delete()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		if (in_array(505, $role_resources_ids)) {
			$id = $this->input->post('_token');
			$get = $this->Purchase_model->read_pr_by_pr_number($id);
			$this->delete_file($get->attachment, 'requisition');

			$result = $this->Purchase_model->delete_pr($id);
			if ($result) {
				//update logs
				$this->Purchase_model->update_pl($id, 'pr_number', ['pr_number' => NULL, 'updated_at' => date("Y-m-d H:i:s")]);

				$Return['result'] = $this->lang->line('ms_success_pr_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function reject()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		if (in_array(506, $role_resources_ids)) {
			$id = $this->input->post('_token');
			$result = $this->Purchase_model->reject_pr($id);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_purchase_rejected');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}

		$this->output($Return);
	}

	public function print()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_pr_by_pr_number($id);

		$user = $this->Xin_model->read_user_info($record->added_by);
		// user full name
		if (!is_null($user)) {
			$record->added_by = $user[0]->first_name . ' ' . $user[0]->last_name;
			$dep = $this->Department_model->read_department_information($user[0]->department_id);

			if (!is_null($dep)) {
				$record->department = $dep[0]->department_name;
			} else {
				$record->department = '--';
			}
		} else {
			$record->added_by = '--';
			$record->department = '--';
		}

		$data['title'] = $this->lang->line('ms_purchase_requisitions') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_detail') . $this->lang->line('ms_purchase_requisitions') . " " . $record->pr_number;

		$records = $this->Purchase_items_model->read_items_pr_by_pr_number($id);
		$item = array();

		if (!is_null($records)) {

			foreach ($records->result() as $r) {

				$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
				if (!is_null($project)) {
					$project_name = $project->title;
				} else {
					$project_name = '--';
				}

				var_dump($records->result());
				$item_name = $r->product_name . '<br><b style="font-size:10px">' . $r->product_number . '</b>';

				$item[] = array(
					$item_name,
					$project_name,
					$this->Xin_model->currency_sign($r->ref_price),
					$r->quantity,
					$this->Xin_model->currency_sign($r->amount),
				);
			}
		}

		// dd($item);
		if ($record) {
			$data['record'] = $record;
			$data['records'] = $item;
		} else {
			redirect('admin/purchase_requisitions');
		}

		$data['log'] = $this->logs();
		$data['path_url'] = 'purchase_requisition';
		// dd($data);
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('121', $role_resources_ids) and !is_null($record)) {
			return $this->load->view("admin/purchase_requisitions/print", $data);
			// $this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function edit()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_pr_by_pr_number($id);
		$data['title'] = $this->lang->line('ms_purchase_requisitions') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('xin_edit') . " " . $this->lang->line('ms_purchase_requisitions') . " " . $record->pr_number;
		if ($record) {
			$data['record'] = $record;
		} else {
			redirect('admin/purchase_requisitions');
		}

		$user = $this->Xin_model->read_user_info($record->added_by);
		if (!is_null($user)) {
			$record->added_by = $user[0]->first_name . ' ' . $user[0]->last_name;
			$dep = $this->Department_model->read_department_information($user[0]->department_id);

			if (!is_null($dep)) {
				$record->department = $dep[0]->department_name;
			} else {
				$record->department = '--';
			}
		} else {
			$record->added_by = '--';
			$record->department = '--';
		}

		$records = $this->Purchase_items_model->read_items_pr_by_pr_number($id);
		$item = array();

		if (!is_null($records)) {

			foreach ($records->result() as $r) {

				$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();
				if (!is_null($project)) {
					$project_name = $project->title;
				} else {
					$project_name = '--';
				}
				// get product
				$product = $this->Xin_model->get_field('ms_products', ['product_name', 'product_number'], 'product_id', $r->ref_item)->row();
				if (!is_null($product)) {
					$item_name = $product->product_name . '<br><b style="font-size:10px">' . $product->product_number . '</b>';
				} else {
					$item_name = $r->item_name;
				}

				$item[] = array(
					$item_name,
					$project_name,
					$this->Xin_model->currency_sign($r->ref_price),
					$r->quantity,
					$this->Xin_model->currency_sign($r->amount),
				);
			}
		}

		$data['records'] = $item;
		$data['path_url'] = 'purchase_requisition';

		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('504', $role_resources_ids) and !is_null($record)) {
			$data['subview'] = $this->load->view("admin/purchase_requisitions/edit", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function update()
	{
		// if ($this->input->is_ajax_request()) {
		if (true) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('priority_status') === '') {
				$Return['error'] = $this->lang->line('ms_error_priority_status_field');
			} else if ($this->input->post('issue_date') === '') {
				$Return['error'] = $this->lang->line('ms_error_issue_date_field');
			} else if ($this->input->post('due_approval_date') === '') {
				$Return['error'] = $this->lang->line('ms_error_due_approval_date_field');
			}

			//item 

			if ($Return['error'] != '') {
				$this->output($Return);
				exit();
			}

			$id = $this->input->post('pr_number');
			$data = array(
				'issue_date'			=> $this->input->post('issue_date'),
				'due_approval_date'		=> $this->input->post('due_approval_date'),
				'purpose' 				=> $this->input->post('purpose'),
				'priority_status'		=> $this->input->post('priority_status'),
				'ref_expedition_name'	=> $this->input->post('ref_expedition_name'),
				'ref_delivery_fee'		=> $this->input->post('ref_delivery_fee'),
				'notes'					=> $this->input->post('notes'),
				'updated_at'			=> date("Y-m-d H:i:s"),
			);

			$item_insert = [];
			$item_update = [];
			if ($this->input->post('row_ref_item')) {
				for ($i = 0; $i < count($this->input->post('row_ref_item')); $i++) {
					$item_id 			= $this->input->post('row_item_id');
					$ref_item 			= $this->input->post('row_ref_item');
					$item_name 			= $this->input->post('row_item_name');
					$project 			= $this->input->post('row_project_id');

					$sub_category_id	= $this->input->post('row_sub_category_id');
					$sub_category_name	= $this->input->post('row_sub_category_name');
					$category_id		= $this->input->post('row_category_id');
					$category_name		= $this->input->post('row_category_name');
					$uom_id				= $this->input->post('row_uom_id');
					$uom_name			= $this->input->post('row_uom_name');

					$qty 				= $this->input->post('row_qty');
					$ref_price			= $this->input->post('row_ref_price');
					$amount				= $this->input->post('row_amount');
					$type				= $this->input->post('row_type');

					if ($type[$i] == "UPDATE") {
						$item_update[] = [
							'item_pr_id' 		=> $item_id[$i],
							'ref_item' 			=> $ref_item[$i] ?? null,
							'item_name' 		=> $item_name[$i],

							'category_id' 		=> $category_id[$i] ?? null,
							'category_name' 	=> $category_name[$i] ?? null,
							'sub_category_id' 	=> $sub_category_id[$i] ?? null,
							'sub_category_name' => $sub_category_name[$i] ?? null,
							'uom_id' 			=> $uom_id[$i] ?? null,
							'uom_name' 			=> $uom_name[$i] ?? null,

							'project_id'		=> $project[$i] ?? null,
							'quantity'			=> $qty[$i],
							'ref_price'			=> $ref_price[$i],
							'amount' 			=> $amount[$i],
							'updated_at' 		=> date("Y-m-d H:i:s")
						];
						#
					} else if ($type[$i] == "INSERT") {
						$item_insert[] = [
							'pr_number' 		=> $id,
							'ref_item' 			=> $ref_item[$i] ?? null,
							'item_name' 		=> $item_name[$i],

							'category_id' 		=> $category_id[$i] ?? null,
							'category_name' 	=> $category_name[$i] ?? null,
							'sub_category_id' 	=> $sub_category_id[$i] ?? null,
							'sub_category_name' => $sub_category_name[$i] ?? null,
							'uom_id' 			=> $uom_id[$i] ?? null,
							'uom_name' 			=> $uom_name[$i] ?? null,

							'pr_number' 		=> $id,
							'project_id'		=> $project[$i] ?? null,
							'quantity'			=> $qty[$i],
							'ref_price'			=> $ref_price[$i],
							'amount' 			=> $amount[$i],
							'created_at'		=> date("Y-m-d H:i:s"),
						];
					}
				}

				$update = $this->Purchase_model->update_pr($id, $data, $item_update, $item_insert);
			} else {
				$update = $this->Purchase_model->update_pr($id, $data);
			}

			if ($update) {
				$Return['result'] = $this->lang->line('xin_edited_msg');
				$this->output($Return);
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
				$this->output($Return);
			}
		}
	}

	public function get_ajax_pr()
	{
		$pr_number = $this->input->get('pr_number');
		$pr_data = $this->Purchase_model->read_pr_by_pr_number($pr_number);
		$pr_items = $this->Purchase_items_model->read_items_pr_by_pr_number($pr_number)->result();

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
}
