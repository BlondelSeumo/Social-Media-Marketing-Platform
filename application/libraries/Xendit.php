<?php
class Xendit{

	public $xendit_secret_api_key;
	public $external_id;
	public $payer_email;
	public $description;
	public $buyer_name;
	public $amount;
	public $xendit_success_redirect_url;
	public $xendit_redirect_url;
	public $xendit_failure_redirect_url;
	public $button_lang;
	public $currency;
	public $xendit_api_url = 'https://api.xendit.co/v2/invoices';
	public $payment_id;
	public $invoice_id;
	public $final_url;

	
	
	function __construct(){
		
		$this->CI =& get_instance();

		$query = $this->CI->db->get('payment_config');
		$config_data = $query->result_array();

		$this->xendit_secret_api_key =isset($config_data[0]['xendit_secret_api_key'])?$config_data[0]['xendit_secret_api_key']:"";
		
	}
	
	function set_button(){

		$button_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay With Xendit");
	
		$button = "
			<a href='".$this->xendit_redirect_url."' class='list-group-item list-group-item-action flex-column align-items-start'>
			<div class='d-flex w-100 align-items-center'>
			<small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/xendit.png')."'></small>
			<h6 class='mb-1'>".$button_lang."</h6>
			</div>
			</a>";
		
		return $button;
	
	}


	function get_long_url()
	{
		$curl = curl_init();
	    /** 

	    1. Encode the Secret Api key  above into Base64 format
	    2. Make sure to include ( : ) at the end of the secret Api key
	  
	    ***/
	    $xendit_secret_api_key = $this->xendit_secret_api_key.':';
	    $xendit_secret_api_key = base64_encode($xendit_secret_api_key);

	    $params = [ 
	    	'external_id' => $this->external_id,
	    	'payer_email' => $this->payer_email,
	    	'description' =>  $this->description,
	    	'amount' => $this->amount,
	    	'success_redirect_url' => $this->xendit_success_redirect_url,
	    	'failure_redirect_url' => $this->xendit_failure_redirect_url,
	    	'currency' => $this->currency,

	    ];
	    $params = json_encode($params);
	    curl_setopt_array($curl, array(
	    	CURLOPT_URL => $this->xendit_api_url,
	    	CURLOPT_RETURNTRANSFER => true,
	    	CURLOPT_ENCODING => '',
	    	CURLOPT_MAXREDIRS => 10,
	    	CURLOPT_TIMEOUT => 0,
	    	CURLOPT_FOLLOWLOCATION => true,
	    	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    	CURLOPT_CUSTOMREQUEST => 'POST',
	    	CURLOPT_POSTFIELDS =>$params,
	    	CURLOPT_HTTPHEADER => array(
	    		'Content-Type: application/json',
	    		'Authorization: Basic '.$xendit_secret_api_key,
	    		'Cookie: visid_incap_2182539=gbPSM59jRHebZE1evmfNVGpE7F8AAAAAQUIPAAAAAADBsA1NmcP5npE9eqj7NJWx; nlbi_2182539=NTHXJ+a+xHZW3ckajjCKbQAAAABOFjauz2/OfLCFxCOPeeDC; incap_ses_714_2182539=/502a4SHST+V5O7NaqPoCb6k8l8AAAAAmvYHuz6v8DN7E0vNYqcg1A=='
	    	),
	    ));

	    $response = curl_exec($curl);

	    curl_close($curl);
	    $response = json_decode($response,true);
	    if(isset($response['message']))
	    {
	    	$this->CI->session->set_flashdata('xendit_currency_error',$response['message'] );
	    	if($this->final_url=='')  $redirect_url=base_url()."payment/transaction_log";
	    	else $redirect_url=$this->final_url."&action=cancel";
	    	redirect($redirect_url, 'refresh');
	    }
	    if(isset($response['status']) && $response['status'] == 'PENDING')
	    {
	    	$this->invoice_id = $response['id'];
	        $invoice_url = $response['invoice_url'];
	    	header('Location:'.$invoice_url);
	    }
	    else
	    {
	    	if($this->final_url=='')  $redirect_url=base_url()."payment/transaction_log?action=cancel";
	    	else $redirect_url=$this->final_url."&action=cancel";
	    	redirect($redirect_url, 'refresh');
	    }
	}


	public function success_action()
	{

		$invoice_id = $this->invoice_id;
		$xendit_secret_api_key = $this->xendit_secret_api_key.':';
		$xendit_secret_api_key = base64_encode($xendit_secret_api_key);
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->xendit_api_url.$invoice_id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic '.$xendit_secret_api_key,
				'Cookie: visid_incap_2182539=gbPSM59jRHebZE1evmfNVGpE7F8AAAAAQUIPAAAAAADBsA1NmcP5npE9eqj7NJWx; nlbi_2182539=xryvT4w/TFdCCRzFjjCKbQAAAABZA7IBlYCl714TNa94Bs2O; incap_ses_714_2182539=XTxPeZx0lnE4HNbNaqPoCT6o8V8AAAAAZEgo713UH5uxKhUWP1x+ug=='
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$response = json_decode( $response, true );
		return $response;
		

	}



	
	
}

?>