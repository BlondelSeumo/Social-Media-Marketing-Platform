<?php 
	$this->load->view("include/upload_js");
	$image_upload_limit = 1; 
	if($this->config->item('facebook_poster_image_upload_limit') != '')
	$image_upload_limit = $this->config->item('facebook_poster_image_upload_limit'); 
	
	$video_upload_limit = 10; 
	if($this->config->item('facebook_poster_video_upload_limit') != '')
	$video_upload_limit = $this->config->item('facebook_poster_video_upload_limit');
	
?>
<style type="text/css">
	.card{margin-bottom:0;border-radius: 0;}
	.main_card{box-shadow: none !important;height: 100%;}
	.collef{padding-right: 0px; border-right:1px solid #f9f9f9;}
	.colmid{padding-left: 0px;}
	.card .card-header input{max-width: 100% !important;}
	.card .card-header h4 a{font-weight: 700 !important;}
	::placeholder{color: white !important;}
	.full-documentation{cursor: pointer;}
	.input-group-prepend{margin-left:-1px;}
	.input-group-text{background: #eee;}
	.schedule_block_item label,label{color:#34395e !important;font-size:12px !important;font-weight:600 !important;letter-spacing: .5px !important;}
	.card-body #post_tab_content { border:solid 1px #dee2e6;border-top:0 !important;padding:25px 20px; }
</style>


<?php
 	if($this->session->userdata("user_type")=="Admin" || in_array(74,$this->module_access)) $like_comment_Share_reply_block_class="";
 	else $like_comment_Share_reply_block_class="hidden";
?>	

<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><a href="<?php echo base_url("ultrapost"); ?>"><?php echo $this->lang->line("Facebook Poster"); ?></a></div>
			<div class="breadcrumb-item"><a href='<?php echo base_url("ultrapost/text_image_link_video"); ?>'><?php echo $this->lang->line("Text/Image/Link/Video Posts"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>
	
	<div class="section-body">
		<div class="row">
			<div class="col-12 col-md-7 collef">
				<div class="card main_card">
					<div class="card-header" style="border-bottom: 0;padding-bottom:0 !important;">
						<ul class="nav nav-tabs" role="tablist" style="width:100% !important">
							<li class="nav-item">
								<a id="text_post" class="nav-link post_type active" data-toggle="tab" href="#textPost" role="tab" aria-selected="false"><i class="fas fa-file-alt"></i> <?php echo $this->lang->line('Text') ?></a>
							</li>              
							<li class="nav-item">
								<a id="link_post" class="nav-link post_type" data-toggle="tab" href="#linkPost" role="tab" aria-selected="true"><i class="fas fa-link"></i> <?php echo $this->lang->line("Link") ?></a>
							</li>
							<li class="nav-item">
								<a id="image_post" class="nav-link post_type" data-toggle="tab" href="#imagePost" role="tab" aria-selected="false"><i class="fas fa-image"></i> <?php echo $this->lang->line("Image"); ?></a>
							</li>
							<li class="nav-item">
								<a id="video_post" class="nav-link post_type" data-toggle="tab" href="#videoPost" role="tab" aria-selected="false"><i class="fas fa-video"></i> <?php echo $this->lang->line("Video"); ?></a>
							</li>
						</ul>
					</div>
		          	<div class="card-body" style="padding-top:0 !important;margin-top: -3px;">
			          	<!-- tab body started -->
			          	<div class="tab-content" id="post_tab_content">
							<form action="#" enctype="multipart/form-data" id="auto_poster_form" method="post">
								<div class="form-group">
									<label><?php echo $this->lang->line('Campaign Name');?></label>
									<input type="input" class="form-control"  name="campaign_name" id="campaign_name">
								</div>

								<div class="form-group">
									<label><?php echo $this->lang->line('Message'); ?></label>
									<a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("support Spintax"); ?>, Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
									<textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line('Type your message here...');?>"></textarea>
								</div>

								<div id="link_block">
									<div class="form-group">
										<label><?php echo $this->lang->line('Paste link');?></label>
										<input class="form-control" name="link" id="link"  type="text">
									</div>

									<div class="form-group hidden">
										<label><?php echo $this->lang->line('Link caption');?></label>
										<input class="form-control" name="link_caption" id="link_caption" type="text">
									</div>
									<div class="form-group hidden">
										<label><?php echo $this->lang->line('Link description');?></label>
										<textarea class="form-control" name="link_description" id="link_description"></textarea>
									</div>
								</div>

								<div id="image_block">
									<div class="form-group">
										<label><i class="fas fa-image"></i> <?php echo $this->lang->line('Image URL');?></label>
										<input class="form-control" name="image_url" id="image_url" type="text">
									</div>
									<div class="form-group">
			                            <div id="image_url_upload"><?php echo $this->lang->line('Upload');?></div>
			                        	<br/>
									</div>
								</div>

								<div id="video_block">
									<div class="row">
										<div class="col-12 col-md-6">
											<div class="form-group">
												<label><?php echo $this->lang->line('Video URL');?></label>
												<input class="form-control" name="video_url" id="video_url" type="text">
											</div>
											<div class="form-group">
					                            <div id="video_url_upload"><?php echo $this->lang->line('Upload');?></div>
					                            <br/>
					                        </div>
										</div>
										<div class="col-12 col-md-6">
											<div class="form-group">
												<label><?php echo $this->lang->line('Video thumbnail URL');?></label>
												<input class="form-control" name="video_thumb_url" id="video_thumb_url" type="text">
											</div>
											<div class="form-group">
					                            <div id="video_thumb_url_upload"><?php echo $this->lang->line('Upload');?></div>
					                            <br/>
											</div>
										</div>
									</div>
									
									
								</div>

								<div class="row">
									<div class="col-12 col-md-6">
										<div class="form-group">
											<label><?php echo $this->lang->line("Post to pages");?>
												<a href="#" data-placement="right" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Select Page"); ?>" data-content="<?php echo $this->lang->line("Select the page you want to post. You can select multiple page to post."); ?>"><i class='fa fa-info-circle'></i> </a>
											</label>
											<select multiple class="form-control select2" id="post_to_pages" name="post_to_pages[]" style="width:100%;">
											<?php
												foreach($fb_page_info as $key=>$val)
												{
													$id=$val['id'];
													$page_name=$val['page_name'];
													echo "<option value='{$id}'>{$page_name}</option>";
												}
											 ?>
											</select>
										</div>
									</div>

									<div class="col-12 col-md-6">
										<div class="form-group">
											<label>
												<?php echo $this->lang->line('Auto Reply Template') ?> 
												<a href="#" data-placement="right" data-toggle="popover" data-trigger="focus" data-content="<?php echo $this->lang->line("Only Works For Pages."); ?>"><i class='fa fa-info-circle'></i> </a>
											</label>
											<select  class="form-control select2" id="auto_reply_template" name="auto_reply_template" style="width:100%;">
											<?php
												echo "<option value='0'>{$this->lang->line('Please select a template')}</option>";
												foreach($auto_reply_template as $key=>$val)
												{
													$id=$val['id'];
													$group_name=$val['ultrapost_campaign_name'];
													echo "<option value='{$id}'>{$group_name}</option>";
												}
											 ?>
											</select>
										</div>
									</div>
									
									<?php if ($facebook_poster_group != '0' && $this->is_group_posting_exist): ?>
									<div class="col-12 col-md-6">
										<div class="form-group">
											<label><?php echo $this->lang->line("Post to groups");?></label>
											<select multiple class="form-control select2" id="post_to_groups" name="post_to_groups[]" style="width: 100%;">	
												<?php
													foreach($fb_group_info as $key=>$val)
													{	
														$id=$val['id'];
														$group_name=$val['group_name'];
														echo "<option value='{$id}'>{$group_name}</option>";								
													}
												 ?>						
											</select>
											<small class="label label-light red full-documentation"><i class="fa fa-info-circle"></i> <?php echo $this->lang->line("For posting to group, you must need to install the APP in your groups. Click here to read the full instruction."); ?></small>
										</div>
									</div>	
									<?php endif; ?>

									<div class="col-12 col-md-6">
										<div class="form-group">
											<label><?php echo $this->lang->line("Posting Time") ?>
												<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Posting Time") ?>" data-content="<?php echo $this->lang->line("If you schedule a campaign, system will automatically process this campaign at mentioned time and time zone. Schduled campaign may take upto 1 hour longer than your schedule time depending on server's processing.") ?>"><i class='fa fa-info-circle'></i> </a>
											</label><br>
										  	<label class="custom-switch mt-2">
												<input type="checkbox" name="schedule_type" value="now" id="schedule_type" class="custom-switch-input" checked>
												<span class="custom-switch-indicator"></span>
												<span class="custom-switch-description"><?php echo $this->lang->line('Post Now');?></span>
										  	</label>
										</div>
									</div>
								</div>	

								<div class="row">
									<div class="schedule_block_item col-12 col-md-6">
										<div class="form-group">
											<label><?php echo $this->lang->line('Schedule time'); ?></label>
											<input placeholder="Time"  name="schedule_time" id="schedule_time" class="form-control datepicker_x" type="text"/>
										</div>
									</div>

									<div class="schedule_block_item col-12 col-md-6">
										<div class="form-group">
											<label><?php echo $this->lang->line('Time zone'); ?></label>
											<?php
											$time_zone[''] =$this->lang->line('Please Select');
											echo form_dropdown('time_zone',$time_zone,$this->config->item('time_zone'),' class="form-control select2" id="time_zone" required style="width:100%;"');
											?>
										</div>
									</div>

									<div class=" schedule_block_item col-12 col-md-6">
										<div class="input-group">
										  	<label class="input-group-addon"><?php echo $this->lang->line('repost this post'); ?></label>
										  	<div class="input-group">
					                          	<input type="number" class="form-control" name="repeat_times" id="repeat_times" aria-describedby="basic-addon2">
					                          	<div class="input-group-prepend">
						                            <div class="input-group-text"><?php echo $this->lang->line('Times'); ?></div>
					                          	</div>
				                        	</div>
										</div>
									  	
									</div>
									<div class="col-12 col-md-6">
										<div class="schedule_block_item">
											<div class="form-group">
												<label><?php echo $this->lang->line('time interval'); ?></label>
												<?php
													$time_interval[''] = $this->lang->line('Please Select Periodic Time Schedule');
													echo form_dropdown('time_interval',$time_interval,set_value('time_interval'),' class="form-control select2" id="time_interval" required style="width:100%;"');
												?>
											</div>
										</div>
									</div>
								</div>
								
								<div class="clearfix"></div>

								<div class="card-footer padding-0">
									<input type="hidden" name="submit_post_hidden" id="submit_post_hidden" value="text_submit">
									<button class="btn btn-lg btn-primary" submit_type="text_submit" id="submit_post" name="submit_post" type="button"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Create Campaign") ?> </button>
									<a class="btn btn-lg btn-light float-right" onclick='goBack("ultrapost/text_image_link_video",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
								</div>

							</form>
						</div>
			        </div>
	          	</div>          
	        </div>
			<!-- preview section -->
			<div class="col-12 col-md-5 colmid d-none d-sm-block">
				<div class="card main_card">
					<div class="card-header"><h4><i class="fab fa-facebook"></i> <?php echo $this->lang->line('Preview'); ?></h4></div>
		          	<div class="card-body">
		          		<div class="section-title text-info" style="margin: -30px 0px 10px 0px; font-weight: normal;">
		          			<?php echo $this->lang->line('This preview may differ with actual post.'); ?>
		          		</div>
			          	<?php $profile_picture="https://graph.facebook.com/me/picture?access_token={$fb_user_info[0]['access_token']}&width=150&height=150"; ?>
						<ul class="list-unstyled list-unstyled-border">
							<li class="media">
							  <img class="mr-3 rounded-circle" width="50" src="<?php echo $profile_picture;?>" alt="avatar">
							  <div class="media-body">
							    <h6 class="media-title"><a href="#"><?php echo $fb_user_info[0]['name'];?></a></h6>
							    <div class="text-small text-muted">
							    	<?php echo isset($app_info[0]['app_name']) ? $app_info[0]['app_name'] : $this->config->item("product_short_name");?> 
							    	<div class="bullet"></div> 
							    	<span class="text-primary">Now</span></div>
							  </div>
							</li>
						</ul>

			          	<span class="preview_message"><br/></span>

			          	<div class="preview_video_block">
		          			<video controls="" width="100%" poster="" style="border:1px solid #ccc"><source  src=""></source></video>
		          			<br/>
			          		<div class="video_preview_og_info_desc inline-block">
			          		</div>
			          	</div>

			          	<div class="preview_img_block">
			          		<div class="preLoader text-center" style="display: none;"></div>
			          		<img src="<?php echo base_url('assets/images/demo_image.png');?>" class="preview_img" alt="No Image Preview">
			          		<div class="preview_og_info">
			          			<div class="preview_og_info_title inline-block"></div>
			          			<div class="preview_og_info_desc inline-block">
			          			</div>
			          			<div class="preview_og_info_link inline-block">
			          			</div>
			          		</div>
			          	</div>

			          	<div class="preview_only_img_block">
			          		<img src="<?php echo base_url('assets/images/demo_image.png');?>" class="only_preview_img" alt="No Image Preview">
			          	</div>
		          	</div>          
		        </div>
			</div>
		</div>
	</div>
</section>

<script>

	$("document").ready(function()	{
	
		var emoji_message_div =	$("#message").emojioneArea({
        	autocomplete: false,
			pickerPosition: "bottom",
			//hideSource: false,
     	 });

		var today = new Date();
		var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
		$('.datepicker_x').datetimepicker({
			theme:'light',
			format:'Y-m-d H:i:s',
			formatDate:'Y-m-d H:i:s',
			minDate: today,
			maxDate: next_date

		})

		$('[data-toggle="popover"]').popover(); 
		$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
		 

		var base_url="<?php echo base_url();?>";


		var makeScheduleValEmptyifscheduleisNow = $("input[name=schedule_type]:checked").val();
		if(makeScheduleValEmptyifscheduleisNow == 'now')
			$("#schedule_time").val("");

        $("#link_block,#image_block,#video_block,.auto_share_post_block_item,.auto_reply_block_item,.auto_comment_block_item,.schedule_block_item,.preview_video_block,.preview_img_block,.preview_only_img_block").hide();

        $(document).on('change','input[name=auto_share_post]',function(){
        	if($("input[name=auto_share_post]:checked").val()=="1")
        	$(".auto_share_post_block_item").show();
        	else $(".auto_share_post_block_item").hide();
        });

        $(document).on('change','input[name=auto_private_reply]',function(){
        	if($("input[name=auto_private_reply]:checked").val()=="1")
        	$(".auto_reply_block_item").show();
        	else $(".auto_reply_block_item").hide();
        });

         $(document).on('change','input[name=auto_comment]',function(){
        	if($("input[name=auto_comment]:checked").val()=="1")
        	$(".auto_comment_block_item").show();
        	else $(".auto_comment_block_item").hide();
        });

        $(document).on('change','input[name=schedule_type]',function(){

        	var scheduleType = $("input[name=schedule_type]:checked").val();

        	if(typeof(scheduleType) =="undefined")
        		$(".schedule_block_item").show();
        	else
        	{
        		$("#schedule_time").val("");
        		$("#time_zone").val("");
        		$("#repeat_times").val("");
        		$("#time_interval").val("");
        		$(".schedule_block_item").hide();
        	}
        });

        var message_pre=$("#message").val();
    	message_pre=message_pre.replace(/[\r\n]/g, "<br />");
    	if(message_pre!="")
    	{
    		message_pre=message_pre+"<br/><br/>";
    		$(".preview_message").html(message_pre);
    	}


        $(document).on('click','.post_type',function(){

        	var post_type=$(this).attr("id");
        	
        	if(post_type=="text_post")
        	{
        		$("#link_block,#image_block,#video_block").hide();
        		$('.post_type').removeClass("active");
        		$('#submit_post').attr("submit_type","text_submit");
        		$('#submit_post_hidden').val("text_submit");
        		$(".preview_img_block").hide();
        		$(".preview_video_block").hide();
        		$(".preview_only_img_block").hide();
        		$(".demo_preview").hide();
        	}

        	else if(post_type=="link_post")
        	{
        		$("#image_block,#video_block").hide();
        		$("#link_block").show();
        		$('.post_type').removeClass("active")
        		$('#submit_post').attr("submit_type","link_submit");
        		$('#submit_post_hidden').val("link_submit");

        		$(".demo_preview").hide();
	    		$(".preview_video_block").hide();
	    		$(".preview_img_block").show();
	    		$(".preview_only_img_block").hide();

        		var link_pre=$("#link").val();

		    	if(link_pre!="")
		    	{
		    		$(".preview_og_info_link").html(link_pre);

		    	}
		    	var link_preview_image_pre=$("#link_preview_image").val();
		    	if(link_preview_image_pre!="")
		    	{
		    		$(".preview_img").attr("src",link_preview_image_pre);
		    	}
				var link_caption_pre=$("#link_caption").val();
		    	if(link_caption_pre!="")
		    	{
		    		$(".preview_og_info_title").html(link_caption_pre);
		    	}
		    	var link_description_pre=$("#link_description").val();
		    	if(link_description_pre!="")
		    	{
		    		$(".preview_og_info_desc").html(link_description_pre);
		    	}

				$("#link").blur();

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
        		$(".demo_preview").hide();
        		$(".preview_only_img_block").show();

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
	    		$(".demo_preview").hide();
	    		$(".preview_img_block").hide();
	    		$(".preview_only_img_block").hide();
	    		$(".preview_video_block").show();
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
        	$('.preview_img').hide();
        	$('.preview_og_info').hide();
	        $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>ultrapost/text_image_link_video_meta_info_grabber",
	            data:{link:link},
	            dataType:'JSON',
	            success:function(response){

	                $("#link_preview_image").val(response.image);
	                $(".preview_img").attr("src",response.image);
	                $('.preview_img').show();

	                if(typeof(response.image)==='undefined' || response.image=="")
	                $(".preview_img").hide();
	                else $(".preview_img").show();

	                $("#link_caption").val(response.title);
	                $(".preview_og_info_title").html(response.title);

	                $("#link_description").val(response.description);
	                $(".preview_og_info_desc").html(response.description);

	                var link_author=link;
	                var link_author = link_author.replace("http://", "");
	                var link_author = link_author.replace("https://", "");
	                if(typeof(response.image)!='undefined' && response.author!=="") link_author=link_author+" | "+response.author;

	                $(".preview_og_info_link").html(link_author);

	                if(response.image==undefined || response.image=="")
	                $(".preview_img").hide();
	                else $(".preview_img").show();

	            	$(".preview_img_block").show();
	            	$('.preview_og_info').show();
	            	$(".preLoader").html("");
	            	$(".preLoader").hide();
	            }
	        });
        });
		
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
		
		

        $(document).on('keyup','.emojionearea-editor',function(){
		
        	var message=$("#message").val();
			message=htmlspecialchars(message);
			message=message.replace(/[\r\n]/g, "<br />");
			
        	if(message!="")
        	{
        		message=message+"<br/><br/>";
        		$(".preview_message").html(message);
        		$(".demo_preview").hide();
        	}
        });

        $(document).on('blur','#link_preview_image',function(){
        	var link=$("#link_preview_image").val();
            $(".preview_img").attr("src",link).show();
        	$(".preview_img_block").show();
        });

        $(document).on('keyup','#link_caption',function(){
        	var link_caption=$("#link_caption").val();
			$(".preview_og_info_title").html(link_caption);
			$(".preview_img_block").show();
        });

        $(document).on('keyup','#link_description',function(){
        	var link_description=$("#link_description").val();
			$(".preview_og_info_desc").html(link_description);
			$(".preview_img_block").show();
        });


        $(document).on('blur','#image_url',function(){

	        var link=$("#image_url").val();
	        var image_url_array = link.split(',');
            $(".only_preview_img").css("border","1px solid #ccc");
            $(".only_preview_img").attr("src",image_url_array[0]);
        	$(".preview_only_img_block").show();

        });



        $(document).on('blur','#video_thumb_url',function(){
        	var link=$("#video_thumb_url").val();
	        if(link!='')
	        {
	            $(".previewLoader").show();
	            var write_html='<video width="100%" height="auto" style="border:1px solid #ccc;" controls poster="'+$("#video_thumb_url").val()+'"><source src="'+$("#video_url").val()+'">Your browser does not support the video tag.</video>';
	            $(".preview_video_block").html(write_html);
	            $(".previewLoader").hide();
	        }

        });


 		$(document).on('blur','#video_url',function(){
        	var link=$("#video_url").val();
	        if(link!='')
	        {
 				$(".previewLoader").show();
	            $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>ultrapost/text_image_link_video_youtube_video_grabber",
	            data:{link:link},
	            success:function(response){
	               if(response!="")
	               {
	               	 	if(response=='fail')
	               	 	{
	               	 		alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('Video URL is invalid or this video is restricted from playback on certain sites.'); ?>",function(){ });
	               	 		$("#video_url").val("");
	               	 	}
	               	 	else
	               	 	{
	               	 		$(".previewLoader").show();
	               	 		var write_html='<video width="100%" height="auto" style="border:1px solid #ccc;" controls poster="'+$("#video_thumb_url").val()+'"><source src="'+response+'">Your browser does not support the video tag.</video>';
	            			$(".preview_video_block").html(write_html);
	            			$(".previewLoader").hide();
	               	 	}
	               	 	$(".previewLoader").hide();
	               }
	            }
	        });




	        }

        });

        var image_upload_limit = "<?php echo $image_upload_limit; ?>";
        var video_upload_limit = "<?php echo $video_upload_limit; ?>";

 		var image_list = [];
        $("#image_url_upload").uploadFile({
	        url:base_url+"ultrapost/text_image_link_video_upload_image_only",
	        fileName:"myfile",
	        maxFileSize:image_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:true,
	        maxFileCount:5,
	        acceptFiles:".png,.jpg,.jpeg",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('ultrapost/text_image_link_video_delete_uploaded_file');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) {
                    	var item_to_delete = base_url+"upload_caster/text_image_link_video/"+data;
                    	image_list = image_list.filter(item => item !== item_to_delete);
                    	if(image_list.length > 0)
                    	$(".only_preview_img").attr("src",image_list[0]);
                    	else
                    	$(".only_preview_img").attr("src",'');
                    	$("#image_url").val(image_list.join());
                    });

	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               var data_modified = base_url+"upload_caster/text_image_link_video/"+data;
	           	   image_list.push(data_modified);
	               $("#image_url").val(image_list.join());
	               $(".only_preview_img").attr("src",data_modified);
	           }
	    });



	    $("#link_preview_upload").uploadFile({
	        url:base_url+"ultrapost/text_image_link_video_upload_link_preview",
	        fileName:"myfile",
	        maxFileSize:image_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1,
	        acceptFiles:".png,.jpg,.jpeg",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('ultrapost/text_image_link_video_delete_uploaded_file');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) {
                    });

	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               var data_modified = base_url+"upload_caster/text_image_link_video/"+data;
	               $("#link_preview_image").val(data_modified);
	               $(".preview_img").attr("src",data_modified);
	           }
	    });

		$("#video_url_upload").uploadFile({
	        url:base_url+"ultrapost/text_image_link_video_upload_video",
	        fileName:"myfile",
	        maxFileSize:video_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1,
	        acceptFiles:".3g2,.3gp,.3gpp,.asf,.avi,.dat,.divx,.dv,.f4v,.flv,.m2ts,.m4v,.mkv,.mod,.mov,.mp4,.mpe,.mpeg,.mpeg4,.mpg,.mts,.nsv,.ogm,.ogv,.qt,.tod,.ts,.vob,.wmv",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('ultrapost/text_image_link_video_delete_uploaded_file');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) {
                    });

	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               var data_modified = base_url+"upload_caster/text_image_link_video/"+data;
	               var write_html='<video width="100%" height="auto" style="border:1px solid #ccc;" controls poster="'+$("#video_thumb_url").val()+'"><source src="'+data_modified+'">Your browser does not support the video tag.</video>';
	               $(".preview_video_block").html(write_html);
	               $("#video_url").val(data_modified);
	           }
	    });

	    $("#video_thumb_url_upload").uploadFile({
	        url:base_url+"ultrapost/text_image_link_video_upload_video_thumb",
	        fileName:"myfile",
	        maxFileSize:image_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1,
	        acceptFiles:".png,.jpg,.jpeg",
	         deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('ultrapost/text_image_link_video_delete_uploaded_file');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) {
                    });

	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               var data_modified = base_url+"upload_caster/text_image_link_video/"+data;
	               $("#video_thumb_url").val(data_modified);
	               var write_html='<video width="100%" height="auto" style="border:1px solid #ccc;" controls poster="'+data_modified+'"><source src="'+$("#video_url").val()+'">Your browser does not support the video tag.</video>';
	               $(".preview_video_block").html(write_html);
	           }
	    });



	    $(document).on('click','#submit_post',function(){

        	var post_type=$(this).attr("submit_type");

        	if(post_type=="text_submit")
        	{
        		if($("#message").val()=="")
        		{
        			swal('<?php echo $this->lang->line("Warning"); ?>',"<?php echo $this->lang->line('Please type a message to post.');?>", 'warning');
        			return;
        		}
        	}

        	else if(post_type=="link_submit")
        	{
        		if($("#link").val()=="")
        		{
        			swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please paste a link to post.');?>", 'warning');
        			return;
        		}
        	}

        	else if(post_type=="image_submit")
        	{
        		if($("#image_url").val()=="")
        		{
        			swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please paste an image url or uplaod an image to post.');?>", 'warning');
        			return;
        		}
        	}

        	else if(post_type=="video_submit")
        	{
        		if($("#video_url").val()=="")
        		{
        			swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please paste an video url or uplaod an video to post.');?>", 'warning');
        			return;
        		}
        	}


        	var post_to_profile = $("input[name=post_to_profile]:checked").val();
        	var post_to_pages = $("#post_to_pages").val();
        	var post_to_groups = $("#post_to_groups").val();

        	if((post_to_pages=='' || typeof(post_to_pages) =='undefined') && (post_to_groups=='' || typeof(post_to_groups) == 'undefined'))
        	{
        		swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select pages or groups to publish this post.');?>", 'warning');
        		return;
        	}

        	var schedule_type = $("input[name=schedule_type]:checked").val();
        	var schedule_time = $("#schedule_time").val();
        	var time_zone = $("#time_zone").val();

        	if(typeof(schedule_type)=='undefined' && (schedule_time=="" || time_zone==""))
        	{
        		swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select schedule time/time zone.');?>", 'warning');
        		return;
        	}

        	$(this).addClass('btn-progress')
        	var that = $(this);

		      var queryString = new FormData($("#auto_poster_form")[0]);
		      $.ajax({
		       type:'POST' ,
		       url: base_url+"ultrapost/text_image_link_video_add_auto_post_action",
		       data: queryString,
		       dataType : 'JSON',
		       // async: false,
		       cache: false,
		       contentType: false,
		       processData: false,
		       success:function(response)
		       {
		       		$(that).removeClass('btn-progress');

		         	var report_link="<a href='"+base_url+"ultrapost/text_image_link_video'> <?php echo $this->lang->line('Click here to see report'); ?></a>";

		         	if(response.status=="1")
			        {
			        	var span = document.createElement("span");
			        	span.innerHTML = report_link;
			        	swal({ title:response.message, content:span,icon:'success'});
			        }
			        else
			        {
			        	var span = document.createElement("span");
			        	span.innerHTML = report_link;
			        	swal({ title:response.message, content:span,icon:'error'});
			        }

		       }

		      });

        });


		
		$(document).on('click','.full-documentation',function(){
			$("#full-documentation-modal").modal();
		})




    });



