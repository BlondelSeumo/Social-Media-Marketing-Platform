<?php
require_once('Google_youtube/autoload.php');
require_once ('Google_youtube/Client.php');
require_once ('Google_youtube/Service/YouTube.php');

class Google_youtube_login {

	public $client="";
	public $secret="";
	public $redirectUrl= "";

	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->model('basic');
		$this->CI->load->helper('url_helper');
		$this->CI->load->library('session');
		$login_config=$this->CI->basic->get_data("login_config",array("where"=>array("status"=>"1")));
		if(isset($login_config[0]))
		{
			$this->client=$login_config[0]["google_client_id"];
			$this->secret=$login_config[0]["google_client_secret"];
		}

		$this->redirectUrl = site_url("comboposter/login_callback/youtube");
	}


	public function youtube_login_button($redirectUrl = ''){

		if ($redirectUrl == '') {
			$redirectUrl = $this->redirectUrl;
		}

		$client = $this->client;
		$login_url="https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri={$redirectUrl}&client_id={$client}&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https://www.googleapis.com/auth/youtube+https://www.googleapis.com/auth/yt-analytics.readonly+https://www.googleapis.com/auth/yt-analytics-monetary.readonly&access_type=offline&approval_prompt=force";

		if (empty($this->client) || empty($this->secret)) {

			if ($this->CI->session->userdata("user_type") == "Admin") {
			  $login_url = base_url('social_apps/google_settings'); 
			} else {
			  $login_url = base_url('comboposter/set_empty_app_error/youtube');
			}
		}

		return "<a href='{$login_url}' class='btn btn-outline-primary login_button' social_account='youtube'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
	}


	public function get_channel_list()
	{
		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->client);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri($this->redirectUrl);
		$gClient->setAccessType("offline");

		$gClient->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner", 'https://www.googleapis.com/auth/youtubepartner-content-owner-readonly'));

		if(isset($_GET['code'])){
			$gClient->authenticate($_GET['code']);
			$access_token=$gClient->getAccessToken();
			if(isset($access_token)){
				$gClient->setAccessToken($access_token);
			}
		}

		$access_token_array=json_decode($access_token,true);
		$channel_list_info['json_access_token'] = $access_token;

		$channel_list_info['channel_list'] = $this->get_channel_content_details($access_token_array['access_token']);

		return $channel_list_info;
	}



	function get_channel_content_details($access_token){
		$url ="https://www.googleapis.com/youtube/v3/channels?part=contentDetails,snippet,statistics&mine=true&access_token={$access_token}";

		return $this->get_curl($url);
	}


	function get_video_details_list($access_token,$video_ids){

	 	$part=urlencode("contentDetails,statistics,snippet");
	 	$url ="https://www.googleapis.com/youtube/v3/videos?part={$part}&id={$video_ids}&mine=true&access_token={$access_token}&maxResults=50";
	 	return $this->get_curl($url);

	 }



	function get_curl($url){
		$ch = curl_init();
		$headers = array("Content-type: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
		$st=curl_exec($ch);
		return $result=json_decode($st,TRUE);
	}



	public function get_channel_analytics($channel_id='',$metrics='',$dimension='',$sort='',$max_result='',$start_date='',$end_date='')
	{
		if($this->CI->session->userdata('individual_channel_access_token') != '')
		{
			$access_token = $this->CI->session->userdata('individual_channel_access_token');
		}
		if($this->CI->session->userdata('individual_channel_refresh_token') != '')
		{
			$refresh_token = $this->CI->session->userdata('individual_channel_refresh_token');
		}

		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->client);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri($this->redirectUrl);
		$gClient->setAccessType("offline");

		$gClient->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner", 'https://www.googleapis.com/auth/youtubepartner-content-owner-readonly'));
		$gClient->refreshToken($refresh_token);
		$gClient->setAccessToken($access_token);


		$analytics = new Google_Service_YouTubeAnalytics($gClient);

		$id = "channel=={$channel_id}";

		// $end_date = date("Y-m-d");
		// $start_date = date('Y-m-d', strtotime("-28 days"));

		if($dimension!='')
			$optparams['dimensions'] = $dimension;
		if($sort!='')
			$optparams['sort'] = $sort;
		if($max_result!='')
			$optparams['max-results'] = $max_result;

		$analytics_info = $analytics->reports->query($id, $start_date, $end_date, $metrics, $optparams);
		return $analytics_info;
	}



	public function get_video_analytics($channel_id='',$metrics='',$dimension='',$sort='',$filter='',$max_result='',$start_date='',$end_date='')
	{
		if($this->CI->session->userdata('individual_video_access_token') != '')
		{
			$access_token = $this->CI->session->userdata('individual_video_access_token');
		}
		if($this->CI->session->userdata('individual_video_refresh_token') != '')
		{
			$refresh_token = $this->CI->session->userdata('individual_video_refresh_token');
		}

		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->client);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri($this->redirectUrl);
		$gClient->setAccessType("offline");

		$gClient->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner", 'https://www.googleapis.com/auth/youtubepartner-content-owner-readonly'));
		$gClient->setAccessToken($access_token);
		$gClient->refreshToken($refresh_token);


		$analytics = new Google_Service_YouTubeAnalytics($gClient);

		$id = "channel=={$channel_id}";

		// $end_date = date("Y-m-d");
		// $start_date = date('Y-m-d', strtotime("-28 days"));

		if($dimension!='')
			$optparams['dimensions'] = $dimension;
		if($sort!='')
			$optparams['sort'] = $sort;
		if($filter!='')
			$optparams['filters'] = $filter;
		if($max_result!='')
			$optparams['max-results'] = $max_result;

		$analytics_info = $analytics->reports->query($id, $start_date, $end_date, $metrics, $optparams);
		return $analytics_info;
	}



	public function uploa_video_to_youtube($title='',$description='',$video_link='',$tags='',$category_id='',$privacy_type='',$live_streaming_flag=0)
	{
		if($this->CI->session->userdata('youtube_upload_access_token') != '')
		{
			$access_token = $this->CI->session->userdata('youtube_upload_access_token');
		}
		if($this->CI->session->userdata('youtube_upload_refresh_token') != '')
		{
			$refresh_token = $this->CI->session->userdata('youtube_upload_refresh_token');
		}

		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->client);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri($this->redirectUrl);
		$gClient->setAccessType("offline");

		$gClient->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner", 'https://www.googleapis.com/auth/youtubepartner-content-owner-readonly'));
		$gClient->setAccessToken($access_token);
		$gClient->refreshToken($refresh_token);


		$youtube = new Google_Service_YouTube($gClient);
		$snippet = new Google_Service_YouTube_VideoSnippet();

		try{
			$tags = explode(',', $tags);
			// used for video matrix video upload
			if($live_streaming_flag == 0)
				$videoPath = realpath(FCPATH."upload/video/".$video_link);
			else
				$videoPath = realpath(FCPATH.$video_link);

			$snippet = new Google_Service_YouTube_VideoSnippet();
			$snippet->setTitle($title);
			$snippet->setDescription($description);
			$snippet->setTags($tags);


    		// https://developers.google.com/youtube/v3/docs/videoCategories/list
			$snippet->setCategoryId($category_id);
			$status = new Google_Service_YouTube_VideoStatus();
			$status->privacyStatus = $privacy_type;

			$video = new Google_Service_YouTube_Video();
			$video->setSnippet($snippet);
			$video->setStatus($status);

			$chunkSizeBytes = 1 * 1024 * 1024;

    		// Setting the defer flag to true tells the client to return a request which can be called
    		// with ->execute(); instead of making the API call immediately.
			$gClient->setDefer(true);

			$insertRequest = $youtube->videos->insert("status,snippet", $video);

			$media = new Google_Http_MediaFileUpload(
				$gClient,
				$insertRequest,
				'video/*',
				null,
				true,
				$chunkSizeBytes
				);
			$media->setFileSize(filesize($videoPath));

			$status = false;
			$handle = fopen($videoPath, "rb");
			while (!$status && !feof($handle)) {
				$chunk = fread($handle, $chunkSizeBytes);
				$status = $media->nextChunk($chunk);
			}

			fclose($handle);

    		// If you want to make other calls after the file upload, set setDefer back to false
			$gClient->setDefer(false);


			// $htmlBody .= "<h3>Video Uploaded</h3><ul>";
			// $htmlBody .= sprintf('<li>%s (%s)</li>',
			// 	$status['snippet']['title'],
			// 	$status['id']);

			// $htmlBody .= '</ul>';
			$response = $status['id'];

		} catch (Google_Service_Exception $e) {
			// $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
				// htmlspecialchars($e->getMessage()));
			$response = '<p>A service error occurred: <code>'.htmlspecialchars($e->getMessage()).'</code></p>';
		} catch (Google_Exception $e) {
			// $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
				// htmlspecialchars($e->getMessage()));
			$response = '<p>A service error occurred: <code>'.htmlspecialchars($e->getMessage()).'</code></p>';
		}



		return $response;

	}



	public function upload_live_event_youtube($title='',$description='',$tags='',$privacy_type='',$start_time='',$end_time='',$time_zone='',$thumnail_path='')
	{
		$tags = explode(',', $tags);

		if($this->CI->session->userdata('videomatrix_liveevent_access_token') != '')
		{
			$access_token = $this->CI->session->userdata('videomatrix_liveevent_access_token');
		}
		if($this->CI->session->userdata('videomatrix_liveevent_refresh_token') != '')
		{
			$refresh_token = $this->CI->session->userdata('videomatrix_liveevent_refresh_token');
		}

		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->client);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri($this->redirectUrl);
		$gClient->setAccessType("offline");

		$gClient->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner", 'https://www.googleapis.com/auth/youtubepartner-content-owner-readonly'));
		$gClient->setAccessToken($access_token);
		$gClient->refreshToken($refresh_token);

		$youtube = new Google_Service_YouTube($gClient);


		try {
			$broadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
			$broadcastSnippet->setTitle($title);
			$broadcastSnippet->setDescription($description);
			date_default_timezone_set($time_zone);
            $start_time = date("c", strtotime($start_time));
            $end_time = date("c", strtotime($end_time));

			$broadcastSnippet->setScheduledStartTime($start_time);
			$broadcastSnippet->setScheduledEndTime($end_time);

			$status = new Google_Service_YouTube_LiveBroadcastStatus();
			$status->setPrivacyStatus($privacy_type);


			$broadcastInsert = new Google_Service_YouTube_LiveBroadcast();
			$broadcastInsert->setSnippet($broadcastSnippet);
			$broadcastInsert->setStatus($status);
			$broadcastInsert->setKind('youtube#liveBroadcast');

			$broadcastsResponse = $youtube->liveBroadcasts->insert('snippet,status',
				$broadcastInsert, array());

			$streamSnippet = new Google_Service_YouTube_LiveStreamSnippet();
			$streamSnippet->setTitle($title);
			$streamSnippet->setDescription($description);

			$cdn = new Google_Service_YouTube_CdnSettings();
			$cdn->setFormat("1080p");
			$cdn->setIngestionType('rtmp');

			$streamInsert = new Google_Service_YouTube_LiveStream();
			$streamInsert->setSnippet($streamSnippet);
			$streamInsert->setCdn($cdn);
			$streamInsert->setKind('youtube#liveStream');

			$streamsResponse = $youtube->liveStreams->insert('snippet,cdn',
				$streamInsert, array());

			$bindBroadcastResponse = $youtube->liveBroadcasts->bind(
				$broadcastsResponse['id'],'id,contentDetails',
				array(
					'streamId' => $streamsResponse['id'],
					));

			$response = array();

			$response['Broadcast_id'] = $broadcastsResponse['id']; //boradcast id == video id
			$response['Stream_id'] = $streamsResponse['id'];
			$response['boundBroadcast_id'] = $bindBroadcastResponse['id'];
			$response['boundStream_id'] = $bindBroadcastResponse['contentDetails']['boundStreamId'];

			$streamsResponse = json_encode($streamsResponse->toSimpleObject());
			$streamsResponse =json_decode($streamsResponse,TRUE);
			$stream_name= $streamsResponse['cdn']['ingestionInfo']['streamName'];
			$stream_ingestion_address= $streamsResponse['cdn']['ingestionInfo']['ingestionAddress'];
			$response['stream_ingestion_address'] = $stream_ingestion_address;
			$response['stream_name'] = $stream_name;

			// add tags to video
			// Call the API's videos.list method to retrieve the video resource.
		    $listResponse = $youtube->videos->listVideos("snippet",
		        array('id' => $broadcastsResponse['id']));
			$video = $listResponse[0];
			$videoSnippet = $video['snippet'];
			$existing_tags = $videoSnippet['tags'];

			if (is_null($existing_tags))
				$tags_to_update = $tags;
			else
				$tags_to_update = array_add($existing_tags, $tags);

			$tags_to_update = array_unique($tags_to_update);

			// Set the tags array for the video snippet
			$videoSnippet['tags'] = $tags_to_update;
			// Update the video resource by calling the videos.update() method.
			$youtube->videos->update("snippet", $video);
			// end of adding tags to video


		} catch (Google_Service_Exception $e) {
			$response = array();
			$response['error'] = '<p>A service error occurred: <code>'.htmlspecialchars($e->getMessage()).'</code></p>';
			return $response;
		} catch (Google_Exception $e) {
			$response = array();
			$response['error'] = '<p>A service error occurred: <code>'.htmlspecialchars($e->getMessage()).'</code></p>';
			return $response;
		}

		if($thumnail_path != '')
		{
			try{
			    $videoId = $broadcastsResponse['id'];
			    $imagePath = $thumnail_path;
			    $chunkSizeBytes = 1 * 1024 * 1024;
			    $gClient->setDefer(true);
			    $setRequest = $youtube->thumbnails->set($videoId);
			    $media = new Google_Http_MediaFileUpload(
			        $gClient,
			        $setRequest,
			        'image/png',
			        null,
			        true,
			        $chunkSizeBytes
			    );
			    $media->setFileSize(filesize($imagePath));
			    $status = false;
			    $handle = fopen($imagePath, "rb");
			    while (!$status && !feof($handle)) {
			      $chunk = fread($handle, $chunkSizeBytes);
			      $status = $media->nextChunk($chunk);
			    }

			    fclose($handle);
			    $gClient->setDefer(false);

			  } catch (Google_Service_Exception $e) {
			    $response = array();
				$response['error_thumbnail'] = '<p>A service error occurred: <code>'.htmlspecialchars($e->getMessage()).'</code></p>';
			  } catch (Google_Exception $e) {
			    $response = array();
				$response['error_thumbnail'] = '<p>A service error occurred: <code>'.htmlspecialchars($e->getMessage()).'</code></p>';
			  }
		}


		return $response;


	}





	public function youtube_live_transition($video_id)
	{

		if($this->CI->session->userdata('videomatrix_liveevent_access_token') != '')
		{
			$access_token = $this->CI->session->userdata('videomatrix_liveevent_access_token');
		}
		if($this->CI->session->userdata('videomatrix_liveevent_refresh_token') != '')
		{
			$refresh_token = $this->CI->session->userdata('videomatrix_liveevent_refresh_token');
		}

		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->client);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri($this->redirectUrl);
		$gClient->setAccessType("offline");

		$gClient->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner", 'https://www.googleapis.com/auth/youtubepartner-content-owner-readonly'));
		$gClient->setAccessToken($access_token);
		$gClient->refreshToken($refresh_token);

		$youtube = new Google_Service_YouTube($gClient);
			/***	First Try  Testing***/

			// sleep(2);


			for($i=0;$i<=10;$i++){

				$is_testing_error="no";

				try{

		 		$bindBroadcastResponse = $youtube->liveBroadcasts->transition("testing",$video_id,"status");

				}

		  		catch (Google_Service_Exception $e) {
					$is_testing_error="yes";
					sleep(1);
					// echo htmlspecialchars($e->getMessage());

		  		} catch (Google_Exception $e) {
					  $is_testing_error="yes";
					sleep(1);
		 		 }

				 if($is_testing_error=="no")
				 	break;
			}



		sleep(2);

		/***After Test Start Live ****/


		for($i=0;$i<=20;$i++){

				$is_live_error="no";

				try{

		 		$bindBroadcastResponse = $youtube->liveBroadcasts->transition("live",$video_id,"status");

				 // echo "<pre>";
		 		// 	print_r($bindBroadcastResponse);

				}

		  		catch (Google_Service_Exception $e) {
					$is_live_error="yes";
					// echo htmlspecialchars($e->getMessage());
					sleep(1);

		  		} catch (Google_Exception $e) {
					  $is_live_error="yes";
					sleep(1);

		 		 }

				 if($is_live_error=="no")
				 	break;
			}

		if($is_live_error == 'yes')
			return "error";
		else
			return 'success';

	}


	public function youtube_live_transition_complete($video_id)
	{

		if($this->CI->session->userdata('videomatrix_liveevent_access_token') != '')
		{
			$access_token = $this->CI->session->userdata('videomatrix_liveevent_access_token');
		}
		if($this->CI->session->userdata('videomatrix_liveevent_refresh_token') != '')
		{
			$refresh_token = $this->CI->session->userdata('videomatrix_liveevent_refresh_token');
		}

		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->client);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri($this->redirectUrl);
		$gClient->setAccessType("offline");

		$gClient->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner", 'https://www.googleapis.com/auth/youtubepartner-content-owner-readonly'));
		$gClient->setAccessToken($access_token);
		$gClient->refreshToken($refresh_token);

		$youtube = new Google_Service_YouTube($gClient);
			/***	First Try  Testing***/

			for($i=0;$i<=10;$i++){

				$is_testing_error="no";

				try{

		 		$bindBroadcastResponse = $youtube->liveBroadcasts->transition("complete",$video_id,"status");

				}

		  		catch (Google_Service_Exception $e) {
					$is_testing_error="yes";


		  		} catch (Google_Exception $e) {
					  $is_testing_error="yes";

		 		 }

				 if($is_testing_error=="no")
				 	break;
			}



	}


	public function cronjob_upload_video_to_youtube($title='',$description='',$video_link='',$tags='',$category_id='',$privacy_type='')
	{
		if($this->CI->session->userdata('cronjob_upload_access_token') != '')
		{
			$access_token = $this->CI->session->userdata('cronjob_upload_access_token');
		}
		if($this->CI->session->userdata('cronjob_upload_refresh_token') != '')
		{
			$refresh_token = $this->CI->session->userdata('cronjob_upload_refresh_token');
		}

		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->client);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri($this->redirectUrl);
		$gClient->setAccessType("offline");

		$gClient->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner", 'https://www.googleapis.com/auth/youtubepartner-content-owner-readonly'));
		$gClient->setAccessToken($access_token);
		$gClient->refreshToken($refresh_token);


		$youtube = new Google_Service_YouTube($gClient);
		$snippet = new Google_Service_YouTube_VideoSnippet();

		$video_link = str_replace(base_url(), '', $video_link);

		try{
			$tags = explode(',', $tags);
			$videoPath = realpath(FCPATH.$video_link);
			$snippet = new Google_Service_YouTube_VideoSnippet();
			$snippet->setTitle($title);
			$snippet->setDescription($description);
			$snippet->setTags($tags);


    		// https://developers.google.com/youtube/v3/docs/videoCategories/list
			$snippet->setCategoryId($category_id);
			$status = new Google_Service_YouTube_VideoStatus();
			$status->privacyStatus = $privacy_type;

			$video = new Google_Service_YouTube_Video();
			$video->setSnippet($snippet);
			$video->setStatus($status);

			$chunkSizeBytes = 1 * 1024 * 1024;

    		// Setting the defer flag to true tells the client to return a request which can be called
    		// with ->execute(); instead of making the API call immediately.
			$gClient->setDefer(true);

			$insertRequest = $youtube->videos->insert("status,snippet", $video);

			$media = new Google_Http_MediaFileUpload(
				$gClient,
				$insertRequest,
				'video/*',
				null,
				true,
				$chunkSizeBytes
				);
			$media->setFileSize(filesize($videoPath));

			$status = false;
			$handle = fopen($videoPath, "rb");
			while (!$status && !feof($handle)) {
				$chunk = fread($handle, $chunkSizeBytes);
				$status = $media->nextChunk($chunk);
			}

			fclose($handle);

    		// If you want to make other calls after the file upload, set setDefer back to false
			$gClient->setDefer(false);


			// $htmlBody .= "<h3>Video Uploaded</h3><ul>";
			// $htmlBody .= sprintf('<li>%s (%s)</li>',
			// 	$status['snippet']['title'],
			// 	$status['id']);

			// $htmlBody .= '</ul>';
			$response = $status['id'];

		} catch (Google_Service_Exception $e) {
			// $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
				// htmlspecialchars($e->getMessage()));
			$response = '<p>A service error occurred: <code>'.htmlspecialchars($e->getMessage()).'</code></p>';
		} catch (Google_Exception $e) {
			// $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
				// htmlspecialchars($e->getMessage()));
			$response = '<p>A service error occurred: <code>'.htmlspecialchars($e->getMessage()).'</code></p>';
		}



		return $response;

	}


	public function blogger_login_button($redirectUrl){

		$client = $this->client;
		$login_url="https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri={$redirectUrl}&client_id={$client}&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https://www.googleapis.com/auth/blogger&access_type=offline&approval_prompt=force";

		if(empty($this->client) || empty($this->secret)) {

			if ($this->CI->session->userdata("user_type") == "Admin") {
			  $login_url = base_url('social_apps/google_settings'); 
			} else {
			  $login_url = base_url('comboposter/set_empty_app_error/blogger');
			}
		}

		return "<a href='{$login_url}' class='btn btn-outline-primary login_button' social_account='blogger'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";

	}



	public function get_blogger_refresh_token($redirectUrl){
		// $redirectUrl = base_url()."rx_video_autopost/blogger_login_callback";
		$authentication_code = $_GET['code'];
		$url = "https://accounts.google.com/o/oauth2/token";
 		$post="code={$authentication_code}&client_id={$this->client}&client_secret={$this->secret}&redirect_uri={$redirectUrl}&grant_type=authorization_code";

	     $ch = curl_init();
	     curl_setopt($ch, CURLOPT_URL, $url);
	     curl_setopt($ch, CURLOPT_POST, true);
	     curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	     curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
	     curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");

		 $st=curl_exec($ch);
		 $result=json_decode($st,TRUE);

		 return $result;

	}


	public function get_blogger_user_accesstoken($access_token){

	  $url = "https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token={$access_token}";
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
	  // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
      curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");

	 $st=curl_exec($ch);
	 $result=json_decode($st,TRUE);

	 return $result;

	}




	public function get_bloger_information($user_id,$acces_token){

		 $url="https://www.googleapis.com/blogger/v3/users/{$user_id}/blogs";
		 $ch = curl_init();
		 $headers = array("Authorization: OAuth {$acces_token}");
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	     curl_setopt($ch, CURLOPT_URL, $url);
		 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	     curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
	     curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
		 $st=curl_exec($ch);
		 return $result=json_decode($st,TRUE);
	}



	function get_access_token_from_refresh_token($refresh_token){
		$client = $this->client;
		$secret = $this->secret;
		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($client);
		$gClient->setClientSecret($secret);
		// $gClient->setRedirectUri($redirectUrl);
		$gClient->setAccessType("offline");
		$gClient->setScopes(array('https://www.googleapis.com/auth/blogger'));
		$gClient->refreshToken($refresh_token);
		$access_token=$gClient->getAccessToken();

		$access_token = json_decode($access_token,true);
		return $access_token['access_token'];
	}

	public function post_to_blogger($blogid,$acces_token,$title,$content){

		$url = 'https://www.googleapis.com/blogger/v3/blogs/'.$blogid.'/posts/';
		$postData = array(
		    'kind' => 'blogger#post',
		    'blog' => array('id' => $blogid),
		    'title' => $title,
		    'content' => $content
		);

		$postData=json_encode($postData);
  		$ch = curl_init();
		$headers = array("Authorization: OAuth {$acces_token}","Content-Type: application/json");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
	  	$response=curl_exec($ch);
	 	$response = json_decode($response,TRUE);
		return $response;

	}





}

?>
