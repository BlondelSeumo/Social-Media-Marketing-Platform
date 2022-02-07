<?php require_once("Home.php"); // including home controller

class Stripe_action extends Home
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Stripe_class');
		$this->load->model('basic');
		set_time_limit(0);
	}
	
	public function index()
	{

		$response= $this->stripe_class->stripe_payment_action();


		if($response['status']=='Error'){
			echo $response['message'];
			exit();
		}
		
		$currency = isset($response['charge_info']['currency'])?$response['charge_info']['currency']:"";
		$currency=strtoupper($currency);

		
		$receiver_email=$response['email'];

		if($currency=='JPY' || $currency=='VND')
			$payment_amount=$response['charge_info']['amount'];
		else
			$payment_amount=$response['charge_info']['amount']/100;


		$transaction_id=$response['charge_info']['balance_transaction'];
		$payment_date=date("Y-m-d H:i:s",$response['charge_info']['created']) ;
		$country=isset($response['charge_info']['source']['country'])?$response['charge_info']['source']['country']:"";

		

		
		$stripe_card_source=isset($response['charge_info']['source'])?$response['charge_info']['source']:"";
		$stripe_card_source=json_encode($stripe_card_source);
		
		
		
		// $user_id=$this->session->userdata('user_id');
		// $package_id=$this->session->userdata('stripe_payment_package_id');		
		$user_id = $this->uri->segment(3);
		$package_id = $this->uri->segment(4);
		$user_id=$user_id;
		$package_id=$package_id;
		
		$simple_where['where'] = array('user_id'=>$user_id);
		$select = array('cycle_start_date','cycle_expired_date');
		
		$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');
		
		$prev_cycle_expired_date="";


		$config_data=array();
		$price=0;
		$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
		if(is_array($package_data) && array_key_exists(0, $package_data))
			$price=$package_data[0]["price"];
		$validity=$package_data[0]["validity"];

		$validity_str='+'.$validity.' day';
		
		foreach($prev_payment_info as $info){
			$prev_cycle_expired_date=$info['cycle_expired_date'];
		}
		
		if($prev_cycle_expired_date==""){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
			$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		
		/** insert the transaction into database ***/
		
		$insert_data=array(
			"verify_status" 	=>"",
			"first_name"		=>"",
			"last_name"			=>"",
			"paypal_email"		=>"STRIPE",
			"receiver_email" 	=>$receiver_email,
			"country"			=>$country,
			"payment_date" 		=>$payment_date,
			"payment_type"		=>"STRIPE",
			"transaction_id"	=>$transaction_id,
			"user_id"           =>$user_id,
			"package_id"		=>$package_id,
			"cycle_start_date"	=>$cycle_start_date,
			"cycle_expired_date"=>$cycle_expired_date,
			"paid_amount"	    =>$payment_amount,
			"stripe_card_source"=>$stripe_card_source
		);


		$this->basic->insert_data('transaction_history', $insert_data);
		$this->session->set_userdata("payment_success",1);
		
		/** Update user table **/
		$table='users';
		$where=array('id'=>$user_id);
		$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
		$this->basic->update_data($table,$where,$data);


		$product_short_name = $this->config->item('product_short_name');
		$from = $this->config->item('institute_email');
		$mask = $this->config->item('product_name');
		$subject = "Payment Confirmation";
		$where = array();
		$where['where'] = array('id'=>$user_id);
		$user_email = $this->basic->get_data('users',$where,$select='');

		$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

		if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

			$to = $user_email[0]['email'];
			$url = base_url();
			$subject = $payment_confirmation_email_template[0]['subject'];
			$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
            //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $user_email[0]['email'];
			$subject = "Payment Confirmation";
			$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
            //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		}

        // new payment made email
		$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

		if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

			$to = $from;
			$subject = $paypal_new_payment_made_email_template[0]['subject'];
			$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
            //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $from;
			$subject = "New Payment Made";
			$message = "New payment has been made by {$user_email[0]['name']}";
            //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
		}
		
		$redirect_url=base_url()."payment/transaction_log?action=success";

		// affiliate Section
		if($this->addon_exist('affiliate_system')) {
			$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
			$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
			if($affiliate_id != 0) {
				$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
			}
		}

		if($this->config->item("auto_relogin_after_purchase") == '1') {

			$this->session->unset_userdata('user_type');
			$this->session->unset_userdata('logged_in');
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('download_id');
			$this->session->unset_userdata('user_login_email');
			$this->session->unset_userdata('expiry_date');
			$this->session->unset_userdata('brand_logo');

			$this->subscriber_login($user_id);
		}

		redirect($redirect_url, 'refresh');

	}

	protected function subscriber_login($user_id='') {

		if($user_id == '' || $user_id == 0) {
			redirect('home/login', 'location');
		}

		$info = $this->basic->get_data("users",['where'=>['id'=>$user_id,'status'=>'1']]);

		$username = $info[0]['name'];
		$user_type = $info[0]['user_type'];
		$logo = $info[0]['brand_logo'];

		$is_mobile = '0';
		if(is_mobile()) $is_mobile = '1';
		$this->session->set_userdata("is_mobile",$is_mobile);

		$this->session->set_userdata('user_type', $user_type); 
		$this->session->set_userdata('logged_in', 1);
		$this->session->set_userdata('username', $username);
		$this->session->set_userdata('user_id', $user_id);
		$this->session->set_userdata('download_id', time());
		$this->session->set_userdata('user_login_email', $info[0]['email']);
		$this->session->set_userdata('expiry_date',$info[0]['expired_date']);
		$this->session->set_userdata('brand_logo',$logo);

        $this->set_google_config_session($user_id);

        $this->set_facebook_config_session($user_id);

        $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
        $package_info_session=array();
        if(array_key_exists(0, $package_info))
        $package_info_session=$package_info[0];
        $this->session->set_userdata('package_info', $package_info_session);
        $this->session->set_userdata('current_package_id',0);

		return true;
	}

	public function razorpay_action($user_id='',$package_id='',$raz_order_id_session="")
	{
		$razorpay_key_id = '';
		$razorpay_key_secret = '';
		$where['where'] = array('deleted'=>'0');
		$payment_config = $this->basic->get_data('payment_config',$where,$select='');
		if(!empty($payment_config)) 
		{
			$currency = $payment_config[0]["currency"];
			$razorpay_key_id = isset($payment_config[0]['razorpay_key_id']) ? $payment_config[0]['razorpay_key_id'] : '';
			$razorpay_key_secret = isset($payment_config[0]['razorpay_key_secret']) ? $payment_config[0]['razorpay_key_secret'] : '';
		} 
		else 
			$currency = "USD";

		$this->load->library('razorpay_class_ecommerce'); 
		$this->razorpay_class_ecommerce->key_id=$razorpay_key_id;    
		$this->razorpay_class_ecommerce->key_secret=$razorpay_key_secret;    
		$response= $this->razorpay_class_ecommerce->razorpay_payment_action($raz_order_id_session);

		if(isset($response['status']) && $response['status']=='Error'){
			echo $response['message'];
			exit();
		} 

		$currency = isset($response['charge_info']['currency'])?$response['charge_info']['currency']:"INR";
		$currency = strtoupper($currency);
		$payment_amount = isset($response['charge_info']['amount_paid'])?($response['charge_info']['amount_paid']/100):"0";
		$transaction_id = isset($response['charge_info']['id'])?$response['charge_info']['id']:"";
		$payment_date= isset($response['charge_info']['created_at']) ? date("Y-m-d H:i:s",$response['charge_info']['created_at']) : '';


		// $user_id=$this->session->userdata('user_id');
		// $package_id=$this->session->userdata('razorpay_payment_package_id');

		$user_id=$user_id;
		$package_id=$package_id;
		
		$simple_where['where'] = array('user_id'=>$user_id);
		$select = array('cycle_start_date','cycle_expired_date');
		
		$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');
		
		$prev_cycle_expired_date="";


		$config_data=array();
		$price=0;
		$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
		if(is_array($package_data) && array_key_exists(0, $package_data))
			$price=$package_data[0]["price"];
		$validity=$package_data[0]["validity"];

		$validity_str='+'.$validity.' day';
		
		foreach($prev_payment_info as $info){
			$prev_cycle_expired_date=$info['cycle_expired_date'];
		}
		
		if($prev_cycle_expired_date==""){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
			$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}


		/** insert the transaction into database ***/
		$receiver_email=$this->session->userdata('user_login_email');
		$country="";
		$insert_data=array(
			"verify_status" 	=>"",
			"first_name"		=>"",
			"last_name"			=>"",
			"paypal_email"		=>"Razorpay",
			"receiver_email" 	=>$receiver_email,
			"country"			=>$country,
			"payment_date" 		=>$payment_date,
			"payment_type"		=>"Razorpay",
			"transaction_id"	=>$transaction_id,
			"user_id"           =>$user_id,
			"package_id"		=>$package_id,
			"cycle_start_date"	=>$cycle_start_date,
			"cycle_expired_date"=>$cycle_expired_date,
			"paid_amount"	    =>$payment_amount
		);


		$this->basic->insert_data('transaction_history', $insert_data);
		$this->session->set_userdata("payment_success",1);

		/** Update user table **/
		$table='users';
		$where=array('id'=>$user_id);
		$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
		$this->basic->update_data($table,$where,$data);

		$product_short_name = $this->config->item('product_short_name');
		$from = $this->config->item('institute_email');
		$mask = $this->config->item('product_name');
		$subject = "Payment Confirmation";
		$where = array();
		$where['where'] = array('id'=>$user_id);
		$user_email = $this->basic->get_data('users',$where,$select='');

		$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

		if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

			$to = $user_email[0]['email'];
			$url = base_url();
			$subject = $payment_confirmation_email_template[0]['subject'];
			$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
            //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $user_email[0]['email'];
			$subject = "Payment Confirmation";
			$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
            //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		}

        // new payment made email
		$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

		if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

			$to = $from;
			$subject = $paypal_new_payment_made_email_template[0]['subject'];
			$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
            //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $from;
			$subject = "New Payment Made";
			$message = "New payment has been made by {$user_email[0]['name']}";
            //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
		}

        // Affiliate Payment system
		if($this->addon_exist('affiliate_system')) {
			$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
			$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
			if($affiliate_id != 0) {
				$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
			}
		}


		if($this->config->item("auto_relogin_after_purchase") == '1') {

			$this->session->unset_userdata('user_type');
			$this->session->unset_userdata('logged_in');
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('download_id');
			$this->session->unset_userdata('user_login_email');
			$this->session->unset_userdata('expiry_date');
			$this->session->unset_userdata('brand_logo');

			$this->subscriber_login($user_id);
		}

		$redirect_url=base_url()."payment/transaction_log?action=success";
		redirect($redirect_url, 'refresh');
	}


	public function paystack_action($user_id='',$package_id='',$reference="")
	{
		if($user_id== "" || $user_id==0) exit;
		if($package_id== "" || $package_id==0) exit;

		// $user_id=$this->session->userdata('user_id');
		// $package_id=$this->session->userdata('paystack_payment_package_id');

		$user_id=$user_id;
		$package_id=$package_id;

		$paystack_secret_key = '';
		$where['where'] = array('deleted'=>'0');
		$payment_config = $this->basic->get_data('payment_config',$where,$select='');
		if(!empty($payment_config)) 
		{
			$currency = $payment_config[0]["currency"];
			$paystack_secret_key = isset($payment_config[0]['paystack_secret_key']) ? $payment_config[0]['paystack_secret_key'] : '';
		} 
		else 
			$currency = "USD";

		$this->load->library('paystack_class_ecommerce'); 
		$this->paystack_class_ecommerce->secret_key=$paystack_secret_key;      
		$response= $this->paystack_class_ecommerce->paystack_payment_action($reference);

		if(isset($response['status']) && $response['status']=='Error'){
			echo $response['message'];
			exit();
		} 

		$receiver_email=$this->session->userdata('user_login_email');
		$country="";

		$currency = isset($response['charge_info']['data']['currency'])?$response['charge_info']['data']['currency']:"NGN";
		$currency = strtoupper($currency);
		$payment_amount = isset($response['charge_info']['data']['amount'])?($response['charge_info']['data']['amount']/100):"0";
		$transaction_id = isset($response['charge_info']['data']['id'])?$response['charge_info']['data']['id']:"";
		$payment_date= isset($response['charge_info']['data']['paid_at']) ? date("Y-m-d H:i:s",strtotime($response['charge_info']['data']['paid_at'])) : '';

		$simple_where['where'] = array('user_id'=>$user_id);
		$select = array('cycle_start_date','cycle_expired_date');
		
		$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');
		
		$prev_cycle_expired_date="";

		$config_data=array();
		$price=0;
		$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
		if(is_array($package_data) && array_key_exists(0, $package_data))
			$price=$package_data[0]["price"];
		$validity=$package_data[0]["validity"];

		$validity_str='+'.$validity.' day';

		foreach($prev_payment_info as $info){
			$prev_cycle_expired_date=$info['cycle_expired_date'];
		}

		if($prev_cycle_expired_date==""){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}

		else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}

		else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
			$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}

		/** insert the transaction into database ***/
		$insert_data=array(
			"verify_status" 	=>"",
			"first_name"		=>"",
			"last_name"			=>"",
			"paypal_email"		=>"Paystack",
			"receiver_email" 	=>$receiver_email,
			"country"			=>$country,
			"payment_date" 		=>$payment_date,
			"payment_type"		=>"Paystack",
			"transaction_id"	=>$transaction_id,
			"user_id"           =>$user_id,
			"package_id"		=>$package_id,
			"cycle_start_date"	=>$cycle_start_date,
			"cycle_expired_date"=>$cycle_expired_date,
			"paid_amount"	    =>$payment_amount
		);

		$this->basic->insert_data('transaction_history', $insert_data);
		$this->session->set_userdata("payment_success",1);

		/** Update user table **/
		$table='users';
		$where=array('id'=>$user_id);
		$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
		$this->basic->update_data($table,$where,$data);

		$product_short_name = $this->config->item('product_short_name');
		$from = $this->config->item('institute_email');
		$mask = $this->config->item('product_name');
		$subject = "Payment Confirmation";
		$where = array();
		$where['where'] = array('id'=>$user_id);
		$user_email = $this->basic->get_data('users',$where,$select='');

		$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

		if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

			$to = $user_email[0]['email'];
			$url = base_url();
			$subject = $payment_confirmation_email_template[0]['subject'];
			$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
	        //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $user_email[0]['email'];
			$subject = "Payment Confirmation";
			$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
	        //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		}

	    // new payment made email
		$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

		if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

			$to = $from;
			$subject = $paypal_new_payment_made_email_template[0]['subject'];
			$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
	        //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $from;
			$subject = "New Payment Made";
			$message = "New payment has been made by {$user_email[0]['name']}";
	        //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
		}

	    // affiliate Section
		if($this->addon_exist('affiliate_system')) {
			$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
			$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
			if($affiliate_id != 0) {
				$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
			}
		}

		if($this->config->item("auto_relogin_after_purchase") == '1') {

			$this->session->unset_userdata('user_type');
			$this->session->unset_userdata('logged_in');
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('download_id');
			$this->session->unset_userdata('user_login_email');
			$this->session->unset_userdata('expiry_date');
			$this->session->unset_userdata('brand_logo');

			$this->subscriber_login($user_id);
		}

		$redirect_url=base_url()."payment/transaction_log?action=success";
		redirect($redirect_url, 'refresh');


	}

	public function mercadopago_action($user_id='',$package_id='')
	{
		if($user_id== "" || $user_id==0) exit;
		if($package_id== "" || $package_id==0) exit;

		$token = isset($_POST['token']) ? $_POST['token'] : '';
		$issuer_id = isset($_POST['issuer_id']) ? $_POST['issuer_id'] : '';
		$installments = isset($_POST['installments']) ? $_POST['installments'] : '';
		$payment_method_id = isset($_POST['payment_method_id']) ? $_POST['payment_method_id'] : '';

		// $user_id = $this->user_id;
		// $package_id = $this->session->userdata('mercadopago_payment_package_id');
		$user_id=$user_id;
		$package_id=$package_id;

		$payment_amount = $this->session->userdata('mercadopago_payment_amount');
		$mercadopago_access_token = $this->session->userdata('mercadopago_accesstoken');

		$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
		$description = isset($package_data[0]['package_name']) ? $package_data[0]['package_name'] : '';

		$user_info = $this->basic->get_data('users',['where'=>['id'=>$user_id]],['email']);
		$payer_email = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';

		$this->load->library("mercadopago");

		$this->mercadopago->accesstoken=$mercadopago_access_token;
		$this->mercadopago->transaction_amount=$payment_amount;
		$this->mercadopago->token=$token;
		$this->mercadopago->description=$description;
		$this->mercadopago->installments=$installments;
		$this->mercadopago->payment_method_id=$payment_method_id;
		$this->mercadopago->issuer_id=$issuer_id;
		$this->mercadopago->payer_email=$payer_email;

		$response = $this->mercadopago->payment_action();
		if(isset($response['status']) && $response['status']=='approved')
		{
			$simple_where['where'] = array('user_id'=>$user_id);
			$select = array('cycle_start_date','cycle_expired_date');
			$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');

			$prev_cycle_expired_date="";
			$price=0;

			$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
			if(is_array($package_data) && array_key_exists(0, $package_data))
				$price=$package_data[0]["price"];
			$validity=$package_data[0]["validity"];

			$validity_str='+'.$validity.' day';
			
			foreach($prev_payment_info as $info){
				$prev_cycle_expired_date=$info['cycle_expired_date'];
			}
			
			if($prev_cycle_expired_date==""){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}
			
			else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}
			
			else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
				$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			/** insert the transaction into database ***/
			$receiver_email=$this->session->userdata('user_login_email');
			$country="";
			$payment_date = date('Y-m-d H:i:s');
			$transaction_id = '';

			$insert_data=array(
				"verify_status" 	=>"",
				"first_name"		=>"",
				"last_name"			=>"",
				"paypal_email"		=>"Mercadopago",
				"receiver_email" 	=>$receiver_email,
				"country"			=>$country,
				"payment_date" 		=>$payment_date,
				"payment_type"		=>"Mercadopago",
				"transaction_id"	=>$transaction_id,
				"user_id"           =>$user_id,
				"package_id"		=>$package_id,
				"cycle_start_date"	=>$cycle_start_date,
				"cycle_expired_date"=>$cycle_expired_date,
				"paid_amount"	    =>$payment_amount
			);
			
			
			$this->basic->insert_data('transaction_history', $insert_data);
			$this->session->set_userdata("payment_success",1);

			/** Update user table **/
			$table='users';
			$where=array('id'=>$user_id);
			$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
			$this->basic->update_data($table,$where,$data);

			$product_short_name = $this->config->item('product_short_name');
			$from = $this->config->item('institute_email');
			$mask = $this->config->item('product_name');
			$subject = "Payment Confirmation";
			$where = array();
			$where['where'] = array('id'=>$user_id);
			$user_email = $this->basic->get_data('users',$where,$select='');

			$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

			if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

				$to = $user_email[0]['email'];
				$url = base_url();
				$subject = $payment_confirmation_email_template[0]['subject'];
				$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
			        //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $user_email[0]['email'];
				$subject = "Payment Confirmation";
				$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
			        //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			}

			// new payment made email
			$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

			if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

				$to = $from;
				$subject = $paypal_new_payment_made_email_template[0]['subject'];
				$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
			        //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $from;
				$subject = "New Payment Made";
				$message = "New payment has been made by {$user_email[0]['name']}";
			        //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
			}

			// affiliate Section
			if($this->addon_exist('affiliate_system')) {
				$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
				$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
				if($affiliate_id != 0) {
					$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
				}
			}

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=success";
			redirect($redirect_url, 'refresh');

		}
		else
		{

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}
			$redirect_url=base_url()."payment/transaction_log?action=cancel";
			redirect($redirect_url, 'refresh');
		}


	}


	public function mollie_action($user_id='',$package_id='')
	{

		if($user_id== "" || $user_id==0) exit;
		if($package_id== "" || $package_id==0) exit;

		// $user_id=$this->session->userdata('user_id');
		// $package_id=$this->session->userdata('mollie_payment_package_id');

		$user_id=$user_id;
		$package_id=$package_id;

		$mollie_api_key = '';
		$where['where'] = array('deleted'=>'0');
		$payment_config = $this->basic->get_data('payment_config',$where,$select='');
		if(!empty($payment_config)) 
		{
			$currency = $payment_config[0]["currency"];
			$mollie_api_key = isset($payment_config[0]['mollie_api_key']) ? $payment_config[0]['mollie_api_key'] : '';
		} 
		else 
			$currency = "USD";

		$this->load->library('mollie_class_ecommerce'); 
		$this->mollie_class_ecommerce->ec_order_id=$this->session->userdata('mollie_unique_id'); 
		$this->mollie_class_ecommerce->api_key=$mollie_api_key; 
		$response= $this->mollie_class_ecommerce->mollie_payment_action();

		if(isset($response['status']) && $response['status']=='Error'){
			$redirect_url=base_url()."payment/transaction_log?action=cancel";
			redirect($redirect_url, 'refresh');
			exit();
		}      
		
		$receiver_email="";
		$country="";

		$currency = isset($response['charge_info']['amount']['currency']) ? $response['charge_info']['amount']['currency'] : "EUR";
		$currency = strtoupper($currency);
		$payment_amount = isset($response['charge_info']['amount']['value']) ? $response['charge_info']['amount']['value'] : "0";
		$transaction_id = isset($response['charge_info']['id']) ? $response['charge_info']['id'] : "";
		$payment_date= isset($response['charge_info']['createdAt']) ? date("Y-m-d H:i:s",strtotime($response['charge_info']['createdAt'])) : '';

		$simple_where['where'] = array('user_id'=>$user_id);
		$select = array('cycle_start_date','cycle_expired_date');
		
		$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');
		
		$prev_cycle_expired_date="";


		$config_data=array();
		$price=0;
		$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
		if(is_array($package_data) && array_key_exists(0, $package_data))
			$price=$package_data[0]["price"];
		$validity=$package_data[0]["validity"];

		$validity_str='+'.$validity.' day';
		
		foreach($prev_payment_info as $info){
			$prev_cycle_expired_date=$info['cycle_expired_date'];
		}
		
		if($prev_cycle_expired_date==""){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
			$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}

		/** insert the transaction into database ***/
		$receiver_email=$this->session->userdata('user_login_email');
		$country="";
		$insert_data=array(
			"verify_status" 	=>"",
			"first_name"		=>"",
			"last_name"			=>"",
			"paypal_email"		=>"Mollie",
			"receiver_email" 	=>$receiver_email,
			"country"			=>$country,
			"payment_date" 		=>$payment_date,
			"payment_type"		=>"Mollie",
			"transaction_id"	=>$transaction_id,
			"user_id"           =>$user_id,
			"package_id"		=>$package_id,
			"cycle_start_date"	=>$cycle_start_date,
			"cycle_expired_date"=>$cycle_expired_date,
			"paid_amount"	    =>$payment_amount
		);
		
		
		$this->basic->insert_data('transaction_history', $insert_data);
		$this->session->set_userdata("payment_success",1);

		/** Update user table **/
		$table='users';
		$where=array('id'=>$user_id);
		$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
		$this->basic->update_data($table,$where,$data);

		$product_short_name = $this->config->item('product_short_name');
		$from = $this->config->item('institute_email');
		$mask = $this->config->item('product_name');
		$subject = "Payment Confirmation";
		$where = array();
		$where['where'] = array('id'=>$user_id);
		$user_email = $this->basic->get_data('users',$where,$select='');

		$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

		if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

			$to = $user_email[0]['email'];
			$url = base_url();
			$subject = $payment_confirmation_email_template[0]['subject'];
			$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
		        //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $user_email[0]['email'];
			$subject = "Payment Confirmation";
			$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
		        //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		}

		    // new payment made email
		$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

		if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

			$to = $from;
			$subject = $paypal_new_payment_made_email_template[0]['subject'];
			$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
		        //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $from;
			$subject = "New Payment Made";
			$message = "New payment has been made by {$user_email[0]['name']}";
		        //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
		}

		// affiliate Section
		if($this->addon_exist('affiliate_system')) {
			$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
			$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
			if($affiliate_id != 0) {
				$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
			}
		}

		if($this->config->item("auto_relogin_after_purchase") == '1') {

			$this->session->unset_userdata('user_type');
			$this->session->unset_userdata('logged_in');
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('download_id');
			$this->session->unset_userdata('user_login_email');
			$this->session->unset_userdata('expiry_date');
			$this->session->unset_userdata('brand_logo');

			$this->subscriber_login($user_id);
		}

		$redirect_url=base_url()."payment/transaction_log?action=success";
		redirect($redirect_url, 'refresh');
	}


	public function sslcommerz_action()
	{
		$where['where'] = array('deleted'=>'0');
		$payment_config = $this->basic->get_data('payment_config',$where,$select='');
		$store_id = isset($payment_config[0]['sslcommerz_store_id']) ? $payment_config[0]['sslcommerz_store_id'] : '';
		$store_passwd = isset($payment_config[0]['sslcommerz_store_password']) ? $payment_config[0]['sslcommerz_store_password'] : '';
		$sslcommers_mode = isset($payment_config[0]['sslcommers_mode']) ? $payment_config[0]['sslcommers_mode'] : '';

		$response = $_REQUEST['cart_json'];
		$response = json_decode($response, true);
		$total_amount = isset($response['total_amount']) ? $response['total_amount'] : '';
		$currency = isset($response['currency']) ? $response['currency'] : '';
		$product_name = isset($response['product_name']) ? $response['product_name'] : '';
		$product_category = isset($response['product_category']) ? $response['product_category'] : '';
		$cus_name = isset($response['cus_name']) ? $response['cus_name'] : '';
		$cus_email = isset($response['cus_email']) ? $response['cus_email'] : '';
		$package_id = isset($response['package_id']) ? $response['package_id'] : 0;
		$user_id = isset($response['user_id']) ? $response['user_id'] : 0;

		$post_data = array();
		$post_data['value_a'] = $user_id;
		$post_data['value_b'] = $package_id;
		$post_data['store_id'] = $store_id;
		$post_data['store_passwd'] = $store_passwd;
		$post_data['total_amount'] = $total_amount;
		$post_data['currency'] = $currency;
		$post_data['tran_id'] = "SSLCZ_TEST_".uniqid();
		$post_data['success_url'] = base_url('stripe_action/sslcommerz_success');
		$post_data['fail_url'] = base_url('stripe_action/sslcommerz_fail');
		$post_data['cancel_url'] = base_url('stripe_action/sslcommerz_fail');

		# CUSTOMER INFORMATION
		$post_data['cus_name'] = $cus_name;
		$post_data['cus_email'] = $cus_email;
		$post_data['cus_add1'] = "N/A";
		$post_data['cus_city'] = "";
		$post_data['cus_postcode'] = "";
		$post_data['cus_country'] = "";
		$post_data['cus_phone'] = 'N/A';
		
		# SHIPMENT INFORMATION
		$post_data['shipping_method'] = "NO";
		$post_data['num_of_item'] = 1;

		#product Details
		$post_data['product_name'] = $product_name;
		$post_data['product_category'] = $product_category;
		$post_data['product_profile'] = "general";


		# EMI STATUS
		$post_data['emi_option'] = "1";

		# REQUEST SEND TO SSLCOMMERZ
		if($sslcommers_mode == 'live')
			$direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
		else
			$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $direct_api_url );
		curl_setopt($handle, CURLOPT_TIMEOUT, 30);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($handle, CURLOPT_POST, 1 );
		curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


		$content = curl_exec($handle );

		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		if($code == 200 && !( curl_errno($handle))) {
			curl_close( $handle);
			$sslcommerzResponse = $content;
		} else {
			curl_close( $handle);
			echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
			exit;
		}

		# PARSE THE JSON RESPONSE
		$sslcz = json_decode($sslcommerzResponse, true );

		// var_dump($sslcz); exit;

		if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="") {
			// this is important to show the popup, return or echo to sent json response back
			echo   json_encode(['status' => 'success', 'data' => $sslcz['GatewayPageURL'], 'logo' => $sslcz['storeLogo'] ]);
		} 
		else {
			$error = isset($sslcz['failedreason']) ? $sslcz['failedreason'] : $this->lang->line('JSON Data parsing error!');
			echo   json_encode(['status' => 'fail', 'data' => null, 'message' => $error]);
		}
	}

	public function sslcommerz_success()
	{
		$user_id = isset($_POST['value_a']) ? $_POST['value_a'] : 0;
		$package_id = isset($_POST['value_b']) ? $_POST['value_b'] : 0;
		$transaction_id = isset($_POST['bank_tran_id']) ? $_POST['bank_tran_id'] : 0;
		$payment_amount = isset($_POST['currency_amount']) ? $_POST['currency_amount'] : 0;
		$card_type = isset($_POST['card_type']) ? $_POST['card_type'] : '';

		$simple_where['where'] = array('user_id'=>$user_id);
		$select = array('cycle_start_date','cycle_expired_date');
		$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');


		$prev_cycle_expired_date="";
		$price=0;

		$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
		if(is_array($package_data) && array_key_exists(0, $package_data))
			$price=$package_data[0]["price"];
		$validity=$package_data[0]["validity"];

		$validity_str='+'.$validity.' day';

		foreach($prev_payment_info as $info){
			$prev_cycle_expired_date=$info['cycle_expired_date'];
		}

		if($prev_cycle_expired_date==""){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}

		else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}

		else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
			$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}

		/** insert the transaction into database ***/
		$userinfos = $this->basic->get_data("users",['where'=>['id'=>$user_id,'status'=>'1']],['email']);
		$receiver_email=isset($userinfos[0]['email']) ? $userinfos[0]['email']:"";
		// $receiver_email=$this->session->userdata('user_login_email');
		$country="";
		$payment_date = date('Y-m-d H:i:s');


		$insert_data=array(
			"verify_status"     =>"",
			"first_name"        =>"",
			"last_name"         =>"",
			"paypal_email"      =>"",
			"receiver_email"    =>$receiver_email,
			"country"           =>$country,
			"payment_date"      =>$payment_date,
			"payment_type"      =>'SSLCOMMERZ',
			"transaction_id"    =>$transaction_id,
			"user_id"           =>$user_id,
			"package_id"        =>$package_id,
			"cycle_start_date"  =>$cycle_start_date,
			"cycle_expired_date"=>$cycle_expired_date,
			"paid_amount"       =>$payment_amount
		);


		$this->basic->insert_data('transaction_history', $insert_data);
		$this->session->set_userdata("payment_success",1);

		/** Update user table **/
		$table='users';
		$where=array('id'=>$user_id);
		$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
		$this->basic->update_data($table,$where,$data);

		$product_short_name = $this->config->item('product_short_name');
		$from = $this->config->item('institute_email');
		$mask = $this->config->item('product_name');
		$subject = "Payment Confirmation";
		$where = array();
		$where['where'] = array('id'=>$user_id);
		$user_email = $this->basic->get_data('users',$where,$select='');

		$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

		if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

			$to = $user_email[0]['email'];
			$url = base_url();
			$subject = $payment_confirmation_email_template[0]['subject'];
			$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
                //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $user_email[0]['email'];
			$subject = "Payment Confirmation";
			$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
                //send mail to user
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		}

        // new payment made email
		$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

		if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

			$to = $from;
			$subject = $paypal_new_payment_made_email_template[0]['subject'];
			$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
                //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

		} else {

			$to = $from;
			$subject = "New Payment Made";
			$message = "New payment has been made by {$user_email[0]['name']}";
                //send mail to admin
			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
		}

        // affiliate Section
		if($this->addon_exist('affiliate_system')) {
			$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
			$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
			if($affiliate_id != 0) {
				$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
			}
		}

		if($this->config->item("auto_relogin_after_purchase") == '1') {

			$this->session->unset_userdata('user_type');
			$this->session->unset_userdata('logged_in');
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('download_id');
			$this->session->unset_userdata('user_login_email');
			$this->session->unset_userdata('expiry_date');
			$this->session->unset_userdata('brand_logo');

			$this->subscriber_login($user_id);
		}

		$redirect_url=base_url()."payment/transaction_log?action=success";
		redirect($redirect_url, 'refresh');

	}

	public function sslcommerz_fail()
	{
		$redirect_url=base_url()."payment/transaction_log?action=cancel";
		redirect($redirect_url, 'refresh');
	}

	public function senangpay_action()
	{
		if($_GET['status_id'] == 1)
		{

			// $user_id = $this->user_id;
			$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
			$ex_order_id = explode("_", $order_id);
			$package_id = isset($ex_order_id[0]) ? $ex_order_id[0]:0;
			$user_id = isset($ex_order_id[1]) ? $ex_order_id[1]:0;
			$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : 0;

			$simple_where['where'] = array('user_id'=>$user_id);
			$select = array('cycle_start_date','cycle_expired_date');
			$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');


			$prev_cycle_expired_date="";
			$price=0;

			$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
			if(is_array($package_data) && array_key_exists(0, $package_data))
				$price=$package_data[0]["price"];
			$validity=$package_data[0]["validity"];

			$payment_amount = isset($package_data[0]['price']) ? $package_data[0]['price'] : 0;

			$validity_str='+'.$validity.' day';

			foreach($prev_payment_info as $info){
				$prev_cycle_expired_date=$info['cycle_expired_date'];
			}

			if($prev_cycle_expired_date==""){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
				$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			/** insert the transaction into database ***/
			$userinfos = $this->basic->get_data("users",['where'=>['id'=>$user_id,'status'=>'1']],['email']);
			$receiver_email=isset($userinfos[0]['email']) ? $userinfos[0]['email']:"";
			// $receiver_email=$this->session->userdata('user_login_email');
			$country="";
			$payment_date = date('Y-m-d H:i:s');


			$insert_data=array(
				"verify_status"     =>"",
				"first_name"        =>"",
				"last_name"         =>"",
				"paypal_email"      =>"",
				"receiver_email"    =>$receiver_email,
				"country"           =>$country,
				"payment_date"      =>$payment_date,
				"payment_type"      =>'Senangpay',
				"transaction_id"    =>$transaction_id,
				"user_id"           =>$user_id,
				"package_id"        =>$package_id,
				"cycle_start_date"  =>$cycle_start_date,
				"cycle_expired_date"=>$cycle_expired_date,
				"paid_amount"       =>$payment_amount
			);


			$this->basic->insert_data('transaction_history', $insert_data);
			$this->session->set_userdata("payment_success",1);

			/** Update user table **/
			$table='users';
			$where=array('id'=>$user_id);
			$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
			$this->basic->update_data($table,$where,$data);

			$product_short_name = $this->config->item('product_short_name');
			$from = $this->config->item('institute_email');
			$mask = $this->config->item('product_name');
			$subject = "Payment Confirmation";
			$where = array();
			$where['where'] = array('id'=>$user_id);
			$user_email = $this->basic->get_data('users',$where,$select='');

			$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

			if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

				$to = $user_email[0]['email'];
				$url = base_url();
				$subject = $payment_confirmation_email_template[0]['subject'];
				$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
	                //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $user_email[0]['email'];
				$subject = "Payment Confirmation";
				$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
	                //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			}

	        // new payment made email
			$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

			if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

				$to = $from;
				$subject = $paypal_new_payment_made_email_template[0]['subject'];
				$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
	                //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $from;
				$subject = "New Payment Made";
				$message = "New payment has been made by {$user_email[0]['name']}";
	                //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
			}

	        // affiliate Section
			if($this->addon_exist('affiliate_system')) {
				$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
				$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
				if($affiliate_id != 0) {
					$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
				}
			}

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=success";
			redirect($redirect_url, 'refresh');
		}
		else
		{

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=cancel";
			redirect($redirect_url, 'refresh');
		}
	}

	public function instamojo_action()
	{
		$package_id = $this->uri->segment(3);
		$user_id = $this->uri->segment(4);
		// $user_id = $this->user_id;

		$package_info = $this->basic->get_data('package',['where'=>['id'=>$package_id]],['package_name','price']);
		$payment_amount = isset($package_info[0]['price']) ? $package_info[0]['price'] : '';
		$package_name = isset($package_info[0]['package_name']) ? $package_info[0]['package_name'] : '';

		$user_info = $this->basic->get_data('users',['where'=>['id'=>$user_id]],['name','email','mobile']);
		$user_name = isset($user_info[0]['name']) ? $user_info[0]['name'] : '';
		$user_email = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';
		$user_mobile = isset($user_info[0]['mobile']) ? $user_info[0]['mobile'] : '012345678901';


		$redirect_url_instamojo = base_url('stripe_action/instamojo_success/').$package_id.'/'.$user_id;
		$this->load->library('instamojo');
		$this->instamojo->purpose =$package_name;
		$this->instamojo->amount = $payment_amount;
		$this->instamojo->redirect_url = $redirect_url_instamojo;
		$this->instamojo->buyer_name = $user_name;
		$this->instamojo->email = $user_email;
		// $this->instamojo->phone = $user_mobile;
		$this->instamojo->button_lang = $this->lang->line('Pay With Instamojo');
		$this->instamojo->get_long_url();
	}




	public function instamojo_success()
	{
		$package_id = $this->uri->segment(3);
		$user_id = $this->uri->segment(4);

		$payment_id = $_GET['payment_id'];
		$payment_request_id = $_GET['payment_request_id'];
		$this->load->library('instamojo');
		$this->instamojo->payment_id =$payment_id;
		$this->instamojo->payment_request_id =$payment_request_id;
		$response = $this->instamojo->success_action();

		if(isset($response['success']) && $response['success'] == 1)
		{
			// $user_id = $this->user_id;
			$user_id = $user_id;
			$package_id = $package_id;
			$transaction_id = isset($response['payment_request']['id']) ? $response['payment_request']['id'] : 0;

			$simple_where['where'] = array('user_id'=>$user_id);
			$select = array('cycle_start_date','cycle_expired_date');
			$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');


			$prev_cycle_expired_date="";
			$price=0;

			$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
			if(is_array($package_data) && array_key_exists(0, $package_data))
				$price=$package_data[0]["price"];
			$validity=$package_data[0]["validity"];

			$payment_amount = isset($package_data[0]['price']) ? $package_data[0]['price'] : 0;

			$validity_str='+'.$validity.' day';

			foreach($prev_payment_info as $info){
				$prev_cycle_expired_date=$info['cycle_expired_date'];
			}

			if($prev_cycle_expired_date==""){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
				$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			/** insert the transaction into database ***/
			$receiver_email=$this->session->userdata('user_login_email');
			$country="";
			$payment_date = date('Y-m-d H:i:s');


			$insert_data=array(
				"verify_status"     =>"",
				"first_name"        =>"",
				"last_name"         =>"",
				"paypal_email"      =>"",
				"receiver_email"    =>$receiver_email,
				"country"           =>$country,
				"payment_date"      =>$payment_date,
				"payment_type"      =>'Instamojo',
				"transaction_id"    =>$transaction_id,
				"user_id"           =>$user_id,
				"package_id"        =>$package_id,
				"cycle_start_date"  =>$cycle_start_date,
				"cycle_expired_date"=>$cycle_expired_date,
				"paid_amount"       =>$payment_amount
			);


			$this->basic->insert_data('transaction_history', $insert_data);
			$this->session->set_userdata("payment_success",1);

			/** Update user table **/
			$table='users';
			$where=array('id'=>$user_id);
			$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
			$this->basic->update_data($table,$where,$data);

			$product_short_name = $this->config->item('product_short_name');
			$from = $this->config->item('institute_email');
			$mask = $this->config->item('product_name');
			$subject = "Payment Confirmation";
			$where = array();
			$where['where'] = array('id'=>$user_id);
			$user_email = $this->basic->get_data('users',$where,$select='');

			$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

			if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

				$to = $user_email[0]['email'];
				$url = base_url();
				$subject = $payment_confirmation_email_template[0]['subject'];
				$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
	                //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $user_email[0]['email'];
				$subject = "Payment Confirmation";
				$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
	                //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			}

	        // new payment made email
			$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

			if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

				$to = $from;
				$subject = $paypal_new_payment_made_email_template[0]['subject'];
				$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
	                //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $from;
				$subject = "New Payment Made";
				$message = "New payment has been made by {$user_email[0]['name']}";
	                //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
			}

	        // affiliate Section
			if($this->addon_exist('affiliate_system')) {
				$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
				$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
				if($affiliate_id != 0) {
					$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
				}
			}

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=success";
			redirect($redirect_url, 'refresh');
		}
		else
		{

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=cancel";
			redirect($redirect_url, 'refresh');
		}

	}

	public function toyyibpay_action()
	{
		$package_id = $this->uri->segment(3);
		$user_id = $this->uri->segment(4);
		// $user_id = $this->user_id;

		$package_info = $this->basic->get_data('package',['where'=>['id'=>$package_id]],['package_name','price']);
		$payment_amount = isset($package_info[0]['price']) ? $package_info[0]['price'] : '';
		$package_name = isset($package_info[0]['package_name']) ? $package_info[0]['package_name'] : '';

		$user_info = $this->basic->get_data('users',['where'=>['id'=>$user_id]],['name','email','mobile']);
		$user_name = isset($user_info[0]['name']) ? $user_info[0]['name'] : '';
		$user_email = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';
		$user_mobile = isset($user_info[0]['mobile']) ? $user_info[0]['mobile'] : '012345678901';


		$redirect_url_toyyibpay = base_url('stripe_action/toyyibpay_success/').$package_id.'/'.$user_id;
		$this->load->library('toyyibpay');
		$this->toyyibpay->purpose =$package_name;
		$this->toyyibpay->amount = $payment_amount;
		$this->toyyibpay->redirect_url = $redirect_url_toyyibpay;
		$this->toyyibpay->buyer_name = $user_name;
		$this->toyyibpay->email = $user_email;
		$this->toyyibpay->phone = $user_mobile;
		$this->toyyibpay->button_lang = $this->lang->line('Pay with toyyibpay');
		$this->toyyibpay->get_billcode();
	}

	public function toyyibpay_success()
	{

		$response = $_GET;

		$this->load->library('toyyibpay');
		$billcode = $response['billcode'];
		$transaction_id = $response['transaction_id'];
		$this->toyyibpay->billcode = $billcode;
		$response_toyyib = $this->toyyibpay->success_action();
		$second_response = $_GET;
		$status_id = $second_response['status_id'];
		if($status_id == 1 || $status_id == 2)
		{
			$package_id = $this->uri->segment(3);
			$user_id = $this->uri->segment(4);
			// $user_id = $this->user_id;
			$package_id = $package_id;
			$simple_where['where'] = array('user_id'=>$user_id);
			$select = array('cycle_start_date','cycle_expired_date');
			$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');


			$prev_cycle_expired_date="";
			$price=0;

			$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
			if(is_array($package_data) && array_key_exists(0, $package_data))
				$price=$package_data[0]["price"];
			$validity=$package_data[0]["validity"];

			$payment_amount = isset($package_data[0]['price']) ? $package_data[0]['price'] : 0;

			$validity_str='+'.$validity.' day';

			foreach($prev_payment_info as $info){
				$prev_cycle_expired_date=$info['cycle_expired_date'];
			}

			if($prev_cycle_expired_date==""){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
				$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			/** insert the transaction into database ***/
			$receiver_email=$this->session->userdata('user_login_email');
			$country="";
			$payment_date = date('Y-m-d H:i:s');


			$insert_data=array(
				"verify_status"     =>"",
				"first_name"        =>"",
				"last_name"         =>"",
				"paypal_email"      =>"",
				"receiver_email"    =>$receiver_email,
				"country"           =>$country,
				"payment_date"      =>$payment_date,
				"payment_type"      =>'Toyyibpay',
				"transaction_id"    =>$transaction_id,
				"user_id"           =>$user_id,
				"package_id"        =>$package_id,
				"cycle_start_date"  =>$cycle_start_date,
				"cycle_expired_date"=>$cycle_expired_date,
				"paid_amount"       =>$payment_amount
			);


			$this->basic->insert_data('transaction_history', $insert_data);
			$this->session->set_userdata("payment_success",1);

			/** Update user table **/
			$table='users';
			$where=array('id'=>$user_id);
			$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
			$this->basic->update_data($table,$where,$data);

			$product_short_name = $this->config->item('product_short_name');
			$from = $this->config->item('institute_email');
			$mask = $this->config->item('product_name');
			$subject = "Payment Confirmation";
			$where = array();
			$where['where'] = array('id'=>$user_id);
			$user_email = $this->basic->get_data('users',$where,$select='');

			$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

			if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

				$to = $user_email[0]['email'];
				$url = base_url();
				$subject = $payment_confirmation_email_template[0]['subject'];
				$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
	                //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $user_email[0]['email'];
				$subject = "Payment Confirmation";
				$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
	                //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			}

	        // new payment made email
			$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

			if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

				$to = $from;
				$subject = $paypal_new_payment_made_email_template[0]['subject'];
				$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
	                //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $from;
				$subject = "New Payment Made";
				$message = "New payment has been made by {$user_email[0]['name']}";
	                //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
			}

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=success";
			redirect($redirect_url, 'refresh');
		}
		else
		{
			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=cancel";
			redirect($redirect_url, 'refresh');
		}

	}

	public function paymaya_action()
	{
		$package_id = $this->uri->segment(3);
		$user_id = $this->uri->segment(4);
		// $user_id = $this->user_id;

		$package_info = $this->basic->get_data('package',['where'=>['id'=>$package_id]],['package_name','price']);
		$payment_amount = isset($package_info[0]['price']) ? $package_info[0]['price'] : '';
		$package_name = isset($package_info[0]['package_name']) ? $package_info[0]['package_name'] : '';
		$user_info = $this->basic->get_data('users',['where'=>['id'=>$user_id]],['name','email','mobile']);
		$user_name = isset($user_info[0]['name']) ? $user_info[0]['name'] : '';
		$user_email = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';
		$user_mobile = isset($user_info[0]['mobile']) ? $user_info[0]['mobile'] : '';


		$success_url_paymaya = base_url('stripe_action/paymaya_success/').$package_id.'/'.$user_id;
		$failure_url_paymaya = base_url('stripe_action/paymaya_success/').$package_id.'/'.$user_id;
		$cancel_url_paymaya = base_url('stripe_action/paymaya_success/').$package_id.'/'.$user_id;
		$this->load->library('paymaya');
		$this->paymaya->purpose =$package_name;
		$this->paymaya->amount = $payment_amount;
		$this->paymaya->success_url = $success_url_paymaya;
		$this->paymaya->failure_url = $failure_url_paymaya;
		$this->paymaya->cancel_url = $cancel_url_paymaya;
		$this->paymaya->buyer_name = $user_name;
		$this->paymaya->email = $user_email;
		// $this->instamojo->phone = $user_mobile;
		$this->paymaya->button_lang = $this->lang->line('Pay with Paymaya');
	    $response = $this->paymaya->checkout_url();
	    $checkout_id =  $response['checkoutId'];
	    $checkout_url = $response['redirectUrl'];
	    $this->session->set_userdata('paymaya_checkoutId',$checkout_id);
	    // $this->session->userdata('paymaya_checkoutId');
	    header('Location:'.$checkout_url);

	}

	public function paymaya_success(){

		$package_id = $this->uri->segment(3);
		$user_id = $this->uri->segment(4);
		$paymaya_checkoutId = $this->session->userdata('paymaya_checkoutId');

		$this->load->library('Paymaya');
		$response = $this->paymaya->get_checkoutid($paymaya_checkoutId);
		if(isset($response['paymentStatus']) == "PAYMENT_SUCCESS")
		{
			// $user_id = $this->user_id;
			$user_id = $user_id;
			$package_id = $package_id;
			
			$transaction_id = isset($response['paymentDetails']['responses']['efs']['receipt']['transactionId']) ? $response['paymentDetails']['responses']['efs']['receipt']['transactionId'] : 0;

			$simple_where['where'] = array('user_id'=>$user_id);
			$select = array('cycle_start_date','cycle_expired_date');
			$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');


			$prev_cycle_expired_date="";
			$price=0;

			$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
			if(is_array($package_data) && array_key_exists(0, $package_data))
				$price=$package_data[0]["price"];
			$validity=$package_data[0]["validity"];

			$payment_amount = isset($package_data[0]['price']) ? $package_data[0]['price'] : 0;

			$validity_str='+'.$validity.' day';

			foreach($prev_payment_info as $info){
				$prev_cycle_expired_date=$info['cycle_expired_date'];
			}

			if($prev_cycle_expired_date==""){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
				$cycle_start_date=date('Y-m-d');
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
				$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
				$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
			}

			/** insert the transaction into database ***/
			$receiver_email=$this->session->userdata('user_login_email');
			$country="";
			$payment_date = date('Y-m-d H:i:s');


			$insert_data=array(
				"verify_status"     =>"",
				"first_name"        =>$response['buyer']['firstName'],
				"last_name"         =>$response['buyer']['lastName'],
				"paypal_email"      =>"",
				"receiver_email"    =>$receiver_email,
				"country"           =>$country,
				"payment_date"      =>$payment_date,
				"payment_type"      =>'paymaya',
				"transaction_id"    =>$transaction_id,
				"user_id"           =>$user_id,
				"package_id"        =>$package_id,
				"cycle_start_date"  =>$cycle_start_date,
				"cycle_expired_date"=>$cycle_expired_date,
				"paid_amount"       =>$payment_amount
			);


			$this->basic->insert_data('transaction_history', $insert_data);
			$this->session->set_userdata("payment_success",1);

			/** Update user table **/
			$table='users';
			$where=array('id'=>$user_id);
			$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
			$this->basic->update_data($table,$where,$data);

			$product_short_name = $this->config->item('product_short_name');
			$from = $this->config->item('institute_email');
			$mask = $this->config->item('product_name');
			$subject = "Payment Confirmation";
			$where = array();
			$where['where'] = array('id'=>$user_id);
			$user_email = $this->basic->get_data('users',$where,$select='');

			$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

			if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

				$to = $user_email[0]['email'];
				$url = base_url();
				$subject = $payment_confirmation_email_template[0]['subject'];
				$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
	                //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $user_email[0]['email'];
				$subject = "Payment Confirmation";
				$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
	                //send mail to user
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			}

	        // new payment made email
			$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

			if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

				$to = $from;
				$subject = $paypal_new_payment_made_email_template[0]['subject'];
				$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
	                //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

			} else {

				$to = $from;
				$subject = "New Payment Made";
				$message = "New payment has been made by {$user_email[0]['name']}";
	                //send mail to admin
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
			}

	        // affiliate Section
			if($this->addon_exist('affiliate_system')) {
				$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
				$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
				if($affiliate_id != 0) {
					$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
				}
			}

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=success";
			redirect($redirect_url, 'refresh');
		}
		else
		{

			if($this->config->item("auto_relogin_after_purchase") == '1') {

				$this->session->unset_userdata('user_type');
				$this->session->unset_userdata('logged_in');
				$this->session->unset_userdata('username');
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata('download_id');
				$this->session->unset_userdata('user_login_email');
				$this->session->unset_userdata('expiry_date');
				$this->session->unset_userdata('brand_logo');

				$this->subscriber_login($user_id);
			}

			$redirect_url=base_url()."payment/transaction_log?action=cancel";
			redirect($redirect_url, 'refresh');
		}

	}



	public function myfatoorah_action()
	{
		$package_id = $this->uri->segment(3);
		$user_id = $this->uri->segment(4);
		// $user_id = $this->user_id;

		$package_info = $this->basic->get_data('package',['where'=>['id'=>$package_id]],['package_name','price']);
		$payment_amount = isset($package_info[0]['price']) ? $package_info[0]['price'] : '';
		$package_name = isset($package_info[0]['package_name']) ? $package_info[0]['package_name'] : '';

		$user_info = $this->basic->get_data('users',['where'=>['id'=>$user_id]],['name','email','mobile']);
		$user_name = isset($user_info[0]['name']) ? $user_info[0]['name'] : '';
		$user_email = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';
		$user_mobile = isset($user_info[0]['mobile']) ? $user_info[0]['mobile'] : '012345678901';


    	$redirect_url_myfatoorah = base_url('stripe_action/myfatoorah_success/').$package_id.'/'.$user_id; // here need to set the actual redirect url not demo
    	$this->load->library('myfatoorah');
    	$this->myfatoorah->purpose =$package_name;
    	$this->myfatoorah->amount = $payment_amount;
    	$this->myfatoorah->callbackurl = $redirect_url_myfatoorah;
    	$this->myfatoorah->errorUrl = $redirect_url_myfatoorah;
    	$this->myfatoorah->buyer_name = $user_name;
    	$this->myfatoorah->email = $user_email;
    	$this->myfatoorah->phone = $user_mobile;
    	$this->myfatoorah->button_lang = $this->lang->line('Pay With myfatoorah');
    	$this->myfatoorah->get_long_url();
    }


    public function myfatoorah_success()
    {
    	$package_id = $this->uri->segment(3);
    	$user_id = $this->uri->segment(4);

    	$payment_id = $_GET['paymentId'];
    	$this->load->library('myfatoorah');
    	$this->myfatoorah->payment_id =$payment_id;
    	$response = $this->myfatoorah->success_action();

    	if(isset($response['Data']['InvoiceStatus']) && $response['Data']['InvoiceStatus'] == "Paid")
    	{
    		// $user_id = $this->user_id;
    		$user_id = $user_id;
    		$package_id = $package_id;
    		$transaction_id = isset($response['Data']['InvoiceTransactions'][0]['TransactionId']) ? $response['Data']['InvoiceTransactions'][0]['TransactionId'] : 0;

    		$simple_where['where'] = array('user_id'=>$user_id);
    		$select = array('cycle_start_date','cycle_expired_date');
    		$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');


    		$prev_cycle_expired_date="";
    		$price=0;

    		$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
    		if(is_array($package_data) && array_key_exists(0, $package_data))
    			$price=$package_data[0]["price"];
    		$validity=$package_data[0]["validity"];

    		$payment_amount = isset($package_data[0]['price']) ? $package_data[0]['price'] : 0;

    		$validity_str='+'.$validity.' day';

    		foreach($prev_payment_info as $info){
    			$prev_cycle_expired_date=$info['cycle_expired_date'];
    		}

    		if($prev_cycle_expired_date==""){
    			$cycle_start_date=date('Y-m-d');
    			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
    		}

    		else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
    			$cycle_start_date=date('Y-m-d');
    			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
    		}

    		else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
    			$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
    			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
    		}

    		/** insert the transaction into database ***/
    		$receiver_email=$this->session->userdata('user_login_email');
    		$country="";
    		$payment_date = date('Y-m-d H:i:s');


    		$insert_data=array(
    			"verify_status"     =>"",
    			"first_name"        =>"",
    			"last_name"         =>"",
    			"paypal_email"      =>"",
    			"receiver_email"    =>$receiver_email,
    			"country"           =>$country,
    			"payment_date"      =>$payment_date,
    			"payment_type"      =>'myfatoorah',
    			"transaction_id"    =>$transaction_id,
    			"user_id"           =>$user_id,
    			"package_id"        =>$package_id,
    			"cycle_start_date"  =>$cycle_start_date,
    			"cycle_expired_date"=>$cycle_expired_date,
    			"paid_amount"       =>$payment_amount
    		);


    		$this->basic->insert_data('transaction_history', $insert_data);
    		$this->session->set_userdata("payment_success",1);

    		/** Update user table **/
    		$table='users';
    		$where=array('id'=>$user_id);
    		$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
    		$this->basic->update_data($table,$where,$data);

    		$product_short_name = $this->config->item('product_short_name');
    		$from = $this->config->item('institute_email');
    		$mask = $this->config->item('product_name');
    		$subject = "Payment Confirmation";
    		$where = array();
    		$where['where'] = array('id'=>$user_id);
    		$user_email = $this->basic->get_data('users',$where,$select='');

    		$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

    		if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

    			$to = $user_email[0]['email'];
    			$url = base_url();
    			$subject = $payment_confirmation_email_template[0]['subject'];
    			$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
	                //send mail to user
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

    		} else {

    			$to = $user_email[0]['email'];
    			$subject = "Payment Confirmation";
    			$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
	                //send mail to user
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

    		}

	        // new payment made email
    		$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

    		if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

    			$to = $from;
    			$subject = $paypal_new_payment_made_email_template[0]['subject'];
    			$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
	                //send mail to admin
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

    		} else {

    			$to = $from;
    			$subject = "New Payment Made";
    			$message = "New payment has been made by {$user_email[0]['name']}";
	                //send mail to admin
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
    		}

	        // affiliate Section
    		if($this->addon_exist('affiliate_system')) {
    			$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
    			$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
    			if($affiliate_id != 0) {
    				$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
    			}
    		}

    		if($this->config->item("auto_relogin_after_purchase") == '1') {

    			$this->session->unset_userdata('user_type');
    			$this->session->unset_userdata('logged_in');
    			$this->session->unset_userdata('username');
    			$this->session->unset_userdata('user_id');
    			$this->session->unset_userdata('download_id');
    			$this->session->unset_userdata('user_login_email');
    			$this->session->unset_userdata('expiry_date');
    			$this->session->unset_userdata('brand_logo');

    			$this->subscriber_login($user_id);
    		}
    		
    		$redirect_url=base_url()."payment/transaction_log?action=success";
    		redirect($redirect_url, 'refresh');

    	}
    	else
    	{

    		if($this->config->item("auto_relogin_after_purchase") == '1') {

    			$this->session->unset_userdata('user_type');
    			$this->session->unset_userdata('logged_in');
    			$this->session->unset_userdata('username');
    			$this->session->unset_userdata('user_id');
    			$this->session->unset_userdata('download_id');
    			$this->session->unset_userdata('user_login_email');
    			$this->session->unset_userdata('expiry_date');
    			$this->session->unset_userdata('brand_logo');

    			$this->subscriber_login($user_id);
    		}

    		$redirect_url=base_url()."payment/transaction_log?action=cancel";
    		redirect($redirect_url, 'refresh');
    	}

    }




    public function xendit_action()
    {
    	$package_id = $this->uri->segment(3);
    	$user_id = $this->uri->segment(4);
    	// $user_id = $this->user_id;
    	$payment_info = $this->basic->get_data('payment_config');

    	$currency=isset($payment_info[0]["currency"])?$payment_info[0]["currency"]:"IDR";

    	$package_info = $this->basic->get_data('package',['where'=>['id'=>$package_id]],['package_name','price']);
    	$payment_amount = isset($package_info[0]['price']) ? $package_info[0]['price'] : '';
    	$package_name = isset($package_info[0]['package_name']) ? $package_info[0]['package_name'] : '';

    	$user_info = $this->basic->get_data('users',['where'=>['id'=>$user_id]],['name','email','mobile']);
    	$user_name = isset($user_info[0]['name']) ? $user_info[0]['name'] : '';
    	$user_email = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';
    	$user_mobile = isset($user_info[0]['mobile']) ? $user_info[0]['mobile'] : '012345678901';


    	$xendit_success_redirect_url = base_url('stripe_action/xendit_success/').$package_id.'/'.$user_id;
    	$xendit_failure_redirect_url = base_url('stripe_action/xendit_fail/').$package_id.'/'.$user_id;
    	$external_id = 'xendit_'.uniqid();
    	$this->load->library('xendit');
    	$this->xendit->external_id =$external_id;
    	$this->xendit->payer_email =$user_email;
    	$this->xendit->description =$package_name;
    	$this->xendit->amount = $payment_amount;
    	$this->xendit->xendit_success_redirect_url = $xendit_success_redirect_url;
    	$this->xendit->xendit_failure_redirect_url = $xendit_failure_redirect_url;
    	$this->xendit->currency = $currency ;
    	$this->xendit->button_lang = $this->lang->line('Pay With Xendit');
    	$this->xendit->get_long_url();
    }
    public function xendit_success()
    {
    	$package_id = $this->uri->segment(3);	
    	$user_id = $this->uri->segment(4);	
    	$this->load->library('xendit');
    	$response = $this->xendit->success_action();
    	if(isset($response[0]['status']) && $response[0]['status'] == 'PAID')
    	{
    		// $user_id = $this->user_id;
    		$user_id = $user_id;
    		$package_id = $package_id;
    		$transaction_id = isset($response[0]['external_id']) ? $response[0]['external_id'] : 0;

    		$simple_where['where'] = array('user_id'=>$user_id);
    		$select = array('cycle_start_date','cycle_expired_date');
    		$prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');


    		$prev_cycle_expired_date="";
    		$price=0;

    		$package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
    		if(is_array($package_data) && array_key_exists(0, $package_data))
    			$price=$package_data[0]["price"];
    		$validity=$package_data[0]["validity"];

    		$payment_amount = isset($package_data[0]['price']) ? $package_data[0]['price'] : 0;

    		$validity_str='+'.$validity.' day';

    		foreach($prev_payment_info as $info){
    			$prev_cycle_expired_date=$info['cycle_expired_date'];
    		}

    		if($prev_cycle_expired_date==""){
    			$cycle_start_date=date('Y-m-d');
    			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
    		}

    		else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
    			$cycle_start_date=date('Y-m-d');
    			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
    		}

    		else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
    			$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
    			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
    		}

    		/** insert the transaction into database ***/
    		$receiver_email=$this->session->userdata('user_login_email');
    		$country="";
    		$payment_date = date('Y-m-d H:i:s');


    		$insert_data=array(
    			"verify_status"     =>"",
    			"first_name"        =>"",
    			"last_name"         =>"",
    			"paypal_email"      =>"",
    			"receiver_email"    =>$receiver_email,
    			"country"           =>$country,
    			"payment_date"      =>$payment_date,
    			"payment_type"      =>'xendit',
    			"transaction_id"    =>$transaction_id,
    			"user_id"           =>$user_id,
    			"package_id"        =>$package_id,
    			"cycle_start_date"  =>$cycle_start_date,
    			"cycle_expired_date"=>$cycle_expired_date,
    			"paid_amount"       =>$payment_amount
    		);


    		$this->basic->insert_data('transaction_history', $insert_data);
    		$this->session->set_userdata("payment_success",1);

    		/** Update user table **/
    		$table='users';
    		$where=array('id'=>$user_id);
    		$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
    		$this->basic->update_data($table,$where,$data);

    		$product_short_name = $this->config->item('product_short_name');
    		$from = $this->config->item('institute_email');
    		$mask = $this->config->item('product_name');
    		$subject = "Payment Confirmation";
    		$where = array();
    		$where['where'] = array('id'=>$user_id);
    		$user_email = $this->basic->get_data('users',$where,$select='');

    		$payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

    		if(isset($payment_confirmation_email_template[0]) && $payment_confirmation_email_template[0]['subject'] != '' && $payment_confirmation_email_template[0]['message'] != '') {

    			$to = $user_email[0]['email'];
    			$url = base_url();
    			$subject = $payment_confirmation_email_template[0]['subject'];
    			$message = str_replace(array('#PRODUCT_SHORT_NAME#','#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#SITE_URL#','#APP_NAME#'),array($product_short_name,$product_short_name,$cycle_expired_date,$url,$mask),$payment_confirmation_email_template[0]['message']);
	                //send mail to user
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

    		} else {

    			$to = $user_email[0]['email'];
    			$subject = "Payment Confirmation";
    			$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
	                //send mail to user
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

    		}

	        // new payment made email
    		$paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

    		if(isset($paypal_new_payment_made_email_template[0]) && $paypal_new_payment_made_email_template[0]['subject'] !='' && $paypal_new_payment_made_email_template[0]['message'] != '') {

    			$to = $from;
    			$subject = $paypal_new_payment_made_email_template[0]['subject'];
    			$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$paypal_new_payment_made_email_template[0]['message']);
	                //send mail to admin
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);

    		} else {

    			$to = $from;
    			$subject = "New Payment Made";
    			$message = "New payment has been made by {$user_email[0]['name']}";
	                //send mail to admin
    			$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
    		}

	        // affiliate Section
    		if($this->addon_exist('affiliate_system')) {
    			$get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
    			$affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
    			if($affiliate_id != 0) {
    				$this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
    			}
    		}

    		if($this->config->item("auto_relogin_after_purchase") == '1') {

    			$this->session->unset_userdata('user_type');
    			$this->session->unset_userdata('logged_in');
    			$this->session->unset_userdata('username');
    			$this->session->unset_userdata('user_id');
    			$this->session->unset_userdata('download_id');
    			$this->session->unset_userdata('user_login_email');
    			$this->session->unset_userdata('expiry_date');
    			$this->session->unset_userdata('brand_logo');

    			$this->subscriber_login($user_id);
    		}
    		
    		$redirect_url=base_url()."payment/transaction_log?action=success";
    		redirect($redirect_url, 'refresh');
    	}
    	else
    	{

    		if($this->config->item("auto_relogin_after_purchase") == '1') {

    			$this->session->unset_userdata('user_type');
    			$this->session->unset_userdata('logged_in');
    			$this->session->unset_userdata('username');
    			$this->session->unset_userdata('user_id');
    			$this->session->unset_userdata('download_id');
    			$this->session->unset_userdata('user_login_email');
    			$this->session->unset_userdata('expiry_date');
    			$this->session->unset_userdata('brand_logo');

    			$this->subscriber_login($user_id);
    		}
    		
    		$redirect_url=base_url()."payment/transaction_log?action=cancel";
    		redirect($redirect_url, 'refresh');
    	}

    }

    public function xendit_fail()
    {
    	$redirect_url=base_url()."payment/transaction_log?action=cancel";
    	redirect($redirect_url, 'refresh');
    }



}

