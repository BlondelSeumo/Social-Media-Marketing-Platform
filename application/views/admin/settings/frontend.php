<style>
	.bg-white{
		background-color: #4D4D4D !important;
	}
	.bg-dark{
		background-color: #000000 !important;
	}
	.bg-blue{
		background-color: #1193D4 !important;
	}
	.bg-green{
		background-color: #00A65A !important;
	}
	.bg-purple{
		background-color: #545096 !important;
	}
	.bg-red{
		background-color: #E55053 !important;
	}
	.bg-yellow{
		background-color: #F39C12 !important;
	}
</style>
<section class="section">
	<div class="section-header">
		<h1><i class="fas fa-toolbox"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
			<div class="breadcrumb-item active"><a href="<?php echo base_url('admin/settings'); ?>"><?php echo $this->lang->line("Settings"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<?php $this->load->view('admin/theme/message'); ?>

	<?php $save_button = '<div class="card-footer bg-whitesmoke">
	                      <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> '.$this->lang->line("Save").'</button>
	                      <button class="btn btn-secondary btn-lg float-right" onclick=\'goBack("admin/settings")\' type="button"><i class="fa fa-remove"></i> '. $this->lang->line("Cancel").'</button>
	                    </div>'; ?>


	<form class="form-horizontal text-c" enctype="multipart/form-data" action="<?php echo site_url('admin/frontend_settings_action');?>" method="POST">	
		
		<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">

		<div class="section-body">
			<div id="output-status"></div>
			<div class="row">

				<div class="col-md-8">
					<div class="card" id="general-settings">

						<div class="card-header">
							<h4><i class="fas fa-wrench"></i> <?php echo $this->lang->line("General Settings"); ?></h4>
						</div>
						<div class="card-body">
							<div class="row">
					            <div class="col-12 col-md-6">
						            <div class="form-group">
					           		  <?php	
				               			$display_landing_page = $this->config->item('display_landing_page');
				               			if($display_landing_page == '') $display_landing_page='0';
				               		  ?>
				               		  <br>
					           		  <label class="custom-switch mt-2">
					           		    <input type="checkbox" name="display_landing_page" value="1" class="custom-switch-input"  <?php if($display_landing_page=='1') echo 'checked'; ?>>
					           		    <span class="custom-switch-indicator"></span>
					           		    <span class="custom-switch-description"><?php echo $this->lang->line('Display Landing Page');?></span>
					           		    <span class="red"><?php echo form_error('display_landing_page'); ?></span>
					           		  </label>
					           		</div>
				           		</div>
				           		<div class="col-12 col-md-6">
						            <div class="form-group">
						             	<label for=""><i class="fas fa-palette"></i> <?php echo $this->lang->line("Theme Color");?> ( <?php echo $this->lang->line("default theme only");?>) </label>            			
				               			<?php 
				               			$select_front_theme="purple";
										if($this->config->item('theme_front')!="") $select_front_theme=$this->config->item('theme_front');
										// echo form_dropdown('theme_front',$themes_front,$select_front_theme,'class="form-control" id="theme_front"');  ?>

										  <div class="row gutters-xs">
										    <div class="col-auto">
										      <label class="colorinput">
										        <input name="theme_front" type="radio" value="white" class="colorinput-input" <?php if ($select_front_theme == 'white') echo "checked"; ?>/>
										        <span class="colorinput-color bg-white"></span>
										      </label>
										    </div>
										    <div class="col-auto">
										      <label class="colorinput">
										        <input name="theme_front" type="radio" value="black" class="colorinput-input" <?php if ($select_front_theme == 'black') echo "checked"; ?>/>
										        <span class="colorinput-color bg-dark"></span>
										      </label>
										    </div>
										    <div class="col-auto">
										      <label class="colorinput">
										        <input name="theme_front" type="radio" value="blue" class="colorinput-input" <?php if ($select_front_theme == 'blue') echo "checked"; ?>/>
										        <span class="colorinput-color bg-blue"></span>
										      </label>
										    </div>
										    <div class="col-auto">
										      <label class="colorinput">
										        <input name="theme_front" type="radio" value="green" class="colorinput-input" <?php if ($select_front_theme == 'green') echo "checked"; ?>/>
										        <span class="colorinput-color bg-green"></span>
										      </label>
										    </div>
										    <div class="col-auto">
										      <label class="colorinput">
										        <input name="theme_front" type="radio" value="purple" class="colorinput-input" <?php if ($select_front_theme == 'purple') echo "checked"; ?>/>
										        <span class="colorinput-color bg-purple"></span>
										      </label>
										    </div>
										    <div class="col-auto">
										      <label class="colorinput">
										        <input name="theme_front" type="radio" value="red" class="colorinput-input" <?php if ($select_front_theme == 'red') echo "checked"; ?>/>
										        <span class="colorinput-color bg-red"></span>
										      </label>
										    </div>
										    <div class="col-auto">
										      <label class="colorinput">
										        <input name="theme_front" type="radio" value="yellow" class="colorinput-input" <?php if ($select_front_theme == 'yellow') echo "checked"; ?>/>
										        <span class="colorinput-color bg-yellow"></span>
										      </label>
										    </div>
										  
										</div>
				             			<span class="red"><?php echo form_error('Front-end Theme'); ?></span>
						            </div>
					        	</div>
				       		</div>
						</div>
						<?php echo $save_button; ?>
					</div>



					<div class="card" id="social-settings">

						<div class="card-header">
							<h4><i class="fas fa-share-square"></i> <?php echo $this->lang->line("Social Settings"); ?></h4>
						</div>
						<div class="card-body">
       	        			<div class="row">
       	        				<div class="col-12 col-md-6"> 			
       					            <div class="form-group">
       					             	<label for=""><i class="fab fa-facebook"></i> <?php echo $this->lang->line("Facebook");?></label>
       					             	<input name="facebook_link" value="<?php echo $this->config->item('facebook');?>" class="form-control" type="text">		          
       			             			<span class="red"><?php echo form_error('facebook_link'); ?></span>           			
       					            </div>
       					        </div>
       					        <div class="col-12 col-md-6">
       					            <div class="form-group">
       					             	<label for=""><i class="fab fa-twitter"></i> <?php echo $this->lang->line("Twitter");?></label>
       					             	<input name="twitter_link" value="<?php echo $this->config->item('twitter');?>" class="form-control" type="text">		          
       			             			<span class="red"><?php echo form_error('twitter_link'); ?></span>           			
       					            </div>
       				            </div>
       			            </div>

       						<div class="row">
       	        				<div class="col-12 col-md-6">
       					            <div class="form-group">
       					             	<label for=""><i class="fab fa-linkedin"></i> <?php echo $this->lang->line("Linkedin");?></label>
       					             	<input name="linkedin_link" value="<?php echo $this->config->item('linkedin');?>" class="form-control" type="text">		          
       			             			<span class="red"><?php echo form_error('linkedin_link'); ?></span>           			
       					            </div>
       					        </div>
       							<div class="col-12 col-md-6">
       					            <div class="form-group">
       					             	<label for=""><i class="fab fa-youtube"></i> <?php echo $this->lang->line("Youtube");?></label>
       					             	<input name="youtube_link" value="<?php echo $this->config->item('youtube');?>" class="form-control" type="text">		          
       			             			<span class="red"><?php echo form_error('youtube_link'); ?></span>           			
       					            </div>
       					        </div>
       					    </div>	
						</div>
						<?php echo $save_button; ?>
					</div>
					


					<div class="card" id="review-settings">

						<div class="card-header">
							<h4><i class="fas fa-smile"></i> <?php echo $this->lang->line("Review Settings"); ?></h4>
						</div>
						<div class="card-body">

							<div class="form-group">
			           		  <?php	
		               			$display_review_block = $this->config->item('display_review_block');
		               			if($display_review_block == '')	$display_review_block='0';
		               		  ?>
			           		  <label class="custom-switch mt-2">
			           		    <input type="checkbox" name="display_review_block" value="1" class="custom-switch-input"  <?php if($display_review_block=='1') echo 'checked'; ?>>
			           		    <span class="custom-switch-indicator"></span>
			           		    <span class="custom-switch-description"><?php echo $this->lang->line('Display Review Block');?></span>
			           		    <span class="red"><?php echo form_error('display_review_block'); ?></span>
			           		  </label>
			           		</div>		

							<!-- review block display section -->
							<?php $customer_review = $this->config->item('customer_review'); ?>

							<div class="allReview">
								<!-- demo video section started -->
					            <div class="form-group">
					             	<label for=""><i class="fa fa-play-circle"></i> <?php echo $this->lang->line("Customer Review Video");?></label>
					             	<input name="customer_review_video" value="<?php echo $this->config->item('customer_review_video');?>" class="form-control" type="text">
			             			<span class="red"><?php echo form_error('customer_review_video'); ?></span>           			
					            </div>
					            <!-- end of the demo video section -->

								
								<!-- showing reviews section -->
								<div id="accordion">
								<?php $i = 1; 
									foreach($customer_review as $singleReview) :
										$original = $singleReview[2];
		                                $base     = base_url();

		                                if (substr($original, 0, 4) != 'http') {
		                                    $img = $base.$original;
		                                } else {
		                                   $img = $original;
		                                }

								?>
									  <div class="accordion">
									    <div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#panel-body-<?php echo $i; ?>" aria-expanded="false">
									      <h4><i class="fa fa-thumbs-up"></i> <?php echo $this->lang->line('Review #').' '.$i.' '; ?></h4>
									    </div>
									    <div class="accordion-body collapse" id="panel-body-<?php echo $i; ?>" data-parent="#accordion" style="padding: 25px;">
									      	<div class="row">
												<div class="col-xs-12 col-md-6">
										           	<div class="form-group">
										             	<label ><i class="fa fa-user"></i> <?php echo $this->lang->line('Name');?></label>
								               			<input name="reviewer<?php echo $i; ?>" value="<?php echo $singleReview[0];?>" class="form-control" type="text">		          
								             			<span class="red"><?php echo form_error('reviewer'); ?></span>
										           </div>
									           	</div>
											
									           	<div class="col-xs-12 col-md-6">
										           	<div class="form-group">
										             	<label ><i class="fa fa-briefcase"></i> <?php echo $this->lang->line('Designation');?></label>
								               			<input name="designation<?php echo $i; ?>" value="<?php echo $singleReview[1];?>"  class="form-control" type="text">		          
								             			<span class="red"><?php echo form_error('designation'); ?></span>
										           </div>
									           	</div>
										  	</div>

											<div class="row">
									           	<div class="col-xs-12 col-md-12">
										           	<div class="form-group">
										             	<label ><i class="fa fa-picture-o"></i> <?php echo $this->lang->line('Avatar');?></label>
								               			<input name="pic<?php echo $i; ?>" value="<?php echo $img;?>"  class="form-control" type="text">		          
								             			<span class="red"><?php echo form_error('pic'); ?></span>
										           </div>
									           	</div>
								          	</div>

								          	<div class="row">
									           	<div class="col-xs-12 col-md-12">
										           	<div class="form-group">
										             	<label ><i class="fa fa-comment"></i> <?php echo $this->lang->line('Review');?><small style="font-size: 12px;">&nbsp;</small></label>
								               			<textarea name="description<?php echo $i; ?>" rows="3" class="form-control" type="text"><?php echo $singleReview[3];?></textarea>	
										           </div>
									           	</div>
								           	</div>
									    </div>
									  </div>
									 
			        			<?php $i++; endforeach; ?>	
			        			</div>
								<!-- end of showing reviews section -->
							</div>
		        			<!-- end display review block section -->
						</div>
						<?php echo $save_button; ?>
					</div>


					
					<div class="card" id="video-settings">

						<div class="card-header">
							<h4><i class="fas fa-video"></i> <?php echo $this->lang->line("Video Settings"); ?></h4>
						</div>
						<div class="card-body">
							<div class="form-group">
			           		  <?php	
		               			$display_video_block = $this->config->item('display_video_block');
		               			if($display_video_block == '')	$display_video_block='0';
		               		  ?>
			           		  <label class="custom-switch mt-2">
			           		    <input type="checkbox" name="display_video_block" value="1" class="custom-switch-input"  <?php if($display_video_block=='1') echo 'checked'; ?>>
			           		    <span class="custom-switch-indicator"></span>
			           		    <span class="custom-switch-description"><?php echo $this->lang->line('Display Tutorial Block');?></span>
			           		    <span class="red"><?php echo form_error('display_video_block'); ?></span>
			           		  </label>
			           		</div>	
			           		
			            	
		           			<div class="extensions">
								<!-- promo video section started -->
					            <div class="form-group">
					             	<label for=""><i class="fa fa-play-circle"></i> <?php echo $this->lang->line("Promo Video");?></label>
					             	<input name="promo_video" value="<?php echo $this->config->item('promo_video');?>" class="form-control" type="text">
			             			<span class="red"><?php echo form_error('promo_video'); ?></span>           			
					            </div>
				            	<!-- end of the promo video section -->

				            	<?php $custom_video = $this->config->item('custom_video'); ?>
				            	<!-- video tutorial section started -->
				            	<div id="accordion-1">
					            <?php $i = 1; 
					            	foreach ($custom_video as $singleVideo) : 
					            	$original_video = $singleVideo[0];
					                $baseurl    = base_url();

					                if (substr($original_video,0,4) != 'http') {
					                    $thumb = $baseurl.$original_video;
					                } else {
					                    $thumb = $original_video;
					                }
					            ?>
									<div class="accordion">
									  <div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#video-settings-body-<?php echo $i; ?>" aria-expanded="false">
									    <h4><i class="fa fa-youtube"></i> <?php echo $this->lang->line('Tutorial # ').' '.$i.' '; ?></h4>
									  </div>
									  <div class="accordion-body collapse" id="video-settings-body-<?php echo $i; ?>" data-parent="#accordion-1" style="padding: 25px;">
						    			<div class="row">
						    				<div class="col-xs-12 col-md-6">
						    		           	<div class="form-group">
						    		             	<label ><i class="fa fa-image"></i> <?php echo $this->lang->line('Thumbnail');?></label>
						                   			<input name="thumbnail<?php echo $i; ?>" value="<?php echo $thumb; ?>"  class="form-control" type="text">		          
						                 			<span class="red"><?php echo form_error('thumbnail'); ?></span>
						    		           </div>
						    	           	</div>
						    				
						    	           	<div class="col-xs-12 col-md-6">
						    		           	<div class="form-group">
						    		             	<label ><i class="fa fa-hashtag"></i> <?php echo $this->lang->line('Title');?></label>
						                   			<input name="title<?php echo $i; ?>" value="<?php echo $singleVideo[1]; ?>"  class="form-control" type="text">		          
						                 			<span class="red"><?php echo form_error('title'); ?></span>
						    		           </div>
						    	           	</div>
						    			</div>

						    			<div class="row">
						    	           	<div class="col-xs-12 col-md-12">
						    		           	<div class="form-group">
						    		             	<label ><i class="fa fa-link"></i> <?php echo $this->lang->line('URL');?></label>
						                   			<input name="video_url<?php echo $i; ?>" value="<?php echo $singleVideo[2]; ?>"  class="form-control" type="text">		          
						                 			<span class="red"><?php echo form_error('video_url'); ?></span>
						    		           </div>
						    	           	</div>
						              	</div>
									  </div>
									</div>
									
				        		<?php $i++; endforeach; ?>
				        		</div>
			        			<!-- end of the video tutorial section -->
				           	</div>
						</div>
						<?php echo $save_button; ?>
					</div>


				</div>


				<div class="col-md-4 d-none d-sm-block">
					<div class="sidebar-item">
						<div class="make-me-sticky">
							<div class="card">
								<div class="card-header">
									<h4><i class="fas fa-columns"></i> <?php echo $this->lang->line("Sections"); ?></h4>
								</div>
								<div class="card-body">
									<ul class="nav nav-pills flex-column settings_menu">
										<li class="nav-item"><a href="#general-settings" class="nav-link"><i class="fas fa-wrench"></i> <?php echo $this->lang->line("General Settings"); ?></a></li>
										<li class="nav-item"><a href="#social-settings" class="nav-link"><i class="fas fa-share-square"></i> <?php echo $this->lang->line("Social Settings"); ?></a></li>
										<li class="nav-item"><a href="#review-settings" class="nav-link"><i class="fas fa-smile"></i> <?php echo $this->lang->line("Review Settings"); ?></a></li>
										<li class="nav-item"><a href="#video-settings" class="nav-link"><i class="fas fa-video"></i> <?php echo $this->lang->line("Video Settings"); ?></a></li>								
									</ul>

								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

</section>




<script>
	$("document").ready(function() {
     	var val1 = "0";
     	var val2 = "0";

     	if ($("input[name='display_review_block']").is(':checked')) val1='1';
     	if ($("input[name='display_video_block']").is(':checked')) val2='1';

      	// initail situation
     	// review block
     	if(val1 =='0') 
     	{
     		$('.allReview').hide();
     		$('.review_block').css("min-height","150px");
     	} 
     	else 
     	{
     		$('.review_block').css("min-height","1266px");
     	}

     	// video block
     	if(val2 =='0') 
     	{
     		$('.extensions').hide();
     		$('.video_block').css("min-height","150px");
     	} 
     	else 
     	{
     		$('.video_block').css("min-height","1266px");
     	}


     	$('input[name=display_review_block]').change(function() {
      		if ($("input[name='display_review_block']").is(':checked'))
      		{
        		$('.allReview').show();
        		$('.review_block').css("min-height","1266px");
        		
        	} else {
        		$('.allReview').hide();
        		$('.review_block').css("min-height","150px");
        	}
   		}); 

     	$('input[name=display_video_block]').change(function() {
        	if ($("input[name='display_video_block']").is(':checked'))
        	{
        		$('.extensions').show();
        		$('.video_block').css("min-height","1266px");
        	} else {
        		$('.extensions').hide();
        		$('.video_block').css("min-height","150px");
        	}
      	});
    });
</script>



<script type="text/javascript">
  $('document').ready(function(){
    $(".settings_menu a").click(function(){
    	$(".settings_menu a").removeClass("active");
    	$(this).addClass("active");
    });
  });
</script>



