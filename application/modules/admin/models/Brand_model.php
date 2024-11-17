<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_brands()
    {
        return $this->db->order_by('picture_name', 'ASC')->get('brands')->result();
    }

    public function add_brand(Array $data)
    {
        $this->db->insert('brands', $data);

        return $this->db->insert_id();
    }

    public function brand_data($id)
    {
        return $this->db->where('id', $id)->get('brands')->row();
    }

    public function edit_brand($id, $data)
    {
        return $this->db->where('id', $id)->update('brands', $data);
    }

    public function delete_brand($id)
    {
        return $this->db->where('id', $id)->delete('brands');
    }

    public function is_brand_have_image($id)
    {
        $data = $this->brand_data($id);
        $file = $data->picture_name;

        return file_exists('./assets/uploads/brands/'. $file) ? TRUE : FALSE;
    }

}
