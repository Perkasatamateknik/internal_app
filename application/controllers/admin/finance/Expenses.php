<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
		$this->load->model('Contact_model');

		$this->redirect_uri = 'admin/finance/expense';
		$this->redirect_access = 'admin/';
	}


	public function export_excell()
	{
		ob_start();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Nama');
		$sheet->setCellValue('C1', 'Kelas');
		$sheet->setCellValue('D1', 'Jenis Kelamin');
		$sheet->setCellValue('E1', 'Alamat');

		$filename = 'laporan-siswa';

		// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		// header('Content-Disposition: attachment;filename=halo.xls');
		// header('Cache-Control: max-age=0');

		// // If you're serving to IE over SSL, then the following may be needed
		// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		// header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		// header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		// header('Pragma: private'); // HTTP/1.0



		// $writer = new Xlsx($spreadsheet);
		// $sheet->setTitle("Laporan Data Siswa"); // Proses file excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Data Siswa.Xlsx"');
		// Set nama file excel nya
		header('Cache-Control: max-age=0');
		ob_end_clean();
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	public function import_excell()
	{

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '', 'validate_error' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();


		$config['upload_path'] = './uploads/temp';
		$config['allowed_types'] = 'xls|xlsx';
		$config['max_size'] = 10000;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('file')) {
			$Return['validate_error'] = $this->upload->display_errors();
			$this->output($Return);
		}

		$data = $this->upload->data();
		$file_path = $data['full_path'];

		// Menggunakan IOFactory untuk menentukan reader berdasarkan ekstensi file
		$extension = pathinfo($file_path, PATHINFO_EXTENSION);

		// $reader = IOFactory::createReader('Xlsx');
		// $spreadsheet = $reader->load($file_path);
		$spreadsheet = IOFactory::load($file_path);
		$sheetData = $spreadsheet->getActiveSheet()->toArray();

		$is_validate = $this->validate_import($sheetData);

		// Hapus file yang diunggah
		unlink($file_path);
		$split_combine = $this->splitAndCombine($sheetData);

		// dd($split_combine);
		if ($is_validate == true) {
			$split_combine = $this->splitAndCombine($sheetData);
			$import_batch = $this->Expense_model->import_batch($split_combine);

			if ($import_batch) {
				$Return['result'] = $this->lang->line('ms_title_success_imported');
				$this->output($Return);
			}
		}

		$Return['error'] = $this->lang->line('ms_title_error');
		$this->output($Return);
	}

	function validate_import($inputData)
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '', 'validate_error' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$rowIndex = 2;

		// Variabel untuk menampung error
		$error = [];
		if (count($inputData) == 0) {
			$error[] = "Data is empty!";
		}

		foreach ($inputData as $i => $row) {
			// Skip Row Pertama
			if ($i == 0) {
				continue;
			}

			$contact_id = $row[0];
			$trans_number = str_replace('/', '-', $row[2]);
			$date = $row[3];
			$due_date = $row[4];
			$ref = $row[5];

			$account_item = $row[6];
			$note = $row[7];
			$ref_tax = $row[8];
			$amount = (float) $row[9];
			$is_billed = $row[10];
			$account_id = $row[11];


			$contact = $this->Contact_model->find_contact_by_id($contact_id);
			if (is_null($contact)) {
				$error[] =  "A" . $rowIndex . " | " . "Kontak <b>" . $contact_id . "</b> tidak ditemukan";
			}

			if (is_null($trans_number)) {
				$error[] =  "C" . $rowIndex . " | " . "Nomor Dokumen wajib di isi!<br>";
			}

			$check_format = validate_expense_format($trans_number);
			if ($check_format) {
				$error[] =  "C" . $rowIndex . " | " .  "Format Nomor Biaya " . $row[2] . " tidak sesuai!";
			}

			$check = $this->Expense_model->get_by_number_doc($trans_number);
			if ($check) {
				$error[] =  "C" . $rowIndex . " | " .  "Duplikat " . $trans_number;
			}

			if (is_null($date)) {
				$error[] =  "D" . $rowIndex . " | " . "Tanggal wajib di isi!";
			}

			if (is_null($due_date)) {
				$error[] =  "E" . $rowIndex . " | " . "Tanggal jatuh tempo wajib di isi!";
			}

			$data_account_item = $this->Accounts_model->get_account_by_account_code($account_item);
			if (is_null($data_account_item)) {
				$error[] = "G" . $rowIndex . " | " .  "Akun <b>" . $account_item . "</b> tidak ditemukan!";
			}

			if (!is_null($ref_tax)) {
				$tax_data = $this->Tax_model->find_best_match($ref_tax);
				if (is_null($tax_data)) {
					$error[] =  "I" . $rowIndex . " | " .  "Pajak <b>" . $ref_tax . "</b> tidak ditemukan!";
				} else {
					if ($tax_data->type == 'fixed' && $tax_data->rate > $amount) {
						$error[] =  "I" . $rowIndex . " | " .  "Nilai pajak <b>" . $ref_tax . "</b> lebih besar dari total!";
					}
				}
			}

			if (is_null($amount)) {
				$error[] = "J" . $rowIndex . " | " .   "Amount wajib di isi!";
			}

			if ($amount < 0) {
				$error[] =  "J" . $rowIndex . " | " .  "Amount tidak boleh kurang dari 0!";
			}

			if (!in_array($is_billed, ['Ya', 'Tidak'])) {
				$error[] =  "K" . $rowIndex . " | " .  "Status dibayar tidak sesuai format!";
			}

			$account = $this->Accounts_model->get_account_by_account_code($account_id);
			if (is_null($account)) {
				$error[] =  "L" . $rowIndex . " | " . "Akun <b>" . $account_id . "</b> tidak ditemukan!";
			}

			$rowIndex++;
		}

		// return 
		if (count($error) != 0) {
			$view_error = $this->error_validate($error);
			$Return['validate_error'] = $view_error;
			$this->output($Return);
			exit;
		} else {
			return true;
		}
	}

	public function error_validate($data)
	{

		$html = '';
		$html .=  "<table class='table table-sm table-borderless'>";
		$html .= "<tr><td colspan='3'><h4>Terjadi kesalahan import pada :</h4></td></tr>";
		foreach ($data as $r) {
			$split = explode("|", $r);
			$html .= "<tr>";
			// $html .= "<td> &bull; </td>";
			$html .= "<td><strong>$split[0]</strong></td>";
			$html .= "<td> : </td>";
			$html .= "<td class='text-danger'>" . $split[1] . "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";

		return $html;
	}

	public function error_upload($data)
	{
		$html = '';
		$html .=  "<table class='table table-sm table-borderless'>";
		$html .= "<tr><td colspan='3'><h4>Oops. Terjadi kesalahan!</h4></td></tr>";
		foreach ($data as $key => $val) {
			$html .= "<tr>";
			// $html .= "<td> &bull; </td>";
			$html .= "<td><strong>$key</strong></td>";
			$html .= "<td> : </td>";
			$html .= "<td class='text-danger'>" . $val . "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";

		return $html;
	}

	function splitAndCombine($inputData)
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$user_id = $this->session->userdata()['username']['user_id'] ?? 0;

		$tagihan = [];
		$data = [];
		$items = [];
		$trans = [];

		$end_digit = rand(100000, 999999);

		foreach ($inputData as $i => $row) {
			// Skip Row Pertama
			if ($i == 0) {
				continue;
			}

			$contact_id = $row[0];
			$trans_number = str_replace('/', '-', $row[2]);
			// replace date

			$date = date('Y-m-d', strtotime(str_replace('/', '-', $row[3])));
			$due_date = date('Y-m-d', strtotime(str_replace('/', '-', $row[4])));
			$ref = $row[5];

			$account_item = $row[6];
			$note = $row[7];
			$ref_tax = $row[8];
			$amount = (float) $row[9];
			$is_billed = $row[10];
			$account_id = $row[11];

			$contact_name = '';

			$key = $trans_number; // Menggabungkan berdasarkan Nomor EXP
			if (is_null($key)) {
				// skipp if null data
				continue;
			}

			$contact = $this->Contact_model->find_contact_by_id($contact_id);
			$contact_id = $contact->contact_id;
			$contact_name = $contact->contact_name;



			// ref trans id
			$ref_trans_id = $trans_number . "-" . $end_digit;

			if (!isset($tagihan[$key])) {

				// Inisialisasi tagihan jika belum ada
				$tagihan[$key] = [
					'amount_tax' => 0,
					'amount_item' => 0,
					'amount' => 0,
				];

				$account = $this->Accounts_model->get_account_by_account_code($account_id);
				$account_id = $account->account_id;

				$data[] = [
					'account_id' => $account_id,
					'beneficiary' => $contact_id,
					'trans_number' => $trans_number,
					'date' => $date,
					'due_date' => $due_date,
					'reference' => $ref,
					'status' => 'unpaid'
				];

				// masukan semua total ke akun Trade Payble = credit
				$trans[$key] = [
					'account_id' => 34, // account_id trade payable
					'user_id' => $user_id,
					'account_trans_cat_id' => 4, // = expense
					'amount' => 0,
					'date' => $date,
					'type' => 'credit',
					'join_id' => $trans_number, // po number
					'ref' => "Expense",
					'note' => "Expense: " . $contact_name,
					'attachment' => null,
					'ref_trans_id' => $ref_trans_id,
				];

				// if ($is_billed == "Ya") {
				// 	$trans[] = [
				// 		'account_id' => $account_id, // account_id
				// 		'user_id' => $user_id,
				// 		'account_trans_cat_id' => 4,
				// 		'amount' => 0,
				// 		'date' => $date,
				// 		'type' => 'credit',
				// 		'join_id' => $trans_number,
				// 		'ref' => "Expense",
				// 		'note' => "Expense Payment: " . $contact_name,
				// 		'attachment' => null,
				// 		'ref_trans_id' => $ref_trans_id,
				// 	];

				// 	$trans[$key] = [
				// 		'account_id' => 34, // account_id trade payable
				// 		'user_id' => $user_id,
				// 		'account_trans_cat_id' => 4, // = expense
				// 		'amount' => 0,
				// 		'date' => $date,
				// 		'type' => 'credit',
				// 		'join_id' => $trans_number, // po number
				// 		'ref' => "Expense",
				// 		'note' => "Expense: " . $contact_name,
				// 		'attachment' => null,
				// 		'ref_trans_id' => $ref_trans_id,
				// 	];
				// }
			}

			$data_account_item = $this->Accounts_model->get_account_by_account_code($account_item);
			$account_id_item = $data_account_item->account_id;
			if (!is_null($ref_tax)) {

				$tax_data = $this->Tax_model->find_best_match($ref_tax);
				if ($tax_data->type == 'percentage') {
					$calculate_tax = get_tax_from_amount($amount, $tax_data->rate);
				} else {
					$calculate_tax = get_tax_from_amount($amount, $tax_data->rate, true);
				}

				$tax_rate = $calculate_tax['tax'];
				$amount_item = $calculate_tax['remaining_amount'];

				if ($tax_data->is_withholding) {
					$trans[] = [
						'account_id' => 14, // account_id
						'user_id' => $user_id,
						'account_trans_cat_id' => 4,
						'amount' => $tax_rate,
						'date' => $date,
						'type' => 'debit',
						'join_id' => $trans_number,
						'ref' => "Expense",
						'note' => "Expense Tax: " . $contact_name,
						'attachment' => null,
						'ref_trans_id' => $ref_trans_id,
					];
				} else {
					$trans[] = [
						'account_id' => 45, // account_id
						'user_id' => $user_id,
						'account_trans_cat_id' => 4,
						'amount' => $tax_rate,
						'date' => $date,
						'type' => 'credit',
						'join_id' => $trans_number,
						'ref' => "Expense",
						'note' => "Expense Tax: " . $contact_name,
						'attachment' => null,
						'ref_trans_id' => $ref_trans_id,
					];
				}

				$items[] = [
					'trans_number' => $trans_number,
					'account_id' => $account_id_item,
					'tax_id' => $tax_data->tax_id,
					'tax_rate' => $tax_rate,
					'tax_type' => $tax_data->type,
					'tax_withholding' => $tax_data->is_withholding,
					'amount' => $amount_item,
					'note' => $note ?? null,
				];
			} else {
				$tax_rate = 0;
				$amount_item = $amount;

				$items[] = [
					'trans_number' => $trans_number,
					'account_id' => $account_id_item,
					'tax_id' => 0,
					'tax_rate' => 0,
					'tax_type' => 0,
					'tax_withholding' => 0,
					'amount' => $amount_item,
					'note' => $note ?? null,
				];
			}

			$tagihan[$key]['amount_item'] += $amount_item;
			$tagihan[$key]['amount_tax'] += $tax_rate;
			$tagihan[$key]['amount'] += $amount;

			$trans[$key]['amount'] += $amount;

			// $trans[$key]['amount'] += $amount; // Update total amount in data

			$trans[] = [
				'account_id' => $account_id_item, // account_id
				'user_id' => $user_id,
				'account_trans_cat_id' => 4,
				'amount' => $amount_item,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $trans_number,
				'ref' => "Expense",
				'note' => "Expense Payment: " . $contact_name,
				'attachment' => null,
				'ref_trans_id' => $ref_trans_id,
			];
		}

		// Reset array keys to be numerical
		$trans = array_values($trans);

		return [
			'tagihan' => $tagihan,
			'data' => $data,
			'items' => $items,
			'trans' => $trans
		];
	}


	public function validate_import1()
	{

		$batch = $this->data_dummy();
		$data = [];
		$items = [];


		$sql_contacts = $this->Contact_model->get_all_contact();

		foreach ($contacts as $contact) {

			$match = $this->find_best_match($contact, $sql_contacts);
			$accuracy = $match['accuracy'];
			$match = $match['match'];

			if ($match) {
				echo "Match found for contact: " . $contact['contact_name'] . "\n";
				echo "Details: \n";
				echo "Contact Name: " . $match['contact_name'] . "\n";
				echo "Email: " . $match['email'] . "\n";
				echo "Phone Number: " . $match['phone_number'] . "\n";
				echo "Accuracy: " . number_format($accuracy, 2) . "%\n";
			} else {
				echo "No match found for contact: " . $contact['contact_name'] . "\n";
			}

			// var_dump($value['data']);
			// $data[] = $value['data'];
			$contact = $this->Contact_model->match_data($value['data'][0], $value['data'][2], $value['data'][4]);
			// if (is_null($contact)) {
			// 	var_dump("NULL data");
			// } else {
			// 	var_dump($contact);
			// }
			var_dump($contact);
			// foreach ($value['data'] as $r) {
			// 	var_dump($r);
			// }
		}
	}

	public function ajax_modal_import()
	{
		$data['all_contact_type'] = $this->Contact_model->get_all_contact()->result();
		$html = $this->load->view("admin/finance/expense/components/modal_import", $data, TRUE);

		return $this->output([
			'data' => $html
		]);
	}

	public function ajax_modal_bulk_payment()
	{
		$ids = $this->input->get('select_id');
		$records = $this->Expense_model->bulk_paymnet($ids);

		// dd($records);
		$html = $this->load->view("admin/finance/expense/components/modal_bulk_payment", $records, TRUE);

		return $this->output([
			'data' => $html,

		]);
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
		$filter = $this->input->get('filter');
		$record = $this->Expense_model->all($filter);


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$from = "--";
		$to = "--";

		foreach ($record->result() as $i => $r) {

			if (!in_array(null, [$r->account_id, $r->beneficiary], true)) {

				$from = account_url($r->account_id);
				$to = contact_url($r->beneficiary);
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

			// get_payment
			$payment_data = $this->Expense_model->get_payment(4, $r->trans_number);

			$data[] = array(
				'<input type="checkbox" class="select_id" name="select_id[]" value="' . $r->trans_number . '">',
				$this->Xin_model->set_date_format($r->date),
				expense_url($r->trans_number),
				$r->reference ?? "--",
				$from,
				status_trans($r->status, true),
				$to,
				"<span class='text-danger font-weight-bold'>" . $this->Xin_model->currency_sign($payment_data->sisa_tagihan) . "</span>",
				"<span class='text-success font-weight-bold'>" . $this->Xin_model->currency_sign($payment_data->jumlah_tagihan) . "</span>",
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
		$date = $this->input->post('date');
		$trans_number = $this->input->post('trans_number');

		$data = [
			'account_id' 	=> $this->input->post('account_id'),
			'trans_number' 	=> $trans_number,
			'beneficiary' 	=> $this->input->post('beneficiary'),
			'date' 			=> $date,
			'reference' 	=> post_data('reference'),
			'due_date' 		=> $this->input->post('due_date'),
			'term' 			=> $this->input->post('select_due_date'),
			'status' 		=> $action == 'save' ? 'draft' : 'unpaid',
		];

		$contact = $this->Contact_model->get_contact($this->input->post('beneficiary'));
		if (!is_null($contact)) {
			$contact_name = $contact->contact_name;
		} else {
			$contact_name = "";
		}

		// ref trans id
		$ref_trans_id = $trans_number . "-" . rand(100000, 999999);

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
				} else {
					$tax_no_withholding += $this->input->post('row_tax_rate')[$i] ?? 0;
					$set_withholding = 0;
				}
			}

			$trans[] = [
				'account_id' 			=> $this->input->post('row_target_id')[$i], // account_id
				'user_id' 				=> $user_id,
				'account_trans_cat_id' 	=> 4,
				'amount' 				=> $this->input->post('row_amount')[$i],
				'date' 					=> $date,
				'type' 					=> 'debit',
				'join_id' 				=> $trans_number,
				'ref' 					=> "Expense",
				'note' 					=> "Expense Payment: " . $contact_name,
				'attachment' 			=> null,
				'ref_trans_id' 			=> $ref_trans_id
			];

			$items[] = [
				'trans_number' 		=> $trans_number,
				'account_id' 		=> $this->input->post('row_target_id')[$i],
				'tax_id' 			=> $this->input->post('row_tax_id')[$i],
				'tax_rate' 			=> $this->input->post('row_tax_rate')[$i],
				'tax_type' 			=> $this->input->post('data_tax_type')[$i],
				'tax_withholding' 	=> $set_withholding ?? 0,
				'amount' 			=> $this->input->post('row_amount')[$i],
				'note' 				=> force_string_null($this->input->post('row_note')[$i]),
			];
		}

		// masukan semua total ke akun Trade Payble = credit
		$trans[] =
			[
				'account_id' 			=> 34, // account_id trade payable
				'user_id' 				=> $user_id,
				'account_trans_cat_id' 	=> 4, // = expense
				'amount' 				=> $amount_item + ($tax_no_withholding - $tax_withholding),
				'date' 					=> $date,
				'type' 					=> 'credit',
				'join_id' 				=> $trans_number, // po number
				'ref' 					=> "Expense",
				'note' 					=> "Expense: " . $contact_name,
				'attachment' 			=> null,
				'ref_trans_id' 			=> $ref_trans_id

			];

		if ($tax_withholding > 0) {
			// masukan tax total ke akun VAT Out - withholding
			$trans[] =
				[
					'account_id' 			=> 45,
					'user_id' 				=> $user_id,
					'account_trans_cat_id' 	=> 4,
					'amount' 				=> $tax_withholding,
					'date' 					=> $date,
					'type' 					=> 'credit',
					'join_id' 				=> $trans_number,
					'ref' 					=> "Expense",
					'note' 					=> "Expense Tax Withholding: " . $contact_name,
					'attachment'			=> null,
					'ref_trans_id' 			=> $ref_trans_id
				];
		}

		if ($tax_no_withholding > 0) {
			// masukan tax total ke akun VAT In - no withholding
			$trans[] =
				[
					'account_id' 			=> 14,
					'user_id' 				=> $user_id,
					'account_trans_cat_id' 	=> 4,
					'amount' 				=> $tax_no_withholding,
					'date' 					=> $date,
					'type' 					=> 'debit',
					'join_id' 				=> $trans_number,
					'ref' 					=> "Expense",
					'note' 					=> "Expense Tax: " . $contact_name,
					'attachment' 			=> null,
					'ref_trans_id' 			=> $ref_trans_id
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
			$record->source_account = account_url($record->account_id, $record->account_name, $record->account_code);
			$record->source_account_name = $record->account_name . " | " . $record->account_code;

			$record->beneficiary = contact_url($record->beneficiary);

			// get payment
			$data['payment'] = $this->Expense_model->get_payment(4, $record->trans_number);

			// 4 => roles expense
			$attachments = $this->Files_ms_model->get_by_access_id(4, $record->trans_number)->result();
			$data['attachments'] = $attachments;

			//add expense items model
			$items = $this->Expense_items_model->get_by_trans_number($record->trans_number);

			if (!is_null($items)) {
				foreach ($items as $i => $item) {
					$tax = $this->Tax_model->read_tax_information($item->tax_id); // return bool

					if ($tax) {
						$item->tax_name = $tax[0]->name;
						$item->tax_rate = $item->tax_rate;
					} else {
						$item->tax_name = "--";
						$item->tax_rate = 0;
					}

					$items[$i]->account = account_url($item->account_id);
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

		// check if all tagihan is paid or partially paid
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

	public function bulk_store_payment()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$trans = [];
		$update_log = [];

		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');

		// get user id
		$user_id = $this->session->userdata('username')['user_id'];

		for ($i = 0; $i < count($trans_number = $this->input->post('_token')); $i++) {
			$trans_number = $this->input->post('_token')[$i];
			$source_payment_account = $this->input->post('source_payment_account')[$i];
			$contact = $this->input->post('contact')[$i];

			// tagihan
			$tagihan = $this->input->post('amount_due')[$i];

			// uang yang dibayar
			$amount_paid = $this->input->post('amount_paid')[$i];

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
					'note' => "Pembayaran Expense: " . $contact,
					'attachment' => null,
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
					'note' => "Pembayaran Expense: " . $contact,
					'attachment' => null,
				];

			if ($tagihan == $amount_paid) {
				$update_log[] = ['trans_number' => $trans_number, 'status' => 'paid'];
			} else {
				$update_log[] = ['trans_number' => $trans_number, 'status' => 'partially_paid'];
			}
		}

		$insert = $this->Account_trans_model->bulk_payment_expense($trans, $update_log);
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

	function data_dummy()
	{

		$jayParsedAry = [
			[
				"*Nama Kontak|*Nomor Biaya" => [
					"data" => [
						"account_id" => "Kode Akun Pembayaran",
						"contact_name" => "*Nama Kontak",
						"company_name" => "Nama Perusahaan",
						"email" => "Email",
						"address" => "Alamat",
						"phone" => "Nomor Telepon",
						"exp_number" => "*Nomor Biaya",
						"date" => "*Tanggal Transaksi (dd/mm/yyyy)",
						"due_date" => "Tanggal Jatuh Tempo (dd/mm/yyyy)",
						"note" => "Catatan"
					],
					"item_expense" => [],
					"items" => [
						[
							"trans_number" => "*Nomor Biaya",
							"account_id" => "*Kode Akun Biaya",
							"note" => "Deskripsi Biaya",
							"tax_ref" => "Pajak Biaya",
							"amount" => "Jumlah Biaya"
						]
					]
				],
				"Umay Dipa Kusumo|EXP-00001" => [
					"data" => [
						"account_id" => "1-10003",
						"contact_name" => "Umay Dipa Kusumo",
						"company_name" => "Perum Puspasari Riyanti",
						"email" => "isetiawan@gmail.co.id",
						"address" => null,
						"phone" => "81776685646",
						"exp_number" => "EXP-00001",
						"date" => "08/06/2024",
						"due_date" => "20/06/2024",
						"note" => null
					],
					"item_expense" => [],
					"items" => [
						[
							"trans_number" => "EXP-00001",
							"account_id" => "5-50300",
							"note" => null,
							"tax_ref" => null,
							"amount" => "5000"
						]
					]
				],
				"Maras Suwarno|EXP-00002" => [
					"data" => [
						"account_id" => "1-10003",
						"contact_name" => "Maras Suwarno",
						"company_name" => null,
						"email" => "ajeng.putra@gmail.co.id",
						"address" => null,
						"phone" => null,
						"exp_number" => "EXP-00002",
						"date" => "08/06/2024",
						"due_date" => null,
						"note" => null
					],
					"item_expense" => [],
					"items" => [
						[
							"trans_number" => "EXP-00002",
							"account_id" => "5-50400",
							"note" => null,
							"tax_ref" => "PPH",
							"amount" => "3000"
						]
					]
				],
				"Kayla Wahyuni|EXP-00003" => [
					"data" => [
						"account_id" => "1-10002",
						"contact_name" => "Kayla Wahyuni",
						"company_name" => "PJ Utama Usada",
						"email" => null,
						"address" => "Kpg. Bazuka Raya No. 514, Madiun 77403, Kaltara",
						"phone" => "81094023560",
						"exp_number" => "EXP-00003",
						"date" => "08/06/2024",
						"due_date" => "04/07/2024",
						"note" => null
					],
					"item_expense" => [],
					"items" => [
						[
							"trans_number" => "EXP-00003",
							"account_id" => "5-50300",
							"note" => null,
							"tax_ref" => "PPH",
							"amount" => "3000"
						]
					]
				],
				"Cawisadi Sihombing|EXP-10001" => [
					"data" => [
						"account_id" => "1-10002",
						"contact_name" => "Cawisadi Sihombing",
						"company_name" => "PD Wijayanti Waluyo (Persero) Tbk",
						"email" => null,
						"address" => "Gg. Basoka Raya No. 756, Makassar 13154, Sulsel",
						"phone" => "81006692375",
						"exp_number" => "EXP-10001",
						"date" => "08/06/2024",
						"due_date" => null,
						"note" => null
					],
					"item_expense" => [],
					"items" => [
						[
							"trans_number" => "EXP-10001",
							"account_id" => "5-50000",
							"note" => null,
							"tax_ref" => null,
							"amount" => "1000"
						],
						[
							"trans_number" => "EXP-10001",
							"account_id" => "5-50100",
							"note" => null,
							"tax_ref" => "PPN",
							"amount" => "2500"
						],
						[
							"trans_number" => "EXP-10001",
							"account_id" => "5-50200",
							"note" => null,
							"tax_ref" => "PPN",
							"amount" => "5000"
						]
					]
				]
			]
		];



		return $jayParsedAry;
	}
}


// function splitAndCombine($inputData)
// {
// 	$result = [];

// 	foreach ($inputData as $i => $row) {

// 		if ($i == 0) {
// 			continue;
// 		}

// 		// replace
// 		$row[5] = str_replace('EXP/', 'EXP-', $row[5]);

// 		$key = $row[5]; // Menggabungkan berdasarkan Nama dan Nomor EXP

// 		if (!isset($result[$key])) {
// 			$result[$key] = [
// 				'data' => array_slice($row, 0, 10),
// 				'items' => []
// 			];
// 		}

// 		$result[$key]['items'][] = array_slice($row, 10);
// 	}

// 	return $result;
// }


// function splitAndCombine($inputData)
// {
// 	$result = [];

// 	foreach ($inputData as $row) {
// 		// Mengubah format "EXP/00001" menjadi "EXP-00001"
// 		foreach ($row as &$value) {
// 			if (is_string($value) && strpos($value, 'EXP/') !== false
// 			) {
// 				$value = str_replace('EXP/', 'EXP-', $value);
// 			}
// 		}
// 		unset($value);

// 		$key = $row[0] . '|' . $row[5]; // Menggabungkan berdasarkan Nama dan Nomor EXP

// 		if (!isset($result[$key])) {
// 			$result[$key] = [
// 				'data' => [
// 					'account_id' => $row[15],
// 					'contact_name' => $row[0],
// 					'company_name' => $row[1],
// 					'email' => $row[2],
// 					'address' => $row[3],
// 					'phone' => $row[4],
// 					'exp_number' => $row[5],
// 					'date' => $row[6],
// 					'due_date' => $row[7],
// 					'note' => $row[8],
// 				],
// 				'item_expense' => []
// 			];
// 		}

// 		$result[$key]['items'][] = [
// 			'trans_number' => $row[5],
// 			// 'with_tax' => $row[9],
// 			'account_id' => $row[10],
// 			'note' => $row[11],
// 			'tax_ref' => $row[12],
// 			// 'tax' => $row[13],
// 			'amount' => $row[13],
// 			// 'reimbursable' => $row[15],
// 			// 'cost_center' => $row[16],
// 			// 'department' => $row[17],
// 			// 'tax_amount' => $row[18],
// 			// 'total_amount' => $row[19],
// 			// 'item_date' => $row[20],
// 		];
// 	}

// 	return $result;
// }
