<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		$this->load->model("Product_model");
		$this->load->model("Xin_model");
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
		$data['title'] = $this->lang->line('ms_products') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_products');
		$data['all_countries'] = $this->Xin_model->get_countries();

		$data['path_url'] = 'products';
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('482', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/products/product_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
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


	public function add_product()
	{
		if ($this->input->post('type') == 'products') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('product_number') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_product_number');
			} else if ($this->input->post('product_name') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_product_name');
			} else if ($this->input->post('uom_id') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_uom_id');
			} else if ($this->input->post('sub_category_id') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_sub_category_id');
			} else if ($this->input->post('price') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_price');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'product_number' => $this->input->post('product_number'),
				'product_name' => $this->input->post('product_name'),
				'uom_id' => $this->input->post('uom_id'),
				'sub_category_id' => $this->input->post('sub_category_id'),
				'price' => $this->input->post('price'),
				'product_desc' => $this->input->post('product_desc'),
				'created_at' => date('Y-m-d H:i:s')
			);

			$result = $this->Product_model->insert($data);
			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('ms_product_success_added');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
		}
	}


	public function product_list()
	{

		//get data role akses
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


		$query = $this->Product_model->gel_all_product();

		$data = array();
		// dd($query->result());
		foreach ($query->result() as $r) {

			// $price = $this->Xin_model->currency_sign($r->price);
			$price = "Rp " . number_format($r->price, 2, ",", ".");

			// get category
			$sub_category = $this->Xin_model->read_product_sub_category($r->sub_category_id);
			if (!is_null($sub_category)) {

				$sub_category_name = $sub_category[0]->sub_category_name;
			} else {
				$sub_category_name = '--';
			}

			// get uom
			$uom = $this->Xin_model->read_uom($r->uom_id);
			if (!is_null($uom)) {
				$uom_name = $uom[0]->uom_name;
			} else {
				$uom_name = '--';
			}

			if (in_array('484', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light"  data-toggle="modal" data-target=".edit-modal-data"  data-product_id="' . $r->product_id . '"><span class="fas fa-pencil-alt"></span></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('485', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->product_id . '"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$combhr = $edit . $delete;

			$data[] = array(
				$combhr,
				$r->product_number,
				$r->product_name,
				$price,
				$uom_name,
				$sub_category_name,
				$r->product_desc,
			);
		}
		$output = array(
			"draw" => $draw,
			"recordsTotal" => $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function delete_product()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		$id = $this->uri->segment(4);
		$this->Product_model->delete($id);
		if (isset($id)) {
			$Return['result'] = $this->lang->line('ms_product_success_deleted');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}
		$this->output($Return);
	}

	public function read()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		}

		$data['title'] = $this->Xin_model->site_title();
		$id = $this->input->get('product_id');
		$result = $this->Product_model->read_info($id);
		// var_dump($result);
		// die;
		$data = array(
			'product_id' => $result[0]->product_id,
			'uom_id' => $result[0]->uom_id,
			'sub_category_id' => $result[0]->sub_category_id,
			'product_name' => $result[0]->product_name,
			'price' => $result[0]->price,
			'product_number' => $result[0]->product_number,
			'product_desc' => $result[0]->product_desc,
			'sub_categories' => $this->Xin_model->get_all_product_sub_categories(),
			'uoms' => $this->Xin_model->get_all_uoms(),
		);
		$session = $this->session->userdata('username');
		if (!empty($session)) {
			$this->load->view('admin/products/dialog_product', $data);
		} else {
			redirect('admin/');
		}
	}

	public function update()
	{
		if ($this->input->post('edit_type') == 'products') {

			$id = $this->uri->segment(4);

			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('product_number') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_product_number');
			} else if ($this->input->post('product_name') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_product_name');
			} else if ($this->input->post('uom_id') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_uom_id');
			} else if ($this->input->post('sub_category_id') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_sub_category_id');
			} else if ($this->input->post('price') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_price');
			}



			$data = array(
				'product_number' => $this->input->post('product_number'),
				'product_name' => $this->input->post('product_name'),
				'uom_id' => $this->input->post('uom_id'),
				'sub_category_id' => $this->input->post('sub_category_id'),
				'price' => $this->input->post('price'),
				'product_desc' => $this->input->post('product_desc')
			);

			$result = $this->Product_model->update($data, $id);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_product_success_updated');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}

			$this->output($Return);
		}
	}

	public function get_ajax_products()
	{
		$searchTerm = $this->input->get('query');
		$data = $this->Product_model->searchProduct($searchTerm);
		echo json_encode($data);
		exit();
	}

	public function set_product()
	{
		$this->Product_model->set_ulang();
	}
}
