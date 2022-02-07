<?php

class Youtube_channel 
{
	protected $youtube_api_endpoint = 'https://www.googleapis.com/youtube/v3/';
	
	public function __construct(){
	
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");		
		
	}

	public function get_videos($channel_id, $api_key, $max_results = 10) 
	{
		$data = [
			'order' => 'date',
			'part' => 'snippet,id',
			'channelId' => $channel_id,
			'key' => $api_key,
			'maxResults' => $max_results,
		];

		$query = http_build_query($data);
		$url = $this->youtube_api_endpoint . 'search?' . $query;

	    $result = $this->send_request($url);
	    $array = json_decode($result, true);

        // An array of prepared error and success message
        $response = [];

        $errors = isset($array['error']['code'])
            ? $array['error']['code']
            : null;

        if ($errors) {
            $message = isset($array['error']['message'])
                ? $array['error']['message']
                : $this->CI->lang->line("Something went wrong! Please try again later.");

            $response['error'] = 1;
            $response['error_message'] = $message;

            return $response;
        }

	    $items = isset($array['items']) ? $array['items'] : [];

	    if(! count($items)) {
	    	$response['error'] = 1;
	    	$response['error_message'] = $this->CI->lang->line("No videos found");

	    	return $response;
	    }

	    $element_list = [];
	    
        foreach($items as $item) {
            $video_id = isset($item['id']['videoId']) ? $item['id']['videoId'] : '';
            $element_list[] = [
                'pubDate' => isset($item['snippet']['publishedAt']) 
                    ? $item['snippet']['publishedAt'] 
                    : '',
                'title' => isset($item['snippet']['title']) ? $item['snippet']['title'] : '',
                'link' => $video_id ? "https://www.youtube.com/watch?v={$video_id}" : '',
            ];
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
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");

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
}