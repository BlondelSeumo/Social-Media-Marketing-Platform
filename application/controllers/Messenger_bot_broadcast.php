<?php

require_once("Home.php"); // loading home controller

class Messenger_bot_broadcast extends Home
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');

        $function_name=$this->uri->segment(2);

        if($function_name!="index" && $function_name!="")
        {
        	if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access))
        	redirect('home/login_page', 'location');
       	}

        if($function_name!="" && $function_name!="index" && $function_name!="otn_subscriber_broadcast_campaign" && $function_name!="otn_subscriber_broadcast_campaign_data")
        {
          if($this->session->userdata("facebook_rx_fb_user_info")==0)
          redirect('social_accounts/index','refresh');
          $this->load->library("fb_rx_login");
        }
        $this->important_feature();
        $this->member_validity();
    }


    public function index()
    {
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        $data['body'] = 'messenger_tools/bulk_message/menu_block';
        $data['page_title'] = $this->lang->line('Broadcasting');
        $this->_viewcontroller($data);
    }


    /*-------------OTN BROADCASTING FUNCTIONS-----------*/
    /*==============================================*/
    public function otn_subscriber_broadcast_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access))
        redirect('home/login_page', 'location');

        $data['body'] = "messenger_tools/otn_manager/subscriber_bulk_broadcast_report";
        $data['page_title'] = $this->lang->line("OTN Subscriber Broadcast");
        $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');
        foreach($page_list as $value)
        {
            $page_info[$value['id']] = $value['page_name'];
        }      
        // $page_info[''] = $this->lang->line("Page");
        $data['page_list'] = $page_info;
        $this->_viewcontroller($data);
    }
    

    public function otn_subscriber_broadcast_campaign_data()
    { 
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();

        $search_value = $this->input->post("search_value",TRUE);
        $page_id = $this->input->post("search_page_id",TRUE);
        $status = $this->input->post("search_status",TRUE);
        $campaign_date_range = $this->input->post("campaign_date_range",TRUE);


        $display_columns = 
        array(
          "#",
          "CHECKBOX",
          'campaign_name',
          'page_name',
          'broadcast_type',
          'posting_status',
          'actions',
          'total_thread',
          'successfully_sent',
          'successfully_delivered',
          'successfully_opened',
          'schedule_time',
          'created_at',
          'label_names'
        );
        

        $search_columns = array('campaign_name','label_names','postback_id','broadcast_type');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 12;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'created_at';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom="messenger_bot_broadcast_serial.user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
        if($campaign_date_range!="")
        {
            $exp = explode('|', $campaign_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
            if($from_date!="Invalid date" && $to_date!="Invalid date")
            $where_custom .= " AND created_at >= '{$from_date}' AND created_at <='{$to_date}'";
        }
        $this->db->where($where_custom);

        if($page_id!="") $this->db->where(array("page_id"=>$page_id)); 
        if($status!="") $this->db->where(array("posting_status"=>$status));       
        $this->db->where(array("broadcast_type"=>"OTN"));       
        
        $table="messenger_bot_broadcast_serial";
        $info=$this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');
        
        $this->db->where($where_custom);
        if($page_id!="") $this->db->where(array("page_id"=>$page_id)); 
        if($status!="") $this->db->where(array("posting_status"=>$status)); 
        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='');

        $total_result=$total_rows_array[0]['total_rows'];

        foreach($info as $key => $value) 
        {
            $action_count = 3;
            if($info[$key]['schedule_time'] != "0000-00-00 00:00:00")
            $scheduled_at = date("M j, y H:i",strtotime($info[$key]['schedule_time']));
            else $scheduled_at = '<span class="text-muted"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line("Not Scheduled")."<span>";
            $info[$key]['schedule_time'] =  "<div style='min-width:110px;'>".$scheduled_at."</div>";

            if($info[$key]['created_at'] != "0000-00-00 00:00:00")
            $info[$key]['created_at'] = "<div style='min-width:110px;'>".date("M j, y H:i",strtotime($info[$key]['created_at']))."</div>";

            $posting_status = $info[$key]['posting_status'];

            $info[$key]['page_name']="<a target='_BLANK' href='https://facebook.com/".$info[$key]['fb_page_id']."'>".$info[$key]['page_name']."</a>";

            if($posting_status=='1')
            $info[$key]['delete'] =  "<a class='btn btn-circle btn-light pointer text-muted'  data-toggle='tooltip' title='".$this->lang->line("Campaign in processing can not be deleted. You can pause campaign and then delete it.")."'><i class='fas fa-trash-alt'></i></a>";
            else  $info[$key]['delete'] =  "<a class='btn btn-circle btn-outline-danger delete'  id='".$info[$key]['id']."' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."' href=''><i class='fas fa-trash-alt'></i></a>";
         
            $is_try_again=$info[$key]["is_try_again"];
            $force_porcess_str="";
            if($this->config->item("broadcaster_number_of_message_to_be_sent_in_try")=="" || $this->config->item("broadcaster_number_of_message_to_be_sent_in_try")=="0")
            {
                $force_porcess_str="";
            }
            else
            {
                $action_count++;
                if($posting_status=='3')$force_porcess_str .= "<a href='' class='btn btn-circle btn-outline-success play_campaign_info' data-toggle='tooltip' title='".$this->lang->line("Resume Campaign")."' table_id='".$info[$key]['id']."'><i class='fas fa-play'></i></a>";
                else if($posting_status!='4')  $force_porcess_str .= "<a href='' class='btn btn-circle btn-outline-dark pause_campaign_info' data-toggle='tooltip' title='".$this->lang->line("Pause Campaign")."' table_id='".$info[$key]['id']."'><i class='fas fa-pause'></i></a>";
            }

            if($posting_status=='1')
            {
                $action_count++;
                $force_porcess_str .= "<a href='' class='btn btn-circle btn-outline-warning force' data-toggle='tooltip' title='".$this->lang->line("Force Re-process Campaign")."' id='".$info[$key]['id']."'><i class='fas fa-sync'></i></a>";
            } 
            $info[$key]['force'] = $force_porcess_str;

            $hold_message = '<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Campaign Status : On-hold").'" data-content="'.$this->lang->line("If campaign receive more than `Subscriber Broadcast - hold after number of errors` error message during broadcast, system hold the campaign to avoid risk. The subscribers those get error, automatically marked as unavailable for future campaign to reduce error rate in future until subscriber send message to your Messenger BOT again. In this case, we suggest you to check the error message in report, and if you think it’s not for your message content, but for specific subscribers, you can restart the campaign from where it is left off, by clicking on the option menu & then click Force Resume. ").'"><i class="fas fa-info-circle"></i> </a>';


            if( $posting_status == '2') $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-success badge"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</span></div>';
            else if( $posting_status == '1') $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-warning"><i class="fas fa-spinner"></i> '.$this->lang->line("Processing").'</span></div>';
            else if( $posting_status == '3') $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-muted"><i class="fas fa-stop"></i> '.$this->lang->line("Paused").'</span></div>';
            else if( $posting_status == '4') $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-dark"><i class="fas fa-ban"></i> '.$this->lang->line("On-hold").$hold_message.'</span></div>';
            else $info[$key]['posting_status'] = '<div style="min-width:100px"><span class="text-danger"><i class="far fa-times-circle"></i> '.$this->lang->line("Pending").'</span></div>';

            $info[$key]['posting_status'] = '<div style="min-width:80px;">'.$info[$key]['posting_status'].'</div>';

            $info[$key]['report'] =  "<a class='btn btn-circle btn-outline-primary sent_report' data-toggle='tooltip' title='".$this->lang->line("Campaign Report")."' href='' cam-id='".$info[$key]['id']."'><i class='fas fa-eye'></i></a>";

            if($posting_status!='0' || $info[$key]['schedule_type']!="later") 
            $info[$key]['edit'] =  "<a class='btn btn-circle btn-light text-muted' data-toggle='tooltip' title='".$this->lang->line("Only scheduled pending campaign can be edited.")."'><i class='fas fa-edit'></i></a>";
            else
            {                
                $edit_url = site_url('messenger_bot_broadcast/otn_edit_subscriber_broadcast_campaign/'.$info[$key]['id']);
                $info[$key]['edit'] =  "<a class='btn btn-circle btn-outline-warning' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."' href='".$edit_url."'><i class='fas fa-edit'></i></a>";
            }

            $action_width = ($action_count*47)+20;
            $info[$key]['actions'] ='
            <div class="dropdown d-inline dropright">
              <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
                $info[$key]['actions'] .= $info[$key]['report'];
                $info[$key]['actions'] .= $info[$key]['edit'];
                $info[$key]['actions'] .= $force_porcess_str;
                $info[$key]['actions'] .= $info[$key]['delete'];
                $info[$key]['actions'] .="
              </div>
            </div>
            <script>
            $('[data-toggle=\"tooltip\"]').tooltip();
            $('[data-toggle=\"popover\"]').popover(); 
            $('[data-toggle=\"popover\"]').on(\"click\", function(e) {e.preventDefault(); return true;});
            </script>";
        }
        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        echo json_encode($data);
    }

    public function otn_subscriber_delete_campaign()
    {   
        $this->ajax_check();
        $this->csrf_token_check();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();

        $id=$this->input->post("id");

        $xdata = $this->basic->get_data("messenger_bot_broadcast_serial",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $posting_status  = isset($xdata[0]["posting_status"]) ? $xdata[0]["posting_status"] : "";
        $broadcast_id  = isset($xdata[0]["broadcast_id"]) ? $xdata[0]["broadcast_id"] : "";
        $page_id  = isset($xdata[0]["page_id"]) ? $xdata[0]["page_id"] : "";
        $otn_postback_str  = isset($xdata[0]["otn_postback_id"]) ? $xdata[0]["otn_postback_id"] : "";
        $total_thread  = isset($xdata[0]["total_thread"]) ? $xdata[0]["total_thread"] : 0;

        if($posting_status=='1')
        {
           echo $this->lang->line("This campaign is in processing state and can not be deleted.");
           exit();
        }

        if($this->basic->delete_data("messenger_bot_broadcast_serial",array("id"=>$id,"user_id"=>$this->user_id)))
        {
            if($posting_status!="2")
            {
                $subscribers = [];
                $otn_postback_ids = explode(',', $otn_postback_str);
                $subscriber_info = $this->basic->get_data('messenger_bot_broadcast_serial_send',['where'=>["campaign_id"=>$id,"user_id"=>$this->user_id]]);
                foreach($subscriber_info as $value)
                    array_push($subscribers, $value['subscribe_id']);
                $this->db->where('page_table_id',$page_id);
                $this->db->where_in('subscriber_id',$subscribers);
                if(!empty($otn_postback_ids)) $this->db->where_in('otn_id',$otn_postback_ids);
                $this->db->update('otn_optin_subscriber',['is_sent'=>'0']);
            }
            $this->basic->delete_data("messenger_bot_broadcast_serial_send",array("campaign_id"=>$id,"user_id"=>$this->user_id));
            if($posting_status!="2") // removing usage data if deleted and campaign is pending
            $this->_delete_usage_log($module_id=275,$request=$total_thread);   
            echo "1";
        } 
      
    }

    public function otn_force_reprocess_campaign()
    {
        $this->ajax_check();
        $this->csrf_token_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();

        $id=$this->input->post("id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('is_try_again'=>'1','posting_status'=>'1');
        $this->basic->update_data('messenger_bot_broadcast_serial',$where,$data);
        if($this->db->affected_rows() != 0)  echo "1";
        else  echo "0";
    }

    public function otn_restart_campaign()
    {
        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();

        $id=$this->input->post("table_id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('is_try_again'=>'1','posting_status'=>'1','last_try_error_count'=>0);
        $this->basic->update_data('messenger_bot_broadcast_serial',$where,$data);
        echo '1';
    }

    public function otn_ajax_campaign_pause()
    {
        $this->ajax_check();
        $this->csrf_token_check();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();

        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('messenger_bot_broadcast_serial',array('id'=>$table_id),array('posting_status'=>'3','is_try_again'=>'0'));
        echo '1';
    }

    public function otn_ajax_campaign_play()
    {
        $this->ajax_check();
        $this->csrf_token_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();

        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('messenger_bot_broadcast_serial',array('id'=>$table_id),array('posting_status'=>'1','is_try_again'=>'1'));
        echo '1';
    }


    public function otn_campaign_sent_status()
    {
        $this->ajax_check();
        $this->csrf_token_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();
        
        $id = $this->input->post("id");

        $campaign_data = $this->basic->get_data("messenger_bot_broadcast_serial",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $report = isset($campaign_data[0]["report"]) ? json_decode($campaign_data[0]["report"],true) : array();
        $campaign_name  = isset($campaign_data[0]["campaign_name"]) ? $campaign_data[0]["campaign_name"] : "";
        $total_thread  = isset($campaign_data[0]["total_thread"]) ? $campaign_data[0]["total_thread"] : 0;
        $successfully_sent  = isset($campaign_data[0]["successfully_sent"]) ? $campaign_data[0]["successfully_sent"] : 0;
        $successfully_delivered  = isset($campaign_data[0]["successfully_delivered"]) ? $campaign_data[0]["successfully_delivered"] : 0;
        $successfully_opened  = isset($campaign_data[0]["successfully_opened"]) ? $campaign_data[0]["successfully_opened"] : 0;
        $successfully_clicked  = isset($campaign_data[0]["successfully_clicked"]) ? $campaign_data[0]["successfully_clicked"] : 0;
        $error_message  = isset($campaign_data[0]["error_message"]) ? $campaign_data[0]["error_message"] : "";
        $page_name  = isset($campaign_data[0]["page_name"]) ? $campaign_data[0]["page_name"] : "";
        $fb_page_id  = isset($campaign_data[0]["fb_page_id"]) ? $campaign_data[0]["fb_page_id"] : "";

        
        if($successfully_sent==0) $sent_rate=0;
        else $sent_rate=($successfully_sent/$total_thread)*100;

        if($successfully_delivered==0) $delivery_rate=0;
        else $delivery_rate=($successfully_delivered/$successfully_sent)*100;

        if($successfully_opened==0) $open_rate=0;
        else $open_rate=($successfully_opened/$successfully_sent)*100;

        if($successfully_clicked==0) $click_rate=0;
        else $click_rate=($successfully_clicked/$successfully_sent)*100;


        $sent_rate = round($sent_rate);
        $delivery_rate = round($delivery_rate);
        $open_rate = round($open_rate);
        $click_rate = round($click_rate);

        $posting_status = $campaign_data[0]['posting_status'];

        if( $posting_status == '2') $posting_status = '<span class="text-success"> ('.$this->lang->line("Completed").')</span>';
        else if( $posting_status == '1') $posting_status = '<span class="text-warning"> ('.$this->lang->line("Processing").')</span>';
        else if( $posting_status == '3') $posting_status = '<span class="text-muted"> ('.$this->lang->line("Paused").')</span>';
        else if( $posting_status == '4') $posting_status = '<span class="text-dark"> ('.$this->lang->line("On-hold").')</span>';
        else $posting_status = '<span class="text-danger"> ('.$this->lang->line("Pending").')</span>';


        $response = "";

        $drop_menu = "";
        $send_where_it_is_left_off = "";

        if($campaign_data[0]['posting_status']=='4')
        {            
        $drop_menu = '<div class="btn-group dropleft float-right"><button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$this->lang->line("Options").'  </button>  <div class="dropdown-menu dropleft"> <a class="dropdown-item has-icon restart_button pointer" title="'.$this->lang->line('If the campaign has been completed due to error but there are still some subscriber to be sent, you can resume it from it was left off by force.').'" data-toggle="tooltip" table_id="'.$id.'"><i class="fas fa-sync"></i> '.$this->lang->line("Force Resume").'</a></div> </div>';
        }

        if($error_message!="")
        $response .= "<div class='alert alert-danger text-center'> {$this->lang->line("Something went wrong for one or more message. Original error message :")} {$error_message} <br><a class='pointer' style='text-decoration:underline;' href='' data-toggle='modal' data-target='#error_message_learn'>".$this->lang->line("Learn more about common error messages")."</a></div>";


        // $response .= "<div class='row'><h6 style='width:100%;padding:0 20px'><span class='float-left'>".$campaign_name." : <a href='https://facebook.com/".$fb_page_id."'>".$page_name."</a></span> <span class='float-right'>".$posting_status."</span></h6></div>";

        $response .='
        <div class="row">
        <div class="col-12 col-sm-6 col-md-6 col-lg-4">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="fas fa-info-circle"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'. $this->lang->line("Campaign").$posting_status.'</h4>
                </div>
                <div class="card-body">
                  '.$campaign_name.'
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-4">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="far fa-newspaper"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'.$this->lang->line("Page Name").'</h4>
                </div>
                <div class="card-body">
                  <a target="_BLANK" href="https://facebook.com/'.$campaign_data[0]["fb_page_id"].'">'.$campaign_data[0]["page_name"].'</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-4">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="far fa-paper-plane"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'.$this->lang->line("Sent").' ('.$sent_rate.'%)</h4>
                </div>
                <div class="card-body">
                  '.$successfully_sent.'/'.$total_thread.'</a>
                </div>
              </div>
            </div>
          </div>
          <!--<div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'.$this->lang->line("Delivered").' ('.$delivery_rate.'%)</h4>
                </div>
                <div class="card-body">
                  '.$successfully_delivered.'/'.$total_thread.'
                </div>
              </div>
            </div>
          </div>-->
          <!--<div class="col-md-4 col-12">
            <div class="card card-statistic-1">
              <div class="card-icon bg-primary">
                <i class="fas fa-eye"></i>
              </div>
              <div class="card-wrap">
                <div class="card-header">
                  <h4>'.$this->lang->line("Opened").' ('.$open_rate.'%)</h4>
                </div>
                <div class="card-body">
                  '.$successfully_opened.'/'.$total_thread.'
                </div>
              </div>
            </div>
          </div>-->

        </div>
        <style>
        .card-statistic-1{border:.5px solid #dee2e6;border-radius: 4px;}
        .card-statistic-1 .card-icon i{font-size:40px !important;margin-top:20px;}
        </style>';       

        echo json_encode(array("response1"=>$response,"response3"=>$drop_menu));
    }

    public function otn_campaign_sent_status_data()
    { 
        $this->ajax_check();
        $this->csrf_token_check();

        $search_value = $_POST['search']['value'];
        $id = $this->input->post("campaign_id");

        $display_columns = 
        array(
          "#",
          "CHECKBOX",
          'subscriber_name',
          'subscriber_lastname',
          'subscribe_id',
          'sent_time',
          'open_time',
          'delivery_time',
          'message_sent_id'
        );
        $search_columns = array('subscriber_name','subscriber_lastname','subscribe_id','sent_time');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'sent_time';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom="user_id = ".$this->user_id." AND campaign_id = ".$id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
     
        $this->db->where($where_custom);
        
        $table="messenger_bot_broadcast_serial_send";
        $info=$this->basic->get_data($table,$where='',$select='',$join='',$limit,$start,$order_by,$group_by='');

        $this->db->where($where_custom);
        $total_rows_array=$this->basic->count_row($table,$where='',$count=$table.".id",$join,$group_by='');

        $total_result=$total_rows_array[0]['total_rows'];


        $i=0;
        foreach($info as $key => $value) 
        {
            $sent=$opened=$clicked=$delivered="<i class='fa fa-remove red'></i>";
            if($value["sent_time"]!="0000-00-00 00:00:00") $sent="<i class='fa fa-check-circle green'></i> ".date("M j, y H:i",strtotime($value['sent_time']));
            if($value["opened"]=="1") $opened="<i class='fa fa-check-circle green'></i> ".date("M j, y H:i",strtotime($value['open_time']));
            if($value["delivered"]=="1") $delivered="<i class='fa fa-check-circle green'></i> ".date("M j, y H:i",strtotime($value['delivery_time']));
            if($value["clicked"]=="1") $clicked="<i class='fa fa-check-circle green'></i> ".date("M j, y H:i",strtotime($value['click_time']));

            $info[$key]['sent_time'] =  $sent;
            $info[$key]['open_time'] =  $opened;
            $info[$key]['delivery_time'] =  $delivered;

            $tempu=explode(' ', $value['message_sent_id']);
            if(isset($tempu[0]) && (strlen($tempu[0])>50) || strpos($tempu[0], 'mid.$') !== false) $msg_sent_id=' <i class="fa fa-check green"></i> '.$this->lang->line("sent")." : ". $value['message_sent_id'];
            else $msg_sent_id=$value['message_sent_id'];

            if($value['message_sent_id']=="") $info[$key]["message_sent_id"]= "<i class='fa fa-remove red'></i>";
            else $info[$key]["message_sent_id"] = $msg_sent_id;

            $i++;
        }
        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        echo json_encode($data);
    }

    private function otn_get_broadcast_tags()
    {
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
        "NON_PROMOTIONAL_SUBSCRIPTION" => "NON_PROMOTIONAL_SUBSCRIPTION",
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
        ""=>$this->lang->line("Select Tag"),
        "ACCOUNT_UPDATE"=>"ACCOUNT_UPDATE",
        "CONFIRMED_EVENT_UPDATE"=>"CONFIRMED_EVENT_UPDATE",
        "HUMAN_AGENT"=>"HUMAN_AGENT (Closed BETA)",
        "POST_PURCHASE_UPDATE"=>"POST_PURCHASE_UPDATE",
      );

      if(strtotime(date("Y-m-d")) > strtotime("2020-3-4")) return $new_tags;

      unset($new_tags[""]);
      $dropdown = array(""=>$this->lang->line("Select Tag"),"New Tags"=>$new_tags,"Tags supported until Mar 4,2020"=>$old_tags);
      return $dropdown;

    }

    public function otn_create_subscriber_broadcast_campaign()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access))
        redirect('home/login_page', 'location');

        $data["templates"]=$this->basic->get_enum_values("messenger_bot_broadcast_serial","template_type");
        if($data['templates'][9] == 'list') unset($data['templates'][9]);

        $data['body'] = 'messenger_tools/otn_manager/subscriber_bulk_broadcast_add';
        $data['page_title'] = $this->lang->line('Add OTN Subscriber Broadcast');  

        $data['page_info'] = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id)));  
        $data['postback_ids'] = $postback_id_list;

        $data['tag_list'] = $this->otn_get_broadcast_tags();
        $data["broadcast_types"]=$this->basic->get_enum_values_assoc("messenger_bot_broadcast_serial","broadcast_type");

        $data['locale_list'] = $this->sdk_locale();
        $data["time_zone_numeric"]= $this->_time_zone_list_numeric();

        $data["time_zone"]= $this->_time_zone_list();
        $this->_viewcontroller($data); 
    }

    public function otn_subscriber_bulk_broadcast_add_action()
    {
         if(function_exists('ini_set')){
            ini_set('memory_limit', '-1');
         } 


        $this->ajax_check();
        $this->csrf_token_check();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();

        $post=$_POST;
        foreach ($post as $key => $value) 
        {            
            if(!is_array($value)) $temp = strip_tags($value);
            else $temp = $value;
            $$key=$temp;
        }

        if($broadcast_type!="Non Promo") $message_tag = "";

        $posting_status = "0";
        $successfully_sent = 0;
        $successfully_delivered = 0;
        $successfully_opened = 0;
        $successfully_clicked = 0;
        $total_thread = 0;
        $insert_data = array();
        $page_id=$this->input->post("page");// database id
        $page_table_id=$page_id;
        $insert_data['campaign_name'] = $campaign_name;
        $insert_data['page_id'] = $page_table_id;
        $insert_data['broadcast_type'] = $broadcast_type;
        $insert_data['message_tag'] = $message_tag;
        $insert_data['user_gender'] = $user_gender;
        $insert_data['user_time_zone'] = $user_time_zone;
        $insert_data['user_locale'] = $user_locale;

        // domain white list section
        $messenger_bot_user_info_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id)));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $page_name = $messenger_bot_user_info_id[0]['page_name'];
        $fb_page_id=$messenger_bot_user_info_id[0]['page_id'];
        $insert_data['fb_page_id'] =  $fb_page_id;
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["facebook_rx_fb_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"page_id"=>$page_table_id)),"domain");

        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();


        for ($k=1; $k <=1 ; $k++) 
        {    
            $template_type = 'template_type_'.$k;
            $template_type = $$template_type;
            $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['text'] = $text_reply;                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
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
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }



        
            if($template_type == 'media')
            {
                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
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
                    $button_text = 'media_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                     //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'media_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
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
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
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
            }



            if($template_type == 'text_with_buttons')
            {
                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'text_with_buttons_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'text_with_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
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
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
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
            }

            if($template_type == 'quick_reply')
            {
                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                { 
                    $button_text = 'quick_reply_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
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
            }

            if($template_type == 'generic_template')
            {
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
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;                    
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
                    $button_text = 'generic_template_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                    //add an extra query parameter for tracking the subscriber to whom send 
                    if($button_web_url!='')
                        $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                    $button_call_us = 'generic_template_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
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
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                            }
                            else
                                $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;

                            if($button_extension != '' && $button_extension != 'birthday')
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
            }

            if($template_type == 'carousel')
            {
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                for ($j=1; $j <=10 ; $j++) 
                {                                 
                    $carousel_image = 'carousel_image_'.$j.'_'.$k;
                    $carousel_title = 'carousel_title_'.$j.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$j.'_'.$k;
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
                        $button_text = 'carousel_button_text_'.$j."_".$i.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$j."_".$i.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$j."_".$i.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$j."_".$i.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                        //add an extra query parameter for tracking the subscriber to whom send 
                        if($button_web_url!='')
                          $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                        $button_call_us = 'carousel_button_call_us_'.$j."_".$i.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';

                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
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
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['webview_height_ratio'] = 'compact';
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                                }
                                else
                                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;

                                if($button_extension != '' && $button_extension != 'birthday')
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
                }
            }

            if($template_type == 'list')
            {
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'list';

                for ($j=1; $j <=4 ; $j++) 
                {                                 
                    $list_image = 'list_image_'.$j.'_'.$k;
                    if(!isset($$list_image) || $$list_image == '') continue;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$list_image;
                    $list_title = 'list_title_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$list_title;
                    $list_subtitle = 'list_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$list_subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                    $list_image_destination_link = 'list_image_destination_link_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$list_image_destination_link;
                    
                }

                $button_text = 'list_with_buttons_text_'.$k;
                $button_text = isset($$button_text) ? $$button_text : '';
                $button_type = 'list_with_button_type_'.$k;
                $button_type = isset($$button_type) ? $$button_type : '';
                $button_postback_id = 'list_with_button_post_id_'.$k;
                $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                $button_web_url = 'list_with_button_web_url_'.$k;
                $button_web_url = isset($$button_web_url) ? $$button_web_url : '';

                //add an extra query parameter for tracking the subscriber to whom send 
                if($button_web_url!='')
                  $button_web_url=add_query_string_to_url($button_web_url,"subscriber_id","#SUBSCRIBER_ID_REPLACE#");

                $button_call_us = 'list_with_button_call_us_'.$k;
                $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                
                if($button_type == 'post_back')
                {
                    if($button_text != '' && $button_type != '' && $button_postback_id != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'postback';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_postback_id;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
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
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'web_url';
                        if($button_extension != '' && $button_extension == 'birthday'){
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['messenger_extensions'] = 'true';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['webview_height_ratio'] = 'compact';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = base_url('webview_builder/get_birthdate?subscriber_id=#SUBSCRIBER_ID_REPLACE#');
                        }
                        else
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = $button_web_url;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;

                        if($button_extension != '' && $button_extension != 'birthday')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['messenger_extensions'] = 'true';
                            $reply_bot[$k]['attachment']['payload']['buttons'][0]['webview_height_ratio'] = $button_extension;
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
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'phone_number';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_call_us;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }


            }

            $bot_message['message'] = $reply_bot[$k]; 

        }
             
        // domain white list section start
        $this->load->library("fb_rx_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $response=$this->fb_rx_login->domain_whitelist($page_access_token,$value);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $messenger_bot_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $value;
                $temp_data['created_at'] = date("Y-m-d H:i:s");

                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data)) $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end

        $campaign_message_send=$bot_message;
        $campaign_message_send["recipient"]=array("one_time_notif_token"=>"PUT_OTN_TOKEN");

        // if($broadcast_type=='Non Promo')
        // {
        //   $campaign_message_send["messaging_type"]="MESSAGE_TAG";
        //   $campaign_message_send["tag"]=$message_tag;
        // }
        // else $campaign_message_send["messaging_type"]="RESPONSE";

        

        $insert_data['message'] = json_encode($campaign_message_send,true);
        $insert_data['user_id'] = $this->user_id;        
        // $insert_data['template_type'] = $template_type;  
        $insert_data['created_at'] = date('Y-m-d H:i:s');

        if(!isset($schedule_type)) $schedule_type='now';       
        $insert_data['schedule_type'] = $schedule_type;        
        if(!isset($schedule_time)) $schedule_time = "";
        if(!isset($time_zone)) $time_zone = "";

        $insert_data['schedule_time'] = $schedule_time; 
        $insert_data['page_name'] = $page_name;         
        $insert_data["posting_status"]=$posting_status;  
        $insert_data['timezone'] = $time_zone;  

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        if(!isset($excluded_label_ids) || !is_array($excluded_label_ids)) $excluded_label_ids=array();
        if(!isset($otn_postback_ids) || !is_array($otn_postback_ids)) $otn_postback_ids=array();

        if(!empty($label_ids)) $insert_data['label_ids'] = implode(',', $label_ids); else $insert_data['label_ids'] ="";
        if(!empty($excluded_label_ids)) $insert_data['excluded_label_ids'] = implode(',', $excluded_label_ids); else $insert_data['excluded_label_ids'] = "";
        if(!empty($otn_postback_ids)) $insert_data['otn_postback_id'] = implode(',', $otn_postback_ids); else $insert_data['otn_postback_id'] = "";

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
        $insert_data['label_names'] = implode(',', $fb_label_names);

        // =========24H and 24+1 campaign=========
        $promo_sql = "";
        date_default_timezone_set('UTC');
        $current_time  = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-23 hour',strtotime($current_time)));
        if($broadcast_type=='24H Promo') $promo_sql = "last_subscriber_interaction_time > '{$previous_time}' AND";
        if($broadcast_type=='24+1 Promo') $promo_sql = "(last_subscriber_interaction_time < '{$previous_time}' AND is_24h_1_sent='0') AND";
        $this->_time_zone_set();
        //========================================

        $excluded_label_ids_temp=$excluded_label_ids;
        $unsubscribe_labeldata=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("user_id"=>$this->user_id,"page_id"=>$page_table_id,"unsubscribe"=>"1")));
        foreach ($unsubscribe_labeldata as $key => $value) 
        {
            array_push($excluded_label_ids_temp, $value["id"]);
        }

        $sql_part = $sql_part2 = '';
        if(count($label_ids)>0) $sql_part = ' messenger_bot_subscribers_label.contact_group_id IN ('.implode(',', $label_ids).') AND ';
        if(count($label_ids)>0) $sql_part2 = ' messenger_bot_subscribers_label.contact_group_id NOT IN ('.implode(',', $excluded_label_ids_temp).') AND ';        

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

        if(!empty($otn_postback_ids))
        {
            $otn_postback_str = implode("','", $otn_postback_ids);
            $sql="SELECT `messenger_bot_subscriber`.*,`otn_optin_subscriber`.otn_token FROM otn_optin_subscriber LEFT JOIN `messenger_bot_subscriber` ON `otn_optin_subscriber`.`subscriber_id`=`messenger_bot_subscriber`.`subscribe_id` LEFT JOIN `messenger_bot_subscribers_label` ON `messenger_bot_subscribers_label`.`subscriber_table_id`=`messenger_bot_subscriber`.`id` WHERE ".$sql_part." ".$sql_part2." ".$sql_part3." ".$promo_sql." user_id = ".$this->user_id." AND unavailable = '0' AND is_bot_subscriber='1' AND messenger_bot_subscriber.page_table_id = {$page_table_id} AND otn_optin_subscriber.is_sent='0' AND `otn_id` IN('".$otn_postback_str."') GROUP BY otn_optin_subscriber.subscriber_id";
        }
        else
            $sql="SELECT `messenger_bot_subscriber`.*,`otn_optin_subscriber`.otn_token FROM otn_optin_subscriber LEFT JOIN `messenger_bot_subscriber` ON `otn_optin_subscriber`.`subscriber_id`=`messenger_bot_subscriber`.`subscribe_id` LEFT JOIN `messenger_bot_subscribers_label` ON `messenger_bot_subscribers_label`.`subscriber_table_id`=`messenger_bot_subscriber`.`id` WHERE ".$sql_part." ".$sql_part2." ".$sql_part3." ".$promo_sql." user_id = ".$this->user_id." AND unavailable = '0' AND is_bot_subscriber='1' AND messenger_bot_subscriber.page_table_id = {$page_table_id} AND otn_optin_subscriber.is_sent='0' GROUP BY otn_optin_subscriber.subscriber_id";


        $lead_list=$this->basic->execute_query($sql);

        $report = array();    
        $subscriber_auto_ids = [];    
        foreach ($lead_list as $key => $value)
        {
           $total_thread++;
           $report[$value['subscribe_id']] = array
           (
                "subscribe_id"=>$value["subscribe_id"],
                "subscriber_auto_id"=>$value["id"],               
                "subscriber_name"=>$value["first_name"],
                "subscriber_lastname"=>$value["last_name"],
                "sent"=>"0",
                "sent_time"=>"",
                "delivered"=>"0",
                "delivery_time"=>"",
                "opened"=>"0",
                "open_time"=>"",
                "clicked"=>"0",
                "click_time"=>"",
                "click_ref"=>"",
                "message_sent_id"=>"",
                'otn_token'=>$value['otn_token']
            );
           $subscriber_auto_ids[] = $value["id"];
        }

        if($total_thread==0)
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Campaign could not target any subscriber to reach message. Please try again with different targeting options.")));
            exit();
        }

        // 24+1 inactivation becuase he is already sending a promo message
        if($broadcast_type!='24H Promo' && !empty($subscriber_auto_ids))
        {
          $sql_24h="UPDATE messenger_bot_subscriber SET is_24h_1_sent='1' WHERE id IN (".implode(',', $subscriber_auto_ids).")";
          $this->basic->execute_complex_query($sql_24h);
        }
        // ===============================================================

        $status=$this->_check_usage($module_id=275,$request=$total_thread);
        if($status=="2")  //monthly limit is exceeded, can not send another ,message this month
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Sorry, your bulk to send subscriber message is exceeded.")));
            exit();
        }
        else if($status=="3")  //monthly limit is exceeded, can not send another ,message this month
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Sorry, your monthly limit to send subscriber message is exceeded.")));
            exit();
        }

        $insert_data["total_thread"]=$total_thread;
        // $insert_data["report"]=json_encode($report);

        if($this->basic->insert_data('messenger_bot_broadcast_serial',$insert_data))
        {
            $campaign_id= $this->db->insert_id();
            $this->_insert_usage_log($module_id=275,$request=$total_thread);

            $report_insert=array();
            $subscriber_ids = [];
            foreach($report as $key2=>$value2) 
            {               
                $client_thread_id_send = $key2;
                array_push($subscriber_ids, $value2["subscribe_id"]);
                $report_insert[]=array
                (
                    "campaign_id"=>$campaign_id,   
                    "user_id"=>$this->user_id,   
                    "page_id"=>$page_id,   
                    "subscribe_id"=>$value2["subscribe_id"],   
                    "subscriber_auto_id"=>$value2["subscriber_auto_id"],
                    "subscriber_name"=>$value2['subscriber_name'],
                    "subscriber_lastname"=>$value2['subscriber_lastname'],
                    'otn_token'=>$value2['otn_token']
                );
            }
            $this->db->insert_batch('messenger_bot_broadcast_serial_send', $report_insert); // strong the leads to send message in database

            $this->db->where('page_table_id',$page_id);
            $this->db->where_in('subscriber_id',$subscriber_ids);
            if(!empty($otn_postback_ids))$this->db->where_in('otn_id',$otn_postback_ids);
            $this->db->update('otn_optin_subscriber',['is_sent'=>'1']);

            $this->session->set_flashdata('broadcast_success',1);
            echo json_encode(array("status" => "1"));            
        }
        
    }

    public function otn_edit_subscriber_broadcast_campaign($id=0)
    {  
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access))
        redirect('home/login_page', 'location');

        $data["templates"]=$this->basic->get_enum_values("messenger_bot_broadcast_serial","template_type");
        if($data['templates'][9] == 'list') unset($data['templates'][9]);

        $data['body'] = 'messenger_tools/otn_manager/subscriber_bulk_broadcast_edit';
        $data['page_title'] = $this->lang->line('Edit OTN Subscriber Broadcast');  

        $data['page_info'] = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),"bot_enabled"=>"1")),$select='',$join='',$limit='',$start=NULL,$order_by='page_name ASC');

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id)));  
        $data['postback_ids'] = $postback_id_list;

        $data["time_zone"]= $this->_time_zone_list();

        $xdata=$this->basic->get_data("messenger_bot_broadcast_serial",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        if($xdata[0]['posting_status']!='0') exit();
        $data['xdata']=$xdata[0];

        $page_id=$xdata[0]['page_id'];// database id      
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1",'template_for'=>"reply_message")),'','','',$start=NULL,$order_by='template_name ASC');        
        $poption=array();
        foreach ($postback_data as $key => $value) 
        {            
            $poption[$value["postback_id"]]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        $data['poption']=$poption;

        $data['tag_list'] = $this->otn_get_broadcast_tags();
        $data["broadcast_types"]=$this->basic->get_enum_values_assoc("messenger_bot_broadcast_serial","broadcast_type");

        $data['locale_list'] = $this->sdk_locale();
        $data["time_zone_numeric"]= $this->_time_zone_list_numeric();
    
        $this->_viewcontroller($data); 
    }

    public function otn_subscriber_bulk_broadcast_edit_action()
    {   
        
        if(function_exists('ini_set')){
            ini_set('memory_limit', '-1');
        } 

        $this->ajax_check();
        if($this->session->userdata('user_type') != 'Admin' && !in_array(275,$this->module_access)) exit();

        $xid=$this->input->post("xid");

        $xdata = $this->basic->get_data("messenger_bot_broadcast_serial",array("where"=>array("id"=>$xid,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        $total_thread  = isset($xdata[0]["total_thread"]) ? $xdata[0]["total_thread"] : 0;
        $posting_status  = isset($xdata[0]["posting_status"]) ? $xdata[0]["posting_status"] : "";
        $schedule_type  = isset($xdata[0]["schedule_type"]) ? $xdata[0]["schedule_type"] : "now";
        $page_id = isset($xdata[0]["page_id"]) ? $xdata[0]["page_id"] : 0;
        $otn_postback_str  = isset($xdata[0]["otn_postback_id"]) ? $xdata[0]["otn_postback_id"] : "";

        if($posting_status!='0') exit();
        if($schedule_type!='later') exit();

        $this->db->trans_start();
        $this->basic->delete_data("messenger_bot_broadcast_serial",array("id"=>$xid,"user_id"=>$this->user_id));

        $subscribers = [];
        $subscriber_info = $this->basic->get_data('messenger_bot_broadcast_serial_send',['where'=>["campaign_id"=>$xid,"user_id"=>$this->user_id]]);
        foreach($subscriber_info as $value)
            array_push($subscribers, $value['subscribe_id']);
        $this->db->where('page_table_id',$page_id);
        $this->db->where_in('subscriber_id',$subscribers);
        $otn_postback_ids = array();
        $otn_postback_ids = explode(',', $otn_postback_str);
        if(!empty($otn_postback_ids)) $this->db->where_in('otn_id',$otn_postback_ids);
        $this->db->update('otn_optin_subscriber',['is_sent'=>'0']);


        $this->basic->delete_data("messenger_bot_broadcast_serial_send",array("campaign_id"=>$xid,"user_id"=>$this->user_id));
        $this->_delete_usage_log(275,$total_thread);
        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        echo json_encode(array('status'=>'0','message'=>$this->lang->line('Something went wrong, please try again.')));
        else 
        {
            echo json_encode(array('status'=>'1','message'=>$this->lang->line('Campaign has been updated successfully.')));
            $this->session->set_flashdata('broadcast_success',1);
        }
    }
    /*-------------OTN BROADCASTING FUNCTIONS-----------*/
    /*==============================================*/




}
