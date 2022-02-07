<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title><?php echo $this->config->item('product_name')." | ".$page_title;?></title>
  <link rel="shortcut icon" href="<?php echo base_url();?>assets/img/favicon.png">
  <?php if($is_rtl) 
  { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/rtl/bootstrap.min.css">
    <?php 
  } 
  else 
  { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/bootstrap.min.css">
    <?php
  } ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/v4-shims.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/components.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
  <?php if($is_rtl) { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/rtl.css">
  <?php } ?>
  <?php include(APPPATH."views/include/js_variables_front.php");?>
  <script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/modules/sweetalert/sweetalert.min.js"></script>
</head>

<body class="bg-info-light-alt gradient">
  <div id="app">
    <section class="section">
      <?php echo $this->load->view($body); ?>
    </section>
  </div>
</body>

<?php $this->load->view("include/fb_px"); ?> 
<?php $this->load->view("include/google_code"); ?> 
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/inline.css');?>">