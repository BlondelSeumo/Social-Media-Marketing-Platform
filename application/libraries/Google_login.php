<?php 
require_once('Google/Google_Client.php');
require_once('Google/contrib/Google_Oauth2Service.php');

class Google_login{

	public $google_client_id="";
	public $google_client_secret="";
	public $redirect_url= "";
	
	function __construct(){		
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->model('basic');	
		$this->CI->load->helper('url_helper');	
		$login_config=$this->CI->basic->get_data("login_config",array("where"=>array("status"=>"1")));
		$this->redirect_url=site_url("home/google_login_back");
		if(isset($login_config[0]))
		{			
			$this->google_client_id=$login_config[0]["google_client_id"];
			$this->google_client_secret=$login_config[0]["google_client_secret"];
		}
	}
	
	
	public function set_login_button(){
		

		if($this->redirect_url=="" || $this->google_client_id=="" || $this->google_client_secret=="") return "";

		$login_url="https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri={$this->redirect_url}&client_id={$this->google_client_id}&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email
&access_type=online&approval_prompt=auto";

		return '<a class="btn btn-block btn-social btn-youtube" href="'.$login_url.'"> <img src="'.base_url("assets/img/google.png").'"> ThisIsTheLoginButtonForGoogle</a>';
	}
	
	
	
	public function user_details(){
	
		$userProfile=array();

		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($this->google_client_id);
		$gClient->setClientSecret($this->google_client_secret);
		$gClient->setRedirectUri($this->redirect_url);
		
		$google_oauthV2 = new Google_Oauth2Service($gClient);
		
		
		if(isset($_GET['code'])){
			$gClient->authenticate();
			$access_token=$gClient->getAccessToken();
			if(isset($access_token)){
				$gClient->setAccessToken($access_token);
				$userProfile = $google_oauthV2->userinfo->get();
			}		
		}
			
		return $userProfile;
		
		
	}
	
	
	
}
	
?>