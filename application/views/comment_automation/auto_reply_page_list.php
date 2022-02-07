<?php 
	$this->load->view("include/upload_js"); 
	if(ultraresponse_addon_module_exist())	$commnet_hide_delete_addon = 1;
	else $commnet_hide_delete_addon = 0;

	if(addon_exist(201,"comment_reply_enhancers")) $comment_tag_machine_addon = 1;
	else $comment_tag_machine_addon = 0;

	$image_upload_limit = 1; 
	if($this->config->item('autoreply_image_upload_limit') != '')
	$image_upload_limit = $this->config->item('autoreply_image_upload_limit'); 

	$video_upload_limit = 3; 
	if($this->config->item('autoreply_video_upload_limit') != '')
	$video_upload_limit = $this->config->item('autoreply_video_upload_limit');			
?>

<style type="text/css">
	.button-outline
	{
	  background: #fff;
	  border: .5px dashed #ccc;
	}
	.button-outline:hover
	{
	  border: 1px dashed var(--blue) !important;
	  cursor: pointer;
	}
	.multi_layout{margin:0;background: #fff}
	.multi_layout .card{margin-bottom:0;border-radius: 0;}
	.multi_layout p, .multi_layout ul:not(.list-unstyled), .multi_layout ol{line-height: 15px;}
	.multi_layout .list-group li{padding: 15px 10px 12px 25px;}
	.multi_layout{border:.5px solid #dee2e6;}
	.multi_layout .collef,.multi_layout .colmid,.multi_layout .colrig{padding-left: 0px; padding-right: 0px;}
	.multi_layout .collef,.multi_layout .colmid{border-right: .5px solid #dee2e6;}
	.multi_layout .main_card{min-height: 500px;box-shadow: none;}
	.multi_layout .collef .makeScroll{max-height: 640px;overflow:auto;}
	.multi_layout .colrig .makeScroll{max-height: 605px;overflow:auto;}
	.multi_layout .list-group .list-group-item{border-radius: 0;border:.5px solid #dee2e6;border-left:none;border-right:none;cursor: pointer;z-index: 0;}
	.multi_layout .list-group .list-group-item:first-child{border-top:none;}
	.multi_layout .list-group .list-group-item:last-child{border-bottom:none;}
	.multi_layout .list-group .list-group-item.active{border:.5px solid var(--blue);}
	.multi_layout .mCSB_inside > .mCSB_container{margin-right: 0;}
	.multi_layout .card-statistic-1{border-radius: 0;}
	.multi_layout h6.page_name{font-size: 14px;}
	.multi_layout .card .card-header input{max-width: 100% !important;}
	.multi_layout .media-title{font-size: 13px;}
	.multi_layout .media-body{padding-left: 15px;}
	.multi_layout .media-body .small{font-size: 10px;color:#000;margin-top:12px;}
	.multi_layout .summary .summary-item{margin-top: 0;}
	.multi_layout .card-primary{margin-top: 35px;margin-bottom: 15px;}
	.multi_layout .product-details .product-name{font-size: 12px;}
	.multi_layout .set_cam_by_post:after {content: none !important;}
	.multi_layout .colrig .media {padding-bottom: 0;}
	.multi_layout .list-unstyled-border li {border-bottom: none;}
	.multi_layout .colmid .card-body {padding: 12px 10px;}
	.multi_layout .colrig .card-body {padding: 12px 20px;}

	.multi_layout .waiting,.modal_waiting {height: 100%;width:100%;display: table;}
    .multi_layout .waiting i,.modal_waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}

    .multi_layout .card .card-header h4 a{font-weight: 700 !important;}
    
    ::placeholder {
      color: #ccc !important;
    }
    .smallspace{padding: 10px 0;}
    .lead_first_name,.lead_last_name,.lead_tag_name{background: #fff !important;}
    .ajax-file-upload-statusbar{width: 100% !important;}
    hr{
       margin-top: 10px;
    }

    .custom-top-margin{
      margin-top: 20px;
    }

    .sync_page_style{
       margin-top: 8px;
    }
    /* .wrapper,.content-wrapper{background: #fafafa !important;} */
    .well{background: #fff;}
    
    .emojionearea, .emojionearea.form-control
    {
    	height: 140px !important;
    }


    .emojionearea.small-height
    {
    	height: 140px !important;
    }

    <?php if($is_rtl) echo '.ajax-file-upload{float: right !important;}';?>

</style>


<section class="section">
	<div class="section-header">
		<h1><i class="fas fa-comments"></i> <?php echo $page_title;?></h1>
		<div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line('Comment Growth Tools'); ?></a></div>
        <div class="breadcrumb-item"><?php echo $page_title ?></div>
      </div>
	  </div>
</section>


<?php if(empty($page_info))
{ ?>
	 
<div class="card" id="nodata">
  <div class="card-body">
    <div class="empty-state">
      <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
      <h2 class="mt-0"><?php echo $this->lang->line("We could not find any page.");?></h2>
      <p class="lead"><?php echo $this->lang->line("Please import account if you have not imported yet.")."<br>".$this->lang->line("If you have already imported account then enable bot connection for one or more page to continue.") ?></p>
      <a href="<?php echo base_url('social_accounts'); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line("Continue");?></a>
    </div>
  </div>
</div>

<?php 
}
else
{ ?>
	

	<?php if(file_exists(APPPATH.'show_comment_automation_message.txt') && $this->session->userdata('user_type') == 'Admin') : ?>
	<div class="row">
		<div class="col-12">
			<div class="alert alert-light alert-has-icon  alert-dismissible show fade" style="margin-bottom:30px"  id="hide_comment_automation_message" >
	          <div class="alert-icon"><i class="fas fa-hand-paper"></i></div>
	          <div class="alert-body">
	          	 <button class="close" data-dismiss="alert" data-toggle="tooltip" title="<?php echo $this->lang->line("Close this mesage forever"); ?>">
	              	<span>×</span>
	             </button>
	            <div class="alert-title"><?php echo $this->lang->line("Hello admin, please read me first"); ?></div>
	            <?php echo $this->lang->line("Comment automation features will not work until your Facebook app is fully approved and is in live mode."); ?>
	          </div>
	        </div>
		</div>
	</div>
	<?php endif; ?>

	<div class="row multi_layout">

		<div class="col-12 col-md-5 col-lg-3 collef">
		  <div class="card main_card">
		    <div class="card-header">
		      <div class="col-6 padding-0">
		        <h4><?php echo $this->lang->line("Pages"); ?></h4>
		      </div>
		      <div class="col-6 padding-0">            
		        <input type="text" class="form-control float-right" id="search_page_list" onkeyup="search_in_ul(this,'page_list_ul')" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>">
		      </div>
		    </div>
		    <div class="card-body padding-0">
		      <div class="makeScroll">
		        <ul class="list-group" id="page_list_ul">
		          <?php $i=0; foreach($page_info as $value) { ?> 
		            <li class="list-group-item <?php if($i==0) echo 'active'; ?> page_list_item" page_table_id="<?php echo $value['id']; ?>">
		              <div class="row">
		                <div class="col-3 col-md-2"><img width="45px" class="rounded-circle" src="<?php echo $value['page_profile']; ?>"></div>
		                <div class="col-9 col-md-10">
		                  <h6 class="page_name"><?php echo $value['page_name']; ?></h6>
		                  <span class="gray"><?php echo $value['page_id']; ?></span>
		                  </div>
		                </div>
		            </li> 
		            <?php $i++; } ?>                
		        </ul>
		      </div>
		    </div>
		  </div>          
		</div>

		<div class="col-12 col-md-7 col-lg-4 colmid" id="middle_column">
			
		</div>

		<div class="col-12 col-md-12 col-lg-5 colrig" id="right_column">
			
	    </div>
		
	</div>

<?php } ?>


<?php 
	$Youdidntprovideallinformation = $this->lang->line("you didn\'t provide all information.");
	$Pleaseprovidepostid = $this->lang->line("please provide post id.");
	$Youdidntselectanytemplate = $this->lang->line("you have not select any template.");
	$Youdidntselectanyoptionyet = $this->lang->line("you have not select any option yet.");
	$Youdidntselectanyoption = $this->lang->line("you have not select any option.");
	
	$AlreadyEnabled = $this->lang->line("already enabled");
	$ThispostIDisnotfoundindatabaseorthispostIDisnotassociatedwiththepageyouareworking = $this->lang->line("This post ID is not found in database or this post ID is not associated with the page you are working.");
	$EnableAutoReply = $this->lang->line("enable auto reply");
	$TypeAutoCampaignname = $this->lang->line("You have not Type auto campaign name");
	$YouDidnotchosescheduleType = $this->lang->line("You have not choose any schedule type");
	$YouDidnotchosescheduletime = $this->lang->line("You have not select any schedule time");
	$YouDidnotchosescheduletimezone = $this->lang->line("You have not select any time zone");
	$YoudidnotSelectPerodicTime = $this->lang->line("You have not select any periodic time");
	$YoudidnotSelectCampaignStartTime = $this->lang->line("You have not choose campaign start time");
	$YoudidnotSelectCampaignEndTime = $this->lang->line("You have not choose campaign end time");

 ?>

<script>
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();	    
	});
	$(document).ready(function(){

		var base_url = "<?php echo base_url(); ?>";
		$(".private_reply_postback").select2({ width: "100%" }); 

		$('#hide_comment_automation_message').on('close.bs.alert', function (e) {
			e.preventDefault();
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>comment_automation/hide_comment_automation_message",
			  data:{},
			  dataType:'JSON',
			  success:function(response){
				$("#hide_comment_automation_message").parent().parent().hide();
			  }
			});
		});

		$(document).on('change', '#switch_media', function(event) {
		  event.preventDefault();
		  var switch_media_type = $('input[name=switch_media]:checked').val();
		  if(typeof(switch_media_type) == 'undefined') {
		    switch_media_type = 'ig';
		  }

		  $.ajax({
		  	url: base_url+'home/switch_to_media',
		  	type: 'POST',
		  	data: {media_type: switch_media_type},
		  	success:function(response){
		  		if(switch_media_type == 'fb') {
		  			window.location.assign('<?php echo base_url('comment_automation/index'); ?>');
		  		} 

		  		if(switch_media_type == 'ig') {
		  			window.location.assign('<?php echo base_url('instagram_reply/get_account_lists'); ?>');

		  		}
		  	}
		  });
		});

		$(".page_list_item").click(function(event) {

			$(".page_list_item").removeClass('active');
			$(this).addClass('active');

			var waiting_div_content = '<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>';
			$("#middle_column").html(waiting_div_content);
			$("#right_column").html(waiting_div_content);

			var page_table_id = $(this).attr('page_table_id');
			$("#dynamic_page_id").val(page_table_id);
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>comment_automation/get_page_details",
			  data:{page_table_id:page_table_id},
			  dataType:'JSON',
			  success:function(response){
				$("#middle_column").html(response.middle_column_content);
				$("#right_column").html(response.right_column_content);
				$("#auto_reply_template").html(response.template_list);
				$(".private_reply_postback").html(response.autoreply_postbacks);
			  }
			});

			$.ajax({
			  type:'POST' ,
			  url: base_url+'comment_automation/get_label_dropdown',
			  data: {page_table_id:page_table_id},
			  dataType : 'JSON',
			  success:function(response){
			    $('.dropdown_con').removeClass('hidden');
			    $('#first_dropdown').html(response.first_dropdown);      
			    $('#edit_first_dropdown').html(response.edit_first_dropdown);      
			  }
			});

		});


		var content_counter = 1;
		var edit_content_counter = 1;

		$('[data-toggle="popover"]').popover(); 
		$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});


		// enable and edit auto reply by post id
		$(document).on('click','.manual_auto_reply',function(){
			var page_name = $(this).attr('page_name');
			var page_table_id = $(this).attr('page_table_id');
			$("#manual_reply_error").html('');
			$("#manual_page_name").html(page_name);
			$("#manual_table_id").val(page_table_id);
			$("#manual_post_id").val('');

			$("#check_post_id").show();

			$("#manual_reply_by_post").addClass('modal');
			$("#manual_reply_by_post").modal();
		});

		$(document).on('click','#check_post_id',function(){
			$("#manual_reply_error").html('');		
			var post_id = $("#manual_post_id").val();
			var page_table_id = $("#manual_table_id").val();
			if(post_id=="")
			{
				swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please provide a post ID");?>', 'warning');
				return false;
			}

			$(this).addClass('btn-progress');
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>comment_automation/checking_post_id",
			  data:{page_table_id:page_table_id,post_id:post_id},
			  dataType:'JSON',
			  context: this,
			  success:function(response){
				$(this).removeClass('btn-progress');
				if(response.error == 'yes')
					swal('<?php echo $this->lang->line("Error"); ?>', response.error_msg, 'error');
				else
				{
					$("#manual_check_button_div").html(response.buttons);
					$("#check_post_id").hide();
				}
			  }
			});
		});


		$("#enable_auto_tag").click(function(){
		  $("#manual_reply_error").html('');    
		  var post_id = $("#manual_post_id").val();
		  var page_id = $(this).attr('page_table_id');
		  $(this).addClass('disabled');
		  $.ajax({
			type:'POST' ,
			url:"<?php echo site_url();?>comment_reply_enhancers/manual_sync_commenter_info",
			data:{page_id:page_id,post_id:post_id},
			dataType:'JSON',
			success:function(response){
			  if(response.status != '1')
			  $("#manual_reply_error").html("<div class='alert alert-danger text-center'><i class='fa fa-close'></i> "+response.message+"</div><br/>");
			  else
			  {
				$("#manual_reply_error").html("<div class='alert alert-success text-center'><i class='fa fa-check'></i> "+response.message+"</div><br/>");
				// $("#manual_post_id").val();
			  }
			  $("#enable_auto_tag").removeClass('disabled');
			}
		  });
		});


		// end of enable and edit auto reply by post id



		$(document).on('click','.enable_auto_commnet',function(){
		
			/** emoji load for offensive private reply  **/
			var page_table_id = $(this).attr('page_table_id');
			var post_id = $(this).attr('post_id');
			var post_permalink = $(this).attr('post_permalink');
			var manual_enable = $(this).attr('manual_enable');
			var Pleaseprovidepostid = "<?php echo $Pleaseprovidepostid; ?>";

			if(typeof(post_id) === 'undefined' || post_id == '')
			{
				alertify.alert('<?php echo $this->lang->line("Alert")?>',Pleaseprovidepostid,function(){});
				return false;
			}

			$("#auto_reply_page_id").val(page_table_id);
			$("#auto_reply_post_id").val(post_id);
			$("#auto_reply_post_permalink").val(post_permalink);
			$("#manual_enable").val(manual_enable);

			$("#create_label_auto_reply").attr("page_id_for_label",page_table_id);

			$(".message").val('').click();
			$(".filter_word").val('');
			$("#auto_campaign_name").val('');
			$("#template_select").prop("checked", true);
			$("#auto_reply_template").val('0');
			$("#auto_reply_templates_section").show();
			$("#new_template_section").hide();
			$("#save_and_create").hide();
			$("#comment_reply_enabled").prop("checked", true);
			$("#delete_offensive_comment_hide").prop("checked", true);
			$("#multiple_reply").prop("checked", false);
			$("#auto_like_comment").prop("checked", false);
			$("#hide_comment_after_comment_reply").prop("checked", false);

			$("#generic").prop("checked", false);
			$("#filter").prop("checked", false);
			$("#generic_message_div").hide();
			$("#filter_message_div").hide();

			$('#label_ids').val(null).trigger('change');



			for(var i=2;i<=20;i++)
			{
				$("#filter_div_"+i).hide();
			}
			content_counter = 1;
			$("#content_counter").val(content_counter);
			$("#add_more_button").show();

			$("#response_status").html('');

			$("#auto_reply_message_modal").addClass("modal");
			$("#auto_reply_message_modal").modal();

			$("#manual_reply_by_post").removeClass('modal');
		});
		


		$("#content_counter").val(content_counter);

		$(document).on('click','#add_more_button',function(){
			content_counter++;
			if(content_counter == 20)
				$("#add_more_button").hide();
			$("#content_counter").val(content_counter);

			$("#filter_div_"+content_counter).show();
			
			/** Load Emoji For Filter Word when click on add more button **/
			
			$("#comment_reply_msg_"+content_counter).emojioneArea({
				autocomplete: false,
				pickerPosition: "bottom"
			});
			

		});


		$(document).on('change','input[name=message_type]',function(){    
			if($("input[name=message_type]:checked").val()=="generic")
			{
				$("#generic_message_div").show();
				$("#filter_message_div").hide();
				
				/*** Load Emoji for generic message when clicked ***/
				
				$("#generic_message").emojioneArea({
					autocomplete: false,
					pickerPosition: "bottom"
				 });
		 
				
			}
			else 
			{
				$("#generic_message_div").hide();
				$("#filter_message_div").show();
				
				/*** Load Emoji When Filter word click , by defualt first textarea are loaded & No match found field****/
				
				$("#comment_reply_msg_1, #nofilter_word_found_text").emojioneArea({
					autocomplete: false,
					pickerPosition: "bottom"
				});
				
			}
		});

		
		$(document).on('click','.lead_first_name',function(){	
			
			var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");   
	        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
	        lastTag=lastTag.trim(lastTag);

	        if(lastTag=="<br>")
	          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
		  
		  
				
			var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
			var new_text = textAreaTxt + txtToAdd;
			$(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
			$(this).parent().next().next().next().children('.emojionearea-editor').click();
			
			
		});

		$(document).on('click','.lead_last_name',function(){

			var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");   
	        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
	        lastTag=lastTag.trim(lastTag);

	        if(lastTag=="<br>")
	          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
		  
		  
				
			var txtToAdd = " #LEAD_USER_LAST_NAME# ";
			var new_text = textAreaTxt + txtToAdd;
		   $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
		   $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
		   
		});

		$(document).on('click','.lead_tag_name',function(){

			var textAreaTxt = $(this).parent().next().next().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");   
	        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
	        lastTag=lastTag.trim(lastTag);

	        if(lastTag=="<br>")
	          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
		  
		  
				
				
			var txtToAdd = " #TAG_USER# ";
			var new_text = textAreaTxt + txtToAdd;
			$(this).parent().next().next().next().next().next().children('.emojionearea-editor').html(new_text);
			$(this).parent().next().next().next().next().next().children('.emojionearea-editor').click();
		});



		$(document).on('click','.save_button',function(){
			var button_type = $(this).attr('button_name');
			var use_template = $("input[name=auto_template_selection]:checked").val();
			var post_id = $("#auto_reply_post_id").val();

			if(button_type == "submit_create_button") $("#submit_btn_values").val("submit_create_button");
			
			if(button_type == "only_submit") $("#submit_btn_values").val("only_submit");

			if(typeof(use_template)==='undefined') 
			{
				var reply_type = $("input[name=message_type]:checked").val();
				var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
				var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
				if (typeof(reply_type)==='undefined')
				{
					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
					return false;
				}
				var auto_campaign_name = $("#auto_campaign_name").val().trim();

				if(reply_type == 'generic')
				{
					if(auto_campaign_name == ''){
						swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
						return false;
					}
				}
				else
				{
					if(auto_campaign_name == ''){
						swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
						return false;
					}
				}

			} else if(use_template == 'yes') 
			{
				var template_selection = $("#auto_reply_template").val();

				if(template_selection == '0') 
				{
					var Youdidntselectanytemplate = "<?php echo $Youdidntselectanytemplate; ?>";
					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanytemplate, 'warning');
					return false;
				}
			}


			$(this).addClass('btn-progress');

			var queryString = new FormData($("#auto_reply_info_form")[0]);
			var AlreadyEnabled = "<?php echo $AlreadyEnabled; ?>";
			$.ajax({
				type:'POST' ,
				url: base_url+"comment_automation/ajax_autoreply_submit",
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
							  $("#manual_reply_by_post").modal('hide');
							  $("#manual_check_button_div").html('');
							  $("#create_label_auto_reply").attr("page_id_for_label","");
							});
						$("button[post_id="+post_id+"][manual_enable='no']").removeClass('btn-outline-success').addClass('btn-outline-warning disabled').html(AlreadyEnabled);
					}
					else
					{
						swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
					}
				}

			});



		});


		// create an new label and put inside label list
		$(document).on('click','#create_label_auto_reply',function(e){
		  e.preventDefault();

		  	var page_id=$(this).attr('page_id_for_label');

	  		swal("<?php echo $this->lang->line('Label Name'); ?>", {
		    	content: "input",
		    	button: {text: "<?php echo $this->lang->line('Create'); ?>"},
		  	})
		  	.then((value) => {
		    	var label_name = `${value}`;
			    if(label_name!="" && label_name!='null')
			    {
		      		$("#save_changes").addClass("btn-progress");
			      	$.ajax({
			        	context: this,
			        	type:'POST',
			        	dataType:'JSON',
			        	url:"<?php echo site_url();?>home/common_create_label_and_assign",
			        	data:{page_id:page_id,label_name:label_name},
			        	success:function(response){

			           		$("#save_changes").removeClass("btn-progress");

			           		if(response.error) {
			              		var span = document.createElement("span");
			              		span.innerHTML = response.error;

				              	swal({
				                	icon: 'error',
				                	title: '<?php echo $this->lang->line('Error'); ?>',
				                	content:span,
				              	});

			           		} else {
			              		var newOption = new Option(response.text, response.id, true, true);
			              		$('#label_ids').append(newOption).trigger('change');
			            	}
			        	}
			      	});
			    }
		  	});
		});


		// create an new label and put inside label list
		$(document).on('click','#create_label_edit_auto_reply',function(e){
		  e.preventDefault();

		  	var page_id=$(this).attr('page_id_for_label');

	  		swal("<?php echo $this->lang->line('Label Name'); ?>", {
		    	content: "input",
		    	button: {text: "<?php echo $this->lang->line('Create'); ?>"},
		  	})
		  	.then((value) => {
		    	var label_name = `${value}`;
			    if(label_name!="" && label_name!='null')
			    {
		      		$("#save_changes").addClass("btn-progress");
			      	$.ajax({
			        	context: this,
			        	type:'POST',
			        	dataType:'JSON',
			        	url:"<?php echo site_url();?>home/common_create_label_and_assign",
			        	data:{page_id:page_id,label_name:label_name},
			        	success:function(response){

			           		$("#save_changes").removeClass("btn-progress");

			           		if(response.error) {
			              		var span = document.createElement("span");
			              		span.innerHTML = response.error;

				              	swal({
				                	icon: 'error',
				                	title: '<?php echo $this->lang->line('Error'); ?>',
				                	content:span,
				              	});

			           		} else {
			              		var newOption = new Option(response.text, response.id, true, true);
			              		$('#edit_label_ids').append(newOption).trigger('change');
			            	}
			        	}
			      	});
			    }
		  	});
		});

		$(document).on('click','#modal_close',function(){
			$(".page_list_item.active").click();
			$("#auto_reply_message_modal").modal('hide');
			$("#pageresponse_auto_reply_message_modal").modal('hide');
			$("#manual_reply_by_post").modal('hide');
			$("#create_label_auto_reply").attr('page_id_for_label','');
		});

		$(document).on('click','#edit_modal_close',function(){  
			$(".page_list_item.active").click();
			$("#edit_auto_reply_message_modal").modal('hide');
			$("#pageresponse_edit_auto_reply_message_modal").modal('hide');
			$("#create_label_edit_auto_reply").attr("page_id_for_label","");
		});



		$('#post_synch_modal').on('hidden.bs.modal', function () { 
			$(".page_list_item.active").click();
		});

		$('#manual_reply_by_post').on('hidden.bs.modal', function () { 
			$(".page_list_item.active").click();
			$("#manual_check_button_div").html('');
		});


		$(document).on('click','.edit_reply_info',function(){

			//$(".emojionearea-editor").html('');

			var emoji_load_div_list="";

			$("#manual_edit_reply_by_post").removeClass('modal');
			$("#edit_auto_reply_message_modal").addClass("modal");
			$("#edit_response_status").html("");

			var table_id = $(this).attr('table_id');
			$(".previewLoader").show();
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>comment_automation/ajax_edit_reply_info",
			  data:{table_id:table_id},
			  dataType:'JSON',
			  success:function(response)
			  {
			  	$("#edit_private_message_offensive_words").html(response.postbacks);
			  	$("#edit_generic_message_private").html(response.postbacks);
			  	$("#edit_nofilter_word_found_text_private").html(response.postbacks);
			  	for(var j=1;j<=20;j++)
			  	{
			  	  $("#edit_filter_div_"+j).hide();
			  	  $("#edit_filter_message_"+j).html(response.postbacks);
			  	}

			  	var edit_label_ids = response.edit_label_ids;
			  	if(edit_label_ids != '')
			  	{
				  	edit_label_ids = edit_label_ids.split(",");
				  	$('#edit_label_ids').val(edit_label_ids).trigger('change');			  		
			  	}
			  	else
			  		$('#edit_label_ids').val(null).trigger('change');

				$("#edit_auto_reply_page_id").val(response.edit_auto_reply_page_id);
				$("#create_label_edit_auto_reply").attr('page_id_for_label',response.edit_auto_reply_page_id);
				$("#edit_auto_reply_post_id").val(response.edit_auto_reply_post_id);
				$("#edit_auto_reply_post_permalink").val(response.edit_auto_reply_post_permalink);
				$("#edit_auto_campaign_name").val(response.edit_auto_campaign_name);

				// comment hide and delete section
				if(response.is_delete_offensive == 'hide')
				{
					$("#edit_delete_offensive_comment_hide").attr('checked','checked');
				}
				else
				{
					$("#edit_delete_offensive_comment_delete").attr('checked','checked');
				}

				if(response.trigger_matching_type == 'exact')
					$("#edit_trigger_keyword_exact").attr('checked','checked');
				else
					$("#edit_trigger_keyword_string").attr('checked','checked');

				$("#edit_delete_offensive_comment_keyword").val(response.offensive_words);
				$("#edit_private_message_offensive_words").val(response.private_message_offensive_words).click();
				

				/**	make the emoji loads div id in a string for selection . This is the first add. **/
				// emoji_load_div_list=emoji_load_div_list+"";

				if(response.hide_comment_after_comment_reply == 'no')
					$("#edit_hide_comment_after_comment_reply").removeAttr('checked','checked');
				else
					$("#edit_hide_comment_after_comment_reply").attr('checked','checked');
				// comment hide and delete section


				$("#edit_"+response.reply_type).prop('checked', true);
				// added by mostofa on 27-04-2017
				if(response.comment_reply_enabled == 'no')
					$("#edit_comment_reply_enabled").removeAttr('checked','checked');
				else
					$("#edit_comment_reply_enabled").attr('checked','checked');

				if(response.multiple_reply == 'no')
					$("#edit_multiple_reply").removeAttr('checked','checked');
				else
					$("#edit_multiple_reply").attr('checked','checked');

				if(response.auto_like_comment == 'no')
					$("#edit_auto_like_comment").removeAttr('checked','checked');
				else
					$("#edit_auto_like_comment").attr('checked','checked');

				var inner_content = '<i class="fas fa-times"></i> Remove';
				
				if(response.reply_type == 'generic')
				{
					$("#edit_generic_message_div").show();
					$("#edit_filter_message_div").hide();
					var i=1;
					edit_content_counter = i;
					var auto_reply_text_array_json = JSON.stringify(response.auto_reply_text);
					auto_reply_text_array = JSON.parse(auto_reply_text_array_json,'true');
					$("#edit_generic_message").val(auto_reply_text_array[0]['comment_reply']).click();	
					$("#edit_generic_message_private").val(auto_reply_text_array[0]['private_reply']).click();
					
					/** Add generic reply textarea id into the emoji load div list***/
					if(emoji_load_div_list == '')
					emoji_load_div_list=emoji_load_div_list+"#edit_generic_message";
					else
					emoji_load_div_list=emoji_load_div_list+", #edit_generic_message";
					
					// comment hide and delete section
					
					$("#edit_generic_image_for_comment_reply_display").attr('src',auto_reply_text_array[0]['image_link']).show();
					if(auto_reply_text_array[0]['image_link']=="")
					{
					  $("#edit_generic_image_for_comment_reply_display").prev('span').removeClass('remove_media').html('');
					  $("#edit_generic_image_for_comment_reply_display").hide();
					}
					else
					  $("#edit_generic_image_for_comment_reply_display").prev('span').addClass('remove_media').html(inner_content);


					var vidreplace='<source src="'+auto_reply_text_array[0]['video_link']+'" id="edit_generic_video_comment_reply_display" type="video/mp4">';
					$("#edit_generic_video_comment_reply_display").parent().html(vidreplace).show();
					
					if(auto_reply_text_array[0]['video_link']=='')
					{
					  $("#edit_generic_video_comment_reply_display").parent().prev('span').removeClass('remove_media').html('');
					  $("#edit_generic_video_comment_reply_display").parent().hide();
					}
					else
					  $("#edit_generic_video_comment_reply_display").parent().prev('span').addClass('remove_media').html(inner_content);


					$("#edit_generic_image_for_comment_reply").val(auto_reply_text_array[0]['image_link']);
					$("#edit_generic_video_comment_reply").val(auto_reply_text_array[0]['video_link']);
					// comment hide and delete section
				}
				else
				{
					var edit_nofilter_word_found_text = JSON.stringify(response.edit_nofilter_word_found_text);
					edit_nofilter_word_found_text = JSON.parse(edit_nofilter_word_found_text,'true');
					$("#edit_nofilter_word_found_text").val(edit_nofilter_word_found_text[0]['comment_reply']).click();
					$("#edit_nofilter_word_found_text_private").val(edit_nofilter_word_found_text[0]['private_reply']).click();
					
					/**Add no match found textarea into emoji load div list***/
					if(emoji_load_div_list == '')
					emoji_load_div_list=emoji_load_div_list+"#edit_nofilter_word_found_text";
					else
					emoji_load_div_list=emoji_load_div_list+", #edit_nofilter_word_found_text";
					
					// comment hide and delete section

					$("#edit_nofilter_image_upload_reply_display").attr('src',edit_nofilter_word_found_text[0]['image_link']).show();
					if(edit_nofilter_word_found_text[0]['image_link']=="")
					{
					  $("#edit_nofilter_image_upload_reply_display").prev('span').removeClass('remove_media').html('');
					  $("#edit_nofilter_image_upload_reply_display").hide();              
					}
					else
					  $("#edit_nofilter_image_upload_reply_display").prev('span').addClass('remove_media').html(inner_content);


					var vidreplace='<source src="'+edit_nofilter_word_found_text[0]['video_link']+'" id="edit_nofilter_video_upload_reply_display" type="video/mp4">';
					$("#edit_nofilter_video_upload_reply_display").parent().html(vidreplace).show();
					
					if(edit_nofilter_word_found_text[0]['video_link']=='')
					{
					  $("#edit_nofilter_video_upload_reply_display").parent().prev('span').removeClass('remove_media').html('');
					  $("#edit_nofilter_video_upload_reply_display").parent().hide();
					}
					else
					  $("#edit_nofilter_video_upload_reply_display").parent().prev('span').addClass('remove_media').html(inner_content);


					$("#edit_nofilter_image_upload_reply").val(edit_nofilter_word_found_text[0]['image_link']);
					$("#edit_nofilter_video_upload_reply").val(edit_nofilter_word_found_text[0]['video_link']);
					// comment hide and delete section

					$("#edit_filter_message_div").show();
					$("#edit_generic_message_div").hide();
					var auto_reply_text_array = JSON.stringify(response.auto_reply_text);
					auto_reply_text_array = JSON.parse(auto_reply_text_array,'true');

					for(var i = 0; i < auto_reply_text_array.length; i++) {
						var j = i+1;
						$("#edit_filter_div_"+j).show();
						$("#edit_filter_word_"+j).val(auto_reply_text_array[i]['filter_word']);
						var unscape_reply_text = auto_reply_text_array[i]['reply_text'];
						$("#edit_filter_message_"+j).val(unscape_reply_text).click();
						// added by mostofa 25-04-2017
						var unscape_comment_reply_text = auto_reply_text_array[i]['comment_reply_text'];
						$("#edit_comment_reply_msg_"+j).val(unscape_comment_reply_text).click();
						
						if(emoji_load_div_list == '')
						emoji_load_div_list=emoji_load_div_list+"#edit_comment_reply_msg_"+j;
						else
						emoji_load_div_list=emoji_load_div_list+", #edit_comment_reply_msg_"+j;
						
						// comment hide and delete section
						
						$("#edit_filter_image_upload_reply_display_"+j).attr('src',auto_reply_text_array[i]['image_link']).show();
						if(auto_reply_text_array[i]['image_link']=="")
						{
						  $("#edit_filter_image_upload_reply_display_"+j).prev('span').removeClass('remove_media').html('');
						  $("#edit_filter_image_upload_reply_display_"+j).hide();
						}
						else
						  $("#edit_filter_image_upload_reply_display_"+j).prev('span').addClass('remove_media').html(inner_content);


						var vidreplace='<source src="'+auto_reply_text_array[i]['video_link']+'" id="edit_filter_video_upload_reply_display'+j+'" type="video/mp4">';
						$("#edit_filter_video_upload_reply_display"+j).parent().html(vidreplace).show();
						if(auto_reply_text_array[i]['video_link']=='')
						{
						  $("#edit_filter_video_upload_reply_display"+j).parent().prev('span').removeClass('remove_media').html('');
						  $("#edit_filter_video_upload_reply_display"+j).parent().hide();
						}
						else
						  $("#edit_filter_video_upload_reply_display"+j).parent().prev('span').addClass('remove_media').html(inner_content);

						$("#edit_filter_image_upload_reply_"+j).val(auto_reply_text_array[i]['image_link']);
						$("#edit_filter_video_upload_reply_"+j).val(auto_reply_text_array[i]['video_link']);
						// comment hide and delete section
					}

					edit_content_counter = i+1;
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
				
			},2000);
			
			
			
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
		

		$(document).on('click','#edit_save_button',function(){

			var post_id = $("#edit_auto_reply_post_id").val();
			var edit_auto_campaign_name = $("#edit_auto_campaign_name").val();
			var reply_type = $("input[name=edit_message_type]:checked").val();
			var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
			var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
			if (typeof(reply_type)==='undefined')
			{
				swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
				return false;
			}
			if(reply_type == 'generic')
			{
				if(edit_auto_campaign_name == ''){
					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			}
			else
			{
				if(edit_auto_campaign_name == ''){
					swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
					return false;
				}
			}

			$(this).addClass('btn-progress');

			var queryString = new FormData($("#edit_auto_reply_info_form")[0]);
			$.ajax({
				type:'POST' ,
				url: base_url+"comment_automation/ajax_update_autoreply_submit",
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
							  $("#create_label_edit_auto_reply").attr("page_id_for_label","");
							});
					}
					else
					{
						swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
					}
				}

			});

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


		
		// start comment tag machine section
		$(document).on('click','.sync_commenter_info',function(){
		  var page_id = $(this).attr('page_table_id');
		  var post_id = $(this).attr('post_id');
		  var post_description = $(this).attr('post-description');
		  var post_created_at = $(this).attr('post-created-at');
		  var Pleaseprovidepostid = "<?php echo $Pleaseprovidepostid; ?>";

		  if(typeof(post_id) === 'undefined' || post_id == '')
		  {
			swal('<?php echo $this->lang->line("Warning"); ?>', Pleaseprovidepostid, 'warning');
			return false;
		  }
		  var button_id=page_id+"-"+post_id;
		  $("#"+button_id).addClass('disabled');


		  $(this).addClass('btn-progress');

		  $.ajax({
			type:'POST' ,
			url:"<?php echo site_url();?>comment_reply_enhancers/sync_commenter_info",
			data:{page_id:page_id,post_id:post_id,post_description:post_description,post_created_at:post_created_at},
			dataType:'JSON',
			context: this,
			success:function(response)
			{
				$(this).removeClass('btn-progress');

				if(response.status=='1')
				{
				  var success_message=response.message;
				  var span = document.createElement("span");
				  span.innerHTML = success_message;

				  swal({title:'<?php echo $this->lang->line("Success"); ?>', content:span, icon:'success'}).then((value) => {
		                $(".page_list_item.active").click();
		                $("#manual_reply_by_post").modal('hide');
		                $("#manual_check_button_div").html('');
		              });
				  $("#"+button_id).removeClass('blue');
				  $("#"+button_id).removeClass('sync_commenter_info');
				  $("#"+button_id).html(response.button_replace);
				}
				else
				{
				  swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
				  $("#"+button_id).removeClass('disabled');
				}
			}
		  });

		});
		// end comment tag machine section
		
		$(document).on('click','.remove_media',function(){
		  $(this).parent().prev('input').val('');
		  $(this).parent().hide();
		});
		
	});
