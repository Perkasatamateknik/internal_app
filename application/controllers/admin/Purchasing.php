<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchasing extends MY_Controller
{

	public $db;
	public function __construct()
	{
		parent::__construct();
		//load the model
		$this->load->model("Tax_model");
		$this->load->model("Product_model");

		$this->load->model("Vendor_model");
		$this->load->model("Product_model");
		$this->load->model("Project_model");
		$this->load->model("Department_model");
		$this->load->model("Purchase_items_model");
		$this->load->model("Purchase_model");
		$this->load->model("Account_trans_model");
		$this->load->model("Account_transactions_model");
	}

	/*Function to set JSON output*/
	public function output($Return = array())
	{
		/*Set respinse header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function index()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_dashboard');
		$data['path_url'] = 'purchasing';
		if (empty($session)) {
			redirect('admin/');
		}

		$data['data'] = $this->get_purchase_data();
		if (in_array('503', $role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/purchasing/dashboard", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function upload_file($type)
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		if ($_FILES['attachment']['name']) {
			if ($type == 'requisition') {
				$upload_path = './uploads/purchase/requisitions/';
			} else if ($type == 'order') {
				$upload_path = './uploads/purchase/orders/';
			} else if ($type == 'invoice') {
				$upload_path = './uploads/purchase/invoices/';
			} else if ($type == 'delivery') {
				$upload_path = './uploads/purchase/deliveries/';
			} else {
				$upload_path = './uploads/purchase/dump/';
			}

			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'gif|jpg|png|pdf'; // Tipe file yang diperbolehkan
			$config['max_size'] = 5120; // Ukuran maksimal file (dalam KB)
			$config['encrypt_name'] = TRUE;

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('attachment')) {
				return $this->upload->data('file_name');
			} else {
				$Return['error'] = "Errror uploading!";
				$this->output($Return);
				exit();
			}
		} else {
			return null;
		}
	}

	public function get_all_trans()
	{
	}

	public function get_last_month_trans_vendor()
	{

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('chart_data' => '', 'c_name' => '', 'd_rows' => '', 'c_color' => '');
		$c_name = array();
		$d_rows = array();
		$c_color = array('#975df3', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b');
		$someArray = array();
		$j = 0;

		foreach ($this->Purchase_items_model->get_trans_vendor()->result() as $r) {

			$condition = "vendor_id =" . "'" . $r->vendor_id . "'";
			$this->db->select('*');
			$this->db->from('ms_vendors');
			$this->db->where($condition);
			$query = $this->db->get();
			$check  = $query->row();

			// check if department available
			if ($query->num_rows() > 0) {
				$row = $query->num_rows();
				$d_rows[] = $row;
				$c_name[] = htmlspecialchars_decode($check->vendor_name);

				$someArray[] = array(
					'label'   => htmlspecialchars_decode($check->vendor_name),
					'value' => $r->amount,
					'bgcolor' => $c_color[$j]
				);
				$j++;
			}
		}
		$Return['c_name'] = $c_name;
		$Return['d_rows'] = $d_rows;
		$Return['chart_data'] = $someArray;
		$this->output($Return);
		exit;
	}

	public function delete_file($file_name, $type)
	{
		if (!is_null($file_name)) {
			// Define the directory where the files are stored
			if ($type == 'requisition') {
				$upload_path = './uploads/purchase/requisitions/';
			} else if ($type == 'order') {
				$upload_path = './uploads/purchase/orders/';
			} else if ($type == 'invoice') {
				$upload_path = './uploads/purchase/invoices/';
			} else if ($type == 'delivery') {
				$upload_path = './uploads/purchase/deliveries/';
			} else {
				$upload_path = './uploads/purchase/dump/';
			}

			// Check if the file exists in the directory
			if (file_exists($upload_path . $file_name)) {
				// Delete the file
				unlink($upload_path . $file_name);
			}

			return true;
		}
	}

	public function get_purchase_data()
	{
		$query_pr = $this->db->query("SELECT purchase_status, COUNT(pr_number) AS total FROM ms_purchase_requisitions GROUP BY purchase_status ORDER BY purchase_status ASC");
		$pr_data = [
			'status_1' => 0,
			'status_2' => 0,
			'status_3' => 0,
		];
		foreach ($query_pr->result() as $row) {
			$status = "status_" . $row->purchase_status;
			$pr_data[$status] = $row->total;
		}


		$query_po = $this->db->query("SELECT status, COUNT(po_number) AS total FROM ms_purchase_orders GROUP BY status ORDER BY status ASC");
		$po_data = [
			'status_0' => 0,
			'status_1' => 0,
			'status_2' => 0
		];
		foreach ($query_po->result() as $row) {
			$status = "status_" . $row->status;
			$po_data[$status] = $row->total;
		}

		$query_pi = $this->db->query("SELECT status, COUNT(pi_number) AS total FROM ms_purchase_invoices GROUP BY status ORDER BY status ASC");
		$pi_data = [
			'status_0' => 0,
			'status_1' => 0,
			'status_2' => 0
		];

		foreach ($query_pi->result() as $row) {
			$status = "status_" . $row->status;
			$pi_data[$status] = $row->total;
		}

		$query_pd = $this->db->query("SELECT status, COUNT(pd_number) AS total FROM ms_purchase_deliveries GROUP BY status ORDER BY status ASC");
		$pd_data = [
			'status_0' => 0,
			'status_1' => 0,
			'status_2' => 0
		];

		foreach ($query_pd->result() as $row) {
			$status = "status_" . $row->status;
			$pd_data[$status] = $row->total;
		}

		$result = [
			'pr_data' => $pr_data,
			'po_data' => $po_data,
			'pi_data' => $pi_data,
			'pd_data' => $pd_data,
			'count' => [
				'pr' => $this->db->query('SELECT COUNT(pr_number) AS total FROM ms_purchase_requisitions WHERE purchase_status = 1')->row()->total,
				'po' => $po_data['status_0'] + $po_data['status_1'],
				'pd' => $pd_data['status_1'],
				'pi' => $pi_data['status_0'] + $pi_data['status_2'],
			]
		];

		return $result;

		// $this->output($result);
		// exit;
	}

	public function get_purchase_by_vendors()
	{

		$month = $this->input->get('month') ?? false;
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('chart_data' => '', 'c_name' => '', 'd_rows' => '', 'c_color' => '');
		$c_name = array();
		$d_rows = array();

		$c_color = ['#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b',  '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#46be8a', '#f96868', '#00c0ef', '#66456e', '#c674ad', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#975df3', '#61a3ca', '#975df3', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#605ca8', '#d81b60', '#001f3f',  '#a98852', '#006400', '#dd4b39', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b'];
		$someArray = array();

		$this->db->select('pi.vendor_id');
		$this->db->select('IFNULL(v.vendor_name, "--") AS vendor_name', FALSE);
		$this->db->select('SUM(i.amount) AS total_purchase_amount');
		$this->db->from('ms_purchase_invoices AS pi');
		$this->db->join('ms_items_purchase_invoice AS i', 'pi.pi_number = i.pi_number', 'left');
		$this->db->join('ms_vendors AS v', 'pi.vendor_id = v.vendor_id', 'left');

		if ($month) {
			$this->db->where('MONTH(pi.date)', $month);
			$this->db->where('YEAR(pi.date)', $month);
		}

		$this->db->group_by('pi.vendor_id, vendor_name');
		$this->db->order_by('total_purchase_amount', 'DESC');
		$this->db->limit(8);

		$result = $this->db->get();
		foreach ($result->result() as $i => $r) {
			$someArray[] = array(
				'label'   => htmlspecialchars_decode($r->vendor_name),
				'value' => $r->total_purchase_amount,
				'format_value' => $this->Xin_model->currency_sign($r->total_purchase_amount),
				'bgcolor' => $c_color[$i]
			);
		}
		$Return['c_name'] = $c_name;
		$Return['d_rows'] = $d_rows;
		$Return['chart_data'] = $someArray;
		$this->output($Return);
		exit;
	}


	public function get_purchase_by_selected()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('chart_data' => '', 'c_name' => '', 'd_rows' => '', 'c_color' => '');
		$c_name = array();
		$d_rows = array();
		$c_color = ['#46be8a', '#f96868', '#00c0ef', '#66456e', '#c674ad', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#975df3', '#61a3ca', '#975df3', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#605ca8', '#d81b60', '#001f3f',  '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b',  '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b'];
		$someArray = array();

		$type = $this->input->get('selected') ?? "";
		$month = $this->input->get('month') ?? false;

		if ($type == "sub-category") {
			$this->db->select('ipi.sub_category_name AS name, SUM(ipi.amount) AS total_pembelian', 'ipi.date');
			$this->db->from('ms_purchase_invoices pi');
			$this->db->join('ms_items_purchase_invoice ipi', 'pi.pi_number = ipi.pi_number');
			$this->db->group_by('ipi.sub_category_name');
			$this->db->order_by('total_pembelian', 'DESC');

			if ($month) {
				$this->db->where('MONTH(pi.date)', $month);
			}
			$this->db->limit(8);
			#
			#
		} else if ($type == "category") {
			$this->db->select('ipi.category_name AS name, SUM(ipi.amount) AS total_pembelian');
			$this->db->from('ms_purchase_invoices pi');
			$this->db->join('ms_items_purchase_invoice ipi', 'pi.pi_number = ipi.pi_number');
			$this->db->group_by('ipi.category_name');
			$this->db->order_by('total_pembelian', 'DESC');
			if ($month) {
				$this->db->where('MONTH(pi.date)', $month);
			}
			$this->db->limit(8);
			#
		} else if ($type == "uom") {
			$this->db->select('ipi.uom_name AS name, SUM(ipi.amount) AS total_pembelian');
			$this->db->from('ms_purchase_invoices pi');
			$this->db->join('ms_items_purchase_invoice ipi', 'pi.pi_number = ipi.pi_number');
			$this->db->group_by('ipi.uom_name');
			$this->db->order_by('total_pembelian', 'DESC');
			if ($month) {
				$this->db->where('MONTH(pi.date)', $month);
			}
			$this->db->limit(8);
			#
		}

		if ($month) {
			$this->db->where('MONTH(pi.date)', $month);
		}
		$result = $this->db->get();
		foreach ($result->result() as $i => $r) {
			$someArray[] = array(
				'label'   => htmlspecialchars_decode($r->name),
				'value' => $r->total_pembelian,
				'format_value' => $this->Xin_model->currency_sign($r->total_pembelian),
				'bgcolor' => $c_color[$i]
			);
		}
		$Return['c_name'] = $c_name;
		$Return['d_rows'] = $d_rows;
		$Return['chart_data'] = $someArray;
		$this->output($Return);
		exit;
	}

	public function update_product()
	{
		// Ambil semua data dari tabel ms_products
		$query = $this->db->get('ms_products');
		$result = $query->result();

		// Perulangan untuk mengupdate data dengan product_number yang di-generate otomatis
		foreach ($result as $row) {
			$data = array(
				'product_number' => $this->generateProductNumber() // Panggil fungsi yang menghasilkan product_number secara otomatis
			);

			$this->db->where('product_id', $row->product_id); // Sesuaikan kolom dan kondisi WHERE yang sesuai
			$this->db->update('ms_products', $data);
		}
	}

	public function generateProductNumber()
	{
		$query = $this->db->order_by('product_number', 'ASC')->get('ms_products', 1)->row();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->product_number, 4));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("SKU-%07d", $nextNumericPart);
	}


	public function logs()
	{
		$id = $this->uri->segment(4);

		$explode = explode('-', $id);
		if ($explode[0] == 'PR') {
			// $res = $this->Purchase_model->read_pr_by_pr_number($id);
			$type = 'pr_number';
		} else if ($explode[0] == 'PO') {
			// $res = $this->Project_model->read_po_by_po_number($id);
			$type = 'po_number';
		} else if ($explode[0] == 'PD') {
			// $res = $this->Project_model->read_pd_by_pd_number($id);
			$type = 'pd_number';
		} else if ($explode[0] == 'PI') {
			// $res = $this->Project_model->read_pi_by_pi_number($id);
			$type = 'pi_number';
		} else {
			// $res = $this->Purchase_model->read_pr_by_pr_number($id);
			$type = 'pr_number';
		}

		$data = [];
		$log = $this->Purchase_model->read_pl($id, $type);

		// jika log tidak ditemukan
		if (is_null($log)) {
			echo "<script>alert('Force Error!. Conflict Data')</script>";
			redirect('admin', 'location', 500);
		}

		$pr = $this->Purchase_model->read_pr_by_pr_number($log->pr_number);

		if (!is_null($pr)) {

			$user = $this->Xin_model->read_user_info($pr->added_by);
			// user full name
			if (!is_null($user)) {
				$full_name = $user[0]->first_name . ' ' . $user[0]->last_name;
			} else {
				$full_name = '--';
			}

			$amount = $this->Purchase_model->get_amount_pr($pr->pr_number)->amount;

			$res = new stdClass();
			$res->date = $pr->issue_date;
			$res->pic = $full_name;
			$res->number = '<a href="' . site_url() . 'admin/purchase_requisitions/view/' . $pr->pr_number . '/">' . $pr->pr_number . '</a>';
			$res->origin = $this->lang->line('ms_purchase_requisitions');
			$res->amount = $this->Xin_model->currency_sign($amount + $pr->ref_delivery_fee);

			if ($log->pr_number != $id) {
				array_push($data, $res);
			}
		}

		$po = $this->Purchase_model->read_po_by_po_number($log->po_number);
		if (!is_null($po)) {
			$user = $this->Xin_model->read_user_info($po->added_by);
			// user full name
			if (!is_null($user)) {
				$full_name = $user[0]->first_name . ' ' . $user[0]->last_name;
			} else {
				$full_name = '--';
			}

			// get amount
			$amount = $this->Purchase_model->get_amount_po($po->po_number)->amount;

			$res = new stdClass();
			$res->date = $po->date;
			$res->pic = $full_name;
			$res->number = '<a href="' . site_url() . 'admin/purchase_orders/view/' . $po->po_number . '/">' . $po->po_number . '</a>';
			$res->origin = $this->lang->line('ms_purchase_orders');
			$res->amount = $this->Xin_model->currency_sign($amount + $po->delivery_fee);

			if ($log->po_number != $id) {
				array_push($data, $res);
			}
		}

		$pd = $this->Purchase_model->read_pd_by_pd_number($log->pd_number);
		if (!is_null($pd)) {

			$user = $this->Xin_model->read_user_info($pd->added_by);
			// user full name
			if (!is_null($user)) {
				$full_name = $user[0]->first_name . ' ' . $user[0]->last_name;
			} else {
				$full_name = '--';
			}

			$res = new stdClass();
			$res->date = $pd->date;
			$res->pic = $full_name;
			$res->number = '<a href="' . site_url() . 'admin/purchase_deliveries/view/' . $pd->pd_number . '/">' . $pd->pd_number . '</a>';
			$res->origin = $this->lang->line('ms_purchase_deliveries');
			$res->amount = $this->Xin_model->currency_sign($pd->delivery_fee);

			if ($log->pd_number != $id) {
				array_push($data, $res);
			}
		}

		$pi = $this->Purchase_model->read_pi_by_pi_number($log->pi_number);
		if (!is_null($pi)) {

			$user = $this->Xin_model->read_user_info($pi->added_by);
			// user full name
			if (!is_null($user)) {
				$full_name = $user[0]->first_name . ' ' . $user[0]->last_name;
			} else {
				$full_name = '--';
			}
			// get amount
			$amount = $this->Purchase_model->get_amount_pi($pi->pi_number)->amount;

			$res = new stdClass();
			$res->date = $pi->created_at;
			$res->pic = $full_name;
			$res->number = '<a href="' . site_url() . 'admin/purchase_invoices/view/' . $pi->pi_number . '/">' . $pi->pi_number . '</a>';
			$res->origin = $this->lang->line('ms_purchase_invoices');
			$res->amount = $this->Xin_model->currency_sign($amount + $pi->delivery_fee);

			if ($log->pi_number != $id) {
				array_push($data, $res);
			}
		}

		// dd($data);
		return $data;
	}

	public function get_log($where, $value_where)
	{
		$this->db->where($where, $value_where);
		return $this->db->get('ms_purchase_logs', 1)->row();
	}

	public function store_payment_pi()
	{
		// return $this->update_trans();
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		// pi number
		$id = $this->input->post('_token');
		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');
		$source_payment_account = $this->input->post('account_id'); // account_id

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

		//get_pi
		// $res = $this->Purchase_model->read_pi_by_pi_number($id);

		// uang yang dibayar
		$amount_paid = $this->input->post('amount_paid');

		$data = [
			[
				'account_id' => 34,
				'user_id' => $user_id,
				'account_trans_cat_id' => 6, // po payment
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $id, // pi_number
				'ref' => $payment_ref,
				'note' => "Kredit Pembayaran Purchase Invoice",
				'attachment' => $newName,
			],
			[
				'account_id' => $source_payment_account,
				'user_id' => $user_id,
				'account_trans_cat_id' => 6, // po payment
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'credit',
				'join_id' => $id, // pi_number
				'ref' => $payment_ref,
				'note' => "Kredit Pembayaran Purchase Invoice",
				'attachment' => $newName,
			],
			[
				'account_id' => 7, // account_id inventory
				'user_id' => $user_id,
				'account_trans_cat_id' => 6, // pi payment
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $id, // pi_number
				'ref' => $payment_ref,
				'note' => "Debit Pembayaran Purchase Invoice",
				'attachment' => $newName,
			]
		];

		$insert = $this->Account_trans_model->insert_payment($data);

		// check if all tagihan is paid or partially paid
		$check_tagihan = $this->Account_trans_model->get_payment(6, $id, 34);


		if ($check_tagihan->sisa_tagihan == 0) {
			// update status
			$this->Purchase_model->update_pi($id, ['status' => 2]);
		} else {
			// update status
			$this->Purchase_model->update_pi($id, ['status' => 1]);
		}

		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}

	public function store_payment()
	{

		// return $this->update_trans();
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		// pi number
		$id = $this->input->post('_token');
		$date = $this->input->post('date');
		$payment_ref = $this->input->post('payment_ref');
		$source_payment_account = $this->input->post('account_id'); // account_id

		$split = explode('-', $id);
		if ($split[0] == "PO") {
			$type = 'po_number';
		} elseif ($split[0] == "PI") {
			$type = 'pi_number';
		} else {
			$type = false;
		}

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

		// uang yang dibayar
		$amount_paid = $this->input->post('amount_paid');

		$log = $this->Purchase_model->read_pl($id, $type);

		$data = [
			[
				'account_id' => 34, // trade payable
				'user_id' => $user_id,
				'account_trans_cat_id' => 6,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'debit',
				'join_id' => $log->payment_number,
				'ref' => $payment_ref,
				'note' => "Kredit Pembayaran Purchase Order",
				'attachment' => $newName,
			],
			[
				'account_id' => $source_payment_account,
				'user_id' => $user_id,
				'account_trans_cat_id' => 6,
				'amount' => $amount_paid,
				'date' => $date,
				'type' => 'credit',
				'join_id' => $log->payment_number,
				'ref' => $payment_ref,
				'note' => "Kredit Pembayaran Purchase Order",
				'attachment' => $newName,
			]
		];

		$insert = $this->Account_trans_model->insert_payment($data);

		// check if all tagihan is paid or partially paid
		$check_tagihan = $this->Account_trans_model->get_purchasing_by_log($log);


		if ($check_tagihan->sisa_tagihan == 0) {
			// update status
			$this->Purchase_model->update_pi($id, ['status' => 2]);
		} else {
			// update status
			$this->Purchase_model->update_pi(
				$id,
				['status' => 1]
			);
		}
		// check if all tagihan is paid or partially paid
		// $check_tagihan = $this->Account_trans_model->get_payment(6, $id, 34);

		// if ($check_tagihan->sisa_tagihan == 0) {
		// 	// update status spend
		// 	$this->Account_spend_model->update($id, ['status' => 'paid']);
		// } else {
		// 	// update status spend
		// 	$this->Account_spend_model->update($id, ['status' => 'partially_paid']);
		// }

		if ($insert) {
			$Return['result'] = $this->lang->line('ms_title_payment_success');
			$this->output($Return);
		} else {
			$Return['error'] = $this->lang->line('ms_title_peyment_error');
			$this->output($Return);
		}
	}
}
