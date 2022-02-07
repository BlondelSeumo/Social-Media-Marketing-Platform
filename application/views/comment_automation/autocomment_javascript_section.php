<script>
"use strict";
$(document).ready(function(){

	var base_url = "<?php echo base_url(); ?>";
	// start of auto comment section
	$(document).on('click','.enable_auto_commnet_template',function(){
	
		var permalink_url = $(this).attr('permalink_url');
		var template_type = $(this).attr('template_type');
		var page_table_id = $(this).attr('page_table_id');
		var post_id = $(this).attr('post_id');
		var manual_enable_template = $(this).attr('manual_enable_template');
		var Pleaseprovidepostid = "<?php echo $Pleaseprovidepostid; ?>";

		if(typeof(post_id) === 'undefined' || post_id == '')
		{
			swal('<?php echo $this->lang->line("Warning"); ?>', Pleaseprovidepostid, 'warning');
			return false;
		}

		$("#auto_campaign_name_template").val('');
		$("#auto_comment_template_id").val('0');
		$("#schedule_now").prop("checked", true);
		$("#schedule_time").val('');
		$("#time_zone").val('');
		$(".schedule_block_item").show();
		$(".schedule_block_item_new").hide();
		$("#periodic_time").val('');
		$("#periodic_time_zone").val('');
		$("#campaign_start_time").val('');
		$("#campaign_end_time").val('');
		$("#random").prop("checked", true);

		$("#auto_reply_page_id_template").val(page_table_id);
		$("#auto_reply_post_id_template").val(post_id);
		$("#manual_enable_template").val(manual_enable_template);
		$("#autocomment_template_type").val(template_type);
		$("#autocomment_permalink_url").val(permalink_url);
		$("#response_status_template").html('');

		$("#auto_reply_message_modal_template").addClass("modal");
		$("#auto_reply_message_modal_template").modal();

		$("#manual_reply_by_post_template").removeClass('modal');
	});

	$(document).on('click','#save_button_template',function(){
		var post_id = $("#auto_reply_post_id_template").val();
		var Youdidntselectanytemplate = "<?php echo $Youdidntselectanytemplate; ?>";
		var Youdidntselectanyoptionyet = "<?php echo $Youdidntselectanyoptionyet; ?>";
		var TypeAutoCampaignname = "<?php echo $TypeAutoCampaignname; ?>";
		var YouDidnotchosescheduleType = "<?php echo $YouDidnotchosescheduleType; ?>";
		var YouDidnotchosescheduletime = "<?php echo $YouDidnotchosescheduletime; ?>";
		var YouDidnotchosescheduletimezone = "<?php echo $YouDidnotchosescheduletimezone; ?>";
		var YoudidnotSelectPerodicTime = "<?php echo $YoudidnotSelectPerodicTime; ?>";
		var YoudidnotSelectCampaignStartTime = "<?php echo $YoudidnotSelectCampaignStartTime; ?>";
		var YoudidnotSelectCampaignEndTime = "<?php echo $YoudidnotSelectCampaignEndTime; ?>";
		

		var schedule_type = $("input[name=schedule_type]:checked").val();
		
		var schedule_time =$("#schedule_time").val();
		var time_zone = $("#time_zone").val();
		var periodic_time = $("#periodic_time").val();
		var campaign_start_time = $("#campaign_start_time").val();
		var campaign_end_time = $("#campaign_end_time").val();
		var auto_comment_template_id = $("#auto_comment_template_id").val();
		var auto_campaign_name_template = $("#auto_campaign_name_template").val().trim();

		if (typeof(schedule_type)==='undefined')
		{
			swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoptionyet, 'warning');
			return false;
		}	

		if(auto_campaign_name_template == ''){
			swal('<?php echo $this->lang->line("Warning"); ?>', TypeAutoCampaignname, 'warning');
			return false;
		}	

		if(auto_comment_template_id == 0){
			swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanytemplate, 'warning');
			return false;
		}			

		if(schedule_type == ''){
			swal('<?php echo $this->lang->line("Warning"); ?>', YouDidnotchosescheduleType, 'warning');
			return false;
		}
		
		if(schedule_type == "onetime")
		{
			if(schedule_time == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YouDidnotchosescheduletime, 'warning');
				return false;
			}				
			if(time_zone == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YouDidnotchosescheduletimezone, 'warning');
				return false;
			}
		}
		if(schedule_type == "periodic")
		{
			if(periodic_time == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YoudidnotSelectPerodicTime, 'warning');
				return false;
			}
			if($("#periodic_time_zone").val() == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YouDidnotchosescheduletimezone, 'warning');
				return false;
			}					
			if(campaign_start_time == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YoudidnotSelectCampaignStartTime, 'warning');
				return false;
			}			
			if(campaign_end_time == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YoudidnotSelectCampaignEndTime, 'warning');
				return false;
			}

			var comment_start_time=$("#comment_start_time").val();
			var comment_end_time=$("#comment_end_time").val();
			var rep1 = parseFloat(comment_start_time.replace(":", "."));
			var rep2 = parseFloat(comment_end_time.replace(":", "."));

			if( comment_start_time== '' ||  comment_end_time== ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select comment between times.');?>", 'warning');
				return false;
			}

			if(rep1 >= rep2)
			{
				swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Comment between start time must be less than end time.');?>", 'warning');
				return false;
			}
		}        

	  
		$(this).addClass('btn-progress');

		var queryString = new FormData($("#auto_reply_info_form_template")[0]);
		var AlreadyEnabled = "<?php echo $AlreadyEnabled; ?>";
		$.ajax({
			type:'POST' ,
			url: base_url+"comment_automation/ajax_autocomment_reply_submit",
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
							  $("#auto_reply_message_modal_template").modal('hide');
							  $("#manual_reply_by_post").modal('hide');
							  $("#manual_check_button_div").html('');
							});
					$("button[post_id="+post_id+"][manual_enable_template='no']").removeClass('btn-outline-success').addClass('btn-outline-warning disabled').html(AlreadyEnabled);
				}
				else
				{
					swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
				}
			}

		});

	});

	$(document).on('click','#modal_close_template',function(){
		$(".page_list_item.active").click();
		$("#auto_reply_message_modal_template").modal('hide');
		$("#manual_reply_by_post").modal('hide');
	});

	$(document).on('click','#edit_modal_close_template',function(){        	
		$(".page_list_item.active").click();
		$("#edit_auto_reply_message_modal_template").modal('hide');
	});

	$(document).on('click','.edit_reply_info_template',function(){

		$("#manual_edit_reply_by_post_template").removeClass('modal');
		$("#edit_auto_reply_message_modal_template").addClass("modal");
		$("#edit_response_status_template").html("");
		var table_id = $(this).attr('table_id');

		$(".previewLoader").show();
		$.ajax({
		  type:'POST' ,
		  url:"<?php echo site_url();?>comment_automation/ajax_edit_autocomment_info",
		  data:{table_id:table_id},
		  dataType:'JSON',
		  success:function(response){

			$("#edit_auto_reply_page_id_template").val(response.edit_auto_reply_page_id);
			$("#edit_auto_reply_post_id_template").val(response.edit_auto_reply_post_id);
			$("#edit_campaign_name_template").val(response.edit_campaign_name);

			if(response.edit_schedule_type == 'onetime')
			{				
				$("#edit_schedule_type_o").attr('checked',true);
				$(".schedule_block_item_o").show();
				$(".schedule_block_item_new_p").hide();
				
				$("#edit_schedule_time_o").val(response.edit_schedule_time_o);
				$("#edit_time_zone_o").val(response.edit_time_zone_o);
			}
			if(response.edit_schedule_type == 'periodic')
			{				
				$("#edit_schedule_type_p").attr('checked',true);
				$(".schedule_block_item_new_p").show();
				$(".schedule_block_item_o").hide();
				$("#edit_periodic_time").val(response.edit_periodic_time);
				$("#edit_campaign_start_time").val(response.edit_campaign_start_time);
				$("#edit_campaign_end_time").val(response.edit_campaign_end_time);
				$("#edit_comment_start_time").val(response.edit_comment_start_time);
				$("#edit_comment_end_time").val(response.edit_comment_end_time);
				$("#edit_periodic_time_zone").val(response.edit_periodic_time_zone);
				if(response.edit_auto_comment_type=='random')
				{
					$("#edit_random").attr('checked',true);

				}
				if(response.edit_auto_comment_type =='serially')
				{
					$("#edit_serially").attr('checked',true);
				}

			}
				
		  $("#edit_auto_comment_template_id").val(response.edit_auto_comment_template_id);
		  $("#edit_auto_reply_message_modal_template").modal();
		  }
		});
		setTimeout(function(){		
			$(".previewLoader").hide();			
		},2000);
					
		
	});


	$(document).on('click','#edit_save_button_template',function(){
		var post_id = $("#edit_auto_reply_post_id_template").val();
		var edit_campaign_name = $("#edit_campaign_name_template").val();
		var edit_schedule_type = $("input[name=edit_schedule_type]:checked").val();
		var edit_schedule_time_o = $("#edit_schedule_time_o").val();
		var edit_time_zone_o = $("#edit_time_zone_o").val();
		var edit_periodic_time = $("#edit_periodic_time").val();
		var edit_campaign_start_time = $("#edit_campaign_start_time").val();
		var edit_campaign_end_time = $("#edit_campaign_end_time").val();
		var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
		var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
		var YouDidnotchosescheduletime = "<?php echo $YouDidnotchosescheduletime; ?>";
		var YouDidnotchosescheduletimezone = "<?php echo $YouDidnotchosescheduletimezone; ?>";
		var YoudidnotSelectPerodicTime = "<?php echo $YoudidnotSelectPerodicTime; ?>";
		var YoudidnotSelectCampaignStartTime = "<?php echo $YoudidnotSelectCampaignStartTime; ?>";
		var YoudidnotSelectCampaignEndTime = "<?php echo $YoudidnotSelectCampaignEndTime; ?>";

		if (typeof(edit_schedule_type)==='undefined')
		{
			swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntselectanyoption, 'warning');
			return false;
		}

		if(edit_campaign_name == ''){
			swal('<?php echo $this->lang->line("Warning"); ?>', Youdidntprovideallinformation, 'warning');
			return false;
		}

		if($("#edit_auto_comment_template_id").val()== 0){
			swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("you have not select any template.");?>', 'warning');
			return false;
		}	

		if(edit_schedule_type == "onetime")
		{
			if(edit_schedule_time_o == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YouDidnotchosescheduletime, 'warning');
				return false;
			}				
			if(edit_time_zone_o == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YouDidnotchosescheduletimezone, 'warning');
				return false;
			}
		}
		if(edit_schedule_type == "periodic")
		{
			if(edit_periodic_time == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YoudidnotSelectPerodicTime, 'warning');
				return false;
			}	
			if($("#edit_periodic_time_zone").val() == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YouDidnotchosescheduletimezone, 'warning');
				return false;
			}				
			if(edit_campaign_start_time == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YoudidnotSelectCampaignStartTime, 'warning');
				return false;
			}			
			if(edit_campaign_end_time == ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', YoudidnotSelectCampaignEndTime, 'warning');
				return false;
			}
			var edit_comment_start_time=$("#edit_comment_start_time").val();
			var edit_comment_end_time=$("#edit_comment_end_time").val();
			var rep1 = parseFloat(edit_comment_start_time.replace(":", "."));
			var rep2 = parseFloat(edit_comment_end_time.replace(":", "."));

			if( edit_comment_start_time== '' ||  edit_comment_end_time== ''){
				swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select comment between times.');?>", 'warning');
				return false;
			}

			if(rep1 >= rep2)
			{
				swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Comment between start time must be less than end time.');?>", 'warning');
				return false;
			}
		}

		$(this).addClass('btn-progress');

		var queryString = new FormData($("#edit_auto_reply_info_form_template")[0]);
		$.ajax({
			type:'POST' ,
			url: base_url+"comment_automation/ajax_update_autocomment_submit",
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
							  $("#edit_auto_reply_message_modal_template").modal('hide');
							});
				}
				else
				{
					swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
				}
			}

		});

	});


	$(document).on('change','input[name=schedule_type]',function(){
		if($("input[name=schedule_type]:checked").val()=="onetime")
		{
			$(".schedule_block_item").show();
			$(".schedule_block_item_new").hide();
			$("#periodic_time").val("");
			$("#campaign_start_time").val("");
			$("#campaign_end_time").val("");
		}
		else
		{
			$("#schedule_time").val("");
			$("#time_zone").val("");
			$(".schedule_block_item_new").show();
			$(".schedule_block_item").hide();
		}
	});



	$(document).on('change','input[name=edit_schedule_type]',function(){
		if($("input[name=edit_schedule_type]:checked").val()=="onetime")
		{
			$(".schedule_block_item_o").show();
			$(".schedule_block_item_new_p").hide();
			$("#periodic_time_p").val("");
			$("#campaign_start_time_p").val("");
			$("#campaign_end_time_p").val("");
		}
		else
		{
			$("#schedule_time_o").val("");
			$("#time_zone_o").val("");
			$(".schedule_block_item_new_p").show();
			$(".schedule_block_item_o").hide();
		}
	});


	$(document).ready(function(){
		$(".schedule_block_item_new").hide();

		var today = new Date();
		var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
		$('.datepicker_x').datetimepicker({
			theme:'light',
			format:'Y-m-d H:i:s',
			formatDate:'Y-m-d H:i:s',
			minDate: today,
			maxDate: next_date

		})

		$('.datetimepicker2').datetimepicker({
		  datepicker:false,
		  format:'H:i'
		});

	});
});
	// end of auto comment section
</script>