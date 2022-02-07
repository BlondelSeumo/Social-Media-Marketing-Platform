"use strict";
var edit = global_lang_edit;
var report = global_lang_report;
var deletet = global_lang_delete;
var pausecampaign = global_lang_pause_campaign;
var startcampaign = global_lang_start_campaign;
$("document").ready(function(){
	
	$('[data-toggle="popover"]').popover(); 
	$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

	$(".schedule_block_item").hide();
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


	// datatable section started
	var perscroll;
	var table = $("#mytable").DataTable({
	    serverSide: true,
	    processing:true,
	    bFilter: true,
	    order: [[ 1, "desc" ]],
	    pageLength: 10,
	    ajax: 
	    {
	        "url": base_url+'comment_automation/all_auto_comment_report_data',
	        "type": 'POST',
		    data: function ( d )
		    {
		        d.page_id = $('#page_id').val();
		        d.campaign_name = $('#campaign_name').val();
		        d.is_instagram = $("#is_instagram").val();
		    }
	    },
	    language: 
	    {
	       url: base_url+"assets/modules/datatables/language/"+selected_language+".json"
	    },
	    dom: '<"top">rt<"bottom"lip><"clear">',
	    columnDefs: [
	        {
	          targets: [1],
	          visible: false
	        },
	        {
	          targets: [0,1,2,5,7,8,10],
	          sortable: false
	        },
	        {
	        	targets:[0,2,5,6,7,8,9],
	        	className:'text-center'
	        }
	    ],
	    fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
	    if(areWeUsingScroll)
	    {
	    	if (perscroll) perscroll.destroy();
	    		perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
	    }
	    },
	    scrollX: 'auto',
	    fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
	    	if(areWeUsingScroll)
	    	{ 
	    	if (perscroll) perscroll.destroy();
	    	perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
	    	}
	    }
	});

	$(document).on('change', '#page_id', function(event) {
	  event.preventDefault(); 
	  table.draw();
	});

	var post_id = instagram_all_auto_comment_report_post_id;
	if(post_id != 0) $("#search_submit").click();

	var page_id = instagram_all_auto_comment_report_page_id;
	if(page_id != 0) $("#search_submit").click();


	$(document).on('click', '#search_submit', function(event) {
	  event.preventDefault(); 
	  table.draw();
	});
	// End of datatable section


	// report table started
	var table1 = '';
	var perscroll1;
	$(document).on('click','.view_report',function(e){
	  e.preventDefault();

		var table_id = $(this).attr('table_id');
		if(table_id !='') 
		{
			$("#put_row_id").val(table_id);
		}

		$("#view_report_modal").modal();

		if (table1 == '')
		{
			setTimeout(function(){
				table1 = $("#mytable1").DataTable({
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
				            d.table_id = $("#put_row_id").val();
				            d.searching = $("#searching").val();
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
			}, 500);				
		}
		else setTimeout(function(){table1.draw();}, 500);
	});

	$(document).on('keyup', '#searching', function(event) {
	  event.preventDefault(); 
	  table1.draw();
	});

	$('#view_report_modal').on('hidden.bs.modal', function () {
		$("#download").attr("href","");
		$("#put_row_id").val('');
		$("#searching").val("");
		table1.draw();
		table.draw();
	});

	$(document).on('click','#edit_modal_close',function(){        
		var manual_post_id = $("#manual_edit_post_id").val();
		if(manual_post_id != '')
		{
			$("#edit_auto_reply_message_modal").modal("hide");
			$("#manual_edit_reply_by_post").modal("hide");
			$("#manual_edit_post_id").val('');
			table.draw();
		}
		else
		{
			$("#edit_auto_reply_message_modal").removeClass("modal");
			table.draw();
		}
	});	
	

	$(document).on('click','.pause_campaign_info',function(e){
		e.preventDefault();
		swal({
			title: '',
			text: instagram_all_auto_comment_report_doyouwanttopausethiscampaign,
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
						table.draw();
					}
				});
			} 
		});

	});

	$(document).on('click','.renew_campaign',function(){		
		var table_id = $(this).attr('table_id');
		$.ajax({
			type:'POST' ,
			url: base_url+"comment_automation/ajax_autocomment_renew_campaign",
			data: {table_id:table_id},
			success:function(response){
				table.draw();
			}
		});		
	});

	
	$(document).on('click','.play_campaign_info',function(e){
		e.preventDefault();
		swal({
			title: '',
			text: instagram_all_auto_comment_report_doyouwanttostarthiscampaign,
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
						table.draw();
					}
				});
			} 
		});

	});



	$(document).on('click','.force',function(e){
		e.preventDefault();
		swal({
			title: global_lang_are_you_sure,
			text: instagram_all_auto_comment_report_doyoureallywanttoreprocessthiscampaign,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
				var id = $(this).attr('id');

				$.ajax({
					context: this,
					type:'POST' ,
					url:base_url+"comment_automation/autocomment_force_reprocess_campaign",
					// dataType: 'json',
					data: {id:id},
					success:function(response){ 
						if(response=='1')
						{
							iziToast.success({title: '',message: global_lang_campaign_force_started_successfully,position: 'bottomRight'});
							table.draw();
						}
						else 
						iziToast.error({title: '',message: instagram_all_auto_comment_report_alreadyenabled,position: 'bottomRight'});
					}
				});
			} 
		});

	});


	$(document).on('click','.delete_report',function(e){
		e.preventDefault();
		swal({
			title: global_lang_are_you_sure,
			text: instagram_all_auto_comment_report_doyouwanttodeletethisrecordfromdatabase,
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
						table.draw();
					}
				});
			} 
		});

	});


	$(document).on('click','.edit_reply_info',function(e){
		e.preventDefault();
	
		$(".previewLoader").show();
		$("#manual_edit_reply_by_post").removeClass('modal');
		$("#edit_auto_reply_message_modal").addClass("modal");
		$("#edit_response_status").html("");
		var table_id = $(this).attr('table_id');
		$.ajax({
		  type:'POST' ,
		  url:base_url+"comment_automation/ajax_edit_autocomment_info",
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
            	$("#edit_time_zone_o").val(response.edit_time_zone_o).change();

            }
            if(response.edit_schedule_type == 'periodic')
            {
                
            	$("#edit_schedule_type_p").attr('checked',true);
            	$(".schedule_block_item_new_p").show();
            	$(".schedule_block_item_o").hide();
            	$("#edit_periodic_time").val(response.edit_periodic_time).change();
            	$("#edit_campaign_start_time").val(response.edit_campaign_start_time);
            	$("#edit_campaign_end_time").val(response.edit_campaign_end_time);
            	$("#edit_comment_start_time").val(response.edit_comment_start_time);
            	$("#edit_comment_end_time").val(response.edit_comment_end_time);
            	$("#edit_periodic_time_zone").val(response.edit_periodic_time_zone).change();
            	if(response.edit_auto_comment_type=='random')
            	{
            		$("#edit_random").attr('checked',true);

            	}
            	if(response.edit_auto_comment_type =='serially')
            	{
            		$("#edit_serially").attr('checked',true);
            	}

            }
     
	
          $("#edit_auto_comment_template_id").val(response.edit_auto_comment_template_id).change();
          $("#edit_auto_reply_message_modal").modal();
		  }
		});
			
		setTimeout(function(){			
			$(".previewLoader").hide();				
		},1000);
		
		
	});

	$(document).on('click','#edit_add_more_button',function(){
		if(edit_content_counter == 11)
			$("#edit_add_more_button").hide();
		$("#edit_content_counter").val(edit_content_counter);

		$("#edit_filter_div_"+edit_content_counter).show();
		
		/** Load Emoji For Filter Word when click on add more button during Edit**/
			
		$("#edit_filter_message_"+edit_content_counter).emojioneArea({
    		autocomplete: false,
			pickerPosition: "bottom"
 	 	});
		
		$("#edit_comment_reply_msg_"+edit_content_counter).emojioneArea({
    		autocomplete: false,
			pickerPosition: "bottom"
 	 	});
		
		edit_content_counter++;

	});



	$(document).on('click','#edit_save_button',function(){
		var post_id = $("#edit_auto_reply_post_id_template").val();
		var edit_campaign_name = $("#edit_campaign_name_template").val();
		var edit_schedule_type = $("input[name=edit_schedule_type]:checked").val();
		var edit_schedule_time_o = $("#edit_schedule_time_o").val();
		var edit_time_zone_o = $("#edit_time_zone_o").val();
		var edit_periodic_time = $("#edit_periodic_time").val();
		var edit_campaign_start_time = $("#edit_campaign_start_time").val();
		var edit_campaign_end_time = $("#edit_campaign_end_time").val();

		if (typeof(edit_schedule_type)==='undefined')
		{
			swal(global_lang_warning, instagram_all_auto_comment_report_youdidntselectanyoption, 'warning');
			return false;
		}

		if(edit_campaign_name == ''){
			swal(global_lang_warning, instagram_all_auto_comment_report_youdidntprovideallinformation, 'warning');
			return false;
		}

		if($("#edit_auto_comment_template_id").val()== 0){
			swal(global_lang_warning, instagram_all_auto_comment_report_youdidntselectanytemplate, 'warning');
			return false;
		}	

		if(edit_schedule_type == "onetime")
		{
			if(edit_schedule_time_o == ''){
				swal(global_lang_warning, instagram_all_auto_comment_report_youdidnotchosescheduletime, 'warning');
				return false;
			}				
			if(edit_time_zone_o == ''){
				swal(global_lang_warning, instagram_all_auto_comment_report_youdidnotchosescheduletimezone, 'warning');
				return false;
			}
		}
		if(edit_schedule_type == "periodic")
		{
			if(edit_periodic_time == ''){
				swal(global_lang_warning, instagram_all_auto_comment_report_youdidnotselectperodictime, 'warning');
				return false;
			}	
			if($("#edit_periodic_time_zone").val() == ''){
				swal(global_lang_warning, instagram_all_auto_comment_report_youdidnotchosescheduletimezone, 'warning');
				return false;
			}				
			if(edit_campaign_start_time == ''){
				swal(global_lang_warning, instagram_all_auto_comment_report_youdidnotselectcampaignstarttime, 'warning');
				return false;
			}			
			if(edit_campaign_end_time == ''){
				swal(global_lang_warning, instagram_all_auto_comment_report_youdidnotselectcampaignendtime, 'warning');
				return false;
			}

			var edit_comment_start_time=$("#edit_comment_start_time").val();
			var edit_comment_end_time=$("#edit_comment_end_time").val();
			var rep1 = parseFloat(edit_comment_start_time.replace(":", "."));
			var rep2 = parseFloat(edit_comment_end_time.replace(":", "."));

			if( edit_comment_start_time== '' ||  edit_comment_end_time== ''){
				swal(global_lang_warning, instagram_all_auto_comment_report_please_select_comment_between_times, 'warning');
				return false;
			}

			if(rep1 >= rep2)
			{
				swal(global_lang_warning, instagram_all_auto_comment_report_comment_between_start_time_must_be_less_than_end_time, 'warning');
				return false;
			}

		}		

		$(this).addClass('btn-progress');

		var queryString = new FormData($("#edit_auto_reply_info_form")[0]);
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
		         	swal(global_lang_success, response.message, 'success').then((value) => {
	         			  $("#edit_auto_reply_message_modal").modal('hide');
						  table.draw();
						});
		        }
		        else
		        {
		         	swal(global_lang_error, response.message, 'error');
		        }
	    	}

	    });

	});

	$(document).on('click','.cancel_button',function(){
		$("#edit_auto_reply_message_modal").modal('hide');
	    table.draw();
	});

});



