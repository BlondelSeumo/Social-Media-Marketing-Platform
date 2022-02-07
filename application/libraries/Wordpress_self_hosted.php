<?php
class Wordpress_self_hosted {

	public $user_id;

	protected $xit_wsh_api_v1_endpoint_post = 'xit/wsh/api/v1/post';

	protected $xit_wsh_api_v1_endpoint_categories = 'xit/wsh/api/v1/categories';

	protected $wp_json_wp_v2_endpoint_categories = 'wp-json/wp/v2/categories';

	public function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");
	}

	public function login_button()
	{
		return "<a href='" . base_url('social_apps/wordpress_settings_self_hosted') . "' class='btn btn-outline-primary login_button' social_account='twitter'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
	}

	public function post_to_wordpress_self_hosted($data)
	{

		if (! isset($data['domain_name']) || empty($data['domain_name'])) {
			throw new \Exception('Wordpress (self-hosted) domain name had not specified.');
		}

		if (! isset($data['user_key']) || empty($data['user_key'])) {
			throw new \Exception('The user key had not specified.');
		}

		if (! isset($data['authentication_key']) || empty($data['authentication_key'])) {
			throw new \Exception('The authentication key had not specified.');
		}				

		$url = $this->trail_slash($data['domain_name']) . $this->xit_wsh_api_v1_endpoint_post;
		$post_data = http_build_query($data);

		// Inits CURL session
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);

		// Closes CURL session
		curl_close($ch);

		return $response;
	}

	public function get_categories($url)
	{
		if (! filter_var($url, FILTER_SANITIZE_URL, FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid blog URL provided.');
		}				

		// Detects which endpoint is being called
		$default_call = true;
		$custom_call = false;

		$blog_url = $this->trail_slash($url) . $this->wp_json_wp_v2_endpoint_categories;
		$result = $this->send_request($blog_url);
		$categories = json_decode($result, true);

        $response = [];

        if (null == $categories || ! is_array($categories)) {
        	$blog_url = $this->trail_slash($url) . $this->xit_wsh_api_v1_endpoint_categories;
			$result = $this->send_request($blog_url);
			$categories = json_decode($result, true);

			if (null == $categories || ! is_array($categories)) {
	            $response['error'] = true;
	            $response['error_message'] = $this->CI->lang->line("Make sure the REST API is NOT disabled or the blog URL is the correct one or Wordpress Self-hosted Poster plugin is installed.");

	            return $response;
			}

			$default_call = false;
			$custom_call = true;
        }

        if(! count($categories)) {
            $response['error'] = true;
            $response['error_message'] = $this->CI->lang->line("No categories found");

            return $response;
        }

        $category_list = [];
        
        if (true == $default_call) { 
	        foreach($categories as $category) {
	            $category_list[$category['id']] = isset($category['name']) 
	            	? $category['name'] 
	            	: 'Uncategorized';
	        }
	    } elseif (true == $custom_call) {
	        foreach($categories as $category) {
	            $category_list[$category['term_id']] = isset($category['name']) 
	            	? $category['name'] 
	            	: 'Uncategorized';
	        }
	    }

        $response['success'] = true;
        $response['category_list'] = $category_list;

        return $response;
	}

	private function send_request($url, $max_request_time = 2, $interval = 0) 
	{	
		// Sets initial request number to 0
		$times = 0;
		$response = null;
        $headers = [
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        ];

		do {
			// Increases request number
			$times++;

			if (is_int($interval) && $interval > 0) {
				// Makes interval among requests
				sleep($interval);
			}

			// Inits curl session
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

			// Grabs what returns
			$response = curl_exec($ch);

			// Grabs request info
			$info = curl_getinfo($ch);

			// Closes curl session
			curl_close($ch);

			// If the respo
			if (isset($info['http_code']) && '200' != $info['http_code']) {
				continue;
			}
			
		} while ($times < $max_request_time);

		// Returns the response
		return $response;
	}

	/**
	 * Trail slash to the url
	 * @param string $url The URL to be suffixed with a forward slash
	 * @return string
	 * @since 1.0.0
	 */ 
	private function trail_slash ( $url ) 
	{
	    $slash = mb_substr($url, -1);
	    
	    if ('/' == $slash) {
	        return $url;
	    } elseif ("\\" == $slash) {
	        return $url;
	    }
	    
	    return $url . '/';
	}	
}
