<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_categories extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		$this->load->model("Xin_model");
		$this->load->model("Product_model");
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

	public function index()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_product_categories') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_product_categories');

		$data['path_url'] = 'product_categories';
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('486', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/product_categories/category_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}


	// Validate and add info in database
	public function create()
	{

		if ($this->input->post('type') == 'create') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('category_name') === '') {
				$Return['error'] = $this->lang->line('ms_error_product_category_name_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'category_name' => $this->input->post('category_name'),
				'created_at' => date('d-m-Y h:i:s')
			);

			$result = $this->Xin_model->add_product_category($data);
			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('ms_product_category_added');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
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


		$constant = $this->Xin_model->get_all_product_categories();

		$data = array();

		foreach ($constant->result() as $r) {

			if (in_array('488', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_id="' . $r->category_id . '" data-field_type="product_categories"><span class="fas fa-pencil-alt"></span></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('489', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->category_id . '" data-token_type="product_categories"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$combhr = $edit . $delete;

			$data[] = array(
				$combhr,
				$r->category_name,
				$r->created_at,
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $constant->num_rows(),
			"recordsFiltered" => $constant->num_rows(),
			"data" => $data
		);

		echo json_encode($output);
		exit();
	}


	public function read_product_categories()
	{
		$data['title'] = $this->Xin_model->site_title();
		$id = $this->input->get('field_id');
		$result = $this->Xin_model->read_product_category($id);
		$data = array(
			'category_id' => $result[0]->category_id,
			'category_name' => $result[0]->category_name,
		);
		$session = $this->session->userdata('username');
		if (!empty($session)) {
			$this->load->view('admin/product_categories/dialog_category', $data);
		} else {
			redirect('admin/');
		}
	}

	public function ajax_get_products()
	{
		$output = '';
		if (isset($_POST["query"])) {

			$query = $this->input->post('query');

			$result = $this->Product_model->get_like_product($query)->result();

			$output = '<ul class="list-unstyled">';

			if (mysqli_num_rows($result) > 0) {
				foreach ($result as $r) {
					$output .= '<li>' . $r->product_name . '</li>';
				}
			} else {
				$output .= '<li>User Not Found</li>';
			}

			$output .= '</ul>';
		}

		echo json_encode($output);
		exit();
	}

	public function delete_product_categories()
	{
		if ($this->input->post('type') == 'delete_record') {

			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			$session = $this->session->userdata('username');
			if (empty($session)) {
				redirect('admin/');
			}

			$id = $this->uri->segment(4);
			$result = $this->Xin_model->delete_product_category_record($id);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_product_category_success_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	// Validate and update info in database
	public function update_product_category()
	{

		if ($this->input->post('edit_type') == 'product_category') {

			$id = $this->uri->segment(4);

			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			if ($this->input->post('category_name') === '') {
				$Return['error'] = $this->lang->line('ms_error_product_category_name_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'category_name' => $this->input->post('category_name'),
			);

			$result = $this->Xin_model->update_category_product_record($data, $id);

			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('ms_product_category_updated');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
		}
	}


	// SUB CATEGORIES
	public function sub()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_product_sub_categories') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_product_sub_categories');

		$data['path_url'] = 'product_categories';
		$data['categories'] = $this->Xin_model->get_all_product_categories();

		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('490', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/product_categories/sub_category_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function create_sub()
	{

		if ($this->input->post('type') == 'create') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('category_id') === '') {
				$Return['error'] = $this->lang->line('ms_error_product_category_name_field');
			} else if ($this->input->post('sub_category_name') === '') {
				$Return['error'] = $this->lang->line('ms_error_product_sub_category_name_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'category_id' => $this->input->post('category_id'),
				'sub_category_name' => $this->input->post('sub_category_name'),
			);

			$result = $this->Xin_model->add_product_sub_category($data);
			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('ms_product_sub_category_added');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
		}
	}

	public function get_ajax_table_sub()
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


		$constant = $this->Xin_model->get_all_product_sub_categories();

		$data = array();

		foreach ($constant->result() as $r) {

			if (in_array('492', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_id="' . $r->sub_category_id . '" data-field_type="product_sub_categories"><span class="fas fa-pencil-alt"></span></button></span>';
			} else {
				$edit = '';
			}

			if (in_array('493', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->sub_category_id . '" data-token_type="product_sub_categories"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			// get vendor
			$sub = $this->Xin_model->read_product_category($r->category_id);
			if (!is_null($sub)) {
				$category_name = $sub[0]->category_name;
			} else {
				$category_name = '--';
			}

			$combhr = $edit . $delete;

			$data[] = array(
				$combhr,
				$category_name,
				$r->sub_category_name,
				$r->created_at,
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $constant->num_rows(),
			"recordsFiltered" => $constant->num_rows(),
			"data" => $data
		);

		echo json_encode($output);
		exit();
	}

	public function delete_product_sub_categories()
	{
		if ($this->input->post('type') == 'delete_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			$session = $this->session->userdata('username');
			if (empty($session)) {
				redirect('admin/');
			}

			$id = $this->uri->segment(4);
			$result = $this->Xin_model->delete_product_sub_category_record($id);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_product_sub_category_success_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function read_product_sub_categories()
	{
		$data['title'] = $this->Xin_model->site_title();
		$id = $this->input->get('field_id');
		$result = $this->Xin_model->read_product_sub_category($id);
		$data = array(
			'categories' => $this->Xin_model->get_all_product_categories(),
			'category_id' => $result[0]->category_id,
			'sub_category_id' => $result[0]->sub_category_id,
			'sub_category_name' => $result[0]->sub_category_name,
		);
		$session = $this->session->userdata('username');
		if (!empty($session)) {
			$this->load->view('admin/product_categories/dialog_sub_category', $data);
		} else {
			redirect('admin/');
		}
	}

	// Validate and update info in database
	public function update_product_sub_category()
	{

		if ($this->input->post('edit_type') == 'product_sub_category') {

			$id = $this->uri->segment(4);

			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			if ($this->input->post('category_name') === '') {
				$Return['error'] = $this->lang->line('ms_error_product_sub_category_name_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'category_id' => $this->input->post('category_id'),
				'sub_category_name' => $this->input->post('sub_category_name'),
			);

			$result = $this->Xin_model->update_sub_category_product_record($data, $id);

			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('ms_product_sub_category_updated');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
		}
	}
}
