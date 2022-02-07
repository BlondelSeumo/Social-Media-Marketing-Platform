<?php
require_once 'mercadopago/autoload.php';
class Mercadopago{

	public $public_key;
	public $accesstoken;
	public $transaction_amount;
	public $description;
	public $redirect_url;
	public $token;
	public $installments;
	public $payment_method_id;
	public $issuer_id;
	public $payer_email;
	public $marcadopago_url;

	public $button_lang='';
	public $secondary_button=false;


	function __construct()
	{		
		$this->CI =& get_instance();
	}

	function set_button()
	{
		$hide_me = $this->secondary_button ? 'display:none;' : '';
		$button = '<form action="'.$this->redirect_url.'" method="POST" id="mercadopagoPaymentForm01" style="'.$hide_me.'">
				 		<script
				 		src="'.$this->marcadopago_url.'/integrations/v1/web-tokenize-checkout.js"
				 		data-public-key="'.$this->public_key.'"
				 		data-transaction-amount="'.$this->transaction_amount.'" data-button-label="'.$this->button_lang.'">
				 		</script>
				 	</form>';
		if($this->secondary_button)
		$button.="
		<a href='#' class='list-group-item list-group-item-action flex-column align-items-start' id='mercadopago_clone' onclick=\"document.querySelector('.mercadopago-button').click();\">
		    <div class='d-flex w-100 align-items-center'>
		      <small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/mercadopago.png')."'></small>
		      <h6 class='mb-1'>".$this->button_lang."</h6>
		    </div>
		</a>";
		return $button;

	}

	function payment_action()
	{
		MercadoPago\SDK::setAccessToken($this->accesstoken);
		$payment = new MercadoPago\Payment();
		$payment->transaction_amount = $this->transaction_amount;
		$payment->token = $this->token;
		$payment->description = $this->description;
		$payment->installments = $this->installments;
		$payment->payment_method_id = $this->payment_method_id;
		$payment->issuer_id = $this->issuer_id;
		$payment->payer = array(
			"email" => $this->payer_email
		);
		// echo "<pre>";print_r($payment);
		// Armazena e envia o pagamento
		$save = $payment->save();

		// Imprime o status do pagamento
		$response['status'] = $payment->status;

		return $response;
	}

}