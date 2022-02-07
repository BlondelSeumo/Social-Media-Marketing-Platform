<?php 
	
	/**
	 * Telegram Library 
	 */
	class Telegram
	{
	    public $link;
	    public $server_link;
	    public $webhook_link;
	    public $updates;
	    public $name;
	    public $chatId;
	    public $text;
	    public $callback_data;
	    public $callback_id;
	    public $callback_from_id;


	    public function __construct()
	    {
	       $this->CI =& get_instance();
	    }

	    public function set_webhook()
	    {
	    	$this->webhook_link = $this->link."/setWebhook?url=".$this->server_link;
	    	$curl = curl_init();
	    	curl_setopt_array($curl, array(
	    		CURLOPT_URL => $this->webhook_link,
	    		CURLOPT_RETURNTRANSFER => true,
	    		CURLOPT_ENCODING => '',
	    		CURLOPT_MAXREDIRS => 10,
	    		CURLOPT_TIMEOUT => 0,
	    		CURLOPT_FOLLOWLOCATION => true,
	    		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    		CURLOPT_CUSTOMREQUEST => 'POST',
	    	));

	    	$response = curl_exec($curl);

	    	curl_close($curl);
	    	// echo  $response;
	    }

	    public function receive_data()
	    {
	    	$this->updates = '{"update_id":357554157,"message":{"message_id":1104,"from":{"id":1472827016,"is_bot":false,"first_name":"Md.","last_name":"Ronok","language_code":"en"},"chat":{"id":1472827016,"first_name":"Md.","last_name":"Ronok","type":"private"},"date":1614409170,"text":"audio"}}';
	    	$this->updates = json_decode( $this->updates, TRUE ); 
	    	return $this->updates;
	    }

	    public function reply_message()
	    {
	    	
	    	$data_text = $this->text;
	   	
	    	switch (strtolower($data_text)) 
	    	{

	    		case "start":
	    		$keyboard = [
	    			'keyboard' => [
	    				[
	    					"This is home button\ud83d\ude08",
	    				],
	    				[
	    					"This is currency button😡"
	    				],
	    				[
	    					"Goto Youtube😸",
	    					"Goto Gmail🤑"
	    				],
	    				[

	    					"Adsense is coming🥱"
	    				]
	    			],
	    			'resize_keyboard' => true,
	    			'one_time_keyboard' => false
	    		];
	    		$encodedKeyboard = json_encode($keyboard);
	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'text' => 'Hi', 
	    			'reply_markup' => $encodedKeyboard
	    		);
	    		$str = str_replace('\\\\', '\\', $parameters);
	    		$this->send('sendMessage', $str);
	    		break;

	    		case 'keyboard':
	    		$keyboard = [
	    			'keyboard'=> [
	    				[
	    					[
	    						'text' => 'Send Contact',
	    						'request_contact' => true,
	    					],
	    					[
	    						'text' => 'Send Location',
	    						'request_location' => true,
	    					],
	    					[
	    						'text' => 'Send poll',
	    						'request_poll' => [
	    							'type' => 'quiz '
	    						],
	    					]
	    				]
	    			],
	    			'resize_keyboard' => true,
	    			'one_time_keyboard' => false,
	    		];
	    		$encodedKeyboard = json_encode($keyboard);
	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'text' => 'Hi', 
	    			'reply_markup' => $encodedKeyboard
	    		);
	    		$this->send('sendMessage', $parameters);
	    		break;

	    		case "/start ronok":

	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'text' => 'Hi My name is Ronok',
	    		);
	    		$this->send('sendMessage', $parameters);
	    		break;

	    		case "/xerochat":

	    		$parameters = 
	    		array(
	    			'chat_id' =>$this->chatId, 
	    			'text' => 'welcome to our xerochat plugin . Its the best plugin you ever see. If you want to purchase click the following link  https://codecanyon.net/item/xerochat-complete-messenger-marketing-software-for-facebook/24477224?s_rank=2', 
	    		);
	    		$this->send('sendMessage', $parameters);
	    		break;

	    		case "/xeroneit":

	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'text' => 'We are a team of highly talented, experienced, professional, and cooperative software engineers who are working in the programming and the web world for more than 9 years.
	    			We assure you a wide range of quality IT services. Our most priority and commitment is the clients’ satisfaction. We believe in long-term client relationships.
	    			We are dedicated to providing what you need as our motto is “We think of your needs”.

	    			Official Web: https://xeroneit.net
	    			CodeCanyon Profile : https://codecanyon.net/user/xeroneitbd
	    			Official Email: info@xeroneit.net (not for support purpose)', 
	    		);
	    		$this->send('sendMessage', $parameters);
	    		break;

	    		case "inline":
	    		$keyboard =[
	    			'inline_keyboard' => [
	    				[
	    					['text' => 'Shop☠️', 'callback_data' => 'hi'],
	    					['text' => 'Our site👽', 'callback_data' => 'k=2'],
	    					['text' => 'Product👹', 'callback_data' => 'k=3'],
	    				]

	    			]
	    		];
	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'text' => 'Hi see below', 
	    			'reply_markup' => $encodedKeyboard
	    		);
	    		$this->send('sendMessage', $parameters);
	    		break;

	    		case "this is home button":
	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'text' => 'https://mail.google.com/mail/u/0/#inbox'
	    		);
	    		$this->send('sendMessage', $parameters);
	    		break;


		    	//send image. It has also (InlineKeyboardMarkup or ReplyKeyboardMarkup or ReplyKeyboardRemove or ForceReply) option
	    		case "image":

	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'photo' => 'https://ibb.co/QDDnPkB'
	    		);
	    		$this->send('sendPhoto', $parameters);

	    		break;	

		    	// send audio  It has also (InlineKeyboardMarkup or ReplyKeyboardMarkup or ReplyKeyboardRemove or ForceReply) option

	    		case "audio":

	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'audio' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3'
	    		);
	    		$this->send('sendAudio', $parameters);

	    		break;	

		    		// send video  It has also (InlineKeyboardMarkup or ReplyKeyboardMarkup or ReplyKeyboardRemove or ForceReply) option

	    		case "video":

	    		$parameters = 
	    		array(
	    			'chat_id' => $this->chatId, 
	    			'video' => 'https://newrajshahi.com/video/first.mp4'
	    		);
	    		$this->send('sendVideo', $parameters);

	    		break;	
	    	}

	    	
	    } 



	    public function callback_reply()
	    {
	    	switch ($this->callback_data) 
	    	{
	    		case "hi":
	    		$keyboard =[
	    			'inline_keyboard' => [
	    				[
	    					['text' => 'Want to purchase😍', 'callback_data' => 'k=1'],
	    					['text' => 'Product Url💩','url' =>'https://codecanyon.net/user/xeroneitbd/portfolio?sso=1&_ga=2.182618606.978718287.1570540949-283641735.1561562480'],

	    				]

	    			]
	    		];
	    		$encodedKeyboard = json_encode($keyboard);
	    		$parameters = 
	    		array(
	    			'chat_id' => $this->callback_from_id, 
	    			'text' => 'See below',
	    			'reply_markup' => $encodedKeyboard
	    		);
	    		$answer_callback_query= array(
	    			'callback_query_id' => $this->callback_id,
	    			'text' 				=> 'See there',
	    			'show_alert'		=>false
	    		);
	    		$this->send('answerCallbackQuery',$answer_callback_query);
	    	    $this->send('sendMessage', $parameters);
	    		break;

	    		case "k=1":
	    		$keyboard =[
	    			'inline_keyboard' => [
	    				[
	    					['text' => '1.Xerochat👨‍🎤', 'url' => 'https://codecanyon.net/user/xeroneitbd/portfolio?sso=1&_ga=2.182618606.978718287.1570540949-283641735.1561562480'],
	    				],
	    				[
	    					['text' => '2.Xerovidd👨‍✈️', 'url' => 'https://codecanyon.net/item/xerovidd-complete-youtube-marketing-application-saas-platform/26121231?s_rank=1'],
	    				],
	    				[
	    					['text' => '3.Sitedoctor💁', 'url' => 'https://codecanyon.net/item/sitedoctor-a-sitespy-addon-website-health-checker/21805699?s_rank=3'],
	    				],
	    				[
	    					['text' => '4.Library Managementt👩‍❤️‍👨', 'url' => 'https://codecanyon.net/user/xeroneitbd/portfolio?sso=1&_ga=2.182618606.978718287.1570540949-283641735.1561562480'],
	    				]

	    			]
	    		];
	    		$encodedKeyboard = json_encode($keyboard);
	    		$parameters = 
	    		array(
	    			'chat_id' => $this->callback_from_id, 
	    			'text' => 'See below',
	    			'reply_markup' => $encodedKeyboard
	    		);
	    		$this->send('sendMessage', $parameters);
	    		break;
	    	}
	    }




	    public function load_command()
	    {
	    	$parameters="";
	    	$command = $this->send('getMyCommands', $parameters);
	    	$command_array = json_decode($command, true );
	    	unset($command_array['ok']);

	    	$cmd_array=$command_array['result'];

            $command_array =  json_encode($cmd_array);
            $parameters = array(
                    'commands' => $command_array
                );
             $this->send('setMyCommands', $parameters);


	    }

	    public function send($method, $data)
	    {
	    	$url = $this->link. "/" . $method;

	    	if (!$curld = curl_init()) {
	    		exit;
	    	}
	    	curl_setopt($curld, CURLOPT_POST, true);
	    	curl_setopt($curld, CURLOPT_POSTFIELDS, $data);
	    	curl_setopt($curld, CURLOPT_URL, $url);
	    	curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
	    	$output = curl_exec($curld);
	    	curl_close($curld);
	    	return $output;

	    }

	}



 ?>