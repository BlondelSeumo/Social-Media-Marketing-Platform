<?php
require_once 'Mandrill.php';

class Email_manager{
	
	public $user_id;
	public $smtp_user;
	public $smtp_host;
	public $smtp_port;
	public $smtp_password;
	public $send_email_address; 
	
	public $mandrill_api_key; 
	public $mandrill_name; 
	
	
	public $sendgrid_username;
	public $sendgrid_password;
	public $sendgrid_from_email;
	
	
	public $mailgun_api_key;
	public $mailgun_domain;
	public $mailgun_reply_to;
	
	
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library('session');
		$this->user_id=$this->CI->session->userdata("user_id");
	}	
	
	function send_madrill_email($from_email,$from_name,$to_emails,$subject,$message,$api_id,$attachment_file='',$filename='')
	{	
		try
		{	
		 	$mandrill = new Mandrill($api_id);

			 $message = array(
			                'subject' => $subject,
			                'html' =>$message, // or just use 'html' to support HTMl markup
			                'from_email' => $from_email,
			                'from_name' => $from_name, //optional
						);


				$send_email_list=array();		
				if(!is_array($to_emails)){
					$to_emails=array($to_emails);
				}
				
				foreach($to_emails as $emails){
					$send_email_list[]['email']=$emails;
					/***We can insert name and type option also**/
				}
				
				$message['to']=$send_email_list;
				
				
				/*** If attachement is available ***/
				if($attachment_file){
					$attachment = file_get_contents($attachment_file);
					$attachment_encoded = base64_encode($attachment); 
					
					$message['attachments']= array(array(
			            'content' => $attachment_encoded,
			            'name' => $filename
					   )
			        );
				}
				
			    $results = $mandrill->messages->send($message);
			   
			    foreach($results as $result){
			   		$email=$result['email'];
					$email_status_info[$email]['status']=$result['status'];
					$email_status_info[$email]['id']=$result['_id'];
					$email_status_info[$email]['reject_reason']=$result['reject_reason'];
			    }

			    return $email_status_info;
		}
		catch(Mandrill_Error $e) 
		{
			$err['error'] = $e->getMessage();
			return $err;
			
		}
		
		   
	}
	
	
	/******This api return just one response like $email_status_info['status']= "success/error" ******/
	public function sendgrid_email_send($from_email,$to,$subject,$message_body,$attachment='',$fileName='')
	{	
		$url = 'https://sendgrid.com/';	
		
		if(!is_array($to)){
			$to=array($to);
		}
		
		$json_string = array(
				  'to' => $to,
				  'category' => 'test_category'
				);
	
		$params = array(
		    'api_user'  => $this->sendgrid_username,
		    'api_key'   => $this->sendgrid_password,
		    'to'        => "example@example.com",
			'x-smtpapi' => json_encode($json_string),
		    'subject'   => $subject,
		    'html'      => $message_body,
		    'from'      => $from_email,
	  	);
		 
		if($attachment && file_exists($attachment))
		{
			$attachment = file_get_contents($attachment,true);
			$params['files['.$fileName.']'] = $attachment;
		}

		try
		{
			$request =  $url.'api/mail.send.json';
			$session = curl_init($request);
			curl_setopt ($session, CURLOPT_POST, true);
			curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($session);

			curl_close($session);

			/* Success Response: 
			 	{
			   		"message": "success"
			  	} 

			  	Error Response: 
			  	{
			  	  "message": "error",
			  	  "errors": [
			  	    "...error messages..."
			  	  ]
			  	}
			*/

			$response=json_decode($response,TRUE);


			if($response['message'] == 'error') {
				$email_status_info['status'] = 'Error: '.$response['errors'][0];
			} else {
				$email_status_info['status']= $response['message'];
			}

			return $email_status_info;
		}
		catch(Exception $e) {
			
			$email_status_info['status'] = $e->getMessage();
			return $email_status_info;
        }

	}
	
	
	
	
/***** This api gives response as Array ( [status] => error )   or Array ( [status] => message queued, thank you [id] => <2020202002> ) *****/

	public function mailgun_email_send($from_email, $to , $subject, $message_body, $attachement="")
	{	
		if(!is_array($to))	{
			$to=array($to);
		}
		
		$to_email=array_pop($to);
		
		$bcc=array();
		if(!empty($to)){
			$bcc=implode(",",$to);
		}
		
		$config = array();
		$config['api_key'] = $this->mailgun_api_key;
		$config['api_url'] = "https://api.mailgun.net/v3/{$this->mailgun_domain}/messages";
		$message = array();
		$message['from'] = $from_email;
		$message['to'] = $to_email;
		if(!empty($bcc))
		$message['bcc']=$bcc;
		$message['h:Reply-To'] =$this->mailgun_reply_to;
		$message['subject'] = $subject;
		//$message['html'] = file_get_contents("http://www.domain.com/email/html");
		$message['html'] = $message_body;

		if($attachement && file_exists($attachement)) {
			$message['attachment[1]'] = curl_file_create($attachement);   /** Here need to pass full path ***/		
		}


		try
		{
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $config['api_url']); 
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
			curl_setopt($ch, CURLOPT_USERPWD, "api:{$config['api_key']}"); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
			curl_setopt($ch, CURLOPT_POST, true);  
			curl_setopt($ch, CURLOPT_POSTFIELDS,$message); 
			$result = curl_exec($ch);
			curl_close($ch); 
			$result=json_decode($result,TRUE);

			/*
			Sample Response
			{
			  "id": "<20111114174239.25659.5817@samples.mailgun.org>"
			  "message": "Queued. Thank you.",
			}
			*/

			if(!$result) {
				$email_status_info['status'] = "error";			
			}
			else {

				$email_status_info['id'] = isset($result['id']) ? $result['id']: "";
				$email_status_info['status'] = $result['message'];
			}
			
			return $email_status_info;
		}
		catch(Exception $e) 
        {
           $email_status_info['status'] = $e->getMessage();
           return $email_status_info;
        }
	
		
	}
	

}