</script>



<div class="modal fade" id="post_synch_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title"><i class="fas fa-poll-h"></i> <?php echo $this->lang->line("latest post for page") ?> - <span id="page_name_div"></span></h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>

			<div class="modal-body" id="post_synch_modal_body">                

			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg"  style="min-width: 70%;">
		<div class="modal-content">

			<div class="modal-header">
			  <h5 class="modal-title" style="padding: 10px 20px 10px 20px;" ><?php echo $this->lang->line("Please give the following information for post auto reply") ?></h5>
			  <button type="button" class="close" id='modal_close'  aria-label="Close">
				<span aria-hidden="true">×</span>
			  </button>
			</div>

			<form action="#" id="auto_reply_info_form" method="post">
				<input type="hidden" name="auto_reply_page_id" id="auto_reply_page_id" value="">
				<input type="hidden" name="auto_reply_post_id" id="auto_reply_post_id" value="">
				<input type="hidden" name="auto_reply_post_permalink" id="auto_reply_post_permalink" value="">
				<input type="hidden" name="manual_enable" id="manual_enable" value="">

				<div class="modal-body" id="auto_reply_message_modal_body">
					<!-- use saved template yes or no(new)  -->
					<br/>
					<div class="row" style="padding-left: 20px; padding-right: 20px;">         			
						<div class="col-12 col-md-6">
							<label><i class="fa fa-th-list"></i> <?php echo $this->lang->line("do you want to use saved template?") ?>
								<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("If you want to set campaign from previously saved template, then keep 'Yes' & select from below select option. If you want to add new settings, then select 'NO' , then auto reply settings form will come."); ?>"><i class='fa fa-info-circle'></i> </a>
							</label>
						</div>
						<div class="col-12 col-md-6">
						  <div class="form-group">
							<label class="custom-switch">
							  <input type="checkbox" name="auto_template_selection" value="yes" id="template_select" class="custom-switch-input" checked>
							  <span class="custom-switch-indicator"></span>
							  <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
							</label>
						  </div>
						</div>
					</div>

					<?php
					  $is_broadcaster_exist=false;
					  if($this->is_broadcaster_exist)
					  {
					      $is_broadcaster_exist=true;
					  }
				      $popover='<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Choose Labels").'" data-content="'.$this->lang->line("If you choose labels, then when user comment on the post & get private reply in their inbox , they will be added in those labels, that will help you to segment your leads & broadcasting from Messenger Broadcaster. If you don`t want to add labels for this post comment , then just keep it blank as it is.
