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
		$this->load->model('Finance_model');
		$this->load->model('Expense_model');
		$this->load->model('Invoices_model');
		$this->load->model('Employees_model');
		$this->load->model('Department_model');
		$this->load->model('Project_model');
		$this->load->model('Awards_model');
		$this->load->model('Training_model');
	}


	public function index()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_cash_bank');
		$data['path_url'] = 'finance/cashbank';
		if (empty($session)) {
			redirect('admin/');
		}

		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/cash_bank/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}
}
