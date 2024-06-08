<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vendors extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		$this->load->model("Xin_model");

		$this->load->model("Vendor_model");
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
		$data['title'] = $this->lang->line('ms_vendors') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_vendors');
		$data['all_countries'] = $this->Xin_model->get_countries();

		$data['path_url'] = 'vendors';
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('478', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/vendors/vendor_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	// update 9-5-2023
	// Vendors
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


		$constant = $this->Xin_model->get_all_vendor();

		$data = array();

		foreach ($constant->result() as $r) {

			// get country
			$country = $this->Xin_model->read_country_info($r->country);
			if (!is_null($country)) {
				$c_name = $country[0]->country_name;
			} else {
				$c_name = '--';
			}

			if (in_array('480', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light"  data-toggle="modal" data-target=".edit-modal-data"  data-vendor_id="' . $r->vendor_id . '"><span class="fas fa-pencil-alt"></span></button></span>';
			} else {
				$edit = '';
			}

			if (in_array(
				'481',
				$role_resources_ids
			)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->vendor_id . '"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$combhr = $edit . $delete;
			$data[] = array(
				$combhr,
				$r->vendor_name,
				$r->vendor_contact,
				$r->vendor_address,
				"<span>" . $this->lang->line('xin_city') . "</span>: " . $r->city . "<br>" .
					"<span>" . $this->lang->line('xin_state') . "</span>: " . $r->state . "<br>" .
					"<span>" . $this->lang->line('xin_zipcode') . "</span>: " . $r->zipcode . "",
				$c_name,
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


	// update feature 9-5-2023
	// Validate and add info in database
	public function create()
	{

		if ($this->input->post('type') == 'vendors') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('vendor_name') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_name_field');
			} else if ($this->input->post('vendor_name') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_name_field');
			} else if ($this->input->post('vendor_contact') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_contact_field');
			} else if ($this->input->post('vendor_address') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_address_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'vendor_name' => $this->input->post('vendor_name'),
				'vendor_contact' => $this->input->post('vendor_contact'),
				'vendor_address' => $this->input->post('vendor_address'),
				'city' => $this->input->post('city'),
				'state' => $this->input->post('state'),
				'zipcode' => $this->input->post('zipcode'),
				'country' => $this->input->post('country'),
				'created_at' => date('d-m-Y h:i:s')
			);

			$result = $this->Xin_model->add_vendor($data);
			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('ms_vendor_added');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
		}
	}

	// Validate and update info in database
	public function update()
	{

		if ($this->input->post('edit_type') == 'vendors') {

			$id = $this->uri->segment(4);

			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('vendor_name') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_name_field');
			} else if ($this->input->post('vendor_name') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_name_field');
			} else if ($this->input->post('vendor_contact') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_contact_field');
			} else if ($this->input->post('vendor_address') === '') {
				$Return['error'] = $this->lang->line('ms_error_vendor_address_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'vendor_name' => $this->input->post('vendor_name'),
				'vendor_contact' => $this->input->post('vendor_contact'),
				'vendor_address' => $this->input->post('vendor_address'),
				'city' => $this->input->post('city'),
				'state' => $this->input->post('state'),
				'zipcode' => $this->input->post('zipcode'),
				'country' => $this->input->post('country'),
			);

			$result = $this->Vendor_model->update_record($data, $id);

			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('ms_success_vendor_updated');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
		}
	}

	// delete constant record > table
	public function delete()
	{

		if ($this->input->post('type') == 'delete') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$id = $this->uri->segment(4);
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$result = $this->Xin_model->delete_vendor_record($id);
			if (isset($id)) {
				$Return['result'] = $this->lang->line('ms_vendor_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function read()
	{
		// if (!$this->input->is_ajax_request()) {
		// 	exit('No direct script access allowed');
		// }

		$data['title'] = $this->Xin_model->site_title();
		$id = $this->input->get('vendor_id');
		$result = $this->Vendor_model->read_vendor_information($id);
		// dd($result);
		$data = array(
			'vendor_id' => $result[0]->vendor_id,
			'vendor_name' => $result[0]->vendor_name,
			'vendor_address' => $result[0]->vendor_address,
			'vendor_contact' => $result[0]->vendor_contact,
			'city' => $result[0]->city,
			'zipcode' => $result[0]->zipcode,
			'state' => $result[0]->state,
			'country' => $result[0]->country,
			'all_countries' => $this->Xin_model->get_countries(),
		);
		$session = $this->session->userdata('username');
		if (!empty($session)) {
			$this->load->view('admin/vendors/dialog_vendor', $data);
		} else {
			redirect('admin/');
		}
	}
}
