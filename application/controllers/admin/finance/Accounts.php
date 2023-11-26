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
		$this->load->model('Finance_trans');
		$this->roles = $this->Xin_model->user_role_resource();
	}

	public function trans_number($type)
	{
		if ($type == "transfer") {
			$query = $this->Account_trans_model->get_last_trans_number();
			#
		} elseif ($type == "spend") {
			$query = $this->Account_spend_model->get_last_trans_number();
			#
		} elseif ($type == "receive") {
			$query = $this->Account_receive_model->get_last_trans_number();
		}

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->trans_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		if ($type == "transfer") {
			return sprintf("TR-%05d", $nextNumericPart);
			#
		} else if ($type == "spend") {
			return sprintf("BS-%05d", $nextNumericPart);
			#
		} else if ($type == "receive") {
			return sprintf("BR-%05d", $nextNumericPart);
		}
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

	// ajax table transactions account
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

			if ($saldo < 0) {
				$color = "text-danger";
			} else {
				$color = "";
			}
			$href = "<a href='" . base_url('admin/finance/accounts/transactions?id=') . $r->account_id . "' class='font-weight-bold text-dark hoverable'>" . $r->account_name . "</a>";
			$data[] = [
				$r->account_code,
				$href,
				$category_name,
				"<span class='" . $color . "'>" . $this->Xin_model->currency_sign($saldo) . "</span>",
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

		$id = $this->input->get('id');
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

		$records = $this->Account_trans_model->get_all_trans($id);

		// dd($records);
		// dd($records->result());
		foreach ($records as $r) {

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


	public function transactions()
	{

		$id = $this->input->get('id');

		$record = $this->Accounts_model->get($id)->row();
		// dd($record);
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = "<strong>" . $this->lang->line('ms_title_transactions') . "</strong>" . "&nbsp;&nbsp;" . $record->account_code;
		$data['path_url'] = 'finance/transaction';
		if (empty($session)) {
			redirect('admin/');
		}

		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/transactions", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_trans()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->input->get('id');
		$record = $this->Finance_trans->get_trans($id);


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$balance = 0;
		foreach ($record->result() as $i => $r) {

			$credit = 0;
			$debit = 0;
			if ($r->type == 'credit') {
				$credit = $r->amount;
				$balance -= $r->amount;
			} else {
				$debit = $r->amount;
				$balance += $r->amount;
			}
			$data[] = array(
				$i += 1,
				$this->Xin_model->set_date_format($r->created_at),
				"Transfer",
				$r->desc,
				$r->ref,
				$this->Xin_model->currency_sign($debit),
				$this->Xin_model->currency_sign($credit),
				$this->Xin_model->currency_sign($balance),
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $record->num_rows(),
			"recordsFiltered" => $record->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function create_trans()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();

		$type = $this->input->get('type') ?? false;
		if (in_array('503', $role_resources_ids)) {

			// pilih menu
			if ($type == "transfer") {

				$data['path_url'] = 'finance/account_transfer';
				$trans_number = $this->trans_number("transfer");
				$init = $this->Account_trans_model->init_trans(['trans_number' => $trans_number]);

				$data['record'] = $this->Account_trans_model->get($init);
				$data['breadcrumbs'] = $this->lang->line('ms_title_transfer');
				$data['subview'] = $this->load->view("admin/finance/accounts/transfer_form", $data, TRUE);
				#
			} elseif ($type == "spend") {
				$data['breadcrumbs'] = $this->lang->line('ms_title_spend');
				$data['subview'] = $this->load->view("admin/finance/accounts/spend_form", $data, TRUE);
				#
			} elseif ($type == "receive") {
				$data['breadcrumbs'] = $this->lang->line('ms_title_receive');
				$data['subview'] = $this->load->view("admin/finance/accounts/receive_form", $data, TRUE);
				#
			} else {
				redirect('admin/dashboard');
			}
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}


	public function print()
	{
		$id = $this->input->get('id');

		$record = $this->Accounts_model->get($id)->row();
		// dd($record);
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = "<strong>" . $record->account_code . "</strong>" . "&nbsp;&nbsp;" . $record->account_name;
		$data['path_url'] = 'finance/transaction';
		if (empty($session)) {
			redirect('admin/');
		}

		// records
		$record = $this->Finance_trans->get_trans($id);


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$records = array();
		$balance = 0;
		foreach ($record->result() as $i => $r) {

			$credit = 0;
			$debit = 0;
			if ($r->type == 'credit') {
				$credit = $r->amount;
				$balance -= $r->amount;
			} else {
				$debit = $r->amount;
				$balance += $r->amount;
			}
			$records[] = array(
				$i += 1,
				$this->Xin_model->set_date_format($r->created_at),
				"Transfer",
				$r->desc,
				$r->ref,
				$this->Xin_model->currency_sign($debit),
				$this->Xin_model->currency_sign($credit),
				$this->Xin_model->currency_sign($balance),
			);
		}

		$data['records'] = $records;
		if (in_array('503', $role_resources_ids)) {
			return $this->load->view("admin/finance/accounts/print", $data);
		} else {
			redirect('admin/dashboard');
		}
	}

	public function spend_view()
	{
		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Account_trans_model->get($id);
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		// $data['breadcrumbs'] = "<strong>" . $record->account_code . "</strong>" . "&nbsp;&nbsp;" . $record->account_name;
		$data['breadcrumbs'] = $this->lang->line('ms_title_spend');
		$data['path_url'] = 'finance/transaction';
		if (empty($session)) {
			redirect('admin/');
		}


		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/spend_view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function transfer_view()
	{
		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Account_trans_model->get($id);
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		// $data['breadcrumbs'] = "<strong>" . $record->account_code . "</strong>" . "&nbsp;&nbsp;" . $record->account_name;
		$data['breadcrumbs'] = $this->lang->line('ms_title_transfer');
		$data['path_url'] = 'finance/transaction';
		if (empty($session)) {
			redirect('admin/');
		}


		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/transfer_view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function receive_view()
	{
		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Account_trans_model->get($id);
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		// $data['breadcrumbs'] = "<strong>" . $record->account_code . "</strong>" . "&nbsp;&nbsp;" . $record->account_name;
		$data['breadcrumbs'] = $this->lang->line('ms_title_receive');
		$data['path_url'] = 'finance/transaction';
		if (empty($session)) {
			redirect('admin/');
		}


		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/receive_view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}
}


// $daftar_akun = [
// 	"Cash & Bank",
// 	"Accounts Receivable (A/R)",
// 	"Inventory",
// 	"Other Current Assets",
// 	"Fixed Assets",
// 	"Depreciation & Amortization",
// 	"Other Assets",
// 	"Accounts Payable (A/P)",
// 	"Other Current Liabilities",
// 	"Long Term Liabilities",
// 	"Equity",
// 	"Income",
// 	"Cost of Sales",
// 	"Expenses",
// 	"Other Income",
// 	"Other Expenses",
// ];
