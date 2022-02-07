<?php $this->load->view('admin/theme/message'); ?>

<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><i class="fa fa-gift"></i> <?php echo $this->lang->line("Offer List");?> </h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; height:659px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."ultrapost/offer_post_list_data"; ?>" 

				pagination="true" 
				rownumbers="true" 
				toolbar="#tb" 
				pageSize="10" 
				pageList="[5,10,15,20,50,100]"  
				fit= "true" 
				fitColumns= "true" 
				nowrap= "true" 
				view= "detailview"
				idField="id"
				>
				
					<!-- url is the link to controller function to load grid data -->					

					<thead>
						<tr>
							<th field="offer_name" sortable="true" formatter="format_name"><?php echo $this->lang->line("Offer Name"); ?></th>
							<th field="page_or_group_or_user_name" sortable="true"><?php echo $this->lang->line("Page"); ?></th>
							<th field="post_type" sortable="true"><?php echo $this->lang->line("Post Type"); ?></th>
							<th field="posting_status" sortable="true"><?php echo $this->lang->line("Status"); ?></th>
							<th field="visit_post" align="center"><?php echo $this->lang->line("Visit Post"); ?></th>
							<!-- <th field="use_this_offer" align="center"><?php echo $this->lang->line("Repost"); ?></th> -->
							<th field="report" align="center"><?php echo $this->lang->line("Details"); ?></th>
							<th field="edit" align="center"><?php echo $this->lang->line("Edit"); ?></th>
							<th field="delete" align="center"><?php echo $this->lang->line("Delete"); ?></th>
							<th field="schedule_time" align="center" sortable="true"><?php echo $this->lang->line("Scheduled at"); ?></th>
							<th field="last_updated_at" align="center" sortable="true"><?php echo $this->lang->line("Last Updated"); ?></th>
							<th field="fb_offer_id" sortable="true"><?php echo $this->lang->line("Offer ID"); ?></th>
							<th field="message" ><?php echo $this->lang->line("Post Content"); ?></th>
						</tr>
					</thead>
				</table>                        
			</div>

			<div id="tb" style="padding:3px">

			<div class="row">
				<div class="col-xs-12">
					<a class="btn btn-info" href="<?php echo base_url("ultrapost/offer_poster");?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("New Post"); ?></a>
				</div>
			</div>

			<?php 			
		        $search_offer_name  = $this->session->userdata('facebook_rx_offer_poster_offer_name');
		        $search_page_or_group_or_user_name  = $this->session->userdata('facebook_rx_offer_poster_page_or_group_or_user_name');
		        $search_scheduled_from = $this->session->userdata('facebook_rx_offer_poster_scheduled_from');
		        $search_scheduled_to = $this->session->userdata('facebook_rx_offer_poster_scheduled_to');
		        $search_post_type= $this->session->userdata('facebook_rx_offer_poster_post_type');
		        $search_posting_status= $this->session->userdata('facebook_rx_offer_poster_posting_status');
			 ?>
 

			<form class="form-inline" style="margin-top:20px">

				<div class="form-group">
					<input id="offer_name" name="offer_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Offer Name'); ?>" value="<?php echo $search_offer_name;?>">
				</div>

				<div class="form-group">
					<input id="page_or_group_or_user_name" name="page_or_group_or_user_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Page Name'); ?>" value="<?php echo $search_page_or_group_or_user_name;?>">
				</div>   

				<div class="form-group">
					<select class="form-control" id="post_type" name="post_type">
						<option <?php if($search_post_type=="") echo 'selected="selected"';?> value=""><?php echo $this->lang->line('Any Offer Type'); ?></option>
						<option <?php if($search_post_type=="image_submit") echo 'selected="selected"';?> value="image_submit">Image</option>
						<option <?php if($search_post_type=="carousel_submit") echo 'selected="selected"';?> value="carousel_submit">Carousel</option>
						<option <?php if($search_post_type=="video_submit") echo 'selected="selected"';?> value="video_submit">Video</option>
						option
					</select>
				</div>

				<div class="form-group">
					<select class="form-control" id="posting_status" name="posting_status">
						<option <?php if($search_posting_status=="") echo 'selected="selected"';?> value=""><?php echo $this->lang->line('Any Stage'); ?></option>
						<option <?php if($search_posting_status==="0") echo 'selected="selected"';?> value="0"><?php echo $this->lang->line('Pending'); ?></option>
						<option <?php if($search_posting_status==="1") echo 'selected="selected"';?> value="1"><?php echo $this->lang->line('Processing'); ?></option>
						<option <?php if($search_posting_status==="2") echo 'selected="selected"';?> value="2"><?php echo $this->lang->line('Completed'); ?></option>
					</select>
				</div>      

				<div class="form-group">
					<input id="scheduled_from" name="scheduled_from" class="form-control datepicker" size="20" placeholder="<?php echo $this->lang->line('Scheduled from'); ?>" value="<?php echo $search_scheduled_from;?>">
				</div>

				<div class="form-group">
					<input id="scheduled_to" name="scheduled_to" class="form-control  datepicker" size="20" placeholder="<?php echo $this->lang->line('Scheduled to'); ?>" value="<?php echo $search_scheduled_to;?>">
				</div>                    

				<button class='btn btn-info'  onclick="doSearch(event)"><?php echo $this->lang->line("search");?></button> 
							
			</div>  

			</form> 

			</div>        
		</div>
	</div>   
