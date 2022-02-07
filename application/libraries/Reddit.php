<?php
require_once('reddit/config.php');

class Reddit {

        public $user_id="";
        public $client_id="";
        public $client_secret="";

        public $access_token;
        public $token_type;
        public $auth_mode;
        public $apiHost;

    function __construct(){

        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->helper('my_helper');
        $this->CI->load->library('session');
        $this->CI->load->model('basic');
        $this->user_id=$this->CI->session->userdata("user_id");
        //set API endpoint
        $this->apiHost = redditConfig::$ENDPOINT_OAUTH;


        $reddit_config = $this->CI->basic->get_data("reddit_config",array('where'=>array('deleted'=>'0', "status"=>"1")));
        if(isset($reddit_config[0]))
        {
            $this->client_id=$reddit_config[0]["client_id"];
            $this->client_secret=$reddit_config[0]["client_secret"];
        }

        $this->auth_mode = '';
        // $this->client_id = 'JgGly0TiE2qLvA';
        // $this->client_secret = 'JCqgnreL69R2sk-DXfWU91zZjqw';

    }


    public function login_button($redirect_uri)
    {
         $state = rand();
         $urlAuth = sprintf("%s?response_type=code&client_id=%s&redirect_uri=%s&duration=permanent&scope=%s&state=%s",
                redditConfig::$ENDPOINT_OAUTH_AUTHORIZE,
                $this->client_id,
                $redirect_uri,
                redditConfig::$SCOPES,
                $state
         );

         if (empty($this->client_id) || empty($this->client_secret)) {

            if ($this->CI->session->userdata("user_type") == "Admin") {
              $urlAuth = base_url('social_apps/reddit_settings'); 
            } else {
              $urlAuth = base_url('comboposter/set_empty_app_error/reddit');
            }
         }

         return "<a href='{$urlAuth}' class='btn btn-outline-primary login_button' social_account='reddit'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line("Import Account")."</a>";
    }



    public function login_info($code,$redirect_uri)
    {
        // $redirect_uri = base_url()."rx_video_autopost/reddit_callback";
        //construct POST object for access token fetch request
        $postvals = sprintf("code=%s&redirect_uri=%s&grant_type=authorization_code",
                            $code,
                            $redirect_uri);

        //get JSON access token object (with refresh_token parameter)
        $token = self::runCurl(redditConfig::$ENDPOINT_OAUTH_TOKEN, $postvals, null, true);

        $this->access_token = $token->access_token;
        $this->token_type = $token->token_type;
        $this->auth_mode = 'oauth';
        $user_info = $this->getUser();


        $response = array();
        $response['access_token'] = $token->access_token;
        $response['token_type'] = $token->token_type;
        $response['username'] = $user_info->name;
        $response['profile_pic'] = $user_info->icon_img;
        $response['url'] = $user_info->subreddit->url;
        $response['refresh_token'] = $token->refresh_token;


        return $response;
    }


