
<div class="col-12 col-sm-8 col-md-3 col-lg-4 colrig">
	<div class="card main_card bg-body">
		<div class="card-header p-0 margin_bottom_22px" style="min-height: 0 !important;">
			<h4 class="full_width">				
				<label class="custom-switch float-right">
				  <input type="checkbox" name="selected_social_post_media_type" id="selected_social_post_media_type" value="1" class="custom-switch-input">
				  <span class="custom-switch-indicator"></span>
				  <span class="custom-switch-description"><?php echo $this->lang->line('Instagram')?></span>
				</label>
			</h4>
		</div>
      	<div class="card-body px-3 py-4 mt-0 mb-2 border preview_container bg-white rounded" id="preview_for_facebook">
          	<?php $profile_picture=(isset($account_list[0]['page_profile']) && $account_list[0]['page_profile']!="")?$account_list[0]['page_profile']:base_url('assets/img/avatar/avatar-1.png'); ?>
			<ul class="list-unstyled list-unstyled-border mb-0 pr-2">
				<li class="media">
				  <img class="mr-3 rounded-circle mt-1" width="45" src="<?php echo $profile_picture;?>" alt="avatar">
				  <div class="media-body">
				    <h6 class="media-title mt-1 font-weight-bold"><a href="#"><?php echo isset($fb_user_info[0]['name'])?$fb_user_info[0]['name']:"Username";?></a></h6>
				    <div class="text-small text-muted">
				    	<!-- Published by <?php echo isset($app_info[0]['app_name']) ? $app_info[0]['app_name'] : $this->config->item("product_short_name");?>  -->
				    	1m <div class="bullet text-muted"></div> <i class="fas fa-globe-asia"></i>
				    </div>
				  </div>
				</li>
			</ul>

          	<div class="preview_message px-2 mt-3 mb-2">Text goes here...</div>

          	<div class="preview_video_block d_none">
      			<video controls="" width="100%" height="290" class="border"><source  src=""></source></video>
      			<br/>
          		<div class="video_preview_og_info_desc inline-block">
          		</div>
          	</div>			          	

          	<div class="preview_img_block">
          		<div class="preLoader text-center" style="display: none;"></div>
          		<img src="<?php echo base_url('assets/img/example-image.jpg');?>" class="img-fluid">
          		<div class="preview_og_info">
          			<div class="preview_og_info_title inline-block">Title goes here...</div>
          			<div class="preview_og_info_desc inline-block">Description goes here...
          			</div>
          			<div class="preview_og_info_link inline-block">
          			</div>
          		</div>
          	</div>

          	<div class="preview_only_img_block">
          		<img src="<?php echo base_url('assets/img/example-image.jpg');?>" class="img-fluid only_preview_img">
          	</div>


          	<img src="<?php echo base_url('assets/img/post_button_fb.png');?>" class="preview_img border-top mt-2 pt-2">

      	</div>

      	<div class="card-body px-3 py-4 mt-0 mb-2 border preview_container bg-white rounded" id="preview_for_instagram">
      		<?php $profile_picture=(isset($account_list[0]['page_profile']) && $account_list[0]['page_profile']!="")?$account_list[0]['page_profile']:base_url('assets/img/avatar/avatar-1.png'); ?>
	      	<ul class="list-unstyled list-unstyled-border mb-0 pr-2">
	      		<li class="media">
	      		  <img class="mr-3 rounded-circle mt-1" width="45" src="<?php echo $profile_picture;?>" alt="avatar">
	      		  <div class="media-body">
	      		    <h6 class="media-title mt-3 font-weight-bold"><a href="#"><?php echo isset($account_list[0]['insta_username'])?$account_list[0]['insta_username']:"Username";?></a></h6>
	      		    <!-- <div class="text-small text-muted">
	      		    	<?php echo isset($app_info[0]['app_name']) ? $app_info[0]['app_name'] : $this->config->item("product_short_name");?> 
	      		    	<div class="bullet"></div> 
	      		    	<span class="text-primary">Now</span>
	      		    </div> -->
	      		  </div>
	      		</li>
	      	</ul>          	

      		<div class="preview_video_block d_none mt-3">
      			<video controls="" width="100%" height="290" class="border"><source  src=""></source></video>
      			<br/>
      			<div class="video_preview_og_info_desc inline-block">
      			</div>
      			<img src="<?php echo base_url('assets/img/post_button.png');?>" class="preview_img mt-2 pt-2">          		

      			<div class="preview_message mt-1 px-2 mb-2"></div>
      		</div>
      		<div class="preview_only_img_block mt-3">
      			<img src="<?php echo base_url('assets/img/example-image.jpg');?>" class="img-fluid only_preview_img">
      			<img src="<?php echo base_url('assets/img/post_button.png');?>" class="preview_img mt-2 pt-2">          		

      			<div class="preview_message mt-1 px-2 mb-2"></div>
      		</div>

      		<div id="not_supported" class="alert alert-light mt-4"><?php echo $this->lang->line("Media type not supported for Instagram"); ?></div>

      	</div>     
    </div>
</div>


        

