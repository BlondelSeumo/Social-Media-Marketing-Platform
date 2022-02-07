<?php
require_once('twitter/TwitterAPIExchange.php');
include_once('twitter/codebird.php');

class Twitter{

    public $user_id="";
    public $consumer_key="";
    public $consumer_secret="";

    public $cb;

  function __construct(){

    $this->CI =& get_instance();
    $this->CI->load->database();
    $this->CI->load->helper('my_helper');
    $this->CI->load->library('session');
    $this->CI->load->model('basic');
    $this->user_id=$this->CI->session->userdata("user_id");


    $twitter_config = $this->CI->basic->get_data("twitter_config",array('where'=>array('deleted'=>'0', "status"=>"1")));
    if(isset($twitter_config[0])) {
      $this->consumer_key=$twitter_config[0]["consumer_id"];
      $this->consumer_secret=$twitter_config[0]["consumer_secret"];
    }
  }


  public function login_button($redirect_rul)
  {

    $auth_url = '';
    if (empty($this->consumer_key) || empty($this->consumer_secret)) {

        if ($this->CI->session->userdata("user_type") == "Admin") {
          $auth_url = base_url('social_apps/twitter_settings'); 
        } else {
          $auth_url = base_url('comboposter/set_empty_app_error/twitter');
        }

        return "<a href='{$auth_url}' class='btn btn-outline-primary login_button' social_account='twitter'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
    }
    
    \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
    $cb = \Codebird\Codebird::getInstance();

    // get the request token
    $reply = $cb->oauth_requestToken([
      'oauth_callback' => $redirect_rul
      ]);
    $error_message = '';

    if(!isset($reply->oauth_token) || !isset($reply->oauth_token_secret)) {

      if(isset($reply->errors[0]->message)) $error_message = $reply->errors[0]->message;
      else $error_message = json_encode($reply);

      if($error_message != '')
        $error_html = ' <i class="fas fa-info-circle red api_error_info" style="cursor: pointer;"></i>
                        <div class="modal fade" id="api_error_info_modal" data-backdrop="static" data-keyboard="false">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title"><i class="fa fa-error"></i> '.$this->CI->lang->line('Error Message').'</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                              </div>
                              <div class="modal-body"> 
                                <p>'.$error_message.'</p>
                              </div>
                              <div class="modal-footer">
                                <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-close"></i> '.$this->CI->lang->line("Close").'</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      ';
      else
        $error_html = '';

      return "<a href='{$auth_url}' class='btn btn-outline-primary login_button' social_account='twitter'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>".$error_html;
    }

    $this->CI->session->set_userdata('oauth_token',$reply->oauth_token);
    $this->CI->session->set_userdata('oauth_token_secret',$reply->oauth_token_secret);
    $this->CI->session->set_userdata('oauth_verify',true);


    $cb->setToken($reply->oauth_token, $reply->oauth_token_secret);
    $auth_url = $cb->oauth_authorize();

    return "<a href='{$auth_url}' class='btn btn-outline-primary login_button' social_account='twitter'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line('Import Account')."</a>";
  }

  public function twitter_login($redirect_rul)
  {

    if (empty($this->consumer_key) || empty($this->consumer_secret)) {
        redirect(base_url('social_apps/twitter_settings'),'refresh');
    }
    
    \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
    $cb = \Codebird\Codebird::getInstance();

      // get the request token
    $reply = $cb->oauth_requestToken([
      'oauth_callback' => $redirect_rul
      ]);

    if(!isset($reply->oauth_token) || !isset($reply->oauth_token_secret)) {

      if(isset($reply->errors[0]->message)) $error_message = $reply->errors[0]->message;
      else $error_message = "Something went wrong.";

      $this->CI->session->set_userdata('comboposter_app_settings_error', $error_message);
      redirect(base_url('comboposter/app_settings_error'),'refresh');
    }

    $this->CI->session->set_userdata('oauth_token',$reply->oauth_token);
    $this->CI->session->set_userdata('oauth_token_secret',$reply->oauth_token_secret);
    $this->CI->session->set_userdata('oauth_verify',true);


    $cb->setToken($reply->oauth_token, $reply->oauth_token_secret);
    $auth_url = $cb->oauth_authorize();
    header('Location: ' . $auth_url);

  }


