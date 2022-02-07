<?php
require_once('Stripe/lib/Stripe.php');
class Stripe_class{

	public $secret_key="";
	public $publishable_key="";
	
	public $description="Package Renew";
	public $amount=0;
	public $action_url="";
	
	public $currency="brl";
	public $secondary_button=false;
	public $button_lang='';

	function __construct(){		
		$this->CI =& get_instance();
		$this->CI->load->database();
		
		/**** Get Stripe Setting informations ***/
			
		$q="select * from payment_config WHERE deleted='0'";
		$query=$this->CI->db->query($q);
		$results=$query->result_array();
		foreach($results as $info){	
			$this->secret_key=$info['stripe_secret_key'];
			$this->publishable_key=$info['stripe_publishable_key'];
			$this->currency=strtolower($info['currency']);
		}
	}
	
	function set_button(){
	
		$button_url=base_url()."assets/img/favicon.png";
		$base_url=base_url();
		$stripe_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay with Stripe");
		$hide_me = $this->secondary_button ? 'display:none;' : '';

		if(strtoupper($this->currency)=='JPY' || strtoupper($this->currency)=='VND')
			$amount=$this->amount;
		else
			$amount=$this->amount*100;
		
		$button="";
		
		$button.="<form action='{$this->action_url}' method='POST' style='".$hide_me."' id='stripePaymentForm01'>
			<script
		    src='https://checkout.stripe.com/checkout.js' class='stripe-button'
		    data-key='{$this->publishable_key}'
		    data-image='{$button_url}'
		    data-name='{$base_url}'
		    data-currency='{$this->currency}'
		    data-description='{$this->description}'
		    data-amount='{$amount}'
		    data-billing-address='true'>
		  </script>
		</form>";

		if($this->secondary_button)
		$button.="
		<a href='#' class='list-group-item list-group-item-action flex-column align-items-start' id='stripe_clone' onclick=\"document.querySelector('#stripePaymentForm01 .stripe-button-el').click();\">
		    <div class='d-flex w-100 align-items-center'>
		      <small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/stripe.png')."'></small>
		      <h6 class='mb-1'>".$stripe_lang."</h6>
		    </div>
		</a>";

		return $button;
		
	}
	
	
	
public function stripe_payment_action(){
		
		$response=array();
		
		$amount= $this->CI->session->userdata('stripe_payment_amount');

		if(strtoupper($this->currency)=='JPY' || strtoupper($this->currency)=='VND')
			$amount=$amount;
		else
			$amount=$amount*100;
		
	try {
	
		Stripe::setApiKey($this->secret_key);	
		$charge = Stripe_Charge::create(array(
	  	"amount" => $amount,
	  	"currency" => $this->currency,
	  	"card" => $_POST['stripeToken'],
	  	"description" => $this->description
	));
	
	$charge_array=$charge->__toArray(true);
	
	$email	= $_POST['stripeEmail'];
	
	$response['status']="Success";
	$response['email']=$email;
	$response['charge_info']=$charge_array;

	return $response;
	
	}
	
	catch(Stripe_CardError $e) {
		$response['status'] ="Error";
		$response['message'] ="Stripe_CardError"." : ".$e->getMessage();
		return $response;
	}
	
	 catch (Stripe_InvalidRequestError $e) {
		$response['status'] ="Error";
		$response['message'] ="Stripe_InvalidRequestError"." : ".$e->getMessage();
		return $response;
	
	} catch (Stripe_AuthenticationError $e) {
		$response['status'] ="Error";
		$response['message'] ="Stripe_AuthenticationError"." : ".$e->getMessage();
		return $response;
	
	} catch (Stripe_ApiConnectionError $e) {
	 	$response['status'] ="Error";
		$response['message'] ="Stripe_ApiConnectionError"." : ".$e->getMessage();
		return $response;
	} catch (Stripe_Error $e) {
		$response['status'] ="Error";
		$response['message'] ="Stripe_Error"." : ".$e->getMessage();
		return $response;
	  
	} catch (Exception $e) {
		$response['status'] ="Error";
		$response['message'] ="Stripe_Error"." : ".$e->getMessage();
		return $response;
	}
		
  }
}

?>