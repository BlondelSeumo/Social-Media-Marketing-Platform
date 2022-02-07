<script>
  var pageresponse_content_counter = 1;
  var pageresponse_edit_content_counter = 1;

  var base_url = '<?php echo base_url(); ?>';
    
  $(document).on('click','.enable_page_response',function(e){
    e.preventDefault();

  	var page_table_id = $(this).attr('page_table_id');
  	var post_id = $(this).attr('page_id');
  	$("#pageresponse_auto_reply_page_id").val(page_table_id);
  	$("#pageresponse_auto_reply_post_id").val(post_id);
  	$("#pageresponse_manual_enable").val('no');
  	$(".message").val('');
  	$(".filter_word").val('');
  	for(var i=2;i<=20;i++)
  	{
  		$("#pageresponse_filter_div_"+i).hide();
  	}
  	pageresponse_content_counter = 1;
  	$("#pageresponse_content_counter").val(pageresponse_content_counter);
  	$("#pageresponse_add_more_button").show();
  	$("#pageresponse_response_status").html('');
  	$("#pageresponse_auto_reply_message_modal").addClass("modal");
  	$("#pageresponse_auto_reply_message_modal").modal();
  });



  $(document).on('click','.fullpage_pause_play',function(e){
    e.preventDefault();
    var to_do = $(this).attr('pause_play');
    var table_id = $(this).attr('table_id');
    var warning_content = '';
    if(to_do == 'play')
      warning_content = "<?php echo $this->lang->line('Do you really want to start full page campaign?'); ?>";
    else
      warning_content = "<?php echo $this->lang->line('Do you really want to stop full page campaign?'); ?>";

    swal({
      title: '<?php echo $this->lang->line("Warning!"); ?>',
      text: warning_content,
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) 
      {
        $(this).addClass('btn-progress');
        var base_url = '<?php echo site_url();?>';
        $.ajax({
          context: this,
          type:'POST' ,
          url:"<?php echo site_url();?>comment_automation/pause_play_campaign",
          dataType: 'json',
          data:{to_do:to_do,table_id:table_id},
          success:function(response){ 
            $(this).removeClass('btn-progress');
            if(response.status == '1')
            {
              swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                $(".page_list_item.active").click();
              });
            }
            else
            {
              swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
            }
          }
        });
      } 
    });


  });
    

  $("#pageresponse_content_counter").val(pageresponse_content_counter);
    
  $(document).on('click','#pageresponse_add_more_button',function(){
    pageresponse_content_counter++;
    if(pageresponse_content_counter == 20)
      $("#pageresponse_add_more_button").hide();
    $("#pageresponse_content_counter").val(pageresponse_content_counter);
    $("#pageresponse_filter_div_"+pageresponse_content_counter).show();
  
    /** Load Emoji For Filter Word when click on add more button **/
		
		
		$("#pageresponse_comment_reply_msg_"+pageresponse_content_counter).emojioneArea({
      		autocomplete: false,
			pickerPosition: "bottom"
   	 	});
  });

  $(document).on('change','input[name=pageresponse_message_type]',function(){    
    if($("input[name=pageresponse_message_type]:checked").val()=="generic")
    {
      $("#pageresponse_generic_message_div").show();
      $("#pageresponse_filter_message_div").hide();

      /*** Load Emoji for generic message when clicked ***/
	
	    $("#pageresponse_generic_message").emojioneArea({
    		autocomplete: false,
		    pickerPosition: "bottom"
 		  });
	 
    }
    else 
    {
      $("#pageresponse_generic_message_div").hide();
      $("#pageresponse_filter_message_div").show();


      /*** Load Emoji When Filter word click , by defualt first textarea are loaded & No match found field****/
	
	    $("#pageresponse_comment_reply_msg_1, #pageresponse_nofilter_word_found_text").emojioneArea({
    		autocomplete: false,
		    pickerPosition: "bottom"
 	 	  });
    }

  });        



  $(document).on('click','#pageresponse_save_button',function(){
    var post_id = $("#pageresponse_auto_reply_post_id").val();
    var reply_type = $("input[name=pageresponse_message_type]:checked").val();
    var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
    var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
    if (typeof(reply_type)==='undefined')
    {
      swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
      return false;
    }
    var pageresponse_auto_campaign_name = $("#pageresponse_auto_campaign_name").val().trim();
    if(reply_type == 'generic')
    {
      if(pageresponse_auto_campaign_name == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
        return false;
      }
    }
    else
    {
      if(pageresponse_auto_campaign_name == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
        return false;
      }
    }

    $(this).addClass('btn-progress');

    var queryString = new FormData($("#pageresponse_auto_reply_info_form")[0]);
      $.ajax({
        type:'POST' ,
        url: base_url+"comment_automation/pageresponse_autoreply_submit",
        data: queryString,
        dataType : 'JSON',
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        context: this,
        success:function(response){
            $(this).removeClass('btn-progress');
            if(response.status=="1")
            {
              swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                $(".page_list_item.active").click();
                $("#auto_reply_message_modal").modal('hide');
                $("#pageresponse_auto_reply_message_modal").modal('hide');
              });
            }
            else
            {
              swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
            }
        }
      });
  });
  

  $(document).on('click','.pageresponse_edit_reply_info',function(e){
    e.preventDefault();
    var emoji_load_div_list="";

    $("#manual_edit_reply_by_post").removeClass('modal');
    $("#pageresponse_edit_auto_reply_message_modal").addClass("modal");
    $("#pageresponse_edit_response_status").html("");
    // for(var j=1;j<=20;j++)
    // {
    //   $("#pageresponse_edit_filter_div_"+j).hide();
    // }
    var table_id = $(this).attr('table_id');
    $(".previewLoader").show();
    $.ajax({
      type:'POST' ,
      url:"<?php echo site_url();?>comment_automation/pageresponse_reply_info",
      data:{table_id:table_id},
      dataType:'JSON',
      success:function(response){

        $("#pageresponse_edit_private_message_offensive_words").html(response.postbacks);
        $("#pageresponse_edit_generic_message_private").html(response.postbacks);
        $("#pageresponse_edit_nofilter_word_found_text_private").html(response.postbacks);
        $("#pageresponse_edit_filter_message_1").html(response.postbacks);
        for(var j=2;j<=20;j++)
        {
          $("#pageresponse_edit_filter_div_"+j).hide();
          $("#pageresponse_edit_filter_message_"+j).html(response.postbacks);
        }
        
        $("#pageresponse_edit_auto_reply_page_id").val(response.pageresponse_edit_auto_reply_page_id);
        $("#pageresponse_edit_auto_reply_post_id").val(response.pageresponse_edit_auto_reply_post_id);
        $("#pageresponse_edit_auto_campaign_name").val(response.pageresponse_edit_auto_campaign_name);
        // comment hide and delete section
        if(response.is_delete_offensive == 'hide')
        {
            $("#pageresponse_edit_delete_offensive_comment_hide").attr('checked','checked');
        }
        else
        {
          $("#pageresponse_edit_delete_offensive_comment_delete").attr('checked','checked');
        }

        if(response.trigger_matching_type == 'exact')
          $("#pageresponse_edit_trigger_keyword_exact").attr('checked','checked');
        else
          $("#pageresponse_edit_trigger_keyword_string").attr('checked','checked');

        $("#pageresponse_edit_delete_offensive_comment_keyword").val(response.offensive_words);
        $("#pageresponse_edit_private_message_offensive_words").val(response.private_message_offensive_words).click();

	  
	      /**	make the emoji loads div id in a string for selection . This is the first add. **/
		    // emoji_load_div_list=emoji_load_div_list+"";
        if(response.hide_comment_after_comment_reply == 'no')
            $("#pageresponse_edit_hide_comment_after_comment_reply").removeAttr('checked','checked');
          else
            $("#pageresponse_edit_hide_comment_after_comment_reply").attr('checked','checked');
        // comment hide and delete section

        $("#pageresponse_edit_"+response.reply_type).prop('checked', true);
        // added by mostofa on 27-04-2017
        if(response.pageresponse_comment_reply_enabled == 'no')
          $("#pageresponse_edit_comment_reply_enabled").removeAttr('checked','checked');
        else
          $("#pageresponse_edit_comment_reply_enabled").attr('checked','checked');

        if(response.pageresponse_multiple_reply == 'no')
          $("#pageresponse_edit_multiple_reply").removeAttr('checked','checked');
        else
          $("#pageresponse_edit_multiple_reply").attr('checked','checked');

        if(response.pageresponse_auto_like_comment == 'no')
          $("#pageresponse_edit_auto_like_comment").removeAttr('checked','checked');
        else
          $("#pageresponse_edit_auto_like_comment").attr('checked','checked');

        var inner_content = '<i class="fas fa-times"></i> Remove';

        if(response.reply_type == 'generic')
        {
          $("#pageresponse_edit_generic_message_div").show();
          $("#pageresponse_edit_filter_message_div").hide();
          var i=1;
          pageresponse_edit_content_counter = i;
          var auto_reply_text_array_json = JSON.stringify(response.auto_reply_text);
          auto_reply_text_array = JSON.parse(auto_reply_text_array_json,'true');
          $("#pageresponse_edit_generic_message").val(auto_reply_text_array[0]['comment_reply']).click(); 
          $("#pageresponse_edit_generic_message_private").val(auto_reply_text_array[0]['private_reply']).click();
		
    			/** Add generic reply textarea id into the emoji load div list***/
          if(emoji_load_div_list == '')
    			emoji_load_div_list=emoji_load_div_list+"#pageresponse_edit_generic_message";
          else
          emoji_load_div_list=emoji_load_div_list+", #pageresponse_edit_generic_message";
		
          // comment hide and delete section
          $("#pageresponse_edit_generic_image_for_comment_reply_display").attr('src',auto_reply_text_array[0]['image_link']).show();
          if(auto_reply_text_array[0]['image_link']=="")
          {            
            $("#pageresponse_edit_generic_image_for_comment_reply_display").prev('span').removeClass('remove_media').html('');
            $("#pageresponse_edit_generic_image_for_comment_reply_display").hide();
          }
          else
            $("#pageresponse_edit_generic_image_for_comment_reply_display").prev('span').addClass('remove_media').html(inner_content);


          var vidreplace='<source src="'+auto_reply_text_array[0]['video_link']+'" id="pageresponse_edit_generic_video_comment_reply_display" type="video/mp4">';
          $("#pageresponse_edit_generic_video_comment_reply_display").parent().html(vidreplace).show();

          if(auto_reply_text_array[0]['video_link']=='')
          {
            $("#pageresponse_edit_generic_video_comment_reply_display").parent().prev('span').removeClass('remove_media').html('');
            $("#pageresponse_edit_generic_video_comment_reply_display").parent().hide();
          }
          else
            $("#pageresponse_edit_generic_video_comment_reply_display").parent().prev('span').addClass('remove_media').html(inner_content);

          $("#pageresponse_edit_generic_image_for_comment_reply").val(auto_reply_text_array[0]['image_link']);
          $("#pageresponse_edit_generic_video_comment_reply").val(auto_reply_text_array[0]['video_link']);
          // comment hide and delete section
        }
        else
        {
          var pageresponse_edit_nofilter_word_found_text = JSON.stringify(response.pageresponse_edit_nofilter_word_found_text);
          pageresponse_edit_nofilter_word_found_text = JSON.parse(pageresponse_edit_nofilter_word_found_text,'true');
          $("#pageresponse_edit_nofilter_word_found_text").val(pageresponse_edit_nofilter_word_found_text[0]['comment_reply']).click();
          $("#pageresponse_edit_nofilter_word_found_text_private").val(pageresponse_edit_nofilter_word_found_text[0]['private_reply']).click();
		
		     /**Add no match found textarea into emoji load div list***/
         if(emoji_load_div_list == '')
		     emoji_load_div_list=emoji_load_div_list+"#pageresponse_edit_nofilter_word_found_text";
         else
         emoji_load_div_list=emoji_load_div_list+", #pageresponse_edit_nofilter_word_found_text";
				
          // comment hide and delete section
          $("#pageresponse_edit_nofilter_image_upload_reply_display").attr('src',pageresponse_edit_nofilter_word_found_text[0]['image_link']).show();
          if(pageresponse_edit_nofilter_word_found_text[0]['image_link']=="")
          {
            $("#pageresponse_edit_nofilter_image_upload_reply_display").prev('span').removeClass('remove_media').html('');
            $("#pageresponse_edit_nofilter_image_upload_reply_display").hide();
          }
          else
            $("#pageresponse_edit_nofilter_image_upload_reply_display").prev('span').addClass('remove_media').html(inner_content);


          var vidreplace='<source src="'+pageresponse_edit_nofilter_word_found_text[0]['video_link']+'" id="pageresponse_edit_nofilter_video_upload_reply_display" type="video/mp4">';
          $("#pageresponse_edit_nofilter_video_upload_reply_display").parent().html(vidreplace).show();
          if(pageresponse_edit_nofilter_word_found_text[0]['video_link']=='')
          {
            $("#pageresponse_edit_nofilter_video_upload_reply_display").parent().prev('span').removeClass('remove_media').html('');
            $("#pageresponse_edit_nofilter_video_upload_reply_display").parent().hide();
          }
          else
            $("#pageresponse_edit_nofilter_video_upload_reply_display").parent().prev('span').addClass('remove_media').html(inner_content);


          $("#pageresponse_edit_nofilter_image_upload_reply").val(pageresponse_edit_nofilter_word_found_text[0]['image_link']);
          $("#pageresponse_edit_nofilter_video_upload_reply").val(pageresponse_edit_nofilter_word_found_text[0]['video_link']);
          // comment hide and delete section
          $("#pageresponse_edit_filter_message_div").show();
          $("#pageresponse_edit_generic_message_div").hide();
          var auto_reply_text_array = JSON.stringify(response.auto_reply_text);
          auto_reply_text_array = JSON.parse(auto_reply_text_array,'true');
          for(var i = 0; i < auto_reply_text_array.length; i++) {
              var j = i+1;
              $("#pageresponse_edit_filter_div_"+j).show();
            $("#pageresponse_edit_filter_word_"+j).val(auto_reply_text_array[i]['filter_word']);
            var unscape_reply_text = auto_reply_text_array[i]['reply_text'];
            $("#pageresponse_edit_filter_message_"+j).val(unscape_reply_text).click();
            // added by mostofa 25-04-2017
            var unscape_comment_reply_text = auto_reply_text_array[i]['comment_reply_text'];
            $("#pageresponse_edit_comment_reply_msg_"+j).val(unscape_comment_reply_text).click();
		        
            if(emoji_load_div_list == '')
		        emoji_load_div_list=emoji_load_div_list+"#pageresponse_edit_comment_reply_msg_"+j;
            else
            emoji_load_div_list=emoji_load_div_list+", #pageresponse_edit_comment_reply_msg_"+j;

            // comment hide and delete section
            $("#pageresponse_edit_filter_image_upload_reply_display_"+j).attr('src',auto_reply_text_array[i]['image_link']).show();
            if(auto_reply_text_array[i]['image_link']=="")
            {
              $("#pageresponse_edit_filter_image_upload_reply_display_"+j).prev('span').removeClass('remove_media').html('');
              $("#pageresponse_edit_filter_image_upload_reply_display_"+j).hide();
            }
            else
              $("#pageresponse_edit_filter_image_upload_reply_display_"+j).prev('span').addClass('remove_media').html(inner_content);


            var vidreplace='<source src="'+auto_reply_text_array[i]['video_link']+'" id="pageresponse_edit_filter_video_upload_reply_display'+j+'" type="video/mp4">';
            $("#pageresponse_edit_filter_video_upload_reply_display"+j).parent().html(vidreplace).show();
            if(auto_reply_text_array[i]['video_link']=='')
            {
              $("#pageresponse_edit_filter_video_upload_reply_display"+j).parent().prev('span').removeClass('remove_media').html('');
              $("#pageresponse_edit_filter_video_upload_reply_display"+j).parent().hide();
            }
            else
              $("#pageresponse_edit_filter_video_upload_reply_display"+j).parent().prev('span').addClass('remove_media').html(inner_content);
              

            $("#pageresponse_edit_filter_image_upload_reply_"+j).val(auto_reply_text_array[i]['image_link']);
            $("#pageresponse_edit_filter_video_upload_reply_"+j).val(auto_reply_text_array[i]['video_link']);
            // comment hide and delete section
          }
          pageresponse_edit_content_counter = i+1;
          $("#pageresponse_edit_content_counter").val(pageresponse_edit_content_counter);
        }
        $("#pageresponse_edit_auto_reply_message_modal").modal();
      }
    });
  
    setTimeout(function(){			
			$(emoji_load_div_list).emojioneArea({
					autocomplete: false,
					pickerPosition: "bottom"
			});
		},2000);
		
		setTimeout(function(){
		
			$(".previewLoader").hide();
			
		},5000);
  
  
  });

  $(document).on('click','#pageresponse_edit_add_more_button',function(){
    if(pageresponse_edit_content_counter == 21)
    $("#pageresponse_edit_add_more_button").hide();

    $("#pageresponse_edit_content_counter").val(pageresponse_edit_content_counter);
    $("#pageresponse_edit_filter_div_"+pageresponse_edit_content_counter).show();
  
    /** Load Emoji For Filter Word when click on add more button during Edit**/
		
		
		$("#pageresponse_edit_comment_reply_msg_"+pageresponse_edit_content_counter).emojioneArea({
    		autocomplete: false,
			pickerPosition: "bottom"
 	 	});
  
    pageresponse_edit_content_counter++;
  });
  
  $(document).on('click','#pageresponse_edit_save_button',function(){
    var post_id = $("#pageresponse_edit_auto_reply_post_id").val();
    var pageresponse_edit_auto_campaign_name = $("#pageresponse_edit_auto_campaign_name").val();
    var reply_type = $("input[name=pageresponse_edit_message_type]:checked").val();
    var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
    var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
    if (typeof(reply_type)==='undefined')
    {
      swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
      return false;
    }
    if(reply_type == 'generic')
    {
      if(pageresponse_edit_auto_campaign_name == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
        return false;
      }
    }
    else
    {
      if(pageresponse_edit_auto_campaign_name == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
        return false;
      }
    }

    $(this).addClass('btn-progress');

    var queryString = new FormData($("#pageresponse_edit_auto_reply_info_form")[0]);
      $.ajax({
        type:'POST' ,
        url: base_url+"comment_automation/pageresponse_autoreply_update",
        data: queryString,
        dataType : 'JSON',
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        context: this,
        success:function(response){
            $(this).removeClass('btn-progress');
            if(response.status=="1")
            {
              swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                $(".page_list_item.active").click();
                $("#edit_auto_reply_message_modal").modal('hide');
                $("#pageresponse_edit_auto_reply_message_modal").modal('hide');
              });
            }
            else
            {
              swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
            }
        }
      });
  });

  $(document).on('change','input[name=pageresponse_edit_message_type]',function(){    
    if($("input[name=pageresponse_edit_message_type]:checked").val()=="generic")
    {
      $("#pageresponse_edit_generic_message_div").show();
      $("#pageresponse_edit_filter_message_div").hide();

      $("#edit_generic_message, #edit_generic_message_private").emojioneArea({
    		autocomplete: false,
		    pickerPosition: "bottom"
 		  });
	 
    }
    else 
    {
      $("#pageresponse_edit_generic_message_div").hide();
      $("#pageresponse_edit_filter_message_div").show();

      /*** Load Emoji When Filter word click during Edit , by defualt first textarea are loaded & No match found field****/
	
      $("#pagerespo_edit_comment_reply_msg_1, #pageresponse_edit_nofilter_word_found_text").emojioneArea({
  		  autocomplete: false,
	      pickerPosition: "bottom"
	 	    });

    }
    
  });
</script>