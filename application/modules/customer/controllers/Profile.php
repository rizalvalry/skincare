<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        verify_session('customer');

        $this->load->model(array(
            'profile_model' => 'profile'
        ));
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data = $this->profile->get_profile();

        $params['title'] = $data->name;
        $user['user'] = $data;
        $user['flash'] = $this->session->flashdata('profile');
		
		$latitude = $user['user']->latitude;
		$longitude = $user['user']->longitude;
		
		// Panggil fungsi get_address_from_coordinates untuk mendapatkan alamat
		$user['user']->api_address = $this->get_address_from_coordinates($latitude, $longitude);

		// var_dump($user);die();

        $this->load->view('header', $params);
        $this->load->view('profile', $user);
        $this->load->view('footer');
    }

	public function get_address_from_coordinates($latitude, $longitude) {
		// URL untuk request ke OpenStreetMap API
		$url = "https://nominatim.openstreetmap.org/reverse?lat=$latitude&lon=$longitude&format=json&addressdetails=1";
		
		// Inisialisasi cURL
		$ch = curl_init();
	
		// Set opsi cURL untuk mengatur User-Agent
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout setelah 10 detik
		curl_setopt($ch, CURLOPT_USERAGENT, 'MyAppName/1.0 (contact@example.com)'); // Sesuaikan dengan nama aplikasi Anda
	
		// Eksekusi cURL
		$response = curl_exec($ch);
	
		// Cek error cURL
		if(curl_errno($ch)) {
			$error_msg = curl_error($ch);
			curl_close($ch);
			return "cURL Error: " . $error_msg;
		}
		
		// Tutup cURL
		curl_close($ch);
	
		// Cek apakah respons berhasil
		if ($response) {
			$data = json_decode($response, true);
	
			// Pastikan ada data address
			if (isset($data['address'])) {
				$address = $data['address'];
	
				// Ambil informasi alamat yang lebih lengkap
				$village = isset($address['village']) ? $address['village'] : '';
				$district = isset($address['city_district']) ? $address['city_district'] : '';
				$city = isset($address['city']) ? $address['city'] : '';
				$state = isset($address['state']) ? $address['state'] : '';
				$postcode = isset($address['postcode']) ? $address['postcode'] : '';
				$country = isset($address['country']) ? $address['country'] : '';
	
				// Gabungkan alamat dengan format yang diinginkan
				$full_address = "$village, $district, $city, $state, $postcode, $country";
	
				return $full_address;
			} else {
				return 'Alamat tidak ditemukan';
			}
		} else {
			return 'Error dalam menghubungi API';
		}
	}
	
	
	

    public function edit_name()
    {
        $this->form_validation->set_rules('name', 'Nama lengkap', 'required|max_length[32]|min_length[4]');

        if ($this->form_validation->run() === FALSE)
        {
            $this->index();
        }
        else
        {
            $data = new stdClass();

            $data->name = $this->input->post('name');
            $data->phone_number = $this->input->post('phone_number');
            $data->address = $this->input->post('address');

            $profile = $this->profile->get_profile();
            $old_profile = $profile->profile_picture;

            if (isset($_FILES) && @$_FILES['file']['error'] == '0') {
                $config['upload_path'] = './assets/uploads/users/';
                $config['allowed_types'] = 'jpg|png';
                $config['max_size'] = 2048;
                
                $this->load->library('upload', $config);

                if ($this->upload->do_upload('file'))
                {
                    if ($old_profile)
                    {
                        unlink('./assets/uploads/users/'. $old_profile);
                    }

                    $file_data = $this->upload->data();
                    $data->profile_picture = $file_data['file_name'];
                }
                else
                {
                    $errors = $this->upload->display_errors();
                    $errors .= '<p>';
                    $errors .= anchor('profile', '&laquo; Kembali');
                    $errors .= '</p>';

                    show_error($errors);
                }
            }

            $flash_message = ($this->profile->update($data)) ? 'Profil berhasil diperbarui!' : 'Terjadi kesalahan';
            
            $this->session->set_flashdata('profile', $flash_message);
            redirect('customer/profile');
        }
    }

    public function edit_account()
    {
        $this->form_validation->set_rules('username', 'Username', 'required|max_length[16]|min_length[4]');
        $this->form_validation->set_rules('password', 'Password', 'min_length[4]');

        if ($this->form_validation->run() === FALSE)
        {
            $this->index();
        }
        else
        {
            $data = new stdClass();
            $profile = $this->profile->get_profile();

            $get_password = $this->input->post('password');

            if ( empty($get_password)) {
                $password = $profile->password;
            }
            else {
                $password = password_hash($get_password, PASSWORD_BCRYPT);
            }

            $data->username = $this->input->post('username');
            $data->password = $password;

            $flash_message = ($this->profile->update_account($data)) ? 'Akun berhasil diperbarui' : 'Terjadi kesalahan';
            
            $this->session->set_flashdata('profile', $flash_message);
            $this->session->set_flashdata('show_tab', 'akun');

            redirect('customer/profile');
        }
    }

    public function edit_email()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[32]|min_length[10]');

        if ($this->form_validation->run() === FALSE)
        {
            $this->index();
        }
        else
        {
            $data = new stdClass();

            $data->email = $this->input->post('email');
            
            $flash_message = ($this->profile->update_account($data)) ? 'Email berhasil diperbarui' : 'Terjadi kesalahan';
            
            $this->session->set_flashdata('profile', $flash_message);
            $this->session->set_flashdata('show_tab', 'email');

            redirect('customer/profile');
        }
    }
}