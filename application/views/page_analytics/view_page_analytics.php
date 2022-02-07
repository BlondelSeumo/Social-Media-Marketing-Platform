<section class="section">

	<div class="section-header">
	    <h1>
	    	<i class="far fa-chart-bar"></i>
			<?php $fb_page_id=isset($page_info['page_id'])?$page_info['page_id']:""; ?>
			<?php $page_auto_id=isset($page_info['id'])?$page_info['id']:0; ?>
			<?php echo $this->lang->line("Page Analytics");?> : 
			<a href="<?php echo "https://facebook.com/".$fb_page_id; ?>" target="_BLANK"><?php echo isset($page_info['page_name'])?$page_info['page_name']:""; ?></a>
	
	    </h1>
		<div class="section-header-breadcrumb">
	      <div class="breadcrumb-item">
	      	<form method="POST" action="<?php echo base_url('page_analytics/analytics/'.$page_auto_id); ?>">					
		      	<div class="input-group">
		      	  <div class="input-group-prepend">
		      	    <div class="input-group-text">
		      	      <i class="fas fa-calendar"></i>
		      	    </div>
		      	  </div>
		      	  <input type="text" class="form-control datepicker" value="<?php echo $from_date; ?>" id="from_date" name="from_date" style="width:115px">	
		      	  <input type="text" class="form-control datepicker" value="<?php echo $to_date	; ?>" id="to_date" name="to_date" style="width:115px">
		      	  <button class="btn btn-outline-primary" style="margin-left:1px" type="submit"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>
		      	</div>
		    </form>
	      </div>
	    </div>
  	</div>


  	<div class="section-body">	
		<?php 
		if($error_message=="")
		{ ?>	

		    <div class="row">
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page and Post Stories (People talking about this)");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page and Post Stories") ?>" data-content="<?php echo $this->lang->line("The summary of Page and Post Stories that people talking about this.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_post_stories_talking_about_this" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>

			   <div class="col-12 col-lg-6">
			     <div class="card">
			       <div class="card-header">
			         <h4>
			         	<?php echo $this->lang->line("Page Impressions: Latest Top 10 Countries Unique");?>
			         	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Impressions") ?>" data-content="<?php echo $this->lang->line("The number of people who have seen any content associated with your Page by country.") ?>"><i class='fas fa-info-circle'></i> </a>
			         </h4>
			       </div>
			       <div class="card-body">
			         <canvas id="page_impressions_latest_country" height="180"></canvas>	       
			       </div>
			     </div>
			   </div>
			</div>

		    <div class="row">
		      <div class="col-12 col-lg-6">
		        <div class="card">
		          <div class="card-header">
		            <h4>
		            	<?php echo $this->lang->line("Page Impressions");?>
		            	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Impressions") ?>" data-content="<?php echo $this->lang->line("The number of times any content from your Page or about your Page entered a person's screen. This includes posts, stories, check-ins, ads, social information from people who interact with your Page Also through paid distribution such as an ad.") ?>"><i class='fas fa-info-circle'></i> </a>
		            </h4>
		          </div>
		          <div class="card-body">
		            <canvas id="page_impressions" height="180"></canvas>	       
		          </div>
		        </div>
		      </div>

			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page Impressions: Paid vs Unpaid");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Impressions") ?>" data-content="<?php echo $this->lang->line("Paid: The number of times any content from your Page or about your Page entered a person's screen through paid distribution such as an ad. 
			        	    Unpaid: The number of times any content from your Page or about your Page entered a person's screen through unpaid distribution. This includes posts, stories, check-ins, social information from people who interact with your Page and more.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_impressions_paid_vs_unpaid" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>
			</div>

			<div class="row">
			  <div class="col-12 col-lg-12">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page Engagement");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Engagement") ?>" data-content="<?php echo $this->lang->line("The number of times any content from your Page or about your Page entered a person's screen. This includes posts, stories, check-ins, ads, social information from people who interact with your Page Also through paid distribution such as an ad.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_engagements" height="280"></canvas>	       
			      </div>
			    </div>
			  </div>
			</div>

			<div class="row">
			  <div class="col-12 col-lg-12">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page Reactions");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Reaction") ?>" data-content="<?php echo $this->lang->line("Daily total post 'like', 'love', 'wow', 'sorry', 'anger'  reactions of a page.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_reactions" height="80"></canvas>	       
			      </div>
			    </div>
			  </div>
			</div>

			<div class="row">
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page CTA Clicks");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page CTA Clicks") ?>" data-content="<?php echo $this->lang->line("The number of clicks on your Page's contact info and call-to-action button.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_cta_clicks" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>

			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page CTA Clicks: Device Statistics");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page CTA Clicks Device Statistics") ?>" data-content="<?php echo $this->lang->line("Number of people who logged in to Facebook and clicked the Page CTA button, broken down by www, mobile, api and other.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_cta_clicks2" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>
			</div>

			<div class="row">
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page Fans");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page User Demographics") ?>" data-content="<?php echo $this->lang->line("The total number of people who have liked your Page, And Daily fan adds in your page and fan Removes in your page.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_user_demographics" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>

			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Daily Fan Adds and removes in your page");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page User Demographics") ?>" data-content="<?php echo $this->lang->line("The total number of people who have liked your Page, fan Removes from your page.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_user_adds_removes" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>

			</div>


			<div class="row">
				<div class="col-12 col-lg-6">
				  <div class="card">
				    <div class="card-header">
				      <h4>
				      	<?php echo $this->lang->line("Page Fans: Top 10 Countries");?>
				      	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page User Demographics") ?>" data-content="<?php echo $this->lang->line("Top 10 Countries Like Your page") ?>"><i class='fas fa-info-circle'></i> </a>
				      </h4>
				    </div>
				    <div class="card-body">
				      <canvas id="page_user_demographics_country" height="180"></canvas>	       
				    </div>
				  </div>
				</div>
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page Views: Latest Viewed Each Page Profile Tab");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page View") ?>" data-content="<?php echo $this->lang->line("The number of people who have viewed each Page profile tab.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_profile_each_tabs" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>



			</div>


			<div class="row">
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page Views: Latest Device Statistics");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Views:Latest Device Statistics") ?>" data-content="<?php echo $this->lang->line("The number of people logged in to Facebook who have viewed your Page profile, broken down by the type of device.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_view_devices_stats" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>
			  <div class="col-12 col-lg-6">
			    <div class="card">
			      <div class="card-header">
			        <h4>
			        	<?php echo $this->lang->line("Page Views: Latest Page Views By Referers Domains");?>
			        	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Views By Referers") ?>" data-content="<?php echo $this->lang->line("Logged-in page visit counts (unique users) by referral source.") ?>"><i class='fas fa-info-circle'></i> </a>
			        </h4>
			      </div>
			      <div class="card-body">
			        <canvas id="page_views_refferer" height="180"></canvas>	       
			      </div>
			    </div>
			  </div>
			




			</div>


			<div class="row">
					<div class="col-12 col-lg-6">
					  <div class="card">
					    <div class="card-header">
					      <h4>
					      	<?php echo $this->lang->line("Page Video Views");?>
					      	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Video Views") ?>" data-content="<?php echo $this->lang->line("The number of times your Page's videos played, During a single instance of a video playing, we'll exclude any time spent replaying the video.") ?>"><i class='fas fa-info-circle'></i> </a>
					      </h4>
					    </div>
					    <div class="card-body">
					      <canvas id="page_video_views" height="180"></canvas>	       
					    </div>
					  </div>
					</div>
					<div class="col-12 col-lg-6">
					  <div class="card">
					    <div class="card-header">
					      <h4>
					      	<?php echo $this->lang->line("Page Video Views: Paid Vs Unpaid");?>
					      	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Video Views: Paid Vs Unpaid") ?>" data-content="<?php echo $this->lang->line("The number of times your Page's promoted and nonpromoted videos played for at least 3 seconds, or for nearly their total length if they're shorter than 3 seconds. For each impression of a video, we'll count video views separately and exclude any time spent replaying the video") ?>"><i class='fas fa-info-circle'></i> </a>
					      </h4>
					    </div>
					    <div class="card-body">
					      <canvas id="page_video_views_paid_vs_unpaid" height="180"></canvas>	       
					    </div>
					  </div>
					</div>
					

			</div>
			
			<div class="row">
					<div class="col-12 col-lg-6">
					  <div class="card">
					    <div class="card-header">
					      <h4>
					      	<?php echo $this->lang->line("Page Post Impressions: Viral Vs Nonviral");?>
					      	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Post Impressions: Viral Vs Nonviral") ?>" data-content="<?php echo $this->lang->line("Viral: The number of times your Page's posts entered a person's screen with social information attached. Social information displays when a person's friend interacted with you Page or post. This includes when someone's friend likes or follows your Page, engages with a post, shares a photo of your Page and checks into your Page. Nonviral : The number of times your Page's posts entered a person's screen. This does not include content created about your Page with social information attached. Social information displays when a person's friend interacted with you Page or post. This includes when someone's friend likes or follows your Page, engages with a post, shares a photo of your Page and checks into your Page.") ?>"><i class='fas fa-info-circle'></i> </a>
					      </h4>
					    </div>
					    <div class="card-body">
					      <canvas id="page_post_impression_viral_nonviral" height="180"></canvas>	       
					    </div>
					  </div>
					</div>
					<div class="col-12 col-lg-6">
					  <div class="card">
					    <div class="card-header">
					      <h4>
					      	<?php echo $this->lang->line("Page Post Impressions: Paid Vs Unpaid");?>
					      	<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Page Post Impressions: Paid Vs Unpaid") ?>" data-content="<?php echo $this->lang->line("Paid: The number of times your Page's posts entered a person's screen through paid distribution such as an ad. Unpaid : The number of times your Page's posts entered a person's screen through unpaid distribution.") ?>"><i class='fas fa-info-circle'></i> </a>
					      </h4>
					    </div>
					    <div class="card-body">
					      <canvas id="page_post_impression_paid_vs_unpaid" height="180"></canvas>	       
					    </div>
					  </div>
					</div>
			</div>
	
  	    <?php 
  		}
  	    else 
  		{ 
  			echo '
  			<div class="card">
                <div class="card-header">
                  <h4>'.$this->lang->line("Something Went Wrong").'</h4>
                </div>
                <div class="card-body">
                  <div class="empty-state" data-height="400" style="height: 400px;">
                    <div class="empty-state-icon bg-danger">
                      <i class="fas fa-times"></i>
                    </div>
                    <h2>'.$this->lang->line("Something Went Wrong").'</h2>
                    <p class="lead">
                     '.$error_message.'
                    </p>
                  </div>
                </div>
              </div>';
  		
  		} ?>
    </div>

