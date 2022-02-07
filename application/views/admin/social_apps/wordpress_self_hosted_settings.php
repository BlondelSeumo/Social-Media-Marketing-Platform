<style>
	.blue{
		color: #2C9BB3 !important;
	}
</style>

<section class="section">
	<div class="section-header">
		<h1><i class="fab fa-wordpress"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><a href="<?php echo base_url('integration'); ?>"><?php echo $this->lang->line("Integration"); ?></a></div>
			<div class="breadcrumb-item"><a href="<?php echo base_url('social_apps/wordpress_settings_self_hosted'); ?>"><?php echo $this->lang->line("Wordpress (Self-Hosted)"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>
	
	<div class="section-body">
		<form method="POST">
			<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">

			<div class="row">
				<div class="col-12">

					<?php if ($this->session->userdata('add_wssh_error')): ?>
					<div class="alert alert-warning alert-dismissible show fade">
						<div class="alert-body">
							<button class="close" data-dismiss="alert">
								<span>×</span>
							</button>
							<?php echo $this->session->userdata('add_wssh_error'); ?>
							<?php echo $this->session->unset_userdata('add_wssh_error'); ?>
						</div>
					</div>
					<?php endif; ?>

					<!-- starts card -->
					<div class="card">

						<div class="card-header">
							<h4 class="card-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("App Details"); ?></h4>
						</div>

						<div class="card-body">              
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="domain_name"><i class="fas fa-globe-americas"></i> <?php echo $this->lang->line("Wordpress blog URL");?></label>
										<span data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('Provide your wordpress blog URL.'); ?>"><i class="fas fa-info-circle"></i></span>
										<input id="domain_name" name="domain_name" value="<?php echo isset($wp_settings['domain_name']) ? $wp_settings['domain_name'] : set_value('domain_name'); ?>" class="form-control" type="text">  
										<span class="red"><?php echo form_error('domain_name'); ?></span>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label for="user_key"><i class="fas fa-file-signature"></i> <?php echo $this->lang->line("User Key");?></label>
										<span data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('User Key can be achieved from the Wordpress Self-hosted Authentication section of the Wordpress > Users > Your Profile page.'); ?>"><i class="fas fa-info-circle"></i></span>
										<input id="user_key" name="user_key" value="<?php echo isset($wp_settings['user_key']) ? $wp_settings['user_key'] : set_value('user_key'); ?>" class="form-control" type="text">  
										<span class="red"><?php echo form_error('user_key'); ?></span>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label for="authentication_key"><i class="far fa-id-card"></i> <?php echo $this->lang->line("Authentication Key");?></label>
										<span data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('Authentication Key needs to be put on the Wordpress Self-hosted Authentication section of the Wordpress > Users > Your Profile page.'); ?>"><i class="fas fa-info-circle"></i></span>
										<input id="authentication_key" name="authentication_key" value="<?php echo isset($wp_settings['authentication_key']) ? $wp_settings['authentication_key'] : set_value('authentication_key', $auth_key); ?>" class="form-control" type="text">  
										<span class="red"><?php echo form_error('authentication_key'); ?></span>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="custom-switch mt-2">
									<input type="checkbox" name="status" value="1" class="custom-switch-input" <?php echo (isset($wp_settings['status']) && '1' == $wp_settings['status']) ? 'checked' : ''; ?>>
									<span class="custom-switch-indicator"></span>
									<span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
									<span class="red"><?php echo form_error('status'); ?></span>
								</label>
							</div>
						</div>

						<div class="card-footer bg-whitesmoke">
							<button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
							<button class="btn btn-secondary btn-lg float-right" onclick='goBack("social_apps/wordpress_settings_self_hosted")' type="button"><i class="fa fa-remove"></i>  <?php echo $this->lang->line("Cancel");?></button>
						</div>						

					</div>
					<!-- ends card -->
				</div>	
				<!-- ends col-12 -->
			</div>
			<!-- ends row -->
		</form>	
	</div>
	<!-- ends section-body -->
</section>