<?php 

/**
 * 		
 */
class Login_callback_handler
{
	private $comboposter;

	function __construct($comboposter_handler)
	{
		$this->comboposter = $comboposter_handler;
	}

	public function twitter($oauth_verifier)
	{
		// ************************************************//
		$status = $this->comboposter->_check_usage($module_id = 102, $request = 1);
		if ($status == "2") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		} else if ($status == "3") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		}
		// ************************************************//
		
		$this->comboposter->load->library('Twitter');
		$this->comboposter->twitter->twitter_login_info($oauth_verifier);

		/* get user info */
		$oauth_token = $this->comboposter->session->userdata('final_auth_token');
		$oauth_token_secret = $this->comboposter->session->userdata('final_oauth_token_secret');
		$twitter_screen_name = $this->comboposter->session->userdata('twitter_screen_name');
		$twitter_user_id = $this->comboposter->session->userdata('twitter_user_id');

		/* check if request successfull */
		if(!isset($oauth_token) || !isset($oauth_token_secret)) {

		    $this->comboposter->session->set_userdata('account_import_error', $this->lang->line("Something went wrong while importing your account."));
		    redirect(base_url('comboposter/social_accounts'),'refresh');
		}

		/* get extra user info */
		$response = $this->comboposter->twitter->get_user_data($oauth_token, $oauth_token_secret, $twitter_screen_name);

		$twitter_name = $response['name'];
		$twitter_profile_image = $response['profile_image'];
		$twitter_followers = $response['followers'];

		/* add/update user info */
		$where['where'] = array(
		    'user_id' => $this->comboposter->user_id,
		    'twitter_user_id' => $twitter_user_id
		);

		$exist_or_not = $this->comboposter->basic->get_data('twitter_users_info', $where);
		$data = array(
		    'user_id' => $this->comboposter->user_id,
		    'oauth_token' => $oauth_token,
		    'oauth_token_secret' => $oauth_token_secret,
		    'screen_name' => $twitter_screen_name,
		    'twitter_user_id' => $twitter_user_id,
		    'name' => $twitter_name,
		    'profile_image' => $twitter_profile_image,
		    'followers' => $twitter_followers,
		    'add_date' => date('Y-m-d')
		);
		if(empty($exist_or_not)) {

		    $this->comboposter->basic->insert_data('twitter_users_info', $data);
		    $this->comboposter->_insert_usage_log($module_id = 102, $request = 1);
		} else {
		    $where = array(
		        'user_id' => $this->comboposter->user_id,
		        'twitter_user_id' => $twitter_user_id
		    );
		    $this->comboposter->basic->update_data('twitter_users_info', $where, $data);
		}

		redirect('comboposter/social_accounts', 'location');
	}

	// public function tumblr($auth_varifier)
	// {
	// 	// ************************************************//
	// 	$status = $this->comboposter->_check_usage($module_id = 104, $request = 1);
	// 	if ($status == "2") {

	// 	    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
	// 	    redirect('comboposter/social_accounts', 'location');
	// 	    exit();
	// 	} else if ($status == "3") {

	// 	    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
	// 	    redirect('comboposter/social_accounts', 'location');
	// 	    exit();
	// 	}
	// 	// ************************************************//
		
	// 	$this->comboposter->load->library('Tumblr');

	// 	/* get user info */
	// 	$auth_token = $this->comboposter->session->userdata('tumblr_auth_token');
	// 	$auth_token_secret = $this->comboposter->session->userdata('tumblr_auth_token_secret');
	// 	$user_info = $this->comboposter->tumblr->tumblr_login_info($auth_token, $auth_token_secret, $auth_varifier);

	// 	/* add / update users data */
	// 	if(!empty($user_info)) {

	// 	    $insert_data['auth_token'] = $auth_token;
	// 	    $insert_data['auth_token_secret'] = $auth_token_secret;
	// 	    $insert_data['auth_varifier'] = $auth_varifier;

	// 	    $insert_data['user_name'] = $user_info['user_name'];
	// 	    $insert_data['user_title'] = $user_info['user_title'];
	// 	    $insert_data['user_followers'] = $user_info['user_followers'];

	// 	    $insert_data['user_id'] = $this->comboposter->user_id;
	// 	    $insert_data['add_date'] = date('Y-m-d');


	// 	    $where['where'] = array(
	// 	        'user_id' => $this->comboposter->user_id,
	// 	        'user_name' => $user_info['user_name']
	// 	    );


	// 	    $exist_or_not = $this->comboposter->basic->get_data('tumblr_users_info', $where);
	// 	    if(empty($exist_or_not)) {

	// 	        $this->comboposter->basic->insert_data('tumblr_users_info', $insert_data);
	// 	        $this->comboposter->_insert_usage_log($module_id = 104, $request = 1);
	// 	    } else {

	// 	        $where = array(
	// 	            'user_id' => $this->user_id,
	// 	            'user_name' => $user_name
	// 	        );
	// 	        $this->comboposter->basic->update_data('tumblr_users_info', $where, $insert_data);
	// 	    }
	// 	    redirect('comboposter/social_accounts', 'Location');
	// 	}
	// }


	public function linkedin($authentication_code)
	{
		// ************************************************//
		$status = $this->comboposter->_check_usage($module_id = 103, $request = 1);
		if ($status == "2") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Linkedin.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		} else if ($status == "3") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Linkedin.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		}
		// ************************************************//
		
		$this->comboposter->load->library('Linkedin');

		/* get user info */
		$redirect_url = base_url('comboposter/login_callback/linkedin');
		$user_info = $this->comboposter->linkedin->linkedin_info($authentication_code, $redirect_url);

		/* check if user info exists */
		if (!isset($user_info['id']) 
			|| !isset($user_info['access_token']) 
			|| !isset($user_info['name']) 
			|| !isset($user_info['profile_pic'])) {

		    
		    $this->comboposter->session->set_userdata('account_import_error', $this->comboposter->lang->line("Something went wrong while importing your account. ").$user_info['error_message']);

		    redirect(base_url('comboposter/social_accounts'),'refresh');
		}

		/* add/update user info on table */
		$where['where'] = array(
		    'user_id' => $this->comboposter->user_id,
		    'linkedin_id' => $user_info['id']
		);
		$data = array(
		    'user_id' => $this->comboposter->user_id,
		    'access_token' => $user_info['access_token'],
		    'add_date' => date('Y-m-d') ,
		    'linkedin_id' => $user_info['id'],
		    'name' => $user_info['name'],
		    'profile_pic' => $user_info['profile_pic']
		);
		$exist_or_not = $this->comboposter->basic->get_data('linkedin_users_info', $where);

		if(empty($exist_or_not)) {

		    $this->comboposter->basic->insert_data('linkedin_users_info', $data);
		    $this->comboposter->_insert_usage_log($module_id = 103, $request = 1);
		} else {
		    $where = array(
		        'user_id' => $this->comboposter->user_id,
		        'linkedin_id' => $user_info['id']
		    );
		    $this->comboposter->basic->update_data('linkedin_users_info', $where, $data);
		}

		redirect('comboposter/social_accounts', 'location');
	}


	public function medium($integration_token)
	{

		$responses = [];

		if($integration_token == '')
		{
			$responses['status'] = '0';
			$responses['error_message'] = $this->comboposter->lang->line("You must provide an integration token.");
			echo json_encode($responses); exit();
		}
		// ************************************************//
		$status = $this->comboposter->_check_usage($module_id = 277, $request = 1);
		if ($status == "2") {
		    $responses['status'] = '0';
		    $responses['error_message'] = $this->comboposter->lang->line("Sorry, your account import limit has been exceeded for Medium.");
		    echo json_encode($responses); exit();
		} else if ($status == "3") {
		    $responses['status'] = '0';
		    $responses['error_message'] = $this->comboposter->lang->line("Sorry, your account import limit has been exceeded for Medium.");
		    echo json_encode($responses); exit();
		}
		// ************************************************//

		$this->comboposter->load->library('Medium');

		$error = 0;

		try
		{
		    $user_info = $this->comboposter->medium->get_user_info($integration_token);
		}
		catch(Exception $e)
		{
		    $error = $e->getMessage();
		}

		if($error != 0) {

			$responses['status'] = '0';
			$responses['error_message'] = $error;
			echo json_encode($responses); exit();
		}

		if(isset($user_info['data'])) {
			$insert_data['user_name'] = $user_info['data']['username'];
			$insert_data['name'] = $user_info['data']['name'];
			$insert_data['medium_id'] = $user_info['data']['id'];
			$insert_data['profile_pic'] = $user_info['data']['imageUrl'];
			$insert_data['user_id'] = $this->comboposter->user_id;
			$insert_data['access_token'] = $integration_token;
			$insert_data['add_date'] = date("Y-m-d H:i:s");
			$where['where'] = array(
			    'user_id' => $this->comboposter->user_id,
			    'medium_id' => $user_info['data']['id']
			);
			$exist_or_not = $this->comboposter->basic->get_data('medium_users_info', $where);
			$responses['status'] = '1';
			if(empty($exist_or_not))
			{
			    $this->comboposter->basic->insert_data('medium_users_info', $insert_data);
			    $this->comboposter->_insert_usage_log($module_id = 277, $request = 1);
			    $responses['success_message'] = $this->comboposter->lang->line("Medium account has been added successfully.");
			}
			else
			{
			    $where = array(
			        'user_id' => $this->comboposter->user_id,
			        'medium_id' => $user_info['data']['id']
			    );

			    $this->comboposter->basic->update_data('medium_users_info', $where, $insert_data);
			    $responses['success_message'] = $this->comboposter->lang->line("Medium account has been updated successfully.");
			}

			echo json_encode($responses);exit;

		} else if(isset($user_info['errors'])) {

			$responses['status'] = '0';
			$responses['error_message'] = $user_info['errors'][0]['message'];
			echo json_encode($responses); exit;

		} else {

			$responses['status'] = '0';
			$responses['error_message'] = $this->comboposter->lang->line("Unknown Error Occured from Medium API, Please try after some times.");
			echo json_encode($responses);exit;
		}
	}


	public function reddit($authentication_code)
	{
		// ************************************************//
		$status = $this->comboposter->_check_usage($module_id = 105, $request = 1);
		if ($status == "2") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		} else if ($status == "3") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		}
		// ************************************************//
		
		$this->comboposter->load->library("Reddit");
		// echo "<pre>";print_r($authentication_code);exit;
		$redirect_uri = base_url('comboposter/login_callback/reddit');

		/* get tockens */
		$this->comboposter->reddit->getAccessToken($authentication_code, $redirect_uri);



		// $token = json_decode($auth, true);

		$response = $this->comboposter->reddit->login_info($authentication_code, $redirect_uri);
		// echo "<pre>";print_r($response);exit;

		if(isset($response['username']) && isset($response['access_token'])) {

		    $data = array(
		        'user_id' => $this->comboposter->user_id,
		        'username' => $response['username'],
		        'access_token' => $response['access_token'],
		        'token_type' => $response['token_type'],
		        'refresh_token' => $response['refresh_token'],
		        'profile_pic' => $response['profile_pic'],
		        'url' => $response['url'],
		        'add_date' => date('Y-m-d')
		    );

		    $where['where'] = array(
		        'user_id' => $this->comboposter->user_id,
		        'username' => $response['username']
		    );

		    $exist_or_not = $this->comboposter->basic->get_data('reddit_users_info', $where);
		    if(empty($exist_or_not)) {

		        $this->comboposter->basic->insert_data('reddit_users_info', $data);
		        $this->comboposter->_insert_usage_log($module_id = 105, $request = 1);
		    } else {

		        $where = array(
		            'user_id' => $this->comboposter->user_id,
		            'username' => $response['username']
		        );

		        $this->comboposter->basic->update_data('reddit_users_info', $where, $data);
		    }
		} else {

			$this->comboposter->session->set_userdata('account_import_error', $this->comboposter->lang->line("Something went wrong while importing your account."));
			redirect(base_url('comboposter/social_accounts'),'refresh');
		}

		redirect('comboposter/social_accounts', 'location');
	}


	public function youtube()
	{
		// ************************************************//
		$status = $this->comboposter->_check_usage($module_id = 33, $request = 1);
		if ($status == "2") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		} else if ($status == "3") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		}
		// ************************************************//
		
		$this->comboposter->load->library('Google_youtube_login', NULL, 'youtube');

		/* get tokens */
		$channel_list_info = $this->comboposter->youtube->get_channel_list();

		$access_token = $channel_list_info['json_access_token'];
		$refresh_token_array = json_decode($access_token, true);
		$refresh_token = $refresh_token_array['refresh_token'];
		$channel_id = isset($channel_list_info['channel_list']['items'][0]['id']) ? $channel_list_info['channel_list']['items'][0]['id'] : '';

		/* check if tokens have set properly */
		if (!isset($access_token) || !isset($refresh_token)) {

		    $this->comboposter->session->set_userdata('account_import_error', $this->comboposter->lang->line("Something went wrong while importing your account."));
		    redirect(base_url('comboposter/social_accounts'),'refresh');
		}

		/* add/update tokens on table */
		$data_1 = array(
		    'access_token' => $access_token,
		    'user_id' => $this->comboposter->user_id,
		    'channel_id' => $channel_id,
		    'refresh_token' => $refresh_token,
		    'last_update' => date("Y-m-d H:i:s")
		);

		$has_channel_imported = $this->comboposter->basic->get_data('youtube_channel_info', array('where' => array('user_id' => $this->comboposter->user_id, 'channel_id' => $channel_id)));

		if (count($has_channel_imported) > 0) {

		    $this->comboposter->basic->update_data('youtube_channel_info', array('id' => $has_channel_imported[0]['id']), $data_1);
		    $channel_info_id = $has_channel_imported[0]['id'];
		} else {

		    $this->comboposter->basic->insert_data('youtube_channel_info', $data_1);
		    $this->comboposter->_insert_usage_log($module_id = 33, $request = 1);

		    $temp_data = $this->comboposter->basic->get_data('youtube_channel_info', array('where' => array('access_token' => $access_token, 'user_id' => $this->comboposter->user_id, 'channel_id' => $channel_id, 'refresh_token' => $refresh_token)));
			// $channel_info_id = $this->comboposter->db->insert_id();
			$channel_info_id =$temp_data[0]['id'];
		}
		
		// echo "<pre>";print_r($channel_info_id);exit;

		/* add/update channel's videos and other infos */
		if(!empty($channel_list_info['channel_list']['items'])) {

		    foreach($channel_list_info['channel_list']['items'] as $value) {

		    	/* add/update channel lists on table */
		        $data = array(
		            'user_id' => $this->comboposter->user_id,
		            'channel_info_id' => $channel_info_id
		        );

		        if(isset($value['id'])) {
		        	$data['channel_id'] = $value['id'];
		        }

		        if(isset($value['snippet']['title'])) {
		        	$data['title'] = $value['snippet']['title'];
		        }

		        if(isset($value['snippet']['description'])) {
		        	$data['description'] = $value['snippet']['description'];
		        }

		        if(isset($value['snippet']['thumbnails']['default'])) {
		        	$data['profile_image'] = $value['snippet']['thumbnails']['default']['url'];
		        }

		        if(isset($value['snippet']['thumbnails']['high'])) {
		        	$data['cover_image'] = $value['snippet']['thumbnails']['high']['url'];
		        }

		        if(isset($value['statistics']['viewCount'])) {
		        	$data['view_count'] = $value['statistics']['viewCount'];
		        }

		        if(isset($value['statistics']['videoCount'])) {
		        	$data['video_count'] = $value['statistics']['videoCount'];
		        }

		        if(isset($value['statistics']['subscriberCount'])) {
		        	$data['subscriber_count'] = $value['statistics']['subscriberCount'];
		        }

		        if(isset($data['channel_id'])) {

		            $where['where'] = array(
		                'user_id' => $this->comboposter->user_id,
		                'channel_id' => $data['channel_id']
		            );

		            /* adding/updating channel lists */
		            $existing_data = $this->comboposter->basic->get_data('youtube_channel_list', $where);
		            if(!empty($existing_data)) {

		                $where_update = array(
		                    'user_id' => $this->comboposter->user_id,
		                    'channel_id' => $data['channel_id']
		                );
		                $this->comboposter->basic->update_data('youtube_channel_list', $where_update, $data);
		            } else {

		                $this->comboposter->basic->insert_data('youtube_channel_list', $data);
		            }

		            $channel_content_details = $this->comboposter->youtube->get_channel_content_details($refresh_token_array['access_token']);
		            $playlist_id = $channel_content_details['items'][0]['contentDetails']['relatedPlaylists']['uploads'];


		            /***** Get all palylist Item ***/
		            $next_page = '';
		            do {

		                $playlist_info = $this->youtube_playlist_item($refresh_token_array['access_token'], $playlist_id, $next_page);

		                if(isset($playlist_info['nextPageToken'])) {
		                	$next_page = $playlist_info['nextPageToken'];
		                } else {
		                	$next_page = '';
		                }

		                $video_id_str = '';
		                foreach($playlist_info['items'] as $info) {

		                    $video_id = $info['snippet']['resourceId']['videoId'];
		                    $video_id_str.= $video_id . ",";
		                    $video_information[$video_id]['publishedAt'] = $info['snippet']['publishedAt'];
		                    $video_information[$video_id]['title'] = $info['snippet']['title'];
		                    $video_information[$video_id]['thumbnails'] = $info['snippet']['thumbnails']['medium']['url'];
		                }

		                $video_info = $this->comboposter->youtube->get_video_details_list($refresh_token_array['access_token'], $video_id_str);

		                foreach($video_info['items'] as $v_info) {

		                    $single_video_id = $v_info['id'];
		                    $video_information[$single_video_id]['description'] = isset($v_info['snippet']['description']) ? $v_info['snippet']['description'] : "";
		                    $video_information[$single_video_id]['tags'] = isset($v_info['snippet']['tags']) ? $v_info['snippet']['tags'] : '';
		                    $video_information[$single_video_id]['categoryId'] = isset($v_info['snippet']['categoryId']) ? $v_info['snippet']['categoryId'] : "";
		                    $video_information[$single_video_id]['liveBroadcastContent'] = isset($v_info['snippet']['liveBroadcastContent']) ? $v_info['snippet']['liveBroadcastContent'] : "";
		                    $video_information[$single_video_id]['duration'] = isset($v_info['contentDetails']['duration']) ? $v_info['contentDetails']['duration'] : "";
		                    $video_information[$single_video_id]['dimension'] = isset($v_info['contentDetails']['dimension']) ? $v_info['contentDetails']['dimension'] : "";


		                    $video_information[$single_video_id]['definition'] = isset($v_info['contentDetails']['definition']) ? $v_info['contentDetails']['definition'] : "";
		                    $video_information[$single_video_id]['caption'] = isset($v_info['contentDetails']['caption']) ? $v_info['contentDetails']['caption'] : "";
		                    $video_information[$single_video_id]['licensedContent'] = isset($v_info['contentDetails']['licensedContent']) ? $v_info['contentDetails']['licensedContent'] : "";
		                    $video_information[$single_video_id]['projection'] = isset($v_info['contentDetails']['projection']) ? $v_info['contentDetails']['projection'] : "";
		                    $video_information[$single_video_id]['viewCount'] = isset($v_info['statistics']['viewCount']) ? $v_info['statistics']['viewCount'] : "";
		                    $video_information[$single_video_id]['likeCount'] = isset($v_info['statistics']['likeCount']) ? $v_info['statistics']['likeCount'] : "";


		                    $video_information[$single_video_id]['dislikeCount'] = isset($v_info['statistics']['dislikeCount']) ? $v_info['statistics']['dislikeCount'] : "";
		                    $video_information[$single_video_id]['favoriteCount'] = isset($v_info['statistics']['favoriteCount']) ? $v_info['statistics']['favoriteCount'] : "";
		                    $video_information[$single_video_id]['commentCount'] = isset($v_info['statistics']['commentCount']) ? $v_info['statistics']['commentCount'] : "";
		                    $video_information[$single_video_id]['localizations'] = isset($v_info['snippet']['localized']) ? json_encode($v_info['snippet']['description']) : "";
		                }

		            } while ($next_page != '');


		            /* add/update video lists on table */
		            if(isset($video_information)) {

		                $channel_id = $value['id'];
		                $delete_where = array(
		                    'user_id' => $this->comboposter->user_id,
		                    'channel_id' => $channel_id
		                );

		                $this->comboposter->basic->delete_data('youtube_video_list', $delete_where);

		                foreach($video_information as $key => $value) {

		                    $video_data = array(
		                        'user_id' => $this->comboposter->user_id,
		                        'channel_id' => $channel_id,
		                        'video_id' => $key,
		                        'title' => isset($value['title']) ? $value['title'] : "",
		                        'image_link' => isset($value['thumbnails']) ? $value['thumbnails'] : "",
		                        'publish_time' => isset($value['publishedAt']) ? $value['publishedAt'] : "",
		                        'description' => isset($value['description']) ? $value['description'] : "",
		                        'tags' => json_encode(isset($value['tags']) ? $value['tags'] : "") ,
		                        'categoryId' => isset($value['categoryId']) ? $value['categoryId'] : "",
		                        'liveBroadcastContent' => isset($value['liveBroadcastContent']) ? $value['liveBroadcastContent'] : "",
		                        'duration' => isset($value['duration']) ? $value['duration'] : "",
		                        'dimension' => isset($value['dimension']) ? $value['dimension'] : "",
		                        'definition' => isset($value['definition']) ? $value['definition'] : "",
		                        'caption' => isset($value['caption']) ? $value['caption'] : "",
		                        'licensedContent' => isset($value['licensedContent']) ? $value['licensedContent'] : "",
		                        'projection' => isset($value['projection']) ? $value['projection'] : "",
		                        'viewCount' => isset($value['viewCount']) ? $value['viewCount'] : "",
		                        'likeCount' => isset($value['likeCount']) ? $value['likeCount'] : "",
		                        'dislikeCount' => isset($value['dislikeCount']) ? $value['dislikeCount'] : "",
		                        'favoriteCount' => isset($value['favoriteCount']) ? $value['favoriteCount'] : "",
		                        'commentCount' => isset($value['commentCount']) ? $value['commentCount'] : ""
		                    );

		                    $this->comboposter->basic->insert_data('youtube_video_list', $video_data);
		                }
		            }
		        }
		    }
		}

		redirect('comboposter/social_accounts', 'Location');
	}


	public function youtube_playlist_item($access_token, $playlist_id, $next_page = '', $max_result = '')
	{
		$this->comboposter->load->library('Google_youtube_login', NULL, 'youtube');

	    if ($max_result != '') {

	       $url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId={$playlist_id}&mine=true&access_token={$access_token}&maxResults={$max_result}&pageToken={$next_page}";
	    } else {

	       $url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId={$playlist_id}&mine=true&access_token={$access_token}&maxResults=10&pageToken={$next_page}";
	    }

	    return $this->comboposter->youtube->get_curl($url);
	}

	/**
	 * incomplete, need to fix on server
	 */
	public function pinterest($authentication_code)
	{
		// ************************************************//
		$status = $this->comboposter->_check_usage($module_id = 101, $request = 1);
		if ($status == "2") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		} else if ($status == "3") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		}
		// ************************************************//
		
		$this->comboposter->load->library('Pinterests');

		$app_id = $this->comboposter->session->userdata('pinterest_app_id');
		$this->comboposter->session->unset_userdata('pinterest_app_id');

		$this->comboposter->pinterests->app_initialize($app_id);

		/* get user info */

		$user_details = $this->comboposter->pinterests->get_userinfo($authentication_code); 

		if(isset($user_details['error']) && $user_details['error_message']){

			$this->comboposter->session->set_userdata('account_import_error', $user_details['error_message']);
			redirect(base_url('comboposter/social_accounts'),'refresh');
		    exit();
		}


		$pinterest_username = isset($user_details['username']) ? $user_details['username'] :"" ;
		$pinterest_user_id = isset($user_details['id']) ? $user_details['id'] :"" ;
		$pinterest_access_token = isset($user_details['access_token']) ? $user_details['access_token'] :"" ;
		$pinterest_name = $user_details['first_name']." ". $user_details['last_name'];
		$pinterest_image = isset($user_details['image']) ? $user_details['image'] :"" ;
		$pinterest_pins = isset($user_details['pins_count']) ? $user_details['pins_count'] :"" ;
		$pinterest_boards = isset($user_details['boards_count']) ? $user_details['boards_count'] :"" ; 
		$board_list=isset($user_details['board_list']) ? $user_details['board_list']:array();

		if (!isset($pinterest_username) || !isset($pinterest_access_token)) {
		    $this->comboposter->session->set_userdata('account_import_error', $this->comboposter->lang->line("Something went wrong while importing your account."));
			redirect(base_url('comboposter/social_accounts'),'refresh');
		}

		$data = array(
		    'user_id' => $this->comboposter->user_id,
		    'pinterest_user_id' => $pinterest_user_id,
		    'user_name' => $pinterest_username,
		    'name' => $pinterest_name,
		    'image' => $pinterest_image,
		    'pins' => $pinterest_pins,
		    'boards' => $pinterest_boards,
		    'code' => $pinterest_access_token,
		    'add_date' => date('Y-m-d') ,
		    'pinterest_config_table_id' => $app_id
		);

		

		$where['where'] = array(
		    'user_id' => $this->comboposter->user_id,
		    'user_name' => $pinterest_username
		);
		$exist_or_not = $this->comboposter->basic->get_data('pinterest_users_info', $where);

		if(empty($exist_or_not)) {

		    $this->comboposter->basic->insert_data('pinterest_users_info', $data);
		    $pinterest_table_id = $this->comboposter->db->insert_id();
		    $this->comboposter->_insert_usage_log($module_id = 101, $request = 1);
		} else {

		    $pinterest_table_id = $exist_or_not[0]['id'];

		    $where = array(
		        'user_id' => $this->comboposter->user_id,
		        'user_name' => $pinterest_username
		    );

		    $this->comboposter->basic->update_data('pinterest_users_info', $where, $data);
		}

		$data = array();
		foreach($board_list as $value) {

		    $data = array(
		        'pinterest_table_id' => $pinterest_table_id,
		        'user_id' => $this->comboposter->user_id,
		        'board_name' => $value['name'],
		        'board_url' => $value['url'],
		        'board_id'	=> $value['id']
		    );

		    $where = array();
		    $where['where'] = array(
		        'pinterest_table_id' => $pinterest_table_id,
		        'board_name' => $value['name']
		    );
		    $exist_or_not = $this->comboposter->basic->get_data('pinterest_board_info', $where);

		    if(empty($exist_or_not)) {
		        $this->comboposter->basic->insert_data('pinterest_board_info', $data);
		    } else {

		        $where = array(
		            'pinterest_table_id' => $pinterest_table_id,
		           'board_name' => $value['name']
		        );
		        $this->comboposter->basic->update_data('pinterest_board_info', $where, $data);
		    }
		}
		
		redirect(base_url('comboposter/social_accounts'),'refresh');
	}


	public function blogger()
	{
		// ************************************************//
		$status = $this->comboposter->_check_usage($module_id = 107, $request = 1);
		if ($status == "2") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		} else if ($status == "3") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		}
		// ************************************************//
		
		$this->comboposter->load->library('Google_youtube_login', NULL, 'blogger');

		$redirectUrl = base_url("comboposter/login_callback/blogger");

        $token_info = $this->comboposter->blogger->get_blogger_refresh_token($redirectUrl);
        $refresh_token = isset($token_info['refresh_token']) ? $token_info['refresh_token'] : "";
        $access_token = isset($token_info['access_token']) ? $token_info['access_token'] : "";

        $user_info = $this->comboposter->blogger->get_blogger_user_accesstoken($access_token);

        $user_id = "g" . $user_info['id'];
        $post_info = $this->comboposter->blogger->get_bloger_information($user_id, $access_token);
        $blog_count = isset($post_info['items']) ? count($post_info['items']) : 0;

        $data = array(
            'user_id' => $this->comboposter->user_id,
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'name' => $user_info['name'],
            'email' => $user_info['email'],
            'picture' => $user_info['picture'],
            'blogger_id' => $user_info['id'],
            'blog_count' => $blog_count,
            'add_date' => date('Y-m-d')
        );

        $where['where'] = array(
            'user_id' => $this->comboposter->user_id,
            'blogger_id' => $user_info['id']
        );
        $exist_or_not = $this->comboposter->basic->get_data('blogger_users_info', $where);

        if(empty($exist_or_not)) {

            $this->comboposter->basic->insert_data('blogger_users_info', $data);
            $blogger_table_id = $this->comboposter->db->insert_id();
            $this->comboposter->_insert_usage_log($module_id = 107, $request = 1);
        } else {

            $blogger_table_id = $exist_or_not[0]['id'];
            $where = array(
                'user_id' => $this->comboposter->user_id,
                'blogger_id' => $user_info['id']
            );
            $this->comboposter->basic->update_data('blogger_users_info', $where, $data);
        }

        if(isset($post_info['items'])) {

            foreach($post_info['items'] as $value) {

                $data = array(
                    'blogger_users_info_table_id' => $blogger_table_id,
                    'user_id' => $this->comboposter->user_id,
                    'blog_id' => $value['id'],
                    'name' => $value['name']
                );

                $where = array();
                $where['where'] = array(
                    'blogger_users_info_table_id' => $blogger_table_id,
                    'blog_id' => $value['id']
                );
                $exist_or_not = $this->comboposter->basic->get_data('blogger_blog_info', $where);

                if(empty($exist_or_not)) {
                    $this->comboposter->basic->insert_data('blogger_blog_info', $data);
                } else {

                    $where = array(
                        'blogger_users_info_table_id' => $blogger_table_id,
                        'blog_id' => $value['id']
                    );
                    $this->comboposter->basic->update_data('blogger_blog_info', $where, $data);
                }
            }
        }

        redirect('comboposter/social_accounts', 'location');
        
	}


	public function wordpress()
	{
		// ************************************************//
		$status = $this->comboposter->_check_usage($module_id = 108, $request = 1);
		if ($status == "2") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		} else if ($status == "3") {

		    $this->comboposter->session->set_userdata('limit_cross', 'Sorry, your account import limit has been exceeded for Twitter.');
		    redirect('comboposter/social_accounts', 'location');
		    exit();
		}
		// ************************************************//
		
		$redirect_url = base_url("comboposter/login_callback/wordpress");
		$client_id = $this->comboposter->session->userdata('wp_org_client_id');
		$client_secret = $this->comboposter->session->userdata('wp_org_client_secret');

		/* get tokens */
		$curl = curl_init('https://public-api.wordpress.com/oauth2/token');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, array(
		    'client_id' => $client_id,
		    'redirect_uri' => $redirect_url,
		    'client_secret' => $client_secret,
		    'code' => $_GET['code'], // The code from the previous request
		    'grant_type' => 'authorization_code'
		));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$auth = curl_exec($curl);
		$secret = json_decode($auth, true);
		curl_close($curl);



		if (!isset($secret['blog_id']) || !isset($secret['blog_url']) || !isset($secret['access_token'])) {

		    $this->comboposter->session->set_userdata('account_import_error', $this->comboposter->lang->line("Something went wrong while importing your account."));
		    redirect(base_url('comboposter/social_accounts'),'refresh');
		}



		/* get site infos */
		$blog_name = str_replace('http://', '', $secret['blog_url']);
		$blog_name = str_replace('https://', '', $blog_name);

		$url = 'https://public-api.wordpress.com/rest/v1.2/sites/'.$blog_name;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$blog_info = curl_exec($curl);
		$blog_info = json_decode($blog_info, true);
		curl_close($curl);

		$name = isset($blog_info['name']) ? $blog_info['name'] : $this->comboposter->lang->line("Name not found");
		$icon = isset($blog_info['icon']['img']) ? $blog_info['icon']['img'] : base_url('assets/images/wordpress.png');
		// echo "<pre>";print_r($blog_info);exit;


		/* get posts amount */
		$post_info_url = isset($blog_info['meta']['links']['posts']) ? $blog_info['meta']['links']['posts'] : '';

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $post_info_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$blog_post_info = curl_exec($curl);
		$blog_post_info = json_decode($blog_post_info, true);
		curl_close($curl);

		$post_found = isset($blog_post_info['found']) ? $blog_post_info['found'] : 0;

		/* add/update table data */
		$data = array(
		    'user_id' => $this->comboposter->user_id,
		    'blog_id' => $secret['blog_id'],
		    'blog_url' => $secret['blog_url'],
		    'name' => $name,
		    'icon' => $icon,
		    'posts' => $post_found,
		    'access_token' => $secret['access_token'],
		    'last_update_time' => date("Y-m-d H:i:s")
		);
		$exist_or_not = $this->comboposter->basic->get_data('wordpress_users_info', array(
		    'where' => array(
		        'user_id' => $this->comboposter->user_id,
		        'blog_id' => $secret['blog_id']
		    )
		));

		if(empty($exist_or_not)) {

		    $this->comboposter->basic->insert_data('wordpress_users_info', $data);
		    $this->comboposter->_insert_usage_log($module_id = 108, $request = 1);
		} else {

			$this->comboposter->basic->update_data('wordpress_users_info', array(
		    	'user_id' => $this->comboposter->user_id,
		    	'blog_id' => $secret['blog_id']
			) , $data);
		}

		redirect('comboposter/social_accounts', 'location');
	}	
}