<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class MY_Controller extends CI_Controller
{
	public $uri;
	public $session;
	public $Xin_model;
	public $Tax_model;
	public $Exin_model;
	public $Vendor_model;
	public $Product_model;
	public $Project_model;
	// public $Department_model;
	public $Company_model;
	// public $Purchase_items_model;
	// public $Purchase_model;
	public $lang;
	public $security;
	public $input;
	public $form_validation;
	public $load;

	public $role;
	public $roles;



	public function __construct()
	{

		parent::__construct();
		$ci = &get_instance();
		$ci->load->helper('language');
		$this->load->library('session');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('url_helper');
		$this->load->helper('html');
		$this->load->database();
		$this->load->helper('security');
		$this->load->library('form_validation');
		$this->load->model("Xin_model");
		$this->load->model("Company_model");
		$this->load->model("Purchase_model");

		// set default timezone  
		$system = $this->read_setting_info(1);

		$session = $this->session->userdata('username');
		if (empty($session)) {
			$default_timezone = $system[0]->system_timezone;
			date_default_timezone_set($default_timezone);
		} else {
			$user_info = $this->Xin_model->read_user_info($session['user_id']);
			$company_info = $this->Company_model->read_company_information($user_info[0]->company_id);
			if (!is_null($company_info)) {
				$default_timezone = $company_info[0]->default_timezone;
				if ($default_timezone == '') {
					$default_timezone = $system[0]->system_timezone;
				} else {
					$default_timezone = $default_timezone;
				}
				date_default_timezone_set($default_timezone);
			} else {
				$default_timezone = $system[0]->system_timezone;
				date_default_timezone_set($default_timezone);
			}
		}

		// dd($session);

		// set language
		$siteLang = $ci->session->userdata('site_lang');
		if ($system[0]->default_language == '') {
			$default_language = 'english';
		} else {
			$default_language = $system[0]->default_language;
		}
		if ($siteLang) {
			$ci->lang->load('hrsale', $siteLang);
		} else {
			$ci->lang->load('hrsale', $default_language);
		}
	}

	// get setting info
	public function read_setting_info($id)
	{

		$condition = "setting_id =" . "'" . $id . "'";
		$this->db->select('*');
		$this->db->from('xin_system_setting');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return null;
		}
	}


	public function upload_attachment($path, $prefix, $allowed_type = 'gif|jpg|png|pdf', $max_size = '10240')
	{
		if (isset($_FILES['attachment']['name'])) {
			// doing attachment
			$config['allowed_types'] = $allowed_type;
			$config['max_size'] = $max_size; // max_size in kb
			$config['upload_path'] = $path;

			$filename = $_FILES['attachment']['name'];

			// get extention
			$extension = pathinfo($filename, PATHINFO_EXTENSION);

			$newName = date('YmdHis') . "_" . str_replace(" ", "_", $prefix) . '.' . $extension;

			$config['filename'] = $newName;

			//load upload class library
			$this->load->library('upload', $config);

			// $upload
			$up = $this->upload->do_upload('attachment');

			if ($up) {
				return $newName;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
}
