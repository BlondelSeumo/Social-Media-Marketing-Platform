<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" <?php if($is_rtl) echo 'dir="rtl"';?>>
<!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?php echo $this->config->item('product_name')." | ".$page_title;?></title>
	<meta name="description" content="">
	<meta name="author" content="<?php echo $this->config->item('institute_address1');?>">

	<!-- Mobile Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Favicon -->
	<link rel="shortcut icon" href="<?php echo base_url();?>assets/img/favicon.png">

    <!--====== STYLESHEETS ======-->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/site_new/css/normalize.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/site_new/css/animate.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/site_new/css/modal-video.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/site_new/css/stellarnav.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/site_new/css/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/site_new/css/slick.css">
    <?php if($is_rtl) 
    { ?>
        <link href="<?php echo base_url();?>assets/site_new/css/bootstrap.rtl.min.css" rel="stylesheet">
        <?php 
    } 
    else 
    { ?>
        <link href="<?php echo base_url();?>assets/site_new/css/bootstrap.min.css" rel="stylesheet">
        <?php
    } ?>
    
    <link href="<?php echo base_url();?>assets/site_new/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/site_new/css/material-icons.css" rel="stylesheet">

    <!--====== MAIN STYLESHEETS ======-->
    <?php include("application/views/site/modern/css/style.php"); ?>
    <link href="<?php echo base_url();?>assets/site_new/css/responsive.css" rel="stylesheet">

    <?php include(APPPATH."views/include/js_variables_front.php");?>

    <script src="<?php echo base_url();?>assets/site_new/js/vendor/modernizr-2.8.3.min.js"></script>
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="home-two" data-spy="scroll" data-target=".mainmenu-area" data-offset="90">

    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!--- PRELOADER -->
    <div class="preeloader">
        <div class="preloader-spinner"></div>
    </div>

    <!--SCROLL TO TOP-->
    <a href="#home" class="scrolltotop"><i class="fa fa-long-arrow-up"></i></a>

    <!--START TOP AREA-->
    <header>
        <div class="header-top-area">
            <!--MAINMENU AREA-->
            <div class="mainmenu-area" id="mainmenu-area">
                <nav class="navbar">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a href="<?php echo base_url();?>" class="navbar-brand"><img class="max_height_45px" src="<?php echo base_url();?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>"></a>
                        </div>
                        <div id="main-nav" class="stellarnav">
                            <div class="search-and-signup-button white pull-right hidden-sm hidden-xs">
                                <a href="<?php echo site_url('home/login'); ?>" class="sign-up"><?php echo $this->lang->line('Login'); ?></a>
                            </div>
                            <ul id="nav" class="nav">
                                <li class="active">
                                    <a href="<?php echo base_url('#home');?>"><?php echo $this->lang->line('home'); ?></a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('#feature');?>"><?php echo $this->lang->line('Features');?></a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('#pricing');?>"><?php echo $this->lang->line('Pricing'); ?></a>
                                </li>
                                <li <?php if($this->config->item('display_video_block') == '0') echo "class='hidden'"; ?>>
                                    <a href="<?php echo base_url('#tutorial');?>"><?php echo $this->lang->line('Tutorial');?></a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('#contact');?>"><?php echo $this->lang->line('Contact'); ?></a>
                                </li>
                                <li class="hidden-md hidden-lg">
                                    <a href="<?php echo site_url('home/login'); ?>"><?php echo $this->lang->line('Login'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <!--END MAINMENU AREA END-->
        </div>
        
    </header>
    <!--END TOP AREA-->




    <!--ABOUT AREA-->
    <section class="about-area section-padding" id="app">
        <div class="container">
            <div class="row flex-v-center">
                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="about-content sm-mb50 sm-center text-justify padding_top_80px">
                        <?php $this->load->view($body);  ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--ABOUT AREA END-->

  

  
    <!--FOOER AREA-->
    <footer class="footer-area white relative">
        <div class="area-bg"></div>
        <div class="footer-bottom-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="footer-copyright text-center wow fadeIn">
                            <p>
                            	<?php echo $this->config->item("product_short_name"); ?> &copy; <a target="_blank" href="<?php echo site_url(); ?>"><?php echo $this->config->item("institute_address1"); ?></a></p>
                        	<p class="text-center font_size_10px">
								<a href="<?php echo base_url('home/privacy_policy'); ?>" target="_blank"><?php echo $this->lang->line("Privacy Policy"); ?></a> | <a href="<?php echo base_url('home/terms_use'); ?>" target="_blank"><?php echo $this->lang->line("Terms of Service"); ?></a> | <a href="<?php echo base_url('home/gdpr'); ?>" target="_blank"><?php echo $this->lang->line("GDPR Compliant"); ?></a>
							</p>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </footer>



    <!--====== SCRIPTS JS ======-->
    <script src="<?php echo base_url('assets/site_new/js/vendor/jquery-1.12.4.min.js');?>"></script>
    <script src="<?php echo base_url('assets/site_new/js/vendor/bootstrap.min.js');?>"></script>

    <!--====== PLUGINS JS ======-->
    <script src="<?php echo base_url('assets/site_new/js/vendor/jquery.easing.1.3.js');?>"></script>
    <script src="<?php echo base_url('assets/site_new/js/vendor/jquery-migrate-1.2.1.min.js');?>"></script>
    <script src="<?php echo base_url('assets/site_new/js/vendor/jquery.appear.js');?>"></script>
    <script src="<?php echo base_url('assets/site_new/js/owl.carousel.min.js');?>"></script>
    <script src="<?php echo base_url('assets/site_new/js/slick.min.js');?>"></script>
    <script src="<?php echo base_url('assets/site_new/js/stellar.js');?>"></script>
    <script src="<?php echo base_url('');?>assets/site_new/js/wow.min.js"></script>
    <script src="<?php echo base_url('assets/site_new/js/jquery-modal-video.min.js');?>"></script>
    <script src="<?php echo base_url('assets/site_new/js/stellarnav.min.js');?>"></script>
    <script src="<?php echo base_url('assets/site_new/js/contact-form.js');?>"></script>
    <script src="<?php echo base_url('');?>assets/site_new/js/jquery.ajaxchimp.js"></script>
    <script src="<?php echo base_url('assets/site_new/js/jquery.sticky.js');?>"></script>

    <!--===== ACTIVE JS=====-->
    <script src="<?php echo base_url();?>assets/site_new/js/main.js"></script>

    <!-- cookiealert section -->

    <?php $this->load->view("include/fb_px"); ?> 
    <?php $this->load->view("include/google_code"); ?> 
    
</body>
</html>

<link rel="stylesheet" href="<?php echo base_url('assets/css/system/theme_front.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/inline.css');?>">