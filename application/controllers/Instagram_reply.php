<?php
require_once("application/controllers/Home.php"); // loading home controller
class Instagram_reply extends Home
{
    public $addon_data = array();

    public function __construct()
    {
        parent::__construct();
        $this->member_validity();
        $this->user_id = $this->session->userdata('user_id'); // user_id of logged in user, we may need it

        $instagram_reply_enable_disable = $this->config->item('instagram_reply_enable_disable');
        if($instagram_reply_enable_disable != 1)
        {
          redirect('home/login_page', 'location');
          exit();
        }

        $function_name=$this->uri->segment(2);
        if($function_name!="webhook_callback" && $function_name!="business_discovery" && $function_name != "hashtag_search_by_cronjob") 
        {
            // all addon must be login protected
            //------------------------------------------------------------------------------------------
            if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');          
            // if you want the addon to be accessed by admin and member who has permission to this addon
            //-------------------------------------------------------------------------------------------
            if(isset($addondata['module_id']) && is_numeric($addondata['module_id']) && $addondata['module_id']>0)
            {
               if($this->session->userdata('user_type') != 'Admin' && !in_array(278,$this->module_access) && !in_array(279,$this->module_access))
                {
                    redirect('home/login_page', 'location');
                    exit();
                }
            }
        }
    }


    // ====================================== New Started ===================================
    public function index()
    {
      $this->get_account_lists();
    }

    public function get_periodic_time()
    {

        $all_periodic_time= array(
        
        '5' =>'every 5 mintues',
        '10' =>'every 10 mintues',
        '15' =>'every 15 mintues',
        '30' =>'every 30 mintues',
        '60' =>'every 1 hours',
        '120'=>'every 2 hours',
        '300'=>'every 5 hours',
        '600'=>'every 10 hours',
        '900'=>'every 15 hours',
        '1200'=>'every 20 hours',
        '1440'=>'every 24 hours',
        '2880'=>'every 48 hours',
        '4320'=>'every 72 hours',
       );
        return $all_periodic_time;
    }

