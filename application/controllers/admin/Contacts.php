<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contacts extends MY_Controller
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
		$this->load->model('Contact_model');
		$this->load->model('Employees_model');
		$this->load->model('Liabilities_model');
		$this->load->model('Receivables_model');
		$this->load->model('Files_ms_model');
		$this->load->model('Accounts_model');
		$this->load->model('Account_trans_model');
	}

	public function index()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_title_contacts');
		$data['path_url'] = 'contacts/contact';

		$filter = $this->input->get('filter') ?? 0;
		$data['count_contacts'] = $this->Contact_model->count_contacts($filter);
		$data['types'] = $this->Contact_model->get_all_contact_type()->result();

		if (in_array('531', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/contacts/contact_list", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function ajax_contact_list()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$filter = $this->input->get("filter");
		$records = $this->Contact_model->get_all_contact($filter);

		$data = array();

		foreach ($records->result() as $r) {

			//view
			if (in_array('533', $role_resources_ids)) {
				$view = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_view') . '"><a class="btn icon-btn btn-sm btn-outline-info waves-effect waves-light" href="' . site_url() . 'admin/contacts/view/' . $r->contact_id  . '"><span class="fa fa-eye"></span></a></span>';
			} else {
				$view = '';
			}

			if (in_array('534', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" onclick="modalEdit(' . $r->contact_id  . ')"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}

			if (in_array('535', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->contact_id . '" data-record-name="' . $r->contact_name . '" data-token_type="contacts" data-warning="Semua data tertaut akan terhapus!"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			/// get contact_type
			$contact_type = $this->Contact_model->get_contact_type($r->contact_type_id);
			if (!is_null($contact_type)) {
				$contact_type = $contact_type->contact_type;
			} else {
				$contact_type = '--';
			}

			/// get country
			$country = $this->Xin_model->read_country_info($r->country);
			if (!is_null($country)) {
				$country = $country[0]->country_name;
			} else {
				$country = '--';
			}

			$combhr = $edit . $delete . $view;
			$email_address = $r->email_address ?? "--";
			$data[] = array(
				$combhr,
				"<a href='" . site_url() . 'admin/contacts/view/' . $r->contact_id  . "' class='text-md font-weight-bold'>$r->contact_name</a><br><span>$contact_type</span>",
				"<b class='text-md'>$r->company_name</b><br><span>$country</span>",
				"<b class='text-md'>$r->phone_number</b><br><span>$email_address</span>",
			);
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


	public function ajax_modal_add()
	{
		$data['all_contact_type'] = $this->Contact_model->get_all_contact()->result();
		$html = $this->load->view("admin/contacts/components/modal_add_contact", $data, TRUE);

		return $this->output([
			'data' => $html
		]);
	}

	public function ajax_modal_edit()
	{

		$id = $this->input->get('_token');
		$data['record'] = $this->Contact_model->get_contact($id);
		$html = $this->load->view("admin/contacts/components/modal_edit_contact", $data, TRUE);

		return $this->output([
			'data' => $html
		]);
	}

	public function ajax_all_trans()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$contact_id = $this->input->get('_token');
		$records = $this->Contact_model->get_all_trans($contact_id);

		$data = array();

		foreach ($records as $r) {
			$data[] = array(
				$r->date,
				$r->trans_type,
				$r->note,
				$this->Xin_model->currency_sign($r->amount),

			);
		}

		$output = array(
			"draw" => $data,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function dashboard()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_contact_dashboard');
		$data['path_url'] = 'contact/dashboard';
		if (empty($session)) {
			redirect('admin/');
		}

		if (in_array('531', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/contacts/dashboard", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function view($id)
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		// filter jika data kosong
		$contact = $this->Contact_model->get_contact($id);
		if (is_null($contact)) {
			redirect('admin/contacts');
		}

		$data['breadcrumbs'] = $this->lang->line('ms_title_contacts');
		$data['path_url'] = 'contacts/contact';
		$data['record'] = $contact;

		$data['count_liabilities'] = $this->Liabilities_model->counts($id);
		$data['count_receivables'] = $this->Receivables_model->counts($id);
		$data['liabilities'] = $this->Liabilities_model->get_list_data($id);
		$data['receivables'] = $this->Receivables_model->get_list_data($id);

		if (in_array('533', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/contacts/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/contacts');
		}
	}

	public function edit($id)
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		// filter jika data kosong
		$contact = $this->Contact_model->get_contact($id);
		if (is_null($contact)) {
			redirect('admin/contacts');
		}

		$data['breadcrumbs'] = $this->lang->line('ms_title_contacts');
		$data['path_url'] = 'contacts/contact';
		$data['record'] = $contact;

		if (in_array('534', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/contacts/edit", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/');
		}
	}

	public function store()
	{

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$type = $this->input->post('contact_type');
		$contact_name = $this->input->post('contact_name');
		$company_name = $this->input->post('company_name');
		$billing_address = $this->input->post('billing_address');
		$country = $this->input->post('country');

		if ($type === '') {
			$Return['error'] = $this->lang->line('ms_title_contact_type_error');
		} else if ($contact_name == "") {
			$Return['error'] = $this->lang->line('ms_title_contact_name_error');
		} else if ($company_name == "") {
			$Return['error'] = $this->lang->line('ms_title_company_name_error');
		} else if ($billing_address == "") {
			$Return['error'] = $this->lang->line('ms_title_billing_address_error');
		} else if ($country == "") {
			$Return['error'] = $this->lang->line('ms_title_country_error');
		}

		$data = [
			'contact_type_id' => $type,
			'contact_name' => $contact_name,
			'company_name' => $company_name,
			'billing_address' => $billing_address,
			'country' => $country,
			'province' => $this->input->post('province'),
			'city' => $this->input->post('city'),
			'email_address' => $this->input->post('email_address'),
			'phone_number' => $this->input->post('phone_number'),
			'tax_number' => $this->input->post('tax_number'),
			'date_of_birth' => $this->input->post('date_of_birth'),
		];

		if ($Return['error'] != '') {
			$this->output($Return);
		}

		$result = $this->Contact_model->store($data);

		if ($result) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}
		$this->output($Return);
		exit;
	}

	public function update()
	{

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$id = $this->input->post('_token');

		$type = $this->input->post('contact_type');
		$contact_name = $this->input->post('contact_name');
		$company_name = $this->input->post('company_name');
		$billing_address = $this->input->post('billing_address');
		$country = $this->input->post('country');

		if ($type === '') {
			$Return['error'] = $this->lang->line('ms_title_contact_type_error');
		} else if ($contact_name == "") {
			$Return['error'] = $this->lang->line('ms_title_contact_name_error');
		} else if ($company_name == "") {
			$Return['error'] = $this->lang->line('ms_title_company_name_error');
		} else if ($billing_address == "") {
			$Return['error'] = $this->lang->line('ms_title_billing_address_error');
		} else if ($country == "") {
			$Return['error'] = $this->lang->line('ms_title_country_error');
		}

		$data = [
			'contact_type_id' => $type,
			'contact_name' => $contact_name,
			'company_name' => $company_name,
			'billing_address' => $billing_address,
			'country' => $country,
			'province' => $this->input->post('province'),
			'city' => $this->input->post('city'),
			'email_address' => $this->input->post('email_address'),
			'phone_number' => $this->input->post('phone_number'),
			'tax_number' => $this->input->post('tax_number'),
			'date_of_birth' => $this->input->post('date_of_birth'),
		];

		if ($Return['error'] != '') {
			$this->output($Return);
		}

		$result = $this->Contact_model->update($id, $data);

		if ($result) {
			$Return['result'] = $this->lang->line('ms_title_success_updated');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}
		$this->output($Return);
		exit;
	}

	public function delete()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		if (in_array(535, $role_resources_ids)) {
			$id = $this->input->post('_token');

			$del_attachment = $this->input->post('del_attachment') ?? false; // bool

			$result = $this->Contact_model->delete($id, $del_attachment);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_title_success_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}


	// trans //

	public function create_trans()
	{

		$role_resources_ids = $this->Xin_model->user_role_resource();

		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		// filter id contact
		$id = $this->input->get('back_id');
		$contact = $this->Contact_model->get_contact($id);
		if (is_null($contact)) {
			redirect('admin/contacts');
		}

		$data['title'] = $this->Xin_model->site_title();
		$type = $this->input->get('type');
		if ($type == 'utang') {

			$data['path_url'] = 'liabilities';
			$init = $this->Liabilities_model->init_trans();

			$data['record'] = $init;
			$data['breadcrumbs'] = $this->lang->line('ms_title_liabilities');
			$data['contact'] = $contact;
			if (in_array(542, $role_resources_ids)) {
				$data['subview'] = $this->load->view("admin/contacts/liabilities/create", $data, TRUE);
			} else {
				redirect('admin/');
			}
			#
		} else if ($type == 'piutang') {
			#
			#
			$data['path_url'] = 'receivables';
			$init = $this->Receivables_model->init_trans();

			$data['record'] = $init;
			$data['contact'] = $contact;
			$data['breadcrumbs'] = $this->lang->line('ms_title_receivables');

			if (in_array(547, $role_resources_ids)) {
				$data['subview'] = $this->load->view("admin/contacts/receivables/create", $data, TRUE);
			} else {
				redirect('admin/');
			}
		}

		$this->load->view('admin/layout/layout_main', $data); //page load
	}

	public function liabilities()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_title_liabilities');
		$data['path_url'] = 'liabilities';

		if (in_array('541', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/contacts/liabilities/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_liabilities()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		$record = $this->Liabilities_model->all();

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		foreach ($record->result() as $i => $r) {

			$amount = $this->Liabilities_model->get_total_amount($r->trans_number);

			$contact = $this->Contact_model->get_contact($r->contact_id);
			if (!is_null($contact)) {

				$contact = '<a href="' . base_url('/admin/contacts/view/' . $contact->contact_id) . '" type="button" class="font-weight-bold">' . $contact->contact_name . '</a>';
			} else {
				$contact = "--";
			}

			$trans_number = '<a href="' . base_url('/admin/contacts/liability_view/' . $r->trans_number) . '" class="font-weight-bold">' . $r->trans_number . '</a>';

			if (in_array('544', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/contacts/liability_edit/' . $r->trans_number  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('545', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->trans_number . '" data-token_type="account_transfer"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}
			if (in_array('543', $role_resources_ids)) { // view
				$href = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_view') . '"><a href="' . base_url('admin/contacts/liability_view/' . $r->trans_number) . '" class="btn icon-btn btn-sm btn-outline-info waves-effect waves-light"><span class="fa fa-eye"></span></a></span>';
			} else {
				$href = '';
			}

			$combhr = $edit . $delete . $href;

			$data[] = array(
				$combhr,
				$this->Xin_model->set_date_format($r->date),
				$trans_number,
				$contact,
				$r->reference ?? "--",
				doc_stats($r->status, true),
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

	public function liability_store()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$trans_number = $this->input->post('_token');
		$user_id = $this->session->userdata()['username']['user_id'] ?? 0;


		$ref_trans_id = $trans_number . "-" . rand(100000, 999999);
		#
		#
		$data = [
			'contact_id' => $this->input->post('contact_id'),
			'trans_number' => $this->input->post('trans_number'),
			'date' => $this->input->post('date'),
			'due_date' => $this->input->post('due_date'),
			'reference' => $this->input->post('reference'),
			'status' => 'unpaid',
		];

		$items = [];
		$trans = [];

		for ($i = 0; $i < count($this->input->post('row_amount')); $i++) {

			$trans[] = [
				'account_id' => $this->input->post('row_target_id')[$i], // account_id
				'user_id' => $user_id,
				'account_trans_cat_id' => 7,
				'amount' => $this->input->post('row_amount')[$i],
				'date' => date('Y-m-d'),
				'type' => 'debit',
				'join_id' => $trans_number,
				'ref' => "Dokumen Utang",
				'note' => "Begin Dokumen Utang",
				'attachment' => null,
				'ref_trans_id' => $ref_trans_id
			];

			$items[] = [
				'trans_number' 		=> $trans_number,
				'account_id' 		=> $this->input->post('row_target_id')[$i],
				'amount' 			=> $this->input->post('row_amount')[$i],
				'note' 				=> $this->input->post('row_note')[$i] ?? null,
			];
		}


		// masukan semua total ke akun hutang usaha trade payable
		$trans[] =
			[
				'account_id' => 34, // account_id 
				'user_id' => $user_id,
				'account_trans_cat_id' => 7,
				'amount' => $this->input->post('amount'),
				'date' => date('Y-m-d'),
				'type' => 'credit',
				'join_id' => $trans_number, // po number
				'ref' => "Dokumen Utang",
				'note' => "Begin Dokumen Utang",
				'attachment' => null,
				'ref_trans_id' => $ref_trans_id

			];

		if (!empty($_FILES["attachments"]["name"][0])) {
			// upload file
			$config['allowed_types'] = 'gif|jpg|png|pdf';
			$config['max_size'] = '10240'; // max_size in kb

			$config['upload_path'] = './uploads/contact/liabilities/';

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
						'file_access' => 7, // expense
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

		$query = $this->Liabilities_model->update_with_items_and_files($trans_number, $data, $trans, $items, $file_attachments);

		if ($query) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
			$Return['path'] = "";
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_error');
			$this->output($Return);
		}
	}

	public function liability_store_item()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$trans_number = $this->input->post('_token');
		$user_id = $this->session->userdata()['username']['user_id'] ?? 0;


		// get old tagihan
		$tagihan = $this->Liabilities_model->get_tagihan($trans_number);

		// tampung ref_trans_id
		$ref_trans_id = $tagihan->ref_trans_id;

		$items = [];
		$trans = [];

		for ($i = 0; $i < count($this->input->post('row_amount')); $i++) {

			$trans[] = [
				'account_id' => $this->input->post('row_target_id')[$i], // account_id
				'user_id' => $user_id,
				'account_trans_cat_id' => 7,
				'amount' => $this->input->post('row_amount')[$i],
				'date' => date('Y-m-d'),
				'type' => 'debit',
				'join_id' => $trans_number,
				'ref' => "Dokumen Utang",
				'note' => "Begin Dokumen Utang",
				'attachment' => null,
				'ref_trans_id' => $ref_trans_id
			];

			$items[] = [
				'trans_number' 		=> $trans_number,
				'account_id' 		=> $this->input->post('row_target_id')[$i],
				'amount' 			=> $this->input->post('row_amount')[$i],
				'note' 				=> $this->input->post('row_note')[$i] ?? null,
			];
		}

		$query = $this->Liabilities_model->insert_items($trans_number, $items, $trans);

		if ($query) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
			$Return['path'] = "";
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_error');
			$this->output($Return);
		}
	}

	public function liability_view($id)
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$data['path_url'] = 'liabilities';
		$data['breadcrumbs'] = $this->lang->line('ms_title_liabilities');
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Liabilities_model->get_by_number_doc($id);

		if (!is_null($record)) {

			// get payment
			$data['payment'] = $this->Liabilities_model->get_payment(7, $record->trans_number);

			// 4 => roles expense
			$attachments = $this->Files_ms_model->get_by_access_id(7, $record->trans_number)->result();
			$data['attachments'] = $attachments;

			//add expense items model
			$items = $this->Liabilities_model->get_items_by_trans_number($record->trans_number);

			$data['items'] = $items;
		} else {
			redirect('admin/contacts');
		}

		// dd($data['payment']);
		$data['record'] = $record;
		if (in_array('543', $role_resources_ids)) { // view
			$data['subview'] = $this->load->view("admin/contacts/liabilities/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function liability_edit($id)
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$data['path_url'] = 'liabilities';
		$data['breadcrumbs'] = $this->lang->line('ms_title_liabilities');
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$record = $this->Liabilities_model->get_by_number_doc($id);

		if (!is_null($record)) {

			$data['contact'] = $this->Contact_model->get_contact($record->contact_id);

			// // get payment
			// $data['payment'] = $this->Liabilities_model->get_payment(7, $record->trans_number);

			// // 4 => roles expense
			// $attachments = $this->Files_ms_model->get_by_access_id(7, $record->trans_number)->result();
			// $data['attachments'] = $attachments;

			//add expense items model
			$items = $this->Liabilities_model->get_items_by_trans_number($record->trans_number);

			$data['items'] = $items;
		} else {
			redirect('admin/contacts');
		}

		$data['record'] = $record;
		if (in_array('544', $role_resources_ids)) { // view
			$data['subview'] = $this->load->view("admin/contacts/liabilities/edit", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_items_liability()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		$id = $this->input->get('_token');

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$record = $this->Liabilities_model->get_items_by_trans_number($id);

		$output = array(
			"draw" => $draw,
			"recordsTotal" => count($record),
			"recordsFiltered" => count($record),
			"items" => $record
		);
		echo json_encode($output);
		exit();
	}

	public function liabilities_delete()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		if (in_array(545, $role_resources_ids)) {
			$id = $this->input->post('_token');

			$del_attachment = $this->input->post('del_attachment') ?? false; // bool

			$result = $this->Liabilities_model->delete($id, $del_attachment);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_title_success_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function liability_update()
	{

		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		die();
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$trans_number = $this->input->post('_token');

		// data liability
		$data = [
			'date' => $this->input->post('date'),
			'due_date' => $this->input->post('due_date'),
			'reference' => $this->input->post('reference')
		];

		// masukan semua total ke akun hutang usaha trade payable
		$trans[] =
			[
				'account_id' => 34, // account_id
				'account_trans_cat_id' => 7,
				'amount' => $this->input->post('amount'),
				'attachment' => null,
				'ref_trans_id' => $ref_trans_id

			];

		for ($i = 0; $i < count($this->input->post('row_amount')); $i++) {

			$trans[] = [
				'account_id' => $this->input->post('row_target_id')[$i],
				'amount' => $this->input->post('row_amount')[$i],
				'note' => $this->input->post('row_note')[$i],
			];
		}



		$desc = "Transfer Doc";
		$amount = $this->input->post('amount');

		if ($this->input->post('target_account') == $this->input->post('old_target_account')) {
			$data = [
				'account_id' => $this->input->post('account_id'),
				'target_account_id' => $this->input->post('old_target_account'),
				'trans_number' => $trans_number,
				'date' => $this->input->post('date'),
				'amount' => $amount,
				'ref' => $this->input->post('ref'),
				'note' => $this->input->post('note'),
				'description' => $desc,
			];

			$new_target_account = false;
		} else {
			$new_target_account = $this->input->post('target_account');
			$data = [
				'account_id' => $this->input->post('account_id'),
				'target_account_id' => $this->input->post('target_account'),
				'trans_number' => $trans_number,
				'date' => $this->input->post('date'),
				'amount' => $amount,
				'ref' => $this->input->post('ref'),
				'note' => $this->input->post('note'),
				'description' => $desc,
			];
		}

		$query = $this->Account_transfer_model->update_by_trans_number($trans_number, $data, $new_target_account);

		if ($query) {
			$Return['result'] = $this->lang->line('ms_title_success_updated');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_error');
			$this->output($Return);
		}
	}

	public function ajax_modal_liability_item()
	{

		$id = $this->input->get('_token');
		$html = $this->load->view("admin/contacts/liabilities/modal_add_item", ['_token' => $id], TRUE);

		return $this->output([
			'data' => $html
		]);
	}

	/* terakhir sampai edit, tapi blm save data */


	public function receivable_store()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$trans_number = $this->input->post('_token');
		$user_id = $this->session->userdata()['username']['user_id'] ?? 0;


		$ref_trans_id = $trans_number . "-" . rand(100000, 999999);
		#
		#
		$data = [
			'contact_id' => $this->input->post('contact_id'),
			'trans_number' => $this->input->post('trans_number'),
			'date' => $this->input->post('date'),
			'due_date' => $this->input->post('due_date'),
			'reference' => $this->input->post('reference'),
			'status' => 'unpaid',
		];

		$items = [];
		$trans = [];

		for ($i = 0; $i < count($this->input->post('row_amount')); $i++) {

			$trans[] = [
				'account_id' => $this->input->post('row_target_id')[$i], // account_id
				'user_id' => $user_id,
				'account_trans_cat_id' => 8,
				'amount' => $this->input->post('row_amount')[$i],
				'date' => date('Y-m-d'),
				'type' => 'credit',
				'join_id' => $trans_number,
				'ref' => "Dokumen Piutang",
				'note' => "Begin Dokumen Piutang",
				'attachment' => null,
				'ref_trans_id' => $ref_trans_id
			];

			$items[] = [
				'trans_number' 		=> $trans_number,
				'account_id' 		=> $this->input->post('row_target_id')[$i],
				'amount' 			=> $this->input->post('row_amount')[$i],
				'note' 				=> $this->input->post('row_note')[$i] ?? null,
			];
		}


		// masukan semua total ke akun hutang usaha 1-10100 Account Receivable
		$trans[] =
			[
				'account_id' => 4, // account_id 
				'user_id' => $user_id,
				'account_trans_cat_id' => 8, // = 
				'amount' => $this->input->post('amount'),
				'date' => date('Y-m-d'),
				'type' => 'debit',
				'join_id' => $trans_number, // po number
				'ref' => "Dokumen Piutang",
				'note' => "Begin Dokumen Piutang",
				'attachment' => null,
				'ref_trans_id' => $ref_trans_id

			];

		if (!empty($_FILES["attachments"]["name"][0])) {
			// upload file
			$config['allowed_types'] = 'gif|jpg|png|pdf';
			$config['max_size'] = '10240'; // max_size in kb

			$config['upload_path'] = './uploads/contact/receivables/';

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
						'file_access' => 8, // expense
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

		$query = $this->Receivables_model->update_with_items_and_files($trans_number, $data, $trans, $items, $file_attachments);

		if ($query) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
			$Return['path'] = "";
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_error');
			$this->output($Return);
		}
	}

	public function receivable_view($id)
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_title_receivables');
		$data['path_url'] = 'receivables';
		if (empty($session)) {
			redirect('admin/');
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$record = $this->Receivables_model->get_by_number_doc($id);

		// dd($record);
		if (!is_null($record)) {

			// get payment
			$data['payment'] = $this->Receivables_model->get_payment(8, $record->trans_number);

			// 4 => roles expense
			$attachments = $this->Files_ms_model->get_by_access_id(8, $record->trans_number)->result();
			$data['attachments'] = $attachments;

			//add expense items model
			$items = $this->Receivables_model->get_items_by_trans_number($record->trans_number);

			if (!is_null($items)) {
				foreach ($items as $item) {
					$item->account_name = $this->Accounts_model->get($item->account_id)->row()->account_name;
				}
			}

			$data['items'] = $items;
		} else {
			redirect('admin/contacts');
		}


		$data['record'] = $record;
		if (in_array('548', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/contacts/receivables/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function receivables()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_title_receivables');
		$data['path_url'] = 'receivables';

		if (in_array('546', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/contacts/receivables/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_receivables()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		$record = $this->Receivables_model->all();

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		foreach ($record->result() as $i => $r) {

			$amount = $this->Receivables_model->get_total_amount($r->trans_number);

			$contact = $this->Contact_model->get_contact($r->contact_id);
			if (!is_null($contact)) {

				$contact = '<a href="' . base_url('/admin/contacts/view/' . $contact->contact_id) . '" type="button" class="font-weight-bold">' . $contact->contact_name . '</a>';
			} else {
				$contact = "--";
			}

			$trans_number = '<a href="' . base_url('/admin/contacts/receivable_view/' . $r->trans_number) . '" type="button" class="font-weight-bold">' . $r->trans_number . '</a>';

			if (in_array('549', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/contacts/receivable_view/' . $r->trans_number  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('550', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->trans_number . '" data-token_type="account_transfer"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}
			if (in_array('548', $role_resources_ids)) { // view
				$href = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_view') . '"><a href="' . base_url('admin/contacts/receivable_view/' . $r->trans_number) . '" class="btn icon-btn btn-sm btn-outline-info waves-effect waves-light"><span class="fa fa-eye"></span></a></span>';
			} else {
				$href = '';
			}

			$combhr = $edit . $delete . $href;

			$data[] = array(
				$combhr,
				$this->Xin_model->set_date_format($r->date),
				$trans_number,
				$contact,
				$r->reference,
				doc_stats($r->status, true),
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

	public function receivables_delete()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		if (in_array(550, $role_resources_ids)) {
			$id = $this->input->post('_token');

			$del_attachment = $this->input->post('del_attachment') ?? false; // bool

			$result = $this->Receivables_model->delete($id, $del_attachment);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_title_success_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}


	// Type of contacts //
	public function types()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/contacts');
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();

		$edit = $this->input->get('edit');
		$get_type = $this->Contact_model->get_contact_type($edit);
		if (!is_null($get_type)) {
			if (in_array('539', $role_resources_ids)) {
				$data['form'] = $this->load->view("admin/contacts/types/edit", ['record' => $get_type], TRUE);
			} else {
				$data['form'] = false;
			}
		} else {
			if (in_array('538', $role_resources_ids)) {
				$data['form'] = $this->load->view("admin/contacts/types/create", [], TRUE);
			} else {
				$data['form'] = false;
			}
		}

		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_title_contact_type');
		$data['path_url'] = 'contacts/contact_type';
		$data['records'] = $this->Contact_model->get_all_contact_type();

		if (in_array('537', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/contacts/types/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function store_type()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$data = [
			'contact_type' => $this->input->post('contact_type'),
			'is_editable' => 1
		];

		$query = $this->Contact_model->store_type($data);

		if ($query) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_error');
			$this->output($Return);
		}
	}

	public function delete_type()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		if (in_array('540', $role_resources_ids)) {

			$id = $this->input->post('_token');

			$result = $this->Contact_model->delete_type($id);
			if ($result) {
				$Return['result'] = $this->lang->line('ms_title_success_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function update_type()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		$id = $this->input->post('_token');
		$data = [
			'contact_type' => $this->input->post('contact_type'),
		];

		$query = $this->Contact_model->update_type($id, $data);

		if ($query) {
			$Return['result'] = $this->lang->line('ms_title_success_added');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_error');
			$this->output($Return);
		}
	}


	// Store Payment //
	public function liability_store_payment()
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

		$file_attachment = $this->upload_attachment('./uploads/liabilities/', 'TRANS');

		$ref_trans_id = $trans_number . "-" . rand(100000, 999999);

		// uang yang dibayar
		$amount_paid = $this->input->post('amount_paid');
		$trans = [];

		// kredit pengirim
		$trans[] =
			[
				'account_id' => $source_payment_account,
				'user_id' => $user_id,
				'account_trans_cat_id' => 7,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'credit',
				'join_id' => $trans_number,
				'ref' => $payment_ref,
				'note' => "Pembayaran Utang",
				'attachment' => $file_attachment,
				'ref_trans_id' => $ref_trans_id
			];

		// debit trade payable
		$trans[] =
			[
				'account_id' => 34,
				'user_id' => $user_id,
				'account_trans_cat_id' => 7,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $trans_number,
				'ref' => $payment_ref,
				'note' => "Pembayaran Utang",
				'attachment' => $file_attachment,
				'ref_trans_id' => $ref_trans_id
			];

		$insert = $this->Account_trans_model->insert_payment($trans);

		$check_tagihan = $this->Liabilities_model->get_payment(7, $trans_number);

		if ($check_tagihan->sisa_tagihan == 0) {
			// update status spend
			$this->Liabilities_model->update_by_trans_number($trans_number, ['status' => 'paid']);
		} else {
			// update status spend
			$this->Liabilities_model->update_by_trans_number($trans_number, ['status' => 'partially_paid']);
		}

		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}

	public function receivable_store_payment()
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

		$file_attachment = $this->upload_attachment('./uploads/receivables/', 'TRANS');

		$ref_trans_id = $trans_number . "-" . rand(100000, 999999);

		// uang yang dibayar
		$amount_paid = $this->input->post('amount_paid');
		$trans = [];

		// kredit pengirim
		$trans[] =
			[
				'account_id' => $source_payment_account,
				'user_id' => $user_id,
				'account_trans_cat_id' => 8,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $trans_number,
				'ref' => $payment_ref,
				'note' => "Pembayaran Piutang",
				'attachment' => $file_attachment,
				'ref_trans_id' => $ref_trans_id
			];

		// debit Account Receivable
		$trans[] =
			[
				'account_id' => 4,
				'user_id' => $user_id,
				'account_trans_cat_id' => 8,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'credit',
				'join_id' => $trans_number,
				'ref' => $payment_ref,
				'note' => "Pembayaran Piutang",
				'attachment' => $file_attachment,
				'ref_trans_id' => $ref_trans_id
			];

		$insert = $this->Account_trans_model->insert_payment($trans);

		$check_tagihan = $this->Receivables_model->get_payment(8, $trans_number);

		if ($check_tagihan->sisa_tagihan == 0) {
			// update status spend
			$this->Receivables_model->update_by_trans_number($trans_number, ['status' => 'paid']);
		} else {
			// update status spend
			$this->Receivables_model->update_by_trans_number($trans_number, ['status' => 'partially_paid']);
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
