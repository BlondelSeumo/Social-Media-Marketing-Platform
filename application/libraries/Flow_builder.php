<?php
class Flow_builder{

	public $flow_array=array();
	public $new_postback_ids=array();
	public $unique_ids=array();
	public $is_button_or_quickreply;

	public $input_output_array= [
	'referenceInput' => [
	'type' => 'reference',
	'output' => 'referenceOutput',
	'sequence' => 'referenceOutputSequence',
	],

	'newPostbackInput' => [
	'type' => 'postback',
	'output' => 'newPostbackOutput',
	'label' =>'newPostbackOutputLabel',
	'sequence' => 'newPostbackOutputSequence',
	],

	'triggerInput' => [
	'type' => 'trigger',
	'output' => 'triggerOutput',
	],

	'textInput' => [
	'type' => 'text',
	'output' => 'textOutput',
	'button'=>'textOutputButton',
	'quickreply'=>'textOutputQuickreply',
	],

	'carouselInput' => [
	'type' => 'carousel',
	'output' => 'carouselOutput',
	'quickreply'=>'carouselOutputQuickreply',
	],

	'carouselItemInput' => [
	'type' => 'carouselItem',
	'output' => 'carouselItemOutput'
	],

	'facebookMediaInput' => [
	'type' => 'facebookMedia',
	'output' => 'facebookMediaOutput',
	'button' => 'facebookMediaOutputButton',
	'quickreply'=>'facebookMediaOutputQuickreply',
	],

	'imageInput' => [
	'type' => 'image',
	'output' => 'imageOutput',
	'quickreply'=>'imageOutQuickreply',
	],

	'videoInput' => [
	'type' => 'videoOutput',
	'output' => 'videoOutput',
	'quickreply'=>'videoOutputQuickreply',
	],

	'audioInput' => [
	'type' => 'audio',
	'output' => 'audioOutput',
	'quickreply'=>'audioOutputQuickreply',
	],

	'fileInput' => [
	'type' => 'file',
	'output' => 'fileOutput',
	'quickreply'=>'fileOutputQuickreply',
	],

	'quickReplyInput' => [
	'type' => 'quickReply',
	'output' => 'quickReplyOutput',
	],

	'buttonInput' => [
	'type' => 'button',
	'output' => 'buttonOutput',
	],

	'delayInput' => [
	'type' => 'delay',
	'output' => 'delayOutput',
	],

	'labelInput' => [
	'type' => 'label',
	],

	'otnInput' => [
	'type' => 'otn',
	'output' => 'otnOutputNewPostback',
	],

	'otnSingleInput' => [
	'type' => 'otnSingle',
	'output' => 'otnSingleOutput',
	'sequence' => 'otnSingleOutputSequence',
	],

	'userInputFlowInput' => [
	'type' => 'userInputFlow',
	'output'=>''
	],

	'sequenceInput' => [
	'type' => 'sequence',
	'output' => 'sequenceOutput',
	],

	'sequenceSingleInput' => [
	'type' => 'sequenceSingle',
	'output' => 'sequenceSingleOutput',
	],

	'ecommerceInput' => [
	'type' => 'ecommerce',
	'output' => 'ecommerceOutput',
	'quickreply'=>'ecommerceOutputQuickreply',
	],

	'genericTemplateInput' => [
	'type' => 'generic',
	'button' => 'genericTemplateOutputButton',
	'output' => 'genericTemplateOutput',
	'quickreply'=>'genericTemplateOutputQuickreply',
	],
	];


	function __construct(){
		$this->CI =& get_instance();
	}


