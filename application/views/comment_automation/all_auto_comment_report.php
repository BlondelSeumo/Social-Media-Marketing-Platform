<?php 
	$this->load->view("include/upload_js"); 
	if(ultraresponse_addon_module_exist())	$commnet_hide_delete_addon = 1;
	else $commnet_hide_delete_addon = 0;

	if(addon_exist(201,"comment_reply_enhancers")) $comment_tag_machine_addon = 1;
	else $comment_tag_machine_addon = 0;		
	$report_page_name=urldecode($this->uri->segment(3));

	$image_upload_limit = 1; 
	if($this->config->item('autoreply_image_upload_limit') != '')
	$image_upload_limit = $this->config->item('autoreply_image_upload_limit'); 

	$video_upload_limit = 3; 
	if($this->config->item('autoreply_video_upload_limit') != '')
	$video_upload_limit = $this->config->item('autoreply_video_upload_limit');
	if(!isset($is_instagram)) $is_instagram = '0';
?>

<link rel="stylesheet" href="<?php echo base_url('assets/css/system/select2_100.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/instagram/all_auto_comment_report.css');?>">

<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-comments"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
      <div class="breadcrumb-item">
      	<a href="<?php echo base_url("comment_automation/comment_section_report"); ?>">
      		<?php echo $this->lang->line("Report"); ?>
      	</a>
      </div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
          	<div class="input-group mb-3" id="searchbox">
          	  	<div class="input-group-prepend">
          	      	<select class="select2 form-control" id="page_id">
          	        	<option value=""><?php echo $is_instagram=='1' ? $this->lang->line("Account Name") :$this->lang->line("Page Name"); ?></option>
		          	        <?php foreach ($page_info as $value): if($is_instagram=='1' && $value['has_instagram']=='0') continue;?>
		          	        	<option value="<?php echo $value['id']; ?>" <?php if($value['id'] == $page_id || $value['id']==$this->session->userdata('selected_global_page_table_id')) echo 'selected'; ?> ><?php echo $is_instagram=='1' ? $value['insta_username'] : $value['page_name']; ?></option>
		          	        <?php endforeach ?>
      	      		</select>
          	    </div>
          	    <input type="text" class="form-control" value="<?php if($post_id != 0) echo $post_id; ?>" id="campaign_name" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
          	    <input type="hidden" class="form-control" value="<?php echo $is_instagram;?>" id="is_instagram" name="is_instagram">
          	  	<div class="input-group-append">
          	    	<button class="btn btn-primary" id="search_submit" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
      	 	 	</div>
          	</div>
            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>      
                    <th><?php echo $this->lang->line("Page ID"); ?></th>
                    <th><?php echo $this->lang->line("Avatar")?></th>
                    <th><?php echo $this->lang->line("Name")?></th>
                    <th class="min_width_70px"><?php echo $is_instagram=='1' ? $this->lang->line("Account") : $this->lang->line("Page");?></th>
                    <th><?php echo $this->lang->line("Post ID")?></th>
                    <th><?php echo $this->lang->line("Actions")?></th>
                    <th><?php echo $this->lang->line("Reply Sent")?></th>
                    <th><?php echo $this->lang->line("status")?></th>
                    <th><?php echo $this->lang->line("Last Reply Time")?></th>
                    <th><?php echo $this->lang->line("Error Message")?></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section> 


<script src="<?php echo base_url('assets/js/system/instagram/all_auto_comment_report.js');?>"></script>

<div class="modal fade" id="view_report_modal" data-backdrop="static" data-keyboard="false">
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
      			  		<input type="text" id="searching" name="searching" class="form-control width_200px" placeholder="<?php echo $this->lang->line("Search..."); ?>">                                          
          			</div>
                    <div class="col-12">
                      <div class="table-responsive2">
                        <input type="hidden" id="put_row_id">
                        <table class="table table-bordered" id="mytable1">
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