Add label will only work once private reply is setup.  And you will need to sync subscribers later to update subscriber information. In this way the subscriber will not eligible for BOT subscriber until they reply back in messenger.").'"><i class="fa fa-info-circle"></i> </a>';
				      echo '<div class="row" style="padding-left: 20px; padding-right: 20px;">
				        <div class="col-3 col-md-3 hidden dropdown_con"> 
				            <div class="form-group">
				              <label style="width:100%"><i class="fas fa-tags"></i> 
				              '.$this->lang->line("Choose Labels").' '.$popover.'
				              </label>                                 
				              <label>
				              	<a class="blue float-right pointer" page_id_for_label="" id="create_label_auto_reply"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Create Label").'</a>
				              </label>
				            </div>       
				        </div>
				        <div class="col-9 col-md-9 hidden dropdown_con"> 
				            <div class="form-group">
				              <span id="first_dropdown"></span>                                  
				            </div>       
				        </div>
				      </div>';
					?>
					
					<div id="auto_reply_templates_section" style="padding: 10px 20px 10px 20px;">
						<!-- <hr> -->
						<div id="all_save_templates">
							<div id="saved_templates">
								<div class="row">
									<div class="form-group col-12 col-md-3">
										<label><i class="fa fa-reply"></i> <?php echo $this->lang->line('Auto Reply Template'); ?>
											<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Select any saved template of Auto Reply Campaign. If you want to modify any settings of this post campaign later, then edit this campaign & modify. Be notified that editing the saved template will not affect the campaign settings. To edit campaign, you need to edit post reply settings.") ?>"><i class='fa fa-info-circle'></i> </a>
										</label>
									</div>

									<div class="col-12 col-md-9">
										<select  class="form-control select2" id="auto_reply_template" name="auto_reply_template">
										<?php
											echo "<option value='0'>{$this->lang->line('Please select a template')}</option>";
											foreach($auto_reply_template as $key => $val)
											{
												$template_id = $val['id'];
												$template_campaign_name = $val['ultrapost_campaign_name'];
												echo "<option value='{$template_id}'>{$template_campaign_name}</option>";
											}
										 ?>
										</select>
									</div>
								</div> <!-- end of row  -->
							</div>
						</div>					
					</div>
					<!-- end of use saved template section -->
					
					<div id="new_template_section">
						<!-- <hr> -->
						<!-- comment hide and delete section -->
						<div class="row" style="padding: 10px 20px 10px 20px; <?php if(!$commnet_hide_delete_addon) echo "display: none;"; ?> ">
							<div class="col-12" style="margin-bottom: 20px;">
								<div class="row">									
									<div class="col-12 col-md-6">
										<label><i class="fa fa-ban"></i> <?php echo $this->lang->line("what do you want about offensive comments?") ?></label>
									</div>
									<div class="row">
									  <div class="col-12 col-md-6">
										<label class="custom-switch">
										  <input type="radio" name="delete_offensive_comment" value="hide" id="delete_offensive_comment_hide" class="custom-switch-input" checked>
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('hide'); ?></span>
										</label>
									  </div>
									  <div class="col-12 col-md-6">
										<label class="custom-switch">
										  <input type="radio" name="delete_offensive_comment" value="delete" id="delete_offensive_comment_delete" class="custom-switch-input">
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('delete'); ?>
										</label>
									  </div>
									</div>
								</div>
							</div>
							<br/><br/>
							
							<div class="col-12">								
								<div class="row">								
									<div class="col-12 col-md-6" id="delete_offensive_comment_keyword_div">
										<div class="form-group" style="border: 1px dashed #e4e6fc; padding: 10px;">
											<label><i class="fa fa-tags"></i> <small><?php echo $this->lang->line("write down the offensive keywords in comma separated") ?></small>
											</label>
											<textarea class="form-control message" name="delete_offensive_comment_keyword" id="delete_offensive_comment_keyword" placeholder="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions") ?>" style="height:59px !important;"></textarea>
										</div>
									</div>
									
									<div class="col-12 col-md-6">
										<div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 10px;">
											<label><small>
												<i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply after deleting offensive comment") ?></small>
											</label>
											<div>                      
												<select class="form-group private_reply_postback" id="private_message_offensive_words" name="private_message_offensive_words">
													<option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
												</select>

												<a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
												<a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

											</div>
										</div>
									</div>

								</div>
							</div>
						</div>  
						<!-- end of comment hide and delete section -->

						<div class="row" style="padding: 10px 20px 10px 20px;">
							<!-- added by mostofa on 26-04-2017 -->
							<div class="col-12">
								<div class="row">									
									<div class="col-12 col-md-6"><label><i class="fa fa-sort-numeric-down"></i> <?php echo $this->lang->line("do you want to send reply message to a user multiple times?") ?></label></div>
									<div class="col-12 col-md-6">
									  <div class="form-group">
										<label class="custom-switch">
										  <input type="checkbox" name="multiple_reply" value="yes" id="multiple_reply" class="custom-switch-input">
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
										</label>
									  </div>
									</div>
								</div>
							</div>
							<div class="smallspace clearfix"></div>
							<div class="col-12">
								<div class="row">									
									<div class="col-12 col-md-6">
										<label><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line("do you want to enable comment reply?") ?></label>
									</div>
									<div class="col-12 col-md-6">
									  <div class="form-group">
										<label class="custom-switch">
										  <input type="checkbox" name="comment_reply_enabled" value="yes" id="comment_reply_enabled" class="custom-switch-input" checked>
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
										</label>
									  </div>
									</div>
								</div>
							</div>
							<div class="smallspace clearfix"></div>
							<div class="col-12">
								<div class="row">									
									<div class="col-12 col-md-6">
										<label><i class="fa fa-comment"></i> <?php echo $this->lang->line("do you want to like on comment by page?") ?></label>
									</div>
									<div class="col-12 col-md-6">
									  <div class="form-group">
										<label class="custom-switch">
										  <input type="checkbox" name="auto_like_comment" value="yes" id="auto_like_comment" class="custom-switch-input">
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
										</label>
									  </div> 
									</div>
								</div>
							</div>
							<div class="smallspace clearfix"></div>
							<!-- comment hide and delete section -->
							<div class="col-12" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
								<div class="row">									
									<div class="col-12 col-md-6">
										<label><i class="fa fa-eye-slash"></i>  <?php echo $this->lang->line("do you want to hide comments after comment reply?") ?></label>
									</div>
									<div class="col-12 col-md-6">
										<div class="form-group">
										  <label class="custom-switch">
											<input type="checkbox" name="hide_comment_after_comment_reply" value="yes" id="hide_comment_after_comment_reply" class="custom-switch-input">
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
										  </label>
										</div>
									</div>
								</div>
							</div>
							<!-- comment hide and delete section -->

							<div class="smallspace clearfix"></div>

							<div class="col-12">
							  <div class="custom-control custom-radio">
								<input type="radio" name="message_type" value="generic" id="generic" class="custom-control-input radio_button">
								<label class="custom-control-label" for="generic"><?php echo $this->lang->line("generic message for all") ?></label>
							  </div>
							  <div class="custom-control custom-radio">
								<input type="radio" name="message_type" value="filter" id="filter" class="custom-control-input radio_button">
								<label class="custom-control-label" for="filter"><?php echo $this->lang->line("send message by filtering word/sentence") ?></label>
							  </div>
							</div>

							<div class="col-12" style="margin-top: 15px;">
								<div class="form-group">
									<label>
										<i class="fas fa-monument"></i> <?php echo $this->lang->line("auto reply campaign name") ?> <span class="red">*</span>
									</label>
									<input class="form-control" type="text" name="auto_campaign_name" id="auto_campaign_name" placeholder="<?php echo $this->lang->line("write your auto reply campaign name here") ?>">
								</div>
							</div>


							<div class="col-12" id="generic_message_div" style="display: none;">
								<div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 20px;">
									<label>
										<i class="fa fa-envelope"></i> <?php echo $this->lang->line("Message for comment reply") ?> <span class="red">*</span>
										<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
									</label>
									<?php if($comment_tag_machine_addon) {?>
									<span class='float-right'> 
										<a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
									</span>
									<?php } ?>
									<span class='float-right'> 
									  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
									</span>
									<span class='float-right'> 
									  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
									</span> 
									
									<div class="clearfix"></div>						
									<textarea class="form-control message" name="generic_message" id="generic_message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>

									<!-- comment hide and delete section -->
									<br/>
									<div class="row clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
										<div class="col-12 col-md-6">
											<label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?></label>									
											<div class="form-group">      
												<div id="generic_comment_image"><?php echo $this->lang->line("upload") ?></div>	     
											</div>
											<div id="generic_image_preview_id"></div>
											<span class="red" id="generic_image_for_comment_reply_error"></span>
											<input type="text" name="generic_image_for_comment_reply" class="form-control" id="generic_image_for_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
										</div>

										<div class="col-12 col-md-6">
											<label class="control-label" ><i class="fa fa-youtube"></i>  <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
												<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
											</label>
											<div class="form-group">      
												<div id="generic_video_upload"><?php echo $this->lang->line("upload") ?></div>	     
											</div>
											<div id="generic_video_preview_id"></div>
											<span class="red" id="generic_video_comment_reply_error"></span>
											<input type="hidden" name="generic_video_comment_reply" class="form-control" id="generic_video_comment_reply" placeholder="<?php echo $this->lang->line("Put your image url here or click upload") ?>"  />
										</div>
									</div>
									<br/><br/>
									<!-- comment hide and delete section -->


									<label>
									  <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
									</label>
									<div>                      
									  <select class="form-group private_reply_postback" id="generic_message_private" name="generic_message_private">
									    <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
									  </select>

									  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
									  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

									</div>
									
								</div>
							</div>


							<div class="col-12" id="filter_message_div">
									<div class="row">
									  <div class="col-12 col-md-6">
										<label class="custom-switch">
										  <input type="radio" name="trigger_matching_type" value="exact" id="trigger_keyword_exact" class="custom-switch-input" checked>
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('Reply if the filter word exactly matches.'); ?></span>
										</label>
									  </div>
									  <div class="col-12 col-md-6">
										<label class="custom-switch">
										  <input type="radio" name="trigger_matching_type" value="string" id="trigger_keyword_string" class="custom-switch-input">
										  <span class="custom-switch-indicator"></span>
										  <span class="custom-switch-description"><?php echo $this->lang->line('Reply if any matches occurs with filter word.'); ?>
										</label>
									  </div>
									</div><br/>
								<?php for ($i=1; $i <= 20 ; $i++) : ?>
										<div class="form-group clearfix" id="filter_div_<?php echo $i; ?>" style="border: 1px dashed #e4e6fc; padding: 20px; margin-bottom: 50px;">
											<label>
												<i class="fa fa-tag"></i> <?php echo $this->lang->line("filter word/sentence") ?> <span class="red">*</span>
												<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the word or sentence for which you want to filter comment. For multiple filter keyword write comma separated. Example -   why, wanto to know, when") ?>"><i class='fa fa-info-circle'></i> </a>
											</label>
											<input class="form-control filter_word" type="text" name="filter_word_<?php echo $i; ?>" id="filter_word_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("write your filter word here") ?>">
											
											
											<!-- new feature comment reply section -->
											<br/>
											<label>
												<i class="fa fa-envelope"></i> <?php echo $this->lang->line("msg for comment reply") ?><span class="red">*</span>
												<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send based on filter words. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
											</label>
											<?php if($comment_tag_machine_addon) {?>
											<span class='float-right'> 
												<a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
											</span>
											<?php } ?>
											<span class='float-right'> 
											  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
											</span>
											<span class='float-right'> 
											  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
											</span> 
											<div class="clearfix"></div>
											<textarea class="form-control message" name="comment_reply_msg_<?php echo $i; ?>" id="comment_reply_msg_<?php echo $i; ?>"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>

											<!-- comment hide and delete section -->
											<br/>
											<div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
												<div class="row">													
													<div class="col-12 col-md-6">
														<label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
														</label>									
														<div class="form-group">      
															<div id="filter_image_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>	     
														</div>
														<div id="generic_image_preview_id_<?php echo $i; ?>"></div>
														<span class="red" id="generic_image_for_comment_reply_error_<?php echo $i; ?>"></span>
														<input type="text" name="filter_image_upload_reply_<?php echo $i; ?>" class="form-control" id="filter_image_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("Put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
													</div>

													<div class="col-12 col-md-6">
														<label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
															<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
														</label>
														<div class="form-group">      
															<div id="filter_video_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>	     
														</div>
														<div id="generic_video_preview_id_<?php echo $i; ?>"></div>
														<span class="red" id="edit_generic_video_comment_reply_error_<?php echo $i; ?>"></span>
														<input type="hidden" name="filter_video_upload_reply_<?php echo $i; ?>" class="form-control" id="filter_video_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("Put your image url here or click upload") ?>"  />
													</div>
												</div>
											</div>
											<!-- comment hide and delete section -->

											<br/>
											
											<label>
											  <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
											</label>
											<div>                      
											  <select class="form-group private_reply_postback" id="filter_message_<?php echo $i; ?>" name="filter_message_<?php echo $i; ?>">
											    <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
											  </select>

											  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
											  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

											</div>


										</div>
								<?php endfor; ?>
								
								<div class="clearfix">
									<input type="hidden" name="content_counter" id="content_counter" />
									<button type="button" class="btn btn-sm btn-outline-primary float-right" id="add_more_button"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add more filtering") ?></button>
								</div>

								<div class="form-group clearfix" id="nofilter_word_found_div" style="margin-top: 10px; border: 1px dashed #e4e6fc; padding: 20px;">
									<label>
										<i class="fa fa-envelope"></i> <?php echo $this->lang->line("comment reply if no matching found") ?>
										<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the message,  if no filter word found. If you don't want to send message them, just keep it blank ."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
									</label>
									<?php if($comment_tag_machine_addon) {?>
									<span class='float-right'> 
										<a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
									</span>
									<?php } ?>
									<span class='float-right'> 
									  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
									</span>
									<span class='float-right'> 
									  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
									</span> 
									<div class="clearfix"></div>
									<textarea class="form-control message" name="nofilter_word_found_text" id="nofilter_word_found_text"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>

									<!-- comment hide and delete section -->
									<br/>
									<div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
										<div class="row">											
											<div class="col-12 col-md-6">
												<label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
												</label>									
												<div class="form-group">      
													<div id="nofilter_image_upload"><?php echo $this->lang->line("upload") ?></div>	     
												</div>
												<div id="nofilter_generic_image_preview_id"></div>
												<span class="red" id="nofilter_image_upload_reply_error"></span>
												<input type="text" name="nofilter_image_upload_reply" class="form-control" id="nofilter_image_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
											</div>

											<div class="col-12 col-md-6">
												<label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
													<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
												</label>
												<div class="form-group">      
													<div id="nofilter_video_upload"><?php echo $this->lang->line("upload") ?></div>	     
												</div>
												<div id="nofilter_video_preview_id"></div>
												<span class="red" id="nofilter_video_upload_reply_error"></span>
												<input type="hidden" name="nofilter_video_upload_reply" class="form-control" id="nofilter_video_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>"  />
											</div>
										</div>
									</div>
									<br/><br/>
									<!-- comment hide and delete section -->

									<label>
									  <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply if no matching found") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
									</label>
									<div>                      
									  <select class="form-group private_reply_postback" id="nofilter_word_found_text_private" name="nofilter_word_found_text_private">
									    <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
									  </select>

									  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
									  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

									</div>
									
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 text-center" id="response_status"></div>
				</div>
				
				<!-- This hidden field is for detecting the clicked button type -->
				<input type="hidden" name="btn_type" value="" id="submit_btn_values">
			</form>
			<div class="clearfix"></div>

			<div class="modal-footer" style="padding-left: 45px; padding-right: 45px; ">
				<div class="row">
					<div class="col-6">
						<button class="btn btn-lg btn-info save_button float-left" id="save_and_create" button_name="submit_create_button"><i class='fa fa-save'></i> <?php echo $this->lang->line("submit & save as template") ?></button>
					</div>  
					<div class="col-6">
						<button class="btn btn-lg btn-primary save_button float-right" id="save_only" button_name="only_submit"><i class='fa fa-save'></i> <?php echo $this->lang->line("submit") ?></button>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>


