<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banner_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_banners()
    {
        return $this->db->order_by('title', 'ASC')->get('banners')->result();
    }

    public function banner_data($id)
    {
        return $this->db->where('id', $id)->get('banners')->row();
    }

    public function is_banner_exist($id)
    {
        return ($this->db->where('id', $id)->get('banners')->num_rows() > 0) ? TRUE : FALSE;
    }

    public function is_banner_have_image($id)
    {
        $data = $this->banner_data($id);
        $file = $data->picture_name;

        return file_exists('./assets/uploads/banners/'. $file) ? TRUE : FALSE;
    }

    public function edit_banner($id, $product)
    {
        return $this->db->where('id', $id)->update('banners', $product);
    }

    public function delete_banner($id)
    {
        return $this->db->where('id', $id)->delete('banners');
    }

    public function add_new_banner(Array $banner)
    {
        $this->db->insert('banners', $banner);

        return $this->db->insert_id();
    }
    




}    