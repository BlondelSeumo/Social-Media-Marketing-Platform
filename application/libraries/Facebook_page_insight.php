<?php
include("Facebook/autoload.php");

class Facebook_page_insight{
				
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
			$facebook_config=$this->CI->basic->get_data("facebook_config",array("where"=>array("user_id"=>$this->user_id,"status"=>"1")));
			if(isset($facebook_config[0]))
			{			
				$this->app_id=$facebook_config[0]["api_id"];
				$this->app_secret=$facebook_config[0]["api_secret"];
				$this->user_access_token=$facebook_config[0]["user_access_token"];
			}
		}
		
		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}
		
		$this->fb = new Facebook\Facebook([
		  'app_id' => $this->app_id, // Replace {app-id} with your app id
		  'app_secret' => $this->app_secret,
		  'default_graph_version' => 'v2.2',
		]);
		
	}
		
	public function login_button($redirect_url){
	
		$helper = $this->fb->getRedirectLoginHelper();
		$permissions = ['email','manage_pages','read_insights'];
		$loginUrl = $helper->getLoginUrl($redirect_url, $permissions);
		return '<a class="btn btn-info btn-lg" style="color:white;" href="' . htmlspecialchars($loginUrl) . '"><i class="fa fa-facebook-square"></i> Log in with Facebook!</a>';	
		
	}
	
	public function login_callback(){
			$helper = $this->fb->getRedirectLoginHelper();
				try {
				  $accessToken = $helper->getAccessToken();
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
				  
				  $response['status']="error";
				  $response['message']= $e->getMessage();
				  
				  return $response;
				  
				} catch(Facebook\Exceptions\FacebookSDKException $e) {
					$response['status']="error";
				    $response['message']= $e->getMessage();
					return $response;
				}
			 
			 $access_token	= (string) $accessToken;
			 $this->CI->session->set_userdata('fb_page_insight_access_token',$access_token);
	}
	
	
	
	public function get_page_list(){
	
		$access_token	= $this->CI->session->userdata("fb_page_insight_access_token");
		$request = $this->fb->get('/me/accounts?fields=cover,emails,picture,id,name,url,username,access_token', $access_token);	
		$response = $request->getGraphList()->asArray();
		return $response;
	}
	
	public function get_page_insight_info($access_token,$metrics,$page_id){
		
		$from = date('Y-m-d', strtotime(date('Y-m-d').' -28 day'));
        $to   = date('Y-m-d', strtotime(date("Y-m-d").'-1 day'));
		$request = $this->fb->get("/{$page_id}/{$metrics}?&since=".$from."&until=".$to,$access_token);
		$response = $request->getGraphList()->asArray();
		return $response;
		 
	}
		
		

}



?>