    public function getAccessToken($authentication_code, $redirect_uri)
    {

        $data = http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $authentication_code,
            'redirect_uri' => $redirect_uri
        ]);

        $credentials = $this->client_id . ':' . $this->client_secret;
        $credentials = base64_encode($credentials);
        $headers = [
            "Authorization: Basic $credentials\r\n",
        ];
        $curl = curl_init('https://www.reddit.com/api/v1/access_token');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $auth = curl_exec($curl);
        $curl_info = curl_getinfo($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // echo '<pre>';
        // print_r($auth);
        // echo '</pre>';
        // echo '<pre>';
        // print_r($curl_info);
        // echo '</pre>';
        // echo '<pre>';
        // print_r($err);
        // echo '</pre>';
        // exit();       
    }


    public function login_to_reddit($redirect_uri)
    {

        if (empty($this->client_id) || empty($this->client_secret)) {

            $this->CI->session->set_userdata('comboposter_app_settings_error', "Your App Is Not Set, So You Can't Import Your Account. Please First Set Your App From API Configuration, Then Import Your Account.");
            redirect(base_url('comboposter/app_settings_error'),'refresh');
        }

        //$redirect_uri = base_url()."rx_video_autopost/reddit_callback";
        $state = rand();
        $urlAuth = sprintf("%s?response_type=code&client_id=%s&redirect_uri=%s&duration=permanent&scope=%s&state=%s",
               redditConfig::$ENDPOINT_OAUTH_AUTHORIZE,
               $this->client_id,
               $redirect_uri,
               redditConfig::$SCOPES,
               $state
       );

        //forward user to PayPal auth page
        header("Location: $urlAuth");
    }


    public function login_info_1($code,$redirect_uri)
    {
        // $redirect_uri = base_url()."rx_video_autopost/reddit_callback";
        //construct POST object for access token fetch request
        $postvals = sprintf("code=%s&redirect_uri=%s&grant_type=authorization_code",
                            $code,
                            $redirect_uri);

        //get JSON access token object (with refresh_token parameter)
        $token = self::runCurl(redditConfig::$ENDPOINT_OAUTH_TOKEN, $postvals, null, true);

        $this->access_token = $token->access_token;
        $this->token_type = $token->token_type;
        $this->auth_mode = 'oauth';
        $user_info = $this->getUser();

        $response = array();
        $response['access_token'] = $token->access_token;
        $response['token_type'] = $token->token_type;
        $response['username'] = $user_info->name;
        $response['refresh_token'] = $token->refresh_token;


        return $response;
    }


    /**
    * Get user
    *
    * Get data for the current user
    * @link http://www.reddit.com/dev/api#GET_api_v1_me
    */
    public function getUser(){
        $urlUser = "{$this->apiHost}/api/v1/me";
        // return $urlUser;
        return self::runCurl($urlUser);
    }


    /**
    * cURL request
    *
    * General cURL request function for GET and POST
    * @link URL
    * @param string $url URL to be requested
    * @param string $postVals NVP string to be send with POST request
    */
    private function runCurl($url, $postVals = null, $headers = null, $auth = false){
        $ch = curl_init($url);

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 20
        );

        if (!empty($_SERVER['HTTP_USER_AGENT'])){
            $options[CURLOPT_USERAGENT] = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $options[CURLOPT_USERAGENT] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
        }

        if ($postVals != null){
            $options[CURLOPT_POSTFIELDS] = $postVals;
            $options[CURLOPT_CUSTOMREQUEST] = "POST";
        }

        if ($this->auth_mode == 'oauth'){
            $headers = array("Authorization: {$this->token_type} {$this->access_token}");
            $options[CURLOPT_HEADER] = false;
            $options[CURLINFO_HEADER_OUT] = false;
            $options[CURLOPT_HTTPHEADER] = $headers;
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }

        if ($auth){
            $options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            $options[CURLOPT_USERPWD] = $this->client_id . ":" . $this->client_secret;
            $options[CURLOPT_SSLVERSION] = 4;
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
        }

        curl_setopt_array($ch, $options);
        $apiResponse = curl_exec($ch);
        $response = json_decode($apiResponse);

        //check if non-valid JSON is returned
        if ($error = json_last_error()){
            $response = $apiResponse;
        }
        curl_close($ch);

        return $response;
    }



     public function createStory($title = null, $link = null, $subreddit = null,$text=null,$refresh_token,$token_type){


        $redirect_uri = base_url()."comboposter/login_callback/reddit";
        $access_token = $this->refresh_access_token($redirect_uri,$refresh_token);

        if(!$access_token){
             $status['error'] = "Error Getting Access Token";
             return $status;
        }

        $this->token_type = $token_type;
        $this->access_token = $access_token;
        $this->auth_mode = 'oauth';
        $urlSubmit = "{$this->apiHost}/api/submit";

        //data checks and pre-setup
        if ($subreddit == null){ return null; }
        $kind = ($link == null) ? "self" : "link";
        
        // $kind = ($text == null) ? "self" : "text";


        // $text = "this is test post :)";


        $postData = sprintf("kind=%s&sr=%s&title=%s&text=%s&r=%s",
                $kind,
                $subreddit,
                urlencode($title),
                $text,
                $subreddit
        );


        
        //if link was present, add to POST data
        if ($link != null){ $postData .= "&url=" . urlencode($link); }

        // var_dump($postData);
        // die();
        

        $response = self::runCurl($urlSubmit, $postData);

        if (isset($response->jquery[18][3][0]) && $response->jquery[18][3][0] == "that link has already been submitted"){
            $status['error'] = $response->jquery[18][3][0];
        }
        if (isset($response->jquery[20][3][0])) {
            $status['error'] = $response->jquery[20][3][0];
        }
        // $status['url'] = $response->jquery[16][3][0];

        return $response;
    }




    public function refresh_access_token($redirect_uri,$refresh_token){

        $authorizeUrl = 'https://www.reddit.com/api/v1/access_token';
        $post = array(
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token,
            "scope" => "save,modposts,identity,edit,flair,history,modconfig,modflair,modlog,modposts,modwiki,mysubreddits,privatemessages,read,report,submit,subscribe,vote,wikiedit,wikiread",
            "state" => "WHATEVER_VALUE",
            "duration" => "temporary",
            "redirect_uri" => $redirect_uri,
        );

        $payload = http_build_query($post);

        $ch = curl_init($authorizeUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_USERPWD, $this->client_id . ":" . $this->client_secret);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result,TRUE);



         if(isset($result['access_token'])){
            return $result['access_token'];
         }

         else
            return 0;

    }



}
