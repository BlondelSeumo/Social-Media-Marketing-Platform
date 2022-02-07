<?php

require_once("Home.php"); // loading home controller

class Social_accounts extends Home
{ 
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        
        if($this->session->userdata('user_type') != 'Admin' && !in_array(65,$this->module_access))
        redirect('home/login_page', 'location'); 

        if($this->session->userdata("facebook_rx_fb_user_info")==0 && $this->config->item("backup_mode")==1 && $this->uri->segment(2)!="app_delete_action")
        redirect('social_apps/index','refresh');

        $this->important_feature();
        $this->member_validity();
        
        $this->load->library("fb_rx_login");       
    }


    public function index()
    {
      $this->account_import();
    }
  
    public function account_import()
    {
        $this->is_group_posting_exist=$this->group_posting_exist();
        $data['body'] = 'facebook_rx/account_import';
        $data['page_title'] = $this->lang->line('Facebook Account Import');

        $redirect_url = base_url()."social_accounts/manual_renew_account";
        $fb_login_button = $this->fb_rx_login->login_for_user_access_token($redirect_url);
        $data['fb_login_button'] = $fb_login_button;

        $where['where'] = array('id'=>$this->session->userdata("facebook_rx_fb_user_info"));
        $existing_accounts = $this->basic->get_data('facebook_rx_fb_user_info',$where);

        $show_import_account_box = 1;
        $data['show_import_account_box'] = 1;
        if(!empty($existing_accounts))
        {
            $i=0;
            foreach($existing_accounts as $value)
            {
                $existing_account_info[$i]['need_to_delete'] = $value['need_to_delete'];
                if($value['need_to_delete'] == '1')
                {
                   $show_import_account_box = 0; 
                   $data['show_import_account_box'] = $show_import_account_box;
                }

                $existing_account_info[$i]['fb_id'] = $value['fb_id'];
                $existing_account_info[$i]['userinfo_table_id'] = $value['id'];
                $existing_account_info[$i]['name'] = $value['name'];
                $existing_account_info[$i]['email'] = $value['email'];
                $existing_account_info[$i]['user_access_token'] = $value['access_token'];

                $valid_or_invalid = $this->fb_rx_login->access_token_validity_check_for_user($value['access_token']);
                if($valid_or_invalid)
                {
                    $existing_account_info[$i]['validity'] = 'yes';
                }
                else{
                    $existing_account_info[$i]['validity'] = 'no';
                }


                $where = array();
                $where['where'] = array('facebook_rx_fb_user_info_id'=>$value['id']);
                $page_count = $this->basic->get_data('facebook_rx_fb_page_info',$where,'','','','','has_instagram DESC');
                $existing_account_info[$i]['page_list'] = $page_count;
                if(!empty($page_count))
                {
                    $existing_account_info[$i]['total_pages'] = count($page_count);                    
                }
                else
                    $existing_account_info[$i]['total_pages'] = 0;


                $group_count = $this->basic->get_data('facebook_rx_fb_group_info',$where);
                $existing_account_info[$i]['group_list'] = $group_count;
                if(!empty($group_count))
                {
                    $existing_account_info[$i]['total_groups'] = count($group_count);                    
                }
                else
                    $existing_account_info[$i]['total_groups'] = 0;
                
                $i++;
            }

            $data['existing_accounts'] = $existing_account_info;
        }
        else
            $data['existing_accounts'] = '0';


        $this->_viewcontroller($data);
    }



    public function group_delete_action()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = 0;
                $response['message'] = "You can not delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }


        $table_id = $this->input->post("group_table_id");
        $data = array('deleted' => '1');
        $this->basic->delete_data('facebook_rx_fb_group_info',array('id'=>$table_id,'user_id'=>$this->user_id));
        echo json_encode(array('status'=>1,'message'=>$this->lang->line('Group has been deleted successfully.')));
    }


    public function page_delete_action()
    {
        $this->ajax_check();
        $response = array();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = 0;
                $response['message'] = "You can not delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }

        $table_id = $this->input->post("page_table_id",true);
        $response = $this->delete_data_basedon_page($table_id);
        echo $response;

    }

    public function location_delete_action()
    {
        $this->ajax_check();
        $response = array();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = 0;
                $response['message'] = "You can not delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }

        $location_table_id = $this->input->post("location_table_id",true);

        $this->db->trans_start();

        $this->basic->delete_data('google_business_locations',['id'=>$location_table_id]);
        $this->basic->delete_data('google_posts_campaign',['user_id'=>$this->user_id,'location_table_id'=>$location_table_id]);
        $this->basic->delete_data('google_review_reply_report',['user_id'=>$this->user_id,'location_id'=>$location_table_id]);
        $this->basic->delete_data('google_review_reply_settings',['user_id'=>$this->user_id,'location_id'=>$location_table_id]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) 
        {    
            $response['status'] = 0;
            $response['message'] = $this->lang->line('Something went wrong, please try again.');         
        }
        else
        {
            $response['status'] = 1;
            $response['message'] = $this->lang->line("Your location and all of it's corresponding campaigns have been deleted successfully.");      
        }

        
        echo json_encode($response);

    }

    public function gmb_account_delete_action()
    {
        $response = array();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = 0;
                $response['message'] = "You can't delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }
        
        $gmb_user_table_id = $this->input->post("gmb_user_table_id");

        $this->db->trans_start();

        $this->basic->delete_data('google_user_account',['user_id'=>$this->user_id,'id'=>$gmb_user_table_id]);
        $locations = $this->basic->get_data('google_business_locations',['where'=>['user_account_id'=>$gmb_user_table_id]],'id');
        foreach($locations as $value)
        {
            $this->basic->delete_data('google_business_locations',['id'=>$value['id']]);
            $this->basic->delete_data('google_posts_campaign',['user_id'=>$this->user_id,'location_table_id'=>$value['id']]);
            $this->basic->delete_data('google_review_reply_report',['user_id'=>$this->user_id,'location_id'=>$value['id']]);
            $this->basic->delete_data('google_review_reply_settings',['user_id'=>$this->user_id,'location_id'=>$value['id']]);
        }
        $this->_delete_usage_log($module_id=1,$request=1);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) 
        {    
            $response['status'] = 0;
            $response['message'] = $this->lang->line('Something went wrong, please try again.');         
        }
        else
        {
            $response['status'] = 1;
            $response['message'] = $this->lang->line("Your account and all of it's corresponding locations and campaigns have been deleted successfully. Now you'll be redirected to the login page again.");      
        }

        $this->session->sess_destroy();
        echo json_encode($response);
        
    }


    public function account_delete_action()
    {
        $response = array();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = 0;
                $response['message'] = "You can't delete anything from admin account!!";
                echo json_encode($response);
                exit();
            }
        }
        
        $facebook_rx_fb_user_info_id = $this->input->post("user_table_id");

        $account_information = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$facebook_rx_fb_user_info_id,'user_id'=>$this->user_id)));
        if(empty($account_information)){
            echo json_encode(array('success'=>0,'message'=>$this->lang->line("Account is not found for this user. Something is wrong.")));
            exit();
        }


        $page_list = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_user_info_id'=>$facebook_rx_fb_user_info_id)),array('id','page_id'));

        foreach($page_list as $value)
        {
        	$this->delete_data_basedon_page($value['id']);
        }

        $response = $this->delete_data_basedon_account($facebook_rx_fb_user_info_id);
        
        echo json_encode($response);
        
    }



    public function app_delete_action()
    {
     if($this->is_demo == '1')
      {
          if($this->session->userdata('user_type') == "Admin")
          {
              $response['status'] = 0;
              $response['message'] = "You can not delete anything from admin account!!";
              echo json_encode($response);
              exit();
          }
      }

      $this->ajax_check();
      $this->csrf_token_check();
      $app_table_id = $this->input->post('app_table_id',true);
      $app_info = $this->basic->get_data('facebook_rx_config',array('where'=>array('id'=>$app_table_id,'user_id'=>$this->user_id)));
      if(empty($app_info))
      {
        $response['status'] = 0;
        $response['message'] = $this->lang->line('We could not find any APP with this ID for this account.');  
        echo json_encode($response);
        exit;
      }

      $fb_user_infos = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('facebook_rx_config_id'=>$app_table_id)),array('id'));
      foreach($fb_user_infos as $value)
      {
        $fb_page_infos = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_user_info_id'=>$value['id'])),array('id'));
        foreach($fb_page_infos as $value2)
          $this->delete_data_basedon_page($value2['id'],'1');

        $this->delete_data_basedon_account($value['id'],'1');
      }

      $this->basic->delete_data('facebook_rx_config',array('id'=>$app_table_id,'user_id'=>$this->user_id));
      $this->session->sess_destroy(); 
      $response['status'] = 1;
      $response['message'] = $this->lang->line("APP and all the data corresponding to this APP has been deleted successfully. Now you'll be redirected to the login page.");  
      echo json_encode($response);
    }



    public function enable_disable_webhook()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        exit();
        if(!$_POST) exit();

        $response = array();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = 0;
                $response['message'] = "This function is disabled from admin account in this demo!!";
                echo json_encode($response);
                exit();
            }
        }

        $user_id = $this->user_id;
        $page_id=$this->input->post('page_id');
        $restart=$this->input->post('restart');
        $enable_disable=$this->input->post('enable_disable');
        $page_data=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));

        if(empty($page_data)){

            echo json_encode(array('success'=>0,'message'=>$this->lang->line("Page is not found for this user. Something is wrong.")));
            exit();
        }


        $fb_page_id=isset($page_data[0]["page_id"]) ? $page_data[0]["page_id"] : "";
        $page_access_token=isset($page_data[0]["page_access_token"]) ? $page_data[0]["page_access_token"] : "";
        $persistent_enabled=isset($page_data[0]["persistent_enabled"]) ? $page_data[0]["persistent_enabled"] : "0";
        $fb_user_id = $page_data[0]["facebook_rx_fb_user_info_id"];
        $fb_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$fb_user_id)));
        $this->fb_rx_login->app_initialize($fb_user_info[0]['facebook_rx_config_id']); 
        if($enable_disable=='enable')
        {
            $already_enabled = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('page_id'=>$fb_page_id,'bot_enabled !='=>'0')));
            if(!empty($already_enabled))
            {                
                if($already_enabled[0]['user_id'] != $this->user_id || $already_enabled[0]['facebook_rx_fb_user_info_id'] != $fb_user_id )
                {
                    $facebook_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$already_enabled[0]['facebook_rx_fb_user_info_id'])));
                    $facebook_user_name = isset($facebook_user_info[0]['name']) ? $facebook_user_info[0]['name'] : '';
                    $system_user_info = $this->basic->get_data('users',array('where'=>array('id'=>$already_enabled[0]['user_id'])));
                    $system_email = isset($system_user_info[0]['email']) ? $system_user_info[0]['email'] : '';
                    $response_message = $this->lang->line("This page is already enabled by other user.").'<br/>';
                    $response_message .= $this->lang->line('Enabled from').':<br/>';
                    $response_message .= $this->lang->line('Email').': '.$system_email.'<br/>';
                    $response_message .= $this->lang->line('FB account name').': '.$facebook_user_name;
                    echo json_encode(array('success'=>0,'message'=>$response_message));
                    exit();
                }
            }
            //************************************************//
            if($restart != '1')
            {                
                $status=$this->_check_usage($module_id=200,$request=1);
                if($status=="2") 
                {
                    echo json_encode(array('success'=>0,'message'=>$this->lang->line("Module limit is over.")));
                    exit();
                }
                else if($status=="3") 
                {
                    echo json_encode(array('success'=>0,'message'=>$this->lang->line("Module limit is over.")));
                    exit();
                }
            }
            //************************************************//

            $output=$this->fb_rx_login->enable_bot($fb_page_id,$page_access_token);
            if(!isset($output['error'])) $output['error'] = '';

            if($output['error'] == '')
            {
                $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id),array("bot_enabled"=>"1"));
                $this->getstarted_enable_disable_onpage($page_id,$started_button_enabled='1',$page_access_token,$fb_user_info[0]['facebook_rx_config_id']);
                $this->check_review_status_broadcaster($page_table_id=$page_id,$fb_page_id,$page_access_token,$fb_user_info[0]['facebook_rx_config_id']);
                $this->add_system_quick_email_reply($page_id,$fb_page_id);
                $this->add_system_quick_phone_reply($page_id,$fb_page_id);
                $this->add_system_quick_location_reply($page_id,$fb_page_id);
                $this->add_system_quick_birthday_reply($page_id,$fb_page_id);
                $this->add_system_postback_entry($page_id,$fb_page_id);
                $this->add_system_getstarted_reply_entry($page_id,$fb_page_id);
                $this->add_system_nomatch_reply_entry($page_id,$fb_page_id);
                $this->add_system_story_mention_reply_entry($page_id,$fb_page_id);
                $this->add_system_story_private_reply_entry($page_id,$fb_page_id);
                $this->add_system_message_unsend_reply_entry($page_id,$fb_page_id);

                if($restart != '1')                    
                    $this->_insert_usage_log($module_id=200,$request=1);
                $response['status'] = 1; 
                $response['message'] = $this->lang->line('Bot Connection has been enabled successfully.');              
            }
            else
            {
                $response['status'] = 0; 
                $response['message'] = $output['error'];
            }
        } 
        else
        {
            $updateData=array("bot_enabled"=>"2");
            if($persistent_enabled=='1') 
            {
                $updateData['persistent_enabled']='0';
                $updateData['started_button_enabled']='0';
                $this->fb_rx_login->delete_persistent_menu($page_access_token); // delete persistent menu
                $this->fb_rx_login->delete_get_started_button($page_access_token); // delete get started button
                // $this->basic->delete_data("messenger_bot_persistent_menu",array("page_id"=>$page_id,"user_id"=>$this->user_id));
                // $this->_delete_usage_log($module_id=197,$request=1);
            }
            $output=$this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);
            if(!isset($output['error'])) $output['error'] = '';
            if($output['error'] == '')
            {
                $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id),$updateData);
                $this->getstarted_enable_disable_onpage($page_id,$started_button_enabled='0',$page_access_token,$fb_user_info[0]['facebook_rx_config_id']);
                // $this->_delete_usage_log($module_id=200,$request=1);
                $response['status'] = 1; 
                $response['message'] = $this->lang->line('Bot Connection has been disabled successfully.');
            }
            else
            {
                $response['status'] = 0; 
                $response['message'] = $output['error'];
            }
        } 
        echo json_encode($response);
    }

    public function enable_disable_insta_autoreply()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(207,$this->module_access))
        exit();
        $this->ajax_check();

        $response = array();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = 0;
                $response['message'] = "This function is disabled from admin account in this demo!!";
                echo json_encode($response);
                exit();
            }
        }

        $user_id = $this->user_id;
        $table_id=$this->input->post('table_id');
        $restart=$this->input->post('restart');
        $enable_disable=$this->input->post('enable_disable');
        $page_data=$this->basic->get_data("instagram_reply_page_info",array("where"=>array("id"=>$table_id,"user_id"=>$this->user_id)));

        if(empty($page_data)){

            echo json_encode(array('success'=>0,'message'=>$this->lang->line("Page is not found for this user. Something is wrong.")));
            exit();
        }


        $fb_page_id=isset($page_data[0]["page_id"]) ? $page_data[0]["page_id"] : "";
        $instagram_business_account_id = isset($page_data[0]["instagram_business_account_id"]) ? $page_data[0]["instagram_business_account_id"] : "";
        $page_access_token=isset($page_data[0]["page_access_token"]) ? $page_data[0]["page_access_token"] : "";
        $fb_user_id = $page_data[0]["facebook_rx_fb_user_info_id"];
        $fb_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$fb_user_id)));
        $this->fb_rx_login->app_initialize($fb_user_info[0]['facebook_rx_config_id']); 
        if($enable_disable=='enable')
        {
            $already_enabled = $this->basic->get_data('instagram_reply_page_info',array('where'=>array('page_id'=>$fb_page_id,'instagram_business_account_id'=>$instagram_business_account_id,'bot_enabled !='=>'0')));
            if(!empty($already_enabled))
            {                
                if($already_enabled[0]['user_id'] != $this->user_id || $already_enabled[0]['facebook_rx_fb_user_info_id'] != $fb_user_id )
                {
                    $facebook_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$already_enabled[0]['facebook_rx_fb_user_info_id'])));
                    $facebook_user_name = isset($facebook_user_info[0]['name']) ? $facebook_user_info[0]['name'] : '';
                    $system_user_info = $this->basic->get_data('users',array('where'=>array('id'=>$already_enabled[0]['user_id'])));
                    $system_email = isset($system_user_info[0]['email']) ? $system_user_info[0]['email'] : '';
                    $response_message = $this->lang->line("This account is already enabled for auto-reply by other user.").'<br/>';
                    $response_message .= $this->lang->line('Enabled from').':<br/>';
                    $response_message .= $this->lang->line('Email').': '.$system_email.'<br/>';
                    $response_message .= $this->lang->line('FB account name').': '.$facebook_user_name;
                    echo json_encode(array('success'=>0,'message'=>$response_message));
                    exit();
                }
            }
            //************************************************//
            if($restart != '1')
            {                
                $status=$this->_check_usage($module_id=207,$request=1);
                if($status=="2") 
                {
                    echo json_encode(array('success'=>0,'message'=>$this->lang->line("Module limit is over.")));
                    exit();
                }
                else if($status=="3") 
                {
                    echo json_encode(array('success'=>0,'message'=>$this->lang->line("Module limit is over.")));
                    exit();
                }
            }
            //************************************************//

            $output=$this->fb_rx_login->enable_bot($fb_page_id,$page_access_token);
            if(!isset($output['error'])) $output['error'] = '';

            if($output['error'] == '')
            {
                $this->basic->update_data("instagram_reply_page_info",array("id"=>$table_id,'user_id'=>$this->user_id),array("bot_enabled"=>"1"));
                if($restart != '1')                    
                    $this->_insert_usage_log($module_id=207,$request=1);
                $response['status'] = 1; 
                $response['message'] = $this->lang->line('Auto Reply has been enabled successfully.');              
            }
            else
            {
                $response['status'] = 0; 
                $response['message'] = $output['error'];
            }
        } 
        else
        {
            $updateData=array("bot_enabled"=>"2");
            $output=$this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);

            if(!isset($output['error'])) $output['error'] = '';
            if($output['error'] == '')
            {
                $this->basic->update_data("instagram_reply_page_info",array("id"=>$table_id,'user_id'=>$this->user_id),$updateData);
                $response['status'] = 1; 
                $response['message'] = $this->lang->line('Auto Reply has been disabled successfully.');
            }
            else
            {
                $response['status'] = 0; 
                $response['message'] = $output['error'];
            }
        } 
        echo json_encode($response);
    }

    private function getstarted_enable_disable_onpage($page_id,$started_button_enabled,$page_access_token,$facebook_rx_config_id)
    {
      $this->load->library("fb_rx_login");
      $this->fb_rx_login->app_initialize($facebook_rx_config_id);
      if($started_button_enabled=='1')
      {
        $response=$this->fb_rx_login->add_get_started_button($page_access_token);
        if(!isset($response['error']))
          $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id,'user_id'=>$this->user_id),array("started_button_enabled"=>'1'));
      }
      else
      {
        $response=$this->fb_rx_login->delete_get_started_button($page_access_token);
        if(!isset($response['error']))
          $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id,'user_id'=>$this->user_id),array("started_button_enabled"=>'0'));
      }
    }

    private function check_review_status_broadcaster($page_table_id=0,$fb_page_id=0,$access_token='',$facebook_rx_config_id=0)
    {
        $auto_id=$page_table_id; // database id
        if($auto_id == 0) return false;

        $page_id=$fb_page_id;
        
        $this->load->library('fb_rx_login');
        $this->fb_rx_login->app_initialize($facebook_rx_config_id);

        $get_page_review_status=$this->fb_rx_login->get_page_review_status($access_token);

        $review_status=isset($get_page_review_status["data"][0]["status"]) ? strtoupper($get_page_review_status["data"][0]["status"]) : "NOT SUBMITTED";
        if($review_status=="") $review_status="NOT SUBMITTED";
        $user_id=$this->user_id;

        
        //DEPRECATED FUNCTION FOR QUICK BROADCAST
        //$existing_labels=$this->fb_rx_login->retrieve_label($access_token);
        $existing_labels = [];
        if(isset($existing_labels['error']['message'])) $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$existing_labels["error"]["message"];

        $group_name="Unsubscribe";
        $group_name2="SystemInvisible01";
        
        if(isset($existing_labels["data"]))
        foreach ($existing_labels["data"] as $key => $value) 
        {
            $existng_name=$value['name'];
            $existng_id=$value['id'];

            $unsbscribed='0';
            if($existng_name==$group_name) $unsbscribed='1';

            $is_invisible='0';
            if($existng_name==$group_name2) $is_invisible='1';

            $existng_name = $this->db->escape($existng_name);

            $sql="INSERT IGNORE INTO messenger_bot_broadcast_contact_group(page_id,group_name,user_id,label_id,unsubscribe,invisible) VALUES('$auto_id',$existng_name,'$user_id','$existng_id','$unsbscribed','$is_invisible')";
            $this->basic->execute_complex_query($sql);
        }

        
        if(!$this->basic->is_exist("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"unsubscribe"=>"1")))
        {
            // $response=$this->fb_rx_login->create_label($access_token,$group_name);
            $response=['id'=>''];
            $label_id=isset($response['id']) ? $response['id'] : "";

            $errormessage="";
            if(isset($response["error"]["error_user_msg"]))
                $errormessage=$response["error"]["error_user_msg"];
            else if(isset($response["error"]["message"]))
                $errormessage=$response["error"]["message"];

            
            // if($label_id=="") 
            // $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$errormessage;
            // else 
            $this->basic->insert_data("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"group_name"=>$group_name,"user_id"=>$this->user_id,"label_id"=>$label_id,"deleted"=>"0","unsubscribe"=>"1"));
        }

        if(!$this->basic->is_exist("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"invisible"=>"1")))
        {            
            //$response=$this->fb_rx_login->create_label($access_token,$group_name2);
            $response = ['id'=>''];
            $label_id=isset($response['id']) ? $response['id'] : "";

            $errormessage="";
            
            if(isset($response["error"]["error_user_msg"]))
                $errormessage=$response["error"]["error_user_msg"];
            else if(isset($response["error"]["message"]))
                $errormessage=$response["error"]["message"];

            
            // if($label_id=="") 
            // $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$errormessage;
            // else 
            $this->basic->insert_data("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"group_name"=>$group_name2,"user_id"=>$this->user_id,"label_id"=>$label_id,"deleted"=>"0","unsubscribe"=>"0","invisible"=>"1"));
        } 


        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$auto_id,"user_id"=>$this->user_id),array("review_status"=>$review_status,"review_status_last_checked"=>date("Y-m-d H:i:s")));

        
            
        if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"UNSUBSCRIBE_QUICK_BOXER","page_id"=>$auto_id)))
        {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Un-Subscribe Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-212,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"UNSUBSCRIBE_QUICK_BOXER","actionTypeText":"Un-subscribe template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"You have been successfully unsubscribed from our list. It is sad to see you go. It is not the same without you! You can join back by clicking the button below.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[{"node":4,"input":"buttonInput","data":{}}]},"textOutputQuickreply":{"connections":[]}},"position":[23,-70],"name":"Text"},"4":{"id":4,"data":{"postbackId":"80q2orNMIqn5Q8n","buttonText":"Resubscribe","buttonType":"RESUBSCRIBE_QUICK_BOXER","text":"Re-subscribe"},"inputs":{"buttonInput":{"connections":[{"node":3,"output":"textOutputButton","data":{}}]}},"outputs":{"buttonOutput":{"connections":[]}},"position":[332.5217603600544,59.82610287873642],"name":"Button"}}}';

            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(1,1000).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Un-Subscribe Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'UNSUBSCRIBE_QUICK_BOXER'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "post-back","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"You have been successfully unsubscribed from our list. It is sad to see you go. It is not the same without you! You can join back by clicking the button below.","buttons":[{"type":"postback","payload":"RESUBSCRIBE_QUICK_BOXER","title":"Resubscribe"}]}}}}}\', "", "", "", "", "", "1", "UNSUBSCRIBE BOT", "UNSUBSCRIBE_QUICK_BOXER", "", "1","flow","fb","'.$visual_flow_campaign_id.'");';

            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'","UNSUBSCRIBE_QUICK_BOXER","'.$auto_id.'","0","1","'.$insert_id.'","UNSUBSCRIBE BOT","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"You have been successfully unsubscribed from our list. It is sad to see you go. It is not the same without you! You can join back by clicking the button below.","buttons":[{"type":"postback","payload":"RESUBSCRIBE_QUICK_BOXER","title":"Resubscribe"}]}}}}}\',"UNSUBSCRIBE TEMPLATE","unsubscribe","flow","fb","'.$visual_flow_campaign_id.'")';
            $this->db->query($sql);
        }

        if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"RESUBSCRIBE_QUICK_BOXER","page_id"=>$auto_id)))
        {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Re-Subscribe Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-212.69564155910325,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"RESUBSCRIBE_QUICK_BOXER","actionTypeText":"Re-subscribe template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"Welcome back! We have not seen you for a while. You will no longer miss our important updates.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[{"node":4,"input":"buttonInput","data":{}}]},"textOutputQuickreply":{"connections":[]}},"position":[23,-70],"name":"Text"},"4":{"id":4,"data":{"postbackId":"80q2orNMIqn5Q8n","buttonText":"Unsubscribe","buttonType":"UNSUBSCRIBE_QUICK_BOXER","text":"Unsubscribe"},"inputs":{"buttonInput":{"connections":[{"node":3,"output":"textOutputButton","data":{}}]}},"outputs":{"buttonOutput":{"connections":[]}},"position":[332.5217603600544,60.52037621914542],"name":"Button"}}}';

            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(1000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Re-Subscribe Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'RESUBSCRIBE_QUICK_BOXER'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "post-back","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"Welcome back! We have not seen you for a while. You will no longer miss our important updates.","buttons":[{"type":"postback","payload":"UNSUBSCRIBE_QUICK_BOXER","title":"Unsubscribe"}]}}}}}\', "", "", "", "", "", "1", "RESUBSCRIBE BOT", "RESUBSCRIBE_QUICK_BOXER", "", "1","flow","fb","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'","RESUBSCRIBE_QUICK_BOXER","'.$auto_id.'","0","1","'.$insert_id.'","RESUBSCRIBE BOT","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"Welcome back! We have not seen you for a while. You will no longer miss our important updates.","buttons":[{"type":"postback","payload":"UNSUBSCRIBE_QUICK_BOXER","title":"Unsubscribe"}]}}}}}\',"RESUBSCRIBE TEMPLATE","resubscribe","flow","fb","'.$visual_flow_campaign_id.'")';
            $this->db->query($sql);
        }


       return true;
    }


    private function add_system_quick_email_reply($auto_id="",$page_id="")
    {
       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"QUICK_REPLY_EMAIL_REPLY_BOT","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Email Quick Reply Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-212.69564155910325,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"QUICK_REPLY_EMAIL_REPLY_BOT","actionTypeText":"Email quick reply template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450.69564156892255,-66.69564156892255],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"Thanks, we have received your email. We will keep you updated. Thank you for being with us.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[23,-70],"name":"Text"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(2000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Email Quick Reply Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'QUICK_REPLY_EMAIL_REPLY_BOT'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $user_id=$this->user_id;
            $sql='INSERT INTO `messenger_bot` ( `user_id`, `page_id`, `fb_page_id`, `template_type`, `bot_type`, `keyword_type`, `keywords`, `message`, `buttons`, `images`, `audio`, `video`, `file`, `status`, `bot_name`, `postback_id`, `last_replied_at`, `is_template`, `visual_flow_type` , `media_type` , `visual_flow_campaign_id`) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "email-quick-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Thanks, we have received your email. We will keep you updated. Thank you for being with us."}}}\', "", "", "", "", "", "1", "QUICK REPLY EMAIL REPLY", "QUICK_REPLY_EMAIL_REPLY_BOT", "0000-00-00 00:00:00", "0", "flow", "fb", "'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'","QUICK_REPLY_EMAIL_REPLY_BOT","'.$auto_id.'","0","1","'.$insert_id.'","QUICK REPLY EMAIL REPLY","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Thanks, we have received your email. We will keep you updated. Thank you for being with us."}}}\',"QUICK REPLY EMAIL REPLY","email-quick-reply","flow","fb","'.$visual_flow_campaign_id.'")';
            $this->db->query($sql);
        }
        return true;
    }

    private function add_system_quick_phone_reply($auto_id="",$page_id="")
    {
       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"QUICK_REPLY_PHONE_REPLY_BOT","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Phone Quick Reply Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-212.69564155910325,-72.30229529177778],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"QUICK_REPLY_PHONE_REPLY_BOT","actionTypeText":"Phone quick reply template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450.69564156892255,-66.69564156892255],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"Thanks, we have received your phone number. We will keep you updated. Thank you for being with us.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[23,-70],"name":"Text"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(3000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Phone Quick Reply Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'QUICK_REPLY_PHONE_REPLY_BOT'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $user_id=$this->user_id;
            $sql='INSERT INTO `messenger_bot` ( `user_id`, `page_id`, `fb_page_id`, `template_type`, `bot_type`, `keyword_type`, `keywords`, `message`, `buttons`, `images`, `audio`, `video`, `file`, `status`, `bot_name`, `postback_id`, `last_replied_at`, `is_template`, `visual_flow_type` , `media_type` , `visual_flow_campaign_id`) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "phone-quick-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Thanks, we have received your phone number. We will keep you updated. Thank you for being with us."}}}\', "", "", "", "", "", "1", "QUICK REPLY PHONE REPLY", "QUICK_REPLY_PHONE_REPLY_BOT", "0000-00-00 00:00:00", "0","flow","fb","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'","QUICK_REPLY_PHONE_REPLY_BOT","'.$auto_id.'","0","1","'.$insert_id.'","QUICK REPLY PHONE REPLY","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Thanks, we have received your phone number. We will keep you updated. Thank you for being with us."}}}\',"QUICK REPLY PHONE REPLY","phone-quick-reply","flow","fb","'.$visual_flow_campaign_id.'")';
            $this->db->query($sql);
        }
        return true;
    }

    private function add_system_quick_location_reply($auto_id="",$page_id="")
    {
       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"QUICK_REPLY_LOCATION_REPLY_BOT","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Location Quick Reply Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-194,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"QUICK_REPLY_LOCATION_REPLY_BOT","actionTypeText":"Location quick reply template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"Thanks, we have received your location. Thank you for being with us.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[58,-103],"name":"Text"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(4000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Location Quick Reply Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'QUICK_REPLY_LOCATION_REPLY_BOT'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $user_id=$this->user_id;
            $sql='INSERT INTO `messenger_bot` ( `user_id`, `page_id`, `fb_page_id`, `template_type`, `bot_type`, `keyword_type`, `keywords`, `message`, `buttons`, `images`, `audio`, `video`, `file`, `status`, `bot_name`, `postback_id`, `last_replied_at`, `is_template`, `visual_flow_type` , `media_type` , `visual_flow_campaign_id`) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "location-quick-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Thanks, we have received your location. Thank you for being with us."}}}\', "", "", "", "", "", "1", "QUICK REPLY LOCATION REPLY", "QUICK_REPLY_LOCATION_REPLY_BOT", "0000-00-00 00:00:00", "0","flow","fb","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'","QUICK_REPLY_LOCATION_REPLY_BOT","'.$auto_id.'","0","1","'.$insert_id.'","QUICK REPLY LOCATION REPLY","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Thanks, we have received your location. Thank you for being with us."}}}\',"QUICK REPLY LOCATION REPLY","location-quick-reply","flow","fb","'.$visual_flow_campaign_id.'")';
            $this->db->query($sql);
        }
        return true;
    }

    private function add_system_quick_birthday_reply($auto_id="",$page_id="")
    {
       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"QUICK_REPLY_BIRTHDAY_REPLY_BOT","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Birthday Quick Reply Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-194,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"QUICK_REPLY_BIRTHDAY_REPLY_BOT","actionTypeText":"Birthday quick reply template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"Thanks, we have received your birthday. Thank you for being with us.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[58,-103],"name":"Text"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(5000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Birthday Quick Reply Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'QUICK_REPLY_BIRTHDAY_REPLY_BOT'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $user_id=$this->user_id;
            $sql='INSERT INTO `messenger_bot` ( `user_id`, `page_id`, `fb_page_id`, `template_type`, `bot_type`, `keyword_type`, `keywords`, `message`, `buttons`, `images`, `audio`, `video`, `file`, `status`, `bot_name`, `postback_id`, `last_replied_at`, `is_template`, `visual_flow_type` , `media_type` , `visual_flow_campaign_id`) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "birthday-quick-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Thanks, we have received your birthday. Thank you for being with us."}}}\', "", "", "", "", "", "1", "QUICK REPLY BIRTHDAY REPLY", "QUICK_REPLY_BIRTHDAY_REPLY_BOT", "0000-00-00 00:00:00", "0","flow","fb","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'","QUICK_REPLY_BIRTHDAY_REPLY_BOT","'.$auto_id.'","0","1","'.$insert_id.'","QUICK REPLY BIRTHDAY REPLY","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Thanks, we have received your birthday. Thank you for being with us."}}}\',"QUICK REPLY BIRTHDAY REPLY","birthday-quick-reply","flow","fb","'.$visual_flow_campaign_id.'")';
            $this->db->query($sql);
        }
        return true;
    }


    private function add_system_postback_entry($auto_id="",$page_id="")
    {
       $user_id=$this->user_id;
        
       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"YES_START_CHAT_WITH_HUMAN","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Chat With Human Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-194,-73.69594455863344],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"YES_START_CHAT_WITH_HUMAN","actionTypeText":"Chat with human template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"Thanks! It is a pleasure talking to you. One of our team members will reply to you soon. If you want to chat with me again, just click the button below.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[{"node":5,"input":"buttonInput","data":{}}]},"textOutputQuickreply":{"connections":[]}},"position":[58,-103],"name":"Text"},"5":{"id":5,"data":{"postbackId":"Om4PDFz9pk_kwts","buttonText":"Resume Chat with Bot","buttonType":"YES_START_CHAT_WITH_BOT","text":"Chat with robot"},"inputs":{"buttonInput":{"connections":[{"node":3,"output":"textOutputButton","data":{}}]}},"outputs":{"buttonOutput":{"connections":[]}},"position":[375.6521739130435,41.043541949728265],"name":"Button"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(6000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Chat With Human Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'YES_START_CHAT_WITH_HUMAN'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "post-back","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"Thanks! It is a pleasure talking to you. One of our team members will reply to you soon. If you want to chat with me again, just click the button below.","buttons":[{"type":"postback","payload":"YES_START_CHAT_WITH_BOT","title":"Resume Chat with Bot"}]}}}}}\', "", "", "", "", "", "1", "CHAT WITH HUMAN", "YES_START_CHAT_WITH_HUMAN", "", "1","flow","fb","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'","YES_START_CHAT_WITH_HUMAN","'.$auto_id.'","0","1","'.$insert_id.'","CHAT WITH HUMAN","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"Thanks! It is a pleasure talking to you. One of our team members will reply to you soon. If you want to chat with me again, just click the button below.","buttons":[{"type":"postback","payload":"YES_START_CHAT_WITH_BOT","title":"Resume Chat with Bot"}]}}}}}\',"CHAT WITH HUMAN TEMPLATE","chat-with-human","flow","fb","'.$visual_flow_campaign_id.'")';
            $this->db->query($sql);
       }

       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"YES_START_CHAT_WITH_BOT","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Chat With Robot Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-194,-73.69594455863344],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"YES_START_CHAT_WITH_BOT","actionTypeText":"Chat with robot template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"I am glad to have you back. I will try my best to answer all questions. If you want to start chatting with humans again you can simply click the button below.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[{"node":5,"input":"buttonInput","data":{}}]},"textOutputQuickreply":{"connections":[]}},"position":[58,-103],"name":"Text"},"5":{"id":5,"data":{"postbackId":"Om4PDFz9pk_kwts","buttonText":"Chat with human","buttonType":"YES_START_CHAT_WITH_HUMAN","text":"Chat with human"},"inputs":{"buttonInput":{"connections":[{"node":3,"output":"textOutputButton","data":{}}]}},"outputs":{"buttonOutput":{"connections":[]}},"position":[375.6521739130435,41.043541949728265],"name":"Button"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(7000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Chat With Robot Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'YES_START_CHAT_WITH_BOT'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "post-back","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"I am glad to have you back. I will try my best to answer all questions. If you want to start chatting with humans again you can simply click the button below.","buttons":[{"type":"postback","payload":"YES_START_CHAT_WITH_HUMAN","title":"Chat with human"}]}}}}}\', "", "", "", "", "", "1", "CHAT WITH BOT", "YES_START_CHAT_WITH_BOT", "", "1","flow","fb","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'","YES_START_CHAT_WITH_BOT","'.$auto_id.'","0","1","'.$insert_id.'","RESUBSCRIBE BOT","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"I am glad to have you back. I will try my best to answer all questions. If you want to start chatting with humans again you can simply click the button below.","buttons":[{"type":"postback","payload":"YES_START_CHAT_WITH_HUMAN","title":"Chat with human"}]}}}}}\',"CHAT WITH BOT TEMPLATE","chat-with-bot","flow","fb","'.$visual_flow_campaign_id.'")';
            $this->db->query($sql);
       }
       return true;
    }


    private function add_system_getstarted_reply_entry($auto_id="",$page_id="")
    {
       $user_id=$this->user_id;
        
       if(!$this->basic->is_exist("messenger_bot",array("keyword_type"=>"get-started","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Get Started Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-212,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"get-started","actionTypeText":"Get-started template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"Hi #LEAD_USER_FIRST_NAME#, Welcome to our page.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[23,-94],"name":"Text"}}}';
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(8000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Get Started Template',
                'media_type' => 'fb',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'get-started'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "get-started","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Hi #LEAD_USER_FIRST_NAME#, Welcome to our page."}}}\', "", "", "", "", "", "1", "GET STARTED", "", "", "0","flow","fb","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
       }
       return true;
    }

    private function add_system_nomatch_reply_entry($auto_id="",$page_id="")
    {
       $user_id=$this->user_id;

       $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"No Match Template","postbackId":"3NYSXhmp_yz-Jvt","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-212,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"d0SDPeFm3qQNxVU","actionType":"no match","actionTypeText":"No match template","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"EKp6WxW7lHlVDBK","textMessage":"Sorry, we could not find any content to show. One of our team members will reply to you soon.","delayReplyFor":"0","IsTypingOnDisplayChecked":false},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputButton":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[23,-94],"name":"Text"}}}';
       $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(1,9999).'-'.uniqid();
       $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
       $flow_insert_data = [
           'user_id' => $this->user_id,
           'page_id' => $auto_id,
           'unique_id' => $unique_id,
           'reference_name' => 'No Match Template',
           'media_type' => 'fb',
           'json_data' => $json_data,
           'is_system' => '1',
           'action_type' => 'no match'
       ];
       $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
       $fb_visual_flow_campaign_id = $this->db->insert_id();
        
       if(!$this->basic->is_exist("messenger_bot",array("keyword_type"=>"no match","page_id"=>$auto_id,"media_type"=>"fb")))
       {
            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "no match","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Sorry, we could not find any content to show. One of our team members will reply to you soon."}}}\', "", "", "", "", "", "1", "NO MATCH FOUND", "", "", "0","flow","fb","'.$fb_visual_flow_campaign_id.'");';
            $this->db->query($sql);
       }

       $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(1000,9999).'-'.uniqid();
       $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
       $flow_insert_data = [
           'user_id' => $this->user_id,
           'page_id' => $auto_id,
           'unique_id' => $unique_id,
           'reference_name' => 'No Match Template',
           'media_type' => 'ig',
           'json_data' => $json_data,
           'is_system' => '1',
           'action_type' => 'no match'
       ];
       $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
       $ig_visual_flow_campaign_id = $this->db->insert_id();

       if(!$this->basic->is_exist("messenger_bot",array("keyword_type"=>"no match","page_id"=>$auto_id,"media_type"=>"ig")))
       {
            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,visual_flow_type,media_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "no match","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":"0","text_reply_unique_id":"EKp6WxW7lHlVDBK","template_type":"text","text":"Sorry, we could not find any content to show. One of our team members will reply to you soon."}}}\', "", "", "", "", "", "1", "NO MATCH FOUND", "", "", "0","flow","ig","'.$ig_visual_flow_campaign_id.'");';
            $this->db->query($sql);
       }
       return true;
    }

    private function add_system_story_mention_reply_entry($auto_id="",$page_id="")
    {
       $user_id=$this->user_id;
        
       if(!$this->basic->is_exist("messenger_bot",array("keyword_type"=>"story-mention","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Story Mention Template","postbackId":"mvs_sYC31zTb1H6","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-212,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"QEY1J4O3de3NxAN","actionType":"STORY_MENTION","actionTypeText":"Story mention","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"HjKksxHRQtGc7hq","textMessage":"Thanks for mentioning me."},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[23.695641568922547,-70],"name":"Text"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(5000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Story Mention Template',
                'media_type' => 'ig',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'STORY_MENTION'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,media_type,visual_flow_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "story-mention","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":0,"text_reply_unique_id":"HjKksxHRQtGc7hq","template_type":"text","text":"Thanks for mentioning me."}}}\', "", "", "", "", "", "1", "STORY MENTION", "STORY_MENTION", "", "0", "ig","flow","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
       }
       return true;
    }

    private function add_system_story_private_reply_entry($auto_id="",$page_id="")
    {
       $user_id=$this->user_id;
        
       if(!$this->basic->is_exist("messenger_bot",array("keyword_type"=>"story-private-reply","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Story Private Reply Template","postbackId":"mvs_sYC31zTb1H6","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-212,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"QEY1J4O3de3NxAN","actionType":"STORY_PRIVATE_REPLY","actionTypeText":"Story private reply","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"HjKksxHRQtGc7hq","textMessage":"Thanks for your comment on my story."},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[28,-85],"name":"Text"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(5000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Story Private Reply Template',
                'media_type' => 'ig',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'STORY_PRIVATE_REPLY'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,media_type,visual_flow_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "story-private-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":0,"text_reply_unique_id":"HjKksxHRQtGc7hq","template_type":"text","text":"Thanks for your comment on my story."}}}\', "", "", "", "", "", "1", "STORY PRIVATE REPLY", "STORY_PRIVATE_REPLY", "", "0", "ig","flow","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
       }
       return true;
    }


    private function add_system_message_unsend_reply_entry($auto_id="",$page_id="")
    {
       $user_id=$this->user_id;
        
       if(!$this->basic->is_exist("messenger_bot",array("keyword_type"=>"message-unsend-private-reply","page_id"=>$auto_id)))
       {
            $json_data = '{"id":"xitFB@0.0.1","nodes":{"1":{"id":1,"data":{"title":"Message Unsend Private Reply Template","postbackId":"mvs_sYC31zTb1H6","xitFbpostbackId":"#UNIQUE_ID#","labelIds":[],"labelIdTextsArray":[],"sequenceIdValue":"","sequenceIdText":"Select a sequence"},"inputs":{"referenceInput":{"connections":[]},"referenceInputActionButton":{"connections":[{"node":2,"output":"actionButtonOutput","data":{}}]}},"outputs":{"referenceOutput":{"connections":[{"node":3,"input":"textInput","data":{}}]},"referenceOutputSequence":{"connections":[]}},"position":[-195,-73],"name":"Start Bot Flow"},"2":{"id":2,"data":{"uniqueId":"QEY1J4O3de3NxAN","actionType":"MESSAGE_UNSEND_PRIVATE_REPLY","actionTypeText":"Message unsend private reply","pluginType":"","pluginTypeText":"Select","domain":"","language":"en_US","languageText":"English (US)","ctaButtonType":"","ctaButtonTypeText":"Default","pluginSkin":"light","pluginCenterAlign":"true","pluginSize":"medium","redirectionStatus":"0","optInSuccessMessage":"You have been subscribed successfully, thank you.","addingButtonInSuccessMessage":"0","buttonText":"Send Message","buttonUrl":"","buttonBgColor":"","buttonTextColor":"","buttonHoverBgColor":"","buttonHoverTextColor":"","buttonSize":"medium","redirectionUrlForSuccessfulOptin":"","checkboxValidationErrorMessage":"","chatPluginLoadingStatus":"show","chatPluginLoadingDelayInSeconds":"","chatPluginThemeColor":"#FFFFFF","hideChatPluginIfNotLoggedIn":"0","greetingTextIfLoggedIn":"","greetingTextIfNotLoggedIn":"","reference":"","labelIds":[],"labelIdTextsArray":[]},"inputs":{},"outputs":{"actionButtonOutput":{"connections":[{"node":1,"input":"referenceInputActionButton","data":{}}]}},"position":[-450,-66],"name":"Action Button"},"3":{"id":3,"data":{"uniqueId":"HjKksxHRQtGc7hq","textMessage":"As you have deleted your message, we want to ensure you that our bot does not save any of your messages. So it will not be saved in our bot. Thanks"},"inputs":{"textInput":{"connections":[{"node":1,"output":"referenceOutput","data":{}}]}},"outputs":{"textOutput":{"connections":[]},"textOutputQuickreply":{"connections":[]}},"position":[108,-112],"name":"Text"}}}';
            
            $unique_id = uniqid().$this->user_id."-".$auto_id.'-'.rand(5000,9999).'-'.uniqid();
            $json_data = str_replace('#UNIQUE_ID#',$unique_id,$json_data);
            $flow_insert_data = [
                'user_id' => $this->user_id,
                'page_id' => $auto_id,
                'unique_id' => $unique_id,
                'reference_name' => 'Message Unsend Private Reply Template',
                'media_type' => 'ig',
                'json_data' => $json_data,
                'is_system' => '1',
                'action_type' => 'MESSAGE_UNSEND_PRIVATE_REPLY'
            ];
            $this->basic->insert_data('visual_flow_builder_campaign',$flow_insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();

            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template,media_type,visual_flow_type,visual_flow_campaign_id) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "message-unsend-private-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"typing_on_settings":"off","delay_in_reply":0,"text_reply_unique_id":"HjKksxHRQtGc7hq","template_type":"text","text":"As you have deleted your message, we want to ensure you that our bot does not save any of your messages. So it will not be saved in our bot. Thanks"}}}\', "", "", "", "", "", "1", "MESSAGE UNSEND PRIVATE REPLY", "MESSAGE_UNSEND_PRIVATE_REPLY", "", "0", "ig","flow","'.$visual_flow_campaign_id.'");';
            $this->db->query($sql);
       }
       return true;
    }



    public function delete_full_bot()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access)) exit();

        $response = array();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                $response['status'] = 0;
                $response['message'] = "This function is disabled from admin account in this demo!!";
                echo json_encode($response);
                exit();
            }
        }

        $user_id = $this->user_id;
        $page_id=$this->input->post('page_id');
        $already_disabled=$this->input->post('already_disabled');       

        $page_data=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));

        if(empty($page_data)){
            echo json_encode(array('success'=>0,'message'=>$this->lang->line("Page is not found for this user. Something is wrong.")));
            exit();
        }

        $fb_page_id=isset($page_data[0]["page_id"]) ? $page_data[0]["page_id"] : "";
        $page_access_token=isset($page_data[0]["page_access_token"]) ? $page_data[0]["page_access_token"] : "";
        $persistent_enabled=isset($page_data[0]["persistent_enabled"]) ? $page_data[0]["persistent_enabled"] : "0";
        $ice_breaker_status=isset($page_data[0]["ice_breaker_status"]) ? $page_data[0]["ice_breaker_status"] : "0";
        $ig_ice_breaker_status=isset($page_data[0]["ig_ice_breaker_status"]) ? $page_data[0]["ig_ice_breaker_status"] : "0";
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

            if($ig_ice_breaker_status=='1') 
            {
                $updateData['ig_ice_breaker_status']='0';
                $this->fb_rx_login->delete_ice_breakers($page_access_token,'ig');
            }

            $response=$this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);
        }
        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id),$updateData);
        $this->_delete_usage_log($module_id=200,$request=1);

        $this->delete_bot_data($page_id,$fb_page_id);

        $response['status'] = 1;
        $response['message'] = $this->lang->line("Bot Connection and all of the settings and campaigns of this page has been deleted successfully.");

        echo json_encode($response);

    }


    private function delete_bot_data($page_id,$fb_page_id)
    {

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

        $table_id = $page_id;
        $page_information = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));

        $table_names=$this->table_names_array();

        foreach($table_names as $value)
        {
          if(isset($value['persistent_getstarted_check']) && $value['persistent_getstarted_check'] == 'yes')
          {
            $fb_page_id=isset($page_information[0]["page_id"]) ? $page_information[0]["page_id"] : "";
            $page_access_token=isset($page_information[0]["page_access_token"]) ? $page_information[0]["page_access_token"] : "";
            $persistent_enabled=isset($page_information[0]["persistent_enabled"]) ? $page_information[0]["persistent_enabled"] : "0";
            $bot_enabled=isset($page_information[0]["bot_enabled"]) ? $page_information[0]["bot_enabled"] : "0";
            $started_button_enabled=isset($page_information[0]["started_button_enabled"]) ? $page_information[0]["started_button_enabled"] : "0";
            $fb_user_id = $page_information[0]["facebook_rx_fb_user_info_id"];
            $fb_user_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$fb_user_id)));
            $this->fb_rx_login->app_initialize($fb_user_info[0]['facebook_rx_config_id']); 

            if($persistent_enabled == '1') 
            {
              $this->fb_rx_login->delete_persistent_menu($page_access_token); // delete persistent menu
              if($admin_access != '1')
                  $this->_delete_usage_log($module_id=197,$request=1);
            }
            if($started_button_enabled == '1') $this->fb_rx_login->delete_get_started_button($page_access_token); // delete get started button
            if($bot_enabled == '1')
            {
              $this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);
              if($admin_access != '1')
                  $this->_delete_usage_log($module_id=200,$request=1);
            }

            if($value['table_name'] != 'facebook_rx_fb_page_info') // need not to delete from page table while disabling bot
            {
                if($this->db->table_exists($value['table_name']))
                  $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
            }
          }
          else if(isset($value['has_dependent_table']) && $value['has_dependent_table'] == 'yes')
          {
            $table_ids_array = array();   
            if($this->db->table_exists($value['table_name']))
            {
              if(isset($value['is_facebook_page_id']) && $value['is_facebook_page_id'] == 'yes')
              {
                $facebook_page_id = $page_information[0]['page_id']; 
                $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$facebook_page_id)),'id');
              }
              else
                $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$table_id)),'id');

            }    
            else continue;

            foreach($table_ids_info as $info)
              array_push($table_ids_array, $info['id']);


            if($this->db->table_exists($value['table_name']))
            {
              if(isset($value['is_facebook_page_id']) && $value['is_facebook_page_id'] == 'yes')
                $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$facebook_page_id));
              else
                $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
            }

            $dependent_table_names = explode(',', $value['dependent_tables']);
            $dependent_table_column = explode(',', $value['dependent_table_column']);
            if(!empty($table_ids_array) && !empty($dependent_table_names))
            {            
              for($i=0;$i<count($dependent_table_names);$i++)
              {
                if($this->db->table_exists($dependent_table_names[$i]))
                {
                  $this->db->where_in($dependent_table_column[$i], $table_ids_array);
                  $this->db->delete($dependent_table_names[$i]);
                }
              }
            }
          }
          else if(isset($value['comma_separated']) && $value['comma_separated'] == 'yes')
          {
            $str = "FIND_IN_SET('".$table_id."', ".$value['column_name'].") !=";
            $where = array($str=>0);
            if($this->db->table_exists($value['table_name']))
              $this->basic->delete_data($value['table_name'],$where);
          }
          else
          {
            if($this->db->table_exists($value['table_name']))
              $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
          }
        }

        return true;
    } 



    public function manual_renew_account()
    {
        $this->is_group_posting_exist=$this->group_posting_exist();
        $id = $this->session->userdata('fb_rx_login_database_id');
        $redirect_url = base_url()."social_accounts/manual_renew_account";

        $user_info = array();
        $user_info = $this->fb_rx_login->login_callback_without_email($redirect_url);   
                
        if( isset($user_info['status']) && $user_info['status'] == '0')
        {
            $data['error'] = 1;
            $data['message'] = $this->lang->line("something went wrong in profile access")." : ".$user_info['message'];
            $data['body'] = "facebook_rx/user_login";
            $this->_viewcontroller($data);
        } 
        else 
        {
            $access_token=$user_info['access_token_set'];

            //checking permission given by the users            
            $permission = $this->fb_rx_login->debug_access_token($access_token);

            $given_permission = array();
            if(isset($permission['data']['scopes']))
            {
                $permission_checking = array();
                $needed_permission = array('manage_pages','publish_pages','pages_messaging');
                $given_permission = $permission['data']['scopes'];
                $permission_checking = array_intersect($needed_permission,$given_permission);
                if(empty($permission_checking))
                {
                    $documentation_link = base_url('documentation/#!/sm_import_account');
                    $text = $this->lang->line("All needed permissions are not approved for your app")." [".implode(',', $needed_permission)."]";
                    $this->session->set_userdata('limit_cross', $text);
                    redirect('social_accounts/index','location');                
                    exit();
                }
            }
            
            if(isset($access_token))
            {
                $data = array(
                    'user_id' => $this->user_id,
                    'facebook_rx_config_id' => $id,
                    'access_token' => $access_token,
                    'name' => $user_info['name'],
                    'fb_id' => $user_info['id'],
                    'add_date' => date('Y-m-d'),
                    'deleted' => '0'
                    );

                $where=array();
                $where['where'] = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                $exist_or_not = array();
                $exist_or_not = $this->basic->get_data('facebook_rx_fb_user_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                if(empty($exist_or_not))
                {
                    //************************************************//
                    $status=$this->_check_usage($module_id=65,$request=1);
                    if($status=="2") 
                    {
                        $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                        redirect('social_accounts/index','location');                
                        exit();
                    }
                    else if($status=="3") 
                    {
                        $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                        redirect('social_accounts/index','location');                
                        exit();
                    }
                    //************************************************//
                    $this->basic->insert_data('facebook_rx_fb_user_info',$data);
                    $facebook_table_id = $this->db->insert_id();

                    //insert data to useges log table
                    $this->_insert_usage_log($module_id=65,$request=1);
                }
                else
                {
                    $facebook_table_id = $exist_or_not[0]['id'];
                    $where = array('user_id'=>$this->user_id,'id'=>$facebook_table_id);
                    $this->basic->update_data('facebook_rx_fb_user_info',$where,$data);
                }

                $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_table_id);  

                $page_list = array();
                $page_list = $this->fb_rx_login->get_page_list($access_token);

                if(isset($page_list['error']) && $page_list['error'] == '1')
                {
                    $data['error'] = 1;
                    $data['message'] = $this->lang->line("Something went wrong in page access")." : ".$page_list['message'];
                    $data['body'] = "facebook_rx/user_login";
                    return $this->_viewcontroller($data);                    
                }

                if(!empty($page_list))
                {
                    foreach($page_list as $page)
                    {
                        $user_id = $this->user_id;
                        $page_id = $page['id'];
                        $page_cover = '';
                        if(isset($page['cover']['source'])) $page_cover = $page['cover']['source'];
                        $page_profile = '';
                        if(isset($page['picture']['url'])) $page_profile = $page['picture']['url'];
                        $page_name = '';
                        if(isset($page['name'])) $page_name = $page['name'];
                        $page_access_token = '';
                        if(isset($page['access_token'])) $page_access_token = $page['access_token'];
                        $page_email = '';
                        if(isset($page['emails'][0])) $page_email = $page['emails'][0];
                        $page_username = '';
                        if(isset($page['username'])) $page_username = $page['username'];

                        $data = array(
                            'user_id' => $user_id,
                            'facebook_rx_fb_user_info_id' => $facebook_table_id,
                            'page_id' => $page_id,
                            'page_cover' => $page_cover,
                            'page_profile' => $page_profile,
                            'page_name' => $page_name,
                            'username' => $page_username,
                            'page_access_token' => $page_access_token,
                            'page_email' => $page_email,
                            'add_date' => date('Y-m-d'),
                            'deleted' => '0'
                            );

                        // instagram section
                        $instagram_account_exist_or_not = '';
                        if($this->config->item('instagram_reply_enable_disable') == '1')
	                      //  $instagram_account_exist_or_not = $this->fb_rx_login->instagram_account_check_by_id($page['id'], $page['access_token']);
                            $instagram_account_exist_or_not = $this->fb_rx_login->instagram_account_check_by_id($page['id'], $access_token);
                        
                        if ($instagram_account_exist_or_not != "") {
                         //  $instagram_account_info = $this->fb_rx_login->instagram_account_info($instagram_account_exist_or_not, $access_token); 
                            $instagram_account_info = $this->fb_rx_login->instagram_account_info($instagram_account_exist_or_not, $access_token); 

                            $data['has_instagram'] = '1';
                            $data['instagram_business_account_id'] = $instagram_account_exist_or_not; 
                            $data['insta_username'] = isset($instagram_account_info['username']) ? $instagram_account_info['username'] : "";
                            $data['insta_followers_count'] = isset($instagram_account_info['followers_count']) ? $instagram_account_info['followers_count'] : "";
                            $data['insta_media_count'] = isset($instagram_account_info['media_count']) ? $instagram_account_info['media_count'] : "";
                            $data['insta_website'] = isset($instagram_account_info['website']) ? $instagram_account_info['website'] : "";
                            $data['insta_biography'] = isset($instagram_account_info['biography']) ? $instagram_account_info['biography'] : "";
                        }
                        // end of instagram section

                        $where=array();
                        $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                        $exist_or_not = array();
                        $exist_or_not = $this->basic->get_data('facebook_rx_fb_page_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('facebook_rx_fb_page_info',$data);
                        }
                        else
                        {
                            $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                            $this->basic->update_data('facebook_rx_fb_page_info',$where,$data);
                        }


                    }
                }

                $group_list = array();
                if($this->config->item('facebook_poster_group_enable_disable') == '1' && $this->is_group_posting_exist)
                    $group_list = $this->fb_rx_login->get_group_list($access_token);


                if(!empty($group_list))
                {
                    foreach($group_list as $group)
                    {
                        $user_id = $this->user_id;
                        $group_access_token = $access_token; // group uses user access token
                        $group_id = $group['id'];
                        $group_cover = '';
                        if(isset($group['cover']['source'])) $group_cover = $group['cover']['source'];
                        $group_profile = '';
                        if(isset($group['picture']['url'])) $group_profile = $group['picture']['url'];
                        $group_name = '';
                        if(isset($group['name'])) $group_name = $group['name'];

                        $data = array(
                            'user_id' => $user_id,
                            'facebook_rx_fb_user_info_id' => $facebook_table_id,
                            'group_id' => $group_id,
                            'group_cover' => $group_cover,
                            'group_profile' => $group_profile,
                            'group_name' => $group_name,
                            'group_access_token' => $group_access_token,
                            'add_date' => date('Y-m-d'),
                            'deleted' => '0'
                            );

                        $where=array();
                        $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                        $exist_or_not = array();
                        $exist_or_not = $this->basic->get_data('facebook_rx_fb_group_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);

                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('facebook_rx_fb_group_info',$data);
                        }
                        else
                        {
                            $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                            $this->basic->update_data('facebook_rx_fb_group_info',$where,$data);
                        }
                    }
                }

                $this->session->set_userdata('success_message', 'success');
                redirect('social_accounts/index','location');                
                exit();
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = "'".$this->lang->line("something went wrong,please")."' <a href='".base_url("social_accounts/account_import")."'>'".$this->lang->line("try again")."'</a>";
                $data['body'] = "facebook_rx/user_login";
                $this->_viewcontroller($data);
            }
        }
    }

    public function fb_rx_account_switch()
    {
        $this->ajax_check();
        $id=$this->input->post("id");
        
        $this->session->set_userdata("facebook_rx_fb_user_info",$id); 

        $get_user_data = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $config_id = isset($get_user_data[0]["facebook_rx_config_id"]) ? $get_user_data[0]["facebook_rx_config_id"] : 0;
        $this->session->set_userdata("fb_rx_login_database_id",$config_id);

        $this->session->unset_userdata("bot_list_get_page_details_page_table_id");
        $this->session->unset_userdata("sync_subscribers_get_page_details_page_table_id");
        $this->session->unset_userdata("get_page_details_page_table_id");
    }

    public function gmb_account_switch()
    {
        $this->ajax_check();
        $id=$this->input->post("id");
        
        $get_user_data = $this->basic->get_data("google_user_account",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)), ['id']);

        if (count($get_user_data)) {
            $this->session->set_userdata("google_mybusiness_user_table_id", $id);
        }
    }

    public function pages_messaging()
    {
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('user_id' => $this->user_id,'facebook_rx_fb_user_info_id'=>$this->session->userdata('facebook_rx_fb_user_info'))), array('id', 'page_id', 'page_name', 'webhook_enabled'));

        $page_dropdown = array();
        $is_any_page_enabled = false;
        $enabled_page = '';

        $page_dropdown[-1] = $this->lang->line('Please select a page');
        foreach ($page_info as $key => $single_page) {
            
            if ($single_page['webhook_enabled'] == '1') {

                $is_any_page_enabled = true;
                $enabled_page = $single_page['id'];
            }

            $page_dropdown[$single_page['id']] = $single_page['page_name'];
        }

        $data['page_dropdown'] = $page_dropdown;
        $data['is_any_page_enabled'] = $is_any_page_enabled;
        $data['enabled_page'] = $enabled_page;
        $data['page_info'] = $page_info;

        if($this->db->table_exists('messenger_bot'))
            $data['has_messenger_bot'] = 'yes';
        else
            $data['has_messenger_bot'] = 'no';

        $page_messaging_info = $this->basic->get_data('page_messaging_information');
        $data['page_messaging_info'] = $page_messaging_info;

        $data['body'] = 'facebook_rx/pages_messaging';
        $data['title'] = 'Page Messaging Settings';
        $this->_viewcontroller($data);
    }


    public function enableDisableWebHook()
    {
        if (!isset($_POST)) exit;

        $page_id = $this->input->post('page_id');
        $page_table_id = $page_id;
        $enable_or_disable = $this->input->post('enable_or_disable');

        if ($enable_or_disable == "disabled")
            $webhook_enabled = '1';
        else
            $webhook_enabled = '0';

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_id)),array('page_access_token','page_id'));
        $page_access_token = $page_info[0]['page_access_token'];
        $page_id = $page_info[0]['page_id'];

        $this->load->library('fb_rx_login');
        $this->fb_rx_login->app_initialize($this->session->userdata('fb_rx_login_database_id'));

        if($webhook_enabled == '1')
            $response = $this->fb_rx_login->enable_webhook($page_id,$page_access_token);
        else
            $response = $this->fb_rx_login->disable_webhook($page_id,$page_access_token);

        if($response['error'] != '')
        {
            echo json_encode($response);
            exit;
        }

        if($response['error'] == '' && $page_table_id != '-1')
        {
            $this->basic->update_data('facebook_rx_fb_page_info', array('id' => $page_table_id), array('webhook_enabled' => $webhook_enabled));
            $response['error'] = '';
            echo json_encode($response);
        }
    }


    public function submitPagesMessageInfo()
    {
        if (!isset($_POST)) exit;

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$value;
        }

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$enabled_page)),array('page_id'));
        $page_id = $page_info[0]['page_id'];

        $this->basic->execute_complex_query("TRUNCATE TABLE page_messaging_information");

        for($i=1;$i<=3;$i++)
        {
            $reply_bot = array();
            $reply_variable = 'reply_'.$i;
            $reply_variable = isset($$reply_variable) ? $$reply_variable : '';

            $keyword = 'keyword_'.$i;
            $keyword = isset($$keyword) ? $$keyword : '';

            $reply_bot['text'] = $reply_variable;

            $json_data = array();
            $json_data['recipient'] = array('id'=>'replace_id');
            $json_data['messaging_type'] = 'RESPONSE';
            $json_data['message'] = $reply_bot;
            $json_encode_data = json_encode($json_data);

            $insert_data = array();
            $insert_data['keywords'] = $keyword;
            $insert_data['message'] = $json_encode_data;
            $insert_data['reply_message'] = $reply_variable;
            $insert_data['user_id'] = $this->user_id;
            $insert_data['page_id'] = $enabled_page;
            $insert_data['fb_page_id'] = $page_id;

            $this->basic->insert_data('page_messaging_information',$insert_data);
                        
        }

        echo 'success';

    }

    /**
     * Added for XeroBiz Addon
     */
    public function import_gmb_account_callback()
    {
        $this->load->library("google_my_business");
        $config_table_id = $this->session->userdata('gmb_config_table_id');
        $myBusiness = $this->google_my_business;
        $client = $this->google_my_business->client;
        $oauth = $this->google_my_business->oauth;

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $access_token = $client->getAccessToken();
            $client->setAccessToken($access_token);
        }

        $login_msg = '';
        $accounts = [];

        try {
            $accounts = $myBusiness->accountInformation();
        } catch (Google_Service_Exception $e) {
            $login_msg = $e->getMessage();
        } catch (Google_Exception $e) {
            $login_msg = $e->getMessage();
        } catch (\Exception $e) {
            $login_msg = $e->getMessage();
        }

        if (! empty($login_msg)) {
            $this->session->set_userdata('gmb_login_msg', $login_msg);
            return redirect("gmb/business_accounts");
        }

        $name = '';
        $account_id = '';

        if (isset($accounts[0])) {
            $account_id = isset($accounts[0]['name']) ? $accounts[0]['name'] : '';
            $name = isset($accounts[0]['accountName']) ? $accounts[0]['accountName'] : '';
        }

        $userProfile = '';

        try {
            $userProfile = $oauth->userinfo->get();
        } catch (Google_Service_Exception $e) {
            $login_msg = $e->getMessage();
        } catch (Google_Exception $e) {
            $login_msg = $e->getMessage();
        } catch (\Exception $e) {
            $login_msg = $e->getMessage();
        }

        if (! empty($login_msg)) {
            $this->session->set_userdata('gmb_login_msg', $login_msg);
            return redirect("gmb/business_accounts");
        }

        $email = '';
        $username = '';
        $profilePicture = '';

        if (is_object($userProfile)) {
            $username = trim($userProfile->getGivenName() . ' ' . $userProfile->getFamilyName());
            $email = $userProfile->getEmail();
            $profilePicture = $userProfile->getPicture();
        }

        $access_token = $client->getAccessToken();

        if($account_id && $email)
        {

                

                $data = [
                    'user_id' => $this->user_id,
                    'app_config_id' => $config_table_id,
                    'account_id'=> $account_id,
                    'account_name' => $username,
                    'email' => $email,
                    'account_display_name' => $name,
                    'access_token' => $access_token,
                    'profile_photo' => $profilePicture
                ];

                $existing_info = $this->basic->get_data('google_user_account',['where'=>['user_id'=>$this->user_id,'account_id'=>$account_id]]);
                if(empty($existing_info))
                {
                    //************************************************//
                    $status=$this->_check_usage($module_id=300,$request=1);
                    if($status=="2") 
                    {
                        $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                        redirect("gmb/business_accounts");               
                        exit();
                    }
                    else if($status=="3") 
                    {
                        $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                        redirect("gmb/business_accounts");               
                        exit();
                    }
                    //************************************************//
                    $this->basic->insert_data('google_user_account',$data);
                    $google_mybusiness_user_table_id = $this->db->insert_id();
                    $this->session->set_userdata('google_mybusiness_user_table_id',$google_mybusiness_user_table_id);

                    $this->_insert_usage_log($module_id=300,$request=1); 
                }
                else
                {
                    $this->basic->update_data('google_user_account',['user_id'=>$this->user_id,'account_id'=>$account_id],$data);
                    $google_mybusiness_user_table_id = $existing_info[0]['id'];
                    $this->session->set_userdata('google_mybusiness_user_table_id',$google_mybusiness_user_table_id);
                }

                $error = '';
                $locations = [];

                try {
                    $locations = $myBusiness->listAccountsLocations($account_id);
                } catch (Google_Service_Exception $e) {
                    $error = $e->getMessage();
                } catch (Google_Exception $e) {
                    $error = $e->getMessage();
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }

                if (! empty($error)) {
                    // Do your stuff
                }

                if (count($locations)) {

                    foreach ($locations as $key => $location) {

                        $reviews_list = [];

                        try {
                            $reviews_list = $myBusiness->reviewsList($location['name']);
                        } catch (Google_Service_Exception $e) {
                            $e->getMessage();
                        } catch (Google_Exception $e) {
                            $e->getMessage();
                        } catch (\Exception $e) {
                            $e->getMessage();
                        }

                        $last_review = null;

                        if (count($reviews_list)) {
                            $last_review = isset($reviews_list['reviews'][0]['name']) ? $reviews_list['reviews'][0]['name'] : null;
                        }
                        $temp = explode('/', $location['name']);
                        $only_location_id = array_pop($temp);
                        $data = [];
                        $data['user_id'] = $this->user_id;
                        $data['user_account_id'] = $google_mybusiness_user_table_id;
                        $data['location_id'] = $location['name'];
                        $data['only_location_id'] = $only_location_id;
                        $data['location_display_name'] = $location['locationName'];
                        $data['latitude_longitude'] = json_encode($location['latlng']);
                        $data['map_url'] = isset($location['metadata']['mapsUrl']) ? $location['metadata']['mapsUrl'] : '';
                        $data['new_review_url'] = isset($location['metadata']['newReviewUrl']) ? $location['metadata']['newReviewUrl'] : '';
                        $data['address'] = json_encode($location['address']);
                        $data['last_review_reply_id'] = $last_review;

                        $profile_google_url = '';
                        $cover_google_url = '';

                        // Pulls off media
                        $error = '';
                        $mediaItems = [];

                        try {
                            $mediaItems = $myBusiness->list_media($location['name']);
                        } catch (Google_Service_Exception $e) {
                            $error = $e->getMessage();
                        } catch (Google_Exception $e) {
                            $error = $e->getMessage();
                        } catch (\Exception $e) {
                            $error = $e->getMessage();
                        }

                        if (! empty($error)) {
                            // Do your stuff
                        }

                        $profile_google_url = '';
                        $cover_google_url = '';

                        if (count($mediaItems)) {
                            foreach ($mediaItems as $key => $media) {

                                if (isset($media['locationAssociation']['category']) 
                                    && 'profile' == strtolower($media['locationAssociation']['category'])
                                ) {
                                    $profile_google_url = isset($media['googleUrl']) ? $media['googleUrl'] : '';
                                }

                                if (isset($media['locationAssociation']['category']) 
                                    && 'cover' == strtolower($media['locationAssociation']['category'])
                                ) {
                                    $cover_google_url = isset($media['googleUrl']) ? $media['googleUrl'] : '';
                                }
                            }
                        }

                        $data['profile_google_url'] = $profile_google_url;
                        $data['cover_google_url'] = $cover_google_url;

                        // Pulls reviews and replies

                        if(!$this->basic->is_exist("google_business_locations",array("user_account_id"=>$google_mybusiness_user_table_id,"location_id"=>$location['name'])))
                            $this->basic->insert_data('google_business_locations',$data);
                        else
                            $this->basic->update_data('google_business_locations',["user_account_id"=>$google_mybusiness_user_table_id,"location_id"=>$location['name']],$data);

                    }
                }

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('gmb/business_accounts', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('gmb/business_accounts', 'location');
                }

        } else {
            $this->session->set_userdata('gmb_login_msg', $this->lang->line("Account name or email not found."));
            return redirect("gmb/business_accounts");
        }
    }
}