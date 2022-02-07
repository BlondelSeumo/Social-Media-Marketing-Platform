<?php
require_once("application/controllers/Home.php"); // loading home controller
class Messenger_bot extends Home
{
    public $addon_data=array();
    public $postback_info;
    public $postback_array=array();
    public $postback_done=array();
    public function __construct()
    {
        parent::__construct();
        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
        $function_name=$this->uri->segment(2);
        if($function_name!="webhook_callback" && $function_name!="webhook_callback_main" && $function_name!="update_first_name_last_name" && $function_name!="send_message_bot_reply") 
        {
             // all addon must be login protected
              //------------------------------------------------------------------------------------------
              if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');          
              // if you want the addon to be accessed by admin and member who has permission to this addon
              //-------------------------------------------------------------------------------------------
              if(isset($addondata['module_id']) && is_numeric($addondata['module_id']) && $addondata['module_id']>0)
              {
                   if($this->session->userdata('user_type') != 'Admin' && !in_array($addondata['module_id'],$this->module_access))
                   {
                        redirect('home/login_page', 'location');
                        exit();
                   }
              }

            $this->member_validity();
        } 
    }


    public function api_member_validity($user_id='')
    {
        if($user_id!='') {
            $where['where'] = array('id'=>$user_id);
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$user_id)),$select="package.price as price, users.user_type",$join=array('package'=>"users.package_id=package.id,left"));

            if(is_array($package_data) && array_key_exists(0, $package_data) && $package_data[0]['user_type'] == 'Admin' )
                return true;

            $price = '';
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;

            
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            return false;
            else return true;  
        }
    } 
  

    private function package_list()
    {
        $payment_package=$this->basic->get_data("package",$where='',$select='',$join='',$limit='',$start=NULL,$order_by='price');
        $return_val=array();
        $config_data=$this->basic->get_data("payment_config");
        $currency=isset($config_data[0]["currency"])?$config_data[0]["currency"]:"USD";
        foreach ($payment_package as $row)
        {
            $return_val[$row['id']]=$row['package_name']." : Only @".$currency." ".$row['price']." for ".$row['validity']." days";
        }
        return $return_val;
    }

    public function get_label_dropdown()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id
        $media_type = $this->input->post('hidden_media_type');
        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0","social_media"=>$media_type);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');
        $result = array();
        $group_name =array();

        $dropdown=array();
        $str='<script>$("#label_ids").select2();</script> ';
        $str .='<select multiple=""  class="form-control select2" id="label_ids" name="label_ids[]">';
        $str .= '<option value="">'.$this->lang->line('Select Labels').'</option>';
        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_type = $value['group_name'];
            $str.=  "<option value='{$search_key}'>".$search_type."</option>";            

        }
        $str.= '</select>';

        echo json_encode(array('first_dropdown'=>$str));
    }

    public function get_flow_campaign_info()
    {
        $this->ajax_check();
        $str = '';
        if($this->addon_exist("custom_field_manager"))
        {
            $page_id=$this->input->post('page_id');// database id
            $media_type = $this->input->post('hidden_media_type');
            $table_type = 'user_input_flow_campaign';
            $where_type['where'] = array('user_id'=>$this->user_id,"page_table_id"=>$page_id,"media_type"=>$media_type);
            $info_type = $this->basic->get_data($table_type,$where_type);
            
            $str = '<option value="">'.$this->lang->line('Select Flow campaign').'</option>';
            foreach ($info_type as  $value)
            {
                $id = $value['id'];
                $name = $value['flow_name'];
                $str.=  "<option value='{$id}'>".$name."</option>";            

            }
        }

        echo json_encode(array('flow_campaigns'=>$str));
    }

    public function get_drip_campaign_dropdown()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id
        $media_type = $this->input->post('hidden_media_type');
        $table_type = 'messenger_bot_drip_campaign';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"media_type"=>$media_type);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='');
        $result = array();
        $group_name =array();

        $dropdown=array();
        $str='<script>$("#drip_campaign_id").select2();</script> ';
        $str .='<select class="form-control select2" id="drip_campaign_id" name="drip_campaign_id[]">';
        $str .= '<option value="">'.$this->lang->line('Select').'</option>';
        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_value = $value['campaign_name'];
            $str.=  "<option value='{$search_key}'>".$search_value."</option>";
        }
        $str.= '</select>';

        echo json_encode(array('dropdown_value'=>$str));
    }


    public function get_postback_dropdown()
    {
        if(!$_POST) exit();
        $page_auto_id=$this->input->post('page_auto_id');// database id
        $default_child_postback_id=$this->input->post('default_child_postback_id');// this will be auto selected

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id,'template_for'=>'reply_message','is_template'=>'0','use_status'=>'0')),array('postback_id','bot_name'));

        $str='';
        if(!empty($postback_id_list))
        {            
            $str='<script>$("#template_postback_id").select2();</script> ';
            $str .='<label>'.$this->lang->line("Select PostBack ID").'</label>
                    <select class="form-control select2" id="template_postback_id" name="template_postback_id">';
            foreach ($postback_id_list as  $value)
            {
                $array_key = $value['postback_id'];
                $array_value = $value['postback_id']." (".$value['bot_name'].")";
                $selected = ($array_key==$default_child_postback_id) ? 'selected' : '';
                $str .="<option value='{$array_key}' {$selected}>{$array_value}</option>";            

            }
            $str.= '</select>';
        }

        echo json_encode(array('first_dropdown'=>$str));
    }

    public function get_postback_dropdown_child()
    {
        if(!$_POST) exit();
        $page_auto_id=$this->input->post('page_auto_id');// database id

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id,'template_for'=>'reply_message','is_template'=>'1','media_type'=>'fb')),array('postback_id','bot_name'));

        $str='';
        $str .="<option value=''>".$this->lang->line("Select")."</option>";
        if(!empty($postback_id_list))
        {            
            foreach ($postback_id_list as  $value)
            {
                $array_key = $value['postback_id'];
                $array_value = $value['postback_id']." (".$value['bot_name'].")";
                $str .="<option value='{$array_key}'>{$array_value}</option>";            

            }
        }

        echo json_encode(array('dropdown'=>$str));
    }

    public function get_ig_postback_dropdown_child()
    {
        if(!$_POST) exit();
        $page_auto_id=$this->input->post('page_auto_id');// database id

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id,'template_for'=>'reply_message','is_template'=>'1','media_type'=>'ig')),array('postback_id','bot_name'));

        $str='';
        $str .="<option value=''>".$this->lang->line("Select")."</option>";
        if(!empty($postback_id_list))
        {            
            foreach ($postback_id_list as  $value)
            {
                $array_key = $value['postback_id'];
                $array_value = $value['postback_id']." (".$value['bot_name'].")";
                $str .="<option value='{$array_key}'>{$array_value}</option>";            

            }
        }

        echo json_encode(array('dropdown'=>$str));
    }
    
    public function create_subscriber($sender_id='', $page_id='',$social_media_type="fb")
    {

        $this->db->db_debug = FALSE; //disable debugging for queries
        $table = "messenger_bot_subscriber";
        $where['where'] = array('messenger_bot_subscriber.subscribe_id' => $sender_id,"social_media"=>$social_media_type);
        $select=array("id","unavailable","subscribe_id","first_name","last_name","full_name","status","page_table_id","is_bot_subscriber","user_id","gender","email","phone_number","birthdate","last_subscriber_interaction_time",'social_media');

        $subscriber_info = $this->basic->get_data($table,$where,$select);

        $response=array();
        $response['is_new']=FALSE;
        if(!empty($subscriber_info) && $subscriber_info[0]['is_bot_subscriber']=='1'){
        	$this->user_id=$subscriber_info[0]['user_id'];
            $response['subscriber_info']=$subscriber_info[0];
            return $response; 
        }

        else{

            if(empty($subscriber_info))
                $response['is_new']=TRUE;

            $table = "facebook_rx_fb_page_info";
            $where['where'] = array('page_id' => $page_id,'bot_enabled'=>'1');
            $page_access_token_array = $this->basic->get_data($table,$where,"page_access_token,user_id,id");
            $page_access_token = $page_access_token_array[0]['page_access_token'];
            $user_id = $page_access_token_array[0]['user_id'];
            $page_table_id = $page_access_token_array[0]['id'];
            $this->user_id=$user_id; // for using in webhook thridparty json trigger function in query 

            // subscriber count check
            $package_info = $this->basic->get_data("users",array('where'=>array('users.id'=>$user_id)),array('package_id','user_type','module_ids','monthly_limit'),array('package'=>"package.id=users.package_id,left"));
            if(!isset($package_info[0])) exit();
            $module_ids = explode(',', $package_info[0]['module_ids']);
            $monthly_limit = json_decode($package_info[0]['monthly_limit'],true);
            $subscriber_limit = $monthly_limit[66] ?? 0;
            // if($package_info[0]['package_id']>0 && in_array(66, $module_ids) && $subscriber_limit>0){
            if($package_info[0]['package_id']>0 && $package_info[0]['user_type']!='Admin' && in_array(66, $module_ids) && $subscriber_limit>0){                
                $total_rows_array = $this->basic->count_row("messenger_bot_subscriber",array('where'=>array('page_id'=>$page_id,'subscriber_type !='=>'system')));
                $total_result=$total_rows_array[0]['total_rows'] ?? 0;
                if($total_result>=$subscriber_limit){
                    if($social_media_type=='') $social_media_type = 'fb';
                    $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,"error_message"=>$this->lang->line("Bot subscriber limit for this page has been exceeded. Bot cannot have more subscribers. Please contact your system admin."),"error_time"=>date("Y-m-d H:i:s"),"media_type"=>$social_media_type);
                    $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                    exit();
                }
            }

            $user_data = $this->subscriber_info($page_access_token,$sender_id,$social_media_type);
            $this->db->db_debug = FALSE; //disable debugging for queries

            //Insert or update subscriber information 

                $subscribe_id = $sender_id;
                $first_name = isset($user_data['first_name']) ? $this->db->escape($user_data['first_name']):"";
                $last_name = isset($user_data['last_name']) ? $this->db->escape($user_data['last_name']):"";
                $profile_pic = isset($user_data['profile_pic']) ? $this->db->escape($user_data['profile_pic']):$this->db->escape("");
                $locale = isset($user_data['locale']) ? $user_data['locale']:"";
                $timezone = isset($user_data['timezone']) ? $user_data['timezone']:"";
                $gender = isset($user_data['gender']) ? $user_data['gender']:"";
                $subscribed_at = date('Y-m-d H:i:s');
                $page_table_id=$page_access_token_array[0]['id'];

                $full_name= isset($user_data['name']) ? $this->db->escape($user_data['name']):""; // for instagram 

                if($social_media_type=="fb")
                $sql="INSERT INTO messenger_bot_subscriber (user_id,page_id,page_table_id,subscribe_id,first_name,last_name,profile_pic,locale,timezone,gender,subscribed_at,is_imported,is_bot_subscriber) 
                VALUES ('$user_id','$page_id','$page_table_id','$subscribe_id',$first_name,$last_name,$profile_pic,'$locale','$timezone','$gender','$subscribed_at','0','1')
                ON DUPLICATE KEY UPDATE first_name=$first_name,last_name=$last_name,profile_pic=$profile_pic,locale='$locale',timezone='$timezone',gender='$gender',is_bot_subscriber='1'; ";

                else{

                $sql= "INSERT INTO messenger_bot_subscriber (user_id,page_id,page_table_id,subscribe_id,full_name,profile_pic,subscribed_at,is_imported,is_bot_subscriber,social_media) 
                VALUES ('$user_id','$page_id','$page_table_id','$subscribe_id',$full_name,$profile_pic,'$subscribed_at','0','1','$social_media_type')
                ON DUPLICATE KEY UPDATE profile_pic=$profile_pic,is_bot_subscriber='1',full_name=$full_name; ";

                }

                $this->basic->execute_complex_query($sql);

                $last_insert_id=$this->db->insert_id();

                if($last_insert_id=='' || $last_insert_id==0)
                    $last_insert_id=$subscriber_info[0]['id'];


                $data = array(
                    'id'=>$last_insert_id,    // Get the table id of the subscriber for assigning drip campaing later. 
                    'user_id' => $user_id,
                    'page_id' => $page_id,
                    'subscribe_id' => $sender_id,
                    'first_name' => $user_data['first_name'] ?? "",
                    'last_name' => $user_data['last_name'] ?? "",
                    'profile_pic' => $profile_pic,
                    'locale' => $user_data['locale'] ?? "",
                    'timezone' => $user_data['timezone'] ?? "",
                    'gender' => $user_data['gender'] ?? "",
                    'subscribed_at' => date('Y-m-d H:i:s'),
                    'status' =>'1',
                    'page_table_id'=>$page_table_id,
                    'full_name' => $user_data['name'] ?? "",
                    'social_media'=>$social_media_type
                );

                $response['subscriber_info']=$data;


               return $response;

        }

        
    }
        

    public function is_email($email)
    {
        $email=trim($email);
        $is_valid=0;
        /***Validation check***/
        $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        if (preg_match($pattern, $email) === 1) {
            $is_valid=1;
        }
        return $is_valid;
    }

    public function is_phone_number($phone)
    {    
        $is_valid=0;
        if(preg_match("#\+\d{7}#",$phone)===1)
            $is_valid=1; 
            
        return $is_valid;
            
    }


    public function phone_info_auto_responder($page_id,$subscriber_info,$phone_number){

        $user_id= isset($subscriber_info[0]['user_id']) ? $subscriber_info[0]['user_id']:"";
        $sender_id= isset($subscriber_info[0]['subscribe_id']) ? $subscriber_info[0]['subscribe_id']:"";
        $payload_id=$phone_number; // Assaign email in payload_id variable as it was using like this in Bot Webhook . 

        $table_name = "messenger_bot";

        $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'facebook_rx_fb_page_info.bot_enabled' => '1',"keyword_type"=>"phone-quick-reply");
        $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

        $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time","facebook_rx_fb_page_info.mail_service_id as mail_service_id","facebook_rx_fb_page_info.sms_api_id as sms_api_id","facebook_rx_fb_page_info.sms_reply_message as sms_reply_message","email_api_id","email_reply_message","email_reply_subject","page_name","sequence_sms_campaign_id","sequence_email_campaign_id"),$join,'','','messenger_bot.id asc');

        $sms_api_id= isset($messenger_bot_info[0]['sms_api_id']) ? $messenger_bot_info[0]['sms_api_id']:"";
        $sms_reply_message= isset($messenger_bot_info[0]['sms_reply_message']) ? $messenger_bot_info[0]['sms_reply_message']:"";

        if(isset($subscriber_info[0]['first_name']))
            $sms_reply_message=str_replace("{{user_first_name}}", $subscriber_info[0]['first_name'], $sms_reply_message);
        if(isset($subscriber_info[0]['last_name']))
            $sms_reply_message=str_replace("{{user_last_name}}", $subscriber_info[0]['last_name'], $sms_reply_message);

        $this->send_sms_by_for_bot_phone_number($sms_api_id,$user_id,$sms_reply_message,$payload_id);

        //Assaign SMS Drip Campaign
            
        $sequence_sms_campaign_id = isset($messenger_bot_info[0]['sequence_sms_campaign_id']) ? $messenger_bot_info[0]['sequence_sms_campaign_id']:"";

        if($sequence_sms_campaign_id!=0){
            $drip_type="custom";
            $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id,$sequence_sms_campaign_id);  
        }
    }


    public function email_info_send_auto_responder($page_id,$subscriber_info,$email){

        $user_id= isset($subscriber_info[0]['user_id']) ? $subscriber_info[0]['user_id']:"";
        $sender_id= isset($subscriber_info[0]['subscribe_id']) ? $subscriber_info[0]['subscribe_id']:"";
        $payload_id=$email; // Assaign email in payload_id variable as it was using like this in Bot Webhook . 

        $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'facebook_rx_fb_page_info.bot_enabled' => '1',"keyword_type"=>"email-quick-reply");

        $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

        $table_name = "messenger_bot";

        $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time","facebook_rx_fb_page_info.mail_service_id as mail_service_id","facebook_rx_fb_page_info.sms_api_id as sms_api_id","facebook_rx_fb_page_info.sms_reply_message as sms_reply_message","email_api_id","email_reply_message","email_reply_subject","page_name","sequence_sms_campaign_id","sequence_email_campaign_id"),$join,'','','messenger_bot.id asc');

        // Send to Email Auto Responder

        $email_auto_responder_id= isset($messenger_bot_info[0]['mail_service_id']) ? $messenger_bot_info[0]['mail_service_id']:"";
        $pagename= isset($messenger_bot_info[0]['page_name']) ? $messenger_bot_info[0]['page_name'] : "";
        $mailchimp_tags=array($pagename); // Page Name
        if($email_auto_responder_id!="")
            $this->send_email_to_autoresponder($email_auto_responder_id, $payload_id,$subscriber_info[0]['first_name'],$subscriber_info[0]['last_name'],$type='quick-reply',$user_id,$mailchimp_tags);

        //Assaign Email Drip Campaign

        $sequence_email_campaign_id = isset($messenger_bot_info[0]['sequence_email_campaign_id']) ? $messenger_bot_info[0]['sequence_email_campaign_id']:"";

        if($sequence_email_campaign_id!=0){
            $drip_type="custom";
             $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id,$sequence_email_campaign_id);  
        }

        // Send Email From System 

        $email_api_id= isset($messenger_bot_info[0]['email_api_id']) ? $messenger_bot_info[0]['email_api_id']:"";
        $email_reply_message= isset($messenger_bot_info[0]['email_reply_message']) ? nl2br($messenger_bot_info[0]['email_reply_message']):"";
        $email_reply_subject= isset($messenger_bot_info[0]['email_reply_subject']) ? $messenger_bot_info[0]['email_reply_subject']:"";

        if($email_api_id!=""){

            if(isset($subscriber_info[0]['first_name']))
                $email_reply_message=str_replace("{{user_first_name}}", $subscriber_info[0]['first_name'], $email_reply_message);
            if(isset($subscriber_info[0]['last_name']))
                $email_reply_message=str_replace("{{user_last_name}}", $subscriber_info[0]['last_name'], $email_reply_message);
            $this->send_email_by_for_bot_email($email_api_id,$email_reply_message,$payload_id, $email_reply_subject,$user_id);

        }
    }




    // Format : Y-m-d H:i:s  , Date : 2012-09-12 12:12:00

    public function is_date_format($format,$date){

        if (DateTime::createFromFormat($format, $date) !== FALSE) {
            return true; 
        }

        else
            return false;
    }


    public function user_input_flow_assaign_check($sender_id,$page_id,$subscriber_info,$answer="",$fb_message_id="",$message_type=""){

        $input_flow_campaign_info=$this->basic->get_data("messenger_bot_subscriber_extra_info",array('where'=>array("subscriber_id"=>$sender_id,"page_id"=>$page_id)));

        if(isset($input_flow_campaign_info[0]) && $input_flow_campaign_info[0]['input_flow_campaign_id']>0){
            $flow_campaign_id=$input_flow_campaign_info[0]['input_flow_campaign_id'];
            $question_id=$input_flow_campaign_info[0]['last_question_sent_id'];
            $this->process_user_input($flow_campaign_id,$question_id,$sender_id,$subscriber_info,$page_id,$answer,$fb_message_id,$message_type); exit; 
        }
    }

    public function process_user_input($flow_campaign_id,$question_id,$sender_id,$subscriber_info,$page_id,$answer="",$fb_message_id="",$message_type=""){

        $value['message']='{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"User_Input_Flow","flow_campaign_id":"'.$flow_campaign_id.'","last_sent_question_id":"'.$question_id.'","typing_on_settings":"off","delay_in_reply":0}}}';

        // Get the access token of the page. 
         $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
         $page_info= $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('page_id' => $page_id,'bot_enabled'=>'1')),array('page_access_token','facebook_rx_config_id','facebook_rx_fb_page_info.id','facebook_rx_fb_page_info.user_id'),$join);

        // If answer is empty, then probability is file or image or video or image. Get the attachment from message_id
        
         $value['page_access_token']=$page_info[0]['page_access_token'];
         $value['id']=0;
         $value['page_id']=$page_info[0]['id'];
         $value['user_id']=$page_info[0]['user_id'];

        if($answer==""){

            $this->load->library("fb_rx_login");         
            $this->fb_rx_login->app_initialize($page_info[0]['facebook_rx_config_id']);

            $message_info=$this->fb_rx_login->get_message_attachment_info($fb_message_id,$value['page_access_token']);

            // Image 
            if(isset($message_info[0]['image_data'])){
                $answer=$message_info[0]['image_data']['url'];
                $message_type="image";
            }

            // File 
            else if(isset($message_info[0]['file_url'])){
                $answer=$message_info[0]['file_url'];
                $message_type="file";
            }
        }

        $this->send_message_bot_reply($value,$sender_id,$subscriber_info,$page_id,$user_reference_id="",$answer,$fb_message_id,$message_type);
    }


    public function get_user_input_flow_next_question($flow_campaign_id,$latest_question_id=0,$page_id=0,$subscriber_info=array(),$answer="",$fb_message_id="",$message_type=""){


        $social_media_type=$subscriber_info[0]['media_type'] ?? "";


        // Get the serial of the latest_question from latest_question_id 

        // if first question, the serial is 0 , means next question start with first. 
        if($latest_question_id==0){
            $latest_question_serial=0; 
        }
        else{

            $question_serial_info = $this->basic->get_data('user_input_flow_questions',array('where'=>array('id'=>$latest_question_id)));
            $latest_question_serial=isset($question_serial_info[0]['serial_no']) ? $question_serial_info[0]['serial_no']:0;
        }
        
        $join = array('user_input_flow_campaign'=>"user_input_flow_campaign.id=user_input_flow_questions.flow_campaign_id,right");
        $flow_campaign_questions = $this->basic->get_data('user_input_flow_questions',array('where'=>array('flow_campaign_id'=>$flow_campaign_id,'serial_no>='=>$latest_question_serial,'user_input_flow_campaign.page_table_id'=>$subscriber_info[0]['page_table_id'])),$select=array("user_input_flow_questions.*"),$join,$limit='2',$start=NULL,$order_by='serial_no ASC');

        // if there is no user input flow information, then exit. It may happen by importing bot from another page but the flow id remain same, but not have on the new imported page. 

        if(empty($flow_campaign_questions)) exit; 



        if($latest_question_id==0)
            $next_question_index=0;
        else
            $next_question_index=1;

        /* Next question Information */
        $question_text=isset($flow_campaign_questions[$next_question_index]['question']) ? $flow_campaign_questions[$next_question_index]['question']:"";
        $question_type= isset($flow_campaign_questions[$next_question_index]['type']) ? $flow_campaign_questions[$next_question_index]['type']:"";
        $question_multiple_options= isset($flow_campaign_questions[$next_question_index]['multiple_choice_options']) ? $flow_campaign_questions[$next_question_index]['multiple_choice_options']:"";
        $question_id= isset($flow_campaign_questions[$next_question_index]['id']) ? $flow_campaign_questions[$next_question_index]['id']:"";
        $question_unique_id = isset($flow_campaign_questions[$next_question_index]['unique_id']) ? $flow_campaign_questions[$next_question_index]['unique_id']:"";

        $quick_reply_email= isset($flow_campaign_questions[$next_question_index]['quick_reply_email']) ? $flow_campaign_questions[$next_question_index]['quick_reply_email']:"";
        $quick_reply_phone= isset($flow_campaign_questions[$next_question_index]['quick_reply_phone']) ? $flow_campaign_questions[$next_question_index]['quick_reply_phone']:"";
        $next_question_skip_button_text= isset($flow_campaign_questions[$next_question_index]['skip_button_text']) ? $flow_campaign_questions[$next_question_index]['skip_button_text']:"";
        $next_question_reply_type= isset($flow_campaign_questions[$next_question_index]['reply_type']) ? $flow_campaign_questions[$next_question_index]['reply_type']:"";

        /*Current question information to process data */
        $custom_field_id= isset($flow_campaign_questions[0]['custom_field_id']) ? $flow_campaign_questions[0]['custom_field_id']:"";
        $label_ids= isset($flow_campaign_questions[0]['label_ids']) ? $flow_campaign_questions[0]['label_ids']:"";
        $messenger_sequence_id= isset($flow_campaign_questions[0]['messenger_sequence_id']) ? $flow_campaign_questions[0]['messenger_sequence_id']:"";
        $email_phone_sequence_id= isset($flow_campaign_questions[0]['email_phone_sequence_id']) ? $flow_campaign_questions[0]['email_phone_sequence_id']:"";
        $system_field= isset($flow_campaign_questions[0]['system_field']) ? $flow_campaign_questions[0]['system_field']:"";
        $skip_button_text= isset($flow_campaign_questions[0]['skip_button_text']) ? $flow_campaign_questions[0]['skip_button_text']:"";
        $reply_type= isset($flow_campaign_questions[0]['reply_type']) ? $flow_campaign_questions[0]['reply_type']:"";

        // check the answer is in correct format or not
        $invalid_answer=false;
        if($answer!=""){

            if($reply_type=="Text"){
                $invalid_answer=false; 
            }

            else if($reply_type=="Email"){
                if(!$this->is_email($answer))
                    $invalid_answer=true; 
            }

            else if ($reply_type=="File"){
                if($message_type!='file')
                    $invalid_answer=true;
            }

            else if ($reply_type=="Image"){
                if($message_type!='image')
                     $invalid_answer=true;
            }

            else if ($reply_type=="Video"){
                if($message_type!='video')
                    $invalid_answer=true;
            }

            else if($reply_type=="Date"){
                if(!$this->is_date_format("Y-m-d",$answer))
                    $invalid_answer=true;
            }

            else if($reply_type=="Time"){
                if(!$this->is_date_format("H:i",$answer))
                    $invalid_answer=true;
            }

            else if($reply_type=="Datetime"){
                if(!$this->is_date_format("Y-m-d H:i",$answer))
                    $invalid_answer=true;
            }


            else if($reply_type=="Number"){
                if(!is_numeric($answer))
                    $invalid_answer=true;
            }

        }

        if($answer!="" && $answer==$skip_button_text) $invalid_answer=false;

        

        // Process answer if it's not the start of the flow && answer is in correct formation . 
        if($latest_question_id!=0 && $invalid_answer==false){

            $sender_id=$subscriber_info[0]['subscribe_id'];


            // Assaign Labels 
            $this->multiple_assign_label($sender_id,$page_id,$label_ids,$social_media_type,$subscriber_info[0]['id']);

            // Assaign Message Sequence  
            $drip_type="custom";
            $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id,$messenger_sequence_id);  

            // Assaign Email/Sms Sequence 
            $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id,$email_phone_sequence_id);  

            // Insert Answer of Question 

            $now_time= date("Y-m-d H:i:s");
            $insert_question_answer="INSERT INTO user_input_flow_questions_answer 
                (subscriber_id,page_id,flow_campaign_id,question_id,user_answer,fb_message_id,answer_time) 
                VALUES ('$sender_id','$page_id',$flow_campaign_id,$latest_question_id,'$answer','$fb_message_id','$now_time')
                ON DUPLICATE KEY UPDATE  user_answer='$answer',fb_message_id='$fb_message_id',answer_time='$now_time'";
            $this->basic->execute_complex_query($insert_question_answer);

            // Assaign Custom Field of subscribers. 
            if($custom_field_id!='' && $custom_field_id!=0)
            {
                $sql="INSERT INTO user_input_custom_fields_assaign 
                    (subscriber_id,page_id,custom_field_id,custom_field_value,assaign_time) 
                    VALUES ('$sender_id','$page_id','$custom_field_id','$answer','$now_time')
                    ON DUPLICATE KEY UPDATE  custom_field_value='$answer',assaign_time='$now_time'";
                $this->basic->execute_complex_query($sql);
            }

            // Insert System field in subscriber table 

            if($system_field!=""){

                if($system_field=="email")
                    $update_data=array("email"=>$answer);

                else if($system_field=="location"){
                	$location_info=array("country"=>"","city"=>"","state"=>"","street"=>$answer,"zip"=>"");
                	$location_info=json_encode($location_info);
                    $update_data=array("user_location"=>$location_info);
                }

                else if($system_field=="birthday")
                    $update_data=array("birthdate"=>$answer);

                else if($system_field=="phone")
                    $update_data=array("phone_number"=>$answer);

                $this->basic->update_data("messenger_bot_subscriber",array("subscribe_id"=>$sender_id,"page_id"=>$page_id),$update_data);
            }

        }

        // Set the next question if the answer is valid or it's the very first question of the flow. 
         $reply_message['question_id'] = $question_id;
         $reply_message['reply_type'] = $reply_type; // This for getting Email & Phone type to process auto responder step 
         $reply_message['question_unique_id'] = $question_unique_id;

        if($invalid_answer==false || $latest_question_id==0){
        
           if($next_question_reply_type=="Date" || $next_question_reply_type=="Time" || $next_question_reply_type=="Datetime"){

                $button_title="Select {$next_question_reply_type}";
                $next_question_reply_type=strtolower($next_question_reply_type);

                $date_calender_url=base_url("webview_builder/get_date_user_input")."?type={$next_question_reply_type}&subscriber_id=#SUBSCRIBER_ID_REPLACE#";
                $reply_message['attachment'] = 
                    array 
                      (
                        'type' => 'template',
                        'payload' => 
                        array 
                        (
                          'template_type' => 'button',
                          'text' => $question_text,
                          'buttons'=> array(
                            0=>array(
                                "type"=>"web_url",
                                "url"=>$date_calender_url,
                                "title"=>$this->lang->line($button_title),
                                "messenger_extensions" => 'true',
                                "webview_height_ratio" => 'full'
                                )                       
                            )
                      )
                    ); 
           }

           else{

            $reply_message['text'] = $question_text;

            // get the postback id for the last reply if the $question_id is empty means the no question left. 
            if($question_id==""){
                $flow_campaign_info = $this->basic->get_data("user_input_flow_campaign",array("where"=>array("id"=>$flow_campaign_id)));
                $reply_message['postback_id'] = $flow_campaign_info[0]['postback_id'];
            }

            if($question_type=='quick replies'){
                $multiple_choice_array=explode(",", $question_multiple_options);
                foreach ($multiple_choice_array as $option) {
                   $reply_message['quick_replies'][] = array("content_type"=>"text","title"=>$option,"payload"=>$option);
                }
            }

            if($quick_reply_email!="" && $next_question_reply_type=="Email"){
                $reply_message['quick_replies'][] = array("content_type"=>"user_email");
            }

            if($quick_reply_phone!="" && $next_question_reply_type=="Phone"){
                $reply_message['quick_replies'][] = array("content_type"=>"user_phone_number");
            }

            // Add skip button as quick reply if it set to add in the question. 
            if($next_question_skip_button_text!=""){
                $reply_message['quick_replies'][] = array("content_type"=>"text","title"=>$next_question_skip_button_text,"payload"=>$next_question_skip_button_text);
            }

        }

         
        }

        if($invalid_answer==true){
             $reply_message['text'] = "Wrong Format.Please send correct {$reply_type}";
             $reply_message['invalid_answer'] = true;
        }

         return $reply_message;
    }


    public function check_all_condition($rules,$condition_type,$subscriber_info,$custom_field_info){

        if(empty($rules)) return true;


        foreach ($rules as $key => $value) {

            $operator=$value['operator'];
            $variable=$value['variable'];
            
            if(!$variable) continue;



            // for custom field, variable are assigned as custom_fieldId, So separting the actual id only to match from $custom_field_info variable, as id are index
            if(preg_match("#^custom_#i", $variable) === 1){
                $variable = substr($variable, strpos($variable, "_") + 1); 
                $variable_value=$custom_field_info[$variable] ?? "";   
            }
            // If contact_group_id means label, then matching will be happen by only in_array search 
            else if($variable=="contact_group_id"){
                $operator="in_array";
                $subscriber_labels = $this->basic->get_data('messenger_bot_subscribers_label',['where'=>['subscriber_table_id'=>$subscriber_info['id']]],["GROUP_CONCAT(DISTINCT contact_group_id separator ',') as contact_group_id"],[],'','','','messenger_bot_subscriber.id');
                
                $variable_value=$subscriber_labels[0][$variable] ?? ""; 
                $variable_value=explode(",", $variable_value);
                $variable_value = array_map('trim', $variable_value);
            }

            else{
                $variable_value=$subscriber_info[$variable] ?? "";
            }

            $value=$value['value'];

            $result=condition_check($variable_value,$value,$operator);

            if($condition_type=="or"){

                $final_result=isset($final_result) ? ($final_result || $result) : $result;
                if($final_result) return $final_result; // if 1 at anytime, return
            }
            else{
                $final_result=isset($final_result) ? ($final_result && $result) : $result;
                if(!$final_result) return $final_result; // If 0 at any time, return
            }
        }
        return $final_result;
}



    //$refference_id is passed for Checkbox plugin only 
    public function send_message_bot_reply($value='',$sender_id='',$subscriber_info=[],$page_id='',$user_reference_id="",$answer="",$fb_message_id="",$message_type=""){

        // Extract varaiable with POST method for Live Chat curl call of this function 

        $reply_error_message = '';
        $return = false;

        if(empty($value) || empty($sender_id) || empty($subscriber_info) || empty($page_id)){
            
            $post_data = $_POST;
            $value = isset($post_data['value']) ? json_decode($post_data['value'],true) : [];
            $sender_id = $post_data['sender_id'] ?? '';
            $subscriber_info =  isset($post_data['subscriber_info']) ? json_decode($post_data['subscriber_info'],true) : [];
            $page_id = $post_data['page_id'] ?? '';
            $return = true;
        }

        // find the social media type 
        $social_media_type=$subscriber_info[0]['social_media'] ?? "fb";
        

        $message_str = $value['message'];

        $conditions=$value['conditions'] ?? "";
        $message_condition_false=$value['message_condition_false'] ?? "";

        if($conditions!="") {
            $conditions=json_decode($conditions,true);
            if(!empty($conditions)){
                // get custom field information
                $custom_fields=$this->basic->get_data("user_input_custom_fields_assaign",array("where"=>array("subscriber_id"=>$subscriber_info[0]['subscribe_id'])));
                $customer_field_information=array();
                foreach($custom_fields as $custom_fields_single){
                    $customer_field_information[$custom_fields_single['custom_field_id']] = $custom_fields_single['custom_field_value'];
                }

                $rules=$conditions['rules'] ?? "";
                $condition_type=$conditions['type'] ?? "";
                $is_condition=$this->check_all_condition($rules,$condition_type,$subscriber_info[0],$customer_field_information);

                if(!$is_condition)
                    $message_str = $message_condition_false;
            }
        }

        
        $message_array = json_decode($message_str,true);
        // if(!isset($message_array[1])) $message_array[1]=$message_array;
        if(!isset($message_array[1])){
            $message_array_org=$message_array;
            $message_array=array();
            $message_array[1]=$message_array_org; 
        }
        foreach($message_array as $msg)
        {
            $template_type_file_track=$msg['message']['template_type'];
            unset($msg['message']['template_type']);

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



            // typing on and typing on delay [alamin]
            $enable_typing_on = $msg['message']['typing_on_settings'];
            $enable_typing_on = ($enable_typing_on=='on')  ? 1 : 0;
            unset($msg['message']['typing_on_settings']);
            $typing_on_delay_time = $msg['message']['delay_in_reply'];
            if($typing_on_delay_time=="") $typing_on_delay_time = 0;
            unset($msg['message']['delay_in_reply']);

            // if it's unser_input_flow , then get the question from flow question table. 

            if($template_type_file_track=='User_Input_Flow'){

                $flow_campaign_id= $msg['message']['flow_campaign_id'];
                $last_sent_question_id= isset($msg['message']['last_sent_question_id']) ? $msg['message']['last_sent_question_id']: 0; 
                $msg['message'] = $this->get_user_input_flow_next_question($flow_campaign_id,$last_sent_question_id,$page_id,$subscriber_info,$answer,$fb_message_id,$message_type);
                $question_id= $msg['message']['question_id'];
                unset($msg['message']['question_id']);

                $text_reply_unique_id = $msg['message']['question_unique_id'] ?? '';
                unset($msg['message']['question_unique_id']);
                
                $invalid_answer= isset($msg['message']['invalid_answer']) ? $msg['message']['invalid_answer']:false;
                unset($msg['message']['invalid_answer']);

                $reply_type= isset($msg['message']['reply_type']) ? $msg['message']['reply_type']:"";
                unset($msg['message']['reply_type']);

                // If there is no question, mean that was the last question, now need to send the final reply from Postback selection in the flow 
                if($question_id=="" && $invalid_answer==false){

                    $postback_id=$msg['message']['postback_id'];
                    unset($msg['message']['postback_id']);

                    $where['where'] = array('messenger_bot.fb_page_id' => $page_id,"postback_id"=>$postback_id,'facebook_rx_fb_page_info.bot_enabled' => '1');
                    $table_name = "messenger_bot";
                    $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   
                    $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
                    $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id,$user_reference_id="");

                    // Update messenger_bot_subscriber_extra_info table by completing the input flow by assaigning Zero. 

                    $update_data=array("input_flow_campaign_id"=>0,"last_question_sent_id"=>0);
                    $this->basic->update_data("messenger_bot_subscriber_extra_info",array("subscriber_id"=>$sender_id,"page_id"=>$page_id),$update_data);

                    // trigger Webhook JSON API 


                

                    // Grab the all answer from answer table of this user input flow campaign. 
                    $join = array('user_input_flow_campaign'=>"user_input_flow_campaign.id=user_input_flow_questions_answer.flow_campaign_id,right",'user_input_flow_questions'=>"user_input_flow_questions.id=user_input_flow_questions_answer.question_id,right"); 

                    $answer_info=$this->basic->get_data('user_input_flow_questions_answer',array("where"=>array("subscriber_id"=>$sender_id,"page_id"=>$page_id,"user_input_flow_questions_answer.flow_campaign_id"=>$flow_campaign_id)),"",$join);

                    $k=0;
                    foreach ($answer_info as $key => $value) {

                        $form_data[$k]['question']=$value['question'];
                        $form_data[$k]['answer']=$value['user_answer'];
                        $k++;
                    }

                    if($this->addon_exist("messenger_bot_connectivity")){
                        
                        $this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_userinput","",$flow_campaign_id,$form_data);
                    }


                    $product_short_name = $this->config->item('product_short_name');
                    $from = $this->config->item('institute_email');
                    $mask = $this->config->item('product_name');
                    $where = array();
                    $user_id=$messenger_bot_info[0]['user_id'];
                    $where['where'] = array('id'=>$user_id);
                    $user_email = $this->basic->get_data('users',$where,$select='');
                    $subscriber_name = $subscriber_info[0]['first_name']; 
                    $form_title= $answer_info[0]['flow_name'];

                    $form_submit_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'input_flow_submission')),array('subject','message'));

                    if(isset($form_submit_email_template[0]) && $form_submit_email_template[0]['subject'] != '' && $form_submit_email_template[0]['message'] != '') {

                        $to = $user_email[0]['email'];
                        $url = base_url();

                         $subject = str_replace(array('#APP_NAME#','#APP_URL#','#SUBSCRIBER_NAME#','#FLOW_NAME#'),array($mask,$url,$subscriber_name,$form_title),$form_submit_email_template[0]['subject']);

                        $form_data_json=json_encode($form_data);
                        
                         $message = str_replace(array('#APP_NAME#','#APP_URL#','#SUBSCRIBER_NAME#','#FLOW_NAME#','#FLOW_DATA#'),array($mask,$url,$subscriber_name,$form_title,$form_data_json),$form_submit_email_template[0]['message']);

                        //send mail to user
                        @$this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
                    }

                    // Process Email Auto Responder 
                    if($reply_type=="Email" && $answer!=""){
                        $this->email_info_send_auto_responder($page_id,$subscriber_info,$answer);
                    }

                     // Process Phone Auto Responder 
                    if($reply_type=="Phone" && $answer!=""){
                        $this->phone_info_auto_responder($page_id,$subscriber_info,$answer);
                    }

                    exit;
                }
            }
            
            /** Spintax **/
            if(isset($msg['message']['text']))
                $msg['message']['text']=spintax_process($msg['message']['text']);
                
            $msg['messaging_type'] = "RESPONSE";
            $reply = json_encode($msg);     

            if($user_reference_id==""){
                $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                $replace_with=array('{"id":"'.$sender_id.'"}',$sender_id);
                $reply=str_replace($replace_search, $replace_with, $reply);
                $sent_to=$sender_id;
            }

            else{

                $reply=str_replace('{"id":"replace_id"}', '{"user_ref":"'.$user_reference_id.'"}', $reply);
                $sent_to=$user_reference_id;
            }

            //get custom fields & value & replace with the actual value. 
            $join = array('user_input_custom_fields'=>"user_input_custom_fields.id=user_input_custom_fields_assaign.custom_field_id,left");   
            $custom_field_info= $this->basic->get_data("user_input_custom_fields_assaign",array("where"=>array("subscriber_id"=>$sender_id,"page_id"=>$page_id)),"",$join);
            $custom_replace_search=array();
            $custom_replace_with=array();
            foreach($custom_field_info as $variable){
                $custom_replace_search[]="#".$variable['name']."#";
                $custom_replace_with[]=$variable['custom_field_value'];
             }

            $reply=str_replace($custom_replace_search, $custom_replace_with, $reply);


            if(isset($subscriber_info[0]['first_name']))
                $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
            if(isset($subscriber_info[0]['last_name']))
                $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
            if(isset($subscriber_info[0]['full_name']))
                $reply=str_replace('#LEAD_FULL_NAME#', $subscriber_info[0]['full_name'], $reply);


            $access_token = $value['page_access_token'];

            if((isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1") || $user_reference_id!="")
            {
                // typing on and typing on delay [alamin]
                if($enable_typing_on && $user_reference_id=="") $this->sender_action($sender_id,"typing_on",$access_token);                                
                if($typing_on_delay_time>0 && $user_reference_id =="") sleep($typing_on_delay_time);

                $reply_response= $this->send_reply($access_token,$reply);
             
             /*****Insert into database messenger_bot_reply_error_log if get error****/
             if(isset($reply_response['error']['message'])){

                $is_error=1;
                $bot_settings_id= $value['id'];
                $reply_error_message= $reply_response['error']['message'];
                $error_time= date("Y-m-d H:i:s");
                $page_table_id=$value['page_id'];
                $user_id=$value['user_id'];
                
                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                    "error_time"=>$error_time);

                if($social_media_type=='ig')
                    $error_insert_data['media_type']="ig";
                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                
             }

             else{ // if not any error 

                $is_error=0;

                // update input flow start information. 

                if($template_type_file_track=='User_Input_Flow' && $invalid_answer==false)
                {

                    $now_time= date("Y-m-d H:i:s");
                    $insert_subscriber_input_flow_assaign="INSERT INTO messenger_bot_subscriber_extra_info (subscriber_id,page_id,input_flow_campaign_id,last_question_sent_id,last_question_sent_time) 
                      VALUES ('$sender_id','$page_id',$flow_campaign_id,$question_id,'$now_time')
                      ON DUPLICATE KEY UPDATE  input_flow_campaign_id='$flow_campaign_id',last_question_sent_id='$question_id',last_question_sent_time='$now_time'";
                    $this->basic->execute_complex_query($insert_subscriber_input_flow_assaign);

                    // Process Email Auto Responder 
                    if($reply_type=="Email" && $answer!=""){
                        $this->email_info_send_auto_responder($page_id,$subscriber_info,$answer);
                    }

                     // Process Phone Auto Responder 
                    if($reply_type=="Phone" && $answer!=""){
                        $this->phone_info_auto_responder($page_id,$subscriber_info,$answer);
                    }

                }


                // update email & phone quick reply button id in subscriber extra table 
                if($has_quick_reply==1){

                    $insert_subscriber_input_flow_assaign="INSERT INTO messenger_bot_subscriber_extra_info (subscriber_id,page_id,email_quick_reply_button_id,phone_quick_reply_button_id) 
                      VALUES ('$sender_id','$page_id','$email_quick_reply_button_id','$phone_quick_reply_button_id')
                      ON DUPLICATE KEY UPDATE  email_quick_reply_button_id='$email_quick_reply_button_id',phone_quick_reply_button_id='$phone_quick_reply_button_id'";
                    $this->basic->execute_complex_query($insert_subscriber_input_flow_assaign);

                }
             }


             if($text_reply_unique_id != '')
             {
                 $page_table_id=$value['page_id'];
                 $message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$sent_to',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
                 $this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
             }

            }   
            
        }

        if($return)
        {
            echo json_encode([
                'status'=> empty($reply_error_message) ? '1' : '0',
                'message'=> empty($reply_error_message) ? $this->lang->line('postback template message has been sent.') : $reply_error_message
                ]);
        }
    }

    public function webhook_callback_main()
    {
        
        $currenTime=date("Y-m-d H:i:s");
        $response_raw=$this->input->post("response_raw");  

        // file_put_contents("fb.txt",$response_raw, FILE_APPEND | LOCK_EX);        
        //exit();

        // $response_raw='{"object":"page","entry":[{"id":"110464057210276","time":1642840506867,"messaging":[{"sender":{"id":"2499471623490220"},"recipient":{"id":"110464057210276"},"timestamp":1642840506240,"message":{"mid":"m_KgUYBn45eTDMGfIc5EtDek4zdFjafo41ubxSawTPhZvcgLdzFLC5gP_JCHHCewRC7DCWifmFu9v7KQ7aZ-0-Zw","text":"Alamin"}}]}]}';

        $response = json_decode($response_raw,TRUE);
        if(isset($response['entry']['0']['messaging'][0]['delivery'])) exit();
        if(isset($response['entry']['0']['messaging'][0]['read'])) exit; 

        if(isset($response['object']) && $response['object']=="instagram")
            $social_media_type="ig";
        else
            $social_media_type="fb";


        // for package expired users bot will not work section
        $page_id = $response['entry']['0']['messaging'][0]['recipient']['id'];

        if($social_media_type=="ig"){
            $ig_info_from_page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("instagram_business_account_id"=>$page_id,"bot_enabled"=>"1")),"page_id");
            $page_id=$ig_info_from_page_info[0]['page_id'] ?? ""; 
        }
       
        //if it's optin from checkbox plugin, then tese action is not needed. As not information can be found for that. 
       
        if(!isset($response['entry'][0]['messaging'][0]['optin']['user_ref'])) 
        {       
            $sender_id= isset($response['entry']['0']['messaging'][0]['sender']['id']) ? $response['entry']['0']['messaging'][0]['sender']['id'] :"";

            // if from custom chat plugin at the first time or open thread to chat 
            $custom_chat_user_ref= isset($response['entry']['0']['messaging'][0]['sender']['user_ref']) ? $response['entry']['0']['messaging'][0]['sender']['user_ref'] :"";

            // Is guest user or not , available in Custom Chat Plugin

            $is_guest_user=isset($response['entry'][0]['messaging'][0]['postback']['referral']['is_guest_user'])?$response['entry'][0]['messaging'][0]['postback']['referral']['is_guest_user']: false;

            if($sender_id!="" && $custom_chat_user_ref==""){

	            //subscriber status
	            $create_subscriber_get_info=$this->create_subscriber($sender_id, $page_id,$social_media_type);
	            $subscriber_new_old_info['is_new']= $create_subscriber_get_info['is_new'];
	            $subscriber_info[0] = $create_subscriber_get_info['subscriber_info'];

	            // find if they were unavailable for broadcasting. 
	            $subsciber_broadcast_unavailable=isset($subscriber_info[0]['unavailable']) ? $subscriber_info[0]['unavailable'] :"0";

            }

            // If from custom chat plugin at the first time. 
            else if($sender_id=="" && $custom_chat_user_ref!=""){
            	 $subscriber_info[0] = array();
            }

            // If coming as Guest from Customer Chat Plugin , now it will work for the first condition, it will not come here. 
            else{

                $subscriber_info[0] = array('subscribe_id' => $sender_id,'status'=>"1");

            }
        
        }
     
        /***   Check if it coming from after subscribing by checkbox plugin    ***/

        if(isset($response['entry'][0]['messaging'][0]['prior_message']['source']) && $response['entry'][0]['messaging'][0]['prior_message']['source']=="checkbox_plugin")
        {
        
            $user_identifier= isset($response['entry'][0]['messaging'][0]['prior_message']['identifier']) ? $response['entry'][0]['messaging'][0]['prior_message']['identifier']:"";
            
            if($user_identifier!="")
            {                
                //Get check_box plugin id searching with user_identifier.                 
                $check_box_plugin_info= $this->basic->get_data("messenger_bot_engagement_checkbox_reply",array("where"=>array("user_ref"=>$user_identifier)));
                
                $check_box_plugin_id=isset($check_box_plugin_info[0]['checkbox_plugin_id']) ? $check_box_plugin_info[0]['checkbox_plugin_id']:"";
                $check_box_plugin_reference=isset($check_box_plugin_info[0]['reference']) ? $check_box_plugin_info[0]['reference']:"";
                $for_woocommerce=isset($check_box_plugin_info[0]['for_woocommerce']) ? $check_box_plugin_info[0]['for_woocommerce']:"";
                $wc_session_unique_id= isset($check_box_plugin_info[0]['wc_session_unique_id']) ? $check_box_plugin_info[0]['wc_session_unique_id']:"";
                                    
                if($check_box_plugin_id!="")
                {
                 // Update subscriber if new, then source is from checkbox plugin & also reffernce updated. 
                    if($subscriber_new_old_info['is_new'])
                    {
                        $plugin_name=$response['entry'][0]['messaging'][0]['prior_message']['source'];
                        $subscriber_id_update=$subscriber_info[0]['id'];
                        $update_data=array("refferer_id"=>$check_box_plugin_reference,"refferer_source"=>$plugin_name,"refferer_uri"=>"N/A");
                        $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscriber_id_update),$update_data);
                    }

                if($for_woocommerce=="1" || $wc_session_unique_id!=""){

                    // Get the woocommerce user id from table woocommerce_drip_campaign_webhook_call , if there available. 
                    $wocommerce_user_info = $this->basic->get_data("woocommerce_drip_campaign_webhook_call",array("where"=>array("wc_session_unique_id"=>$wc_session_unique_id)));
                    $wc_user_id= isset($wocommerce_user_info[0]['wc_user_id']) ? $wocommerce_user_info[0]['wc_user_id']:"";
                    $woocommerce_drip_campaign_id= isset($wocommerce_user_info[0]['woocommerce_drip_campaign_id']) ? $wocommerce_user_info[0]['woocommerce_drip_campaign_id']:"";

                    if($wc_user_id!=""){
                        $update_data=array("wc_user_id"=>$wc_user_id,"woocommerce_drip_campaign_id"=>$woocommerce_drip_campaign_id);
                        $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscriber_id_update),$update_data);
                    }
                    
                }
                    
                /****Assign Drip Messaging Campaing ID ***/
                $drip_type="messenger_bot_engagement_checkbox";
                $this->assign_drip_messaging_id($drip_type,$check_box_plugin_id,$subscriber_info[0]['page_table_id'],$subscriber_info[0]['subscribe_id']);   
                    
                    
                    $engagementer_info= $this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("id"=>$check_box_plugin_id)));
            
                    $label_ids=isset($engagementer_info[0]['label_ids']) ? $engagementer_info[0]['label_ids']:"";
                    
                    if($label_ids!="" )
                    {                 
                    
                        //DEPRECATED FUNCTION FOR QUICK BROADCAST
                        $this->multiple_assign_label($sender_id,$page_id,$label_ids,$social_media_type,$subscriber_info[0]['id']);
                    }                        
                    
                }   
                
            }
        
        }
     
     
        //message for all   
        if(isset($response['entry'][0]['messaging'][0]['message']['text']) 
        && !isset($response['entry'][0]['messaging'][0]['message']['quick_reply']) 
        && !isset($response['entry'][0]['messaging'][0]['postback']) 
        && !isset($response['entry'][0]['messaging'][0]['optin'])
        && !isset($response['entry'][0]['messaging'][0]['message']['reply_to']['story'])) 
        {
            
            $messages = $response['entry']['0']['messaging'][0]['message']['text'];
            $table_name = "messenger_bot";

            // Check if the user in the user input flow. 
            $fb_message_id=$response['entry'][0]['messaging'][0]['message']['mid'];
            $this->user_input_flow_assaign_check($sender_id,$page_id,$subscriber_info,$messages,$fb_message_id,$message_type="text");


            $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot.status'=>'1','facebook_rx_fb_page_info.bot_enabled' => '1','media_type'=>$social_media_type);

            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id desc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $trigger_matching_type = $messenger_bot_info[0]['trigger_matching_type'];

            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']); 


            // split words from message into one/two/three words
            if($trigger_matching_type == 'exact')
            {
                $single_words_from_message_array = [];
                $twowords_from_message_array = [];
                $three_words_from_message_array = [];
                if(function_exists('iconv') && function_exists('mb_detect_encoding'))
                {
                    $encoded_message = mb_detect_encoding($messages);
                    if(isset($encoded_message))
                        $utf_message = iconv($encoded_message, "UTF-8//TRANSLIT", $messages);
                    $words_from_message = mb_split(' ', $utf_message);
                    
                    foreach($words_from_message as $single_word)
                    {
                        $new_single_word = trim($single_word, ",.!'/#* <>$&%@()[];?^+-=~`".'"');
                        array_push($single_words_from_message_array, strtolower($new_single_word));
                    }
                    $single_words_from_message_array = array_filter($single_words_from_message_array);

                    $number_of_words = count($single_words_from_message_array);

                    // creating two/three words array
                    $two_half = 2;
                    $three_half = 3;
                    for($i=0; $i<$number_of_words - 1; $i++) // first for loop for total number of words
                    {   
                        $two_words_string=""; // a blank string       
                        $three_words_string=""; // a blank string       
                        
                        for($j=$i; $j<$two_half+$i; $j++) // 2nd for loop for creating all the phrases
                        {
                            if(isset($single_words_from_message_array[$j]))
                                $two_words_string = $two_words_string." ".$single_words_from_message_array[$j];            
                        }       

                        if($two_words_string!="") 
                            $twowords_from_message_array[]=trim($two_words_string);  // saving phrases to an array


                        for($j=$i; $j<$three_half+$i; $j++) // 2nd for loop for creating all the phrases
                        {
                            if(isset($single_words_from_message_array[$j]))
                                $three_words_string = $three_words_string." ".$single_words_from_message_array[$j];            
                        }       

                        if($three_words_string!="") 
                            $three_words_from_message_array[]=trim($three_words_string);  // saving phrases to an array
                    }
                    $twowords_from_message_array = array_filter($twowords_from_message_array);
                    $three_words_from_message_array = array_filter($three_words_from_message_array);
                }
            }


            foreach ($messenger_bot_info as $key => $value) {
                $cam_keywords_str = $value['keywords'];
                $cam_keywords_array = explode(",", $cam_keywords_str);

                $temp_cam_keywords_array = [];
                foreach ($cam_keywords_array as $cam_keywords) 
                {
                    if(function_exists('iconv') && function_exists('mb_detect_encoding'))
                    {
                        $encoded_word =  mb_detect_encoding($cam_keywords);
                        if(isset($encoded_word)){
                            $cam_keywords = strtolower(iconv($encoded_word, "UTF-8//TRANSLIT", $cam_keywords));
                            $cam_keywords = trim($cam_keywords);
                        }
                    }

                    
                    if($trigger_matching_type == 'exact')
                    {
                        $search_array = [];
                        $temp_cam_keywords_array = explode(" ", $cam_keywords);
                        if(count($temp_cam_keywords_array) == 1) $search_array = $single_words_from_message_array;
                        else if(count($temp_cam_keywords_array) == 2) $search_array = $twowords_from_message_array;
                        else if(count($temp_cam_keywords_array) == 3) $search_array = $three_words_from_message_array;

                        if(in_array($cam_keywords, $search_array))
                            $matches = TRUE;
                        else $matches = FALSE;
                    }
                    else
                        $matches = stripos($messages,trim($cam_keywords));

                    if($matches !== FALSE)
                    {

                        $this->send_message_bot_reply($value,$sender_id,$subscriber_info,$page_id);


                        //update Subscriber Last Interaction time. 
                        $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

                        /***Update Source if user send text message just after click to messenger ads action ***/

                        $reference_id = isset($response['entry'][0]['messaging'][0]['referral']['ref']) ? $response['entry'][0]['messaging'][0]['referral']['ref']:"";
                        $reference_source=isset($response['entry'][0]['messaging'][0]['referral']['source']) ? $response['entry'][0]['messaging'][0]['referral']['source']:"";

                        if($reference_source=='ADS'){

                            $reference_ad_id=isset($response['entry'][0]['messaging'][0]['referral']['ad_id']) ? $response['entry'][0]['messaging'][0]['referral']['ad_id']:"";
                            $reference_ad_id="ad_id: ".$reference_ad_id;

                            if($subscriber_new_old_info['is_new']){
                                $subscriber_id_update=$subscriber_info[0]['id'];
                                $update_data=array("refferer_id"=>$reference_id,"refferer_source"=>"ADS","refferer_uri"=>$reference_ad_id);
                                $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscriber_id_update),$update_data);
                            }
                        }
                        
                        die();
                    }
                }
            }


            $table_name = "messenger_bot";

            if($social_media_type=='fb')
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id, 'messenger_bot.keyword_type' => 'no match','facebook_rx_fb_page_info.bot_enabled' => '1','facebook_rx_fb_page_info.no_match_found_reply'=>'enabled',
                    'media_type'=>$social_media_type);
            else
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id, 'messenger_bot.keyword_type' => 'no match','facebook_rx_fb_page_info.bot_enabled' => '1','facebook_rx_fb_page_info.ig_no_match_found_reply'=>'enabled',
                    'media_type'=>$social_media_type);

            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'1','','messenger_bot.id asc');

            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'] ?? "";
            
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info))
            {
                $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);
                //update Subscriber Last Interaction time. 
                $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);
                die();
            }
        }

        elseif(isset($response['entry'][0]['messaging'][0]['optin'])) //Optins from Send to messengers 
        {
            
            $user_reference_id=""; // assigning variable for CheckBox Plugin 

            $is_one_time_notification= isset($response['entry'][0]['messaging'][0]['optin']['type']) ? $response['entry'][0]['messaging'][0]['optin']['type']:"";
            
            if($is_one_time_notification!='one_time_notif_req')
            {
        
                $reference_id = isset($response['entry'][0]['messaging'][0]['optin']['ref'])?$response['entry'][0]['messaging'][0]['optin']['ref']:"";
                $user_reference_id = isset($response['entry'][0]['messaging'][0]['optin']['user_ref'])?$response['entry'][0]['messaging'][0]['optin']['user_ref']:"";


                // check if this come from woocommer abandoned cart plugin

                $wc_pos=strpos($reference_id, "xitwacr");
                if($wc_pos!==false){
                    $reference_str_array=explode("_", $reference_id);
                    $unique_session_id= isset($reference_str_array[1]) ? $reference_str_array[1] :"";
                    unset($reference_str_array[0]);
                    unset($reference_str_array[1]);
                    $plugin_reference_id=implode("_", $reference_str_array);

                    $engagementer_info= $this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("reference"=>$plugin_reference_id)));
                    $plugin_auto_id=isset($engagementer_info[0]['id']) ? $engagementer_info[0]['id']:"";

                    $reference_data_checkbox['user_ref']=$user_reference_id;
                    $reference_data_checkbox['checkbox_plugin_id']=$plugin_auto_id;
                    $reference_data_checkbox['reference']=$plugin_reference_id;
                    $reference_data_checkbox['optin_time']=date("Y-m-d H:i:s");
                    $reference_data_checkbox['for_woocommerce']="1";
                    $reference_data_checkbox['wc_session_unique_id']=$unique_session_id;
                    $this->basic->insert_data("messenger_bot_engagement_checkbox_reply",$reference_data_checkbox);
                    exit;
                }
                
                if($user_reference_id!="")
                    $table_name="messenger_bot_engagement_checkbox";
                    
                else
                {
                    $table_name="messenger_bot_engagement_send_to_msg";
                    if($subscriber_new_old_info['is_new'])
                    {
                        $plugin_name="SEND-TO-MESSENGER-PLUGIN";
                        $subscriber_id_update=$subscriber_info[0]['id'];
                        
                        $update_data=array("refferer_id"=>$reference_id,"refferer_source"=>$plugin_name,"refferer_uri"=>"N/A");
                        $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscriber_id_update),$update_data);
                    }
                    
                }
                    
                
                $engagementer_info= $this->basic->get_data($table_name,array("where"=>array("reference"=>$reference_id)));
                
                $label_ids=isset($engagementer_info[0]['label_ids']) ? $engagementer_info[0]['label_ids']:"";
                
                $template_id=isset($engagementer_info[0]['template_id']) ? $engagementer_info[0]['template_id']:"";
                
                $plugin_auto_id=isset($engagementer_info[0]['id']) ? $engagementer_info[0]['id']:"";
                
                
                if($template_id!=""){
                    
                    $postback_id_info= $this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$template_id)));
                    $postback_id= isset($postback_id_info[0]['postback_id']) ? $postback_id_info[0]['postback_id'] :"";
                }
                
                $table_name = "messenger_bot";
                
                if($template_id=="")
                    $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'keyword_type'=>'get-started','facebook_rx_fb_page_info.bot_enabled' => '1');
                else    
                    $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'facebook_rx_fb_page_info.bot_enabled' => '1',"postback_id"=>$postback_id);
                    
            }


            elseif ($response['entry'][0]['messaging'][0]['optin']['type']=='one_time_notif_req') {

                $otn_payload_id_unique_id = $response['entry'][0]['messaging'][0]['optin']['payload'];

                $otn_payload_id_info = explode('::',$otn_payload_id_unique_id);
                $otn_payload = $otn_payload_id_info[0];
                $button_unique_id = $otn_payload_id_info[1] ?? '';

                $otn_token= $response['entry'][0]['messaging'][0]['optin']['one_time_notif_token'];

                // Insert subscriber token info 
                $optin_time=date("Y-m-d H:i:s");

               //If not exist this subscriber, then insert. If exist then update token & optin time . And also mark as is_sent=0 , that means if already sent, then token will updated & eligible for send again. 

                $page_table_auto_id=$subscriber_info[0]['page_table_id']; 
                $sql_otn_insert="INSERT INTO otn_optin_subscriber (otn_id,subscriber_id,otn_token,optin_time,page_table_id) 
                      VALUES ('$otn_payload','$sender_id','$otn_token','$optin_time','$page_table_auto_id')
                      ON DUPLICATE KEY UPDATE  otn_token='$otn_token',optin_time='$optin_time',is_sent='0'";
                      
                $this->basic->execute_complex_query($sql_otn_insert);

                // inset into sent-stat table for otn unique id
                if($button_unique_id != '')
                {
                    $message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$sender_id',$page_table_auto_id,'$button_unique_id','postback',1,0) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1";
                    $this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
                }


                

                $otn_postback_info=$this->basic->get_data('otn_postback',array("where"=>array("id"=>$otn_payload)));

                $label_ids=isset($otn_postback_info[0]['label_id']) ? $otn_postback_info[0]['label_id']:"";
                $template_id=isset($otn_postback_info[0]['reply_postback_id']) ? $otn_postback_info[0]['reply_postback_id']:"";
                $drip_campaign_id=isset($otn_postback_info[0]['drip_campaign_id']) ? $otn_postback_info[0]['drip_campaign_id']:"";

                if($template_id!=""){ 
                    $postback_id_info= $this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$template_id)));
                    $postback_id= isset($postback_id_info[0]['postback_id']) ? $postback_id_info[0]['postback_id'] :"";
                    $where['where'] = array('messenger_bot.fb_page_id' => $page_id,"postback_id"=>$postback_id,'facebook_rx_fb_page_info.bot_enabled' => '1');
                     $table_name = "messenger_bot";
                }
            }



            
            else{
            
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'keyword_type'=>'get-started','facebook_rx_fb_page_info.bot_enabled' => '1');
                 /** Assign Drip Messaging Campaign ID ****/
                $drip_type="default";
                $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$subscriber_info[0]['subscribe_id']);
            }
            
            
            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');

            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];            
            
            if($enable_mark_seen && $user_reference_id=="")
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);
                
            
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){
                
                if($user_reference_id=="") 
                    $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);
                else
                    $this->send_message_bot_reply($messenger_bot_info[0],'',array(),$page_id,$user_reference_id);


                /*** Assign Drip Campaing & also Label ***/

                /***    Assign Drip Messaging Campaign ID *****/

                if($is_one_time_notification=='one_time_notif_req'){

                    if($drip_campaign_id!="" && $drip_campaign_id!="0"){
                        
                        $drip_type="custom";
                        $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$subscriber_info[0]['subscribe_id'],$drip_campaign_id); 
                    }
                    
                }

                else if($user_reference_id==""){
                    $drip_type="messenger_bot_engagement_send_to_msg";
                    $this->assign_drip_messaging_id($drip_type,$plugin_auto_id,$subscriber_info[0]['page_table_id'],$subscriber_info[0]['subscribe_id']);    
                }       
            
                 /** Insert into messenger_bot_engagement_checkbox_reply if it comes from checkbox plugin ***/
                 if($user_reference_id!="")
                 {
                    $reference_data_checkbox['user_ref']=$user_reference_id;
                    $reference_data_checkbox['checkbox_plugin_id']=$plugin_auto_id;
                    $reference_data_checkbox['reference']=$reference_id;
                    $reference_data_checkbox['optin_time']=date("Y-m-d H:i:s");
                    $this->basic->insert_data("messenger_bot_engagement_checkbox_reply",$reference_data_checkbox);
                    
                 }
            
                if($label_ids!="" && $user_reference_id==""){   // Update Label if only send-to-messenger. Don't for checkbox for first time. As we can't infromation

                    //DEPRECATED FUNCTION FOR QUICK BROADCAST
                   $this->multiple_assign_label($sender_id,$page_id,$label_ids,$social_media_type,$subscriber_info[0]['id']);
                     
                } 
    

             //update Subscriber Last Interaction time. 
            if($user_reference_id=="")
                $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

            exit; 

            }
        }
        
        
        elseif((isset($response['entry'][0]['messaging'][0]['postback']['referral']['type']) && $response['entry'][0]['messaging'][0]['postback']['referral']['type']=="OPEN_THREAD" && isset($response['entry'][0]['messaging'][0]['postback']['referral']['ref'])) || 
        
        (isset($response['entry'][0]['messaging'][0]['postback']['payload']) && $response['entry'][0]['messaging'][0]['postback']['payload']=="GET_STARTED_PAYLOAD" ) ||
        (isset($response['entry'][0]['messaging'][0]['referral']['source']) && $response['entry'][0]['messaging'][0]['referral']['type']=="OPEN_THREAD"))
        
            //When not any conversation and get started button is added
            {   


                /* If get started not set, then get the refferal means already have the conversation */
                $reference_id = isset($response['entry'][0]['messaging'][0]['postback']['referral']['ref'])?$response['entry'][0]['messaging'][0]['postback']['referral']['ref']:$response['entry'][0]['messaging'][0]['referral']['ref'];
                
                $reference_source=isset($response['entry'][0]['messaging'][0]['postback']['referral']['source'])?$response['entry'][0]['messaging'][0]['postback']['referral']['source']:$response['entry'][0]['messaging'][0]['referral']['source'];

                // Check if this m.me link & from Ecommerce QR Code 

                $reference_source_explode = explode("-", $reference_id);

                if(isset($reference_source_explode[0]) && $reference_source_explode[0]=="ecomqrmelink01"  && $reference_source=="SHORTLINK"){

                    // get reply information from Ecommerce store. 
                    $ecom_unique_id=$reference_source_explode[2];

                    $pick_up_point_id= $reference_source_explode[3] ?? "";

                    $where_qr['where']=array("store_unique_id"=>$ecom_unique_id);
                    $store_information = $this->basic->get_data("ecommerce_store",$where_qr,array("qr_code"));
                    $qr_code_reply_info=isset($store_information[0]['qr_code']) ? $store_information[0]['qr_code'] : "";
                    $qr_code_reply_info=json_decode($qr_code_reply_info,true);

                    $qr_reply_message=isset($qr_code_reply_info['msg_text']) ? $qr_code_reply_info['msg_text'] : "Hi {{first_name}}, welcome to our store.";
                    $qr_reply_message=str_replace('{{first_name}}','#LEAD_USER_FIRST_NAME#',$qr_reply_message);
                    $qr_reply_message=str_replace('{{last_name}}','#LEAD_USER_LAST_NAME#', $qr_reply_message);

                    $qr_reply_button_text=isset($qr_code_reply_info['msg_btn']) ? $qr_code_reply_info['msg_btn'] : "START NOW";
                    $ecom_url=base_url("ecommerce/store/".$ecom_unique_id)."?subscriber_id=#SUBSCRIBER_ID_REPLACE#";

                    if($pick_up_point_id!="" && $pick_up_point_id!=0){
                        $ecom_url=base_url("ecommerce/store/".$ecom_unique_id)."?pickup=".$pick_up_point_id."&subscriber_id=#SUBSCRIBER_ID_REPLACE#";
                    }

                    // Get page access token 
                    $access_token_where['where']=array("page_id"=>$page_id,"bot_enabled"=>"1");
                    $access_token_info= $this->basic->get_data('facebook_rx_fb_page_info',$access_token_where,array("page_access_token","id","user_id"));
                    $access_token=$access_token_info[0]['page_access_token'];
                    $page_auto_id_table= $access_token_info[0]['id'];
                    $user_id= $access_token_info[0]['user_id'];
                    $messenger_bot_info['message']='{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"'.$qr_reply_message.'","buttons":[{"type":"web_url","url":"'.$ecom_url.'","title":"'.$qr_reply_button_text.'","messenger_extensions":"true","webview_height_ratio":"full"}]}},"typing_on_settings":"off","delay_in_reply":"0"}}}';

                    $messenger_bot_info['page_access_token']= $access_token;
                    $messenger_bot_info['page_id']= $page_auto_id_table;
                    $messenger_bot_info['id']= 0;
                    $messenger_bot_info['user_id']= $user_id; 

                    $this->send_message_bot_reply($messenger_bot_info,$sender_id,$subscriber_info,$page_id);
                    //update Subscriber Last Interaction time. 
                    $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

                    exit;
                }

            
                /**Check If the Engagement add-on is installed or not. Check a table of this addon is exist or not**/
                
            	if($this->addon_exist("messenger_bot_enhancers")){
                
                if($reference_source=="CUSTOMER_CHAT_PLUGIN"){ // If from Custom CHat
                    $table_name="messenger_bot_engagement_2way_chat_plugin";
                    $plugin_name=$reference_source;
                    $refferer_uri=isset($response['entry'][0]['messaging'][0]['postback']['referral']['referer_uri'])?$response['entry'][0]['messaging'][0]['postback']['referral']['referer_uri']:"";
                    $drip_type="messenger_bot_engagement_2way_chat_plugin";
                }
                    
                else if($reference_source=="SHORTLINK"){ // If from custom link
                
                    $table_name="messenger_bot_engagement_mme";
                    $plugin_name=$reference_source;
                    $refferer_uri="N/A";
                    $drip_type="messenger_bot_engagement_mme";
                    
                }
                else if($reference_source=="MESSENGER_CODE"){ //if messenger codes
                
                    $table_name="messenger_bot_engagement_messenger_codes";
                    $plugin_name=$reference_source;
                    $refferer_uri="N/A";
                    $drip_type="messenger_bot_engagement_messenger_codes";
                    
                }

                else if($reference_source=="ADS"){  // if come after Click to Messenger ads Action. 

                    $table_name="";
                    $reference_ad_id=isset($response['entry'][0]['messaging'][0]['referral']['ad_id']) ? $response['entry'][0]['messaging'][0]['referral']['ad_id']:"";
                    $reference_ad_id="ad_id: ".$reference_ad_id;
                    $plugin_name=$reference_source;
                    $refferer_uri=$reference_ad_id;

                }

                else{  // If come from page directly
                    $table_name="";
                    $plugin_name="FB PAGE";
                    $refferer_uri="N/A";
                    $drip_type="default";
                    $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id);
                }
                
                // if not coming from customer chat plugin first time with user_ref
                if($custom_chat_user_ref==""){
                if($subscriber_new_old_info['is_new']){
                        $subscriber_id_update=$subscriber_info[0]['id'];
                        $update_data=array("refferer_id"=>$reference_id,"refferer_source"=>$plugin_name,"refferer_uri"=>$refferer_uri);
                        $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscriber_id_update),$update_data);
                    }
                }

                //If come after click to messengers ads , then if it's postback click or quick reply click, then need to jump to postback or quick reply section for replying. Because this part is only for get started button click & other engagment plugin . 

                if($reference_source=="ADS" && $response['entry'][0]['messaging'][0]['postback']['payload']!="GET_STARTED_PAYLOAD" ){

                    if(isset($response['entry'][0]['messaging'][0]['message']['quick_reply'])) goto QUICK_REPLY_BLOCK;
                    else if (isset($response['entry'][0]['messaging'][0]['postback'])) goto POST_BACK_BLOCK;

                    exit;

                } 

                
                
                $postback_id="";
                
                if($table_name!=""){
                
                $engagementer_info= $this->basic->get_data($table_name,array("where"=>array("reference"=>$reference_id)));
                
                $plugin_auto_id=isset($engagementer_info[0]['id']) ? $engagementer_info[0]['id']:"";

                $label_ids=isset($engagementer_info[0]['label_ids']) ? $engagementer_info[0]['label_ids']:"";
                $template_id=isset($engagementer_info[0]['template_id']) ? $engagementer_info[0]['template_id']:"";
                
                if($template_id!=""){
                    $postback_id_info= $this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$template_id)));
                    $postback_id= isset($postback_id_info[0]['postback_id']) ? $postback_id_info[0]['postback_id'] :"";
                    
                }
                
            }
            
            
            if($postback_id=="")
                
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'keyword_type'=>'get-started','facebook_rx_fb_page_info.bot_enabled' => '1');
                    
            else    
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'facebook_rx_fb_page_info.bot_enabled' => '1',"postback_id"=>$postback_id);
            
            }
            
            else{  // if engagement add-on not installed, then default query for get started. 
            
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'keyword_type'=>'get-started','facebook_rx_fb_page_info.bot_enabled' => '1');
                 /** Assign Drip Messaging Campaign ID ****/
                $drip_type="default";
                $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id);
            }
            
                    
            
            $table_name = "messenger_bot";
            
            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");  

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            
            if($enable_mark_seen && $custom_chat_user_ref=="") // mark ass seen action
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);
            
              if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){
              
              	if($custom_chat_user_ref=="")
              		 $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);
                	
                else if($custom_chat_user_ref!="")
                	$this->send_message_bot_reply($messenger_bot_info[0],'',array(),$page_id,$custom_chat_user_ref);
                
               
                /****   Update Drip Messaging Campaign ID ****/
                if(isset($plugin_auto_id) && $custom_chat_user_ref=="")
                $this->assign_drip_messaging_id($drip_type,$plugin_auto_id,$subscriber_info[0]['page_table_id'],$sender_id);    

                else if($reference_source=="ADS"){  // If Ads & come from Get Started Button Click 
                    $drip_type="default";
                    $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id);
                }
           
                if(!empty($label_ids)){
                    //DEPRECATED FUNCTION FOR QUICK BROADCAST
                    $this->multiple_assign_label($sender_id,$page_id,$label_ids,$social_media_type,$subscriber_info[0]['id']);
                     
                } 
                
                 //update Subscriber Last Interaction time. 
                $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

                die();
            }
        }
        
        elseif (isset($response['entry'][0]['messaging'][0]['message']['quick_reply'])) //quick_reply
        {

            QUICK_REPLY_BLOCK: 

            //catch payload_id from response
            $payload_id_unique_id = $response['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];

            $payload_id_info = explode('::',$payload_id_unique_id);
            $payload_id = $payload_id_info[0];
            $button_unique_id = $payload_id_info[1] ?? '';

            $messages = $response['entry'][0]['messaging'][0]['message']['text'];
            $table_name = "messenger_bot";


            // Check if the user in the user input flow. 
            $fb_message_id=$response['entry'][0]['messaging'][0]['message']['mid'];
            $this->user_input_flow_assaign_check($sender_id,$page_id,$subscriber_info,$payload_id,$fb_message_id,$message_type='quick_replies');


            $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'postback_id'=>$payload_id,'facebook_rx_fb_page_info.bot_enabled' => '1','media_type'=>$social_media_type);

            $where_custom = "(keyword_type='post-back' or keyword_type='reply')";
            $this->db->where($where_custom);


            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time","facebook_rx_fb_page_info.mail_service_id as mail_service_id","facebook_rx_fb_page_info.sms_api_id as sms_api_id","facebook_rx_fb_page_info.sms_reply_message as sms_reply_message","email_api_id","email_reply_message","email_reply_subject","page_name","sequence_sms_campaign_id","sequence_email_campaign_id"),$join,'','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
            $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
            if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            
            /***    Insert email into database if it's email from quick reply ***/
            
            if($this->is_email($payload_id)){
                
                $user_id=$subscriber_info[0]['user_id'];
                $fb_user_id=$subscriber_info[0]['subscribe_id'];
                $update_time=date("Y-m-d H:i:s");
                $email=$payload_id;                
               
                $sql="UPDATE messenger_bot_subscriber SET email='$email' WHERE subscribe_id='$fb_user_id';";  

                $this->basic->execute_complex_query($sql);
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'facebook_rx_fb_page_info.bot_enabled' => '1',"keyword_type"=>"email-quick-reply");

                $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

                $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time","facebook_rx_fb_page_info.mail_service_id as mail_service_id","facebook_rx_fb_page_info.sms_api_id as sms_api_id","facebook_rx_fb_page_info.sms_reply_message as sms_reply_message","email_api_id","email_reply_message","email_reply_subject","page_name","sequence_sms_campaign_id","sequence_email_campaign_id"),$join,'','','messenger_bot.id asc');
                $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
                $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
                
                $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
                if($typing_on_delay_time=="0") $typing_on_delay_time=1;

                // get email quick reply button id from subscriber extra table 
                $subscriber_extra_info= $this->basic->get_data("messenger_bot_subscriber_extra_info",array("where"=>array("subscriber_id"=>$fb_user_id,'page_id'=>$page_id)));
                $button_unique_id=$subscriber_extra_info[0]['email_quick_reply_button_id'] ?? "";


            }
            elseif($this->is_phone_number($payload_id)){
            
              
                $user_id=$subscriber_info[0]['user_id'];
                $fb_user_id=$subscriber_info[0]['subscribe_id'];
                $update_time=date("Y-m-d H:i:s");
                $phone_number=$payload_id;
                
                $sql="UPDATE messenger_bot_subscriber SET phone_number='$phone_number' WHERE subscribe_id='$fb_user_id';";              
                    
                $this->basic->execute_complex_query($sql);

                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'facebook_rx_fb_page_info.bot_enabled' => '1',"keyword_type"=>"phone-quick-reply");

                $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

                $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time","facebook_rx_fb_page_info.mail_service_id as mail_service_id","facebook_rx_fb_page_info.sms_api_id as sms_api_id","facebook_rx_fb_page_info.sms_reply_message as sms_reply_message","email_api_id","email_reply_message","email_reply_subject","page_name","sequence_sms_campaign_id","sequence_email_campaign_id"),$join,'','','messenger_bot.id asc');
                
                $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];

                // get phone quick reply button id from subscriber extra table 
                $subscriber_extra_info= $this->basic->get_data("messenger_bot_subscriber_extra_info",array("where"=>array("subscriber_id"=>$fb_user_id,'page_id'=>$page_id)));
                $button_unique_id=$subscriber_extra_info[0]['phone_quick_reply_button_id'] ?? "";
            }
            
            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);   
           
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){

	            $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);

                // insert messenger_bot_message_sent_stat table 
                $page_table_id=$messenger_bot_info[0]['page_id'];

                if($button_unique_id != '')
                {
                    $message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$sender_id',$page_table_id,'$button_unique_id','postback',1,0) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1";
                    $this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
                }


	            /** Assign Drip Messaging Campaign ID ****/
	            $drip_assign_id=isset($messenger_bot_info[0]['drip_campaign_id']) ? $messenger_bot_info[0]['drip_campaign_id']:"";
	             if($drip_assign_id!="" && $drip_assign_id!="0"){
	                $drip_type="custom";
	                $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id,$drip_assign_id);  
	            }
                

	            /***Set labels if any setup available for this postback for quickReply ***/

	            $label_ids=isset($messenger_bot_info[0]['broadcaster_labels']) ? $messenger_bot_info[0]['broadcaster_labels']:"";
	       
	            if(!empty($label_ids)){

	                //DEPRECATED FUNCTION FOR QUICK BROADCAST
	                $this->multiple_assign_label($sender_id,$page_id,$label_ids,$social_media_type,$subscriber_info[0]['id']);
	                 
	            } 

	           if($this->addon_exist("messenger_bot_connectivity")) 
	            {
	                if($this->is_email($payload_id))
	                	$this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_email");
	                else if($this->is_phone_number($payload_id))
	                	$this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_phone_number");
	                else
	                	$this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_postback",$payload_id);
	            }

                // Send to Email Auto Responder
	            if($this->is_email($payload_id)){
	                $email_auto_responder_id= isset($messenger_bot_info[0]['mail_service_id']) ? $messenger_bot_info[0]['mail_service_id']:"";
	                $pagename= isset($messenger_bot_info[0]['page_name']) ? $messenger_bot_info[0]['page_name'] : "";
	                $mailchimp_tags=array($pagename); // Page Name
	                if($email_auto_responder_id!="")
	                    $this->send_email_to_autoresponder($email_auto_responder_id, $payload_id,$subscriber_info[0]['first_name'],$subscriber_info[0]['last_name'],$type='quick-reply',$user_id,$mailchimp_tags);

	                //Assaign Email Drip Campaign

	                $sequence_email_campaign_id = isset($messenger_bot_info[0]['sequence_email_campaign_id']) ? $messenger_bot_info[0]['sequence_email_campaign_id']:"";

	                if($sequence_email_campaign_id!=0){
	                	$drip_type="custom";
	                	 $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id,$sequence_email_campaign_id);  
	                }


	                // Send Email From System 

                    $email_api_id= isset($messenger_bot_info[0]['email_api_id']) ? $messenger_bot_info[0]['email_api_id']:"";
                    $email_reply_message= isset($messenger_bot_info[0]['email_reply_message']) ? nl2br($messenger_bot_info[0]['email_reply_message']):"";
                    $email_reply_subject= isset($messenger_bot_info[0]['email_reply_subject']) ? $messenger_bot_info[0]['email_reply_subject']:"";

                    if($email_api_id!=""){

                        if(isset($subscriber_info[0]['first_name']))
                            $email_reply_message=str_replace("{{user_first_name}}", $subscriber_info[0]['first_name'], $email_reply_message);
                        if(isset($subscriber_info[0]['last_name']))
                            $email_reply_message=str_replace("{{user_last_name}}", $subscriber_info[0]['last_name'], $email_reply_message);
                        $this->send_email_by_for_bot_email($email_api_id,$email_reply_message,$payload_id, $email_reply_subject,$user_id);

                    }
            	}


	            // Send SMS to Phone Number With Email Sender 

	       if($this->is_phone_number($payload_id)){

	            $sms_api_id= isset($messenger_bot_info[0]['sms_api_id']) ? $messenger_bot_info[0]['sms_api_id']:"";
	            $sms_reply_message= isset($messenger_bot_info[0]['sms_reply_message']) ? $messenger_bot_info[0]['sms_reply_message']:"";

	            if(isset($subscriber_info[0]['first_name']))
	                $sms_reply_message=str_replace("{{user_first_name}}", $subscriber_info[0]['first_name'], $sms_reply_message);
	            if(isset($subscriber_info[0]['last_name']))
	                $sms_reply_message=str_replace("{{user_last_name}}", $subscriber_info[0]['last_name'], $sms_reply_message);

	            $this->send_sms_by_for_bot_phone_number($sms_api_id,$user_id,$sms_reply_message,$payload_id);


	            //Assaign SMS Drip Campaign
	                
	                $sequence_sms_campaign_id = isset($messenger_bot_info[0]['sequence_sms_campaign_id']) ? $messenger_bot_info[0]['sequence_sms_campaign_id']:"";

	                if($sequence_sms_campaign_id!=0){
	                	$drip_type="custom";
	                	$this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id,$sequence_sms_campaign_id);  
	                }
	            
	            }

	            //update Subscriber Last Interaction time. 
	            $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

	           exit; 
        	}
    
        }

        elseif(isset($response['entry'][0]['messaging'][0]['postback']))//Clicking on Payload Button like Start Chatting
        {

            POST_BACK_BLOCK:

            $payload_id_unique_id = $response['entry'][0]['messaging'][0]['postback']['payload'];

            $payload_id_info = explode('::',$payload_id_unique_id);
            $payload_id = $payload_id_info[0];
            $button_unique_id = $payload_id_info[1] ?? '';

            $table_name = "messenger_bot";

            $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'postback_id'=>$payload_id,'facebook_rx_fb_page_info.bot_enabled' => '1','media_type'=>$social_media_type);

            $where_custom = "(keyword_type='post-back' or keyword_type='reply')";
            $this->db->where($where_custom);

            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left"); 

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time","facebook_rx_fb_page_info.page_name as page_name","facebook_rx_fb_page_info.chat_human_email"),$join,'','','messenger_bot.id asc');

            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'] ?? false;            
            
            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);

            if($payload_id=="UNSUBSCRIBE_QUICK_BOXER")
            {  
                //$this->unsubscribe_webhook_call($sender_id,$page_id);
                
                // DEPRECATED FUNCTION FOR QUICK BROADCAST
                $post_data_unsubscribe=array("psid"=>$sender_id,"fb_page_id"=>$page_id);
                $url=base_url()."home/unsubscribe_webhook_call";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_unsubscribe);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                $reply_response=curl_exec($ch);  
            }
            elseif($payload_id=="RESUBSCRIBE_QUICK_BOXER")
            {
                // $this->resubscribe_webhook_call($sender_id,$page_id);

                //DEPRECATED FUNCTION FOR QUICK BROADCAST
                $post_data_unsubscribe=array("psid"=>$sender_id,"fb_page_id"=>$page_id);
                $url=base_url()."home/resubscribe_webhook_call";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_unsubscribe);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                $reply_response=curl_exec($ch);  
            }
            elseif($payload_id=="YES_START_CHAT_WITH_HUMAN")
            {
                if($this->basic->update_data("messenger_bot_subscriber",array("page_id"=>$page_id,"subscribe_id"=>$sender_id),array("status"=>"0")))
                {
                    $pagename= isset($messenger_bot_info[0]['page_name']) ? $messenger_bot_info[0]['page_name'] : "";
                    $chat_human_email=isset($messenger_bot_info[0]['chat_human_email']) ? $messenger_bot_info[0]['chat_human_email'] : "";

                    if($chat_human_email!="")
                    {
                        $message = "Hello,<br/> One of your messenger bot subscriber has stopped robot chat and wants to chat with human a agent.<br/><br/>";
                        $message.="Page : <a target='_BLANK' href='https://www.facebook.com/".$page_id."/inbox'>".$pagename."</a><br>";
                        $message.="Subscriber ID : ".$sender_id."<br>";
                        if(isset($subscriber_info[0]['first_name']))
                        $message.="Subscriber Name : ".$subscriber_info[0]['first_name'];
                        if(isset($subscriber_info[0]['last_name']))
                        $message.=" ".$subscriber_info[0]['last_name'];
                        $message.="<br/><br> Thank you";
                        
                        $mask="";
                        if($this->config->item("product_name")!="")
                        {
                            $message.=",".$this->config->item("product_name");
                            $mask=$this->config->item("product_name");
                        }

                        $subject="Want to chat with a human agent";
                        $this->_mail_sender($from, $chat_human_email, $subject, $message,$mask);
                    }
                }
            }
            elseif($payload_id=="YES_START_CHAT_WITH_BOT")
            {
                $this->basic->update_data("messenger_bot_subscriber",array("page_id"=>$page_id,"subscribe_id"=>$sender_id),array("status"=>"1"));
                //added by Konok to eligible the subscribers for bot reply. 
                $subscriber_info[0]['status']=1;

            }

            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){

                $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);

                // insert messenger_bot_message_sent_stat table
                $page_table_id=$messenger_bot_info[0]['page_id'];
                if($button_unique_id != '')
                {
                    $message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$sender_id',$page_table_id,'$button_unique_id','postback',1,0) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1";
                    $this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
                }


                $drip_assign_id=isset($messenger_bot_info[0]['drip_campaign_id']) ? $messenger_bot_info[0]['drip_campaign_id']:"";
                
                if($drip_assign_id!="" && $drip_assign_id!="0"){
                    $drip_type="custom";
                    $this->assign_drip_messaging_id($drip_type,"0",$subscriber_info[0]['page_table_id'],$sender_id,$drip_assign_id);  
                }


               /***Set labels if any setup available for this postback for quickReply ***/

                $label_ids=isset($messenger_bot_info[0]['broadcaster_labels']) ? $messenger_bot_info[0]['broadcaster_labels']:"";
           
                if(!empty($label_ids)){
                
                    //DEPRECATED FUNCTION FOR QUICK BROADCAST
                    $this->multiple_assign_label($sender_id,$page_id,$label_ids,$social_media_type,$subscriber_info[0]['id']);
                } 

                if($this->addon_exist("messenger_bot_connectivity"))                
                 	$this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_postback",$payload_id);

                //update Subscriber Last Interaction time. 
                $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

               exit; 
            }
        }
        
        //Instagram Mention Reply 
        elseif(isset($response['entry'][0]['messaging'][0]['message']['attachments'][0]['type']) && 
                $response['entry'][0]['messaging'][0]['message']['attachments'][0]['type']=="story_mention"){


            $table_name = "messenger_bot";

            $where['where'] = array('messenger_bot.fb_page_id' => $page_id, 'messenger_bot.keyword_type' => 'story-mention','facebook_rx_fb_page_info.bot_enabled' => '1','media_type'=>$social_media_type);

            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'1','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'] ?? "";
            
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){

                 $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);
                 //update Subscriber Last Interaction time. 
                 $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

                die();
            }
        }

        //Instagram Story Private Reply 
        elseif(isset($response['entry'][0]['messaging'][0]['message']['reply_to']['story']['id']) && 
                $response['entry'][0]['messaging'][0]['message']['reply_to']['story']['id']!=""){

            $table_name = "messenger_bot";

            $where['where'] = array('messenger_bot.fb_page_id' => $page_id, 'messenger_bot.keyword_type' => 'story-private-reply','facebook_rx_fb_page_info.bot_enabled' => '1','media_type'=>$social_media_type);

            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'1','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'] ?? "";
            
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){

                 $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);
                 //update Subscriber Last Interaction time. 
                 $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

                die();
            }
        }

        //Instagram Message Unsend Private Reply 
        elseif(isset($response['entry'][0]['messaging'][0]['message']['is_deleted']) && 
                $response['entry'][0]['messaging'][0]['message']['is_deleted']!=""){

            $table_name = "messenger_bot";

            $where['where'] = array('messenger_bot.fb_page_id' => $page_id, 'messenger_bot.keyword_type' => 'message-unsend-private-reply','facebook_rx_fb_page_info.bot_enabled' => '1','media_type'=>$social_media_type);

            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");   

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'1','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'] ?? "";
            
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){

                 $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);
                 //update Subscriber Last Interaction time. 
                 $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);

                die();
            }
        }


        else
        {   
            // Get the message id to extract the attachment. 
            $fb_message_id=$response['entry'][0]['messaging'][0]['message']['mid'];

            /*For gif, sticker, video upload, it gives the attachment details in the json. 
            For file or image upload, it gives only message_id, no attachment. */

            $attachment_url= isset($response['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['url']) ? $response['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['url'] : "" ;
            $attachment_type= isset($response['entry'][0]['messaging'][0]['message']['attachments'][0]['type']) ? $response['entry'][0]['messaging'][0]['message']['attachments'][0]['type'] : "" ;

            $this->user_input_flow_assaign_check($sender_id,$page_id,$subscriber_info,$answer=$attachment_url,$fb_message_id,$message_type=$attachment_type);

            $table_name = "messenger_bot";

            $where['where'] = array('messenger_bot.fb_page_id' => $page_id, 'messenger_bot.keyword_type' => 'no match','facebook_rx_fb_page_info.bot_enabled' => '1','facebook_rx_fb_page_info.no_match_found_reply'=>'enabled','media_type'=>$social_media_type);

            $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot.page_id,left");  

            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","facebook_rx_fb_page_info.page_access_token as page_access_token","facebook_rx_fb_page_info.enable_mark_seen as enable_mark_seen","facebook_rx_fb_page_info.enbale_type_on as enbale_type_on","facebook_rx_fb_page_info.reply_delay_time as reply_delay_time"),$join,'1','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'] ?? "";
            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){
                $this->send_message_bot_reply($messenger_bot_info[0],$sender_id,$subscriber_info,$page_id);
                //update Subscriber Last Interaction time. 
               $this->update_subscriber_last_interaction($sender_id,$currenTime,$subsciber_broadcast_unavailable);
                
                die();
            }
        }
    }


   

    public function index()
    {
        $this->bot_menu_section();
    }
    

    public function switch_to_media()
    {
      $this->ajax_check();
      $media_type = $this->input->post("media_type");
      $this->session->set_userdata('selected_global_media_type',$media_type);
      echo "1";
    }


    //=================================BOT SETTINGS===============================
    public function bot_list($media_type='fb')
    {   
        // echo $this->session->userdata('selected_global_media_type');
        if($this->session->userdata('selected_global_media_type')) {
            $media_type = $this->session->userdata('selected_global_media_type');
        }

        // echo $this->session->userdata('selected_global_page_table_id');exit;

        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        redirect('home/login_page', 'location'); 

    	$this->is_sms_email_drip_exist = $this->addon_exist('sms_email_sequence');
        $this->instagram_bot_addon_exist = true;

        $media_type = $media_type;
        $data['media_type'] = $media_type;

        // if($media_type == 'ig' && !$this->instagram_bot_addon_exist) exit();
        

        $custom_field_exist = 'no';
        if($this->addon_exist("custom_field_manager"))
        {
            $custom_field_exist = 'yes';
            if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
                $custom_field_exist = 'no';
        }
        $data['custom_field_exist'] = $custom_field_exist;
    	
        $data['body'] = 'messenger_tools/bot_list';
        $data['page_title'] = $this->lang->line('Bot Manager');  
        $data['media_icon'] = '<i class="fas fa-robot"></i>';  

        $data['is_media_type_checked'] = "checked";
        if($this->session->userdata("selected_global_media_type") == 'ig') {
            $data['is_media_type_checked'] = "";
        }


        // if($media_type == 'ig') {
        //     $data['page_title'] = $this->lang->line('Instagram Messenger Tools');
        //     $data['media_icon'] = '<i class="fab fa-instagram"></i>';  
        // }

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('bot_enabled' => "1",'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id'=> $this->session->userdata('facebook_rx_fb_user_info'));

        if($this->instagram_bot_addon_exist) {
            if($media_type == "ig") {
                $where['where'] = array('bot_enabled' => "1",'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id'=> $this->session->userdata('facebook_rx_fb_user_info'),"has_instagram"=>"1");
            }
        }

        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id"),$join,'','','page_name asc');
        $error_record = $this->basic->get_data('messenger_bot_reply_error_log',array('where'=>array('user_id'=>$this->user_id)),$select=array('page_id','count(id) as total_error'),$join='',$limit='',$start=NULL,$order_by='',$group_by='page_id');
        $error_record_array = array();
        foreach($error_record as $value)
        {
            $error_record_array[$value['page_id']] = $value['total_error'];
        }
        $data['error_record'] = $error_record_array;
        $len_page_info = count($page_info); 

        $page_list = array();
        $selected_mailchimp_list_ids = array();
        $selected_sendinblue_list_ids = array();
        $selected_activecampaign_list_ids = array();
        $selected_mautic_list_ids = array();
        $selected_acelle_list_ids = array();
        $sms_api_id = 0;
        $sms_reply_message = '';

        $sequence_sms_api_id = 0;
        $sequence_email_api_id = 0;
        $sequence_sms_campaign_id = 0;
        $sequence_email_campaign_id = 0;

        if(!empty($page_info))
        {
            $i = 1;
            $selected_page_id = $this->session->userdata('selected_global_page_table_id');
            foreach($page_info as $value)
            {
                if($value['id'] == $selected_page_id)
                {
                    if($value['mail_service_id'] != '')
                    {
                        $mail_service_id = json_decode($value['mail_service_id'],true);
                        $selected_mailchimp_list_ids = isset($mail_service_id['mailchimp']) ? $mail_service_id['mailchimp']:"";
                        $selected_sendinblue_list_ids = isset($mail_service_id['sendinblue']) ? $mail_service_id['sendinblue']:"";
                        $selected_activecampaign_list_ids = isset($mail_service_id['activecampaign']) ? $mail_service_id['activecampaign']:"";
                        $selected_mautic_list_ids = isset($mail_service_id['mautic']) ? $mail_service_id['mautic']:"";
                        $selected_acelle_list_ids = isset($mail_service_id['acelle']) ? $mail_service_id['acelle']:"";
                    }
                    $page_list[0] = $value;

                    $sms_api_id = $value['sms_api_id'];
                    $sms_reply_message = $value['sms_reply_message'];

                    $sequence_sms_api_id = $value['sequence_sms_api_id'];
                    $sequence_email_api_id = $value['sequence_email_api_id'];
                }
                else $page_list[$i] = $value;
                $i++;
            }
        }
        ksort($page_list);
        $data['page_info'] = $page_list;
        $data['sms_api_id'] = $sms_api_id;
        $data['sms_reply_message'] = $sms_reply_message;

        $data['sequence_sms_api_id'] = $sequence_sms_api_id;
        $data['sequence_email_api_id'] = $sequence_email_api_id;

        $data['package_list'] = $this->package_list(); // get user package

        // get eligible saved templates
        if($this->db->table_exists('messenger_bot_saved_templates')) 
        {
            if ($this->session->userdata("user_type")=="Member") 
            {
                $package_info=$this->session->userdata('package_info');
                $search_package_id=isset($package_info['id'])?$package_info['id']:'0';
                $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public') OR (template_access='private' AND user_id='".$this->user_id."'))";
            }
            else $where_custom="user_id='".$this->user_id."'";        

            $this->db->select('*');
            $this->db->where( $where_custom );
            $this->db->order_by("saved_at DESC");
            $query = $this->db->get('messenger_bot_saved_templates');
            $template_data=$query->result_array();
            $data["saved_template_list"]=$template_data;
        }
        else $data["saved_template_list"]=array();
        // ----------------------------------
        
        $join = array('mailchimp_list'=>"mailchimp_config.id=mailchimp_list.mailchimp_config_id,right");
        $mailchimp_info = $this->basic->get_data('mailchimp_config',array('where'=>array('user_id'=>$this->user_id,'service_type'=>'mailchimp')),array("list_name","list_id","tracking_name","mailchimp_list.id","mailchimp_config.id as config_id"),$join);
        
        $mailchimp_list=array();
        $i=0;
        foreach($mailchimp_info as $key => $value) 
        {
           $mailchimp_list[$value["config_id"]]["tracking_name"]=$value['tracking_name'];
           $mailchimp_list[$value["config_id"]]["data"][$i]["list_name"]=$value['list_name'];
           $mailchimp_list[$value["config_id"]]["data"][$i]["list_id"]=$value['list_id'];
           $mailchimp_list[$value["config_id"]]["data"][$i]["table_id"]=$value['id'];
           $i++;
        }
        
        $data['mailchimp_list'] = $mailchimp_list;
        $data['selected_mailchimp_list_ids'] = $selected_mailchimp_list_ids;


        /* sendinblue */
        $join = array('mailchimp_list'=>"mailchimp_config.id=mailchimp_list.mailchimp_config_id,right");
        $sendinblue_info = $this->basic->get_data('mailchimp_config',array('where'=>array('user_id'=>$this->user_id,'service_type'=>'sendinblue')),array("list_name","list_id","tracking_name","mailchimp_list.id","mailchimp_config.id as config_id"),$join);
        
        $sendinblue_list=array();
        $i=0;
        foreach($sendinblue_info as $key => $value) 
        {
           $sendinblue_list[$value["config_id"]]["tracking_name"]=$value['tracking_name'];
           $sendinblue_list[$value["config_id"]]["data"][$i]["list_name"]=$value['list_name'];
           $sendinblue_list[$value["config_id"]]["data"][$i]["list_id"]=$value['list_id'];
           $sendinblue_list[$value["config_id"]]["data"][$i]["table_id"]=$value['id'];
           $i++;
        }
        $data['sendinblue_list'] = $sendinblue_list;
        $data['selected_sendinblue_list_ids'] = $selected_sendinblue_list_ids;


        /* Activecampaign */
        $join = array('mailchimp_list'=>"mailchimp_config.id=mailchimp_list.mailchimp_config_id,right");
        $activecampaign_info = $this->basic->get_data('mailchimp_config',array('where'=>array('user_id'=>$this->user_id,'service_type'=>'activecampaign')),array("list_name","list_id","tracking_name","mailchimp_list.id","mailchimp_config.id as config_id"),$join);
        
        $activecampaign_list=array();
        $i=0;
        foreach($activecampaign_info as $key => $value) 
        {
           $activecampaign_list[$value["config_id"]]["tracking_name"]=$value['tracking_name'];
           $activecampaign_list[$value["config_id"]]["data"][$i]["list_name"]=$value['list_name'];
           $activecampaign_list[$value["config_id"]]["data"][$i]["list_id"]=$value['list_id'];
           $activecampaign_list[$value["config_id"]]["data"][$i]["table_id"]=$value['id'];
           $i++;
        }
        $data['activecampaign_list'] = $activecampaign_list;
        $data['selected_activecampaign_list_ids'] = $selected_activecampaign_list_ids;


        /* Mautic */
        $join = array('mailchimp_list'=>"mailchimp_config.id=mailchimp_list.mailchimp_config_id,right");
        $mautic_info = $this->basic->get_data('mailchimp_config',array('where'=>array('user_id'=>$this->user_id,'service_type'=>'mautic')),array("list_name","list_id","tracking_name","mailchimp_list.id","mailchimp_config.id as config_id"),$join);
        
        $mautic_list=array();
        $i=0;
        foreach($mautic_info as $key => $value) 
        {
           $mautic_list[$value["config_id"]]["tracking_name"]=$value['tracking_name'];
           $mautic_list[$value["config_id"]]["data"][$i]["list_name"]=$value['list_name'];
           $mautic_list[$value["config_id"]]["data"][$i]["list_id"]=$value['list_id'];
           $mautic_list[$value["config_id"]]["data"][$i]["table_id"]=$value['id'];
           $i++;
        }
        $data['mautic_list'] = $mautic_list;
        $data['selected_mautic_list_ids'] = $selected_mautic_list_ids;


        /* Acelle */
        $join = array('mailchimp_list'=>"mailchimp_config.id=mailchimp_list.mailchimp_config_id,right");
        $acelle_info = $this->basic->get_data('mailchimp_config',array('where'=>array('user_id'=>$this->user_id,'service_type'=>'acelle')),array("list_name","list_id","tracking_name","mailchimp_list.id","mailchimp_config.id as config_id"),$join);
        
        $acelle_list=array();
        $i=0;
        foreach($acelle_info as $key => $value) 
        {
           $acelle_list[$value["config_id"]]["tracking_name"]=$value['tracking_name'];
           $acelle_list[$value["config_id"]]["data"][$i]["list_name"]=$value['list_name'];
           $acelle_list[$value["config_id"]]["data"][$i]["list_id"]=$value['list_id'];
           $acelle_list[$value["config_id"]]["data"][$i]["table_id"]=$value['id'];
           $i++;
        }
        $data['acelle_list'] = $acelle_list;
        $data['selected_acelle_list_ids'] = $selected_acelle_list_ids;


        /***get sms config***/
        $temp_userid = $this->user_id;
        $apiAccess = $this->config->item('sms_api_access');
        if($this->config->item('sms_api_access') == "") $apiAccess = "0";

        if(isset($apiAccess) && $apiAccess == '1' && $this->session->userdata("user_type") == 'Member')
        {
            $join = array('users' => 'sms_api_config.user_id=users.id,left');
            $select = array('sms_api_config.*','users.id AS usersId','users.user_type');
            $where_in = array('sms_api_config.user_id'=>array('1',$temp_userid),'users.user_type'=>array('Admin','Member'));
            $where = array('where'=> array('sms_api_config.status'=>'1'),'where_in'=>$where_in);
            $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select, $join, $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        } else
        {
            $where = array("where" => array('user_id'=>$temp_userid,'status'=>'1'));
            $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select='', $join='', $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        }

        $sms_api_config_option=array();
        foreach ($sms_api_config as $info) {
            $id=$info['id'];

            if ($info['gateway_name'] == 'custom') {
                $info['gateway_name'] = $this->lang->line("Custom"). ' : '. $info['custom_name'];
            }

            if($info['phone_number'] !="")
                $sms_api_config_option[$id]=$info['gateway_name'].": ".$info['phone_number'];
            else
                $sms_api_config_option[$id]=$info['gateway_name'];
        }
        $data['sms_option'] = $sms_api_config_option;


        /***get smtp  option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_smtp_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        $smtp_option=array();
        foreach ($smtp_info as $info) {
            $id="email_smtp_config_".$info['id'];
            $smtp_option[$id]="SMTP: ".$info['email_address'];
        }
        
        /***get mandrill option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_mandrill_config_".$info['id'];
            $smtp_option[$id]="Mandrill: ".$info['email_address'];
        }

        /***get sendgrid option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_sendgrid_config_".$info['id'];
            $smtp_option[$id]="SendGrid: ".$info['email_address'];
        }

        /***get mailgun option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="email_mailgun_config_".$info['id'];
            $smtp_option[$id]="Mailgun: ".$info['email_address'];
        }
        $data['email_apis'] = $smtp_option;


        $this->_viewcontroller($data);   
    }


    public function get_page_details()
    {
        $this->ajax_check();
        $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
        $this->is_engagement_exist=$this->engagement_exist();
        $page_table_id = $this->input->post('page_table_id',true);
        $media_type = $this->input->post("media_type",true);
        $hide_sections = "";
        $only_for_ig = 'hidden';
        if(isset($media_type) && $media_type == "ig") {
            $hide_sections = "hidden";
            $only_for_ig = 'show';
        }

        $facebook_rx_fb_user_info_id  =  $this->session->userdata('facebook_rx_fb_user_info');
        $this->session->set_userdata('selected_global_page_table_id',$page_table_id);

        $where = array();
        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('facebook_rx_fb_user_info_id' => $facebook_rx_fb_user_info_id,'id'=>$page_table_id);
        $page_info = $this->basic->get_data($table_name,$where,'','','','','page_name asc');

        // ice breaker block
        if($media_type == 'ig')
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_table_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'ig')),'','','',$start=NULL,'id DESC');
        else
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_table_id,"is_template"=>"1",'template_for'=>'reply_message')),'','','',$start=NULL,'id DESC');

        $push_postback="";
        $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        foreach ($postback_data as $key => $value) 
        {
            $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }

        if($media_type == 'ig')
        {
            $response['ice_breaker_status'] = $page_info[0]['ig_ice_breaker_status'];
            $ice_breaker_questions = [];
            $ice_breaker_info = $page_info[0]['ig_ice_breaker_questions'];
            if($ice_breaker_info != '')
                $ice_breaker_questions = json_decode($page_info[0]['ig_ice_breaker_questions'],true);
        }
        else
        {
            $response['ice_breaker_status'] = $page_info[0]['ice_breaker_status'];
            $ice_breaker_questions = [];
            $ice_breaker_info = $page_info[0]['ice_breaker_questions'];
            if($ice_breaker_info != '')
                $ice_breaker_questions = json_decode($page_info[0]['ice_breaker_questions'],true);
        }
        
        $ice_breaker_html = '';
        $question_block_counter = 0;
        $id_counter = 1;
        foreach($ice_breaker_questions as $value)
        {
            $ice_breaker_html .= '
                <div class="single_question_block">
                  <p class="clearfix"><b>'.$this->lang->line('Question Block').'</b> <button class="btn btn-sm btn-outline-secondary float-right remove_question_div"><i class="fas fa-times"></i> '.$this->lang->line('Remove').'</button></p>
                  <div class="input-group" style="margin-bottom: 5px;">                            
                    <div class="input-group-prepend">
                      <div class="input-group-text" style="font-weight: bold;">
                        '.$this->lang->line("Type your question").'
                      </div>
                    </div>
                    <input class="form-control" type="text" name="questions[]" value="'.$value['questions'].'">
                  </div>
                  <div class="input-group">                            
                    <div class="input-group-prepend">
                      <div class="input-group-text" style="font-weight: bold;">
                        '.$this->lang->line("Reply Message Template").'
                      </div>
                    </div>
                    <select class="form-control" id="select_tag_id_'.$id_counter.'" name="question_replies[]">
                        <option value="">'.$this->lang->line("Select").'</option>';
                        foreach ($postback_data as $postbacks)
                        {
                            if($value['question_replies'] == $postbacks['postback_id'])
                                $selected = 'selected';
                            else
                                $selected = '';
                            $ice_breaker_html.="<option value='".$postbacks['postback_id']."' ".$selected.">".$postbacks['template_name'].' ['.$postbacks['postback_id'].']'."</option>";
                        }

            $ice_breaker_html .= '</select>
                  </div>
                  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     '.$this->lang->line("Add Message Template").'</a>
                  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> '.$this->lang->line("Refresh List").'</a>
                  <br/>
                </div>';
            $question_block_counter++;
            $id_counter++;
        }

        if($ice_breaker_html == '')
            $ice_breaker_html .= '
                <div class="single_question_block">
                  <p class="clearfix"><b>'.$this->lang->line('Question Block').'</b> <button class="btn btn-sm btn-outline-secondary float-right remove_question_div"><i class="fas fa-times"></i> '.$this->lang->line('Remove').'</button></p>
                  <div class="input-group" style="margin-bottom: 5px;">                            
                    <div class="input-group-prepend">
                      <div class="input-group-text" style="font-weight: bold;">
                        '.$this->lang->line("Type your question").'
                      </div>
                    </div>
                    <input class="form-control" type="text" name="questions[]">
                  </div>
                  <div class="input-group">                            
                    <div class="input-group-prepend">
                      <div class="input-group-text" style="font-weight: bold;">
                        '.$this->lang->line("Reply Message Template").'
                      </div>
                    </div>
                    <select class="form-control" id="select_tag_id_1" name="question_replies[]">
                    '.$push_postback.'
                    </select>
                  </div>
                  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     '.$this->lang->line("Add Message Template").'</a>
                  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> '.$this->lang->line("Refresh List").'</a>
                  <br/>
                </div>';

        if($question_block_counter == 0)
            $ice_breaker_html .= '
                <div class="clearfix add_more_question_block">
                    <input type="hidden" id="question_block_counter" value="1">
                    <button class="btn btn-outline-primary float-right" id="add_more_question_button"><i class="fa fa-plus-circle"></i> '.$this->lang->line('Add more question').'</button>
                </div>';
        else
            $ice_breaker_html .= '
                <div class="clearfix add_more_question_block">
                    <input type="hidden" id="question_block_counter" value="'.$question_block_counter.'">
                    <button class="btn btn-outline-primary float-right" id="add_more_question_button"><i class="fa fa-plus-circle"></i> '.$this->lang->line('Add more question').'</button>
                </div>';
        $response['ice_breaker_html'] = $ice_breaker_html;
        // end of ice breaker block 

        $mail_service_id = json_decode($page_info[0]['mail_service_id'],true);
        $selected_mailchimp_list_ids = isset($mail_service_id['mailchimp']) ? $mail_service_id['mailchimp'] : array();
        $selected_sendinblue_list_ids = isset($mail_service_id['sendinblue']) ? $mail_service_id['sendinblue'] : array();
        $selected_activecampaign_list_ids = isset($mail_service_id['activecampaign']) ? $mail_service_id['activecampaign'] : array();
        $selected_mautic_list_ids = isset($mail_service_id['mautic']) ? $mail_service_id['mautic'] : array();
        $selected_acelle_list_ids = isset($mail_service_id['acelle']) ? $mail_service_id['acelle'] : array();


        $subscription_messaging_permission_str = '';
        if($page_info[0]['review_status'] == 'PENDING')
            $subscription_messaging_permission_str = '<a class="badge badge-status orange"><i class="fas fa-hourglass-start"></i> '.$this->lang->line('Pending').'</a>';
        else if($page_info[0]['review_status'] == 'REJECTED')
            $subscription_messaging_permission_str = '<a class="badge badge-status red"><i class="fas fa-ban"></i> '.$this->lang->line('Rejected').'</a>';
        else if($page_info[0]['review_status'] == 'APPROVED')
            $subscription_messaging_permission_str = '<a class="badge badge-status blue"><i class="fas fa-check-circle"></i> '.$this->lang->line("Approved").'</a>';
        else if($page_info[0]['review_status'] == 'LIMITED')
            $subscription_messaging_permission_str = '<a class="badge badge-status blue"><i class="fas fa-lock"></i> '.$this->lang->line("Limited").'</a>';
        else
            $subscription_messaging_permission_str = '<a class="badge badge-status"><i class="fas fa-times-circle"></i> '.$this->lang->line('Not Submitted').'</a>';

        if($page_info[0]['estimated_reach'] != '')
            $estimated_reach = custom_number_format($page_info[0]['estimated_reach']);
        else
            $estimated_reach = 0;

        $where = array();
        $table_name = "messenger_bot_subscriber";
        $where['where'] = array('user_id' => $this->user_id, 'page_table_id' => $page_info[0]['id'],"social_media"=>"fb");
        if($media_type == "ig") {
            $where['where'] = array('user_id' => $this->user_id, 'page_table_id' => $page_info[0]['id'],"social_media"=>"ig");
        }

        $sub_count = $this->basic->get_data($table_name,$where,'id');
        $subscriber_count = count($sub_count);

        $error_record = $this->basic->get_data('messenger_bot_reply_error_log',array('where'=>array('user_id' => $this->user_id, 'page_id' => $page_info[0]['id'],"media_type"=>"fb")),$select=array('id'));

        if($media_type == "ig") {
            $error_record = $this->basic->get_data('messenger_bot_reply_error_log',array('where'=>array('user_id' => $this->user_id, 'page_id' => $page_info[0]['id'],"media_type"=>"ig")),$select=array('id'));
        }
        $error_count = count($error_record);

        if($this->addon_exist("visual_flow_builder"))
            $flowbuilder_exist = 'yes';
        else
            $flowbuilder_exist = 'no';

        $getstarted_info = $this->basic->get_data("messenger_bot",array("where"=>array("keyword_type"=>"get-started","user_id"=>$this->user_id,"page_id"=>$page_table_id)));
        $gid=$gurl='';
        if(isset($getstarted_info[0]['id'])) $gid = $getstarted_info[0]['id'];

        if($flowbuilder_exist == 'no' || $gid == '')
            $gurl = base_url("messenger_bot/edit_bot/").$gid."/1/getstart";
        else
        {
            if($gid!='' && $getstarted_info[0]['visual_flow_type']=='general') 
                $gurl = base_url("messenger_bot/edit_bot/").$gid."/1/getstart";
            else
                $gurl = base_url("visual_flow_builder/edit_builder_data/").$getstarted_info[0]['visual_flow_campaign_id']."/1";
        }
        $response['getstarted_button_edit_url'] = $gurl;

        $story_mention_url = $story_private_reply_url = $message_unsend_private_reply_url = '';
        $story_mention_id = '0-'.$page_table_id;
        $story_private_reply_id = '0-'.$page_table_id;
        $message_unsend_private_reply_id = '0-'.$page_table_id;
        if($media_type == 'ig')
        {
            $nomatch_info = $this->basic->get_data("messenger_bot",array("where"=>array("keyword_type"=>"no match","user_id"=>$this->user_id,"page_id"=>$page_table_id,"media_type"=>"ig")));
            $nurl='';
            $nid='0-'.$page_table_id;
            if(!empty($nomatch_info))
            {
                if(isset($nomatch_info[0]['id'])) $nid = $nomatch_info[0]['id'];

                if($flowbuilder_exist == 'no' || $nid == '')
                    $nurl = base_url("messenger_bot/edit_bot/").$nid."/1/nomatch/".$media_type;
                else
                {
                    if($nid!='' && (isset($nomatch_info[0]['visual_flow_type']) && $nomatch_info[0]['visual_flow_type']=='general')) 
                        $nurl = base_url("messenger_bot/edit_bot/").$nid."/1/nomatch/".$media_type;
                    else
                        $nurl = base_url("visual_flow_builder/edit_builder_data/").$nomatch_info[0]['visual_flow_campaign_id']."/1/".$media_type;
                }
            }
            else
               $nurl = base_url("messenger_bot/edit_bot/").$nid."/1/nomatch/".$media_type; 

            $story_mention_info = $this->basic->get_data("messenger_bot",array("where"=>array("keyword_type"=>"story-mention","user_id"=>$this->user_id,"page_id"=>$page_table_id,"media_type"=>"ig")));
            if(!empty($story_mention_info))
            {
                if(isset($story_mention_info[0]['id'])) $story_mention_id = $story_mention_info[0]['id'];

                if($flowbuilder_exist == 'no' || $story_mention_id == '')
                    $story_mention_url = base_url("messenger_bot/edit_bot/").$story_mention_id."/1/story-mention/".$media_type;
                else
                {
                    if($story_mention_id!='' && $story_mention_info[0]['visual_flow_type']=='general') 
                        $story_mention_url = base_url("messenger_bot/edit_bot/").$story_mention_id."/1/story-mention/".$media_type;
                    else
                        $story_mention_url = base_url("visual_flow_builder/edit_builder_data/").$story_mention_info[0]['visual_flow_campaign_id']."/0/".$media_type;
                }
            }
            else {
              $story_mention_url = base_url("messenger_bot/edit_bot/").$story_mention_id."/1/story-mention/".$media_type;  
            }


            $story_private_reply_info = $this->basic->get_data("messenger_bot",array("where"=>array("keyword_type"=>"story-private-reply","user_id"=>$this->user_id,"page_id"=>$page_table_id,"media_type"=>"ig")));
            if(!empty($story_private_reply_info))
            {
                if(isset($story_private_reply_info[0]['id'])) $story_private_reply_id = $story_private_reply_info[0]['id'];

                if($flowbuilder_exist == 'no' || $story_private_reply_id == '')
                    $story_private_reply_url = base_url("messenger_bot/edit_bot/").$story_private_reply_id."/1/story-private-reply/".$media_type;
                else
                {
                    if($story_private_reply_id!='' && $story_private_reply_info[0]['visual_flow_type']=='general') 
                        $story_private_reply_url = base_url("messenger_bot/edit_bot/").$story_private_reply_id."/1/story-private-reply/".$media_type;
                    else
                        $story_private_reply_url = base_url("visual_flow_builder/edit_builder_data/").$story_private_reply_info[0]['visual_flow_campaign_id']."/0/".$media_type;
                }
            }
            else {
              $story_private_reply_url = base_url("messenger_bot/edit_bot/").$story_private_reply_id."/1/story-private-reply/".$media_type;  
            }


            $message_unsend_private_reply_info = $this->basic->get_data("messenger_bot",array("where"=>array("keyword_type"=>"message-unsend-private-reply","user_id"=>$this->user_id,"page_id"=>$page_table_id,"media_type"=>"ig")));
            if(!empty($message_unsend_private_reply_info))
            {
                if(isset($message_unsend_private_reply_info[0]['id'])) $message_unsend_private_reply_id = $message_unsend_private_reply_info[0]['id'];

                if($flowbuilder_exist == 'no' || $message_unsend_private_reply_id == '')
                    $message_unsend_private_reply_url = base_url("messenger_bot/edit_bot/").$message_unsend_private_reply_id."/1/story-private-reply/".$media_type;
                else
                {
                    if($message_unsend_private_reply_id!='' && $message_unsend_private_reply_info[0]['visual_flow_type']=='general') 
                        $message_unsend_private_reply_url = base_url("messenger_bot/edit_bot/").$message_unsend_private_reply_id."/1/story-private-reply/".$media_type;
                    else
                        $message_unsend_private_reply_url = base_url("visual_flow_builder/edit_builder_data/").$message_unsend_private_reply_info[0]['visual_flow_campaign_id']."/0/".$media_type;
                }
            }
            else {
              $message_unsend_private_reply_url = base_url("messenger_bot/edit_bot/").$message_unsend_private_reply_id."/1/story-private-reply/".$media_type;  
            }
        }
        else
        {
            $nomatch_info = $this->basic->get_data("messenger_bot",array("where"=>array("keyword_type"=>"no match","user_id"=>$this->user_id,"page_id"=>$page_table_id,"media_type"=>"fb")));
            $nid=$nurl='';
            if(isset($nomatch_info[0]['id'])) $nid = $nomatch_info[0]['id'];

            if($flowbuilder_exist == 'no' || $nid == '')
                $nurl = base_url("messenger_bot/edit_bot/").$nid."/1/nomatch";
            else
            {
                if($nid!='' && $nomatch_info[0]['visual_flow_type']=='general') 
                    $nurl = base_url("messenger_bot/edit_bot/").$nid."/1/nomatch";
                else
                    $nurl = base_url("visual_flow_builder/edit_builder_data/").$nomatch_info[0]['visual_flow_campaign_id']."/1";
            }
        }


        $action_button_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_table_id,"template_for !="=>'reply_message')));
        $uurl=$rurl=$eurl=$purl=$lurl=$hurl=$burl=$jurl='';
        foreach($action_button_info as $key => $value) 
        {
            if($flowbuilder_exist == 'yes' && $value['visual_flow_type']=='flow')
            {
                if($value['template_for']=='unsubscribe') $uurl=base_url("visual_flow_builder/edit_builder_data/").$value['visual_flow_campaign_id']."/1";
                else if($value['template_for']=='resubscribe') $rurl=base_url("visual_flow_builder/edit_builder_data/").$value['visual_flow_campaign_id']."/1";
                else if($value['template_for']=='email-quick-reply') $eurl=base_url("visual_flow_builder/edit_builder_data/").$value['visual_flow_campaign_id']."/1";         
                else if($value['template_for']=='phone-quick-reply') $purl=base_url("visual_flow_builder/edit_builder_data/").$value['visual_flow_campaign_id']."/1";         
                else if($value['template_for']=='location-quick-reply') $lurl=base_url("visual_flow_builder/edit_builder_data/").$value['visual_flow_campaign_id']."/1";         
                else if($value['template_for']=='birthday-quick-reply') $jurl=base_url("visual_flow_builder/edit_builder_data/").$value['visual_flow_campaign_id']."/1";         
                else if($value['template_for']=='chat-with-human') $hurl=base_url("visual_flow_builder/edit_builder_data/").$value['visual_flow_campaign_id']."/1";        
                else if($value['template_for']=='chat-with-bot') $burl=base_url("visual_flow_builder/edit_builder_data/").$value['visual_flow_campaign_id']."/1";
            }
            else
            {
                if($value['template_for']=='unsubscribe') $uurl=base_url('messenger_bot/edit_template/').$value['id'].'/1/default';
                else if($value['template_for']=='resubscribe') $rurl=base_url('messenger_bot/edit_template/').$value['id'].'/1/default';
                else if($value['template_for']=='email-quick-reply') $eurl=base_url('messenger_bot/edit_template/').$value['id'].'/1/default'; 
                else if($value['template_for']=='phone-quick-reply') $purl=base_url('messenger_bot/edit_template/').$value['id'].'/1/default';
                else if($value['template_for']=='location-quick-reply') $lurl=base_url('messenger_bot/edit_template/').$value['id'].'/1/default';
                else if($value['template_for']=='birthday-quick-reply') $jurl=base_url('messenger_bot/edit_template/').$value['id'].'/1/default';
                else if($value['template_for']=='chat-with-human') $hurl=base_url('messenger_bot/edit_template/').$value['id'].'/1/default';
                else if($value['template_for']=='chat-with-bot') $burl=base_url('messenger_bot/edit_template/').$value['id'].'/1/default';
            }

        }

        $murl = base_url('messenger_bot/persistent_menu_list/').$page_table_id.'/1';
        $durl = base_url('messenger_bot_enhancers/sequence_message_campaign/').$page_table_id.'/1/'.$media_type;
        $ses_url = base_url('sms_email_sequence/sms_email_sequence_message_campaign/').$page_table_id.'/1';
        $surl = base_url('subscriber_manager/bot_subscribers/0/').$page_table_id;

        $user_input_url = $custom_field_url = '';
        if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) {
            $user_input_url = base_url('custom_field_manager/campaign_list/').$page_table_id.'/1/'.$media_type;
            $custom_field_url = base_url('custom_field_manager/custom_field_list/').$page_table_id.'/1/'.$media_type;
        }

        $bot_reply_settings_url = base_url("messenger_bot/bot_settings/").$page_info[0]['id']."/1";
        $postback_manager_url = base_url("messenger_bot/template_manager/").$page_info[0]['id']."/1";
        $whitelisted_domains_url = base_url("messenger_bot/domain_whitelist/").$page_info[0]['id']."/1";
        $middle_page_account_url = '<i class="fab fa-facebook-square"></i> <a target="_BLANK" href="https://facebook.com/'.$page_info[0]['page_id'].'">'.$page_info[0]['page_name'].'</a>';

        $bot_flow_settings_url = base_url('visual_flow_builder/flowbuilder_manager/').$page_info[0]['id']."/1";

        $nomatch_reply_found_val = $page_info[0]['no_match_found_reply'];
        $chatwith_human_email = $page_info[0]['chat_human_email'];
        if($media_type == "ig") {
            $bot_reply_settings_url = base_url("messenger_bot/ig_bot_settings/").$page_info[0]['id']."/1/".$media_type;
            $middle_page_account_url = '<i class="fab fa-instagram"></i> <a target="_BLANK" href="https://www.instagram.com/'.$page_info[0]['insta_username'].'">'.$page_info[0]['insta_username'].'</a>';

            $nomatch_reply_found_val = $page_info[0]['ig_no_match_found_reply'];
            $chatwith_human_email = $page_info[0]['ig_chat_human_email'];

        }

        $middle_column_content='
        <div class="card main_card">
          <div class="card-header padding-left-10">
            <h4 class="put_page_name_url">'.$middle_page_account_url.'</h4>
          </div>
          <div class="card-body padding-10">
            <div class="row">
              <div class="col-12">
                <div class="card card-large-icons card-condensed active">
                  <div class="card-icon">
                    <i class="fas fa-robot"></i>
                  </div>
                  <div class="card-body">
                    <h4>'.$this->lang->line("Bot Flow Builder").'</h4>                    
                    <a href="'.$bot_flow_settings_url.'" data-page-id="'.$page_info[0]['id'].'" data-height="795" class="card-cta iframed" id="bot_flow_settings">'.$this->lang->line("Change Settings").'</a>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="card card-large-icons card-condensed active">
                  <div class="card-icon">
                    <i class="fas fa-tag"></i>
                  </div>
                  <div class="card-body">
                    <h4>'.$this->lang->line("Bot Keyword Settings").'</h4>                    
                    <a href="'.$bot_reply_settings_url.'" data-page-id="'.$page_info[0]['id'].'" data-height="795" class="card-cta iframed" id="reply_settings">'.$this->lang->line("Change Settings").'</a>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="card card-large-icons card-condensed active">
                  <div class="card-icon">
                    <i class="fas fa-exchange-alt"></i>
                  </div>
                  <div class="card-body">
                    <h4>'.$this->lang->line("Postback Manager").'</h4>                    
                    <a href="'.$postback_manager_url.'" data-page-id="'.$page_info[0]['id'].'" data-height="795" class="card-cta iframed" id="postback_manager">'.$this->lang->line("Change Settings").'</a>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="card card-large-icons card-condensed">
                  <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <div class="card-body">
                    <h4>'.$this->lang->line("Get Started Settings").'</h4>                    
                    <a href="" id="get_started_settings" data-page-id="'.$page_table_id.'" class="card-cta enable_start_button" welcome-message="'.htmlspecialchars($page_info[0]['welcome_message']).'" sbutton-enable="'.$page_info[0]['id'].'" sbutton-status="'.$page_info[0]['started_button_enabled'].'" >'.$this->lang->line("Change Settings").'</a>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="card card-large-icons card-condensed">
                  <div class="card-icon">
                    <i class="fas fa-cog"></i>
                  </div>
                  <div class="card-body">
                    <h4>'.$this->lang->line("General Settings").'</h4>                    
                    <a href="" id="general_settings" class="card-cta enable_general_settings" chat_human_email="'.$chatwith_human_email.'" table_id="'.$page_info[0]['id'].'" mark_seen_status="'.$page_info[0]['enable_mark_seen'].'" no_match_found_reply="'.$nomatch_reply_found_val.'" >'.$this->lang->line("Change Settings").'</a>

                  </div>
                </div>
              </div>';

            $middle_column_content .='<div class="col-12">
                <div class="card card-large-icons card-condensed">
                    <div class="card-icon">
                        <i class="far fa-hand-pointer"></i>
                    </div>
                    <div class="card-body">
                        <h4>'.$this->lang->line("Action Button Settings").'</h4>
                        <a href="" data-page-id="'.$page_info[0]['id'].'" class="card-cta action_button_settings" id="action_button_settings">'.$this->lang->line("Change Settings").'</a>
                    </div>
                </div>
            </div>';

            $common_str = '
                <div class="col-12 col-md-6">
                    <a href="#URL#" class="pointer #ISIFRAME#" data-height="795" target="_BLANK">
                      <div class="card card-large-icons card-condensed bg-body">
                        <div class="card-icon" style="width:70px !important;">
                          <i class="#ICON#"></i>
                        </div>
                        <div class="card-body">
                          <h4>'.$this->lang->line("#TITLE#").'</h4>
                        </div>
                      </div>
                    </a>
                </div>';

            $action_buttons_str = '';
            if($media_type == "fb" || $media_type == "") {

                if(stripos($gurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$gurl,'','fas fa-check-circle text-primary','Get-started Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$gurl,'iframed','fas fa-check-circle text-primary','Get-started Template'], $common_str);
                }

                if(stripos($nurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$nurl,'','fas fa-comment-slash text-success','No Match Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$nurl,'iframed','fas fa-comment-slash text-success','No Match Template'], $common_str);
                }

                if(stripos($uurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$uurl,'','fas fa-user-slash text-warning','Un-subscribe Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$uurl,'iframed','fas fa-user-slash text-warning','Un-subscribe Template'], $common_str);
                }

                if(stripos($rurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$rurl,'','fas fa-user-circle text-info','Re-subscribe Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$rurl,'iframed','fas fa-user-circle text-info','Re-subscribe Template'], $common_str);
                }

                if(stripos($eurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$eurl,'','fas fa-envelope text-danger','Email Quick Reply Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$eurl,'iframed','fas fa-envelope text-danger','Email Quick Reply Template'], $common_str);
                }

                if(stripos($purl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$purl,'','fas fa-phone text-success','Phone Quick Reply Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$purl,'iframed','fas fa-phone text-success','Phone Quick Reply Template'], $common_str);
                }

                if(stripos($lurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$lurl,'','fas fa-map text-primary','Location Quick Reply Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$lurl,'iframed','fas fa-map text-primary','Location Quick Reply Template'], $common_str);
                }

                if(stripos($jurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$jurl,'','fas fa-birthday-cake text-warning','Birthday Quick Reply Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$jurl,'iframed','fas fa-birthday-cake text-warning','Birthday Quick Reply Template'], $common_str);
                }

                if(stripos($hurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$hurl,'','fas fa-headset text-info','Chat with Human Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$hurl,'iframed','fas fa-headset text-info','Chat with Human Template'], $common_str);
                }

                if(stripos($burl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$burl,'','fas fa-robot text-danger','Chat with Robot Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$burl,'iframed','fas fa-robot text-danger','Chat with Robot Template'], $common_str);
                }
            }

            if($media_type == "ig") {
                
                if(stripos($nurl,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$nurl,'','fas fa-comment-slash text-primary','No Match Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$nurl,'iframed','fas fa-comment-slash text-primary','No Match Template'], $common_str);
                }
                
                if(stripos($story_mention_url,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$story_mention_url,'','fas fa-user-tag text-success','Story Mention Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$story_mention_url,'iframed','fas fa-user-tag text-success','Story Mention Template'], $common_str);
                }
                
                if(stripos($story_private_reply_url,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$story_private_reply_url,'','fas fa-reply text-warning','Story Private Reply Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$story_private_reply_url,'iframed','fas fa-reply text-warning','Story Private Reply Template'], $common_str);
                }
                
                if(stripos($message_unsend_private_reply_url,"visual_flow_builder/edit_builder_data")) {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$message_unsend_private_reply_url,'','fas fa-bell-slash text-danger','Message Unsend Private Reply Template'], $common_str);
                }
                else {
                    $action_buttons_str .= str_replace(['#URL#','#ISIFRAME#','#ICON#','#TITLE#'], [$message_unsend_private_reply_url,'iframed','fas fa-bell-slash text-danger','Message Unsend Private Reply Template'], $common_str);
                }
            }

            $response['action_buttons_str'] = $action_buttons_str;
              
         if($this->session->userdata('user_type') == 'Admin' || in_array(197,$this->module_access)) : 
            $pm_str="";
            if($page_info[0]['persistent_enabled']=='1') $pm_str='<small class="badge badge-status green">'.$this->lang->line("Published").'</small>';
            $middle_column_content .='
              <div class="col-12 '.$hide_sections.'">
                <div class="card card-large-icons card-condensed">
                  <div class="card-icon">
                    <i class="fas fa-bars"></i>
                  </div>
                  <div class="card-body">
                    <h4>'.$this->lang->line("Persistent Menu Settings").'</h4>                    
                    <a href="'.$murl.'" class="card-cta iframed" data-height="795">'.$this->lang->line("Change Settings").' '.$pm_str.'</a>
                  </div>
                </div>
              </div>';
        endif;
        
        $middle_column_content .='</div>';
            
        if($this->is_drip_campaigner_exist && strtotime(date("Y-m-d")) <= strtotime("2020-3-4")) :
            $middle_column_content .='
            <div class="row custom-top-margin">              
              <div class="col-12">
                  <div class="product-item pb-3">
                    <div class="product-image">
                      <img src="../assets/img/icon/access.png" class="img-fluid rounded">
                    </div>
                    <div class="product-details">
                      <div class="product-name">'.$this->lang->line("Subscription Messaging Permission").'
                      <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Subscription Messaging Permission").'" data-content="'.$this->lang->line("Non-promo message sending with NON_PROMOTIONAL_SUBSCRIPTION tag will require pages_messaging_subscriptions permission approved. This permission has been deprecated on July 29, 2019. You can only use this tag until 4th March 2020 if your page has already pages_messaging_subscriptions permission approved.").'"><i class="fa fa-info-circle text-danger"></i> </a></div>                                        
                      '.$subscription_messaging_permission_str.'
                    </div>
                    <div class="product-cta">
                      <a href="#" class="btn btn-sm small btn-info check_review_status_class" data-id="'.$page_info[0]['id'].'"><i class="fas fa-check-circle"></i> '.$this->lang->line("Check Status").'</a>
                    </div>
                  </div>
              </div>
              <!--
              <div class="col-12 col-md-12 col-lg-6">
                  <div class="product-item pb-3">
                    <div class="product-image">
                      <img src="../assets/img/icon/paper-plane.png" class="img-fluid rounded">
                    </div>
                    <div class="product-details">
                      <div class="product-name">'.$this->lang->line("Quick Broadcast Estimated Reach").'</div>  
                      <a class="badge badge-status blue"><i class="fas fa-circle"></i> '.$this->lang->line("Estimate").' : '.$estimated_reach.'</a>
                    </div>
                    <div class="product-cta">
                      <a href="#" class="btn btn-sm small btn-primary estimate_now_class" data-id="'.$page_info[0]['id'].'" ><i class="fas fa-user-friends"></i> '.$this->lang->line("Estimate Now").'</a>
                    </div>
                  </div>
              </div>
              -->
            </div>';
        endif;

        if($this->session->userdata('user_type') == 'Admin' || in_array(275,$this->module_access)) :

            $otn_view_loader = base_url("messenger_bot/otn_template_manager/").$page_info[0]['id'].'/1';
            $middle_column_content .= '
            <div class="row '.$hide_sections.'">
                <div class="col-12">
                    <div class="card card-large-icons card-condensed">
                      <div class="card-icon">
                        <i class="fas fa-th-large"></i>
                      </div>

                      <div class="card-body">
                        <h4>'.$this->lang->line("OTN post-back manager").'</h4>
                        <a class="card-cta iframed" href="'.$otn_view_loader.'" data-page-id="'.$page_info[0]['id'].'" data-height="795" class="card-cta" id="otn_postback_settings">'.$this->lang->line("Change Settings").'
                        </a>
                      </div>
                    </div>
                </div>
            </div>
            ';

            
        endif;

        if($this->basic->is_exist("add_ons",array("project_id"=>31))) :
        if($this->session->userdata('user_type') == 'Admin' || in_array(261,$this->module_access)) :
            $webform_view_loader = base_url("messenger_bot_connectivity/webview_builder_manager/").$page_info[0]['id']."/1";
            $middle_column_content .= '
            <div class="row '.$hide_sections.'">
                <div class="col-12">
                    <div class="card card-large-icons card-condensed">
                      <div class="card-icon">
                        <i class="fab fa-wpforms"></i>
                      </div>

                      <div class="card-body">
                        <h4>'.$this->lang->line("Webform Builder").'</h4>
                        <a href="'.$webform_view_loader.'" data-page-id="'.$page_info[0]['id'].'" data-height="795" class="card-cta iframed" id="webform_builder_list">'.$this->lang->line("Change Settings").'</a>
                      </div>
                    </div>
                </div>
            </div>
        ';
        endif;
        endif;

        if($this->session->userdata('user_type') == 'Admin' || in_array(257,$this->module_access)) :
                $middle_column_content .= '
                <div class="row">
                    <div class="col-12">
                        <div class="card card-large-icons card-condensed">
                          <div class="card-icon">
                            <i class="fas fa-tasks"></i>
                          </div>

                          <div class="card-body">
                            <h4>'.$this->lang->line("Saved Templates").'</h4>
                            <a target="_BLANK" href="'.base_url('messenger_bot/saved_templates').'" class="card-cta">'.$this->lang->line("Change Settings").'</a>
                          </div>
                        </div>
                    </div>
                </div>
            ';
        endif;

        $common_block_str = '
            <div class="col-12 col-md-6">
              <div class="card card-large-icons card-condensed bg-body" block-name="#BLKNAME#">
                <div class="card-icon">
                  <i class="#ICON#"></i>
                </div>
                <div class="card-body">
                  <h4 class="mb-3 text-primary">'.$this->lang->line("#TITLE#").'</h4>
                  <a id="#ID1#" data-toggle="tooltip" title="'.$this->lang->line('Create using Classic Editor').'" class="#CLASS1# iframed btn btn-sm btn-outline-primary block-button" href="#URL1#" data-height="795"><i class="fas fa-plus"></i> '.$this->lang->line("Classic").'</a>

                  <a id="#ID2#" data-toggle="tooltip" title="'.$this->lang->line('Create using Flow Builder').'" class="#CLASS2# btn btn-sm btn-outline-primary block-button" target="_BLANK" href="#URL2#"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Flow Builder").'</a>

                  <a id="#ID3#" data-toggle="tooltip" title="'.$this->lang->line('Campaign Lists').'" class="#CLASS3# iframed btn btn-sm btn-outline-primary block-button" href="#URL3#" data-height="795"><i class="fas fa-list"></i> '.$this->lang->line("Lists").'</a>
                </div>
              </div>
            </div><script>$("[data-toggle=\'tooltip\']").tooltip();</script>';

        $messenger_sequence_lists = $messenger_sequence_create_url = $sms_email_sequence_lists = $sms_email_sequence_create_url = '';
        $sequence_message_button_str = '';
        if($this->is_drip_campaigner_exist 
            || ( $this->addon_exist("sms_email_sequence") && 
                    ($this->session->userdata('user_type') == 'Admin' || 
                        count(array_intersect($this->module_access, array(270,271))) > 0
                    )
                )
        ) :
            // Messenger Sequence
            if($this->is_drip_campaigner_exist) { 

                $messenger_sequence_create_url_classic = base_url("messenger_bot_enhancers/create_sequence_campaign/".$page_table_id.'/1/'.$media_type);;
                $messenger_sequence_create_url_builder = base_url("visual_flow_builder/load_builder/".$page_table_id.'/1/'.$media_type);
                $messenger_sequence_lists = base_url('messenger_bot_enhancers/sequence_message_campaign/').$page_table_id.'/1/'.$media_type;;

                $sequence_message_button_str .= str_replace(
                    ['#BLKNAME#','#ICON#','#TITLE#','#URL1#','#URL2#','#URL3#'], 
                    ['sequence_message_settings','fab fa-facebook-messenger','Messenger Sequence',$messenger_sequence_create_url_classic,$messenger_sequence_create_url_builder,$messenger_sequence_lists], 
                    $common_block_str);
            }

            // SMS/Email Sequence
            if($this->addon_exist("sms_email_sequence") && $media_type!='ig') {
              if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, array(270,271))) > 0 ) {
                $sms_email_sequence_create_url_classic = '';
                $sms_email_sequence_create_url_builder = '';
                $sms_email_sequence_lists = base_url('sms_email_sequence/sms_email_sequence_message_campaign/').$page_table_id.'/1';;

                $sequence_message_button_str .= str_replace(
                    ['#BLKNAME#','#ICON#','#TITLE#','#URL1#','#URL2#','#URL3#','#CLASS1#','#CLASS2#'], 
                    ['sequence_message_settings','fas fa-envelope','SMS/Email Sequence',$sms_email_sequence_create_url_classic,$sms_email_sequence_create_url_builder,$sms_email_sequence_lists,'d-none','d-none'], 
                    $common_block_str);
              }
            }

              $middle_column_content .= '
                <div class="row">
                    <div class="col-12">
                        <div class="card card-large-icons card-condensed has_children" block-name="sequence_message_settings">
                            <div class="card-icon">
                                <i class="fas fa-tint"></i>
                            </div>
                            <div class="card-body">
                                <h4>'.$this->lang->line("Sequence Message Settings").'</h4>
                                <a href="" data-page-id="'.$page_info[0]['id'].'" class="card-cta sequence_message_settings" id="sequence_message_settings" block-name="sequence_message_settings">'.$this->lang->line("Change Settings").'</a>
                            </div>
                        </div>
                    </div>
                </div>
              ';
        endif;
        $response['sequence_message_button_str'] = $sequence_message_button_str;


        $messenger_engagment_str = '';
        if($this->is_engagement_exist) :
        if($media_type == 'fb') :

            $create_checkbox_classic = $create_checkbox_builder = $checkbox_lists = $create_send_to_msngr_classic = $create_send_to_msngr_builder = $send_to_msngr_lists = $create_mme_classic = $create_mme_builder = $mme_lists = $create_customer_chat_classic = $create_customer_chat_builder = $custom_chat_lists = '';

            // checkbox plugin
            if($this->session->userdata('user_type') == 'Admin' || in_array(213,$this->module_access)) {
                $create_checkbox_classic = base_url("messenger_bot_enhancers/checkbox_plugin_add/".$page_table_id.'/1');
                $create_checkbox_builder = base_url("visual_flow_builder/load_builder/".$page_table_id.'/1/'.$media_type.'?type=messenger-engagement&plugin=checkbox_plugin&action=messenger_engagement_plugin');
                $checkbox_lists = base_url('messenger_bot_enhancers/checkbox_plugin_list/').$page_table_id.'/1';

                $messenger_engagment_str .= str_replace(
                    ['#BLKNAME#','#ICON#','#TITLE#','#URL1#','#URL2#','#URL3#'], 
                    ['messenger_engagement_settings','fas fa-check-square','Checkbox plugin',$create_checkbox_classic,$create_checkbox_builder,$checkbox_lists], 
                    $common_block_str);
            }

            // send to messenger
            if($this->session->userdata('user_type') == 'Admin' || in_array(214,$this->module_access)) {

                $create_send_to_msngr_classic = base_url("messenger_bot_enhancers/send_to_messenger_add/".$page_table_id.'/1');
                $create_send_to_msngr_builder = base_url("visual_flow_builder/load_builder/".$page_table_id.'/1/'.$media_type.'?type=messenger-engagement&plugin=send_to_messenger&action=messenger_engagement_plugin');
                $send_to_msngr_lists = base_url('messenger_bot_enhancers/send_to_messenger_list/').$page_table_id.'/1';

                $messenger_engagment_str .= str_replace(
                    ['#BLKNAME#','#ICON#','#TITLE#','#URL1#','#URL2#','#URL3#'], 
                    ['messenger_engagement_settings','fas fa-paper-plane','Send to Messenger',$create_send_to_msngr_classic,$create_send_to_msngr_builder,$send_to_msngr_lists], 
                    $common_block_str);
            }

            // m.me link
            if($this->session->userdata('user_type') == 'Admin' || in_array(215,$this->module_access)) {

                $create_mme_classic = base_url("messenger_bot_enhancers/mme_link_add/".$page_table_id.'/1');
                $create_mme_builder = base_url("visual_flow_builder/load_builder/".$page_table_id.'/1/'.$media_type.'?type=messenger-engagement&plugin=m_me_link&action=messenger_engagement_plugin');
                $mme_lists = base_url('messenger_bot_enhancers/mme_link_list/').$page_table_id.'/1';

                $messenger_engagment_str .= str_replace(
                    ['#BLKNAME#','#ICON#','#TITLE#','#URL1#','#URL2#','#URL3#'], 
                    ['messenger_engagement_settings','fas fa-link','M.me Link',$create_mme_classic,$create_mme_builder,$mme_lists], 
                    $common_block_str);
            }

            // customer chat plugin
            if($this->session->userdata('user_type') == 'Admin' || in_array(217,$this->module_access)) {

                $create_customer_chat_classic = base_url("messenger_bot_enhancers/customer_chat_add/".$page_table_id.'/1');
                $create_customer_chat_builder = base_url("visual_flow_builder/load_builder/".$page_table_id.'/1/'.$media_type.'?type=messenger-engagement&plugin=customer_chat_plugin&action=messenger_engagement_plugin');
                $custom_chat_lists = base_url('messenger_bot_enhancers/customer_chat_plugin_list/').$page_table_id.'/1';

                $messenger_engagment_str .= str_replace(
                    ['#BLKNAME#','#ICON#','#TITLE#','#URL1#','#URL2#','#URL3#'], 
                    ['messenger_engagement_settings','fas fa-comments','Customer Chat Plugin',$create_customer_chat_classic,$create_customer_chat_builder,$custom_chat_lists], 
                    $common_block_str);
            }

            $middle_column_content .= '
              <div class="row">
                  <div class="col-12">
                      <div class="card card-large-icons card-condensed has_children" block-name="messenger_engagement_settings">
                          <div class="card-icon">
                              <i class="fas fa-ring"></i>
                          </div>
                          <div class="card-body">
                              <h4>'.$this->lang->line("Messenger Engagement").'</h4>
                              <a href="" data-page-id="'.$page_info[0]['id'].'" class="card-cta messenger_engagement_settings" id="messenger_engagement_settings" block-name="messenger_engagement_settings">'.$this->lang->line("Change Settings").'</a>
                          </div>
                      </div>
                  </div>
              </div>
            ';
        endif;
        endif;
        $response['messenger_engagment_str'] = $messenger_engagment_str;


        $user_input_flow_str = '';
        if($this->basic->is_exist("add_ons",array("project_id"=>49))) :
        if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access)) :

            $create_user_input_flow_classic = base_url("custom_field_manager/input_flow_builder/".$page_table_id.'/1/'.$media_type);
            $create_user_input_flow_builder = base_url("visual_flow_builder/load_builder/".$page_table_id.'/1/'.$media_type);
            $input_flow_lists = $user_input_url;
            $custom_field_lists = $custom_field_url;

            $user_input_flow_str .= str_replace(
                ['#BLKNAME#','#ICON#','#TITLE#','#URL1#','#URL2#','#URL3#'],
                ['user_input_flow_settings','fas fa-stream','User Input Flow',$create_user_input_flow_classic,$create_user_input_flow_builder,$input_flow_lists],
                $common_block_str
            );

            // custom fields
            $user_input_flow_str .= str_replace(
                ['#BLKNAME#','#ICON#','#TITLE#','#URL1#','#URL2#','#URL3#','#CLASS1#','#CLASS2#'],
                ['user_input_flow_settings','fas fa-burn','Custom Fields','','',$custom_field_lists,'d-none','d-none'],
                $common_block_str
            );


            $middle_column_content .= '
              <div class="row">
                  <div class="col-12">
                      <div class="card card-large-icons card-condensed has_children" block-name="user_input_flow_settings">
                          <div class="card-icon">
                              <i class="fab fa-stack-overflow"></i>
                          </div>
                          <div class="card-body">
                              <h4>'.$this->lang->line("User Input Flow").'</h4>
                              <a href="" data-page-id="'.$page_info[0]['id'].'" class="card-cta user_input_flow_settings" id="user_input_flow_settings">'.$this->lang->line("Change Settings").'</a>
                          </div>
                      </div>
                  </div>
              </div>
            ';


        endif;
        endif;
        $response['user_input_flow_str'] = $user_input_flow_str;

        $middle_column_content .= '
        <div class="row">
            <div class="col-12">
              <div class="card card-large-icons card-condensed active">
                <div class="card-icon">
                  <i class="fas fa-globe"></i>
                </div>
                <div class="card-body">
                  <h4>'.$this->lang->line("Whitelisted Domains").'</h4>                    
                  <a href="'.$whitelisted_domains_url.'" data-page-id="'.$page_info[0]['id'].'" data-height="795" class="card-cta iframed" id="whitelisted_domains">'.$this->lang->line("Change Settings").'</a>
                </div>
              </div>
            </div>
        </div>
        ';

        $middle_column_content .='
          </div>
          <div class="card-footer text-center">
            <a href="'.$surl.'" class="btn btn-sm btn-outline-primary float-left"><i class="fas fa-user-friends"></i> '.$this->lang->line("Subscribers").' <span class="badge badge-primary">'.custom_number_format($subscriber_count,2).'</span></a>
            <a href="" class="btn btn-sm btn-outline-danger float-right error_log_report" id="error_log" table_id="'.$page_table_id.'"><i class="fas fa-bug"></i> '.$this->lang->line("Errors").' <span class="badge badge-danger">'.$error_count.'</span></a>
          </div>
        </div>
        
        <script>
        $(\'[data-toggle="popover"]\').popover(); 
        $(\'[data-toggle="popover"]\').on("click", function(e) {e.preventDefault(); return true;});
        </script>
        ';     

        $response['middle_column_content'] = $middle_column_content;
        $response['selected_mailchimp_list_ids'] = $selected_mailchimp_list_ids;
        $response['selected_sendinblue_list_ids'] = $selected_sendinblue_list_ids;
        $response['selected_activecampaign_list_ids'] = $selected_activecampaign_list_ids;
        $response['selected_mautic_list_ids'] = $selected_mautic_list_ids;
        $response['selected_acelle_list_ids'] = $selected_acelle_list_ids;

        $response['sms_api_id'] = $page_info[0]['sms_api_id'];
        $response['sms_reply_message'] = $page_info[0]['sms_reply_message'];

        // sms email drip campaign add-on
        $sequence_sms_campaign_lists = $this->basic->get_data("messenger_bot_drip_campaign",["where"=>["page_id"=>$page_table_id,"campaign_type"=>"sms","user_id"=>$this->user_id]]);
        $sequence_email_campaign_lists = $this->basic->get_data("messenger_bot_drip_campaign",["where"=>["page_id"=>$page_table_id,"campaign_type"=>"email","user_id"=>$this->user_id]]);

        $sequence_sms_div_html = '<select class="form-control select2" id="sequence_sms_campaign_id" name="sequence_sms_campaign_id" style="width:100%;"><option value="">'.$this->lang->line("Select Campaign").'</option>';
        if(!empty($sequence_sms_campaign_lists)) {
            foreach ($sequence_sms_campaign_lists as $value) {
                $sequence_sms_div_html .= '<option value="'.$value['id'].'">'.$value['campaign_name'].'</option>';
            }
        }
        $sequence_sms_div_html .= '</select>';

        $sequence_email_div_html = '<select class="form-control select2" id="sequence_email_campaign_id" name="sequence_email_campaign_id" style="width:100%;"><option value="">'.$this->lang->line("Select Campaign").'</option>';
        if(!empty($sequence_email_campaign_lists)) {
            foreach ($sequence_email_campaign_lists as $value) {
                $sequence_email_div_html .= '<option value="'.$value['id'].'">'.$value['campaign_name'].'</option>';
            }
        }
        $sequence_email_div_html .= '</select>
        <script>
            $("#sequence_sms_campaign_id,#sequence_email_campaign_id").select2();
        </script>
        ';

        $response['sequence_sms_div_html'] = $sequence_sms_div_html;
        $response['sequence_email_div_html'] = $sequence_email_div_html;
        
        $response['sequence_sms_api_id'] = $page_info[0]['sequence_sms_api_id'];
        $response['sequence_sms_campaign_id'] = $page_info[0]['sequence_sms_campaign_id'];
        $response['sequence_email_api_id'] = $page_info[0]['sequence_email_api_id'];
        $response['sequence_email_campaign_id'] = $page_info[0]['sequence_email_campaign_id'];
        // sms email drip campaign add-on

        $response['email_api_id'] = $page_info[0]['email_api_id'];
        $response['email_reply_message'] = $page_info[0]['email_reply_message'];
        $response['email_reply_subject'] = $page_info[0]['email_reply_subject'];
        echo json_encode($response);
    }


    public function refresh_sequence_campaign_lists()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['270','271']))==0) exit;

        $page_id = $this->input->post("page_table_id");
        if($page_id == "" || $page_id == "") exit;

        $campaign_type = $this->input->post("campaign_type");
        $current_campaign_id = $this->input->post("current_campaign_id");
        $user_id = $this->user_id;

        $cam_lists = $this->basic->get_data("messenger_bot_drip_campaign",['where'=>['user_id'=>$user_id,'page_id'=>$page_id,'campaign_type'=>$campaign_type]]);

        $sequence_list = '<option value="">'.$this->lang->line("Select Campaign").'</option>';
        foreach ($cam_lists as $value) {
            $selected = '';
            if($value['id'] == $current_campaign_id) $selected = 'selected';
            $sequence_list .= '<option value="'.$value['id'].'" '.$selected.'>'.$value['campaign_name'].'</option>';
        }

        echo $sequence_list;

    }


    public function edit_bot($bot_id='0',$iframe='0',$default_template='0',$media_type='fb')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200, $this->module_access))
        redirect ('home/login_page','location');
        // for instagram default template check
        $page_table_id = 0;
        $bot_id_table_id = explode('-',$bot_id);
        $bot_id = $bot_id_table_id[0];
        $page_table_id = $bot_id_table_id[1] ?? 0;
        if($page_table_id != 0)
        {
            $user_id = $this->user_id;
            $page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['id'=>$page_table_id,'user_id'=>$user_id]],['page_id']);
            $page_id = $page_info[0]['page_id'] ?? 0;

            if($default_template == 'nomatch')
            {
                $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,media_type) VALUES
                ("'.$user_id.'", "'.$page_table_id.'", "'.$page_id.'", "text", "generic", "no match","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text","typing_on_settings":"off","delay_in_reply":"0","text":"Sorry, we could not find any content to show. One of our team member will reply you soon."}}}\', "", "", "", "", "", "1", "NO MATCH FOUND", "", "", "0", "ig");';
                $this->db->query($sql);
            }
            else if($default_template == 'story-mention')
            {
                $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,media_type) VALUES
                ("'.$user_id.'", "'.$page_table_id.'", "'.$page_id.'", "text", "generic", "story-mention","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text","typing_on_settings":"off","delay_in_reply":"0","text":"Thanks for mentioning me."}}}\', "", "", "", "", "", "1", "STORY MENTION", "STORY_MENTION", "", "0", "ig");';
                $this->db->query($sql);
            }
            $bot_id = $this->db->insert_id();

        }

        if($bot_id == 0)
        die();
        $table_name = "messenger_bot";
        $where_bot['where'] = array('id' => $bot_id);
        $bot_info = $this->basic->get_data($table_name, $where_bot);
        if(!isset($bot_info[0]))
        redirect('messenger_bot/bot_list', 'location');

        $full_message_json = $bot_info[0]['message'];
        $full_message_array = json_decode($full_message_json,true);
        $store_list = [];
        $store_info = [];
        $all_products = [];
        // foreach($full_message_array as $value)
        // {
        //     if($value['message']['template_type'] != 'Ecommerce') continue;
            
            $temp_page_table_id = $bot_info[0]["page_id"];
            $temp_select = ['ecommerce_store.id as store_table_id','ecommerce_product.id as product_table_id','store_name','store_city','product_name'];
            $temp_join = ['ecommerce_product'=>'ecommerce_product.store_id=ecommerce_store.id,left'];
            $page_wise_store_products = $this->basic->get_data('ecommerce_store',['where'=>['ecommerce_store.user_id'=>$this->user_id,'page_id'=>$temp_page_table_id]],$temp_select,$temp_join);
            foreach($page_wise_store_products as $temp)
            {
                $store_list[$temp['store_table_id']] = $temp['store_name']." - ".$temp['store_city'];
                $store_info[$temp['store_table_id']][$temp['product_table_id']] = $temp['product_name'];
                $all_products[$temp['product_table_id']] = $temp['store_table_id'];
            }

        // }
        $data['store_list'] = $store_list;
        $data['store_info'] = $store_info;
        $data['all_products'] = $all_products;

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('bot_enabled' => "1", "facebook_rx_fb_page_info.id"=>$bot_info[0]["page_id"], "facebook_rx_fb_page_info.user_id"=>$this->user_id);
        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");
        $page_info = $this->basic->get_data($table_name,$where, array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id"),$join);
        if(!isset($page_info[0]))
        redirect('messenger_bot/bot_list','location'); 

        $template_types=$this->basic->get_enum_values("messenger_bot","template_type");
        if(!$this->addon_exist("custom_field_manager"))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        } 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        }

        if($media_type == 'ig')
        {
            $need_to_remove = [
                                'audio',
                                'video',
                                'file',
                                'text with buttons',
                                'media',
                                'One Time Notification'
                            ];
            foreach ($need_to_remove as $value) {
                if (($key = array_search($value, $template_types)) !== false) {
                    unset($template_types[$key]);
                }
            }
        }

        $data["templates"]=$template_types; 
        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $data['body'] = 'messenger_tools/edit_bot_settings';
        $data['page_title'] = $this->lang->line('Edit Bot Settings');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();  
        $data['bot_info'] = isset($bot_info[0]) ? $bot_info[0] : array();
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"])));
        $current_postbacks = array();
        foreach ($postback_id_list as $value) {
            if($value['messenger_bot_table_id'] == $bot_id)
            $current_postbacks[] = $value['postback_id'];
        }
        $data['postback_ids'] = $postback_id_list;
        $data['current_postbacks'] = $current_postbacks;

        $page_id=$page_info[0]['id'];// database id   
        if($media_type == 'ig')   
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'ig'),"or_where"=>array("messenger_bot_table_id"=>$bot_id)),'','','',$start=NULL,$order_by='template_name ASC');
        else
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'fb'),"or_where"=>array("messenger_bot_table_id"=>$bot_id)),'','','',$start=NULL,$order_by='template_name ASC');

        
        $poption=array();
        foreach ($postback_data as $key => $value) 
        {
            $poption[$value["postback_id"]]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        $data['poption']=$poption;

        if($this->basic->is_exist("add_ons",array("project_id"=>16)))
            $data['has_broadcaster_addon'] = 1;
        else
            $data['has_broadcaster_addon'] = 0;

        $otn_postbacks = [];
        $otn_postback_info = $this->basic->get_data('otn_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_id)),array('id','otn_postback_id','template_name'));
        foreach($otn_postback_info as $value)
        {
            $otn_postbacks[$value['id']] = $value['otn_postback_id']." (".$value['template_name'].")";
        }
        $data['otn_postback_list'] = $otn_postbacks;

        if($this->addon_exist("custom_field_manager"))
            $flow_campaign_info = $this->basic->get_data('user_input_flow_campaign',['where'=>['user_id'=>$this->user_id,'page_table_id'=>$bot_info[0]["page_id"]]]);
        else
           $flow_campaign_info = []; 
        $data['flow_campaigns'] = $flow_campaign_info;
        
        $data['default_template'] = $default_template;
        $data['media_type'] = $media_type;
        $data['iframe']=$iframe;
        $this->_viewcontroller($data); 
    }

    public function bot_settings($page_auto_id='0',$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        redirect('home/login_page', 'location'); 
        if($page_auto_id==0) exit();

        $media_type = 'fb';
        if($this->session->userdata('selected_global_media_type')) {
            $media_type = $this->session->userdata('selected_global_media_type');
        }
        $data['media_type'] = $media_type;

        $ecommerce_stores = '<option value="">'.$this->lang->line('Select').'</option>';
        $ecommerce_stores_info = $this->basic->get_data('ecommerce_store',['where'=>['user_id'=>$this->user_id,'page_id'=>$page_auto_id]],['id','store_name','store_city']);
        foreach($ecommerce_stores_info as $value)
        {
            $ecommerce_stores .= '<option value="'.$value["id"].'">'.$value["store_name"].' - '.$value["store_city"].'</option>';
        }
        $data['ecommerce_stores'] = $ecommerce_stores;

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('bot_enabled' => "1","facebook_rx_fb_page_info.id"=>$page_auto_id,"facebook_rx_fb_page_info.user_id"=>$this->user_id);
        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id"),$join);

        if(!isset($page_info[0]))
        redirect('messenger_bot/bot_list', 'location'); 
        $bot_settings=$this->basic->get_data("messenger_bot",array("where"=>array("page_id"=>$page_auto_id,"is_template"=>"0","media_type"=>"fb")),'','','','','bot_name asc');

        $template_types=$this->basic->get_enum_values("messenger_bot","template_type");
        if(!$this->addon_exist("custom_field_manager"))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        } 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        }
        $data["templates"]=$template_types;

        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $data['body'] = 'messenger_tools/bot_settings';
        $data['page_title'] = $this->lang->line('Bot Settings');
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();  
        $data['bot_settings'] = $bot_settings;

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)));
        $data['postback_ids'] = $postback_id_list;

        if($this->basic->is_exist("add_ons",array("project_id"=>16)))
            $data['has_broadcaster_addon'] = 1;
        else  $data['has_broadcaster_addon'] = 0;

        $otn_postbacks = [];
        $otn_postback_info = $this->basic->get_data('otn_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)),array('id','otn_postback_id','template_name'));
        foreach($otn_postback_info as $value)
        {
            $otn_postbacks[$value['id']] = $value['otn_postback_id']." (".$value['template_name'].")";
        }
        $data['otn_postback_list'] = $otn_postbacks;

        if($this->addon_exist("custom_field_manager"))
            $flow_campaign_info = $this->basic->get_data('user_input_flow_campaign',['where'=>['user_id'=>$this->user_id,'page_table_id'=>$page_auto_id]]);
        else
           $flow_campaign_info = []; 
        $data['flow_campaigns'] = $flow_campaign_info;

        if($this->addon_exist("visual_flow_builder"))
            $data['visual_flow_builder_exist'] = 'yes';
        else
            $data['visual_flow_builder_exist'] = 'no';

        $data['iframe']=$iframe;
        $this->_viewcontroller($data);  
    }

    public function ig_bot_settings($page_auto_id='0',$iframe='0',$media_type='fb')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        redirect('home/login_page', 'location'); 
        if($page_auto_id==0) exit();

        $ecommerce_stores = '<option value="">'.$this->lang->line('Select').'</option>';
        $ecommerce_stores_info = $this->basic->get_data('ecommerce_store',['where'=>['user_id'=>$this->user_id,'page_id'=>$page_auto_id]],['id','store_name','store_city']);
        foreach($ecommerce_stores_info as $value)
        {
            $ecommerce_stores .= '<option value="'.$value["id"].'">'.$value["store_name"].' - '.$value["store_city"].'</option>';
        }
        $data['ecommerce_stores'] = $ecommerce_stores;

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('bot_enabled' => "1","facebook_rx_fb_page_info.id"=>$page_auto_id,"facebook_rx_fb_page_info.user_id"=>$this->user_id,"has_instagram"=>"1");
        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id"),$join);

        if(!isset($page_info[0]))
        redirect('messenger_bot/bot_list/ig', 'location'); 
        $bot_settings=$this->basic->get_data("messenger_bot",array("where"=>array("page_id"=>$page_auto_id,"is_template"=>"0","media_type"=>"ig","postback_id !="=>"STORY_MENTION")),'','','','','bot_name asc');

        $template_types=$this->basic->get_enum_values("messenger_bot","template_type");
        if(!$this->addon_exist("custom_field_manager"))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        } 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        }
        $need_to_remove = [
                            'audio',
                            'video',
                            'file',
                            'text with buttons',
                            'media',
                            'One Time Notification'
                        ];
        foreach ($need_to_remove as $value) {
            if (($key = array_search($value, $template_types)) !== false) {
                unset($template_types[$key]);
            }
        }
        $data["templates"]=$template_types;

        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $data['body'] = 'messenger_tools/ig_bot_settings';
        $data['media_type'] = $media_type;
        $data['page_title'] = $this->lang->line('Bot Settings');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();  
        $data['bot_settings'] = $bot_settings;

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)));
        $data['postback_ids'] = $postback_id_list;

        if($this->basic->is_exist("add_ons",array("project_id"=>16)))
            $data['has_broadcaster_addon'] = 1;
        else  $data['has_broadcaster_addon'] = 0;

        $otn_postbacks = [];
        $otn_postback_info = $this->basic->get_data('otn_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)),array('id','otn_postback_id','template_name'));
        foreach($otn_postback_info as $value)
        {
            $otn_postbacks[$value['id']] = $value['otn_postback_id']." (".$value['template_name'].")";
        }
        $data['otn_postback_list'] = $otn_postbacks;

        if($this->addon_exist("custom_field_manager"))
            $flow_campaign_info = $this->basic->get_data('user_input_flow_campaign',['where'=>['user_id'=>$this->user_id,'page_table_id'=>$page_auto_id]]);
        else
           $flow_campaign_info = []; 
        $data['flow_campaigns'] = $flow_campaign_info;

        if($this->addon_exist("visual_flow_builder"))
            $data['visual_flow_builder_exist'] = 'yes';
        else
            $data['visual_flow_builder_exist'] = 'no';

        $data['iframe']=$iframe;
        $this->_viewcontroller($data);
        
    }

    public function change_bot_state()
    {
        $this->ajax_check();

        $table_id = $this->input->post('table_id', true);

        /* check this users requested bot existance */
        $bot_info = $this->basic->get_data('messenger_bot', array('where' => array('id' => $table_id, 'user_id' => $this->user_id)), array('status'));

        if (count($bot_info) > 0) {
            
            if ($bot_info[0]['status'] == '1') {
                $new_state = '0';
            } else {
                $new_state = '1';
            }

            $this->basic->update_data('messenger_bot', array('id' => $table_id, 'user_id' => $this->user_id), array('status' => $new_state));
            echo json_encode(array('status' => 'success', 'message' => $this->lang->line("State has successfully changed.")));
        } else {
            echo json_encode(array('status' => 'error', 'message' => $this->lang->line("Something went wrong.")));
        }
    }

    public function get_postback()
    {
        if(!$_POST) exit();
        $is_from_add_button=$this->input->post('is_from_add_button');
        $page_id=$this->input->post('page_id');// database id      
        $order_by=$this->input->post('order_by');     
        if($order_by=="") $order_by="id DESC";
        else $order_by=$order_by." ASC";
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'fb')),'','','',$start=NULL,$order_by);
        $push_postback="";

        if($is_from_add_button=='0')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        }
        
        foreach ($postback_data as $key => $value) 
        {
            $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }

        if($is_from_add_button=='1' || $is_from_add_button=='')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        }

        echo $push_postback;   
    }

    public function get_ig_postback()
    {
        if(!$_POST) exit();
        $is_from_add_button=$this->input->post('is_from_add_button');
        $page_id=$this->input->post('page_id');// database id      
        $order_by=$this->input->post('order_by');     
        if($order_by=="") $order_by="id DESC";
        else $order_by=$order_by." ASC";
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'ig')),'','','',$start=NULL,$order_by);
        $push_postback="";

        if($is_from_add_button=='0')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        }
        
        foreach ($postback_data as $key => $value) 
        {
            $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }

        if($is_from_add_button=='1' || $is_from_add_button=='')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        }

        echo $push_postback;   
    }

    public function get_postback_for_persistent_menu()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id      
        $order_by=$this->input->post('order_by');     
        if($order_by=="") $order_by="id DESC";
        else $order_by=$order_by." ASC";
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1','template_for'=>'reply_message')),'','','',$start=NULL,$order_by);
        $push_postback="";
        foreach ($postback_data as $key => $value) 
        {
            // if($value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply") continue;
            $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        echo $push_postback;   
    }
    //=================================BOT SETTINGS===============================
    public function edit_generate_messenger_bot()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            if(!is_array($value))
                $temp = strip_tags($value);
            else
                $temp = $value;

            $$key=$temp;
        }

        // Added by Konok for sorting main reply order 22.08.2020
        $main_reply_sort_order_serialize= $_POST['main_reply_sort_order'];
        parse_str($main_reply_sort_order_serialize,$main_reply_sort_order_array);
        $main_reply_final_order = $main_reply_sort_order_array['multiple_template_div'];


        // $template_type = trim($template_type);
        $insert_data = array();
        $insert_data['media_type'] = $media_type;
        $insert_data['bot_name'] = $bot_name;
        $insert_data['fb_page_id'] = $page_id;
        $insert_data['keywords'] = trim($keywords_list);
        $insert_data['page_id'] = $page_table_id;
        // $insert_data['template_type'] = $template_type;
        $insert_data['keyword_type'] = $keyword_type;
        if($keyword_type == 'post-back')
            $insert_data['postback_id'] = implode(',', $keywordtype_postback_id);

        // $template_type = str_replace(' ', '_', $template_type);
        // domain white list section
        $facebook_rx_fb_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_access_token"));
        $page_access_token = $facebook_rx_fb_user_info_id[0]['page_access_token'];
        $facebook_rx_fb_user_info_id = $facebook_rx_fb_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$facebook_rx_fb_user_info_id,"page_id"=>$page_table_id)),"domain");
        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();
        // domain white list section

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();

        for ($no_reply_org=1; $no_reply_org <=6 ; $no_reply_org++) 
        {    

            // assaign $k variable with in order of main reply 
            $k= $main_reply_final_order[$no_reply_org-1];


            $template_type = 'template_type_'.$k;
            if(!isset($$template_type)) continue;
            $template_type = $$template_type;
            // $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'Ecommerce')
            {
                $this->load->helper('ecommerce_helper');
                $currency_icons = $this->currency_icon();

                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';

                $buy_now_text = 'ecommerce_button_text'.$k;
                $buy_now_text = isset($$buy_now_text) ? $$buy_now_text : $this->lang->line('Buy Now');
                $carousel_prodcuts_array = 'ecommerce_product_ids'.$k;
                $carousel_prodcuts = isset($$carousel_prodcuts_array) ? $$carousel_prodcuts_array : [];
                $index_variable = 0;
                foreach($carousel_prodcuts as $value)
                {
                    $product_data = $this->basic->get_data('ecommerce_product',['where'=>['id'=>$value,'user_id'=>$this->user_id]],['store_id','product_name','original_price','sell_price','thumbnail','id','woocommerce_product_id']);
                    if(!isset($product_data[0])) continue;
                    $store_id = isset($product_data[0]['store_id']) ? $product_data[0]['store_id'] : 0;
                    $ecommerce_config = $this->basic->get_data('ecommerce_config',['where'=>['user_id'=>$this->user_id,'store_id'=>$store_id]],['currency','currency_position','decimal_point','thousand_comma']);

                    $original_price = isset($product_data[0]['original_price']) ? $product_data[0]['original_price'] : 0;
                    $sell_price = isset($product_data[0]['sell_price']) ? $product_data[0]['sell_price'] : 0;
                    $currency_position = isset($ecommerce_config[0]['currency_position']) ? $ecommerce_config[0]['currency_position'] : 'left';
                    $decimal_point = isset($ecommerce_config[0]['decimal_point']) ? $ecommerce_config[0]['decimal_point'] : 0;
                    $thousand_comma = isset($ecommerce_config[0]['thousand_comma']) ? $ecommerce_config[0]['thousand_comma'] : 0;
                    $currency_icon = '$';
                    if(isset($ecommerce_config[0]['currency']))
                    {
                        $currency_icon = isset($currency_icons[$ecommerce_config[0]['currency']]) ? $currency_icons[$ecommerce_config[0]['currency']] : '$';
                    }
                    $subtitle = mec_display_price($original_price,$sell_price,$currency_icon,'2',$currency_position,$decimal_point,$thousand_comma);
                    if($currency_position == 'left') $subtitle = $currency_icon.$subtitle;
                    else $subtitle = $subtitle.$currency_icon;
                    $title = isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : '';

                    // $thumbnail = isset($product_data[0]['thumbnail']) ? base_url('upload/ecommerce/').$product_data[0]['thumbnail'] : '';

                    $thumbnail = ($product_data[0]['thumbnail']!='') ? base_url('upload/ecommerce/'.$product_data[0]['thumbnail']) : base_url('assets/img/products/product-1.jpg');
                    if(isset($product_data[0]["woocommerce_product_id"]) && !is_null($product_data[0]["woocommerce_product_id"]) && $product_data[0]['thumbnail']!='')
                    $thumbnail = $product_data[0]['thumbnail'];


                    if(function_exists('getimagesize') && $thumbnail!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($thumbnail);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }
                    $buy_now_url = base_url('ecommerce/product/').$value;
                    $buy_now_url = add_query_string_to_url($buy_now_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['title'] = $title;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['subtitle'] = $subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['image_url'] = $thumbnail;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['default_action']['type'] = 'web_url';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['default_action']['url'] = $buy_now_url;

                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['type'] = 'web_url';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['messenger_extensions'] = 'true';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['webview_height_ratio'] = 'full';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['url'] = $buy_now_url;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['title'] = $buy_now_text;

                    $index_variable++;
                }
            }

            if($template_type == 'User_Input_Flow')
            {
                $flow_campaign_id = 'flow_campaign_id_'.$k;
                $flow_campaign_id = isset($$flow_campaign_id) ? $$flow_campaign_id : 0;
                if($flow_campaign_id != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['flow_campaign_id'] = $flow_campaign_id;
                    
                }
            }

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $text_reply;
                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }

            if($template_type == 'One_Time_Notification')
            {
                $otn_title = 'otn_title_'.$k;
                $otn_postback = 'otn_postback_'.$k;
                $otn_title = isset($$otn_title) ? $$otn_title : '';
                $otn_postback = isset($$otn_postback) ? $$otn_postback : '';
                if($otn_postback != '' && $otn_postback != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = "one_time_notif_req";
                    $reply_bot[$k]['attachment']['payload']['title'] = $otn_title;
                    $reply_bot[$k]['attachment']['payload']['payload'] = $otn_postback;
                }
            }
            
            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }

            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }

            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }

        
            if($template_type == 'media')
            {   

                $media_postback_hidden_field_ids="media_postback_sort_order_{$k}";
                $media_postback_sort_order_array_str= $_POST[$media_postback_hidden_field_ids];
                preg_match_all("#media_row_(.*?)_{$k}#si", $media_postback_sort_order_array_str, $media_postback_sort_order_match_result);
                $media_postback_sort_order_array=$media_postback_sort_order_match_result[1];


                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                {   
                    $media_postback_sorted_order= $media_postback_sort_order_array[$i-1];

                    $button_text = 'media_text_'.$media_postback_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$media_postback_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$media_postback_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$media_postback_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                      $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'media_call_us_'.$media_postback_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';

                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;

                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                if(isset($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']))
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'] = array_values($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']);

            }


            if($template_type == 'text_with_buttons')
            {   

                $text_button_hidden_field_ids="text_button_sort_order_{$k}";
                $text_button_sort_order_array_str= $_POST[$text_button_hidden_field_ids];
                preg_match_all("#text_with_buttons_row_(.*?)_{$k}#si", $text_button_sort_order_array_str, $text_button_sort_order_match_result);
                $text_button_sort_order_array=$text_button_sort_order_match_result[1];


                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    
                    $text_button_sorted_order= $text_button_sort_order_array[$i-1];

                    $button_text = 'text_with_buttons_text_'.$text_button_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$text_button_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$text_button_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$text_button_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");



                    $button_call_us = 'text_with_button_call_us_'.$text_button_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';

                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                if(isset($reply_bot[$k]['attachment']['payload']['buttons']))
                    $reply_bot[$k]['attachment']['payload']['buttons'] = array_values($reply_bot[$k]['attachment']['payload']['buttons']);

            }

            if($template_type == 'quick_reply')
            {   

                $quick_reply_hidden_field_ids="quick_reply_sort_order_{$k}";
                $quick_reply_sort_order_array_str= $_POST[$quick_reply_hidden_field_ids];
                preg_match_all("#quick_reply_row_(.*?)_{$k}#si", $quick_reply_sort_order_array_str, $quick_reply_sort_order_match_result);
                $quick_reply_sort_order_array=$quick_reply_sort_order_match_result[1];


                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                { 
                   
                    $quick_reply_sorted_order= $quick_reply_sort_order_array[$i-1];


                    $button_text = 'quick_reply_button_text_'.$quick_reply_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$quick_reply_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$quick_reply_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';

                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }   

                 // Re indexing quick reply button array 
                $reply_bot[$k]['quick_replies']=array_values($reply_bot[$k]['quick_replies']);

            }

            if($template_type == 'generic_template')
            {   

                $generic_button_hidden_field_ids="generic_button_sort_order_{$k}";
                $generic_button_sort_order_array_str= $_POST[$generic_button_hidden_field_ids];
                preg_match_all("#generic_template_row_(.*?)_{$k}#si", $generic_button_sort_order_array_str, $generic_button_sort_order_match_result);
                $generic_button_sort_order_array=$generic_button_sort_order_match_result[1];



                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                   
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['messenger_extensions'] = true;
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['webview_height_ratio'] = 'tall';
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['fallback_url'] = $generic_template_image_destination_link;

                for ($i=1; $i <= 3 ; $i++) 
                { 

                    $generic_button_sorted_order= $generic_button_sort_order_array[$i-1];


                    $button_text = 'generic_template_button_text_'.$generic_button_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$generic_button_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$generic_button_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$generic_button_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'generic_template_button_call_us_'.$generic_button_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';


                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                // Re indexing buttons array
                if(isset($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'])) 
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'] =array_values($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']);

            }

            if($template_type == 'carousel')
            {   

                $carousel_reply_hidden_field_ids="carousel_reply_sort_order_{$k}";
                $carousel_reply_sort_order_array_str= $_POST[$carousel_reply_hidden_field_ids];
                preg_match_all("#carousel_div_(.*?)_{$k}#si", $carousel_reply_sort_order_array_str, $carousel_reply_sort_order_match_result);
                $carousel_reply_sort_order_array=$carousel_reply_sort_order_match_result[1];


                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';

                for ($j=1; $j <=10 ; $j++) 
                {      

                    $carousel_reply_sorted_order= $carousel_reply_sort_order_array[$j-1];

                    $carousel_button_hidden_field_ids="carousel_button_sort_order_{$carousel_reply_sorted_order}_{$k}";
                    $carousel_button_sort_order_array_str= $_POST[$carousel_button_hidden_field_ids];
                    preg_match_all("#carousel_row_{$carousel_reply_sorted_order}_(.*?)_{$k}#si", $carousel_button_sort_order_array_str, $carousel_button_sort_order_match_result);

                    $carousel_button_sort_order_array=$carousel_button_sort_order_match_result[1];


                    $carousel_image = 'carousel_image_'.$carousel_reply_sorted_order.'_'.$k;
                    $carousel_title = 'carousel_title_'.$carousel_reply_sorted_order.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$carousel_reply_sorted_order.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$carousel_reply_sorted_order.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }


                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['messenger_extensions'] = true;
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['webview_height_ratio'] = 'tall';
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['fallback_url'] = $$carousel_image_destination_link;

                    for ($i=1; $i <= 3 ; $i++) 
                    { 
                        
                        $carousel_button_sorted_order= $carousel_button_sort_order_array[$i-1];

                        $button_text = 'carousel_button_text_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                          $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                        $button_call_us = 'carousel_button_call_us_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';


                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $button_postback_id;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = $bot_name;
                                $postback_insert_data[] = $single_postback_insert_data; 
                            }
                        }
                        if(strpos($button_type,'web_url') !== FALSE)
                        {
                            $button_type_array = explode('_', $button_type);
                            if(isset($button_type_array[2]))
                            {
                                $button_extension = trim($button_type_array[2],'_'); 
                                array_pop($button_type_array);
                            }            
                            else $button_extension = '';
                            $button_type = implode('_', $button_type_array);

                            if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                if($button_extension != '' && $button_extension == 'birthday'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_birthdate');
                                }
                                else if($button_extension != '' && $button_extension == 'email'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_email');
                                }
                                else if($button_extension != '' && $button_extension == 'phone'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_phone');
                                }
                                else if($button_extension != '' && $button_extension == 'location'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_location');
                                }
                                else
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                                {
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                    // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                                }

                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }

                    // Added by Konok to reindexing from zero 
                    if(isset($reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']))
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']=array_values($reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']);
                }

                // Added by Konok to reindexing from zero 
                if(isset($reply_bot[$k]['attachment']['payload']['elements']))
                    $reply_bot[$k]['attachment']['payload']['elements'] = array_values($reply_bot[$k]['attachment']['payload']['elements']);
            }


            if(isset($reply_bot[$k]))
            {     
                $typing_on_settings = 'typing_on_enable_'.$k;
                if(!isset($$typing_on_settings)) $typing_on_settings = 'off';
                else $typing_on_settings = $$typing_on_settings;

                $delay_in_reply = 'delay_in_reply_'.$k;
                if(!isset($$delay_in_reply)) $delay_in_reply = 0;
                else $delay_in_reply = $$delay_in_reply;

                $reply_bot[$k]['typing_on_settings'] = $typing_on_settings;
                $reply_bot[$k]['delay_in_reply'] = $delay_in_reply;

                $bot_message[$k]['recipient'] = array('id'=>'replace_id');
                $bot_message[$k]['message'] = $reply_bot[$k];
            }

        }
        
        $reply_bot_filtered = array();
        $m=0;
        foreach ($bot_message as $value) {
            $m++;
            $reply_bot_filtered[$m] = $value;
        }

        // domain white list section start
        $this->load->library("fb_rx_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            
            $domain_only_whitelist= get_domain_only_with_http($value);
            if(in_array($domain_only_whitelist, $white_listed_domain_array)) continue; 

            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$domain_only_whitelist);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $facebook_rx_fb_user_info_id; 
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $domain_only_whitelist;
                $temp_data['created_at'] = date("Y-m-d H:i:s");
                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data))
            $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end

        $insert_data['message'] = json_encode($reply_bot_filtered,true);
        $insert_data['user_id'] = $this->user_id;
        $this->basic->update_data('messenger_bot',array("id" => $id),$insert_data);
        // $this->basic->delete_data('messenger_bot_postback',array('messenger_bot_table_id'=> $id));
        $messenger_bot_table_id = $id;
        
        $existing_postback_ids_array = array();
        $existing_postback_ids = $this->basic->get_data('messenger_bot_postback',array('where'=>array('messenger_bot_table_id'=>$messenger_bot_table_id)),array('postback_id'));
        if(!empty($existing_postback_ids))
        {
            foreach($existing_postback_ids as $value)
            {
                array_push($existing_postback_ids_array, strtoupper($value['postback_id']));
            }
        }

        $postback_insert_data_modified = array();
        $m=0;
        foreach($postback_insert_data as $value)
        {
            if(in_array(strtoupper($value['postback_id']), $existing_postback_ids_array)) continue;
            $postback_insert_data_modified[$m]['user_id'] = $value['user_id'];
            $postback_insert_data_modified[$m]['postback_id'] = $value['postback_id'];
            $postback_insert_data_modified[$m]['page_id'] = $value['page_id'];
            $postback_insert_data_modified[$m]['bot_name'] = $value['bot_name'];
            $postback_insert_data_modified[$m]['messenger_bot_table_id'] = $messenger_bot_table_id;
            $m++;
        }

        if($keyword_type == 'post-back' && !empty($keywordtype_postback_id))
        {   
            $this->db->where("page_id",$page_table_id);         
            $this->db->where_in("postback_id", $keywordtype_postback_id);
            $this->db->update('messenger_bot_postback', array('use_status' => '1'));
        }
        
        // if(!empty($postback_insert_data_modified))
        // $this->db->insert_batch('messenger_bot_postback',$postback_insert_data_modified);

        // $this->session->set_flashdata('bot_update_success',1);
        echo json_encode(array("status" => "1", "message" =>$this->lang->line("Bot settings has been updated successfully.")));        

    }


    public function ajax_generate_messenger_bot()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }


        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            if(!is_array($value))
                $temp = strip_tags($value);
            else
                $temp = $value;

            $$key=$temp;
        }

        // Added by Konok for sorting main reply order 22.08.2020

        $main_reply_sort_order_serialize= $_POST['main_reply_sort_order'];
        parse_str($main_reply_sort_order_serialize,$main_reply_sort_order_array);
        $main_reply_final_order = $main_reply_sort_order_array['multiple_template_div'];



        // $template_type = trim($template_type);
        $insert_data = array();
        $insert_data['media_type'] = $media_type;
        $insert_data['bot_name'] = $bot_name;
        $insert_data['fb_page_id'] = $page_id;
        $insert_data['keywords'] = trim($keywords_list);
        $insert_data['page_id'] = $page_table_id;
        // $insert_data['template_type'] = $template_type;
        $insert_data['keyword_type'] = $keyword_type;
        if($keyword_type == 'post-back')
            $insert_data['postback_id'] = implode(',', $keywordtype_postback_id);

        // $template_type = str_replace(' ', '_', $template_type);
        // domain white list section
        $facebook_rx_fb_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_access_token"));
        $page_access_token = $facebook_rx_fb_user_info_id[0]['page_access_token'];
        $facebook_rx_fb_user_info_id = $facebook_rx_fb_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$facebook_rx_fb_user_info_id,"page_id"=>$page_table_id)),"domain");
        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();
        // domain white list section

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();

        //for ($k=1; $k <=6 ; $k++) 
        for ($no_reply_org=1; $no_reply_org <=6 ; $no_reply_org++) 
        {    

            // assaign $k variable with in order of main reply 
            $k= $main_reply_final_order[$no_reply_org-1];


            $template_type = 'template_type_'.$k;
            if(!isset($$template_type)) continue;
            $template_type = $$template_type;
            // $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'Ecommerce')
            {
                $this->load->helper('ecommerce_helper');
                $currency_icons = $this->currency_icon();

                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';

                $buy_now_text = 'ecommerce_button_text'.$k;
                $buy_now_text = isset($$buy_now_text) ? $$buy_now_text : $this->lang->line('Buy Now');
                $carousel_prodcuts_array = 'ecommerce_product_ids'.$k;
                $carousel_prodcuts = isset($$carousel_prodcuts_array) ? $$carousel_prodcuts_array : [];
                $index_variable = 0;
                foreach($carousel_prodcuts as $value)
                {
                    $product_data = $this->basic->get_data('ecommerce_product',['where'=>['id'=>$value,'user_id'=>$this->user_id]],['store_id','product_name','original_price','sell_price','thumbnail','id','woocommerce_product_id']);
                    if(!isset($product_data[0])) continue;
                    $store_id = isset($product_data[0]['store_id']) ? $product_data[0]['store_id'] : 0;
                    $ecommerce_config = $this->basic->get_data('ecommerce_config',['where'=>['user_id'=>$this->user_id,'store_id'=>$store_id]],['currency','currency_position','decimal_point','thousand_comma']);

                    $original_price = isset($product_data[0]['original_price']) ? $product_data[0]['original_price'] : 0;
                    $sell_price = isset($product_data[0]['sell_price']) ? $product_data[0]['sell_price'] : 0;
                    $currency_position = isset($ecommerce_config[0]['currency_position']) ? $ecommerce_config[0]['currency_position'] : 'left';
                    $decimal_point = isset($ecommerce_config[0]['decimal_point']) ? $ecommerce_config[0]['decimal_point'] : 0;
                    $thousand_comma = isset($ecommerce_config[0]['thousand_comma']) ? $ecommerce_config[0]['thousand_comma'] : 0;
                    $currency_icon = '$';
                    if(isset($ecommerce_config[0]['currency']))
                    {
                        $currency_icon = isset($currency_icons[$ecommerce_config[0]['currency']]) ? $currency_icons[$ecommerce_config[0]['currency']] : '$';
                    }
                    $subtitle = mec_display_price($original_price,$sell_price,$currency_icon,'2',$currency_position,$decimal_point,$thousand_comma);
                    if($currency_position == 'left') $subtitle = $currency_icon.$subtitle;
                    else $subtitle = $subtitle.$currency_icon;
                    $title = isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : '';

                    // $thumbnail = isset($product_data[0]['thumbnail']) ? base_url('upload/ecommerce/').$product_data[0]['thumbnail'] : '';

                    $thumbnail = ($product_data[0]['thumbnail']!='') ? base_url('upload/ecommerce/'.$product_data[0]['thumbnail']) : base_url('assets/img/products/product-1.jpg');
                    if(isset($product_data[0]["woocommerce_product_id"]) && !is_null($product_data[0]["woocommerce_product_id"]) && $product_data[0]['thumbnail']!='')
                    $thumbnail = $product_data[0]['thumbnail'];


                    if(function_exists('getimagesize') && $thumbnail!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($thumbnail);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }
                    $buy_now_url = base_url('ecommerce/product/').$value;
                    $buy_now_url = add_query_string_to_url($buy_now_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['title'] = $title;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['subtitle'] = $subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['image_url'] = $thumbnail;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['default_action']['type'] = 'web_url';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['default_action']['url'] = $buy_now_url;

                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['type'] = 'web_url';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['messenger_extensions'] = 'true';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['webview_height_ratio'] = 'full';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['url'] = $buy_now_url;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['title'] = $buy_now_text;

                    $index_variable++;
                }
            }

            if($template_type == 'User_Input_Flow')
            {
                $flow_campaign_id = 'flow_campaign_id_'.$k;
                $flow_campaign_id = isset($$flow_campaign_id) ? $$flow_campaign_id : 0;
                if($flow_campaign_id != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['flow_campaign_id'] = $flow_campaign_id;
                    
                }
            }

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $text_reply;
                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }

            if($template_type == 'One_Time_Notification')
            {
                $otn_title = 'otn_title_'.$k;
                $otn_postback = 'otn_postback_'.$k;
                $otn_title = isset($$otn_title) ? $$otn_title : '';
                $otn_postback = isset($$otn_postback) ? $$otn_postback : '';
                if($otn_postback != '' && $otn_postback != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = "one_time_notif_req";
                    $reply_bot[$k]['attachment']['payload']['title'] = $otn_title;
                    $reply_bot[$k]['attachment']['payload']['payload'] = $otn_postback;
                }
            }

            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }

            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }

            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }


            if($template_type == 'media')
            {   

                $media_postback_hidden_field_ids="media_postback_sort_order_{$k}";
                $media_postback_sort_order_array_str= $_POST[$media_postback_hidden_field_ids];
                preg_match_all("#media_row_(.*?)_{$k}#si", $media_postback_sort_order_array_str, $media_postback_sort_order_match_result);
                $media_postback_sort_order_array=$media_postback_sort_order_match_result[1];


                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                {   

                    $media_postback_sorted_order= $media_postback_sort_order_array[$i-1];


                    $button_text = 'media_text_'.$media_postback_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$media_postback_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$media_postback_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$media_postback_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                     //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'media_call_us_'.$media_postback_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                if(isset($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']))
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'] = array_values($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']);
            }


            if($template_type == 'text_with_buttons')
            {   

                $text_button_hidden_field_ids="text_button_sort_order_{$k}";
                $text_button_sort_order_array_str= $_POST[$text_button_hidden_field_ids];
                preg_match_all("#text_with_buttons_row_(.*?)_{$k}#si", $text_button_sort_order_array_str, $text_button_sort_order_match_result);
                $text_button_sort_order_array=$text_button_sort_order_match_result[1];



                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                {   
                    $text_button_sorted_order= $text_button_sort_order_array[$i-1];

                    $button_text = 'text_with_buttons_text_'.$text_button_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$text_button_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$text_button_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$text_button_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'text_with_button_call_us_'.$text_button_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }

                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';

                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                if(isset($reply_bot[$k]['attachment']['payload']['buttons']))
                    $reply_bot[$k]['attachment']['payload']['buttons'] = array_values($reply_bot[$k]['attachment']['payload']['buttons']);

            }

            if($template_type == 'quick_reply')
            {   
                
                $quick_reply_hidden_field_ids="quick_reply_sort_order_{$k}";
                $quick_reply_sort_order_array_str= $_POST[$quick_reply_hidden_field_ids];
                preg_match_all("#quick_reply_row_(.*?)_{$k}#si", $quick_reply_sort_order_array_str, $quick_reply_sort_order_match_result);
                $quick_reply_sort_order_array=$quick_reply_sort_order_match_result[1];


                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                {   

                    $quick_reply_sorted_order= $quick_reply_sort_order_array[$i-1];

                    $button_text = 'quick_reply_button_text_'.$quick_reply_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$quick_reply_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$quick_reply_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }

                // Re indexing quick reply button array 
                $reply_bot[$k]['quick_replies']=array_values($reply_bot[$k]['quick_replies']);

            }

            if($template_type == 'generic_template')
            {   

                $generic_button_hidden_field_ids="generic_button_sort_order_{$k}";
                $generic_button_sort_order_array_str= $_POST[$generic_button_hidden_field_ids];
                preg_match_all("#generic_template_row_(.*?)_{$k}#si", $generic_button_sort_order_array_str, $generic_button_sort_order_match_result);
                $generic_button_sort_order_array=$generic_button_sort_order_match_result[1];


                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                   
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                

                for ($i=1; $i <= 3 ; $i++) 
                {   
                    $generic_button_sorted_order= $generic_button_sort_order_array[$i-1];

                    $button_text = 'generic_template_button_text_'.$generic_button_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$generic_button_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$generic_button_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$generic_button_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'generic_template_button_call_us_'.$generic_button_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                 // Re indexing buttons array 
                if(isset($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']))
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'] =array_values($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']);


            }

            if($template_type == 'carousel')
            {   

                $carousel_reply_hidden_field_ids="carousel_reply_sort_order_{$k}";
                $carousel_reply_sort_order_array_str= $_POST[$carousel_reply_hidden_field_ids];
                preg_match_all("#carousel_div_(.*?)_{$k}#si", $carousel_reply_sort_order_array_str, $carousel_reply_sort_order_match_result);
                $carousel_reply_sort_order_array=$carousel_reply_sort_order_match_result[1];


                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';

                for ($j=1; $j <=10 ; $j++) 
                {       

                    $carousel_reply_sorted_order= $carousel_reply_sort_order_array[$j-1];


                    $carousel_button_hidden_field_ids="carousel_button_sort_order_{$carousel_reply_sorted_order}_{$k}";
                    $carousel_button_sort_order_array_str= $_POST[$carousel_button_hidden_field_ids];
                    preg_match_all("#carousel_row_{$carousel_reply_sorted_order}_(.*?)_{$k}#si", $carousel_button_sort_order_array_str, $carousel_button_sort_order_match_result);

                    $carousel_button_sort_order_array=$carousel_button_sort_order_match_result[1];



                    $carousel_image = 'carousel_image_'.$carousel_reply_sorted_order.'_'.$k;
                    $carousel_title = 'carousel_title_'.$carousel_reply_sorted_order.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$carousel_reply_sorted_order.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$carousel_reply_sorted_order.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }
                    
                    for ($i=1; $i <= 3 ; $i++) 
                    {   
                        $carousel_button_sorted_order= $carousel_button_sort_order_array[$i-1];

                        $button_text = 'carousel_button_text_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                          $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                        $button_call_us = 'carousel_button_call_us_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';


                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $button_postback_id;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = $bot_name;
                                $postback_insert_data[] = $single_postback_insert_data; 
                            }
                        }
                        if(strpos($button_type,'web_url') !== FALSE)
                        {
                            $button_type_array = explode('_', $button_type);
                            if(isset($button_type_array[2]))
                            {
                                $button_extension = trim($button_type_array[2],'_'); 
                                array_pop($button_type_array);
                            }            
                            else $button_extension = '';
                            $button_type = implode('_', $button_type_array);

                            if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                if($button_extension != '' && $button_extension == 'birthday'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_birthdate');
                                }
                                else if($button_extension != '' && $button_extension == 'email'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_email');
                                }
                                else if($button_extension != '' && $button_extension == 'phone'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_phone');
                                }
                                else if($button_extension != '' && $button_extension == 'location'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_location');
                                }
                                else
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                                {
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                    // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                                }

                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }

                    if(isset($reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']))
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']=array_values($reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']);
                }

                // Added by Konok to reindexing from zero 
                if(isset($reply_bot[$k]['attachment']['payload']['elements']))
                    $reply_bot[$k]['attachment']['payload']['elements'] = array_values($reply_bot[$k]['attachment']['payload']['elements']);


            }


            if(isset($reply_bot[$k]))
            {   
                $typing_on_settings = 'typing_on_enable_'.$k;
                if(!isset($$typing_on_settings)) $typing_on_settings = 'off';
                else $typing_on_settings = $$typing_on_settings;

                $delay_in_reply = 'delay_in_reply_'.$k;
                if(!isset($$delay_in_reply)) $delay_in_reply = 0;
                else $delay_in_reply = $$delay_in_reply;

                $reply_bot[$k]['typing_on_settings'] = $typing_on_settings;
                $reply_bot[$k]['delay_in_reply'] = $delay_in_reply;
                       
                $bot_message[$k]['recipient'] = array('id'=>'replace_id');
                $bot_message[$k]['message'] = $reply_bot[$k];
            }

        }

        $reply_bot_filtered = array();
        $m=0;
        foreach ($bot_message as $value) {
            $m++;
            $reply_bot_filtered[$m] = $value;
        }

        // domain white list section start
        $this->load->library("fb_rx_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
             $domain_only_whitelist= get_domain_only_with_http($value);
             if(in_array($domain_only_whitelist, $white_listed_domain_array)) continue; 

            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$domain_only_whitelist);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $facebook_rx_fb_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $domain_only_whitelist;
                $temp_data['created_at'] = date("Y-m-d H:i:s");
                $domain_whitelist_insert_data[] = $temp_data;
            }
        }


        if(!empty($domain_whitelist_insert_data))
            $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end
        
        $insert_data['message'] = json_encode($reply_bot_filtered,true);
        $insert_data['user_id'] = $this->user_id;        
        $this->basic->insert_data('messenger_bot',$insert_data);
        $messenger_bot_table_id = $this->db->insert_id();
        $postback_insert_data_modified = array();
        $m=0;
        foreach($postback_insert_data as $value)
        {
            $postback_insert_data_modified[$m]['user_id'] = $value['user_id'];
            $postback_insert_data_modified[$m]['postback_id'] = $value['postback_id'];
            $postback_insert_data_modified[$m]['page_id'] = $value['page_id'];
            $postback_insert_data_modified[$m]['bot_name'] = $value['bot_name'];
            $postback_insert_data_modified[$m]['messenger_bot_table_id'] = $messenger_bot_table_id;
            $m++;
        }

        if($keyword_type == 'post-back' && !empty($keywordtype_postback_id))
        {    
            $this->db->where("page_id",$page_table_id);        
            $this->db->where_in("postback_id", $keywordtype_postback_id);
            $this->db->update('messenger_bot_postback', array('use_status' => '1'));
        }
        
        // if(!empty($postback_insert_data_modified))
        // $this->db->insert_batch('messenger_bot_postback',$postback_insert_data_modified);
        // $this->session->set_flashdata('bot_success',1); 
        echo json_encode(array("status" => "1", "message" =>$this->lang->line("new bot settings has been stored successfully.")));
        
    }

    public function template_manager($page_auto_id='0',$iframe='0')
    {
        $media_type = 'fb';
        if($this->session->userdata('selected_global_media_type')) {
            $media_type = $this->session->userdata('selected_global_media_type');
        }
        $data['iframe'] = $iframe;

        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);

        $group_page_list = array();

        $flow_page_list = array();
        if(isset($page_info) && count($page_info) > 0) {
            $flow_page_list['media_name'] = $this->lang->line("Facebook");
            foreach($page_info as $value)
            {
                $flow_page_list['page_list'][$value['id']] = $value['page_name']." [".$value['name']."]";
            }
            array_push($group_page_list,$flow_page_list);
        }

        $data['page_title'] = $this->lang->line('Post-back Manager');
        if(isset($media_type) && $media_type == "ig") {
            $data['page_title'] = $this->lang->line('Instagram Post-back Manager');
            $ig_page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','has_instagram'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','insta_username'),$join);

            $ig_flow_page_list = array();
            if(isset($ig_page_info) && count($ig_page_info) > 0) {
                $ig_flow_page_list['media_name'] = $this->lang->line("Instagram");
                foreach($ig_page_info as $ig_value)
                {
                    $ig_flow_page_list['page_list'][$ig_value['id']."-".$media_type] = $ig_value['page_name']." [".$ig_value['insta_username']."]";
                }
                array_push($group_page_list,$ig_flow_page_list);
            }
            $data['ig_flow_page_list'] = $ig_flow_page_list;
        }


        $data['flow_page_list'] = $flow_page_list;
        $data['group_page_list'] = $group_page_list;

        $flow_page_list2 = array();
        foreach($page_info as $value)
        {
            $flow_page_list2[$value['id']] = $value['page_name']." [".$value['name']."]";
        }
        
        $data['page_list'] = $flow_page_list2;
        $data['page_info'] = $page_info;

        if($this->addon_exist("visual_flow_builder"))
        {
            $data['visual_flow_builder_exist'] = 'yes';
            if($this->session->userdata('user_type') == 'Admin' || in_array(315,$this->module_access))
                $data['builder_access'] = 'yes';
            else
                $data['builder_access'] = 'no';
        }
        else
            $data['visual_flow_builder_exist'] = 'no';

        // if($media_type == '') redirect("error_404","refresh");

        $data['media_type'] = $media_type;
        $data['page_id'] = $page_auto_id;
        $data['body'] = 'messenger_tools/template_manager';
        $this->_viewcontroller($data);
    }

    public function template_manager_data()
    {
        $this->ajax_check();
        $page_id = $this->input->post('page_id',true);
        $template_media_type = $this->input->post('template_media_type',true);
        $postback_id = $_POST['search']['value'];
        if($this->addon_exist("visual_flow_builder"))
            $display_columns = array("#","CHECKBOX",'id', 'bot_name', 'postback_id', 'visual_flow_type', 'action');
        else
            $display_columns = array("#","CHECKBOX",'id', 'bot_name', 'postback_id', 'action');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;


        $where_simple = array();
        $where_simple['user_id'] = $this->user_id;
        $where_simple['media_type'] = $template_media_type;
        $where_simple['page_id'] = $page_id;
        $where_simple['is_template'] = '1';
        $where_simple['template_for'] = 'reply_message';

        if($postback_id != '') $where_simple['postback_id like'] = "%".$postback_id."%";

        $table="messenger_bot_postback";
        $where = array('where'=>$where_simple);

        $info=$this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $i=0;
        $base_url=base_url();
        foreach ($info as $key => $value) 
        {
            $delete_btn = "";
            $action = "<div style='min-width:120px'><a href='#' class='btn btn-circle btn-outline-info get_json_code' title='".$this->lang->line("Get JSON Code")."' table_id='".$value['id']."'><i class='fas fa-code'></i></a>&nbsp;";

            if(addon_exist($module_id=315,$addon_unique_name="visual_flow_builder") && isset($value['visual_flow_type']) && $value['visual_flow_type'] == 'flow') {
                $builder_url = base_url("visual_flow_builder/edit_builder_data/{$value['visual_flow_campaign_id']}/1/{$value['media_type']}");
                $action .= "<a target='_BLANK' class='btn btn-circle btn-outline-warning' title='". $this->lang->line("Edit") ."' href='".$builder_url."'><i class='fas fa-edit'></i></a>&nbsp;";
                $info[$i]["visual_flow_type"] = $this->lang->line('Flow Builder');
            }
            else {
                $delete_btn = "<a href='#' class='btn btn-circle btn-outline-danger delete_template' title='".$this->lang->line("Delete")."' table_id='".$value['id']."'><i class='fa fa-trash'></i></a>";
                $temp = base_url("messenger_bot/edit_template/{$value["id"]}/1/0/{$value["media_type"]}");
                $action .= "<a class='btn btn-circle btn-outline-warning' title='".$this->lang->line("Edit")."' href='".$temp."'><i class='fas fa-edit'></i></a>&nbsp;";
                $info[$i]["visual_flow_type"] = $this->lang->line('Classic Editor');
            }

            $temp = base_url("messenger_bot/clone_template/{$value["id"]}/1/0/{$value["media_type"]}");
            $action .= "<a class='btn btn-circle btn-outline-primary' title='". $this->lang->line("Clone") ."' href='".$temp."'><i class='far fa-copy'></i></a>&nbsp;";

            $action .= $delete_btn;

            $action .= "</div>";
            $info[$i]["action"] = $action;
            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);

    }

    public function create_new_template($is_iframe="0",$default_page="",$default_child_postback_id="",$media_type='fb')
    {
        $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
        $this->is_sms_email_drip_campaigner_exist=$this->sms_email_drip_campaigner_exist();
        $data['body'] = 'messenger_tools/add_new_template';
        $data['page_title'] = $this->lang->line('Add Facebook Post-back Template');
        if($media_type == "ig") {
            $data['page_title'] = $this->lang->line('Add Instagram Post-back Template');
        }

        $custom_field_exist = 'no';
        if($this->addon_exist("custom_field_manager"))
        {
            $custom_field_exist = 'yes';
            if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
                $custom_field_exist = 'no';
        }
        $data['custom_field_exist'] = $custom_field_exist;

        $template_types=$this->basic->get_enum_values("messenger_bot","template_type");

        $data['media_type'] = $media_type;

        if(!$this->addon_exist("custom_field_manager"))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        } 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        }
        if($media_type == 'ig')
        {
            $need_to_remove = [
                                'audio',
                                'video',
                                'file',
                                'text with buttons',
                                'media',
                                'One Time Notification'
                            ];
            foreach ($need_to_remove as $value) {
                if (($key = array_search($value, $template_types)) !== false) {
                    unset($template_types[$key]);
                }
            }
        }
        $data["templates"]=$template_types;

        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");

        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        if($media_type == 'ig')
            $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','has_instagram'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','insta_username'),$join);
        else
            $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);

        $page_list = array();
        foreach($page_info as $value)
        {
            if($media_type == 'ig')
                $page_list[$value['id']] = $value['page_name']." [".$value['insta_username']."]";
            else
                $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";

        }
        $data['page_list'] = $page_list;

        $data['is_iframe'] = $is_iframe;
        $data['iframe'] = $is_iframe;
        if($default_page == 0) $default_page = '';
        $data['default_page'] = $default_page;
        if($default_child_postback_id == 0) $default_child_postback_id = '';
        $data['default_child_postback_id'] = $default_child_postback_id;
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id)));

        $data['postback_ids'] = $postback_id_list;
        if($this->basic->is_exist("add_ons",array("project_id"=>16))) $data['has_broadcaster_addon'] = 1;
        else  $data['has_broadcaster_addon'] = 0;

        $this->_viewcontroller($data); 
    }
    
    public function create_template_action()
    {
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            if(!is_array($value))
                $temp = strip_tags($value);
            else
                $temp = $value;

            $$key=$temp;
        }


        // Added by Konok for sorting main reply order 22.08.2020

        $main_reply_sort_order_serialize= $_POST['main_reply_sort_order'];
        parse_str($main_reply_sort_order_serialize,$main_reply_sort_order_array);
        $main_reply_final_order = $main_reply_sort_order_array['multiple_template_div'];


        $this->db->trans_start();
        $user_all_postback = array();
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_table_id)));

        foreach($postback_id_list as $value)
        {
            $temp_postback_id = trim($value['postback_id']);
            array_push($user_all_postback, $temp_postback_id);
        }

        // $template_type = trim($template_type);
        $insert_data = array();
        $insert_data_to_bot = array();
        $insert_data['bot_name'] = $bot_name;
        $insert_data_to_bot['bot_name'] = $bot_name;
        $insert_data['template_name'] = $bot_name;
        $insert_data['postback_id'] = trim($template_postback_id);
        $insert_data_to_bot['postback_id'] = trim($template_postback_id);
        $insert_data['page_id'] = $page_table_id;
        $insert_data_to_bot['page_id'] = $page_table_id;
        $insert_data['is_template'] = '1';
        $insert_data_to_bot['is_template'] = '1';
        $insert_data['use_status'] = '1';

        $insert_data['media_type'] = $media_type;
        $insert_data_to_bot['media_type'] = $media_type;

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        $label_ids=array_filter($label_ids);
        $new_label_ids=implode(',', $label_ids);
        $insert_data["broadcaster_labels"]=$new_label_ids;
        $insert_data_to_bot["broadcaster_labels"]=$new_label_ids;

        if(!isset($drip_campaign_id) || !is_array($drip_campaign_id)) $drip_campaign_id=array();
        $drip_campaign_id=array_filter($drip_campaign_id);
        $new_drip_campaign_id=implode(',', $drip_campaign_id);
        $insert_data["drip_campaign_id"]=$new_drip_campaign_id;
        $insert_data_to_bot["drip_campaign_id"]=$new_drip_campaign_id;
        
        $facebook_rx_fb_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_access_token","page_id"));
        $insert_data_to_bot['fb_page_id'] = $facebook_rx_fb_user_info_id[0]['page_id'];
        
        $page_access_token = $facebook_rx_fb_user_info_id[0]['page_access_token'];
        $facebook_rx_fb_user_info_id = $facebook_rx_fb_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$facebook_rx_fb_user_info_id,"page_id"=>$page_table_id)),"domain");

        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();
        // domain white list section

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();



        //for ($k=1; $k <=6 ; $k++) 
        for ($no_reply_org=1; $no_reply_org <=6 ; $no_reply_org++) 

        {       
            // assaign $k variable with in order of main reply 

            $k= $main_reply_final_order[$no_reply_org-1];

            $template_type = 'template_type_'.$k;
            if(!isset($$template_type)) continue;
            $template_type = $$template_type;
            $template_type = str_replace(' ', '_', $template_type);


            if($template_type == 'Ecommerce')
            {
                $this->load->helper('ecommerce_helper');
                $currency_icons = $this->currency_icon();

                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';

                $buy_now_text = 'ecommerce_button_text'.$k;
                $buy_now_text = isset($$buy_now_text) ? $$buy_now_text : $this->lang->line('Buy Now');
                $carousel_prodcuts_array = 'ecommerce_product_ids'.$k;
                $carousel_prodcuts = isset($$carousel_prodcuts_array) ? $$carousel_prodcuts_array : [];
                $index_variable = 0;
                foreach($carousel_prodcuts as $value)
                {
                    $product_data = $this->basic->get_data('ecommerce_product',['where'=>['id'=>$value,'user_id'=>$this->user_id]],['store_id','product_name','original_price','sell_price','thumbnail','id','woocommerce_product_id']);
                    if(!isset($product_data[0])) continue;
                    $store_id = isset($product_data[0]['store_id']) ? $product_data[0]['store_id'] : 0;
                    $ecommerce_config = $this->basic->get_data('ecommerce_config',['where'=>['user_id'=>$this->user_id,'store_id'=>$store_id]],['currency','currency_position','decimal_point','thousand_comma']);

                    $original_price = isset($product_data[0]['original_price']) ? $product_data[0]['original_price'] : 0;
                    $sell_price = isset($product_data[0]['sell_price']) ? $product_data[0]['sell_price'] : 0;
                    $currency_position = isset($ecommerce_config[0]['currency_position']) ? $ecommerce_config[0]['currency_position'] : 'left';
                    $decimal_point = isset($ecommerce_config[0]['decimal_point']) ? $ecommerce_config[0]['decimal_point'] : 0;
                    $thousand_comma = isset($ecommerce_config[0]['thousand_comma']) ? $ecommerce_config[0]['thousand_comma'] : 0;
                    $currency_icon = '$';
                    if(isset($ecommerce_config[0]['currency']))
                    {
                        $currency_icon = isset($currency_icons[$ecommerce_config[0]['currency']]) ? $currency_icons[$ecommerce_config[0]['currency']] : '$';
                    }
                    $subtitle = mec_display_price($original_price,$sell_price,$currency_icon,'2',$currency_position,$decimal_point,$thousand_comma);
                    if($currency_position == 'left') $subtitle = $currency_icon.$subtitle;
                    else $subtitle = $subtitle.$currency_icon;
                    $title = isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : '';

                    // $thumbnail = isset($product_data[0]['thumbnail']) ? base_url('upload/ecommerce/').$product_data[0]['thumbnail'] : '';

                    $thumbnail = ($product_data[0]['thumbnail']!='') ? base_url('upload/ecommerce/'.$product_data[0]['thumbnail']) : base_url('assets/img/products/product-1.jpg');
                    if(isset($product_data[0]["woocommerce_product_id"]) && !is_null($product_data[0]["woocommerce_product_id"]) && $product_data[0]['thumbnail']!='')
                    $thumbnail = $product_data[0]['thumbnail'];


                    if(function_exists('getimagesize') && $thumbnail!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($thumbnail);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }
                    $buy_now_url = base_url('ecommerce/product/').$value;
                    $buy_now_url = add_query_string_to_url($buy_now_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    if(!in_array($buy_now_url, $white_listed_domain_array))
                    {
                        $need_to_whitelist_array[] = $buy_now_url;
                    }

                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['title'] = $title;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['subtitle'] = $subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['image_url'] = $thumbnail;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['default_action']['type'] = 'web_url';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['default_action']['url'] = $buy_now_url;

                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['type'] = 'web_url';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['messenger_extensions'] = 'true';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['webview_height_ratio'] = 'full';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['url'] = $buy_now_url;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['title'] = $buy_now_text;

                    $index_variable++;
                }
            }


            if($template_type == 'User_Input_Flow')
            {
                $flow_campaign_id = 'flow_campaign_id_'.$k;
                $flow_campaign_id = isset($$flow_campaign_id) ? $$flow_campaign_id : 0;
                if($flow_campaign_id != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['flow_campaign_id'] = $flow_campaign_id;
                    
                }
            }

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $text_reply;
                    
                }
            }

            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }

            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }

            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }

            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }

            if($template_type == 'One_Time_Notification')
            {
                $otn_title = 'otn_title_'.$k;
                $otn_postback = 'otn_postback_'.$k;
                $otn_title = isset($$otn_title) ? $$otn_title : '';
                $otn_postback = isset($$otn_postback) ? $$otn_postback : '';
                if($otn_title != '' && $otn_postback != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = "one_time_notif_req";
                    $reply_bot[$k]['attachment']['payload']['title'] = $otn_title;
                    $reply_bot[$k]['attachment']['payload']['payload'] = $otn_postback;
                }
            }

 
            if($template_type == 'media')
            {
                $media_postback_hidden_field_ids="media_postback_sort_order_{$k}";
                $media_postback_sort_order_array_str= $_POST[$media_postback_hidden_field_ids];
                preg_match_all("#media_row_(.*?)_{$k}#si", $media_postback_sort_order_array_str, $media_postback_sort_order_match_result);
                $media_postback_sort_order_array=$media_postback_sort_order_match_result[1];


                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 

                    $media_postback_sorted_order= $media_postback_sort_order_array[$i-1];


                    $button_text = 'media_text_'.$media_postback_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$media_postback_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$media_postback_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$media_postback_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                     //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'media_call_us_'.$media_postback_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = trim($button_postback_id);
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                if(isset($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']))
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'] = array_values($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']);
            }


            if($template_type == 'text_with_buttons')
            {
                $text_button_hidden_field_ids="text_button_sort_order_{$k}";
                $text_button_sort_order_array_str= $_POST[$text_button_hidden_field_ids];
                preg_match_all("#text_with_buttons_row_(.*?)_{$k}#si", $text_button_sort_order_array_str, $text_button_sort_order_match_result);
                $text_button_sort_order_array=$text_button_sort_order_match_result[1];


                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                {   

                    $text_button_sorted_order= $text_button_sort_order_array[$i-1];


                    $button_text = 'text_with_buttons_text_'.$text_button_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$text_button_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$text_button_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$text_button_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'text_with_button_call_us_'.$text_button_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = trim($button_postback_id);
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }

                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';

                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                if(isset($reply_bot[$k]['attachment']['payload']['buttons']))
                    $reply_bot[$k]['attachment']['payload']['buttons'] = array_values($reply_bot[$k]['attachment']['payload']['buttons']);
            }

            if($template_type == 'quick_reply')
            {
                $quick_reply_hidden_field_ids="quick_reply_sort_order_{$k}";
                $quick_reply_sort_order_array_str= $_POST[$quick_reply_hidden_field_ids];
                preg_match_all("#quick_reply_row_(.*?)_{$k}#si", $quick_reply_sort_order_array_str, $quick_reply_sort_order_match_result);
                $quick_reply_sort_order_array=$quick_reply_sort_order_match_result[1];

                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                // Chnaged by Konok 22-080-2020 to apply sorting ability 

                for ($i=1; $i <= 11 ; $i++) 
                { 

                    $quick_reply_sorted_order= $quick_reply_sort_order_array[$i-1];
                    

                    $button_text = 'quick_reply_button_text_'.$quick_reply_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';

                    $button_postback_id = 'quick_reply_post_id_'.$quick_reply_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$quick_reply_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';

                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = trim($button_postback_id);
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }

                // Re indexing quick reply button array 
                $reply_bot[$k]['quick_replies']=array_values($reply_bot[$k]['quick_replies']);

            }

            if($template_type == 'generic_template')
            {
                $generic_button_hidden_field_ids="generic_button_sort_order_{$k}";
                $generic_button_sort_order_array_str= $_POST[$generic_button_hidden_field_ids];
                preg_match_all("#generic_template_row_(.*?)_{$k}#si", $generic_button_sort_order_array_str, $generic_button_sort_order_match_result);
                $generic_button_sort_order_array=$generic_button_sort_order_match_result[1];


                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                   
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    
                    $generic_button_sorted_order= $generic_button_sort_order_array[$i-1];


                    $button_text = 'generic_template_button_text_'.$generic_button_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$generic_button_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$generic_button_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$generic_button_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'generic_template_button_call_us_'.$generic_button_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = trim($button_postback_id);
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                // Re indexing buttons array 
                if(isset($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']))
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'] =array_values($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']);


            }

            if($template_type == 'carousel')
            {

                $carousel_reply_hidden_field_ids="carousel_reply_sort_order_{$k}";
                $carousel_reply_sort_order_array_str= $_POST[$carousel_reply_hidden_field_ids];
                preg_match_all("#carousel_div_(.*?)_{$k}#si", $carousel_reply_sort_order_array_str, $carousel_reply_sort_order_match_result);
                $carousel_reply_sort_order_array=$carousel_reply_sort_order_match_result[1];


                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';


                for ($j=1; $j <=10 ; $j++) 
                {   

                    $carousel_reply_sorted_order= $carousel_reply_sort_order_array[$j-1];


                    $carousel_button_hidden_field_ids="carousel_button_sort_order_{$carousel_reply_sorted_order}_{$k}";
                    $carousel_button_sort_order_array_str= $_POST[$carousel_button_hidden_field_ids];
                    preg_match_all("#carousel_row_{$carousel_reply_sorted_order}_(.*?)_{$k}#si", $carousel_button_sort_order_array_str, $carousel_button_sort_order_match_result);

                    $carousel_button_sort_order_array=$carousel_button_sort_order_match_result[1];


                    $carousel_image = 'carousel_image_'.$carousel_reply_sorted_order.'_'.$k;
                    $carousel_title = 'carousel_title_'.$carousel_reply_sorted_order.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$carousel_reply_sorted_order.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$carousel_reply_sorted_order.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }
                    
                    for ($i=1; $i <= 3 ; $i++) 
                    { 

                        $carousel_button_sorted_order= $carousel_button_sort_order_array[$i-1];

                        $button_text = 'carousel_button_text_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                          $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                        $button_call_us = 'carousel_button_call_us_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = trim($button_postback_id);
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = $bot_name;
                                $postback_insert_data[] = $single_postback_insert_data; 
                            }
                        }
                        if(strpos($button_type,'web_url') !== FALSE)
                        {
                            $button_type_array = explode('_', $button_type);
                            if(isset($button_type_array[2]))
                            {
                                $button_extension = trim($button_type_array[2],'_'); 
                                array_pop($button_type_array);
                            }            
                            else $button_extension = '';
                            $button_type = implode('_', $button_type_array);

                            if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                if($button_extension != '' && $button_extension == 'birthday'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_birthdate');
                                }
                                else if($button_extension != '' && $button_extension == 'email'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_email');
                                }
                                else if($button_extension != '' && $button_extension == 'phone'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_phone');
                                }
                                else if($button_extension != '' && $button_extension == 'location'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_location');
                                }
                                else
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                                {
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                    // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                                }

                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }

                    if(isset($reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']))
	                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']=array_values($reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']);

                }

                // Added by Konok to reindexing from zero 
                if(isset($reply_bot[$k]['attachment']['payload']['elements']))
                    $reply_bot[$k]['attachment']['payload']['elements'] = array_values($reply_bot[$k]['attachment']['payload']['elements']);
            }

            if(isset($reply_bot[$k]))
            {       
                $typing_on_settings = 'typing_on_enable_'.$k;
                if(!isset($$typing_on_settings)) $typing_on_settings = 'off';
                else $typing_on_settings = $$typing_on_settings;

                $delay_in_reply = 'delay_in_reply_'.$k;
                if(!isset($$delay_in_reply)) $delay_in_reply = 0;
                else $delay_in_reply = $$delay_in_reply;

                $reply_bot[$k]['typing_on_settings'] = $typing_on_settings;
                $reply_bot[$k]['delay_in_reply'] = $delay_in_reply;

                $bot_message[$k]['recipient'] = array('id'=>'replace_id');
                $bot_message[$k]['message'] = $reply_bot[$k];
            }

        }

        $reply_bot_filtered = array();
        $m=0;
        foreach ($bot_message as $value) {
            $m++;
            $reply_bot_filtered[$m] = $value;
        }

        // domain white list section start
        $this->load->library("fb_rx_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $domain_only_whitelist= get_domain_only_with_http($value);
            if(in_array($domain_only_whitelist, $white_listed_domain_array)) continue; 

            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$domain_only_whitelist);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $facebook_rx_fb_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $domain_only_whitelist;
                $temp_data['created_at'] = date("Y-m-d H:i:s");
                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data))
            $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end
        
        $insert_data['template_jsoncode'] = json_encode($reply_bot_filtered,true);
        $insert_data_to_bot['message'] = json_encode($reply_bot_filtered,true);
        $insert_data['user_id'] = $this->user_id;        
        $insert_data_to_bot['user_id'] = $this->user_id;        
        $this->basic->insert_data('messenger_bot',$insert_data_to_bot);
        $messenger_bot_table_id = $this->db->insert_id();


        if($postback_type == 'child')
        {
            $template_json = json_encode($reply_bot_filtered,true);
            $postback_update_data = array('use_status'=>'1','messenger_bot_table_id'=>$messenger_bot_table_id,'template_jsoncode'=>$template_json,'is_template'=>'1','bot_name'=>$bot_name,'template_name'=>$bot_name,'template_id'=>'0');
            $this->basic->update_data('messenger_bot_postback',array('postback_id'=>$template_postback_id,'page_id'=>$page_table_id,'user_id'=>$this->user_id),$postback_update_data);
            $template_info = $this->basic->get_data('messenger_bot_postback',array('where'=>array('postback_id'=>$template_postback_id,'page_id'=>$page_table_id,'user_id'=>$this->user_id)));
            if(!empty($template_info)) $template_id = $template_info[0]['id'];
            else $template_id = 0;
        }
        else
        {
            $insert_data['messenger_bot_table_id'] = $messenger_bot_table_id;
            $this->basic->insert_data('messenger_bot_postback',$insert_data);
            $template_id = $this->db->insert_id();            
        }
 

        $postback_insert_data_modified = array();

        $m=0;

        $unique_postbacks = array();

        foreach($postback_insert_data as $value)
        {
            if(in_array($value['postback_id'], $user_all_postback)) continue;
            if(in_array($value['postback_id'], $unique_postbacks)) continue;

            $postback_insert_data_modified[$m]['user_id'] = $value['user_id'];
            $postback_insert_data_modified[$m]['postback_id'] = $value['postback_id'];
            $postback_insert_data_modified[$m]['page_id'] = $value['page_id'];
            $postback_insert_data_modified[$m]['bot_name'] = $value['bot_name'];
            $postback_insert_data_modified[$m]['template_id'] = $template_id;
            $postback_insert_data_modified[$m]['inherit_from_template'] = '1';
            array_push($unique_postbacks, $value['postback_id']);
            $m++;
        }
        
        if(!empty($postback_insert_data_modified))
        $this->db->insert_batch('messenger_bot_postback',$postback_insert_data_modified);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Creating template was unsuccessful. Database error occured during creating template.")));
            exit();
        }
        else
        {
            $this->session->set_flashdata('bot_success',1);
            echo json_encode(array("status" => "1", "message" =>$this->lang->line("New template has been stored successfully.")));
        }
        
    }

    public function edit_template($postback_table_id=0,$iframe='0',$is_default='0',$media_type='fb')
    {
        if($postback_table_id == 0) exit();
        $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
        $this->is_sms_email_drip_campaigner_exist=$this->sms_email_drip_campaigner_exist();
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        $table_name = "messenger_bot_postback";
        $where_bot['where'] = array('id' => $postback_table_id, 'status' => '1', 'user_id'=>$this->user_id);
        $bot_info = $this->basic->get_data($table_name, $where_bot);
        if(empty($bot_info)) redirect('messenger_bot/template_manager', 'location');

      
        $full_message_json = $bot_info[0]['template_jsoncode'];
        $full_message_array = json_decode($full_message_json,true);
        $store_list = [];
        $store_info = [];
        $all_products = [];
        // foreach($full_message_array as $value)
        // {
        //     if($value['message']['template_type'] != 'Ecommerce') continue;
            
            $temp_page_table_id = $bot_info[0]["page_id"];
            $temp_select = ['ecommerce_store.id as store_table_id','ecommerce_product.id as product_table_id','store_name','store_city','product_name'];
            $temp_join = ['ecommerce_product'=>'ecommerce_product.store_id=ecommerce_store.id,left'];
            $page_wise_store_products = $this->basic->get_data('ecommerce_store',['where'=>['ecommerce_store.user_id'=>$this->user_id,'page_id'=>$temp_page_table_id]],$temp_select,$temp_join);
            foreach($page_wise_store_products as $temp)
            {
                $store_list[$temp['store_table_id']] = $temp['store_name']." - ".$temp['store_city'];
                $store_info[$temp['store_table_id']][$temp['product_table_id']] = $temp['product_name'];
                $all_products[$temp['product_table_id']] = $temp['store_table_id'];
            }

        // }
        $data['store_list'] = $store_list;
        $data['store_info'] = $store_info;
        $data['all_products'] = $all_products;
        
        $data['body'] = 'messenger_tools/edit_template';
        $data['page_title'] = $this->lang->line('Edit Facebook Post-back template');
        if($media_type == "ig") {
            $data['page_title'] = $this->lang->line('Edit Instagram Post-back Template');
        }

        $template_types=$this->basic->get_enum_values("messenger_bot","template_type");

        $data['media_type'] = $media_type;

        if(!$this->addon_exist("custom_field_manager"))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        } 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        }
        if($media_type == 'ig')
        {
            $need_to_remove = [
                                'audio',
                                'video',
                                'file',
                                'text with buttons',
                                'media',
                                'One Time Notification'
                            ];
            foreach ($need_to_remove as $value) {
                if (($key = array_search($value, $template_types)) !== false) {
                    unset($template_types[$key]);
                }
            }
        }
        $data["templates"]=$template_types;

        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);
        $page_list = array();
        foreach($page_info as $value)
        {
            $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
        }
        $data['page_list'] = $page_list;
        $data['bot_info'] = isset($bot_info[0]) ? $bot_info[0] : array();

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"],"media_type"=>$media_type),'where_not_in'=>array('postback_id'=>array('UNSUBSCRIBE_QUICK_BOXER','RESUBSCRIBE_QUICK_BOXER','YES_START_CHAT_WITH_HUMAN','YES_START_CHAT_WITH_BOT'))));

        $current_postbacks = array();
        foreach ($postback_id_list as $value) {
            if($value['template_id'] == $postback_table_id || $value['id'] == $postback_table_id)
            $current_postbacks[] = $value['postback_id'];
        }
        $data['postback_ids'] = $postback_id_list;
        $data['current_postbacks'] = $current_postbacks;

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"],"unsubscribe"=>"0","invisible"=>"0","social_media"=>$media_type);
        $data['info_type'] = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        if($this->is_drip_campaigner_exist || $this->is_sms_email_drip_campaigner_exist)
        {          

            $table_type = 'messenger_bot_drip_campaign';
            $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"],"media_type"=>$media_type);
            $data['dripcampaign_list'] = $this->basic->get_data($table_type,$where_type,$select='');
        }
        else 
        {
            $data['dripcampaign_list']=array();
        }


        if($this->is_broadcaster_exist)
            $data['has_broadcaster_addon'] = 1;
        else
            $data['has_broadcaster_addon'] = 0;

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"],'template_for'=>'reply_message','media_type'=>$media_type)),array('postback_id','bot_name'));
        $postback_dropdown = array();
        if(!empty($postback_id_list))
        {
            foreach($postback_id_list as $value)
                $postback_dropdown[$value['postback_id']] = $value['postback_id'];
                // array_push($postback_dropdown, $value['postback_id']);
        }
        $data['postback_dropdown'] = $postback_dropdown;

        $otn_postbacks = [];
        $otn_postback_info = $this->basic->get_data('otn_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"])),array('id','otn_postback_id','template_name'));
        foreach($otn_postback_info as $value)
        {
            $otn_postbacks[$value['id']] = $value['otn_postback_id']." (".$value['template_name'].")";
        }
        $data['otn_postback_list'] = $otn_postbacks;

        if($this->addon_exist("custom_field_manager"))
            $flow_campaign_info = $this->basic->get_data('user_input_flow_campaign',['where'=>['user_id'=>$this->user_id,'page_table_id'=>$bot_info[0]["page_id"],"media_type"=>$media_type]]);
        else
           $flow_campaign_info = []; 
        $data['flow_campaigns'] = $flow_campaign_info;


        $data['iframe'] = $iframe;
        $data['is_default'] = $is_default;

        $data['iframe']=$iframe;
        $this->_viewcontroller($data);  
    }

    public function edit_template_action()
    {  
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            if(!is_array($value))
                $temp = strip_tags($value);
            else
                $temp = $value;

            $$key=$temp;
        }

        // Added by Konok for sorting main reply order 22.08.2020

        $main_reply_sort_order_serialize= $_POST['main_reply_sort_order'];
        parse_str($main_reply_sort_order_serialize,$main_reply_sort_order_array);
        $main_reply_final_order = $main_reply_sort_order_array['multiple_template_div'];

        $this->db->trans_start();
        // $template_type = trim($template_type);
        $insert_data = array();
        $insert_data['bot_name'] = $bot_name;
        $insert_data['template_name'] = $bot_name;
        $insert_data['postback_id'] = $template_postback_id;
        $insert_data['page_id'] = $page_table_id;
        $insert_data['is_template'] = '1';

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        $label_ids=array_filter($label_ids);
        $new_label_ids=implode(',', $label_ids);
        $insert_data["broadcaster_labels"]=$new_label_ids;

        if(!isset($drip_campaign_id) || !is_array($drip_campaign_id)) $drip_campaign_id=array();
        $drip_campaign_id=array_filter($drip_campaign_id);
        $new_drip_campaign_id=implode(',', $drip_campaign_id);
        $insert_data["drip_campaign_id"]=$new_drip_campaign_id;

        // domain white list section
        $facebook_rx_fb_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_access_token"));
        $page_access_token = $facebook_rx_fb_user_info_id[0]['page_access_token'];
        $facebook_rx_fb_user_info_id = $facebook_rx_fb_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$facebook_rx_fb_user_info_id,"page_id"=>$page_table_id)),"domain");
        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();
        // domain white list section

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();

        for ($no_reply_org=1; $no_reply_org <=6 ; $no_reply_org++) 
        {    

            // assaign $k variable with in order of main reply 
            $k= $main_reply_final_order[$no_reply_org-1];


            $template_type = 'template_type_'.$k;
            if(!isset($$template_type)) continue;
            $template_type = $$template_type;
            // $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);
            
            if($template_type == 'Ecommerce')
            {
                $this->load->helper('ecommerce_helper');
                $currency_icons = $this->currency_icon();

                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';

                $buy_now_text = 'ecommerce_button_text'.$k;
                $buy_now_text = isset($$buy_now_text) ? $$buy_now_text : $this->lang->line('Buy Now');
                $carousel_prodcuts_array = 'ecommerce_product_ids'.$k;
                $carousel_prodcuts = isset($$carousel_prodcuts_array) ? $$carousel_prodcuts_array : [];
                $index_variable = 0;
                foreach($carousel_prodcuts as $value)
                {
                    $product_data = $this->basic->get_data('ecommerce_product',['where'=>['id'=>$value,'user_id'=>$this->user_id]],['store_id','product_name','original_price','sell_price','thumbnail','id','woocommerce_product_id']);
                    if(!isset($product_data[0])) continue;
                    $store_id = isset($product_data[0]['store_id']) ? $product_data[0]['store_id'] : 0;
                    $ecommerce_config = $this->basic->get_data('ecommerce_config',['where'=>['user_id'=>$this->user_id,'store_id'=>$store_id]],['currency','currency_position','decimal_point','thousand_comma']);

                    $original_price = isset($product_data[0]['original_price']) ? $product_data[0]['original_price'] : 0;
                    $sell_price = isset($product_data[0]['sell_price']) ? $product_data[0]['sell_price'] : 0;
                    $currency_position = isset($ecommerce_config[0]['currency_position']) ? $ecommerce_config[0]['currency_position'] : 'left';
                    $decimal_point = isset($ecommerce_config[0]['decimal_point']) ? $ecommerce_config[0]['decimal_point'] : 0;
                    $thousand_comma = isset($ecommerce_config[0]['thousand_comma']) ? $ecommerce_config[0]['thousand_comma'] : 0;
                    $currency_icon = '$';
                    if(isset($ecommerce_config[0]['currency']))
                    {
                        $currency_icon = isset($currency_icons[$ecommerce_config[0]['currency']]) ? $currency_icons[$ecommerce_config[0]['currency']] : '$';
                    }
                    $subtitle = mec_display_price($original_price,$sell_price,$currency_icon,'2',$currency_position,$decimal_point,$thousand_comma);
                    if($currency_position == 'left') $subtitle = $currency_icon.$subtitle;
                    else $subtitle = $subtitle.$currency_icon;
                    $title = isset($product_data[0]['product_name']) ? $product_data[0]['product_name'] : '';

                    // $thumbnail = isset($product_data[0]['thumbnail']) ? base_url('upload/ecommerce/').$product_data[0]['thumbnail'] : '';

                    $thumbnail = ($product_data[0]['thumbnail']!='') ? base_url('upload/ecommerce/'.$product_data[0]['thumbnail']) : base_url('assets/img/products/product-1.jpg');
                    if(isset($product_data[0]["woocommerce_product_id"]) && !is_null($product_data[0]["woocommerce_product_id"]) && $product_data[0]['thumbnail']!='')
                    $thumbnail = $product_data[0]['thumbnail'];


                    if(function_exists('getimagesize') && $thumbnail!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($thumbnail);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }
                    $buy_now_url = base_url('ecommerce/product/').$value;
                    $buy_now_url = add_query_string_to_url($buy_now_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['title'] = $title;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['subtitle'] = $subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['image_url'] = $thumbnail;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['default_action']['type'] = 'web_url';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['default_action']['url'] = $buy_now_url;

                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['type'] = 'web_url';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['messenger_extensions'] = 'true';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['webview_height_ratio'] = 'full';
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['url'] = $buy_now_url;
                    $reply_bot[$k]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['title'] = $buy_now_text;

                    $index_variable++;
                }
            }


            if($template_type == 'User_Input_Flow')
            {
                $flow_campaign_id = 'flow_campaign_id_'.$k;
                $flow_campaign_id = isset($$flow_campaign_id) ? $$flow_campaign_id : 0;
                if($flow_campaign_id != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['flow_campaign_id'] = $flow_campaign_id;
                    
                }
            }

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $text_reply;
                    
                }
            }

            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }

            if($template_type == 'One_Time_Notification')
            {
                $otn_title = 'otn_title_'.$k;
                $otn_postback = 'otn_postback_'.$k;
                $otn_title = isset($$otn_title) ? $$otn_title : '';
                $otn_postback = isset($$otn_postback) ? $$otn_postback : '';
                if($otn_postback != '' && $otn_postback != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = "one_time_notif_req";
                    $reply_bot[$k]['attachment']['payload']['title'] = $otn_title;
                    $reply_bot[$k]['attachment']['payload']['payload'] = $otn_postback;
                }
            }

            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }

            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }

            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }

 
            if($template_type == 'media')
            {

                $media_postback_hidden_field_ids="media_postback_sort_order_{$k}";
                $media_postback_sort_order_array_str= $_POST[$media_postback_hidden_field_ids];
                preg_match_all("#media_row_(.*?)_{$k}#si", $media_postback_sort_order_array_str, $media_postback_sort_order_match_result);
                $media_postback_sort_order_array=$media_postback_sort_order_match_result[1];


                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                {   
                    $media_postback_sorted_order= $media_postback_sort_order_array[$i-1];


                    $button_text = 'media_text_'.$media_postback_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$media_postback_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$media_postback_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$media_postback_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                      $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'media_call_us_'.$media_postback_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                if(isset($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']))
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'] = array_values($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']);

            }



            if($template_type == 'text_with_buttons')
            {

                $text_button_hidden_field_ids="text_button_sort_order_{$k}";
                $text_button_sort_order_array_str= $_POST[$text_button_hidden_field_ids];
                preg_match_all("#text_with_buttons_row_(.*?)_{$k}#si", $text_button_sort_order_array_str, $text_button_sort_order_match_result);
                $text_button_sort_order_array=$text_button_sort_order_match_result[1];



                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                {   

                    $text_button_sorted_order= $text_button_sort_order_array[$i-1];

                    $button_text = 'text_with_buttons_text_'.$text_button_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$text_button_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$text_button_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$text_button_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");


                    $button_call_us = 'text_with_button_call_us_'.$text_button_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';

                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                if(isset($reply_bot[$k]['attachment']['payload']['buttons']))
                    $reply_bot[$k]['attachment']['payload']['buttons'] = array_values($reply_bot[$k]['attachment']['payload']['buttons']);
            }

            if($template_type == 'quick_reply')
            {   

                $quick_reply_hidden_field_ids="quick_reply_sort_order_{$k}";
                $quick_reply_sort_order_array_str= $_POST[$quick_reply_hidden_field_ids];
                preg_match_all("#quick_reply_row_(.*?)_{$k}#si", $quick_reply_sort_order_array_str, $quick_reply_sort_order_match_result);
                $quick_reply_sort_order_array=$quick_reply_sort_order_match_result[1];


                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                {   

                    $quick_reply_sorted_order= $quick_reply_sort_order_array[$i-1];


                    $button_text = 'quick_reply_button_text_'.$quick_reply_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$quick_reply_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$quick_reply_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }

                // Re indexing quick reply button array 
                $reply_bot[$k]['quick_replies']=array_values($reply_bot[$k]['quick_replies']);


            }

            if($template_type == 'generic_template')
            {   

                $generic_button_hidden_field_ids="generic_button_sort_order_{$k}";
                $generic_button_sort_order_array_str= $_POST[$generic_button_hidden_field_ids];
                preg_match_all("#generic_template_row_(.*?)_{$k}#si", $generic_button_sort_order_array_str, $generic_button_sort_order_match_result);
                $generic_button_sort_order_array=$generic_button_sort_order_match_result[1];


                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                   
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['messenger_extensions'] = true;
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['webview_height_ratio'] = 'tall';
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['fallback_url'] = $generic_template_image_destination_link;

                for ($i=1; $i <= 3 ; $i++) 
                {   

                    $generic_button_sorted_order= $generic_button_sort_order_array[$i-1];


                    $button_text = 'generic_template_button_text_'.$generic_button_sorted_order.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$generic_button_sorted_order.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$generic_button_sorted_order.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$generic_button_sorted_order.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'generic_template_button_call_us_'.$generic_button_sorted_order.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_type_array = explode('_', $button_type);
                        if(isset($button_type_array[2]))
                        {
                            $button_extension = trim($button_type_array[2],'_'); 
                            array_pop($button_type_array);
                        }            
                        else $button_extension = '';
                        $button_type = implode('_', $button_type_array);

                        if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){                                
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }

                // Re indexing buttons array 
                if(isset($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']))
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'] =array_values($reply_bot[$k]['attachment']['payload']['elements'][0]['buttons']);


            }

            if($template_type == 'carousel')
            {   

                $carousel_reply_hidden_field_ids="carousel_reply_sort_order_{$k}";
                $carousel_reply_sort_order_array_str= $_POST[$carousel_reply_hidden_field_ids];
                preg_match_all("#carousel_div_(.*?)_{$k}#si", $carousel_reply_sort_order_array_str, $carousel_reply_sort_order_match_result);
                $carousel_reply_sort_order_array=$carousel_reply_sort_order_match_result[1];


                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';

                for ($j=1; $j <=10 ; $j++) 
                {   

                    $carousel_reply_sorted_order= $carousel_reply_sort_order_array[$j-1];

                    $carousel_button_hidden_field_ids="carousel_button_sort_order_{$carousel_reply_sorted_order}_{$k}";
                    $carousel_button_sort_order_array_str= $_POST[$carousel_button_hidden_field_ids];
                    preg_match_all("#carousel_row_{$carousel_reply_sorted_order}_(.*?)_{$k}#si", $carousel_button_sort_order_array_str, $carousel_button_sort_order_match_result);

                    $carousel_button_sort_order_array=$carousel_button_sort_order_match_result[1];




                    $carousel_image = 'carousel_image_'.$carousel_reply_sorted_order.'_'.$k;
                    $carousel_title = 'carousel_title_'.$carousel_reply_sorted_order.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$carousel_reply_sorted_order.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$carousel_reply_sorted_order.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }

                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['messenger_extensions'] = true;
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['webview_height_ratio'] = 'tall';
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['fallback_url'] = $$carousel_image_destination_link;

                    for ($i=1; $i <= 3 ; $i++) 
                    {   

                        $carousel_button_sorted_order= $carousel_button_sort_order_array[$i-1];

                        $button_text = 'carousel_button_text_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                          $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                        $button_call_us = 'carousel_button_call_us_'.$carousel_reply_sorted_order."_".$carousel_button_sorted_order.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $button_postback_id;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = $bot_name;
                                $postback_insert_data[] = $single_postback_insert_data; 
                            }
                        }
                        if(strpos($button_type,'web_url') !== FALSE)
                        {
                            $button_type_array = explode('_', $button_type);
                            if(isset($button_type_array[2]))
                            {
                                $button_extension = trim($button_type_array[2],'_'); 
                                array_pop($button_type_array);
                            }            
                            else $button_extension = '';
                            $button_type = implode('_', $button_type_array);

                            if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                if($button_extension != '' && $button_extension == 'birthday'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_birthdate');
                                }
                                else if($button_extension != '' && $button_extension == 'email'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_email');
                                }
                                else if($button_extension != '' && $button_extension == 'phone'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_phone');
                                }
                                else if($button_extension != '' && $button_extension == 'location'){
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'full';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_location');
                                }
                                else
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                                {
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['messenger_extensions'] = 'true';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = $button_extension;
                                    // $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['fallback_url'] = $button_web_url;
                                }

                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }

                    // Added by Konok to reindexing from zero 
                    if(isset($reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']))
                    	$reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']=array_values($reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons']);
                }

                // Added by Konok to reindexing from zero 
                if(isset($reply_bot[$k]['attachment']['payload']['elements']))
                    $reply_bot[$k]['attachment']['payload']['elements'] = array_values($reply_bot[$k]['attachment']['payload']['elements']);


            }

            if(isset($reply_bot[$k]))
            {     
                $typing_on_settings = 'typing_on_enable_'.$k;
                if(!isset($$typing_on_settings)) $typing_on_settings = 'off';
                else $typing_on_settings = $$typing_on_settings;

                $delay_in_reply = 'delay_in_reply_'.$k;
                if(!isset($$delay_in_reply)) $delay_in_reply = 0;
                else $delay_in_reply = $$delay_in_reply;

                $reply_bot[$k]['typing_on_settings'] = $typing_on_settings;
                $reply_bot[$k]['delay_in_reply'] = $delay_in_reply;

                $bot_message[$k]['recipient'] = array('id'=>'replace_id');
                $bot_message[$k]['message'] = $reply_bot[$k];
            }

        }


        $reply_bot_filtered = array();
        $m=0;
        foreach ($bot_message as $value) {
            $m++;
            $reply_bot_filtered[$m] = $value;
        }

        // domain white list section start
        $this->load->library("fb_rx_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            
            $domain_only_whitelist= get_domain_only_with_http($value);
            if(in_array($domain_only_whitelist, $white_listed_domain_array)) continue; 

            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$domain_only_whitelist);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $facebook_rx_fb_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $domain_only_whitelist;
                $temp_data['created_at'] = date("Y-m-d H:i:s");
                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data))
            $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end

        $insert_data['template_jsoncode'] = json_encode($reply_bot_filtered,true);
        $insert_data['user_id'] = $this->user_id;
        $insert_data['template_id'] = 0;
        $this->basic->update_data('messenger_bot_postback',array("id" => $id),$insert_data);

        $existing_data = $this->basic->get_data('messenger_bot_postback',array('where'=>array('id'=>$id)));
        $this->basic->update_data('messenger_bot',array('id'=>$existing_data[0]['messenger_bot_table_id']),array('message'=>$existing_data[0]['template_jsoncode'],"broadcaster_labels"=>$new_label_ids,'bot_name'=>$existing_data[0]['template_name'],'drip_campaign_id'=>$existing_data[0]['drip_campaign_id']));

        $messenger_bot_table_id = $existing_data[0]['messenger_bot_table_id'];  

        $user_all_postback = array();
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_table_id)));
        foreach($postback_id_list as $value)
        {
            array_push($user_all_postback, strtoupper($value['postback_id']));
        }


        // $this->basic->delete_data('messenger_bot_postback',array('page_id'=>$page_table_id,'template_id'=>$id,'use_status'=>'0','is_template'=>'0','inherit_from_template'=>'1'));
        

        $postback_insert_data_modified = array();
        $m=0;
        $unique_postbacks = array();
        foreach($postback_insert_data as $value)
        {
            if(in_array(strtoupper($value['postback_id']), $unique_postbacks)) continue;
            if(in_array(strtoupper($value['postback_id']), $user_all_postback)) continue;
            if($value['postback_id'] == 'UNSUBSCRIBE_QUICK_BOXER' || $value['postback_id'] == 'RESUBSCRIBE_QUICK_BOXER' || $value['postback_id'] == 'YES_START_CHAT_WITH_HUMAN' || $value['postback_id'] == 'YES_START_CHAT_WITH_BOT') continue;
            $postback_insert_data_modified[$m]['user_id'] = $value['user_id'];
            $postback_insert_data_modified[$m]['postback_id'] = $value['postback_id'];
            $postback_insert_data_modified[$m]['page_id'] = $value['page_id'];
            $postback_insert_data_modified[$m]['bot_name'] = $value['bot_name'];
            $postback_insert_data_modified[$m]['messenger_bot_table_id'] = 0;
            $postback_insert_data_modified[$m]['inherit_from_template'] = '1';
            $postback_insert_data_modified[$m]['template_id'] = $id;
            array_push($unique_postbacks, $value['postback_id']);
            $m++;
        }

        
        if(!empty($postback_insert_data_modified))
        $this->db->insert_batch('messenger_bot_postback',$postback_insert_data_modified);

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE)
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Template update was unsuccessful. Database error occured during update.")));
            exit();
        }
        else
        {
            $this->session->set_flashdata('bot_update_success',1);
            echo json_encode(array("status" => "1", "message" =>$this->lang->line("Template been updated successfully.")));        
        }


    }

    public function clone_template($postback_table_id=0,$iframe='0',$is_default='0',$media_type='fb')
    {
        if($postback_table_id == 0) exit();
        $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
        $this->is_sms_email_drip_campaigner_exist=$this->sms_email_drip_campaigner_exist();
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        $table_name = "messenger_bot_postback";
        $where_bot['where'] = array('id' => $postback_table_id, 'status' => '1', 'user_id'=>$this->user_id);
        $bot_info = $this->basic->get_data($table_name, $where_bot);
        if(empty($bot_info)) redirect('messenger_bot/template_manager', 'location');

        $data['body'] = 'messenger_tools/edit_template';
        $data['page_title'] = $this->lang->line('Clone Facebook Post-back template');
        if($media_type == "ig") {
            $data['page_title'] = $this->lang->line('Edit Instagram Post-back Template');
        }
        $data['media_type'] = $media_type;
        $data["templates"]=$this->basic->get_enum_values("messenger_bot","template_type");

        $template_types=$this->basic->get_enum_values("messenger_bot","template_type");
        if(!$this->addon_exist("custom_field_manager"))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        } 
        if($this->session->userdata('user_type') != 'Admin' && !in_array(292,$this->module_access))
        {
            $key = array_search('User Input Flow',$template_types);
            If($key!==false) 
            unset($template_types[$key]);
        }

        if($media_type == 'ig')
        {
            $need_to_remove = [
                                'audio',
                                'video',
                                'file',
                                'text with buttons',
                                'media',
                                'One Time Notification'
                            ];
            foreach ($need_to_remove as $value) {
                if (($key = array_search($value, $template_types)) !== false) {
                    unset($template_types[$key]);
                }
            }
        }
        $data["templates"]=$template_types;

        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);
        $page_list = array();
        foreach($page_info as $value)
        {
            $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
        }

        $data['page_list'] = $page_list;
        $data['bot_info'] = isset($bot_info[0]) ? $bot_info[0] : array();

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"],"media_type"=>$media_type),'where_not_in'=>array('postback_id'=>array('UNSUBSCRIBE_QUICK_BOXER','RESUBSCRIBE_QUICK_BOXER','YES_START_CHAT_WITH_HUMAN','YES_START_CHAT_WITH_BOT'))));

        $current_postbacks = array();
        foreach ($postback_id_list as $value) {
            if($value['template_id'] == $postback_table_id || $value['id'] == $postback_table_id)
            $current_postbacks[] = $value['postback_id'];
        }
        $data['postback_ids'] = $postback_id_list;
        $data['current_postbacks'] = $current_postbacks;

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"],"unsubscribe"=>"0","invisible"=>"0","social_media"=>$media_type);
        $data['info_type'] = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        if($this->is_broadcaster_exist)
        {          

            $table_type = 'messenger_bot_drip_campaign';
            $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"],"media_type"=>$media_type);
            $data['dripcampaign_list'] = $this->basic->get_data($table_type,$where_type,$select='');
        }
        else 
        {
            $data['dripcampaign_list']=array();
        }


        if($this->is_broadcaster_exist)
            $data['has_broadcaster_addon'] = 1;
        else
            $data['has_broadcaster_addon'] = 0;

        
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"],'template_for'=>'reply_message',"media_type"=>$media_type)),array('postback_id','bot_name'));
        $postback_dropdown = array();
        if(!empty($postback_id_list))
        {
            foreach($postback_id_list as $value)
                $postback_dropdown[$value['postback_id']] = $value['postback_id'];
        }
        $data['postback_dropdown'] = $postback_dropdown;

        $otn_postbacks = [];
        $otn_postback_info = $this->basic->get_data('otn_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"])),array('id','otn_postback_id','template_name'));
        foreach($otn_postback_info as $value)
        {
            $otn_postbacks[$value['id']] = $value['otn_postback_id']." (".$value['template_name'].")";
        }
        $data['otn_postback_list'] = $otn_postbacks;

        if($this->addon_exist("custom_field_manager"))
            $flow_campaign_info = $this->basic->get_data('user_input_flow_campaign',['where'=>['user_id'=>$this->user_id,'page_table_id'=>$bot_info[0]["page_id"],"media_type"=>$media_type]]);
        else
           $flow_campaign_info = []; 
        $data['flow_campaigns'] = $flow_campaign_info;
        
        $data['iframe'] = $iframe;
        $data['is_default'] = $is_default;
        $data['action_type'] = 'clone';

        $data['iframe']=$iframe;
        $this->_viewcontroller($data);  
    }

    public function ajax_delete_template_info()
    {
        $id = $this->input->post('table_id',true);
        $postback_info = $this->basic->get_data('messenger_bot_postback',array('where'=>array('id'=>$id,'user_id'=>$this->user_id)));
        if(empty($postback_info))
        {
            echo "no_match";
            exit;
        }
        $postback_id = $postback_info[0]['postback_id'];
        $page_table_id = $postback_info[0]['page_id'];
        $search_content = '%"payload":"'.$postback_id.'"%';
        $bot_info = $this->basic->get_data('messenger_bot',array('where'=>array('message like'=>$search_content,'page_id'=>$page_table_id)));
        
        if(!empty($bot_info))
        {
            $response = "<div class='text-center alert alert-danger'>".$this->lang->line('You can not delete this template because it is being used in the following bots. First make sure that these templates are free to delete. You can do this by editing or deleting the following bots.')."</div><br>";
            $response.= '
                 <script>
                     $(document).ready(function() {
                         $("#need_to_delete_bots").DataTable();
                     }); 
                  </script>
                  <style>
                     .dataTables_filter
                      {
                         float : right;
                      }
                  </style>
                 <div class="table-responsive">
                 <table id="need_to_delete_bots" class="table table-bordered">
                     <thead>
                         <tr>
                             <th>'.$this->lang->line("SN.").'</th>
                             <th>'.$this->lang->line("Bot Name").'</th>
                             <th>'.$this->lang->line("Kyeword").'</th>
                             <th>'.$this->lang->line("Keyword Type").'</th>
                             <th class="text-center">'.$this->lang->line("Actions").'</th>
                         </tr>
                     </thead>
                     <tbody>';
            $sn = 0;
            $value = array();
            foreach($bot_info as $value)
            {
                $sn++;
                $bot_id = $value['id'];
                $url = '#';
                if($value['is_template'] == '1')
                {
                    $child_postback_info = $this->basic->get_data('messenger_bot_postback',array('where'=>array('messenger_bot_table_id'=>$value['id'])));

                    $postback_table_id = 0;
                    if(isset($child_postback_info[0]['id'])) $postback_table_id = $child_postback_info[0]['id'];
                    $url = base_url('messenger_bot/edit_template/').$postback_table_id;
                }
                else
                    $url = base_url('messenger_bot/edit_bot/').$bot_id.'/postback';
                $response .= '<tr>
                            <td>'.$sn.'</td>
                            <td>'.$value['bot_name'].'</td>
                            <td>'.$value['keywords'].'</td>
                            <td>'.$value['keyword_type'].'</td>
                            <td class="text-center"><a class="btn btn-outline-warning" title="'.$this->lang->line("edit").'" target="_BLANK" href="'.$url.'"><i class="fa fa-edit"></i></a></td>
                        </tr>';
            }
            $response .= '</tbody>
                 </table></div>';
            echo $response;
        }
        else
        {

            $this->basic->delete_data('messenger_bot_postback',array('id'=>$id,'page_id'=>$page_table_id));
            $this->basic->delete_data('messenger_bot_postback',array('template_id'=>$id,'is_template'=>'0','page_id'=>$page_table_id));
            $this->basic->delete_data('messenger_bot',array('postback_id'=>$postback_id,'page_id'=>$page_table_id));
            echo "success";
        }
    }
    
    public function upload_image_only()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $folder_path = FCPATH."upload/image";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        $output_dir = FCPATH."upload/image/".$this->user_id;
        if (!file_exists($output_dir)) {
            mkdir($output_dir, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="image_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            $allow=".jpg,.jpeg,.png,.gif";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }

            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }

    public function delete_uploaded_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/image/".$this->user_id."/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }

    public function upload_live_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $output_dir = FCPATH."upload/video";
        $folder_path = FCPATH."upload/video";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="video_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            $allow=".mov,.mpeg4,.mp4,.avi,.wmv,.mpegps,.flv,.3gpp,.webm";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("go_live_video_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("go_live_video_filename", $filename); 
            echo json_encode($filename);
        }
    }

    public function delete_uploaded_live_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/video/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }
    
    // audio/pdf/doc file upload section
    public function upload_audio_file()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $output_dir = FCPATH."upload/audio";
        $folder_path = FCPATH."upload/audio";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="audio_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            $allow=".amr,.mp3,.wav,.WAV,.MP3,.AMR";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("go_live_video_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("go_live_video_filename", $filename); 
            echo json_encode($filename);
        }
    }

    public function delete_audio_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/audio/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }

    public function upload_general_file()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $output_dir = FCPATH."upload/file";
        $folder_path = FCPATH."upload/file";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="file_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            $allow=".doc,.docx,.pdf,.txt,.ppt,.pptx,.xls,.xlsx";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("go_live_video_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("go_live_video_filename", $filename); 
            echo json_encode($filename);
        }
    }

    public function delete_general_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/file/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }
    //===========================ENABLE DISABLE STARTED Button====================


    public function get_started_welcome_message()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access)) exit();
        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo json_encode(array('status'=>'0','message'=>'This function is disabled from admin account in this demo!!'));
                exit();
            }
        }

        $page_id=$this->input->post('page_info_table_id_icebreaker');
        $welcome_message=$this->input->post('welcome_message');
        $started_button_enabled=$this->input->post('started_button_enabled');

        $ice_breaker_status=$this->input->post('ice_breaker_status');
        $questions=$this->input->post('questions');
        $question_replies=$this->input->post('question_replies');
        $ice_breaker_for=$this->input->post('ice_breaker_for');

        $questionaries = [];
        $ice_breaker_array = [];
        $ice_breaker_arry_question = [];

        $given_question_answer = 0;
        if($questions != '' && $question_replies != '')
        {
            for($i=0;$i<count($questions);$i++)
            {
                if($questions[$i] != '' && $question_replies[$i] != '')
                {
                    $questionaries[$i]['questions'] = $questions[$i];
                    $ice_breaker_arry_question[$i]['question'] = $questions[$i];
                    $questionaries[$i]['question_replies'] = $question_replies[$i];
                    $ice_breaker_arry_question[$i]['payload'] = $question_replies[$i];

                    $ice_breaker_array["ice_breakers"][]=$ice_breaker_arry_question[$i];
                    $given_question_answer = 1;
                }
            }
        }

        if($ice_breaker_for == 'ig')
            $update_data = array(
                'ig_ice_breaker_status' => $ice_breaker_status
            );
        else
            $update_data = array(
                'started_button_enabled' => $started_button_enabled,
                'ice_breaker_status' => $ice_breaker_status
            );

        if($ice_breaker_status == '0')
        {
            if($ice_breaker_for == 'ig')
                $update_data['ig_ice_breaker_questions'] = json_encode($questionaries);
            else
                $update_data['ice_breaker_questions'] = json_encode($questionaries);

        }
        else
        {
            if($ice_breaker_for == 'ig')
                $update_data['ig_ice_breaker_questions'] = json_encode($questionaries);
            else
                $update_data['ice_breaker_questions'] = json_encode($questionaries);

            if($given_question_answer == 0)
            {
                echo json_encode(array('status'=>'0','message'=>'To enable Ice Breakers you have to provide at least one question and answer.'));
                exit();
            }
        }

        if($ice_breaker_for != 'ig')
        {
            if($started_button_enabled == '0')
                $update_data['welcome_message'] = '';
            else
                $update_data['welcome_message'] = $welcome_message;
        }


        $this->load->library("fb_rx_login");
        $final_result = [];

        $page_data=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $page_access_token=isset($page_data[0]["page_access_token"]) ? $page_data[0]["page_access_token"] : "";
        
        if($ice_breaker_for != 'ig')
        {
            if($started_button_enabled=='1')
            {
                $response=$this->fb_rx_login->add_get_started_button($page_access_token);
                if(!isset($response['error']))
                {
                    $response2=$this->fb_rx_login->set_welcome_message($page_access_token,$welcome_message);
                    if(!isset($response2['error']))
                    {
                        $final_result['get_started_status'] = '1';
                        $final_result['get_started_message'] = $this->lang->line("Get started button has been enabled successfully.");
                    }
                    else
                    {
                        $error_msg2=isset($response2['error']['message'])?$response2['error']['message']:$this->lang->line("something went wrong, please try again.");
                        $final_result['get_started_status'] = '0';
                        $final_result['get_started_message'] = $error_msg2;
                    }
                }
                else
                {
                    $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                    $final_result['get_started_status'] = '0';
                    $final_result['get_started_message'] = $error_msg;
                }
            }
            else
            {
                $response=$this->fb_rx_login->delete_get_started_button($page_access_token);
                if(!isset($response['error']))
                {
                    $response2=$this->fb_rx_login->unset_welcome_message($page_access_token);
                    if(!isset($response2['error']))
                    {
                       $final_result['get_started_status'] = '1';
                       $final_result['get_started_message'] = $this->lang->line("Get started button has been disabled successfully.");
                    }
                    else
                    {
                        $error_msg2=isset($response2['error']['message'])?$response2['error']['message']:$this->lang->line("something went wrong, please try again.");
                        $final_result['get_started_status'] = '0';
                        $final_result['get_started_message'] = $error_msg2;
                    }
                }
                else
                {
                    $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                    $final_result['get_started_status'] = '0';
                    $final_result['get_started_message'] = $error_msg;
                }

            }
        }

        if($ice_breaker_status == '1')
        {
            $ice_breaker_json = json_encode($ice_breaker_array);
            $response=$this->fb_rx_login->add_ice_breakers($page_access_token,$ice_breaker_json,$ice_breaker_for);
            if(!isset($response['error']))
            {
                $final_result['ice_breaker_status'] = '1';
                $final_result['ice_breaker_message'] = $this->lang->line("Ice breakers has been enabled successfully.");
            }
            else
            {
                $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                $final_result['ice_breaker_status'] = '0';
                $final_result['ice_breaker_message'] = "Ice Breakers: ".$error_msg;
            }

        }
        else
        {
            $response=$this->fb_rx_login->delete_ice_breakers($page_access_token,$ice_breaker_for);
            if(!isset($response['error']))
            {
                $final_result['ice_breaker_status'] = '1';
                $final_result['ice_breaker_message'] = $this->lang->line("Ice breakers has been disabled successfully.");
            }
            else
            {
                $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                $final_result['ice_breaker_status'] = '0';
                $final_result['ice_breaker_message'] = "Ice Breakers: ".$error_msg;
            }
        }

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id,"user_id"=>$this->user_id),$update_data);

        echo json_encode($final_result);


    }

    public function export_bot()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if(!$_POST) exit();
        
        $page_id=$this->input->post('export_id');
        $template_name=strip_tags($this->input->post('template_name'));
        $template_description=strip_tags($this->input->post('template_description'));
        $template_preview_image=$this->input->post('template_preview_image');
        $template_access=$this->input->post('template_access');
        $allowed_package_ids=$this->input->post('allowed_package_ids');
        $media_type=$this->input->post('export_media_type');

        $template_preview_image=str_replace(base_url('upload/image/'.$this->user_id.'/'), '', $template_preview_image);

        if(!is_array($allowed_package_ids) || $template_access=='private')  $allowed_package_ids=array();

        $get_bot_settings=$this->get_bot_settings($page_id,$media_type);
        $savedata=json_encode($get_bot_settings);

        if($this->session->userdata('user_type') != 'Admin') $template_access='private';

        $this->basic->insert_data("messenger_bot_saved_templates",array("template_name"=>$template_name,"savedata"=>$savedata,"saved_at"=>date("Y-m-d H:i:s"),"user_id"=>$this->user_id,"template_access"=>$template_access,"description"=>$template_description,"preview_image"=>$template_preview_image,"allowed_package_ids"=>implode(',', $allowed_package_ids),"media_type"=>$media_type));
        $insert_id=$this->db->insert_id();

        $message="<div class='alert alert-info text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Bot template has been saved to database successfully.")."</div><br><a class='btn-block btn btn-outline-info'  href='".base_url('messenger_bot/saved_templates/').$media_type."'><i class='fa fa-save'></i> ".$this->lang->line("My Saved Templates")."</a><a target='_BLANK' class='btn-block btn btn-outline-primary' href='".base_url('messenger_bot/export_bot_download/').$insert_id."'><i class='fa fa-file-download'></i> ".$this->lang->line("Download Template")."</a>";
        echo json_encode(array('status'=>'0','message'=>$message));
    }

    public function export_bot_download($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if($id==0) exit();

        $save_data=$this->basic->get_data("messenger_bot_saved_templates",array("where"=>array("id"=>$id)));
        if(!isset($save_data[0])) exit();

        $template_name=isset($save_data[0]['template_name'])?$save_data[0]['template_name']:"";
        $savedata=isset($save_data[0]['savedata'])?$save_data[0]['savedata']:"";

        $template_name = preg_replace("/[^a-z0-9]+/i", "", $template_name);
        $filename=$template_name.".json";
        $f = fopen('php://memory', 'w'); 
        fwrite($f, $savedata);
        fseek($f, 0);
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        fpassthru($f);  
    }

    public function upload_json_template()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $output_dir = FCPATH."upload";
        if (!file_exists($output_dir)) {
            mkdir($output_dir, 0755, true);
        }
        if (isset($_FILES["myfile"])) 
        {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="json_template_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;


            $allow=".json";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            echo json_encode($filename);
        }
    }

    public function upload_json_template_delete() // deletes the uploaded video to upload another one
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }


    public function import_bot_check()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if(!$_POST) exit();

        $media_type=$this->input->post('media_type',true);
        $template_id=$this->input->post('template_id',true);
        $page_id=$this->input->post('import_id',true);
        $json_upload_input=$this->input->post('json_upload_input');

        if($template_id=="" && $json_upload_input=="")
        {
            echo json_encode(array('json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'0','message'=>$this->lang->line("No template found also no json found.")));
            exit();
        }

        if($template_id!="" && $json_upload_input!="")
        {
            echo json_encode(array('json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'0','message'=>$this->lang->line("You can not choose both template and upload file at the same time.")));
            exit();
        }

        if($json_upload_input!="")
        {
            $path=FCPATH.'upload/'.$json_upload_input;
            $array='';
            if(file_exists($path))
            {
                $json=file_get_contents($path);
                $array=json_decode($json,true);
            }
            if(!is_array($array))
            {
                 echo json_encode(array('json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'0','message'=>$this->lang->line("Uploaded json is not a valid template json.")));
                 exit();
            }
        }

        if($this->basic->is_exist("messenger_bot",array("page_id"=>$page_id)) || $this->basic->is_exist("messenger_bot_postback",array("page_id"=>$page_id)) || $this->basic->is_exist("messenger_bot_persistent_menu",array("page_id"=>$page_id)) )
        {
            echo json_encode(array('media_type'=>$media_type,'json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'1','message'=>$this->lang->line("Template has not been imported because there are existing bot settings or persistent menu settings found. Importing this template will delete all your previous bot settings, persistent menu settings as well as get started welcome screen message etc. Do you want to delete all your previous settings for this page and import this template?")));
            exit();
        }
        
        echo json_encode(array('media_type'=>$media_type,'json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'1','message'=>$this->lang->line("System has finished data checking and ready to import new template settings. Are you sure that you want to import this template?")));     
     }

    public function import_bot()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if(!$_POST) exit();

        $media_type_form=$this->input->post('media_type',true);
        $template_id=$this->input->post('template_id',true);
        $page_id=$this->input->post('page_id',true);
        $json_upload_input=$this->input->post('json_upload_input');


        $pagedata=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));     
        if(!isset($pagedata[0]))
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Page not found")));
            exit();
        }

        $jsondata='';
        if($template_id!="")
        {
            $get_bot_settings=$this->basic->get_data("messenger_bot_saved_templates",array("where"=>array("id"=>$template_id)));        
            if(!isset($get_bot_settings[0]))
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("Template not found")));
                exit();
            }
            $jsondata=$get_bot_settings[0]['savedata'];
        }
        else
        {
            $path=FCPATH.'upload/'.$json_upload_input;
            if(file_exists($path))
            {
                $jsondata=file_get_contents($path);
                @unlink($path); 
            }          
        }

        $savedata=json_decode($jsondata,true);     
        $media_type=$savedata['media_type'] ?? "fb";

        if($media_type_form!=$media_type){
             echo json_encode(array('status'=>'0','message'=>$this->lang->line("Unsupported Template for ").$media_type));
            exit();
        }

        $this->db->db_debug = FALSE; //disable debugging for queries
        $this->db->trans_start();

        // deleting current settings so that we can import new settings
        $this->basic->delete_data("messenger_bot",array("page_id"=>$page_id,"user_id"=>$this->user_id,'media_type'=>$media_type));
        $this->basic->delete_data("messenger_bot_postback",array("page_id"=>$page_id,"user_id"=>$this->user_id,'media_type'=>$media_type));
        if($media_type=="fb")
            $this->basic->delete_data("messenger_bot_persistent_menu",array("page_id"=>$page_id));
        // -------------------------------------------------------------
        

       


        if($this->db->table_exists('user_input_flow_campaign')) {

            // Delete all current user input campaign from this page 

            $delete_query="DELETE user_input_flow_campaign,user_input_flow_questions from user_input_flow_campaign left JOIN user_input_flow_questions on user_input_flow_campaign.id=user_input_flow_questions.flow_campaign_id where user_input_flow_campaign.user_id={$this->user_id} and page_table_id={$page_id} and user_input_flow_campaign.media_type='{$media_type}'";

            $this->db->query($delete_query);

            // Inserting user input & custom fields. 

            $custom_fields=isset($savedata['custom_fields'])?$savedata['custom_fields']:array();
            $input_flow=isset($savedata['input_flow'])?$savedata['input_flow']:array();

            // Insert Custom Fields, Custom fields are user based, so if it's from same user, then no need to insert. 

            $custom_field_id_change_log=array();
            $custom_field_insert_info=array("user_id"=>$this->user_id,"create_time"=>date("Y-m-d H:i:s"));


            foreach($custom_fields as $c_field){

                // If same user
                if($c_field['user_id']==$this->user_id){

                    $custom_field_id_change_log[$c_field['id']] = $c_field['id'];
                }

                else{ // If different user

                    foreach($c_field as $key=>$value){
                        if($key=="id" || $key=="user_id" || $key=="create_time") continue;
                        $custom_field_insert_info[$key]=$value;
                    }

                    $custom_field_insert_info['media_type']=$media_type;
                    // Insert into database & keep track of new id of the insertion 
                    $this->basic->insert_data("user_input_custom_fields",$custom_field_insert_info);
                    $custom_field_insert_id=$this->db->insert_id();  
                    $custom_field_id_change_log[$c_field['id']] = $custom_field_insert_id;
                }
            }


            // Insert user input flow campaign -------------------------------------

            $flow_campaign_insert_log=array();
            $flow_campaing_in_bot_search=array();
            $flow_campaing_in_bot_replace=array();

            $input_flow_campaign_insert_info=array("user_id"=>$this->user_id,"page_table_id"=>$page_id);

            foreach($input_flow as $flow_info){

                $input_flow_campaign_insert_info['flow_name'] = $flow_info['flow_name'];
                $input_flow_campaign_insert_info['postback_id'] = $flow_info['postback_id'];
                $input_flow_campaign_insert_info['media_type'] = $media_type;

                $this->basic->insert_data("user_input_flow_campaign",$input_flow_campaign_insert_info);
                $user_input_flow_insert_id=$this->db->insert_id();  

                $flow_campaign_insert_log[$flow_info['id']] = $user_input_flow_insert_id; 

                $flow_campaing_in_bot_search[]='{\"template_type\":\"User_Input_Flow\",\"flow_campaign_id\":\"'.$flow_info['id'].'\"';
                $flow_campaing_in_bot_replace[]='{\"template_type\":\"User_Input_Flow\",\"flow_campaign_id\":\"'.$user_input_flow_insert_id.'\"';

                //This double replacing condition as sometime user input campaign id as integer inside json. 
                
                $flow_campaing_in_bot_search[]='\"template_type\":\"User_Input_Flow\",\"flow_campaign_id\":'.$flow_info['id'];
                $flow_campaing_in_bot_replace[]='\"template_type\":\"User_Input_Flow\",\"flow_campaign_id\":'.$user_input_flow_insert_id;


                 // visual flow builder input flow campaign id change. 

                $flow_campaing_in_visual_flow_search[]= '{\"userInputFlowIdValue\":\"'.$flow_info['id'].'\"';
                $flow_campaing_in_visual_flow_replace[]= '{\"userInputFlowIdValue\":\"'.$user_input_flow_insert_id.'\"';

                //This double replacing condition as sometime user input campaign id as integer inside json. 
                
                $flow_campaing_in_visual_flow_search[]= '\"userInputFlowIdValue\":'.$flow_info['id'];
                $flow_campaing_in_visual_flow_replace[]= '\"userInputFlowIdValue\":'.$user_input_flow_insert_id;




                $questions_list = $flow_info['questions_list'];

                $querstion_insert_info=array("user_id"=>$this->user_id,"flow_campaign_id"=>$user_input_flow_insert_id);

                foreach($questions_list as $q_list){

                    foreach($q_list as $key=>$value){

                        if($key=="flow_campaign_id" || $key=="messenger_sequence_id" || $key=="email_phone_sequence_id") continue;

                        if($key=="custom_field_id" && $value>0)
                            $querstion_insert_info[$key]=$custom_field_id_change_log[$value];
                        else
                            $querstion_insert_info[$key]=$value;
                    }

                    $this->basic->insert_data("user_input_flow_questions",$querstion_insert_info);

                }
            }

        // Replace main json with new flow campaign id 
        $jsondata=str_replace($flow_campaing_in_bot_search, $flow_campaing_in_bot_replace, $jsondata);

        }
        
 
        //----------------------------------------------------------------- Added by Konok 22.12.2020


        // Flow Builder Campaign info insert  Added by Konok 18.03.2021
        if($this->db->table_exists('visual_flow_builder_campaign')) {

            // replace user input flow id inside visual flow builder as selection 
            if(isset($flow_campaing_in_visual_flow_search)){
                $jsondata=str_replace($flow_campaing_in_visual_flow_search, $flow_campaing_in_visual_flow_replace, $jsondata);
            }
            $savedata1=json_decode($jsondata,true);

        	$flow_builder_campaing_in_bot_search=array();
            $flow_builder_campaing_in_bot_replace=array();
        	$visual_flow_campaign_insert_log=array();

            // Delete previous flow builder if there any 
            $this->basic->delete_data("visual_flow_builder_campaign",array("user_id"=>$this->user_id,"page_id"=>$page_id,"media_type"=>$media_type));

            $flow_builder_campaign_information= $savedata1['flow_builder_campaign_information'] ?? array();
            foreach($flow_builder_campaign_information as $flow_builder_list){
                $flow_builder_campaign_insert=array("user_id"=>$this->user_id,"page_id"=>$page_id);
                $flow_builder_campaign_insert['reference_name'] = $flow_builder_list['reference_name'];
                $flow_builder_campaign_insert['json_data'] = $flow_builder_list['json_data'];
                $flow_builder_campaign_insert['media_type'] = $media_type;
                $this->basic->insert_data("visual_flow_builder_campaign",$flow_builder_campaign_insert);
                $flow_builder_campaign_insert_id=$this->db->insert_id();
                //Keep visaul flow campaign new & old id in the array 
                $visual_flow_campaign_insert_log[$flow_builder_list['id']]= $flow_builder_campaign_insert_id;

                $flow_builder_campaing_in_bot_search[]='"visual_flow_campaign_id":"'.$flow_builder_list['id'].'"';
                $flow_builder_campaing_in_bot_replace[]='"visual_flow_campaign_id":"'.$flow_builder_campaign_insert_id.'"';
            }

            $jsondata=str_replace($flow_builder_campaing_in_bot_search, $flow_builder_campaing_in_bot_replace, $jsondata);
            }








        $savedata=json_decode($jsondata,true);  
        $fb_page_id=isset($pagedata[0]['page_id'])?$pagedata[0]['page_id']:"";
        $page_access_token=isset($pagedata[0]['page_access_token'])?$pagedata[0]['page_access_token']:"";

        $bot_settings=isset($savedata['bot_settings'])?$savedata['bot_settings']:array();
        $empty_postback_settings=isset($savedata['empty_postback_settings'])?$savedata['empty_postback_settings']:array();
        $persistent_menu_settings=isset($savedata['persistent_menu_settings'])?$savedata['persistent_menu_settings']:array();
        $bot_general_info=isset($savedata['bot_general_info'])?$savedata['bot_general_info']:array();

        // inserting messenger_bot + messenger_bot_postback data        
        foreach ($bot_settings as $key => $value)
        {
            $bot_info=isset($value['message_bot'])?$value['message_bot']:array();

            $messenger_bot_row=array
            (
                "user_id"=>$this->user_id,
                "page_id"=>$page_id,
                "fb_page_id"=>$fb_page_id
            );
            foreach ($bot_info as $key2 => $value2) 
            {
              if($key2=="postback_template_info") continue;
              $messenger_bot_row[$key2]=$value2;
            }           

             $messenger_bot_row["media_type"]=$media_type;
            $this->basic->insert_data("messenger_bot",$messenger_bot_row);
            $messenger_bot_insert_id=$this->db->insert_id();      

            $postback_template_info=isset($value['message_bot']['postback_template_info'])?$value['message_bot']['postback_template_info']:array(); // getting postback data
            foreach ($postback_template_info as $key2 => $value2) 
            {               
                $messenger_bot_postback_row=array
                (
                    "user_id"=>$this->user_id,
                    "page_id"=>$page_id
                );

                $old_postback_table_id= $value2['id'] ?? "";

                foreach ($value2 as $key3 => $value3)
                {
                   if($key3=="postback_child" || $key3=="id") continue;
                   $messenger_bot_postback_row[$key3]=$value3;
                }   
                $messenger_bot_postback_row['messenger_bot_table_id']=$messenger_bot_insert_id;
                $messenger_bot_postback_row['template_id']=0;
                $messenger_bot_postback_row['media_type']=$media_type;

                $this->basic->insert_data("messenger_bot_postback",$messenger_bot_postback_row);
                $messenger_bot_postback_insert_id=$this->db->insert_id();  

                // Store old postback id & new postback as array . Old id is index & new id is value. 
                $new_postback_id_information[$old_postback_table_id] = $messenger_bot_postback_insert_id;

                $postback_template_info2=isset($value2['postback_child'])?$value2['postback_child']:array(); // getting postback data level2

                
                foreach ($postback_template_info2 as $key3 => $value3) 
                {
                   $messenger_bot_postback_row2=array
                   (
                        "user_id"=>$this->user_id,
                        "page_id"=>$page_id
                   );
                   foreach ($value3 as $key4 => $value4) 
                   {
                     $messenger_bot_postback_row2[$key4]=$value4;
                   }
                   $messenger_bot_postback_row2['messenger_bot_table_id']=0;
                   $messenger_bot_postback_row2['template_id']=$messenger_bot_postback_insert_id;
                   $messenger_bot_postback_row2['media_type']=$media_type;

                   $this->basic->insert_data("messenger_bot_postback",$messenger_bot_postback_row2);
                }
                
            }              
        }
        // ----------------------------------------------------------------


        // inserting empty postback
        foreach ($empty_postback_settings as $key => $value) 
        {           
            $messenger_bot_postback_empty_row=array
            (
                "user_id"=>$this->user_id,
                "page_id"=>$page_id
            );
            foreach ($value as $key2 => $value2)
            {
               $messenger_bot_postback_empty_row[$key2]=$value2;
            }   
            $messenger_bot_postback_empty_row['template_id']=0;
            $messenger_bot_postback_empty_row['media_type']=$media_type;
            $this->basic->insert_data("messenger_bot_postback",$messenger_bot_postback_empty_row);            
        }
        //-----------------------------------------------------------------


        // Inserting Sequence Campaign Information

         if($this->db->table_exists('messenger_bot_drip_campaign')) {

            //Delete existing sequence campaign & import all new. 

            $this->basic->delete_data("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,'media_type'=>$media_type));

            $sequence_campaign_information= $savedata['sequence_campaign_information'] ?? array();

            foreach($sequence_campaign_information as $sequence_campaign_list){

                $daily_message_content=array();
                $hourly_message_content=array();

                $old_sequence_id=$sequence_campaign_list['id'] ?? "";

                // change the postback id of daily content json 
                $message_content=$sequence_campaign_list['message_content'] ?? "[]";
                $message_content= json_decode($message_content); 

                foreach($message_content as $dkey=>$dvalue){
                    $daily_message_content[$dkey]= $new_postback_id_information[$dvalue];
                }

                $sequence_campaign_list['message_content'] = json_encode($daily_message_content);

                // change the postback id of hourly content json 
                $message_content_hourly=$sequence_campaign_list['message_content_hourly'] ?? "[]";
                $message_content_hourly= json_decode($message_content_hourly);

                foreach($message_content_hourly as $hkey=>$hvalue){
                    $hourly_message_content[$hkey]= $new_postback_id_information[$hvalue];
                }

                $sequence_campaign_list['message_content_hourly'] = json_encode($hourly_message_content);

                // Insert sequence information. unset id , set page_id & user_id
                unset($sequence_campaign_list['id']);
                $sequence_campaign_list['page_id']=$page_id;
                $sequence_campaign_list['user_id']=$this->user_id;
                $sequence_campaign_list['media_type']=$media_type;

                //assaign new visual campaign id in sequence campaign if it's created from flow builder, otherwise 0. 
             //   $old_visual_flow_campaign_id= $sequence_campaign_list['visual_flow_campaign_id'] ?? 0;
              //  $sequence_campaign_list['visual_flow_campaign_id'] =$visual_flow_campaign_insert_log[$old_visual_flow_campaign_id];

                $this->basic->insert_data("messenger_bot_drip_campaign",$sequence_campaign_list);
                $new_sequence_id= $this->db->insert_id();

                // Now update messenger_bot_postback table drip_campaign_id column with new sequence id
                 $this->basic->update_data("messenger_bot_postback",array("user_id"=>$this->user_id,"page_id"=>$page_id,"drip_campaign_id"=>$old_sequence_id),array("drip_campaign_id"=>$new_sequence_id));

                 $this->basic->update_data("messenger_bot",array("user_id"=>$this->user_id,"page_id"=>$page_id,"drip_campaign_id"=>$old_sequence_id),array("drip_campaign_id"=>$new_sequence_id));
                 
            }

        }


        // -------------- End of sequence campaign information


        if($media_type=='fb'){

            // inserting persistent menu
            if($this->session->userdata('user_type') == 'Admin' || in_array(197,$this->module_access))
            {
                foreach ($persistent_menu_settings as $key => $value) 
                {
                    $persistent_menu_row=array();
                    foreach ($value as $key2 => $value2) 
                    {
                       $persistent_menu_row[$key2]=$value2;
                    }
                    $persistent_menu_row['page_id']=$page_id;
                    $persistent_menu_row['user_id']=$this->user_id;
                    unset($persistent_menu_row['id']);
                    $this->basic->insert_data("messenger_bot_persistent_menu",$persistent_menu_row); 
                }
            }

    }


        //-----------------------------------------------------------------
        

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            echo "<div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$this->lang->line("Import was unsuccessful. Database error occured during importing template.")."</div>";
            exit();
        }

        $welcome_message=isset($bot_general_info['welcome_message'])?$bot_general_info['welcome_message']:"";
        $started_button_enabled=isset($bot_general_info['started_button_enabled'])?$bot_general_info['started_button_enabled']:"0";
        $persistent_enabled=isset($bot_general_info['persistent_enabled'])?$bot_general_info['persistent_enabled']:"0";
        $enable_mark_seen=isset($bot_general_info['enable_mark_seen'])?$bot_general_info['enable_mark_seen']:"0";
        $enbale_type_on=isset($bot_general_info['enbale_type_on'])?$bot_general_info['enbale_type_on']:"0";
        $reply_delay_time=isset($bot_general_info['reply_delay_time'])?$bot_general_info['reply_delay_time']:"0";

        $this->load->library("fb_rx_login"); 


        if($media_type=="fb"){

            //enabling get started
            $error_msg_array=array();
            $success_msg_array=array();
            if($started_button_enabled=='1')
            {
                $response=$this->fb_rx_login->add_get_started_button($page_access_token);
                if(!isset($response['error']))
                {
                    $response2=$this->fb_rx_login->set_welcome_message($page_access_token,$welcome_message);
                    if(!isset($response2['error']))
                    {
                       $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id,"user_id"=>$this->user_id),array("started_button_enabled"=>"1","welcome_message"=>$welcome_message));
                       $success_msg=$this->lang->line("Successful");
                       $success_msg=$this->lang->line("Enable Get Started")." : ".$success_msg;
                       array_push($success_msg_array, $success_msg);
                    }
                    else
                    {
                        $error_msg=isset($response2['error']['message'])?$response2['error']['message']:$this->lang->line("something went wrong, please try again.");
                        $error_msg=$this->lang->line("Enable Get Started")." : ".$error_msg;
                        array_push($error_msg_array, $error_msg);
                    }
                }
                else
                {
                    $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                    $error_msg=$this->lang->line("Enable Get Started")." : ".$error_msg;
                    array_push($error_msg_array, $error_msg);
                }
            }
            else
            {
                $response=$this->fb_rx_login->delete_get_started_button($page_access_token);
                if(!isset($response['error']))
                {
                    $response2=$this->fb_rx_login->unset_welcome_message($page_access_token);
                    if(!isset($response2['error']))
                    {
                       $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id,"user_id"=>$this->user_id),array("started_button_enabled"=>"0","welcome_message"=>""));
                       $success_msg=$this->lang->line("Successful");
                       $success_msg=$this->lang->line("Disable Get Started")." : ".$success_msg;
                       array_push($success_msg_array, $success_msg);
                    }
                    else
                    {
                        $error_msg=isset($response2['error']['message'])?$response2['error']['message']:$this->lang->line("something went wrong, please try again.");
                        $error_msg=$this->lang->line("Disable Get Started")." : ".$error_msg;
                        array_push($error_msg_array, $error_msg);
                    }
                }
                else
                {
                    $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                    $error_msg=$this->lang->line("Disable Get Started")." : ".$error_msg;
                    array_push($error_msg_array, $error_msg);
                }
            }
            //-----------------------------------------------------------------


            // Publishing persistent menu
            if($this->session->userdata('user_type') == 'Admin' || in_array(197,$this->module_access))
            {
                if($persistent_enabled=='1')
                {
                    $json_array=array();
                    $menu_data=$this->basic->get_data("messenger_bot_persistent_menu",array("where"=>array("page_id"=>$page_id,"user_id"=>$this->user_id)));
                    foreach ($menu_data as $key => $value) 
                    {
                        $temp=json_decode($value["item_json"],true);
                        $json_array["persistent_menu"][]=$temp;
                    }            
                    $json=json_encode($json_array);          
                    $response=$this->fb_rx_login->add_persistent_menu($page_access_token,$json);            
                    if(!isset($response['error']))
                    {                
                        $this->basic->update_data('facebook_rx_fb_page_info',array("id"=>$page_id,'user_id'=>$this->user_id),array("persistent_enabled"=>'1'));
                        $success_msg=$this->lang->line("Successful");
                        $success_msg=$this->lang->line("Persistent Menu Publish")." : ".$success_msg;
                        array_push($success_msg_array, $success_msg);
                    }
                    else
                    {
                        $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                        $error_msg=$this->lang->line("Persistent Menu Publish")." : ".$error_msg;
                        array_push($error_msg_array, $error_msg);
                    }
                }
                else
                {         
                    $response=$this->fb_rx_login->delete_persistent_menu($page_access_token);            
                    if(!isset($response['error']))
                    {                
                        $this->basic->update_data('facebook_rx_fb_page_info',array("id"=>$page_id,'user_id'=>$this->user_id),array("persistent_enabled"=>'0'));
                        $success_msg=$this->lang->line("Successful");
                        $success_msg=$this->lang->line("Persistent Menu Remove")." : ".$success_msg;
                        array_push($success_msg_array, $success_msg);
                    }
                    else
                    {
                        $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                        $error_msg=$this->lang->line("Persistent Menu Remove")." : ".$error_msg;
                        array_push($error_msg_array, $error_msg);
                    }
                }
            }
            //-----------------------------------------------------------------

            
            // enabling mark seen       
            if($this->basic->update_data('facebook_rx_fb_page_info',array('id'=>$page_id,"user_id"=>$this->user_id),array('enable_mark_seen'=>$enable_mark_seen)))
            {
                if($enable_mark_seen=='1')
                {
                    $success_msg=$this->lang->line("Successful");
                    $success_msg=$this->lang->line("enable mark seen")." : ".$success_msg;
                    array_push($success_msg_array, $success_msg);
                }
                else
                {
                    $success_msg=$this->lang->line("Successful");
                    $success_msg=$this->lang->line("disable mark seen")." : ".$success_msg;
                    array_push($success_msg_array, $success_msg);
                }
            }
            else
            {
                $error_msg=$this->lang->line("something went wrong, please try again.");
                if($enable_mark_seen=='1') $error_msg=$this->lang->line("enable mark seen")." : ".$error_msg;
                else $error_msg=$this->lang->line("disable mark seen")." : ".$error_msg;
                array_push($error_msg_array, $error_msg);
            }    
        }

        //-----------------------------------------------------------------
        


        // typing on settings
        // if($this->basic->update_data('facebook_rx_fb_page_info',array('id'=>$page_id,"user_id"=>$this->user_id),array('enbale_type_on'=>$enbale_type_on,'reply_delay_time'=>$reply_delay_time)))
        // {
        //     $success_msg=$this->lang->line("Successful");
        //     $success_msg=$this->lang->line("Typing on Settings")." : ".$success_msg;
        //     array_push($success_msg_array, $success_msg);
        // }
        // else
        // {
        //     $error_msg=$this->lang->line("something went wrong, please try again.");
        //     $error_msg=$this->lang->line("Typing on Settings")." : ".$error_msg;
        //     array_push($error_msg_array, $error_msg);
        // }
        //-----------------------------------------------------------------



        $this->session->set_userdata("selected_global_page_table_id",$page_id); 

        echo "<div class='alert alert-info text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Template settings has been imported to database successfully.")." <a href='".base_url("messenger_bot/bot_list")."'><i class='fas fa-hand-pointer'></i> ".$this->lang->line("Go to bot settings")."</a></div>";

        if(!empty($success_msg_array))
        {
            echo "<br><br>";
            echo "<div class='text-left'><i class='fas fa-list-ol'></i> ".$this->lang->line("Related successful operations")."<br>";
            $i=0;
                echo '<div style="margin-top:10px;padding-left:10px;">';
                    foreach ($success_msg_array as $key => $value) 
                    {
                        $i++;
                        echo "<i class='fa fa-check-circle'></i> ".$value.'<br>';
                    }
                echo '</div>';
            echo "</div>";
        }

        if(!empty($error_msg_array))
        {
            echo "<br><br>";
            echo "<div class='alert alert-warning'><i class='fa fa-info-circle'></i> ".$this->lang->line("Related unsuccessful operations").":<br>";
            $i=0;
                echo '<div style="margin-top:10px;padding-left:10px;">';
                    foreach ($error_msg_array as $key => $value) 
                    {
                        $i++;
                        echo "<i class='fa fa-remove'></i> ".$value.'<br>';
                    }
                echo '</div>';
            echo "</div>";
        }

        
    }


    private function get_bot_settings($page_table_id=0,$media_type='fb')
    {
        $where['where'] = array('page_id'=> $page_table_id,"user_id"=>$this->user_id,'media_type'=>$media_type);
        /**Get BOT settings information from messenger_bot table as base table. **/
        $messenger_bot_info = $this->basic->get_data("messenger_bot",$where);
        $bot_settings=array();
        $i=0;
        foreach ($messenger_bot_info as $bot_info) 
        {
            $message_bot_id= $bot_info['id'];
            foreach ($bot_info as $key => $value) 
            {
                if($key=='id' || $key=='user_id' || $key=='page_id' || $key=='fb_page_id' || $key=='last_replied_at' || $key=='broadcaster_labels') continue;
                $bot_settings[$i]['message_bot'][$key]=$value;
            }

            /*** Get postback information from messenger_bot_postback table, it's from postback manager  ****/
            $where['where'] = array('messenger_bot_table_id'=> $message_bot_id);
            $messenger_postback_info = $this->basic->get_data("messenger_bot_postback",$where);

            $j=0;
            foreach ($messenger_postback_info as $postback_info) 
            {
                $message_postback_id= $postback_info['id'];
                foreach ($postback_info as $key1 => $value1) 
                {
                    if($key1=="template_id" || $key1=='user_id' || $key1=='page_id' || $key1=='messenger_bot_table_id' || $key1=='last_replied_at' || $key1=='broadcaster_labels') continue;
                    $bot_settings[$i]['message_bot']['postback_template_info'][$j][$key1]=$value1;
                }
                /** Get Child Postback from Post back Manager  whose BOT is already set.**/
                $where['where'] = array('template_id'=> $message_postback_id);
                $messenger_postback_child_info = $this->basic->get_data("messenger_bot_postback",$where);
                $m=0;
                foreach ($messenger_postback_child_info as $postback_child_info) 
                {
                    foreach ($postback_child_info as $key2 => $value2) 
                    {
                        if($key2=="template_id" || $key2=='id' || $key2=='user_id' || $key2=='page_id' || $key2=='messenger_bot_table_id' || $key2=='last_replied_at' || $key2=='broadcaster_labels') continue;

                        $bot_settings[$i]['message_bot']['postback_template_info'][$j]["postback_child"][$m][$key2]=$value2;
                    }
                    $m++;
                }
                $j++;
            }
            $i++;
        }
        /*** Get empty Postback from messenger_bot_postback table. The child postback for those bot isn't set yet . ***/
        $where['where'] = array('template_id'=> '0','messenger_bot_table_id'=>'0','is_template'=>'0','page_id'=>$page_table_id,'media_type'=>$media_type);
        $messenger_emptypostback_info = $this->basic->get_data("messenger_bot_postback",$where);
        $empty_postback_settings=array();
        $x=0;
        foreach ($messenger_emptypostback_info as $emptypostback_child_info) 
        {
            foreach ($emptypostback_child_info as $key4 => $value4) 
            {
                if($key4=='id' || $key4=='user_id' || $key4=='page_id' || $key4=='messenger_bot_table_id' || $key4=='last_replied_at' || $key4=='broadcaster_labels') continue;
                $empty_postback_settings[$x][$key4]=$value4;
            }
            $x++;
        }


        if($media_type=='fb'){
            /****   Get Information of Persistent Menu ***/
            $persistent_menu_settings=array();
            $where['where'] = array('page_id'=>$page_table_id);
            $persistent_menu_info = $this->basic->get_data("messenger_bot_persistent_menu",$where);
            $y=0;
            foreach ($persistent_menu_info as $persistent_menu) 
            {
                foreach ($persistent_menu as $key5 => $value5) 
                {
                    $persistent_menu_settings[$y][$key5] = $value5;
                }
                $y++;
            }
        }


        /***Get general information from facebook_rx_fb_page_info table***/
        $bot_general_info=array();
        $where['where'] = array('id'=>$page_table_id);
        $bot_page_general_info = $this->basic->get_data("facebook_rx_fb_page_info",$where);
        foreach ($bot_page_general_info as $general_info) 
        {
            $bot_general_info['welcome_message']= isset($general_info['welcome_message']) ? $general_info['welcome_message']:"";
            $bot_general_info['started_button_enabled']= isset($general_info['started_button_enabled']) ? $general_info['started_button_enabled']:"";
            $bot_general_info['persistent_enabled']= isset($general_info['persistent_enabled']) ? $general_info['persistent_enabled']:"";
            $bot_general_info['enable_mark_seen']= isset($general_info['enable_mark_seen']) ? $general_info['enable_mark_seen']:"";
            $bot_general_info['enbale_type_on']= isset($general_info['enbale_type_on']) ? $general_info['enbale_type_on']:"";
            $bot_general_info['reply_delay_time']= isset($general_info['reply_delay_time']) ? $general_info['reply_delay_time']:"";
        }


        $full_bot_settings=array();
        $full_bot_settings['bot_settings']=$bot_settings;
        $full_bot_settings['empty_postback_settings']=$empty_postback_settings;     
        if($media_type=="fb")
            $full_bot_settings['persistent_menu_settings']=$persistent_menu_settings;    
        $full_bot_settings['bot_general_info']=$bot_general_info;   
        $full_bot_settings['media_type']= $media_type;

        // Get input flow settings information 
        if($this->db->table_exists('user_input_flow_campaign')) {

            $input_flow_info=$this->get_user_input_flow_settings_info($this->user_id,$page_table_id,$media_type);
            $full_bot_settings['custom_fields']=$input_flow_info['custom_fields'];   
            $full_bot_settings['input_flow']=$input_flow_info['input_flow'];   
        }

        // Get Flow Builder's All campaign 

        if($this->db->table_exists('visual_flow_builder_campaign')) {
            $full_bot_settings['flow_builder_campaign_information']= $this->get_flow_builder_settings_info($this->user_id,$page_table_id,$media_type);
        }

        if($this->db->table_exists('messenger_bot_drip_campaign')) {
            $full_bot_settings['sequence_campaign_information']= $this->get_sequence_campaign_info($this->user_id,$page_table_id,$media_type);
        }
        return $full_bot_settings;
    }


    public function get_flow_builder_settings_info($user_id,$page_table_id,$media_type="fb"){

        $flow_builder_campaign_where['where']=array("user_id"=>$user_id,"page_id"=>$page_table_id,'media_type'=>$media_type);
        $flow_builder_campaign_information = $this->basic->get_data("visual_flow_builder_campaign",$flow_builder_campaign_where,array("id","reference_name","json_data"));
        return $flow_builder_campaign_information;
    }

    public function get_sequence_campaign_info($user_id,$page_table_id,$media_type="fb"){

    	$sequence_campaign_where['where']=array("user_id"=>$user_id,"page_id"=>$page_table_id,"campaign_type"=>"messenger","drip_type"=>"custom",'media_type'=>$media_type);
        $sequence_campaign_information = $this->basic->get_data("messenger_bot_drip_campaign",$sequence_campaign_where,array("id","campaign_name","message_content","message_content_hourly","between_start","between_end","timezone","message_tag","visual_flow_campaign_id","visual_flow_sequence_id"));
        return $sequence_campaign_information;

    }


    public function get_user_input_flow_settings_info($user_id,$page_table_id,$media_type="fb"){

        // get custom fields data. Custom fields data is user based not page based. So collecting it as where user_id 
        $custom_field_where['where']=array("user_id"=>$user_id,'media_type'=>$media_type);
        $custom_field_information = $this->basic->get_data("user_input_custom_fields",$custom_field_where,array("id","user_id","name","reply_type"));

        // Get User Input flow Campaign data 
        $user_input_flow_where['where']=array("page_table_id"=>$page_table_id,"deleted"=>"0",'media_type'=>$media_type);
        $user_input_flow_campaign_information= $this->basic->get_data("user_input_flow_campaign",$user_input_flow_where,array("id","flow_name","postback_id"));

        // Collect flow's all question for each campaign. 

        foreach ($user_input_flow_campaign_information as $key=>$info) {
                
            $campaign_id= isset($info['id']) ? $info['id'] : "";

            // Collect questions of this campaign. 
            $select_fields=array("flow_campaign_id","serial_no","question","type","reply_type","quick_reply_email","quick_reply_phone","multiple_choice_options","custom_field_id","messenger_sequence_id","email_phone_sequence_id","system_field","skip_button_text");

            $user_input_flow_campaign_question= $this->basic->get_data("user_input_flow_questions",array("where"=>array("flow_campaign_id"=>$campaign_id)),$select_fields,$join="", $limit="", $start="", "serial_no ASC");

            $user_input_flow_campaign_information[$key]['questions_list']=$user_input_flow_campaign_question;
        }

        $response['custom_fields']= $custom_field_information;
        $response['input_flow']= $user_input_flow_campaign_information;
        return $response;

    }

    

    public function tree_view($page_id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if($page_id==0) exit();
        $page_table_id=$page_id;


        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id,"user_id"=>$this->user_id)));
    

        /***    Get Started Information    ***/
        $where=array();
        $where['where'] = array('page_id'=> $page_table_id,'keyword_type'=>"get-started");
        $messenger_bot_info = $this->basic->get_data("messenger_bot",$where,$select='',$join='',$limit='1');
        $this->postback_info=array();
        $get_started_data=$this->get_child_info($messenger_bot_info,$page_table_id);
        $get_started_data_copy=$get_started_data;

        $get_started_tree = $this->make_tree($get_started_data_copy,1,$page_table_id);

         /***   No match tree    ***/
        $where=array();
        $where['where'] = array('page_id'=> $page_table_id,'keyword_type'=>"no match");
        $messenger_bot_info = $this->basic->get_data("messenger_bot",$where,$select='',$join='',$limit='1');
        $this->postback_info=array();
        $no_match_data=$this->get_child_info($messenger_bot_info,$page_table_id);
        $no_match_data_copy=$no_match_data;

        $no_match_tree = $this->make_tree($no_match_data_copy,2,$page_table_id);


        /**Get BOT settings information from messenger_bot table as base table. **/
        $where=array();
        $where['where'] = array('page_id'=> $page_table_id,'keywords !=' => "");
        $messenger_bot_info = $this->basic->get_data("messenger_bot",$where);
        $this->postback_info=array();
        $keyword_data=$this->get_child_info($messenger_bot_info,$page_table_id);
        $keyword_data_copy=$keyword_data;

        $keyword_bot_tree=array();

        foreach ($keyword_data_copy as $key => $value) 
        {
            $bot_tree_optimize_array=array($key=>$value);
            // echo "<pre>";print_r($bot_tree_optimize_array); 
            $keyword_bot_tree[] = $this->make_tree($bot_tree_optimize_array,0,$page_table_id);
        }


        $data['get_started_tree']=$get_started_tree;
        $data['keyword_bot_tree']=$keyword_bot_tree;
        $data['no_match_tree']=$no_match_tree;
        $data['body']='messenger_tools/tree_view';
        $data['page_info'] = isset($page_info[0])?$page_info[0]:array();
        $page_name = isset($page_info[0]['page_name']) ? $page_info[0]['page_name'] : "";
        $data['page_title']=$page_name.' - '.$this->lang->line("Tree View");
        $this->_viewcontroller($data);
    }

   

    private function make_tree($get_started_data_copy,$is_get_started=1,$page_table_id=0) // 0 = keyword, 1=get started, 2 = no match
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) return "";
        $get_started_level=0;
        $postback_array=array();
        $parent_key='';
        $linear_postback_array=array(); // holds associative array of postback and it's content
        foreach ($get_started_data_copy as $key => $value) 
        {
            $parent_key=$key;
            $postback_array=isset($value['postback_info'])?$value['postback_info']:array();
            $keywrods_list=isset($value['keywrods_list'])?$value['keywrods_list']:array();
            $postback_array_temp=$postback_array;
            foreach ($postback_array as $key2 => $value2) 
            {
                if(!isset($linear_postback_array[$key2]))
                $linear_postback_array[$key2]=$value2;
            }
            $last_postback_info=array_pop($postback_array_temp);
            $get_started_level=isset($last_postback_info['level'])?$last_postback_info['level']:0; // maximum postback nest level
            break;
        }

        $this->postback_array=$this->set_nest_easy($postback_array,$get_started_level);

        // putting nested postback to main data
        if(isset($get_started_data_copy[$parent_key]['postback_info']))$get_started_data_copy[$parent_key]['postback_info']=$this->postback_array;
        if($is_get_started!='0')// keyword list is always empty for get started and no match
        if(isset($get_started_data_copy[$parent_key]['keywrods_list']))unset($get_started_data_copy[$parent_key]['keywrods_list']);

        if($is_get_started=='1')
        {
            if($parent_key=="") $getstarted_start='<i class="fas fa-check-circle"></i> Get <br> Started'; 
            else $getstarted_start='<div class="getstartedcell"><a class="iframed" href="'.base_url('messenger_bot/edit_bot/'.$parent_key.'/1/getstart').'"><span data-toggle="tooltip" title="Click to edit settings"><i class="fas fa-check-circle"></i> Get <br> Started</span></a></div>';
        }
        else if($is_get_started=='2')
        {
            if($parent_key=="") $getstarted_start='<i class="fas fa-comment-slash""></i> No <br> Match'; 
            else $getstarted_start='<div class="getstartedcell"><a class="iframed" href="'.base_url('messenger_bot/edit_bot/'.$parent_key.'/1/nomatch').'"><span data-toggle="tooltip" title="Click to edit settings"><i class="fas fa-comment-slash"></i> No <br> Match</span></a></div>';
        }
        else
        {
            if($parent_key=="") $getstarted_start='<i class="fas fa-times"></i> No <br> Keyword'; // no get started found
            else $getstarted_start='<div class="keywordcell" title="'.$keywrods_list.'"><a class="iframed" href="'.base_url('messenger_bot/edit_bot/'.$parent_key.'/1').'"><span data-toggle="tooltip" title="Click to edit settings">'.$keywrods_list.'</span></a></div>';
        }


        $get_started_tree='
        <li>
            '.$getstarted_start.'
            <ul>';
                foreach ($get_started_data_copy as $key_temp => $value_temp) 
                {
                  foreach ($value_temp as $key_temp2 => $value_temp2) 
                  {
                    if($key_temp2=="keywrods_list") continue;
                    if($key_temp2!="postback_info")
                    {
                      $templabel=$this->formatlabel($this->tree_security($key_temp2));                      
                      if(is_array($value_temp2) && !empty($value_temp2))
                      {
                          if($key_temp2=="web_url") 
                          {
                            foreach($value_temp2 as $tempukey => $tempuval) 
                            {                                
                                $get_started_tree.= '
                                <li>
                                    <a data-toggle="tooltip" title="'.$this->tree_security($tempuval).'" href="'.$this->tree_security($tempuval).'" target="_blank"><i class="fas fa-external-link-alt"></i> '.$templabel.'</a>
                                </li>';
                            }
                          }
                          else if($key_temp2=="call_us") 
                          {
                            foreach($value_temp2 as $tempukey => $tempuval) 
                            {                                
                                $get_started_tree.= '
                                <li data-toggle="tooltip" title="'.$this->tree_security($tempuval).'"><i class="fas fa-headset"></i> '.$templabel.'</li>';
                            }
                          }
                          else if($key_temp2=="birthdate") 
                          {
                            foreach($value_temp2 as $tempukey => $tempuval) 
                            {                                
                                $get_started_tree.= '
                                <li data-toggle="tooltip" title="'.$this->tree_security($tempuval).'"><i class="fas fa-birthday-cake"></i> '.$templabel.'</li>';
                            }
                          }
                          else if($key_temp2=="webview") 
                          {
                            foreach($value_temp2 as $tempukey => $tempuval) 
                            {                                
                                $get_started_tree.= '
                                <li>
                                    <a data-toggle="tooltip" title="'.$this->tree_security($tempuval).'" href="'.$this->tree_security($tempuval).'" target="_blank"><i class="fab fa-wpforms"></i> '.$templabel.'</a>
                                </li>';
                            }
                          }
                          else 
                          {
                            foreach($value_temp2 as $tempukey => $tempuval) 
                            {                                
                                $get_started_tree.= '
                                <li><i class="far fa-circle"></i> '.$templabel.'</li>';
                            }
                          }
                      }
                    }
                    else //postback sub-tree
                    {
                      $postback_info=array_filter($value_temp2);

                      if(count($postback_info)>0)                        
                        foreach ($postback_info as $key0 => $value0)
                        {       
                            if(is_array($value0)) // if have new child that does not appear in parent tree
                            {
                                $tempid=isset($value0['id'])?$value0['id']:0;
                                $tempis_template=isset($value0['is_template'])?$value0['is_template']:'';
                                $tempostbackid=isset($value0['postback_id'])?$this->tree_security($value0['postback_id']):'';
                                $tempbotname=isset($value0['bot_name'])?$this->tree_security($value0['bot_name']):'';

                                if($tempis_template=='1') $tempurl=base_url('messenger_bot/edit_template/'.$tempid.'/1'); // it is template
                                else if($tempis_template=='0') $tempurl=base_url('messenger_bot/edit_bot/'.$tempid.'/1'); // it is bot
                                else $tempurl="";
                                
                                if($tempbotname!='') $display="<span class='text-info' data-toggle='tooltip' title='".$tempostbackid." : click to edit settings'><i class='far fa-hand-pointer'></i> ".$tempbotname.'</span>';
                                else $display="<span class='text-info'><i class='far fa-hand-pointer'></i> ".$tempostbackid.'</span>';

                                if($tempurl!="") $templabel='<a class="iframed" href="'.$tempurl.'">'.$display.'</a>';
                                else $templabel=$display;

                                $get_started_tree.= '
                                <li>'.$templabel;
                            }
                            else // child already appear in parent tree
                            {                                
                                if(isset($linear_postback_array[$value0]))
                                {
                                    $tempid=isset($linear_postback_array[$value0]['id'])?$linear_postback_array[$value0]['id']:0;
                                    $tempis_template=isset($linear_postback_array[$value0]['is_template'])?$linear_postback_array[$value0]['is_template']:'';
                                    $tempostbackid=isset($linear_postback_array[$value0]['postback_id'])?$this->tree_security($linear_postback_array[$value0]['postback_id']):'';
                                    $tempbotname=isset($linear_postback_array[$value0]['bot_name'])?$this->tree_security($linear_postback_array[$value0]['bot_name']):'';

                                    if($tempis_template=='1') $tempurl=base_url('messenger_bot/edit_template/'.$tempid.'/1'); // it is template
                                    else if($tempis_template=='0') $tempurl=base_url('messenger_bot/edit_bot/'.$tempid.'/1'); // it is bot
                                    else $tempurl="";

                                    if($tempbotname!='') $display="<span class='text-muted' data-toggle='tooltip' title='".$tempostbackid." is already exist in the tree><i class='far fa-hand-pointer'></i> ".$tempbotname.'</span>';
                                    else $display="<span class='text-muted' class='text-muted' data-toggle='tooltip' title='".$tempostbackid." is already exist in the tree'><i class='far fa-hand-pointer'></i> ".$tempostbackid.'</span>';

                                    if($tempurl!="") $templabel='<a class="iframed" href="'.$tempurl.'">'.$display.'</a>';
                                    else $templabel=$display;

                                    $get_started_tree.= '
                                    <li>'.$templabel;
                                }
                            }

                           $phpcomand_array=array();
                           $closing_bracket='';

                           for($i=1; $i<=$get_started_level;$i++) 
                            {    
                                $phpcomand_array[]=$this->get_nest($i,$page_table_id);
                                $closing_bracket.="}  \$get_started_tree.='</ul>';";                                
                            }
                            $phpcomand_str=implode(' ', $phpcomand_array);
                            $phpcomand_str.=$closing_bracket;
                            eval($phpcomand_str);
                            
                            $get_started_tree.= 
                            "</li>";
                        }


                    } // end if postbock          
                  } // end 2nd foreach
                } // end 1st foreach
            $get_started_tree.='
            </ul>
        </li>';

        return $get_started_tree;

    }



    private function formatlabel($raw="")
    {
        if($raw=="") return "";  
        $tempraw=str_replace('_', ' ', $raw);
        $tempraw=ucwords($tempraw);
        return $tempraw;
    }

    private function tree_security($input="")
    {
        $output=strip_tags($input);
        $output=str_replace(array('<?php','<?','<? php','?>','<?=','$','(',')','{','}','[',']',"'",'"',"\\"), "", $input);
        return $output;
    }



    public function typing_on_settings()
    {
        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $table_id=$this->input->post('table_id');
        $reply_delay_time=$this->input->post('reply_delay_time');
        $enbale_type_on=$this->input->post('enbale_type_on');
        if($enbale_type_on=="0") $reply_delay_time=0;
        $this->basic->update_data('facebook_rx_fb_page_info',array('id'=>$table_id,"user_id"=>$this->user_id),array('enbale_type_on'=>$enbale_type_on,'reply_delay_time'=>$reply_delay_time));
        $this->session->set_flashdata('bot_action',$this->lang->line("Settings has been saved successfully."));
    }

    

    public function mark_seen_chat_human_settings()
    {

        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $table_id=$this->input->post('table_id',true);
        $mark_seen_status=$this->input->post('mark_seen_status');
        $chat_human_email=strip_tags($this->input->post('chat_human_email',true));
        $no_match_found_reply=strip_tags($this->input->post('no_match_found_reply',true));
        $mailchimp_list_id=$this->input->post('mailchimp_list_id');
        $sendinblue_list_id=$this->input->post('sendinblue_list_id');
        $activecampaign_list_id=$this->input->post('activecampaign_list_id');
        $mautic_list_id=$this->input->post('mautic_list_id');
        $acelle_list_id=$this->input->post('acelle_list_id');
        
        $sms_api_id=$this->input->post('sms_api_id');
        $message=$this->input->post('sms_reply_message');
       // $sms_reply_message=str_replace(array("'",'"'),array('`','`'),$message);
        $sms_reply_message=$message;


        $email_api_id=$this->input->post('email_api_id');
        $email_message=$this->input->post('email_reply_message');
        $email_reply_subject=$this->input->post('email_reply_subject');
        //$email_reply_message=str_replace(array("'",'"'),array('`','`'),$email_message);
        $email_reply_message=$email_message;

        $sequence_sms_api_id = $this->input->post("sequence_sms_api_id");
        $sequence_email_api_id = $this->input->post("sequence_email_api_id");
        $sequence_sms_campaign_id = $this->input->post("sequence_sms_campaign_id");
        $sequence_email_campaign_id = $this->input->post("sequence_email_campaign_id");
        $media_type = $this->input->post("media_type");

        if($mailchimp_list_id == '') 
            $mail_service = array('mailchimp'=>array());
        else
            $mail_service = array('mailchimp'=>$mailchimp_list_id);

        /* sendinblue */
        if($sendinblue_list_id == '') 
            $mail_service['sendinblue'] = array();
        else
            $mail_service['sendinblue'] = $sendinblue_list_id;

        /* activecampaign */
        if($activecampaign_list_id == '') 
            $mail_service['activecampaign'] = array();
        else
            $mail_service['activecampaign'] = $activecampaign_list_id;

        /* mautic */
        if($mautic_list_id == '') 
            $mail_service['mautic'] = array();
        else
            $mail_service['mautic'] = $mautic_list_id;

        /* acelle */
        if($acelle_list_id == '') 
            $mail_service['acelle'] = array();
        else
            $mail_service['acelle'] = $acelle_list_id;


        $mail_service = json_encode($mail_service);

        $updated_data = array();
        $updated_data['enable_mark_seen'] = $mark_seen_status;
        $updated_data['mail_service_id'] = $mail_service;
        $updated_data['sms_api_id'] = $sms_api_id;
        $updated_data['sms_reply_message'] = $sms_reply_message;
        $updated_data['email_api_id'] = $email_api_id;
        $updated_data['email_reply_message'] = $email_reply_message;
        $updated_data['email_reply_subject'] = $email_reply_subject;
        $updated_data['sequence_sms_api_id'] = $sequence_sms_api_id;
        $updated_data['sequence_email_api_id'] = $sequence_email_api_id;
        $updated_data['sequence_sms_campaign_id'] = $sequence_sms_campaign_id;
        $updated_data['sequence_email_campaign_id'] = $sequence_email_campaign_id;
        if($media_type == "ig") {
            $updated_data['ig_chat_human_email'] = $chat_human_email;
            $updated_data['ig_no_match_found_reply'] = $no_match_found_reply;
        } else {
            $updated_data['chat_human_email'] = $chat_human_email;
            $updated_data['no_match_found_reply'] = $no_match_found_reply;
        }

        $this->basic->update_data('facebook_rx_fb_page_info',array('id'=>$table_id),$updated_data);
        $response['status'] = '1';
        $response['message'] = $this->lang->line('General settings have been stored successfully.');
        echo json_encode($response);
    }

   
    public function check_page_response()
    {
        $response = array('has_pageresponse'=>'0');
        echo json_encode($response);
    }

    public function delete_full_bot()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        exit();
        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $user_id = $this->user_id;
        $page_id=$this->input->post('page_id');
        $already_disabled=$this->input->post('already_disabled');

        $this->load->library("fb_rx_login");         

        $page_data=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id)));
        $fb_page_id=isset($page_data[0]["page_id"]) ? $page_data[0]["page_id"] : "";
        $page_access_token=isset($page_data[0]["page_access_token"]) ? $page_data[0]["page_access_token"] : "";
        $persistent_enabled=isset($page_data[0]["persistent_enabled"]) ? $page_data[0]["persistent_enabled"] : "0";
        $ice_breaker_status=isset($page_data[0]["ice_breaker_status"]) ? $page_data[0]["ice_breaker_status"] : "0";
        $fb_user_id = $page_data[0]["facebook_rx_fb_user_info_id"];
        $fb_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$fb_user_id)));
        $this->fb_rx_login->app_initialize($fb_user_info[0]['facebook_rx_config_id']);

        $updateData=array("bot_enabled"=>"0");
        if($already_disabled == 'no')
        {            
            if($persistent_enabled=='1') 
            {
                $updateData['persistent_enabled']='0';
                $updateData['started_button_enabled']='0';
                $this->fb_rx_login->delete_persistent_menu($page_access_token); // delete persistent menu
                $this->fb_rx_login->delete_get_started_button($page_access_token); // delete get started button
                $this->basic->delete_data("messenger_bot_persistent_menu",array("page_id"=>$page_id,"user_id"=>$this->user_id));                
            }
            if($ice_breaker_status=='1') 
            {
                $updateData['ice_breaker_status']='0';
                $this->fb_rx_login->delete_ice_breakers($page_access_token);
            }
            $response=$this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);
        }
        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id),$updateData);
        $this->_delete_usage_log($module_id=200,$request=1);

        $this->delete_bot_data($page_id,$fb_page_id);

        echo json_encode(array('success'=>'successfully deleted.'));

    }


    private function delete_bot_data($page_id,$fb_page_id)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if($this->db->table_exists('messenger_bot_engagement_checkbox'))
        {            
            $get_checkbox=$this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("page_id"=>$page_id)));
            $checkbox_ids=array();
            foreach ($get_checkbox as $key => $value) 
            {
                $checkbox_ids[]=$value['id'];
            }

            $this->basic->delete_data("messenger_bot_engagement_checkbox",array("page_id"=>$page_id));
        
            if(!empty($checkbox_ids))
            {
                $this->db->where_in('checkbox_plugin_id', $checkbox_ids);
                $this->db->delete('messenger_bot_engagement_checkbox_reply');
            }
        }

        $del_list=array (
          0 => 
          array 
          (
            'table_name' => 'messenger_bot',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          1 => 
          array (
            'table_name' => 'messenger_bot_persistent_menu',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          2 => 
          array (
            'table_name' => 'messenger_bot_postback',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),         
          4 => 
          array (
            'table_name' => 'messenger_bot_reply_error_log',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          5 => 
          array (
            'table_name' => 'messenger_bot_subscriber',
            'where_field' => 'page_id',
            'value' =>$fb_page_id,
          ),
          7 => 
          array (
            'table_name' => 'fb_chat_plugin_2way',
            'where_field' => 'page_auto_id',
            'value' =>$page_id,
            'where_field2' => 'core_or_bot',
            'value2' =>'0',
          ),
          8 => 
          array (
            'table_name' => 'messenger_bot_domain_whitelist',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          9 => 
          array (
            'table_name' => 'messenger_bot_engagement_2way_chat_plugin',
            'where_field' => 'page_auto_id',
            'value' =>$page_id,
          ),
          10 => 
          array (
            'table_name' => 'messenger_bot_engagement_messenger_codes',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          11 => 
          array (
            'table_name' => 'messenger_bot_engagement_mme',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          12 => 
          array (
            'table_name' => 'messenger_bot_engagement_send_to_msg',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          13 => 
          array (
            'table_name' => 'messenger_bot_drip_campaign',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          14 => 
          array (
            'table_name' => 'messenger_bot_drip_report',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          15 => 
          array (
            'table_name' => 'messenger_bot_broadcast',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          16 => 
          array (
            'table_name' => 'messenger_bot_broadcast_contact_group',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          17 => 
          array (
            'table_name' => 'messenger_bot_broadcast_serial',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          18 => 
          array (
            'table_name' => 'messenger_bot_broadcast_serial_send',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
        );

        foreach ($del_list as $key => $value) 
        {
            if($this->db->table_exists($value['table_name']))
            {
                $where=array($value['where_field']=>$value['value']);
                if(isset($value['where_field2'])) $where[$value['where_field2']]=$value['value2'];
                $this->basic->delete_data($value['table_name'],$where);
            }
        }

        return true;
    } 

   //=============================DOMAIN WHITELIST================================
    public function domain_whitelist($page_auto_id='0',$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        redirect('home/login_page', 'location'); 
        $table = "facebook_rx_fb_page_info";
        $where_simple['facebook_rx_fb_page_info.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_page_info.bot_enabled'] = '1';
        $where  = array('where'=>$where_simple);
        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
        $page_info = $this->basic->get_data($table, $where, $select=array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name"),$join,'','','page_name asc');

        $pagelist=array();
        $i=0;
        foreach($page_info as $key => $value) 
        {
           $pagelist[$value["facebook_rx_fb_user_info_id"]]["account_name"]=$value['account_name'];
           $pagelist[$value["facebook_rx_fb_user_info_id"]]["page_data"][$i]["page_name"]=$value['page_name'];
           $pagelist[$value["facebook_rx_fb_user_info_id"]]["page_data"][$i]["page_id"]=$value['id'];
           $i++;
        }
        $data['page_title'] = $this->lang->line("Whitelisted Domains");
        $data['pagelist'] = $pagelist;
        $data['iframe'] = $iframe;
        $data['page_id'] = $page_auto_id;
        $data['body'] = 'messenger_tools/domain_list';
        $this->_viewcontroller($data); 
    }

    public function domain_whitelist_data()
    {
        $this->ajax_check();
        $domain_page = $_POST['search']['value'];
        $page_id = $this->input->post("page_id",true);
        $display_columns = array("#","CHECKBOX",'id', 'account_name', 'page_name', 'count', 'action');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'page_name';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'ASC';
        $order_by=$sort." ".$order;


        $where_simple=array();
        if($domain_page != '') {
            // $sql = "domain like '%".$domain_page."%' OR page_name like '%".$domain_page."%'";
            // $this->db->where($sql);
            $where_simple['messenger_bot_domain_whitelist.domain like'] = '%'.$domain_page.'%';
        }
        
        $where_simple['messenger_bot_domain_whitelist.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_page_info.user_id'] = $this->user_id;
        $where_simple['messenger_bot_domain_whitelist.page_id'] = $page_id;
        $where_simple['facebook_rx_fb_page_info.deleted'] = '0';
        $where_simple['facebook_rx_fb_page_info.bot_enabled'] = '1';
        $where  = array('where'=>$where_simple);
        $result = array();       
        $table = "messenger_bot_domain_whitelist";     
        $join = array(
            'facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot_domain_whitelist.page_id,left",
            'facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left"
        );   
        $group_by = "messenger_bot_domain_whitelist.page_id";
        $info = $this->basic->get_data($table, $where, $select=array("messenger_bot_domain_whitelist.*","facebook_rx_fb_page_info.page_name","facebook_rx_fb_page_info.id as page_table_id", "facebook_rx_fb_page_info.page_id as fb_page_id", "facebook_rx_fb_user_info.name as account_name","count(messenger_bot_domain_whitelist.id) as count"), $join, $limit, $start, $order_by,$group_by);

        $total_rows_array = $this->basic->count_row($table, $where, $count="messenger_bot_domain_whitelist.id",$join,$group_by);      
        $total_result = $total_rows_array[0]['total_rows'];

        $i=0;
        $base_url=base_url();
        foreach ($info as $key => $value) {

            $info[$i]["action"] = "<a href='#' style='cursor:pointer' class='btn btn-circle btn-outline-info domain_list' title='".$this->lang->line("Domain List")."' data-account-name='".$value['account_name']."' data-page-name='".$value['page_name']."' data-page='".$value['page_table_id']."'><i class='fa fa-eye'></i></a>";
            $info[$i]["page_name"] = "<a target='_BLANK' href='https://facebook.com/".$value['fb_page_id']."'>".$value['page_name']."</i></a>";
            $i++;

        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);

    }

    public function domain_details()
    {
        $this->ajax_check();
        $page_id = $this->input->post("page_id");
        $searching = $this->input->post('searching');
        $display_columns = array("#", 'domain', 'created_at', 'actions');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'messenger_bot_domain_whitelist.id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;


        $table_name = "messenger_bot_domain_whitelist";
        $where_simple['user_id'] = $this->user_id;
        $where_simple['page_id'] = $page_id;
        if($searching != '')
        $where_simple['domain like'] = '%'.$searching.'%';

        $where['where'] = $where_simple;
        $info = $this->basic->get_data($table_name,$where,'','',$limit,$start,$order_by);

        $total_rows_array=$this->basic->count_row('messenger_bot_domain_whitelist',$where,"messenger_bot_domain_whitelist.id");
        $total_result=$total_rows_array[0]['total_rows'];

        foreach ($info as $key => $one_user) 
        {
            $btn_id=$one_user['id'];
            $delete_btn= "<a href='#' class='btn btn-circle btn-outline-danger delete_domain'title='".$this->lang->line("delete")."' id='domain-".$btn_id."' data-id='".$btn_id."'><i class='fa fa-trash'></i></a>";       
            
            $info[$key]['actions'] = $delete_btn;
            $info[$key]['domain'] = "<a target='_BLANK' href='".$one_user['domain']."'>".$one_user['domain']."</a>";
            $info[$key]['created_at'] = date("jS M, y H:i:s",strtotime($one_user['created_at']));
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
        
    }

    public function delete_domain()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        
        $this->ajax_check();
        $domain_id=$this->input->post('domain_id');
        if($this->basic->delete_data('messenger_bot_domain_whitelist',array('id'=>$domain_id,'user_id'=>$this->user_id))) echo "1";
        else echo "0";
    }

    public function delete_bot()
    {
        if(!$_POST) exit();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        $id=$this->input->post("id");
        $bot_posback_ids = $this->basic->get_data('messenger_bot',array('where'=>array('id'=>$id)));
        $postback_id = array();
        if($bot_posback_ids[0]['keyword_type'] == 'post-back')
        {
            $postback_id = explode(',', $bot_posback_ids[0]['postback_id']);
        }

        $this->db->trans_start();
        $this->basic->delete_data("messenger_bot",array("id"=>$id,"user_id"=>$this->user_id));
        
        if(!empty($postback_id))
        {            
            $this->db->where_in("postback_id", $postback_id);
            $this->db->update('messenger_bot_postback', array('use_status' => '0'));
        }      
        $this->db->trans_complete();
        if($this->db->trans_status() === false)
            echo '0';
        else
            echo '1';
    }

    public function add_domain()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
            exit();        
        $this->ajax_check();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $page_id=$this->input->post('page_id');
        $domain_name=strip_tags($this->input->post('domain_name'));
        $userdata=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $facebook_rx_fb_user_info_id=isset($userdata[0]['facebook_rx_fb_user_info_id']) ? $userdata[0]['facebook_rx_fb_user_info_id'] : "";
        $page_access_token=isset($userdata[0]['page_access_token']) ? $userdata[0]['page_access_token'] : "";

        if(!$this->basic->is_exist('messenger_bot_domain_whitelist',array('page_id'=>$page_id,'domain'=>$domain_name,'user_id'=>$this->user_id)))
        {
            $this->basic->insert_data('messenger_bot_domain_whitelist',array('page_id'=>$page_id,'domain'=>$domain_name,"created_at"=>date("Y-m-d H:i:s"),"messenger_bot_user_info_id"=>$facebook_rx_fb_user_info_id,"user_id"=>$this->user_id));
            $this->load->library("fb_rx_login"); 
            $response=array();
            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$domain_name);
        }
        else {
            
             $this->load->library("fb_rx_login"); 
            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$domain_name);
            $response=array('status'=>'1','result'=>$this->lang->line("Successfully updated whitelisted domains"));
        }

        echo json_encode($response);
       
    }
   //=============================DOMAIN WHITELIST================================

    
    public function delete_error_log($id=0)
    {  
        if($id == 0) exit();      
        $this->basic->delete_data("messenger_bot_reply_error_log",array("id"=>$id));
        redirect(base_url('messenger_bot/bot_list'),'location');
    }

    public function error_log_report()
    {
        $this->ajax_check();
        $user_id = $this->user_id;
        $page_table_id = $this->input->post('table_id');     
        $error_search = $this->input->post('error_search');    
        $media_type_error = $this->input->post('media_type_error');    
        $display_columns = array("#", 'bot_name', 'error_message', 'error_time', 'actions'); 

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'messenger_bot_reply_error_log.id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;


        $table_name = "messenger_bot_reply_error_log";
        $select = array("messenger_bot_reply_error_log.*","bot_name");
        $join = array('messenger_bot'=>"messenger_bot_reply_error_log.bot_settings_id=messenger_bot.id,left");  
        $where_simple['messenger_bot_reply_error_log.user_id'] = $user_id;
        $where_simple['messenger_bot_reply_error_log.page_id'] = $page_table_id;
        $where_simple['messenger_bot_reply_error_log.media_type'] = $media_type_error;
        $where['where'] = $where_simple;

        $sql="";
        if($error_search != '')
        {
            $sql = "(messenger_bot.bot_name like '%".$error_search."%' OR messenger_bot_reply_error_log.error_message like '%".$error_search."%')";
            $this->db->where($sql);
        }

        $info = $this->basic->get_data($table_name,$where,$select,$join,$limit,$start,$order_by);   

        if($sql!="") $this->db->where($sql);
        $total_rows_array=$this->basic->count_row('messenger_bot_reply_error_log',$where,"messenger_bot_reply_error_log.id",$join);
        $total_result=$total_rows_array[0]['total_rows'];   

        foreach ($info as $key=>$error_info) 
        {
            $action_button = "<div style='min-width:90px'><a class='btn btn-circle btn-outline-warning' data-toggle='tooltip' title='".$this->lang->line("Edit Bot")."' href='".base_url('messenger_bot/edit_bot_settings_from_error_log/').$error_info['bot_settings_id']."/0/errlog'> <i class='fa fa-edit'></i></a>&nbsp;<a class='btn btn-circle btn-outline-danger' data-toggle='tooltip' title='".$this->lang->line("Delete Log")."' href=".base_url('messenger_bot/delete_error_log/').$error_info['id']."> <i class='fa fa-trash'></i></a></div>
                              <script>
                $('[data-toggle=\"tooltip\"]').tooltip();
              </script>";
            $info[$key]['actions'] = $action_button;
            $info[$key]['bot_name'] = $error_info['bot_name'];
            $info[$key]['error_message'] = $error_info['error_message'];
            $info[$key]['error_time'] = date("jS M, y H:i:s",strtotime($error_info['error_time']));
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
        
    }


    public function error_log_report_X()
    {
        if(empty($_POST['table_id'])) {
            die();
        }
        $user_id = $this->user_id;
        $page_table_id = $this->input->post('table_id');        
        $table_name = "messenger_bot_reply_error_log";
        $select=array("messenger_bot_reply_error_log.*","bot_name");
        $join = array('messenger_bot'=>"messenger_bot_reply_error_log.bot_settings_id=messenger_bot.id,left");   
        $where['where'] = array('messenger_bot_reply_error_log.user_id' => $user_id, 'messenger_bot_reply_error_log.page_id' => $page_table_id);
        $error_log_report_info = $this->basic->get_data($table_name,$where,$select,$join);       
        $html = '<script>
                    $(document).ready(function() {
                        $("#error_log_datatable").DataTable();
                    }); 
                 </script>
                 <style>
                    .dataTables_filter
                     {
                        float : right;
                     }
                 </style>';
        $html .= "
            <table id='error_log_datatable' class='table table-striped table-bordered' cellspacing='0' width='100%''>
            <thead>
                <tr>
                    <th>".$this->lang->line("Bot Name")."</th>
                    <th>".$this->lang->line("Error Message")."</th>
                    <th class='text-center'>".$this->lang->line("Error Time")."</th>
                    <th class='text-center'>".$this->lang->line("Actions")."</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($error_log_report_info as $error_info) 
        {
            $html .= "<tr>
                        <td>".$error_info['bot_name']."</td>
                        <td>".$error_info['error_message']."</td>
                        <td class='text-center'>".date("jS M, y H:i:s",strtotime($error_info['error_time']))."</td>
                        <td class='text-center'>
                              <a class='btn btn-outline-warning' href='".base_url('messenger_bot/edit_bot/').$error_info['bot_settings_id']."/0/errlog'> <i class='fa fa-edit'></i> ".$this->lang->line("Edit Bot")."</a> 
                              <a class='btn btn-outline-danger' href=".base_url('messenger_bot/delete_error_log/').$error_info['id']."> <i class='fa fa-trash'></i> ".$this->lang->line("Delete Log")."</a> 
                             
                        </td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>
                </table>
                ";
        echo $html;
    }
    
    public function remove_persistent_menu_locale($auto_id=0,$page_auto_id=0,$iframe=0)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        $this->basic->delete_data("messenger_bot_persistent_menu",array("id"=>$auto_id,"user_id"=>$this->user_id));
        $this->session->set_flashdata('remove_persistent_menu_locale',1);
        redirect(base_url('messenger_bot/persistent_menu_list/'.$page_auto_id.'/'.$iframe),'location');    
    } 

    public function remove_persistent_menu($page_auto_id=0,$iframe=0)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        $this->load->library("fb_rx_login"); 
        $page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();
        $page_access_token=$page_info[0]['page_access_token'];
        $response=$this->fb_rx_login->delete_persistent_menu($page_access_token);
        if(!isset($response['error']))
        {
            $this->basic->update_data('facebook_rx_fb_page_info',array("id"=>$page_auto_id,'user_id'=>$this->user_id),array("persistent_enabled"=>'0'));
            $this->basic->delete_data('messenger_bot_persistent_menu',array("page_id"=>$page_auto_id,'user_id'=>$this->user_id));
            $this->session->set_flashdata('perrem_success',1);
            $this->_delete_usage_log($module_id=197,$request=1);
        }
        else
        {
            $err_message=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
            
            $this->session->set_flashdata('perrem_success',0);
            $this->session->set_flashdata('perrem_message',$err_message);
        }
        redirect(base_url("messenger_bot/persistent_menu_list/$page_auto_id/$iframe"),'location');
    } 

    public function publish_persistent_menu($page_auto_id=0,$iframe=0)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        $page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();
        $page_access_token=$page_info[0]['page_access_token'];
        $is_already_persistent_enabled=$page_info[0]['persistent_enabled'];
        if($is_already_persistent_enabled=='0') // no need to check if it was already published and user is just editing menu
        {
            $status=$this->_check_usage($module_id=197,$request=1);
            if($status=="3") 
            {
                $this->session->set_flashdata('per_success',0);
                $this->session->set_flashdata('per_message',$this->lang->line("You are not allowed to publish new persistent menu. Module limit has been exceeded.")); 
                $this->_insert_usage_log($module_id=197,$request=1);   
                redirect(base_url('messenger_bot/persistent_menu_list/'.$page_auto_id.'/'.$iframe),'location'); 
            }
        }
        $this->load->library("fb_rx_login"); 
        $json_array=array();
        $menu_data=$this->basic->get_data("messenger_bot_persistent_menu",array("where"=>array("page_id"=>$page_auto_id,"user_id"=>$this->user_id)));
        foreach ($menu_data as $key => $value) 
        {
            $temp=json_decode($value["item_json"],true);
            $temp2=isset($temp['call_to_actions']) ? $temp['call_to_actions'] : array();
          
            if($this->session->userdata('user_type') == 'Member' && in_array(198,$this->module_access) && count($temp2)<3)
            {
                end($temp2);        
                $key2 = key($temp2); 
                $key2++;
                $copyright_text=$this->config->item("persistent_menu_copyright_text");
                if($copyright_text=="") $copyright_text=$this->config->item("product_name");
                $copyright_url=$this->config->item("persistent_menu_copyright_url");
                if($copyright_url=="") $copyright_url=base_url();
                $temp["call_to_actions"][$key2]["title"]=$copyright_text;
                $temp["call_to_actions"][$key2]["type"]="web_url";
                $temp["call_to_actions"][$key2]["url"]=$copyright_url;
            }
            $json_array["persistent_menu"][]=$temp;
        }
        
        $json=json_encode($json_array);
      
        $response=$this->fb_rx_login->add_persistent_menu($page_access_token,$json);
        
        if(!isset($response['error']))
        {
            if(!empty($postback_insert_data))
            $this->db->insert_batch('messenger_bot_postback',$postback_insert_data);
            $this->basic->update_data('facebook_rx_fb_page_info',array("id"=>$page_auto_id,'user_id'=>$this->user_id),array("persistent_enabled"=>'1'));
            $this->session->set_flashdata('menu_success',1); 
            if($is_already_persistent_enabled=='0') // no need to check if it was already published and user is just editing menu
            $this->_insert_usage_log($module_id=197,$request=1);   
            redirect(base_url('messenger_bot/persistent_menu_list/'.$page_auto_id.'/'.$iframe),'location');        
        }
        else
        {
            $err_message=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
            $this->session->set_flashdata('per_success',0);
            $this->session->set_flashdata('per_message',$err_message); 
            redirect(base_url('messenger_bot/persistent_menu_list/'.$page_auto_id.'/'.$iframe),'location');       
        }         
    }

    public function persistent_menu_list($page_auto_id=0,$iframe='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        $data['body'] = 'messenger_tools/persistent_menu_list';
        $data['page_title'] = $this->lang->line('Persistent Menu List');  
        $page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();
        
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array(); 
        $data["menu_info"]=$this->basic->get_data("messenger_bot_persistent_menu",array("where"=>array("page_id"=>$page_auto_id,"user_id"=>$this->user_id)));
        
        $data['iframe']=$iframe;
        $this->_viewcontroller($data); 
    }

    public function create_persistent_menu($page_auto_id=0,$iframe='0')
    {        
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        $data['body'] = 'messenger_tools/persistent_menu';
        $data['page_title'] = $this->lang->line('Persistent Menu');  
        $page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();
        
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array(); 
        $started_button_enabled = isset($page_info[0]["started_button_enabled"])?$page_info[0]["started_button_enabled"]:"0";
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)));
        $data['postback_ids'] = $postback_id_list;
        $data['page_auto_id'] = $page_auto_id;
        $data['started_button_enabled'] = $started_button_enabled;
        $data['locale']=$this->sdk_locale();
        
        $data['iframe']=$iframe;
        $this->_viewcontroller($data); 
    }

    public function create_persistent_menu_action()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if(!$_POST) exit();
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            if(!is_array($value))
                $temp = strip_tags($value);
            else
                $temp = $value;

            $$key=$temp;
        }
        if($this->basic->is_exist("messenger_bot_persistent_menu",array("page_id"=>$page_table_id,"locale"=>$locale)))
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("persistent menu is already exists for this locale.")));
            exit();
        }
        $menu=array();
        $postback_insert_data=array();
        $only_postback=array();
        for($i=1;$i<=$level1_limit;$i++)
        {
            $level_title_temp="text_with_buttons_text_".$i;
            $level_type_temp="text_with_button_type_".$i;
            if($$level_title_temp=="") continue; // form gets everything but we need only filled data
            if($$level_type_temp=="post_back") $$level_type_temp="postback";
            $menu[$i]=array
            (
                "title"=>$$level_title_temp,
                "type"=> $$level_type_temp
            );
            if($$level_type_temp=="postback")
            {
                $level_postback_temp="text_with_button_post_id_".$i;
                $level_postback_temp_data=isset($$level_postback_temp) ? $$level_postback_temp : '';
                // $$level_postback_temp=strtoupper($$level_postback_temp);
                $menu[$i]["payload"]=$level_postback_temp_data;
                $single_postback_insert_data = array();
                $single_postback_insert_data['user_id'] = $this->user_id;
                $single_postback_insert_data['postback_id'] = $level_postback_temp_data;
                $single_postback_insert_data['page_id'] = $page_table_id;
                $single_postback_insert_data['bot_name'] = '';
                $postback_insert_data[] = $single_postback_insert_data; 
                $only_postback[]=$level_postback_temp_data;
            }
            else if($$level_type_temp=="web_url")
            {
                $level_web_url_temp="text_with_button_web_url_".$i;
                $menu[$i]["url"]=$$level_web_url_temp;
            }
            else
            {
                for($j=1;$j<=$level2_limit;$j++)
                {
                    $level2_title_temp="text_with_buttons_text_".$i."_".$j;
                    $level2_type_temp="text_with_button_type_".$i."_".$j;
                    if($$level2_title_temp=="") continue; // form gets everything but we need only filled data
                    if($$level2_type_temp=="post_back") $$level2_type_temp="postback";
                    $menu[$i]["call_to_actions"][$j]["title"]=$$level2_title_temp;
                    $menu[$i]["call_to_actions"][$j]["type"]=$$level2_type_temp;
                    if($$level2_type_temp=="postback")
                    {
                        $level2_postback_temp="text_with_button_post_id_".$i."_".$j;
                        $level2_postback_temp_data=isset($$level2_postback_temp) ? $$level2_postback_temp : '';
                        // $$level2_postback_temp=strtoupper($$level2_postback_temp);
                        $menu[$i]["call_to_actions"][$j]["payload"]=$level2_postback_temp_data;
                        $single_postback_insert_data = array();
                        $single_postback_insert_data['user_id'] = $this->user_id;
                        $single_postback_insert_data['postback_id'] = $level2_postback_temp_data;
                        $single_postback_insert_data['page_id'] = $page_table_id;
                        $single_postback_insert_data['bot_name'] = '';
                        $postback_insert_data[] = $single_postback_insert_data; 
                        $only_postback[]=$level2_postback_temp_data;
                    }
                    else if($$level2_type_temp=="web_url")
                    {
                        $level2_web_url_temp="text_with_button_web_url_".$i."_".$j;
                        $menu[$i]["call_to_actions"][$j]["url"]=$$level2_web_url_temp;
                    }
                    else
                    {
                        for($k=1;$k<=$level3_limit;$k++)
                        {
                            $level3_title_temp="text_with_buttons_text_".$i."_".$j."_".$k;
                            $level3_type_temp="text_with_button_type_".$i."_".$j."_".$k;
                            if($$level3_title_temp=="") continue; // form gets everything but we need only filled data
                            if($$level3_type_temp=="post_back") $$level3_type_temp="postback";
                            $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["title"]=$$level3_title_temp;
                            $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["type"]=$$level3_type_temp;
                            if($$level3_type_temp=="postback")
                            {
                                $level3_postback_temp="text_with_button_post_id_".$i."_".$j."_".$k;
                                $level3_postback_temp_data=isset($$level3_postback_temp) ? $$level3_postback_temp : '';
                                // $$level3_postback_temp=strtoupper($$level3_postback_temp);
                                $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["payload"]=$level3_postback_temp_data;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $level3_postback_temp_data;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = '';
                                $postback_insert_data[] = $single_postback_insert_data; 
                                $only_postback[]=$level3_postback_temp_data;
                            }
                            else if($$level3_type_temp=="web_url")
                            {
                                $level3_web_url_temp="text_with_button_web_url_".$i."_".$j."_".$k;
                                $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["url"]=$$level3_web_url_temp;
                            }
                        }
                    }
                }
            }
        }
        $menu_json_array=array();
        $menu_json_array["locale"]=$locale;
        $composer_input_disabled2='false';
        if($composer_input_disabled==='1') $composer_input_disabled2='true';
        $menu_json_array["composer_input_disabled"]=$composer_input_disabled2;
        $index=1;
        foreach ($menu as $key => $value) 
        {
           $menu_json_array["call_to_actions"][$index]=$value;
           $index++;
        }
        $menu_json=json_encode($menu_json_array); 
        $insert_data = array();       
        $insert_data['page_id'] = $page_table_id;
        $facebook_rx_fb_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_access_token"));
        $page_access_token = $facebook_rx_fb_user_info_id[0]['page_access_token'];
        $facebook_rx_fb_user_info_id = $facebook_rx_fb_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $this->db->trans_start();
        // if(!empty($postback_insert_data)) $this->db->insert_batch('messenger_bot_postback',$postback_insert_data);
        $this->basic->insert_data("messenger_bot_persistent_menu",array("user_id"=>$this->user_id,"page_id"=>$page_table_id,"locale"=>$locale,"item_json"=>$menu_json,"composer_input_disabled"=>$composer_input_disabled,'poskback_id_json'=>json_encode($only_postback)));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
        else  
        {
            $this->session->set_flashdata('per_success',1);
            echo json_encode(array('status'=>'1','message'=>$this->lang->line("persistent menu has been created successfully.")));
        }      
    }

    public function edit_persistent_menu($id=0,$iframe=0)
    {        
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        $data['body'] = 'messenger_tools/persistent_menu_edit';
        $data['page_title'] = $this->lang->line('Edit Persistent Menu');  
        $xdata=$this->basic->get_data("messenger_bot_persistent_menu",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];
        $page_auto_id=$xdata[0]["page_id"];
        $page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();

        $page_id=$page_auto_id;// database id      
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1','template_for'=>'reply_message')),'','','',$start=NULL,$order_by='template_name ASC');        
        $poption=array();
        foreach ($postback_data as $key => $value) 
        {
            // if($value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply") continue;
            $poption[$value["postback_id"]]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        $data['poption']=$poption;
        
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array(); 
        $started_button_enabled = isset($page_info[0]["started_button_enabled"])?$page_info[0]["started_button_enabled"]:"0";
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)));
        $data['postback_ids'] = $postback_id_list;
        $data['page_auto_id'] = $page_auto_id;
        $data['started_button_enabled'] = $started_button_enabled;
        $data['locale']=$this->sdk_locale();
        
        $data['iframe']=$iframe;
        $this->_viewcontroller($data); 
    }

    public function edit_persistent_menu_action()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if(!$_POST) exit();
        $post=$_POST;

        foreach ($post as $key => $value) 
        {
            if(!is_array($value))
                $temp = strip_tags($value);
            else
                $temp = $value;

            $$key=$temp;
        }
        if($this->basic->is_exist("messenger_bot_persistent_menu",array("page_id"=>$page_table_id,"locale"=>$locale,"id!="=>$auto_id)))
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("persistent menu is already exists for this locale.")));
            exit();
        }
        $menu=array();
        $postback_insert_data=array();
        $only_postback=array();
        $current_postbacks=json_decode($current_postbacks,true);
        $current_postbacks=array_map('strtoupper', $current_postbacks);
        for($i=1;$i<=$level1_limit;$i++)
        {
            $level_title_temp="text_with_buttons_text_".$i;
            $level_type_temp="text_with_button_type_".$i;
            if($$level_title_temp=="") continue; // form gets everything but we need only filled data
            if($$level_type_temp=="post_back") $$level_type_temp="postback";
            $menu[$i]=array
            (
                "title"=>$$level_title_temp,
                "type"=> $$level_type_temp
            );
            if($$level_type_temp=="postback")
            {
                $level_postback_temp="text_with_button_post_id_".$i;
                $level_postback_temp_data=isset($$level_postback_temp) ? $$level_postback_temp : '';
                // $$level_postback_temp=strtoupper($$level_postback_temp);
                $menu[$i]["payload"]=$level_postback_temp_data;
                $single_postback_insert_data = array();
                $single_postback_insert_data['user_id'] = $this->user_id;
                $single_postback_insert_data['postback_id'] = $level_postback_temp_data;
                $single_postback_insert_data['page_id'] = $page_table_id;
                $single_postback_insert_data['bot_name'] = '';
                if(!in_array(strtoupper($level_postback_temp_data), $current_postbacks))
                $postback_insert_data[] = $single_postback_insert_data; 
                $only_postback[]=$level_postback_temp_data;
            }
            else if($$level_type_temp=="web_url")
            {
                $level_web_url_temp="text_with_button_web_url_".$i;
                $menu[$i]["url"]=$$level_web_url_temp;
            }
            else
            {
                for($j=1;$j<=$level2_limit;$j++)
                {
                    $level2_title_temp="text_with_buttons_text_".$i."_".$j;
                    $level2_type_temp="text_with_button_type_".$i."_".$j;
                    if($$level2_title_temp=="") continue; // form gets everything but we need only filled data
                    if($$level2_type_temp=="post_back") $$level2_type_temp="postback";
                    $menu[$i]["call_to_actions"][$j]["title"]=$$level2_title_temp;
                    $menu[$i]["call_to_actions"][$j]["type"]=$$level2_type_temp;
                    if($$level2_type_temp=="postback")
                    {
                        $level2_postback_temp="text_with_button_post_id_".$i."_".$j;
                        $level2_postback_temp_data=isset($$level2_postback_temp) ? $$level2_postback_temp : '';
                        // $$level2_postback_temp=strtoupper($$level2_postback_temp);
                        $menu[$i]["call_to_actions"][$j]["payload"]=$level2_postback_temp_data;
                        $single_postback_insert_data = array();
                        $single_postback_insert_data['user_id'] = $this->user_id;
                        $single_postback_insert_data['postback_id'] = $level2_postback_temp_data;
                        $single_postback_insert_data['page_id'] = $page_table_id;
                        $single_postback_insert_data['bot_name'] = '';
                        if(!in_array(strtoupper($level2_postback_temp_data), $current_postbacks))
                        $postback_insert_data[] = $single_postback_insert_data; 
                        $only_postback[]=$level2_postback_temp_data;
                    }
                    else if($$level2_type_temp=="web_url")
                    {
                        $level2_web_url_temp="text_with_button_web_url_".$i."_".$j;
                        $menu[$i]["call_to_actions"][$j]["url"]=$$level2_web_url_temp;
                    }
                    else
                    {
                        for($k=1;$k<=$level3_limit;$k++)
                        {
                            $level3_title_temp="text_with_buttons_text_".$i."_".$j."_".$k;
                            $level3_type_temp="text_with_button_type_".$i."_".$j."_".$k;
                            if($$level3_title_temp=="") continue; // form gets everything but we need only filled data
                            if($$level3_type_temp=="post_back") $$level3_type_temp="postback";
                            $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["title"]=$$level3_title_temp;
                            $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["type"]=$$level3_type_temp;
                            if($$level3_type_temp=="postback")
                            {
                                $level3_postback_temp="text_with_button_post_id_".$i."_".$j."_".$k;
                                $level3_postback_temp_data=isset($$level3_postback_temp) ? $$level3_postback_temp : '';
                                // $$level3_postback_temp=strtoupper($$level3_postback_temp);
                                $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["payload"]=$level3_postback_temp_data;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $level3_postback_temp_data;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = '';
                                if(!in_array(strtoupper($level3_postback_temp_data), $current_postbacks))
                                $postback_insert_data[] = $single_postback_insert_data; 
                                $only_postback[]=$level3_postback_temp_data;
                            }
                            else if($$level3_type_temp=="web_url")
                            {
                                $level3_web_url_temp="text_with_button_web_url_".$i."_".$j."_".$k;
                                $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["url"]=$$level3_web_url_temp;
                            }
                        }
                    }
                }
            }
        }


        $menu_json_array=array();
        $menu_json_array["locale"]=$locale;
        $composer_input_disabled2='false';
        if($composer_input_disabled==='1') $composer_input_disabled2='true';
        $menu_json_array["composer_input_disabled"]=$composer_input_disabled2;
        $index=1;
        foreach ($menu as $key => $value) 
        {
           $menu_json_array["call_to_actions"][$index]=$value;
           $index++;
        }
        $menu_json=json_encode($menu_json_array); 
        $insert_data = array();       
        $insert_data['page_id'] = $page_table_id;
        $facebook_rx_fb_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_access_token"));
        $page_access_token = $facebook_rx_fb_user_info_id[0]['page_access_token'];
        $facebook_rx_fb_user_info_id = $facebook_rx_fb_user_info_id[0]["facebook_rx_fb_user_info_id"];
        
        $this->db->trans_start();
        // if(!empty($postback_insert_data)) $this->db->insert_batch('messenger_bot_postback',$postback_insert_data);
        $this->basic->update_data("messenger_bot_persistent_menu",array("id"=>$auto_id,"user_id"=>$this->user_id),array("locale"=>$locale,"item_json"=>$menu_json,"composer_input_disabled"=>$composer_input_disabled,'poskback_id_json'=>json_encode($only_postback)));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
        else  
        {
            $this->session->set_flashdata('per_update_success',1);
            echo json_encode(array('status'=>'1','message'=>$this->lang->line("persistent menu has been updated successfully.")));
        }      
    }


    // -------------Tree view data functions-------------------------
    private function set_nest_easy($postback_array=array(),$get_started_level)
    {
        for ($loop_level=$get_started_level-1; $loop_level >=1 ; $loop_level--) 
        { 
            foreach ($postback_array as $key => $value) 
            {
                $level=$value['level'];
                if($level==$loop_level)
                {
                    if(isset($value['child_postback']) && is_array($value['child_postback']))
                    {
                        foreach ($value['child_postback'] as $key2 => $value2) 
                        {
                            $postback_array[$key]['child_postback'][$key2]=$postback_array[$value2];
                        }
                    }
                }

            }
        }

        foreach ($postback_array as $key => $value)
        {
            if($value['level']>1)
            unset($postback_array[$key]); // removing other  unnessary rows so that only nested postback stays 
        }

        return $postback_array;
    }

    private function get_nest($current_level=1,$page_table_id=0)
    {       
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return ""; 
            $current_level_prev=$current_level-1;

            $condition="if(\$tempurl!='') \$templabel='<a class=\"iframed\" href=\"'.\$tempurl.'\">'.\$display.'</a>';
            else \$templabel='<span class=\"text-danger\">'.\$display.'</span>';";

            $output="";
            $output.=" 
            \$get_started_tree.='<ul>'; ";
            $output.="
            // nested post back may have weburl,phone or email child and they are single element without child                
            if(!empty(\$value".$current_level_prev."['web_url'])) // has a web url as child, 0 index consists url
            {              
              foreach(\$value".$current_level_prev."['web_url'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li>
                    <a href=\"'.\$this->tree_security(\$tempuval).'\" data-toggle=\"tooltip\" data-title=\"'.\$this->tree_security(\$tempuval).'\" target=\"_blank\"><i class=\"fas fa-external-link-alt\"></i> Web URL</a>
                </li>';
              }
            }
            if(!empty(\$value".$current_level_prev."['phone_number'])) // has a phone as child
            {
              foreach(\$value".$current_level_prev."['phone_number'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li><i class=\"fas fa-phone-square\"></i> Phone Number</li>';
              }
            }

            if(!empty(\$value".$current_level_prev."['email'])) // has a email as child
            {
              foreach(\$value".$current_level_prev."['email'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li> <i class=\"far fa-envelope-open\"></i> Email</li>';
              }
            }

            if(!empty(\$value".$current_level_prev."['location'])) // has a location as child
            {
              foreach(\$value".$current_level_prev."['location'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li><i class=\"fas fa-map-marker-alt\"></i> Location</li>';
              }
            }

            if(!empty(\$value".$current_level_prev."['call_us'])) // has a call_us as child
            {
              foreach(\$value".$current_level_prev."['call_us'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li><span data-toggle=\"tooltip\" title=\"'.\$this->tree_security(\$tempuval).'\"><i class=\"fas fa-headset\"></i> Call Us</span></li>';
              }
            }

            if(!empty(\$value".$current_level_prev."['birthdate'])) // has a birthdate webview as child
            {
              foreach(\$value".$current_level_prev."['birthdate'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li><i class=\"fas fa-birthday-cake\"></i> Birthdate</li>';
              }
            }

            if(!empty(\$value".$current_level_prev."['webview'])) // has a web view as child, 0 index consists url
            {              
              foreach(\$value".$current_level_prev."['webview'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li>
                    <a href=\"'.\$this->tree_security(\$tempuval).'\" data-toggle=\"tooltip\" data-title=\"'.\$this->tree_security(\$tempuval).'\" target=\"_blank\"><i class=\"fab fa-wpforms\"></i> Webview</a>
                </li>';
              }
            }

            if(isset(\$value".$current_level_prev."['child_postback']))
            foreach (\$value".$current_level_prev."['child_postback'] as \$key".$current_level." => \$value".$current_level.")
            {                                    
                if(is_array(\$value".$current_level.")) // if have new child that does not appear in parent tree
                {
                    \$tempid=isset(\$value".$current_level."['id'])?\$value".$current_level."['id']:0;
                    \$tempis_template=isset(\$value".$current_level."['is_template'])?\$value".$current_level."['is_template']:'';
                    \$tempostbackid=isset(\$value".$current_level."['postback_id'])?\$this->tree_security(\$value".$current_level."['postback_id']):'';
                    \$tempbotname=isset(\$value".$current_level."['bot_name'])?\$this->tree_security(\$value".$current_level."['bot_name']):'';
                    
                    if(\$tempis_template=='1') \$tempurl=base_url('messenger_bot/edit_template/'.\$tempid.'/1'); // it is template
                    else if(\$tempis_template=='0') \$tempurl=base_url('messenger_bot/edit_bot/'.\$tempid.'/1'); // it is bot
                    else \$tempurl='';  

                    if(\$tempurl!='')
                    {
                        if(\$tempbotname!='') \$display='<span class=\"text-info\" data-toggle=\"tooltip\" title=\"'.\$tempostbackid.' : click to edit settings\"><i class=\"far fa-hand-pointer\"></i> '.\$tempbotname.'</span>';
                        else \$display='<span class=\"text-info\"><i class=\"far fa-hand-pointer\"></i> '.\$tempostbackid.'</span>';
                    }
                    else  // orphan postback
                    {
                        \$create_child_postback_url = base_url('messenger_bot/create_new_template/1/'.\$page_table_id.'/'.urlencode(\$tempostbackid));
                        \$display='<a class=\"iframed text-danger\" href=\"'.\$create_child_postback_url.'\" data-toggle=\"tooltip\" title=\"'.\$tempostbackid.' is an empty child postback, click to set reply\"><i class=\"fas fa-exclamation-triangle\"></i> '.\$tempostbackid.'</a>';
                    }   
                    
                    ".$condition."

                    \$get_started_tree.= '
                    <li>'.\$templabel;
                } 
                else // child already appear in parent tree
                {                    
                    if(isset(\$linear_postback_array[\$value".$current_level."])) 
                    {
                        \$tempid=isset(\$linear_postback_array[\$value".$current_level."]['id'])?\$linear_postback_array[\$value".$current_level."]['id']:0;
                        \$tempis_template=isset(\$linear_postback_array[\$value".$current_level."]['is_template'])?\$linear_postback_array[\$value".$current_level."]['is_template']:'';
                        \$tempostbackid=isset(\$linear_postback_array[\$value".$current_level."]['postback_id'])?\$this->tree_security(\$linear_postback_array[\$value".$current_level."]['postback_id']):'';
                        \$tempbotname=isset(\$linear_postback_array[\$value".$current_level."]['bot_name'])?\$this->tree_security(\$linear_postback_array[\$value".$current_level."]['bot_name']):'';

                        if(\$tempis_template=='1') \$tempurl=base_url('messenger_bot/edit_template/'.\$tempid.'/1'); // it is template
                        else if(\$tempis_template=='0') \$tempurl=base_url('messenger_bot/edit_bot/'.\$tempid.'/1'); // it is bot
                        else \$tempurl='';

                        if(\$tempbotname!='') \$display='<span class=\"text-muted\" data-toggle=\"tooltip\" title=\"'.\$tempostbackid.' is already exist in the tree\"><i class=\"fas fa-redo\"></i> '.\$tempbotname.'</span>';
                        else \$display='<span class=\"text-muted\" data-toggle=\"tooltip\" title=\"'.\$tempostbackid.' is already exist in the tree\"><i class=\"fas fa-redo\"></i> '.\$tempostbackid.'</span>';
                        
                         ".$condition."

                        \$get_started_tree.= '
                        <li>'.\$templabel;
                    }
                
                }";

        return $output;
    }

    

    private function get_child_info($messenger_bot_info,$page_table_id)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return array(); 
        foreach ($messenger_bot_info as $info) 
        {

            $message= $info['message'];
            $keyword_bot_id= $info['id'];
            $keywrods_list= $info['keywords'];
            $template_type=$info['template_type'];
            $this->postback_info[$keyword_bot_id]['keywrods_list']=$keywrods_list;


            /** Get all postback button id from json message **/

            $button_information= $this->get_button_information_from_json($message,$template_type);
            $matches[1]=isset($button_information['postback']) ? $button_information['postback'] : array();
            
            $web_url=isset($button_information['web_url']) ? $button_information['web_url'] : array();
            $webview=isset($button_information['webview']) ? $button_information['webview'] : array();
            $phone_number=isset($button_information['phone_number']) ? $button_information['phone_number'] : array();
            $email=isset($button_information['email']) ? $button_information['email'] : array();
            $location=isset($button_information['location']) ? $button_information['location'] : array();
            $call_us=isset($button_information['call_us']) ? $button_information['call_us'] : array();
            $birthdate=isset($button_information['birthdate']) ? $button_information['birthdate'] : array();


            $k=0;
            $level=0;

            do
            {

                $level++;
                $this->get_postback_info($matches[1],$page_table_id,$keyword_bot_id,$level);

                $matches=array();

                if(!isset($this->postback_info[$keyword_bot_id]['postback_info'])) break;

                foreach ($this->postback_info[$keyword_bot_id]['postback_info'] as $p_info) {

                    $child=$p_info['child_postback'];

                    if(empty($child)) continue;

                    foreach ($child as $child_postback) {
                        if(!isset($this->postback_info[$keyword_bot_id]['postback_info'][$child_postback])) 
                            $matches[1][]=$child_postback;
                    }
                    
                }

                 $k++;

                if($k==100) break;


            }
            while(!empty($matches[1])); 

            $this->postback_info[$keyword_bot_id]['web_url']= $web_url;
            $this->postback_info[$keyword_bot_id]['webview']= $webview;
            $this->postback_info[$keyword_bot_id]['phone_number']= $phone_number;
            $this->postback_info[$keyword_bot_id]['email']= $email;
            $this->postback_info[$keyword_bot_id]['location']= $location;
            $this->postback_info[$keyword_bot_id]['call_us']= $call_us;

        }
    
        return $this->postback_info;

    }

    private function get_postback_info($matches,$page_table_id,$keyword_bot_id,$level)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return array();     
        foreach ($matches as $postback_match) 
        {

            $where['where'] = array('page_id'=> $page_table_id,'postback_id' =>$postback_match);
            /**Get BOT settings information from messenger_bot table as base table. **/
            $messenger_postback_info = $this->basic->get_data("messenger_bot",$where);

            $message= isset($messenger_postback_info[0]['message']) ? $messenger_postback_info[0]['message'] :"" ;

            $id= isset($messenger_postback_info[0]['id']) ? $messenger_postback_info[0]['id']:"";
            $is_template= isset($messenger_postback_info[0]['is_template']) ? $messenger_postback_info[0]['is_template']:"";
            $template_type= isset($messenger_postback_info[0]['template_type']) ? $messenger_postback_info[0]['template_type']:"";
            $bot_name= isset($messenger_postback_info[0]['bot_name']) ? $messenger_postback_info[0]['bot_name']:"";


            if($is_template=='1'){
                $postback_id_info=$this->basic->get_data('messenger_bot_postback',array('where'=>array('messenger_bot_table_id'=>$id,'is_template'=>'1')));
                $id= isset($postback_id_info[0]['id']) ? $postback_id_info[0]['id']:"";
            }          

            

            preg_match_all('#payload":"(.*?)"#si', $message, $matches);

            $button_information= $this->get_button_information_from_json($message,$template_type);
            $matches[1]=isset($button_information['postback']) ? $button_information['postback'] : array();

            $web_url= isset($button_information['web_url']) ? $button_information['web_url'] : array();
            $webview=isset($button_information['webview']) ? $button_information['webview'] : array();
            $phone_number=isset($button_information['phone_number']) ? $button_information['phone_number'] : array();
            $email=isset($button_information['email']) ? $button_information['email'] : array();
            $location=isset($button_information['location']) ? $button_information['location'] : array();
            $call_us=isset($button_information['call_us']) ? $button_information['call_us'] : array();
            $birthdate=isset($button_information['birthdate']) ? $button_information['birthdate'] : array();
        
            $this->postback_info[$keyword_bot_id]['postback_info'][$postback_match] = array("id"=>$id,"child_postback"=>$matches[1],'postback_id'=>$postback_match,"level"=>$level,'is_template'=>$is_template,"web_url"=>$web_url,"webview"=>$webview,
                "phone_number" =>$phone_number,
                "email"     =>$email,
                "location"  =>$location,
                'bot_name'  =>$bot_name,
                'call_us'   =>$call_us,
                'birthdate' =>$birthdate
                );
        }

        return $this->postback_info;
    }


    private function get_button_information_from_json($json_message,$template_type)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return array();

        $full_message_array = json_decode($json_message,true);
        $result = array();

        if(!isset($full_message_array[1]))
        {
          $full_message_array[1] = $full_message_array;
          $full_message_array[1]['message']['template_type'] = $template_type;
        }


        for($k=1;$k<=6;$k++)
        { 

          $full_message[$k] = isset($full_message_array[$k]['message']) ? $full_message_array[$k]['message'] : array();

          if(isset($full_message[$k]["template_type"]))
            $full_message[$k]["template_type"] = str_replace('_', ' ', $full_message[$k]["template_type"]);  

          for ($i=1; $i <=11 ; $i++) 
          {

            if(isset($full_message[$k]['quick_replies'][$i-1]['payload']))
              $result['postback'][] = (isset($full_message[$k]['quick_replies'][$i-1]['payload'])) ? $full_message[$k]['quick_replies'][$i-1]['payload']:"";

            else if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_phone_number')
              $result['phone_number'][] = "user_phone_number";

            else if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_email')
              $result['email'][] = "user_email";

            else if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'location')
              $result['location'][] = "location";


            else if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback')
              $result['postback'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']:"";


            else if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio']) && strpos($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'], 'webview_builder/get_birthdate') !==false)
              $result['birthdate'][] = "user_birthdate";


              else if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio']))
              $result['webview'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'])) ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'] : "";

            
            else if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url']) && !isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio']))
              $result['web_url'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'])) ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'] : "";

           

            else if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'phone_number')
              $result['call_us'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'phone_number') ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload'] : "";
          }


          for ($j=1; $j <=5 ; $j++)
          {
            for ($i=1; $i <=3 ; $i++)
            {
              if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback')
                $result['postback'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']:"";

              else if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio']) && strpos($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'], 'webview_builder/get_birthdate') !==false)
              $result['birthdate'][] = "user_birthdate";


             else if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio']))
                $result['webview'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'])) ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] : "";

              else if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url']) && !isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio']))
                $result['web_url'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'])) ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] : "";

            


              else if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'phone_number')
                $result['call_us'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'phone_number') ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] : "";
            }
          }

        }

        return $result;
    }
    // -------------Tree view data functions-------------------------



    public function bot_menu_section()
    {
        $this->is_engagement_exist=$this->engagement_exist();
        $data['body'] = 'messenger_tools/menu_block';
        $data['page_title'] = $this->lang->line('Messenger Bot');
        $this->_viewcontroller($data);
    }
    


    public function update_subscriber_last_interaction($subscriber_id='',$time='',$subsciber_broadcast_unavailable='0'){

        //if config is disabled for subscriber last interaction to reduce mysql query execute 
        $is_enable=$this->config->item('enable_tracking_subscribers_last_interaction');
        
        if($is_enable == "no")
            return true; 

        $unixtime=strtotime($time);
        date_default_timezone_set('UTC');
        $utc_time= date("Y-m-d H:i:s",$unixtime);

        //update Subscriber information
        $update_data=array();
        $update_data['last_subscriber_interaction_time']=$utc_time;
        if($subsciber_broadcast_unavailable=="1")
             $update_data['unavailable']="0";
        $this->basic->update_data('messenger_bot_subscriber',$where_array=array("subscribe_id"=>$subscriber_id),$update_data); 

    }

    public function get_json_code()
    {
        $this->ajax_check();
        $table_id = $this->input->post('table_id');
        $postback_info = $this->basic->get_data('messenger_bot_postback',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)),array('template_jsoncode'));
        if(empty($postback_info))
        {
            $error_message = '
                        <div class="card" id="nodata">
                          <div class="card-body">
                            <div class="empty-state">
                              <img class="img-fluid" style="height: 200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                              <h2 class="mt-0">'.$this->lang->line("We could not find any data.").'</h2>
                            </div>
                          </div>
                        </div>';
            echo $error_message;
        }
        else
        {     
            $json_info = json_decode($postback_info[0]['template_jsoncode'],true);

            $content='<div class="row">
                    <div class="col-12"> <div class="alert alert-light alert-has-icon">
                                    <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                                    <div class="alert-body">
                                      <div class="alert-title">'.$this->lang->line("Usage").'</div>
                                      '.$this->lang->line("JSON Code can be used for Facebook click to Messenger ads. For ads avoid putting first name, last name, direct link of webivew form, direct link of ecommerce store. These will not work. Instead use postback button or quick reply button to start the main conversation with JSON ads.").'
                                    </div>
                                  </div>';
            $i=1;
            foreach($json_info as $value)
            {
                $json_value['message'] = $value['message'];
                unset($json_value['message']['typing_on_settings']);
                unset($json_value['message']['delay_in_reply']);
                unset($json_value['message']['template_type']);
                $content .= '
                            <div class="card">
                              <div class="card-header">
                                <h4>Reply '.$i.'</h4>
                              </div>
                              <div class="card-body">
                                <pre class="language-javascript">
                                    <code class="dlanguage-javascript copy_code">
'.json_encode($json_value).'
                                    </code>
                                </pre>
                              </div>
                            </div>';
                $i++;
            }

                        


            $content .='</div>
                </div>
                <script>
                    $(document).ready(function() {
                        Prism.highlightAll();
                        $(".toolbar-item").find("a").addClass("copy");

                        $(document).on("click", ".copy", function(event) {
                            event.preventDefault();

                            $(this).html("'.$this->lang->line('Copied!').'");
                            var that = $(this);
                            
                            var text = $(this).prev("code").text();
                            var temp = $("<input>");
                            $("body").append(temp);
                            temp.val(text).select();
                            document.execCommand("copy");
                            temp.remove();

                            setTimeout(function(){
                              $(that).html("'.$this->lang->line('Copy').'");
                            }, 2000); 

                        });
                    });
                </script>
                ';
            echo $content;
        }

    }


        /* 
        ===============================================
        MESSENGER BOT EXPORT IMPORT
        ***********************************************
        */
        // public function saved_templates()
        // {
        //     if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access))  redirect('home/login_page', 'location');

        //     $data['body'] = "messenger_tools/saved_templates";
        //     $data['page_title'] = $this->lang->line("My Saved Templates");
        //     $this->_viewcontroller($data);
        // }

        // ZILANI TEMPLATE UPLOAD STARTED
        public function saved_templates($media_type='fb')
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access))  redirect('home/login_page', 'location');


            if($this->session->userdata('selected_global_media_type')) {
              $media_type = $this->session->userdata('selected_global_media_type');
            }
          
            $data['media_type'] = $media_type;
            $data['body'] = "messenger_tools/saved_templates";
            $data['page_title'] = $media_type=="fb" ? $this->lang->line("Facebook Saved Templates"):$this->lang->line("Instagram Saved Templates");
            $data['header_icon'] = $media_type=="fb" ? "<i class='fab fa-facebook-square'></i>":"<i class='fab fa-instagram'></i>";
            $data['package_list'] = $this->package_list();

            $category_id = isset($_GET['category']) ? $_GET['category']:"";

            $table_name = "facebook_rx_fb_page_info";
            $where['where'] = array('bot_enabled' => "1",'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id'=> $this->session->userdata('facebook_rx_fb_user_info'));
            $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");  
            if($media_type == 'ig')
                $this->db->where('has_instagram','1'); 
            $page_info = $this->basic->get_data($table_name,$where,array("facebook_rx_fb_page_info.id","facebook_rx_fb_page_info.page_name","facebook_rx_fb_page_info.insta_username","facebook_rx_fb_user_info.name as account_name"),$join,'','','page_name asc');
            $data['page_lists'] = $page_info;

            // category lists
            $current_userid = $this->user_id;
            
            $get_admin = $this->basic->get_data('users',['where'=>['user_type'=>'Admin']],['id']);
            $admin_ids = array();
            foreach ($get_admin as $admin) {
                array_push($admin_ids,$admin['id']);
            }
            array_push($admin_ids,$current_userid);
            $admin_ids = array_unique($admin_ids);

            if($this->session->userdata('user_type') != "Admin"){
                $this->db->where_in('user_id',$admin_ids);
            }

            $data['category_list'] = $this->basic->get_data("messenger_bot_template_category");

            $per_page = 15;
            if ($this->session->userdata("user_type")=="Member") 
            {
                $package_info=$this->session->userdata('package_info');
                $search_package_id=isset($package_info['id'])?$package_info['id']:'0';
                
                $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public') OR (template_access='private' AND user_id='".$this->user_id."'))";
                $this->db->where( $where_custom );
            }
            else {
                $this->db->where('user_id',$this->user_id);
            }

            if($category_id != '') {
                $this->db->where('template_category_id',$category_id);
            }

            $this->db->where('media_type',$media_type);
            $templates = $this->basic->get_data('messenger_bot_saved_templates');
            

            /* set cinfiguration for pagination */
            // $base_url = base_url('messenger_bot/saved_templates/{$media_type}');
            $config = array(
                'uri_segment' => 4,
                'base_url' => base_url("messenger_bot/saved_templates/{$media_type}"),
                'total_rows' => count($templates),
                'per_page' => $per_page,

                'full_tag_open' => '<ul class="pagination">',
                'full_tag_close' => '</ul>',

                'first_link' => $this->lang->line('First Page'),
                'first_tag_open' => '<li class="page-item">',
                'first_tag_close' => '</li>',

                'last_link' => $this->lang->line('Last Page'),
                'last_tag_open' => '<li class="page-item">',
                'last_tag_close' => '</li>',

                'next_link' => $this->lang->line('Next'),
                'next_tag_open' => '<li class="page-item">',
                'next_tag_close' => '</li>',

                'prev_link' => $this->lang->line('Previous'),
                'prev_tag_open' => '<li class="page-item">',
                'prev_tag_close' => '</li>',

                'cur_tag_open' => '<li class="page-item active"><a class="page-link">',
                'cur_tag_close' => '</a></li>',

                'num_tag_open' => '<li class="page-item">',
                'num_tag_close' => '</li>',
                'attributes' => array('class' => 'page-link')
            );
            $this->pagination->initialize($config);
            $page_links = $this->pagination->create_links();

            $start = $this->uri->segment(4);
            $limit = $config['per_page'];

            if ($this->session->userdata("user_type")=="Member") {
                $package_info=$this->session->userdata('package_info');
                $search_package_id=isset($package_info['id'])?$package_info['id']:'0';
                
                $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public') OR (template_access='private' AND user_id='".$this->user_id."'))";
                $this->db->where( $where_custom );
            }
            else {
                $this->db->where('user_id',$this->user_id);
            }

            if($category_id != '') {
                $this->db->where('template_category_id',$category_id);
            }

            $table = "messenger_bot_saved_templates";
            $this->db->where('media_type',$media_type);
            $info = $this->basic->get_data($table,'','', '', $limit, $start);

            for ($i=0; $i < count($info) ; $i++) { 
                if($info[$i]['template_category_id'] > 0) {
                    $get_category_name = $this->basic->get_data("messenger_bot_template_category",['where'=>['id'=>$info[$i]['template_category_id']]],['category_name']);
                    $info[$i]['category_name'] = $get_category_name[0]['category_name'];
                } else {
                    $info[$i]['category_name'] = $this->lang->line("No category");

                }
            }
            $data['template_lists'] = $info;
            $data['page_links'] = $page_links;

            $this->_viewcontroller($data);
        }

        public function add_template_Category()
        {
            $this->ajax_check();
            $category_name = trim(strip_tags($this->input->post("category_name",true)));
            $in_data = array(
                'user_id' => $this->user_id,
                'category_name' => $category_name
            );

            $is_exists = $this->basic->get_data("messenger_bot_template_category",['where'=>["user_id"=>$this->user_id,"category_name"=>$category_name]]);

            if(isset($is_exists[0]))
            {
                $insert_id = $is_exists[0]['id'];
                $category_name = $is_exists[0]['type'];

            } else {

                $this->basic->insert_data("messenger_bot_template_category", $in_data);
                $insert_id = $this->db->insert_id();   
            }

            echo json_encode(array('id'=>$insert_id,"text"=>$category_name));
        }

        public function save_messenger_template_info()
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();

            if(!$_POST) exit();
            // echo "<pre>"; print_r($_POST); exit;

            $template_name=strip_tags($this->input->post('template_name'));
            $template_description=strip_tags($this->input->post('template_description'));
            $template_preview_image=$this->input->post('template_preview_image');
            $template_access=$this->input->post('template_access');
            $allowed_package_ids=$this->input->post('allowed_package_ids');
            $bot_category = $this->input->post('bot_category');
            $json_file = $this->input->post('json_upload_input');

            $template_preview_image=str_replace(base_url('upload/image/'.$this->user_id.'/'), '', $template_preview_image);

            if(!is_array($allowed_package_ids) || $template_access=='private')  $allowed_package_ids=array();

            if($this->session->userdata('user_type') != 'Admin') $template_access='private';
            $get_saved_data = '';

            if($json_file != '' && file_exists('upload/'.$json_file)) {
                $filename='upload/'.$json_file;
                $get_saved_data = file_get_contents($filename);
            }

            $savedata=json_decode($get_saved_data,true);     
            $media_type=$savedata['media_type'] ?? "fb";


            $insertData = [];
            $insertData['template_name'] = $template_name;
            $insertData['description'] = $template_description;
            $insertData['savedata'] = $get_saved_data;
            $insertData['saved_at'] = date("Y-m-d H:i:s");
            $insertData['user_id'] = $this->user_id;
            $insertData['template_access'] = $template_access;
            $insertData['allowed_package_ids'] = implode(',', $allowed_package_ids);
            $insertData['preview_image'] = $template_preview_image;
            $insertData['template_category_id'] = $bot_category;
            $insertData['media_type'] = $media_type;

            $this->basic->insert_data("messenger_bot_saved_templates",$insertData);
            $insert_id=$this->db->insert_id();

            $message="<div class='alert alert-info text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Bot template has been saved to database successfully.")."</div><br><a class='btn-block btn btn-outline-info'  href='".base_url('messenger_bot/saved_templates/').$media_type."'><i class='fa fa-save'></i> ".$this->lang->line("My Saved Templates")."</a><a target='_BLANK' class='btn-block btn btn-outline-primary' href='".base_url('messenger_bot/export_bot_download/').$insert_id."'><i class='fa fa-file-download'></i> ".$this->lang->line("Download Template")."</a>";
            echo json_encode(array('status'=>'0','message'=>$message));
  
        }

        public function update_messenger_template_info()
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();

            if(!$_POST) exit();

            $id = $this->input->post('hidden_id');
            $template_name=strip_tags($this->input->post('template_name'));
            $template_description=strip_tags($this->input->post('template_description'));
            $template_preview_image=$this->input->post('template_preview_image_edit');
            $template_access=$this->input->post('template_access2');
            $allowed_package_ids=$this->input->post('allowed_package_ids2');
            $bot_category = $this->input->post('bot_category');
            $json_file = $this->input->post('json_upload_input_edit');

            $template_preview_image=str_replace(base_url('upload/image/'.$this->user_id.'/'), '', $template_preview_image);

            if(!is_array($allowed_package_ids) || $template_access=='private')  $allowed_package_ids=array();

            $insertData = [];

            if($this->session->userdata('user_type') != 'Admin') $template_access='private';

            if($json_file != '' && file_exists('upload/'.$json_file)) {
                $filename='upload/'.$json_file;
                $get_saved_data = file_get_contents($filename);
                $insertData['savedata'] = $get_saved_data;
            }

            $insertData['template_name'] = $template_name;
            $insertData['description'] = $template_description;
            $insertData['saved_at'] = date("Y-m-d H:i:s");
            $insertData['user_id'] = $this->user_id;
            $insertData['template_access'] = $template_access;
            $insertData['allowed_package_ids'] = implode(',', $allowed_package_ids);
            $insertData['preview_image'] = $template_preview_image;
            $insertData['template_category_id'] = $bot_category;

            $this->basic->update_data("messenger_bot_saved_templates",array("id"=>$id,"user_id"=>$this->user_id),$insertData);

            $message="<div class='alert alert-info text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Bot template has been updated to database successfully.")."</div><br><a class='btn-block btn btn-outline-info'  href='".base_url('messenger_bot/saved_templates')."'><i class='fa fa-save'></i> ".$this->lang->line("My Saved Templates")."</a><a target='_BLANK' class='btn-block btn btn-outline-primary' href='".base_url('messenger_bot/export_bot_download/').$id."'><i class='fa fa-file-download'></i> ".$this->lang->line("Download Template")."</a>";
            echo json_encode(array('status'=>'0','message'=>$message));
  
        }

        public function get_bot_template_form()
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access))  exit();

            if(!$_POST) exit();
            $id=$this->input->post('table_id',true);

            $xdata=$this->basic->get_data("messenger_bot_saved_templates",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
            if(!isset($xdata[0]))
            {
                echo "<div class='alert alert-warning text-center'>".$this->lang->line("Template not found.")."</div>";
                exit();
            }

            $package_list=$this->package_list();


            // category lists
            $current_userid = $this->user_id;
            $admin_id = '1';
            $where_in = array('user_id'=>array($admin_id,$current_userid));
            if($this->session->userdata('user_type') != "Admin"){
                $ids1 = array($admin_id,$current_userid);
                $this->db->where_in('user_id',$ids1);
            }

            $category_list = $this->basic->get_data("messenger_bot_template_category");

            $image_upload_limit = 1; 
            if($this->config->item('messengerbot_image_upload_limit') != '')
            $image_upload_limit = $this->config->item('messengerbot_image_upload_limit'); 

            echo '
            <form id="export_bot_form_edit" method="POST">
              <input type="hidden" name="hidden_id" id="hidden_id" value="'.$xdata[0]['id'].'">
              <div class="col-12">
                <div class="form-group">
                  <label>'.$this->lang->line('Template Name').' *</label>
                  <input type="text" name="template_name" class="form-control" id="template_name2" value="'.$xdata[0]['template_name'].'">                    
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <label>'.$this->lang->line('Template Description').'</label>
                  <textarea type="text" rows="4" name="template_description" class="form-control" id="template_description2">'.$xdata[0]['description'].'</textarea>                    
                </div>
              </div>

              <div class="col-12">
                <div class="form-group">
                  <label>'.$this->lang->line('Template Category').'</label>
                  <small class="blue float-right pointer" id="create_category2"><i class="fas fa-plus-circle"></i> '.$this->lang->line('Create category').'</small>
                  <select name="bot_category" id="bot_category2" class="form-control select2 bot_category2" style="width:100% !important">
                    <option value="">'.$this->lang->line('Select Category').'</option>';
                    foreach($category_list as $category) {
                        $s = '';
                        if($category['id'] == $xdata[0]['template_category_id']) $s = 'selected';
                        echo '<option value="'.$category['id'].'" '.$s.'>'.$category['category_name'].'</option>';
                    }
                  echo '</select>
                </div>
              </div>

              <div class="col-12">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                          <label>'.$this->lang->line('Template Preview Image').'
                            <a href="#" data-placement="top"  data-toggle="popover" title="'.$this->lang->line("Template Preview Image").'" data-content="'.$this->lang->line("Upload a preview image for this template and the image will be showed as preview image of the template.").'"Square image like (400x400) is recommended."><i class="fa fa-info-circle"></i> </a>&nbsp;<span style="cursor:pointer;" class="badge badge-status blue load_preview_modal_edit float-right" item_type="image" file_path="'.$xdata[0]['preview_image'].'"><i class="fa fa-eye"></i></span>
                          </label>
                          

                          <input type="hidden" name="template_preview_image_edit" class="form-control" id="template_preview_image_edit" value="'.$xdata[0]['preview_image'].'">                   
                          <div id="template_preview_image_div_edit">'.$this->lang->line("upload").'</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                          <label>'.$this->lang->line("Upload Template JSON").'</label>
                          <div class="form-group">    
                            <div id="json_upload_edit">'.$this->lang->line("upload").'</div>
                            <input type="hidden" id="json_upload_input_edit" name="json_upload_input_edit">
                          </div>                
                        </div>
                    </div>
                </div>
              </div>';

              if($this->session->userdata("user_type")=='Admin')
              { 
                $select1=$select2=$hiddenclass="";
                if($xdata[0]["template_access"]=="private") $select1='checked';
                if($xdata[0]["template_access"]=="public") $select2='checked';
                if($xdata[0]["template_access"]=="private") $hiddenclass='hidden';
                echo '
                <div class="col-12">
                  <div class="form-group">
                    <div class="control-label">'.$this->lang->line('Template Access').' *</div>
                    <div class="custom-switches-stacked mt-2">
                      <label class="custom-switch">
                        <input type="radio" name="template_access2" value="private" class="custom-switch-input" '.$select1.'>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">'.$this->lang->line("Only me").'</span>
                      </label>
                      <label class="custom-switch">
                        <input type="radio" name="template_access2" value="public" class="custom-switch-input" '.$select2.'>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">'.$this->lang->line("Me as well as other users").'</span>
                      </label>
                    </div>                
                  </div>
                </div>

                <div class="col-12 '.$hiddenclass.'" id="allowed_package_ids_con2">
                  <div class="form-group">
                    <label>'.$this->lang->line('Choose User Packages').' *</label><br/>';
                    $xpacks=explode(',', $xdata[0]['allowed_package_ids']);
                    $xpacks=array_filter($xpacks);
                    echo "<select class='form-control select2' id='allowed_package_ids2' name='allowed_package_ids2[]' multiple=''>";
                    foreach ($package_list as $key => $value) 
                    {
                        $select3="";
                        if(in_array($key, $xpacks)) $select3='selected="selected"';
                        echo '<option value="'.$key.'" '.$select3.'>'.$value.'</option>';
                    } 
                    echo "</select>";
                  echo '
                  </div>
                </div>';
              }

              echo '
                <script type="text/javascript">
                  $("document").ready(function(){

                    $("#allowed_package_ids2,#bot_category2").select2({ width: "100%" });

                    var base_url="'.site_url().'";
                    var user_id = "'.$this->session->userdata("user_id").'";
                    var image_upload_limit = "'.$image_upload_limit.'";
                    $("#template_preview_image_div_edit").uploadFile({
                      url:base_url+"messenger_bot/upload_image_only",
                      fileName:"myfile",
                      maxFileSize:image_upload_limit*1024*1024,
                      showPreview:false,
                      returnType: "json",
                      dragDrop: true,
                      showDelete: true,
                      multiple:false,
                      maxFileCount:1, 
                      acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
                      deleteCallback: function (data, pd) {
                          var delete_url="'.site_url("messenger_bot/delete_uploaded_file").'";
                          $.post(delete_url, {op: "delete",name: data},
                              function (resp,textStatus, jqXHR) {
                                $("#template_preview_image_edit").val("");                    
                              });
                         
                       },
                       onSuccess:function(files,data,xhr,pd)
                         {
                             var data_modified = base_url+"upload/image/"+user_id+"/"+data;
                             $("#template_preview_image_edit").val(data_modified);
                         }
                    });

                    $("#json_upload_edit").uploadFile({
                        url:base_url+"messenger_bot/upload_json_template",
                        fileName:"myfile",
                        showPreview:false,
                        returnType: "json",
                        dragDrop: true,
                        showDelete: true,
                        multiple:false,
                        maxFileCount:1, 
                        acceptFiles:".json",
                        deleteCallback: function (data, pd) {
                            var delete_url="'.site_url("messenger_bot/upload_json_template_delete").'";
                              $.post(delete_url, {op: "delete",name: data},
                                  function (resp,textStatus, jqXHR) { 
                                    $("#json_upload_input_edit").val("");                      
                                  });
                           
                         },
                         onSuccess:function(files,data,xhr,pd)
                           {
                               var data_modified = data;
                               $("#json_upload_input_edit").val(data_modified);
                           }
                    });

                  });
                </script>
              ';

              echo'
              <div class="row">
                  <div class="col-6"><a href="#" id="update_bot_submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> '.$this->lang->line("Update").'</a></div>
                  <div class="col-6"><a href="#" id="cancel_bot_submit2" class="btn btn-secondary btn-lg float-right"><i class="fa fa-close"></i> '.$this->lang->line("Cancel").'</a></div>
              </div>            
              <div class="clearfix"></div>
            </form>';
        }

        // ZILANI END

        public function saved_templates_data()
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access))  exit();
            
            $this->ajax_check();
            $search_template_name = trim($this->input->post("search_template_name"));
            $search_template_access = trim($this->input->post("search_template_access"));
            $display_columns = array("#","CHECKBOX",'template_name', 'owner', 'saved_at', 'actions');

            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
            $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
            $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
            $order_by=$sort." ".$order;

            $where_simple=array();

            if ($search_template_name != '') $where_simple['template_name like ']    = "%".$search_template_name."%";

            if ($this->session->userdata("user_type")=="Member") 
            {
                $package_info=$this->session->userdata('package_info');
                $search_package_id=isset($package_info['id'])?$package_info['id']:'0';

                if($search_template_access=="public") $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public'))";
                else if($search_template_access=="private") $where_custom="(template_access='private' AND user_id='".$this->user_id."')";
                else $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public') OR (template_access='private' AND user_id='".$this->user_id."'))";
                $this->db->where( $where_custom );
            }
            else
            {
                $where_simple["user_id"]=$this->user_id;
                if ($search_template_access) $where_simple['template_access'] = $search_template_access;
            }

            $where = array('where' => $where_simple);

            $table = "messenger_bot_saved_templates";
            $info = $this->basic->get_data($table,$where,$select='',$join='',$limit, $start,$order_by);  

            for($i=0;$i<count($info);$i++)
            {
                $action_count = 4;
                if($info[$i]['saved_at'] != "0000-00-00 00:00:00")
                $info[$i]['saved_at'] = date("M j, y H:i",strtotime($info[$i]['saved_at']));

                if($this->session->userdata("user_type")=="Admin")
                {
                    if($info[$i]['template_access'] == 'private') $info[$i]['owner'] = '<span class="badge badge-status"><i class="fa fa-user-secret orange"></i> '.$this->lang->line("Private").'</span>';
                    else $info[$i]['owner'] = '<span class="badge badge-status"><i class="fa fa-check-circle green"></i> '.$this->lang->line("Public").'</span>';
                }
                else
                {
                    if($info[$i]['template_access'] == 'private') $info[$i]['owner'] = '<span class="badge badge-status"><i class="fa fa-user-secret green"></i> '.$this->lang->line("My Template").'</span>';
                    else $info[$i]['owner'] = '<span class="badge badge-status"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("Admin Template").'</span>';
                }           

                $action_width = ($action_count*47)+20;

                if($info[$i]['user_id']==$this->user_id)
                $info[$i]['delete'] =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("delete")."' id='".$info[$i]['id']."' class='delete btn btn-circle btn-outline-danger'><i class='fa fa-trash'></i></a>";
                else $info[$i]['delete'] =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("This is not your template")."' class='btn btn-circle btn-default border_gray'><i class='fa fa-trash'></i></a>";
                
                $info[$i]['download'] =  "<a target='_BLANK' href='".base_url("messenger_bot/export_bot_download/".$info[$i]['id'])."' data-toggle='tooltip' title='".$this->lang->line("download")."' class='btn btn-circle btn-outline-primary'><i class='fa fa-cloud-download'></i></a>";

                $info[$i]['view'] =  "<a target='_BLANK' href='".base_url("messenger_bot/saved_template_view/".$info[$i]['id'])."' data-toggle='tooltip' title='".$this->lang->line("view")."' class='btn btn-circle btn-outline-info'><i class='fa fa-eye'></i></a>";
                
                if($info[$i]['user_id']==$this->user_id)
                $info[$i]['edit'] =  "<a href='#' target='_BLANK' data-toggle='tooltip' title='".$this->lang->line("Edit this template")."' table_id='".$info[$i]['id']."' class='export_bot btn btn-circle btn-outline-warning'><i class='fa fa-edit'></i></a>";
                else $info[$i]['edit'] =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("This is not your template")."' class='btn btn-circle btn-default border_gray'><i class='fa fa-edit'></i></a>";
                
                $info[$i]['actions']='<div class="dropdown d-inline dropright">
                  <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
                  <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">'.$info[$i]['view']." ".$info[$i]['download']." ".$info[$i]['edit']." ".$info[$i]['delete']."</div></div><script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
            }


            if ($this->session->userdata("user_type")=="Member") 
            {
                $package_info=$this->session->userdata('package_info');
                $search_package_id=isset($package_info['id'])?$package_info['id']:'0';

                if($search_template_access=="public") $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public'))";
                else if($search_template_access=="private") $where_custom="(template_access='private' AND user_id='".$this->user_id."')";
                else $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public') OR (template_access='private' AND user_id='".$this->user_id."'))";
                $this->db->where( $where_custom );
            }
            else
            {
                $where_simple["user_id"]=$this->user_id;
                if ($search_template_access) $where_simple['template_access'] = $search_template_access;
            }
            $total_rows_array = $this->basic->get_data($table,$where); 
            $total_result = count($total_rows_array);

            $data['draw'] = (int)$_POST['draw'] + 1;
            $data['recordsTotal'] = $total_result;
            $data['recordsFiltered'] = $total_result;
            $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

            echo json_encode($data);
        }

        

        public function delete_template()
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();

            if(!$_POST) exit();
            $id=$this->input->post("id");     

            if($this->basic->delete_data("messenger_bot_saved_templates",array("id"=>$id,"user_id"=>$this->user_id))) echo "1";
            else echo "0";
        }


        public function get_export_bot_form()
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access))  exit();

            if(!$_POST) exit();
            $id=$this->input->post('table_id',true);

            $xdata=$this->basic->get_data("messenger_bot_saved_templates",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
            if(!isset($xdata[0]))
            {
                echo "<div class='alert alert-warning text-center'>".$this->lang->line("Template not found.")."</div>";
                exit();
            }

            $package_list=$this->package_list();

            $image_upload_limit = 1; 
            if($this->config->item('messengerbot_image_upload_limit') != '')
            $image_upload_limit = $this->config->item('messengerbot_image_upload_limit'); 

            echo '
            <form id="export_bot_form" method="POST">
              <input type="hidden" name="hidden_id" id="hidden_id" value="'.$xdata[0]['id'].'">
              <div class="col-12">
                <div class="form-group">
                  <label>'.$this->lang->line('Template Name').' *</label>
                  <input type="text" name="template_name" class="form-control" id="template_name" value="'.$xdata[0]['template_name'].'">                    
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <label>'.$this->lang->line('Template Description').'</label>
                  <textarea type="text" rows="4" name="template_description" class="form-control" id="template_description">'.$xdata[0]['description'].'</textarea>                    
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <label>'.$this->lang->line('Template Preview Image').' [Square image like (400x400) is recommended]</label>
                  <span style="cursor:pointer;" class="badge badge-status blue load_preview_modal float-right" item_type="image" file_path="'.$xdata[0]['preview_image'].'"><i class="fa fa-eye"></i> '.$this->lang->line('preview').'</span>

                  <input type="hidden" name="template_preview_image" class="form-control" id="template_preview_image" value="'.$xdata[0]['preview_image'].'">                   
                  <div id="template_preview_image_div">'.$this->lang->line("upload").'</div>
                </div>
              </div>';

              if($this->session->userdata("user_type")=='Admin')
              { 
                $select1=$select2=$hiddenclass="";
                if($xdata[0]["template_access"]=="private") $select1='checked';
                if($xdata[0]["template_access"]=="public") $select2='checked';
                if($xdata[0]["template_access"]=="private") $hiddenclass='hidden';
                echo '
                <div class="col-12">
                  <div class="form-group">
                    <div class="control-label">'.$this->lang->line('Template Access').' *</div>
                    <div class="custom-switches-stacked mt-2">
                      <label class="custom-switch">
                        <input type="radio" name="template_access" value="private" class="custom-switch-input" '.$select1.'>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">'.$this->lang->line("Only me").'</span>
                      </label>
                      <label class="custom-switch">
                        <input type="radio" name="template_access" value="public" class="custom-switch-input" '.$select2.'>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">'.$this->lang->line("Me as well as other users").'</span>
                      </label>
                    </div>                
                  </div>
                </div>

                <div class="col-12 '.$hiddenclass.'" id="allowed_package_ids_con">
                  <div class="form-group">
                    <label>'.$this->lang->line('Choose User Packages').' *</label><br/>';
                    $xpacks=explode(',', $xdata[0]['allowed_package_ids']);
                    $xpacks=array_filter($xpacks);
                    echo "<select class='form-control select2' id='allowed_package_ids' name='allowed_package_ids[]' multiple=''>";
                    foreach ($package_list as $key => $value) 
                    {
                        $select3="";
                        if(in_array($key, $xpacks)) $select3='selected="selected"';
                        echo '<option value="'.$key.'" '.$select3.'>'.$value.'</option>';
                    } 
                    echo "</select>";
                  echo '
                  </div>
                </div>
                <script type="text/javascript">
                  $("document").ready(function(){

                    $("#allowed_package_ids").select2({ width: "100%" });

                    var base_url="'.site_url().'";
                    var user_id = "'.$this->session->userdata("user_id").'";
                    var image_upload_limit = "'.$image_upload_limit.'";
                    $("#template_preview_image_div").uploadFile({
                      url:base_url+"messenger_bot/upload_image_only",
                      fileName:"myfile",
                      maxFileSize:image_upload_limit*1024*1024,
                      showPreview:false,
                      returnType: "json",
                      dragDrop: true,
                      showDelete: true,
                      multiple:false,
                      maxFileCount:1, 
                      acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
                      deleteCallback: function (data, pd) {
                          var delete_url="'.site_url("messenger_bot/delete_uploaded_file").'";
                          $.post(delete_url, {op: "delete",name: data},
                              function (resp,textStatus, jqXHR) {
                                $("#template_preview_image").val("");                    
                              });
                         
                       },
                       onSuccess:function(files,data,xhr,pd)
                         {
                             var data_modified = base_url+"upload/image/"+user_id+"/"+data;
                             $("#template_preview_image").val(data_modified);
                         }
                    });

                  });
                </script>';
              }

              echo'
              <div class="row">
                  <div class="col-6"><a href="#" id="export_bot_submit" class="btn btn-info btn-lg"><i class="fa fa-save"></i> '.$this->lang->line("Save").'</a></div>
                  <div class="col-6"><a href="#" id="cancel_bot_submit" class="btn btn-secondary btn-lg float-right"><i class="fa fa-close"></i> '.$this->lang->line("Cancel").'</a></div>
              </div>            
              <div class="clearfix"></div>
            </form>';
        }

        public function edit_export_bot()
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
            if(!$_POST) exit();
            
            $id=$this->input->post('hidden_id');
            $template_name=$this->input->post('template_name');
            $template_description=$this->input->post('template_description');
            $template_preview_image=$this->input->post('template_preview_image');
            $template_access=$this->input->post('template_access');
            $allowed_package_ids=$this->input->post('allowed_package_ids');

            $template_preview_image=str_replace(base_url('upload/image/'.$this->user_id.'/'), '', $template_preview_image);

            if(!is_array($allowed_package_ids) || $template_access=='private')  $allowed_package_ids=array();

            $this->basic->update_data("messenger_bot_saved_templates",array("id"=>$id,"user_id"=>$this->user_id),array("template_name"=>$template_name,"description"=>$template_description,"preview_image"=>$template_preview_image,"saved_at"=>date("Y-m-d H:i:s"),"template_access"=>$template_access,"allowed_package_ids"=>implode(',', $allowed_package_ids)));
        }

        public function saved_template_view($id=0)
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access))  redirect('home/login_page', 'location');

            if($id==0) exit();

            if($this->session->userdata("user_type")=="Member") 
            {
                $package_info=$this->session->userdata('package_info');
                $search_package_id=isset($package_info['id'])?$package_info['id']:'0';
                $where_custom="id=".$id." AND ((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public') OR (template_access='private' AND user_id='".$this->user_id."'))";
                $this->db->where( $where_custom );
                $getdata=$this->basic->get_data("messenger_bot_saved_templates");
            }
            else
            {
                $where_simple["id"]=$id;
                $where_simple["user_id"]=$this->user_id;
                $where = array('where' => $where_simple);
                $getdata=$this->basic->get_data("messenger_bot_saved_templates",$where);

            }
            if(!isset($getdata[0])) exit();

            $data=array('templatedata'=>$getdata[0],"body"=>"messenger_tools/saved_template_view","page_title"=>$this->lang->line("Template Details"));
            $this->_viewcontroller($data);

        }


    
        public function error_log_report_autoreponder()
        {
            $this->ajax_check();
            $user_id = $this->user_id;    
            $error_search = $this->input->post('error_search');    
            $auto_responder_type = $this->input->post('auto_responder_type');    
            $autoresponder_service_name = $this->input->post('autoresponder_service_name');    
            $display_columns = array("#", 'settings_type', 'status', 'email','auto_responder_type','api_name','insert_time', 'actions'); 

            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 6;
            $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'insert_time';
            $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
            $order_by=$sort." ".$order;


            $table_name = "send_email_to_autoresponder_log";

            if($this->session->userdata('user_type') == 'Admin')
            {
                $sql = "(user_id=0 OR user_id=".$this->user_id.")";

                if($auto_responder_type!="") $sql.=" AND auto_responder_type='".$auto_responder_type."'";
                if($autoresponder_service_name!="" && $auto_responder_type=="Email Autoresponder") $sql.=" AND api_name='".$autoresponder_service_name."'";

                if($error_search!="") $sql.=" AND (status like '%".$error_search."%' OR auto_responder_type like '%".$error_search."%' OR response like '%".$error_search."%' OR email like '%".$error_search."%')";
                $this->db->where($sql);
            }
            else
            {
                $sql = "user_id=".$this->user_id;

                if($auto_responder_type!="") $sql.=" AND auto_responder_type='".$auto_responder_type."'";
                if($autoresponder_service_name!="" && $auto_responder_type=="Email Autoresponder") $sql.=" AND api_name='".$autoresponder_service_name."'";

                if($error_search!="") $sql.=" AND (status like '%".$error_search."%' OR auto_responder_type like '%".$error_search."%' OR response like '%".$error_search."%' OR email like '%".$error_search."%')";
                $this->db->where($sql);
            }
            $info = $this->basic->get_data($table_name,$where='',$select='',$join='',$limit,$start,$order_by);   

            $this->db->where($sql);
            $total_rows_array=$this->basic->count_row('send_email_to_autoresponder_log',$where='');
            $total_result=$total_rows_array[0]['total_rows'];   

            foreach ($info as $key=>$error_info) 
            {
                $action_button = "<div style='min-width:90px'><a class='btn btn-circle btn-outline-danger error_response' data-toggle='tooltip' title='".$this->lang->line("Response")."' href='#' data-id='".$error_info['id']."'> <i class='fas fa-eye'></i></a></div>
                                  <script>
                    $('[data-toggle=\"tooltip\"]').tooltip();
                  </script>";
                $info[$key]['actions'] = $action_button;
                $info[$key]['status'] = $error_info['status'];
                $info[$key]['email'] = $error_info['email'];
                $info[$key]['api_name'] = ucfirst($error_info['api_name']);
                $info[$key]['auto_responder_type'] = $error_info['auto_responder_type'];
                $info[$key]['settings_type'] = ucfirst($error_info['settings_type']);
                $info[$key]['insert_time'] = date("jS M, y H:i:s",strtotime($error_info['insert_time']));
            }

            $data['draw'] = (int)$_POST['draw'] + 1;
            $data['recordsTotal'] = $total_result;
            $data['recordsFiltered'] = $total_result;
            $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

            echo json_encode($data);
            
        }

        public function error_log_response()
        {
            $this->ajax_check();
            $id = $this->input->post('id',true);

            if($this->session->userdata('user_type') == 'Admin') $sql = "(user_id=0 OR user_id=".$this->user_id.") AND id={$id}";
            else $sql = "user_id=".$this->user_id." AND id={$id}";
            $this->db->where($sql);    
            $getdata = $this->basic->get_data("send_email_to_autoresponder_log");
            $response = isset($getdata[0]['response']) ? $getdata[0]['response'] : '';

            
            if(is_array(json_decode($response,true))) 
            {
                echo "<pre class='text-left'>";
                print_r(json_decode($response,true));
                echo "</pre>";
            }
            else
            {
                echo "<pre class='text-center'>";
                print_r($response);
                echo "</pre>";
            }
     

        }



        public function edit_bot_settings_from_error_log($settings_id=161){

            if($settings_id=="") {
                echo "BOT Settings ID Not Found"; exit;
            }

            $where=array('where'=>array("id"=>$settings_id));
            $bot_settings_info=$this->basic->get_data('messenger_bot',$where,$select=array('id','postback_id','page_id','keyword_type'));


            $postback_id=isset($bot_settings_info[0]['postback_id']) ? $bot_settings_info[0]['postback_id']:"";
            $page_id=isset($bot_settings_info[0]['page_id']) ? $bot_settings_info[0]['page_id']:"";

            if($postback_id!=""){
                $where=array();
                $where=array('where'=>array("postback_id"=>$postback_id,'page_id'=>$page_id));
                $postback_info=$this->basic->get_data('messenger_bot_postback',$where,$select=array('id','postback_id'));
                $postback_auto_id=isset($postback_info[0]['id']) ? $postback_info[0]['id']:"";


                if($postback_id=='UNSUBSCRIBE_QUICK_BOXER' || $postback_id=='RESUBSCRIBE_QUICK_BOXER' ||$postback_id=='QUICK_REPLY_EMAIL_REPLY_BOT' || $postback_id=='QUICK_REPLY_PHONE_REPLY_BOT' || $postback_id=='QUICK_REPLY_LOCATION_REPLY_BOT' || $postback_id=='QUICK_REPLY_BIRTHDAY_REPLY_BOT' || $postback_id=='YES_START_CHAT_WITH_HUMAN' || $postback_id=='YES_START_CHAT_WITH_BOT')
                    
                    $edit_link="messenger_bot/edit_template/{$postback_auto_id}/0/default";

                else
                    $edit_link="messenger_bot/edit_template/{$postback_auto_id}";

                if($postback_auto_id!="")
                    redirect($edit_link, 'location');
                else
                    echo "Postbac Not Found for Edit";
            }
            else{   

                $keyword_type=isset($bot_settings_info[0]['keyword_type']) ? $bot_settings_info[0]['keyword_type']:"";

                if($keyword_type=='get-started')
                    $edit_link="messenger_bot/edit_bot/{$settings_id}/0/getstart";
                elseif($keyword_type=='get-started')
                     $edit_link="messenger_bot/edit_bot/{$settings_id}/0/nomatch";
                 else
                    $edit_link="messenger_bot/edit_bot/{$settings_id}";

                if($settings_id!="")
                    redirect($edit_link, 'location');  
            }
        }



        // OTN template manager section
        public function otn_template_manager($page_id=0,$iframe='0')
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access))
            redirect('home/login_page', 'location');

            $media_type = 'fb';
            if($this->session->userdata('selected_global_media_type')) {
                $media_type = $this->session->userdata('selected_global_media_type');
            }
            $data['media_type'] = $media_type;

            $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,'bot_enabled'=>'1')),array('page_name','id'));
            $data['page_info'] = $page_list;
            $data['body'] = 'messenger_tools/otn_manager/template_manager';
            $data['page_title'] = $this->lang->line('Template Manager');
            $data['iframe'] = $iframe;
            $data['page_id'] = $page_id;
            $this->_viewcontroller($data);
        }

        public function otn_template_manager_data()
        {
            $this->ajax_check();
            $search_value = isset($_POST['search']) ? $_POST['search']['value'] : null;
            $page_id = $this->input->post('page_id',true);
            $display_columns = array("#","CHECKBOX",'id', 'page_name', 'template_name', 'otn_postback_id', 'total_optin_subscriber', 'total_sent', 'total_not_sent','flow_type', 'action');
            $search_columns = ['template_name','otn_postback_id'];

            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
            $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
            $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
            $order_by=$sort." ".$order;

            $where_custom = '';
            $where_custom .= "otn_postback.user_id = ".$this->user_id;
            $where_custom .= " AND otn_postback.page_id = ".$page_id;

            if ($search_value != '') {
                foreach ($search_columns as $key => $value) 
                $temp[] = $value." LIKE "."'%$search_value%'";
                $imp = implode(" OR ", $temp);
                $where_custom .=" AND (".$imp.") ";
            }

            $where_simple = array();
            $table="otn_postback";
            $join = array(
                'facebook_rx_fb_page_info'=>'otn_postback.page_id=facebook_rx_fb_page_info.id,left',
                'otn_optin_subscriber'=>'otn_postback.id=otn_optin_subscriber.otn_id,left'
            );
            $select = array('otn_postback.*','page_name','count(otn_optin_subscriber.id) as total_optin_subscriber','count(case when is_sent = "1" THEN otn_optin_subscriber.id END) as total_sent','count(case when is_sent = "0" THEN otn_optin_subscriber.id END) as total_not_sent');
            $this->db->where($where_custom);
            $info=$this->basic->get_data($table,$where='',$select,$join,$limit,$start,$order_by,$group_by='otn_postback.id');

            $this->db->where($where_custom);
            $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='otn_postback.id');
            $total_result=$total_rows_array[0]['total_rows'];

            $i=0;
            $base_url=base_url();
            foreach ($info as $key => $value) 
            {
                $info[$i]["action"] = "<div style='min-width:100px'>";
                if(addon_exist($module_id=315,$addon_unique_name="visual_flow_builder") && isset($value['flow_type']) && $value['flow_type'] == 'flow')
                {
                    $info[$i]["action"] .= "<a target='_BLANK' class='btn btn-circle btn-outline-warning' title='". $this->lang->line("Edit") ."' href='".base_url('visual_flow_builder/edit_builder_data/').$value['visual_flow_campaign_id']."/1'><i class='fas fa-edit'></i></a>&nbsp;";
                }

                if($info[$i]['flow_type'] == "general") {

                    $info[$i]['action'] .= "<a href='".base_url('messenger_bot/otn_edit_template/').$value['id']."' class='btn btn-circle btn-outline-warning'><i class='fa fa-edit'></i></a>&nbsp;";
                    $info[$i]['action'] .= "<a href='#' class='btn btn-sm btn-outline-danger delete_template' title='Delete' table_id='".$value['id']."'><i class='fa fa-trash'></i></a>";
                }

                $info[$i]["action"] .= "<div>";
                $info[$i]['flow_type'] = ucfirst($value['flow_type']);

                $i++;
            }

            $data['draw'] = (int)$_POST['draw'] + 1;
            $data['recordsTotal'] = $total_result;
            $data['recordsFiltered'] = $total_result;
            $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

            echo json_encode($data);


        }

        public function otn_create_new_template($is_iframe="0",$default_page="",$default_child_postback_id="")
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access))
            redirect('home/login_page', 'location');

            $media_type = 'fb';
            if($this->session->userdata('selected_global_media_type')) {
                $media_type = $this->session->userdata('selected_global_media_type');
            }
            $data['media_type'] = $media_type;

            $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
            $this->is_sms_email_drip_campaigner_exist=$this->sms_email_drip_campaigner_exist();
            $data['body'] = 'messenger_tools/otn_manager/add_new_template';
            $data['page_title'] = $this->lang->line('Create new OTN template');
            $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
            $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);
            $page_list = array();
            foreach($page_info as $value)
            {
                $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
            }
            $data['page_list'] = $page_list;
            $data['is_iframe'] = $is_iframe;
            $data['iframe'] = $is_iframe;
            $data['default_page'] = $default_page;
            $data['default_child_postback_id'] = $default_child_postback_id;

            if($this->basic->is_exist("add_ons",array("project_id"=>16))) $data['has_broadcaster_addon'] = 1;
            else  $data['has_broadcaster_addon'] = 0;

            $this->_viewcontroller($data); 
        }
        
        public function otn_create_template_action()
        {
            $this->ajax_check();
            $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
            $this->is_sms_email_drip_campaigner_exist=$this->sms_email_drip_campaigner_exist();
            $template_name = strip_tags($this->input->post('bot_name',true));
            $page_table_id = strip_tags($this->input->post('page_table_id',true));
            $reply_postback_id = strip_tags($this->input->post('reply_postback_id',true));
            $template_postback_id = strip_tags($this->input->post('template_postback_id',true));
            $label_ids_array = $this->input->post('label_ids',true);
            $label_ids = '';
            if($label_ids_array)
            $label_ids = implode(',', $label_ids_array);
            

            $this->db->trans_start();
   
            $data = array(
                'user_id' => $this->user_id,
                'template_name' => $template_name,
                'page_id' => $page_table_id,
                'otn_postback_id' => $template_postback_id,
                'reply_postback_id' => $reply_postback_id,
                'label_id' => $label_ids,
                'flow_type' => 'general'
            );
            if($this->is_drip_campaigner_exist || $this->is_sms_email_drip_campaigner_exist)
            {
                $drip_campaign_id_array = $this->input->post('drip_campaign_id',true);
                $drip_campaign_ids = implode(',', $drip_campaign_id_array);
                $data['drip_campaign_id'] = $drip_campaign_ids;
            }
            $this->basic->insert_data('otn_postback',$data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                echo json_encode(array("status" => "0", "message" =>$this->lang->line("Creating template was unsuccessful. Database error occured during creating template.")));
                exit();
            }
            else
            {
                echo json_encode(array("status" => "1", "message" =>$this->lang->line("New template has been stored successfully.")));
            }
            
        }

        public function otn_edit_template($postback_table_id=0,$iframe='0',$is_default='0')
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access))
            redirect('home/login_page', 'location');

            if($postback_table_id == 0) exit();
            $this->is_broadcaster_exist=$this->broadcaster_exist();
            $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
            $this->is_sms_email_drip_campaigner_exist=$this->sms_email_drip_campaigner_exist();
            $table_name = "otn_postback";
            $where_bot['where'] = array('id' => $postback_table_id, 'user_id'=>$this->user_id);
            $bot_info = $this->basic->get_data($table_name, $where_bot);
            if(empty($bot_info)) redirect('messenger_bot/otn_template_manager', 'location');
            $data['body'] = 'messenger_tools/otn_manager/edit_template';
            $data['page_title'] = $this->lang->line('Edit template');

            $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
            $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);
            $page_list = array();
            foreach($page_info as $value)
            {
                $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
            }
            $data['page_list'] = $page_list;
            $data['bot_info'] = isset($bot_info[0]) ? $bot_info[0] : array();

            $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"]),'where_not_in'=>array('postback_id'=>array('UNSUBSCRIBE_QUICK_BOXER','RESUBSCRIBE_QUICK_BOXER','YES_START_CHAT_WITH_HUMAN','YES_START_CHAT_WITH_BOT'))));

            $current_postbacks = array();
            foreach ($postback_id_list as $value) {
                if($value['template_id'] == $postback_table_id || $value['id'] == $postback_table_id)
                $current_postbacks[] = $value['postback_id'];
            }
            $data['postback_ids'] = $postback_id_list;
            $data['current_postbacks'] = $current_postbacks;

            $table_type = 'messenger_bot_broadcast_contact_group';
            $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"],"unsubscribe"=>"0","invisible"=>"0");
            $data['info_type'] = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

            if($this->is_broadcaster_exist)
            {          

                $table_type = 'messenger_bot_drip_campaign';
                $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"]);
                $data['dripcampaign_list'] = $this->basic->get_data($table_type,$where_type,$select='');
            }
            else 
            {
                $data['dripcampaign_list']=array();
            }

            if($this->is_broadcaster_exist)
                $data['has_broadcaster_addon'] = 1;
            else
                $data['has_broadcaster_addon'] = 0;

            $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"],'template_for'=>'reply_message')),array('id','postback_id','bot_name'));
            $postback_dropdown = array();
            if(!empty($postback_id_list))
            {
                foreach($postback_id_list as $value)
                    // $postback_dropdown[$value['id']] = $value['postback_id'];
                    $postback_dropdown[$value['id']] = $value['bot_name'].' ['.$value['postback_id'].']';
            }
            $data['postback_dropdown'] = $postback_dropdown;
            $data['iframe'] = $iframe;
            $data['is_default'] = $is_default;

            $data['iframe']=$iframe;
            $this->_viewcontroller($data);  
        }

        public function otn_edit_template_action()
        {
            $this->ajax_check();
            $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
            $this->is_sms_email_drip_campaigner_exist=$this->sms_email_drip_campaigner_exist();
            $table_id = strip_tags($this->input->post('id',true));

            $postback_info = $this->basic->get_data('otn_postback',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));
            if(empty($postback_info))
            {
                echo json_encode(array("status" => "0", "message" =>$this->lang->line("No Template is found for this user with this ID.")));
                exit();
            }

            $template_name = strip_tags($this->input->post('bot_name',true));
            $page_table_id = strip_tags($this->input->post('page_table_id',true));
            $reply_postback_id = strip_tags($this->input->post('reply_postback_id',true));
            $template_postback_id = strip_tags($this->input->post('template_postback_id',true));
            $label_ids_array = $this->input->post('label_ids',true);
            
            $label_ids = '';
            if($label_ids_array)
            $label_ids = implode(',', $label_ids_array);

            $this->db->trans_start();
            
            $data = array(
                'user_id' => $this->user_id,
                'template_name' => $template_name,
                'reply_postback_id' => $reply_postback_id,
                'label_id' => $label_ids
            );
            if($this->is_drip_campaigner_exist || $this->is_sms_email_drip_campaigner_exist)
            {
                $drip_campaign_id_array = $this->input->post('drip_campaign_id',true);
                $drip_campaign_ids = implode(',', $drip_campaign_id_array);
                $data['drip_campaign_id'] = $drip_campaign_ids;
            }

            $this->basic->update_data('otn_postback',array('id'=>$table_id),$data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                echo json_encode(array("status" => "0", "message" =>$this->lang->line("Updating template was unsuccessful. Database error occured during updating template.")));
                exit();
            }
            else
            {
                echo json_encode(array("status" => "1", "message" =>$this->lang->line("Template has been updated successfully.")));
            }
        }

        public function otn_clone_template($postback_table_id=0,$iframe='0',$is_default='0')
        {
            if($postback_table_id == 0) exit();
            $table_name = "messenger_bot_postback";
            $where_bot['where'] = array('id' => $postback_table_id, 'status' => '1', 'user_id'=>$this->user_id);
            $bot_info = $this->basic->get_data($table_name, $where_bot);
            if(empty($bot_info)) redirect('messenger_bot/template_manager', 'location');

            $data['body'] = 'messenger_tools/edit_template';
            $data['page_title'] = $this->lang->line('Clone template');
            $data["templates"]=$this->basic->get_enum_values("messenger_bot","template_type");
            $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");

            $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
            $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);
            $page_list = array();
            foreach($page_info as $value)
            {
                $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
            }

            $data['page_list'] = $page_list;
            $data['bot_info'] = isset($bot_info[0]) ? $bot_info[0] : array();

            $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"]),'where_not_in'=>array('postback_id'=>array('UNSUBSCRIBE_QUICK_BOXER','RESUBSCRIBE_QUICK_BOXER','YES_START_CHAT_WITH_HUMAN','YES_START_CHAT_WITH_BOT'))));

            $current_postbacks = array();
            foreach ($postback_id_list as $value) {
                if($value['template_id'] == $postback_table_id || $value['id'] == $postback_table_id)
                $current_postbacks[] = $value['postback_id'];
            }
            $data['postback_ids'] = $postback_id_list;
            $data['current_postbacks'] = $current_postbacks;

            $table_type = 'messenger_bot_broadcast_contact_group';
            $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"],"unsubscribe"=>"0","invisible"=>"0");
            $data['info_type'] = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

            if($this->is_broadcaster_exist)
            {          

                $table_type = 'messenger_bot_drip_campaign';
                $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"]);
                $data['dripcampaign_list'] = $this->basic->get_data($table_type,$where_type,$select='');
            }
            else 
            {
                $data['dripcampaign_list']=array();
            }


            if($this->is_broadcaster_exist)
                $data['has_broadcaster_addon'] = 1;
            else
                $data['has_broadcaster_addon'] = 0;

            // $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"],'template_for'=>'reply_message','is_template'=>'1'),'or_where'=>array('template_id'=>$postback_table_id)),array('postback_id','bot_name'));
            $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"],'template_for'=>'reply_message')),array('postback_id','bot_name'));
            $postback_dropdown = array();
            if(!empty($postback_id_list))
            {
                foreach($postback_id_list as $value)
                    $postback_dropdown[$value['postback_id']] = $value['postback_id'];
                    // array_push($postback_dropdown, $value['postback_id']);
            }
            $data['postback_dropdown'] = $postback_dropdown;
            $data['iframe'] = $iframe;
            $data['is_default'] = $is_default;
            $data['action_type'] = 'clone';

            $data['iframe']=$iframe;
            $this->_viewcontroller($data);  
        }

        public function otn_ajax_delete_template_info()
        {
            $id = $this->input->post('table_id',true);
            $postback_info = $this->basic->get_data('otn_postback',array('where'=>array('id'=>$id,'user_id'=>$this->user_id)));
            if(empty($postback_info))
            {
                echo "no_match";
                exit;
            }
            $postback_id = $postback_info[0]['otn_postback_id'];
            $search_content = '%"payload":"'.$postback_id.'"%';
            $bot_info = $this->basic->get_data('messenger_bot',array('where'=>array('message like'=>$search_content)));
            
            if(!empty($bot_info))
            {
                $response = "<div class='text-center alert alert-danger'>".$this->lang->line('You can not delete this template because it is being used in the following bots. First make sure that these templates are free to delete. You can do this by editing or deleting the following bots.')."</div><br>";
                $response.= '
                     <script>
                         $(document).ready(function() {
                             $("#need_to_delete_bots").DataTable();
                         }); 
                      </script>
                      <style>
                         .dataTables_filter
                          {
                             float : right;
                          }
                      </style>
                     <div class="table-responsive">
                     <table id="need_to_delete_bots" class="table table-bordered">
                         <thead>
                             <tr>
                                 <th>'.$this->lang->line("SN.").'</th>
                                 <th>'.$this->lang->line("Bot Name").'</th>
                                 <th>'.$this->lang->line("Kyeword").'</th>
                                 <th>'.$this->lang->line("Keyword Type").'</th>
                                 <th class="text-center">'.$this->lang->line("Actions").'</th>
                             </tr>
                         </thead>
                         <tbody>';
                $sn = 0;
                $value = array();
                foreach($bot_info as $value)
                {
                    $sn++;
                    $bot_id = $value['id'];
                    $url = '#';
                    if($value['is_template'] == '1')
                    {
                        $child_postback_info = $this->basic->get_data('messenger_bot_postback',array('where'=>array('messenger_bot_table_id'=>$value['id'])));

                        $postback_table_id = 0;
                        if(isset($child_postback_info[0]['id'])) $postback_table_id = $child_postback_info[0]['id'];
                        $url = base_url('messenger_bot/edit_template/').$postback_table_id;
                    }
                    else
                        $url = base_url('messenger_bot/edit_bot/').$bot_id.'/postback';
                    $response .= '<tr>
                                <td>'.$sn.'</td>
                                <td>'.$value['bot_name'].'</td>
                                <td>'.$value['keywords'].'</td>
                                <td>'.$value['keyword_type'].'</td>
                                <td class="text-center"><a class="btn btn-outline-warning" title="'.$this->lang->line("edit").'" target="_BLANK" href="'.$url.'"><i class="fa fa-edit"></i></a></td>
                            </tr>';
                }
                $response .= '</tbody>
                     </table></div>';
                echo $response;
            }
            else
            {
                $this->basic->delete_data('otn_postback',array('id'=>$id));
                $this->basic->delete_data('otn_optin_subscriber',['otn_id'=>$id]);
                echo "success";
            }
        }

        public function get_otn_postback_dropdown()
        {
            if(!$_POST) exit();
            $page_auto_id=$this->input->post('page_auto_id');// database id

            $postback_id_list = $this->basic->get_data('otn_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)),array('id','otn_postback_id','template_name'));

            $str='';
            $str .="<option value=''>".$this->lang->line("Select")."</option>";
            if(!empty($postback_id_list))
            {            
                foreach ($postback_id_list as  $value)
                {
                    $array_key = $value['id'];
                    $array_value = $value['otn_postback_id']." (".$value['template_name'].")";
                    $str .="<option value='{$array_key}'>{$array_value}</option>";            

                }
            }

            echo json_encode(array('dropdown'=>$str));
        }

        public function get_otn_reply_postback()
        {
            if(!$_POST) exit();
            $page_auto_id=$this->input->post('page_auto_id');// database id

            $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id,'template_for'=>'reply_message','is_template'=>'1')),array('id','postback_id','bot_name'));

            $str='';
            $str .="<option value=''>".$this->lang->line("Select")."</option>";
            if(!empty($postback_id_list))
            {            
                foreach ($postback_id_list as  $value)
                {
                    $array_key = $value['id'];
                    $array_value = $value['postback_id']." (".$value['bot_name'].")";
                    $str .="<option value='{$array_key}'>{$array_value}</option>";            

                }
            }

            echo json_encode(array('dropdown'=>$str));
        }

        public function get_otn_postback_refresh()
        {
            if(!$_POST) exit();
            $is_from_add_button=$this->input->post('is_from_add_button');
            $page_id=$this->input->post('page_id');// database id      
            $order_by=$this->input->post('order_by');     
            if($order_by=="") $order_by="id DESC";
            else $order_by=$order_by." ASC";
            $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>'reply_message')),'','','',$start=NULL,$order_by);
            $push_postback="";

            if($is_from_add_button=='0')
            {
                $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
            }
            
            foreach ($postback_data as $key => $value) 
            {
                $push_postback.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
            }

            if($is_from_add_button=='1' || $is_from_add_button=='')
            {
                $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
            }

            echo $push_postback;   
        }

        public function otn_subscribers($page_id=0,$iframe='0')
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access))
            redirect('home/login_page', 'location');
            $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,'bot_enabled'=>'1')),array('page_name','id'));
            $data['page_info'] = $page_list;
            $data['body'] = 'messenger_tools/otn_manager/subscribers';
            $data['page_title'] = $this->lang->line('OTN Subscribers');
            $data['page_id'] = $page_id;
            $data['iframe'] = $iframe;
            $this->_viewcontroller($data);
        }

        public function otn_subscribers_data()
        {
            $this->ajax_check();
            $page_id = $this->input->post('page_table_id',true);
            $postback_id = $this->input->post('postback_id',true);

            $display_columns = array("#","page_name","first_name","last_name","otn_postback_id","subscriber_id","otn_token","optin_time");

            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 7;
            $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'optin_time';
            $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
            $order_by=$sort." ".$order;

            $where = array();
            $where_simple = array();
            $where_simple['otn_postback.user_id'] = $this->user_id;
            if($page_id != '') $where_simple['otn_postback.page_id'] = $page_id;
            if($postback_id != '') $where_simple['otn_postback.otn_postback_id like'] = "%".$postback_id."%";
            $where = array('where'=>$where_simple);

            $table="otn_optin_subscriber";
            $join = array(
                'messenger_bot_subscriber'=>'otn_optin_subscriber.subscriber_id=messenger_bot_subscriber.subscribe_id,left',
                'otn_postback'=>'otn_optin_subscriber.otn_id=otn_postback.id,left',
                'facebook_rx_fb_page_info'=>'otn_postback.page_id=facebook_rx_fb_page_info.id,left'
            );
            $select = array('otn_optin_subscriber.*','messenger_bot_subscriber.first_name','messenger_bot_subscriber.last_name','otn_postback.user_id','otn_postback.otn_postback_id','facebook_rx_fb_page_info.page_name');
            $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');

            $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
            $total_result=$total_rows_array[0]['total_rows'];

            $data['draw'] = (int)$_POST['draw'] + 1;
            $data['recordsTotal'] = $total_result;
            $data['recordsFiltered'] = $total_result;
            $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

            echo json_encode($data);
        }

    public function get_pagewise_ecommerce_store()
    {
        $this->ajax_check();
        $page_auto_id=$this->input->post('page_auto_id');// database id

        $store_lists = $this->basic->get_data('ecommerce_store',['where'=>['user_id'=>$this->user_id,'page_id'=>$page_auto_id]],['id','store_name','store_city']);

        $str='';
        $str .="<option value=''>".$this->lang->line("Select")."</option>";
        if(!empty($store_lists))
        {            
            foreach ($store_lists as  $value)
            {
                $array_key = $value['id'];
                $array_value = $value['store_name']." - ".$value['store_city'];
                $str .="<option value='{$array_key}'>{$array_value}</option>";            

            }
        }

        echo json_encode(array('dropdown'=>$str));
    } 

    public function get_storewise_products()
    {
        $this->ajax_check();
        $page_auto_id=$this->input->post('page_auto_id');// database id
        $store_id=$this->input->post('store_id');

        $store_lists = $this->basic->get_data('ecommerce_product',['where'=>['user_id'=>$this->user_id,'store_id'=>$store_id]],['id','product_name']);

        $str='';
        $str .="<option value=''>".$this->lang->line("Select")."</option>";
        if(!empty($store_lists))
        {            
            foreach ($store_lists as  $value)
            {
                $array_key = $value['id'];
                $array_value = $value['product_name'];
                $str .="<option value='{$array_key}'>{$array_value}</option>";            

            }
        }

        echo json_encode(array('dropdown'=>$str));
    }

    public function messenger_bot_templates()
	{
		$data = [];
		$data['body'] = "messenger_tools/messenger_bot_templates/template_lists";
		$data['page_title'] = $this->lang->line("Messenger Bot templates");

		// get eligible saved templates
		if($this->db->table_exists('messenger_bot_saved_templates')) 
		{
		    if ($this->session->userdata("user_type")=="Member") 
		    {
		        $package_info=$this->session->userdata('package_info');
		        $search_package_id=isset($package_info['id'])?$package_info['id']:'0';
		        $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public') OR (template_access='private' AND user_id='".$this->user_id."'))";
		    }
		    else $where_custom="user_id='".$this->user_id."'";        

		    $this->db->select('*');
		    $this->db->where( $where_custom );
		    $this->db->order_by("saved_at DESC");
		    $query = $this->db->get('messenger_bot_saved_templates');
		    $template_data=$query->result_array();
		    $data["saved_template_list"]=$template_data;
		}
		else $data["saved_template_list"]=array();

		$this->_viewcontroller($data);
	} 
    


}   