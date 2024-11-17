<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banners extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        verify_session('admin');

        $this->load->model('banner_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $params['title'] = 'Kelola Banners';
        $categories['categories'] = $this->banner_model->get_all_banners();
        $this->load->view('header', $params);
        $this->load->view('banners/banners', $categories);
        $this->load->view('footer');
    }

    public function _get_banner_list()
    {
        $banners = $this->banner_model->get_all_banners();
        $n = 0;

        foreach ($banners as $banner)
        {
            $banners[$n]->is_active = ($banner->is_active == 1) ? 'Aktif' : 'Tidak Aktif';
            $n++;
        }

        return $banners;
    }


    public function banner_api()
    {
        $action = $this->input->get('action');

        switch ($action) {
            case 'banner_list' :
                $banners['data'] = $this->_get_banner_list();

                $response = $banners;
            break;
            case 'view_data' :
                $id = $this->input->get('id');

                $data['data'] = $this->banner_model->banner_data($id);
                $response = $data;
            break;
            case 'delete_banner' :
                $id = $this->input->post('id');
                $this->banner_model->delete_banner($id);
                $response = array('code' => 204, 'message' => 'Banner berhasil dihapus!');
            break;
        }

        $response = json_encode($response);
        $this->output->set_content_type('application/json')
            ->set_output($response);
    }


    public function add_new_banner()
    {
        $params['title'] = 'Tambah Banner Baru';

        $banner['flash'] = $this->session->flashdata('add_new_banner_flash');

        $this->load->view('header', $params);
        $this->load->view('banners/add_new_banner', $banner);
        $this->load->view('footer');
    }


    public function add_banner()
    {
        $this->form_validation->set_error_delimiters('<div class="form-error text-danger font-weight-bold">', '</div>');

        $this->form_validation->set_rules('title', 'Nama Title', 'trim|required|min_length[4]|max_length[255]');
        $this->form_validation->set_rules('subtitle', 'Subtitle', 'trim|required');
        $this->form_validation->set_rules('button_text', 'Button Text', 'required|min_length[4]|max_length[20]');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->add_new_banner();
        }
        else
        {
            $title = $this->input->post('title');
            $subtitle = $this->input->post('subtitle');
            $button_text = $this->input->post('button_text');
            $is_active = $this->input->post('is_active');

            $config['upload_path'] = './assets/uploads/banners/';
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


            $banner['title'] = $title;
            $banner['subtitle'] = $subtitle;
            $banner['button_text'] = $button_text;
            $banner['picture_name'] = $file_name;
            $banner['is_active'] = $is_active;

            $this->banner_model->add_new_banner($banner);
            $this->session->set_flashdata('add_new_banner_flash', 'Banner baru berhasil ditambahkan!');

            redirect('admin/banners/add_new_banner');
        }
    }


    public function edit($id = 0)
    {
        if ( $this->banner_model->is_banner_exist($id))
        {
            $data = $this->banner_model->banner_data($id);

            $params['title'] = 'Edit '. $data->title;

            $banner['flash'] = $this->session->flashdata('edit_banner_flash');
            $banner['banner'] = $data;

            $this->load->view('header', $params);
            $this->load->view('banners/edit_banner', $banner);
            $this->load->view('footer');
        }
        else
        {
            show_404();
        }
    }


    public function edit_banner()
    {
        $this->form_validation->set_error_delimiters('<div class="form-error text-danger font-weight-bold">', '</div>');

        $this->form_validation->set_rules('title', 'Nama Title', 'trim|required|min_length[4]|max_length[255]');
        $this->form_validation->set_rules('subtitle', 'Subtitle', 'trim|required');
        $this->form_validation->set_rules('button_text', 'Button Text', 'required|min_length[4]|max_length[20]');
        
        if ($this->form_validation->run() == FALSE)
        {
            $id = $this->input->post('id');
            $this->edit($id);
        }
        else
        {
            $id = $this->input->post('id');
            $data = $this->banner_model->banner_data($id);
            $current_picture = $data->picture_name;

            $title = $this->input->post('title');
            $subtitle = $this->input->post('subtitle');
            $button_text = $this->input->post('button_text');
            $is_active = $this->input->post('is_active');

            $config['upload_path'] = './assets/uploads/banners/';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            if ( isset($_FILES['picture']) && @$_FILES['picture']['error'] == '0')
            {
                if ( $this->upload->do_upload('picture'))
                {
                    $upload_data = $this->upload->data();
                    $new_file_name = $upload_data['file_name'];

                    if ( $this->banner_model->is_banner_have_image($id))
                    {
                        $file = './assets/uploads/banners/'. $current_picture;

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
                $file_name = ($this->banner_model->is_banner_have_image($id)) ? $current_picture : NULL;
            }

            $banner['title'] = $title;
            $banner['subtitle'] = $subtitle;
            $banner['button_text'] = $button_text;
            $banner['picture_name'] = $file_name;
            $banner['is_active'] = $is_active;

            $this->banner_model->edit_banner($id, $banner);
            $this->session->set_flashdata('edit_banner_flash', 'Banner berhasil diperbarui!');

            redirect('admin/banners/edit/'. $id);
        }
    }

    
}