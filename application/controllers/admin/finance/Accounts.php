<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Accounts extends MY_Controller
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

	private $roles;
	public function __construct()
	{
		parent::__construct();
		//load the models
		$this->load->model('Xin_model');
		$this->load->model('Accounts_model');
		$this->load->model('Account_categories_model');
		$this->load->model('Account_trans_model');
		$this->roles = $this->Xin_model->user_role_resource();
	}

	public function index()
	{

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_accounts');
		$data['path_url'] = 'finance/accounts';
		$data['categories'] = $this->Account_categories_model->all()->result();

		if (empty($session)) {
			redirect('admin/');
		}

		if (in_array('503', $this->roles)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function insert()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();


		// if ($this->input->get('is_ajax')) {
		$data = array(
			'category_id' => $this->input->post('category_id'),
			'account_name' => $this->input->post('account_name'),
			'account_code' => $this->input->post('account_code'),
			'account_origin' => $this->input->post('account_origin'),
		);

		if ($Return['error'] != '') {
			$this->output($Return);
		}

		$result = $this->Accounts_model->insert($data);
		if ($result) {
			$Return['result'] = $this->lang->line('xin_success_add');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}
		$this->output($Return);
		exit;
		// }
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

		$records = $this->Accounts_model->all();

		// dd($records->result());
		foreach ($records->result() as $r) {

			// get category`
			$category = $this->Account_categories_model->get($r->category_id);
			if (!is_null($category)) {
				$category_name = $category->category_name;
			} else {
				$category_name = '--';
			}
			$trans = $this->Account_trans_model->get_by_account($r->account_id);
			if (!is_null($trans)) {

				$saldo = 0;
				foreach ($trans as $t) {
					if ($t->type == 'credit') {
						$saldo += $t->amount;
					} else {
						$saldo -= $t->amount;
					}
				}
			} else {
				$saldo = 0;
			}

			$href = "<a href='" . base_url('admin/finance/accounts/view/') . $r->account_id . "' class='font-weight-bold text-dark hoverable'>" . $r->account_name . "</a>";
			$data[] = [
				$r->account_code,
				$href,
				$category_name,
				$this->Xin_model->currency_sign($saldo),
			];
		}
		$output = array(
			"draw" => $draw,
			// "recordsTotal" => $records->num_rows(),
			// "recordsFiltered" => $records->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function view($id)
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_accounts');
		$data['path_url'] = 'finance/accounts';
		$data['id'] = $id;
		if (empty($session)) {
			redirect('admin/');
		}

		if (in_array('503', $this->roles)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_table_transaction()
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

		$records = $this->Accounts_tramodel->all();

		// dd($records->result());
		foreach ($records->result() as $r) {

			// get category`
			$category = $this->Account_categories_model->get($r->category_id);
			if (!is_null($category)) {
				$category_name = $category->category_name;
			} else {
				$category_name = '--';
			}
			$trans = $this->Account_trans_model->get_by_account($r->account_id);
			if (!is_null($trans)) {

				$saldo = 0;
				foreach ($trans as $t) {
					if ($t->type == 'credit') {
						$saldo += $t->amount;
					} else {
						$saldo -= $t->amount;
					}
				}
			} else {
				$saldo = 0;
			}

			$href = "<a href='" . base_url('admin/finance/account/view/') . $r->account_id . "' class='font-weight-bold text-dark hoverable'>" . $r->account_name . "</a>";
			$data[] = [
				$r->account_code,
				$href,
				$category_name,
				$this->Xin_model->currency_sign($saldo),
			];
		}
		$output = array(
			"draw" => $draw,
			// "recordsTotal" => $records->num_rows(),
			// "recordsFiltered" => $records->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function close_book()
	{
		$data['title'] = $this->Xin_models->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_accounts');
		$data['path_url'] = 'finance/accounts';
		if (empty($session)) {
			redirect('admin/');
		}

		if (in_array('503', $this->roles)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/close_book", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}
}
