<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-comments"></i> <?php echo $page_title;?></h1>
    <div class="section-header-button">
      <a href="<?php echo base_url('messenger_bot_broadcast/create_conversation_campaign'); ?>" class="btn btn-primary"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Campaign"); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="<?php echo base_url('messenger_bot_broadcast'); ?>"><?php echo $this->lang->line("Broadcasting");?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title;?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <style type="text/css">
    #search_page_id{width: 145px;}
    #search_status{width: 95px;}
    @media (max-width: 575.98px) {
      #search_page_id{width: 90px;}
      #search_status{width: 75px;}
    }
  </style>

  <?php $status_options = array(""=>$this->lang->line("Status"),"0"=>$this->lang->line("Pending"),"1"=>$this->lang->line("Processing"),"2"=>$this->lang->line("Completed")) ?>

  <div class='text-center text-danger' style='padding:12px;border:.5px solid #dee2e6; color:var(--blue);background: #fff;font-size: 20px;'>
    <?php  echo $this->lang->line("Conversation broadcast feature is going to be deprecated after 30th June,2020 for all old Facebook APPs. For new Facebook APP v6.0, conversation broadcasting feature will not work anymore."); ?>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
            <div class="row">
              <div class="col-12 col-md-9">
                <?php echo 
                '<div class="input-group mb-3" id="searchbox">
                  <div class="input-group-prepend">
                    '.form_dropdown('search_page_id',$page_list,'','class="form-control select2" id="search_page_id"').'
                  </div>
                  <div class="input-group-prepend">
                  '.form_dropdown('search_status',$status_options,'','class="form-control select2" id="search_status"').'
                  </div>

                  <input type="text" class="form-control" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:30%;">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                  </div>
                </div>'; ?>                                          
              </div>

              <div class="col-12 col-md-3">

              	<?php
				echo $drop_menu ='<a href="javascript:;" id="campaign_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="campaign_date_range_val">';
				?>

                                         
              </div>
            </div>

            <div class="table-responsive2">
                <input type="hidden" id="put_page_id">
                <table class="table table-bordered" id="mytable">
                  <thead>
                    <tr>
                      <th>#</th>      
                      <th style="vertical-align:middle;width:20px">
                          <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                      </th>
                      <th><?php echo $this->lang->line("Name"); ?></th>
                      <th><?php echo $this->lang->line("Page Name")?></th>
                      <th><?php echo $this->lang->line("Status"); ?></th>
                      <th><?php echo $this->lang->line("Sent"); ?></th>
                      <th><?php echo $this->lang->line("Actions"); ?></th>
                      <th><?php echo $this->lang->line("Scheduled at"); ?></th>
                      <th><?php echo $this->lang->line("Created at"); ?></th>
                      <th><?php echo $this->lang->line("Labels"); ?></th>
                    </tr>
                  </thead>
                </table>
            </div>
          </div>
        </div>
      </div>       
        
    </div>
  </div>          

</section>


<?php
	$somethingwentwrong = $this->lang->line("Something went wrong.");
	$Doyouwanttopausethiscampaign = $this->lang->line("Do you want to pause this campaign? Pause campaign may not stop the campaign immediately if it is currently processing by cron job. This will affect from next cron job run after it finish currently processing messages.");
	$whenitpause = $this->lang->line("This will affect from next cron job run after it finish currently processing messages.");
	$Doyouwanttostartthiscampaign = $this->lang->line("Do you want to resume this campaign?");
	$doyoureallywanttodeletethiscampaign = $this->lang->line("Do you really want to delete this campaign?");
	$alreadyEnabled = $this->lang->line("This campaign is already enabled for processing.");
	$doyoureallywanttoReprocessthiscampaign = $this->lang->line("Force Reprocessing means you are going to process this campaign again from where it ended. You should do only if you think the campaign is hung for long time and didn't send message for long time. It may happen for any server timeout issue or server going down during last attempt or any other server issue. So only click OK if you think message is not sending. Are you sure to Reprocessing ?");
	$wanttounsubscribe = $this->lang->line("Do you really want to unsubscribe this user?");

 ?>
