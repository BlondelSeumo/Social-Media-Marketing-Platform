<?php
require_once 'twilio/Twilio/autoload.php';
use Twilio\Rest\Client;

class Sms_manager{

    protected $user;
    protected $password;
    protected $recepients=array();
	protected $api_id; /**This is for clickatell gateway**/
	protected $phone_numbner;
	public $gateway_name;
	protected $gateway_id;
	protected $user_id;
	protected $finalized_http_url; 
	protected $post_base_url; 
	protected $post_data; 

	
	
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->CI->load->config("my_config");
		$this->user_id=$this->CI->session->userdata("user_id");
		ignore_user_abort(TRUE);
		
	}	
    // ========private methods=================
    private function run_curl($url)
    {
        $ch = curl_init();
        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        // grab URL and pass it to the browser
        $response=curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);
		return $response;	
    }



    public function run_curl_post($url,$post_fields){

	    $ch=curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
		$response = curl_exec( $ch );
		curl_close($ch);
		return $response;	
    }

    public function set_credentioal($id,$user_id) /**$api_id is for clickatell gateway***/
    {
		$where = array('where'=>array('id'=>$id));
		$results = $this->CI->basic->get_data("sms_api_config",$where);

		if(count($results)==0) return false;	

		foreach($results as $info) {

			$gateway=$info['gateway_name'];
			$auth_id=$info['username_auth_id'];
			$token = $info['password_auth_token'];
			$routeHostname = $info['routesms_host_name'];
			$phone_number=$info['phone_number'];
			$api_id = $info['api_id'];
			$gateway_id = $info['id'];
			$finalized_http_url = $info['final_http_url'];
			$post_base_url= $info['base_url'];
			$post_data=$info['post_data'];
		}
		
		$this->userid = $user_id;
        $this->user   = $auth_id;
        $this->password = $token;
        $this->routesms_host_name = $routeHostname;
		$this->api_id = $api_id;
		$this->gateway_id = $gateway_id;
		$this->gateway_name = $gateway;
		$this->phone_number = $phone_number;
		$this->finalized_http_url = $finalized_http_url;
		$this->post_base_url=$post_base_url;
		$this->post_data=$post_data;
    }

    public function send_sms($msg, $recepient)
    {
    	$msg=html_entity_decode($msg);

		 if(!is_array($recepient)){
            	$recepient = array($recepient);
        }
		
		/****Initialize Message id as empty at first ****/
		$message_id="";
		$in_user_id = $this->userid;
		$gateway_id=$this->gateway_id;
		$gateway=$this->gateway_name;
		$hostname = $this->routesms_host_name;
		if(substr($hostname, -1) == '/')
			$hostname = substr($hostname, 0, -1);
		$message_info=array();
		
		/****** Planet IT SMS Manager ******/
		if($gateway=='planet')
		{
			try
			{
				$msg=urlencode($msg);
		        $str_recepient = implode(',',$recepient);
		        $mask= urlencode($this->phone_number);
			    $api_url="http://app.planetgroupbd.com/api/sendsms/plain?user={$this->user}&password={$this->password}&sender={$mask}&SMSText={$msg}&GSM={$str_recepient}";
		        $message_info['id'] = $this->run_curl($api_url);
		    }
		    catch (Exception $e) 
			{
				$message_info['id'] ="";
				$message_info['status']='error in config';
				return $message_info;
			}
		}
		/***Plivo sms sending option****/
		// if($gateway=='plivo')
		// {	
		// 	foreach($recepient as $to_number)
		// 	{
		// 		$message_info = $this->plivo_sms_send($this->phone_number,$to_number,$msg);
		// 	}
		// }
		else if($gateway=='plivo')
		{	
			foreach($recepient as $to_number)
			{
				$msg=html_entity_decode($msg);
				$message_info = $this->plivo_sms_send($this->phone_number,$to_number,$msg);
			}
		}
		/***Twilio sms sending option**/
		else if($gateway=='twilio')
		{
			foreach($recepient as $to_number)
			{
				$message_info = $this->twilio_sms_sent($this->phone_number,$to_number,$msg);
			}
		}
		/***2-way sms sending option****/
		/***not used ****/
		else if($gateway=='2-way')
		{
			foreach($recepient as $to_number)
			{
				$message_info=$this->send_sms_2way($to_number,$msg);
			}
		}
		/**** Clickatell sending option *****/
		else if($gateway=='clickatell'){
				$msg=urlencode($msg);	
			 	$message_info	= $this->clickatell_send_sms($recepient,$msg);
		}
		else if($gateway=='clickatell-platform'){
				$msg=urlencode($msg);	
			 	$message_info = $this->clickatell_platform_send_sms($recepient,$msg);
		}
		else if($gateway=='nexmo')
		{
			$msg=urlencode($msg);
			
			foreach($recepient as $to_number)
			{
				$message_info	= $this->nexmo_send_sms($this->phone_number,$to_number,$msg);
			}
		}
		else if($gateway=='msg91.com')
		{
			$msg=urlencode($msg);			
			foreach($recepient as $to_number)
			{
				$message_info	= $this->msg91_send_sms($this->phone_number,$to_number,$msg);
			}
		}
		else if($gateway == 'semysms.net')
		{
			foreach($recepient as $to_number)
			{
				$message_info	= $this->semysms_send_sms($to_number,$msg);
			}
		}
		else if($gateway=='routesms.com')
		{				
			foreach($recepient as $to_number)
			{
				$message_info	= $this->send_sms_route($this->phone_number,$to_number,$msg,$hostname);
			}
		}
		else if($gateway=='textlocal.in')
		{	
			foreach($recepient as $to_number)
			{
				$message_info	= $this->textlocal_in($this->phone_number,$to_number,$msg);
			}
		}
		else if($gateway=='sms4connect.com')
		{	
			foreach($recepient as $to_number)
			{
				$message_info	= $this->sms_4_connect($this->phone_number,$to_number,$msg);
			}
		}
		else if($gateway=='mvaayoo.com')
		{	
			foreach($recepient as $to_number)
			{
				$message_info	= $this->mvaayoo_send_sms($this->phone_number,$to_number,$msg);
			}
		}
		else if($gateway=='telnor.com')
		{	
			$auth_response_telnor=$this->telnor_session_id();
			$session_id=$auth_response_telnor['session_id'];
			foreach($recepient as $to_number)
			{
				$message_info	= $this->telnor_send_sms($session_id,$this->phone_number,$to_number,$msg);
			}	
		}
		else if($gateway=='trio-mobile.com')
		{	
			foreach($recepient as $to_number)
			{
				$message_info	= $this->cloudsm_trio_mobile_send_sms($this->phone_number,$to_number,$msg);
			}
		}
		else if($gateway == 'sms40.com')
		{
			foreach($recepient as $to_number)
			{
				$message_info	= $this->send_sms_by_sms40($this->phone_number,$to_number,$msg);
			}
		}
		else if($gateway == 'africastalking.com')
		{
			foreach($recepient as $to_number)
			{
				$message_info	= $this->africastalking_send_sms($to_number,$msg);
			}
		}
		else if($gateway == 'infobip.com')
		{
			foreach($recepient as $to_number)
			{
				$message_info	= $this->infobip_send_sms($to_number,$msg);
			}
		}
		else if($gateway == 'smsgatewayme')
		{
			foreach($recepient as $to_number)
			{
				$message_info	= $this->smsgatewayme_send_sms($to_number,$msg);
			}
		}
		else if($gateway == 'Shreeweb')
		{
			foreach($recepient as $to_number)
			{
				$message_info	= $this->shreeweb_send_sms($to_number,$msg);
			}
		} else if ($gateway == 'custom'){


			try {

				$msg = urlencode($msg);
		        $str_recepient = implode(',',$recepient);
		        

				$this->finalized_http_url = str_replace('#MESSAGE_CONTENT#', $msg, $this->finalized_http_url);
				$this->finalized_http_url = str_replace('#DESTINATION_NUMBER#', $str_recepient, $this->finalized_http_url);

		        $message_info['id'] = $this->run_curl($this->finalized_http_url); 

		        if($message_info['id'] !='')
					$message_info['status'] = "Submitted";
				else
					$message_info['status'] = "Something is wrong";

		    } catch (Exception $e) {

				$message_info['id'] ="";
				$message_info['status'] = $e->getMessage();

				return $message_info;
			}
		}

		else if ($gateway=='custom_post'){

			try {
				$msg = urlencode($msg);
		        $str_recepient = implode(',',$recepient);
		       
		        $post_data_array = json_decode($this->post_data,true);
		        
		        $str="";
		        foreach ($post_data_array as $p_data) {
		          	
		         	if($p_data['type'] == "fixed")
		         		$str.= "{$p_data['key']}=". $p_data['value']."&";

		         	else if($p_data['type'] == "destination_number")
		         		$str.= "{$p_data['key']}=". $str_recepient."&";

		         	else if($p_data['type'] == "message_content")
		         		$str.= "{$p_data['key']}=". $msg."&";	

		        }
		       
		       $str = rtrim($str,'&');
		       $message_info['id'] = $this->run_curl_post($this->post_base_url,$str);

		        if($message_info['id'] !='')
					$message_info['status'] = "Submitted";
				else
					$message_info['status'] = "Something is wrong in configuration or destination number";

		    } catch (Exception $e) {

				$message_info['id'] ="";
				$message_info['status'] = $e->getMessage();

				return $message_info;
			}




		}
		
		/****Insert sms_history into database ****/
		// $user_id=$this->user_id;
		// $recepient_str=implode($recepient);
		// $time=date('Y-m-d H:i:s');
		
		// $message_id="";
		// if(isset($message_info['id']))$message_id=$message_info['id'];

		// if(is_array($message_id)) $message_id=implode(',',$message_id);
		
		// $message_status="";
		// if(isset($message_info['status'])) {
		// 	$message_status = $message_info['status'];
		// }

		// $in_data = array(
		// 	'user_id' => $in_user_id,
		// 	'gateway_id' => $gateway_id,
		// 	'to_number' =>$recepient_str ,
		// 	'sms_uid' => $message_id,
		// 	'sms_status' => $message_status,
		// 	'sent_time' => $time,
		// 	'message' => $msg,
		// );		
		
		// $this->CI->basic->insert_data("sms_history",$in_data);

		return $message_info;

    }
	
	
	
	/***This Api return response like that 
	
	{
    "message": "message(s) queued",
    "message_uuid": ["db3ce55a-7f1d-11e1-8ea7-1231380bc196"],
    "api_id": "db342550-7f1d-11e1-8ea7-1231380bc196"
	}
	
	For error : 
	
	{ "api_id": "768a6c29-ae31-11e5-8a51-22000acb8c2c", "error": "Insufficient credit" } 

	***/
	
	public function plivo_sms_send($src,$dst,$text)
	{

		$dst=ltrim($dst, '+');

		# Plivo AUTH ID
		$AUTH_ID = $this->user;
		# Plivo AUTH TOKEN
		$AUTH_TOKEN = $this->password;

		 
		
		try
		{
			$url = 'https://api.plivo.com/v1/Account/'.$AUTH_ID.'/Message/';
			$data = array("src" => "$src", "dst" => "$dst", "text" => "$text");
			$data_string = json_encode($data);
			$ch=curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_HEADER, 0);  
	    	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	     	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	     	curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
	    	curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");  
	     	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	     	curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
			curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$response = curl_exec( $ch );
			curl_close($ch);
			$response=json_decode($response,TRUE);
			
			if(isset($response['message_uuid']))
			{
				$message_info['id']=$response['message_uuid'][0];
				$message_info['status']=$response['message'];
			}
			
			else
			{
				$message_info['id']="";
				$message_info['status']=$response['error'];
			}			
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	}
	
	
	/**** Get plivo balance *******/
	public function get_plivo_balance(){
	
		# Plivo AUTH ID
		$AUTH_ID = $this->user;
		# Plivo AUTH TOKEN
		$AUTH_TOKEN = $this->password;
		
		$url = "https://api.plivo.com/v1/Account/{$AUTH_ID}/";
		$ch=curl_init($url);
	    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	    curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$response = curl_exec( $ch );
		curl_close($ch);
		$response=json_decode($response,TRUE);
		$balance=isset($response['cash_credits']) ? $response['cash_credits'] : 0;
		return $balance;
	}
	/***	
			Array
				(
				    [id] => 
				    [status] => "Queued/Bad Credentials"
				)  
				
			This api return response as string now. 
				
	****/
	public function twilio_sms_sent($from,$to,$text)
	{

		if($to[0]!="+") $to="+".$to;
		// set your AccountSid and AuthToken from www.twilio.com/user/account		
		try
		{

			$twilio = new Client($this->user, $this->password);

			$message = $twilio->messages
			                  ->create($to, // to
			                           array("from" => $from, "body" => $text)
			                  );




			$message_info['id']=$message->sid;
			$message_info['status']=$message->status;
					
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']=$e->getMessage();
			return $message_info;
		}
	}
	 
	 // not used
	public function send_sms_2way($to,$text)
	{	 	
		$api_code=$this->api_id;
		$token=$this->password; 
		try
		{
			$url = "http://www.proovl.com/api/{$api_code}/send.php";
			$postfields = 
				array
				(
					'token' => "$token",
					'to' => "$to",
					'text' => "$text"
				);
		
			if(!$curld = curl_init()) 
			{
				exit;
			}
			curl_setopt($curld, CURLOPT_POST, true);
			curl_setopt($curld, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($curld, CURLOPT_URL,$url);
			curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
					
			$output = curl_exec($curld);
			curl_close ($curld);
			
			$result = explode(';',$output);

			if ($result[0] == "Error") 
			{
				$message_info['id']="";
				$message_info['status']=$result[1];
			} 
			else 
			{
				if ($result[2] == $token) 
				{
					$message_info['id']=$result[1];
					$message_info['status']=$result[0];
				} 
				else 
				{
					$message_info['id']="";
					$message_info['status']="Incorrect token";
				}
			}				
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	}
	
	/*** Pass the $to_numbers as array. Clickatell will send more numbers at a time with comma separated 
		For single number return the response like this 
			
			Array
				(
				    [id] => 
				    [status] => "sent/Bad Credentials"
				)  
				
				This api return response as string now. 
				
	****/
	
	function clickatell_send_sms($to_numbers,$msg)
	{
		try
		{
			/***** $to_numbers converted to array then implode it by commaseparated ****/
			if(!is_array($to_numbers))
			{
	            $to_numbers = array($to_numbers);
	        }

	       	for($i=0;$i<count($to_numbers);$i++)
	       	{
	       		$to_numbers[$i]=ltrim($to_numbers[$i], '+');
	       		$to_numbers[$i]=ltrim($to_numbers[$i], '0');
	       	}
			$to_numbers=implode(",",$to_numbers);	
			 $url="http://api.clickatell.com/http/sendmsg?user={$this->user}&password={$this->password}&api_id={$this->api_id}&to={$to_numbers}&text={$msg}";
			
			// for us only
			// $url="https://api.clickatell.com/http/sendmsg?user={$this->user}&password={$this->password}&api_id={$this->api_id}&mo=1&from={$this->phone_number}&to={$to_numbers}&text={$msg}";

			$response = $this->run_curl($url);
			$id_pos = strpos($response, "ID:");		
			if($id_pos=== FALSE)
			{
				/** If no ID: is found then means error is occured **/
				$message_info['id']="";
				$message_info['status']=$response;
			}		
			else
			{
				$response=str_replace("ID:","",$response);
				$response=trim($response);
				
				$message_info['id']=$response;
				$message_info['status']="Sent";
			}		
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	}

	function clickatell_platform_send_sms($to_numbers,$msg)
	{
		try
		{
			/***** $to_numbers converted to array then implode it by commaseparated ****/
			if(!is_array($to_numbers))
			{
	            $to_numbers = array($to_numbers);
	        }
	       	for($i=0;$i<count($to_numbers);$i++)
	       	{
	       		$to_numbers[$i]=ltrim($to_numbers[$i], '+');
	       		$to_numbers[$i]=ltrim($to_numbers[$i], '0');
	       	}
			$to_numbers=implode(",",$to_numbers);	

			$url="https://platform.clickatell.com/messages/http/send?apiKey={$this->api_id}&to={$to_numbers}&content={$msg}&unicode=1";

			$response = $this->run_curl($url);
			$id_pos = strpos($response, "ID:");		
			if($id_pos=== FALSE)
			{
				$response = trim($response);
				$response_decode = json_decode($response,TRUE);
				/** If no ID: is found then means error is occured **/
				if(isset($response_decode['messages'][0]['apiMessageId']) && $response_decode['messages'][0]['accepted']== 'true' && $response_decode['messages'][0]['apiMessageId'] !='null') {

					$message_info['id'] = $response_decode['messages'][0]['apiMessageId'];
				}
				else {
					if(isset($response_decode['messages'][0]['errorDescription']) && !empty($response_decode['messages'][0]['errorDescription'])) {

						$message_info['status']= $response_decode['messages'][0]['errorDescription'];

					} else if(isset($response_decode['errorDescription']) && !empty($response_decode['errorDescription'])) {

						$message_info['status']= $response_decode['errorDescription'];
						
					} else {
						$message_info['status'] = $this->CI->lang->line('something went wrong.');
					}
				}
			}		
			else
			{
				$response=str_replace("ID:","",$response);
				$response=trim($response);
				$message_info['id']=$response;
				$message_info['status']="Sent";
			}		
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	}

	
	/****Get clickatell Balance ******/
	public function get_clickatell_balance()
	{	
		$url="https://api.clickatell.com/http/getbalance?user={$this->user}&password={$this->password}&api_id={$this->api_id}";
		$ch=curl_init($url);
	    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		$response = curl_exec( $ch );
		curl_close($ch);		
		$balance=str_replace("Credit:","",$response);
		return trim($balance);	
		
	}

	public function get_clickatell_platform_balance()
	{	

		$url="https://platform.clickatell.com/public-client/balance";
		$api_key=$this->api_id;

		$ch = curl_init();  
		$headers = array(
		    			"Content-type: application/json",
						"Authorization: {$api_key}"
					);						
					
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		 curl_setopt($ch, CURLOPT_URL, $url);
		 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		 $st=curl_exec($ch); 
		 $response=json_decode($st,TRUE);
		 return $response['balance'].$response['currency'];
	}


	
	
	
	
		/**** 
		Using Nexmo api   This function return 
			Array
				(
				    [id] => 
				    [status] => "sent/Bad Credentials"
				)  
			
		****/	
		
	function nexmo_send_sms($from,$to_number,$msg)
	{
		try
		{
			$url="https://rest.nexmo.com/sms/json?api_key={$this->user}&api_secret={$this->password}&from={$from}&to={$to_number}&text={$msg}&type=text";
			$response=$this->run_curl($url);
			$result=json_decode($response,TRUE);
			
			if(isset($result['messages'][0]['message-id']))
				$message_info['id']=$result['messages'][0]['message-id'];
			else $message_info['id']="";				
				
			if(isset($result['messages'][0]['status']) && $result['messages'][0]['status']=='0' )
			{
				$message_info['status']="Sent";
				return $message_info;
			}	
			else $message_info['status']="";							
				
			if(isset($result['messages'][0]['error-text']))	
				$message_info['status']=$result['messages'][0]['error-text'];
			else $message_info['status']="";
				
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	}

	/**** Return Balance in Account ****/
	
	function get_nexmo_balance()
	{
		$url="https://rest.nexmo.com/account/get-balance/{$this->user}/{$this->password}";
		$ch=curl_init($url);
	    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$response = curl_exec( $ch );
		curl_close($ch);
		$response=json_decode($response,TRUE);
		$balance=isset($response['value']) ? $response['value']:0;
		return $balance;
	}
	

	function msg91_send_sms($from,$to_number,$msg)
	{	
		try
		{
			$to_number=ltrim($to_number, '+');
			$url="http://api.msg91.com/api/sendhttp.php?authkey={$this->user}&mobiles={$to_number}&message={$msg}&sender={$from}&route=4&country=91";
			$result=$this->run_curl($url);

			$id="";
			$status="";

			if(strlen($result)==24)
			{
				$id=$result;
				$status="Submitted";
			}
			else
			{
				$status=$result;
			}

			$message_info['id']=$id;
			$message_info['status']=$status;
				
			return $message_info;	
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}		
		
	}
	
	
	public function textlocal_in($from,$to,$msg)
	{		
		try
		{
			// Textlocal account details
			/*$username = 'youremail@address.com';
			$hash = 'Your API hash';*/

			// Message details
			$to=ltrim($to, '+');
			$numbers = $to;
			$sender = urlencode($from);
			$message = rawurlencode($msg);
		 
			if(is_array($numbers))
			$numbers = implode(',', $numbers);
		 
			// Prepare data for POST request
			//$data = array('username' => $username, 'hash' => $hash, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
			$data = array('apiKey'=>$this->api_id, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		 
			// Send the POST request with cURL
			$ch = curl_init('http://api.textlocal.in/send/');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			
			$response=json_decode($response,true);
			
			if(isset($response['status']) && $response['status']=='success')
			{			
				$send_info=$response['messages'];
				
				$result=array("id"=>"","status"=>"Queued"); // initialized
				$i=0;
				foreach($send_info as $info)
				{
					$result[$info->recipient]['id']=$info->id;
				}
			}		
			// Process your response here
			return $result;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	
	}
	

	
	function sms_4_connect($from,$to,$msg)
	{
	
		/******		
		id		= 		A valid Account ID for the customer
		password=	A valid Account Password for the customer				
		***************/
		
		try
		{
			$msg=urlencode($msg);	
			$url="http://sms4connect.com/api/sendsms.php/sendsms/url?id={$this->user}&pass={$this->password}&mask={$from}&to={$to}&lang=English&msg={$msg}&type=json";
			
			$result=$this->run_curl($url);
			$result=json_decode($result,TRUE);
			
			if($result['corpsms'][0]['code']==300)
			{
				$message_info['id']=$result['corpsms'][0]['transactionID'];
			}
			else
			{
				$message_info['id']=0;
			}
			
			$message_info['status']=$result['corpsms'][0]['response'];
			
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}	
		
	}
		
	
	
	public function telnor_session_id()
	{
	
		try
		{
			$url="https://telenorcsms.com.pk:27677/corporate_sms2/api/auth.jsp?msisdn={$this->user}&password={$this->password}";
			$content = $this->run_curl($url);
			$xml=simplexml_load_string($content);
			
			/*** Error / Ok  as response */
			
			$response['status']=(string)$xml->response;
			$response['session_id']=(string)$xml->data;
			
			return $response;
		}
		catch (Exception $e) 
		{
			$response['session_id']="";
			$response['status']='error in config';
			return $response;
		}
	}
	
	public function telnor_send_sms($session_id,$from,$to,$msg)
	{
	
		try
		{
			$msg=urlencode($msg);	
		
			$url="https://telenorcsms.com.pk:27677/corporate_sms2/api/sendsms.jsp?session_id={$session_id}&to={$to}&text={$msg}&mask={$from}";
			
			$content = $this->run_curl($url);
			$xml=simplexml_load_string($content);
			
			/*** Error / Ok  as response */
			
			
			$status	= (string)$xml->response;		
			
			if($status=='OK')
			{
				$message_info['id']=(string)$xml->data;
				$message_info['status']=(string)$xml->response;
			
			}
			
			else{
				$message_info['id']="";
				$message_info['status']=(string)$xml->response;
			}
			
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	
	}
	
	
	
	public function mvaayoo_send_sms($from,$to,$msg)
	{
		
		/******
				$this->user		= admin 	 = info@eztechnologies.in
				$this->password	= user 		 = prachi1786@gmail.com:xxxx
				$from			= senderID   = ABSMRT
		*****/
		
		
		try
		{
			$msg=urlencode($msg);
			$url="http://59.162.167.52/api/MessageCompose?admin={$this->user}&user={$this->password}&senderID={$from}&receipientno={$to}&msgtxt={$msg}&state=4";
			$result=$this->run_curl($url);
			
			/*****		No details documentation found 	******/
			
			$message_info['id']=$result;
			$message_info['status']=$result;
			
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
		
	}
	
	
	public function cloudsm_trio_mobile_send_sms($from,$to,$msg)
	{	
		try
		{
			$msg=urlencode($msg);
			$url="http://cloudsms.trio-mobile.com/index.php/api/bulk_mt?api_key={$this->user}&action=send&to={$to}&msg={$msg}&sender_id={$from}&content_type=1&mode=longcode";

			$result=$this->run_curl($url);
			$message_info['id']=$result;
			$message_info['status']=$result;
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
		
	}
	
	public function send_sms_route($from,$to,$msg,$hostname){
		try
		{
			$msg=urlencode($msg);
			// $url="http://smsplus.routesms.com:8080/bulksms/bulksms?username={$this->user}&password={$this->password}&type=0&dlr=1&destination={$to}&source={$from}&message={$msg}";
			$url = $hostname."/bulksms/bulksms?username={$this->user}&password={$this->password}&type=0&dlr=1&destination={$to}&source={$from}&message={$msg}";
			$result = $this->run_curl($url);
			$result_array = explode('|',$result);
			$sms_id="";
			$status="failed";
			if(count($result_array)>0)
			{
				if($result_array[0]=="1701") 
				{
					$status="success";
					$sms_id = $result_array[2];
				} else
				{
					$code_meaning = array("1702" => "One of the parameter is missing","1703" => "User authentication has failed","1704" => "Invalid message type","1705" => "Invalid message","1706" => "Invalid destination","1707" => "Invalid source (Sender ID)","1710" => "Unknown error","1712" => "Bad DB connection","1713"=>"Too many destinations","1813"=>"Error while creating job file for the scheduled job","1901"=>"Invalid XML content","1902"=>"Bad schedule date","1904"=>"Bad schedule time","1903" => "Invalid GMT","1905"=> "Invalid date and time");
					$status = $result_array[0]." | ".$code_meaning[$result_array[0]];
				}
			}
			$message_info['id'] = $sms_id;
			$message_info['status'] = $status;
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	}

	public function send_sms_by_sms40($from,$to,$msg)
	{
		try
		{
			$msg=urlencode($msg);
			$url="http://www.sms40.com/api2.php?username={$this->user}&password={$this->password}&type=SendSMS&sender={$from}&mobile={$to}&message={$msg}";
			$result=$this->run_curl($url);
			$message_info['id']="";
			$message_info['status']=$result;
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	}
	

	/**********  {"SMSMessageData":{"Message":"Sent to 1/1 Total Cost: BDT 1.5673","Recipients":[{"number":"+8801722977459","status":"Success","cost":"USD 0.0200","messageId":"ATXid_c54d5e335a8eb83f707a4b9caddd0dbb"}]}}   ***********/

	function  africastalking_send_sms($to,$msg)
	{	
		$api_key=$this->user;
		$username=$this->phone_number;
		try
		{
			$url = 'http://api.sandbox.africastalking.com/version1/messaging';
			$data = array("username" =>$username, "message" =>$msg, "to" => $to);
			
			$ch=curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_HEADER, 0);  
		    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		    curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
		    curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");  
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0'); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json","Apikey:{$api_key}"));
			$response1 = curl_exec( $ch );
			curl_close($ch);
			$response=json_decode($response1,TRUE);
			$message_info['id']=isset($response['SMSMessageData']['Recipients'][0]['messageId']) ? $response['SMSMessageData']['Recipients'][0]['messageId'] : "";

			$message_info['status']=isset($response['SMSMessageData']['Recipients'][0]['status'])? $response['SMSMessageData']['Recipients'][0]['status']: $response1;

			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
		
	}
	
	
	public function africastalking_sms_balance(){
	
		$api_key=$this->user;
		$username=$this->phone_number;
		 
		$url = "https://api.africastalking.com/version1/user?username={$username}";
		$ch=curl_init($url);
	    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json","Apikey:{$api_key}"));
		$response = curl_exec( $ch );
		curl_close($ch);
		$response=json_decode($response,TRUE);
		
		$balance= isset($response['UserData']['balance'])? $response['UserData']['balance']:0;
		
		return $balance;
		
	}
	
	
	
	
	
	public function infobip_send_sms($to,$msg)
	{
		
		try
		{
			$url="https://api.infobip.com/sms/1/text/single";
			$user=$this->user;
			$password=$this->password;
			$from=$this->phone_number;

			$user_pass="{$user}:$password";
			$authorization=base64_encode($user_pass);
			$headers=array('Content-Type: application/json',
							'Accept: application/json',
							'Authorization: Basic '.$authorization);
			$post_fields=array("from"=>$from,"to"=>$to,"text"=>$msg);
			
			$post_fields=json_encode($post_fields);	
			
			$ch=curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);    
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
			curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
			$response = curl_exec( $ch );
			$response=json_decode($response,TRUE);
			
			if(isset($response['requestError'])){
				$message_info['id']="";
				$message_info['status']="Error";
				return $message_info;
			}		
				
			$message_info['id']	=isset($response['messages'][0]['messageId']) ? $response['messages'][0]['messageId'] : "";
			$message_info['status']=isset($response['messages'][0]['status']['description'])?$response['messages'][0]['status']['description'] : "";
			
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
							
	}
		
		
		
		
	public function infobip_balance_check()
	{		
		try
		{
			$url="https://api.infobip.com/account/1/balance";
		
			$user=$this->user;
			$password=$this->password;

			$user_pass="{$user}:$password";
			$authorization=base64_encode($user_pass);
			$headers=array('Content-Type: application/json',
							'Accept: application/json',
							'Authorization: Basic '.$authorization);
			
			$response['balance']="";
			$response['currency']="";
			
			$ch=curl_init($url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0'); 		
			curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
			$response = curl_exec( $ch );
			$response=json_decode($response,TRUE);
			$balance= $response['balance']." ".$response['currency'];	
			return $balance;	
		}
		catch (Exception $e) 
		{
			return "0";
		}	
		
	}
	
	
	
	
	// public function smsgateway_device_id()
	// {
	
	// 	$url="http://smsgateway.me/api/v3/devices";
	// 	$user=$this->user;
	// 	$password=$this->password;
	// 	$post_fields=array("email"=>$user,"password"=>$password,);
	// 	$ch=curl_init($url);
	// 	curl_setopt($ch, CURLOPT_POST, true);
	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);    
	// 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	// 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  		$response = curl_exec( $ch );
	// 	$response=json_decode($response,TRUE);
	// 	$device_id= isset($response['result']['data'][0]['id'])?$response['result']['data'][0]['id']:"";
	// 	return $device_id;
	// }
	
	
	public function smsgatewayme_send_sms($to,$msg)
	{
		try
		{
			$api_token=$this->password;
			$device_id=$this->api_id;
			$url="https://smsgateway.me/api/v4/message/send";
			$headers = array("Content-type: application/json","Authorization: ".$api_token);
			$postarray=	array(0=>array("phone_number"=>$to,"message"=>$msg,"device_id"=>$device_id));
			$post_fields=json_encode($postarray);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
			// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
			curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
			curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0'); 
			$response=curl_exec($ch); 
			$response=json_decode($response,TRUE);

			if(isset($response[0]['status'])) $message_info['status']=$response[0]['status'];				
			else $message_info['status']="fail";			
			
			$message_info['id']	=isset($response[0]['id']) ? $response[0]['id']:"";
			
			return $message_info;		
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	
	}
	
	
	
	public function semysms_send_sms($to,$msg)
	{	
		try
		{
			$msg=urlencode($msg);
			$token=$this->password;
			$device_id=$this->api_id;			
			$url="https://semysms.net/api/3/sms.php?token={$token}&device={$device_id}&phone={$to}&msg={$msg}";
			$result=$this->run_curl($url);
			$result=json_decode($result,TRUE);
			$message_info['id']=isset($result['id'])?$result['id']:"";
			$message_info['status']="Request Sent";
			return $message_info;
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']='error in config';
			return $message_info;
		}
	}
	
	
	public function shreeweb_send_sms($to,$msg){
		try{
			$msg=urlencode($msg);
			$username=$this->user;
        	$password=$this->password;
			$from=$this->phone_number;
			
			 $url="http://sms.shreeweb.com/sendsms/sendsms.php?username={$username}&password={$password}&type=UNICODE&mobile={$to}&sender={$from}&message={$msg}";
			$result=$this->run_curl($url);
			
			$message_info['id']=$result;
			$message_info['status']= $result;
			return $message_info;
			
			
		}
		catch (Exception $e) 
		{
			$message_info['id']="";
			$message_info['status']= $e->getMessage();
			return $message_info;
		}
	
		
	}


	public function get_shreeweb_balance()
	{
		try
		{
			$url="http://sms.shreeweb.com/sendsms/checkbalance.php?username={$this->user}&password={$this->password}";			 
			$result=$this->run_curl($url);
			return $result;
		}
		catch (Exception $e) 
		{
			return "";
		}
	}
	
	
	
	
}



?>