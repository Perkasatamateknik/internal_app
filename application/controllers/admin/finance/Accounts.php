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
		$this->load->model('Account_transfer_model');
		$this->load->model('Account_spend_model');
		$this->load->model('Account_spend_items_model');
		$this->load->model('Account_receive_model');
		$this->load->model('Account_receive_items_model');
		$this->load->model('Account_trans_model');
		$this->load->model('Files_ms_model');
		$this->load->model('Tax_model');
		$this->load->model('Employees_model');
		$this->load->model('Finance_trans');
		$this->load->model('Vendor_model');
		$this->roles = $this->Xin_model->user_role_resource();
	}

	function checkPattern($input, $prefix)
	{
		$pattern = '/^' . preg_quote($prefix, '/') . '-\d{5}$/';

		if (preg_match($pattern, $input)) {
			return true;
		} else {
			return false;
		}
	}

	public function trans_number($type)
	{
		if ($type == "transfer") {
			$query = $this->Account_transfer_model->get_last_trans_number();
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

		$last_account = $this->Accounts_model->get_last_account();
		$manual_account_id = $last_account->account_id + 1;
		$data = array(
			'account_id' => $manual_account_id,
			'category_id' => $this->input->post('category_id'),
			'account_name' => $this->input->post('account_name'),
			'account_code' => $this->input->post('account_code'),
			'account_number' => $this->input->post('account_number'),
			'account_origin' => $this->input->post('account_origin'),
		);

		if ($Return['error'] != '') {
			$this->output($Return);
		}

		$get_saldo_awal = $this->input->post('saldo_awal') ?? false;

		// dd($get_saldo_awal);
		if ($get_saldo_awal) {

			$trans_number = $this->Account_receive_model->trans_number();
			$last_receive = $this->Account_receive_model->get_last_receive();

			$user_id = $this->session->userdata('username')['user_id'] ?? 0;
			$manual_receive_id = $last_receive->receive_id + 1;

			$receive_doc['data'] = [
				'receive_id' => $manual_receive_id,
				'user_id' => $user_id,
				'vendor_id' => $this->input->post('vendor_id') ?? 0,
				'trans_number' => $trans_number,
				'date' => date('Y-m-d'),
				'reference' => "#FirstDebit",
				'status' => 'paid',
			];

			$receive_doc['items'] = [
				'receive_id' => $manual_receive_id,
				'account_id' => $manual_account_id, // account_id yg di buat
				'tax_id' => 0,
				'tax_rate' => 0,
				'amount' => $get_saldo_awal,
				'note' => "Pemasukan Saldo Awal",
			];

			$trans_saldo_awal = [
				[
					'account_id' => 59, // account_id opening balance
					'user_id' => $user_id,
					'account_trans_cat_id' => 3, // 3 = receive fund
					'amount' => $get_saldo_awal,
					'date' => date('Y-m-d'),
					'type' => 'credit',
					'join_id' => $manual_receive_id, // receive_id
					'ref' => "Opening Balance",
					'note' => "Kredit Opening Balance",
					'attachment' => null,
				],
				[
					'account_id' => $manual_account_id,
					'user_id' => $user_id,
					'account_trans_cat_id' => 3,
					'amount' => $get_saldo_awal,
					'date' => date('Y-m-d'),
					'type' => 'debit',
					'join_id' => $manual_receive_id, // receive_id
					'ref' => "Opening Balance",
					'note' => "Debit Opening Balance",
					'attachment' => null,
				]
			];


			$result = $this->Accounts_model->insert_with_saldo($data, $receive_doc, $trans_saldo_awal);
		} else {
			$result = $this->Accounts_model->insert($data);
		}
		if ($result) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
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

			$saldo = $this->Account_trans_model->get_saldo($r->account_id);

			if ($saldo < 0) {
				$color = "text-danger";
			} else {
				$color = "";
			}
			$href = "<a href='" . base_url('admin/finance/accounts/transactions?id=') . $r->account_id . "' class='font-weight-bold text-dark hoverable'>" . $r->account_name . "</a>";
			$data[] = [
				$r->account_code,
				$href,
				$category_name ?? "--",
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

	public function transfers()
	{
		$id = $this->input->get('id');

		$record = $this->Account_transfer_model->get_by_number_doc($id);


		$role_resources_ids = $this->Xin_model->user_role_resource();

		$title = "<strong>" . $this->lang->line('ms_title_transfer') . "</strong>";
		$data['title'] = $this->Xin_model->site_title();
		$data['breadcrumbs'] = $title;
		$data['path_url'] = 'finance/account_transfer';

		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/transfers", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function spends()
	{
		$title = "<strong>" . $this->lang->line('ms_title_spend') . "</strong>";

		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $title;
		$data['path_url'] = 'finance/account_spend';
		// $data['records']
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/finance/accounts/spends", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
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
		// $id = $this->input->get('id');
		// dd($id);

		// regex only integer
		// $id = preg_match('/^[0-9]+$/', $id) ? $id : false;
		// dd($id);
		if (in_array('503', $role_resources_ids)) {

			// pilih menu
			if ($type == "transfer") {

				$data['path_url'] = 'finance/account_transfer';
				$init = $this->Account_transfer_model->init_trans();

				$data['record'] = $init;
				$data['breadcrumbs'] = $this->lang->line('ms_title_transfer');
				$data['subview'] = $this->load->view("admin/finance/accounts/transfer_form", $data, TRUE);
				#
			} elseif ($type == "spend") {
				$data['path_url'] = 'finance/account_spend';
				$init = $this->Account_spend_model->init_trans();

				$data['record'] = $init;
				$data['breadcrumbs'] = $this->lang->line('ms_title_spend');
				$data['subview'] = $this->load->view("admin/finance/accounts/spend_form", $data, TRUE);
				#
			} elseif ($type == "receive") {
				$data['path_url'] = 'finance/account_receive';
				$init = $this->Account_receive_model->init_trans();

				$data['record'] = $init;
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


	public function update_trans()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		// $id = $this->security->xss_clean($this->input->post('_token'));
		$id = $this->input->post('_token');
		$type = $this->input->post('type');

		$action = $this->input->post('act_type');
		if ($type == 'transfer') {

			$from = $this->Accounts_model->get($this->input->post('account_id'));
			$to = $this->Accounts_model->get($this->input->post('target_account'));

			$desc = "Transfer from " . $from . " to " . $to;

			$data = [
				'account_id' => $this->input->post('account_id'),
				'terget_account_id' => $this->input->post('target_account'),
				'trans_number' => $this->input->post('trans_number'),
				'date' => $this->input->post('date'),
				'amount' => $this->input->post('amount'),
				'ref' => $this->input->post('ref'),
				'note' => $this->input->post('note'),
				'status' => $action == 'save' ? 'draft' : 'unpaid',
				'description' => $desc,
			];

			// upload file
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size'] = '1024'; // max_size in kb
			$config['file_name'] = $_FILES['attachment']['name'];

			//load upload class library
			$up = $this->load->library('upload', $config);

			$query = $this->Account_transfer_model->update($id, $data);

			if ($query) {
				$Return['result'] = $this->lang->line('ms_title_success_added');
				$this->output($Return);
			} else {
				$Return['error'] = $this->lang->line('ms_title_error');
				$this->output($Return);
			}
		}
	}

	public function store_trans()
	{
		// return $this->update_trans();
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		// $id = $this->security->xss_clean($this->input->post('_token'));
		$id = $this->input->post('_token');
		$type = $this->input->post('type');

		$user_id = $this->session->userdata()['username']['user_id'] ?? 0;

		$action = $this->input->post('act_type');
		if ($type == 'transfer') {

			$from = $this->Accounts_model->get($this->input->post('account_id'))->row();
			$to = $this->Accounts_model->get($this->input->post('target_account'))->row();

			$desc = "Transfer from " . $from->account_name . " to " . $to->account_name;

			$data = [
				'user_id' => $user_id,
				'account_id' => $this->input->post('account_id'),
				'terget_account_id' => $this->input->post('target_account'),
				'trans_number' => $this->input->post('trans_number'),
				'date' => $this->input->post('date'),
				'amount' => $this->input->post('amount'),
				'ref' => $this->input->post('ref'),
				'note' => $this->input->post('note'),
				'status' => $action == 'save' ? 'draft' : 'unpaid',
				'description' => $desc,
			];

			// upload file
			$config['allowed_types'] = 'gif|jpg|png|pdf';
			$config['max_size'] = '1024'; // max_size in kb

			$config['upload_path'] = './uploads/finance/account_transfer/';

			//load upload class library
			$this->load->library('upload', $config);

			$files = $_FILES['attachments'];
			$file_data = array();

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

					$file_data[] = array(
						'file_name' => $newName,
						'file_size' => $files['size'][$key],
						'file_type' => $files['type'][$key],
						'file_ext' => $extension,
						'file_access' => 1, // transfer
						'access_id' => $id
					);
				} else {
					$Return['error'] = $this->upload->display_errors();
					$this->output($Return);
				}
			}

			$query = $this->Account_transfer_model->update_with_files($id, $data, $file_data);

			if ($query) {
				$Return['result'] = $this->lang->line('ms_title_success_added');
				$this->output($Return);
			} else {
				$Return['error'] = $this->lang->line('ms_title_error');
				$this->output($Return);
			}
			#
			#
			#
		} else if ($type == 'spend') {
			#
			#
			$data = [
				'user_id' => $user_id,
				'account_id' => $this->input->post('account_id'),
				'trans_number' => $this->input->post('trans_number'),
				'beneficiary' => $this->input->post('beneficiary'),
				'date' => $this->input->post('date'),
				'reference' => $this->input->post('reference'),
				'due_date' => $this->input->post('date'),
				'status' => $action == 'save' ? 'draft' : 'unpaid',
			];

			// dd($data);
			$items = [];
			for ($i = 0; $i < count($this->input->post('row_amount')); $i++) {

				$items[] = [
					'spend_id' => $id,
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
							'file_access' => 2, // spend
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

			$query = $this->Account_spend_model->update_with_items_and_files($id, $data, $items, $file_attachments);

			if ($query) {
				$Return['result'] = $this->lang->line('ms_title_success_added');
				$this->output($Return);
			} else {
				$Return['error'] = $this->lang->line('ms_title_error');
				$this->output($Return);
			}
		} else if ($type == 'receive') {
			#
			#
			#
			$data = [
				'user_id' => $user_id,
				'vendor_id' => $this->input->post('vendor_id'),
				'receive_account_id' => $this->input->post('receive_account_id'),
				'trans_number' => $this->input->post('trans_number'),
				'date' => $this->input->post('date'),
				'reference' => $this->input->post('reference'),
				'status' => $action == 'save' ? 'draft' : 'unpaid',
			];

			$items = [];
			for ($i = 0; $i < count($this->input->post('row_amount')); $i++) {

				$items[] = [
					'receive_id' => $id,
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
							'file_access' => 2, // spend
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


			$query = $this->Account_receive_model->update_with_items_and_files($id, $data, $items, $file_attachments);

			if ($query) {
				$Return['result'] = $this->lang->line('ms_title_success_added');
				$this->output($Return);
			} else {
				$Return['error'] = $this->lang->line('ms_title_error');
				$this->output($Return);
			}
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
		$record = $this->Account_spend_model->get_by_number_doc($id);

		if (!is_null($record)) {
			$from = $this->Accounts_model->get($record->account_id)->row();
			$to = $this->Employees_model->read_employee_information($record->beneficiary);
			$record->source_account = $from->account_name;
			$record->beneficiary = $to[0]->first_name ?? "" . " " . $to[0]->last_name ?? "";

			//
			$attachments = $this->Files_ms_model->get_by_access_id(2, $record->spend_id)->result();
			$data['attachments'] = $attachments;

			//
			$items = $this->Account_spend_items_model->get($record->spend_id);

			//
			$get_trans_payment = $this->Account_spend_model->get_trans_payment($record->spend_id);
			$record->jumlah_dibayar = $get_trans_payment->jumlah_dibayar;
			$record->sisa_tagihan = $get_trans_payment->sisa_tagihan;
			$record->log_payments = $get_trans_payment->log_payments;

			// dd($record);
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
			redirect('admin/finance/accounts');
		}

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_spend');
		$data['path_url'] = 'finance/account_spend';
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
		$record = $this->Account_transfer_model->get_by_number_doc($id);

		if (!is_null($record)) {
			$from = $this->Accounts_model->get($record->account_id)->row();
			$to = $this->Accounts_model->get($record->terget_account_id)->row();
			// dd($record);
			$record->source_account = $from->account_name;
			$record->target_account = $to->account_name;

			//
			$make_payment = $this->Account_trans_model->open_payment($id, $record->account_id);

			$get_trans_payment = $this->Account_transfer_model->get_trans_payment($record->transfer_id);
			$record->jumlah_dibayar = $get_trans_payment->jumlah_dibayar;
			$record->sisa_tagihan = $get_trans_payment->sisa_tagihan;
			$record->log_payments = $get_trans_payment->log_payments;

			$attachments = $this->Files_ms_model->get_by_access_id(1, $record->transfer_id)->result();
			$data['attachments'] = $attachments;
		} else {
			redirect('admin/finance/accounts');
		}

		$data['make_payment'] = $make_payment ?? true;
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		// $data['breadcrumbs'] = "<strong>" . $record->account_code . "</strong>" . "&nbsp;&nbsp;" . $record->account_name;
		$data['breadcrumbs'] = $this->lang->line('ms_title_transfer');
		$data['path_url'] = 'finance/account_transfer';


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
		$record = $this->Account_receive_model->get_by_number_doc($id);
		// dd($record);
		if (!is_null($record)) {
			/// get vendor
			$vendor = $this->Vendor_model->read_vendor_information($record->vendor_id);
			if (!is_null($vendor)) {
				$vendor = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
			} else {
				$vendor = "--";
			}

			$record->vendor = $vendor;

			$attachments = $this->Files_ms_model->get_by_access_id(3, $record->receive_id)->result();
			$data['attachments'] = $attachments;


			//
			$get_trans_payment = $this->Account_receive_model->get_trans_payment($record->receive_id);
			$record->jumlah_dibayar = $get_trans_payment->jumlah_dibayar;
			$record->sisa_tagihan = $get_trans_payment->sisa_tagihan;
			$record->log_payments = $get_trans_payment->log_payments;

			// dd($get_trans_payment);
			//
			$items = $this->Account_receive_items_model->get($record->receive_id);
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
			redirect('admin/finance/accounts');
		}

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_receive');
		$data['path_url'] = 'finance/account_receive';
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


	public function transaction_view()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['breadcrumbs'] = $this->lang->line('ms_title_trans_view');
		$data['path_url'] = 'finance/trans_view';

		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Account_trans_model->get_trans($id);
		$record->user_paid = $record->first_name . "  " . $record->last_name;

		if (!is_null($record)) {
			$data['record'] = $record;
			if (in_array('503', $role_resources_ids)) {
				$data['subview'] = $this->load->view("admin/finance/accounts/transaction_view", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/dashboard');
			}
		} else {
			redirect('admin/finance/accounts');
		}
	}
	// Ajax Request

	public function get_ajax_trans_account()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->input->get('id');
		$record = $this->Account_trans_model->get($id);


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$balance = 0;
		foreach ($record->result() as $i => $r) {

			$trans_cat = $this->Account_trans_model->get_trans_type($r->account_trans_cat_id);
			if (!is_null($trans_cat)) {
				$r->trans_type = $trans_cat->trans_type;
			} else {
				$r->trans_type = "--";
			}


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
				"<a href='" . base_url('admin/finance/accounts/transaction_view?id=' . $r->account_trans_id . '&back_id=' . $r->account_id) . "' class='text-dark'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_type,
				$r->note,
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

	public function get_ajax_account_trans_doc()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$record = $this->Account_transfer_model->draft();
		$record_2 = $this->Account_spend_model->draft();
		$record_3 = $this->Account_receive_model->draft();

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$balance = 0;
		foreach ($record->result() as $i => $r) {

			$from_account = $this->Accounts_model->get($r->account_id)->row();
			if (!is_null($from_account)) {
				$from = "<b>$from_account->account_name</b>" . "  " . $from_account->account_code;
			} else {
				$from = "--";
			}

			// dd($r);
			$to_account = $this->Accounts_model->get($r->terget_account_id)->row();
			if (!is_null($to_account)) {
				$to = "<b>$to_account->account_name</b>" . "  " . $to_account->account_code;
			} else {
				$to = "--";
			}

			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/transfer_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $to . "</small>",
				$r->ref,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($r->amount),
			);
		}

		foreach ($record_2->result() as $i => $r) {

			if (!in_array(null, [$r->account_id, $r->beneficiary], true)) {

				$from_account = $this->Accounts_model->get($r->account_id)->row();
				if (!is_null($from_account)) {
					$from = "<b>$from_account->account_name</b>" . "  " . $from_account->account_code;
				} else {
					$from = "--";
				}

				$to_beneficiary = $this->Employees_model->read_employee_information($r->beneficiary);
				if (!is_null($to_beneficiary)) {
					$to = $to_beneficiary[0]->first_name . "  " . $to_beneficiary[0]->last_name;
				} else {
					$to = "--";
				}
			} else {
				$from = "--";
				$to = "--";
			}


			$amount = $this->Account_spend_items_model->get_total_amount($r->spend_id);
			if (!is_null($amount)) {
				$amount = $amount;
			} else {
				$amount = 0;
			}

			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/spend_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $to . "</small>",
				$r->reference,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($amount),
			);
		}

		foreach ($record_3->result() as $i => $r) {
			$receives = $this->Account_receive_items_model->get_account_from_receive($r->receive_id);
			$result_receives = "";
			foreach ($receives as $key => $rev) {
				$titik_kome = $key == array_key_last($receives) ? ". " : ", ";
				$result_receives .= "<b>$rev->account_name</b>" . "  " . $rev->account_code . $titik_kome;
			}

			$vendor = $this->Vendor_model->read_vendor_information($r->vendor_id);
			if (!is_null($vendor)) {
				$from = $vendor[0]->vendor_name;
			} else {
				$from = "--";
			}


			$amount = $this->Account_receive_items_model->get_total_amount($r->receive_id);
			if (!is_null($amount)) {
				$amount = $amount;
			} else {
				$amount = 0;
			}

			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/receive_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $result_receives . "</small>",
				$r->reference,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($amount),
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

	public function get_ajax_account_trans()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$record = $this->Account_transfer_model->all();
		$record_2 = $this->Account_spend_model->all();
		$record_3 = $this->Account_receive_model->all();

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$balance = 0;
		foreach ($record->result() as $i => $r) {

			$from_account = $this->Accounts_model->get($r->account_id)->row();
			if (!is_null($from_account)) {
				$from = "<b>$from_account->account_name</b>" . "  " . $from_account->account_code;
			} else {
				$from = "--";
			}

			// dd($r);
			$to_account = $this->Accounts_model->get($r->terget_account_id)->row();
			if (!is_null($to_account)) {
				$to = "<b>$to_account->account_name</b>" . "  " . $to_account->account_code;
			} else {
				$to = "--";
			}

			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/transfer_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $to . "</small>",
				$r->ref,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($r->amount),
			);
		}

		foreach ($record_2->result() as $i => $r) {

			if (!in_array(null, [$r->account_id, $r->beneficiary], true)) {

				$from_account = $this->Accounts_model->get($r->account_id)->row();
				if (!is_null($from_account)) {
					$from = "<b>$from_account->account_name</b>" . "  " . $from_account->account_code;
				} else {
					$from = "--";
				}

				$to_beneficiary = $this->Employees_model->read_employee_information($r->beneficiary);
				if (!is_null($to_beneficiary)) {
					$to = $to_beneficiary[0]->first_name . "  " . $to_beneficiary[0]->last_name;
				} else {
					$to = "--";
				}
			} else {
				$from = "--";
				$to = "--";
			}


			$amount = $this->Account_spend_items_model->get_total_amount($r->spend_id);
			if (!is_null($amount)) {
				$amount = $amount;
			} else {
				$amount = 0;
			}

			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/spend_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $to . "</small>",
				$r->reference,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($amount),
			);
		}

		foreach ($record_3->result() as $i => $r) {
			$receives = $this->Account_receive_items_model->get_account_from_receive($r->receive_id);
			$result_receives = "";
			foreach ($receives as $key => $rev) {
				$titik_kome = $key == array_key_last($receives) ? ". " : ", ";
				$result_receives .= "<b>$rev->account_name</b>" . "  " . $rev->account_code . $titik_kome;
			}

			$vendor = $this->Vendor_model->read_vendor_information($r->vendor_id);
			if (!is_null($vendor)) {
				$from = $vendor[0]->vendor_name;
			} else {
				$from = "--";
			}


			$amount = $this->Account_receive_items_model->get_total_amount($r->receive_id);
			if (!is_null($amount)) {
				$amount = $amount;
			} else {
				$amount = 0;
			}

			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/receive_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $result_receives . "</small>",
				$r->reference,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($amount),
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

	public function get_ajax_account_transfer()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$record = $this->Account_transfer_model->all();


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$balance = 0;
		foreach ($record->result() as $i => $r) {

			$from_account = $this->Accounts_model->get($r->account_id)->row();
			if (!is_null($from_account)) {
				$from = "<b>$from_account->account_name</b>" . "  " . $from_account->account_code;
			} else {
				$from = "--";
			}

			// dd($r);
			$to_account = $this->Accounts_model->get($r->terget_account_id)->row();
			if (!is_null($to_account)) {
				$to = "<b>$to_account->account_name</b>" . "  " . $to_account->account_code;
			} else {
				$to = "--";
			}


			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/transfer_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $to . "</small>",
				$r->ref,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($r->amount),
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

	public function get_ajax_account_spends()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$record = $this->Account_spend_model->all();


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$balance = 0;
		foreach ($record->result() as $i => $r) {

			if (!in_array(null, [$r->account_id, $r->beneficiary], true)) {

				$from_account = $this->Accounts_model->get($r->account_id)->row();
				if (!is_null($from_account)) {
					$from = "<b>$from_account->account_name</b>" . "  " . $from_account->account_code;
				} else {
					$from = "--";
				}

				$to_beneficiary = $this->Employees_model->read_employee_information($r->beneficiary);
				if (!is_null($to_beneficiary)) {
					$to = $to_beneficiary[0]->first_name . "  " . $to_beneficiary[0]->last_name;
				} else {
					$to = "--";
				}
			} else {
				$from = "--";
				$to = "--";
			}


			$amount = $this->Account_spend_items_model->get_total_amount($r->spend_id);
			if (!is_null($amount)) {
				$amount = $amount;
			} else {
				$amount = 0;
			}

			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/spend_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $to . "</small>",
				$r->reference,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($amount),
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

	public function get_ajax_account_receives()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$record = $this->Account_receive_model->all();


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$balance = 0;
		foreach ($record->result() as $i => $r) {
			$receives = $this->Account_receive_items_model->get_account_from_receive($r->receive_id);
			$result_receives = "";
			foreach ($receives as $key => $rev) {
				$titik_kome = $key == array_key_last($receives) ? ". " : ",<br>";
				$result_receives .= "<b>$rev->account_name</b>" . "  " . $rev->account_code . $titik_kome;
			}

			$vendor = $this->Vendor_model->read_vendor_information($r->vendor_id);
			if (!is_null($vendor)) {
				$from = $vendor[0]->vendor_name;
			} else {
				$from = "--";
			}


			$amount = $this->Account_receive_items_model->get_total_amount($r->receive_id);
			if (!is_null($amount)) {
				$amount = $amount;
			} else {
				$amount = 0;
			}

			$data[] = array(
				"<a href='" . base_url('admin/finance/accounts/receive_view?id=' . $r->trans_number) . "' class='text-secondary'><i class='fa fa-eye fa-fw' aria-hidden='true'></i></a>",
				$this->Xin_model->set_date_format($r->created_at),
				$r->trans_number,
				"<small>" . $from . "<br>To<br>" . $result_receives . "</small>",
				$r->reference,
				$r->status == "paid" ? "<span class='badge badge-success'>" . $r->status . "</span>" : "<span class='badge badge-warning'>" . $r->status . "</span>",
				$this->Xin_model->currency_sign($amount),
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

	public function getetet()
	{
		$id = $this->input->get('id');
		// if ($this->checkPattern($id, "TR")) {
		// 	$records = $this->Account_transfer_model->get_all_trans_by_trans_number($id);
		// } else {
		// 	redirect('admin/finance/accounts');
		// }

		// dd($id);
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$records = $this->Account_trans_model->get($id);
		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();

		foreach ($records->result() as $r) {

			// get category
			$category = $this->Account_categories_model->get($r->account_trans_cat_id);
			if (!is_null($category)) {
				$category_name = $category->category_name;
			} else {
				$category_name = '--';
			}

			// $trans = $this->Account_trans_model->get($r->account_id);
			$saldo = 0;

			if ($r->type == 'credit') {
				$saldo += $r->amount;
			} else {
				$saldo -= $r->amount;
			}

			$href = "<a href='" . base_url('admin/finance/account/view/') . $r->account_id . "' class='font-weight-bold text-dark hoverable'>" . $r->account_name . "</a>";
			$data[] = [
				$r->account_code,
				$href,
				$category_name ?? "--",
				$this->Xin_model->currency_sign($saldo),
			];
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $records->num_rows(),
			"recordsFiltered" => $records->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function export_trans()
	{
		$type = $this->input->get('type') ?? false;
		$id = $this->input->get('id');
	}


	public function transfer_print()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Account_transfer_model->get_by_number_doc($id);

		if (!is_null($record)) {
			$from = $this->Accounts_model->get($record->account_id)->row();
			$to = $this->Accounts_model->get($record->terget_account_id)->row();
			$record->source_account = $from->account_code . " / " . $from->account_name;
			$record->target_account = $to->account_code . " / " . $to->account_name;

			$user_data = $this->Employees_model->read_employee_information($record->user_id);
			$record->user_created = $user_data[0]->first_name . "  " . $user_data[0]->last_name;

			$user_data_2 = $this->Employees_model->read_employee_information($user_data[0]->reports_to);
			$record->user_approved = $user_data_2[0]->first_name . "  " . $user_data_2[0]->last_name;

			$get_trans_payment = $this->Account_transfer_model->get_trans_payment($record->transfer_id);
			$record->jumlah_dibayar = $get_trans_payment->jumlah_dibayar;
			$record->sisa_tagihan = $get_trans_payment->sisa_tagihan;
		} else {
			redirect('admin/finance/accounts');
		}


		$data['title'] = $this->Xin_model->site_title();

		// $data['breadcrumbs'] = "<strong>" . $record->account_code . "</strong>" . "&nbsp;&nbsp;" . $record->account_name;
		$data['breadcrumbs'] = $this->lang->line('ms_title_transfer');
		$data['path_url'] = 'finance/account_transfer';


		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {
			$html = $this->load->view("admin/finance/accounts/transfer_print", $data, true);

			// return $html; //page load
			$mpdf = new \Mpdf\Mpdf([
				'orientation' => 'P',
				'margin_top' => 10,
				'margin_left' => 10,
				'margin_right' => 10,
				'format' => 'A4-P',
				'default_font' => 'plusjakartasans'
			]);

			$mpdf->WriteHTML($html);
			$mpdf->Output($record->trans_number . "_" . $record->date . '.pdf', 'I');
		} else {
			redirect('admin/dashboard');
		}
	}

	public function spend_print()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Account_spend_model->get_by_number_doc($id);

		if (!is_null($record)) {
			$from = $this->Accounts_model->get($record->account_id)->row();
			$to = $this->Employees_model->read_employee_information($record->beneficiary);
			$record->source_account = $from->account_code . " / " . $from->account_name;
			$record->beneficiary = $to[0]->first_name . " " . $to[0]->last_name;

			$user_data = $this->Employees_model->read_employee_information($record->user_id);
			$record->user_created = $user_data[0]->first_name . "  " . $user_data[0]->last_name;

			$user_data_2 = $this->Employees_model->read_employee_information($user_data[0]->reports_to);
			$record->user_approved = $user_data_2[0]->first_name . "  " . $user_data_2[0]->last_name;
			//
			$items = $this->Account_spend_items_model->get($record->spend_id);

			//
			$get_trans_payment = $this->Account_spend_model->get_trans_payment($record->spend_id);
			$record->jumlah_dibayar = $get_trans_payment->jumlah_dibayar;
			$record->amount = $get_trans_payment->jumlah_tagihan;
			$record->sisa_tagihan = $get_trans_payment->sisa_tagihan;

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
			redirect('admin/finance/accounts');
		}

		$data['title'] = $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_title_spend');
		$data['path_url'] = 'finance/account_spend';
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}


		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {



			$html = $this->load->view("admin/finance/accounts/spend_print", $data, true); //page load
			$mpdf = new \Mpdf\Mpdf([
				'orientation' => 'P',
				'margin_top' => 10,
				'margin_left' => 10,
				'margin_right' => 10,
				'format' => 'A4-P',
				'default_font' => 'plusjakartasans'
			]);

			$type = $this->input->get('type');
			// dd($type);
			if ($type == "export") {
				// download 
				$mpdf->WriteHTML($html);
				$mpdf->Output($record->trans_number . "_" . $record->date . '.pdf', \Mpdf\Output\Destination::DOWNLOAD);
			} else {
				$mpdf->WriteHTML($html);
				$mpdf->Output($record->trans_number . "_" . $record->date . '.pdf', 'I');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function receive_print()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->input->get('id');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Account_receive_model->get_by_number_doc($id);

		if (!is_null($record)) {

			$receives = $this->Account_receive_items_model->get_account_from_receive($record->receive_id);
			$result_receives = "";
			foreach ($receives as $key => $rev) {
				$titik_kome = $key == array_key_last($receives) ? ". " : ",<br>";
				$result_receives .= "<b>$rev->account_name</b>" . "  " . $rev->account_code . $titik_kome;
			}

			$record->to_account = $result_receives;

			/// get vendor
			$vendor = $this->Vendor_model->read_vendor_information($record->vendor_id);
			if (!is_null($vendor)) {
				$record->source_account = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
			} else {
				$record->source_account = "--";
			}

			$record->vendor = $vendor;

			$user_data = $this->Employees_model->read_employee_information($record->user_id);
			$record->user_created = $user_data[0]->first_name . "  " . $user_data[0]->last_name;

			$user_data_2 = $this->Employees_model->read_employee_information($user_data[0]->reports_to);
			$record->user_approved = $user_data_2[0]->first_name . "  " . $user_data_2[0]->last_name;
			//
			$items = $this->Account_receive_items_model->get($record->receive_id);

			//
			$get_trans_payment = $this->Account_receive_model->get_trans_payment($record->receive_id);
			$record->jumlah_dibayar = $get_trans_payment->jumlah_dibayar;
			$record->amount = $get_trans_payment->jumlah_tagihan;
			$record->sisa_tagihan = $get_trans_payment->sisa_tagihan;

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
			redirect('admin/finance/accounts');
		}

		$data['title'] = $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_title_receive');
		$data['path_url'] = 'finance/account_receive';
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}


		$data['record'] = $record;
		if (in_array('503', $role_resources_ids)) {



			$html = $this->load->view("admin/finance/accounts/receive_print", $data, true); //page load
			$mpdf = new \Mpdf\Mpdf([
				'orientation' => 'P',
				'margin_top' => 10,
				'margin_left' => 10,
				'margin_right' => 10,
				'format' => 'A4-P',
				'default_font' => 'plusjakartasans'
			]);

			$type = $this->input->get('type');
			// dd($type);
			if ($type == "export") {
				// download 
				$mpdf->WriteHTML($html);
				$mpdf->Output($record->trans_number . "_" . $record->date . '.pdf', \Mpdf\Output\Destination::DOWNLOAD);
			} else {
				$mpdf->WriteHTML($html);
				$mpdf->Output($record->trans_number . "_" . $record->date . '.pdf', 'I');
			}
		} else {
			redirect('admin/dashboard');
		}
	}


	// draft_doc
	public function draft_doc()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->Xin_model->site_title();

		$title = "<strong>" . $this->lang->line('ms_title_draft_document') . "</strong>";
		$data['breadcrumbs'] = $title;
		$data['path_url'] = 'finance/trans_doc';

		$data['subview'] = $this->load->view("admin/finance/accounts/trans_doc_draft", $data, TRUE);
		$this->load->view('admin/layout/layout_main', $data); //page load
	}


	public function trans_doc()
	{

		$type = $this->input->get('type');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->Xin_model->site_title();

		if ($type == 'transfer') {
			#
			#
			$title = "<strong>" . $this->lang->line('ms_title_transfer') . "</strong>";
			$data['breadcrumbs'] = $title;
			$data['path_url'] = 'finance/account_transfer';

			$data['subview'] = $this->load->view("admin/finance/accounts/transfers", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else if ($type == 'spend') {
			#
			#
			$title = "<strong>" . $this->lang->line('ms_title_spend') . "</strong>";
			$data['breadcrumbs'] = $title;
			$data['path_url'] = 'finance/account_spend';

			$data['subview'] = $this->load->view("admin/finance/accounts/spends", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else if ($type == 'receive') {
			#
			#
			$title = "<strong>" . $this->lang->line('ms_title_spend') . "</strong>";
			$data['breadcrumbs'] = $title;
			$data['path_url'] = 'finance/account_receive';

			$data['subview'] = $this->load->view("admin/finance/accounts/receives", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			#
			#
			$title = "<strong>" . $this->lang->line('ms_title_all_trans_doc') . "</strong>";
			$data['breadcrumbs'] = $title;
			$data['path_url'] = 'finance/trans_doc';

			$data['subview'] = $this->load->view("admin/finance/accounts/trans_doc", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		}
	}

	public function store_payment()
	{
		// transfer_id
		$id = $this->input->post('_token');
		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');
		$attachment = $_FILES['attachment'];
		$source_payment_account = $this->input->post('source_payment_account');
		$amount_paid = $this->input->post('amount_paid');


		// get user id
		$user_id = $this->session->userdata('username')['user_id'];

		// penerima debit
		$target_account_id = $this->input->post('target_account_id');

		// doing attachment
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size'] = '10240'; // max_size in kb
		$config['upload_path'] = './uploads/finance/account_trans/';

		$filename = $_FILES['attachment']['name'];

		// get extention
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$newName = date('YmdHis') . '_TRANS.' . $extension;

		$config['filename'] = $newName;

		//load upload class library
		$this->load->library('upload', $config);

		// $upload
		$upload = $this->upload->do_upload('attachment');

		$data = [
			[
				'account_id' => $source_payment_account,
				'user_id' => $user_id,
				'account_trans_cat_id' => 1,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'credit',
				'join_id' => $id, // transfer_id
				'ref' => $payment_ref,
				'note' => "Kredit Pembayaran Transfer",
				'attachment' => $newName,
			],
			[
				'account_id' => $target_account_id,
				'user_id' => $user_id,
				'account_trans_cat_id' => 1,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $id, // transfer_id
				'ref' => $payment_ref,
				'note' => "Debit Pembayaran Transfer",
				'attachment' => $newName,
			]
		];

		$insert = $this->Account_trans_model->insert_payment($data);

		// check if all tagihan is paid or partially paid
		$check_tagihan = $this->Account_transfer_model->get_trans_payment($id);

		if ($check_tagihan->sisa_tagihan == 0) {
			// update status transfer
			$this->Account_transfer_model->update($id, ['status' => 'paid']);
		} else {
			// update status transfer
			$this->Account_transfer_model->update($id, ['status' => 'partially_paid']);
		}

		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}

	public function store_spend_payment()
	{
		// spend id
		$id = $this->input->post('_token');
		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');
		$source_payment_account = $this->input->post('source_payment_account'); // vendor id

		// get user id
		$user_id = $this->session->userdata('username')['user_id'];

		// doing attachment
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size'] = '10240'; // max_size in kb
		$config['upload_path'] = './uploads/finance/account_trans/';

		$filename = $_FILES['attachment']['name'];

		// get extention
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$newName = date('YmdHis') . '_TRANS.' . $extension;

		$config['filename'] = $newName;

		//load upload class library
		$this->load->library('upload', $config);

		// $upload
		$this->upload->do_upload('attachment');

		// ambil tagihan
		$list_tagihan = $this->Account_spend_model->get_list_tagihan($id);

		// uang yang dibayar
		$amount_paid = $this->input->post('amount_paid');

		$data = [];
		foreach ($list_tagihan as $bill) {

			// filter jika sudah paid
			if (true) {
				// $totalBillAmount = floatval($bill->tax_rate) + floatval($bill->amount) - floatval($bill->bill_paid);
				$totalBillAmount = $bill->bill_remaining;

				if ($amount_paid >= $totalBillAmount) {
					// Jika pembayaran cukup untuk tagihan saat ini
					$make_bill = $totalBillAmount;
					$amount_paid -= $totalBillAmount;
				} else {
					// Jika pembayaran tidak cukup untuk tagihan saat ini
					$make_bill = $amount_paid;
					$amount_paid = 0;
				}

				//
				$data[] =
					[
						'account_id' => $source_payment_account,
						'user_id' => $user_id,
						'account_trans_cat_id' => 2,
						'amount' => $make_bill,
						'date' => $date,
						'type' => 'credit',
						'join_id' => $id, // spend_id
						'ref_trans_id' => $bill->spend_trans_id,
						'ref' => $payment_ref,
						'note' => "Kredit Pembayaran Spend",
						'attachment' => $newName,
					];
				$data[] = [
					'account_id' => $bill->account_id,
					'user_id' => $user_id,
					'account_trans_cat_id' => 2,
					'amount' => $make_bill,
					'date' => $date,
					'type' => 'debit',
					'join_id' => $id, // Spend_id
					'ref_trans_id' => $bill->spend_trans_id,
					'ref' => $payment_ref,
					'note' => "Debit Pembayaran Spend",
					'attachment' => $newName,
				];

				if ($amount_paid <= 0) {
					// Jika pembayaran sudah habis
					break;
				}
			}
		}

		// dd($data);
		$insert = $this->Account_trans_model->insert_payment($data);

		// check if all tagihan is paid or partially paid
		$check_tagihan = $this->Account_spend_model->get_trans_payment($id);

		// dd($check_tagihan);
		if ($check_tagihan->sisa_tagihan == 0) {
			// update status spend
			$this->Account_spend_model->update($id, ['status' => 'paid']);
		} else {
			// update status spend
			$this->Account_spend_model->update($id, ['status' => 'partially_paid']);
		}

		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}

	public function store_receive_payment()
	{
		// receive id
		$id = $this->input->post('_token');
		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');
		$receive_account_id = $this->input->post('receive_account_id');

		// get user id
		$user_id = $this->session->userdata('username')['user_id'];

		// doing attachment
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size'] = '10240'; // max_size in kb
		$config['upload_path'] = './uploads/finance/account_trans/';

		$filename = $_FILES['attachment']['name'];

		// get extention
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$newName = date('YmdHis') . '_TRANS.' . $extension;

		$config['filename'] = $newName;

		//load upload class library
		$this->load->library('upload', $config);

		// $upload
		$this->upload->do_upload('attachment');

		// ambil tagihan
		$list_tagihan = $this->Account_receive_model->get_list_tagihan($id);

		// dd($list_tagihan);
		// uang yang dibayar
		$amount_paid = $this->input->post('amount_paid');

		$data = [];
		foreach ($list_tagihan as $bill) {

			// filter jika sudah paid
			if (true) {
				// $totalBillAmount = floatval($bill->tax_rate) + floatval($bill->amount) - floatval($bill->bill_paid);
				$totalBillAmount = $bill->bill_remaining;

				if ($amount_paid >= $totalBillAmount) {
					// Jika pembayaran cukup untuk tagihan saat ini
					$make_bill = $totalBillAmount;
					$amount_paid -= $totalBillAmount;
				} else {
					// Jika pembayaran tidak cukup untuk tagihan saat ini
					$make_bill = $amount_paid;
					$amount_paid = 0;
				}

				//
				$data[] =
					[
						'account_id' => $bill->account_id,
						'user_id' => $user_id,
						'account_trans_cat_id' => 3, //receive
						'amount' => $make_bill,
						'date' => $date,
						'type' => 'credit',
						'join_id' => $id, // receive_id
						'ref_trans_id' => $bill->receive_trans_id,
						'ref' => $payment_ref,
						'note' => "Kredit Pembayaran receive",
						'attachment' => $newName,
					];
				$data[] = [
					'account_id' => $receive_account_id,
					'user_id' => $user_id,
					'account_trans_cat_id' => 3, // receive
					'amount' => $make_bill,
					'date' => $date,
					'type' => 'debit',
					'join_id' => $id, // receive_id
					'ref_trans_id' => $bill->receive_trans_id,
					'ref' => $payment_ref,
					'note' => "Debit Pembayaran receive",
					'attachment' => $newName,
				];

				if ($amount_paid <= 0) {
					// Jika pembayaran sudah habis
					break;
				}
			}
		}

		// dd($data);
		$insert = $this->Account_trans_model->insert_payment($data);

		// check if all tagihan is paid or partially paid
		$check_tagihan = $this->Account_receive_model->get_trans_payment($id);

		if ($check_tagihan->sisa_tagihan == 0) {
			// update status receive
			$this->Account_receive_model->update($id, ['status' => 'paid']);
		} else {
			// update status receive
			$this->Account_receive_model->update($id, ['status' => 'partially_paid']);
		}

		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}

	public function get_list_tagihan($id)
	{


		$jumlah_pembayaran = 10000;
		$list_tagihan = $this->Account_spend_model->get_list_tagihan($id);
		do {

			// pay each tagihan
			// foreach ($list_tagihan as $tagihan) {

			$tagihan = $this->Account_spend_model->get_tagihan_terkecil();
			if ($tagihan) {
				$sisa_pembayaran = $tagihan->jumlah_tagihan - $tagihan->sudah_dibayar;

				if ($jumlah_pembayaran >= $sisa_pembayaran) {
					$this->Account_spend_model->update_tagihan($tagihan->id_tagihan, $tagihan->jumlah_tagihan);
					$jumlah_pembayaran -= $sisa_pembayaran;
				} else {
					$this->Account_spend_model->update_tagihan($tagihan->id_tagihan, $tagihan->sudah_dibayar + $jumlah_pembayaran);
					$jumlah_pembayaran = 0;
				}
			} else {
				// Tidak ada tagihan lagi
				break;
			}
			// }
		} while ($jumlah_pembayaran > 0);
	}

	public function spend_payment()
	{
		$id = $this->input->post('_token');
		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');
		$source_payment_account = $this->input->post('source_payment_account');
		$amount_paid = $this->input->post('amount_paid');

		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size'] = '10240'; // max_size in kb
		$config['upload_path'] = './uploads/finance/account_trans/';

		//load upload class library
		$this->load->library('upload', $config);

		$filename = $_FILES['attachment']['name'];

		// get extention
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$newName = date('YmdHis') . '_TRANS.' . $extension;

		// $upload
		$this->upload->do_upload('attachment');


		// ambil data dokumen 
		$doc = $this->Account_spend_model->get($id);

		// get list tagihan
		$list_tagihan = $this->Account_spend_model->get_list_tagihan($doc->spend_id);

		while ($amount_paid == 0) {
		}
		$data = [
			[
				'account_id' => $source_payment_account,
				'account_trans_cat_id' => 1,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'credit',
				'join_id' => $id, // transfer_id
				'ref' => $payment_ref,
				'note' => "--",
				'attachment' => $newName,
			],
			[
				'account_id' => $target_account_id,
				'account_trans_cat_id' => 1,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $id, // transfer_id
				'ref' => $payment_ref,
				'note' => "--",
				'attachment' => $newName,
			]
		];


		// dd($data);
		// $debit = [];
		// if (is_array($target_account_id)) {
		// 	foreach ($target_account_id as $key => $val) {
		// 		$debit[] = [
		// 			'account_id' => $source_payment_account,
		// 			'account_trans_cat_id' => 1,
		// 			'amount' => $amount_paid,
		// 			'date' => $date,
		// 			'type' => 'debit',
		// 			'join_id' => $id, // transfer_id
		// 			'ref' => $payment_ref,
		// 			'note' => "--",
		// 			'attachment' => $attachment,
		// 		];
		// 	}
		// }



		$insert = $this->Account_trans_model->insert_payment($data);
		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}

	public function store_payment_purchasing()
	{
		// spend id
		$id = $this->input->post('_token');
		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');
		$source_payment_account = $this->input->post('source_payment_account'); // vendor id

		// get user id
		$user_id = $this->session->userdata('username')['user_id'];

		// doing attachment
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size'] = '10240'; // max_size in kb
		$config['upload_path'] = './uploads/finance/account_trans/';

		$filename = $_FILES['attachment']['name'];

		// get extention
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$newName = date('YmdHis') . '_TRANS.' . $extension;

		$config['filename'] = $newName;

		//load upload class library
		$this->load->library('upload', $config);

		// $upload
		$this->upload->do_upload('attachment');

		// ambil tagihan
		$list_tagihan = $this->Account_spend_model->get_list_tagihan($id);

		// uang yang dibayar
		$amount_paid = $this->input->post('amount_paid');

		$data = [];
		foreach ($list_tagihan as $bill) {

			// filter jika sudah paid
			if (true) {
				// $totalBillAmount = floatval($bill->tax_rate) + floatval($bill->amount) - floatval($bill->bill_paid);
				$totalBillAmount = $bill->bill_remaining;

				if ($amount_paid >= $totalBillAmount) {
					// Jika pembayaran cukup untuk tagihan saat ini
					$make_bill = $totalBillAmount;
					$amount_paid -= $totalBillAmount;
				} else {
					// Jika pembayaran tidak cukup untuk tagihan saat ini
					$make_bill = $amount_paid;
					$amount_paid = 0;
				}

				//
				$data[] =
					[
						'account_id' => $source_payment_account,
						'user_id' => $user_id,
						'account_trans_cat_id' => 2,
						'amount' => $make_bill,
						'date' => $date,
						'type' => 'credit',
						'join_id' => $id, // spend_id
						'ref_trans_id' => $bill->spend_trans_id,
						'ref' => $payment_ref,
						'note' => "Kredit Pembayaran Spend",
						'attachment' => $newName,
					];
				$data[] = [
					'account_id' => $bill->account_id,
					'user_id' => $user_id,
					'account_trans_cat_id' => 2,
					'amount' => $make_bill,
					'date' => $date,
					'type' => 'debit',
					'join_id' => $id, // Spend_id
					'ref_trans_id' => $bill->spend_trans_id,
					'ref' => $payment_ref,
					'note' => "Debit Pembayaran Spend",
					'attachment' => $newName,
				];

				if ($amount_paid <= 0) {
					// Jika pembayaran sudah habis
					break;
				}
			}
		}

		// dd($data);
		$insert = $this->Account_trans_model->insert_payment($data);

		// check if all tagihan is paid or partially paid
		$check_tagihan = $this->Account_spend_model->get_trans_payment($id);

		// dd($check_tagihan);
		if ($check_tagihan->sisa_tagihan == 0) {
			// update status spend
			$this->Account_spend_model->update($id, ['status' => 'paid']);
		} else {
			// update status spend
			$this->Account_spend_model->update($id, ['status' => 'partially_paid']);
		}

		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}
}
