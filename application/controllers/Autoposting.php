<?php
require_once("application/controllers/Home.php"); // loading home controller
class Autoposting extends Home
{
    public $is_broadcaster_exist_deprecated=false;
    public $is_wp_social_sharing_exist = false;

    public function __construct()
    {
        parent::__construct();

        $function_name=$this->uri->segment(2);
        if($function_name!="autoposting_campaign_create")
        {
            if ($this->session->userdata('logged_in') != 1)
            redirect('home/login_page', 'location');   

            if($this->session->userdata('user_type') != 'Admin' && !in_array(256,$this->module_access))
            redirect('home/login_page', 'location'); 

            $this->member_validity();
        }
        // if($this->session->userdata("facebook_rx_fb_user_info")==0)
        // redirect('facebook_rx_account_import/index','refresh'); 

        $this->load->library('rss_feed');    
    } 

    public function index()
    {
        $this->settings();
    }

    public function settings()
    {
        $this->is_ultrapost_exist=$this->ultrapost_exist();
        $page_info=array();
        if($this->is_ultrapost_exist)
        {
            $table_name = "facebook_rx_fb_page_info";
            $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
            $page_info = $this->basic->get_data($table_name,array("where"=>array("facebook_rx_fb_page_info.user_id"=>$this->user_id)),array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id","facebook_rx_fb_user_info.access_token"),$join);
        }

        $settings_data=$this->basic->get_data("autoposting",array("where"=>array("user_id"=>$this->user_id,"feed_type"=>"rss")),'','','','','feed_name asc');
        
        $data['body'] = 'autoposting/settings';
        $data['page_title'] = $this->lang->line('RSS Auto-Posting');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();  
        $data['settings_data'] = $settings_data;
        $data["feed_types"]=$this->basic->get_enum_values("autoposting","feed_type");
       
        $this->_viewcontroller($data); 
    }    

    public function add_feed_action()
    {
        if(!$_POST) exit();

        $feed_name=$this->input->post('feed_name',true);
        $feed_type=$this->input->post('feed_type',true);
        $feed_url=$this->input->post('feed_url',true);

        // if($this->basic->is_exist("autoposting",array("feed_url"=>$feed_url,"user_id"=>$this->user_id),'id'))
        // {
        //     $error_message=$this->lang->line("This feed URL has been already added.");
        //     echo json_encode(array('status'=>'0','message'=>"<i class='fa fa-remove'></i> ".$error_message));
        //     exit();
        // }

        $feed = $this->rss_feed->getFeed($feed_url);

        if(!isset($feed['success']) || $feed['success']!='1')
        {
            $error_message=isset($feed['error_message'])?$feed['error_message']:$this->lang->line("Something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>$error_message));
            exit();
        }

        $datetime=date("Y-m-d H:i:s");
        date_default_timezone_set('Europe/Dublin'); // operating in GMT

        $last_pub_date = "";
        $last_pub_title = "";
        $last_pub_url = "";
        $element_list  = ($feed['element_list']) ? ($feed['element_list']) : array();
        foreach ($element_list as $key => $value) 
        {
            if($value['pubDate']=="") continue;
            if($last_pub_date=="" || (strtotime($value['pubDate'])>strtotime($last_pub_date)))
            {
                $last_pub_date=isset($value['pubDate'])?$value['pubDate']:"";
                $last_pub_date=date("Y-m-d H:i:s",strtotime($last_pub_date));
                $last_pub_title=isset($value['title'])?$value['title']:"";
                $last_pub_url=isset($value['link'])?$value['link']:"";
            }            
        }

        $insert_data["user_id"] = $this->user_id;
        $insert_data["feed_name"] = $feed_name;
        $insert_data["feed_type"] = $feed_type;
        $insert_data["feed_url"] = $feed_url;
        $insert_data["last_pub_date"] = $last_pub_date;
        $insert_data["last_pub_title"] = $last_pub_title;
        $insert_data["last_pub_url"] = $last_pub_url;
        $insert_data["last_updated_at"] = $datetime;
        $insert_data["error_message"] = json_encode(array());

        if($this->basic->insert_data("autoposting",$insert_data)) 
        {
            $this->_insert_usage_log(256,1);
            $success_message=$this->lang->line("Feed has been added successfully.");
            echo json_encode(array('status'=>'1','message'=>$success_message));
        }
        else
        {
            $error_message=$this->lang->line("Something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>$error_message));
        }
    }

    public function campaign_settings()
    {
        $this->ajax_check();
        $this->is_ultrapost_exist=$this->ultrapost_exist();
        $id=$this->input->post('id',true);

        if(!$this->is_ultrapost_exist && !$this->is_broadcaster_exist_deprecated)
        {
            $error='<div class="alert alert-danger text-center"><i class="fa fa-remove"></i> '.$this->lang->line("Access forbidden : you do not have access to publish module. Please contact application admin to get access.").'</div>';
            echo json_encode(array('html'=>$error,'feed_name'=>'','status'=>'0'));
            exit(); 
        }

        $timezones=$this->_time_zone_list();
        $get_data=$this->basic->get_data("autoposting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($get_data[0]))
        {
             $error='<div class="alert alert-danger text-center"><i class="fa fa-remove"></i> '.$this->lang->line("Feed not found.").'</div>';
             echo json_encode(array('html'=>$error,'feed_name'=>'','status'=>'0'));
             exit();
        }

        $fb_page_info=array();
        if($this->is_ultrapost_exist) $fb_page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_page_info.user_id"=>$this->user_id,"bot_enabled"=>'1')),array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name"),array('facebook_rx_fb_user_info'=>"facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left"));

        /* other social media accounts info */
        $twitter_accounts = $this->basic->get_data('twitter_users_info', array('where' => array('user_id' => $this->user_id)), array('id', 'name'));
        $linkedin_accounts = $this->basic->get_data('linkedin_users_info', array('where' => array('user_id' => $this->user_id)), array('id', 'name'));
        $reddit_accounts = $this->basic->get_data('reddit_users_info', array('where' => array('user_id' => $this->user_id)), array('id', 'username'));
        $subreddits = $this->subRedditList();



        if($this->is_broadcaster_exist_deprecated)
        {
            $table_name = "facebook_rx_fb_page_info";
            $where['where'] = array('bot_enabled' => "1","user_id"=>$this->user_id);
            $broadcaste_page_info = $this->basic->get_data($table_name,$where);
        }

        $feed_name=isset($get_data[0]['feed_name'])?$get_data[0]['feed_name']:'';
        $feed_url=isset($get_data[0]['feed_url'])?$get_data[0]['feed_url']:'';
        $feed_name_send="<a href='".$feed_url."' target='_BLANK'>".$feed_name."</a>";

        $html='';
        $script='
        <script>
            $("#submit_status").show();
            $("document").ready(function(){setTimeout(function(){ $("#page").change();$("#submit_status").hide();}, 1000);}); 
            $("[data-toggle=\"popover\"]").popover(); 
            $("[data-toggle=\"popover\"]").on("click", function(e) {e.preventDefault(); return true;});
            $("#page,#broadcast_timezone,#posting_timezone,#post_to_pages").select2();'.
            "           
            $(document.body).on('change','#page',function(){     
              var page_id=$(this).val();
              if(page_id=='') return;
              var hidden_id='".$id."';
              var table_name= 'autoposting';
              $('.dropdown_con').addClass('hidden');
              $.ajax({
                type:'POST' ,
                url: base_url+'autoposting/get_label_dropdown_edit',
                data: {page_id:page_id,hidden_id:hidden_id,table_name:table_name},
                dataType : 'JSON',
                success:function(response){
                  $('.dropdown_con').removeClass('hidden');
                  $('#first_dropdown').html(response.first_dropdown);
                  $('#second_dropdown').html(response.second_dropdown);                                     
                  $('#fb_page_id').val(response.pageinfo.page_id);
                }
              });
            });
        </script>";

        $gaptime=$this->lang->line("If the system gets small number of feeds they will be processed in first hour of given time range. If system gets large amount of feeds then they will be processed spanning all over the time range.");
        $tooplip1='<a data-html="true" href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Post Between Time").'" data-content="'.$this->lang->line('Feed information will only be posted during this time slot.')." ".$gaptime.'">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> </a>';
        $tooplip2='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Post Between Time").'" data-content="'.$this->lang->line('Feed information will only be broadcaster during this time slot.')." ".$gaptime.'">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> </a>';
        $tooplip3='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Notification Type").'" data-content="'.$this->lang->line('Regular Push notification will make a sound and display a phone notification. Use it for important messages.')."<br><br>".$this->lang->line('Silent Push notification will display a phone notification without sound. Use it for regular messages that do not require immediate action.')."<br><br>".$this->lang->line('No push will not display any notification. Use it for silently sending content updates.').'">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> </a>';

        if($this->is_ultrapost_exist)
        {
            $xpost_to_pages=isset($get_data[0]['page_ids'])?explode(',', $get_data[0]['page_ids']):array();

            $xtwitter_accounts = array();
            $xlinkedin_accounts = array();
            $xreddit_accounts = array();
            $xsubreddits = '';

            if ($get_data[0]['twitter_accounts'] != "" &&  $get_data[0]['twitter_accounts'] != "null") {
                $xtwitter_accounts = json_decode($get_data[0]['twitter_accounts']);
            }
            if ($get_data[0]['linkedin_accounts'] != "" &&  $get_data[0]['linkedin_accounts'] != "null") {
                $xlinkedin_accounts = json_decode($get_data[0]['linkedin_accounts']);
            }
            if ($get_data[0]['reddit_accounts'] != "" &&  $get_data[0]['reddit_accounts'] != "null") {
                $xreddit_accounts = json_decode($get_data[0]['reddit_accounts']);
            }
            if ($get_data[0]['subreddits'] != "") {
                $xsubreddits = $get_data[0]['subreddits'];
            }

            $posting_message = ($get_data[0]['posting_message'] != '') ? $get_data[0]['posting_message'] : '';
            $xposting_timezone=isset($get_data[0]['posting_timezone'])?$get_data[0]['posting_timezone']:"";
            $xposting_start_time=isset($get_data[0]['posting_start_time'])?$get_data[0]['posting_start_time']:"";
            $xposting_end_time=isset($get_data[0]['posting_end_time'])?$get_data[0]['posting_end_time']:"";

            if($xposting_timezone=="") $xposting_timezone=$this->config->item("time_zone");
            if($xposting_start_time=="") $xposting_start_time="00:00";
            if($xposting_end_time=="") $xposting_end_time="23:59";
        }

        if($this->is_broadcaster_exist_deprecated)
        {
            $xbroadcast_timezone=isset($get_data[0]['broadcast_timezone'])?$get_data[0]['broadcast_timezone']:"";
            $xpage_id=isset($get_data[0]['page_id'])?$get_data[0]['page_id']:"";
            $xbroadcast_start_time=isset($get_data[0]['broadcast_start_time'])?$get_data[0]['broadcast_start_time']:"";
            $xbroadcast_end_time=isset($get_data[0]['broadcast_end_time'])?$get_data[0]['broadcast_end_time']:"";
            $xbroadcast_notification_type=isset($get_data[0]['broadcast_notification_type'])?$get_data[0]['broadcast_notification_type']:"";
            $xbroadcast_display_unsubscribe=isset($get_data[0]['broadcast_display_unsubscribe'])?$get_data[0]['broadcast_display_unsubscribe']:"";

            if($xbroadcast_timezone=="") $xbroadcast_timezone=$this->config->item("time_zone");
            if($xbroadcast_start_time=="") $xbroadcast_start_time="00:00";
            if($xbroadcast_end_time=="") $xbroadcast_end_time="23:59";
            if($xbroadcast_notification_type=="") $xnotification_type="REGULAR";
            if($xbroadcast_display_unsubscribe=="") $xbroadcast_display_unsubscribe="0";
        }

        

        $html.= $script;
        $html.='<form action="#" enctype="multipart/form-data" id="campaign_settings_form" method="post">';     
        $html.='<div class="text-center waiting" id="submit_status"><i class="fas fa-spinner fa-spin blue text-center" style="font-size:40px"></i></div>';
        if($this->is_ultrapost_exist)
        {
            $others_media_count = 0;
            $html .= '<div class="row">';
            $html.='<div class="col-12 col-md-6">
                        <div class="form-group">
                            <input type="hidden" name="campaign_id" id="campaign_id" value="'.$id.'">
                            <label>'.$this->lang->line("Post to Facebook Pages").'</label>
                            <select multiple="multiple"  class="form-control" id="post_to_pages" name="post_to_pages[]">';
                            
                                foreach($fb_page_info as $key=>$val)
                                {
                                    $id=$val['id'];
                                    $page_name=$val['page_name'];
                                    $account_name=$val['account_name'];
                                    $selected='';
                                    if(in_array($id, $xpost_to_pages)) $selected="selected";
                                    $html.="<option value='{$id}' {$selected}>{$page_name}</option>";
                                }
                            $html.=
                            '</select>
                        </div>
                    </div>';

            if (!empty($twitter_accounts)) {

                $others_media_count++;
                
                $html.='<div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line("Post to Twitter Accounts").'</label>
                                <select multiple="multiple"  class="form-control" id="select_twitter_accounts" name="twitter_accounts[]">';
                                
                                    foreach($twitter_accounts as $key=>$val)
                                    {
                                        $id=$val['id'];
                                        $name=$val['name'];
                                        $selected='';
                                        if(in_array('twitter_users_info-'.$id, $xtwitter_accounts)) $selected="selected";
                                        $html.="<option value='twitter_users_info-{$id}' {$selected}>{$name}</option>";
                                    }
                                $html.=
                                '</select>
                            </div>
                        </div>';
            }

            if (!empty($linkedin_accounts)) {
                
                $others_media_count++;
                
                $html.='<div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line("Post to Linkedin Accounts").'</label>
                                <select multiple="multiple"  class="form-control" id="select_linkedin_accounts" name="linkedin_accounts[]">';
                                
                                    foreach($linkedin_accounts as $key=>$val)
                                    {
                                        $id=$val['id'];
                                        $name=$val['name'];
                                        $selected='';
                                        if(in_array('linkedin_users_info-'.$id, $xlinkedin_accounts)) $selected="selected";
                                        $html.="<option value='linkedin_users_info-{$id}' {$selected}>{$name}</option>";
                                    }
                                $html.=
                                '</select>
                            </div>
                        </div>';
            }

            if (!empty($reddit_accounts)) {
                
                $others_media_count += 2;
                
                $html.='<div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line("Post to Reddit Accounts").'</label>
                                <select multiple="multiple"  class="form-control" id="select_reddit_accounts" name="reddit_accounts[]">';
                                
                                    foreach($reddit_accounts as $key=>$val)
                                    {
                                        $id=$val['id'];
                                        $username=$val['username'];
                                        $selected='';
                                        if(in_array('reddit_users_info-'.$id, $xreddit_accounts)) $selected="selected";
                                        $html.="<option value='reddit_users_info-{$id}' {$selected}>{$username}</option>";
                                    }
                                $html.=
                                '</select>
                            </div>
                        </div>';
                
                $html.='<div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>'.$this->lang->line("Subreddit List").'</label>
                                <select class="form-control" id="select_subreddits" name="subreddits">';
                                    
                                    foreach($subreddits as $key=>$val)
                                    {
                                        
                                        $selected='';
                                        if($key == $xsubreddits) $selected="selected";
                                        // if(in_array($key, $xsubreddits)) $selected="selected";
                                        $html.="<option value='{$key}' {$selected}>{$val}</option>";
                                    }
                                $html.=
                                '</select>
                            </div>
                        </div>';
            }

            $html.='<div class="col-12 col-md-6">
                            <div class="form-group">
                            <label>'.$this->lang->line("Posting Timezone").'</label>
                            '.form_dropdown('posting_timezone', $timezones, $xposting_timezone,"class='form-control' id='posting_timezone'").'
                            </div>
                        </div>';

            if ($others_media_count == 1) {
                $html .= '<div class="col-12 col-md-6"></div>';
            }

            $html.='<div class="col-12 col-md-6">
                        <div class="form-group">
                        <label>'.$this->lang->line("Post Between Time")." ".$tooplip1.'</label>
                        <input type="text" class="form-control timepicker" value="'.$xposting_start_time.'" id="posting_start_time" name="posting_start_time">
                        </div>
                    </div>';
            $html.='<div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="hidden-xs hidden-sm" style="position: relative;right: 22px;top: 32px;">'.$this->lang->line("To").'</label>
                            <input type="text" class="form-control timepicker" value="'.$xposting_end_time.'" id="posting_end_time" name="posting_end_time">
                        </div>
                    </div>';
            $html.='<div class="col-12">
                        <div class="form-group">
                            <label>'.$this->lang->line("Message").'</label>
                            <span class="float-right" id="title_variable"><a title="" data-toggle="tooltip" data-placement="top" class="btn btn-sm" title="'.$this->lang->line("You can use the original title from the feed.").'" style="cursor: pointer;border: .5px dashed #ccc;margin-bottom: 3px;"><i class="far fa-lightbulb"></i>  '.$this->lang->line("Title").'</a></span>
                            <textarea class="form-control" id="message_textarea" name="message">'.$posting_message.'</textarea>
                        </div>
                    </div>';
            $html .= '</div>
                        <script>$("#select_twitter_accounts").select2();</script>
                        <script>$("#select_linkedin_accounts").select2();</script>
                        <script>$("#select_reddit_accounts").select2();</script>
                        <script>$(function () { $("[data-toggle=\'tooltip\']").tooltip()})</script>
                        <script>$("#message_textarea").emojioneArea({
                                    autocomplete: false,
                                    pickerPosition: "bottom",
                                    //hideSource: false,
                                 });</script>
                        ';
        }

        if($this->is_broadcaster_exist_deprecated)
        {
            $html .= '<hr><div class="row">';
            $html.='<div class="col-12 col-md-6"> 
                        <div class="form-group">
                           <label>'.$this->lang->line("Broadcast to Pages").'</label>
                              <input type="hidden" name="fb_page_id" id="fb_page_id">
                              <select class="form-control" id="page" name="page"> 
                                <option value="">'.$this->lang->line("Select Page").'</option>';                                              
                                  foreach($broadcaste_page_info as $key=>$val)
                                  { 
                                    $id=$val['id'];
                                    $page_name=$val['page_name'];
                                    $selected='';
                                    if($id==$xpage_id) $selected="selected";
                                    $html.="<option value='{$id}' {$selected}>{$page_name}</option>";               
                                  }           
                              $html.=
                            '</select>
                        </div>
                    </div>';
            $html.='<div class="col-12 col-md-6">
                        <div class="form-group">
                        <label>'.$this->lang->line("Broadcast Timezone").'</label>
                        '.form_dropdown('broadcast_timezone', $timezones, $xbroadcast_timezone,"class='form-control' id='broadcast_timezone'").'
                        </div>
                    </div>';
            $html.='<div class="col-12 col-md-6">
                        <div class="form-group">
                        <label>'.$this->lang->line("Broadcast Between Time")." ".$tooplip2.'</label>
                        <input type="text" class="form-control timepicker" value="'.$xbroadcast_start_time.'"  id="broadcast_start_time" name="broadcast_start_time">
                        </div>
                    </div>';
            $html.='<div class="col-12 col-md-6">
                        <div class="form-group">
                             <label class="hidden-xs hidden-sm" style="position: relative;right: 22px;top: 32px;">'.$this->lang->line("To").'</label>
                            <input type="text" class="form-control timepicker" value="'.$xbroadcast_end_time.'"  id="broadcast_end_time" name="broadcast_end_time">
                        </div>
                    </div>';

            $html.='<div class="col-12 col-md-6 hidden dropdown_con"> 
                        <div class="form-group">
                          <label style="width:100%">
                          '.$this->lang->line("Choose Labels").'
                          </label>
                          <span id="first_dropdown"></span>                                  
                        </div>       
                    </div>
                    <div class="col-12 col-md-6 hidden dropdown_con"> 
                        <div class="form-group">
                          <label style="width:100%">
                            '.$this->lang->line("Exclude Labels").'
                          </label>
                          <span id="second_dropdown"></span>                 
                        </div>       
                    </div>';

            $notification_types=array("REGULAR"=>"REGULAR","SILENT_PUSH"=>"SILENT_PUSH","NO_PUSH"=>"NO_PUSH");
            $notification_type_form=form_dropdown('broadcast_notification_type', $notification_types, $xbroadcast_notification_type,'class="form-control" id="broadcast_display_unsubscribe"');

            $html.='<div class="col-12 col-md-6">
                        <div class="form-group">
                            <label>'.$this->lang->line("Notification Type").' '.$tooplip3.'</label>
                            '.$notification_type_form.'
                        </div>
                    </div>';

            $unsubscribe_form="";
            if($xbroadcast_display_unsubscribe=='1')
            {
                $unsubscribe_form.='<br><input type="radio" id="unsubscribe_yes" value="1" name="broadcast_display_unsubscribe" checked> <label for="unsubscribe_yes">'.$this->lang->line("yes").'</label>';
                $unsubscribe_form.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="unsubscribe_no" value="0" name="broadcast_display_unsubscribe"> <label for="unsubscribe_no">'.$this->lang->line("no").'</label>';
            }
            else
            {
                $unsubscribe_form.='<br><input type="radio" id="unsubscribe_yes" value="1" name="broadcast_display_unsubscribe"> <label for="unsubscribe_yes">'.$this->lang->line("yes").'</label>';
                $unsubscribe_form.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="unsubscribe_no" value="0" name="broadcast_display_unsubscribe" checked> <label for="unsubscribe_no">'.$this->lang->line("no").'</label>';
            }
            $html.='<div class="col-12 col-md-6">
                        <div class="form-group"><label>'.$this->lang->line("Display Unsubscribe Button?").'</label>'.$unsubscribe_form.'</div>
                    </div>';
            $html .= '</div>';
        }

        $html.='<div id="submit_response"></div>';
        $html.='</form><div class="clearfix">';
       

        echo json_encode(array('html'=>$html,'feed_name'=>$feed_name_send,'status'=>'1'));
    }

    public function create_campaign()
    {
        $this->ajax_check();
        if(!$_POST) exit();
        $this->is_ultrapost_exist=$this->ultrapost_exist();
        $campaign_id=$this->input->post("campaign_id",true);
        $xdata=$this->basic->get_data("autoposting",array("where"=>array("id"=>$campaign_id,"user_id"=>$this->user_id)));

        $post_to_pages=array();
        $page_names=array();
        $posting_title="";
        $posting_start_time="";
        $posting_end_time="";
        $posting_end_time="";
        $posting_timezone="";

        $broadcast_fb_page_id="";
        $broadcast_page_id="";
        $broadcast_page_name="";
        $broadcast_label_ids=array();
        $broadcast_start_time="";
        $broadcast_end_time="";
        $broadcast_notification_type="";
        $broadcast_display_unsubscribe="";
        $broadcast_excluded_label_ids=array();
        $broadcast_timezone="";

        if($this->is_ultrapost_exist)
        {
            $posting_message = $this->input->post('message', true);
            $post_to_pages=$this->input->post("post_to_pages",true);
            if(!isset($post_to_pages) || !is_array($post_to_pages)) $post_to_pages=array();
            $posting_start_time=$this->input->post("posting_start_time",true);
            $posting_end_time=$this->input->post("posting_end_time",true);
            $posting_end_time=$this->input->post("posting_end_time",true);
            $posting_timezone=$this->input->post("posting_timezone",true);

            $twitter_accounts = $this->input->post('twitter_accounts', true);
            $linkedin_accounts = $this->input->post('linkedin_accounts', true);
            $reddit_accounts = $this->input->post('reddit_accounts', true);
            $subreddits = $this->input->post('subreddits', true);
        }  

        if($this->is_broadcaster_exist_deprecated)
        {
            $broadcast_fb_page_id=$this->input->post("fb_page_id",true);
            $broadcast_page_id=$this->input->post("page",true);
            $broadcast_label_ids=$this->input->post("label_ids",true);
            $broadcast_excluded_label_ids=$this->input->post("excluded_label_ids",true);
            $broadcast_start_time=$this->input->post("broadcast_start_time",true);
            $broadcast_end_time=$this->input->post("broadcast_end_time",true);
            $broadcast_timezone=$this->input->post("broadcast_timezone",true);
            $broadcast_notification_type=$this->input->post("broadcast_notification_type",true);
            $broadcast_display_unsubscribe=$this->input->post("broadcast_display_unsubscribe",true);
            if(!isset($broadcast_label_ids) || !is_array($broadcast_label_ids)) $broadcast_label_ids=array();
            if(!isset($broadcast_excluded_label_ids) || !is_array($broadcast_excluded_label_ids)) $broadcast_excluded_label_ids=array();
        }

        $update_data=array
        (                     
           "last_updated_at"=>date("Y-m-d H:i:s")
        );

        if($this->is_ultrapost_exist && count($post_to_pages)>0)
        {
            $pagedata=$this->basic->get_data("facebook_rx_fb_page_info",array("where_in"=>array("id"=>$post_to_pages)));
            $page_names_array=array();
            $facebook_rx_fb_user_info_id_array=array();
            foreach ($pagedata as $key => $value)
            {
                $page_names_array[$value['id']]=$value["page_name"];
                $facebook_rx_fb_user_info_id_array[$value['id']]=$value["facebook_rx_fb_user_info_id"];
            }
            $update_data["page_ids"]=implode(',', $post_to_pages);
            $update_data["page_names"]=json_encode($page_names_array);
            $update_data["facebook_rx_fb_user_info_ids"]=json_encode($facebook_rx_fb_user_info_id_array);
            $update_data["posting_start_time"]=$posting_start_time;
            $update_data["posting_end_time"]=$posting_end_time;
            $update_data["posting_timezone"]=$posting_timezone;
            $update_data["posting_message"]=$posting_message;

            $update_data["twitter_accounts"] = ($twitter_accounts != '') ? json_encode($twitter_accounts) : '';
            $update_data["linkedin_accounts"] = ($linkedin_accounts != '') ? json_encode($linkedin_accounts) : '';
            $update_data["reddit_accounts"] = ($reddit_accounts != '') ? json_encode($reddit_accounts) : '';
            $update_data["subreddits"] = $subreddits;
        }
        else
        {
            $update_data["page_ids"]="";
            $update_data["page_names"]=json_encode(array());
            $update_data["facebook_rx_fb_user_info_ids"]=json_encode(array());
            $update_data["posting_start_time"]="";
            $update_data["posting_end_time"]="";
            $update_data["posting_timezone"]="";
            $update_data["posting_message"]= $posting_message;

            $update_data["twitter_accounts"] = ($twitter_accounts != '') ? json_encode($twitter_accounts) : '';
            $update_data["linkedin_accounts"] = ($linkedin_accounts != '') ? json_encode($linkedin_accounts) : '';
            $update_data["reddit_accounts"] = ($reddit_accounts != '') ? json_encode($reddit_accounts) : '';
            $update_data["subreddits"] = $subreddits;
        }

        if($this->is_broadcaster_exist_deprecated && $broadcast_page_id!="")
        {
            $pagedata2=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$broadcast_page_id)));
            $broadcast_page_name=isset($pagedata2[0]['page_name'])?$pagedata2[0]['page_name']:"";
            $update_data["page_id"]=$broadcast_page_id;
            $update_data["fb_page_id"]=$broadcast_fb_page_id;
            $update_data["page_name"]=$broadcast_page_name;
            $update_data["label_ids"]=implode(',', $broadcast_label_ids);
            $update_data["excluded_label_ids"]=implode(',', $broadcast_excluded_label_ids);
            $update_data["broadcast_start_time"]=$broadcast_start_time;
            $update_data["broadcast_end_time"]=$broadcast_end_time;
            $update_data["broadcast_timezone"]=$broadcast_timezone;
            $update_data["broadcast_notification_type"]=$broadcast_notification_type;
            $update_data["broadcast_display_unsubscribe"]=$broadcast_display_unsubscribe;
        }
        else
        {
            $broadcast_page_name="";
            $update_data["page_id"]="";
            $update_data["fb_page_id"]="";
            $update_data["page_name"]="";
            $update_data["label_ids"]="";
            $update_data["excluded_label_ids"]="";
            $update_data["broadcast_start_time"]="";
            $update_data["broadcast_end_time"]="";
            $update_data["broadcast_timezone"]="";
            $update_data["broadcast_notification_type"]="";
            $update_data["broadcast_display_unsubscribe"]="";
        }

