<style>

	.makeScroll {height: 500px;overflow: hidden;}
	.medium_text_and_button h4 {
		line-height: 40px !important;
	}

	.medium_text_and_button .card-header-action {
		margin-top:5px !important; 
	}

	.medium_account_token_field input {
		font-size: 14px !important;
	    padding: 10px 15px !important;
	    height: 35px !important;
	    border-top-right-radius: 0 !important;
	    border-bottom-right-radius: 0 !important;
	}

	.medium_account_token_field .import_medium_account {
		border-top-left-radius: 0 !important;
		border-bottom-left-radius: 0 !important;
	}
	@media (max-width: 575.98px) {
		.avatar-item .avatar-badge {
		    position: absolute;
		    bottom: 5px;
		    right: 95px;
		    margin-right: 0;
		}
	}

	.modal-backdrop {
	    display: none;
	}
	
</style>

<section class="section">
	<div class="section-header">
		<h1><i class="far fa-list-alt"></i> <?php echo $this->lang->line('Social Accounts'); ?></h1>
		<div class="section-header-breadcrumb">
		  <div class="breadcrumb-item"><a href="<?php echo base_url('ultrapost'); ?>"><?php echo $this->lang->line('Comboposter'); ?></a></div>
		  <div class="breadcrumb-item"><?php echo $this->lang->line('Social Accounts'); ?></div>
		</div>
	</div>

	<?php 

		if($this->session->userdata('account_import_error') != '') {

			echo "<div class='alert alert-danger text-center'><i class='fas fa-check-circle'></i> ".$this->session->userdata('account_import_error')."</div>";
			$this->session->unset_userdata('account_import_error');
		}

		
		if($this->session->userdata('limit_cross') != '') {

			echo "<div class='alert alert-danger text-center'><i class='fas fa-check-circle'></i> ".$this->session->userdata('limit_cross')."</div>";
			$this->session->unset_userdata('limit_cross');
		}

	 ?>

	<div class="section-body">
		<div class="row">
			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(102,$this->module_access)) : ?>
				<div class="col-12 col-sm-12 col-lg-6 ">
					<div class="card makeScroll">
					  <div class="card-header">
					    <h4><i class="fab fa-twitter"></i> <?php echo $this->lang->line('Twitter Accounts'); ?></h4>
					    <div class="card-header-action youtube_button">
					    	<?php echo $twitter_login_button; ?>
					    </div>
					  </div>
					  <div class="card-body">
					    
					        
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($twitter_account_list as $key => $single_account): ?>

					        		<li class="media">
					        		    <?php $img_src = $single_account['profile_image']; ?>
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		        	  		<img alt="image" width="50" src="<?php echo $img_src; ?>" class="img-fluid">
		        		        	  		<div class="avatar-badge delete_account" title="<?php echo $this->lang->line("Delete Account"); ?>" social_media="twitter" table_id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#"><i class="fas fa-trash-alt red" style="margin-left: 0;"></i></a>
		        		    	    	  	</div>
		        		    	    	</div>
					        			
					        		    <div class="media-body">
					        		      <div class="media-title"><?php echo $single_account['name']; ?></div>
					        		      <div class="text-job text-muted"><?php echo $single_account['screen_name'] ?></a></div>
					        		    </div>
					        		    <div class="media-items">
					        		      <div class="media-item">
					        		        <div class="media-value"><?php echo $single_account['followers']; ?></div>
					        		        <div class="media-label"><?php echo $this->lang->line('Followers'); ?></div>
					        		      </div>
					        		    </div>
					        		</li>
					        		

					        	<?php endforeach ?>
						        
					        </ul>
					      
					  </div>
					</div>
				</div>
			<?php endif; ?>

			<!--
			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(104,$this->module_access)) : ?>
				<div class="col-12 col-sm-12 col-lg-6 ">
					<div class="card makeScroll">
					  <div class="card-header">
					    <h4><i class="fab fa-twitter"></i> <?php echo $this->lang->line('Tumblr Accounts'); ?></h4>
					    <div class="card-header-action youtube_button">
					    	<?php echo $tumblr_login_button; ?>
					    </div>
					  </div>
					  <div class="card-body">
					    
					        
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($tumblr_account_list as $key => $single_account): ?>

					        		<li class="media">
					        		    <?php $img_src = "https://api.tumblr.com/v2/blog/" . $single_account['user_name'] . ".tumblr.com/avatar"; ?>
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		        	  		<img alt="image" width="50" src="<?php echo $img_src; ?>" class="img-fluid">
		        		        	  		<div class="avatar-badge delete_account" title="<?php echo $this->lang->line("Delete Account"); ?>" social_media="tumbrl" table_id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#"><i class="fas fa-trash-alt red" style="margin-left: 0;"></i></a>
		        		    	    	  	</div>
		        		    	    	</div>
					        			
					        		    <div class="media-body">
					        		      <div class="media-title"><?php echo $single_account['user_title']; ?></div>
					        		      <div class="text-job text-muted"><?php echo $single_account['user_name'] ?></a></div>
					        		    </div>
					        		    <div class="media-items">
					        		      <div class="media-item">
					        		        <div class="media-value"><?php echo $single_account['user_followers']; ?></div>
					        		        <div class="media-label"><?php echo $this->lang->line('Followers'); ?></div>
					        		      </div>
					        		    </div>
					        		</li>
					        		

					        	<?php endforeach ?>
						        
					        </ul>
					      
					  </div>
					</div>
				</div>
			<?php endif; ?>
			-->

			
			<!-- <?php if($this->session->userdata('user_type') == 'Admin' || in_array(33,$this->module_access)) : ?>			
				<div class="col-12 col-sm-12 col-lg-6">
					<div class="card makeScroll">
						<div class="card-header">
						    <h4><i class="fab fa-youtube"></i> <?php echo $this->lang->line('Youtube Channels') ?></h4>
						    <div class="card-header-action">
						    	<?php echo $youtube_login_button; ?>
						    </div>
						</div>
						<div class="card-body">
						    <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
						    	<?php foreach ($youtube_channel_list as $key => $single_channel): ?>
								    <li class="media">
								    	<div class="avatar-item" style="margin-right:20px;">
							    	  		<img alt="image" width="50" src="<?php echo $single_channel['profile_image']; ?>" class="img-fluid">
							    	  		<div class="avatar-badge delete_account" title="<?php echo $this->lang->line("Delete Account"); ?>" social_media="youtube" table_id="<?php echo $single_channel['id']; ?>" data-toggle="tooltip">
								    	  		<a href="#"><i class="fas fa-trash-alt red" style="margin-left: 0;"></i></a>
								    	  	</div>
								    	</div>
								        
								        <div class="media-body">
							          		<div class="media-title">
							          			<?php echo (strlen($single_channel['title']) < 15 ) ? $single_channel['title'] : substr($single_channel['title'], 0,12).'...'; ?>
						          			</div>
								          	<div class="text-job text-muted"><?php echo $single_channel['channel_id']; ?></div>
								        </div>
								        <div class="media-items">
								          	<div class="media-item">
								            	<div class="media-value"><?php echo $single_channel['video_count']; ?></div>
								            	<div class="media-label"><?php echo $this->lang->line('Videos') ?></div>
								          	</div>
								          	<div class="media-item">
								            	<div class="media-value"><?php echo $single_channel['subscriber_count']; ?></div>
								            	<div class="media-label"><?php echo $this->lang->line('Subscribers') ?></div>
								          	</div>
								        </div>
								    </li>
						    	<?php endforeach ?>
						    </ul>
						</div>
					</div>
				</div>
			<?php endif; ?> -->

			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(103,$this->module_access)) : ?>
				<div class="col-12 col-sm-12 col-lg-6 ">
					<div class="card makeScroll">
					  <div class="card-header">
					    <h4><i class="fab fa-linkedin"></i> <?php echo $this->lang->line('Linkedin Accounts'); ?></h4>
					    <div class="card-header-action youtube_button">
					    	<?php echo $linkedin_login_button; ?>
					    </div>
					  </div>
					  <div class="card-body">
					    
					        
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($linkedin_account_list as $key => $single_account): ?>

					        		<li class="media">
					        		    <?php $img_src = $single_account['profile_pic']; ?>
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		        	  		<img alt="image" width="50" src="<?php echo $img_src; ?>" class="img-fluid">
		        		        	  		<div class="avatar-badge delete_account" title="<?php echo $this->lang->line("Delete Account"); ?>" social_media="linkedin" table_id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#"><i class="fas fa-trash-alt red" style="margin-left: 0;"></i></a>
		        		    	    	  	</div>
		        		    	    	</div>
					        		    <div class="media-body">
					        		      <div class="media-title" style="padding-top: 12px;"><?php echo $single_account['name']; ?></div>
					        		      <div class="text-job text-muted"><?php echo $single_account['linkedin_id'] ?></a></div>
					        		    </div>
					        		    
					        		</li>
					        		

					        	<?php endforeach ?>
						        
					        </ul>
					      
					  </div>
					</div>
				</div>
			<?php endif; ?>

			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(105,$this->module_access)) : ?>
				<div class="col-12 col-sm-12 col-lg-6 ">
					<div class="card makeScroll">
					  <div class="card-header">
					    <h4><i class="fab fa-reddit-square"></i> <?php echo $this->lang->line('Reddit Accounts'); ?></h4>
					    <div class="card-header-action youtube_button">
					    	<?php echo $reddit_login_button; ?>
					    </div>
					  </div>
					  <div class="card-body">
					    
					        
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($reddit_account_list as $key => $single_account): ?>

					        		<li class="media">
					        		    <?php $img_src = $single_account['profile_pic']; ?>
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		        	  		<img alt="image" width="50" src="<?php echo $img_src; ?>" class="img-fluid">
		        		        	  		<div class="avatar-badge delete_account" title="<?php echo $this->lang->line("Delete Account"); ?>" social_media="reddit" table_id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#"><i class="fas fa-trash-alt red" style="margin-left: 0;"></i></a>
		        		    	    	  	</div>
		        		    	    	</div>
					        		    <div class="media-body">
					        		      <div class="media-title"><?php echo $single_account['username']; ?></div>
					        		      <div class="text-job text-muted"><a href="<?php echo 'https://www.reddit.com'.$single_account['url'] ?>" target="_BLANK"><?php echo $this->lang->line("Visit Reddit"); ?></a></div>
					        		    </div>
					        		    
					        		</li>
					        		

					        	<?php endforeach ?>
						        
					        </ul>
					      
					  </div>
					</div>
				</div>
			<?php endif; ?>
			
			<!--
			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(101,$this->module_access)) : ?>	
				<div class="col-12 col-sm-12 col-lg-6 ">
					<div class="card makeScroll">
					  <div class="card-header">
					    <h4><i class="fab fa-pinterest-square"></i> <?php echo $this->lang->line('Pinterest Accounts'); ?></h4>
					    <div class="card-header-action youtube_button">
					    	<?php echo $pinterest_login_button; ?>
					    </div>
					  </div>
					  <div class="card-body">
					    
					        
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($pinterest_account_list as $key => $single_account): ?>

					        		<li class="media">
					        		    <?php $img_src = $single_account['image']; ?>
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		        	  		<img alt="image" width="50" src="<?php echo $img_src; ?>" class="img-fluid">
		        		        	  		<div class="avatar-badge delete_account" title="<?php echo $this->lang->line("Delete Account"); ?>" social_media="pinterest" table_id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#"><i class="fas fa-trash-alt red" style="margin-left: 0;"></i></a>
		        		    	    	  	</div>
		        		    	    	</div>
					        		    <div class="media-body">
					        		      <div class="media-title" style="padding-top: 12px;"><?php echo $single_account['name']; ?></div>
					        		      <div class="text-job text-muted"><?php echo $single_account['user_name'] ?></a></div>
					        		    </div>
					        		    <div class="media-items">
					        		      <div class="media-item">
					        		        <div class="media-value"><?php echo $single_account['boards']; ?></div>
					        		        <div class="media-label"><?php echo $this->lang->line('Boards'); ?></div>
					        		      </div>
					        		      <div class="media-item">
					        		        <div class="media-value"><?php echo $single_account['pins']; ?></div>
					        		        <div class="media-label"><?php echo $this->lang->line('Pins'); ?></div>
					        		      </div>
					        		    </div>
					        		    
					        		</li>
					        		

					        	<?php endforeach ?>
						        
					        </ul>
					      
					  </div>
					</div>
				</div>
			<?php endif; ?>	
			-->

			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(107,$this->module_access)) : ?>
				<div class="col-12 col-sm-12 col-lg-6 ">
					<div class="card makeScroll">
					  <div class="card-header">
					    <h4><i class="fab fa-blogger"></i> <?php echo $this->lang->line('Blogger Accounts'); ?></h4>
					    <div class="card-header-action youtube_button">
					    	<?php echo $blogger_login_button; ?>
					    </div>
					  </div>
					  <div class="card-body">
					    
					        
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($blogger_account_list as $key => $single_account): ?>

					        		<li class="media">
					        		    <?php $img_src = $single_account['picture']; ?>
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		        	  		<img alt="image" width="50" src="<?php echo $img_src; ?>" class="img-fluid">
		        		        	  		<div class="avatar-badge delete_account" title="<?php echo $this->lang->line("Delete Account"); ?>" social_media="blogger" table_id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#"><i class="fas fa-trash-alt red" style="margin-left: 0;"></i></a>
		        		    	    	  	</div>
		        		    	    	</div>
					        		    <div class="media-body">
					        		      <div class="media-title" style="padding-top: 12px;"><?php echo $single_account['name']; ?></div>
					        		      <div class="text-job text-muted"><?php echo $single_account['blogger_id'] ?></a></div>
					        		    </div>
					        		    <div class="media-items">
					        		      <div class="media-item">
					        		        <div class="media-value"><?php echo $single_account['blog_count']; ?></div>
					        		        <div class="media-label"><?php echo $this->lang->line('Blogs'); ?></div>
					        		      </div>
					        		    </div>
					        		    
					        		</li>
					        		

					        	<?php endforeach ?>
						        
					        </ul>
					      
					  </div>
					</div>
				</div>
			<?php endif; ?>

			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(108,$this->module_access)) : ?>
				<div class="col-12 col-sm-12 col-lg-6 ">
					<div class="card makeScroll">
					  <div class="card-header">
					    <h4><i class="fab fa-wordpress"></i> <?php echo $this->lang->line('Wordpress Accounts'); ?></h4>
					    <div class="card-header-action youtube_button">
					    	<?php echo $wordpress_login_button; ?>
					    </div>
					  </div>
					  <div class="card-body">
					    
					        
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($wordpress_account_list as $key => $single_account): ?>

					        		<li class="media">
					        		    <?php $img_src = $single_account['icon']; ?>
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		        	  		<img alt="image" width="50" src="<?php echo $img_src; ?>" class="img-fluid">
		        		        	  		<div class="avatar-badge delete_account" title="<?php echo $this->lang->line("Delete Account"); ?>" social_media="wordpress" table_id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#"><i class="fas fa-trash-alt red" style="margin-left: 0;"></i></a>
		        		    	    	  	</div>
		        		    	    	</div>
					        		    <div class="media-body">
					        		      <div class="media-title" style="padding-top: 12px;"><?php echo $single_account['name']; ?></div>
					        		      <div class="text-job text-muted"><?php echo $single_account['blog_id'] ?></a></div>
					        		    </div>
					        		    <div class="media-items">
					        		      <div class="media-item">
					        		        <div class="media-value"><?php echo $single_account['posts']; ?></div>
					        		        <div class="media-label"><?php echo $this->lang->line('Posts'); ?></div>
					        		      </div>
					        		    </div>
					        		    
					        		</li>
					        		

					        	<?php endforeach ?>
						        
					        </ul>
					      
					  </div>
					</div>
				</div>
			<?php endif; ?>

			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(109,$this->module_access)) : ?>
				<div class="col-12 col-sm-12 col-lg-6">
					<div class="card makeScroll">
					  	<div class="card-header">
						    <h4><i class="fab fa-wordpress"></i> <?php echo $this->lang->line('Wordpress Site (Self-Hosted)'); ?></h4>
						    <div class="card-header-action wordpress_self_hosted_login_button">
						    	<?php echo $wordpress_self_hosted_login_button; ?>
						    </div>
					  	</div><!-- card-header -->
						<div class="card-body">
						    
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($wordpress_account_list_self_hosted as $key => $single_account): ?>

					        		<li class="media">
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		        	  		<img alt="image" width="50" src="<?php echo base_url('assets/images/wordpress.png'); ?>" class="img-fluid">
		        		        	  		<div id="delete-wssh-settings" class="avatar-badge" data-original-title="<?php echo $this->lang->line("Delete Account"); ?>" data-site-id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#">
		        		    	    	  			<i class="fas fa-trash-alt red" style="margin-left: 0;"></i>
		        		    	    	  		</a>
		        		    	    	  	</div>
		        		    	    	</div>
					        		    <div class="media-body">
					        		      	<div class="media-title" style="padding-top: 12px;"><?php echo $single_account['domain_name']; ?></div>
					        		      	<div class="text-muted"><?php echo $single_account['user_key'] ?></div>
					        		    </div>
					        		</li>
					        		
					        	<?php endforeach ?>
						        
					        </ul>
						      
						</div><!-- card-body -->
					</div>
				</div>			
			<?php endif; ?>

			<?php if($this->session->userdata('user_type') == 'Admin' || in_array(277,$this->module_access)) : ?>
				<div class="col-12 col-sm-12 col-lg-6">
					<div class="card makeScroll">
					  	<div class="card-header d-block">
					  		<div class="medium_text_and_button">
					  			<h4 class="d-inline"><i class="fab fa-medium"></i> <?php echo $this->lang->line('Medium Accounts'); ?></h4>
					  			<div class="card-header-action float-right">
					  				<a href="#" class="btn btn-outline-primary show_hide_medium_token_field"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Import Account'); ?></a>
					  				<!-- <?php echo $medium_login_button; ?> -->
					  			</div>
					  		</div>
					  		<div class="form-group mb-0 mt-3 medium_account_token_field" style="display: none;">
					  			<div class="input-group">
					  				<input type="text" class="form-control" id="medium_integration_token" name="medium_integration_token" placeholder="<?php echo $this->lang->line('Provide Integration Token'); ?>" aria-label="">
					  				<div class="input-group-append">
					  					<button class="btn btn-primary mt-0 import_medium_account" type="button"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line('Import'); ?></button>
					  				</div>
					  			</div>
					  		</div>
					  	</div><!-- card-header -->
						<div class="card-body">
						    
					        <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
					        	<?php foreach ($medium_account_list as $key => $single_account): ?>

					        		<li class="media">
		        		    	    	<div class="avatar-item" style="margin-right:20px;">
		        		    	    		<img class="rounded-circle" width="50" src="<?php echo $single_account['profile_pic']; ?>" alt="avatar">

		        		        	  		<div class="avatar-badge delete_account" social_media="medium" data-original-title="<?php echo $this->lang->line("Delete Account"); ?>" table_id="<?php echo $single_account['id']; ?>" data-toggle="tooltip">
		        		    	    	  		<a href="#">
		        		    	    	  			<i class="fas fa-trash-alt red" style="margin-left: 0;"></i>
		        		    	    	  		</a>
		        		    	    	  	</div>
		        		    	    	</div>
					        		    <div class="media-body">
					        		      	<div class="media-title" style="padding-top: 12px;"><?php echo $single_account['name']; ?></div>
					        		      	<div class="text-muted"><?php echo $single_account['medium_id'] ?></div>
					        		    </div>
					        		</li>
					        		
					        	<?php endforeach ?>
						        
					        </ul>
						      
						</div><!-- card-body -->
					</div>
				</div>			
			<?php endif; ?>


		</div>
	</div>