<div class="modal fade" id="edit_auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" style="min-width: 70%;">
		<div class="modal-content">

			<div class="modal-header">
			  <h5 class="modal-title" style="padding: 10px 20px 10px 20px;" ><?php echo $this->lang->line("Please give the following information for post auto reply") ?></h5>
			  <button type="button" class="close" id='edit_modal_close'  aria-label="Close">
				<span aria-hidden="true">×</span>
			  </button>
			</div>
			
			<form action="#" id="edit_auto_reply_info_form" method="post">
				<input type="hidden" name="edit_auto_reply_page_id" id="edit_auto_reply_page_id" value="">
				<input type="hidden" name="edit_auto_reply_post_id" id="edit_auto_reply_post_id" value="">
				<input type="hidden" name="edit_auto_reply_post_permalink" id="edit_auto_reply_post_permalink" value="">
			<div class="modal-body" id="edit_auto_reply_message_modal_body">   
			
			<div class="text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
			
			<br/>
				<?php
				  $is_broadcaster_exist=false;
				  if($this->is_broadcaster_exist)
				  {
				      $is_broadcaster_exist=true;
				  }
			      $popover='<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Choose Labels").'" data-content="'.$this->lang->line("If you choose labels, then when user comment on the post & get private reply in their inbox , they will be added in those labels, that will help you to segment your leads & broadcasting from Messenger Broadcaster. If you don`t want to add labels for this post comment , then just keep it blank as it is.
