<?php

class Pinterests {

		public $user_id="";
		public $app_id="";
		public $app_secret="";
		public $pinterest="";
		public $pinterest_config_table_id="";


	function __construct(){

		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");


		$pinterest_config = $this->CI->basic->get_data("pinterest_config",array('where'=>array("user_id"=> $this->user_id, 'deleted'=>'0', "status"=>"1")));

		if(isset($pinterest_config[0])) {

			$this->app_id=$pinterest_config[0]["client_id"];
			$this->app_secret=$pinterest_config[0]["client_secret"];
			$this->pinterest_config_table_id=$pinterest_config[0]["id"];
		}


		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}


		
	}

	
	public function app_initialize($pinterest_config_table_id)
	{	  
	    $pinterest_config = $this->CI->basic->get_data("pinterest_config",array('where'=>array('id'=>$pinterest_config_table_id)));
		if(isset($pinterest_config[0]))
		{			
			$this->app_id=$pinterest_config[0]["client_id"];
			$this->app_secret=$pinterest_config[0]["client_secret"];
		}	
		if (session_status() == PHP_SESSION_NONE) 
		{
		    session_start();
		}
	}



	public function login_button($redirect_uris)
	{	
		$state=rand();
		$loginurl = "https://api.pinterest.com/oauth/?response_type=code&redirect_uri={$redirect_uris}&client_id={$this->app_id}&scope=read_public,write_public&state={$state}";

		if ($this->app_id == '' || $this->app_secret == '') {
			$loginurl = base_url('social_apps/pinterest_settings'); 
		}

		return $loginurl;
	}

	public function get_userinfo($code)
	{
		if (!isset($this->app_id) || !isset($this->app_secret)) {

		    $this->CI->session->set_userdata('account_import_error', $this->CI->lang->line("App Id or App Secret has not set yet. Please set it on:")." <a href='".base_url("social_apps/pinterest_settings")."'>".$this->CI->lang->line("Here")."</a>");
			redirect(base_url('comboposter/social_accounts'),'refresh');
		}

		$user_info=array();

		/**Get Access Token **/

		$url="https://api.pinterest.com/v1/oauth/token";
		$postdata="grant_type=authorization_code&client_id={$this->app_id}&client_secret={$this->app_secret}&code={$code}";

		$access_token_info=$this->curl_post_request($url,$postdata);

		if(isset($access_token_info['body_response']['access_token']))
			$user_info['access_token']=$access_token_info['body_response']['access_token'];
		else{
			$user_info['error']=1;
			$user_info['error_message']="Error HTTP Code: ".$access_token_info['http_curl_info']['http_code']." : ".json_encode($access_token_info['body_response']);
			return $user_info;
		}

		/** Find User Information **/

		$url="https://api.pinterest.com/v1/me/?access_token={$user_info['access_token']}&fields=username,first_name,last_name,counts,image[small,large]";
		$user_details_info=$this->curl_get_request($url);

		if(isset($user_details_info['body_response']['data']['id'])){

			$user_info['id'] = $user_details_info['body_response']['data']['id'];
			$user_info['username'] = $user_details_info['body_response']['data']['username'];
			$user_info['first_name'] = $user_details_info['body_response']['data']['first_name'];
			$user_info['last_name'] = $user_details_info['body_response']['data']['last_name'];
			$user_info['boards_count'] = $user_details_info['body_response']['data']['counts']['boards'];
			$user_info['pins_count'] = $user_details_info['body_response']['data']['counts']['pins'];
			$user_info['image'] = $user_details_info['body_response']['data']['image']['large']['url'];
		}
		else{

			$user_info['error']=1;
			$user_info['error_message']="Error HTTP Code: ".$user_details_info['http_curl_info']['http_code']." : ".json_encode($user_details_info['body_response']);
			return $user_info;
		}


		/**Get Board List **/

		$url="https://api.pinterest.com/v1/me/boards/?access_token={$user_info['access_token']}";
		$user_boards_info=$this->curl_get_request($url);


		if(isset($user_boards_info['body_response']['data'])){
			$user_info['board_list'] = $user_boards_info['body_response']['data'];
		}

		else{

			$user_info['error']=1;
			$user_info['error_message']="Error HTTP Code: ".$user_boards_info['http_curl_info']['http_code']." : ".json_encode($user_boards_info['body_response']);
			return $user_info;
		}

		return $user_info;
	}

	

	public function youtube_video_post_to_pinterest($username,$bordname,$video_url,$access_token,$description)
	{
    	
	}

	public function image_post_to_pinterest($username,$board_id,$image_url,$access_token,$description,$link="")
	{

		$url="https://api.pinterest.com/v1/pins/";
		$postdata=array(
			"board"   =>$board_id,
			"note" =>$description,
			"image_url" => $image_url,
			'access_token' => $access_token
		);

		if($link!="")
			$postdata['link']=$link;

		$post_info=$this->curl_post_request($url,$postdata);


		
		if(isset($post_info['body_response']['data']['url'])){
			$response['url']= $post_info['body_response']['data']['url'];
			return $response;
		}
		else if($post_info['http_curl_info']['http_code']=='201'){
			$response['url']= "Success";
			return $response;
		}
		else{
			$response['error']=1;
			$response['error_message']="Error HTTP Code: ".$post_info['http_curl_info']['http_code']." : ".json_encode($post_info['body_response']);
			return $response;
		}
	}


	public function getVideoId($url)
	{
			$url = $url;

			if (parse_url ($url,  PHP_URL_HOST) == "www.youtube.com") {
					$url = explode("=", $url);
					if (strpos($url[1], '&list') !== false) {
							return $video_id = str_replace("&list", '', $url[1]); //str_replace('search', 'replace', 'string' )
					}
					return $video_id = $url[1];
			}

			if (parse_url ($url,  PHP_URL_HOST) == "youtu.be") {
					$url = explode("/", $url);
					return $video_id = end($url); //end($array) return last array element
			}

			return "This is not valid youtube url.";
	}



	public function curl_get_request($url){

		$ch=curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		$response = curl_exec( $ch );
		$response = json_decode($response,true);

		$response_final['body_response'] = $response;
		$response_final['http_curl_info']=curl_getinfo($ch);

		return $response_final;
	}


	function curl_post_request($url,$postdata){

			$ch=curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
			$response = curl_exec( $ch );
			$response = json_decode($response,true);

			$response_final['body_response'] = $response;
			$response_final['http_curl_info']=curl_getinfo($ch);

			return $response_final;

	}

}