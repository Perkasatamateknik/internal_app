<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function gel_all_product($query = false)
	{
		if ($query) {
			$this->db->like('product_name', $query);
			$this->db->or_like('product_number', $query);
		}

		return $this->db->get("ms_products");
	}

	public function get_like_product($id)
	{

		$sql = "SELECT * FROM ms_products WHERE product_name LIKE %$id%";
		$query = $this->db->query($sql);
		return $query;
	}

	public function read_info($id)
	{

		$sql = 'SELECT * FROM ms_products WHERE product_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	// Function to add record in table
	public function insert($data, $batch = false)
	{
		if ($batch) {
			$this->db->insert_batch('ms_products', $data);
		} else {
			$this->db->insert('ms_products', $data);
		}

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	// Function to add record in table
	public function insert_batch($data)
	{
		$this->db->insert_batch('ms_products', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Function to Delete selected record from table
	public function delete($id)
	{
		$this->db->where('product_id', $id);
		$this->db->delete('ms_products');
	}

	// Function to update record in table
	public function update($data, $id)
	{
		$this->db->where('product_id', $id);
		if ($this->db->update('ms_products', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function searchProduct($searchTerm)
	{
		// Query untuk mencari produk berdasarkan product_name atau product_number
		$this->db->select('product_id, product_name, product_number, uom_id, category_id');
		$this->db->like('product_name', $searchTerm);
		$this->db->or_like('product_number', $searchTerm);
		$result = $this->db->get('ms_products');
		return $result->result();
	}

	public function find_product($query)
	{
		$this->db->like('product_name', $query);
		$this->db->or_like('product_name', $query);
		return $this->db->get('ms_products', 10, 0)->result();
	}

	public function find_product_by_id($query)
	{
		// $this->db->where('product_id', $query);
		// return $this->db->get('ms_products')->row();

		$this->db->select('ms_products.*, ms_product_sub_categories.*, ms_product_categories.*, ms_measurement_units.*');
		$this->db->from('ms_products');
		$this->db->join('ms_product_sub_categories', 'ms_product_sub_categories.sub_category_id = ms_products.sub_category_id', 'left');
		$this->db->join('ms_product_categories', 'ms_product_categories.category_id = ms_product_sub_categories.category_id', 'left');
		$this->db->join('ms_measurement_units', 'ms_measurement_units.uom_id = ms_products.uom_id', 'left');
		$this->db->where('ms_products.product_id', $query);
		return $this->db->get()->row();
	}

	public function kd_number()
	{
		$query = $this->db->select('product_number')
			->from('ms_products')
			->order_by('product_number', 'desc')
			->limit(1)->get()->row();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->product_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("KD-%07d", $nextNumericPart);
	}

	public function set_ulang()
	{
		$data = $this->gel_all_product()->result();

		foreach ($data as $r) {

			$kd = $this->kd_number();

			$this->db->update('ms_products', ['product_number' => $kd], ['product_id' => $r->product_id]);
		}


		echo "success!";
	}

	public function set_po()
	{
		// Ambil semua data dari tabel ms_items_purchase_order
		$purchase_orders = $this->db->get('ms_items_purchase_order')->result();

		// Lakukan perulangan pada setiap baris data
		foreach ($purchase_orders as $purchase_order) {
			// Cari data produk berdasarkan product_id
			$product_id = $purchase_order->product_id;
			$product = $this->db->get_where('ms_products', array('product_id' => $product_id))->row();

			// Perbarui product_number dalam ms_items_purchase_order dengan product_number dari ms_products
			if ($product) {
				$new_product_number = $product->product_number;
				$this->db->set('product_number', $new_product_number);
				$this->db->where('product_id', $product_id);
				$this->db->update('ms_items_purchase_order');
			}
		}

		// Mengembalikan jumlah baris yang diperbarui
		return count($purchase_orders);
	}
}