<script>
	$(document).ready(function() {
		$(document).on('click', '.delete_account', function(event) {
			event.preventDefault();

			swal({
			  title: 'Are you sure?',
			  text: 'Do you really want to delete this account? If you delete this account it will be deleted from your database.',
			  icon: 'warning',
			  buttons: true,
			  dangerMode: true,
			})
			.then((willDelete) => {
			  if (willDelete) 
			  {
			    let social_media = $(this).attr('social_media');
			    let table_id = $(this).attr('table_id');

			    $.ajax({
			      context: this,
			      type:'POST',
			      dataType: 'json',
			      url:"<?php echo base_url('comboposter/delete_social_account'); ?>",
			      data:{social_media: social_media, table_id: table_id},
			      success:function(response){ 

			        if (response.status == 'success') {
			          iziToast.success({title: '', message: response.message, position: 'bottomRight'});
			        } else if (response.status == 'error') {
			          iziToast.error({title: '',message: response.message, position: 'bottomRight'});
			        }

			        window.location.href = "<?php echo base_url('comboposter/social_accounts'); ?>";
			      }
			    });
			  } 
			});
		});

		$(document).on('click', '.show_hide_medium_token_field', function(event) {
			event.preventDefault();
			$(".medium_account_token_field").toggle(500);
		});

		$(document).on('click', '.api_error_info', function(event) {
			event.preventDefault();
			$("#api_error_info_modal").modal();
		});

		$(document).on('click', '.import_medium_account', function(event) {
			event.preventDefault();

			var integration_token = $("#medium_integration_token").val();

			if(integration_token == "" || integration_token == 'undefined') {
				swal('<?php echo $this->lang->line("Error"); ?>', '<?php echo $this->lang->line("Please provide your integration token"); ?>', 'error');
			}

			$(this).addClass('btn-progress');
			var redirect_url = '<?php echo base_url()?>'+'comboposter/social_accounts';

			$.ajax({
				context:this,
				url: '<?php echo base_url()?>'+'comboposter/login_callback/medium',
				type: 'POST',
				data: {integration_token: integration_token},
				dataType: 'json',
				success:function(response){
					$(this).removeClass('btn-progress');

					if(response.status == '1') {
						var span = document.createElement("span");
						span.innerHTML = response.success_message;
						swal({ title:'<?php echo $this->lang->line("Success"); ?>', content:span,icon:'success'}).then((value) => {window.location.href=redirect_url;});
					}

					if(response.status == '0') {
						var span = document.createElement("span");
						span.innerHTML = response.error_message;
						swal({ title:'<?php echo $this->lang->line("Error"); ?>', content:span,icon:'error'}).then((value) => {window.location.href=redirect_url;});
					}
					
				}
			})
			
		});

		// Attempts to delete wordpress site's settings
		$(document).on('click', '#delete-wssh-settings', function(e) {
			e.preventDefault()

			// Makes reference
			var that = this;

			// Grabs site ID
			var site_id = $(that).data('site-id');

			swal({
				title: '<?php ('Are you sure?'); ?>',
				text: '<?php echo $this->lang->line('Once deleted, you will not be able to recover this wordpress site\'s settings!'); ?>',
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			}).then((yes) => {
				if (yes) {
					$.ajax({
						type: 'POST',
						url: '<?php echo base_url('social_apps/delete_wordpress_settings_self_hosted') ?>',
						dataType: 'JSON',
						data: { site_id },
						success: function(res) {
							if (res) {
								if ('ok' == res.status) {
									// Displays success message
									iziToast.success({title: '',message: res.message,position: 'bottomRight'});
									
									// Removes this element from the UI
									var media_el = $(that).parent().parent();
									media_el.remove();
								} else if (true === res.error) {
									// Displays error message
									iziToast.error({title: '',message: res.message, position: 'bottomRight'});
								}	
							}
						},
						error: function(xhr, status, error) {
							// Displays error message
							iziToast.error({title: '',message: error,position: 'bottomRight'});	
						}
					})
				} else {
					return
				}
			});
		});		
	});
</script>