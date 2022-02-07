<?php

require_once("application/controllers/Home.php"); // loading home controller
class Messenger_bot_analytics extends Home
{
    public function __construct()
    {
        parent::__construct();
        $this->member_validity();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location'); 
        
    }


    public function result($page_auto_id='0')
    {
        $from_date = $this->input->post('from_date',true);
        $to_date = $this->input->post('to_date',true);

        $error_message = '';
        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,"user_id"=>$this->user_id)));
 
        $page_access_token = isset($page_info[0]["page_access_token"]) ? $page_info[0]["page_access_token"] : "";

        $today = date("Y-m-d");
        if($from_date == '') $from_date = date("Y-m-d", strtotime("$today - 30 days"));
        if($to_date == '') $to_date = $today;

        $this->load->library('fb_rx_login');
        $analytics_data = $this->fb_rx_login->get_analytics_data($page_access_token,$from_date,$to_date);
       
        if(isset($analytics_data['error']) && $error_message=='')
        {
            // if(isset($analytics_data['error']['code'])) $error_message.="#".$analytics_data['error']['code']." - ";
            if(!isset($page_info[0])) $error_message .= "Page information not found. ";
            if(isset($analytics_data['error']['message'])) $error_message.=$analytics_data['error']['message'].". ";
            if(isset($analytics_data['error']['error_user_title'])) $error_message.=$analytics_data['error']['error_user_title'].". ";
            if(isset($analytics_data['error']['error_user_msg'])) $error_message.=$analytics_data['error']['error_user_msg'];
            $error_message = str_replace("..", '.', $error_message);
        }
      
        $week_date_array=array();
        $month_date_array=array();
        for($i=2; $i <=30 ; $i++) 
        { 
           $temp_date = date('Y-m-d', strtotime($today. " - $i days"));
           if($i<=7) array_push($week_date_array, $temp_date); 
           array_push($month_date_array, $temp_date); 
        }

        $page_messages_new_conversations_unique_temp =  array();
        $page_messages_active_threads_unique_temp = array();
        $page_messages_blocked_conversations_unique_temp = array();
        $page_messages_reported_conversations_unique_temp = array();
        $page_messages_reported_conversations_by_report_type_unique_temp = array();
        $page_messages_total_messaging_connections_temp = array();

        if(isset($analytics_data['data']))
        {
          foreach ($analytics_data['data'] as $key => $value) 
          {
            if($value['name']=='page_messages_new_conversations_unique') $page_messages_new_conversations_unique_temp = $value['values'];
            if($value['name']=='page_messages_active_threads_unique') $page_messages_active_threads_unique_temp = $value['values']; // deprecated
            if($value['name']=='page_messages_blocked_conversations_unique') $page_messages_blocked_conversations_unique_temp = $value['values'];
            if($value['name']=='page_messages_reported_conversations_unique') $page_messages_reported_conversations_unique_temp = $value['values'];
            if($value['name']=='page_messages_reported_conversations_by_report_type_unique') $page_messages_reported_conversations_by_report_type_unique_temp = $value['values']; // deprecated
            if($value['name']=='page_messages_total_messaging_connections') $page_messages_total_messaging_connections_temp = $value['values'];
          }
        }


        $page_messages_new_conversations_unique_summary=array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
        $page_messages_new_conversations_unique=array();        
        $i=0;
        foreach ($page_messages_new_conversations_unique_temp as $key => $value) 
        {
           $page_messages_new_conversations_unique[$i]['value'] = isset($value['value'])?$value['value']:0; 
           $page_messages_new_conversations_unique[$i]['date'] = date('Y-m-d',strtotime($value['end_time']));

           if($i==0) $page_messages_new_conversations_unique_summary['today']=$page_messages_new_conversations_unique_summary['today']+$page_messages_new_conversations_unique[$i]['value'];
           if(in_array($page_messages_new_conversations_unique[$i]['date'], $week_date_array)) $page_messages_new_conversations_unique_summary['week']=$page_messages_new_conversations_unique_summary['week']+$page_messages_new_conversations_unique[$i]['value'];
           if(in_array($page_messages_new_conversations_unique[$i]['date'], $month_date_array)) $page_messages_new_conversations_unique_summary['month']=$page_messages_new_conversations_unique_summary['month']+$page_messages_new_conversations_unique[$i]['value'];
           $page_messages_new_conversations_unique_summary['search']=$page_messages_new_conversations_unique_summary['search']+$page_messages_new_conversations_unique[$i]['value'];

           $i++;
        }

        $page_messages_active_threads_unique_summary=array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
        $page_messages_active_threads_unique=array();
        $i=0;
        foreach ($page_messages_active_threads_unique_temp as $key => $value) 
        {
           $page_messages_active_threads_unique[$i]['value'] = isset($value['value'])?$value['value']:0; 
           $page_messages_active_threads_unique[$i]['date'] = date('Y-m-d',strtotime($value['end_time']));

           if($i==0) $page_messages_active_threads_unique_summary['today']=$page_messages_active_threads_unique_summary['today']+$page_messages_active_threads_unique[$i]['value'];
           if(in_array($page_messages_active_threads_unique[$i]['date'], $week_date_array)) $page_messages_active_threads_unique_summary['week']=$page_messages_active_threads_unique_summary['week']+$page_messages_active_threads_unique[$i]['value'];
           if(in_array($page_messages_active_threads_unique[$i]['date'], $month_date_array)) $page_messages_active_threads_unique_summary['month']=$page_messages_active_threads_unique_summary['month']+$page_messages_active_threads_unique[$i]['value'];
           $page_messages_active_threads_unique_summary['search']=$page_messages_active_threads_unique_summary['search']+$page_messages_active_threads_unique[$i]['value'];

           $i++;
        }

        $page_messages_reported_vs_blocked_conversations = array();
        $page_messages_blocked_conversations_unique_summary=array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
        $page_messages_blocked_conversations_unique=array();        
        $i=0;
        foreach ($page_messages_blocked_conversations_unique_temp as $key => $value) 
        {
           $page_messages_blocked_conversations_unique[$i]['value'] = isset($value['value'])?$value['value']:0; 
           $page_messages_blocked_conversations_unique[$i]['date'] = date('Y-m-d',strtotime($value['end_time']));

           $page_messages_reported_vs_blocked_conversations[$i]['reported'] = 0;
           $page_messages_reported_vs_blocked_conversations[$i]['blocked'] = isset($value['value'])?$value['value']:0;
           $page_messages_reported_vs_blocked_conversations[$i]['date'] = date('Y-m-d',strtotime($value['end_time']));

           if($i==0) $page_messages_blocked_conversations_unique_summary['today']=$page_messages_blocked_conversations_unique_summary['today']+$page_messages_blocked_conversations_unique[$i]['value'];
           if(in_array($page_messages_blocked_conversations_unique[$i]['date'], $week_date_array)) $page_messages_blocked_conversations_unique_summary['week']=$page_messages_blocked_conversations_unique_summary['week']+$page_messages_blocked_conversations_unique[$i]['value'];
           if(in_array($page_messages_blocked_conversations_unique[$i]['date'], $month_date_array)) $page_messages_blocked_conversations_unique_summary['month']=$page_messages_blocked_conversations_unique_summary['month']+$page_messages_blocked_conversations_unique[$i]['value'];
           $page_messages_blocked_conversations_unique_summary['search']=$page_messages_blocked_conversations_unique_summary['search']+$page_messages_blocked_conversations_unique[$i]['value'];

           $i++;
        }

        
        $page_messages_reported_conversations_unique_summary=array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
        $page_messages_reported_conversations_unique=array();
        $i=0;
        foreach ($page_messages_reported_conversations_unique_temp as $key => $value) 
        {
           $page_messages_reported_conversations_unique[$i]['value'] = isset($value['value'])?$value['value']:0; 
           $page_messages_reported_conversations_unique[$i]['date'] = date('Y-m-d',strtotime($value['end_time']));

           if(isset($page_messages_reported_vs_blocked_conversations[$i]['reported']))
           $page_messages_reported_vs_blocked_conversations[$i]['reported'] = isset($value['value'])?$value['value']:0;

           if($i==0) $page_messages_reported_conversations_unique_summary['today']=$page_messages_reported_conversations_unique_summary['today']+$page_messages_reported_conversations_unique[$i]['value'];
           if(in_array($page_messages_reported_conversations_unique[$i]['date'], $week_date_array)) $page_messages_reported_conversations_unique_summary['week']=$page_messages_reported_conversations_unique_summary['week']+$page_messages_reported_conversations_unique[$i]['value'];
           if(in_array($page_messages_reported_conversations_unique[$i]['date'], $month_date_array)) $page_messages_reported_conversations_unique_summary['month']=$page_messages_reported_conversations_unique_summary['month']+$page_messages_reported_conversations_unique[$i]['value'];
           $page_messages_reported_conversations_unique_summary['search']=$page_messages_reported_conversations_unique_summary['search']+$page_messages_reported_conversations_unique[$i]['value'];

           $i++;
        }

        $page_messages_reported_conversations_by_report_type_unique=array();
        $total_spam=$total_inappropiate=$total_other=0;
        $i=0;
        foreach ($page_messages_reported_conversations_by_report_type_unique_temp as $key => $value) 
        {
           $page_messages_reported_conversations_by_report_type_unique[$i]['spam'] = isset($value['value']['spam'])?$value['value']['spam']:0; 
           $page_messages_reported_conversations_by_report_type_unique[$i]['inappropriate'] = isset($value['value']['inappropriate'])?$value['value']['inappropriate']:0; 
           $page_messages_reported_conversations_by_report_type_unique[$i]['other'] = isset($value['value']['other'])?$value['value']['other']:0; 
           $page_messages_reported_conversations_by_report_type_unique[$i]['date'] = date('Y-m-d',strtotime($value['end_time']));

           $total_spam=$total_spam+$page_messages_reported_conversations_by_report_type_unique[$i]['spam'];
           $total_inappropiate=$total_inappropiate+$page_messages_reported_conversations_by_report_type_unique[$i]['inappropriate'];
           $total_other=$total_other+$page_messages_reported_conversations_by_report_type_unique[$i]['other'];

           $i++;
        }


        $total_connections=0;
        $page_messages_total_messaging_connections=array();        
        $i=0;
        $num_rows = count($page_messages_total_messaging_connections_temp);
        foreach ($page_messages_total_messaging_connections_temp as $key => $value) 
        {
           $page_messages_total_messaging_connections[$i]['value'] = isset($value['value'])?$value['value']:0; 
           $page_messages_total_messaging_connections[$i]['date'] = date('Y-m-d',strtotime($value['end_time']));
           if($i==($num_rows-1)) $total_connections=$page_messages_total_messaging_connections[$i]['value'];
           $i++;
        }


        $page_messages_reported_conversations_by_report_type_pie = 
        array
        (
            0 => array
            (
                "value" => $total_spam,
                "color" => "#FF4D7B",
                "highlight" => "#FF4D7B",
                "label" => $this->lang->line('Spam'),
            ),
            1 => array
            (
                "value" => $total_inappropiate,
                "color" => "orange",
                "highlight" => "orange",
                "label" => $this->lang->line('Inappropriate'),
            ),
            2 => array
            (
                "value" => $total_other,
                "color" => "#144676",
                "highlight" => "#144676",
                "label" => $this->lang->line('Other'),
            )
        );

        $page_messages_reported_conversations_by_report_type_li = '';
        $page_messages_reported_conversations_by_report_type_li .= '<i class="fa fa-circle" style="color: #FF4D7B;"></i> '.$this->lang->line('Spam').' ';
        $page_messages_reported_conversations_by_report_type_li .= '<i class="fa fa-circle" style="color: orange;"></i> '.$this->lang->line('Inappropriate').' ';
        $page_messages_reported_conversations_by_report_type_li .= '<i class="fa fa-circle" style="color: #144676;"></i> '.$this->lang->line('Other').' ';



        $data['page_messages_new_conversations_unique'] = $page_messages_new_conversations_unique;
        //echo "<pre>";print_r($data['page_messages_new_conversations_unique']);exit;
        $data['page_messages_active_threads_unique'] = $page_messages_active_threads_unique;
        $data['page_messages_blocked_conversations_unique'] = $page_messages_blocked_conversations_unique;
        $data['page_messages_reported_conversations_unique'] = $page_messages_reported_conversations_unique;
        $data['page_messages_reported_conversations_by_report_type_unique'] = $page_messages_reported_conversations_by_report_type_unique;
        $data['page_messages_reported_conversations_by_report_type_pie'] = $page_messages_reported_conversations_by_report_type_pie;
        $data['page_messages_reported_conversations_by_report_type_li'] = $page_messages_reported_conversations_by_report_type_li;
        $data['page_messages_reported_vs_blocked_conversations']=$page_messages_reported_vs_blocked_conversations;
        $data['page_messages_total_messaging_connections'] = $page_messages_total_messaging_connections;

        $data['page_messages_new_conversations_unique_summary']=$page_messages_new_conversations_unique_summary;
        $data['page_messages_active_threads_unique_summary']=$page_messages_active_threads_unique_summary;
        $data['page_messages_blocked_conversations_unique_summary']=$page_messages_blocked_conversations_unique_summary;
        $data['page_messages_reported_conversations_unique_summary']=$page_messages_reported_conversations_unique_summary;

        $data['total_connections'] = $total_connections;
        $data['page_info'] = isset($page_info[0])?$page_info[0]:array();
        $data['error_message'] = $error_message;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        $data['body'] = 'messenger_tools/analytics';
        $page_name = isset($page_info[0]['page_name']) ? $page_info[0]['page_name'] : "";
        $data['page_title'] =$page_name.' - '.$this->lang->line('Analytics');
        $this->_viewcontroller($data);

        
    }

 


    

}