    public function get_account_lists()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
          redirect('home/login_page', 'location');
        }

        if($this->session->userdata('selected_global_media_type') == "fb") {
          redirect("comment_automation/index");
        }

       $data = [];
       $data['body']  = "instagram_reply/comment_reply/auto_reply_page_list";

       $data['auto_comment_template'] = $this->basic->get_data('auto_comment_reply_tb',array("where"=>array('user_id'=>$this->user_id)),array('id','template_name'));
       $data["time_zone"]= $this->_time_zone_list();
       $data["periodic_time"] = $this->get_periodic_time();
       
       if($this->addon_exist('instagram_reply_enhancers'))
       {
        if($this->session->userdata('user_type') == 'Admin' || in_array(278,$this->module_access))
          $data['commnet_hide_delete_addon'] = 1;
        else
          $data['commnet_hide_delete_addon'] = 0;
       }
       else
        $data['commnet_hide_delete_addon'] = 0;

       $data['page_title'] = $this->lang->line("Instagram Comment Automation Campaign");

       $table = "facebook_rx_fb_page_info";
       $where['where'] = ['user_id'=>$this->user_id,"bot_enabled"=>"1","has_instagram"=>"1","facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info")];

       $account_info = array();

       $account_list = $this->basic->get_data($table,$where);

       if(!empty($account_list))
       {
           $i = 1;
           $selected_page_id = $this->session->userdata('selected_global_page_table_id');
           foreach($account_list as $value)
           {
               if($value['id'] == $selected_page_id)
               {
                   $account_info[0]['id'] = $value['id'];
                   $account_info[0]['page_profile'] = $value['page_profile'];
                   $account_info[0]['insta_username'] = $value['insta_username'];
                   $account_info[0]['instagram_business_account_id'] = $value['instagram_business_account_id'];
               }
               else
               {                    
                   $account_info[$i]['id'] = $value['id'];
                   $account_info[$i]['page_profile'] = $value['page_profile'];
                   $account_info[$i]['insta_username'] = $value['insta_username'];
                   $account_info[$i]['instagram_business_account_id'] = $value['instagram_business_account_id'];
               }
               $i++;

           }
       }
       ksort($account_info);

       $data['account_info'] = $account_info;

       $data['instagram_bot_exist'] = false;
       if(addon_exist($module_id=320,$addon_unique_name="instagram_bot")) {
         $data['instagram_bot_exist'] = true;
       }

       $this->_viewcontroller($data);
    }

    public function get_account_details()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) exit;
        $page_table_id = $this->input->post('page_table_id',true);
        $this->session->set_userdata('selected_global_page_table_id',$page_table_id);

        $join = ['facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left'];
        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_page_info.id"=>$page_table_id,"facebook_rx_fb_page_info.user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),['facebook_rx_fb_page_info.*','access_token'],$join);

        $middle_column_content = '';
        $right_column_content = '';
        $error_msg = '';

        if(!empty($page_info)) {

            
            $autoreply_info = $this->basic->get_data('instagram_reply_autoreply', array('where' => array('page_info_table_id' => $page_table_id, 'report_type' => 'post')), array('last_reply_time','auto_comment_reply_count','post_id','id','post_pause_play'), '', '', '', 'last_reply_time desc');
            $total_autoreply_enabled_post = count($autoreply_info);

            $autocomment_info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('page_info_table_id'=>$page_info[0]['id'],"social_media_type"=>"Instagram")),'','','','','last_reply_time desc');
            $total_autocomment_enabled_post = count($autocomment_info);
            $autocomment_count = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('page_info_table_id'=>$page_info[0]['id'],"social_media_type"=>"Instagram")),array('sum(auto_comment_count) as auto_comment_count'));
            $total_autocomment_count = isset($autocomment_count[0]['auto_comment_count'])?$autocomment_count[0]['auto_comment_count']:0;

            $existing_data_comment = array();           
            if(!empty($autocomment_info))
            {
                foreach($autocomment_info as $value)
                {
                    $existing_data_comment[$value['post_id']]['id'] = $value['id'];
                    $existing_data_comment[$value['post_id']]['pause_play'] = $value['auto_private_reply_status'];
                }
            }

            $total_autoreply_count = 0;
            foreach($autoreply_info as $val)
                $total_autoreply_count += $val['auto_comment_reply_count'];

            if(!empty($autoreply_info) && $autoreply_info[0]['last_reply_time']!='0000-00-00 00:00:00') {
                $last_reply_time = date_time_calculator($autoreply_info[0]['last_reply_time'],true);
            }
            else {
                $last_reply_time = $this->lang->line('Not replied yet');
            }

            if(!empty($autocomment_info) && $autocomment_info[0]['last_reply_time']!='0000-00-00 00:00:00') {
                $last_reply_time_comment = date_time_calculator($autocomment_info[0]['last_reply_time'],true);
            }
            else {
                $last_reply_time_comment = $this->lang->line('Not replied yet');
            }

            $dis_start_button = '';
            if($page_info[0]['bot_enabled'] == "1") {
                $dis_start_button = '<a href="#" class="float-right disable_reply" table_id="'.$page_table_id.'" pause_play="pause" data-toggle="tooltip" title="'.$this->lang->line("Stop Reply").'" alt-content="'.$this->lang->line("Do you really want to stop reply?").'" ><i class="fas fa-toggle-on font_size_18px"></i></a>';;
            } else {
                $dis_start_button = '<a href="#" class="float-right restart_reply" table_id="'.$page_table_id.'" pause_play="play" data-toggle="tooltip" title="'.$this->lang->line("Re-start Reply").'" alt-content="'.$this->lang->line("Do you really want to re-start reply?").'" ><i class="fas fa-toggle-off font_size_18px"></i></a>';

            }


            $full_and_mentions_enabled_pages = $this->basic->get_data('instagram_reply_autoreply', array('where' => array('page_info_table_id' => $page_table_id,'autoreply_type !=' => 'post_autoreply','user_id'=>$this->user_id)),array('page_info_table_id','id','mentions_pause_play','autoreply_type','full_pause_play','mentions_pause_play'));

            $full_account_enabled_page_id = $mention_reply_enabled_page_id = $full_account_enable_page_table_id = $mention_reply_enable_page_table_id = $mention_reply_pause_play = $full_account_pause_play = '';

            foreach ($full_and_mentions_enabled_pages as $full_mention_values) {

              if($full_mention_values['autoreply_type'] == "account_autoreply") {
                $full_account_enabled_page_id = $full_mention_values['page_info_table_id'];
                $full_account_enable_page_table_id = $full_mention_values['id'];
                $full_account_pause_play = $full_mention_values['full_pause_play'];
              } 

              if($full_mention_values['autoreply_type'] == "mentions_autoreply") {
                $mention_reply_enabled_page_id = $full_mention_values['page_info_table_id'];
                $mention_reply_enable_page_table_id = $full_mention_values['id'];
                $mention_reply_pause_play = $full_mention_values['mentions_pause_play'];
              }
            }


            if($page_table_id == $full_account_enabled_page_id) { // if full account enabled

              $full_enabled_or_not = $this->lang->line("Enabled");

              $full_account_enabled_button = '
              <div class="dropdown-menu mini_dropdown text-center width_208px">
                <a title="'.$this->lang->line("Campaign Report").'" data-toggle="tooltip" data-placement="top" href="'.base_url("instagram_reply/instagram_autoreply_report/full/".$page_table_id.'/'.$full_account_enable_page_table_id).'" class="btn btn-circle btn-outline-primary full_campaign_report"><i class="fas fa-eye"></i></a>
                <a title="'.$this->lang->line("Edit Campaign").'" table_id="'.$full_account_enable_page_table_id.'" data-toggle="tooltip" data-placement="top" href="#" class="btn btn-circle btn-outline-warning edit_enable_full_auto_commnet"><i class="fas fa-edit"></i></a>';

                  if($full_account_pause_play == 'play') {

                    $full_account_enabled_button .= '<a title="'.$this->lang->line("Pause Campaign").'" data-toggle="tooltip" data-placement="top" href="#" table_id="'.$full_account_enable_page_table_id.'" to_do="pause" class="btn btn-circle btn-outline-secondary pause_play_button"><i class="fas fa-pause"></i></a>';

                  } else {

                    $full_account_enabled_button .= '<a title="'.$this->lang->line("Play Campaign").'" data-toggle="tooltip" data-placement="top" href="#" table_id="'.$full_account_enable_page_table_id.'" to_do="play" class="btn btn-circle btn-outline-dark pause_play_button"><i class="fas fa-play"></i></a>';

                  }

                $full_account_enabled_button .= '<a title="'.$this->lang->line("Delete Campaign").'" data-toggle="tooltip" data-placement="top" href="" table_id="'.$full_account_enable_page_table_id.'" page_info_table_id="'.$page_table_id.'" autoreply_type="account_autoreply" class="btn btn-circle btn-outline-danger delete_full_campaign"><i class="fas fa-trash-alt"></i></a>
              </div>';

            } else {

              $full_enabled_or_not = $this->lang->line("Not Enabled");

              $full_account_enabled_button = '
                <div class="dropdown-menu mini_dropdown text-center width_67px">
                  <a title="'.$this->lang->line("enable auto comment reply").'" table_id="'.$page_table_id.'" data-toggle="tooltip" data-placement="top" href="#" class="btn btn-circle btn-outline-primary enable_full_auto_commnet"><i class="fas fa-plug"></i></a>
                </div>';
            }



            if($page_table_id == $mention_reply_enabled_page_id) {

              $mention_enabled_or_not = $this->lang->line("Enabled");

              $mention_reply_enabled_button = '
              <div class="dropdown-menu mini_dropdown text-center width_208px">
                <a title="'.$this->lang->line("Campaign Report").'" data-toggle="tooltip" data-placement="top" href="'.base_url("instagram_reply/instagram_autoreply_report/mention/".$page_table_id.'/'.$mention_reply_enable_page_table_id).'" class="btn btn-circle btn-outline-primary mention_reply_report"><i class="fas fa-eye"></i></a>
                <a title="'.$this->lang->line("Edit Campaign").'" table_id="'.$mention_reply_enable_page_table_id.'" data-toggle="tooltip" data-placement="top" href="#" class="btn btn-circle btn-outline-warning edit_enable_mentions_auto_commnet"><i class="fas fa-edit"></i></a>';

                if($mention_reply_pause_play == 'play') {
                  $mention_reply_enabled_button .= '<a title="'.$this->lang->line("Pause Campaign").'" data-toggle="tooltip" data-placement="top" href="#" table_id="'.$mention_reply_enable_page_table_id.'" to_do="pause" class="btn btn-circle btn-outline-secondary mentions_pause_play_button"><i class="fas fa-pause"></i></a>';

                } else {

                  $mention_reply_enabled_button .= '<a title="'.$this->lang->line("Play Campaign").'" data-toggle="tooltip" data-placement="top" href="#" table_id="'.$mention_reply_enable_page_table_id.'" to_do="play" class="btn btn-circle btn-outline-dark mentions_pause_play_button"><i class="fas fa-play"></i></a>';
                  
                }
              
                $mention_reply_enabled_button .= '<a title="'.$this->lang->line("Delete Campaign").'" data-toggle="tooltip" data-placement="top" href="#" table_id="'.$mention_reply_enable_page_table_id.'" page_info_table_id="'.$page_table_id.'" autoreply_type="mentions_autoreply" class="btn btn-circle btn-outline-danger delete_mentions_campaign"><i class="fas fa-trash-alt"></i></a>
              </div>';

            } else {

              $mention_enabled_or_not = $this->lang->line("Not Enabled");

              // first time enabling button
              $mention_reply_enabled_button = '
                <div class="dropdown-menu mini_dropdown text-center width_67px">
                  <a title="'.$this->lang->line("enable auto comment reply").'" table_id="'.$page_table_id.'" data-toggle="tooltip" data-placement="top" href="#" class="btn btn-circle btn-outline-primary enable_mentions_auto_commnet"><i class="fas fa-plug"></i></a>
                </div>';
            }

            $middle_column_content .= '
                <div class="card main_card">
                  <div class="card-header">
                    <h4><i class="fab fa-instagram"></i> <a target="_BLANK" href="https://www.instagram.com/'.$page_info[0]['insta_username'].'">'.$page_info[0]['insta_username'].'</a></h4>
                  </div>
                  <div class="card-body">
                    <div class="summary">             
                      <div class="summary-item mb-5">
                        <ul class="list-unstyled list-unstyled-border">
                          <li class="media">                    

                            <img class="mr-3 rounded" width="50" src="../assets/img/icon/reply.png">
                            
                            <div class="media-body">
                              <div class="media-right">
                                  <a class="btn btn-outline-primary btn-circle" href="'.base_url('comment_automation/all_auto_comment_report').'/'.$page_table_id.'/0/1" data-toggle="tooltip" data-placement="bottom" title="'.$this->lang->line('See Result').'">
                                    <i class="fa fa-eye"></i>
                                  </a>
                              </div>
                              <div class="media-title">'. $this->lang->line('Auto Comment').'</div>
                              <div class="text-muted text-small">'. $this->lang->line("Enabled").' : <b>'.$total_autocomment_enabled_post.'</b><div class="bullet"></div>'.$this->lang->line("Comment").' : <b>'.$total_autocomment_count.'</b> <div class="bullet"></div> '.$last_reply_time_comment.'</div>
                            </div>
                          </li>
                          <li class="media">                    

                            <img class="mr-3 rounded" width="50" src="../assets/img/icon/response.png">
                            
                            <div class="media-body">
                              <div class="media-right">
                                  <a class="btn btn-outline-success btn-circle" href="'.base_url('instagram_reply/instagram_autoreply_report/post').'/'.$page_table_id.'" data-toggle="tooltip" data-placement="bottom" title="'.$this->lang->line('See Result').'">
                                    <i class="fa fa-eye"></i>
                                  </a>
                              </div>
                              <div class="media-title">'. $this->lang->line('Auto Comment Reply').'</div>
                              <div class="text-muted text-small">'. $this->lang->line("Enabled").' : <b>'.$total_autoreply_enabled_post.'</b><div class="bullet"></div>'. $this->lang->line("Response").' : <b>'.$total_autoreply_count.'</b> <div class="bullet"></div> '.$last_reply_time.'</div>
                            </div>
                          </li>

                          ';

            if($this->addon_exist('instagram_reply_enhancers'))
            
            if($this->session->userdata('user_type') == 'Admin' || in_array(278,$this->module_access))
            {
              $middle_column_content .='
                            <li class="media">
                              <div class="page_thumbnail">
                                <img alt="image" class="mr-3 rounded" width="50" src="../assets/img/icon/page.png">
                              </div>
                              <div class="media-body"> 
                                <div class="media-right">
                                  <div class="dropdown d-inline dropleft text-center">
                                    <button class="btn btn-outline-info dropdown-toggle no_caret btn-circle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      <i class="fa fa-briefcase"></i>
                                    </button>'.$full_account_enabled_button.'
                                  </div>
                                </div>
                                <div class="media-title">'.$this->lang->line('Full Account Comment Reply').'</div>
                                <div class="text-small text-muted">'.$this->lang->line('Manage Full Account Reply').' <div class="bullet"></div> '.$full_enabled_or_not.'</div>
                              </div>
                            </li>

                            <li class="media">
                              <div class="page_thumbnail">
                                <img alt="image" class="mr-3 rounded" width="50" src="../assets/img/icon/tag.png">
                              </div>
                              <div class="media-body"> 
                                <div class="media-right">
                                  <div class="dropdown d-inline dropleft text-center">
                                    <button class="btn btn-outline-warning dropdown-toggle no_caret btn-circle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      <i class="fa fa-briefcase"></i>
                                    </button>'.$mention_reply_enabled_button.'
                                  </div>
                                </div>
                                <div class="media-title">'.$this->lang->line('Mention Reply').'</div>
                                <div class="text-small text-muted">'.$this->lang->line('Manage Mention Reply').' <div class="bullet"></div> '.$mention_enabled_or_not.'</div>
                              </div>
                            </li>

                            <a  class="tagged_media text-decoration-none pointer" page_table_id="'.$page_table_id.'">
                              <li class="media">
                                  <div class="page_thumbnail">
                                    <img alt="image" class="mr-3 rounded" width="50" src="../assets/img/icon/single_tag.png">
                                  </div>
                                  <div class="media-body"> 
                                    <div class="media-right">
                                        <button class="btn btn-outline-danger btn-circle p-0 tagged_media_button" page_table_id="'.$page_table_id.'" type="button" data-toggle="tooltip" data-placement="bottom" title="'.$this->lang->line('See Result').'">
                                          <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="media-title">'.$this->lang->line('Tagged Media').'</div>
                                    <div class="text-small text-muted">'.$this->lang->line('Get the media objects in which Business has been tagged.').'</div>
                                  </div>
                              </li>
                            </a>
                            ';

            }

            $middle_column_content .='
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>';


                // ============= right column section ====================

                $instagram_account_id = $page_info[0]['instagram_business_account_id'];
                $page_access_token = $page_info[0]['page_access_token'];
                $user_access_token = $page_info[0]['access_token'];
                $error_msg = '';

                $existing_data = array();
                if (!empty($autoreply_info)) {
                    foreach ($autoreply_info as $value) {
                        $existing_data[$value['post_id']]['id'] = $value['id'];
                        $existing_data[$value['post_id']]['post_pause_play'] = $value['post_pause_play'];
                    }
                }

                $this->load->library("fb_rx_login");

                try {
                    $this->fb_rx_login->app_initialize($this->session->userdata("fb_rx_login_database_id"));
                    $post_list = $this->fb_rx_login->get_postlist_from_instagram_account($instagram_account_id, $user_access_token);

                    if(isset($post_list['data']) && empty($post_list['data'])){
                        $error_msg = '
                            <div class="card no_shadow" id="nodata">
                              <div class="card-body">
                                <div class="empty-state">
                                  <img class="img-fluid height_200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                                  <h2 class="mt-0">'.$this->lang->line("We could not find any data.").'</h2>
                                </div>
                              </div>
                            </div>';
                    }
                    else if(!isset($post_list['data']))
                    {
                        $error_msg = '
                            <div class="card no_shadow" id="nodata">
                              <div class="card-body">
                                <div class="empty-state">
                                  <img class="img-fluid height_200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                                  <h2 class="mt-0">'.$this->lang->line("Something went wrong, please try again after some time.").'</h2>
                                </div>
                              </div>
                            </div>';
                    }
                    else
                    {
                        $str='';
                        $i = 1;

                        $right_column_content = '
                          <div class="card main_card">
                              <div class="card-header">
                               <div class="col-7 col-md-6 padding-0">
                                <h4><i class="fas fa-rss"></i> '.$this->lang->line("Latest Posts").'</h4>
                               </div>
                               <div class="col-5 col-md-6 padding-0">
                                  <input type="text" class="form-control float-right" onkeyup="search_in_ul(this,\'post_list_ul\')" placeholder="'.$this->lang->line("Search...").'">
                               </div>


                              </div>
                              <div class="card-body">
                                <div class="makeScroll">
                                  <div class="text-center" id="sync_commenter_info_response"></div>
                                  <ul class="list-unstyled list-unstyled-border" id="post_list_ul">';

                                  foreach($post_list['data'] as $value)
                                  {     
                                      $caption = isset($value['caption']) ? $value['caption'] : '';
                                      // need to check mb is enabled or not
                                      if(mb_strlen($caption) >= 61)
                                          $caption = mb_substr($caption, 0, 59).'...';
                                      else $caption = $caption;

                                      $thumbnail = "";
                                      $media_url = isset($value['media_url']) ? $value['media_url'] : "";

                                      $icon='<i class="fa fa-image"></i>';
                                      if($value['media_type'] == "CAROUSEL_ALBUM") $icon='<i class="fa fa-images"></i>';
                                      else if ($value['media_type'] == "VIDEO") $icon='<i class="fa fa-youtube"></i>';

                                      if ($value['media_type'] == "IMAGE" || $value['media_type'] == "CAROUSEL_ALBUM") 
                                      {
                                          $thumbnail = $media_url;
                                      } 

                                      if($thumbnail=="") {
                                        $thumbnail=base_url('assets/img/avatar/avatar-1.png');
                                      }


                                      if (array_key_exists($value['id'], $existing_data)) {

                                          $button = "<a class='pointer dropdown-item has-icon edit_reply_info orange' table_id='".$existing_data[$value['id']]['id']."'><i class='fas fa-edit'></i> {$this->lang->line("edit auto comment reply")}</a>";

                                          $button .= "<a class='pointer dropdown-item has-icon ig_view_report blue' post_id='".$value['id']."' reply_type='post' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-eye'></i> {$this->lang->line("view comment reply report")}</a>";

                                          if($existing_data[$value['id']]['post_pause_play']=='play')
                                            $button .= "<a class='pointer dropdown-item has-icon ig_pause_campaign_info dark' to_do='pause' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-pause'></i> {$this->lang->line("pause comment reply campaign")}</a>";
                                          else
                                            $button .= "<a class='pointer dropdown-item has-icon ig_pause_campaign_info green' to_do='play' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-play'></i> {$this->lang->line("start comment reply campaign")}</a>";

                                          $button .= "<a class='pointer dropdown-item has-icon ig_delete_post_report red' autoreply_type='post_autoreply' page_info_table_id='".$page_table_id."' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-trash-alt'></i> {$this->lang->line("delete comment reply report")}</a>";

                                      } else {

                                          $button = "<a class='pointer dropdown-item has-icon enable_auto_commnet blue' manual_enable='no' page_table_id='" . $page_table_id . "' post_id='" . $value['id'] . "'><i class='fas fa-check-circle'></i> {$this->lang->line("enable auto comment reply")}</a>";

                                          
                                      }

                                      // auto comment section
                                      $permalink_url = isset($value['permalink']) ? $value['permalink'] : '';
                                      if(array_key_exists($value['id'], $existing_data_comment))
                                      {
                                        $button .= "<a class='pointer dropdown-item has-icon edit_reply_info_template orange' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fas fa-edit'></i> {$this->lang->line("edit auto comment")}</a>";

                                        $button .= "<a class='pointer dropdown-item has-icon autocomment_view_report blue' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-eye'></i> {$this->lang->line("view auto comment report")}</a>";
                                        if($existing_data_comment[$value['id']]['pause_play']=='0' || $existing_data_comment[$value['id']]['pause_play']=='1')
                                          $button .= "<a class='pointer dropdown-item has-icon autocomment_pause_campaign_info dark' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-pause'></i> {$this->lang->line("pause auto comment campaign")}</a>";
                                        else
                                          $button .= "<a class='pointer dropdown-item has-icon autocomment_play_campaign_info green' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-play'></i> {$this->lang->line("start auto comment campaign")}</a>";

                                        $button .= "<a class='pointer dropdown-item has-icon autocomment_delete_report red' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-trash-alt'></i> {$this->lang->line("delete auto comment report")}</a>";
                                      } 
                                      else
                                        $button .= "<a class='pointer dropdown-item has-icon enable_auto_commnet_template blue' manual_enable_template='no' permalink_url='".$permalink_url."' template_type='instagram' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fas fa-check-circle'></i> {$this->lang->line("enable auto comment")}</a>";

                                      $button .= "<a class='pointer dropdown-item has-icon instant_comment red' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fas fa-comment'></i> {$this->lang->line("Leave a comment now")}</a>";


                                      $comment_enabled_or_not = '';
                                      if($this->addon_exist('instagram_reply_enhancers'))
                                        if($this->session->userdata('user_type') == 'Admin' || in_array(278,$this->module_access))
                                        {
                                          $button .= "<a class='pointer dropdown-item has-icon check_all_comments' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fas fa-comments'></i> {$this->lang->line("Check all comments")}</a>";

                                          if($value['is_comment_enabled'] == 1)
                                          {
                                            $button .= "<a class='pointer dropdown-item has-icon enable_disable_comments' enable_or_disable='disable' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fas fa-times-circle red'></i> {$this->lang->line("Disable comments on Instagram")}</a><div class='dropdown-divider'></div>";
                                            $comment_enabled_or_not = '<i class="fas fa-check-circle blue" data-toggle="tooltip" title="'.$this->lang->line('Comment Enabled on Instagram').'" data-placement="right"></i>';
                                          }
                                          else
                                          {
                                            $button .= "<a class='pointer dropdown-item has-icon enable_disable_comments' enable_or_disable='enable' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fas fa-check-circle blue'></i> {$this->lang->line("Enable comments on Instagram")}</a><div class='dropdown-divider'></div>";
                                            $comment_enabled_or_not = '<i class="fas fa-times-circle red" data-toggle="tooltip" title="'.$this->lang->line('Comment Disabled on Instagram').'" data-placement="right"></i>';
                                          }
                                        }
                                        else
                                        $button .= "<div class='dropdown-divider'></div>";                                          
                                      else
                                        $button .= "<div class='dropdown-divider'></div>";

                                      $button .= "<a class='pointer dropdown-item has-icon media_insights black' page_table_id='" . $page_table_id . "' post_id='" . $value['id'] . "'><i class='fas fa-chart-bar'></i> {$this->lang->line("analytics")}</a>";

                                      $post_created_at =isset($value['timestamp'])? $value['timestamp']:"";

                                      $post_created_at = $post_created_at." UTC";
                                      $post_created_at=date("d M y H:i",strtotime($post_created_at));

                                      $i++;

                                      $right_column_content .= '
                                        <li class="media">
                                          <div class="avatar-item">
                                            <img alt="image border" src="'.$thumbnail.'" width="70" height="70" data-toggle="tooltip" title="'.date_time_calculator($post_created_at,true).'">
                                            <div class="dropdown dropright avatar-badge">
                                                <span class="dropdown-toggle set_cam_by_post pointer blue" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cog"></i>
                                                </span>
                                                <div class="dropdown-menu large">
                                                    '.$button.'
                                                </div>
                                            </div>
                                          </div>
                                          <div class="media-body">
                                            <div class="media-title"><a href="'.$permalink_url.'" target="_BLANK" >'.$value['id'].'</a> '.$comment_enabled_or_not.'</div>
                                            <span class="text-small"><i class="fas fa-clock"></i> '.date_time_calculator($post_created_at,true).' </span> : 
                                            <span class="text-small text-muted text-justify">'.$caption.'</span>
                                            <div class="text-small text-muted text-justify"><i class="fas fa-heart red"></i> <b>'.$value["like_count"].'</b>&nbsp;&nbsp;&nbsp;<i class="fas fa-comment blue"></i> <b>'.$value["comments_count"].'</b></div>
                                          </div>
                                        </li>';
                                  }


                        $right_column_content .= '
                                  </ul>
                              </div>
                            </div>
                            <script src="'.base_url().'assets/js/system/tooltip_popover.js"></script>
                            ';

                        if($this->session->userdata("is_mobile")=='0')
                        $right_column_content .= '<script src="'.base_url().'assets/js/system/make_scroll.js"></script>';

                    }

                } catch (Exception $e) {

                  $error_msg = '
                    <div class="card" id="nodata">
                      <div class="card-body">
                        <div class="empty-state">
                          <img class="img-fluid height_200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                          <h2 class="mt-0">'.$e->getMessage().'</h2>
                        </div>
                      </div>
                    </div>';
                    
                }
        }
        // $error_msg = '';

        if($right_column_content != '' && $error_msg == '')
          $response['right_column_content'] = $right_column_content;
        else
          $response['right_column_content'] = $error_msg;

        $response['middle_column_content'] = $middle_column_content;

        $autoreply_templates = "";
        $template_info = $this->basic->get_data('instagram_reply_template',['where'=>['user_id'=>$this->user_id,'page_id'=>$page_table_id]]);
        foreach ($template_info as $value) {
          $autoreply_templates .= "<option value='".$value['id']."'>".$value['auto_reply_campaign_name']."</option>";
        }
        if($autoreply_templates == '')
          $autoreply_templates = "<option value='0'>{$this->lang->line('You currently have no template. Please create template first.')}</option>";
        $response['autoreply_templates'] = $autoreply_templates;

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'ig','page_id'=>$page_table_id)),'','','',$start=NULL,'');
        $postback_str = '';
        $postback_str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";
        foreach ($postback_data as $key => $value) 
        {
            $postback_str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $response['autoreply_postbacks'] = $postback_str;

        echo json_encode($response);
    }

    public function update_your_account_info()
    {
        $this->ajax_check();
        $table_id = $this->input->post('table_id',true);
        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('id'=>$table_id,'user_id'=>$this->user_id);
        $instagram_reply_page_info = $this->basic->get_data($table_name, $where);
        $page_access_token = isset($instagram_reply_page_info[0]['page_access_token']) ? $instagram_reply_page_info[0]['page_access_token'] : "";
        $instagram_business_account_id = isset($instagram_reply_page_info[0]['instagram_business_account_id']) ? $instagram_reply_page_info[0]['instagram_business_account_id'] : "";
        $facebook_rx_fb_user_info_id = isset($instagram_reply_page_info[0]['facebook_rx_fb_user_info_id']) ? $instagram_reply_page_info[0]['facebook_rx_fb_user_info_id'] : "";

        $this->load->library("fb_rx_login");

        $config_data=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=>$facebook_rx_fb_user_info_id)));
        $facebook_rx_config_id=isset($config_data[0]['facebook_rx_config_id'])?$config_data[0]['facebook_rx_config_id']:0;
        $user_accesstoken = isset($config_data[0]['access_token'])?$config_data[0]['access_token']:0;
        $this->fb_rx_login->app_initialize($facebook_rx_config_id);
        $instagram_account_info = $this->fb_rx_login->instagram_account_info($instagram_business_account_id, $user_accesstoken);

        $instradata = array(
            'insta_followers_count' => isset($instagram_account_info['followers_count']) ? $instagram_account_info['followers_count'] : "",
            'insta_media_count' => isset($instagram_account_info['media_count']) ? $instagram_account_info['media_count'] : "",
            'insta_website' => isset($instagram_account_info['website']) ? $instagram_account_info['website'] : "",
            'insta_biography' => isset($instagram_account_info['biography']) ? $instagram_account_info['biography'] : "",
            'insta_username' => isset($instagram_account_info['username']) ? $instagram_account_info['username'] : "",
        );
        $where = array('id'=>$table_id,'user_id'=>$this->user_id);
        $this->basic->update_data('facebook_rx_fb_page_info', $where, $instradata);
        $str = "Now you have {$instagram_account_info['followers_count']} followers and {$instagram_account_info['media_count']} media";
        $response = array();
        $response["message"] = $str;
        $response["status"] = 1;
        $response["media_count"] = custom_number_format($instagram_account_info['media_count']);
        $response["follower_count"] = custom_number_format($instagram_account_info['followers_count']);
        echo json_encode($response);
    }

    public function reports()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
        redirect('home/login_page', 'location');
      }

      $media_type = $this->session->userdata('selected_global_media_type');
      if($media_type == 'fb') {
        redirect('comment_automation/comment_section_report');
      }
      
      $data = [];
      if($this->addon_exist('instagram_reply_enhancers'))
      {
        if($this->session->userdata('user_type') == 'Admin' || in_array(278,$this->module_access))
          $data['instagram_reply_enhancers_access'] = 1;
        else
          $data['instagram_reply_enhancers_access'] = 0;
      }
      else
        $data['instagram_reply_enhancers_access'] = 0;
      $data['body'] = "instagram_reply/comment_reply/report_section";
      $data['page_title'] = $this->lang->line("Report Section");
      $this->_viewcontroller($data);
    }

    public function instagram_autoreply_report($reply_type='',$page_info_table_id='',$auto_reply_campaign_id='')
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
            redirect('home/login_page', 'location');
      }

      if($reply_type == "") {
        redirect("home/error_404","location");
      } 

      $data['instagram_bot_exist'] = false;
      if(addon_exist($module_id=320,$addon_unique_name="instagram_bot")) {
        $data['instagram_bot_exist'] = true;
      }
      
      $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_info_table_id,'user_id'=>$this->user_id)),'','',1);
      
      $join = array("facebook_rx_fb_page_info"=>"instagram_reply_autoreply.page_info_table_id=facebook_rx_fb_page_info.id,left","facebook_rx_fb_user_info"=>"instagram_reply_autoreply.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left");
      $select = array(
        "instagram_reply_autoreply.*",
        "facebook_rx_fb_page_info.has_instagram",
        "facebook_rx_fb_page_info.instagram_business_account_id",
        "facebook_rx_fb_page_info.insta_username",
        "facebook_rx_fb_page_info.page_name",
        "facebook_rx_fb_user_info.name"
      );

      $where['where'] = array(
        "has_instagram"=>"1",
        "instagram_reply_autoreply.user_id"=>$this->user_id,
        "instagram_reply_autoreply.report_type"=>$reply_type,
        "instagram_reply_autoreply.facebook_rx_fb_user_info_id"=> $this->session->userdata("facebook_rx_fb_user_info")
      );

      $accounts = $this->basic->get_data("instagram_reply_autoreply",$where,$select,$join,'','','',$group_by='instagram_business_account_id');

      // echo "<pre>"; print_r($accounts); exit;

      $data['insta_accounts'] = $accounts;

      $data['insta_username'] = isset($page_info[0]['insta_username']) ? $page_info[0]['insta_username']:'';
      $data['page_name'] = isset($page_info[0]['page_name']) ? $page_info[0]['page_name']:'';
      $data['reply_type'] = $reply_type;
      $data['auto_reply_campaign_id'] = $auto_reply_campaign_id;

      if($this->addon_exist('instagram_reply_enhancers'))
        if($this->session->userdata('user_type') == 'Admin' || in_array(278,$this->module_access))
          $data['commnet_hide_delete_addon'] = 1;
        else
          $data['commnet_hide_delete_addon'] = 0;

      else
        $data['commnet_hide_delete_addon'] = 0;

      $data['body'] = 'instagram_reply/comment_reply/all_autoreply_report';
      $data['page_title'] = ucfirst($reply_type).' '. $this->lang->line('Autoreply Report');
      $data['page_table_id'] = $page_info_table_id;
      // $data['emotion_list'] = $this->get_emotion_list();
      $this->_viewcontroller($data);
    }

    public function all_autoreply_report_data()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
            exit;
      }
      $this->ajax_check();

      $search_post_id = $this->input->post("post_id",true);
      $search_with_accounts = $this->input->post("search_with_accounts",true);


      $reply_type = trim($this->input->post("reply_type",true));
      $page_info_table_id = trim($this->input->post("page_info_table_id",true));
      $auto_reply_campaign_id = trim($this->input->post("auto_reply_campaign_id",true));
      $display_columns = array("#",'id','thumbnail','post_id','actions','last_reply_time');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 6;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'last_reply_time';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;

      $where = [];
      $where_simple = [];
      
      $table_name = "";
      $sql = '';

      $explode_account_info = explode("-", $search_with_accounts);
      if($reply_type == "post") {

        $table_name = "instagram_reply_autoreply";
        $where_simple['user_id'] = $this->user_id;

        if($search_post_id != '') {
          $where_simple['post_id'] = $search_post_id;
        }

        if(isset($search_with_accounts) && !empty($search_with_accounts)) {

          $where_simple['page_info_table_id'] = $explode_account_info[1];
          $where_simple['report_type'] = $explode_account_info[0];
        } else {

          $where_simple['report_type'] = $reply_type;
        }
        $where_simple['instagram_reply_autoreply.facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        $where  = array('where'=>$where_simple);
        $info = $this->basic->get_data($table_name,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');
        $total_rows_array=$this->basic->count_row($table_name,$where,$count="id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

      } else if($reply_type == "full" || $reply_type == "mention") {

        $table_name = "instagram_autoreply_report";

        $join = array("instagram_reply_autoreply" => "instagram_autoreply_report.autoreply_table_id=instagram_reply_autoreply.id,left");

        $select = array('instagram_autoreply_report.*',"max(instagram_autoreply_report.reply_time) as last_reply_time",'instagram_reply_autoreply.page_info_table_id');

        $where_simple['instagram_autoreply_report.user_id'] = $this->user_id;

        if(isset($search_with_accounts) && !empty($search_with_accounts)) {
          $where_simple['instagram_autoreply_report.autoreply_table_id'] = $explode_account_info[2];
          $where_simple['instagram_autoreply_report.reply_type'] = $explode_account_info[0];
        } else {

          $where_simple['instagram_autoreply_report.reply_type'] = $reply_type;
        }
        
        if($search_post_id !='') {
          $where_simple['instagram_autoreply_report.post_id'] = $search_post_id;
        }
        $where_simple['instagram_reply_autoreply.facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        $where  = array('where'=>$where_simple);

        $info = $this->basic->get_data($table_name,$where,$select,$join,$limit,$start,$order_by,$group_by='instagram_autoreply_report.post_id');

        $total_rows_array=$this->basic->count_row($table_name,$where,$count=$table_name.".id",$join,$group_by='instagram_autoreply_report.post_id');
        $total_result=$total_rows_array[0]['total_rows'];
      } 

      

      for ($i=0; $i < count($info) ; $i++) 
      { 
          $info[$i]['thumbnail'] = "<img class='rounded-circle instagram_height_width_40px_bordered' src='".base_url('assets/img/avatar/avatar-1.png')."' alt='Thumbnail'>";
          if($info[$i]['media_type'] == 'IMAGE' || $info[$i]['media_type'] == 'CAROUSEL_ALBUM') {
            $info[$i]['thumbnail'] = "<img class='rounded-circle instagram_height_width_40px_bordered' src='".$info[$i]['media_url']."' alt='Thumbnail'>";
          }

          if($info[$i]['media_type'] == 'VIDEO') {
            $info[$i]['thumbnail'] = "<video width='50' height='50' controls='controls'><source src='".$info[$i]['media_url']."' type='video/mp4'></video>";
          }

          $last_reply_time = $info[$i]['last_reply_time'];

          if($last_reply_time == '0000-00-00 00:00:00') {
            $info[$i]['last_reply_time'] ='<span class="text-muted"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line("Not Replied").'</span>';
          }
          else {
            $info[$i]['last_reply_time'] = date("M j, Y H:i A",strtotime($last_reply_time));
          }
          
          $info_new[$i]['error_message']    = $info[$i]['error_message'];

          $page_url  = $button = $deleteUrl = "";

          if($reply_type == "post") {

            $action_count = 5;

            $page_url = "<a href='#' class='btn btn-circle btn-outline-primary view_report' table_id='".$info[$i]['id']."' post_id='".$info[$i]['post_id']."' page_info_table_id='".$info[$i]['page_info_table_id']."' reply_type='".$reply_type."' data-toggle='tooltip' title='".$this->lang->line("Campaign Report")."'><i class='fas fa-eye'></i></a>
            <a href='#' class='btn btn-circle btn-outline-warning edit_reply_info' table_id='".$info[$i]['id']."' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."'><i class='fas fa-edit'></i></a>";

            $deleteUrl ="<a href='#' class='btn btn-circle btn-outline-danger delete_post_report red' table_id='".$info[$i]['id']."' page_info_table_id='".$info[$i]['page_info_table_id']."' autoreply_type='post_autoreply' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."'><i class='fas fa-trash-alt'></i></a>";

            $button = '';
            if($info[$i]['post_pause_play'] == "play") {

              $button = "<a href='#' class='btn btn-circle btn-outline-dark pause_campaign_info' to_do='pause' table_id='".$info[$i]['id']."' title='".$this->lang->line("pause campaign")."'><i class='fas fa-pause'></i></a>";

            } else {
              $button = "<a href='#' class='btn btn-circle btn-outline-dark pause_campaign_info' to_do='play' table_id='".$info[$i]['id']."' title='".$this->lang->line("play campaign")."'><i class='fas fa-play'></i></a>";

            }

            $button .= "<a href='#' class='btn btn-circle btn-outline-info media_insights' page_table_id='" . $info[$i]['page_info_table_id'] . "' post_id='" . $info[$i]['post_id'] . "' title='".$this->lang->line("Analytics")."'><i class='fas fa-chart-bar'></i></a>";

          } else if($reply_type == "full" || $reply_type == "mention") {

            $action_count = 3;

            $page_url = "<a href='#' class='btn btn-circle btn-outline-primary view_report' table_id='".$info[$i]['autoreply_table_id']."' post_id='".$info[$i]['post_id']."' page_info_table_id='".$info[$i]['page_info_table_id']."' reply_type='".$reply_type."' data-toggle='tooltip' title='".$this->lang->line("Campaign Report")."'><i class='fas fa-eye'></i></a>
            <a href='#' class='btn btn-circle btn-outline-info media_insights' page_table_id='".$info[$i]['page_info_table_id']."' post_id='".$info[$i]['post_id']."' title='".$this->lang->line("Analytics")."'><i class='fas fa-chart-bar'></i></a>";

            $deleteUrl ="<a href='#' class='btn btn-circle btn-outline-danger delete_full_mention_report red' table_id='".$info[$i]['autoreply_table_id']."' post_id='".$info[$i]['post_id']."' page_info_table_id='".$info[$i]['page_info_table_id']."' reply_type='".$reply_type."' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."'><i class='fas fa-trash-alt'></i></a>";

          }

          $info[$i]['post_id'] = "<a target='_blank' href='".$info[$i]['post_url']."'>".$info[$i]['post_id']."</a>";

          $action_width = ($action_count*47)+20;
          $info[$i]['actions'] = '<div class="dropdown d-inline dropright">
              <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
          $info[$i]['actions'] .= $page_url;
          $info[$i]['actions'] .= $button;
          $info[$i]['actions'] .= $deleteUrl;
          $info[$i]['actions'] .="</div></div><script src='".base_url()."assets/js/system/tooltip_popover.js'></script>";
      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);
      
    }


    public function get_content_info()
    {
      $this->ajax_check();

      // check intagram bot addon existance and access
      $this->is_instagram_bot_addon = addon_exist($module_id=320,$addon_unique_name="instagram_bot");

      $report_table_id = $this->input->post("table_id",true);
      $post_id = $this->input->post("post_id",true);
      $reply_type = trim($this->input->post("reply_type",true));

      $where['where'] = ['autoreply_table_id'=>$report_table_id,'post_id'=>$post_id,'user_id'=>$this->user_id,'reply_type'=>$reply_type];

      $autocomment_count = $this->basic->get_data('instagram_autoreply_report',$where,array('sum(auto_comment_reply_count) as total_comment_reply'));
      $hiddencomment_count = $this->basic->get_data('instagram_autoreply_report',$where,array('sum(hidden_comment_count) as total_hidden'));
      $deletedcomment_count = $this->basic->get_data('instagram_autoreply_report',$where,array('sum(deleted_comment_count) as total_deleted'));

      $private_reply_count = $total_private_replies = $private_reply_count_html = '';
      $block_column = "col-md-4 col-12";

      if($this->is_instagram_bot_addon && $reply_type != "mention") {

        $private_reply_count = $this->basic->get_data('instagram_autoreply_report',$where,array('sum(auto_private_reply_count) as total_private_reply'));
        $total_private_replies = isset($autocomment_count[0]['total_private_reply']) ? $autocomment_count[0]['total_comment_reply']:0;
        $block_column = "col-md-3 col-12";

        $private_reply_count_html = '
        <div class="'.$block_column.'">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>'.$this->lang->line('Private reply count').'</h4>
                    </div>
                    <div class="card-body" id="comment_reply_count">'.$total_private_replies.'</div>
                </div>
            </div>
        </div>
        ';
      }

 
      $total_replies = isset($autocomment_count[0]['total_comment_reply']) ? $autocomment_count[0]['total_comment_reply']:0;
      $total_hidden = isset($hiddencomment_count[0]['total_hidden']) ? $hiddencomment_count[0]['total_hidden']:0;
      $total_deleted = isset($deletedcomment_count[0]['total_deleted']) ? $deletedcomment_count[0]['total_deleted']:0;

      $str = '
        <div class="row">
            <div class="'.$block_column.'">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>'.$this->lang->line('Comment reply count').'</h4>
                        </div>
                        <div class="card-body" id="comment_reply_count">'.$total_replies.'</div>
                    </div>
                </div>
            </div>'.$private_reply_count_html.'
            
            <div class="'.$block_column.'">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-eye-slash"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>'.$this->lang->line('Hidden comment count').'</h4>
                        </div>
                        <div class="card-body" id="hidden_comment_count">'.$total_hidden.'</div>
                    </div>
                </div>
            </div>
            <div class="'.$block_column.'">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>'.$this->lang->line('Deleted comment count').'</h4>
                        </div>
                        <div class="card-body" id="deleted_comment_count">'.$total_deleted.'</div>
                    </div>
                </div>
            </div>
        </div>
      ';

      echo $str;

    }

    public function get_autoreply_report()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
            exit;
      }

      $this->ajax_check();

      // check intagram bot addon existance and access
      $this->is_instagram_bot_addon = addon_exist($module_id=320,$addon_unique_name="instagram_bot");

      $search_value = $_POST['search']['value'];

      $report_table_id = $this->input->post("table_id",true);
      $post_id = $this->input->post("post_id",true);
      $reply_type = trim($this->input->post("reply_type",true));
      $page_info_table_id = trim($this->input->post("page_info_table_id",true));

      if($this->is_instagram_bot_addon && $reply_type != "mention") {
        $display_columns = array("#",'id','commenter_name','comment_text','comment_reply_text','reply_time','reply_status_comment','reply_status','error_message');
      }
      else {
        $display_columns = array("#",'id','commenter_name','comment_text','comment_reply_text','reply_time','reply_status_comment','error_message');
      }

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 6;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'last_reply_time';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;

      $where = [];
      $where_simple = [];
      $where_simple['user_id'] = $this->user_id;
      $where_simple['reply_type'] = $reply_type;
      $where_simple['autoreply_table_id'] = $report_table_id;
      $where_simple['post_id'] = $post_id;

      $sql = '';
      if ($search_value != '') {
        $sql = "(commenter_name LIKE  '%".$search_value."%' OR comment_text LIKE '%".$search_value."%')";
      }
      if($sql != '') {
        $this->db->where($sql);
      }

      $where  = array('where'=>$where_simple);
      $info = $this->basic->get_data("instagram_autoreply_report",$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

      $total_rows_array=$this->basic->count_row("instagram_autoreply_report",$where,$count="id",$join='',$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      for ($i=0; $i < count($info); $i++) { 

        $last_reply_time = $info[$i]['reply_time'];

        if($last_reply_time == '0000-00-00 00:00:00') {
          $info[$i]['reply_time'] ='<span class="text-muted"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line("Not Replied").'</span>';
        }
        else {
          $info[$i]['reply_time'] = date("M j, Y H:i A",strtotime($last_reply_time));
        }

        $status = $info[$i]['reply_status_comment'];

        if($this->is_instagram_bot_addon && $reply_type != "mention") {
          $private_reply_status = $info[$i]['reply_status'];
          $private_reply_status = json_decode($private_reply_status,true);
          $private_reply_status = $private_reply_status[0];
          if($private_reply_status == "success") {
            $info[$i]['reply_status'] = '<span class="text-muted"><i class="fas fa-check-circle green"></i> '.$private_reply_status.'</span>';
          } else {
            $info[$i]['reply_status'] = '<span class="text-muted"><i class="fas fa-exclamation-circle red"></i> '.$private_reply_status.'</span>';
          }
        }

        if($status == "success") {
          $info[$i]['reply_status_comment'] = '<span class="text-muted"><i class="fas fa-check-circle green"></i> '.$status.'</span>';

        } else if($status == "comment hidden") {
          $info[$i]['reply_status_comment'] = '<span class="text-muted"><i class="fas fa-eye-slash orange"></i> '.$this->lang->line("Hidden").'</span>';

        } else {
          $info[$i]['reply_status_comment'] = '<span class="text-muted"><i class="fas fa-exclamation-circle red"></i> '.$info[$i]['reply_status_comment'].'</span>';
        }
        
      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);
    }

    public function delete_post_report()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
            exit;
      }

      $this->ajax_check();

      $table_id = $this->input->post("table_id",true);
      $page_info_table_id = $this->input->post("page_info_table_id",true);
      $autoreply_type = $this->input->post("autoreply_type",true);

      if($table_id == "" || $table_id == 0 ) exit;

      $table_name = "instagram_reply_autoreply";
      $where = [];
      $where['id'] = $table_id;
      $where['user_id'] = $this->user_id;
      $where['page_info_table_id'] = $page_info_table_id;
      $where['autoreply_type'] = $autoreply_type;

      if($this->basic->delete_data($table_name,$where)) {
        $this->basic->delete_data("instagram_autoreply_report",['autoreply_table_id'=>$table_id,'user_id'=>$this->user_id]);
        echo "1";
      } else {
        echo "0";
      }
    }

    public function delete_full_mention_report()
    {
      $this->ajax_check();

      $table_id = $this->input->post("table_id",true);
      $post_id = $this->input->post("post_id",true);
      $reply_type = $this->input->post("reply_type",true);

      if($table_id == "" || $table_id == 0 ) exit;

      $table_name = "instagram_autoreply_report";
      $where = [];
      $where['autoreply_table_id'] = $table_id;
      $where['user_id'] = $this->user_id;
      $where['post_id'] = $post_id;
      $where['reply_type'] = $reply_type;

      if($this->basic->delete_data($table_name,$where)) {
        echo "1";
      } else {
        echo "0";
      }
    }


    public function ajax_autoreply_submit()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
        exit;
      }
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $this->load->library("fb_rx_login");
        if ($_POST) {
            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key,true);
            }
        }

        //************************************************//
        $status = $this->_check_usage($module_id = 279, $request = 1);
        if ($status == "2") {
            $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.") . "<a href='" . site_url('payment/usage_history') . "'>" . $this->lang->line("click here to see usage log") . "</a>";
            $return_val = array("status" => "0", "message" => $error_msg);
            echo json_encode($return_val);
            exit();
        } else if ($status == "3") {
            $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.") . "<a href='" . site_url('payment/usage_history') . "'>" . $this->lang->line("click here to see usage log") . "</a>";
            $return_val = array("status" => "0", "message" => $error_msg);
            echo json_encode($return_val);
            exit();
        }
        //************************************************//
        
        $join = ['facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left'];
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('facebook_rx_fb_page_info.id' => $auto_reply_page_id,'facebook_rx_fb_page_info.user_id'=>$this->user_id)),['facebook_rx_fb_page_info.*','access_token'],$join);
        $page_name = $page_info[0]['page_name'];
        $user_accesstoken = $page_info[0]['access_token'];

        $this->fb_rx_login->app_initialize($this->session->userdata("fb_rx_login_database_id"));
        $media_id = trim($auto_reply_post_id);
        $auto_reply_post_id = trim($auto_reply_post_id);
        $auto_reply_post_id_array = explode('_', $auto_reply_post_id);
        if (count($auto_reply_post_id_array) == 1) {
            $auto_reply_post_id = $page_info[0]['page_id'] . "_" . $auto_reply_post_id;
        }
        // $manual_reply_description = "";
        if ($manual_enable == 'yes') {
            try {
                $post_info = $this->fb_rx_login->instagram_get_post_info_by_id($media_id, $page_info[0]['access_token']);
                if (isset($post_info['error'])) {
                    $response['error'] = 'yes';
                    $response['error_msg'] = $post_info['error']['message'];
                } else {
                    $post_created_at = isset($post_info[$media_id]['timestamp']) ? $post_info[$media_id]['timestamp'] : "";
                    if (isset($post_info[$media_id]['caption']))
                        $post_description = isset($post_info[$media_id]['caption']) ? $post_info[$media_id]['caption'] : "";
                    else if (isset($post_info[$media_id]['name']))
                        $post_description = isset($post_info[$media_id]['name']) ? $post_info[$media_id]['name'] : "";
                    else
                        $post_description = isset($post_info[$media_id]['description']) ? $post_info[$media_id]['description'] : "";
                }
            } catch (Exception $e) {
                $post_created_at = "";
                $post_description = "";
            }
        } else {
            try {
                $post_list = $this->fb_rx_login->get_postlist_from_instagram_account($page_info[0]['instagram_business_account_id'], $page_info[0]['access_token']);
                if (isset($post_list['data']) && !empty($post_list['data'])) {
                    foreach ($post_list['data'] as $value) {
                        if ($value['id'] == $media_id) {
                            $post_created_at = $value['timestamp'];
                            // $post_description = isset($value['message']) ? $value['message'] : '';
                            if (isset($value['caption']))
                                $post_description = isset($value['caption']) ? $value['caption'] : "";
                            else if (isset($value['name']))
                                $post_description = isset($value['name']) ? $value['name'] : "";
                            else
                                $post_description = isset($value['description']) ? $value['description'] : "";
                            // $manual_reply_description = "found";
                            break;
                        }
                    }
                }
            } catch (Exception $e) {
                $post_created_at = "";
                $post_description = "";
            }
        }
        
        $post_description = $this->db->escape($post_description);
        $return = array();
        $date_time = date("Y-m-d H:i:s");

        $template_selection = $this->input->post('auto_template_selection',true);
        if($template_selection)
        {
          $template_table_id = $this->input->post('auto_reply_template',true);
          $template_info = $this->basic->get_data('instagram_reply_template', ['where'=>['id'=>$template_table_id,'user_id'=>$this->user_id]]);

          $auto_campaign_name = $template_info[0]['auto_reply_campaign_name'];
          $multiple_reply = $template_info[0]['multiple_reply'];
          $hide_comment_after_comment_reply = $template_info[0]['hide_comment_after_comment_reply'];
          $nofilter_word_found_text = $template_info[0]['nofilter_word_found_text'];
          $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);
          $is_delete_offensive = $template_info[0]['is_delete_offensive'];
          $offensive_words = $template_info[0]['offensive_words'];
          $offensive_words = $this->db->escape($offensive_words);

          $template_autoreply_text = $template_info[0]['auto_reply_text'];
          $template_autoreply_text = $this->db->escape($template_autoreply_text);

          $private_message_offensive_words  = $this->db->escape($template_info[0]['private_message_offensive_words']);
          $nofilter_word_found_text         = $this->db->escape($template_info[0]['nofilter_word_found_text']);

          $message_type = $template_info[0]['reply_type'];
        }
        else
        {
          $nofilter_array['comment_reply'] = trim($nofilter_word_found_text);
          $nofilter_array['private_reply'] = trim($nofilter_word_found_text_private);
          $multiple_reply                   = $this->input->post('multiple_reply',true);
          $hide_comment_after_comment_reply = $this->input->post('hide_comment_after_comment_reply',true);
          if($multiple_reply == '') $multiple_reply = 'no';
          if($hide_comment_after_comment_reply == '') $hide_comment_after_comment_reply = 'no';
          
          $no_filter_array = array();
          array_push($no_filter_array, $nofilter_array);
          $nofilter_word_found_text = json_encode($no_filter_array);
          $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);
          // comment hide and delete section
          $is_delete_offensive = $delete_offensive_comment;
          $offensive_words = trim($delete_offensive_comment_keyword);
          $offensive_words = $this->db->escape($offensive_words);
          $private_message_offensive_words = $this->db->escape($private_message_offensive_words);
        }

        $create_template = $this->input->post('create_new_template',true);
        $facebook_rx_fb_user_info = $this->session->userdata("facebook_rx_fb_user_info");
        // end of comment hide and delete section
        $page_name = $this->db->escape($page_name);
        $report_type = "post";
        if ($message_type == 'generic') {
          if($template_selection)
          {
            $auto_reply_text = $template_autoreply_text;
          }
          else
          {
            $generic_message_array['comment_reply'] = trim($generic_message);
            $generic_message_array['private_reply'] = trim($generic_message_private);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = '';
            $auto_reply_text = json_encode($generic_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);
          }
          $sql = "INSERT INTO instagram_reply_autoreply (facebook_rx_fb_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_created_at,post_description,reply_type,report_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,auto_reply_text,last_updated_at,nofilter_word_found_text,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id','$auto_campaign_name','$auto_reply_page_id',$page_name,'$media_id','$post_created_at',$post_description,'$message_type','$report_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text,'$trigger_matching_type'
          )
          ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,multiple_reply='$multiple_reply',auto_reply_campaign_name='$auto_campaign_name',nofilter_word_found_text=$nofilter_word_found_text,trigger_matching_type='$trigger_matching_type'";

          if($create_template == 'yes')
          {
            $template_sql = "INSERT INTO instagram_reply_template (user_id,auto_reply_campaign_name,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,auto_reply_text,last_updated_at,nofilter_word_found_text) VALUES ('$this->user_id','$auto_campaign_name','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text
              )
              ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,multiple_reply='$multiple_reply',auto_reply_campaign_name='$auto_campaign_name',nofilter_word_found_text=$nofilter_word_found_text";
            $this->db->query($template_sql);
          }

        } 
        else {
          if($template_selection)
          {
            $auto_reply_text = $template_autoreply_text;
          }
          else
          {
            $auto_reply_text_array = array();
            for ($i = 1; $i <= 20; $i++) {
                $filter_word = 'filter_word_' . $i;
                $filter_word_text = $this->input->post($filter_word,true);
                $comment_message = 'comment_reply_msg_' . $i;
                $comment_message_text = $this->input->post($comment_message,true);

                $filter_message = 'filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);

                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != '')) 
                {
                    $data['filter_word'] = trim($filter_word_text);
                    $data['comment_reply_text'] = trim($comment_message_text);

                    $data['reply_text'] = trim($filter_message_text);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = '';
            $auto_reply_text = json_encode($auto_reply_text_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);

          }

          $sql = "INSERT INTO instagram_reply_autoreply (facebook_rx_fb_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_created_at,post_description,reply_type,report_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,auto_reply_text,last_updated_at,nofilter_word_found_text,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id','$auto_campaign_name','$auto_reply_page_id',$page_name,'$media_id','$post_created_at',$post_description,'$message_type','$report_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text,trigger_matching_type='$trigger_matching_type')
          ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,multiple_reply='$multiple_reply',auto_reply_campaign_name='$auto_campaign_name',nofilter_word_found_text=$nofilter_word_found_text,trigger_matching_type='$trigger_matching_type'";

          if($create_template == 'yes')
          {
            $template_sql = "INSERT INTO instagram_reply_template (user_id,auto_reply_campaign_name,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,auto_reply_text,last_updated_at,nofilter_word_found_text) VALUES ('$this->user_id','$auto_campaign_name','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text)
              ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,multiple_reply='$multiple_reply',auto_reply_campaign_name='$auto_campaign_name',nofilter_word_found_text=$nofilter_word_found_text";
            $this->db->query($template_sql);
          }

        }


        if ($this->db->query($sql)) {
            $this->_insert_usage_log($module_id = 279, $request = 1);
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        } else {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }


    public function ajax_edit_reply_info()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
            exit;
      }
        $respnse = array();
        $table_id = $this->input->post('table_id');
        $second_table_data = $this->input->post('second_table_data');
        if($second_table_data == 'yes')
        {
            $table_data = $this->basic->get_data('instagram_reply_full_mentions_report', array('where' => array('id' => $table_id)));
            $info = $this->basic->get_data('instagram_reply_autoreply', array('where' => array('id' => $table_data[0]['full_mentions_autoreply_id'])));
        }
        else
            $info = $this->basic->get_data('instagram_reply_autoreply', array('where' => array('id' => $table_id)));

        if ($info[0]['reply_type'] == 'generic') {
            $reply_content = json_decode($info[0]['auto_reply_text']);
            if (!is_array($reply_content)) {
                $reply_content[0]['comment_reply'] = "";
                $reply_content[0]['private_reply'] = $info[0]['auto_reply_text'];
            }
        } else
            $reply_content = json_decode($info[0]['auto_reply_text']);

        $nofilter_word_text = json_decode($info[0]['nofilter_word_found_text']);
        if (!is_array($nofilter_word_text)) {
            $nofilter_word_text[0]['comment_reply'] = '';
            $nofilter_word_text[0]['private_reply'] = $info[0]['nofilter_word_found_text'];
        }

        $respnse['reply_type'] = $info[0]['reply_type'];
        $respnse['trigger_matching_type'] = $info[0]['trigger_matching_type'];
        $respnse['multiple_reply'] = $info[0]['multiple_reply'];
        $respnse['auto_reply_text'] = $reply_content;
        $respnse['edit_auto_reply_page_id'] = $info[0]['page_info_table_id'];
        $respnse['edit_auto_reply_post_id'] = $info[0]['post_id'];
        $respnse['edit_auto_campaign_name'] = $info[0]['auto_reply_campaign_name'];

        $respnse['edit_nofilter_word_found_text'] = $nofilter_word_text;

        $respnse['is_delete_offensive'] = $info[0]['is_delete_offensive'];
        $respnse['offensive_words'] = $info[0]['offensive_words'];

        $respnse['private_message_offensive_words'] = $info[0]['private_message_offensive_words'];

        $respnse['hide_comment_after_comment_reply'] = $info[0]['hide_comment_after_comment_reply'];

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'ig','page_id'=>$info[0]['page_info_table_id'])),'','','',$start=NULL,'');
        $str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";
        foreach ($postback_data as $key => $value) 
        {
          $str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $respnse['postbacks'] = $str;

        echo json_encode($respnse);
    }

    public function ajax_update_autoreply_submit()
    {
      if($this->session->userdata('user_type') != 'Admin' && !in_array(279,$this->module_access)) {
            exit;
      }
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if ($_POST) {
            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key,true);
            }
        }

        $edit_multiple_reply = $this->input->post('edit_multiple_reply',true);
        $edit_hide_comment_after_comment_reply = $this->input->post('edit_hide_comment_after_comment_reply',true);

        if($edit_multiple_reply == '') $edit_multiple_reply = 'no';
        if($edit_hide_comment_after_comment_reply == '') $edit_hide_comment_after_comment_reply = 'no';


        $return = array();
        if ($edit_message_type == 'generic') {
            $generic_message_array['comment_reply'] = trim($edit_generic_message);
            $generic_message_array['private_reply'] = trim($edit_generic_message_private);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = json_encode($generic_array);
        } else {
            $auto_reply_text_array = array();
            for ($i = 1; $i <= 20; $i++) {
                $filter_word = 'edit_filter_word_' . $i;
                $filter_word_text = $this->input->post($filter_word,true);

                $filter_message = 'edit_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message,true);

                $comment_message = 'edit_comment_reply_msg_' . $i;
                $comment_message_text = $this->input->post($comment_message,true);

                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != '')) 
                {
                    $data['filter_word'] = trim($filter_word_text);

                    $data['reply_text'] = trim($filter_message_text);

                    $data['comment_reply_text'] = trim($comment_message_text);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = json_encode($auto_reply_text_array);
        }
        $no_filter_array['comment_reply'] = trim($edit_nofilter_word_found_text);

        $no_filter_array['private_reply'] = trim($edit_nofilter_word_found_text_private);

        $nofilter_array = array();
        array_push($nofilter_array, $no_filter_array);
        $data = array(
            'auto_reply_text' => $auto_reply_text,
            'reply_type' => $edit_message_type,
            'auto_reply_campaign_name' => $edit_auto_campaign_name,
            'nofilter_word_found_text' => json_encode($nofilter_array),
            'multiple_reply' => $edit_multiple_reply,
            'is_delete_offensive' => $edit_delete_offensive_comment,
            'offensive_words' => trim($edit_delete_offensive_comment_keyword),
            'hide_comment_after_comment_reply' => $edit_hide_comment_after_comment_reply,
            'private_message_offensive_words' => trim($edit_private_message_offensive_words),
            'trigger_matching_type' => $edit_trigger_matching_type
        );
        $where = array(
            'user_id' => $this->user_id,
            'page_info_table_id' => $edit_auto_reply_page_id,
            'post_id' => $edit_auto_reply_post_id
        );
        if ($this->basic->update_data('instagram_reply_autoreply', $where, $data)) {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        } else {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }


    public function media_insights_modal_data()
    {
        if(!$_POST) exit();
        $this->load->library("fb_rx_login");
        $page_table_id = $this->input->post('page_id',true);
        $media_id = $this->input->post('post_id',true);
        
        $join = ['facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left'];
        $access_token_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('facebook_rx_fb_page_info.id' => $page_table_id,'facebook_rx_fb_page_info.user_id'=>$this->user_id)), array('page_access_token','access_token'),$join);

        $access_token = $access_token_info[0]['page_access_token'];
        $user_accesstoken = $access_token_info[0]['access_token'];
        
        $metric = "engagement,impressions,reach,saved";

        $this->fb_rx_login->app_initialize($this->session->userdata("fb_rx_login_database_id"));
        $media_insights = $this->fb_rx_login->instagram_media_insights($media_id, $metric, $user_accesstoken);

        if(isset($media_insights['status']) && $media_insights['status'] == 'error')
        {
            $str = "<div class='text-center alert alert-danger'><i class='fa fa-times-circle'></i> ".$media_insights['message']."</div>";
        }
        else
        {
            $engagement_value = isset($media_insights[0]['values'][0]['value']) ? $media_insights[0]['values'][0]['value'] : 0;
            $impressions_value = isset($media_insights[1]['values'][0]['value']) ? $media_insights[1]['values'][0]['value'] : 0;
            $reach_value = isset($media_insights[2]['values'][0]['value']) ? $media_insights[2]['values'][0]['value'] : 0;
            $saved_value = isset($media_insights[3]['values'][0]['value']) ? $media_insights[3]['values'][0]['value'] : 0;

            $str = '
            <div class="row">
              <div class="col-lg-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-primary">
                    <i class="fas fa-ring"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>'.$this->lang->line("Engagement").'</h4>
                    </div>
                    <div class="card-body">
                      '.$engagement_value.'
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-danger">
                    <i class="far fa-eye"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>'.$this->lang->line("Impressions").'</h4>
                    </div>
                    <div class="card-body">'.$impressions_value.'</div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-warning">
                    <i class="fas fa-bullhorn"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>'.$this->lang->line("Reach").'</h4>
                    </div>
                    <div class="card-body">'.$reach_value.'</div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-success">
                    <i class="fas fa-bookmark"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>'.$this->lang->line("Saved").'</h4>
                    </div>
                    <div class="card-body">'.$saved_value.'</div>
                  </div>
                </div>
              </div>
            </div>';           
        }

        echo $str;
    }

    public function full_autoreply_submit()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if ($_POST) {
            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key);
            }
        }

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('id' => $full_auto_reply_page_id)));
        $page_name = $page_info[0]['page_name'];
        $auto_reply_page_id = $page_info[0]['page_id'];

        $return = array();
        $date_time = date("Y-m-d H:i:s");
        $nofilter_array['comment_reply'] = trim($full_nofilter_word_found_text);
        $nofilter_array['private_reply'] = trim($full_nofilter_word_found_text_private);

        $full_multiple_reply                   = $this->input->post('full_multiple_reply',true);
        $full_hide_comment_after_comment_reply = $this->input->post('full_hide_comment_after_comment_reply',true);

        if($full_multiple_reply == '') $full_multiple_reply = 'no';
        if($full_hide_comment_after_comment_reply == '') $full_hide_comment_after_comment_reply = 'no';

        $no_filter_array = array();
        array_push($no_filter_array, $nofilter_array);
        $nofilter_word_found_text = json_encode($no_filter_array);
        $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);
        // comment hide and delete section
        $is_delete_offensive = $full_delete_offensive_comment;
        $offensive_words = trim($full_delete_offensive_comment_keyword);
        $offensive_words = $this->db->escape($offensive_words);
        $private_message_offensive_words = $this->db->escape($full_private_message_offensive_words);
        // end of comment hide and delete section
        $page_name = $this->db->escape($page_name);
        $facebook_rx_fb_user_info = $this->session->userdata("facebook_rx_fb_user_info");

        $report_type = "full";
        if($full_message_type == 'generic')
        {
            $generic_message_array['comment_reply'] = trim($full_generic_message);
            $generic_message_array['private_reply'] = trim($full_generic_message_private);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = '';
            $auto_reply_text = json_encode($generic_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);
            $sql = "INSERT INTO instagram_reply_autoreply (facebook_rx_fb_user_info_id,autoreply_type,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_created_at,post_description,reply_type,report_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,auto_reply_text,last_updated_at,
            nofilter_word_found_text) VALUES ('$facebook_rx_fb_user_info','account_autoreply','$this->user_id','$full_auto_campaign_name','$full_auto_reply_page_id',$page_name,'','','','$full_message_type','$report_type','$full_hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$full_multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text)"; 
        }
        else
        {
            $auto_reply_text_array = array();
            for ($i = 1; $i <= 20; $i++) {
                $filter_word = 'full_filter_word_' . $i;
                $filter_word_text = $this->input->post($filter_word);

                $comment_message = 'full_comment_reply_msg_' . $i;
                $comment_message_text = $this->input->post($comment_message);

                $filter_message = 'full_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);

                if ($filter_word_text != '' && ($comment_message_text != '' || $filter_message_text != '') ) {
                    $data['filter_word'] = trim($filter_word_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['reply_text'] = trim($filter_message_text);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = '';
            $auto_reply_text = json_encode($auto_reply_text_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);

            $sql = "INSERT INTO instagram_reply_autoreply (facebook_rx_fb_user_info_id,autoreply_type,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_created_at,post_description,reply_type,report_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,auto_reply_text,
            last_updated_at,nofilter_word_found_text,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','account_autoreply','$this->user_id','$full_auto_campaign_name','$full_auto_reply_page_id',$page_name,'','','','$full_message_type','$report_type','$full_hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$full_multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text,'$full_trigger_matching_type')";
        }
        if ($this->db->query($sql)) {
            //insert data to useges log table
            // $this->_insert_usage_log($module_id = 278, $request = 1);
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        } else {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function full_edit_reply_info()
    {
        $respnse = array();
        $table_id = $this->input->post('table_id');
        $info = $this->basic->get_data('instagram_reply_autoreply', array('where' => array('id' => $table_id,'user_id'=>$this->user_id)));

        if ($info[0]['reply_type'] == 'generic') {
            $reply_content = json_decode($info[0]['auto_reply_text'],true);
            if (!is_array($reply_content)) {
                $reply_content[0]['comment_reply'] = '';
                $reply_content[0]['private_reply'] = $info[0]['auto_reply_text'];
            }
        } else {
            $reply_content = json_decode($info[0]['auto_reply_text'],true);
        }
        $nofilter_word_text = json_decode($info[0]['nofilter_word_found_text']);
        if (!is_array($nofilter_word_text)) {
            $nofilter_word_text[0]['comment_reply'] = '';
            $nofilter_word_text[0]['private_reply'] = $info[0]['nofilter_word_found_text'];
        }
        $respnse['reply_type'] = $info[0]['reply_type'];
        $respnse['trigger_matching_type'] = $info[0]['trigger_matching_type'];
        $respnse['multiple_reply'] = $info[0]['multiple_reply'];
        $respnse['auto_reply_text'] = $reply_content;
        $respnse['edit_auto_reply_page_id'] = $info[0]['page_info_table_id'];
        $respnse['edit_auto_campaign_name'] = $info[0]['auto_reply_campaign_name'];
        $respnse['edit_nofilter_word_found_text'] = $nofilter_word_text;
        // comment hide and delete section
        $respnse['is_delete_offensive'] = $info[0]['is_delete_offensive'];
        $respnse['offensive_words'] = $info[0]['offensive_words'];

        $respnse['private_message_offensive_words'] = $info[0]['private_message_offensive_words'];

        $respnse['hide_comment_after_comment_reply'] = $info[0]['hide_comment_after_comment_reply'];

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'ig','page_id'=>$info[0]['page_info_table_id'])),'','','',$start=NULL,'');
        $str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";
        foreach ($postback_data as $key => $value) 
        {
          $str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $respnse['postbacks'] = $str;
        
        echo json_encode($respnse);
    }

    public function full_edit_autoreply_submit()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if ($_POST) {
            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key);
            }
        }

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('id' => $full_edit_auto_reply_page_id)));
        $page_name = $page_info[0]['page_name'];
        $auto_reply_page_id = $page_info[0]['page_id'];

        $full_edit_multiple_reply                   = $this->input->post('full_edit_multiple_reply',true);
        $full_edit_hide_comment_after_comment_reply = $this->input->post('full_edit_hide_comment_after_comment_reply',true);

        if($full_edit_multiple_reply == '') $full_edit_multiple_reply = 'no';
        if($full_edit_hide_comment_after_comment_reply == '') $full_edit_hide_comment_after_comment_reply = 'no';

        $return = array();
        if ($full_edit_message_type == 'generic') {
            $generic_message_array['comment_reply'] = trim($full_edit_generic_message);
            $generic_message_array['private_reply'] = trim($full_edit_generic_message_private);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = json_encode($generic_array);
        } else {
            $auto_reply_text_array = array();
            for ($i = 1; $i <= 20; $i++) {
                $filter_word = 'full_edit_filter_word_' . $i;
                $filter_word_text = $this->input->post($filter_word);

                $comment_message = 'full_edit_comment_reply_msg_' . $i;
                $comment_message_text = $this->input->post($comment_message);

                $filter_message = 'full_edit_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);

                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != '')) 
                {
                    $data['filter_word'] = trim($filter_word_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['reply_text'] = trim($filter_message_text);
                    array_push($auto_reply_text_array, $data);
                }
            }

            $auto_reply_text = json_encode($auto_reply_text_array);
        }

        $no_filter_array['comment_reply'] = trim($full_edit_nofilter_word_found_text);
        $no_filter_array['private_reply'] = trim($full_edit_nofilter_word_found_text_private);

        $nofilter_array = array();
        array_push($nofilter_array, $no_filter_array);

        if ($full_edit_message_type == 'generic'){
            $message_type = 'generic';
        } else {
            $message_type = 'filter';
        }

        $data = array(
            'auto_reply_text' => $auto_reply_text,
            'reply_type' => $message_type,
            'auto_reply_campaign_name' => $full_edit_auto_campaign_name,
            'nofilter_word_found_text' => json_encode($nofilter_array),
            'multiple_reply' => $full_edit_multiple_reply,
            'is_delete_offensive' => $full_delete_offensive_comment,
            'offensive_words' => trim($full_edit_delete_offensive_comment_keyword),
            'hide_comment_after_comment_reply' => $full_edit_hide_comment_after_comment_reply,
            'private_message_offensive_words' => trim($full_edit_private_message_offensive_words),
            'trigger_matching_type' => $full_edit_trigger_matching_type
        );

        $where = array('id' => $autoreply_table_id,'user_id'=>$this->user_id);

        if ($this->basic->update_data('instagram_reply_autoreply', $where, $data)) {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        } else {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function delete_account_campaign()
    {
      $this->ajax_check();
      $table_id = $this->input->post("table_id",true);
      $page_info_table_id = $this->input->post("page_info_table_id",true);
      $autoreply_type = $this->input->post("autoreply_type",true);

      if($table_id == "" || $table_id == 0 ) exit;

      $table_name = "instagram_reply_autoreply";
      $where = [];
      $where['id'] = $table_id;
      $where['user_id'] = $this->user_id;
      $where['page_info_table_id'] = $page_info_table_id;
      $where['autoreply_type'] = $autoreply_type;

      if($this->basic->delete_data($table_name,$where)) {
        echo "1";
      } else {
        echo "0";
      }
    }

    public function pause_campaign_info()
    {

      if($this->is_demo == '1')
      {
          if($this->session->userdata('user_type') == "Admin")
          {
              echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
              exit();
          }
      }

      $table_id = $this->input->post('table_id',true);

      if($table_id == "" || $table_id == 0) exit;

      $to_do = $this->input->post('to_do',true);
      $update_data = array('post_pause_play'=>$to_do);

      if($this->basic->update_data('instagram_reply_autoreply',array('id'=>$table_id,'user_id'=>$this->user_id),$update_data)) {
        echo "1";
      } else {
        echo "0";
      }
      
    }

    public function pause_play_campaign()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $table_id = $this->input->post('table_id',true);

        if($table_id == "" || $table_id == 0) exit;

        $to_do = $this->input->post('to_do',true);
        $update_data = array('full_pause_play'=>$to_do);

        if($this->basic->update_data('instagram_reply_autoreply',array('id'=>$table_id,'user_id'=>$this->user_id),$update_data)) {
          echo "1";
        } else {
          echo "0";
        }
    }


    public function mentions_autoreply_submit()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if ($_POST) {
            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key);
            }           
        }

        $mentions_auto_reply_page_id = $mentions_auto_reply_page_id;

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('id' => $mentions_auto_reply_page_id)));
        
        $page_name = $page_info[0]['page_name'];
        $auto_reply_page_id = $page_info[0]['page_id'];

        $return = array();
        $date_time = date("Y-m-d H:i:s");
        $nofilter_array['comment_reply'] = trim($mentions_nofilter_word_found_text);
        $nofilter_array['private_reply'] = trim($mentions_nofilter_word_found_text_private);

        $mentions_multiple_reply                   = $this->input->post('mentions_multiple_reply',true);
        $mentions_hide_comment_after_comment_reply = $this->input->post('mentions_hide_comment_after_comment_reply',true);

        if($mentions_multiple_reply == '') $mentions_multiple_reply = 'no';
        if($mentions_hide_comment_after_comment_reply == '') $mentions_hide_comment_after_comment_reply = 'no';

        $no_filter_array = array();
        array_push($no_filter_array, $nofilter_array);
        $nofilter_word_found_text = json_encode($no_filter_array);
        $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);
        // comment hide and delete section
        $is_delete_offensive = $mentions_delete_offensive_comment;
        $offensive_words = trim($mentions_delete_offensive_comment_keyword);
        $offensive_words = $this->db->escape($offensive_words);
        $private_message_offensive_words = $this->db->escape($mentions_private_message_offensive_words);
        // end of comment hide and delete section
        $page_name = $this->db->escape($page_name);
        $report_type = "mention";
        $facebook_rx_fb_user_info = $this->session->userdata("facebook_rx_fb_user_info");

        if($mentions_message_type == 'generic')
        {
            $generic_message_array['comment_reply'] = trim($mentions_generic_message);
            $generic_message_array['private_reply'] = trim($mentions_generic_message_private);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = '';
            $auto_reply_text = json_encode($generic_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);
            $sql = "INSERT INTO instagram_reply_autoreply (facebook_rx_fb_user_info_id,autoreply_type,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_created_at,post_description,reply_type,report_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,auto_reply_text,last_updated_at,nofilter_word_found_text) VALUES ('$facebook_rx_fb_user_info','mentions_autoreply','$this->user_id','$mentions_auto_campaign_name','$mentions_auto_reply_page_id',$page_name,'','','','$mentions_message_type','$report_type','$mentions_hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$mentions_multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text)"; 
        }
        else
        {
            $auto_reply_text_array = array();
            for ($i = 1; $i <= 20; $i++) {
                $filter_word = 'mentions_filter_word_' . $i;
                $filter_word_text = $this->input->post($filter_word);

                $comment_message = 'mentions_comment_reply_msg_' . $i;
                $comment_message_text = $this->input->post($comment_message);

                $filter_message = 'mentions_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);

                if ($filter_word_text != '' && ($comment_message_text != '' || $filter_message_text != '') ) {
                    $data['filter_word'] = trim($filter_word_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['reply_text'] = trim($filter_message_text);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = '';
            $auto_reply_text = json_encode($auto_reply_text_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);

            $sql = "INSERT INTO instagram_reply_autoreply (facebook_rx_fb_user_info_id,autoreply_type,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_created_at,post_description,reply_type,report_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,auto_reply_text,last_updated_at,nofilter_word_found_text) VALUES ('$facebook_rx_fb_user_info','mentions_autoreply','$this->user_id','$mentions_auto_campaign_name','$mentions_auto_reply_page_id',$page_name,'','','','$mentions_message_type','$report_type','$mentions_hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$mentions_multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text)";
        }
        if ($this->db->query($sql)) {
            //insert data to useges log table
            // $this->_insert_usage_log($module_id = 278, $request = 1);
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        } else {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }


    public function mentions_edit_reply_info()
    {
        $respnse = array();
        $table_id = $this->input->post('table_id');
        $info = $this->basic->get_data('instagram_reply_autoreply', array('where' => array('id' => $table_id,'user_id'=>$this->user_id)));

        if ($info[0]['reply_type'] == 'generic') {
            $reply_content = json_decode($info[0]['auto_reply_text'],true);
            if (!is_array($reply_content)) {
              $reply_content[0]['comment_reply'] = '';
              $reply_content[0]['private_reply'] = $info[0]['auto_reply_text'];
            }
        } else {
          $reply_content = json_decode($info[0]['auto_reply_text'],true);
        }

        $nofilter_word_text = json_decode($info[0]['nofilter_word_found_text']);
        if (!is_array($nofilter_word_text)) {
          $nofilter_word_text[0]['comment_reply'] = '';
          $nofilter_word_text[0]['private_reply'] = $info[0]['nofilter_word_found_text'];
        }

        $respnse['reply_type'] = $info[0]['reply_type'];
        $respnse['multiple_reply'] = $info[0]['multiple_reply'];
        $respnse['auto_reply_text'] = $reply_content;
        $respnse['edit_auto_reply_page_id'] = $info[0]['page_info_table_id'];
        $respnse['edit_auto_campaign_name'] = $info[0]['auto_reply_campaign_name'];
        $respnse['edit_nofilter_word_found_text'] = $nofilter_word_text;
        // comment hide and delete section
        $respnse['is_delete_offensive'] = $info[0]['is_delete_offensive'];
        $respnse['offensive_words'] = $info[0]['offensive_words'];
        $respnse['hide_comment_after_comment_reply'] = $info[0]['hide_comment_after_comment_reply'];

        $respnse['private_message_offensive_words'] = $info[0]['private_message_offensive_words'];
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'ig','page_id'=>$info[0]['page_info_table_id'])),'','','',$start=NULL,'');
        $str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";
        foreach ($postback_data as $key => $value) 
        {
          $str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $respnse['postbacks'] = $str;
        echo json_encode($respnse);
    }

    public function mentions_edit_autoreply_submit()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if ($_POST) {
            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key);
            }
        }

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('id' => $mentions_edit_auto_reply_page_id)));
        $page_name = $page_info[0]['page_name'];
        $auto_reply_page_id = $page_info[0]['page_id'];

        $mentions_edit_multiple_reply                   = $this->input->post('mentions_edit_multiple_reply',true);
        $mentions_edit_hide_comment_after_comment_reply = $this->input->post('mentions_edit_hide_comment_after_comment_reply',true);

        if($mentions_edit_multiple_reply == '') $mentions_edit_multiple_reply = 'no';
        if($mentions_edit_hide_comment_after_comment_reply == '') $mentions_edit_hide_comment_after_comment_reply = 'no';


        $return = array();
        if ($mentions_edit_message_type == 'generic') {
            $generic_message_array['comment_reply'] = trim($mentions_edit_generic_message);
            $generic_message_array['private_reply'] = trim($mentions_edit_generic_message_private);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = json_encode($generic_array);
        } else {
            $auto_reply_text_array = array();
            for ($i = 1; $i <= 20; $i++) {
                $filter_word = 'mentions_edit_filter_word_' . $i;
                $filter_word_text = $this->input->post($filter_word);

                $comment_message = 'mentions_edit_comment_reply_msg_' . $i;
                $comment_message_text = $this->input->post($comment_message);

                $filter_message = 'mentions_edit_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);

                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                {
                    $data['filter_word'] = trim($filter_word_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['reply_text'] = trim($filter_message_text);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = json_encode($auto_reply_text_array);
        }
        $no_filter_array['comment_reply'] = trim($mentions_edit_nofilter_word_found_text);
        $no_filter_array['private_reply'] = trim($mentions_edit_nofilter_word_found_text_private);

        $nofilter_array = array();
        array_push($nofilter_array, $no_filter_array);

        if ($mentions_edit_message_type == 'generic'){
            $message_type = 'generic';
        } else {
            $message_type = 'filter';
        }
        $data = array(
            'auto_reply_text' => $auto_reply_text,
            'reply_type' => $message_type,
            'auto_reply_campaign_name' => $mentions_edit_auto_campaign_name,
            'nofilter_word_found_text' => json_encode($nofilter_array),
            'multiple_reply' => $mentions_edit_multiple_reply,
            'is_delete_offensive' => $mentions_delete_offensive_comment,
            'offensive_words' => trim($mentions_edit_delete_offensive_comment_keyword),
            'hide_comment_after_comment_reply' => $mentions_edit_hide_comment_after_comment_reply,
            'private_message_offensive_words' => trim($mentions_edit_private_message_offensive_words),
        );

        $where = array("id"=>$mentions_autoreply_table_id,'user_id'=>$this->user_id);
        if ($this->basic->update_data('instagram_reply_autoreply', $where, $data)) {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        } else {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function mentions_pause_play_campaign()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $table_id=$this->input->post('table_id');
        if($table_id == "" || $table_id == 0) exit;

        $to_do=$this->input->post('to_do');
        $update_data = array('mentions_pause_play'=>$to_do);

        if($this->basic->update_data('instagram_reply_autoreply',array('id'=>$table_id,'user_id'=>$this->user_id),$update_data)) {
          echo "1";
        } else {
          echo "0";
        }
    }

    public function instant_commnet_submit()
    {
      $this->ajax_check();
      $page_table_id = $this->input->post('page_table_id');
      $post_id = $this->input->post('post_id');
      $message = $this->input->post('message');
      $response = [];

      if(trim($message) == '')
      {
        $response['status'] = 0;
        $response['message'] = $this->lang->line('Please provide your comment first.');
        echo json_encode($response);
        exit;
      }

      //post comment
      $this->load->library('fb_rx_login');

      $select = ['page_access_token','facebook_rx_config_id','instagram_business_account_id','access_token'];
      $where = ['where'=>['facebook_rx_fb_page_info.id'=>$page_table_id,'facebook_rx_fb_page_info.user_id'=>$this->user_id]];
      $join = ['facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left'];
      $info = $this->basic->get_data('facebook_rx_fb_page_info',$where,$select,$join);

      if(empty($info))
      {
        $response['status'] = 0;
        $response['message'] = $this->lang->line('No data found from database.');
        echo json_encode($response);
        exit;
      }

      $app_config_id = $info[0]['facebook_rx_config_id'];
      $page_access_token = $info[0]['page_access_token'];
      $user_access_token = $info[0]['access_token'];
      $instagram_business_account = $info[0]['instagram_business_account_id'];
      $this->fb_rx_login->app_initialize($app_config_id);

      try 
      {
        $response=$this->fb_rx_login->instagram_direct_auto_comment($message,$post_id,$user_access_token);
        $commentid=isset($response['id'])?$response['id']:"";  
        $id = $commentid;
        $post_url = '';
        try 
        {
            $media_info = $this->fb_rx_login->instagram_get_media_url($instagram_business_account,$post_id,$user_access_token);
            $post_url = isset($media_info['permalink']) ? $media_info['permalink'] : '';
            $media_url = isset($media_info['media_url']) ? $media_info['media_url'] : '';           
        } 
        catch (Exception $e) {
        }

        $response['status'] = 1;
        $response['message'] = $this->lang->line("Your comment has been created successfully, you can check it from")." "."<b><a target='_BLANK' href='".$post_url."'>here</a></b>";
        echo json_encode($response);
        exit;
      } 
      catch (Exception $e) 
      {
        $error_msg = $e->getMessage();
        $response['status'] = 0;
        $response['message'] = $error_msg;
        echo json_encode($response);
        exit;
      }

    }


    public function hashTag_search()
    {
      $data = [];
      $data['body'] = "instagram_reply/hashTag/hashTag_search";
      $data['page_title'] = $this->lang->line("HashTag Search");

      $table = "facebook_rx_fb_page_info";
      $where['where'] = ['user_id'=>$this->user_id,"bot_enabled"=>"1","has_instagram"=>"1","facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info")];
      $data['account_lists'] = $this->basic->get_data($table,$where);
      
      $this->_viewcontroller($data);
    }

    public function hashtag_search_result()
    {
      $this->ajax_check();

      $this->load->library("fb_rx_login");

      $account_selected = $this->input->post('account_name', TRUE);
      $hash_tag         = $this->input->post('hash_tag', TRUE);
      $table = "facebook_rx_fb_page_info";
      $where['where'] = array("facebook_rx_fb_page_info.id" => $account_selected, "facebook_rx_fb_page_info.user_id"=>$this->user_id);
      $select = array("page_id","page_name","page_access_token","instagram_business_account_id","insta_username","access_token");
      $join = ['facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left'];
      $page_info = $this->basic->get_data($table,$where,$select,$join);

      if(!empty($page_info)) {

        $this->fb_rx_login->app_initialize($this->session->userdata("fb_rx_login_database_id"));
        $search_result = $this->fb_rx_login->instagram_get_hashtag_id($page_info[0]['instagram_business_account_id'],$hash_tag,$page_info[0]['access_token']);

        $str = "";


        if(isset($search_result['data'])) {

          $hashtag_id = $search_result['data'][0]['id'];
          $user_id = $page_info[0]['instagram_business_account_id'];
          $result_type = "top_media";
          $user_accesstoken = $page_info[0]['access_token'];
          $top_media = $this->fb_rx_login->instagram_get_hashtag_result($user_id,$hashtag_id,$result_type,$user_accesstoken);

          $result_type = "recent_media";
          $recent_media = $this->fb_rx_login->instagram_get_hashtag_result($user_id,$hashtag_id,$result_type,$user_accesstoken);

          $str = '
            <div class="card border">
              <div class="card-header bbw bg-primary text-center d-block">
                <h4 class="text-white">'.$hash_tag.'</h4>
              </div>

              <div class="card-body">
                <h2 class="section-title mt-2">'.$this->lang->line('Top Media').'</h2>
                <div class="media_scroll mb-5"><div class="row">';
                  if(isset($top_media['data']) && !empty($top_media['data'])) {

                    $html_ref = "";
                    foreach($top_media['data'] as $single_media_value) {

                      $permalink = isset($single_media_value['permalink']) ? $single_media_value['permalink']:"";
                      $title = !empty($single_media_value['caption']) ? mb_substr($single_media_value['caption'], 0, 20): $this->lang->line("No Caption"); 

                      if($single_media_value['media_type'] == "IMAGE" || $single_media_value['media_type'] == "CAROUSEL_ALBUM") {
                        $img_src = isset($single_media_value['media_url']) ? $single_media_value['media_url']:base_url("assets/images/carousel_post.jpg");

                        $html_ref = '<img class="article-image" src="'.$img_src.'" alt="media image">';
                      }

                      if($single_media_value['media_type'] == "VIDEO") {
                        $video_src = isset($single_media_value['media_url']) ? $single_media_value['media_url']:"";
                        $html_ref = '<video class="article-image" controls="controls"><source src="'.$video_src.'" type="video/mp4"></video>';
                      }

                      $caption = isset($single_media_value['caption']) ? $single_media_value['caption'] : '';
                      $like_count = isset($single_media_value['like_count']) ? $single_media_value['like_count'] : 0;
                      $comment_count = isset($single_media_value['comments_count']) ? $single_media_value['comments_count'] : 0;

                      $str.='
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <article class="article profile-widget mb-0">
                                <div class="article-header">
                                    <a target="_BLANK" href="'.$permalink.'">'.$html_ref.'</a>

                                    <div class="article-title">
                                        <h2 class="pointer white" title="'.$caption.'">'.$title.'</h2>
                                    </div>
                                </div>
                                <div class="article-details p-0">                  
                                    <div class="profile-widget-items">
                                        <div class="profile-widget-item">
                                            <div class="profile-widget-item-label"><i class="fas fa-heart red" aria-hidden="true"></i></div>
                                            <div class="profile-widget-item-value">'.$like_count.'</div>
                                        </div>
                                        <div class="profile-widget-item">
                                            <div class="profile-widget-item-label"><i class="fas fa-comment blue" aria-hidden="true"></i></div>
                                            <div class="profile-widget-item-value">'.$comment_count.'</div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                      ';
                    }
                  } else {
                    $str .= '
                      <div class="col-12">
                        <div class="empty-state mb-4 height_200px" data-height="200">
                          <div class="empty-state-icon">
                            <i class="fas fa-question"></i>
                          </div><h2>'.$this->lang->line('We could not find any data').'</h2></div>
                      </div>';
                  }
                $str .='</div></div>';

                $str .= '
                  <h2 class="section-title">'.$this->lang->line('Recent Media').'</h2>
                  <div class="media_scroll"><div class="row">';
                    if(isset($recent_media['data']) && !empty($recent_media['data'])) {

                      $html_ref2 = "";

                      foreach($recent_media['data'] as $single_media_value) {

                        $permalink = isset($single_media_value['permalink']) ? $single_media_value['permalink']:"";
                        $title = !empty($single_media_value['caption']) ? mb_substr($single_media_value['caption'], 0, 20): $this->lang->line("No Caption"); 

                        if($single_media_value['media_type'] == "IMAGE" || $single_media_value['media_type'] == "CAROUSEL_ALBUM") {
                          $img_src = isset($single_media_value['media_url']) ? $single_media_value['media_url']:base_url("assets/images/carousel_post.jpg");

                          $html_ref = '<img class="article-image" src="'.$img_src.'" alt="media image">';
                        }

                        if($single_media_value['media_type'] == "VIDEO") {
                          $video_src = isset($single_media_value['media_url']) ? $single_media_value['media_url']:"";
                          $html_ref = '<video class="article-image" controls="controls"><source src="'.$video_src.'" type="video/mp4"></video>';
                        }

                        $caption = isset($single_media_value['caption']) ? $single_media_value['caption'] : '';
                        $like_count = isset($single_media_value['like_count']) ? $single_media_value['like_count'] : 0;
                        $comment_count = isset($single_media_value['comments_count']) ? $single_media_value['comments_count'] : 0;

                        $str.='
                          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                              <article class="article profile-widget mb-0">
                                  <div class="article-header">
                                      <a href="'.$permalink.'" target="_BLANK">'.$html_ref.'</a>

                                      <div class="article-title">
                                          <h2 class="pointer white" title="'.$caption.'">'.$title.'</h2>
                                      </div>
                                  </div>
                                  <div class="article-details p-0">                  
                                      <div class="profile-widget-items">
                                          <div class="profile-widget-item">
                                              <div class="profile-widget-item-label" title="'.$this->lang->line('Reactions').'"><i class="fas fa-heart red" aria-hidden="true"></i></div>
                                              <div class="profile-widget-item-value">'.$like_count.'</div>
                                          </div>
                                          <div class="profile-widget-item">
                                              <div class="profile-widget-item-label" title="'.$this->lang->line('Comments').'"><i class="fas fa-comment blue" aria-hidden="true"></i></div>
                                              <div class="profile-widget-item-value">'.$comment_count.'</div>
                                          </div>
                                      </div>
                                  </div>
                              </article>
                          </div>';
                      }
                    } else {
                      $str .= '
                        <div class="col-12">
                          <div class="empty-state mb-4 height_200px" data-height="200">
                            <div class="empty-state-icon">
                              <i class="fas fa-question"></i>
                            </div><h2>'.$this->lang->line('We could not find any data').'</h2></div>
                        </div>';
                    }
                    
                $str.='</div></div>';

                $str .='</div></div>  
                <script src="'.base_url().'assets/js/system/make_scroll.js"></script>';
          
        }

      }

      echo $str;
    }


    // ====================================== End of New Started ===================================

    public function get_fb_rx_config($fb_user_id=0)
    {
        if($fb_user_id==0) return 0;
        $getdata= $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=>$fb_user_id)),array("facebook_rx_config_id"));
        $return_val = isset($getdata[0]["facebook_rx_config_id"]) ? $getdata[0]["facebook_rx_config_id"] : 0;
        return $return_val; 
       
    }

    public function webhook_callback()
    {  

        $instagram_reply_enable_disable = $this->config->item('instagram_reply_enable_disable');
        if($instagram_reply_enable_disable != 1) exit;
        $response_raw=$this->input->post("response_raw");  

        $response = json_decode($response_raw,TRUE);
        // file_put_contents("mostofa.txt",$response_raw, FILE_APPEND | LOCK_EX); exit();
        
        // $response_raw='{"object": "instagram", "entry": [{"id": "17841444061670612", "time": 1643015209, "changes": [{"value": {"from": {"id": "17841444061670612", "username": "boostuptechs"}, "media": {"id": "17928823672993780", "media_product_type": "FEED"}, "id": "17975493841484491", "text": "Auto comment for instagram"}, "field": "comments"}]}]}';
        // $response = json_decode($response_raw,TRUE);

        if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'mentions')
        {
          if($this->addon_exist('instagram_reply_enhancers'))
          {
            $instagram_business_account = $response['entry'][0]['id'];
            $comment_id = isset($response['entry'][0]['changes'][0]['value']['comment_id']) ? $response['entry'][0]['changes'][0]['value']['comment_id'] : 0;
            $media_id = $response['entry'][0]['changes'][0]['value']['media_id'];

            //mentions auto reply
            $where['where']=array('instagram_reply_autoreply.autoreply_type' => 'mentions_autoreply','facebook_rx_fb_page_info.instagram_business_account_id'=>$instagram_business_account, 'instagram_reply_autoreply.mentions_pause_play'=>'play','bot_enabled'=>'1');
            $select = "instagram_reply_autoreply.autoreply_type,instagram_reply_autoreply.id as column_id,post_id,instagram_business_account_id,page_id,facebook_rx_fb_page_info.page_name as page_name,page_access_token,auto_reply_text,instagram_reply_autoreply.facebook_rx_fb_user_info_id,multiple_reply,reply_type,nofilter_word_found_text,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,hidden_comment_count,deleted_comment_count,auto_comment_reply_count,facebook_rx_fb_user_info.deleted as user_deleted, facebook_rx_fb_user_info.access_token as user_access_token, facebook_rx_fb_page_info.user_id as user_id, instagram_reply_autoreply.page_info_table_id as page_info_table_id,auto_reply_campaign_name";
            $join = array(
                'facebook_rx_fb_page_info' => 'facebook_rx_fb_page_info.id=instagram_reply_autoreply.page_info_table_id,left',
                'facebook_rx_fb_user_info' => 'instagram_reply_autoreply.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left'
            );
            $mentions_autoreply_info = $this->basic->get_data("instagram_reply_autoreply", $where, $select, $join);

            $post_column_id = isset($mentions_autoreply_info[0]['column_id']) ? $mentions_autoreply_info[0]['column_id'] : 0;
            $user_access_token = $mentions_autoreply_info[0]['user_access_token'];
            $page_access_token = $mentions_autoreply_info[0]['page_access_token'];
            
            // setting fb confid id for library call
            $config_id_database=array();
            $fb_rx_fb_user_info_id= $mentions_autoreply_info[0]['facebook_rx_fb_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }

            
            $skip_error_message = '';

            if($config_id_database[$fb_rx_fb_user_info_id] == 0)
            {
                $skip_error_message = "Corresponding Facebook account has been removed from database";
                exit;
            }
            // setting fb confid id for library call
            $this->load->library('fb_rx_login');
            $this->fb_rx_login->app_initialize($config_id_database[$fb_rx_fb_user_info_id]);

            // for second time webhook call skip
            if($comment_id == 0) $current_id = $media_id;
            else $current_id = $comment_id;

            $already_replied_info = $this->basic->get_data('instagram_autoreply_report',['where'=>['autoreply_table_id'=>$post_column_id,'comment_id'=>$current_id]]);
            if(!empty($already_replied_info)) exit;


            if(isset($mentions_autoreply_info) && !empty($mentions_autoreply_info)) {
                foreach ($mentions_autoreply_info as $value) {
                    try {
                        if($comment_id != 0)
                        $comment_list = $this->fb_rx_login->instagram_get_all_comment_of_mention_post($instagram_business_account, $comment_id, $user_access_token);
                        else
                        $comment_list = $this->fb_rx_login->instagram_get_all_comment_of_mention_caption($instagram_business_account, $media_id, $user_access_token);

                        if(isset($comment_list) && !empty($comment_list)){
                            goto mentionsCommentsListSection;
                        } 
                    } catch (Exception $e) {
                        exit;
                    }
                    
                }
            }


            mentionsCommentsListSection:

            $instagram_business_account_id = $mentions_autoreply_info[0]['instagram_business_account_id'];
            $offensive_words = $mentions_autoreply_info[0]['offensive_words'];
            $is_delete_offensive = $mentions_autoreply_info[0]['is_delete_offensive'];
            $user_access_token = $mentions_autoreply_info[0]['user_access_token'];
            $comment_reply_enabled = 'yes';
            $multiple_reply = $mentions_autoreply_info[0]['multiple_reply'];
            $hide_comment_after_comment_reply = $mentions_autoreply_info[0]['hide_comment_after_comment_reply'];
            $auto_reply_message_raw = $mentions_autoreply_info[0]['auto_reply_text'];
            $auto_reply_type = $mentions_autoreply_info[0]['reply_type'];

            $default_reply_no_filter = json_decode($mentions_autoreply_info[0]['nofilter_word_found_text'],true);
            if(is_array($default_reply_no_filter))
            {
              $default_reply_no_filter_comment = $default_reply_no_filter[0]['comment_reply'];
              $default_reply_no_filter_private = $default_reply_no_filter[0]['private_reply'];
            }
            else
            {
                $default_reply_no_filter_comment = "";
                $default_reply_no_filter_private = $mentions_autoreply_info[0]['nofilter_word_found_text'];
            }

            $comment_list_array = array();
            if(isset($comment_list['mentioned_comment'])) $comment_list_array[0] = $comment_list['mentioned_comment'];
            if(isset($comment_list['mentioned_media'])) $comment_list_array[0] = $comment_list['mentioned_media'];

            $post_permalink = '';
            $post_media_link = '';
            $media_type = '';

            foreach ($comment_list_array as $comment_info) {
                if($comment_info['id'] == $current_id) {
                    // $comment_text = '@mostofa.ru hello';
                    if (function_exists('iconv') && function_exists('mb_detect_encoding')) {
                        if($comment_id != 0){
                          $encoded_comment = mb_detect_encoding($comment_info['text']);
                          $comment_text    = $comment_info['text'];
                        }
                        else
                        {
                          $encoded_comment = mb_detect_encoding($comment_info['caption']);
                          $comment_text    = $comment_info['caption'];  
                        }

                        if (isset($encoded_comment)) {
                            $comment_text = iconv($encoded_comment, "UTF-8//TRANSLIT", $comment_text);
                        }
                    }

                    $commenter_id = $comment_info['username'];
                    $commenter_name = $comment_info['username'];
                    $commenter_name_tag = "@" . $comment_info['username'];
                    $comment_time = $comment_info['timestamp'];
                    $auto_reply_comment_message = "";
                    $auto_reply_private_message="";
                    // do not reply if the commenter is account owner
                    if ($instagram_business_account_id == $commenter_id) exit;
                    // comment hide and delete section
                    // to prevent duplicate reply
                    if ($multiple_reply == 'no') {
                      $already_replied_info = $this->basic->get_data('instagram_autoreply_report',['where'=>['autoreply_table_id'=>$post_column_id,'commenter_id'=>$commenter_id]]);
                      if(!empty($already_replied_info)) exit;
                    }
                    /** If not sent, then sent him reply ***/
                    if ($auto_reply_type == 'generic') {
                        $auto_generic_reply__array = json_decode($auto_reply_message_raw, TRUE);
                        if (is_array($auto_generic_reply__array)) {
                            $auto_generic_reply__array[0]['comment_reply'] = $auto_generic_reply__array[0]['comment_reply'];
                            $auto_reply_private_message = $auto_generic_reply__array[0]['private_reply'];

                        } else {
                            $auto_generic_reply__array[0]['comment_reply'] = "";
                        }
                        $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#', $commenter_name, $auto_generic_reply__array[0]['comment_reply']);
                        $auto_reply_comment_message = str_replace('#TAG_USER#', $commenter_name_tag, $auto_reply_comment_message);
                    }
                    if ($auto_reply_type == "filter") {
                        $auto_reply_message_array = json_decode($auto_reply_message_raw, TRUE);
                        foreach ($auto_reply_message_array as $message_info) {
                            $filter_word = $message_info['filter_word'];
                            $filter_word = explode(",", $filter_word);
                            foreach ($filter_word as $f_word) {
                                if (function_exists('iconv') && function_exists('mb_detect_encoding')) {
                                    $encoded_word = mb_detect_encoding($f_word);
                                    if (isset($encoded_word)) {
                                        $f_word = iconv($encoded_word, "UTF-8//TRANSLIT", $f_word);
                                    }
                                }
                                $pos = stripos($comment_text, trim($f_word));
                                if ($pos !== FALSE) {
                                    $auto_reply_comment_message_individual = $message_info['comment_reply_text'];
                                    $auto_reply_private_message = $message_info['private_reply'];

                                    $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#', $commenter_name, $auto_reply_comment_message_individual);
                                    $auto_reply_comment_message = str_replace('#TAG_USER#', $commenter_name_tag, $auto_reply_comment_message);
                                    break;
                                }
                            }
                            if ($pos !== FALSE) break;
                        }
                        if ($auto_reply_comment_message == '') {
                            $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#', $commenter_name, $default_reply_no_filter_comment);
                            $auto_reply_comment_message = str_replace('#TAG_USER#', $commenter_name_tag, $auto_reply_comment_message);
                        }
                    }

                    $insert_data = array(
                        "comment_id" => $current_id,
                        "comment_text" => $comment_text,
                        "commenter_name" => $commenter_name,
                        "commenter_id" => $commenter_id,
                        "comment_time" => $comment_time,
                        "reply_time" => date("Y-m-d H:i:s")
                    );

                    $auto_reply_comment_message = spintax_process($auto_reply_comment_message);
                    $insert_data['comment_reply_text'] = $auto_reply_comment_message;

                    if ($comment_reply_enabled == 'yes' && $auto_reply_comment_message != '') {         
                        try {
                            if($comment_id != 0)
                            $reply_info = $this->fb_rx_login->instagram_auto_mention_comment($auto_reply_comment_message, $media_id, $user_access_token,$instagram_business_account,$comment_id);
                            else
                            $reply_info = $this->fb_rx_login->instagram_auto_mention_caption_comment($auto_reply_comment_message, $media_id, $user_access_token,$instagram_business_account);
                            $insert_data['reply_status_comment'] = "success";
                            $insert_data['auto_comment_reply_count'] = 1;
                            if ($hide_comment_after_comment_reply == 'yes') {
                                try {
                                    $this->fb_rx_login->instagram_hide_comment($comment_id, $user_access_token);
                                    $insert_data['reply_status_comment'] = "comment hidden";
                                    $insert_data['hidden_comment_count'] = 1;
                                } catch (Exception $e) {
                                }
                            }
                        } catch (Exception $e) {
                            $insert_data['reply_status_comment'] = $e->getMessage();
                        }
                    }



                  try{

                    if($auto_reply_private_message!=""){
                    
                        $template_id=$auto_reply_private_message;
                        $postback_id_info= $this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$template_id)));
                        $template_json_code=isset($postback_id_info[0]['template_jsoncode']) ? $postback_id_info[0]['template_jsoncode'] :"";
                        $template_array_code=json_decode($template_json_code,TRUE);
                        $temp_result = [];
                        $temp_counter = 0;
                        foreach($template_array_code as $reply_message)
                        {
                          unset($reply_message['message']['template_type']);
                          unset($reply_message['message']['typing_on_settings']);
                          unset($reply_message['message']['delay_in_reply']);

                          if(isset($reply_message['message']['text']))
                            $reply_message['message']['text']=spintax_process($reply_message['message']['text']);

                          //$reply_message['messaging_type'] = "RESPONSE";
                          $reply_message['tag'] = "HUMAN_AGENT";

                          $reply_message=json_encode($reply_message);
                          $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                          $replace_with=array('{"comment_id":"'.$comment_id.'"}',$commenter_id);
                          $reply_message=str_replace($replace_search, $replace_with, $reply_message);

                          if(isset($commenter_name))
                            $reply_message=str_replace('#LEAD_FULL_NAME#', $commenter_name, $reply_message);
                          if(isset($commenter_name_tag))
                            $reply_message=str_replace('#TAG_USER#', $commenter_name_tag, $reply_message);

                          $send_reply_info=$this->send_reply($page_access_token,$reply_message);

                          if(isset($send_reply_info['error']))
                          {
                              $temp_result['reply_status'][$temp_counter]= $send_reply_info['error']['message'];
                              $temp_result['reply_id'][$temp_counter]="";
                              $temp_result['reply_text'][$temp_counter] = $auto_reply_private_message;
                          }
                          else
                          {

                              $temp_result['reply_status'][$temp_counter]= "success";
                              $temp_result['reply_id'][$temp_counter]=isset($send_reply_info['message_id'])?$send_reply_info['message_id']:"";
                              $temp_result['reply_text'][$temp_counter] = $auto_reply_private_message;
                          } 
                          $temp_counter++;

                        } //end of foreach
                        //$insert_data_comment['reply_text'] = json_encode($temp_result['reply_text']);
                        $insert_data['reply_status'] = json_encode($temp_result['reply_status']);
                       // $insert_data_comment['reply_id'] = json_encode($temp_result['reply_id']);
                
                    }
                    else{
                        $insert_data['reply_status']= "Not Replied ! No match found corresponding filter words";
                        //$insert_data['reply_id']="";
                    }
                  }

                  catch(Exception $e){
                      $insert_data['reply_status']= $e->getMessage();
                     // $insert_data['reply_id']="";
                  }



                    $insert_data['post_id'] = $media_id;
                    $insert_data['reply_type'] = 'mention';
                    $insert_data['autoreply_table_id'] = $mentions_autoreply_info[0]['column_id'];
                    $insert_data['user_id'] = $mentions_autoreply_info[0]['user_id'];
                    $this->basic->insert_data('instagram_autoreply_report',$insert_data);
                    $insert_id = $this->db->insert_id();

                    if(isset($comment_info['media']))
                    {
                        $post_permalink = isset($comment_info['media']['permalink']) ? $comment_info['media']['permalink'] : '';
                        $post_media_link = isset($comment_info['media']['media_url']) ? $comment_info['media']['media_url'] : '';
                        $media_type = isset($comment_info['media']['media_type']) ? $comment_info['media']['media_type'] : '';

                        $update_data = array("post_url"=>$post_permalink,"media_url"=>$post_media_link,"media_type"=>$media_type);
                        $this->basic->update_data('instagram_autoreply_report',array('id'=>$insert_id),$update_data);                        
                    }
                    else
                    {
                        try 
                        {
                            $media_info = $this->fb_rx_login->instagram_get_media_url($instagram_business_account,$media_id,$user_access_token);
                            $post_url = isset($media_info['permalink']) ? $media_info['permalink'] : '';
                            $media_url = isset($media_info['media_url']) ? $media_info['media_url'] : '';
                            $media_type = isset($media_info['media_type']) ? $media_info['media_type'] : '';
                            $update_data = array("post_url"=>$post_url,"media_url"=>$media_url,"media_type"=>$media_type);
                            $this->basic->update_data('instagram_autoreply_report',array('id'=>$insert_id),$update_data);            
                        } 
                        catch (Exception $e) {
                        }
                    }
               }
            }
          } // end of if exist condition

        }

        if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'comments')
        {
          if(isset($response['entry'][0]['changes'][0]['value']['id']) && isset($response['entry'][0]['id']) && !empty($response['entry'][0]['changes'][0]['value']['id']) && !empty($response['entry'][0]['id'])){

              $instagram_business_account = $response['entry'][0]['id'];
              $commenter_id = $response['entry'][0]['changes'][0]['value']['from']['id'] ?? '';
              if($instagram_business_account == $commenter_id) exit;

              $comment_id = $response['entry'][0]['changes'][0]['value']['id'];
              $comment_text = $response['entry'][0]['changes'][0]['value']['text'];

              $table_name = "facebook_rx_fb_user_info";
              $where = array();
              $where['where'] = array('facebook_rx_fb_page_info.instagram_business_account_id' => $instagram_business_account,'bot_enabled'=>'1');
              $join = array('facebook_rx_fb_page_info' => "facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left");
              $instra_info = $this->basic->get_data($table_name, $where, array("facebook_rx_fb_user_info.access_token as user_access_token","facebook_rx_fb_user_info.id","facebook_rx_fb_user_info.user_id as user_id","facebook_rx_fb_page_info.page_access_token"), $join);
              $user_access_token = $instra_info[0]['user_access_token'];
              $page_access_token = $instra_info[0]['page_access_token'];

              $user_id = $instra_info[0]['user_id'];

              if(empty($instra_info)) exit;

              // setting fb confid id for library call
              $facebook_rx_fb_user_info_id = $instra_info[0]['id'];
              if (!isset($config_id_database[$facebook_rx_fb_user_info_id])) {
                  $config_id_database[$facebook_rx_fb_user_info_id] = $this->get_fb_rx_config($facebook_rx_fb_user_info_id);
              }

              $skip_error_message = '';
              if ($config_id_database[$facebook_rx_fb_user_info_id] == 0) {
                  $skip_error_message = "Corresponding Facebook account has been removed from database";
                  exit;
              }
              // setting fb confid id for library call
              $this->load->library("fb_rx_login");
              $this->fb_rx_login->app_initialize($config_id_database[$facebook_rx_fb_user_info_id]);

              $media_info = $this->fb_rx_login->instagram_get_media_info_by_comment($comment_id,$user_access_token);
              $media_id = $media_info['media']['id'];
              $media_text = $media_info['text'];
              $commenter_username = $media_info['username'];


              $where['where']=array('instagram_reply_autoreply.post_id'=> $media_id,'instagram_reply_autoreply.autoreply_type' => 'post_autoreply','facebook_rx_fb_page_info.instagram_business_account_id'=>$instagram_business_account,'bot_enabled'=>'1','post_pause_play'=>'play');

              $select = "instagram_reply_autoreply.autoreply_type,instagram_reply_autoreply.id as column_id,post_id,instagram_business_account_id,page_id,facebook_rx_fb_page_info.page_name as page_name,page_access_token,auto_reply_text,instagram_reply_autoreply.facebook_rx_fb_user_info_id,multiple_reply,reply_type,nofilter_word_found_text,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,hidden_comment_count,deleted_comment_count,auto_comment_reply_count, facebook_rx_fb_user_info.access_token as user_access_token,facebook_rx_fb_page_info.user_id as user_id,instagram_reply_autoreply.page_info_table_id as page_info_table_id,auto_reply_campaign_name,trigger_matching_type";

              $join = array(
                  'facebook_rx_fb_page_info' => 'facebook_rx_fb_page_info.id=instagram_reply_autoreply.page_info_table_id,left',
                  'facebook_rx_fb_user_info' => 'instagram_reply_autoreply.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left'
              );

              $autoreply_info = $this->basic->get_data("instagram_reply_autoreply", $where, $select, $join, $limit = '50', $start = '0', $order_by = 'last_reply_time ASC');


              if(isset($autoreply_info) && !empty($autoreply_info)){
                  foreach ($autoreply_info as $value) {
                      try {
                          $comment_list = $this->fb_rx_login->instagram_get_all_comment_of_post($media_id, $value['user_access_token']);
                          if(isset($comment_list) && !empty($comment_list)){
                            goto commentsListSection;
                          }                       
                      } catch (Exception $e) {
                      }
                  }
              }


              //account auto reply
              if($this->addon_exist('instagram_reply_enhancers'))
              {
                $where['where']=array('instagram_reply_autoreply.autoreply_type' => 'account_autoreply','facebook_rx_fb_page_info.instagram_business_account_id'=>$instagram_business_account, 'instagram_reply_autoreply.full_pause_play'=>'play','bot_enabled'=>'1');
                $select = "instagram_reply_autoreply.autoreply_type,instagram_reply_autoreply.id as column_id,post_id,instagram_business_account_id,page_id,facebook_rx_fb_page_info.page_name as page_name,page_access_token,auto_reply_text,instagram_reply_autoreply.facebook_rx_fb_user_info_id,multiple_reply,reply_type,nofilter_word_found_text,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,hidden_comment_count,deleted_comment_count,auto_comment_reply_count, facebook_rx_fb_user_info.access_token as user_access_token,facebook_rx_fb_page_info.user_id as user_id,instagram_reply_autoreply.page_info_table_id as page_info_table_id,auto_reply_campaign_name,trigger_matching_type";
                $join = array(
                    'facebook_rx_fb_page_info' => 'facebook_rx_fb_page_info.id=instagram_reply_autoreply.page_info_table_id,left',
                    'facebook_rx_fb_user_info' => 'instagram_reply_autoreply.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left'
                );
                $autoreply_info = [];
                $autoreply_info = $this->basic->get_data("instagram_reply_autoreply", $where, $select, $join, $limit = '50', $start = '0', $order_by = 'last_reply_time ASC');

                if(isset($autoreply_info) && !empty($autoreply_info)) {
                    foreach ($autoreply_info as $value) {
                        try {
                            $comment_list = $this->fb_rx_login->instagram_get_all_comment_of_post($media_id, $value['user_access_token']);
                            if(isset($comment_list) && !empty($comment_list)){
                                goto commentsListSection;
                            } 
                        } catch (Exception $e) {
                        }
                    }
                }
              }


              commentsListSection:

              $trigger_matching_type = isset($autoreply_info[0]['trigger_matching_type']) ? $autoreply_info[0]['trigger_matching_type'] : 'exact';

              if($autoreply_info[0]['autoreply_type'] == 'account_autoreply') $report_type = 'full';
              if($autoreply_info[0]['autoreply_type'] == 'post_autoreply') $report_type = 'post';

              $post_column_id= isset($autoreply_info[0]['column_id']) ? $autoreply_info[0]['column_id'] : 0;
              // for second time webhook call skip
              $already_replied_info = $this->basic->get_data('instagram_autoreply_report',['where'=>['autoreply_table_id'=>$post_column_id,'comment_id'=>$comment_id]]);
              if(!empty($already_replied_info)) exit;
              
              $instagram_business_account_id = $autoreply_info[0]['instagram_business_account_id'];
              $offensive_words = $autoreply_info[0]['offensive_words'];
              $is_delete_offensive = $autoreply_info[0]['is_delete_offensive'];
              $comment_reply_enabled = 'yes';
              $multiple_reply = $autoreply_info[0]['multiple_reply'];
              $hide_comment_after_comment_reply = $autoreply_info[0]['hide_comment_after_comment_reply'];
              $auto_reply_message_raw = $autoreply_info[0]['auto_reply_text'];
              $auto_reply_type = $autoreply_info[0]['reply_type'];

              $private_message_offensive_words = $autoreply_info[0]['private_message_offensive_words'];

              $default_reply_no_filter = isset($autoreply_info[0]['nofilter_word_found_text']) ? json_decode($autoreply_info[0]['nofilter_word_found_text'],true) : array();
              if(is_array($default_reply_no_filter))
              {
                  $default_reply_no_filter_comment = $default_reply_no_filter[0]['comment_reply'];
                  $default_reply_no_filter_private = $default_reply_no_filter[0]['private_reply'];
              }
              else
              {
                  $default_reply_no_filter_comment = "";
                  $default_reply_no_filter_private = $autoreply_info[0]['nofilter_word_found_text'];
              }


              foreach ($comment_list as $comment_info) {
                 if($comment_info['id'] == $comment_id) {

                      // if (function_exists('iconv') && function_exists('mb_detect_encoding')) {
                      //     $encoded_comment = mb_detect_encoding($comment_text);
                      //     if (isset($encoded_comment)) {
                      //         $comment_text = iconv($encoded_comment, "UTF-8//TRANSLIT", $comment_text);
                      //     }
                      // }

                      // split words from message into one/two/three words
                      if($trigger_matching_type == 'exact')
                      {
                        $single_words_from_message_array = [];
                        $twowords_from_message_array = [];
                        $three_words_from_message_array = [];
                        if(function_exists('iconv') && function_exists('mb_detect_encoding'))
                        {
                            $encoded_message = mb_detect_encoding($comment_text);
                            if(isset($encoded_message))
                                $comment_text = iconv($encoded_message, "UTF-8//TRANSLIT", $comment_text);
                            $words_from_message = mb_split(' ', $comment_text);
                            
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
                      // end of word spliting section

                      $commenter_id = $commenter_username;
                      $commenter_name = $commenter_username;
                      $commenter_name_tag = "@" . $commenter_username;
                      $comment_time = $comment_info['timestamp'];
                      $auto_reply_comment_message = "";
                      // comment hide and delete section
                      $is_delete = 0;
                      $is_hidden = 0;
                      $offensive_words_array = explode(',', $offensive_words);
                      foreach ($offensive_words_array as $key => $value) {

                          if($trigger_matching_type == 'exact')
                          {
                            $search_array = [];
                            $temp_cam_keywords_array = [];
                            $temp_cam_keywords_array = explode(" ", $value);
                            if(count($temp_cam_keywords_array) == 1) $search_array = $single_words_from_message_array;
                            else if(count($temp_cam_keywords_array) == 2) $search_array = $twowords_from_message_array;
                            else if(count($temp_cam_keywords_array) == 3) $search_array = $three_words_from_message_array;

                            if(in_array($value, $search_array))
                              $pos = TRUE;
                            else $pos = FALSE;
                          }
                          else
                            $pos = stripos($comment_text, trim($value));


                          if($pos !== FALSE)
                          {
                              if ($is_delete_offensive == 'delete') {
                                  try {
                                      $insert_data = array(
                                          "comment_id" => $comment_id,
                                          "comment_text" => $comment_text,
                                          "commenter_name" => $commenter_name,
                                          "commenter_id" => $commenter_id,
                                          "comment_time" => $comment_time,
                                          "reply_time" => date("Y-m-d H:i:s"),
                                          "user_id" => $user_id,
                                          "post_id" => $media_id

                                      );

                                      // private reply section
                                      if($private_message_offensive_words != '')
                                      {
                                       
                                          $template_id=$private_message_offensive_words;
                                          $postback_id_info= $this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$template_id)));
                                          $template_json_code=isset($postback_id_info[0]['template_jsoncode']) ? $postback_id_info[0]['template_jsoncode'] :"";
                                          $template_array_code=json_decode($template_json_code,TRUE);
                                          $temp_result = [];
                                          $temp_counter = 0;
                                          foreach($template_array_code as $reply_message)
                                          {
                                            unset($reply_message['message']['template_type']);
                                            unset($reply_message['message']['typing_on_settings']);
                                            unset($reply_message['message']['delay_in_reply']);

                                            if(isset($reply_message['message']['text']))
                                              $reply_message['message']['text']=spintax_process($reply_message['message']['text']);

                                             $reply_message['tag'] = "HUMAN_AGENT";
                                            $reply_message=json_encode($reply_message);
                                            $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                                            $replace_with=array('{"comment_id":"'.$comment_id.'"}',$commenter_id);
                                            $reply_message=str_replace($replace_search, $replace_with, $reply_message);


                                           if(isset($commenter_name))
                                              $reply_message=str_replace('#LEAD_FULL_NAME#', $commenter_name, $reply_message);
                                            if(isset($commenter_name_tag))
                                              $reply_message=str_replace('#TAG_USER#', $commenter_name_tag, $reply_message);

                                            $send_reply_info=$this->send_reply($page_access_token,$reply_message);

                                            if(isset($send_reply_info['error'])){
                                                $temp_result['reply_status'][$temp_counter]= $send_reply_info['error']['message'];
                                                $temp_result['reply_id'][$temp_counter]="";
                                                $temp_result['reply_text'][$temp_counter] = $private_message_offensive_words;
                                                $insert_data['auto_private_reply_count'] = 1;
                                            }
                                            else
                                            {

                                                $temp_result['reply_status'][$temp_counter]= "success";
                                                $temp_result['reply_id'][$temp_counter]=isset($send_reply_info['message_id'])?$send_reply_info['message_id']:"";
                                                $temp_result['reply_text'][$temp_counter] = $private_message_offensive_words;
                                            } 
                                            $temp_counter++;

                                          } //end of foreach
                                          //$insert_data['reply_text'] = json_encode($temp_result['reply_text']);
                                          $insert_data['reply_status'] = json_encode($temp_result['reply_status']);
                                          //$insert_data['reply_id'] = json_encode($temp_result['reply_id']);
                                      
                                          
                                      }
                                      else
                                      {
                                          $insert_data['reply_text'] = '';
                                          $insert_data['reply_status']= '';
                                          $insert_data['reply_id']="";
                                      }
                                      // end of private reply section

                                      $this->fb_rx_login->instagram_delete_comment($comment_id, $user_access_token);
                                      $insert_data['reply_status_comment'] = "comment deleted";
                                      $insert_data['deleted_comment_count'] = 1;
                                      $insert_data['comment_reply_text'] = '';
                                      if($report_type == 'full')
                                        $insert_data['reply_type'] = 'full';
                                      else
                                        $insert_data['reply_type'] = 'post';
                                      $insert_data['autoreply_table_id'] = $post_column_id;
                                      $this->basic->insert_data('instagram_autoreply_report',$insert_data);
                                      $insert_id = $this->db->insert_id();

                                      try 
                                      {
                                          $media_info = $this->fb_rx_login->instagram_get_media_url($instagram_business_account,$media_id,$user_access_token);
                                          $post_url = isset($media_info['permalink']) ? $media_info['permalink'] : '';
                                          $media_url = isset($media_info['media_url']) ? $media_info['media_url'] : '';
                                          $media_type = isset($media_info['media_type']) ? $media_info['media_type'] : '';
                                          $update_data = array("post_url"=>$post_url,"media_url"=>$media_url,"media_type"=>$media_type);
                                          if($report_type == 'full')
                                            $this->basic->update_data('instagram_autoreply_report',array('id'=>$insert_id),$update_data);
                                          else
                                            $this->basic->update_data('instagram_reply_autoreply',array('id'=>$post_column_id),$update_data);

                                      } 
                                      catch (Exception $e) {
                                      }

                                      $is_delete = 1;
                                      break;
                                  } catch (Exception $e) {
                                  }
                              }
                              if ($is_delete_offensive == 'hide') {
                                  if(empty($already_replied_info)) {
                                      try {
                                          $this->fb_rx_login->instagram_hide_comment($comment_id, $user_access_token);
                                          $is_hidden = 1;
                                      }catch (Exception $e) {
                                      }
                                  }
                              }
                          }
                      }

                      if ($is_delete) continue;
                      if ($multiple_reply == 'no') {
                        $already_replied_info = $this->basic->get_data('instagram_autoreply_report',['where'=>['autoreply_table_id'=>$post_column_id,'commenter_id'=>$commenter_name]]);
                        if(!empty($already_replied_info)) continue;
                      }
                      /** If not sent, then sent him reply ***/
                      if ($auto_reply_type == 'generic') {
                          $auto_generic_reply__array = json_decode($auto_reply_message_raw, TRUE);

                          if (is_array($auto_generic_reply__array)) {
                              $auto_generic_reply__array[0]['comment_reply'] = $auto_generic_reply__array[0]['comment_reply'];
                              $auto_generic_reply__array[0]['private_reply'] = $auto_generic_reply__array[0]['private_reply'];

                          } else {
                              $auto_generic_reply__array[0]['comment_reply'] = "";
                              $auto_generic_reply__array[0]['private_reply'] = $auto_reply_message_raw;
                          }

                          $auto_reply_private_message = $auto_generic_reply__array[0]['private_reply'];

                          $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#', $commenter_name, $auto_generic_reply__array[0]['comment_reply']);
                          $auto_reply_comment_message = str_replace('#TAG_USER#', $commenter_name_tag, $auto_reply_comment_message);
                      }
                      if ($auto_reply_type == "filter") {
                          $auto_reply_private_message_array = json_decode($auto_reply_message_raw, TRUE);
                          foreach ($auto_reply_private_message_array as $message_info) {
                              $filter_word = $message_info['filter_word'];
                              $filter_word = explode(",", $filter_word);
                              foreach ($filter_word as $f_word) {

                                  if (function_exists('iconv') && function_exists('mb_detect_encoding')) {
                                      $encoded_word = mb_detect_encoding($f_word);
                                      if (isset($encoded_word)) {
                                          $f_word = iconv($encoded_word, "UTF-8//TRANSLIT", $f_word);
                                      }
                                  }

                                  if($trigger_matching_type == 'exact')
                                  {
                                    $search_array = [];
                                    $temp_cam_keywords_array = [];
                                    $temp_cam_keywords_array = explode(" ", $f_word);
                                    if(count($temp_cam_keywords_array) == 1) $search_array = $single_words_from_message_array;
                                    else if(count($temp_cam_keywords_array) == 2) $search_array = $twowords_from_message_array;
                                    else if(count($temp_cam_keywords_array) == 3) $search_array = $three_words_from_message_array;

                                    if(in_array($f_word, $search_array))
                                      $pos = TRUE;
                                    else $pos = FALSE;
                                  }
                                  else
                                    $pos = stripos($comment_text, trim($f_word));

                                  if($pos !== FALSE){

                                    $auto_reply_comment_message_individual = $message_info['comment_reply_text'];
                                    $auto_reply_private_message_individual = $message_info['reply_text'];

                                    $auto_reply_private_message = $auto_reply_private_message_individual;

                                    $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#', $commenter_name, $auto_reply_comment_message_individual);
                                    $auto_reply_comment_message = str_replace('#TAG_USER#', $commenter_name_tag, $auto_reply_comment_message);
                                    break;
                                  }
                              }
                              if ($pos !== FALSE) break;
                          }
                          if ($auto_reply_comment_message == '') {
                              $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#', $commenter_name, $default_reply_no_filter_comment);
                              $auto_reply_comment_message = str_replace('#TAG_USER#', $commenter_name_tag, $auto_reply_comment_message);
                          }
                      }

                      $insert_data_comment = array(
                          "comment_id" => $comment_id,
                          "comment_text" => $comment_text,
                          "commenter_name" => $commenter_name,
                          "commenter_id" => $commenter_id,
                          "comment_time" => $comment_time,
                          "reply_time" => date("Y-m-d H:i:s"),
                          "user_id" => $user_id,
                          "post_id" => $media_id

                      );
                      if($report_type == 'full')
                        $insert_data_comment['reply_type'] = 'full';
                      else
                        $insert_data_comment['reply_type'] = 'post';
                      $insert_data_comment['autoreply_table_id'] = $post_column_id;

                      if($is_hidden)
                      {
                        $insert_data_comment['comment_reply_text'] = '';
                        $insert_data_comment['reply_status_comment'] = "";
                        $insert_data_comment['reply_status_comment'] = "comment hidden";
                        $insert_data_comment['hidden_comment_count'] = 1;
                      }

                      $auto_reply_comment_message = spintax_process($auto_reply_comment_message);
                      if ($comment_reply_enabled == 'yes' && $auto_reply_comment_message != '') {
                          try {
                              $reply_info = $this->fb_rx_login->instagram_auto_comment($auto_reply_comment_message, $comment_id, $user_access_token);
                              $insert_data_comment['comment_reply_text'] = $auto_reply_comment_message;
                              $insert_data_comment['reply_status_comment'] = "success";
                              $insert_data_comment['auto_comment_reply_count'] = 1;

                              if ($hide_comment_after_comment_reply == 'yes') {
                                  try {
                                      $this->fb_rx_login->instagram_hide_comment($comment_id, $user_access_token);
                                      $insert_data_comment['reply_status_comment'] = "comment hidden";
                                      $insert_data_comment['hidden_comment_count'] = 1;
                                  } catch (Exception $e) {
                                  }
                              }
                          } catch (Exception $e) {
                              $insert_data_comment['reply_status_comment'] = $e->getMessage();
                          }
                      }




                      try{

                        if($auto_reply_private_message!=""){
                        
                            $template_id=$auto_reply_private_message;
                            $postback_id_info= $this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$template_id)));
                            $template_json_code=isset($postback_id_info[0]['template_jsoncode']) ? $postback_id_info[0]['template_jsoncode'] :"";
                            $template_array_code=json_decode($template_json_code,TRUE);
                            $temp_result = [];
                            $temp_counter = 0;
                            $auto_reply_counter = 0;
                            foreach($template_array_code as $reply_message)
                            {
                              unset($reply_message['message']['template_type']);
                              unset($reply_message['message']['typing_on_settings']);
                              unset($reply_message['message']['delay_in_reply']);

                              if(isset($reply_message['message']['text']))
                                $reply_message['message']['text']=spintax_process($reply_message['message']['text']);

                              //$reply_message['messaging_type'] = "RESPONSE";
                              $reply_message['tag'] = "HUMAN_AGENT";

                              $reply_message=json_encode($reply_message);
                              $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                              $replace_with=array('{"comment_id":"'.$comment_id.'"}',$commenter_id);
                              $reply_message=str_replace($replace_search, $replace_with, $reply_message);

                              if(isset($commenter_name))
                                $reply_message=str_replace('#LEAD_FULL_NAME#', $commenter_name, $reply_message);
                              if(isset($commenter_name_tag))
                                $reply_message=str_replace('#TAG_USER#', $commenter_name_tag, $reply_message);

                              $send_reply_info=$this->send_reply($page_access_token,$reply_message);

                              if(isset($send_reply_info['error'])){
                                  $temp_result['reply_status'][$temp_counter]= $send_reply_info['error']['message'];
                                  $temp_result['reply_id'][$temp_counter]="";
                                  $temp_result['reply_text'][$temp_counter] = $auto_reply_private_message;
                              }
                              else
                              {
                                  $auto_reply_counter++;
                                  $temp_result['reply_status'][$temp_counter]= "success";
                                  $temp_result['reply_id'][$temp_counter]=isset($send_reply_info['message_id'])?$send_reply_info['message_id']:"";
                                  $temp_result['reply_text'][$temp_counter] = $auto_reply_private_message;
                              } 
                              $temp_counter++;

                            } //end of foreach
                            //$insert_data_comment['reply_text'] = json_encode($temp_result['reply_text']);
                            $insert_data_comment['reply_status'] = json_encode($temp_result['reply_status']);
                           // $insert_data_comment['reply_id'] = json_encode($temp_result['reply_id']);
                    
                        }
                        else{
                            $insert_data_comment['reply_status']= "Not Replied ! No match found corresponding filter words";
                            $insert_data_comment['reply_id']="";
                        }
                      }

                      catch(Exception $e){
                          $insert_data_comment['reply_status']= $e->getMessage();
                          $insert_data_comment['reply_id']="";
                      }


                      $insert_data_comment['auto_private_reply_count'] = $auto_reply_counter;

                      $this->basic->insert_data('instagram_autoreply_report',$insert_data_comment);
                      $insert_id = $this->db->insert_id();

                      try 
                      {
                          $media_info = $this->fb_rx_login->instagram_get_media_url($instagram_business_account,$media_id,$user_access_token);
                          $post_url = isset($media_info['permalink']) ? $media_info['permalink'] : '';
                          $media_url = isset($media_info['media_url']) ? $media_info['media_url'] : '';
                          $media_type = isset($media_info['media_type']) ? $media_info['media_type'] : '';
                          if($report_type == 'full')
                          {
                            $update_data = array("post_url"=>$post_url,"media_url"=>$media_url,"media_type"=>$media_type);
                            $this->basic->update_data('instagram_autoreply_report',array('id'=>$insert_id),$update_data);
                          }
                          else
                          {
                            $update_data = array("post_url"=>$post_url,"media_url"=>$media_url,"media_type"=>$media_type,"last_reply_time" => date("Y-m-d H:i:s"));
                            $this->basic->update_data('instagram_reply_autoreply',array('id'=>$post_column_id),$update_data);
                          }

                      } 
                      catch (Exception $e) {
                      }
                 }
              }

          }
        }
    }


    public function enable_disable_commnets()
    {
      $this->ajax_check();
      $page_table_id = $this->input->post('page_table_id',true);
      $to_do = $this->input->post('to_do',true);
      $post_id = $this->input->post('post_id',true);

      $info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['facebook_rx_fb_page_info.id'=>$page_table_id,'facebook_rx_fb_page_info.user_id'=>$this->user_id]],['page_access_token','facebook_rx_config_id','access_token'],['facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left']);
      $page_access_token = isset($info[0]['page_access_token']) ? $info[0]['page_access_token'] : '';
      $user_accesstoken = isset($info[0]['access_token']) ? $info[0]['access_token'] : '';
      $facebook_rx_config_id = isset($info[0]['facebook_rx_config_id']) ? $info[0]['facebook_rx_config_id'] : '';
      $this->load->library('fb_rx_login');
      $this->fb_rx_login->app_initialize($facebook_rx_config_id);
      if($to_do == 'enable') $is_enable = true;
      else $is_enable = false;
      $result = $this->fb_rx_login->instagram_media_comment_enable_disable($post_id,$user_accesstoken,$is_enable);
      if(isset($result['success']) && $result['success'] == 1) echo "1";
      else
        echo '0';

    }


    public function get_all_comments_of_post()
    {
      $this->ajax_check();
      $page_table_id = $this->input->post('page_table_id',true);
      $post_id = $this->input->post('post_id',true);
      $info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['facebook_rx_fb_page_info.id'=>$page_table_id,'facebook_rx_fb_page_info.user_id'=>$this->user_id]],['page_access_token','facebook_rx_config_id','access_token'],['facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left']);
      $page_access_token = isset($info[0]['page_access_token']) ? $info[0]['page_access_token'] : '';
      $user_access_token = isset($info[0]['access_token']) ? $info[0]['access_token'] : '';
      $facebook_rx_config_id = isset($info[0]['facebook_rx_config_id']) ? $info[0]['facebook_rx_config_id'] : '';
      $this->load->library('fb_rx_login');
      $this->fb_rx_login->app_initialize($facebook_rx_config_id);
      $comment_info = $this->fb_rx_login->instagram_get_all_comment_of_post($post_id,$user_access_token);
      
      $html = '
        <div class="card mb-0" id="comment_lists">
          <div class="card-header bg-primary">
              <h4 id="display-tracking-name" class="text-white"><i class="fas fa-list-alt"></i> '.$this->lang->line('Comment Lists').'</h4>
              <div class="card-header-action">
                <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">&times;</button>
              </div>
          </div>
          <div class="card-body p-0">
              <div id="activecampaign-list-group" class="list-group">';
                if(!empty($comment_info)) {
                  $html .='<div class="tickets-list makeScroll">';
                      foreach ($comment_info as $value) {

                        if(mb_strlen($value['text']) >= 95)
                            $comment_text = mb_substr($value['text'], 0, 90).'...';
                        else $comment_text = $value['text'];

                        $html .='
                          <div class="ticket-item list-group-item-action border border-bottom-0">
                            <div class="ticket-title mb-3">
                              <h4 class="text-primary">
                                <small class="float-right text-muted font_size_12px">'.date("M j, Y H:i A",strtotime($value['timestamp'])).'</small>'.$comment_text.'</h4>
                              </div>
                              <div class="row"><div class="col-12 col-md-6">
                                <div class="ticket-info float-left">
                                  <div>by</div>&nbsp;
                                  <div class="text-primary"><a target="_blank" href="https://instagram.com/'.$value['username'].'">'.$value['username'].'</a></div>
                                </div></div>
                            </div>
                          </div>
                        ';
                      }
                      
                  $html .='</div>';
                } else {
                  $html .= '
                    <div class="tickets-list">
                      <a href="#" class="ticket-item list-group-item-action border border-bottom-0">
                        <div class="ticket-title">
                          <h4 class="text-center">'.$this->lang->line('Sorry, No data Available').'</h4>
                        </div>
                      </a>
                    </div>
                  ';
                }
          $html .='
              </div>
          </div>
        </div>';

        if($this->session->userdata("is_mobile")=='0')
        $html .= '<script src="'.base_url().'assets/js/system/make_scroll.js"></script>';

      echo $html;
      
    }

    public function get_all_tagged_media()
    {
      $this->ajax_check();
      $page_table_id = $this->input->post('page_table_id',true);
      $info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['facebook_rx_fb_page_info.id'=>$page_table_id,'facebook_rx_fb_page_info.user_id'=>$this->user_id]],['page_access_token','facebook_rx_config_id','instagram_business_account_id','access_token'],['facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left']);
      $page_access_token = isset($info[0]['page_access_token']) ? $info[0]['page_access_token'] : '';
      $user_access_token = isset($info[0]['access_token']) ? $info[0]['access_token'] : '';
      $facebook_rx_config_id = isset($info[0]['facebook_rx_config_id']) ? $info[0]['facebook_rx_config_id'] : '';
      $instagram_business_account_id = isset($info[0]['instagram_business_account_id']) ? $info[0]['instagram_business_account_id'] : '';
      $this->load->library('fb_rx_login');
      $this->fb_rx_login->app_initialize($facebook_rx_config_id);
      $tagged_media = $this->fb_rx_login->instagram_tagged_media($instagram_business_account_id,$user_access_token);
     
      if(empty($tagged_media))
      {
        $response = '
            <div class="card no_shadow" id="nodata">
              <div class="card-body">
                <div class="empty-state">
                  <img class="img-fluid height_200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                  <h2 class="mt-0">'.$this->lang->line("We could not find any data.").'</h2>
                </div>
              </div>
            </div>';
        echo $response;
        exit;
      }
      else
      {
        $response = '
          <div class="card main_card">
            <div class="card-header">
             <div class="col-12 col-md-4 padding-0">
              <h4><i class="fas fa-tags"></i> '.$this->lang->line('Tagged Media').'</h4>
             </div> 

            </div>
            <div class="card-body">
              <div class="makeScroll">
                <ul class="list-unstyled list-unstyled-border" id="post_list_ul">';
                  foreach ($tagged_media as $key => $value) {

                    $caption = isset($value['caption']) ? $value['caption'] : '';
                    // need to check mb is enabled or not
                    if(mb_strlen($caption) >= 61)
                        $caption = mb_substr($caption, 0, 59).'...';
                    else $caption = $caption;

                    $post_created_at = $value['timestamp']." UTC";
                    $post_created_at=date("d M y H:i",strtotime($post_created_at));

                    $thumbnail = "";
                    $media_url = isset($value['media_url']) ? $value['media_url'] : "";

                    if ($value['media_type'] == "IMAGE" || $value['media_type'] == "CAROUSEL_ALBUM") 
                    {
                        $thumbnail = $media_url;
                    } 

                    if($thumbnail=="" || $value['media_type'] == "VIDEO") {
                      $thumbnail=base_url('assets/img/avatar/avatar-1.png');
                    }

                    $response .= '
                      <li class="media">
                        <div class="avatar-item">
                          <img alt="image border" src="'.$thumbnail.'" width="70" height="70" data-toggle="tooltip" title="'.date_time_calculator($post_created_at,true).'">
                        </div>
                        <div class="media-body">
                          <div class="media-title">
                            <a href="'.$value['permalink'].'" target="_BLANK">'.$value['id'].'</a>
                          </div>
                          <span class="text-small"><i class="fas fa-clock"></i> '.date_time_calculator($post_created_at,true).'</span> : 
                          <span class="text-small text-muted text-justify">'.$caption.'</span>
                          <div class="text-small text-muted text-justify">'.$this->lang->line('Tagged by').' : <a target="_BLANK" href="https://instagram.com/'.$value['username'].'">'.$value['username'].'</a></div>
                        </div>
                      </li>
                    ';
                  }
                      
              $response .='</ul>
            </div>
          </div>
          <script src="'.base_url().'assets/js/system/tooltip_popover.js"></script>
          <script src="'.base_url().'assets/js/system/make_scroll.js"></script>
          </div>
        ';
      }

      echo $response;

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
            $push_postback.="<option value=''>".$this->lang->line("Select a message template")."</option>";
        }

        if($is_from_add_button=='1' || $is_from_add_button=='')
        {
            $push_postback.="<option value=''>".$this->lang->line("Select a message template")."</option>";
        }
        
        foreach ($postback_data as $key => $value) 
        {
            $push_postback.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        echo $push_postback;   
    }

    
    public function template_manager()
    {   

      $media_type = $this->session->userdata('selected_global_media_type');
      if($media_type == 'fb') {
        redirect('comment_automation/template_manager');
      }

      $data['body'] = 'instagram_reply/template_manager';
      $data['page_title'] = $this->lang->line('Comment Reply Template Manager');

      $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
      $page_info = $this->basic->get_data('facebook_rx_fb_page_info',
        array('where'=>
          array(
            "facebook_rx_fb_page_info.facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),
            'facebook_rx_fb_page_info.user_id'=>$this->user_id,
            'bot_enabled'=>'1',
            'has_instagram'=>'1'
          )
        ),
        array(
          'facebook_rx_fb_page_info.id',
          'page_name',
          'name',
          'facebook_rx_fb_page_info.has_instagram',
          'facebook_rx_fb_page_info.insta_username'
        ),$join);

      $page_list = array();
      foreach($page_info as $value)
      {
          $page_list[$value['id']] = $value['insta_username']." [".$value['page_name']."]";
      }
      $data['page_list'] = $page_list;


      $this->_viewcontroller($data);
    }

    public function autoreply_template_manager_data()
    {
        $this->ajax_check();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','auto_reply_campaign_name','ig_username');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;
        $sql = '';
        if ($search_value != '') 
        {
          $sql = "(auto_reply_campaign_name like '%".$search_value."%')";
          $this->db->where($sql);
        }
        
        $where_simple['instagram_reply_template.user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $table = "instagram_reply_template";
        $info  = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');
        
        if($sql != '') $this->db->where($sql);
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function delete_template()
    {
      $this->ajax_check();
      $table_id = $this->input->post('table_id',true);
      $result = $this->basic->delete_data('instagram_reply_template', ['id' => $table_id, 'user_id' => $this->user_id]);
      
      echo "successfull";
    }

    public function create_template_action()
    {
        $this->load->library("fb_rx_login");
        if ($_POST) {
            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key,true);
            }
        }

        $this->fb_rx_login->app_initialize($this->session->userdata("fb_rx_login_database_id"));
        $auto_campaign_name = strip_tags($auto_campaign_name);

        $return = array();
        $date_time = date("Y-m-d H:i:s");
        $nofilter_array['comment_reply'] = trim($nofilter_word_found_text);
        $nofilter_array['private_reply'] = trim($nofilter_word_found_text_private);
        $multiple_reply                   = $this->input->post('multiple_reply',true);
        $hide_comment_after_comment_reply = $this->input->post('hide_comment_after_comment_reply',true);

        if($multiple_reply == '') $multiple_reply = 'no';
        if($hide_comment_after_comment_reply == '') $hide_comment_after_comment_reply = 'no';
        
        $no_filter_array = array();
        array_push($no_filter_array, $nofilter_array);
        $nofilter_word_found_text = json_encode($no_filter_array);
        $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);
        // comment hide and delete section
        $is_delete_offensive = $delete_offensive_comment;
        $offensive_words = trim($delete_offensive_comment_keyword);
        $offensive_words = $this->db->escape($offensive_words);
        // end of comment hide and delete section

        $private_message_offensive_words = $this->db->escape($private_message_offensive_words);

        $page_id = $this->input->post("ig_account_page_id",true);
        $account_info = $this->basic->get_data("facebook_rx_fb_page_info",['where'=>['id'=>$page_id,'user_id'=>$this->user_id]],['insta_username']);

        $insta_username = $this->db->escape($account_info[0]['insta_username']);
        
        if ($message_type == 'generic') {
            $generic_message_array['comment_reply'] = trim($generic_message);
            $generic_message_array['private_reply'] = trim($generic_message_private);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = '';
            $auto_reply_text = json_encode($generic_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);
            $sql = "INSERT INTO instagram_reply_template (user_id,auto_reply_campaign_name,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,multiple_reply,auto_reply_text,last_updated_at,nofilter_word_found_text,private_message_offensive_words,page_id,ig_username) VALUES ('$this->user_id','$auto_campaign_name','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,'$multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text,$private_message_offensive_words,$page_id,$insta_username
            )
            ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,multiple_reply='$multiple_reply',auto_reply_campaign_name='$auto_campaign_name',nofilter_word_found_text=$nofilter_word_found_text,private_message_offensive_words=$private_message_offensive_words,page_id=$page_id,ig_username=$insta_username";
        } else {
            $auto_reply_text_array = array();
            for ($i = 1; $i <= 20; $i++) {
                $filter_word = 'filter_word_' . $i;
                $filter_word_text = $this->input->post($filter_word,true);
                $filter_message = 'filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);
                $comment_message = 'comment_reply_msg_' . $i;
                $comment_message_text = $this->input->post($comment_message,true);

                if ($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != '')) {
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = '';
            $auto_reply_text = json_encode($auto_reply_text_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);
            
            $sql = "INSERT INTO instagram_reply_template (user_id,auto_reply_campaign_name,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,multiple_reply,auto_reply_text,last_updated_at,nofilter_word_found_text,private_message_offensive_words,page_id,ig_username,trigger_matching_type) VALUES ('$this->user_id','$auto_campaign_name','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,'$multiple_reply',$auto_reply_text,'$date_time',$nofilter_word_found_text,$private_message_offensive_words,$page_id,$insta_username,'$trigger_matching_type')
            ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,multiple_reply='$multiple_reply',auto_reply_campaign_name='$auto_campaign_name',nofilter_word_found_text=$nofilter_word_found_text,private_message_offensive_words=$private_message_offensive_words,page_id=$page_id,ig_username=$insta_username,trigger_matching_type='$trigger_matching_type'";
        }
        if ($this->db->query($sql)) {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        } else {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function edit_template()
    {
        $respnse = array();
        $table_id = $this->input->post('table_id');
        $info = $this->basic->get_data('instagram_reply_template', array('where' => array('id' => $table_id, 'user_id' => $this->user_id)));

        $ex_page_id = $info[0]['page_id'];

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','page_id'=>$ex_page_id,'media_type'=>'ig')),'','','',$start=NULL,'');
        $str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";

        foreach ($postback_data as $key => $value) 
        {
            $str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $respnse['postbacks'] = $str;

        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',
          array('where'=>
            array(
              'facebook_rx_fb_page_info.user_id'=>$this->user_id,
              'bot_enabled'=>'1',
              'has_instagram'=>'1'
            )
          ),
          array(
            'facebook_rx_fb_page_info.id',
            'page_name',
            'name',
            'facebook_rx_fb_page_info.has_instagram',
            'facebook_rx_fb_page_info.insta_username'
          ),$join);

        $page_lists = array();
        foreach($page_info as $value)
        {
            $page_lists[$value['id']] = $value['insta_username']." [".$value['page_name']."]";
        }
        $page_lists[''] = $this->lang->line("Please select an account");
        $page_dropdown = form_dropdown('ig_account_edit_page_id',$page_lists,$ex_page_id,'class="form-control select2" id="ig_account_edit_page_id" style="width:100%"');

        $respnse['ig_page_list'] = $page_dropdown." <script>$('#ig_account_edit_page_id').select2();</script>";

        if($info[0]['reply_type'] == 'generic') 
        {
            $reply_content = json_decode($info[0]['auto_reply_text']);
            if (!is_array($reply_content)) 
            {
                $reply_content[0]['comment_reply'] = "";
                $reply_content[0]['private_reply'] = $info[0]['auto_reply_text'];
            }
        } 
        else
          $reply_content = json_decode($info[0]['auto_reply_text']);

        $nofilter_word_text = json_decode($info[0]['nofilter_word_found_text']);
        if (!is_array($nofilter_word_text)) {
            $nofilter_word_text[0]['comment_reply'] = '';
            $nofilter_word_text[0]['private_reply'] = $info[0]['nofilter_word_found_text'];
        }
        $respnse['table_id'] = $info[0]['id'];
        $respnse['reply_type'] = $info[0]['reply_type'];
        $respnse['multiple_reply'] = $info[0]['multiple_reply'];
        $respnse['auto_reply_text'] = $reply_content;
        $respnse['edit_auto_campaign_name'] = $info[0]['auto_reply_campaign_name'];
        $respnse['edit_nofilter_word_found_text'] = $nofilter_word_text;
        $respnse['is_delete_offensive'] = $info[0]['is_delete_offensive'];
        $respnse['offensive_words'] = $info[0]['offensive_words'];
        $respnse['hide_comment_after_comment_reply'] = $info[0]['hide_comment_after_comment_reply'];
        $respnse['private_message_offensive_words'] = $info[0]['private_message_offensive_words'];
        $respnse['trigger_matching_type'] = $info[0]['trigger_matching_type'];
        
        echo json_encode($respnse);
    }

    public function edit_template_action()
    {
        if ($_POST) {
            $post = $_POST;
            foreach ($post as $key => $value) {
                $$key = $this->input->post($key,true);
            }
        }

        $table_id = $this->input->post('table_id',true);
        $edit_multiple_reply = $this->input->post('edit_multiple_reply',true);
        $edit_hide_comment_after_comment_reply = $this->input->post('edit_hide_comment_after_comment_reply',true);

        if($edit_multiple_reply == '') $edit_multiple_reply = 'no';
        if($edit_hide_comment_after_comment_reply == '') $edit_hide_comment_after_comment_reply = 'no';


        $page_id = $this->input->post("ig_account_edit_page_id",true);
        $account_info = $this->basic->get_data("facebook_rx_fb_page_info",['where'=>['id'=>$page_id,'user_id'=>$this->user_id]],['insta_username']);

        $insta_username = $account_info[0]['insta_username'];

        $return = array();
        if ($edit_message_type == 'generic') {
            $generic_message_array['comment_reply'] = trim($edit_generic_message);
            $generic_message_array['private_reply'] = trim($edit_generic_message_private);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = json_encode($generic_array);
        } else {
            $auto_reply_text_array = array();
            for ($i = 1; $i <= 20; $i++) {
                $filter_word = 'edit_filter_word_' . $i;
                $filter_word_text = $this->input->post($filter_word,true);
                $filter_message = 'edit_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);
                $comment_message = 'edit_comment_reply_msg_' . $i;
                $comment_message_text = $this->input->post($comment_message,true);
                if ($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != '')) {
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = json_encode($auto_reply_text_array);
        }
        $no_filter_array['comment_reply'] = trim($edit_nofilter_word_found_text);
        $no_filter_array['private_reply'] = trim($edit_nofilter_word_found_text_private);
        $nofilter_array = array();
        array_push($nofilter_array, $no_filter_array);
        $edit_auto_campaign_name = strip_tags($edit_auto_campaign_name);
        $data = array(
            'auto_reply_text' => $auto_reply_text,
            'reply_type' => $edit_message_type,
            'auto_reply_campaign_name' => $edit_auto_campaign_name,
            'nofilter_word_found_text' => json_encode($nofilter_array),
            'multiple_reply' => $edit_multiple_reply,
            'is_delete_offensive' => $edit_delete_offensive_comment,
            'offensive_words' => trim($edit_delete_offensive_comment_keyword),
            'hide_comment_after_comment_reply' => $edit_hide_comment_after_comment_reply,
            'private_message_offensive_words' => trim($edit_private_message_offensive_words),
            'page_id' => $page_id,
            'ig_username' => $insta_username,
            'trigger_matching_type' => $edit_trigger_matching_type
        );
        $where = array(
            'user_id' => $this->user_id,
            'id' => $table_id
        );
        if ($this->basic->update_data('instagram_reply_template', $where, $data)) {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        } else {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function get_private_reply_postbacks()
    {
      $this->ajax_check();
      $page_table_ids = $this->input->post('page_table_ids',true);
      $is_from_add_button=$this->input->post('is_from_add_button',true);
      if($is_from_add_button == '1') $order_by = "id DESC";
      else
        $order_by = "id ASC";
      $str = '';
      if(!empty($page_table_ids))
      {        
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'ig'),"where_in"=>array("page_id"=>$page_table_ids)),'','','',$start=NULL,$order_by);
        if($is_from_add_button != '1')
          $str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";

        foreach ($postback_data as $key => $value) 
        {
            $str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }

        if($is_from_add_button == '1')
          $str .= "<option value=''>".$this->lang->line('Please select a message template')."</option>";
      }
      else
        $str = "<option value=''>".$this->lang->line('Please select a page first to see the message template.')."</option>";

      $response = array();
      $response['options'] = $str;

      echo json_encode($response);
    }

}