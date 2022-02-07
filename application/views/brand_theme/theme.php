<!DOCTYPE html>

<html>

<head>

	<title><?php echo $page_title;?></title>

	<meta charset="utf-8">
	<link rel="shortcut icon" href="<?php echo base_url();?>assets/images/favicon.png"> 
	<meta name="description" content="<?php echo $meta_description;?>">

	<meta name="keywords" content="<?php echo $meta_keyword;?>">

	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">

	<link href="<?php echo site_url();?>assets/login/plugins/jquery.ui/smoothness/jquery-ui-1.10.1.custom.css" rel="stylesheet" >

	<link href="<?php echo site_url();?>assets/login/plugins/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

	<link rel="stylesheet" href="<?php echo site_url();?>assets/login/plugins/daterangepicker/daterangepicker-bs3.css">

    <link rel="stylesheet" href="<?php echo site_url();?>assets/login/plugins/simplepicker/jquery.simple-dtpicker.css">

    <link rel="stylesheet" href="<?php echo site_url();?>assets/login/plugins/icheck/icheck.css">

	<link href="<?php echo site_url();?>assets/login/plugins/elfinder/css/elfinder.min.css" rel="stylesheet" >

    <link href="<?php echo site_url();?>assets/login/plugins/elfinder/css/theme.css" rel="stylesheet" >

    <link href="<?php echo site_url();?>assets/login/plugins/elfinder/css/dialog.css" rel="stylesheet" >

	<link href="<?php echo site_url();?>assets/login/css/fonts.css" rel="stylesheet" type="text/css">

	<link href="<?php echo site_url();?>assets/login/css/style.css" rel="stylesheet" type="text/css">

	<script src="<?php echo site_url();?>assets/login/plugins/jquery/jquery.min.js"></script>

</head>

<body>

	<?php $this->load->view("brand_theme/header"); ?>
	<?php $this->load->view($body); ?>




	<!--javascript-->

	<script src="<?php echo site_url();?>assets/login/plugins/bootstrap/bootstrap.min.js"></script>

	<script src="<?php echo site_url();?>assets/login/plugins/highcharts/highcharts.js"></script>

	<script src="<?php echo site_url();?>assets/login/plugins/jquery.ui/jquery.ui.min.js"></script>

	<script src="<?php echo site_url();?>assets/login/plugins/icheck/icheck.min.js"></script>

	<script src="<?php echo site_url();?>assets/login/plugins/elfinder/js/elfinder.full.js"></script>

    <script src="<?php echo site_url();?>assets/login/plugins/elfinder/js/jquery.dialogelfinder.js"></script>

    <script src="<?php echo site_url();?>assets/login/plugins/daterangepicker/moment.min.js"></script>

    <script src="<?php echo site_url();?>assets/login/plugins/daterangepicker/daterangepicker.js"></script>

    <script src="<?php echo site_url();?>assets/login/plugins/simplepicker/jquery.simple-dtpicker.js"></script>

	<script src="<?php echo site_url();?>assets/login/js/instagram.js"></script>

	<script src="<?php echo site_url();?>assets/login/js/main.js"></script>

</body>

</html>