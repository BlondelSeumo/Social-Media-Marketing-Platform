<script>

	"use strict";
	var areyousure = "<?php echo $areyousure;?>";
	var disablebot = "<?php echo $disablebot;?>";
	var enablebot = "<?php echo $enablebot;?>";
	var restart_bot = "<?php echo $restart_bot;?>";
	
	$(document).ready(function($) {
		// if(is_mobile=='0' && ((controller_name=="instagram_reply" && (function_name=="get_account_lists" || function_name==""))))
		//     setTimeout(function(){ $("#collapse_me_plz").click();}, 100);   

		// ================== use saved template or not =================================
		if($("input[name=auto_template_selection]:checked").val()=="yes")
		{
		    $("#auto_reply_templates_section").show();
		    $("#new_template_section").hide();
		    $(".save_create").hide();
		}
		else 
		{
		    $("#auto_reply_templates_section").hide();
		    $("#new_template_section").show();
		    $(".save_create").show();
		}

		$(document).on('change','input[name=auto_template_selection]',function(){
		    if($("input[name=auto_template_selection]:checked").val()=="yes")
		    {
		        $("#auto_reply_templates_section").show();
		        $("#new_template_section").hide();
		        $(".save_create").hide();
		        
		    }
		    else 
		    {
		        $("#auto_reply_templates_section").hide();
		        $("#new_template_section").show();
		        $(".save_create").show();

		    }
		});
		
		$("#auto_reply_template").select2({width: "100%"});
		$(document).on('click','.cancel_button',function(){ 
		    $("#auto_reply_message_modal_template").modal('hide'); 
		    $("#edit_auto_reply_message_modal_template").modal('hide');
		});


		$('[data-toggle="tooltip"]').tooltip();
		var base_url = "<?php echo base_url(); ?>";

		$(function(){
	       $('.page_list_item.active').trigger('click'); 
		});

		$(document).on('click', '.page_list_item', function(event) {
			event.preventDefault();

			$(".page_list_item").removeClass('active');
			$(this).addClass('active');

			var waiting_div_content = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
			$("#middle_column").html(waiting_div_content);
			$("#right_column").html(waiting_div_content);

			var page_table_id = $(this).attr('page_table_id');
			$("#dynamic_page_id").val(page_table_id);
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>instagram_reply/get_account_details",
			  data:{page_table_id:page_table_id},
			  dataType:'JSON',
			  success:function(response){
			  	$("#auto_reply_template").html(response.autoreply_templates);
				$("#middle_column").html(response.middle_column_content);
				$("#right_column").html(response.right_column_content);
				$(".private_reply_postback").html(response.autoreply_postbacks);
			  }
			});

		});

		var content_counter = 1;
		var edit_content_counter = 1;


		$(document).on('click', '.enable_auto_commnet', function () {

		    var page_table_id = $(this).attr('page_table_id');
		    var post_id = $(this).attr('post_id');
		    var manual_enable = $(this).attr('manual_enable');
		    var Pleaseprovidepostid = "<?php echo $this->lang->line("Please provide Post ID"); ?>";

		    $("#auto_reply_message_modal").addClass("modal");
		    $("#auto_reply_message_modal").modal();

		    if (typeof(post_id) === 'undefined' || post_id == '') {
		        swal('<?php echo $this->lang->line("Warning"); ?>', Pleaseprovidepostid, 'warning');
				return false;
		    }

		    $("#auto_reply_page_id").val(page_table_id);
		    $("#auto_reply_post_id").val(post_id);
		    $("#manual_enable").val(manual_enable);
		    $(".message").val('').trigger("change");
		    $(".filter_word").val('').trigger("change");
		    $("#delete_offensive_comment_hide").prop("checked", true);
		    $("#multiple_reply").prop("checked", false);
		    $("#auto_campaign_name").val('');
		    $("#hide_comment_after_comment_reply").prop("checked", false);

		    $("#generic").prop("checked", false);
		    $("#filter").prop("checked", false);
		    $("#generic_message_div").hide();
		    $("#filter_message_div").hide();

		    for (var i = 2; i <= 20; i++) {
		        $("#filter_div_" + i).hide();
		    }
		    content_counter = 1;
		    $("#content_counter").val(content_counter);
		    $("#add_more_button").show();

		    $("#manual_reply_by_post").removeClass('modal');
		});

		$(document).on('change', 'input[name=message_type]', function () {
		    if ($("input[name=message_type]:checked").val() == "generic") {
		        $("#generic_message_div").show();
		        $("#filter_message_div").hide();

		        /*** Load Emoji for generic message when clicked ***/
		        
		        $("#generic_message").emojioneArea({
		            autocomplete: false,
		            pickerPosition: "bottom"
		         });

		    }
		    else {
		        $("#generic_message_div").hide();
		        $("#filter_message_div").show();

		        /*** Load Emoji When Filter word click , by defualt first textarea are loaded & No match found field****/
		        
		        $("#comment_reply_msg_1, #nofilter_word_found_text").emojioneArea({
		            autocomplete: false,
		            pickerPosition: "bottom"
		        });

		    }
		});

		$(document).on('change','input[name=edit_message_type]',function(){    
			if($("input[name=edit_message_type]:checked").val()=="generic")
			{
				$("#edit_generic_message_div").show();
				$("#edit_filter_message_div").hide();
				
			}
			else 
			{
				$("#edit_generic_message_div").hide();
				$("#edit_filter_message_div").show();
				
				
				/*** Load Emoji When Filter word click during Edit , by defualt first textarea are loaded & No match found field****/
				
				$("#edit_comment_reply_msg_1, #edit_nofilter_word_found_text").emojioneArea({
					autocomplete: false,
					pickerPosition: "bottom"
				});
			}
		});


		$(document).on('click', '#save_button', function () {
			var use_template = $("input[name=auto_template_selection]:checked").val();
		    var post_id = $("#auto_reply_post_id").val();
		    var create_template = $(this).attr('create_template');
		    $("#create_new_template").val(create_template);
		    
		    if(typeof(use_template)==='undefined') 
		    {
		    	var reply_type = $("input[name=message_type]:checked").val();
		    	var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
		    	var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
		    	if (typeof(reply_type) === 'undefined') {
		    	    swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
		    	    return false;
		    	}
		    	var auto_campaign_name = $("#auto_campaign_name").val().trim();
		    	if (reply_type == 'generic') {
		    	    if (auto_campaign_name == '') {
		    	        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
		    	        return false;
		    	    }
		    	}
		    	else {
		    	    if (auto_campaign_name == '') {
		    	        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
		    	        return false;
		    	    }
		    	}

		    } 
		    else if(use_template == 'yes') 
		    {
		    	var template_selection = $("#auto_reply_template").val();

		    	if(template_selection == '0') 
		    	{
		    		var Youdidntselectanytemplate = "<?php echo $this->lang->line('you have not select any template.'); ?>";
		    		swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanytemplate, 'warning');
		    		return false;
		    	}
		    }

		    $(this).addClass('btn-progress');

		    var queryString = new FormData($("#auto_reply_info_form")[0]);
		    var AlreadyEnabled = "<?php echo $AlreadyEnabled; ?>";
		    $.ajax({
		        type: 'POST',
		        url: base_url + "instagram_reply/ajax_autoreply_submit",
		        data: queryString,
		        dataType: 'JSON',
		        // async: false,
		        cache: false,
		        contentType: false,
		        processData: false,
		        context:this,
		        success: function (response) {
		        	$(this).removeClass('btn-progress');
		            if (response.status == "1") {
		            	swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
		            		  $(".page_list_item.active").click();
		            		  $("#auto_reply_message_modal").modal('hide');
		            		});
		                $("button[post_id=" + post_id + "]").removeClass('btn-outline-success').addClass('btn-outline-warning disabled').html(AlreadyEnabled);
		            }
		            else {
		                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
		            }
		        }

		    });

		});

		$(document).on('click', '.edit_reply_info', function () {

		    var emoji_load_div_list="";

		    $("#manual_edit_reply_by_post").removeClass('modal');
		    $("#edit_auto_reply_message_modal").addClass("modal");
		    $("#edit_response_status").html("");
		    for (var j = 1; j <= 20; j++) {
		        $("#edit_filter_div_" + j).hide();
		    }

		    var table_id = $(this).attr('table_id');
		    $(".previewLoader").show();
		    var second_table_data = 'no';
		    $.ajax({
		        type: 'POST',
		        url: "<?php echo site_url();?>instagram_reply/ajax_edit_reply_info",
		        data: {table_id: table_id, second_table_data:second_table_data},
		        dataType: 'JSON',
		        success: function (response) {
		        	$("#edit_private_message_offensive_words").html(response.postbacks);
		        	$("#edit_generic_message_private").html(response.postbacks);
		        	$("#edit_nofilter_word_found_text_private").html(response.postbacks);
		        	for(var j=1;j<=20;j++)
		        	{
		        	  $("#edit_filter_div_"+j).hide();
		        	  $("#edit_filter_message_"+j).html(response.postbacks);
		        	}

		            $("#edit_auto_reply_page_id").val(response.edit_auto_reply_page_id);
		            $("#edit_auto_reply_post_id").val(response.edit_auto_reply_post_id);
		            $("#edit_auto_campaign_name").val(response.edit_auto_campaign_name);

		            // comment hide and delete section
		            if (response.is_delete_offensive == 'hide') {
		                $("#edit_delete_offensive_comment_hide").attr('checked', 'checked');
		            }
		            else {
		                $("#edit_delete_offensive_comment_delete").attr('checked', 'checked');
		            }

		            if(response.trigger_matching_type == 'exact')
		            	$("#edit_trigger_keyword_exact").attr('checked','checked');
		            else
		            	$("#edit_trigger_keyword_string").attr('checked','checked');
		            
		            $("#edit_delete_offensive_comment_keyword").val(response.offensive_words);

		            $("#edit_private_message_offensive_words").val(response.private_message_offensive_words).change();

		            /** make the emoji loads div id in a string for selection . This is the first add. **/
		            emoji_load_div_list=emoji_load_div_list;

		            if(response.hide_comment_after_comment_reply == 'no')
		            	$("#edit_hide_comment_after_comment_reply").removeAttr('checked','checked');
		            else
		            	$("#edit_hide_comment_after_comment_reply").attr('checked','checked');
		            // comment hide and delete section

		            $("#edit_" + response.reply_type).prop('checked', true);
		            // added by mostofa on 27-04-2017
		            if(response.multiple_reply == 'no')
		            	$("#edit_multiple_reply").removeAttr('checked','checked');
		            else
		            	$("#edit_multiple_reply").attr('checked','checked');

		            if (response.reply_type == 'generic') {
		                $("#edit_generic_message_div").show();
		                $("#edit_filter_message_div").hide();
		                var i = 1;
		                edit_content_counter = i;
		                var auto_reply_text_array_json = JSON.stringify(response.auto_reply_text);
		                auto_reply_text_array = JSON.parse(auto_reply_text_array_json, 'true');
		                $("#edit_generic_message").val(auto_reply_text_array[0]['comment_reply']).click();
		                $("#edit_generic_message_private").val(auto_reply_text_array[0]['private_reply']).change();

		                /** Add generic reply textarea id into the emoji load div list***/
		                if(emoji_load_div_list == '')
		                	emoji_load_div_list=emoji_load_div_list+"#edit_generic_message";
		                else
		                	emoji_load_div_list=emoji_load_div_list+", #edit_generic_message";
		            }
		            else {
		                var edit_nofilter_word_found_text = JSON.stringify(response.edit_nofilter_word_found_text);
		                edit_nofilter_word_found_text = JSON.parse(edit_nofilter_word_found_text, 'true');
		                $("#edit_nofilter_word_found_text").val(edit_nofilter_word_found_text[0]['comment_reply']).click();
		                $("#edit_nofilter_word_found_text_private").val(edit_nofilter_word_found_text[0]['private_reply']).change();

		                /**Add no match found textarea into emoji load div list***/
		                if(emoji_load_div_list == '')
		                	emoji_load_div_list=emoji_load_div_list+"#edit_nofilter_word_found_text";
		                else
		                	emoji_load_div_list=emoji_load_div_list+", #edit_nofilter_word_found_text";


		                $("#edit_filter_message_div").show();
		                $("#edit_generic_message_div").hide();
		                var auto_reply_text_array = JSON.stringify(response.auto_reply_text);
		                auto_reply_text_array = JSON.parse(auto_reply_text_array, 'true');

		                for (var i = 0; i < auto_reply_text_array.length; i++) {
		                    var j = i + 1;
		                    $("#edit_filter_div_" + j).show();
		                    $("#edit_filter_word_" + j).val(auto_reply_text_array[i]['filter_word']);
		                    var unscape_reply_text = auto_reply_text_array[i]['reply_text'];
		                    $("#edit_filter_message_" + j).val(unscape_reply_text).change();
		                    // added by mostofa 25-04-2017
		                    var unscape_comment_reply_text = auto_reply_text_array[i]['comment_reply_text'];
		                    $("#edit_comment_reply_msg_" + j).val(unscape_comment_reply_text).click();

		                    if(emoji_load_div_list == '')
		                    	emoji_load_div_list=emoji_load_div_list+"#edit_comment_reply_msg_"+j;
		                    else
		                    	emoji_load_div_list=emoji_load_div_list+", #edit_comment_reply_msg_"+j;
		                }

		                edit_content_counter = i + 1;
		                $("#edit_content_counter").val(edit_content_counter);
		            }
		            $("#edit_auto_reply_message_modal").modal();
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

		
		$(document).on('click', '#edit_save_button', function () {
		    var post_id = $("#edit_auto_reply_post_id").val();
		    var edit_auto_campaign_name = $("#edit_auto_campaign_name").val();
		    var reply_type = $("input[name=edit_message_type]:checked").val();
		    var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
		    var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
		    if (typeof(reply_type) === 'undefined') {
		        swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
		        return false;
		    }

		    if (reply_type == 'generic') {
		        if (edit_auto_campaign_name == '') {
		            swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
		            return false;
		        }
		    }
		    else {
		        if (edit_auto_campaign_name == '') {
		            swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
		            return false;
		        }
		    }

		    $(this).addClass('btn-progress');

		    var queryString = new FormData($("#edit_auto_reply_info_form")[0]);
		    $.ajax({
		        type: 'POST',
		        url: base_url + "instagram_reply/ajax_update_autoreply_submit",
		        data: queryString,
		        dataType: 'JSON',
		        // async: false,
		        cache: false,
		        contentType: false,
		        processData: false,
		        context:this,
		        success: function (response) {
		        	$(this).removeClass('btn-progress');
		            if (response.status == "1") {
		                swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
		                	$(".page_list_item.active").click();
		                	$("#edit_auto_reply_message_modal").modal('hide');
		                });
		            }
		            else {
		                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
		            }
		        }

		    });

		});

		$(document).on('click', '#modal_close', function () {
		    var manual_post_id = $("#manual_post_id").val();
		    $(".page_list_item.active").click();
		    if (manual_post_id != '') {
		        $("#auto_reply_message_modal").modal("hide");
		        $("#manual_reply_by_post").modal("hide");
		        $("#manual_post_id").val('');
		    }
		    else
		        $("#auto_reply_message_modal").modal("hide");
		});

		$(document).on('click', '#edit_modal_close', function () {
		    var manual_post_id = $("#manual_edit_post_id").val();
		    $(".page_list_item.active").click();
		    if (manual_post_id != '') {
		        $("#edit_auto_reply_message_modal").modal("hide");
		        $("#manual_edit_reply_by_post").modal("hide");
		        $("#manual_edit_post_id").val('');
		    }
		    else
		        $("#edit_auto_reply_message_modal").removeClass("modal");
		});

		$(document).on('click','.cancel_button',function(){
			$("#edit_auto_reply_message_modal").modal('hide'); 
			$(".page_list_item.active").click();
		});

		$("#content_counter").val(content_counter);
		$(document).on('click', '#add_more_button', function () {
		    content_counter++;
		    if (content_counter == 20)
		        $("#add_more_button").hide();
		    $("#content_counter").val(content_counter);

		    $("#filter_div_" + content_counter).show();

		    /** Load Emoji For Filter Word when click on add more button **/
		    
		   
		    $("#comment_reply_msg_"+content_counter).emojioneArea({
		        autocomplete: false,
		        pickerPosition: "bottom"
		    });
		    
		});

		$(document).on('click','#edit_add_more_button',function(){
			if(edit_content_counter == 21)
				$("#edit_add_more_button").hide();
			$("#edit_content_counter").val(edit_content_counter);

			$("#edit_filter_div_"+edit_content_counter).show();
			
			/** Load Emoji For Filter Word when click on add more button during Edit**/
			

			$("#edit_comment_reply_msg_"+edit_content_counter).emojioneArea({
				autocomplete: false,
				pickerPosition: "bottom"
			});
			
			edit_content_counter++;
			
		});

		$(document).on('click','.lead_first_name',function(){ 
		    
		    var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
		    
		    var lastIndex = textAreaTxt.lastIndexOf("<br>");
		    
		    if(lastIndex!='-1')
		        textAreaTxt = textAreaTxt.substring(0, lastIndex);
		        
		    var txtToAdd = " #LEAD_USER_NAME# ";
		    var new_text = textAreaTxt + txtToAdd;
		    $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
		    $(this).parent().next().next().next().children('.emojionearea-editor').click();
		    
		    
		});

		$(document).on('click','.lead_tag_name',function(){

		    var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
		    
		    var lastIndex = textAreaTxt.lastIndexOf("<br>");
		    
		    if(lastIndex!='-1')
		        textAreaTxt = textAreaTxt.substring(0, lastIndex);
		        
		    var txtToAdd = " #TAG_USER# ";
		    var new_text = textAreaTxt + txtToAdd;
		   $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
		   $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
		   
		});

		$(document).on('click','.instant_comment',function(){
			var page_table_id = $(this).attr('page_table_id');
			$("#instant_comment_page_id").val(page_table_id);
			var post_id = $(this).attr('post_id');
			$("#instant_comment_post_id").val(post_id);
			$("#instant_comment_message").val('');
		    $("#instant_comment_modal").modal();
		});

		$(document).on('click','.submit_instant_comment',function(){
			var page_table_id = $("#instant_comment_page_id").val();
			var post_id = $("#instant_comment_post_id").val();
			var message = $("#instant_comment_message").val();
			if(message == '')
			{
				swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide your comment first."); ?>', 'warning');
				return false;
			}

			$(this).addClass('btn-progress');

			$.ajax({
			    context: this,
			    type:'POST' ,
			    url:"<?php echo site_url();?>instagram_reply/instant_commnet_submit",
			    data: {page_table_id:page_table_id,post_id:post_id,message:message},
			    dataType: 'json',
			    success:function(response){ 
			        if(response.status == 1)
			        {
			            $(this).removeClass('btn-progress');
			            var span = document.createElement("span");
			            span.innerHTML = response.message;
			            swal({ title:'<?php echo $this->lang->line("Success"); ?>', content:span,icon:'success'}).then((value) => {
			                 	$("#instant_comment_modal").modal('hide');
			                });
			        }
			        else
			        {
			            swal('<?php echo $this->lang->line("Error!"); ?>', response.message, 'error');
			        }
			    },
			    error:function(response){
			        var span = document.createElement("span");
			        span.innerHTML = response.responseText;
			        swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
			    }
			});

		});


		$(document).on('click','.pause_campaign_info',function(e){
			e.preventDefault();
			var to_do = $(this).attr('to_do');
			swal({
			    title: '<?php echo $this->lang->line("Are you sure?"); ?>',
			    text: '<?php echo $this->lang->line("Do you really want to"); ?> '+to_do+' <?php echo $this->lang->line("this campaign?"); ?>',
			    icon: 'warning',
			    buttons: true,
			    dangerMode: true,
			})
			.then((willDelete) => {
			    if (willDelete) 
			    {
			        var table_id = $(this).attr('table_id');

			        $.ajax({
			            context: this,
			            type:'POST' ,
			            url:"<?php echo base_url('instagram_reply/pause_campaign_info')?>",
			            data:{table_id:table_id, to_do:to_do},
			            success:function(response){ 

			                if (response == "1") {
			                	swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Camapaign Status hase been updated successfully."); ?>', 'success');
			                	report_table.draw();
			                }
			                else {
			                    swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
			                }
			            }
			        });
			    } 
			});

		}); 


		// ======================================================================================
		// ======================== full account reply events ===================================

		var full_content_counter = 1;
		var full_edit_content_counter = 1;


		$(document).on('click','.enable_full_auto_commnet',function(){

		    var page_table_id = $(this).attr('table_id'); 

		    $("#full_auto_reply_page_id").val(page_table_id);
		    $(".full_message").val('').trigger("change");
		    $(".full_filter_word").val('').trigger("change");
		    $("#full_delete_offensive_comment_hide").prop("checked", true);
		    $("#full_multiple_reply").prop("checked", false);
		    $("#full_hide_comment_after_comment_reply").prop("checked", false);
		    $("#full_auto_campaign_name").val('');
		    $("#full_generic").prop("checked", false);
		    $("#full_filter").prop("checked", false);
		    $("#full_generic_message_div").hide();
		    $("#full_filter_message_div").hide();

		    for (var i = 2; i <= 20; i++) {
		        $("#full_filter_div_" + i).hide();
		    }
		    full_content_counter = 1;
		    $("#full_content_counter").val(full_content_counter);
		    $("#full_add_more_button").show();

		    $("#full_auto_reply_message_modal").addClass("modal");
		    $("#full_auto_reply_message_modal").modal();
		});

		$(document).on('click', '#full_modal_close', function () {
		    $(".page_list_item.active").click();
		    $("#full_auto_reply_message_modal").modal("hide");
		});

		$(document).on('click', '#full_edit_modal_close', function () {
		    $(".page_list_item.active").click();
		    $("#full_edit_auto_reply_message_modal").modal("hide");
		});

		$(document).on('change', 'input[name=full_message_type]', function () {
		  	if ($("input[name=full_message_type]:checked").val() == "generic") {
		        $("#full_generic_message_div").show();
		        $("#full_filter_message_div").hide();

		        /*** Load Emoji for generic message when clicked ***/
		        $("#full_generic_message").emojioneArea({
		            autocomplete: false,
		            pickerPosition: "bottom"
		        });
		    }
	        else {
	            $("#full_generic_message_div").hide();
	            $("#full_filter_message_div").show();

	            /*** Load Emoji When Filter word click , by defualt first textarea are loaded & No match found field****/
	    
	          	$("#full_comment_reply_msg_1, #full_nofilter_word_found_text").emojioneArea({
	                autocomplete: false,
	                pickerPosition: "bottom"
	            });

	        }
		});
		

		$(document).on('change','input[name=full_edit_message_type]',function(){

		  	if ($("input[name=full_edit_message_type]:checked").val() == "generic") {
		    	$("#full_edit_generic_message_div").show();
		    	$("#full_edit_filter_message_div").hide();

		    	$("#full_edit_generic_message").emojioneArea({
	          		autocomplete: false,
	          		pickerPosition: "bottom"
		       	});

		  	} else {

		    	$("#full_edit_generic_message_div").hide();
		    	$("#full_edit_filter_message_div").show();

		    	/*** Load Emoji When Filter word click during Edit , by defualt first textarea are loaded & No match found field****/
		    	$("#full_edit_comment_reply_msg_1, #full_edit_nofilter_word_found_text").emojioneArea({
	          		autocomplete: false,
          			pickerPosition: "bottom"
		      	});
		  	}
		});


		$("#full_content_counter").val(full_content_counter);

		$(document).on('click', '#full_add_more_button', function () {
	  		full_content_counter++;
		  	if (full_content_counter == 20) {
	      		$("#full_add_more_button").hide();
		  	}
		  	$("#full_content_counter").val(full_content_counter);
		  	$("#full_filter_div_" + full_content_counter).show();

		  	/** Load Emoji For Filter Word when click on add more button **/
		  	$("#full_comment_reply_msg_"+full_content_counter).emojioneArea({
		        autocomplete: false,
		        pickerPosition: "bottom"
		    });
		});


		$(document).on('click','#full_edit_add_more_button',function(){
		
			if (full_edit_content_counter == 21) {
		    	$("#full_edit_add_more_button").hide();
			}

		  $("#full_edit_content_counter").val(full_edit_content_counter);
		  $("#full_edit_filter_div_" + full_edit_content_counter).show();

		  $("#full_edit_comment_reply_msg_"+full_edit_content_counter).emojioneArea({
		        autocomplete: false,
		        pickerPosition: "bottom"
		    });

		    full_edit_content_counter++;

		});

		$(document).on('click','.full_lead_first_name',function(){ 
		   
		   var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
		   
		   var lastIndex = textAreaTxt.lastIndexOf("<br>");
		   
		   	if(lastIndex!='-1')
	     		textAreaTxt = textAreaTxt.substring(0, lastIndex);
		     
	     	var txtToAdd = " #LEAD_USER_NAME# ";
	     	var new_text = textAreaTxt + txtToAdd;
		    $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
		    $(this).parent().next().next().next().children('.emojionearea-editor').click();   
		});

	 	$(document).on('click','.full_lead_tag_name',function() {

       		var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
	   		var lastIndex = textAreaTxt.lastIndexOf("<br>");
		   
		   	if(lastIndex!='-1')
	     		textAreaTxt = textAreaTxt.substring(0, lastIndex);
		     
	     	var txtToAdd = " #TAG_USER# ";
	   		var new_text = textAreaTxt + txtToAdd;
		    $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
		    $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
		    
		});


		$(document).on('click', '#full_save_button', function () {

			var full_reply_type = $("input[name=full_message_type]:checked").val();
			var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
			var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";

			if (typeof(full_reply_type) === 'undefined') {

				swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
				return false;
			}

			var full_auto_campaign_name = $("#full_auto_campaign_name").val().trim();
			if (full_reply_type == 'generic') {
				if (full_auto_campaign_name == '') {

					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			}
			else {
				if (full_auto_campaign_name == '') {

					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			}

			$(this).addClass('btn-progress');

			var queryString = new FormData($("#full_auto_reply_info_form")[0]);
			var AlreadyEnabled = "<?php echo $AlreadyEnabled; ?>";

			$.ajax({
				context:this,
				type: 'POST',
				url: base_url + "instagram_reply/full_autoreply_submit",
				data: queryString,
				dataType: 'JSON',
				cache: false,
				contentType: false,
				processData: false,
				success: function (response) {

					$(this).removeClass('btn-progress');

					if (response.status == "1") {
						swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
							  $(".page_list_item.active").click();
							  $("#full_auto_reply_message_modal").modal('hide');
						});
					}
					else {
					    swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
					}
				}
			});

		});


		$(document).on('click','.edit_enable_full_auto_commnet',function(){

		  var emoji_load_div_list="";

		  $("#full_edit_auto_reply_message_modal").modal();
		  for (var j = 1; j <= 20; j++) {
		    $("#full_edit_filter_div_" + j).hide();
		  }

		  var table_id = $(this).attr('table_id');
		  $("#autoreply_table_id").val(table_id);
		  $(".previewLoader").show();

		  $.ajax({
		    type: 'POST',
		    url: "<?php echo site_url();?>instagram_reply/full_edit_reply_info",
		    data: {table_id: table_id},
		    dataType: 'JSON',
		    success: function (response) {
		      $("#full_edit_auto_reply_page_id").val(response.edit_auto_reply_page_id);
		      $("#full_edit_auto_campaign_name").val(response.edit_auto_campaign_name);
		      $("#full_edit_delete_offensive_comment_keyword").val(response.offensive_words);

		      $("#full_edit_private_message_offensive_words").html(response.postbacks);
		      $("#full_edit_generic_message_private").html(response.postbacks);
		      $("#full_edit_nofilter_word_found_text_private").html(response.postbacks);

		      $("#full_edit_private_message_offensive_words").val(response.private_message_offensive_words).change();

		      // comment hide and delete section
		      if (response.is_delete_offensive == 'hide') {
		        $("#full_edit_delete_offensive_comment_hide").attr('checked', 'checked');
		      } else {
		        $("#full_edit_delete_offensive_comment_delete").attr('checked', 'checked');
		      }

		      if(response.trigger_matching_type == 'exact')
		      	$("#full_edit_trigger_keyword_exact").attr('checked','checked');
		      else
		      	$("#full_edit_trigger_keyword_string").attr('checked','checked');

		      if(response.hide_comment_after_comment_reply == 'no')
		      	$("#full_edit_hide_comment_after_comment_reply").removeAttr('checked','checked');
		      else
		      	$("#full_edit_hide_comment_after_comment_reply").attr('checked','checked');

		      // comment hide and delete section
		      $("#full_edit_" + response.reply_type).prop('checked', true);
		      
		      if (response.multiple_reply == 'no')
		        $("#full_edit_multiple_reply").removeAttr('checked', 'checked');
		      else
		        $("#full_edit_multiple_reply").attr('checked', 'checked');


		      if (response.reply_type == 'generic') {

		        $("#full_edit_generic_message_div").show();
		        $("#full_edit_filter_message_div").hide();
		        var i = 1;
		        full_edit_content_counter = i;
		        var auto_reply_text_array_json = JSON.stringify(response.auto_reply_text);
		        auto_reply_text_array = JSON.parse(auto_reply_text_array_json, 'true');
		        $("#full_edit_generic_message").val(auto_reply_text_array[0]['comment_reply']).click();
		        $("#full_edit_generic_message_private").val(auto_reply_text_array[0]['private_reply']).change();


		        /** Add generic reply textarea id into the emoji load div list***/
		        if(emoji_load_div_list == '')
		        	emoji_load_div_list=emoji_load_div_list+"#full_edit_generic_message";
		        else
		        	emoji_load_div_list=emoji_load_div_list+", #full_edit_generic_message";

		      } else {

		        var edit_nofilter_word_found_text = JSON.stringify(response.edit_nofilter_word_found_text);
		        edit_nofilter_word_found_text = JSON.parse(edit_nofilter_word_found_text, 'true');
		        $("#full_edit_nofilter_word_found_text").val(edit_nofilter_word_found_text[0]['comment_reply']).click();
		        $("#full_edit_nofilter_word_found_text_private").val(edit_nofilter_word_found_text[0]['private_reply']).change();

		       if(emoji_load_div_list == '')
		       	emoji_load_div_list=emoji_load_div_list+"#full_edit_nofilter_word_found_text";
		       else
		       	emoji_load_div_list=emoji_load_div_list+", #full_edit_nofilter_word_found_text";

		        $("#full_edit_filter_message_div").show();
		        $("#full_edit_generic_message_div").hide();
		        var auto_reply_text_array = JSON.stringify(response.auto_reply_text);
		        auto_reply_text_array = JSON.parse(auto_reply_text_array, 'true');
		        for (var i = 0; i < auto_reply_text_array.length; i++) {
		          var j = i + 1;
		          $("#full_edit_filter_div_" + j).show();
		          $("#full_edit_filter_word_" + j).val(auto_reply_text_array[i]['filter_word']);
		          
		          var unscape_reply_text = auto_reply_text_array[i]['reply_text'];
		          $("#full_edit_filter_message_" + j).val(unscape_reply_text).change();

		          var unscape_comment_reply_text = auto_reply_text_array[i]['comment_reply_text'];
		          $("#full_edit_comment_reply_msg_" + j).val(unscape_comment_reply_text).click();

		          if(emoji_load_div_list == '')
		          	emoji_load_div_list=emoji_load_div_list+"#full_edit_comment_reply_msg_"+j;
		          else
		          	emoji_load_div_list=emoji_load_div_list+", #full_edit_comment_reply_msg_"+j;

		        }

		        full_edit_content_counter = i + 1;
		        $("#full_edit_content_counter").val(full_edit_content_counter);
		      }

		      $("#full_edit_auto_reply_message_modal").modal();
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


		
		$(document).on('click', '#full_edit_save_button', function () {

			var full_reply_type = $("input[name=full_edit_message_type]:checked").val();
			var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
			var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";

			if (typeof(full_reply_type) === 'undefined') {

				swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
				return false;
			}

			var full_auto_campaign_name = $("#full_edit_auto_campaign_name").val().trim();
			if (full_reply_type == 'generic') {
				if (full_auto_campaign_name == '') {

					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			} else {
				if (full_auto_campaign_name == '') {

					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			}

			$(this).addClass('btn-progress');

			var queryString = new FormData($("#full_edit_auto_reply_info_form")[0]);
			var AlreadyEnabled = "<?php echo $AlreadyEnabled; ?>";

			$.ajax({
				context:this,
				type: 'POST',
				url: base_url + "instagram_reply/full_edit_autoreply_submit",
				data: queryString,
				dataType: 'JSON',
				cache: false,
				contentType: false,
				processData: false,
				success: function (response) {

					$(this).removeClass('btn-progress');

					if (response.status == "1") {
						swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
							  $(".page_list_item.active").click();
							  $("#full_edit_auto_reply_message_modal").modal('hide');
						});
					}
					else {
					    swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
					}
				}
			});

		});


		$(document).on('click','.delete_full_campaign',function(e){
		    e.preventDefault();
		    swal({
		        title: '<?php echo $this->lang->line("Are you sure?"); ?>',
		        text: '<?php echo $this->lang->line("Do you want to delete Full Account Campaign for the account?"); ?>',
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            var table_id = $(this).attr('table_id');
		            var page_info_table_id = $(this).attr('page_info_table_id');
		            var autoreply_type = $(this).attr('autoreply_type');

		            $.ajax({
		                context: this,
		                type:'POST' ,
		                url:"<?php echo base_url('instagram_reply/delete_account_campaign')?>",
		                data:{table_id:table_id, page_info_table_id:page_info_table_id, autoreply_type:autoreply_type},
		                success:function(response){ 

		                    if (response == "1") {
		                    	swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Full Account Campaign has been successfully deleted."); ?>', 'success').then((value) => {
		                    		  $(".page_list_item.active").click();
		                    	});
		                    }
		                    else {
		                        swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
		                    }
		                }
		            });
		        } 
		    });
		});


		$(document).on('click','.delete_post_report',function(e){
		    e.preventDefault();
		    swal({
		        title: '<?php echo $this->lang->line("Are you sure?"); ?>',
		        text: '<?php echo $this->lang->line("Do you want to delete Full Account Campaign for the account?"); ?>',
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            var table_id = $(this).attr('table_id');
		            var page_info_table_id = $(this).attr('page_info_table_id');
		            var autoreply_type = $(this).attr('autoreply_type');

		            $.ajax({
		                context: this,
		                type:'POST' ,
		                url:"<?php echo base_url('instagram_reply/delete_account_campaign')?>",
		                data:{table_id:table_id, page_info_table_id:page_info_table_id, autoreply_type:autoreply_type},
		                success:function(response){ 

		                    if (response == "1") {
		                    	swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Full Account Campaign has been successfully deleted."); ?>', 'success').then((value) => {
		                    		  $(".page_list_item.active").click();
		                    	});
		                    }
		                    else {
		                        swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
		                    }
		                }
		            });
		        } 
		    });
		});


		$(document).on('click','.pause_play_button',function(e){
			e.preventDefault();
			var to_do = $(this).attr('to_do');
			swal({
			    title: '<?php echo $this->lang->line("Are you sure?"); ?>',
			    text: '<?php echo $this->lang->line("Do you really want to"); ?> '+to_do+' <?php echo $this->lang->line("this campaign?"); ?>',
			    icon: 'warning',
			    buttons: true,
			    dangerMode: true,
			})
			.then((willDelete) => {
			    if (willDelete) 
			    {
			        var table_id = $(this).attr('table_id');

			        $.ajax({
			            context: this,
			            type:'POST' ,
			            url:"<?php echo base_url('instagram_reply/pause_play_campaign')?>",
			            data:{table_id:table_id, to_do:to_do},
			            success:function(response){ 

			                if (response == "1") {
			                	swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Camapaign Status hase been updated successfully."); ?>', 'success').then((value) => {
			                		  $(".page_list_item.active").click();
			                	});
			                }
			                else {
			                    swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
			                }
			            }
			        });
			    } 
			});

		}); 



		// ======================================================================================
		// ======================== Mention account reply events ===================================


		var mentions_content_counter = 1;
		var mentions_edit_content_counter = 1;


		$(document).on('click','.enable_mentions_auto_commnet',function(){
		    var page_table_id = $(this).attr('table_id');

		    $("#mentions_auto_reply_page_id").val(page_table_id);
		    $(".mentions_message").val('').trigger("change");
		    $(".mentions_filter_word").val('').trigger("change");
		    $("#mentions_delete_offensive_comment_hide").prop("checked", true);
		    $("#mentions_multiple_reply").prop("checked", false);
		    $("#mentions_hide_comment_after_comment_reply").prop("checked", false);
		    $("#mentions_auto_campaign_name").val('');
		    $("#mentions_generic").prop("checked", false);
		    $("#mentions_filter").prop("checked", false);
		    $("#mentions_generic_message_div").hide();
		    $("#mentions_filter_message_div").hide();

		    for (var i = 2; i <= 20; i++) {
		        $("#mentions_filter_div_" + i).hide();
		    }
		    mentions_content_counter = 1;
		    $("#mentions_content_counter").val(mentions_content_counter);
		    $("#mentions_add_more_button").show();

		    $("#mentions_auto_reply_message_modal").addClass("modal");
		    $("#mentions_auto_reply_message_modal").modal();
		});


		$(document).on('click', '#mentions_modal_close', function () {
		    $(".page_list_item.active").click();
		    $("#mentions_auto_reply_message_modal").modal("hide");
		});

		$(document).on('click', '#mentions_edit_modal_close', function () {
		    $(".page_list_item.active").click();
		    $("#mentions_edit_auto_reply_message_modal").modal("hide");
		});

		$(document).on('change', 'input[name=mentions_message_type]', function () {
		  	if ($("input[name=mentions_message_type]:checked").val() == "generic") {
		        $("#mentions_generic_message_div").show();
		        $("#mentions_filter_message_div").hide();

		        /*** Load Emoji for generic message when clicked ***/
		        $("#mentions_generic_message").emojioneArea({
		              autocomplete: false,
		              pickerPosition: "bottom"
		        });
		    }
	        else {
	            $("#mentions_generic_message_div").hide();
	            $("#mentions_filter_message_div").show();

	            /*** Load Emoji When Filter word click , by defualt first textarea are loaded & No match found field****/
	    
	          	$("#mentions_comment_reply_msg_1, #mentions_nofilter_word_found_text").emojioneArea({
	                autocomplete: false,
	                pickerPosition: "bottom"
	            });

	        }
		});

		$(document).on('change','input[name=mentions_edit_message_type]',function(){

		  	if ($("input[name=mentions_edit_message_type]:checked").val() == "generic") {
		    	$("#mentions_edit_generic_message_div").show();
		    	$("#mentions_edit_filter_message_div").hide();

		    	$("#mentions_edit_generic_message").emojioneArea({
	          		autocomplete: false,
	          		pickerPosition: "bottom"
		       	});

		  	} else {

		    	$("#mentions_edit_generic_message_div").hide();
		    	$("#mentions_edit_filter_message_div").show();

		    	/*** Load Emoji When Filter word click during Edit , by defualt first textarea are loaded & No match found field****/
		    
		    	$("#mentions_edit_comment_reply_msg_1, #mentions_edit_nofilter_word_found_text").emojioneArea({
	          		autocomplete: false,
          			pickerPosition: "bottom"
		      	});
		  	}
		});


		$("#mentions_content_counter").val(mentions_content_counter);

		$(document).on('click', '#mentions_add_more_button', function () {
	  		mentions_content_counter++;
		  	if (mentions_content_counter == 20) {
	      		$("#mentions_add_more_button").hide();
		  	}
		  	$("#mentions_content_counter").val(mentions_content_counter);
		  	$("#mentions_filter_div_" + mentions_content_counter).show();

		  	/** Load Emoji For Filter Word when click on add more button **/
		  	$("#mentions_comment_reply_msg_"+mentions_content_counter).emojioneArea({
		        autocomplete: false,
		        pickerPosition: "bottom"
		    });
		});


		$(document).on('click','#mentions_edit_add_more_button',function(){
		
			if (mentions_edit_content_counter == 21) {
		    	$("#mentions_edit_add_more_button").hide();
			}

		  $("#mentions_edit_content_counter").val(mentions_edit_content_counter);
		  $("#mentions_edit_filter_div_" + mentions_edit_content_counter).show();

		  $("#mentions_edit_comment_reply_msg_"+mentions_edit_content_counter).emojioneArea({
		        autocomplete: false,
		        pickerPosition: "bottom"
		    });

		    mentions_edit_content_counter++;

		});

		$(document).on('click','.mentions_lead_first_name',function(){ 
		   
		   var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
		   
		   var lastIndex = textAreaTxt.lastIndexOf("<br>");
		   
		   	if(lastIndex!='-1')
	     		textAreaTxt = textAreaTxt.substring(0, lastIndex);
		     
	     	var txtToAdd = " #LEAD_USER_NAME# ";
	     	var new_text = textAreaTxt + txtToAdd;
		    $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
		    $(this).parent().next().next().next().children('.emojionearea-editor').click();   
		});

	 	$(document).on('click','.mentions_lead_tag_name',function() {

       		var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
	   		var lastIndex = textAreaTxt.lastIndexOf("<br>");
		   
		   	if(lastIndex!='-1')
	     		textAreaTxt = textAreaTxt.substring(0, lastIndex);
		     
	     	var txtToAdd = " #TAG_USER# ";
	   		var new_text = textAreaTxt + txtToAdd;
		    $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
		    $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
		    
		});

		$(document).on('click', '#mentions_save_button', function () {

			var mentions_reply_type = $("input[name=mentions_message_type]:checked").val();
			var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
			var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";

			if (typeof(mentions_reply_type) === 'undefined') {

				swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
				return false;
			}

			var mentions_auto_campaign_name = $("#mentions_auto_campaign_name").val().trim();
			if (mentions_reply_type == 'generic') {
				if (mentions_auto_campaign_name == '') {

					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			}
			else {
				if (mentions_auto_campaign_name == '') {

					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			}

			$(this).addClass('btn-progress');

			var queryString = new FormData($("#mentions_auto_reply_info_form")[0]);
			var AlreadyEnabled = "<?php echo $AlreadyEnabled; ?>";

			$.ajax({
				context:this,
				type: 'POST',
				url: base_url + "instagram_reply/mentions_autoreply_submit",
				data: queryString,
				dataType: 'JSON',
				cache: false,
				contentType: false,
				processData: false,
				success: function (response) {

					$(this).removeClass('btn-progress');

					if (response.status == "1") {
						swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
							  $(".page_list_item.active").click();
							  $("#mentions_auto_reply_message_modal").modal('hide');
						});
					}
					else {
					    swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
					}
				}
			});

		});


		$(document).on('click','.edit_enable_mentions_auto_commnet',function(){

		  var emoji_load_div_list="";

		  $("#mentions_edit_auto_reply_message_modal").modal();
		  for (var j = 1; j <= 20; j++) {
		    $("#mentions_edit_filter_div_" + j).hide();
		  }

		  var table_id = $(this).attr('table_id');
		  $("#mentions_autoreply_table_id").val(table_id);
		  $(".previewLoader").show();

		  $.ajax({
		    type: 'POST',
		    url: "<?php echo site_url();?>instagram_reply/mentions_edit_reply_info",
		    data: {table_id: table_id},
		    dataType: 'JSON',
		    success: function (response) {
		      $("#mentions_edit_auto_reply_page_id").val(response.edit_auto_reply_page_id);
		      $("#mentions_edit_auto_campaign_name").val(response.edit_auto_campaign_name);

		      $("#mentions_edit_delete_offensive_comment_keyword").val(response.offensive_words);

		      $("#mentions_edit_private_message_offensive_words").html(response.postbacks);
		      $("#mentions_edit_generic_message_private").html(response.postbacks);
		      $("#mentions_edit_nofilter_word_found_text_private").html(response.postbacks);

		      $("#mentions_edit_private_message_offensive_words").val(response.private_message_offensive_words).change();

		      // comment hide and delete section
		      if (response.is_delete_offensive == 'hide') {
		        $("#mentions_edit_delete_offensive_comment_hide").attr('checked', 'checked');
		      } else {
		        $("#mentions_edit_delete_offensive_comment_delete").attr('checked', 'checked');
		      }

		      if(response.hide_comment_after_comment_reply == 'no')
		      	$("#mentions_edit_hide_comment_after_comment_reply").removeAttr('checked','checked');
		      else
		      	$("#mentions_edit_hide_comment_after_comment_reply").attr('checked','checked');

		      // comment hide and delete section
		      $("#mentions_edit_" + response.reply_type).prop('checked', true);
		      
		      if (response.multiple_reply == 'no')
		        $("#mentions_edit_multiple_reply").removeAttr('checked', 'checked');
		      else
		        $("#mentions_edit_multiple_reply").attr('checked', 'checked');


		      if (response.reply_type == 'generic') {

		        $("#mentions_edit_generic_message_div").show();
		        $("#mentions_edit_filter_message_div").hide();
		        var i = 1;
		        mentions_edit_content_counter = i;
		        var auto_reply_text_array_json = JSON.stringify(response.auto_reply_text);
		        auto_reply_text_array = JSON.parse(auto_reply_text_array_json, 'true');
		        $("#mentions_edit_generic_message").val(auto_reply_text_array[0]['comment_reply']).click();
		        $("#mentions_edit_generic_message_private").val(auto_reply_text_array[0]['private_reply']).change();

		        /** Add generic reply textarea id into the emoji load div list***/
		        if(emoji_load_div_list == '')
		        	emoji_load_div_list=emoji_load_div_list+"#mentions_edit_generic_message";
		        else
		        	emoji_load_div_list=emoji_load_div_list+", #mentions_edit_generic_message";

		      } else {

		        var edit_nofilter_word_found_text = JSON.stringify(response.edit_nofilter_word_found_text);
		        edit_nofilter_word_found_text = JSON.parse(edit_nofilter_word_found_text, 'true');
		        $("#mentions_edit_nofilter_word_found_text").val(edit_nofilter_word_found_text[0]['comment_reply']).click();
		        $("#mentions_edit_nofilter_word_found_text_private").val(edit_nofilter_word_found_text[0]['private_reply']).change();

		       if(emoji_load_div_list == '')
		       	emoji_load_div_list=emoji_load_div_list+"#mentions_edit_nofilter_word_found_text";
		       else
		       	emoji_load_div_list=emoji_load_div_list+", #mentions_edit_nofilter_word_found_text";

		        $("#mentions_edit_filter_message_div").show();
		        $("#mentions_edit_generic_message_div").hide();
		        var auto_reply_text_array = JSON.stringify(response.auto_reply_text);
		        auto_reply_text_array = JSON.parse(auto_reply_text_array, 'true');
		        for (var i = 0; i < auto_reply_text_array.length; i++) {
		          var j = i + 1;
		          $("#mentions_edit_filter_div_" + j).show();
		          $("#mentions_edit_filter_word_" + j).val(auto_reply_text_array[i]['filter_word']);

		          var unscape_reply_text = auto_reply_text_array[i]['reply_text'];
		          $("#mentions_edit_filter_message_" + j).val(unscape_reply_text).change();

		          var unscape_comment_reply_text = auto_reply_text_array[i]['comment_reply_text'];
		          $("#mentions_edit_comment_reply_msg_" + j).val(unscape_comment_reply_text).click();

		          if(emoji_load_div_list == '')
		          	emoji_load_div_list=emoji_load_div_list+"#mentions_edit_comment_reply_msg_"+j;
		          else
		          	emoji_load_div_list=emoji_load_div_list+", #mentions_edit_comment_reply_msg_"+j;

		        }

		        mentions_edit_content_counter = i + 1;
		        $("#mentions_edit_content_counter").val(mentions_edit_content_counter);
		      }

		      $("#mentions_edit_auto_reply_message_modal").modal();
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

		
		$(document).on('click', '#mentions_edit_save_button', function () {

			var mentions_reply_type = $("input[name=mentions_edit_message_type]:checked").val();
			var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
			var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";

			if (typeof(mentions_reply_type) === 'undefined') {

				swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
				return false;
			}

			var mentions_auto_campaign_name = $("#mentions_edit_auto_campaign_name").val().trim();
			if (mentions_reply_type == 'generic') {
				if (mentions_auto_campaign_name == '') {

					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			} else {
				if (mentions_auto_campaign_name == '') {

					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			}

			$(this).addClass('btn-progress');

			var queryString = new FormData($("#mentions_edit_auto_reply_info_form")[0]);
			var AlreadyEnabled = "<?php echo $AlreadyEnabled; ?>";

			$.ajax({
				context:this,
				type: 'POST',
				url: base_url + "instagram_reply/mentions_edit_autoreply_submit",
				data: queryString,
				dataType: 'JSON',
				cache: false,
				contentType: false,
				processData: false,
				success: function (response) {

					$(this).removeClass('btn-progress');

					if (response.status == "1") {
						swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
							  $(".page_list_item.active").click();
							  $("#mentions_edit_auto_reply_message_modal").modal('hide');
						});
					}
					else {
					    swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
					}
				}
			});

		});


		$(document).on('click','.delete_mentions_campaign',function(e){
		    e.preventDefault();
		    swal({
		        title: '<?php echo $this->lang->line("Are you sure?"); ?>',
		        text: '<?php echo $this->lang->line("Do you want to delete mentions Account Campaign for the account?"); ?>',
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            var table_id = $(this).attr('table_id');
		            var page_info_table_id = $(this).attr('page_info_table_id');
		            var autoreply_type = $(this).attr('autoreply_type');

		            $.ajax({
		                context: this,
		                type:'POST' ,
		                url:"<?php echo base_url('instagram_reply/delete_account_campaign')?>",
		                data:{table_id:table_id, page_info_table_id:page_info_table_id, autoreply_type:autoreply_type},
		                success:function(response){ 

		                    if (response == "1") {
		                    	swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("mentions Account Campaign has been successfully deleted."); ?>', 'success').then((value) => {
		                    		  $(".page_list_item.active").click();
		                    	});
		                    }
		                    else {
		                        swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
		                    }
		                }
		            });
		        } 
		    });
		});


		$(document).on('click','.mentions_pause_play_button',function(e){
			e.preventDefault();
			var to_do = $(this).attr('to_do');
			swal({
			    title: '<?php echo $this->lang->line("Are you sure?"); ?>',
			    text: '<?php echo $this->lang->line("Do you really want to"); ?> '+to_do+' <?php echo $this->lang->line("this campaign?"); ?>',
			    icon: 'warning',
			    buttons: true,
			    dangerMode: true,
			})
			.then((willDelete) => {
			    if (willDelete) 
			    {
			        var table_id = $(this).attr('table_id');
			        $.ajax({
			            context: this,
			            type:'POST' ,
			            url:"<?php echo base_url('instagram_reply/mentions_pause_play_campaign')?>",
			            data:{table_id:table_id, to_do:to_do},
			            success:function(response){ 

			                if (response == "1") {
			                	swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Camapaign Status hase been updated successfully."); ?>', 'success').then((value) => {
			                		  $(".page_list_item.active").click();
			                	});
			                }
			                else {
			                    swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
			                }
			            }
			        });
			    } 
			});

		}); 


		$(document).on('click', '.media_insights', function(event) {
			event.preventDefault();

			var page_id = $(this).attr("page_table_id");
			var post_id = $(this).attr("post_id");

			if(typeof(post_id) === 'undefined' || post_id == "") {
				swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please Provide Post ID"); ?>', 'warning');
				return false;
			}

			var loading = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center font_size_60px"></i></div>';

			$("#media_insights_modal").modal();
			$("#media_insights_modal_body").html(loading);

			$.ajax({
				url: base_url+'instagram_reply/media_insights_modal_data',
				type: 'POST',
				data: {page_id: page_id, post_id:post_id},
				success:function(response) {
					$("#media_insights_modal_body").html(response);
				}
			})

		});


		// ================================= post, full , mentions reply report section ============================

		var perscroll;
		var report_table = $("#mytable_instagram_report").DataTable({
		    serverSide: true,
		    processing:true,
		    bFilter: false,
		    order: [[ 1, "desc" ]],
		    pageLength: 10,
		    ajax: 
		    {
		      "url": base_url+'instagram_reply/all_autoreply_report_data',
		      "type": 'POST',
		      data: function ( d )
		      {
		          d.reply_type = $('#reply_type').val();
		          d.page_info_table_id = $('#page_info_table_id').val();
		          d.auto_reply_campaign_id = $('#auto_reply_campaign_id').val();
		          d.search_with_accounts = $("#insta_accounts").val();
		          d.post_id = $("#post_id").val();
		      }
		    },
		    language: 
		    {
		      url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
		    },
		    dom: '<"top"f>rt<"bottom"lip><"clear">',
		    columnDefs: [
		        {
		          targets: [1],
		          visible: false
		        },
		        {
		          targets: [0,1,2,3,4,5],
		          className: 'text-center'
		        },
		        {
		          targets: '',
		          sortable: false
		        }
		    ],
		    fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
		      if(areWeUsingScroll)
		      {
		        if (perscroll) perscroll.destroy();
		        perscroll = new PerfectScrollbar('#mytable_instagram_report_wrapper .dataTables_scrollBody');
		      }
		    },
		    scrollX: 'auto',
		    fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
		      if(areWeUsingScroll)
		      { 
		        if (perscroll) perscroll.destroy();
		        perscroll = new PerfectScrollbar('#mytable_instagram_report_wrapper .dataTables_scrollBody');
		      }
		    }
		});

		$(document).on('click', '#report_search', function(event) {
		  event.preventDefault(); 
		  report_table.draw();
		});

		$(document).on('change', '#insta_accounts', function(event) {
		  event.preventDefault(); 
		  report_table.draw();
		});


		// End of datatable section

		$(document).on('click','.delete_post_report',function(e){
		    e.preventDefault();
		    swal({
		        title: '<?php echo $this->lang->line("Are you sure?"); ?>',
		        text: '<?php echo $this->lang->line("Do you really want to delete this Campaign? If you delete this all of your saved data and post report will be deleted."); ?>',
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            var table_id = $(this).attr('table_id');
		            var page_info_table_id = $(this).attr('page_info_table_id');
		            var autoreply_type = $(this).attr('autoreply_type');

		            $.ajax({
		                context: this,
		                type:'POST' ,
		                url:"<?php echo base_url('instagram_reply/delete_post_report')?>",
		                data:{table_id:table_id, page_info_table_id:page_info_table_id, autoreply_type:autoreply_type},
		                success:function(response){ 

		                    if (response == "1") {
		                    	swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Post Auto reply and reports has been successfully deleted."); ?>', 'success');
		                    	report_table.draw();
		                    }
		                    else {
		                        swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
		                    }
		                }
		            });
		        } 
		    });
		});

		$(document).on('click','.delete_full_mention_report',function(e){
		    e.preventDefault();
		    swal({
		        title: '<?php echo $this->lang->line("Are you sure?"); ?>',
		        text: '<?php echo $this->lang->line("Do you really want to delete this Campaign? If you delete this all of your post report will be deleted."); ?>',
		        icon: 'warning',
		        buttons: true,
		        dangerMode: true,
		    })
		    .then((willDelete) => {
		        if (willDelete) 
		        {
		            var table_id = $(this).attr('table_id');
		            var post_id = $(this).attr('post_id');
		            var reply_type = $(this).attr('reply_type');

		            $.ajax({
		                context: this,
		                type:'POST' ,
		                url:"<?php echo base_url('instagram_reply/delete_full_mention_report')?>",
		                data:{table_id:table_id, post_id:post_id, reply_type:reply_type},
		                success:function(response){ 

		                    if (response == "1") {
		                    	swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Campaign Reports has been successfully deleted."); ?>', 'success');
		                    	report_table.draw();
		                    }
		                    else {
		                        swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
		                    }
		                }
		            });
		        } 
		    });
		});


		var table_campaign_report = '';
		var perscroll_campaign_report;
		$(document).on('click','.view_report',function(e){
		  e.preventDefault();

		  	var table_id = $(this).attr("table_id");
		  	var post_id = $(this).attr("post_id");
		  	var reply_type = $(this).attr("reply_type");
		    $("#campaign_report_modal").modal();

		    $("#contents").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center font_size_40px"></i></div');

		  	$.ajax({
		  		url: base_url+'instagram_reply/get_content_info',
		  		type: 'post',
		  		data: {table_id: table_id,post_id:post_id,reply_type},
		  		success:function(response) {
		  			$("#contents").html(response);
		  		}
		  	})

		  	$("#campaign_report_post_id").val(post_id);
		  	$("#table_id").val(table_id);
		  	$("#reply_type").val(reply_type);

		    setTimeout(function(){
		        if (table_campaign_report == '')
		        {
		            table_campaign_report = $("#mytable_campaign_report").DataTable({
		                serverSide: true,
		                processing:true,
		                bFilter: true,
		                order: [[ 1, "desc" ]],
		                pageLength: 10,
		                ajax: {
		                    url: base_url+'instagram_reply/get_autoreply_report',
		                    type: 'POST',
		                    data: function ( d )
		                    {
		                        d.table_id = $("#table_id").val();
		                        d.reply_type = $("#reply_type").val();
		                        d.post_id = $("#campaign_report_post_id").val();
		                    }
		                },
		                language: 
		                {
		                  url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
		                },
		                dom: '<"top"f>rt<"bottom"lip><"clear">',
		                columnDefs: [
		                    {
		                        targets:[1],
		                        visible: false
		                    },
		                    {
		                      targets: [5,6],
		                      className: 'text-center'
		                    },
		                    {
		                      targets: [0,1,2,3,4,5,6,7],
		                      sortable: false
		                    }
		                ],
		                fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
		                if(areWeUsingScroll)
		                {
		                    if (perscroll_campaign_report) perscroll_campaign_report.destroy();
		                        perscroll_campaign_report = new PerfectScrollbar('#mytable_campaign_report_wrapper .dataTables_scrollBody');
		                }
		                },
		                scrollX: 'auto',
		                fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
		                    if(areWeUsingScroll)
		                    { 
		                    if (perscroll_campaign_report) perscroll_campaign_report.destroy();
		                    perscroll_campaign_report = new PerfectScrollbar('#mytable_campaign_report_wrapper .dataTables_scrollBody');
		                    }
		                }
		            });
		        }
		        else table_campaign_report.draw();
		    },1000);
		});

		$('#campaign_report_modal').on('hidden.bs.modal', function () {
		    table_campaign_report.draw();
		    report_table.draw();
		});


		$(document).on('click','.enable_disable_comments',function(e){
			e.preventDefault();
			var to_do = $(this).attr('enable_or_disable');
			swal({
			    title: '<?php echo $this->lang->line("Are you sure?"); ?>',
			    text: '<?php echo $this->lang->line("Do you really want to"); ?> '+to_do+' <?php echo $this->lang->line("comments for this post on Instagram?"); ?>',
			    icon: 'warning',
			    buttons: true,
			    dangerMode: true,
			})
			.then((willDelete) => {
			    if (willDelete) 
			    {
			        var page_table_id = $(this).attr('page_table_id');
			        var post_id = $(this).attr('post_id');

			        $.ajax({
			            context: this,
			            type:'POST' ,
			            url:"<?php echo base_url('instagram_reply/enable_disable_commnets')?>",
			            data:{page_table_id:page_table_id, to_do:to_do, post_id:post_id},
			            success:function(response){ 

			                if (response == "1") {

			                	if(to_do == 'enable')
			                		swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Comments for this post has been enabled successfully."); ?>', 'success').then((value) => {
			                		  $(".page_list_item.active").click(); });
			                	else
			                		swal('<?php echo $this->lang->line("Success"); ?>', '<?php echo $this->lang->line("Comments for this post has been disabled successfully."); ?>', 'success').then((value) => {
			                		  $(".page_list_item.active").click(); });

			                }
			                else {
			                    swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Something went wrong, please try once again. "); ?>', 'error');
			                }
			            }
			        });
			    } 
			});

		}); 

		$(document).on('click','.check_all_comments',function(e){
		  e.preventDefault();

		  	var page_table_id = $(this).attr("page_table_id");
		  	var post_id = $(this).attr("post_id");
		    $("#all_comments_modal").modal();

		    $("#all_comments_modal_contents").html('<div class="text-center waiting p-4"><i class="fas fa-spinner fa-spin blue text-center font_size_60px"></i></div');

		  	$.ajax({
		  		url: base_url+'instagram_reply/get_all_comments_of_post',
		  		type: 'post',
		  		data: {page_table_id:page_table_id, post_id:post_id},
		  		success:function(response) {
		  			$("#all_comments_modal_contents").html(response);
		  		}
		  	})

		});

		$(document).on('click','.tagged_media',function(e){
		  e.preventDefault();

		  	var page_table_id = $(this).attr("page_table_id");
		  	$('.tagged_media_button').removeClass('btn-outline-danger');
		  	$('.tagged_media_button').addClass('btn-danger')
		  	$('.tagged_media_button').addClass('btn-progress');

		  	$.ajax({
		  		context:this,
		  		url: base_url+'instagram_reply/get_all_tagged_media',
		  		type: 'post',
		  		data: {page_table_id:page_table_id},
		  		success:function(response) {
		  			$('.tagged_media_button').addClass('btn-outline-danger');
		  			$('.tagged_media_button').removeClass('btn-danger')
		  			$('.tagged_media_button').removeClass('btn-progress');
		  			$("#right_column").html(response);
		  		}
		  	})

		});

	});
</script>