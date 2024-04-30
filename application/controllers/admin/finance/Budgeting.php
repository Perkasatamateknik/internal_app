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
		$this->load->model('department_model');
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

	public function modal_budgeting()
	{
		$data['used_year'] = $this->Budgeting_model->get_used_year();
		$this->load->view("admin/finance/budgeting/dialog_budget", $data);
	}

	public function modal_budgeting_data()
	{
		$data['used_year'] = $this->Budgeting_model->get_used_year();
		$this->load->view("admin/finance/budgeting/dialog_budget_data", $data);
	}

	public function insert_budget()
	{
		if ($this->input->is_ajax_request()) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('company_id') === '') {
				$Return['error'] = $this->lang->line('error_company_field');
			} else if ($this->input->post('employee_id') === '') {
				$Return['error'] = $this->lang->line('xin_error_employee_id');
			} else if ($this->input->post('month_year') === '') {
				$Return['error'] = $this->lang->line('xin_hr_report_error_month_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = [
				'year' => $this->input->post('year')
			];

			$insert = $this->Budgeting_model->add_budget($data);
			if ($insert) {
				$Return['result'] = $this->lang->line('ms_title_success_added');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function insert_budget_data()
	{
		// if ($this->input->is_ajax_request()) {
		if (true) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('company_id') === '') {
				$Return['error'] = $this->lang->line('error_company_field');
			} else if ($this->input->post('employee_id') === '') {
				$Return['error'] = $this->lang->line('xin_error_employee_id');
			} else if ($this->input->post('month_year') === '') {
				$Return['error'] = $this->lang->line('xin_hr_report_error_month_field');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = [];

			$budget_id = $this->input->post('budget_id');
			$items = $this->input->post('row_amount');
			for ($i = 0; $i < count($items); $i++) {
				$data[] = [
					'budget_dep_id' => 0,
					'budget_name' 	=> $this->input->post('row_budget_name')[$i],
					'account_id' 	=> $this->input->post('row_account')[$i],
					'amount' 		=> $this->input->post('row_amount')[$i],
				];
			}

			$department = [
				'budget_id' => $budget_id,
				'department_id' => $this->input->post('department_id') ?? 0,
			];

			$insert = $this->Budgeting_model->add_budget_data($department, $data);
			if ($insert) {
				$Return['result'] = $this->lang->line('ms_title_success_added');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function view()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_budgetings');
		$data['path_url'] = 'finance/budgeting';
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->input->get('id'); //id_budget

		$data['record'] = $this->Budgeting_model->get($id);
		$data['records'] = $this->Budgeting_model->get_items_budget($id);

		if (in_array('503', $this->roles)) {
			$data['subview'] = $this->load->view("admin/finance/budgeting/view", $data, true);

			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function find_department()
	{
		$budget_id = $this->input->get('id');
		$query = $this->input->get('query');
		$res = $this->department_model->find_department_budget($query, $budget_id);
		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->department_id,
				'text' => $r->department_name,
			);
		}
		echo $this->output($data);
		exit();
	}
}
