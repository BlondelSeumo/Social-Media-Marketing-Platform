<?php
/* 
Addon Name: Comboposter
Unique Name: comboposter
Module ID: 220
Project ID: 19
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 1.0
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

require_once("application/modules/comboposter/controllers/Login_callback_handler.php");
require_once("application/modules/comboposter/controllers/Existing_social_accounts_handler.php");
require_once("application/modules/comboposter/controllers/Text_post_handler.php");
require_once("application/modules/comboposter/controllers/Image_post_handler.php");
require_once("application/modules/comboposter/controllers/Video_post_handler.php");
require_once("application/modules/comboposter/controllers/Link_post_handler.php");
require_once("application/modules/comboposter/controllers/Html_post_handler.php");

class Comboposter extends Home
{
    public $addon_data=array();
    public function __construct()
    {
        parent::__construct();

        $function_name=$this->uri->segment(2);
        if($function_name!="post_to_all_media") 
        {
            if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');         
            $this->member_validity();
        }

        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path);
        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it 
    }


    public function index()
    {
        $this->social_accounts();
    }

    public function social_accounts()
    {
        /* load libraries */
        $this->load->library('Google_youtube_login', NULL, 'google');
        $this->load->library('Twitter');
        // $this->load->library('Tumblr');
        $this->load->library('Linkedin');
        $this->load->library('Medium');
        $this->load->library('Pinterests', NULL, 'pinterest');
        $this->load->library('Reddit');
        $this->load->library('Wp_org_poster', NULL, 'wordpress');
        $this->load->library('Wordpress_self_hosted', NULL, 'wordpress_self_hosted');


        /* get login buttons */
        $redirect_url = base_url('comboposter/login_callback');

        $data['youtube_login_button'] = $this->google->youtube_login_button($redirect_url. '/youtube');
        $data['blogger_login_button'] = $this->google->blogger_login_button($redirect_url. '/blogger');
        $data['twitter_login_button'] = $this->twitter->login_button($redirect_url. '/twitter');
        // $data['tumblr_login_button'] = $this->tumblr->login_button($redirect_url. '/tumblr');
        $data['linkedin_login_button'] = $this->linkedin->login_button($redirect_url. '/linkedin');
        // $data['medium_login_button'] = $this->medium->login_button($redirect_url. '/medium');
        $data['reddit_login_button'] = $this->reddit->login_button($redirect_url. '/reddit');
        $data['wordpress_login_button'] = $this->wordpress->login_button($redirect_url. '/wordpress');
        $data['wordpress_self_hosted_login_button'] = $this->wordpress_self_hosted->login_button();
        $data['pinterest_login_button'] = "<a href='". base_url('social_apps/pinterest_settings') ."' class='btn btn-outline-primary login_button' social_account='pinterest'><i class='fas fa-plus-circle'></i> ".$this->lang->line("Import Account")."</a>";;


        /**
         * get social accounts lists
         */
        $data['twitter_account_list'] = $this->basic->get_data('twitter_users_info', array('where' => array('user_id' => $this->user_id)));
        // $data['tumblr_account_list'] = $this->basic->get_data('tumblr_users_info', array('where' => array('user_id' => $this->user_id)));
        $data['linkedin_account_list'] = $this->basic->get_data('linkedin_users_info', array('where' => array('user_id' => $this->user_id)));

        $data['medium_account_list'] = $this->basic->get_data('medium_users_info', array('where' => array('user_id' => $this->user_id)));

        $data['reddit_account_list'] = $this->basic->get_data('reddit_users_info', array('where' => array('user_id' => $this->user_id)));
        $data['youtube_channel_list'] = $this->basic->get_data('youtube_channel_list', array('where' => array('user_id' => $this->user_id)));
        $data['blogger_account_list'] = $this->basic->get_data('blogger_users_info', array('where' => array('user_id' => $this->user_id)));
        $data['wordpress_account_list'] = $this->basic->get_data('wordpress_users_info', array('where' => array('user_id' => $this->user_id)));
        $data['wordpress_account_list_self_hosted'] = $this->basic->get_data('wordpress_config_self_hosted', array('where' => array('user_id' => $this->user_id)));
        $data['pinterest_account_list'] = $this->basic->get_data('pinterest_users_info', array('where' => array('user_id' => $this->user_id)));
        // echo "<pre>";print_r($data['pinterest_account_list']);
        

        $data['page_title'] = $this->lang->line('Social Accounts');
        $data['title'] = $this->lang->line('Social Accounts');
        $data['body'] = 'social_account_list';

        $this->_viewcontroller($data);
    }

    public function delete_social_account()
    {
        $this->ajax_check();

        $social_media = $this->input->post('social_media', true);
        $table_id = $this->input->post('table_id', true);

        $response = array();
        $response['status'] = 'success';
        $response['message'] = $this->lang->line("Your account has deleted successfully.");

        if ($social_media == 'twitter') {

            $this->basic->delete_data('twitter_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 102, $request = 1);
        } 
        // else if ($social_media == 'tumblr') {

        //     $this->basic->delete_data('tumblr_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
        //     $this->_delete_usage_log($module_id = 102, $request = 1);
        // }
         else if ($social_media == 'youtube') {

            $account_info = $this->basic->get_data('youtube_channel_info', array('where' => array('user_id' => $this->user_id, 'id' => $table_id)), array('channel_id'));
            if (count($account_info) > 0) {

                $channel_id = $account_info[0]['channel_id'];

                $this->basic->delete_data('youtube_channel_info', array('user_id' => $this->user_id, 'id' => $table_id));
                $this->basic->delete_data('youtube_channel_list', array('user_id' => $this->user_id, 'channel_id' => $channel_id));
                $this->basic->delete_data('youtube_video_list', array('user_id' => $this->user_id, 'channel_id' => $channel_id));

                $this->_delete_usage_log($module_id = 33, $request = 1);
            }
        } else if ($social_media == 'linkedin') {

            $this->basic->delete_data('linkedin_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 103, $request = 1);

        } else if ($social_media == 'medium') {

            $this->basic->delete_data('medium_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 277, $request = 1);
            
        } else if ($social_media == 'reddit') {

            $this->basic->delete_data('reddit_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 105, $request = 1);

        } else if ($social_media == 'pinterest') {

            $this->basic->delete_data('pinterest_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->basic->delete_data('pinterest_board_info', array('user_id' => $this->user_id, 'pinterest_table_id' => $table_id));

            $this->_delete_usage_log($module_id = 101, $request = 1);
        } else if ($social_media == 'blogger') {

            $this->basic->delete_data('blogger_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->basic->delete_data('blogger_blog_info', array('user_id' => $this->user_id, 'blogger_users_info_table_id' => $table_id));

            $this->_delete_usage_log($module_id = 107, $request = 1);
        } else if ($social_media == 'wordpress') {

            $this->basic->delete_data('wordpress_users_info', array('user_id' => $this->user_id, 'id' => $table_id));
            $this->_delete_usage_log($module_id = 108, $request = 1);

        }

        echo json_encode($response);
    }


    public function set_empty_app_error($social_media)
    {

        $this->session->set_userdata('account_import_error', $this->lang->line("Perhaps admin has not set corresponding apps information yet."));
        redirect(base_url('comboposter/social_accounts'), 'location');
    }

    public function login_callback($social_media = '')
    {
        $login_callback_handler = new Login_callback_handler($this);

        if ($social_media == 'twitter') {

            $oauth_verifier = $_GET['oauth_verifier'];
            $login_callback_handler->twitter($oauth_verifier);

        }

        // else if ($social_media == 'tumblr') {

        //     $auth_varifier = $_GET['oauth_verifier'];
        //     $login_callback_handler->tumblr($auth_varifier);

        // }
         else if ($social_media == 'linkedin') {

            if (!isset($_GET['code'])) {

                $this->session->set_userdata('account_import_error', $this->lang->line("Something went wrong while importing your account."));
                redirect(base_url('comboposter/social_accounts'),'refresh');
            } else {
                $code = $_GET['code'];
            }

            $login_callback_handler->linkedin($code);

        } else if ($social_media == 'medium') {

            $integration_token = trim($this->input->post("integration_token",true));

            if (!isset($integration_token)) {

                $this->session->set_userdata('account_import_error', $this->lang->line("Something went wrong while importing your account."));
                redirect(base_url('comboposter/social_accounts'),'refresh');
            } else {
                $code = $integration_token;
            }

            $login_callback_handler->medium($code);

        } else if ($social_media == 'reddit') {

            $code = $_GET["code"];
            $login_callback_handler->reddit($code);

        } else if ($social_media == 'pinterest') {

            $code = $_GET['code'];
            $login_callback_handler->pinterest($code);

        } else if ($social_media == 'wordpress') {

            $login_callback_handler->wordpress();
            
        } else if ($social_media == 'wordpress_self_hosted') {

            $login_callback_handler->wordpress_self_hosted();
            
        } else if ($social_media == 'wordpress_self_hosted_callback') {

            $login_callback_handler->wordpress_self_hosted_callback();
            
        } else if ($social_media == 'youtube') {

            $login_callback_handler->youtube();

        } else if ($social_media == 'blogger') {

            $login_callback_handler->blogger();

        }
    }


    public function campaigns_info($campaign_type ='')
    {
        if ($campaign_type == '') {
            redirect('404','refresh');
        }

        /* check if has module access */
        $module_id_for_this_type = 0;

        if ($campaign_type == 'text') {

            $data['icon'] = 'fa fa-file-text';
            $module_id_for_this_type = 110;
        } else if ($campaign_type == 'image') {

            $data['icon'] = 'fa fa-picture-o';
            $module_id_for_this_type = 111;
        } else if ($campaign_type == 'video') {

            $data['icon'] = 'fas fa-video';
            $module_id_for_this_type = 112;
        } else if ($campaign_type == 'link') {

            $data['icon'] = 'fa fa-link ';
            $module_id_for_this_type = 113;
        } else if ($campaign_type == 'html') {

            $data['icon'] = 'fa fa-html5';
            $module_id_for_this_type = 114;
        }

        // echo "<pre>";print_r($module_id_for_this_type);exit;

        if ($this->session->userdata('user_type') == 'Member' && !in_array($module_id_for_this_type,$this->module_access)) {
           redirect('404','refresh');
        }



        $data['campaign_type'] = $campaign_type;

        $title = ucfirst($campaign_type).' post';
        $data['page_title'] = $this->lang->line($title);
        $data['title'] = $this->lang->line($title);
        $data['body'] = 'posts/campaigns';

        $this->_viewcontroller($data);
    }


    public function campaigns_info_data($campaign_type)     
    {
        $this->ajax_check();

        $searching       = trim($this->input->post("searching",true));
        $post_date_range = $this->input->post("post_date_range",true);
        $display_columns = array("#",'id','campaign_name','campaign_type','posting_medium', 'action','posting_status','schedule_time');
        $search_columns = array('campaign_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();

        if($post_date_range!="")
        {
            $exp = explode('|', $post_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date   = isset($exp[1])?$exp[1]:"";

            if($from_date!="Invalid date" && $to_date!="Invalid date")
            {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date   = date('Y-m-d', strtotime($to_date));
                $where_simple["Date_Format(schedule_time,'%Y-%m-%d') >="] = $from_date;
                $where_simple["Date_Format(schedule_time,'%Y-%m-%d') <="] = $to_date;
            }
        }

        if($searching !="") $where_simple['campaign_name like'] = "%".$searching."%";
        $where_simple['user_id'] = $this->user_id;

        $this->db->where("campaign_type='". $campaign_type."'");
        $this->db->where("is_child='0'");
        $where  = array('where'=>$where_simple);

        $table = "comboposter_campaigns";
        $info = $this->basic->get_data($table,$where,"","",$limit,$start,$order_by,$group_by='');

        $this->db->where("campaign_type='". $campaign_type."'");
        $total_rows_array=$this->basic->count_row($table, $where, $count=$table.".id", "", $group_by='');
        $total_result=$total_rows_array[0]['total_rows'];



        /* complete main campaign count */
        $query = $this->db->query("SELECT parent_campaign_id, COUNT(posting_status) as complete FROM `comboposter_campaigns` WHERE parent_campaign_id IS NOT NULL AND parent_campaign_id != '0' AND campaign_type = '{$campaign_type}' AND posting_status = 'completed' GROUP BY parent_campaign_id");
        $temp_complete_results = $query->result_array();
        $complete_results = array();

        foreach ($temp_complete_results as $key => $value) {
        	$complete_results[$value['parent_campaign_id']] = $value['complete'];
        }



        for($i=0;$i<count($info);$i++)
        {   
            $posting_status = $info[$i]['posting_status'];
            $schedule_type  = $info[$i]['schedule_type'];
           
            if ($posting_status == 'pending') {
                $info[$i]['action'] = '<div style="min-width:180px">';
            } else {
                $info[$i]['action'] = '<div style="min-width:130px">';
            }



            /* posting status */
            if( $posting_status == 'completed') {

                if ($info[$i]['parent_campaign_id'] == "0") {
                    
                    if ($info[$i]['full_complete'] == "1") {
                        $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-success"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</div>';
                    } else {

                    	$completed = 1;
                    	if (isset($complete_results[$info[$i]['id']])) {
                    		$completed = $complete_results[$info[$i]['id']] + 1;
                    	}

                    	$total = $info[$i]['repeat_times'] + 1;
                        $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-muted"><i class="fas fa-exclamation-circle"></i> '. $completed . '/' . $total . ' '. $this->lang->line("completed").'</div>';
                    }
                    
                } else {
                    
                    $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-success"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</div>';
                }
            } else if( $posting_status == 'processing') {

                $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-warning"><i class="fas fa-spinner"></i> '.$this->lang->line("Processing").'</div>';
            } else {

                $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-danger"><i class="far fa-times-circle"></i> '.$this->lang->line("Pending").'</div>';

                $info[$i]['action'] .= '<a href="'. base_url('comboposter/'.$campaign_type.'_post/edit/'.$info[$i]['id']) .'" class="btn btn-outline-warning btn-circle" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Edit this campaign") .'"><i class="fas fa-edit"></i></a>';
            }
            

            /* time scheduled */
            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;'>".date("M j, y H:i",strtotime($info[$i]['schedule_time']))."</div>";
            else 
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;' class='text-muted'><i class='fas fa-exclamation-circle'></i> ".$this->lang->line('Not Scheduled')."</div>";


            
            /* posting mediums */
            $posting_medium_list = json_decode($info[$i]['posting_medium'], true);
            $posting_medium = array();

            foreach ($posting_medium_list as $single_medium) {

                $temp = explode('_', $single_medium);
                array_push($posting_medium, ucfirst($temp[0]));
            }

            $posting_medium = array_unique($posting_medium);
            $info[$i]['posting_medium'] = implode(', ', $posting_medium);



            /* campaign actions */
            $info[$i]['action'] .= ' <a href="'. base_url('comboposter/'.$campaign_type.'_post/clone/'.$info[$i]['id']) .'" class="btn btn-outline-info btn-circle" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Clone this campaign") .'"><i class="fa fa-clone"></i></a>';

            if ($info[$i]['parent_campaign_id'] == "0") {

                $info[$i]['action'] .= ' <a href="#" class="btn btn-outline-primary btn-circle main_campaign_report" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("View campaign report") .'"  campaign_id="'. $info[$i]['id'] .'"><i class="fas fa-eye"></i></a>';

                $info[$i]['action'] .= ' <a href="#" class="btn btn-outline-danger btn-circle delete_campaign" table_id="'. $info[$i]['id'] .'" campaign_type="main_campaign" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Delete this app") .'"><i class="fas fa-trash-alt"></i></a></div><script>$(\'[data-toggle="tooltip"]\').tooltip();</script>';

                /* campaign type */
                $info[$i]['campaign_type'] = $this->lang->line("Main campaign");
            } else {

                $info[$i]['action'] .= ' <a href="'. base_url('comboposter/'.$campaign_type.'_post/report/'.$info[$i]['id']) .'" class="btn btn-outline-primary btn-circle" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("View campaign report") .'"><i class="fas fa-eye"></i></a>';

                $info[$i]['action'] .= ' <a href="#" class="btn btn-outline-danger btn-circle delete_campaign" table_id="'. $info[$i]['id'] .'" campaign_type="not_main_campaign" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Delete this app") .'"><i class="fas fa-trash-alt"></i></a></div><script>$(\'[data-toggle="tooltip"]\').tooltip();</script>';

                /* campaign type */
                $info[$i]['campaign_type'] = $this->lang->line("Single campaign");
            }


             

              
            
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        // echo "<pre>";print_r($data);exit;
        echo json_encode($data);
    }

    public function main_campaigns_info_data()     
    {
        $this->ajax_check();

        $searching       = trim($this->input->post("searching",true));
        $campaign_id       = trim($this->input->post("campaign_id",true));
        $display_columns = array("#",'id','campaign_name','campaign_type','posting_medium', 'action','posting_status','schedule_time');
        $search_columns = array('campaign_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();


        if($searching !="") $where_simple['campaign_name like'] = "%".$searching."%";
        $where_simple['user_id'] = $this->user_id;

        $this->db->where("(id='". $campaign_id."'");
        $this->db->or_where("parent_campaign_id='". $campaign_id."')");
        $this->db->order_by('id', 'ASC');
        $where  = array('where'=>$where_simple);

        $table = "comboposter_campaigns";
        $info = $this->basic->get_data($table,$where,"","",$limit,$start,$order_by,$group_by='');

        $this->db->where("(id='". $campaign_id."'");
        $this->db->or_where("parent_campaign_id='". $campaign_id."')");
        $total_rows_array=$this->basic->count_row($table, $where, $count=$table.".id", "", $group_by='');
        $total_result=$total_rows_array[0]['total_rows'];
        

        for($i=0;$i<count($info);$i++)
        {   
            $posting_status = $info[$i]['posting_status'];
            $schedule_type  = $info[$i]['schedule_type'];
            $campaign_type  = $info[$i]['campaign_type'];
           
            if ($posting_status == 'pending') {
                $info[$i]['action'] = '<div style="min-width:180px">';
            } else {
                $info[$i]['action'] = '<div style="min-width:130px">';
            }
            
            if( $posting_status == 'completed') {

                $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-success"><i class="fas fa-check-circle"></i> '.$this->lang->line("Completed").'</div>';
            } else if( $posting_status == 'processing') {

                $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-warning"><i class="fas fa-spinner"></i> '.$this->lang->line("Processing").'</div>';
            } else {

                $info[$i]['posting_status'] = '<div style="min-width:120px;" class="text-danger"><i class="far fa-times-circle"></i> '.$this->lang->line("Pending").'</div>';

                $info[$i]['action'] .= '<a href="'. base_url('comboposter/'.$campaign_type.'_post/edit/'.$info[$i]['id']) .'" class="btn btn-outline-warning btn-circle" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Edit this campaign") .'"><i class="fas fa-edit"></i></a>';
            }
            
            if ($info[$i]['parent_campaign_id'] == "0") {

                $info[$i]['campaign_type'] = $this->lang->line("Main campaign");

                $info[$i]['action'] .= ' <a href="'. base_url('comboposter/'.$campaign_type.'_post/clone/'.$info[$i]['id']) .'" class="btn btn-outline-info btn-circle" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Clone this campaign") .'"><i class="fa fa-clone"></i></a>';
            } else {
                $info[$i]['campaign_type'] = $this->lang->line("Sub campaign");
            }


            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;'>".date("M j, y H:i",strtotime($info[$i]['schedule_time']))."</div>";
            else 
                $info[$i]['schedule_time'] = "<div style='min-width:120px !important;' class='text-muted'><i class='fas fa-exclamation-circle'></i> ".$this->lang->line('Not Scheduled')."</div>";

            
            $posting_medium_list = json_decode($info[$i]['posting_medium'], true);
            $posting_medium = array();


            foreach ($posting_medium_list as $single_medium) {

                $temp = explode('_', $single_medium);
                array_push($posting_medium, ucfirst($temp[0]));
            }

            $posting_medium = array_unique($posting_medium);
            $info[$i]['posting_medium'] = implode(', ', $posting_medium);


            
            $info[$i]['action'] .= ' <a href="'. base_url('comboposter/'.$campaign_type.'_post/report/'.$info[$i]['id']) .'" class="btn btn-outline-primary btn-circle" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("View campaign report") .'"><i class="fas fa-eye"></i></a>';
            
            if ($info[$i]['parent_campaign_id'] == "0") {

                $info[$i]['action'] .= ' <a href="#" class="btn btn-outline-danger btn-circle delete_campaign" table_id="'. $info[$i]['id'] .'" campaign_type="main_campaign" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Delete this app") .'"><i class="fas fa-trash-alt"></i></a></div><script>$(\'[data-toggle="tooltip"]\').tooltip();</script>';
            } else {

                $info[$i]['action'] .= ' <a href="#" class="btn btn-outline-danger btn-circle delete_campaign" table_id="'. $info[$i]['id'] .'" campaign_type="not_main_campaign" data-toggle="tooltip" data-placement="top" data-title="'. $this->lang->line("Delete this app") .'"><i class="fas fa-trash-alt"></i></a></div><script>$(\'[data-toggle="tooltip"]\').tooltip();</script>';
            }

              
            
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        // echo "<pre>";print_r($data);exit;
        echo json_encode($data);
    }


    public function campaign_report($table_id)
    {
        if ($table_id == '') {
            redirect('404','refresh');
        }

        /* get campaign info */
        $campaigns_info = $this->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->user_id, 'id' => $table_id)));

        if (count($campaigns_info) == 0) {
            redirect(base_url('404'),'refresh');
        }
        $campaigns_info = $campaigns_info[0];

        $data['campaign_type'] = $campaigns_info['campaign_type'];

        /* list of posting mediums */
        $posting_mediums_info = json_decode($campaigns_info['posting_medium'], true);
        unset($campaigns_info['posting_medium']);
        $posting_medium = array();

        foreach ($posting_mediums_info as $single_medium) {

            $temp = explode('_', $single_medium);
            array_push($posting_medium, ucfirst($temp[0]));
        }

        $posting_medium = array_unique($posting_medium);
        $data['posting_medium'] = $posting_medium;


        /* report info */
        $post_report = json_decode($campaigns_info['report'], true);
        if (!is_array($post_report)) {
            $post_report = array();
        }
        unset($campaigns_info['report']);
        $data['post_report'] = $post_report;
        // echo "<pre>";print_r($post_report);exit;


        $data['campaigns_info'] = $campaigns_info;


        // echo "<pre>";print_r($campaigns_info);exit;
        $data['page_title'] = $this->lang->line("Campaign Report");
        $data['title'] = $this->lang->line("Campaign Report");
        $data['body'] = 'posts/report';

        $this->_viewcontroller($data);
    }


    public function delete_campaign()
    {
        $this->ajax_check();

        $table_id = $this->input->post('table_id', true);

        $table_info = $this->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->user_id, 'id' => $table_id)));

        $response = array();
        if (count($table_info) > 0) {

            $this->basic->delete_data('comboposter_campaigns', array('id' => $table_id));

            if ($table_info[0]['parent_campaign_id'] == "0") {
                $this->basic->delete_data('comboposter_campaigns', array('parent_campaign_id' => $table_id));
            }

            $response['status'] = 'success';
            $response['message'] = $this->lang->line("Campaign deleted successfully.");
            echo json_encode($response);
        } else {

            $response['status'] = 'error';
            $response['message'] = $this->lang->line("May be you have no permission for deleting this campaign or something went wrong.");
            echo json_encode($response);
        }
    }


    public function text_post($action_type = '', ...$extra_parameter)
    {
        /* check module access */
        $module_id_for_this_type = 110;
        if ($this->session->userdata('user_type') == 'Member' && !in_array($module_id_for_this_type,$this->module_access)) {
           redirect('404','refresh');
        }


        $text_post_handler = new Text_post_handler($this);

        if ($action_type == 'campaigns') {

            $this->campaigns_info('text');
        } else if ($action_type == 'create') {

            $text_post_handler->create();
        } else if ($action_type == 'add') {

            $this->ajax_check();
            $text_post_handler->add();
        } else if ($action_type == 'edit') {

            if (count($extra_parameter) > 0) {
                $text_post_handler->edit($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $text_post_handler->edit_action();
            }
        } else if ($action_type == 'clone') {

            if (count($extra_parameter) > 0) {
                $text_post_handler->clone_campaign($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $text_post_handler->add();
            }
        } else if ($action_type == 'report') {

            if (count($extra_parameter) > 0) {
                $this->campaign_report($extra_parameter[0]);
            } else {
                redirect(base_url('404'),'refresh');
            }
        }
    }

    public function image_post($action_type = '', ...$extra_parameter)
    {
        /* check module access */
        $module_id_for_this_type = 111;
        if ($this->session->userdata('user_type') == 'Member' && !in_array($module_id_for_this_type,$this->module_access)) {
           redirect('404','refresh');
        }


        $image_post_handler = new Image_post_handler($this);

        if ($action_type == 'campaigns') {

            $this->campaigns_info('image');
        } else if ($action_type == 'create') {

            $image_post_handler->create();
        } else if ($action_type == 'add') {

            $this->ajax_check();
            $image_post_handler->add();
        } else if ($action_type == 'edit') {

            if (count($extra_parameter) > 0) {
                $image_post_handler->edit($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $image_post_handler->edit_action();
            }
        } else if ($action_type == 'clone') {

            if (count($extra_parameter) > 0) {
                $image_post_handler->clone_campaign($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $image_post_handler->add();
            }
        } else if ($action_type == 'report') {

            if (count($extra_parameter) > 0) {
                $this->campaign_report($extra_parameter[0]);
            } else {
                redirect(base_url('404'),'refresh');
            }
        }
    }

    public function video_post($action_type = '', ...$extra_parameter)
    {
        /* check module access */
        $module_id_for_this_type = 112;
        if ($this->session->userdata('user_type') == 'Member' && !in_array($module_id_for_this_type,$this->module_access)) {
           redirect('404','refresh');
        }


        $video_post_handler = new Video_post_handler($this);

        if ($action_type == 'campaigns') {

            $this->campaigns_info('video');
        } else if ($action_type == 'create') {

            $video_post_handler->create();
        } else if ($action_type == 'add') {

            $this->ajax_check();
            $video_post_handler->add();
        } else if ($action_type == 'edit') {

            if (count($extra_parameter) > 0) {
                $video_post_handler->edit($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $video_post_handler->edit_action();
            }
        } else if ($action_type == 'clone') {

            if (count($extra_parameter) > 0) {
                $video_post_handler->clone_campaign($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $video_post_handler->add();
            }
        } else if ($action_type == 'report') {

            if (count($extra_parameter) > 0) {
                $this->campaign_report($extra_parameter[0]);
            } else {
                redirect(base_url('404'),'refresh');
            }
        }
    }

    public function link_post($action_type = '', ...$extra_parameter)
    {
        /* check module access */
        $module_id_for_this_type = 113;
        if ($this->session->userdata('user_type') == 'Member' && !in_array($module_id_for_this_type,$this->module_access)) {
           redirect('404','refresh');
        }


        $link_post_handler = new Link_post_handler($this);

        if ($action_type == 'campaigns') {

            $this->campaigns_info('link');
        } else if ($action_type == 'create') {

            $link_post_handler->create();
        } else if ($action_type == 'add') {

            $this->ajax_check();
            $link_post_handler->add();
        } else if ($action_type == 'edit') {

            if (count($extra_parameter) > 0) {
                $link_post_handler->edit($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $link_post_handler->edit_action();
            }
        } else if ($action_type == 'clone') {

            if (count($extra_parameter) > 0) {
                $link_post_handler->clone_campaign($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $link_post_handler->add();
            }
        } else if ($action_type == 'report') {

            if (count($extra_parameter) > 0) {
                $this->campaign_report($extra_parameter[0]);
            } else {
                redirect(base_url('404'),'refresh');
            }
        }
    }

    public function html_post($action_type = '', ...$extra_parameter)
    {
        /* check module access */
        $module_id_for_this_type = 114;
        if ($this->session->userdata('user_type') == 'Member' && !in_array($module_id_for_this_type,$this->module_access)) {
           redirect('404','refresh');
        }


        $html_post_handler = new Html_post_handler($this);

        if ($action_type == 'campaigns') {

            $this->campaigns_info('html');
        } else if ($action_type == 'create') {

            $html_post_handler->create();
        } else if ($action_type == 'add') {

            $this->ajax_check();
            $html_post_handler->add();
        } else if ($action_type == 'edit') {

            if (count($extra_parameter) > 0) {
                $html_post_handler->edit($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $html_post_handler->edit_action();
            }
        } else if ($action_type == 'clone') {

            if (count($extra_parameter) > 0) {
                $html_post_handler->clone_campaign($extra_parameter[0]);
            } else {

                $this->ajax_check();
                $html_post_handler->add();
            }
        } else if ($action_type == 'report') {

            if (count($extra_parameter) > 0) {
                $this->campaign_report($extra_parameter[0]);
            } else {
                redirect(base_url('404'),'refresh');
            }
        }
    }


    public function upload_file_handler($type = '')
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' || $type == '') {
            exit();
        } 


        $ret = array();
        $output_dir = FCPATH."upload/comboposter/";
        if(!file_exists($output_dir)) {
            mkdir($output_dir,0777);
        }

        $output_dir = $output_dir.'/'.$this->user_id.'/';
        if(!file_exists($output_dir)) {
            mkdir($output_dir,0777);
        }

        if ($type == 'image') {
            $allow = ".png,.jpg,.jpeg,.bmp,.tiff";
        } else if ($type == 'video') {
            $allow = ".mp4,.mov,.avi,.wmv,.mpg,.flv";
        }

        if (isset($_FILES["file"])) {

            $error = $_FILES["file"]["error"];

            $post_fileName = $_FILES["file"]["name"];
            $post_fileName_array = explode(".", $post_fileName);
            $ext = array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename=$this->user_id."_".$type."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

            
            $allow = str_replace('.', '', $allow);
            $allow = explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit;
            }

            move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }


    public function delete_file_handler()
    {
        
        if (!$_POST) {
            exit();
        }

        $output_dir = FCPATH."upload/comboposter/".$this->user_id.'/';
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
            $fileName = $_POST['name'];
            $fileName = str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
            $filePath = $output_dir. $fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } else {
            $fileName = $this->input->post("fileName",true);
            if (file_exists($output_dir.$fileName)) {
                unlink($output_dir.$fileName);
            }
        }
    }



    public function getUserAccountsList($social_media, $user_id)
    {
        
        $existing_social_accounts_handler = new Existing_social_accounts_handler($this);

        if ($social_media == 'facebook') {
            return $existing_social_accounts_handler->facebook($user_id);
        } else if ($social_media == 'twitter') {
            return $existing_social_accounts_handler->twitter($user_id);
        }
        //  else if ($social_media == 'tumblr') {
        //     return $existing_social_accounts_handler->tumblr($user_id);
        // }
         else if ($social_media == 'youtube') {
            return $existing_social_accounts_handler->youtube($user_id);
        } else if ($social_media == 'linkedin') {
            return $existing_social_accounts_handler->linkedin($user_id);
        } else if ($social_media == 'medium') {
            return $existing_social_accounts_handler->medium($user_id);
        } else if ($social_media == 'pinterest') {
            return $existing_social_accounts_handler->pinterest($user_id);
        } else if ($social_media == 'blogger') {
            return $existing_social_accounts_handler->blogger($user_id);
        } else if ($social_media == 'wordpress') {
            return $existing_social_accounts_handler->wordpress($user_id);
        } else if ($social_media == 'wordpress_self_hosted') {
            return $existing_social_accounts_handler->wordpress_self_hosted($user_id);
        } else if ($social_media == 'reddit') {
            return $existing_social_accounts_handler->reddit($user_id);
        }
    }


    public function link_meta_info_grabber()
    {
        if ($_POST) {

            $link = $this->input->post("link");
            $this->load->library("fb_rx_login");
            $response = $this->fb_rx_login->get_meta_tag_fb($link);
            echo json_encode($response);
        }
    }


    public function mutiArrToSingleArr($data)
    {
        $medium_list = [];
        foreach($data as $singleArr) {

            foreach($singleArr as $value) {
                array_push($medium_list, $value);
            }
        }
        return $medium_list;
    }


    public function get_periodic_time()
    {

        $all_periodic_time= array(
        
        
        '60' =>'every 1 hours',
        '120'=>'every 2 hours',
        '300'=>'every 5 hours',
        '1440'=>'every 1 days',
        '2880'=>'every 2 days',
        '4320'=>'every 3 days',
        '7200'=>'every 5 days',
        '8640'=>'every 6 days',
        '10080'=>'every 7 days',
        '43200'=>'every 1 months',
        '86400'=>'every 2 months',
        '259200'=>'every 6 months',
       );
        return $all_periodic_time;
    }


    public function single_campaign_post_to_all_media($table_id)
    {
        $campaign_info = $this->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->user_id, 'id' => $table_id)));

        if (count($campaign_info) == 0) {
            return;
        } 

        $this->basic->update_data('comboposter_campaigns', array('id' => $table_id), array('posting_status' => 'processing'));

        $campaign_info = $campaign_info[0];


        $post_content = [];
        $time_zone = $campaign_info['schedule_timezone'];
        $schedule_time = $campaign_info['schedule_time'];

        if($time_zone != '') {
            date_default_timezone_set($time_zone);
        }

        $current_time = date("Y-m-d H:i:s", strtotime("+5 minutes"));
        $compare_value = strtotime($schedule_time);
        $current_value = strtotime($current_time);

        $selected_social_media = json_decode($campaign_info['posting_medium'],true);
        $post_type = $campaign_info['campaign_type'];
        $post_content['user_id'] = $campaign_info['user_id'];

            
        /*--------  get post content accourding to post type  -------*/
        if($campaign_info['campaign_type'] == 'text') {

            $post_content['campaign_name'] = $campaign_info['campaign_name'];
            $post_content['title'] = $campaign_info['title'];
            $post_content['tag'] = $campaign_info['tag'];
            $post_content['message'] = $campaign_info['message'];
            $post_content['subreddits'] = $campaign_info['subreddits'];
            $post_content['wpsh_selected_category'] = $single_campaign_info['wpsh_selected_category'];
        } else if($campaign_info['campaign_type'] == 'image') {

            $post_content['campaign_name'] = $campaign_info['campaign_name'];
            $post_content['title'] = $campaign_info['title'];
            $post_content['tag'] = $campaign_info['tag'];
            $post_content['message'] = $campaign_info['message'];
            $post_content['rich_content'] = $campaign_info['rich_content'];
            $post_content['image_url'] = $campaign_info['image_url'];
            $post_content['link'] = $campaign_info['link'];
            $post_content['wpsh_selected_category'] = $single_campaign_info['wpsh_selected_category'];

        } else if($campaign_info['campaign_type'] == 'video') {

            $post_content['campaign_name'] = $campaign_info['campaign_name'];
            $post_content['title'] = $campaign_info['title'];
            $post_content['tag'] = $campaign_info['tag'];
            $post_content['message'] = $campaign_info['message'];
            $post_content['privacy_type'] = $campaign_info['privacy_type'];
            $post_content['video_url'] = $campaign_info['video_url'];
            $post_content['thumbnail_url'] = $campaign_info['thumbnail_url'];
            $post_content['wpsh_selected_category'] = $single_campaign_info['wpsh_selected_category'];
        } else if($campaign_info['campaign_type'] == 'link') {

            $post_content['campaign_name'] = $campaign_info['campaign_name'];
            $post_content['title'] = $campaign_info['title'];
            $post_content['link_caption'] = $campaign_info['link_caption'];
            $post_content['link_description'] = $campaign_info['link_description'];
            $post_content['link'] = $campaign_info['link'];
            $post_content['thumbnail_url'] = $campaign_info['thumbnail_url'];
            $post_content['message'] = $campaign_info['message'];
            $post_content['subreddits'] = $campaign_info['subreddits'];
        } else if($campaign_info['campaign_type'] == 'html') {

            $post_content['campaign_name'] = $campaign_info['campaign_name'];
            $post_content['title'] = $campaign_info['title'];
            $post_content['tag'] = $campaign_info['tag'];
            $post_content['rich_content'] = $campaign_info['rich_content'];
            $post_content['wpsh_selected_category'] = $single_campaign_info['wpsh_selected_category'];
        }

        /*--------  end get post content   -------*/
        $post_response = $this->post_to_all_media_action($post_type, $post_content, $selected_social_media);

        foreach($post_response as $key => $single_response)
            if(count($single_response) == 0) unset($post_response[$key]);
        
        
        $update_data = [];
        $update_data['report'] = json_encode($post_response);
        $update_data['posting_status'] = 'completed';
        $this->basic->update_data('comboposter_campaigns', array( 'id' => $campaign_info['id'] ) , $update_data);


    }


    public function api_key_check($api_key="")
    {
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


    // giant poster cron job start
    // run every 10 mins
    // it will execute 1 campaign each time, recommend time interval 3 minutes
    public function post_to_all_media($api_key = "")
    {
       // $this->api_key_check($api_key);

        $total_rows_array = $this->basic->count_row("comboposter_campaigns", array(
            "where" => array(
                "posting_status" => "processing"
            )
        ) , $count = "id", $join = '');
        $data["total_processing"] = $total_rows_array[0]['total_rows'];
        if($data["total_processing"] > 15) exit();


        $campaign_info = $this->basic->get_data('comboposter_campaigns', array( 'where' => array( 'posting_status' => 'pending' ) ) , '', '', '100', 0, 'schedule_time asc');
        
        /* for updating campaign status processing */
        $processing_campaign_id_list = array();
        foreach($campaign_info as $single_campaign)
            array_push($processing_campaign_id_list, $single_campaign['id']);

        if(empty($processing_campaign_id_list)) return;


        // update campaign status using where_in
        // $update_data = [];
        // $update_data['posting_status'] = 'processing';
        // $this->db->where_in('id', $processing_campaign_id_list);
        // $this->db->update('comboposter_campaigns', $update_data); 


        foreach($campaign_info as $single_campaign_info)
        {
            
            $post_content = [];

            $time_zone = $single_campaign_info['schedule_timezone'];
            $schedule_time = $single_campaign_info['schedule_time'];
            if($time_zone != '') date_default_timezone_set($time_zone);

            $current_time = date("Y-m-d H:i:s", strtotime("+5 minutes"));
            $compare_value = strtotime($schedule_time);
            $current_value = strtotime($current_time);

            $selected_social_media = json_decode($single_campaign_info['posting_medium'],true);

            $post_type = $single_campaign_info['campaign_type'];
            $post_content['user_id'] = $single_campaign_info['user_id'];

            if($compare_value <= $current_value)
            {
                
                $this->basic->update_data('comboposter_campaigns', array('id' => $single_campaign_info['id']), array('posting_status' => 'processing'));
                
                /*--------  get post content accourding to post type  -------*/
                if($single_campaign_info['campaign_type'] == 'text')
                {
                    $post_content['campaign_name'] = $single_campaign_info['campaign_name'];
                    $post_content['title'] = $single_campaign_info['title'];
                    $post_content['tag'] = $single_campaign_info['tag'];
                    $post_content['message'] = $single_campaign_info['message'];
                    $post_content['subreddits'] = $single_campaign_info['subreddits'];
                    $post_content['wpsh_selected_category'] = $single_campaign_info['wpsh_selected_category'];
                }
                else if($single_campaign_info['campaign_type'] == 'image')
                {
                    $post_content['campaign_name'] = $single_campaign_info['campaign_name'];
                    $post_content['title'] = $single_campaign_info['title'];
                    $post_content['tag'] = $single_campaign_info['tag'];
                    $post_content['message'] = $single_campaign_info['message'];
                    $post_content['rich_content'] = $single_campaign_info['rich_content'];
                    $post_content['image_url'] = $single_campaign_info['image_url'];
                    $post_content['link'] = $single_campaign_info['link'];
                    $post_content['wpsh_selected_category'] = $single_campaign_info['wpsh_selected_category'];
                }
                else if($single_campaign_info['campaign_type'] == 'video')
                {
                    $post_content['campaign_name'] = $single_campaign_info['campaign_name'];
                    $post_content['title'] = $single_campaign_info['title'];
                    $post_content['tag'] = $single_campaign_info['tag'];
                    $post_content['message'] = $single_campaign_info['message'];
                    $post_content['privacy_type'] = $single_campaign_info['privacy_type'];
                    $post_content['video_url'] = $single_campaign_info['video_url'];
                    $post_content['thumbnail_url'] = $single_campaign_info['thumbnail_url'];
                    $post_content['wpsh_selected_category'] = $single_campaign_info['wpsh_selected_category'];
                }
                else if($single_campaign_info['campaign_type'] == 'link')
                {
                    $post_content['campaign_name'] = $single_campaign_info['campaign_name'];
                    $post_content['title'] = $single_campaign_info['title'];
                    $post_content['link_caption'] = $single_campaign_info['link_caption'];
                    $post_content['link_description'] = $single_campaign_info['link_description'];
                    $post_content['link'] = $single_campaign_info['link'];
                    $post_content['thumbnail_url'] = $single_campaign_info['thumbnail_url'];
                    $post_content['message'] = $single_campaign_info['message'];
                    $post_content['subreddits'] = $single_campaign_info['subreddits'];
                }
                else if($single_campaign_info['campaign_type'] == 'html')
                {
                    $post_content['campaign_name'] = $single_campaign_info['campaign_name'];
                    $post_content['title'] = $single_campaign_info['title'];
                    $post_content['tag'] = $single_campaign_info['tag'];
                    $post_content['rich_content'] = $single_campaign_info['rich_content'];
                    $post_content['wpsh_selected_category'] = $single_campaign_info['wpsh_selected_category'];
                }

                /*--------  end get post content   -------*/
                $post_response = $this->post_to_all_media_action($post_type, $post_content, $selected_social_media);

                foreach($post_response as $key => $single_response)
                    if(count($single_response) == 0) unset($post_response[$key]);
                
                
                $update_data = [];

                $update_data['report'] = json_encode($post_response);
                $update_data['posting_status'] = 'completed';

                $this->basic->update_data('comboposter_campaigns', array(
                    'id' => $single_campaign_info['id']
                ) , $update_data);

                if ($single_campaign_info['parent_campaign_id'] != null && $single_campaign_info['parent_campaign_id'] != "0") {
                    
                    $parent_id = $single_campaign_info['parent_campaign_id'];

                    $siblings_info = $this->basic->get_data('comboposter_campaigns', array('where' => array('parent_campaign_id', $parent_id)), array('posting_status'));

                    $completed_all = true;

                    foreach ($siblings_info as $sibling) {
                        
                        if ($sibling['posting_status'] != 'completed') {
                            $completed_all = false;
                        }
                    }

                    if ($completed_all) {
                        $this->basic->update_data('comboposter_campaigns', array('id'=> $parent_id), array('full_complete' => "1"));
                    }
                }

                $temp_processing_id = array_search($single_campaign_info['id'], $processing_campaign_id_list);
                if(isset($temp_processing_id)) unset($processing_campaign_id_list[$temp_processing_id]);




                // if(isset($single_campaign_info['video_url'])) {


                //  $temp_video_url = str_replace(base_url(), FCPATH, $single_campaign_info['video_url']);

                //  if(file_exists($temp_video_url)) @unlink($temp_video_url);
                // }
                // print_r($single_campaign_info['id']);
                // echo "<br />";
            }
        }
    }

    


    private function post_to_all_media_action($post_type = "", $post_content = "", $selected_social_media = "")
    {
        $this->load->library('Twitter');
        $this->load->library('Linkedin');
        $this->load->library('Medium');
        $this->load->library('Google_youtube_login');
        $this->load->library('Reddit');
        $this->load->library('Pinterests');
        $this->load->library('Wp_org_poster');
        $this->load->library('Wordpress_self_hosted');
        $this->load->library('Fb_rx_login');
        // $this->load->library('google_youtube_login');
        

        $posting_report = [];
        $posting_report['Twitter'] = [];
        $posting_report['Pinterest'] = [];
        $posting_report['Linkedin'] = [];
        $posting_report['Medium'] = [];
        $posting_report['Wordpress'] = [];
        $posting_report['Wordpress_self_hosted'] = [];
        $posting_report['Blogger'] = [];
        $posting_report['Facebook'] = [];
        $posting_report['Youtube'] = [];
        $posting_report['Reddit'] = [];

        $selected_social_media=array_filter($selected_social_media);

        foreach($selected_social_media as $single_media)
        {
            $social_media_info = explode('-', $single_media);
            if($social_media_info[1] == "") continue;
            if(!isset($social_media_info[1])) continue;

            if(strpos($single_media, 'facebook') !== false) {

                /* get facebook info & access tokens */
                $facebook_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('id' => $social_media_info[1])));
                if(!isset($facebook_info[0])) continue;

                $page_access_token = $facebook_info[0]['page_access_token'];
                $facebook_rx_fb_user_info_id = $facebook_info[0]['facebook_rx_fb_user_info_id'];
                $page_id = $facebook_info[0]['page_id'];


                /* initialize app */
                $facebook_rx_config = $this->basic->get_data('facebook_rx_fb_user_info', array('where' => array('id' => $facebook_rx_fb_user_info_id)));
                $facebook_rx_config_id=isset($facebook_rx_config[0]['facebook_rx_config_id'])?$facebook_rx_config[0]['facebook_rx_config_id']:0;
                $this->fb_rx_login->app_initialize($facebook_rx_config_id);


                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $facebook_info[0]['page_name'];
                $temp_report['display_account_image'] = $facebook_info[0]['page_profile'];

                /* posting */
                if($post_type == 'text') {

                    $message = $post_content['message'];

                    try {

                        $facebook_status = $this->fb_rx_login->feed_post($message, "", "", "", "", "", $page_access_token, $page_id);
                        $post_url_data = $this->fb_rx_login->get_post_permalink($facebook_status['id'], $page_access_token);
                        $temp_report['report'] = $post_url_data['permalink_url'];
                        array_push($posting_report['Facebook'], $temp_report);
                    } catch(Exception $e) {

                        $error_msg = $e->getMessage();
                        $temp_report['report'] = $error_msg;
                        array_push($posting_report['Facebook'], $temp_report);
                    }
                } else if($post_type == 'image') {

                    $message = $post_content['message'];
                    $image_url = $post_content['image_url'];

                    try {

                        $facebook_status = $this->fb_rx_login->photo_post($message, $image_url, "", $page_access_token, $page_id);
                        $post_url_data = $this->fb_rx_login->get_post_permalink($facebook_status['post_id'], $page_access_token);
                        $temp_report['report'] = $post_url_data['permalink_url'];
                        array_push($posting_report['Facebook'], $temp_report);
                    } catch(Exception $e) {

                        $error_msg = $e->getMessage();
                        $temp_report['report'] = $error_msg;
                        array_push($posting_report['Facebook'], $temp_report);
                    }
                } else if($post_type == 'video') {

                    $message = $post_content['message'];
                    $title = $post_content['title'];
                    $video_url = $post_content['video_url'];
                    $thumbnail_url = $post_content['thumbnail_url'];


                    try {

                        $facebook_status = $this->fb_rx_login->post_video($message, $title, $video_url, "", $thumbnail_url, "", $page_access_token, $page_id);
                        $post_url_data = $this->fb_rx_login->get_post_permalink($facebook_status['id'], $page_access_token);
                        $temp_report['report'] = $post_url_data['permalink_url'];
                        array_push($posting_report['Facebook'], $temp_report);
                    } catch(Exception $e) {

                        $error_msg = $e->getMessage();
                        $temp_report['report'] = $error_msg;
                        array_push($posting_report['Facebook'], $temp_report);
                    }
                } else if($post_type == 'link') {

                    $message = $post_content['message'];
                    $link = $post_content['link'];
                    $thumbnail_url = $post_content['thumbnail_url'];
                    $link_caption = $post_content['link_caption'];
                    $link_description = $post_content['link_description'];


                    try {

                        $facebook_status = $this->fb_rx_login->feed_post($message, $link, "", "", "", "", $page_access_token, $page_id);
                        $post_url_data = $this->fb_rx_login->get_post_permalink($facebook_status['id'], $page_access_token);
                        $temp_report['report'] = $post_url_data['permalink_url'];
                        array_push($posting_report['Facebook'], $temp_report);
                    } catch(Exception $e) {

                        $error_msg = $e->getMessage();
                        $temp_report['report'] = $error_msg;
                        array_push($posting_report['Facebook'], $temp_report);
                    }
                }
            } elseif(strpos($single_media, 'twitter') !== false) {

                /* get account info & access tokens */
                $twitter_info = $this->basic->get_data('twitter_users_info', array('where' => array( 'id' => $social_media_info[1] )));
                if(!isset($twitter_info[0])) continue;

                $oauth_token = $twitter_info[0]['oauth_token'];
                $oauth_token_secret = $twitter_info[0]['oauth_token_secret'];

                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $twitter_info[0]['screen_name'];
                $temp_report['display_account_image'] = $twitter_info[0]['profile_image'];

                /* posting */
                if($post_type == 'text') {

                    $message_twitter = $post_content['message'];
                    if (strlen($message_twitter) > 280) {
                        $message_twitter = substr($message_twitter, 0, 280);
                    }

                    try {

                        $twitter_status = $this->twitter->post_to_twitter($oauth_token, $oauth_token_secret, $message_twitter);
                        $twitter_status = json_decode($twitter_status, true);
                        

                        if(!isset($twitter_status['errors'])) {

                            if(isset($twitter_status['id_str'])) {
                                $tweet_url = "https://twitter.com/" . $twitter_info[0]['screen_name'] . "/status/" . $twitter_status['id_str'];
                            }

                            $temp_report['report'] = $tweet_url;
                            array_push($posting_report['Twitter'], $temp_report);
                        } else {

                            $temp_report['report'] = $twitter_status['errors'][0]['message'];
                            array_push($posting_report['Twitter'], $temp_report);
                        }
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Twitter'], $temp_report);
                    }
                } else if($post_type == 'image') {

                    $image_url = $post_content['image_url'];
                    $image_url = str_replace(base_url() , '', $image_url);

                    $message_twitter = $post_content['message'];
                    if (strlen($message_twitter) > 280) {
                        $message_twitter = substr($message_twitter, 0, 280);
                    }

                    try {

                        $twitter_status = $this->twitter->photo_post_to_twitter($oauth_token, $oauth_token_secret, $image_url, $message_twitter);

                        if(!isset($twitter_status->error)) {

                            if(isset($twitter_status->id_str)) {
                                $tweet_url = "https://twitter.com/" . $twitter_info[0]['screen_name'] . "/status/" . $twitter_status->id_str;
                            }

                            $temp_report['report'] = $tweet_url;
                            array_push($posting_report['Twitter'], $temp_report);
                        } else {

                            $temp_report['report'] = $twitter_status->error;
                            array_push($posting_report['Twitter'], $temp_report);
                        }
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Twitter'], $temp_report);
                    }
                } else if($post_type == 'video') {

                    $video_url = str_replace(base_url() , FCPATH, $post_content['video_url']);


                    $message_twitter = $post_content['message'];
                    if (strlen($message_twitter) > 280) {
                        $message_twitter = substr($message_twitter, 0, 280);
                    }

                    try {

                        $twitter_status = $this->twitter->video_post_to_twitter($oauth_token, $oauth_token_secret, $video_url, $message_twitter);
                        $twitter_status = json_decode($twitter_status,true);
                        // echo "<pre>";print_r($twitter_status);exit;

                        if(isset($twitter_status['id'])) {

                            if(isset($twitter_status['id'])) {
                                $tweet_url = "https://twitter.com/" . $twitter_info[0]['screen_name'] . "/status/" . $twitter_status['id'];
                            }

                            $temp_report['report'] = $tweet_url;
                            array_push($posting_report['Twitter'], $temp_report);
                        } else {

                            $temp_report['report'] = $twitter_status['error'];
                            array_push($posting_report['Twitter'], $temp_report);
                        }
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Twitter'], $temp_report);
                    }
                } else if($post_type == 'link') {

                    $link = $post_content['link'];

                    try {

                        $twitter_status = $this->twitter->post_to_twitter($oauth_token, $oauth_token_secret, $link);
                        $twitter_status = json_decode($twitter_status, true);

                        if(!isset($twitter_status['error'])) {

                            if (isset($twitter_status['id_str'])) {
                                $tweet_url = "https://twitter.com/" . $twitter_info[0]['screen_name'] . "/status/" . $twitter_status['id_str'];
                            }

                            $temp_report['report'] = $tweet_url;
                            array_push($posting_report['Twitter'], $temp_report);
                        } else {

                            $temp_report['report'] = $twitter_status['error'];
                            array_push($posting_report['Twitter'], $temp_report);
                        }
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Twitter'], $temp_report);
                    }
                }
            } elseif(strpos($single_media, 'reddit') !== false) {

                /* get account info & access tokens */
                $reddit = $this->basic->get_data('reddit_users_info', array( 'where' => array( 'id' => $social_media_info[1] ) ));
                if(!isset($reddit[0])) continue;

                $refresh_token = $reddit[0]['refresh_token'];
                $token_type = $reddit[0]['token_type'];

                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $reddit[0]['username'];
                $temp_report['display_account_image'] = $reddit[0]['profile_pic'];

                /* posting */
                if($post_type == 'text') {

                    $title = $post_content['title'];
                    $subreddits = $post_content['subreddits'];
                    $text = substr($post_content['message'], 0, 300);

                    try {

                        $reddit_status = $this->reddit->createStory($title, "", $subreddits, $text, $refresh_token, $token_type);
                        $reddit_url = $reddit_status->jquery[10][3][0];

                        if (strpos($reddit_url, 'https://www.reddit.com') !== false) {
                            $temp_report['report'] = $reddit_url;
                        } else {
                            $reddit_error="";

                            for($i=10;$i<=24;$i++) {

                                if(isset($reddit_status->jquery[$i][3][0]) && is_array($reddit_status->jquery[$i][3])){
                                   $reddit_error = $reddit_error." | ". $reddit_status->jquery[$i][3][0]; 
                                }
                            }

                            $temp_report['report'] = trim($reddit_error,' | ');

                        }

                        array_push($posting_report['Reddit'], $temp_report);
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Reddit'], $temp_report);
                    }
                } else if($post_type == 'link') {

                    $subreddits = $post_content['subreddits'];
                    $link = $post_content['link'];
                    $title = $post_content['title'];


                    try {

                        $reddit_status = $this->reddit->createStory($title, $link, $subreddits, "", $refresh_token, $token_type);

                        if(isset($reddit_status['error'])){
                            $temp_report['report'] = $reddit_status['error'];
                            array_push($posting_report['Reddit'], $temp_report);
                        }
                        else {

                            $reddit_url = $reddit_status->jquery[16][3][0];

                            if(strpos($reddit_url, 'https://www.reddit.com') !== false) {
                                $temp_report['report'] = $reddit_url;
                            } 
                            else {
                                $reddit_error="";

                                for($i=10;$i<=24;$i++) {

                                    if(isset($reddit_status->jquery[$i][3][0]) && is_array($reddit_status->jquery[$i][3])){
                                       $reddit_error = $reddit_error." | ". $reddit_status->jquery[$i][3][0]; 
                                    }
                                }

                                $temp_report['report'] = trim($reddit_error,' | ');
                            }

                            array_push($posting_report['Reddit'], $temp_report);
                        } 
                    } 
                    catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Reddit'], $temp_report);
                    }
                }
            } elseif(strpos($single_media, 'pinterest') !== false) {

                /* get accounts & access tokens */
                $select = array( 'user_name', 'image', 'code', 'pinterest_board_info.board_name as board_name','board_id','pinterest_config_table_id' );
                $join = array( 'pinterest_board_info' => 'pinterest_users_info.id=pinterest_board_info.pinterest_table_id,left' );
                $wherer['where_in'] = array( 'pinterest_board_info.id' => $social_media_info[1] );

                $pinterest = $this->basic->get_data('pinterest_users_info', $wherer, $select, $join);
                if(!isset($pinterest[0])) continue;

                $pinterest = $pinterest[0];

                $user_name = $pinterest['user_name'];
                $board_name = $pinterest['board_name'];
                $board_id = $pinterest['board_id'];
                $code = $pinterest['code'];

                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $pinterest['user_name']." | ".$board_name;
                $temp_report['display_account_image'] = $pinterest['image'];

                /* posting */
                if($post_type == 'image') {

                    $image_url = $post_content['image_url'];
                    $message = $post_content['message'];
                    $pin_link=$post_content['link'];

                    try {

                        $this->pinterests->app_initialize($pinterest['pinterest_config_table_id']);
                        $pinterest_status = $this->pinterests->image_post_to_pinterest($user_name, $board_id, $image_url, $code, $message,$pin_link);

                        if(isset($pinterest_status['error']) && isset($pinterest_status['error_message'])){
                             $temp_report['report'] = $pinterest_status['error_message'];
                             array_push($posting_report['Pinterest'], $temp_report);
                        }

                        else{
                             $pinterest_url = $pinterest_status['url'];
                             $temp_report['report'] = $pinterest_url;
                            array_push($posting_report['Pinterest'], $temp_report);
                        }

                       
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Pinterest'], $temp_report);
                    }
                }
                else if($post_type == 'link') {
                }
            } elseif(strpos($single_media, 'wordpress_self_hosted') !== false) {

                $wp_sh_config = $this->basic->get_data("wordpress_config_self_hosted", ['where' => [ 'id' => $social_media_info[1], 'deleted' => '0', 'status' => '1']]);

                if (! sizeof($wp_sh_config) > 0) {
                    continue;
                }

                $wp_sh_config = $wp_sh_config[0];

                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $wp_sh_config['domain_name'];
                $temp_report['display_account_image'] = base_url('assets/images/wordpress.png');
                $post_category = isset($post_content['wpsh_selected_category']) 
                    ? (string) $post_content['wpsh_selected_category']
                    : '';

                $post_category = json_decode($post_category, true);


                $data = [
                    'post_title' => $post_content['title'],
                    'domain_name' => $wp_sh_config['domain_name'],
                    'user_key' => $wp_sh_config['user_key'],
                    'authentication_key' => $wp_sh_config['authentication_key'],
                    'post_category' => $post_category,
                ];                

                /* posting */
                if($post_type == 'image') {
                    $data['type'] = 'image';
                    $data['post_content'] = $post_content['message'];
                    $data['media_url'] = $post_content['image_url'];
                } else if($post_type == 'video') {
                    $data['type'] = 'video';
                    $data['media_url'] = $post_content['video_url'];
                    $data['post_content'] = $post_content['message'];
                } else if($post_type == 'text') {
                    $data['type'] = 'text';
                    $data['post_content'] = $post_content['message'];
                } else if($post_type == 'html') {
                    $data['type'] = 'html';    
                    $data['post_content'] = $post_content['rich_content'];
                }

                try {
                    $wordpress_status = $this->wordpress_self_hosted->post_to_wordpress_self_hosted($data);

                    if (is_array( $wordpress_status )) {
                        $wp_sh_url = $wordpress_status['url'];
                        $temp_report['report'] = $wp_sh_url;

                        array_push($posting_report['Wordpress_self_hosted'], $temp_report);
                    }
                } catch(Exception $e) {
                    $temp_report['report'] = $e->getMessage();
                    array_push($posting_report['Wordpress_self_hosted'], $temp_report);
                }
            } elseif(strpos($single_media, 'linkedin') !== false) {
                
                /* get account info & access tokens */
                $linkedin = $this->basic->get_data('linkedin_users_info', array('where' => array('id' => $social_media_info[1])));
                if(!isset($linkedin[0])) continue;
                $linkedin = $linkedin[0];

                $access_token = $linkedin['access_token'];
                $linkedin_id = $linkedin['linkedin_id'];
                
                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $linkedin['name'];
                $temp_report['display_account_image'] = $linkedin['profile_pic'];

                /* posting */
                if($post_type == 'text') {

                    $message = $post_content['message'];
                    
                    try {

                        $linkedin_status = $this->linkedin->text_post_to_linkedin($linkedin_id, $access_token, $message);

                        $temp_report['report'] = $linkedin_status;
                        array_push($posting_report['Linkedin'], $temp_report);
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Linkedin'], $temp_report);
                    }
                } else if($post_type == 'link') {

                    $link = $post_content['link'];
                    $title = $post_content['title'];
                    $message = $post_content['message'];
                    $thumbnail_url = $post_content['thumbnail_url'];
                    
                    try {

                        $linkedin_status = $this->linkedin->link_post_to_linkedin($linkedin_id, $access_token, $message, $title, $link);
    
                        $temp_report['report'] = $linkedin_status;
                        array_push($posting_report['Linkedin'], $temp_report);
                    } catch(Exception $e) {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Linkedin'], $temp_report);
                    }
                }
            } elseif(strpos($single_media, 'wordpress') !== false) {
                /* get accounts info & access tokens */
                $wordpress = $this->basic->get_data('wordpress_users_info', array( 'where' => array( 'id' => $social_media_info[1] ) ));
                if(!isset($wordpress[0])) continue;
                $wordpress = $wordpress[0];

                $access_token = $wordpress['access_token'];
                $blog_id = $wordpress['blog_id'];


                $categories = $wordpress['categories'];
                $title = $post_content['title'];
 
                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $wordpress['blog_url'];
                $temp_report['display_account_image'] = $wordpress['icon'];

                /* posting */
                if($post_type == 'image') {

                    $img = "<img src='{$post_content['image_url']}'>";
                    $msg = html_entity_decode($post_content['rich_content']);
                    $content = $img . "<br />" . $msg;


                    try {

                        $wordpress_status = $this->wp_org_poster->post_to_wporg($access_token, $blog_id, $categories, $title, $content, $tag);

                        $wp_post_url = $wordpress_status['URL'];
                        $temp_report['report'] = $wp_post_url;

                        array_push($posting_report['Wordpress'], $temp_report);
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Wordpress'], $temp_report);
                    }
                } else if($post_type == 'html') {

                    $content = html_entity_decode($post_content['rich_content']);

                    try {

                        $wordpress_status = $this->wp_org_poster->post_to_wporg($access_token, $blog_id, $categories, $title, $content, $tag);

                        $wordpress_url = $wordpress_status['URL'];
                        $temp_report['report'] = $wordpress_url;

                        array_push($posting_report['Wordpress'], $temp_report);
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Wordpress'], $temp_report);
                    }
                }
            } else if(strpos($single_media, 'blogger') !== false) {

                /* get accounts info & access tokens */
                $join = ['blogger_users_info' => 'blogger_blog_info.blogger_users_info_table_id=blogger_users_info.id'];
                $select = ['blogger_users_info.access_token as access_token', 'blogger_users_info.refresh_token as refresh_token', 'blogger_blog_info.blog_id as blog_id', 'blogger_blog_info.name as name'];
                $blogger = $this->basic->get_data('blogger_blog_info', array( 'where' => array( 'blogger_blog_info.id' => $social_media_info[1] ) ) , $select, $join);

                $access_token = $this->google_youtube_login->get_access_token_from_refresh_token($blogger[0]['refresh_token']);
                if(!isset($blogger[0])) continue;
                $blogger = $blogger[0];


                $blog_id = $blogger['blog_id'];
                $title = $post_content['title'];

                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $blogger['name'];
                $temp_report['display_account_image'] = $blogger['picture'];

                /* posting */
                if($post_type == 'image') {

                    $img = "<img src='{$post_content['image_url']}'>";
                    $msg = html_entity_decode($post_content['rich_content']);
                    $content = $img . "<br />" . $msg;

                    try {

                        $blogger_status = $this->google_youtube_login->post_to_blogger($blog_id, $access_token, $title, $content);

                        $blogger_url = $blogger_status['url'];
                        $temp_report['report'] = $blogger_url;

                        array_push($posting_report['Blogger'], $temp_report);
                    } catch(Exception $e) {

                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Blogger'], $temp_report);
                    }
                } else if($post_type == 'html') {

                    $content = html_entity_decode($post_content['rich_content']);

                    try {

                        $blogger_status = $this->google_youtube_login->post_to_blogger($blog_id, $access_token, $title, $content);

                        $blogger_url = $blogger_status['url'];
                        $temp_report['report'] = $blogger_url;

                        array_push($posting_report['Blogger'], $temp_report);
                    } catch(Exception $e) {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Blogger'], $temp_report);
                    }
                }
            } else if(strpos($single_media, 'youtube') !== false) {

                /* get accounts & access tokens */
                $temp_chanel_id = str_replace('youtube_channel_list-', '', $single_media);
               
                $where['where'] = array( 'youtube_channel_list.channel_id' => $temp_chanel_id );
                $select = array( 'youtube_channel_list.id as table_id', 'access_token', 'refresh_token', 'youtube_channel_list.channel_id', 'title' );
                $join = array( 'youtube_channel_info' => 'youtube_channel_list.channel_info_id=youtube_channel_info.id,left' );

                $channel_info = $this->basic->get_data('youtube_channel_list', $where, $select, $join);
                // echo "<pre>";print_r($this->db->last_query());exit;

                if(isset($channel_info[0])) {

                    $this->session->set_userdata('cronjob_upload_access_token', $channel_info[0]['access_token']);
                    $this->session->set_userdata('cronjob_upload_refresh_token', $channel_info[0]['refresh_token']);
                }
                if(!isset($channel_info[0])) continue;


                $title = $post_content['title'];
                $description = $post_content['message'];

                $file_name = str_replace(base_url() , "", $post_content['video_url']);
                $file_name = str_replace("upload/video/", "", $file_name);

                $category = "";
                $privacy_type = $post_content['privacy_type'];
                $tags = '';

                /* set report */
                $temp_report = [];
                $temp_report['display_name'] = $channel_info[0]['title'];
                $temp_report['display_account_image'] = $channel_info[0]['profile_image'];
                $uploaded_video_status = 'A service error occurred';

                /* posting */
                try {

                    $uploaded_video_status = $this->google_youtube_login->cronjob_upload_video_to_youtube($title, $description, $file_name, $tags, $category, $privacy_type);
                    
                    $temp_report['report'] = 'https://www.youtube.com/watch?v=' . $uploaded_video_status;
                    array_push($posting_report['Youtube'], $temp_report);
                } catch(Exception $e) {

                    $temp_report['report'] = $e->getMessage();
                    array_push($posting_report['Youtube'], $uploaded_video_status);
                }
            } else if(strpos($single_media, 'tumblr') !== false) {
                $tumblr = $this->basic->get_data('rx_tumblr_autopost', array(
                    'where' => array(
                        'id' => $social_media_info[1]
                    )
                ));
                if(!isset($tumblr[0])) continue;
                $auth_token = $tumblr[0]['auth_token'];
                $auth_token_secret = $tumblr[0]['auth_token_secret'];
                $auth_varifier = $tumblr[0]['auth_varifier'];
                $name = $tumblr[0]['user_name'];
                $temp_report = [];
                $temp_report['display_name'] = $tumblr[0]['user_name'];
                // $temp_report['display_account_image'] = $tumblr[0][''];
                if($post_type == 'text')
                {
                    $title = $post_content['title'];
                    $message = $post_content['message'];
                    try
                    {
                        $tumblr_status = $this->tumblr->text_posts($auth_token, $auth_token_secret, $auth_varifier, $name, $title, $message);
                        if(!isset($twitter_status['error']))
                        {
                            $tumblr_url = "https://" . $name . ".tumblr.com/post/" . $tumblr_status['id'];
                            $temp_report['report'] = $tumblr_url;
                            array_push($posting_report['Tumblr'], $temp_report);
                        }
                        // else{
                        //     $temp_report['report'] = $twitter_status['error'];
                        //     array_push($posting_report['Twitter'], $temp_report);
                        // }
                    }
                    catch(Exception $e)
                    {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Tumblr'], $temp_report);
                    }
                }
                else if($post_type == 'image')
                {
                    $image_url = $post_content['image_url'];
                    $message = $post_content['message'];
                    try
                    {
                        $tumblr_status = $this->tumblr->photo_posts($auth_token, $auth_token_secret, $auth_varifier, $name, $image_url, $message);
                        // $twitter_status = json_decode($twitter_status,true);
                        if(!isset($tumblr_status['error']))
                        {
                            $tumblr_url = "https://" . $name . ".tumblr.com/post/" . $tumblr_status['id'];
                            $temp_report['report'] = $tumblr_url;
                            array_push($posting_report['Tumblr'], $temp_report);
                        }
                        else
                        {
                            $temp_report['report'] = 'Invalid URL';
                            array_push($posting_report['Tumblr'], $temp_report);
                        }
                    }
                    catch(Exception $e)
                    {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Tumblr'], $temp_report);
                    }
                }
                else if($post_type == 'video')
                {
                    $video_url = $post_content['video_url'];
                    $title = $post_content['title'];
                    $message = $post_content['message'];
                    try
                    {
                        $tumblr_status = $this->tumblr->video_posts($auth_token, $auth_token_secret, $auth_varifier, $name, $video_url, $title, $message);
                        if(!isset($tumblr_status['error']))
                        {
                            $tumblr_url = "https://" . $name . ".tumblr.com/post/" . $tumblr_status['id'];
                            $temp_report['report'] = $tumblr_url;
                            array_push($posting_report['Tumblr'], $temp_report);
                        }
                        else
                        {
                            $temp_report['report'] = 'Invalid Link';
                            array_push($posting_report['Tumblr'], $temp_report);
                        }
                    }
                    catch(Exception $e)
                    {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Tumblr'], $temp_report);
                    }

                    // print_r($tumblr_status);
                    // echo "<br />";
                    // print_r($posting_report);
                    // die();
                    // echo "<hr>";
                }
                else if($post_type == 'link')
                {
                    $link = $post_content['link'];
                    $thumbnail_url = $post_content['thumbnail_url'];
                    $message = $post_content['message'];
                    try
                    {
                        $tumblr_status = $this->tumblr->link_posts($auth_token, $auth_token_secret, $auth_varifier, $name, $link, $thumbnail_url, $message);
                        // $twitter_status = json_decode($twitter_status,true);
                        if(!isset($tumblr_status['error']))
                        {
                            $tumblr_url = "https://" . $name . ".tumblr.com/post/" . $tumblr_status['id'];
                            $temp_report['report'] = $tumblr_url;
                            array_push($posting_report['Tumblr'], $temp_report);
                        }
                        else
                        {
                            $temp_report['report'] = 'Invalid Link';
                            array_push($posting_report['Tumblr'], $temp_report);
                        }
                    }
                    catch(Exception $e)
                    {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Tumblr'], $temp_report);
                    }
                }
                else if($post_type == 'html')
                {
                    $title = $post_content['title'];
                    $rich_content = $post_content['rich_content'];
                    try
                    {
                        $tumblr_status = $this->tumblr->text_posts($auth_token, $auth_token_secret, $auth_varifier, $name, $title, $rich_content);
                        if(!isset($twitter_status['error']))
                        {
                            $tumblr_url = "https://" . $name . ".tumblr.com/post/" . $tumblr_status['id'];
                            $temp_report['report'] = $tumblr_url;
                            array_push($posting_report['Tumblr'], $temp_report);
                        }
                        // else{
                        //     $temp_report['report'] = $twitter_status['error'];
                        //     array_push($posting_report['Twitter'], $temp_report);
                        // }
                    }
                    catch(Exception $e)
                    {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Tumblr'], $temp_report);
                    }
                }
            } else if(strpos($single_media, 'medium') !== false) {

                $medium = $this->basic->get_data("medium_users_info",['where'=>['id'=>$social_media_info[1]]]);

                if(!isset($medium[0])) continue;
                $medium = $medium[0];

                $medium_id = $medium['medium_id'];
                // $refresh_token = $medium['refresh_token'];
                $refresh_token = $medium['access_token'];

                $title = $post_content['title'];
                $tag = $post_content['tag'];
                $temp_tag = explode(',', $tag);
                $final_tag = '';

                foreach($temp_tag as $single_tag) {
                    $final_tag = $final_tag . '#' . $single_tag . ' ';
                }

                $tag = $final_tag;

                $temp_report = [];
                $temp_report['display_name'] = $medium['name'];
                $temp_report['display_account_image'] = $medium['profile_pic'];

                if($post_type == "image") {

                    $img = "<img src='{$post_content['image_url']}'>";
                    $content = $img . "<br />" . html_entity_decode($post_content['rich_content']);

                    try
                    {
                        $medium_status = $this->medium->create_post($medium_id, $refresh_token, $title, $content, $tag);
                        $temp_report['report'] = isset($medium_status['data']['url']) ? $medium_status['data']['url']:$this->lang->line('Unknown Error occured from Medium API, please try after some time.');
                        array_push($posting_report['Medium'], $temp_report);
                    }
                    catch(Exception $e)
                    {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Medium'], $temp_report);
                    }
                } else if($post_type == 'html') {

                    $rich_content = html_entity_decode($post_content['rich_content']);
                    try
                    {
                        $medium_status = $this->medium->create_post($medium_id, $refresh_token, $title, $rich_content, $tag);
                        $temp_report['report'] = isset($medium_status['data']['url']) ? $medium_status['data']['url']:$this->lang->line('Unknown Error occured from Medium API, please try after some time.');
                        array_push($posting_report['Medium'], $temp_report);
                    }
                    catch(Exception $e)
                    {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Medium'], $temp_report);
                    }

                } else if($post_type == 'text') {

                    $message = $post_content['message'];
                    try
                    {
                        $medium_status = $this->medium->create_post($medium_id, $refresh_token, $title, $message, $tag);
                        $temp_report['report'] = isset($medium_status['data']['url']) ? $medium_status['data']['url']:$this->lang->line('Unknown Error occured from Medium API, please try after some time.');
                        array_push($posting_report['Medium'], $temp_report);
                    }
                    catch(Exception $e)
                    {
                        $temp_report['report'] = $e->getMessage();
                        array_push($posting_report['Medium'], $temp_report);
                    }  
                }
            }
        }
        return $posting_report;
    }
}
