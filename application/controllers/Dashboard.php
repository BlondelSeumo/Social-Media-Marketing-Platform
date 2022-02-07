<?php

require_once("Home.php"); // including home controller

/**
* class config
* @category controller
*/
class Dashboard extends Home
{
    public $user_id;
    public $demo_mode;
    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');
        $this->user_id=$this->session->userdata('user_id');
    
        set_time_limit(0);
        $this->important_feature();
        $this->member_validity();   
        $this->demo_mode = false;
    }

    public function user($default_value='0')
    {
       return $this->index($default_value);
    }

    /**
    * load index method. redirect to config
    * @access public
    * @return void
    */
    
    public function index($default_value='0')
    {
        $this->is_broadcaster_exist=$this->broadcaster_exist(); 
        if($this->session->userdata('user_type') != 'Admin') $default_value='0';
        if($default_value == '0')
        {
            $user_id=$this->user_id;
            $data['other_dashboard'] = '0';
        }
        else
        {
            $user_id = $default_value;            
            $user_info = $this->basic->get_data('users',array('where'=>array('id'=>$user_id)));
            $data['user_name'] = isset($user_info[0]['name']) ? $user_info[0]['name'] : '';
            $data['user_email'] = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';    
            $data['other_dashboard'] = '1';
        }

        if($this->is_demo === '1' && $data['other_dashboard'] === '1')
        {
        	echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        // last 30 days subscribers
        $today = date("Y-m-d H:i:s");
        $days_30_before = date('Y-m-d H:i:s',strtotime("$today - 30 days"));


        $current_year = date("Y");
        $lastyear = $current_year-1;
        $current_month = date("Y-m");
        $current_date = date("Y-m-d");
        $current_time = date("Y-m-d H:i:s");
        $data['month_number'] = date('m');

        // get last twelve months (not used)
        $last_tweleve_month = array();
        $current_year_month = date('Y-m');
        for($i = 12; $i >= 1; $i--) {
            $year_month = date('Y-m', strtotime($current_year_month . " -$i month"));
            $last_tweleve_month[$year_month] = date("F Y",strtotime($year_month));
        }
        // get last twelve months (not used)
        $last_month = date('m', strtotime(date('m') . " -11 month"));
        $last_24_hour = date('Y-m-d H:i:s', strtotime($current_time . " -24 hour"));


        // first block item section 30 days
            $fbsub = $igsub = $esub = 0;
            $where = array(
                'where' => array(
                    // 'user_id' => $user_id,
                    'date_format(subscribed_at,"%Y-%m")' => $current_month,
                )
            );
            $where['where']['user_id'] = $user_id;
            $select = array('SUM(subscriber_type="system") as esub','SUM(CASE WHEN subscriber_type="messenger" AND social_media="fb" THEN 1 ELSE 0 END) as fbsub','SUM(CASE WHEN subscriber_type="messenger" AND social_media="ig" THEN 1 ELSE 0 END) as igsub');

            $subscriber_info = $this->basic->get_data('messenger_bot_subscriber',$where,$select);

            if(count($subscriber_info) == 1 || count($subscriber_info) > 1) {
                $fbsub = $subscriber_info[0]['fbsub'] ?? 0;
                $igsub = $subscriber_info[0]['igsub'] ?? 0;
                $esub = $subscriber_info[0]['esub'] ?? 0;
            }

            $total_sub = $fbsub + $igsub + $esub;

            // this section is for circle chart under different subscriber source
            if($this->demo_mode){
                $data['fsub_chart_data'] = 50;
                $data['igsub_chart_data'] = 60;
                $data['esub_chart_data'] = 70;
            }
            else{
                $data['fsub_chart_data'] = $total_sub > 0 ? round($fbsub / $total_sub):0;
                $data['igsub_chart_data'] = $total_sub > 0 ? round($igsub / $total_sub):0;
                $data['esub_chart_data'] = $total_sub > 0 ? round($esub / $total_sub):0;
            }
            // this section is for circle chart under different subscriber source

            $data['fbsub'] = $fbsub;
            $data['igsub'] = $igsub;
            $data['esub'] = $esub;
            $data['total_sub'] = $total_sub;
        // end of first block item section

        // start second block item section ( facebook page,ig account section )
            $total_pages = $this->basic->count_row('facebook_rx_fb_page_info',['where'=>['user_id'=>$user_id]]);
            $total_ig_account = $this->basic->count_row('facebook_rx_fb_page_info',['where'=>['user_id'=>$user_id,'has_instagram'=>'1']]);
            $number_of_bots = $this->basic->count_row('facebook_rx_fb_page_info',['where'=>['user_id'=>$user_id,'bot_enabled'=>'1']]);
            $number_of_bot_flow = $this->basic->count_row('visual_flow_builder_campaign',['where'=>['visual_flow_builder_campaign.user_id'=>$user_id]],'visual_flow_builder_campaign.id',['facebook_rx_fb_page_info'=>'visual_flow_builder_campaign.page_id=facebook_rx_fb_page_info.id,left']);
            $number_of_auto_comment_reply_fb = $this->basic->count_row('facebook_ex_autoreply',['where'=>['user_id'=>$user_id]]);
            $number_of_auto_comment_reply_ig = $this->basic->count_row('instagram_reply_autoreply',['where'=>['user_id'=>$user_id]]);
            $number_of_auto_comment_report_fb = $this->basic->count_row('facebook_ex_autoreply_report',['where'=>['user_id'=>$user_id,'reply_time >='=>$last_24_hour,'reply_time <='=>$current_time]]);
            $number_of_auto_comment_report_ig = $this->basic->count_row('instagram_autoreply_report',['where'=>['user_id'=>$user_id,'reply_time >='=>$last_24_hour,'reply_time <='=>$current_time]]);
            $number_of_bot_settings = $this->basic->count_row('messenger_bot',['where'=>['user_id'=>$user_id]]);

            $data['total_pages'] = $total_pages[0]['total_rows'];
            $data['total_ig_account'] = $total_ig_account[0]['total_rows'];
            $data['number_of_bots'] = $number_of_bots[0]['total_rows'];
            $data['number_of_bot_flow'] = $number_of_bot_flow[0]['total_rows'];
            $data['number_of_auto_comment_reply_campaign'] = $number_of_auto_comment_reply_fb[0]['total_rows']+$number_of_auto_comment_reply_ig[0]['total_rows'];
            $data['number_of_auto_comment_report_fb'] = $number_of_auto_comment_report_fb[0]['total_rows'];
            $data['number_of_auto_comment_report_ig'] = $number_of_auto_comment_report_ig[0]['total_rows'];
        // End second block item section (facebook page,ig account section)

        // Start Third block section (ecommerce statistics section)
            $this->db->select('currency');
            $this->db->distinct('currency');
            $this->db->from('ecommerce_config');
            $this->db->where('user_id',$user_id);
            $this->db->order_by('currency','desc');
            $store_currency = $this->db->get()->result_array();
            $store_currency = array_column($store_currency,'currency');
            $data['currency_lists'] = $store_currency;

            $selected_curr = '';
            if(!empty($store_currency))  {
                $curr = array_keys($store_currency)[0] ?? 'USD';
                $selected_curr = $store_currency[$curr];
            }

            if($this->session->userdata('ecommerce_dashboard_currency')) 
                $selected_curr = $this->session->userdata('ecommerce_dashboard_currency');

            $data['selected_curr'] = $selected_curr;
            $data['total_orders'] = $this->basic->count_row('ecommerce_cart',['where'=>['user_id'=>$user_id,'date_format(ordered_at,"%Y-%m")' => $current_month,'currency'=>$selected_curr]])[0]['total_rows'];

            // ecommerce earning block statistics
            $data['currency_icons'] = $this->currency_icon();
            $select_cart_data = ['id','user_id','store_id','action_type','last_completed_hour','status','payment_amount','currency','updated_at'];
            $cart_data_statistics = $this->basic->get_data('ecommerce_cart',['where'=>['user_id'=>$user_id,'currency'=>$selected_curr,'date_format(updated_at,"%Y-%m")' => $current_month]],$select_cart_data);

            $summary_checkout_cart = $summary_recovered_cart = $summary_earning = $summary_reminder_cart = 0;
            foreach ($cart_data_statistics as $value) {

              if($value['action_type']=='checkout') $summary_checkout_cart++;   

              if($value["last_completed_hour"]>0) {
                $summary_reminder_cart++;
                if($value['status']=='pending') $summary_recovered_cart++;          
              }

              if($value["status"]!='pending' && $value["status"]!='rejected') {
                $summary_earning += $value["payment_amount"];
              }
            }

            $data['summary_checkout_cart'] = $summary_checkout_cart;
            $data['summary_recovered_cart'] = $summary_recovered_cart;
            $data['summary_reminder_cart'] = $summary_reminder_cart;
            $data['summary_earning'] = $summary_earning;
        // end Third block section (ecommerce statistics third block)

        // Start Fourth section (ecommerce total earning graphical 30 days)
            $select_graph_data = array("ecommerce_cart.*","messenger_bot_subscriber.id as subscriber_table_id","first_name","last_name","full_name","profile_pic","email","image_path","subscriber_id","social_media");
            $join_graph_data = array('messenger_bot_subscriber'=>"messenger_bot_subscriber.subscribe_id=ecommerce_cart.subscriber_id,left");
            $cart_data_graph = $this->basic->get_data('ecommerce_cart',['where'=>['ecommerce_cart.user_id'=>$user_id,'ecommerce_cart.updated_at >='=>$days_30_before,'ecommerce_cart.updated_at <='=>$today,'currency'=>$selected_curr]],$select_graph_data,$join_graph_data);
            $earning_chart_labels = array();
            $fb_earning_chart_values = array();
            $ig_earning_chart_values = array();
            $web_earning_chart_values = array();
            $total_earning_chart_values = array();

            $graph_from_date = strtotime($days_30_before);
            $graph_to_date = strtotime($today);

            do 
            {
               $temp = date("Y-m-d",$graph_from_date);
               $temp2 = date("j M",$graph_from_date);;
               $fb_earning_chart_values[$temp] = 0;
               $ig_earning_chart_values[$temp] = 0;
               $web_earning_chart_values[$temp] = 0;
               $total_earning_chart_values[$temp] = 0;
               $earning_chart_labels[] = $temp2;
               $graph_from_date = strtotime('+1 day',$graph_from_date); 
            } 
            while ($graph_from_date <= $graph_to_date);

            $graph_summary_checkout_cart = $graph_summary_recovered_cart = $graph_summary_earning = $graph_summary_reminder_cart = 0;
            foreach ($cart_data_graph as $values) {

                if($values['action_type']=='checkout') $graph_summary_checkout_cart++;   

                if($values["last_completed_hour"]>0) {
                $graph_summary_reminder_cart++;
                if($values['action_type']=='checkout') $graph_summary_recovered_cart++;          
                }

                $updated_at_formatted = date("Y-m-d",strtotime($values['updated_at']));

                if($values["status"]!='pending' && $values["status"]!='rejected')
                {
                    $graph_summary_earning += $values["payment_amount"];
                    if(strpos($values['subscriber_id'],"sys-") === FALSE && strpos($values['subscriber_id'],"sys-guest-") === FALSE) {
                        if($values['social_media'] == 'fb') {
                            $fb_earning_chart_values[$updated_at_formatted] += $values["payment_amount"];
                        } else if($values['social_media'] == 'ig') {
                            $ig_earning_chart_values[$updated_at_formatted] += $values["payment_amount"];
                        }
                    } else if(strpos($values['subscriber_id'],"sys-") !== FALSE || strpos($values['subscriber_id'],"sys-guest-") !== FALSE) {
                        $web_earning_chart_values[$updated_at_formatted] += $values["payment_amount"];
                    } 
                    $total_earning_chart_values[$updated_at_formatted] += $values["payment_amount"];
                }
            }

            if($this->demo_mode){
                $fb_earning_chart_values = $this->demo_data('fb');
                $ig_earning_chart_values = $this->demo_data('ig');
                $web_earning_chart_values = $this->demo_data('ec');
                $total_earning_chart_values = $this->demo_data('fb');
            }

            $largest_values = array();
            $max_value = 1;
            if(!empty($fb_earning_chart_values)) array_push($largest_values, max($fb_earning_chart_values));
            if(!empty($ig_earning_chart_values)) array_push($largest_values, max($ig_earning_chart_values));
            if(!empty($web_earning_chart_values)) array_push($largest_values, max($web_earning_chart_values));
            if(!empty($largest_values)) $max_value = max($largest_values);
            if($max_value > 10) $data['step_size'] = floor($max_value/10);
            else $data['step_size'] = 1;

            $data['graph_summary_checkout_cart'] = $graph_summary_checkout_cart;
            $data['graph_summary_recovered_cart'] = $graph_summary_recovered_cart;
            $data['graph_summary_reminder_cart'] = $graph_summary_reminder_cart;
            $data['graph_summary_earning'] = $graph_summary_earning;
            $data['earning_chart_labels'] = $earning_chart_labels;
            $data['fb_earning_chart_values'] = $fb_earning_chart_values;
            $data['ig_earning_chart_values'] = $ig_earning_chart_values;
            $data['web_earning_chart_values'] = $web_earning_chart_values;
            $data['total_earning_chart_values'] = $total_earning_chart_values;
            $data['cart_data_graph'] = $cart_data_graph;

            $select_top_product = ["ecommerce_product.product_name","ecommerce_product.thumbnail","ecommerce_product.woocommerce_product_id","sum(quantity) as sales_count","ecommerce_cart_item.*"];
            $join_cart_items = ['ecommerce_product'=>'ecommerce_cart_item.product_id=ecommerce_product.id,left'];
            $data['top_products'] = $this->basic->get_data("ecommerce_cart_item",array("where"=>array("user_id"=>$user_id,"ecommerce_cart_item.updated_at >="=>$days_30_before,"ecommerce_cart_item.updated_at <="=>$today)),$select_top_product,$join_cart_items,$limit='10',$start=NULL,$order_by='sales_count desc',$group_by='ecommerce_cart_item.product_id');
        // end Fourth Section (ecommerce total Earning Graphical)

        // Fifth Item section (Subscribers combined chart section 12 months)
            $month_name_array = array(
                '12' => 'December',
                '11' => 'November',
                '10' => 'October',
                '9' => 'September',
                '8' => 'August',
                '7' => 'July',
                '6' => 'June',
                '5' => 'May',
                '4' => 'April',
                '3' => 'March',
                '2' => 'February',
                '1' => 'January',
            );
            $data['last_tweleve_month'] = $month_name_array;

            $total_subscribers = [];
            $email_subscribers = [];
            $phone_subscribers = [];

            $where_subscribers = ['where'=>['user_id'=>$user_id]];
            $select_subscribers = ['COUNT(id) as subscriber_gain','SUM(email != "") as email_gain','SUM(phone_number != "") as phone_gain','month(subscribed_at) as month_number'];
            $this->db->where('subscribed_at >= DATE_SUB(NOW(),INTERVAL 11 MONTH)');
            $this->db->where('is_bot_subscriber ="1"');
            $subscribers = $this->basic->get_data("messenger_bot_subscriber",$where_subscribers,$select_subscribers,'','','','month(subscribed_at) ASC','month(subscribed_at)');

            $data['subscribers_gain'] = $this->demo_mode ? 891: array_sum(array_column($subscribers,'subscriber_gain'));
            $data['email_gain'] = $this->demo_mode ? 343 : array_sum(array_column($subscribers,'email_gain'));
            $data['phone_gain'] = $this->demo_mode ? 159 : array_sum(array_column($subscribers,'phone_gain'));
            
            
            $month_keys = array_keys($month_name_array);
            $month_keys2 = array_column($subscribers, 'month_number');
            $month_diff = array_diff($month_keys, $month_keys2);

            if(!empty($subscribers) && count($month_diff)) {
                foreach($month_diff as $month) {
                    $missing_month = [
                        'subscriber_gain' => 0,
                        'email_gain' => 0,
                        'phone_gain' => 0,
                        'month_number' => $month
                    ];
                    array_push($subscribers, $missing_month);
                }
            }

            usort($subscribers, function($a, $b) {
                if ($a['month_number'] == $b['month_number']) {
                    return 0;
                }

                return ($a['month_number'] < $b['month_number']) ? 1 : -1;
            });


            foreach ($subscribers as $value) {
                $total_subscribers[$value['month_number']] = $value['subscriber_gain'];
                $email_subscribers[$value['month_number']] = $value['email_gain'];
                $phone_subscribers[$value['month_number']] = $value['phone_gain'];
            }


            if($this->demo_mode) {
                $total_subscribers = $this->subscribers_demo_data("all");
                $email_subscribers = $this->subscribers_demo_data("email");
                $phone_subscribers = $this->subscribers_demo_data("phone");
            }

            $large_val = array();
            $max_values = 1;
            if(!empty($total_subscribers)) array_push($large_val, max($total_subscribers));
            if(!empty($email_subscribers)) array_push($large_val, max($email_subscribers));
            if(!empty($phone_subscribers)) array_push($large_val, max($phone_subscribers));
            if(!empty($large_val)) $max_values = max($large_val);
            if($max_values > 10) $data['stepSize'] = floor($max_values/10);
            else $data['stepSize'] = 1;


            $data['total_subscribers'] = $total_subscribers;
            $data['email_subscribers'] = $email_subscribers;
            $data['phone_subscribers'] = $phone_subscribers;            
        // Fifth Item section (Subscribers Combined Chart Section 12 months End)

        // sixth item section [latest subscribers]
            $page_list = array();
            $latest_subscriber_list = array();
            $where = array(
                'where' => array(
                    'permission' => '1',
                    'is_bot_subscriber' => '1',
                    'social_media'=>'fb'
                )
            );
            $where['where']['user_id'] = $user_id;
            $latest_subscriber_info = $this->basic->get_data('messenger_bot_subscriber',$where,'','',10,'','subscribed_at desc');

            $where = array();
            $where['where']['user_id'] = $user_id;
            $page_info = $this->basic->get_data('facebook_rx_fb_page_info',$where,array('id','page_name','page_id'));
            foreach($page_info as $value)
            {
                $page_list[$value['id']]['page_name'] = $value['page_name'];
                $page_list[$value['id']]['page_id'] = $value['page_id'];
            }
            $i=0;
            foreach($latest_subscriber_info as $value)
            {
                $latest_subscriber_list[$i]['first_name'] = $value['first_name'];
                $latest_subscriber_list[$i]['last_name'] = $value['last_name'];
                $latest_subscriber_list[$i]['full_name'] = $value['full_name'];
                if($value['link'] == '')
                    $latest_subscriber_list[$i]['link'] = 'disabled';
                else
                    $latest_subscriber_list[$i]['link'] = $value['link'];

                $latest_subscriber_list[$i]['subscribed_at'] = date_time_calculator($value['subscribed_at'],true);
                $latest_subscriber_list[$i]['subscribe_id'] = $value['subscribe_id'];
                $latest_subscriber_list[$i]['page_name'] = $page_list[$value['page_table_id']]['page_name'];
                $latest_subscriber_list[$i]['page_id'] = $page_list[$value['page_table_id']]['page_id'];

                $profile_pic = ($value['profile_pic']!="") ? $value["profile_pic"] :  base_url('assets/img/avatar/avatar-1.png');
                $latest_subscriber_list[$i]['image_path']=($value["image_path"]!="") ? base_url($value["image_path"]) : $profile_pic;

                $i++;
            }
            $data['latest_subscriber_list'] = $latest_subscriber_list;
        // end sixth item section [latest subscribers]

        // seventh + eight item section [Different sources of subscribers 12 months]
            $refferer_source_info = array();
            $refferer_source_info['checkbox_plugin']['title'] = $this->lang->line("Checkbox Plugin");
            $refferer_source_info['customer_chat_plugin']['title'] = $this->lang->line("Customer Chat Plugin");
            $refferer_source_info['sent_to_messenger']['title'] = $this->lang->line("Sent to Messenger Plugin");
            $refferer_source_info['me_link']['title'] = $this->lang->line("m.me Link");
            $refferer_source_info['direct']['title'] = $this->lang->line("Direct From Facebook");
            $refferer_source_info['comment_private_reply']['title'] = $this->lang->line("Comment Private Reply");

            $where = array();
            $where['where']['user_id'] = $user_id;
            $select = array(
                'count(id) as subscribers',
                'SUM(refferer_source="checkbox_plugin") as checkbox_plugin',
                'SUM(refferer_source="CUSTOMER_CHAT_PLUGIN") as customer_chat_plugin',
                'SUM(refferer_source="SEND-TO-MESSENGER-PLUGIN") as sent_to_messenger',
                'SUM(refferer_source="SHORTLINK") as me_link',
                'SUM(refferer_source="FB PAGE" || refferer_source="") as fb_page',
                'SUM(refferer_source="COMMENT PRIVATE REPLY") as comment_private_reply',
                'refferer_source'
            );
            $this->db->where('subscribed_at >= DATE_SUB(NOW(),INTERVAL 11 MONTH)');
            $this->db->where('is_bot_subscriber ="1"');
            $subscriber_refferer_info = $this->basic->get_data('messenger_bot_subscriber',$where,$select,'','','','','refferer_source');

            if($this->demo_mode) {
                $refferer_source_info['checkbox_plugin']['subscribers'] = 100;
                $refferer_source_info['customer_chat_plugin']['subscribers'] =  100;
                $refferer_source_info['sent_to_messenger']['subscribers'] =  100;
                $refferer_source_info['me_link']['subscribers'] =  100;
                $refferer_source_info['direct']['subscribers'] =  100;
                $refferer_source_info['comment_private_reply']['subscribers'] =  100;

            } else {

                $subscribers = 0;
                $checkbox_plugin = 0;
                $customer_chat_plugin = 0;
                $sent_to_messenger = 0;
                $me_link = 0;
                $direct = 0;
                $comment_private_reply = 0;
                foreach($subscriber_refferer_info as $key=>$value){
                    if($value['refferer_source']=='checkbox_plugin') $checkbox_plugin+=$value['checkbox_plugin'];
                    else if($value['refferer_source']=='CUSTOMER_CHAT_PLUGIN') $customer_chat_plugin+=$value['customer_chat_plugin'];
                    else if($value['refferer_source']=='SEND-TO-MESSENGER-PLUGIN') $sent_to_messenger+=$value['sent_to_messenger'];
                    else if($value['refferer_source']=='SHORTLINK') $me_link+=$value['me_link'];
                    else if($value['refferer_source']=='COMMENT PRIVATE REPLY') $comment_private_reply+=$value['comment_private_reply'];
                    else $direct+=$value['fb_page'];

                    $subscribers+=$value['subscribers'];
                }

                $refferer_source_info['checkbox_plugin']['subscribers'] = $checkbox_plugin;
                $refferer_source_info['customer_chat_plugin']['subscribers'] = $customer_chat_plugin;
                $refferer_source_info['sent_to_messenger']['subscribers'] = $sent_to_messenger;
                $refferer_source_info['me_link']['subscribers'] = $me_link;
                $refferer_source_info['direct']['subscribers'] = $direct;
                $refferer_source_info['comment_private_reply']['subscribers'] = $comment_private_reply;
            }
            $data['refferer_source_info'] = $refferer_source_info;
        // end of seventh + eight item section [top sources of subscribers 12 months]

        // Eighth section start
            $subscribers_source_info = array(
                $this->lang->line('Checkbox') => $refferer_source_info['checkbox_plugin']['subscribers'],        
                $this->lang->line('Customer Chat') => $refferer_source_info['customer_chat_plugin']['subscribers'],     
                $this->lang->line('Sent to Messenger') => $refferer_source_info['sent_to_messenger']['subscribers'],        
                $this->lang->line('m.me') => $refferer_source_info['me_link']['subscribers'],        
                $this->lang->line('Direct') => $refferer_source_info['direct']['subscribers'],        
                $this->lang->line('Comment Reply') => $refferer_source_info['comment_private_reply']['subscribers'],      
            );
            $data['subscribers_source_info'] = $subscribers_source_info;
        // eighth section end

        // Ninth Section (Email and SMS campaigns overviews 30 days)
            // email section
            $total_email_campaigns = $completed_email_campaigns = $pending_email_campaigns = $processing_email_campaigns = $email_campaigns_total_thread = $email_sent_successfully = 0;
            $select_from_email_lists = array(
                'COUNT(id) as total_email_campaigns',
                'SUM(posting_status="0") as pending_email_campaigns',
                'SUM(posting_status="1") as processing_email_campaigns',
                'SUM(posting_status="2") as completed_email_campaigns',
                'SUM(successfully_sent) as emai_successfully_sent',
                'SUM(total_thread) as total_emailCampaign_thread',
            );
            $where_email_lists = array(
                'where'=>array(
                    'user_id'=>$user_id,
                    'created_at >='=>$days_30_before,
                    'created_at <='=>$today
                )
            );
            $email_campaigns = $this->basic->get_data("email_sending_campaign",$where_email_lists,$select_from_email_lists);
            
            if(count($email_campaigns) == 1 || count($email_campaigns) > 1) {
                $total_email_campaigns = $email_campaigns[0]['total_email_campaigns'] ?? 0;
                $pending_email_campaigns = $email_campaigns[0]['pending_email_campaigns'] ?? 0;
                $processing_email_campaigns = $email_campaigns[0]['processing_email_campaigns'] ?? 0;
                $completed_email_campaigns = $email_campaigns[0]['completed_email_campaigns'] ?? 0;
                $email_sent_successfully = $email_campaigns[0]['emai_successfully_sent'] ?? 0;
                $email_campaigns_total_thread = $email_campaigns[0]['total_emailCampaign_thread'] ?? 0;
            }

            $email_campaign_percent = $total_email_campaigns > 0 ? round($total_email_campaigns - $completed_email_campaigns): 0;
            $email_to_be_sent = $email_campaigns_total_thread > 0 ? $email_campaigns_total_thread - $email_sent_successfully:0;

            $data['email_campaigns'] = $email_campaign_percent;
            $data['total_threads'] = $email_campaigns_total_thread;
            $data['total_sent'] = $email_sent_successfully;
            $data['remaining_threads'] = $email_to_be_sent;
            $data['total_email_campaign'] = $total_email_campaigns;
            $data['total_sent'] = $email_sent_successfully;
            $data['email_campaign_chart_labels'] = [$this->lang->line('pending'),$this->lang->line('processing'),$this->lang->line('completed')];
            $data['email_campaign_chart_data'] = [$pending_email_campaigns,$processing_email_campaigns,$completed_email_campaigns];

            // sms section
            $total_sms_campaigns = $completed_sms_campaigns = $pending_sms_campaigns = $processing_sms_campaigns = $sms_successfully_sent = $sms_campaigns_total_thread = 0;
            $select_from_sms_lists = array(
                'COUNT(id) as total_sms_campaigns',
                'SUM(posting_status="0") as pending_sms_campaigns',
                'SUM(posting_status="1") as processing_sms_campaigns',
                'SUM(posting_status="2") as completed_sms_campaigns',
                'SUM(successfully_sent) as sms_successfully_sent',
                'SUM(total_thread) as total_smsCampaign_thread',
            );
            $where_sms_lists = array(
                'where'=>array(
                    'user_id'=>$user_id,
                    'created_at >='=>$days_30_before,
                    'created_at <='=>$today
                )
            );
            $sms_campaigns = $this->basic->get_data("sms_sending_campaign",$where_sms_lists,$select_from_sms_lists);

            if(count($sms_campaigns) == 1 || count($sms_campaigns) > 1) {
                $total_sms_campaigns = $sms_campaigns[0]['total_sms_campaigns'] ?? 0;
                $pending_sms_campaigns = $sms_campaigns[0]['pending_sms_campaigns'] ?? 0;
                $processing_sms_campaigns = $sms_campaigns[0]['processing_sms_campaigns'] ?? 0;
                $completed_sms_campaigns = $sms_campaigns[0]['completed_sms_campaigns'] ?? 0;
                $sms_successfully_sent = $sms_campaigns[0]['sms_successfully_sent'] ?? 0;
                $sms_campaigns_total_thread = $sms_campaigns[0]['total_smsCampaign_thread'] ?? 0;
            }

            $sms_campaign_percent = $total_sms_campaigns > 0 ? round($total_sms_campaigns - $completed_sms_campaigns) : 0;
            $sms_to_be_sent = $sms_campaigns_total_thread > 0 ? round($sms_campaigns_total_thread - $sms_successfully_sent):0;
            $data['sms_campaigns'] = $sms_campaign_percent;

            $data['sms_campaign_chart_labels'] = [$this->lang->line('pending'),$this->lang->line('processing'),$this->lang->line('completed')];
            $data['sms_campaign_chart_data'] = [$pending_sms_campaigns,$processing_sms_campaigns,$completed_sms_campaigns];
            $data['total_threads2'] = $sms_campaigns_total_thread;
            $data['total_sent2'] = $sms_successfully_sent;
            $data['remaining_threads2'] = $sms_to_be_sent;

        // Ninth Section End  (Email and SMS campaigns overviews 30 days)   

        $data['body'] = 'dashboard/dashboard';
        $data['page_title'] = $this->lang->line('Dashboard');
        $this->_viewcontroller($data);
    }
  

    public function get_first_div_content()
    {
        $this->ajax_check();

        $this->is_broadcaster_exist=$this->broadcaster_exist(); 
        $month_no = $this->input->post('month_no',true);
        if($month_no == 'year') $search_year = date("Y");
        else $search_month = date("Y-{$month_no}");

        $user_id = $this->user_id;
        $user_id_url = $this->input->post('user_id_url',true);

        if(!empty($user_id_url) && $user_id_url>0 && $this->session->userdata('user_type')=='Admin') $user_id = $user_id_url;

        // first item section
        $where_simple = array();
        $where_simple['user_id'] = $user_id;
        $where_simple['permission'] = '1';
        // $where_simple['social_media'] = 'fb';
        if($month_no == 'year') $where_simple['date_format(subscribed_at,"%Y")'] = $search_year;
        else $where_simple['date_format(subscribed_at,"%Y-%m")'] = $search_month;

        $where = array('where' => $where_simple);

        $fbsub = $igsub = $esub = 0;
        $select = array('SUM(subscriber_type="system") as esub','SUM(CASE WHEN subscriber_type="messenger" AND social_media="fb" THEN 1 ELSE 0 END) as fbsub','SUM(CASE WHEN subscriber_type="messenger" AND social_media="ig" THEN 1 ELSE 0 END) as igsub');

        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber',$where,$select);

        if(count($subscriber_info) == 1 || count($subscriber_info) > 1) {
            $fbsub = $subscriber_info[0]['fbsub'] ?? 0;
            $igsub = $subscriber_info[0]['igsub'] ?? 0;
            $esub = $subscriber_info[0]['esub'] ?? 0;
        }
        $total_sub = $fbsub + $igsub + $esub;

        $data['fbsub'] = $fbsub;
        $data['igsub'] = $igsub;
        $data['esub'] = $esub;
        $data['total_sub'] = $fbsub + $igsub + $esub;

        // end of first item section
        echo json_encode($data,true);
    }

    /**
    * load index method. redirect to config
    * @access public
    * @return void
    */

    public function set_currency()
    {
        $this->ajax_check();
        $currency = $this->input->post('item_name',true);
        $this->session->set_userdata('ecommerce_dashboard_currency',$currency);
        echo '1';
    }


    public function get_ecommerce_div_content()
    {
        $this->ajax_check();

        // ecommerce statistics section (third block)
        $user_id = $this->user_id;
        $user_id_url = $this->input->post('user_id_url',true);

        if(!empty($user_id_url) && $user_id_url>0 && $this->session->userdata('user_type')=='Admin') $user_id = $user_id_url;

        $this->db->select('currency');
        $this->db->distinct('currency');
        $this->db->from('ecommerce_config');
        $this->db->where('user_id',$user_id);
        $this->db->order_by('currency','desc');
        $store_currency = $this->db->get()->result_array();
        $store_currency = array_column($store_currency,'currency');
        $data['currency_lists'] = $store_currency;

        $selected_curr = '';
        if(!empty($store_currency))  {
            // $curr = array_key_first($store_currency);
            $curr = array_keys($store_currency)[0] ?? "USD";
            $selected_curr = $store_currency[$curr];
        }

        if($this->session->userdata('ecommerce_dashboard_currency')) 
            $selected_curr = $this->session->userdata('ecommerce_dashboard_currency');

        $month_no = $this->input->post('month_no',true);
        if($month_no == 'year') $search_year = date("Y");
        else $search_month = date("Y-{$month_no}");

        $where_simple = array();
        $where_simple['user_id'] = $user_id;
        $where_simple['currency'] = $selected_curr;
        if($month_no == 'year')
            $where_simple['date_format(updated_at,"%Y")'] = $search_year;
        else
            $where_simple['date_format(updated_at,"%Y-%m")'] = $search_month;

        $where = array(
            'where' => $where_simple
        );

        $data['selected_curr'] = $selected_curr;
        $data['total_orders'] = $this->basic->count_row('ecommerce_cart',$where)[0]['total_rows'];

        // ecommerce earning block statistics
        $data['currency_icons'] = $this->currency_icon();
        $select_cart_data = ['ecommerce_cart.id','ecommerce_cart.user_id','ecommerce_cart.store_id','action_type','last_completed_hour','ecommerce_cart.status','payment_amount','currency','updated_at'];
        $cart_data_statistics = $this->basic->get_data('ecommerce_cart',$where,$select_cart_data);
        
        $summary_checkout_cart = $summary_recovered_cart = $summary_earning = $summary_reminder_cart = 0;
        foreach ($cart_data_statistics as $value) {

          if($value['action_type']=='checkout') $summary_checkout_cart++;   

          if($value["last_completed_hour"]>0) {
            $summary_reminder_cart++;
            if($value['action_type']=='checkout') $summary_recovered_cart++;          
          }

          if($value["status"]!='pending' && $value["status"]!='rejected') {
            $summary_earning += $value["payment_amount"];
          }
        }

        $data['summary_checkout_cart'] = $summary_checkout_cart;
        $data['summary_recovered_cart'] = $summary_recovered_cart;
        $data['summary_reminder_cart'] = $summary_reminder_cart;
        $data['summary_earning'] = $summary_earning;

        echo json_encode($data,true);
    }

    public function subscribers_demo_data($type="all")
    {
        $rand = 1000;
        if($type=='email') $rand = 1000;
        if($type=='phone') $rand = 1000;

        return array(
            '1' => rand(0,$rand),
            '2' => rand(0,$rand),
            '3' => rand(0,$rand),
            '4' => rand(0,$rand),
            '5' => rand(0,$rand),
            '6' => rand(0,$rand),
            '7' => rand(0,$rand),
            '8' => rand(0,$rand),
            '9' => rand(0,$rand),
            '10' => rand(0,$rand),
            '11' => rand(0,$rand),
            '12' => rand(0,$rand),
        );
    }

    public function demo_data($type='ec')
    {
        $rand = 1000;
        if($type=='fb') $rand = 1000;
        if($type=='ig') $rand = 1000;
        return  array ( 
            '2021-11-15' => rand(0,$rand), 
            '2021-11-16' => rand(0,$rand), 
            '2021-11-17' => rand(0,$rand), 
            '2021-11-18' => rand(0,$rand), 
            '2021-11-19' => rand(0,$rand), 
            '2021-11-20' => rand(0,$rand), 
            '2021-11-21' => rand(0,$rand), 
            '2021-11-22' => rand(0,$rand), 
            '2021-11-23' => rand(0,$rand), 
            '2021-11-24' => rand(0,$rand), 
            '2021-11-25' => rand(0,$rand), 
            '2021-11-26' => rand(0,$rand), 
            '2021-11-27' => rand(0,$rand), 
            '2021-11-28' => rand(0,$rand), 
            '2021-11-29' => rand(0,$rand), 
            '2021-11-30' => rand(0,$rand), 
            '2021-12-01' => rand(0,$rand), 
            '2021-12-02' => rand(0,$rand), 
            '2021-12-03' => rand(0,$rand), 
            '2021-12-04' => rand(0,$rand), 
            '2021-12-05' => rand(0,$rand), 
            '2021-12-06' => rand(0,$rand), 
            '2021-12-07' => rand(0,$rand), 
            '2021-12-08' => rand(0,$rand), 
            '2021-12-09' => rand(0,$rand), 
            '2021-12-10' => rand(0,$rand), 
            '2021-12-11' => rand(0,$rand), 
            '2021-12-12' => rand(0,$rand), 
            '2021-12-13' => rand(0,$rand), 
            '2021-12-14' => rand(0,$rand), 
            '2021-12-15' => rand(0,$rand)
        );
    }



 
}