<script>

	var base_url="<?php echo site_url(); ?>";

	var somethingwentwrong = "<?php echo $somethingwentwrong; ?>";
	var Doyouwanttopausethiscampaign = "<?php echo $Doyouwanttopausethiscampaign; ?>";
	var whenitpause = "<?php echo $whenitpause; ?>";
	var Doyouwanttostartthiscampaign = "<?php echo $Doyouwanttostartthiscampaign; ?>";
	var doyoureallywanttodeletethiscampaign = "<?php echo $doyoureallywanttodeletethiscampaign; ?>";
	var alreadyEnabled = "<?php echo $alreadyEnabled; ?>";
	var doyoureallywanttoReprocessthiscampaign = "<?php echo $doyoureallywanttoReprocessthiscampaign; ?>";
	var wanttounsubscribe = "<?php echo $wanttounsubscribe; ?>";

	$('#campaign_date_range').daterangepicker({
	  ranges: {
	    '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
	    '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
	    '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	  },
	  startDate: moment().subtract(29, 'days'),
	  endDate  : moment()
	}, function (start, end) {
	  $('#campaign_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
	});

	var perscroll;
	var table1 = '';
	table1 = $("#mytable").DataTable({
	  serverSide: true,
	  processing:true,
	  bFilter: false,
	  order: [[ 8, "desc" ]],
	  pageLength: 10,
	  ajax: {
	      url: base_url+'messenger_bot_broadcast/conversation_broadcast_campaign_data',
	      type: 'POST',
	      data: function ( d )
	      {
	          d.search_page_id = $('#search_page_id').val();
	          d.search_value = $('#search_value').val();
	          d.search_status = $('#search_status').val();
	          d.campaign_date_range = $('#campaign_date_range_val').val();
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
	        targets: [4,5,6,7,8],
	        className: 'text-center'
	    },
	    {
	        targets: [6],
	        sortable: false
	    }
	  ],
	  fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
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


	$("document").ready(function(){	   

	    $(document).on('change', '#search_page_id', function(e) {
	        table1.draw();
	    });

	    $(document).on('change', '#search_status', function(e) {
	        table1.draw();
	    });

	    $(document).on('change', '#campaign_date_range_val', function(event) {
        	event.preventDefault(); 
        	table1.draw();
      	});

      	$(document).on('click', '#search_action', function(event) {
        	event.preventDefault(); 
        	table1.draw();
      	});

      	var table2 = '';
	    $(document).on('click','.sent_report',function(e){
	      
	      e.preventDefault();
	      var id = $(this).attr('cam-id');
	      $('#hidden_cam_id').val(id);

	      $("#sent_report_modal").modal();

	      $("#sent_report_body").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 50px"></i></div><br/>');
    	  setTimeout(function(){
	    	$.ajax({
	    		type:'POST' ,
	    		url:"<?php echo site_url();?>messenger_bot_broadcast/campaign_sent_status",
	    		data:{id:id},
	    		dataType:'JSON',
	    		success:function(response){
	    			$("#sent_report_body").html(response.response1);
	    			$("#sent_report_body2").html(response.response2);

	    			if (table2 == '')
	    			{	    			  
	    			  setTimeout(function(){ 
	                  	$("#mytable2_filter").append(response.response3);
	                  	$("[data-toggle=\'tooltip\']").tooltip();
	                  }, 1000);

	    			  var perscroll2;
	    			  table2 = $("#mytable2").DataTable({
	    			      serverSide: true,
	    			      processing:true,
	    			      bFilter: true,
	    			      order: [[ 3, "desc" ]],
	    			      pageLength: 10,
	    			      ajax: {
	    			          url: '<?php echo base_url("messenger_bot_broadcast/campaign_sent_status_data"); ?>',
	    			          type: 'POST',
	    			          dataSrc: function ( json ) 
	    			          {
	    			            $(".table-responsive").niceScroll();
	    			            return json.data;
	    			          },
	    			          data: function ( d )
	    			          {
	    			              d.campaign_id = $('#hidden_cam_id').val();
	    			          }
	    			      },
	    			      language: 
	    			      {
	    			        url: '<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json');?>'
	    			      },
	    			      dom: '<"top"f>rt<"bottom"lip><"clear">',
	    			      columnDefs: [
	    			        {
	    			            targets: [1],
	    			            visible: false
	    			        },
	    			        {
	    			            targets: [0,3],
	    			            className: 'text-center'
	    			        }
	    			      ],
	    			      fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
	    			          if(areWeUsingScroll)
	    			          {
	    			            if (perscroll2) perscroll2.destroy();
	    			            perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
	    			          }
	    			      },
	    			      scrollX: 'auto',
	    			      fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
	    			          if(areWeUsingScroll)
	    			          { 
	    			            if (perscroll2) perscroll2.destroy();
	    			            perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
	    			          }
	    			      }
	    			  });
	    			}
	    			else table2.draw();
	    		}
	    	});	    	

    	  }, 1000);


	    });


	});


	// sweet alert + confirmation

	$(document).on('click','.restart_button',function(e){
		e.preventDefault();
		var table_id = $(this).attr('table_id');

		swal({
			title: '<?php echo $this->lang->line("Force Resume"); ?>',
			text: Doyouwanttostartthiscampaign,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
			    $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
			    $.ajax({
			       context: this,
			       type:'POST' ,
			       url: "<?php echo base_url('messenger_bot_broadcast/restart_campaign')?>",
			       data: {table_id:table_id},
			       success:function(response)
			       {
				       	$(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');
				       	if(response=='1') 
				       	{
				       		$("#sent_report_modal").modal('hide');
				      		iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been resumed by force successfully."); ?>',position: 'bottomRight'});
				       		table1.draw();
				       	}      	
				      	else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
			       }
				});
			} 
		});

	});


	$(document).on('click','.unsubscribe_me',function(){
		var client_id = $(this).attr('client_id');
		var wanttounsubscribe = "<?php echo $wanttounsubscribe; ?>";

		swal({
			title: '<?php echo $this->lang->line("Unsubscribe"); ?>',
			text: wanttounsubscribe,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
			    $(this).addClass('btn-progress btn-danger').removeClass('btn-outline-danger');
			    $.ajax({
			       context: this,
			       type:'POST' ,
			        url: "<?php echo base_url('messenger_bot_broadcast/new_unsubscribe')?>",
		           data: {client_id:client_id},
			       success:function(response)
			       {
				       	$(this).removeClass('btn-progress btn-danger').addClass('btn-outline-danger');
				       	var errormesg  = response;
				       	if(errormesg=="") errormesg = somethingwentwrong;

				      	if(response=='1') 
				       	{
				      		iziToast.success({title: '',message: '<?php echo $this->lang->line("subscriber has been unsubscribed successfully."); ?>',position: 'bottomRight'});
				      		$(this).hide();
				       	}      	
				      	else iziToast.error({title: '',message: errormesg,position: 'bottomRight'});
			       }
				});
			} 
		});
	});


	$(document).on('click','.force',function(e){
		e.preventDefault();
		var id = $(this).attr('id');
		var alreadyEnabled = "<?php echo $alreadyEnabled; ?>";
		var doyoureallywanttoReprocessthiscampaign = "<?php echo $doyoureallywanttoReprocessthiscampaign; ?>";

		swal({
			title: '<?php echo $this->lang->line("Force Re-process Campaign"); ?>',
			text: doyoureallywanttoReprocessthiscampaign,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
			    $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
			    $.ajax({
			       context: this,
			       type:'POST' ,
			       url: "<?php echo base_url('messenger_bot_broadcast/force_reprocess_campaign')?>",
			       data: {id:id},
			       success:function(response)
			       {
				       	$(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

				      	if(response=='1') 
				       	{
				      		iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been re-processed by force successfully."); ?>',position: 'bottomRight'});
				       		table1.draw();
				       	}      	
				      	else iziToast.error({title: '',message: alreadyEnabled,position: 'bottomRight'});
			       }
				});
			} 
		});

	});

	$(document).on('click','.pause_campaign_info',function(e){
		e.preventDefault();
		var table_id = $(this).attr('table_id');

		swal({
			title: '<?php echo $this->lang->line("Pause Campaign"); ?>',
			text: Doyouwanttopausethiscampaign,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
			    $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
			    $.ajax({
			       context: this,
			       type:'POST' ,
			       url: "<?php echo base_url('messenger_bot_broadcast/ajax_campaign_pause')?>",
			       data: {table_id:table_id},
			       success:function(response)
			       {
				       	$(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

				      	if(response=='1') 
				       	{
				      		iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been paused successfully."); ?>',position: 'bottomRight'});
				       		table1.draw();
				       	}      	
				      	else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
			       }
				});
			} 
		});

	});


	$(document).on('click','.play_campaign_info',function(e){
		e.preventDefault();
		var table_id = $(this).attr('table_id');

		swal({
			title: '<?php echo $this->lang->line("Resume Campaign"); ?>',
			text: Doyouwanttostartthiscampaign,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
			    $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
			    $.ajax({
			       context: this,
			       type:'POST' ,
			       url: "<?php echo base_url('messenger_bot_broadcast/ajax_campaign_play')?>",
			       data: {table_id:table_id},
			       success:function(response)
			       {
				       	$(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

				      	if(response=='1') 
				       	{
				      		iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been resumed successfully."); ?>',position: 'bottomRight'});
				       		table1.draw();
				       	}      	
				      	else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
			       }
				});
			} 
		});

	});


	$(document).on('click','.delete',function(e){
		e.preventDefault();

		var id = $(this).attr('id');
	    if (typeof(id)==='undefined')
	    { 
	    	swal('', '<?php echo $this->lang->line("This campaign is in processing state and can not be deleted.");?>', 'warning');
	    	return;
	    }

		swal({
			title: '<?php echo $this->lang->line("Delete Campaign"); ?>',
			text: doyoureallywanttodeletethiscampaign,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
			if (willDelete) 
			{
			    $(this).parent().prev().addClass('btn-progress btn-primary').removeClass('btn-outline-primary');
			    $.ajax({
			       context: this,
			       type:'POST' ,
			       url: "<?php echo base_url('messenger_bot_broadcast/delete_campaign')?>",
			       data: {id:id},
			       success:function(response)
			       {
				       	$(this).parent().prev().removeClass('btn-progress btn-primary').addClass('btn-outline-primary');

				      	if(response=='1') 
				       	{
				      		iziToast.success({title: '',message: '<?php echo $this->lang->line("Campaign has been deleted successfully."); ?>',position: 'bottomRight'});
				       		table1.draw();
				       	}      	
				      	else iziToast.error({title: '',message: somethingwentwrong,position: 'bottomRight'});
			       }
				});
			} 
		});

	});


  	// $(document).on('click','.sent_report',function(e){
  	// 	e.preventDefault();
  	// 	$("#sent_report_modal").modal();
  	// 	var id = $(this).attr('cam-id');

  	// 	$("#sent_report_body").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 50px"></i></div>');

  	// 	setTimeout(function(){
  	// 		$.ajax({
	  //           type:'POST' ,
	  //           url:"<?php echo site_url();?>messenger_bot_broadcast/campaign_sent_status",
	  //           data:{id:id},
	  //           success:function(response){
	  //           	$("#sent_report_body").html(response);
	  //           }
	  //       });
  	// 	}, 1000);

  		
  	// });




