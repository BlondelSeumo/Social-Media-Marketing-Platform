<?php
class Senangpay{

	public $merchant_id;
	public $secretkey;
	public $detail;
	public $amount;
	public $order_id;
	public $name;
	public $email;
	public $phone;
	public $senangpay_mode;
	public $secondary_button=false;
	public $button_lang;
	public $hashed_string;
	
	
	function __construct(){
		
		$this->CI =& get_instance();
	}
	
	function set_button(){

		$button_lang = !empty($this->button_lang)?$this->button_lang:$this->CI->lang->line("Pay With Senangpay");
		$hide_me = $this->secondary_button ? 'display:none;' : '';

		if($this->senangpay_mode == 'live')
			$action_url = "https://app.senangpay.my/payment/".$this->merchant_id;
		else
			$action_url = "https://sandbox.senangpay.my/payment/".$this->merchant_id;
	
		$button="";
		
		$button.= '<form id="senangpay_form" method="POST" action="'.$action_url.'" style="padding: 0; margin: 0;'.$hide_me.'">
            <input type="hidden" name="detail" value="'.$this->detail.'">
            <input type="hidden" name="amount" value="'.$this->amount.'">
            <input type="hidden" name="order_id" value="'.$this->order_id.'">
            <input type="hidden" name="name" value="'.$this->name.'">
            <input type="hidden" name="email" value="'.$this->email.'">
            <input type="hidden" name="phone" value="'.$this->phone.'">
            <input type="hidden" name="hash" value="'.$this->hashed_string.'">
        </form>';
				

		if($this->secondary_button)
			$button .= "
				<a href='#' class='list-group-item list-group-item-action flex-column align-items-start' onclick=\"document.getElementById('senangpay_form').submit();\">
				<div class='d-flex w-100 align-items-center'>
				<small class='text-muted'><img class='rounded' width='60' height='60' src='".base_url('assets/img/payment/senangPay.png')."'></small>
				<h6 class='mb-1'>".$button_lang."</h6>
				</div>
				</a>";
		
		return $button;
	
	}
	
	
}

?>