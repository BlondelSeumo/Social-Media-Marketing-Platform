<link rel="stylesheet" href="<?php echo base_url('assets/css/system/select2_100.css');?>">
<div class="modal fade" id="auto_reply_message_modal_template" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			
			<div class="modal-header">
			  <h5 class="modal-title padding_10_20_10_20px" ><?php echo $this->lang->line("Please give the following information for post auto comment") ?></h5>
			  <button type="button" class="close" id='modal_close_template' aria-label="Close">
				<span aria-hidden="true">×</span>
			  </button>
			</div>

			<form action="#" id="auto_reply_info_form_template" method="post">
				<input type="hidden" name="auto_reply_page_id_template" id="auto_reply_page_id_template" value="">
				<input type="hidden" name="auto_reply_post_id_template" id="auto_reply_post_id_template" value="">
				<input type="hidden" name="manual_enable_template" id="manual_enable_template" value="">
				<input type="hidden" name="autocomment_template_type" id="autocomment_template_type" value="">
				<input type="hidden" name="autocomment_permalink_url" id="autocomment_permalink_url" value="">
			<div class="modal-body" id="auto_reply_message_modal_template_body_template">  
				<!-- comment hide and delete section -->
				
				<div class="row padding_20px">

					<div class="col-12 margin_top_15">
						<div class="form-group">
							<label>
								<i class="fas fa-monument"></i> <?php echo $this->lang->line('Auto comment campaign name'); ?> <span class="red">*</span> 
								
							</label>
							<br>
							<input class="form-control" type="text" name="auto_campaign_name_template" id="auto_campaign_name_template" placeholder="<?php echo $this->lang->line('Write your campaign name here'); ?>">
						</div>
					</div>
					<div class="col-12">
						<div class="form-group p-0">
							<label><?php echo $this->lang->line('') ?></label>
							<label>
								<i class="fa fa-th-large"></i> <?php echo $this->lang->line('Auto Comment Template'); ?> <span class="red">*</span> 							
							</label>
							<br>
							<select  class="form-control select2 w-100" id="auto_comment_template_id" name="auto_comment_template_id">
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
						      <input type="radio" name="schedule_type" value="onetime" id="schedule_now" class="custom-switch-input" checked>
						      <span class="custom-switch-indicator"></span>
						      <span class="custom-switch-description"><?php echo $this->lang->line('One Time'); ?></span>
						    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						  
						    <label class="custom-switch">
						      <input type="radio" name="schedule_type" value="periodic"  id="schedule_later" class="custom-switch-input">
						      <span class="custom-switch-indicator"></span>
						      <span class="custom-switch-description"><?php echo $this->lang->line('Periodic'); ?>
						    </label>
						</div>
						
						<div class="row">							
							<div class="form-group schedule_block_item col-12 col-md-6">
								<label><?php echo $this->lang->line('Schedule time'); ?></label>
								<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="schedule_time" id="schedule_time" class="form-control datepicker_x" type="text"/>
							</div>

							<div class="form-group schedule_block_item col-12 col-md-6">
								<label><?php echo $this->lang->line('Time zone'); ?></label>
								<?php
								$time_zone[''] =$this->lang->line('Please Select');
								echo form_dropdown('time_zone',$time_zone,set_value('time_zone'),' class="form-control select2 w-100" id="time_zone" required');
								?>
							</div>
						</div>

						<div class='schedule_block_item_new instagram_padded_bordered_background_schedule_block'>
							<div class="clearfix"></div>

							<div class="row">								
								<div class="form-group schedule_block_item_new col-12 col-md-6">

									<label><?php echo $this->lang->line('Periodic Schedule time'); ?>
										<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Choose how frequently you want to comment'); ?>" data-original-title="<?php echo $this->lang->line('Periodic Schedule time'); ?>"><i class="fa fa-info-circle"></i> </a>
									</label>
									<?php
									$periodic_time[''] =$this->lang->line('Please Select Periodic Time Schedule');
									echo form_dropdown('periodic_time',$periodic_time,set_value('periodic_time'),' class="form-control select2 w-100" id="periodic_time" required');
									?>
								</div>
								<div class="form-group schedule_block_item_new col-12 col-md-6">
									<label><?php echo $this->lang->line('Time zone'); ?></label>
									<?php
									$time_zone[''] =$this->lang->line('Please Select');
									echo form_dropdown('periodic_time_zone',$time_zone,set_value('periodic_time_zone'),' class="form-control select2 w-100" id="periodic_time_zone" required');
									?>
								</div>
							</div>
							
							<div class="row">								
								<div class="form-group schedule_block_item_new col-12 col-md-6">
									<label><?php echo $this->lang->line('Campaign Start time'); ?></label>
									<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="campaign_start_time" id="campaign_start_time" class="form-control datepicker_x" type="text"/>
								</div>						
								<div class="form-group schedule_block_item_new col-12 col-md-6">
									<label><?php echo $this->lang->line('Campaign End time'); ?></label>
									<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="campaign_end_time" id="campaign_end_time" class="form-control datepicker_x" type="text"/>
								</div>
							</div>

							<div class="row">								
								<div class="form-group schedule_block_item_new col-12 col-md-6">
									 <label>
										<?php echo $this->lang->line('Comment Between Time'); ?>
										<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line("Set the allowed time of the comment. As example you want to auto comment by page from 10 AM to 8 PM. You don't want to comment other time. So set it 10:00 & 20:00"); ?>" data-original-title="<?php echo $this->lang->line('Comment Between Time'); ?>"><i class="fa fa-info-circle"></i> 
										</a>
										
									 </label> 
									<input placeholder="<?php echo $this->lang->line('Time'); ?>" value="00:00"  name="comment_start_time" id="comment_start_time" class="form-control datetimepicker2" type="text"/>
								</div>
						

								<div class="form-group schedule_block_item_new col-12 col-md-6">								
									<label class="instagram_relative_top_right_22px"><?php echo $this->lang->line('to'); ?></label> 
									<input placeholder="<?php echo $this->lang->line('Time'); ?>" value="23:59"  name="comment_end_time" id="comment_end_time" class="form-control datetimepicker2" type="text"/>
								</div>
							</div>
							  
							<div class="row">								
								<div class="form-group schedule_block_item_new col-12 col-md-12">

									<label>
										<i class="fas fa-comment"></i> <?php echo $this->lang->line('Auto Comment Type'); ?> <span class="red">*</span> 
										<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Random type will pick a comment from template randomly each time and serial type will pick the comment serially from selected template first to last.'); ?>" data-original-title="<?php echo $this->lang->line('Auto Comment Type'); ?>"><i class="fa fa-info-circle"></i> </a>
									</label>
									<br>

									<label class="custom-switch">
									  <input type="radio" name="auto_comment_type" value="random" id="random" class="custom-switch-input" checked>
									  <span class="custom-switch-indicator"></span>
									  <span class="custom-switch-description"><?php echo $this->lang->line('Random'); ?></span>
									</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									
									<label class="custom-switch">
									  <input type="radio" name="auto_comment_type" value="serially"  id="serially" class="custom-switch-input">
									  <span class="custom-switch-indicator"></span>
									  <span class="custom-switch-description"><?php echo $this->lang->line('Serially'); ?>
									</label>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>	
					</div>



					<br/><br/>
				
	
				</div>  
				<!-- end of comment hide and delete section -->

				<div class="row padding_10_20_10_20px">
					<div class="smallspace clearfix"></div>
				</div>
				
				<div class="col-12 text-center" id="response_status_template"></div>
			</div>
			</form>
			<div class="clearfix"></div>

			<div class="modal-footer padding_0_45px">
			  <div class="row">
			    <div class="col-6">
			      <button class="btn btn-lg btn-primary float-left" id="save_button_template"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
			    </div>  
			    <div class="col-6">
			      <button class="btn btn-lg btn-secondary float-right cancel_button"><i class='fas fa-times'></i> <?php echo $this->lang->line("cancel") ?></button>
			    </div>
			  </div>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="edit_auto_reply_message_modal_template" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title padding_10_20_10_20px" ><?php echo $this->lang->line("Please give the following information for post auto comment") ?></h5>
			  <button type="button" class="close" id='edit_modal_close_template' aria-label="Close">
				<span aria-hidden="true">×</span>
			  </button>
			</div>

			<form action="#" id="edit_auto_reply_info_form_template" method="post">
				<input type="hidden" name="edit_auto_reply_page_id_template" id="edit_auto_reply_page_id_template" value="">
				<input type="hidden" name="edit_auto_reply_post_id_template" id="edit_auto_reply_post_id_template" value="">
			<div class="modal-body" id="edit_auto_reply_message_modal_template_body">   
			
			<div class="text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center font_size_40px"></i></div>

				<div class="row padding_20px">

					<div class="col-12 margin_top_15">
						<div class="form-group">
							<label>
								<i class="fas fa-monument"></i> <?php echo $this->lang->line('Auto comment campaign name'); ?> <span class="red">*</span> 
							</label>
							<br>
							<input class="form-control"type="text" name="edit_campaign_name_template" id="edit_campaign_name_template" placeholder="<?php echo $this->lang->line('Write your campaign name here'); ?>">
						</div>
					</div>

					<div class="col-12">						
						<div class="form-group p-0">
							<label><?php echo $this->lang->line('') ?></label>
							<label>
								<i class="fa fa-th-large"></i> <?php echo $this->lang->line('Auto Comment Template'); ?> <span class="red">*</span> 
							</label>
							<br>
							<select  class="form-control" id="edit_auto_comment_template_id" name="edit_auto_comment_template_id">
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
							  <input type="radio" name="edit_schedule_type" value="onetime" id="edit_schedule_type_o" class="custom-switch-input" checked>
							  <span class="custom-switch-indicator"></span>
							  <span class="custom-switch-description"><?php echo $this->lang->line('One Time'); ?></span>
							</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							
							<label class="custom-switch">
							  <input type="radio" name="edit_schedule_type" value="periodic"  id="edit_schedule_type_p" class="custom-switch-input">
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
								echo form_dropdown('edit_time_zone_o',$time_zone,set_value('time_zone'),' class="form-control" id="edit_time_zone_o" required');
								?>
							</div>
						</div>

						<div class='schedule_block_item_new_p instagram_padded_bordered_background_schedule_block2'>
							<div class="clearfix"></div>
							<div class="row">								
								<div class="form-group schedule_block_item_new_p col-12 col-md-6">

									<label><?php echo $this->lang->line('Periodic Schedule time'); ?>
										<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Choose how frequently you want to comment'); ?>" data-original-title="<?php echo $this->lang->line('Periodic Schedule time'); ?>"><i class="fa fa-info-circle"></i> </a>
									</label>
									<?php
									$periodic_time[''] =$this->lang->line('Please Select Periodic Time Schedule');
									echo form_dropdown('edit_periodic_time',$periodic_time,set_value('edit_periodic_time'),' class="form-control" id="edit_periodic_time" required');
									?>
								</div>
								<div class="form-group schedule_block_item_new_p col-12 col-md-6">
									<label><?php echo $this->lang->line('Time zone'); ?></label>
									<?php
									$time_zone[''] =$this->lang->line('Please Select');
									echo form_dropdown('edit_periodic_time_zone',$time_zone,set_value('edit_periodic_time_zone'),' class="form-control" id="edit_periodic_time_zone" required');
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
									<label><?php echo $this->lang->line('Comment Between Time'); ?>
										<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line("Set the allowed time of the comment. As example you want to auto comment by page from 10 AM to 8 PM. You don't want to comment other time. So set it 10:00 & 20:00"); ?>" data-original-title="<?php echo $this->lang->line('Comment Between Time'); ?>"><i class="fa fa-info-circle"></i> 
										</a>
									</label>
									<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_comment_start_time" id="edit_comment_start_time" class="form-control datetimepicker2" type="text"/>
								</div>
								<div class="form-group schedule_block_item_new_p col-12 col-md-6">
									<label class="instagram_relative_top_right_22px"><?php echo $this->lang->line('to'); ?></label> 
									<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_comment_end_time" id="edit_comment_end_time" class="form-control datetimepicker2" type="text"/>
								</div>
							</div>
							
							<div class="row">								
								<div class="form-group schedule_block_item_new_p col-12 col-md-12">
									<label>
										<i class="fas fa-comment"></i> <?php echo $this->lang->line('Auto Comment Type'); ?> <span class="red">*</span> 
										<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Random type will pick a comment from template randomly each time and serial type will pick the comment serially from selected template first to last.'); ?>" data-original-title="<?php echo $this->lang->line('Auto Comment Type'); ?>"><i class="fa fa-info-circle"></i> </a>
									</label>
									<br>
									
									<label class="custom-switch">
									  <input type="radio" name="edit_auto_comment_type" value="random" id="edit_random" class="custom-switch-input" checked>
									  <span class="custom-switch-indicator"></span>
									  <span class="custom-switch-description"><?php echo $this->lang->line('Random'); ?></span>
									</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									
									<label class="custom-switch">
									  <input type="radio" name="edit_auto_comment_type" value="serially"  id="edit_serially" class="custom-switch-input">
									  <span class="custom-switch-indicator"></span>
									  <span class="custom-switch-description"><?php echo $this->lang->line('Serially'); ?>
									</label>

									
								</div>	
							</div>
							<div class="clearfix"></div>
						</div>

					</div>

				<br/>
				
				</div>  

				<div class="row padding_10_20_10_20px">
					<div class="smallspace clearfix"></div>
				</div>
				<div class="col-12 text-center" id="edit_response_status_template"></div>
			</div>
			</form>
			<div class="clearfix"></div>

			<div class="modal-footer padding_0_45px">
			  <div class="row">
			    <div class="col-6">
			      <button class="btn btn-lg btn-primary float-left" id="edit_save_button_template"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
			    </div>  
			    <div class="col-6">
			      <button class="btn btn-lg btn-secondary float-right cancel_button"><i class='fas fa-times'></i> <?php echo $this->lang->line("cancel") ?></button>
			    </div>
			  </div>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="manual_reply_by_post_template" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h5 class="modal-title"><?php echo $this->lang->line("please provide a post id of page") ?> (<span id="manual_page_name"></span>)</h5>
			</div>
			<div class="modal-body ">
				<div class="row">
					<div class="col-12" id="waiting_div"></div>
					<div class="col-12 col-md-8 col-md-offset-2">
						<form>
							<div class="form-group">
							  <label for="manual_post_id_template"><?php echo $this->lang->line("post id") ?> :</label>
							  <input type="text" class="form-control" id="manual_post_id_template" placeholder="<?php echo $this->lang->line("please give a post id") ?>" value="">
							  <input type="hidden" id="manual_table_id_template">
							</div>
							<div class="text-center" id="manual_reply_error_template"></div>
						 
							<div class="form-group text-center">
							  <button type="button" class="btn btn-primary enable_auto_commnet_template" id="manual_auto_reply_template"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("Enable Auto Comment") ?></button>
							</div>
						 </form>
						 <div class="form-group text-center">
							<button type="button" class="btn btn-outline-warning" id="check_post_id_template"><i class="fa fa-check"></i> <?php echo $this->lang->line("check existance") ?></button>
						 </div>
						
					</div>                    
				</div>               
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="manual_edit_reply_by_post_template" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h5 class="modal-title"><?php echo $this->lang->line("please provide a post id of page") ?> (<span id="manual_edit_page_name_template"></span>)</h5>
			</div>
			<div class="modal-body ">
				<div class="row">
					<div class="col-12" id="waiting_div"></div>
					<div class="col-12 col-md-8 col-md-offset-2">
						<form>
							<div class="form-group">
							  <label for="manual_post_id_template"><?php echo $this->lang->line("post id") ?> :</label>
							  <input type="text" class="form-control" id="manual_edit_post_id_template" placeholder="<?php echo $this->lang->line("please give a post id") ?>" value="">
							  <input type="hidden" id="manual_edit_table_id_template">
							</div>
							<div class="text-center" id="manual_edit_error_template"></div>                           
						</form>
						<div class="form-group text-center margin_top_15">
						   <button type="button" class="btn btn-outline-warning edit_reply_info_template" id="manual_edit_auto_reply_template"><i class="fa fa-edit"></i> <?php echo $this->lang->line("Edit Auto Comment") ?></button>
						</div>
						
					</div>                    
				</div>               
			</div>
		</div>
	</div>
</div>