</script>



<div class="modal fade" id="sent_report_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-mega">
		<div class="modal-content">
			<div class="modal-header">
				<?php 
				$delete_junk_data_after_how_many_days = $this->config->item("delete_junk_data_after_how_many_days");
	    		if($delete_junk_data_after_how_many_days=="") $delete_junk_data_after_how_many_days = 30;
				?>
			  <h5 class="modal-title"><i class="fas fa-eye"></i> <?php echo $this->lang->line("Campaign Report"); ?> <small>(<?php echo $this->lang->line("Details data shows for last")." : ".$delete_junk_data_after_how_many_days." ".$this->lang->line("days"); ?>)</small></h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>

			<div class="modal-body data-card">
				<input type="hidden" id="hidden_cam_id">
				<div id="sent_report_body"></div>
				<div class="table-responsive2">
				    <table class="table table-bordered" id="mytable2">
				      <thead>
				        <tr>
				          <th>#</th>      
				          <th style="vertical-align:middle;width:20px">
				              <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
				          </th>
				          <th><?php echo $this->lang->line("Subscriber"); ?></th>
				          <th><?php echo $this->lang->line("Sent at")?></th>
				          <th><?php echo $this->lang->line("Response"); ?></th>
				        </tr>
				      </thead>
				    </table>
				</div>
				<div id="sent_report_body2"></div>
			</div>
		</div>
	</div>