<div class="modal fade" id="edit_auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center pt-2 pb-2 pl-4 pr-4"><?php echo $this->lang->line("Please give the following information for post auto comment") ?></h5>
                <button type="button" id='edit_modal_close' class="close">&times;</button>
            </div>
            <form action="#" id="edit_auto_reply_info_form" method="post">
	            <input type="hidden" name="edit_auto_reply_page_id_template" id="edit_auto_reply_page_id_template" value="">
	            <input type="hidden" name="edit_auto_reply_post_id_template" id="edit_auto_reply_post_id_template" value="">
	            <div class="modal-body" id="edit_auto_reply_message_modal_body">   
				
				<div class="text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center font_size_40px"></i></div>

	        	<div class="row pt-2 pb-2 pr-4 pl-4">

	        		<div class="col-12 mt-3">
	        			<div class="form-group">
	        				<label>
	        					<i class="fas fa-monument"></i> <?php echo $this->lang->line('Auto comment campaign name'); ?> <span class="red">*</span> 
	        				</label>
	        				<br>
	        				<input class="form-control"type="text" name="edit_campaign_name_template" id="edit_campaign_name_template" placeholder="Write your auto reply campaign name here">
	        			</div>
	        		</div>

					<div class="col-12">
	                    <div class="form-group col-12 col-md-12 p-0">
							<label>
								<i class="fa fa-th-large"></i> <?php echo $this->lang->line('Auto Comment Template'); ?> <span class="red">*</span> 
							</label>
							<br>
							<select  class="form-control select2 w-100" id="edit_auto_comment_template_id" name="edit_auto_comment_template_id">
							<?php
								echo "<option value='0'>{$this->lang->line('Please select a template')}</option>";
								foreach($auto_comment_template as $key=>$val)
								{
									$id=$val['id'];
									$group_name=$val['template_name'];
									echo "<option value='{$id}'>{$group_name}</option>";
								}
							 ?>
							</select>
					    </div>
					</div>
					<br>

	               <br>
					<div class="col-12">
						<div class="form-group">
							<label>
								<i class="fas fa-clock"></i> <?php echo $this->lang->line('Schedule Type'); ?> <span class="red">*</span> 
								<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Onetime campaign will comment only the first comment of the selected template and periodic campaign will auto comment multiple time periodically as per your settings.'); ?>" data-original-title="<?php echo $this->lang->line('Schedule Type'); ?>"><i class="fa fa-info-circle"></i> </a>
							</label>
							<br>
							<label class="custom-switch">
							  <input type="radio" name="edit_schedule_type" value="onetime" id="edit_schedule_type_o" class="custom-switch-input">
							  <span class="custom-switch-indicator"></span>
							  <span class="custom-switch-description"><?php echo $this->lang->line('One Time'); ?></span>
							</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label class="custom-switch">
							  <input type="radio" name="edit_schedule_type" value="periodic" id="edit_schedule_type_p" class="custom-switch-input">
							  <span class="custom-switch-indicator"></span>
							  <span class="custom-switch-description"><?php echo $this->lang->line('Periodic'); ?>
							</label>
						</div>
						
						<div class="row">							
							<div class="form-group schedule_block_item_o col-12 col-md-6">
								<label><?php echo $this->lang->line('Schedule time'); ?></label>
								<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_schedule_time_o" id="edit_schedule_time_o" class="form-control datepicker_x" type="text"/>
							</div>

							<div class="form-group schedule_block_item_o col-12 col-md-6">
								<label><?php echo $this->lang->line('Time zone'); ?></label>
								<?php
								$time_zone[''] =$this->lang->line('Please Select');
								echo form_dropdown('edit_time_zone_o',$time_zone,set_value('time_zone'),' class="form-control select2 w-100" id="edit_time_zone_o" required');
								?>
							</div>
						</div>

						<div class='schedule_block_item_new_p inatagram_padded_bordered_background_schedule_block'>
							<div class="clearfix"></div>
							<div class="row">
								<div class="form-group schedule_block_item_new_p col-12 col-md-6">
									<label><?php echo $this->lang->line('Periodic Schedule time'); ?>
										<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Choose how frequently you want to comment'); ?>" data-original-title="<?php echo $this->lang->line('Periodic Schedule time'); ?>"><i class="fa fa-info-circle"></i> </a>
									</label>
									<?php
									$periodic_time[''] =$this->lang->line('Please Select Periodic Time Schedule');
									echo form_dropdown('edit_periodic_time',$periodic_time,set_value('edit_periodic_time'),' class="form-control select2 w-100" id="edit_periodic_time" required');
									?>
								</div>

								<div class="form-group schedule_block_item_new_p col-12 col-md-6">
									<label><?php echo $this->lang->line('Time zone'); ?></label>
									<?php
									$time_zone[''] =$this->lang->line('Please Select');
									echo form_dropdown('edit_periodic_time_zone',$time_zone,set_value('edit_periodic_time_zone'),' class="form-control select2 w-100" id="edit_periodic_time_zone" required');
									?>
								</div>
							</div>
						
							<div class="row">
								<div class="form-group schedule_block_item_new_p col-12 col-md-6">
									<label><?php echo $this->lang->line('Campaign Start time'); ?></label>
									<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_campaign_start_time" id="edit_campaign_start_time" class="form-control datepicker_x" type="text"/>
								</div>						
								<div class="form-group schedule_block_item_new_p col-12 col-md-6">
									<label><?php echo $this->lang->line('Campaign End time'); ?></label>
									<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_campaign_end_time" id="edit_campaign_end_time" class="form-control datepicker_x" type="text"/>
								</div>
							</div>
							
							<div class="row">
								<div class="form-group schedule_block_item_new_p col-12 col-md-6">
									<label>
										<?php echo $this->lang->line('Comment Between Time'); ?>
										<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line("Set the allowed time of the comment. As example you want to auto comment by page from 10 AM to 8 PM. You don't want to comment other time. So set it 10:00 & 20:00"); ?>" data-original-title="<?php echo $this->lang->line('Comment Between Time'); ?>"><i class="fa fa-info-circle"></i> 
										</a>												
									</label> 
									<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_comment_start_time" id="edit_comment_start_time" class="form-control datetimepicker2" type="text"/>
								</div>
								<div class="form-group schedule_block_item_new_p col-12 col-md-6">
									<label class="inatagram_relative_top_right_22px"><?php echo $this->lang->line('to'); ?></label> 
									<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_comment_end_time" id="edit_comment_end_time" class="form-control datetimepicker2" type="text"/>
								</div>
							</div>

							<div class="form-group schedule_block_item_new_p col-12 col-md-12">

								<label>
									<i class="fas fa-comment"></i> <?php echo $this->lang->line('Auto Comment Type'); ?> <span class="red">*</span> 
									<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Random type will pick a comment from template randomly each time and serial type will pick the comment serially from selected template first to last.'); ?>" data-original-title="<?php echo $this->lang->line('Auto Comment Type'); ?>"><i class="fa fa-info-circle"></i> </a>
								</label>
								<br>
								<label class="custom-switch">
								  <input type="radio" name="edit_auto_comment_type" value="random" id="edit_random" class="custom-switch-input">
								  <span class="custom-switch-indicator"></span>
								  <span class="custom-switch-description"><?php echo $this->lang->line('Random'); ?></span>
								</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<label class="custom-switch">
								  <input type="radio" name="edit_auto_comment_type" value="serially" id="edit_serially" class="custom-switch-input">
								  <span class="custom-switch-indicator"></span>
								  <span class="custom-switch-description"><?php echo $this->lang->line('Serially'); ?>
								</label>
							</div>
							<div class="clearfix"></div>
						</div>	
					</div>
				<br/>
				</div>  
				<div class="row pt-2 pb-2 pl-4 pr-4">
						<!-- added by mostofa on 26-04-2017 -->
					<div class="smallspace clearfix"></div>
				</div>

				<div class="col-12 text-center" id="edit_response_status"></div>
	            </div>
            </form>
            <div class="clearfix"></div>

            <div class="modal-footer padding_0_45px">
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