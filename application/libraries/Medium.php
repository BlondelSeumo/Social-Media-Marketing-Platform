<?php

class Medium {

	public $user_id="";
	public $client_id="";
	public $client_secret="";

	function __construct(){

		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");

		// $medium_config = $this->CI->basic->get_data("medium_config",array('where'=>array("status"=>"1",'deleted'=>'0')));

		// if(isset($medium_config[0]))
		// {
		// 	$this->client_id=$medium_config[0]["client_id"];
		// 	$this->client_secret=$medium_config[0]["client_secret"];
		// }
	}



	public function login_button($redirect_url)

	{

		$auth_url="https://medium.com/m/oauth/authorize?client_id={$this->client_id}&scope=basicProfile,publishPost&state=VideoMatrix&response_type=code&redirect_uri={$redirect_url}";

		if (empty($this->client_id) || empty($this->client_secret)) {

			if ($this->CI->session->userdata("user_type") == "Admin") {
				$auth_url = base_url('social_apps/medium_settings'); 
			}
			else {
				$auth_url = base_url('comboposter/set_empty_app_error/medium');
			}

		}

		return "<a href='{$auth_url}' class='btn btn-outline-primary btn-rounded'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
	}


	public function get_access_and_refresh_token($code,$redirect_url)
	{
		$post_data=array(
			'client_id' => $this->client_id,
			'redirect_uri' => $redirect_url,
			'client_secret' => $this->client_secret,
			'code' => $code,
			'grant_type' => 'authorization_code'

		);

		$post_data=http_build_query($post_data);

		/**Get access token **/
		$headers = array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Accept-Charset' => 'utf-8'
		);

		$curl = curl_init('https://api.medium.com/v1/tokens');

		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$auth = curl_exec( $curl );

		$secret = json_decode($auth, true);

		return $secret;
	}


	public function get_access_token_from_refresh_token($refresh_token)
	{
		$post_data=array(
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'grant_type' => 'refresh_token',
			'refresh_token'=>$refresh_token
		);

		$post_data=http_build_query($post_data);

		/**Get access token **/
		$headers = array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Accept-Charset' => 'utf-8'
		);

		$curl = curl_init('https://api.medium.com/v1/tokens');

		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$auth = curl_exec( $curl );

		$secret = json_decode($auth);
		$access_key = $secret->access_token;

		return $access_key;

	}


	public function get_user_info($integration_token){

		// $access_token = $this->get_access_token_from_refresh_token($refresh_token);

		$headers = array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Accept-Charset' => 'utf-8',
			"Authorization: Bearer {$integration_token}");

		$curl = curl_init('https://api.medium.com/v1/me');

		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$user_info = curl_exec( $curl );
		$user_info = json_decode($user_info,TRUE);

		return $user_info;

	}


	public function create_post($medium_user_id,$integration_token,$title,$content,$tags){

		// $access_token = $this->get_access_token_from_refresh_token($refresh_token);
		$headers = array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Accept-Charset' => 'utf-8',
			"Authorization: Bearer {$integration_token}"
		);


		$url="https://api.medium.com/v1/users/{$medium_user_id}/posts";

		$title=urlencode($title);
		$content=urlencode($content);
		// $content="<img src='http://i.stack.imgur.com/R7QBbm.jpg' width='100' height='100'>";

		$tags=urlencode($tags);

		$post_data="title={$title}&content={$content}&contentFormat=html";


		$curl = curl_init($url);
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$post_info = curl_exec( $curl );

		$post_info = json_decode($post_info, true);


		return $post_info;

	}









}