Add label will only work once private reply is setup.  And you will need to sync subscribers later to update subscriber information. In this way the subscriber will not eligible for BOT subscriber until they reply back in messenger.").'"><i class="fa fa-info-circle"></i> </a>';
			      echo '<div class="row" style="padding-left: 20px; padding-right: 20px;">
			        <div class="col-3 col-md-3 hidden dropdown_con"> 
			            <div class="form-group">
			              <label style="width:100%"><i class="fas fa-tags"></i> 
			              '.$this->lang->line("Choose Labels").' '.$popover.'
			              </label>
			              <label>
			              	<a class="blue float-right pointer" page_id_for_label="" id="create_label_edit_auto_reply"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Create Label").'</a>
			              </label>                              
			            </div>       
			        </div>
			        <div class="col-9 col-md-9 hidden dropdown_con"> 
			            <div class="form-group">
			              <span id="edit_first_dropdown"></span>                                  
			            </div>       
			        </div>
			      </div>';
				?>

				<!-- comment hide and delete section -->
				<div class="row" style="padding: 20px;<?php if(!$commnet_hide_delete_addon) echo "display: none;"; ?> ">
					<div class="col-12" style="margin-bottom: 20px;">
						<div class="row">							
							<div class="col-12 col-md-6" >
								<label><i class="fa fa-ban"></i> <?php echo $this->lang->line("what do you want about offensive comments?") ?></label>
							</div>
							<div class="row">
							  <div class="col-12 col-md-6">
							    <label class="custom-switch">
							      <input type="radio" name="edit_delete_offensive_comment" value="hide" id="edit_delete_offensive_comment_hide" class="custom-switch-input" checked>
							      <span class="custom-switch-indicator"></span>
							      <span class="custom-switch-description"><?php echo $this->lang->line('hide'); ?></span>
							    </label>
							  </div>
							  <div class="col-12 col-md-6">
							    <label class="custom-switch">
							      <input type="radio" name="edit_delete_offensive_comment" value="delete"  id="edit_delete_offensive_comment_delete" class="custom-switch-input">
							      <span class="custom-switch-indicator"></span>
							      <span class="custom-switch-description"><?php echo $this->lang->line('delete'); ?>
							    </label>
							  </div>
							</div>
						</div>
					</div>
					<br/><br/>
					<div class="col-12 col-md-6" id="edit_delete_offensive_comment_keyword_div">
						<div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 10px;">
							<label><i class="fa fa-tags"></i> <small><?php echo $this->lang->line("write down the offensive keywords in comma separated") ?></small>
							</label>
							<textarea class="form-control message" name="edit_delete_offensive_comment_keyword" id="edit_delete_offensive_comment_keyword" placeholder="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions") ?>" style="height:59px !important;"></textarea>
						</div>
					</div>
					
					<div class="col-12 col-md-6">
						<div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 10px;">
							<label><small>
								<i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply after deleting offensive comment") ?></small>
							</label>
							<div>                      
								<select class="form-group private_reply_postback" id="edit_private_message_offensive_words" name="edit_private_message_offensive_words">
									<option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
								</select>

								<a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
								<a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

							</div>
						</div>
					</div>

				</div> 
				<!-- end of comment hide and delete section -->
				<div class="row" style="padding: 10px 20px 10px 20px;">
					<!-- added by mostofa on 26-04-2017 -->
					<div class="col-12">
						<div class="row">							
							<div class="col-12 col-md-6" ><label><i class="fa fa-sort-numeric-down"></i> <?php echo $this->lang->line("do you want to send reply message to a user multiple times?") ?></label></div>
							<div class="col-12 col-md-6">
							  <div class="form-group">
							    <label class="custom-switch">
							      <input type="checkbox" name="edit_multiple_reply" value="yes" id="edit_multiple_reply" class="custom-switch-input">
							      <span class="custom-switch-indicator"></span>
							      <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
							    </label>
							  </div>
							</div>
						</div>
					</div>
					<div class="smallspace clearfix"></div>
					<div class="col-12">
						<div class="row">							
							<div class="col-12 col-md-6" >
								<label><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line("do you want to enable comment reply?") ?></label>
							</div>
							<div class="col-12 col-md-6">
							  <div class="form-group">
							    <label class="custom-switch">
							      <input type="checkbox" name="edit_comment_reply_enabled" value="yes" id="edit_comment_reply_enabled" class="custom-switch-input">
							      <span class="custom-switch-indicator"></span>
							      <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
							    </label>
							  </div>
							</div>						
						</div>					
					</div>
					<div class="smallspace clearfix"></div>
					<div class="col-12">
						<div class="row">							
							<div class="col-12 col-md-6" >
								<label><i class="fa fa-comment"></i> <?php echo $this->lang->line("do you want to like on comment by page?") ?></label>
							</div>
							<div class="col-12 col-md-6">
							  <div class="form-group">
							    <label class="custom-switch">
							      <input type="checkbox" name="edit_auto_like_comment" value="yes" id="edit_auto_like_comment" class="custom-switch-input">
							      <span class="custom-switch-indicator"></span>
							      <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
							    </label>
							  </div>
							</div>
						</div>
					</div>
					<div class="smallspace clearfix"></div>
					<!-- comment hide and delete section -->
					<div class="col-12" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
						<div class="row">							
							<div class="col-12 col-md-6" >
								<label><i class="fa fa-eye-slash"></i> <?php echo $this->lang->line("do you want to hide comments after comment reply?") ?></label>
							</div>
							<div class="col-12 col-md-6">
							  <div class="form-group">
							    <label class="custom-switch">
							      <input type="checkbox" name="edit_hide_comment_after_comment_reply" value="yes" id="edit_hide_comment_after_comment_reply" class="custom-switch-input">
							      <span class="custom-switch-indicator"></span>
							      <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
							    </label>
							  </div>
							</div>
						</div>
					</div>
					<!-- comment hide and delete section -->

					<div class="smallspace clearfix"></div>
					<div class="col-12">
					  <div class="custom-control custom-radio">
					  	<input type="radio" name="edit_message_type" value="generic" id="edit_generic" class="custom-control-input radio_button">
					  	<label class="custom-control-label" for="edit_generic"><?php echo $this->lang->line("generic message for all") ?></label>
					  </div>
					  <div class="custom-control custom-radio">
					  	<input type="radio" name="edit_message_type" value="filter" id="edit_filter" class="custom-control-input radio_button">
					  	<label class="custom-control-label" for="edit_filter"><?php echo $this->lang->line("send message by filtering word/sentence") ?></label>
					  </div>
					</div>

					<div class="col-12" style="margin-top: 15px;">
						<div class="form-group">
							<label>
								<i class="fa fa-monument"></i> <?php echo $this->lang->line("auto reply campaign name") ?> <span class="red">*</span>
							</label>
							<input class="form-control" type="text" name="edit_auto_campaign_name" id="edit_auto_campaign_name" placeholder="<?php echo $this->lang->line("write your auto reply campaign name here") ?>">
						</div>
					</div>

					<div class="col-12" id="edit_generic_message_div" style="display: none;">
						<div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 20px;">
							<label>
								<i class="fa fa-envelope"></i> <?php echo $this->lang->line("message for comment reply") ?> <span class="red">*</span>
								<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
							</label>
							<?php if($comment_tag_machine_addon) {?>
							<span class='float-right'> 
								<a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
							</span>
							<?php } ?>
							<span class='float-right'> 
							  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
							</span>
							<span class='float-right'> 
							  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
							</span> 
							<div class="clearfix"></div>
							<textarea class="form-control message" name="edit_generic_message" id="edit_generic_message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>
							

							<!-- comment hide and delete scetion -->
							<br/>
							<div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
								<div class="row">									
									<div class="col-12 col-md-6">
										<label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
										</label>									
										<div class="form-group">      
											<div id="edit_generic_comment_image"><?php echo $this->lang->line("upload") ?></div>	     
										</div>
										<div id="edit_generic_image_preview_id"></div>
										<span class="red" id="generic_image_for_comment_reply_error"></span>
										<input type="text" name="edit_generic_image_for_comment_reply" class="form-control" id="edit_generic_image_for_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />

										<div class="overlay_wrapper">
											<span></span>
											<img src="" alt="image" id="edit_generic_image_for_comment_reply_display" height="240" width="100%" style="display:none;" />
										</div>
									</div>

									<div class="col-12 col-md-6">
										<label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
											<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
										</label>
										<div class="form-group">      
											<div id="edit_generic_video_upload"><?php echo $this->lang->line("upload") ?></div>	     
										</div>
										<div id="edit_generic_video_preview_id"></div>
										<span class="red" id="edit_generic_video_comment_reply_error"></span>
										<input type="hidden" name="edit_generic_video_comment_reply" class="form-control" id="edit_generic_video_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>" />

										<div class="overlay_wrapper">
											<span></span>
											<video width="100%" height="240" controls style="border:1px solid #ccc;display:none;">
												<source src="" id="edit_generic_video_comment_reply_display" type="video/mp4">
											<?php echo $this->lang->line("your browser does not support the video tag.") ?>
											</video>
										</div>
									</div>
								</div>
							</div>
							<br/><br/>
							<!-- comment hide and delete scetion -->

							<label>
							  <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
							</label>
							<div>                      
							  <select class="form-group private_reply_postback" id="edit_generic_message_private" name="edit_generic_message_private">
							    <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
							  </select>

							  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
							  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

							</div>
							
						</div>
					</div>

					<div class="col-12" id="edit_filter_message_div" style="display: none;">
						<div class="row">
						  <div class="col-12 col-md-6">
							<label class="custom-switch">
							  <input type="radio" name="edit_trigger_matching_type" value="exact" id="edit_trigger_keyword_exact" class="custom-switch-input" checked>
							  <span class="custom-switch-indicator"></span>
							  <span class="custom-switch-description"><?php echo $this->lang->line('Reply if the filter word exactly matches.'); ?></span>
							</label>
						  </div>
						  <div class="col-12 col-md-6">
							<label class="custom-switch">
							  <input type="radio" name="edit_trigger_matching_type" value="string" id="edit_trigger_keyword_string" class="custom-switch-input">
							  <span class="custom-switch-indicator"></span>
							  <span class="custom-switch-description"><?php echo $this->lang->line('Reply if any matches occurs with filter word.'); ?>
							</label>
						  </div>
						</div><br/>
					<?php for($i=1;$i<=20;$i++) :?>
						<div class="form-group clearfix" id="edit_filter_div_<?php echo $i; ?>" style="border: 1px dashed #e4e6fc; padding: 20px; margin-bottom: 50px;">
							<label>
								<i class="fa fa-tag"></i> <?php echo $this->lang->line("filter word/sentence") ?> <span class="red">*</span>
								<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the word or sentence for which you want to filter comment. For multiple filter keyword write comma separated. Example -   why, want to know, when") ?>"><i class='fa fa-info-circle'></i> </a>
							</label>
							<input class="form-control filter_word" type="text" name="edit_filter_word_<?php echo $i; ?>" id="edit_filter_word_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("write your filter word here") ?>">
							
							
							<br/>
							<label>
								<i class="fa fa-envelope"></i> <?php echo $this->lang->line("msg for comment reply") ?><span class="red">*</span>
								<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send based on filter words. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
							</label>
							<?php if($comment_tag_machine_addon) {?>
							<span class='float-right'> 
								<a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
							</span>
							<?php } ?>
							<span class='float-right'> 
							  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
							</span>
							<span class='float-right'> 
							  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
							</span> 
							<div class="clearfix"></div>
							<textarea class="form-control message" name="edit_comment_reply_msg_<?php echo $i; ?>" id="edit_comment_reply_msg_<?php echo $i; ?>"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>
							

							<!-- comment hide and delete section -->
							<br/>
							<div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
								<div class="row">									
									<div class="col-12 col-md-6">
										<label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
										</label>									
										<div class="form-group">      
											<div id="edit_filter_image_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>	     
										</div>
										<div id="edit_generic_image_preview_id_<?php echo $i; ?>"></div>
										<span class="red" id="edit_generic_image_for_comment_reply_error_<?php echo $i; ?>"></span>
										<input type="text" name="edit_filter_image_upload_reply_<?php echo $i; ?>" class="form-control" id="edit_filter_image_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />

										<div class="overlay_wrapper">
											<span></span>
											<img src="" alt="image" id="edit_filter_image_upload_reply_display_<?php echo $i; ?>" height="240" width="100%" style="display:none"/>
										</div>
									</div>

									<div class="col-12 col-md-6">
										<label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
											<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
										</label>
										<div class="form-group">      
											<div id="edit_filter_video_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>	     
										</div>
										<div id="edit_generic_video_preview_id_<?php echo $i; ?>"></div>
										<span class="red" id="edit_generic_video_comment_reply_error_<?php echo $i; ?>"></span>
										<input type="hidden" name="edit_filter_video_upload_reply_<?php echo $i; ?>" class="form-control" id="edit_filter_video_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>"  />

										<div class="overlay_wrapper">
											<span></span>
											<video width="100%" height="240" controls style="border:1px solid #ccc;display:none;">
												<source src="" id="edit_filter_video_upload_reply_display<?php echo $i; ?>" type="video/mp4">
											<?php echo $this->lang->line("your browser does not support the video tag.") ?>
											</video>
										</div>
									</div>
								</div>
							</div>
							<!-- comment hide and delete section -->

							<br/>
							
							<label>
							  <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
							</label>
							<div>                      
							  <select class="form-group private_reply_postback" id="edit_filter_message_<?php echo $i; ?>" name="edit_filter_message_<?php echo $i; ?>">
							    <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
							  </select>

							  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
							  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

							</div>

						</div>
					<?php endfor; ?>
						

						<div class="clearfix">
							<input type="hidden" name="edit_content_counter" id="edit_content_counter" />
							<button type="button" class="btn btn-sm btn-outline-primary float-right" id="edit_add_more_button"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add more filtering") ?></button>
						</div>

						<div class="form-group clearfix" id="edit_nofilter_word_found_div" style="margin-top: 10px; border: 1px dashed #e4e6fc; padding: 20px;">
							<label>
								<i class="fa fa-envelope"></i> <?php echo $this->lang->line("comment reply if no matching found") ?>
								<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the message,  if no filter word found. If you don't want to send message them, just keep it blank ."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
							</label>
							<?php if($comment_tag_machine_addon) {?>
							<span class='float-right'> 
								<a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
							</span>
							<?php } ?>
							<span class='float-right'> 
							  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
							</span>
							<span class='float-right'> 
							  <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
							</span> 
							<div class="clearfix"></div>
							<textarea class="form-control message" name="edit_nofilter_word_found_text" id="edit_nofilter_word_found_text"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px !important;"></textarea>
							
							
							<!-- comment hide and delete section -->
							<br/>
							<div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
								<div class="row">									
									<div class="col-12 col-md-6">
										<label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
										</label>									
										<div class="form-group">      
											<div id="edit_nofilter_image_upload"><?php echo $this->lang->line("upload") ?></div>	     
										</div>
										<div id="edit_nofilter_generic_image_preview_id"></div>
										<span class="red" id="edit_nofilter_image_upload_reply_error"></span>
										<input type="text" name="edit_nofilter_image_upload_reply" class="form-control" id="edit_nofilter_image_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />

										<div class="overlay_wrapper">
											<span></span>
											<img src="" alt="image" id="edit_nofilter_image_upload_reply_display"  height="240" width="100%" style="display:none;" />
										</div>
									</div>

									<div class="col-12 col-md-6">
										<label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Prefered");?>]
											<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
										</label>
										<div class="form-group">      
											<div id="edit_nofilter_video_upload"><?php echo $this->lang->line("upload") ?></div>	     
										</div>
										<div id="edit_nofilter_video_preview_id"></div>
										<span class="red" id="edit_nofilter_video_upload_reply_error"></span>
										<input type="hidden" name="edit_nofilter_video_upload_reply" class="form-control" id="edit_nofilter_video_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>" />

										<div class="overlay_wrapper">
											<span></span>
											<video width="100%" height="240" controls style="border:1px solid #ccc;display:none;">
												<source src="" id="edit_nofilter_video_upload_reply_display" type="video/mp4">
											<?php echo $this->lang->line("your browser does not support the video tag.") ?>
											</video>
										</div>
									</div>
								</div>
							</div>
							<br/><br/>
							<!-- comment hide and delete section -->

							<label>
							  <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply if no matching found") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
							</label>
							<div>                      
							  <select class="form-group private_reply_postback" id="edit_nofilter_word_found_text_private" name="edit_nofilter_word_found_text_private">
							    <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
							  </select>

							  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
							  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

							</div>
							
						</div>


					</div>
				</div>
				<div class="col-12 text-center" id="edit_response_status"></div>
			</div>
			</form>
			<div class="clearfix"></div>
			<div class="modal-footer" style="padding-left: 45px; padding-right: 45px; ">
				<div class="row">
					<div class="col-6">
						<button class="btn btn-lg btn-primary float-left" id="edit_save_button"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
					</div>  
					<div class="col-6">
						<button class="btn btn-lg btn-secondary float-right cancel_button"><i class='fas fa-times'></i> <?php echo $this->lang->line("cancel") ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="manual_reply_by_post" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title"><?php echo $this->lang->line("Enable reply by Post ID") ?> (<span id="manual_page_name"></span>)</h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">×</span>
			  </button>
			</div>

			<div class="modal-body ">
				<div class="row">
					<div class="col-12">						
						<div class="alert alert-light alert-has-icon alert-dismissible show fade">
							<div class="alert-icon"><i class="fas fa-hand-paper"></i></div>
							<div class="alert-body">
								<button class="close" data-dismiss="alert">
									<span>×</span>
								</button>
								<div class="alert-title"><?php echo $this->lang->line('Disclaimer'); ?></div>
								<?php 
									echo $this->lang->line("Facebook ads post that you have created from Facebook Ads Manager, if you have ever edited/modified your ads after creating, then this technique of 'Set Campaign by ID' may not work as Facebook creates different variation of your post for each time you edit. Preview post for that ads may have different ID, which leads to a wrong ID actually sometimes."); 
									if(addon_exist(204,"comment_reply_enhancers"))
										echo $this->lang->line("In this case we suggest to use 'Full page campaigns' feature.");
								?>	
							</div>
						</div>
					</div>
					<br/><br/>
					<div class="col-12" id="waiting_div"></div>
					<div class="col-12">
						<form>

						    <input type="hidden" id="manual_table_id">
							<div class="input-group">
		                        <div class="input-group-prepend">
		                          <div class="input-group-text">
		                            <?php echo $this->lang->line("post id") ?>
		                          </div>
		                        </div>
		                        <input type="text" class="form-control" id="manual_post_id" placeholder="<?php echo $this->lang->line("please give a post id") ?>">
		                    </div>

							<div class="text-center" id="manual_reply_error"></div>
						  	<br/>
							<div class="form-group text-center" id="manual_check_button_div">
							</div>
						 </form>
						 <div class="form-group text-center">
							<button type="button" class="btn btn-info" id="check_post_id"><i class="fa fa-check"></i> <?php echo $this->lang->line("check existance") ?></button>
						 </div>
						
					</div>                    
				</div>               
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="manual_edit_reply_by_post" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h5 class="modal-title"><?php echo $this->lang->line("please provide a post id of page") ?> (<span id="manual_edit_page_name"></span>)</h5>
			</div>
			<div class="modal-body ">
				<div class="row">
					<div class="col-12" id="waiting_div"></div>
					<div class="col-12 col-md-8 col-md-offset-2">
						<form>
							<div class="form-group">
							  <label for="manual_post_id"><?php echo $this->lang->line("post id") ?> :</label>
							  <input type="text" class="form-control" id="manual_edit_post_id" placeholder="<?php echo $this->lang->line("please give a post id") ?>" value="">
							  <input type="hidden" id="manual_edit_table_id">
							</div>
							<div class="text-center" id="manual_edit_error"></div>                           
						</form>
						<div class="form-group text-center" style="margin-top: 15px;">
						   <button type="button" class="btn btn-outline-warning edit_reply_info" id="manual_edit_auto_reply"><i class="fa fa-edit"></i> <?php echo $this->lang->line("edit auto reply") ?></button>
						</div>
						
					</div>                    
				</div>               
			</div>
		</div>
	</div>
