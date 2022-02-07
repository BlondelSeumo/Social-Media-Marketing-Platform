<?php
class Paypal_class_ecommerce{

	public $mode;
	public $success_url;
	public $cancel_url;
	public $notify_url;
	public $business_email;
	public $product_quantity=1;
	public $product_name;
	public $product_number;
	public $amount;
	public $shipping_amount=0;
	public $currency="USD";
	public $ipn_response;
	public $cart_id;
	public $subscriber_id;
	public $button_lang='';
	public $secondary_button=false;
	
	function __construct()
	{		
		$this->CI =& get_instance();
		
	}
	
	function set_button(){
	
		$button="";
		if($this->mode=='sandbox') $paypal_url="https://www.sandbox.paypal.com/cgi-bin/webscr";
		else $paypal_url="https://www.paypal.com/cgi-bin/webscr";

		$paypal_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay with PayPal");
		$hide_me = $this->secondary_button ? 'display:none;' : '';
		
		$button.= "<form action='{$paypal_url}' method='post' style='padding: 0; margin: 0;".$hide_me."' id='paypalPaymentForm01'>";
			$button.= "<input type='hidden' name='cmd' value='_xclick' />";
			$button.= "<input type='hidden' name='business' value='{$this->business_email}' />";
			$button.= "<input type='hidden' name='quantity' value='{$this->product_quantity}' />";
			$button.= "<input type='hidden' name='item_name' value='{$this->product_name}' />";
			$button.= "<input type='hidden' name='item_number' value='{$this->product_number}' />";
			$button.= "<input type='hidden' name='amount' value='{$this->amount}' />";
			$button.= "<input type='hidden' name='shipping' value='{$this->shipping_amount}' />";
			$button.= "<input type='hidden' name='no_note' value='1' />";
			$button.= "<input type='hidden' name='notify_url' value='{$this->notify_url}'>";
			$button.= "<input type='hidden' name='currency_code' value='{$this->currency}' />";
			$button.= "<input type='hidden' name='return' value='{$this->success_url}'>";
			$button.= "<input type='hidden' name='cancel_return' value='{$this->cancel_url}'>";
			$button.= "<input type='hidden' name='custom' value='{$this->cart_id}_{$this->subscriber_id}'>";
			$button.= "<button type='submit' class='btn btn-info btn-lg'>".$paypal_lang."</button>";
		$button.= "</form>";

		if($this->secondary_button)
		$button.="
		<a href='#' class='list-group-item list-group-item-action flex-column align-items-start' id='paypal_clone' onclick=\"document.getElementById('paypalPaymentForm01').submit();\">
		    <div class='d-flex w-100 align-items-center'>
		      <small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/paypal.png')."'></small>
		      <h6 class='mb-1'>".$paypal_lang."</h6>
		    </div>
		</a>";	
		
		return $button;
	
	}
	
	/*
	This run_ipn() function will return the verified status that is payment is VERIFIED or NOTVERIFIED. And some correspoding data of the payment.  
 	$payment_info=$paypal_ipn->run_ipn();
	$verify_status=$payment_info['verify_status'];
	$first_name=$payment_info['data']['first_name'];
	$last_name=$payment_info['data']['last_name'];
	$buyer_email=$payment_info['data']['payer_email'];
	$receiver_email=$payment_info['data']['receiver_id'];
	$country=$payment_info['data']['address_country'];
	$payment_date=$payment_info['data']['payment_date'];
	$transaction_id=$payment_info['data']['txn_id'];
	$payment_type=$payment_info['data']['payment_type'];			
	*/	
	function run_ipn($insert=0)
	{
		if($this->mode=='sandbox') $paypal_url="https://www.sandbox.paypal.com/cgi-bin/webscr";
		else $paypal_url="https://www.paypal.com/cgi-bin/webscr";

		$req = 'cmd=' . urlencode('_notify-validate');
		 foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		 }		
		 $ch = curl_init();
		 $headers = array("Content-type: application/json");
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		 curl_setopt($ch, CURLOPT_POST, 1);  
		 curl_setopt($ch, CURLOPT_POSTFIELDS,$req);
	     curl_setopt($ch, CURLOPT_URL, $paypal_url);
		 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	     curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
	     curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
		 $st=curl_exec($ch);  
		 curl_close($ch); 	
		 $response['verify_status']=$st;
		 $response['data']=$_POST;
		 $this->ipn_response=$response;		 			
		 return $response;	 
	}
}

?>