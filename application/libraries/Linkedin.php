<?php
include_once('linkedin/function.php');
include_once('linkedin/vendor/autoload.php');

class Linkedin{

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


		$linkedin_config = $this->CI->basic->get_data("linkedin_config",array('where'=>array('status'=>'1')));
		if(isset($linkedin_config[0]))
		{
			$this->client_id=$linkedin_config[0]["client_id"];
			$this->client_secret=$linkedin_config[0]["client_secret"];
		}

	}


	public function login_button($redirect_uris)
	{

        $auth_url="https://www.linkedin.com/oauth/v2/authorization?client_id={$this->client_id}&redirect_uri={$redirect_uris}&scope=r_liteprofile%20r_emailaddress%20w_member_social&response_type=code&state=offline";

        if(empty($this->client_id) || empty($this->client_secret)){ 
        	
        	if ($this->CI->session->userdata("user_type") == "Admin")
        		$auth_url = base_url('social_apps/linkedin_settings'); 
        	else 
        		$auth_url = base_url('comboposter/set_empty_app_error/linkedin');
        }

        return "<a href='{$auth_url}' class='btn btn-outline-primary btn-rounded'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
	}


	public function linkedin_info($authentication_code='',$redirect_uris)
	{

		$result=get_access_token($authentication_code,$this->client_id,$this->client_secret,$redirect_uris);
		/***Take access token, also there is the expiration duration*****/
		if (!isset($result['access_token']) || !isset($result['expires_in'])) {

			 $info['error_message'] = isset($result['error_description']) ? $result['error_description'] : "Unknown Error";
			 return $info;
		}

		$access_token=$result['access_token'];
		$token_expired=$result['expires_in'];

		$data=get_curl("https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))&oauth2_access_token={$access_token}");
		// $data=get_curl("https://api.linkedin.com/v2/people/~?format=json&oauth2_access_token={$access_token}");

		if(!isset($data['id'])){
			 $info['error_message'] = isset($data['message']) ? $data['message'] : "Unknown Error";
			 return $info;
		}

		
		$data['access_token'] = $access_token;



		$response = array();

		$first_name = isset($data['firstName']['localized']['en_US']) ? $data['firstName']['localized']['en_US'] : "";
		$last_name = isset($data['lastName']['localized']['en_US']) ? $data['lastName']['localized']['en_US'] : "";

		$response['name'] = $first_name . ' ' . $last_name;

		$profile_pic = isset($data['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier']) ? $data['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'] : "";

		$response['profile_pic'] = $profile_pic;
		$response['access_token'] = $access_token;
		$response['id'] = $data['id'];


		return $response;
	}


	public function updateProfilePicture($access_token)
	{
		$data=get_curl("https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))&oauth2_access_token={$access_token}");
		// $data=get_curl("https://api.linkedin.com/v2/people/~?format=json&oauth2_access_token={$access_token}");
		$data['access_token'] = $access_token;

		$response = array();

		$first_name = isset($data['firstName']['localized']['en_US']) ? $data['firstName']['localized']['en_US'] : "";
		$last_name = isset($data['lastName']['localized']['en_US']) ? $data['lastName']['localized']['en_US'] : "";

		$response['name'] = $first_name . ' ' . $last_name;

		$profile_pic = isset($data['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier']) ? $data['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'] : "";

		$response['profile_pic'] = $profile_pic;
		$response['access_token'] = $access_token;
		$response['id'] = $data['id'];


		$this->CI->basic->update_data('table_name', array(where_condition), $data);
	}


	public function linkedin_page_info($access_token)
	{
		$data=get_curl("https://api.linkedin.com/v2/companies?format=json&is-company-admin=true&oauth2_access_token={$access_token}");
		// $company_id= $data['values'][0]['id'];
		return $data;
	}



	public function text_post_to_linkedin($linkedin_id,$access_token,$message = '')
	{
		$url = "https://api.linkedin.com/v2/ugcPosts";

	 	$post = array(
		    "author" => "urn:li:person:".$linkedin_id,
		    "lifecycleState" => "PUBLISHED",
		    "specificContent" => array(
		      "com.linkedin.ugc.ShareContent" => array(
		        "shareCommentary" => array(
		          "text" => $message,
		        ),
		        "shareMediaCategory" => "NONE",
		      )
		    ),
		    "visibility" => array(
		      "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC",
		    )

	  	);

		$headers = array("Authorization: Bearer {$access_token}", "X-Restli-Protocol-Version: 2.0.0", "Content-type: application/json");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  
		$st=curl_exec($ch);  
		$result=json_decode($st,TRUE);  

		// Return headers seperatly from the Response Body
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($st, 0, $header_size);
		$body = substr($st, $header_size);

		$headers_arr = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n)
		// print_r($headers_arr); // Shows the content of the $headers_arr array

		if (trim($headers_arr[0]) == 'HTTP/2 201') {

		    $temp_id = str_replace('x-restli-id: ', '', $headers_arr[4]);
		    return $final_link = "https://www.linkedin.com/feed/update/{$temp_id}";

		} else if (trim($headers_arr[0]) == 'HTTP/2 409') {

		    return "Duplicate post.";

		} else {

		    return $headers_arr[0];
		}
	}

	public function link_post_to_linkedin($linkedin_id, $access_token, $message, $title, $link)
	{

		$url = "https://api.linkedin.com/v2/ugcPosts";

		$post = array(
			"author" =>"urn:li:person:".$linkedin_id,
			"lifecycleState" =>"PUBLISHED",
			"specificContent" => array(
				"com.linkedin.ugc.ShareContent" => array(
					"shareCommentary" => array(
						"text" => $message,
					),
					"shareMediaCategory" => "ARTICLE",
					"media" => array(
						"0" => array(
							"status" => "READY",
							"description" => array(
							    "text" => "",
							),
							"originalUrl" => $link,
							"title" => array(
							    "text" => $title,
							),
						),
					),
				),
			),
			"visibility" => array(
			    "com.linkedin.ugc.MemberNetworkVisibility" => "CONNECTIONS"
			)
		);
		        

		$headers = array("Authorization: Bearer {$access_token}", "X-Restli-Protocol-Version: 2.0.0", "Content-type: application/json");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  
		$st=curl_exec($ch);  
		$result=json_decode($st,TRUE);  


		// Retudn headers seperatly from the Response Body
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($st, 0, $header_size);
		$body = substr($st, $header_size);

		$headers_arr = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n)
		// print_r($headers_arr); // Shows the content of the $headers_arr array

		if (trim($headers_arr[0]) == 'HTTP/2 201') {
		    
		    $temp_id = str_replace('x-restli-id: ', '', $headers_arr[4]);
		    return $final_link = "https://www.linkedin.com/feed/update/{$temp_id}";

		} else if (trim($headers_arr[0]) == 'HTTP/2 409') {

		    return "Duplicate post.";

		} else {

		    return $headers_arr[0];
		}
	}

}