</section>


<?php

$steps = 10;
// Page Content Activity Steps
$page_content_activity_data_label = array_column($page_content_activity_unique, 'date');
$page_content_activity_data_values = array_column($page_content_activity_unique, 'value');
$page_content_activity_by_action_type_fan_value = array_column($page_content_activity_by_action_type_unique_fan, 'page_story_fan');
$page_content_activity_by_action_type_other_value = array_column($page_content_activity_by_action_type_unique_other, 'page_story_other');
$page_content_activity_by_action_type_page_post_value = array_column($page_content_activity_by_action_type_unique_page_post, 'page_story_page_post');
$page_content_final = array_merge($page_content_activity_data_values,$page_content_activity_by_action_type_fan_value,$page_content_activity_by_action_type_other_value,$page_content_activity_by_action_type_page_post_value);

$page_content_activity_unique_steps = (!empty($page_content_final)) ? round(max($page_content_final)/$steps) : 1;
if($page_content_activity_unique_steps==0) $page_content_activity_unique_steps = 1;


//Page Impressions Steps
$page_impressions_data_label =array_column($page_impressions_paid, 'date');
$page_impressions_paid_data_value = array_column($page_impressions_paid, 'value');
$page_impressions_paid_unique_data_value = array_column($page_impressions_paid_unique, 'value');
$page_impressions_organic = array_column($page_impressions_organic, 'value');
$page_impressions_organic_unique = array_column($page_impressions_organic_unique, 'value');


$page_impressions_final = array_merge($page_impressions_paid_data_value,$page_impressions_paid_unique_data_value,$page_impressions_organic,$page_impressions_organic_unique);
$page_impressions_steps = (!empty($page_impressions_final)) ? round(max($page_impressions_final)/$steps) : 1;
if($page_impressions_steps==0) $page_impressions_steps = 1;

//Page Impressions Second Steps

$page_impressions_data_label1 = array_column($page_impressions, 'date');
$page_impressions_values = array_column($page_impressions, 'value');
$page_impressions_unique_values = array_column($page_impressions_unique, 'value');
$page_impressions_viral_values = array_column($page_impressions_viral, 'value');
$page_impressions_viral_unique_values = array_column($page_impressions_viral_unique, 'value');
$page_impressions_nonviral_values = array_column($page_impressions_nonviral, 'value');
$page_impressions_nonviral_unique_values = array_column($page_impressions_nonviral_unique, 'value');
$page_impressions_final2 = array_merge($page_impressions_values,$page_impressions_unique_values,$page_impressions_viral_values,$page_impressions_viral_unique_values,$page_impressions_nonviral_values,$page_impressions_nonviral_unique_values);
$page_impressions_steps2 = (!empty($page_impressions_final2)) ? round(max($page_impressions_final2)/$steps) : 1;
if($page_impressions_steps2==0) $page_impressions_steps2 = 1;



//Page Engagement

$page_engaged_users_data_label = array_column($page_engaged_users, 'date');
$page_engaged_users_data_values = array_column($page_engaged_users, 'value');
$page_post_engagements_data_values = array_column($page_post_engagements, 'value');
$page_consumptions_data_values = array_column($page_consumptions, 'value');
$page_consumptions_unique_data_values = array_column($page_consumptions_unique, 'value');
$page_places_checkin_total_data_values = array_column($page_places_checkin_total, 'value');
$page_negative_feedback_data_values = array_column($page_negative_feedback, 'value');
$page_positive_feedback_by_type_total = array_column($page_positive_feedback_by_type_total, 'value');
$page_fans_online_per_day_data_values = array_column($page_fans_online_per_day, 'value');

//Page Reaction

