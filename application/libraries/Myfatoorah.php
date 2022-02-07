<?php

class Myfatoorah{



	public $myfatoorah_api_key;

	public $purpose;

	public $amount;

	public $phone;

	public $buyer_name;

	public $email;

	public $redirect_url;

	public $callbackurl;

	public $errorUrl;

	public $fail_url;

	public $myfatoorah_mode;

	public $button_lang;

	public $myfatoorah_api_url;

	public $payment_id;

	public $api_main_url_success_url;

	public $paymentId_response;

	public $myfatoorah_currency;

	

	

	function __construct(){

		

		$this->CI =& get_instance();



		$query = $this->CI->db->get('payment_config');

		$config_data = $query->result_array();



		$this->myfatoorah_api_key =isset($config_data[0]['myfatoorah_api_key'])?$config_data[0]['myfatoorah_api_key']:"";

		$this->myfatoorah_mode =isset($config_data[0]['myfatoorah_mode'])?$config_data[0]['myfatoorah_mode']:"live";

		$this->myfatoorah_currency = isset($config_data[0]['currency'])?$config_data[0]['currency']:"KWD";

		

		if($this->myfatoorah_mode=='sandbox'){

		 $this->myfatoorah_api_url="https://apitest.myfatoorah.com/v2/SendPayment";

		 $this->api_main_url_success_url = "https://apitest.myfatoorah.com/v2/getPaymentStatus";

		}

		else{

			$this->myfatoorah_api_url="https://api.myfatoorah.com/v2/SendPayment";

			$this->api_main_url_success_url = "https://api.myfatoorah.com/v2/getPaymentStatus";

		} 

	}

	

	function set_button(){



		$button_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay With Myfatoorah");

	

		$button = "

			<a href='".$this->redirect_url."' class='list-group-item list-group-item-action flex-column align-items-start'>

			<div class='d-flex w-100 align-items-center'>

			<small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/myfatoorah.png')."'></small>

			<h6 class='mb-1'>".$button_lang."</h6>

			</div>

			</a>";

		

		return $button;

	

	}





	public function get_long_url()

	{



		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->myfatoorah_api_url);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Authorization: Bearer $this->myfatoorah_api_key","Content-Type: application/json"));

		$payload = Array(

			'NotificationOption' => 'ALL',

			'DisplayCurrencyIso' => $this->myfatoorah_currency,

			'InvoiceValue' => $this->amount,

			'CustomerMobile' => $this->phone,

			'CustomerName' => $this->buyer_name,

			'CustomerEmail' => $this->email,

			'CallBackUrl' => $this->callbackurl,

			'ErrorUrl' => $this->errorUrl,

			'Language' => 'en',

			'InvoiceItems' => 

			array (

				0 => 

				array (

					'ItemName' =>$this->purpose,

					'Quantity' => 1,

					'UnitPrice' => $this->amount,

				),

			),

		);

		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

		$response = curl_exec($ch);

		curl_close($ch);



		$response = json_decode( $response,true );	

		if(isset($response['IsSuccess']) && $response['IsSuccess'] == 1)

		{

			$longurl = $response['Data']['InvoiceURL'];

			header('Location:'.$longurl);

		}

		else

		{

	    	if($this->fail_url=='')  $redirect_url=base_url()."payment/transaction_log?action=cancel";

	    	else $redirect_url=$this->fail_url;

			redirect($redirect_url, 'refresh');

		}

	}





	public function success_action()

	{

		$url = $this->api_main_url_success_url;

		$data   = array(

			'KeyType' => 'paymentId',

		    'Key'     => $this->payment_id //the callback paymentID

		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Authorization: Bearer $this->myfatoorah_api_key","Content-Type: application/json"));

		$response = curl_exec($ch);

		curl_close($ch); 

		$response = json_decode( $response, true );
		// echo '<pre>';
		// print_r($response);
		// exit();
		return $response;

	}
	

}



?>