<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Budgeting extends MY_Controller
{

	public $redirect_uri;
	public $redirect_access;

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
		$this->load->model('Budgeting_model');
		$this->redirect_uri = 'admin/finance/invoices';
		$this->redirect_access = 'admin/';
	}

	public function index()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_budgeting');
		$data['path_url'] = 'finance/budgeting';
		if (empty($session)) {
			redirect($this->redirect_access);
		}
		$data['records'] = $this->Budgeting_model->all()->result();

		if (true) {
			$data['subview'] = $this->load->view("admin/finance/budgeting/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect($this->redirect_access);
		}
	}

	public function create()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_budgeting');
		$data['path_url'] = 'finance/budgeting';
		if (empty($session)) {
			redirect($this->redirect_access);
		}

		if (true) {
			$data['subview'] = $this->load->view("admin/finance/budgeting/create", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect($this->redirect_access);
		}
	}
}
