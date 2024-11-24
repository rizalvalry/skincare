<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->library('cart');
        $this->load->model(array(
            'product_model' => 'product',
            'customer_model' => 'customer'
        ));

        $params = array('server_key' => 'SB-Mid-server-j-Us55kzzKRBrGV95aRebZ9H', 'production' => false);
		$this->load->library('midtrans');
		$this->midtrans->config($params);
		$this->load->helper('url');	

    }

    public function product($id = 0, $sku = '')
    {
        if ($id == 0 || empty($sku))
        {
            show_error('Akses tidak sah!');
        }
        else
        {
            if ($this->product->is_product_exist($id, $sku))
        {
            // Ambil data produk
            $data = $this->product->product_data($id);
            
            // Ambil sub-produk yang terkait dengan produk ini
            $sub_products = $this->product->get_sub_products($id);
            
            // Ambil produk terkait
            $related_products = $this->product->related_products($data->id, $data->category_id);

            // Siapkan data untuk dikirimkan ke view
            $product['product'] = $data;
            $product['sub_products'] = $sub_products;
            $product['related_products'] = $related_products;
			// var_dump($product['product']);
			// die();

            // Load header, view, dan footer
            get_header($data->name .' | '. get_settings('store_tagline'));
            get_template_part('shop/view_single_product', $product);
            get_footer();
        }
        else
        {
            show_404();
        }
        }
    }

    public function cart()
	{
		$cart['carts'] = $this->cart->contents();
		$cart['total_cart'] = $this->cart->total();

		$ongkir = ($cart['total_cart'] >= get_settings('min_shop_to_free_shipping_cost')) ? 0 : get_settings('shipping_cost');
		$cart['total_price'] = $cart['total_cart'] + $ongkir;

		// Panggil API untuk mendapatkan kurir Gojek dan Grab
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://api.biteship.com/v1/couriers',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => [
				'Authorization: biteship_test.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoia3VsaW5lcmJldGF3aSIsInVzZXJJZCI6IjY3NDE1M2I1YzM2NjI2MDAxMzFkZjZlMiIsImlhdCI6MTczMjM1NDM4M30.d8Bw1wtln4qAk4Ss5yKPYyAe4sZ4j88DhBgbm4mJ6CU',
				'Content-Type: application/json'
			],
		]);

		$response = curl_exec($curl);
		curl_close($curl);

		// Decode response JSON
		$couriers = json_decode($response, true)['couriers'] ?? [];
		
		// Filter hanya kurir Gojek dan Grab
		$cart['couriers'] = array_filter($couriers, function ($courier) {
			return in_array($courier['courier_code'], ['gojek', 'grab']);
		});

		// Jika Anda ingin memeriksa hasil parsing:
		// var_dump($cart['couriers']); die();

		get_header('Keranjang Makanan');
		get_template_part('shop/cart', $cart);
		get_footer();
	}


	public function get_shipping_cost()
	{
		// Pastikan data yang diterima dari AJAX sudah benar
		$courier = $this->input->post('courier');
		$origin_latitude = $this->input->post('origin_latitude');
		$origin_longitude = $this->input->post('origin_longitude');
		$destination_latitude = $this->input->post('destination_latitude');
		$destination_longitude = $this->input->post('destination_longitude');
		$items = $this->input->post('items');

		// Panggil API untuk mendapatkan tarif ongkir
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://api.biteship.com/v1/rates/couriers',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => [
				'Authorization: biteship_test.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoia3VsaW5lcmJldGF3aSIsInVzZXJJZCI6IjY3NDE1M2I1YzM2NjI2MDAxMzFkZjZlMiIsImlhdCI6MTczMjM1NDM4M30.d8Bw1wtln4qAk4Ss5yKPYyAe4sZ4j88DhBgbm4mJ6CU',
				'Content-Type: application/json'
			],
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode([
				"origin_latitude" => $origin_latitude,
				"origin_longitude" => $origin_longitude,
				"destination_latitude" => $destination_latitude,
				"destination_longitude" => $destination_longitude,
				"couriers" => $courier,
				"items" => $items
			])
		]);

		$response = curl_exec($curl);
		curl_close($curl);

		// Decode response JSON
		$data = json_decode($response, true);

		// Cek apakah tarif ongkir ditemukan dan kirim ke tampilan
		if (isset($data['success']) && $data['success']) {
			// Ambil tarif ongkir dari response (sesuaikan dengan struktur API yang digunakan)
			$shipping_cost = $data['rates'][0]['rate'] ?? 0; // Misalnya jika struktur response seperti ini
			echo json_encode([
				'success' => true,
				'shipping_cost' => $shipping_cost
			]);
		} else {
			echo json_encode([
				'success' => false
			]);
		}
	}



    function updateItemQty(){
        $update = 0;
        
        // Get cart item info
        $rowid = $this->input->get('rowid');
        $qty = $this->input->get('qty');
        
        // Update item in the cart
        if(!empty($rowid) && !empty($qty)){
            $data = array(
                'rowid' => $rowid,
                'qty'   => $qty
            );
            $update = $this->cart->update($data);
        }
        
        // Return response
        echo $update?'ok':'err';
    }
    
    public function checkout($action = '')
    {

        if ( ! is_login()) {
            $coupon = $this->input->post('coupon_code');
            $quantity = $this->input->post('quantity');

            $this->session->set_userdata('_temp_coupon', $coupon);
            $this->session->set_userdata('_temp_quantity', $quantity);

            verify_session('customer');
        }
        switch ($action)
        {

            default :
                $coupon = $this->input->post('coupon_code') ? $this->input->post('coupon_code') : $this->session->userdata('_temp_coupon');
                $quantity = $this->input->post('quantity') ? $this->input->post('quantity') : $this->session->userdata('_temp_quantity');

                if ($this->session->userdata('_temp_quantity') || $this->session->userdata('_temp_coupon'))
                {
                    $this->session->unset_userdata('_temp_coupon');
                    $this->session->unset_userdata('_temp_quantity');
                }

                $items = [];

                foreach ($quantity as $rowid => $qty)
                {
                    $items['rowid'] = $rowid;
                    $items['qty'] = $qty;
                }

                $this->cart->update($items);

                if ( empty($coupon)) 
                {
                    $discount = 0;
                    $disc = 'Tidak menggunkan kupon';
                }
                else
                {
                    if ($this->customer->is_coupon_exist($coupon))
                    {
                        if ($this->customer->is_coupon_active($coupon))
                        {
                            if ( $this->customer->is_coupon_expired($coupon))
                            {
                                $discount = 0;
                                $disc = 'Kupon kadaluarsa';
                            }
                            else
                            {
                                $coupon_id = $this->customer->get_coupon_id($coupon);
                                $this->session->set_userdata('coupon_id', $coupon_id);

                                $credit = $this->customer->get_coupon_credit($coupon);
                                $discount = $credit;
                                $disc = '<span class="badge badge-success">'. $coupon .'</span> Rp '. format_rupiah($credit);
                            }
                        }
                        else
                        {
                            $discount = 0;
                            $disc = 'Kupon sudah tidak aktif';
                        }
                    }
                    else
                    {
                        $discount = 0;
                        $disc = 'Kupon tidak terdaftar';
                    }
                }

                $items = [];

                foreach ($this->cart->contents() as $item)
                {
                    $items[$item['id']]['qty'] = $item['qty'];
                    $items[$item['id']]['price'] = $item['price'];
                }
                 
                $subtotal = $this->cart->total();
                $ongkir = (int) ($subtotal >= get_settings('min_shop_to_free_shipping_cost')) ? 0 : get_settings('shipping_cost');

                $params['product'] = $this->product->product_data($item['id']);
                $params['customer'] = $this->customer->data();
                $params['subtotal'] = $subtotal;
                $params['ongkir'] = ($ongkir > 0) ? 'Rp'. format_rupiah($ongkir) : 'Gratis';
                $params['total'] = $subtotal + $ongkir - $discount;
                $params['discount'] = $disc;

                
                $this->session->set_userdata('order_quantity', $items);
                $this->session->set_userdata('total_price', $params['total']);
                
                
                get_header('Checkout');
                get_template_part('shop/checkout', $params);
                get_footer();
            break;
            case 'order' :
                $quantity = $this->session->userdata('order_quantity');

                $user_id = get_current_user_id();
                $coupon_id = $this->session->userdata('coupon_id');
                $order_number = $this->_create_order_number($quantity, $user_id, $coupon_id);
                $order_date = date('Y-m-d H:i:s');
                $total_price = $this->session->userdata('total_price');
                $total_items = count($quantity);
                // $payment = $this->input->post('payment');
                $payment = 2;

                $name = $this->input->post('name');
                $phone_number = $this->input->post('phone_number');
                $address = $this->input->post('address');
                $note = $this->input->post('note');

                $delivery_data = array(
                    'customer' => array(
                        'name' => $name,
                        'phone_number' => $phone_number,
                        'address' => $address
                    ),
                    'note' => $note
                );

                $delivery_data = json_encode($delivery_data, true);

                $order = array(
                    'user_id' => $user_id,
                    'coupon_id' => $coupon_id,
                    'order_number' => $order_number,
                    'order_status' => 1,
                    'order_date' => $order_date,
                    'total_price' => $total_price,
                    'total_items' => $total_items,
                    'payment_method' => $payment,
                    'delivery_data' => $delivery_data
                );
                
                $get = $this->customer->get_email($user_id);
                // print_r();
                // die();
                $order = $this->product->create_order($order);


                $n = 0;
                foreach ($quantity as $id => $data)
                {
                    $items[$n]['order_id'] = $order;
                    $items[$n]['product_id'] = $id;
                    $items[$n]['order_qty'] = $data['qty'];
                    $items[$n]['order_price'] = $data['price'];

                    $n++;
                }

                $this->product->create_order_items($items);

                $this->cart->destroy();
                $this->session->unset_userdata('order_quantity');
                $this->session->unset_userdata('total_price');
                $this->session->unset_userdata('coupon_id');

                $nameproduct = $this->product->product_data($id);
                $seing = $nameproduct->name;

                // Midtrans Start
                // $vt = new Veritrans;

                $transaction_details = array(
                    'order_id' 			=> $order_number,
                    'gross_amount' 	=> $total_price
                );

                // Populate items
                $item1_details = array(
                        'id' 			=> $order_number,
                        'price' 		=> $total_price,
                        'quantity' 	    => $total_items,
                        'name' 			=> $seing
                    );
        
                $item_details = array ($item1_details);
                // Populate customer's billing address
                $billing_address = array(
                    'first_name' 	=> $name,
                    'last_name' 	=> $name,
                    'address' 		=> $address,
                    'city' 			=> "Jakarta",
                    'postal_code' 	=> "51161",
                    'phone' 		=> $phone_number,
                    'country_code'	=> 'IDN'
                    );
        
                // Populate customer's shipping address
                $shipping_address = array(
                    'first_name' 	=> $name,
                    'last_name' 	=> $name,
                    'address' 		=> $address,
                    'city' 			=> "Jakarta",
                    'postal_code'   => "51162",
                    'phone' 		=> $phone_number,
                    'country_code'  => 'IDN'
                    );
        
                // Populate customer's Info
                $customer_details = array(
                    'first_name' 			=> $name,
                    'last_name' 			=> $name,
                    'email' 					=> $get->email,
                    'phone' 					=> $phone_number,
                    'billing_address' => $billing_address,
                    'shipping_address'=> $shipping_address
                    );

                $enable_payments = array(
                    'cimb_clicks', 'mandiri_clickpay', 'echannel', 'alfamart'
                );
        
                // Data yang akan dikirim untuk request redirect_url.
                // Uncomment 'credit_card_3d_secure' => true jika transaksi ingin diproses dengan 3DSecure.
                $transaction_data = array(
                    'transaction_details' => $transaction_details,
                    'item_details' 			 => $item_details,
                    'customer_details' 	 => $customer_details
                );

                error_log(json_encode($transaction_data));
                $snapToken = $this->midtrans->getSnapToken($transaction_data);
                error_log($snapToken);
                echo $snapToken;

                // $this->session->set_flashdata('order_flash', 'Order berhasil ditambahkan');
            break;       
            case 'cod' :
                $quantity = $this->session->userdata('order_quantity');

                $user_id = get_current_user_id();
                $coupon_id = $this->session->userdata('coupon_id');
                $order_number = $this->_create_order_number($quantity, $user_id, $coupon_id);
                $order_date = date('Y-m-d H:i:s');
                $total_price = $this->session->userdata('total_price');
                $total_items = count($quantity);
				$payment = 1;
				

                $name = $this->input->post('name');
                $phone_number = $this->input->post('phone_number');
                $address = $this->input->post('address');
                $note = $this->input->post('note');

                $delivery_data = array(
                    'customer' => array(
                        'name' => $name,
                        'phone_number' => $phone_number,
                        'address' => $address
                    ),
                    'note' => $note
                );

                $delivery_data = json_encode($delivery_data, true);

                $order = array(
                    'user_id' => $user_id,
                    'coupon_id' => $coupon_id,
                    'order_number' => $order_number,
                    'order_status' => 1,
                    'order_date' => $order_date,
                    'total_price' => $total_price,
                    'total_items' => $total_items,
                    'payment_method' => $payment,
                    'delivery_data' => $delivery_data
                );

				
                
                $get = $this->customer->get_email($user_id);
                // print_r();
                // die();
                $order = $this->product->create_order($order);


                $n = 0;
                foreach ($quantity as $id => $data)
                {
                    $items[$n]['order_id'] = $order;
                    $items[$n]['product_id'] = $id;
                    $items[$n]['order_qty'] = $data['qty'];
                    $items[$n]['order_price'] = $data['price'];

                    $n++;
                }

                $this->product->create_order_items($items);

                $this->cart->destroy();
                $this->session->unset_userdata('order_quantity');
                $this->session->unset_userdata('total_price');
                $this->session->unset_userdata('coupon_id');

                $nameproduct = $this->product->product_data($id);
                $seing = $nameproduct->name;

                redirect('customer/orders');
            break;      
        }

    }


    public function finish()
    {
    	$result = json_decode($this->input->post('result_data'), true);
    	// echo 'RESULT <br><pre>';
    	// echo '</pre>' ;

		$data = [
			'order_id' => $result['order_id'],
			'gross_amount' => $result['gross_amount'],
			'payment_type' => $result['payment_type'],
			'transaction_time' => $result['transaction_time'],
			'bank' => $result['va_numbers'][0]['bank'],
			'va_number' => $result['va_numbers'][0]['va_number'],
			'pdf_url' => $result['pdf_url'],
			'status_code' => $result['status_code']

		];

		$save = $this->db->insert('transaksi_midtrans', $data);
        
        $order_id = $this->product->orderGet($result['order_id']);

		if($save) {
			$this->session->set_flashdata('order_flash', 'Order berhasil ditambahkan');
            redirect('customer/orders/view/'. $order_id->id);
		} else {
			echo "gagal";
		}
    }

	// refund
	public function refund_transaction($order_id) {
		$params = array(
			'refund_key' => uniqid("refund-"),
			'amount' => 10000,               
			'reason' => 'Item out of stock'  
		);
	
		try {
			$refund = $this->midtrans->refund($order_id, $params);
			print_r($status);
	
			if ($refund) {
				echo "Refund berhasil: ";
				var_dump($refund);
			}
		} catch (Exception $e) {
			echo "Refund gagal: " . $e->getMessage();
		}
	}
	


    public function cart_api()
    {
        $action = $this->input->get('action');

        switch ($action)
        {
            case 'add_item' :
                $id = $this->input->post('id');
                $qty = $this->input->post('qty');
                $sku = $this->input->post('sku');
                $name = $this->input->post('name');
                $price = $this->input->post('price');
                
                $item = array(
                    'id' => $id,
                    'qty' => $qty,
                    'price' => $price,
                    'name' => $name
                );
                $this->cart->insert($item);
                $total_item = count($this->cart->contents());

                $response = array('code' => 200, 'message' => 'Item dimasukkan dalam keranjang', 'total_item' => $total_item);
            break;
            case 'display_cart' :
                $carts = [];

                foreach ($this->cart->contents() as $items)
                {
                    $carts[$items['rowid']]['id'] = $items['id'];
                    $carts[$items['rowid']]['name'] = $items['name'];
                    $carts[$items['rowid']]['qty'] = $items['qty'];
                    $carts[$items['rowid']]['price'] = $items['price'];
                    $carts[$items['rowid']]['subtotal'] = $items['subtotal'];
                }

                $response = array('code' => 200, 'carts' => $carts);
            break;
            case 'cart_info' :
                $total_price = $this->cart->total();
                $total_item = count($this->cart->contents());

                $data['total_price']= $total_price;
                $data['total_item'] = $total_item;

                $response['data'] = $data;
            break;
            case 'remove_item' :
                $rowid = $this->input->post('rowid');

                $this->cart->remove($rowid);
                
                $total_price = $this->cart->total();
                $ongkir = (int) ($total_price >= get_settings('min_shop_to_free_shipping_cost')) ? 0 : get_settings('shipping_cost');
                $data['code'] = 204;
                $data['message'] = 'Item dihapus dari keranjang';
                $data['total']['subtotal'] = 'Rp '. format_rupiah($total_price);
                $data['total']['ongkir'] = ($ongkir > 0) ? 'Rp '. format_rupiah($ongkir) : 'Gratis';
                $data['total']['total'] = 'Rp '. format_rupiah($total_price + $ongkir);

                $response = $data;
            break;
        }

        $response = json_encode($response);
        $this->output->set_content_type('application/json')
            ->set_output($response);
    }

    public function _create_order_number($quantity, $user_id, $coupon_id)
    {
        $this->load->helper('string');

        $alpha = strtoupper(random_string('alpha', 3));
        $num = random_string('numeric', 3);
        $count_qty = count($quantity);


        $number = $alpha . date('j') . date('n') . date('y') . $count_qty . $user_id . $coupon_id . $num;
        //Random 3 letter . Date . Month . Year . Quantity . User ID . Coupon Used . Numeric

        return $number;
    }
}