</script>


<div class="modal fade" id="response_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $this->lang->line('Auto Post Campaign Status'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert text-center" id="response_modal_content">

				</div>
			</div>
		</div>
	</div>
</div>



<style type="text/css" media="screen">
	/* .box-header{border-bottom:1px solid #ccc !important;margin-bottom:15px;} */
	/* .box-primary{border:1px solid #ccc !important;} */
	/* .box-footer{border-top:1px solid #ccc !important;} */
	.padding-5{padding:5px;}
	.padding-20{padding:20px;}
	.box-body,.box-footer{padding:20px;}
	.box-header{padding-left: 20px;}
	.preview
	{
		font-family: helvetica,​arial,​sans-serif;
		padding: 20px;
	}
	/*.preLoader{ margin-bottom:30px !important; }*/
	.preview_cover_img
	{
		width:45px;
		height:45px;
		border: .5px solid #ccc;
	}
	.preview_page
	{
		padding-left: 7px;
		color: #365899;
		font-weight: 700;
		font-size: 14px;
		cursor: pointer;
	}
	.preview_page_sm
	{
		padding-left: 7px;
		padding-top: 7px;
		color: #9197a3;
		font-size: 13px;
		font-weight: 300;
		cursor: pointer;
	}
	.preview_img
	{
		width:100%;
		border: 1px solid #ccc;
		border-bottom: none;
		cursor: pointer;
	}
	.only_preview_img
	{
		width:100%;
		border: 1px solid #ccc;
		cursor: pointer;
	}
	.demo_preview
	{
		width:100%;
		/*border: 1px solid #f5f5f5; */
		cursor: pointer;
	}
	.preview_og_info
	{
		border: 1px solid #ccc;
/*		box-shadow: 0px 0px 2px #ddd;
		-webkit-box-shadow: 0px 0px 2px #ddd;
		-moz-box-shadow: 0px 0px 2px #ddd;*/
		padding: 10px;
		cursor: pointer;

	}
	.preview_og_info_title
	{
		font-size: 23px;
		font-weight: 400;
		font-family: 'Times New Roman',helvetica,​arial;

	}
	.preview_og_info_desc
	{
		margin-top: 5px;
		font-size: 13px;
	}
	.preview_og_info_link
	{
		text-transform: uppercase;
		color: #9197a3;
		margin-top: 7px;
	}
	.post_type
	{
		padding: 10px 12px;
		border: 1px solid <?php echo $THEMECOLORCODE;?>;
		font-weight: bold;
		color: <?php echo $THEMECOLORCODE;?>;
		margin-right: 2px;
	}
	.post_type.active
	{
		background: <?php echo $THEMECOLORCODE;?>;
		/*color: #fff;*/
	}
	.ms-choice span
	{
		padding-top: 2px !important;
	}
	.hidden
	{
		display: none;
	}
	.box-primary
	{
		-webkit-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
	}
	.content-wrapper{background: #fff;}
	.ajax-upload-dragdrop{width:100% !important;}
</style>



<div class="modal fade" id="full-documentation-modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-mega">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('How to install APP in group.'); ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>For posting to your Facebook groups you must need to install the app named "<b><?php echo $current_app_name; ?></b>" in your Facebook group settings. The thumbnail of the app "<b><?php echo $current_app_name; ?></b>" will be similar to the below image.</p>
				<div>					
					<img src="<?php echo $current_app_photo_url; ?>" alt="">
				</div>
				<br/>
				<p>You can follow the below steps to install the app "<b><?php echo $current_app_name; ?></b>" in your Facebook group.</p>
				<ol>
					<li>First, go to your Facebook group and click the "More" button. Then click the "Edit Group Settings" menu from the dropdown list. As shown in the Step 1 section.</li>
					<li>Click on the "Add Apps" button in the Apps section. As shown in the Step 2 section.</li>
					<li>Here a pop-up window will come from where you can browse apps for this group. In the search box type "<b><?php echo $current_app_name; ?></b>" and then click on the app "<b><?php echo $current_app_name; ?></b>" that matches your search result. As shown in Step 3 section.</li>
					<li>After clicking on the app "<b><?php echo $current_app_name; ?></b>" another pop-up windown will appear. Now click on the "Add" button to add install this app in your group. As shown in Step 4 section.</li>
				</ol>
				<br/>
				<h4 class="text-center"><b>Step 1</b></h4><br/>
				<img class="img-responsive" src="<?php echo base_url('assets/images/group_posting_instructions/group_posting_instruction1.png'); ?>" alt="">
				<br/>
				<h4 class="text-center"><b>Step 2</b></h4><br/>
				<img class="img-responsive" src="<?php echo base_url('assets/images/group_posting_instructions/group_posting_instruction2.png'); ?>" alt="">
				<br/>
				<h4 class="text-center"><b>Step 3</b></h4><br/>
				<img class="img-responsive" src="<?php echo base_url('assets/images/group_posting_instructions/group_posting_instruction3.png'); ?>" alt="">
				<br/>
				<h4 class="text-center"><b>Step 4</b></h4><br/>
				<img class="img-responsive" src="<?php echo base_url('assets/images/group_posting_instructions/group_posting_instruction4.png'); ?>" alt="">
				<br/>
				<h3 class="text-center"><b>You are done!</b></h3><br/>
			</div>
		</div>
	</div>
</div>
