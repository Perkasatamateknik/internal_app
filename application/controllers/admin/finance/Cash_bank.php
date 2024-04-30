<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cash_bank extends MY_Controller
{

	/*Function to set JSON output*/
	public function output($Return = array())
	{
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function __construct()
	{
		parent::__construct();
		//load the models
		$this->load->model('Xin_model');
		$this->load->model('Accounts_model');
	}


	public function index()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_cash_bank');
		$data['path_url'] = 'finance/cash_bank';
		if (empty($session)) {
			redirect('admin/');
		}

		$data['records'] = $this->Accounts_model->get_all_bank();

		// dd($data);
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/cash_bank/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_modal_add_cash_bank()
	{
		// $data['categories'] = $this->Account_categories_model->all()->result();
		return $this->load->view("admin/finance/cash_bank/add_cash_bank");
	}

	public function get_modal_edit_cash_bank()
	{
		$id = $this->input->get('id');
		$data['record'] = $this->Accounts_model->get_account_by_id($id);
		// dd($data);
		return $this->load->view("admin/finance/cash_bank/edit_cash_bank", $data);
	}

	public function update()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$data = array(
			'account_id' => $this->input->post('account_id'),
			'account_name' => $this->input->post('account_name'),
			'account_code' => $this->input->post('account_code'),
			'account_number' => $this->input->post('account_number'),
			'account_origin' => $this->input->post('account_origin'),
		);

		$result = $this->Accounts_model->update($data);
		if ($result) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}

		$this->output($Return);
		exit;
	}
}
