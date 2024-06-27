<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contact_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function generate_contact_id($kategori_id)
	{
		$prefix = $this->get_contact_type($kategori_id)->prefix;

		$last_contact = $this->get_last_contact_id($kategori_id);

		if ($last_contact) {
			$last_number = intval(substr($last_contact->contact_id, 4));
			$new_number = $last_number + 1;
		} else {
			$new_number = 1;
		}

		return $prefix . sprintf('%05d', $new_number);  //  digit padding dengan nol
	}

	public function get_last_contact_id($type)
	{
		$this->db->where('contact_type_id', $type);
		$this->db->order_by('contact_id', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get('ms_contacts');
		return $query->row();
	}

	public function find_best_match($contact_name, $threshold = 70)
	{
		$contacts = $this->get_all_contact()->result();
		$bestMatch = null;
		$highestSimilarity = 0;

		$contact_name_preprocessed = preprocess_string($contact_name);

		foreach ($contacts as $contact) {
			$contact_preprocessed = preprocess_string($contact->contact_name);
			$similarity = $this->levenshtein_similarity($contact_name_preprocessed, $contact_preprocessed);
			if ($similarity > $highestSimilarity) {
				$highestSimilarity = $similarity;
				$bestMatch = $contact;
			}
		}

		if ($highestSimilarity >= $threshold) {
			return $bestMatch;
		} else {
			return null; // Tidak ada kecocokan yang cukup mirip
		}
	}

	private function levenshtein_similarity($str1, $str2)
	{
		$distance = levenshtein($str1, $str2);
		$max_len = max(strlen($str1), strlen($str2));
		if ($max_len == 0) {
			return 100; // Jika kedua string kosong
		}
		return ((1 - ($distance / $max_len)) * 100);
	}

	public function get_all_contact($filter = false)
	{
		if ($filter) {
			$this->db->where('contact_type_id', $filter);
		}
		return $this->db->get("ms_contacts");
	}
	public function get_contact($id)
	{
		// return $this->db->select(["ms_contacts.*", "xin_countries.country_id", "xin_countries.country_name", "ms_contact_types.type_id", "ms_contact_types.contact_type"])
		return $this->db->select("*")
			->from('ms_contacts')
			// ->join('ms_contact_types', 'ms_contacts.contact_type_id=ms_contact_types.type_id', 'RIGHT')
			// ->join('xin_countries', 'ms_contacts.country=xin_countries.country_id', 'RIGHT')
			->where('ms_contacts.contact_id', $id)
			->get()->row();
	}

	public function get($id)
	{
		return $this->db->where('contact_id', $id)
			->get('ms_contacts');
	}

	public function find_contact($query)
	{
		return $this->db->select(['ms_contacts.*', 'ms_contact_types.contact_type'])
			->from('ms_contacts')
			->join('ms_contact_types', 'ms_contacts.contact_type_id=ms_contact_types.type_id')
			->like('contact_name', $query)->get()->result();
	}
	public function find_contact_by_id($query)
	{
		return $this->db->select(['ms_contacts.*', 'ms_contact_types.contact_type'])
			->from('ms_contacts')
			->join('ms_contact_types', 'ms_contacts.contact_type_id=ms_contact_types.type_id')
			->where('contact_id', $query)->get()->row();
		// $this->db->where('contact_id', $query);
		// return $this->db->get('ms_vendors')->row();
	}

	public function count_contacts($filter = false)
	{
		if ($filter) {
			$query = $this->db->select('COUNT(ms_contacts.contact_id) as count')->where('contact_type_id', $filter)->get('ms_contacts')->row()->count;
		} else {
			$query = $this->db->select('COUNT(ms_contacts.contact_id) as count')->get('ms_contacts')->row()->count;
		}
		return sprintf("%03d", $query);
	}

	// Function to update selected record from table
	public function update($id, $data)
	{
		$this->db->where('contact_id', $id);
		return $this->db->update('ms_contacts', $data);
	}

	// Function to update selected record from table
	public function delete($id, $del_file)
	{
		$this->db->trans_start();

		// delete contact
		$this->db->where('contact_id', $id)->delete('ms_contacts');


		// get all liabilities
		$liabilities = $this->db->where('contact_id', $id)->get('ms_liabilities')->result();
		foreach ($liabilities as $r) {
			// hapus seluruh item terkait liability
			$this->db->where('trans_number', $r->trans_number)->delete('ms_liability_trans');

			// hapus semua transaksi terkait
			$this->db->where('join_id', $r->trans_number)->delete('ms_finance_account_transactions');
		}

		// hapus data liabilities
		$this->db->where('contact_id', $id)->delete('ms_liabilities');


		// get all receivables
		$receivables = $this->db->where('contact_id', $id)->get('ms_receivables')->result();
		foreach ($receivables as $r) {
			// hapus seluruh item terkait receivable
			$this->db->where('trans_number', $r->trans_number)->delete('ms_receivable_trans');

			// hapus semua transaksi terkait
			$this->db->where('join_id', $r->trans_number)->delete('ms_finance_account_transactions');
		}

		// hapus data receivables
		$this->db->where('contact_id', $id)->delete('ms_receivables');

		if ($del_file) {

			// get data
			$files = $this->db->where('access_id', $id)->get('ms_files')->result();

			foreach ($files as $file) {
				// check file in dir
				if (file_exists("./uploads/contact/" . $file->file_name)) {
					// Delete the file
					unlink("./uploads/contact/" . $file->file_name);
				}
			}

			$this->db->where('access_id', $id)->delete('ms_files');
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function get_all_contact_type()
	{
		return $this->db->get("ms_contact_types");
	}

	public function get_contact_type($id)
	{
		return $this->db->get_where("ms_contact_types", ['type_id' => $id])->row();
	}

	public function store($data)
	{
		return $this->db->insert('ms_contacts', $data);
	}

	public function store_type($data)
	{
		$this->db->insert('ms_contact_types', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_type($id)
	{
		$this->db->where('type_id', $id);
		return $this->db->delete('ms_contact_types');
	}

	public function update_type($id, $data)
	{
		$this->db->where('type_id', $id);
		return $this->db->update('ms_contact_types', $data);
	}

	// new row
	public function find_contact_type($query = false)
	{
		if ($query) {
			$this->db->like('contact_type', $query);
		}

		// limit record to 10
		$this->db->limit(10);
		// order by account_name
		$this->db->order_by('contact_type', 'ASC');
		return $this->db->get("ms_contact_types")->result();
	}

	public function get_all_trans($contact_id)
	{

		$liabilities = $this->db->select('trans_number')->where('contact_id', $contact_id)->get('ms_liabilities')->result();

		$trans = [];
		foreach ($liabilities as $l) {
			foreach ($this->db->select(['ms_finance_account_transactions.*', 'ms_finance_account_trans_categories.trans_type'])
				->from('ms_finance_account_transactions')
				->join('ms_finance_account_trans_categories', 'ms_finance_account_transactions.account_trans_cat_id=ms_finance_account_trans_categories.trans_cat_id', 'inner')
				->where('ms_finance_account_transactions.join_id', $l->trans_number)
				->where('ms_finance_account_transactions.type', 'credit')
				->get()->result() as $li) {
				$trans[] = $li;
			}
		}

		$receivables = $this->db->select('trans_number')->where('contact_id', $contact_id)->get('ms_receivables')->result();

		foreach ($receivables as $r) {
			foreach ($this->db->select(['ms_finance_account_transactions.*', 'ms_finance_account_trans_categories.trans_type'])
				->from('ms_finance_account_transactions')
				->join('ms_finance_account_trans_categories', 'ms_finance_account_transactions.account_trans_cat_id=ms_finance_account_trans_categories.trans_cat_id', 'inner')
				->where('ms_finance_account_transactions.join_id', $r->trans_number)
				->where('ms_finance_account_transactions.type', 'debit')
				->get()->result() as $ri) {
				$trans[] = $ri;
			}
		}

		$purchase_invoices = $this->db->select('pi_number')->where('contact_id', $contact_id)->get('ms_purchase_invoices')->result();
		foreach ($purchase_invoices as $r) {
			$log = $this->db->select('payment_number')->where('pi_number', $r->pi_number)->get('ms_purchase_logs')->row();
			// var_dump($log);
			if (!is_null($log)) {
				foreach ($this->db->select(['ms_finance_account_transactions.*', 'ms_finance_account_trans_categories.trans_type'])
					->from('ms_finance_account_transactions')
					->join('ms_finance_account_trans_categories', 'ms_finance_account_transactions.account_trans_cat_id=ms_finance_account_trans_categories.trans_cat_id', 'left')
					->where('ms_finance_account_transactions.join_id', $log->payment_number)
					->where('ms_finance_account_transactions.type', 'credit')
					->get()->result() as $pi) {

					// var_dump($pi);
					$trans[] = $pi;
				}
			}
		}


		$expenses = $this->db->select('trans_number')->where('beneficiary', $contact_id)->get('ms_finance_expenses')->result();
		foreach ($expenses as $ex) {
			foreach ($this->db->select(['ms_finance_account_transactions.*', 'ms_finance_account_trans_categories.trans_type'])
				->from('ms_finance_account_transactions')
				->join('ms_finance_account_trans_categories', 'ms_finance_account_transactions.account_trans_cat_id=ms_finance_account_trans_categories.trans_cat_id', 'inner')
				->where('ms_finance_account_transactions.join_id', $ex->trans_number)
				->where('ms_finance_account_transactions.type', 'credit')
				->get()->result() as $ri) {
				$trans[] = $ri;
			}
		}

		$spends = $this->db->select('trans_number')->where('beneficiary', $contact_id)->get('ms_finance_account_spends')->result();
		foreach ($spends as $ex) {
			foreach ($this->db->select(['ms_finance_account_transactions.*', 'ms_finance_account_trans_categories.trans_type'])
				->from('ms_finance_account_transactions')
				->join('ms_finance_account_trans_categories', 'ms_finance_account_transactions.account_trans_cat_id=ms_finance_account_trans_categories.trans_cat_id', 'inner')
				->where('ms_finance_account_transactions.join_id', $ex->trans_number)
				->where('ms_finance_account_transactions.type', 'credit')
				->get()->result() as $ri) {
				$trans[] = $ri;
			}
		}

		$receives = $this->db->select('trans_number')->where('contact_id', $contact_id)->get('ms_finance_account_receives')->result();
		foreach ($receives as $re) {
			foreach ($this->db->select(['ms_finance_account_transactions.*', 'ms_finance_account_trans_categories.trans_type'])
				->from('ms_finance_account_transactions')
				->join('ms_finance_account_trans_categories', 'ms_finance_account_transactions.account_trans_cat_id=ms_finance_account_trans_categories.trans_cat_id', 'inner')
				->where('ms_finance_account_transactions.join_id', $re->trans_number)
				->where('ms_finance_account_transactions.type', 'credit')
				->get()->result() as $ri) {
				$trans[] = $ri;
			}
		}

		return $trans;
	}
}
