<?php 
if(isset($fb_login_button))
$fb_login_button=str_replace("ThisIsTheLoginButtonForFacebook",$this->lang->line("Login with Facebook"), $fb_login_button); 
?>
<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fab fa-facebook-square"></i> <?php echo $this->lang->line('Admin login section'); ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
			<div class="breadcrumb-item"><a href="<?php echo base_url('social_apps/index'); ?>"><?php echo $this->lang->line("Social APPs"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<div class="section-body">
		<div class="card" id="nodata">
			<div class="card-body">
				<div class="empty-state">
					<?php if(isset($expired_or_not) && $expired_or_not==1) : ?>
						<h5 class="text-center"><i class="fas fa-info-circle" style="font-size: 18px;"></i> <?php echo $this->lang->line("User access token is valid. you can login and get new user access token if you want."); ?></h5>
					<?php endif; ?>
					<br/>

					<?php if(isset($fb_login_button)) : ?>
						<h3 class="text-center"><?php echo $fb_login_button; ?></h3>
					<?php endif; ?>
					
					<?php 
						if(isset($message)) :
						if(isset($error) && $error==1) : 
					?>
						<img class="img-fluid" style="height: 300px" src="<?php echo base_url('assets/img/drawkit/drawkit-full-stack-man-colour.svg'); ?>" alt="image">
						<h2 class="mt-0 text-danger"><?php echo $message; ?></h2>
						<br/>
					<?php else : ?>
						<h2 class="mt-0 text-info"><?php echo $message; ?></h2>
						<br/>
					<?php 
						endif; 
						endif; 
					?>
					<center><a href="<?php echo base_url("social_apps/index"); ?>"><i class="fa fa-arrow-circle-left"></i> <?php echo $this->lang->line("go back"); ?></a></center>
				</div>
			</div>
		</div>
	</div>
</section>   