	public function get_new_sequence_info($new_sequence_node_id,$input_type){

		$new_squence_output= isset($this->input_output_array[$input_type]['output']) ? $this->input_output_array[$input_type]['output'] : "";

		$new_squence_info['name']= isset($this->flow_array['nodes'][$new_sequence_node_id]['data']['name']) ? $this->flow_array['nodes'][$new_sequence_node_id]['data']['name'] : "";
		$new_squence_info['startingTime']= isset($this->flow_array['nodes'][$new_sequence_node_id]['data']['startingTime']) ? $this->flow_array['nodes'][$new_sequence_node_id]['data']['startingTime'] : "";
		$new_squence_info['closingTime']= isset($this->flow_array['nodes'][$new_sequence_node_id]['data']['closingTime']) ? $this->flow_array['nodes'][$new_sequence_node_id]['data']['closingTime'] : "";
		$new_squence_info['timezone']= isset($this->flow_array['nodes'][$new_sequence_node_id]['data']['timezone']) ? $this->flow_array['nodes'][$new_sequence_node_id]['data']['timezone'] : "";
		$new_squence_info['messageTag']= isset($this->flow_array['nodes'][$new_sequence_node_id]['data']['messageTag']) ? $this->flow_array['nodes'][$new_sequence_node_id]['data']['messageTag'] : "";
		$new_squence_info['xitFbUniqueSequenceId']= isset($this->flow_array['nodes'][$new_sequence_node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$new_sequence_node_id]['data']['uniqueId'] : "";

		$sequence_items= isset($this->flow_array['nodes'][$new_sequence_node_id]['outputs'][$new_squence_output]['connections']) ? $this->flow_array['nodes'][$new_sequence_node_id]['outputs'][$new_squence_output]['connections'] : [];

		$promotional=array();
		$non_promotional=array();

		$pr=0; // for promotional index 
		$np=0; // for non promotional index 
		foreach($sequence_items as $sequence_single_item){

			$single_sequence_node_id= isset($sequence_single_item['node']) ? $sequence_single_item['node']  :   "";

			// Find out postback id for this sequence single item . This id is a postback node id

			$single_sequence_next_node_id= isset($this->flow_array['nodes'][$single_sequence_node_id]['outputs']["sequenceSingleOutput"]['connections'][0]['node']) ? $this->flow_array['nodes'][$single_sequence_node_id]['outputs']["sequenceSingleOutput"]['connections'][0]['node'] : "";
			$postback_id= isset($this->flow_array['nodes'][$single_sequence_next_node_id]['data']['postbackId']) ? $this->flow_array['nodes'][$single_sequence_next_node_id]['data']['postbackId'] : "";
			array_push($this->new_postback_ids, $single_sequence_next_node_id);

			$isPromotionalChecked= isset($this->flow_array['nodes'][$single_sequence_node_id]['data']['isPromotionalChecked']) ? $this->flow_array['nodes'][$single_sequence_node_id]['data']['isPromotionalChecked'] : "";
			$isNonPromotionalChecked= isset($this->flow_array['nodes'][$single_sequence_node_id]['data']['isNonPromotionalChecked']) ? $this->flow_array['nodes'][$single_sequence_node_id]['data']['isNonPromotionalChecked'] : "";

			if($isPromotionalChecked){
				$promotional[$pr]['time'] = isset($this->flow_array['nodes'][$single_sequence_node_id]['data']['promotional']) ? $this->flow_array['nodes'][$single_sequence_node_id]['data']['promotional'] : "";
				$promotional[$pr]['postback_id'] =$postback_id;
				$pr++;
			}
			else if($isNonPromotionalChecked){
				$non_promotional[$np]['time'] = isset($this->flow_array['nodes'][$single_sequence_node_id]['data']['nonPromotional']) ? $this->flow_array['nodes'][$single_sequence_node_id]['data']['nonPromotional'] : "";
				$non_promotional[$np]['postback_id'] =$postback_id;
				$np++;
			}
		}

		// Sort the array by the time . Also we will need to make it uniquey by time. There must not be multiple message in same time. 

		// Sort promotional sequence
		$pr_time_column = array_column($promotional, 'time');
		array_multisort($pr_time_column, SORT_ASC, $promotional);

		// Make promotional unique
		$pr_time_column_unique=array_unique($pr_time_column);
		$promotional=array_intersect_key($promotional, $pr_time_column_unique);

		// Sort non promotional sequence
		$np_time_column = array_column($non_promotional, 'time');
		array_multisort($np_time_column, SORT_ASC, $non_promotional);

		// Make non promotional  unique
		$np_time_column_unique=array_unique($np_time_column);
		$non_promotional=array_intersect_key($non_promotional, $np_time_column_unique);

		$new_squence_info['promotional']=$promotional;
		$new_squence_info['non_promotional']=$non_promotional;

		return $new_squence_info;
	}

	public function get_button_info($node_id,$button_ref_name){

		$button_no=0;
		$button_info=array();

		foreach($this->flow_array['nodes'][$node_id]['outputs'][$button_ref_name]['connections'] as $button_list){

            $p_id=$button_list['node'];
            $this->is_button_or_quickreply= $button_list['input'];

            if($this->is_button_or_quickreply=="buttonInput"){

            	$button_quickreply_unique_id = isset($this->flow_array['nodes'][$p_id]['data']['postbackId']) ? $this->flow_array['nodes'][$p_id]['data']['postbackId'] : "";
            	array_push($this->unique_ids,$button_quickreply_unique_id);

                $button_text= isset($this->flow_array['nodes'][$p_id]['data']['buttonText']) ? $this->flow_array['nodes'][$p_id]['data']['buttonText'] : '';
                $button_type= isset($this->flow_array['nodes'][$p_id]['data']['buttonType']) ? $this->flow_array['nodes'][$p_id]['data']['buttonType'] : '';
                $button_value= isset($this->flow_array['nodes'][$p_id]['data']['value']) ? $this->flow_array['nodes'][$p_id]['data']['value'] : "";

                if($button_type=="YES_START_CHAT_WITH_HUMAN" || $button_type=="YES_START_CHAT_WITH_BOT"|| $button_type=="UNSUBSCRIBE_QUICK_BOXER" || $button_type=="RESUBSCRIBE_QUICK_BOXER"){
                    $button_value = $button_type;
                    $button_type ="post_back";
                }

                if($button_type=="new_post_back"){
                    $next_post_back_info_id= isset($this->flow_array['nodes'][$p_id]['outputs']['buttonOutput']['connections'][0]['node']) ? $this->flow_array['nodes'][$p_id]['outputs']['buttonOutput']['connections'][0]['node'] : "";
                    $button_value= isset($this->flow_array['nodes'][$next_post_back_info_id]['data']['postbackId']) ? $this->flow_array['nodes'][$next_post_back_info_id]['data']['postbackId'] : "";
                    array_push($this->new_postback_ids, $next_post_back_info_id);
                }
            }

            else if($this->is_button_or_quickreply=="quickReplyInput"){

            	$button_quickreply_unique_id = isset($this->flow_array['nodes'][$p_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$p_id]['data']['uniqueId'] : "";
            	array_push($this->unique_ids,$button_quickreply_unique_id);

                $button_text= isset($this->flow_array['nodes'][$p_id]['data']['buttonText']) ? $this->flow_array['nodes'][$p_id]['data']['buttonText'] : "";
                $button_type= isset($this->flow_array['nodes'][$p_id]['data']['replyType']) ? $this->flow_array['nodes'][$p_id]['data']['replyType'] : "";
                $button_value= isset($this->flow_array['nodes'][$p_id]['data']['postbackId']) ? $this->flow_array['nodes'][$p_id]['data']['postbackId'] : "";

                if($button_type=="newPostback"){
                    $next_post_back_info_id= isset($this->flow_array['nodes'][$p_id]['outputs']['quickReplyOutput']['connections'][0]['node']) ? $this->flow_array['nodes'][$p_id]['outputs']['quickReplyOutput']['connections'][0]['node'] : "";
                    $button_value= isset($this->flow_array['nodes'][$next_post_back_info_id]['data']['postbackId']) ? $this->flow_array['nodes'][$next_post_back_info_id]['data']['postbackId'] : "";
                    array_push($this->new_postback_ids, $next_post_back_info_id);
                }
            }

            // If it's a new postback, then get the postback id from node output.

            $button_info[$button_no]['button_text']=$button_text;
            $button_info[$button_no]['button_type']=$button_type;
            
            if($button_type == 'phone' || $button_type == 'email')
            {
	        	$button_info[$button_no]['value'] = '';
	        	$button_info[$button_no]['unique_id'] = $button_quickreply_unique_id;
            }
	        else if($button_type == 'post_back' || $button_type == 'new_post_back' || $button_type == 'newPostback')
	        {
	            $button_info[$button_no]['value']=$button_value."::".$button_quickreply_unique_id;
	        }
	        else
	        	$button_info[$button_no]['value'] = $button_value;

            $button_no++;

        }

        return $button_info;
	}


	public function get_carousel_item_info($node_id,$carousel_item_ref_name="carouselItems"){

		$carouselitem_no=0;
		$carousel_item_info=array();

		foreach($this->flow_array['nodes'][$node_id]['outputs'][$carousel_item_ref_name]['connections'] as $carousel_item_list){

            $carousel_item_button_info=array();

            $caroursel_node_id=$carousel_item_list['node'];

            $carousel_item_info[$carouselitem_no]['carousel_item_title']= isset($this->flow_array['nodes'][$caroursel_node_id]['data']['carouselItemTitle']) ? $this->flow_array['nodes'][$caroursel_node_id]['data']['carouselItemTitle'] : "";
            $carousel_item_info[$carouselitem_no]['carousel_item_sub_title']= isset($this->flow_array['nodes'][$caroursel_node_id]['data']['carouselItemSubtitle']) ? $this->flow_array['nodes'][$caroursel_node_id]['data']['carouselItemSubtitle'] : "";
            $carousel_item_info[$carouselitem_no]['carousel_item_image_destination']= isset($this->flow_array['nodes'][$caroursel_node_id]['data']['imageClickDestinationLink']) ? $this->flow_array['nodes'][$caroursel_node_id]['data']['imageClickDestinationLink'] : "";


            $image_filename= isset($this->flow_array['nodes'][$caroursel_node_id]['data']['file']) ? $this->flow_array['nodes'][$caroursel_node_id]['data']['file'] : "";
            if($image_filename!=""){
                $carousel_item_info[$carouselitem_no]['image_url']=$image_filename;
            }

            if(isset($this->flow_array['nodes'][$caroursel_node_id]['outputs']['carouselItemOutput']['connections']))
                $carousel_item_button_info= $this->get_button_info($caroursel_node_id,$button_ref_name="carouselItemOutput");

            $carousel_item_info[$carouselitem_no]['button_info']=$carousel_item_button_info;

            $carouselitem_no++;
		}

		return $carousel_item_info;
	}


	public function get_user_input_flow_info($node_id){

		$user_input_flow_information=array();
		$new_question_unique_id = isset($this->flow_array['nodes'][$node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$node_id]['data']['uniqueId'] : "";
		array_push($this->unique_ids,$new_question_unique_id);

		$user_input_flow_information['flow_name']= isset($this->flow_array['nodes'][$node_id]['data']['campaignName']) ? $this->flow_array['nodes'][$node_id]['data']['campaignName'] : "";
		$user_input_flow_information['unique_id']= isset($this->flow_array['nodes'][$node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$node_id]['data']['uniqueId'] : "";

		$question_node_id = isset($this->flow_array['nodes'][$node_id]['outputs']['userInputFlowOutput']['connections'][0]['node']) ? $this->flow_array['nodes'][$node_id]['outputs']['userInputFlowOutput']['connections'][0]['node'] : "";
		$question_node_type = isset($this->flow_array['nodes'][$node_id]['outputs']['userInputFlowOutput']['connections'][0]['input']) ? $this->flow_array['nodes'][$node_id]['outputs']['userInputFlowOutput']['connections'][0]['input'] : "";
		$no_question=0;

		while($question_node_id!=""){

			$user_input_flow_information['questions'][$no_question]['type'] = isset($this->flow_array['nodes'][$question_node_id]['data']['questionType']) ? $this->flow_array['nodes'][$question_node_id]['data']['questionType'] : "";
			$user_input_flow_information['questions'][$no_question]['question'] = isset($this->flow_array['nodes'][$question_node_id]['data']['question']) ? $this->flow_array['nodes'][$question_node_id]['data']['question'] : "";
			$user_input_flow_information['questions'][$no_question]['reply_type'] = isset($this->flow_array['nodes'][$question_node_id]['data']['replyType']) ? $this->flow_array['nodes'][$question_node_id]['data']['replyType'] : "";
			$user_input_flow_information['questions'][$no_question]['unique_id'] = isset($this->flow_array['nodes'][$question_node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$question_node_id]['data']['uniqueId'] : "";

			$question_unique_id = isset($this->flow_array['nodes'][$question_node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$question_node_id]['data']['uniqueId'] : "";
			array_push($this->unique_ids,$question_unique_id);

			$emailQuickreply = isset($this->flow_array['nodes'][$question_node_id]['data']['emailQuickreply']) ? $this->flow_array['nodes'][$question_node_id]['data']['emailQuickreply'] : "";
			if($emailQuickreply == 1)
			$user_input_flow_information['questions'][$no_question]['quick_reply_email'] = 'yes';

			$phoneQuickreply = isset($this->flow_array['nodes'][$question_node_id]['data']['phoneQuickreply']) ? $this->flow_array['nodes'][$question_node_id]['data']['phoneQuickreply'] : "";
			if($phoneQuickreply == 1)
			$user_input_flow_information['questions'][$no_question]['quick_reply_phone'] = 'yes';

			$user_input_flow_information['questions'][$no_question]['custom_field_id'] = isset($this->flow_array['nodes'][$question_node_id]['data']['customField']) ? $this->flow_array['nodes'][$question_node_id]['data']['customField'] : 0;
			$user_input_flow_information['questions'][$no_question]['system_field'] = isset($this->flow_array['nodes'][$question_node_id]['data']['systemField']) ? $this->flow_array['nodes'][$question_node_id]['data']['systemField'] : '';

			$assignToLabels = isset($this->flow_array['nodes'][$question_node_id]['data']['assignToLabels']) ? $this->flow_array['nodes'][$question_node_id]['data']['assignToLabels'] : [];
			$user_input_flow_information['questions'][$no_question]['label_ids'] = implode(',',$assignToLabels);

			$multipleChoices = isset($this->flow_array['nodes'][$question_node_id]['data']['multipleChoices']) ? $this->flow_array['nodes'][$question_node_id]['data']['multipleChoices'] : [];
			$user_input_flow_information['questions'][$no_question]['multiple_choice_options'] = implode(',',$multipleChoices);
			
			$user_input_flow_information['questions'][$no_question]['messenger_sequence_id'] = isset($this->flow_array['nodes'][$question_node_id]['data']['assignToMessengerSequence']) ? $this->flow_array['nodes'][$question_node_id]['data']['assignToMessengerSequence'] : 0;
			$user_input_flow_information['questions'][$no_question]['email_phone_sequence_id'] = isset($this->flow_array['nodes'][$question_node_id]['data']['assignToPhoneSequence']) ? $this->flow_array['nodes'][$question_node_id]['data']['assignToPhoneSequence'] : 0;
			$user_input_flow_information['questions'][$no_question]['skip_button_text'] = isset($this->flow_array['nodes'][$question_node_id]['data']['skipButtonText']) ? $this->flow_array['nodes'][$question_node_id]['data']['skipButtonText'] : '';

			$question_node_type = isset($this->flow_array['nodes'][$question_node_id]['outputs']['userInputFlowSingleOutput']['connections'][0]['input']) ? $this->flow_array['nodes'][$question_node_id]['outputs']['userInputFlowSingleOutput']['connections'][0]['input'] : "";
			$final_reply_type = isset($this->flow_array['nodes'][$question_node_id]['outputs']['userInputFlowSingleOutputFinalReply']['connections'][0]['input']) ? $this->flow_array['nodes'][$question_node_id]['outputs']['userInputFlowSingleOutputFinalReply']['connections'][0]['input'] : "";

			if($final_reply_type == 'newPostbackInput')
				$question_node_id = isset($this->flow_array['nodes'][$question_node_id]['outputs']['userInputFlowSingleOutputFinalReply']['connections'][0]['node']) ? $this->flow_array['nodes'][$question_node_id]['outputs']['userInputFlowSingleOutputFinalReply']['connections'][0]['node'] : "";
			else
				$question_node_id = isset($this->flow_array['nodes'][$question_node_id]['outputs']['userInputFlowSingleOutput']['connections'][0]['node']) ? $this->flow_array['nodes'][$question_node_id]['outputs']['userInputFlowSingleOutput']['connections'][0]['node'] : "";


			if($final_reply_type=='newPostbackInput'){
				$user_input_flow_information['postback_id']= isset($this->flow_array['nodes'][$question_node_id]['data']['postbackId']) ? $this->flow_array['nodes'][$question_node_id]['data']['postbackId'] : "";
				array_push($this->new_postback_ids, $question_node_id);
				$question_node_id="";
			}

			$no_question++;

		}

		return $user_input_flow_information;
	}

	public function get_new_otn_info($node_id){

		$otn_campaign_info=array();
		$newotn_uinque_id = isset($this->flow_array['nodes'][$node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$node_id]['data']['uniqueId'] : "";
		array_push($this->unique_ids,$newotn_uinque_id);

		$otn_campaign_info['campaign_name'] = isset($this->flow_array['nodes'][$node_id]['data']['templateName']) ? $this->flow_array['nodes'][$node_id]['data']['templateName'] : "";
		$otn_campaign_info['unique_id'] = isset($this->flow_array['nodes'][$node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$node_id]['data']['uniqueId'] : "";

		if(isset($this->flow_array['nodes'][$node_id]['data']['labelIds']) && !empty($this->flow_array['nodes'][$node_id]['data']['labelIds']))
		$otn_campaign_info['label_ids'] = implode(',',$this->flow_array['nodes'][$node_id]['data']['labelIds']);



		$next_postback_node_id = isset($this->flow_array['nodes'][$node_id]['outputs']['otnSingleOutput']['connections'][0]['node']) ? $this->flow_array['nodes'][$node_id]['outputs']['otnSingleOutput']['connections'][0]['node'] : "";
		$output_postback = isset($this->flow_array['nodes'][$node_id]['outputs']['otnSingleOutput']['connections'][0]['input']) ? $this->flow_array['nodes'][$node_id]['outputs']['otnSingleOutput']['connections'][0]['input'] : "";

		$output_sequence = isset($this->flow_array['nodes'][$node_id]['outputs']['otnSingleOutputSequence']['connections'][0]['input']) ? $this->flow_array['nodes'][$node_id]['outputs']['otnSingleOutputSequence']['connections'][0]['input'] : "";
		$next_sequence_node_id = isset($this->flow_array['nodes'][$node_id]['outputs']['otnSingleOutputSequence']['connections'][0]['node']) ? $this->flow_array['nodes'][$node_id]['outputs']['otnSingleOutputSequence']['connections'][0]['node'] : "";
		
		if($output_postback=='newPostbackInput'){
			$otn_campaign_info['postback_id']= isset($this->flow_array['nodes'][$next_postback_node_id]['data']['postbackId']) ? $this->flow_array['nodes'][$next_postback_node_id]['data']['postbackId'] : "";
			array_push($this->new_postback_ids, $next_postback_node_id);
			$next_postback_node_id="";
		}

		if($output_sequence=='sequenceInput'){
			$new_sequence_information = $this->get_new_sequence_info($next_sequence_node_id,"sequenceInput");
			$otn_campaign_info['new_sequence_information']=$new_sequence_information;
		}


		return $otn_campaign_info;
	}


    public function next_node_info($node_id, $node_type)
    {
        // Assign some variables
        $delay_time = 0;
        $is_typing_display = false;
        $button_info = array();
        $quickreply_info = array();
        $this->is_button_or_quickreply = "";
        $response = array();

        $text_reply_unique_id = isset($this->flow_array['nodes'][$node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$node_id]['data']['uniqueId'] : "";
        array_push($this->unique_ids,$text_reply_unique_id);

        $reply_text = isset($this->flow_array['nodes'][$node_id]['data']['textMessage']) ? $this->flow_array['nodes'][$node_id]['data']['textMessage'] : "";        
        $delay_time = isset($this->flow_array['nodes'][$node_id]['data']['delayReplyFor']) ? $this->flow_array['nodes'][$node_id]['data']['delayReplyFor'] : 0;
        $is_typing_display = isset($this->flow_array['nodes'][$node_id]['data']['IsTypingOnDisplayChecked']) ? $this->flow_array['nodes'][$node_id]['data']['IsTypingOnDisplayChecked'] : false;

        if ($node_type == "imageInput" || $node_type == "videoInput" || $node_type == "audioInput" || $node_type == "fileInput") {
            $response['url'] = isset($this->flow_array['nodes'][$node_id]['data']['file']) ? $this->flow_array['nodes'][$node_id]['data']['file'] : "";
        } // Extract Carousel items list if it's carousel type

        else if ($node_type == 'carouselInput') {
            $carousel_items = $this->get_carousel_item_info($node_id);
            $response['carousel_items'] = $carousel_items;
        } // If generic template, extract generic template inforation.

        else if ($node_type == "genericTemplateInput") {

            $generic_template['carousel_item_title'] = isset($this->flow_array['nodes'][$node_id]['data']['genericTemplateTitle']) ? $this->flow_array['nodes'][$node_id]['data']['genericTemplateTitle'] : "";
            $generic_template['carousel_item_sub_title'] = isset($this->flow_array['nodes'][$node_id]['data']['genericTemplateSubtitle']) ? $this->flow_array['nodes'][$node_id]['data']['genericTemplateSubtitle'] : "";
            $generic_template['carousel_item_image_destination'] = isset($this->flow_array['nodes'][$node_id]['data']['imageClickDestinationLink']) ? $this->flow_array['nodes'][$node_id]['data']['imageClickDestinationLink'] : "";

            $image_filename = isset($this->flow_array['nodes'][$node_id]['data']['file']) ? $this->flow_array['nodes'][$node_id]['data']['file'] : "";
            if ($image_filename != "") {
                $generic_template['image_url'] = $image_filename;
            }

            $response['generic_template'] = $generic_template;

        } else if ($node_type == "userInputFlowInput") {

            $response['user_input_flow_id'] = isset($this->flow_array['nodes'][$node_id]['data']['userInputFlowIdValue']) ? $this->flow_array['nodes'][$node_id]['data']['userInputFlowIdValue'] : "";

            if ($response['user_input_flow_id'] == 'new')
                $response['user_input_flow_campaign_info'] = $this->get_user_input_flow_info($node_id);

        } else if ($node_type == 'otnInput') 
        {

            $reply_text = isset($this->flow_array['nodes'][$node_id]['data']['otnTitleValue']) ? $this->flow_array['nodes'][$node_id]['data']['otnTitleValue'] : "";
            $response['otn_postback_id'] = isset($this->flow_array['nodes'][$node_id]['data']['otnPostbackIdValue']) ? $this->flow_array['nodes'][$node_id]['data']['otnPostbackIdValue'] : "";

            if ($response['otn_postback_id'] == 'newOtn'){
            	$next_otn_info_node_id= $this->flow_array['nodes'][$node_id]['outputs']['otnOutput']['connections'][0]['node'] ?? "";
                $response['otn_campaign_info'] = $this->get_new_otn_info($next_otn_info_node_id);
            }

        } 
        else if ($node_type == "facebookMediaInput") {
            $response['media_url'] = isset($this->flow_array['nodes'][$node_id]['data']['mediaUrl']) ? $this->flow_array['nodes'][$node_id]['data']['mediaUrl'] : "";
        } else if ($node_type == "ecommerceInput") {
            $response['store_id'] = isset($this->flow_array['nodes'][$node_id]['data']['ecommerceStoreValue']) ? $this->flow_array['nodes'][$node_id]['data']['ecommerceStoreValue'] : "";
            $response['product_ids'] = isset($this->flow_array['nodes'][$node_id]['data']['ecommerceProductValue']) ? $this->flow_array['nodes'][$node_id]['data']['ecommerceProductValue'] : "";
            $response['buy_now_button_text'] = isset($this->flow_array['nodes'][$node_id]['data']['ecommerceButtonTextValue']) ? $this->flow_array['nodes'][$node_id]['data']['ecommerceButtonTextValue'] : "";
        }

        $output_type = isset($this->input_output_array[$node_type]['output']) ? $this->input_output_array[$node_type]['output'] : "";

        $next_node_id = isset($this->flow_array['nodes'][$node_id]['outputs'][$output_type]['connections'][0]['node']) ? $this->flow_array['nodes'][$node_id]['outputs'][$output_type]['connections'][0]['node'] : "";
        $next_node_type = isset($this->flow_array['nodes'][$node_id]['outputs'][$output_type]['connections'][0]['input']) ? $this->flow_array['nodes'][$node_id]['outputs'][$output_type]['connections'][0]['input'] : "";


        // Find out button information if there any button are attached with the reply
        $button_ref_name = isset($this->input_output_array[$node_type]['button']) ? $this->input_output_array[$node_type]['button'] : "";
        if (isset($this->flow_array['nodes'][$node_id]['outputs'][$button_ref_name])) {
            $button_info = $this->get_button_info($node_id, $button_ref_name);
        }

        // Find out Quick Reply Information if there any quick reply buttons are attached.

        $quickreply_ref_name = isset($this->input_output_array[$node_type]['quickreply']) ? $this->input_output_array[$node_type]['quickreply'] : "";
        if (isset($this->flow_array['nodes'][$node_id]['outputs'][$quickreply_ref_name])) {
            $quickreply_info = $this->get_button_info($node_id, $quickreply_ref_name);
        }


        //if($node_type=='textInput')
        $response['reply_type'] = $node_type;
        $response['text_reply_unique_id'] = $text_reply_unique_id;
        $response['reply_text'] = $reply_text;
        $response['quick_replies'] = $quickreply_info;
        $response['buttons'] = $button_info;
        $response['delay_time'] = $delay_time;
        $response['is_typing_display'] = $is_typing_display;
        $response['next_node_id'] = $next_node_id;
        $response['next_node_type'] = $next_node_type;

        return $response;
    }

	public function extract_json($flow_array){

		$this->flow_array=$flow_array;
		$this->new_postback_ids=array(1);
		$count=count($this->new_postback_ids);
		$bot_settings_array=array();
		$no_of_entry=0;
		
		$processed_postback_array=array();
		
		for($i=0;$i<$count;$i++){

			$this->new_postback_ids=$this->new_postback_ids;
			$index_id=$this->new_postback_ids[$i];

			if(in_array($index_id, $processed_postback_array)) continue; 
			
			$processed_postback_array[]=$index_id;

			$output_type="referenceOutput";

			if($index_id>1)
				$output_type="newPostbackOutput";

			// Trigger  Extraction 
			if ($output_type=="referenceOutput")
				$input_type_trigger="referenceInput";
			else if ($output_type=="newPostbackOutput")
				$input_type_trigger="newPostbackInputTrigger";

			$trigger_node_id= isset($this->flow_array['nodes'][$index_id]['inputs'][$input_type_trigger]['connections'][0]['node']) ? $this->flow_array['nodes'][$index_id]['inputs'][$input_type_trigger]['connections'][0]['node'] : "";
			$trigger_type= isset($this->flow_array['nodes'][$trigger_node_id]['data']['triggerType']) ? $this->flow_array['nodes'][$trigger_node_id]['data']['triggerType'] : "";
			$trigger_keywords= isset($this->flow_array['nodes'][$trigger_node_id]['data']['triggerKeyword']) ? $this->flow_array['nodes'][$trigger_node_id]['data']['triggerKeyword'] : "";
			$trigger_matching_type= isset($this->flow_array['nodes'][$trigger_node_id]['data']['triggerMatchingType']) ? $this->flow_array['nodes'][$trigger_node_id]['data']['triggerMatchingType'] : "";

			// action button settings info. Action button will only be available with first reference . 
			if ($output_type=="referenceOutput"){
				$input_type_action_button="referenceInputActionButton";
				$action_button_node_id= isset($this->flow_array['nodes'][$index_id]['inputs'][$input_type_action_button]['connections'][0]['node']) ? $this->flow_array['nodes'][$index_id]['inputs'][$input_type_action_button]['connections'][0]['node'] : "";
				$action_button_type= isset($this->flow_array['nodes'][$action_button_node_id]['data']['actionType']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['actionType'] : "";
				$bot_settings_array[$no_of_entry]['action_button_type']=$action_button_type;
				$bot_settings_array[$no_of_entry]['xitFbUniqueId']= isset($this->flow_array['nodes'][$index_id]['data']['xitFbpostbackId']) ? $this->flow_array['nodes'][$index_id]['data']['xitFbpostbackId'] : "";

				// if the reference node has new sequence campaign, then extract that information.
				$new_squence_output_type= isset($this->input_output_array["referenceInput"]['sequence']) ? $this->input_output_array["referenceInput"]['sequence'] : "";
				$new_sequence_node_id= isset($this->flow_array['nodes'][$index_id]['outputs'][$new_squence_output_type]['connections'][0]['node']) ? $this->flow_array['nodes'][$index_id]['outputs'][$new_squence_output_type]['connections'][0]['node'] : "";

				if($new_sequence_node_id){
					$new_sequence_information = $this->get_new_sequence_info($new_sequence_node_id,"sequenceInput");
					$bot_settings_array[$no_of_entry]['new_sequence_information']=$new_sequence_information;
				}

				if($action_button_type=="messenger_engagement_plugin"){
					$button_info = [];
					$bot_settings_array[$no_of_entry]['uniqueId']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['uniqueId']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['uniqueId'] : "";

					$bot_settings_array[$no_of_entry]['pluginType']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['pluginType']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['pluginType'] : "";

					$bot_settings_array[$no_of_entry]['domain_name']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['domain']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['domain'] : "";

					$bot_settings_array[$no_of_entry]['language']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['language']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['language'] : "";

					$bot_settings_array[$no_of_entry]['cta_text_option']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['ctaButtonType']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['ctaButtonType'] : "";

					$bot_settings_array[$no_of_entry]['skin']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['pluginSkin']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['pluginSkin'] : "";

					$bot_settings_array[$no_of_entry]['center_align']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['pluginCenterAlign']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['pluginCenterAlign'] : "";

					$bot_settings_array[$no_of_entry]['btn_size']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['pluginSize']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['pluginSize'] : "";

					$bot_settings_array[$no_of_entry]['redirect']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['redirectionStatus']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['redirectionStatus'] : "0";

					$bot_settings_array[$no_of_entry]['button_click_success_message']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['optInSuccessMessage']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['optInSuccessMessage'] : "";

					$bot_settings_array[$no_of_entry]['add_button_with_message']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['addingButtonInSuccessMessage']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['addingButtonInSuccessMessage'] : "0";

					$button_info['success_button']= $this->flow_array['nodes'][$action_button_node_id]['data']['buttonText'] ?: "Send Message";
					$button_info['success_url']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['buttonUrl']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['buttonUrl'] : "";
					$button_info['success_button_bg_color']= $this->flow_array['nodes'][$action_button_node_id]['data']['buttonBgColor'] ?: "#5cb85c";
					$button_info['success_button_color']= $this->flow_array['nodes'][$action_button_node_id]['data']['buttonTextColor'] ?: "#ffffff";
					$button_info['success_button_bg_color_hover']= $this->flow_array['nodes'][$action_button_node_id]['data']['buttonHoverBgColor'] ?: "#339966";
					$button_info['success_button_color_hover']= $this->flow_array['nodes'][$action_button_node_id]['data']['buttonHoverTextColor'] ?: "#fffddd";
					$bot_settings_array[$no_of_entry]['button_with_message_content'] = json_encode($button_info);
					$bot_settings_array[$no_of_entry]['success_redirect_url']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['redirectionUrlForSuccessfulOptin']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['redirectionUrlForSuccessfulOptin'] : "";
					$bot_settings_array[$no_of_entry]['validation_error']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['checkboxValidationErrorMessage']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['checkboxValidationErrorMessage'] : "";
					$bot_settings_array[$no_of_entry]['minimized']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['chatPluginLoadingStatus']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['chatPluginLoadingStatus'] : "";
					$bot_settings_array[$no_of_entry]['delay']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['chatPluginLoadingDelayInSeconds']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['chatPluginLoadingDelayInSeconds'] : "";
					$bot_settings_array[$no_of_entry]['color']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['chatPluginThemeColor']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['chatPluginThemeColor'] : "";
					$bot_settings_array[$no_of_entry]['donot_show_if_not_login']="0";
					$bot_settings_array[$no_of_entry]['logged_in']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['greetingTextIfLoggedIn']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['greetingTextIfLoggedIn'] : "";
					$bot_settings_array[$no_of_entry]['logged_out']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['greetingTextIfNotLoggedIn']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['greetingTextIfNotLoggedIn'] : "";
					$bot_settings_array[$no_of_entry]['reference']= isset($this->flow_array['nodes'][$action_button_node_id]['data']['reference']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['reference'] : "";
					$label_ids_engagement= isset($this->flow_array['nodes'][$action_button_node_id]['data']['labelIds']) ? $this->flow_array['nodes'][$action_button_node_id]['data']['labelIds'] : [];
					$bot_settings_array[$no_of_entry]['engagement_label_ids'] = implode(",", $label_ids_engagement);
				}
			}

			$bot_settings_array[$no_of_entry]['trigger_type']=$trigger_type;
			$bot_settings_array[$no_of_entry]['trigger_keywords']=$trigger_keywords;
			$bot_settings_array[$no_of_entry]['trigger_matching_type']=$trigger_matching_type;

			// Labels & Sequence Extraction 

			$bot_settings_array[$no_of_entry]['label_ids']= isset($this->flow_array['nodes'][$index_id]['data']['labelIds']) ? $this->flow_array['nodes'][$index_id]['data']['labelIds'] : "";
			$bot_settings_array[$no_of_entry]['sequence_id']= isset($this->flow_array['nodes'][$index_id]['data']['sequenceIdValue']) ? $this->flow_array['nodes'][$index_id]['data']['sequenceIdValue'] : "";

			// if the new postback has new sequence campaign, then extract that information. 
			$new_squence_output_type= isset($this->input_output_array["newPostbackInput"]['sequence']) ? $this->input_output_array["newPostbackInput"]['sequence'] : "";
			$new_sequence_node_id= isset($this->flow_array['nodes'][$index_id]['outputs'][$new_squence_output_type]['connections'][0]['node']) ? $this->flow_array['nodes'][$index_id]['outputs'][$new_squence_output_type]['connections'][0]['node'] : "";

			if($new_sequence_node_id){
				$new_sequence_information = $this->get_new_sequence_info($new_sequence_node_id,"sequenceInput");
				$bot_settings_array[$no_of_entry]['new_sequence_information']=$new_sequence_information;
			}

			// Postback Id & title Extraction 
			$postback_title= isset($this->flow_array['nodes'][$index_id]['data']['title']) ? $this->flow_array['nodes'][$index_id]['data']['title'] : "";
			$postback_id= isset($this->flow_array['nodes'][$index_id]['data']['postbackId']) ? $this->flow_array['nodes'][$index_id]['data']['postbackId'] : "";

			$bot_settings_array[$no_of_entry]['postback_id']=$postback_id;
			$bot_settings_array[$no_of_entry]['postback_title']=$postback_title;

			$node_id= isset($this->flow_array['nodes'][$index_id]['outputs'][$output_type]['connections'][0]['node']) ? $this->flow_array['nodes'][$index_id]['outputs'][$output_type]['connections'][0]['node'] : "";
			$node_type= isset($this->flow_array['nodes'][$index_id]['outputs'][$output_type]['connections'][0]['input']) ? $this->flow_array['nodes'][$index_id]['outputs'][$output_type]['connections'][0]['input'] : "";

			$next_node_id=$node_id;

			//Condition check. If there any condition, then find out the next true & false node 
			if($node_type=='conditionInput'){

				// Get true node information 
				$node_id= isset($this->flow_array['nodes'][$next_node_id]['outputs']["conditionOutputTrue"]['connections'][0]['node']) ? $this->flow_array['nodes'][$next_node_id]['outputs']["conditionOutputTrue"]['connections'][0]['node'] : "";
				$node_type= isset($this->flow_array['nodes'][$next_node_id]['outputs']["conditionOutputTrue"]['connections'][0]['input']) ? $this->flow_array['nodes'][$next_node_id]['outputs']["conditionOutputTrue"]['connections'][0]['input'] : "";

				// get false node information
				$node_false_id= isset($this->flow_array['nodes'][$next_node_id]['outputs']["conditionOutputFalse"]['connections'][0]['node']) ? $this->flow_array['nodes'][$next_node_id]['outputs']["conditionOutputFalse"]['connections'][0]['node'] : "";
				$node_false_type= isset($this->flow_array['nodes'][$next_node_id]['outputs']["conditionOutputFalse"]['connections'][0]['input']) ? $this->flow_array['nodes'][$next_node_id]['outputs']["conditionOutputFalse"]['connections'][0]['input'] : "";

				// Get all condition information 

				 $all_match= isset($this->flow_array['nodes'][$next_node_id]['data']['all_match']) ? $this->flow_array['nodes'][$next_node_id]['data']['all_match'] : 0;
				 $any_match= isset($this->flow_array['nodes'][$next_node_id]['data']['any_match']) ? $this->flow_array['nodes'][$next_node_id]['data']['any_match'] : 0;

				 $conditions=array();

				 if($all_match) $condition_type="and"; else $condition_type="or";
				 $conditions['type'] = $condition_type;

				$system_field_variable= isset($this->flow_array['nodes'][$next_node_id]['data']['system_field_variable']) ? $this->flow_array['nodes'][$next_node_id]['data']['system_field_variable'] : array();
				$system_field_operator= isset($this->flow_array['nodes'][$next_node_id]['data']['system_field_operator']) ? $this->flow_array['nodes'][$next_node_id]['data']['system_field_operator'] : array();
				$system_field_variable_value= isset($this->flow_array['nodes'][$next_node_id]['data']['system_field_variable_value']) ? $this->flow_array['nodes'][$next_node_id]['data']['system_field_variable_value'] : array();
				$system_field_gender= isset($this->flow_array['nodes'][$next_node_id]['data']['system_field_gender']) ? $this->flow_array['nodes'][$next_node_id]['data']['system_field_gender'] : array();
				$system_field_contact_group_id_selected_values= isset($this->flow_array['nodes'][$next_node_id]['data']['system_field_contact_group_id_selected_values']) ? $this->flow_array['nodes'][$next_node_id]['data']['system_field_contact_group_id_selected_values'] : array();

				$custom_field_variable= isset($this->flow_array['nodes'][$next_node_id]['data']['custom_field_variable']) ? $this->flow_array['nodes'][$next_node_id]['data']['custom_field_variable'] : array();
				$custom_field_operator= isset($this->flow_array['nodes'][$next_node_id]['data']['custom_field_operator']) ? $this->flow_array['nodes'][$next_node_id]['data']['custom_field_operator'] : array();
				$custom_field_variable_value= isset($this->flow_array['nodes'][$next_node_id]['data']['custom_field_variable_value']) ? $this->flow_array['nodes'][$next_node_id]['data']['custom_field_variable_value'] : array();

				//{"variable":"gender","operator":"=","value":"Male"}

				$p=0;
				foreach ($system_field_variable as $key => $value) {

					$rules[$p]['variable'] = $value; 
					$rules[$p]['operator'] = isset($system_field_operator[$key]) ? $system_field_operator[$key] : "";

					if($value=="gender")
						$rules[$p]['value'] = isset($system_field_gender[$key]) ? $system_field_gender[$key] : "";
					else if($value=="contact_group_id")
						$rules[$p]['value'] = implode(",", $system_field_contact_group_id_selected_values);
					else
						$rules[$p]['value'] = isset($system_field_variable_value[$key]) ? $system_field_variable_value[$key] : "";
					$p++;
				}	

				// Custom fields condition process here : 

				foreach ($custom_field_variable as $key => $value) {

					$rules[$p]['variable'] = $value; 
					$rules[$p]['operator'] = isset($custom_field_operator[$key]) ? $custom_field_operator[$key] : "";
					$rules[$p]['value'] = isset($custom_field_variable_value[$key]) ? $custom_field_variable_value[$key] : "";
					$p++;
				}	

				$conditions['rules'] = $rules;
				$conditions=json_encode($conditions);

				$bot_settings_array[$no_of_entry]['conditions']=$conditions;
			}

			$k=0;

			// True or default node information. Default means , if there no conditions 

			while($node_id!=""){

				$response= $this->next_node_info($node_id,$node_type);
				$bot_settings_array[$no_of_entry]['reply'][$k]=$response;
				$node_id=$response['next_node_id'];
				$node_type=$response['next_node_type'];
				$k++;
				// if($k==10) break;
			}

			// False node Information 

			if(isset($node_false_id)) 
			while($node_false_id!=""){

				$response= $this->next_node_info($node_false_id,$node_false_type);
				$bot_settings_array[$no_of_entry]['reply_false'][$k]=$response;
				$node_false_id=$response['next_node_id'];
				$node_false_type=$response['next_node_type'];
				$k++;
				// if($k==10) break;
			}

			$count=count($this->new_postback_ids);
			$no_of_entry++;
		}

		$bot_settings_array['unique_ids'] = $this->unique_ids;

		return $bot_settings_array;
		// echo "<pre>";
		 // print_r($bot_settings_array);

		// echo "<pre>";
			// print_r($this->new_postback_ids);
	}
}

