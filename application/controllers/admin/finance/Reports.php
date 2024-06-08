<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends MY_Controller
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
		$this->load->model('Contact_model');
	}

	public function index()
	{
		$type = $this->input->get('type');

		$role_resources_ids = $this->Xin_model->user_role_resource();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$data['title'] = $this->Xin_model->site_title();
		$title = "<strong>" . $this->lang->line('ms_title_trans_overview') . "</strong>";
		$data['breadcrumbs'] = $title;
		$data['path_url'] = 'finance/report';

		if (true) {
			// if (in_array('503', $role_resources_ids)) {

			$data['subview'] = $this->load->view("admin/finance/reports/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('/admin');
		}
	}

	public function get_ajax_report()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

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
				$from = "<a href='" . site_url() . 'admin/finance/accounts/transactions?id=' . $from_account->account_id  . "' class='text-md font-weight-bold'><b>" . $from_account->account_name . "</b>  " . $from_account->account_code . "</a>";
			} else {
				$from = "--";
			}

			$to_account = $this->Accounts_model->get($r->target_account_id)->row();
			if (!is_null($to_account)) {
				$to = "<a href='" . site_url() . 'admin/finance/accounts/transactions?id=' . $to_account->account_id  . "' class='text-md font-weight-bold'><b>" . $to_account->account_name . "</b>  " . $to_account->account_code . "</a>";
			} else {
				$to = "--";
			}

			if (in_array('99999', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/finance/accounts/transfer_edit?id=' . $r->trans_number  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('99999', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->trans_number . '" data-token_type="account_transfer"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$href = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_view') . '"><a href="' . base_url('admin/finance/accounts/transfer_view?id=' . $r->trans_number) . '" class="btn icon-btn btn-sm btn-outline-info waves-effect waves-light"><span class="fa fa-eye"></span></a></span>';
			$combhr = $edit . $delete . $href;

			$data[] = array(
				$combhr,
				$this->Xin_model->set_date_format($r->created_at),
				"<a href='" . base_url('admin/finance/accounts/transfer_view?id=' . $r->trans_number) . "' class=''>" . $r->trans_number . "</a>",
				"<small>" . $from . "<br>To<br>" . $to . "</small>",
				$r->ref,
				doc_stats($r->status, true),
				$this->Xin_model->currency_sign($r->amount),
			);
		}

		foreach ($record_2->result() as $i => $r) {

			if (!in_array(null, [$r->account_id, $r->beneficiary], true)) {

				$from_account = $this->Accounts_model->get($r->account_id)->row();
				if (!is_null($from_account)) {
					$from = "<a href='" . site_url() . 'admin/finance/accounts/transactions?id=' . $from_account->account_id  . "' class='text-md font-weight-bold'><b>" . $from_account->account_name . "</b>  " . $from_account->account_code . "</a>";
				} else {
					$from = "--";
				}

				$contact = $this->Contact_model->get_contact($r->beneficiary);
				if (!is_null($contact)) {
					$to = "<a href='" . site_url() . 'admin/contacts/view/' . $contact->contact_id  . "' class='text-md font-weight-bold'>" . $contact->contact_name . "</a>";
				} else {
					$to = "--";
				}
			} else {
				$from = "--";
				$to = "--";
			}

			$amount = $this->Account_spend_items_model->get_total_amount($r->trans_number);
			if (!is_null($amount)) {
				$amount = $amount;
			} else {
				$amount = 0;
			}


			if (in_array('99999', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/purchase_requisitions/edit/' . $r->spend_id  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}

			// jgn pake petik
			if (in_array('99999', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->trans_number . '" data-token_type="account_spend"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$href = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_view') . '"><a href="' . base_url('admin/finance/accounts/spend_view?id=' . $r->trans_number) . '" class="btn icon-btn btn-sm btn-outline-info waves-effect waves-light"><span class="fa fa-eye"></span></a></span>';

			$combhr = $edit . $delete . $href;

			$data[] = array(
				$combhr,
				$this->Xin_model->set_date_format($r->created_at),
				"<a href='" . base_url('admin/finance/accounts/spend_view?id=' . $r->trans_number) . "' class=''>" . $r->trans_number . "</a>",
				"<small>" . $from . "<br>To<br>" . $to . "</small>",
				$r->reference,
				doc_stats($r->status, true),
				$this->Xin_model->currency_sign($amount),
			);
		}

		foreach ($record_3->result() as $i => $r) {
			$receives = $this->Account_receive_items_model->get_account_from_receive($r->trans_number);
			$result_receives = "";
			foreach ($receives as $key => $rev) {
				$titik_kome = $key == array_key_last($receives) ? ". " : ",<br>";
				$result_receives .= "<a href='" . site_url() . 'admin/finance/accounts/transactions?id=' . $rev->account_id  . "' class='text-md font-weight-bold'><b>" . $rev->account_name . "</b>  " . $rev->account_code . "</a>" . $titik_kome;
			}

			$contact = $this->Contact_model->get_contact($r->contact_id);
			if (!is_null($contact)) {
				$from = "<a href='" . site_url() . 'admin/contacts/view/' . $contact->contact_id  . "' class='text-md font-weight-bold'>" . $contact->contact_name . "</a>";
			} else {
				$from = "--";
			}

			$amount = $this->Account_receive_items_model->get_total_amount($r->trans_number);
			if (!is_null($amount)) {
				$amount = $amount;
			} else {
				$amount = 0;
			}

			if (in_array('99999', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" href="' . site_url() . 'admin/purchase_requisitions/edit/' . $r->receive_id  . '"><span class="fas fa-pencil-alt"></span></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('99999', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->trans_number . '" data-token_type="account_receive"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$href = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_view') . '"><a href="' . base_url('admin/finance/accounts/receive_view?id=' . $r->trans_number) . '" class="btn icon-btn btn-sm btn-outline-info waves-effect waves-light"><span class="fa fa-eye"></span></a></span>';
			$combhr = $edit . $delete . $href;

			$data[] = array(
				$combhr,
				$this->Xin_model->set_date_format($r->date),
				"<a href='" . base_url('admin/finance/accounts/receive_view?id=' . $r->trans_number) . "' class=''>" . $r->trans_number . "</a>",
				"<small>" . $from . "<br>To<br>" . $result_receives . "</small>",
				$r->reference,
				doc_stats($r->status, true),
				$this->Xin_model->currency_sign($amount),
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function balance_sheet()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$data['title'] = $this->Xin_model->site_title();
		$title = "<strong>" . $this->lang->line('ms_title_trans_overview') . "</strong>";
		$data['breadcrumbs'] = $title;
		$data['path_url'] = 'finance/report';

		if (true) {
			// if (in_array('503', $role_resources_ids)) {

			$data['subview'] = $this->load->view("admin/finance/reports/balance_sheet", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('/admin');
		}
	}
}
