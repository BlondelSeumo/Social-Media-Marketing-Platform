<!DOCTYPE html>
<html lang="en">
	<head>
	  <meta charset="UTF-8">
	  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
		<?php 
		include(FCPATH.'application/views/include/css_include_back.php'); 
		include(FCPATH.'application/views/include/js_include_back.php'); 
		?>

		<script src="<?php echo base_url('assets/js/system/theme_iframe.js');?>"></script>
		<link rel="stylesheet" href="<?php echo base_url('assets/css/system/theme_iframe.css');?>">

	</head>
	<body>
		<div class="text-center preloading_body">
		  <i class="fas fa-spinner fa-spin blue text-center"></i>
		</div>
		<div id="theme_iframe_container"> 
			<?php $this->load->view($body); ?>
		</div>
	</body>
</html>
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/inline.css');?>">