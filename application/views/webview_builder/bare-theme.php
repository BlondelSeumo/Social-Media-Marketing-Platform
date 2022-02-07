<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
		<title><?= htmlspecialchars($form['form_title']) ?></title>
		<link rel="shortcut icon" href="<?= base_url();?>assets/img/favicon.png">

		<!-- General CSS Files -->
		<?php if(isset($is_rtl) && $is_rtl==true) 
		{ ?>
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/rtl/bootstrap.min.css">
			<?php 
		} 
		else 
		{ ?>
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/bootstrap.min.css">
			<?php
		} ?>
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/all.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/v4-shims.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-social/bootstrap-social.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-daterangepicker/daterangepicker.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/select2/dist/css/select2.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jquery-selectric/selectric.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/ionicons/css/ionicons.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/izitoast/css/iziToast.min.css">

		<!-- Template CSS -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/components.css">

		<!--Jquey Date Time Picker -->
		<link href="<?php echo base_url();?>plugins/datetimepickerjquery/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />

		<!--Emoji CSS-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>plugins/emoji/dist/emojionearea.min.css" media="screen">

		<!-- Custom -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">

		<?php if(isset($is_rtl) && $is_rtl==true) { ?>
			<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/rtl.css">
		<?php } ?>

		<!-- General JS Scripts -->
		<script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/popper.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/tooltip.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/moment.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/stisla.js"></script>

		<!-- JS Libraies -->
		<script src="<?php echo base_url(); ?>assets/modules/bootstrap-daterangepicker/daterangepicker.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/select2/dist/js/select2.full.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/jquery-selectric/jquery.selectric.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/sweetalert/sweetalert.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/modules/izitoast/js/iziToast.min.js"></script>

		<!--Jquery Date Time Picker -->
		<script type="text/javascript" src="<?php echo base_url();?>plugins/datetimepickerjquery/jquery.datetimepicker.js"></script>

		<!-- Emoji Library-->
		<script src="<?php echo base_url();?>plugins/emoji/dist/emojionearea.js" type="text/javascript"></script>

		<!-- Template JS File -->
		<script src="<?php echo base_url(); ?>assets/js/scripts.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/custom.js"></script>

		<!-- Load Facebook Messenger SDK -->
		
		<script  type="text/javascript">



			var PSID; 

			(function(d, s, id){
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) {return;}
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/messenger.Extensions.js";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'Messenger'));


			window.extAsyncInit = function() {
				
				MessengerExtensions.getContext('<?php echo $fb_app_id; ?>', 
				  function success(thread_context){
				  	 PSID=thread_context.psid;
				  },
				  function error(err){
				   	console.log(err);
				  }
				);

			};



			
		</script>

		<script type="text/javascript">
		  <?php
		  if(isset($is_rtl) && $is_rtl==true) echo 'var is_rtl = true;';
		  else echo 'var is_rtl = false;';
		  ;?>
		</script>

	</head>

	<body>
	  <div id="app">
	    <div class="main-wrapper">
			<div class="container">
				<?php $this->load->view($body) ?>
			</div>
		</div>
	  </div>
	</body>
</html>
