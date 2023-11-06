<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transactions extends MY_Controller
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
		$this->load->model('Finance_trans');
	}


















	public function print_transfer()
	{
		return $this->load->view('admin/finance/transactions/print_transfer');
	}

	public function print_trans()
	{
		return $this->load->view('admin/finance/transactions/print_trans');
	}
}