  public function twitter_login_info($oauth_verifier)
  {
    \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
    $cb = \Codebird\Codebird::getInstance();

    $oauth_token = $this->CI->session->userdata('oauth_token');
    $oauth_token_secret = $this->CI->session->userdata('oauth_token_secret');
    // verify the token
    $cb->setToken($oauth_token, $oauth_token_secret);

      // get the access token
    $reply = $cb->oauth_accessToken([
      'oauth_verifier' => $oauth_verifier
      ]);

      // store the token (which is different from the request token!)
    $this->CI->session->set_userdata('final_auth_token',$reply->oauth_token);
    $this->CI->session->set_userdata('final_oauth_token_secret',$reply->oauth_token_secret);
    $this->CI->session->set_userdata('twitter_screen_name',$reply->screen_name);
    $this->CI->session->set_userdata('twitter_user_id',$reply->user_id);
  }

  public function test_tw($oauth_token,$oauth_token_secret)
  {
    $url = "https://data-api.twitter.com/insights/engagement/totals";
    $consumer_key = $this->consumer_key;
    $consumer_secret = $this->consumer_secret;
    $oauth_access_token = $oauth_token;
    $oauth_access_token_secret = $oauth_token_secret;

    $oauth = array( 'oauth_consumer_key' => $consumer_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $oauth_access_token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0');

    $base_info = $this->buildBaseString($url, 'POST', $oauth);
    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;

    $arrayPostFields['tweet_ids'] = array('910058273369288705');

    $arrayPostFields['engagement_types'] = array('impressions','engagements','favorites','retweets','replies');
    $arrayPostFields['groupings']['group1']['group_by'] = array('tweet.id','engagement.type');

    $postfields = json_encode($arrayPostFields);

    $header = array($this->buildAuthorizationHeader($oauth), 'Accept-Encoding: gzip','Content-Type: application/json');

    //Accept-Encoding: gzip

    $options = array( CURLOPT_HTTPHEADER => $header,
                      CURLOPT_POSTFIELDS => $postfields,
                      CURLOPT_HEADER => false,
                      CURLOPT_URL => $url ,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_SSL_VERIFYPEER => false);


    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $buffer = curl_exec($ch);

     // echo 'errno:' . curl_errno($ch);
     // echo 'error text:' . curl_error($ch);
     // echo "\n";

    curl_close($ch);

    $twitter_data_json = gzinflate(substr($buffer, 10, -8));
    if($twitter_data_json!="")
    {
    $twitter_data = json_decode($twitter_data_json, true);
    }

    print_r($twitter_data);echo "\n";

  }

  function buildBaseString($baseURI, $method, $params) {
    $r = array();
    ksort($params);
    foreach($params as $key=>$value){
        $r[] = "$key=" . rawurlencode($value);
    }
    return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
  }

  function buildAuthorizationHeader($oauth) {
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach($oauth as $key=>$value)
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
    $r .= implode(', ', $values);
    return $r;
  }

  public function get_user_data($oauth_token='', $oauth_token_secret='', $screen_name)
  {
      $auth_token = $oauth_token;
      $oauth_token_secret = $oauth_token_secret;

      \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
      $cb = \Codebird\Codebird::getInstance();

      // assign access token on each page load
      $cb->setToken($auth_token, $oauth_token_secret);

      $reply = $cb->users_show("screen_name=$screen_name");
      $response['name'] = $reply->name;
      $response['profile_image'] = $reply->profile_image_url_https;

      $reply = $cb->followers_list();
      $response['followers'] = count($reply->users);

      return $response;
  }

  public function post_to_twitter($oauth_token,$oauth_token_secret,$message)
  {
    $auth_token = $oauth_token;
    $oauth_token_secret = $oauth_token_secret;

    \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
    $cb = \Codebird\Codebird::getInstance();

    // assign access token on each page load
    $cb->setToken($auth_token, $oauth_token_secret);

    $settings = array(
      'oauth_access_token' => $auth_token,
      'oauth_access_token_secret' => $oauth_token_secret,
      'consumer_key' => $this->consumer_key,
      'consumer_secret' => $this->consumer_secret
      );
    $twitter = new TwitterAPIExchange($settings);

    $url = 'https://api.twitter.com/1.1/statuses/update.json';
    $requestMethod = 'POST';
    $postfields = array(
      'status' => $message );
    $response= $twitter->buildOauth($url, $requestMethod)
                       ->setPostfields($postfields)
                       ->performRequest();

    return $response;
  }

  public function photo_post_to_twitter($oauth_token, $oauth_token_secret, $image_str, $photos_caption = '')
    {
        $auth_token = $oauth_token;
        $oauth_token_secret = $oauth_token_secret;

        \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
        $cb = \Codebird\Codebird::getInstance();

        $cb->setToken($auth_token, $oauth_token_secret);

        $media_files[] = $image_str;
        $media_ids = [];

        foreach ($media_files as $file) {
            $reply = $cb->media_upload([
            'media' => $file,
          ]);
            $media_ids[] = $reply->media_id_string;
        }
        $media_ids = implode(',', $media_ids);

        // send Tweet with these medias
        $reply = $cb->statuses_update([
          'status'    => $photos_caption,
          'media_ids' => $media_ids,
        ]);

        if (isset($reply)) {
            return $reply;
        } else {
            return 'oppose something is wrong';
        }
    }


  public function video_post_to_twitter($oauth_token, $oauth_token_secret, $file_path, $video_caption = '')
  {
      $auth_token = $oauth_token;
      $oauth_token_secret = $oauth_token_secret;

      \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
      $cb = \Codebird\Codebird::getInstance();

      // assign access token on each page load
      $cb->setToken($auth_token, $oauth_token_secret);
      $cb->setTimeout(180 * 1000); // 60 second request timeout

      // *************** start video *************//

        $file = $file_path;
        $size_bytes = filesize($file);
        $fp = fopen($file, 'r');

        // INIT the upload

        $reply = $cb->media_upload([
            'command'     => 'INIT',
            'media_type'  => 'video/mp4',
            'total_bytes' => (int)$size_bytes,
            'media_category' => 'tweet_video'
        ]);

        if (!isset($reply->media_id_string)) {
            return $reply;
        }


        $media_id = $reply->media_id_string;
        // APPEND data to the upload
        $segment_id = 0;
        while (! feof($fp)) {
            $chunk = fread($fp, 5242880); // 5MB per chunk for this sample

            $reply = $cb->media_upload([
                'command'       => 'APPEND',
                'media_id'      => $media_id,
                'segment_index' => $segment_id++,
                'media_data' => base64_encode($chunk)
            ]);
            // $segment_id++;
        }
        fclose($fp);
        // FINALIZE the upload
        $reply = $cb->media_upload([
          'command'       => 'FINALIZE',
          'media_id'      => $media_id
        ]);


        $finalize_info = (array)$reply;
        $progress_info = (array)$finalize_info['processing_info'];

        if($progress_info['state'] == 'pending') {
            sleep($progress_info['check_after_secs']);
        }

        $reply = $cb->media_upload([
          'command'       => 'STATUS',
          'media_id'      => $media_id
        ]);

        $status_info = (array)$reply;
        $progress_info = (array)$status_info['processing_info'];

        if(isset($progress_info['error']) || $progress_info['state']=='failed')
        {
          $errors = (array)$progress_info['error'];
          return json_encode(array('error'=>$errors['message']));
        }

        if($progress_info['state']=='in_progress' || $progress_info['state']=='pending')
        {
          $max_wait_time = 0;
          while($progress_info['progress_percent'] != 100){

            $reply = $cb->media_upload([
              'command'       => 'STATUS',
              'media_id'      => $media_id
            ]);

            $status_info = (array)$reply;
            $progress_info = (array)$status_info['processing_info'];

            if(isset($progress_info['error'])) break;

            if(isset($progress_info['check_after_secs'])) {
                sleep($progress_info['check_after_secs']);
                $max_wait_time += $progress_info['check_after_secs'];
                // sleep(5);
            }

            // $max_wait_time += 5;

            if($max_wait_time >= 200) break;
          }
          
          if(isset($progress_info['error']) || $progress_info['state']=='failed')
          {
            $errors = (array)$progress_info['error'];
            return json_encode(array('error'=>$errors['message']));
          }
        }

      // echo $max_wait_time;

      // Now use the media_id in a Tweet
      $reply = $cb->statuses_update([
          'status'    => $video_caption,
          'media_ids' => $media_id,
      ]);

      if (isset($reply)) {
        $result = (array)$reply;
        return json_encode(array('id'=>$result['id_str']));
      } else {
        return json_encode(array('error'=>'oppose something is wrong'));
      }
  }

  public function video_post_to_twitter_temp($oauth_token, $oauth_token_secret, $file_path, $video_caption = '')
  {
      $auth_token = $oauth_token;
      $oauth_token_secret = $oauth_token_secret;

      \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
      $cb = \Codebird\Codebird::getInstance();

      // assign access token on each page load
      $cb->setToken($auth_token, $oauth_token_secret);
      $cb->setTimeout(180 * 1000); // 60 second request timeout

      // *************** start video *************//

        $file = $file_path;
        $size_bytes = filesize($file);
        $fp = fopen($file, 'r');

        // INIT the upload

        $reply = $cb->media_upload([
            'command'     => 'INIT',
            'media_type'  => 'video/mp4',
            'total_bytes' => (int)$size_bytes,
            'media_category' => 'tweet_video'
        ]);
        if (!isset($reply->media_id_string)) {
            return $reply;
        }

        $media_id = $reply->media_id_string;
        // APPEND data to the upload
        $segment_id = 0;
        while (! feof($fp)) {
            $chunk = fread($fp, 1048576); // 1MB per chunk for this sample
            $reply = $cb->media_upload([
                'command'       => 'APPEND',
                'media_id'      => $media_id,
                'segment_index' => $segment_id++,
                'media_data' => base64_encode($chunk)
            ]);
            // $segment_id++;
        }
        fclose($fp);
        // FINALIZE the upload
        $reply = $cb->media_upload([
          'command'       => 'FINALIZE',
          'media_id'      => $media_id
        ]);

        $finalize_info = (array)$reply;
        $progress_info = (array)$finalize_info['processing_info'];
        if($progress_info['state'] == 'pending')
        sleep($progress_info['check_after_secs']);

        $reply = $cb->media_upload([
          'command'       => 'STATUS',
          'media_id'      => $media_id
        ]);
        $status_info = (array)$reply;
        $progress_info = (array)$status_info['processing_info'];

        if(isset($progress_info['error']) || $progress_info['state']=='failed')
        {
          $errors = (array)$progress_info['error'];
          return json_encode(array('error'=>$errors['message']));
        }

        if($progress_info['state']=='in_progress')
        {
          $max_wait_time = 0;
          while($progress_info['progress_percent'] != 100){
            $reply = $cb->media_upload([
              'command'       => 'STATUS',
              'media_id'      => $media_id
            ]);
            $status_info = (array)$reply;
            $progress_info = (array)$status_info['processing_info'];
            if(isset($progress_info['error'])) break;
            if(isset($progress_info['check_after_secs'])) sleep($progress_info['check_after_secs']);
            $max_wait_time += $progress_info['check_after_secs'];
            if($max_wait_time >= 60) break;
          }
          if(isset($progress_info['error']) || $progress_info['state']=='failed')
          {
            $errors = (array)$progress_info['error'];
            return json_encode(array('error'=>$errors['message']));
          }
        }

      echo $max_wait_time;

      // Now use the media_id in a Tweet
      $reply = $cb->statuses_update([
          'status'    => $video_caption,
          'media_ids' => $media_id,
      ]);

      if (isset($reply)) {
        $result = (array)$reply;
        return json_encode(array('id'=>$result['id_str']));
      } else {
        return json_encode(array('error'=>'oppose something is wrong'));
      }
  }

  public function old_video_post_to_twitter($oauth_token, $oauth_token_secret, $file_path, $video_caption = '')
  {
      $auth_token = $oauth_token;
      $oauth_token_secret = $oauth_token_secret;

      \Codebird\Codebird::setConsumerKey($this->consumer_key, $this->consumer_secret);
      $cb = \Codebird\Codebird::getInstance();

      // assign access token on each page load
      $cb->setToken($auth_token, $oauth_token_secret);

      // *************** start video *************//

      $file = $file_path;
      $size_bytes = filesize($file);
      $fp = fopen($file, 'r');
      // INIT the upload
      $reply = $cb->media_upload([
         'command'     => 'INIT',
         'media_type'  => 'video/mp4',
         'total_bytes' => $size_bytes,
      ]);

      if (!isset($reply->media_id_string)) {
          return $reply;
      }
      $media_id = $reply->media_id_string;

      // APPEND data to the upload
      $segment_id = 0;
      while (!feof($fp)) {
          $chunk = fread($fp, 1048576); // 1MB per chunk for this sample
        $reply = $cb->media_upload([
          'command'       => 'APPEND',
          'media_id'      => $media_id,
          'segment_index' => $segment_id,
          'media'         => $chunk,
        ]);
          $segment_id++;
      }
      fclose($fp);
      // FINALIZE the upload
      $reply = $cb->media_upload([
        'command'       => 'FINALIZE',
        'media_id'      => $media_id,
      ]);
      // var_dump($reply);
      if ($reply->httpstatus < 200 || $reply->httpstatus > 299) {
          die();
      }

      // Now use the media_id in a Tweet
      $reply = $cb->statuses_update([
          'status'    => $video_caption,
          'media_ids' => $media_id,
      ]);

      if (isset($reply)) {
          return $reply;
      } else {
          return 'oppose something is wrong';
      }
  }
}