$page_actions_post_reactions_like_total_data_label = array_column($page_actions_post_reactions_like_total, 'date');
$page_actions_post_reactions_like_total_data_values = array_column($page_actions_post_reactions_like_total, 'value');
$page_actions_post_reactions_love_total_data_values = array_column($page_actions_post_reactions_love_total, 'value');
$page_actions_post_reactions_wow_total_data_values = array_column($page_actions_post_reactions_wow_total, 'value');

//Page CTA Clicks
$page_total_actions_data_label = array_column($page_total_actions, 'date');
$page_total_actions_data_values = array_column($page_total_actions, 'value');
$page_cta_clicks_logged_in_total_values = array_column($page_cta_clicks_logged_in_total, 'value');
$page_call_phone_clicks_logged_in_unique_values = array_column($page_call_phone_clicks_logged_in_unique, 'value');
$page_get_directions_clicks_logged_in_unique_values = array_column($page_get_directions_clicks_logged_in_unique, 'value');
$page_cta_final = array_merge($page_total_actions_data_values,$page_cta_clicks_logged_in_total_values,$page_call_phone_clicks_logged_in_unique_values,$page_get_directions_clicks_logged_in_unique_values);

$page_cta_clicks = (!empty($page_cta_final)) ? round(max($page_cta_final)/$steps) : 1;
if($page_cta_clicks==0) $page_cta_clicks = 1;

//Page Fans

$page_fans_data_values = array_column($page_fans, 'value');
$page_fans_steps = (!empty($page_fans_data_values)) ? round(max($page_fans_data_values)/$steps) : 1;
if($page_fans_steps==0) $page_fans_steps =1;

// Page fan Adds and removes
$page_fan_adds_data_values = array_column($page_fan_adds, 'value');
$page_fan_removes_data_values = array_column($page_fan_removes, 'value');
$user_demographics_merge = array_merge($page_fan_adds_data_values,$page_fan_removes_data_values);
$demo_steps = (!empty($user_demographics_merge)) ? round(max($user_demographics_merge)/$steps) : 1;
if($demo_steps==0) $demo_steps = 1;


//$country_code = array_keys($page_fans_country['country']);


//Page Content
// $page_content_keys = array_keys(isset($page_tab_views_login_top['value']) ? $page_tab_views_login_top['value']: array()) ;
// $final_page_content_keys = str_replace('_', ' ', $page_content_keys);

//Page Video Views Paid Vs Unpaid
$page_video_views_paid_data_label =array_column($page_video_views_paid, 'date');
$page_video_views_paid_data_values = array_column($page_video_views_paid, 'value');
$page_video_views_organic_data_values = array_column($page_video_views_organic, 'value');
$page_video_views_final = array_merge($page_video_views_paid_data_values,$page_video_views_organic_data_values);
$page_video_steps = (!empty($page_video_views_final)) ? round(max($page_video_views_final)/$steps) : 1;
if($page_video_steps==0) $page_video_steps = 1;


// Page Video views
	
$page_video_views_data_label =array_column($page_video_views, 'date');
$page_video_views_data_values =array_column($page_video_views, 'value');
$page_video_views_autoplayed_data_values =array_column($page_video_views_autoplayed, 'value');
$page_video_views_click_to_play_data_values =array_column($page_video_views_click_to_play, 'value');
$page_video_views_unique_data_values =array_column($page_video_views_unique, 'value');
$page_video_view_time_data_values =array_column($page_video_view_time, 'value');

$page_video_views_final2 = array_merge($page_video_views_data_values,$page_video_views_autoplayed_data_values,$page_video_views_click_to_play_data_values,$page_video_views_unique_data_values,$page_video_view_time_data_values);
$page_video_steps2 = (!empty($page_video_views_final2)) ? round(max($page_video_views_final2)/$steps) : 1;
if($page_video_steps2==0) $page_video_steps2 = 1;


 // Page Post Impressions viral and Nonviral
 $page_posts_impressions_viral_data_label = array_column($page_posts_impressions_viral, 'date');
 $page_posts_impressions_viral_data_values = array_column($page_posts_impressions_viral, 'value');
 $page_posts_impressions_nonviral_data_values = array_column($page_posts_impressions_nonviral, 'value');
 $page_post_impression_viral_nonviral_final = array_merge($page_posts_impressions_viral_data_values,$page_posts_impressions_nonviral_data_values);
 $page_post_impressions_steps = (!empty($page_post_impression_viral_nonviral_final)) ? round(max($page_post_impression_viral_nonviral_final)/$steps) : 1;
 if($page_post_impressions_steps==0) $page_post_impressions_steps = 1;


 // Page Post Impressions Paid vs Unpaid
 $page_posts_impressions_paid_data_label = array_column($page_posts_impressions_paid, 'date');
 $page_posts_impressions_paid_data_values = array_column($page_posts_impressions_paid, 'value');
 $page_posts_impressions_organic_data_values = array_column($page_posts_impressions_organic, 'value');
 $page_post_impression_paid_vs_unpaid_final = array_merge($page_posts_impressions_paid_data_values,$page_posts_impressions_organic_data_values);
 $page_post_impressions_steps2 = (!empty($page_post_impression_paid_vs_unpaid_final)) ? round(max($page_post_impression_paid_vs_unpaid_final)/$steps) : 1; 
 if($page_post_impressions_steps2 == 0)
 $page_post_impressions_steps2 = 1;

?>



