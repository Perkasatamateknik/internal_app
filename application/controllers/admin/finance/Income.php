<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Income extends MY_Controller
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
		echo "halo";
	}
}
