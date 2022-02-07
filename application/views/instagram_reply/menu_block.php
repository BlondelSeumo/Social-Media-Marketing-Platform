<section class="section">
	<div class="section-header">
		<h1><i class="fab fa-instagram"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<div class="section-body">
		<div class="row">

			<div class="col-lg-6">
				<div class="card card-large-icons">
					<div class="card-icon text-primary">
						<i class="fas fa-reply-all"></i>
					</div>
					<div class="card-body">
						<h4><?php echo $this->lang->line("Commnet Reply"); ?></h4>
						<p><?php echo $this->lang->line("Full Account Reply, mention reply"); ?></p>
						<a href="<?php echo base_url("instagram_reply/get_account_lists"); ?>" class="card-cta"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="card card-large-icons">
					<div class="card-icon text-primary">
						<i class="fas fa-tags"></i>
					</div>
					<div class="card-body">
						<h4><?php echo $this->lang->line("Hashtag Search"); ?></h4>
						<p><?php echo $this->lang->line("Top & Recent media with hash tag"); ?></p>
						<a href="<?php echo base_url("instagram_reply/hashTag_search"); ?>" class="card-cta"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>