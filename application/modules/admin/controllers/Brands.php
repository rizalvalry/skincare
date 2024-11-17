<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brands extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        verify_session('admin');

        $this->load->model('brand_model', 'brand');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $params['title'] = 'Kelola Brands';
        $brands['brands'] = $this->brand->get_all_brands();
        $this->load->view('header', $params);
        $this->load->view('brands/brands', $brands);
        $this->load->view('footer');
    }

    public function _get_brand_list()
    {
        $brands = $this->brand->get_all_brands();
        $n = 0;

        foreach ($brands as $brand)
        {
            $n++;
        }

        return $brands;
    }

    public function brand_api()
    {
        $action = $this->input->get('action');

        switch ($action) {
            case 'brand_list' :
                $brands['data'] = $this->_get_brand_list();

                $response = $brands;
            break;
            case 'view_data' :
                $id = $this->input->get('id');

                $data['data'] = $this->brand->brand_data($id);
                $response = $data;
            break;
            case 'add_brand' :
                $picture = $this->input->post('picture');

                $config['upload_path'] = './assets/uploads/brands/';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size'] = 2048;

                $this->load->library('upload', $config);

                if ( isset($_FILES['picture']) && @$_FILES['picture']['error'] == '0')
                {
                    if ( ! $this->upload->do_upload('picture'))
                    {
                        $error = array('error' => $this->upload->display_errors());

                        show_error($error);
                    }
                    else
                    {
                        $upload_data = $this->upload->data();
                        $file_name = $upload_data['file_name'];
                        
        
                    }
                }
                
                $banner['picture_name'] = $file_name;

                $this->brand->add_brand($banner);
                $coupons['data'] =  $this->_get_brand_list();
            
                $response = $coupons;
            break;
            case 'delete_brand' :
                $id = $this->input->post('id');

                $this->brand->delete_brand($id);
                $response = array('code' => 204, 'message' => 'Kupon berhasil dihapus!');
            break;
            case 'edit_brand' :
                $id = $this->input->post('id');
                $picture_name = $this->input->post('picture_name');

                $id = $this->input->post('id');
                $data = $this->brand->brand_data($id);
                $current_picture = $data->picture_name;

                $config['upload_path'] = './assets/uploads/brands/';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size'] = 2048;

                $this->load->library('upload', $config);

            if ( isset($_FILES['picture']) && @$_FILES['picture']['error'] == '0')
            {
                if ( $this->upload->do_upload('picture'))
                {
                    $upload_data = $this->upload->data();
                    $new_file_name = $upload_data['file_name'];

                    if ( $this->brand->is_brand_have_image($id))
                    {
                        $file = './assets/uploads/brands/'. $current_picture;

                        $file_name = $new_file_name;
                        unlink($file);
                    }
                    else
                    {
                        $file_name = $new_file_name;
                    }
                }
                else
                {
                    show_error($this->upload->display_errors());
                }
            }
            else
            {
                $file_name = ($this->brand->is_brand_have_image($id)) ? $current_picture : NULL;
            }

                $banner['picture_name'] = $file_name;

                $this->brand->edit_brand($id, $banner);
                $coupons['data'] =  $this->_get_brand_list();

                $response = array('code' => 201, 'message' => 'Kupon berhasil diperbarui');
            break;
        }

        $response = json_encode($response);
        $this->output->set_content_type('application/json')
            ->set_output($response);
    }
    
}