</div>


<!-- start of auto comment javascript section -->
<?php include(FCPATH.'application/views/comment_automation/autocomment_javascript_section.php'); ?>
<!-- end of auto comment javascript section -->
<!-- start of auto comment modal section -->
<?php include(FCPATH.'application/views/comment_automation/autocomment_modal_section.php'); ?>
<!-- end of auto comment modal section -->



<!-- comment hide and delete section -->
<div class="modal fade" id="modal-live-video-library"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header clearfix">
		<a class="pull-right" id="filemanager_close" style="font-size: 14px; color: white; cursor: pointer;" >&times;</a>
		<h5 class="modal-title"><i class="fa fa-file-video-o"></i> <?php echo $this->lang->line("filemanager Library") ?></h5>
	  </div>
	  <div class="modal-body">
		
	  </div>
	</div>
  </div>
</div>

<!-- instand comment checker -->
<div class="modal fade" id="instant_comment_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title"><i class="fas fa-comments"></i> <?php echo $this->lang->line("Instant Comment") ?></h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>

			<div class="modal-body">     
				<input type="hidden" name="instant_comment_page_id" id="instant_comment_page_id">           
				<input type="hidden" name="instant_comment_post_id" id="instant_comment_post_id">           
				<div class="row">
					<div class="col-12">
						<div class="form-group">
							<label><i class="fas fa-keyboard"></i> <?php echo $this->lang->line("Please provide a message as comment") ?></label>
							<textarea class="form-control" name="instant_comment_message" id="instant_comment_message" placeholder="<?php echo $this->lang->line("Type your comment here.") ?>"></textarea>
						</div>
					</div>
					<div class="col-12">
						<button class="btn btn-primary submit_instant_comment"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line('Create Comment'); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$("document").ready(function(){

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
			    url:"<?php echo site_url();?>comment_automation/instant_commnet_submit",
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

		$("#auto_reply_template").select2({width: "100%"});
		
		$("#filemanager_close").click(function(){
			$("#modal-live-video-library").removeClass('modal');
		});

		$(document).on('click','.cancel_button',function(){
			$("#pageresponse_auto_reply_message_modal").modal('hide');
			$("#pageresponse_edit_auto_reply_message_modal").modal('hide'); 
			$("#edit_auto_reply_message_modal").modal('hide'); 
			$("#auto_reply_message_modal_template").modal('hide'); 
			$("#edit_auto_reply_message_modal_template").modal('hide'); 
			$(".page_list_item.active").click();
		});

		// ===== auto click to the page name box to trigger click event [click function is in top of the javascript code] =========//
		var session_value = "<?php echo $this->session->userdata('get_page_details_page_table_id'); ?>";
		if(session_value==''){

			$(".list-group li:first").click();
		}
		else
			$("li[page_table_id='"+session_value+"']").click();



		// ================== use saved template or not =================================
		if($("input[name=auto_template_selection]:checked").val()=="yes")
		{
			$("#auto_reply_templates_section").show();
			$("#new_template_section").hide();
			$("#save_and_create").hide();
		}
		else 
		{
			$("#auto_reply_templates_section").hide();
			$("#new_template_section").show();
			$("#save_and_create").show();
		}

		$(document).on('change','input[name=auto_template_selection]',function(){
			if($("input[name=auto_template_selection]:checked").val()=="yes")
			{
				$("#auto_reply_templates_section").show();
				$("#new_template_section").hide();
				$("#save_and_create").hide();
				
			}
			else 
			{
				$("#auto_reply_templates_section").hide();
				$("#new_template_section").show();
				$("#save_and_create").show();

			}
		});
		// ========================= end of use saved template or not =================

		var image_upload_limit = "<?php echo $image_upload_limit; ?>";
		var video_upload_limit = "<?php echo $video_upload_limit; ?>";

		var base_url="<?php echo site_url(); ?>";
		var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
		<?php for($k=1;$k<=20;$k++) : ?>
			$("#edit_filter_video_upload_<?php echo $k; ?>").uploadFile({
				url:base_url+"comment_automation/upload_live_video",
				fileName:"myfile",
				maxFileSize:video_upload_limit*1024*1024,
				showPreview:false,
				returnType: "json",
				dragDrop: true,
				showDelete: true,
				multiple:false,
				maxFileCount:1, 
				acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
				deleteCallback: function (data, pd) {
					var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
					$.post(delete_url, {op: "delete",name: data},
						function (resp,textStatus, jqXHR) {  
							$("#edit_filter_video_upload_reply_<?php echo $k; ?>").val('');              
						});

				},
				onSuccess:function(files,data,xhr,pd)
				{
					var file_path = base_url+"upload/video/"+data;
					$("#edit_filter_video_upload_reply_<?php echo $k; ?>").val(file_path);	
				}
			});


			$("#edit_filter_image_upload_<?php echo $k; ?>").uploadFile({
				url:base_url+"comment_automation/upload_image_only",
				fileName:"myfile",
				maxFileSize:image_upload_limit*1024*1024,
				showPreview:false,
				returnType: "json",
				dragDrop: true,
				showDelete: true,
				multiple:false,
				maxFileCount:1, 
				acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
				deleteCallback: function (data, pd) {
					var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
					$.post(delete_url, {op: "delete",name: data},
						function (resp,textStatus, jqXHR) {
							$("#edit_filter_image_upload_reply_<?php echo $k; ?>").val('');                      
						});
				   
				 },
				 onSuccess:function(files,data,xhr,pd)
				   {
					   var data_modified = base_url+"upload/image/"+user_id+"/"+data;
					   $("#edit_filter_image_upload_reply_<?php echo $k; ?>").val(data_modified);	
				   }
			});
		<?php endfor; ?>

		<?php for($k=1;$k<=20;$k++) : ?>
			$("#filter_video_upload_<?php echo $k; ?>").uploadFile({
				url:base_url+"comment_automation/upload_live_video",
				fileName:"myfile",
				maxFileSize:video_upload_limit*1024*1024,
				showPreview:false,
				returnType: "json",
				dragDrop: true,
				showDelete: true,
				multiple:false,
				maxFileCount:1, 
				acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
				deleteCallback: function (data, pd) {
					var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
					$.post(delete_url, {op: "delete",name: data},
						function (resp,textStatus, jqXHR) {  
							$("#filter_video_upload_reply_<?php echo $k; ?>").val('');              
						});

				},
				onSuccess:function(files,data,xhr,pd)
				{
					var file_path = base_url+"upload/video/"+data;
					$("#filter_video_upload_reply_<?php echo $k; ?>").val(file_path);	
				}
			});


			$("#filter_image_upload_<?php echo $k; ?>").uploadFile({
				url:base_url+"comment_automation/upload_image_only",
				fileName:"myfile",
				maxFileSize:image_upload_limit*1024*1024,
				showPreview:false,
				returnType: "json",
				dragDrop: true,
				showDelete: true,
				multiple:false,
				maxFileCount:1, 
				acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
				deleteCallback: function (data, pd) {
					var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
					$.post(delete_url, {op: "delete",name: data},
						function (resp,textStatus, jqXHR) {
							$("#filter_image_upload_reply_<?php echo $k; ?>").val('');                      
						});
				   
				 },
				 onSuccess:function(files,data,xhr,pd)
				   {
					   var data_modified = base_url+"upload/image/"+user_id+"/"+data;
					   $("#filter_image_upload_reply_<?php echo $k; ?>").val(data_modified);	
				   }
			});
		<?php endfor; ?>

		$("#generic_video_upload").uploadFile({
			url:base_url+"comment_automation/upload_live_video",
			fileName:"myfile",
			maxFileSize:video_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {  
						$("#generic_video_comment_reply").val('');              
					});

			},
			onSuccess:function(files,data,xhr,pd)
			{
				var file_path = base_url+"upload/video/"+data;
				$("#generic_video_comment_reply").val(file_path);	
			}
		});


		$("#generic_comment_image").uploadFile({
			url:base_url+"comment_automation/upload_image_only",
			fileName:"myfile",
			maxFileSize:image_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {
						$("#generic_image_for_comment_reply").val('');                      
					});
			   
			 },
			 onSuccess:function(files,data,xhr,pd)
			   {
				   var data_modified = base_url+"upload/image/"+user_id+"/"+data;
				   $("#generic_image_for_comment_reply").val(data_modified);		
			   }
		});


		$("#nofilter_video_upload").uploadFile({
			url:base_url+"comment_automation/upload_live_video",
			fileName:"myfile",
			maxFileSize:video_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {  
						$("#nofilter_video_upload_reply").val('');              
					});

			},
			onSuccess:function(files,data,xhr,pd)
			{
				var file_path = base_url+"upload/video/"+data;
				$("#nofilter_video_upload_reply").val(file_path);	
			}
		});


		$("#nofilter_image_upload").uploadFile({
			url:base_url+"comment_automation/upload_image_only",
			fileName:"myfile",
			maxFileSize:image_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {
						$("#nofilter_image_upload_reply").val('');                      
					});
			   
			 },
			 onSuccess:function(files,data,xhr,pd)
			   {
				   var data_modified = base_url+"upload/image/"+user_id+"/"+data;
				   $("#nofilter_image_upload_reply").val(data_modified);		
			   }
		});

		$("#edit_generic_video_upload").uploadFile({
			url:base_url+"comment_automation/upload_live_video",
			fileName:"myfile",
			maxFileSize:video_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {  
						$("#edit_generic_video_comment_reply").val('');              
					});

			},
			onSuccess:function(files,data,xhr,pd)
			{
				var file_path = base_url+"upload/video/"+data;
				$("#edit_generic_video_comment_reply").val(file_path);	
			}
		});


		$("#edit_generic_comment_image").uploadFile({
			url:base_url+"comment_automation/upload_image_only",
			fileName:"myfile",
			maxFileSize:image_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {
						$("#edit_generic_image_for_comment_reply").val('');                      
					});
			   
			 },
			 onSuccess:function(files,data,xhr,pd)
			   {
				   var data_modified = base_url+"upload/image/"+user_id+"/"+data;
				   $("#edit_generic_image_for_comment_reply").val(data_modified);		
			   }
		});


		$("#edit_nofilter_video_upload").uploadFile({
			url:base_url+"comment_automation/upload_live_video",
			fileName:"myfile",
			maxFileSize:video_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {  
						$("#edit_nofilter_video_upload_reply").val('');              
					});

			},
			onSuccess:function(files,data,xhr,pd)
			{
				var file_path = base_url+"upload/video/"+data;
				$("#edit_nofilter_video_upload_reply").val(file_path);	
			}
		});


		$("#edit_nofilter_image_upload").uploadFile({
			url:base_url+"comment_automation/upload_image_only",
			fileName:"myfile",
			maxFileSize:image_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {
						$("#edit_nofilter_image_upload_reply").val('');                      
					});
			   
			 },
			 onSuccess:function(files,data,xhr,pd)
			   {
				   var data_modified = base_url+"upload/image/"+user_id+"/"+data;
				   $("#edit_nofilter_image_upload_reply").val(data_modified);		
			   }
		});	

		// ============ pageresponse file upload section ==============//
		
		// ============ end of pageresponse file upload section ==============//

	});
