<?php
require_once('tumblroauth/tumblroauth.php');
require_once('tumblr_vendor/autoload.php');

class Tumblr {

		public $user_id="";
		public $consumer_key="";
		public $consumer_secret="";


	function __construct(){

		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");


		$tumblr_config = $this->CI->basic->get_data("tumblr_config",array('where'=>array('deleted'=>'0', "status"=>"1")));
		if(isset($tumblr_config[0]))
		{
			$this->consumer_key=$tumblr_config[0]["consumer_id"];
			$this->consumer_secret=$tumblr_config[0]["consumer_secret"];
		}

		if (session_status() == PHP_SESSION_NONE)
		{
		    session_start();
		}

	}

	public function login_button($callback_url)
	{

		if ($this->CI->session->userdata("user_type") == "Admin") {
        	$auth_url = base_url('social_apps/tumblr_settings'); 
        } else {

        	$auth_url = base_url('comboposter/set_empty_app_error/tumblr');

        	return "<a href='{$auth_url}' class='btn btn-outline-primary login_button' social_account='tumblr'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
        }

		
		$client = new Tumblr\API\Client($this->consumer_key, $this->consumer_secret);

		$requestHandler = $client->getRequestHandler();
		$requestHandler->setBaseUrl('https://www.tumblr.com/');

		$response = $requestHandler->request('POST', 'oauth/request_token', [
		    'oauth_callback' => $callback_url
		]);


		parse_str((string) $response->body, $tokens);


		if(!isset($tokens['oauth_token']) || !isset($tokens['oauth_token_secret'])) {

			return "<a href='' class='btn btn-outline-primary login_button' social_account='tumblr'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
		}


		$this->CI->session->set_userdata('tumblr_auth_token', $tokens['oauth_token']);
		$this->CI->session->set_userdata('tumblr_auth_token_secret', $tokens['oauth_token_secret']);

		return "<a href='https://www.tumblr.com/oauth/authorize?oauth_token=" .$tokens['oauth_token'] . "' class='btn btn-outline-primary login_button' social_account='tumblr'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
	}

	public function get_username($auth_token,$auth_token_secret,$auth_varifier)
	{
		

		$client = new Tumblr\API\Client($this->consumer_key, $this->consumer_secret, $auth_token, $auth_token_secret);

		$requestHandler = $client->getRequestHandler();
		$requestHandler->setBaseUrl('https://www.tumblr.com/');

		$response = $requestHandler->request('POST', 'oauth/access_token', [
		    'oauth_verifier' => $auth_varifier
		]);


		parse_str((string) $response->body, $tokens);

		$client = new Tumblr\API\Client($this->consumer_key, $this->consumer_secret, $tokens['oauth_token'], $tokens['oauth_token_secret']);
		

		foreach ($client->getUserInfo()->user->blogs as $blog) {
		  $result['user_name'] = $blog->name;
		}

		return $result;

	}


