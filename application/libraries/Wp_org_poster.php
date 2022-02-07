<?php
class Wp_org_poster {

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


		$rx_wp_org_config = $this->CI->basic->get_data("wordpress_config",array('where'=>array('deleted'=>'0', "status"=>"1")));
		if(isset($rx_wp_org_config[0]))
		{
			$this->client_id=$rx_wp_org_config[0]["client_id"];
			$this->client_secret=$rx_wp_org_config[0]["client_secret"];
			$this->CI->session->set_userdata('wp_org_client_id',$this->client_id);
			$this->CI->session->set_userdata('wp_org_client_secret',$this->client_secret);
		}
	}

	public function login_button($redirect_url)
	{
		$login_url="https://public-api.wordpress.com/oauth2/authorize?client_id={$this->client_id}&redirect_uri={$redirect_url}&response_type=code";

		if(empty($this->client_id) || empty($this->client_secret)){

			if ($this->CI->session->userdata("user_type") == "Admin") {
			  $login_url = base_url('social_apps/wordpress_settings'); 
			} else {
			  $login_url = base_url('comboposter/set_empty_app_error/wordpress');
			}
		}

		return "<a href='{$login_url}' class='btn btn-outline-primary login_button' social_account='wordpress'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
	}

	public function post_to_wporg($accesstoken,$blog_id,$categories='',$title,$content,$tags='')
	{
		$ch = curl_init();

		$url= "https://public-api.wordpress.com/rest/v1.1/sites/{$blog_id}/posts/new/";
		$headers = array("authorization: Bearer $accesstoken","Content-Type: application/x-www-form-urlencoded");

		// $title="Hello Curl";
		// $content="Curl Content \n\n https://www.youtube.com/watch?v=EdLovuizuDc";

		$title=urlencode($title);
		$content=urlencode($content);

		$postData="title={$title}&content={$content}&category={$categories}&tag={$tags}&status=publish";

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