</script>
<!-- comment hide and delete section -->

<style type="text/css">
	.ajax-upload-dragdrop{width:100% !important;}

	.overlay_wrapper {
	  position: relative;
	  max-height: 240px;
	  width: 100%;
	  overflow: hidden;
	}

	.overlay_wrapper span.remove_media {
	  position: absolute;
	  right: 5px;
	  top: 5px;
	  background-color: black;
	  color: white;
	  padding: 4px 15px;
	  font-size: 12px;
	  border-radius: 15px;
	  transition: 0.4s;
	  -o-transition: 0.4s;
	  -webkit-transition: 0.4s;
	  -moz-transition: 0.4s;
	  -ms-transition: 0.4s;
	  opacity: 0;
	  cursor: pointer;
	  visibility: hidden;
	}

	.overlay_wrapper:hover span.remove_media{
	  display: block;
	  opacity: 1;
	  visibility: visible;
	  z-index: 1000;
	}
	.add_template,.ref_template{font-size: 10px;}
</style>



<!-- =========== pageresponse javascript section =============  --> 
<?php include(FCPATH.'application/views/comment_automation/pageresponse_javascript_section.php'); ?>
<!-- =========== end of pageresponse javascript section =============  --> 

<!-- =========== pageresponse modal section ============= -->
<?php include(FCPATH.'application/views/comment_automation/pageresponse_modal_section.php'); ?>
<!-- =========== end of pageresponse modal section ============= -->


<!-- =========== pageresponse likeshare section ============= -->
<?php include(FCPATH.'application/views/comment_automation/pageresponse_likeshare_section.php'); ?>
<!-- =========== end of pageresponse likeshare section ============= -->


<!-- postback add/refresh button section -->
<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body"> 
        <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close & Refresh List");?></button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" name="dynamic_page_id" id="dynamic_page_id">
<script type="text/javascript">
	$("document").ready(function(){		
		var base_url = "<?php echo base_url(); ?>";

		$('.modal').on("hidden.bs.modal", function (e) { 
		    if ($('.modal:visible').length) { 
		        $('body').addClass('modal-open');
		    }
		});

		$(document).on('click','.add_template',function(e){
		  e.preventDefault();
		  var current_id=$(this).prev().prev().attr('id');
		  var current_val=$(this).prev().prev().val();
		  var page_id = get_page_id();
		  if(page_id=="")
		  {
		    swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
		    return false;
		  }
		  $("#add_template_modal").attr("current_id",current_id);
		  $("#add_template_modal").attr("current_val",current_val);
		  $("#add_template_modal").modal();
		});

		$(document).on('click','.ref_template',function(e){
		  e.preventDefault();
		  var current_val=$(this).prev().prev().prev().val();
		  var current_id=$(this).prev().prev().prev().attr('id');
		  var page_id = get_page_id();
		   if(page_id=="")
		   {
		     swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
		     return false;
		   }
		   $.ajax({
		     type:'POST',
		     url: base_url+"comment_automation/get_private_reply_postbacks",
		     data: {page_table_ids:page_id},
		     dataType: 'JSON',
		     success:function(response){
		       $("#"+current_id).html(response.options).val(current_val);
		     }
		   });
		});

		$('#add_template_modal').on('hidden.bs.modal', function (e) { 
		  var current_id=$("#add_template_modal").attr("current_id");
		  var current_val=$("#add_template_modal").attr("current_val");
		  var page_id = get_page_id();
		   if(page_id=="")
		   {
		     swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
		     return false;
		   }
		   $.ajax({
		     type:'POST' ,
		     url: base_url+"comment_automation/get_private_reply_postbacks",
		     data: {page_table_ids:page_id,is_from_add_button:'1'},
		     dataType: 'JSON',
		     success:function(response){
		       $("#"+current_id).html(response.options);
		     }
		   });
		});

		// getting postback list and making iframe
		$('#add_template_modal').on('shown.bs.modal',function(){ 
			var page_id = get_page_id();
			var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id;
		  	$(this).find('iframe').attr('src',iframe_link); 
		});   
		// getting postback list and making iframe

	});

	function get_page_id()
	{
		var page_id = $("#dynamic_page_id").val();
		return page_id;
	}
</script>




