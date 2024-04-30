<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_transactions_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function store_peyment($data)
	{
	}

	
}
