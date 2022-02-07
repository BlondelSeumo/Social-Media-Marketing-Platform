<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fab fa-facebook-square"></i> <?php echo $this->lang->line('User login section'); ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><a href="<?php echo base_url('social_accounts/account_import'); ?>"><?php echo $this->lang->line("Import Account"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<div class="section-body">
		<div class="card" id="nodata">
			<div class="card-body">
				<div class="empty-state">
					<?php 
						if(isset($message)) :
						if(isset($error) && $error==1) : 
					?>
						<img class="img-fluid" style="height: 300px" src="<?php echo base_url('assets/img/drawkit/drawkit-full-stack-man-colour.svg'); ?>" alt="image">
						<h2 class="mt-0 text-danger"><?php echo $message; ?></h2>
						<br/>
					<?php else : ?>
						<h2 class="mt-0 text-success text-info"><?php echo $message; ?></h2>
						<br/>
					<?php 
						endif; 
						endif; 
					?>
					<center><a href="<?php echo base_url("social_accounts/account_import"); ?>"><i class="fa fa-arrow-circle-left"></i> <?php echo $this->lang->line("go back"); ?></a></center>
				</div>
			</div>
		</div>
	</div>
</section>   