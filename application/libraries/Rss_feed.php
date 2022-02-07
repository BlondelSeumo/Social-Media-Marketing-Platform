<?php
class Rss_feed{
	function __construct(){
	
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");		
		
	}

	public function getFeed($feed_url='') 
	{
		if($feed_url=='')
		return array('error'=>'1','error_message'=>$this->CI->lang->line("Feed URL can not be empty."));

		$response=array();
	    $content = $this->curl_call($feed_url);
	    try
	    {
	    	$x = @new SimpleXmlElement($content);
	    }
	    catch(Exception $e)
	    {
	    	 $response['error']=1;
	    	 $response['error_message']=$e->getMessage();
	    	
	    }

	    if(isset($response['error']))
	    return $response;

	    $element_list=array();
	     
	    $i=0;

	    if(!isset($x->channel->item)){

	    	$response['error']=1;
	    	$response['error_message']=$this->CI->lang->line("RSS Feed has not any Channel");
	    	return $response;
	    }




	    foreach($x->channel->item as $entry) 
	    {
	    	$element_list[$i]['title']= (string) $entry->title;
	    	$element_list[$i]['link']= (string) $entry->link;
	    	$element_list[$i]['pubDate']= (string) $entry->pubDate;
	    	$i++;
		}

		$response['success']=1; 
		$response['element_list']=$element_list;
		return $response;
	}	

	public function getNewFeed($feed_url='',$lastPubDate='') 
	{
		if($feed_url=='')
		return array('error'=>'1','error_message'=>$this->CI->lang->line("Feed URL can not be empty."));

		if($lastPubDate=='')
		return array('error'=>'1','error_message'=>$this->CI->lang->line("You must specify the last publication date."));

		$response=array();
	    $content = $this->curl_call($feed_url);
	    try
	    {
	    	$x = @new SimpleXmlElement($content);
	    }
	    catch(Exception $e)
	    {
	    	 $response['error']=1;
	    	 $response['error_message']=$e->getMessage();
	    	
	    }

	    if(isset($response['error']))
	    return $response;

	    $element_list=array();
	     
	    $i=0;
	    foreach($x->channel->item as $entry) 
	    {
	    	$element_list[$i]['title']= isset($entry->title)?(string) $entry->title:"";
	    	$element_list[$i]['link']= isset($entry->link)?(string) $entry->link:"";
	    	$element_list[$i]['pubDate']= isset($entry->pubDate)?(string) $entry->pubDate:"";
	    	$element_list[$i]['image']= "";

	    	date_default_timezone_set('Europe/Dublin'); // operating in GMT
	        $last_pub_date=isset($element_list[$i]['pubDate'])?$element_list[$i]['pubDate']:"";
	        $last_pub_date=date("Y-m-d H:i:s",strtotime($last_pub_date));

	        if($last_pub_date>$lastPubDate)
	        {	        	
	        	if($element_list[$i]['link']!="")
	        	{
	        		$image_response=$this->get_meta_tag_fb($element_list[$i]['link']);
	        		$element_list[$i]['image']=isset($image_response['image'])?$image_response['image']:"";
	        	}
	        }
	    	$i++;
		}

		$response['success']=1; 
		$response['element_list']=$element_list;
		return $response;
	}	


	function curl_call($url='')
    {
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);    	
    	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    	//curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
    	$st=curl_exec($ch);
    	$info = curl_getinfo($ch);

    	if(isset($info['http_code']) && $info['http_code']!='200')
    	{
    		$ch = curl_init();
	    	curl_setopt($ch, CURLOPT_URL, $url);    	
	    	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	    	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
	    	$st=curl_exec($ch);
    	}

    	return $st;
    }


    function get_meta_tag_fb($url)
	{  
		$html=$this->run_curl_for_fb($url);	  
		$doc = new DOMDocument();
		@$doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$html);
		$nodes = $doc->getElementsByTagName('title');	  
		if(isset($nodes->item(0)->nodeValue))
			$title = $nodes->item(0)->nodeValue;
		else  $title="";

		$response=array('title'=>'','image'=>'','description'=>'','author'=>'');

		$response['title']=$title;
		$org_desciption="";

		$metas = $doc->getElementsByTagName('meta');

		for ($i = 0; $i < $metas->length; $i++)
		{
			$meta = $metas->item($i);	   
			if($meta->getAttribute('property')=='og:title')
				$response['title'] = $meta->getAttribute('content');		    
			if($meta->getAttribute('property')=='og:image')
				$response['image'] = $meta->getAttribute('content');		    
			if($meta->getAttribute('property')=='og:description')
				$response['description'] = $meta->getAttribute('content');		   
			if($meta->getAttribute('name')=='author')
				$response['author'] = $meta->getAttribute('content');		    
			if($meta->getAttribute('name')=='description')
				$org_desciption =  $meta->getAttribute('content');   
		}

		if(!isset($response['description']))
			$org_desciption =  $org_desciption;

		return $response;   

	}

	public function run_curl_for_fb($url)
	{
		$headers = array("Content-type: application/json"); 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		$results=curl_exec($ch); 	   
		return  $results;   
	}

}

?>