<?php

require_once("Home.php"); // including home controller

class Comment_automation extends Home
{

    public function __construct()
    {
      parent::__construct();
      $function_name=$this->uri->segment(2);
      if($function_name!="webhook_callback_main" && $function_name!='send_autoreply_with_postid') 
      {
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($this->module_access, array(278,279,80))) == 0)
        redirect('home/login_page', 'location'); 
      }

      if($function_name!="" && $function_name!="index" && $function_name!="comment_template_manager" && $function_name!="template_manager" && $function_name!="template_manager_data" && $function_name!="autoreply_template_manager_data" && $function_name!="comment_section_report" && $function_name!="delete_comment" && $function_name!="delete_template" && $function_name!="create_template_action" && $function_name!="autoreply_template_submit" && $function_name!="ajaxselect")
      {
        if($function_name!="webhook_callback_main" && $function_name!='send_autoreply_with_postid') {
             if($this->session->userdata("facebook_rx_fb_user_info")==0)
             redirect('social_accounts/index','refresh');
        }
      
        $this->load->library("fb_rx_login");
      }

      $this->member_validity(); 

    }


    public function index()
    {
      if(addon_exist($module_id=320,$addon_unique_name="instagram_bot")) {
        if($this->session->userdata('selected_global_media_type') == "ig") {
          redirect("instagram_reply/get_account_lists");
        }
      }
      
      $this->get_page_list();
    }

    public function hide_comment_automation_message()
    {
      $this->ajax_check();
      if($this->session->userdata('user_type') != 'Admin') {
        echo '0';
        exit();
      }
      unlink(APPPATH.'show_comment_automation_message.txt');
      echo "1";
    }


    public function get_page_list()
    {
      $this->is_broadcaster_exist=$this->broadcaster_exist();
      $data['body'] = 'comment_automation/auto_reply_page_list';
      $data['page_title'] = $this->lang->line('Facebook Comment Automation Campaign');
      // echo $this->session->userdata('selected_global_page_table_id');exit;

      $data['auto_comment_template'] = $this->basic->get_data('auto_comment_reply_tb',array("where"=>array('user_id'=>$this->user_id)),array('id','template_name'));

      $data["time_zone"]= $this->_time_zone_list();
      $data["periodic_time"] = $this->get_periodic_time();

      $page_info = array();
      $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("bot_enabled"=>'1',"user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),"","","","","page_name asc");
      if(!empty($page_list))
      {
          $i = 1;
          $selected_page_id = $this->session->userdata('selected_global_page_table_id');
          foreach($page_list as $value)
          {
              if($value['id'] == $selected_page_id)
              {
                  $page_info[0]['id'] = $value['id'];
                  $page_info[0]['page_id'] = $value['page_id'];
                  $page_info[0]['page_profile'] = $value['page_profile'];
                  $page_info[0]['page_name'] = $value['page_name'];
              }
              else
              {                    
                  $page_info[$i]['id'] = $value['id'];
                  $page_info[$i]['page_id'] = $value['page_id'];
                  $page_info[$i]['page_profile'] = $value['page_profile'];
                  $page_info[$i]['page_name'] = $value['page_name'];
              }
              $i++;

          }
      }
      ksort($page_info);

      $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id,'structured_message'=>'yes')),array('id','ultrapost_campaign_name'));

      $data["page_info"] = $page_info;
      $data['emotion_list'] = $this->get_emotion_list();

      $this->_viewcontroller($data);
    }


    public function get_page_details()
    {
        $page_table_id = $this->input->post('page_table_id',true);
        $this->session->set_userdata('selected_global_page_table_id',$page_table_id);

        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id,"user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        $page_comment_reply_info = array();
        $middle_column_content = '';
        $right_column_content = '';
        $error_msg = '';
        if(!empty($page_info))
        {
            $autoreply_info = $this->basic->get_data('facebook_ex_autoreply',array('where'=>array('page_info_table_id'=>$page_info[0]['id'])),'','','','','last_reply_time desc');
            $autoreply_count = $this->basic->get_data('facebook_ex_autoreply',array('where'=>array('page_info_table_id'=>$page_info[0]['id'])),array('sum(auto_private_reply_count) as auto_private_reply_count'));

            $autocomment_info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('page_info_table_id'=>$page_info[0]['id'],"social_media_type"=>"Facebook")),'','','','','last_reply_time desc');
            $autocomment_count = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('page_info_table_id'=>$page_info[0]['id'],"social_media_type"=>"Facebook")),array('sum(auto_comment_count) as auto_comment_count'));


            $tagenable_info = $this->basic->get_data('tag_machine_enabled_post_list',array('where'=>array('page_info_table_id'=>$page_info[0]['id'])));
            $tagreply_info = $this->basic->get_data('tag_machine_bulk_reply',array('where'=>array('page_info_table_id'=>$page_info[0]['id'])),'','','','','last_updated_at desc');
            $tagreply_sent = 0;
            foreach($tagreply_info as $value)
            {
              $tagreply_sent = $tagreply_sent+$value['successfully_sent'];
            }

            $page_comment_reply_info['auto_reply_enabled_post'] = count($autoreply_info);
            if(!empty($autoreply_info) && $autoreply_info[0]['last_reply_time']!='0000-00-00 00:00:00') $page_comment_reply_info['last_auto_reply_sent'] = date_time_calculator($autoreply_info[0]['last_reply_time'],true);
            else $page_comment_reply_info['last_auto_reply_sent'] = 'Not replied yet';

            if(empty($autoreply_count))
                $page_comment_reply_info['autoreply_count'] = 0;
            else
                $page_comment_reply_info['autoreply_count'] = ($autoreply_count[0]['auto_private_reply_count']=='') ? 0 : $autoreply_count[0]['auto_private_reply_count'];

            $page_comment_reply_info['auto_comment_enabled_post'] = count($autocomment_info);
            if(!empty($autocomment_info) && $autocomment_info[0]['last_reply_time']!='0000-00-00 00:00:00') $page_comment_reply_info['last_auto_comment_sent'] = date_time_calculator($autocomment_info[0]['last_reply_time'],true);
            else $page_comment_reply_info['last_auto_comment_sent'] = 'Not commented yet';

            if(empty($autocomment_count))
                $page_comment_reply_info['autocomment_count'] = 0;
            else
                $page_comment_reply_info['autocomment_count'] = ($autocomment_count[0]['auto_comment_count']=='') ? 0 : $autocomment_count[0]['auto_comment_count'];

            $page_comment_reply_info['page_name'] = $page_info[0]['page_name'];
            $page_comment_reply_info['tag_enabled_post'] = count($tagenable_info);
            $page_comment_reply_info['total_tagreply_sent'] = $tagreply_sent;
            if(!empty($tagreply_info) && $tagreply_info[0]['last_updated_at']!='0000-00-00 00:00:00') $page_comment_reply_info['last_tagreply_sent'] = date_time_calculator($tagreply_info[0]['last_updated_at'],true);
            else $page_comment_reply_info['last_tagreply_sent'] = 'Not replied yet';

            $full_response_pause_play_button = '';
            $pageresponse_enabled_pages = $this->basic->get_data('page_response_autoreply',array('where'=>array('user_id'=>$this->user_id,'page_info_table_id'=>$page_table_id)),array('page_info_table_id','id','pause_play'));
            $pageresponse_button='';
            if(empty($pageresponse_enabled_pages))
                $pageresponse_button = '<a href="#" page_table_id="'.$page_info[0]['id'].'" page_id="'.$page_info[0]['page_id'].'" class="btn btn-sm small btn-info enable_page_response"><i class="fas fa-cog"></i> '. $this->lang->line("Enable Full Page Reply").'</a>';
            else
            {
              $pause_or_play =$is_checked= '';
              if($pageresponse_enabled_pages[0]['pause_play'] == 'play') {
                $pause_or_play = 'pause';
                $is_checked = 'checked';
              } else {
                $pause_or_play = 'play';
                $is_checked = '';

              }

              $full_response_pause_play_button = '
              <label class="custom-switch float-right">
                <input type="checkbox" name="selected_global_media_type" id="selected_global_media_type" value="1" class="custom-switch-input fullpage_pause_play" pause_play="'.$pause_or_play.'" table_id="'.$pageresponse_enabled_pages[0]['id'].'" '.$is_checked.'>
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description">'.$this->lang->line('On').'</span>
              </label>
              ';

              // if($pageresponse_enabled_pages[0]['pause_play'] == 'play')
              //   $full_response_pause_play_button = '<a href="#" class="float-right fullpage_pause_play" table_id="'.$pageresponse_enabled_pages[0]['id'].'" pause_play="pause" data-toggle="tooltip" title="'.$this->lang->line("Stop Campaign").'" alt-content="'.$this->lang->line("Do you really want to stop full page campaign?").'" ><i class="fas fa-toggle-on font_size_16px"></i></a>';
              // else
              //   $full_response_pause_play_button = '<a href="#" class="float-right fullpage_pause_play" table_id="'.$pageresponse_enabled_pages[0]['id'].'" pause_play="play" data-toggle="tooltip" title="'.$this->lang->line("Start Campaign").'" alt-content="'.$this->lang->line("Do you really want to start full page campaign?").'" ><i class="fas fa-toggle-off font_size_16px"></i></a>';

              $pageresponse_button = '<a href="#" table_id="'.$pageresponse_enabled_pages[0]['id'].'" class="btn btn-sm small btn-warning pageresponse_edit_reply_info"><i class="fas fa-edit"></i> '. $this->lang->line("Edit Full Page Reply").'</a>';
            }


            $pageresponse_likeshare_enabled = $this->basic->get_data('page_response_auto_like_share',array('where'=>array('user_id'=>$this->user_id,'page_info_table_id'=>$page_table_id)),array('page_info_table_id','id'));
            $autolikeshare_button = '';
            if(empty($pageresponse_likeshare_enabled))
                $autolikeshare_button = '<a href="#" class="btn btn-sm small btn-primary enable_auto_share" page_response_user_info_id="'.$page_info[0]['facebook_rx_fb_user_info_id'].'" page_table_id="'.$page_info[0]['id'].'" page_id="'.$page_info[0]['page_id'].'" ><i class="fas fa-cog"></i> '. $this->lang->line("Enable Like & Share").'</a>';
            else
                $autolikeshare_button = '<a href="#" class="btn btn-sm small btn-warning edit_auto_share" page_response_user_info_id="'.$page_info[0]['facebook_rx_fb_user_info_id'].'" table_id="'.$pageresponse_likeshare_enabled[0]['id'].'" page_id="'.$page_info[0]['page_id'].'" ><i class="fas fa-edit"></i> '. $this->lang->line("Edit Like & Share").'</a>';


            $middle_column_content .= '
                <div class="card main_card">
                  <div class="card-header">
                    <h4><i class="fab fa-facebook-square"></i> <a target="_BLANK" href="https://www.facebook.com/'.$page_info[0]['page_id'].'">'.$page_comment_reply_info['page_name'].'</a></h4>
                  </div>
                  <div class="card-body">
                    <div class="summary">             
                      <div class="summary-item">
                        <ul class="list-unstyled list-unstyled-border">
                          <li class="media">                    

                            <img class="mr-3 rounded" width="50" src="../assets/img/icon/reply.png">
                            
                            <div class="media-body">
                              <div class="media-right badge badge-light small">'.$page_comment_reply_info['auto_reply_enabled_post'].'</div>
                              <div class="media-title">'. $this->lang->line('Auto Reply Enabled Posts').'</div>
                              <div class="text-muted text-small">'. $this->lang->line("Response").' : <b>'.$page_comment_reply_info['autoreply_count'].'</b> <div class="bullet"></div> '.$page_comment_reply_info['last_auto_reply_sent'].'</div>
                            </div>
                          </li>';
            if($this->session->userdata('user_type') == 'Admin' || in_array(251,$this->module_access)) :
            $middle_column_content .= '
                          <li class="media">
                            <a href="#">
                              <img class="mr-3 rounded" width="50" src="../assets/img/icon/comment.png">
                            </a>
                            <div class="media-body">
                              <div class="media-right badge badge-light small">'.$page_comment_reply_info['auto_comment_enabled_post'].'</div>
                              <div class="media-title">'. $this->lang->line('Auto Comment Enabled Posts').'</div>
                              <div class="text-muted text-small">'. $this->lang->line("Comments").' : <b>'.$page_comment_reply_info['autocomment_count'].'</b> <div class="bullet"></div> '.$page_comment_reply_info['last_auto_comment_sent'].'
                              </div>
                            </div>
                          </li>';
            endif;

            if($this->basic->is_exist("add_ons",array("project_id"=>29)))
            if($this->session->userdata('user_type') == 'Admin' || in_array(201,$this->module_access)) :
            $middle_column_content .= '
                          <li class="media">
                            <a href="#">
                              <img class="mr-3 rounded" width="50" src="../assets/img/icon/tag.png">
                            </a>
                            <div class="media-body">
                              <div class="media-right badge badge-light small">'.$page_comment_reply_info['tag_enabled_post'].'</div>
                              <div class="media-title">'. $this->lang->line('Tag Reply Enabled Posts').'</div>
                              <div class="text-muted text-small">'. $this->lang->line("Response").' : <b>'.$page_comment_reply_info['total_tagreply_sent'].'</b> <div class="bullet"></div> '.$page_comment_reply_info['last_tagreply_sent'].'
                              </div>
                            </div>
                          </li>';
            endif;

            $middle_column_content .= '
                        </ul>
                      </div>';
          if($this->basic->is_exist("add_ons",array("project_id"=>29)))
          if($this->session->userdata('user_type') == 'Admin' || in_array(204,$this->module_access)) :
            $middle_column_content .= '
                      <div class="card card-primary">
                        <div class="card-header">
                          <h4 class="full_width">'
                            .$this->lang->line("Full Page Campaigns").$full_response_pause_play_button.                            
                          '</h4>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-12 col-md-12 col-lg-6">
                                <div class="product-item pb-3">
                                  <div class="product-image">
                                    <img src="../assets/img/icon/page.png" class="img-fluid rounded-circle">
                                  </div>
                                  <div class="product-details">
                                    <div class="product-name">'. $this->lang->line("Comment & Inbox Reply").'</div>                      
                                    <div class="product-cta">
                                      '.$pageresponse_button.'
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 col-lg-6">
                                <div class="product-item pb-3">
                                  <div class="product-image">
                                    <img src="../assets/img/icon/like.png" class="img-fluid rounded-circle">
                                  </div>
                                  <div class="product-details">
                                    <div class="product-name">'. $this->lang->line("Auto Like & Share").'</div>                      
                                    <div class="product-cta">
                                      '.$autolikeshare_button.'
                                    </div>
                                  </div>
                                </div>
                            </div>
                          </div>
                        </div>
                      </div>';
          endif;
            $middle_column_content .= '
                    </div>
                  </div>
                  <div class="card-footer text-center">
                    <div class="dropdown droptop">
                        <a href="#" data-toggle="dropdown" class="btn btn-outline-primary dropdown-toggle"><i class="fas fa-eye"></i> '. $this->lang->line('See Campaign Reports').'</a>
                        <div class="dropdown-menu large">
                          <a href="'.base_url('comment_automation/auto_reply_report').'/'.$page_table_id.'" class="dropdown-item has-icon"><i class="fas fa-comment-dots"></i> '. $this->lang->line("Auto Reply Report").'</a>
                          <a href="'.base_url('comment_automation/all_auto_comment_report').'/'.$page_table_id.'" class="dropdown-item has-icon"><i class="fas fa-comment-alt"></i> '. $this->lang->line("Auto Comment Report").'</a>';
                          
                          if($this->basic->is_exist("add_ons",array("project_id"=>29)))
                          if($this->session->userdata('user_type') == 'Admin' || in_array(201,$this->module_access)) :
                            $middle_column_content .= '
                              <div class="dropdown-divider"></div>
                              <a href="'.base_url("comment_reply_enhancers/post_list/".$page_table_id).'" class="dropdown-item has-icon"><i class="fas fa-tags"></i> '. $this->lang->line("CommentTag Report").'</a>';
                          endif;
                          
                          if($this->basic->is_exist("add_ons",array("project_id"=>29)))
                          if($this->session->userdata('user_type') == 'Admin' || in_array(204,$this->module_access)) :
                            $middle_column_content .= '
                              <div class="dropdown-divider"></div>
                              <a href="'.base_url('comment_reply_enhancers/page_response_report').'/'.$page_table_id.'" class="dropdown-item has-icon"><i class="fas fa-pager"></i> '. $this->lang->line("Full Page Reply Report").'</a>';

                            if($this->session->userdata('user_type') == 'Admin' || in_array(206,$this->module_access)) :
                              $middle_column_content .= '
                                <a href="'.base_url('comment_reply_enhancers/page_like_share_report').'/'.$page_table_id.'" class="dropdown-item has-icon"><i class="fas fa-thumbs-up"></i> '. $this->lang->line("Auto Like & Share Report").'</a>';
                            endif;
                          endif;
            $middle_column_content .= '        
                        </div>
                    </div>
                  </div>
                </div>
               <script src="'.base_url().'assets/js/system/tooltip_popover.js"></script>';

 
            // ============ right column content section ====================== //

            $existing_data = array();
            if(!empty($autoreply_info))
            {
                foreach($autoreply_info as $value)
                {
                    $existing_data[$value['post_id']]['id'] = $value['id'];
                    $existing_data[$value['post_id']]['pause_play'] = $value['auto_private_reply_status'];
                }
            }

            // comment reply section [getting existing data]
            $existing_data_comment = array();
            if(!empty($autocomment_info))
            {
                foreach($autocomment_info as $value)
                {
                    $existing_data_comment[$value['post_id']]['id'] = $value['id'];
                    $existing_data_comment[$value['post_id']]['pause_play'] = $value['auto_private_reply_status'];
                }
            }
            // end of comment reply section [getting existing data]

            // comment tag machine section [getting existing data]
            $existing_data_tag = array();
            $existing_data_info_tag = $this->basic->get_data('tag_machine_enabled_post_list',array('where'=>array('facebook_rx_fb_user_info_id'=>$this->session->userdata("facebook_rx_fb_user_info"),'page_info_table_id'=>$page_table_id)));

            if(!empty($existing_data_info_tag))
            {
                foreach($existing_data_info_tag as $value)
                {
                    $existing_data_tag[$value['post_id']] = array("id"=>$value['id'],"post_id"=>$value['post_id'],"commenter_count"=>$value['commenter_count'],"comment_count"=>$value["comment_count"]);
                }
            }
            $existing_post_id=array_keys($existing_data_tag);
            // end of comment tag machine section [getting existing data]
            
            $page_id = $page_info[0]['page_id'];
            $access_token = $page_info[0]['page_access_token'];


            try
            {
                $post_list = $this->fb_rx_login->get_postlist_from_fb_page($page_id,$access_token);

                if(isset($post_list['data']) && empty($post_list['data'])){
                    $error_message = '
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
                    $error_message = '
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
                           <div class="col-12 col-md-4 padding-0">
                            <h4><i class="fas fa-rss"></i> '.$this->lang->line("Latest Posts").'</h4>
                           </div>        
                           <div class="col-8 col-md-5 padding-0">
                            <div class="input-group-append dropbottom">
                              <button class="btn btn-outline-primary manual_auto_reply" page_name="'.$page_info[0]['page_name'].'" page_table_id="'.$page_info[0]['id'].'" type="button">'.$this->lang->line("Set Campaign by ID").'</button>
                            </div>
                           </div>
                           <div class="col-4 col-md-3 padding-0">
                              <input type="text" class="form-control float-right" onkeyup="search_in_ul(this,\'post_list_ul\')" placeholder="'.$this->lang->line("Search...").'">
                           </div>


                          </div>
                          <div class="card-body">
                            <div class="makeScroll">
                              <div class="text-center" id="sync_commenter_info_response"></div>
                              <ul class="list-unstyled list-unstyled-border" id="post_list_ul">';

                    foreach($post_list['data'] as $value)
                    {
                        $message = isset($value['message']) ? $value['message'] : '';
                        $permalink_url = isset($value['permalink_url']) ? $value['permalink_url'] : '';
                        $encoded_message=htmlspecialchars($message);
                        // need to check mb is enabled or not
                        if(mb_strlen($message) >= 120)
                            $message = mb_substr($message, 0, 117).'...';
                        else $message = $message;

                        $post_thumb = isset($value['picture']) ? $value['picture'] : base_url('assets/img/avatar/avatar-1.png');
                        $post_created_at =isset($value['created_time']['date'])? $value['created_time']['date']:"";

                        $post_created_at = $post_created_at." UTC";
                        $post_created_at=date("d M y H:i",strtotime($post_created_at));


                        if($message=='') $message='<span class="label label-light border-0"><i>'.$this->lang->line("No description found").'</i></span>';

                        if(array_key_exists($value['id'], $existing_data))
                        {
                            $button = "<a class='pointer dropdown-item has-icon edit_reply_info orange' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-edit'></i> {$this->lang->line("edit auto reply")}</a> ";

                            $button .= "<a class='pointer dropdown-item has-icon view_report blue' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-eye'></i> {$this->lang->line("view auto reply report")}</a>";
                            if($existing_data[$value['id']]['pause_play']=='0' || $existing_data[$value['id']]['pause_play']=='1')
                              $button .= "<a class='pointer dropdown-item has-icon pause_campaign_info dark' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-pause'></i> {$this->lang->line("pause auto reply campaign")}</a>";
                            else
                              $button .= "<a class='pointer dropdown-item has-icon play_campaign_info green' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-play'></i> {$this->lang->line("start auto reply campaign")}</a>";

                            $button .= "<a class='pointer dropdown-item has-icon delete_report red' table_id='".$existing_data[$value['id']]['id']."'><i class='fa fa-trash-alt'></i> {$this->lang->line("delete auto reply report")}</a>"; 
                        }
                        else
                        $button = "<a class='pointer dropdown-item has-icon enable_auto_commnet blue' manual_enable='no' page_table_id='".$page_table_id."' post_id='".$value['id']."' post_permalink='".$permalink_url."'><i class='fa fa-check-circle'></i> {$this->lang->line("enable auto reply")}</a>";

                        if($this->session->userdata('user_type') == 'Admin' || in_array(251,$this->module_access)) :
                          if(array_key_exists($value['id'], $existing_data_comment))
                          {
                              $button .= "<a class='pointer dropdown-item has-icon edit_reply_info_template orange' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-edit'></i> {$this->lang->line("edit auto comment")}</a>";

                              $button .= "<a class='pointer dropdown-item has-icon autocomment_view_report blue' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-eye'></i> {$this->lang->line("view auto comment report")}</a>";
                              if($existing_data_comment[$value['id']]['pause_play']=='0' || $existing_data_comment[$value['id']]['pause_play']=='1')
                                $button .= "<a class='pointer dropdown-item has-icon autocomment_pause_campaign_info dark' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-pause'></i> {$this->lang->line("pause auto comment campaign")}</a>";
                              else
                                $button .= "<a class='pointer dropdown-item has-icon autocomment_play_campaign_info green' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-play'></i> {$this->lang->line("start auto comment campaign")}</a>";

                              $button .= "<a class='pointer dropdown-item has-icon autocomment_delete_report red' table_id='".$existing_data_comment[$value['id']]['id']."'><i class='fa fa-trash-alt'></i> {$this->lang->line("delete auto comment report")}</a>";
                          } 
                          else
                          $button .= "<a class='pointer dropdown-item has-icon enable_auto_commnet_template blue' manual_enable_template='no' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fa fa-check-circle'></i> {$this->lang->line("enable auto comment")}</a>";
                        endif;

                        if($this->basic->is_exist("add_ons",array("project_id"=>29)))
                        if($this->session->userdata('user_type') == 'Admin' || in_array(201,$this->module_access)) :
                          if(in_array($value['id'], $existing_post_id))
                          {
                              $button .= "<a class='pointer dropdown-item has-icon disabled orange'><i class='fa fa-check'></i> ".$this->lang->line("Tag Already Enabled")."</a>";
                          }
                          else  $button .= "<a class='pointer dropdown-item has-icon sync_commenter_info blue' post-description='".$encoded_message."' post-created-at='".$value['created_time']['date']."' id='".$page_table_id.'-'.$value['id']."' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fa fa-check-circle'></i> ".$this->lang->line('Enable & Fetch Commenter')."</a>";
                        endif;

                        $button .= "<a class='pointer dropdown-item has-icon get_all_comments blue' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fa fa-comments'></i> {$this->lang->line("Check latest comments")}</a>";
                        $button .= "<a class='pointer dropdown-item has-icon instant_comment red' page_table_id='".$page_table_id."' post_id='".$value['id']."'><i class='fa fa-comment'></i> {$this->lang->line("Leave a comment now")}</a>";
                       
                        $i++;   


                        $right_column_content .= '
                            <li class="media">
                              <div class="avatar-item">
                                <img alt="image" src="'.$post_thumb.'" width="70" height="70" class="border" data-toggle="tooltip" title="'.date_time_calculator($post_created_at,true).'">
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
                                <div class="media-title"> <a href="'.$permalink_url.'" target="_BLANK" >'.$value['id'].'</a></div>
                                <span class="text-small"><i class="fas fa-clock"></i> '.date_time_calculator($post_created_at,true).' </span> : 
                                <span class="text-small text-muted text-justify">'.$message.'</span>
                              </div>
                            </li>';
                                          
                    }

                    $right_column_content .= '
                              </ul>
                          </div>
                        </div>';

                    if($this->session->userdata("is_mobile")=='0')
                    $right_column_content .= '<script src="'.base_url().'assets/js/system/make_scroll.js"></script>';

                }

            }
            catch(Exception $e) 
            {
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

        $response['middle_column_content'] = $middle_column_content;
        if($right_column_content != '' && $error_msg == '')
        $response['right_column_content'] = $right_column_content;
        else
        $response['right_column_content'] = $error_msg;

        $auto_reply_template = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id,'structured_message'=>'yes')),array('id','ultrapost_campaign_name'));
        $str = '';
        $str = "<option value='0'>".$this->lang->line('Please select a template')."</option>";
        foreach($auto_reply_template as $key => $val)
        {
          $template_id = $val['id'];
          $template_campaign_name = $val['ultrapost_campaign_name'];
          $str .= "<option value='".$template_id."'>".$template_campaign_name."</option>";
        }
        $response['template_list'] = $str;

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','page_id'=>$page_table_id)),'','','',$start=NULL,'');
        $postback_str = '';
        $postback_str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";

        foreach ($postback_data as $key => $value) 
        {
            $postback_str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $response['autoreply_postbacks'] = $postback_str;

        echo json_encode($response);
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

      $select = ['page_access_token','facebook_rx_config_id'];
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
      $this->fb_rx_login->app_initialize($app_config_id);

      try 
      {
        $response=$this->fb_rx_login->auto_comment($message,$post_id,$page_access_token);
        $commentid=isset($response['id'])?$response['id']:"";  
        $id = $commentid;

        $response['status'] = 1;
        $response['message'] = $this->lang->line("Your comment has been created successfully, you can check it from")." "."<b><a target='_BLANK' href='https://www.facebook.com/".$id."'>here</a></b>";
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

    public function get_label_dropdown()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_table_id');// database id

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');
        $result = array();
        $group_name =array();

        $str='<script src="'.base_url().'assets/js/system/instagram/label_ids_select2.js"></script>';
        $str .='<select multiple=""  class="form-control select2" id="label_ids" name="label_ids[]">';
        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_type = $value['group_name'];
            $str.=  "<option value='{$search_key}'>".$search_type."</option>";            

        }
        $str.= '</select>';

        $str2='<script src="'.base_url().'assets/js/system/instagram/label_ids_edit_select2.js"></script>';
        $str2 .='<select multiple=""  class="form-control select2" id="edit_label_ids" name="edit_label_ids[]">';
        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_type = $value['group_name'];
            $str2.=  "<option value='{$search_key}'>".$search_type."</option>";            

        }
        $str2.= '</select>';

        echo json_encode(array('first_dropdown'=>$str,'edit_first_dropdown'=>$str2));
    }

    public function pause_play_campaign()
    {
        $table_id=$this->input->post('table_id');
        $to_do=$this->input->post('to_do');
        $update_data = array('pause_play'=>$to_do);
        $this->basic->update_data('page_response_autoreply',array('id'=>$table_id),$update_data);
        $response['status'] = '1';
        if($to_do == 'play'){
          $response['message'] = $this->lang->line('Full page campaign has been started successfully.');
        }
        else
          $response['message'] = $this->lang->line('Full page campaign has been stopped successfully.');

        echo json_encode($response);
    }

    // =============== autoreply template manager section ======================//
    public function template_manager()
    {   
        $media_type = $this->session->userdata('selected_global_media_type');
        if($media_type == 'ig') {
          redirect('instagram_reply/template_manager');
        }
        $data['body'] = 'comment_automation/template_manager';
        $data['page_title'] = $this->lang->line('Auto Reply Template Manager');
        $data['emotion_list'] = $this->get_emotion_list();

        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','facebook_rx_fb_page_info.facebook_rx_fb_user_info_id'=> $this->session->userdata('facebook_rx_fb_user_info'))),array('facebook_rx_fb_page_info.id','facebook_rx_fb_page_info.page_name','facebook_rx_fb_user_info.name','facebook_rx_fb_user_info.id as fb_user_id'),$join);

        $page_list=array();
        $i=0;
        foreach($page_info as $key => $value) 
        {
           $page_list[$value["fb_user_id"]]["fb_user_name"]=$value['name'];
           $page_list[$value["fb_user_id"]]["data"][$i]["page_name"]=$value['page_name'];
           $page_list[$value["fb_user_id"]]["data"][$i]["table_id"]=$value['id'];
           $i++;
        }
        $data['page_list'] = $page_list;
        $this->_viewcontroller($data);
    }

    public function autoreply_template_manager_data()
    {
        $this->ajax_check();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','ultrapost_campaign_name','page_name');
        $search_columns = array('ultrapost_campaign_name');

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
        	$sql = "(ultrapost_campaign_name like '%".$search_value."%' OR page_name like '%".$search_value."%')";
        	$this->db->where($sql);
        }
        
        $where_simple['ultrapost_auto_reply.user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $table = "ultrapost_auto_reply";
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
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$this->input->post($key);
            }
        }

        $result = $this->basic->delete_data('ultrapost_auto_reply', ['id' => $table_id, 'user_id' => $this->user_id]);

        if ($result) 
            echo "successfull";
        else
            echo "unseccessfull";

    }

    public function autoreply_template_submit()
    {
        $this->ajax_check();
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }

        $page_ids = implode(',', $page_ids);

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_ids,'user_id'=>$this->user_id)),array('	page_name'));
        $page_name = $this->db->escape($page_info[0]['page_name']);
            
        $return = array();
        $facebook_rx_fb_user_info = $this->session->userdata("facebook_rx_fb_user_info");
        
        $nofilter_array['comment_reply'] = trim($nofilter_word_found_text);
        $nofilter_array['private_reply'] = trim($nofilter_word_found_text_private);
        $nofilter_array['image_link'] = trim($nofilter_image_upload_reply);
        $nofilter_array['video_link'] = trim($nofilter_video_upload_reply);
        $no_filter_array = array();
        array_push($no_filter_array, $nofilter_array);
        $nofilter_word_found_text = json_encode($no_filter_array);
        $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);
        // comment hide and delete section
        $is_delete_offensive = $delete_offensive_comment;
        $offensive_words = trim($delete_offensive_comment_keyword);
        $offensive_words = $this->db->escape($offensive_words);
        $private_message_offensive_words = $this->db->escape($private_message_offensive_words);
        // end of comment hide and delete section
        // $page_name = $this->db->escape($page_name);
        $multiple_reply = $this->input->post('multiple_reply');
        $auto_like_comment = $this->input->post('auto_like_comment');
        $comment_reply_enabled = $this->input->post('comment_reply_enabled');
        $hide_comment_after_comment_reply = $this->input->post('hide_comment_after_comment_reply');

        if($multiple_reply == '') $multiple_reply = 'no';
        if($comment_reply_enabled == '') $comment_reply_enabled = 'no';
        if($auto_like_comment == '') $auto_like_comment = 'no';
        if($hide_comment_after_comment_reply == '') $hide_comment_after_comment_reply = 'no';
        
        $auto_campaign_name = $this->db->escape($auto_campaign_name);
        
        if($message_type == 'generic')
        {
            $generic_message_array['comment_reply'] = trim($generic_message);
            $generic_message_array['private_reply'] = trim($generic_message_private);
            $generic_message_array['image_link'] = trim($generic_image_for_comment_reply);
            $generic_message_array['video_link'] = trim($generic_video_comment_reply);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = '';
            $auto_reply_text = json_encode($generic_array);
            $auto_reply_text = $this->db->escape($auto_reply_text); 
            $sql = "INSERT INTO ultrapost_auto_reply (user_id,ultrapost_campaign_name,reply_type,auto_like_comment,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,comment_reply_enabled,auto_reply_text,nofilter_word_found_text,page_ids,structured_message,page_name) VALUES ('$this->user_id',$auto_campaign_name,'$message_type','$auto_like_comment','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$multiple_reply','$comment_reply_enabled',$auto_reply_text,$nofilter_word_found_text,'$page_ids','yes',$page_name)
            ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',ultrapost_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,page_ids='$page_ids',page_name=$page_name,structured_message='yes'";
        }
        else
        {
            $auto_reply_text_array = array();
            for($i=1;$i<=20;$i++)
            {
                $filter_word = 'filter_word_'.$i;
                $filter_word_text = $this->input->post($filter_word);
                $filter_message = 'filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);
                // added 25-04-2017
                $comment_message = 'comment_reply_msg_'.$i;
                $comment_message_text = $this->input->post($comment_message);
                $image_field_name = 'filter_image_upload_reply_'.$i;
                $image_link = $this->input->post($image_field_name);
                $video_field_name = 'filter_video_upload_reply_'.$i;
                $video_link = $this->input->post($video_field_name);
                
                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                {
                    // $auto_reply_text_array[$filter_word_text] = $filter_message_text;
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['image_link'] = trim($image_link);
                    $data['video_link'] = trim($video_link);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = '';
            $auto_reply_text = json_encode($auto_reply_text_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);
            $sql = "INSERT INTO ultrapost_auto_reply (user_id,ultrapost_campaign_name,reply_type,auto_like_comment,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,comment_reply_enabled,auto_reply_text,nofilter_word_found_text,page_ids,structured_message,page_name,trigger_matching_type) VALUES ('$this->user_id',$auto_campaign_name,'$message_type',auto_like_comment='$auto_like_comment','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$multiple_reply','$comment_reply_enabled',$auto_reply_text,$nofilter_word_found_text,'$page_ids','yes',$page_name,'$trigger_matching_type')
            ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',ultrapost_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,page_ids='$page_ids',page_name=$page_name,structured_message='yes',trigger_matching_type='$trigger_matching_type'";
        } 
        
        if($this->db->query($sql))
        {
            //insert data to useges log table
            $this->_insert_usage_log($module_id=204,$request=1);
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function templatemanager_reply_info()
    {
        $this->ajax_check();
        $respnse = array();
        $table_id = $this->input->post('table_id');
        $info = $this->basic->get_data('ultrapost_auto_reply',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));

        $page_table_id = $info[0]['page_ids'];

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','page_id'=>$page_table_id)),'','','',$start=NULL,'');
        $str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";

        foreach ($postback_data as $key => $value) 
        {
            $str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $respnse['postbacks'] = $str;

        $join = array('facebook_rx_fb_user_info'=>'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left');
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1','facebook_rx_fb_page_info.facebook_rx_fb_user_info_id'=> $this->session->userdata('facebook_rx_fb_user_info'))),array('facebook_rx_fb_page_info.id','facebook_rx_fb_page_info.page_name','facebook_rx_fb_user_info.name','facebook_rx_fb_user_info.id as fb_user_id'),$join);

        $page_list=array();
        $i=0;
        foreach($page_info as $key => $value) 
        {
           $page_list[$value["fb_user_id"]]["fb_user_name"]=$value['name'];
           $page_list[$value["fb_user_id"]]["data"][$i]["page_name"]=$value['page_name'];
           $page_list[$value["fb_user_id"]]["data"][$i]["table_id"]=$value['id'];
           $i++;
        }

        $pages = '';
        foreach ($page_list as $key => $value) 
        {
          $pages .= '<optgroup label="'.addslashes($value['fb_user_name']).'">';
          foreach ($value['data'] as $key2 => $value2) 
          {
            if($value2['table_id'] == $page_table_id) $selected = 'selected';
            else $selected = '';
            $pages .= "<option value='".$value2['table_id']."' {$selected}>".$value2['page_name']."</option>";
          }
          $pages .= '</optgroup>';
        }

        $respnse['page_list'] = $pages;

        
        if($info[0]['reply_type'] == 'generic'){
          $reply_content = json_decode($info[0]['auto_reply_text']);
          if(!is_array($reply_content))
          {
              $reply_content[0]['comment_reply'] = "";
              $reply_content[0]['private_reply'] = $info[0]['auto_reply_text'];
              $reply_content[0]['image_link'] = "";
              $reply_content[0]['video_link'] = "";
          }
        }
        else
          $reply_content = json_decode($info[0]['auto_reply_text']);
        $nofilter_word_text = json_decode($info[0]['nofilter_word_found_text']);
        if(!is_array($nofilter_word_text))
        {
          $nofilter_word_text[0]['comment_reply'] = '';
          $nofilter_word_text[0]['image_link'] = '';
          $nofilter_word_text[0]['video_link'] = '';
          $nofilter_word_text[0]['private_reply'] = $info[0]['nofilter_word_found_text'];
        }
        $respnse['reply_type'] = $info[0]['reply_type'];
        $respnse['comment_reply_enabled'] = $info[0]['comment_reply_enabled'];
        $respnse['multiple_reply'] = $info[0]['multiple_reply'];
        $respnse['auto_like_comment'] = $info[0]['auto_like_comment'];
        $respnse['auto_reply_text'] = $reply_content;
        $respnse['table_id'] = $info[0]['id'];
        $respnse['edit_auto_campaign_name'] = $info[0]['ultrapost_campaign_name'];
        $respnse['edit_nofilter_word_found_text'] = $nofilter_word_text;
        $respnse['is_delete_offensive'] = $info[0]['is_delete_offensive'];
        $respnse['offensive_words'] = $info[0]['offensive_words'];
        $respnse['private_message_offensive_words'] = $info[0]['private_message_offensive_words'];
        $respnse['hide_comment_after_comment_reply'] = $info[0]['hide_comment_after_comment_reply'];
        $respnse['trigger_matching_type'] = $info[0]['trigger_matching_type'];
        // comment hide and delete section
        echo json_encode($respnse);
    }

    public function update_templatemanager_info()
    {
        $this->ajax_check();
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }

        $page_ids = implode(',', $edit_page_ids);
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_ids,'user_id'=>$this->user_id)),array(' page_name'));
        $page_name = $page_info[0]['page_name'];

        $edit_multiple_reply = $this->input->post('edit_multiple_reply',true);
        $edit_auto_like_comment = $this->input->post('edit_auto_like_comment',true);
        $edit_comment_reply_enabled = $this->input->post('edit_comment_reply_enabled',true);
        $edit_hide_comment_after_comment_reply = $this->input->post('edit_hide_comment_after_comment_reply',true);

        if($edit_multiple_reply == '') $edit_multiple_reply = 'no';
        if($edit_comment_reply_enabled == '') $edit_comment_reply_enabled = 'no';
        if($edit_auto_like_comment == '') $edit_auto_like_comment = 'no';
        if($edit_hide_comment_after_comment_reply == '') $edit_hide_comment_after_comment_reply = 'no';
        
        $return = array();
        if($edit_message_type == 'generic')
        {
            $generic_message_array['comment_reply'] = trim($edit_generic_message);
            $generic_message_array['private_reply'] = trim($edit_generic_message_private);
            $generic_message_array['image_link'] = trim($edit_generic_image_for_comment_reply);
            $generic_message_array['video_link'] = trim($edit_generic_video_comment_reply);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = json_encode($generic_array);
        }
        else
        {
            $auto_reply_text_array = array();
            for($i=1;$i<=20;$i++)
            {
                $filter_word = 'edit_filter_word_'.$i;
                $filter_word_text = $this->input->post($filter_word);
                $filter_message = 'edit_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);
                // added 25-04-2017
                $comment_message = 'edit_comment_reply_msg_'.$i;
                $comment_message_text = $this->input->post($comment_message);
                $image_field_name = 'edit_filter_image_upload_reply_'.$i;
                $image_link = $this->input->post($image_field_name);
                $video_field_name = 'edit_filter_video_upload_reply_'.$i;
                $video_link = $this->input->post($video_field_name);
                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                {
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['image_link'] = trim($image_link);
                    $data['video_link'] = trim($video_link);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = json_encode($auto_reply_text_array);
        }
        $no_filter_array['comment_reply'] = trim($edit_nofilter_word_found_text);
        $no_filter_array['private_reply'] = trim($edit_nofilter_word_found_text_private);
        $no_filter_array['image_link'] = trim($edit_nofilter_image_upload_reply);
        $no_filter_array['video_link'] = trim($edit_nofilter_video_upload_reply);
        $nofilter_array = array();
        array_push($nofilter_array, $no_filter_array);
        $data = array(
            'auto_reply_text' => $auto_reply_text,
            'reply_type' => $edit_message_type,
            'ultrapost_campaign_name' => $edit_auto_campaign_name,
            'nofilter_word_found_text' => json_encode($nofilter_array),
            'comment_reply_enabled' => $edit_comment_reply_enabled,
            'multiple_reply' => $edit_multiple_reply,
            // comment hide and delete section
            'is_delete_offensive' => $edit_delete_offensive_comment,
            'offensive_words' => trim($edit_delete_offensive_comment_keyword),
            'private_message_offensive_words' => trim($edit_private_message_offensive_words),
            'hide_comment_after_comment_reply' => $edit_hide_comment_after_comment_reply,
            // comment hide and delete section
            'auto_like_comment' => $edit_auto_like_comment,
            'page_ids' => $page_ids,
            'page_name' => $page_name,
            'structured_message' => 'yes',
            'trigger_matching_type' => $edit_trigger_matching_type
            );
        $where = array(
            'user_id' => $this->user_id,
            'id' => $table_id
            );
        if($this->basic->update_data('ultrapost_auto_reply',$where,$data))
        {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }



    // =============== end of autoreply template manager section ======================//


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

    public function get_emotion_list()
    {
        $dirTree=$this->scanAll(FCPATH."assets/images/emotions-fb");
        $map = array
        (
            "angel" => "o:)",
            "colonthree" => ":3",
            "confused" => "o.O",
            "cry" => ":'(",
            "devil" => "3:)",
            "frown" => ":(",
            "gasp" => ":O",
            "glasses" => "8)",
            "grin" => ":D",
            "grumpy" => ">:(",
            "heart" => "<3",
            "kiki" => "^_^",
            "kiss" => ":*",
            "pacman" => ":v",
            "smile" => ":)",
            "squint" => "-_-",
            "sunglasses" => "8|",
            "tongue" => ":p",
            "upset" => ">:O",
            "wink" => ";)"
            );
        $str = "";
        foreach ($dirTree as $value) 
        {
            $temp = array();
            $value['file'] = str_replace('\\','/', $value['file']);
            $temp =explode('/', $value["file"]);
            $filename = array_pop($temp);

            if(!strpos($filename,'.gif')) continue;

            $title = str_replace('.gif',"",$filename);
            $eval = $map[$title];

            $src= base_url('assets/images/emotions-fb/'.$filename);
            $str.= '&nbsp;&nbsp;<img eval="'.$eval.'" title="'.$title.'"  class="cursor_pointer emotion inline" src="'.$src.'"/>&nbsp;&nbsp;';
        }
        return $str;
    }

    public function scanAll($myDir)
    {
        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $dirTree[$i]['size'] = $file_size;
            $dirTree[$i]['time'] =date("Y-m-d H:i:s",$file_modification_time);

            $i++;

        }

        return $dirTree;
    }


    public function checking_post_id()
    {        
        $post_id = trim($this->input->post('post_id'));
        $page_table_id = trim($this->input->post('page_table_id'));
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_table_id)));
        $page_name = $page_info[0]['page_name'];
        $access_token = $page_info[0]['page_access_token'];
        //adding page id before post id, for error handling
        if(strpos($post_id, '_')!==FALSE) $post_id=$post_id;
        else $post_id = $page_info[0]['page_id']."_".$post_id;

        try
        {
            $post_info = $this->fb_rx_login->get_post_info_by_id($post_id,$access_token);

            if(isset($post_info['error']))
            {
                $response['error'] = 'yes';
                $response['error_msg'] = $post_info['error']['message'];
            }
            else
                $response['error'] = 'no';

            if(empty($post_info))
            {
                $response['error'] = 'yes';
                $response['error_msg'] = $this->lang->line("please provide correct post id.");
            }

        }
        catch(Exception $e)
        {
            $response['error'] = 'yes';
            $response['error_msg'] = $e->getMessage();
        }

        if($response['error'] == 'no')
        {
            $post_created_at = isset($post_info[$post_id]['created_time']) ? $post_info[$post_id]['created_time'] : "";
            $message = isset($post_info[$post_id]['message']) ? $post_info[$post_id]['message'] : "";
            $post_permalink = isset($post_info[$post_id]['permalink_url']) ? $post_info[$post_id]['permalink_url'] : "";
            $encoded_message = htmlspecialchars($message);


            $existing_data = array();
            $existing_data_info = $this->basic->get_data('facebook_ex_autoreply',array('where'=>array('user_id'=>$this->user_id,'page_info_table_id'=>$page_table_id)));

            if(!empty($existing_data_info))
            {
                foreach($existing_data_info as $value)
                {
                    $existing_data[$value['post_id']] = $value['id'];
                }
            }

            // comment reply section [getting existing data]
            $existing_data_comment = array();
            $existing_data_info_comment = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('user_id'=>$this->user_id,'page_info_table_id'=>$page_table_id,"social_media_type"=>"Facebook")));

            if(!empty($existing_data_info_comment))
            {
                foreach($existing_data_info_comment as $value)
                {
                    $existing_data_comment[$value['post_id']] = $value['id'];
                }
            }

            // end of comment reply section [getting existing data]

            // comment tag machine section [getting existing data]
            $existing_data_tag = array();
            $existing_data_info_tag = $this->basic->get_data('tag_machine_enabled_post_list',array('where'=>array('facebook_rx_fb_user_info_id'=>$this->session->userdata("facebook_rx_fb_user_info"),'page_info_table_id'=>$page_table_id)));

            if(!empty($existing_data_info_tag))
            {
                foreach($existing_data_info_tag as $value)
                {
                    $existing_data_tag[$value['post_id']] = array("id"=>$value['id'],"post_id"=>$value['post_id'],"commenter_count"=>$value['commenter_count'],"comment_count"=>$value["comment_count"]);
                }
            }
            $existing_post_id=array_keys($existing_data_tag);
            // end of comment tag machine section [getting existing data]
            
            $button = '';
            if(array_key_exists($post_id, $existing_data))
            {
                $button = "<a class='btn btn-outline-warning' href='".base_url("comment_automation/all_auto_reply_report/{$post_id}")."'><i class='fas fa-edit'></i> {$this->lang->line("edit auto reply from list")}</a>&nbsp;&nbsp;";
            }
            else
            $button = '<button type="button" class="btn btn-outline-primary enable_auto_commnet" page_table_id="'.$page_table_id.'" post_id="'.$post_id.'" post_permalink="'.$post_permalink.'" id="manual_auto_reply"><i class="fas fa-check-circle"></i> '.$this->lang->line("enable auto reply").'</button>&nbsp;&nbsp;';

            if(array_key_exists($post_id, $existing_data_comment))
            {
                $button .= "<a class='btn btn-outline-warning' href='".base_url("comment_automation/all_auto_comment_report/0/{$post_id}")."'><i class='fas fa-edit'></i> {$this->lang->line("edit auto comment from list")}</a>&nbsp;&nbsp;";
            } 
            else
            $button .= '<button type="button" class="btn btn-outline-primary enable_auto_commnet_template" page_table_id="'.$page_table_id.'" post_id="'.$post_id.'" id="manual_auto_reply_template"><i class="fas fa-check-circle"></i> '.$this->lang->line("Enable Auto Comment").'</button>&nbsp;&nbsp;';

            if($this->basic->is_exist("add_ons",array("project_id"=>29)))
            if($this->session->userdata('user_type') == 'Admin' || in_array(201,$this->module_access)) :
              if(in_array($post_id, $existing_post_id))
              {
                  $button .= "<a class='btn btn-outline-warning' href='".base_url("comment_reply_enhancers/post_list/0/{$post_id}")."'><i class='fas fa-binoculars'></i> ".$this->lang->line("see tag report from list")."</a>&nbsp;&nbsp;";
              }
              else  $button .= '<button type="button" class="btn btn-outline-primary sync_commenter_info" post-description="'.$encoded_message.'" post-created-at="'.$post_created_at.'" id="'.$page_table_id.'-'.$post_id.'" page_table_id="'.$page_table_id.'" post_id="'.$post_id.'"><i class="fas fa-check-square"></i> '.$this->lang->line("Enable & Fetch Commenter").'</button>&nbsp;&nbsp;';
            endif;
            
            $response['buttons'] = $button;

        }


        echo json_encode($response);
    }


    public function get_tableid_by_postid()
    {
        $page_table_id = $this->input->post('page_table_id');
        $post_id = $this->input->post('post_id');
        $page_table_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_table_id)),array('page_id'));
        $page_id = $page_table_info[0]['page_id'];
        if(strpos($post_id, '_')!==FALSE) $post_id=$post_id;
        else $post_id = $page_id."_".$post_id;


        $where['where'] = array(
            'user_id' => $this->user_id,
            'page_info_table_id' => $page_table_id,
            'post_id' => $post_id
            );


        $table_info = $this->basic->get_data('facebook_ex_autoreply',$where,'','',1);
        if(empty($table_info))
            $respnse['error'] = 'yes';
        else
        {
            $respnse['error'] = 'no';
            $respnse['table_id'] = $table_info[0]['id'];

        }
        echo json_encode($respnse);
    }


    public function ajax_autoreply_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        $label_ids=array_filter($label_ids);
        $new_label_ids=implode(',', $label_ids);
        $broadcaster_labels = $this->db->escape($new_label_ids);


        //************************************************//
        $status=$this->_check_usage($module_id=80,$request=1);
        if($status=="2") 
        {
            $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        else if($status=="3") 
        {
            $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        //************************************************//

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$auto_reply_page_id)));
        $page_name = $page_info[0]['page_name'];

        $auto_reply_post_id = trim($auto_reply_post_id);
        $auto_reply_post_id_array = explode('_', $auto_reply_post_id);
        if(count($auto_reply_post_id_array) == 1)
        {
            $auto_reply_post_id = $page_info[0]['page_id']."_".$auto_reply_post_id;
        }

        $post_created_at = "";
        $post_description = "";
        $post_thumb = "";

        try
        {
            $post_info = $this->fb_rx_login->get_post_info_by_id($auto_reply_post_id,$page_info[0]['page_access_token']);

            if(isset($post_info['error']))
            {
                $response['error'] = 'yes';
                $response['error_msg'] = $post_info['error']['message'];
            }
            else
            {
                $post_created_at = isset($post_info[$auto_reply_post_id]['created_time']) ? $post_info[$auto_reply_post_id]['created_time'] : "";
                if(isset($post_info[$auto_reply_post_id]['message']))
                    $post_description = isset($post_info[$auto_reply_post_id]['message']) ? $post_info[$auto_reply_post_id]['message'] : "";
                else if(isset($post_info[$auto_reply_post_id]['name']))
                    $post_description = isset($post_info[$auto_reply_post_id]['name']) ? $post_info[$auto_reply_post_id]['name'] : "";
                else
                    $post_description = isset($post_info[$auto_reply_post_id]['description']) ? $post_info[$auto_reply_post_id]['description'] : "";
                
                $post_thumb = isset($post_info[$auto_reply_post_id]['picture']) ? $post_info[$auto_reply_post_id]['picture'] : "";
            }

        }
        catch(Exception $e)
        {
            $post_created_at = "";
            $post_description = "";
            $post_thumb = "";
        }


        $post_description = $this->db->escape($post_description);
        
        $return = array();
        $facebook_rx_fb_user_info = $this->session->userdata("facebook_rx_fb_user_info");
        $date_time = date("Y-m-d H:i:s");

        $auto_template_selection = $this->input->post('auto_template_selection',true);

        // if select to use saved template
        if($auto_template_selection == 'yes' && $btn_type =='only_submit') 
        {
            $ultrapost_auto_reply_table_data = $this->basic->get_data('ultrapost_auto_reply',array('where'=>array('id'=>$auto_reply_template)));

            $auto_campaign_name               = $this->db->escape($ultrapost_auto_reply_table_data[0]['ultrapost_campaign_name']);
            $reply_type                       = $ultrapost_auto_reply_table_data[0]['reply_type'];
            $hide_comment_after_comment_reply = $ultrapost_auto_reply_table_data[0]['hide_comment_after_comment_reply'];
            $is_delete_offensive              = $ultrapost_auto_reply_table_data[0]['is_delete_offensive'];
            $offensive_words                  = $this->db->escape($ultrapost_auto_reply_table_data[0]['offensive_words']);
            $private_message_offensive_words  = $this->db->escape($ultrapost_auto_reply_table_data[0]['private_message_offensive_words']);
            $auto_like_comment                = $ultrapost_auto_reply_table_data[0]['auto_like_comment'];
            $multiple_reply                   = $ultrapost_auto_reply_table_data[0]['multiple_reply'];
            $comment_reply_enabled            = $ultrapost_auto_reply_table_data[0]['comment_reply_enabled'];
            $auto_reply_text                  = $this->db->escape($ultrapost_auto_reply_table_data[0]['auto_reply_text']);
            $nofilter_word_found_text         = $this->db->escape($ultrapost_auto_reply_table_data[0]['nofilter_word_found_text']);
            $trigger_matching_type            = $ultrapost_auto_reply_table_data[0]['trigger_matching_type'];

            $page_name = $this->db->escape($page_name);

            $sql = "INSERT INTO facebook_ex_autoreply (facebook_rx_fb_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_permalink,post_created_at,post_description,post_thumb,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,last_updated_at,nofilter_word_found_text,template_manager_table_id,broadcaster_labels,structured_message,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id',$auto_campaign_name,'$auto_reply_page_id',$page_name,'$auto_reply_post_id','$auto_reply_post_permalink','$post_created_at',$post_description,'$post_thumb','$reply_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$auto_like_comment','$multiple_reply','$comment_reply_enabled',$auto_reply_text,'$date_time',$nofilter_word_found_text,'$auto_reply_template',$broadcaster_labels,'yes','$trigger_matching_type')
                ON DUPLICATE KEY UPDATE post_thumb='$post_thumb',auto_reply_text=$auto_reply_text,reply_type='$reply_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',auto_reply_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,broadcaster_labels=$broadcaster_labels,structured_message='yes',trigger_matching_type='$trigger_matching_type'";

        } 
        else if($auto_template_selection =='') // if select to create new template
        {
            $nofilter_array['comment_reply'] = trim($nofilter_word_found_text);
            $nofilter_array['private_reply'] = trim($nofilter_word_found_text_private);

            $nofilter_array['image_link'] = trim($nofilter_image_upload_reply);
            $nofilter_array['video_link'] = trim($nofilter_video_upload_reply);

            $no_filter_array = array();
            array_push($no_filter_array, $nofilter_array);
            $nofilter_word_found_text = json_encode($no_filter_array);
            $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);

            // comment hide and delete section
            $is_delete_offensive = $delete_offensive_comment;
            $offensive_words = trim($delete_offensive_comment_keyword);
            $offensive_words = $this->db->escape($offensive_words);
            $private_message_offensive_words = $this->db->escape($private_message_offensive_words);
            // end of comment hide and delete section

            $page_name = $this->db->escape($page_name);
            
            $auto_campaign_name = $this->db->escape($auto_campaign_name);

            $multiple_reply                   = $this->input->post('multiple_reply',true);
            $auto_like_comment                = $this->input->post('auto_like_comment',true);
            $comment_reply_enabled            = $this->input->post('comment_reply_enabled',true);
            $hide_comment_after_comment_reply = $this->input->post('hide_comment_after_comment_reply',true);

            if($multiple_reply == '') $multiple_reply = 'no';
            if($comment_reply_enabled == '') $comment_reply_enabled = 'no';
            if($auto_like_comment == '') $auto_like_comment = 'no';
            if($hide_comment_after_comment_reply == '') $hide_comment_after_comment_reply = 'no';
            

            if($message_type == 'generic')
            {
                $generic_message_array['comment_reply'] = trim($generic_message);
                $generic_message_array['private_reply'] = trim($generic_message_private);

                $generic_message_array['image_link'] = trim($generic_image_for_comment_reply);
                $generic_message_array['video_link'] = trim($generic_video_comment_reply);

                $generic_array = array();
                array_push($generic_array, $generic_message_array);
                $auto_reply_text = '';
                $auto_reply_text = json_encode($generic_array);
                $auto_reply_text = $this->db->escape($auto_reply_text);

                if($btn_type == "submit_create_button") 
                {
                    // insert into ultrapost_autoreply_teble
                    $crateTemplate = "INSERT INTO ultrapost_auto_reply (ultrapost_campaign_name,user_id,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,nofilter_word_found_text,structured_message,page_ids,page_name) VALUES ($auto_campaign_name,'$this->user_id','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$auto_like_comment','$multiple_reply','$comment_reply_enabled',$auto_reply_text,$nofilter_word_found_text,'yes','$auto_reply_page_id',$page_name)
                    ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',ultrapost_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,page_ids='$auto_reply_page_id',page_name=$page_name,structured_message='yes'";

                    // getting template id
                    if($this->db->query($crateTemplate))
                        $insert_id = $this->db->insert_id();


                    // insert into facebook_autoreply_table with template_manager_id
                    $sql = "INSERT INTO facebook_ex_autoreply (facebook_rx_fb_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_permalink,post_created_at,post_description,post_thumb,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,last_updated_at,nofilter_word_found_text,template_manager_table_id,broadcaster_labels,structured_message,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id',$auto_campaign_name,'$auto_reply_page_id',$page_name,'$auto_reply_post_id','$auto_reply_post_permalink','$post_created_at',$post_description,'$post_thumb','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$auto_like_comment','$multiple_reply','$comment_reply_enabled',$auto_reply_text,'$date_time',$nofilter_word_found_text,'$insert_id',$broadcaster_labels,'yes','$trigger_matching_type')
                    ON DUPLICATE KEY UPDATE post_thumb='$post_thumb',auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',auto_reply_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,broadcaster_labels=$broadcaster_labels,structured_message='yes',trigger_matching_type='$trigger_matching_type'";


                }
                else if($btn_type == "only_submit") 
                {
                    $sql = "INSERT INTO facebook_ex_autoreply (facebook_rx_fb_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_permalink,post_created_at,post_description,post_thumb,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,last_updated_at,nofilter_word_found_text,broadcaster_labels,structured_message,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id',$auto_campaign_name,'$auto_reply_page_id',$page_name,'$auto_reply_post_id','$auto_reply_post_permalink','$post_created_at',$post_description,'$post_thumb','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$auto_like_comment','$multiple_reply','$comment_reply_enabled',$auto_reply_text,'$date_time',$nofilter_word_found_text,$broadcaster_labels,'yes','$trigger_matching_type')
                    ON DUPLICATE KEY UPDATE post_thumb='$post_thumb',auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',auto_reply_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,broadcaster_labels=$broadcaster_labels,structured_message='yes',trigger_matching_type='$trigger_matching_type'";
                }

            }
            else
            {
                $auto_reply_text_array = array();
                for($i=1;$i<=20;$i++)
                {
                    $filter_word = 'filter_word_'.$i;
                    $filter_word_text = $this->input->post($filter_word);
                    $filter_message = 'filter_message_'.$i;
                    $filter_message_text = $this->input->post($filter_message);

                    // added 25-04-2017
                    $comment_message = 'comment_reply_msg_'.$i;
                    $comment_message_text = $this->input->post($comment_message);

                    $image_field_name = 'filter_image_upload_reply_'.$i;
                    $image_link = $this->input->post($image_field_name);

                    $video_field_name = 'filter_video_upload_reply_'.$i;
                    $video_link = $this->input->post($video_field_name);

                    if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                    {
                        // $auto_reply_text_array[$filter_word_text] = $filter_message_text;
                        $data['filter_word'] = trim($filter_word_text);
                        $data['reply_text'] = trim($filter_message_text);
                        $data['comment_reply_text'] = trim($comment_message_text);

                        $data['image_link'] = trim($image_link);
                        $data['video_link'] = trim($video_link);

                        array_push($auto_reply_text_array, $data);
                    }
                }
                $auto_reply_text = '';
                $auto_reply_text = json_encode($auto_reply_text_array);
                $auto_reply_text = $this->db->escape($auto_reply_text);



                if($btn_type == 'submit_create_button') // if clicked on create & submit button
                {
                    // insert into ultrapost_autoreply_teble
                    $crateTemplate = "INSERT INTO ultrapost_auto_reply (ultrapost_campaign_name,user_id,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,nofilter_word_found_text,structured_message,page_ids,page_name,trigger_matching_type) 
                    VALUES ($auto_campaign_name,'$this->user_id','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$auto_like_comment','$multiple_reply','$comment_reply_enabled',$auto_reply_text,$nofilter_word_found_text,'yes','$auto_reply_page_id',$page_name,'$trigger_matching_type')
                    ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',ultrapost_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,page_ids='$auto_reply_page_id',page_name=$page_name,structured_message='yes',trigger_matching_type='$trigger_matching_type'";

                    // getting template id
                    if($this->db->query($crateTemplate))
                        $insert_id = $this->db->insert_id();

                    // insert into facebook_ex_autoreply table with template id
                    $sql = "INSERT INTO facebook_ex_autoreply (facebook_rx_fb_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_permalink,post_created_at,post_description,post_thumb,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,last_updated_at,nofilter_word_found_text,template_manager_table_id,broadcaster_labels,structured_message,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id',$auto_campaign_name,'$auto_reply_page_id',$page_name,'$auto_reply_post_id','$auto_reply_post_permalink','$post_created_at',$post_description,'$post_thumb','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$auto_like_comment','$multiple_reply','$comment_reply_enabled',$auto_reply_text,'$date_time',$nofilter_word_found_text,$insert_id,$broadcaster_labels,'yes','$trigger_matching_type')
                    ON DUPLICATE KEY UPDATE post_thumb='$post_thumb',auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',auto_reply_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,broadcaster_labels=$broadcaster_labels,structured_message='yes',trigger_matching_type='$trigger_matching_type'";


                } else if($btn_type == 'only_submit') // if clicked on only submit button
                {

                    $sql = "INSERT INTO facebook_ex_autoreply (facebook_rx_fb_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_permalink,post_created_at,post_description,post_thumb,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,last_updated_at,nofilter_word_found_text,broadcaster_labels,structured_message,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id',$auto_campaign_name,'$auto_reply_page_id',$page_name,'$auto_reply_post_id','$auto_reply_post_permalink','$post_created_at',$post_description,'$post_thumb','$message_type','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$auto_like_comment','$multiple_reply','$comment_reply_enabled',$auto_reply_text,'$date_time',$nofilter_word_found_text,$broadcaster_labels,'yes','$trigger_matching_type')
                    ON DUPLICATE KEY UPDATE post_thumb='$post_thumb',auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',auto_reply_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,broadcaster_labels=$broadcaster_labels,structured_message='yes',trigger_matching_type='$trigger_matching_type'";
            }
            }

        }
        

        if($this->db->query($sql))
        {
            //insert data to useges log table
            $this->_insert_usage_log($module_id=80,$request=1);
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }


    public function ajax_autoreply_delete()
    {
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->get_data('facebook_ex_autoreply',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));
        if($post_info[0]['auto_private_reply_count'] == 0)
        {
            //******************************//
            // delete data to useges log table
            $this->_delete_usage_log($module_id=80,$request=1);   
            //******************************//
        }

        $this->basic->delete_data('facebook_ex_autoreply',array('id'=>$table_id));
        $this->basic->delete_data('facebook_ex_autoreply_report',array('autoreply_table_id'=>$table_id,'user_id'=>$this->user_id));
        echo 'success';
    }


    public function ajax_edit_reply_info()
    {
        $respnse = array();
        $table_id = $this->input->post('table_id');
        $info = $this->basic->get_data('facebook_ex_autoreply',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));

        if($info[0]['post_permalink'] != '')
        {
          $facebook_rx_fb_user_info_id = $info[0]['facebook_rx_fb_user_info_id'];
          $facebook_rx_config_info = $this->basic->get_data('facebook_rx_fb_user_info',array('where'=>array('id'=>$facebook_rx_fb_user_info_id,'user_id'=>$this->user_id)),array('facebook_rx_config_id'));
          $facebook_rx_config_id = $facebook_rx_config_info[0]['facebook_rx_config_id'];
          $this->load->library('fb_rx_login');
          $this->fb_rx_login->app_initialize($facebook_rx_config_id);
          $access_token_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$info[0]['page_info_table_id'],'user_id'=>$this->user_id)),array('page_access_token'));
          $post_info = $this->fb_rx_login->get_post_info_by_id($info[0]['post_id'],$access_token_info[0]['page_access_token']);
          $permalink = $post_info[$info[0]['post_id']]['permalink_url'];
        }
        else
          $permalink = $info[0]['post_permalink'];


        $page_table_id = $info[0]['page_info_table_id'];

        // $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message')),'','','',$start=NULL,'');

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','page_id'=>$page_table_id)),'','','',$start=NULL,'');
        
        $str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";

        foreach ($postback_data as $key => $value) 
        {
            $str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $respnse['postbacks'] = $str;

        if($info[0]['reply_type'] == 'generic'){
            $reply_content = json_decode($info[0]['auto_reply_text']);
            if(!is_array($reply_content))
            {
                $reply_content[0]['comment_reply'] = "";
                $reply_content[0]['private_reply'] = $info[0]['auto_reply_text'];
                $reply_content[0]['image_link'] = "";
                $reply_content[0]['video_link'] = "";
            }
        }
        else
            $reply_content = json_decode($info[0]['auto_reply_text']);

        $nofilter_word_text = json_decode($info[0]['nofilter_word_found_text']);
        if(!is_array($nofilter_word_text))
        {
            $nofilter_word_text[0]['comment_reply'] = '';
            $nofilter_word_text[0]['image_link'] = '';
            $nofilter_word_text[0]['video_link'] = '';
            $nofilter_word_text[0]['private_reply'] = $info[0]['nofilter_word_found_text'];
        }

        $respnse['reply_type'] = $info[0]['reply_type'];
        $respnse['trigger_matching_type'] = $info[0]['trigger_matching_type'];
        $respnse['comment_reply_enabled'] = $info[0]['comment_reply_enabled'];
        $respnse['multiple_reply'] = $info[0]['multiple_reply'];
        $respnse['auto_like_comment'] = $info[0]['auto_like_comment'];
        $respnse['auto_reply_text'] = $reply_content;
        $respnse['edit_auto_reply_page_id'] = $info[0]['page_info_table_id'];
        $respnse['edit_auto_reply_post_id'] = $info[0]['post_id'];
        $respnse['edit_auto_reply_post_permalink'] = $permalink;
        $respnse['edit_auto_campaign_name'] = $info[0]['auto_reply_campaign_name'];
        $respnse['edit_nofilter_word_found_text'] = $nofilter_word_text;
        // comment hide and delete section
        $respnse['is_delete_offensive'] = $info[0]['is_delete_offensive'];
        $respnse['offensive_words'] = $info[0]['offensive_words'];
        $respnse['private_message_offensive_words'] = $info[0]['private_message_offensive_words'];
        $respnse['hide_comment_after_comment_reply'] = $info[0]['hide_comment_after_comment_reply'];
        // comment hide and delete section
        $respnse['edit_label_ids'] = $info[0]['broadcaster_labels'];


        $broadcaster_labels=$info[0]['broadcaster_labels'];
        $broadcaster_labels=explode(',', $broadcaster_labels);
        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$info[0]['page_info_table_id'],"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');

        $dropdown=array();
        $str='<script src="'.base_url().'assets/js/system/instagram/label_ids_edit_select2.js"></script>';
        $str .='<select multiple=""  class="form-control select2" id="edit_label_ids" name="edit_label_ids[]">';
        // $str .= '<option value="">'.$this->lang->line('Select Labels').'</option>';
        foreach ($info_type as  $value)
        {          
          $search_key = $value['id'];
          $search_type = $value['group_name'];
          $selected='';
          if(in_array($search_key, $broadcaster_labels)) $selected='selected="selected"';
          $str.=  "<option value='{$search_key}' {$selected}>".$search_type."</option>";
        }
        $str.= '</select>';
        $respnse['label_ids_div'] = $str;

        echo json_encode($respnse);
    }


    public function ajax_update_autoreply_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }

        $edit_multiple_reply = $this->input->post('edit_multiple_reply',true);
        $edit_auto_like_comment = $this->input->post('edit_auto_like_comment',true);
        $edit_comment_reply_enabled = $this->input->post('edit_comment_reply_enabled',true);
        $edit_hide_comment_after_comment_reply = $this->input->post('edit_hide_comment_after_comment_reply',true);

        if($edit_multiple_reply == '') $edit_multiple_reply = 'no';
        if($edit_comment_reply_enabled == '') $edit_comment_reply_enabled = 'no';
        if($edit_auto_like_comment == '') $edit_auto_like_comment = 'no';
        if($edit_hide_comment_after_comment_reply == '') $edit_hide_comment_after_comment_reply = 'no';

        $return = array();

        if($edit_message_type == 'generic')
        {
            // $auto_reply_text = $edit_generic_message;

            $generic_message_array['comment_reply'] = trim($edit_generic_message);
            $generic_message_array['private_reply'] = trim($edit_generic_message_private);
            $generic_message_array['image_link'] = trim($edit_generic_image_for_comment_reply);
            $generic_message_array['video_link'] = trim($edit_generic_video_comment_reply);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = json_encode($generic_array);
            // $auto_reply_text = $this->db->escape($generic_message_text);
        }
        else
        {
            $auto_reply_text_array = array();
            for($i=1;$i<=20;$i++)
            {
                $filter_word = 'edit_filter_word_'.$i;
                $filter_word_text = $this->input->post($filter_word);
                $filter_message = 'edit_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);

                // added 25-04-2017
                $comment_message = 'edit_comment_reply_msg_'.$i;
                $comment_message_text = $this->input->post($comment_message);

                $image_field_name = 'edit_filter_image_upload_reply_'.$i;
                $image_link = $this->input->post($image_field_name);


                $video_field_name = 'edit_filter_video_upload_reply_'.$i;
                $video_link = $this->input->post($video_field_name);

                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                {
                    // $auto_reply_text_array[$filter_word_text] = $this->db->escape($filter_message_text);
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);

                    $data['image_link'] = trim($image_link);
                    $data['video_link'] = trim($video_link);

                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = json_encode($auto_reply_text_array);
        }

        $no_filter_array['comment_reply'] = trim($edit_nofilter_word_found_text);
        $no_filter_array['private_reply'] = trim($edit_nofilter_word_found_text_private);

        $no_filter_array['image_link'] = trim($edit_nofilter_image_upload_reply);
        $no_filter_array['video_link'] = trim($edit_nofilter_video_upload_reply);

        $nofilter_array = array();
        array_push($nofilter_array, $no_filter_array);

        if(!isset($edit_label_ids) || !is_array($edit_label_ids)) $edit_label_ids=array();
        $edit_label_ids=array_filter($edit_label_ids);
        $new_label_ids=implode(',', $edit_label_ids);
        $broadcaster_labels = $new_label_ids;

        $data = array(
            'auto_reply_text' => $auto_reply_text,
            'reply_type' => $edit_message_type,
            'auto_reply_campaign_name' => $edit_auto_campaign_name,
            'nofilter_word_found_text' => json_encode($nofilter_array),
            'comment_reply_enabled' => $edit_comment_reply_enabled,
            'multiple_reply' => $edit_multiple_reply,
            'post_permalink' => $edit_auto_reply_post_permalink,
            // comment hide and delete section
            'is_delete_offensive' => $edit_delete_offensive_comment,
            'offensive_words' => trim($edit_delete_offensive_comment_keyword),
            'private_message_offensive_words' => trim($edit_private_message_offensive_words),
            'hide_comment_after_comment_reply' => $edit_hide_comment_after_comment_reply,
            // comment hide and delete section
            'auto_like_comment' => $edit_auto_like_comment,
            'broadcaster_labels' => $broadcaster_labels,
            'structured_message' => 'yes',
            'trigger_matching_type' => $edit_trigger_matching_type
            );

        $where = array(
            'user_id' => $this->user_id,
            'page_info_table_id' => $edit_auto_reply_page_id,
            'post_id' => $edit_auto_reply_post_id
            );

        if($this->basic->update_data('facebook_ex_autoreply',$where,$data))
        {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }


    public function auto_reply_report($page_info_table_id=0)
    {
        $this->is_broadcaster_exist=$this->broadcaster_exist();
        if($page_info_table_id==0) exit();
        $page_info = $this->basic->get_data('facebook_ex_autoreply',array('where'=>array('page_info_table_id'=>$page_info_table_id,'user_id'=>$this->user_id)),'','',1);

        $data['page_name'] = isset($page_info[0]['page_name']) ? $page_info[0]['page_name']:'';

        $data['body'] = 'comment_automation/auto_reply_report';
        $data['page_title'] = $this->lang->line('Auto reply - Report');
        $data['page_table_id'] = $page_info_table_id;
        $data['emotion_list'] = $this->get_emotion_list();
        $this->_viewcontroller($data);
    }

    public function auto_reply_report_data($table_id=0)
    {
      $this->ajax_check();

      $search_value = $_POST['search']['value'];
      $display_columns = array("#",'id','post_thumb','auto_reply_campaign_name','post_id','actions','last_reply_time','error_message');
      $search_columns = array('auto_reply_campaign_name','post_id');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 6;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'last_reply_time';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;

      $where_simple=array();   

      $where_simple['page_info_table_id'] = $table_id;
      $where_simple['user_id'] = $this->user_id;

      $sql = '';
      if ($search_value != '') 
        $sql = "(auto_reply_campaign_name LIKE  '%".$search_value."%' OR post_id LIKE '%".$search_value."%')";
      if($sql != '')
        $this->db->where($sql);

      $where  = array('where'=>$where_simple);

      $table ="facebook_ex_autoreply";
      $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');

      if($sql != '')
        $this->db->where($sql);
      $total_rows_array=$this->basic->count_row($table,$where,$count="id",$join='',$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      $info_new = array();
      $info_row_number = count($info);
      $i = 0;
      foreach($info as $value)
      {
          $action_count = 4;
          $onlypostid = explode('_', $value['post_id']);
          $onlypostid2 = isset($onlypostid[1])?$onlypostid[1]:$value['post_id'];
          $permalink = $value['post_permalink'];
          if($permalink == '')
            $permalink = "https://facebook.com/".$value['post_id'];

          $info_new[$i]['id'] = $value['id'];
          $info_new[$i]['auto_reply_campaign_name'] = $value['auto_reply_campaign_name'];
          $info_new[$i]['post_id'] = "<a target='_BLANK' href='".$permalink."' data-toggle='tooltip' title='".$this->lang->line('Visit Post')."'>".$onlypostid2."</a>";
          $info_new[$i]['auto_private_reply_count'] = $value['auto_private_reply_count'];

          $page_url = "<a href='#' class='btn btn-circle btn-outline-primary view_report' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Campaign Report")."'><i class='fas fa-eye'></i></a>
          <a href='#' class='btn btn-circle btn-outline-warning edit_reply_info' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."'><i class='fas fa-edit'></i></a>";

          $deleteUrl ="<a href='#' class='btn btn-circle btn-outline-danger delete_report red' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."'><i class='fas fa-trash-alt'></i></a>";

          $button = '';
          
          if($value['auto_private_reply_status'] == '0' || $value['auto_private_reply_status'] == '1')
            $button = "<a href='#' class='btn btn-circle btn-outline-dark pause_campaign_info' table_id='".$value['id']."' title='".$this->lang->line("pause campaign")."'><i class='fas fa-pause'></i></a>";
          if($value['auto_private_reply_status'] == '2')
            $button = "<a href='#' class='btn btn-circle btn-outline-success play_campaign_info' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Start campaign")."'><i class='fas fa-play'></i></a>";
          $force = '';
          // $force = "<a href='#' id='".$value['id']."' class='btn btn-circle btn-outline-info force' title='".$this->lang->line("force reprocessing")."'><i class='fas fa-sync'></i></a>";

          $info_new[$i]['post_created_at'] = $value['post_created_at'];
          $last_reply_time = $value['last_reply_time'];

          if($last_reply_time == '0000-00-00 00:00:00') 
            $last_reply_time='<span class="text-muted"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line("Not Replied").'</span>';
          else 
            $last_reply_time = date("M j, y H:i",strtotime($last_reply_time));

          $info_new[$i]['last_reply_time']  = $last_reply_time;
          $info_new[$i]['error_message']    = $value['error_message'];
          $info_new[$i]['post_description'] = $value['post_description'];

          $post_thumb = ($info[$i]['post_thumb']!="") ? $info[$i]['post_thumb'] : base_url('assets/img/avatar/avatar-1.png');
          $info_new[$i]['post_thumb'] = "<img class='rounded-circle instagram_height_width_40px_bordered' src='".$post_thumb."'>";

          // Action section started from here
          $action_width = ($action_count*47)+20;
          $info_new[$i]['actions'] = '<div class="dropdown d-inline dropright">
              <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
          $info_new[$i]['actions'] .= $page_url;
          $info_new[$i]['actions'] .= $button;
          // $info_new[$i]['actions'] .= $force;
          $info_new[$i]['actions'] .= $deleteUrl;
          $info_new[$i]['actions'] .="</div></div><script src='".base_url()."assets/js/system/tooltip_popover.js'></script>";

          $i++;
      }


      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info_new, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);
    }


    public function ajax_get_reply_info()
    {
        $this->ajax_check();

        $table_id = $this->input->post('table_id');
        $searching = $this->input->post('searching',true);

        $display_columns = array("#","comment_text","commenter_name","comment_time","reply_time","comment_reply_id","reply_id","reply_status_comment","reply_status","hide_delete_status");

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'comment_time';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where = array();
        $where['where'] = array('autoreply_table_id'=> $table_id,'user_id'=>$this->user_id);

        $sql = '';
        if ($searching != '') 
          $sql = "(comment_text LIKE '%".$searching."%' OR commenter_name LIKE '%".$searching."%' OR comment_reply_text LIKE '%".$searching."%' OR reply_text LIKE '%".$searching."%')";
        if($sql != '')
          $this->db->where($sql);

        $table="facebook_ex_autoreply_report";
        $info = $this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');


        if($sql != '')
          $this->db->where($sql);
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $i = 0;
        $info_new = array();
        foreach($info as $value)
        {
          $info_new[$i]['comment_text'] = "<a href='https://facebook.com/".$value['comment_id']."' target='_BLANK'>".$value['comment_text']."</a>";
          $info_new[$i]['commenter_name'] = $value['commenter_name'];
          $info_new[$i]['comment_time'] = date('jS F y, H:i', strtotime($value['comment_time']));
          $info_new[$i]['reply_time'] = date('jS F y, H:i', strtotime($value['reply_time']));

          $search_char = ["'",'"'];

          if($value['comment_reply_text'] != '')
          {
            $comment_text = str_replace($search_char, "`", $value['comment_reply_text']);
            $short_comment = mb_substr($value['comment_reply_text'],0,20);
            $short_comment = str_replace($search_char, "`", $short_comment);
            $info_new[$i]['comment_reply_id'] = "<a data-toggle='tooltip' data-placement='top' title='".$comment_text."' href='https://facebook.com/".$value['comment_reply_id']."' target='_BLANK'>".$short_comment."...</a>";            
          }
          else
            $info_new[$i]['comment_reply_id'] = '';

          if($value['reply_text'] != '')
          {
            $substr = substr($value['reply_text'],0,2);
            if($substr == '["')
            {
              $reply_text = json_decode($value['reply_text'],true);
              $postback_link = base_url('messenger_bot/edit_template').'/'.$reply_text[0];
              $info_new[$i]['reply_id'] = '<div data-toggle="tooltip" data-placement="top" title="You can view/edit private reply message template by clicking here."><a href="'.$postback_link.'" target="_BLANK">View/Edit</a></div><script src="'.base_url().'assets/js/system/tooltip_popover.js"></script>';
              
            }
            else
            {
              $full_message = str_replace($search_char, "`", $value['reply_text']);
              $short_msg = mb_substr($value['reply_text'],0,20);
              $short_msg = str_replace($search_char, "`", $short_msg);
              $info_new[$i]['reply_id'] = '<div data-toggle="tooltip" data-placement="top" title="'.$full_message.'">'.$short_msg.'...</div><script src="'.base_url().'assets/js/system/tooltip_popover.js"></script>';
            }
          }
          else
            $info_new[$i]['reply_id'] = '';

          if($value['reply_status_comment'] == 'success')
            $info_new[$i]['reply_status_comment'] = "<span class='text-success'><i class='fas fa-check-circle'></i> Success</span>";
          else
            $info_new[$i]['reply_status_comment'] = $value['reply_status_comment'];

          $substr = substr($value['reply_status'],0,2);
          if($substr == '["')
          {
            $reply_status = json_decode($value['reply_status'],true);
            $info_new[$i]['reply_status'] = '';
            foreach($reply_status as $valuex)
            {
              if($valuex == 'success')
                $info_new[$i]['reply_status'] .= "<span class='text-success'><i class='fas fa-check-circle'></i> Success</span><br/>";
              else
                $info_new[$i]['reply_status'] .= $valuex."<br/>";
            }
          }
          else
          {
            if($value['reply_status'] == 'success')
              $info_new[$i]['reply_status'] = "<span class='text-success'><i class='fas fa-check-circle'></i> Success</span>";
            else
              $info_new[$i]['reply_status'] = $value['reply_status'];
          }

          if($value['is_deleted'] == '1')
            $info_new[$i]['hide_delete_status'] = "<span class='text-danger'><i class='fas fa-trash'></i> Deleted</span>";
          else if ($value['is_hidden'] == '1')
            $info_new[$i]['hide_delete_status'] = "<span class='text-warning'><i class='fas fa-eye-slash'></i> Hidden</span>";
          else
            $info_new[$i]['hide_delete_status'] = "";
          $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info_new, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function get_count_info()
    {
      $this->ajax_check();
      $table_id = $this->input->post('table_id',true);
      $info = $this->basic->get_data('facebook_ex_autoreply_report',array('where'=>array('autoreply_table_id'=>$table_id)));
      $comment_reply_sent = 0;
      $private_reply_sent = 0;
      $hidden_comment = 0;
      $deleted_comment = 0;
      foreach($info as $value)
      {
        if($value['reply_status_comment'] == 'success')
          $comment_reply_sent++;
        if($value['reply_status'] == 'success')
          $private_reply_sent++;
        if($value['is_deleted'] == '1')
          $deleted_comment++;
        if($value['is_hidden'] == '1')
          $hidden_comment++;
      }
      
      $str = "<div class='row text-center'><div class='col-6 col-sm-3'><i class='fas fa-reply-all blue'></i> ".$this->lang->line('Private reply sent')." : ".$private_reply_sent."</div>";
      $str .= "<div class='col-6 col-sm-3'><i class='fas fa-comment-dots green'></i> ".$this->lang->line('Comment reply sent')." : ".$comment_reply_sent."</div>";
      if(ultraresponse_addon_module_exist())
      {        
        $str .= "<div class='col-6 col-sm-3'><i class='fas fa-trash red'></i> ".$this->lang->line('Comment deleted')." : ".$deleted_comment."</div>";
        $str .= "<div class='col-6 col-sm-3'><i class='fas fa-eye-slash orange'></i> ".$this->lang->line('Comment hidden')." : ".$hidden_comment."</div>";
      }


      $str .= "</div>";

      echo json_encode(array('status'=>'1','str'=>$str));
    }


    public function download_get_reply_info($table_id)
    {

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        
        // $table_id = $this->input->post('table_id');
        $reply_info = $this->basic->get_data('facebook_ex_autoreply_report',array('where'=>array('autoreply_table_id'=>$table_id)));

        if(!empty($reply_info))
        {
            $filename="{$this->user_id}_commentator_info.csv";
            // make output csv file unicode compatible
            $f = fopen('php://memory', 'w'); 
            fputs( $f, "\xEF\xBB\xBF" );

            /**Write header in csv file***/
            $write_data[]="Name";
            $write_data[]="Client Id";
            $write_data[]="Comment Id";
            $write_data[]="Comment Text";

            fputcsv($f,$write_data, ",");

            foreach($reply_info as $value)
            {
                
                $write_data=array();
                $write_data[]=$value['commenter_name'];
                $write_data[]=$value['commenter_id'];
                $write_data[]=$value['comment_id'];
                $write_data[]=$value['comment_text'];

                fputcsv($f,$write_data, ",");
            }

            // reset the file pointer to the start of the file
            fseek($f, 0);
            // tell the browser it's going to be a csv file
            header('Content-Type: application/csv');
            // tell the browser we want to save it instead of displaying it
            header('Content-Disposition: attachment; filename="'.$filename.'";');
            // make php send the generated csv lines to the browser
            fpassthru($f);  
        }
        else
        {
            $str = "<div class='alert alert-danger'>{$this->lang->line("no data to show")}</div>";
        }
    }


    public function all_auto_reply_report($post_id=0)
    {
    	$this->is_broadcaster_exist=$this->broadcaster_exist();
        $page_info = array();
        $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'bot_enabled'=>'1')));
        if(!empty($page_list))
        {
            $data['page_info'] = $page_list;
        }

        $data['body'] = 'comment_automation/all_auto_reply_report';
        $data['page_title'] = $this->lang->line('All Auto Reply Report');
        $data['emotion_list'] = $this->get_emotion_list();
        $data['post_id'] = $post_id;
        $this->_viewcontroller($data);
    }


    public function all_auto_reply_report_data()
    {
      $this->ajax_check();

      $pagename = trim($this->input->post("page_id",true));
      $campaign_name = trim($this->input->post("campaign_name",true));

      $display_columns = array("#",'id','post_thumb','auto_reply_campaign_name','page_name','post_id','actions','last_reply_time','error_message');
      $search_columns = array('auto_reply_campaign_name','post_id');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 7;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'last_reply_time';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;

      $where=array();
      $where_simple = array();

      $where_simple['facebook_ex_autoreply.user_id'] = $this->user_id;
      $where_simple['facebook_ex_autoreply.facebook_rx_fb_user_info_id'] = $this->session->userdata('facebook_rx_fb_user_info'); 

      if($pagename !="") 
        $where_simple['facebook_ex_autoreply.page_info_table_id LIKE'] = "%".$pagename."%";

      $sql = '';
      if($campaign_name != '') 
        $sql = "(facebook_ex_autoreply.auto_reply_campaign_name LIKE  '%".$campaign_name."%' OR facebook_ex_autoreply.post_id LIKE '%".$campaign_name."%')";
      if($sql != '')
        $this->db->where($sql);

      $where['where'] = $where_simple;

      $join = array('facebook_rx_fb_page_info'=>'facebook_ex_autoreply.page_info_table_id=facebook_rx_fb_page_info.id,left');
      $select = array('facebook_ex_autoreply.*','facebook_rx_fb_page_info.page_id AS pageid');
      $table ="facebook_ex_autoreply";
      $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');

      if($sql != '')
        $this->db->where($sql);
      $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
      $total_result=$total_rows_array[0]['total_rows'];

      $i = 0;
      $info_new = array();
      foreach($info as $value)
      {
          $action_count = 4;
          $onlypostid = explode('_', $value['post_id']);
          
          $onlypostid2 = isset($onlypostid[1])?$onlypostid[1]:$value['post_id'];

          $permalink = $value['post_permalink'];
          if($permalink == '')
            $permalink = "https://facebook.com/".$value['post_id'];

          $info_new[$i]['id'] = $value['id'];
          $info_new[$i]['auto_reply_campaign_name'] = $value['auto_reply_campaign_name'];
          $info_new[$i]['page_name'] = "<a class='ash' target='_BLANK' data-toggle='tooltip' title='".$this->lang->line("Visit Page")."' href='https://facebook.com/".$value['pageid']."'>".$value['page_name']."</a>";
          $info_new[$i]['post_id'] = "<a target='_BLANK' data-toggle='tooltip' title='".$this->lang->line("Visit Post")."' href='".$permalink."'>".$onlypostid2."</a>";
          $info_new[$i]['auto_private_reply_count'] = $value['auto_private_reply_count'];
          
          $page_url = "<a href='#' class='btn btn-circle btn-outline-primary view_report' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Campaign Report")."'><i class='fas fa-eye'></i></a>
          <a href='#' class='btn btn-circle btn-outline-warning edit_reply_info' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("edit campaign")."'><i class='fas fa-edit'></i></a>";

          $deleteUrl = "<a href='#' class='btn btn-circle btn-outline-danger delete_report red' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("delete campaign")."'><i class='fas fa-trash-alt'></i></a>";
          
          
          $button = '';
          if($value['auto_private_reply_status'] == '0' || $value['auto_private_reply_status'] == '1')
            $button = "<a href='#' class='btn btn-circle btn-outline-dark pause_campaign_info' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Pause campaign")."'><i class='fas fa-pause'></i></a>";
          if($value['auto_private_reply_status'] == '2')
            $button = "<a href='#' class='btn btn-circle btn-outline-success play_campaign_info' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Start campaign")."'><i class='fas fa-play'></i></a>";

          $force = '';
          // $force = "<a href='#' id='".$value['id']."' class='btn btn-circle btn-outline-info force' data-toggle='tooltip' title='".$this->lang->line("force reprocessing")."'><i class='fas fa-sync'></i></a>";
      
          $info_new[$i]['post_created_at'] = $value['post_created_at'];
          $last_reply_time = $value['last_reply_time'];

          if($last_reply_time=='0000-00-00 00:00:00') $last_reply_time ='<span class="text-muted min_width_100px"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line("Not Replied").'</span>';
          else $last_reply_time= '<div class="min_width_100px">'.date("M j, y H:i",strtotime($last_reply_time)).'</div>';

          $info_new[$i]['last_reply_time'] = $last_reply_time;
          $info_new[$i]['error_message'] = $value['error_message'];
          $info_new[$i]['post_description'] = $value['post_description'];

          $post_thumb = ($info[$i]['post_thumb']!="") ? $info[$i]['post_thumb'] : base_url('assets/img/avatar/avatar-1.png');
          $info_new[$i]['post_thumb'] = "<img class='rounded-circle instagram_height_width_40px_bordered' src='".$post_thumb."'>";

          // Action section started from here
          $action_width = ($action_count*47)+20;
          $info_new[$i]['actions'] = '<div class="dropdown d-inline dropright">
          <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i></button>
          <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
          $info_new[$i]['actions'] .= $page_url;
          $info_new[$i]['actions'] .= $button;
          // $info_new[$i]['actions'] .= $force;
          $info_new[$i]['actions'] .= $deleteUrl;
          $info_new[$i]['actions'] .= "</div></div>
          <script src='".base_url()."assets/js/system/tooltip_popover.js'></script>";

          $i++;
      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info_new, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);
    }


    public function ajax_autoreply_pause()
    {
      $table_id = $this->input->post('table_id');
      $this->basic->update_data('facebook_ex_autoreply',array('id'=>$table_id),array('auto_private_reply_status'=>'2'));
      echo 'success';
    }

    public function ajax_renew_campaign()
    {
        $table_id = $this->input->post('table_id');
        $this->basic->update_data('facebook_ex_autoreply',array('id'=>$table_id),array('last_updated_at'=>date("Y-m-d H:i:s")));
        echo 'success';
    }

    public function ajax_autoreply_play()
    {
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('facebook_ex_autoreply',array('id'=>$table_id),array('auto_private_reply_status'=>'0'));
        echo 'success';
    }


    public function force_reprocess_campaign()
    {
        if(!$_POST) exit();
        $id=$this->input->post("id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('auto_private_reply_status'=>'0');
        $this->basic->update_data('facebook_ex_autoreply',$where,$data);
        if($this->db->affected_rows() != 0)
            echo "1";
        else
            echo "0";
    }


    public function upload_live_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $output_dir = FCPATH."upload/video";

        $folder_path = FCPATH."upload/video";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="video_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;


            $allow=".mov,.mpeg4,.mp4,.avi,.wmv,.mpegps,.flv,.3gpp,.webm";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
              $custom_error['jquery-upload-file-error']=$this->lang->line("File type not allowed.");
              echo json_encode($custom_error);
              exit();
            }


            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("go_live_video_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("go_live_video_filename", $filename); 
            echo json_encode($filename);
        }
    }



    public function delete_uploaded_live_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/video/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }



    public function upload_image_only()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $folder_path = FCPATH."upload/image";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        $output_dir = FCPATH."upload/image/".$this->user_id;
        if (!file_exists($output_dir)) {
            mkdir($output_dir, 0777, true);
        }

        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="image_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;


            $allow=".jpg,.jpeg,.png,.gif";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
              $custom_error['jquery-upload-file-error']=$this->lang->line("File type not allowed.");
              echo json_encode($custom_error);
              exit();
            }



            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }


    public function delete_uploaded_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/image/".$this->user_id."/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }


    public function comment_section_report()
    {
        $media_type = $this->session->userdata('selected_global_media_type');
        if($media_type == 'ig') {
          redirect('instagram_reply/reports');
        }

        $data['body'] = 'comment_automation/report_block';
        $data['page_title'] = $this->lang->line('Report Section');
        $this->_viewcontroller($data);
    }



    // ============================================= Auto Comment Section Started =========================================


    public function ajax_autocomment_reply_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }
        $post_created_at = "";
        $post_description = "";
        $post_thumb = "";
        $data['auto_comment_template_id'] = $auto_comment_template_id;
        $data['user_id']=$this->user_id;

        $autocomment_template_type = $this->input->post('autocomment_template_type',true);
        if($autocomment_template_type == 'instagram') $data['social_media_type'] = 'Instagram';
        $autocomment_permalink_url = $this->input->post('autocomment_permalink_url',true);
      
        if(isset($_POST['schedule_type']))
        {
           $schedule_type = $_POST['schedule_type'];

        }
    
        if($schedule_type == "onetime"){
             

             $data['schedule_time'] = $schedule_time;
             $data['time_zone'] = $time_zone;
             $data['schedule_type'] = $schedule_type;

          
        }
        if($schedule_type == "periodic")
        {
            $data['periodic_time'] = $periodic_time;
            $data['schedule_type'] = $schedule_type;
            $data['time_zone'] = $periodic_time_zone;
            $data['campaign_start_time'] = $campaign_start_time;
            $data['campaign_end_time'] = $campaign_end_time;
            $data['comment_start_time'] = $comment_start_time;
            $data['comment_end_time'] = $comment_end_time;

            if(isset($_POST['auto_comment_type']))
            {
                $auto_comment_type = $_POST['auto_comment_type'];

            }
            if($auto_comment_type == "random"){

                $data['auto_comment_type'] = $auto_comment_type;
            }
            if($auto_comment_type == "serially"){

                $data['auto_comment_type'] =$auto_comment_type;
            }
         }


        $data['campaign_name'] = $auto_campaign_name_template;

        
      


        //************************************************//

        $status=$this->_check_usage($module_id=251,$request=1);
        if($status=="2") 
        {
            $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        else if($status=="3") 
        {
            $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        //************************************************//

    
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$auto_reply_page_id_template)));

        $page_name = $page_info[0]['page_name'];

        $auto_reply_post_id = trim($auto_reply_post_id_template);
        $auto_reply_post_id_array = explode('_', $auto_reply_post_id_template);

        if($autocomment_template_type != 'instagram')
        {
          if(count($auto_reply_post_id_array) == 1)
          {
              $auto_reply_post_id = $page_info[0]['page_id']."_".$auto_reply_post_id;
          }
        }

        // $manual_reply_description = "";

        if($manual_enable_template == 'yes')
        {
            try
            {
                $post_info = $this->fb_rx_login->get_post_info_by_id($auto_reply_post_id,$page_info[0]['page_access_token']);

                if(isset($post_info['error']))
                {
                    $response['error'] = 'yes';
                    $response['error_msg'] = $post_info['error']['message'];
                }
                else
                {
                    $post_created_at = isset($post_info[$auto_reply_post_id]['created_time']) ? $post_info[$auto_reply_post_id]['created_time'] : "";
                    if(isset($post_info[$auto_reply_post_id]['message']))
                        $post_description = isset($post_info[$auto_reply_post_id]['message']) ? $post_info[$auto_reply_post_id]['message'] : "";
                    else if(isset($post_info[$auto_reply_post_id]['name']))
                        $post_description = isset($post_info[$auto_reply_post_id]['name']) ? $post_info[$auto_reply_post_id]['name'] : "";
                    else
                        $post_description = isset($post_info[$auto_reply_post_id]['description']) ? $post_info[$auto_reply_post_id]['description'] : "";
                    
                    $post_thumb = isset($post_info[$auto_reply_post_id]['picture']) ? $post_info[$auto_reply_post_id]['picture'] : "";
                }

            }
            catch(Exception $e)
            {
                $post_created_at = "";
                $post_description = "";
                $post_thumb = "";
            }
        }
        else
        {
            try{

                $post_list = $this->fb_rx_login->get_postlist_from_fb_page($page_info[0]['page_id'],$page_info[0]['page_access_token']);
                if(isset($post_list['data']) && !empty($post_list['data']))
                {
                    foreach($post_list['data'] as $value)
                    {
                        if($value['id'] == $auto_reply_post_id)
                        {
                            $post_created_at = isset($value['created_time']['date']) ? $value['created_time']['date'] : '';
                            // $post_description = isset($value['message']) ? $value['message'] : '';

                            if(isset($value['message']))
                                $post_description = isset($value['message']) ? $value['message'] : "";
                            else if(isset($value['name']))
                                $post_description = isset($value['name']) ? $value['name'] : "";
                            else
                                $post_description = isset($value['description']) ? $value['description'] : "";

                            $post_thumb = isset($value['picture']) ? $value['picture'] : "";

                            // $manual_reply_description = "found";
                            break;
                        }
                    }
                }
            }
            catch(Exception $e)
            {            
                $post_created_at = "";
                $post_description = "";
                $post_thumb = "";
            }
        }
        $post_description = $this->db->escape($post_description);
        $data['post_description'] = $post_description;
        $data['post_id'] = $auto_reply_post_id;
        $data['page_info_table_id'] =$auto_reply_page_id_template;
        $data['post_created_at']=$post_created_at;
        $data['page_name'] = $page_name;
        $data['insta_media_url'] = $autocomment_permalink_url;
        

        if($this->basic->insert_data('auto_comment_reply_info',$data))
        {
            //insert data to useges log table
            $this->_insert_usage_log($module_id=251,$request=1);
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function ajax_autocomment_delete()
    {
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('id'=>$table_id)));
        if($post_info[0]['auto_comment_count'] == 0)
        {
            //******************************//
            // delete data to useges log table
            $this->_delete_usage_log($module_id=251,$request=1);   
            //******************************//
        }

        $this->basic->delete_data('auto_comment_reply_info',array('id'=>$table_id));
        echo 'success';
    }


    public function ajax_edit_autocomment_info()
    {
        $respnse = array();
        $table_id = $this->input->post('table_id',true);
        $info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('id'=>$table_id)));



        if($info[0]['schedule_type'] == 'onetime'){
              
              $response['edit_schedule_time_o'] = $info[0]['schedule_time'];
              $response['edit_time_zone_o'] = $info[0]['time_zone'];
              $response['edit_schedule_type'] =$info[0]['schedule_type'];
        }
        
        if($info[0]['schedule_type'] == 'periodic')
        {
            $response['edit_campaign_start_time'] = $info[0]['campaign_start_time'];
            $response['edit_campaign_end_time'] = $info[0]['campaign_end_time'];
            $response['edit_periodic_time'] = $info[0]['periodic_time'];
            $response['edit_schedule_type'] =$info[0]['schedule_type'];
            $response['edit_periodic_time_zone'] = $info[0]['time_zone'];
            $response['edit_comment_start_time'] =$info[0]['comment_start_time'];
            $response['edit_comment_end_time'] =$info[0]['comment_end_time'];
            if($info[0]['auto_comment_type']=='random'){
                $response['edit_auto_comment_type'] = $info[0]['auto_comment_type'];
            }        
            if($info[0]['auto_comment_type']=='serially'){
                $response['edit_auto_comment_type'] = $info[0]['auto_comment_type'];
            }
        }
        
        $response['edit_auto_comment_template_id'] = $info[0]['auto_comment_template_id'];
        $response['edit_campaign_name'] = $info[0]['campaign_name'];
        $response['edit_auto_reply_page_id'] = $info[0]['page_info_table_id'];
        $response['edit_auto_reply_post_id'] = $info[0]['post_id'];
    
        echo json_encode($response);
       
    }


    public function ajax_update_autocomment_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }

        if(isset($_POST['edit_schedule_type']))
        {
           $schedule_type = $_POST['edit_schedule_type'];

        }
        
        if($schedule_type == "onetime"){
             

             $edit_schedule_time_o = $edit_schedule_time_o;
             $edit_time_zone_o = $edit_time_zone_o;
           
             $schedule_type = $schedule_type;
             $edit_periodic_time='';
             $edit_auto_comment_type='';
             $edit_campaign_start_time='';
             $edit_campaign_end_time='';
             $edit_comment_start_time='';
             $edit_comment_end_time='';
            
        }
        if($schedule_type == "periodic")
        {
            $edit_periodic_time = $edit_periodic_time;
            $schedule_type = $schedule_type;
            $edit_campaign_start_time = $edit_campaign_start_time;
            $edit_campaign_end_time = $edit_campaign_end_time;
            $edit_schedule_time_o = '';
            $edit_time_zone_o = $edit_periodic_time_zone;
            //$edit_periodic_time =$periodic_time_zone;
            $edit_comment_start_time=$edit_comment_start_time;
            $edit_comment_end_time=$edit_comment_end_time;
            if(isset($_POST['edit_auto_comment_type']))
            {
               $edit_auto_comment_type = $_POST['edit_auto_comment_type'];

            }

            if($edit_auto_comment_type == "random"){
               $edit_auto_comment_type =$edit_auto_comment_type;
            }
            if($edit_auto_comment_type == "serially"){
               $edit_auto_comment_type =$edit_auto_comment_type;
            }
         }


        $data = array(
    
            'campaign_name' => $edit_campaign_name_template,
            'auto_comment_template_id' => $edit_auto_comment_template_id,
            'schedule_type'=> $schedule_type,
            'schedule_time' =>$edit_schedule_time_o,
            'time_zone' => $edit_time_zone_o,
            'periodic_time' => $edit_periodic_time,
            'campaign_start_time' => $edit_campaign_start_time,
            'campaign_end_time' => $edit_campaign_end_time,
            'auto_comment_type' =>$edit_auto_comment_type,
            'comment_start_time'=>$edit_comment_start_time,
            'comment_end_time'=>$edit_comment_end_time

           );

        $where = array(
            'user_id' => $this->user_id,
            'page_info_table_id' => $edit_auto_reply_page_id_template,
            'post_id' => $edit_auto_reply_post_id_template
            );

        if($this->basic->update_data('auto_comment_reply_info',$where,$data))
        {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function ajax_get_autocomment_reply_info()
    {
      $this->ajax_check();

      $table_id = $this->input->post('table_id');
      $searching = trim($this->input->post("searching",true));

      $display_columns = array("#","id","comment_text","comment_time","schedule_type","reply_status");

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
      $order_by=$sort." ".$order;

      $where['where'] = array('id'=> $table_id);

      $table="auto_comment_reply_info";
      $info = $this->basic->get_data($table,$where,$select='');

      if(isset($info[0]['auto_reply_done_info']) && $info[0]['auto_reply_done_info'] != '')
      {
        $campaign_details = $info[0];

        $report_info = json_decode($campaign_details['auto_reply_done_info'],true);
        $reply_info = $report_info;

        $reply_info = array_filter($reply_info, function($single_reply) use ($searching) 
        {
          if ($searching != '') {

            if (stripos($single_reply['comment_text'], $searching) !== false || stripos($single_reply['comment_time'], $searching) !== false || stripos($single_reply['reply_status'], $searching) !== false) 
            {
              return TRUE; 
            }
            else
            {
              return FALSE;  
            }
          }
          else
          {
            return TRUE;
          }

        });

        
        usort($reply_info, function($first, $second) use ($sort, $order)
        {
          if ($first[$sort] == $second[$sort]) {
            return 0;
          }
          else if ($first[$sort] > $second[$sort]) {
            if ($order == 'desc') return 1;
            else return -1;
          }
          else if ($first[$sort] < $second[$sort]) {
            if ($order == 'desc') return -1;
            else return 1;
          }
                          
        });


        $final_info = array();
        $i = 0;
        $upper_limit = $start + $limit;

        foreach ($reply_info as $key => $value) { 

          if ($i >= $start && $i < ($upper_limit))
            array_push($final_info, $value);

            $i++;
        }

        $result = array();
        foreach ($final_info as $value) {
            
          $temp = array();
          array_push($temp, ++$start);

          foreach ($value as $key => $column) 
          {
            if($key == 'id')
            {
              if($info[0]["social_media_type"]=='Facebook')
              $column = '<a class="blue" target="_BLANK" href="http://facebook.com/'.$column.'">'.$column.'</a>';
              else $column = '<a class="blue" target="_BLANK" href="'.$info[0]["insta_media_url"].'">'.$column.'</a>';
            }

            if($key == 'reply_status' && $column == 'success')
              $column = "<span class='text-success'><i class='fas fa-check-circle'></i> ".$column."</span>";

            if ($key == 'comment_time')
              $column = date('jS F y H:i', strtotime($column));
            
            if (in_array($key, $display_columns)) 
              array_push($temp, $column);
          }

          array_push($result, $temp);
            
        }

      }
      else {

          $total_result = 0;
          $reply_info = array();
          $result = array();
      }
      
      $total_result = count($reply_info);
      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = $result;


      echo json_encode($data);
    }    

    public function ajax_get_autocomment_reply_info1()
    {
        $table_id = $this->input->post('table_id');
        $reply_info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('id'=>$table_id)));

        if(isset($reply_info[0]['auto_reply_done_info']) && $reply_info[0]['auto_reply_done_info'] != '')
        {
            $str = '<script src="'.base_url().'assets/js/system/instagram/campaign_report_datatable.js"></script>
                 <div class="table-responsive">
                 <table id="campaign_report" class="table table-bordered">
                     <thead>
                         <tr>
                             <th class="text-center">'.$this->lang->line("comment id").'</th>
                             <th class="text-center">'.$this->lang->line("comment status").'</th>
                             <th class="text-center">'.$this->lang->line("comment time").'</th>
                             <th class="text-center">'.$this->lang->line("schedule type").'</th>
                             <th>'.$this->lang->line("comment").'</th>
                             
                         </tr>
                     </thead>
                     <tbody>';
                         
                     
            $info = json_decode($reply_info[0]['auto_reply_done_info'],true);
    

            foreach($info as $value)
            {
                $comment_time = date('d M y H:i:s',strtotime($value['comment_time']));
               
                $reply_status = isset($value['reply_status']) ? $value['reply_status']:"";
                $schedule_type = isset($value['schedule_type']) ? $value['schedule_type']:"";
                if($reply_status=='success')

                 $reply_status='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("success").'</span>';
                if(!strpos($reply_status,'failed'))


                if($schedule_type =='periodic')
                 {$schedule_type='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("perodic").'</span>';}

        
                if($schedule_type == 'onetime')

                $schedule_type='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("onetime").'</span>';



                $str .= '<tr>
                            <td class="text-center"><a target="_BLANK" href="http://facebook.com/'.$value['id'].'" class="product-title">'.$value['id'].'</a></td>
                           
                            <td class="text-center">'.$reply_status.'</td>
                            <td class="text-center">'.$comment_time.'</td>
                            <td class="text-center">'.$schedule_type.'</td>
                            <td>'.$value['comment_text'].'</td>
                            
                        </tr>';
            }

            $str .= '</tbody>
                 </table></div>';
        }
        else
        {
            $str = "<div class='alert alert-danger'>{$this->lang->line("no data to show")}</div>";
        }

        echo $str;
    }

    public function all_auto_comment_report($page_id=0,$post_id=0,$is_instagram=0)
    {
        $this->session->set_userdata('all_search_page_name', '');

        $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"),'bot_enabled'=>'1')));

        $data['auto_comment_template'] = $this->basic->get_data('auto_comment_reply_tb',array("where"=>array('user_id'=>$this->user_id)),array('id','template_name'));
        $data["time_zone"]= $this->_time_zone_list();
        $data["periodic_time"] = $this->get_periodic_time();
        
        $data['page_info'] = $page_list;
        $data['post_id'] = $post_id;
        $data['page_id'] = $page_id;
        $data['is_instagram'] = $is_instagram;
        $data['body'] = 'comment_automation/all_auto_comment_report';
        $data['page_title'] = $this->lang->line('All Auto Comment Report');
        $data['emotion_list'] = $this->get_emotion_list();
        $this->_viewcontroller($data);
    }

    public function all_auto_comment_report_data()
    {
        $this->ajax_check();

        $page_table_id = trim($this->input->post("page_id",true));
        $campaign_name = trim($this->input->post("campaign_name",true));
        $is_instagram = trim($this->input->post("is_instagram",true));
        if(!isset($is_instagram) || $is_instagram=='') $is_instagram = '0';

        $display_columns = array("#",'id','post_thumb','campaign_name','page_name','post_id','actions','auto_comment_count','status','last_reply_time','error_message');
        $search_columns = array('campaign_name','post_id');
        if($is_instagram=='1') $display_columns[4] = 'insta_username';

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple=array();
        $where_simple['auto_comment_reply_info.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        if($is_instagram=='1')$where_simple['auto_comment_reply_info.social_media_type'] = "Instagram";
        else $where_simple['auto_comment_reply_info.social_media_type'] = "Facebook";


        if($page_table_id !="") $where_simple['auto_comment_reply_info.page_info_table_id'] = $page_table_id;

        $sql = '';
        if ($campaign_name != '') 
          $sql = "(auto_comment_reply_info.campaign_name LIKE  '%".$campaign_name."%' OR auto_comment_reply_info.post_id LIKE '%".$campaign_name."%')";
        if($sql != '')
          $this->db->where($sql);

        $where  = array('where'=>$where_simple);
        $join = array('facebook_rx_fb_page_info'=>'auto_comment_reply_info.page_info_table_id=facebook_rx_fb_page_info.id,left');
        $select = array('auto_comment_reply_info.*','facebook_rx_fb_page_info.page_id AS pageid','insta_username');
        $table = "auto_comment_reply_info";
        $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='');

        if($sql != '')
          $this->db->where($sql);
        $total_rows_array = $this->basic->count_row($table,$where,$count=$table.".id",$join,$group_by='');
        $total_result = $total_rows_array[0]['total_rows'];

        $info_new = array();
        $i = 0;
        foreach($info as $value)
        {
            $action_count = 5;
            if($value['auto_private_reply_status']=='2')
              $info_new[$i]['status'] = "<span class='text-danger min_width_180px'><i class='fas fa-clock'></i> ".$this->lang->line('expired')."</span>";
            else  
              $info_new[$i]['status'] = "<span class='text-success min_width_100px'><i class='fa fa-check-circle green'></i> ".$this->lang->line('live')."</span>";
            
            $onlypostid=explode('_', $value['post_id']);
            $onlypostid2=isset($onlypostid[1])?$onlypostid[1]:$value['post_id'];

            $info_new[$i]['id'] = $value['id'];
            $info_new[$i]['campaign_name'] = $value['campaign_name'];

            if($is_instagram=='1')
            $info_new[$i]['insta_username'] = "<a class='ash' data-toggle='tooltip' title='".$this->lang->line("Visit Account")."' target='_BLANK' href='https://instagram.com/".$value['insta_username']."'>".$value['insta_username']."</a>";
            else
              $info_new[$i]['page_name'] = "<a class='ash' data-toggle='tooltip' title='".$this->lang->line("Visit Page")."' target='_BLANK' href='https://facebook.com/".$value['pageid']."'>".$value['page_name']."</a>";
            
            if($is_instagram=='1')
            $info_new[$i]['post_id'] = "<a target='_BLANK' href='".$value['insta_media_url']."' data-toggle='tooltip' title='".$this->lang->line("Visit Post")."'>".$onlypostid2."</a>";
            else $info_new[$i]['post_id'] = "<a target='_BLANK' href='https://facebook.com/".$value['post_id']."' data-toggle='tooltip' title='".$this->lang->line("Visit Post")."'>".$onlypostid2."</a>";
            $info_new[$i]['auto_comment_count'] = $value['auto_comment_count'];
            
            $report_btn = "<a href='#' class='btn btn-circle btn-outline-primary view_report' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Campaign Report")."'><i class='fas fa-eye'></i></a>";

            $edit_btn ="<a href='#' class='btn btn-circle btn-outline-warning edit_reply_info' data-toggle='tooltip' title='".$this->lang->line("Edit Campaign")."' table_id='".$value['id']."'><i class='fas fa-edit'></i></a>";

            $deleteUrl ="<a href='#' class='btn btn-circle btn-outline-danger delete_report red' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("Delete Campaign")."'><i class='fas fa-trash-alt'></i></a>";
            
            $button = '';
            if($value['auto_private_reply_status'] == '0' || $value['auto_private_reply_status'] == '1') 
            {
              $button = "<a href='#' class='btn btn-circle btn-outline-dark pause_campaign_info' table_id='".$value['id']."' title='".$this->lang->line("pause campaign")."'><i class='fas fa-pause'></i></a>";
            }

            if($value['auto_private_reply_status'] == '2')
            {
              $button = "<a href='#' class='btn btn-circle btn-outline-success play_campaign_info' table_id='".$value['id']."' data-toggle='tooltip' title='".$this->lang->line("start campaign")."'><i class='fas fa-play'></i></a>";
            }

            $force = "<a href='#' id='".$value['id']."' class='force btn btn-circle btn-outline-info' data-toggle='tooltip' title='".$this->lang->line("force reprocessing")."'><i class='fas fa-sync'></i></a>";
            
            $info_new[$i]['post_created_at'] = $value['post_created_at'];
            $last_reply_time = $value['last_reply_time'];

            if($last_reply_time=='0000-00-00 00:00:00') 
              $last_reply_time='<span class="text-muted min_width_100px"><i class="fas fa-exclamation-circle"></i> '.$this->lang->line("Not Replied")."</span>";
            else 
              $last_reply_time= '<div class="min_width_100px">'.date("M j, y H:i",strtotime($last_reply_time)).'</div>';

            $info_new[$i]['last_reply_time'] = $last_reply_time;
            $info_new[$i]['error_message'] = $value['error_message'];
            $info_new[$i]['post_description'] = $value['post_description'];

            $post_thumb = isset($info_new[$i]['post_thumb']) ? $info_new[$i]['post_thumb'] : base_url('assets/img/avatar/avatar-1.png');
            $info_new[$i]['post_thumb'] = "<img class='rounded-circle instagram_height_width_40px_bordered' src='".$post_thumb."'>";

            // Action section started from here
            $action_width = ($action_count*47)+20;
            $info_new[$i]['actions'] = '<div class="dropdown d-inline dropright">
              <button class="btn btn-outline-primary dropdown-toggle no_caret" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-briefcase"></i>
              </button>
              <div class="dropdown-menu mini_dropdown text-center" style="width:'.$action_width.'px !important">';
            $info_new[$i]['actions'] .= $report_btn;
            $info_new[$i]['actions'] .= $edit_btn;
            $info_new[$i]['actions'] .= $button;
            $info_new[$i]['actions'] .= $force;
            $info_new[$i]['actions'] .= $deleteUrl;
            $info_new[$i]['actions'] .= "</div></div><script src='".base_url()."assets/js/system/tooltip_popover.js'></script>";

            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info_new, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function ajax_autocomment_pause()
    {
        $table_id = $this->input->post('table_id');
        $this->basic->update_data('auto_comment_reply_info',array('id'=>$table_id),array('auto_private_reply_status'=>'2'));
        echo 'success';
    }

    public function ajax_autocomment_renew_campaign()
    {
        $table_id = $this->input->post('table_id');
        $this->basic->update_data('auto_comment_reply_info',array('id'=>$table_id),array('last_updated_at'=>date("Y-m-d H:i:s")));
        echo 'success';
    }

    public function ajax_autocomment_play()
    {
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('auto_comment_reply_info',array('id'=>$table_id),array('auto_private_reply_status'=>'0'));
        echo 'success';
    }

    public function autocomment_force_reprocess_campaign()
    {
        if(!$_POST) exit();
        $id=$this->input->post("id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('auto_private_reply_status'=>'0');
        $this->basic->update_data('auto_comment_reply_info',$where,$data);
        if($this->db->affected_rows() != 0)
            echo "1";
        else
            echo "0";
    }


    /* Auto Comment reply Template Manager */

    public function comment_template_manager()
    {
        $data['body'] = 'comment_automation/auto_comment_reply_template';
        $data['page_title'] = $this->lang->line('Auto Comment Template Manager');
        
        $this->_viewcontroller($data);
    }

    public function create_template_action()
    {
        if(isset($_POST["action"]))
        {
            if($_POST["action"] == "insert")
            {
                  $auto_reply_comment_text =  $this->input->post("auto_reply_comment_text",true);
                
                  $auto_reply_comment_text=json_encode($auto_reply_comment_text);
                  $auto_reply_comment_text = str_replace('"",', '', $auto_reply_comment_text);
                
                
                  $data = array(
                      'user_id' => $this->user_id,
                      'template_name'                     => strip_tags($this->input->post("template_name",true)),
                      'auto_reply_comment_text'    =>  $auto_reply_comment_text
                  );

                 
                  if($this->basic->insert_data('auto_comment_reply_tb',$data)) 
                  {

                      $return['status'] = 1;
                      $return['message'] = "<div class='alert alert-success'>".$this->lang->line("your given information has been submitted successfully.")."</div>";
                  }
                  else
                  {
                      $return['status'] = 0;
                      $return['message'] = "<div class='alert alert-danger'>".$this->lang->line("something went wrong, please try again.")."</div>";
                  }
                
                  echo json_encode($return);
                
              
            }




          if($_POST["action"] == "edit")
          {
                $id = $_POST['hidden_id'];
               
                $where = array('id'=>$id);

                $auto_reply_comment_text =  $this->input->post("auto_reply_comment_text",true);
                
                $auto_reply_comment_text=json_encode($auto_reply_comment_text);
                $auto_reply_comment_text = str_replace('"",', '', $auto_reply_comment_text);
            
                $data = array(
                    
                    'template_name' => strip_tags($this->input->post("template_name",true)),
                    'auto_reply_comment_text' => $auto_reply_comment_text

                    );
                
              if($this->basic->update_data('auto_comment_reply_tb',$where,$data)){
                    echo "success";

                } else
                {
                    echo "fail";
                }
          }

        }

    }


    public function template_manager_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $this->ajax_check();

        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','template_name');
        $search_columns = array('template_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        if ($search_value != '') $where_simple['template_name like ']    = "%".$search_value."%";
        
        $where_simple['auto_comment_reply_tb.user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);

        $table="auto_comment_reply_tb";
        $info=$this->basic->get_data($table,$where,$select='',$join='',$limit,$start,$order_by,$group_by='');
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

        echo json_encode($data);
    }

    public function ajaxselect()
    {  
        $id = $this->input->post('id');
        $info = $this->basic->get_data("auto_comment_reply_tb",array("where"=>array("id"=>$id)));

        $auto_reply_comment_text = '';
        $template_name = '';
         foreach($info as $row)
        {
            $template_name = $row["template_name"];
            $comment_array = json_decode($row["auto_reply_comment_text"]);
            $x = count($comment_array);
            $count = 1;
            foreach($comment_array as $comment)
            {

    

                $auto_reply_comment_text .= '
                    <div class="single_item card card-primary margin_top_10px pb-0 mb-0">
                      <div class="card-header"><h4 class="modal-title text-center"><i class="fa fa-comments"></i> '.$this->lang->line("auto comment").'</h4></div> 
                      <div class="card-body">
                        <textarea type="text" name="auto_reply_comment_text[]" id="auto_reply_comment_text_'.$count.'" class="form-control name_list height_70px width_100">'.$comment.'</textarea>
                          <span class="clearfix"><a href="#" class="font_size_10px text-center btn btn-outline-danger btn-sm remove_field float-right title="'.$this->lang->line('remove').'"><i class="fas fa-times"></i> '.$this->lang->line("remove").'</a>
                      </div>
                        
                      </span>
                    </div>
                ';
                $count++;
            }
         }
         $output = array(
            'template_name'  =>  $template_name,
            'auto_reply_comment_text' =>  $auto_reply_comment_text,
            'x' => $x
            
         );
        
         echo json_encode($output);
        
    }

    public function delete_comment()
    {
        if(isset($_POST["id"]))
        {
            $id = $this->input->post('id');
            $this->basic->delete_data('auto_comment_reply_tb',array('id'=>$id,'user_id'=>$this->user_id));
        }
    }


    // =========== pageresponse section ================== //
    public function pageresponse_autoreply_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }
        //************************************************//
        $status=$this->_check_usage($module_id=204,$request=1);
        if($status=="2") 
        {
            $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        else if($status=="3") 
        {
            $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        //************************************************//
        
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$pageresponse_auto_reply_page_id,'user_id'=>$this->user_id)));
        $page_name = $page_info[0]['page_name'];
        $auto_reply_post_id = trim($pageresponse_auto_reply_post_id);
        
        
        $return = array();
        $facebook_rx_fb_user_info = $this->session->userdata("facebook_rx_fb_user_info");
        $date_time = date("Y-m-d H:i:s");
        $post_created_at = $date_time;
        $post_description = "";
        $post_description = $this->db->escape($post_description);
        $nofilter_array['comment_reply'] = trim($pageresponse_nofilter_word_found_text);
        $nofilter_array['private_reply'] = trim($pageresponse_nofilter_word_found_text_private);
        $nofilter_array['image_link'] = trim($pageresponse_nofilter_image_upload_reply);
        $nofilter_array['video_link'] = trim($pageresponse_nofilter_video_upload_reply);
        $no_filter_array = array();
        array_push($no_filter_array, $nofilter_array);
        $nofilter_word_found_text = json_encode($no_filter_array);
        $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);
        // comment hide and delete section
        $is_delete_offensive = $pageresponse_delete_offensive_comment;
        $offensive_words = trim($pageresponse_delete_offensive_comment_keyword);
        $offensive_words = $this->db->escape($offensive_words);
        $private_message_offensive_words = $this->db->escape($pageresponse_private_message_offensive_words);
        // end of comment hide and delete section
        $page_name = $this->db->escape($page_name);
        $auto_campaign_name = $this->db->escape($pageresponse_auto_campaign_name);

        $pageresponse_multiple_reply                   = $this->input->post('pageresponse_multiple_reply',true);
        $pageresponse_auto_like_comment                = $this->input->post('pageresponse_auto_like_comment',true);
        $pageresponse_comment_reply_enabled            = $this->input->post('pageresponse_comment_reply_enabled',true);
        $pageresponse_hide_comment_after_comment_reply = $this->input->post('pageresponse_hide_comment_after_comment_reply',true);

        if($pageresponse_multiple_reply == '') $pageresponse_multiple_reply = 'no';
        if($pageresponse_comment_reply_enabled == '') $pageresponse_comment_reply_enabled = 'no';
        if($pageresponse_auto_like_comment == '') $pageresponse_auto_like_comment = 'no';
        if($pageresponse_hide_comment_after_comment_reply == '') $pageresponse_hide_comment_after_comment_reply = 'no';
    
    
    
        if($pageresponse_message_type == 'generic')
        {
            $generic_message_array['comment_reply'] = trim($pageresponse_generic_message);
            $generic_message_array['private_reply'] = trim($pageresponse_generic_message_private);
            $generic_message_array['image_link'] = trim($pageresponse_generic_image_for_comment_reply);
            $generic_message_array['video_link'] = trim($pageresponse_generic_video_comment_reply);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = '';
            $auto_reply_text = json_encode($generic_array);
            $auto_reply_text = $this->db->escape($auto_reply_text); 
            $sql = "INSERT INTO page_response_autoreply (page_response_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_created_at,post_description,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,last_updated_at,nofilter_word_found_text,structured_message,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id',$auto_campaign_name,'$pageresponse_auto_reply_page_id',$page_name,'$pageresponse_auto_reply_post_id','$post_created_at',$post_description,'$pageresponse_message_type','$pageresponse_hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$pageresponse_auto_like_comment','$pageresponse_multiple_reply','$pageresponse_comment_reply_enabled',$auto_reply_text,'$date_time',$nofilter_word_found_text,'yes','$pageresponse_trigger_matching_type')
            ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$pageresponse_message_type',hide_comment_after_comment_reply='$pageresponse_hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$pageresponse_auto_like_comment',multiple_reply='$pageresponse_multiple_reply',comment_reply_enabled='$pageresponse_comment_reply_enabled',auto_reply_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,trigger_matching_type='$pageresponse_trigger_matching_type'";
        }
        else
        {
            $auto_reply_text_array = array();
            for($i=1;$i<=20;$i++)
            {
                $filter_word = 'pageresponse_filter_word_'.$i;
                $filter_word_text = $this->input->post($filter_word);
                $filter_message = 'pageresponse_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);
                // added 25-04-2017
                $comment_message = 'pageresponse_comment_reply_msg_'.$i;
                $comment_message_text = $this->input->post($comment_message);
                $image_field_name = 'pageresponse_filter_image_upload_reply_'.$i;
                $image_link = $this->input->post($image_field_name);
                $video_field_name = 'pageresponse_filter_video_upload_reply_'.$i;
                $video_link = $this->input->post($video_field_name);
                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                {
                    // $auto_reply_text_array[$filter_word_text] = $filter_message_text;
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['image_link'] = trim($image_link);
                    $data['video_link'] = trim($video_link);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = '';
            $auto_reply_text = json_encode($auto_reply_text_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);
            $sql = "INSERT INTO page_response_autoreply (page_response_user_info_id,user_id,auto_reply_campaign_name,page_info_table_id,page_name,post_id,post_created_at,post_description,reply_type,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,auto_like_comment,multiple_reply,comment_reply_enabled,auto_reply_text,last_updated_at,nofilter_word_found_text,structured_message,trigger_matching_type) VALUES ('$facebook_rx_fb_user_info','$this->user_id',$auto_campaign_name,'$pageresponse_auto_reply_page_id',$page_name,'$pageresponse_auto_reply_post_id','$post_created_at',$post_description,'$pageresponse_message_type','$pageresponse_hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$pageresponse_auto_like_comment','$pageresponse_multiple_reply','$pageresponse_comment_reply_enabled',$auto_reply_text,'$date_time',$nofilter_word_found_text,'yes','$pageresponse_trigger_matching_type')
            ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$pageresponse_message_type',hide_comment_after_comment_reply='$pageresponse_hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$pageresponse_auto_like_comment',multiple_reply='$pageresponse_multiple_reply',comment_reply_enabled='$pageresponse_comment_reply_enabled',auto_reply_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text,trigger_matching_type='$pageresponse_trigger_matching_type'";
        }
        
        if($this->db->query($sql))
        {
            //insert data to useges log table
            $this->_insert_usage_log($module_id=204,$request=1);
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }

    public function pageresponse_reply_info()
    {
        $respnse = array();
        $table_id = $this->input->post('table_id');
        $info = $this->basic->get_data('page_response_autoreply',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)));

        $page_table_id = $info[0]['page_info_table_id'];
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','page_id'=>$page_table_id)),'','','',$start=NULL,'');
        $str = "<option value=''>".$this->lang->line('Please select a message template')."</option>";

        foreach ($postback_data as $key => $value) 
        {
            $str.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $respnse['postbacks'] = $str;
        
        if($info[0]['reply_type'] == 'generic'){
            $reply_content = json_decode($info[0]['auto_reply_text']);
            if(!is_array($reply_content))
            {
                $reply_content[0]['comment_reply'] = "";
                $reply_content[0]['private_reply'] = $info[0]['auto_reply_text'];
                $reply_content[0]['image_link'] = "";
                $reply_content[0]['video_link'] = "";
            }
        }
        else
            $reply_content = json_decode($info[0]['auto_reply_text']);
        $nofilter_word_text = json_decode($info[0]['nofilter_word_found_text']);
        if(!is_array($nofilter_word_text))
        {
            $nofilter_word_text[0]['comment_reply'] = '';
            $nofilter_word_text[0]['image_link'] = '';
            $nofilter_word_text[0]['video_link'] = '';
            $nofilter_word_text[0]['private_reply'] = $info[0]['nofilter_word_found_text'];
        }
        $respnse['reply_type'] = $info[0]['reply_type'];
        $respnse['trigger_matching_type'] = $info[0]['trigger_matching_type'];
        $respnse['pageresponse_comment_reply_enabled'] = $info[0]['comment_reply_enabled'];
        $respnse['pageresponse_multiple_reply'] = $info[0]['multiple_reply'];
        $respnse['pageresponse_auto_like_comment'] = $info[0]['auto_like_comment'];
        $respnse['auto_reply_text'] = $reply_content;
        $respnse['pageresponse_edit_auto_reply_page_id'] = $info[0]['page_info_table_id'];
        $respnse['pageresponse_edit_auto_reply_post_id'] = $info[0]['post_id'];
        $respnse['pageresponse_edit_auto_campaign_name'] = $info[0]['auto_reply_campaign_name'];
        $respnse['pageresponse_edit_nofilter_word_found_text'] = $nofilter_word_text;
        // comment hide and delete section
        $respnse['is_delete_offensive'] = $info[0]['is_delete_offensive'];
        $respnse['offensive_words'] = $info[0]['offensive_words'];
        $respnse['private_message_offensive_words'] = $info[0]['private_message_offensive_words'];
        $respnse['hide_comment_after_comment_reply'] = $info[0]['hide_comment_after_comment_reply'];
        // comment hide and delete section
        echo json_encode($respnse);
    }

    public function pageresponse_autoreply_update()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }
        $return = array();

        $pageresponse_edit_multiple_reply                   = $this->input->post('pageresponse_edit_multiple_reply',true);
        $pageresponse_edit_auto_like_comment                = $this->input->post('pageresponse_edit_auto_like_comment',true);
        $pageresponse_edit_comment_reply_enabled            = $this->input->post('pageresponse_edit_comment_reply_enabled',true);
        $pageresponse_edit_hide_comment_after_comment_reply = $this->input->post('pageresponse_edit_hide_comment_after_comment_reply',true);

        if($pageresponse_edit_multiple_reply == '') $pageresponse_edit_multiple_reply = 'no';
        if($pageresponse_edit_comment_reply_enabled == '') $pageresponse_edit_comment_reply_enabled = 'no';
        if($pageresponse_edit_auto_like_comment == '') $pageresponse_edit_auto_like_comment = 'no';
        if($pageresponse_edit_hide_comment_after_comment_reply == '') $pageresponse_edit_hide_comment_after_comment_reply = 'no';

        if($pageresponse_edit_message_type == 'generic')
        {
            // $auto_reply_text = $edit_generic_message;
            $generic_message_array['comment_reply'] = trim($pageresponse_edit_generic_message);
            $generic_message_array['private_reply'] = trim($pageresponse_edit_generic_message_private);
            $generic_message_array['image_link'] = trim($pageresponse_edit_generic_image_for_comment_reply);
            $generic_message_array['video_link'] = trim($pageresponse_edit_generic_video_comment_reply);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = json_encode($generic_array);
            // $auto_reply_text = $this->db->escape($generic_message_text);
        }
        else
        {
            $auto_reply_text_array = array();
            for($i=1;$i<=20;$i++)
            {
                $filter_word = 'pageresponse_edit_filter_word_'.$i;
                $filter_word_text = $this->input->post($filter_word);
                $filter_message = 'pageresponse_edit_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);
                // added 25-04-2017
                $comment_message = 'pageresponse_edit_comment_reply_msg_'.$i;
                $comment_message_text = $this->input->post($comment_message);
                $image_field_name = 'pageresponse_edit_filter_image_upload_reply_'.$i;
                $image_link = $this->input->post($image_field_name);
                $video_field_name = 'pageresponse_edit_filter_video_upload_reply_'.$i;
                $video_link = $this->input->post($video_field_name);
                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                {
                    // $auto_reply_text_array[$filter_word_text] = $this->db->escape($filter_message_text);
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['image_link'] = trim($image_link);
                    $data['video_link'] = trim($video_link);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = json_encode($auto_reply_text_array);
        }
        $no_filter_array['comment_reply'] = trim($pageresponse_edit_nofilter_word_found_text);
        $no_filter_array['private_reply'] = trim($pageresponse_edit_nofilter_word_found_text_private);
        $no_filter_array['image_link'] = trim($pageresponse_edit_nofilter_image_upload_reply);
        $no_filter_array['video_link'] = trim($pageresponse_edit_nofilter_video_upload_reply);
        $nofilter_array = array();
        array_push($nofilter_array, $no_filter_array);
        $data = array(
            'auto_reply_text' => $auto_reply_text,
            'reply_type' => $pageresponse_edit_message_type,
            'auto_reply_campaign_name' => $pageresponse_edit_auto_campaign_name,
            'nofilter_word_found_text' => json_encode($nofilter_array),
            'comment_reply_enabled' => $pageresponse_edit_comment_reply_enabled,
            'multiple_reply' => $pageresponse_edit_multiple_reply,
            // comment hide and delete section
            'is_delete_offensive' => $pageresponse_edit_delete_offensive_comment,
            'offensive_words' => trim($pageresponse_edit_delete_offensive_comment_keyword),
            'private_message_offensive_words' => trim($pageresponse_edit_private_message_offensive_words),
            'hide_comment_after_comment_reply' => $pageresponse_edit_hide_comment_after_comment_reply,
            // comment hide and delete section
            'auto_like_comment' => $pageresponse_edit_auto_like_comment,
            'structured_message' => 'yes',
            'trigger_matching_type' => $pageresponse_edit_trigger_matching_type
            );

        $where = array(
            'user_id' => $this->user_id,
            'page_info_table_id' => $pageresponse_edit_auto_reply_page_id,
            'post_id' => $pageresponse_edit_auto_reply_post_id
            );
        if($this->basic->update_data('page_response_autoreply',$where,$data))
        {
            $return['status'] = 1;
            $return['message'] = $this->lang->line("your given information has been updated successfully.");
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = $this->lang->line("something went wrong, please try again.");
        }
        echo json_encode($return);
    }


    public function add_auto_like_share()
    {
        $table_id = $this->input->post('table_id');
        $page_table_id = $this->input->post('page_table_id');
        $page_id = $this->input->post('page_id');
        $facebook_rx_fb_user_info_id = $this->input->post('page_response_user_info_id');  
        $page_list = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_user_info_id'=>$facebook_rx_fb_user_info_id)));

        $str = '
                <form action="#" id="autolikeshare_reply_info_form" method="post">
                  <input type="hidden" name="autolikeshare_page_info_table_id" id="autolikeshare_page_info_table_id" value="'.$page_table_id.'">
                  <input type="hidden" name="autolikeshare_page_id" id="autolikeshare_page_id" value="'.$page_id.'">
                  <input type="hidden" name="facebook_rx_fb_user_info_id" id="facebook_rx_fb_user_info_id" value="'.$facebook_rx_fb_user_info_id.'">
                  <div class="row padding_0_10px">               
                    <div class="col-12 col-md-6">
                      <label><i class="fa fa-share-square"></i> '.$this->lang->line("Auto share this post").'
                      </label>
                    </div>
                    <div class="col-12 col-md-6">
                      <div class="form-group">
                      <label class="custom-switch">
                        <input type="checkbox" name="auto_share_post" value="1" id="auto_share_post" class="custom-switch-input">
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">'.$this->lang->line("Enable").'</span>
                      </label>
                      </div>
                    </div>
                  </div>

                  <div class="auto_share_post_block_item">
                    <div class="row padding_0_10px">
                      <div class="col-12">
                        <div class="row">
                          <div class="col-12 col-md-6">
                            <label><i class="fas fa-file-alt"></i> '.$this->lang->line("Auto share as pages").'</label>
                            <div class="form-group">
                              <select multiple="" class="form-control select2" id="auto_share_this_post_by_pages" name="auto_share_this_post_by_pages[]">';
                                foreach($page_list as $key=>$val)
                                { 
                                  $id=$val['id'];
                                  $page_name=$val['page_name'];
                                  $str .= "<option value='{$id}'>{$page_name}</option>";               
                                }
                                          
                              $str .= '</select>
                            </div>
                          </div>
                          <div class="col-12 col-md-6">
                            <label><i class="far fa-clock"></i> '.$this->lang->line("Delay time (Seconds) between share to pages").'</label>
                            <div class="form-group">
                              <select class="form-control" id="delay_time" name="delay_time">  
                                <option value="0">Random Delay</option>    
                                <option value="5">5 sec.</option>    
                                <option value="10">10 sec.</option>    
                                <option value="15">15 sec.</option>    
                                <option value="20">20 sec.</option>    
                                <option value="25">25 sec.</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                  
                  <div class="row padding_0_10px">               
                    <div class="col-12 col-md-6">
                      <label><i class="far fa-thumbs-up"></i> '.$this->lang->line("Auto like this post by other pages").'
                      </label>
                    </div>
                    <div class="col-12 col-md-6">
                      <div class="form-group">
                        <label class="custom-switch">
                          <input type="checkbox" name="auto_like_post" value="1" id="auto_like_post" class="custom-switch-input">
                          <span class="custom-switch-indicator"></span>
                          <span class="custom-switch-description">'.$this->lang->line("Enable").'</span>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="auto_like_post_block_item">
                    <div class="row padding_0_10px">
                      <div class="col-12 col-md-12">
                        <label><i class="fas fa-file-alt"></i> '.$this->lang->line("Auto like as pages").' [<span class="red">'.$this->lang->line('Support Only two pages by Facebook').'</span>]</label>
                        <div class="form-group">
                          <select multiple="" class="form-control select2" id="auto_like_this_post_by_pages" name="auto_like_this_post_by_pages[]">';
                          
                            foreach($page_list as $key=>$val)
                            { 
                              $id=$val['id'];
                              $page_name=$val['page_name'];
                              $str .= "<option value='{$id}'>{$page_name}</option>";               
                            }          
                          $str .='</select>
                        </div> 
                      </div>
                    </div>
                  </div>
                </form>              
               ';
          $str .= '<script src="'.base_url().'assets/js/system/instagram/auto_like_share_add.js"></script>';
        echo $str;
    }
    
    public function ajax_auto_share_like_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }

        $auto_share_post = $this->input->post("auto_share_post",true);
        $auto_like_post = $this->input->post("auto_like_post",true);

        if($auto_share_post == "") $auto_share_post = "0";
        if($auto_like_post == "") $auto_like_post = "0";


        //************************************************//
        $status=$this->_check_usage($module_id=206,$request=1);
        if($status=="2") 
        {
            $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        else if($status=="3") 
        {
            $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        //************************************************//
        if(!isset($auto_share_this_post_by_pages) || !is_array($auto_share_this_post_by_pages)) $auto_share_this_post_by_pages=array();
        $auto_share_this_post_by_pages_new=json_encode($auto_share_this_post_by_pages);
        if(!isset($auto_like_this_post_by_pages) || !is_array($auto_like_this_post_by_pages)) $auto_like_this_post_by_pages=array();
        $auto_like_this_post_by_pages_new=json_encode($auto_like_this_post_by_pages);
      
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$autolikeshare_page_info_table_id)));
        $page_name = $page_info[0]['page_name'];
        $return = array();

        $page_name = $this->db->escape($page_name);        
        $sql = "INSERT INTO page_response_auto_like_share 
        (
            page_response_user_info_id,
            user_id,
            page_info_table_id,
            page_name,
            page_id,
            auto_share_post,
            auto_share_this_post_by_pages,
            auto_like_post,
            auto_like_this_post_by_pages,
            delay_time
        ) 
        VALUES 
        (
            '$facebook_rx_fb_user_info_id',
            '$this->user_id',
            '$autolikeshare_page_info_table_id',
             $page_name,
            '$autolikeshare_page_id',
            '$auto_share_post',
            '$auto_share_this_post_by_pages_new',
            '$auto_like_post',
            '$auto_like_this_post_by_pages_new',
            '$delay_time'
        )";
     
        if($this->db->query($sql))
        {
            $this->_insert_usage_log($module_id=206,$request=1);
            $return['status'] = 1;
            $return['message'] = "Campaign has been created successfully.";
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = "Something went wrong, please try again.";
        }
        echo json_encode($return);
    }

    

    public function edit_auto_like_share()
    {
        $table_id = $this->input->post('table_id');
        $facebook_rx_fb_user_info_id = $this->input->post('page_response_user_info_id');
        $existing_data = $this->basic->get_data('page_response_auto_like_share',array('where'=>array('id'=>$table_id)));
        $auto_like_pages = json_decode($existing_data[0]['auto_like_this_post_by_pages'],true);
        $auto_share_pages = json_decode($existing_data[0]['auto_share_this_post_by_pages'],true);
        $auto_like_enable = $existing_data[0]['auto_like_post'];
        $auto_share_enable = $existing_data[0]['auto_share_post'];
        $delay_time = $existing_data[0]['delay_time'];
        $page_list = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('facebook_rx_fb_user_info_id'=>$facebook_rx_fb_user_info_id)));

        if($auto_like_enable == "1") $like_checked="checked";
        else $like_checked = "";
        if($auto_share_enable == "1") $share_checked="checked";
        else $share_checked = "";

        $str = '
                          <form action="#" id="autolikeshare_edit_auto_reply_info_form" method="post">
                            <input type="hidden" name="table_id" id="table_id" value="'.$table_id.'">
                            <div class="row padding_0_10px">               
                              <div class="col-12 col-md-6">
                                <label><i class="fa fa-share-square"></i> '.$this->lang->line("Auto share this post").'
                                </label>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="form-group">
                                <label class="custom-switch">
                                  <input type="checkbox" name="edit_auto_share_post" value="1" id="edit_auto_share_post" class="custom-switch-input" '.$share_checked.'>
                                  <span class="custom-switch-indicator"></span>
                                  <span class="custom-switch-description">'.$this->lang->line("Enable").'</span>
                                </label>
                                </div>
                              </div>
                            </div>

                            <div class="edit_auto_share_post_block_item">
                              <div class="row padding_0_10px">
                                <div class="col-12">
                                  <div class="row">
                                    <div class="col-12 col-md-6">
                                      <label><i class="fas fa-file-alt"></i> '.$this->lang->line("Auto share as pages").'</label>
                                      <div class="form-group">
                                        <select multiple="" class="form-control select2" id="edit_auto_share_this_post_by_pages" name="auto_share_this_post_by_pages[]">';
                                          foreach($page_list as $key=>$val)
                                          { 
                                            $temp = '';
                                            $id=$val["id"];
                                            $page_name=$val["page_name"];
                                            if(in_array($id, $auto_share_pages)) $temp = 'selected';
                                            $str .= "<option value=".$id." ".$temp.">".$page_name."</option>";               
                                          }
                                                    
                                        $str .= '</select>
                                      </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                      <label><i class="far fa-clock"></i> '.$this->lang->line("Delay time (Seconds) between share to pages").'</label>
                                      <div class="form-group">
                                        <select class="form-control" id="delay_time" name="delay_time">  
                                          <option value="0">Random Delay</option>    
                                          <option value="5"'; if($delay_time == "5") $str.="selected"; $str.='>5 sec.</option>    
                                          <option value="10"'; if($delay_time == "10")$str.= "selected"; $str.='>10 sec.</option>    
                                          <option value="15"'; if($delay_time == "15")$str.= "selected"; $str.='>15 sec.</option>    
                                          <option value="20"'; if($delay_time == "20")$str.= "selected"; $str.='>20 sec.</option>    
                                          <option value="25"'; if($delay_time == "25")$str.= "selected"; $str.='>25 sec.</option>
                                        </select>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                
                              </div>
                            </div>
                            
                            <div class="row padding_0_10px">               
                              <div class="col-12 col-md-6">
                                <label><i class="far fa-thumbs-up"></i> '.$this->lang->line("Auto like this post by other pages").'
                                </label>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="form-group">
                                  <label class="custom-switch">
                                    <input type="checkbox" name="edit_auto_like_post" value="1" id="edit_auto_like_post" class="custom-switch-input" '.$like_checked.'>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">'.$this->lang->line("Enable").'</span>
                                  </label>
                                </div>
                              </div>
                            </div>

                            <div class="edit_auto_like_post_block_item">
                              <div class="row padding_0_10px">
                                <div class="col-12 col-md-12">
                                  <label><i class="fas fa-file-alt"></i> '.$this->lang->line("Auto like as pages").' [<span class="red">'.$this->lang->line('Support Only two pages by Facebook').'</span>]</label>
                                  <div class="form-group">
                                    <select multiple="" class="form-control select2" id="edit_auto_like_this_post_by_pages" name="auto_like_this_post_by_pages[]">';
                                    
                                      foreach($page_list as $key=>$val)
                                      { 
                                        $temp = '';
                                        $id=$val["id"]; 
                                        $page_name=$val["page_name"];
                                        if(in_array($id, $auto_like_pages)) $temp = 'selected';
                                        $str .= "<option value=".$id." ".$temp.">".$page_name."</option>";               
                                      }          
                                    $str .='</select>
                                  </div> 
                                </div>
                              </div>
                            </div>
                          </form>';
                 $str .= '<script src="'.base_url().'assets/js/system/instagram/auto_like_share_edit.js"></script>';
        echo $str;
    }

    public function edit_auto_like_share_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                if(!is_array($value))
                    $temp = strip_tags($value);
                else
                    $temp = $value;

                $$key=$temp;
            }
        }

        $edit_auto_share_post = $this->input->post("edit_auto_share_post",true);
        $edit_auto_like_post = $this->input->post("edit_auto_like_post",true);

        if($edit_auto_share_post == "") $edit_auto_share_post = "0";
        if($edit_auto_like_post == "") $edit_auto_like_post = "0";

        if(!isset($auto_share_this_post_by_pages) || !is_array($auto_share_this_post_by_pages)) $auto_share_this_post_by_pages=array();
        $auto_share_this_post_by_pages_new=json_encode($auto_share_this_post_by_pages);
        if(!isset($auto_like_this_post_by_pages) || !is_array($auto_like_this_post_by_pages)) $auto_like_this_post_by_pages=array();
        $auto_like_this_post_by_pages_new=json_encode($auto_like_this_post_by_pages);
        $update_data = array(
            'auto_share_post' => $edit_auto_share_post,
            'auto_share_this_post_by_pages' => $auto_share_this_post_by_pages_new,
            'auto_like_post' => $edit_auto_like_post,
            'auto_like_this_post_by_pages' => $auto_like_this_post_by_pages_new,
            'delay_time' => $delay_time
        );
        $this->basic->update_data('page_response_auto_like_share',array('id'=>$table_id),$update_data);
        echo 'success';
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

    public function get_fb_rx_config($fb_user_id=0)
    {
        if($fb_user_id==0) return 0;
        $getdata= $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=>$fb_user_id)),array("facebook_rx_config_id"));
        $return_val = isset($getdata[0]["facebook_rx_config_id"]) ? $getdata[0]["facebook_rx_config_id"] : 0;
        return $return_val; 
       
    }

    
    public function webhook_callback_main()
    {
        $response_raw=$this->input->post("response_raw"); 
        $response = json_decode($response_raw,TRUE);

        if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] != 'feed') exit();

        if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'comment') 
        {
          $page_id = $response['entry'][0]['id'];
          $post_id = $response['entry'][0]['changes'][0]['value']['parent_id'];


          $comment = isset($response['entry'][0]['changes'][0]['value']['message'])? $response['entry'][0]['changes'][0]['value']['message'] :"";
          if($comment=="") exit; 

          $sender_name = '';
          if(isset($response['entry'][0]['changes'][0]['value']['sender_name']))
              $sender_name = $response['entry'][0]['changes'][0]['value']['sender_name'];
          if(isset($response['entry'][0]['changes'][0]['value']['from']['name']))
              $sender_name = $response['entry'][0]['changes'][0]['value']['from']['name'];


          $final_response[0]['created_time']['date'] = isset($response['entry'][0]['changes'][0]['value']['created_time']) ? date("Y-m-d H:i:s",$response['entry'][0]['changes'][0]['value']['created_time']) : array();

          $final_response[0]['from'] = isset($response['entry'][0]['changes'][0]['value']['from']) ? $response['entry'][0]['changes'][0]['value']['from'] : array();
          $final_response[0]['message'] = isset($response['entry'][0]['changes'][0]['value']['message']) ? $response['entry'][0]['changes'][0]['value']['message'] : '';
          $final_response[0]['id'] = isset($response['entry'][0]['changes'][0]['value']['comment_id']) ? $response['entry'][0]['changes'][0]['value']['comment_id'] : '';

          $comment_list = $final_response;

          $already_enabled_by_post_id = $this->basic->get_data('facebook_ex_autoreply',array('where'=>array('post_id'=>$post_id)),array('id'));
          if(!empty($already_enabled_by_post_id)) 
          {
            $post_table_id = isset($already_enabled_by_post_id[0]['id']) ? $already_enabled_by_post_id[0]['id'] : 0;
            $this->send_autoreply_with_postid($post_table_id,$final_response);
            exit();
          }

          if(!$this->basic->is_exist("add_ons",array("project_id"=>29))){
         	  exit; 
          }


          $where['where']=array('post_id'=>$page_id, 'pause_play'=>'play');
          $select="page_response_autoreply.id as column_id,post_id,page_id,page_access_token,auto_reply_text,facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,multiple_reply,comment_reply_enabled,reply_type,auto_like_comment,nofilter_word_found_text,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,hidden_comment_count,deleted_comment_count,auto_comment_reply_count,users.deleted as user_deleted,users.status as user_status,page_response_autoreply.page_name as page_name,page_response_autoreply.user_id as user_id,page_response_autoreply.page_info_table_id,structured_message";
          $join=array(
              'facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=page_response_autoreply.page_info_table_id,left",
              'users' => 'page_response_autoreply.user_id=users.id,left'
              );
          $post_info= $this->basic->get_data("page_response_autoreply",$where,$select,$join);

          if(empty($post_info)) exit;


          /***    Start Sending Private reply ****/
          $config_id_database=array();
          // setting fb confid id for library call
          $this->load->library("Fb_rx_login");
          foreach($post_info as $info){
              // if(!$this->api_member_validity($info['user_id'])) continue;.
              $page_table_id = $info['page_info_table_id'];

              $structured_message = $info['structured_message'];
              /***    get all comment from post **/
              $auto_like_comment = $info['auto_like_comment'];
              $page_id = $info['page_id'];
              $post_access_token = $info['page_access_token'];
              $post_reply_report_data = $this->basic->get_data('page_response_report',array('where'=>array('post_id'=>$post_id,'page_response_autoreply_id'=>$info['column_id'])));
              $post_column_id= isset($post_reply_report_data[0]['id']) ? $post_reply_report_data[0]['id'] : 0;

              $user_id = $info['user_id'];
              $temp_insert_data = array(
                  "page_response_autoreply_id" => $info['column_id'],                
                    "post_id" => $post_id,
                    'page_name' => $info['page_name'],
                    'user_id' => $info['user_id'],
                    'page_info_table_id' => $info['page_info_table_id']
                );
              if(empty($post_reply_report_data))
              {
                $this->basic->insert_data('page_response_report',$temp_insert_data);
                $post_column_id = $this->db->insert_id();
              }

              $trigger_matching_type = $info['trigger_matching_type'];
              // comment hide and delete section
              $private_message_offensive_words = $info['private_message_offensive_words'];
              $hidden_comment_count = 0;
              $deleted_comment_count = 0;
              $auto_comment_reply_count = 0;
              $hidden_comment_count = isset($post_reply_report_data[0]["hidden_comment_count"]) ? $post_reply_report_data[0]["hidden_comment_count"] : 0;
              $deleted_comment_count = isset($post_reply_report_data[0]["deleted_comment_count"]) ? $post_reply_report_data[0]["deleted_comment_count"] : 0;
              $auto_comment_reply_count = isset($post_reply_report_data[0]["auto_comment_reply_count"]) ? $post_reply_report_data[0]["auto_comment_reply_count"] : 0;
              $hide_comment_after_comment_reply = $info['hide_comment_after_comment_reply'];
              $is_delete_offensive = $info['is_delete_offensive'];
              $offensive_words = $info['offensive_words'];
              
              $auto_reply_private_message_raw= $info['auto_reply_text'];
              $auto_reply_type= $info['reply_type'];
              $default_reply_no_filter = json_decode($info['nofilter_word_found_text'],true);
              if(is_array($default_reply_no_filter))
              {
                  $default_reply_no_filter_comment = $default_reply_no_filter[0]['comment_reply'];
                  $default_reply_no_filter_private = $default_reply_no_filter[0]['private_reply'];
                  $default_reply_no_filter_comment_image_link = $default_reply_no_filter[0]['image_link'];
                  $default_reply_no_filter_comment_video_link = $default_reply_no_filter[0]['video_link'];
              }
              else
              {
                  $default_reply_no_filter_comment = "";
                  $default_reply_no_filter_private = $info['nofilter_word_found_text'];
              }
              $comment_reply_enabled = $info['comment_reply_enabled'];
              $multiple_reply = $info['multiple_reply'];


              if($multiple_reply == 'no')
              { 
                $commenter_id_check_mutliple  = isset($comment_list[0]['from']['id']) ? $comment_list[0]['from']['id'] : '';
                $already_replied_commenter_id = $this->basic->get_data('facebook_ex_autoreply_report',array('where'=>array('commenter_id'=>$commenter_id_check_mutliple,'autoreply_table_id'=>$post_column_id,'reply_type'=>'full_page_response')));
                if(!empty($already_replied_commenter_id)) exit; 
              }


              // setting fb config id for library call
              $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
              if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
              {
                  $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
              }
              
              $skip_error_message = '';
              
              $new_replied_info=array();

              if($config_id_database[$fb_rx_fb_user_info_id] == 0)
              {
                  $skip_error_message = "Corresponding Facebook account has been removed from database";
                  goto skipped;
              }


              // setting fb confid id for library call
              $this->fb_rx_login->app_initialize($config_id_database[$fb_rx_fb_user_info_id]);


              foreach($comment_list as $comment_info){

                  $comment_id        = $comment_info['id'];  
                  $comment_text      = $comment_info['message'];
             
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

                  $commenter_name    = isset($comment_info['from']['name']) ? $comment_info['from']['name'] : '';
                  $commenter_id  = isset($comment_info['from']['id']) ? $comment_info['from']['id'] : '';
                  $commenter_name_array    = explode(' ', $commenter_name);
                  $commenter_last_name = array_pop($commenter_name_array);
                  $commenter_first_name = implode(' ', $commenter_name_array);

                  $comment_time = $comment_info['created_time']['date'];

                  $auto_reply_private_message="";
                  // added by mostofa on 26-04-2017
                  $auto_reply_comment_message="";


                  // do not reply if the commenter is page itself
                  if($page_id==$commenter_id) continue;

                  // comment hide and delete section
                  $is_delete=0;
                  $is_hidden_success = 0;
                  $offensive_words_array = explode(',', $offensive_words);

                  foreach ($offensive_words_array as $key => $value)
                  {
                      if(function_exists('iconv') && function_exists('mb_detect_encoding'))
                      {
                          $encoded_offensive_word =  mb_detect_encoding($value);
                          if(isset($encoded_offensive_word)){
                             $value = strtolower(iconv( $encoded_offensive_word, "UTF-8//TRANSLIT", $value ));
                             $value = trim($value);
                          }
                      }

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
                        $pos = stripos($comment_text,trim($value));



                      if($pos !== FALSE)
                      {
                          if($is_delete_offensive == 'delete')
                          {
                              try{
                                  $comment_result_info=array(
                                      "comment_id" => $comment_id,
                                      "comment_text" =>$comment_text,
                                      "commenter_name"      =>$commenter_name,
                                      "commenter_id"      =>$commenter_id,
                                      "comment_time" =>$comment_time,
                                      "reply_time"   =>date("Y-m-d H:i:s"),
                                      "autoreply_table_id" => $post_column_id,
                                      "user_id" => $user_id
                                  );

                                  if($private_message_offensive_words != '')
                                  {
                                    if($structured_message == 'no')
                                    {
                                      $auto_reply_private_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$private_message_offensive_words);
                                      $auto_reply_private_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_private_message);
                                      $auto_reply_private_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_private_message);

                                      try{
                                          $send_reply_info=$this->fb_rx_login->send_private_reply($auto_reply_private_message,$comment_id,$post_access_token);
                                          $comment_result_info['reply_status']= "success";
                                          $comment_result_info['reply_text']= $auto_reply_private_message;
                                          $comment_result_info['reply_id']=isset($send_reply_info['id'])?$send_reply_info['id']:"";
                                          // increase auto reply count
                                          
                                      }catch(Exception $e)
                                      {
                                          $comment_result_info['reply_status']= $e->getMessage();
                                          $comment_result_info['reply_text']= $auto_reply_private_message;
                                          $comment_result_info['reply_id']="";
                                      }                                      
                                    }
                                    else
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

                                        $text_reply_unique_id = '';
                                        if(isset($reply_message['message']['text_reply_unique_id']))
                                        {
                                            $text_reply_unique_id=$reply_message['message']['text_reply_unique_id'];
                                            unset($reply_message['message']['text_reply_unique_id']);
                                        }


                                        if(isset($reply_message['message']['text']))
                                          $reply_message['message']['text']=spintax_process($reply_message['message']['text']);

                                        $reply_message['messaging_type'] = "RESPONSE";
                                        $reply_message=json_encode($reply_message);
                                        $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                                        $replace_with=array('{"comment_id":"'.$comment_id.'"}',$commenter_id);
                                        $reply_message=str_replace($replace_search, $replace_with, $reply_message);


                                        if(isset($commenter_first_name))
                                          $reply_message=str_replace('#LEAD_USER_FIRST_NAME#', $commenter_first_name, $reply_message);
                                        if(isset($commenter_last_name))
                                          $reply_message=str_replace('#LEAD_USER_LAST_NAME#', $commenter_last_name, $reply_message);

                                        $send_reply_info=$this->send_reply($post_access_token,$reply_message);

                                        if(isset($send_reply_info['error'])){
                                          $temp_result['reply_status'][$temp_counter]= $send_reply_info['error']['message'];
                                          $temp_result['reply_id'][$temp_counter]="";
                                          $temp_result['reply_text'][$temp_counter] = $private_message_offensive_words;
                                          $is_error = 1;
                                        }
                                        else{

                                          $temp_result['reply_status'][$temp_counter]= "success";
                                          $temp_result['reply_id'][$temp_counter]=isset($send_reply_info['message_id'])?$send_reply_info['message_id']:"";
                                          $temp_result['reply_text'][$temp_counter] = $private_message_offensive_words;
                                          $is_error = 0;
                                        } 
                                        $temp_counter++;

                                      } //end of foreach
                                      $comment_result_info['reply_text'] = json_encode($temp_result['reply_text']);
                                      $comment_result_info['reply_status'] = json_encode($temp_result['reply_status']);
                                      $comment_result_info['reply_id'] = json_encode($temp_result['reply_id']);
                                    }
                                      
                                  }
                                  else
                                  {
                                      $comment_result_info['reply_text'] = '';
                                      $comment_result_info['reply_status']= '';
                                      $comment_result_info['reply_id']="";
                                  }

                                  $this->fb_rx_login->delete_comment($comment_id,$post_access_token);
                          
                                  $comment_result_info['is_deleted'] = "1";  
                                  $comment_result_info['reply_type'] = "full_page_response";    
                                  
                                  $new_replied_info[0]=$comment_result_info;
                                  
                                  $deleted_comment_count++;
                                  $is_delete=1; 
                                  break;
                                
                              }
                              catch(Exception $e){
                                  
                              }
                          }
                          if($is_delete_offensive == 'hide')
                          {
                            try{
                                $this->fb_rx_login->hide_comment($comment_id,$post_access_token);
                                $is_hidden_success = 1;  
                            }
                            catch(Exception $e){

                            }
                          }
                      }
                  }

                  if($is_delete) continue;
                  // comment hide and delete section   

                  if($auto_reply_type=='generic'){
                      $auto_generic_reply__array=json_decode($auto_reply_private_message_raw,TRUE);

                      // image or video in comment section
                      $comment_image_link = $auto_generic_reply__array[0]['image_link'];
                      $comment_gif_link = '';
                      if($comment_image_link != '')
                      {
                          $image_link_array = explode('.', $comment_image_link);
                          $ext = array_pop($image_link_array);
                          if($ext != 'png' && $ext != 'PNG' && $ext != 'jpg' && $ext != 'JPG' && $ext != 'jpeg' && $ext != 'JPEG')
                          {
                              $comment_gif_link = $comment_image_link;
                              $comment_image_link = '';
                          }
                      }
                      $comment_video_link = $auto_generic_reply__array[0]['video_link'];
                      if($comment_video_link != '')
                      {                        
                          $comment_video_link = str_replace(base_url(), '', $auto_generic_reply__array[0]['video_link']);
                          $comment_video_link = FCPATH.$comment_video_link;
                      }
                      // image or video in comment section

                      if(is_array($auto_generic_reply__array))
                      {
                          $auto_generic_reply__array[0]['private_reply'] = $auto_generic_reply__array[0]['private_reply'];
                          $auto_generic_reply__array[0]['comment_reply'] = $auto_generic_reply__array[0]['comment_reply'];
                      }
                      else
                      {
                          $auto_generic_reply__array[0]['private_reply'] = $auto_reply_private_message_raw;
                          $auto_generic_reply__array[0]['comment_reply'] = "";
                      }

                      $auto_reply_private_message = $auto_generic_reply__array[0]['private_reply'];
                      if($structured_message == 'no')
                      {
                        $auto_reply_private_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$auto_generic_reply__array[0]['private_reply']);
                        $auto_reply_private_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_private_message);
                        $auto_reply_private_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_private_message);
                      }
                      // added by mostofa on 26-04-2017
                      $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$auto_generic_reply__array[0]['comment_reply']);
                      $auto_reply_comment_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_comment_message);
                      $auto_reply_comment_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_comment_message);
                      $auto_reply_comment_message = str_replace("#TAG_USER#","@[".$commenter_id."]",$auto_reply_comment_message);
                  }



                  if($auto_reply_type=="filter"){

                      $auto_reply_private_message_array=json_decode($auto_reply_private_message_raw,TRUE);    

                      foreach($auto_reply_private_message_array as $message_info){

                          $filter_word= $message_info['filter_word'];
                          $filter_word = explode(",",$filter_word);


                          foreach($filter_word as $f_word){

                              if(function_exists('iconv') && function_exists('mb_detect_encoding')){
                                  $encoded_word =  mb_detect_encoding($f_word);
                                  if(isset($encoded_word)){
                                      $f_word = strtolower(iconv( $encoded_word, "UTF-8//TRANSLIT", $f_word ));
                                      $f_word = trim($f_word);
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
                                $pos = stripos($comment_text,trim($f_word));

                              if($pos !== FALSE){
                                  // image or video in comment section
                                  $comment_image_link = $message_info['image_link'];
                                  $comment_gif_link = '';
                                  if($comment_image_link != '')
                                  {
                                      $image_link_array = explode('.', $comment_image_link);
                                      $ext = array_pop($image_link_array);
                                      if($ext != 'png' && $ext != 'PNG' && $ext != 'jpg' && $ext != 'JPG' && $ext != 'jpeg' && $ext != 'JPEG')
                                      {
                                          $comment_gif_link = $comment_image_link;
                                          $comment_image_link = '';
                                      }
                                  }
                                  $comment_video_link = $message_info['video_link'];
                                  if($comment_video_link != '')
                                  {
                                      $comment_video_link = str_replace(base_url(), '', $message_info['video_link']);
                                      $comment_video_link = FCPATH.$comment_video_link;
                                  }
                                  // image or video in comment section

                                  $auto_reply_private_message_individual = $message_info['reply_text'];
                                  $auto_reply_comment_message_individual = $message_info['comment_reply_text'];

                                  $auto_reply_private_message = $auto_reply_private_message_individual;
                                  if($structured_message == 'no')
                                  {
                                    $auto_reply_private_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$auto_reply_private_message_individual);
                                    $auto_reply_private_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_private_message);
                                    $auto_reply_private_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_private_message);
                                  }

                                  // added by mostofa on 26-04-2017
                                  $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$auto_reply_comment_message_individual);
                                  $auto_reply_comment_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_comment_message);
                                  $auto_reply_comment_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_comment_message);
                                  $auto_reply_comment_message = str_replace("#TAG_USER#","@[".$commenter_id."]",$auto_reply_comment_message);
                                  break;
                              }

                          }   

                          if($pos!==FALSE) break;

                      }

                      if($auto_reply_private_message==""){
                        $auto_reply_private_message = $default_reply_no_filter_private;
                        if($structured_message == 'no')
                        {
                          $auto_reply_private_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$default_reply_no_filter_private);
                          $auto_reply_private_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_private_message);
                          $auto_reply_private_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_private_message);
                        }
                      }

                      if($auto_reply_comment_message=='')
                      {
                          // image or video in comment section
                          $comment_image_link = $default_reply_no_filter_comment_image_link;
                          $comment_gif_link = '';
                          if($comment_image_link != '')
                          {
                              $image_link_array = explode('.', $comment_image_link);
                              $ext = array_pop($image_link_array);
                              if($ext != 'png' && $ext != 'PNG' && $ext != 'jpg' && $ext != 'JPG' && $ext != 'jpeg' && $ext != 'JPEG')
                              {
                                  $comment_gif_link = $comment_image_link;
                                  $comment_image_link = '';
                              }
                          }
                          $comment_video_link = $default_reply_no_filter_comment_video_link;
                          if($comment_video_link != '')
                          {
                              $comment_video_link = str_replace(base_url(), '', $default_reply_no_filter_comment_video_link);
                              $comment_video_link = FCPATH.$comment_video_link;
                          }
                          // image or video in comment section
                          // added by mostofa on 26-04-2017
                          $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$default_reply_no_filter_comment);
                          $auto_reply_comment_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_comment_message);
                          $auto_reply_comment_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_comment_message);
                          $auto_reply_comment_message = str_replace("#TAG_USER#","@[".$commenter_id."]",$auto_reply_comment_message);
                      }


                  }

                  $comment_result_info=array(
                      "comment_id" => $comment_id,
                      "comment_text" =>$comment_text,
                      "commenter_name" =>$commenter_name,
                      "commenter_id"      =>$commenter_id,
                      "comment_time" =>$comment_time,
                      "reply_time"   =>date("Y-m-d H:i:s"),
                      "autoreply_table_id" => $post_column_id,
                      "user_id" => $user_id
                      );
                  $auto_reply_comment_message = spintax_process($auto_reply_comment_message);
                  $auto_reply_private_message = spintax_process($auto_reply_private_message);
                  
                  // added by mostofa on 27-04-2017
                  $comment_result_info['comment_reply_text'] = $auto_reply_comment_message;                
                  $comment_result_info['reply_text'] = $auto_reply_private_message;

                  if($comment_reply_enabled == 'yes' && $auto_reply_comment_message!='')
                  {
                      try
                      {

                          $reply_info = $this->fb_rx_login->auto_comment($auto_reply_comment_message,$comment_id,$post_access_token,$comment_image_link,$comment_video_link,$comment_gif_link);

                          
                          $comment_result_info['reply_status_comment']= "success";
                          $comment_result_info['comment_reply_id']=isset($reply_info['id'])?$reply_info['id']:"";
                          
                          $auto_comment_reply_count++;
                          if($hide_comment_after_comment_reply == 'yes')
                          {
                              try{
                                  $this->fb_rx_login->hide_comment($comment_id,$post_access_token);
                                  $is_hidden_success = 1; 
                                  $hidden_comment_count++;
                              }catch(Exception $e){

                              }
                          }
                      }
                      catch(Exception $e){
                          $comment_result_info['reply_status_comment']= $e->getMessage();
                      }
                  }


                  try{

                      if($auto_reply_private_message!=""){
                        if($structured_message == 'no')
                        {
                          $send_reply_info=$this->fb_rx_login->send_private_reply($auto_reply_private_message,$comment_id,$post_access_token);

                          if(isset($send_reply_info['error'])){
                              $comment_result_info['reply_status']= $send_reply_info['error']['message'];
                              $comment_result_info['reply_id']="";
                          }
                          else{

                              $comment_result_info['reply_status']= "success";
                              $comment_result_info['reply_id']=isset($send_reply_info['id'])?$send_reply_info['id']:"";
                          }
                        }
                        else
                        {
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

                            $text_reply_unique_id = '';
                            if(isset($reply_message['message']['text_reply_unique_id']))
                            {
                                $text_reply_unique_id=$reply_message['message']['text_reply_unique_id'];
                                unset($reply_message['message']['text_reply_unique_id']);
                            }


                            if(isset($reply_message['message']['text']))
                              $reply_message['message']['text']=spintax_process($reply_message['message']['text']);

                            $reply_message['messaging_type'] = "RESPONSE";
                            $reply_message=json_encode($reply_message);
                            $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                            $replace_with=array('{"comment_id":"'.$comment_id.'"}',$commenter_id);
                            $reply_message=str_replace($replace_search, $replace_with, $reply_message);

                            if(isset($commenter_first_name))
                              $reply_message=str_replace('#LEAD_USER_FIRST_NAME#', $commenter_first_name, $reply_message);
                            if(isset($commenter_last_name))
                              $reply_message=str_replace('#LEAD_USER_LAST_NAME#', $commenter_last_name, $reply_message);

                            $send_reply_info=$this->send_reply($post_access_token,$reply_message);

                            if(isset($send_reply_info['error'])){
                                $temp_result['reply_status'][$temp_counter]= $send_reply_info['error']['message'];
                                $temp_result['reply_id'][$temp_counter]="";
                                $temp_result['reply_text'][$temp_counter] = $auto_reply_private_message;
                                $is_error = 1;
                            }
                            else{

                                $temp_result['reply_status'][$temp_counter]= "success";
                                $temp_result['reply_id'][$temp_counter]=isset($send_reply_info['message_id'])?$send_reply_info['message_id']:"";
                                $temp_result['reply_text'][$temp_counter] = $auto_reply_private_message;
                                $is_error = 0;
                            }

                            $temp_counter++;

                          } //end of foreach
                          $comment_result_info['reply_text'] = json_encode($temp_result['reply_text']);
                          $comment_result_info['reply_status'] = json_encode($temp_result['reply_status']);
                          $comment_result_info['reply_id'] = json_encode($temp_result['reply_id']);
                        }
                      }

                      else{
                          $comment_result_info['reply_status']= "Not Replied ! No match found corresponding filter words";
                          $comment_result_info['reply_id']="";
                      }
                  }

                  catch(Exception $e){
                      $comment_result_info['reply_status']= $e->getMessage();
                      $comment_result_info['reply_id']="";
                  }


                  // added by mostofa on 26-04-2017 for comment reply
                  if($auto_like_comment == 'yes')
                  {
                      try
                      {
                          $this->fb_rx_login->auto_like($comment_id,$post_access_token);
                      }
                      catch(Exception $e){

                      }

                  }
                  if($is_hidden_success == 1)
                    $comment_result_info['is_hidden'] = "1";
                  

                  $comment_result_info['reply_type'] = "full_page_response";
                  $new_replied_info[0]=$comment_result_info;

              }


              skipped:
              /*****  Update post *****/    
              $update_data = array("auto_private_reply_status"=>"0",
                  "last_reply_time" => date("Y-m-d H:i:s"),
                  "hidden_comment_count" => $hidden_comment_count,
                  "deleted_comment_count" => $deleted_comment_count,
                  "auto_comment_reply_count" => $auto_comment_reply_count
                  );
              if($skip_error_message != '')
              {
                  $update_data['auto_private_reply_status'] = '1';
                  $update_data['error_message'] = $skip_error_message;
              }

              $this->basic->update_data("page_response_report",array("id"=>$post_column_id),$update_data);
              $this->db->insert_batch('facebook_ex_autoreply_report', $new_replied_info); 


              //Assaign Label for that users & insert into subscriber table if isn't subscriber yet. Insert subscriber id & name into database. 
              $private_reply_message_ids=$comment_result_info['reply_id']; 
              //find the information of the message to get the commenter PSID
              if(isset($private_reply_message_ids) && $private_reply_message_ids!="")
              {

                If($structured_message=='yes'){
                    $private_reply_message_id_array=json_decode($private_reply_message_ids,TRUE);
                    $private_reply_first_message_id= isset($private_reply_message_id_array[0]) ? $private_reply_message_id_array[0]:"";
                }
                else
                    $private_reply_first_message_id= $private_reply_message_ids;

                if($private_reply_first_message_id!="")
                    $subscriber_info=$this->fb_rx_login->get_private_reply_message_id_info($private_reply_first_message_id,$post_access_token);


                $subscriber_id=isset($subscriber_info['to'][0]['id']) ? $subscriber_info['to'][0]['id']:"";
                $subsciber_name=isset($subscriber_info['to'][0]['name']) ? $subscriber_info['to'][0]['name']: "";

                if($subscriber_id!="")
                {
                  //refferer_id = Post_id,refferer_source="COMMENT PRIVATE REPLY",refferer_uri="Comment_id with facebook "
                  $subsciber_name=$this->db->escape($subsciber_name);
                  $refferer_uri="https://facebook.com/{$comment_id}";
                  $subscriber_time = date("Y-m-d H:i:s"); 


                  $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscriber(user_id,page_table_id,page_id,subscribe_id,full_name,refferer_id,refferer_source,refferer_uri,subscribed_at,is_bot_subscriber) 
                  VALUES('$user_id','$page_table_id','$page_id','$subscriber_id',$subsciber_name,'$post_id','COMMENT PRIVATE REPLY','$refferer_uri','$subscriber_time','0');");

                  if($text_reply_unique_id != '')
                  {
                    $message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$subscriber_id',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
                    $this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
                  }
               }

              }

              exit();
              
          }
        }  



        if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'photo')
        {
          if(isset($response['entry'][0]['changes'][0]['value']['verb']) && $response['entry'][0]['changes'][0]['value']['verb'] == 'edited')
            exit;
          goto autolike_share;
        }
        
        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'status') 
          goto autolike_share;
        else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'share')
        {
          $share_link = $response['entry'][0]['changes'][0]['value']['link'];

          if(stripos($share_link, 'https://web.facebook.com/') !== false || stripos($share_link, 'http://web.facebook.com/') !== false)
          exit();

          $first_8_digit = substr($share_link, 0, 7);
          if(stripos($first_8_digit,'https') !== false || stripos($first_8_digit,'http') !== false)
            goto autolike_share;
          else
            exit();
        }
      
        autolike_share :

        $page_id = $response['entry'][0]['id'];
        $post_id = $response['entry'][0]['changes'][0]['value']['post_id'];
        $page_response_auto_like_share_campaign = $this->basic->get_data('page_response_auto_like_share',array('where'=>array('page_id'=>$page_id)));
        $insert_data = array();
        if(!empty($page_response_auto_like_share_campaign))
        {
            foreach($page_response_auto_like_share_campaign as $info)
            {
                foreach($info as $key=>$value)
                {
                    $$key = $value;
                    $insert_data[$key] = $$key;
                }
            }
            unset($insert_data['id']);
            $insert_data['page_response_auto_like_share_id'] = $id;
            $insert_data['post_id'] = $post_id;
            $insert_data['auto_share_report'] = json_encode(array());
            $insert_data['auto_like_report'] = json_encode(array());
            $insert_data['share_count'] = isset($insert_data['auto_share_this_post_by_pages']) ? count(json_decode($insert_data['auto_share_this_post_by_pages'],true)) : 0;
            $insert_data['like_count'] = isset($insert_data['auto_like_this_post_by_pages']) ? count(json_decode($insert_data['auto_like_this_post_by_pages'],true)) : 0;

           // file_put_contents("test1.txt",json_encode($insert_data), FILE_APPEND | LOCK_EX);

            $this->basic->insert_data('page_response_auto_like_share_report',$insert_data);
            $page_response_auto_like_share_report_id = $this->db->insert_id();
        }
        $insert_data = array();
        if(!empty($page_response_auto_like_share_campaign) && $page_response_auto_like_share_campaign[0]['auto_share_this_post_by_pages'] != "[]" && isset($page_response_auto_like_share_report_id))
        {
            $auto_share_page_ids = json_decode($page_response_auto_like_share_campaign[0]['auto_share_this_post_by_pages'],true);
            foreach($auto_share_page_ids as $value)
            {
                $temp = array();
                $temp['page_response_auto_like_share_report_id'] = $page_response_auto_like_share_report_id;
                $temp['user_id'] = $page_response_auto_like_share_campaign[0]['user_id'];
                $temp['auto_share_page_table_id'] = $value;
                array_push($insert_data, $temp);
            }
            $this->db->insert_batch('page_response_auto_share_report',$insert_data);
        }
        $insert_data = array();
        if(!empty($page_response_auto_like_share_campaign) && $page_response_auto_like_share_campaign[0]['auto_like_this_post_by_pages'] != "[]" && isset($page_response_auto_like_share_report_id))
        {
            $auto_share_page_ids = json_decode($page_response_auto_like_share_campaign[0]['auto_like_this_post_by_pages'],true);
            foreach($auto_share_page_ids as $value)
            {
                $temp = array();
                $temp['page_response_auto_like_share_report_id'] = $page_response_auto_like_share_report_id;
                $temp['user_id'] = $page_response_auto_like_share_campaign[0]['user_id'];
                $temp['auto_like_page_table_id'] = $value;
                array_push($insert_data, $temp);
            }
            $this->db->insert_batch('page_response_auto_like_report',$insert_data);
        }
    }


    public function send_autoreply_with_postid($post_table_id,$comment_list='')
    {
        if($this->is_demo == '1')
          $where['where']=array("facebook_ex_autoreply.id"=>$post_table_id,"facebook_ex_autoreply.user_id !="=>1,"auto_private_reply_status !="=>'2','user_type !=' => 'Admin');
        else            
          $where['where']=array("facebook_ex_autoreply.id"=>$post_table_id,"auto_private_reply_status !="=>'2');

        $select="facebook_ex_autoreply.id as column_id,post_id,page_id,page_access_token,auto_reply_text,facebook_ex_autoreply.facebook_rx_fb_user_info_id,multiple_reply,comment_reply_enabled,reply_type,auto_like_comment,nofilter_word_found_text,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,hidden_comment_count,deleted_comment_count,auto_comment_reply_count,users.deleted as user_deleted,users.status as user_status, users.expired_date as expired_date, users.user_type as user_type, users.id as user_id,broadcaster_labels,facebook_ex_autoreply.page_info_table_id,structured_message,trigger_matching_type";

        $join=array(
            'facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=facebook_ex_autoreply.page_info_table_id,left",
            'users' => 'facebook_ex_autoreply.user_id=users.id,left'
            );

        $post_info= $this->basic->get_data("facebook_ex_autoreply",$where,$select,$join);

        if(empty($post_info)) exit; 

        $label_ids=isset($post_info[0]['broadcaster_labels']) ? $post_info[0]['broadcaster_labels']:""; 
        $page_table_id= isset($post_info[0]['page_info_table_id']) ? $post_info[0]['page_info_table_id']:""; 
        $user_id= isset($post_info[0]['user_id']) ? $post_info[0]['user_id']:""; 


        /***    Start Sending Private reply ****/
        $config_id_database=array();
        // setting fb confid id for library call
        $this->load->library("fb_rx_login");

        foreach($post_info as $info){

            if(isset($info['user_type']) && $info['user_type'] != 'Admin')
            {
                $user_status = $info['user_status'];
                $user_deleted = $info['user_deleted'];
                if($user_deleted == '1' || $user_status == '0') continue;

                // if(!$this->api_member_validity($info['user_id'])) continue;         
            }

            $structured_message = $info['structured_message'];
            $trigger_matching_type = $info['trigger_matching_type'];
            /***    get all comment from post **/
            $auto_like_comment = $info['auto_like_comment'];
            $post_id=   $info['post_id'];
            $page_id = $info['page_id'];
            $post_access_token = $info['page_access_token'];

            // comment hide and delete section
            $private_message_offensive_words = $info['private_message_offensive_words'];
            $hidden_comment_count = 0;
            $deleted_comment_count = 0;
            $auto_comment_reply_count = 0;
            $hidden_comment_count = $info['hidden_comment_count'];
            $deleted_comment_count = $info['deleted_comment_count'];
            $auto_comment_reply_count = $info['auto_comment_reply_count'];
            $hide_comment_after_comment_reply = $info['hide_comment_after_comment_reply'];
            $is_delete_offensive = $info['is_delete_offensive'];
            $offensive_words = $info['offensive_words'];
            // comment hide and delete section
 
            $auto_reply_private_message_raw= $info['auto_reply_text'];
            $auto_reply_type= $info['reply_type'];

            $default_reply_no_filter = json_decode($info['nofilter_word_found_text'],true);
            if(is_array($default_reply_no_filter))
            {
                $default_reply_no_filter_comment = $default_reply_no_filter[0]['comment_reply'];
                $default_reply_no_filter_private = $default_reply_no_filter[0]['private_reply'];
                $default_reply_no_filter_comment_image_link = $default_reply_no_filter[0]['image_link'];
                $default_reply_no_filter_comment_video_link = $default_reply_no_filter[0]['video_link'];
            }
            else
            {
                $default_reply_no_filter_comment = "";
                $default_reply_no_filter_private = $info['nofilter_word_found_text'];
            }


            $comment_reply_enabled = $info['comment_reply_enabled'];
            $multiple_reply = $info['multiple_reply'];

            if($multiple_reply == 'no')
            { 
              $commenter_id_check_mutliple  = isset($comment_list[0]['from']['id']) ? $comment_list[0]['from']['id'] : '';
              $already_replied_commenter_id = $this->basic->get_data('facebook_ex_autoreply_report',array('where'=>array('commenter_id'=>$commenter_id_check_mutliple,'autoreply_table_id'=>$info['column_id'])));

              if(!empty($already_replied_commenter_id)) exit; 
            }

            // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            
            $skip_error_message = '';
            $post_column_id= $info['column_id'];
           
            $new_replied_info=array();
  
            if($config_id_database[$fb_rx_fb_user_info_id] == 0)
            {
                $skip_error_message = "Corresponding Facebook account has been removed from database";
                goto skipped;
            }


            // setting fb confid id for library call
            $this->fb_rx_login->app_initialize($config_id_database[$fb_rx_fb_user_info_id]);


            foreach($comment_list as $comment_info){
                $comment_id        = $comment_info['id'];   
                //$comment_parent_id  = isset($comment_info['comment_parent_id']) ? $comment_info['comment_parent_id']: "";

                $comment_text      = $comment_info['message'];
                // if(function_exists('iconv') && function_exists('mb_detect_encoding')){

                //     $encoded_comment =  mb_detect_encoding($comment_text);
                //     if(isset($encoded_comment)){
                //         $comment_text = iconv( $encoded_comment, "UTF-8//TRANSLIT", $comment_text );
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

                $commenter_name    = isset($comment_info['from']['name']) ? $comment_info['from']['name'] : '';
                $commenter_id  = isset($comment_info['from']['id']) ? $comment_info['from']['id'] : '';
                $commenter_name_array    = explode(' ', $commenter_name);
                $commenter_last_name = array_pop($commenter_name_array);
                $commenter_first_name = implode(' ', $commenter_name_array);

                $comment_time = $comment_info['created_time']['date'];

                $auto_reply_private_message="";
                // added by mostofa on 26-04-2017
                $auto_reply_comment_message="";


                // do not reply if the commenter is page itself
                if($page_id==$commenter_id) continue;

                // comment hide and delete section
                $is_delete=0;
                $is_hidden_success = 0;
                $offensive_words_array = explode(',', $offensive_words);
                foreach ($offensive_words_array as $key => $value)
                {
                    if(function_exists('iconv') && function_exists('mb_detect_encoding'))
                    {
                        $encoded_offensive_word =  mb_detect_encoding($value);
                        if(isset($encoded_offensive_word)){
                           $value = strtolower(iconv( $encoded_offensive_word, "UTF-8//TRANSLIT", $value ));
                           $value = trim($value);
                        }
                    }

                    if($trigger_matching_type == 'exact')
                    {
                      $search_array = [];
                      $temp_cam_keywords_array = [];
                      $temp_cam_keywords_array = explode(" ", $value);
                      if(count($temp_cam_keywords_array) == 1) $search_array = $single_words_from_message_array;
                      else if(count($temp_cam_keywords_array) == 2) $search_array = $twowords_from_message_array;
                      else if(count($temp_cam_keywords_array) == 3) $search_array = $three_words_from_message_array;

                      if(in_array($cam_keywords, $search_array))
                          $matches = TRUE;
                      else $matches = FALSE;
                    }
                    else
                      $matches = stripos($comment_text,trim($value));



                    if($matches !== FALSE)
                    {
                        if($is_delete_offensive == 'delete')
                        {
                            try{
                                $comment_result_info=array(
                                    "comment_id" => $comment_id,
                                    "comment_text" =>$comment_text,
                                    "commenter_name"      =>$commenter_name,
                                    "commenter_id"      =>$commenter_id,
                                    "comment_time" =>$comment_time,
                                    "reply_time"   =>date("Y-m-d H:i:s"),
                                    "autoreply_table_id" => $post_table_id,
                                    "user_id" => $user_id
                                );

                                if($private_message_offensive_words != '')
                                {
                                  if($structured_message == 'no')
                                  {
                                    $auto_reply_private_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$private_message_offensive_words);
                                    $auto_reply_private_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_private_message);
                                    $auto_reply_private_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_private_message);

                                    try{
                                        $send_reply_info=$this->fb_rx_login->send_private_reply($auto_reply_private_message,$comment_id,$post_access_token);
                                        $comment_result_info['reply_status']= "success";
                                        $comment_result_info['reply_text']= $auto_reply_private_message;
                                        $comment_result_info['reply_id']=isset($send_reply_info['id'])?$send_reply_info['id']:"";
                                        // increase auto reply count
                                        
                                    }catch(Exception $e)
                                    {
                                        $comment_result_info['reply_status']= $e->getMessage();
                                        $comment_result_info['reply_text']= $auto_reply_private_message;
                                        $comment_result_info['reply_id']="";
                                    }                                    
                                  }
                                  else
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

                                      $text_reply_unique_id = '';
                                      if(isset($reply_message['message']['text_reply_unique_id']))
                                      {
                                          $text_reply_unique_id=$reply_message['message']['text_reply_unique_id'];
                                          unset($reply_message['message']['text_reply_unique_id']);
                                      }


                                      if(isset($reply_message['message']['text']))
                                        $reply_message['message']['text']=spintax_process($reply_message['message']['text']);

                                      $reply_message['messaging_type'] = "RESPONSE";
                                      $reply_message=json_encode($reply_message);
                                      $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                                      $replace_with=array('{"comment_id":"'.$comment_id.'"}',$commenter_id);
                                      $reply_message=str_replace($replace_search, $replace_with, $reply_message);

                                      if(isset($commenter_first_name))
                                        $reply_message=str_replace('#LEAD_USER_FIRST_NAME#', $commenter_first_name, $reply_message);
                                      if(isset($commenter_last_name))
                                        $reply_message=str_replace('#LEAD_USER_LAST_NAME#', $commenter_last_name, $reply_message);

                                      $send_reply_info=$this->send_reply($post_access_token,$reply_message);

                                      if(isset($send_reply_info['error'])){
                                        $temp_result['reply_status'][$temp_counter]= $send_reply_info['error']['message'];
                                        $temp_result['reply_id'][$temp_counter]="";
                                        $temp_result['reply_text'][$temp_counter] = $private_message_offensive_words;
                                        $is_error = 1;
                                      }
                                      else{
                                        $temp_result['reply_status'][$temp_counter]= "success";
                                        $temp_result['reply_id'][$temp_counter]=isset($send_reply_info['message_id'])?$send_reply_info['message_id']:"";
                                        $temp_result['reply_text'][$temp_counter] = $private_message_offensive_words;
                                        $is_error = 0;
                                      }

                                      $temp_counter++;

                                    } //end of foreach
                                    $comment_result_info['reply_text'] = json_encode($temp_result['reply_text']);
                                    $comment_result_info['reply_status'] = json_encode($temp_result['reply_status']);
                                    $comment_result_info['reply_id'] = json_encode($temp_result['reply_id']);
                                  }
                                    
                                }
                                else
                                {
                                    $comment_result_info['reply_text'] = '';
                                    $comment_result_info['reply_status']= '';
                                    $comment_result_info['reply_id']="";
                                }

                                $this->fb_rx_login->delete_comment($comment_id,$post_access_token);
                        
                                $comment_result_info['is_deleted'] = "1";      
                                
                                $new_replied_info[0]=$comment_result_info;
                                
                                $deleted_comment_count++;
                                $is_delete=1; 
                                break;
                              
                            }
                            catch(Exception $e){
                                
                            }
                        }
                        if($is_delete_offensive == 'hide')
                        {
                          try{
                              $this->fb_rx_login->hide_comment($comment_id,$post_access_token);
                              $is_hidden_success = 1;  
                          }
                          catch(Exception $e){

                          }
                        }
                    }
                }

                if($is_delete) continue;
                // comment hide and delete section   

                if($auto_reply_type=='generic'){
                    $auto_generic_reply__array=json_decode($auto_reply_private_message_raw,TRUE);

                    // image or video in comment section
                    $comment_image_link = $auto_generic_reply__array[0]['image_link'];
                    $comment_gif_link = '';
                    if($comment_image_link != '')
                    {
                        $image_link_array = explode('.', $comment_image_link);
                        $ext = array_pop($image_link_array);
                        if($ext != 'png' && $ext != 'PNG' && $ext != 'jpg' && $ext != 'JPG' && $ext != 'jpeg' && $ext != 'JPEG')
                        {
                            $comment_gif_link = $comment_image_link;
                            $comment_image_link = '';
                        }
                    }
                    $comment_video_link = $auto_generic_reply__array[0]['video_link'];
                    if($comment_video_link != '')
                    {                        
                        $comment_video_link = str_replace(base_url(), '', $auto_generic_reply__array[0]['video_link']);
                        $comment_video_link = FCPATH.$comment_video_link;
                    }
                    // image or video in comment section

                    if(is_array($auto_generic_reply__array))
                    {
                        $auto_generic_reply__array[0]['private_reply'] = $auto_generic_reply__array[0]['private_reply'];
                        $auto_generic_reply__array[0]['comment_reply'] = $auto_generic_reply__array[0]['comment_reply'];
                    }
                    else
                    {
                        $auto_generic_reply__array[0]['private_reply'] = $auto_reply_private_message_raw;
                        $auto_generic_reply__array[0]['comment_reply'] = "";
                    }
                    $auto_reply_private_message = $auto_generic_reply__array[0]['private_reply'];
                    if($structured_message == 'no')
                    {
                      $auto_reply_private_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$auto_generic_reply__array[0]['private_reply']);
                      $auto_reply_private_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_private_message);
                      $auto_reply_private_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_private_message);
                    }
                    // added by mostofa on 26-04-2017
                    $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$auto_generic_reply__array[0]['comment_reply']);
                    $auto_reply_comment_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_comment_message);
                    $auto_reply_comment_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_comment_message);
                    $auto_reply_comment_message = str_replace("#TAG_USER#","@[".$commenter_id."]",$auto_reply_comment_message);
                }



                if($auto_reply_type=="filter"){

                    $auto_reply_private_message_array=json_decode($auto_reply_private_message_raw,TRUE);    

                    foreach($auto_reply_private_message_array as $message_info){

                        $filter_word= $message_info['filter_word'];
                        $filter_word = explode(",",$filter_word);


                        foreach($filter_word as $f_word){

                            if(function_exists('iconv') && function_exists('mb_detect_encoding')){
                                $encoded_word =  mb_detect_encoding($f_word);
                                if(isset($encoded_word)){
                                    $f_word = strtolower(iconv( $encoded_word, "UTF-8//TRANSLIT", $f_word ));
                                    $f_word = trim($f_word);
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
                              $pos = stripos($comment_text,trim($f_word));

                            if($pos !== FALSE){
                                // image or video in comment section
                                $comment_image_link = $message_info['image_link'];
                                $comment_gif_link = '';
                                if($comment_image_link != '')
                                {
                                    $image_link_array = explode('.', $comment_image_link);
                                    $ext = array_pop($image_link_array);
                                    if($ext != 'png' && $ext != 'PNG' && $ext != 'jpg' && $ext != 'JPG' && $ext != 'jpeg' && $ext != 'JPEG')
                                    {
                                        $comment_gif_link = $comment_image_link;
                                        $comment_image_link = '';
                                    }
                                }
                                $comment_video_link = $message_info['video_link'];
                                if($comment_video_link != '')
                                {
                                    $comment_video_link = str_replace(base_url(), '', $message_info['video_link']);
                                    $comment_video_link = FCPATH.$comment_video_link;
                                }
                                // image or video in comment section

                                $auto_reply_private_message_individual = $message_info['reply_text'];
                                $auto_reply_comment_message_individual = $message_info['comment_reply_text'];

                                $auto_reply_private_message = $auto_reply_private_message_individual;

                                if($structured_message == 'no')
                                {
                                  $auto_reply_private_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$auto_reply_private_message_individual);
                                  $auto_reply_private_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_private_message);
                                  $auto_reply_private_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_private_message);
                                }

                                // added by mostofa on 26-04-2017
                                $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$auto_reply_comment_message_individual);
                                $auto_reply_comment_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_comment_message);
                                $auto_reply_comment_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_comment_message);
                                $auto_reply_comment_message = str_replace("#TAG_USER#","@[".$commenter_id."]",$auto_reply_comment_message);
                                break;
                            }

                        }   

                        if($pos!==FALSE) break;

                    }

                    if($auto_reply_private_message=="")
                    {
                      $auto_reply_private_message = $default_reply_no_filter_private;
                      if($structured_message == 'no')
                      {
                        $auto_reply_private_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$default_reply_no_filter_private);
                        $auto_reply_private_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_private_message);
                        $auto_reply_private_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_private_message);
                      }
                    }

                    if($auto_reply_comment_message=='')
                    {
                        // image or video in comment section
                        $comment_image_link = $default_reply_no_filter_comment_image_link;
                        $comment_gif_link = '';
                        if($comment_image_link != '')
                        {
                            $image_link_array = explode('.', $comment_image_link);
                            $ext = array_pop($image_link_array);
                            if($ext != 'png' && $ext != 'PNG' && $ext != 'jpg' && $ext != 'JPG' && $ext != 'jpeg' && $ext != 'JPEG')
                            {
                                $comment_gif_link = $comment_image_link;
                                $comment_image_link = '';
                            }
                        }
                        $comment_video_link = $default_reply_no_filter_comment_video_link;
                        if($comment_video_link != '')
                        {
                            $comment_video_link = str_replace(base_url(), '', $default_reply_no_filter_comment_video_link);
                            $comment_video_link = FCPATH.$comment_video_link;
                        }
                        // image or video in comment section
                        // added by mostofa on 26-04-2017
                        $auto_reply_comment_message = str_replace('#LEAD_USER_NAME#',$commenter_name,$default_reply_no_filter_comment);
                        $auto_reply_comment_message = str_replace("#LEAD_USER_FIRST_NAME#",$commenter_first_name,$auto_reply_comment_message);
                        $auto_reply_comment_message = str_replace("#LEAD_USER_LAST_NAME#",$commenter_last_name,$auto_reply_comment_message);
                        $auto_reply_comment_message = str_replace("#TAG_USER#","@[".$commenter_id."]",$auto_reply_comment_message);
                    }




                }


                $comment_result_info=array(
                    "comment_id" => $comment_id,
                    "comment_text" =>$comment_text,
                    "commenter_name" =>$commenter_name,
                    "commenter_id"      =>$commenter_id,
                    "comment_time" =>$comment_time,
                    "reply_time"   =>date("Y-m-d H:i:s"),
                    "autoreply_table_id" => $post_table_id,
                    "user_id" => $user_id
                    );
                $auto_reply_comment_message = spintax_process($auto_reply_comment_message);
                $auto_reply_private_message = spintax_process($auto_reply_private_message);
                
                // added by mostofa on 27-04-2017
                $comment_result_info['comment_reply_text'] = $auto_reply_comment_message;                
                $comment_result_info['reply_text'] = $auto_reply_private_message;

                if($comment_reply_enabled == 'yes' && $auto_reply_comment_message!='')
                {
                    try
                    {
                        $reply_info = $this->fb_rx_login->auto_comment($auto_reply_comment_message,$comment_id,$post_access_token,$comment_image_link,$comment_video_link,$comment_gif_link);

                        $comment_result_info['reply_status_comment']= "success";
                        $comment_result_info['comment_reply_id']=isset($reply_info['id'])?$reply_info['id']:"";
                        
                        $auto_comment_reply_count++;
                        if($hide_comment_after_comment_reply == 'yes')
                        {
                            try{
                                $this->fb_rx_login->hide_comment($comment_id,$post_access_token);
                                $is_hidden_success = 1; 
                                $hidden_comment_count++;
                            }catch(Exception $e){

                            }
                        }
                    }
                    catch(Exception $e){
                        $comment_result_info['reply_status_comment']= $e->getMessage();
                    }
                }


                try{

                    if($auto_reply_private_message!=""){
                      if($structured_message == 'no')
                      {
                        $send_reply_info=$this->fb_rx_login->send_private_reply($auto_reply_private_message,$comment_id,$post_access_token);

                        if(isset($send_reply_info['error'])){
                            $comment_result_info['reply_status']= $send_reply_info['error']['message'];
                            $comment_result_info['reply_id']="";
                        }
                        else{

                            $comment_result_info['reply_status']= "success";
                            $comment_result_info['reply_id']=isset($send_reply_info['id'])?$send_reply_info['id']:"";
                        }
                      }
                      else
                      {
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

                          $text_reply_unique_id = '';
                          if(isset($reply_message['message']['text_reply_unique_id']))
                          {
                              $text_reply_unique_id=$reply_message['message']['text_reply_unique_id'];
                              unset($reply_message['message']['text_reply_unique_id']);
                          }


                          if(isset($reply_message['message']['text']))
                            $reply_message['message']['text']=spintax_process($reply_message['message']['text']);

                          $reply_message['messaging_type'] = "RESPONSE";
                          $reply_message=json_encode($reply_message);
                          $replace_search=array('{"id":"replace_id"}','#SUBSCRIBER_ID_REPLACE#');
                          $replace_with=array('{"comment_id":"'.$comment_id.'"}',$commenter_id);
                          $reply_message=str_replace($replace_search, $replace_with, $reply_message);

                          if(isset($commenter_first_name))
                            $reply_message=str_replace('#LEAD_USER_FIRST_NAME#', $commenter_first_name, $reply_message);
                          if(isset($commenter_last_name))
                            $reply_message=str_replace('#LEAD_USER_LAST_NAME#', $commenter_last_name, $reply_message);

                          $send_reply_info=$this->send_reply($post_access_token,$reply_message);

                          if(isset($send_reply_info['error'])){
                            $temp_result['reply_status'][$temp_counter]= $send_reply_info['error']['message'];
                            $temp_result['reply_id'][$temp_counter]="";
                            $temp_result['reply_text'][$temp_counter] = $auto_reply_private_message;
                            $is_error = 1;
                          }
                          else{
                            $temp_result['reply_status'][$temp_counter]= "success";
                            $temp_result['reply_id'][$temp_counter]=isset($send_reply_info['message_id'])?$send_reply_info['message_id']:"";
                            $temp_result['reply_text'][$temp_counter] = $auto_reply_private_message;
                            $is_error = 0;
                          }

                          $temp_counter++;

                        } //end of foreach
                        $comment_result_info['reply_text'] = json_encode($temp_result['reply_text']);
                        $comment_result_info['reply_status'] = json_encode($temp_result['reply_status']);
                        $comment_result_info['reply_id'] = json_encode($temp_result['reply_id']);
                      }
                    }
                    else{
                        $comment_result_info['reply_status']= "Not Replied ! No match found corresponding filter words";
                        $comment_result_info['reply_id']="";
                    }
                }

                catch(Exception $e){
                    $comment_result_info['reply_status']= $e->getMessage();
                    $comment_result_info['reply_id']="";
                }


                // added by mostofa on 26-04-2017 for comment reply
                if($auto_like_comment == 'yes')
                {
                    try
                    {
                        $this->fb_rx_login->auto_like($comment_id,$post_access_token);
                    }
                    catch(Exception $e){

                    }

                }
                if($is_hidden_success == 1)
                  $comment_result_info['is_hidden'] = "1";
                
                $new_replied_info[0]=$comment_result_info;

            }


            skipped:
            /*****  Update post *****/    
            $update_data = array("auto_private_reply_status"=>"0",
                "last_reply_time" => date("Y-m-d H:i:s"),
                "hidden_comment_count" => $hidden_comment_count,
                "deleted_comment_count" => $deleted_comment_count,
                "auto_comment_reply_count" => $auto_comment_reply_count
                );
            if($skip_error_message != '')
            {
                $update_data['auto_private_reply_status'] = '1';
                $update_data['error_message'] = $skip_error_message;
            }

            $this->basic->update_data("facebook_ex_autoreply",array("id"=>$post_column_id),$update_data);
            $this->db->insert_batch('facebook_ex_autoreply_report', $new_replied_info); 


            //Assaign Label for that users & insert into subscriber table if isn't subscriber yet. Insert subscriber id & name into database. 
            $private_reply_message_ids=$comment_result_info['reply_id']; 
            //find the information of the message to get the commenter PSID
            if(isset($private_reply_message_ids) && $private_reply_message_ids!="")
            {

              If($structured_message=='yes'){
                  $private_reply_message_id_array=json_decode($private_reply_message_ids,TRUE);
                  $private_reply_first_message_id= isset($private_reply_message_id_array[0]) ? $private_reply_message_id_array[0]:"";
              }
              else
                  $private_reply_first_message_id= $private_reply_message_ids;

              if($private_reply_first_message_id!="")
                  $subscriber_info=$this->fb_rx_login->get_private_reply_message_id_info($private_reply_first_message_id,$post_access_token);


              $subscriber_id=isset($subscriber_info['to'][0]['id']) ? $subscriber_info['to'][0]['id']:"";
              $subsciber_name=isset($subscriber_info['to'][0]['name']) ? $subscriber_info['to'][0]['name']: "";

              if($subscriber_id!="")
              {
                //refferer_id = Post_id,refferer_source="COMMENT PRIVATE REPLY",refferer_uri="Comment_id with facebook "
                $subsciber_name=$this->db->escape($subsciber_name);
                $refferer_uri="https://facebook.com/{$comment_id}";
                $subscriber_time = date("Y-m-d H:i:s"); 


                $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscriber(user_id,page_table_id,page_id,subscribe_id,full_name,refferer_id,refferer_source,refferer_uri,subscribed_at,is_bot_subscriber) 
                VALUES('$user_id','$page_table_id','$page_id','$subscriber_id',$subsciber_name,'$post_id','COMMENT PRIVATE REPLY','$refferer_uri','$subscriber_time','0');");

                $subscriber_table_id = $this->db->insert_id();

                if($label_ids!=""){

                  //DEPRECATED FUNCTION FOR QUICK BROADCAST// 
                  $post_data_label_assign=array("psid"=>$subscriber_id,"fb_page_id"=>$page_id,"label_auto_ids"=>$label_ids,'subscriber_table_id'=>$subscriber_table_id);
                  $url=base_url()."home/assign_label_webhook_call";
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_URL, $url);
                  curl_setopt($ch,CURLOPT_POST,1);
                  curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_label_assign);
                  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                  // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                  $reply_response=curl_exec($ch); 
                }

                if($text_reply_unique_id != '')
                {
                  $message_sent_stat_data_insert_sql="INSERT INTO messenger_bot_message_sent_stat(subscriber_id,page_table_id,message_unique_id,message_type,no_sent_click,error_count) VALUES('$subscriber_id',$page_table_id,'$text_reply_unique_id','message',1,$is_error) ON DUPLICATE KEY UPDATE no_sent_click=no_sent_click+1,error_count=error_count+$is_error";
                  $this->basic->execute_complex_query($message_sent_stat_data_insert_sql);
                }
             }

            }


        }

    }



    // =========== end of pageresponse section ================== //


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
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("user_id"=>$this->user_id,"is_template"=>"1",'template_for'=>'reply_message','media_type'=>'fb'),"where_in"=>array("page_id"=>$page_table_ids)),'','','',$start=NULL,$order_by);
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


    public function comment_growth_tools($media_type='fb')
    {
      if($this->session->userdata('selected_global_media_type') != '') {
        $media_type = $this->session->userdata('selected_global_media_type');
      }
    	$data = [];
    	$data['body'] = "comment_automation/comment_growth_tools";
      $user_type = $this->session->userdata("user_type");
      $data['media_type'] = $media_type;

      if($media_type == 'fb' || $this->session->userdata('selected_global_media_type') == '') {

          $data['page_icon'] = '<i class="fab fa-facebook-square"></i>';
          $data['page_title'] = $this->lang->line("Facebook Comment Growth Tools");
      }

      else {

        $data['page_icon'] = '<i class="fab fa-instagram"></i>';
        $data['page_title'] = $this->lang->line("Instagram Comment Growth Tools");

      }


      $data['comment_growth_tools'] = $this->lists_comment_growth_tools($media_type);
    	$this->_viewcontroller($data);

    }

    public function lists_comment_growth_tools()
    {
      $facebook_comment_template_access = false;
      $facebook_comment_reply_access = false;
      $facebook_automation_access = false;
      $facebook_tag_campaign_access = false;
      $facebook_reports_access = false;
      $has_instagram_growth_access = false;
      $user_type = $this->session->userdata("user_type");
      
      if($user_type=="Admin" || count(array_intersect($this->module_access, ['80','201','202','204','206','220','222','223','251','256'])) > 0 ) {

        if($user_type=="Admin" || in_array(251,$this->module_access)) {
          $facebook_comment_template_access = true;
        }

        if($user_type=="Admin" || count(array_intersect($this->module_access, ['80','220','222','223','256'])) > 0 ) {
          $facebook_comment_reply_access = true;
        }

        if($user_type=="Admin" || count(array_intersect($this->module_access, ['80','204','206','251'])) > 0 ) {
          $facebook_automation_access = true;
        }

        if($this->basic->is_exist("add_ons",array("project_id"=>29))) {
          if($user_type=="Admin" || count(array_intersect($this->module_access, ['201','202'])) > 0 ) {
            $facebook_tag_campaign_access = true;
          }
        }

        if($user_type=="Admin" || count(array_intersect($this->module_access, ['80','201','202','204','206'])) > 0 ) {
          $facebook_reports_access = true;
        }

      }

      if($user_type=="Admin" || count(array_intersect($this->module_access, ['278','279'])) > 0 ) {
        if($this->config->item('instagram_reply_enable_disable') == '1') {
          $has_instagram_growth_access = true;
        }
      }


      return [
        'fb' => [
          '0' => [
            'title'=>'Comment Template',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/comment_temp.png'),
            'url'=>base_url('comment_automation/comment_template_manager'),
            'has_access'=> $facebook_comment_template_access, 
          ],
          '1' => [
            'title'=>'Reply Template',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/reply_temp.png'),
            'url'=>base_url('comment_automation/template_manager'),
            'has_access'=> $facebook_comment_reply_access,
          ],
          '2' => [
            'title'=>'Campaigns',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/automatic.png'),
            'url'=> base_url('comment_automation/index'),
            'has_access'=> $facebook_automation_access,
          ],
          '3' => [
            'title'=>'Tag Campaign',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/tag.png'),
            'url'=> base_url('comment_reply_enhancers/post_list'),
            'has_access'=> $facebook_tag_campaign_access,
          ],
          '4' => [
            'title'=>'Reports',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/report.png'),
            'url'=> base_url('comment_automation/comment_section_report'),
            'has_access'=> $facebook_reports_access,
          ],
        ],
        'ig' => [
          '0' => [
            'title'=>'Comment Template',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/comment_temp.png'),
            'url'=> base_url('comment_automation/comment_template_manager'),
            'has_access'=> $has_instagram_growth_access,
          ],
          '1' => [
            'title'=>'Reply Template',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/reply_temp.png'),
            'url'=> base_url('instagram_reply/template_manager'),
            'has_access'=> $has_instagram_growth_access,
          ],
          '2' => [
            'title'=>'Campaigns',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/automatic.png'),
            'url'=> base_url('instagram_reply/get_account_lists'),
            'has_access'=> $has_instagram_growth_access,
          ],
          '3' => [
            'title'=>'Reports',
            'img_path'=>base_url('assets/img/api_channel_icon/temp/report.png'),
            'url'=> base_url('instagram_reply/reports'),
            'has_access'=> $has_instagram_growth_access,
          ],
        ]
      ];
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
      $comment_info = $this->fb_rx_login->get_all_comment_of_post($post_id,$page_access_token);

      // pre($comment_info); exit;
      $html = '
        <div class="card mb-0" id="comment_lists">
          <div class="card-header bg-primary">
              <h4 id="display-tracking-name" class="text-white"><i class="fas fa-list-alt"></i> '.$this->lang->line('Comment List').'</h4>
              <div class="card-header-action">
                <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">&times;</button>
              </div>
          </div>
          <div class="card-body p-0">
              <div id="activecampaign-list-group" class="list-group">';
                if(!empty($comment_info)) {
                  $html .='<div class="tickets-list makeScroll">';
                      foreach ($comment_info as $value) {

                        if(mb_strlen($value['message']) >= 95)
                            $comment_text = mb_substr($value['message'], 0, 90).'...';
                        else $comment_text = $value['message'];

                        $commenter_name = isset($value['from']['name']) ? $value['from']['name'] : '';

                        $date=new DateTime($value['created_time']['date'],new DateTimeZone("UTC"));
                        $tz = date_default_timezone_get();
                        $date->setTimezone(new DateTimeZone($tz));
                        $created_time_new = $date->format('M j, Y h:i A');

                        $html .='
                          <div class="ticket-item list-group-item-action border border-bottom-0">
                            <div class="ticket-title mb-3">
                              <h4 class="text-primary">
                                <small class="float-right text-muted font_size_12px">'.$created_time_new.'</small>'.$comment_text.'</h4>
                              </div>
                              <div class="row"><div class="col-12 col-md-6">
                                <div class="ticket-info float-left">
                                  <div>by</div>&nbsp;
                                  <div class="text-primary">'.$commenter_name.'</div>
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



    public function switch_to_media()
    {
      $this->ajax_check();
      $media_type = $this->input->post("media_type");
      $this->session->set_userdata('selected_global_media_type',$media_type);
      echo "1";
    }

}