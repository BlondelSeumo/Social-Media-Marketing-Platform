<?php

class Toyyibpay{

	public $toyyibpay_secret_key;
	public $toyyibpay_category_code;
	public $purpose;
	public $amount;
	public $phone;
	public $buyer_name;
	public $email;
	public $redirect_url;
	public $toyyibpay_mode;
	public $button_lang;
	public $toyyibpay_api_url;
	public $toyyibpay_bill_url;
	public $billcode;
	public $getBillTransactions;
	
	
	function __construct(){
		
		$this->CI =& get_instance();

		$query = $this->CI->db->get('payment_config');
		$config_data = $query->result_array();

		$this->toyyibpay_secret_key =isset($config_data[0]['toyyibpay_secret_key'])?$config_data[0]['toyyibpay_secret_key']:"";
		$this->toyyibpay_category_code =isset($config_data[0]['toyyibpay_category_code'])?$config_data[0]['toyyibpay_category_code']:"";
		$this->toyyibpay_mode =isset($config_data[0]['toyyibpay_mode'])?$config_data[0]['toyyibpay_mode']:"live";
		if($this->toyyibpay_mode=='sandbox') {
			$this->toyyibpay_api_url="https://dev.toyyibpay.com/";
			$this->toyyibpay_bill_url="https://dev.toyyibpay.com/index.php/api/createBill";
			$this->getBillTransactions = "https://dev.toyyibpay.com/index.php/api/runBill";
		}
		else{

			 $this->toyyibpay_api_url="https://toyyibpay.com/";
			 $this->toyyibpay_bill_url="https://toyyibpay.com/index.php/api/createBill";
			 $this->getBillTransactions = "https://toyyibpay.com/index.php/api/runBill";
		}
	}
	
	function set_button(){

		$button_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay with toyyibpay");
	
		$button = "
			<a target='_blank' href='".$this->redirect_url."' class='list-group-item list-group-item-action flex-column align-items-start'>
			<div class='d-flex w-100 align-items-center'>
			<small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/toyyibpay.png')."'></small>
			<h6 class='mb-1'>".$button_lang."</h6>
			</div>
			</a>";
		
		return $button;
	
	}


	public function get_billcode()
	{
		$post_data = array(
		    'userSecretKey' => $this->toyyibpay_secret_key,
		    'categoryCode' => $this->toyyibpay_category_code,
		    'billName' => $this->purpose,
		    'billDescription' => ' ',
		    'billPriceSetting' =>1,
		    'billPayorInfo' =>0,
		    'billAmount' =>$this->amount*100,
		    'billReturnUrl' =>$this->redirect_url,
		    'billCallbackUrl' =>' ',
		    'billExternalReferenceNo' => '',
		    'billTo' =>$this->buyer_name,
		    'billEmail' =>$this->email,
		    'billPhone' =>$this->phone,
		    'billPaymentChannel' =>' ',
		    'billDisplayMerchant' =>' ',
		  );  


		  $curl = curl_init();
		  curl_setopt($curl, CURLOPT_POST, 1);
		  curl_setopt($curl, CURLOPT_URL, $this->toyyibpay_bill_url);  
		  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);

		  $result = curl_exec($curl);
		  curl_close($curl);
		  $response = json_decode($result,true);
		  $this->billcode = $response[0]['BillCode'];

		  $checkout_url = $this->toyyibpay_api_url.$this->billcode;
		   header('Location:'.$checkout_url);


	}
	

	public function success_action()
	{
		
		$some_data = array(
			'userSecretKey' => $this->toyyibpay_secret_key ,
			'billCode' => $this->billcode,
			'billpaymentAmount' => $this->amount*100,
			'billpaymentPayorName' => $this->buyer_name,
			'billpaymentPayorPhone'=>$this->phone,
			'billpaymentPayorEmail'=>$this->email,
			'billBankID'=>''

		);  

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_URL, $this->getBillTransactions);  
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
	}	
		
	}

?>