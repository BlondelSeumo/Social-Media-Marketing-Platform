<?php 

class Mailchimp_api 
{
	function syncMailchimp($data='', $apikey, $listId,$tags='') 
 	{
       
        $apikey_explode = explode('-',$apikey); // The API ID is the last part of your api key, after the hyphen (-), 
        if(is_array($apikey_explode) && isset($apikey_explode[1])) $api_id=$apikey_explode[1];
        else $api_id="";

        if($apikey=="" || $api_id=="" || $listId=="" || $data==""){

              $result['error']="Error in API ID Settings";
              return json_encode($result);
        }
      
        $auth = base64_encode( 'user:'.$apikey );
		
        $insert_data=array
        (
			'email_address'  => $data['email'],
			'status'         => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
			'merge_fields'  => array('FNAME'=>$data['firstname'],'LNAME'=>$data['lastname'],'CITY'=>'','MMERGE5'=>"Subscriber")	
	    );

        if($tags!=""){
            
            if(is_array($tags))
                $insert_data['tags']=$tags; 
            else
                $insert_data['tags']=array($tags); 
        }
            
			
		$insert_data=json_encode($insert_data);
 	
		$url="https://".$api_id.".api.mailchimp.com/3.0/lists/".$listId."/members/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Basic '.$auth));
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $insert_data);
        return $result = curl_exec($ch);
    }


    function get_all_list($apikey) 
 	{
       
        $apikey_explode = explode('-',$apikey); // The API ID is the last part of your api key, after the hyphen (-), 
        if(is_array($apikey_explode) && isset($apikey_explode[1])) $api_id=$apikey_explode[1];
        else $api_id="";

        if($apikey=="" || $api_id=="") {
            $result['error']=true;
            return json_encode($result);
        }
      
        $auth = base64_encode( 'user:'.$apikey );

		$url="https://".$api_id.".api.mailchimp.com/3.0/lists?fields=lists";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Basic '.$auth));
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
       
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     
        $response = curl_exec($ch);
        
        $curl_info=curl_getinfo($ch);
        if($curl_info['http_code']!='200'){
            $result=array();
            if($response!="")
                $result['error_message']=$response;
            else{
                 $result['error_message']="Http Code - ". $curl_info['http_code']." : ".curl_error($ch);
            }
            $result['error']=true;
            return json_encode($result);    
        }

        return $response;  
    }



    public function sendinblue_contact_list($api_key){

        $url="https://api.sendinblue.com/v3/contacts/lists";
        $header=array("api-key: {$api_key}","Content-Type: application/json");
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $response = curl_exec( $ch );
        $response=json_decode($response,true);

        return $response;

    }



    public function sendinblue_add_contact($api_key,$email,$firstname,$lastname,$list_id){

        $url="https://api.sendinblue.com/v3/contacts";
        $header=array("api-key: {$api_key}","Content-Type: application/json");
        $postdata['email']=$email;
        $postdata['attributes']['FIRSTNAME']=$firstname;
        $postdata['attributes']['LASTNAME']=$lastname;
        $postdata['listIds'][0]=(int)$list_id;
        $postdata=json_encode($postdata);

        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $response = curl_exec( $ch );
        return $response;
    }


    public function activecampaign_contact_list($api_key,$url){

        $url=$url."/api/3/lists";

        $header=array("Api-Token: {$api_key}","Content-Type: application/json");
        
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $response = curl_exec( $ch );
        $response=json_decode($response,true);

        return $response;

    }



    public function activecampaign_add_contact($api_key,$url,$email,$firstname,$lastname,$list_id){

        $url_add=$url."/api/3/contacts";
        $header=array("Api-Token: {$api_key}","Content-Type: application/json");
        $postdata['contact']['email']=$email;
        $postdata['contact']['firstName']=$firstname;
        $postdata['contact']['lastName']=$lastname;
        $postdata=json_encode($postdata);

        $ch=curl_init($url_add);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $response = curl_exec( $ch );

        $curl_info=curl_getinfo($ch);

        if($curl_info['http_code']=='403'){
            $response=array();
            $response['errors']=true;
            $response['errors'][0]['code']="The request could not be authenticated or the authenticated user is not authorized to access the requested resource";
            return json_encode($response);    
        }

        $response_arr=json_decode($response,true);

        if(isset($response_arr['errors']))
            return $response;

        $contact_id=$response_arr['contact']['id'];
        $url_list_update=$url."/api/3/contactLists";

        $postdata=array();
        $postdata['contactList']['list']=(int)$list_id;
        $postdata['contactList']['contact']=$contact_id;
        $postdata['contactList']['status']=1;

        $postdata=json_encode($postdata);

        $ch=curl_init($url_list_update);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $response = curl_exec( $ch );
        return $response;

    }




     public function mautic_segment_list($base_64_user_pass,$url){
        
        $url=$url."/api/segments";
        $header = array("Authorization: Basic " . $base_64_user_pass);
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        $response = curl_exec( $ch );
        $response=json_decode($response,true);
        return $response;
    }


    public function mautic_add_contact($base_64_user_pass,$url,$email,$firstname,$lastname,$list_id,$tag){

        $url_add=$url."/api/contacts/new";

        $header = array("Authorization: Basic " . $base_64_user_pass);

        $data = array(
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'email'     => $email,
        'ipAddress' => $_SERVER['REMOTE_ADDR'],
        'tags' =>$tag,
        'overwriteWithBlank' => true,
        );

        $ch=curl_init($url_add);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec( $ch );
        $response=json_decode($response,true);


        if(isset($response['errors']))
            return $response;

        $contact_id=$response['contact']['id'];
        $url_segment_update=$url."/api/segments/{$list_id}/contact/{$contact_id}/add";

        $ch=curl_init($url_segment_update);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        curl_setopt($ch, CURLOPT_POST, true);
        $final_response = curl_exec( $ch );
        return $final_response;






     }







    public function acelle_segment_list($api_token,$url){
        
        $url=$url."/lists?api_token={$api_token}";
        $header=array("accept:application/json");

        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        $response = curl_exec( $ch );
        $response=json_decode($response,true);
        return $response;
    }


    public function acelle_add_contact($api_token,$url,$email,$firstname,$lastname,$list_id){

      //  $url=$url."/lists/{$list_id}/subscribers/store?api_token={$api_token}";
        $url=$url."/subscribers?list_uid={$list_id}&api_token={$api_token}";
        $header=array("accept:application/json");

        $data = array(
            'FIRST_NAME' => $firstname,
            'LAST_NAME'  => $lastname,
            'EMAIL'     => $email,
        );
            
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec( $ch );
        return $response;

    }

}