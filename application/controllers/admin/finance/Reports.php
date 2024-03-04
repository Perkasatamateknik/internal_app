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
		$this->load->model('Vendor_model');
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

		$data['subview'] = $this->load->view("admin/finance/reports/index", $data, TRUE);
		$this->load->view('admin/layout/layout_main', $data); //page load

	}

	public function get_ajax_report()
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
				doc_stats($r->status, true),
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
				doc_stats($r->status, true),
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
}
