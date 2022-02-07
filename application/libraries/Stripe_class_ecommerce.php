<?php
require_once('Stripe/lib/Stripe.php');
class Stripe_class_ecommerce{

	public $secret_key;
	public $publishable_key;	
	public $description;
	public $amount;
	public $action_url;	
	public $currency;
	public $img_url;
	public $title;
	public $button_lang='';
	public $stripe_billing_address='0';
	public $secondary_button=false;

	function __construct()
	{		
		$this->CI =& get_instance();
	}
	
	function set_button(){
	
		// $base_url=base_url();
		if(strtoupper($this->currency)=='JPY' || strtoupper($this->currency)=='VND') $amount=$this->amount;
		else $amount=$this->amount*100;
		
		$button="";
		$stripe_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay with Stripe");
		$billing_address="";
		if($this->stripe_billing_address=='1') $billing_address = "data-billing-address='true'";
		$hide_me = $this->secondary_button ? 'display:none;' : '';
		
		$button.="<form action='{$this->action_url}' method='POST' style='".$hide_me."' id='stripePaymentForm01'>
			<script
		    src='https://checkout.stripe.com/checkout.js' class='stripe-button'
		    data-key='{$this->publishable_key}'
		    data-image='{$this->img_url}'
		    data-name='{$this->title}'
		    data-currency='{$this->currency}'
		    data-description='{$this->description}'
		    data-amount='{$amount}'
		    data-label='{$stripe_lang}'
		    {$billing_address}>
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
	
	
	
public function stripe_payment_action($amount='',$currency='',$description='')
{		
	$response=array();		
	// $amount= $this->CI->session->userdata('ecommerce_stripe_payment_amount');	
	// $currency= $this->CI->session->userdata('ecommerce_stripe_payment_currency');
	// $description= $this->CI->session->userdata('ecommerce_stripe_payment_description');

	$description = urldecode($description);

	if(strtoupper($currency)=='JPY' || strtoupper($currency)=='VND')$amount=$amount;
	else $amount=$amount*100;
		
	try
	{
	
		Stripe::setApiKey($this->secret_key);	
		$charge = Stripe_Charge::create(array(
		  	"amount" => $amount,
		  	"currency" => $currency,
		  	"card" => $_POST['stripeToken'],
		  	"description" => $description
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