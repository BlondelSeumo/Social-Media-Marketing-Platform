<?php
require_once("application/controllers/Home.php"); // loading home controller
class Webview_builder extends Home 
{
	public function __construct()
    {
        parent::__construct();              
    }


    public function get_date_user_input()
	{
		//header('X-Frame-Options: ALLOW-FROM https://www.messenger.com/');
		//header('X-Frame-Options: ALLOW-FROM https://www.facebook.com/');

		$subscriber_id=$this->input->get('subscriber_id');
		$picker_type=$this->input->get('type');

		$picker_class="datepicker_x";

		if($picker_type=='date'){
			$picker_class="datepicker_x";
			$title="Select Date";
			$placeholder="YYYY-MM-DD";
		}
		else if($picker_type=='time'){
			$picker_class="timepicker_x";
			$title="Select Time";
			$placeholder="HH:MM";
		}
		else if($picker_type=='datetime'){
			$picker_class="datetimepicker_x";
			$title="Select Datetime";
			$placeholder="YYYY-MM-DD HH:MM";
		}

		$data['subscriber_id'] =$subscriber_id;
		$data['form']['form_title'] = $this->lang->line($title);
        $data['body'] = 'webview_builder/user_input_get_date_view.php';
        $data['picker_class'] = $picker_class;
        $data['page_title'] = '';
        $data['field_title'] = $this->lang->line($title);
        $data['placeholder'] = $placeholder;


        // Get Facebook app ID 
        $fb_app_id_info=$this->basic->get_data('facebook_rx_config',$where=array('where'=>array('status'=>'1')));
        $data['fb_app_id']=isset($fb_app_id_info[0]['api_id']) ? $fb_app_id_info[0]['api_id']: "";

        $data['is_rtl'] = $this->is_rtl;
        $this->load->view('webview_builder/bare-theme', $data);  
	}


   
	public function get_birthdate()
	{
		//header('X-Frame-Options: ALLOW-FROM https://www.messenger.com/');
		//header('X-Frame-Options: ALLOW-FROM https://www.facebook.com/');

		$subscriber_id=$this->input->get('subscriber_id');
		$data['subscriber_id'] =$subscriber_id;
		$data['form']['form_title'] = $this->lang->line("Your Birthdate");
        $data['body'] = 'webview_builder/birthday_view';
        $data['page_title'] = '';

        // Get Facebook app ID 
        $fb_app_id_info=$this->basic->get_data('facebook_rx_config',$where=array('where'=>array('status'=>'1')));
        $data['fb_app_id']=isset($fb_app_id_info[0]['api_id']) ? $fb_app_id_info[0]['api_id']: "";

        $data['is_rtl'] = $this->is_rtl;
        $this->load->view('webview_builder/bare-theme', $data);  
	}

	public function get_location()
	{
		//header('X-Frame-Options: ALLOW-FROM https://www.messenger.com/');
		//header('X-Frame-Options: ALLOW-FROM https://www.facebook.com/');

		$subscriber_id=$this->input->get('subscriber_id');
		$data['subscriber_id'] =$subscriber_id;
		$data['form']['form_title'] = $this->lang->line("Your Location");
        $data['body'] = 'webview_builder/location_view';
        $data['page_title'] = '';
        $country_list = $this->get_country_names();
        $data['country_list'] = $country_list;

        // Get Facebook app ID 
        $fb_app_id_info=$this->basic->get_data('facebook_rx_config',$where=array('where'=>array('status'=>'1')));
        $data['fb_app_id']=isset($fb_app_id_info[0]['api_id']) ? $fb_app_id_info[0]['api_id']: "";

        $data['is_rtl'] = $this->is_rtl;
        $this->load->view('webview_builder/bare-theme', $data);  
	}





	public function get_email()
	{
		//header('X-Frame-Options: ALLOW-FROM https://www.messenger.com/');
		//header('X-Frame-Options: ALLOW-FROM https://www.facebook.com/');

		$subscriber_id=$this->input->get('subscriber_id');
		$data['subscriber_id'] =$subscriber_id;
		$data['form']['form_title'] = $this->lang->line("Your Email");
        $data['body'] = 'webview_builder/email_view';
        $data['page_title'] = '';

        // Get Facebook app ID 
        $fb_app_id_info=$this->basic->get_data('facebook_rx_config',$where=array('where'=>array('status'=>'1')));
        $data['fb_app_id']=isset($fb_app_id_info[0]['api_id']) ? $fb_app_id_info[0]['api_id']: "";

        $data['is_rtl'] = $this->is_rtl;
        $this->load->view('webview_builder/bare-theme', $data);  
	}



