<?php

class Wordpress_post 
{
	protected $wp_json_wp_v2_endpoint_posts = 'wp-json/wp/v2/posts';

	protected $xit_wsh_api_v1_endpoint_posts = 'xit/wsh/api/v1/posts';
	
	public function __construct(){
	
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");		
		
	}

    public function get_posts($url)
    {
		if (! filter_var($url, FILTER_SANITIZE_URL, FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid blog URL provided.');
		}				

		// Detects which endpoint is being called
		$default_call = true;
		$custom_call = false;

        $blog_url = $this->trail_slash($url) . $this->wp_json_wp_v2_endpoint_posts;
        $result = $this->send_request($blog_url);
        $posts = json_decode($result, true);

        $response = [];

        if (null == $posts || ! is_array($posts)) {

	        $blog_url = $this->trail_slash($url) . $this->xit_wsh_api_v1_endpoint_posts;
	        $result = $this->send_request($blog_url);
	        $posts = json_decode($result, true);

	        if (null == $posts || ! is_array($posts)) {
	            $response['error'] = 1;
	            $response['error_message'] = $this->CI->lang->line("Make sure the REST API is not disabled or your blog URL is the correct one.");

	            return $response;
	        }

			$default_call = false;
			$custom_call = true;
        }

        if(! count($posts)) {
            $response['error'] = 1;
            $response['error_message'] = $this->CI->lang->line("No posts found");

            return $response;
        }

        $element_list = [];
        
        if (true == $default_call) {
	        foreach($posts as $post) {
	            $post_type = isset($post['type']) ? $post['type'] : '';
	            if ('post' == $post_type) {
	                $element_list[] = [
	                    'pubDate' => isset($post['date']) ? $post['date'] : '',
	                    'link' => isset($post['link']) ? $post['link'] : '',
	                    'title' => isset($post['title']['rendered']) ? $post['title']['rendered'] : '',
	                ];
	            }
	        }
        } elseif (true == $custom_call) {
	        foreach($posts as $post) {
	            $post_status = isset($post['post_status']) ? $post['post_status'] : '';
	            if ('publish' == $post_status) {
	                $element_list[] = [
	                    'pubDate' => isset($post['post_date']) ? $post['post_date'] : '',
	                    'title' => isset($post['post_title']) ? $post['post_title'] : '',
	                    'link' => isset($post['guid']) ? $post['guid'] : '',
	                ];
	            }
	        }
        }


        $response['success'] = 1; 
        $response['element_list'] = $element_list;

        return $response;
    }

	private function send_request($url, $max_request_time = 2, $interval = 0) 
	{	
		// Sets initial values to some vars
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

	private function trail_slash($url) 
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