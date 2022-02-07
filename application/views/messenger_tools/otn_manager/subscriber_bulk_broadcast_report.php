<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-users"></i> <?php echo $page_title;?></h1>
    <div class="section-header-button">
      <a href="<?php echo base_url('messenger_bot_broadcast/otn_create_subscriber_broadcast_campaign'); ?>" class="btn btn-primary"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Campaign"); ?></a>
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
                    '.form_dropdown('search_page_id',$page_list,$this->session->userdata('selected_global_page_table_id'),'class="form-control select2" id="search_page_id"').'
                  </div>
                  <div class="input-group-prepend">'; ?>

                  <select name="search_status" id="search_status"  class="form-control select2">
                  	<option value=""><?php echo $this->lang->line("status") ?></option>
                  	<option value="0"><?php echo $this->lang->line("Pending") ?></option>
                  	<option value="1"><?php echo $this->lang->line("Processing") ?></option>
                  	<option value="2"><?php echo $this->lang->line("Completed") ?></option>
                  	<option value="3"><?php echo $this->lang->line("Stopped") ?></option>
                  </select>
                  </div>

                  <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">


                  <?php
                  echo 
                  '<input type="text" class="form-control" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:30%;">
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
                      <th><?php echo $this->lang->line("Type")?></th>
                      <th><?php echo $this->lang->line("Status"); ?></th>
                      <th><?php echo $this->lang->line("Actions"); ?></th>
                      <th><?php echo $this->lang->line("Subscriber"); ?></th>
                      <th><?php echo $this->lang->line("Sent"); ?></th>
                      <th><?php echo $this->lang->line("Delivered"); ?></th>
                      <th><?php echo $this->lang->line("Open"); ?></th>
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
	  order: [[ 12, "desc" ]],
	  pageLength: 10,
	  ajax: {
	      url: base_url+'messenger_bot_broadcast/otn_subscriber_broadcast_campaign_data',
	      type: 'POST',
	      data: function ( d )
	      {
	          d.search_page_id = $('#search_page_id').val();
	          d.search_value = $('#search_value').val();
	          d.search_status = $('#search_status').val();
	          d.campaign_date_range = $('#campaign_date_range_val').val();
	          d.csrf_token=	$("#csrf_token").val();
	      }
	  },
	  language: 
	  {
	    url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
	  },
	  dom: '<"top"f>rt<"bottom"lip><"clear">',
	  columnDefs: [
	    {
	        targets: [1,4,9,10],
	        visible: false
	    },
	    {
	        targets: [4,5,6,7,8,9,10,11,12],
	        className: 'text-center'
	    },
	    {
	        targets: [0,6,7],
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
	      var csrf_token = $("#csrf_token").val();

	      $('#hidden_cam_id').val(id);

	      $("#sent_report_modal").modal();

	      $("#sent_report_body").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 50px"></i></div><br/>');
    	  setTimeout(function(){
	    	$.ajax({
	    		type:'POST' ,
	    		url:"<?php echo site_url();?>messenger_bot_broadcast/otn_campaign_sent_status",
	    		data:{id:id,csrf_token:csrf_token},
	    		dataType:'JSON',
	    		success:function(response){
	    			$("#sent_report_body").html(response.response1);

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
	    			          url: '<?php echo base_url("messenger_bot_broadcast/otn_campaign_sent_status_data"); ?>',
	    			          type: 'POST',
	    			          dataSrc: function ( json ) 
	    			          {
	    			            $(".table-responsive").niceScroll();
	    			            return json.data;
	    			          },
	    			          data: function ( d )
	    			          {
	    			              d.campaign_id = $('#hidden_cam_id').val();
	    			              d.csrf_token = $("#csrf_token").val();
	    			          }
	    			      },
	    			      language: 
	    			      {
	    			        url: '<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json');?>'
	    			      },
	    			      dom: '<"top"f>rt<"bottom"lip><"clear">',
	    			      columnDefs: [
	    			        {
	    			            targets: [1,6,7],
	    			            visible: false
	    			        },
	    			        {
	    			            targets: [0,4,5,6,7,8],
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


	$(document).on('click','.restart_button',function(e){
		e.preventDefault();
		var table_id = $(this).attr('table_id');
		var csrf_token = $("#csrf_token").val();

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
			       url: "<?php echo base_url('messenger_bot_broadcast/otn_restart_campaign')?>",
			       data: {table_id:table_id,csrf_token:csrf_token},
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

	$(document).on('click','.force',function(e){
		e.preventDefault();
		var id = $(this).attr('id');
		var csrf_token = $("#csrf_token").val();
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
			       url: "<?php echo base_url('messenger_bot_broadcast/otn_force_reprocess_campaign')?>",
			       data: {id:id,csrf_token:csrf_token},
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
		var csrf_token = $("#csrf_token").val();

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
			       url: "<?php echo base_url('messenger_bot_broadcast/otn_ajax_campaign_pause')?>",
			       data: {table_id:table_id,csrf_token:csrf_token},
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
		var csrf_token = $("#csrf_token").val();

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
			       url: "<?php echo base_url('messenger_bot_broadcast/otn_ajax_campaign_play')?>",
			       data: {table_id:table_id,csrf_token:csrf_token},
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
		var csrf_token = $("#csrf_token").val();
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
			       url: "<?php echo base_url('messenger_bot_broadcast/otn_subscriber_delete_campaign')?>",
			       data: {id:id,csrf_token:csrf_token},
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
				          <th><?php echo $this->lang->line("First Name"); ?></th>
				          <th><?php echo $this->lang->line("Last Name"); ?></th>
				          <th><?php echo $this->lang->line("Subscriber ID"); ?></th>
				          <th><?php echo $this->lang->line("Sent at"); ?></th>
				          <th><?php echo $this->lang->line("Delivered at"); ?></th>
				          <th><?php echo $this->lang->line("Opened at"); ?></th>
				          <th><?php echo $this->lang->line("Sent Response"); ?></th>
				        </tr>
				      </thead>
				    </table>
				</div>
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

        <h2 class="section-title"> '.$this->lang->line("(#551) This person isn't available right now").'</h2>
        <p>
        '.$this->lang->line("This error messages comes from Facebook. The possible reasons are below").' : 
         <ol>
              <li>'.$this->lang->line("Subscriber deactivated their account.").'</li>
              <li>'.$this->lang->line("Subscriber blocked your page.").'</li>
              <li>'.$this->lang->line("Subscriber does not have activity for long days with your page.").'</li>
              <li>'.$this->lang->line("The user may in conversation subscribers as got private reply of comment but never replied back.").'</li>
              <li>'.$this->lang->line("APP may not have pages_messaging approval.").'</li>
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