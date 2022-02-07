<?php 
require_once("Home.php"); // loading home controller

class Demo_account_delete extends Home
{
	public function __construct()
	{
	    parent::__construct();	    
	    $this->load->library("fb_rx_login");       
	}

    public function delete_accounts_imported_before_threedays($secret_code='42TDcCVuRsJQgPXf6q')
    {
    	if($secret_code != '42TDcCVuRsJQgPXf6q') exit;
    	$current_date = date("Y-m-d");
    	$last_seven_day = date("Y-m-d", strtotime("$current_date - 3 days"));
    	$admin_user_info = $this->basic->get_data('users',array('where'=>array('user_type'=>'Admin','email'=>'admin@xerochat.com')));

    	if(!empty($admin_user_info))
    	{
    		$admin_user_id = $admin_user_info[0]['id'];    		
	    	$fb_user_infos = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('add_date <'=>$last_seven_day,'user_id !='=>$admin_user_id)),array('id'));
	    	foreach($fb_user_infos as $value)
	    	{
	    	  $fb_page_infos = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_user_info_id'=>$value['id'])),array('id'));
	    	  foreach($fb_page_infos as $value2)
	    	    $this->demo_delete_data_basedon_page($value2['id']);

	    	  $this->demo_delete_data_basedon_account($value['id'],'1');
	    	}
    	}
    }

    private function demo_delete_data_basedon_account($fb_user_id=0,$app_delete=0)
    {
      $this->db->trans_start();
      $table_names = $this->demo_table_names_array_foraccount();
      foreach($table_names as $value)
      {
        if($this->db->table_exists($value['table_name']))
          $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$fb_user_id));
      }
      $this->db->trans_complete();                

      if ($this->db->trans_status() === FALSE) 
      {   
          $response['status'] = 0;
          $response['message'] = $this->lang->line('Something went wrong, please try again.');           
      }
      else
      {
          if($app_delete!='1')
          {
            // delete data to useges log table
            $this->_delete_usage_log($module_id=65,$request=1);
            $this->session->sess_destroy();            
          }
          $response['status'] = 1;
          $response['message'] = $this->lang->line("Your account and all of it's corresponding pages, groups and campaigns have been deleted successfully. Now you'll be redirected to the login page.");       
      }
      return $response;
    }

    private function demo_delete_data_basedon_page($table_id=0)
    {
      if($table_id == 0)
      {
        return json_encode(array('success'=>0,'message'=>$this->lang->line("Page is not found for this user. Something is wrong.")));
        exit();
      }

      $page_information = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$table_id)));
      
      $table_names = $this->demo_table_names_array();
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
            $this->_delete_usage_log($module_id=197,$request=1);
          }
          if($started_button_enabled == '1') $this->fb_rx_login->delete_get_started_button($page_access_token); // delete get started button
          if($bot_enabled == '1')
          {
            $this->fb_rx_login->disable_bot($fb_page_id,$page_access_token);
            $this->_delete_usage_log($module_id=200,$request=1);
          }

          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));
        }
        else if(isset($value['has_dependent_table']) && $value['has_dependent_table'] == 'yes')
        {
          if(isset($value['is_facebook_page_id']) && $value['is_facebook_page_id'] == 'yes')
            $table_id = $page_information[0]['page_id'];   

          $table_ids_array = array();   
          if($this->db->table_exists($value['table_name']))     
            $table_ids_info = $this->basic->get_data($value['table_name'],array('where'=>array("{$value['column_name']}"=>$table_id)),'id');
          else continue;

          foreach($table_ids_info as $info)
            array_push($table_ids_array, $info['id']);

          if($this->db->table_exists($value['table_name']))
            $this->basic->delete_data($value['table_name'],array("{$value['column_name']}"=>$table_id));

          $dependent_table_names = explode(',', $value['dependent_tables']);
          $dependent_table_column = explode(',', $value['dependent_table_column']);
          if(!empty($table_ids_array) && !empty($dependent_table_names))
          {            
            for($i=0;$i<count($dependent_table_names);$i++)
            {
              $this->db->where_in($dependent_table_column[$i], $table_ids_array);
              if($this->db->table_exists($dependent_table_names[$i]))
                $this->db->delete($dependent_table_names[$i]);
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

    }

    public function demo_table_names_array()
    {
      $tables = array (
                    0 => 
                    array (
                      'table_name' => 'auto_comment_reply_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    1 => 
                    array (
                      'table_name' => 'ultrapost_auto_reply',
                      'column_name' => 'page_ids',
                      'module_id' => ''
                    ),
                    2 => 
                    array (
                      'table_name' => 'autoposting',
                      'column_name' => 'page_ids',
                      'module_id' => '',
                      'comma_separated' => 'yes'
                    ),
                    3 => 
                    array (
                      'table_name' => 'facebook_ex_autoreply',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'facebook_ex_autoreply_report',
                      'dependent_table_column' =>'autoreply_table_id'
                    ),
                    4 => 
                    array (
                      'table_name' => 'facebook_ex_conversation_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    5 => 
                    array (
                      'table_name' => 'facebook_ex_conversation_campaign_send',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    6 => 
                    array (
                      'table_name' => 'facebook_page_insight_page_list',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    7 => 
                    array (
                      'table_name' => 'facebook_rx_auto_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    8 => 
                    array (
                      'table_name' => 'facebook_rx_cta_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),                          
                    10 => 
                    array (
                      'table_name' => 'facebook_rx_fb_page_info',
                      'column_name' => 'id',
                      'persistent_getstarted_check' => 'yes',
                      'module_id' => ''
                    ),                          
                    12 => 
                    array (
                      'table_name' => 'facebook_rx_offer_campaign',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    13 => 
                    array (
                      'table_name' => 'facebook_rx_offer_campaign_view',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),
                    16 => 
                    array (
                      'table_name' => 'facebook_rx_slider_post',
                      'column_name' => 'page_group_user_id',
                      'module_id' => ''
                    ),                          
                    24 => 
                    array (
                      'table_name' => 'messenger_bot',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    26 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_contact_group',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    27 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_serial',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    28 => 
                    array (
                      'table_name' => 'messenger_bot_broadcast_serial_send',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    29 => 
                    array (
                      'table_name' => 'messenger_bot_domain_whitelist',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    30 => 
                    array (
                      'table_name' => 'messenger_bot_drip_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    31 => 
                    array (
                      'table_name' => 'messenger_bot_drip_report',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    32 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_2way_chat_plugin',
                      'column_name' => 'page_auto_id',
                      'module_id' => ''
                    ),
                    33 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_checkbox',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    34 => 
                    array (
                      'table_name' => 'messenger_bot_drip_campaign_assign',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    ),
                    36 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_mme',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    37 => 
                    array (
                      'table_name' => 'messenger_bot_engagement_send_to_msg',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    38 => 
                    array (
                      'table_name' => 'messenger_bot_persistent_menu',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    39 => 
                    array (
                      'table_name' => 'messenger_bot_postback',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    40 => 
                    array (
                      'table_name' => 'messenger_bot_reply_error_log',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),                          
                    42 => 
                    array (
                      'table_name' => 'messenger_bot_subscriber',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    ),
                    43 => 
                    array (
                      'table_name' => 'messenger_bot_thirdparty_webhook',
                      'column_name' => 'page_id',
                      'is_facebook_page_id' => 'yes',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'messenger_bot_thirdparty_webhook_activity,messenger_bot_thirdparty_webhook_trigger',
                      'dependent_table_column' => 'webhook_id,webhook_id',
                      'module_id' => ''
                    ),
                    45 => 
                    array (
                      'table_name' => 'messenger_bot_user_custom_form_webview_data',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    48 => 
                    array (
                      'table_name' => 'page_response_auto_like_share',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    49 => 
                    array (
                      'table_name' => 'page_response_auto_like_share_report',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'page_response_auto_like_report,page_response_auto_share_report',
                      'dependent_table_column' =>'page_response_auto_like_share_report_id,page_response_auto_like_share_report_id'
                    ),
                    51 => 
                    array (
                      'table_name' => 'page_response_autoreply',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    52 => 
                    array (
                      'table_name' => 'page_response_report',
                      'column_name' => 'page_info_table_id',
                      'module_id' => '',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'facebook_ex_autoreply_report',
                      'dependent_table_column' =>'autoreply_table_id'
                    ),
                    54 => 
                    array (
                      'table_name' => 'tag_machine_bulk_reply',
                      'column_name' => 'page_info_table_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'tag_machine_bulk_reply_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    56 => 
                    array (
                      'table_name' => 'tag_machine_bulk_tag',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    57 => 
                    array (
                      'table_name' => 'tag_machine_comment_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    58 => 
                    array (
                      'table_name' => 'tag_machine_commenter_info',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ),
                    59 => 
                    array (
                      'table_name' => 'tag_machine_enabled_post_list',
                      'column_name' => 'page_info_table_id',
                      'module_id' => ''
                    ), 
                    63 => 
                    array (
                      'table_name' => 'webview_builder',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    64 => 
                    array (
                      'table_name' => 'email_sending_campaign',
                      'column_name' => 'page_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'email_sending_campaign_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    65 => 
                    array (
                      'table_name' => 'sms_sending_campaign',
                      'column_name' => 'page_id',
                      'has_dependent_table' => 'yes',
                      'dependent_tables' => 'sms_sending_campaign_send',
                      'dependent_table_column' => 'campaign_id',
                      'module_id' => ''
                    ),
                    66 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    67 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign_report',
                      'column_name' => 'page_id',
                      'module_id' => ''
                    ),
                    68 => 
                    array (
                      'table_name' => 'woocommerce_drip_campaign_webhook_call',
                      'column_name' => 'page_table_id',
                      'module_id' => ''
                    )
                  );
      return $tables;
    }

    public function demo_table_names_array_foraccount()
    {
        $tables = array(
                        1 => 
                        array (
                          'table_name' => 'facebook_rx_fb_group_info',
                          'column_name' => 'facebook_rx_fb_user_info_id',
                          'module_id' => ''
                        ),
                        2 => 
                        array (
                          'table_name' => 'facebook_rx_fb_user_info',
                          'column_name' => 'id',
                          'module_id' => ''
                        )
                );
        return $tables;
    }


    



}