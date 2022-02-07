<?php  
include("Facebook/autoload.php");

class Fb_rx_login
{				
	public $database_id=""; 
	public $app_id="";
	public $app_secret="";		
	public $user_access_token="";
	public $fb;


	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');

		$this->CI->load->model('basic');
		$this->database_id=$this->CI->session->userdata("fb_rx_login_database_id");

		if($this->CI->session->userdata("social_login_session_set") == 1)
		{
			$facebook_config=$this->CI->basic->get_data("facebook_rx_config",array("where"=>array("status"=>"1"),$select='',$join='',$limit=1,$start=NULL,$order_by=rand()));

			if(empty($facebook_config)) $this->database_id='';
			else
			{
				$config_id = isset($facebook_config[0]) ? $facebook_config[0]['id'] : 0;
				$this->database_id = $config_id;
				$this->CI->session->unset_userdata('social_login_session_set');
				$this->CI->session->set_userdata('return_configid_used_for_social_login',$config_id);
			}
		}

		if($this->CI->uri->segment(1)!='social_apps')
		{
		    if($this->CI->session->userdata("user_type")=="Admin" && ($this->database_id=="" || $this->database_id==0)) 
    		{
    			echo "<h3 align='center' style='font-family:arial;line-height:35px;margin:20px;padding:20px;border:1px solid #ccc;'>Hello Admin : No Facebook app configuration found. You have to  <a href='".base_url("social_apps/facebook_settings")."'> add facebook app & login with facebook</a>. If you just added your first app and redirected here again then <a href='".base_url("home/logout")."'> logout</a>, login again and <a href='".base_url("social_apps/facebook_settings")."'> go to this link</a> to login with facebook for your just added app.</h3>";
    			exit();
    		}
    
    		if($this->CI->session->userdata("user_type")=="Member" && ($this->database_id=="" || $this->database_id==0) && $this->CI->config->item("backup_mode")==1) 
    		{
    			echo "<h3 align='center' style='font-family:arial;line-height:35px;margin:20px;padding:20px;border:1px solid #ccc;'>Hello user : No Facebook app configuration found. You have to  <a href='".base_url("social_apps/facebook_settings")."'> add facebook app & login with facebook</a>. If you just added your first app and redirected here again then <a href='".base_url("home/logout")."'> logout</a>, login again and <a href='".base_url("social_apps/facebook_settings")."'> go to this link</a> to login with facebook for your just added app.</h3>";
    			exit();
    		}

    		if($this->CI->session->userdata("user_type")=="Member" && ($this->database_id=="" || $this->database_id==0) && $this->CI->config->item("backup_mode")==0) 
    		{
    			echo "<h3 align='center' style='font-family:arial;line-height:35px;margin:20px;padding:20px;border:1px solid #ccc;'>Hello User : No Facebook app configuration found. Please contact admin to setup app for the system.</h3>";
    			exit();
    		}
		}