        $this->basic->update_data("autoposting",array("id"=>$campaign_id,"user_id"=>$this->user_id),$update_data);
        {
            echo json_encode(array("status"=>"1","message"=>$this->lang->line("Campaign has been submitted successfully and will start processing shortly as per your settings.")));
        }

    }

    public function enable_settings()
    {
        if(!$_POST) exit();

        $feed = [];
        $id = $this->input->post('id',true);
        $media_type = $this->input->post('media_type',true);
        $get_data=$this->basic->get_data("autoposting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        
        if(!isset($get_data[0]))
        {
             $error=$this->lang->line("Feed not found.");
             echo json_encode(array('message'=>$error,'status'=>'0'));
             exit();
        }
        
        $feed_url = isset($get_data[0]['feed_url']) ? $get_data[0]['feed_url'] : '';
        $feed = $this->rss_feed->getFeed($feed_url);

        if(!isset($feed['success']) || $feed['success']!='1')
        {
            $error_message=isset($feed['error_message'])?$feed['error_message']:$this->lang->line("Something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>$error_message));
            exit();
        }
        
        $datetime=date("Y-m-d H:i:s");
        date_default_timezone_set('Europe/Dublin'); // operating in GMT
        $last_pub_date=isset($feed['element_list'][0]['pubDate'])?$feed['element_list'][0]['pubDate']:"";
        $last_pub_date=date("Y-m-d H:i:s",strtotime($last_pub_date));
        $last_pub_title=isset($feed['element_list'][0]['title'])?$feed['element_list'][0]['title']:"";
        $last_pub_url=isset($feed['element_list'][0]['link'])?$feed['element_list'][0]['link']:"";

        $update_data=array
        (
            "last_pub_date"=>$last_pub_date,
            "last_pub_title"=>$last_pub_title,
            "last_pub_url"=>$last_pub_url,
            "last_updated_at"=>$datetime,
            "status"=>"1"
        );

        if($this->basic->update_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id),$update_data))
        $this->session->set_flashdata('auto_success',1);
        else $this->session->set_flashdata('auto_success',0);       

        echo json_encode(array('status'=>'1'));     
    }

    public function disable_settings()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);

        if($this->basic->update_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id),array("status"=>"0")))
        {
            $this->session->set_flashdata('auto_success',1);
        }
        else
        {
            $this->session->set_flashdata('auto_success',0);
        }
    }

    public function force_process()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);

        if($this->basic->update_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id),array("cron_status"=>"0")))
        {
            $this->session->set_flashdata('auto_success',1);
        }
        else
        {
            $this->session->set_flashdata('auto_success',0);
        }
    }

    public function delete_settings()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);

        if($this->basic->delete_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id)))
        {
            $this->session->set_flashdata('auto_success',1);
            $this->_delete_usage_log(256,1);
        }
        else
        {
            $this->session->set_flashdata('auto_success',0);
        }
    }

    public function error_log()
    {
        $this->ajax_check();
        $id=$this->input->post('id',true);
        $get_data=$this->basic->get_data("autoposting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $error_log=isset($get_data[0]["error_message"])?json_decode($get_data[0]["error_message"],true):array();
        if(!is_array($error_log) || count($error_log)==0)
        {
            echo "<div class='alert alert-light text-center'>".$this->lang->line('no error found')."</div>";
        }
        else
        {
            $error_log=array_reverse($error_log);

            echo '<script>
                  $(document).ready(function() {
                      $(".mypre").mCustomScrollbar({
                        autoHideScrollbar:true,
                        theme:"3d-dark",
                        axis: "x"
                      });
                    });
                  </script>';
             echo "<div class='clearfix'><a href='' class='clear_log btn btn-outline-danger btn-sm float-right' data-id='".$id."'><i class='fa fa-trash'></i>".$this->lang->line('Delete')."</a></div>";
            echo "<ul class='list-group'>";
            foreach ($error_log as $key => $value) 
            {
                echo "<li class='list-group-item'>".date("d-m-Y H:i:s",strtotime($value['time']))." : ".$value["message"]."</li>";
            }
            echo "</ul>";
           
        }
    }

    public function clear_log()
    {
        $this->ajax_check();
        $id=$this->input->post('id',true);
        $this->basic->update_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id),array("error_message"=>json_encode(array()),"last_updated_at"=>date("Y-m-d H:i:s")));      
        echo "1";        
    }

    public function get_label_dropdown_edit()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id
        $hidden_id=$this->input->post('hidden_id');
        $table_name=$this->input->post('table_name');

        $xdata=$this->basic->get_data($table_name,array("where"=>array("id"=>$hidden_id)));
        $xlabel_ids=isset($xdata[0]["label_ids"])?$xdata[0]["label_ids"]:"";
        $xexcluded_label_ids=isset($xdata[0]["excluded_label_ids"])?$xdata[0]["excluded_label_ids"]:"";
        $xlabel_ids=explode(',', $xlabel_ids);
        $xexcluded_label_ids=explode(',', $xexcluded_label_ids);

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');
        $result = array();
        $group_name =array();

        $dropdown=array();
        $str='<script>$("#label_ids").select2();</script> ';
        $str2='  
          <script>$("#excluded_label_ids").select2();</script> ';
        $str .='<select multiple="multiple"  class="form-control" id="label_ids" name="label_ids[]">';
        $str2.='<select multiple="multiple"  class="form-control" id="excluded_label_ids" name="excluded_label_ids[]">';
        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_type = $value['group_name'];
            $where_simple=array('messenger_bot_subscriber.user_id'=>$this->user_id);
            $where_simple['messenger_bot_page_info.user_id'] = $this->user_id;
            $where_simple['messenger_bot_page_info.deleted'] = '0';;
            $this->db->where("FIND_IN_SET('$search_key',messenger_bot_subscriber.contact_group_id) !=", 0);
            $where=array('where'=>$where_simple);
            $select=array("messenger_bot_subscriber.*");
            $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot_subscriber.page_id,left");
            $group_by = "id";
            $contact_details=$this->basic->get_data('messenger_bot_subscriber', $where, $select, $join, $limit='', $start='', $order_by='messenger_bot_subscriber.first_name', $group_by);

            $contact_count[$search_key]=0;
            foreach ($contact_details as $key2 => $value2)
            {
                $temp=explode(',', $value2["contact_group_id"]);
                if(in_array($search_key, $temp))
                $contact_count[$search_key]++;
            }
            if($contact_count[$search_key]>0)
            {
                $temp_count=$contact_count[$search_key];
                $temp_group_name=$search_type." (".$temp_count.")";

                $select="";
                if(in_array($search_key, $xlabel_ids)) $select='selected';

                $select2="";
                if(in_array($search_key, $xexcluded_label_ids)) $select2='selected';

                $str.=  "<option data-count='".$temp_count."' value='{$search_key}' {$select}>".$temp_group_name."</option>";
                $str2.= "<option data-count='".$temp_count."' value='{$search_key}' {$select2}>".$temp_group_name."</option>";
            }

        }
        $str.= '</select>';
        $str2.='</select>';

        $pageinfo = $this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));
        $page_info = isset($pageinfo[0])?$pageinfo[0]:array();

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1')),'','','',$start=NULL,$order_by='template_name ASC');
        $push_postback="";
        foreach ($postback_data as $key => $value) 
        {
            if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply") continue;
            $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }

        echo json_encode(array('first_dropdown'=>$str,'second_dropdown'=>$str2,"pageinfo"=>$page_info,"push_postback"=>$push_postback));
    }

    public function subRedditList()
    {
         
        $subRedditListArr = array(
            "1200isplenty" => "1200isplenty",
            "AdventureTime" => "AdventureTime",
            "Android" => "Android",
            "Android" => "Android",
            "Animated" => "Animated",
            "Anxiety" => "Anxiety",
            "ArcherFX" => "ArcherFX",
            "ArtPorn" => "ArtPorn",
            "AskReddit" => "AskReddit",
            "AskScience" => "AskScience",
            "AskScience" => "AskScience",
            "AskSocialScience" => "AskSocialScience",
            "Baseball" => "Baseball",
            "BetterCallSaul" => "BetterCallSaul",
            "Bitcoin" => "Bitcoin",
            "Bitcoin" => "Bitcoin",
            "BobsBurgers" => "BobsBurgers",
            "Books" => "Books",
            "BreakingBad" => "BreakingBad",
            "Cheap_Meals" => "Cheap_Meals",
            "Classic4chan" => "Classic4chan",
            "DeepIntoYouTube" => "DeepIntoYouTube",
            "Discussion" => "Discussion",
            "DnD" => "DnD",
            "Doctor Who" => "Doctor Who",
            "DoesAnybodyElse" => "DoesAnybodyElse",
            "DunderMifflin" => "DunderMifflin",
            "EDC" => "EDC",
            "Economics" => "Economics",
            "Entrepreneur" => "Entrepreneur",
            "FlashTV" => "FlashTV",
            "FoodPorn" => "FoodPorn",
            "General" => "General",
            "General" => "General",
            "General" => "General",
            "General" => "General",
            "General" => "General",
            "HistoryPorn" => "HistoryPorn",
            "Hockey" => "Hockey",
            "HybridAnimals" => "HybridAnimals",
            "IASIP" => "IASIP",
            "Jokes" => "Jokes",
            "LetsNotMeet" => "LetsNotMeet",
            "LifeProTips" => "LifeProTips",
            "Lifestyle" => "Lifestyle",
            "Linux" => "Linux",
            "LiverpoolFC" => "LiverpoolFC",
            "Netflix" => "Netflix",
            "Occupation" => "Occupation",
            "Offensive_Wallpapers" => "Offensive_Wallpapers",
            "OutOfTheLoop" => "OutOfTheLoop",
            "PandR" => "PandR",
            "Pokemon" => "Pokemon",
            "Seinfeld" => "Seinfeld",
            "Sherlock" => "Sherlock",
            "Soccer" => "Soccer",
            "Sound" => "Sound",
            "SpacePorn" => "SpacePorn",
            "Television" => "Television",
            "TheSimpsons" => "TheSimpsons",
            "Tinder" => "Tinder",
            "TrueDetective" => "TrueDetective",
            "UniversityofReddit" => "UniversityofReddit",
            "YouShouldKnow" => "YouShouldKnow",
            "advice" => "advice",
            "amiugly" => "amiugly",
            "anime" => "anime",
            "apple" => "apple",
            "aquariums" => "aquariums",
            "askculinary" => "askculinary",
            "askengineers" => "askengineers",
            "askengineers" => "askengineers",
            "askhistorians" => "askhistorians",
            "askmen" => "askmen",
            "askphilosophy" => "askphilosophy",
            "askwomen" => "askwomen",
            "bannedfromclubpenguin" => "bannedfromclubpenguin",
            "batman" => "batman",
            "battlestations" => "battlestations",
            "bicycling" => "bicycling",
            "bigbrother" => "bigbrother",
            "biology" => "biology",
            "blackmirror" => "blackmirror",
            "blackpeoplegifs" => "blackpeoplegifs",
            "budgetfood" => "budgetfood",
            "business" => "business",
            "casualiama" => "casualiama",
            "celebs" => "celebs",
            "changemyview" => "changemyview",
            "changemyview" => "changemyview",
            "chelseafc" => "chelseafc",
            "chemicalreactiongifs" => "chemicalreactiongifs",
            "chemicalreactiongifs" => "chemicalreactiongifs",
            "chemistry" => "chemistry",
            "coding" => "coding",
            "college" => "college",
            "comics" => "comics",
            "community" => "community",
            "confession" => "confession",
            "cooking" => "cooking",
            "cosplay" => "cosplay",
            "cosplay" => "cosplay",
            "crazyideas" => "crazyideas",
            "cyberpunk" => "cyberpunk",
            "dbz" => "dbz",
            "depression" => "depression",
            "doctorwho" => "doctorwho",
            "education" => "education",
            "educationalgifs" => "educationalgifs",
            "engineering" => "engineering",
            "entertainment" => "entertainment",
            "environment" => "environment",
            "everymanshouldknow" => "everymanshouldknow",
            "facebookwins" => "facebookwins",
            "facepalm" => "facepalm",
            "facepalm" => "facepalm",
            "fantasy" => "fantasy",
            "fantasyfootball" => "fantasyfootball",
            "firefly" => "firefly",
            "fitmeals" => "fitmeals",
            "flexibility" => "flexibility",
            "food" => "food",
            "funny" => "funny",
            "futurama" => "futurama",
            "gadgets" => "gadgets",
            "gallifrey" => "gallifrey",
            "gamedev" => "gamedev",
            "gameofthrones" => "gameofthrones",
            "geek" => "geek",
            "gentlemanboners" => "gentlemanboners",
            "gifs" => "gifs",
            "girlsmirin" => "girlsmirin",
            "google" => "google",
            "gravityfalls" => "gravityfalls",
            "gunners" => "gunners",
            "hardbodies" => "hardbodies",
            "hardware" => "hardware",
            "harrypotter" => "harrypotter",
            "history" => "history",
            "hockey" => "hockey",
            "houseofcards" => "houseofcards",
            "howto" => "howto",
            "humor" => "humor",
            "investing" => "investing",
            "japanesegameshows" => "japanesegameshows",
            "keto" => "keto",
            "ketorecipes" => "ketorecipes",
            "languagelearning" => "languagelearning",
            "law" => "law",
            "learnprogramming" => "learnprogramming",
            "lectures" => "lectures",
            "lego" => "lego",
            "lifehacks" => "lifehacks",
            "linguistics" => "linguistics",
            "literature" => "literature",
            "loseit" => "loseit",
            "magicTCG" => "magicTCG",
            "marvelstudios" => "marvelstudios",
            "math" => "math",
            "mrrobot" => "mrrobot",
            "mylittlepony" => "mylittlepony",
            "naruto" => "naruto",
            "nasa" => "nasa",
            "nbastreams" => "nbastreams",
            "nosleep" => "nosleep",
            "nutrition" => "nutrition",
            "olympics" => "olympics",
            "onepunchman" => "onepunchman",
            "paleo" => "paleo",
            "patriots" => "patriots",
            "pettyrevenge" => "pettyrevenge",
            "photoshop" => "photoshop",
            "physics" => "physics",
            "pics" => "pics",
            "pizza" => "pizza",
            "podcasts" => "podcasts",
            "poetry" => "poetry",
            "pokemon" => "pokemon",
            "preppers" => "preppers",
            "prettygirls" => "prettygirls",
            "psychology" => "psychology",
            "python" => "python",
            "quotes" => "quotes",
            "rainmeter" => "rainmeter",
            "rateme" => "rateme",
            "reactiongifs" => "reactiongifs",
            "recipes" => "recipes",
            "reddevils" => "reddevils",
            "relationship_advice" => "relationship_advice",
            "rickandmorty" => "rickandmorty",
            "running" => "running",
            "samplesize" => "samplesize",
            "scifi" => "scifi",
            "screenwriting" => "screenwriting",
            "seinfeld" => "seinfeld",
            "self" => "self",
            "sex" => "sex",
            "shield" => "shield",
            "simpleliving" => "simpleliving",
            "soccer" => "soccer",
            "southpark" => "southpark",
            "stockmarket" => "stockmarket",
            "stocks" => "stocks",
            "talesfromtechsupport" => "talesfromtechsupport",
            "tattoos" => "tattoos",
            "teachers" => "teachers",
            "thewalkingdead" => "thewalkingdead",
            "thinspo" => "thinspo",
            "tinyhouses" => "tinyhouses",
            "todayilearned" => "todayilearned",
            "topgear" => "topgear",
            "tumblr" => "tumblr",
            "twinpeaks" => "twinpeaks",
            "twitch" => "twitch",
            "vandwellers" => "vandwellers",
            "vegan" => "vegan",
            "videos" => "videos",
            "wallpaper" => "wallpaper",
            "weathergifs" => "weathergifs",
            "westworld" => "westworld",
            "whitepeoplegifs" => "whitepeoplegifs",
            "wikileaks" => "wikileaks",
            "wikipedia" => "wikipedia",
            "woodworking" => "woodworking",
            "writing" => "writing",
            "wwe" => "wwe",
            "youtube" => "youtube",
            "youtubehaiku" => "youtubehaiku",
            "zombies" => "zombies"
        );

        $subRedditListArr = array_unique($subRedditListArr);

        return $subRedditListArr;
    }
}