</section>


<script>

	$(document.body).on('click','.repost',function(){    
    	var offer_id=$(this).attr('data-id');
    	$("#hidden_repost_id").val(offer_id);

    	$.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('ultrapost/offer_post_repost_form_data')?>",
		       data: {offer_id:offer_id},
		       success:function(response)
		       { 
		       		$("#repost_message").val(response);
		       		$("#repost_form_modal").modal();
		       }
			});
			

    	
    });
     

     $(document.body).on('click','#submit_repost',function(){    
    	var offer_id=$("#hidden_repost_id").val();
    	var message=$("#repost_message").val();
    	var repost_schedule_time=$("#repost_schedule_time").val();
    	var repost_time_zone=$("#repost_time_zone").val();

    	if(message=="" || repost_schedule_time=="" || repost_time_zone=="")
    	{
    		alert("<?php echo $this->lang->line('Post Content, schedule time & time zone are required'); ?>");
    		return;
    	}

    	$.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('ultrapost/offer_post_repost_action')?>",
		       data: {offer_id:offer_id,message:message,repost_schedule_time:repost_schedule_time,repost_time_zone:repost_time_zone},
		       success:function(response)
		       { 
		       		if(response=='1') $("#repost_success").attr("class","alert alert-success text-center").html("<i class='fa fa-check'></i> Offer has been submitted for reposting.");
		       		else if(response=='2')$("#repost_success").attr("class","alert alert-danger text-center").html("The offer is expired and can not be reposted.");
		       		else $("#repost_success").attr("class","alert alert-danger text-center").html("Something went wrong.");
		       		// location.reload();

		       		console.log(response);
		       }
			});
			

    	
    });
      

	$(document.body).on('click','.delete',function(){ 
		var id = $(this).attr('id');
		var ans = confirm("<?php echo $this->lang->line('Do you really want to delete this offer from our database?'); ?>");
		if(ans)
		{
			$.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('ultrapost/delete_post_offer')?>",
		       data: {id:id},
		       success:function(response)
		       { 
		       	if(response=='1')
		       	$j('#tt').datagrid('reload');
		       	else alert("<?php echo $this->lang->line('Something went wrong.'); ?>");
		       }
			});
			
		}
	});



	

	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{
			post_type   :     $j('#post_type').val(),              
			offer_name   :     $j('#offer_name').val(),              
			page_or_group_or_user_name  :     $j('#page_or_group_or_user_name').val(),              
			scheduled_from  		:     $j('#scheduled_from').val(),    
			scheduled_to    		:     $j('#scheduled_to').val(),         
			posting_status    		:     $j('#posting_status').val(),         
			is_searched		:      1
		});

	} 


	function format_name(text,row,index)
	{
		 var temp;
		 if (typeof(text)==='undefined') text="";
		 if (text.length > 20) 
		 {
		    temp = text.substr(0, 17) + '...';
		 }
		 else temp = text;

		 return "<span title='"+text+"'>"+temp+"</span>";
	}
	
	
</script>


<div class="modal fade" id="repost_form_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="fa fa-copy"></i> <?php echo $this->lang->line('Repost'); ?></h4>
			</div>
			<div class="modal-body">
				<input type="hidden" name="hidden_repost_id" id="hidden_repost_id">
				<div id="repost_form_modal_content">
				<div id="repost_success"></div>
					<div class="form-group">
						<label><?php echo $this->lang->line('Post Content'); ?> *</label>
						<textarea class="form-control" name="repost_message" id="repost_message" placeholder="Type your message here..."></textarea>
					</div>
					<div class="form-group schedule_block_item col-xs-12 col-md-6" style="padding:0 5px 0 0">
						<label><?php echo $this->lang->line('Schedule Time'); ?> *</label>
						<input placeholder="Schedule Time"  name="repost_schedule_time" id="repost_schedule_time" class="form-control datepicker" type="text"/>
					</div>

					<div class="form-group schedule_block_item col-xs-12 col-md-6" style="padding:0 0 0 5px">
						<label>Time Zone *</label>
						<?php
						$time_zone[''] = 'Please Select';
						echo form_dropdown('repost_time_zone',$time_zone,$this->config->item('time_zone'),' class="form-control" id="repost_time_zone"'); 
						?>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-warning btn-lg pull-left" name="submit_repost" id="submit_repost" type="button"><i class="fa fa-send"></i> <?php echo $this->lang->line('Submit Repost'); ?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$j('.datepicker').datetimepicker({
	    theme:'light',
	    format:'Y-m-d',
	    formatDate:'Y-m-d',
	    timepicker:false
    });


	// $('#repost_form_modal').on('hidden.bs.modal', function () { 
	// 	location.reload();
	// });

</script>