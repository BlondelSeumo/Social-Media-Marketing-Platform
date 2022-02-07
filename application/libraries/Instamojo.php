<?php
class Instamojo{

	public $instamojo_api_key;
	public $instamojo_auth_token;
	public $purpose;
	public $amount;
	public $phone;
	public $buyer_name;
	public $email;
	public $redirect_url;
	public $fail_url;
	public $instamojo_mode;
	public $button_lang;
	public $instamojo_api_url;
	public $payment_id;
	public $payment_request_id;
	
	
	function __construct(){
		
		$this->CI =& get_instance();

		$query = $this->CI->db->get('payment_config');
		$config_data = $query->result_array();

		$this->instamojo_api_key =isset($config_data[0]['instamojo_api_key'])?$config_data[0]['instamojo_api_key']:"";
		$this->instamojo_auth_token =isset($config_data[0]['instamojo_auth_token'])?$config_data[0]['instamojo_auth_token']:"";
		$this->instamojo_mode =isset($config_data[0]['instamojo_mode'])?$config_data[0]['instamojo_mode']:"live";
		
		if($this->instamojo_mode=='sandbox') $this->instamojo_api_url="https://test.instamojo.com/api/1.1/payment-requests/";
		else $this->instamojo_api_url="https://www.instamojo.com/api/1.1/payment-requests/";
	}
	
	function set_button(){

		$button_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay With Instamojo");
	
		$button = "
			<a href='".$this->redirect_url."' class='list-group-item list-group-item-action flex-column align-items-start'>
			<div class='d-flex w-100 align-items-center'>
			<small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/instamojo.png')."'></small>
			<h6 class='mb-1'>".$button_lang."</h6>
			</div>
			</a>";
		
		return $button;
	
	}


	public function get_long_url()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->instamojo_api_url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		    array("X-Api-Key:".$this->instamojo_api_key,
		      "X-Auth-Token:".$this->instamojo_auth_token));
		$payload = Array(
		    'purpose' => $this->purpose,
		    'amount' => $this->amount,
		    'phone' => $this->phone,
		    'buyer_name' => $this->buyer_name,
		    'email' => $this->email,
		    'redirect_url' => $this->redirect_url,
		    'send_email' => false,
		    'webhook' => '',
		    'send_sms' => false,
		    'allow_repeated_payments' => false
		);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
		$response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode( $response,true );
		
		if(isset($response['success']) && $response['success'] == 1)
		{
			$longurl = $response['payment_request']['longurl'];
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
		$url = $this->instamojo_api_url.$this->payment_request_id.'/'.$this->payment_id;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		array("X-Api-Key:".$this->instamojo_api_key,
		      "X-Auth-Token:".$this->instamojo_auth_token));
		$response = curl_exec($ch);
		curl_close($ch); 
		$response = json_decode( $response, true );
		return $response;
	}



	
	
}

?>