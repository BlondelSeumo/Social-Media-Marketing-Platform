<?php
include("Facebook/autoload.php");

class Fb_autopost{
				
		public $user_id=""; 
		public $app_id="";
		public $app_secret="";		
		public $user_access_token="";
		
		public $fb;

	function __construct(){
	
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");

		if($this->user_id!="")
		{
			$facebook_config=$this->CI->basic->get_data("facebook_config",array("where"=>array("status"=>"1","user_id"=>$this->user_id)));
			if(isset($facebook_config[0]))
			{			
				$this->app_id=$facebook_config[0]["api_id"];
				$this->app_secret=$facebook_config[0]["api_secret"];
				$this->user_access_token=$facebook_config[0]["user_access_token"];
			}
			
			if (session_status() == PHP_SESSION_NONE) {
			    session_start();
			}

			if($this->app_id!="" && $this->app_secret!="") 
			{
				$this->fb = new Facebook\Facebook([
				  'app_id' => $this->app_id, // Replace {app-id} with your app id
				  'app_secret' => $this->app_secret,
				  'default_graph_version' => 'v2.2',
				]);
			}
			
		}
		
		
	}
		
	public function login_button(){

		if($this->app_id=="" || $this->app_secret=="")
		{			
			$base_url=base_url("config/facebook_config");
			return '<a class="btn btn-default" href="'.$base_url.'"><i class="fa fa-cog red"></i> Config facebook app</a>';
		}

		$redirect_url = base_url()."rx_video_autopost/fb_login_callback";
		$helper = $this->fb->getRedirectLoginHelper();
		$permissions = ['email','manage_pages','read_insights','publish_actions','publish_pages'];
		$loginUrl = $helper->getLoginUrl($redirect_url, $permissions);
		return '<a class="btn btn-default" href="' . htmlspecialchars($loginUrl) . '"><i class="fa fa-plus blue"></i> Add New Account</a>';	
		
	}
	
	public function login_callback(){
			$helper = $this->fb->getRedirectLoginHelper();
				try {
				  	$accessToken = $helper->getAccessToken();
				  	$response = $this->fb->get('/me?fields=id,name,email', $accessToken);
					$user = $response->getGraphUser()->asArray();
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
				  
				  	$user['status']="0";
				    $user['message']= $e->getMessage();
					return $user;
				  
				} catch(Facebook\Exceptions\FacebookSDKException $e) {
					$user['status']="0";
				    $user['message']= $e->getMessage();
					return $user;
				}
			 
			 $access_token	= (string) $accessToken;
			 $access_token = $this->create_long_lived_access_token($access_token);

			 $this->CI->session->set_userdata('fb_autopost_access_token',$access_token);

			 return $user;
	}



	public function create_long_lived_access_token($short_lived_user_token){
	
		$app_id=$this->app_id;
		$app_secret=$this->app_secret;
		$short_token=$short_lived_user_token;
		
		$url="https://graph.facebook.com/v2.6/oauth/access_token?grant_type=fb_exchange_token&client_id={$app_id}&client_secret={$app_secret}&fb_exchange_token={$short_token}";
		
		$headers = array("Content-type: application/json");
		 	 
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	     curl_setopt($ch, CURLOPT_URL, $url);
		 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	     curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
	     curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		   
		 $st=curl_exec($ch); 
		 $result=json_decode($st,TRUE);

		 $access_token=isset($result["access_token"]) ? $result["access_token"] : "";
		 
		 return $access_token;
		 
	}
	
	
	
	public function get_page_list(){
	
		$access_token	= $this->CI->session->userdata("fb_autopost_access_token");
		$request = $this->fb->get('/me/accounts?fields=cover,emails,picture,id,name,url,username,access_token', $access_token);	
		$response = $request->getGraphList()->asArray();
		return $response;
	}



	public function post_to_facebook($app_id,$app_secret,$message,$link,$id,$access_token)
	{
		$fb = new Facebook\Facebook([
		  'app_id' => $app_id, // Replace {app-id} with your app id
		  'app_secret' => $app_secret,
		  'default_graph_version' => 'v2.2',
		]);
		$params = array("message" => $message,"link"=>$link);
		$response = $fb->post("{$id}/feed",$params,$access_token);
		$response = $response->getGraphObject()->asArray();

		return $response;
	}
	
		
		

}



?>