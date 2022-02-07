<?php 

require_once("Home.php"); // loading home controller

class Sms_email_manager extends Home
{
    public $user_id;

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

    public function __construct()
    {

        parent::__construct();
        $function_name = $this->uri->segment(2);
        
        if($function_name != "checking_open_rate" && $function_name != "checking_click_rate") {
            if ($this->session->userdata('logged_in') != 1) {
                redirect('home/login_page', 'location');
            }
        }

        $this->load->library('Sms_manager');
        $this->load->library('Email_manager');

        set_time_limit(0);
        $this->important_feature();
        $this->member_validity();
    }


    public function index()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) {
            redirect('home/login_page', 'location');
        }

        $data['body'] = 'sms_email_manager/section_menu_block';
        $data['page_title'] = $this->lang->line('SMS/ Email Manager');
        $this->_viewcontroller($data);
    }

    // SMS API Section Started
    public function sms_api_lists()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) {
            redirect('home/login_page', 'location');
        }

        $data['body'] = 'sms_email_manager/sms/sms_api';
        $data['gateway_lists'] = $this->_api_gateways();
        $data['page_title'] = $this->lang->line('SMS API');
        $this->_viewcontroller($data);   
    }

    public function sms_api_list_data()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;
        
        $this->ajax_check();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','gateway_name','phone_number','status','actions');
        $search_columns = array('gateway_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple = array();

        $where_simple['deleted'] = '0';
        $where_simple['user_id'] = $this->user_id;

        if($search_value != "")
        {
        	foreach ($search_columns as $key => $value) 
        		$where_simple[$value.' LIKE '] = "%$search_value%";
        }

        $where  = array('where'=>$where_simple);

        $table = "sms_api_config";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        $total_rows_array = $this->basic->count_row($table,$where,$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];


        for($i = 0; $i < count($info); $i++)
        {

            $status = $info[$i]["status"];
            if($status=='1') $info[$i]["status"] = "<i title ='".$this->lang->line('Active')."'class='status-icon fas fa-toggle-on text-primary'></i>";
            else $info[$i]["status"] = "<i title ='".$this->lang->line('Inactive')."'class='status-icon fas fa-toggle-off gray'></i>";

            if(isset($info[$i]['phone_number']) && $info[$i]['phone_number'] !="")
                $info[$i]['phone_number'] = $info[$i]['phone_number'];
            else
                $info[$i]['phone_number'] = "-";

            $info[$i]['actions'] = "<div style='min-width:100px;'><a href='#' title='".$this->lang->line("Send Test SMS")."' class='btn btn-circle btn-outline-primary test_sms' gateway_name='".$info[$i]['gateway_name']."' table_id='".$info[$i]['id']."'><i class='fa fa-paper-plane'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' title='".$this->lang->line("View Details")."' class='btn btn-circle btn-outline-info see_api_details' table_id='".$info[$i]['id']."'><i class='fas fa-info-circle'></i></a>&nbsp;&nbsp;";
            

            $edit_class = '';
            if ($info[$i]['gateway_name'] == 'custom') {
                $edit_class = 'edit_custom_api';
                $info[$i]['gateway_name'] = $this->lang->line("Custom")." - ". $info[$i]['custom_name'];
            } else if ($info[$i]['gateway_name'] == 'custom_post') {
                $edit_class = 'edit_custom_post_api';
                $info[$i]['gateway_name'] = $this->lang->line("Custom")." - ". $info[$i]['custom_name'];
            }  else {
                $edit_class = 'edit_api';
            }

            $info[$i]['actions'] .= "<a href='#' title='".$this->lang->line("Edit API")."' class='btn btn-circle btn-outline-warning ". $edit_class . "' gateway='".$info[$i]['gateway_name']."' table_id='".$info[$i]['id']."'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' title='".$this->lang->line("Delete API")."' class='btn btn-circle btn-outline-danger delete_api' table_id='".$info[$i]['id']."'><i class='fa fa-trash-alt'></i></a></div>
                <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }



    public function api_infos()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

    	$this->ajax_check();

    	$res = array();
    	$table_id = $this->input->post("table_id");

    	$where_simple = array();

    	$where_simple['deleted'] = '0';
    	$where_simple['user_id'] = $this->user_id;
    	$where_simple['id'] = $table_id;

    	$where  = array('where'=>$where_simple);

        /* Gettings Corresponding API informations */
    	$api_info = $this->basic->get_data("sms_api_config",$where);

    	$res['gateway_name'] = $api_info[0]['gateway_name'];
        $res['username_auth_id'] = $api_info[0]['username_auth_id'];
    	$res['password_auth_token'] = $api_info[0]['password_auth_token'];
    	$res['api_id'] = $api_info[0]['api_id'];
        $res['hostname'] = $api_info[0]['routesms_host_name'];

    	$this->sms_manager->set_credentioal($table_id,$this->user_id);

        /* Getting balance of SMS Gateway */
    	$res['remaining_credetis'] = "-";
    	if($api_info[0]['gateway_name'] == "plivo") 
    		$res['remaining_credetis'] = $this->sms_manager->get_plivo_balance();
    	if($api_info[0]['gateway_name'] == "clickatell") 
    		$res['remaining_credetis'] = $this->sms_manager->get_clickatell_balance();
    	if($api_info[0]['gateway_name'] == "clickatell-platform") 
    		$res['remaining_credetis'] = $this->sms_manager->get_clickatell_platform_balance();
    	if($api_info[0]['gateway_name'] == "nexmo") 
    		$res['remaining_credetis'] = $this->sms_manager->get_nexmo_balance();
    	if($api_info[0]['gateway_name'] == "africastalking.com") 
    		$res['remaining_credetis'] = $this->sms_manager->africastalking_sms_balance();
    	if($api_info[0]['gateway_name'] == "infobip.com") 
    		$res['remaining_credetis'] = $this->sms_manager->infobip_balance_check();
    	if($api_info[0]['gateway_name'] == "Shreeweb") 
    		$res['remaining_credetis'] = $this->sms_manager->get_shreeweb_balance();

        /* returning the result */
    	echo json_encode($res);
    }


    public function ajax_create_sms_api()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

    	$this->ajax_check();

    	$return_response = array();

    	if($_POST)
    	{
    		$post = $_POST;
    		foreach ($post as $key => $value) 
    		{
    		    $$key = trim($this->input->post($key,TRUE));
    		}

            $status_checked = $this->input->post("status");
            if($status_checked == "") $status_checked = "0";

    		$inserted_data = array();
    		$inserted_data['user_id'] = $this->user_id;
    		$inserted_data['gateway_name'] = $gateway_name;
    		$inserted_data['username_auth_id'] = $username_auth_id;
    		$inserted_data['password_auth_token'] = $password_auth_token;
            if($gateway_name != "routesms.com")
                $inserted_data['routesms_host_name'] = "";
            else  
                $inserted_data['routesms_host_name'] = $routesms_host_name;
    		$inserted_data['api_id'] = $api_id;
    		$inserted_data['phone_number'] = $phone_number;
    		$inserted_data['status'] = $status_checked;

    		if($this->basic->insert_data("sms_api_config",$inserted_data))
    		{
    			$return_response['status'] = "1";
    			$return_response['msg']  = $this->lang->line('New API Information has been added successfully');
    			
    		} else
    		{
    			$return_response['status'] = "0";
    			$return_response['msg']  = $this->lang->line('Something went wrong, please try again.');
    		}

    		echo json_encode($return_response);
    	}

    }

    public function send_test_sms()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;
        $this->ajax_check();

        $result = [];
        $table_id = $this->input->post('table_id',true);
        $test_gateway_name = trim($this->input->post('test_gateway_name',true));
        $number = trim($this->input->post('number',true));
        $message = $this->input->post('message',true);
        $user_id = $this->user_id;

        if($table_id == "" || $table_id == 0) exit;

        // set credential for sms api
        $this->sms_manager->set_credentioal($table_id,$user_id);
        $response = $this->sms_manager->send_sms($message, $number);
        
        if($test_gateway_name != 'custom') {
            echo json_encode($response); exit;
        } else if($test_gateway_name == 'custom') {
            unset($response['status']);
            echo json_encode($response); exit;
        } else {
            echo json_encode(['response'=>"something went wrong, please try once again"]); exit;
        }
    }

    public function ajax_get_api_info_for_update()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

    	$this->ajax_check();

    	$table_id = $this->input->post("table_id",true);
    	$gateway_lists = $this->_api_gateways();

    	$where['where'] = array("id"=>$table_id,'user_id'=>$this->user_id);

    	$get_info = $this->basic->get_data("sms_api_config",$where);
    	$gateway_name = isset($get_info[0]['gateway_name']) ? $get_info[0]['gateway_name']: "";
    	$username_auth_id = isset($get_info[0]['username_auth_id']) ? $get_info[0]['username_auth_id']:"";
    	$password_auth_token = isset($get_info[0]['password_auth_token']) ? $get_info[0]['password_auth_token']: "";
    	$api_id = isset($get_info[0]['api_id']) ? $get_info[0]['api_id']: "";
    	$phone_number = isset($get_info[0]['phone_number']) ? $get_info[0]['phone_number']: "";
        $routesmsHostname = isset($get_info[0]['routesms_host_name']) ? $get_info[0]['routesms_host_name']: "";
    	$status = $get_info[0]['status'];

        if($status == "1") $status_checked = "checked";
        else $status_checked = "";

    	$update_data_form = '<div class="row">
                    <div class="col-12">                    
                        <form action="#" enctype="multipart/form-data" id="update_sms_api_form" method="post">
                        	<input type="hidden" name="table_id" id="table_id" value="'.$get_info[0]['id'].'">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('Gateway Name').'</label>'.
                            form_dropdown("gateway_name",$gateway_lists,$gateway_name, "class='form-control select2' id='updated_gateway_name' style='width:100%;'");


        $update_data_form .= '</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('Auth ID/ Auth Key/ API Key/ MSISDN/ Account SID/ Account ID/ Username/ Admin').'</label>
                                        <input type="text" class="form-control" name="username_auth_id" id="updated_username_auth_id" value="'.$username_auth_id.'">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('Auth Token/ API Secret/ Password').'</label>
                                        <input type="text" class="form-control" name="password_auth_token" id="updated_password_auth_token" value="'.$password_auth_token.'">
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6" id="updated_routehostdiv">
                                    <div class="form-group">
                                        <label id="hostname">'.$this->lang->line("Routesms Host Name").'
                                            <a href="#" data-placement="top" data-html="true" data-toggle="popover" title="'.$this->lang->line("Message").'" data-content="'.$this->lang->line("Write your routesms.com registered hostname which was provided from routesms.com. You must include your hostname as given below example formate. Example <b>http://smsplus.routesms.com/</b>").'"><i class="fa fa-info-circle"></i> </a>
                                        </label>
                                        <strong class="float-right">[i.e: http://smsplus.routesms.com/ ]</strong> 
                                        <input type="text" class="form-control" name="routesms_host_name" id="update_routesms_host_name" value="'.$routesmsHostname.'">
                                        <script>$("[data-toggle=\'popover\']").popover();</script>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('API ID').'</label>
                                        <input type="text" class="form-control" name="api_id" id="updated_api_id" value="'.$api_id.'">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('Sender/ Sender ID/ Mask/ From').'</label>
                                        <input type="text" class="form-control" name="phone_number" id="updated_phone_number" value="'.$phone_number.'">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label style="margin-bottom:20px;">'.$this->lang->line('Status').'</label><br>
                                        <label class="custom-switch">
                                            <input type="checkbox" name="status" value="1" id="status" class="custom-switch-input" '.$status_checked.'>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description">'.$this->lang->line('Active').'</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <script>$("#updated_status,#updated_gateway_name").select2();</script>';

        echo $update_data_form;
    }

    public function ajax_update_sms_api()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();

        $return_response = array();

        $table_id = $this->input->post("table_id",true);

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = trim($this->input->post($key,TRUE));
            }

            $status_checked = $this->input->post("status");
            if($status_checked == "") $status_checked = "0";

            $updated_data = array();
            $updated_data['user_id'] = $this->user_id;
            $updated_data['gateway_name'] = $gateway_name;
            $updated_data['username_auth_id'] = $username_auth_id;
            $updated_data['password_auth_token'] = $password_auth_token;
            if($gateway_name != "routesms.com")
                $updated_data['routesms_host_name'] = "";
            else  
                $updated_data['routesms_host_name'] = $routesms_host_name;
            $updated_data['api_id'] = $api_id;
            $updated_data['phone_number'] = $phone_number;
            $updated_data['status'] = $status_checked;

            $where = array("user_id"=>$this->user_id,'id'=>$table_id);

            if($this->basic->update_data("sms_api_config",$where,$updated_data))
            {
                $return_response['status'] = "1";
                $return_response['msg']  = $this->lang->line('API Information has been updated successfully');
                
            } else
            {
                $return_response['status'] = "0";
                $return_response['msg']  = $this->lang->line('Something went wrong, please try again.');
            }

            echo json_encode($return_response);
        }
    }

    public function delete_sms_api()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id",true);

        if($table_id == "" || $table_id == "0") exit;

        if($this->basic->delete_data("sms_api_config",array("id"=>$table_id)))
        {
            if($this->basic->delete_data("sms_sending_campaign",array("api_id"=>$table_id))) {

                $this->basic->delete_data("sms_sending_campaign_send",array("sms_api_id"=>$table_id));
                
            }

            echo "1";

        } else {

            echo "0";
        }
    }
    // End of the SMS API section


    // Phonebook section started
    public function contact_group_list()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0)
            redirect('home/login_page', 'location');

        $per_page = 10;
        $search_value = "";

        // set per_page and search_value from user_submission
        if (isset($_POST['rows_number']) || isset($_POST['search_value'])) {

            $per_page = $this->input->post('rows_number', true);
            $search_value = $this->input->post('search_value', true);

            $this->session->set_userdata('sms_email_contact_group_per_page', $per_page);
            $this->session->set_userdata('sms_email_contact_group_search_value', $search_value);
        }


        // set session so that pagination can get proper per_page & search_value
        if ($this->session->userdata('sms_email_contact_group_per_page')) 
            $per_page = $this->session->userdata('sms_email_contact_group_per_page');

        if ($this->session->userdata('sms_email_contact_group_search_value')) 
            $search_value = $this->session->userdata('sms_email_contact_group_search_value');

        $where['where'] = array('user_id' => $this->user_id, 'type LIKE' => '%'.$search_value.'%');


        $total_group = $this->basic->get_data('sms_email_contact_group', $where,'','','','','id DESC');


        if ($per_page == 'all')
            $per_page = count($total_group);

        /* set cinfiguration for pagination */
        $config = array(
            'uri_segment' => 3,
            'base_url' => base_url('sms_email_manager/contact_group_list/'),
            'total_rows' => count($total_group),
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


        $start = $this->uri->segment(3);
        $limit = $config['per_page'];

        $contact_group = $this->basic->get_data('sms_email_contact_group', array('where' => array('user_id' => $this->user_id, 'type LIKE' => '%'.$search_value.'%')), '', '', $limit, $start, 'id DESC');

        $data['page_title'] = $this->lang->line("Contact Group");
        $data['contactGroups'] = $contact_group;
        $data['page_links'] = $page_links;
        $data['per_page'] = ($per_page == count($total_group)) ? 'all' : $per_page;
        $data['search_value'] = $search_value;
        $data['body'] = "sms_email_manager/contact_book/contact_group";


        $this->_viewcontroller($data);
    }

    public function add_contact_group_action()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();
        $group_name = trim(strip_tags($this->input->post("group_name")));
        $in_data = array(
            'user_id' => $this->user_id,
            'type' => $group_name,
            'created_at' => date("Y-m-d H:i:s")
        );

        if((isset($group_name) && !empty($group_name)) && $this->basic->is_exist("sms_email_contact_group",$where=array("user_id"=>$this->user_id,"type"=>$group_name)))
        {
            echo "2";
            exit;
        }

        if($this->basic->insert_data("sms_email_contact_group", $in_data))
        {
            echo "1";
        } else
        {
            echo "0";
        }
    }

    public function ajax_get_group_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();
        $group_id = $this->input->post("group_id");
        
        if($group_id == "0" || $group_id == "") exit;

        $group_info =$this->basic->get_data("sms_email_contact_group", array('where'=>array("id"=>$group_id, 'user_id'=> $this->user_id)));
        $updateForm = '<div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>'.$this->lang->line('Group Name').'</label>
                            <input type="text" class="form-control" name="group_name" id="update_group_name" value="'.$group_info[0]["type"].'">
                            <input type="hidden" class="form-control" name="table_id" id="table_id" value="'.$group_id.'">
                        </div>
                    </div>
                </div>';

        echo $updateForm;
    }

    public function ajax_update_group_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id");

        if($table_id == "0" || $table_id == "") exit;

        $group_name = trim(strip_tags($this->input->post("group_name")));

        $oldData = $this->basic->get_data("sms_email_contact_group", array("where"=>array("id"=>$table_id,"user_id"=>$this->user_id)),array("type"));

        if($oldData[0]['type'] != $group_name)
        {
            if((isset($group_name) && !empty($group_name)) && $this->basic->is_exist("sms_email_contact_group",$where=array("user_id"=>$this->user_id,"type"=>$group_name)))
            {
                echo "2";
                exit;
            }
        }

        if($this->basic->update_data("sms_email_contact_group",array("id"=>$table_id,'user_id'=>$this->user_id), array("type"=>$group_name)))
        {
            echo "1";
        } else
        {
            echo "0";
        }
    }

    public function delete_contact_group()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == "") exit;

        if($this->basic->delete_data("sms_email_contact_group",array("id"=>$table_id)))
        {
            echo "1";
        } else
        {
            echo "0";
        }
    }

    public function contact_list()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0)
            redirect('home/login_page', 'location');
        
        $this->sms_email_drip_exist = $this->addon_exist('sms_email_sequence');
        $data['body'] = 'sms_email_manager/contact_book/contact_lists';

        $table = 'sms_email_contact_group';
        $where['where'] = array('user_id'=>$this->user_id);

        $info = $this->basic->get_data($table,$where);

        foreach ($info as $key => $value) 
        {
            $result = $value['id'];
            $data['contact_group_lists'][$result] = $value['type'];
        }

        $data['page_title'] = $this->lang->line('Contact Book');
        $this->_viewcontroller($data);
    }

    public function contact_lists_data()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;
        $this->ajax_check();

        $group_id = trim($this->input->post("group_id",true));
        $searching = trim($this->input->post("contact_list_searching",true));
        $display_columns = array("#",'CHECKBOX','id','first_name','last_name','email','phone_number','contact_type_id','actions');
        $search_columns = array('first_name', 'last_name','phone_number','email');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple = array();
        $where_simple['user_id'] = $this->user_id;

        if ($group_id) 
        {
            // $where_simple['contact_type_id like ']    = "%".$group_id."%";
            $this->db->where("FIND_IN_SET('$group_id',sms_email_contacts.contact_type_id) !=", 0);
        }

        $sql = '';
        if ($searching != '')
        {
            $sql = "(first_name LIKE  '%".$searching."%' OR last_name LIKE '%".$searching."%' OR phone_number LIKE '%".$searching."%' OR email LIKE '%".$searching."%')";
        }
        if($sql != '') $this->db->where($sql);

        $where = array('where' => $where_simple);

        $table = "sms_email_contacts";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        $total_rows_array = $this->basic->count_row($table,$where,$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        foreach ($info as $key => $value) 
        {
            $groupids = $info[$key]['contact_type_id'];

            $type_id = explode(",",$groupids);

            $table = 'sms_email_contact_group';
            $select = array('type');

            $where_group['where_in'] = array('id'=>$type_id);
            $where_group['where'] = array('deleted'=>'0');

            $info1 = $this->basic->get_data($table,$where_group,$select);

            $str = '';
            foreach ($info1 as  $value1)
            {
                $str.= $value1['type'].", ";
            }

            $str = trim($str, ", ");

            $info[$key]['contact_type_id'] = $str;

            $info[$key]['email'] = "<div style='min-width:150px'>".$info[$key]['email']."</div>";
            $info[$key]['actions'] = "<div style='min-width:150px'><a href='#' title='".$this->lang->line("View Details")."' class='btn btn-circle btn-outline-primary contact_details' groups='".$str."' table_id='".$info[$key]['id']."'><i class='fa fa-eye'></i></a>&nbsp;&nbsp;";

            $info[$key]['actions'] .= "<a href='#' title='".$this->lang->line("Edit Contact")."' class='btn btn-circle btn-outline-warning edit_contact' table_id='".$info[$key]['id']."'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;";

            $info[$key]['actions'] .= "<a href='#' title='".$this->lang->line("Delete Contact")."' class='btn btn-circle btn-outline-danger delete_contact' table_id='".$info[$key]['id']."'><i class='fa fa-trash-alt'></i></a></div>
                <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function get_contact_details()
    {
        $this->ajax_check();

        $id = $this->input->post("id");
        $groups = $this->input->post("groups");
        $userid = $this->user_id;
        $this->sms_email_drip_exist = $this->addon_exist('sms_email_sequence');

        $sequence_lists = $this->basic->get_data("messenger_bot_drip_campaign",['where'=>['user_id'=>$userid,'page_id'=>'0','drip_type'=>'custom','campaign_type !='=>'messenger']]);

        $user_sequence = $this->basic->get_data("messenger_bot_drip_campaign_assign",["where"=>["subscribe_id"=>$id,"user_id"=>$userid,"page_table_id"=>"0",'drip_type'=>'custom']]);
        
        $contact_details = $this->basic->get_data('sms_email_contacts',['where'=>['id'=>$id,'user_id'=>$userid]]);
        $contact_details = $contact_details[0];
        $drip_types=$this->get_drip_type();

        $current_sequence_array = [];
        $option=array('0'=>$this->lang->line('Choose Sequence'));

        foreach ($sequence_lists as $key1 => $value1) 
        {
            $option[$value1['id']]="";
            if($value1['campaign_name'] != "") {
                $option[$value1['id']] .= $value1['campaign_name']." : ";
            }

            $option[$value1['id']].=$drip_types[$value1['drip_type']]." - ".$value1['campaign_type']." [".date("jS M, y H:i:s",strtotime($value1['created_at']))."]";
        }

        foreach ($user_sequence as $key2 => $value2) {
            $current_sequence_array[] = $value2['messenger_bot_drip_campaign_id'];
        }

        $sequence_dropdwon = form_dropdown('assign_campaign_id', $option, $current_sequence_array,'style="width:100%" class="form-control inline" id="assign_campaign_id" multiple');

        // subscribe unsubscribe blobk
        if($contact_details['unsubscribed'] == '0') {
            $status ='<span class="subsribe_unsubscribe_container"><a class="text-primary" id="status">'.$this->lang->line("Subscribed").'</a> <a class="text-muted pointer subscribe_unsubscribe_contact" id="'.$contact_details['id']."-".$contact_details['unsubscribed'].'">('.$this->lang->line("Unsubscribe").')</a></span>';
        }
        else {
            $status ='<span class="subsribe_unsubscribe_container"><a class="text-primary" id="status">'.$this->lang->line("Unsubscribed").'</a> <a class="text-muted pointer subscribe_unsubscribe_contact" id="'.$contact_details['id']."-".$contact_details['unsubscribed'].'">('.$this->lang->line("Subscribe").')</a></span>';
        }


        $html = '<ul class="nav nav-tabs" id="myTab" role="tablist">
                <input type="hidden" id="contact_id" name="contact_id" value="'.$contact_details['id'].'">
                <li class="nav-item">
                    <a class="nav-link active" id="default-tab" data-toggle="tab" href="#default" role="tab" aria-controls="default" aria-selected="true">Contact data</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active show" id="default" role="tabpanel" aria-labelledby="default-tab">
                    <div class="row multi_layout"><div class="col-12 col-md-5 col-lg-4 collef">
                        <div class="card main_card">
                            <div class="card-body padding-0">
                                <ul class="list-group list-group-flush">       
                                    <li class="list-group-item">
                                      <i class="fas fa-check-circle subscriber_details blue" data-toggle="tooltip" title="'.$this->lang->line('Status').'"></i>
                                      '.$status.'                    
                                    </li>           
                                    <li class="list-group-item"><i class="fas fa-user subscriber_details blue" data-toggle="tooltip" title="" data-original-title="Frist Name"></i>'.$contact_details['first_name'].'</li>           
                                    <li class="list-group-item"><i class="fas fa-user-circle subscriber_details blue" data-toggle="tooltip" title="" data-original-title="Subscriber id"></i>'.$contact_details['last_name'].'</li>                
                                    <li class="list-group-item"><i class="fas fa-envelope subscriber_details blue" data-toggle="tooltip" title="" data-original-title="Subscriber id"></i>'.$contact_details['email'].'</li>                
                                    <li class="list-group-item"><i class="fas fa-phone subscriber_details blue" data-toggle="tooltip" title="" data-original-title="Subscriber id"></i>'.$contact_details['phone_number'].'</li>                
                                    <li class="list-group-item"><i class="fas fa-users subscriber_details blue" data-toggle="tooltip" title="" data-original-title="Subscriber id"></i>'.$groups.'</li>
                                </ul>

                                </div>
                            </div>          
                        </div>

                        <div class="col-12 col-md-7 col-lg-8 colmid" id="middle_column">
                            <div class="card main_card">
                                <div class="card-header full_width" style="display: block;padding-top:25px;">
                                    <h4>'.$contact_details['first_name'].' '.$contact_details['last_name'].'</h4>
                                </div>
                                <div class="card-body">';

                                if($this->sms_email_drip_exist) {
                                    if($this->basic->is_exist("modules",array("id"=>270)) && $this->basic->is_exist("modules",array("id"=>271))) {  
                                      if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, array('270','271'))) !=0) {

                                            $html .= '<div class="section">   
                                                    <div class="section-title mt-0"> '.$this->lang->line('Assign Sequence').'</div>
                                                    <div class="form-group">
                                                        '.$sequence_dropdwon.'
                                                    </div>
                                                </div>';
                                            }
                                        }
                                    }

                                    $html .= '<div class="section">
                                        <div class="form-group">
                                            <div class="section-title mt-0"> '.$this->lang->line('Notes').'</div>
                                            <textarea class="form-control" id="notes" name="notes">'.$contact_details['notes'].'</textarea>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <a class="btn btn-primary float-left" href="" id="assign_manual_sequence_submit"><i class="fas fa-save"></i> Save changes</a>
                                        <a class="btn btn-outline-secondary float-right" data-dismiss="modal"><i class="fas fa-times"></i> Close</a>
                                    </div>

                                </div>               
                            </div>
                        </div> 
                    </div>
                </div>

            </div><script>
        $("#assign_campaign_id").select2({
             placeholder: "'.$this->lang->line('Choose Sequence').'",
            allowClear: true
        });
        </script>';

        echo $html;
    }

    public function manual_assign_sequence()
    {
        $this->ajax_check();
        $this->is_sms_email_drip_campaigner_exist=$this->sms_email_drip_campaigner_exist();
        $contact_id = $this->input->post("contact_id");
        $campaign_ids = $this->input->post("campaign_ids");
        $notes = strip_tags(trim($this->input->post("notes",true)));
        $drip_type = "custom";
        $page_id = "0";


        $this->basic->update_data("sms_email_contacts",['id'=>$contact_id,'user_id'=>$this->user_id],['notes'=>$notes]);

        if(!empty($campaign_ids) && $this->is_sms_email_drip_campaigner_exist) {
            foreach ($campaign_ids as $value) {
                $this->assign_drip_messaging_id($drip_type,"0",$page_id,$contact_id,$value);
            }
        }

        if($this->is_sms_email_drip_campaigner_exist) {
            if(!empty($campaign_ids)) {
                $this->db->where_not_in("messenger_bot_drip_campaign_id",$campaign_ids); 
            }

            $this->db->where("subscribe_id",$contact_id);
            $this->db->delete("messenger_bot_drip_campaign_assign");

            echo $this->db->last_query();
        }

        echo "1";
    }


    public function subscribe_unsubscribe_contact_action()
    {
        $this->ajax_check();

        $contact_details_id = $this->input->post("contact_details_id");
        $ex_ids = explode("-", $contact_details_id);
        $contact_id = $ex_ids[0];
        $type = $ex_ids[1];

        if($type == "0") {

            $this->basic->update_data("sms_email_contacts",['id'=>$contact_id,'user_id'=>$this->user_id],["unsubscribed"=>"1"]);
            echo "unsubscribed";

        } else if($type == "1") {
            $this->basic->update_data("sms_email_contacts",['id'=>$contact_id,'user_id'=>$this->user_id],["unsubscribed"=>"0"]);
            echo "subscribed";
        } else {
            echo "0";
        }
    }



    public function ajax_export_contacts()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();

        $table = 'sms_email_contacts';       
        $selected_contact_data = $this->input->post('info', true);
        $url_names_array = array();

        foreach ($selected_contact_data as  $value) 
        {
            $id_array[] = $value;
        }

        $where['where_in'] = array('id' => $id_array);

        $info = $this->basic->get_data('sms_email_contacts',$where);
        $info_count = count($info);

        for($i=0; $i<$info_count; $i++)
        {
            $group_ids = $info[$i]['contact_type_id'];
            $exploded_group_ids = explode(",",$group_ids);

            $table = 'sms_email_contact_group';
            $select = array('type');

            $where_group['where_in'] = array('id'=>$exploded_group_ids);
            $where_group['where'] = array('deleted'=>'0');

            $info1 = $this->basic->get_data($table,$where_group,$select);

            $str = '';
            foreach ($info1 as  $value1)
            {
                $str .= $value1['type'].","; 
            }

            $str = trim($str, ",");

            $info[$i]['contact_type_id'] = $str;
        }

        $dir_name = FCPATH."download/contact_export/";

        if(!file_exists($dir_name))
        {
            mkdir($dir_name,0777);
        }

        $file_name = "download/contact_export/exported_contact_list_".time()."_".$this->user_id.".csv";
        $fp = fopen($file_name, "w");
        $head = array("first_name","last_name","phone_number","email");
        fputcsv($fp, $head);
        $write_info = array();

        foreach ($info as  $value) 
        {
            $write_info = array();            
            $write_info[] = $value['first_name'];
            $write_info[] = $value['last_name'];
            $write_info[] = $value['phone_number'];
            $write_info[] = $value['email'];   
            fputcsv($fp, $write_info);  
        }

        fclose($fp);  
        echo $file_name;
    }

    public function get_sequence_campaigns()
    {
      $this->ajax_check();
      $ids = $this->input->post("ids");
      $user_id = $this->user_id;

      $sequence_lists = $this->basic->get_data("messenger_bot_drip_campaign",['where'=>['user_id'=>$user_id,'page_id'=>'0','drip_type'=>'custom','campaign_type !='=>'messenger']]);

      $sequence_lists_html = '
        <label>'.$this->lang->line("Select Sequence Campaign").'</label>
        <select name="sequence_ids" class="form-control" id="sequence_ids" multiple style="width:100%;">';
      foreach ($sequence_lists as $key => $value) 
      {
         $sequence_lists_html .= '<option value="'. $value['id'].'">'.$value['campaign_name'].' ['.$value['campaign_type'].']</option>';
      }

      $sequence_lists_html .='</select><script>$("#sequence_ids").select2();</script>';

      echo $sequence_lists_html;

      
    } 

    public function bulk_sequence_campaign_assign()
    {
        $this->ajax_check();

        $ids = $this->input->post("ids");
        $page_id = $this->input->post("page_id");
        $sequence_id = $this->input->post("sequence_id");
        $drip_type = "custom";

        $subscriber_data = $this->basic->get_data("sms_email_contacts",array("where_in"=>array("id"=>$ids)));

        foreach ($subscriber_data as $value) 
        {
            $subscribe_id = $value["id"];

            foreach ($sequence_id as $value2) {
                $this->assign_drip_messaging_id($drip_type,"0",$page_id,$subscribe_id,$value2);
            }
        }

        echo "1";
    }

    public function ajax_delete_all_selected_contacts()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();

        $selected_contact_data = $this->input->post('info', true);

        if(!is_array($selected_contact_data)) {
            $selected_contact_data = array();
        }

        $implode_ids = implode(",",$selected_contact_data);

        $table = "sms_email_contacts";

        if(!empty($selected_contact_data)) {

            $final_sql = "DELETE FROM sms_email_contacts WHERE id IN({$implode_ids})";

            $this->db->query($final_sql);

            if($this->db->affected_rows() > 0) {
                echo "1";
            } else {
                echo "0";
            }

        }

    }

    public function ajax_import_csv_files()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $ret = array();
        $output_dir = FCPATH."upload/csv";

        if(!file_exists($output_dir))
        {
            mkdir($output_dir,0777);
        }

        if (isset($_FILES["file"])) {

            $error = $_FILES["file"]["error"];

            $post_fileName = $_FILES["file"]["name"];
            $post_fileName_array = explode(".", $post_fileName);
            $ext = array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename=$this->user_id."_"."contact"."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

            $allow = ".csv";
            $allow = str_replace('.', '', $allow);
            $allow = explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit;
            }

            move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }

    public function ajax_campaign_import_csv_files()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $ret = array();
        $output_dir = FCPATH."upload/csv";

        if(!file_exists($output_dir))
        {
            mkdir($output_dir,0777);
        }

        if (isset($_FILES["file"])) {

            $error = $_FILES["file"]["error"];

            $post_fileName = $_FILES["file"]["name"];
            $post_fileName_array = explode(".", $post_fileName);
            $ext = array_pop($post_fileName_array);
            $filename = implode('.', $post_fileName_array);
            $filename = $this->user_id."_"."sms"."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

            $allow = ".csv";
            $allow = str_replace('.', '', $allow);
            $allow = explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit;
            }

            move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }

    public function generating_numbers()
    {
        $this->ajax_check();

        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $res = array();

        $filename = strip_tags($this->input->post("fileval",true));

        $csv = FCPATH.'upload/csv/'.$filename;

        if(!file_exists($csv))
        {
            $res['status'] = '0';
            $res['message'] = $this->lang->line("Sorry, file does not exists in the directory.");

        } else{
            $file = file_get_contents($csv);
            
            $file=str_replace(array("\'", "\"","\t","\r"," "), '', $file);
            $file=str_replace(array("\n"), ',', $file);
            $file=trim($file,",");

            $res['status'] = '1';
            $res['message'] = $this->lang->line("your given information has been updated successfully.");
            $res['file'] = $file;
        }

        echo json_encode($res);
    }

    public function delete_uploaded_csv_file()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;
        if(!$_POST) exit();

        $output_dir = FCPATH."upload/csv/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
            $fileName = $_POST['name'];
            $fileName = str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
            $filePath = $output_dir. $fileName;
            if (file_exists($filePath))
            {
            unlink($filePath);
            }
        } else {
            $fileName = $this->input->post("fileName",true);
            if(file_exists($output_dir.$fileName)){
                unlink($output_dir.$fileName);
            }
        }
    }

    public function import_contact_action_ajax()
    { 
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $user_id = $this->user_id;
        $contact_group = $this->input->post("csv_group_id");

        if(!is_array($contact_group)) {
            $contact_group = array();
        }

        $csv = realpath(FCPATH.'upload/csv/'.$_POST['csv_file']);

        if (!is_readable($csv)) {

            $response['status']="File is not readable.";

        } else {
            $delimiter=',';
            $header = null;
            $data = array();
            if (($handle = fopen($csv, 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) 
                {
                    $data[] =  $row;                        
                }
                fclose($handle);
            }

            $this->db->trans_start();
            $count_insert=0;
            $rejected_rows = array();

            $total_updated = 0;
            $total_inserted = 0;

            foreach ($data as $row) 
            {
                $csv_data = array();
                $contact_groups_new = array();
                $newGroups = array();
                $where_simple = array();
                $where = array();
                $csv_data['user_id'] = $user_id;
                $csv_data['deleted'] = '0';

                if($row[0] != '') $csv_data['first_name'] = trim($row[0]);
                if($row[1] != '') $csv_data['last_name'] = trim($row[1]);
                if($row[2] != '') $csv_data['phone_number'] = trim($row[2]);
                if($row[3] != '') $csv_data['email'] = trim($row[3]);

                $new_email          = trim($row[3]);
                $new_phone_number   = trim($row[2]);


                if($new_email=="email" && $new_phone_number=="phone_number") continue;

                if($new_phone_number == '' && $new_email == '') continue;

                $sql = '';

                if($new_phone_number != '' && $new_email !='') {

                    $or_where = array("email"=> $new_email, "phone_number" => $new_phone_number);

                    $sql = "(user_id ='".$user_id."' AND (phone_number='".$new_phone_number."' OR email='".$new_email."'))";
                    $this->db->where($sql);
                    $db_data = $this->basic->get_data("sms_email_contacts");

                    if(count($db_data) > 0) {

                        if(count($db_data) == 1) {

                            $temp = $contact_group;

                            $contact_groups_new = explode(",",$db_data[0]['contact_type_id']);

                            foreach ($contact_groups_new as $new_group_id) {

                                array_push($temp, $new_group_id);
                            }

                            $newGroups = array_unique($temp);
                            $csv_data['contact_type_id'] = implode(",",$newGroups);

                            if($new_phone_number == $db_data[0]['phone_number']) {

                                $this->basic->update_data("sms_email_contacts",array("user_id"=>$user_id,"phone_number"=>$new_phone_number), $csv_data);
                                $total_updated++;
                                continue;

                            } else {

                                $this->basic->update_data("sms_email_contacts",array("user_id"=>$user_id,"email"=>$new_email), $csv_data);
                                $total_updated++;
                                continue;
                            }
                        } else if(count($db_data) > 1) {
                            
                            array_push($rejected_rows, ['reason' => $this->lang->line("Either email or phone number already Exists in database"),'data'=> $csv_data]);
                            continue;

                        }

                    } else {

                        $csv_data['contact_type_id'] = implode(",",$contact_group);
                        $this->basic->insert_data("sms_email_contacts",$csv_data);
                        $total_inserted++;
                    }

                } else if($new_phone_number != '') {

                    if($this->basic->is_exist("sms_email_contacts", array("user_id"=>$user_id,"phone_number"=>$new_phone_number))) {

                        $contactWithPhone = $this->basic->get_data("sms_email_contacts",array("where"=>array("user_id"=>$user_id,"phone_number"=>$new_phone_number)));

                        $temp2 = $contact_group;
                        $contact_groups_new = explode(",",$contactWithPhone[0]['contact_type_id']);
                        foreach ($contact_groups_new as $new_group_id) {

                            array_push($temp2, $new_group_id);
                        }

                        $newGroups = array_unique($temp2);
                        $csv_data['contact_type_id'] = implode(",",$newGroups);

                        $this->basic->update_data("sms_email_contacts", array("user_id"=>$user_id,"phone_number" => $new_phone_number), $csv_data);
                        $total_updated++;
                        continue;

                    } else {

                        $csv_data['contact_type_id'] = implode(",",$contact_group);
                        $this->basic->insert_data("sms_email_contacts",$csv_data);
                        $total_inserted++;

                    }
                } else if($new_email != '') {

                    if($this->basic->is_exist("sms_email_contacts", array("user_id"=>$user_id,"email"=>$new_email))) {

                        $contactWithEmail = $this->basic->get_data("sms_email_contacts",array("where"=>array("user_id"=>$user_id,"email"=>$new_email)));
                        $temp3 = $contact_group;
                        $contact_groups_new = explode(",",$contactWithEmail[0]['contact_type_id']);
                        foreach ($contact_groups_new as $new_group_id) {

                            array_push($temp3, $new_group_id);
                        }

                        $newGroups = array_unique($temp3);

                        $csv_data['contact_type_id'] = implode(",",$newGroups);

                        $this->basic->update_data("sms_email_contacts", array("user_id"=>$user_id,"email" => $new_email), $csv_data);
                        $total_updated++;
                        continue;

                    } else {

                        $csv_data['contact_type_id'] = implode(",",$contact_group);
                        $this->basic->insert_data("sms_email_contacts",$csv_data);
                        $total_inserted++;

                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {

                $response['status']='Database error occoured. Please try again.';

            } else {
                $response['status'] = 'ok';
                $response['total_updated'] = $total_updated;
                $response['total_inserted'] = $total_inserted;

                if(count($rejected_rows) > 0) {
                    $response['rejected'] = "1";
                    $response['total_rejected'] = count($rejected_rows);
                }
            }
        }


        $response['status'] = str_replace("<p>", "", $response['status']);
        $response['status'] = str_replace("</p>", "", $response['status']);

        echo json_encode($response);
    }

    public function download_contact_upload_error_file($filename=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        if($this->is_demo == '1') {

            if($this->session->userdata('user_type') == "Admin") {

                echo json_encode(array('status'=>'0','message'=>$this->lang->line("This action is disabled in this demo account. Please signup as user and try this with your account")));
                exit();
            }
        }

        $user_id = $this->user_id;

        $this->load->helper('download');
        $name = $filename;

        $fileDir = FCPATH.'upload/csv/'.$filename;
        if(file_exists($fileDir)) {

            $data = file_get_contents(FCPATH.'upload/csv/'.$filename); 
            force_download($name, $data);

        } else {

            $this->error_404();
        }
    }

    public function ajax_create_new_contact()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();

        $result = array();

        if($_POST) {

            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key,TRUE);
            }

            if((isset($contact_email) && !empty($contact_email)) && $this->basic->is_exist("sms_email_contacts",$where=array("user_id"=>$this->user_id,"email"=>$contact_email)))
            {
                $result['status'] = "2";
                $result['msg'] = $this->lang->line("Email Already Exists. Please try with new Email.");

            } else if((isset($phone_number) && !empty($phone_number)) && $this->basic->is_exist("sms_email_contacts",$where=array("user_id"=>$this->user_id,"phone_number"=>$phone_number)))
            {
                $result['status'] = "3";
                $result['msg'] = $this->lang->line("Phone Number Already Exists. Please try with new Phone Number.");
            }
            else
            {
                $userid = $this->user_id;

                $temp = $this->input->post('contact_group_name', true);
                $group = '';
                if (isset($temp)) 
                {
                    $group = implode(',',$temp);
                }

                $contact_type_id = $group;

                $inserted_data = array();
                $inserted_data['first_name'] = trim(strip_tags($first_name));
                $inserted_data['last_name'] = trim(strip_tags($last_name));
                $inserted_data['email'] = trim(strip_tags($contact_email));
                $inserted_data['phone_number'] = trim(strip_tags($phone_number));
                $inserted_data['contact_type_id'] = $contact_type_id;
                $inserted_data['user_id'] = $userid;

                if($this->basic->insert_data("sms_email_contacts",$inserted_data))
                {
                    $result['status'] = "1";
                    $result['msg'] = $this->lang->line("Contact has been added successfully.");
                } else
                {
                    $result['status'] = "0";
                    $result['msg'] = $this->lang->line("Something went wrong, please try once again.");
                }
            }

            echo json_encode($result);

        }
    }

    public function ajax_get_contact_update_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id");
        $user_id = $this->user_id;

        if($table_id == "0" || $table_id == null) exit;

        $group_info = $this->basic->get_data("sms_email_contact_group",array('where'=>array("user_id"=>$user_id)));
        $contact_details = $this->basic->get_data("sms_email_contacts",array('where'=>array('id'=> $table_id, 'user_id'=> $user_id)));

        $update_contact_type_id = $contact_details[0]['contact_type_id'];
        $ex_update_contact_type_id = explode(',',$update_contact_type_id);


        $form = '<div class="row">
                    <div class="col-12">                    
                        <form action="#" enctype="multipart/form-data" id="contact_update_form" method="post">
                            <input type="hidden" name="table_id" value="'.$table_id.'">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('First Name').'</label>
                                        <input type="text" class="form-control" name="first_name" id="updated_first_name" value="'.$contact_details[0]['first_name'].'">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('Last Name').'</label>
                                        <input type="text" class="form-control" name="last_name" id="updated_last_name" value="'.$contact_details[0]['last_name'].'">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('Email').'</label>
                                        <input type="email" class="form-control" name="contact_email" id="updated_contact_email" value="'.$contact_details[0]['email'].'">
                                        
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('Phone Number').'</label>
                                        <input type="text" class="form-control" name="phone_number" id="updated_phone_number" value="'.$contact_details[0]['phone_number'].'">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>'.$this->lang->line('Contact Group').'
                                            <a href="#" data-toggle="tooltip" title="'.$this->lang->line("You Can select multiple contact group.").'"><i class="fas fa-info-circle"></i></a>
                                        </label>
                                        <select name="contact_group_name[]" id="updated_contact_group_name" multiple class="form-control select2" style="width:100%;">';
                                            foreach($group_info as $key => $val)
                                            {
                                                $comparing_group_id = $val['id'];

                                                if(in_array($comparing_group_id, $ex_update_contact_type_id))
                                                {
                                                    $form .='<option value="'.$comparing_group_id.'" selected>'.$val['type'].'</option>';
                                                } else
                                                {
                                                    $form .='<option value="'.$comparing_group_id.'">'.$val['type'].'</option>';
                                                }
                                            }
                                            
        $form .='</select>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <script>$("#updated_contact_group_name").select2()';

        echo $form;
    }

    public function ajax_update_contact()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id");

        $result = array();

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $oldData = $this->basic->get_data("sms_email_contacts", array("where"=>array("id"=>$table_id,"user_id"=>$this->user_id)),array("phone_number","email"));


            if($oldData[0]['email'] != $contact_email && $oldData[0]['phone_number'] != $phone_number)
            {
                if($this->basic->is_exist("sms_email_contacts",$where=array("user_id"=>$this->user_id,"phone_number"=>$phone_number,"email"=>$contact_email)))
                {
                    $result['status'] = "4";
                    $result['msg'] = $this->lang->line("Email and Phone Number Already Exists. Please try with different Email/Phone Number.");
                    echo json_encode($result);
                    exit;
                }
            }
            
            if(!empty($contact_email) && ($oldData[0]['email'] != $contact_email))
            {
                if((isset($contact_email) && !empty($contact_email)) && $this->basic->is_exist("sms_email_contacts",$where=array("user_id"=>$this->user_id,"email"=>$contact_email)))
                {
                    $result['status'] = "2";
                    $result['msg'] = $this->lang->line("Email Already Exists. Please try with new Email.");
                    echo json_encode($result);
                    exit;
                } 
            }

            if(!empty($phone_number) && ($oldData[0]['phone_number'] != $phone_number))
            {
                if((isset($phone_number) && !empty($phone_number)) && $this->basic->is_exist("sms_email_contacts",$where=array("user_id"=>$this->user_id,"phone_number"=>$phone_number)))
                {
                    $result['status'] = "3";
                    $result['msg'] = $this->lang->line("Phone Number Already Exists. Please try with new Phone Number.");
                    echo json_encode($result);
                    exit;
                }
            } 

            $temp = $this->input->post('contact_group_name', true);
            $group = '';
            if (isset($temp)) 
            {
                $group = implode(',',$temp);
            }

            $contact_type_id = $group;

            $inserted_data = array();
            $updated_data['first_name'] = trim(strip_tags($first_name));
            $updated_data['last_name'] = trim(strip_tags($last_name));
            $updated_data['email'] = trim(strip_tags($contact_email));
            $updated_data['phone_number'] = trim(strip_tags($phone_number));
            $updated_data['contact_type_id'] = $contact_type_id;
            $updated_data['user_id'] = $this->user_id;

            $where = array("id"=>$table_id,'user_id'=>$this->user_id);

            if($this->basic->update_data("sms_email_contacts",$where,$updated_data))
            {
                $result['status'] = "1";
                $result['msg'] = $this->lang->line("Contact has been updated successfully.");
            } else
            {
                $result['status'] = "0";
                $result['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($result);

        }
    }

    public function delete_contact()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array('263','264')))==0) exit;

        $this->ajax_check();
        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == null) exit;

        if($this->basic->delete_data("sms_email_contacts", array("id"=>$table_id,'user_id'=>$this->user_id)))
        {
            echo "1";
        } else
        {
            echo "0";
        }

    }
    // End of phonebook Section


    // =============================== Email API SECTION ==================================
    public function smtp_config()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) redirect('home/login_page', 'location');

        $data['body'] = 'sms_email_manager/email/email_api_config/smtp_config';

        $data['page_title'] = $this->lang->line('SMTP API');
        $this->_viewcontroller($data);
    }

    public function smtp_config_data()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','email_address','smtp_host','smtp_user','smtp_password','smtp_port','smtp_type','status','actions');
        $search_columns = array('email_address','smtp_host','smtp_user','smtp_type');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';
        $where_custom = "user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        $table = "email_smtp_config";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table,$where='',$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for ($i=0; $i < count($info) ; $i++) 
        { 
            $status = $info[$i]["status"];
            if($status=='1') $info[$i]["status"] = "<i title ='".$this->lang->line('Active')."'class='status-icon fas fa-toggle-on text-primary'></i>";
            else $info[$i]["status"] = "<i title ='".$this->lang->line('Inactive')."'class='status-icon fas fa-toggle-off gray'></i>";

            $info[$i]['actions'] = "<div style='min-width:140px'><a href='#' data-toggle='tooltip' title='".$this->lang->line("Send Test Mail")."' class='btn btn-circle btn-outline-primary send_testmail' table_id='".$info[$i]['id']."'><i class='fa fa-paper-plane'></i></a>&nbsp;<a href='#' data-toggle='tooltip' title='".$this->lang->line("Edit")."' class='btn btn-circle btn-outline-warning edit_smtp' table_id='".$info[$i]['id']."'><i class='fa fa-edit'></i></a>&nbsp;";

            $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Delete")."' class='btn btn-circle btn-outline-danger delete_smtp' table_id='".$info[$i]['id']."'><i class='fa fa-trash-alt'></i></a></div>
                <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
        
    }

    public function send_test_mail()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        if($this->is_demo == '1')
        {
            echo "Test Email sending is disabled in this demo.";
            exit();
        }

        if($_POST) {

            $email_table_id = $this->input->post("table_id",true);
            $service_type = $this->input->post("service_type",true);

            if($service_type == "smtp") {

                $from_email = "smtp_".$email_table_id;

            } else if($service_type == "mailgun") {

                $from_email = "mailgun_".$email_table_id;

            } else if($service_type == "sendgrid") {

                $from_email = "sendgrid_".$email_table_id;
                
            } else if($service_type == "mandrill") {

                $from_email = "mandrill_".$email_table_id;
                
            }

            $to_email = trim($this->input->post("email"));
            $subject = trim($this->input->post("subject"));
            $message = $this->input->post("message");
            $attachement = "";
            $filename = "";
            $user_id = $this->user_id;
            $test_mail = 1;
            
            $response = @$this->_email_send_function($from_email, $message, $to_email, $subject, $attachement, $filename,$user_id,$test_mail);
            echo $response;
        
        }
    }

    public function ajax_save_smtp_api()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $save_data = array();
        $ret = array();

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $smtp_status = $this->input->post("smtp_status",true);
            if($smtp_status == "") $smtp_status = "0";

            $save_data['user_id']       = $this->user_id;
            $save_data['email_address'] = trim(strip_tags($smtp_email));
            $save_data['smtp_host']     = trim(strip_tags($smtp_host));
            $save_data['smtp_port']     = trim(strip_tags($smtp_port));
            $save_data['smtp_user']     = trim(strip_tags($smtp_username));
            $save_data['smtp_password'] = trim(strip_tags($smtp_password));
            $save_data['smtp_type']     = trim(strip_tags($smtp_type));
            $save_data['status']        = trim(strip_tags($smtp_status));
            $save_data['sender_name']   = trim(strip_tags($sender_name));

            if($this->basic->insert_data("email_smtp_config",$save_data))
            {
                $ret['status'] = '1';
                $ret['msg'] = $this->lang->line("SMTP API Information has been added successfully.");
            } else
            {
                $ret['status'] = '0';
                $ret['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($ret);
        }

    }

    public function ajax_get_smtp_api_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $user_id = $this->user_id;
        $table_id = $this->input->post("table_id",true);
        if($table_id == "0" || $table_id == "") exit;

        $smtp_api_info = $this->basic->get_data("email_smtp_config",array("where"=>array("user_id"=>$user_id,"id"=>$table_id)));

        $smtp_email = $smtp_api_info[0]['email_address'];
        $smtp_host  = $smtp_api_info[0]['smtp_host'];
        $smtp_port  = $smtp_api_info[0]['smtp_port'];
        $smtp_user  = $smtp_api_info[0]['smtp_user'];
        $smtp_pass  = $smtp_api_info[0]['smtp_password'];
        $smtp_type  = $smtp_api_info[0]['smtp_type'];
        $status     = $smtp_api_info[0]['status'];
        $sender_name= $smtp_api_info[0]['sender_name'];

        if($smtp_type == "Default") $default_selected = "selected";
        else $default_selected = ""; 
        if($smtp_type == "tls") $tls_selected = "selected";
        else $tls_selected = ""; 
        if($smtp_type == "ssl") $ssl_selected = "selected";
        else $ssl_selected = ""; 

        if($status == "1") $status = "checked";
        else $status = "";

        $update_form = '
        <div class="row">
            <div class="col-12">
                <form action="#" method="POST" id="smtp_api_update_form">
                    <input type="hidden" name="table_id" id="table_id" value="'.$table_id.'">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line('Email Address').'</label>
                                <input type="text" class="form-control" id="updated_smtp_email" name="smtp_email" value="'.$smtp_email.'">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line('SMTP Host').'</label>
                                <input type="text" class="form-control" id="updated_smtp_host" name="smtp_host" value="'.$smtp_host.'">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line('SMTP Port').'</label>
                                <input type="text" class="form-control" id="updated_smtp_port" name="smtp_port" value="'.$smtp_port.'">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line('SMTP Username').'</label>
                                <input type="text" class="form-control" id="updated_smtp_username" name="smtp_username" value="'.$smtp_user.'">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line('SMTP Password').'</label>
                                <input type="text" class="form-control" id="updated_smtp_password" name="smtp_password" value="'.$smtp_pass.'">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line('SMTP Type').'</label>
                                <select class="form-control select2" id="updated_smtp_type" name="smtp_type" style="width:100%;">
                                    <option value="Default" '.$default_selected.'>'.$this->lang->line('Default').'</option>
                                    <option value="tls" '.$tls_selected.'>'.$this->lang->line('tls').'</option>
                                    <option value="ssl" '.$ssl_selected.'>'.$this->lang->line('ssl').'</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line('Status').'</label><br>
                                <label class="custom-switch">
                                    <input type="checkbox" name="smtp_status" value="1" id="updated_smtp_status" class="custom-switch-input" '.$status.'>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">'.$this->lang->line('Active').'</span>
                                </label>
                            </div>
                        </div>

                          <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line('Sender Name').'</label>
                                <input type="text" class="form-control" id="updated_sender_name" name="sender_name" value="'.$sender_name.'">
                            </div>
                        </div>


                    </div>
                </form>
            </div>
        </div><script>$("#updated_smtp_type").select2();</script>';

        echo $update_form;
    }


    public function ajax_update_smtp_api()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $save_data = array();
        $ret = array();

        $table_id = $this->input->post("table_id");

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $smtp_status = $this->input->post("smtp_status",true);
            if($smtp_status == "") $smtp_status = "0";

            $save_data['user_id']       = $this->user_id;
            $save_data['email_address'] = trim(strip_tags($smtp_email));
            $save_data['smtp_host']     = trim(strip_tags($smtp_host));
            $save_data['smtp_port']     = trim(strip_tags($smtp_port));
            $save_data['smtp_user']     = trim(strip_tags($smtp_username));
            $save_data['smtp_password'] = trim(strip_tags($smtp_password));
            $save_data['smtp_type']     = trim(strip_tags($smtp_type));
            $save_data['status']        = trim(strip_tags($smtp_status));
            $save_data['sender_name']   = trim(strip_tags($sender_name));

            if($this->basic->update_data("email_smtp_config",array("user_id"=>$this->user_id,'id'=>$table_id),$save_data))
            {
                $ret['status'] = '1';
                $ret['msg'] = $this->lang->line("SMTP API Information has been updated successfully.");
            } else
            {
                $ret['status'] = '0';
                $ret['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($ret);
        }
    }

    public function delete_smtp_api()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id",true);
        if($table_id == "0" || $table_id == "") exit;

        if($this->basic->delete_data("email_smtp_config",array("id"=>$table_id)))
        {
            echo "1";

        } else
        {
            echo "0";
        }
    }

    // mailgun section started
    public function mailgun_api_config()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) redirect('home/login_page', 'location');

        $data['body'] = 'sms_email_manager/email/email_api_config/mailgun_config';
        $data['page_title'] = $this->lang->line('Mailgun API');
        $this->_viewcontroller($data);
    }

    public function mailgun_config_data()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','email_address','domain_name','api_key','status','actions');
        $search_columns = array('email_address','domain_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';
        $where_custom = "user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        $table = "email_mailgun_config";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table,$where='',$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for ($i=0; $i < count($info) ; $i++) 
        { 
            $status = $info[$i]["status"];
            if($status=='1') $info[$i]["status"] = "<i title ='".$this->lang->line('Active')."'class='status-icon fas fa-toggle-on text-primary'></i>";
            else $info[$i]["status"] = "<i title ='".$this->lang->line('Inactive')."'class='status-icon fas fa-toggle-off gray'></i>";

            $info[$i]['actions'] = "<div style='min-width:150px'><a href='#' data-toggle='tooltip' title='".$this->lang->line("Send Test Mail")."' class='btn btn-circle btn-outline-primary send_testmail' table_id='".$info[$i]['id']."'><i class='fa fa-send'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Edit")."' class='btn btn-circle btn-outline-warning edit_mailgun_api' table_id='".$info[$i]['id']."'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Delete")."' class='btn btn-circle btn-outline-danger delete_mailgun_api' table_id='".$info[$i]['id']."'><i class='fa fa-trash-alt'></i></a></div>
                <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);   
    }

    public function ajax_mailgun_api_save()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $save_data = array();
        $ret = array();

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $mailgun_status = $this->input->post("mailgun_status",true);
            if($mailgun_status == "") $mailgun_status = "0";

            $save_data['user_id']       = $this->user_id;
            $save_data['email_address'] = trim(strip_tags($mailgun_email));
            $save_data['domain_name']   = trim(strip_tags($mailgun_domain));
            $save_data['api_key']       = trim(strip_tags($mailgun_api_key));
            $save_data['status']        = trim(strip_tags($mailgun_status));

            if($this->basic->insert_data("email_mailgun_config",$save_data))
            {
                $ret['status'] = '1';
                $ret['msg'] = $this->lang->line("Mailgun API Information has been added successfully.");
            } else
            {
                $ret['status'] = '0';
                $ret['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($ret);
        }
    }

    public function ajax_get_mailgun_api_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $user_id  = $this->user_id;
        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == "") exit;

        $table = "email_mailgun_config";
        $where['where'] = array("id"=>$table_id,"user_id"=>$user_id);
        $mailgun_api_info = $this->basic->get_data($table,$where);

        $email   = $mailgun_api_info[0]['email_address'];
        $domain  = $mailgun_api_info[0]['domain_name'];
        $api_key = $mailgun_api_info[0]['api_key'];
        $status  = $mailgun_api_info[0]['status'];

        if($status == "1") $status_checked = "checked";
        else $status_checked = "";

        $updated_form ='
        <div class="row">
            <div class="col-12">
                <form action="#" method="POST" id="update_mailgun_api_form">
                    <input type="hidden" name="table_id" id="table_id" value="'.$table_id.'">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Email Address').'</label>
                                <input type="text" class="form-control" id="updated_mailgun_email" name="mailgun_email" value="'.$email.'">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Domain Name').'</label>
                                <input type="text" class="form-control" id="updated_mailgun_domain" name="mailgun_domain" value="'.$domain.'">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('API Key').'</label>
                                <input type="text" class="form-control" id="updated_mailgun_api_key" name="mailgun_api_key" value="'.$api_key.'">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Status').'</label><br>
                                <label class="custom-switch">
                                    <input type="checkbox" name="mailgun_status" value="1" id="updated_mailgun_status" class="custom-switch-input" '.$status_checked.'>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">'.$this->lang->line('Active').'</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>';

        echo $updated_form;
    }

    public function ajax_update_mailgun_api_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $save_data = array();
        $ret = array();

        $table_id = $this->input->post("table_id");

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $mailgun_status = $this->input->post("mailgun_status",true);
            if($mailgun_status == "") $mailgun_status = "0";

            $save_data['user_id']       = $this->user_id;
            $save_data['email_address'] = trim(strip_tags($mailgun_email));
            $save_data['domain_name']   = trim(strip_tags($mailgun_domain));
            $save_data['api_key']       = trim(strip_tags($mailgun_api_key));
            $save_data['status']        = trim(strip_tags($mailgun_status));

            if($this->basic->update_data("email_mailgun_config",array("id"=>$table_id,"user_id"=>$this->user_id),$save_data))
            {
                $ret['status'] = '1';
                $ret['msg'] = $this->lang->line("Mailgun API Information has been updated successfully.");
            } else
            {
                $ret['status'] = '0';
                $ret['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($ret);
        }
    }

    public function delete_mailgun_api()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == "") exit;

        if($this->basic->delete_data("email_mailgun_config",array("id"=>$table_id,"user_id"=>$this->user_id)))
        {
            echo "1";
        } else
        {
            echo "0";
        }
    }

    //mailgun section
    public function mandrill_api_config()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) redirect('home/login_page', 'location');

        $data['body'] = 'sms_email_manager/email/email_api_config/mandrill_config';
        $data['page_title'] = $this->lang->line('Mandrill API');
        $this->_viewcontroller($data);
    }

    public function mandrill_config_data()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','your_name','email_address','api_key','status','actions');
        $search_columns = array('email_address','your_name','api_key');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';
        $where_custom = "user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        $table = "email_mandrill_config";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table,$where='',$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for ($i=0; $i < count($info) ; $i++) 
        { 
            $status = $info[$i]["status"];
            if($status=='1') $info[$i]["status"] = "<i title ='".$this->lang->line('Active')."'class='status-icon fas fa-toggle-on text-primary'></i>";
            else $info[$i]["status"] = "<i title ='".$this->lang->line('Inactive')."'class='status-icon fas fa-toggle-off gray'></i>";

            $info[$i]['actions'] = "<div style='min-width:150px'><a href='#' data-toggle='tooltip' title='".$this->lang->line("Send Test Mail")."' class='btn btn-circle btn-outline-primary send_testmail' table_id='".$info[$i]['id']."'><i class='fa fa-send'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' title='".$this->lang->line("Edit")."' class='btn btn-circle btn-outline-warning edit_mandrill_api' table_id='".$info[$i]['id']."'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' title='".$this->lang->line("Delete")."' class='btn btn-circle btn-outline-danger delete_mandrill_api' table_id='".$info[$i]['id']."'><i class='fa fa-trash-alt'></i></a></div>
                <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function ajax_mandrill_api_save()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $save_data = array();
        $ret = array();

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $mandrill_status = $this->input->post("mandrill_status",true);
            if($mandrill_status == "") $mandrill_status = "0";

            $save_data['user_id']       = $this->user_id;
            $save_data['your_name']     = trim(strip_tags($mandrill_name));
            $save_data['email_address'] = trim(strip_tags($mandrill_email));
            $save_data['api_key']       = trim(strip_tags($mandrill_api_key));
            $save_data['status']        = trim(strip_tags($mandrill_status));

            if($this->basic->insert_data("email_mandrill_config",$save_data))
            {
                $ret['status'] = '1';
                $ret['msg'] = $this->lang->line("Mandrill API Information has been added successfully.");
            } else
            {
                $ret['status'] = '0';
                $ret['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($ret);
        }
    }

    public function ajax_get_mandrill_api_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $user_id  = $this->user_id;
        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == "") exit;

        $table = "email_mandrill_config";
        $where['where'] = array("id"=>$table_id,"user_id"=>$user_id);
        $mailgun_api_info = $this->basic->get_data($table,$where);

        $name    = $mailgun_api_info[0]['your_name'];
        $email   = $mailgun_api_info[0]['email_address'];
        $api_key = $mailgun_api_info[0]['api_key'];
        $status  = $mailgun_api_info[0]['status'];

        if($status == "1") $status_checked = "checked";
        else $status_checked = "";

        $updated_form ='
        <div class="row">
            <div class="col-12">
                <form action="#" method="POST" id="update_mandrill_api">
                    <input type="hidden" name="table_id" id="table_id" value="'.$table_id.'">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Your Name').'</label>
                                <input type="text" class="form-control" id="updated_mandrill_name" name="mandrill_name" value="'.$name.'">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Email Address').'</label>
                                <input type="text" class="form-control" id="updated_mandrill_email" name="mandrill_email" value="'.$email.'">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('API Key').'</label>
                                <input type="text" class="form-control" id="updated_mandrill_api_key" name="mandrill_api_key" value="'.$api_key.'">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Status').'</label><br>
                                <label class="custom-switch">
                                    <input type="checkbox" name="mandrill_status" value="1" id="updated_mandrill_status" class="custom-switch-input" '.$status_checked.'>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">'.$this->lang->line('Active').'</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>';

        echo $updated_form;
    }

    public function ajax_update_mandrill_api_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $save_data = array();
        $ret = array();

        $table_id = $this->input->post("table_id");

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $mandrill_status = $this->input->post("mandrill_status",true);
            if($mandrill_status == "") $mandrill_status = "0";

            $save_data['user_id']       = $this->user_id;
            $save_data['your_name']     = trim(strip_tags($mandrill_name));
            $save_data['email_address'] = trim(strip_tags($mandrill_email));
            $save_data['api_key']       = trim(strip_tags($mandrill_api_key));
            $save_data['status']        = trim(strip_tags($mandrill_status));

            if($this->basic->update_data("email_mandrill_config",array('id'=>$table_id,'user_id'=>$this->user_id),$save_data))
            {
                $ret['status'] = '1';
                $ret['msg'] = $this->lang->line("Mandrill API Information has been updated successfully.");
            } else
            {
                $ret['status'] = '0';
                $ret['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($ret);
        }
    }

    public function delete_mandrill_api()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == "") exit;

        if($this->basic->delete_data("email_mandrill_config",array("id"=>$table_id,"user_id"=>$this->user_id)))
        {
            echo "1";
        } else
        {
            echo "0";
        }
    }


    // Sendgrid section started
    public function sendgrid_api_config()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) redirect('home/login_page', 'location');

        $data['body'] = 'sms_email_manager/email/email_api_config/sendgrid_config';
        $data['page_title'] = $this->lang->line('Sendgrid API');
        $this->_viewcontroller($data);
    }

    public function sendgrid_config_data()
    {
        $this->ajax_check();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','email_address','username','password','status','actions');
        $search_columns = array('email_address','username');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom = '';
        $where_custom = "user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        $table = "email_sendgrid_config";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table,$where='',$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for ($i=0; $i < count($info) ; $i++) 
        { 
            $status = $info[$i]["status"];
            if($status=='1') $info[$i]["status"] = "<i title ='".$this->lang->line('Active')."'class='status-icon fas fa-toggle-on text-primary'></i>";
            else $info[$i]["status"] = "<i title ='".$this->lang->line('Inactive')."'class='status-icon fas fa-toggle-off gray'></i>";

            $info[$i]['actions'] = "<div style='min-width:100px'><a href='#' data-toggle='tooltip' title='".$this->lang->line("Send Test Mail")."' class='btn btn-circle btn-outline-primary send_testmail' table_id='".$info[$i]['id']."'><i class='fa fa-send'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Edit")."' class='btn btn-circle btn-outline-warning edit_sendgrid_api' table_id='".$info[$i]['id']."'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;";

            $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Delete")."' class='btn btn-circle btn-outline-danger delete_sendgrid_api' table_id='".$info[$i]['id']."'><i class='fa fa-trash-alt'></i></a></div>
                <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function ajax_sendgrid_api_save()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $save_data = array();
        $ret = array();

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $sendgrid_status = $this->input->post("sendgrid_status",true);
            if($sendgrid_status == "") $sendgrid_status = "0";

            $save_data['user_id']       = $this->user_id;
            $save_data['email_address'] = trim(strip_tags($sendgrid_email));
            $save_data['username']      = trim(strip_tags($sendgrid_username));
            $save_data['password']      = trim(strip_tags($sendgrid_password));
            $save_data['status']        = trim(strip_tags($sendgrid_status));

            if($this->basic->insert_data("email_sendgrid_config",$save_data))
            {
                $ret['status'] = '1';
                $ret['msg'] = $this->lang->line("Sendgrid API Information has been added successfully.");
            } else
            {
                $ret['status'] = '0';
                $ret['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($ret);
        }

    }

    public function ajax_get_sendgrid_api_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $user_id  = $this->user_id;
        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == "") exit;

        $table = "email_sendgrid_config";
        $where['where'] = array("id"=>$table_id,"user_id"=>$user_id);
        $sendgrid_api_info = $this->basic->get_data($table,$where);

        $email    = $sendgrid_api_info[0]['email_address'];
        $username = $sendgrid_api_info[0]['username'];
        $password = $sendgrid_api_info[0]['password'];
        $status   = $sendgrid_api_info[0]['status'];

        if($status == "1") $status_checked = "checked";
        else $status_checked = "";

        $updated_forms = '
        <div class="row">
            <div class="col-12">
                <form action="#" method="POST" id="update_sendgrid_api_form">
                    <input type="hidden" name="table_id" id="table_id" value="'.$table_id.'">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Email Address').'</label>
                                <input type="text" class="form-control" id="updated_sendgrid_email" name="sendgrid_email" value="'.$email.'">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Username').'</label>
                                <input type="text" class="form-control" id="updated_sendgrid_username" name="sendgrid_username" value="'.$username.'">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Password').'</label>
                                <input type="text" class="form-control" id="updated_sendgrid_password" name="sendgrid_password" value="'.$password.'">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>'.$this->lang->line('Status').'</label><br>
                                <label class="custom-switch">
                                    <input type="checkbox" name="sendgrid_status" value="1" id="updated_sendgrid_status" class="custom-switch-input" '.$status_checked.'>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">'.$this->lang->line('Active').'</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>';

        echo $updated_forms;
    }

    public function ajax_sendgrid_api_update()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $save_data = array();
        $ret = array();

        $table_id = $this->input->post("table_id");

        if($_POST)
        {
            $post = $_POST;
            foreach ($post as $key => $value) 
            {
                $$key = $this->input->post($key,TRUE);
            }

            $sendgrid_status = $this->input->post("sendgrid_status",true);
            if($sendgrid_status == "") $sendgrid_status = "0";

            $save_data['user_id']       = $this->user_id;
            $save_data['email_address'] = trim(strip_tags($sendgrid_email));
            $save_data['username']      = trim(strip_tags($sendgrid_username));
            $save_data['password']      = trim(strip_tags($sendgrid_password));
            $save_data['status']        = trim(strip_tags($sendgrid_status));

            if($this->basic->update_data("email_sendgrid_config",array("id"=>$table_id,"user_id"=>$this->user_id),$save_data))
            {
                $ret['status'] = '1';
                $ret['msg'] = $this->lang->line("Sendgrid API Information has been updated successfully.");
            } else
            {
                $ret['status'] = '0';
                $ret['msg'] = $this->lang->line("Something went wrong, please try once again.");
            }

            echo json_encode($ret);
        }
    }

    public function delete_sendgrid_api()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id");
        if($table_id == "0" || $table_id == "") exit;

        if($this->basic->delete_data("email_sendgrid_config",array("id"=>$table_id,"user_id"=>$this->user_id)))
        {
            echo "1";
        } else
        {
            echo "0";
        }
    }

    // ===========================================================================================================
    //                                             SMS Campaign Section                                                        
    // ============================================================================================================

    // SMS campaign Creation section started
    public function sms_campaign_lists()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) redirect('home/login_page', 'location');

        $data['body'] = 'sms_email_manager/sms/sms_campaign_lists';
        $data['page_title'] = $this->lang->line('SMS Campaign');
        $this->_viewcontroller($data);
    }

    public function sms_campaign_lists_data()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();

        $campaign_status     = trim($this->input->post("campaign_status",true));
        $searching_campaign  = trim($this->input->post("searching_campaign",true));
        $post_date_range = $this->input->post("post_date_range",true);

        $display_columns = array("#",'id','campaign_name','send_as','sent_count','actions','posting_status','schedule_time','created_at');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();
        $where_simple['sms_sending_campaign.user_id'] = $this->user_id;

        if($post_date_range!="")
        {
            $exp = explode('|', $post_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";

            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(created_at,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(created_at,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($campaign_status !="") $where_simple['sms_sending_campaign.posting_status'] = $campaign_status;
        if($searching_campaign !="") $where_simple['sms_sending_campaign.campaign_name like'] = "%".$searching_campaign."%";

        $where  = array('where'=>$where_simple);
        $join   = array("sms_api_config" => "sms_sending_campaign.api_id=sms_api_config.id,left");
        $select = array("sms_sending_campaign.*","sms_api_config.gateway_name","sms_api_config.custom_name","sms_api_config.phone_number AS api_phone");

        $table = "sms_sending_campaign";
        $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');

        $total_rows_array = $this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        for ($i=0; $i < count($info) ; $i++) 
        { 
            $action_count = 3;
            $posting_status = $info[$i]['posting_status'];

            if ($info[$i]['gateway_name'] == 'custom') {
                $info[$i]['gateway_name'] = $this->lang->line("Custom - "). $info[$i]['custom_name'];
            }

            if($info[$i]['api_phone'] != "")
                $info[$i]['send_as'] = $info[$i]['gateway_name'].' : '.$info[$i]['api_phone'];
            else
                $info[$i]['send_as'] = $info[$i]['gateway_name'];

            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
                $info[$i]['schedule_time'] = "<div style='min-width:100px !important;'>".date("M j, Y H:i",strtotime($info[$i]['schedule_time']))."</div>";
            else 
                $info[$i]['schedule_time'] = "<div style='min-width:100px !important;' class='text-muted'><i class='fas fa-exclamation-circle'></i> ".$this->lang->line('Not Scheduled')."</div>";

            // added date
            if($info[$i]['created_at'] != "0000-00-00 00:00:00")
                $info[$i]['created_at'] = "<div style='min-width:100px !important;'>".date("M j, Y H:i",strtotime($info[$i]['created_at']))."</div>";

            // generating delete button
            if($posting_status=='1')
                $delete_btn = "<a href='#' class='btn btn-circle btn-light pointer text-muted' data-toggle='tooltip' title='".$this->lang->line("Campaign in processing can not be deleted. You can pause campaign and then delete it.")."'><i class='fa fa-trash'></i></a>";
            else 
                $delete_btn =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("delete campaign")."' id='".$info[$i]['id']."' class='delete_sms_campaign btn btn-circle btn-outline-danger'><i class='fa fa-trash'></i></a>";

            $is_try_again = $info[$i]["is_try_again"];

            $force_porcess_str="";

            $number_of_sms_to_be_sent_in_try = $this->config->item("number_of_sms_to_be_sent_in_try");
            if($number_of_sms_to_be_sent_in_try == "") $number_of_sms_to_be_sent_in_try = 10;

            // generating restat and force processing button
            if($number_of_sms_to_be_sent_in_try == "" ||  $number_of_sms_to_be_sent_in_try == "0")
            {
                $force_porcess_str="";
            }
            else
            {
                $action_count++;
                if($posting_status=='1' && $is_try_again=='1')
                    $force_porcess_str .= "<a href='#' class='btn btn-circle btn-outline-dark pause_campaign_info' table_id='".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("Pause Campaign")."'><i class='fas fa-pause'></i></a>";
                if($posting_status=='3')
                    $force_porcess_str .= "<a href='#' class='btn btn-circle btn-outline-success play_campaign_info' table_id='".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("Start Campaign")."'><i class='fas fa-play'></i></a>";
            }

            if($posting_status=='1'){
                $action_count++;
                $force_porcess_str .= "<a href='#' id='".$info[$i]['id']."' class='force btn btn-circle btn-outline-warning' data-toggle='tooltip' title='".$this->lang->line("force reprocessing")."'><i class='fas fa-sync'></i></a>";
            }

            // status
            if( $posting_status == '2') 
                $info[$i]['posting_status'] = '<div style="min-width:100px"><span class="text-success badge"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</span></div>';
            else if( $posting_status == '1') 
                $info[$i]['posting_status'] = '<div style="min-width:100px"><span class="text-warning"><i class="fas fa-spinner"></i> '.$this->lang->line("Processing").'</span></div>';
            else if( $posting_status == '3') 
                $info[$i]['posting_status'] = '<div style="min-width:100px"><span class="text-muted"><i class="fas fa-stop"></i> '.$this->lang->line("Paused").'</span></div>';
            else 
                $info[$i]['posting_status'] = '<div style="min-width:100px"><span class="text-danger"><i class="far fa-times-circle"></i> '.$this->lang->line("Pending").'</span></div>';

            // sent column
            $info[$i]["sent_count"] =  $info[$i]["successfully_sent"]."/". $info[$i]["total_thread"] ;

            $report_btn = "<a href='#' class='campaign_report btn btn-circle btn-outline-primary' data-toggle='tooltip' title='".$this->lang->line("View Report")."' 
                table_id='".$info[$i]['id']."' 
                campaign_name='".$info[$i]['campaign_name']."' 
                campaign_message='".htmlspecialchars($info[$i]['campaign_message'],ENT_QUOTES)."'
                send_as='".$info[$i]['send_as']."' 
                campaign_status='".$posting_status."' 
                successfullysent='".$info[$i]["successfully_sent"]."' 
                totalThread='".$info[$i]["total_thread"]."' 
                ><i class='fas fa-eye'></i> </a>";

            if($posting_status != '0' || $info[$i]['time_zone'] == "") 
                $edit_btn = "<a href='#' data-toggle='tooltip' title='".$this->lang->line("only pending campaigns are editable")."' class='btn btn-circle btn-light'><i class='fas fa-edit'></i></a>";
            else
            {
                $edit_url = site_url('sms_email_manager/edit_sms_campaign/'.$info[$i]['id']);
                $edit_btn =  "<a data-toggle='tooltip' title='".$this->lang->line('edit campaign')."' href='".$edit_url."' class='btn btn-circle btn-outline-warning'><i class='fas fa-edit'></i></a>";
            }


            $action_width = ($action_count*47)+20;
            $info[$i]['actions'] ='
            <div class="dropdown d-inline dropright">
              <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
                $info[$i]['actions'] .= $report_btn;
                $info[$i]['actions'] .= $edit_btn;
                $info[$i]['actions'] .= $force_porcess_str;
                $info[$i]['actions'] .= $delete_btn;
                $info[$i]['actions'] .="
              </div>
            </div>
            <script>
            $('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function delete_sms_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();

        $id = $this->input->post("campaign_id");
        if($id == "" || $id=="0") exit;

        $campaign_data = $this->basic->get_data("sms_sending_campaign",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)),array("posting_status","total_thread"));

        $current_total_thread_abs  = isset($campaign_data[0]["total_thread"]) ? $campaign_data[0]["total_thread"] : 0;
        $posting_status  = isset($campaign_data[0]["posting_status"]) ? $campaign_data[0]["posting_status"] : "";

        if($posting_status=="0") // removing usage data if deleted and campaign is pending
        {
            if($current_total_thread_abs>0)
                $this->_delete_usage_log($module_id=264,$request=$current_total_thread_abs);
        }

        if($this->basic->delete_data("sms_sending_campaign",array("id"=>$id,"user_id"=>$this->user_id)))
        {
            if($this->basic->delete_data("sms_sending_campaign_send",array("campaign_id"=>$id,"user_id"=>$this->user_id))){
                echo "1";
            }
        }
        else {
            echo "0";
        }
    }

    public function ajax_get_sms_campaign_report_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();

        $table_id = $this->input->post('table_id');
        $search_value = trim($this->input->post("searching",true));  

        $display_columns = array("#","id","contact_first_name","contact_last_name","contact_phone_number","sent_time","delivery_id");
        $search_columns = array('contact_first_name','contact_last_name', 'contact_phone_number');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom ="campaign_id = ".$table_id." AND user_id = ".$this->user_id;

        if ($search_value != '') {

            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        $table = "sms_sending_campaign_send";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table,$where='',$count="id",$join='',$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        for ($i=0; $i <count($info) ; $i++) { 

            if($info[$i]['contact_first_name'] == "") $info[$i]['contact_first_name'] = "<div class='text-center'>-</div>";
            if($info[$i]['contact_last_name'] == "") $info[$i]['contact_last_name'] = "<div class='text-center'>-</div>";

            $info[$i]['contact_phone_number'] = '<div style="width:150px;">'.$info[$i]['contact_phone_number'].'</div>';

            if($info[$i]['delivery_id'] != 'pending') {

                $info[$i]['delivery_id'] = $info[$i]['delivery_id'];
            }
            else {

                $info[$i]['delivery_id'] = '<div style="min-width:100px"><span class="text-danger badge"><i class="far fa-times-circle"> '.ucfirst($info[$i]['delivery_id']).'</span></div>';
            }

            if($info[$i]['sent_time'] != "0000-00-00 00:00:00") {

                $info[$i]['sent_time'] = "<div style='min-width:120px !important;'>".date("M j, Y H:i",strtotime($info[$i]['sent_time']))."</div>";

            } else {

                $info[$i]['sent_time'] = "<div class='text-center'>x</div>";
            }
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function edit_campaign_content($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) redirect('home/login_page', 'location');

        if($id==0) exit();

        $data['body'] = "sms_email_manager/sms/edit_message_content";
        $data['page_title'] = $this->lang->line("Edit Message Contents");
        $data["message_data"] = $this->basic->get_data("sms_sending_campaign",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $this->_viewcontroller($data);
    }

    public function edit_campaign_content_action()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();

        $table_id = $this->input->post("table_id",true);
        $user_id = $this->user_id;
        $message = $this->input->post("message");
        $message = str_replace(array("'",'"'), array('`','`'), $message);
        $edited_message   = array('campaign_message' => $message);

        if($this->basic->update_data('sms_sending_campaign',array("id"=>$table_id,"user_id"=>$this->user_id),$edited_message))
        {
            echo "1";
        } else
        {
            echo "0";
        }
        
    }

    public function restart_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();
        $id = $this->input->post("table_id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('is_try_again'=>'1','posting_status'=>'1');
        $this->basic->update_data('sms_sending_campaign',$where,$data);
        echo '1';
    }

    public function ajax_sms_campaign_pause()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('sms_sending_campaign',array('id'=>$table_id),array('posting_status'=>'3','is_try_again'=>'0'));
        echo '1';
        
    }

    public function ajax_sms_campaign_play()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('sms_sending_campaign',array('id'=>$table_id),array('posting_status'=>'1','is_try_again'=>'1'));
        echo '1';
    }

    public function force_reprocess_sms_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        $this->ajax_check();
        $id = $this->input->post("id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('is_try_again'=>'1','posting_status'=>'1');
        $this->basic->update_data('sms_sending_campaign',$where,$data);
        if($this->db->affected_rows() != 0) echo "1";
        else  echo "0";
    }

    public function get_subscribers_phone()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        if($this->session->userdata('logged_in') != 1) exit();
        $this->ajax_check();
        $page_id=$this->input->post('page_id');// database id
        $user_gender=$this->input->post('user_gender');
        $user_time_zone=$this->input->post('user_time_zone');
        $user_locale=$this->input->post('user_locale');
        $load_label=$this->input->post('load_label');
        $label_ids=$this->input->post('label_ids');
        $excluded_label_ids=$this->input->post('excluded_label_ids');

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids =array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids =array();

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        $result = array();
        date_default_timezone_set('UTC');
        $current_time  = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-23 hour',strtotime($current_time)));
        $this->_time_zone_set();
        $dropdown=array();
        $str = $str2 = "";

        if($load_label=='1')
        {
            $str='<script>$("#label_ids").select2();</script> ';
            $str2='<script>$("#excluded_label_ids").select2();</script> ';
            $str .='<select multiple="multiple"  class="form-control" id="label_ids" name="label_ids[]" style="width:100%;">';
            $str2.='<select multiple="multiple"  class="form-control" id="excluded_label_ids" name="excluded_label_ids[]" style="width:100%;">';        

            foreach ($info_type as  $value)
            {                
                $str.=  "<option value='".$value['id']."'>".$value['group_name']."</option>";
                $str2.= "<option value='".$value['id']."'>".$value['group_name']."</option>"; 
            }

            $str.= '</select>';
            $str2.='</select>';
        }

        $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $page_info = isset($pageinfo[0])?$pageinfo[0]:array();

        if(isset($page_info['page_access_token'])) unset($page_info['page_access_token']);

        $subscriber_count = 0;

        $where_simple2 =array("page_table_id"=>$page_id,'is_bot_subscriber'=> '1','phone_number != '=>'','unavailable'=>'0','user_id'=>$this->user_id,'permission'=>'1');

        if(isset($user_gender) && $user_gender!="")  $where_simple2['messenger_bot_subscriber.gender'] = $user_gender;
        if(isset($user_time_zone) && $user_time_zone!="")  $where_simple2['messenger_bot_subscriber.timezone'] = $user_time_zone;
        if(isset($user_locale) && $user_locale!="")  $where_simple2['messenger_bot_subscriber.locale'] = $user_locale;
    
        $sql_part = "";
        if($load_label=='0')
        {
           if(count($label_ids)>0) $sql_part="("; else $sql_part="";        
           $sql_part_array=array();
           foreach ($label_ids as $key => $value) 
           {
              $sql_part_array[]="FIND_IN_SET('".$value."',contact_group_id) !=0";
           }
           $sql_part.=implode(' OR ', $sql_part_array);
           if(count($label_ids)>0) $sql_part.=")";
           if($sql_part!="") $this->db->where($sql_part);

           foreach ($excluded_label_ids as $key => $value) 
           {
              $sq="NOT FIND_IN_SET('".$value."',contact_group_id) !=0";
              $this->db->where($sq);
           }
        }

        $where2 = array('where'=>$where_simple2);
        $bot_subscriber=$this->basic->get_data("messenger_bot_subscriber",$where2,'count(id) as subscriber_count');
        $subscriber_count = isset($bot_subscriber[0]['subscriber_count'])? $bot_subscriber[0]['subscriber_count'] : 0;
        $page_info['subscriber_count'] = $subscriber_count;

        $page_total_subscribers = $this->basic->get_data("messenger_bot_subscriber",array("where"=>array("page_table_id"=>$page_id,'is_bot_subscriber'=> '1','phone_number != '=>'','unavailable'=>'0','user_id'=>$this->user_id,'permission'=>'1')));
        $page_info['page_total_subscribers'] = count($page_total_subscribers);

        echo json_encode(array('first_dropdown'=>$str,'second_dropdown'=>$str2,"pageinfo"=>$page_info));
    }

    public function contacts_total_numbers()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        if(!$_POST) exit;
        $this->ajax_check();

        $user_id = $this->user_id;
        $contacts_sms_group = $this->input->post('contact_ids', true);

        if(isset($contacts_sms_group) && !empty($contacts_sms_group))
        foreach ($contacts_sms_group as $key => $value) 
        {
            $where_simple = array('sms_email_contacts.user_id'=>$this->user_id,"sms_email_contacts.phone_number !="=>"");
            $this->db->where("FIND_IN_SET('$value',sms_email_contacts.contact_type_id) !=", 0);
            $where = array('where'=>$where_simple);    
            $contact_details = $this->basic->get_data('sms_email_contacts', $where,array("phone_number"));

            foreach ($contact_details as $key2 => $value2) 
            {   
                $contacts_id[] = isset($value2["id"]) ? $value2["id"]: "";
            }
        }

        $total_contact = isset($contacts_id) ? count($contacts_id):0;
        echo $total_contact;

    }

    public function create_sms_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) redirect('home/login_page', 'location');

        /**Get contact number and sms_email_contact_group***/
        $user_id = $this->user_id;
        $table_type = 'sms_email_contact_group';   
        $where_type['where'] = array('user_id'=>$user_id);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='type');

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>'1')),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');
        $data['page_info'] = $page_info;  
        $result = array();

        if(isset($info_type) && !empty($info_type))
        {
            foreach ($info_type as  $value) 
            {
                $search_key  = $value['id'];
                $search_type = $value['type'];

                $where_simple = array('sms_email_contacts.user_id'=>$user_id);
                $this->db->where("FIND_IN_SET('$search_key',sms_email_contacts.contact_type_id) !=", 0);
                $this->db->where('unsubscribed !=', 0);
                $where = array('where'=>$where_simple);

                $this->db->select("count(sms_email_contacts.id) as number_count",false);    

                $contact_details = $this->basic->get_data('sms_email_contacts', $where, $select='', $join='', $limit='', $start='', $order_by='sms_email_contacts.first_name', $group_by='', $num_rows=0);
                foreach ($contact_details as $key2 => $value2) 
                {
                    if($value2['number_count']>0)
                    $group_name[$search_key] = $search_type." (".$value2['number_count'].")";
                }
                    
            }  
        }   


        $where_simple = array();
        $temp_userid = $user_id;

        /***get sms config***/
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
        foreach ($sms_api_config as &$info) {

            $info['gateway_name'] = ($info['gateway_name'] == 'custom') ? $this->lang->line('Custom API')." : ".$info['custom_name'] : $info['gateway_name'];
            
            $id=$info['id'];

            if($info['phone_number'] !="")
                $sms_api_config_option[$id]=$info['gateway_name'].": ".$info['phone_number'];
            else
                $sms_api_config_option[$id]=$info['gateway_name'];

        }
        unset($info);

        $data['sms_option'] = $sms_api_config_option;
        $data['groups_name']  = isset($group_name) ? $group_name: "";
        $data['time_zone']  = $this->_time_zone_list();
        $data["time_zone_numeric"]= $this->_time_zone_list_numeric();
        $data['locale_list'] = $this->sdk_locale();
        $data['body'] = 'sms_email_manager/sms/create_sms_campaigns';
        $data['page_title'] = $this->lang->line('Create SMS Campaign');
        $this->_viewcontroller($data);
        
    }

    public function create_sms_campaign_action()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        if(!$_POST) exit();
        $this->ajax_check();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {                
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("This action is disabled in this demo account. Please signup as user and try this with your account")));
                exit();
            }
        }

        $report = array();

        $campaign_name = strip_tags(trim($this->input->post('campaign_name', true)));
        $message       = $this->input->post('message', true);
        $schedule_time = $this->input->post('schedule_time');
        $time_zone     = strip_tags(trim($this->input->post('time_zone', true)));
        $sms_api       = strip_tags(trim($this->input->post('from_sms', true)));
        $to_numbers    = trim($this->input->post('to_numbers', true));
        $campaign_delay    = trim($this->input->post('campaign_delay', true));
        $country_code_add  = trim($this->input->post('country_code_add', true));
        $country_code_remove  = trim($this->input->post('country_code_remove', true));

        $page_auto_id = $this->input->post('page',true); // page auto id
        $label_ids = $this->input->post('label_ids',true);
        $excluded_label_ids = $this->input->post('excluded_label_ids',true);
        $user_gender = $this->input->post('user_gender',true);
        $user_time_zone = $this->input->post('user_time_zone',true);
        $user_locale = $this->input->post('user_locale',true);

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids=array();

        if($time_zone=='') $time_zone = "Asia/Novosibirsk";

        $successfully_sent = 0;
        $added_at          = date("Y-m-d H:i:s");
        $posting_status    = "0";

        // Messenger Subscriber Section Started
        if(isset($page_auto_id) && !empty($page_auto_id))
        {
            $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,"user_id"=>$this->user_id)));
            if(!isset($pageinfo[0]))
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("Something went wrong.")));
                exit();
            }

            $fb_page_id  = $pageinfo[0]['page_id'];
            $page_name  = $pageinfo[0]['page_name'];

            $excluded_label_ids_temp=$excluded_label_ids;
            $unsubscribe_labeldata=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("user_id"=>$this->user_id,"page_id"=>$page_auto_id,"unsubscribe"=>"1")));
            foreach ($unsubscribe_labeldata as $key => $value) 
            {
                array_push($excluded_label_ids_temp, $value["id"]);
            }

            if(count($label_ids)>0) $sql_part="("; else $sql_part="";        
            $sql_part_array=array();
            foreach ($label_ids as $key => $value) 
            {
               $sql_part_array[]="FIND_IN_SET('".$value."',contact_group_id) !=0";
            }        
            if(count($label_ids)>0) 
            {
                $sql_part.=implode(' OR ', $sql_part_array);
                $sql_part.=") AND ";
            }

            $sql_part2="";
            $sql_part_array2=array();
            foreach ($excluded_label_ids_temp as $key => $value) 
            {
              $sql_part_array2[]="NOT FIND_IN_SET('".$value."',contact_group_id) !=0";          
            }        
            if(count($excluded_label_ids_temp)>0) 
            {
                $sql_part2=implode(' AND ', $sql_part_array2);
                $sql_part2.=" AND ";
            }

            $sql_part3="";
            $sql_part_array3 = array();
            if($user_gender!='') $sql_part_array3[] = "gender = '{$user_gender}'";
            if($user_time_zone!='') $sql_part_array3[] = "timezone = '{$user_time_zone}'";
            if($user_locale!='') $sql_part_array3[] = "locale = '{$user_locale}'";

            if(count($sql_part_array3)>0) 
            {
                $sql_part3 = implode(' AND ', $sql_part_array3);
                $sql_part3 .=" AND ";
            }

            $sql="SELECT * FROM messenger_bot_subscriber WHERE ".$sql_part." ".$sql_part2." ".$sql_part3." user_id = ".$this->user_id." AND page_table_id = {$page_auto_id} AND is_bot_subscriber='1' AND phone_number!='' AND permission='1';";
            $lead_list = $this->basic->execute_query($sql);

            if(isset($lead_list) && !empty($lead_list)){
                foreach ($lead_list as $lead_key => $lead_value) {

                    if($lead_value['phone_number'] == "") continue;

                    if(isset($country_code_add) & $country_code_add != '')
                    {
                        if(!preg_match("/^\+?{$country_code_add}/",$lead_value['phone_number'])) 
                        {
                            $lead_value['phone_number'] = $country_code_add.$lead_value['phone_number'];
                        }
                    }
                    else if(isset($country_code_remove) && $country_code_remove != '')
                    {
                        if(preg_match("/^\+?{$country_code_remove}/",$lead_value['phone_number'])) {
                            $lead_value['phone_number'] = preg_replace("/^\+?{$country_code_remove}/",'',$lead_value['phone_number']);
                        }
                    }

                    $report[$lead_value['phone_number']] = array(
                        'api_id'              => $sms_api,
                        'contact_id'          => '0',
                        'subscriber_id'       => $lead_value['id'],
                        'contact_first_name'  => isset($lead_value['first_name']) ? $lead_value['first_name']:"",
                        'contact_last_name'   => isset($lead_value['last_name']) ? $lead_value['last_name']:"",
                        'contact_email'       => isset($lead_value['email']) ? $lead_value['email']:"",
                        'contact_phone_number'=> isset($lead_value['phone_number']) ? $lead_value['phone_number']:"",
                        'sent_time'           =>'pending',
                        'delivery_id'         =>'pending',
                    );
                }
            }
        }
        // Messenger Subscriber Section Ended


        // Contact Group Section Started
        if(isset($to_numbers) && !empty($to_numbers))
        {
            $exploded_to_numbers = explode(',',$to_numbers);
            $exploded_to_numbers = array_unique($exploded_to_numbers);
        }

        $contacts_sms_group = $this->input->post('contacts_id', true);
        if(isset($contacts_sms_group) && !empty($contacts_sms_group))
            $contact_groupid    = implode(",",$contacts_sms_group);

        $manual_numbers = array();
        $contacts_id = array();


        if(isset($contacts_sms_group) && !empty($contacts_sms_group)){
            foreach ($contacts_sms_group as $key => $value) 
            {
                $where_simple = array('sms_email_contacts.user_id'=>$this->user_id);
                $this->db->where("FIND_IN_SET('$value',sms_email_contacts.contact_type_id) !=", 0);
                $where = array('where'=>$where_simple);    
                $contact_details = $this->basic->get_data('sms_email_contacts', $where);

                foreach ($contact_details as $key2 => $value2) 
                {   
                    if($value2['phone_number'] == "") continue;

                    if(isset($country_code_add) & $country_code_add != '')
                    {
                        if(!preg_match("/^\+?{$country_code_add}/",$value2['phone_number'])) 
                        {
                            $value2['phone_number'] = $country_code_add.$value2['phone_number'];
                        }
                    }
                    else if(isset($country_code_remove) && $country_code_remove != '')
                    {
                        if(preg_match("/^\+?{$country_code_remove}/",$value2['phone_number'])) {
                            $value2['phone_number'] = preg_replace("/^\+?{$country_code_remove}/",'',$value2['phone_number']);
                        }
                    }                    

                    $report[$value2['phone_number']] = array(
                        'api_id'              => $sms_api,
                        'contact_id'          => $value2['id'],
                        'subscriber_id'       => "0",
                        'contact_first_name'  => isset($value2['first_name']) ? $value2['first_name']:"",
                        'contact_last_name'   => isset($value2['last_name']) ? $value2['last_name']:"",
                        'contact_email'       => isset($value2['email']) ? $value2['email']:"",
                        'contact_phone_number'=> isset($value2['phone_number']) ? $value2['phone_number']:"",
                        'sent_time'           =>'pending',
                        'delivery_id'         =>'pending',
                    );

                    $contacts_id[] = isset($value2["id"]) ? $value2["id"]: "";
                }
            }
        }

        $contacts_id = array_filter($contacts_id);
        $contacts_id = array_unique($contacts_id);
        $contacts_id = implode(',', $contacts_id);

        // for manual phone number insertion
        $manual_thread = 0;
        if(isset($exploded_to_numbers))
        {
            foreach ($exploded_to_numbers as $single_values) 
            {
                if(isset($country_code_add) & $country_code_add != '')
                {
                    if(!preg_match("/^\+?{$country_code_add}/",$single_values)) 
                    {
                         $single_values = $country_code_add.$single_values;
                    }
                }
                else if(isset($country_code_remove) && $country_code_remove != '')
                {
                    // $single_values = preg_replace("/^\+?{$country_code_remove}/", '',$single_values);
                    if(preg_match("/^\+?{$country_code_remove}/",$single_values)) {
                        $single_values = preg_replace("/^\+?{$country_code_remove}/",'',$single_values);
                    }
                }

                $report[$single_values] = array(
                    'api_id' => $sms_api,
                    'contact_id' => '0',
                    'subscriber_id' => '0',
                    'contact_first_name'=> "",
                    'contact_last_name'=> "",
                    'contact_email'=> "",
                    'contact_phone_number'=>$single_values,
                    'sent_time' =>'pending',
                    'delivery_id' =>'pending',
                );

                $manual_thread++;
            }
        }
        // Contact Group Section Ended

        $thread = count($report);

        if($thread==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Campaign could not target any subscriber with phone number to reach message. Please try again with different targeting options.")));
            exit();
        }

        // inserting data of sms_campaign_campaign Table
        $inserted_data = array(
            "user_id"           => $this->user_id,
            "api_id"            => $sms_api,
            'page_id'           => isset($page_auto_id) ? $page_auto_id:"",
            'fb_page_id'        => isset($fb_page_id) ? $fb_page_id:"", 
            'page_name'         => isset($page_name) ? $page_name:"",
            "contact_ids"       => isset($contacts_id) ? $contacts_id:"",
            'contact_type_id'   => isset($contact_groupid) ? $contact_groupid:"",
            "campaign_name"     => $campaign_name,
            "campaign_message"  => str_replace(array("'",'"'),array('`','`'),$message),
            'manual_phone'      => $to_numbers,
            "posting_status"    => $posting_status, 
            "schedule_time"     => $schedule_time,
            "time_zone"         => $time_zone,
            "total_thread"      => $thread,
            "successfully_sent" => $successfully_sent,
            "campaign_delay"    => $campaign_delay,
            "created_at"        => $added_at,
            'user_gender'       => isset($user_gender) ? $user_gender:"",
            'user_time_zone'    => isset($user_time_zone) ? $user_time_zone:"",
            'user_locale'       => isset($user_locale) ? $user_locale:""
        );

        if(!empty($label_ids)) 
            $inserted_data['label_ids'] = implode(',', $label_ids); 
        else 
            $inserted_data['label_ids'] ="";

        if(!empty($excluded_label_ids)) 
            $inserted_data['excluded_label_ids'] = implode(',', $excluded_label_ids); 
        else 
            $inserted_data['excluded_label_ids'] = "";

        $fb_label_names = array();
        if(!empty($label_ids))
        {
            $fb_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids)));
            foreach ($fb_label_data as $key => $value) 
            {
               if($value['invisible']=='0')
                $fb_label_names[]=$value["group_name"];
            }  
        }

        $inserted_data['label_names'] = implode(',', $fb_label_names);

        $status = $this->_check_usage($module_id=264,$request=$thread);
        if($status=="3")  //monthly limit is exceeded, can not send another ,message this month
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Sorry, your monthly limit to send SMS is exceeded.")));
            exit();
        }


        if($this->basic->insert_data("sms_sending_campaign", $inserted_data))
        {
            // getting inserted row id
            $campaign_id = $this->db->insert_id();

            $report_insert = array();
            foreach ($report as $key=>$value) 
            {
                $report_insert = array(
                    'user_id'              => $this->user_id,
                    'sms_api_id'           => $value['api_id'],
                    'campaign_id'          => $campaign_id,
                    'contact_id'           => $value['contact_id'],
                    'subscriber_id'        => $value['subscriber_id'],
                    'contact_first_name'   => $value['contact_first_name'],
                    'contact_last_name'    => $value['contact_last_name'],
                    'contact_email'        => $value['contact_email'],
                    'contact_phone_number' => $key,
                    'sent_time'            => '',
                    'delivery_id'          => 'pending',
                    'processed'            => '0'
                );
                
                $this->basic->insert_data("sms_sending_campaign_send", $report_insert);
            }

            $this->_insert_usage_log($module_id=264,$request=$thread);

            echo json_encode(array('status'=>'1'));
        } else
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Something went wrong, please try once again.')));
        }
    }

    public function edit_sms_campaign($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) redirect('home/login_page', 'location');

        if($id==0) exit();

        $data['body']          = "sms_email_manager/sms/edit_sms_campaigns";
        $data["time_zone"]     = $this->_time_zone_list();
        $data["campaign_data"] = $this->basic->get_data("sms_sending_campaign",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $data['selected_contact_gorups'] = explode(",",$data['campaign_data'][0]['contact_type_id']);

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>'1')),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');
        $data['page_info'] = $page_info;

        // only pending campaigns are editable
        if(!isset($data["campaign_data"][0]["posting_status"]) || $data["campaign_data"][0]["posting_status"]!='0' ) exit();

        // only scheduled campaigns can be editted
        if(!isset($data["campaign_data"][0]["time_zone"]) || $data["campaign_data"][0]["time_zone"]=='' ) exit();
        
        /**Get contact number and   sms_email_contact_group***/
        $user_id = $this->user_id;
        $table_type = ' sms_email_contact_group';   
        $where_type['where'] = array('user_id'=>$user_id);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='type');  
        $result = array();

        if(isset($info_type) && !empty($info_type))
        {
            foreach ($info_type as  $value) 
            {
                $search_key = $value['id'];
                $search_type = $value['type'];

                $where_simple = array('sms_email_contacts.user_id' => $this->user_id);
                $this->db->where("FIND_IN_SET('$search_key',sms_email_contacts.contact_type_id) !=", 0);
                $where = array('where'=>$where_simple);
                $this->db->select("count(sms_email_contacts.id) as number_count",false);    
                $contact_details = $this->basic->get_data('sms_email_contacts', $where, $select='', $join='', $limit='', $start='', $order_by='sms_email_contacts.first_name', $group_by='', $num_rows=0);
            
                foreach ($contact_details as $key2 => $value2) 
                {
                    if($value2['number_count']>0)
                    $group_name[$search_key] = $search_type." (".$value2['number_count'].")";
                }
                    
            }  
        }   

                                                        
        /***get sms config***/
        $apiAccess = $this->config->item('sms_api_access');
        if($this->config->item('sms_api_access') == "") $apiAccess = "0";

        if($apiAccess == '1' && $this->session->userdata("user_type") == 'Member')
        {
            $join = array('users' => 'sms_api_config.user_id=users.id,left');
            $select = array('sms_api_config.*','users.id AS usersId','users.user_type');
            $where_in = array('sms_api_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','User'));
            $where = array('where'=> array('sms_api_config.status'=>'1'),'where_in'=>$where_in);
            $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select, $join, $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        } else
        {
            $where = array("where" => array('user_id'=>$this->user_id,'status'=>'1'));
            $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select='', $join='', $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        }
        
        $sms_api_config_option = array();

        foreach ($sms_api_config as $info) {

            $info['gateway_name'] = ($info['gateway_name'] == 'custom') ? $this->lang->line('Custom API')." : ".$info['custom_name'] : $info['gateway_name'];

            $id = $info['id'];
            if($info['phone_number'] != "")
                $sms_api_config_option[$id] = $info['gateway_name'].": ".$info['phone_number'];
            else
                $sms_api_config_option[$id] = $info['gateway_name'];
        }

        $data['locale_list']       = $this->sdk_locale();
        $data["time_zone_numeric"] = $this->_time_zone_list_numeric();
        $data['sms_option']        = $sms_api_config_option;
        $data['groups_name']       = isset($group_name) ? $group_name: "";
        $data['page_title']        = $this->lang->line('Edit SMS Campaign');

        $this->_viewcontroller($data);   
    }

    public function edit_sms_campaign_action()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(264,$this->module_access)) exit;

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {                
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("This action is disabled in this demo account. Please signup as user and try this with your account")));
                exit();
            }
        }

        if(!$_POST) exit();

        $report = array();

        $campaign_id   = $this->input->post('campaign_id',true);
        $previous_thread = $this->input->post("previous_thread");
        $schedule_name = strip_tags(trim($this->input->post('campaign_name', true)));
        $message       = $this->input->post('message', true);
        $schedule_time = $this->input->post('schedule_time');
        $time_zone     = strip_tags(trim($this->input->post('time_zone', true)));
        $sms_api       = strip_tags(trim($this->input->post('from_sms', true)));
        $to_numbers    = trim($this->input->post('to_numbers', true));
        $campaign_delay    = trim($this->input->post('campaign_delay', true));
        $country_code_add  = trim($this->input->post('country_code_add', true));
        $country_code_remove  = trim($this->input->post('country_code_remove', true));

        $page_auto_id = $this->input->post('page',true); // page auto id
        $label_ids = $this->input->post('label_ids',true);
        $excluded_label_ids = $this->input->post('excluded_label_ids',true);
        $user_gender = $this->input->post('user_gender',true);
        $user_time_zone = $this->input->post('user_time_zone',true);
        $user_locale = $this->input->post('user_locale',true);

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids=array();

        if($time_zone=='') $time_zone = "Asia/Novosibirsk";

        $successfully_sent  = 0;
        $added_at           = date("Y-m-d H:i:s");
        $posting_status     = "0";

        // Messenger Subscriber Section Started
        if(isset($page_auto_id) && !empty($page_auto_id))
        {
            $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,"user_id"=>$this->user_id)));
            if(!isset($pageinfo[0]))
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("Something went wrong.")));
                exit();
            }
            $fb_page_id  = $pageinfo[0]['page_id'];
            $page_name  = $pageinfo[0]['page_name'];

            $excluded_label_ids_temp=$excluded_label_ids;
            $unsubscribe_labeldata=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("user_id"=>$this->user_id,"page_id"=>$page_auto_id,"unsubscribe"=>"1")));
            foreach ($unsubscribe_labeldata as $key => $value) 
            {
                array_push($excluded_label_ids_temp, $value["id"]);
            }

            if(count($label_ids)>0) $sql_part="("; else $sql_part="";        
            $sql_part_array=array();
            foreach ($label_ids as $key => $value) 
            {
               $sql_part_array[]="FIND_IN_SET('".$value."',contact_group_id) !=0";
            }        
            if(count($label_ids)>0) 
            {
                $sql_part.=implode(' OR ', $sql_part_array);
                $sql_part.=") AND ";
            }

            $sql_part2="";
            $sql_part_array2=array();
            foreach ($excluded_label_ids_temp as $key => $value) 
            {
              $sql_part_array2[]="NOT FIND_IN_SET('".$value."',contact_group_id) !=0";          
            }        
            if(count($excluded_label_ids_temp)>0) 
            {
                $sql_part2=implode(' AND ', $sql_part_array2);
                $sql_part2.=" AND ";
            }

            $sql_part3="";
            $sql_part_array3 = array();
            if($user_gender!='') $sql_part_array3[] = "gender = '{$user_gender}'";
            if($user_time_zone!='') $sql_part_array3[] = "timezone = '{$user_time_zone}'";
            if($user_locale!='') $sql_part_array3[] = "locale = '{$user_locale}'";

            if(count($sql_part_array3)>0) 
            {
                $sql_part3 = implode(' AND ', $sql_part_array3);
                $sql_part3 .=" AND ";
            }

            $sql="SELECT * FROM messenger_bot_subscriber WHERE ".$sql_part." ".$sql_part2." ".$sql_part3." user_id = ".$this->user_id." AND page_table_id = {$page_auto_id} AND is_bot_subscriber='1' AND phone_number!='' AND permission='1';";
            $lead_list = $this->basic->execute_query($sql);

            if(isset($lead_list) && !empty($lead_list)){
                foreach ($lead_list as $lead_key => $lead_value) {

                    if($lead_value['phone_number'] == "") continue;

                    if(isset($country_code_add) & $country_code_add != '')
                    {
                        if(!preg_match("/^\+?{$country_code_add}/",$lead_value['phone_number'])) 
                        {
                            $lead_value['phone_number'] = $country_code_add.$lead_value['phone_number'];
                        }
                    }
                    else if(isset($country_code_remove) && $country_code_remove != '')
                    {
                        // $lead_value['phone_number'] = preg_replace("/^\+?{$country_code_remove}/", '',$lead_value['phone_number']);
                        if(preg_match("/^\+?{$country_code_remove}/",$lead_value['phone_number'])) {
                            $lead_value['phone_number'] = preg_replace("/^\+?{$country_code_remove}/",'',$lead_value['phone_number']);
                        }
                    }

                    $report[$lead_value['phone_number']] = array(
                        'api_id'              => $sms_api,
                        'contact_id'          => '0',
                        'subscriber_id'       => $lead_value['id'],
                        'contact_first_name'  => isset($lead_value['first_name']) ? $lead_value['first_name']:"",
                        'contact_last_name'   => isset($lead_value['last_name']) ? $lead_value['last_name']:"",
                        'contact_email'       => isset($lead_value['email']) ? $lead_value['email']:"",
                        'contact_phone_number'=> isset($lead_value['phone_number']) ? $lead_value['phone_number']:"",
                        'sent_time'           =>'pending',
                        'delivery_id'         =>'pending',
                    );
                }
            }
        }
        // Messenger Subscriber Section Ended

        $contacts_sms_group = $this->input->post('contacts_id', true);
        if(isset($contacts_sms_group) && !empty($contacts_sms_group))
            $contact_groupid    = implode(",",$contacts_sms_group);

        if(!empty($to_numbers))
        {
            $exploded_to_numbers = explode(',',$to_numbers);
            $exploded_to_numbers = array_unique($exploded_to_numbers);
        }

        $manual_numbers = array();
        $contacts_id = array();
        $total_user = array();

        if(isset($contacts_sms_group) && !empty($contacts_sms_group)){
            foreach ($contacts_sms_group as $key => $value) 
            {
                $where_simple = array('sms_email_contacts.user_id'=>$this->user_id);
                $this->db->where("FIND_IN_SET('$value',sms_email_contacts.contact_type_id) !=", 0);
                $where = array('where'=>$where_simple);

                $contact_details = $this->basic->get_data('sms_email_contacts', $where, $select='');
                foreach ($contact_details as $key2 => $value2) 
                {
                    if($value2['phone_number'] == "") continue;

                    if(isset($country_code_add) & $country_code_add != '')
                    {
                        if(!preg_match("/^\+?{$country_code_add}/",$value2['phone_number'])) 
                        {
                            $value2['phone_number'] = $country_code_add.$value2['phone_number'];
                        }
                    }
                    else if(isset($country_code_remove) && $country_code_remove != '')
                    {
                        if(preg_match("/^\+?{$country_code_remove}/",$value2['phone_number'])) 
                        {
                            $value2['phone_number'] = preg_replace("/^\+?{$country_code_remove}/",'',$value2['phone_number']);
                        }

                    }

                    $fullname = $value2['first_name']." ".$value2['last_name'];

                    $report[$value2['phone_number']] = array(
                        'api_id'               => $sms_api,
                        'contact_id'           => $value2['id'],
                        'subscriber_id'        => "0",
                        'contact_first_name'   => isset($value2['first_name']) ? $value2['first_name']:"",
                        'contact_last_name'    => isset($value2['last_name']) ? $value2['last_name']:"",
                        'contact_email'        => isset($value2['email']) ? $value2['email']:"",
                        'contact_phone_number' => isset($value2['phone_number']) ? $value2['phone_number']:"",
                        'sent_time'            =>'pending',
                        'delivery_id'          =>'pending',
                    );
                  
                    $contacts_id[] = isset($value2["id"]) ? $value2["id"]: "";
                }
            }
        }
        
        // for manual phone number insertion into report
        $manual_thread = 0;
        if(isset($exploded_to_numbers))
        {
            foreach ($exploded_to_numbers as $single_values) 
            {
                if(isset($country_code_add) & $country_code_add != '')
                {
                    if(preg_match("/^\+?{$country_code_add}/",$single_values)) 
                    {
                        $single_values = $single_values;
                    }
                    else
                    { 
                        $single_values = $country_code_add.$single_values;
                    }

                }
                else if(isset($country_code_remove) && $country_code_remove != '')
                {
                    // $single_values = preg_replace("/^\+?{$country_code_remove}/", '',$single_values);
                    if(preg_match("/^\+?{$country_code_remove}/",$single_values)) {
                        $single_values = preg_replace("/^\+?{$country_code_remove}/",'',$single_values);
                    }
                    else
                    { 
                        $single_values = $single_values;
                    }

                } else
                {
                    $single_values = $single_values;
                }

                $report[$single_values] = array(
                    'api_id'            => $sms_api,
                    'contact_id'        => "0",
                    'subscriber_id'     => "0",
                    'contact_first_name'=> "",
                    'contact_last_name'=> "",
                    'contact_username'  => "",
                    'contact_email'     => "",
                    'contact_phone_number'=>$single_values,
                    'sent_time' =>'pending',
                    'delivery_id' =>'pending',
                );

                $manual_thread++;
            }
        }

        $contacts_id = array_filter($contacts_id);
        $contacts_id = array_unique($contacts_id);
        $contacts_id = implode(',', $contacts_id);

        $thread = count($report);

        if($thread==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Campaign could not target any subscriber with phone number to reach message. Please try again with different targeting options.")));
            exit();
        }

        // updating data of sms_campaign_campaign Table
        $updated_data = array(
            "user_id"           => $this->user_id,
            "api_id"            => $sms_api,
            'page_id'           => isset($page_auto_id) ? $page_auto_id:"",
            'fb_page_id'        => isset($fb_page_id) ? $fb_page_id:"", 
            'page_name'         => isset($page_name) ? $page_name:"",
            "contact_ids"       => isset($contacts_id) ? $contacts_id:"",
            'contact_type_id'   => isset($contact_groupid) ? $contact_groupid:"",
            "campaign_name"     => $schedule_name,
            "campaign_message"  => str_replace(array("'",'"'),array('`','`'),$message),
            'manual_phone'      => $to_numbers,
            "posting_status"    => $posting_status, 
            "schedule_time"     => $schedule_time,
            "time_zone"         => $time_zone,
            "total_thread"      => $thread,
            "successfully_sent" => $successfully_sent,
            "campaign_delay"    => $campaign_delay,
            "created_at"        => $added_at,
            'user_gender'       => isset($user_gender) ? $user_gender:"",
            'user_time_zone'    => isset($user_time_zone) ? $user_time_zone:"",
            'user_locale'       => isset($user_locale) ? $user_locale:""
        );

        if(!empty($label_ids)) 
            $updated_data['label_ids'] = implode(',', $label_ids); 
        else 
            $updated_data['label_ids'] ="";

        if(!empty($excluded_label_ids)) 
            $updated_data['excluded_label_ids'] = implode(',', $excluded_label_ids); 
        else 
            $updated_data['excluded_label_ids'] = "";

        $fb_label_names = array();
        if(!empty($label_ids))
        {
            $fb_label_data = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids)));
            foreach ($fb_label_data as $key => $value) 
            {
               if($value['invisible']=='0')
               $fb_label_names[]=$value["group_name"];
            }  
        }

        $updated_data['label_names'] = implode(',', $fb_label_names);

        $current_total_thread = $previous_thread - $thread;
        $current_total_thread_abs = abs($current_total_thread);
        if($current_total_thread<0)
        {
            $status=$this->_check_usage($module_id=264,$request=$current_total_thread_abs);
             if($status=="3")  //monthly limit is exceeded, can not send another ,message this month
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("Sorry, your monthly limit to send SMS is exceeded.")));
                exit();
            }
        }

        /* updating sms_sending_campaign table data of the campaign */
        if($this->basic->update_data("sms_sending_campaign", array("id" => $campaign_id,"user_id"=>$this->user_id), $updated_data))
        {
            /* Delete the rows of updated campaign from sms_sending_campaign_send table */
            $this->basic->delete_data("sms_sending_campaign_send", array("campaign_id" =>$campaign_id));

            $report_insert = array();
            foreach ($report as $key=>$value) 
            {
                $report_insert = array(
                    'user_id'              => $this->user_id,
                    'sms_api_id'           => $value['api_id'],
                    'campaign_id'          => $campaign_id,
                    'contact_id'           => $value['contact_id'],
                    'subscriber_id'        => $value['subscriber_id'],
                    'contact_first_name'   => $value['contact_first_name'],
                    'contact_last_name'    => $value['contact_last_name'],
                    'contact_email'        => $value['contact_email'],
                    'contact_phone_number' => $key,
                    'delivery_id'          => 'pending',
                    'sent_time'            => '',
                    'processed'            => '0'
                );

                /* Inserting again the updated report data into sms_sending_campaign_send table */
                $this->basic->insert_data("sms_sending_campaign_send", $report_insert);
            }

            if($current_total_thread<0){
                $this->_insert_usage_log($module_id=264,$request=$current_total_thread_abs);
            }
            else {
                $this->_delete_usage_log($module_id=264,$request=$current_total_thread_abs);
            }

            echo json_encode(array('status'=>'1'));
        } else
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Something went wrong, please try once again.')));
        }
    }

    // ===========================================================================================================
    //                                             Email Section                                                        
    // ============================================================================================================

    public function email_campaign_lists()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) {
            redirect('home/login_page', 'location');
        }

        $data['body'] = 'sms_email_manager/email/email_campaign/email_campaign_lists';
        $data['page_title'] = $this->lang->line('Email Campaign');
        $this->_viewcontroller($data);
    }

    public function email_campaign_lists_data()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $campaign_status     = trim($this->input->post("campaign_status",true));
        $searching_campaign  = trim($this->input->post("searching_campaign",true));
        $post_date_range = $this->input->post("post_date_range",true);

        $display_columns = array("#",'id','campaign_name','total_recipients','sent_successfully','number_of_unique_open','number_of_unique_clickers','total_unsubscribed','schedule_time','posting_status','actions');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();
        $where_simple['user_id'] = $this->user_id;

        if($post_date_range!="")
        {
            $exp = explode('|', $post_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";

            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(created_at,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(created_at,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($searching_campaign !="") $where_simple['campaign_name like'] = "%".$searching_campaign."%";
        if($campaign_status !="") $where_simple['posting_status'] = $campaign_status;

        $where  = array('where'=>$where_simple);

        $table = "email_sending_campaign";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        $total_rows_array = $this->basic->count_row($table,$where,$count="id",$join,$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        for($i = 0; $i < count($info); $i++)
        {
            $action_count = 3;
            $posting_status = $info[$i]['posting_status'];

            $total_thread = $info[$i]["total_thread"];
            $successfullySent = $info[$i]['successfully_sent'];


            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00") {

                $info[$i]['schedule_time'] = "<div style='min-width:140px !important;' class='text-dark text-center'>".date("M j, y h:i A",strtotime($info[$i]['schedule_time']))."</div>";
            }
            else {

                $info[$i]['schedule_time'] = "<div class='text-muted text-center'><i class='fas fa-times'></i></div>";
            }

            $email_api_infos = $this->basic->get_data($info[$i]['configure_email_table'],array('where'=>array('id'=>$info[$i]['api_id'])));

            if($info[$i]['configure_email_table'] == 'email_smtp_config') {
                $info[$i]['email_api'] = isset($email_api_infos[0]['email_address']) ? "SMTP - ".$email_api_infos[0]['email_address']: $this->lang->line("Corresponding API Information has been deleted.");
            } else if($info[$i]['configure_email_table'] == 'email_mailgun_config') {
                $info[$i]['email_api'] = isset($email_api_infos[0]['email_address']) ? "Mailgun - ".$email_api_infos[0]['email_address']: $this->lang->line("Corresponding API Information has been deleted.");
            } else if($info[$i]['configure_email_table'] == 'email_mandrill_config') {
                $info[$i]['email_api'] = isset($email_api_infos[0]['email_address']) ? "Mandrill - ".$email_api_infos[0]['email_address']: $this->lang->line("Corresponding API Information has been deleted.");
            } else if($info[$i]['configure_email_table'] == 'email_sendgrid_config') {
                $info[$i]['email_api'] = isset($email_api_infos[0]['email_address']) ? "Sendgrid - ".$email_api_infos[0]['email_address']: $this->lang->line("Corresponding API Information has been deleted.");
            }

            if(isset($info[$i]['email_attachment']) && $info[$i]['email_attachment'] != '') {

                $action_count++;
                $attachment = "<a target='_BLANK' href='".base_url('sms_email_manager/download_email_attachment/').$info[$i]['email_attachment']."/".$this->user_id."' data-toggle='tooltip' title='".$this->lang->line("Attachment")."' class='btn btn-circle btn-outline-info'><i class='fas fa-paperclip'></i></a>";
                $forReport = $info[$i]['email_attachment'];
            } else
            {
                $attachment = "";
                $forReport = "";
            }


            // sent column
            $info[$i]["total_recipients"] = "<div class='text-center text-dark fw_700'>".$total_thread." <sub class='text-muted fw_none'>100%</sub>";
            $sent_number = $successfullySent / $total_thread;
            $delivered_percentage = round($sent_number * 100);

            $info[$i]["sent_successfully"] = "<div class='text-center text-success fw_700'>".$successfullySent." <sub class='text-muted fw_none'>".$delivered_percentage."%</sub></div>";


            if($this->config->item("enable_open_rate") == "1") {

                if($successfullySent != "0" && $info[$i]['number_of_unique_open'] != "0") {

                    $openRate = $info[$i]['number_of_unique_open'] / $successfullySent;
                    $openRate_percentage = round($openRate * 100);

                    $info[$i]['number_of_unique_open'] = "<div class='text-center text-primary fw_700'>".$info[$i]['number_of_unique_open']." <sub class='text-muted fw_none'>".$openRate_percentage."%<sub></div>";

                } else {

                    $info[$i]['number_of_unique_open'] = "<div class='text-center text-primary fw_700'>".$info[$i]['number_of_unique_open']." <sub class='text-muted fw_none'>0%<sub></div>";
                }

            } else {

                unset($display_columns[5]);
            }

            if($this->config->item("enable_click_rate") == "1") {

                if($successfullySent != "0" && $info[$i]['number_of_unique_clickers'] != "0") {

                    $clickRate = $info[$i]['number_of_unique_clickers'] / $successfullySent;
                    $clickRate_percentage = round($clickRate * 100);

                    $info[$i]['number_of_unique_clickers'] = "<div class='text-center text-info fw_700'>".$info[$i]['number_of_unique_clickers']." <sub class='text-muted fw_none'>".$clickRate_percentage."%</div>";
                } else {

                    $info[$i]['number_of_unique_clickers'] = "<div class='text-center text-info fw_700'>".$info[$i]['number_of_unique_clickers']." <sub class='text-muted fw_none'>0%</div>";
                }

                if($successfullySent != "0" && $info[$i]['total_unsubscribed'] != "0") {

                    $unsubscribedaRate = $info[$i]['total_unsubscribed'] / $successfullySent;
                    $unsubscribedaRate_percentage = round($unsubscribedaRate * 100);
                    $info[$i]['total_unsubscribed'] = "<div class='text-warning text-center fw_700'>".$info[$i]['total_unsubscribed']." <sub class='text-muted fw_none'>".$unsubscribedaRate_percentage."%</div>";
                } else {

                    $info[$i]['total_unsubscribed'] = "<div class='text-warning text-center fw_700'>".$info[$i]['total_unsubscribed']." <sub class='text-muted fw_none'>0%</div>";
                }


            } else {

                unset($display_columns[6]);
                unset($display_columns[7]);
            }

            // generating delete button
            if($posting_status=='1') {
                $delete_btn = "<a href='#' class='btn btn-circle btn-light pointer text-muted' data-toggle='tooltip' title='".$this->lang->line("Campaign in processing can not be deleted. You can pause campaign and then delete it.")."'><i class='fa fa-trash'></i></a>";
            } else { 
                $delete_btn =  "<a href='#' data-toggle='tooltip' title='".$this->lang->line("delete campaign")."' id='".$info[$i]['id']."' class='delete_email_campaign btn btn-circle btn-outline-danger'><i class='fa fa-trash'></i></a>";
            }

            $is_try_again = $info[$i]["is_try_again"];

            $force_porcess_str="";

            // generating restat and force processing button
            $number_of_email_to_be_sent_in_try = $this->config->item("number_of_email_to_be_sent_in_try");

            if($number_of_email_to_be_sent_in_try == "") { 
                $number_of_email_to_be_sent_in_try = 10; 
            }

            if($number_of_email_to_be_sent_in_try == "" || $number_of_email_to_be_sent_in_try == "0") {
                $force_porcess_str="";
            }
            else
            {
                $action_count++;
                if($posting_status=='1' && $is_try_again=='1') {

                    $force_porcess_str .= "<a href='#' class='btn btn-circle btn-outline-dark pause_email_campaign_info' table_id='".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("Pause Campaign")."'><i class='fas fa-pause'></i></a>";
                }

                if($posting_status=='3') {

                    $force_porcess_str .= "<a href='#' class='btn btn-circle btn-outline-success play_email_campaign_info' table_id='".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("Start Campaign")."'><i class='fas fa-play'></i></a>";
                }
            }

            if($posting_status=='1') {
                $action_count++;
                $force_porcess_str .= "<a href='#' id='".$info[$i]['id']."' class='force_email btn btn-circle btn-outline-warning' data-toggle='tooltip' title='".$this->lang->line("force reprocessing")."'><i class='fas fa-sync'></i></a>";
            }

            // status
            if( $posting_status == '2') 
                $info[$i]['posting_status'] = '<div style="min-width:100px"><span class="text-success text-center badge"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</span></div>';
            else if( $posting_status == '1') 
                $info[$i]['posting_status'] = '<div style="min-width:100px"><span class="text-warning text-center badge"><i class="fas fa-spinner"></i> '.$this->lang->line("Processing").'</span></div>';
            else if( $posting_status == '3') 
                $info[$i]['posting_status'] = '<div style="min-width:100px"><span class="text-muted text-center badge"><i class="fas fa-stop"></i> '.$this->lang->line("Paused").'</span></div>';
            else 
                $info[$i]['posting_status'] = '<div style="min-width:100px"><span class="text-danger text-center badge"><i class="far fa-times-circle"></i> '.$this->lang->line("Pending").'</span></div>';


            $report_btn = "<a target='_BLANK' href='".base_url('sms_email_manager/email_campaign_reports/').$info[$i]['id']."' class='btn btn-circle btn-outline-primary' data-toggle='tooltip' title='".$this->lang->line("View Report")."'><i class='fas fa-eye'></i> </a>";

            if($posting_status != '0' || $info[$i]['time_zone'] == "") 
                $edit_btn = "<a href='#' data-toggle='tooltip' title='".$this->lang->line("only pending campaigns are editable")."' class='btn btn-circle btn-light'><i class='fas fa-edit'></i></a>";
            else
            {
                $edit_url = site_url('sms_email_manager/edit_email_campaign/'.$info[$i]['id']);
                $edit_btn =  "<a data-toggle='tooltip' title='".$this->lang->line('edit campaign')."' href='".$edit_url."' class='btn btn-circle btn-outline-warning'><i class='fas fa-edit'></i></a>";
            }

            $action_width = ($action_count*47)+20;
            $info[$i]['actions'] ='
            <div class="dropdown d-inline dropright text-center">
              <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
                $info[$i]['actions'] .= $report_btn;
                $info[$i]['actions'] .= $edit_btn;
                $info[$i]['actions'] .= $force_porcess_str;
                if(isset($attachment) && !empty($attachment))
                    $info[$i]['actions'] .= $attachment;

                $info[$i]['actions'] .= $delete_btn;
                $info[$i]['actions'] .="
              </div>
            </div>
            <script>
            $('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function email_campaign_reports($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) redirect('home/login_page', 'location');
        if($id==0 || $id == "") exit();

        $where['where'] = array("user_id"=>$this->user_id, "id"=>$id);
        $campaign_datas = $this->basic->get_data("email_sending_campaign",$where);

        $email_api_infos = $this->basic->get_data($campaign_datas[0]['configure_email_table'],array('where'=>array('id'=>$campaign_datas[0]['api_id'])));


        if($campaign_datas[0]['configure_email_table'] == 'email_smtp_config') {

            $campaign_datas[0]['email_api'] = isset($email_api_infos[0]['email_address']) ? "SMTP - ".$email_api_infos[0]['email_address']: $this->lang->line("Corresponding API Information has been deleted.");

        } else if($campaign_datas[0]['configure_email_table'] == 'email_mailgun_config') {

            $campaign_datas[0]['email_api'] = isset($email_api_infos[0]['email_address']) ? "Mailgun - ".$email_api_infos[0]['email_address']: $this->lang->line("Corresponding API Information has been deleted.");

        } else if($campaign_datas[0]['configure_email_table'] == 'email_mandrill_config') {

            $campaign_datas[0]['email_api'] = isset($email_api_infos[0]['email_address']) ? "Mandrill - ".$email_api_infos[0]['email_address']: $this->lang->line("Corresponding API Information has been deleted.");

        } else if($campaign_datas[0]['configure_email_table'] == 'email_sendgrid_config') {

            $campaign_datas[0]['email_api'] = isset($email_api_infos[0]['email_address']) ? "Sendgrid - ".$email_api_infos[0]['email_address']: $this->lang->line("Corresponding API Information has been deleted.");
            
        }


        if($campaign_datas[0]['posting_status'] == "0") {

            $campaign_datas[0]['status'] = "<span class='text-danger'>".$this->lang->line("Pending")."</span>";

        } else if($campaign_datas[0]['posting_status'] == "1") {

            $campaign_datas[0]['status'] = "<span class='text-warning'>".$this->lang->line("Processing")."</span>";

        } else if($campaign_datas[0]['posting_status'] == "2") {

            $campaign_datas[0]['status'] = "<span class='text-success'>".$this->lang->line("Completed")."</span>";

        } else if($campaign_datas[0]['posting_status'] == "3") {

            $campaign_datas[0]['status'] = "<span class='text-dark'>".$this->lang->line("Paused")."</span>";

        }

        // Get percentage of sending thread
        $campaign_datas[0]['sent'] = $campaign_datas[0]['successfully_sent'].'/'.$campaign_datas[0]['total_thread'];
        $sent_percentage = $campaign_datas[0]['successfully_sent'] / $campaign_datas[0]['total_thread'];
        $campaign_datas[0]['sent_rate'] = round($sent_percentage * 100);

        // calculation open rate
        if($campaign_datas[0]['successfully_sent'] != "0" && $campaign_datas[0]['number_of_unique_open'] != "0") {

            $open_rate_result = $campaign_datas[0]['number_of_unique_open'] / $campaign_datas[0]['successfully_sent'];
            $campaign_datas[0]['open_rate'] = round($open_rate_result * 100);

        } else {
            $campaign_datas[0]['open_rate'] = 0;
        }

        // calculating Click Rate
        if($campaign_datas[0]['successfully_sent'] != "0" && $campaign_datas[0]['number_of_unique_clickers'] != "0") {

            $click_rate_result = $campaign_datas[0]['number_of_unique_clickers'] / $campaign_datas[0]['successfully_sent'];
            $campaign_datas[0]['click_rate'] = round($click_rate_result * 100);

        } else {
            $campaign_datas[0]['click_rate'] = 0;
        }

        // calculating Subscription Rate
        if($campaign_datas[0]['successfully_sent'] != "0" && $campaign_datas[0]['total_unsubscribed'] != "0") {

            $unsubscribe_rate_result = $campaign_datas[0]['total_unsubscribed'] / $campaign_datas[0]['successfully_sent'];
            $campaign_datas[0]['unsubscribe_rate'] = round($unsubscribe_rate_result * 100);

        } else {
            $campaign_datas[0]['unsubscribe_rate'] = 0;
        }

        if($campaign_datas[0]['number_of_unique_clickers'] != "0" && $campaign_datas[0]['number_of_unique_open'] != "0") {

            $click_to_open_rate_result = $campaign_datas[0]['number_of_unique_clickers'] / $campaign_datas[0]['number_of_unique_open'];
            $campaign_datas[0]['click_to_open_rate'] = round($click_to_open_rate_result * 100);
        } else {

            $campaign_datas[0]['click_to_open_rate'] = 0;

        }

        if($campaign_datas[0]['last_clicked_at'] != "0000-00-00 00:00:00") {

            $campaign_datas[0]['last_clicked_at'] = date("M j, y h:i A",strtotime($campaign_datas[0]['last_clicked_at']));

        } else {

            $campaign_datas[0]['last_clicked_at'] = "00-00-0000 00:00";
        }

        if($campaign_datas[0]['completed_at'] != "0000-00-00 00:00:00") {

            $campaign_datas[0]['completed_at'] = date("M j, y h:i A",strtotime($campaign_datas[0]['completed_at']));

        } else {

            $campaign_datas[0]['completed_at'] = $this->lang->line("Not Sent Yet");
        }


        if($campaign_datas[0]['last_unsubscribed_at'] != "0000-00-00 00:00:00") {

            $campaign_datas[0]['last_unsubscribed_at'] = date("M j, y h:i A",strtotime($campaign_datas[0]['last_unsubscribed_at']));

        } else {

            $campaign_datas[0]['last_unsubscribed_at'] = "00-00-0000 00:00";
        }


        $data['cam_data'] = $campaign_datas;
        $data['body'] = "sms_email_manager/email/email_campaign/email_campaign_report";
        $data['page_title'] = $this->lang->line("Campaign Report");
        $this->_viewcontroller($data);        
    }


    public function ajax_get_email_campaign_report_info()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check();

        $table_id = $this->input->post('table_id');
        $search_value = trim($this->input->post("searching",true));
        $rate_type = trim($this->input->post("rate_type",true));    

        $display_columns = array("#","id","contact_first_name","contact_last_name","contact_email","sent_time","delivery_id","is_open","number_of_time_open","email_opened_at",'is_clicked',"number_of_clicked","is_unsubscribed","last_clicked_at");
        $search_columns = array('contact_first_name','contact_last_name', 'contact_email');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom ="campaign_id = ".$table_id." AND user_id = ".$this->user_id;

        if ($search_value != '') {

            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        if($rate_type != '') {

            if($rate_type == "open") {

                $where_custom .= " AND is_open = '1'";

            } else if($rate_type == "click") {

                $where_custom .= " AND is_clicked = '1'";

            } else if($rate_type == "unsubscribe") {

                $where_custom .= " AND is_unsubscribed = '1'";
            }
        }

        $table="email_sending_campaign_send";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table,$where='',$count="id",$join='',$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        for ($i=0; $i <count($info) ; $i++) { 

            if($info[$i]['delivery_id'] == 'Submited') {

                $info[$i]['delivery_id'] = '<div style="min-width:100px"><span class="text-success badge"><i class="fas fa-check-circle"></i> '.$info[$i]['delivery_id'].'</span></div>';
            }
            else if($info[$i]['delivery_id'] == 'pending') {

                $info[$i]['delivery_id'] = '<div style="min-width:100px"><span class="text-danger badge"><i class="far fa-times-circle"> '.ucfirst($info[$i]['delivery_id']).'</span></div>';
            }
            else {

                $info[$i]['delivery_id'] = '<div style="min-width:100px"><span class="text-danger badge">'.$info[$i]['delivery_id'].'</span></div>';
            }

            if($info[$i]['sent_time'] != "0000-00-00 00:00:00") {

                $info[$i]['sent_time'] = "<div style='min-width:100px !important;'>".date("M j, y H:i",strtotime($info[$i]['sent_time']))."</div>";

            } else {

                $info[$i]['sent_time'] = "<div class='text-center'>x</div>";
            }


            if($this->config->item("enable_open_rate") == "1") {

                if($info[$i]['is_open'] == "1") {
                    $info[$i]['is_open'] = "<div class='font-weight-bold text-dark text-center'>".$this->lang->line('Yes')."</div>";
                }
                else {
                    $info[$i]['is_open'] = "<div class='font-weight-bold text-muted text-center'>".$this->lang->line('No')."</div>";
                }

                if($info[$i]['number_of_time_open'] != "0") {
                    $info[$i]['number_of_time_open'] = "<div class='text-center text-dark'>".$info[$i]['number_of_time_open']."</div>";
                } else {
                    $info[$i]['number_of_time_open'] = "<div class='text-center text-muted'>".$info[$i]['number_of_time_open']."</div>";
                }

                if($info[$i]['email_opened_at'] != "0000-00-00 00:00:00") {

                    $info[$i]['email_opened_at'] = "<div class='text-center' style='min-width:100px !important;'>".date("M j, Y H:i",strtotime($info[$i]['email_opened_at']))."</div>";

                } else {

                    $info[$i]['email_opened_at'] = "<div class='text-center'>x</div>";
                }

            } else {

                unset($display_columns[7]);
                unset($display_columns[8]);
                unset($display_columns[9]);
            }

            if($this->config->item("enable_click_rate") == "1") {


                if($info[$i]['number_of_clicked'] != "0") {

                    $info[$i]['number_of_clicked'] = "<div class='text-center text-dark'>".$info[$i]['number_of_clicked']."</div>";

                } else {

                    $info[$i]['number_of_clicked'] = "<div class='text-center text-muted'>".$info[$i]['number_of_clicked']."</div>";

                }

                if($info[$i]['is_clicked'] == "1") {
                    $info[$i]['is_clicked'] = "<div class='font-weight-bold text-dark text-center'>".$this->lang->line('Yes')."</div>";
                }
                else {
                    $info[$i]['is_clicked'] = "<div class='font-weight-bold text-muted text-center'>".$this->lang->line('No')."</div>";
                }

                if($info[$i]['is_unsubscribed'] == "1") {
                    $info[$i]['is_unsubscribed'] = "<div class='font-weight-bold text-dark text-center'>".$this->lang->line('Yes')."</div>";
                }
                else {
                    $info[$i]['is_unsubscribed'] = "<div class='font-weight-bold text-muted text-center'>".$this->lang->line('No')."</div>";
                }

                if($info[$i]['last_clicked_at'] != "0000-00-00 00:00:00") {

                    $info[$i]['last_clicked_at'] = "<div style='min-width:100px !important;'>".date("M j, Y H:i",strtotime($info[$i]['last_clicked_at']))."</div>";

                } else {

                    $info[$i]['last_clicked_at'] = "<div class='text-center'>x</div>";
                }

            } else {
                unset($display_columns[10]);
                unset($display_columns[11]);
                unset($display_columns[12]);
                unset($display_columns[13]);
            }
        }



        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function ajax_attachment_upload()
    {
       if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

       if($this->is_demo == '1')
       {
           if($this->session->userdata('user_type') == "Admin")
           {                
               echo json_encode(array('status'=>'0','message'=>$this->lang->line("This action is disabled in this demo account. Please signup as user and try this with your account")));
               exit();
           }
       }

       if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

       $ret = array();
       $output_dir = FCPATH."upload/attachment/";

       if(!file_exists($output_dir))
       {
           mkdir($output_dir,0777,true);
       }

       if (isset($_FILES["file"])) {

           $error = $_FILES["file"]["error"];

           $post_fileName = $_FILES["file"]["name"];
           $post_fileName_array = explode(".", $post_fileName);
           $ext = array_pop($post_fileName_array);
           $filename = implode('.', $post_fileName_array);
           $filename = $filename."_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

           $allow = ".png,.jpg,.jpeg,docx,.txt,.pdf,.ppt,.zip,.avi,.mp4,.mkv,.wmv,.mp3";
           $allow = str_replace('.', '', $allow);
           $allow = explode(',', $allow);
           if(!in_array(strtolower($ext), $allow)) 
           {
               echo json_encode("Are you kidding???");
               exit;
           }

           move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir.'/'.$filename);
           $ret[]= $filename;
           $this->session->set_userdata("attachment_file_path_name_scheduler", $output_dir.'/'.$filename);
           $this->session->set_userdata("attachment_filename_scheduler", $filename);
           echo json_encode($filename);
       } 
    }

    public function delete_attachment()
    {
        unlink($this->session->userdata("attachment_file_path_name_scheduler"));
        $this->session->unset_userdata("attachment_file_path_name_scheduler");
        $this->session->unset_userdata("attachment_filename_scheduler");
    }

    public function download_email_attachment($filename=0,$userid)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {                
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("This action is disabled in this demo account. Please signup as user and try this with your account")));
                exit();
            }
        }

        $user_id = $this->user_id;

        if($user_id != $userid) redirect('home/access_forbidden', 'location');

        $this->load->helper('download');
        $name = $filename;

        $fileDir = FCPATH.'upload/attachment/'.$filename;
        if(file_exists($fileDir)) {

            $data = file_get_contents(FCPATH.'upload/attachment/'.$filename); 
            force_download($name, $data);

        } else {

            $this->error_404();
        }
    }


    public function delete_email_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        if($this->is_demo == '1') {

            if($this->session->userdata('user_type') == "Admin") {                
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("This action is disabled in this demo account. Please signup as user and try this with your account")));
                exit();
            }
        }

        $this->ajax_check();

        $id = $this->input->post("campaign_id");
        if($id == "" || $id=="0") exit;

        $file_data = $this->basic->get_data("email_sending_campaign",array('where'=>array('id'=>$id,'user_id'=>$this->user_id)));

        if($file_data[0]['email_attachment'] != '') {

            $file = FCPATH."upload/attachment/".$file_data[0]['email_attachment'];

            if(file_exists($file)) { 
                unlink($file);
            }
        }

        if($this->basic->delete_data("email_sending_campaign",array("id"=>$id,"user_id"=>$this->user_id))) {

            if($this->basic->delete_data("email_sending_campaign_send",array("campaign_id"=>$id,"user_id"=>$this->user_id))) {
                
                echo "1";
            }
        }
        else {

            echo "0";
        }
    }

    public function edit_email_campaign_content($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) redirect('home/login_page', 'location');
        if($id==0 || $id == "") exit();

        $data['body'] = "sms_email_manager/email/email_campaign/edit_email_campaign_message_content";
        $data['page_title'] = $this->lang->line("Edit Message Contents");
        $data["message_data"] = $this->basic->get_data("email_sending_campaign",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $this->_viewcontroller($data);
    }

    public function edit_email_campaign_content_action()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {                
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("This action is disabled in this demo account. Please signup as user and try this with your account")));
                exit();
            }
        }

        $this->ajax_check();

        $table_id = $this->input->post("table_id",true);
        $user_id = $this->user_id;
        $message = $this->input->post("message");
        // $message  = preg_replace("@<(script|script[^>]+)>(.*)(</script>)?@mui", "[removed]disallowed characters[removed]", $message);
        $edited_message = array('email_message' => $message);

        if($this->basic->update_data('email_sending_campaign',array("id"=>$table_id,"user_id"=>$this->user_id),$edited_message))
        {
            echo "1";
        } else
        {
            echo "0";
        }  
    }

    public function restart_email_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;
        $this->ajax_check();
        $id = $this->input->post("table_id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('is_try_again'=>'1','posting_status'=>'1');
        $this->basic->update_data('email_sending_campaign',$where,$data);
        echo '1';
    }

    public function ajax_email_campaign_pause()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;
        $this->ajax_check();
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('email_sending_campaign',array('id'=>$table_id),array('posting_status'=>'3','is_try_again'=>'0'));
        echo '1';
        
    }

    public function ajax_email_campaign_play()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;
        $this->ajax_check();
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('email_sending_campaign',array('id'=>$table_id),array('posting_status'=>'1','is_try_again'=>'1'));
        echo '1';
    }

    public function force_reprocess_email_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;
        $this->ajax_check();
        $id = $this->input->post("id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('is_try_again'=>'1','posting_status'=>'1');
        $this->basic->update_data('email_sending_campaign',$where,$data);
        if($this->db->affected_rows() != 0) echo "1";
        else  echo "0";
    }

    public function get_subscribers_email()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        if($this->session->userdata('logged_in') != 1) exit();

        $this->ajax_check();

        $page_id        = $this->input->post('page_id');// database id
        $user_gender    = $this->input->post('user_gender');
        $user_time_zone = $this->input->post('user_time_zone');
        $user_locale    = $this->input->post('user_locale');
        $load_label     = $this->input->post('load_label');
        $label_ids      = $this->input->post('label_ids');

        $excluded_label_ids = $this->input->post('excluded_label_ids');

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids =array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids =array();

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        $result = array();
        date_default_timezone_set('UTC');
        $current_time  = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-23 hour',strtotime($current_time)));
        $this->_time_zone_set();
        $dropdown=array();
        $str = $str2 = "";

        if($load_label=='1')
        {
            $str='<script>$("#label_ids").select2();</script> ';
            $str2='<script>$("#excluded_label_ids").select2();</script> ';
            $str .='<select multiple="multiple"  class="form-control" id="label_ids" name="label_ids[]" style="width:100%;">';
            $str2.='<select multiple="multiple"  class="form-control" id="excluded_label_ids" name="excluded_label_ids[]" style="width:100%;">';        

            foreach ($info_type as  $value)
            {                
                $str.=  "<option value='".$value['id']."'>".$value['group_name']."</option>";
                $str2.= "<option value='".$value['id']."'>".$value['group_name']."</option>"; 
            }

            $str.= '</select>';
            $str2.='</select>';
        }

        $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $page_info = isset($pageinfo[0])?$pageinfo[0]:array();

        if(isset($page_info['page_access_token'])) unset($page_info['page_access_token']);

        $subscriber_count = 0;

        $where_simple2 =array("page_table_id"=>$page_id,'is_bot_subscriber'=> '1','email != '=>'','is_email_unsubscriber'=>'0','unavailable'=>'0','user_id'=>$this->user_id,'permission'=>'1');

        if(isset($user_gender) && $user_gender!="")  $where_simple2['messenger_bot_subscriber.gender'] = $user_gender;
        if(isset($user_time_zone) && $user_time_zone!="")  $where_simple2['messenger_bot_subscriber.timezone'] = $user_time_zone;
        if(isset($user_locale) && $user_locale!="")  $where_simple2['messenger_bot_subscriber.locale'] = $user_locale;
    
        $sql_part = "";
        if($load_label=='0')
        {
           if(count($label_ids)>0) $sql_part="("; else $sql_part="";        
           $sql_part_array=array();
           foreach ($label_ids as $key => $value) 
           {
              $sql_part_array[]="FIND_IN_SET('".$value."',contact_group_id) !=0";
           }
           $sql_part.=implode(' OR ', $sql_part_array);
           if(count($label_ids)>0) $sql_part.=")";
           if($sql_part!="") $this->db->where($sql_part);

           foreach ($excluded_label_ids as $key => $value) 
           {
              $sq="NOT FIND_IN_SET('".$value."',contact_group_id) !=0";
              $this->db->where($sq);
           }
        }

        $where2 = array('where'=>$where_simple2);
        $bot_subscriber=$this->basic->get_data("messenger_bot_subscriber",$where2,'count(id) as subscriber_count');
        $subscriber_count = isset($bot_subscriber[0]['subscriber_count'])? $bot_subscriber[0]['subscriber_count'] : 0;
        $page_info['subscriber_count'] = $subscriber_count;

        $page_total_subscribers = $this->basic->get_data("messenger_bot_subscriber",array("where"=>array("page_table_id"=>$page_id,'is_bot_subscriber'=> '1','email != '=>'','is_email_unsubscriber'=>'0','unavailable'=>'0','user_id'=>$this->user_id,'permission'=>'1')));
        $page_info['page_total_subscribers'] = isset($page_total_subscribers) ? count($page_total_subscribers) : "0";

        echo json_encode(array('first_dropdown'=>$str,'second_dropdown'=>$str2,"pageinfo"=>$page_info));
    }

    public function contacts_total_emails()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        if(!$_POST) exit;
        $this->ajax_check();

        $user_id = $this->user_id;
        $contacts_sms_group = $this->input->post('contact_ids', true);

        if(isset($contacts_sms_group) && !empty($contacts_sms_group)){
            foreach ($contacts_sms_group as $key => $value) 
            {
                $where_simple = array('sms_email_contacts.user_id'=>$this->user_id,'sms_email_contacts.unsubscribed'=>'0');
                $this->db->where("FIND_IN_SET('$value',sms_email_contacts.contact_type_id) !=", 0);
                $where = array('where'=>$where_simple);
                $contact_details = $this->basic->get_data('sms_email_contacts', $where);
                foreach ($contact_details as $key2 => $value2) 
                {  
                    $contacts_id[] = $value2["id"];
                }
            }

            foreach ($contacts_sms_group as $key1 => $value1) 
            {
                $where_simple1 = array('sms_email_contacts.user_id'=>$this->user_id,'sms_email_contacts.email !='=>"",'sms_email_contacts.unsubscribed'=>'0');
                $this->db->where("FIND_IN_SET('$value1',sms_email_contacts.contact_type_id) !=", 0);
                $where1 = array('where'=>$where_simple1);    
                $contact_details2 = $this->basic->get_data('sms_email_contacts', $where1);

                foreach ($contact_details2 as $key3 => $value3) 
                {  
                    $wtihEmail[] = $value3["id"];
                }
            }
        }

        $total_contact_with_email = isset($wtihEmail) ? count($wtihEmail) : 0;
        $total_contact = isset($contacts_id) ? count($contacts_id):0;

        $result = array();
        $result['total_contact_with_email'] = isset($total_contact_with_email) ? $total_contact_with_email : "0";
        $result['total_contact'] = isset($total_contact) ? $total_contact : "0";

        echo json_encode($result);
    }

    public function create_email_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) redirect('home/login_page', 'location');

        $this->session->unset_userdata("attachment_file_path_name_scheduler");
        $this->session->unset_userdata("attachment_filename_scheduler");
        
        /**Get contact number and sms_email_contact_group***/
        $user_id = $this->user_id;
        $table_type = 'sms_email_contact_group';   
        $where_type['where'] = array('user_id'=>$user_id);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='type');

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>'1')),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');
        $data['page_info'] = $page_info; 
        $result = array();

        foreach ($info_type as  $value) 
        {
            $search_key = $value['id'];
            $search_type = $value['type'];

            $where_simple=array('sms_email_contacts.user_id'=>$this->user_id,'sms_email_contacts.unsubscribed'=>'0');
            $this->db->where("FIND_IN_SET('$search_key',sms_email_contacts.contact_type_id) !=", 0);
            $where=array('where'=>$where_simple);
            $this->db->select("count(sms_email_contacts.id) as number_count",false);    
            $contact_details=$this->basic->get_data('sms_email_contacts', $where, $select='', $join='', $limit='', $start='', $order_by=' sms_email_contacts.first_name', $group_by='', $num_rows=0);
        
            foreach ($contact_details as $key2 => $value2) 
            {
                if($value2['number_count']>0)
                $group_name[$search_key] = $search_type." (".$value2['number_count'].")";
            }
                
        }
        
        $email_api_access = $this->config->item('email_api_access');
        if($this->config->item('email_api_access') == '') $email_api_access = '0';

        if($email_api_access == '1' && $this->session->userdata("user_type") == 'Member')
        {                                                            
            /***get smtp  option***/
            $join = array('users'=>'email_smtp_config.user_id=users.id,left');
            $select = array('email_smtp_config.*','users.id AS usersID','users.user_type');
            $where_in = array('email_smtp_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','Member'));
            $where = array('where'=> array('email_smtp_config.status'=>'1'),'where_in'=>$where_in);
            $smtp_info=$this->basic->get_data('email_smtp_config', $where, $select, $join, $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            $smtp_option=array();
            foreach ($smtp_info as $info) {
                $id="smtp_".$info['id'];
                $smtp_option[$id]="SMTP: ".$info['email_address'];
            }
            
            /***get mandrill option***/
            $join = array('users'=>'email_mandrill_config.user_id=users.id,left');
            $select = array('email_mandrill_config.*','users.id AS usersID','users.user_type');
            $where_in = array('email_mandrill_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','Member'));
            $where = array('where'=> array('email_mandrill_config.status'=>'1'),'where_in'=>$where_in);
            $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select, $join, $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="mandrill_".$info['id'];
                $smtp_option[$id]="Mandrill: ".$info['email_address'];
            }

            /***get sendgrid option***/
            $join = array('users'=>'email_sendgrid_config.user_id=users.id,left');
            $select = array('email_sendgrid_config.*','users.id AS usersID','users.user_type');
            $where_in = array('email_sendgrid_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','Member'));
            $where = array('where'=> array('email_sendgrid_config.status'=>'1'),'where_in'=>$where_in);
            $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select, $join, $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="sendgrid_".$info['id'];
                $smtp_option[$id]="SendGrid: ".$info['email_address'];
            }

            /***get mailgun option***/
            $join = array('users'=>'email_mailgun_config.user_id=users.id,left');
            $select = array('email_mailgun_config.*','users.id AS usersID','users.user_type');
            $where_in = array('email_mailgun_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','Member'));
            $where = array('where'=> array('email_mailgun_config.status'=>'1'),'where_in'=>$where_in);
            $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select, $join, $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="mailgun_".$info['id'];
                $smtp_option[$id]="Mailgun: ".$info['email_address'];
            }

        } else
        {
            /***get smtp  option***/
            $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
            $smtp_info=$this->basic->get_data('email_smtp_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            $smtp_option=array();
            foreach ($smtp_info as $info) {
                $id="smtp_".$info['id'];
                $smtp_option[$id]="SMTP: ".$info['email_address'];
            }
            
            /***get mandrill option***/
            $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
            $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="mandrill_".$info['id'];
                $smtp_option[$id]="Mandrill: ".$info['email_address'];
            }

            /***get sendgrid option***/
            $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
            $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="sendgrid_".$info['id'];
                $smtp_option[$id]="SendGrid: ".$info['email_address'];
            }

            /***get mailgun option***/
            $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
            $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="mailgun_".$info['id'];
                $smtp_option[$id]="Mailgun: ".$info['email_address'];
            }
        }

        $data['email_option'] = $smtp_option;
        $data['groups_name'] = isset($group_name) ? $group_name:"";
        $data['email_templates'] = $this->get_email_templates_list();
        $data["time_zone"]   = $this->_time_zone_list();
        $data["time_zone_numeric"] = $this->_time_zone_list_numeric();
        $data['locale_list'] = $this->sdk_locale();
        $data['body']        = "sms_email_manager/email/email_campaign/create_email_campaign";
        $data['page_title']  = $this->lang->line('Create Email Campaign');
        $this->_viewcontroller($data);
    }

    public function create_email_campaign_action()
    {
        /* Check that if the email module exists or not */
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        $this->ajax_check(); /* verification checking through ajax_check() method */

        /* If the application is in demo mode, then set some restrictions */
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {                
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("This action is disabled in this demo account. Please signup as user and try this with your account")));
                exit();
            }
        }

        $report = array();
        $allowed_tags = "";

        /* Campaign form data */
        $campaign_name        = strip_tags(trim($this->input->post('campaign_name', true)));
        $email_subject        = strip_tags(trim($this->input->post('email_subject', true)));

        $email_template = $this->input->post('message');
        $email_template_id = (int) $this->input->post('email-template');
        $selected_tab = (int) $this->input->post('selected-tab');
        
        $email_message = '';

        if (! empty($email_template)) {
            $email_message = $this->input->post('message');
        } else if (! empty($email_template_id)) {
            $data = $this->basic->get_data('email_sms_template', ['where' => [ 'user_id' => $this->user_id, 'id' => $email_template_id ]], ['id', 'content'], '', 1);

            $content = isset($data[0]['content']) ? $data[0]['content'] : '';
            $content = json_decode($content, true);

            $email_message = isset($content['refinedMailTemplateHtml']) ? $content['refinedMailTemplateHtml'] : '';
        }

        $from_email           = strip_tags(trim($this->input->post('from_email', true)));

        /* Email API name */
        if(isset($from_email) && !empty($from_email)) { $from_email_separate  = explode('_', $from_email); }

        $api_id               = $from_email_separate[1]; /* email api table id */
        $configure_table_name = "email_smtp_config";
        $schedule_time        = $this->input->post('schedule_time');
        $time_zone            = strip_tags(trim($this->input->post('time_zone', true)));
        /* stored the attachment file path in attachment variable */
        $attachement          = $this->session->userdata("attachment_file_path_name_scheduler");
        /* stored the attachment file name in filename variable */
        $filename             = $this->session->userdata("attachment_filename_scheduler");

        /* informations from Messenger Subscriber section */
        $page_auto_id         = $this->input->post('page',true); /* facebook_rx_fb_page_info_table_id */
        $label_ids            = $this->input->post('label_ids',true);
        $excluded_label_ids   = $this->input->post('excluded_label_ids',true);
        $user_gender          = $this->input->post('user_gender',true);
        $user_time_zone       = $this->input->post('user_time_zone',true);
        $user_locale          = $this->input->post('user_locale',true);

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids=array();

        if($time_zone==''){ $time_zone = "Europe/Dublin"; /* if timezone field is empty, set this timezone */  }

        /* destroy the attachment related sessions */
        $this->session->unset_userdata("attachment_file_path_name_scheduler");
        $this->session->unset_userdata("attachment_filename_scheduler");

        /* Set the Email API Table Name */
        if (strtolower($from_email_separate[0])=='mandrill') {
            $configure_table_name = "email_mandrill_config";
        } elseif (strtolower($from_email_separate[0])=='sendgrid') {
            $configure_table_name = "email_sendgrid_config";
        } elseif (strtolower($from_email_separate[0])=='mailgun') {
            $configure_table_name = "email_mailgun_config";
        }

        $successfully_sent      = 0; /* initially total sent item is 0 */
        $added_at               = date("Y-m-d H:i:s"); /* set campaign created date as current date */
        $posting_status         = "0"; /* At initial state, campaign status is pending(0)  */

        /* by default open rate and click rate fields are 0 for campaign table */
        $number_of_unique_open  = "0";
        $number_of_total_open   = "0";

        $number_of_unique_click = "0";
        $number_of_total_click  = "0";
        $number_of_unique_clickers = "0";
        $total_unsubscribed = "0";

        /* For campaign send table fields */
        $is_open = "0";
        $number_of_time_open = "0";
        $number_of_clicked   = "0";

        // Messenger Subscriber Section Started
        if(isset($page_auto_id) && !empty($page_auto_id))
        {
            $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,"user_id"=>$this->user_id)));
            if(!isset($pageinfo[0]))
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("Something went wrong.")));
                exit();
            }
            $fb_page_id  = $pageinfo[0]['page_id'];
            $page_name  = $pageinfo[0]['page_name'];

            $excluded_label_ids_temp=$excluded_label_ids;
            $unsubscribe_labeldata=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("user_id"=>$this->user_id,"page_id"=>$page_auto_id,"unsubscribe"=>"1")));
            foreach ($unsubscribe_labeldata as $key => $value) 
            {
                array_push($excluded_label_ids_temp, $value["id"]);
            }

            if(count($label_ids)>0) $sql_part="("; else $sql_part="";        
            $sql_part_array=array();
            foreach ($label_ids as $key => $value) 
            {
               $sql_part_array[]="FIND_IN_SET('".$value."',contact_group_id) !=0";
            }        
            if(count($label_ids)>0) 
            {
                $sql_part.=implode(' OR ', $sql_part_array);
                $sql_part.=") AND ";
            }

            $sql_part2="";
            $sql_part_array2=array();
            foreach ($excluded_label_ids_temp as $key => $value) 
            {
              $sql_part_array2[]="NOT FIND_IN_SET('".$value."',contact_group_id) !=0";          
            }        
            if(count($excluded_label_ids_temp)>0) 
            {
                $sql_part2=implode(' AND ', $sql_part_array2);
                $sql_part2.=" AND ";
            }

            $sql_part3="";
            $sql_part_array3 = array();
            if($user_gender!='') $sql_part_array3[] = "gender = '{$user_gender}'";
            if($user_time_zone!='') $sql_part_array3[] = "timezone = '{$user_time_zone}'";
            if($user_locale!='') $sql_part_array3[] = "locale = '{$user_locale}'";

            if(count($sql_part_array3)>0) 
            {
                $sql_part3 = implode(' AND ', $sql_part_array3);
                $sql_part3 .=" AND ";
            }

            $sql="SELECT * FROM messenger_bot_subscriber WHERE ".$sql_part." ".$sql_part2." ".$sql_part3." user_id = ".$this->user_id." AND page_table_id = {$page_auto_id} AND is_bot_subscriber='1' AND email !='' AND is_email_unsubscriber='0' AND permission='1';";
            $lead_list = $this->basic->execute_query($sql);

            if(isset($lead_list) && !empty($lead_list)){
                foreach ($lead_list as $lead_key => $lead_value) {

                    $report[$lead_value['email']] = array(
                        'email_table_name'       => $configure_table_name,
                        'email_api_id'           => $api_id,
                        'contact_id'             => '0',
                        'subscriber_id'          => $lead_value['id'],
                        'contact_first_name'     => isset($lead_value['first_name']) ? $lead_value['first_name']:"",
                        'contact_last_name'      => isset($lead_value['last_name']) ? $lead_value['last_name']:"",
                        'contact_email'          => isset($lead_value['email']) ? $lead_value['email']:"",
                        'contact_phone_number'   => isset($lead_value['phone_number']) ? $lead_value['phone_number']:"",
                        'is_open'                => $is_open,  
                        'number_of_time_open'    => $number_of_time_open,
                        'number_of_clicked'      => $number_of_clicked,
                        'sent_time'              =>'pending',
                        'delivery_id'            =>'pending',
                    );
                }
            }
        }
        // Messenger Subscriber Section Ended
        // echo $this->db->last_query(); exit;

        /* External Subscriber Section started */
        $contacts_email_group = $this->input->post('contacts_id', true);

        /* if contact group is not an array, make it forcely array */
        if(!is_array($contacts_email_group)) {
            $contacts_email_group=array();  
        }

        /* Imploding Contact groups to get the sms_email_contact_groups table id */
        if(isset($contacts_email_group) && !empty($contacts_email_group)) { $contact_groupid = implode(",",$contacts_email_group); }

        $contacts_id = array();

        /* Formating contact information from sms_email_contacts table */
        if(isset($contacts_email_group) && !empty($contacts_email_group)) {
            foreach ($contacts_email_group as $key => $value) 
            {
                $where_simple = array('sms_email_contacts.user_id'=>$this->user_id,'sms_email_contacts.unsubscribed'=>'0');
                $this->db->where("FIND_IN_SET('$value',sms_email_contacts.contact_type_id) !=", 0);
                $where = array('where'=>$where_simple);    
                $contact_details=$this->basic->get_data('sms_email_contacts', $where);   
                foreach ($contact_details as $key2 => $value2) 
                {
                    if($value2['email'] == "") continue;

                    $report[$value2['email']] = array(
                        'email_table_name'       => $configure_table_name,
                        'email_api_id'           => $api_id,
                        'contact_id'             => $value2['id'],
                        'subscriber_id'          => '0',
                        'contact_first_name'     => isset($value2['first_name']) ? $value2['first_name']:"",
                        'contact_last_name'      => isset($value2['last_name']) ? $value2['last_name']:"",
                        'contact_email'          => isset($value2['email']) ? $value2['email']:"",
                        'contact_phone_number'   => isset($value2['phone_number']) ? $value2['phone_number']:"",
                        'is_open'                => $is_open,  
                        'number_of_time_open'    => $number_of_time_open,
                        'number_of_clicked'      => $number_of_clicked,
                        'sent_time'              =>'pending',
                        'delivery_id'            =>'pending',
                    );

                    $contacts_id[] = isset($value2["id"]) ? $value2["id"]: "";                
                }
            }
        }

        $contacts_id = array_filter($contacts_id);
        $contacts_id = array_unique($contacts_id);
        $contacts_id = implode(',', $contacts_id);

        $thread = count($report); /* total thread */

        /* if total thread is 0, show the error message */
        if($thread==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Campaign could not target any subscriber with email to reach message. Please try again with different targeting options.")));
            exit();
        }

        /* Make inserting data ready to insert into table */
        $inserted_data = array(
            "user_id"                => $this->user_id,
            "configure_email_table"  => $configure_table_name,
            "api_id"                 => $api_id,
            'page_id'                => isset($page_auto_id) ? $page_auto_id:"",
            'fb_page_id'             => isset($fb_page_id) ? $fb_page_id:"", 
            'page_name'              => isset($page_name) ? $page_name:"",
            "contact_ids"            => isset($contacts_id) ? $contacts_id:"",
            'contact_type_id'        => isset($contact_groupid) ? $contact_groupid:"",
            "campaign_name"          => $campaign_name,
            "email_subject"          => $email_subject,
            "email_message"          => $email_message,
            "email_attachment"       => isset($filename) ? $filename: "",
            "posting_status"         => $posting_status, 
            "schedule_time"          => $schedule_time,
            "time_zone"              => $time_zone,
            "total_thread"           => $thread,
            "successfully_sent"      => $successfully_sent,
            "created_at"             => $added_at,
            'user_gender'            => isset($user_gender) ? $user_gender:"",
            'user_time_zone'         => isset($user_time_zone) ? $user_time_zone:"",
            'user_locale'            => isset($user_locale) ? $user_locale:"",
            'number_of_unique_open'  => $number_of_unique_open,
            'number_of_total_open'   => $number_of_total_open,
            'number_of_unique_click' => $number_of_unique_click,
            'number_of_total_click'  => $number_of_total_click,
            'number_of_unique_clickers'  => $total_unsubscribed,
            'total_unsubscribed'     => $total_unsubscribed,
        );

        if (('drag-and-drop-tab' == $selected_tab) && $email_template_id) {
            $inserted_data['email_template_id'] = $email_template_id;
        }

        if(!empty($label_ids)) 
            $inserted_data['label_ids'] = implode(',', $label_ids); 
        else 
            $inserted_data['label_ids'] ="";

        if(!empty($excluded_label_ids)) 
            $inserted_data['excluded_label_ids'] = implode(',', $excluded_label_ids); 
        else 
            $inserted_data['excluded_label_ids'] = "";

        $fb_label_names = array();
        if(!empty($label_ids))
        {
            $fb_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids)));
            foreach ($fb_label_data as $key => $value) 
            {
               if($value['invisible']=='0')
                $fb_label_names[]=$value["group_name"];
            }  
        }

        $inserted_data['label_names'] = implode(',', $fb_label_names);

        $status = $this->_check_usage($module_id=263,$request=$thread);
        if($status=="3")  //monthly limit is exceeded, can not send another ,message this month
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Sorry, your monthly limit to send SMS is exceeded.")));
            exit();
        }

        /* Insert campaign data into email_sending_campaign table */
        if($this->basic->insert_data("email_sending_campaign",$inserted_data))
        {
            // getting inserted row id of email_sending_campaign table
            $campaign_id = $this->db->insert_id();

            $report_insert = array();
            foreach ($report as $key=>$value) 
            {
                $report_insert = array(
                    'user_id'                => $this->user_id,
                    'email_table_name'       => $value['email_table_name'],
                    'email_api_id'           => $value['email_api_id'],
                    'campaign_id'            => $campaign_id,
                    'contact_id'             => $value['contact_id'],
                    'subscriber_id'          => $value['subscriber_id'],
                    'contact_first_name'     => $value['contact_first_name'],
                    'contact_last_name'      => $value['contact_last_name'],
                    'contact_email'          => $key,
                    'contact_phone'          => $value['contact_phone_number'],
                    'is_open'                => $value['is_open'],  
                    'number_of_time_open'    => $value['number_of_time_open'],
                    'number_of_clicked'      => $value['number_of_clicked'],
                    'sent_time'              => '',
                    'delivery_id'            => 'pending',
                    'processed'              => '0',
                );
                
                $this->basic->insert_data("email_sending_campaign_send", $report_insert);
            }

            $this->_insert_usage_log($module_id=263,$request=$thread);

            echo json_encode(array("status"=>"1"));
        } else
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Something went wrong, please try once again.')));
        }
    }

    public function edit_email_campaign($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) redirect('home/login_page', 'location');

        if($id==0) exit();

        $data['body'] = "sms_email_manager/email/email_campaign/edit_email_campaigns";
        $campaign_data = $this->basic->get_data("email_sending_campaign",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $data['selected_contact_gorups'] = explode(",",$campaign_data[0]['contact_type_id']);
    
        $this->session->unset_userdata("attachment_file_path_name_scheduler");
        $this->session->unset_userdata("attachment_filename_scheduler");

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>'1')),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');
        $data['page_info'] = $page_info;


        // only pending campaigns are editable
        if(!isset($campaign_data[0]["posting_status"]) || $campaign_data[0]["posting_status"] != '0' ) exit();

        // only scheduled campaigns can be editted
        if(!isset($campaign_data[0]["time_zone"]) || $campaign_data[0]["time_zone"]=='' ) exit();
        
        /**Get contact number and contact_type***/
        $user_id = $this->user_id;
        $table_type = 'sms_email_contact_group';   
        $where_type['where'] = array('user_id'=>$user_id);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='type');  
        $result = array();

        foreach ($info_type as  $value) {

            $search_key = $value['id'];
            $search_type = $value['type'];

            $where_simple=array('sms_email_contacts.user_id'=>$this->user_id,'sms_email_contacts.unsubscribed'=>'0');
            $this->db->where("FIND_IN_SET('$search_key',sms_email_contacts.contact_type_id) !=", 0);
            $where=array('where'=>$where_simple);
            $this->db->select("count(sms_email_contacts.id) as number_count",false);    
            $contact_details=$this->basic->get_data('sms_email_contacts', $where, $select='', $join='', $limit='', $start='', $order_by='sms_email_contacts.first_name', $group_by='', $num_rows=0);
        
            foreach ($contact_details as $key2 => $value2) 
            {
                if($value2['number_count']>0)
                $group_name[$search_key] = $search_type." (".$value2['number_count'].")";
            }
                
        }

        $email_api_access = $this->config->item('email_api_access');
        if($this->config->item('email_api_access') == '') $email_api_access = '0';                                   

        /***get smtp option***/
        if($email_api_access == '1' && $this->session->userdata("user_type") == 'Member') {                                                            
            /***get smtp  option***/
            $join = array('users'=>'email_smtp_config.user_id=users.id,left');
            $select = array('email_smtp_config.*','users.id AS usersID','users.user_type');
            $where_in = array('email_smtp_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','User'));
            $where = array('where'=> array('status'=>'1'),'where_in'=>$where_in);
            $smtp_info=$this->basic->get_data('email_smtp_config', $where, $select, $join, $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            $smtp_option=array();
            foreach ($smtp_info as $info) {
                $id="smtp_".$info['id'];
                $smtp_option[$id]="SMTP: ".$info['email_address'];
            }
            
            /***get mandrill option***/
            $join = array('users'=>'email_mandrill_config.user_id=users.id,left');
            $select = array('email_mandrill_config.*','users.id AS usersID','users.user_type');
            $where_in = array('email_mandrill_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','User'));
            $where = array('where'=> array('status'=>'1'),'where_in'=>$where_in);
            $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select, $join, $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="mandrill_".$info['id'];
                $smtp_option[$id]="Mandrill: ".$info['email_address'];
            }

            /***get sendgrid option***/
            $join = array('users'=>'email_sendgrid_config.user_id=users.id,left');
            $select = array('email_sendgrid_config.*','users.id AS usersID','users.user_type');
            $where_in = array('email_sendgrid_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','User'));
            $where = array('where'=> array('status'=>'1'),'where_in'=>$where_in);
            $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select, $join, $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="sendgrid_".$info['id'];
                $smtp_option[$id]="SendGrid: ".$info['email_address'];
            }

            /***get mailgun option***/
            $join = array('users'=>'email_mailgun_config.user_id=users.id,left');
            $select = array('email_mailgun_config.*','users.id AS usersID','users.user_type');
            $where_in = array('email_mailgun_config.user_id'=>array('1',$this->user_id),'users.user_type'=>array('Admin','User'));
            $where = array('where'=> array('status'=>'1'),'where_in'=>$where_in);
            $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select, $join, $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="mailgun_".$info['id'];
                $smtp_option[$id]="Mailgun: ".$info['email_address'];
            }
        } else {

            /***get smtp  option***/
            $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
            $smtp_info=$this->basic->get_data('email_smtp_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            $smtp_option=array();
            foreach ($smtp_info as $info) {
                $id="smtp_".$info['id'];
                $smtp_option[$id]="SMTP: ".$info['email_address'];
            }
            
            /***get mandrill option***/
            $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
            $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="mandrill_".$info['id'];
                $smtp_option[$id]="Mandrill: ".$info['email_address'];
            }

            /***get sendgrid option***/
            $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
            $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="sendgrid_".$info['id'];
                $smtp_option[$id]="SendGrid: ".$info['email_address'];
            }

            /***get mailgun option***/
            $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
            $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
            
            foreach ($smtp_info as $info) {
                $id="mailgun_".$info['id'];
                $smtp_option[$id]="Mailgun: ".$info['email_address'];
            }
        }

        $api_arr = array();

        if($campaign_data[0]['configure_email_table'] == 'email_smtp_config') {
            $data['email_name'] = "smtp_".$campaign_data[0]['api_id'];
        }

        if($campaign_data[0]['configure_email_table'] == 'email_mandrill_config') {
            $data['email_name'] = "mandrill_".$campaign_data[0]['api_id'];
        }

        if($campaign_data[0]['configure_email_table'] == 'email_sendgrid_config') {
            $data['email_name'] = "sendgrid_".$campaign_data[0]['api_id'];
        }

        if($campaign_data[0]['configure_email_table'] == 'email_mailgun_config') {
            $data['email_name'] = "mailgun_".$campaign_data[0]['api_id'];
        }            
  
        $template_id = null;
        if (isset($campaign_data[0]['email_template_id']) && null != $campaign_data[0]['email_template_id']) {
            $template_id = (int) $campaign_data[0]['email_template_id'];

        }

        $data["campaign_data"] = $campaign_data;
        $data['email_option'] = $smtp_option;
        $data['template_id'] = $template_id;
        $data['email_templates'] = $this->get_email_templates_list();
        $data['groups_name'] = isset($group_name) ? $group_name:"";
        $data["time_zone"]   = $this->_time_zone_list();
        $data["time_zone_numeric"] = $this->_time_zone_list_numeric();
        $data['locale_list'] = $this->sdk_locale();
        $data['page_title']  = $this->lang->line('Edit Email Campaign');
        $this->_viewcontroller($data);  
    }

    public function edit_email_campaign_action()
    {
        /* Check that the email broadcasting module exists or not */
        if($this->session->userdata('user_type') != 'Admin' && !in_array(263,$this->module_access)) exit;

        /* security checking */
        $this->ajax_check();

        $report = array();

        $campaign_id          = $this->input->post("campaign_id");
        $previous_thread      = $this->input->post("previous_thread");
        $campaign_name        = strip_tags(trim($this->input->post('campaign_name', true)));
        $email_subject        = strip_tags(trim($this->input->post('email_subject', true)));
        
        // $email_message        = $this->input->post('message');

        $email_template = $this->input->post('message');
        $email_template_id = (int) $this->input->post('email-template');
        $selected_tab = (string) $this->input->post('selected-tab', true);
        
        $email_message = '';

        if (('rich-text-editor-tab' == $selected_tab) && $email_template) {
            $email_message = $this->input->post('message');
        } else if (('drag-and-drop-tab' == $selected_tab) && $email_template_id) {
            $data = $this->basic->get_data('email_sms_template', ['where' => [ 'user_id' => $this->user_id, 'id' => $email_template_id ]], ['id', 'content'], '', 1);

            $content = isset($data[0]['content']) ? $data[0]['content'] : '';
            $content = json_decode($content, true);

            $email_message = isset($content['refinedMailTemplateHtml']) ? $content['refinedMailTemplateHtml'] : '';
        }


        // $email_message        = preg_replace("@<(script|script[^>]+)>(.*)(</script>)?@mui", "[removed]>disallowed characters[removed]", $email_message);
        $from_email           = strip_tags(trim($this->input->post('from_email', true)));
        $from_email_separate  = explode('_', $from_email);
        $api_id               = $from_email_separate[1];
        $configure_table_name = "email_smtp_config";
        $schedule_time        = $this->input->post('schedule_time');
        $time_zone            = strip_tags(trim($this->input->post('time_zone', true)));
        $attachement          = $this->session->userdata("attachment_file_path_name_scheduler");
        $filename             = $this->session->userdata("attachment_filename_scheduler");

        /* informations from Messenger Subscriber section */
        $page_auto_id         = $this->input->post('page',true); /* facebook_rx_fb_page_info_table_id */
        $label_ids            = $this->input->post('label_ids',true);
        $excluded_label_ids   = $this->input->post('excluded_label_ids',true);
        $user_gender          = $this->input->post('user_gender',true);
        $user_time_zone       = $this->input->post('user_time_zone',true);
        $user_locale          = $this->input->post('user_locale',true);

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids=array();

        /* Getting the existing attachment if available */
        $existed_attachment   = $this->basic->get_data("email_sending_campaign", array('where'=>array('id'=>$campaign_id,'user_id'=>$this->user_id)),array('email_attachment'));

        // remove old attachment from upload/attachment directory
        if((isset($attachement) && $attachement != '') || (isset($filename) && $filename != ''))
        {
            if(isset($existed_attachment[0]['email_attachment']) && !empty($existed_attachment[0]['email_attachment'])) 
            {
                $file = FCPATH."upload/attachment/".$existed_attachment[0]['email_attachment'];
                if(file_exists($file))
                {
                    unlink($file);
                }
            } 
        }

        $this->session->unset_userdata("attachment_file_path_name_scheduler");
        $this->session->unset_userdata("attachment_filename_scheduler");


        if (strtolower($from_email_separate[0])=='mandrill') {
            $configure_table_name = "email_mandrill_config";
        } elseif (strtolower($from_email_separate[0])=='sendgrid') {
            $configure_table_name = "email_sendgrid_config";
        } elseif (strtolower($from_email_separate[0])=='mailgun') {
            $configure_table_name = "email_mailgun_config";
        }

        $successfully_sent = 0;
        $added_at          = date("Y-m-d H:i:s");
        $posting_status    = "0";

        /* by default open rate and click rate fields are 0 for campaign table */
        $number_of_unique_open  = "0";
        $number_of_total_open   = "0";

        $number_of_unique_click = "0";
        $number_of_total_click  = "0";

        $number_of_unique_clickers = "0";
        $total_unsubscribed = "0";

        /* For campaign send table fields */
        $is_open = "0";
        $number_of_time_open = "0";
        $number_of_clicked   = "0";


        // Messenger Subscriber Section Started
        if(isset($page_auto_id) && !empty($page_auto_id))
        {
            $pageinfo = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,"user_id"=>$this->user_id)));
            if(!isset($pageinfo[0]))
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("Something went wrong.")));
                exit();
            }
            $fb_page_id  = $pageinfo[0]['page_id'];
            $page_name  = $pageinfo[0]['page_name'];

            $excluded_label_ids_temp=$excluded_label_ids;
            $unsubscribe_labeldata=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("user_id"=>$this->user_id,"page_id"=>$page_auto_id,"unsubscribe"=>"1")));
            foreach ($unsubscribe_labeldata as $key => $value) 
            {
                array_push($excluded_label_ids_temp, $value["id"]);
            }

            if(count($label_ids)>0) $sql_part="("; else $sql_part="";        
            $sql_part_array=array();
            foreach ($label_ids as $key => $value) 
            {
               $sql_part_array[]="FIND_IN_SET('".$value."',contact_group_id) !=0";
            }        
            if(count($label_ids)>0) 
            {
                $sql_part.=implode(' OR ', $sql_part_array);
                $sql_part.=") AND ";
            }

            $sql_part2="";
            $sql_part_array2=array();
            foreach ($excluded_label_ids_temp as $key => $value) 
            {
              $sql_part_array2[]="NOT FIND_IN_SET('".$value."',contact_group_id) !=0";          
            }        
            if(count($excluded_label_ids_temp)>0) 
            {
                $sql_part2=implode(' AND ', $sql_part_array2);
                $sql_part2.=" AND ";
            }

            $sql_part3="";
            $sql_part_array3 = array();
            if($user_gender!='') $sql_part_array3[] = "gender = '{$user_gender}'";
            if($user_time_zone!='') $sql_part_array3[] = "timezone = '{$user_time_zone}'";
            if($user_locale!='') $sql_part_array3[] = "locale = '{$user_locale}'";

            if(count($sql_part_array3)>0) 
            {
                $sql_part3 = implode(' AND ', $sql_part_array3);
                $sql_part3 .=" AND ";
            }

            $sql="SELECT * FROM messenger_bot_subscriber WHERE ".$sql_part." ".$sql_part2." ".$sql_part3." user_id = ".$this->user_id." AND page_table_id = {$page_auto_id} AND is_bot_subscriber='1' AND email !='' AND is_email_unsubscriber='0' AND permission='1';";
            $lead_list = $this->basic->execute_query($sql);

            if(isset($lead_list) && !empty($lead_list)){
                foreach ($lead_list as $lead_key => $lead_value) {

                    $report[$lead_value['email']] = array(
                        'email_table_name'       => $configure_table_name,
                        'email_api_id'           => $api_id,
                        'contact_id'             => '0',
                        'subscriber_id'          => $lead_value['id'],
                        'contact_first_name'     => isset($lead_value['first_name']) ? $lead_value['first_name']:"",
                        'contact_last_name'      => isset($lead_value['last_name']) ? $lead_value['last_name']:"",
                        'contact_email'          => isset($lead_value['email']) ? $lead_value['email']:"",
                        'contact_phone_number'   => isset($lead_value['phone_number']) ? $lead_value['phone_number']:"",
                        'is_open'                => $is_open,  
                        'number_of_time_open'    => $number_of_time_open,
                        'number_of_clicked'      => $number_of_clicked,
                        'sent_time'              =>'pending',
                        'delivery_id'            =>'pending',
                    );
                }
            }
        }
        // Messenger Subscriber Section Ended

        /* External broadcasting subscribers section started from here (Contact Group section) */
        $contacts_email_group = $this->input->post('contacts_id', true);

        /* If contact groups are not in array, make them an array */
        if(!is_array($contacts_email_group)) {
            $contacts_email_group=array();  
        }

        if(isset($contacts_email_group) && !empty($contacts_email_group)) {
            $contact_groupid    = implode(",",$contacts_email_group); 
        }

        $contacts_id = array();

        if(isset($contacts_email_group) && !empty($contacts_email_group)) {
            foreach ($contacts_email_group as $key => $value) {

                $where_simple = array('sms_email_contacts.user_id'=>$this->user_id,'sms_email_contacts.unsubscribed'=>'0');
                $this->db->where("FIND_IN_SET('$value',sms_email_contacts.contact_type_id) !=", 0);
                $where = array('where'=>$where_simple);    
                $contact_details=$this->basic->get_data('sms_email_contacts', $where);       
                foreach ($contact_details as $key2 => $value2) {

                    $report[$value2['email']] = array(
                        'email_table_name'    => $configure_table_name,
                        'email_api_id'        => $api_id,
                        'contact_id'          => $value2['id'],
                        'subscriber_id'       => '0',
                        'contact_first_name'  => isset($value2['first_name']) ? $value2['first_name']:"",
                        'contact_last_name'   => isset($value2['last_name']) ? $value2['last_name']:"",
                        'contact_email'       => isset($value2['email']) ? $value2['email']:"",
                        'contact_phone_number'=> isset($value2['phone_number']) ? $value2['phone_number']:"",
                        'is_open'             => $is_open,  
                        'number_of_time_open' => $number_of_time_open,
                        'number_of_clicked'   => $number_of_clicked,
                        'sent_time'           =>'pending',
                        'delivery_id'         =>'pending',
                    );

                    $contacts_id[] = $value2["id"];                
                }
            }
        }

        $contacts_id = array_filter($contacts_id);
        $contacts_id = array_unique($contacts_id);
        $contacts_id = implode(',', $contacts_id);

        if($filename == "") {
            $filename = $existed_attachment[0]['email_attachment'];
        }

        $thread = count($report);

        /* if total thread is 0, show the error message */
        if($thread==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Campaign could not target any subscriber with email to reach message. Please try again with different targeting options.")));
            exit();
        }

        $updated_data = array(
            "user_id"               => $this->user_id,
            "configure_email_table" => $configure_table_name,
            "api_id"                => $api_id,
            'page_id'               => isset($page_auto_id) ? $page_auto_id:"",
            'fb_page_id'            => isset($fb_page_id) ? $fb_page_id:"", 
            'page_name'             => isset($page_name) ? $page_name:"",
            "contact_ids"           => isset($contacts_id) ? $contacts_id:"",
            'contact_type_id'       => isset($contact_groupid) ? $contact_groupid:"",
            "campaign_name"         => $campaign_name,
            "email_subject"         => $email_subject,
            "email_attachment"      => $filename,
            "posting_status"        => $posting_status, 
            "schedule_time"         => $schedule_time,
            "time_zone"             => $time_zone,
            "total_thread"          => $thread,
            "successfully_sent"     => $successfully_sent,
            "created_at"            => $added_at,
            'user_gender'           => isset($user_gender) ? $user_gender:"",
            'user_time_zone'        => isset($user_time_zone) ? $user_time_zone:"",
            'user_locale'           => isset($user_locale) ? $user_locale:"",
            'number_of_unique_open' => $number_of_unique_open,
            'number_of_total_open'  => $number_of_total_open,
            'number_of_unique_click'=> $number_of_unique_click, 
            'number_of_total_click' => $number_of_total_click,
            'number_of_unique_clickers'  => $total_unsubscribed,
            'total_unsubscribed'     => $total_unsubscribed,

        );

        if ('rich-text-editor-tab' == $selected_tab) {
            $updated_data['email_message'] = $email_message;
            $updated_data['email_template_id'] = null;      
        } else if ('drag-and-drop-tab' == $selected_tab) {
            $updated_data['email_message'] = $email_message;
            $updated_data['email_template_id'] = $email_template_id;
        }

        if(!empty($label_ids)) 
            $updated_data['label_ids'] = implode(',', $label_ids); 
        else 
            $updated_data['label_ids'] ="";

        if(!empty($excluded_label_ids)) 
            $updated_data['excluded_label_ids'] = implode(',', $excluded_label_ids); 
        else 
            $updated_data['excluded_label_ids'] = "";

        $fb_label_names = array();
        if(!empty($label_ids))
        {
            $fb_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where_in"=>array("id"=>$label_ids)));
            foreach ($fb_label_data as $key => $value) 
            {
               if($value['invisible']=='0')
                $fb_label_names[]=$value["group_name"];
            }  
        }

        $updated_data['label_names'] = implode(',', $fb_label_names);

        $current_total_thread = $previous_thread - $thread;
        $current_total_thread_abs = abs($current_total_thread);
        if($current_total_thread < 0)
        {
            $status = $this->_check_usage($module_id=263,$request=$current_total_thread_abs);
             if($status == "3")  //monthly limit is exceeded, can not send another ,message this month
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("Sorry, your monthly limit to send promo message is exceeded.")));
                exit();
            }
        }


        if($this->basic->update_data("email_sending_campaign",array("id" => $campaign_id,"user_id"=>$this->user_id),$updated_data))
        {
            /* Delete the rows of updated campaign from sms_sending_campaign_send table */
            $this->basic->delete_data("email_sending_campaign_send", array("campaign_id" =>$campaign_id));

            $report_insert = array();
            foreach ($report as $key=>$value) {
                $report_insert = array(
                    'user_id'              => $this->user_id,
                    'email_table_name'     => $value['email_table_name'],
                    'email_api_id'         => $value['email_api_id'],
                    'campaign_id'          => $campaign_id,
                    'contact_id'           => $value['contact_id'],
                    'subscriber_id'        => $value['subscriber_id'],
                    'contact_first_name'   => $value['contact_first_name'],
                    'contact_last_name'    => $value['contact_last_name'],
                    'contact_email'        => $key,
                    'contact_phone'        => $value['contact_phone_number'],
                    'is_open'              => $value['is_open'],
                    'number_of_time_open'  => $value['number_of_time_open'],
                    'number_of_clicked'    => $value['number_of_clicked'],
                    'sent_time'            => '',
                    'delivery_id'          => 'pending',
                    'processed'            => '0'
                );
                
                $this->basic->insert_data("email_sending_campaign_send", $report_insert);
            }

            if($current_total_thread < 0){
                $this->_insert_usage_log($module_id=263,$request=$current_total_thread_abs);
            } else { 
                $this->_delete_usage_log($module_id=263,$request=$current_total_thread_abs);
            }

            echo json_encode(array("status"=>"1"));

        } else
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Something went wrong, please try once again.')));
        }
    }

    public function checking_open_rate($campaign_table_id=0,$temp_table_id=0)
    {
        if(!$this->basic->is_exist("email_sending_campaign",$where=array('id'=>$campaign_table_id))) exit;

        $email_sending_campaign_table_id = $campaign_table_id;
        $email_sending_campaign_send_table_id = $temp_table_id;

        if($email_sending_campaign_table_id == "0" || $email_sending_campaign_send_table_id == "0") exit;
        if($email_sending_campaign_table_id == "" || $email_sending_campaign_send_table_id == "") exit;

        $userData = $this->basic->get_data("email_sending_campaign_send",array("where"=>array("id"=>$email_sending_campaign_send_table_id, "campaign_id"=>$email_sending_campaign_table_id)), array("is_open"));

        $last_opened_date = date("Y-m-d H:i:s");

        /* increasing open rate data in campaign table */
        if(isset($userData) && !empty($userData)) {

            $this->db->trans_begin();

            if($userData[0]['is_open'] == "0") {

                $increment_total_unique_openers = "update email_sending_campaign set number_of_unique_open=number_of_unique_open+1, last_opened_at='{$last_opened_date}' where id='{$email_sending_campaign_table_id}'";

                if($increment_total_unique_openers != "") {
                    $this->db->query($increment_total_unique_openers);
                }

                $this->basic->update_data("email_sending_campaign_send",array("id"=>$email_sending_campaign_send_table_id,"campaign_id"=>$email_sending_campaign_table_id),array("is_open"=>"1","email_opened_at"=>$last_opened_date));
            }

            $increment_total_opened = "update email_sending_campaign set number_of_total_open=number_of_total_open+1, last_opened_at='{$last_opened_date}' where id='{$email_sending_campaign_table_id}'";

            if($increment_total_opened != "") {
                $this->db->query($increment_total_opened);
            }

            /* increasing open rate dara in temporary campaign table */ 
            $number_of_opened_increment_sql = "update email_sending_campaign_send set number_of_time_open=number_of_time_open+1, email_opened_at='{$last_opened_date}' where id='{$email_sending_campaign_send_table_id}' AND campaign_id='{$email_sending_campaign_table_id}'";

            if($number_of_opened_increment_sql != '') {
                $this->db->query($number_of_opened_increment_sql);
            }


            if ($this->db->trans_status() === FALSE) {

                $this->db->trans_rollback();
            }
            else {

                $this->db->trans_commit();
            }
        }

    }

    public function checking_click_rate($campaign_table_id=0,$temp_table_id=0,$link_id=0)
    {
        $email_sending_campaign_table_id = $campaign_table_id;
        $email_sending_campaign_send_table_id = $temp_table_id;
        $email_sending_campaign_link_id = $link_id;

        // number_of_unique_clickers = How many unique users click a link of the campaign. Suppose a reciever clicks on 3 links of the campaign then number_of_unique_clickers will be 1;

        //  number_of_total_click = How much users click links of the campaign. Suppose a reciever clicks on 3 links of the campaign then   number_of_total_click will be 3;

        if($email_sending_campaign_table_id == "0" || $email_sending_campaign_send_table_id == "0") exit;
        if($email_sending_campaign_table_id == "" || $email_sending_campaign_send_table_id == "" || $link_id == "") exit;

        $campaign_backup_links = $this->basic->get_data("email_clickrate_links_backup", array("where"=>array("campaign_id"=> $email_sending_campaign_table_id)));

        $userData = $this->basic->get_data("email_sending_campaign_send",array("where"=>array("id"=>$email_sending_campaign_send_table_id, "campaign_id"=>$email_sending_campaign_table_id)), array("is_clicked"));

        $last_clicked_data = date("Y-m-d H:i:s");

        if(isset($campaign_backup_links[0]['links']) && !empty($campaign_backup_links[0]['links'])) {

            $decoding_links = json_decode($campaign_backup_links[0]['links']);

            foreach ($decoding_links as $key => $value) {

                if($key==$email_sending_campaign_link_id) {

                    if(isset($userData) && !empty($userData)) {

                        $this->db->trans_begin();

                        if($userData[0]['is_clicked'] == "0") {

                            $increment_total_unique_clickers = "update email_sending_campaign set number_of_unique_clickers=number_of_unique_clickers+1, last_clicked_at='{$last_clicked_data}' where id='{$email_sending_campaign_table_id}'";

                            if($increment_total_unique_clickers != "") {
                                $this->db->query($increment_total_unique_clickers);
                            }

                            $this->basic->update_data("email_sending_campaign_send",array("id"=>$email_sending_campaign_send_table_id,"campaign_id"=>$email_sending_campaign_table_id),array("is_clicked"=>"1","last_clicked_at"=>$last_clicked_data));
                        }


                        /* increasing open rate dara in temporary campaign table */ 
                        $number_of_total_clicked_increment_campaign_table = "update email_sending_campaign set number_of_total_click=  number_of_total_click+1, last_clicked_at='{$last_clicked_data}' where id='{$email_sending_campaign_table_id}'";

                        if($number_of_total_clicked_increment_campaign_table != "") {
                            $this->db->query($number_of_total_clicked_increment_campaign_table);
                        }

                        $number_of_total_clicked_increment_send_table = "update email_sending_campaign_send set number_of_clicked=number_of_clicked+1, last_clicked_at='{$last_clicked_data}' where id='{$email_sending_campaign_send_table_id}' AND campaign_id='{$email_sending_campaign_table_id}'";

                        if($number_of_total_clicked_increment_send_table != "") {
                            $this->db->query($number_of_total_clicked_increment_send_table);
                        }

                        if ($this->db->trans_status() === FALSE) {

                            $this->db->trans_rollback();
                        }
                        else {

                            $this->db->trans_commit();
                        }
                    }

                    redirect($value, "refresh");

                }
            }
        }
    }

    function _api_gateways()
    {
    	$gateway_lists = array(
			'plivo'               => 'Plivo [Required : Auth ID, Auth Token, Sender]',
			'twilio'              => 'Twilio [Required : Account Sid, Auth Token, From]',
			'nexmo'               => 'Nexmo [Required : API Key, API Secret, Sender]',
			'planet'              => 'Planet [Required : Username, Password, Sender.]',
			'semysms.net'         => 'semysms.net [Required : Auth Token, API ID [Use device ID in API ID]]',
			'clickatell-platform' => 'Clickatell-platform [Required : API ID]',
			'clickatell'          => 'Clickatell [Required : API Username, API Password, API ID]',
			'msg91.com'           => 'msg91.com [Required : Auth Key, Sender]',
			'africastalking.com'  => 'africastalking.com [Required : API Key, Sender ID/From [Use username in Sender ID/From]]',
			'routesms.com'        => 'routesms.com [Required : Username, Password, Hostname, Sender ID/From]',
            // not tested
			// 'textlocal.in'        => 'textlocal.in [Required : API Key, Sender]',
			// 'sms4connect.com'     => 'sms4connect.com [Required : Account ID, Password, Mask]',
			// 'telnor.com'          => 'telnor.com [Required : MSISDN, Password, From]',
			// 'mvaayoo.com'         => 'mvaayoo.com [Required : Admin, Password, Sender ID]',
			// 'trio-mobile.com'     => 'trio-mobile.com [Required : API Key, Sender ID]',
			// 'sms40.com'           => 'sms40.com [Required : Username, Password, Sender ID/From]',
			// 'infobip.com'         => 'infobip.com [Required : Username, Password, Sender ID/From]',
			// 'smsgatewayme'        => 'smsgatewayme [Required : API Token, API ID [Use device ID in API ID]]',
    	);

    	return $gateway_lists;
    }


    private function create_table_string_for_analyzing($form_array, $query_pieces = array(), $table_id = 0)
    {
        /* if for edit call then get necessary data and do the initialization */
        if ($table_id != 0) {
            
            $api_info = $this->basic->get_data('sms_api_config', array('where' => array('id' => $table_id, 'user_id' => $this->user_id)));

            if (count($api_info) == 0) {
                return "";
            }

            $input_http_url = $api_info[0]['input_http_url'];
            $finalized_input_url = $api_info[0]['final_http_url'];

            /* input url parsing */
            $parse_result = parse_url($input_http_url);
            $query = $parse_result['query'];
            $input_query_pieces = explode('&', $query);

            /* finalized url parsing */
            $parse_result = parse_url($finalized_input_url);
            $query = $parse_result['query'] . '#' . $parse_result['fragment'];
            $query_pieces = explode('&', $query);
        } else {

            $final_form = preg_replace("/(\r?\n){2,}/", "", form_dropdown('', $form_array, 'fixed', 'class="form-control select_option"'));
        }


        if (count($query_pieces) > 0 && empty($query_pieces[0])) {
            $query_pieces = array();
        }


        $i = 0;
        $final_query_pieces = array();

        /* start building the table string */

        $final_string = '<div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-cog"></i> HTTP Query Parameter</h4>
                            </div><div class="card-body p-0">
                            <table class="table table-bordered m-0">
                                <thead>
                                    <tr>
                                      <th scope="col">'.$this->lang->line("Key").'</th>
                                      <th scope="col">'.$this->lang->line("Type").'</th>
                                      <th scope="col">'.$this->lang->line("Value").'</th>
                                    </tr>
                                </thead>
                                <tbody>';



        foreach ($query_pieces as $single_query) {
            
            $pieces = explode('=', $single_query);
            $final_query_pieces[$pieces[0]] = array();
            $input_disabled = '';

            /* $table_id = 0 means for creating call and $table_id != 0 means for edit call */
            if ($table_id != 0) {

                $initial_value = explode('=', $input_query_pieces[$i]);

                $final_query_pieces[$pieces[0]]['has_changed'] = true;
                $final_query_pieces[$pieces[0]]['initial_value'] = $initial_value[1];
                $final_query_pieces[$pieces[0]]['changed_value'] = $pieces[1];

                $form_select = 'fixed';
                if ($pieces[1] == '#DESTINATION_NUMBER#') {

                    $form_select = 'destination_number';
                    $input_disabled = 'disabled';
                } elseif ($pieces[1] == '#MESSAGE_CONTENT#') {

                    $form_select = 'message_content';
                    $input_disabled = 'disabled';
                }


                $final_form = preg_replace("/(\r?\n){2,}/", "", form_dropdown('', $form_array, $form_select, 'class="form-control select_option"'));

                $i++;
            } else {

                $final_query_pieces[$pieces[0]]['has_changed'] = false;
                $final_query_pieces[$pieces[0]]['initial_value'] = $pieces[1];
                $final_query_pieces[$pieces[0]]['changed_value'] = '';
            }
            
            $final_string .= '<tr>
                                 <td>'.$pieces[0].'</td>';
            $final_string .=    '<td>'.$final_form.'</td>';
            $final_string .=    '<td><input type="text" class="form-control parsed_single_value" value="'.$pieces[1].'" '. $input_disabled .'></td>
                              </tr>';

        }
        $final_string .= '</tbody>
                    </table></div></div>';

        return array('table_string' => $final_string, 'final_query_pieces' => $final_query_pieces);
    }

    public function analize_custom_api_url()
    {
        $this->ajax_check();

        $custom_url = $this->input->post('custom_url', true);

        /* if is called for edit */
        $action_type = $this->input->post('action_type', true);
        $table_id = $this->input->post('table_id', true);
        $is_first_time_edit_request = $this->input->post('is_first_time_edit_request', true);


        /* parse the url */
        $parse_result = parse_url($custom_url);
        $scheme = isset($parse_result['scheme']) ? $parse_result['scheme'] : '';
        $host = isset($parse_result['host']) ? $parse_result['host'] : '';
        $path = isset($parse_result['path']) ? $parse_result['path'] : '';
        $query = isset($parse_result['query']) ? $parse_result['query'] : '';
        $query_pieces = explode('&', $query);
        $final_query_pieces = array();
        // echo "<pre>";print_r($scheme);exit;


        /* check valid url */
        if ($scheme == '') {
            
            echo json_encode(array('base_url' => '','message' => 'error','query_pieces' => ''));
            exit;
        } else if (!($scheme == 'http' || $scheme == 'https')) {
            
            echo json_encode(array('base_url' => '','message' => 'error','query_pieces' => ''));
            exit;
        }

        
        /* replacement constant */
        $DESTINATION_NUMBER = '#DESTINATION_NUMBER#';
        $SENDER_ID = '#SENDER_ID#';
        $MESSAGE_CONTENT = '#MESSAGE_CONTENT#';


        $form_array = array(
            'fixed' => $this->lang->line("Fixed"),
            'destination_number' => $this->lang->line("Destination Number"),
            'message_content' => $this->lang->line("Message Content")
        );


        /* build the output string */
        $final_string = '';

        if ($action_type != 'edit') {

            $final_form = preg_replace("/(\r?\n){2,}/", "", form_dropdown('', $form_array, 'fixed', 'class="form-control select_option"'));
        }

        if (!($action_type == 'edit' && $is_first_time_edit_request == 'yes')) {
            
            $api_response = $this->run_curl($custom_url);

            // Test response
            $final_string .= '<div class="card">
                  <div class="card-header">
                    <h4><i class="fas fa-code"></i> '.$this->lang->line("Test response : ").'</h4>
                  </div>
                  <div class="card-body">
                    <label style="word-break: break-all;" class="p-0 m-0">'.$api_response.'</label>
                  </div>
                </div>';
        }

        // Base URL
        $final_string .= '<div class="card" style="padding:20px;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.09);"><label style="word-break: break-all;margin-bottom: 0;"><span style="font-size: 16px;color: var(--blue);padding-right: 10px;margin-bottom: 0;font-weight: bold;"><i class="fas fa-link"></i> '.$this->lang->line("Base URL : ").'</span> '. $scheme . '://'. $host. $path .'</label></div>';


        /* get ta table string */
        if ($action_type == 'edit' && $is_first_time_edit_request == 'yes') {
            $cumputed_table_info = $this->create_table_string_for_analyzing($form_array, $query_pieces, $table_id);
        } else {
            $cumputed_table_info = $this->create_table_string_for_analyzing($form_array, $query_pieces);
        }


        $final_string .= $cumputed_table_info['table_string'];

        // Generated URL
        $final_string .= '<div class="card m-0" style="padding:20px;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.09);"><label style="word-break: break-all;margin-bottom: 0;"><span style="font-size: 16px;color: var(--blue);padding-right: 10px;margin-bottom: 0;font-weight: bold;"><i class="fas fa-link"></i> '.$this->lang->line("Generated URL : ").'</span> <span id="updated_url"> '. $custom_url.'</span></label></div>';


        /* prepare the output */
        $final_response = array(
            'base_url' => $scheme . '://'. $host. $path,
            'message' => $final_string,
            'query_pieces' => $cumputed_table_info['final_query_pieces']
        );

        echo json_encode($final_response);
    }
    // analize custom_api_post_method
  
    public function analize_custom_api_url_post_method()
    {
        //custom code goes here   
        $this->ajax_check();

        $custom_url = $this->input->post('custom_api_base_url_post_method', true);

        /* if is called for edit */
        $action_type = $this->input->post('action_type', true);
        $table_id = $this->input->post('table_id', true);
        $is_first_time_edit_request = $this->input->post('is_first_time_edit_request', true);
        $key=$this->input->post('key',true);
        $type=$this->input->post('types',true);
        $key_value=$this->input->post('value',true);
        $check_destination_number=0;
        $check_message_content=0;
        foreach ($type as $type_single) {
            if($type_single=='destination_number')
            {
                $check_destination_number=1;
            }
            if ($type_single=='message_content') {
                $check_message_content=1;
            }
        }
        $str= '';
        $i=0;
        $str='';
        foreach ($key as $val) {
            if(isset($key_value[$i]))
            {
                $str.= "$val=".$key_value[$i]."&";
                $i++;
            }
        }
        $str = rtrim($str,'&');


        /* check valid url */
        $parse_result = parse_url($custom_url);
        // print_r($parse_result);exit();
        $scheme = isset($parse_result['scheme']) ? $parse_result['scheme'] : '';
        $final_query_pieces = array();


        /* check valid url */
        if ($scheme == '') {
            echo json_encode(array('status' => '0','message' => $this->lang->line("Please provide a valid url")));
            exit;
        } else if (!($scheme == 'http' || $scheme == 'https')) {

            echo json_encode(array('status' => '0','message' => $this->lang->line("Please provide a valid url")));
            exit;
        }
          if($check_message_content!=1 || $check_destination_number!=1) 
        {
            $message = $this->lang->line("Final URL must have both #DESTINATION_NUMBER# and #MESSAGE_CONTENT#"); 
            echo json_encode(array('status' => 'error', 'message' => $message));
            exit();
        }

        

        // /* build the output string */
        $final_string = '';

        if (!($action_type == 'edit' && $is_first_time_edit_request == 'yes')) {
            
            $api_response = $this->run_curl_post($custom_url,$str);
            // Test response
            $final_string .= '<div class="card card_post">
                  <div class="card-header">
                    <h4><i class="fas fa-code"></i> '.$this->lang->line("Test response").' :</h4>
                  </div>
                  <div class="card-body">
                    <label style="word-break: break-all;" class="p-0 m-0">'.$api_response.'</label>
                  </div>
                </div>';
        }
        else
        {
             $api_response = $this->run_curl_post($custom_url,$str);
            // Test response
            $final_string .= '<div class="card card_post">
                  <div class="card-header">
                    <h4><i class="fas fa-code"></i> '.$this->lang->line("Test response").' :</h4>
                  </div>
                  <div class="card-body">
                    <label style="word-break: break-all;" class="p-0 m-0">'.$api_response.'</label>
                  </div>
                </div>';
        }
        // $final_string .= '<div class="card" style="padding:20px;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.09);"><label style="word-break: break-all;margin-bottom: 0;"><span style="font-size: 16px;color: var(--blue);padding-right: 10px;margin-bottom: 0;font-weight: bold;"><i class="fas fa-link"></i> '.$this->lang->line("Genarated URL : ").'</span> '.$custom_url."?".$str.'</label></div>';
        echo json_encode(array('status'=>'1','message'=>$final_string));

    }

    public function create_custom_api()
    {
        $this->ajax_check();

        $custom_api_name = $this->input->post('custom_api_name', true);
        $custom_api_url = $this->input->post('custom_api_url', true);
        $updated_url = $this->input->post('updated_url', true);

        $action_type = $this->input->post('action_type', true);
        $table_id = $this->input->post('table_id', true);

        /* validate inputs */
        if ($custom_api_name == '' ||
             $custom_api_url == '' || 
             $updated_url == '' || 
             strpos($updated_url, '#DESTINATION_NUMBER#') === false || 
             strpos($updated_url, '#MESSAGE_CONTENT#') === false) {

            $message = $this->lang->line("Something went wrong");
            if (strpos($updated_url, '#DESTINATION_NUMBER#') === false || strpos($updated_url, '#MESSAGE_CONTENT#') === false) {
                $message = $this->lang->line("Final URL must have both #DESTINATION_NUMBER# and #MESSAGE_CONTENT#");
            }

            echo json_encode(array('status' => 'error', 'message' => $message));
        } else {

            $insert_data = array(
                'user_id' => $this->user_id,
                'gateway_name' => 'custom',
                'custom_name' => $custom_api_name,
                'input_http_url' => $custom_api_url,
                'final_http_url' => $updated_url,
            );

            if ($action_type == 'edit') {
                $this->basic->update_data('sms_api_config', array('id' => $table_id), $insert_data);
                echo json_encode(array('status' => 'success', 'message' => $this->lang->line("Your API has updated successfully.")));
            } else {
                $this->basic->insert_data('sms_api_config', $insert_data);
                echo json_encode(array('status' => 'success', 'message' => $this->lang->line("Your API has created successfully.")));
            }
        }
    }
    
        public function create_custom_api_post_method()
        {

           $this->ajax_check();

           $custom_post_api_name = $this->input->post('custom_api_name_post_method', true);
           $custom_post_api_base_url = $this->input->post('custom_api_base_url_post_method', true);
           $updated_url = $this->input->post('updated_url', true);

           $action_type = $this->input->post('action_type', true);
           $table_id = $this->input->post('table_id', true);
           $key =$this->input->post('key');
           $value =$this->input->post('value');
           $type=$this->input->post('types');

           $check_destination_number=0;
           $check_message_content=0;
           foreach ($type as $type_single) {
            if($type_single=='destination_number')
            {
                $check_destination_number=1;
            }
            if ($type_single=='message_content') {
                $check_message_content=1;
            }
        }

        if ($custom_post_api_name == '' || $custom_post_api_base_url == ''  ) {

            $message = $this->lang->line("Something went wrong");
                // if (strpos($updated_url, '#DESTINATION_NUMBER#') === false || strpos($updated_url, '#MESSAGE_CONTENT#') === false) {
                //     $message = $this->lang->line("Final URL must have both #DESTINATION_NUMBER# and #MESSAGE_CONTENT#");
                // }

            echo json_encode(array('status' => 'error', 'message' => $message));
        }
        else if($check_message_content!=1 || $check_destination_number!=1) 
        {
            $message = $this->lang->line("Final URL must have both #DESTINATION_NUMBER# and #MESSAGE_CONTENT#"); 
            echo json_encode(array('status' => 'error', 'message' => $message));
        }

        else {

            $minimized_data = [];
            foreach ($key as $index => $single_value) {
                $item = [
                    'key'=>$single_value,
                    'type'=>isset($type[$index]) ? $type[$index] : "",
                    "value" => isset($value[$index]) ? $value[$index] : "",
                ];

                array_push($minimized_data, $item);

            }

            $insert_data = array(
                'user_id' => $this->user_id,
                'gateway_name' => 'custom_post',
                'custom_name' => $custom_post_api_name,
                'base_url' => $custom_post_api_base_url,
                'post_data' => json_encode($minimized_data),
            );

            if ($action_type == 'edit') {
                $this->basic->update_data('sms_api_config', array('id' => $table_id), $insert_data);
                $check_message_content=0;
                $check_destination_number=0;
                echo json_encode(array('status' => 'success', 'message' => $this->lang->line("Your API has updated successfully.")));
            } else {
                $this->basic->insert_data('sms_api_config', $insert_data);
                $check_message_content=0;
                $check_destination_number=0;
                echo json_encode(array('status' => 'success', 'message' => $this->lang->line("Your API has created successfully.")));

            }
        }

    }

    public function edit_custom_api_info()
    {
        $this->ajax_check();

        $table_id = $this->input->post('table_id', true);

        $api_info = $this->basic->get_data('sms_api_config', array('where' => array('id' => $table_id, 'user_id' => $this->user_id)));

        if (count($api_info) > 0) {
            
            $api_info = $api_info[0];

            echo json_encode(array(
                'status' => 'success',
                'name' => $api_info['custom_name'], 
                'input_http_url' => $api_info['input_http_url']
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => $this->lang->line("Something went wrong")));
        }
    }
    public function edit_custom_post_api_info()
    {
         $this->ajax_check();

        $table_id = $this->input->post('table_id', true);

        $api_info = $this->basic->get_data('sms_api_config', array('where' => array('id' => $table_id, 'user_id' => $this->user_id)));

        if (count($api_info) > 0) {
            
            $api_info = $api_info[0];
            $post_data= json_decode($api_info['post_data'],true);
            // echo "<pre>"; print_r($post_data); exit;

            echo json_encode(array(
                'status' => 'success',
                'name' => $api_info['custom_name'], 
                'base_url' => $api_info['base_url'],
                'postData' => $post_data,
                // 'key' => $post_data->key,
                // 'type' =>$post_data->type,
                // 'value'=>$post_data->value
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => $this->lang->line("Something went wrong")));
        }
    }


    private function run_curl($url)
    {
        $ch = curl_init();
        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        // grab URL and pass it to the browser
        $response=curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);
        return $response;   
    }

    public function run_curl_post($url,$post_fields){

        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        $response = curl_exec( $ch );
        curl_close($ch);
        return $response;   
    }

    public function drag_drop_email_template($id = null) 
    {   
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['263','271']))==0) {
            redirect('home/login_page', 'location');
        }

        $data = [];
        $data['product_name'] = $this->config->item('product_name');
        $data['page_title'] = $this->lang->line('Drag & Drop Email Template Builder');      

        if (null != $id && $id > 0) {
            $where = [
                'where' => [
                    'id' => $id,
                    'user_id' => $this->user_id,
                ],   
            ];

            $content = $this->basic->get_data('email_sms_template', $where, ['id', 'user_id', 'content', 'location_hash', 'template_name', 'subject'], '', 1);
            $content = isset($content[0]) ? $content[0] : [];
            $template_id = isset($content['id']) ? $content['id'] : null;

            if (! count($content) > 0) {
                return $this->error_404();
            }

            $locationHash = '';
            $mailTemplateHtml = '';
            
            $array = json_decode($content['content'], true);
            $locationHash = isset($content['location_hash']) ? $content['location_hash'] : '';
            $templateName = isset($content['template_name']) ? $content['template_name'] : '';
            $emailSubject = isset($content['subject']) ? $content['subject'] : '';
            $mailTemplateHtml = isset($array['mailTemplateHtml']) ? $array['mailTemplateHtml'] : '';

            $data['templateId'] = $template_id;
            $data['locationHash'] = $locationHash;
            $data['templateName'] = $templateName;
            $data['emailSubject'] = $emailSubject;
            $data['mailTemplateData'] = addslashes($mailTemplateHtml);
        }

        $this->load->view('sms_email_manager/email/email_templates/template', $data);
    }

    public function save_template()
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['263','271']))==0) {
            redirect('home/login_page', 'location');
        }

        $this->ajax_check();

        $templateId = (int) $this->input->post('templateId');
        $templateName = $this->input->post('templateName', true);
        $emailSubject = $this->input->post('emailSubject', true);
        $mailTemplateHtml = $this->input->post('mailTemplateHtml');
        $locationHash = (string) $this->input->post('locationHash', true);
        $refinedMailTemplateHtml = $this->input->post('refinedMailTemplateHtml');

        $templateName = $templateName ? $templateName : 'Untitled template';
        $content = ['mailTemplateHtml' => $this->minify_html($mailTemplateHtml), 'refinedMailTemplateHtml' => $this->minify_html($refinedMailTemplateHtml)];
        $content = json_encode($content, JSON_HEX_APOS);

        if ($templateId) {

            $get_where = [
                'where' => [
                    'id' => $templateId,
                    'user_id' => $this->user_id,
                ],   
            ];

            $result = $this->basic->get_data('email_sms_template', $get_where, ['id', 'user_id'], '', 1);

            if (1 != count($result)) {
                echo json_encode([
                    'status' => false,
                    'message' => $this->lang->line('You do NOT have permission!'),
                ]);

                exit;
            }

            $update_where = [
                'id' => $templateId,
                'user_id' => $this->user_id,
            ];

            $update_data = [
                'template_name' => $templateName,
                'subject' => $emailSubject,
                'content' => $content,
            ];

            $this->basic->update_data('email_sms_template', $update_where, $update_data);

            if ($this->db->affected_rows() > 0) {
                echo json_encode([
                    'status' => true,
                    'locationHash' => $locationHash,
                    'templateName' => $templateName,
                    'subject' => $emailSubject,
                    'templateId' => $templateId,
                    'message' => $this->lang->line('The template has been saved!'),
                ]);

                exit;
            }

            echo json_encode([
                'status' => false,
                'message' => $this->lang->line('There is nothing to save!'),
            ]);
            
            exit;
        }

        $data['user_id'] = $this->user_id;
        $data['editor_type'] = 'drag_and_drop';
        $data['template_name'] = $templateName;

        if ($emailSubject) {
            $data['subject'] = $emailSubject;
        }
        
        $data['location_hash'] = $locationHash;
        $data['content'] = $content;
        $data['template_type'] = 'email';

        $this->basic->insert_data('email_sms_template', $data);

        if ($this->db->affected_rows() > 0) {
            echo json_encode([
                'status' => true,
                'locationHash' => $locationHash,
                'templateName' => $templateName,
                'subject' => $emailSubject,
                'templateId' => $this->db->insert_id(),
                'message' => $this->lang->line('The template has been saved!'),
            ]);

            exit;
        }
        
        echo json_encode([
            'status' => false,
            'message' => $this->lang->line('There is nothing to save!'),
        ]);

        exit;       
    }

    public function upload_file()
    {
        $this->ajax_check();

        header('Access-Control-Allow-Origin: *');

        // Determines upload path
        $upload_dir = APPPATH . '../upload/image/';

        // Starts uploading file
        if (isset($_FILES['uploadedFile'])) {

            $error = $_FILES['uploadedFile']['error'];
            
            if( $error ) {
                $message = isset($this->php_file_upload_errors[$error])
                    ? $this->php_file_upload_errors[$error]
                    : $this->lang->line('Unknown error occurred');

                echo json_encode([
                    'status' => false,
                    'message' => $message,
                ]);
                exit();
            }

            if (is_uploaded_file($_FILES['uploadedFile']['tmp_name'])) {
                $tmp_name = $_FILES['uploadedFile']['tmp_name'];
                $post_filename = $_FILES["uploadedFile"]["name"];
                $extension = mb_substr($post_filename, mb_strrpos($post_filename, '.'));

                if(! in_array(strtolower($extension), ['.jpeg', '.jpg', '.png', '.gif'])) {
                    echo json_encode([
                        'status' => false,
                        'message' => $this->lang->line('File type not allowed'),
                    ]);
                    exit();
                }

                $filename = 'media_' . $this->user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) . $extension;
                $destination = $upload_dir . $filename;

                if (move_uploaded_file($tmp_name, $destination)) {

                    // Changes the file permission
                    chmod($destination, 0644);

                    $url = base_url('upload/image/' . $filename);
                    echo json_encode([
                        'status' => true,
                        'filename' => $url,
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

    public function send_email() 
    {
        // header('Access-Control-Allow-Origin: *');
        // header('content-tye: application/json');
        // echo json_encode([
        //     "status" => true,
        // ]);
    }


    public function email_templates_list()
    {
        $user_id = $this->input->post('user_id');

        if ($user_id != md5($this->user_id)) {
            echo json_encode([
                'status' => false,
                'message' => $this->lang->line('Invalid request'),
            ]);

            exit;
        }

        $email_templates_list = $this->get_email_templates_list();

        echo json_encode([
            'status' => true,
            'message' => $this->lang->line('Email templates list updated'),
            'data' => $email_templates_list,
        ]);
    }

    private function get_email_templates_list() 
    {
        $email_template_where = [
            'where' => [
                'user_id' => $this->user_id,
                'editor_type' => 'drag_and_drop',
            ],
        ];

        $email_template_select = [
            'id',
            'template_name',
        ];

        $email_templates = $this->basic->get_data('email_sms_template', $email_template_where, $email_template_select, '', null, null, 'id desc');

        if (count($email_templates) > 0) {
            $email_templates = array_column($email_templates, 'template_name', 'id');
        } else {
            $email_templates = [];
        }

        return $email_templates;
    }

    public function template_lists($type='')
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['263','271']))==0) {
            redirect('home/login_page', 'location');
        }

        $data['body'] = 'sms_email_manager/email/email_templates/template_lists';
        $data['page_title'] = ucfirst($type). ' ' .$this->lang->line('Template');
        $data['template_type'] = $type;
        $this->_viewcontroller($data); 
    }

    public function template_lists_data()
    {
        $this->ajax_check();

        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['263','271']))==0) exit;

        $template_type = trim($this->input->post("template_type",true));
        $template_text  = trim($this->input->post("template_text",true));

        if ('email' == $template_type) {
            $display_columns = array("#",'id','template_name','template_type', 'editor_type', 'actions');
        } else if ('sms' == $template_type) {
            $display_columns = array("#",'id','template_name','template_type', 'actions');
        }

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple = array();
        $where_simple['user_id'] = $this->user_id;
        $where_simple['template_type'] = $template_type;

        if($template_text != '') $where_simple['template_name like'] = "%".$template_text."%";

        $where  = array('where'=>$where_simple);

        $table = "email_sms_template";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

        $total_rows_array = $this->basic->count_row($table,$where,$count="id",$join="",$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        for($i = 0; $i < count($info); $i++)
        {
            $tempType = $info[$i]['template_type'];
            if($tempType == 'sms') {
                $info[$i]['template_type'] = "<span class='badge badge-light'><i class='fas fa-sms'></i> ".strtoupper($tempType)."</span>";
            } else {
                $info[$i]['template_type'] = "<span class='badge badge-light'><i class='fas fa-envelope'></i> ".ucfirst($tempType)."</span>";
            }

            $editor_type = $info[$i]['editor_type'];
            
            if ('email' == $template_type) {
                if('rich_text_editor' == $editor_type) {
                    $info[$i]['editor_type'] = "<span class='badge badge-light'><i class='fa fa-file-text'></i> " . $this->lang->line("Rich Text") . "</span>";
                } else {
                    $info[$i]['editor_type'] = "<span class='badge badge-light'><i class='fa fa-bars'></i> " . $this->lang->line("Drag & Drop") . "</span>";
                }
            }

            if('drag_and_drop' == $editor_type) {
                $content_array = json_decode($info[$i]['content'], true);
                $refinedEmailTemplateData = isset($content_array['refinedMailTemplateHtml']) 
                    ? $content_array['refinedMailTemplateHtml'] 
                    : '';

                $info[$i]['actions'] = "<div><a href='#' data-toggle='tooltip' title='".$this->lang->line("View Template")."' class='btn btn-circle btn-outline-primary view-emial-template' data-email-template-data=''><i class='fas fa-eye'></i></a><div class='d-none'>{$refinedEmailTemplateData}</div>&nbsp;&nbsp;";

                $info[$i]['actions'] .= "<a href='" . base_url('sms_email_manager/drag_drop_email_template/' . $info[$i]['id'] . '/' . $info[$i]['location_hash']) . "' data-toggle='tooltip' title='".$this->lang->line("Edit Template")."' class='btn btn-circle btn-outline-warning' target='_BLANK'><i class='fas fa-edit'></i></a>&nbsp;&nbsp;";
            } else {
                $info[$i]['actions'] = "<div><a href='".base_url()."sms_email_manager/view_template/".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("View Template")."' class='btn btn-circle btn-outline-primary'><i class='fas fa-eye'></i></a>&nbsp;&nbsp;";

                $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Edit Template")."' class='btn btn-circle btn-outline-warning edit_template' table_id='".$info[$i]['id']."' type='".$tempType."'><i class='fas fa-edit'></i></a>&nbsp;&nbsp;";
            }

            $info[$i]['actions'] .= "<a href='#' data-toggle='tooltip' title='".$this->lang->line("Delete Template")."' class='btn btn-circle btn-outline-danger delete_template' table_id='".$info[$i]['id']."' type='".$tempType."'><i class='fas fa-trash-alt'></i></a></div>
            <script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
        }


        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function create_template_action()
    {
        $this->ajax_check();

        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['263','271']))==0) exit;

        $this->csrf_token_check();

        $template_type = $this->input->post("template_type",true);
        $this->form_validation->set_rules('temp_name', $this->lang->line('Template Name'), 'trim|required');

        if($template_type == 'email') {
            $this->form_validation->set_rules('temp_subject', $this->lang->line('Template Subject'), 'trim|required');
        }

        $this->form_validation->set_rules('temp_contents', $this->lang->line('Template Contents'), 'trim|required');

        if (false === $this->form_validation->run()) {
            $message = '';
            if ($this->form_validation->error('temp_name')) {
                $message = $this->form_validation->error('temp_name');
            } elseif ($this->form_validation->error('temp_subject')) {
                $message = $this->form_validation->error('temp_subject');
            } elseif ($this->form_validation->error('temp_contents')) {
                $message = $this->form_validation->error('temp_contents');
            }

            $message = strip_tags($message);
            echo json_encode(["error"=>true,"message"=>$message]);
            exit;
        }

        $inserted_data = [];
        $inserted_data['template_name'] = strip_tags(trim($this->input->post("temp_name",true)));
        $inserted_data['subject'] = strip_tags(trim($this->input->post("temp_subject",true)));
        $inserted_data['content'] = $this->input->post("temp_contents");
        $inserted_data['template_type'] = $template_type;
        $inserted_data['user_id'] = $this->user_id;

        if($this->basic->insert_data("email_sms_template",$inserted_data)) {
            $message = $this->lang->line('Template has been Created successfully.');
            echo json_encode(["status"=>"1","message"=>$message]); exit;
        } else {
            $message = $this->lang->line('Something went wrong, please try once again.');
            echo json_encode(["status"=>"0","message"=>$message]); exit;
        }
    }

    public function view_template($id='')
    {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['263','271']))==0) {
            redirect('home/login_page', 'location');
        }

        if($id == '' || $id == "0") {
            redirect("home/error_404","location");
        }

        $data['template_data'] = $this->basic->get_data("email_sms_template",['where'=>['id'=>$id,'user_id'=>$this->user_id]]);
        $data['templateType'] = $data['template_data'][0]['template_type'];
        $data['body'] = 'sms_email_manager/sms/view_template';
        $data['page_title'] = $this->lang->line("View"). ' '. ucfirst($data['templateType']). ' ' .$this->lang->line('Template');
        $this->_viewcontroller($data); 
    }

    public function update_template_action()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['263','271']))==0) exit;
        $this->csrf_token_check();

        $table_id = $this->input->post("tableid",true);
        if($table_id == '' || $table_id == 0) exit;

        $template_type = $this->input->post("tem_type",true);
        $this->form_validation->set_rules('updated_template_name', $this->lang->line('Template Name'), 'trim|required');

        if($template_type == 'email') {
            $this->form_validation->set_rules('updated_template_subject', $this->lang->line('Template Subject'), 'trim|required');
        }

        $this->form_validation->set_rules('updated_template_contents', $this->lang->line('Template Contents'), 'trim|required');

        if (false === $this->form_validation->run()) {
            $message = '';
            if ($this->form_validation->error('updated_template_name')) {
                $message = $this->form_validation->error('updated_template_name');
            } elseif ($this->form_validation->error('updated_template_subject')) {
                $message = $this->form_validation->error('updated_template_subject');
            } elseif ($this->form_validation->error('updated_template_contents')) {
                $message = $this->form_validation->error('updated_template_contents');
            }

            $message = strip_tags($message);
            echo json_encode(["error"=>true,"message"=>$message]);
            exit;
        }

        $updated_data = [];
        $updated_data['template_name'] = strip_tags(trim($this->input->post("updated_template_name",true)));
        $updated_data['subject'] = strip_tags(trim($this->input->post("updated_template_subject",true)));
        $updated_data['content'] = $this->input->post("updated_template_contents");
        $updated_data['template_type'] = $template_type;
        $updated_data['user_id'] = $this->user_id;

        if($this->basic->update_data("email_sms_template",['id'=>$table_id,'user_id'=>$this->user_id],$updated_data)) {
            $message = $this->lang->line('Template has been Updated successfully.');
            echo json_encode(["status"=>"1","message"=>$message]); exit;
        } else {
            $message = $this->lang->line('Something went wrong, please try once again.');
            echo json_encode(["status"=>"0","message"=>$message]); exit;
        }
    }

    public function delete_template()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, ['263','271']))==0) exit;
        $this->csrf_token_check();

        $table_id = $this->input->post("table_id",true);
        $type = $this->input->post("type",true);

        if($table_id == "" || $table_id == "0") exit;

        if($this->basic->delete_data("email_sms_template",array("id"=>$table_id,"user_id"=>$this->user_id)))
        {
            echo "1";
        } else {
            echo "0";
        }
    }                  

    public function get_template_info()
    {
        $this->ajax_check();

        $table_id = $this->input->post("table_id",true);
        if($table_id == '' || $table_id == "0") exit;

        $template_info = $this->basic->get_data("email_sms_template",['where'=>['id'=>$table_id,'user_id'=>$this->user_id]]);
        $template_info = $template_info[0];

        $additional_class = $additional_class2 = '';
        if($template_info['template_type']=='sms') {
            $additional_class = "d-none";
        }
        if($template_info['template_type'] == 'email') {
            $additional_class2 = 'col-md-6';
        }

        $html = '';
        if(isset($template_info)) {
            $html .= '
                <div class="row">
                    <input type="hidden" name="table_id" id="table_id" value="'.$template_info['id'].'">
                    <input type="hidden" name="tem_type" id="tem_type" value="'.$template_info['template_type'].'">
                    <div class="col-12 '.$additional_class2.'" id="name-div">
                        <div class="form-group">
                            <label>'.$this->lang->line("Template Name").'</label>
                            <input type="text" class="form-control" name="updated_template_name" id="updated_template_name" value="'.$template_info['template_name'].'">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 '.$additional_class.'" id="subject-div">
                        <div class="form-group">
                            <label>'.$this->lang->line("Subject").'</label>
                            <input type="text" class="form-control" name="updated_template_subject" id="updated_template_subject" value="'.$template_info['subject'].'">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>'.$this->lang->line("content").'</label>
                            <span class="float-right"> 
                              <a title="'.$this->lang->line("You can include #LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.").'" data-toggle="tooltip" data-placement="top" class="btn-sm lead_last_name button-outline"><i class="fa fa-user"></i> '.$this->lang->line("last name").'</a>
                            </span>
                            <span class="float-right"> 
                              <a title="'.$this->lang->line("You can include #FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.").'" data-toggle="tooltip" data-placement="top" class="btn-sm lead_first_name button-outline"><i class="fa fa-user"></i> '.$this->lang->line("first name").'</a>
                            </span>
                            <textarea name="updated_template_contents" id="updated_template_contents" class="form-control updated_template_contents">'.$template_info['content'].'</textarea>
                        </div>
                    </div>
                </div>
            ';
        }

        echo $html;

    }

    private function minify_html($input) {
    
        if(trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));
        // Minify inline CSS declaration(s)
        if(strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . minify_css_helper($matches[3]) . $matches[2];
            }, $input);
        }
        return preg_replace(
            array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1\x1A>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                // Force line-break with `&#10;` or `&#xa;`
                '#&\#(?:10|xa);#',
                // Force white-space with `&#32;` or `&#x20;`
                '#&\#(?:32|x20);#',
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
            ),
            array(
                "<$1$2</$1\x1A>",
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                "\n",
                ' ',
                ""
            ),
        $input);
    }

}