<!-- FB auto reply and comment report section starts here -->
<?php 
	
	$Edit = $this->lang->line("edit");
	$Report = $this->lang->line("report");
	$Delete = $this->lang->line("delete");
	$PauseCampaign = $this->lang->line("pause campaign");
	$StartCampaign = $this->lang->line("start campaign");
	$Doyouwanttopausethiscampaign = $this->lang->line("do you want to pause this campaign?");
	$Doyouwanttostarthiscampaign = $this->lang->line("do you want to start this campaign?");
	$Doyouwanttodeletethisrecordfromdatabase = $this->lang->line("do you want to delete this record from database?");
	$Youdidntselectanyoption = $this->lang->line("you didnt select any option.");
	$Youdidntprovideallinformation = $this->lang->line("you didnt provide all information.");
	
	$doyoureallywanttoReprocessthiscampaign = $this->lang->line("Force Reprocessing means you are going to process this campaign again from where it ended. You should do only if you think the campaign is hung for long time and didn't send message for long time. It may happen for any server timeout issue or server going down during last attempt or any other server issue. So only click OK if you think message is not sending. Are you sure to Reprocessing ?");
	$alreadyEnabled = $this->lang->line("this campaign is already enable for processing.");

?>

<script type="text/javascript">
	var table1 = '';
	var perscroll1;
	$(document).on('click','.view_report',function(e){
	  e.preventDefault();

	  var table_id = $(this).attr('table_id');

	  if(table_id !='') 
	  {
	  	$("#put_row_id").val(table_id);
	  	$("#download").attr("href",base_url+"comment_automation/download_get_reply_info/"+table_id);
	  }


	  $("#view_report_modal").modal();

	  var commnet_hide_delete_addon = "<?php echo $commnet_hide_delete_addon; ?>";
	  if(commnet_hide_delete_addon == 1)
	  	var visible_section = "";
	  else
	  	var visible_section = [9];

	  setTimeout(function(){ 
  	  if (table1 == '')
  	  {
  	    table1 = $("#mytable1").DataTable({
  	        serverSide: true,
  	        processing:true,
  	        bFilter: false,
	        order: [[ 3, "desc" ]],
  	        pageLength: 10,
  	        ajax: {
  	            url: base_url+'comment_automation/ajax_get_reply_info',
  	            type: 'POST',
  	            data: function ( d )
  	            {
  	                d.table_id = $("#put_row_id").val();
  	                d.searching = $("#searching").val();
  	            }
  	        },
  	        language: 
  	        {
  	          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
  	        },
  	        dom: '<"top"f>rt<"bottom"lip><"clear">',
  	        columnDefs: [
  	          {
  	            targets: visible_section,
  	            visible: false
  	          },
  	          {
  	              targets: '',
  	              className: 'text-center'
  	          },
  	          {
  	              targets: [0,1,2,5,6,7,8,9],
  	              sortable: false
  	          }
  	        ],
  	        fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
  	        if(areWeUsingScroll)
  	        {
  	        	if (perscroll1) perscroll1.destroy();
  	        		perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
  	        }
  	        },
  	        scrollX: 'auto',
  	        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
  	        	if(areWeUsingScroll)
  	        	{ 
  	        	if (perscroll1) perscroll1.destroy();
  	        	perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
  	        	}
  	        }
  	    });
  	  }
  	  else table1.draw();
    }, 1000);
    
	  $("#outside_filter").html('');
	  setTimeout(function(){
	  	$.ajax({
	  		type:'POST' ,
	  		url: "<?php echo site_url(); ?>comment_automation/get_count_info",
	  		data:{table_id:table_id},
	  		dataType:'JSON',
	  		success:function(response)
	  		{
	  			if(response.status === '1')
	  				$("#outside_filter").html(response.str); 
	  		}
	  	}); 
	  }, 2000);

	});

	$(document).on('keyup', '#searching', function(event) {
	  event.preventDefault(); 
	  table1.draw();
	});


	$('#view_report_modal').on('hidden.bs.modal', function () {
		$("#download").attr("href","");
		$("#put_row_id").val('');
		$("#searching").val("");
		table.draw();
	});

	var Edit = "<?php echo $Edit; ?>";
	var Report = "<?php echo $Report; ?>";
	var Delete = "<?php echo $Delete; ?>";
	var PauseCampaign = "<?php echo $PauseCampaign; ?>";
	var StartCampaign = "<?php echo $StartCampaign; ?>";
	
	
	var Doyouwanttopausethiscampaign = "<?php echo $Doyouwanttopausethiscampaign; ?>";

	$(document).on('click','.pause_campaign_info',function(e){
		e.preventDefault();
		swal({
			title: '',
			text: Doyouwanttopausethiscampaign,
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
					url:"<?php echo base_url('comment_automation/ajax_autoreply_pause')?>",
					data: {table_id:table_id},
					success:function(response){ 
			        iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been paused successfully."); ?>',position: 'bottomRight'});
						  $(".page_list_item.active").click();
					}
				});
			} 
		});

	});


	var Doyouwanttostarthiscampaign = "<?php echo $Doyouwanttostarthiscampaign; ?>";

	$(document).on('click','.play_campaign_info',function(e){
		e.preventDefault();
		swal({
			title: '',
			text: Doyouwanttostarthiscampaign,
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
					url:"<?php echo base_url('comment_automation/ajax_autoreply_play')?>",
					data: {table_id:table_id},
					success:function(response){ 
			        iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been started successfully."); ?>',position: 'bottomRight'});
						  $(".page_list_item.active").click();
					}
				});
			} 
		});

	});


	var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $Doyouwanttodeletethisrecordfromdatabase; ?>";

	$(document).on('click','.delete_report',function(e){
		e.preventDefault();
		swal({
			title: '<?php echo $this->lang->line("Are you sure?"); ?>',
			text: Doyouwanttodeletethisrecordfromdatabase,
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
					url:"<?php echo base_url('comment_automation/ajax_autoreply_delete')?>",
					data: {table_id:table_id},
					success:function(response){ 
			      iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',position: 'bottomRight'});
						$(".page_list_item.active").click();
					}
				});
			} 
		});

	});



	var autocomment_table1 = '';
	var autocomment_perscroll1;
	$(document).on('click','.autocomment_view_report',function(e){
	  e.preventDefault();

		var table_id = $(this).attr('table_id');
		if(table_id !='') 
		{
			$("#autocomment_put_row_id").val(table_id);
		}

		$("#autocomment_view_report_modal").modal();

		if (autocomment_table1 == '')
		{
			setTimeout(function(){
				autocomment_table1 = $("#autocomment_mytable1").DataTable({
				    serverSide: true,
				    processing:true,
				    bFilter: false,
				    order: [[ 5, "desc" ]],
				    pageLength: 10,
				    ajax: {
				        url: base_url+'comment_automation/ajax_get_autocomment_reply_info',
				        type: 'POST',
				        data: function ( d )
				        {
				            d.table_id = $("#autocomment_put_row_id").val();
				            d.searching = $("#autocomment_searching").val();
				        }
				    },
				    language: 
				    {
				       url: base_url+"assets/modules/datatables/language/"+selected_language+".json"
				    },
				    dom: '<"top"f>rt<"bottom"lip><"clear">',
				    columnDefs: [
				      {
				          targets: '',
				          className: 'text-center'
				      },
				      {
				          targets: '',
				          sortable: false
				      }
				    ],
				    fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
				    if(areWeUsingScroll)
				    {
				    	if (autocomment_perscroll1) autocomment_perscroll1.destroy();
				    		autocomment_perscroll1 = new PerfectScrollbar('#autocomment_mytable1_wrapper .dataTables_scrollBody');
				    }
				    },
				    scrollX: 'auto',
				    fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
				    	if(areWeUsingScroll)
				    	{ 
				    	if (autocomment_perscroll1) autocomment_perscroll1.destroy();
				    	autocomment_perscroll1 = new PerfectScrollbar('#autocomment_mytable1_wrapper .dataTables_scrollBody');
				    	}
				    }
				});
			}, 500);				
		}
		else setTimeout(function(){autocomment_table1.draw();}, 500);
	});

	$(document).on('keyup', '#autocomment_searching', function(event) {
	  event.preventDefault(); 
	  autocomment_table1.draw();
	});

	$('#autocomment_view_report_modal').on('hidden.bs.modal', function () {
		$("#autocomment_put_row_id").val('');
		$("#autocomment_searching").val("");
		autocomment_table1.draw();
	});

	$(document).on('click','.autocomment_pause_campaign_info',function(e){
		e.preventDefault();
		swal({
			title: '',
			text: Doyouwanttopausethiscampaign,
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
					url:base_url+"comment_automation/ajax_autocomment_pause",
					data: {table_id:table_id},
					success:function(response){ 
			      iziToast.success({title: '',message: global_lang_campaign_paused_successfully,position: 'bottomRight'});
						$(".page_list_item.active").click();
					}
				});
			} 
		});

	});

	$(document).on('click','.autocomment_play_campaign_info',function(e){
		e.preventDefault();
		swal({
			title: '',
			text: Doyouwanttostarthiscampaign,
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
					url:base_url+"comment_automation/ajax_autocomment_play",
					data: {table_id:table_id},
					success:function(response){ 
			      iziToast.success({title: '',message: global_lang_campaign_started_successfully,position: 'bottomRight'});
						$(".page_list_item.active").click();
					}
				});
			} 
		});

	});


	$(document).on('click','.autocomment_delete_report',function(e){
		e.preventDefault();
		swal({
			title: '<?php echo $this->lang->line("Are you sure?"); ?>',
			text: Doyouwanttodeletethisrecordfromdatabase,
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
					url:base_url+"comment_automation/ajax_autocomment_delete",
					data: {table_id:table_id},
					success:function(response){ 
			      iziToast.success({title: '',message: global_lang_campaign_deleted_successfully,position: 'bottomRight'});
						$(".page_list_item.active").click();
					}
				});
			} 
		});

	});


	$(document).on('click','.get_all_comments',function(e){
	  e.preventDefault();

	  	var page_table_id = $(this).attr("page_table_id");
	  	var post_id = $(this).attr("post_id");
	    $("#all_comments_modal").modal();

	    $("#all_comments_modal_contents").html('<div class="text-center waiting p-4"><i class="fas fa-spinner fa-spin blue text-center font_size_60px"></i></div');

	  	$.ajax({
	  		url: base_url+'comment_automation/get_all_comments_of_post',
	  		type: 'post',
	  		data: {page_table_id:page_table_id, post_id:post_id},
	  		success:function(response) {
	  			$("#all_comments_modal_contents").html(response);
	  		}
	  	})

	});

</script>

<div class="modal fade" id="view_report_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="far fa-list-alt"></i> <?php echo $this->lang->line("Report of Auto Reply");?>
            	<small>
            	(
            	<?php 
            	$delete_junk_data_after_how_many_days = $this->config->item("delete_junk_data_after_how_many_days");
            	if($delete_junk_data_after_how_many_days=="") $delete_junk_data_after_how_many_days = 30;
            	?>
            	<?php echo $this->lang->line("Details data shows for last")." : ".$delete_junk_data_after_how_many_days." ".$this->lang->line("days"); ?>
            	)
              </small>
        	  </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body data-card">
              <div class="row">
        			<div class="col-12 text-center" id="outside_filter"></div>
        			<br><br>
        			<div class="col-12 col-md-9">
			  		<input type="text" id="searching" name="searching" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
    			</div>
    			<div class="col-12 col-md-3">
			   		<a href="" target="_blank" class="btn btn-outline-primary download_lead_list float-right" id="download"><i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("Download lead list"); ?></a>                         
    			</div>
                  <div class="col-12">
                    <div class="table-responsive2">
                      <input type="hidden" id="put_row_id">
                      <table class="table table-bordered" id="mytable1">
                        	<thead>
                            <tr>
                              <th>#</th>
                              <th><?php echo $this->lang->line("Comment"); ?></th> 
                              <th><?php echo $this->lang->line("name"); ?></th> 
                              <th><?php echo $this->lang->line("comment time"); ?></th>      
                              <th><?php echo $this->lang->line("reply time"); ?></th>
                              <th><?php echo $this->lang->line("comment reply message"); ?></th>
                              <th><?php echo $this->lang->line("private reply message"); ?></th>
                              <th><?php echo $this->lang->line("comment reply status"); ?></th>      
                              <th><?php echo $this->lang->line("private reply status"); ?></th> 
                              <th><?php echo $this->lang->line("Hide/Delete status"); ?></th> 
                            </tr>
                        	</thead>
                      </table>
                    </div>
                  </div> 
              </div>               
          </div>
      </div>
  </div>
</div>


<div class="modal fade" id="autocomment_view_report_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-comments"></i> <?php echo $this->lang->line("Auto Comment Report");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body data-card">
                <div class="row">
          			<div class="col-12 col-md-9">
      			  		<input type="text" id="autocomment_searching" name="autocomment_searching" class="form-control width_200px" placeholder="<?php echo $this->lang->line("Search..."); ?>">                                          
          			</div>
                    <div class="col-12">
                      <div class="table-responsive2">
                        <input type="hidden" id="autocomment_put_row_id">
                        <table class="table table-bordered" id="autocomment_mytable1">
                          	<thead>
	                            <tr>
	                              <th>#</th>
	                              <th><?php echo $this->lang->line("Comment ID"); ?></th> 
	                              <th><?php echo $this->lang->line("Comment"); ?></th> 
	                              <th><?php echo $this->lang->line("comment time"); ?></th>      
	                              <th><?php echo $this->lang->line("Schedule Type"); ?></th>
	                              <th><?php echo $this->lang->line("Comment Status"); ?></th>
	                            </tr>
                          	</thead>
                        </table>
                      </div>
                    </div> 
                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="all_comments_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div id="all_comments_modal_contents"></div>
            </div>
        </div>
    </div>
</div>

<!-- FB auto reply and comment report section ends here -->