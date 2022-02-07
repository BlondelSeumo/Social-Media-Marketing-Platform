<style>
	.card {box-shadow: none !important;}
	.data-div {margin-left: 45px;}
	.margin-top {margin-top: 30px;}
	.flex-column .nav-item .nav-link.active
	{
	  background: #fff !important;
	  color: #3516df !important;
	  border: 1px solid #988be1 !important;
	}

	.flex-column .nav-item .nav-link .form_id, .flex-column .nav-item .nav-link .insert_date
	{
	  color: #608683 !important;
	  font-size: 12px !important;
	  padding: 0 !important;
	  margin: 0 !important;
	}
	.waiting {height: 100%;width:100%;display: table;}
  	.waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}
</style>

<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fab fa-wordpress"></i> <?php echo $this->lang->line("Wordpress Settings (Self-Hosted)"); ?></h1>
		<div class="section-header-button">
	     	<a class="btn btn-primary" href="<?= base_url('social_apps/add_wordpress_self_hosted_settings') ?>">
	        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Add New Site'); ?></a>
	    </div>

	    <div class="section-header-breadcrumb">
	      <div class="breadcrumb-item active">
	      	<a href="<?php echo base_url('integration'); ?>"><?php echo $this->lang->line("Integration"); ?></a>
	      </div>
	      <div class="breadcrumb-item active"><?php echo $page_title; ?></div>
	    </div>

	</div>
	<div class="section-body">
		<div class="row">
			<div class="col-12">

				<?php if ($this->session->userdata('edit_wssh_success')): ?>
				<div class="alert alert-success alert-dismissible show fade">
					<div class="alert-body text-center">
						<button class="close" data-dismiss="alert">
							<span>×</span>
						</button>
						<?php echo $this->session->userdata('edit_wssh_success'); ?>
					</div>
				</div>
				<?php $this->session->unset_userdata('edit_wssh_success'); ?>
				<?php endif; ?>

				<div class="card">
					<div class="card-header d-flex justify-content-between">
						<p><?php echo $this->lang->line("Make sure the REST API is NOT disabled of your wordpress blog."); ?></p>
						<a class="btn btn-primary" href="<?php echo base_url('assets/wordpress-self-hosted/wp-self-hosted-poster.zip'); ?>"><i class="fa fa-download"></i> <?php echo $this->lang->line('Download API Plugin'); ?></a>
					</div>
					<div class="card-body data-card">
						<div class="table-responsive">
							<table id="wssh-datatable" class="table table-bordered" style="width:100%">
						        <thead>
						            <tr>
						                <th>#</th>
						                <th><?php echo $this->lang->line('Domain Name'); ?></th>
						                <th><?php echo $this->lang->line('User Key'); ?></th>
						                <th><?php echo $this->lang->line('Authentication Key'); ?></th>
						                <th><?php echo $this->lang->line('Status'); ?></th>
						                <th><?php echo $this->lang->line('Actions'); ?></th>
						            </tr>
						        </thead>
						    </table>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	$(document).ready(function() {
		var base_url = '<?php echo base_url(); ?>';

		var wssh_table = $('#wssh-datatable').DataTable({
	      	processing: true,
	      	serverSide: true,
			order: [[ 0, "desc" ]],
			pageLength: 10,	        
	        ajax: {
	        	url: '<?= base_url('social_apps/wordpress_self_hosted_settings_data') ?>',
	        	type: 'POST',
	        	dataSrc: function (json) {
	                $(".table-responsive").niceScroll();
	                return json.data;
	            },
	        },
	        columns: [
			    {data: 'id'},
			    {data: 'domain_name'},
			    {data: 'user_key'},
			    {data: 'authentication_key'},
			    {data: 'status'},
			    {data: 'actions'}
			],
			language: {
        		url: "<?= base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
  			},
      		columnDefs: [
				{ 
					'sortable': false, 
					'targets': [2,3,4,5]
				},
				{
				    targets: [0,1,2,3,4,5],
				    className: 'text-center'
				}
			],
			dom: '<"top"f>rt<"bottom"lip><"clear">',
		});

		// Loads categories
		$(document).on('click', '.update-categories', function(e) {
			e.preventDefault();

			var that = this;
			var wp_app_id = $(that).data('wp-app-id');
			
			// Handles spinner
			$(that).removeClass('btn-outline-primary');
			$(that).addClass('btn-primary btn-progress');

			$.ajax({
				type: 'POST',
				dataType: 'JSON',
				data: { wp_app_id },
				url: base_url + 'social_apps/wordpress_self_hosted_settings_load_categories',
				success: function(res) {

					// Handles spinner
					$(that).addClass('btn-outline-primary');
					$(that).removeClass('btn-primary btn-progress');

					if (false === res.status) {
						swal('<?php echo $this->lang->line("Error"); ?>', res.message, 'error');
						return;
					}

					if (true === res.status) {
						swal('<?php echo $this->lang->line("Success"); ?>', res.message, 'success');
					}
				},
			});
		});

		// Attempts to delete wordpress site's settings
		$(document).on('click', '#delete-wssh-settings', function(e) {
			e.preventDefault()

			// Grabs site ID
			var site_id = $(this).data('site-id');
			var csrf_token = $(this).attr('csrf_token');

			swal({
				title: '<?php ('Are you sure?'); ?>',
				text: '<?php echo $this->lang->line('Once deleted, you will not be able to recover this wordpress site settings!'); ?>',
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			}).then((yes) => {
				if (yes) {
					$.ajax({
						type: 'POST',
						url: '<?php echo base_url('social_apps/delete_wordpress_self_hosted_settings') ?>',
						dataType: 'JSON',
						data: { site_id:site_id,csrf_token:csrf_token },
						success: function(res) {
							
							if ('ok' == res.status) {
								swal('<?php echo $this->lang->line("Success"); ?>', res.message, 'success').then((value) => {
								    location.reload();
								});
							} 
							else swal('<?php echo $this->lang->line("Error"); ?>', res.error, 'error');
							
						
						}
					})
				} else {
					return
				}
			});
		});
	});
</script>