	public function get_phone()
	{
		//header('X-Frame-Options: ALLOW-FROM https://www.messenger.com/');
		//header('X-Frame-Options: ALLOW-FROM https://www.facebook.com/');

		$subscriber_id=$this->input->get('subscriber_id');
		$data['subscriber_id'] =$subscriber_id;
		
		$data['form']['form_title'] = $this->lang->line("Your Phone Number");
        $data['body'] = 'webview_builder/phone_view';
        $data['page_title'] = '';

        // Get Facebook app ID 
        $fb_app_id_info=$this->basic->get_data('facebook_rx_config',$where=array('where'=>array('status'=>'1')));
        $data['fb_app_id']=isset($fb_app_id_info[0]['api_id']) ? $fb_app_id_info[0]['api_id']: "";

        $data['is_rtl'] = $this->is_rtl;
        $this->load->view('webview_builder/bare-theme', $data);  
	}








    public function birthdate_submit(){

    	$form_data=$this->input->post();
    	$subscriber_id=$form_data['subscriber_id'];
    	$birthdate=$form_data['birthdate'];
  
    	if($subscriber_id==""){
    		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
    	}

    	// Get subscriber information & page information 

       $where = array("where"=> array('messenger_bot_subscriber.subscribe_id'=>$subscriber_id));
       $select = array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id","facebook_rx_fb_page_info.page_id","page_access_token","first_name","last_name","messenger_bot_subscriber.status","facebook_rx_fb_page_info.id","messenger_bot_subscriber.user_id");
       $join  = array("facebook_rx_fb_page_info"=>"messenger_bot_subscriber.page_table_id=facebook_rx_fb_page_info.id,left");
       $table="messenger_bot_subscriber";
       $subscriber_info = $this->basic->get_data($table,$where,$select,$join);

       $page_access_token= isset($subscriber_info[0]['page_access_token']) ? $subscriber_info[0]['page_access_token'] : ""; 
       $page_id= isset($subscriber_info[0]['page_id']) ? $subscriber_info[0]['page_id'] : ""; 
       $page_table_id=isset($subscriber_info[0]['id']) ? $subscriber_info[0]['id'] : "";
       $user_id= isset($subscriber_info[0]['user_id']) ? $subscriber_info[0]['user_id'] : "";

       if(empty($subscriber_info)){
       		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
       }

       //Update Subscribers BirthDate : 
       $update_data=array("birthdate"=>$birthdate);
       $update_where=array("subscribe_id"=>$subscriber_id,"page_table_id"=>$page_table_id);
       $this->basic->update_data("messenger_bot_subscriber",$update_where,$update_data);


	    // Send message to subscriber 

	  	$where=array('where'=>array('keyword_type'=>'birthday-quick-reply','page_id'=>$page_table_id));
		$reply_template_info=$this->basic->get_data('messenger_bot',$where);

		if(!empty($reply_template_info)) {

			$message_str = $reply_template_info[0]['message'];
		   $message_array = json_decode($message_str,true);

		   foreach($message_array as $msg)
		   {
		   	$template_type_file_track=$msg['message']['template_type'];
		   	unset($msg['message']['template_type']);

		        // typing on and typing on delay [alamin]
		   	$enable_typing_on = isset($msg['message']['typing_on_settings']) ? $msg['message']['typing_on_settings'] : "";
		   	$enable_typing_on = ($enable_typing_on=='on')  ? 1 : 0;
		   	unset($msg['message']['typing_on_settings']);
		   	$typing_on_delay_time = isset($msg['message']['delay_in_reply']) ? $msg['message']['delay_in_reply'] : "";
		   	if($typing_on_delay_time=="") $typing_on_delay_time = 0;
		   	unset($msg['message']['delay_in_reply']);

		   	$text_reply_unique_id = '';
		   	if(isset($msg['message']['text_reply_unique_id']))
		   	{
		   		$text_reply_unique_id=$msg['message']['text_reply_unique_id'];
		   		unset($msg['message']['text_reply_unique_id']);
		   	}


		      // keep the track of quick reply send for email & phone button. 
		   	$quick_replies=array();
		   	$phone_quick_reply_button_id="";
		   	$email_quick_reply_button_id="";
		   	$has_quick_reply=0;

		   	$quick_replies= $msg['message']['quick_replies'] ?? [];

		   	if(!empty($quick_replies)){

		   		$has_quick_reply=1;

		   		foreach($quick_replies as $q_index=>$q_reply){

		   			if($q_reply['content_type']=="user_phone_number"){

		   				$phone_quick_reply_button_id= $msg['message']['quick_replies'][$q_index]['unique_id'] ?? "";
		   				unset($msg['message']['quick_replies'][$q_index]['unique_id']);
		   			}

		   			else if($q_reply['content_type']=="user_email"){

		   				$email_quick_reply_button_id= $msg['message']['quick_replies'][$q_index]['unique_id'] ?? "";
		   				unset($msg['message']['quick_replies'][$q_index]['unique_id']);
		   			}

		   		}

		   	}

		   	/** Spintax **/
		   	if(isset($msg['message']['text']))
		   		$msg['message']['text']=spintax_process($msg['message']['text']);               

		   	$msg['messaging_type'] = "RESPONSE";
		   	$reply = json_encode($msg);     

		   	$replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
		   	$replace_with=array('{"id":"'.$subscriber_id.'"}',$subscriber_id);
		   	$reply=str_replace($replace_search, $replace_with, $reply);

		   	if(isset($subscriber_info[0]['first_name']))
		   		$reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
		   	if(isset($subscriber_info[0]['last_name']))
		   		$reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
		   	$access_token = $page_access_token;
		   	$is_error=0;

		   	if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){
		            // typing on and typing on delay [alamin]
		   		if($enable_typing_on) $this->sender_action($subscriber_id,"typing_on",$access_token);                                
		   		if($typing_on_delay_time>0) sleep($typing_on_delay_time);

		   		if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
		   			$post_data=array("access_token"=>$access_token,"reply"=>$reply);
		   			$url=base_url()."home/send_reply_curl_call";
		   			$ch = curl_init();
		   			curl_setopt($ch, CURLOPT_URL, $url);
		   			curl_setopt($ch,CURLOPT_POST,1);
		   			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		   			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		   			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		   			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		   			$reply_response=curl_exec($ch);  

		   		}
		   		else
		   			$reply_response=$this->send_reply($access_token,$reply);
		   		/*****Insert into database messenger_bot_reply_error_log if get error****/

		   		if(isset($reply_response['error']['message']))
		   		{
		   			$is_error=1;
		   			$bot_settings_id= $reply_template_info[0]['id'];
		   			$reply_error_message= $reply_response['error']['message'];
		   			$error_time= date("Y-m-d H:i:s");
		   			$error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
		   				"error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
		   				"error_time"=>$error_time);
		   			$this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);

		   		}

		   	}

		   	// update email & phone quick reply button id in subscriber extra table 
		   	if($has_quick_reply==1 && $is_error==0){

		   		$insert_subscriber_input_flow_assaign="INSERT INTO messenger_bot_subscriber_extra_info (subscriber_id,page_id,email_quick_reply_button_id,phone_quick_reply_button_id) 
		   		VALUES ('$subscriber_id','$page_id','$email_quick_reply_button_id','$phone_quick_reply_button_id')
		   		ON DUPLICATE KEY UPDATE  email_quick_reply_button_id='$email_quick_reply_button_id',phone_quick_reply_button_id='$phone_quick_reply_button_id'";
		   		$this->basic->execute_complex_query($insert_subscriber_input_flow_assaign);

		   	}
		   	if($text_reply_unique_id != '')
		   	{
		   		
		   		$message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$subscriber_id',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
		   		$this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
		   	}

		   }


		}

		$this->user_id=$user_id;

		 if($this->addon_exist("messenger_bot_connectivity")) 
		$this->thirdparty_webhook_trigger($page_id,$subscriber_id,"trigger_birthdate");

		$response['error']='0';
		echo json_encode($response);


    }


    public function user_input_date_submit(){

    	$form_data=$this->input->post();
    	$subscriber_id=$form_data['subscriber_id'];
    	$date=$form_data['select_date'];
  
    	if($subscriber_id==""){
    		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
    	}

    	// Get subscriber information & page information 

       $where = array("where"=> array('messenger_bot_subscriber.subscribe_id'=>$subscriber_id));

       $select = array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id","facebook_rx_fb_page_info.page_id","page_access_token","first_name","last_name","messenger_bot_subscriber.status","facebook_rx_fb_page_info.id","messenger_bot_subscriber.user_id","mail_service_id","email_api_id","email_reply_message","email_reply_subject","facebook_rx_fb_page_info.page_name","sequence_sms_campaign_id","sequence_email_campaign_id");
       $join  = array("facebook_rx_fb_page_info"=>"messenger_bot_subscriber.page_table_id=facebook_rx_fb_page_info.id,left");
       $table="messenger_bot_subscriber";
       $subscriber_info = $this->basic->get_data($table,$where,$select,$join);


       $page_access_token= isset($subscriber_info[0]['page_access_token']) ? $subscriber_info[0]['page_access_token'] : ""; 
       $page_id= isset($subscriber_info[0]['page_id']) ? $subscriber_info[0]['page_id'] : ""; 
       $page_table_id=isset($subscriber_info[0]['id']) ? $subscriber_info[0]['id'] : "";
       $user_id= isset($subscriber_info[0]['user_id']) ? $subscriber_info[0]['user_id'] : "";

       if(empty($subscriber_info)){
       		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
       }

    	$response_raw= '{"object":"page","entry":[{"id":"'.$page_id.'","time":1605647192167,"messaging":[{"sender":{"id":"'.$subscriber_id.'"},"recipient":{"id":"'.$page_id.'"},"timestamp":1605647191972,"message":{"mid":"Webview Submit","text":"'.$date.'"}}]}]}';

    	$json_response=array("response_raw"=>$response_raw);

    	$url=base_url()."messenger_bot/webhook_callback_main";

    	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$json_response);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        echo $reply_response=curl_exec($ch);
		
		$response['error']='0';
		echo json_encode($response);


    }

    public function email_submit(){

    	$form_data=$this->input->post();
    	$subscriber_id=$form_data['subscriber_id'];
    	$email=$form_data['email'];
  
    	if($subscriber_id==""){
    		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
    	}

    	// Get subscriber information & page information 

       $where = array("where"=> array('messenger_bot_subscriber.subscribe_id'=>$subscriber_id));

       $select = array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id","facebook_rx_fb_page_info.page_id","page_access_token","first_name","last_name","messenger_bot_subscriber.status","facebook_rx_fb_page_info.id","messenger_bot_subscriber.user_id","mail_service_id","email_api_id","email_reply_message","email_reply_subject","facebook_rx_fb_page_info.page_name","sequence_sms_campaign_id","sequence_email_campaign_id");
       $join  = array("facebook_rx_fb_page_info"=>"messenger_bot_subscriber.page_table_id=facebook_rx_fb_page_info.id,left");
       $table="messenger_bot_subscriber";
       $subscriber_info = $this->basic->get_data($table,$where,$select,$join);


       $page_access_token= isset($subscriber_info[0]['page_access_token']) ? $subscriber_info[0]['page_access_token'] : ""; 
       $page_id= isset($subscriber_info[0]['page_id']) ? $subscriber_info[0]['page_id'] : ""; 
       $page_table_id=isset($subscriber_info[0]['id']) ? $subscriber_info[0]['id'] : "";
       $user_id= isset($subscriber_info[0]['user_id']) ? $subscriber_info[0]['user_id'] : "";

       if(empty($subscriber_info)){
       		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
       }

       //Update Subscribers BirthDate : 
       $update_data=array("email"=>$email);
       $update_where=array("subscribe_id"=>$subscriber_id,"page_table_id"=>$page_table_id);
       $this->basic->update_data("messenger_bot_subscriber",$update_where,$update_data);


	    // Send message to subscriber 

	  	$where=array('where'=>array('keyword_type'=>'email-quick-reply','page_id'=>$page_table_id));
		$reply_template_info=$this->basic->get_data('messenger_bot',$where);

		if(!empty($reply_template_info)) {

			$message_str = $reply_template_info[0]['message'];
		   $message_array = json_decode($message_str,true);

		   foreach($message_array as $msg)
		   {
		   	$template_type_file_track=$msg['message']['template_type'];
		   	unset($msg['message']['template_type']);

		        // typing on and typing on delay [alamin]
		   	$enable_typing_on = isset($msg['message']['typing_on_settings']) ? $msg['message']['typing_on_settings'] : "";
		   	$enable_typing_on = ($enable_typing_on=='on')  ? 1 : 0;
		   	unset($msg['message']['typing_on_settings']);
		   	$typing_on_delay_time = isset($msg['message']['delay_in_reply']) ? $msg['message']['delay_in_reply'] : "";
		   	if($typing_on_delay_time=="") $typing_on_delay_time = 0;
		   	unset($msg['message']['delay_in_reply']);

		   	$text_reply_unique_id = '';
		   	if(isset($msg['message']['text_reply_unique_id']))
		   	{
		   		$text_reply_unique_id=$msg['message']['text_reply_unique_id'];
		   		unset($msg['message']['text_reply_unique_id']);
		   	}


			      // keep the track of quick reply send for email & phone button. 
		   	$quick_replies=array();
		   	$phone_quick_reply_button_id="";
		   	$email_quick_reply_button_id="";
		   	$has_quick_reply=0;

		   	$quick_replies= $msg['message']['quick_replies'] ?? [];

		   	if(!empty($quick_replies)){

		   		$has_quick_reply=1;

		   		foreach($quick_replies as $q_index=>$q_reply){

		   			if($q_reply['content_type']=="user_phone_number"){

		   				$phone_quick_reply_button_id= $msg['message']['quick_replies'][$q_index]['unique_id'] ?? "";
		   				unset($msg['message']['quick_replies'][$q_index]['unique_id']);
		   			}

		   			else if($q_reply['content_type']=="user_email"){

		   				$email_quick_reply_button_id= $msg['message']['quick_replies'][$q_index]['unique_id'] ?? "";
		   				unset($msg['message']['quick_replies'][$q_index]['unique_id']);
		   			}

		   		}

		   	}

		   	/** Spintax **/
		   	if(isset($msg['message']['text']))
		   		$msg['message']['text']=spintax_process($msg['message']['text']);               

		   	$msg['messaging_type'] = "RESPONSE";
		   	$reply = json_encode($msg);     

		   	$replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
		   	$replace_with=array('{"id":"'.$subscriber_id.'"}',$subscriber_id);
		   	$reply=str_replace($replace_search, $replace_with, $reply);

		   	if(isset($subscriber_info[0]['first_name']))
		   		$reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
		   	if(isset($subscriber_info[0]['last_name']))
		   		$reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
		   	$access_token = $page_access_token;
		   	$is_error=0;

		   	if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){
		         // typing on and typing on delay [alamin]
		   		if($enable_typing_on) $this->sender_action($subscriber_id,"typing_on",$access_token);                                
		   		if($typing_on_delay_time>0) sleep($typing_on_delay_time);

		   		if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
		   			$post_data=array("access_token"=>$access_token,"reply"=>$reply);
		   			$url=base_url()."home/send_reply_curl_call";
		   			$ch = curl_init();
		   			curl_setopt($ch, CURLOPT_URL, $url);
		   			curl_setopt($ch,CURLOPT_POST,1);
		   			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		   			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		   			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		   			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		   			$reply_response=curl_exec($ch);  

		   		}
		   		else
		   			$reply_response=$this->send_reply($access_token,$reply);
		   		/*****Insert into database messenger_bot_reply_error_log if get error****/
		   		if(isset($reply_response['error']['message']))
		   		{
		   			$is_error=1;
		   			$bot_settings_id= $reply_template_info[0]['id'];
		   			$reply_error_message= $reply_response['error']['message'];
		   			$error_time= date("Y-m-d H:i:s");
		   			$error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
		   				"error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
		   				"error_time"=>$error_time);
		   			$this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);

		   		}
		   	}

		   	// update email & phone quick reply button id in subscriber extra table 
		   	if($has_quick_reply==1 && $is_error==0){

		   		$insert_subscriber_input_flow_assaign="INSERT INTO messenger_bot_subscriber_extra_info (subscriber_id,page_id,email_quick_reply_button_id,phone_quick_reply_button_id) 
		   		VALUES ('$subscriber_id','$page_id','$email_quick_reply_button_id','$phone_quick_reply_button_id')
		   		ON DUPLICATE KEY UPDATE  email_quick_reply_button_id='$email_quick_reply_button_id',phone_quick_reply_button_id='$phone_quick_reply_button_id'";
		   		$this->basic->execute_complex_query($insert_subscriber_input_flow_assaign);

		   	}
		   	if($text_reply_unique_id != '')
		   	{
		   		
		   		$message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$subscriber_id',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
		   		$this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
		   	}
		   }
		}

		$this->user_id=$user_id;

		if($this->addon_exist("messenger_bot_connectivity")) 
		$this->thirdparty_webhook_trigger($page_id,$subscriber_id,"trigger_email");


	    // Send to Email Auto Responder

        $email_auto_responder_id= isset($subscriber_info[0]['mail_service_id']) ? $subscriber_info[0]['mail_service_id']:"";
        $pagename= isset($subscriber_info[0]['page_name']) ? $subscriber_info[0]['page_name'] : "";
        $mailchimp_tags=array($pagename); // Page Name
        if($email_auto_responder_id!="")
            $this->send_email_to_autoresponder($email_auto_responder_id, $email,$subscriber_info[0]['first_name'],$subscriber_info[0]['last_name'],$type='quick-reply',$user_id,$mailchimp_tags);



           //Assaign Email Drip Campaign

            $sequence_email_campaign_id = isset($subscriber_info[0]['sequence_email_campaign_id']) ? $subscriber_info[0]['sequence_email_campaign_id']:"";

            if($sequence_email_campaign_id!=0){
            	$drip_type="custom";
            	 $this->assign_drip_messaging_id($drip_type,"0",$page_table_id,$subscriber_id,$sequence_email_campaign_id);  
            }


        // Send Email From System 

         $email_api_id= isset($subscriber_info[0]['email_api_id']) ? $subscriber_info[0]['email_api_id']:"";
         $email_reply_message= isset($subscriber_info[0]['email_reply_message']) ? nl2br($subscriber_info[0]['email_reply_message']):"";
         $email_reply_subject= isset($subscriber_info[0]['email_reply_subject']) ? $subscriber_info[0]['email_reply_subject']:"";

         if($email_api_id!=""){

            if(isset($subscriber_info[0]['first_name']))
                $email_reply_message=str_replace("{{user_first_name}}", $subscriber_info[0]['first_name'], $email_reply_message);
            if(isset($subscriber_info[0]['last_name']))
                $email_reply_message=str_replace("{{user_last_name}}", $subscriber_info[0]['last_name'], $email_reply_message);
           $this->send_email_by_for_bot_email($email_api_id,$email_reply_message,$email, $email_reply_subject,$user_id);



         }


		$response['error']='0';
		echo json_encode($response);


    }




    public function phone_submit(){

    	$form_data=$this->input->post();
    	$subscriber_id=$form_data['subscriber_id'];
    	$phone_number=$form_data['phone'];
  
    	if($subscriber_id==""){
    		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
    	}

    	// Get subscriber information & page information 

       $where = array("where"=> array('messenger_bot_subscriber.subscribe_id'=>$subscriber_id));
       $select = array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id","facebook_rx_fb_page_info.page_id","page_access_token","first_name","last_name","messenger_bot_subscriber.status","facebook_rx_fb_page_info.id","messenger_bot_subscriber.user_id","sms_api_id","sms_reply_message","sequence_sms_campaign_id","sequence_email_campaign_id");

       $join  = array("facebook_rx_fb_page_info"=>"messenger_bot_subscriber.page_table_id=facebook_rx_fb_page_info.id,left");
       $table="messenger_bot_subscriber";
       $subscriber_info = $this->basic->get_data($table,$where,$select,$join);

       $page_access_token= isset($subscriber_info[0]['page_access_token']) ? $subscriber_info[0]['page_access_token'] : ""; 
       $page_id= isset($subscriber_info[0]['page_id']) ? $subscriber_info[0]['page_id'] : ""; 
       $page_table_id=isset($subscriber_info[0]['id']) ? $subscriber_info[0]['id'] : "";
       $user_id= isset($subscriber_info[0]['user_id']) ? $subscriber_info[0]['user_id'] : "";

       if(empty($subscriber_info)){
       		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
       }

       //Update Subscribers BirthDate : 
       $update_data=array("phone_number"=>$phone_number);
       $update_where=array("subscribe_id"=>$subscriber_id,"page_table_id"=>$page_table_id);
       $this->basic->update_data("messenger_bot_subscriber",$update_where,$update_data);


	    // Send message to subscriber 

	  	$where=array('where'=>array('keyword_type'=>'phone-quick-reply','page_id'=>$page_table_id));
		$reply_template_info=$this->basic->get_data('messenger_bot',$where);

		if(!empty($reply_template_info)) {

			$message_str = $reply_template_info[0]['message'];
		   $message_array = json_decode($message_str,true);
		   foreach($message_array as $msg)
		   {
					$template_type_file_track=$msg['message']['template_type'];
					unset($msg['message']['template_type']);

					// typing on and typing on delay [alamin]
					$enable_typing_on = isset($msg['message']['typing_on_settings']) ? $msg['message']['typing_on_settings'] : "";
					$enable_typing_on = ($enable_typing_on=='on')  ? 1 : 0;
					unset($msg['message']['typing_on_settings']);
					$typing_on_delay_time = isset($msg['message']['delay_in_reply']) ? $msg['message']['delay_in_reply'] : "";
					if($typing_on_delay_time=="") $typing_on_delay_time = 0;
					unset($msg['message']['delay_in_reply']);

		         $text_reply_unique_id = '';
			   	if(isset($msg['message']['text_reply_unique_id']))
			   	{
			   		$text_reply_unique_id=$msg['message']['text_reply_unique_id'];
			   		unset($msg['message']['text_reply_unique_id']);
			   	}


				   // keep the track of quick reply send for email & phone button. 
			   	$quick_replies=array();
			   	$phone_quick_reply_button_id="";
			   	$email_quick_reply_button_id="";
			   	$has_quick_reply=0;

			   	$quick_replies= $msg['message']['quick_replies'] ?? [];

			   	if(!empty($quick_replies)){

			   		$has_quick_reply=1;

			   		foreach($quick_replies as $q_index=>$q_reply){

			   			if($q_reply['content_type']=="user_phone_number"){

			   				$phone_quick_reply_button_id= $msg['message']['quick_replies'][$q_index]['unique_id'] ?? "";
			   				unset($msg['message']['quick_replies'][$q_index]['unique_id']);
			   			}

			   			else if($q_reply['content_type']=="user_email"){

			   				$email_quick_reply_button_id= $msg['message']['quick_replies'][$q_index]['unique_id'] ?? "";
			   				unset($msg['message']['quick_replies'][$q_index]['unique_id']);
			   			}

			   		}

			   	}
		        
		         /** Spintax **/
		         if(isset($msg['message']['text']))
		            $msg['message']['text']=spintax_process($msg['message']['text']);               
		        
		        $msg['messaging_type'] = "RESPONSE";
		        $reply = json_encode($msg);     

		        $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
		        $replace_with=array('{"id":"'.$subscriber_id.'"}',$subscriber_id);
		        $reply=str_replace($replace_search, $replace_with, $reply);

		        if(isset($subscriber_info[0]['first_name']))
		            $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
		        if(isset($subscriber_info[0]['last_name']))
		            $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
		        $access_token = $page_access_token;
		        $is_error=0;

		        if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){
		        
		            // typing on and typing on delay [alamin]
		            if($enable_typing_on) $this->sender_action($subscriber_id,"typing_on",$access_token);                                
		            if($typing_on_delay_time>0) sleep($typing_on_delay_time);

		            if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
		                $post_data=array("access_token"=>$access_token,"reply"=>$reply);
		                $url=base_url()."home/send_reply_curl_call";
		                $ch = curl_init();
		                curl_setopt($ch, CURLOPT_URL, $url);
		                curl_setopt($ch,CURLOPT_POST,1);
		                curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		                $reply_response=curl_exec($ch);  
		        
		            }
		            else
		                 $reply_response=$this->send_reply($access_token,$reply);
		                /*****Insert into database messenger_bot_reply_error_log if get error****/
		                 if(isset($reply_response['error']['message']))
		                 {
		                 	  $is_error=1;
		                    $bot_settings_id= $reply_template_info[0]['id'];
		                    $reply_error_message= $reply_response['error']['message'];
		                    $error_time= date("Y-m-d H:i:s");
		                    $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
		                                        "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
		                                        "error_time"=>$error_time);
		                    $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
		                    
		                }
		         }

		         // update email & phone quick reply button id in subscriber extra table 
			   	if($has_quick_reply==1 && $is_error==0){

			   		$insert_subscriber_input_flow_assaign="INSERT INTO messenger_bot_subscriber_extra_info (subscriber_id,page_id,email_quick_reply_button_id,phone_quick_reply_button_id) 
			   		VALUES ('$subscriber_id','$page_id','$email_quick_reply_button_id','$phone_quick_reply_button_id')
			   		ON DUPLICATE KEY UPDATE  email_quick_reply_button_id='$email_quick_reply_button_id',phone_quick_reply_button_id='$phone_quick_reply_button_id'";
			   		$this->basic->execute_complex_query($insert_subscriber_input_flow_assaign);

			   	}
			   	if($text_reply_unique_id != '')
			   	{
			   		
			   		$message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$subscriber_id',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
			   		$this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
			   	}
		    }

		}

		$this->user_id=$user_id;

		if($this->addon_exist("messenger_bot_connectivity")) 
		$this->thirdparty_webhook_trigger($page_id,$subscriber_id,"trigger_phone_number");





        $sms_api_id= isset($subscriber_info[0]['sms_api_id']) ? $subscriber_info[0]['sms_api_id']:"";

        if($sms_api_id!="" && $sms_api_id!="0"){

	        $sms_reply_message= isset($subscriber_info[0]['sms_reply_message']) ? $subscriber_info[0]['sms_reply_message']:"";

	        if(isset($subscriber_info[0]['first_name']))
	            $sms_reply_message=str_replace("{{user_first_name}}", $subscriber_info[0]['first_name'], $sms_reply_message);
	        if(isset($subscriber_info[0]['last_name']))
	            $sms_reply_message=str_replace("{{user_last_name}}", $subscriber_info[0]['last_name'], $sms_reply_message);

	        $this->send_sms_by_for_bot_phone_number($sms_api_id,$user_id,$sms_reply_message,$phone_number);
		}


		 //Assaign SMS Drip Campaign

        $sequence_sms_campaign_id = isset($subscriber_info[0]['sequence_sms_campaign_id']) ? $subscriber_info[0]['sequence_sms_campaign_id']:"";

        if($sequence_sms_campaign_id!=0){
        	$drip_type="custom";
        	$this->assign_drip_messaging_id($drip_type,"0",$page_table_id,$subscriber_id,$sequence_sms_campaign_id);  
        }

            



		$response['error']='0';
		echo json_encode($response);


    }



    public function location_submit(){

    	$form_data=$this->input->post();
    	$subscriber_id=$form_data['subscriber_id'];
    	$street=$form_data['street'];
    	$state=$form_data['state'];
    	$country=$form_data['country'];
    	$zip=$form_data['zip'];
    	$city=$form_data['city'];

    	$address=json_encode(array("street"=>$street,"city"=>$city,"state"=>$state,"country"=>$country,"zip"=>$zip));


    	if($subscriber_id==""){
    		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
    	}

    	// Get subscriber information & page information 

       $where = array("where"=> array('messenger_bot_subscriber.subscribe_id'=>$subscriber_id));
       $select = array("facebook_rx_fb_page_info.facebook_rx_fb_user_info_id","facebook_rx_fb_page_info.page_id","page_access_token","first_name","last_name","messenger_bot_subscriber.status","facebook_rx_fb_page_info.id","messenger_bot_subscriber.user_id");
       $join  = array("facebook_rx_fb_page_info"=>"messenger_bot_subscriber.page_table_id=facebook_rx_fb_page_info.id,left");
       $table="messenger_bot_subscriber";
       $subscriber_info = $this->basic->get_data($table,$where,$select,$join);

       $page_access_token= isset($subscriber_info[0]['page_access_token']) ? $subscriber_info[0]['page_access_token'] : ""; 
       $page_id= isset($subscriber_info[0]['page_id']) ? $subscriber_info[0]['page_id'] : ""; 
       $page_table_id=isset($subscriber_info[0]['id']) ? $subscriber_info[0]['id'] : "";
       $user_id= isset($subscriber_info[0]['user_id']) ? $subscriber_info[0]['user_id'] : "";

       if(empty($subscriber_info)){
       		$response['error']='1';
    		$response['error_message']=$this->lang->line("Subscriber information not found.");
       		echo json_encode($response);
    		exit; 
       }

       //Update Subscribers BirthDate : 
       $update_data=array("user_location"=>$address);
       $update_where=array("subscribe_id"=>$subscriber_id,"page_table_id"=>$page_table_id);
       $this->basic->update_data("messenger_bot_subscriber",$update_where,$update_data);


	    // Send message to subscriber 

	  	$where=array('where'=>array('keyword_type'=>'location-quick-reply','page_id'=>$page_table_id));
		$reply_template_info=$this->basic->get_data('messenger_bot',$where);

		if(!empty($reply_template_info)) {

			$message_str = $reply_template_info[0]['message'];
		   $message_array = json_decode($message_str,true);

		   foreach($message_array as $msg)
		   {
		   	$template_type_file_track=$msg['message']['template_type'];
		   	unset($msg['message']['template_type']);

		        // typing on and typing on delay [alamin]
		   	$enable_typing_on = isset($msg['message']['typing_on_settings']) ? $msg['message']['typing_on_settings'] : "";
		   	$enable_typing_on = ($enable_typing_on=='on')  ? 1 : 0;
		   	unset($msg['message']['typing_on_settings']);
		   	$typing_on_delay_time = isset($msg['message']['delay_in_reply']) ? $msg['message']['delay_in_reply'] : "";
		   	if($typing_on_delay_time=="") $typing_on_delay_time = 0;
		   	unset($msg['message']['delay_in_reply']);

		   	$text_reply_unique_id = '';
		   	if(isset($msg['message']['text_reply_unique_id']))
		   	{
		   		$text_reply_unique_id=$msg['message']['text_reply_unique_id'];
		   		unset($msg['message']['text_reply_unique_id']);
		   	}


				   // keep the track of quick reply send for email & phone button. 
		   	$quick_replies=array();
		   	$phone_quick_reply_button_id="";
		   	$email_quick_reply_button_id="";
		   	$has_quick_reply=0;

		   	$quick_replies= $msg['message']['quick_replies'] ?? [];

		   	if(!empty($quick_replies)){

		   		$has_quick_reply=1;

		   		foreach($quick_replies as $q_index=>$q_reply){

		   			if($q_reply['content_type']=="user_phone_number"){

		   				$phone_quick_reply_button_id= $msg['message']['quick_replies'][$q_index]['unique_id'] ?? "";
		   				unset($msg['message']['quick_replies'][$q_index]['unique_id']);
		   			}

		   			else if($q_reply['content_type']=="user_email"){

		   				$email_quick_reply_button_id= $msg['message']['quick_replies'][$q_index]['unique_id'] ?? "";
		   				unset($msg['message']['quick_replies'][$q_index]['unique_id']);
		   			}

		   		}

		   	}

		   	/** Spintax **/
		   	if(isset($msg['message']['text']))
		   		$msg['message']['text']=spintax_process($msg['message']['text']);               

		   	$msg['messaging_type'] = "RESPONSE";
		   	$reply = json_encode($msg);     

		   	$replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
		   	$replace_with=array('{"id":"'.$subscriber_id.'"}',$subscriber_id);
		   	$reply=str_replace($replace_search, $replace_with, $reply);

		   	if(isset($subscriber_info[0]['first_name']))
		   		$reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
		   	if(isset($subscriber_info[0]['last_name']))
		   		$reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
		   	$access_token = $page_access_token;
		   	$is_error=0;
		   	if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){

		            // typing on and typing on delay [alamin]
		   		if($enable_typing_on) $this->sender_action($subscriber_id,"typing_on",$access_token);                                
		   		if($typing_on_delay_time>0) sleep($typing_on_delay_time);

		   		if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
		   			$post_data=array("access_token"=>$access_token,"reply"=>$reply);
		   			$url=base_url()."home/send_reply_curl_call";
		   			$ch = curl_init();
		   			curl_setopt($ch, CURLOPT_URL, $url);
		   			curl_setopt($ch,CURLOPT_POST,1);
		   			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		   			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		   			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		   			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		   			$reply_response=curl_exec($ch);  

		   		}
		   		else
		   			$reply_response=$this->send_reply($access_token,$reply);
		   		/*****Insert into database messenger_bot_reply_error_log if get error****/
		   		if(isset($reply_response['error']['message'])){
		   			$is_error=1;
		   			$bot_settings_id= $reply_template_info[0]['id'];
		   			$reply_error_message= $reply_response['error']['message'];
		   			$error_time= date("Y-m-d H:i:s");
		   			$error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
		   				"error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
		   				"error_time"=>$error_time);
		   			$this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);

		   		}
		   	}

		   	// update email & phone quick reply button id in subscriber extra table 
		   	if($has_quick_reply==1 && $is_error==0){

		   		$insert_subscriber_input_flow_assaign="INSERT INTO messenger_bot_subscriber_extra_info (subscriber_id,page_id,email_quick_reply_button_id,phone_quick_reply_button_id) 
		   		VALUES ('$subscriber_id','$page_id','$email_quick_reply_button_id','$phone_quick_reply_button_id')
		   		ON DUPLICATE KEY UPDATE  email_quick_reply_button_id='$email_quick_reply_button_id',phone_quick_reply_button_id='$phone_quick_reply_button_id'";
		   		$this->basic->execute_complex_query($insert_subscriber_input_flow_assaign);

		   	}
		   	if($text_reply_unique_id != '')
		   	{
		   		$message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$subscriber_id',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
		   		$this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
		   	}

		   }

		}

		$this->user_id=$user_id;

		if($this->addon_exist("messenger_bot_connectivity")) 
		$this->thirdparty_webhook_trigger($page_id,$subscriber_id,"trigger_location");

		$response['error']='0';
		echo json_encode($response);


    }





}