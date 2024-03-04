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
		$this->load->model('Finance_model');
		$this->load->model('Expense_model');
		$this->load->model('Invoices_model');
		$this->load->model('Employees_model');
		$this->load->model('Department_model');
		$this->load->model('Project_model');
		$this->load->model('Awards_model');
		$this->load->model('Accounts_model');
		$this->load->model('Training_model');
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
					// } else {
					// 	$from = "--";
				}

				$to_beneficiary = $this->Employees_model->read_employee_information($r->beneficiary);
				if (!is_null($to_beneficiary)) {
					$to = $to_beneficiary[0]->first_name . "  " . $to_beneficiary[0]->last_name;
					// } else {
					// 	$to = "--";
				}
				// } else {
				// 	$from = "--";
				// 	$to = "--";
			}

			$data[] = array(
				$this->Xin_model->set_date_format($r->date),
				"<a href='" . base_url('admin/finance/expense/view?id=' . $r->trans_number) . "' class='text-secondary'>" . $r->trans_number . "</a>",
				$r->reference ?? "--",
				$from,
				status_trans($r->status),
				$to,
				$this->Xin_model->currency_sign(0),
				'<div class="dropdown open">
					<button class="btn btn-transparent btn-sm m-0" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
					</button>
					<div class="dropdown-menu" aria-labelledby="triggerId">
						<button class="dropdown-item" href="#">' . $this->lang->line('xin_edit') . '</button>
						<button class="dropdown-item" href="#">' . $this->lang->line('xin_delete') . '</button>
					</div>
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
		$id = $this->input->post('_token');

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
		for ($i = 0; $i < count($this->input->post('row_amount')); $i++) {

			$items[] = [
				'expense_id' => $id,
				'account_id' => $this->input->post('row_target_id')[$i],
				'tax_id' => $this->input->post('row_tax_id')[$i],
				'tax_rate' => $this->input->post('row_tax_rate')[$i],
				'amount' => $this->input->post('row_amount')[$i],
				'note' => $this->input->post('row_note')[$i] ?? null,
			];
		}

		if (isset($_FILES["attachments"])) {
			// upload file
			$config['allowed_types'] = 'gif|jpg|png|pdf';
			$config['max_size'] = '10240'; // max_size in kb

			$config['upload_path'] = './uploads/finance/account_spend/';

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
						'access_id' => $id
					);
				} else {
					$Return['error'] = $this->upload->display_errors();
					$this->output($Return);
				}
			}
		} else {
			$file_attachments = null;
		}

		$query = $this->Expense_model->update_with_items_and_files($id, $data, $items, $file_attachments);

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
		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Expense_model->get_by_number_doc($id);

		if (!is_null($record)) {
			$from = $this->Accounts_model->get($record->account_id)->row();
			$to = $this->Employees_model->read_employee_information($record->beneficiary);
			$record->source_account = $from->account_name . " " . $from->account_code;
			$record->beneficiary = $to[0]->first_name . " " . $to[0]->last_name;

			// dd($record);

			// 4 => roles expense
			$attachments = $this->Files_ms_model->get_by_access_id(4, $record->expense_id)->result();
			$data['attachments'] = $attachments;

			//add expense items model
			$items = $this->Expense_items_model->get($record->expense_id);
			// dd($items);
			if (!is_null($items)) {
				foreach ($items as $item) {
					$item->account_name = $this->Accounts_model->get($item->account_id)->row()->account_name;
					$tax = $this->Tax_model->read_tax_information($item->tax_id); // return bool

					if ($tax) {
						$item->tax_name = $tax[0]->name;
						$item->tax_rate = $tax[0]->rate;
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

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_expense');
		$data['path_url'] = 'finance/account_expense';
		if (empty($session)) {
			redirect('admin/');
		}


		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/expense/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}
}
