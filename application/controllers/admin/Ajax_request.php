<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajax_request extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		// $this->load->model("Complaints_model");
		$this->load->model("Tax_model");
		// $this->load->model("Product_categories_model");
		$this->load->model("Employees_model");
		$this->load->model("Exin_model");

		$this->load->model("Vendor_model");
		$this->load->model("Product_model");
		$this->load->model("Project_model");
		$this->load->model("Project_costs_model");
		$this->load->model("Purchase_items_model");
		$this->load->model("Accounts_model");

		// if (!$this->input->is_ajax_request()) {
		// 	$this->output([
		// 		'error' => 403
		// 	]);
		// 	exit('No direct script access allowed');
		// }
	}

	/*Function to set JSON output*/
	public function output($Return = array())
	{
		$output = array('status' => 'success', 'data' => []);

		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");

		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function get_taxs()
	{
		$taxs = $this->Tax_model->get_taxes()->result();
		$data = [];
		foreach ($taxs as $i => $tax) {
			if ($tax->type == "fixed") {
				$text = $this->Xin_model->currency_sign($tax->rate);
			} else {
				$text = $tax->rate . "%";
			}
			$taxs[$i]->name = $tax->name . " ($text)";

			// $for = new stdClass();
			// $for->tax_id = $tax->tax_id;
			// $for->name = $tax->name . " ($text)";
			// $for->rate = $tax->rate;
			// $for->type = $tax->type;

			// $data[] = $for;
		}
		echo $this->output($taxs);
		exit();
	}

	public function get_discounts()
	{
		$res = $this->Xin_model->get_all_discounts()->result();
		foreach ($res as $i => $r) {
			if ($r->discount_type == 0) {
				$text = $this->Xin_model->currency_sign($r->discount_value);
			} else {
				$text = $r->discount_value . "%";
			}
			$res[$i]->discount_name = $r->discount_name . " ($text)";
		}
		echo $this->output($res);
		exit();
	}

	public function get_projects()
	{
		$completed = $this->input->get('completed');
		$res = $this->Project_model->get_all_projects($completed);

		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->project_id,
				'value' => $r->title,
			);
		}
		// dd($data);
		echo $this->output($data);
		exit();
	}

	public function get_products()
	{
		if ($this->input->get('query')) {
			$query = $this->input->get('query');
		} else {
			$query = false;
		}
		$res = $this->Product_model->gel_all_product($query)->result();

		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->product_id,
				'label' => $r->product_name,
				'price' => $r->price,
				'product_id' => $r->product_id,
				'sub_category_id' => $r->sub_category_id,
				'product_number' => $r->product_number,
				'uom_id' => $r->uom_id,
			);
		}
		echo $this->output($data);
		exit();
	}

	public function get_accounts()
	{
		if ($this->input->get('query')) {
			$query = $this->input->get('query');
		} else {
			$query = false;
		}
		$res = $this->Accounts_model->gel_all_account($query)->result();

		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->account_id,
				'value' => $r->account_code . " - " . $r->account_name
			);
		}
		echo $this->output($data);
		exit();
	}
	// public function get_products()
	// {
	// 	if ($this->input->get('query')) {
	// 		$query = $this->input->get('query');
	// 	} else {
	// 		$query = false;
	// 	}
	// 	$res = $this->Product_model->gel_all_product($query)->result();

	// 	$data = [];
	// 	foreach ($res as $key => $r) {
	// 		$data[] = array(
	// 			'id' => $r->product_id,
	// 			'label' => $r->product_name,
	// 			'price' => $r->price,
	// 			'product_id' => $r->product_id,
	// 			'sub_category_id' => $r->sub_category_id,
	// 			'product_number' => $r->product_number,
	// 			'uom_id' => $r->uom_id,
	// 		);
	// 	}
	// 	echo $this->output($data);
	// 	exit();
	// }

	public function find_vendor()
	{
		$query = $this->input->get('query');
		$res = $this->Vendor_model->find_vendor($query);
		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->vendor_id,
				'text' => $r->vendor_name,
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_product()
	{
		$query = $this->input->get('query');
		$res = $this->Product_model->find_product($query);
		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->product_id,
				'text' => $r->product_name,
				'number' => $r->product_number,
				'price' => $r->price,
				// 'sub_category_id' => $r->sub_category_id,
				// 'sub_category_name' => $r->sub_category_name,
				// 'category_id' => $r->category_id,
				// 'category_name' => $r->category_name,
				// 'uom_id' => $r->uom_id,
				// 'uom_name' => $r->uom_name,
			);
		}
		echo $this->output($data);
		exit();
	}



	public function get_ajax_table()
	{

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();

		$records = $this->accounts_model->all();
		// dd($records);
		$output = array(
			"draw" => $draw,
			// "recordsTotal" => $records->num_rows(),
			// "recordsFiltered" => $records->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}


	public function find_product_by_id()
	{
		$query = $this->input->get('query');
		$res = $this->Product_model->find_product_by_id($query);
		echo $this->output($res);
		exit();
	}

	public function find_project()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_project($query);
		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->project_id,
				'text' => $r->title,
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_tax()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_tax($query);
		$data = [];

		foreach ($res as $key => $r) {
			if ($r->type == "fixed") {
				$text = $this->Xin_model->currency_sign($r->rate);
			} else {
				$text = $r->rate . "%";
			}

			$data[] = array(
				'id' => $r->tax_id,
				'text' => $r->name . " ($text)",
				'rate' => $r->rate,
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_tax_by_id()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_tax_by_id($query);
		echo $this->output($res);
		exit();
	}
	public function find_project_by_id()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_project_by_id($query);
		echo $this->output($res);
		exit();
	}


	public function find_discount()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_discount($query);
		$data = [];

		foreach ($res as $key => $r) {
			if ($r->discount_type == 0) {
				$text = $this->Xin_model->currency_sign($r->discount_value);
			} else {
				$text = $r->discount_value . "%";
			}

			$data[] = array(
				'id' => $r->discount_id,
				'text' => $r->discount_name . " ($text)",
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_discount_by_id()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_discount_by_id($query);
		echo $this->output($res);
		exit();
	}

	public function find_vendor_by_id()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_vendor_by_id($query);
		echo $this->output($res);
		exit();
	}

	public function delete_item_pr()
	{

		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$id = $this->input->post('id');
		$type = $this->input->post('type') ?? "";

		if ($type == 'pr_number') {
			$del = $this->Purchase_items_model->delete_item_pr_by_pr_number($id);
		} else {
			$del = $this->Purchase_items_model->delete_item_pr_by_id($id);
		}

		if ($del) {
			$Return['result'] = $this->lang->line('ms_item_deleted');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}

		$this->output($Return);
		exit;
	}

	public function delete_item_po()
	{
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$id = $this->input->post('id');
		$type = $this->input->post('type') ?? "";

		if ($type == 'po_number') {
			$del = $this->Purchase_items_model->delete_item_po_by_po_number($id);
		} else {
			$del = $this->Purchase_items_model->delete_item_po_by_id($id);
		}

		if ($del) {
			$Return['result'] = $this->lang->line('ms_item_deleted');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}

		$this->output($Return);
		exit;
	}

	public function delete_item_pi()
	{
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$id = $this->input->post('id');
		$type = $this->input->post('type') ?? "";

		if ($type == 'po_number') {
			$del = $this->Purchase_items_model->delete_item_pi_by_pi_number($id);
		} else {
			$del = $this->Purchase_items_model->delete_item_pi_by_id($id);
		}

		if ($del) {
			$Return['result'] = $this->lang->line('ms_item_deleted');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}

		$this->output($Return);
		exit;
	}
}