		if($this->database_id != '')
		{
			$facebook_config=$this->CI->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->database_id)));
			if(isset($facebook_config[0]))
			{
				if(isset($facebook_config[0]['developer_access']) && $facebook_config[0]['developer_access'] == '1')
				{
					$encrypt_method = "AES-256-CBC";
					$secret_key = 't8Mk8fsJMnFw69FGG5';
					$secret_iv = '9fljzKxZmMmoT358yZ';
					$key = hash('sha256', $secret_key);
					$iv = substr(hash('sha256', $secret_iv), 0, 16);
					$this->app_id = openssl_decrypt(base64_decode($facebook_config[0]["api_id"]), $encrypt_method, $key, 0, $iv);
					$this->app_secret = openssl_decrypt(base64_decode($facebook_config[0]["api_secret"]), $encrypt_method, $key, 0, $iv);
					$this->user_access_token=$facebook_config[0]["user_access_token"];
				}	
				else
				{					
					$this->app_id=$facebook_config[0]["api_id"];
					$this->app_secret=$facebook_config[0]["api_secret"];
					$this->user_access_token=$facebook_config[0]["user_access_token"];
				}		
				if (session_status() == PHP_SESSION_NONE) 
				{
				    session_start();
				}
		
				$this->fb = new Facebook\Facebook([
					'app_id' => $this->app_id, 
					'app_secret' => $this->app_secret,
					'default_graph_version' => 'v10.0',
					'fileUpload'	=>TRUE
					]);
			}
		}


	}
	
	
	
	public function app_initialize($fb_rx_login_database_id){
	    
	    $this->database_id=$fb_rx_login_database_id;
	    $facebook_config=$this->CI->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->database_id)));
		if(isset($facebook_config[0]))
		{			
			if(isset($facebook_config[0]['developer_access']) && $facebook_config[0]['developer_access'] == '1')
			{
				$encrypt_method = "AES-256-CBC";
				$secret_key = 't8Mk8fsJMnFw69FGG5';
				$secret_iv = '9fljzKxZmMmoT358yZ';
				$key = hash('sha256', $secret_key);
				$iv = substr(hash('sha256', $secret_iv), 0, 16);
				$this->app_id = openssl_decrypt(base64_decode($facebook_config[0]["api_id"]), $encrypt_method, $key, 0, $iv);
				$this->app_secret = openssl_decrypt(base64_decode($facebook_config[0]["api_secret"]), $encrypt_method, $key, 0, $iv);
				$this->user_access_token=$facebook_config[0]["user_access_token"];
			}	
			else
			{					
				$this->app_id=$facebook_config[0]["api_id"];
				$this->app_secret=$facebook_config[0]["api_secret"];
				$this->user_access_token=$facebook_config[0]["user_access_token"];
			}
			if (session_status() == PHP_SESSION_NONE) 
			{
			    session_start();
			}
	
			$this->fb = new Facebook\Facebook([
				'app_id' => $this->app_id, 
				'app_secret' => $this->app_secret,
				'default_graph_version' => 'v10.0',
				'fileUpload'	=>TRUE
				]);
		}
		
	    
	}


	function login_for_user_access_token($redirect_url="")
	{	
		$redirect_url=rtrim($redirect_url,'/');

		$helper = $this->fb->getRedirectLoginHelper();

		if($this->CI->config->item('facebook_poster_group_enable_disable') == '1' && $this->CI->is_group_posting_exist)
			$permissions = ['email','pages_manage_posts','pages_manage_engagement','pages_manage_metadata','pages_read_engagement','pages_show_list','pages_messaging','public_profile','publish_to_groups','read_insights','instagram_manage_messages'];
		else
			$permissions = ['email','pages_manage_posts','pages_manage_engagement','pages_manage_metadata','pages_read_engagement','pages_show_list','pages_messaging','public_profile','read_insights','instagram_manage_messages'];

		// if($this->CI->basic->is_exist("add_ons",array("project_id"=>41)))
			// array_push($permissions, 'publish_video');

		if($this->CI->config->item('instagram_reply_enable_disable') == '1')
			array_push($permissions, 'instagram_basic','instagram_manage_comments','instagram_manage_insights','instagram_content_publish');


		$loginUrl = $helper->getLoginUrl($redirect_url, $permissions);
		
		return '<a class="btn btn-block btn-social btn-facebook" href="'.htmlspecialchars($loginUrl).'"><span class="fab fa-facebook"></span> ThisIsTheLoginButtonForFacebook</a>';	
	}


	public function login_callback_without_email($redirect_url="")
	{
		$redirect_url=rtrim($redirect_url,'/');
		$helper = $this->fb->getRedirectLoginHelper();
		try {
			$accessToken = $helper->getAccessToken($redirect_url);
			$response = $this->fb->get('/me?fields=id,name', $accessToken);

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

		$user["access_token_set"]=$access_token;

		return $user;
	}


	public function login_callback($redirect_url="")
	{
		$redirect_url=rtrim($redirect_url,'/');
		$helper = $this->fb->getRedirectLoginHelper();
		try {
			$accessToken = $helper->getAccessToken($redirect_url);
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

		$user["access_token_set"]=$access_token;

		return $user;
	}



	public function app_id_secret_check()
	{
		if($this->app_id == '' || $this->app_secret == '') return 'not_configured';
	}

	function access_token_validity_check(){

		$access_token=$this->user_access_token;
		$client_id=$this->app_id;
		$result=array();
		$url="https://graph.facebook.com/v10.0/oauth/access_token_info?client_id={$client_id}&access_token={$access_token}";

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
		if(!isset($result["error"]) && isset($result["access_token"]) && $result["access_token"]!='') return 1;
		else return 0;

	}



	function access_token_validity_check_for_user($access_token){

		$client_id=$this->app_id;
		$result=array();
		$url="https://graph.facebook.com/v10.0/oauth/access_token_info?client_id={$client_id}&access_token={$access_token}";

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

		if(!isset($result["error"])) return 1;
		else return 0;

	}



	public function create_long_lived_access_token($short_lived_user_token){

		$app_id=$this->app_id;
		$app_secret=$this->app_secret;
		$short_token=$short_lived_user_token;

		$url="https://graph.facebook.com/v10.0/oauth/access_token?grant_type=fb_exchange_token&client_id={$app_id}&client_secret={$app_secret}&fb_exchange_token={$short_token}";

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



	public function facebook_api_call($url){

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

		return  $results=json_decode($st,TRUE);	 
	}

	public function get_page_list($access_token="")
	{

		$error=false;
		try {

			$request = $this->fb->get('/me/accounts?fields=cover,emails,picture,id,name,url,username,access_token&limit=400', $access_token);	
			$response = $request->getGraphList()->asArray();
			return $response;
		} catch(Facebook\Exceptions\FacebookResponseException $e) {

			$error=true;

		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$error=true;
		}


		if($error)
		{
			
			try {

				$request = $this->fb->get('/me/accounts?fields=cover,emails,picture,id,name,url,username,access_token&limit=400', $access_token);	
				$response = $request->getGraphList()->asArray();
				return $response;
			}

			catch(Facebook\Exceptions\FacebookResponseException $e) {
				$response['error']='1';
				$response['message']= $e->getMessage();
				return $response; 
			}

			catch(Facebook\Exceptions\FacebookSDKException $e) {
				$response['error']='1';
				$response['message']= $e->getMessage();
				return $response; 
			}


		}

		
	}


	public function get_page_insight_info($access_token,$metrics,$page_id){
		
		$from = date('Y-m-d', strtotime(date('Y-m-d').' -28 day'));
        $to   = date('Y-m-d', strtotime(date("Y-m-d").'-1 day'));
		$request = $this->fb->get("/{$page_id}/{$metrics}?&since=".$from."&until=".$to,$access_token);
		$response = $request->getGraphList()->asArray();
		return $response;
		 
	}


	public function get_group_list($access_token="")
	{		

		$error=false;
		try {

			$request = $this->fb->get('/me/groups?fields=cover,picture,id,name&limit=400&admin_only=1', $access_token);	
			$response_group = $request->getGraphList()->asArray();		
			return $response_group;
		} catch(Facebook\Exceptions\FacebookResponseException $e) {

			$error=true;

		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$error=true;
		}

		if($error)
		{
			$request = $this->fb->get('/me/groups?fields=cover,emails,picture,id,name,url,username,access_token,accounts,perms,category&limit=400', $access_token);	
			$response_group = $request->getGraphList()->asArray();		
			return $response_group;
		}

	}


	public function send_user_roll_access($app_id,$user_id, $user_access_token)
	{
		$url="https://graph.facebook.com/{$app_id}/roles?user={$user_id}&role=testers&access_token={$user_access_token}&method=post";
		$resuls = $this->run_curl_for_fb($url);
		return json_decode($resuls,TRUE);
	}


	public function run_curl_for_fb($url)
	{
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
		$results=curl_exec($ch); 	   
		return  $results;   
	}


	public function get_videolist_from_fb_page($page_id,$access_token)
	{
		$url = "https://graph.facebook.com/$page_id/videos?access_token=$access_token&fields=is_crossposting_eligible,description,created_time,permalink_url,picture";
		$video_list = $this->run_curl_for_fb($url);
		return json_decode($video_list,TRUE);
	}

	public function get_crosspost_whitelisted_pages($page_id,$access_token)
	{
		$url = "https://graph.facebook.com/$page_id/crosspost_whitelisted_pages?access_token=$access_token&limit=200";
		$whitelisted_pages = $this->run_curl_for_fb($url);
		return json_decode($whitelisted_pages,TRUE);
	}


	public function get_postlist_from_fb_page($page_id,$access_token)
	{
		$request = $this->fb->get("$page_id/posts?fields=id,message,permalink_url,picture,created_time&limit=50", $access_token);	
		$response = $request->getGraphList()->asArray();

		$response= json_encode($response);
		$response=json_decode($response,true);

		$final_data['data']=$response;
		return $final_data;
	}
	

	function get_meta_tag_fb($url)
	{  
		$html=$this->run_curl_for_fb($url);	  
		$doc = new DOMDocument();
		@$doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$html);
		$nodes = $doc->getElementsByTagName('title');	  
		if(isset($nodes->item(0)->nodeValue))
			$title = $nodes->item(0)->nodeValue;
		else  $title="";

		$response=array('title'=>'','image'=>'','description'=>'','author'=>'');


		$response['title']=$title;
		$org_desciption="";

		$metas = $doc->getElementsByTagName('meta');

		for ($i = 0; $i < $metas->length; $i++)
		{
			$meta = $metas->item($i);	   
			if($meta->getAttribute('property')=='og:title')
				$response['title'] = $meta->getAttribute('content');		    
			if($meta->getAttribute('property')=='og:image')
				$response['image'] = $meta->getAttribute('content');		    
			if($meta->getAttribute('property')=='og:description')
				$response['description'] = $meta->getAttribute('content');		   
			if($meta->getAttribute('name')=='author')
				$response['author'] = $meta->getAttribute('content');		    
			if($meta->getAttribute('name')=='description')
				$org_desciption =  $meta->getAttribute('content');   
		}

		if(!isset($response['description']))
			$org_desciption =  $org_desciption;

		return $response;   

	}


	public function view_loader()
	{
		$pos=strpos(base_url(), 'localhost');
        if($pos!==FALSE) return true;

		//bugs
	}


	public function get_general_content_with_checking_library($url,$proxy=""){
            
            $ch = curl_init(); // initialize curl handle
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method

         
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $content = curl_exec($ch); // run the whole process 
            $response['content'] = $content;

            $res = curl_getinfo($ch);
            if($res['http_code'] != 200)
                $response['error'] = 'error';
            curl_close($ch);
            return json_encode($response);
            
    }




	/*	$page_id = page id / profile id / Group id 
	$scheduled_publish_time = TimeStamp Format using strtotime() function and set the date_default_timezone_set(),
	$post_access_token = user access token for profile and group/ page access token for page post. 
	$image_link can't be use without $link	
	*/

	function feed_post($message="",$link="",$image_link="",$scheduled_publish_time="",$link_overwrite_title="",$link_overwrite_description="",$post_access_token="",$page_id="",$og_action_type_id="",$og_object_id="")
	{
		
		$message=spintax_process($message);

		if($message!="")
			$params['message'] = $message;


		if($link!=""){

			$params['link'] = $link;

			if($image_link!="")
				$params['thumbnail'] = $this->fb->fileToUpload($image_link);

			if($link_overwrite_description!="")
				$params['description']= $link_overwrite_description;

			if($link_overwrite_title!="")
				$params['name']= $link_overwrite_title;
		}
		if($scheduled_publish_time!=""){
			$params['scheduled_publish_time'] = $scheduled_publish_time;
			$params['published'] = false;
		}


		if($og_action_type_id!="" && $og_object_id!=""){
			$params['og_action_type_id'] = $og_action_type_id;
			$params['og_object_id'] = $og_object_id;
		}

		$response = $this->fb->post("{$page_id}/feed",$params,$post_access_token);

		return $response->getGraphObject()->asArray();					
	}





	public function cta_post($message="", $link="",$description="",$name="",$cta_type="",$cta_value="",$thumbnail="",$scheduled_publish_time="",$post_access_token,$page_id,$og_action_type_id="",$og_object_id="")
	{
		$message=spintax_process($message);

		if($message!="")
			$params['message'] = $message;

		if($link!="")
			$params['link'] = $link;

		if($description!="")
			$params['description'] = $description;

		if($thumbnail!="")
			$params['thumbnail'] =$this->fb->fileToUpload($thumbnail) ;

		if($name!="")
			$params['name']= $name;

		$call_to_action_array=array(
			"type"=>$cta_type,
			"value"=>$cta_value
			);

		$params['call_to_action'] = $call_to_action_array;

		if($scheduled_publish_time!=""){
			$params['scheduled_publish_time'] = $scheduled_publish_time;
			$params['published'] = false;
		}

		if($og_action_type_id!="" && $og_object_id!=""){
			$params['og_action_type_id'] = $og_action_type_id;
			$params['og_object_id'] = $og_object_id;
		}

		$response = $this->fb->post("{$page_id}/feed",$params,$post_access_token);	

		return $response->getGraphObject()->asArray();

	}




	public function get_post_permalink($post_id,$post_access_token)
	{
		$params['fields']="permalink_url";
		
		try 
		{
			$response = $this->fb->get("{$post_id}?fields=permalink_url",$post_access_token);
			$response_data=$response->getGraphObject()->asArray();
			if(isset($response_data["permalink_url"]))
			{
				if(strpos($response_data["permalink_url"], 'facebook.com') !== false)
					return $response_data; 
				else
				{
					$response_data["permalink_url"] = "https://www.facebook.com".$response_data["permalink_url"];
					return $response_data; 
				}
			}
			return $response_data; 
		} 
		catch(Exceptions $e) 
		{
			$response_data['error'] = $e->getMessage();
			return $response_data;
		} 

	}
	/*********  

	Auto like $object_id is the post's id, Only for live video id is not worked, we need to get permalink and get the id from it and pass it. 

	**********/

	public function get_live_video_id($video_permalink)
	{
		// $video_permalink = "https://www.facebook.com/alaminJwel/videos/1376495642371308/";
		
		if($video_permalink=="") return "";

		$video_permalink=trim($video_permalink,"/");
		$video_permalink = str_replace("http://", "", $video_permalink);
		$video_permalink = str_replace("https://", "", $video_permalink);
		$url_explode =explode('/',$video_permalink);
		$count_url_seg= count($url_explode);
		$id_seg = $count_url_seg - 1 ;
		$video_id = isset($url_explode[$id_seg]) ? trim($url_explode[$id_seg]) : "";
		return $video_id;
	}

	public function auto_like($object_id,$post_access_token)
	{
		$response = $this->fb->post("{$object_id}/likes",array(),$post_access_token);
		return $response->getGraphObject()->asArray();	
	}



	// image = url , video = file path, gif = url
	public function auto_comment($message,$object_id,$post_access_token,$image='',$video="",$gif='')
	{
		if($image != '')
			$params['attachment_url']=$image;

		if($video != '')
			$params['source']=$this->fb->fileToUpload($video);

		if($gif != '')
			$message = $message." ".$gif;		  
  
		$params['message']=$message;
		$response = $this->fb->post("{$object_id}/comments",$params,$post_access_token);

		return $response->getGraphObject()->asArray();	


	}


	public function delete_comment($comment_id,$post_access_token){

		$url="https://graph.facebook.com/v4.0/{$comment_id}?method=delete&access_token={$post_access_token}";
		$results= $this->run_curl_for_fb($url);
		return json_decode($results,TRUE);
	}


	public function hide_comment($comment_id,$post_access_token){
		$url="https://graph.facebook.com/v4.0/{$comment_id}?method=post&access_token={$post_access_token}&is_hidden=true";
		$results= $this->run_curl_for_fb($url);
		return json_decode($results,TRUE);
	}



	public function get_all_conversation_page($post_access_token,$page_id,$auto_sync_limit=0,$scan_limit='',$folder='',$platform='')
	{

		$message_info=array();
		$i=0;

		$real_limit=$scan_limit;
		if($scan_limit!='') //per page scan grabs 499 lead in real
		{
			$how_many_page=$scan_limit/500;
			$real_limit=$scan_limit-$how_many_page;
		}

		//	$url = "https://graph.facebook.com/{$page_id}/conversations?access_token={$post_access_token}&limit=200&fields=participants,message_count,unread_count,senders,is_subscribed,snippet,id";	

		if($platform=='ig')
		$url = "https://graph.facebook.com/{$page_id}/conversations?access_token={$post_access_token}&limit=500&fields=participants,id,updated_time&platform=instagram";	
		else $url = "https://graph.facebook.com/{$page_id}/conversations?access_token={$post_access_token}&folder={$folder}&limit=500&fields=participants,message_count,unread_count,is_subscribed,snippet,id,updated_time,link";	

		do
		{
			$results = $this->run_curl_for_fb($url);
			$results=json_decode($results,true);

			if(isset($results['error'])){
				$message_info['error']=1;
				$message_info['error_msg']= isset($results['error']['message']) ? $results['error']['message'] : json_encode($results);
				return $message_info; 
			}


			if(isset($results['data']))
			{
				foreach($results['data'] as $thread_info)
				{
					foreach($thread_info['participants']['data'] as $participant_info){
						$user_id= $participant_info['id'];
						if($user_id!=$page_id){
							if($platform=="ig")
								$message_info[$i]['name']=$participant_info['username'];
							else $message_info[$i]['name']=$participant_info['name'];
							$message_info[$i]['id']=$participant_info['id'];
						}
					}
					$message_info[$i]['is_subscribed'] = isset($thread_info['is_subscribed'])?$thread_info['is_subscribed']:0;
					$message_info[$i]['thead_id'] = $thread_info['id'];
					$message_info[$i]['message_count'] = isset($thread_info['message_count']) ? $thread_info['message_count']:0;
					$message_info[$i]['unread_count'] = isset($thread_info['unread_count']) ? $thread_info['unread_count']:0;
					$message_info[$i]['snippet'] = isset($thread_info['snippet']) ? $thread_info['snippet']:"";
					$message_info[$i]['updated_time'] = isset($thread_info['updated_time']) ? $thread_info['updated_time']:"";
					$message_info[$i]['link'] = isset($thread_info['link']) ? $thread_info['link']:"";

					$i++;
				}
			}

			$url= isset($results['paging']['next']) ? $results['paging']['next']: "" ;
			if($scan_limit!='' && $real_limit<=$i) break;
			if($auto_sync_limit!=0) break;

		}
		while($url!='');
		return $message_info;
	}
	
	public function get_all_conversation_page_cron($post_access_token,$page_id,$scan_limit='',$url='',$platform='')
	{

		$message_info=array();
		$i=0;

		$real_limit=$scan_limit;
		if($scan_limit!='') //per page scan grabs 499 lead in real
		{
			$how_many_page=$scan_limit/500;
			$real_limit=$scan_limit-$how_many_page;
		}

		//	$url = "https://graph.facebook.com/{$page_id}/conversations?access_token={$post_access_token}&limit=200&fields=participants,message_count,unread_count,senders,is_subscribed,snippet,id";	

		if($url=='')
		{

			if($platform=='ig')
			$url = "https://graph.facebook.com/{$page_id}/conversations?access_token={$post_access_token}&limit=500&fields=participants,id,updated_time&platform=instagram";	

			$url = "https://graph.facebook.com/{$page_id}/conversations?access_token={$post_access_token}&limit=500&fields=participants,message_count,unread_count,is_subscribed,snippet,id,updated_time,link";	
		}

		do
		{
			$results = $this->run_curl_for_fb($url);
			$results=json_decode($results,true);

			if(isset($results['data']))
			{
				foreach($results['data'] as $thread_info)
				{
					foreach($thread_info['participants']['data'] as $participant_info){
						$user_id= $participant_info['id'];
						if($user_id!=$page_id){
							if($platform=="ig")
								$message_info[$i]['name']=$participant_info['username'];
							else $message_info[$i]['name']=$participant_info['name'];
							$message_info[$i]['id']=$participant_info['id'];
						}
					}
					$message_info[$i]['is_subscribed'] = isset($thread_info['is_subscribed'])?$thread_info['is_subscribed']:0;
					$message_info[$i]['thead_id'] = $thread_info['id'];
					$message_info[$i]['message_count'] = isset($thread_info['message_count']) ? $thread_info['message_count']:0;
					$message_info[$i]['unread_count'] = isset($thread_info['unread_count']) ? $thread_info['unread_count']:0;
					$message_info[$i]['snippet'] = isset($thread_info['snippet']) ? $thread_info['snippet']:"";
					$message_info[$i]['updated_time'] = isset($thread_info['updated_time']) ? $thread_info['updated_time']:"";
					$message_info[$i]['link'] = isset($thread_info['link']) ? $thread_info['link']:"";

					$i++;
				}
			}

			$url= isset($results['paging']['next']) ? $results['paging']['next']: "" ;
			if($scan_limit!='' && $real_limit<=$i) break;

		}
		while($url!='');

		$return=array("next_scan_url"=>$url,"message_info"=>$message_info);
		return $return;
	}
	
	
	
	public function get_messages_from_thread($thread_id,$post_access_token){
		$url= "https://graph.facebook.com/{$thread_id}/messages?access_token={$post_access_token}&fields=id,message,created_time,from&limit=200";
		$results = $this->run_curl_for_fb($url);
		$results=json_decode($results,true);
		return $results;
	}

	public function get_messages_from_thread_instagram($thread_id,$post_access_token){
		$url= "https://graph.facebook.com/{$thread_id}/messages?access_token={$post_access_token}&fields=id,message,created_time,from,to,is_unsupported,attachments&limit=200";
		$results = $this->run_curl_for_fb($url);
		$results=json_decode($results,true);
		return $results;
	}
	
	
	

	public function send_message_to_thread($thread_id,$message,$post_access_token)
	{
		// $message=urlencode($message);
		// $url= "https://graph.facebook.com/v2.6/{$thread_id}/messages?access_token={$post_access_token}&message={$message}&method=post";
		// $results= $this->run_curl_for_fb($url);
		// return json_decode($results,TRUE);
		$params['message']=$message;
		try{
			$response = $this->fb->post("{$thread_id}/messages",$params,$post_access_token);
			return $response->getGraphObject()->asArray();
		}

		catch(Exception $e) 
		{
		  
		  $error_info["error"]["message"]  = $e->getMessage();
		  $error_info["error"]["code"]     = $e->getCode();
		  return $error_info;
		}  
       

	}


	public function get_message_attachment_info($messag_id,$post_access_token)
	{
  		$response = $this->fb->get("{$messag_id}/attachments", $post_access_token);
		$data= $response->getGraphEdge()->asArray();
	    return $data;
	}



	public function get_all_comment_of_post($post_ids,$post_access_token)
	{ 
		//$number_of_old_comment_reply=$this->CI->config->item('number_of_old_comment_reply');
		//if($number_of_old_comment_reply == '') $number_of_old_comment_reply = 20;
        $number_of_old_comment_reply = 200;
		$response = $this->fb->get("{$post_ids}/comments?filter=toplevel&order=reverse_chronological&limit={$number_of_old_comment_reply}",$post_access_token);
	  
	    $data =  $response->getGraphEdge()->asArray();
	    $data = json_encode($data);
	    $data = json_decode($data,true);
	    return $data;
	}


	 public function get_post_info_by_id($post_id,$page_access_token)
	 {
	 	$url="https://graph.facebook.com/?ids={$post_id}&access_token={$page_access_token}&fields=id,message,permalink_url,picture,created_time";
	   	$results= $this->run_curl_for_fb($url);
	   	$results= json_decode($results,TRUE);
	   	return $results;

	 }


	 
	public function send_private_reply($message,$comment_id,$post_access_token)
	 {	 
	   $params['message']=$message;
       $response = $this->fb->post("{$comment_id}/private_replies",$params,$post_access_token);
       return $response->getGraphObject()->asArray();	  
	}

	 // Finding out the original PSID of the commenter from message id after giving private reply. In webhook event commenter id isn't PSID, so we have to do extra call to get the PSID from the message id, to parameter return PSID

	 /* return array example : 
				 Array
			(
			    [id] => m_I_H3qypwJlaHvtiHiFcLJOIJnJ-QsF0WUMigN3BM88GCgjjx2QccestJA57DlE020jM88DTHrrJDAPmQQAegww
			    [from] => Array
			        (
			            [name] => Bot Inboxer
			            [email] => 822937534561560@facebook.com
			            [id] => 822937534561560
			        )

			    [message] => Hi Name, Comment Private Reply
			    [to] => Array
			        (
			            [0] => Array
			                (
			                    [name] => Name Name
			                    [email] => 1727413630650055@facebook.com
			                    [id] => 1727413630650055
			                )

			        )

			    [created_time] => DateTime Object
			        (
			            [date] => 2019-07-13 11:02:05.000000
			            [timezone_type] => 1
			            [timezone] => +00:00
			        )

			)*/



	public function get_private_reply_message_id_info($private_reply_message_id,$post_access_token){

	 	$response = $this->fb->get("{$private_reply_message_id}/?fields=id,from,message,to,created_time",$post_access_token);
	  	return $response->getGraphNode()->asArray();
	 }

	 


	public function video_insight($video_id,$post_access_token){
		$request = $this->fb->get("/{$video_id}/video_insights",$post_access_token);
		$response = $request->getGraphList()->asArray();
		return $response;	 
	}



	public function post_insight($post_id,$post_access_token)
	{	
	  //	echo	$url="https://graph.facebook.com/v2.6/{$post_id}/insights?access_token={$post_access_token}"; 
	  // $response= $this->facebook_api_call($url);
	  
		 $request = $this->fb->get("/{$post_id}/insights",$post_access_token,"","v2.6");
		 $response = $request->getGraphList()->asArray();
		
		 return $response;
	}
	
	


	public function debug_access_token($input_token){

		$url="https://graph.facebook.com/debug_token?input_token={$input_token}&access_token={$this->user_access_token}";
		$results= $this->run_curl_for_fb($url);
		return json_decode($results,TRUE);

	}


	public function read_notification($page_id,$post_access_token){

	  $response = $this->fb->get("{$page_id}/notifications?fields=from,title,unread,to,created_time,application,object,link",$post_access_token);
	  return $response->getGraphEdge()->asArray();
	  
	  
	 }


	public function photo_post($message="",$image='',$scheduled_publish_time="",$post_access_token,$page_id){

		$message=spintax_process($message);

	 	if($message!="")
	 		$params['message'] = $message;
	 	if($image!="")
	 		$params['source']= $this->fb->fileToUpload($image);

	 	if($scheduled_publish_time!=""){
	 		$params['scheduled_publish_time'] = $scheduled_publish_time;
	 		$params['published'] = true;
	 	}
	 	
	 	$params['no_story']="false";
	 	$response = $this->fb->post("{$page_id}/photos",$params,$post_access_token);
	 	return $response->getGraphObject()->asArray();
	 }

	public function photo_post_no_story($message="",$image='',$scheduled_publish_time="",$post_access_token,$page_id){
		$message=spintax_process($message);

	 	if($message!="")
	 		$params['message'] = $message;
	 	if($image!="")
	 		$params['source']= $this->fb->fileToUpload($image);

	 	if($scheduled_publish_time!=""){
	 		$params['scheduled_publish_time'] = $scheduled_publish_time;
	 		$params['published'] = false;
	 	}
	 	
	 	$params['no_story']="true";
	 	$response = $this->fb->post("{$page_id}/photos",$params,$post_access_token);
	 	return $response->getGraphObject()->asArray();
	 }
	 
	 
	public function photo_post_for_multipost($message="",$image='',$scheduled_publish_time="",$post_access_token,$page_id){

	 	$message=spintax_process($message);
	 
	 	if($message!="")
	 		$params['message'] = $message;
	 	if($image!="")
	 		$params['source']= $this->fb->fileToUpload($image);
		
	 		$params['published'] = FALSE;
			
	 	$response = $this->fb->post("{$page_id}/photos",$params,$post_access_token);
		
	 	return $response->getGraphObject()->asArray();
	 }
	 
	 
	public function multi_photo_post($message="",$attach_media_array=array(),$scheduled_publish_time="",$post_access_token,$page_id){

	 	$message=spintax_process($message);
	
		if($message!="")
			$params['message'] = $message;
			
		$params['attached_media'] = $attach_media_array;
		
		$response = $this->fb->post("{$page_id}/feed",$params,$post_access_token);	
		
		return $response->getGraphObject()->asArray();	
		
	}

	 


	public function post_video($description="",$title="",$file_url="", $file_source="",$thumbnail="",$scheduled_publish_time="",$post_access_token,$page_id ){
	 	
	 	$description=spintax_process($description);

	 	if($description!="")
	 		$params['description']=$description;
	 	if($description!="")
	 		$params['title']=$title;
	 	if($file_url!="")
	 		$params['file_url']=$file_url;
	 	if($file_source!="")
	 		$params['source']=$this->fb->fileToUpload($file_source);
	 	if($thumbnail!="")
	 		$params['thumb']=$this->fb->fileToUpload($thumbnail);
	 	if($scheduled_publish_time!=""){
	 		$params['scheduled_publish_time'] = $scheduled_publish_time;
	 		$params['published'] = true;
	 	}
	 	
	 	$params['is_crossposting_eligible']=1;
	 	$params['no_story']="false";
	 	$response = $this->fb->post("{$page_id}/videos",$params,$post_access_token);
	 	return $response->getGraphObject()->asArray();	
	 }

	public function post_video_no_story($description="",$title="",$file_url="", $file_source="",$thumbnail="",$scheduled_publish_time="",$post_access_token,$page_id ){

		$description=spintax_process($description);

	 	if($description!="")
	 		$params['description']=$description;
	 	if($description!="")
	 		$params['title']=$title;
	 	if($file_url!="")
	 		$params['file_url']=$file_url;
	 	if($file_source!="")
	 		$params['source']=$this->fb->fileToUpload($file_source);
	 	if($thumbnail!="")
	 		$params['thumb']=$this->fb->fileToUpload($thumbnail);
	 	if($scheduled_publish_time!=""){
	 		$params['scheduled_publish_time'] = $scheduled_publish_time;
	 		$params['published'] = false;
	 	}
	 	
	 	$params['is_crossposting_eligible']=1;
	 	$params['no_story']="true";
	 	$response = $this->fb->post("{$page_id}/videos",$params,$post_access_token);
	 	return $response->getGraphObject()->asArray();	
	 }


	public function get_youtube_video_url($youtube_video_id)
	{
	 	$vformat = "video/mp4"; 
	 	parse_str(file_get_contents("http://youtube.com/get_video_info?video_id={$youtube_video_id}"),$info);
	 	if(isset($info['status']) && $info['status']=="fail")
	 		return 'fail';

	 	$streams = $info['url_encoded_fmt_stream_map']; 
	 	$streams = explode(',',$streams);
	 	foreach($streams as $stream){
	 		parse_str($stream,$data); 
	 		if(stripos($data['type'],$vformat) !== false){ //We've found the right stream with the correct format
	 		$video_file_url = $data['url'];
	 		}
	 	}
	 	return $video_file_url;				
	}
	
	
	public function create_native_offer($page_id,$post_access_token,$discount_type,$disc_text,$disc_value,$details="",$expiration_time,$link,$location_type="",$terms="",$max_save_count="",$coupon_code="",$barcode_type="",$barcode_value="",$instore_code="",$currency="",$time_zone=""){
	
			/****This is the array format for list<object>	****/
				
			if($discount_type!="")	
				$disc_1['type']=$discount_type;
			if($disc_text!="")
				$disc_1['text']=$disc_text;
			if($disc_value!="")
				$disc_1['value1'] =$disc_value;
			if($currency!="")
				$disc_1['currency'] =$currency;
			
			$discounts=array("0"=>$disc_1);
			
			$params['discounts']=$discounts;
			
			if($details!="")
				$params['details']=$details;
			if($expiration_time!="")
			{
				date_default_timezone_set($time_zone);
            	$expiration_time_formated=strtotime($expiration_time);
            	$params['date_format']='U';
				$params['expiration_time']=$expiration_time_formated;
			}
			if($link!="")
				$params['redemption_link']=$link;
			if($max_save_count!="")	
				$params['max_save_count']=$max_save_count;
			if($location_type!="")
				$params['location_type']=$location_type;
			if($coupon_code!="")
				$params['online_code']=$coupon_code;
			if($terms!="")
				$params['terms']=$terms;
			if($barcode_type!="")
				$params['barcode_type']=$barcode_type;
			if($barcode_value!="")
				$params['barcode_value']=$barcode_value;
			if($instore_code!="")
				$params['instore_code']=$instore_code;

			$response = $this->fb->post("{$page_id}/nativeoffers",$params,$post_access_token);

			return $response->getGraphObject()->asArray();
			
	}

	public function create_native_offer_views($offer_id,$post_access_token,$photo_array="",$video_array="",$message){
	
		
		$params['message']=$message;
		$params['published']=1;
		if($photo_array!="")
			$params['photos']=$photo_array;
		if($video_array!="")
			$params['videos']=$video_array;
		
		$response = $this->fb->post("{$offer_id}/nativeofferviews",$params,$post_access_token);
		return $response->getGraphObject()->asArray();
		echo "<pre>";
		print_r($photo_array);
		exit();
		
		
	}

	function carousel_post($message="",$link="",$child_attachments="",$scheduled_publish_time="",$post_access_token="",$page_id="")
	{
		$message=spintax_process($message);

		if($message!="")
			$params['message'] = $message;
		if($link!=""){
			$params['link'] = $link;
		}
		$params['child_attachments'] = $child_attachments;
		if($scheduled_publish_time!=""){
			$params['scheduled_publish_time'] = $scheduled_publish_time;
			$params['published'] = false;
		}
		$response = $this->fb->post("{$page_id}/feed",$params,$post_access_token);
		return $response->getGraphObject()->asArray();
	}

	
	public function post_image_video($description="",$image_urls=array(),$duration,$transition_time,$scheduled_publish_time="",$post_access_token,$page_id)
	{

		$description=spintax_process($description);

		$slideshow_spec_array=array(
		"images_urls"=>$image_urls,
		"duration_ms"  => $duration,
		"transition_ms"  => $transition_time
		);
		if($description!="")
			$params['description'] = $description;

		if($scheduled_publish_time!=""){
			$params['scheduled_publish_time'] = $scheduled_publish_time;
			$params['published'] = false;
		}
		$params['slideshow_spec'] = $slideshow_spec_array;
		$response = $this->fb->post("{$page_id}/videos",$params,$post_access_token);
		return $response->getGraphObject()->asArray();
	}


	public function app_info_graber($app_id='',$app_secret='')
	{
		$url = "https://graph.facebook.com/".$app_id."?access_token=".$app_id."|".$app_secret."&fields=name,link,id,category,photo_url";
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
		return $result;
		
	}


	// ================== webhook enable disable ==============//
	// Array([success] => 1)
	public function enable_bot($page_id='',$post_access_token='')
	{
		if($page_id=='' || $post_access_token=='') 
		{
			return array('success'=>0,'error'=>$this->CI->lang->line("Something went wrong, please try again.")); 
			exit();
		}
		try 
		{
			$params=array();			
			$params['subscribed_fields']= array("messages","messaging_optins","messaging_postbacks","messaging_referrals","feed");			
			$response = $this->fb->post("{$page_id}/subscribed_apps",$params,$post_access_token);			
			$response = $response->getGraphObject()->asArray();
			$response['error']='';
			return $response;			
		} 
		catch (Exception $e) 
		{
			return array('success'=>0,'error'=>$e->getMessage());
		}
	}

	// Array([success] => 1)
	public function disable_bot($page_id='',$post_access_token='')
	{
		if($page_id=='' || $post_access_token=='') 
		{
			return array('success'=>0,'error'=>$this->CI->lang->line("Something went wrong, please try again.")); 
			exit();
		}
		try 
		{
			$response = $this->fb->delete("{$page_id}/subscribed_apps",array(),$post_access_token);
			$response = $response->getGraphObject()->asArray();
			$response['error']='';
			return $response;			
		} 
		catch (Exception $e) 
		{
			return array('success'=>0,'error'=>$e->getMessage());
		}
	}

	/* Delete Persistent Menu */
	public function delete_persistent_menu($post_access_token='')
	{
		$url = "https://graph.facebook.com/v4.0/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data='{"fields":["persistent_menu"]}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json; charset=UTF-8");
	
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 
	 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
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

	/* Delete get Started Button */
	public function delete_get_started_button($post_access_token='')
	{
		$url = "https://graph.facebook.com/v4.0/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data='{"fields":["get_started"]}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 
	 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch); 
	 	$result=json_decode($st,TRUE);
	 
	 	if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['success']=1;
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
			$result['success']=0;
		}
		return $result;
	}


	/* Add get Started Button */
	public function add_get_started_button($post_access_token='')
	{
	
		$url = "https://graph.facebook.com/v4.0/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data='{"get_started":{"payload":"GET_STARTED_PAYLOAD"}}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 
	 	curl_setopt($ch,CURLOPT_POST,1);
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch);	 
	 	$result=json_decode($st,TRUE);
	 	if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['success']=1;
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
			$result['success']=0;
		}
		return $result;
	}


	public function set_welcome_message($post_access_token='',$welcome_message='')
	{
		if($welcome_message=='') return false;
	
		$url = "https://graph.facebook.com/v4.0/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data=array
		(
			'greeting'=>array(0=>array("locale"=>"default","text"=>$welcome_message))
		);
		// $get_started_data='{"greeting":[{"locale":"default","text":"'.$welcome_message.'"}]}';
		$get_started_data=json_encode($get_started_data);
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 
	 	curl_setopt($ch,CURLOPT_POST,1);
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch);	 
	 	$result=json_decode($st,TRUE);
	 	if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['success']=1;
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
			$result['success']=0;
		}

		return $result;
	}


	public function unset_welcome_message($post_access_token='')
	{
		$url = "https://graph.facebook.com/v4.0/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data='{"fields":["greeting"]}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 
	 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch); 
	 	$result=json_decode($st,TRUE);
	 
	 	if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['success']=1;
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
			$result['success']=0;
		}
		return $result;
	}


	/* Add Persistent Menu */
	public function add_persistent_menu($post_access_token='',$menu_content_json='')
	{
		$url = "https://graph.facebook.com/v4.0/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data=$menu_content_json;
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 
	 	curl_setopt($ch,CURLOPT_POST,1);
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
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

	

	function get_page_review_status($post_access_token='')
	{
		$url="https://graph.facebook.com/v4.0/me/messaging_feature_review?access_token={$post_access_token}";
		$headers = array("Content-type: application/json");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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


	//https://developers.facebook.com/docs/messenger-platform/send-messages/broadcast-messages/estimate-reach/
	function start_reach_estimation($post_access_token='')
	{
		$url="https://graph.facebook.com/v4.0/me/broadcast_reach_estimations?access_token={$post_access_token}&method=post";
		$ch = curl_init();
		$headers = array("Content-type: application/json");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// curl_setopt($ch,CURLOPT_POST,1);
		//curl_setopt($ch,CURLOPT_POSTFIELDS,$message);
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



	function reach_estimation_count($reach_estimation_id='',$post_access_token='')
	{
		$url="https://graph.facebook.com/v4.0/{$reach_estimation_id}?access_token={$post_access_token}";
		$ch = curl_init();
		$headers = array("Content-type: application/json");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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
	

	/*** Subscription based message sent  https://developers.facebook.com/docs/messenger-platform/send-messages/message-tags ***/
	function send_non_promotional_message_subscription($message='[]',$post_access_token='')
	{
		$url = "https://graph.facebook.com/v4.0/me/messages?access_token={$post_access_token}";

		$ch = curl_init();
		$headers = array("Content-type: application/json");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$message);
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


	//calls fb api using post variable and json header
	function call_api_post($json='',$url='',$delete=false)
	{
		$ch = curl_init();
		$headers = array("Content-type: application/json");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if($delete)	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		if($json!="") 
		{
			curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
			curl_setopt($ch,CURLOPT_POST,1);
		}
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
		$st=curl_exec($ch);  
		$result=json_decode($st,TRUE);
		return $result;
	}


	// Array([result] => Successfully updated whitelisted domains)
	public function domain_whitelist($access_token='',$domain='')
	{
		if($access_token=='' || $domain=='') 
		{
			return array('status'=>'0','result'=>$this->CI->lang->line("Something went wrong, please try again.")); 
			exit();
		}

		// Fetch all current whitelisted domains 
		$url = "https://graph.facebook.com/v8.0/me/messenger_profile?fields=whitelisted_domains&access_token={$access_token}";
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch); 
	 	$result=json_decode($st,TRUE);

 		if(isset($result["error"])) 
 		{
 			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
 			$result['status']='0';
 			return $result;
 		}

 		$current_whitelisted_domains= isset($result['data'][0]['whitelisted_domains']) ? $result['data'][0]['whitelisted_domains'] : array();
 		$current_whitelisted_domains[]=$domain;

 		$url = "https://graph.facebook.com/v8.0/me/messenger_profile?access_token={$access_token}";
 		$whitelisted_domains_data['whitelisted_domains']= $current_whitelisted_domains;
 		$whitelisted_domains_data=json_encode($whitelisted_domains_data);

		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 	curl_setopt($ch,CURLOPT_POST,1);
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$whitelisted_domains_data); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch);	 
 	 	$result=json_decode($st,TRUE);

 	 	if(isset($result["result"])) 
 		{
 			$result["result"]=$this->CI->lang->line(trim($result["result"]));
 			$result['status']='1';
 		}
 		if(isset($result["error"])) 
 		{
 			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
 			$result['status']='0';
 		}

 		return $result;
	}

	// page_messages_reported_conversations_by_report_type_unique and page_messages_active_threads_unique metrics have been depreciated
	function get_analytics_data($access_token="",$from_date="",$to_date='')
	{
		$url = "https://graph.facebook.com/v4.0/me/insights/?metric=page_messages_total_messaging_connections,page_messages_new_conversations_unique,page_messages_blocked_conversations_unique,page_messages_reported_conversations_unique&access_token={$access_token}&since={$from_date}&until={$to_date}";
    	$ch = curl_init();
    	$headers = array("Content-type: application/json");
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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


	
	function create_label($page_access_token="",$label="") //{"id": 1712444532121303}
	{
		$url="https://graph.facebook.com/v4.0/me/custom_labels?access_token={$page_access_token}";
		$json=json_encode(array("page_label_name"=>$label));
		return $this->call_api_post($json,$url);
	}

	function assign_label($page_access_token='',$psid='',$label_id='') //{"success": true}
	{
		$url="https://graph.facebook.com/v4.0/{$label_id}/label?access_token={$page_access_token}";
		$json=json_encode(array("user"=>$psid));
		return $this->call_api_post($json,$url);
	}

	function deassign_label($page_access_token='',$psid='',$label_id='')//{"success": true}
	{
		$url="https://graph.facebook.com/v4.0/{$label_id}/label?access_token={$page_access_token}";
		$json=json_encode(array("user"=>$psid));
		return $this->call_api_post($json,$url,true);
	}

	function delete_label($page_access_token='',$label_id='')//{"success": true}
	{
		$url="https://graph.facebook.com/v4.0/{$label_id}?access_token={$page_access_token}";
		return $this->call_api_post('',$url,true);
	}

	function retrieve_label($page_access_token='')
	{
		$url="https://graph.facebook.com/v4.0/me/custom_labels?fields=page_label_name&access_token={$page_access_token}&limit=200";
		return $this->call_api_post('',$url,false);
	}

	function retrieve_level_of_psid($psid,$page_access_token){

		$url="https://graph.facebook.com/v4.0/{$psid}/custom_labels?fields=page_label_name&access_token=$page_access_token&limit=200";
		return $this->call_api_post('',$url,false);

	}

	public function get_all_comment_of_post_pagination($post_ids,$post_access_token)
	{ 
		$url="{$post_ids}/comments?order=reverse_chronological&summary=1&limit=400&filter=toplevel";

		$comment_info=array();
		$commenter_info=array();

		$i=0;

		do
		{
			$response = $this->fb->get($url,$post_access_token);
			$data =  $response->getGraphEdge()->asArray();
			$paging_data= $response->getGraphEdge()->getMetaData(); 

			foreach($data as $info){

				$time=  isset($info['created_time'])?(array)$info['created_time']:"";

				$comment_info[$i]['created_time']=isset($time['date'])?$time['date']:"";
				$comment_info[$i]['commenter_name']=isset($info['from']['name'])? $info['from']['name']:"";
				$comment_info[$i]['commenter_id']=isset($info['from']['id'])?$info['from']['id']:"";
				$comment_info[$i]['message']=isset($info['message'])?$info['message']:"";
				$comment_info[$i]['comment_id']=isset($info['id'])?$info['id']:"";

				/* Store Commenter info as unique */

				if(!isset($commenter_info[$comment_info[$i]['commenter_id']])){
					$commenter_info[$comment_info[$i]['commenter_id']]['name']=$comment_info[$i]['commenter_name'];
					$commenter_info[$comment_info[$i]['commenter_id']]['last_comment']=$comment_info[$i]['message'];
					$commenter_info[$comment_info[$i]['commenter_id']]['last_comment_id']=$comment_info[$i]['comment_id'];
					$commenter_info[$comment_info[$i]['commenter_id']]['last_comment_time']=$comment_info[$i]['created_time'];
				}

				$i++;
			}

			$next= isset($paging_data['paging']['cursors']['after'])?$paging_data['paging']['cursors']['after']:"";

			if($next!="")
				$url="{$post_ids}/comments?order=reverse_chronological&after={$next}&limit=400&filter=toplevel";
			else
				$url="";

		}
		while($url!='');

		$all_info=array();

		$all_info['comment_info']= $comment_info;
		$all_info['commenter_info']= $commenter_info;

		return $all_info;
	}

	function fb_like_comment_share($url,$access_token)
	{
		
		$url="https://graph.facebook.com/v4.0/?id={$url}&fields=engagement,og_object&access_token={$access_token}";
		$response1=$this->run_curl_for_fb($url);
		$response = json_decode($response1,true);
		if(isset($response['error']['message'])){
			$response_error['errormessage']= $response['error']['message'];
			return $response_error; 
		}

		
		if (isset($response['engagement']['share_count'])) 
			 $get_total_share['total_share'] = $response['engagement']['share_count']; 
		else
			$get_total_share['total_share'] = 0;

		if (isset($response['engagement']['reaction_count']))
			$get_total_share['total_reaction'] = $response['engagement']['reaction_count'];
		else
			$get_total_share['total_reaction'] = 0;

		if (isset($response['engagement']['comment_count']))
			$get_total_share['total_comment'] = $response['engagement']['comment_count'];
		else
			$get_total_share['total_comment'] = 0;

		if (isset($response['engagement']['comment_plugin_count']))
			$get_total_share['total_comment_plugin'] = $response['engagement']['comment_plugin_count'];
		else
			$get_total_share['total_comment_plugin'] = 0;

		$get_total_share['og_id']= isset($response['og_object']['id']) ? $response['og_object']['id']:"";
		$get_total_share['description']=isset($response['og_object']['description']) ? $response['og_object']['description']:"";
		$get_total_share['title']= isset($response['og_object']['title']) ? $response['og_object']['title']:"";
		$get_total_share['type']= isset($response['og_object']['type']) ? $response['og_object']['type']:"";
		$get_total_share['updated_time']= isset($response['og_object']['updated_time']) ? $response['og_object']['updated_time']:"";

		return $get_total_share;
	}

	function update_rul_for_like_share_count($url,$access_token){
		
	}




	public function location_search($access_token,$keyword,$latitude,$longitude,$distance,$search_limit){
			
		$keyword=urlencode($keyword);
		$center=$latitude.",".$longitude;	
	
		$url="https://graph.facebook.com/v4.0/search?q={$keyword}&type=place&access_token={$access_token}&fields=id,name,overall_star_rating,website,about,category_list,checkins,cover,description,engagement,hours,is_always_open,is_permanently_closed,payment_options,price_range,rating_count,restaurant_services,is_verified,location,link,phone&center={$center}&distance={$distance}&limit={$search_limit}";
		 $results=$this->facebook_api_call($url);
		
		 if(isset($results['error']['message']))
		 {
		 	$response_error['error_message'] = $results['error']['message'];
		 	return $response_error;
		 }
		 
		 if(!is_array($results) || !isset($results['data'])) return array("data"=>array());
		 
		 $final_result[0]=$results['data'];
		 $total_found = count($final_result[0]);

		 $next_page= isset($results['paging']['next']) ? $results['paging']['next']:"" ;
		 
		 for($i=1;$i<=5;$i++){
		 
		 	if(!$next_page){
				break;
		 	}
		 
		 	$next_page_result	= $this->facebook_api_call($next_page);
			$final_result[$i]=isset($next_page_result['data']) ? $next_page_result['data']:array();
			$total_found += count($final_result[$i]);
			$next_page= isset($next_page_result['paging']['next']) ? $next_page_result['paging']['next']:"" ;
		 }
		$response['total_found']=$total_found;
		$response['data']=$final_result;
		
		return $response;
	


	}

	/**
	 * Facebook Page insights
	 * @param  string $access_token page access token
	 * @param  string $page_id      Page id
	 * @param  string $from_date    from date
	 * @param  string $to_date      to date
	 * @return array
	 */
	public function page_insights($access_token="",$page_id ="",$from_date="",$to_date="")
	{

		// $from = date('Y-m-d', strtotime(date('Y-m-d').' -28 day'));
		// $to   = date('Y-m-d', strtotime(date("Y-m-d").'-1 day'));

		/* Page Metrics */
		$metrics = 'page_content_activity_by_action_type_unique,page_content_activity,page_content_activity_by_action_type,page_impressions,page_impressions_unique,page_impressions_paid,page_impressions_paid_unique,page_impressions_organic,page_impressions_organic_unique,page_impressions_viral,page_impressions_viral_unique,page_impressions_nonviral,page_impressions_nonviral_unique,page_impressions_by_country_unique,page_engaged_users,page_post_engagements,page_consumptions,page_consumptions_unique,page_places_checkin_total,page_negative_feedback,page_positive_feedback_by_type,page_fans_online_per_day,page_actions_post_reactions_like_total,page_actions_post_reactions_love_total,page_actions_post_reactions_wow_total,page_actions_post_reactions_haha_total,page_actions_post_reactions_sorry_total,page_actions_post_reactions_anger_total,page_total_actions,page_cta_clicks_logged_in_total,page_call_phone_clicks_logged_in_unique,page_get_directions_clicks_logged_in_unique,page_website_clicks_logged_in_unique,page_website_clicks_by_site_logged_in_unique,page_get_directions_clicks_logged_in_by_city_unique,page_fans,page_fans_country,page_fan_adds,page_fans_by_like_source,page_fan_removes,page_fans_by_unlike_source_unique,page_tab_views_login_top,page_views_total,page_views_by_profile_tab_total,page_views_by_site_logged_in_unique,page_views_by_referers_logged_in_unique,page_video_views,page_video_views_paid,page_video_views_organic,page_video_views_autoplayed,page_video_views_click_to_play,page_video_views_unique,page_video_view_time,page_posts_impressions_viral,page_posts_impressions_nonviral,page_posts_impressions_paid,page_posts_impressions_organic,page_posts_impressions';
		try {
		  $request = $this->fb->get("/{$page_id}/insights/{$metrics}?&period=day&since=".$from_date."&until=".$to_date,$access_token);
		  $response['data'] = $request->getGraphList()->asArray();
		  return $response;

		} catch (Facebook\Exceptions\FacebookResponseException $e) {
		  $response['error']='1';
		  $response['message']= $e->getMessage();
		  return $response; 
		 
		
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$response['error']='1';
			$response['message']= $e->getMessage();
			return $response; 
		}

		 
	}


	/* Add Ice Breaker Questions */
	public function add_ice_breakers($post_access_token='',$icebreakers_content_json='',$social_media_type='fb')
	{
		
		if($social_media_type=='ig'){
			$url = "https://graph.facebook.com/v5.0/me/messenger_profile?platform=instagram&access_token={$post_access_token}";
			$icebreakers_content_array=json_decode($icebreakers_content_json,true);
			$icebreakers_content_array['platform']="instagram";
			$icebreakers_content_json=json_encode($icebreakers_content_array);
		}
		else
			$url = "https://graph.facebook.com/v5.0/me/messenger_profile?access_token={$post_access_token}";


		$ice_breakers_data=$icebreakers_content_json;
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 
	 	curl_setopt($ch,CURLOPT_POST,1);
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$ice_breakers_data); 
	 
	 	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch);	 
 	 	$result=json_decode($st,TRUE);
 	 	if(isset($result["result"])) 
 		{
 			$result["result"]=$this->CI->lang->line(trim($result["result"]));
 			$result['success']=1;
 		}
 		if(isset($result["error"])) 
 		{
 			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
 			$result['success']=0;
 		}
 		return $result;
	}

	public function delete_ice_breakers($post_access_token='',$social_media_type='fb')
	{	
		if($social_media_type=='fb')
			$url = "https://graph.facebook.com/v5.0/me/messenger_profile?access_token={$post_access_token}";
		else
			$url = "https://graph.facebook.com/v5.0/me/messenger_profile?platform=instagram&access_token={$post_access_token}";

		$get_started_data='{"fields":["ice_breakers"]}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 
	 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch); 
	 	$result=json_decode($st,TRUE);
 	 	if(isset($result["result"])) 
 		{
 			$result["result"]=$this->CI->lang->line(trim($result["result"]));
 			$result['success']=1;
 		}
 		if(isset($result["error"])) 
 		{
 			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
 			$result['success']=0;
 		}
 		return $result;
	}



	// vidcasterlive section start
	public function live_video_schedule($description="",$planned_time,$image="",$post_access_token,$page_id,$crossposting_pages=array())
	{
		if($description!="")
			$params['description'] = $description;

		if($planned_time!="")
			$params['planned_start_time'] = $planned_time;

		if($image!="")
			$params['schedule_custom_profile_image'] = $this->fb->fileToUpload($image);

		$params['status'] = "SCHEDULED_UNPUBLISHED"; 
		
		$privacy = array(
     		   'value' => 'EVERYONE' //private
   			 );
			 
		$params['privacy'] = $privacy;	


		if($crossposting_pages!="" && !empty($crossposting_pages)){

			$crossposting_actions=array();
			$i=0;
			foreach ($crossposting_pages as $key => $value) {
				$crossposting_actions[$i]['page_id'] = $value;
				$crossposting_actions[$i]['action'] = "enable_crossposting_and_create_post";
				$i++;
			}

			$crossposting_actions=json_encode($crossposting_actions);
			$params['crossposting_actions']=$crossposting_actions;
		}




		$response = $this->fb->post("{$page_id}/live_videos",$params,$post_access_token);
		$response= $response->getGraphObject()->asArray();

		return $response;
	}

	public function live_video_schedule_direct($description="",$post_access_token,$page_id,$crossposting_pages=array()) // no live event will be displayed
	{

		if($description!="")
			$params['description'] = $description;

		$params['status'] = "LIVE_NOW"; 
		
		$privacy = array(
     		   'value' => 'EVERYONE' //private
   			 );
			 
		$params['privacy'] = $privacy;	

		if($crossposting_pages!="" && !empty($crossposting_pages)){

			$crossposting_actions=array();
			$i=0;
			foreach ($crossposting_pages as $key => $value) {
				$crossposting_actions[$i]['page_id'] = $value;
				$crossposting_actions[$i]['action'] = "enable_crossposting_and_create_post";
				$i++;
			}

			$crossposting_actions=json_encode($crossposting_actions);
			$params['crossposting_actions']=$crossposting_actions;
		}


		$response = $this->fb->post("{$page_id}/live_videos",$params,$post_access_token);
		$response= $response->getGraphObject()->asArray();

		return $response;
	}

    function live_stream_ffmpeg_command_run_using_curl($url)
    {
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		// curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		// curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
		// curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt"); 	
		$content = curl_exec($ch);
		// $content=json_decode($content,TRUE);
		return true;
	}

	public function get_live_video_embed_code($post_id,$page_access_token){
		
		$url = "https://graph.facebook.com/?id={$post_id}&access_token={$page_access_token}&fields=description,embed_html";
		$response = $this->facebook_api_call($url);
		return $response;

	}


	/***Param status must be one of {UNPUBLISHED, LIVE_NOW, SCHEDULED_UNPUBLISHED, SCHEDULED_LIVE, SCHEDULED_CANCELED}***/

	public function update_live_video_schedule($live_video_id,$description="",$planned_time="",$image="",$is_live=0,$post_access_token,$page_id)
	{

		if($description!="")
			$params['description'] = $description;

		if($planned_time!="")
			$params['planned_start_time'] = $planned_time;

		if($image!="")
			$params['schedule_custom_profile_image'] = $this->fb->fileToUpload($image);

		if($is_live==1)
			$params['status'] = "LIVE_NOW";

		$response = $this->fb->post("{$live_video_id}",$params,$post_access_token);

		$response= $response->getGraphObject()->asArray();

		return $response;
	}
	
	// vidcasterlive section end


	// Start instagram function are here



	public function instagram_account_check_by_id($page_id='', $page_access_token='')
	{
		try
		{
			$request = $this->fb->get("{$page_id}?fields=instagram_business_account", $page_access_token);
			$response = $request->getGraphObject()->asArray();
			if(isset($response['instagram_business_account']['id']))
			{
				$instagram_business_account_id = $response['instagram_business_account']['id'];
			}else
			{
				$instagram_business_account_id = "";
			}
			return $instagram_business_account_id;
		}

		catch(Facebook\Exceptions\FacebookResponseException $e)
		{
			return $instagram_business_account_id="";
		}
		catch(Facebook\Exceptions\FacebookSDKException $e)
		{
			return $instagram_business_account_id="";
		}
	}

	public function instagram_account_info($instagram_account_id,$page_access_token)
	{
		$request = $this->fb->get("{$instagram_account_id}?fields=id,username,followers_count,media_count,website,biography", $page_access_token);
		$response = $request->getGraphObject()->asArray();
		return $response;
	}

	public function get_postlist_from_instagram_account($instagram_account_id,$page_access_token)
	{

		$limit = 100;

		$request = $this->fb->get("{$instagram_account_id}/media?fields=id,timestamp,caption,like_count,comments_count,media_type,media_url,permalink,is_comment_enabled&limit={$limit}", $page_access_token);
		$response = $request->getGraphList()->asArray();

		$response= json_encode($response);
		$response=json_decode($response,true);

		$final_data['data']=$response;
		return $final_data;
	}


	public function instagram_get_post_info_by_id($media_id,$page_access_token)
	{
		$request = $this->fb->get("{$media_id}?fields=caption,media_type,timestamp,permalink", $page_access_token);
		$response = $request->getGraphObject()->asArray();

		$response= json_encode($response);
		$response=json_decode($response,true);

		//$final_data['data']=$response;
		return $response;

		//$results= json_decode($results,TRUE);
	   //return $results;
	}

	public function instagram_get_media_info_by_comment($commentId, $userAccessToken)
	{
		$response = $this->fb->get("{$commentId}?fields=media,username,text,like_count", $userAccessToken);
		$data = $response->getGraphObject()->asArray();
		$data = json_encode($data);
	    $data = json_decode($data,true);
	    return $data;
	}

	public function instagram_get_all_comment_of_post($post_id,$page_access_token)
	{
		$response = $this->fb->get("{$post_id}/comments?fields=id,text,timestamp,username&limit=20", $page_access_token);
		$data = $response->getGraphList()->asArray();

		$data = json_encode($data);
	    $data = json_decode($data,true);
	    return $data;	    
	}

	public function instagram_get_all_comment_of_mention_post($user_id,$comment_id,$user_access_token)
	{
		$response = $this->fb->get("{$user_id}?fields=mentioned_comment.comment_id({$comment_id}){username,text,timestamp,media{id,media_url,permalink,media_type}}", $user_access_token);
		$data = $response->getGraphObject()->asArray();

		$data = json_encode($data);
	    $data = json_decode($data,true);

	    return $data;	    
	}

	public function instagram_get_all_comment_of_mention_caption($user_id,$media_id,$user_access_token)
	{
		$response = $this->fb->get("{$user_id}?fields=mentioned_media.media_id({$media_id}){caption,media_type,username,timestamp,media_url}", $user_access_token);
		$data = $response->getGraphObject()->asArray();

		$data = json_encode($data);
	    $data = json_decode($data,true);

	    return $data;	    
	}

	public function instagram_get_media_url($user_id,$media_id,$user_access_token)
	{
  		$response = $this->fb->get("{$media_id}?fields=id,media_type,media_url,owner,timestamp,permalink", $user_access_token);
		$data = $response->getGraphObject()->asArray();

		$data = json_encode($data);
	    $data = json_decode($data,true);

	    return $data;
	}

	public function instagram_get_hashtag_id($user_id,$tag,$user_access_token)
	{
		$url = "https://graph.facebook.com/ig_hashtag_search?user_id={$user_id}&access_token={$user_access_token}&q={$tag}";
		$results= $this->run_curl_for_fb($url);
		return json_decode($results,TRUE);
	}

	public function instagram_get_hashtag_result($user_id,$hashtag_id,$result_type,$user_access_token)
	{
		$url = "https://graph.facebook.com/{$hashtag_id}/{$result_type}?user_id={$user_id}&access_token={$user_access_token}&fields=id,media_url,like_count,comments_count,permalink,caption,media_type&limit=50";
		$results= $this->run_curl_for_fb($url);
		return json_decode($results,TRUE);
	}



	public function instagram_hide_comment($comment_id,$post_access_token)
	{
		$url="https://graph.facebook.com/v2.11/{$comment_id}?method=post&access_token={$post_access_token}&hide=true";
		$results= $this->run_curl_for_fb($url);
		return json_decode($results,TRUE);
	}

	public function instagram_delete_comment($comment_id,$post_access_token)
	{
		$url="https://graph.facebook.com/{$comment_id}?access_token={$post_access_token}&method=delete";
		$resuls = $this->run_curl_for_fb($url);
		return json_decode($resuls,TRUE);
	}
	public function instagram_auto_comment($auto_reply_comment_message, $comment_id, $user_access_token)
	{
		$response = $this->fb->post(
		    "/{$comment_id}/replies",
		    array (
		      "message" => $auto_reply_comment_message
		    ),
		    $user_access_token
		);
		return $response->getGraphObject()->asArray();
	}




	public function instagram_direct_auto_comment($message,$object_id,$page_access_token)
	{
		$params['message']=$message;
		$response = $this->fb->post("{$object_id}/comments",$params,$page_access_token);
		return $response->getGraphObject()->asArray();	
	}

	public function instagram_auto_mention_comment($auto_reply_comment_message, $media_id, $user_access_token, $user_id, $comment_id)
	{
		$auto_reply_comment_message=urlencode($auto_reply_comment_message);
		$url="https://graph.facebook.com/{$user_id}/mentions?comment_id={$comment_id}&media_id={$media_id}&message={$auto_reply_comment_message}&access_token={$user_access_token}&method=post";
		$resuls = $this->run_curl_for_fb($url);
		return json_decode($resuls,TRUE);
	}

	public function instagram_auto_mention_caption_comment($auto_reply_comment_message, $media_id, $user_access_token, $user_id)
	{
		$auto_reply_comment_message=urlencode($auto_reply_comment_message);
		$url="https://graph.facebook.com/{$user_id}/mentions?&media_id={$media_id}&message={$auto_reply_comment_message}&access_token={$user_access_token}&method=post";
		$resuls = $this->run_curl_for_fb($url);
		return json_decode($resuls,TRUE);
	}

	public function instagram_business_discovery_data($my_instagram_account_id, $my_user_access_token, $discover_username='')
	{
  		$response = $this->fb->get(
    		"/{$my_instagram_account_id}?fields=business_discovery.username({$discover_username}){followers_count,media_count}",
    		$my_user_access_token
  		);
  		return $response->getGraphObject()->asArray();
	}
	public function instagram_business_discovery_media_data($my_instagram_account_id, $my_user_access_token, $discover_username='')
	{
		$response = $this->fb->get(
    		"/{$my_instagram_account_id}?fields=business_discovery.username({$discover_username}){followers_count,media_count,media{caption,comments_count,like_count,media_type,media_url,permalink}}",
    		$my_user_access_token
  		);
  		return $response->getGraphObject()->asArray();
	}
	public function instagram_check_instagram_username($my_instagram_account_id, $my_user_access_token, $discover_username='')
	{
		try
		{
  			$response = $this->fb->get(
    		"/$my_instagram_account_id?fields=business_discovery.username($discover_username)",
    		$my_user_access_token
  			);
			$report['status'] = 'success';
			return json_encode($report);
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return json_encode($report);
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return json_encode($report);
		}
	}
	public function instagram_user_insight($business_account_id='',$metric='',$period='',$access_toke='')
	{
		$response = $this->fb->get("$business_account_id/insights?metric=$metric&period=$period",$access_toke);
		return $response = $response->getGraphList()->asArray();
	}

	public function instagram_media_insights($media_id='',$metric='',$access_toke='')
	{
		try
		{			
			$response = $this->fb->get("$media_id/insights?metric=$metric",$access_toke);
			return $response = $response->getGraphList()->asArray();
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return $report;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return $report;
		}
	}



	public function instagram_media_comment_enable_disable($media_id,$user_access_token,$is_enable=true)
	{
  		$response = $this->fb->post(
		    "/{$media_id}",
		    array (
		      "comment_enabled" => $is_enable
		    ),
		    $user_access_token
		);
		return $response->getGraphObject()->asArray();


	}


	// to get the media objects in which a Business or Creator Account has been tagged

	public function instagram_tagged_media($business_account_id='',$access_token='')
	{
		try
		{			
			$response = $this->fb->get("$business_account_id/tags?fields=permalink,media_type,media_url,timestamp,username,caption&limit=100",$access_token);
			return $response = $response->getGraphList()->asArray();
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return $report;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return $report;
		}
	}


	// Return array . Index :  id for success .  For error ,  status=error & message=error message. 

	public function instagram_create_post($business_account_id,$type="IMAGE",$media_url,$caption="",$user_access_token){

		$response = $this->instagram_create_media_container($business_account_id,$type,$media_url,$caption,$user_access_token);

		if(isset($response['status']) && $response['status']=="error"){
			$report['status'] = 'error';
			$report['message'] = $response['message'];
			return $report;
		}

		$container_id= $response['id'] ?? "";

		if($type == 'VIDEO') sleep(30);
		$response=$this->instagram_publishing_post_from_container($business_account_id,$container_id,$user_access_token);

		if(isset($response['status']) && $response['status']=="error"){
			$report['status'] = 'error';
			$report['message'] = $response['message'];
			return $report;
		}

		return $response;


	}



	public function instagram_create_media_container($business_account_id,$type="IMAGE",$media_url,$caption="",$user_access_token){

		// First Create Media Container 

		$params=array();

		if($type=="IMAGE"){
			$params['image_url'] = $media_url;
		}
		else{
			$params['video_url'] = $media_url;
			$params['media_type'] = "VIDEO";
		}

		if(isset($caption) && $caption!=""){
			$params['caption'] = $caption;
		}
		
		try
		{			
			$response = $this->fb->post("{$business_account_id}/media",$params,$user_access_token);
			return $response->getGraphObject()->asArray();			
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return $report;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return $report;
		}

	}


	public function instagram_publishing_post_from_container($business_account_id,$creation_id,$user_access_token){

		$params=array();
		$params['creation_id'] = $creation_id;
		
		try
		{			
			$response = $this->fb->post("{$business_account_id}/media_publish",$params,$user_access_token);
			return $response->getGraphObject()->asArray();			
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return $report;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$report['status'] = 'error';
			$report['message'] = $e->getMessage();
			return $report;
		}
	}

}