</div>



<?php 
echo '
<div class="modal fade" id="error_message_learn" tabindex="-1" role="dialog" aria-labelledby="error_message_learn" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-bug"></i> '.$this->lang->line("Common Error Message").'</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

     <div class="section">                
        <h2 class="section-title"> '.$this->lang->line("(#368) the action attempted has been deemed abusive or is otherwise disallowed").'</h2>
        <p>
         '.$this->lang->line("This error messages comes from Facebook. The possible reasons are below").' : 
         <ol>
              <li>'.$this->lang->line("Your content may be spammy by Facebook filter.").'</li>
              <li>'.$this->lang->line(" You have any link attached that blocked by Facebook").'</li>
              <li>'.$this->lang->line("You are sending message too fast without setting delay.").'</li>
              <li>'.$this->lang->line("If too much people mark your message as spam.").'</li>
              <li>'.$this->lang->line("The subscribers from whom it gets the error message is inactive from many days with your messenger.").'</li>
              <li>'.$this->lang->line("The subscribers who has marked your message as spam in past.").'</li>
        </ol>
        '.$this->lang->line("In this case system automatically mark the subscriber as unviable for future conversation broadcasting campaign. ").'
        </p>

        <h2 class="section-title"> '.$this->lang->line("(#230) Permissions disallow message to user").'</h2>
        <p>
        '.$this->lang->line("This error messages comes from Facebook. The possible reasons are below").' : 
         <ol>
              <li>'.$this->lang->line("Subscribers block your page from sending message from their settings").'</li>
              <li>'.$this->lang->line("Subscriber has deactivated their Facebook account").'</li>
        </ol>
        '.$this->lang->line("In this case system automatically mark the subscriber as unviable for future conversation broadcasting campaign. ").'
        </p>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>'; ?>