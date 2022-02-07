<?php
	function get_access_token($authentication_code,$client_id,$client_secret,$redirect_uris){
	
	$url = "https://www.linkedin.com/oauth/v2/accessToken";
 	$post="code={$authentication_code}&client_id={$client_id}&client_secret={$client_secret}&redirect_uri={$redirect_uris}&grant_type=authorization_code";
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, true);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
     curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
     curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  
	 $st=curl_exec($ch);  
	 return $result=json_decode($st,TRUE);	 
	 }
	 
	 
	 function get_curl($url){
	 	
	 		$ch = curl_init();
	 		$headers = array("Content-type: application/json");
	 		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 	    curl_setopt($ch, CURLOPT_URL, $url);
	 		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	 	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	 	    curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
	 	    curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
	 	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	 	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
	 		$st=curl_exec($ch);  
	 		return $result=json_decode($st,TRUE);
	 }
	 
	 
	 
	 function get_channel_content_details($access_token){
	 	$url ="https://www.googleapis.com/youtube/v3/channels?part=contentDetails&mine=true&access_token={$access_token}"; 
		
		return get_curl($url);
	 }
	 
	 
	 function playlist_item($access_token,$playlist_id,$next_page=''){	
	 $url ="https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId={$playlist_id}&mine=true&access_token={$access_token}&maxResults=10&pageToken={$next_page}";
	 return get_curl($url);
	 
	 }
	 
	 function get_video_details($access_token,$video_ids){
	 
	 	$part=urlencode("contentDetails,statistics,snippet");
	 	 $url ="https://www.googleapis.com/youtube/v3/videos?part={$part}&id={$video_ids}&mine=true&access_token={$access_token}";
	 	return get_curl($url);
		
	 }
	 

 ?>