<script>

	//page_post_stories_talking_about_this

	var page_content_activity_unique_data = document.getElementById("page_post_stories_talking_about_this").getContext('2d');

	var page_content_activity_data_label = <?php echo json_encode($page_content_activity_data_label); ?>;
	var page_content_activity_data_values = <?php echo json_encode($page_content_activity_data_values); ?>;
	var page_content_activity_by_action_type_fan_values = <?php echo json_encode($page_content_activity_by_action_type_fan_value); ?>;
	var page_content_activity_by_action_type_other_values = <?php echo json_encode($page_content_activity_by_action_type_other_value); ?>;
	var page_content_activity_by_action_type_page_post_values = <?php echo json_encode($page_content_activity_by_action_type_page_post_value); ?>;
	var page_messages_new_conversations_unique_chart = new Chart(page_content_activity_unique_data, {
	  type: 'line',
	  data: {
	    labels: page_content_activity_data_label,
	    datasets: [{
	      label: '<?php echo $this->lang->line("Page Content Activity"); ?>',
	      data: page_content_activity_data_values,
	      borderWidth: 3,
	      borderColor: '#36a2eb',
	      backgroundColor: 'transparent',
	      pointBackgroundColor: '#fff',
	      pointBorderColor: '#36a2eb',
	      pointRadius: 2
	    },
	    {
	      label: '<?php echo $this->lang->line("Page Content Activity Type Unique Fan"); ?>',
	      data: page_content_activity_by_action_type_fan_values,
	      borderWidth: 3,
	      borderColor: '#4bc0c0',
	      backgroundColor: 'transparent',
	      pointBackgroundColor: '#fff',
	      pointBorderColor: '#4bc0c0',
	      pointRadius: 2
	    },{
	      label: '<?php echo $this->lang->line("Page Content Activity Type Unique Other"); ?>',
	      data: page_content_activity_by_action_type_other_values,
	      borderWidth: 3,
	      borderColor: '#ff6384',
	      backgroundColor: 'transparent',
	      pointBackgroundColor: '#fff',
	      pointBorderColor: '#ff6384',
	      pointRadius: 2
	    },{
	      label: '<?php echo $this->lang->line("Page Content Activity Type Unique Page Post"); ?>',
	      data: page_content_activity_by_action_type_page_post_values,
	      borderWidth: 3,
	      borderColor: 'var(--blue)',
	      backgroundColor: 'transparent',
	      pointBackgroundColor: '#fff',
	      pointBorderColor: 'var(--blue)',
	      pointRadius: 2

	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          display: false,
	          drawBorder: false,
	        },
	        ticks: {
	          stepSize: <?php echo $page_content_activity_unique_steps; ?>
	        }
	      }],
	      xAxes: [{
	        gridLines: {
	          color: '#fbfbfb',
	          lineWidth: 2
	        },
	        type: 'time',
	           time: {

	               displayFormats: {
	                   quarter: 'MMM YYYY'
	               }
	           },
	      }],

	    },
	  }
	});



	// Page Impressions Latest Country Unique
	
	var page_impressions_latest_country_config = {
	 	type: 'doughnut',
	 	data: {
	 		datasets: [{
	 			data: <?php echo json_encode(array_values(isset($page_impressions_by_country_unique_total['value']) ? $page_impressions_by_country_unique_total['value'] : array())); ?>,
	 			backgroundColor: [
	 				'#ff5e57',
	 				'#ff6384',
	 				'var(--blue)',
	 				'#ffa426',
	 				'#c32849',
	 				'#fe8886',
	 				'#63ed7a',
	 				'#655dd0',
	 				'#273c75',
	 				'#fd79a8'
	 			],
	 			
	 		}],
	 		labels: <?php echo json_encode(array_keys(isset($page_impressions_by_country_unique_total['value']) ? $page_impressions_by_country_unique_total['value'] : array())); ?>
	 	},
	 	options: {
	 		responsive: true,
	 		legend: {
	 			display: false,
	 		},
	 		
	 		animation: {
	 			animateScale: true,
	 			animateRotate: true
	 		},
	 		rotation: 1 * Math.PI,
	 		circumference: 1 * Math.PI

	 	}
	 };

	
	var page_impressions_latest_country_ctx = document.getElementById('page_impressions_latest_country').getContext('2d');
	var page_impressions_latest_country_my_chart = new Chart(page_impressions_latest_country_ctx, page_impressions_latest_country_config);

	//Page Impressions
    var page_impressions = document.getElementById("page_impressions").getContext('2d');
    var page_impressions_chart_data = new Chart(page_impressions, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($page_impressions_data_label1); ?>,
        datasets: [{
          label: '<?php echo $this->lang->line("Page Impressions"); ?>',
          data: <?php echo json_encode($page_impressions_values); ?>,
          borderWidth: 3,
          borderColor: '#36a2eb',
          backgroundColor: 'transparent',
          pointBackgroundColor: '#fff',
          pointBorderColor: '#36a2eb',
          pointRadius: 2
        },
        {
          label: '<?php echo $this->lang->line("Page Impressions Unique"); ?>',
          data: <?php echo json_encode($page_impressions_unique_values); ?>,
          borderWidth: 3,
          borderColor: '#4bc0c0',
          backgroundColor: 'transparent',
          pointBackgroundColor: '#fff',
          pointBorderColor: '#4bc0c0',
          pointRadius: 2
        },{
          label: '<?php echo $this->lang->line("Page Impressions Viral"); ?>',
          data: <?php echo json_encode($page_impressions_viral_values); ?>,
          borderWidth: 3,
          borderColor: '#ff6384',
          backgroundColor: 'transparent',
          pointBackgroundColor: '#fff',
          pointBorderColor: '#ff6384',
          pointRadius: 2
        },
        {
          label: '<?php echo $this->lang->line("Page Impressions Viral Unique"); ?>',
          data: <?php echo json_encode($page_impressions_viral_unique_values) ?>,
          borderWidth: 3,
          borderColor: 'var(--blue)',
          backgroundColor: 'transparent',
          pointBackgroundColor: '#fff',
          pointBorderColor: 'var(--blue)',
          pointRadius: 2

        },
        {
          label: '<?php echo $this->lang->line("Page Impressions Non Viral"); ?>',
          data: <?php echo json_encode($page_impressions_nonviral_values) ?>,
          borderWidth: 3,
          borderColor: '#a55eea',
          backgroundColor: 'transparent',
          pointBackgroundColor: '#fff',
          pointBorderColor: '#a55eea',
          pointRadius: 2

        },
        {
          label: '<?php echo $this->lang->line("Page Impressions Non Viral Unique"); ?>',
          data: <?php echo json_encode($page_impressions_nonviral_unique_values) ?>,
          borderWidth: 3,
          borderColor: '#63ed7a',
          backgroundColor: 'transparent',
          pointBackgroundColor: '#fff',
          pointBorderColor: '#63ed7a',
          pointRadius: 2

        },

        ]
      },
      options: {
        legend: {
          display: false
        },
        scales: {
          yAxes: [{
            gridLines: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              stepSize: <?php echo $page_impressions_steps2; ?>
            }
          }],
          xAxes: [{
            gridLines: {
              color: '#fbfbfb',
              lineWidth: 2
            },
            type: 'time',
               time: {
               		
                   displayFormats: {
                       quarter: 'MMM YYYY'
                   }
               },
          }]
        },

      }
    });
    //Page Impressions Paid vs Unpaid
	var ctx_impressesion_p_un = document.getElementById("page_impressions_paid_vs_unpaid").getContext('2d');
	var page_impressions_data_label = <?php echo json_encode($page_impressions_data_label); ?>;
	var page_impressions_organic = <?php echo json_encode($page_impressions_organic); ?>;
	var page_impressions_organic_unique = <?php echo json_encode($page_impressions_organic_unique); ?>;
	var page_impressions_paid_data_value = <?php echo json_encode($page_impressions_paid_data_value); ?>;
	var page_impressions_paid_unique_data_value = <?php echo json_encode($page_impressions_paid_unique_data_value); ?>;

	var page_ImpressionPaid_vs_unpaidChart = new Chart(ctx_impressesion_p_un, {
	  type: 'line',
	  data: {
	    labels: page_impressions_data_label,
	    datasets: [{
	      label: '<?php echo $this->lang->line("Page Impressions Unpaid"); ?>',
	      data: page_impressions_organic,
	      borderWidth: 2,
	      backgroundColor: 'rgba(63,82,227,.8)',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
	    },
	    {
	      label: '<?php echo $this->lang->line("Page Impressions Unpaid Unique") ?>',
	      data: page_impressions_organic_unique,
	      borderWidth: 2,
	      backgroundColor: 'rgb(78, 89, 167)',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0 ,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: 'rgb(78, 89, 167)',
	    },
	    {
	      label: '<?php echo $this->lang->line("Page Impressions Paid") ?>',
	      data: page_impressions_paid_data_value,
	      borderWidth: 2,
	      backgroundColor: 'rgb(254, 136, 134)',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0 ,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: 'rgb(254, 136, 134)',
	    },
	     {
	       label: '<?php echo $this->lang->line("Page Impressions Paid Unique") ?>',
	       data: page_impressions_paid_unique_data_value,
	       borderWidth: 2,
	       backgroundColor: 'rgb(93, 210, 118)',
	       borderWidth: 0,
	       borderColor: 'transparent',
	       pointBorderWidth: 0 ,
	       pointRadius: 3.5,
	       pointBackgroundColor: 'transparent',
	       pointHoverBackgroundColor: 'rgb(93, 210, 118)',
	     }
	     ]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          display: false,
	          drawBorder: false,
	        },

	        ticks: {
	          beginAtZero: true,
	          stepSize: <?php echo $page_impressions_steps; ?>,
	          callback: function(value, index, values) {
	            return value;
	          }
	        }
	      }],
	      xAxes: [{
	        gridLines: {
	          color: '#fbfbfb',
	          lineWidth: 2
	        },
	        type: 'time',
	           time: {
	           		
	               displayFormats: {
	                   quarter: 'MMM YYYY'
	               }
	           },

	      }]
	    },
	  }
	});


    //Page Engagement
    var page_engagement_canvas_id = document.getElementById("page_engagements").getContext('2d');
    var page_engaged_users_data_label = <?php echo json_encode($page_engaged_users_data_label); ?>;
	var page_enagagement_data = {
				labels: page_engaged_users_data_label,
				datasets: [{
					backgroundColor: '#36a2eb',
					borderColor: 'transparent',
					data: <?php echo json_encode($page_engaged_users_data_values); ?>,
					hidden: false,
					label: '<?php echo $this->lang->line("Page Engaged Users"); ?>'
				}, 
				{
					backgroundColor: '#4bc0c0',
					borderColor: 'transparent',
					data: <?php echo json_encode($page_post_engagements_data_values); ?>,
					label: '<?php echo $this->lang->line("Page Post Engagement"); ?>',
					fill: '-1'
				},
				{
					backgroundColor: '#ff6384',
					borderColor: 'transparent',
					data: <?php echo json_encode($page_consumptions_data_values); ?>,
					label: '<?php echo $this->lang->line("Page Consumptions") ?>',
					fill: 1
				},
				{
					backgroundColor: 'var(--blue)',
					borderColor: 'transparent',
					data: <?php echo json_encode($page_consumptions_unique_data_values); ?>,
					label: '<?php echo $this->lang->line("Page Consumptions Unique"); ?>',
					fill: '-1'
				},
				{
					backgroundColor: '#ffa426',
					borderColor: 'transparent',
					data: <?php echo json_encode($page_places_checkin_total_data_values) ?>,
					label: '<?php echo $this->lang->line("Page Places Checkin Total"); ?>',
					fill: '-1'
				},
 				{
					backgroundColor: '#fe8886',
					borderColor: 'transparent',
					data: <?php echo json_encode($page_negative_feedback_data_values); ?>,
					label: '<?php echo $this->lang->line("Page Negative Feedback"); ?>',
					fill: '+2'
				},
				{
					backgroundColor: '#8BC34A',
					borderColor: 'transparent',
					data: <?php echo json_encode($page_positive_feedback_by_type_total); ?>,
					label: '<?php echo $this->lang->line("Page Positive Feedback"); ?>',
					fill: 1
				},
				{
					backgroundColor: '#e25f5d',
					borderColor: 'transparent',
					data: <?php echo json_encode($page_fans_online_per_day_data_values); ?>,
					label: '<?php echo $this->lang->line("Page Fans Online Per Day"); ?>',
					fill: 1
				}


				]
			};

	var page_engagement_options = {
		legend: {
		  display: false
		},
		animation: {
			animateScale: true,
			animateRotate: true
		},
		maintainAspectRatio: false,
		elements: {
			line: {
				tension: 0.4
			}
		},
		scales: {
			yAxes: [{
				stacked: true,
				gridLines: {
				  display: false,
				  drawBorder: false,
				}

			}],
			xAxes: [{
			  gridLines: {
			    color: '#fbfbfb',
			    lineWidth: 2
			  },
			  type: 'time',
			     time: {
			     		
			         displayFormats: {
			             quarter: 'MMM YYYY'
			         }
			     },

			}]
		},
		plugins: {
			filler: {
				propagate: true
			},

		}
	};

	var page_engagements_chart = new Chart(page_engagement_canvas_id, {
		type: 'line',
		data: page_enagagement_data,
		options: page_engagement_options
	});

    // Page Reaction 
   
    var page_reactions_config = {
			   	type: 'line',
			   	data: {
			   		labels: <?php echo json_encode($page_actions_post_reactions_like_total_data_label); ?>,
			   		datasets: [{
			   			label: '<?php echo $this->lang->line("Daily Page Post Total Like"); ?>',
			   			data: <?php echo json_encode($page_actions_post_reactions_like_total_data_values); ?>,
			   			backgroundColor: 'var(--blue)',
			   			borderColor: 'var(--blue)',
			   			fill: false,
			   			borderDash: [5, 5],
			   			pointRadius: 4,
			   			pointHoverRadius: 6,
			   		},
 					{
			   			label: '<?php echo $this->lang->line("Daily Page Post Total Love Reaction"); ?>',
			   			data: <?php echo json_encode($page_actions_post_reactions_love_total_data_values); ?>,
			   			backgroundColor: '#ff6384',
			   			borderColor: '#ff6384',
			   			fill: false,
			   			borderDash: [5, 5],
			   			pointRadius: 4,
			   			pointHoverRadius: 6,
			   		},
	   				{
	   		   			label: '<?php echo $this->lang->line("Daily Page Post Total Wow Reaction"); ?>',
	   		   			data: <?php echo json_encode($page_actions_post_reactions_wow_total_data_values); ?>,
	   		   			backgroundColor: '#ffa426',
	   		   			borderColor: '#ffa426',
	   		   			fill: false,
	   		   			pointRadius: 4,
	   		   			pointHoverRadius: 6,
	   		   		},
	   				{
	   		   			label: '<?php echo $this->lang->line("Daily Page Post Total Haha Reaction"); ?>',
	   		   			data: <?php echo json_encode(array_column($page_actions_post_reactions_haha_total, 'value')); ?>,
	   		   			backgroundColor: '#36a2eb',
	   		   			borderColor: '#36a2eb',
	   		   			fill: false,
	   		   			pointHitRadius: 15,
	   		   			pointRadius: 4,
	   		   			pointHoverRadius: 6,
	   		   		},
	   		   		{
			   			label: '<?php echo $this->lang->line("Daily Page Post Total Sad Reaction"); ?>',
			   			data: <?php echo json_encode(array_column($page_actions_post_reactions_sorry_total, 'value')); ?>,
			   			backgroundColor: '#fe8886',
			   			borderColor: '#fe8886',
			   			fill: false,
			   			borderDash: [5, 5],
			   			pointRadius: 4,
			   			pointHoverRadius: 6,
			   		},
	   				{
	   		   			label: '<?php echo $this->lang->line("Daily Page Post Total Angry Reaction"); ?>',
	   		   			data: <?php echo json_encode(array_column($page_actions_post_reactions_anger_total, 'value')); ?>,
	   		   			backgroundColor: '#c32849',
	   		   			borderColor: '#c32849',
	   		   			fill: false,
	   		   			pointRadius: 4,
	   		   			pointHoverRadius: 6,
	   		   		}]
			   	},
			   	options: {
			   		responsive: true,
			   		legend: {
			   		  display: false
			   		},
			   		animation: {
			   			animateScale: true,
			   			animateRotate: true
			   		},
			   		hover: {
			   			mode: 'index'
			   		},
			   		scales: {
			   			xAxes: [{
			   				gridLines: {
			   				  color: '#fbfbfb',
			   				  lineWidth: 2
			   				},
			   				type: 'time',
			   				   time: {
			   				   		
			   				       displayFormats: {
			   				           quarter: 'MMM YYYY'
			   				       }
			   				   },

			   			}],
			   			yAxes: [{
			   				gridLines: {
			   					          display: false,
			   					          drawBorder: false,
			   					        }

			   			}]
			   		},

			   	}
			   };

	var page_reactions_chart = document.getElementById('page_reactions').getContext('2d');
	var page_reactions_my_chart = new Chart(page_reactions_chart, page_reactions_config);


    //Page CTA Clicks
	var page_cta_clicks_ctx1 = document.getElementById("page_cta_clicks").getContext('2d');
	var myChart = new Chart(page_cta_clicks_ctx1, {
	  	  type: 'line',
	  	  data: {
	  	    labels: <?php echo json_encode($page_total_actions_data_label); ?>,
	  	    datasets: [{
	  	      label: '<?php echo $this->lang->line("Page CTA Total"); ?>',
	  	      data: <?php echo json_encode($page_total_actions_data_values); ?>,
	  	      backgroundColor: 'var(--blue)',
	  	      borderColor: 'transparent',
	  	      hidden: false,

	  	    },

	  	    {
	  	      label: '<?php echo $this->lang->line("Page CTA Click User Logged-in"); ?>',
	  	      data: <?php echo json_encode(array_column($page_cta_clicks_logged_in_total, 'value')); ?>,
	  	      backgroundColor: '#ffb1c1',
	  	      borderColor: 'transparent',
	  	      fill:'-1'
	  	    },
	  	    {
	  	      label: '<?php echo $this->lang->line("Clicked Page Call Now Button Unique"); ?>',
	  	      data: <?php echo json_encode(array_column($page_call_phone_clicks_logged_in_unique, 'value')); ?>,
	  	      backgroundColor: '#9ad0f5',
	  	      borderColor: 'transparent',
	  	      fill: 1
	  	    },
	  	    {
	  	      label: '<?php echo $this->lang->line("Clicked Page Get Direection Button Unique"); ?>',
	  	      data: <?php echo json_encode(array_column($page_get_directions_clicks_logged_in_unique, 'value')); ?>,
	  	      backgroundColor: '#ff6384',
	  	      borderColor: 'transparent',
	  	      fill: '-1'
	  	    },
	  

	  	    ]
	  	  },
	  	  options: {
	  	  	legend: {
	  	  	  display: false
	  	  	},
	  	  	animation: {
	  	  		animateScale: true,
	  	  		animateRotate: true
	  	  	},
	  	    scales: {
	  	      yAxes: [{
	  	        gridLines: {
	  	          drawBorder: false,
	  	          color: '#f2f2f2',
	  	        },
	  	        ticks: {
	  	          beginAtZero: true,
	  	          stepSize: <?php echo $page_cta_clicks; ?>
	  	        }
	  	      }],
	  	      xAxes: [{
	  	        ticks: {
	  	          display: false
	  	        },
	  	        gridLines: {
	  	          display: false
	  	        }
	  	      }]
	  	    },
	  	  }
	  	});


	//Page CTA Clicks 2
	
	var page_cta_divices_stats_config = {
	 	type: 'doughnut',
	 	data: {
	 		datasets: [{
	 			data: <?php echo json_encode(array_values(isset($page_website_clicks_by_site_logged_in_unique['value']) ? $page_website_clicks_by_site_logged_in_unique['value'] : array())); ?>,
	 			backgroundColor: [
	 				'#ff5e57',
	 				'#ff6384',
	 				'var(--blue)',
	 				'#ffa426',
	 				'#c32849',
	 				'#fe8886',
	 				'#63ed7a',
	 				'#655dd0',
	 				'#273c75',
	 				'#fd79a8'
	 			],
	 			
	 		}],
	 		labels: <?php echo json_encode(array_keys(isset($page_website_clicks_by_site_logged_in_unique['value']) ? $page_website_clicks_by_site_logged_in_unique['value'] : array())); ?>
	 	},
	 	options: {
	 		responsive: true,
	 		legend: {
	 			display: false,
	 		},
	 		
	 		animation: {
	 			animateScale: true,
	 			animateRotate: true
	 		},
	 		

	 	}
	 };

	
	var page_cta_device_stats_ctx = document.getElementById('page_cta_clicks2').getContext('2d');
	var page_cta_device_stats_my_chart = new Chart(page_cta_device_stats_ctx, page_cta_divices_stats_config);
	
    // Page Fans
 	var page_user_demographics_ctx = document.getElementById("page_user_demographics").getContext('2d');
 	var page_user_demographics_chart = new Chart(page_user_demographics_ctx, {
 	  type: 'line',
 	  data: {
 	    labels: <?php echo json_encode(array_column($page_fans, 'date')) ?>,
 	    datasets: [{
 	      label: '<?php echo $this->lang->line("Page Fans"); ?>',
 	      data: <?php echo json_encode(array_column($page_fans, 'value')) ?>,
 	      borderWidth: 3,
 	      borderColor: '#36a2eb',
 	      backgroundColor: 'transparent',
 	      pointBackgroundColor: '#fff',
 	      pointBorderColor: '#36a2eb',
 	      pointRadius: 2
 	    },
 	    ]
 	  },
 	  options: {
 	    legend: {
 	      display: false
 	    },
 	    animation: {
 	    	animateScale: true,
 	    	animateRotate: true
 	    },
 	    scales: {
 	      yAxes: [{
 	        gridLines: {
 	          display: false,
 	          drawBorder: false,
 	        },
 	        ticks: {
 	          stepSize: <?php echo $page_fans_steps; ?>
 	        }
 	        
 	      }],
 	      xAxes: [{
 	        gridLines: {
 	          color: '#fbfbfb',
 	          lineWidth: 2
 	        },
 	        type: 'time',
 	           time: {

 	               displayFormats: {
 	                   quarter: 'MMM YYYY'
 	               }
 	           },
 	      }],

 	    },
 	  }
 	});

 	//Page Fan adds and remove 
 	var page_user_demographics_add_remove_ctx = document.getElementById("page_user_adds_removes").getContext('2d');
 	var page_user_demographics_add_remove_chart = new Chart(page_user_demographics_add_remove_ctx, {
 	  type: 'line',
 	  data: {
 	    labels: <?php echo json_encode(array_column($page_fan_adds, 'date')) ?>,
 	    datasets: [
 	    {
 	      label: '<?php echo $this->lang->line("Daily Page Fan Adds"); ?>',
 	      data: <?php echo json_encode(array_column($page_fan_adds, 'value')); ?>,
 	      borderWidth: 3,
 	      borderColor: '#4bc0c0',
 	      backgroundColor: 'transparent',
 	      pointBackgroundColor: '#fff',
 	      pointBorderColor: '#4bc0c0',
 	      pointRadius: 2
 	    },{
 	      label: '<?php echo $this->lang->line("Daily Page Fan Removes"); ?>',
 	      data: <?php echo json_encode(array_column($page_fan_removes,'value')); ?>,
 	      borderWidth: 3,
 	      borderColor: '#ff6384',
 	      backgroundColor: 'transparent',
 	      pointBackgroundColor: '#fff',
 	      pointBorderColor: '#ff6384',
 	      pointRadius: 2
 	    }
 	    ]
 	  },
 	  options: {
 	    legend: {
 	      display: false
 	    },
 	    animation: {
 	    	animateScale: true,
 	    	animateRotate: true
 	    },
 	    scales: {
 	      yAxes: [{
 	        gridLines: {
 	          display: false,
 	          drawBorder: false,
 	        },
 	        ticks: {
 	          stepSize: <?php echo $demo_steps; ?>
 	        }
 	        
 	      }],
 	      xAxes: [{
 	        gridLines: {
 	          color: '#fbfbfb',
 	          lineWidth: 2
 	        },
 	        type: 'time',
 	           time: {

 	               displayFormats: {
 	                   quarter: 'MMM YYYY'
 	               }
 	           },
 	      }],

 	    },
 	  }
 	});
    // Page User Demographics Country
	var page_user_demographics_country_config = {
	  	type: 'doughnut',
	  	data: {
	  		datasets: [{
	  			data: <?php echo json_encode(array_values(isset($page_fans_country['country']) ? $page_fans_country['country'] : array())); ?>,
	  			backgroundColor: [
	  				'#ff5e57',
	  				'#ff6384',
	  				'var(--blue)',
	  				'#ffa426',
	  				'#c32849',
	  				'#fe8886',
	  				'#63ed7a',
	  				'#655dd0',
	  				'#273c75',
	  				'#fd79a8'
	  			],
	  			
	  		}],
	  		labels: <?php echo json_encode(array_keys(isset($page_fans_country['country']) ? $page_fans_country['country'] : array())); ?>
	  	},
	  	options: {
	  		responsive: true,
	  		legend: {
	  			display: false,
	  		},
	  		
	  		animation: {
	  			animateScale: true,
	  			animateRotate: true
	  		},

	  	}
	  };

	 
	 var page_user_demographics_country_ctx = document.getElementById('page_user_demographics_country').getContext('2d');
	 var page_user_demographics_country_chart = new Chart(page_user_demographics_country_ctx, page_user_demographics_country_config);
	
    // Page Content
 



    //Page Views 
	var page_content_config = {
	 	type: 'pie',
	 	data: {
	 		datasets: [{
	 			data: <?php echo json_encode(array_values(isset($page_views_by_profile_tab_total['value']) ? $page_views_by_profile_tab_total['value']: array())); ?>,
	 			backgroundColor: [
	 			
	 			'#ff5e57',
	 			'#ff6384',
	 			'var(--blue)',
	 			'#ff6384',
	 			'#c32849',
	 			'#fe8886',
	 			'#63ed7a',
	 			'#655dd0',
	 			'#273c75',
	 			'#fd79a8'


	 			],
	 			
	 			
	 		}],
	 		labels: <?php echo json_encode(array_keys(isset($page_views_by_profile_tab_total['value']) ? $page_views_by_profile_tab_total['value'] : array())); ?>
	 	},
	 	options: {
	 		responsive: true,
	 		legend: {
	 			display: false,
	 		},
	 		
	 		animation: {
	 			animateScale: true,
	 			animateRotate: true
	 		},
	 	}
	 };


	var page_content_ctx = document.getElementById('page_profile_each_tabs').getContext('2d');
	var page_content_ctx_chart = new Chart(page_content_ctx, page_content_config);

    // Page Views 2
 	var page_views_devices_stats_config = {
 	 	type: 'doughnut',
 	 	data: {
 	 		datasets: [{
 	 			data: <?php echo json_encode(array_values(isset($page_views_by_site_logged_in_unique['value']) ? $page_views_by_site_logged_in_unique['value'] : array())); ?>,
 	 			backgroundColor: [
 	 				'#ff5e57',
 	 				'#ff6384',
 	 				'var(--blue)',
 	 				'#ffa426',
 	 				'#c32849',
 	 				'#fe8886',
 	 				'#63ed7a',
 	 				'#655dd0',
 	 				'#273c75',
 	 				'#fd79a8'
 	 			],
 	 			
 	 		}],
 	 		labels: <?php echo json_encode(array_keys(isset($page_views_by_site_logged_in_unique['value']) ? $page_views_by_site_logged_in_unique['value'] : array())); ?>
 	 	},
 	 	options: {
 	 		responsive: true,
 	 		legend: {
 	 			display: false,
 	 		},
 	 		
 	 		animation: {
 	 			animateScale: true,
 	 			animateRotate: true
 	 		},
 	 		

 	 	}
 	 };

 	
 	var page_views_device_stats_ctx = document.getElementById('page_view_devices_stats').getContext('2d');
 	var page_views_device_my_chart = new Chart(page_views_device_stats_ctx, page_views_devices_stats_config);
    // Page Views 3
    
	var page_views_refferer_config = {
	 	data: {
	 		datasets: [{
	 			data: <?php echo json_encode(array_values(isset($page_views_by_referers_logged_in_unique['value']) ? $page_views_by_referers_logged_in_unique['value'] : array())) ?>,
	 			backgroundColor: [
	 			'#ff5e57',
	 			'#ff6384',
	 			'var(--blue)',
	 			'#8e44ad',
	 			'#c32849',
	 			'#fe8886',
	 			'#63ed7a',
	 			'#655dd0',
	 			'#273c75',
	 			'#fd79a8'
	 			],
	 			label: 'My dataset' // for legend
	 		}],
	 		labels: <?php echo json_encode(array_keys(isset($page_views_by_referers_logged_in_unique['value']) ? $page_views_by_referers_logged_in_unique['value'] : array() )) ?>
	 	},
	 	options: {
	 		responsive: true,
	 		legend: {
	 			display: false,
	 		},
	 		scales: {
	 		  yAxes: [{
	 		    gridLines: {
	 		      drawBorder: false,
	 		      display: false,
	 		      circular: true
	 		    },
	 		    ticks: {
                display: false
                }
	 		 
	 		  }],
	 		  xAxes: [{
	 		    gridLines: {
	 		      display: false, 
	 		      drawBorder: false,
	 		      circular: true
	 		    },
	 		    ticks: {
                display: false
                }
	 		  }]
	 		},
	 		
	 		animation: {
	 			animateRotate: false,
	 			animateScale: true
	 		}
	 	}
	 };

	
	var page_views_refferar_ctx = document.getElementById('page_views_refferer');
	var page_views_refferer_config_mychart = Chart.PolarArea(page_views_refferar_ctx, page_views_refferer_config);
	
	//Page Video Views Paid vs Unpaid
	var page_video_views_paid_vs_un_ctx = document.getElementById("page_video_views_paid_vs_unpaid").getContext('2d');


	var page_video_views_paid_vs_un_chart = new Chart(page_video_views_paid_vs_un_ctx, {
	  type: 'line',
	  data: {
	    labels: <?php echo json_encode($page_video_views_paid_data_label); ?>,
	    datasets: [
	    {
	      label: '<?php echo $this->lang->line("Page Video Views Unpaid") ?>',
	      data: <?php echo json_encode($page_video_views_organic_data_values); ?>,
	      borderWidth: 2,
	      backgroundColor: 'var(--blue)',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0 ,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: 'var(--blue)',
	    },
	    {
	      label: '<?php echo $this->lang->line("Page Video Views Paid"); ?>',
	      data: <?php echo json_encode($page_video_views_paid_data_values); ?>,
	      borderWidth: 2,
	      backgroundColor: '#0984e3',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: '#0984e3',
	    },

	    
	    
	     ]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          display: false,
	          drawBorder: false,
	        },

	        ticks: {
	          beginAtZero: true,
	          stepSize: <?php echo $page_video_steps; ?>,
	          callback: function(value, index, values) {
	            return value;
	          }
	        }
	      }],
	      xAxes: [{
	        gridLines: {
	          color: '#fbfbfb',
	          lineWidth: 2
	        },
	        type: 'time',
	           time: {
	           		
	               displayFormats: {
	                   quarter: 'MMM YYYY'
	               }
	           },

	      }]
	    },
	  }
	});

	// Page Video Views

	var page_video_views_ctx = document.getElementById("page_video_views").getContext('2d');
	var page_video_views_mychart = new Chart(page_video_views_ctx, {
	  type: 'line',
	  data: {
	    labels: <?php echo json_encode($page_video_views_data_label); ?>,
	    datasets: [{
	      label: '<?php echo $this->lang->line("Page Videos View "); ?>',
	      data: <?php echo json_encode($page_video_views_data_values); ?>,
	      borderWidth: 2,
	      backgroundColor: '#36a2eb',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0 ,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: '#36a2eb',
	    },
	    {
	      label: '<?php echo $this->lang->line("Page Videos View By Auto Played"); ?>',
	      data: <?php echo json_encode($page_video_views_autoplayed_data_values); ?>,
	      borderWidth: 2,
	      backgroundColor: '#4bc0c0',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0 ,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: '#4bc0c0',
	    },{
	      label: '<?php echo $this->lang->line("Page Videos View By Click"); ?>',
	      data: <?php echo json_encode($page_video_views_click_to_play_data_values); ?>,
	      borderWidth: 2,
	      backgroundColor: '#ff6384',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0 ,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: '#ff6384',
	    },{
	      label: '<?php echo $this->lang->line("Page Videos View Unique"); ?>',
	      data: <?php echo json_encode($page_video_views_unique_data_values); ?>,
	      borderWidth: 2,
	      backgroundColor: 'var(--blue)',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0 ,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: 'var(--blue)',

	    },
	    {
	      label: '<?php echo $this->lang->line("Total Vidoes View Time in milliseconds"); ?>',
	      data: <?php echo json_encode($page_video_view_time_data_values); ?>,
	      borderWidth: 2,
	      backgroundColor: 'var(--blue)',
	      borderWidth: 0,
	      borderColor: 'transparent',
	      pointBorderWidth: 0 ,
	      pointRadius: 3.5,
	      pointBackgroundColor: 'transparent',
	      pointHoverBackgroundColor: 'var(--blue)',

	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          display: false,
	          drawBorder: false,
	        },
	        ticks: {
	          stepSize: <?php echo $page_video_steps2; ?>
	        }
	      }],
	      xAxes: [{
	        gridLines: {
	          color: '#fbfbfb',
	          lineWidth: 2
	        },
	        type: 'time',
	           time: {

	               displayFormats: {
	                   quarter: 'MMM YYYY'
	               }
	           },
	      }],

	    },
	  }
	});

   // Page Post Impressions viral and Nonviral
   var page_post_impression_viral_non_ctx = document.getElementById("page_post_impression_viral_nonviral").getContext('2d');
   var page_post_impression_viral_non_my_chart = new Chart(page_post_impression_viral_non_ctx, {
     type: 'line',
     data: {
       labels: <?php echo json_encode($page_posts_impressions_viral_data_label); ?>,
       datasets: [{
         label: '<?php echo $this->lang->line("Page Post Impressions Nonviral "); ?>',
         data: <?php echo json_encode($page_posts_impressions_nonviral_data_values); ?>,
         borderWidth: 2,
         backgroundColor: '#36a2eb',
         borderWidth: 0,
         borderColor: 'transparent',
         pointBorderWidth: 0 ,
         pointRadius: 3.5,
         pointBackgroundColor: 'transparent',
         pointHoverBackgroundColor: '#36a2eb',
       },
       {
         label: '<?php echo $this->lang->line("Page Post Impressions Viral"); ?>',
         data: <?php echo json_encode($page_posts_impressions_viral_data_values); ?>,
         borderWidth: 2,
         backgroundColor: '#6c5ce7',
         borderWidth: 0,
         borderColor: 'transparent',
         pointBorderWidth: 0 ,
         pointRadius: 3.5,
         pointBackgroundColor: 'transparent',
         pointHoverBackgroundColor: '#6c5ce7',
       },]
     },
     options: {
       legend: {
         display: false
       },
       scales: {
         yAxes: [{
           gridLines: {
             display: false,
             drawBorder: false,
           },
           ticks: {
             stepSize: <?php echo $page_post_impressions_steps; ?>
           }
         }],
         xAxes: [{
           gridLines: {
             color: '#fbfbfb',
             lineWidth: 2
           },
           type: 'time',
              time: {

                  displayFormats: {
                      quarter: 'MMM YYYY'
                  }
              },
         }],

       },
     }
   });

   // Page Post Impressions Paid Vs Unpaid
   
   var page_post_impression_paid_unpaid_ctx = document.getElementById("page_post_impression_paid_vs_unpaid").getContext('2d');
   var page_post_impression_paid_unpaid_my_chart = new Chart(page_post_impression_paid_unpaid_ctx, {
     type: 'line',
     data: {
       labels: <?php echo json_encode($page_posts_impressions_paid_data_label); ?>,
       datasets: [{
         label: '<?php echo $this->lang->line("Page Post Impressions Paid "); ?>',
         data: <?php echo json_encode($page_posts_impressions_paid_data_values); ?>,
         borderWidth: 2,
         backgroundColor: '#36a2eb',
         borderWidth: 0,
         borderColor: 'transparent',
         pointBorderWidth: 0 ,
         pointRadius: 3.5,
         pointBackgroundColor: 'transparent',
         pointHoverBackgroundColor: '#36a2eb',
       },
       {
         label: '<?php echo $this->lang->line("Page Post Impressions Unpaid"); ?>',
         data: <?php echo json_encode($page_posts_impressions_organic_data_values); ?>,
         borderWidth: 2,
         backgroundColor: '#6c5ce7',
         borderWidth: 0,
         borderColor: 'transparent',
         pointBorderWidth: 0 ,
         pointRadius: 3.5,
         pointBackgroundColor: 'transparent',
         pointHoverBackgroundColor: '#6c5ce7',
       },]
     },
     options: {
       legend: {
         display: false
       },
       scales: {
         yAxes: [{
           gridLines: {
             display: false,
             drawBorder: false,
           },
           ticks: {
             stepSize: <?php echo $page_post_impressions_steps2; ?>
           }
         }],
         xAxes: [{
           gridLines: {
             color: '#fbfbfb',
             lineWidth: 2
           },
           type: 'time',
              time: {

                  displayFormats: {
                      quarter: 'MMM YYYY'
                  }
              },
         }],

       },
     }
   });
   
</script>
