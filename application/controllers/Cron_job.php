<?php
require_once("Home.php");

class Cron_job extends Home
{
    public function __construct()
    {
        parent::__construct();
        $this->upload_path = realpath( APPPATH . '../upload');        
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

    protected function get_fb_rx_config($fb_user_id=0)
    {
        if($fb_user_id==0) return 0;

        $getdata= $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=>$fb_user_id)),array("facebook_rx_config_id"));
        $return_val = isset($getdata[0]["facebook_rx_config_id"]) ? $getdata[0]["facebook_rx_config_id"] : 0;

        return $return_val; 
       
    }


    public function index()
    {
       $this->get_api();
    }


    public function get_api()
    {
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');

        if($this->session->userdata('user_type') != 'Admin')
        redirect('home/login_page', 'location');

        $this->member_validity();

        $data['body'] = "admin/cron_job/command";
        $data['page_title'] = $this->lang->line("Cron Job");
        $this->_viewcontroller($data);
    }

 


    public function api_key_check($api_key="")
    {

        return TRUE;
        if($this->input->is_cli_request()) return TRUE;

        $user_id="";
        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }

        if($api_key=="")
        {        
            echo "API Key is required.";    
            exit();
        }

        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
           echo "API Key does not match with any user.";
           exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "API Key does not match with any authentic user.";
            exit();
        }              
       

    }      


    // =====================SUBSCRIBER & AUTO REPLY/COMMENT===================
    public function background_scanning($api_key="") //background_scanning_update_subscriber_info
    {    
        // $this->api_key_check($api_key);
        $this->load->library('Fb_rx_login');

        $auto_sync_data = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("auto_sync_lead"=>"1")),$select='',$join='',$limit=1,$start=NULL,$order_by='last_lead_sync ASC'); // will work on only one row

        foreach ($auto_sync_data as $key2 => $value2) 
        {          

            $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$value2['id']),array("auto_sync_lead"=>"2")); // making it processing
            $facebook_rx_fb_page_info_id = $value2['id'];
            $get_concersation_info = $this->fb_rx_login->get_all_conversation_page_cron($value2['page_access_token'],$value2['page_id'],$scan_limit=1000,$value2["next_scan_url"]); // will get 1000 lead per cron call

            $package_info = $this->basic->get_data("users",array('where'=>array('users.id'=>$value2['user_id'])),array('package_id','user_type','module_ids','monthly_limit'),array('package'=>"package.id=users.package_id,left"));
            if(!isset($package_info[0])) exit();
            $module_ids = explode(',', $package_info[0]['module_ids']);
            $monthly_limit = json_decode($package_info[0]['monthly_limit'],true);
            $subscriber_limit = $monthly_limit[66] ?? 0;
            // if($package_info[0]['package_id']>0 && in_array(66, $module_ids) && $subscriber_limit>0){
            if($package_info[0]['package_id']>0 && $package_info[0]['user_type']!='Admin' && in_array(66, $module_ids) && $subscriber_limit>0){                
                $total_rows_array = $this->basic->count_row("messenger_bot_subscriber",array('where'=>array('page_table_id'=>$value2['id'],'subscriber_type !='=>'system')));
                $total_result=$total_rows_array[0]['total_rows'] ?? 0;
                $conversation_count = isset($get_concersation_info['message_info']) ? count($get_concersation_info['message_info']) : 0;
                $total_result = $total_result+$conversation_count;
                if($total_result>=$subscriber_limit){
                    $error_insert_data=array("page_id"=>$value2['id'],"fb_page_id"=>$value2['page_id'],"user_id"=>$value2['user_id'],"error_message"=>$this->lang->line("Bot subscriber limit for this page has been exceeded. Bot cannot have more subscribers. Please contact your system admin."),"error_time"=>date("Y-m-d H:i:s"),"media_type"=>'fb');
                    $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                    $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$value2['id']),array("auto_sync_lead"=>"0")); // making it disabled
                    continue;
                }
            }


            $success = 0;
            $total=0;

            $facebook_rx_fb_user_info_id = $value2['facebook_rx_fb_user_info_id'];                
            $db_page_id =  $value2['page_id'];
            $db_user_id =  $value2['user_id'];

            foreach($get_concersation_info['message_info'] as &$item) 
            {                
                $db_client_id  =  $item['id'];
                $db_client_thread_id  =   $item['thead_id'];

                $insert_name=0;
                if($item['name'] != 'Facebook User') $insert_name=1;
                $link = $item['link'];

                $db_client_name  =  $this->db->escape($item['name']);
                $db_permission  =  '1';
                $subscribed_at=date("Y-m-d H:i:s");

                if($insert_name)
                {                
                    // $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscriber(page_table_id,page_id,user_id,client_thread_id,subscribe_id,full_name,permission,subscribed_at,link,is_imported,is_updated_name,is_bot_subscriber) VALUES('$facebook_rx_fb_page_info_id','$db_page_id',$db_user_id,'$db_client_thread_id','$db_client_id',$db_client_name,'$db_permission','$subscribed_at','$link','1','0','0');");

                    $sql="INSERT INTO messenger_bot_subscriber(page_table_id,page_id,user_id,client_thread_id,subscribe_id,full_name,permission,subscribed_at,link,is_imported,is_updated_name,is_bot_subscriber) VALUES('$facebook_rx_fb_page_info_id','$db_page_id',$db_user_id,'$db_client_thread_id','$db_client_id',$db_client_name,'$db_permission','$subscribed_at','$link','1','1','0')
                      ON DUPLICATE KEY UPDATE client_thread_id =  '$db_client_thread_id',link='$link',full_name=$db_client_name";

                    $this->basic->execute_complex_query($sql);

                    if($this->db->affected_rows() != 0) $success++ ;
                    $total++;
                }
            }

            $next_scan_url=$get_concersation_info["next_scan_url"];
            if($next_scan_url=="") $current_state="3";
            else $current_state="1";

            $sql = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$facebook_rx_fb_page_info_id' AND permission='1' AND subscriber_type!='system' AND user_id=".$db_user_id;
            $count_data = $this->db->query($sql)->row_array();

            $sql2 = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE page_table_id='$facebook_rx_fb_page_info_id' AND permission='0' AND subscriber_type!='system' AND user_id=".$db_user_id;
            $count_data2 = $this->db->query($sql2)->row_array();

           // how many are subscribed and how many are unsubscribed
            $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
            $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
            $current_lead_count=$subscribed+$unsubscribed;

            $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$facebook_rx_fb_page_info_id,"facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id),array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed,"last_lead_sync"=>date("Y-m-d H:i:s"),"current_lead_count"=>$current_lead_count,"auto_sync_lead"=>$current_state,"next_scan_url"=>$next_scan_url));
            // echo $this->db->last_query();
        
        } 
    }
    
    public function auto_comment_on_post_orginal($api_key = '')
    {

        //api key need to be checked
        // $this->api_key_check($api_key);

        //load library for commenting
        $this->load->library('fb_rx_login');

        //fetch data from database
        if($this->is_demo == '1')
        $where['where'] = array('auto_comment_reply_info.auto_private_reply_status' => '0', 'auto_comment_reply_info.user_id !='=>1);
        else
        $where['where'] = array('auto_comment_reply_info.auto_private_reply_status' => '0');

        $join = array('auto_comment_reply_tb'=>"auto_comment_reply_info.auto_comment_template_id=auto_comment_reply_tb.id,left");
        $select = array('auto_comment_reply_info.*','auto_comment_reply_tb.auto_reply_comment_text');
        $limit = 10;
        $order_by = 'auto_comment_reply_info.last_updated_at asc';
        $auto_comment_reply_info = $this->basic->get_data('auto_comment_reply_info', $where, $select, $join, $limit, "", $order_by);

        if(count($auto_comment_reply_info) == 0) 
            return; 

        //update campaign status and create page access token's array
        $page_info_table_list = array();
        $campaign_post_id_info = array();
        $campaign_post_info = array();

        $existing_campaigns = [];

        foreach ($auto_comment_reply_info as $single_comment_reply_info) {
            
            // $this->basic->update_data('auto_comment_reply_info', array("id" => $single_comment_reply_info['id']), array("auto_private_reply_status" => '1'));
            array_push($existing_campaigns,$single_comment_reply_info['id']);

            array_push($page_info_table_list, $single_comment_reply_info['page_info_table_id']);
            $campaign_post_id_info[$single_comment_reply_info['id']] = $single_comment_reply_info['page_info_table_id'];
        }

        if(!empty($existing_campaigns))
        {
            $this->db->set('auto_private_reply_status','1');
            $this->db->where_in('id',$existing_campaigns);
            $this->db->update('auto_comment_reply_info');
        }
        
        $page_info_table_list = array_unique($page_info_table_list);


        //page's info array
        $where = array("where_in" => array("facebook_rx_fb_page_info.id" => $page_info_table_list) );
        $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left");
        $select = array("facebook_rx_fb_page_info.*", "facebook_rx_fb_user_info.facebook_rx_config_id","facebook_rx_fb_user_info.access_token");
        $page_info_list = $this->basic->get_data('facebook_rx_fb_page_info',$where, $select, $join);


        //associate page info and other info with campaign id
        foreach ($campaign_post_id_info as $key_id => $page_info_id) {
            
            foreach ($page_info_list as $single_page_info) {
                
                if($page_info_id == $single_page_info['id']){

                    $campaign_post_info[$key_id]['facebook_rx_fb_user_info_id'] = $single_page_info['facebook_rx_fb_user_info_id'];
                    $campaign_post_info[$key_id]['page_access_token'] = $single_page_info['page_access_token'];
                    $campaign_post_info[$key_id]['facebook_rx_config_id'] = $single_page_info['facebook_rx_config_id'];
                    $campaign_post_info[$key_id]['user_access_token'] = $single_page_info['access_token'];

                }
            }
    
        }

        foreach ($auto_comment_reply_info as $single_comment_reply_info) {

            //check if template exists
            if($single_comment_reply_info['auto_reply_comment_text'] == ""){

                $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"2", "error_message" => "Template is missing."));
                continue;
            }
            
            $time_zone = $single_comment_reply_info['time_zone'];
            if($time_zone != '')
              date_default_timezone_set($time_zone);

            $current_time = date("Y-m-d H:i:s");
            $current_value = strtotime($current_time);

            //check comment schedule type
            $comment_schedule_type = $single_comment_reply_info['schedule_type'];

            if($comment_schedule_type == "onetime"){

                //check time
                $schedule_time = $single_comment_reply_info['schedule_time'];
                $compare_value = strtotime($schedule_time);
                if($current_value >= $compare_value){

                    //post comment
                    $this->fb_rx_login->app_initialize($campaign_post_info[$single_comment_reply_info['id']]['facebook_rx_config_id']);

                    $temp_message = $single_comment_reply_info['auto_reply_comment_text'];
                    $temp_message = json_decode($temp_message,true);
                    $message = $temp_message[0];
                    $post_id = $single_comment_reply_info['post_id'];
                    $access_token = $campaign_post_info[$single_comment_reply_info['id']]['page_access_token'];
                    $user_access_token_insta= $campaign_post_info[$single_comment_reply_info['id']]['user_access_token'];

                    try 
                    {

                      if($single_comment_reply_info['social_media_type']=="Facebook")
                      	$response=$this->fb_rx_login->auto_comment($message,$post_id,$access_token);
                      else
                        $response=$this->fb_rx_login->instagram_direct_auto_comment($message,$post_id,$user_access_token_insta);

                      $commentid=isset($response['id'])?$response['id']:"";  

                      $id = $commentid;
                      $comment_text = $message;
                      $comment_time = $current_time;
                      $schedule_type = $comment_schedule_type;
                      $reply_status = "success";

                      $report_data = array();
                      $report_data['id'] = $id;
                      $report_data['comment_text'] = $comment_text;
                      $report_data['comment_time'] = $comment_time;
                      $report_data['schedule_type'] = $schedule_type;
                      $report_data['reply_status'] = $reply_status;

                      $auto_reply_done_info = array();
                      if($single_comment_reply_info['auto_reply_done_info'] != "")
                        $auto_reply_done_info = json_decode($single_comment_reply_info['auto_reply_done_info'],true);
                      array_push($auto_reply_done_info, $report_data);

                      $report = json_encode($auto_reply_done_info);

       
                      $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"2","last_reply_time"=>$current_time,"last_updated_at"=>$current_time, "auto_reply_done_info" => $report, "auto_comment_count" => 1));
                    } 
                    catch (Exception $e) 
                    {
                      $error_msg = $e->getMessage();

                      $id = "";
                      $comment_text = $message;
                      $comment_time = $current_time;
                      $schedule_type = $comment_schedule_type;
                      $reply_status = "failed (".$error_msg.")";

                      $report_data = array();
                      $report_data['id'] = $id;
                      $report_data['comment_text'] = $comment_text;
                      $report_data['comment_time'] = $comment_time;
                      $report_data['schedule_type'] = $schedule_type;
                      $report_data['reply_status'] = $reply_status;

                      $auto_reply_done_info = array();
                      if($single_comment_reply_info['auto_reply_done_info'] != "")
                        $auto_reply_done_info = json_decode($single_comment_reply_info['auto_reply_done_info'],true);
                      array_push($auto_reply_done_info, $report_data);

                      $report = json_encode($auto_reply_done_info);


                      $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"2","last_reply_time"=>$current_time,"last_updated_at"=>$current_time,"error_message"=>$error_msg, "auto_reply_done_info" => $report));
                    }
                    
                }
                else{

                    //update status
                    $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"0"));
                }
            }
            else if($comment_schedule_type == "periodic"){

                //check time
                $campaign_start_time = $single_comment_reply_info['campaign_start_time'];
                $campaign_end_time = $single_comment_reply_info['campaign_end_time'];

                $compare_start = strtotime($campaign_start_time);
                $compare_end = strtotime($campaign_end_time);


                if($current_value >= $compare_start && $current_value <= $compare_end){

                    $comment_start_time = $single_comment_reply_info['comment_start_time'];
                    $comment_end_time = $single_comment_reply_info['comment_end_time'];

                    $comment_start = strtotime($comment_start_time);
                    $comment_end = strtotime($comment_end_time);

                    $current_date_time = date("H:i:s");
                    $current_date_time_value = strtotime($current_date_time);

                    if($current_date_time_value >= $comment_start && $current_date_time_value <= $comment_end)
                    {

                        //check time again
                        $periodic_time = $single_comment_reply_info['periodic_time'];

                        $last_reply_time = $single_comment_reply_info['last_reply_time'];
                        $last_reply_time_value = strtotime($last_reply_time);

                        $temp = ($last_reply_time_value + ($periodic_time * 60) );
                        
                        if($last_reply_time_value == "" || ($temp <= $current_value) ){

                            //post comment
                            $this->fb_rx_login->app_initialize($campaign_post_info[$single_comment_reply_info['id']]['facebook_rx_config_id']);

                            $auto_comment_type = $single_comment_reply_info['auto_comment_type'];
                            $temp_message = $single_comment_reply_info['auto_reply_comment_text'];
                            $temp_message = json_decode($temp_message,true);

                            if($auto_comment_type == "random"){
                                $rand_index = rand(0,(count($temp_message)-1));
                                $message = $temp_message[$rand_index];
                            }
                            else{

                                $periodic_serial_reply_count = $single_comment_reply_info['periodic_serial_reply_count'];
                                if($periodic_serial_reply_count >= count($temp_message))
                                    $periodic_serial_reply_count = 0;

                                $message = $temp_message[$periodic_serial_reply_count];
                                $periodic_serial_reply_count++;
                                
                            }
                            $post_id = $single_comment_reply_info['post_id'];
                            $access_token = $campaign_post_info[$single_comment_reply_info['id']]['page_access_token'];
                            $user_access_token_insta= $campaign_post_info[$single_comment_reply_info['id']]['user_access_token'];

                            try 
                            {

                              if($single_comment_reply_info['social_media_type']=="Facebook")
                              	$response=$this->fb_rx_login->auto_comment($message,$post_id,$access_token);
                              else
                              	 $response=$this->fb_rx_login->instagram_direct_auto_comment($message,$post_id,$user_access_token_insta);

                              $commentid=isset($response['id'])?$response['id']:"";        

                              $auto_comment_count = $single_comment_reply_info['auto_comment_count']; 
                              $auto_comment_count++;

                              $id = $commentid;
                              $comment_text = $message;
                              $comment_time = $current_time;
                              $schedule_type = $comment_schedule_type;
                              $reply_status = "success";

                              $report_data = array();
                              $report_data['id'] = $id;
                              $report_data['comment_text'] = $comment_text;
                              $report_data['comment_time'] = $comment_time;
                              $report_data['schedule_type'] = $schedule_type;
                              $report_data['reply_status'] = $reply_status;

                              $auto_reply_done_info = array();
                              if($single_comment_reply_info['auto_reply_done_info'] != "")
                                $auto_reply_done_info = json_decode($single_comment_reply_info['auto_reply_done_info'],true);
                              array_push($auto_reply_done_info, $report_data);

                              $report = json_encode($auto_reply_done_info);

                              $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"0","last_reply_time"=>$current_time,"last_updated_at"=>$current_time, "auto_comment_count" => $auto_comment_count, "auto_reply_done_info" => $report));

                              //update comment count if necessary
                              if($auto_comment_type == "serially")
                              {

                                $periodic_serial_reply_count = $single_comment_reply_info['periodic_serial_reply_count'];
                                if($periodic_serial_reply_count >= count($temp_message))
                                    $periodic_serial_reply_count = 0;

                                $periodic_serial_reply_count++;

                                $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("periodic_serial_reply_count"=>$periodic_serial_reply_count));
                              }
                            } 
                            catch (Exception $e) 
                            {
                              $error_msg = $e->getMessage();


                              $id = "";
                              $comment_text = $message;
                              $comment_time = $current_time;
                              $schedule_type = $comment_schedule_type;
                              $reply_status = "failed (".$error_msg.")";

                              $report_data = array();
                              $report_data['id'] = $id;
                              $report_data['comment_text'] = $comment_text;
                              $report_data['comment_time'] = $comment_time;
                              $report_data['schedule_type'] = $schedule_type;
                              $report_data['reply_status'] = $reply_status;

                              $auto_reply_done_info = array();
                              if($single_comment_reply_info['auto_reply_done_info'] != "")
                                $auto_reply_done_info = json_decode($single_comment_reply_info['auto_reply_done_info'],true);
                              array_push($auto_reply_done_info, $report_data);

                              $report = json_encode($auto_reply_done_info);


                              $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"0","last_reply_time"=>$current_time,"last_updated_at"=>$current_time,"error_message"=>$error_msg, "auto_reply_done_info" => $report));
                            }
                            //update campaign status
                        }
                        else{

                            //update campaign status
                            $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"0"));
                        }
                    }
                    else{

                        //update campaign status
                        $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"0"));
                    }
                }
                else if($current_value > $compare_end){
                    
                    //update campaign status
                    $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"2"));
                }
                else
                {
                    //update campaign status
                    $this->basic->update_data("auto_comment_reply_info",array("id"=>$single_comment_reply_info['id']),array("auto_private_reply_status"=>"0"));
                }
            }
            
        }
    }
    // =====================SUBSCRIBER & AUTO REPLY/COMMENT===================
    
  
 

    // ===========FACEBOOK POSTER RELATED FUNCTIONS=============
    public function text_image_link_video_post($api_key="") //publish_post
    {
        if($this->is_demo == '1')
        $where['where']=array("posting_status"=>"0", "facebook_rx_auto_post.user_id !="=>1);
        else
        $where['where']=array("posting_status"=>"0");
        /*** Taking fist 200 post for auto post ***/
        $post_info= $this->basic->get_data("facebook_rx_auto_post",$where,$select='',$join='',$limit=25, $start=0, $order_by='schedule_time ASC');

        $database = array();

        $campaign_id_array=array();

        foreach($post_info as $info)
        {
            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            if($time_zone) date_default_timezone_set($time_zone);
            $now_time = date("Y-m-d H:i:s");

            if(strtotime($now_time) < strtotime($schedule_time)) continue;

            $campaign_id_array[] = $info['id'];
        }

        if(empty($campaign_id_array)) exit();

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("facebook_rx_auto_post",array("posting_status"=>"1"));

        $config_id_database = array();
        foreach($post_info as $info)
        {
            $campaign_id= $info['id'];

            if(!in_array($campaign_id, $campaign_id_array)) continue;

            $media_type = $info['media_type'];

            $post_type= $info['post_type'];
            $page_group_user_id= $info["page_group_user_id"];
            $page_or_group_or_user= $info["page_or_group_or_user"];
            $user_id= $info['user_id'];
            $message =$info['message'];
            $link =$info['link'];
            $link_preview_image =$info['link_preview_image'];
            $link_caption =$info['link_caption'];
            $link_description =$info['link_description'];
            $image_url =$info['image_url'];
            $video_title =$info['video_title'];
            $video_url =$info['video_url'];
            $video_thumb_url =$info['video_thumb_url'];
            $link =$info['link'];

            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            // setting fb config id id for library call
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];

            if($media_type == 'instagram')
            {
                $user_infos = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id,"id"=>$fb_rx_fb_user_info_id)));
                $user_access_token = isset($user_infos[0]['access_token']) ? $user_infos[0]['access_token'] : '';
                $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$user_id,"id"=>$page_group_user_id)),['instagram_business_account_id']);
                $instagram_business_account_id = isset($page_info[0]['instagram_business_account_id']) ? $page_info[0]['instagram_business_account_id'] : '';
            }
            else
            {
                $user_access_token = '';
                $instagram_business_account_id = '';
            }

            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            $this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->load->library("fb_rx_login");
            // setting fb confid id for library call


            if($page_or_group_or_user=="page")
            {
                $table_name = "facebook_rx_fb_page_info";
                $fb_id_field =  "page_id";
                $access_token_field =  "page_access_token";
            }
            else if($page_or_group_or_user=="user")
            {
                $table_name = "facebook_rx_fb_user_info";
                $fb_id_field =  "fb_id";
                $access_token_field =  "access_token";
            }
            else
            {
                $table_name = "facebook_rx_fb_group_info";
                $fb_id_field =  "group_id";
                $access_token_field =  "group_access_token";

            }

            if(!isset($database[$page_or_group_or_user][$page_group_user_id])) // if not exists in database
            {
                $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));

                $use_access_token = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";
                $use_fb_id = isset($access_data["0"][$fb_id_field]) ? $access_data["0"][$fb_id_field] : "";

                //inserting new data in database
                $database[$page_or_group_or_user][$page_group_user_id] = array("use_access_token"=>$use_access_token,"use_fb_id"=>$use_fb_id);
            }

            $use_access_token = isset($database[$page_or_group_or_user][$page_group_user_id]["use_access_token"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_access_token"] : "";
            $use_fb_id = isset($database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"] : "";

            $response =array();
            $error_msg ="";
            if($post_type=="text_submit")
            {
                try
                {
                    $response = $this->fb_rx_login->feed_post($message,"","","","","",$use_access_token,$use_fb_id);
                }
                catch(Exception $e)
                {
                    $error_msg = $e->getMessage();
                }
            }

            else if($post_type=="link_submit")
            {
                try
                {
                    $response = $this->fb_rx_login->feed_post($message,$link,"","","","",$use_access_token,$use_fb_id);
                }
                catch(Exception $e)
                {
                    $error_msg = $e->getMessage();
                }
            }

            else if($post_type=="image_submit")
            {
                $image_list = explode(',', $image_url);

                if($media_type == 'instagram')
                {
                    // $image_list[0] = 'https://betterstudio.com/wp-content/uploads/2019/05/4-5-instagram-819x1024.jpg';
                    $message=spintax_process($message);
                    $response = $this->fb_rx_login->instagram_create_post($instagram_business_account_id,$type="IMAGE",$image_list[0],$message,$user_access_token);
                    if(isset($response['status']) && $response['status']=="error"){
                        $error_msg = $response['message'];
                    }
                }
                else
                {
                    if(count($image_list) == 1)
                    {                    
                        try
                        {
                            $response = $this->fb_rx_login->photo_post($message,$image_list[0],"",$use_access_token,$use_fb_id);
                        }
                        catch(Exception $e)
                        {
                            $error_msg = $e->getMessage();
                        }
                    }
                    else
                    {
                        $multi_image_post_response_array = array();
                        $attach_media_array = array();
                        foreach ($image_list as $key => $value) {
                            try
                            {
                                $response = $this->fb_rx_login->photo_post_for_multipost($message,$value,"",$use_access_token,$use_fb_id);
                                $attach_media_array['media_fbid'] = $response['id'];
                                $multi_image_post_response_array[] = $attach_media_array;
                            }
                            catch(Exception $e)
                            {
                                $error_msg = $e->getMessage();
                            }
                        }


                        try
                        {
                            $response = $this->fb_rx_login->multi_photo_post($message,$multi_image_post_response_array,"",$use_access_token,$use_fb_id);
                        }
                        catch(Exception $e)
                        {
                            $error_msg = $e->getMessage();
                        }
                    }
                }

            }

            else
            {
                if($media_type == 'instagram')
                {
                    // $video_url = 'https://xerochat.in/upload/video/new_intro.mp4';
                    $message=spintax_process($message);
                    $response = $this->fb_rx_login->instagram_create_post($instagram_business_account_id,$type="VIDEO",$video_url,$message,$user_access_token);
                    if(isset($response['status']) && $response['status']=="error"){
                        $error_msg = $response['message'];
                    }
                }
                else
                {
                    try
                    {
                        $response = $this->fb_rx_login->post_video($message,$video_title,$video_url,"",$video_thumb_url,"",$use_access_token,$use_fb_id);
                    }
                    catch(Exception $e)
                    {
                        $error_msg = $e->getMessage();
                    }
                }
            }

            if($post_type=="image_submit")
            {
                if($media_type == 'instagram')
                    $object_id = isset($response['id']) ? $response['id'] : '';
                else
                {
                    if(count($image_list) > 1)
                    $object_id=isset($response["id"]) ? $response["id"] : "";
                    else
                    $object_id=isset($response["post_id"]) ? $response["post_id"] : "";
                }
                
            }
            else $object_id=isset($response["id"]) ? $response["id"] : "";

            $temp_data=array();
            try
            {
                if($media_type == 'instagram')
                    $temp_data=$this->fb_rx_login->instagram_get_post_info_by_id($object_id,$user_access_token);
                else
                    $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$use_access_token);
            }
            catch(Exception $e)
            {
                $error_msg1 = $e->getMessage();
            }

            if($media_type == 'instagram')
                $post_url= isset($temp_data["permalink"]) ? $temp_data["permalink"] : "";
            else
                $post_url= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : "";


            if($object_id=="" && $error_msg==""){
                $error_msg=json_encode($response); // added later by Konok to catch up the error in unknown situation 
            }

            $update_data = array("posting_status"=>'2',"full_complete"=>'1',"post_id"=>$object_id,"post_url"=>$post_url,"error_mesage"=>$error_msg,"last_updated_at"=>date("Y-m-d H:i:s"));

            $this->basic->update_data("facebook_rx_auto_post",array("id"=>$campaign_id),$update_data);



            if($info['ultrapost_auto_reply_table_id'] != 0)
            {

                //************************************************//
                $status=$this->_check_usage($module_id=204,$request=1,$user_id);
                if($status!="2" && $status!="3") 
                {

                    $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $info['ultrapost_auto_reply_table_id'] ]]);

                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['id' => $info['page_group_user_id']]]);

                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id')
                                continue;

                            if($auto_key == 'page_ids')
                                continue;

                            if($auto_key == 'ultrapost_campaign_name')
                                $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                            else
                                $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }



                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $fb_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $facebook_page_info[0]['page_name'];

                    if($post_type=="video_submit")
                        $auto_reply_table_data['post_id'] = $facebook_page_info[0]['page_id'].'_'.$object_id;
                    else
                        $auto_reply_table_data['post_id'] = $object_id;

                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;
                    $auto_reply_table_data['auto_private_reply_status'] = '0';

                    $auto_reply_table_data['auto_private_reply_count'] = 0;
                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;

                    $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);
                    $this->_insert_usage_log($module_id=204,$request=1,$user_id);                        
                }
                //************************************************//
            }

            if($info['instagram_reply_template_id'] != 0)
            {

                //************************************************//
                $status=$this->_check_usage($module_id=278,$request=1,$user_id);
                if($status!="2" && $status!="3") 
                {
                    $auto_reply_table_info = $this->basic->get_data('instagram_reply_template',['where'=>['id' => $info['instagram_reply_template_id'] ]]);
                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['id' => $info['page_group_user_id']]]);
                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id' || $auto_key == 'ig_username' || $auto_key == 'page_id')
                                continue;

                            $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }



                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $fb_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $facebook_page_info[0]['page_name'];

                    $auto_reply_table_data['post_id'] = $object_id;
                    $auto_reply_table_data['post_url'] = $post_url;

                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;

                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;

                    $this->basic->insert_data('instagram_reply_autoreply', $auto_reply_table_data);

                 
                    $this->_insert_usage_log($module_id=278,$request=1,$user_id);                        
                 }
                //************************************************//
            }


            sleep(rand ( 1 , 6 ));

        }
    }

    public function cta_post($api_key="") //publish_post
    {
        // $this->api_key_check($api_key);

        // $this->load->library('Fb_rx_login');
        if($this->is_demo == '1')
        $where['where']=array("posting_status"=>"0","facebook_rx_cta_post.user_id !="=>1);
        else
        $where['where']=array("posting_status"=>"0");

        $select="schedule_time,time_zone,cta_value,facebook_rx_cta_post.id as column_id,page_id,page_group_user_id,page_access_token,cta_type,message,facebook_rx_cta_post.ultrapost_auto_reply_table_id,link,link_preview_image,link_description,link_caption,facebook_rx_cta_post.facebook_rx_fb_user_info_id";
        $join=array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=facebook_rx_cta_post.page_group_user_id,left");

        /***    Taking fist 200 post for auto reply ***/
        $post_info= $this->basic->get_data("facebook_rx_cta_post",$where,$select,$join,$limit=30, $start=0,$order_by='schedule_time ASC');

        $campaign_id_array=array();

        foreach($post_info as $info)
        {
            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            if($time_zone) date_default_timezone_set($time_zone);
            $now_time = date("Y-m-d H:i:s");

            if(strtotime($now_time) < strtotime($schedule_time)) continue;

            $campaign_id_array[] = $info['column_id'];
        }

        if(empty($campaign_id_array)) exit();
        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("facebook_rx_cta_post",array("posting_status"=>"1"));

        $config_id_database = array();
        foreach($post_info as $info)
        {

            $page_id =   $info['page_id'];
            $page_access_token = $info['page_access_token'];
            $post_column_id= $info['column_id'];

            if(!in_array($post_column_id, $campaign_id_array)) continue;

            $cta_type = $info["cta_type"];
            $cta_value = $info["cta_value"];
            $message = $info["message"];
            $link = $info["link"];
            $link_preview_image = $info["link_preview_image"];
            $link_caption = $info["link_caption"];
            $link_description = $info["link_description"];

            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            $this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->load->library("fb_rx_login");
            // setting fb confid id for library call

            $response =array();
            $error_msg ="";


            try
            {
                $response = $this->fb_rx_login->cta_post($message, $link,"","",$cta_type,$cta_value,"","",$page_access_token,$page_id);
            }
            catch(Exception $e)
            {
              $error_msg = $e->getMessage();
            }

            $object_id=isset($response["id"]) ? $response["id"] : "";

            $temp_data=array();
            try
            {
                $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$page_access_token);
            }
            catch(Exception $e)
            {
                $error_msg1 = $e->getMessage();
            }

            $post_url= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : "";

            $update_data = array("posting_status"=>'2',"full_complete"=>'1',"post_id"=>$object_id,"post_url"=>$post_url,"error_mesage"=>$error_msg,"last_updated_at"=>date("Y-m-d H:i:s"));

            $this->basic->update_data("facebook_rx_cta_post",array("id"=>$post_column_id),$update_data);


            if($post_info[0]['ultrapost_auto_reply_table_id'] != 0)
            {

                //************************************************//
                $status=$this->_check_usage($module_id=204,$request=1);
                if($status!="2" && $status!="3") 
                {

                    $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $post_info[0]['ultrapost_auto_reply_table_id'] ]]);

                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['id' => $info['page_group_user_id']]]);

                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id')
                                continue;

                            if($auto_key == 'page_ids')
                                continue;

                            if($auto_key == 'ultrapost_campaign_name')
                                $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                            else
                                $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }

                   

                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $fb_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $facebook_page_info[0]['page_name'];
                    $auto_reply_table_data['post_id'] = $object_id;
                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;
                    $auto_reply_table_data['auto_private_reply_status'] = '0';

                    $auto_reply_table_data['auto_private_reply_count'] = 0;
                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;

                    $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

                 
                     $this->_insert_usage_log($module_id=204,$request=1);                        
                 }
                //************************************************//
            }

            sleep(rand ( 1 , 6 ));


        }

    }


    public function carousel_slider_post($api_key="") //publish_post
    {
        // $this->api_key_check($api_key);
        //$this->load->library("fb_rx_login");
        if($this->is_demo == '1')
        $where['where']=array("posting_status"=>"0","facebook_rx_slider_post.user_id !="=>1);
        else
        $where['where']=array("posting_status"=>"0");
        /***   Taking fist 200 post for auto post ***/
        $post_info= $this->basic->get_data("facebook_rx_slider_post",$where,$select='',$join='',$limit=20, $start=0, $order_by='schedule_time ASC');


        $database = array();

        $campaign_id_array = array();

        foreach($post_info as $info)
        {
            $time_zone = $info['time_zone'];
            $schedule_time = $info['schedule_time']; 

            if($time_zone) date_default_timezone_set($time_zone);            
            $now_time = date("Y-m-d H:i:s");
            
            if(strtotime($now_time) < strtotime($schedule_time)) continue; 

            $campaign_id_array[] = $info['id'];       
        }

        if(empty($campaign_id_array)) exit();

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("facebook_rx_slider_post",array("posting_status"=>"1"));
       
        $config_id_database = array();
        $this->load->library("fb_rx_login");
        foreach($post_info as $info)
        {    
            $campaign_id= $info['id'];

            if(!in_array($campaign_id, $campaign_id_array)) continue;

            $post_type= $info['post_type'];
            $page_group_user_id= $info["page_group_user_id"];
            $page_or_group_or_user= $info["page_or_group_or_user"];
            $user_id= $info['user_id'];            
            $message =$info['message'];

            $carousel_content=json_decode($info["carousel_content"],true);
            $carousel_link=$info["carousel_link"];
            $slider_images=json_decode($info["slider_images"],true);
            $slider_image_duration=$info["slider_image_duration"];
            $slider_transition_duration=$info["slider_transition_duration"];
           
            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            //$this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->fb_rx_login->app_initialize($config_id_database[$fb_rx_fb_user_info_id]);
            // setting fb confid id for library call  
            
            if($page_or_group_or_user=="page")
            {
                $table_name = "facebook_rx_fb_page_info";
                $fb_id_field =  "page_id";
                $access_token_field =  "page_access_token";  
            }
            else if($page_or_group_or_user=="user")
            {
                $table_name = "facebook_rx_fb_user_info";
                $fb_id_field =  "fb_id";
                $access_token_field =  "access_token";               
            }
            else
            {
                $table_name = "facebook_rx_fb_group_info`";
                $fb_id_field =  "group_id";
                $access_token_field =  "group_access_token";

            }

            if(!isset($database[$page_or_group_or_user][$page_group_user_id])) // if not exists in database
            {
                $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));
                          
                $use_access_token = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";
                $use_fb_id = isset($access_data["0"][$fb_id_field]) ? $access_data["0"][$fb_id_field] : "";
                
                //inserting new data in database
                $database[$page_or_group_or_user][$page_group_user_id] = array("use_access_token"=>$use_access_token,"use_fb_id"=>$use_fb_id);
            }

            $use_access_token = isset($database[$page_or_group_or_user][$page_group_user_id]["use_access_token"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_access_token"] : "";
            $use_fb_id = isset($database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"] : "";

            $response =array();
            $error_msg ="";
            if($post_type == 'carousel_post') //carousel post
            {
                try
                {
                    $response = $this->fb_rx_login->carousel_post($message,$carousel_link,$carousel_content,"",$use_access_token,$use_fb_id);                    
                }
                catch(Exception $e) 
                {
                    $error_msg = $e->getMessage();
                }
            }
            else // slider post
            {
                try
                {
                    $response = $this->fb_rx_login->post_image_video($message,$slider_images,$slider_image_duration,$slider_transition_duration,"",$use_access_token,$use_fb_id);
                }
                catch(Exception $e) 
                {
                  $error_msg = $e->getMessage();
                }
            } 

            $object_id=isset($response["id"]) ? $response["id"] : "";
            
            $temp_data=array();
            try
            {
                $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$use_access_token);
            }
            catch(Exception $e) 
            {
                $error_msg1 = $e->getMessage();
            }
            
            $post_url= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : "";               

            $update_data = array("posting_status"=>'2',"full_complete"=>'1',"post_id"=>$object_id,"post_url"=>$post_url,"error_mesage"=>$error_msg,"last_updated_at"=>date("Y-m-d H:i:s"));

            $this->basic->update_data("facebook_rx_slider_post",array("id"=>$campaign_id),$update_data);



            if($info['ultrapost_auto_reply_table_id'] != 0)
            {

                //************************************************//
                $status=$this->_check_usage($module_id=204,$request=1);
                if($status!="2" && $status!="3") 
                {

                    $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $info['ultrapost_auto_reply_table_id'] ]]);

                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['id' => $info['page_group_user_id']]]);

                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id')
                                continue;

                            if($auto_key == 'page_ids')
                                continue;

                            if($auto_key == 'ultrapost_campaign_name')
                                $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                            else
                                $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }



                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $fb_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $facebook_page_info[0]['page_name'];

                    if($post_type=="slider_post")
                        $auto_reply_table_data['post_id'] = $facebook_page_info[0]['page_id'].'_'.$object_id;
                    else
                        $auto_reply_table_data['post_id'] = $object_id;

                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;
                    $auto_reply_table_data['auto_private_reply_status'] = '0';

                    $auto_reply_table_data['auto_private_reply_count'] = 0;
                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;

                    $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

               
                     $this->_insert_usage_log($module_id=204,$request=1);                        
                 }
                //************************************************//
            }

            sleep(rand ( 1 , 6 ));


        }
     
    }


    public function rss_auto_post($api_key="") //publish_post
    {
        // $this->api_key_check($api_key);

        $cron_limit=5;

        $feed_data=$this->basic->get_data("autoposting",array("where"=>array("cron_status"=>"0","status"=>"1")),'','',$cron_limit,NULL,'last_updated_at ASC');

        $all_feed_id=array();
        foreach ($feed_data as $key => $value) 
        {   
            $user_id=isset($value['user_id'])?$value['user_id']:'';

            $where['where'] = array('id'=>$user_id);
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$user_id)),$select="package.price as price",$join=array('package'=>"users.package_id=package.id,left"));
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            exit;

            if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1"))) // cancelinng inactive users feeds so that they does not start again
            {
                $this->basic->update_data("autoposting",array("id"=>$value['id']),array("status"=>"2","last_updated_at"=>date("Y-m-d H:i:s")));
                continue;
            }
            $all_feed_id[]=$value['id'];
        }
        if(empty($all_feed_id)) exit(); // stop, no data found

        $this->db->where_in("id",$all_feed_id);
        $this->db->update("autoposting",array("cron_status"=>"1","last_updated_at"=>date("Y-m-d H:i:s")));

        $datetime=date("Y-m-d H:i:s");
        
        foreach ($feed_data as $key => $value) 
        {            
            $user_id=isset($value['user_id'])?$value['user_id']:'';
            if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1"))) continue; // skipping inactive users feeds
       
            $feed_name=isset($value['feed_name'])?$value['feed_name']:'';
            $last_pub_date=isset($value['last_pub_date'])?$value['last_pub_date']:'';
            $error_log=isset($value['error_message'])?json_decode($value['error_message'],true):array();

            $posting_message=isset($value['posting_message'])?$value['posting_message']:"";
            $posting_start_time=isset($value['posting_start_time'])?$value['posting_start_time']:"00:00";
            $posting_end_time=isset($value['posting_end_time'])?$value['posting_end_time']:"23:59";
            $posting_timezone=isset($value['posting_timezone'])?$value['posting_timezone']:"";
            if($posting_timezone=="") $posting_timezone=$this->config->item("time_zone");

            $broadcast_start_time=isset($value['broadcast_start_time'])?$value['broadcast_start_time']:"00:00";
            $broadcast_end_time=isset($value['broadcast_end_time'])?$value['broadcast_end_time']:"23:59";
            $broadcast_timezone=isset($value['broadcast_timezone'])?$value['broadcast_timezone']:"";
            if($broadcast_timezone=="") $broadcast_timezone=$this->config->item("time_zone");
            $broadcast_notification_type=isset($value['broadcast_notification_type'])?$value['broadcast_notification_type']:"REGULAR";
            $broadcast_display_unsubscribe=isset($value['broadcast_display_unsubscribe'])?$value['broadcast_display_unsubscribe']:"0";

            $feed = [];
            $feed_type_uppercase = '';
            $feed_type = $value['feed_type'];

            if ('rss' == $feed_type) {
                $feed_type_uppercase = 'RSS';
                $feed_url = isset($value['feed_url']) ? $value['feed_url'] : '';

                $this->load->library("rss_feed");
                $feed = $this->rss_feed->getFeed($feed_url);
            } elseif ('youtube' == $feed_type) {
                $feed_type_uppercase = 'YouTube Video';
                $youtube_channel_id = isset($value['youtube_channel_id'])
                    ? trim($value['youtube_channel_id'])
                    : '';

                $google_settings = $this->basic->get_data('login_config', '', ['api_key']);
                $youtube_api_key = isset($google_settings[0]['api_key']) 
                    ? $google_settings[0]['api_key'] 
                    : '';

                if (! $youtube_api_key) {
                    $error_message = $this->lang->line("API key is not set for YouTube Data API.");
                    $error_message .= " [{$feed_type_uppercase}]";
                    $error_row = array("time"=>$datetime,"message"=>$error_message);
                    array_push($error_log, $error_row);
                    $this->basic->update_data("autoposting",array("id"=>$value['id']),array("cron_status"=>"0","last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));
                    continue;
                }

                $youtube_api_called_at = $value['youtube_api_called_at'];
                $called_at = date('d-m-Y H:i:s', strtotime($youtube_api_called_at));
                $called_at = strtotime($called_at) + (2 * 60 * 60);

                if ($called_at < time()) {
	                $this->load->library('youtube_channel');
	                $feed = $this->youtube_channel->get_videos($youtube_channel_id, $youtube_api_key);
					$this->basic->update_data("autoposting",array("id"=>$value['id']),array("youtube_api_called_at"=>$datetime));
                } else {
                	continue;
                }

            } elseif ('wordpress' == $feed_type) {
                $feed_type_uppercase = 'Wordpress Post';
                $wordpress_blog_url = isset($value['wordpress_blog_url'])
                    ? $value['wordpress_blog_url']
                    : '';

                if (! $wordpress_blog_url) {
                    $error_message = $this->lang->line("Wordpress blog url is not set up.");
                    $error_message .= " [{$feed_type_uppercase}]";
                    $error_row = array("time"=>$datetime,"message"=>$error_message);
                    array_push($error_log, $error_row);
                    $this->basic->update_data("autoposting",array("id"=>$value['id']),array("cron_status"=>"0","last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));
                    continue;
                }

                $this->load->library('wordpress_post');
                $feed = $this->wordpress_post->get_posts($wordpress_blog_url);      
            }

            if(!isset($feed['success']) || $feed['success']!='1') // stop if get error while getting feed
            {
                $error_message = isset($feed['error_message'])?$feed['error_message']:$this->lang->line("Something went wrong while fetching feed data.");
                $error_message .= " [{$feed_type_uppercase}]";
                $error_row = array("time"=>$datetime,"message"=>$error_message);
                array_push($error_log, $error_row);
                $this->basic->update_data("autoposting",array("id"=>$value['id']),array("cron_status"=>"0","last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));
                continue;
            }           

            // $new_last_pub_title=isset($feed['element_list'][0]['title'])?$feed['element_list'][0]['title']:"";
            // $new_last_pub_url=isset($feed['element_list'][0]['link'])?$feed['element_list'][0]['link']:"";
            // $new_last_pub_date=$last_pub_date;
            date_default_timezone_set('Europe/Dublin'); // operating in GMT
            // $new_last_pub_date=isset($feed['element_list'][0]['pubDate'])?$feed['element_list'][0]['pubDate']:"";
            // if($new_last_pub_date!="") $new_last_pub_date=date("Y-m-d H:i:s",strtotime($new_last_pub_date));

            $element_list=isset($feed['element_list'])?$feed['element_list']:array();
            $element_list=array_reverse($element_list);

            $valid_feed=0;
            $new_last_pub_title="";
            $new_last_pub_url="";
            $new_last_pub_date="";

            foreach($element_list as $key2 => $value2) // how many latest feed there will be
            {
                $pub_date=isset($value2['pubDate'])?$value2['pubDate']:"";
                $pub_date=date("Y-m-d H:i:s",strtotime($pub_date));
                if(strtotime($pub_date)>strtotime($last_pub_date)) $valid_feed++;

                if($value2['pubDate']!="")
                if($new_last_pub_date=="" || (strtotime($value2['pubDate'])>strtotime($new_last_pub_date)))
                {
                    $new_last_pub_date=isset($value2['pubDate'])?$value2['pubDate']:"";
                    $new_last_pub_date=date("Y-m-d H:i:s",strtotime($new_last_pub_date));
                    $new_last_pub_title=isset($value2['title'])?$value2['title']:"";
                    $new_last_pub_url=isset($value2['link'])?$value2['link']:"";
                }
            }

            if($valid_feed==0) // stop cron if no latest feed found
            {
                $time_zone = $this->config->item('time_zone');
                if($time_zone== '') $time_zone="Europe/Dublin";
                date_default_timezone_set($time_zone);
                $this->db->where_in("id",$all_feed_id);
                $this->db->update("autoposting",array("cron_status"=>"0","last_updated_at"=>date("Y-m-d H:i:s")));
                continue;
            }

            // posting time calculation
            date_default_timezone_set($posting_timezone);
            $current_datetime=date("Y-m-d H:i:s");
            $current_date=date("Y-m-d");
            $current_time=date("H:i");

            $temp0 = (float) str_replace(':','.',$current_time);
            $temp1 = (float) str_replace(':','.',$posting_start_time);
            $temp2 = (float) str_replace(':','.',$posting_end_time);
            $temp_difference = $temp2-$temp1;
            $temp_hour_min=ceil($temp_difference)*60;
            $temp_min=$temp_difference-ceil($temp_difference);
            $temp_min=number_format((float)$temp_min, 2, '.', '');
            $available_min=$temp_hour_min+$temp_min;
            $gap_minute=round($available_min/$valid_feed); // say we have 120 min time span and have 10 valid feed, then campaigns will be scheduled every 12 minutes

            $post_schedule_time="";

            if($temp0>=$temp1 && $temp0<=$temp2) // matches time slot
            {
                $post_schedule_time = strtotime($current_datetime.' + 2 minute');
                $post_schedule_time = date('Y-m-d H:i:s', $post_schedule_time);
            }
            else
            {
                $make_date=$current_date." ".$posting_start_time.":00";
                if(strtotime($make_date)<strtotime($current_datetime)) // if start time is less than current time then we will schedule it next day
                {
                    $post_schedule_time = strtotime($make_date.' + 1 day');
                    $post_schedule_time = date('Y-m-d H:i:s', $post_schedule_time);
                }
                else $post_schedule_time=$make_date;
            }            
            $post_gap_minute=0;
            // posting time calculation


            // broadcast time calculation
            date_default_timezone_set($broadcast_timezone);
            $broadcast_current_datetime=date("Y-m-d H:i:s");
            $broadcast_current_date=date("Y-m-d");
            $broadcast_current_time=date("H:i");

            $broadcast_temp0 = (float) str_replace(':','.',$broadcast_current_time);
            $broadcast_temp1 = (float) str_replace(':','.',$broadcast_start_time);
            $broadcast_temp2 = (float) str_replace(':','.',$broadcast_end_time);
            $broadcast_temp_difference = $broadcast_temp2-$broadcast_temp1;
            $broadcast_temp_hour_min=ceil($broadcast_temp_difference)*60;
            $broadcast_temp_min=$broadcast_temp_difference-ceil($broadcast_temp_difference);
            $broadcast_temp_min=number_format((float)$broadcast_temp_min, 2, '.', '');
            $broadcast_available_min=$broadcast_temp_hour_min+$broadcast_temp_min;
            $gap_minute2=round($broadcast_available_min/$valid_feed); // say we have 120 min time span and have 10 valid feed, then campaigns will be scheduled every 12 minutes

            $broadcast_schedule_time="";

            if($broadcast_temp0>=$broadcast_temp1 && $broadcast_temp0<=$broadcast_temp2) // matches time slot
            {
                $broadcast_schedule_time = strtotime($broadcast_current_datetime.' + 2 minute');
                $broadcast_schedule_time = date('Y-m-d H:i:s', $broadcast_schedule_time);
            }
            else
            {
                $broadcast_make_date=$broadcast_current_date." ".$broadcast_start_time.":00";
                if(strtotime($broadcast_make_date)<strtotime($broadcast_current_datetime)) // if start time is less than current time then we will schedule it next day
                {
                    $broadcast_schedule_time = strtotime($broadcast_make_date.' + 1 day');
                    $broadcast_schedule_time = date('Y-m-d H:i:s', $broadcast_schedule_time);
                }
                else $broadcast_schedule_time=$broadcast_make_date;
            }            
            $broadcast_gap_minute=0;
            // broadcast time calculation

            $times = 0;
            foreach($element_list as $key2 => $value2) 
            {
                $times++;
                date_default_timezone_set('Europe/Dublin'); // operating in GMT
                $pub_date=isset($value2['pubDate'])?$value2['pubDate']:"";
                $pub_date=date("Y-m-d H:i:s",strtotime($pub_date));

                if(strtotime($pub_date)>strtotime($last_pub_date)) // only work with recent feed
                {                    
                    if($valid_feed>3) 
                    {
                        $post_gap_minute+=$gap_minute; 
                        $broadcast_gap_minute+=$gap_minute2;
                    }
                    else
                    {
                        $post_gap_minute+=15; 
                        $broadcast_gap_minute+=15;
                    }

                    $post_feed_url=isset($value2['link'])?$value2['link']:"";   
                    $post_feed_title = isset($value2['title'])? htmlspecialchars_decode($value2['title'], ENT_QUOTES) : "";  
                    $temp_posting_message = str_replace('#TITLE#', $post_feed_title, $posting_message); 

                    // processing facebook post
                    $page_ids = isset($value['page_ids'])?explode(',', $value['page_ids']):array();
                    $facebook_rx_fb_user_info_ids = isset($value['facebook_rx_fb_user_info_ids'])?json_decode($value['facebook_rx_fb_user_info_ids'],true):array();
                    $page_names = isset($value['page_names'])?json_decode($value['page_names'],true):array();
                    $request_count=count(array_filter($page_ids));

                    if($request_count>0)
                    {
                        $status=$this->_check_usage($module_id=223,$request_count,$user_id);
                        if($status=="3")
                        {
                            $this->basic->update_data("autoposting",array("id"=>$value['id']),array("error_message"=>$error_message));  

                            $error_message = $this->lang->line("Your monthly limit for Facebook posting module has been exceeded.");  
                            $error_message.=" [Facebook Posting]";
                            $error_row=array("time"=>$datetime,"message"=>$error_message);
                            array_push($error_log, $error_row);
                            $this->basic->update_data("autoposting",array("id"=>$value['id']),array("last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));               
                        }
                        else
                        {                            
                            foreach($page_ids as $key3 => $value3) 
                            {                               
                               $facebook_rx_fb_user_info_id=isset($facebook_rx_fb_user_info_ids[$value3])?$facebook_rx_fb_user_info_ids[$value3]:0;
                               $page_or_group_or_user_name=isset($page_names[$value3])?$page_names[$value3]:"";

                               $post_schedule_time_gapped=$post_schedule_time;
                               if($valid_feed<=3) // if there is a small amount of feeds then we will try to post in first hour
                               {
                                   $post_schedule_time_gapped = strtotime($post_schedule_time.' + '.$post_gap_minute.' minute');
                                   $post_schedule_time_gapped = date('Y-m-d H:i:s', $post_schedule_time_gapped);
                               }
                               else // if there is a large amount of feeds then we will try to span the feed post process to cover whole timeslot
                               {
                                   if($post_gap_minute>0)
                                   {
                                       $post_schedule_time_gapped = strtotime($post_schedule_time.' + '.$post_gap_minute.' minute');
                                       $post_schedule_time_gapped = date('Y-m-d H:i:s', $post_schedule_time_gapped);
                                   }
                                }

                               $create_campaign_data=array
                               (
                                  "user_id"=>$user_id,
                                  "facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id,
                                  "post_type"=>"link_submit",
                                  "campaign_name"=>$feed_name." [{$feed_type_uppercase} Autopost]",
                                  "message"=>$temp_posting_message,
                                  "page_group_user_id"=>$value3,
                                  "page_or_group_or_user"=>"page",
                                  "page_or_group_or_user_name"=>$page_or_group_or_user_name,
                                  "link"=>$post_feed_url,
                                  "posting_status"=>"0",
                                  "last_updated_at"=>$datetime,
                                  "schedule_time"=>$post_schedule_time_gapped,
                                  "time_zone"=>$posting_timezone,
                                  "is_autopost"=>"1"
                               );                             

                               $this->basic->insert_data("facebook_rx_auto_post",$create_campaign_data);
                               $this->_insert_usage_log($module_id=223,$request=1,$user_id);
                            }                            
                            
                        }
                    }
                    // processing facebook post

                    /* processing other social media's post */
                    $twitter_accounts = json_decode($value['twitter_accounts'], true);
                    $linkedin_accounts = json_decode($value['linkedin_accounts'], true);
                    $reddit_accounts = json_decode($value['reddit_accounts'], true);
                    $subreddits = $value['subreddits'];

                    if(!is_array($twitter_accounts)) $twitter_accounts=array();
                    if(!is_array($linkedin_accounts)) $linkedin_accounts=array();
                    if(!is_array($reddit_accounts)) $reddit_accounts=array();


                    $final_accounts_list = array_merge($twitter_accounts, $linkedin_accounts, $reddit_accounts);

                    if (count($final_accounts_list) > 0) {
                    	
                    	$create_campaign_data_1 = array (

                    	   "user_id"=>$user_id,
                    	   "campaign_type"=>'link',
                    	   "campaign_name"=>$feed_name." [{$feed_type_uppercase} Autopost]",
                    	   "title"=>$post_feed_title,
                           "message"=>$temp_posting_message,
                           "link"=>$post_feed_url,
                    	   "subreddits"=> $subreddits,
                    	   "posting_medium"=> json_encode($final_accounts_list),
                    	   "schedule_type"=> 'later',
                    	   "schedule_time"=>$post_schedule_time_gapped,
                    	   "schedule_timezone"=>$posting_timezone,
                    	   "posting_status"=>"pending"
                    	);

                    	$this->basic->insert_data('comboposter_campaigns', $create_campaign_data_1);
                    }
                    /* processing other social media's post */
                }
                
            }

            $this->basic->update_data("autoposting",array("id"=>$value['id']),array("last_pub_date"=>$new_last_pub_date,"last_pub_title"=>$new_last_pub_title,"last_pub_url"=>$new_last_pub_url));
            
        } 

        $time_zone = $this->config->item('time_zone');
        if($time_zone== '') $time_zone="Europe/Dublin";
        date_default_timezone_set($time_zone);
        $this->db->where_in("id",$all_feed_id);
        $this->db->update("autoposting",array("cron_status"=>"0","last_updated_at"=>date("Y-m-d H:i:s")));
    }
    // ===========FACEBOOK POSTER RELATED FUNCTIONS=============



    // ===========MESSENGER BOT RELATED FUNCTIONS=============
    function download_subscriber_avatar($api_key="")
    {   
       // $this->api_key_check($api_key);
       $limit=$this->config->item("messengerbot_subscriber_avatar_download_limit_per_cron_job");
       if($limit=="") $limit=25;
       $subscriber_info = $this->basic->get_data('messenger_bot_subscriber',array('where'=>array('is_image_download'=>'0', 'is_updated_name' => '1','is_bot_subscriber'=>'1')),$select='',$join='',$limit);
        
        foreach($subscriber_info as $info){
        
            $profile_pic_url=$info['profile_pic'];
            $subscribe_id=$info['subscribe_id'];
            $subscribe_auto_id=$info['id'];


            $upload_path="upload/subscriber_pic"; 
            
            if(!file_exists($upload_path))
                mkdir($upload_path,0755);
                
            $user_pic_name=$upload_path."/".$subscribe_id.".png";
        
        
            $content= @file_get_contents($profile_pic_url);
            
            if($content===FALSE){
                
                $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscribe_auto_id),array("is_image_download"=>"1"));
                
            }
            else{
                file_put_contents($user_pic_name,$content);
                $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscribe_auto_id),array("is_image_download"=>"1","image_path"=>$user_pic_name));
            }
        }        
    
    }

    public function update_subscriber_profile_info($api_key="") //background_scanning_update_subscriber_info
    {
        // $this->api_key_check($api_key);
        $this->load->library("fb_rx_login"); 

        $limit=$this->config->item("messengerbot_subscriber_profile_update_limit_per_cron_job");
        if($limit=="") $limit=100;
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber',array('where'=>array('is_updated_name'=>'0','is_bot_subscriber'=>'0')),$select='',$join='',$limit, '', 'last_name_update_time asc');
         
         foreach($subscriber_info as $info){
         
             $subscribe_id=$info['subscribe_id'];
             $subscribe_auto_id=$info['id'];
             $page_id = $info['page_id'];
             $page_table_id = $info['page_table_id'];

             $facebook_rx_fb_page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('id' => $page_table_id)));
             $facebook_rx_fb_page_info = $facebook_rx_fb_page_info[0];
             $access_token = $facebook_rx_fb_page_info['page_access_token'];

             $user_info = $this->subscriber_info($access_token, $subscribe_id); // home controller

             // $xlabels=$this->fb_rx_login->retrieve_level_of_psid($subscribe_id,$access_token);
             $xlabels=[];
            
            // if(isset($xlabels['data']))
            // {
            //   $get_groupdata=$this->basic->get_data('messenger_bot_broadcast_contact_group',array('where'=>array('page_id'=>$page_table_id)));
            //   $label_id=array();
            //   foreach ($get_groupdata as $key => $value) 
            //   {
            //       $label_id[$value['label_id']]=$value['id'];
            //   }
            //   $existing_label_array=array();
            //   foreach ($xlabels['data'] as $key => $value) 
            //   {
            //     if(isset($label_id[$value['id']])) $existing_label_array[]=$label_id[$value['id']];
            //   }
            //   $existing_label_str = implode(',', $existing_label_array);
            // }

             if (!isset($user_info['error'])) {

                 $first_name = isset($user_info['first_name']) ? $user_info['first_name'] : "";
                 $last_name = isset($user_info['last_name']) ? $user_info['last_name'] : "";
                 $profile_pic = isset($user_info['profile_pic']) ? $user_info['profile_pic'] : "";
                 $gender = isset($user_info['gender']) ? $user_info['gender'] : "";
                 $locale = isset($user_info['locale']) ? $user_info['locale'] : "";
                 $timezone = isset($user_info['timezone']) ? $user_info['timezone'] : "";
                 $full_name = isset($user_info['name']) ? $user_info['name'] : "";


                 if ($first_name != "") {

                     $data = array(
                         'first_name' => $first_name,
                         'last_name' => $last_name,
                         'profile_pic' => $profile_pic,
                         'is_updated_name' => '1',
                         'is_bot_subscriber' => '1',
                         'gender'=>$gender,
                         'locale'=>$locale,
                         'timezone'=>$timezone,
                         'last_name_update_time' => date('Y-m-d H:i:s')
                     );
                     if($full_name!="") $data["full_name"] = $full_name;

                     $this->basic->update_data('messenger_bot_subscriber', array('id' => $subscribe_auto_id), $data);
                 }
                 else 
                 {
                    $data = array('is_updated_name' => '1','is_bot_subscriber' => '0','last_name_update_time' => date('Y-m-d H:i:s')
                     );
                     $this->basic->update_data('messenger_bot_subscriber', array('id' => $subscribe_auto_id), $data);
                 }

                
             }
             else 
             {
                $data = array('is_updated_name' => '1','is_bot_subscriber' => '0','last_name_update_time' => date('Y-m-d H:i:s')
                 );
                 $this->basic->update_data('messenger_bot_subscriber', array('id' => $subscribe_auto_id), $data);
             }
             
         }
    }
    // ===========MESSENGER BOT RELATED FUNCTIONS=============



    public function subscriber_broadcaster($api_key="") // braodcast_message
    {
        // $this->api_key_check($api_key);
        $broadcaster_number_of_message_to_be_sent_in_try=$this->config->item("broadcaster_number_of_message_to_be_sent_in_try");
        if($broadcaster_number_of_message_to_be_sent_in_try==0) $broadcaster_number_of_message_to_be_sent_in_try="";
        $broadcaster_update_report_after_time=$this->config->item("broadcaster_update_report_after_time"); 
        if($broadcaster_update_report_after_time=="" || $broadcaster_update_report_after_time==0) $broadcaster_update_report_after_time=10;
        $number_of_campaign_to_be_processed = 1; // max number of campaign that can be processed by this cron job
        // $number_of_message_tob_be_sent = 50000;  // max number of message that can be sent in an hour


        $subscriber_broadcaster_hold_after_number_of_errors=$this->config->item("subscriber_broadcaster_hold_after_number_of_errors");
        if($subscriber_broadcaster_hold_after_number_of_errors=="" || $subscriber_broadcaster_hold_after_number_of_errors==0) 
            $subscriber_broadcaster_hold_after_number_of_errors=30; // default 10



        /****** Get all campaign from database where status=0 means pending ******/
        $where_str = " (posting_status='0' OR is_try_again='1') AND posting_status!='3'";
        $this->db->where($where_str);
        $join = array('users'=>'messenger_bot_broadcast_serial.user_id=users.id,left');
        $campaign_info= $this->basic->get_data("messenger_bot_broadcast_serial",$where='',$select=array("messenger_bot_broadcast_serial.*","users.deleted as user_deleted","users.status as user_status","users.user_type as user_type"),$join,$limit=50, $start=0, $order_by='schedule_time ASC');  

        $page_ids_names=array();
        $access_token_database_database=array();
        $facebook_rx_fb_user_info_id_database=array();
        $campaign_id_array=array();  // all selected campaign id array
        $campaign_info_fildered = array(); // valid for process, campign info array

        $valid_campaign_count = 1;
        foreach($campaign_info as $info)
        {
            if($this->is_demo=='1' && $info['user_type']=="Admin")
            {
                $this->db->where("id",$info['id']);
                $this->db->update("messenger_bot_broadcast_serial",array("posting_status"=>"1","is_try_again"=>"0"));
                continue;
            }
            if($info['user_deleted'] == '1' || $info['user_status']=="0")
            {
                $this->db->where("id",$info['id']);
                $this->db->update("messenger_bot_broadcast_serial",array("posting_status"=>"1","is_try_again"=>"0"));
                continue;
            }

            $campaign_id= $info['id'];
            $time_zone= $info['timezone'];
            $schedule_time= $info['schedule_time']; 
            $total_thread = $info["total_thread"];
            $page_id =$info["page_id"]; // auto ids
            $fb_page_id =$info["fb_page_id"]; 
            $user_id = $info["user_id"];                  

            if($time_zone) date_default_timezone_set($time_zone);            
            $now_time = date("Y-m-d H:i:s");

            if((strtotime($now_time) < strtotime($schedule_time)) && $time_zone!="") continue; 
            if($valid_campaign_count > $number_of_campaign_to_be_processed) break; 

            // get access token and fb user id
            $token_info =  $this->basic->get_data('facebook_rx_fb_page_info',array("where"=>array('id'=>$page_id,'user_id'=>$user_id)));
            foreach ($token_info as $key => $value) 
            {
                $access_token_database_database[$campaign_id][$value["id"]] = $value['page_access_token'];
                $facebook_rx_fb_user_info_id = $value["facebook_rx_fb_user_info_id"];
                $facebook_rx_fb_user_info_id_database[$campaign_id] = $facebook_rx_fb_user_info_id;
                $page_ids_names[$value["id"]] = $value["page_name"];
            }

            // valid campaign info and campig ids
            $campaign_info_fildered[] = $info;
            $campaign_id_array[] = $info['id']; 
            $valid_campaign_count++;      
        }

        if(count($campaign_id_array)==0) exit();        

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("messenger_bot_broadcast_serial",array("posting_status"=>"1","is_try_again"=>"0"));

        // get config id
        $getdata= $this->basic->get_data("facebook_rx_fb_user_info",array("where_in"=>array("id"=>$facebook_rx_fb_user_info_id_database)),array("id","facebook_rx_config_id"));
        foreach ($getdata as $key => $value) 
        {
            $facebook_rx_config_id_database[$value["id"]] = $value["facebook_rx_config_id"];
        } 

        $this->load->library("fb_rx_login"); 

        // send message
        foreach($campaign_info_fildered as $info)
        {
            $campaign_id= $info['id'];            
            $user_id = $info["user_id"];           
            $catch_error_count=$info["last_try_error_count"];
            $successfully_sent=$info["successfully_sent"];
            $successfully_delivered=$info["successfully_delivered"];
 
            $fb_rx_fb_user_info_id = $facebook_rx_fb_user_info_id_database[$campaign_id]; // find gb user id for this campaign
            $this->fb_rx_login->app_initialize($facebook_rx_config_id_database[$fb_rx_fb_user_info_id]);

    
            $i=0;
        
            $campaign_lead=$this->basic->get_data("messenger_bot_broadcast_serial_send",array("where"=>array("campaign_id"=>$campaign_id,"processed"=>"0")),'','',$broadcaster_number_of_message_to_be_sent_in_try);

            // echo "<pre>".$this->db->last_query(); 
            // print_r($campaign_lead); 

            foreach($campaign_lead as $key => $value) 
            {
                if($catch_error_count>$subscriber_broadcaster_hold_after_number_of_errors)  // if 30 catch block error then stop sending, mark as complete
                {
                    $this->basic->update_data("messenger_bot_broadcast_serial",array("id"=>$campaign_id),array("posting_status"=>'4','successfully_sent'=>$successfully_sent,'completed_at'=>date("Y-m-d H:i:s"),"error_message"=>$error_msg,"is_try_again"=>"0","last_try_error_count"=>$catch_error_count));
                    break;
                }
                $campaign_message_send=$info["message"];
                $page_id_send  = $value["page_id"];
                $send_table_id = $value['id'];
                $subscribe_id = $value['subscribe_id'];
                $subscribeauto_id = $value['subscriber_auto_id'];
                $client_first_name = $value['subscriber_name'];
                $client_last_name = $value['subscriber_lastname'];
                $client_otn_token=$value['otn_token']; // if OTN Campaign
                
                $error_msg="";
                $message_error_code = "";
                $message_sent_id = "";

                if(!isset($access_token_database_database[$campaign_id][$page_id_send])) continue;
                $page_access_token_send = $access_token_database_database[$campaign_id][$page_id_send]; // get access toke from our access token database

                //  generating message
                $campaign_message_send = str_replace('{{first_name}}',$client_first_name,$campaign_message_send);
                $campaign_message_send = str_replace('{{last_name}}',$client_last_name,$campaign_message_send);
                $replace_search=array('PUT_SUBSCRIBER_ID','#SUBSCRIBER_ID_REPLACE#');
                $campaign_message_send=str_replace($replace_search, $subscribe_id, $campaign_message_send);

                if($client_otn_token!="")
                    $campaign_message_send = str_replace('PUT_OTN_TOKEN',$client_otn_token,$campaign_message_send);


                // print_r($campaign_message_send); continue;

                $message_sent_id="";
                $now_sent_time=date("Y-m-d H:i:s");  
                $deliveryTime=''; 
                $isDelivered='0';  
                $successfully_sent++;    
                try
                {
                    // $campaign_message_send = spintax_process($campaign_message_send);
                    $response = $this->fb_rx_login->send_non_promotional_message_subscription($campaign_message_send,$page_access_token_send);

                    if(isset($response['message_id']))
                    {
                        $message_sent_id = $response['message_id']; 
                        $successfully_delivered++;
                        $deliveryTime=date("Y-m-d H:i:s");
                        $isDelivered="1";
                    }
                    else 
                    {
                        if(isset($response["error"]["message"])) $message_sent_id = $response["error"]["message"];  
                        if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"];

                        if($message_error_code=="551") // unvalilable user
                        {
                            $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscribeauto_id),array("unavailable"=>"1","last_error_message"=>$message_sent_id));
                        }
                        else
                        {
                            $error_msg = $message_sent_id;
                            $catch_error_count++;
                        }                    
                    }              
                    
                }

                catch(Exception $e) 
                {
                  $error_msg = $e->getMessage();
                  $catch_error_count++;
                }

                // generating new report with send message info

                $i++;  
                // after 10 send update report in database
                if($i%$broadcaster_update_report_after_time==0)
                {
                    $this->basic->update_data("messenger_bot_broadcast_serial",array("id"=>$campaign_id),array('successfully_sent'=>$successfully_sent,"successfully_delivered"=>$successfully_delivered,"error_message"=>$error_msg,"last_try_error_count"=>$catch_error_count));
                }
       
                // updating a lead, marked as processed
                $this->basic->update_data("messenger_bot_broadcast_serial_send",array("id"=>$send_table_id),array('processed'=>'1',"sent_time"=>$now_sent_time,"delivered"=>$isDelivered,"delivery_time"=>$deliveryTime,"message_sent_id"=>$message_sent_id));
            
            } 
        

            // one campaign completed, now update database finally
            if((count($campaign_lead)<$broadcaster_number_of_message_to_be_sent_in_try) || $broadcaster_number_of_message_to_be_sent_in_try=="" || $catch_error_count>$subscriber_broadcaster_hold_after_number_of_errors)
            { 
                $new_posting_status = ($catch_error_count>$subscriber_broadcaster_hold_after_number_of_errors) ? '4' : '2';
                $complete_update=array("posting_status"=>$new_posting_status,'successfully_sent'=>$successfully_sent,"successfully_delivered"=>$successfully_delivered,'completed_at'=>date("Y-m-d H:i:s"),"is_try_again"=>"0","last_try_error_count"=>$catch_error_count);
                if(isset($error_msg))
                $complete_update["error_message"]=$error_msg;
                $this->basic->update_data("messenger_bot_broadcast_serial",array("id"=>$campaign_id),$complete_update);
            }
            else // suppose broadcaster_update_report_after_time=20 but there are 19 message to sent, need to update report in that case
            {
                $this->basic->update_data("messenger_bot_broadcast_serial",array("id"=>$campaign_id),array('successfully_sent'=>$successfully_sent,"successfully_delivered"=>$successfully_delivered,"is_try_again"=>"1"));
            }
        }          
   
    }
    // ===========MESSENGER BROADCASTER RELATED FUNCTIONS=============




    // ===========MESSENGER DRIP RELATED FUNCTIONS=============
    public function sequence_message_broadcast_hourly($api_key="")
    { 
        // $this->api_key_check($api_key);
        $number_of_row=100; // number of subscriber on cron will process

        // reseting to system timezone
        $time_zone = $this->config->item('time_zone');
        if($time_zone== '') $time_zone="Europe/Dublin";        
        date_default_timezone_set($time_zone);
        
        $page_database=array(); // associated page auto id and page id

        // getting eligible subscriber data
        $subscriber_where=
        array
        (
            "where"=>array
            (
                "messenger_bot_drip_campaign_id !="=>"0",
                "messenger_bot_drip_initial_date !="=>"0000-00-00 00:00:00",
                "messenger_bot_drip_is_toatally_complete_hourly"=>"0",
                "messenger_bot_drip_processing_status_hourly"=>"0"
            ),
        );
        $join = array('messenger_bot_subscriber'=>"messenger_bot_subscriber.subscribe_id=messenger_bot_drip_campaign_assign.subscribe_id,Left",
                      'messenger_bot_drip_campaign'=>"messenger_bot_drip_campaign.id=messenger_bot_drip_campaign_assign.messenger_bot_drip_campaign_id,Left",
                      'facebook_rx_fb_page_info'=>'facebook_rx_fb_page_info.id=messenger_bot_drip_campaign_assign.page_table_id,Left',
                      'sms_email_contacts'=>'sms_email_contacts.id=messenger_bot_drip_campaign_assign.subscribe_id,Left'
                    );

        $subscriber_data=$this->basic->get_data("messenger_bot_drip_campaign_assign",$subscriber_where,'messenger_bot_drip_campaign_assign.*,
            messenger_bot_subscriber.permission,
            messenger_bot_subscriber.page_id,
            messenger_bot_subscriber.first_name,
            messenger_bot_subscriber.last_name,
            messenger_bot_subscriber.full_name,
            messenger_bot_subscriber.email,
            messenger_bot_subscriber.phone_number, 
            messenger_bot_subscriber.is_email_unsubscriber, 
            messenger_bot_drip_campaign.message_content_hourly,
            campaign_type,
            external_sequence_sms_api_id,external_sequence_email_api_id,
            facebook_rx_fb_page_info.page_access_token,
            facebook_rx_fb_page_info.page_id,
            facebook_rx_fb_page_info.sequence_sms_api_id,
            facebook_rx_fb_page_info.sequence_email_api_id, 
            sms_email_contacts.first_name as external_first_name,
            sms_email_contacts.last_name as external_last_name,
            sms_email_contacts.phone_number as external_phone_number,
            sms_email_contacts.email as external_email,
            sms_email_contacts.unsubscribed as external_unsubscribed
        ',$join,$number_of_row,NULL,'last_processing_started_at_hourly ASC');

        if(empty($subscriber_data)) exit();

        $subscriber_data_ids = array();
        foreach ($subscriber_data as $key_sub => $value_sub)
        {
            $subscriber_data_ids[] = $value_sub['id'];
        }
       

        $this->load->library("fb_rx_login"); 

        // marking subscribers this cron is operating as processing (comment this query while test)
        if(count($subscriber_data_ids)>0)
        {
            $this->db->where_in("id",$subscriber_data_ids);
            $this->db->update("messenger_bot_drip_campaign_assign", array('messenger_bot_drip_processing_status_hourly' => "1","last_processing_started_at_hourly"=>date("Y-m-d H:i:s")));
        }


        foreach ($subscriber_data as $key => $value) 
        {            
            
            $is_external_contact=0;

            $user_id=$value["user_id"];
            $subscribe_auto_id=$value["id"];
            $subscribe_id=$value["subscribe_id"];
            $page_table_id=$value["page_table_id"];
            $fb_page_id=$value["page_id"];
          

            $messenger_bot_drip_campaign_id=$value["messenger_bot_drip_campaign_id"];
            $messenger_bot_drip_initial_date=$value["messenger_bot_drip_initial_date"];
            $messenger_bot_drip_last_completed_day=$value["messenger_bot_drip_last_completed_hour"];
           
            $drip_campaign_type= $value['campaign_type'];

            if($page_table_id!='0' && $page_table_id!=''){

                $first_name=isset($value["first_name"])? $value["first_name"]:"";
                $last_name=isset($value["last_name"]) ? $value["last_name"]:"";
                $full_name=isset($value["full_name"]) ? $value["full_name"]:"";
                $subscriber_email=isset($value['email']) ? $value['email']:"";
                $subscriber_phone_number=isset($value['phone_number']) ? $value['phone_number']:"";
                $sequence_sms_api_id=isset($value['sequence_sms_api_id']) ? $value['sequence_sms_api_id'] : "";
                $sequence_email_api_id=isset($value['sequence_email_api_id']) ? $value['sequence_email_api_id']:"";
                $email_unsubscribed= isset($value['is_email_unsubscriber']) ? $value['is_email_unsubscriber']:"0";
            }

            else{

                $is_external_contact=1;

                $first_name=isset($value["external_first_name"])? $value["external_first_name"]:"";
                $last_name=isset($value["external_last_name"]) ? $value["external_last_name"]:"";
                $subscriber_email=isset($value['external_email']) ? $value['external_email']:"";
                $subscriber_phone_number=isset($value['external_phone_number']) ? $value['external_phone_number']:"";
                $sequence_sms_api_id=isset($value['external_sequence_sms_api_id']) ? $value['external_sequence_sms_api_id'] : "";
                $sequence_email_api_id=isset($value['external_sequence_email_api_id']) ? $value['external_sequence_email_api_id']:"";
                $email_unsubscribed= isset($value['external_unsubscribed']) ? $value['external_unsubscribed']:"0";
            }
 

            $message_content=json_decode($value["message_content_hourly"],true);

            // if there is no hourly sequence not sent for this drip campaign, then update the subscribers hourly campaign as completed. 
            if(empty($message_content)){
                 $this->basic->update_data("messenger_bot_drip_campaign_assign",array("subscribe_id"=>$subscribe_id,"messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id),array("messenger_bot_drip_is_toatally_complete_hourly"=>"1"));
                 continue;
            }

            $message_days=array_keys($message_content); // th days campaign will send message
            $max_send_day=max($message_days); // maximum campaign day, will decide campaign totally complete or not
            
            if($max_send_day==$messenger_bot_drip_last_completed_day)
            $this->basic->update_data("messenger_bot_drip_campaign_assign",array("subscribe_id"=>$subscribe_id,"messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id),array("messenger_bot_drip_is_toatally_complete_hourly"=>"1"));

            foreach ($message_days as $key2 => $value2) 
            {
               if($value2>$messenger_bot_drip_last_completed_day) // getting the next day to start sending message
               {
                
                  $today=date("Y-m-d H:i:s");
                 
                  $sending_day=$value2; // currently processing this drip time
                 // $adding_days=$sending_day-$messenger_bot_drip_last_completed_day; 
                  $sending_date=date('Y-m-d H:i:s', strtotime($messenger_bot_drip_initial_date. ' + '.$sending_day.' minutes'));
                  $is_totally_complete='0';
                  if($max_send_day==$sending_day) $is_totally_complete='1';

                  // calculate after 24 hours date. If for any reason it exceeds 24 hours then complete the campaign.
                   $after_24_date=date('Y-m-d H:i:s', strtotime($messenger_bot_drip_initial_date. '+ 24 hours'));

                  if(strtotime($today)>strtotime($after_24_date)) // if somehow some subscriber was failed to sent message and it will never be comeplete so we are canceling it
                  {
                    $this->basic->update_data("messenger_bot_drip_campaign_assign",array("subscribe_id"=>$subscribe_id,"messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id),array("messenger_bot_drip_is_toatally_complete_hourly"=>"1"));
                  }


                  if(strtotime($today)>=strtotime($sending_date) && strtotime($today)<strtotime($after_24_date) ) // deciding if we have to send message or not now
                  {
                    //getting message template
                    $sent_response=array();
                    $sending_template_id=$message_content[$sending_day];


                    if($drip_campaign_type=='messenger'){



                        $template_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$sending_template_id)));
                        if(!isset($template_data[0])) 
                        {
                              $sent_response[] = "Message template not found.";
                            // break;
                        }

                        //making message to be sent
                        $temp=isset($template_data[0]['template_jsoncode'])?$template_data[0]['template_jsoncode']:"";  

                        $message_array=($temp!="") ? json_decode($temp,true) : array();  

                        $p=0;
                        $curdate=date("Y-m-d H:i:s"); 
                        foreach($message_array as $msg)
                        {
                            $p++;
                            $template_type_file_track=$msg['message']['template_type'];
                            unset($msg['message']['template_type']);

                            $enable_typing_on = $msg['message']['typing_on_settings'];
                            $enable_typing_on = ($enable_typing_on=='on')  ? 1 : 0;
                            unset($msg['message']['typing_on_settings']);
                            $typing_on_delay_time = $msg['message']['delay_in_reply'];
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
                            // $msg["tag"]="NON_PROMOTIONAL_SUBSCRIPTION";


                            $campaign_message_send=json_encode($msg); 
                            $campaign_message_send = str_replace('#LEAD_USER_FIRST_NAME#',$first_name,$campaign_message_send);
                            $campaign_message_send = str_replace('#LEAD_USER_LAST_NAME#',$last_name,$campaign_message_send);
                            $campaign_message_send = str_replace('#LEAD_FULL_NAME#',$full_name,$campaign_message_send);

                            $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                            $replace_with=array('{"id":"'.$subscribe_id.'"}',$subscribe_id);
                            $campaign_message_send=str_replace($replace_search, $replace_with, $campaign_message_send);
                                                  
                            $error_count=0;
                            try
                            {
                                $page_access_token_send=isset($value['page_access_token'])?$value['page_access_token']:"";
                                $response = $this->fb_rx_login->send_non_promotional_message_subscription($campaign_message_send,$page_access_token_send);
                            
                                if(isset($response['message_id']))
                                {
                                    $sent_response[] = $response['message_id']; 
                                    $is_error = 0;
                                }
                                else 
                                {
                                    if(isset($response["error"]["message"])) $sent_response[] = $response["error"]["message"];  
                                    // if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"]; 
                                    $error_count++;    
                                    $is_error=1;             
                                }              
                                
                            }
                            catch(Exception $e) 
                            {
                              $sent_response[] = $e->getMessage();
                              $error_count++;
                              $is_error=1;
                            }


                             // update email & phone quick reply button id in subscriber extra table 

                            if($has_quick_reply==1 && $is_error==0){
                                $insert_subscriber_input_flow_assaign="INSERT INTO messenger_bot_subscriber_extra_info (subscriber_id,page_id,email_quick_reply_button_id,phone_quick_reply_button_id) 
                                  VALUES ('$subscribe_id','$fb_page_id','$email_quick_reply_button_id','$phone_quick_reply_button_id')
                                  ON DUPLICATE KEY UPDATE  email_quick_reply_button_id='$email_quick_reply_button_id',phone_quick_reply_button_id='$phone_quick_reply_button_id'";
                                $this->basic->execute_complex_query($insert_subscriber_input_flow_assaign);

                            }

                            if($text_reply_unique_id != ''){
                                $page_table_id=$value['page_id'];
                                $message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$subscribe_id',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
                                $this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
                             }
                        } 

                    }

                    else if($drip_campaign_type=='email'){

                        $template_data=$this->basic->get_data("email_sms_template",array("where"=>array("id"=>$sending_template_id)));

                        if(!isset($template_data[0])) 
                        {
                              $sent_response[] = "Email template not found.";
                            // break;
                        }

                        //making message to be sent
                        $campaign_message_subject =isset($template_data[0]['subject'])?$template_data[0]['subject']:"";  
                        $campaign_message_send    =isset($template_data[0]['content'])?$template_data[0]['content']:"";

                        $template_type = isset($template_data[0]['editor_type']) ? $template_data[0]['editor_type']:"";
                        if($template_type == 'drag_and_drop') {

                            $campaign_message_send    =json_decode($campaign_message_send,true); 
                            $campaign_message_send = isset($campaign_message_send['refinedMailTemplateHtml']) ? $campaign_message_send['refinedMailTemplateHtml'] : '';

                        }



                        $p=0;
                        $curdate=date("Y-m-d H:i:s"); 

                        $campaign_message_send = str_replace('#FIRST_NAME#',$first_name,$campaign_message_send);
                        $campaign_message_send = str_replace('#LAST_NAME#',$last_name,$campaign_message_send);

                       
                        $formatted = "";
                        $unscubscribe_btn = "";

                        $unsubscription_info = $messenger_bot_drip_campaign_id."-".$subscribe_auto_id;

                        if($is_external_contact)
                             $formatted = $subscribe_id.'-contact-'.$unsubscription_info."-Drip";
                         else
                            $formatted = $subscribe_id.'-subscriber-'.$unsubscription_info."-Drip";

                        $unscubscribe_btn   = '<a href="'.base_url("home/unsubscribe/").bin2hex($formatted).'/'.bin2hex($subscriber_email).'">'.$this->lang->line('Click to Unsubscribe').'</a>';

                        $campaign_message_send  = str_replace("#UNSUBSCRIBE_LINK#",$unscubscribe_btn,$campaign_message_send);



                                              
                        $error_count=0;
                        try
                        {
                             


                            if($sequence_email_api_id==""){
                                $sent_response[]=$this->lang->line("No Email Sender Found");
                                $error_count++;          
                            }
                            else if ($subscriber_email==""){
                                $sent_response[]=$this->lang->line("Subscriber has no email address");
                                $error_count++;          
                            }

                            else if($email_unsubscribed=='1'){
                                $sent_response[]=$this->lang->line("Subscriber is unsubscribed");
                                $error_count++;        
                            }

                            else{



                            $response = $this->send_email_by_for_bot_email($sequence_email_api_id,$campaign_message_send,$subscriber_email, $campaign_message_subject,$user_id);
                        
                            if(isset($response['message_id']))
                            {
                                $sent_response[] = $response['message_id']; 
                            }
                            else 
                            {
                                if(isset($response["error"]["message"])) $sent_response[] = $response["error"]["message"];  
                                // if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"]; 
                                $error_count++;                 
                            }    

                          }
                            
                        }


                        catch(Exception $e) 
                        {
                          $sent_response[] = $e->getMessage();
                          $error_count++;
                        }  
                    }


                    else if($drip_campaign_type=='sms'){

                        $template_data=$this->basic->get_data("email_sms_template",array("where"=>array("id"=>$sending_template_id)));

                        if(!isset($template_data[0])) 
                        {
                              $sent_response[] = "SMS template not found.";
                            // break;
                        }

                        //making message to be sent 
                        $campaign_message_send    =isset($template_data[0]['content'])?$template_data[0]['content']:"";   

                        $p=0;
                        $curdate=date("Y-m-d H:i:s"); 

                        $campaign_message_send = str_replace('#FIRST_NAME#',$first_name,$campaign_message_send);
                        $campaign_message_send = str_replace('#LAST_NAME#',$last_name,$campaign_message_send);


                                              
                        $error_count=0;
                        try
                        {
                            if($sequence_sms_api_id==0){
                                $sent_response[]=$this->lang->line("No SMS Sender Found");
                                $error_count++;          
                            }
                            else if ($subscriber_phone_number==""){
                                $sent_response[]=$this->lang->line("Subscriber has no phone number address");
                                $error_count++;          
                            }

                            else{

                            $response =  $this->send_sms_by_for_bot_phone_number($sequence_sms_api_id,$user_id,$campaign_message_send,$subscriber_phone_number);
                        
                            if(isset($response['message_id']))
                            {
                                $sent_response[] = $response['message_id']; 
                            }
                            else 
                            {
                                if(isset($response["error"]["message"])) $sent_response[] = $response["error"]["message"];  
                                // if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"]; 
                                $error_count++;                 
                            }     

                           }         
                            
                        }
                        catch(Exception $e) 
                        {
                          $sent_response[] = $e->getMessage();
                          $error_count++;
                        }  
                    }



                   
                    $insert_data=array
                    (
                        "messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id,
                        "messenger_bot_subscriber_id"=>$subscribe_auto_id,
                        "page_id"=>$page_table_id,
                        "user_id"=>$user_id,
                        "subscribe_id"=>$subscribe_id,
                        "first_name"=>$first_name,
                        "last_name"=>$last_name,
                        "last_completed_hour"=>$sending_day,
                        "is_sent"=>'1',
                        "sent_at"=>$curdate,
                        "last_updated_at"=>$curdate,
                        "sent_response"=>json_encode($sent_response)
                    );

                    if(isset($message_array))
                        $total_count=count($message_array);
                    else
                        $total_count=1; 

                    if(isset($error_count) && $error_count!=$total_count) // do not need to update delivery status if error
                    {
                        $curdate2=date("Y-m-d H:i:s");
                        $success_count=$total_count-$error_count;
                        $del_response=$success_count. "/". $total_count." ".$this->lang->line("success");
                        $insert_data["is_delivered"]='1';
                        $insert_data["delivered_at"]=$curdate2;
                        $insert_data["last_updated_at"]=$curdate2;
                        $insert_data["delivered_response"]=$del_response;
                    }
                    $this->basic->insert_data("messenger_bot_drip_report",$insert_data); // inserting send report
 
                    $sub_update=array
                    (
                        "messenger_bot_drip_last_completed_hour"=>$sending_day,
                        "messenger_bot_drip_is_toatally_complete_hourly"=>$is_totally_complete,
                        "messenger_bot_drip_last_sent_at"=>$curdate,
                        "messenger_bot_drip_processing_status_hourly"=>"0",
                    );
                    // comment this query while test
                    $this->basic->update_data("messenger_bot_drip_campaign_assign",array("subscribe_id"=>$subscribe_id,"messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id),$sub_update);// updating subscriber so that it will process next drip day again
                  }
                  break;
               }
            }             
        }

        // marking subscribers this cron is operating as ok to process by another cron later  (comment this query while test)
        if(count($subscriber_data_ids)>0)
        {
            $this->db->where_in("id",$subscriber_data_ids);
            $this->db->update("messenger_bot_drip_campaign_assign", array('messenger_bot_drip_processing_status_hourly' => "0"));
        }

        $this->db->update("messenger_bot_drip_campaign", array('last_sent_at' => date("Y-m-d H:i:s")));
    }

    public function sequence_message_broadcast_daily($api_key="")
    { 
        // $this->api_key_check($api_key);
        $number_of_row=50; // number of subscriber on cron will process

        $fb_page_ids=array(); // all facebook pages to be operated [auto id actiually]

        $get_all_campaign=$this->basic->get_data("messenger_bot_drip_campaign");
        $time_match_campaign_ids=array(); // holds ids of campaigns which time interval matches current time
        $campaign_data_formatted=array();

        foreach ($get_all_campaign as $key => $value) 
        {
            $cam_timezone=$value['timezone'];
            if($cam_timezone)  date_default_timezone_set($cam_timezone);

            $cam_between_start=$value['between_start'];
            $cam_between_end=$value['between_end'];

            $current_time=date("H:i");

            $temp0 = (float) str_replace(':','.',$current_time);
            $temp1 = (float) str_replace(':','.',$cam_between_start);
            $temp2 = (float) str_replace(':','.',$cam_between_end);      

            if($temp0>=$temp1 && $temp0<=$temp2) // matches time slot
            {
                $time_match_campaign_ids[]=$value['id'];
                $campaign_data_formatted[$value['id']]=$value;
                $fb_page_ids[]=$value['page_id'];
            }
        }
        $fb_page_ids=array_unique($fb_page_ids);

        if(empty($time_match_campaign_ids)) exit(); // no campaign matches current time zone, go home :p

        // reseting to system timezone
        $time_zone = $this->config->item('time_zone');
        if($time_zone== '') $time_zone="Europe/Dublin";        
        date_default_timezone_set($time_zone);
        
        $page_database=array(); // associated page auto id and page id

        // getting eligible subscriber data
        $subscriber_where=
        array
        (
            "where"=>array
            (
                "messenger_bot_drip_campaign_id !="=>"0",
                "messenger_bot_drip_initial_date !="=>"0000-00-00 00:00:00",
                "messenger_bot_drip_is_toatally_complete"=>"0",
                "messenger_bot_drip_processing_status"=>"0"
            ),
            "where_in"=>array("messenger_bot_drip_campaign_id"=>$time_match_campaign_ids)
        );

        $join = array('messenger_bot_subscriber'=>"messenger_bot_subscriber.subscribe_id=messenger_bot_drip_campaign_assign.subscribe_id,left",
                      'sms_email_contacts'=>'sms_email_contacts.id=messenger_bot_drip_campaign_assign.subscribe_id,Left');    

        $subscriber_data=$this->basic->get_data("messenger_bot_drip_campaign_assign",$subscriber_where,
            'messenger_bot_drip_campaign_assign.*,
            permission,page_id,
            messenger_bot_subscriber.first_name,
            messenger_bot_subscriber.last_name,
            messenger_bot_subscriber.email,
            messenger_bot_subscriber.phone_number,
            messenger_bot_subscriber.is_email_unsubscriber, 
            sms_email_contacts.first_name as external_first_name,
            sms_email_contacts.last_name as external_last_name,
            sms_email_contacts.phone_number as external_phone_number,
            sms_email_contacts.email as external_email,
            sms_email_contacts.unsubscribed as external_unsubscribed
            ',$join,$number_of_row,NULL,'last_processing_started_at ASC');


        if(empty($subscriber_data)) exit();

        $subscriber_data_ids = array();
        foreach ($subscriber_data as $key_sub => $value_sub)
        {
            $subscriber_data_ids[] = $value_sub['id'];
        }
       

        $this->load->library("fb_rx_login"); 

        // marking subscribers this cron is operating as processing (comment this query while test)
        if(count($subscriber_data_ids)>0)
        {
            $this->db->where_in("id",$subscriber_data_ids);
            $this->db->update("messenger_bot_drip_campaign_assign", array('messenger_bot_drip_processing_status' => "1","last_processing_started_at"=>date("Y-m-d H:i:s")));
        }

        // getting page access token
        $page_data=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("bot_enabled"=>"1"),"where_in"=>array('id'=>$fb_page_ids)));
        foreach ($page_data as $key => $value) 
        {
            $page_database[$value['page_id']]=array("page_id"=>$value['id'],"fb_page_id"=>$value['page_id'],"page_access_token"=>$value['page_access_token'],"sequence_sms_api_id"=>$value['sequence_sms_api_id'],"sequence_email_api_id"=>$value['sequence_email_api_id']);
        }
           
        foreach ($subscriber_data as $key => $value) 
        {      

            $is_external_contact=0;

            $user_id=$value["user_id"];
            $subscribe_auto_id=$value["id"];
            $subscribe_id=$value["subscribe_id"];

            $page_table_auto_id=isset($value["page_table_id"]) ? $value["page_table_id"]:"";


            $messenger_bot_drip_campaign_id=$value["messenger_bot_drip_campaign_id"];
            $messenger_bot_drip_initial_date=$value["messenger_bot_drip_initial_date"];
            $messenger_bot_drip_last_completed_day=$value["messenger_bot_drip_last_completed_day"];

            
            $drip_campaign_type= $campaign_data_formatted[$messenger_bot_drip_campaign_id]['campaign_type'];

            if($page_table_auto_id!="0" && $page_table_auto_id!=""){

                $first_name=isset($value["first_name"]) ? $value["first_name"] : "" ;
                $last_name=isset($value["last_name"]) ? $value["last_name"] : "";
                $subscriber_email=isset($value['email']) ? $value['email'] : "";
                $subscriber_phone_number=isset($value['phone_number']) ? $value['phone_number'] : "";
                $sequence_sms_api_id=isset($page_database[$value['page_id']]['sequence_sms_api_id'])?$page_database[$value['page_id']]['sequence_sms_api_id']:"";
                $sequence_email_api_id=isset($page_database[$value['page_id']]['sequence_email_api_id'])?$page_database[$value['page_id']]['sequence_email_api_id']:"";

                $email_unsubscribed= isset($value['is_email_unsubscriber']) ? $value['is_email_unsubscriber']:"0";


            }   

            else{

                $is_external_contact=1;

                $first_name=isset($value["external_first_name"]) ? $value["external_first_name"] : "" ;
                $last_name=isset($value["external_last_name"]) ? $value["external_last_name"] : "";
                $subscriber_email=isset($value['external_email']) ? $value['external_email'] : "";
                $subscriber_phone_number=isset($value['external_phone_number']) ? $value['external_phone_number'] : "";

                $sequence_sms_api_id=isset($campaign_data_formatted[$messenger_bot_drip_campaign_id]['external_sequence_sms_api_id'])?$campaign_data_formatted[$messenger_bot_drip_campaign_id]['external_sequence_sms_api_id'] : "";

                $sequence_email_api_id=isset($campaign_data_formatted[$messenger_bot_drip_campaign_id]['external_sequence_email_api_id'])?$campaign_data_formatted[$messenger_bot_drip_campaign_id]['external_sequence_email_api_id'] : "";

                $email_unsubscribed= isset($value['external_unsubscribed']) ? $value['external_unsubscribed']:"0";


            }

            
            if(!isset($campaign_data_formatted[$messenger_bot_drip_campaign_id])) 
            {
                echo "Drip campaign ID : ".$messenger_bot_drip_campaign_id." not found <br>";
                continue;
            }
           
            $message_content=json_decode($campaign_data_formatted[$messenger_bot_drip_campaign_id]["message_content"],true);

           

             // if there is no Daily sequence not sent for this drip campaign, then update the subscribers Daily campaign as completed. 
            if(empty($message_content)){
                 $this->basic->update_data("messenger_bot_drip_campaign_assign",array("subscribe_id"=>$subscribe_id,"messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id),array("messenger_bot_drip_is_toatally_complete"=>"1"));
                 continue;
            }



            $message_days=array_keys($message_content); // th days campaign will send message
            $max_send_day=max($message_days); // maximum campaign day, will decide campaign totally complete or not

            if($max_send_day==$messenger_bot_drip_last_completed_day)
            $this->basic->update_data("messenger_bot_drip_campaign_assign",array("subscribe_id"=>$subscribe_id,"messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id),array("messenger_bot_drip_is_toatally_complete"=>"1"));


            foreach ($message_days as $key2 => $value2) 
            {
               if($value2>$messenger_bot_drip_last_completed_day) // getting the next day to start sending message
               {
                  $today=date("Y-m-d");
                  // $today="2018-08-12";
                  $sending_day=$value2; // currently processing this drip day
                  //$adding_days=$sending_day-$messenger_bot_drip_last_completed_day; 
                  $sending_date=date('Y-m-d', strtotime($messenger_bot_drip_initial_date. ' + '.$sending_day.' days'));
                  $is_totally_complete='0';
                  if($max_send_day==$sending_day) $is_totally_complete='1';

                  if(strtotime($today)>strtotime($sending_date)) // if somehow some subscriber was failed to sent message and it will never be comeplete so we are canceling it
                  {
                    $this->basic->update_data("messenger_bot_drip_campaign_assign",array("subscribe_id"=>$subscribe_id,"messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id),array("messenger_bot_drip_is_toatally_complete"=>"1"));
                  }

                  if(strtotime($today)==strtotime($sending_date)) // deciding if we have to send message or not today
                  {
                    //getting message template
                    $sent_response=array();
                    $sending_template_id=$message_content[$sending_day];


                if($drip_campaign_type=='messenger'){

                    $template_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$sending_template_id)));
                    if(!isset($template_data[0])) 
                    {
                          $sent_response[] = "Message template not found.";
                        // break;
                    }

                    //making message to be sent
                    $temp=isset($template_data[0]['template_jsoncode'])?$template_data[0]['template_jsoncode']:"";  

                    $message_array=($temp!="") ? json_decode($temp,true) : array();  

                    $p=0;
                    $curdate=date("Y-m-d H:i:s"); 
                    foreach($message_array as $msg){
                        $p++;
                        $template_type_file_track=$msg['message']['template_type'];
                        unset($msg['message']['template_type']);

                        $enable_typing_on = $msg['message']['typing_on_settings'];
                        $enable_typing_on = ($enable_typing_on=='on')  ? 1 : 0;
                        unset($msg['message']['typing_on_settings']);
                        $typing_on_delay_time = $msg['message']['delay_in_reply'];
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

                        $msg['messaging_type'] = "MESSAGE_TAG";
                        $msg["tag"]=$campaign_data_formatted[$messenger_bot_drip_campaign_id]["message_tag"];
                        


                        $campaign_message_send=json_encode($msg); 
                        $campaign_message_send = str_replace('#LEAD_USER_FIRST_NAME#',$first_name,$campaign_message_send);
                        $campaign_message_send = str_replace('#LEAD_USER_LAST_NAME#',$last_name,$campaign_message_send);

                        $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                        $replace_with=array('{"id":"'.$subscribe_id.'"}',$subscribe_id);
                        $campaign_message_send=str_replace($replace_search, $replace_with, $campaign_message_send);
                                              
                        $error_count=0;
                        try
                        {
                            $page_access_token_send=isset($page_database[$value['page_id']]['page_access_token'])?$page_database[$value['page_id']]['page_access_token']:"";
                            $response = $this->fb_rx_login->send_non_promotional_message_subscription($campaign_message_send,$page_access_token_send);
                        
                            if(isset($response['message_id']))
                            {
                                $sent_response[] = $response['message_id']; 
                                $is_error=0;
                            }
                            else 
                            {
                                if(isset($response["error"]["message"])) $sent_response[] = $response["error"]["message"];  
                                // if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"]; 
                                $error_count++;    
                                $is_error=1;             
                            }              
                            
                        }
                        catch(Exception $e) 
                        {
                          $sent_response[] = $e->getMessage();
                          $error_count++;
                          $is_error=1;
                        }


                        // update email & phone quick reply button id in subscriber extra table 

                        if($has_quick_reply==1 && $is_error==0){
                            $insert_subscriber_input_flow_assaign="INSERT INTO messenger_bot_subscriber_extra_info (subscriber_id,page_id,email_quick_reply_button_id,phone_quick_reply_button_id) 
                              VALUES ('$subscribe_id','$fb_page_id','$email_quick_reply_button_id','$phone_quick_reply_button_id')
                              ON DUPLICATE KEY UPDATE  email_quick_reply_button_id='$email_quick_reply_button_id',phone_quick_reply_button_id='$phone_quick_reply_button_id'";
                            $this->basic->execute_complex_query($insert_subscriber_input_flow_assaign);

                        }

                        if($text_reply_unique_id != ''){
                            $page_table_id=$value['page_id'];
                            $message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$subscribe_id',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
                            $this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
                        }




                    } 

                }




                    else if($drip_campaign_type=='email'){

                        $template_data=$this->basic->get_data("email_sms_template",array("where"=>array("id"=>$sending_template_id)));

                        if(!isset($template_data[0])) 
                        {
                              $sent_response[] = "Email template not found.";
                            // break;
                        }

                        //making message to be sent
                        $campaign_message_subject =isset($template_data[0]['subject'])?$template_data[0]['subject']:"";  
                        $campaign_message_send    =isset($template_data[0]['content'])?$template_data[0]['content']:"";

                        $template_type = isset($template_data[0]['editor_type']) ? $template_data[0]['editor_type']:"";
                        if($template_type == 'drag_and_drop') {

                            $campaign_message_send    =json_decode($campaign_message_send,true); 
                            $campaign_message_send = isset($campaign_message_send['refinedMailTemplateHtml']) ? $campaign_message_send['refinedMailTemplateHtml'] : '';

                        }   

                        $p=0;
                        $curdate=date("Y-m-d H:i:s"); 

                        $campaign_message_send = str_replace('#FIRST_NAME#',$first_name,$campaign_message_send);
                        $campaign_message_send = str_replace('#LAST_NAME#',$last_name,$campaign_message_send);

                       
                        $formatted = "";
                        $unscubscribe_btn = "";

                        $unsubscription_info = $messenger_bot_drip_campaign_id."-".$subscribe_auto_id;

                        if($is_external_contact)
                             $formatted = $subscribe_id.'-contact-'.$unsubscription_info."-Drip";
                         else
                            $formatted = $subscribe_id.'-subscriber-'.$unsubscription_info."-Drip";

                        $unscubscribe_btn   = '<a href="'.base_url("home/unsubscribe/").bin2hex($formatted).'/'.bin2hex($subscriber_email).'">'.$this->lang->line('Click to Unsubscribe').'</a>';

                        $campaign_message_send  = str_replace("#UNSUBSCRIBE_LINK#",$unscubscribe_btn,$campaign_message_send);

                                              
                        $error_count=0;
                        try
                        {
                             


                            if($sequence_email_api_id==""){
                                $sent_response[]=$this->lang->line("No Email Sender Found");
                                $error_count++;          
                            }
                            else if ($subscriber_email==""){
                                $sent_response[]=$this->lang->line("Subscriber has no email address");
                                $error_count++;          
                            }

                            else if($email_unsubscribed=='1'){
                                $sent_response[]=$this->lang->line("Subscriber is unsubscribed");
                                $error_count++;        
                            }


                            else{



                            $response = $this->send_email_by_for_bot_email($sequence_email_api_id,$campaign_message_send,$subscriber_email, $campaign_message_subject,$user_id);
                        
                            if(isset($response['message_id']))
                            {
                                $sent_response[] = $response['message_id']; 
                            }
                            else 
                            {
                                if(isset($response["error"]["message"])) $sent_response[] = $response["error"]["message"];  
                                // if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"]; 
                                $error_count++;                 
                            }    

                          }
                            
                        }


                        catch(Exception $e) 
                        {
                          $sent_response[] = $e->getMessage();
                          $error_count++;
                        }  
                    }


                    else if($drip_campaign_type=='sms'){

                        $template_data=$this->basic->get_data("email_sms_template",array("where"=>array("id"=>$sending_template_id)));

                        if(!isset($template_data[0])) 
                        {
                              $sent_response[] = "SMS template not found.";
                            // break;
                        }

                        //making message to be sent 
                        $campaign_message_send    =isset($template_data[0]['content'])?$template_data[0]['content']:"";   

                        $p=0;
                        $curdate=date("Y-m-d H:i:s"); 

                        $campaign_message_send = str_replace('#FIRST_NAME#',$first_name,$campaign_message_send);
                        $campaign_message_send = str_replace('#LAST_NAME#',$last_name,$campaign_message_send);


                                              
                        $error_count=0;
                        try
                        {
                            if($sequence_sms_api_id==0){
                                $sent_response[]=$this->lang->line("No SMS Sender Found");
                                $error_count++;          
                            }
                            else if ($subscriber_phone_number==""){
                                $sent_response[]=$this->lang->line("Subscriber has no phone number address");
                                $error_count++;          
                            }

                            else{

                            $response =  $this->send_sms_by_for_bot_phone_number($sequence_sms_api_id,$user_id,$campaign_message_send,$subscriber_phone_number);
                        
                            if(isset($response['message_id']))
                            {
                                $sent_response[] = $response['message_id']; 
                            }
                            else 
                            {
                                if(isset($response["error"]["message"])) $sent_response[] = $response["error"]["message"];  
                                // if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"]; 
                                $error_count++;                 
                            }     

                           }         
                            
                        }
                        catch(Exception $e) 
                        {
                          $sent_response[] = $e->getMessage();
                          $error_count++;
                        }  
                    }


                    $page_id_insert_report=isset($page_database[$value['page_id']]['page_id']) ? $page_database[$value['page_id']]['page_id'] : '0';

                   
                    $insert_data=array
                    (
                        "messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id,
                        "messenger_bot_subscriber_id"=>$subscribe_auto_id,
                        "page_id"=>$page_id_insert_report,
                        "user_id"=>$user_id,
                        "subscribe_id"=>$subscribe_id,
                        "first_name"=>$first_name,
                        "last_name"=>$last_name,
                        "last_completed_day"=>$sending_day,
                        "is_sent"=>'1',
                        "sent_at"=>$curdate,
                        "last_updated_at"=>$curdate,
                        "sent_response"=>json_encode($sent_response)
                    );

                    if(isset($message_array))
                        $total_count=count($message_array);
                    else
                        $total_count=1; 
                    
                    if(isset($error_count) && $error_count!=$total_count) // do not need to update delivery status if error
                    {
                        $curdate2=date("Y-m-d H:i:s");
                        $success_count=$total_count-$error_count;
                        $del_response=$success_count. "/". $total_count." ".$this->lang->line("success");
                        $insert_data["is_delivered"]='1';
                        $insert_data["delivered_at"]=$curdate2;
                        $insert_data["last_updated_at"]=$curdate2;
                        $insert_data["delivered_response"]=$del_response;
                    }
                    $this->basic->insert_data("messenger_bot_drip_report",$insert_data); // inserting send report
 
                    $sub_update=array
                    (
                        "messenger_bot_drip_last_completed_day"=>$sending_day,
                        "messenger_bot_drip_is_toatally_complete"=>$is_totally_complete,
                        "messenger_bot_drip_last_sent_at"=>$curdate,
                        // "messenger_bot_drip_initial_date"=>$curdate,
                        "messenger_bot_drip_processing_status"=>"0",
                    );
                    // comment this query while test
                    $this->basic->update_data("messenger_bot_drip_campaign_assign",array("subscribe_id"=>$subscribe_id,"messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id),$sub_update);// updating subscriber so that it will process next drip day again
                  }
                  break;
               }
            }             
        }

        // marking subscribers this cron is operating as ok to process by another cron later  (comment this query while test)
        if(count($subscriber_data_ids)>0)
        {
            $this->db->where_in("id",$subscriber_data_ids);
            $this->db->update("messenger_bot_drip_campaign_assign", array('messenger_bot_drip_processing_status' => "0"));
        }

        //updaing date in messenger_bot_drip_campaign table
        if(count($time_match_campaign_ids)>0)
        {
            $this->db->where_in('id', $time_match_campaign_ids);
            $this->db->update("messenger_bot_drip_campaign", array('last_sent_at' => date("Y-m-d H:i:s")));
        }
    }
    // ===========MESSENGER DRIP RELATED FUNCTIONS=============



    // =====================OTHER FUNCTIONS===================
    public function membership_alert($api_key="") //membership_alert_delete_junk_data
    {
        // $this->api_key_check($api_key);    

        $free_package_info = $this->basic->get_data('package',['where'=>['price'=>'0','validity'=>'0','is_default'=>'1']]);
        $free_package_id = isset($free_package_info[0]['id']) ? $free_package_info[0]['id'] : 0;

        $current_date = date("Y-m-d");
        $tenth_day_before_expire = date("Y-m-d", strtotime("$current_date + 10 days"));
        $one_day_before_expire = date("Y-m-d", strtotime("$current_date + 1 days"));
        $one_day_after_expire = date("Y-m-d", strtotime("$current_date - 1 days"));

        // echo $tenth_day_before_expire."<br/>".$one_day_before_expire."<br/>".$one_day_after_expire;

        //send notification to members before 10 days of expire date
        $where = array();
        $where['where'] = array(
            'user_type !=' => 'Admin',
            'expired_date' => $tenth_day_before_expire,
            'package_id !=' => $free_package_id
            );
        $info = array();
        $value = array();
        $info = $this->basic->get_data('users',$where,$select='');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');

        // getting email template info
        $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'membership_expiration_10_days_before')),array('subject','message'));

        if(isset($email_template_info[0]) && $email_template_info[0]['subject'] !='' && $email_template_info[0]['message'] !='') {

            $subject = $email_template_info[0]['subject'];
            foreach ($info as $value) 
            {
                if(!$this->api_member_validity($value['id'])) continue;
                $url = base_url();

                $message = str_replace(array('#USERNAME#','#APP_URL#','#APP_NAME#'),array($value['name'],$url,$mask),$email_template_info[0]['message']);

                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
            }


        } else {

            $subject = "Payment Notification";
            foreach ($info as $value) 
            {
                if(!$this->api_member_validity($value['id'])) continue;
                $message = "Dear {$value['name']},<br/> your account will expire after 10 days, Please pay your fees.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
            }

        }

        //send notificatio to members before 1 day of expire date
        $where = array();
        $where['where'] = array(
            'user_type !=' => 'Admin',
            'expired_date' => $one_day_before_expire,
            'package_id !=' => $free_package_id
            );
        $info = array();
        $value = array();
        $info = $this->basic->get_data('users',$where,$select='');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');

        // getting email template info
        $email_template_info_01 = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'membership_expiration_1_day_before')),array('subject','message'));

        if(isset($email_template_info_01[0]) && $email_template_info_01[0]['subject'] != '' && $email_template_info_01[0]['message'] != '') {

            $subject = $email_template_info_01[0]['subject'];
            foreach ($info as $value) {
                if(!$this->api_member_validity($value['id'])) continue;
                $url = base_url();
                $message = str_replace(array('#USERNAME#','#APP_URL#','#APP_NAME#'),array($value['name'],$url,$mask),$email_template_info_01[0]['message']);

                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
            }

        }
        else {

            $subject = "Payment Notification";
            foreach ($info as $value) {
                if(!$this->api_member_validity($value['id'])) continue;
                $message = "Dear {$value['name']},<br/> your account will expire tomorrow, Please pay your fees.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
            }

        }
        

        //send notificatio to members after 1 day of expire date
        $where = array();
        $where['where'] = array(
            'user_type !=' => 'Admin',
            'expired_date' => $one_day_after_expire,
            'package_id !=' => $free_package_id
            );
        $info = array();
        $value = array();
        $info = $this->basic->get_data('users',$where,$select='');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');

        $email_template_info_02 = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'membership_expiration_1_day_after')),array('subject','message'));

        if(isset($email_template_info_02[0]) && $email_template_info_02[0]['subject'] != '' && $email_template_info_02[0]['message'] != '') {

            $subject = $email_template_info_02[0]['subject'];

            foreach ($info as $value) {
                if(!$this->api_member_validity($value['id'])) continue;
                $url = base_url();
                $message = str_replace(array('#USERNAME#','#APP_URL#','#APP_NAME#'),array($value['name'],$url,$mask),$email_template_info_02[0]['message']);
                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
            }

        } else {

            $subject = "Payment Notification";
            foreach ($info as $value) {
                if(!$this->api_member_validity($value['id'])) continue;
                $message = "Dear {$value['name']},<br/> your account has been expired, Please pay your fees for continuity.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=1);
            }
        }        

    }
    public function delete_junk_data($api_key="") //membership_alert_delete_junk_data
    {
        // $this->api_key_check($api_key);

        $delete_junk_data_after_how_many_days = $this->config->item("delete_junk_data_after_how_many_days");
        if($delete_junk_data_after_how_many_days=="") $delete_junk_data_after_how_many_days = 30;

        $cur_time=date('Y-m-d H:i:s');
        $last_time=date("Y-m-d H:i:s",strtotime($cur_time." -".$delete_junk_data_after_how_many_days." day"));
        // conversation broadcast report delete
        $this->basic->delete_data("facebook_ex_conversation_campaign_send",array("processed"=>"1","sent_time <="=>$last_time));

        // auto reply report delete
        $this->basic->delete_data("facebook_ex_autoreply_report",array("reply_time <="=>$last_time));

        /**Clean Messenger Broadcaster Subscriber Sending Table**/        
       if($this->db->table_exists('messenger_bot_broadcast_serial_send')){
            $this->basic->delete_data("messenger_bot_broadcast_serial_send",array("processed"=>"1","sent_time <="=>$last_time));
       }

       /****Clean Cache Directory , keep all files of last 24 hours******/
       $all_cache_file=$this->delete_cache('application/cache');
       $all_cache_file=$this->delete_cache('upload/qrc');

       /**Clean send_email_to_autoresponder_log **/       
       $this->basic->delete_data("send_email_to_autoresponder_log",array("insert_time <="=>$last_time));
       $this->basic->delete_data("messenger_bot_reply_error_log",array("error_time <="=>$last_time));

       /**Clean JSON API Log Table**/
       if($this->addon_exist("messenger_bot_connectivity")) 
            $this->basic->delete_data("messenger_bot_thirdparty_webhook_activity",array("post_time <="=>$last_time));

       //Delete error log file in root
       @unlink("error_log");

    }
    protected function delete_cache($myDir) //delete_junk_data
    {

        $cur_time=date('Y-m-d H:i:s');
        $yesterday=date("Y-m-d H:i:s",strtotime($cur_time." -2 day"));
        $yesterday=strtotime($yesterday);


        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);
        
        foreach (new RecursiveIteratorIterator($di) as $filename) {
        
        $dir = str_replace($myDir, '', dirname($filename));
        //$dir = str_replace('/', '>', substr($dir,1));
        
        $org_dir=str_replace("\\", "/", $dir);
        
        
        if($org_dir)
        $file_path = $org_dir. "/". basename($filename);
        else
        $file_path = basename($filename);

        $path_explode = explode(".",$file_path);
        $extension= array_pop($path_explode);

        if($file_path!='.htaccess' && $file_path!='index.html'){

             $full_file_path=$myDir."/".$file_path;

             $file_creation_time=filemtime($full_file_path);
             $file_creation_time=date('Y-m-d H:i:s',$file_creation_time); //convert unix time to system time zone 
             $file_creation_time=strtotime($file_creation_time);


             if($file_creation_time<$yesterday){
                $dirTree[] = trim($file_path,"/");
                unlink($full_file_path);

             }
                
        }

        
        }
        
        return $dirTree;
            
    }
    // =====================OTHER FUNCTIONS===================



    //Manin cron job with multiple child cron job

    // 1  min
    public function braodcast_message($api_key='')
    {
    	// $link=base_url().'cron_job/conversation_broadcast/'.$api_key;
    	// $this->call_curl_internal_cronjob($link);

		$link=base_url().'cron_job/subscriber_broadcaster/'.$api_key;
		$this->call_curl_internal_cronjob($link);
    	
    	// Email Broadcast
    	if($this->basic->is_exist("modules",array("id"=>263)))
    	{
    		$link=base_url().'cron_job/email_sending_command/'.$api_key;
    		$this->call_curl_internal_cronjob($link);
    	}
    	
    }


    // 5 mins
    public function auto_comment_on_post($api_key='')
    {        
        $link=base_url().'cron_job/auto_comment_on_post_orginal/'.$api_key;
        $this->call_curl_internal_cronjob($link);

        if($this->basic->is_exist("add_ons",array("project_id"=>29))){
            
            $link=base_url().'cron_job/comment_bulk_tag/'.$api_key;
            $this->call_curl_internal_cronjob($link);

            $link=base_url().'cron_job/bulk_comment_reply/'.$api_key;
            $this->call_curl_internal_cronjob($link);

            $link=base_url().'cron_job/auto_share_on_post/'.$api_key;
            $this->call_curl_internal_cronjob($link);

            $link=base_url().'cron_job/auto_like_on_post/'.$api_key;
            $this->call_curl_internal_cronjob($link);
        }

        // Woocommerce abandoned cart
        if($this->basic->is_exist("add_ons",array("project_id"=>33)))
        {
            $link=base_url().'cron_job/woocommerce_abandoned_cart_reminder/'.$api_key;
            $this->call_curl_internal_cronjob($link);
        }
        // xerobizz add-on exist
        if($this->basic->is_exist("add_ons",array("project_id"=>55)))
        {
            $link=base_url().'cron_job/xerobiz_reply_to_review/'.$api_key;
            $this->call_curl_internal_cronjob($link);
        }

        $link=base_url().'cron_job/ecommerce_abandoned_cart_reminder/'.$api_key;
        $this->call_curl_internal_cronjob($link);

        //SMS Broadcast 
        $link=base_url().'cron_job/sms_sending_command/'.$api_key;
        $this->call_curl_internal_cronjob($link);      

    }


    // 15 min
    public function background_scanning_update_subscriber_info($api_key="")
    {
    	$link=base_url().'cron_job/background_scanning/'.$api_key;
    	$this->call_curl_internal_cronjob($link);

    	$link=base_url().'cron_job/update_subscriber_profile_info/'.$api_key;
    	$this->call_curl_internal_cronjob($link);

        $link=base_url().'cron_job/extract_subscribers_label/'.$api_key;
        $this->call_curl_internal_cronjob($link);

    	if($this->basic->is_exist("add_ons",array("project_id"=>30)) || $this->basic->is_exist("add_ons",array("project_id"=>40)))
    	{
			$link=base_url().'cron_job/sequence_message_broadcast_daily/'.$api_key;
			$this->call_curl_internal_cronjob($link);

            $link=base_url().'cron_job/sequence_message_broadcast_hourly/'.$api_key;
            $this->call_curl_internal_cronjob($link);

    	}
    
    }

    //1 day
    public function membership_alert_delete_junk_data($api_key="")
    {

    	$link=base_url().'cron_job/membership_alert/'.$api_key;
    	$this->call_curl_internal_cronjob($link);

        $link=base_url().'cron_job/expired_users_disable_bot/'.$api_key;
        $this->call_curl_internal_cronjob($link);

    	$link=base_url().'cron_job/delete_junk_data/'.$api_key;
    	$this->call_curl_internal_cronjob($link);

        if($this->basic->is_exist("add_ons",array("project_id"=>41)))
        {
            $link=base_url().'cron_job/delete_vidcasterlive_junk_data/'.$api_key;
            $this->call_curl_internal_cronjob($link);  

            $link=base_url().'cron_job/facebook_live_streaming_expired/'.$api_key;
            $this->call_curl_internal_cronjob($link);    

                     
        }
    }

    // 5 min
    public function publish_post($api_key="")
    {
    	
    	$link=base_url().'comboposter/post_to_all_media/'.$api_key;
        $this->call_curl_internal_cronjob($link);

    	$link=base_url().'cron_job/text_image_link_video_post/'.$api_key;
    	$this->call_curl_internal_cronjob($link);

    	$link=base_url().'cron_job/cta_post/'.$api_key;
    	$this->call_curl_internal_cronjob($link);

    	$link=base_url().'cron_job/carousel_slider_post/'.$api_key;
    	$this->call_curl_internal_cronjob($link);

    	$link=base_url().'cron_job/rss_auto_post/'.$api_key;
    	$this->call_curl_internal_cronjob($link);

    	if($this->basic->is_exist("add_ons",array("project_id"=>41)))
        {
            $link=base_url().'cron_job/stream_live_video/'.$api_key;
            $this->call_curl_internal_cronjob($link);      
        }
        // xerobizz add-on exist
        if($this->basic->is_exist("add_ons",array("project_id"=>55)))
        {
            $link=base_url().'cron_job/post_to_gmb/'.$api_key;
            $this->call_curl_internal_cronjob($link);      
        }


    }

    protected function call_curl_internal_cronjob($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        echo $reply_response=curl_exec($ch); 
    }
    // comment reply enhancers add-on cron job

    
    public function comment_bulk_tag($api_key="")
    {
        // $this->api_key_check($api_key);

        $number_of_campaign_to_be_processed = 1; // max number of campaign that can be processed by this cron job

        /****** Get all campaign from database where status=0 means pending ******/
        $where['where'] = array('posting_status'=>"0");
        $join = array('users'=>'tag_machine_bulk_tag.user_id=users.id,left');
        $campaign_info= $this->basic->get_data("tag_machine_bulk_tag",$where,$select=array("tag_machine_bulk_tag.*","users.deleted as user_deleted"),$join,$limit=50, $start=0, $order_by='schedule_time ASC');  
        
        $access_token_database_database = array(); //  [campaign_id][page_auto_id] =>access token
        $facebook_rx_fb_user_info_id_database = array(); // campaign_id => facebook_rx_fb_user_info_id
        $facebook_rx_config_id_database = array(); // facebook_rx_fb_user_info_id => facebook_rx_config_id
        $campaign_id_array=array();  // all selected campaign id array
        $campaign_info_fildered = array(); // valid for process, campign info array

        $valid_campaign_count = 1;
        foreach($campaign_info as $info)
        {
             if($info['user_deleted'] == '1' || $info['user_deleted']=="") continue;

            $campaign_id = $info['id'];
            $time_zone = $info['time_zone'];
            $schedule_time = $info['schedule_time'];     
            $page_id = $info["page_info_table_id"]; 

            if($time_zone) date_default_timezone_set($time_zone);            
            $now_time = date("Y-m-d H:i:s");

            if((strtotime($now_time) < strtotime($schedule_time)) && $time_zone!="") continue; 
            if($valid_campaign_count > $number_of_campaign_to_be_processed) break; 

            $token_info =  $this->basic->get_data('facebook_rx_fb_page_info',array("where"=>array('id'=>$page_id)),array("page_access_token","facebook_rx_fb_user_info_id","id","page_name"));
            foreach ($token_info as $key => $value) 
            {
                $access_token_database_database[$campaign_id][$value["id"]] = $value['page_access_token'];
                $facebook_rx_fb_user_info_id = $value["facebook_rx_fb_user_info_id"];
                $facebook_rx_fb_user_info_id_database[$campaign_id] = $facebook_rx_fb_user_info_id;
            }           
           
            // valid campaign info and campig ids
            $campaign_info_fildered[] = $info;
            $campaign_id_array[] = $info['id']; 
            $valid_campaign_count++;   
        }

        if(count($campaign_id_array)==0) exit();        

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("tag_machine_bulk_tag",array("posting_status"=>"1"));

        // get config id
        $getdata= $this->basic->get_data("facebook_rx_fb_user_info",array("where_in"=>array("id"=>$facebook_rx_fb_user_info_id_database)),array("id","facebook_rx_config_id"));
        foreach ($getdata as $key => $value) 
        {
            $facebook_rx_config_id_database[$value["id"]] = $value["facebook_rx_config_id"];
        } 

        $this->load->library("fb_rx_login");

        foreach($campaign_info_fildered as $info)
        {
            $campaign_id = $info['id'];                   
            $post_id = $info['post_id'];                   
            $page_id = $info['page_info_table_id']; 
            $post_access_token = isset($access_token_database_database[$campaign_id][$page_id]) ? $access_token_database_database[$campaign_id][$page_id] : "";   
            $uploaded_image_video = $info["uploaded_image_video"]; 
     
            $now_time = date("Y-m-d H:i:s");

            if(!isset($post_access_token) || $post_access_token=="") 
            {
              $this->basic->update_data("tag_machine_bulk_tag",array("id"=>$campaign_id),array("posting_status"=>"2","error_message"=>"Access token not found.","last_updated_at"=>$now_time));
              continue;
            }

            $image=$video=$gif="";
            if($uploaded_image_video!="")
            {
              $ext_exp=explode('.', $uploaded_image_video);
              $ext=array_pop($ext_exp);
              $video_array=array("flv","mp4","wmv");
              if(in_array($ext,$video_array)) 
              $video=FCPATH.'upload/comment_reply_enhancers/'.$uploaded_image_video;  
              else if($ext=='gif') $gif=base_url("upload/comment_reply_enhancers/".$uploaded_image_video);
              else $image=base_url("upload/comment_reply_enhancers/".$uploaded_image_video); 
            }    

            $fb_rx_fb_user_info_id = isset($facebook_rx_fb_user_info_id_database[$campaign_id])?$facebook_rx_fb_user_info_id_database[$campaign_id]:""; // find gb user id for this campaign
            
            if(!isset($fb_rx_fb_user_info_id) || $facebook_rx_fb_user_info_id_database=="") 
            {
              $this->basic->update_data("tag_machine_bulk_tag",array("id"=>$campaign_id),array("posting_status"=>"2","error_message"=>"Facebook accouny not found.","last_updated_at"=>$now_time));
              continue;
            }
            
            $this->fb_rx_login->app_initialize($facebook_rx_config_id_database[$fb_rx_fb_user_info_id]);

            $tag_database=json_decode($info["tag_database"],true);
            $tags="";
            foreach ($tag_database as $key => $value) 
            {
               $tags.="@[".$key."], ";
            }
            $tags=trim($tags);
            $tags=trim($tags,',');
            $tag_content=$info["tag_content"]."

            ".$tags;

            try 
            {
              $response=$this->fb_rx_login->auto_comment($tag_content,$post_id,$post_access_token,$image,$video,$gif);
              $commentid=isset($response['id'])?$response['id']:"";         
              $this->basic->update_data("tag_machine_bulk_tag",array("id"=>$campaign_id),array("posting_status"=>"2","tag_response"=>$commentid,"last_updated_at"=>$now_time));
            } 
            catch (Exception $e) 
            {
              $error_msg = $e->getMessage();
              $this->basic->update_data("tag_machine_bulk_tag",array("id"=>$campaign_id),array("posting_status"=>"2","error_message"=>$error_msg,"last_updated_at"=>$now_time));
            }

            
        }          
    
    }
    

    public function bulk_comment_reply($api_key="")
    {
        // $this->api_key_check($api_key);

        $number_of_message_to_be_sent_in_try=$this->config->item("number_of_message_to_be_sent_in_try"); 
        if($number_of_message_to_be_sent_in_try==0) $number_of_message_to_be_sent_in_try="";
        $update_report_after_time=$this->config->item("update_report_after_time"); 
        if($update_report_after_time=="" || $update_report_after_time==0) $update_report_after_time=10;
        $number_of_campaign_to_be_processed = 1; // max number of campaign that can be processed by this cron job

        /****** Get all campaign from database where status=0 means pending ******/
        $where['or_where'] = array('posting_status'=>"0","is_try_again"=>"1");
        $join = array('users'=>'tag_machine_bulk_reply.user_id=users.id,left');
        $campaign_info= $this->basic->get_data("tag_machine_bulk_reply",$where,$select=array("tag_machine_bulk_reply.*","users.deleted as user_deleted"),$join,$limit=50, $start=0, $order_by='schedule_time ASC');  

        $access_token_database_database = array(); //  [campaign_id][page_auto_id] =>access token
        $facebook_rx_fb_user_info_id_database = array(); // campaign_id => facebook_rx_fb_user_info_id
        $facebook_rx_config_id_database = array(); // facebook_rx_fb_user_info_id => facebook_rx_config_id
        $campaign_id_array=array();  // all selected campaign id array
        $campaign_info_fildered = array(); // valid for process, campign info array

        $valid_campaign_count = 1;
        foreach($campaign_info as $info)
        {
            if($info['user_deleted'] == '1' || $info['user_deleted']=="") continue;

            $campaign_id= $info['id'];
            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];    
            $page_info_table_id = $info["page_info_table_id"]; 

            if($time_zone) date_default_timezone_set($time_zone);            
            $now_time = date("Y-m-d H:i:s");

            if((strtotime($now_time) < strtotime($schedule_time)) && $time_zone!="") continue; 
            if($valid_campaign_count > $number_of_campaign_to_be_processed) break; 

            $token_info =  $this->basic->get_data('facebook_rx_fb_page_info',array("where"=>array('id'=>$page_info_table_id)),array("page_access_token","facebook_rx_fb_user_info_id","id","page_name"));
            foreach ($token_info as $key => $value) 
            {
                $access_token_database_database[$campaign_id][$value["id"]] = $value['page_access_token'];
                $facebook_rx_fb_user_info_id = $value["facebook_rx_fb_user_info_id"];
                $facebook_rx_fb_user_info_id_database[$campaign_id] = $facebook_rx_fb_user_info_id;
            }  
            // valid campaign info and campig ids
            $campaign_info_fildered[] = $info;
            $campaign_id_array[] = $info['id']; 
            $valid_campaign_count++;      
        }


        if(count($campaign_id_array)==0) exit();        

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("tag_machine_bulk_reply",array("posting_status"=>"1","is_try_again"=>"0"));

        // get config id
        $getdata= $this->basic->get_data("facebook_rx_fb_user_info",array("where_in"=>array("id"=>$facebook_rx_fb_user_info_id_database)),array("id","facebook_rx_config_id"));
        foreach ($getdata as $key => $value) 
        {
            $facebook_rx_config_id_database[$value["id"]] = $value["facebook_rx_config_id"];
        } 


        $this->load->library("fb_rx_login");
        foreach($campaign_info_fildered as $info)
        {
            $campaign_id= $info['id']; 
            $user_id = $info["user_id"]; 
            $delay_time = $info["delay_time"];
            $catch_error_count=$info["last_try_error_count"];
            $successfully_sent=$info["successfully_sent"];

            $reply_content = $info["reply_content"];                
            $post_id = $info['post_id'];                   
            $page_id = $info['page_info_table_id']; 

            $post_access_token = isset($access_token_database_database[$campaign_id][$page_id]) ? $access_token_database_database[$campaign_id][$page_id] : "";
            if(!isset($post_access_token) || $post_access_token=="") 
            {
              $this->basic->update_data("tag_machine_bulk_reply",array("id"=>$campaign_id),array("posting_status"=>"2","is_try_again"=>"0","error_message"=>"Access token not found.","last_updated_at"=>date("Y-m-d H:i:s")));
              continue;
            }

            $uploaded_image_video = $info["uploaded_image_video"]; 
            $image=$video=$gif="";
            if($uploaded_image_video!="")
            {
              $ext_exp=explode('.', $uploaded_image_video);
              $ext=array_pop($ext_exp);
              $video_array=array("flv","mp4","wmv");
              if(in_array($ext,$video_array)) 
              $video=FCPATH.'upload/comment_reply_enhancers/'.$uploaded_image_video;  
              else if($ext=='gif') $gif=base_url("upload/comment_reply_enhancers/".$uploaded_image_video);
              else $image=base_url("upload/comment_reply_enhancers/".$uploaded_image_video); 
            }   

            $fb_rx_fb_user_info_id = $facebook_rx_fb_user_info_id_database[$campaign_id]; // find gb user id for this campaign
            $this->fb_rx_login->app_initialize($facebook_rx_config_id_database[$fb_rx_fb_user_info_id]);

            $report = json_decode($info["report"],true); // get json lead list from database and decode it
            $i=0;
            $send_report = $report;
        
            $campaign_lead=$this->basic->get_data("tag_machine_bulk_reply_send",array("where"=>array("campaign_id"=>$campaign_id,"processed"=>"0")),'','',$number_of_message_to_be_sent_in_try);
            foreach($campaign_lead as $key => $value) 
            {             
                $send_table_id = $value['id'];
                $comment_id = $value['comment_id'];
                $commenter_fb_id = $value['commenter_fb_id'];
                $commenter_name = $value['commenter_name'];
                $comment_time = $value['comment_time'];
                $commenter_name_array = explode(' ', $commenter_name);
                $commenter_last_name = array_pop($commenter_name_array);
                $commenter_first_name = implode(' ', $commenter_name_array);
                $commenter_tag_name = "@[".$commenter_fb_id."]";
                $error_msg="";
                $reply_id = "";

                //  generating message
                $reply_content_send = $reply_content;
                $reply_content_send = str_replace('#LEAD_USER_FIRST_NAME#',$commenter_first_name,$reply_content_send);
                $reply_content_send = str_replace('#LEAD_USER_LAST_NAME#',$commenter_last_name,$reply_content_send);
                $reply_content_send = str_replace('#TAG_USER#',$commenter_tag_name,$reply_content_send);
                $reply_content_send = spintax_process($reply_content_send);
                               
                try
                {
                    $response = $this->fb_rx_login->auto_comment($reply_content_send,$comment_id,$post_access_token,$image,$video,$gif);
                    if(isset($response['id']))
                    {
                       $reply_id = $response['id']; 
                       $successfully_sent++; 
                    }
                    else 
                    {
                       $catch_error_count++;
                    } 
                    if($delay_time==0)
                    sleep(rand(2,10));
                    else sleep($delay_time); 
                }

                catch(Exception $e) 
                {
                  $error_msg = $e->getMessage();
                  $catch_error_count++;
                }

                // generating new report
                $now_sent_time=date("Y-m-d H:i:s");
                $reply_status="";
                if($reply_id!="") $reply_status=$reply_id;
                else $reply_id=$error_msg;

                $send_report[$comment_id] = array
                ( 
                    "commenter_name"=>$commenter_name,
                    "commenter_fb_id"=>$commenter_fb_id,
                    "comment_id"=> $comment_id,
                    "comment_time"=> $comment_time,
                    "status" => $reply_status,
                    "replied_at" => $now_sent_time
                );

                $i++;  
                // after 10 send update report in database
                if($i%$update_report_after_time==0)
                {
                    $send_report_json= json_encode($send_report);
                    $this->basic->update_data("tag_machine_bulk_reply",array("id"=>$campaign_id),array("report"=>$send_report_json,'successfully_sent'=>$successfully_sent,"error_message"=>$error_msg,"last_try_error_count"=>$catch_error_count,"last_updated_at"=>$now_sent_time));
                }

                // updating a lead, marked as processed
                $this->basic->update_data("tag_machine_bulk_reply_send",array("id"=>$send_table_id),array('processed'=>'1',"sent_time"=>$now_sent_time,"response"=>$reply_status));
            
            } 

            // one campaign completed, now update database finally
            $send_report_json= json_encode($send_report);
            if((count($campaign_lead)<$number_of_message_to_be_sent_in_try) || $number_of_message_to_be_sent_in_try=="")
            {
                $complete_update=array("report"=>$send_report_json,"posting_status"=>'2','successfully_sent'=>$successfully_sent,'last_updated_at'=>date("Y-m-d H:i:s"),"is_try_again"=>"0","last_try_error_count"=>$catch_error_count);
                if(isset($error_msg))
                $complete_update["error_message"]=$error_msg;
                $this->basic->update_data("tag_machine_bulk_reply",array("id"=>$campaign_id),$complete_update);
            }
            else // suppose update_report_after_time=20 but there are 19 message to sent, need to update report in that case
            {
                $this->basic->update_data("tag_machine_bulk_reply",array("id"=>$campaign_id),array("report"=>$send_report_json,'successfully_sent'=>$successfully_sent,"is_try_again"=>"1",'last_updated_at'=>date("Y-m-d H:i:s")));
            }
        }          
    
    }

    public function auto_like_on_post($api_key="")
    {
        // $this->api_key_check($api_key);

        $auto_like_per_cron_job=$this->config->item("page_response_auto_like_per_cron_job");
        if($auto_like_per_cron_job=="") $auto_like_per_cron_job=10;

        $update_auto_like_report_every=$this->config->item("page_update_auto_like_report_every");
        if($update_auto_like_report_every=="") $update_auto_like_report_every=5;

        $where['where']=array("auto_like_post"=>"1"); //0 = no, 1 = yes, 2 = processing, 3 = completed
        $str =  "like_done < like_count";
        $this->db->where($str);

        $post_info= $this->basic->get_data("page_response_auto_like_share_report",$where,$select="",$join='',$limit='1', $start='', $order_by='id ASC');

        if(!empty($post_info))
            $this->basic->update_data('page_response_auto_like_share_report',array('id'=>$post_info[0]['id']),array('auto_like_post'=>'2'));

        $config_id_database = array();
        foreach($post_info as $info)
        {
            $user_id = $info['user_id'];
            $post_id = $info['post_id'];
            $post_column_id= $info['id'];
            $like_done= $info['like_done'];
            $post_user_fb_id= $info['page_response_user_info_id'];
            $like_report=json_decode($info["auto_like_report"],true);

            // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['page_response_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            $this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->load->library("fb_rx_login");
            // setting fb confid id for library call

            /***   Get all pages of the user for publishing like by them ***/

            if(!isset($pages_access_token_user_wise[$post_user_fb_id])){

                $where['where']= array("user_id"=>$user_id);
                $select="id,page_id,page_access_token,page_name";
                $page_info= $this->basic->get_data("facebook_rx_fb_page_info",$where,$select);

                $i=0;
                foreach($page_info as $p){

                    $pages_access_token_user_wise[$p['id']]['page_id'] = $p['page_id'];
                    $pages_access_token_user_wise[$p['id']]['page_access_token'] = $p['page_access_token'];
                    $pages_access_token_user_wise[$p['id']]['page_name'] = $p['page_name'];
                    $i++;
                }
            }

            $i=$like_done;
            $get_auto_like_rows=$this->basic->get_data("page_response_auto_like_report",array("where"=>array("page_response_auto_like_share_report_id"=>$post_column_id,'status'=>'0')),"","",$auto_like_per_cron_job);

            foreach($get_auto_like_rows as $like_page)
            {
                $access_token = isset($pages_access_token_user_wise[$like_page['auto_like_page_table_id']]['page_access_token']) ? $pages_access_token_user_wise[$like_page['auto_like_page_table_id']]['page_access_token'] : 0;
                /**Like on the post***/
                try
                {
                    $like_response  =  $this->fb_rx_login->auto_like($post_id,$access_token);
                    $like_report[$i]['page_name']=$pages_access_token_user_wise[$like_page['auto_like_page_table_id']]['page_name'];
                    $like_report[$i]['page_id']=$pages_access_token_user_wise[$like_page['auto_like_page_table_id']]['page_id'];
                    $like_report[$i]['status']="Success";
                }

                catch(Exception $e) 
                {
                    $like_report[$i]['page_name']=$pages_access_token_user_wise[$like_page['auto_like_page_table_id']]['page_name'];
                    $like_report[$i]['page_id']=$pages_access_token_user_wise[$like_page['auto_like_page_table_id']]['page_id'];
                    $like_report[$i]['status']= $e->getMessage();                    
                }
                $i++;
                $like_done++;
                $this->basic->delete_data("page_response_auto_like_report",array("id"=>$like_page['id']));
                sleep(rand(1,5));

                 /****       Update databse that auto like is done        *****/ 
                if($i%$update_auto_like_report_every==0 || $like_done>=$info["like_count"])
                {
                    $like_report_json= json_encode($like_report);

                    if($like_done>=$info["like_count"])
                    $updateData=array("auto_like_post"=>"3",'auto_like_report'=>$like_report_json,"like_done"=>$like_done,"like_last_tried"=>date("Y-m-d H:i:s"));
                    else
                    $updateData=array("auto_like_post"=>"2",'auto_like_report'=>$like_report_json,"like_done"=>$like_done,"like_last_tried"=>date("Y-m-d H:i:s"));

                    $this->basic->update_data("page_response_auto_like_share_report",array("id"=>$post_column_id),$updateData);
                }
            }


        }

    }

    public function auto_share_on_post($api_key="")
    {
        // $this->api_key_check($api_key);

        $auto_like_per_cron_job=$this->config->item("page_response_auto_like_per_cron_job");
        if($auto_like_per_cron_job=="") $auto_like_per_cron_job=10;

        $update_auto_share_report_every=$this->config->item("page_update_auto_share_report_every");
        if($update_auto_share_report_every=="") $update_auto_share_report_every=5;

        $where['where']=array("auto_share_post"=>"1"); //0 = no, 1 = yes, 2 = processing, 3 = completed
        $str =  "share_done < share_count";
        $this->db->where($str);

        $post_info= $this->basic->get_data("page_response_auto_like_share_report",$where,$select="",$join='',$limit='1', $start='', $order_by='id ASC');

        if(!empty($post_info))
            $this->basic->update_data('page_response_auto_like_share_report',array('id'=>$post_info[0]['id']),array('auto_share_post'=>'2'));

        $config_id_database = array();
        foreach($post_info as $info)
        {
            $user_id = $info['user_id'];
            $post_id = $info['post_id'];
            $post_column_id= $info['id'];
            $share_done= $info['share_done'];
            $post_user_fb_id= $info['page_response_user_info_id'];
            $main_page_info_table_id= $info['page_info_table_id'];
            $share_report=json_decode($info["auto_share_report"],true);

            // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['page_response_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            $this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->load->library("fb_rx_login");
            // setting fb confid id for library call

            /***   Get all pages of the user for publishing like by them ***/

            if(!isset($pages_access_token_user_wise[$post_user_fb_id])){

                $where['where']= array("user_id"=>$user_id);
                $select="id,page_id,page_access_token,page_name";
                $page_info= $this->basic->get_data("facebook_rx_fb_page_info",$where,$select);

                $i=0;
                foreach($page_info as $p){

                    $pages_access_token_user_wise[$p['id']]['page_id'] = $p['page_id'];
                    $pages_access_token_user_wise[$p['id']]['page_access_token'] = $p['page_access_token'];
                    $pages_access_token_user_wise[$p['id']]['page_name'] = $p['page_name'];
                    $i++;
                }
            }

            $i=$share_done;
            $get_auto_like_rows=$this->basic->get_data("page_response_auto_share_report",array("where"=>array("page_response_auto_like_share_report_id"=>$post_column_id,'status'=>'0')),"","",$auto_like_per_cron_job);

            $get_accesstoken=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$main_page_info_table_id)));
            $main_access_token=isset($get_accesstoken[0]["page_access_token"]) ? $get_accesstoken[0]["page_access_token"] : "";
            $post_url = array();
            $post_url['permalink_url'] = '';
            if($main_access_token != "") $post_url= $this->fb_rx_login->get_post_permalink($post_id,$main_access_token);


            foreach($get_auto_like_rows as $share_page)
            {
                $access_token = isset($pages_access_token_user_wise[$share_page['auto_share_page_table_id']]['page_access_token']) ? $pages_access_token_user_wise[$share_page['auto_share_page_table_id']]['page_access_token'] : 0;
                $sharing_page_id = isset($pages_access_token_user_wise[$share_page['auto_share_page_table_id']]['page_id']) ? $pages_access_token_user_wise[$share_page['auto_share_page_table_id']]['page_id'] : 0;


                try
                {
                    $share_response= $this->fb_rx_login->feed_post($message="",$post_url['permalink_url'],"","","","",$access_token,$sharing_page_id); 

                    $share_report[$i]['page_name']=$pages_access_token_user_wise[$share_page['auto_share_page_table_id']]['page_name'];
                    $share_report[$i]['page_id']=$pages_access_token_user_wise[$share_page['auto_share_page_table_id']]['page_id'];
                    $share_report[$i]['status']="Success";
                }

                catch(Exception $e) 
                {
                    $share_report[$i]['page_name']=$pages_access_token_user_wise[$share_page['auto_share_page_table_id']]['page_name'];
                    $share_report[$i]['page_id']=$pages_access_token_user_wise[$share_page['auto_share_page_table_id']]['page_id'];
                    $share_report[$i]['status']= $e->getMessage();                    
                }
                $i++;
                $share_done++;
                 $this->basic->delete_data("page_response_auto_share_report",array("id"=>$share_page['id']));

                $delay = $info['delay_time'];
                if($delay == 0)
                    sleep(rand(1,10));
                else
                    sleep($delay);

                 /****       Update databse that auto like is done        *****/ 
                if($i%$update_auto_share_report_every==0 || $share_done>=$info["share_count"])
                {
                    $share_report_json= json_encode($share_report);

                    if($share_done>=$info["share_count"])
                    $updateData=array("auto_share_post"=>"3",'auto_share_report'=>$share_report_json,"share_done"=>$share_done,"share_last_tried"=>date("Y-m-d H:i:s"));
                    else
                    $updateData=array("auto_share_post"=>"2",'auto_share_report'=>$share_report_json,"share_done"=>$share_done,"share_last_tried"=>date("Y-m-d H:i:s"));

                    $this->basic->update_data("page_response_auto_like_share_report",array("id"=>$post_column_id),$updateData);
                }
            }


        }
    }


    public function sms_sending_command($api_key="")
    {
        // $this->api_key_check($api_key);

        $this->load->library('Sms_manager');

        $number_of_sms_to_be_sent_in_try = $this->config->item("number_of_sms_to_be_sent_in_try");

        if($number_of_sms_to_be_sent_in_try == "") 
            $number_of_sms_to_be_sent_in_try = 100; // default 10
        else if($number_of_sms_to_be_sent_in_try == 0) 
            $number_of_sms_to_be_sent_in_try = ""; // 0 means unlimited

        $update_sms_sending_report_after_time = $this->config->item("update_sms_sending_report_after_time"); 

        if($update_sms_sending_report_after_time == "" || $update_sms_sending_report_after_time == 0) 
            $update_sms_sending_report_after_time = 10;

        $number_of_campaign_to_be_processed = 1; // max number of campaign that can be processed by this cron job
        // $number_of_message_tob_be_sent = 50000;  // max number of message that can be sent in an hour

        $where['or_where'] = array('posting_status'=>"0","is_try_again"=>"1");

        /****** Get all campaign from database where status=0 means pending ******/
        $join = array('users'=>'sms_sending_campaign.user_id=users.id,left');
        $campaign_info = $this->basic->get_data("sms_sending_campaign",$where,$select=array("sms_sending_campaign.*","users.deleted as user_deleted","users.status as user_status"),$join,$limit=50, $start=0, $order_by='schedule_time ASC');

        $campaign_id_array = array();  // all selected campaign id array
        $campaign_info_fildered = array(); // valid for process, campign info array

        $valid_campaign_count = 1;
        foreach($campaign_info as $info)
        {
            if($info['user_deleted'] == '1' || $info['user_status']=="0")
            {
                $this->db->where("id",$info['id']);
                $this->db->update("sms_sending_campaign",array("posting_status"=>"1","is_try_again"=>"0"));
                continue;
            } 

            $user_id       = $info["user_id"];
            $sms_api       = $info['api_id'];
            $campaign_id   = $info['id'];
            $time_zone     = $info['time_zone'];
            $schedule_time = $info['schedule_time']; 
            $total_thread  = $info["total_thread"];       

            if($time_zone) date_default_timezone_set($time_zone);            
            $now_time = date("Y-m-d H:i:s");

            if((strtotime($now_time) < strtotime($schedule_time)) && $time_zone!="") continue; 
            if($valid_campaign_count > $number_of_campaign_to_be_processed) break; 

           
            // valid campaign info and campig ids
            $campaign_info_fildered[] = $info;
            $campaign_id_array[] = $info['id']; 
            $valid_campaign_count++;      
        }

        if(count($campaign_id_array) == 0) exit();

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("sms_sending_campaign",array("posting_status"=>"1","is_try_again"=>"0"));

        foreach($campaign_info_fildered as $final_campaign_info)
        {
            $i = 0;

            $campaign_id       = $final_campaign_info['id'];
            $user_id           = $final_campaign_info["user_id"]; 
            $sms_api           = $final_campaign_info['api_id'];
            $campaign_message  = $final_campaign_info['campaign_message'];  
            $successfully_sent = $final_campaign_info["successfully_sent"];
            $manual_phones     = explode(",",$final_campaign_info['manual_phone']);
            $pageTableId       = $final_campaign_info['page_id'];
            $campaign_delay       = $final_campaign_info['campaign_delay'];


            $campaign_contacts = $this->basic->get_data("sms_sending_campaign_send",array("where"=>array("campaign_id"=>$campaign_id,"processed"=>"0")),'','',$number_of_sms_to_be_sent_in_try);

            foreach ($campaign_contacts as $contacts_details) 
            {
                $send_table_id      = $contacts_details['id'];
                $contact_first_name = isset($contacts_details['contact_first_name']) ? $contacts_details['contact_first_name']:"";
                $contact_last_name  = isset($contacts_details['contact_last_name']) ? $contacts_details['contact_last_name']:"";
                $contact_email      = isset($contacts_details['contact_email']) ? $contacts_details['contact_email']:"";
                $contact_mobile     = isset($contacts_details['contact_phone_number']) ? $contacts_details['contact_phone_number']:"";
                $contact_phone      = $contacts_details['contact_phone_number'];


                if($campaign_delay!="" && $campaign_delay!=0){
                    sleep($campaign_delay);
                }




                $campaign_message_send = $campaign_message;
                $campaign_message_send = str_replace(array("#FIRST_NAME#","#first_name#","#firstname#"),$contact_first_name,$campaign_message_send);
                $campaign_message_send = str_replace(array("#LAST_NAME#","#last_name#","#lastname#"),$contact_last_name,$campaign_message_send);
                $message_sent_id = "";

                $this->sms_manager->set_credentioal($sms_api,$user_id);

                try {

                    $campaign_message_send = str_replace(array("'",'"'),array('`','`'),$campaign_message_send);
                    $response = $this->sms_manager->send_sms($campaign_message_send, $contact_phone);

                    if(isset($response['id']) && !empty($response['id'])) {   
                        $message_sent_id = $response['id']; 
                        $successfully_sent++; 
                    } else {   
                        if(isset($response['status']) && !empty($response['status'])) {
                            $message_sent_id = $response["status"];
                        }
                    }           
                    
                } catch(Exception $e) {
                   $message_sent_id = $e->getMessage();
                }

                // generating new report with send message info
                $now_sent_time = date("Y-m-d H:i:s");

                $i++;  
                // after 10 send update report in database
                if($i%$update_sms_sending_report_after_time==0)
                {
                    $this->basic->update_data("sms_sending_campaign",array("id"=>$campaign_id),array('successfully_sent'=>$successfully_sent));
                }
                
                // updating a contact, marked as processed
                $this->basic->update_data("sms_sending_campaign_send",array("id"=>$send_table_id),array('processed'=>'1',"sent_time"=>$now_sent_time,"delivery_id"=>$message_sent_id));
            }

            if((count($campaign_contacts) < $number_of_sms_to_be_sent_in_try) || $number_of_sms_to_be_sent_in_try == "")
            {
                $complete_update = array("posting_status"=>'2','successfully_sent'=>$successfully_sent,'completed_at'=>date("Y-m-d H:i:s"),"is_try_again"=>"0");                
                $this->basic->update_data("sms_sending_campaign",array("id"=>$campaign_id),$complete_update);
            }
            else // suppose update_sms_sending_report_after_time=20 but there are 19 message to sent, need to update report in that case
            { 
                $this->basic->update_data("sms_sending_campaign",array("id"=>$campaign_id),array('successfully_sent'=>$successfully_sent,"is_try_again"=>"1"));
            }
        }          
    }

    public function email_sending_command($api_key="")
    {
        // $this->api_key_check($api_key);
        $number_of_email_to_be_sent_in_try = $this->config->item("number_of_email_to_be_sent_in_try");

        if($number_of_email_to_be_sent_in_try == "") 
            $number_of_email_to_be_sent_in_try = 100; // default 10
        else if($number_of_email_to_be_sent_in_try == 0) 
            $number_of_email_to_be_sent_in_try = ""; // 0 means unlimited

        $update_email_sending_report_after_time = $this->config->item("update_email_sending_report_after_time"); 

        if($update_email_sending_report_after_time == "" || $update_email_sending_report_after_time == 0) 
            $update_email_sending_report_after_time = 10;

        $number_of_campaign_to_be_processed = 1; // max number of campaign that can be processed by this cron job
        // $number_of_message_tob_be_sent = 50000;  // max number of message that can be sent in an hour

        $where['or_where'] = array('posting_status'=>"0","is_try_again"=>"1");

        /****** Get all campaign from database where status=0 means pending ******/
        $join = array('users'=>'email_sending_campaign.user_id=users.id,left');
        $campaign_info = $this->basic->get_data("email_sending_campaign",$where,$select=array("email_sending_campaign.*","users.deleted as user_deleted","users.status as user_status"),$join,$limit=50, $start=0, $order_by='schedule_time ASC');


        $campaign_id_array = array();  // all selected campaign id array
        $campaign_info_fildered = array(); // valid for process, campign info array

        $valid_campaign_count = 1;
        foreach($campaign_info as $info1)
        {
            if($info1['user_deleted'] == '1' || $info1['user_status']=="0")
            {
                $this->db->where("id",$info1['id']);
                $this->db->update("email_sending_campaign",array("posting_status"=>"1","is_try_again"=>"0"));
                continue;
            } 

            $campaign_id   = $info1['id'];
            $user_id       = $info1["user_id"];           
            $time_zone     = $info1['time_zone'];
            $schedule_time = $info1['schedule_time']; 
            $total_thread  = $info1["total_thread"];

            if($time_zone) date_default_timezone_set($time_zone);            
            $now_time = date("Y-m-d H:i:s");

            if((strtotime($now_time) < strtotime($schedule_time)) && $time_zone!="") continue; 
            if($valid_campaign_count > $number_of_campaign_to_be_processed) break;
           
            // valid campaign info and campig ids
            $campaign_info_fildered[] = $info1;
            $campaign_id_array[] = $info1['id']; 
            $valid_campaign_count++;
        }


        if(count($campaign_id_array) == 0) exit();        

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("email_sending_campaign",array("posting_status"=>"1","is_try_again"=>"0"));

        foreach($campaign_info_fildered as $info2)
        {
            $i = 0;

            $campaign_id       = $info2['id'];
            $user_id           = $info2["user_id"];
            $configure_email_table = $info2['configure_email_table'];
            $email_api     = $info2['api_id'];
            $pageTableId   = $info2['page_id'];

            $subject = $info2['email_subject'];

            $from_email = "";

            if ($configure_email_table == "email_smtp_config") {
                $from_email = "smtp_".$info2["api_id"];

            } elseif ($configure_email_table == "email_mandrill_config") {

                $from_email = "mandrill_".$info2["api_id"];

            } elseif ($configure_email_table == "email_sendgrid_config") {

                $from_email = "sendgrid_".$info2["api_id"];

            } elseif ($configure_email_table == "email_mailgun_config") {

                $from_email = "mailgun_".$info2["api_id"];
            }

            $output_dir = FCPATH."upload/attachment";
            $filename = $info2['email_attachment'];

            if($filename == "0") {
                $filename = "";
            }
            
            if($filename != "") {
                $attachement = $output_dir.'/'.$filename;
            }
            else {
                $attachement = "";
            }

            $campaign_message  = $info2['email_message'];  
            $successfully_sent = $info2["successfully_sent"];

            $where1['where']   = array("campaign_id"=>$campaign_id,"processed"=>"0");
            $campaign_contacts = $this->basic->get_data("email_sending_campaign_send",$where1,'','',$number_of_email_to_be_sent_in_try);

            foreach ($campaign_contacts as $contacts_details) 
            {
                $send_table_id      = $contacts_details['id'];
                $contactid          = $contacts_details['contact_id'];
                $subscriberId       = $contacts_details['subscriber_id'];
                $contact_first_name = isset($contacts_details['contact_first_name']) ? $contacts_details['contact_first_name']:"";
                $contact_last_name  = isset($contacts_details['contact_last_name']) ? $contacts_details['contact_last_name']:"";
                $contact_email      = isset($contacts_details['contact_email']) ? $contacts_details['contact_email']:"";
                $contact_mobile     = isset($contacts_details['contact_phone']) ? $contacts_details['contact_phone']:"";

                $formatted = "";
                $unscubscribe_btn = "";

                $unsubscription_info = $campaign_id."-".$send_table_id;

                if($contactid != "0") {
                    $formatted = $contactid.'-contact-'.$unsubscription_info;
                    $unscubscribe_btn   = '<a href="'.base_url("home/unsubscribe/").bin2hex($formatted).'/'.bin2hex($contact_email).'">'.$this->lang->line('Click to Unsubscribe').'</a>';
                } else if($subscriberId != "0") {
                    $formatted = $subscriberId.'-subscriber-'.$unsubscription_info;
                    $unscubscribe_btn   = '<a href="'.base_url("home/unsubscribe/").bin2hex($formatted).'/'.bin2hex($contact_email).'">'.$this->lang->line('Click to Unsubscribe').'</a>';
                }

                $campaign_message_send  = $campaign_message;
                $campaign_message_send  = str_replace(array("#FIRST_NAME#","#firstname#"),$contact_first_name,$campaign_message_send);
                $campaign_message_send  = str_replace(array("#LAST_NAME#","#lastname#"),$contact_last_name,$campaign_message_send);
                $campaign_message_send  = str_replace(array("#MOBILE#","#mobile#"),$contact_mobile,$campaign_message_send);
                $campaign_message_send  = str_replace(array("#EMAIL_ADDRESS#","#email#"),$contact_email,$campaign_message_send);
                $campaign_message_send  = str_replace("#UNSUBSCRIBE_LINK#",$unscubscribe_btn,$campaign_message_send);

                if($this->config->item("enable_open_rate") == "1") {

                    $open_rate_url_into_msg = base_url('sms_email_manager/checking_open_rate/').$campaign_id.'/'.$send_table_id;
                    $campaign_message_send  = $campaign_message_send."<img src='".$open_rate_url_into_msg."' width='1px' height='1px'>";

                }

                if($this->config->item("enable_click_rate") == "1") {
                    
                    $newurl = base_url("sms_email_manager/checking_click_rate/").$campaign_id.'/'.$send_table_id;

                    $matched_links = "";

                    if(preg_match_all("@href=[\"|\'](https?://[^\s]*)[\"|\']@",$campaign_message_send,$matches)) {

                        $matched_links = json_encode($matches[1]);

                        if(isset($matched_links) && !empty($matched_links)) {

                            $clickRateTableData = array();
                            $clickRateTableData['user_id'] = $user_id;
                            $clickRateTableData['campaign_id'] = $campaign_id;
                            $clickRateTableData['links'] = $matched_links;

                            if(!$this->basic->is_exist("email_clickrate_links_backup",array("campaign_id"=>$campaign_id))) {
                                $this->basic->insert_data("email_clickrate_links_backup",$clickRateTableData);
                            }

                            $campaign_message_send = preg_replace_callback(
                                "@href=[\"|\'](https?://[^\s]*)[\"|\']@",
                                function ($matches) use ($newurl) {
                                    static $i = 0;
                                    $customUrl = "href='{$newurl}/{$i}'";
                                    $i++;
                                    return $customUrl;
                                },
                                $campaign_message_send
                            );

                        }

                    }
                }

                $message_sent_id = "";

                try
                {
                    $campaign_message_send = $campaign_message_send;
                    $response = $this->_email_send_function($from_email, $campaign_message_send, $contact_email, $subject, $attachement, $filename,$user_id);

                    if((isset($response) && !empty($response)) && $response == "Submited")
                    {   
                        $message_sent_id = $response; 
                        $successfully_sent++;
                    }
                    else 
                    {   
                        $message_sent_id = $response;
                    }           
                }
                catch(Exception $e) 
                {
                   $message_sent_id = $e->getMessage();
                }

                // generating new report with send message info
                $now_sent_time = date("Y-m-d H:i:s");
                $i++;
                // after 10 send update report in database
                if($i%$update_email_sending_report_after_time==0)
                {
                    $this->basic->update_data("email_sending_campaign",array("id"=>$campaign_id),array('successfully_sent'=>$successfully_sent));
                }
                
                // updating a contact, marked as processed
                $this->basic->update_data("email_sending_campaign_send",array("id"=>$send_table_id),array('processed'=>'1',"sent_time"=>$now_sent_time,"delivery_id"=>$message_sent_id));
            }


            if((count($campaign_contacts) < $number_of_email_to_be_sent_in_try) || $number_of_email_to_be_sent_in_try == "")
            {         
                $complete_update = array("posting_status"=>'2','successfully_sent'=>$successfully_sent,'completed_at'=>date("Y-m-d H:i:s"),"is_try_again"=>"0");            
                $this->basic->update_data("email_sending_campaign",array("id"=>$campaign_id),$complete_update);
            }
            else // suppose update_email_sending_report_after_time=20 but there are 19 message to sent, need to update report in that case
            { 
                $this->basic->update_data("email_sending_campaign",array("id"=>$campaign_id),array('successfully_sent'=>$successfully_sent,"is_try_again"=>"1"));
            }
        }     
    }

    // Woo commerce abandoned cart recovery add-on
    private function woocommerce_spin_and_replace($str="",$replace = array(),$is_spin=true)
   	{
   		if(!isset($replace['first_name'])) $replace['first_name'] = '';
   		if(!isset($replace['last_name'])) $replace['last_name'] = '';
   		if(!isset($replace['email'])) $replace['email'] = '';
   		if(!isset($replace['mobile'])) $replace['mobile'] = '';
   		if(!isset($replace['cart_url'])) $replace['cart_url'] = '';
   		if(!isset($replace['checkout_url'])) $replace['checkout_url'] = '';

   		$replace_values = array_values($replace);
   		$str = str_replace(array("{{first_name}}","{{last_name}}","{{email}}","{{mobile}}","{{cart_url}}","{{checkout_url}}"), $replace_values, $str);

   		if($is_spin) return spintax_process($str);
   		else return $str;
   	}

   	private function woocommerce_send_messenger_reminder($message='',$page_access_token='')
   	{        
   		$sent_response = array();

   		// return $sent_response = array("response"=>'515151515151551',"status"=>'1'); 

        $this->load->library("fb_rx_login"); 
        try
   		{
   		    $response = $this->fb_rx_login->send_non_promotional_message_subscription($message,$page_access_token);
   		
   		    if(isset($response['message_id']))
   		    {
   		       $sent_response = array("response"=>$response['message_id'],"status"=>'1'); 
   		    }
   		    else 
   		    {
   		        if(isset($response["error"]["message"])) 
   		        $sent_response = array("response"=> $response["error"]["message"],"status"=>'0');              
   		    }              
   		    
   		}
   		catch(Exception $e) 
   		{
   		  $sent_response = array("response"=> $e->getMessage(),"status"=>'0'); 
   		}
   		return $sent_response;
   	}

    public function woocommerce_abandoned_cart_reminder($api_key="")
    {       
        // $this->api_key_check($api_key);
        $number_of_row=100; // number of subscriber on cron will process

        // reseting to system timezone
        $time_zone = $this->config->item('time_zone');
        if($time_zone== '') $time_zone="Europe/Dublin";        
        date_default_timezone_set($time_zone);
        
        $page_database=array(); // associated page auto id and page id

        // getting eligible subscriber data
        $subscriber_where=
        array
        (
            "where"=>array
            (
                "woocommerce_drip_campaign_id !="=>"0",
                "initial_date !="=>"0000-00-00 00:00:00",
                "is_totally_completed"=>"0",
                "processing_status"=>"0",
                "action_type !="=>"checkout"
            ),
        );
        $select = "woocommerce_drip_campaign_webhook_call.*,messenger_content,sms_content,woocommerce_drip_campaign.sms_api_id,woocommerce_drip_campaign.email_subject,email_content,woocommerce_drip_campaign.email_api_id,configure_email_table,page_access_token";
        $join =  array('woocommerce_drip_campaign'=>"woocommerce_drip_campaign.id=woocommerce_drip_campaign_webhook_call.woocommerce_drip_campaign_id,left",'facebook_rx_fb_page_info'=>'facebook_rx_fb_page_info.id=woocommerce_drip_campaign_webhook_call.page_table_id,right');
        $subscriber_data=$this->basic->get_data("woocommerce_drip_campaign_webhook_call",$subscriber_where,$select,$join,$number_of_row,NULL,'last_processing_started_at ASC');
        if(empty($subscriber_data)) exit();

        // echo "<pre>";print_r($subscriber_data); exit();

        // marking subscribers this cron is operating as processing (comment this query while test)
        $this->db->where($subscriber_where["where"]);
        $this->db->update("woocommerce_drip_campaign_webhook_call", array('processing_status' => "1","last_processing_started_at"=>date("Y-m-d H:i:s")));

        $this->load->library('Sms_manager');
        foreach ($subscriber_data as $key => $value) 
        {            
            $user_id=$value["user_id"];
            $wc_first_name=$value["wc_first_name"];
            $wc_last_name=$value["wc_last_name"];
            $wc_email=$value["wc_email"];
            $wc_mobile=$value["wc_mobile"];
            $woocommerce_drip_campaign_id=$value["woocommerce_drip_campaign_id"];
            $initial_date=$value["initial_date"];
            $last_completed_hour=$value["last_completed_hour"];
            $page_table_id=$value["page_table_id"];
            $sms_api_id=$value["sms_api_id"];
            $email_api_id=$value["email_api_id"];
            $email_subject=$value["email_subject"];
            $configure_email_table=$value["configure_email_table"];
            $cart_url=$value["cart_url"];
            $checkout_url=$value["checkout_url"];
            $woocommerce_drip_campaign_webhook_call_id=$value["id"];
            $check_box_unique_id=$value["check_box_unique_id"];
            $page_access_token=$value["page_access_token"];
            $page_table_id=$value["page_table_id"];

            $replace_variables = array("first_name"=>$wc_first_name,"last_name"=>$wc_last_name,"email"=>$wc_email,"mobile"=>$wc_mobile,"cart_url"=>$cart_url,"checkout_url"=>$checkout_url);

            // $messenger_content=json_decode($value['messenger_content'],true);
            $sms_content=json_decode($value['sms_content'],true);
            $email_content=json_decode($value['email_content'],true);
            $messenger_content=json_decode($value['messenger_content'],true);
            $cart_info=json_decode($value['cart_info'],true);
            
            $reminder_content = array();
            if(isset($messenger_content['reminder']))
            foreach ($messenger_content['reminder'] as $key2 => $value2) 
            {
            	$reminder_content[$value2['hour']]['messenger'] = $value2;
            }

            if(isset($sms_content['reminder']))
            foreach ($sms_content['reminder'] as $key2 => $value2) 
            {
            	$reminder_content[$value2['hour']]['sms'] = $value2;
            }

            if(isset($email_content['reminder']))
            foreach ($email_content['reminder'] as $key2 => $value2) 
            {
            	$reminder_content[$value2['hour']]['email'] = $value2;
            }
            ksort($reminder_content);

            // echo "<pre>";print_r($reminder_content); echo "<pre>";


            // if there is no hourly sequence not sentn, then update the subscribers hourly campaign as completed. 
            if(empty($reminder_content)){
                 $this->basic->update_data("woocommerce_drip_campaign_webhook_call",array("id"=>$woocommerce_drip_campaign_webhook_call_id),array("is_totally_completed"=>"1"));
                 continue;
            }

            $message_hours=array_keys($reminder_content); // th hour campaign will send message
            $max_send_hour=max($message_hours); // maximum campaign hour, will decide campaign totally complete or not
           

            foreach ($message_hours as $key2 => $value2)
            {
               if($value2>$last_completed_hour) // getting the next hour to start sending message
               {                
                  $today=date("Y-m-d H:i:s");
                 
                  $sending_hour=$value2; // currently processing this drip time
                  $sending_minutes=$sending_hour*60;
                  $sending_date=date('Y-m-d H:i:s', strtotime($initial_date. ' + '.$sending_minutes.' minutes'));
                  $is_totally_completed='0';
                  if($max_send_hour==$sending_hour) $is_totally_completed='1';

                  // calculate after 24 hours date. If for any reason it exceeds 24 hours then complete the campaign.
                  $after_24_date=date('Y-m-d H:i:s', strtotime($initial_date. '+ 24 hours'));    

                  if(strtotime($today)>strtotime($after_24_date)) // if somehow some subscriber was failed to sent message and it will never be comeplete so we are canceling it
                  {
                    $this->basic->update_data("woocommerce_drip_campaign_webhook_call",array("id"=>$woocommerce_drip_campaign_webhook_call_id),array("is_totally_completed"=>"1"));
                  }            

                  if(strtotime($today)>=strtotime($sending_date) && strtotime($today)<strtotime($after_24_date) ) // deciding if we have to send message or not now
                  {
                    //getting message template
                    $sent_response=array();      

                    //making message to be sent  
                    $temp_reminder_text = isset($reminder_content[$sending_hour]['messenger']['reminder_text']) ? $this->woocommerce_spin_and_replace($reminder_content[$sending_hour]['messenger']['reminder_text'],$replace_variables) : "";
                    $temp_reminder_btn_details = isset($reminder_content[$sending_hour]['messenger']['reminder_btn_details']) ? $reminder_content[$sending_hour]['messenger']['reminder_btn_details'] : "VISIT DETAILS";
                    $temp_reminder_text_checkout = isset($reminder_content[$sending_hour]['messenger']['reminder_text_checkout']) ? $this->woocommerce_spin_and_replace($reminder_content[$sending_hour]['messenger']['reminder_text_checkout'],$replace_variables) : "";
                    $temp_reminder_btn_checkout = isset($reminder_content[$sending_hour]['messenger']['reminder_btn_checkout']) ? $reminder_content[$sending_hour]['messenger']['reminder_btn_checkout'] : "CHECKOUT NOW";

                    $temp_carousel_element = array();
                    $temp_i=0;
                    foreach ($cart_info as $key3 => $value3) 
                    {
                    	$temp_title =  isset($value3['product_name']) ? $value3['product_name'] : "Cart Item";
                    	$temp_price =  isset($value3['price']) ? $value3['price'] : "0";
                    	$temp_quantity =  isset($value3['quantity']) ? $value3['quantity'] : "1";
                    	$temp_currency =  isset($value3['currency']) ? $value3['currency'] : "USD";
                    	$temp_thumbnail =  isset($value3['thumbnail']) ? $value3['thumbnail'] : "https://via.placeholder.com/500x300";
                    	
                    	$temp_url =  isset($value3['permalink']) ? $value3['permalink'] : "https://via.placeholder.com/500x300";
                    	$temp_action = array("type"=>"web_url","url"=>$temp_url);
                    	$temp_button = array(0=>array("type"=>"web_url","url"=>$temp_url,"title"=>$temp_reminder_btn_details));
                    	$temp_button_checkout = array(0=>array("type"=>"web_url","url"=>$checkout_url,"title"=>$temp_reminder_btn_checkout));

                    	$temp_carousel_element[$temp_i]['title'] = $temp_title;
                    	$temp_carousel_element[$temp_i]['subtitle'] = "Qty : ".$temp_quantity." | "."Price : ".$temp_currency." ".$temp_price;
                    	$temp_carousel_element[$temp_i]['image_url'] = $temp_thumbnail;
                    	$temp_carousel_element[$temp_i]['default_action'] = $temp_action;
                    	$temp_carousel_element[$temp_i]['buttons'] = $temp_button;
                    	$temp_i++;
                    }                    

                    $messenger_reminder_message_send1 = json_encode(array("recipient"=>array("user_ref"=>$check_box_unique_id),"message"=>array("text"=>$temp_reminder_text)));
                    $messenger_reminder_message_send2 = array 
                    (
					  "recipient" => array("user_ref"=>$check_box_unique_id),					  
					  'message' => array
					  (
					  	'attachment' => 
						  array 
						  (
						    'type' => 'template',
						    'payload' => 
						    array 
						    (
						      'template_type' => 'generic',
						      'elements' => $temp_carousel_element
						    ),
						  )
					  )					  
					);
                    $messenger_reminder_message_send2 = json_encode($messenger_reminder_message_send2);

                    $messenger_reminder_message_send3 = array 
                    (
					  "recipient" => array("user_ref"=>$check_box_unique_id),					  
					  'message' => array
					  (
					  	'attachment' => 
						  array 
						  (
						    'type' => 'template',
						    'payload' => 
						    array 
						    (
						      'template_type' => 'button',
						      'text' => $temp_reminder_text_checkout,
						      'buttons'=>$temp_button_checkout
						    ),
						  )
					  )					  
					);
					$messenger_reminder_message_send3 = json_encode($messenger_reminder_message_send3);


					if(trim($temp_reminder_text)!="") $sent_response['messenger']['template1'] = $this->woocommerce_send_messenger_reminder($messenger_reminder_message_send1,$page_access_token);
					$sent_response['messenger']['template2'] = $this->woocommerce_send_messenger_reminder($messenger_reminder_message_send2,$page_access_token);
					$sent_response['messenger']['template3'] = $this->woocommerce_send_messenger_reminder($messenger_reminder_message_send3,$page_access_token);
              
		
                    //  SMS Sending block
                    if($wc_mobile!="" && $sms_api_id!='0')
                    {
                    	$temp_reminder_text_sms = isset($reminder_content[$sending_hour]['sms']['reminder_text']) ? $this->woocommerce_spin_and_replace($reminder_content[$sending_hour]['sms']['reminder_text'],$replace_variables,false) : "";
   		                $temp_reminder_text_sms = str_replace(array("'",'"'),array('`','`'),$temp_reminder_text_sms);
                   		
                   		$this->sms_manager->set_credentioal($sms_api_id,$user_id);
                   		$sms_response = array("response"=> 'missing param',"status"=>'0');

                   		if(trim($temp_reminder_text_sms)!="")
                   		{
	                		try
			                {
			                    $response = $this->sms_manager->send_sms($temp_reminder_text_sms, $wc_mobile);

			                    if(isset($response['id']) && !empty($response['id']))
			                    {   
			                        $message_sent_id = $response['id'];
			                        $sms_response = array("response"=> $message_sent_id,"status"=>'1');              
			                    }
			                    else 
			                    {   if(isset($response['status']) && !empty($response['status']))
			                		{
			                            $message_sent_id = $response["status"];
			                            $sms_response = array("response"=> $message_sent_id,"status"=>'0');  
			                        }
			                    }           
			                    
			                }
			                catch(Exception $e) 
			                {
			                   $message_sent_id = $e->getMessage();
			                   $sms_response = array("response"=> $message_sent_id,"status"=>'0');
			                }
                   		}

   		                $sent_response['sms']=$sms_response;
                    }
                    //  SMS Sending block



                    //  Email Sending block
                    if($wc_email!="" && $email_api_id!='0')
                    {
                    	$temp_reminder_text_email = isset($reminder_content[$sending_hour]['email']['reminder_text']) ? $this->woocommerce_spin_and_replace($reminder_content[$sending_hour]['email']['reminder_text'],$replace_variables,false) : "";
                    	$from_email = "";

                    	if ($configure_email_table == "email_smtp_config") 
                    	{
                    		$from_email = "smtp_".$email_api_id;

                    	} 
                    	elseif ($configure_email_table == "email_mandrill_config") 
                    	{
                    		$from_email = "mandrill_".$email_api_id;

                    	} 
                    	elseif ($configure_email_table == "email_sendgrid_config") 
                    	{
                    		$from_email = "sendgrid_".$email_api_id;
                    	} 
                    	elseif ($configure_email_table == "email_mailgun_config") 
                    	{
                    		$from_email = "mailgun_".$email_api_id;
                    	}

                    	$email_response = array("response"=> 'missing param',"status"=>'0');  
                    	if(trim($temp_reminder_text_email)!='')
                    	{
	                    	try
	                    	{
	                    		$response = $this->_email_send_function($from_email, $temp_reminder_text_email, $wc_email, $email_subject, $attachement='', $filename='',$user_id);
	              
	                    		if(isset($response) && !empty($response) && $response == "Submited")
	                    		{   
	                    			$message_sent_id = $response;
	                    			if($message_sent_id=="Submited") $message_sent_id = "Submitted";
	                    			$email_response = array("response"=> $message_sent_id,"status"=>'1');  
	                    		}
	                    		else 
	                    		{   
	                    			$message_sent_id = $response;
	                    			$email_response = array("response"=> $message_sent_id,"status"=>'0');  
	                    		}           
	                    	}
	                    	catch(Exception $e) 
	                    	{
	                    		$message_sent_id = $e->getMessage();
	                    		$email_response = array("response"=> $message_sent_id,"status"=>'0');  
	        	            }
	        	        }
        	            $sent_response['email']=$email_response;
                    }
                    //  Email Sending block



                    $curdate = date("Y-m-d H:i:s");
                    $insert_data=array
                    (
                        "woocommerce_drip_campaign_id"=>$woocommerce_drip_campaign_id,
                        "page_id"=>$page_table_id,
                        "user_id"=>$user_id,
                        "subscribe_id"=>$check_box_unique_id,
                        "first_name"=>$wc_first_name,
                        "last_name"=>$wc_last_name,
                        "email"=>$wc_email,
                        "last_completed_hour"=>$sending_hour,
                        "is_sent"=>'1',
                        "sent_at"=>$curdate,
                        "sent_response"=>json_encode($sent_response),
                        "last_updated_at"=>$curdate
                    );
                    $this->basic->insert_data("woocommerce_drip_campaign_report",$insert_data); // inserting send report
 
                    $sub_update=array
                    (
                        "last_completed_hour"=>$sending_hour,
                        "is_totally_completed"=>$is_totally_completed,
                        "last_sent_at"=>$curdate,
                        "processing_status"=>"0",
                    );
                    $this->basic->update_data("woocommerce_drip_campaign_webhook_call",array("id"=>$woocommerce_drip_campaign_webhook_call_id),$sub_update);// updating subscriber so that it will process next drip day again

                  }
                  break;
               }
            }             
        }

        //marking subscribers this cron is operating as ok to process by another cron later  (comment this query while test)
        if(isset($subscriber_where['where']['processing_status'])) unset($subscriber_where['where']['processing_status']);
        $this->db->where($subscriber_where["where"]);
        $this->db->update("woocommerce_drip_campaign_webhook_call", array('processing_status' => "0"));
        $this->db->update("woocommerce_drip_campaign", array('last_sent_at' => date("Y-m-d H:i:s")));
    }

    private function ecommerce_spin_and_replace($str="",$replace = array(),$is_spin=true)
    {      
      if(!isset($replace['store_name'])) $replace['store_name'] = '';
      if(!isset($replace['store_url'])) $replace['store_url'] = '';
      if(!isset($replace['order_no'])) $replace['order_no'] = '';
      if(!isset($replace['order_url'])) $replace['order_url'] = '';
      if(!isset($replace['checkout_url'])) $replace['checkout_url'] = '';
      if(!isset($replace['my_orders_url'])) $replace['my_orders_url'] = '';
      if(!isset($replace['last_name'])) $replace['last_name'] = '';
      if(!isset($replace['first_name'])) $replace['first_name'] = '';
      if(!isset($replace['email'])) $replace['email'] = '';
      if(!isset($replace['mobile'])) $replace['mobile'] = '';

      $replace_values = array_values($replace);
      $str = str_replace(array("{{store_name}}","{{store_url}}","{{order_no}}","{{order_url}}","{{checkout_url}}","{{my_orders_url}}","{{last_name}}","{{first_name}}","{{email}}","{{mobile}}"), $replace_values, $str);
      if($is_spin) return spintax_process($str);
      else return $str;
    }

    private function ecommerce_send_messenger_reminder($message='',$page_access_token='')
    {        
        $sent_response = array();

        // return $sent_response = array("response"=>'515151515151551',"status"=>'1'); 

        $this->load->library("fb_rx_login"); 
        try
        {
            $response = $this->fb_rx_login->send_non_promotional_message_subscription($message,$page_access_token);
        
            if(isset($response['message_id']))
            {
               $sent_response = array("response"=>$response['message_id'],"status"=>'1'); 
            }
            else 
            {
                if(isset($response["error"]["message"])) 
                $sent_response = array("response"=> $response["error"]["message"],"status"=>'0');              
            }              
            
        }
        catch(Exception $e) 
        {
          $sent_response = array("response"=> $e->getMessage(),"status"=>'0'); 
        }
        return $sent_response;
    }


    public function ecommerce_abandoned_cart_reminder($api_key="")
    {       
        // $this->api_key_check($api_key);
        $number_of_row=100; // number of subscriber on cron will process

        // reseting to system timezone
        $time_zone = $this->config->item('time_zone');
        if($time_zone== '') $time_zone="Europe/Dublin";        
        date_default_timezone_set($time_zone);
        
        $page_database=array(); // associated page auto id and page id

        // getting eligible subscriber data
        $subscriber_where=
        array
        (
            "where"=>array
            (
                "initial_date !="=>"0000-00-00 00:00:00",
                "is_totally_completed"=>"0",
                "processing_status"=>"0",
                "action_type !="=>"checkout"
            ),
        );
        $select = "ecommerce_cart.*,store_unique_id,store_name,messenger_content,sms_content,email_content,ecommerce_store.page_id,ecommerce_store.sms_api_id,ecommerce_store.email_api_id,email_subject,email_content,configure_email_table,page_access_token";
        $join =  array('ecommerce_store'=>"ecommerce_store.id=ecommerce_cart.store_id,left",'facebook_rx_fb_page_info'=>'facebook_rx_fb_page_info.id=ecommerce_store.page_id,right');
        $subscriber_data=$this->basic->get_data("ecommerce_cart",$subscriber_where,$select,$join,$number_of_row,NULL,'last_processing_started_at ASC');
        if(empty($subscriber_data)) exit();

        // echo "<pre>";print_r($subscriber_data); exit();

        // marking subscribers this cron is operating as processing (comment this query while test)
        $this->db->where($subscriber_where["where"]);
        $this->db->update("ecommerce_cart", array('processing_status' => "1","last_processing_started_at"=>date("Y-m-d H:i:s")));

        $this->load->library('Sms_manager');
        foreach ($subscriber_data as $key => $value) 
        {            
            $user_id=$value["user_id"];
            $subscriber_id=$value["subscriber_id"];
            $wc_first_name=$value["buyer_first_name"];
            $wc_last_name=$value["buyer_last_name"];
            $wc_email=!empty($value["bill_email"])?$value["bill_email"]:$value["buyer_email"];
            $wc_mobile=!empty($value["bill_mobile"])?$value["bill_mobile"]:$value["buyer_mobile"];
            $initial_date=$value["initial_date"];
            $last_completed_hour=$value["last_completed_hour"];
            $page_table_id=$value["page_id"];
            $sms_api_id=$value["sms_api_id"];
            $email_api_id=$value["email_api_id"];
            $email_subject=$value["email_subject"];
            $configure_email_table=$value["configure_email_table"];
            $page_access_token=$value["page_access_token"];
           
            $cart_id=$order_no=$value["id"];
            $store_name = $value["store_name"];
            $store_unique_id = $value["store_unique_id"];
            $store_id = $value["store_id"];
            $currency = $value["currency"];
            if(empty($currency)) $currency="USD";

            $checkout_url = base_url("ecommerce/cart/".$cart_id."?subscriber_id=".$subscriber_id);
            $order_url = base_url("ecommerce/order/".$cart_id."?subscriber_id=".$subscriber_id);
            $store_url = base_url("ecommerce/store/".$store_unique_id."?subscriber_id=".$subscriber_id);
            $my_orders_url = base_url("ecommerce/my_orders/".$store_id."?subscriber_id=".$subscriber_id);

            $replace_variables = array("store_name"=>$store_name,"store_url"=>$store_url,"order_no"=>$order_no,"order_url"=>$order_url,"checkout_url"=>$checkout_url,"my_orders_url"=>$my_orders_url,"last_name"=>$wc_last_name,"first_name"=>$wc_first_name,"email"=>$wc_email,"mobile"=>$wc_mobile);
            $email_subject = $this->ecommerce_spin_and_replace($email_subject,$replace_variables);
            
            $sms_content=json_decode($value['sms_content'],true);
            $email_content=json_decode($value['email_content'],true);
            $messenger_content=json_decode($value['messenger_content'],true);

            $reminder_content = array();
            if(isset($messenger_content['reminder']))
            foreach ($messenger_content['reminder'] as $key2 => $value2) 
            {
                $reminder_content[$value2['hour']]['messenger'] = $value2;
            }

            if(isset($sms_content['reminder']))
            foreach ($sms_content['reminder'] as $key2 => $value2) 
            {
                $reminder_content[$value2['hour']]['sms'] = $value2;
            }

            if(isset($email_content['reminder']))
            foreach ($email_content['reminder'] as $key2 => $value2) 
            {
                $reminder_content[$value2['hour']]['email'] = $value2;
            }
            ksort($reminder_content);


            // if there is no hourly sequence not sent, then update the subscribers hourly campaign as completed. 
            if(empty($reminder_content)){
                 $this->basic->update_data("ecommerce_cart",array("id"=>$cart_id),array("is_totally_completed"=>"1"));
                 continue;
            }

            $message_hours=array_keys($reminder_content); // th hour campaign will send message
            $max_send_hour=max($message_hours); // maximum campaign hour, will decide campaign totally complete or not           

            foreach ($message_hours as $key2 => $value2)
            {
               if($value2>$last_completed_hour) // getting the next hour to start sending message
               {                
                  $today=date("Y-m-d H:i:s");
                 
                  $sending_hour=$value2; // currently processing this drip time
                  $sending_minutes=$sending_hour*60;
                  $sending_date=date('Y-m-d H:i:s', strtotime($initial_date. ' + '.$sending_minutes.' minutes'));
                  $is_totally_completed='0';
                  if($max_send_hour==$sending_hour) $is_totally_completed='1';

                  // calculate after 24 hours date. If for any reason it exceeds 24 hours then complete the campaign.
                  $after_24_date=date('Y-m-d H:i:s', strtotime($initial_date. '+ 24 hours'));

                  if(strtotime($today)>strtotime($after_24_date)) // if somehow some subscriber was failed to sent message and it will never be comeplete so we are canceling it
                  {
                    $this->basic->update_data("ecommerce_cart",array("id"=>$cart_id),array("is_totally_completed"=>"1"));
                  }


                  if(strtotime($today)>=strtotime($sending_date) && strtotime($today)<strtotime($after_24_date) ) // deciding if we have to send message or not now
                  {
                    //getting message template
                    $sent_response=array();      

                    //making message to be sent  
                    $temp_reminder_text = isset($reminder_content[$sending_hour]['messenger']['reminder_text']) ? $this->ecommerce_spin_and_replace($reminder_content[$sending_hour]['messenger']['reminder_text'],$replace_variables) : "";
                    $temp_reminder_btn_details = isset($reminder_content[$sending_hour]['messenger']['reminder_btn_details']) ? $reminder_content[$sending_hour]['messenger']['reminder_btn_details'] : "VISIT DETAILS";
                    $temp_reminder_text_checkout = isset($reminder_content[$sending_hour]['messenger']['reminder_text_checkout']) ? $this->ecommerce_spin_and_replace($reminder_content[$sending_hour]['messenger']['reminder_text_checkout'],$replace_variables) : "";
                    $temp_reminder_btn_checkout = isset($reminder_content[$sending_hour]['messenger']['reminder_btn_checkout']) ? $reminder_content[$sending_hour]['messenger']['reminder_btn_checkout'] : "CHECKOUT NOW";

                    $temp_carousel_element = array();
                    $temp_i=0;
                    $cart_info =  $this->basic->get_data("ecommerce_cart_item",array("where"=>array("cart_id"=>$cart_id)),"quantity,product_name,unit_price,coupon_info,attribute_info,thumbnail,product_id,woocommerce_product_id",array('ecommerce_product'=>"ecommerce_cart_item.product_id=ecommerce_product.id,left"));    

                    foreach ($cart_info as $key3 => $value3) 
                    {
                        $temp_title =  isset($value3['product_name']) ? $value3['product_name'] : "Cart Item";
                        $temp_price =  isset($value3['unit_price']) ? $value3['unit_price'] : "0";
                        $temp_quantity =  isset($value3['quantity']) ? $value3['quantity'] : "1";
                        $temp_currency =  $currency;

                        if(!isset($value3['thumbnail']) || $value3['thumbnail']=='') $temp_thumbnail = base_url('assets/img/products/product-1.jpg');
                        else $temp_thumbnail = base_url('upload/ecommerce/'.$value3['thumbnail']);
                        if(isset($value3['woocommerce_product_id']) && !is_null($value3['woocommerce_product_id']) && $value3['thumbnail']!='')
                        $temp_thumbnail = $value3['thumbnail'];

                        // if(!isset($value3['thumbnail']) || $value3['thumbnail']=='') $temp_thumbnail = base_url('assets/img/products/product-1.jpg');
                        // else $temp_thumbnail = base_url('upload/ecommerce/'.$value3['thumbnail']);
                        
                        $temp_url = base_url("ecommerce/product/".$value3['product_id']."?subscriber_id=".$subscriber_id);
                        $temp_action = array("type"=>"web_url","url"=>$temp_url);
                        $temp_button = array(0=>array("type"=>"web_url","url"=>$temp_url,"title"=>$temp_reminder_btn_details,"messenger_extensions" => 'true',"webview_height_ratio" => 'full'));
                        $temp_button_checkout = array(0=>array("type"=>"web_url","url"=>$checkout_url,"title"=>$temp_reminder_btn_checkout,"messenger_extensions" => 'true',"webview_height_ratio" => 'full'));

                        $temp_carousel_element[$temp_i]['title'] = $temp_title;
                        $temp_carousel_element[$temp_i]['subtitle'] = "Qty : ".$temp_quantity." | "."Price : ".$temp_currency." ".$temp_price;
                        $temp_carousel_element[$temp_i]['image_url'] = $temp_thumbnail;
                        $temp_carousel_element[$temp_i]['default_action'] = $temp_action;
                        $temp_carousel_element[$temp_i]['buttons'] = $temp_button;
                        $temp_i++;
                    }                    

                    $messenger_reminder_message_send1 = json_encode(array("recipient"=>array("id"=>$subscriber_id),"message"=>array("text"=>$temp_reminder_text)));
                    $messenger_reminder_message_send2 = array 
                    (
                      "recipient" => array("id"=>$subscriber_id),                     
                      'message' => array
                      (
                        'attachment' => 
                          array 
                          (
                            'type' => 'template',
                            'payload' => 
                            array 
                            (
                              'template_type' => 'generic',
                              'elements' => $temp_carousel_element
                            ),
                          )
                      )                   
                    );
                    $messenger_reminder_message_send2 = json_encode($messenger_reminder_message_send2);

                    $messenger_reminder_message_send3 = array 
                    (
                      "recipient" => array("id"=>$subscriber_id),                     
                      'message' => array
                      (
                        'attachment' => 
                          array 
                          (
                            'type' => 'template',
                            'payload' => 
                            array 
                            (
                              'template_type' => 'button',
                              'text' => $temp_reminder_text_checkout,
                              'buttons'=>$temp_button_checkout
                            ),
                          )
                      )                   
                    );
                    $messenger_reminder_message_send3 = json_encode($messenger_reminder_message_send3);

                    $subscriber_type_explode=explode("-",$subscriber_id);

                    if(isset($subscriber_type_explode[0]) && $subscriber_type_explode[0]=='sys'){

                        $sent_response['messenger']['template1']=array("response"=> $this->lang->line("Not a Messenger subscriber, message sending was skipped."),"status"=>'1'); 
                        $sent_response['messenger']['template2'] =array("response"=> $this->lang->line("Not a Messenger subscriber, message sending was skipped."),"status"=>'1'); 
                        $sent_response['messenger']['template3']= array("response"=> $this->lang->line("Not a Messenger subscriber, message sending was skipped."),"status"=>'1'); 
                    }

                    else{

                    if(trim($temp_reminder_text)!="") $sent_response['messenger']['template1'] = $this->ecommerce_send_messenger_reminder($messenger_reminder_message_send1,$page_access_token);
                    $sent_response['messenger']['template2'] = $this->ecommerce_send_messenger_reminder($messenger_reminder_message_send2,$page_access_token);
                    $sent_response['messenger']['template3'] = $this->ecommerce_send_messenger_reminder($messenger_reminder_message_send3,$page_access_token);

                    }
                    
              
        
                    //  SMS Sending block
                    if($wc_mobile!="" && $sms_api_id!='0')
                    {
                        $temp_reminder_text_sms = isset($reminder_content[$sending_hour]['sms']['reminder_text']) ? $this->ecommerce_spin_and_replace($reminder_content[$sending_hour]['sms']['reminder_text'],$replace_variables,false) : "";
                        $temp_reminder_text_sms = str_replace(array("'",'"'),array('`','`'),$temp_reminder_text_sms);
                        
                        $this->sms_manager->set_credentioal($sms_api_id,$user_id);
                        $sms_response = array("response"=> 'missing param',"status"=>'0');

                        if(trim($temp_reminder_text_sms)!="")
                        {
                            try
                            {
                                $response = $this->sms_manager->send_sms($temp_reminder_text_sms, $wc_mobile);

                                if(isset($response['id']) && !empty($response['id']))
                                {   
                                    $message_sent_id = $response['id'];
                                    $sms_response = array("response"=> $message_sent_id,"status"=>'1');              
                                }
                                else 
                                {   if(isset($response['status']) && !empty($response['status']))
                                    {
                                        $message_sent_id = $response["status"];
                                        $sms_response = array("response"=> $message_sent_id,"status"=>'0');  
                                    }
                                }           
                                
                            }
                            catch(Exception $e) 
                            {
                               $message_sent_id = $e->getMessage();
                               $sms_response = array("response"=> $message_sent_id,"status"=>'0');
                            }
                        }

                        $sent_response['sms']=$sms_response;
                    }
                    //  SMS Sending block



                    //  Email Sending block
                    if($wc_email!="" && $email_api_id!='0')
                    {
                        $temp_reminder_text_email = isset($reminder_content[$sending_hour]['email']['reminder_text']) ? $this->ecommerce_spin_and_replace($reminder_content[$sending_hour]['email']['reminder_text'],$replace_variables,false) : "";
                        $from_email = "";

                        if ($configure_email_table == "email_smtp_config") 
                        {
                            $from_email = "smtp_".$email_api_id;

                        } 
                        elseif ($configure_email_table == "email_mandrill_config") 
                        {
                            $from_email = "mandrill_".$email_api_id;

                        } 
                        elseif ($configure_email_table == "email_sendgrid_config") 
                        {
                            $from_email = "sendgrid_".$email_api_id;
                        } 
                        elseif ($configure_email_table == "email_mailgun_config") 
                        {
                            $from_email = "mailgun_".$email_api_id;
                        }

                        $email_response = array("response"=> 'missing param',"status"=>'0');  
                        if(trim($temp_reminder_text_email)!='')
                        {
                            try
                            {
                                $response = $this->_email_send_function($from_email, $temp_reminder_text_email, $wc_email, $email_subject, $attachement='', $filename='',$user_id);
                  
                                if(isset($response) && !empty($response) && $response == "Submited")
                                {   
                                    $message_sent_id = $response;
                                    if($message_sent_id=="Submited") $message_sent_id = "Submitted";
                                    $email_response = array("response"=> $message_sent_id,"status"=>'1');  
                                }
                                else 
                                {   
                                    $message_sent_id = $response;
                                    $email_response = array("response"=> $message_sent_id,"status"=>'0');  
                                }           
                            }
                            catch(Exception $e) 
                            {
                                $message_sent_id = $e->getMessage();
                                $email_response = array("response"=> $message_sent_id,"status"=>'0');  
                            }
                        }
                        $sent_response['email']=$email_response;
                    }
                    //  Email Sending block



                    $curdate = date("Y-m-d H:i:s");
                    $insert_data=array
                    (
                        "store_id"=>$store_id,
                        "cart_id"=>$cart_id,
                        "user_id"=>$user_id,
                        "subscriber_id"=>$subscriber_id,
                        "first_name"=>$wc_first_name,
                        "last_name"=>$wc_last_name,
                        "email"=>$wc_email,
                        "last_completed_hour"=>$sending_hour,
                        "is_sent"=>'1',
                        "sent_at"=>$curdate,
                        "sent_response"=>json_encode($sent_response),
                        "last_updated_at"=>$curdate
                    );
                    $this->basic->insert_data("ecommerce_reminder_report",$insert_data); // inserting send report
 
                    $sub_update=array
                    (
                        "last_completed_hour"=>$sending_hour,
                        "is_totally_completed"=>$is_totally_completed,
                        "last_sent_at"=>$curdate,
                        "processing_status"=>"0",
                    );
                    $this->basic->update_data("ecommerce_cart",array("id"=>$cart_id),$sub_update);// updating subscriber so that it will process next drip day again

                  }
                  break;
               }
            }             
        }

        //marking subscribers this cron is operating as ok to process by another cron later  (comment this query while test)
        if(isset($subscriber_where['where']['processing_status'])) unset($subscriber_where['where']['processing_status']);
        $this->db->where($subscriber_where["where"]);
        $this->db->update("ecommerce_cart", array('processing_status' => "0"));
        $this->db->update("ecommerce_store", array('last_sent_at' => date("Y-m-d H:i:s")));
    }

    public function expired_users_disable_bot($api_key='')
    {
        // $this->api_key_check($api_key);
        $current_date = date("Y-m-d H:i:s",strtotime("-2 day"));
        $free_package_info = $this->basic->get_data('package',['where'=>['price'=>'0','validity'=>'0','is_default'=>'1']]);
        $free_package_id = isset($free_package_info[0]['id']) ? $free_package_info[0]['id'] : 0;
        $user_info = $this->basic->get_data('users',array('where'=>array('user_type !='=>'Admin','deleted'=>'0','expired_date <='=>$current_date,'bot_status'=>'1','package_id !='=>$free_package_id)),array('id'));

        foreach($user_info as $value)
        {
            $fb_page_infos = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('user_id'=>$value['id'])),array('id'));
            foreach($fb_page_infos as $value2)
            {
                $this->disable_bot_basedon_pages($value2['id']);
            }
            $this->basic->update_data('users',array('id'=>$value['id']),array('bot_status'=>'0'));
        }
    }

    private function disable_bot_basedon_pages($table_id=0)
    {
        $page_information = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$table_id)));

        if(!empty($page_information))
        {
            $fb_page_id=isset($page_information[0]["page_id"]) ? $page_information[0]["page_id"] : "";
            $page_access_token=isset($page_information[0]["page_access_token"]) ? $page_information[0]["page_access_token"] : "";
            $persistent_enabled=isset($page_information[0]["persistent_enabled"]) ? $page_information[0]["persistent_enabled"] : "0";
            $bot_enabled=isset($page_information[0]["bot_enabled"]) ? $page_information[0]["bot_enabled"] : "0";
            $started_button_enabled=isset($page_information[0]["started_button_enabled"]) ? $page_information[0]["started_button_enabled"] : "0";
            $ice_breaker_status=isset($page_information[0]["ice_breaker_status"]) ? $page_information[0]["ice_breaker_status"] : "0";
            $fb_user_id = $page_information[0]["facebook_rx_fb_user_info_id"];
            $fb_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$fb_user_id)));
            $this->load->library('Fb_rx_login');
            $this->fb_rx_login->app_initialize($fb_user_info[0]['facebook_rx_config_id']);

            if($persistent_enabled == '1') 
        	{
	            $this->fb_rx_login->delete_persistent_menu($page_access_token); // delete persistent menu
	            $this->basic->update_data('facebook_rx_fb_page_info',array('id'=>$table_id),array('persistent_enabled'=>'0'));        		
        	}
            if($started_button_enabled == '1') 
            {
	            $this->fb_rx_login->delete_get_started_button($page_access_token); // delete get started button
	            $this->basic->update_data('facebook_rx_fb_page_info',array('id'=>$table_id),array('started_button_enabled'=>'0'));            	
            }
            if($bot_enabled == '1') 
            {
            	$this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);
	            $this->basic->update_data('facebook_rx_fb_page_info',array('id'=>$table_id),array('bot_enabled'=>'2'));            	
            }
            if($ice_breaker_status == '1') 
            {
            	$this->fb_rx_login->delete_ice_breakers($page_access_token); // delete get started button
	            $this->basic->update_data('facebook_rx_fb_page_info',array('id'=>$table_id),array('ice_breaker_status'=>'0'));            	
            }
        }
    }

    // vidcasterlive video streaming (once per 5 minutes)
    public function stream_live_video($api_key="")
    {
        // $this->api_key_check($api_key);

        if($this->config->item('maximum_simultaneous_live_stream')=="") $maximum_simultaneous_live_stream=10; 
        else $maximum_simultaneous_live_stream=$this->config->item('maximum_simultaneous_live_stream');
                        
        /****if queue more than maximum_simultaneous_live_stream, then exit ****/
        
        $total_rows_array = $this->basic->count_row("vidcaster_facebook_rx_live_scheduler", array("where"=>array("posting_status"=>"1")), $count="id", $join='');
        $data["total_processing"] = $total_rows_array[0]['total_rows'];
        if($data["total_processing"]>$maximum_simultaneous_live_stream) exit; 
        
        $where['where'] = array('is_live'=>"0","schedule_type"=>"later","posting_status !="=>"1");
        $campaign_info= $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",$where,$select="",$join="",$limit=100, $start=0, $order_by='schedule_time ASC'); 
        
         
        $config_id_database = array();
        foreach($campaign_info as $info){
            
            $schedule_time= $info['schedule_time'];
            $time_zone = $info['time_zone'];
            
            date_default_timezone_set($time_zone);
            
            $server_time= date("Y-m-d H:i:s");
            $now_time_for_schedule   = date("Y-m-d H:i:s", strtotime("+10 minutes", strtotime(date("Y-m-d H:i:s"))));
            
            $campaign_id= $info['id'];
            $live_video_id=$info['post_id'];
            $stream_url=$info['stream_url'];
            $secure_stream_url=$info['secure_stream_url'];
            $file_name="upload_caster/live_video/".$info['scheduled_video_url'];
            $create_event=$info['create_event'];
            $message=$info['message'];
            
            
            
            if(strtotime($now_time_for_schedule)>=strtotime($schedule_time)){
            
            $this->basic->update_data("vidcaster_facebook_rx_live_scheduler",array("id"=>$campaign_id),array("posting_status"=>"1","stream_started"=>$server_time));
             // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
            $page_group_user_id= $info["page_group_user_id"];
            $page_or_group_or_user= $info["page_or_group_or_user"];
            
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            // $this->session->set_userdata("vidcaster_fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->load->library("fb_rx_login");
            $this->fb_rx_login->app_initialize($config_id_database[$fb_rx_fb_user_info_id]);
            // setting fb confid id for library call  
       
            
            if($page_or_group_or_user=="page")
            {
                $table_name = "facebook_rx_fb_page_info";
                $fb_id_field =  "page_id";
                $access_token_field =  "page_access_token";  
            }
            else if($page_or_group_or_user=="user")
            {
                $table_name = "facebook_rx_fb_user_info";
                $fb_id_field =  "fb_id";
                $access_token_field =  "access_token";               
            }
            else
            {
                $table_name = "facebook_rx_fb_group_info`";
                $fb_id_field =  "group_id";
                $access_token_field =  "group_access_token";

            }

            if(!isset($database[$page_or_group_or_user][$page_group_user_id])) // if not exists in database
            {
                $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));
                          
                $use_access_token = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";
                $use_fb_id = isset($access_data["0"][$fb_id_field]) ? $access_data["0"][$fb_id_field] : "";
                
                //inserting new data in database
                $database[$page_or_group_or_user][$page_group_user_id] = array("use_access_token"=>$use_access_token,"use_fb_id"=>$use_fb_id);
            }

            $use_access_token = isset($database[$page_or_group_or_user][$page_group_user_id]["use_access_token"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_access_token"] : "";
            $use_fb_id = isset($database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"] : "";


            if($create_event=="0")
            {
                $share_or_cross = $info['share_or_cross'];
                $crosspost_this_post_by_pages = json_decode($info['crosspost_this_post_by_pages'],true);
                try
                {
                    if($share_or_cross == 'crossposting' && !empty($crosspost_this_post_by_pages))
                        $response = $this->fb_rx_login->live_video_schedule_direct($message,$use_access_token,$use_fb_id,$crosspost_this_post_by_pages); 
                    else
                        $response = $this->fb_rx_login->live_video_schedule_direct($message,$use_access_token,$use_fb_id); 

                    $live_video_id = isset($response["id"]) ? $response["id"] : "";
                    $stream_url = isset($response["stream_url"]) ? $response["stream_url"] : "";
                    $secure_stream_url = isset($response["secure_stream_url"]) ? $response["secure_stream_url"] : "";
                    $temp_data=$this->fb_rx_login->get_post_permalink($live_video_id,$use_access_token);
                    $permalink = isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : ""; 

                    $this->basic->update_data("vidcaster_facebook_rx_live_scheduler",array("id"=>$campaign_id),array("stream_url"=>$stream_url,"post_id"=>$live_video_id,"post_url"=>$permalink,"secure_stream_url"=>$secure_stream_url));

                }
                catch(Exception $e) 
                {
                    $error_msg = $e->getMessage();
                    exit();
                }               


            }
            else
            {               
                try
                {
                    $this->fb_rx_login->update_live_video_schedule($live_video_id,$description="",$planned_time="",$image="",$is_live=1,$use_access_token,"");
                }
                catch(Exception $e) 
                {
                    $error_msg = $e->getMessage();
                    exit();
                }
                
            }


            $update_data =array();
            $update_data["is_live"]="1";
            $this->basic->update_data('vidcaster_facebook_rx_live_scheduler',array("id"=>$campaign_id),$update_data);        
        
            sleep(4);

            $file_name=urlencode($file_name);
            $stream_url=urlencode($stream_url);
            $secure_stream_url=urlencode($secure_stream_url);
            $video_width = $info['video_width'];
            $video_height = $info['video_height'];

            $curl_url = base_url("vidcasterlive/live_stream_ffmpeg_command?filename={$file_name}&strem_url={$stream_url}&secret=3582583258972672396532987&campaign_id={$campaign_id}&video_width={$video_width}&video_height={$video_height}");

            if($info['use_system_video'] == 'yes')
                $this->fb_rx_login->live_stream_ffmpeg_command_run_using_curl($curl_url);

            $this->basic->update_data("vidcaster_facebook_rx_live_scheduler",array("id"=>$campaign_id),array("posting_status"=>"2","stream_completed"=>date("Y-m-d H:i:s")));


            //******************* AUTO LIKE SHARE COMMENT **********************************
            //******************************************************************************
            if($create_event=="0") // otherwise autolike/share/comment done at the time of event create
            {
                $auto_share_post = $info["auto_share_post"];
                $auto_like_post = $info["auto_like_post"];
                $auto_comment = $info["auto_comment"];
                $user_id = $info["user_id"];
                $account_switching_id = $info["facebook_rx_fb_user_info_id"];
                $auto_comment_text = $info["auto_comment_text"];
                $auto_share_to_profile = $info["auto_comment_text"];
                $auto_share_this_post_by_pages = json_decode($info["auto_share_this_post_by_pages"],true);
                $get_live_video_id = $this->fb_rx_login->get_live_video_id($permalink);

                if(!is_array($auto_share_this_post_by_pages)) $auto_share_this_post_by_pages=array();

                $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id))); 

                if($auto_share_post=="1" || $auto_like_post=="1" || $auto_comment=="1" ) sleep(10);
                if($page_or_group_or_user=="page" && $auto_like_post=="1")
                {  
                   foreach ($page_info as $key2 => $value2) 
                    {
                        $like_page_accesstoken =  isset($value2["page_access_token"]) ? $value2["page_access_token"] : ""; 
                        try
                        {
                            $this->fb_rx_login->auto_like($get_live_video_id,$like_page_accesstoken);
                            sleep(rand ( 1 , 5 ));
                        }
                        catch(Exception $e) 
                        {
                          $error_msg = $e->getMessage();
                        }
                    } 
                       
                }
                if($auto_comment=="1")
                {               
                    if($page_or_group_or_user=="page")
                    {
                        $table_name = "facebook_rx_fb_page_info";
                        $access_token_field =  "page_access_token";  
                    }
                    else if($page_or_group_or_user=="user")
                    {
                        $table_name = "facebook_rx_fb_user_info";
                        $access_token_field =  "access_token";               
                    }
                    else
                    {
                        $table_name = "facebook_rx_fb_group_info`";
                        $access_token_field =  "group_access_token";

                    }

                    $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));
                    $comment_page_accesstoken = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";

                   try
                   {
                        $this->fb_rx_login->auto_comment($auto_comment_text,$get_live_video_id,$comment_page_accesstoken);   
                   }
                   catch(Exception $e) 
                    {
                      $error_msg = $e->getMessage();
                    }
                }
                
                if(($auto_share_post=="1" || $auto_share_to_profile!="0"))
                {                
                    if($permalink!='')
                    {
                        if($auto_share_post=="1")
                        {
                           foreach ($page_info as $key => $value) 
                           {
                                if(!in_array($value["id"],$auto_share_this_post_by_pages)) continue;
                                if($page_or_group_or_user="page" && ($page_group_user_id==$value["id"])) continue;
                                $share_page_id =  isset($value["page_id"]) ? $value["page_id"] : ""; 
                                $share_page_accesstoken =  isset($value["page_access_token"]) ? $value["page_access_token"] : "";
                                try
                                {
                                    $this->fb_rx_login->feed_post("",$permalink,"","","","",$share_page_accesstoken,$share_page_id);
                                    sleep(rand ( 1 , 5 ));
                                }
                                catch(Exception $e) 
                                {
                                  $error_msg = $e->getMessage();
                                }
                            } 
                        }

                        if($auto_share_to_profile!="0")
                        {                        
                            $profile_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=> $account_switching_id,"user_id"=>$user_id)));  
                            $user_access_token =  isset($profile_info[0]["access_token"]) ? $profile_info[0]["access_token"] : ""; 
                            $user_fb_id =  isset($profile_info[0]["fb_id"]) ? $profile_info[0]["fb_id"] : ""; 
                            try
                            {
                                $this->fb_rx_login->feed_post("",$permalink,"","","","",$user_access_token,$user_fb_id);
                                sleep(rand ( 1 , 5 ));
                            }
                            catch(Exception $e) 
                            {
                              $error_msg = $e->getMessage();
                            }
                        }  

                    }
                }
            }
            //******************* AUTO LIKE SHARE COMMENT **********************************
            //******************************************************************************  
                    
            break;
            }
                
        }
        
    }

    //once per hour
    public function facebook_live_streaming_expired($api_key="")
    {    
        // $this->api_key_check($api_key);

        if($this->config->item('maximum_length_of_live_stream')=="") $maximum_length_of_live_stream=1; 
        else $maximum_length_of_live_stream=$this->config->item('maximum_length_of_live_stream');
        
        date("Y-m-d H:i:s");
        $str="-".$maximum_length_of_live_stream." hours";
        $last_date = date("Y-m-d H:i:s",strtotime($str));           
        
        $where['where']=array("posting_status"=>"1","stream_started <="=>$last_date);
        $post_info= $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",$where);
        
        foreach($post_info as $info)
        {                   
            $this->basic->update_data("vidcaster_facebook_rx_live_scheduler",array("id"=>$info["id"]),array("posting_status"=>"2","is_live"=>"1"));
        }
        
    }

    // every day
    public function delete_vidcasterlive_junk_data($api_key='')
    {
        // $this->api_key_check($api_key); 
        $curdate=date("Y-m-d H:i:s");
        $one_month_before_date = date("Y-m-d", strtotime("$curdate -31 days"));

        $campaign_info=$this->basic->get_data("vidcaster_facebook_rx_live_scheduler",array("where"=>array("posting_status"=>"2","is_live"=>"1")));
        foreach ($campaign_info as $key => $value) 
        {
           $user_id=isset($value["user_id"]) ? $value["user_id"] : 0;
           $video_file_link=isset($value["scheduled_video_url"]) ? $value["scheduled_video_url"] : "";
           $video_file_thumb_link=$video_file_link.".jpg";
     
           $video_file_link = 'upload_caster/live_video/'.$video_file_link;
           $video_file_thumb_link = 'upload_caster/live_video/thumb/'.$user_id.'/'.$video_file_thumb_link;

           @unlink($video_file_link);
           @unlink($video_file_thumb_link);       
        }
    }

    /* XeroBiz cron methods */

    public function post_to_gmb($api_key="")
    {
        
        $link=base_url().'cron_job/xerobiz_all_type_post/'.$api_key;
        $this->call_curl_internal_cronjob($link);

        $link=base_url().'cron_job/xerobiz_media_upload_to_gmb/'.$api_key;
        $this->call_curl_internal_cronjob($link);

        $link=base_url().'cron_job/xerobiz_rss_auto_post/'.$api_key;
        $this->call_curl_internal_cronjob($link);
    }

    public function xerobiz_all_type_post($api_key='')
    {
        // $this->api_key_check($api_key);

        $where = [
            'where' => [
                'google_posts_campaign.status' => '0',
                'google_posts_campaign.deleted' => '0',
            ],
        ];

        $campaigns = $this->basic->get_data('google_posts_campaign', $where, [], [], 15, '', 'id desc');

        if (! count($campaigns)) {
            echo 'No campaigns found to be posted in GMB';
            return;
        }

        $ids = [];
        foreach ($campaigns as $key => $campaign) {
            // Prepares post IDs
            $ids[] = $campaign['id'];

            if ($campaign['schedule_time']) {
                if ($campaign['time_zone']) {
                    $time_zone = $campaign['time_zone'];
                } else {
                    if (date_default_timezone_get()) {
                        $time_zone = date_default_timezone_get();
                    }
                }

                // Timezone
                $set_time_zone = new DateTimeZone($time_zone);

                // Scheduled time
                $scheduled_time = new DateTime($campaign['schedule_time'], $set_time_zone);

                // Current date and time
                $now = new DateTime('now', $set_time_zone);
                $now->add(new DateInterval('PT10M'));

                // If scheduled time is not less than current
                // time + 10 minutes then just skip this post
                if (! ($scheduled_time <= $now)) {
                    unset($campaigns[$key]);

                    // Unsets scheduled id that is not being sent
                    array_splice($ids, array_search($campaign['id'], $ids), 1);
                }
            }
        }

        if (! count($ids)) {
            echo 'No campaigns found to be posted in GMB';
            return;
        }

        // Update state of posts
        $this->db->set('status', '1');
        $this->db->where_in('id', $ids);
        $this->db->update('google_posts_campaign');

        foreach ($campaigns as $post) {
            $user_id = $post['user_id'];
            if(!$this->api_member_validity($user_id)) continue;
            $params['gmb_user_table_id'] = $post['user_account_id'];
            $params['redirectUri'] = '';

            $this->load->library('google_my_business',$params);
            $this->google_my_business->initializeGclient($params);

            $gmb = $this->google_my_business;

            $location_table_ids = (array) json_decode($post['location_table_id'], true);
            $location_names = (array) json_decode($post['location_names'], true);

            foreach ($location_table_ids as $id) {
                $where = [
                    'where' => [
                        'id' => $id,
                        'user_account_id' => $post['user_account_id'],
                    ]
                ];

                $location = $this->basic->get_data(
                    'google_business_locations',
                    $where,
                    [
                        'location_id',
                        'location_display_name'
                    ],
                    [],
                    1
                );

                if (1 != count($location)) {
                    continue;
                }

                $location_id = isset($location[0]['location_id'])
                    ? trim($location[0]['location_id'])
                    : null;

                $location_name = isset($location[0]['location_display_name'])
                    ? trim($location[0]['location_display_name'])
                    : null;    

                if (null == $location_id || null == $location_name) {
                    continue;
                }

                if ('cta_post' == $post['post_type']) {
                    static $cta_results = [];
                    static $cta_errors = [];

                    // @TODO - Upload multiple images
                    $media = null;
                    if ($post['media_type'] && $post['media_url']) {
                        $media = [
                            [
                                'mediaFormat' => $post['media_type'],
                                'sourceUrl' => $post['media_url'],
                            ],
                        ];

                    }

                    $action_url = ('call' == strtolower($post['cta_action_type']))
                        ? null
                        : $post['cta_action_url'];
                    $post_summary = spintax_process($post['summary']);

                    $response = '';
                    try {
                        $response = $gmb->callToActionPost(
                            $location_id,
                            $post['cta_action_type'],
                            $post_summary,
                            $media,
                            $action_url
                        );
                    } catch (Google_Service_Exception $e) {
                        $cta_error = $e->getMessage();
                    } catch (Google_Exception $e) {
                        $cta_error = $e->getMessage();
                    } catch (\Exception $e) {
                        $cta_error = $e->getMessage();
                    }

                    if ($response) {
                        $response->location_name = $location_name;
                        $cta_results[] = $response;
                    } elseif ($cta_error) {
                        $result['location_name'] = $location_name;
                        $result['error_message'] = $cta_error;
                        $cta_results[] = $result;
                        $cta_errors[] = $cta_error;

                        if ($location_name && is_array($location_names)) {
                            $key = array_search($location_name, array_column($location_names, 'name'));
                            if ($key) {
                                if (isset($location_names[$key]['status'])) {
                                    $location_names[$key]['status'] = false;
                                }
                            }
                        }
                    }

                    $this->basic->update_data(
                        'google_posts_campaign',
                        ['id' => $post['id']],
                        [
                            'status' => '2',
                            'location_names' => json_encode($location_names),
                            'response' => count($cta_results) ? json_encode($cta_results) : null,
                            'error' => count($cta_errors) ? json_encode($cta_errors) : null,
                        ]
                    );
                } elseif ('event_post' == $post['post_type']) {
                    static $event_results = [];
                    static $event_errors = [];

                    $start_date_time = new DateTime($post['start_date_time']);
                    $end_date_time = new DateTime($post['end_date_time']);
                    $media = [
                        [
                            'mediaFormat' => $post['media_type'],
                            'sourceUrl' => $post['media_url']
                        ]
                    ];
                    $post_summary = spintax_process($post['summary']);

                    $response = '';
                    try {
                        $response = $gmb->eventPost(
                            $location_id,
                            $post['event_post_title'],
                            $post_summary,
                            $start_date_time,
                            $end_date_time,
                            $media
                        );
                    } catch (Google_Service_Exception $e) {
                        $event_error = $e->getMessage();
                    } catch (Google_Exception $e) {
                        $event_error = $e->getMessage();
                    } catch (\Exception $e) {
                        $event_error = $e->getMessage();
                    }

                    if ($response) {
                        $response->location_name = $location_name;
                        $event_results[] = $response;

                    } elseif ($event_error) {
                        $result['location_name'] = $location_name;
                        $result['error_message'] = $event_error;
                        $event_results[] = $result;
                        $event_errors[] = $event_error;

                        if ($location_name && is_array($location_names)) {
                            $key = array_search($location_name, array_column($location_names, 'name'));
                            if ($key) {
                                if (isset($location_names[$key]['status'])) {
                                    $location_names[$key]['status'] = false;
                                }
                            }
                        }
                    }

                    $this->basic->update_data(
                        'google_posts_campaign',
                        ['id' => $post['id']],
                        [
                            'status' => '2',
                            'location_names' => json_encode($location_names),
                            'response' => count($event_results) ? json_encode($event_results) : null,
                            'error' => count($event_errors) ? json_encode($event_errors) : null,
                        ]
                    );
                } elseif ('offer_post' == $post['post_type']) {
                    static $offer_results = [];
                    static $offer_errors = [];

                    $media = [
                        [
                            'mediaFormat' => $post['media_type'],
                            'sourceUrl' => $post['media_url']
                        ]
                    ];
                    $post_summary = spintax_process($post['summary']);

                    $response = '';
                    try {
                        $response = $gmb->offerPost(
                            $location_id,
                            $post['offer_coupon_code'],
                            $post['offer_redeem_url'],
                            $post_summary,
                            $media
                        );

                    } catch (Google_Service_Exception $e) {
                        $offer_error = $e->getMessage();
                    } catch (Google_Exception $e) {
                        $offer_error = $e->getMessage();
                    } catch (\Exception $e) {
                        $offer_error = $e->getMessage();
                    }

                    if ($response) {
                        $response->location_name = $location_name;
                        $offer_results[] = $response;
                    } elseif ($offer_error) {
                        $result['location_name'] = $location_name;
                        $result['error_message'] = $offer_error;
                        $offer_results[] = $result;
                        $offer_errors[] = $offer_error;

                        if ($location_name && is_array($location_names)) {
                            $key = array_search($location_name, array_column($location_names, 'name'));
                            if ($key) {
                                if (isset($location_names[$key]['status'])) {
                                    $location_names[$key]['status'] = false;
                                }
                            }
                        }
                    }

                    $this->basic->update_data(
                        'google_posts_campaign',
                        ['id' => $post['id']],
                        [
                            'status' => '2',
                            'location_names' => json_encode($location_names),
                            'response' => count($offer_results) ? json_encode($offer_results) : null,
                            'error' => count($offer_errors) ? json_encode($offer_errors) : null,
                        ]
                    );
                }
            }

            // Unsets all arrays
            $cta_results = [];
            $cta_errors = [];
            $event_results = [];
            $event_errors = [];
            $offer_results = [];
            $offer_errors = [];
        }
    }

    public function xerobiz_reply_to_review($api_key='')
    {
        // $this->api_key_check($api_key);

        $where = [
            'where' => [
                'google_review_reply_settings.status' => '0',
                'google_business_locations.deleted' => '0'
            ],
        ];

        $select = [
            'google_review_reply_settings.*',
            'google_review_reply_settings.location_id as location_table_id',
            'google_business_locations.user_account_id',
            'google_business_locations.location_id as location_name',
            'google_business_locations.last_review_reply_id',
            'google_business_locations.location_display_name',
        ];

        $join = [
            'google_business_locations' => 'google_review_reply_settings.location_id=google_business_locations.id,left',
            'google_user_account' => 'google_business_locations.user_account_id=google_user_account.id,left',
        ];

        $locations = $this->basic->get_data('google_review_reply_settings', $where, $select, $join, 10, '', 'google_review_reply_settings.last_reply_time asc');


        $settings_ids = [];
        foreach ($locations as $location) {
            $settings_ids[] = $location['id'];
        }

        if (count($settings_ids)) {
            $this->db->where_in('id', $settings_ids);
            $this->db->update('google_review_reply_settings', ['status' => '1']);
        }

        // Loops through locations
        foreach ($locations as $location) {

            $user_id = $location['user_id'];
            if(!$this->api_member_validity($user_id)) continue;
            // Prepares GMB
            $params['gmb_user_table_id'] = $location['user_account_id'];
            $params['redirectUri'] = '';

            $this->load->library('google_my_business',$params);
            $this->google_my_business->initializeGclient($params);
            $gmb = $this->google_my_business;

            // Tries to get reviews list
            $error = '';
            $response = '';

            try {
                $response = $gmb->reviewsList($location['location_name']);
            } catch (Google_Service_Exception $e) {
                $error = $e->getMessage();
            } catch (Google_Exception $e) {
                $error = $e->getMessage();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            if (! empty($error)) {
                $error = '';
                continue;
            }

            // Gets reviews IDs
            $unreplied_reviews = [];
            if (is_object($response)) {
                if (isset($response['reviews'])) {
                    foreach ($response['reviews'] as $review) {
                        if (is_object($review)) {
                            $unreplied_reviews[] = [
                                'name' => $review->getName(),
                                'star' => $review->getStarRating(),
                                'comment' => $review->getComment(),
                                'reviewer_photo' => is_object($review->getReviewer())
                                    ? $review->getReviewer()->getProfilePhotoUrl()
                                    : null,
                                'reviewer_display_name' => is_object($review->getReviewer())
                                    ? $review->getReviewer()->getDisplayName()
                                    : null,
                                'review_create_time' => $review->getCreateTime(),
                                'isReviewReplied' => is_object($review->getReviewReply()) 
                                    ? 'yes'
                                    : 'no',
                            ];
                        }
                    }
                }
            }

            // Deprecated algorithm for a reply to a review
            // Finds index of the matched item
            // $key = array_search($location['last_review_reply_id'], array_column($unreplied_reviews, 'name'));

            // Modifies $unreplied_reviews array - removes review ids that are already answered
            // array_splice($unreplied_reviews, $key);

            $unreplied_reviews = array_filter($unreplied_reviews, function($val) {
                return ('no' == $val['isReviewReplied']);
            });

            if (count($unreplied_reviews)) {

                // Reverses the order based on key
                krsort($unreplied_reviews);

                // Loops through each review and replies to the review
                foreach ($unreplied_reviews as $review) {
                    // Updates last review ID
                    $this->basic->update_data('google_business_locations',
                        [
                            'id' => $location['location_table_id']
                        ],
                        [
                            'last_review_reply_id' => $review['name'],
                        ]
                    );

                    switch ($review['star']) {
                        case 'FIVE':
                            if ($location['five_star']) {
                                $data = (array) json_decode($location['five_star']);
                                $comment = $this->xeroBizzGetReviewComment($data, $review['comment']);
                                $this->xeroBizzReplyReview($gmb, $location, $review, $comment);
                            }
                            break;

                        case 'FOUR':
                            if ($location['four_star']) {
                                $data = (array) json_decode($location['four_star']);
                                $comment = $this->xeroBizzGetReviewComment($data, $review['comment']);
                                $this->xeroBizzReplyReview($gmb, $location, $review, $comment);
                            }
                            break;

                        case 'THREE':
                            if ($location['three_star']) {
                                $data = (array) json_decode($location['three_star']);
                                $comment = $this->xeroBizzGetReviewComment($data, $review['comment']);
                                $this->xeroBizzReplyReview($gmb, $location, $review, $comment);
                            }
                            break;

                        case 'TWO':
                            if ($location['two_star']) {
                                $data = (array) json_decode($location['two_star']);
                                $comment = $this->xeroBizzGetReviewComment($data, $review['comment']);
                                $this->xeroBizzReplyReview($gmb, $location, $review, $comment);
                            }
                            break;

                        case 'ONE':
                            if ($location['one_star']) {
                                $data = (array) json_decode($location['one_star']);
                                $comment = $this->xeroBizzGetReviewComment($data, $review['comment']);
                                $this->xeroBizzReplyReview($gmb, $location, $review, $comment);
                            }
                            break;
                    }
                }
            }

            $this->basic->update_data('google_review_reply_settings',
                [
                    'id' => $location['id']
                ],
                [
                    'status' => '0',
                    'last_reply_time' => date('Y-m-d H:i:s')
                ]
            );

        }

        // If the method above cant reset statuses, then falls back to this
        $this->db->update('google_review_reply_settings', ['status' => '0']);    
    }

    public function xerobiz_media_upload_to_gmb($api_key='')
    {
        // $this->api_key_check($api_key);

        $where = [
            'where' => [
                'google_media_campaign.status' => '0',
                'google_media_campaign.deleted' => '0',
            ],
        ];

        $campaigns = $this->basic->get_data('google_media_campaign', $where, [], [], 15, '', 'id desc');

        if (! count($campaigns)) {
            echo 'No media found to be uploaded in GMB';
            return;
        }

        $ids = [];
        foreach ($campaigns as $key => $campaign) {
            // Prepares post IDs
            $ids[] = $campaign['id'];

            if ($campaign['schedule_time']) {
                if ($campaign['time_zone']) {
                    $time_zone = $campaign['time_zone'];
                } else {
                    if (date_default_timezone_get()) {
                        $time_zone = date_default_timezone_get();
                    }
                }

                // Timezone
                $set_time_zone = new DateTimeZone($time_zone);

                // Scheduled time
                $scheduled_time = new DateTime($campaign['schedule_time'], $set_time_zone);

                // Current date and time
                $now = new DateTime('now', $set_time_zone);
                $now->add(new DateInterval('PT10M'));

                // If scheduled time is not less than current
                // time + 10 minutes then just skip this post
                if (! ($scheduled_time <= $now)) {
                    unset($campaigns[$key]);

                    // Unsets scheduled id that is not being sent
                    array_splice($ids, array_search($campaign['id'], $ids), 1);
                }
            }
        }

        // Update state of posts
        $this->db->set('status', '1');
        $this->db->where_in('id', $ids);
        $this->db->update('google_media_campaign');

        foreach ($campaigns as $post) {
            $user_id = $post['user_id'];
            if(!$this->api_member_validity($user_id)) continue;
            $params['gmb_user_table_id'] = $post['user_account_id'];
            $params['redirectUri'] = '';

            $this->load->library('google_my_business',$params);
            $this->google_my_business->initializeGclient($params);
            $gmb = $this->google_my_business;

            $location_table_ids = (array) json_decode($post['location_table_id'], true);
            $location_names = (array) json_decode($post['location_names'], true);

            foreach ($location_table_ids as $id) {
                $where = [
                    'where' => [
                        'id' => $id,
                        'user_account_id' => $post['user_account_id'],
                    ]
                ];

                $location = $this->basic->get_data(
                    'google_business_locations',
                    $where,
                    [
                        'location_id',
                        'location_display_name'
                    ],
                    [],
                    1
                );

                if (1 != count($location)) {
                    continue;
                }

                $location_id = isset($location[0]['location_id'])
                    ? trim($location[0]['location_id'])
                    : null;

                if (null == $location_id) {
                    continue;
                }

                $location_name = isset($location[0]['location_display_name'])
                    ? trim($location[0]['location_display_name'])
                    : null;

                // Declares some vars
                $response = '';
                static $results = [];
                static $errors = [];

                try {
                    $media_description = spintax_process($post['media_description']);
                    $response = $gmb->mediaUpload(
                        $location_id,
                        $post['media_type'],
                        $post['media_category'],
                        $media_description,
                        $post['media_url']
                    );
                } catch (Google_Service_Exception $e) {
                    $error = $e->getMessage();
                } catch (Google_Exception $e) {
                    $error = $e->getMessage();
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }

                if ($response) {
                    $response->location_name = $location_name;
                    $results[] = $response;
                } elseif ($error) {
                    $result['location_name'] = $location_name;
                    $result['error_message'] = $error;
                    $results[] = $result;
                    $errors[] = $error;

                    if ($location_name && is_array($location_names)) {
                        $key = array_search($location_name, array_column($location_names, 'name'));
                        if ($key) {
                            if (isset($location_names[$key]['status'])) {
                                $location_names[$key]['status'] = false;
                            }
                        }
                    }
                }

                $this->basic->update_data(
                    'google_media_campaign',
                    ['id' => $post['id']],
                    [
                        'status' => '2',
                        'location_names' => json_encode($location_names),
                        'response' => count($results) ? json_encode($results) : null,
                        'error' => count($errors) ? json_encode($errors) : null,
                    ]
                );
            }

            // Unsets all arrays
            $results = [];
            $errors = [];
        }
    }

    public function xerobiz_rss_auto_post($api_key="")
    {
        // $this->api_key_check($api_key);
        $this->load->library("rss_feed");

        $cron_limit = 15;

        $feed_data = $this->basic->get_data("google_rss_feed_posting",array("where"=>array("cron_status"=>"0","status"=>"1")),'','',$cron_limit,NULL,'last_updated_at ASC');

        $all_feed_id = array();
        foreach ($feed_data as $key => $value) {   
            $user_id = isset($value['user_id']) ? $value['user_id']: '';

            // cancelinng inactive users feeds so that they does not start again
            if(!$this->api_member_validity($user_id)) {
                $this->basic->update_data("google_rss_feed_posting",array("id"=>$value['id']),array("status"=>"2","last_updated_at"=>date("Y-m-d H:i:s")));
                continue;
            }

            $all_feed_id[] = $value['id'];
        }

        if(empty($all_feed_id)) {
            echo 'No feed found to be inserted in post campaign table';
            return;
        }

        $this->db->where_in("id", $all_feed_id);
        $this->db->update("google_rss_feed_posting", array("cron_status"=>"1","last_updated_at"=>date("Y-m-d H:i:s")));

        $datetime = date("Y-m-d H:i:s");
        
        foreach ($feed_data as $key => $value) {         
            $user_id = isset($value['user_id']) ? $value['user_id'] : '';

            // skipping inactive users feeds
            if(!$this->api_member_validity($user_id)) continue;

            $feed_name = isset($value['feed_name']) ? $value['feed_name'] : '';
            $last_pub_date = isset($value['last_pub_date']) ? $value['last_pub_date'] : '';
            $error_log = isset($value['error_message']) ? json_decode($value['error_message'],true) : array();

            $posting_message = isset($value['posting_message']) ? $value['posting_message'] : "";
            $posting_start_time = isset($value['posting_start_time']) ? $value['posting_start_time'] : "00:00";
            $posting_end_time = isset($value['posting_end_time']) ? $value['posting_end_time'] : "23:59";
            $posting_timezone = isset($value['posting_timezone']) ? $value['posting_timezone'] : "";

            if($posting_timezone == "") {
                $posting_timezone = $this->config->item("time_zone");
            }

            $feed_url= isset($value['feed_url']) ? $value['feed_url'] : '';
            $feed = $this->rss_feed->getFeed($feed_url);

            // stop if get error while getting feed
            if(! isset($feed['success']) || $feed['success'] != '1') {
                $error_message = isset($feed['error_message']) 
                    ? $feed['error_message']
                    : $this->lang->line("Something went wrong while fetching feed data.");

                $error_message .= " [RSS]";
                $error_row = array("time" => $datetime, "message" => $error_message);
                array_push($error_log, $error_row);

                $this->basic->update_data("google_rss_feed_posting",array("id"=>$value['id']),array("cron_status"=>"0","last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));

                continue;
            }

            // operating in GMT
            date_default_timezone_set('Europe/Dublin');

            $element_list = isset($feed['element_list']) ? $feed['element_list'] : array();
            $element_list = array_reverse($element_list);

            $valid_feed = 0;
            $new_last_pub_title = "";
            $new_last_pub_url = "";
            $new_last_pub_date = "";

            // how many latest feed there will be
            foreach($element_list as $key2 => $value2) {
                $pub_date = isset($value2['pubDate']) ? $value2['pubDate'] : "";
                $pub_date = date("Y-m-d H:i:s", strtotime($pub_date));

                if(strtotime($pub_date) > strtotime($last_pub_date)) {
                    $valid_feed++;
                } 

                if($value2['pubDate']!="")
                if($new_last_pub_date=="" || (strtotime($value2['pubDate'])>strtotime($new_last_pub_date))) {
                    $new_last_pub_date = isset($value2['pubDate']) ? $value2['pubDate'] : "";
                    $new_last_pub_date = date("Y-m-d H:i:s", strtotime($new_last_pub_date));
                    $new_last_pub_title = isset($value2['title']) ? $value2['title'] : "";
                    $new_last_pub_url = isset($value2['link']) ? $value2['link'] : "";
                }
            }

            // stop cron if no latest feed found
            if($valid_feed==0) {
                $time_zone = $this->config->item('time_zone');

                if($time_zone== '') {
                    $time_zone="Europe/Dublin";
                }

                date_default_timezone_set($time_zone);
                
                $this->db->where_in("id",$all_feed_id);
                $this->db->update("google_rss_feed_posting",array("cron_status"=>"0","last_updated_at"=>date("Y-m-d H:i:s")));
                continue;
            }

            // posting time calculation
            date_default_timezone_set($posting_timezone);

            $current_datetime = date("Y-m-d H:i:s");
            $current_date = date("Y-m-d");
            $current_time = date("H:i");

            $temp0 = (float) str_replace(':','.',$current_time);
            $temp1 = (float) str_replace(':','.',$posting_start_time);
            $temp2 = (float) str_replace(':','.',$posting_end_time);

            $temp_difference = $temp2 - $temp1;
            $temp_hour_min = ceil($temp_difference) * 60;
            $temp_min = $temp_difference - ceil($temp_difference);
            $temp_min = number_format((float)$temp_min, 2, '.', '');

            // say we have 120 min time span and have 10 valid feed, 
            // then campaigns will be scheduled every 12 minutes
            $available_min = $temp_hour_min + $temp_min;
            $gap_minute = round($available_min / $valid_feed); 

            $post_schedule_time = "";

            // matches time slot
            if($temp0 >= $temp1 && $temp0 <= $temp2) {
                $post_schedule_time = strtotime($current_datetime . ' + 2 minute');
                $post_schedule_time = date('Y-m-d H:i:s', $post_schedule_time);
            } else {
                $make_date = $current_date . " " . $posting_start_time . ":00";

                // if start time is less than current time then we will schedule it next day
                if(strtotime($make_date) < strtotime($current_datetime)) {
                    $post_schedule_time = strtotime($make_date.' + 1 day');
                    $post_schedule_time = date('Y-m-d H:i:s', $post_schedule_time);
                } else {
                    $post_schedule_time = $make_date;
                }
            }

            // posting time calculation
            $post_gap_minute = 0;

            foreach($element_list as $key2 => $value2) {

                // operating in GMT
                date_default_timezone_set('Europe/Dublin'); 

                $pub_date = isset($value2['pubDate']) ? $value2['pubDate'] : "";
                $pub_date = date("Y-m-d H:i:s",strtotime($pub_date));                

                // only work with recent feed
                if(strtotime($pub_date) > strtotime($last_pub_date)) {

                    if($valid_feed > 3) {
                        $post_gap_minute += $gap_minute;
                    } else {
                        $post_gap_minute += 15; 
                    }

                    $post_feed_url = isset($value2['link']) ? $value2['link'] : "";   
                    $post_feed_title = isset($value2['title']) ? $value2['title'] : "";  
                    $temp_posting_message = str_replace('#TITLE#', $post_feed_title, $posting_message);

                    $media_type = 'PHOTO';
                    $feed_img_url = $this->xerobizz_get_img_from_metatag($post_feed_url);
                    if (null != $feed_img_url) {
                        $allowed_extensions = ['.jpg', '.jpeg', '.png', '.gif'];
                        $extension = mb_substr($feed_img_url, mb_strrpos($feed_img_url, '.'));

                        if (! in_array($extension, $allowed_extensions)) {
                            $media_url = $value['default_media_url'];
                        } else {
                            $media_url = $feed_img_url;
                        }

                    } else {
                        $media_url = $value['default_media_url'];
                    }


                    // processing facebook post
                    $location_ids = isset($value['location_ids']) ? $value['location_ids'] : '';
                    $location_names = isset($value['location_names']) ? $value['location_names'] : '';
                    $user_account_id = isset($value['user_account_id']) ? $value['user_account_id'] : null;

                    $locations_array = (is_array(json_decode($location_ids, true)) && null != json_decode($location_ids, true))
                        ? json_decode($location_ids, true)
                        : [];
                    $location_count = count($locations_array);

                    if($location_count > 0) {

                        $status = $this->_check_usage($module_id=305, $location_count, $user_id);

                        if($status == "3") {
                        
                            $this->basic->update_data("google_rss_feed_posting",array("id"=>$value['id']),array("error_message"=>$error_message));  

                            $error_message = $this->lang->line("Your monthly limit for RSS fedd posting module has been exceeded.");  
                            $error_message .= " [RSS Feed Posting]";
                            $error_row = array("time"=>$datetime,"message"=>$error_message);

                            array_push($error_log, $error_row);
                            
                            $this->basic->update_data("google_rss_feed_posting",array("id"=>$value['id']),array("last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));

                        } else {

                            $post_schedule_time_gapped = $post_schedule_time;

                            // if there is a small amount of feeds then we will try to post in first hour
                            if($valid_feed <= 3) {
                               $post_schedule_time_gapped = strtotime($post_schedule_time .' + ' . $post_gap_minute . ' minute');
                               $post_schedule_time_gapped = date('Y-m-d H:i:s', $post_schedule_time_gapped);

                            // if there is a large amount of feeds then we will try to span the feed post process to cover whole timeslot
                            } else {
                                if($post_gap_minute > 0) {
                                    $post_schedule_time_gapped = strtotime($post_schedule_time . ' + ' . $post_gap_minute . ' minute');
                                    $post_schedule_time_gapped = date('Y-m-d H:i:s', $post_schedule_time_gapped);
                                }
                            }
                            
                            $create_campaign_data = array(
                                "user_id" => $user_id,
                                "user_account_id" => $user_account_id,
                                "location_table_id" => $location_ids,
                                "location_names" => $location_names,
                                "campaign_name" => $post_feed_title,
                                "cta_action_type" => 'LEARN_MORE',
                                "cta_action_url" => $post_feed_url,
                                "summary" => $temp_posting_message,
                                "media_type" => $media_type,
                                "media_url" => $media_url,
                                "schedule_type" => null,
                                "schedule_time" => $post_schedule_time_gapped,
                                "time_zone" => $posting_timezone,
                                "updated_at" => $datetime,
                                "created_at" => $datetime,
                                "status" => "0",
                            );                             

                            $this->basic->insert_data("google_posts_campaign",$create_campaign_data);
                            $this->_insert_usage_log($module_id=305,$request=1,$user_id);
                        }
                    }
                }
            }

            $this->basic->update_data(
                "google_rss_feed_posting",
                array(
                    "id" => $value['id']
                ),
                array(
                    "last_pub_date"=>$new_last_pub_date,
                    "last_pub_title"=>$new_last_pub_title,
                    "last_pub_url"=>$new_last_pub_url
                )
            );
        } 

        $time_zone = $this->config->item('time_zone');
        if($time_zone == '') $time_zone="Europe/Dublin";
        
        // Redo timezone
        date_default_timezone_set($time_zone);

        $this->db->where_in("id", $all_feed_id);
        $this->db->update("google_rss_feed_posting",array("cron_status"=>"0","last_updated_at"=>date("Y-m-d H:i:s")));
    }    

    /**
     * Returns appropriate comment
     *
     * @param array $data
     * @param string $comment
     * @return string
     */
    private function xeroBizzGetReviewComment($data, $comment)
    {
        $reply_type = $data['reply_type'];

        if ('keyword' == $reply_type) {
            if (isset($data['keyword_settings'])
                && is_array($data['keyword_settings'])
            ) {
                foreach($data['keyword_settings'] as $key => $keyword) {
                    if (false !== stripos($comment, $keyword)) {
                        $comment = isset($data['reply_settings'][$key])
                            ? $data['reply_settings'][$key]
                            : $data['not_found_reply_settings'];

                        break;
                    } else {
                        $comment = $data['not_found_reply_settings'];
                    }
                }
            } else {
                $comment = $data['not_found_reply_settings'];
            }
        } elseif ('generic' == $reply_type) {
            $comment = $data['generic_message'];
        }

        return spintax_process($comment);
    }

    /**
     * Replies to a review
     *
     * @param Google_my_business $gmb
     * @param array $location
     * @param array $review
     * @param string $comment
     * @return void
     */
    private function xeroBizzReplyReview($gmb, array $location, array $review, string $comment)
    {
        $error = '';

        try {
            $gmb->replyReview($review['name'], $comment);
        } catch (Google_Service_Exception $e) {
            $error = $e->getMessage();
        } catch (Google_Exception $e) {
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $review_reply = empty($error) ? $comment : '';
        $error = ! empty($error) ? $error : null;
        $reply_time = date('Y-m-d H:i:s');

        $location_display_name = $this->db->escape($location['location_display_name']);
        $review_id = $this->db->escape($review['name']);
        $reviewer_name = $this->db->escape($review['reviewer_display_name']);
        $review_comment = $this->db->escape($review['comment']);
        $review_reply = $this->db->escape($review_reply);        

        $sql = "INSERT INTO `google_review_reply_report` 
                (user_id, location_id, location_display_name, review_id, reviewer_photo, reviewer_name, review_star, review_comment, review_reply, review_create_time, error, reply_time)
                VALUES ('{$location['user_id']}', '{$location['location_table_id']}', $location_display_name, $review_id, '{$review['reviewer_photo']}', $reviewer_name, '{$review['star']}', $review_comment, $review_reply, '{$review['review_create_time']}', '{$error}', '{$reply_time}')
                ON DUPLICATE KEY UPDATE review_reply = $review_reply, error = '{$error}', reply_time = '{$reply_time}'";

        $this->basic->execute_complex_query($sql);        
        
    }

    /**
     * Grabs an image url from meta tags
     *
     * @param string $url
     * @return string|null
     */
    private function xerobizz_get_img_from_metatag($url) 
    {
        $data = file_get_contents($url);
        preg_match('@(?:["|\']og:image["|\']\s*content=["|\']([\S]+)["|\'])|(?:["|\']twitter:image["|\']\s*content=["|\']([\S]+)["|\'])|(?:["|\']twitter:image:src["|\']\s*content=["|\']([\S]+)["|\'])@i', $data, $matches);

        return isset($matches[1]) ? $matches[1] : null;
    } 


    public function extract_subscribers_label() //background_scanning_update_subscriber_info
    {
        $labelinfo = $this->basic->get_data('messenger_bot_subscriber',['where'=>['contact_group_id !='=>'','cron_lock'=>'0']],['id','contact_group_id'],[],100,NULL,'id asc');
        
        $subscriber_table_ids = [];
        foreach($labelinfo as $info)
            array_push($subscriber_table_ids,$info['id']);

        if(!empty($subscriber_table_ids))
        {
            $update_data = ['cron_lock'=>'1'];
            $this->db->where_in('id', $subscriber_table_ids);
            $this->db->update('messenger_bot_subscriber',$update_data);
        }

        if(!empty($labelinfo))
        {
            foreach($labelinfo as $value)
            {
                $subscriber_table_id = $value['id'];
                $contact_group_ids = explode(',',$value['contact_group_id']);
                foreach($contact_group_ids as $contact_table_id)
                {
                    $contact_table_id = trim($contact_table_id);
                    $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscribers_label(contact_group_id,subscriber_table_id) 
                        VALUES('$contact_table_id','$subscriber_table_id');");
                }
                $this->basic->update_data('messenger_bot_subscriber',['id'=>$subscriber_table_id],['cron_lock'=>'0','contact_group_id'=>'']);
            }
        }
    }



}