<?php session_start();
	set_time_limit(0);
	ob_start();
 ?>
 
<link href="css/bootstrap.min.css" rel="stylesheet">
<?php 
	
	
	
	include('config.php');
	include('function.php');
	
/****Get code after Call back***/
if(isset($_GET['code'])){
	$authentication_code=$_GET['code'];
}

/*** Check if the access token is expired or not****/

/*******Login Url. This will again return into my page with a code********/

if(isset($_COOKIE['access_token'])){
	$access_token=$_COOKIE['access_token'];
}	

if ($access_token=='' && $authentication_code!=''){
	/******Get The authorization Code/Access Token******/
	$result=get_access_token($authentication_code,$client_id,$client_secret,$redirect_uris);
	/***Take access token, also there is the expiration duration*****/
	$access_token=$result['access_token'];
	$token_expired=$result['expires_in'];
	
	/***Set Cookie for access token***/
	setcookie("access_token", $access_token, time()+$token_expired);
}

?>


<body>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	    <!-- Static navbar -->
	    <nav class="navbar navbar-inverse navbar-static-top">
	      <div class="container">
	        <div class="navbar-header">
	          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
	            <span class="sr-only">Toggle navigation</span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a class="navbar-brand" href="#">Project LinkedIn</a>
	        </div>
	      </div>
	    </nav>
	    <div class='container'>
			
		<?php
		if(!$access_token){
			$auth_url="https://www.linkedin.com/uas/oauth2/authorization?client_id={$client_id}&redirect_uri={$redirect_uris}&scope=r_basicprofile%20r_emailaddress%20w_share&response_type=code&state=offline";
			echo "<a href='{$auth_url}'>Login</a>";
			exit;
		}
	$data=get_curl("https://api.linkedin.com/v1/people/~?format=json&oauth2_access_token={$access_token}");
	
	echo "<pre>";
	print_r($data);
	echo "</pre>";

	?>
			
		</div>
	<script src="js/jquery-1.11.2.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js"></script>
	</body>
		
		
<?php

include_once('vendor/autoload.php');

	$linkedIn=new Happyr\LinkedIn\LinkedIn($client_id, $client_secret);
	
	$linkedIn->setAccessToken($access_token);

	$options = array('json'=>
    array(
        'comment' => 'Im testing LinkedIn Post! https://www.youtube.com/watch?v=ZVlUwwgOfKw',
        'visibility' => array(
            'code' => 'anyone'
        )
    )
);

$result = $linkedIn->post('v1/people/~/shares', $options);

	echo "<pre>";
	var_dump($result);
	echo "</pre>";