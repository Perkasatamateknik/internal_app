	<?php
	defined('BASEPATH') or exit('No direct script access allowed');

	class Files_ms_model extends CI_Model
	{

		public function __construct()
		{
			parent::__construct();
			$this->load->database();
		}


		//! Role Purchase Requisition 
		public function get_by_access_id($type, $id)
		{
			$this->db->where('file_access', $type);
			$this->db->where('access_id', $id);
			return $this->db->get("ms_files");
		}
	}