	public function get_username1($auth_token,$auth_token_secret,$auth_varifier)
	{
		// Create instance of TumblrOAuth.
		// It'll need our Consumer Key and Secret as well as our Request Token and Secret
		$tum_oauth = new TumblrOAuth($this->consumer_key, $this->consumer_secret, $auth_token, $auth_token_secret);

		// Ok, let's get an Access Token. We'll need to pass along our oauth_verifier which was given to us in the URL.
		$access_token = $tum_oauth->getAccessToken($auth_varifier);

		// Make sure nothing went wrong.
		if (200 == $tum_oauth->http_code) {
		  // good to go
		} else {
		  // die('Unable to authenticate');
		  $response = "Unable to authenticate";
		  return $response;
		}

		$tum_oauth = new TumblrOAuth($this->consumer_key, $this->consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

		$response['auth_token'] = $access_token['oauth_token'];
		$response['auth_token_secret'] = $access_token['oauth_token_secret'];

		// Make an API call with the TumblrOAuth instance.  There's also a post and delete method too.
		$userinfo = $tum_oauth->get('http://api.tumblr.com/v2/user/info');

		// You don't actuall have to pass a full URL,  TukmblrOAuth will complete the URL for you.
		// This will also work: $userinfo = $tum_oauth->get('user/info');

		// Check for an error.
		if (200 == $tum_oauth->http_code) {
		  // good to go
		} else {
		  // die('Unable to get info');
		  $response['error'] = "Unable to get info";
		  return $response;
		}


		$client = new Tumblr\API\Client($this->consumer_key, $this->consumer_secret);
		$client->setToken($access_token['oauth_token'], $access_token['oauth_token_secret']);



		foreach ($client->getUserInfo()->user->blogs as $blog) {
		  $response['user_name'] = $blog->name;
		}

		return $response;

	}


	public function tumblr_login_info($auth_token,$auth_token_secret,$auth_varifier)
	{

		$client = new Tumblr\API\Client($this->consumer_key, $this->consumer_secret, $auth_token, $auth_token_secret);

		$requestHandler = $client->getRequestHandler();
		$requestHandler->setBaseUrl('https://www.tumblr.com/');

		$response = $requestHandler->request('POST', 'oauth/access_token', [
		    'oauth_verifier' => $auth_varifier
		]);


		parse_str((string) $response->body, $tokens);
		// echo "<pre>";print_r($tokens);exit;

		$client = new Tumblr\API\Client($this->consumer_key, $this->consumer_secret, $tokens['oauth_token'], $tokens['oauth_token_secret']);
		// echo "<pre>";print_r($client->getUserInfo());exit;

		foreach ($client->getUserInfo()->user->blogs as $blog) {
		  $result['user_name'] = $blog->name;
		  $result['user_title'] = $blog->title;
		  $result['user_followers'] = $blog->followers;
		}

		return $result;

	}


	public function create_post($auth_token,$auth_token_secret,$auth_varifier,$username,$link,$title,$message)
	{
		//posting URI - http://www.tumblr.com/docs/en/api/v2#posting
		$post_URI = 'http://api.tumblr.com/v2/blog/'.$username.'/post';

		$tum_oauth = new TumblrOAuth($this->consumer_key, $this->consumer_secret, $auth_token, $auth_token_secret);

		// Make an API call with the TumblrOAuth instance. For text Post, pass parameters of type, title, and body
		$embed = '<iframe width="854" height="480" src="'.$link.'" frameborder="0" allowfullscreen></iframe>';
		$arrMessage = array(
		              'type' => 'video',
		              'embed' => $embed,
		              'title' => $title,
		              'caption' => $message
		              );

		$post_info = $tum_oauth->post($post_URI,$arrMessage);

		// Check for an error.
		if (201 == $tum_oauth->http_code) {
			$response['id'] = $post_info->response->id;
		} else {
		  $response['error'] = '1';
		}

		return $response;

	}

	public function text_posts($auth_token,$auth_token_secret,$auth_varifier,$username,$title,$body)
	{
		$post_URI = 'http://api.tumblr.com/v2/blog/'.$username.'/post';
		$tum_oauth = new TumblrOAuth($this->consumer_key, $this->consumer_secret, $auth_token, $auth_token_secret);
		$arrMessage = array(
		    'type' => 'text',
		    'title' => $title,
		    'body' => $body
		);
		$post_info = $tum_oauth->post($post_URI,$arrMessage);

		if (201 == $tum_oauth->http_code) {
			$response['id'] = $post_info->response->id;
		} else {
		  $response['error'] = '1';
		}

		return $response;
	}

	public function link_posts($auth_token,$auth_token_secret,$auth_varifier,$username,$url,$thumbnail,$description)
	{
		$post_URI = 'http://api.tumblr.com/v2/blog/'.$username.'/post';
		$tum_oauth = new TumblrOAuth($this->consumer_key, $this->consumer_secret, $auth_token, $auth_token_secret);

		$arrMessage = array(
		    'type' => 'link',
		    'url' => $url,
		    'description' => $description
		);
		if ($thumbnail != "") {
				$arrMessage['thumbnail'] = $thumbnail;
		}
		$post_info = $tum_oauth->post($post_URI,$arrMessage);

		if (201 == $tum_oauth->http_code) {
			$response['id'] = $post_info->response->id;
		} else {
		  $response['error'] = '1';
		}

		return $response;
	}


	public function photo_posts($auth_token,$auth_token_secret,$auth_varifier,$username,$source,$description)
	{
		$post_URI = 'http://api.tumblr.com/v2/blog/'.$username.'/post';
		$tum_oauth = new TumblrOAuth($this->consumer_key, $this->consumer_secret, $auth_token, $auth_token_secret);
		$arrMessage = array(
		    'type' => 'photo',
		    'source' => $source,
				'caption' => $description
		);
		$post_info = $tum_oauth->post($post_URI,$arrMessage);

		if (201 == $tum_oauth->http_code) {
			$response['id'] = $post_info->response->id;
		} else {
		  $response['error'] = '1';
		}

		return $response;
	}

	public function video_posts($auth_token,$auth_token_secret,$auth_varifier,$username,$video_url,$title,$description)
	{
		$post_URI = 'http://api.tumblr.com/v2/blog/'.$username.'/post';

		$embed = "<iframe width='854' height='480' src='{$video_url}' frameborder='0' allowfullscreen></iframe>";

		$tum_oauth = new TumblrOAuth($this->consumer_key, $this->consumer_secret, $auth_token, $auth_token_secret);
		$arrMessage = array(
		    'type' => 'video',
			'embed' => $embed,
        	'title' => $title,
			'caption' => $description
		);
		$post_info = $tum_oauth->post($post_URI,$arrMessage);

		if (201 == $tum_oauth->http_code) {
			$response['id'] = $post_info->response->id;
		} else {
		  $response['error'] = '1';
		}

		return $response;
	}


}
