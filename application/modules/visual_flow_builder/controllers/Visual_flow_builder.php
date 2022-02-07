<?php
/*
Addon Name: Visual Flow Builder 
Unique Name: visual_flow_builder
Modules:
{
   "315":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"Visual flow builder access"
   }
}
Project ID: 59
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.6.9
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

class Visual_flow_builder extends Home
{
    /**
     * An array of php file upload errors
     *
     * @var array
     */
    protected $php_file_upload_errors = [
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    ];   

    public $addon_data=array(); 
    public $need_to_whitelist_array = [];
    public $new_sequence_information_array = [];
    public $user_input_flowcampaigns_unique_ids = [];

    public function __construct()
    {
        parent::__construct();

        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $addondata=$this->get_addon_data($addon_path);
        $this->addon_data=$addondata;


        // all addon must be login protected
        // but we need allow ajax cors and ajax check for flowbuilder development environment
        //------------------------------------------------------------------------------------------
        if ($this->session->userdata('logged_in')!= 1 && $this->strict_ajax_call) redirect('home/login', 'location'); 
        if(!$this->strict_ajax_call) {
            $this->user_id = 1;
            $this->module_access = [213,214,215,217,219,292,315,325,330];
        }

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

    public function activate()
    {
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
        $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
        
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array(); 
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=
        array
        (
            0 => "CREATE TABLE IF NOT EXISTS `visual_flow_builder_campaign` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `user_id` int(11) NOT NULL,
                      `page_id` int(11) NOT NULL,
                      `unique_id` varchar(50) NOT NULL,
                      `reference_name` text NOT NULL,
                      `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
                      `json_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code);
    }


    public function deactivate()
    {        
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);      
    }

    public function delete()
    {        
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]

        // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql=array
        (       
            0=> "DROP TABLE IF EXISTS `visual_flow_builder_campaign`;"
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }

    public function index()
    {
        $this->flowbuilder_manager();
    }

    

    public function get_dropdown_postback()
    {
        $this->ajax_check();

        $page_table_id = $this->input->post('page_table_id',true);
        $instagram_bot_addon = (bool) $this->input->post('instagram_bot_addon',true);

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_table_id,"media_type" => $instagram_bot_addon ? 'ig' : 'fb',"user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message')),['postback_id','template_name','postback_type'],$join='',$limit='',$start=NULL,$order_by='postback_type ASC');
        
        $push_postback="";
        $push_postback.="<option value=''>".$this->lang->line("Select")."</option>";
        foreach ($postback_data as $key => $value) 
        {
            $push_postback .= "<option value='".$value['postback_id']."'>".$value['template_name']."</option>";
        }
        $response['dropdown_str'] = $push_postback;
        echo json_encode($response,true);

    }

    public function get_dropdown_otn()
    {
        $this->ajax_check();

        $page_auto_id=$this->input->post('page_table_id',true);// database id

        $postback_id_list = $this->basic->get_data('otn_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)),array('id','otn_postback_id','template_name'));

        $str='';
        $str .="<option value=''>".$this->lang->line("Select")."</option>";
        $str .="<option value='newOtn'>".$this->lang->line("New OTN")."</option>";
        if(!empty($postback_id_list))
        {            
            foreach ($postback_id_list as  $value)
            {
                $array_key = $value['id'];
                $array_value = $value['otn_postback_id']." (".$value['template_name'].")";
                $str .="<option value='{$array_key}'>{$array_value}</option>";            

            }
        }

        echo json_encode(array('dropdown_str'=>$str));
    }

    public function get_userinput_flow_list()
    {
        $this->ajax_check();

        $str = '';
        if($this->addon_exist("custom_field_manager"))
        {
            $page_id=$this->input->post('page_table_id',true);// database 
            $instagram_bot_addon = (bool) $this->input->post('instagram_bot_addon',true);

            $table_type = 'user_input_flow_campaign';
            $where_type['where'] = array('user_id'=>$this->user_id,"media_type" => $instagram_bot_addon ? 'ig' : 'fb',"page_table_id"=>$page_id);
            $info_type = $this->basic->get_data($table_type,$where_type);
            
            $str = '<option value="">'.$this->lang->line('Select Flow campaign').'</option>';
            foreach ($info_type as  $value)
            {
                $id = $value['id'];
                $name = $value['flow_name'];
                $str.=  "<option value='{$id}'>".$name."</option>";            

            }
        }

        echo json_encode(array('dropdown_str'=>$str));
    }

    public function get_label_sequence_dropdown()
    {
        $this->ajax_check();

        $page_id=$this->input->post('page_table_id'); // database id
        $requested_from=$this->input->post('requested_from'); // Request from what?
        $instagram_bot_addon = (bool) $this->input->post('instagram_bot_addon',true);

        $response = [];
        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"social_media" => $instagram_bot_addon ? "ig" : "fb","unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');
        $label_str = '';
        // $label_str = '<option value="0">'.$this->lang->line('Select Labels').'</option>';
        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_type = $value['group_name'];
            $label_str .=  "<option value='{$search_key}'>".$search_type."</option>";            
        }
        $response['label_dropdown'] = $label_str;


        $table_type = 'messenger_bot_drip_campaign';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='');
        $drip_str = '<option value="">'.$this->lang->line('Select a sequence').'</option>';

        if ('new_postback' == $requested_from || 'reference' == $requested_from) {
            $drip_str .= '<option value="newSequence">'.$this->lang->line('New sequence').'</option>';
        }

        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_value = $value['campaign_name'];
            $drip_str .=  "<option value='{$search_key}'>".$search_value."</option>";
        }
        $response['drip_dropdown'] = $drip_str;

        $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id)));        
        $mme_link=base_url();
        if(isset($pageinfo[0]))
        {
          $param=isset($pageinfo[0]['username'])?$pageinfo[0]['username']:"";
          if($param=="") $param=$pageinfo[0]['page_id'];
          $mme_link="https://m.me/".$param;
        }
        $response['mme_link'] = $mme_link;

        echo json_encode($response,true);
    }

    public function get_label_dropdown()
    {
        $this->ajax_check();

        $page_id=$this->input->post('page_table_id'); // database id
        $response = [];
        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');
            
        foreach ($info_type as  $value)
        {            
            $response[] = [ 'key' => $value['id'], 'value' => $value['group_name']];
        }

        echo json_encode($response, true);
    }

    public function get_custom_field_variable_dropdown($media_type)
    {
        $this->ajax_check();

        $response = [];
        $table_type = 'user_input_custom_fields';
        $where_type['where'] = array('user_id'=>$this->user_id,"media_type"=>('ig' == $media_type ? "ig" : "fb"));
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='name');
        $options = '<option value="">' . $this->lang->line("Select") . '</option>';
        $optionsArray = [];

        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_type = $value['name'];
            $options .=  "<option value='custom_{$search_key}'>".$search_type."</option>";
            $optionsArray[] = ['key' => $value['id'], 'value' => $value['name']];
        }
        $response['custom_field_variable_dropdown'] = $options;
        $response['custom_field_variable_dropdown_array'] = $optionsArray;

        echo json_encode($response,true);
    }   

    public function get_store_list()
    {
        $this->ajax_check();
        $page_auto_id=$this->input->post('page_table_id');// database id

        $store_lists = $this->basic->get_data('ecommerce_store',['where'=>['ecommerce_store.user_id'=>$this->user_id,'page_id'=>$page_auto_id]],['ecommerce_store.id','store_name','store_city','currency'],array('ecommerce_config'=>"ecommerce_config.store_id=ecommerce_store.id,left"));

        $str = '';
        $str .= "<option value=''>".$this->lang->line("Select ecommerce store")."</option>";
        if(!empty($store_lists))
        {            
            foreach ($store_lists as  $value)
            {
                $currency = $value['currency'];
                $array_key = $value['id'];
                $array_value = $value['store_name']." - ".$value['store_city'];
                $str .= "<option data-currency='{$currency}' value='{$array_key}'>{$array_value}</option>";            

            }
        }

        echo json_encode(array('store_list'=>$str));

    }

    public function get_storewise_products()
    {
        $this->ajax_check();

        $store_id=$this->input->post('selectedStoreId',true);
        $product_lists = $this->basic->get_data('ecommerce_product',['where'=>['user_id'=>$this->user_id,'store_id'=>$store_id]],['id','product_name','thumbnail','sell_price']);

        $str='';
        // $str .="<option value=''>".$this->lang->line("Select")."</option>";
        if(!empty($product_lists))
        {            
            foreach ($product_lists as  $value)
            {
                $array_key = $value['id'];
                $array_value = $value['product_name'];
                $img = $value['thumbnail'];
                $price = $value['sell_price'];
                $str .="<option data-price='{$price}' data-thumbnail='{$img}' value='{$array_key}'>{$array_value}</option>";    
            }
        }

        echo json_encode(array('product_list'=>$str));
    }


    public function flowbuilder_submit()
    {
        $this->ajax_check();
        $this->load->library('flow_builder');

        $flow_data=$this->input->post('flow_data',true);
        if(!$flow_data){
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("POST data not sent correctly.Try by disabling mod_security for flow builder url.")));
            exit; 
        }

        $message_sent_stat_addon = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>64)))
            if($this->session->userdata('user_type') == 'Admin' || count(array_intersect(array(330),$this->module_access))>0)
                $message_sent_stat_addon = 1;

        $builder_table_id=$this->input->post('builder_table_id',true);
        $flow_array = json_decode($flow_data,true);
        $bot_settings_array = $this->flow_builder->extract_json($flow_array);

        $reference_name = $bot_settings_array[0]['postback_title'] ?? '';
        $unique_id = $bot_settings_array[0]['xitFbUniqueId'] ?? '';
        $json_data = $flow_data;
        $page_table_id = $this->input->post('page_table_id',true);
        $instagram_bot_addon = $this->input->post('instagram_bot_addon',true);


        // Get the visual flow campaign information , this is needed if it comes from creation & or edit without inserting the unique id 
        if(!$builder_table_id){
            $flow_where['where']=array("unique_id"=>$unique_id);
            $visual_flow_campaign_info= $this->basic->get_data("visual_flow_builder_campaign",$flow_where);
            $builder_table_id= $visual_flow_campaign_info[0]['id'] ?? 0; 
        }


        $action_buttons_array = ['reply','UNSUBSCRIBE_QUICK_BOXER','RESUBSCRIBE_QUICK_BOXER','YES_START_CHAT_WITH_BOT','YES_START_CHAT_WITH_HUMAN','QUICK_REPLY_BIRTHDAY_REPLY_BOT','QUICK_REPLY_LOCATION_REPLY_BOT','QUICK_REPLY_PHONE_REPLY_BOT','QUICK_REPLY_EMAIL_REPLY_BOT','get-started','no match','STORY_MENTION','STORY_PRIVATE_REPLY','MESSAGE_UNSEND_PRIVATE_REPLY'];
        $action_button_type = $bot_settings_array[0]['action_button_type'] ?? '';
        if(in_array($action_button_type, $action_buttons_array))
        {
            $system_flow_where = [
                'where' => [
                    'user_id' => $this->user_id,
                    'page_id' => $page_table_id,
                    'is_system' => '1',
                    'action_type' => $action_button_type
                ]
            ];
            if($instagram_bot_addon == 1) $system_flow_where['where']['media_type'] = 'ig';
            else $system_flow_where['where']['media_type'] = 'fb';
            $system_flow_data = $this->basic->get_data('visual_flow_builder_campaign',$system_flow_where,'id');
            if(!empty($system_flow_data))
              $builder_table_id = $system_flow_data[0]['id'];  
        }

        $this->db->trans_start();

        $insert_data = [
                        'user_id' => $this->user_id,
                        'page_id' => $page_table_id,
                        'json_data' => $json_data,
                        'reference_name' => $reference_name,
                        'unique_id' => $unique_id
                    ];
        if($instagram_bot_addon == 1) $insert_data['media_type'] = 'ig';

        $existing_bot_data = [];
        $existing_postback_data = [];
        $edited = 0;

        if($builder_table_id != 0)
        {
            $edited = 1;
            $this->basic->update_data('visual_flow_builder_campaign',['id'=>$builder_table_id,'user_id'=>$this->user_id],$insert_data);
            $visual_flow_campaign_id = $builder_table_id;

            $temp_bot_data = $this->basic->get_data('messenger_bot',['where'=>['visual_flow_campaign_id'=>$visual_flow_campaign_id,'user_id'=>$this->user_id]],['postback_id','id']);
            foreach($temp_bot_data as $value)
                $existing_bot_data[$value['postback_id']] = $value['id'];

            $temp_postback_data = $this->basic->get_data('messenger_bot_postback',['where'=>['visual_flow_campaign_id'=>$visual_flow_campaign_id,'user_id'=>$this->user_id]], ['id','postback_id']);
            foreach($temp_postback_data as $value)
                $existing_postback_data[$value['postback_id']] = $value['id'];

            $this->basic->delete_data('messenger_bot',['visual_flow_campaign_id'=>$visual_flow_campaign_id,'user_id'=>$this->user_id,'keyword_type'=>'reply']);
            $this->basic->delete_data('messenger_bot_postback',['visual_flow_campaign_id'=>$visual_flow_campaign_id,'user_id'=>$this->user_id,'template_for'=>'reply_message']);

        }
        else
        {
            if(in_array($action_button_type, $action_buttons_array))
            {
                $insert_data['is_system'] = '1';
                $insert_data['action_type'] = $action_button_type;
            }
            $this->basic->insert_data('visual_flow_builder_campaign',$insert_data);
            $visual_flow_campaign_id = $this->db->insert_id();
        }

        $submitted_unique_ids = $bot_settings_array['unique_ids'];
        if($message_sent_stat_addon)
        {
            $existing_unique_ids = [];
            unset($bot_settings_array['unique_ids']);
            $existing_unique_ids_info = $this->basic->get_data('visual_flow_campaign_unique_ids',['where'=>['visual_flow_campaign_id'=>$visual_flow_campaign_id,'page_table_id'=>$page_table_id]],['element_unique_id']);
            foreach($existing_unique_ids_info as $temp_info)
                array_push($existing_unique_ids,$temp_info['element_unique_id']);
            $need_to_insert = array_diff($submitted_unique_ids, $existing_unique_ids);

            foreach($need_to_insert as $temp_unique_id)
                $this->basic->execute_complex_query("INSERT IGNORE INTO visual_flow_campaign_unique_ids(page_table_id,visual_flow_campaign_id,element_unique_id) VALUES($page_table_id,$visual_flow_campaign_id,'$temp_unique_id');");
            
        }
        else
            unset($bot_settings_array['unique_ids']);


        $facebook_rx_fb_page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)),array("facebook_rx_fb_user_info_id","page_access_token","page_id"));
        $page_access_token = $facebook_rx_fb_page_info[0]['page_access_token'];
        $fb_page_id = $facebook_rx_fb_page_info[0]["page_id"];
        $facebook_rx_fb_user_info_id = $facebook_rx_fb_page_info[0]["facebook_rx_fb_user_info_id"];

        $facebook_rx_config_info = $this->basic->get_data('facebook_rx_fb_user_info',['where'=>['facebook_rx_fb_user_info.id'=>$facebook_rx_fb_user_info_id,'facebook_rx_fb_user_info.user_id'=>$this->user_id]],['api_id'],['facebook_rx_config'=>'facebook_rx_fb_user_info.facebook_rx_config_id=facebook_rx_config.id,left']);
        $fb_app_id = $facebook_rx_config_info[0]['api_id'];

        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$facebook_rx_fb_user_info_id,"page_id"=>$page_table_id)),"domain");

        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }

        // $need_to_whitelist_array = array();
        // $new_sequence_information_array=array();

        $otn_postback_ids_info = [];
        $postbacks_for_otn_single = [];
        $postback_id_table_id_info = [];

        foreach ($bot_settings_array as $key => $value) {
            $reply_bot = [];
            $postback_id = $value['postback_id'];
            $postback_title = $value['postback_title'];
            $trigger_keywords = $value['trigger_keywords'] ?? '';
            $trigger_matching_type = $value['trigger_matching_type'] ?? 'exact';
            $sequence_id = $value['sequence_id'] ?? '';
            $conditions = $value['conditions'] ?? "";

            if(is_array($value['label_ids']))
                $label_ids = implode(',', $value['label_ids']);
            else
                $label_ids = '';

            // keep the new sequence information in another array to process it later after inserting all postbacks . 
            $action_button_type = '';
            $new_sequence_information= $value['new_sequence_information'] ?? '';
            if($new_sequence_information){
                $this->new_sequence_information_array[$postback_id] = $new_sequence_information;
                
                $action_button_type = $value['action_button_type'] ?? '';
                if($action_button_type)
                    $this->new_sequence_information_array[$postback_id]['action_button_settings']['action_button_type'] = $action_button_type;
            }

            $insert_data = array();
            $insert_data_to_bot = array();

            if(isset($value['xitFbUniqueId']))
                $insert_data['postback_type'] = 'main';
            else
                $insert_data['postback_type'] = 'sub';

            $insert_data['bot_name'] = $postback_title;
            $insert_data_to_bot['bot_name'] = $postback_title;
            $insert_data['template_name'] = $postback_title;
            $insert_data['postback_id'] = trim($postback_id);
            $insert_data_to_bot['postback_id'] = trim($postback_id);
            $insert_data['page_id'] = $page_table_id;
            $insert_data_to_bot['page_id'] = $page_table_id;
            $insert_data['is_template'] = '1';
            $insert_data_to_bot['is_template'] = '1';
            $insert_data['use_status'] = '1';
            $insert_data["broadcaster_labels"]=$label_ids;
            $insert_data_to_bot["broadcaster_labels"]=$label_ids;
            $insert_data["drip_campaign_id"]=$sequence_id;
            $insert_data_to_bot["drip_campaign_id"]=$sequence_id;
            $insert_data_to_bot['fb_page_id'] = $facebook_rx_fb_page_info[0]['page_id'];
            $insert_data['user_id'] = $this->user_id;        
            $insert_data_to_bot['user_id'] = $this->user_id;

            $insert_data['visual_flow_type'] = 'flow';
            $insert_data['visual_flow_campaign_id'] = $visual_flow_campaign_id;
            $insert_data_to_bot['visual_flow_type'] = 'flow';
            $insert_data_to_bot['visual_flow_campaign_id'] = $visual_flow_campaign_id;

            if($instagram_bot_addon == 1)
            {
                $media_type = 'ig';
                $insert_data['media_type'] = 'ig';
                $insert_data_to_bot['media_type'] = 'ig';
            }
            else
                $media_type = 'fb';

            if(isset($value['reply']))
                $reply_bot = $this->return_single_reply_array($value['reply'], $white_listed_domain_array, $page_table_id, $media_type, $visual_flow_campaign_id);

            $reply_bot_false = [];
            if(isset($value['reply_false']))
                $reply_bot_false = $this->return_single_reply_array($value['reply_false'], $white_listed_domain_array, $page_table_id, $media_type, $visual_flow_campaign_id);

            // pre($reply_bot); exit;

            $reply_bot_filtered = array();
            $m=0;
            foreach ($reply_bot as $value2) {
                $m++;
                if(isset($value2['otn_postback_table_id']))
                {
                    $otn_postback_ids_info[$value2['otn_sequence_unique_id']] = $value2['otn_postback_table_id'];
                    $postbacks_for_otn_single[$value2['postback_for_postbacktable']]['otn_postback_table_id'] = $value2['otn_postback_table_id'];
                    $postbacks_for_otn_single[$value2['postback_for_postbacktable']]['postback'] = $value2['postback_for_postbacktable'];
                    unset($value2['otn_sequence_unique_id']);
                    unset($value2['otn_postback_table_id']);
                    unset($value2['postback_for_postbacktable']);
                }

                $reply_bot_filtered[$m]['recipient'] = array('id'=>'replace_id');
                $reply_bot_filtered[$m]['message'] = $value2;

            }


            $reply_bot_false_filtered = array();
            $m=0;
            foreach ($reply_bot_false as $value2) {
                $m++;
                if(isset($value2['otn_postback_table_id']))
                {
                    $otn_postback_ids_info[$value2['otn_sequence_unique_id']] = $value2['otn_postback_table_id'];
                    $postbacks_for_otn_single[$value2['postback_for_postbacktable']]['otn_postback_table_id'] = $value2['otn_postback_table_id'];
                    $postbacks_for_otn_single[$value2['postback_for_postbacktable']]['postback'] = $value2['postback_for_postbacktable'];
                    unset($value2['otn_sequence_unique_id']);
                    unset($value2['otn_postback_table_id']);
                    unset($value2['postback_for_postbacktable']);
                }

                $reply_bot_false_filtered[$m]['recipient'] = array('id'=>'replace_id');
                $reply_bot_false_filtered[$m]['message'] = $value2;
            }

            $insert_data['template_jsoncode'] = json_encode($reply_bot_filtered,true);
            $insert_data_to_bot['message'] = json_encode($reply_bot_filtered,true);
            $insert_data_to_bot['message_condition_false'] = json_encode($reply_bot_false_filtered,true);
            if($conditions)  
                $insert_data_to_bot['conditions'] = $conditions;
            
            if(array_key_exists($insert_data_to_bot['postback_id'],$existing_bot_data))
                $insert_data_to_bot['id'] = $existing_bot_data[$insert_data_to_bot['postback_id']];
            
            if(!in_array($action_button_type, $action_buttons_array))
                $this->basic->insert_data('messenger_bot',$insert_data_to_bot);

            $messenger_bot_table_id = $this->db->insert_id();
            unset($insert_data_to_bot['id']);

            $messenger_bot_table_id_info[$insert_data['postback_id']]= $messenger_bot_table_id;
            $insert_data['messenger_bot_table_id'] = $messenger_bot_table_id;

            if(array_key_exists($insert_data['postback_id'],$existing_postback_data))
            {
                $insert_data['id'] = $existing_postback_data[$insert_data['postback_id']];
                $postback_table_idfor_engagement = $existing_postback_data[$insert_data['postback_id']];
            }

            if(!in_array($action_button_type, $action_buttons_array))
            {
                $this->basic->insert_data('messenger_bot_postback',$insert_data);
                $postback_table_idfor_engagement = $this->db->insert_id();
                $temp_postback_table_id = $postback_table_idfor_engagement;
                $postback_id_table_id_info[$insert_data['postback_id']]= $temp_postback_table_id;
            }
            unset($insert_data['id']);


            if($trigger_keywords != '')
            {
                $insert_data_to_bot['is_template'] = '0';
                $insert_data_to_bot['keywords'] = $trigger_keywords;
                $insert_data_to_bot['trigger_matching_type'] = $trigger_matching_type;
                $this->basic->insert_data('messenger_bot',$insert_data_to_bot);
            }


            $action_button_type = $value['action_button_type'] ?? '';
            if($action_button_type)
            {

                if($action_button_type == 'messenger_engagement_plugin')
                {
                    if($value['domain_name'])
                    {
                        array_push($this->need_to_whitelist_array, $value['domain_name']);
                    }

                    if($value['success_redirect_url'])
                    {
                        array_push($this->need_to_whitelist_array, $value['success_redirect_url']);
                    }

                    $insert_data2 = [];
                    $insert_data2['user_id'] = $this->user_id;
                    $insert_data2['visual_flow_campaign_id'] = $visual_flow_campaign_id;
                    $insert_data2['template_id'] = $postback_table_idfor_engagement;
                    $insert_data2['visual_flow_type'] = 'flow';

                    if($value['pluginType'] == 'checkbox_plugin'){
                        
                        if($edited != 1)
                        {
                            $domain_code = $this->_random_number_generator(8);
                            $insert_data2['domain_code'] = $domain_code;

                            $status=$this->_check_usage($module_id=213,$request=1);
                            if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
                            {
                                echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded for Messenger Bot - Enhancers : Engagement : Checkbox Plugin. you can no longer use this feature.")));
                                exit();
                            }
                            $this->_insert_usage_log($module_id=213,$request=1);
                        }
                        $table_name = "messenger_bot_engagement_checkbox";
                        $insert_data2['created_at'] = date("Y-m-d H:i:s");
                        $insert_data2['page_id'] = $page_table_id;
                        $insert_data2['domain_name'] = $value['domain_name'];
                        $insert_data2['btn_size'] = $value['btn_size'];
                        $insert_data2['skin'] = $value['skin'];
                        $insert_data2['center_align'] = $value['center_align'];
                        $insert_data2['button_click_success_message'] = $value['button_click_success_message'];
                        $insert_data2['validation_error'] = $value['validation_error'];
                        $insert_data2['label_ids'] = $value['engagement_label_ids'];
                        $insert_data2['reference'] = $value['reference'];
                        $insert_data2['language'] = $value['language'];
                        $insert_data2['add_button_with_message'] = $value['add_button_with_message'];
                        $insert_data2['button_with_message_content'] = $value['button_with_message_content'];
                        $insert_data2['success_redirect_url'] = $value['success_redirect_url'];
                        $insert_data2['redirect'] = $value['redirect']; 

                    }
                    else if($value['pluginType'] == 'send_to_messenger'){

                        if($edited != 1)
                        {
                            $domain_code = time().$this->user_id;
                            $insert_data2['domain_code'] = $domain_code;

                            $status=$this->_check_usage($module_id=214,$request=1);
                            if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
                            {
                                echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded for Messenger Bot - Enhancers : Engagement : Send to Messenger. you can no longer use this feature.")));
                                exit();
                            }
                            $this->_insert_usage_log($module_id=214,$request=1);
                        }
                        $table_name = "messenger_bot_engagement_send_to_msg";
                        $insert_data2['created_at'] = date("Y-m-d H:i:s");
                        $insert_data2['page_id'] = $page_table_id;
                        $insert_data2['domain_name'] = $value['domain_name'];
                        $insert_data2['btn_size'] = $value['btn_size'];
                        $insert_data2['skin'] = $value['skin'];
                        $insert_data2['button_click_success_message'] = $value['button_click_success_message'];
                        $insert_data2['label_ids'] = $value['engagement_label_ids'];
                        $insert_data2['cta_text_option'] = $value['cta_text_option'];
                        $insert_data2['reference'] = $value['reference'];
                        // echo $value['redirect']; exit;
                        if($value['redirect'] == 1)
                            $insert_data2['redirect'] = (string)$value['redirect'];
                        else
                            $insert_data2['redirect'] = '0';
                        $insert_data2['language'] = $value['language'];
                        $insert_data2['add_button_with_message'] = $value['add_button_with_message'];
                        $insert_data2['button_with_message_content'] = $value['button_with_message_content'];
                        $insert_data2['success_redirect_url'] = $value['success_redirect_url'];
                    } 
                    else if($value['pluginType'] == 'm_me_link'){

                        if($edited != 1)
                        {
                            $link_code = $this->_random_number_generator(8);
                            $insert_data2['link_code'] = $link_code;

                            $status=$this->_check_usage($module_id=215,$request=1);
                            if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
                            {
                                echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded for Messenger Bot - Enhancers : Engagement : m.me Links. you can no longer use this feature.")));
                                exit();
                            }
                            $this->_insert_usage_log($module_id=215,$request=1);
                        }
                        $table_name = "messenger_bot_engagement_mme";
                        $button_array = json_decode($value['button_with_message_content'],true);
                        $insert_data2['created_at'] = date("Y-m-d H:i:s");
                        $insert_data2['page_id'] = $page_table_id;
                        $insert_data2['btn_size'] = $value['btn_size'];
                        $insert_data2['new_button_bg_color'] = $button_array['success_button_bg_color'];
                        $insert_data2['new_button_bg_color_hover'] = $button_array['success_button_bg_color_hover'];
                        $insert_data2['new_button_color'] = $button_array['success_button_color'];
                        $insert_data2['new_button_color_hover'] = $button_array['success_button_color_hover'];
                        $insert_data2['new_button_display'] = $button_array['success_button'];
                        $insert_data2['label_ids'] = $value['engagement_label_ids'];
                        $insert_data2['reference'] = $value['reference'];

                    } 
                    else if($value['pluginType'] == 'customer_chat_plugin')
                    {

                        $domain_code = time().$this->user_id;
                        
                        if($edited != 1)
                        {
                            $insert_data2['domain_code'] = $domain_code;
                            $status=$this->_check_usage($module_id=217,$request=1);
                            if($status=="3")  //monthly limit is exceeded, can not create another campaign this month
                            {
                                echo json_encode(array("status" => "0", "message" =>$this->lang->line("limit has been exceeded for Messenger Bot - Enhancers : Engagement : Customer Chat Plugin. you can no longer use this feature.")));
                                exit();
                            }
                            $this->_insert_usage_log($module_id=217,$request=1);
                        }
                        $table_name = "messenger_bot_engagement_2way_chat_plugin";
                        $insert_data2['page_auto_id'] = $page_table_id;
                        $insert_data2['facebook_rx_fb_user_info_id'] = $facebook_rx_fb_user_info_id;
                        $insert_data2['domain_name'] = $value['domain_name'];
                        $insert_data2['language'] = $value['language'];
                        $insert_data2['minimized'] = $value['minimized'];
                        $insert_data2['logged_in'] = $value['logged_in'];
                        $insert_data2['logged_out'] = $value['logged_out'];
                        $insert_data2['color'] = $value['color'];
                        $insert_data2['label_ids'] = $value['engagement_label_ids'];
                        $insert_data2['reference'] = $value['reference'];
                        $insert_data2['delay'] = $value['delay'];
                        $insert_data2['donot_show_if_not_login'] = $value['donot_show_if_not_login'];
                        $insert_data2['add_date'] = date("Y-m-d H-i-s");

                        // wordpress and js file create for two way customer chat plugin
                        $chat_plugin_js=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/chat-js-base.txt',true);
                        $chat_plugin_js_new=str_replace("APP_ID",$fb_app_id, $chat_plugin_js);
                        $chat_plugin_js_new=str_replace("LOCALE",$value['language'], $chat_plugin_js_new);
                        $chat_plugin_js_new=str_replace("{DONOT_SHOW_IF_NOT_LOGIN}",$value['donot_show_if_not_login'], $chat_plugin_js_new);
                        file_put_contents('js/2waychat/plugin-'.$domain_code.'.js', $chat_plugin_js_new, LOCK_EX);

                        $name="easyembedchat";
                        $name2="EasyEmbedChat-".$this->user_id;
                        $chat_plugin_js2=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/chat-js-base2.txt',true);
                        $chat_plugin_js_new2=str_replace("APP_ID",$fb_app_id, $chat_plugin_js2);
                        $chat_plugin_js_new2=str_replace("PAGE_ID",$fb_page_id, $chat_plugin_js_new2);
                        $chat_plugin_js_new2=str_replace("LOCALE",$value['language'], $chat_plugin_js_new2);
                        $chat_plugin_js_new2=str_replace("GREETING_DIALOG_DISPLAY",$value['minimized'], $chat_plugin_js_new2);
                        $chat_plugin_js_new2=str_replace("REFERENCE",$value['reference'], $chat_plugin_js_new2);
                        $chat_plugin_js_new2=str_replace("{DONOT_SHOW_IF_NOT_LOGIN}",$value['donot_show_if_not_login'], $chat_plugin_js_new2);

                        $color_replace=$logged_in_replace=$logged_out_replace=$greeting_dialog_delay_replace="";
                        if($value['color']!="")      $color_replace=' theme_color="'.$value['color'].'"'; 
                        if($value['logged_in']!="")  $logged_in_replace=' logged_in_greeting="'.$value['logged_in'].'"'; 
                        if($value['logged_out']!="") $logged_out_replace=' logged_out_greeting="'.$value['logged_out'].'"';
                        if($value['delay']!="" && $value['delay']>0) $greeting_dialog_delay_replace=' greeting_dialog_delay="'.$value['delay'].'"';
                        $chat_plugin_js_new2=str_replace("{COLOR_PARAM}",$color_replace, $chat_plugin_js_new2);
                        $chat_plugin_js_new2=str_replace("{LOGGED_IN_PARAM}",$logged_in_replace, $chat_plugin_js_new2);
                        $chat_plugin_js_new2=str_replace("{LOGGED_OUT_PARAM}",$logged_out_replace, $chat_plugin_js_new2);
                        $chat_plugin_js_new2=str_replace("{GREETING_DIALOG_DELAY_PARAM}",$greeting_dialog_delay_replace, $chat_plugin_js_new2);

                        $wp_content=file_get_contents(APPPATH.'modules/messenger_bot_enhancers/fb-chat-wp.txt',true);
                        $wp_content=str_replace("LOAD_CHAT_CODE_HERE",$chat_plugin_js_new2, $wp_content);
                        // file_put_contents('download/'.$name.'.php', $wp_content, LOCK_EX);

                        if(!class_exists('ZipArchive'))
                        {
                           $download_url=base_url('messenger_bot_enhancers/zip_error');
                        }
                        else
                        {
                            $zip = new ZipArchive;
                            if ($zip->open('download/'.$name2.'.zip', ZipArchive::CREATE) === TRUE)
                            {
                                $zip->addFile($name.'/'.$name.'.php');
                                $zip->addFromString($name.'/'.$name.'.php', $wp_content);
                                $zip->close();
                            }
                            $download_url=base_url('download/'.$name2.'.zip');
                        }


                    }

                    if(isset($value['new_sequence_information']))
                    {
                        $this->new_sequence_information_array[$postback_id]['action_button_settings']['action_button_type'] = $value['pluginType'];

                        $existance = $this->basic->get_data($table_name,['where'=>['visual_flow_campaign_id'=>$visual_flow_campaign_id,'user_id'=>$this->user_id]],['id']);
                        if(!empty($existance))
                        {
                            $this->basic->update_data($table_name,['id'=>$existance[0]['id']],$insert_data2);
                            $this->new_sequence_information_array[$postback_id]['action_button_settings']['insert_table_id'] = $existance[0]['id'];
                        }
                        else
                        {
                            $this->basic->insert_data($table_name,$insert_data2);
                            $this->new_sequence_information_array[$postback_id]['action_button_settings']['insert_table_id'] = $this->db->insert_id();
                        }
                    }

                }
                else
                {
                    $insert_template = $this->return_static_array();
                    $insert_data_to_bot['postback_id'] = $insert_template[$action_button_type]['postback_id'];
                    $insert_data_to_bot['bot_name'] = $insert_template[$action_button_type]['bot_name'];
                    $insert_data_to_bot['keyword_type'] = $insert_template[$action_button_type]['keyword_type'];
                    $insert_data_to_bot['is_template'] = '0';
                    unset($insert_data_to_bot['keywords']);

                    $insert_data['bot_name'] = $insert_template[$action_button_type]['bot_name'];
                    $insert_data['template_name'] = $insert_template[$action_button_type]['bot_name'];
                    $insert_data['postback_id'] = $insert_template[$action_button_type]['postback_id'];
                    $insert_data['use_status'] = '0';

                    if($action_button_type == 'get-started' || $action_button_type == 'no match' || $action_button_type == 'STORY_MENTION' || $action_button_type == 'STORY_PRIVATE_REPLY' || $action_button_type == 'MESSAGE_UNSEND_PRIVATE_REPLY')
                    {
                        $this->basic->update_data('messenger_bot',['user_id'=>$this->user_id,'page_id'=>$insert_data_to_bot['page_id'],'keyword_type'=>$insert_data_to_bot['keyword_type']],$insert_data_to_bot);
                    }
                    else
                    {
                        $insert_data['template_for'] = $insert_template[$action_button_type]['template_for'];

                        $this->basic->update_data('messenger_bot',['user_id'=>$this->user_id,'page_id'=>$insert_data_to_bot['page_id'],'keyword_type'=>$insert_data_to_bot['keyword_type']],$insert_data_to_bot);
                        $this->basic->update_data('messenger_bot_postback',['user_id'=>$this->user_id,'page_id'=>$insert_data['page_id'],'template_for'=>$insert_data['template_for']],$insert_data);

                    }
                }
            }


            // update otn postback table 
            if(isset($postbacks_for_otn_single[$insert_data['postback_id']]))
            {
                $otn_postback_table_id = $postbacks_for_otn_single[$insert_data['postback_id']]['otn_postback_table_id'];
                $postback_table_id= $postback_id_table_id_info[$postbacks_for_otn_single[$insert_data['postback_id']]['postback']];
                $this->basic->update_data('otn_postback',array("id"=>$otn_postback_table_id),array("reply_postback_id"=>$postback_table_id));
            }
        }

        // Process new Messenger Sequence Campaign 
        foreach($this->new_sequence_information_array as $key_postback=>$new_sequence_campaign)
        {
            if($instagram_bot_addon == 1)
                $insert_info['media_type'] = 'ig';
            $insert_info['campaign_name']= $new_sequence_campaign['name'] ?? "" ;
            $insert_info['between_start']= $new_sequence_campaign['startingTime'] ?? "";
            $insert_info['between_end']= $new_sequence_campaign['closingTime'] ?? "";
            $insert_info['timezone']= $new_sequence_campaign['timezone'] ?? "";
            $insert_info['message_tag']= $new_sequence_campaign['messageTag'] ?? "";
            $insert_info['page_id']= $page_table_id;
            $insert_info['user_id']= $this->user_id;  
            $insert_info['created_at']= date("Y-m-d H:i:s");
            $insert_info['drip_type']= "custom";
            $insert_info['campaign_type']= "messenger";
            $insert_info['visual_flow_campaign_id']= $visual_flow_campaign_id;
            $insert_info['visual_flow_sequence_id']= $new_sequence_campaign['xitFbUniqueSequenceId'] ?? 0;

            if(isset($new_sequence_campaign['action_button_settings']))
            {
                if($new_sequence_campaign['action_button_settings']['action_button_type'] == 'get-started') $insert_info['drip_type'] = "default";
                else if($new_sequence_campaign['action_button_settings']['action_button_type'] == 'checkbox_plugin') $insert_info['drip_type'] = "messenger_bot_engagement_checkbox";
                else if($new_sequence_campaign['action_button_settings']['action_button_type'] == 'send_to_messenger') $insert_info['drip_type'] = "messenger_bot_engagement_send_to_msg";
                else if($new_sequence_campaign['action_button_settings']['action_button_type'] == 'm_me_link') $insert_info['drip_type'] = "messenger_bot_engagement_mme";
                else if($new_sequence_campaign['action_button_settings']['action_button_type'] == 'customer_chat_plugin') $insert_info['drip_type'] = "messenger_bot_engagement_2way_chat_plugin";

                if($new_sequence_campaign['action_button_settings']['action_button_type'] != 'get-started') $insert_info['engagement_table_id'] = $new_sequence_campaign['action_button_settings']['insert_table_id'];
            }


            $promotional_campaing= $new_sequence_campaign['promotional'] ?? array();

            $promotional_campaing_data_array=array();

            foreach($promotional_campaing as $promotional_campaing_single){
                $promotional_campaing_data_array[$promotional_campaing_single['time']] = $postback_id_table_id_info[$promotional_campaing_single['postback_id']];
            }


            $non_promotional_campaing= $new_sequence_campaign['non_promotional'] ?? array();

            $non_promotional_campaing_data_array=array();

            foreach($non_promotional_campaing as $non_promotional_campaing_single){
                $non_promotional_campaing_data_array[$non_promotional_campaing_single['time']] = $postback_id_table_id_info[$non_promotional_campaing_single['postback_id']];
            }

            $insert_info['message_content_hourly']= json_encode($promotional_campaing_data_array);
            $insert_info['message_content']= json_encode($non_promotional_campaing_data_array);

            $where_array['where']=array('visual_flow_sequence_id'=>$new_sequence_campaign['xitFbUniqueSequenceId'],'page_id'=>$page_table_id);
            $sequence_exist= $this->basic->get_data('messenger_bot_drip_campaign',$where_array,'id');

            if(empty($sequence_exist)){

                $status=$this->_check_usage($module_id=219,$request=1);
                if($status=="3") 
                {
                    echo json_encode(array("status" => "0", "message" =>$this->lang->line("You can not create more sequence message campaign. Module limit exceeded.")));
                    exit();
                }
                $this->_insert_usage_log($module_id=219,$request=1);

                $this->basic->insert_data('messenger_bot_drip_campaign',$insert_info);
                $new_sequence_id=$this->db->insert_id();
            }
            else{
                $update_data_new_sequence=array("message_content"=>$insert_info['message_content'],"message_content_hourly"=>$insert_info['message_content_hourly']);
                $new_sequence_id=$sequence_exist[0]['id'] ?? "";
                $this->basic->update_data('messenger_bot_drip_campaign',array("id"=>$new_sequence_id),$update_data_new_sequence);                
            }

            // Update the main postback where this squence need to be assaigned. 
            if(isset($postback_id_table_id_info[$key_postback]) || isset($messenger_bot_table_id_info[$key_postback]))
            {
                $postback_table_id= $postback_id_table_id_info[$key_postback] ?? '';
                $messenger_bot_table_id= $messenger_bot_table_id_info[$key_postback] ?? '';

                $this->basic->update_data('messenger_bot_postback',array("id"=>$postback_table_id),array("drip_campaign_id"=>$new_sequence_id));    
                $this->basic->update_data('messenger_bot',array("id"=>$messenger_bot_table_id),array("drip_campaign_id"=>$new_sequence_id));
            }
            else
            {
                if(isset($otn_postback_ids_info[$key_postback]))
                {
                    $otn_postback_table_id = $otn_postback_ids_info[$key_postback];
                    $this->basic->update_data('otn_postback',array("id"=>$otn_postback_table_id),array("drip_campaign_id"=>$new_sequence_id));
                }
                
            }

          

        }

        // delete userinputflow campaign/questions/answers
        if($this->addon_exist('custom_field_manager'))
        {
            if(empty($this->user_input_flowcampaigns_unique_ids))
                $existing_user_input_flow_campaigins = $this->basic->get_data('user_input_flow_campaign',['where'=>['visual_flow_campaign_id'=>$visual_flow_campaign_id]],['id']);
            else
                $existing_user_input_flow_campaigins = $this->basic->get_data('user_input_flow_campaign',['where'=>['visual_flow_campaign_id'=>$visual_flow_campaign_id],'where_not_in'=>['unique_id'=>$this->user_input_flowcampaigns_unique_ids]],['id']);

            foreach($existing_user_input_flow_campaigins as $single_flow_campaign)
            {
                $user_input_flow_campaign_id = $single_flow_campaign['id'];
                $this->basic->delete_data('user_input_flow_campaign',['id'=>$user_input_flow_campaign_id]);

                $this->db->where_in('flow_campaign_id',$user_input_flow_campaign_id);
                $this->db->delete('user_input_flow_questions'); 

                $this->db->where_in('flow_campaign_id',$user_input_flow_campaign_id);
                $this->db->delete('user_input_flow_questions_answer');
            }
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Creating template was unsuccessful. Database error occured during creating template.")));
        else
        {
            // domain white list section start
            $this->load->library("fb_rx_login"); 
            $domain_whitelist_insert_data = array();
            foreach($this->need_to_whitelist_array as $value)
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
            
            if($builder_table_id != 0)
                echo json_encode(array("status" => "1", "message" =>$this->lang->line("Template has been updated successfully.")));
            else
                echo json_encode(array("status" => "1", "message" =>$this->lang->line("New template has been stored successfully.")));
        }
    }


    public function return_single_reply_array($reply=[], $white_listed_domain_array, $page_table_id, $media_type='fb', $visual_flow_campaign_id)
    {
        foreach ($reply as $single_reply_key => $single_reply_value) 
        {
            if($single_reply_value['is_typing_display'] == 'true') 
                $reply_bot[$single_reply_key]['typing_on_settings'] = 'on';
            else 
                $reply_bot[$single_reply_key]['typing_on_settings'] = 'off';

            if($single_reply_value['delay_time'] != '')
                $reply_bot[$single_reply_key]['delay_in_reply'] = $single_reply_value['delay_time'];
            else
                $reply_bot[$single_reply_key]['delay_in_reply'] = 0;

            $reply_bot[$single_reply_key]['text_reply_unique_id'] = $single_reply_value['text_reply_unique_id'] ?? '';

            if($single_reply_value['reply_type'] == 'textInput')
            {
                if(!empty($single_reply_value['buttons']))
                {
                    $reply_bot[$single_reply_key]['template_type'] = 'text_with_buttons';
                    $reply_bot[$single_reply_key]['attachment']['type'] = 'template';
                    $reply_bot[$single_reply_key]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$single_reply_key]['attachment']['payload']['text'] = $single_reply_value['reply_text'] ?? '';
                    
                    foreach($single_reply_value['buttons'] as $single_button_key => $single_button_info)
                    {
                        $button_text = $single_button_info['button_text'] ?? '';

                        if($single_button_info['button_type'] == 'post_back' || $single_button_info['button_type'] == 'new_post_back')
                        {
                            $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['type'] = 'postback';
                            $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['payload'] = $single_button_info['value'] ?? '';
                            $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['title'] = $button_text;
                        }
                        else if($single_button_info['button_type'] == 'phone_number')
                        {
                            $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['type'] = 'phone_number';
                            $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['payload'] = $single_button_info['value'] ?? '';
                            $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['title'] = $button_text;
                        }
                        else if(strpos($single_button_info['button_type'],'web_url') !== FALSE)
                        {
                            if($single_button_info['value']!='')
                                $button_web_url=add_query_string_to_url($single_button_info['value'],"subscriber_id","#SUBSCRIBER_ID_REPLACE#");
                            else
                                $button_web_url = '';

                            $button_type_array = explode('_', $single_button_info['button_type']);
                            if(isset($button_type_array[2]))
                            {
                                $button_extension = trim($button_type_array[2],'_'); 
                                array_pop($button_type_array);
                            }            
                            else $button_extension = '';
                            $button_type = implode('_', $button_type_array);

                            if($button_text != '' && $button_type != '' && ($button_web_url != '' || $button_extension != ''))
                            {
                                $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['type'] = 'web_url';

                                if($button_extension != '' && $button_extension == 'birthday'){
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['webview_height_ratio'] = 'full';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_birthdate');
                                }
                                else if($button_extension != '' && $button_extension == 'email'){
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['webview_height_ratio'] = 'full';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_email');
                                }
                                else if($button_extension != '' && $button_extension == 'phone'){
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['webview_height_ratio'] = 'full';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_phone');
                                }
                                else if($button_extension != '' && $button_extension == 'location'){
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['webview_height_ratio'] = 'full';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_location');
                                }
                                else
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['url'] = $button_web_url;
                                $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                                {
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['buttons'][$single_button_key]['webview_height_ratio'] = $button_extension;
                                }

                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    array_push($this->need_to_whitelist_array, $button_web_url);
                                }
                            }
                        }
                    }
                }
                else
                {
                    $reply_bot[$single_reply_key]['template_type'] = 'text';
                    $reply_bot[$single_reply_key]['text'] = $single_reply_value['reply_text'] ?? '';
                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }

            }

            else if($single_reply_value['reply_type'] == 'imageInput')
            {
                $image_url = $single_reply_value['url'] ?? '';
                if($image_url != '')
                {
                    $reply_bot[$single_reply_key]['template_type'] = 'image';
                    $reply_bot[$single_reply_key]['attachment']['type'] = 'image';
                    $reply_bot[$single_reply_key]['attachment']['payload']['url'] = $image_url;
                    $reply_bot[$single_reply_key]['attachment']['payload']['is_reusable'] = true;                    
                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }
            }

            else if($single_reply_value['reply_type'] == 'audioInput')
            {
                $audio_file_url = $single_reply_value['url'] ?? '';
                if($audio_file_url != '')
                {
                    $reply_bot[$single_reply_key]['template_type'] = 'audio';
                    $reply_bot[$single_reply_key]['attachment']['type'] = 'audio';
                    $reply_bot[$single_reply_key]['attachment']['payload']['url'] = $audio_file_url;
                    $reply_bot[$single_reply_key]['attachment']['payload']['is_reusable'] = true;
                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }
                
            }

            else if($single_reply_value['reply_type'] == 'videoInput')
            {
                $video_file_url = $single_reply_value['url'] ?? '';
                if($video_file_url != '')
                {
                    $reply_bot[$single_reply_key]['template_type'] = 'video';
                    $reply_bot[$single_reply_key]['attachment']['type'] = 'video';
                    $reply_bot[$single_reply_key]['attachment']['payload']['url'] = $video_file_url;
                    $reply_bot[$single_reply_key]['attachment']['payload']['is_reusable'] = true;                    
                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }
            }

            else if($single_reply_value['reply_type'] == 'fileInput')
            {
                $file_type_url = $single_reply_value['url'] ?? '';
                if($file_type_url != '')
                {       
                    $reply_bot[$single_reply_key]['template_type'] = 'file';             
                    $reply_bot[$single_reply_key]['attachment']['type'] = 'file';
                    $reply_bot[$single_reply_key]['attachment']['payload']['url'] = $file_type_url;
                    $reply_bot[$single_reply_key]['attachment']['payload']['is_reusable'] = true;
                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }
            }

            else if($single_reply_value['reply_type'] == 'otnInput')
            {
                $otn_title = $single_reply_value['reply_text'];
                $otn_postback = $single_reply_value['otn_postback_id'];
                $otn_unique_id = $single_reply_value['text_reply_unique_id'] ?? '';

                if($otn_postback == 'newOtn')
                {
                    $otn_campaign_info = $single_reply_value['otn_campaign_info'];
                    $otn_unique_id = $otn_campaign_info['unique_id'] ?? '';
                    if(isset($otn_campaign_info['new_sequence_information']) && !empty($otn_campaign_info['new_sequence_information']))
                        $this->new_sequence_information_array[$otn_campaign_info['new_sequence_information']['xitFbUniqueSequenceId']] = $otn_campaign_info['new_sequence_information'];

                    $insert_data = [];
                    $insert_data['template_name'] = $otn_campaign_info['campaign_name'] ?? '';
                    $insert_data['user_id'] = $this->user_id;
                    $insert_data['page_id'] = $page_table_id;
                    $insert_data['label_id'] = $otn_campaign_info['label_ids'] ?? '';
                    $insert_data['flow_type'] = 'flow';
                    // $insert_data['otn_postback_id'] = $otn_campaign_info['new_sequence_information']['xitFbUniqueSequenceId'] ?? '';
                    $insert_data['otn_postback_id'] = $otn_campaign_info['postback_id'] ?? '';
                    $insert_data['visual_flow_campaign_id'] = $visual_flow_campaign_id;
                    $this->basic->insert_data('otn_postback',$insert_data);
                    $otn_postback = $this->db->insert_id();

                    $reply_bot[$single_reply_key]['postback_for_postbacktable'] = $otn_campaign_info['postback_id'];
                    $reply_bot[$single_reply_key]['otn_postback_table_id'] = $otn_postback;
                    $reply_bot[$single_reply_key]['otn_sequence_unique_id'] = $otn_campaign_info['new_sequence_information']['xitFbUniqueSequenceId'] ?? '';

                }

                if($otn_title != '' && $otn_postback != '')
                {       
                    $reply_bot[$single_reply_key]['template_type'] = 'One_Time_Notification';             
                    $reply_bot[$single_reply_key]['attachment']['type'] = 'template';
                    $reply_bot[$single_reply_key]['attachment']['payload']['template_type'] = "one_time_notif_req";
                    $reply_bot[$single_reply_key]['attachment']['payload']['title'] = $otn_title;
                    $reply_bot[$single_reply_key]['attachment']['payload']['payload'] = $otn_postback."::".$otn_unique_id;
                }
            }

            else if($single_reply_value['reply_type'] == 'facebookMediaInput')
            {
                $media_input = $single_reply_value['media_url'];
                if($media_input != '')
                {
                    $reply_bot[$single_reply_key]['template_type'] = 'media';
                    $reply_bot[$single_reply_key]['attachment']['type'] = 'template';
                    $reply_bot[$single_reply_key]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                foreach ($single_reply_value['buttons'] as $media_reply_button_key => $media_reply_button_info)
                { 
                    $button_text = $media_reply_button_info['button_text'] ?? '';
                    $button_type = $media_reply_button_info['button_type'] ?? '';

                    if($button_type == 'post_back' || $button_type == 'new_post_back')
                    {
                        $button_postback_id = $media_reply_button_info['value'] ?? '';
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['type'] = 'postback';
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['payload'] = $button_postback_id;
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['title'] = $button_text;
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_web_url = $media_reply_button_info['value'] ?? '';
                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                            $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

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
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['webview_height_ratio'] = 'full';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['webview_height_ratio'] = 'full';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['webview_height_ratio'] = 'full';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['webview_height_ratio'] = 'full';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['url'] = $button_web_url;
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['webview_height_ratio'] = $button_extension;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        $button_call_us = $media_reply_button_info['value'] ?? '';
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['type'] = 'phone_number';
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['payload'] = $button_call_us;
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$media_reply_button_key]['title'] = $button_text;
                        }
                    }
                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }
            }

            else if($single_reply_value['reply_type'] == 'genericTemplateInput')
            {
                $generic_template_title = $single_reply_value['generic_template']['carousel_item_title'] ?? '';
                $generic_template_subtitle = $single_reply_value['generic_template']['carousel_item_sub_title'] ?? '';
                $generic_template_image = $single_reply_value['generic_template']['image_url'] ?? '';
                $generic_template_image_destination_link = $single_reply_value['generic_template']['carousel_item_image_destination'] ?? '';

                if($generic_template_title != '')
                {
                    $reply_bot[$single_reply_key]['template_type'] = 'generic_template';
                    $reply_bot[$single_reply_key]['attachment']['type'] = 'template';
                    $reply_bot[$single_reply_key]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                   
                }

                if($generic_template_subtitle != '')
                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['image_url'] =$generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    // if(function_exists('getimagesize') && $generic_template_image!='') 
                    // {
                    //     $full_generic_template_image = base_url().$generic_template_image;
                    //     list($width, $height, $type, $attr) = getimagesize($full_generic_template_image);
                    //     if($width==$height)
                    //         $reply_bot[$single_reply_key]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    // }
                    $reply_bot[$single_reply_key]['attachment']['payload']['image_aspect_ratio'] = 'square';

                }
                

                foreach ($single_reply_value['buttons'] as $generic_button_index => $generic_button_info)
                { 
                    $button_type = $generic_button_info['button_type'] ?? '';
                    $button_text = $generic_button_info['button_text'] ?? '';
                    
                    if($button_type == 'post_back' || $button_type == 'new_post_back')
                    {
                        $button_postback_id = $generic_button_info['value'] ?? '';
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['type'] = 'postback';
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['payload'] = $button_postback_id;
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['title'] = $button_text;
                        }
                    }
                    if(strpos($button_type,'web_url') !== FALSE)
                    {
                        $button_web_url = $generic_button_info['value'] ?? '';
                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                            $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

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
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['type'] = 'web_url';
                            if($button_extension != '' && $button_extension == 'birthday'){                                
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['webview_height_ratio'] = 'full';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_birthdate');
                            }
                            else if($button_extension != '' && $button_extension == 'email'){                                
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['webview_height_ratio'] = 'full';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_email');
                            }
                            else if($button_extension != '' && $button_extension == 'phone'){                                
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['webview_height_ratio'] = 'full';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_phone');
                            }
                            else if($button_extension != '' && $button_extension == 'location'){                                
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['webview_height_ratio'] = 'full';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                $button_web_url = base_url('webview_builder/get_location');
                            }
                            else
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['url'] = $button_web_url;
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                            {
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['messenger_extensions'] = 'true';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['webview_height_ratio'] = $button_extension;
                            }

                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                array_push($this->need_to_whitelist_array, $button_web_url);
                            }

                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        $button_call_us = $generic_button_info['value'] ?? '';
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['type'] = 'phone_number';
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['payload'] = $button_call_us;
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][0]['buttons'][$generic_button_index]['title'] = $button_text;
                        }
                    }
                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }

            }

            else if($single_reply_value['reply_type'] == 'carouselInput')
            {
                $reply_bot[$single_reply_key]['template_type'] = 'carousel';
                $reply_bot[$single_reply_key]['attachment']['type'] = 'template';
                $reply_bot[$single_reply_key]['attachment']['payload']['template_type'] = 'generic';


                foreach ($single_reply_value['carousel_items'] as $carousel_item_index => $carousel_item_info) 
                {   
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['title'] = $carousel_item_info['carousel_item_title'];
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['subtitle'] = $carousel_item_info['carousel_item_sub_title'];
                    $carousel_image = $carousel_item_info['image_url'] ?? '';

                    if(isset($carousel_image) && $carousel_image!="")
                    {
                        $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['image_url'] = $carousel_image;                    
                        $carousel_image_destination_link = $carousel_item_info['carousel_item_image_destination'] ?? '';
                        if($carousel_image_destination_link!="") 
                        {
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['default_action']['type'] = 'web_url';
                            $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['default_action']['url'] = $carousel_image_destination_link;
                        }

                        // if(function_exists('getimagesize') && $carousel_image!='') 
                        // {
                        //     $full_carousel_image = base_url().$carousel_image;
                        //     list($width, $height, $type, $attr) = getimagesize($full_carousel_image);
                        //     if($width==$height)
                        //         $reply_bot[$single_reply_key]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        // }
                        $reply_bot[$single_reply_key]['attachment']['payload']['image_aspect_ratio'] = 'square';

                    }
                    
                    foreach ($carousel_item_info['button_info'] as $carousel_item_button_index => $carousel_item_button_info)
                    { 
                        $button_type = $carousel_item_button_info['button_type'];
                        $button_text = $carousel_item_button_info['button_text'];
                        
                        if($button_type == 'post_back' || $button_type == 'new_post_back')
                        {
                            $button_postback_id = $carousel_item_button_info['value'] ?? '';
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['type'] = 'postback';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['payload'] = $button_postback_id;
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['title'] = $button_text;
                            }
                        }

                        if(strpos($button_type,'web_url') !== FALSE)
                        {
                            $button_web_url = $carousel_item_button_info['value'] ?? '';
                            //add an extra query parameter for tracking the subscriber to whom send 
                            if($button_web_url!='')
                              $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

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
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['type'] = 'web_url';
                                if($button_extension != '' && $button_extension == 'birthday'){
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['webview_height_ratio'] = 'full';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_birthdate');
                                }
                                else if($button_extension != '' && $button_extension == 'email'){
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['webview_height_ratio'] = 'full';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['url'] = base_url('webview_builder/get_email?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_email');
                                }
                                else if($button_extension != '' && $button_extension == 'phone'){
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['webview_height_ratio'] = 'full';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['url'] = base_url('webview_builder/get_phone?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_phone');
                                }
                                else if($button_extension != '' && $button_extension == 'location'){
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['webview_height_ratio'] = 'full';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['url'] = base_url('webview_builder/get_location?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                    $button_web_url = base_url('webview_builder/get_location');
                                }
                                else
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['url'] = $button_web_url;
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday' && $button_extension !="email" && $button_extension !="phone" && $button_extension !="location")
                                {
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['messenger_extensions'] = 'true';
                                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['webview_height_ratio'] = $button_extension;
                                }

                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    array_push($this->need_to_whitelist_array, $button_web_url);
                                }

                            }
                        }

                        if($button_type == 'phone_number')
                        {
                            $button_call_us = $carousel_item_button_info['value'] ?? '';

                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['type'] = 'phone_number';
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['payload'] = $button_call_us;
                                $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$carousel_item_index]['buttons'][$carousel_item_button_index]['title'] = $button_text;
                            }
                        }
                    }

                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }

            }

            else if($single_reply_value['reply_type'] == 'ecommerceInput')
            {
                $this->load->helper('ecommerce_helper');
                $currency_icons = $this->currency_icon();

                $reply_bot[$single_reply_key]['template_type'] = 'Ecommerce';
                $reply_bot[$single_reply_key]['attachment']['type'] = 'template';
                $reply_bot[$single_reply_key]['attachment']['payload']['template_type'] = 'generic';

                $buy_now_text = $single_reply_value['buy_now_button_text'];
                $products_array = $single_reply_value['product_ids'];

                foreach($products_array as $index_variable => $product_id)
                {
                    $product_data = $this->basic->get_data('ecommerce_product',['where'=>['id'=>$product_id,'user_id'=>$this->user_id]],['store_id','product_name','original_price','sell_price','thumbnail','id','woocommerce_product_id']);
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


                    $thumbnail = ($product_data[0]['thumbnail']!='') ? base_url('upload/ecommerce/'.$product_data[0]['thumbnail']) : base_url('assets/img/products/product-1.jpg');
                    if(isset($product_data[0]["woocommerce_product_id"]) && !is_null($product_data[0]["woocommerce_product_id"]) && $product_data[0]['thumbnail']!='')
                    $thumbnail = $product_data[0]['thumbnail'];


                    $reply_bot[$single_reply_key]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    $buy_now_url = base_url('ecommerce/product/').$product_id;
                    $buy_now_url = add_query_string_to_url($buy_now_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    if(!in_array($buy_now_url, $white_listed_domain_array))
                    {
                        array_push($this->need_to_whitelist_array, $buy_now_url);
                    }

                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['title'] = $title;
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['subtitle'] = $subtitle;
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['image_url'] = $thumbnail;
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['default_action']['type'] = 'web_url';
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['default_action']['url'] = $buy_now_url;

                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['type'] = 'web_url';
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['messenger_extensions'] = 'true';
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['webview_height_ratio'] = 'full';
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['url'] = $buy_now_url;
                    $reply_bot[$single_reply_key]['attachment']['payload']['elements'][$index_variable]['buttons'][$index_variable]['title'] = $buy_now_text;
                }

                if(!empty($single_reply_value['quick_replies']))
                {
                    $quick_replies = $this->generate_quick_replies($single_reply_value['quick_replies']);
                    $reply_bot[$single_reply_key]['quick_replies'] = $quick_replies;
                }
            }

            else if($single_reply_value['reply_type'] == 'userInputFlowInput')
            {
                $flow_campaign_id = $single_reply_value['user_input_flow_id'] ?? '';
                // $flowcampaign_unique_id = $single_reply_value['text_reply_unique_id'] ?? '';
                if($flow_campaign_id == 'new')
                {
                    $last_question_ids = [];
                    $new_question_ids = [];
                    $question_unique_ids = [];
                    $unique_id = $single_reply_value['user_input_flow_campaign_info']['unique_id'] ?? '';
                    array_push($this->user_input_flowcampaigns_unique_ids, $unique_id);
                    $flow_campaigns = $this->basic->get_data('user_input_flow_campaign',['where'=>['user_id'=>$this->user_id,'unique_id'=>$unique_id]]);
                    $insert_update_data = [
                      'user_id' => $this->user_id,
                      'flow_name' => $single_reply_value['user_input_flow_campaign_info']['flow_name'] ?? '',
                      'page_table_id' => $page_table_id,
                      'postback_id' => $single_reply_value['user_input_flow_campaign_info']['postback_id'] ?? '',
                      'media_type' => $media_type,
                      'unique_id' => $unique_id,
                      'visual_flow_type' => 'flow',
                      'visual_flow_campaign_id' => $visual_flow_campaign_id
                    ];
                    if(!empty($flow_campaigns))
                    {
                        $flow_campaign_id = $flow_campaigns[0]['id'] ?? 0;
                        $this->basic->update_data('user_input_flow_campaign',['id'=>$flow_campaign_id],$insert_update_data);

                        $flow_questions = $this->basic->get_data('user_input_flow_questions',['where'=>['flow_campaign_id'=>$flow_campaign_id,'user_id'=>$this->user_id]],['id','unique_id']);
                        foreach($flow_questions as $question_ids)
                        {
                            array_push($last_question_ids, $question_ids['id']);
                            $question_unique_ids[$question_ids['unique_id']] = $question_ids['id'];
                        }
                    }
                    else
                    {
                        $this->basic->insert_data('user_input_flow_campaign',$insert_update_data);
                        $flow_campaign_id = $this->db->insert_id();
                    }

                    $i=0;
                    foreach($single_reply_value['user_input_flow_campaign_info']['questions'] as $question_insert)
                    {
                        $i++;
                        if($question_insert['type'] == 'keyboard') $question_insert['type'] = 'keyboard input';
                        else $question_insert['type'] = 'quick replies';
                        $question_insert['flow_campaign_id'] = $flow_campaign_id;
                        $question_insert['serial_no'] = $i;
                        $question_insert['user_id'] = $this->user_id;
                        if(array_key_exists($question_insert['unique_id'],$question_unique_ids))
                        {
                            array_push($new_question_ids, $question_unique_ids[$question_insert['unique_id']]);
                            $this->basic->update_data('user_input_flow_questions',['id'=>$question_unique_ids[$question_insert['unique_id']]],$question_insert);
                        }
                        else
                        {
                            $this->basic->insert_data('user_input_flow_questions',$question_insert);
                        }
                    }

                    $need_to_delete_ids = array_diff($last_question_ids, $new_question_ids);
                    if(!empty($need_to_delete_ids))
                    {
                      $this->db->where_in('id',$need_to_delete_ids);
                      $this->db->delete('user_input_flow_questions'); 

                      $this->db->where_in('question_id',$need_to_delete_ids);
                      $this->db->delete('user_input_flow_questions_answer');
                    }

                }
                if($flow_campaign_id != '')
                {
                    $reply_bot[$single_reply_key]['template_type'] = 'User_Input_Flow';
                    $reply_bot[$single_reply_key]['flow_campaign_id'] = $flow_campaign_id;
                    
                }
            }

        }

        return $reply_bot;
    }


    public function return_static_array()
    {
        $insert_template['UNSUBSCRIBE_QUICK_BOXER']['postback_id'] = 'UNSUBSCRIBE_QUICK_BOXER';
        $insert_template['UNSUBSCRIBE_QUICK_BOXER']['bot_name'] = 'UNSUBSCRIBE BOT';
        $insert_template['UNSUBSCRIBE_QUICK_BOXER']['keyword_type'] = 'post-back';
        $insert_template['UNSUBSCRIBE_QUICK_BOXER']['template_for'] = 'unsubscribe';

        $insert_template['RESUBSCRIBE_QUICK_BOXER']['postback_id'] = 'RESUBSCRIBE_QUICK_BOXER';
        $insert_template['RESUBSCRIBE_QUICK_BOXER']['bot_name'] = 'RESUBSCRIBE BOT';
        $insert_template['RESUBSCRIBE_QUICK_BOXER']['keyword_type'] = 'post-back';
        $insert_template['RESUBSCRIBE_QUICK_BOXER']['template_for'] = 'resubscribe';

        $insert_template['YES_START_CHAT_WITH_BOT']['postback_id'] = 'YES_START_CHAT_WITH_BOT';
        $insert_template['YES_START_CHAT_WITH_BOT']['bot_name'] = 'CHAT WITH BOT';
        $insert_template['YES_START_CHAT_WITH_BOT']['keyword_type'] = 'post-back';
        $insert_template['YES_START_CHAT_WITH_BOT']['template_for'] = 'chat-with-bot';

        $insert_template['YES_START_CHAT_WITH_HUMAN']['postback_id'] = 'YES_START_CHAT_WITH_HUMAN';
        $insert_template['YES_START_CHAT_WITH_HUMAN']['bot_name'] = 'CHAT WITH HUMAN';
        $insert_template['YES_START_CHAT_WITH_HUMAN']['keyword_type'] = 'post-back';
        $insert_template['YES_START_CHAT_WITH_HUMAN']['template_for'] = 'chat-with-human';

        $insert_template['QUICK_REPLY_BIRTHDAY_REPLY_BOT']['postback_id'] = 'QUICK_REPLY_BIRTHDAY_REPLY_BOT';
        $insert_template['QUICK_REPLY_BIRTHDAY_REPLY_BOT']['bot_name'] = 'QUICK REPLY BIRTHDAY REPLY';
        $insert_template['QUICK_REPLY_BIRTHDAY_REPLY_BOT']['keyword_type'] = 'birthday-quick-reply';
        $insert_template['QUICK_REPLY_BIRTHDAY_REPLY_BOT']['template_for'] = 'birthday-quick-reply';

        $insert_template['QUICK_REPLY_LOCATION_REPLY_BOT']['postback_id'] = 'QUICK_REPLY_LOCATION_REPLY_BOT';
        $insert_template['QUICK_REPLY_LOCATION_REPLY_BOT']['bot_name'] = 'QUICK REPLY LOCATION REPLY';
        $insert_template['QUICK_REPLY_LOCATION_REPLY_BOT']['keyword_type'] = 'location-quick-reply';
        $insert_template['QUICK_REPLY_LOCATION_REPLY_BOT']['template_for'] = 'location-quick-reply';

        $insert_template['QUICK_REPLY_PHONE_REPLY_BOT']['postback_id'] = 'QUICK_REPLY_PHONE_REPLY_BOT';
        $insert_template['QUICK_REPLY_PHONE_REPLY_BOT']['bot_name'] = 'QUICK REPLY PHONE REPLY';
        $insert_template['QUICK_REPLY_PHONE_REPLY_BOT']['keyword_type'] = 'phone-quick-reply';
        $insert_template['QUICK_REPLY_PHONE_REPLY_BOT']['template_for'] = 'phone-quick-reply';

        $insert_template['QUICK_REPLY_EMAIL_REPLY_BOT']['postback_id'] = 'QUICK_REPLY_EMAIL_REPLY_BOT';
        $insert_template['QUICK_REPLY_EMAIL_REPLY_BOT']['bot_name'] = 'QUICK REPLY EMAIL REPLY';
        $insert_template['QUICK_REPLY_EMAIL_REPLY_BOT']['keyword_type'] = 'email-quick-reply';
        $insert_template['QUICK_REPLY_EMAIL_REPLY_BOT']['template_for'] = 'email-quick-reply';

        $insert_template['get-started']['postback_id'] = '';
        $insert_template['get-started']['bot_name'] = 'GET STARTED';
        $insert_template['get-started']['keyword_type'] = 'get-started';
        $insert_template['get-started']['template_for'] = '';

        $insert_template['no match']['postback_id'] = '';
        $insert_template['no match']['bot_name'] = 'NO MATCH FOUND';
        $insert_template['no match']['keyword_type'] = 'no match';
        $insert_template['no match']['template_for'] = '';

        $insert_template['STORY_MENTION']['postback_id'] = 'STORY_MENTION';
        $insert_template['STORY_MENTION']['bot_name'] = 'STORY MENTION';
        $insert_template['STORY_MENTION']['keyword_type'] = 'story-mention';
        $insert_template['STORY_MENTION']['template_for'] = '';

        $insert_template['STORY_PRIVATE_REPLY']['postback_id'] = 'STORY_PRIVATE_REPLY';
        $insert_template['STORY_PRIVATE_REPLY']['bot_name'] = 'STORY PRIVATE REPLY';
        $insert_template['STORY_PRIVATE_REPLY']['keyword_type'] = 'story-private-reply';
        $insert_template['STORY_PRIVATE_REPLY']['template_for'] = '';

        $insert_template['MESSAGE_UNSEND_PRIVATE_REPLY']['postback_id'] = 'MESSAGE_UNSEND_PRIVATE_REPLY';
        $insert_template['MESSAGE_UNSEND_PRIVATE_REPLY']['bot_name'] = 'MESSAGE UNSEND PRIVATE REPLY';
        $insert_template['MESSAGE_UNSEND_PRIVATE_REPLY']['keyword_type'] = 'message-unsend-private-reply';
        $insert_template['MESSAGE_UNSEND_PRIVATE_REPLY']['template_for'] = '';

        return $insert_template;
    }


    public function generate_quick_replies($quick_replies_array=[])
    {
        $reply_bot = [];
        foreach($quick_replies_array as $quick_reply_key => $quick_reply_info)
        { 
            $button_text = $quick_reply_info['button_text'] ?? '';
            $button_type = $quick_reply_info['button_type'] ?? '';
            $quick_reply_unique_id = $quick_reply_info['unique_id'] ?? '';

            if($button_type=='postback' || $button_type=='newPostback')
            {
                $button_postback_id = $quick_reply_info['value'] ?? '';
                if($button_text != '' && $button_postback_id != '')
                {
                    $reply_bot[$quick_reply_key]['content_type'] = 'text';
                    $reply_bot[$quick_reply_key]['payload'] = $button_postback_id;
                    $reply_bot[$quick_reply_key]['title'] = $button_text; 
                }                    
            }
            if($button_type=='phone')
            {
                $reply_bot[$quick_reply_key]['content_type'] = 'user_phone_number';
                $reply_bot[$quick_reply_key]['unique_id'] = $quick_reply_unique_id;
            }
            if($button_type=='email')
            {
                $reply_bot[$quick_reply_key]['content_type'] = 'user_email';
                $reply_bot[$quick_reply_key]['unique_id'] = $quick_reply_unique_id;
            }
            if($button_type=='location')
            {
                $reply_bot[$quick_reply_key]['content_type'] = 'location';
                $reply_bot[$quick_reply_key]['unique_id'] = $quick_reply_unique_id;
            }

        }
        return $reply_bot;
    }


    
    public function flow_builder_upload_media()
    {
        if (! headers_sent()) {
            header('Content-Type: application/json');
        }

        $this->ajax_check();

        // Determines upload path
        $upload_dir = APPPATH . '../upload/flow_builder/';

        if (! file_exists($upload_dir)) {
            mkdir($upload_dir, 0755);
        }

        // Starts uploading file
        if (isset($_FILES['media_file'])) {
            $error = $_FILES['media_file']['error'];

            if($error) {
                $upload_errors = [
                    0 => 'There is no error, the file uploaded with success',
                    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                    3 => 'The uploaded file was only partially uploaded',
                    4 => 'No file was uploaded',
                    6 => 'Missing a temporary folder',
                    7 => 'Failed to write file to disk.',
                    8 => 'A PHP extension stopped the file upload.',
                ];

                echo json_encode([
                    'status' => false,
                    'message' => $upload_errors[$error]
                ]);
                exit;
            }

            if (is_uploaded_file($_FILES['media_file']['tmp_name'])) {
                $tmp_name = $_FILES['media_file']['tmp_name'];
                $post_filename = $_FILES["media_file"]["name"];
                $extension = mb_substr($post_filename, mb_strrpos($post_filename, '.'));

                if (! $this->is_valid_extension($extension)) {
                    echo json_encode([
                        'status' => false,
                        'message' => $this->lang->line('File type is not allowed'),
                    ]);
                    exit();
                }

                if (! $this->is_valid_file_size($_FILES)) {
                    echo json_encode([
                        'status' => false,
                        'message' => $this->lang->line('File size is not allowed'),
                    ]);
                    exit();
                }

                $filename = 'flow_builder_' . $this->user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) . $extension;
                $destination = $upload_dir . $filename;
                $mime_type = mime_content_type($tmp_name);

                if (move_uploaded_file($tmp_name, $destination)) {

                    // Changes the file permission
                    chmod($destination, 0644);

                    $file = base_url() . 'upload/flow_builder/' . $filename;
                    
                    echo json_encode([
                        'status' => true,
                        'mime_type' => $mime_type,
                        'file' => $file,
                    ]);
                    exit;
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => $this->lang->line('Something went wrong while uploading file'),
                    ]);
                    exit();
                }
            }
        }

        echo json_encode([
            'status' => false,
            'message' => $this->lang->line('Something went wrong while uploading file'),
        ]);
        exit();
    }

    private function is_valid_extension($extension) 
    {
        $supported_extensions = [
            '.png', 
            '.jpg', 
            '.jpeg', 
            '.gif',

            '.doc',
            '.docx',
            '.pdf',
            '.txt',
            '.ppt',
            '.pptx',
            '.xls',
            '.xlsx',
            
            '.amr', 
            '.mp3', 
            '.wav',

            '.flv',
            '.mp4',
            '.wmv',
       ];

       return in_array(strtolower($extension), $supported_extensions);
    }

    private function is_valid_mime_type($temporary_file) 
    {
        $mime_type = mime_content_type($temporary_file);

        /**
         * An array of supported mime types
         * @var $supported_mime_types
         */
        $supported_mime_types = [
            // Image extensions
            'image/jpeg',

            'image/png',

            'image/gif',

            // Video extensions
            'video/x-flv',

            'video/ogg',
            'application/ogg',

            'video/mp4',

            'video/x-ms-wmv',

            // Audio extensions
            'audio/AMR',
            'audio/amr',
            'audio/AMR-WB',
            'audio/amr-wb+',

            'audio/mpeg',

            'audio/wave',
            'audio/wav',
            'audio/x-wav',
            'audio/x-pn-wav',

            // File extensions
            'application/msword', 
            
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
            
            'application/pdf', 
            
            'text/plain', 
            
            'application/vnd.ms-powerpoint',
            
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', 
            
            'application/vnd.ms-excel', 
            
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return in_array(strtolower($mime_type), $supported_mime_types);
    }

    private function find_file_type($filename) {
        $allowed_image_extensions = [
			// jpeg or jpg images
		    '.jpeg',
			'.jpg', 

		    // png images
		    '.png',

		    // gif images
		    '.gif',
		];

        $allowed_video_extensions = [
            // Video extensions
            '.flv',

            // ogv or ogg videos
            '.ogg',

            // '.webm',

            // 3gp or mts videos 
            // '.3gpp',

            '.mp4',

            // '.mkv',

            // '.mpeg',

            // '.mov',

            // '.avi', 

            '.wmv',

            // '.m4v',
		];

        $allowed_audio_extensions = [
            // Audio extensions
            '.amr',

            '.mp3',

            '.wav',
        ];

        $allowed_file_extensions = [
            // File extensions
            '.doc', 
            
            '.docx', 
            
            '.pdf', 
            
            '.txt', 
            
            '.ppt',
            
            '.pptx', 
            
            '.xls', 
            
            '.xlsx',
        ];

        $extension = mb_substr($filename, mb_strrpos($filename, '.'));

        if(in_array(strtolower($extension), $allowed_image_extensions)) {
            return 'image';
        } else if (in_array(strtolower($extension), $allowed_audio_extensions)) {
            return 'audio';
        } else if (in_array(strtolower($extension), $allowed_video_extensions)) {
            return 'video';
        } else if (in_array(strtolower($extension), $allowed_file_extensions)) {
            return 'file';
        }

        return false;
    }

    private function is_valid_file_size($file) 
    {
        $file_size = isset($file["media_file"]["size"]) ? $file["media_file"]["size"] : -1;
        $filename = isset($file['media_file']['name']) ? $file['media_file']['name'] : '';

        if ($file_size && $filename) {
            $extension = mb_substr($filename, mb_strrpos($filename, '.'));
            $file_type = $this->find_file_type($extension);

            if ($file_type) {
                $one_mega_bytes = 1024 * 1024;
                $image_upload_limit = (int) $this->config->item('messengerbot_image_upload_limit') * $one_mega_bytes;
                $video_upload_limit = (int) $this->config->item('messengerbot_video_upload_limit') * $one_mega_bytes;
                $audio_upload_limit = (int) $this->config->item('messengerbot_audio_upload_limit') * $one_mega_bytes;
                $file_upload_limit = (int) $this->config->item('messengerbot_file_upload_limit') * $one_mega_bytes;
        
                if (('image' == $file_type) && ($file_size <= $image_upload_limit)) {
                    return true;
                } else if (('video' == $file_type) && ($file_size <= $video_upload_limit)) {
                    return true;
                } else if (('audio' == $file_type) && ($file_size <= $audio_upload_limit)) {
                    return true;
                } else if (('file' == $file_type) && ($file_size <= $file_upload_limit)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }
    
    public function flow_builder_delete_media()
    {
        if (! headers_sent()) {
            header('Content-Type: application/json');
        }

        $this->ajax_check();

        $upload_dir = APPPATH . '../upload/flow_builder/';

        if(isset($_POST['file'])) {
            $file = filter_var($_POST['file'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);

            $file_paths = explode('/', $file);
            $filename = end($file_paths);

            if (! $filename) {
                echo json_encode([
                    'status' => false,
                    'message' => $this->lang->line('Invalid file provided'),
                ]);
                exit();
            }

            $absolute_file_path = $upload_dir . $filename;

            if (! is_dir($absolute_file_path) && file_exists($absolute_file_path)) {
                
                // Deletes the file
                unlink($absolute_file_path);

                echo json_encode([
                    'status' => true,
                    'message' => $this->lang->line('File has been deleted successfully'),
                ]);
                exit;
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => $this->lang->line('Could not delete the file'),
                ]);
                exit();
            }
        }

        echo json_encode([
            'status' => false,
            'message' => $this->lang->line('Bad request'),
        ]);
        exit;
    }


    public function load_builder($page_table_id = '', $go_back_link=1, $media_type='fb')
    {
        $info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['id'=>$page_table_id,'user_id'=>$this->user_id]],['id', 'page_id', 'page_name', 'insta_username']);

        if($page_table_id == '' || empty($info))
            redirect('visual_flow_builder/flowbuilder_manager', 'location');

        $user_input_flow_addon = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>49)))
            if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access))
                $user_input_flow_addon = 1;
        $data['user_input_flow_addon'] = $user_input_flow_addon;

        $messenger_engagement_plugin = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>30)))
            if($this->session->userdata('user_type') == 'Admin' || count(array_intersect(array(213,214,215,217),$this->module_access))>0)
                $messenger_engagement_plugin = 1;
        $data['messenger_engagement_plugin'] = $messenger_engagement_plugin;

        $sequence_addon = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>30)))
            if($this->session->userdata('user_type') == 'Admin' || in_array(219,$this->module_access))
                $sequence_addon = 1;
        $data['sequence_addon'] = $sequence_addon;

        if($go_back_link == 1) {
        	// $data['go_back_link'] = base_url('visual_flow_builder/flowbuilder_manager');
            $data['go_back_link'] = base_url("messenger_bot/bot_list/{$media_type}");
        }
        else {
        	$data['go_back_link'] = base_url('messenger_bot/template_manager');
        }

        $data['body'] = 'index';
        $data['page_title'] = $this->lang->line('Add new flow');
        $data['page_table_id'] = $page_table_id;
        $data['json_data'] = null;
        $data['builder_table_id'] = 0;

        if($media_type == 'ig') {
            $data['instagram_bot_addon'] = 1;
            $data['page_name_or_insta_username'] = $info[0]['insta_username'];
        } else  {
            $data['instagram_bot_addon'] = 0;
            $data['page_name_or_insta_username'] = $info[0]['page_name'];
            $data['fb_page_id'] = $info[0]['page_id'];
        }

        $messenger_bot_condition = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>63)))
            if($this->session->userdata('user_type') == 'Admin' || count(array_intersect(array(325),$this->module_access))>0)
                $messenger_bot_condition = 1;
        $data['messenger_bot_condition'] = $messenger_bot_condition;

        $message_sent_stat = [];
        $data['message_sent_stat'] = json_encode($message_sent_stat);
        $message_sent_stat_addon = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>64)))
            if($this->session->userdata('user_type') == 'Admin' || count(array_intersect(array(330),$this->module_access))>0)
                $message_sent_stat_addon = 1;
        $data['message_sent_stat_addon'] = $message_sent_stat_addon;

        $this->load->view('index.php', $data); 
    } 

    public function edit_builder_data($table_id = 0, $go_back_link=1, $media_type='fb')
    {
        $info = $this->basic->get_data('visual_flow_builder_campaign',['where'=>['user_id'=>$this->user_id,'id'=>$table_id]]);
        if(empty($info) || $table_id==0)
            redirect('visual_flow_builder/flowbuilder_manager', 'location');

        $message_sent_stat = $existing_flow_unique_ids = $message_sent_stat_info = [];
        $existing_flow_unique_ids_info = $this->basic->get_data('visual_flow_campaign_unique_ids',['where'=>['visual_flow_campaign_id'=>$table_id]],['element_unique_id']);
        foreach($existing_flow_unique_ids_info as $single_unique_id)
            array_push($existing_flow_unique_ids,$single_unique_id['element_unique_id']);

        if(!empty($existing_flow_unique_ids))
            $message_sent_stat_info = $this->basic->get_data('messenger_bot_message_sent_stat',['where_in'=>['message_unique_id'=>$existing_flow_unique_ids]],['sum(no_sent_click) as sent_click',"sum(error_count) as total_error","count(DISTINCT subscriber_id) as subscribers",'message_unique_id'],$join='',$limit='',$start=NULL,$order_by='',$group_by='message_unique_id');

        foreach($message_sent_stat_info as $sent_stat)
        {
            $message_sent_stat[$sent_stat['message_unique_id']]['sent_click'] = custom_number_format($sent_stat['sent_click']);
            $message_sent_stat[$sent_stat['message_unique_id']]['total_error'] = custom_number_format($sent_stat['total_error']);
            $message_sent_stat[$sent_stat['message_unique_id']]['subscribers'] = custom_number_format($sent_stat['subscribers']);
            $total_sent = $sent_stat['sent_click'];
            $successfully_sent = $sent_stat['sent_click'] - $sent_stat['total_error'];
            $delivered = ($successfully_sent*100)/$total_sent;
            $message_sent_stat[$sent_stat['message_unique_id']]['delivered'] = number_format($delivered,2)."%";
        }
        $data['message_sent_stat'] = json_encode($message_sent_stat);

        // pre($message_sent_stat); exit;

        $message_sent_stat_addon = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>64)))
            if($this->session->userdata('user_type') == 'Admin' || count(array_intersect(array(330),$this->module_access))>0)
                $message_sent_stat_addon = 1;
        $data['message_sent_stat_addon'] = $message_sent_stat_addon;

        $user_input_flow_addon = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>49)))
            if($this->session->userdata('user_type') == 'Admin' || in_array(292,$this->module_access))
                $user_input_flow_addon = 1;
        $data['user_input_flow_addon'] = $user_input_flow_addon;

        $sequence_addon = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>30)))
            if($this->session->userdata('user_type') == 'Admin' || in_array(219,$this->module_access))
                $sequence_addon = 1;
        $data['sequence_addon'] = $sequence_addon;

        $messenger_engagement_plugin = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>30)))
            if($this->session->userdata('user_type') == 'Admin' || count(array_intersect(array(213,214,215,217),$this->module_access))>0)
                $messenger_engagement_plugin = 1;
        $data['messenger_engagement_plugin'] = $messenger_engagement_plugin;

        $data['go_back_link'] = base_url("messenger_bot/bot_list");

        $info2 = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['id'=>$info[0]['page_id'],'user_id'=>$this->user_id]],['id', 'page_name', 'insta_username','page_id']);
        if($media_type == 'ig') {
            $data['instagram_bot_addon'] = 1;
            $data['page_name_or_insta_username'] = $info2[0]['insta_username'];
        } else  {
            $data['instagram_bot_addon'] = 0;
            $data['page_name_or_insta_username'] = $info2[0]['page_name'];
            $data['fb_page_id'] = $info2[0]['page_id'];
        }

        $messenger_bot_condition = 0;
        if($this->basic->is_exist("add_ons",array("project_id"=>63)))
            if($this->session->userdata('user_type') == 'Admin' || count(array_intersect(array(325),$this->module_access))>0)
                $messenger_bot_condition = 1;
        $data['messenger_bot_condition'] = $messenger_bot_condition;

        $data['body'] = 'index';
        $data['page_title'] = $this->lang->line('Edit flow');
        $data['page_table_id'] = $info[0]['page_id'] ?? 0;
        $data['json_data'] = $info[0]['json_data'] ?? null;
        $data['builder_table_id'] = $table_id;
        $this->load->view('index.php', $data);
    } 


    public function flowbuilder_manager($page_id=0,$iframe='0')
    {
        $data['body'] = 'flow_builder_list';
        $data['page_title'] = $this->lang->line('Visual Flow Builder');
        $data['iframe'] = '1';

        $data['media_type'] = $this->session->userdata("selected_global_media_type");

        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name'),$join);

        $ig_page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','has_instagram'=>'1')),array('facebook_rx_fb_page_info.id','page_name','name','insta_username'),$join);

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

        $ig_flow_page_list = array();
        if(isset($ig_page_info) && count($ig_page_info) > 0) {
            $ig_flow_page_list['media_name'] = $this->lang->line("Instagram");
            foreach($ig_page_info as $ig_value)
            {
                $ig_flow_page_list['page_list'][$ig_value['id']."-ig"] = $ig_value['page_name']." [".$ig_value['insta_username']."]";
            }
            array_push($group_page_list,$ig_flow_page_list);
        }

        $data['group_page_list'] = $group_page_list;

        $page_list2 = array();
        foreach($page_info as $value)
        {
            $page_list2[$value['id']] = $value['page_name']." [".$value['name']."]";
        }
        $data['page_list'] = $page_list2;
        $data['page_auto_id'] = $page_id; 
        $this->_viewcontroller($data);  
    }

    public function visual_flow_builder_data($page_auto_id=0)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $this->ajax_check();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','reference_name','page_name','media_type');
        $search_columns = array('reference_name','page_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'visual_flow_builder_campaign.id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';
        $where_custom = "visual_flow_builder_campaign.user_id = ".$this->user_id;
        if($page_auto_id != 0) {
            $where_custom .= " AND visual_flow_builder_campaign.page_id = ".$page_auto_id;
        }

        if($this->session->userdata('selected_global_media_type') != '') {
            $where_custom .= " AND visual_flow_builder_campaign.media_type = ".$this->db->escape($this->session->userdata('selected_global_media_type'));
        }

        if ($search_value != '') {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        $where_custom .= " AND is_system = '0'";

        $this->db->where($where_custom);

        $table="visual_flow_builder_campaign";
        $join = array('facebook_rx_fb_page_info' => "visual_flow_builder_campaign.page_id = facebook_rx_fb_page_info.id,left");
        $select =array('visual_flow_builder_campaign.*','facebook_rx_fb_page_info.page_name');
        $info=$this->basic->get_data($table,$where='',$select,$join,$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);

        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
   
    }

    public function delete_flowbuilder_data()
    {
        $this->ajax_check();

        $table_id = $this->input->post('table_id',true);
        $response = [];
        $info = $this->basic->get_data('visual_flow_builder_campaign',['where'=>['id'=>$table_id,'user_id'=>$this->user_id]]);
        if(empty($info))
        {
            $response['status'] = 0; 
            $response['message'] = $this->lang->line('This campaign does not belong to you.');
        }
        else
        {
            $this->db->trans_start();

            $this->basic->delete_data('visual_flow_builder_campaign',['id'=>$table_id,'user_id'=>$this->user_id]);
            
            $unique_ids = [];
            $unique_ids_info = $this->basic->get_data('visual_flow_campaign_unique_ids',['where'=>['visual_flow_campaign_id'=>$table_id]],['element_unique_id']);
            foreach($unique_ids_info as $single_id)
                array_push($unique_ids,$single_id['element_unique_id']);
            $this->basic->delete_data('visual_flow_campaign_unique_ids',['visual_flow_campaign_id'=>$table_id]);
            if(!empty($unique_ids))
            {
                $this->db->where_in('message_unique_id',$unique_ids);
                $this->db->delete('messenger_bot_message_sent_stat');
            }

            $this->basic->delete_data('otn_postback',['visual_flow_campaign_id'=>$table_id]);
            if($this->addon_exist("messenger_bot_enhancers"))
            {
                $this->basic->delete_data('messenger_bot_engagement_mme',['visual_flow_campaign_id'=>$table_id]);
                $this->basic->delete_data('messenger_bot_engagement_2way_chat_plugin',['visual_flow_campaign_id'=>$table_id]);
                $this->basic->delete_data('messenger_bot_engagement_checkbox',['visual_flow_campaign_id'=>$table_id]);
                $this->basic->delete_data('messenger_bot_engagement_send_to_msg',['visual_flow_campaign_id'=>$table_id]);
            }

            if($this->addon_exist('custom_field_manager'))
            {
                $user_input_flow_campaign_ids = [];
                $user_input_flow_campaign_ids_info = $this->basic->get_data('user_input_flow_campaign',['where'=>['visual_flow_campaign_id'=>$table_id]],['id']);
                foreach($user_input_flow_campaign_ids_info as $single_flow_id)
                    array_push($user_input_flow_campaign_ids,$single_flow_id['id']);
                $this->basic->delete_data('user_input_flow_campaign',['visual_flow_campaign_id'=>$table_id]);
                if(!empty($user_input_flow_campaign_ids))
                {
                    $this->db->where_in('flow_campaign_id',$user_input_flow_campaign_ids);
                    $this->db->delete('user_input_flow_questions');
                    $this->db->where_in('flow_campaign_id',$user_input_flow_campaign_ids);
                    $this->db->delete('user_input_flow_questions_answer');
                }
            }

            $drip_campaign_ids = [];
            $drip_campaign_ids_info = $this->basic->get_data('messenger_bot_drip_campaign',['where'=>['visual_flow_campaign_id'=>$table_id]],['id']);
            foreach($drip_campaign_ids_info as $single_drip_id)
                array_push($drip_campaign_ids,$single_drip_id['id']);
            $this->basic->delete_data('messenger_bot_drip_campaign',['visual_flow_campaign_id'=>$table_id]);
            if(!empty($drip_campaign_ids))
            {
                $this->db->where_in('messenger_bot_drip_campaign_id',$drip_campaign_ids);
                $this->db->delete('messenger_bot_drip_campaign_assign');
                $this->db->where_in('messenger_bot_drip_campaign_id',$drip_campaign_ids);
                $this->db->delete('messenger_bot_drip_report');
            }

            $this->basic->delete_data('messenger_bot',['visual_flow_campaign_id'=>$table_id,'user_id'=>$this->user_id,'keyword_type'=>'reply']);
            $this->basic->update_data('messenger_bot',['visual_flow_campaign_id'=>$table_id,'user_id'=>$this->user_id,'keyword_type !='=>'reply'],['visual_flow_campaign_id'=>0, 'visual_flow_type'=>'general']);

            $this->basic->delete_data('messenger_bot_postback',['visual_flow_campaign_id'=>$table_id,'user_id'=>$this->user_id,'template_for'=>'reply_message']);
            $this->basic->update_data('messenger_bot_postback',['visual_flow_campaign_id'=>$table_id,'user_id'=>$this->user_id,'template_for !='=>'reply_message'], ['visual_flow_campaign_id'=>0, 'visual_flow_type'=>'general']);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $response['status'] = 0; 
                $response['message'] = $this->lang->line('Deleting template was unsuccessful. Database error occured during deleting template.');
            }
            else
            {
                $response['status'] = 1; 
                $response['message'] = $this->lang->line('Template and all of the corresponding data have been deleted successfully.');
            }
        }
        echo json_encode($response,true);
    }

    public function get_broadcast_tags($media_type='fb')
    {
        if (! headers_sent()) {
            header('Content-Type: application/json');
        }

        $old_tags = array
        (
            "ACCOUNT_UPDATE" => "ACCOUNT_UPDATE",
            "APPLICATION_UPDATE" => "APPLICATION_UPDATE",
            "APPOINTMENT_UPDATE" => "APPOINTMENT_UPDATE",
            "BUSINESS_PRODUCTIVITY" => "BUSINESS_PRODUCTIVITY",
            "COMMUNITY_ALERT" => "COMMUNITY_ALERT",
            "CONFIRMED_EVENT_REMINDER" => "CONFIRMED_EVENT_REMINDER",
            "FEATURE_FUNCTIONALITY_UPDATE" => "FEATURE_FUNCTIONALITY_UPDATE",
            "GAME_EVENT" => "GAME_EVENT",
            "ISSUE_RESOLUTION" => "ISSUE_RESOLUTION",
            "PAIRING_UPDATE" => "PAIRING_UPDATE",
            "PAYMENT_UPDATE" => "PAYMENT_UPDATE",
            "PERSONAL_FINANCE_UPDATE" => "PERSONAL_FINANCE_UPDATE",
            "RESERVATION_UPDATE" => "RESERVATION_UPDATE",
            "SHIPPING_UPDATE" => "SHIPPING_UPDATE",
            "TICKET_UPDATE" => "TICKET_UPDATE",
            "TRANSPORTATION_UPDATE" => "TRANSPORTATION_UPDATE",
        );

        $new_tags = array
        (
            "ACCOUNT_UPDATE"=>"ACCOUNT_UPDATE",
            "CONFIRMED_EVENT_UPDATE"=>"CONFIRMED_EVENT_UPDATE",
            "HUMAN_AGENT"=>"HUMAN_AGENT (Closed BETA)",
            "POST_PURCHASE_UPDATE"=>"POST_PURCHASE_UPDATE",        
            "NON_PROMOTIONAL_SUBSCRIPTION" => "NON_PROMOTIONAL_SUBSCRIPTION (NPI REGISTERED ONLY)"
        );

        $new_tags_ig = array(
            "HUMAN_AGENT"=>"HUMAN_AGENT (Closed BETA)",
        );

        $instagram_bot_addon = (bool) $this->input->post('instagram_bot_addon',true);

        if ($instagram_bot_addon) {
            echo json_encode($new_tags_ig); 
            exit;
        } else {
            echo json_encode($new_tags); 
            exit;
        }
    }

    // public function assets(){
    //     $args = func_get_args();
    //     $file_name = array_pop($args);
    //     $path = FCPATH.'assets'.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$args).DIRECTORY_SEPARATOR.$file_name;
    //     $content = file_get_contents($path);
    //     header('content-type: '.mime_content_type($path));
    //     echo $content;
    // }

    // public function upload(){
    //     $args = func_get_args();
    //     $file_name = array_pop($args);
    //     $path = FCPATH.'upload'.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$args).DIRECTORY_SEPARATOR.$file_name;
    //     $content = file_get_contents($path);
    //     header('content-type: '.mime_content_type($path));
    //     echo $content;
    // }

    public function language_file() {
        header("Content-Type: application/javascript; charset=utf-8");
        header("Cache-Control: max-age=604800, public");
        ?>
        "use strict";
        var postback_click_lang = '<?php echo $this->lang->line("Click"); ?>';
        var message_sent_lang = '<?php echo $this->lang->line("Sent"); ?>';
        var message_sent_stat_error_lang = '<?php echo $this->lang->line("Errors"); ?>';
        var message_sent_stat_subscribers = '<?php echo $this->lang->line("Subscribers"); ?>';
        var message_sent_stat_delivered = '<?php echo $this->lang->line("Delivered"); ?>';

        var action_button_name = '<?php echo $this->lang->line("Action Buttons"); ?>';
        var action_button_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';

        var action_button_html_title_action_button = '<?php echo $this->lang->line("Action Button"); ?>';
        var action_button_html_title_messenger_engagement_plugin = '<?php echo $this->lang->line("Messenger engagement plugin"); ?>';
        var action_button_html_title_select = '<?php echo $this->lang->line("Select"); ?>';

        var action_button_html_actions_messenger_engagement_plugin = '<?php echo $this->lang->line("Messenger engagement plugin"); ?>';
        var action_button_html_actions_get_started = '<?php echo $this->lang->line("Get-started template"); ?>';
        var action_button_html_actions_no_match = '<?php echo $this->lang->line("No match template"); ?>';
        var action_button_html_actions_unsubscribe = '<?php echo $this->lang->line("Un-subscribe template"); ?>';
        var action_button_html_actions_resubscribe = '<?php echo $this->lang->line("Re-subscribe template"); ?>';
        var action_button_html_actions_email = '<?php echo $this->lang->line("Email quick reply template"); ?>';
        var action_button_html_actions_phone = '<?php echo $this->lang->line("Phone quick reply template"); ?>';
        var action_button_html_actions_location = '<?php echo $this->lang->line("Location quick reply template"); ?>';
        var action_button_html_actions_birthday = '<?php echo $this->lang->line("Birthday quick reply template"); ?>';
        var action_button_html_actions_with_human = '<?php echo $this->lang->line("Chat with human template"); ?>';
        var action_button_html_actions_with_robot = '<?php echo $this->lang->line("Chat with robot template"); ?>';

        var action_button_html_plugin_type = '<?php echo $this->lang->line("Plugin type"); ?>';
        var action_button_html_domain = '<?php echo $this->lang->line("Domain"); ?>';
        var action_button_html_language = '<?php echo $this->lang->line("Language"); ?>';
        var action_button_html_language_tooltip = '<?php echo $this->lang->line("Plugin will be loaded in this language."); ?>';

        var action_button_html_cta_button_text = '<?php echo $this->lang->line("Cta button text"); ?>';
        var action_button_html_cta_button_text_tooltip = '<?php echo $this->lang->line("You can choose cta button text from this cta list."); ?>';

        var action_button_html_plugin_skin = '<?php echo $this->lang->line("Plugin skin"); ?>';
        var action_button_html_plugin_skin_light = '<?php echo $this->lang->line("Light"); ?>';
        var action_button_html_plugin_skin_dark = '<?php echo $this->lang->line("Dark"); ?>';
        var action_button_html_plugin_skin_white = '<?php echo $this->lang->line("White"); ?>';
        var action_button_html_plugin_skin_blue = '<?php echo $this->lang->line("Blue"); ?>';
        var action_button_html_plugin_skin_tooltip = '<?php echo $this->lang->line("Light skin is suitable for pages with dark background and dark skin is suitable for pages with light background."); ?>';

        var action_button_html_center_align = '<?php echo $this->lang->line("Center align"); ?>';
        var action_button_html_center_align_yes = '<?php echo $this->lang->line("Yes"); ?>';
        var action_button_html_center_align_no = '<?php echo $this->lang->line("No"); ?>';
        var action_button_html_center_align_tooltip = '<?php echo $this->lang->line("Choosing yes will make the plugin aligned center, otherwise left."); ?>';

        var action_button_html_plugin_size = '<?php echo $this->lang->line("Plugin size"); ?>';
        var action_button_html_plugin_size_small = '<?php echo $this->lang->line("Small"); ?>';
        var action_button_html_plugin_size_medium = '<?php echo $this->lang->line("Medium"); ?>';
        var action_button_html_plugin_size_large = '<?php echo $this->lang->line("Large"); ?>';
        var action_button_html_plugin_size_xlarge = '<?php echo $this->lang->line("Extra Large"); ?>';
        var action_button_html_plugin_size_standard = '<?php echo $this->lang->line("Standard"); ?>';
        var action_button_html_plugin_size_tooltip = '<?php echo $this->lang->line("Overall plugin size."); ?>';

        var action_button_html_redirect_to_a_webpage_on_successful_optin = '<?php echo $this->lang->line("Redirect to a webpage on successful opt-in"); ?>';

        var action_button_html_optin_success_redirect_url = '<?php echo $this->lang->line("Opt-in success redirect url"); ?>';
        var action_button_html_optin_success_redirect_url_tooltip = '<?php echo $this->lang->line("Visitors will be redirected to this url after successful opt-in."); ?>';

        var action_button_html_optin_success_message_in_website = '<?php echo $this->lang->line("Opt-in success message in website"); ?>';
        var action_button_html_optin_success_message_in_website_textarea_placeholder = '<?php echo $this->lang->line("Keep it blank if you do not want."); ?>';
        var action_button_html_optin_success_message_in_website_textarea_default_message = '<?php echo $this->lang->line("You have been subscribed successfully, thank you."); ?>';

        var action_button_html_i_want_to_add_a_button_in_success_message = '<?php echo $this->lang->line("I want to add a button in success message"); ?>';

        var action_button_html_button_text = '<?php echo $this->lang->line("Button text"); ?>';
        var action_button_html_button_text_tooltip = '<?php echo $this->lang->line("This button will be embeded with opt-in successful message."); ?>';

        var action_button_html_button_url = '<?php echo $this->lang->line("Button url"); ?>';
        var action_button_html_button_url_tooltip = '<?php echo $this->lang->line("Button click action url."); ?>';

        var action_button_html_button_background = '<?php echo $this->lang->line("Button background"); ?>';
        var action_button_html_button_text_color = '<?php echo $this->lang->line("Button text color"); ?>';
        var action_button_html_button_hover_background = '<?php echo $this->lang->line("Button hover background"); ?>';
        var action_button_html_button_text_hover_color = '<?php echo $this->lang->line("Button text hover color"); ?>';

        var action_button_html_button_size = '<?php echo $this->lang->line("Button size"); ?>';
        var action_button_html_button_size_small = '<?php echo $this->lang->line("Small"); ?>';
        var action_button_html_button_size_medium = '<?php echo $this->lang->line("Medium"); ?>';
        var action_button_html_button_size_large = '<?php echo $this->lang->line("Large"); ?>';
        var action_button_html_button_size_extra_large = '<?php echo $this->lang->line("Extra Large"); ?>';
        var action_button_html_button_size_tooltip = '<?php echo $this->lang->line("Choose how big you want the button to be."); ?>';

        var action_button_html_checkbox_validation_error_message = '<?php echo $this->lang->line("Checkbox validation error message"); ?>';
        var action_button_html_checkbox_validation_error_message_tooltip = '<?php echo $this->lang->line("This message will be displayed if checkbox is not checked."); ?>';

        var action_button_html_chat_plugin_loading = '<?php echo $this->lang->line("Chat plugin loading"); ?>';
        var action_button_html_chat_plugin_loading_hide = '<?php echo $this->lang->line("Hide"); ?>';
        var action_button_html_chat_plugin_loading_show = '<?php echo $this->lang->line("Show"); ?>';
        var action_button_html_chat_plugin_loading_fade = '<?php echo $this->lang->line("Fade"); ?>';
        var action_button_html_chat_plugin_loading_tooltip = '<?php echo $this->lang->line("Choose how chat plugin will be loaded"); ?>';

        var action_button_html_loading_delay_seconds = '<?php echo $this->lang->line("Loading delay (Seconds)"); ?>';
        var action_button_html_loading_delay_seconds_tooltip = '<?php echo $this->lang->line("Plugin will be loaded after few seconds."); ?>';

        var action_button_html_theme_color = '<?php echo $this->lang->line("Theme color"); ?>';
        var action_button_html_theme_color_tooltip = '<?php echo $this->lang->line("The color to use as a theme for the plugin. Supports any color except white. We highly recommend you choose a color that has a high contrast to white. Keep it blank if you want default theme."); ?>';

        var action_button_html_do_not_show_if_not_logged_in = '<?php echo $this->lang->line("Do not show if not logged in?"); ?>';
        var action_button_html_do_not_show_if_not_logged_in_yes = '<?php echo $this->lang->line("Yes"); ?>';
        var action_button_html_do_not_show_if_not_logged_in_no = '<?php echo $this->lang->line("No"); ?>';
        var action_button_html_do_not_show_if_not_logged_in_tooltip = '<?php echo $this->lang->line("Chat plugin will not be loaded if visitor is not logged in to Facebook."); ?>';

        var action_button_html_greeting_text_if_logged_in_to_facebook = '<?php echo $this->lang->line("Greeting text if logged in to Facebook"); ?>';
        var action_button_html_greeting_text_if_logged_in_to_facebook_tooltip = '<?php echo $this->lang->line("The greeting text that will be displayed if the user is currently logged in to Facebook. Maximum 80 characters."); ?>';
        var action_button_html_greeting_text_if_logged_in_to_facebook_placeholder = '<?php echo $this->lang->line("Maximum 80 characters"); ?>';

        var action_button_html_greeting_text_if_not_logged_in_to_facebook = '<?php echo $this->lang->line("Greeting text if not logged in to Facebook"); ?>';
        var action_button_html_greeting_text_if_not_logged_in_to_facebook_tooltip = '<?php echo $this->lang->line("The greeting text that will be displayed if the user is not logged in to Facebook. Maximum 80 characters."); ?>';
        var action_button_html_greeting_text_if_not_logged_in_to_facebook_placeholder = '<?php echo $this->lang->line("Maximum 80 characters"); ?>';

        var action_button_html_reference = '<?php echo $this->lang->line("Reference"); ?>';
        var action_button_html_reference_tooltip = '<?php echo $this->lang->line("Put a unique reference to track this plugin later."); ?>';

        var action_button_html_select_labels = '<?php echo $this->lang->line("Select label(s)"); ?>';
        var action_button_html_select_labels_tooltip = '<?php echo $this->lang->line("Subscriber obtained from this plugin will be enrolled in these labels. You must select page to fill this list with data."); ?>';

        var action_button_html_actions_story_mention = '<?php echo $this->lang->line("Story mention"); ?>';
        var action_button_html_actions_story_private_reply = '<?php echo $this->lang->line("Story private reply"); ?>';
        var action_button_html_actions_message_unsend_private_reply = '<?php echo $this->lang->line("Message unsend private reply"); ?>';

        var action_button_html_messenger_engagement_plugin_checkbox_plugin = '<?php echo $this->lang->line("Checkbox plugin"); ?>';
        var action_button_html_messenger_engagement_plugin_send_to_messenger = '<?php echo $this->lang->line("Send to messenger"); ?>';
        var action_button_html_messenger_engagement_plugin_m_me_link = '<?php echo $this->lang->line("M.me link"); ?>'; 
        var action_button_html_messenger_engagement_plugin_customer_chat_plugin = '<?php echo $this->lang->line("Customer chat plugin"); ?>'; 

        var action_button_modal_title = '<?php echo $this->lang->line("Configure Action Button"); ?>';
        var action_button_modal_msg_action_type = '<?php echo $this->lang->line("Please choose a action type."); ?>';

        var action_button_modal_msg_plugin_type = '<?php echo $this->lang->line("The messenger engagement plugin field is empty."); ?>';
        var action_button_modal_msg_domain = '<?php echo $this->lang->line("The domain field is empty."); ?>';
        var action_button_modal_msg_language = '<?php echo $this->lang->line("The language field is empty."); ?>';

        var action_button_modal_msg_plugin_skin = '<?php echo $this->lang->line("Choose a plugin skin type."); ?>';
        var action_button_modal_msg_plugin_size = '<?php echo $this->lang->line("Choose a plugin size type."); ?>';

        var action_button_modal_msg_plugin_redirection_status = '<?php echo $this->lang->line("The opt-in success redirect url field is empty."); ?>';

        var action_button_modal_msg_button_text = '<?php echo $this->lang->line("The button text field is empty."); ?>';
        var action_button_modal_msg_button_url = '<?php echo $this->lang->line("The button URL field is empty."); ?>';

        var action_button_modal_msg_button_attributes = '<?php echo $this->lang->line("The button attribute(s) field is empty."); ?>';
        var action_button_modal_msg_plugin_center_align = '<?php echo $this->lang->line("Should the plugin be centered?"); ?>';

        var action_button_modal_msg_cta_button_type = '<?php echo $this->lang->line("The cta button text field is empty."); ?>';
        var action_button_modal_msg_button_size = '<?php echo $this->lang->line("Choose a button size type."); ?>';

        var action_button_modal_msg_chat_plugin_loading_status = '<?php echo $this->lang->line("Choose a chat plugin loading status."); ?>';

        var action_button_modal_msg_chat_plugin_theme_color = '<?php echo $this->lang->line("The theme color field is empty."); ?>';

        var action_button_modal_msg_hide_chat_plugin_if_not_logged_in = '<?php echo $this->lang->line("Should the chat plugin be hidden if user not logged in?"); ?>';

        var action_button_modal_msg_reference = '<?php echo $this->lang->line("The reference field is empty."); ?>';

        var audio_name = '<?php echo $this->lang->line("Audio"); ?>';
        var audio_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var audio_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var audio_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';
        var audio_html_label_media_url = '<?php echo $this->lang->line("Please provide reply audio url"); ?>';
        var audio_html_ph_media_url = '<?php echo $this->lang->line("Put audio url here or click the upload box."); ?>';
        var audio_html_info_supported_media = '<?php echo $this->lang->line("Supported types: amr, mp3, wav"); ?>';
        var audio_modal_title = '<?php echo $this->lang->line("Configure Audio"); ?>';
        var audio_modal_msg_required_media = '<?php echo $this->lang->line("Upload a properly encoded audio."); ?>';

        var button_name = '<?php echo $this->lang->line("Button"); ?>';
        var button_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var button_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var button_html_title_select = '<?php echo $this->lang->line("Select"); ?>';

        var button_html_label_button_text = '<?php echo $this->lang->line("Button text"); ?>';
        var button_html_label_button_type = '<?php echo $this->lang->line("Button type"); ?>';
        var button_html_label_postback_id = '<?php echo $this->lang->line("Postback ID"); ?>';
        var button_html_label_web_url = '<?php echo $this->lang->line("Web URL"); ?>';
        var button_html_label_mobile = '<?php echo $this->lang->line("Mobile/Phone"); ?>';
        var button_html_label_webview_url = '<?php echo $this->lang->line("Webview URL"); ?>';

        var button_html_types_postback_new = '<?php echo $this->lang->line("New postback"); ?>';
        var button_html_types_postback = '<?php echo $this->lang->line("Postback"); ?>';
        var button_html_types_web_url = '<?php echo $this->lang->line("Web URL"); ?>';
        var button_html_types_web_url_full = '<?php echo $this->lang->line("Webview [FULL]"); ?>';
        var button_html_types_web_url_compact = '<?php echo $this->lang->line("Webview [COMPACT]"); ?>';
        var button_html_types_web_url_tall = '<?php echo $this->lang->line("Webview [TALL]"); ?>';
        var button_html_types_web_url_birthday = '<?php echo $this->lang->line("User birthday"); ?>';
        var button_html_types_web_url_email = '<?php echo $this->lang->line("User email"); ?>';
        var button_html_types_web_url_phone = '<?php echo $this->lang->line("User phone"); ?>';
        var button_html_types_web_url_location = '<?php echo $this->lang->line("User location"); ?>';
        var button_html_types_phone_number = '<?php echo $this->lang->line("Call us"); ?>';
        var button_html_types_unsubscribe = '<?php echo $this->lang->line("Unsubscribe"); ?>';
        var button_html_types_resubscribe = '<?php echo $this->lang->line("Re-subscribe"); ?>';
        var button_html_types_with_human = '<?php echo $this->lang->line("Chat with human"); ?>';
        var button_html_types_with_robot = '<?php echo $this->lang->line("Chat with robot"); ?>';

        var button_modal_title = '<?php echo $this->lang->line("Configure Button"); ?>';
        var button_modal_msg_button_name = '<?php echo $this->lang->line("Please provide a button name."); ?>';
        var button_modal_msg_button_type = '<?php echo $this->lang->line("Please choose a button type."); ?>';
        var button_modal_msg_postback_id = '<?php echo $this->lang->line("Please choose a postback ID."); ?>';
        var button_modal_msg_web_url = '<?php echo $this->lang->line("Please enter a valid web URL."); ?>';
        var button_modal_msg_webview_url = '<?php echo $this->lang->line("Please enter a valid webview URL."); ?>';
        var button_modal_msg_mobile = '<?php echo $this->lang->line("Please enter a valid mobile/phone number."); ?>';

        var carousel_name = '<?php echo $this->lang->line("Carousel"); ?>';
        var carousel_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var carousel_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var carousel_socket_output_items = '<?php echo $this->lang->line("Items"); ?>';
        var carousel_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';
        var carousel_modal_title = '<?php echo $this->lang->line("Configure Carousel"); ?>';

        var carousel_item_name = '<?php echo $this->lang->line("Carousel Item"); ?>';
        var carousel_item_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var carousel_item_socket_output_buttons = '<?php echo $this->lang->line("Buttons"); ?>';
        var carousel_item_html_label_reply_img = '<?php echo $this->lang->line("Please provide your reply image"); ?>';
        var carousel_item_html_label_destination_link = '<?php echo $this->lang->line("Image click destination link"); ?>';
        var carousel_item_html_label_img_link = '<?php echo $this->lang->line("Image click destination link"); ?>';
        var carousel_item_html_label_title = '<?php echo $this->lang->line("Title"); ?>';
        var carousel_item_html_label_subtitle = '<?php echo $this->lang->line("Subtitle"); ?>';

        var carousel_item_html_ph_media_url = '<?php echo $this->lang->line("Put your image url here or click the upload box."); ?>';
        var carousel_item_html_info_supported_img = '<?php echo $this->lang->line("Supported types: png, jpg, gif"); ?>';

        var carousel_item_modal_title = '<?php echo $this->lang->line("Configure Carousel Item"); ?>';
        var carousel_item_modal_msg_title = '<?php echo $this->lang->line("Please provide a title."); ?>';

        var condition_name = '<?php echo $this->lang->line("Conditional Reply"); ?>';
        var condition_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var condition_socket_output_true = '<?php echo $this->lang->line("True"); ?>';
        var condition_socket_output_false = '<?php echo $this->lang->line("False"); ?>';
        var condition_html_title_select = '<?php echo $this->lang->line("Select"); ?>';
        var condition_html_label_all_match = '<?php echo $this->lang->line("All Match"); ?>';
        var condition_html_label_any_match = '<?php echo $this->lang->line("Any Match"); ?>';
        var condition_html_label_system_field = '<?php echo $this->lang->line("System Field"); ?>';
        var condition_html_label_custom_field = '<?php echo $this->lang->line("Custom Field"); ?>';
        var condition_html_label_variable = '<?php echo $this->lang->line("Variable"); ?>';
        var condition_html_label_operator = '<?php echo $this->lang->line("Operator"); ?>';
        var condition_html_label_gender = '<?php echo $this->lang->line("Gender"); ?>';
        var condition_html_label_value = '<?php echo $this->lang->line("Value"); ?>';
        var condition_html_label_contact_group_id = '<?php echo $this->lang->line("Contact group ID"); ?>';

        var condition_html_gender_male = '<?php echo $this->lang->line("Male"); ?>';
        var condition_html_gender_female = '<?php echo $this->lang->line("Female"); ?>';

        var condition_html_operators_system_contains = '<?php echo $this->lang->line("Contains"); ?>';
        var condition_html_operators_system_start_with = '<?php echo $this->lang->line("Start With"); ?>';
        var condition_html_operators_system_end_with = '<?php echo $this->lang->line("End With"); ?>';
        var condition_html_operators_system_has_value = '<?php echo $this->lang->line("Has Value"); ?>';

        var condition_html_variables_system_gender = '<?php echo $this->lang->line("Gender"); ?>';
        var condition_html_variables_system_first_name = '<?php echo $this->lang->line("First Name"); ?>';
        var condition_html_variables_system_last_name = '<?php echo $this->lang->line("Last Name"); ?>';
        var condition_html_variables_system_contact_group_id = '<?php echo $this->lang->line("Label"); ?>';
        var condition_html_variables_system_email = '<?php echo $this->lang->line("Email"); ?>';
        var condition_html_variables_system_phone_number = '<?php echo $this->lang->line("Phone Number"); ?>';

        var condition_modal_title = '<?php echo $this->lang->line("Configure Condition"); ?>';
        var condition_modal_msg_duplicates_system_field = '<?php echo $this->lang->line("System field variable has duplicate values."); ?>';
        var condition_modal_msg_required_equal_operator = '<?php echo $this->lang->line("System field row operator must be set to equal."); ?>';
        var condition_modal_msg_gender_type = '<?php echo $this->lang->line("System field row gender type is empty."); ?>';
        var condition_modal_msg_label = '<?php echo $this->lang->line("System field row contact group ID is empty."); ?>';
        var condition_modal_msg_operator_system_field = '<?php echo $this->lang->line("System field row operator is empty."); ?>';
        var condition_modal_msg_variable_value_system_field = '<?php echo $this->lang->line("System field row variable value is empty."); ?>';

        var condition_modal_msg_duplicates_custom_field = '<?php echo $this->lang->line("Custom field variable has duplicate values."); ?>';
        var condition_modal_msg_operator_custom_field = '<?php echo $this->lang->line("Custom field row operator is empty."); ?>';
        var condition_modal_msg_variable_value_custom_field = '<?php echo $this->lang->line("Custom field row variable value is empty."); ?>';

        var condition_template_all = '<?php echo $this->lang->line("All"); ?>';
        var condition_template_any = '<?php echo $this->lang->line("Any"); ?>';
        var condition_template_match_type = '<?php echo $this->lang->line("Match Type"); ?>';
        var condition_template_system_field = '<?php echo $this->lang->line("System Field"); ?>';
        var condition_template_custom_field = '<?php echo $this->lang->line("Custom Field"); ?>';
        var condition_template_operator = '<?php echo $this->lang->line("Operator"); ?>';
        var condition_template_variable = '<?php echo $this->lang->line("Variable"); ?>';
        var condition_template_value = '<?php echo $this->lang->line("Value"); ?>';

        var ecommerce_name = '<?php echo $this->lang->line("Ecommerce"); ?>';
        var ecommerce_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var ecommerce_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var ecommerce_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';
        var ecommerce_html_title_select = '<?php echo $this->lang->line("Select"); ?>';
        var ecommerce_html_label_store = '<?php echo $this->lang->line("Select your ecommerce store"); ?>';
        var ecommerce_html_label_products = '<?php echo $this->lang->line("Select products for carousel/generic reply"); ?>';
        var ecommerce_html_label_button_name = '<?php echo $this->lang->line("Buy now button text"); ?>';

        var ecommerce_modal_title = '<?php echo $this->lang->line("Configure Ecommerce"); ?>';
        var ecommerce_modal_msg_store = '<?php echo $this->lang->line("Please choose a ecommerce store."); ?>';
        var ecommerce_modal_msg_product = '<?php echo $this->lang->line("Please choose a product."); ?>';
        var ecommerce_modal_msg_button_name = '<?php echo $this->lang->line("Please give a name to buy-now-button."); ?>';

        var facebook_media_name = '<?php echo $this->lang->line("FB Media"); ?>';
        var facebook_media_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var facebook_media_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var facebook_media_socket_output_buttons = '<?php echo $this->lang->line("Buttons"); ?>';
        var facebook_media_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';
        var facebook_media_html_label_media_url = '<?php echo $this->lang->line("Facebook Media URL"); ?>';
        var facebook_media_html_placeholder_page_example = '<?php echo $this->lang->line("Put your Facebook page media url"); ?>';

        var facebook_media_modal_title = '<?php echo $this->lang->line("Facebook Media URL"); ?>';
        var facebook_media_modal_msg_media_url = '<?php echo $this->lang->line("Please provide a valid URL."); ?>';

        var file_name = '<?php echo $this->lang->line("File"); ?>';
        var file_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var file_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var file_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';
        var file_html_label_media_url = '<?php echo $this->lang->line("Please provide file url") ?>';
        var file_html_ph_media_url = '<?php echo $this->lang->line("Put file url here or click the upload box."); ?>';
        var file_html_info_supported_media = '<?php echo $this->lang->line("Supported media types: doc, docx, pdf, txt, ppt, pptx, xls, xlsx"); ?>';

        var file_modal_title = '<?php echo $this->lang->line("Configure File"); ?>';
        var file_modal_msg_file = '<?php echo $this->lang->line("Please upload a valid file."); ?>';

        var generic_template_name = '<?php echo $this->lang->line("Card"); ?>';
        var generic_template_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var generic_template_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var generic_template_socket_output_buttons = '<?php echo $this->lang->line("Buttons"); ?>';
        var generic_template_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';
        var generic_template_html_label_reply_img_url = '<?php echo $this->lang->line("Please provide your reply image"); ?>';
        var generic_template_html_label_dest_img_link = '<?php echo $this->lang->line("Image click destination link"); ?>';
        var generic_template_html_label_title = '<?php echo $this->lang->line("Title"); ?>';
        var generic_template_html_label_subtitle = '<?php echo $this->lang->line("Subtitle"); ?>';
        var generic_template_html_placeholder_reply_img_url = '<?php echo $this->lang->line("Put your image url here or click the upload box."); ?>';
        var generic_template_html_info_supported_media = '<?php echo $this->lang->line("Supported types: png, jpg, gif"); ?>';

        var generic_template_modal_title = '<?php echo $this->lang->line("Configure Generic Template"); ?>';
        var generic_template_modal_msg_title = '<?php echo $this->lang->line("Please provide a title."); ?>';

        var image_name = '<?php echo $this->lang->line("Image"); ?>';
        var image_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var image_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var image_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';
        var image_html_label_img_media_url = '<?php echo $this->lang->line("Please provide your reply image"); ?>';
        var image_html_placeholder_img_url = '<?php echo $this->lang->line("Put your image url here or click the upload box."); ?>';
        var image_html_info_supported_media = '<?php echo $this->lang->line("Supported types: png, jpg, gif"); ?>';

        var image_modal_title = '<?php echo $this->lang->line("Configure Image"); ?>';
        var image_modal_msg_image = '<?php echo $this->lang->line("Please upload a valid image."); ?>';

        var postback_new_name = '<?php echo $this->lang->line("New Postback"); ?>';
        var postback_new_socket_input_trigger = '<?php echo $this->lang->line("Trigger"); ?>';
        var postback_new_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var postback_new_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var postback_new_socket_output_sequence = '<?php echo $this->lang->line("Sequence"); ?>';
        var postback_new_socket_output_user_input_flow = '<?php echo $this->lang->line("User Input Flow"); ?>';
        var postback_new_html_label_title = '<?php echo $this->lang->line("Title"); ?>';
        var postback_new_html_label = '<?php echo $this->lang->line("Choose label(s)"); ?>';
        var postback_new_html_sequence = '<?php echo $this->lang->line("Choose sequence"); ?>';

        var postback_new_html_info_sequence = '<?php echo $this->lang->line("You are going to change the sequence value. If you do so, then the components, created by choosing 'New sequence' previously, will be lost. If you want so, click on the 'OK' button, otherwise, click on 'Cancel' button."); ?>';

        var postback_new_modal_title = '<?php echo $this->lang->line("Configure New Postback"); ?>';
        var postback_new_modal_msg_title = '<?php echo $this->lang->line("Please provide a title."); ?>';

        var otn_name = '<?php echo $this->lang->line("OTN"); ?>';
        var otn_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var otn_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var otn_html_label_title = '<?php echo $this->lang->line("Title"); ?>';
        var otn_html_label_postback_id = '<?php echo $this->lang->line("OTN postback ID"); ?>';
        var otn_html_sequence_info = '<?php echo $this->lang->line("You are going to change the OTN postback ID. If you do so, then the components, created by choosing 'New OTN' previously, will be lost. If you want so, click on the 'OK' button, otherwise, click on 'Cancel' button."); ?>';

        var otn_modal_title = '<?php echo $this->lang->line("Configure One Time Notification"); ?>';
        var otn_modal_msg_title = '<?php echo $this->lang->line("Please provide a title."); ?>';
        var otn_modal_msg_postback_id = '<?php echo $this->lang->line("Please choose a OTN postback ID."); ?>';


        var otn_single_name = '<?php echo $this->lang->line("New OTN"); ?>';
        var otn_single_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var otn_single_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var otn_single_socket_output_next_sequence = '<?php echo $this->lang->line("Sequence"); ?>';

        var otn_single_modal_title = '<?php echo $this->lang->line("Configure New OTN"); ?>';

        var otn_single_modal_template_name_msg = '<?php echo $this->lang->line("Please provide a template name."); ?>';

        var otn_single_html_template_name = '<?php echo $this->lang->line("Template name"); ?>';
        var otn_single_html_labels = '<?php echo $this->lang->line("Label(s)"); ?>';
        var otn_single_html_sequence = '<?php echo $this->lang->line("Choose sequence"); ?>';
        var otn_single_html_sequence_info = '<?php echo $this->lang->line("You are going to change the sequence value. If you do so, then the components, created by choosing 'New sequence' previously, will be lost. If you want so, click on the 'OK' button, otherwise, click on 'Cancel' button."); ?>';

        var otn_single_template_template_name = '<?php echo $this->lang->line("Template name"); ?>';
        var otn_single_template_labels = '<?php echo $this->lang->line("Label(s)"); ?>';
        var otn_single_template_sequence_name = '<?php echo $this->lang->line("Sequence"); ?>';

        var quick_reply_name = '<?php echo $this->lang->line("Quick Reply"); ?>';
        var quick_reply_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var quick_reply_socket_output_new_postback = '<?php echo $this->lang->line("NewPostback"); ?>';
        var quick_reply_html_title_select = '<?php echo $this->lang->line("Select"); ?>';
        var quick_reply_html_label_reply_type = '<?php echo $this->lang->line("Quick reply type"); ?>';
        var quick_reply_html_label_button_text = '<?php echo $this->lang->line("Button text"); ?>';
        var quick_reply_html_label_postback_id = '<?php echo $this->lang->line("Postback ID"); ?>';

        var quick_reply_html_reply_types_newPostback = '<?php echo $this->lang->line("New Postback"); ?>';
        var quick_reply_html_reply_types_postback = '<?php echo $this->lang->line("Postback"); ?>';
        var quick_reply_html_reply_types_phone = '<?php echo $this->lang->line("Phone"); ?>';
        var quick_reply_html_reply_types_email = '<?php echo $this->lang->line("Email"); ?>';

        var quick_reply_modal_title = '<?php echo $this->lang->line("Configure Quick Reply"); ?>';
        var quick_reply_modal_msg_reply_type = '<?php echo $this->lang->line("Please choose a reply type."); ?>';
        var quick_reply_modal_msg_button_text = '<?php echo $this->lang->line("Please name after the button."); ?>';
        var quick_reply_modal_msg_postback_id = '<?php echo $this->lang->line("Please choose a postback."); ?>';

        var postback_name = '<?php echo $this->lang->line("Start Bot Flow"); ?>';
        var postback_socket_input_trigger = '<?php echo $this->lang->line("Trigger"); ?>';
        var postback_socket_input_action_button = '<?php echo $this->lang->line("Action Button"); ?>';
        var postback_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var postback_html_label_title = '<?php echo $this->lang->line("Title"); ?>';
        var postback_html_label_label = '<?php echo $this->lang->line("Choose label(s)"); ?>';
        var postback_html_label_create_new = '<?php echo $this->lang->line("Add new"); ?>';
        var postback_html_label_create_new_info = '<?php echo $this->lang->line("To create new label, write down the name of the label and hit enter button."); ?>';
        var postback_html_label_could_not_create = '<?php echo $this->lang->line("Could not create the label."); ?>';
        var postback_html_label_sequence = '<?php echo $this->lang->line("Choose sequence"); ?>';

        var postback_modal_title = '<?php echo $this->lang->line("Configure Reference"); ?>';
        var postback_modal_msg_title = '<?php echo $this->lang->line("Please provide a title."); ?>';

        var sequence_name = '<?php echo $this->lang->line("New Sequence"); ?>';
        var sequence_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var sequence_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var sequence_html_title_select = '<?php echo $this->lang->line("Select"); ?>';
        var sequence_html_label_name = '<?php echo $this->lang->line("Sequence Name"); ?>';
        var sequence_html_label_start_time = '<?php echo $this->lang->line("Starting Time"); ?>';
        var sequence_html_label_closing_time = '<?php echo $this->lang->line("Closing Time"); ?>';
        var sequence_html_label_timezone = '<?php echo $this->lang->line("Time Zone"); ?>';
        var sequence_html_label_message_tag = '<?php echo $this->lang->line("Message Tag"); ?>';

        var sequence_html_info_non_promotional_requirements = '<?php echo $this->lang->line("The following fields are required for non-promotional daily sequences."); ?>';

        var sequence_modal_title = '<?php echo $this->lang->line("Configure New Sequence"); ?>';
        var sequence_modal_msg_name = '<?php echo $this->lang->line("Sequence name is required."); ?>';

        var sequence_template_start_time = '<?php echo $this->lang->line("Starting Time"); ?>';
        var sequence_template_closing_time = '<?php echo $this->lang->line("Closing Time"); ?>';
        var sequence_template_timezone = '<?php echo $this->lang->line("Timezone"); ?>';
        var sequence_template_message_tag = '<?php echo $this->lang->line("Message Tag"); ?>';

        var sequence_single_name = '<?php echo $this->lang->line("Sequence Item"); ?>';
        var sequence_single_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var sequence_single_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';

        var sequence_single_html_title_select = '<?php echo $this->lang->line("Select"); ?>';
        var sequence_single_html_title_daily = '<?php echo $this->lang->line("Daily"); ?>';
        var sequence_single_html_title_hours_24 = '<?php echo $this->lang->line("24 hours"); ?>';

        var sequence_single_html_label_promotional = '<?php echo $this->lang->line("Promotional"); ?>';
        var sequence_single_html_label_non_promotional = '<?php echo $this->lang->line("Non-promotional"); ?>';
        var sequence_single_html_label_hours = '<?php echo $this->lang->line("Hours"); ?>';
        var sequence_single_html_label_days = '<?php echo $this->lang->line("Days"); ?>';

        var sequence_single_html_hours_min_1 = '<?php echo $this->lang->line("1 min"); ?>';
        var sequence_single_html_hours_mins_5 = '<?php echo $this->lang->line("5 mins"); ?>';
        var sequence_single_html_hours_mins_15 = '<?php echo $this->lang->line("15 mins"); ?>';
        var sequence_single_html_hours_mins_30 = '<?php echo $this->lang->line("30 mins"); ?>';
        var sequence_single_html_hours_mins_45 = '<?php echo $this->lang->line("45 mins"); ?>';
        var sequence_single_html_hours_hour_1 = '<?php echo $this->lang->line("1 hour"); ?>';
        var sequence_single_html_hours_hours_2 = '<?php echo $this->lang->line("2 hours"); ?>';
        var sequence_single_html_hours_hours_3 = '<?php echo $this->lang->line("3 hours"); ?>';
        var sequence_single_html_hours_hours_4 = '<?php echo $this->lang->line("4 hours"); ?>';
        var sequence_single_html_hours_hours_5 = '<?php echo $this->lang->line("5 hours"); ?>';
        var sequence_single_html_hours_hours_6 = '<?php echo $this->lang->line("6 hours"); ?>';
        var sequence_single_html_hours_hours_7 = '<?php echo $this->lang->line("7 hours"); ?>';
        var sequence_single_html_hours_hours_8 = '<?php echo $this->lang->line("8 hours"); ?>';
        var sequence_single_html_hours_hours_9 = '<?php echo $this->lang->line("9 hours"); ?>';
        var sequence_single_html_hours_hours_10 = '<?php echo $this->lang->line("10 hours"); ?>';
        var sequence_single_html_hours_hours_11 = '<?php echo $this->lang->line("11 hours"); ?>';
        var sequence_single_html_hours_hours_12 = '<?php echo $this->lang->line("12 hours"); ?>';
        var sequence_single_html_hours_hours_13 = '<?php echo $this->lang->line("13 hours"); ?>';
        var sequence_single_html_hours_hours_14 = '<?php echo $this->lang->line("14 hours"); ?>';
        var sequence_single_html_hours_hours_15 = '<?php echo $this->lang->line("15 hours"); ?>';
        var sequence_single_html_hours_hours_16 = '<?php echo $this->lang->line("16 hours"); ?>';
        var sequence_single_html_hours_hours_17 = '<?php echo $this->lang->line("17 hours"); ?>';
        var sequence_single_html_hours_hours_18 = '<?php echo $this->lang->line("18 hours"); ?>';
        var sequence_single_html_hours_hours_19 = '<?php echo $this->lang->line("19 hours"); ?>';
        var sequence_single_html_hours_hours_20 = '<?php echo $this->lang->line("20 hours"); ?>';
        var sequence_single_html_hours_hours_21 = '<?php echo $this->lang->line("21 hours"); ?>';
        var sequence_single_html_hours_hours_22 = '<?php echo $this->lang->line("22 hours"); ?>';
        var sequence_single_html_hours_hours_23 = '<?php echo $this->lang->line("23 hours"); ?>';

        var sequence_single_html_days_day_1 = '<?php echo $this->lang->line("Day-1"); ?>';
        var sequence_single_html_days_day_2 = '<?php echo $this->lang->line("Day-2"); ?>';
        var sequence_single_html_days_day_3 = '<?php echo $this->lang->line("Day-3"); ?>';
        var sequence_single_html_days_day_4 = '<?php echo $this->lang->line("Day-4"); ?>';
        var sequence_single_html_days_day_5 = '<?php echo $this->lang->line("Day-5"); ?>';
        var sequence_single_html_days_day_6 = '<?php echo $this->lang->line("Day-6"); ?>';
        var sequence_single_html_days_day_7 = '<?php echo $this->lang->line("Day-7"); ?>';
        var sequence_single_html_days_day_8 = '<?php echo $this->lang->line("Day-8"); ?>';
        var sequence_single_html_days_day_9 = '<?php echo $this->lang->line("Day-9"); ?>';
        var sequence_single_html_days_day_10 = '<?php echo $this->lang->line("Day-10"); ?>';
        var sequence_single_html_days_day_11 = '<?php echo $this->lang->line("Day-11"); ?>';
        var sequence_single_html_days_day_12 = '<?php echo $this->lang->line("Day-12"); ?>';
        var sequence_single_html_days_day_13 = '<?php echo $this->lang->line("Day-13"); ?>';
        var sequence_single_html_days_day_14 = '<?php echo $this->lang->line("Day-14"); ?>';
        var sequence_single_html_days_day_15 = '<?php echo $this->lang->line("Day-15"); ?>';
        var sequence_single_html_days_day_16 = '<?php echo $this->lang->line("Day-16"); ?>';
        var sequence_single_html_days_day_17 = '<?php echo $this->lang->line("Day-17"); ?>';
        var sequence_single_html_days_day_18 = '<?php echo $this->lang->line("Day-18"); ?>';
        var sequence_single_html_days_day_19 = '<?php echo $this->lang->line("Day-19"); ?>';
        var sequence_single_html_days_day_20 = '<?php echo $this->lang->line("Day-20"); ?>';
        var sequence_single_html_days_day_21 = '<?php echo $this->lang->line("Day-21"); ?>';
        var sequence_single_html_days_day_22 = '<?php echo $this->lang->line("Day-22"); ?>';
        var sequence_single_html_days_day_23 = '<?php echo $this->lang->line("Day-23"); ?>';
        var sequence_single_html_days_day_24 = '<?php echo $this->lang->line("Day-24"); ?>';
        var sequence_single_html_days_day_25 = '<?php echo $this->lang->line("Day-25"); ?>';
        var sequence_single_html_days_day_26 = '<?php echo $this->lang->line("Day-26"); ?>';
        var sequence_single_html_days_day_27 = '<?php echo $this->lang->line("Day-27"); ?>';
        var sequence_single_html_days_day_28 = '<?php echo $this->lang->line("Day-28"); ?>';
        var sequence_single_html_days_day_29 = '<?php echo $this->lang->line("Day-29"); ?>';
        var sequence_single_html_days_day_30 = '<?php echo $this->lang->line("Day-30"); ?>';

        var sequence_single_modal_title = '<?php echo $this->lang->line("Configure New Postback"); ?>';
        var sequence_single_modal_msg_promotional = '<?php echo $this->lang->line("Please provide value for promotional."); ?>';
        var sequence_single_modal_msg_non_promotional = '<?php echo $this->lang->line("Please provide value for non-promotional."); ?>';
        var sequence_single_modal_msg_promotional_or_non_promotional = '<?php echo $this->lang->line("Please choose either promotional or non-promotional."); ?>';

        var text_name = '<?php echo $this->lang->line("Text"); ?>';
        var text_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var text_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var text_socket_output_buttons = '<?php echo $this->lang->line("Buttons"); ?>';
        var text_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';

        var text_html_title_custom = '<?php echo $this->lang->line("Custom"); ?>';
        var text_html_title_first_name = '<?php echo $this->lang->line("F. Name"); ?>';
        var text_html_title_last_name = '<?php echo $this->lang->line("L. Name"); ?>';

        var text_html_instagram_tag = '<?php echo $this->lang->line("Mention"); ?>';
        var text_html_instagram_username = '<?php echo $this->lang->line("Full Name"); ?>';
        var text_html_instagram_username_info = '<?php echo $this->lang->line("You can include #LEAD_FULL_NAME# variable inside your message. The variable will be replaced by real full name when we will send it."); ?>';

        var text_html_label_reply_message = '<?php echo $this->lang->line("Please provide your reply message"); ?>';

        var text_html_info_custom_variable = '<?php echo $this->lang->line("The custom variable will be replaced by actual value before sending it."); ?>';
        var text_html_info_firstname = '<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it."); ?>';
        var text_html_info_lastname = '<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it."); ?>';

        var text_modal_title = '<?php echo $this->lang->line("Configure Text Message"); ?>';
        var text_modal_msg_reply_message = '<?php echo $this->lang->line("Please write a reply message."); ?>';

        var text_with_buttons_name = '<?php echo $this->lang->line("Text With Buttons"); ?>';

        var trigger_name = '<?php echo $this->lang->line("Trigger Keywords"); ?>';

        var trigger_html_label_keywords = '<?php echo $this->lang->line("Write keywords separating by comma."); ?>';
        var trigger_html_placeholder_keywords = '<?php echo $this->lang->line("Hello, Hi, Start"); ?>';
        var trigger_html_info_keywords = '<?php echo $this->lang->line("Comma separated keywords for which the bot will be triggered"); ?>';
        var trigger_html_matching_type = '<?php echo $this->lang->line("Send reply based on your matching type."); ?>';
        var trigger_html_exact_match = '<?php echo $this->lang->line("Exact keyword match"); ?>';
        var trigger_html_string_match = '<?php echo $this->lang->line("String match"); ?>';


        var trigger_modal_title = '<?php echo $this->lang->line("Configure Trigger"); ?>';
        var trigger_modal_msg_keywords = '<?php echo $this->lang->line("Please write a keyword."); ?>';

        var user_input_flow_name = '<?php echo $this->lang->line("User Input Flow"); ?>';
        var user_input_flow_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var user_input_flow_socket_input_questions = '<?php echo $this->lang->line("Questions"); ?>';
        var user_input_flow_html_label_flow = '<?php echo $this->lang->line("User input flow"); ?>';
        var user_input_flow_html_label_campaign = '<?php echo $this->lang->line("Campaign name"); ?>';

        var user_input_flow_modal_title = '<?php echo $this->lang->line("Configure User-Input-Flow"); ?>';
        var user_input_flow_modal_msg_flow = '<?php echo $this->lang->line("Please choose a user input flow ID."); ?>';

        var user_input_flow_single_name = '<?php echo $this->lang->line("New Question"); ?>';
        var user_input_flow_single_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var user_input_flow_single_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var user_input_flow_single_socket_output_final_reply = '<?php echo $this->lang->line("Final Reply"); ?>';

        var user_input_flow_single_html_question_type = '<?php echo $this->lang->line("Choose question type"); ?>';
        var user_input_flow_single_html_question_type_keyboard = '<?php echo $this->lang->line("Free keyboard input"); ?>';
        var user_input_flow_single_html_question_type_multiple = '<?php echo $this->lang->line("Multiple choice"); ?>';

        var user_input_flow_single_html_question = '<?php echo $this->lang->line("Question"); ?>';
        var user_input_flow_single_html_question_placeholder = '<?php echo $this->lang->line("Put your question here"); ?>';
        var user_input_flow_single_html_question_option1_placeholder = '<?php echo $this->lang->line("Option 1"); ?>';
        var user_input_flow_single_html_question_option2_placeholder = '<?php echo $this->lang->line("Option 2"); ?>';
        var user_input_flow_single_html_question_option3_placeholder = '<?php echo $this->lang->line("Option 3"); ?>';
        var user_input_flow_single_html_question_option4_placeholder = '<?php echo $this->lang->line("Option 4"); ?>';
        var user_input_flow_single_html_question_another_option_placeholder = '<?php echo $this->lang->line("Another option"); ?>';
        var user_input_flow_single_html_question_option_add_more = '<?php echo $this->lang->line("Add more"); ?>';

        var user_input_flow_single_html_reply_type = '<?php echo $this->lang->line("Reply type"); ?>';
        var user_input_flow_single_html_reply_type_email = '<?php echo $this->lang->line("Email"); ?>';
        var user_input_flow_single_html_reply_type_phone = '<?php echo $this->lang->line("Phone"); ?>';
        var user_input_flow_single_html_reply_type_text = '<?php echo $this->lang->line("Text"); ?>';
        var user_input_flow_single_html_reply_type_number = '<?php echo $this->lang->line("Number"); ?>';
        var user_input_flow_single_html_reply_type_url = '<?php echo $this->lang->line("URL"); ?>';
        var user_input_flow_single_html_reply_type_file = '<?php echo $this->lang->line("File"); ?>';
        var user_input_flow_single_html_reply_type_image = '<?php echo $this->lang->line("Image"); ?>';
        var user_input_flow_single_html_reply_type_video = '<?php echo $this->lang->line("Video"); ?>';
        var user_input_flow_single_html_reply_type_date = '<?php echo $this->lang->line("Date"); ?>';
        var user_input_flow_single_html_reply_type_time = '<?php echo $this->lang->line("Time"); ?>';
        var user_input_flow_single_html_reply_type_datetime = '<?php echo $this->lang->line("Datetime"); ?>';

        var user_input_flow_single_html_quick_reply_email = '<?php echo $this->lang->line("Attach email quick-reply"); ?>';
        var user_input_flow_single_html_quick_reply_phone = '<?php echo $this->lang->line("Attach phone quick-reply"); ?>';

        var user_input_flow_single_html_custom_field = '<?php echo $this->lang->line("Save to custom field"); ?>';
        var user_input_flow_single_html_system_field = '<?php echo $this->lang->line("Save to system field"); ?>';

        var user_input_flow_single_html_system_field_select = '<?php echo $this->lang->line("Please select"); ?>';
        var user_input_flow_single_html_system_field_email = '<?php echo $this->lang->line("Email"); ?>';
        var user_input_flow_single_html_system_field_phone = '<?php echo $this->lang->line("Phone"); ?>';
        var user_input_flow_single_html_system_field_birthday = '<?php echo $this->lang->line("Birthday"); ?>';
        var user_input_flow_single_html_system_field_location = '<?php echo $this->lang->line("Location"); ?>';

        var user_input_flow_single_html_assign_to_labels = '<?php echo $this->lang->line("Assign to labels"); ?>';
        var user_input_flow_single_html_assign_to_labels_create_new = '<?php echo $this->lang->line("Add new"); ?>';
        var user_input_flow_single_html_assign_to_labels_create_new_info = '<?php echo $this->lang->line("To create new label, write down the name of the label and hit enter button."); ?>';
        var user_input_flow_single_html_assign_to_messenger_sequence = '<?php echo $this->lang->line("Assign to a messenger sequence"); ?>';
        var user_input_flow_single_html_assign_to_email_phone_sequence = '<?php echo $this->lang->line("Assign to a email/phone sequence"); ?>';

        var user_input_flow_single_html_skip_button_text = '<?php echo $this->lang->line("Skip button text"); ?>';
        var user_input_flow_single_html_skip_button_text_placeholder = '<?php echo $this->lang->line("Put your skip button text here"); ?>';

        var user_input_flow_single_modal_title = '<?php echo $this->lang->line("Configure New Question"); ?>';

        var user_input_flow_single_modal_msg_question_type = '<?php echo $this->lang->line("Choose a quesiton type."); ?>';
        var user_input_flow_single_modal_msg_question = '<?php echo $this->lang->line("Write down your quesiton."); ?>';
        var user_input_flow_single_modal_msg_multiple_choices = '<?php echo $this->lang->line("Fill in at least two multiple-choice options."); ?>';

        var user_input_flow_single_template_question = '<?php echo $this->lang->line("Question"); ?>';
        var user_input_flow_single_template_options = '<?php echo $this->lang->line("Options"); ?>';
        var user_input_flow_single_template_reply_type = '<?php echo $this->lang->line("Reply type"); ?>';
        var user_input_flow_single_template_quick_reply_email = '<?php echo $this->lang->line("Attach email quick-reply"); ?>';
        var user_input_flow_single_template_quick_reply_phone = '<?php echo $this->lang->line("Attach phone quick-reply"); ?>';
        var user_input_flow_single_template_quick_reply_answer_yes = '<?php echo $this->lang->line("Yes"); ?>';
        var user_input_flow_single_template_custom_field = '<?php echo $this->lang->line("Save to custom field"); ?>';
        var user_input_flow_single_template_system_field = '<?php echo $this->lang->line("Save to system field"); ?>';
        var user_input_flow_single_template_assign_to_labels = '<?php echo $this->lang->line("Assign to labels"); ?>';
        var user_input_flow_single_template_assign_to_messenger_sequence = '<?php echo $this->lang->line("Assign to a messenger sequence"); ?>';
        var user_input_flow_single_template_assign_to_email_phone_sequence = '<?php echo $this->lang->line("Assign to a email/phone sequence"); ?>';
        var user_input_flow_single_template_skip_button_text = '<?php echo $this->lang->line("Skip button text"); ?>';

        var video_name = '<?php echo $this->lang->line("Video"); ?>';
        var video_socket_input_reply = '<?php echo $this->lang->line("Reply"); ?>';
        var video_socket_output_next = '<?php echo $this->lang->line("Next"); ?>';
        var video_socket_output_quick_replies = '<?php echo $this->lang->line("Quick Replies"); ?>';
        var video_html_label_video_url = '<?php echo $this->lang->line("Please provide your reply video url"); ?>';
        var video_html_placeholder_video_url = '<?php echo $this->lang->line("Put your video url here or click the upload box."); ?>';
        var video_html_info_supported_media = '<?php echo $this->lang->line("Supported types: mp4, flv, wmv"); ?>';

        var video_modal_title = '<?php echo $this->lang->line("Configure Video"); ?>';
        var video_modal_msg_video = '<?php echo $this->lang->line("Upload a properly encoded video."); ?>';

        var delay_html_label_typing_display = '<?php echo $this->lang->line("Typing on display"); ?>';
        var delay_html_label_delay_in_reply = '<?php echo $this->lang->line("Delay in reply"); ?>';

        var delay_html_info_sec = '<?php echo $this->lang->line("sec"); ?>';
        var delay_html_msg_delay_range = '<?php echo $this->lang->line("The delay range is between 1 to 60 sec."); ?>';

        var common_button_ok = '<?php echo $this->lang->line("Done"); ?>';
        var common_button_cancel = '<?php echo $this->lang->line("Cancel"); ?>';

        var common_info_sec = '<?php echo $this->lang->line("Sec"); ?>';
        var common_info_error = '<?php echo $this->lang->line("Error"); ?>';
        var common_info_success = '<?php echo $this->lang->line("Success"); ?>';
        var common_info_warning = '<?php echo $this->lang->line("Warning"); ?>';
        var common_info_typing_display = '<?php echo $this->lang->line("Typing Display"); ?>';
        var common_info_audio_not_supported = '<?php echo $this->lang->line("Your browser does not support the audio tag."); ?>';
        var common_info_video_not_supported = '<?php echo $this->lang->line("Your browser does not support the video tag."); ?>';

        var others_buttons_title_back = '<?php echo $this->lang->line("Back"); ?>';
        var others_buttons_title_rearrange = '<?php echo $this->lang->line("Rearrange"); ?>';
        var others_buttons_title_save = '<?php echo $this->lang->line("Save Ctrl+S"); ?>';
        var others_buttons_title_success = '<?php echo $this->lang->line("Success!"); ?>';

        var others_buttons_msg_components_required = '<?php echo $this->lang->line("Please add some more components."); ?>';
        var others_buttons_msg_components_components_connection_required = '<?php echo $this->lang->line("All components should be connected."); ?>';
        var others_buttons_msg_components_components_data_required = '<?php echo $this->lang->line("Some component(s) have no data."); ?>';
        var others_buttons_msg_components_new_postback_next_connection_required = '<?php echo $this->lang->line("New postback's Next must have connection"); ?>';
        var others_buttons_msg_components_non_promotional_data_required = '<?php echo $this->lang->line("Provide data for non-promotional sequence."); ?>';

        var utils_title_success = '<?php echo $this->lang->line("Success!"); ?>';
        var utils_title_warning = '<?php echo $this->lang->line("Warning!"); ?>';
        var utils_title_info = '<?php echo $this->lang->line("Info!"); ?>';
        var utils_title_error = '<?php echo $this->lang->line("Error!"); ?>';

        var utils_button_ok = '<?php echo $this->lang->line("Done"); ?>';
        var utils_button_cancel = '<?php echo $this->lang->line("Cancel"); ?>';

        var swal_button_ok = '<?php echo $this->lang->line("Done"); ?>';
        var swal_button_cancel = '<?php echo $this->lang->line("Cancel"); ?>';

        var plugin_component = '<?php echo $this->lang->line("Component"); ?>';
        var plugin_components = '<?php echo $this->lang->line("Components"); ?>';
        var plugin_incompatible_connection = '<?php echo $this->lang->line("You made an incompatible connection"); ?>';
        var plugin_incompatible_connection_user_input_flow_builder = '<?php echo $this->lang->line("Only New Question and New Postback are allowed to connect. Please add a New Postback to complete the flow."); ?>';
        var plugin_connection_limit = '<?php echo $this->lang->line("You cannot connect more than"); ?>';
        var plugin_recursion_problem = '<?php echo $this->lang->line("Connection removed due to recursion"); ?>';
        <?php
        exit;    
    }

    public function check_field_value_exists($builder_table_id, $table_type, $field_value) 
    {
        // $this->ajax_check();

        $table_map = [
            'checkbox_plugin' => 'messenger_bot_engagement_checkbox',
            'send_to_messenger' => 'messenger_bot_engagement_send_to_msg',
            'm_me_link' => 'messenger_bot_engagement_mme',
            'customer_chat_plugin' => 'messenger_bot_engagement_2way_chat_plugin',
        ];

        $table = !empty($table_map[$table_type]) ? $table_map[$table_type] : '';

        if (!$table) {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line('Could not determine database')
            ]);

            exit;
        }

        $value = addslashes(trim(strip_tags($field_value)));
        $where = [
            'where' => [
                'reference' => $value,
            ],
        ];

        if (0 != $builder_table_id) {
            $where['where']['visual_flow_campaign_id !='] = $builder_table_id;
        }

        $info = $this->basic->get_data($table, $where, ['id'], '', 1);

        if (!empty($info[0]['id'])) {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line('The reference value already exists.')
            ]);

            exit;
        }

        echo json_encode([
            'status' => true,
            'message' => $this->lang->line('The reference value may be used.')
        ]);
    }
}