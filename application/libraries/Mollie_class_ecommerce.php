<?php
class Mollie_class_ecommerce{

	// Documentation: https://docs.mollie.com/reference/v2/payments-api/create-payment

	public $api_key;
	public $description;
	public $amount;
	public $action_url;	
	public $currency;
	public $img_url;
	public $title;
	public $customer_name;
	public $customer_email;
	public $customer_phone; 
	public $webhook_url=''; 
	public $ec_order_id;
	public $mollie_lang;
	public $secondary_button=false;

	function __construct()
	{		
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->model('basic');
	}
	
	function set_button(){

		$receipt="".time()."";
		$mollie_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Mollie Payment");
		
		$url="https://api.mollie.com/v2/payments";
		$headers=array("content-type:application/json","Authorization: Bearer ".$this->api_key);
		$amount=number_format((float)$this->amount, 2, '.', '');
		$redirect_url=trim($this->action_url,"/")."/".$this->ec_order_id;

		$post_data=array("amount"=>array("currency"=>$this->currency,"value"=>$amount),"description"=>$this->description,"redirectUrl"=>$redirect_url,"metadata"=>array("order_id"=>$this->ec_order_id),"webhookUrl"=>"$this->webhook_url");

		$post_data=json_encode($post_data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$result = curl_exec($ch);

		$result=json_decode($result,true);
		$order_id= isset($result['id']) ? $result['id']:"";

		$order_id_session="order_id_".$this->ec_order_id;

		$this->CI->session->set_userdata($order_id_session,$order_id);	

		if($order_id==""){
			return $error= $result['status']." : ".$result['detail'];
		}

		$checkout_link=$result['_links']['checkout']['href'];

		if($this->secondary_button)
		{
			$button="
			<a href='{$checkout_link}' class='list-group-item list-group-item-action flex-column align-items-start' id='mollie-payment-button'>
			    <div class='d-flex w-100 align-items-center'>
			      <small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/mollie.png')."'></small>
			      <h6 class='mb-1'>".$mollie_lang."</h6>
			    </div>
			</a>";
		}
		else $button="<a id='mollie-payment-button' href='{$checkout_link}'>{$mollie_lang}</a>";

		return $button;
	}
	
	public function mollie_payment_action()
	{		

		$order_id_session	="order_id_".$this->ec_order_id;
		$transaction_id		= $this->CI->session->userdata($order_id_session);	

		$response=array();
		$url="https://api.mollie.com/v2/payments/{$transaction_id}";
		$headers=array("content-type:application/json","Authorization: Bearer ".$this->api_key);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		$charge_info=json_decode($result,true);

		$err = curl_error($ch);
  		curl_close($ch);

		  if ($err) {

			$response['status'] ="Error";
			$response['message'] ="cURL Error #:".$err;
			return $response;

		  } else {

			if($charge_info['status']!="paid"){

			  	$response['status'] ="Error";

			  	if(isset($charge_info['detail']))
					$response['message'] ="Transaction Verification Error :".$charge_info['detail'];
				else
					$response['message'] ="Transaction is : ".$charge_info['status'];
				return $response;
  			}

	  	$response['status']="Success";
		$response['charge_info']=$charge_info;

		return $response;
  		}

	}

	function set_button_ecommerce(){

		$receipt="".time()."";
		$mollie_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Mollie Payment");
		
		$url="https://api.mollie.com/v2/payments";
		$headers=array("content-type:application/json","Authorization: Bearer ".$this->api_key);
		$amount=number_format((float)$this->amount, 2, '.', '');
		$redirect_url=trim($this->action_url,"/")."/".$this->ec_order_id;

		$post_data=array("amount"=>array("currency"=>$this->currency,"value"=>$amount),"description"=>$this->description,"redirectUrl"=>$redirect_url,"metadata"=>array("order_id"=>$this->ec_order_id),"webhookUrl"=>"$this->webhook_url");

		$post_data=json_encode($post_data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$result = curl_exec($ch);

		$result=json_decode($result,true);
		$order_id= isset($result['id']) ? $result['id']:"";

		// $order_id_session="order_id_".$this->ec_order_id;
		// $this->CI->session->set_userdata($order_id_session,$order_id);

		$this->CI->basic->update_data("ecommerce_cart",array("id"=>$this->ec_order_id),array("payment_temp_session"=>$order_id));

		if($order_id==""){
			return $error= $result['status']." : ".$result['detail'];
		}

		$checkout_link=$result['_links']['checkout']['href'];

		if($this->secondary_button)
		{
			$button="
			<a href='{$checkout_link}' class='list-group-item list-group-item-action flex-column align-items-start' id='mollie-payment-button'>
			    <div class='d-flex w-100 align-items-center'>
			      <small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/mollie.png')."'></small>
			      <h6 class='mb-1'>".$mollie_lang."</h6>
			    </div>
			</a>";
		}
		else $button="<a id='mollie-payment-button' href='{$checkout_link}'>{$mollie_lang}</a>";

		return $button;
	}
	
	public function mollie_payment_action_ecommerce($cart_id='')
	{		

		$session_data = $this->CI->basic->get_data("ecommerce_cart",array("where"=>array("id"=>$cart_id)));
		$transaction_id = isset($session_data[0]['payment_temp_session'])?$session_data[0]['payment_temp_session']:'';

		// $order_id_session	="order_id_".$this->ec_order_id;
		// $transaction_id		= $this->CI->session->userdata($order_id_session);	

		$response=array();
		$url="https://api.mollie.com/v2/payments/{$transaction_id}";
		$headers=array("content-type:application/json","Authorization: Bearer ".$this->api_key);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		$charge_info=json_decode($result,true);

		$err = curl_error($ch);
  		curl_close($ch);

		  if ($err) {

			$response['status'] ="Error";
			$response['message'] ="cURL Error #:".$err;
			return $response;

		  } else {

			if($charge_info['status']!="paid"){

			  	$response['status'] ="Error";

			  	if(isset($charge_info['detail']))
					$response['message'] ="Transaction Verification Error :".$charge_info['detail'];
				else
					$response['message'] ="Transaction is : ".$charge_info['status'];
				return $response;
  			}

	  	$response['status']="Success";
		$response['charge_info']=$charge_info;

		return $response;
  		}

	}
}

?>