function message_change() {
  	var message=$("#message").val();
		message=htmlspecialchars(message);
		message=message.replace(/[\r\n]/g, "<br />");

		var post_type=$('.post_type.active').attr("id");
    if(post_type=="text_post" && message=='') message = 'Text goes here';
  	
		// message=message+"<br/><br/>";
		$(".preview_message").html(message);
  	
}

function htmlspecialchars(str) {
  	 if (typeof(str) == "string") {
  	  str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
  	  str = str.replace(/"/g, "&quot;");
  	  str = str.replace(/'/g, "&#039;");
  	  str = str.replace(/</g, "&lt;");
  	  str = str.replace(/>/g, "&gt;");
  	  }
  	 return str;
  }

$(function() {
	"use strict";
	
  var image_list = [];
	$("document").ready(function()	{
	
		var emoji_message_div =	$("#message").emojioneArea({
        	autocomplete: false,
			pickerPosition: "bottom"
     	 });

		var today = new Date();
		var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
		$('.datepicker_x').datetimepicker({
			theme:'light',
			format:'Y-m-d H:i:s',
			formatDate:'Y-m-d H:i:s',
			minDate: today,
			maxDate: next_date

		});

		setTimeout(function() {		
			$(".upload_block").niceScroll();
		}, 1000);

		

		$(document).on('click','#check_all_pages',function(e){
			if ($(this).is(':checked')) $('.post_to_pages').prop('checked', true);
			else $('.post_to_pages').prop('checked', false);
		});

		$(document).on('click','#check_all_groups',function(e){
			if ($(this).is(':checked')) $('.post_to_groups').prop('checked', true);
			else $('.post_to_groups').prop('checked', false);
		});

		$(document).on('click','#check_all_accounts',function(e){
			if ($(this).is(':checked')) $('.post_to_accounts').prop('checked', true);
			else $('.post_to_accounts').prop('checked', false);
		});

		$(document).on('click','.select_media',function(e){
        	e.preventDefault();
        	var media_src = $(this).attr('src');
        	if($(this).hasClass('image'))
        	{
        		$(".select_media.image").removeClass('active');
        		$(this).addClass('active');
        		$('#image_url').val(media_src).blur();
        	}
        	else
        	{
        		$(".select_media.video").removeClass('active');
        		$(this).addClass('active');
        		$('#video_url').val(media_src).blur();
        	}
        });

		$(document).on('click','.video_format_info',function(e){
    	e.preventDefault();
    	$("#video_format_info_modal").modal();
    });

	  $("#image_url_upload").uploadFile({
	      url:base_url+"instagram_poster/image_video_upload_image_only",
	      fileName:"myfile",
	      maxFileSize:instragram_post_image_upload_limit*1024*1024,
	      showPreview:false,
	      returnType: "json",
	      dragDrop: true,
	      showDelete: false,
	      multiple:false,
	      acceptFiles:".jpg,.jpeg,.png",
	      onSuccess:function(files,data,xhr,pd)
	        {
	           var data_modified = base_url+"upload_caster/image_video/"+user_id+"/"+data;
	           var new_html = '<div class="col-4 col-md-3 col-lg-2 p-0 no-gutters"><img src="'+data_modified+'" width="100%" height="100" class="pr-1 pb-1 select_media image pointer"></div>';
	           $("#image_block .upload_block").prepend(new_html);
	           $('.select_media.image[src="'+data_modified+'"]').click();
	        }
	  });

    $("#video_url_upload").uploadFile({
        url:base_url+"instagram_poster/image_video_upload_video",
        fileName:"myfile",
        maxFileSize:100*1024*1024,
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: false,
        multiple:false,
        acceptFiles:".mov,.mpeg4,.mp4,.avi,.wmv,.mpegps,.flv,.3gpp,.webm",
        onSuccess:function(files,data,xhr,pd)
          {
             var data_modified = base_url+"upload_caster/image_video/"+user_id+"/"+data;
             var new_html = '<div class="col-4 col-md-3 col-lg-2 p-0 no-gutters"><video src="'+data_modified+'" width="100%" height="100" class="pr-1 pb-1 select_media video pointer border"></video></div>';
             $("#video_block .upload_block").prepend(new_html);
             $('.select_media.video[src="'+data_modified+'"]').click();
          }
    }); 

    $(document).on('click','.delete_media',function(e){
        e.preventDefault();
        swal({
          title: global_lang_are_you_sure,
          text: global_lang_delete_confirmation,
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) 
          {
            var file_url = $(this).attr('data-src');
	      $(this).addClass('btn-progress');
	      $.ajax({
	       type:'POST' ,
	       context : this,
	       url: base_url+"instagram_poster/image_video_delete_file",
	       data: {file_url:file_url},
	       success:function(response)
	       {
	         $(this).removeClass('btn-progress');
	       	 $('.select_media[src="'+file_url+'"]').parent().remove();
	       	 if($(this).hasClass('image'))
	       	 {
	       	 	$("#image_url").val('').blur();
	       	 }
					 else
					 {
					 	$("#video_url").val('').blur();
					 }
					 
					 $(".ajax-file-upload-statusbar").remove();
	       }

	      });
          } 
        });

        
    });

    $(document).on('keyup','.emojionearea-editor',function(){
					message_change();        	
    });

    $(document).on('blur','.emojionearea-editor',function(){
					message_change();        	
    });

    $(document).on('blur','#image_url',function(){

        var link=$("#image_url").val();
        link = link.trim();    	        
      	$(".preview_only_img_block").show();
      	$(".preview_video_block").hide();
      	if(link!="")
      	{
      		$("#image_edit_block a").attr('data-src',link);
      		$("#image_edit_block").removeClass("d_none");
      	}
      	else
      	{
      		$("#image_edit_block a").attr('data-src','');
      		$("#image_edit_block").addClass("d_none");
      	}
      	if(link=='')  link = base_url+'assets/img/example-image.jpg';
        $(".only_preview_img").attr("src",link);

    });

    $(document).on('blur','#video_url',function(){
      	var link=$("#video_url").val();
      	link = link.trim();
          var write_html='<video width="100%" height="auto" controls><source src="'+$("#video_url").val()+'">Your browser does not support the video tag.</video>';
          $(".preview_video_block").html(write_html).show();
          $(".preview_only_img_block").hide();
          if(link!="")
          {
          	$("#video_edit_block a").attr('data-src',link);
          	$("#video_edit_block").removeClass("d_none");
          }
          else
      	 {
      		$("#video_edit_block a").attr('data-src','');
      		$("#video_edit_block").addClass("d_none");
      	}

    });

    $(document).on('change','#selected_social_post_media_type',function(){        	
      	$('.post_type.active').click();
    });


    $(document).on('click','.post_type',function(){

      	var post_type=$(this).attr("id");
      	$('.emojionearea-editor').blur();

      	var media_type = 'facebook';
      	if ($('#selected_social_post_media_type').is(':checked')){
      			$("#preview_for_facebook").hide();
      			$("#preview_for_instagram").show();
      			if(post_type=='text_post' || post_type=='link_post') $("#not_supported").show();
      			else $("#not_supported").hide();
      			media_type = 'instagram';
      	}
      	else
      	{
      			$("#preview_for_facebook").show();
      			$("#preview_for_instagram").hide();
      			$("#not_supported").hide();
      	}
      	
      	if(post_type=="text_post")
      	{
      		$("#link_block,#image_block,#video_block").hide();
      		$("#not_supported").hide();
      		if(media_type=='instagram') {
      			$("#not_supported").show();
      			$("#preview_for_instagram .preview_video_block,#preview_for_instagram .preview_only_img_block").hide();
      			$("#post_to_instagram").show();
      		}
      		else
      		{
        		$('.post_type').removeClass("active");
        		$('#submit_post').attr("submit_type","text_submit");
        		$('#submit_post_hidden').val("text_submit");

        		$(".preview_img_block").hide();
        		$(".preview_video_block").hide();
        		$(".preview_only_img_block").hide();

        		$('.post_to_accounts').prop('checked', false);
        		$('#check_all_accounts').prop('checked', false);
        		$("#post_to_instagram").hide();
      		}
      	}

      	else if(post_type=="link_post")
      	{
        	$("#image_block,#video_block").hide();
      		$("#not_supported").hide();
      		if(media_type=='instagram') {
      			$("#not_supported").show();
      			$("#preview_for_instagram .preview_video_block,#preview_for_instagram .preview_only_img_block").hide();
      			$("#post_to_instagram").show();
      		}
      		else
      		{
        		$("#link_block").show();
        		$('#submit_post').attr("submit_type","link_submit");
        		$('#submit_post_hidden').val("link_submit");

        		$('.post_to_accounts').prop('checked', false);
        		$('#check_all_accounts').prop('checked', false);
        		$("#post_to_instagram").hide();

	    		  $(".preview_img_block").show();
	    		  $(".preview_video_block").hide();
	    		  $(".preview_only_img_block").hide();

						$("#link").blur();
					}

      	}

      	else if(post_type=="image_post")
      	{
      		$("#link_block,#video_block").hide();
      		$("#image_block").show();
      		$('.post_type').removeClass("active");
      		$('#submit_post').attr("submit_type","image_submit");
      		$('#submit_post_hidden').val("image_submit");

      		$(".preview_img_block").hide();
      		$(".preview_video_block").hide();
      		$(".preview_only_img_block").show();

      		$("#post_to_instagram").show();

      		var image_url_pre=$("#image_url").val();
		    	if(image_url_pre!="")
		    	{
		    		var image_url_array = image_url_pre.split(',');
		    		$(".only_preview_img").attr("src",image_url_array[0]);
		    	}
        }
        else if(post_type=="video_post")
        {
        		$("#link_block,#image_block").hide();
        		$("#video_block").show();
        		$('.post_type').removeClass("active");
        		$('#submit_post').attr("submit_type","video_submit");
	        	$('#submit_post_hidden').val("video_submit");

		    		$(".preview_img_block").hide();
		    		$(".preview_only_img_block").hide();
		    		$(".preview_video_block").show();

		    		$("#post_to_instagram").show();

			    	var video_url_pre=$("#video_url").val();
			    	if(video_url_pre!="")
			    	{
			    		var write_html='<video width="100%" height="auto" style="border:1px solid #ccc;" controls poster="'+$("#video_thumb_url").val()+'"><source src="'+video_url_pre+'">Your browser does not support the video tag.</video>';
			    		$(".preview_video_block").html(write_html);

			    	}

      	}
      	$(this).addClass("active");
      });

      $(document).on('blur','#link',function(){
        var link=$("#link").val();
        if(link=='') return;
      	// $(".previewLoader").show();
      	$(".preLoader").show();
      	$(".preLoader").html('<i class="fas fa-spinner fa-spin blue text-center" style="font-size:50px;"></i>');
      	$('.preview_img_block img').hide();
      	$('.preview_og_info').hide();
        $.ajax({
            type:'POST' ,
            url:instagram_meta_info_grabber_url,
            data:{link:link},
            dataType:'JSON',
            success:function(response){

                $(".preview_img_block img").attr("src",response.image);

                if(typeof(response.image)==='undefined' || response.image=="")
                $(".preview_img_block img").hide();
                else $(".preview_img_block img").show();

                $("#link_caption").val(response.title);
                $(".preview_og_info_title").html(response.title);

                $("#link_description").val(response.description);
                $(".preview_og_info_desc").html(response.description);

                var link_author=link;
                var link_author = link_author.replace("http://", "");
                var link_author = link_author.replace("https://", "");
                if(typeof(response.image)!='undefined' && response.author!=="") link_author=link_author+" | "+response.author;

                $(".preview_og_info_link").html(link_author);

            	$(".preview_img_block").show();
            	$('.preview_og_info').show();
            	$(".preLoader").html("");
            	$(".preLoader").hide();
            }
        });
    });	 

	});
});