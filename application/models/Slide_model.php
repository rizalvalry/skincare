<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slide_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();

    }

    public function slider() {
        return $this->db->order_by('title', 'ASC')->get('banners')->result();
    }

    public function slider_brands() {
        return $this->db->order_by('id', 'ASC')->get('brands')->result();
    }



}