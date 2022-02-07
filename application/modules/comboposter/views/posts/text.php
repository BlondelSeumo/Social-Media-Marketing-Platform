<?php $include_view_prefix = 'application/modules/comboposter/views/'; ?>


<?php 
	/* include universal css */
	include $include_view_prefix.'posts/universal_css.php';
?>

<section class="section">
	<div class="section-header">
		<?php 
			if ($post_action == 'add') {

				$header = $this->lang->line("Create Post"); 
				$breadcrumb = $this->lang->line("Create post"); 
				$icon = 'fas fa-plus-circle';
				$campaign_btn = $this->lang->line("Create Campaign");
				$campaign_btn_icon = 'fas fa-paper-plane';
			} else if ($post_action == 'edit') {

				$header = $this->lang->line("Edit Post"); 
				$breadcrumb = $this->lang->line("Edit post"); 
				$icon = 'fas fa-edit';
				$campaign_btn = $this->lang->line("Edit Campaign");
				$campaign_btn_icon = 'fas fa-edit';
			} else if ($post_action == 'clone') {

				$header = $this->lang->line("Clone post"); 
				$breadcrumb = $this->lang->line("Clone Post"); 
				$icon = 'fas fa-clone';
				$campaign_btn = $this->lang->line("Clone Campaign");
				$campaign_btn_icon = 'fas fa-clone';
			}
		?>
		<h1><i class="<?php echo $icon; ?>"></i> <?php echo $header; ?></h1>
		<div class="section-header-breadcrumb">
		  <div class="breadcrumb-item"><a href="<?php echo base_url('ultrapost'); ?>"><?php echo $this->lang->line('Comboposter'); ?></a></div>
		  <div class="breadcrumb-item"><a href="<?php echo base_url('comboposter/text_post/campaigns'); ?>"><?php echo $this->lang->line('Text post'); ?></a></div>
		  <div class="breadcrumb-item"><?php echo $breadcrumb; ?></div>
		</div>
	</div>

   	<div class="section-body">
		<div class="row">
			<form action="#" enctype="multipart/form-data" id="comboposter_form" method="post" style="width: 100%;">

				<div class="col-12">
					<?php 
						/* include form */
						include $include_view_prefix.'posts/forms/text_post_form.php';
					?>
				</div>

				<!-- Accounts -->
				<div class="col-12">
					<div class="row">

						<div class="col-12 col-lg-6">
							<?php include $include_view_prefix.'posts/social_accounts/facebook.php'; ?>
						</div>

						<?php if($this->session->userdata('user_type') == 'Admin' || in_array(102,$this->module_access)) : ?>
							<!-- twitter -->
		                    <div class="col-12 col-lg-6">
		                      <?php include $include_view_prefix.'posts/social_accounts/twitter.php'; ?>
		                    </div>
						<?php endif; ?>

						<!--
						<?php if($this->session->userdata('user_type') == 'Admin' || in_array(104,$this->module_access)) : ?>
							 tumblr 
		                    <div class="col-12 col-lg-6">
		                      <?php // include $include_view_prefix.'posts/social_accounts/tumblr.php'; ?>
		                    </div>
						<?php endif; ?>
						-->

						<?php if($this->session->userdata('user_type') == 'Admin' || in_array(103,$this->module_access)) : ?>
		                    <!-- linkedin -->
		                    <div class="col-12 col-lg-6">
		                      <?php include $include_view_prefix.'posts/social_accounts/linkedin.php'; ?>
		                    </div>
						<?php endif; ?>

						<?php if($this->session->userdata('user_type') == 'Admin' || in_array(105,$this->module_access)) : ?>
		                    <!-- reddit -->
		                    <div class="col-12 col-lg-6">
		                      <?php include $include_view_prefix.'posts/social_accounts/reddit.php'; ?>
		                    </div>
						<?php endif; ?>

						<?php if($this->session->userdata('user_type') == 'Admin' || in_array(109,$this->module_access)) : ?>
							<div class="col-12 col-lg-6">
								<?php include $include_view_prefix.'posts/social_accounts/wordpress_self_hosted.php'; ?>
							</div>
						<?php endif; ?>

						<?php if($this->session->userdata('user_type') == 'Admin' || in_array(277,$this->module_access)) : ?>
							<div class="col-12 col-lg-6">
								<?php include $include_view_prefix.'posts/social_accounts/medium.php'; ?>
							</div>
						<?php endif; ?>

					</div>
				</div>	

			</form>

			<div class="col-12">
				<div class="card">
					
			      	<div class="card-body">
						<div class="card-footer padding-0">
							<button class="btn btn-lg btn-primary" post_type="text" action_type="<?php echo $post_action; ?>" id="submit_post" type="button"><i class="<?php echo $campaign_btn_icon ?>"></i> <?php echo $campaign_btn; ?> </button>

							<a class="btn btn-lg btn-light float-right" onclick='goBack("comboposter/text_post/campaigns",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
						</div>
			        </div>
				</div>          

			</div>

		</div>
   	</div>
</section>



<?php 
	/* include universal js */
	include $include_view_prefix.'posts/universal_js.php';
?>


<?php 
 	/* include js for triggering preview for edit */
 	if ($post_action == 'edit') {
 		include $include_view_prefix.'posts/trigger_preview_for_edit.php';
 	}
?>