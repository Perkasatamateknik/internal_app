<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Incomes extends MY_Controller
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
		$this->load->model('Vendor_model');
		$this->load->model('Employees_model');
		$this->load->model('Accounts_model');
		$this->load->model('Account_receive_model');
		$this->load->model('Account_spend_model');
		$this->load->model('Account_spend_items_model');
		$this->load->model('Account_transfer_model');
		$this->load->model('Purchase_model');
		$this->load->model('Invoices_model');
		$this->load->model('Project_model');
	}

	public function index()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->Xin_model->site_title();


		$title = "<strong>" . $this->lang->line('ms_title_income') . "</strong>";
		$data['breadcrumbs'] = $title;
		$data['path_url'] = 'finance/income';

		// if (in_array('503', $role_resources_ids)) {
		if (true) {
			$data['subview'] = $this->load->view("admin/finance/incomes/index", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_invoices()
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$record = $this->Invoices_model->all();


		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));

		$data = array();
		$from = "--";
		$to = "--";

		foreach ($record->result() as $i => $r) {


			// $project = $this->Project_model->read_project_information($r->project_id);
			// if (!is_null($project)) {
			// 	$project_name = "<b>$project->title</b><br>" . $this->Xin_model->set_date_format($r->publish_date);
			// } else {
			// 	$project_name = "--";
			// }
			$from_account = $this->Accounts_model->get($r->account_id)->row();
			if (!is_null($from_account)) {
				$from = "<b>$from_account->account_name</b>" . "  " . $from_account->account_code;
			} else {
				$from = "--";
			}

			$to_beneficiary = $this->Employees_model->read_employee_information($r->client_id);
			if (!is_null($to_beneficiary)) {
				$to = $to_beneficiary[0]->first_name . "  " . $to_beneficiary[0]->last_name;
			} else {
				$to = "--";
			}
			// } else {
			// 	$from = "--";
			// 	$to = "--";

			$data[] = array(
				"<b>$to</b><br><small>Jl. example, No.12 Kegabutan</small>",
				"<a href='" . base_url('admin/finance/invoice/view?id=' . $r->trans_number) . "' class='text-secondary'>" . $r->trans_number . "</a><br><small>" . getTimeDisplay($r->due_date, "", $this->lang->line('ms_title_ago')) . "</small>",
				doc_stats($r->status, true),
				$this->Xin_model->currency_sign(100000),
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
			// "recordsTotal" => $record->num_rows(),
			// "recordsFiltered" => $record->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
}
