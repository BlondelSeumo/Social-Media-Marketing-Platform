<?php
require_once("application/controllers/Home.php"); // loading home controller
class Page_analytics extends Home
{
    public function __construct()
    {
        parent::__construct();
        $this->member_validity();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location'); 
        
    }

    public function analytics( $page_auto_id = "0" )
    {
        $from_date = $this->input->post('from_date',true);
        $to_date = $this->input->post('to_date',true);

    	$error_message = '';
    	$today = date("Y-m-d");
    	$page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_auto_id,"user_id"=>$this->user_id)));
    	$page_access_token = isset($page_info[0]["page_access_token"]) ? $page_info[0]["page_access_token"] : "";
    	$page_id = isset($page_info[0]["page_id"]) ? $page_info[0]["page_id"] : "";
    	
    	$this->load->library("Fb_rx_login");

        $today = date("Y-m-d");
        if($from_date == '') $from_date = date("Y-m-d", strtotime("$today - 27 days"));
        if($to_date == '') $to_date = date("Y-m-d");


    	/* Page and Post Stories and "People talking about this */
    	$page_post_stories_analytics_data = $this->fb_rx_login->page_insights($page_access_token,$page_id,$from_date,$to_date);
   		//echo "<pre>";print_r($page_post_stories_analytics_data);exit;
    	if( isset($page_post_stories_analytics_data['error']) && $error_message == '' ){


			if(!isset($page_info[0])) $error_message .= "Page information not found.";
    		if(isset($page_post_stories_analytics_data['message'])) $error_message.=$page_post_stories_analytics_data['message'].". ";
           
    	}

    	$week_date_array=array();
    	$month_date_array=array();
    	for( $i=2; $i <=30 ; $i++ ) 
    	{ 
    	   $temp_date = date('Y-m-d', strtotime($today. " - $i days"));
    	   if( $i<=7 ) array_push($week_date_array, $temp_date); 
    	   array_push($month_date_array, $temp_date); 
    	}
    	//Page Page and Post Stories and "People talking about this"
        $page_content_activity_by_action_type_unique_temp = array();
        $page_content_activity_temp = array();


        //Page Impressions
        $page_impressions_temp =array();
        $page_impressions_unique_temp = array();
        $page_impressions_paid_temp = array();
        $page_impressions_paid_unique_temp = array();
        $page_impressions_organic_temp = array();
        $page_impressions_organic_unique_temp = array();
        $page_impressions_viral_temp = array();
        $page_impressions_viral_unique_temp = array();
        $page_impressions_nonviral_temp = array();
        $page_impressions_nonviral_unique_temp = array();
        $page_impressions_by_country_unique_temp = array();

        //Page Engagement
        $page_engaged_users_temp = array();
        $page_post_engagements_temp = array();
        $page_consumptions_temp = array();
        $page_consumptions_unique_temp = array();
		$page_places_checkin_total_temp = array();
        $page_negative_feedback_temp = array();
        $page_positive_feedback_by_type_temp = array();
		$page_fans_online_per_day_temp = array();

		//Page Reactions
		$page_actions_post_reactions_like_total_temp = array();
		$page_actions_post_reactions_love_total_temp = array();
		$page_actions_post_reactions_wow_total_temp = array();
		$page_actions_post_reactions_haha_total_temp = array();
		$page_actions_post_reactions_sorry_total_temp = array();
		$page_actions_post_reactions_anger_total_temp = array();
		
		//Page CTA Clicks
		$page_total_actions_temp = array();
		$page_cta_clicks_logged_in_total_temp = array();
		$page_call_phone_clicks_logged_in_unique_temp = array();
		$page_get_directions_clicks_logged_in_unique_temp = array();
		$page_website_clicks_logged_in_unique_temp = array();
		$page_website_clicks_by_site_logged_in_unique_temp = array();

		// Page User Demographics
		$page_fans_temp = array();
        $page_fans_country_temp = array();
        $page_fan_adds_temp = array();
        $page_fans_by_like_source_temp = array();
        $page_fan_removes_temp = array();
        $page_fans_by_unlike_source_unique_temp = array();

        // Page Content 
        $page_tab_views_login_top_temp = array();

        //Page Views
        $page_views_total_temp = array();
        $page_views_by_profile_tab_total_temp = array();
        $page_views_by_site_logged_in_unique_temp =  array();
        $page_views_by_referers_logged_in_unique_temp =  array();

        //Page Video Views
        $page_video_views_temp = array();
        $page_video_views_paid_temp = array();
        $page_video_views_organic_temp = array();
        $page_video_views_autoplayed_temp = array();
        $page_video_views_click_to_play_temp = array();
        $page_video_views_unique_temp = array();
        $page_video_view_time_temp = array();

        //Page Post Impressions
        $page_posts_impressions_viral_temp = array();
        $page_posts_impressions_nonviral_temp = array();
        $page_posts_impressions_paid_temp = array();
        $page_posts_impressions_organic_temp = array();
        $page_posts_impressions_temp = array();

        if( isset($page_post_stories_analytics_data['data']) ){
        	foreach ( $page_post_stories_analytics_data['data'] as $key => $value ) {

        		 //Page Page and Post Stories and "People talking about this"
        		 if( $value['name'] == 'page_content_activity_by_action_type_unique' )
        		 	$page_content_activity_by_action_type_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_content_activity' )
        		 	$page_content_activity_temp = $value['values'];

        		 //Page Impressions
        		 if( $value['name'] == 'page_impressions' )
        		 	$page_impressions_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_unique' )
        		 	$page_impressions_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_paid' )
        		 	$page_impressions_paid_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_paid_unique' )
        		 	$page_impressions_paid_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_organic' )
        		 	 $page_impressions_organic_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_organic_unique' )
        		 	 $page_impressions_organic_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_viral' )
        		 	$page_impressions_viral_temp = $value['values'];
        		 if(  $value['name'] == 'page_impressions_viral_unique' )
        		 	$page_impressions_viral_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_nonviral' )
        		 	$page_impressions_nonviral_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_nonviral_unique' )
        		 	$page_impressions_nonviral_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_impressions_by_country_unique' )
        		 	$page_impressions_by_country_unique_temp = $value['values'];

        		 //Page Engagement
        		 if( $value['name'] == 'page_engaged_users' )
        		 	$page_engaged_users_temp = $value['values'];
        		 if( $value['name'] == 'page_post_engagements' )
        		 	$page_post_engagements_temp = $value['values'];
        		 if( $value['name'] == 'page_consumptions' )
        		 	$page_consumptions_temp = $value['values'];
        		 if( $value['name'] == 'page_consumptions_unique')
        		 	$page_consumptions_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_places_checkin_total' )
        		 	$page_places_checkin_total_temp = $value['values'];
        		 if( $value['name'] == 'page_negative_feedback' )
        		 	$page_negative_feedback_temp = $value['values'];
        		 if( $value['name'] == 'page_positive_feedback_by_type' )
        		 	$page_positive_feedback_by_type_temp = $value['values'];
        		 if( $value['name'] == 'page_fans_online_per_day' )
        		 	$page_fans_online_per_day_temp = $value['values'];

        		 //Page Reaction 
        		 if(  $value['name'] == 'page_actions_post_reactions_like_total' )
        		 	$page_actions_post_reactions_like_total_temp = $value['values'];
        		 if( $value['name'] == 'page_actions_post_reactions_love_total' )
        		 	$page_actions_post_reactions_love_total_temp = $value['values'];
        		 if( $value['name'] == 'page_actions_post_reactions_wow_total' )
        		 	$page_actions_post_reactions_wow_total_temp = $value['values'];
        		 if( $value['name'] == 'page_actions_post_reactions_haha_total' )
        		 	$page_actions_post_reactions_haha_total_temp = $value['values'];
        		 if( $value['name'] == 'page_actions_post_reactions_sorry_total' )
        		 	$page_actions_post_reactions_sorry_total_temp = $value['values'];
        		 if( $value['name'] == 'page_actions_post_reactions_anger_total' )
        		 	$page_actions_post_reactions_anger_total_temp = $value['values'];

        		 //Page CTA Clicks
        		 if( $value['name'] == 'page_total_actions' )
        		 	$page_total_actions_temp = $value['values'];
        		 if( $value['name'] == 'page_cta_clicks_logged_in_total' )
        		 	$page_cta_clicks_logged_in_total_temp = $value['values'];
        		 if( $value['name'] == 'page_call_phone_clicks_logged_in_unique')
        		 	$page_call_phone_clicks_logged_in_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_get_directions_clicks_logged_in_unique' )
        		 	$page_get_directions_clicks_logged_in_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_website_clicks_logged_in_unique')
        		 	$page_website_clicks_logged_in_unique_temp = $value['values'];
        		 if( $value['name'] == 'page_website_clicks_by_site_logged_in_unique')
        		 	$page_website_clicks_by_site_logged_in_unique_temp = $value['values'];

                 //Page User Demographics 
                 if( $value['name'] == 'page_fans' )
                   $page_fans_temp = $value['values'];
                 if( $value['name'] == 'page_fans_country' )
                    $page_fans_country_temp = $value['values'];
                 if( $value['name'] == 'page_fan_adds')
                    $page_fan_adds_temp = $value['values'];
                 if( $value['name']  == 'page_fans_by_like_source' )
                    $page_fans_by_like_source_temp = $value['values'];
                 if( $value['name'] == 'page_fan_removes' )
                    $page_fan_removes_temp = $value['values'];
                 if( $value['name'] == 'page_fans_by_unlike_source_unique' )
                    $page_fans_by_unlike_source_unique_temp = $value['values'];

                // Page Content
                 if( $value['name'] == 'page_tab_views_login_top' )
                    $page_tab_views_login_top_temp = $value['values'];

                //Page Views

                 if( $value['name'] == 'page_views_total' )
                    $page_views_total_temp = $value['values'];
                 if( $value['name'] == 'page_views_by_profile_tab_total')
                    $page_views_by_profile_tab_total_temp = $value['values'];
                 if( $value['name'] == 'page_views_by_site_logged_in_unique')
                    $page_views_by_site_logged_in_unique_temp = $value['values'];
                 if( $value['name'] == 'page_views_by_referers_logged_in_unique')
                    $page_views_by_referers_logged_in_unique_temp = $value['values'];

                //Page Video Views
                 if( $value['name'] == 'page_video_views_paid' )
                    $page_video_views_paid_temp = $value['values'];
                 if( $value['name'] == 'page_video_views_organic' )
                    $page_video_views_organic_temp = $value['values'];
                 if( $value['name'] == 'page_video_views' )
                    $page_video_views_temp = $value['values'];
                 if( $value['name'] == 'page_video_views_autoplayed' )
                    $page_video_views_autoplayed_temp = $value['values'];
                 if( $value['name'] == 'page_video_views_click_to_play' )
                    $page_video_views_click_to_play_temp = $value['values'];
                 if( $value['name'] == 'page_video_views_unique')
                    $page_video_views_unique_temp = $value['values'];
                 if ( $value['name'] == 'page_video_view_time')
                    $page_video_view_time_temp = $value['values'];

                //Page Post Impressions
                 if( $value['name'] == 'page_posts_impressions_viral' )
                    $page_posts_impressions_viral_temp = $value['values'];
                 if( $value['name'] == 'page_posts_impressions_nonviral' )
                    $page_posts_impressions_nonviral_temp = $value['values'];
                 if( $value['name'] == 'page_posts_impressions_paid' )
                    $page_posts_impressions_paid_temp = $value['values'];
                 if( $value['name'] == 'page_posts_impressions_organic' )
                    $page_posts_impressions_organic_temp = $value['values'];
                 if( $value['name'] == 'page_posts_impressions' )
                    $page_posts_impressions_temp = $value['values'];
        	}
        }

        //Page Page and Post Stories and "People talking about this"
        $page_content_activity_unique_summary=array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_content_activity_unique = array();

    	$i = 0;
    	foreach ($page_content_activity_temp as $key => $value) {
    		$page_content_activity_unique[$i]["value"] = isset($value["value"]) ? $value["value"] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_content_activity_unique [$i]['date'] =date('Y-m-d',strtotime($date_convert['date']));

    		if($i==0) $page_content_activity_unique_summary['today'] = $page_content_activity_unique_summary['today']+$page_content_activity_unique[$i]['value'];

			if(in_array($page_content_activity_unique[$i]['date'], $week_date_array)) $page_content_activity_unique_summary['week']=$page_content_activity_unique_summary['week']+$page_content_activity_unique[$i]['value'];

    		if(in_array($page_content_activity_unique[$i]['date'], $month_date_array)) $page_content_activity_unique_summary['month']=$page_content_activity_unique_summary['month']+$page_content_activity_unique[$i]['value'];

    		$page_content_activity_unique_summary['search']=$page_content_activity_unique_summary['search']+$page_content_activity_unique[$i]['value'];

    		$i++;
    	}
   
    	$page_content_activity_by_action_type_unique_summary_fan = array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_content_activity_by_action_type_unique_fan = array();

    	$i = 0; 
    	foreach ($page_content_activity_by_action_type_unique_temp as $key => $value) {
    		
    		$page_content_activity_by_action_type_unique_fan[$i]['page_story_fan'] = isset($value['value']['fan']) ? $value['value']['fan'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_content_activity_by_action_type_unique_fan[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));

    		if($i==0) $page_content_activity_by_action_type_unique_summary_fan['today'] = $page_content_activity_by_action_type_unique_summary_fan['today']+$page_content_activity_by_action_type_unique_fan[$i]['page_story_fan'];

    		if(in_array($page_content_activity_by_action_type_unique_fan[$i]['date'], $week_date_array)) $page_content_activity_by_action_type_unique_summary_fan['week'] = $page_content_activity_by_action_type_unique_summary_fan['week']+$page_content_activity_by_action_type_unique_fan[$i]['page_story_fan'];

    		if(in_array($page_content_activity_by_action_type_unique_fan[$i]['date'], $month_date_array)) $page_content_activity_by_action_type_unique_summary_fan['month'] = $page_content_activity_by_action_type_unique_summary_fan['month']+$page_content_activity_by_action_type_unique_fan[$i]['page_story_fan'];

    		$page_content_activity_by_action_type_unique_summary_fan['search'] = $page_content_activity_by_action_type_unique_summary_fan['search']+$page_content_activity_by_action_type_unique_fan[$i]['page_story_fan'];

    		$i++;
    	}
    	
    	$page_content_activity_by_action_type_unique_summary_other = array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_content_activity_by_action_type_unique_other = array();

    	$i = 0;
    	foreach ($page_content_activity_by_action_type_unique_temp as $key => $value) {
    		$page_content_activity_by_action_type_unique_other[$i]['page_story_other'] = isset($value['value']['other']) ? $value['value']['other'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_content_activity_by_action_type_unique_other[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));

    		if($i==0) $page_content_activity_by_action_type_unique_summary_other['today'] = $page_content_activity_by_action_type_unique_summary_other['today']+$page_content_activity_by_action_type_unique_other[$i]['page_story_other'];

    		if(in_array($page_content_activity_by_action_type_unique_other[$i]['date'], $week_date_array)) $page_content_activity_by_action_type_unique_summary_other['week'] = $page_content_activity_by_action_type_unique_summary_other['week']+$page_content_activity_by_action_type_unique_other[$i]['page_story_other'];

    		if(in_array($page_content_activity_by_action_type_unique_other[$i]['date'], $month_date_array)) $page_content_activity_by_action_type_unique_summary_other['month'] = $page_content_activity_by_action_type_unique_summary_other['month']+$page_content_activity_by_action_type_unique_other[$i]['page_story_other'];
    		$page_content_activity_by_action_type_unique_summary_other['search'] = $page_content_activity_by_action_type_unique_summary_other['search']+$page_content_activity_by_action_type_unique_other[$i]['page_story_other'];

    		$i++;
    	}

    	$page_content_activity_by_action_type_unique_summary_page_post = array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_content_activity_by_action_type_unique_page_post = array();

    	$i = 0;
    	foreach ($page_content_activity_by_action_type_unique_temp as $key => $value) {
    		$page_content_activity_by_action_type_unique_page_post[$i]['page_story_page_post'] = isset($value['value']['page post']) ? $value['value']['page post'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_content_activity_by_action_type_unique_page_post[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		if($i==0) $page_content_activity_by_action_type_unique_summary_page_post['today'] = $page_content_activity_by_action_type_unique_summary_page_post['today']+$page_content_activity_by_action_type_unique_page_post[$i]['page_story_page_post'];

    		if(in_array($page_content_activity_by_action_type_unique_page_post[$i]['date'], $week_date_array)) $page_content_activity_by_action_type_unique_summary_page_post['week'] = $page_content_activity_by_action_type_unique_summary_page_post['week']+$page_content_activity_by_action_type_unique_page_post[$i]['page_story_page_post'];

    		if(in_array($page_content_activity_by_action_type_unique_page_post[$i]['date'], $month_date_array)) $page_content_activity_by_action_type_unique_summary_page_post['month'] = $page_content_activity_by_action_type_unique_summary_page_post['month']+$page_content_activity_by_action_type_unique_page_post[$i]['page_story_page_post'];

    		$page_content_activity_by_action_type_unique_summary_page_post['search'] = $page_content_activity_by_action_type_unique_summary_page_post['search']+$page_content_activity_by_action_type_unique_page_post[$i]['page_story_page_post'];


    		$i++;
    	}
    	
    	/**
    	 * All array values sum and store like an array.
    	 * @param  array $first  
    	 * @param  array $second    
    	 * @param  array $third  
    	 * @param  array $fourth 
    	 * @return array
    	 */
    	function merge_arr($first, $second, $third, $fourth)
    	{
    	    return array(
    	    	"value" => $first['value'] + $second['page_story_fan'] + $third['page_story_other'] + $fourth['page_story_page_post'],
    	    	"date" => $first['date']
    	    );

    	}


    	$page_and_post_stories_final_array = array_map('merge_arr', $page_content_activity_unique, $page_content_activity_by_action_type_unique_fan, $page_content_activity_by_action_type_unique_other, $page_content_activity_by_action_type_unique_page_post);

    	//Page Impressions
    	$page_impressions_summary=array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_impressions = array();

    	$i = 0;
    	foreach ( $page_impressions_temp as $key => $value ) {
    		$page_impressions[$i]["value"] = isset($value["value"]) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions[$i]['date'] =date('Y-m-d',strtotime($date_convert['date']));

    		if( $i == 0 ) $page_impressions_summary['today'] = $page_impressions_summary['today']+$page_impressions[$i]['value'];

    		if( in_array($page_impressions[$i]['date'], $week_date_array) ) $page_impressions_summary['week'] = $page_impressions_summary['week']+$page_impressions[$i]['value'];
    		if( in_array($page_impressions[$i]['date'], $month_date_array) ) $page_impressions_summary['month'] = $page_impressions_summary['month']+$page_impressions[$i]['value'];
    		$page_impressions_summary['search'] = $page_impressions_summary['search']+ $page_impressions[$i]['value'];

    		$i++;
    	}

    	$page_impressions_unique_summary = array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_impressions_unique = array();

    	$i = 0;
    	foreach ( $page_impressions_unique_temp as $key => $value ) {
    		$page_impressions_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));

    		if( $i == 0) $page_impressions_unique_summary['today'] = $page_impressions_unique_summary['today']+$page_impressions_unique[$i]['value'];
    		if( in_array($page_impressions_unique[$i]['date'], $week_date_array) ) $page_impressions_unique_summary['week']=
    			$page_impressions_unique_summary['week']+$page_impressions_unique[$i]['value'];
    		if( in_array($page_impressions_unique[$i]['date'], $month_date_array) ) $page_impressions_unique_summary['month'] = $page_impressions_unique_summary['month']+$page_impressions_unique[$i]['value'];

    		$page_impressions_unique_summary['search'] = $page_impressions_unique_summary['search']+$page_impressions_unique[$i]['value'];
    		$i++;
    	}

    	$page_impressions_paid_summary = array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_impressions_paid = array();

    	$i = 0;
    	foreach ($page_impressions_paid_temp as $key => $value) {
    		$page_impressions_paid[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_paid[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));

    		if( $i==0 ) 
    			$page_impressions_paid_summary['today'] = $page_impressions_paid_summary['today']+$page_impressions_paid[$i]['value'];
    		if( in_array($page_impressions_paid[$i]['date'], $week_date_array) ) $page_impressions_paid_summary['week'] = $page_impressions_paid_summary['week']+$page_impressions_paid[$i]['value'];
    		if( in_array($page_impressions_paid[$i]['date'], $month_date_array)) $page_impressions_paid_summary['month'] = $page_impressions_paid_summary['month']+$page_impressions_paid[$i]['value'];
    		$page_impressions_paid_summary['search'] = $page_impressions_paid_summary['search']+$page_impressions_paid[$i]['value'];
    		$i++;
    	}

    	$page_impressions_paid_unique_summary = array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_impressions_paid_unique = array();

    	$i = 0;
    	foreach ($page_impressions_paid_unique_temp as $key => $value) {
    		$page_impressions_paid_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_paid_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));

    		if( $i == 0) $page_impressions_paid_unique_summary['today'] = $page_impressions_paid_unique_summary['today']+$page_impressions_paid_unique[$i]['value'];
    		if( in_array($page_impressions_paid_unique[$i]['date'], $week_date_array) ) $page_impressions_paid_unique_summary['week'] = $page_impressions_paid_unique_summary['week']+ $page_impressions_paid_unique[$i]['value'];
    		if( in_array($page_impressions_paid_unique[$i]['date'], $month_date_array) ) $page_impressions_paid_unique_summary['month'] = $page_impressions_paid_unique_summary['month']+$page_impressions_paid_unique[$i]['value'];
    		$page_impressions_paid_unique_summary['search'] = $page_impressions_paid_unique_summary['search']+$page_impressions_paid_unique[$i]['value'];
    		$i++;
    	}


    	$page_impressions_organic_summary = array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_impressions_organic = array();

    	$i = 0;
    	foreach ($page_impressions_organic_temp as $key => $value) {
    		$page_impressions_organic[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_organic[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));

    		if( $i == 0) $page_impressions_organic_summary['today'] = $page_impressions_organic_summary['today']+$page_impressions_organic[$i]['value'];
    		if( in_array($page_impressions_organic[$i]['date'], $week_date_array) ) $page_impressions_organic_summary['week'] = $page_impressions_organic_summary['week']+ $page_impressions_organic[$i]['value'];
    		if( in_array($page_impressions_organic[$i]['date'], $month_date_array) ) $page_impressions_organic_summary['month'] = $page_impressions_organic_summary['month']+$page_impressions_organic[$i]['value'];
    		$page_impressions_organic_summary['search'] = $page_impressions_organic_summary['search']+$page_impressions_organic[$i]['value'];
    		$i++;
    	}

    	$page_impressions_organic_unique_summary = array("today"=>0,"week"=>0,"month"=>0,"search"=>0);
    	$page_impressions_organic_unique = array();

    	$i = 0;
    	foreach ($page_impressions_organic_unique_temp as $key => $value) {

    		$page_impressions_organic_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_organic_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));

    		if( $i == 0) $page_impressions_organic_unique_summary['today'] = $page_impressions_organic_unique_summary['today']+$page_impressions_organic_unique[$i]['value'];
    		if( in_array($page_impressions_organic_unique[$i]['date'], $week_date_array) ) $page_impressions_organic_unique_summary['week'] = $page_impressions_organic_unique_summary['week']+ $page_impressions_organic_unique[$i]['value'];
    		if( in_array($page_impressions_organic_unique[$i]['date'], $month_date_array) ) $page_impressions_organic_unique_summary['month'] = $page_impressions_organic_unique_summary['month']+$page_impressions_organic_unique[$i]['value'];
    		$page_impressions_organic_unique_summary['search'] = $page_impressions_organic_unique_summary['search']+$page_impressions_organic_unique[$i]['value'];
    		$i++;
    	}
    	
    	/**
    	 * Those section not implement yet today, week, month, search
    	 * If Needed I will do it 
    	 */
    	$page_impressions_viral = array();
    	$i = 0;
    	foreach ($page_impressions_viral_temp as $key => $value) {
    		$page_impressions_viral[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_viral[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_impressions_viral_unique = array();
    	$i = 0;
    	foreach ($page_impressions_viral_unique_temp as $key => $value) {
    		$page_impressions_viral_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_viral_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_impressions_nonviral = array();
    	$i = 0;
    	foreach ($page_impressions_nonviral_temp as $key => $value) {
    		$page_impressions_nonviral[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_nonviral[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_impressions_nonviral_unique = array();
    	$i = 0;
    	foreach ($page_impressions_nonviral_unique_temp as $key => $value) {
    		$page_impressions_nonviral_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_nonviral_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}
    	
    	$page_impressions_by_country_unique_total = array();
    	$i = 0;
    	foreach ($page_impressions_by_country_unique_temp as $key => $value) {

            $top_ten_country_impression = isset($value['value']) ? $value['value'] : 0;
            arsort($top_ten_country_impression);
            $highest_ten = array_slice($top_ten_country_impression, 0, 10, true);
    		$page_impressions_by_country_unique_total[$i]['value'] = $highest_ten;
    		$date_convert = (array) $value["end_time"];
    		$page_impressions_by_country_unique_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}
        
    	//Page Engagement
    	$page_engaged_users = array();
    	$i = 0;
    	foreach ($page_engaged_users_temp as $key => $value) {
    		
    		$page_engaged_users[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_engaged_users[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_post_engagements = array();
    	$i = 0;
    	foreach ($page_post_engagements_temp as $key => $value) {
    		$page_post_engagements[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_post_engagements[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}
    	
    	$page_consumptions = array();
    	$i = 0;
    	foreach ($page_consumptions_temp as $key => $value) {
    		$page_consumptions[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_consumptions[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_consumptions_unique = array();
    	$i = 0;
    	foreach ($page_consumptions_unique_temp as $key => $value) {
    		$page_consumptions_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_consumptions_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_places_checkin_total = array();
    	$i = 0;
    	foreach ($page_places_checkin_total_temp as $key => $value) {
    		$page_places_checkin_total[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_places_checkin_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_negative_feedback = array();
    	$i = 0;
    	foreach ($page_negative_feedback_temp as $key => $value) {
    		$page_negative_feedback[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_negative_feedback[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_positive_feedback_by_type_total = array();
    	$i = 0;
    	foreach ($page_positive_feedback_by_type_temp as $key => $value) {
    		$total_postive_feedback = isset($value['value']) ? $value['value'] : array();
    		$page_positive_feedback_by_type_total[$i]['value'] = array_sum($total_postive_feedback);
    		$date_convert = (array) $value["end_time"];
    		$page_positive_feedback_by_type_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_fans_online_per_day = array();

    	foreach ($page_fans_online_per_day_temp as $key => $value) {
    		
    		$page_fans_online_per_day[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_fans_online_per_day[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	//Page Reaction
    	
    	$page_actions_post_reactions_like_total = array();
    	$i = 0;
    	foreach ($page_actions_post_reactions_like_total_temp as $key => $value) {
    		$page_actions_post_reactions_like_total[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_actions_post_reactions_like_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_actions_post_reactions_love_total = array();
    	$i = 0;
    	foreach ($page_actions_post_reactions_love_total_temp as $key => $value) {
    		$page_actions_post_reactions_love_total[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_actions_post_reactions_love_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_actions_post_reactions_wow_total = array();
    	$i = 0;
    	foreach ($page_actions_post_reactions_wow_total_temp as $key => $value) {
    		
    		$page_actions_post_reactions_wow_total[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_actions_post_reactions_wow_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_actions_post_reactions_haha_total = array();
    	$i = 0;
    	foreach ($page_actions_post_reactions_haha_total_temp as $key => $value) {
    		$page_actions_post_reactions_haha_total[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_actions_post_reactions_haha_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_actions_post_reactions_sorry_total = array();
    	$i = 0;
    	foreach ($page_actions_post_reactions_sorry_total_temp as $key => $value) {
    		$page_actions_post_reactions_sorry_total[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_actions_post_reactions_sorry_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_actions_post_reactions_anger_total = array();
    	$i = 0;
    	foreach ($page_actions_post_reactions_anger_total_temp as $key => $value) {
    		$page_actions_post_reactions_anger_total[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_actions_post_reactions_anger_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	//Page Cta Clicks
    	
    	$page_total_actions = array();
    	$i = 0;
    	foreach ($page_total_actions_temp as $key => $value) {
    		$page_total_actions[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_total_actions[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_cta_clicks_logged_in_total = array();
    	foreach ($page_cta_clicks_logged_in_total_temp as $key => $value) {
    		$total_cta_clicks_logged_in = isset($value['value']) ? $value['value'] : array();
    		$page_cta_clicks_logged_in_total[$i]['value'] = array_sum($total_cta_clicks_logged_in);
    		$date_convert = (array) $value["end_time"];
    		$page_cta_clicks_logged_in_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_call_phone_clicks_logged_in_unique = array();
    	$i = 0;
    	foreach ($page_call_phone_clicks_logged_in_unique_temp as $key => $value) {
    		
    		$page_call_phone_clicks_logged_in_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_call_phone_clicks_logged_in_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;

    	}

    	$page_get_directions_clicks_logged_in_unique = array();
    	$i = 0;
    	foreach ($page_get_directions_clicks_logged_in_unique_temp as $key => $value) {
    		$page_get_directions_clicks_logged_in_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_get_directions_clicks_logged_in_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

    	$page_website_clicks_logged_in_unique = array();
    	$i = 0;
    	foreach ($page_website_clicks_logged_in_unique_temp as $key => $value) {
    		$page_website_clicks_logged_in_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
    		$date_convert = (array) $value["end_time"];
    		$page_website_clicks_logged_in_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}


       
    	$page_website_clicks_by_site_logged_in_unique = array();
    	$i = 0;
    	foreach ($page_website_clicks_by_site_logged_in_unique_temp as $key => $value) {

    		$page_website_clicks_by_site_logged_in_unique[$i]['value'] = isset($value['value']) ? $value['value'] : array();
    		$date_convert = (array) $value["end_time"];
    		$page_website_clicks_by_site_logged_in_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
    		$i++;
    	}

        //Page User Demographics 
        $page_fans = array();
        $i = 0;
        foreach ($page_fans_temp as $key => $value) {

            $page_fans[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_fans[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }
        //echo "<pre>";print_r($page_fans);exit;

        $page_fans_country = array();
        $i = 0;
        foreach ($page_fans_country_temp as $key => $value) {
          
             $top_ten_country = isset($value['value']) ? $value['value'] : 0;
             arsort($top_ten_country);
             $highest_ten = array_slice($top_ten_country, 0, 10, true);
             $page_fans_country[$i]['country'] = $highest_ten;
             $date_convert = (array) $value["end_time"];
             $page_fans_country[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
             $i++;
        }
    

        $page_fan_adds = array();
        $i = 0;
        foreach ($page_fan_adds_temp as $key => $value) {
            

            $page_fan_adds[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_fan_adds[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        $page_fan_removes = array();
        $i = 0;
        foreach ($page_fan_removes_temp as $key => $value) {

            $page_fan_removes[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_fan_removes[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }
        
        // Page Content
        $page_tab_views_login_top = array();
        $i = 0;
        foreach ($page_tab_views_login_top_temp as $key => $value) {

            $page_tab_views_login_top[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_tab_views_login_top[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        //Page Views
        $page_views_by_profile_tab_total = array();
        $i = 0;
        foreach ($page_views_by_profile_tab_total_temp as $key => $value) {
            
             $page_views_by_profile_tab_total[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
             $date_convert = (array) $value["end_time"];
             $page_views_by_profile_tab_total[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
             $i++;
        }

        $page_views_by_site_logged_in_unique = array();
        $i = 0;
        foreach ($page_views_by_site_logged_in_unique_temp as $key => $value) {

            $page_views_by_site_logged_in_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_views_by_site_logged_in_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }
        // echo "<pre>"; print_r($page_views_by_site_logged_in_unique_temp); exit;

        $page_views_by_referers_logged_in_unique = array();
        $i = 0;
        foreach ($page_views_by_referers_logged_in_unique_temp as $key => $value) {

            $page_views_by_referers_logged_in_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_views_by_referers_logged_in_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        // Page Video Views
        $page_video_views_paid = array();
        $i = 0;
        foreach ($page_video_views_paid_temp as $key => $value) {
           
           $page_video_views_paid[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
           $date_convert = (array) $value["end_time"];
           $page_video_views_paid[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
           $i++;
        }

        $page_video_views_organic = array();
        $i = 0;
        foreach ($page_video_views_organic_temp as $key => $value) {
            $page_video_views_organic[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_video_views_organic[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        $page_video_views = array();
        $i = 0;
        foreach ($page_video_views_temp as $key => $value) {
            
            $page_video_views[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_video_views[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        $page_video_views_autoplayed = array();
        $i = 0;
        foreach ($page_video_views_autoplayed_temp as $key => $value) {
            $page_video_views_autoplayed[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_video_views_autoplayed[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        $page_video_views_click_to_play = array();
        $i = 0;
        foreach ($page_video_views_click_to_play_temp as $key => $value) {
           
           $page_video_views_click_to_play[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
           $date_convert = (array) $value["end_time"];
           $page_video_views_click_to_play[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
           $i++;
        }

        $page_video_views_unique = array();
        $i =0;
        foreach ($page_video_views_unique_temp as $key => $value) {
            
            $page_video_views_unique[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_video_views_unique[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        $page_video_view_time = array();
        $i=0;
        foreach ($page_video_view_time_temp as $key => $value) {

            $page_video_view_time[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_video_view_time[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        //Page Post Impressions
        $page_posts_impressions_viral = array();
        $i = 0;
        foreach ($page_posts_impressions_viral_temp as $key => $value) {
            $page_posts_impressions_viral[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_posts_impressions_viral[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        $page_posts_impressions_nonviral = array();
        $i = 0;
        foreach ($page_posts_impressions_nonviral_temp as $key => $value) {
            $page_posts_impressions_nonviral[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_posts_impressions_nonviral[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        $page_posts_impressions_paid = array();
        $i = 0;
        foreach ($page_posts_impressions_paid_temp as $key => $value) {
            
            $page_posts_impressions_paid[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_posts_impressions_paid[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }

        $page_posts_impressions_organic = array();
        $i = 0;
        foreach ($page_posts_impressions_organic_temp as $key => $value) {

            $page_posts_impressions_organic[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_posts_impressions_organic[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }
        
        $page_posts_impressions = array();
        $i = 0;
        foreach ($page_posts_impressions_temp as $key => $value) {      
            
            $page_posts_impressions[$i]['value'] = isset($value['value']) ? $value['value'] : 0;
            $date_convert = (array) $value["end_time"];
            $page_posts_impressions[$i]['date'] = date('Y-m-d',strtotime($date_convert['date']));
            $i++;
        }
        //echo "<pre>";print_r($page_posts_impressions);exit;
    	$data['page_and_post_stories_final_array'] = $page_and_post_stories_final_array;
    	$data['page_content_activity_unique'] = $page_content_activity_unique;
    	$data['page_content_activity_by_action_type_unique_fan'] = $page_content_activity_by_action_type_unique_fan;
    	$data['page_content_activity_by_action_type_unique_other'] = $page_content_activity_by_action_type_unique_other;
    	$data['page_content_activity_by_action_type_unique_page_post'] = $page_content_activity_by_action_type_unique_page_post;

    	//Page Impressions
    	$data['page_impressions'] = $page_impressions;
    	$data['page_impressions_unique'] = $page_impressions_unique;
    	$data['page_impressions_paid'] = $page_impressions_paid;
    	$data['page_impressions_paid_unique'] = $page_impressions_paid_unique;
    	$data['page_impressions_organic'] = $page_impressions_organic;
    	$data['page_impressions_organic_unique'] = $page_impressions_organic_unique;
		$data['page_impressions_viral'] = $page_impressions_viral;
    	$data['page_impressions_viral_unique'] = $page_impressions_viral_unique;
    	$data['page_impressions_nonviral'] = $page_impressions_nonviral;
    	$data['page_impressions_nonviral_unique'] = $page_impressions_nonviral_unique;
    	$data['page_impressions_by_country_unique_total'] = end($page_impressions_by_country_unique_total);
    	
    	// Page Engagement
    	$data['page_engaged_users'] = $page_engaged_users;
    	$data['page_post_engagements'] = $page_post_engagements;
    	$data['page_consumptions'] = $page_consumptions;
    	$data['page_consumptions_unique'] = $page_consumptions_unique;
    	$data['page_places_checkin_total'] = $page_places_checkin_total;
    	$data['page_negative_feedback'] = $page_negative_feedback;
    	$data['page_positive_feedback_by_type_total'] = $page_positive_feedback_by_type_total;
    	$data['page_fans_online_per_day'] = $page_fans_online_per_day;

    	//Page Reaction
    	$data['page_actions_post_reactions_like_total'] = $page_actions_post_reactions_like_total;
    	$data['page_actions_post_reactions_love_total'] = $page_actions_post_reactions_love_total;
    	$data['page_actions_post_reactions_wow_total'] = $page_actions_post_reactions_wow_total;
    	$data['page_actions_post_reactions_haha_total'] = $page_actions_post_reactions_haha_total;
    	$data['page_actions_post_reactions_sorry_total'] = $page_actions_post_reactions_sorry_total;
    	$data['page_actions_post_reactions_anger_total'] = $page_actions_post_reactions_anger_total;

    	//Page CTA Clicks
    	$data['page_total_actions'] = $page_total_actions;
    	$data['page_cta_clicks_logged_in_total'] = $page_cta_clicks_logged_in_total;
    	$data['page_call_phone_clicks_logged_in_unique'] = $page_call_phone_clicks_logged_in_unique;
    	$data['page_get_directions_clicks_logged_in_unique'] = $page_get_directions_clicks_logged_in_unique;
        $data['page_website_clicks_logged_in_unique'] = $page_website_clicks_logged_in_unique;
        $data['page_website_clicks_by_site_logged_in_unique'] = end($page_website_clicks_by_site_logged_in_unique);


        //Page User Demographics 
        $data['page_fans'] = $page_fans;
        $data['page_fans_country'] = end($page_fans_country);
        $data['page_fan_adds'] = $page_fan_adds;
        $data['page_fan_removes'] = $page_fan_removes;
        
        //Page Content
        $data['page_tab_views_login_top'] = end($page_tab_views_login_top);
        
        //Page View 
        $data['page_views_by_profile_tab_total'] = end($page_views_by_profile_tab_total);
        $data['page_views_by_site_logged_in_unique'] = end($page_views_by_site_logged_in_unique);
        $data['page_views_by_referers_logged_in_unique'] = end($page_views_by_referers_logged_in_unique);

        //Page Video Views
        $data['page_video_views_paid'] = $page_video_views_paid;
        $data['page_video_views_organic'] = $page_video_views_organic;
        $data['page_video_views'] = $page_video_views;
        $data['page_video_views_autoplayed'] = $page_video_views_autoplayed;
        $data['page_video_views_click_to_play'] = $page_video_views_click_to_play;
        $data['page_video_views_unique'] = $page_video_views_unique;
        $data['page_video_view_time'] = $page_video_view_time;

        //Page Post Impressions
        $data['page_posts_impressions_viral'] = $page_posts_impressions_viral;
        $data['page_posts_impressions_nonviral'] = $page_posts_impressions_nonviral;
        $data['page_posts_impressions_paid'] = $page_posts_impressions_paid;
        $data['page_posts_impressions_organic'] = $page_posts_impressions_organic;
        $data['page_posts_impressions'] = $page_posts_impressions;

        $data['error_message'] = $error_message;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
    	$data['page_info'] = isset($page_info[0])?$page_info[0]:array();
    	$data['body'] = 'page_analytics/view_page_analytics';
    	$page_name = isset($page_info[0]['page_name']) ? $page_info[0]['page_name'] : "";
    	$data['page_title'] =$page_name.' - '.$this->lang->line('Page Analytics');
    	$this->_viewcontroller($data);
    	






    }
}