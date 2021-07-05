<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_products()
    {
        return $this->db->get('products')->result();
    }

    public function best_deal_product()
    {
        $data = $this->db->where('is_available', 1)
            ->order_by('current_discount', 'DESC')
            ->limit(1)
            ->get('products')
            ->row();

        return $data;
    }

    public function is_product_exist($id, $sku)
    {
        return ($this->db->where(array('id' => $id, 'sku' => $sku))->get('products')->num_rows() > 0) ? TRUE : FALSE;
    }

    public function product_data($id)
    {
        $data = $this->db->query("
            SELECT p.*, pc.name as category_name
            FROM products p
            JOIN product_category pc
                ON pc.id = p.category_id
            WHERE p.id = '$id'
        ")->row();

        return $data;
    }

    public function related_products($current, $category)
    {
        return $this->db->where(array('id !=' => $current, 'category_id' => $category))->limit(4)->get('products')->result();
    }

    public function getSku() {
        $this->db->select('sku');
        $this->db->from('products');
        // $this->db->query("SELECT 'sku' FROM products");
        // return $this->db->result_array();
        return $this->db->get()->result();
    }

    public function create_order(Array $data)
    {
        $this->db->insert('orders', $data);

        return $this->db->insert_id();
    }

    public function create_order_items($data)
    {
        return $this->db->insert_batch('order_items', $data);
    }

    public function getDiscount() {
        $this->db->from('products');
		$this->db->where('current_discount > 0');
		$query = $this->db->get();

		return $query->result();
    }

    public function orderGet($id) {
        $data = $this->db->query("
        SELECT id FROM orders WHERE order_number = '$id'
        ")->row();

		return $data;
    }
}