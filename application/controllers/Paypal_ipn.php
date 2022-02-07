<?php

class Paypal_ipn extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('paypal_class');
        $this->load->model('basic');
        $query="SET SESSION sql_mode = ''";
        $this->db->query($query);
        $time_zone = $this->config->item('time_zone');
        if ($time_zone== '') {
            $time_zone="Europe/Dublin";
        }
        date_default_timezone_set($time_zone);
        set_time_limit(0);
    }

      public function ipn_notify(){

            $payment_info=$this->paypal_class->run_ipn();

            $api_data=$this->basic->get_data("native_api","","",$join='',$limit='1',$start=0,$order_by='ID ASC',$group_by='');
            $api_key="";
            if(count($api_data)>0) 
                $api_key=$api_data[0]["api_key"];

            $payment_info['api_key'] =$api_key;

            $payment_info_json=json_encode($payment_info);

            $post_data_payment_info=array("response_raw"=>$payment_info_json);
            $url=base_url()."paypal_ipn/ipn_notify_main";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_payment_info);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            $reply_response=curl_exec($ch); 

           $curl_information =  curl_getinfo($ch);
        	$curl_error="";
	        if($curl_information['http_code']!='200'){
	            $curl_error = curl_error($ch);
	        }

	        $curl_error=$curl_information['http_code']." : ".$curl_error;

	        $insert_data['call_time']=date("Y-m-d H:i:s");
	        $insert_data['ipn_value']=$payment_info_json;
	        $insert_data['error_log']=$curl_error; 
	        $this->db->insert('paypal_error_log',$insert_data);
    }


    
    public function ipn_notify_main()
    {
    
        $response_raw=$this->input->post("response_raw");   
        $payment_info = json_decode($response_raw,TRUE);
        
        $verify_status=isset($payment_info['verify_status']) ? $payment_info['verify_status']:"";
        $first_name= isset($payment_info['data']['first_name']) ? $payment_info['data']['first_name']:"";
        $last_name= isset($payment_info['data']['last_name']) ? $payment_info['data']['last_name']:"";
        $buyer_email= isset($payment_info['data']['payer_email']) ? $payment_info['data']['payer_email']:"";
        $receiver_email= isset($payment_info['data']['receiver_email']) ? $payment_info['data']['receiver_email']:""; 
        $country= isset($payment_info['data']['address_country_code']) ? $payment_info['data']['address_country_code']:""; 
        $payment_date=isset($payment_info['data']['payment_date']) ? $payment_info['data']['payment_date']:""; 
        $transaction_id=isset($payment_info['data']['txn_id']) ? $payment_info['data']['txn_id']:""; 
        $payment_type=isset($payment_info['data']['payment_type']) ? "PAYPAL-".ucfirst($payment_info['data']['payment_type']) : "PAYPAL"; 
        $payment_amount=isset($payment_info['data']['mc_gross']) ? $payment_info['data']['mc_gross']:"";
        $user_id_package_id=explode('_',$payment_info['data']['custom']);
        $user_id=$user_id_package_id[0];
        $package_id=$user_id_package_id[1];

        $payment_date = date("Y-m-d H:i:s",strtotime($payment_date));

        /****Get API Key & Match With the post API Key, If not same , then exit it . ***/

        $api_data=$this->basic->get_data("native_api","","",$join='',$limit='1',$start=0,$order_by='ID ASC',$group_by='');
        $api_key="";
        if(count($api_data)>0) 
            $api_key=$api_data[0]["api_key"];

        $post_api_from_ipn= $payment_info['api_key']; 

        if($api_key!=$post_api_from_ipn) exit();

        /***Check if the transaction id is already used or not, if used, then exit to prevent multiple add***/

        $simple_where_duplicate_check['where'] = array('transaction_id'=>$transaction_id);
        $prev_payment_info_transaction = $this->basic->get_data('transaction_history',$simple_where_duplicate_check,"",$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');

        if(count($prev_payment_info_transaction)>0)
            exit;
        
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
        
       
        $paypal_status_verification = $this->config->item("paypal_status_verification");
        if($paypal_status_verification=='') $paypal_status_verification='1';
       
       /* if($paypal_status_verification=='1')
        {
            if($verify_status!="VERIFIED" || $payment_amount<$price) exit();
        }
        else
        {
            if($payment_amount<$price)  exit();
        } */
        
        
         $insert_data=array(
                "verify_status"     =>$verify_status,
                "first_name"        =>$first_name,
                "last_name"         =>$last_name,
                "paypal_email"      =>$buyer_email,
                "receiver_email"    =>$receiver_email,
                "country"           =>$country,
                "payment_date"      =>$payment_date,
                "payment_type"      =>$payment_type,
                "transaction_id"    =>$transaction_id,
                "user_id"           =>$user_id,
                "package_id"        =>$package_id,
                "cycle_start_date"  =>$cycle_start_date,
                "cycle_expired_date"=>$cycle_expired_date,
                "paid_amount"       =>$payment_amount
            );
            
            
        $this->basic->insert_data('transaction_history', $insert_data);     
        
        /** Update user table **/
        $table='users';
        $where=array('id'=>$user_id);
        $data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id,"bot_status"=>"1");
        $this->basic->update_data($table,$where,$data);


        $product_short_name = $this->config->item('product_short_name');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');
        $where = array();
        $where['where'] = array('id'=>$user_id);
        $user_email = $this->basic->get_data('users',$where,$select='');

        // affiliate Section
        if($this->basic->is_exist("add_ons",array("unique_name"=>"affiliate_system"))) {
            $get_affiliate_id = $this->basic->get_data("users",['where'=>['id'=>$user_id]],['affiliate_id']);
            $affiliate_id = isset($get_affiliate_id[0]['affiliate_id']) ? $get_affiliate_id[0]['affiliate_id']:0;
            // echo $affiliate_id; exit;
            if($affiliate_id != 0) {
                // $this->affiliate_commission($affiliate_id,$user_id,'payment',$price);
                $link=base_url().'home/affiliate_commission/'.$affiliate_id.'/'.$user_id.'/payment/'.$price;
                $this->call_curl_internal_cronjob($link);
            }
        }


        $payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'paypal_payment')),array('subject','message'));

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
        $paypal_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'paypal_new_payment_made')),array('subject','message'));

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

    }


    private function _mail_sender($from = '', $to = '', $subject = '', $message = '', $mask = "", $html = 1, $smtp = 1,$attachement="",$test_mail="")
    {
        if ($to!= '' && $subject!='' && $message!= '')
        {
            if($this->config->item('email_sending_option') == '') $email_sending_option = 'smtp';
            else $email_sending_option = $this->config->item('email_sending_option');

            if($test_mail == 1) $email_sending_option = 'smtp';

            $message=$message."<br/><br/>".$this->lang->line("The email was sent by"). ": ".$from;

            if($email_sending_option == 'smtp')
            {
                if ($smtp == '1') {
                    $where2 = array("where" => array('status' => '1','deleted' => '0'));
                    $email_config_details = $this->basic->get_data("email_config", $where2, $select = '', $join = '', $limit = '', $start = '', $group_by = '', $num_rows = 0);

                    if (count($email_config_details) == 0) {
                        $this->load->library('email');
                    } else {
                        foreach ($email_config_details as $send_info) {
                            $send_email = trim($send_info['email_address']);
                            $smtp_host = trim($send_info['smtp_host']);
                            $smtp_port = trim($send_info['smtp_port']);
                            $smtp_user = trim($send_info['smtp_user']);
                            $smtp_password = trim($send_info['smtp_password']);
                            $smtp_type = trim($send_info['smtp_type']);
                        }

                    /*****Email Sending Code ******/
                    $config = array(
                      'protocol' => 'smtp',
                      'smtp_host' => "{$smtp_host}",
                      'smtp_port' => "{$smtp_port}",
                      'smtp_user' => "{$smtp_user}", // change it to yours
                      'smtp_pass' => "{$smtp_password}", // change it to yours
                      'mailtype' => 'html',
                      'charset' => 'utf-8',
                      'newline' =>  "\r\n",
                      'set_crlf'=> "\r\n",
                      'smtp_timeout' => '30',
                      'wrapchars'   => '998'
                     );
                    if($smtp_type != 'Default')
                        $config['smtp_crypto'] = $smtp_type;

                        $this->load->library('email', $config);
                    }
                } /*** End of If Smtp== 1 **/

                if (isset($send_email) && $send_email!= "") {
                    $from = $send_email;
                }
                $this->email->from($from, $mask);
                $this->email->to($to);
                $this->email->subject($subject);
                $this->email->message($message);
                if ($html == 1) {
                    $this->email->set_mailtype('html');
                }
                if ($attachement!="") {
                    $this->email->attach($attachement);
                }

                if ($this->email->send()) {
                    return true;
                } else {

                    if($test_mail==1) {
                        return $this->email->print_debugger();
                    } else {
                        return false;
                    }
                }                
            }

            if($email_sending_option == 'php_mail')
            {
                $from = get_domain_only(base_url());
                $from = "support@".$from;
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: {$from}" . "\r\n";
                if(mail($to, $subject, $message, $headers))
                    return true;
                else
                    return false;
            }



        } else {
            return false;
        }
    }

    private function call_curl_internal_cronjob($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        echo $reply_response=curl_exec($ch);
        curl_close($ch); 
    }

}