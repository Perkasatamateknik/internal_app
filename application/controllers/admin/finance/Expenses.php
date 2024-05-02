<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Expenses extends MY_Controller
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
		$this->load->model('Tax_model');
		$this->load->model('Expense_model');
		$this->load->model('Employees_model');
		$this->load->model('Accounts_model');
		$this->load->model('Account_trans_model');
		$this->load->model('Files_ms_model');
		$this->load->model('Expense_items_model');

		$this->redirect_uri = 'admin/finance/expense';
		$this->redirect_access = 'admin/';
	}

	public function index()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_expense');
		$data['path_url'] = 'finance/expense';
		if (empty($session)) {
			redirect($this->redirect_access);
		}

		if (true) {
			$data['subview'] = $this->load->view("admin/finance/expense/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect($this->redirect_access);
		}
	}

	public function get_ajax_expenses()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$record = $this->Expense_model->all();


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$from = "--";
		$to = "--";

		foreach ($record->result() as $i => $r) {

			if (!in_array(null, [$r->account_id, $r->beneficiary], true)) {

				$from_account = $this->Accounts_model->get($r->account_id)->row();
				if (!is_null($from_account)) {
					$from = "<b>$from_account->account_name</b>" . "  " . $from_account->account_code;
				}

				$to_beneficiary = $this->Employees_model->read_employee_information($r->beneficiary);
				if (!is_null($to_beneficiary)) {
					$to = $to_beneficiary[0]->first_name . "  " . $to_beneficiary[0]->last_name;
				}
			}

			// $sisa_tagihan = $this->Expense_model->get_sisa_tagihan($r->trans_number);
			$sisa_tagihan = 0;

			if (true) { //edit
				$edit = '<a class="dropdown-item delete waves-effect waves-light" href="' . site_url() . 'admin/finance/expenses/edit?id=' . $r->trans_number  . '" type="button">' . $this->lang->line('xin_edit') . '</a>';
			} else {
				$edit = '';
			}

			if (true) { // delete
				$delete = '<button class="dropdown-item delete waves-effect waves-light" href="#" type="button" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->trans_number . '" data-token_type="expense">' . $this->lang->line('xin_delete') . '</button>';
			} else {
				$delete = '';
			}


			$data[] = array(
				$this->Xin_model->set_date_format($r->date),
				"<a href='" . base_url('admin/finance/expenses/view?id=' . $r->trans_number) . "' class='text-secondary'>" . $r->trans_number . "</a>",
				$r->reference ?? "--",
				$from,
				status_trans($r->status, true),
				$to,
				$this->Xin_model->currency_sign($sisa_tagihan),
				'<div class="dropdown open">
					<button class="btn btn-default btn-sm m-0" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
					</button>
					<div class="dropdown-menu" aria-labelledby="triggerId">' .
					$edit . $delete
					. '</div>
				</div>'
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

	public function create()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect($this->redirect_access);
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();

		if (in_array('503', $role_resources_ids)) {
			$data['path_url'] = 'finance/expense';
			$init = $this->Expense_model->init_trans();

			$data['record'] = $init;
			$data['breadcrumbs'] = $this->lang->line('ms_title_expense');
			$data['subview'] = $this->load->view("admin/finance/expense/create", $data, TRUE);
			#
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect($this->redirect_access);
		}
	}

	public function store()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$action = $this->input->post('act_type');
		$trans_number = $this->input->post('_token');
		$user_id = $this->session->userdata()['username']['user_id'] ?? 0;

		#
		#
		$data = [
			'account_id' => $this->input->post('account_id'),
			'trans_number' => $this->input->post('trans_number'),
			'beneficiary' => $this->input->post('beneficiary'),
			'date' => $this->input->post('date'),
			'reference' => $this->input->post('reference'),
			'due_date' => $this->input->post('due_date'),
			'term' => $this->input->post('select_due_date'),
			'status' => $action == 'save' ? 'draft' : 'unpaid',
		];

		$items = [];
		$trans = [];

		$tax_withholding = 0;
		$tax_no_withholding = 0;
		$amount_item = 0;

		for ($i = 0; $i < count($this->input->post('row_amount')); $i++) {
			$amount_item += $this->input->post('row_amount')[$i];
			$tax_id = $this->input->post('row_tax_id')[$i];

			$check_tax = $this->Tax_model->read_tax_information($tax_id);
			if ($check_tax) {
				if ($check_tax[0]->is_withholding == 1) {
					$tax_withholding += $this->input->post('row_tax_rate')[$i] ?? 0;
					$set_withholding = 1;

					// $trans[] = [
					// 	'account_id' => $this->input->post('row_target_id')[$i], // account_id
					// 	'user_id' => $user_id,
					// 	'account_trans_cat_id' => 4,
					// 	'amount' => $this->input->post('row_amount')[$i] - $this->input->post('row_tax_rate')[$i],
					// 	'date' => date('Y-m-d'),
					// 	'type' => 'debit',
					// 	'join_id' => $trans_number,
					// 	'ref' => "Dokumen Expense",
					// 	'note' => "Payment Dokumen Expense",
					// 	'attachment' => null,
					// ];
				} else {
					$tax_no_withholding += $this->input->post('row_tax_rate')[$i] ?? 0;
					$set_withholding = 0;

					// $trans[] = [
					// 	'account_id' => $this->input->post('row_target_id')[$i], // account_id
					// 	'user_id' => $user_id,
					// 	'account_trans_cat_id' => 4,
					// 	'amount' => $this->input->post('row_amount')[$i] + $this->input->post('row_tax_rate')[$i],
					// 	'date' => date('Y-m-d'),
					// 	'type' => 'debit',
					// 	'join_id' => $trans_number,
					// 	'ref' => "Dokumen Expense",
					// 	'note' => "Payment Dokumen Expense",
					// 	'attachment' => null,
					// ];
				}
				// } else {
			}

			$trans[] = [
				'account_id' => $this->input->post('row_target_id')[$i], // account_id
				'user_id' => $user_id,
				'account_trans_cat_id' => 4,
				'amount' => $this->input->post('row_amount')[$i],
				'date' => date('Y-m-d'),
				'type' => 'debit',
				'join_id' => $trans_number,
				'ref' => "Dokumen Expense",
				'note' => "Payment Dokumen Expense",
				'attachment' => null,
			];

			$items[] = [
				'trans_number' 		=> $trans_number,
				'account_id' 		=> $this->input->post('row_target_id')[$i],
				'tax_id' 			=> $this->input->post('row_tax_id')[$i],
				'tax_rate' 			=> $this->input->post('row_tax_rate')[$i],
				'tax_type' 			=> $this->input->post('data_tax_type')[$i],
				'tax_withholding' 	=> $set_withholding ?? 0,
				'amount' 			=> $this->input->post('row_amount')[$i],
				'note' 				=> $this->input->post('row_note')[$i] ?? null,
			];
		}


		// masukan semua total ke akun Trade Payble = credit
		$trans[] =
			[
				'account_id' => 34, // account_id trade payable
				'user_id' => $user_id,
				'account_trans_cat_id' => 4, // = expense
				'amount' => $amount_item + ($tax_no_withholding - $tax_withholding),
				'date' => date('Y-m-d'),
				'type' => 'credit',
				'join_id' => $trans_number, // po number
				'ref' => "Dokumen Expense",
				'note' => "Begin Dokumen Expense",
				'attachment' => null,
			];

		// // masukan semua total ke akun pengirim = credit
		// $trans[] =
		// 	[
		// 		'account_id' => $this->input->post('account_id'),
		// 		'user_id' => $user_id,
		// 		'account_trans_cat_id' => 4, // = spend
		// 		'amount' => $amount_item + ($tax_no_withholding - $tax_withholding),
		// 		'date' => date('Y-m-d'),
		// 		'type' => 'credit',
		// 		'join_id' => $trans_number, // po number
		// 		'ref' => "Dokumen Spend",
		// 		'note' => "Begin Dokumen Spend",
		// 		'attachment' => null,
		// 	];

		if ($tax_withholding > 0) {
			// masukan tax total ke akun VAT Out - withholding
			$trans[] =
				[
					'account_id' => 45,
					'user_id' => $user_id,
					'account_trans_cat_id' => 4,
					'amount' => $tax_withholding,
					'date' => date('Y-m-d'),
					'type' => 'credit',
					'join_id' => $trans_number,
					'ref' => "Tax from",
					'note' => "Tax from Dokumen Expense",
					'attachment' => null,
				];
		}

		if ($tax_no_withholding > 0) {
			// masukan tax total ke akun VAT In - no withholding
			$trans[] =
				[
					'account_id' => 14,
					'user_id' => $user_id,
					'account_trans_cat_id' => 4,
					'amount' => $tax_no_withholding,
					'date' => date('Y-m-d'),
					'type' => 'debit',
					'join_id' => $trans_number,
					'ref' => "Tax from",
					'note' => "Tax from Dokumen Expense",
					'attachment' => null,
				];
		}


		if (!empty($_FILES["attachments"]["name"][0])) {
			// upload file
			$config['allowed_types'] = 'gif|jpg|png|pdf';
			$config['max_size'] = '10240'; // max_size in kb

			$config['upload_path'] = './uploads/finance/expense/';

			//load upload class library
			$this->load->library('upload', $config);

			$files = $_FILES['attachments'];
			$file_attachments = array();

			foreach ($files['name'] as $key => $filename) {

				// Get the file extension
				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				$newName = $this->input->post('trans_number') . "_" . time() . "_" . $key . "." . $extension;

				$_FILES['attachments'] = array(
					'name'     => $newName,
					'type'     => $files['type'][$key],
					'tmp_name' => $files['tmp_name'][$key],
					'error'    => $files['error'][$key],
					'size'     => $files['size'][$key]
				);

				if ($this->upload->do_upload('attachments')) {
					$this->upload->data();

					$file_attachments[] = array(
						'file_name' => $newName,
						'file_size' => $files['size'][$key],
						'file_type' => $files['type'][$key],
						'file_ext' => $extension,
						'file_access' => 4, // expense
						'access_id' => $trans_number
					);
				} else {
					$Return['error'] = $this->upload->display_errors();
					$this->output($Return);
				}
			}
		} else {
			$file_attachments = null;
		}

		$query = $this->Expense_model->update_with_items_and_files($trans_number, $data, $trans, $items, $file_attachments);

		if ($query) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_error');
			$this->output($Return);
		}
	}

	public function view()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_expense');
		$data['path_url'] = 'finance/expense';
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Expense_model->get_by_number_doc($id);

		if (!is_null($record)) {
			$from = $this->Accounts_model->get($record->account_id)->row();
			$to = $this->Employees_model->read_employee_information($record->beneficiary);
			$record->source_account = $from->account_name . " " . $from->account_code;
			$record->beneficiary = $to[0]->first_name . " " . $to[0]->last_name;

			// get payment
			$data['payment'] = $this->Expense_model->get_payment(4, $record->trans_number);

			// 4 => roles expense
			$attachments = $this->Files_ms_model->get_by_access_id(4, $record->trans_number)->result();
			$data['attachments'] = $attachments;

			//add expense items model
			$items = $this->Expense_items_model->get_by_trans_number($record->trans_number);
			// dd($items);
			if (!is_null($items)) {
				foreach ($items as $item) {
					$item->account_name = $this->Accounts_model->get($item->account_id)->row()->account_name;
					$tax = $this->Tax_model->read_tax_information($item->tax_id); // return bool

					if ($tax) {
						$item->tax_name = $tax[0]->name;
						$item->tax_rate = $item->tax_rate;;
					} else {
						$item->tax_name = "--";
						$item->tax_rate = 0;
					}
				}
			}

			$data['items'] = $items;
		} else {
			redirect('admin/finance/expense');
		}


		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/expense/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function edit()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_expense');
		$data['path_url'] = 'finance/expense';
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Expense_model->get_by_number_doc($id);

		if (!is_null($record)) {
			$from = $this->Accounts_model->get($record->account_id)->row();
			$to = $this->Employees_model->read_employee_information($record->beneficiary);
			$record->source_account = $from->account_name . " " . $from->account_code;
			$record->beneficiary_name = $to[0]->first_name . " " . $to[0]->last_name;

			// get payment
			$data['payment'] = $this->Expense_model->get_payment(4, $record->trans_number);

			// 4 => roles expense
			$attachments = $this->Files_ms_model->get_by_access_id(4, $record->trans_number)->result();
			$data['attachments'] = $attachments;

			//add expense items model
			$items = $this->Expense_items_model->get_by_trans_number($record->trans_number);
			// dd($items);
			if (!is_null($items)) {
				foreach ($items as $item) {
					$item->account_name = $this->Accounts_model->get($item->account_id)->row()->account_name;
					$tax = $this->Tax_model->read_tax_information($item->tax_id); // return bool

					if ($tax) {
						$item->tax_name = $tax[0]->name;
						$item->tax_rate = $item->tax_rate;;
					} else {
						$item->tax_name = "--";
						$item->tax_rate = 0;
					}
				}
			}

			$data['items'] = $items;
		} else {
			redirect('admin/finance/expense');
		}

		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/expense/edit", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}


	public function store_payment()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$trans_number = $this->input->post('_token');
		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');
		$source_payment_account = $this->input->post('source_payment_account');

		// get user id
		$user_id = $this->session->userdata('username')['user_id'];

		$file_attachment = $this->upload_attachment('./uploads/finance/account_trans/', 'TRANS');

		// uang yang dibayar
		$amount_paid = $this->input->post('amount_paid');
		$trans = [];

		// kredit pengirim
		$trans[] =
			[
				'account_id' => $source_payment_account,
				'user_id' => $user_id,
				'account_trans_cat_id' => 4, // 2= expense
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'credit',
				'join_id' => $trans_number,
				'ref' => $payment_ref,
				'note' => "Pembayaran Expense",
				'attachment' => $file_attachment,
			];

		// debit trade payable
		$trans[] =
			[
				'account_id' => 34,
				'user_id' => $user_id,
				'account_trans_cat_id' => 4, // 2= spend
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $trans_number,
				'ref' => $payment_ref,
				'note' => "Debit Expense",
				'attachment' => $file_attachment,
			];

		$insert = $this->Account_trans_model->insert_payment($trans);

		// // check if all tagihan is paid or partially paid
		$check_tagihan = $this->Expense_model->get_payment(4, $trans_number);

		if ($check_tagihan->sisa_tagihan == 0) {
			// update status spend
			$this->Expense_model->update_by_trans_number($trans_number, ['status' => 'paid']);
		} else {
			// update status spend
			$this->Expense_model->update_by_trans_number($trans_number, ['status' => 'partially_paid']);
		}

		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}

	public function delete()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		if (in_array(505, $role_resources_ids)) {
			$id = $this->input->post('_token');

			$del_attachment = $this->input->post('del_attachment') ?? false; // bool

			$result = $this->Expense_model->delete($id, $del_attachment);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_success_pr_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function get_ajax_items_expense()
	{
		// $data = array();
		$id = $this->input->get('_token');
		$items = $this->Expense_items_model->get_by_trans_number($id);

		if (!is_null($items)) {
			$output = [
				// 'data' => $pr_data,
				'items' => $items
			];
		} else {
			$output = false;
		}
		$this->output($output);
		exit();
	}
}
