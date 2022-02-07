<?php $this->load->view("include/upload_js"); ?>
<div class="card">
		<div class="card-header" style="border-bottom: 0;padding-bottom:0 !important;">
			<h4><?php echo $this->lang->line("Campaign Info"); ?></h4>
		</div>
      	<div class="card-body">
			<div class="row">
				<?php if ($post_action == 'edit' || $post_action == 'clone'): ?>
					<input type="hidden" name="table_id" value="<?php echo $campaign_form_info['id']; ?>">
				<?php endif ?>
				<div class="col-12 col-md-6">
					<div class="form-group">
						<label><?php echo $this->lang->line('Campaign Name');?></label>
						<input type="input" class="form-control"  name="campaign_name" id="campaign_name" value="<?php if ($post_action == 'edit' || $post_action == 'clone') echo $campaign_form_info['campaign_name']; ?>">
					</div>
					
				<!--
					<div class="form-group">
					    <label><?php echo $this->lang->line("Privacy Type (Youtube)"); ?> </label>
						
						<?php 
							$privacy_type = array(
								'0' => $this->lang->line("Please Select"),
								'public' => $this->lang->line("Public"),
								'private' => $this->lang->line("Private"),
								'unlisted' => $this->lang->line("Unlisted"),
							);

							if ($post_action == 'add') {
								$default_value = '0';
							} else if ($post_action == 'edit' || $post_action == 'clone') {
								$default_value = $campaign_form_info['privacy_type'];
							}
							echo form_dropdown('privacy_type', $privacy_type, $default_value, 'class="form-control select2" id="privacy_type"');
						 ?>
					</div>
				-->

					<div class="form-group">
						<label><?php echo $this->lang->line('Video URL');?></label>
						<input type="input" class="form-control"  name="video_url" id="video_url" value="<?php if ($post_action == 'edit' || $post_action == 'clone') echo $campaign_form_info['video_url']; ?>">
					</div>

					<div class="form-group">
						<label><?php echo $this->lang->line('Upload video'); ?> 
							<a href="#" data-placement="top" data-toggle="popover"  data-content="<?php echo $this->lang->line("Maximum video upload limit for Twitter is 15MB and Twitter doesn't support video URL, You need to upload video for Twitter.").' '.$this->lang->line("Allowed files are .mp4,.mov,.avi,.wmv,.mpg,.flv"); ?>"><i class='fa fa-info-circle'></i> 
							</a>
						</label>
						<div id="upload_video" class="pointer"><?php echo $this->lang->line('Upload'); ?></div>
					</div>
				</div>

				<div class="col-12 col-md-6">
					<div class="form-group">
					    <!-- <label><?php echo $this->lang->line("Title (Facebook, Youtube)"); ?> </label> -->
					    <label><?php echo $this->lang->line("Title (Facebook)"); ?> </label>
					    <input class="form-control" name="title" id="campaign_title" type="input" value="<?php if ($post_action == 'edit' || $post_action == 'clone') echo $campaign_form_info['title']; ?>">
					</div>

					<div class="form-group">
						<label><?php echo $this->lang->line('Video Thumbnail URL (Facebook)');?></label>
						<input type="input" class="form-control"  name="video_url_thumbnail" id="video_url_thumbnail" value="<?php if ($post_action == 'edit' || $post_action == 'clone') echo $campaign_form_info['thumbnail_url']; ?>">
					</div>

					<div class="form-group">
						<label><?php echo $this->lang->line('Upload video thumbnail'); ?></label>
						<div id="upload_video_thumbnail" class="pointer"><?php echo $this->lang->line('Upload'); ?></div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<div class="form-group">
						<!-- <label><?php echo $this->lang->line('Message (Facebook, Twitter, Youtube)'); ?></label> -->
						<label><?php echo $this->lang->line('Message (Facebook, Twitter)'); ?></label>
						<textarea class="form-control" name="message" id="message" rows="11" placeholder="<?php echo $this->lang->line('Type your message here...');?>" style="height: 137px !important;"><?php if ($post_action == 'edit' || $post_action == 'clone') echo $campaign_form_info['message']; ?></textarea>
					</div>
				</div>
			</div>

			<div class="row">
				
				<div class="col-12 col-md-6">
					<div class="form-group">
						<label><?php echo $this->lang->line("Posting Time") ?>
							<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Posting Time") ?>" data-content="<?php echo $this->lang->line("If you schedule a campaign, system will automatically process this campaign at mentioned time and time zone. Schduled campaign may take upto 1 hour longer than your schedule time depending on server's processing.") ?>"><i class='fa fa-info-circle'></i> </a>
						</label><br>
					  	<label class="custom-switch mt-2">
							<input type="checkbox" name="schedule_type" value="now" id="schedule_type" class="custom-switch-input"  <?php if ($post_action == 'add' || (($post_action == 'edit' || $post_action == 'clone') && $campaign_form_info['schedule_type'] == 'now')) echo "checked"; ?>>
							<span class="custom-switch-indicator"></span>
							<span class="custom-switch-description"><?php echo $this->lang->line('Post Now');?></span>
					  	</label>
					</div>
				</div>

				<div class="col-12">
					<div class="row">
						<div class="schedule_block_item col-12 col-md-6" style="display: none;">
							<div class="form-group">
								<label><?php echo $this->lang->line('Schedule time'); ?></label>
								<input placeholder="Time"  name="schedule_time" id="schedule_time" class="form-control datepicker_x" type="text" value="<?php if ($post_action == 'edit' || $post_action == 'clone') echo $campaign_form_info['schedule_time']; ?>"/>
							</div>
						</div>

						<div class="schedule_block_item col-12 col-md-6" style="display: none;">
							<div class="form-group">
								<label><?php echo $this->lang->line('Time zone'); ?></label>
								<?php
								$time_zone[''] =$this->lang->line('Please Select');

								if ($post_action == 'edit' || $post_action == 'clone') {
									$default = $campaign_form_info['schedule_timezone'];
								} else {
									$default = $this->config->item('time_zone'); 
								}

								echo form_dropdown('time_zone',$time_zone, $default,' class="form-control" id="time_zone" required style="width:100%;"');
								?>
							</div>
						</div>
							
						<?php if ($post_action == 'add' 
									|| ((isset($campaign_form_info['parent_campaign_id']) && $campaign_form_info['parent_campaign_id'] == "0") 
										|| ($campaign_form_info['parent_campaign_id'] == null && $post_action == 'clone'))): ?>
							
							<div class="schedule_block_item col-12 col-md-6" style="display: none;">
								<div class="input-group">
								  	<label class="input-group-addon"><?php echo $this->lang->line('repost this post'); ?></label>
								  	<div class="input-group">
			                          	<input type="number" class="form-control" name="repeat_times" id="repeat_times" aria-describedby="basic-addon2" value="<?php if ($post_action == 'edit' || $post_action == 'clone') echo $campaign_form_info['repeat_times']; ?>">
			                          	<div class="input-group-prepend">
				                            <div class="input-group-text"><?php echo $this->lang->line('Times'); ?></div>
			                          	</div>
		                        	</div>
								</div>
							</div>

							<div class="schedule_block_item col-12 col-md-6" style="display: none;">
								<div class="schedule_block_item">
									<div class="form-group">
										<label><?php echo $this->lang->line('time interval'); ?></label>
										<?php
											$time_interval[''] = $this->lang->line('Please Select Periodic Time Schedule');

											$current_value = "";
											if ($post_action == 'edit' || $post_action == 'clone') {
												$current_value = $campaign_form_info['time_interval'] != 0 ? $campaign_form_info['time_interval'] : "";
											}

											echo form_dropdown('time_interval',$time_interval,$current_value,' class="form-control select2" id="time_interval" required style="width:100%;"');
										?>
									</div>
								</div>
							</div>

						<?php endif ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix"></div>

			
        </div>
</div>          
