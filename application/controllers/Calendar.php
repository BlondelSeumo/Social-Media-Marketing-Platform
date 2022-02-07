<?php

require_once("Home.php"); // loading home controller

class calendar extends Home
{    

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');
        // if($this->session->userdata('user_type') != 'Admin' && !in_array(76,$this->module_access))
        // redirect('home/login_page', 'location');\

        // if($this->session->userdata("facebook_rx_fb_user_info")==0)
        // redirect('social_accounts/index','refresh');        
       
    }

    public function user($user_id=0)
    {
      $this->index($user_id);
    }



    public function index($user_id=0)
    {
      $data['body'] = "calendar/full_calendar";
      $data['page_title'] = $this->lang->line("activity calendar");
      $this->_viewcontroller($data);
    }

    public function new_full_calendar(){

      $data['data'] = array();

      $user_id = $this->user_id;
      $user_id_url = $this->input->post('user_id_url',true);

      if(!empty($user_id_url) && $user_id_url>0 && $this->session->userdata('user_type')=='Admin') $user_id = $user_id_url;
      
      $start = $this->input->post('start');
      $end = $this->input->post('end');
      $start = date("Y-m-d",strtotime($start));
      $end = date("Y-m-d",strtotime($end));

      $where = ['where'=>['user_id'=>$user_id,'schedule_time >=' => $start,'schedule_time <='=> $end]];

      $select = ["id","message as description","post_type","schedule_time","time_zone","posting_status","campaign_name","page_or_group_or_user_name","media_type","link","image_url"];
      $multimedia_posts = $this->basic->get_data('facebook_rx_auto_post',$where,$select);

      $select = ["id","message as description","posting_status","time_zone","schedule_time","campaign_name","page_or_group_or_user_name",'cta_type',"link"];
      $cta_posts = $this->basic->get_data('facebook_rx_cta_post',$where,$select);

      $select = ["id","message as description","post_type","posting_status","schedule_time","time_zone","campaign_name","page_or_group_or_user_name"];
      $slider_posts = $this->basic->get_data('facebook_rx_slider_post',$where,$select);

      $select = ['id','email_message as description','campaign_name','schedule_time','time_zone','posting_status','configure_email_table as email_api_table','total_thread','successfully_sent'];
      $email_campaigns = $this->basic->get_data('email_sending_campaign',$where,$select);

      $select = ['id','campaign_message as description','campaign_name','schedule_time','time_zone','posting_status','api_id as sms_api_table','total_thread','successfully_sent'];
      $sms_campaigns = $this->basic->get_data('sms_sending_campaign',$where,$select);

      $select = ["id","message as description","posting_status","schedule_time","timezone","campaign_name","posting_status","broadcast_type","total_thread","successfully_sent"];
      $messenger_bot_broadcast_campaigns = $this->basic->get_data('messenger_bot_broadcast_serial',$where,$select);

      $select = ["id","message as description","campaign_type","campaign_name","schedule_time","schedule_timezone","posting_status","link","image_url","video_url"];
      $comboposter_campaigns = $this->basic->get_data('comboposter_campaigns',$where,$select);

      $liveStreaming_campaigns = [];
      if($this->addon_exist('vidcasterlive')) {
        $select = ["id","message as description","scheduler_name as campaign_name","schedule_time","time_zone","posting_status","stream_url"];
        $liveStreaming_campaigns = $this->basic->get_data('vidcaster_facebook_rx_live_scheduler',$where,$select);
      }


      $activities = array_merge($multimedia_posts,$cta_posts,$slider_posts,$email_campaigns,$sms_campaigns,$messenger_bot_broadcast_campaigns,$comboposter_campaigns,$liveStreaming_campaigns);


      $c_type = '';
      $edit_url = '';

      foreach($activities as $key => $value ){

        $data['data'][$key]['start'] = $value['schedule_time'];

        $campaign_post_type = $value['post_type'] ?? "";
        $posting_status = $value['posting_status'];

        $media_type ='';
        $post_type = '';
        $edit_url = '';        
        $broadcast_status = '';    
        $media_preview = '';

        if(isset($value['media_type']) && !empty($value['media_type'])) {
          $media_type = ($value['media_type']=="facebook") ? $this->lang->line("FB"):$this->lang->line("IG");
        }

        if(isset($campaign_post_type) && $campaign_post_type== "image_submit") {
          $post_type = $media_type.' '.$this->lang->line("Post (Image)");
          $edit_url = site_url('instagram_poster/image_video_edit_auto_post/'.$value['id']);

        } elseif(isset($campaign_post_type) && $campaign_post_type== "text_submit") {
          $post_type = $media_type.' '.$this->lang->line("Post (Text)");
          $edit_url = site_url('instagram_poster/image_video_edit_auto_post/'.$value['id']);
        }
        elseif(isset($campaign_post_type) && $campaign_post_type== "link_submit")
        {
          $post_type = $media_type.' '.$this->lang->line("Post (Link)");
          $edit_url = site_url('instagram_poster/image_video_edit_auto_post/'.$value['id']);

        }
        elseif(isset($campaign_post_type) && $campaign_post_type== "video_submit")
        {
          $post_type = $media_type.' '.$this->lang->line("Post (Video)");
          $edit_url = site_url('instagram_poster/image_video_edit_auto_post/'.$value['id']);

        } elseif(isset($campaign_post_type) && $campaign_post_type== "carousel_post") {

          $post_type = $this->lang->line("FB Post (Carousel)");
          $edit_url = site_url('ultrapost/edit_carousel_slider/'.$value['id']);

        } elseif(isset($campaign_post_type) && $campaign_post_type== "slider_post") {

          $post_type = $this->lang->line("FB Post (Slider)");
          $edit_url = site_url('ultrapost/edit_carousel_slider/'.$value['id']);

        }
        elseif(isset($value['cta_type'])) {
          $post_type = $this->lang->line("FB Post (CTA)");
          $edit_url = base_url('ultrapost/edit_cta_post/'.$value['id']);
        }
        elseif(isset($value['email_api_table'])) {
          $post_type = 'Email Campaign';
          $edit_url = base_url('sms_email_manager/edit_email_campaign/'.$value['id']);
        }
        elseif(isset($value['sms_api_table'])) {
          $post_type = 'SMS Campaign';
          $edit_url = base_url('sms_email_manager/edit_sms_campaign/'.$value['id']);
        }
        elseif(isset($value['campaign_type'])) {
          $post_type = $this->lang->line("Social Post")." (".ucfirst($this->lang->line($value['campaign_type'])).")";
          $edit_url = base_url('comboposter/text_post/edit/'.$value['id']);
        }
        elseif(isset($value['stream_url'])) {
          $post_type = $this->lang->line("Live Streaming");
          $edit_url = base_url('vidcasterlive/edit_live_scheduler/'.$value['id']);
        }

        if(isset($value['total_thread']) && isset($value['successfully_sent'])){
          $broadcast_status = '<div class="text-time">'.$this->lang->line("Sent").' : '.$value['successfully_sent'].'/'.$value['total_thread'].'</div>';
        }
        if(isset($value['image_url'])) $media_preview = '<img src="'.$value['image_url'].'" class="img-fluid">';     

        $data['data'][$key]['title'] = $post_type;
        if($posting_status == '1' || $posting_status == "processing") {
          $data['data'][$key]['color'] = "#ffc107";
          $data['data'][$key]['className'] = "text-warning";
          $data['data'][$key]['url']='#';

          if(strlen($value['description']) > 125) $str = substr($value['description'],0,120).'........';
          else $str = $value['description'];
          $data['data'][$key]['description'] = '<ul class="list-unstyled list-unstyled-border list-unstyled-noborder event_description">
                    <li class="media">
                      <div class="media-body">
                        <div class="media-right"><div class="text-small"><span class="badge badge-pill badge-warning">'.$this->lang->line("Processing").'</span></div></div>
                        <div class="media-title mb-1">'.$value['campaign_name'].'</div>
                        <div class="text-time">'.date('h:i a',strtotime($value['schedule_time'])).'</div>
                        '.$broadcast_status.'
                        '.$media_preview.'
                        <hr>
                        <div class="media-description text-muted">'.$str.'</div>
                      </div>
                    </li>
                  </ul>';
                  
        }
        elseif($posting_status == '2' || $posting_status == "completed") {
          $data['data'][$key]['color'] = "#09ce2a";
          $data['data'][$key]['className'] = "text-success"; 
          $data['data'][$key]['url']='#';

          if(strlen($value['description']) > 125) $str = substr($value['description'],0,120).'........';
          else $str = $value['description'];
          $data['data'][$key]['description'] = '<ul class="list-unstyled list-unstyled-border list-unstyled-noborder event_description">
                    <li class="media">
                      <div class="media-body">
                        <div class="media-right"><div class="text-small"><span class="badge badge-pill badge-success">'.$this->lang->line("Completed").'</span></div></div>
                        <div class="media-title mb-1">'.$value['campaign_name'].'</div>
                        <div class="text-time">'.date('h:i a',strtotime($value['schedule_time'])).'</div>
                        '.$broadcast_status.'
                        '.$media_preview.'
                        <hr>
                        <div class="media-description text-muted">'.$str.'</div>                        
                      </div>
                    </li>
                  </ul>';
                  
        }
        elseif($posting_status == '3' || $posting_status == "stopped") {
          $data['data'][$key]['color'] = "#4CAF50";
          $data['data'][$key]['className'] = "text-secondary";
          $data['data'][$key]['url']='#';

          if(strlen($value['description']) > 125) $str = substr($value['description'],0,120).'........';
          else $str = $value['description'];
          $data['data'][$key]['description'] = '<ul class="list-unstyled list-unstyled-border list-unstyled-noborder event_description">
                    <li class="media">
                      <div class="media-body">
                        <div class="media-right"><div class="text-small"><span class="badge badge-pill badge-secondary">'.$this->lang->line("Stopped").'</span></div></div>
                        <div class="media-title mb-1">'.$value['campaign_name'].'</div>
                        <div class="text-time">'.date('h:i a',strtotime($value['schedule_time'])).'</div>
                        '.$broadcast_status.'
                        '.$media_preview.'
                        <hr>
                        <div class="media-description text-muted">'.$str.'</div>
                      </div>
                    </li>
                  </ul>';
             
        }
        else{
            $data['data'][$key]['color'] = "#fc544b";
            $data['data'][$key]['className'] = "text-danger";
            if(strlen($value['description']) > 125) $str = substr($value['description'],0,120).'........';
            else $str = $value['description'];
            $data['data'][$key]['description'] = '<ul class="list-unstyled list-unstyled-border list-unstyled-noborder event_description">
                      <li class="media">
                        <div class="media-body">
                          <div class="media-right"><div class="text-small"><span class="badge badge-pill badge-danger">'.$this->lang->line("Pending").'</span></div></div>
                          <div class="media-title mb-1">'.$value['campaign_name'].'</div>
                          <div class="text-time">'.date('h:i a',strtotime($value['schedule_time'])).'</div>
                          '.$broadcast_status.'
                          '.$media_preview.'
                          <hr>
                          <div class="media-description text-muted">'.$str.'</div>
                        </div>
                      </li>
                    </ul>';

          $data['data'][$key]['url']=$edit_url;
        }


      }

      echo json_encode($data);


    }
}
