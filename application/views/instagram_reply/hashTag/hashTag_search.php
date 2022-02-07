<link rel="stylesheet" href="<?php echo base_url('assets/css/system/instagram/instagram_comment_reply.css');?>">
<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fab fa-instagram"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><a href="<?php echo base_url("instagram_reply") ?>"><?php echo $this->lang->line("Instagram Reply");?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<div class="section-body">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 ">
						<div class="form-group">
							<div class="form-group">
								<div class="input-group">
									<select name="account_name" id="account_name" class="form-control select2">
										<option value=""><?php echo $this->lang->line('Select Account'); ?></option>
										<?php foreach ($account_lists as $value) {
											echo '<option value="'.$value['id'].'">'.$value['insta_username'].' ['.$value['page_name'].'] '.'</option>';
										} ?>
									</select>
									<input type="text" class="form-control" id="hash_tag" name="hash_tag" placeholder="<?php echo $this->lang->line('Provide hash tag'); ?>">
									<div class="input-group-append">
										<button class="btn btn-primary" id="search_hashtag"><i class="fas fa-search"></i> <?php echo $this->lang->line('Search'); ?></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12 text-center" id="preloader"></div>

					<div class="col-12">
						<div id="hashtag_search_result"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


 <script src="<?php echo base_url('assets/js/system/instagram/hash_tag.js');?>"></script>