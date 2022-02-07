<?php
/*
Theme Name: Modern 
Unique Name: modern
Theme URI: https://chatpion.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description: This is a default theme provided by the Author of ChatPion. We highly recommend not to change core files for your customization needs. For your own customization, create your own theme as per our <a href="https://xeroneit.net/blog/xerochat-front-end-theme-development-manual" target="_BLANK">documentation</a>. 
*/
?>
<!doctype html>
<html class="no-js" lang="en" <?php if($is_rtl) echo 'dir="rtl" style="overflow-x:hidden;"';?>>

<head>
	<meta charset="utf-8">

	<!--====== Title ======-->
	<title><?php echo $this->config->item('product_name'); if($this->config->item('slogan')!='') echo " | ".$this->config->item('slogan')?></title>

	<meta name="description" content="<?php echo $this->config->item('slogan'); ?>">
	<meta name="author" content="<?php echo $this->config->item('institute_address1');?>">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--====== Favicon Icon ======-->
	<link rel="shortcut icon" href="<?php echo base_url();?>assets/img/favicon.png">

	<!--====== Animate CSS ======-->
	<link rel="stylesheet" href="<?php echo base_url('assets/modern/css/animate.css');?>">

	<!--====== Tiny slider CSS ======-->
	<link rel="stylesheet" href="<?php echo base_url('assets/modern/css/tiny-slider.css');?>">

	<!--====== Swiper slider css ======-->
	<link rel="stylesheet" href="<?php echo base_url('assets/modern/css/swiper.min.css');?>">

	<!--====== Glightbox CSS ======-->
	<link rel="stylesheet" href="<?php echo base_url('assets/modern/css/glightbox.min.css');?>">

	<!--====== Line Icons CSS ======-->
	<link rel="stylesheet" href="<?php echo base_url('assets/modern/css/LineIcons.2.0.css');?>">

	<!--====== Bootstrap CSS ======-->

	<?php if($is_rtl) 
	{ ?>
		<link rel="stylesheet" href="<?php echo base_url('assets/modern/css/rtl/bootstrap.rtl.min.css');?>">
		<?php 
	} 
	else 
	{ ?>
		<link rel="stylesheet" href="<?php echo base_url('assets/modern/css/bootstrap-5.0.5-alpha.min.css');?>">
		<?php
	} ?>

	<!--====== Style CSS ======-->
	<link rel="stylesheet" href="<?php echo base_url('assets/modern/css/style.css');?>">

</head>

<body>
	<!--[if IE]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->

	<!--====== PRELOADER PART START ======-->

	<!-- <div class="preloader">
		<div class="loader">
			<div class="ytp-spinner">
				<div class="ytp-spinner-container">
					<div class="ytp-spinner-rotator">
						<div class="ytp-spinner-left">
							<div class="ytp-spinner-circle"></div>
						</div>
						<div class="ytp-spinner-right">
							<div class="ytp-spinner-circle"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> -->

	<!--====== PRELOADER PART ENDS ======-->

	<!--====== HEADER PART START ======-->

	<header class="header_area">
		<div id="header_navbar" class="header_navbar">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<nav class="navbar navbar-expand-lg">
							<a class="navbar-brand" href="">
								<img id="logo" src="<?php echo base_url();?>assets/img/logo.png" alt="Logo">
							</a>
							<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
								aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
								<span class="toggler-icon"></span>
								<span class="toggler-icon"></span>
								<span class="toggler-icon"></span>
							</button>
							<div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
								<ul id="nav" class="navbar-nav ml-auto">
									<li class="nav-item">
										<a class="page-scroll active" href="#home"><?php echo $this->lang->line('Home'); ?></a>
									</li>
									<li class="nav-item">
										<a class="page-scroll" href="#feature"><?php echo $this->lang->line('Features');?></a>
									</li>									
									<li class="nav-item">
										<a class="page-scroll" href="#pricing"><?php echo $this->lang->line('Pricing'); ?></a>
									</li>
									<li <?php if($this->config->item('display_video_block') == '0') echo "class='d-none'"; else echo "class='nav-item'";?>>
	                                    <a class="page-scroll" href="#tutorial"><?php echo $this->lang->line('Tutorial');?></a>
	                                </li>
	                                <?php if ($this->session->userdata('license_type') == 'double')  {?>
	                                <li class="nav-item">
	                                    <a href="<?php echo base_url('blog');?>"><?php echo $this->lang->line('Blog'); ?></a>
	                                </li>
	                                <?php } ?>
									<li class="nav-item">
										<a class="page-scroll" href="#contact"><?php echo $this->lang->line('Contact'); ?></a>
									</li>
									<li class="nav-item">
									    <a class="" href="<?php echo site_url('home/login'); ?>"><?php echo $this->lang->line('Login'); ?></a>
									</li>
								</ul>
							</div> <!-- navbar collapse -->
						</nav> <!-- navbar -->
					</div>
				</div> <!-- row -->
			</div> <!-- container -->
		</div> <!-- header navbar -->
	</header>

	<!--====== HEADER PART ENDS ======-->

	<!--====== HERO PART START ======-->
	<section id="home" class="hero-area bg_cover">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-xl-6 col-lg-6">
					<div class="hero-content">
						<h2 class="wow fadeInUp" data-wow-delay=".2s"><?php echo $this->config->item('product_name'); ?></h2>
						<h3 class="wow fadeInUp" data-wow-delay=".2s"> <?php echo $this->lang->line("Developed using Facebook official API"); ?></h3><br>
						<p class="wow fadeInUp" data-wow-delay=".4s"><?php echo $this->lang->line("Revolutionary, world's very first, and complete marketing software for Facebook & Other Social Medias developed using official APIs."); ?></p>
						<div class="hero-btns">
							<a href="<?php echo site_url('home/sign_up'); ?>" class="main-btn btn-hover wow fadeInUp <?php if($this->config->item('enable_signup_form') =='0') echo "d-none"; ?>" data-wow-delay=".45s"><?php echo $this->lang->line("Sign up now"); ?></a>
							<?php 
							    $promo_video = $this->config->item('promo_video');
							    $video_source = videoType($promo_video);
							 ?>
							<a href="#" class="ms-2 watch-btn glightbox wow fadeInUp <?php if($this->config->item('display_video_block') == '0' || $this->config->item('promo_video') == '') echo 'd-none';?>" data-wow-delay=".5s"> <i class="lni lni-play"></i> <span><?php echo $this->lang->line("Quick Video"); ?></span></a>
						</div>
					</div>
				</div>
				<div class="col-xl-6 col-lg-6">
					<div class="hero-img">
						<img src="<?php echo base_url();?>assets/modern/images/features-image-2.png" alt="" class="wow fadeInRight" data-wow-delay=".2s">
						<img src="<?php echo base_url();?>assets/modern/images/features-image-1.png" alt="" class="img-screen screen-1 wow fadeInUp" data-wow-delay=".25s">
						<img src="<?php echo base_url();?>assets/modern/images/features-image-2.png" alt="" class="img-screen screen-2 wow fadeInUp" data-wow-delay=".3s">
						<img src="<?php echo base_url();?>assets/modern/images/features-image-2.png" alt="" class="img-screen screen-3 wow fadeInUp" data-wow-delay=".35s">
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--====== HERO PART END ======-->

	<!--====== FEATURE PART START ======-->
	<section id="feature" class="feature-area pt-120">
		<div class="container">
			<div class="section-title">
				<h3 class="mb-60 wow fadeInUp" data-wow-delay=".2s"><?php echo $this->lang->line("Awesome Features"); ?></h3>
			</div>
			<div class="row">
				<div class="col-xl-3 col-lg-3 col-md-6">
					<div class="single-feature item-1 wow fadeInUp" data-wow-delay=".2s">
						<div class="feature-icon icon-style gradient-1">
							<i class="lni lni-facebook-messenger"></i>
						</div>
						<div class="feature-content">
							<h4><?php echo $this->lang->line("Messenger Chatbot"); ?></h4>
							<p><?php echo $this->lang->line("Setup Messenger bot for replying 24/7 with Visual Flow Builder."); ?></p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-md-6">
					<div class="single-feature item-2 wow fadeInUp" data-wow-delay=".4s">
						<div class="feature-icon icon-style gradient-2">
							<i class="lni lni-wechat"></i>
						</div>
						<div class="feature-content">
							<h4><?php echo $this->lang->line("Live Chat"); ?></h4>
							<p><?php echo $this->lang->line("Live Chat with Facebook/Instagram subscribers"); ?></p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-md-6">
					<div class="single-feature item-3 wow fadeInUp" data-wow-delay=".6s">
						<div class="feature-icon icon-style gradient-3">
							<i class="lni lni-reply"></i>
						</div>
						<div class="feature-content">
							<h4><?php echo $this->lang->line("Comment Reply"); ?></h4>
							<p><?php echo $this->lang->line("Template, hide/delete offensive comment, keyword based reply, generic reply to facebook pages posts comment."); ?></p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-md-6">
					<div class="single-feature item-4 wow fadeInUp" data-wow-delay=".8s">
						<div class="feature-icon icon-style gradient-4">
							<i class="lni lni-tag"></i>
						</div>
						<div class="feature-content">
							<h4><?php echo $this->lang->line("Social Poster"); ?></h4>
							<p><?php echo $this->lang->line("Instant/Schedule posting on Social Medias."); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--====== FEATURE PART ENDS ======-->

	<!--====== HOW-WORK PART START ======-->
	<section id="how-work" class="how-work-area pt-130">
		<div class="container">
			<div class="row">
				<div class="col-xl-5 col-lg-6">
					<div class="how-work-img text-center text-lg-left">
						<img src="<?php echo base_url();?>assets/modern/images/download-img.png" alt="" class="w-100 wow fadeInLeft img-fluid" data-wow-delay=".2s">
						<img src="<?php echo base_url();?>assets/modern/images/dots-shape.svg" alt="" class="shape dots-shape wow fadeInUp" data-wow-delay=".3s">
					</div>
				</div>
				<div class="col-xl-6 offset-xl-1 col-lg-6">
					<div class="how-work-content-wrapper">
						<div class="section-title">
							<h3 class="mb-45 wow fadeInUp" data-wow-delay=".2s"><?php echo $this->lang->line("How does this App Work?"); ?></h3>
							<p class="mb-35 wow fadeInUp" data-wow-delay=".3s"><?php echo $this->lang->line("Few steps to connect your Facebook & Instagram account and make this app work.");?></p>
						</div>
						<div class="how-work-accordion accordion-style">
							<div class="accordion" id="accordionExample">
								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".2s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
											<span class="d-inline-flex mb-0 icon-style gradient-4">
												<i class="lni lni-facebook-original"></i>
											</span>
											<span><?php echo $this->lang->line("Connect Social account"); ?></span>
										</button>
									</div>

									<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
										<div class="accordion-content">
											<?php echo $this->lang->line("Connect Facebook account is just few clicks easy. Clicking 'Login with Facebook' button will prompt you to allow access to import your facebook & instagram account and enable bot for page and you are ready to go."); ?>
										</div>
									</div>
								</div>
								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".3s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
											<span class="d-inline-flex mb-0 icon-style gradient-1">
												<i class="lni lni-comments-alt"></i>
											</span>
											<span><?php echo $this->lang->line("Create Comment"); ?></span>
										</button>
									</div>

									<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
										<div class="accordion-content">
											<?php echo $this->lang->line("You can comment your own post one time or multiple times. You can save your comments as template and use it whenever you want. Perodic posting feature will allow you to comment randomly or serially taking content from template in a frequent manner and start-end time interval."); ?>
										</div>
									</div>
								</div>
								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".4s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
											<span class="d-inline-flex mb-0 icon-style gradient-3">
												<i class="lni lni-reply"></i>
											</span>
											<span><?php echo $this->lang->line("Create Comment Reply"); ?></span>
										</button>
									</div>

									<div id="collapseThree" class="collapse" aria-labelledby="headingThree"
										data-parent="#accordionExample">
										<div class="accordion-content">
											<?php echo $this->lang->line("Reply automatically your facebook post based on comment content. You can also hide/delete any offensive comments. You can save your replies as template and use it whenever you want."); ?>
										</div>
									</div>
								</div>

								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".5s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
											<span class="d-inline-flex mb-0 icon-style gradient-1">
												<i class="lni lni-facebook-messenger"></i>
											</span>
											<span><?php echo $this->lang->line("Create Messenger Bot"); ?></span>
										</button>
									</div>

									<div id="collapseFour" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
										<div class="accordion-content">
											<?php echo $this->lang->line("You can set messenger bot with Visual flow builder beside classic builder, So your messenger will work 24/7 automatically."); ?>
										</div>
									</div>
								</div>
								
								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".6s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
											<span class="d-inline-flex mb-0 icon-style gradient-2">
												<i class="lni lni-telegram-original"></i>
											</span>
											<span><?php echo $this->lang->line("Create Posting Campaign"); ?></span>
										</button>
									</div>

									<div id="collapseFive" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
										<div class="accordion-content">
											<?php echo $this->lang->line("Just create text/image/video/link content you want to post. Now post it or schedule it to post later. You can also set scheduled periodic post to post same content periodically."); ?>
										</div>
									</div>
								</div>
								
							</div>
						</div>
						<div class="download-btn wow fadeInUp" data-wow-delay=".5s">
							<a href="<?php echo site_url('home/sign_up'); ?>" class="main-btn btn-hover <?php if($this->config->item('enable_signup_form') =='0') echo "d-none"; ?>" data-wow-delay=".45s"><?php echo $this->lang->line("See Features in Action"); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--====== HOW-WORK PART ENDS ======-->

	<!--====== VIDEO PART START ======-->
	<section id="tutorial" class="video-area pt-130 <?php if($this->config->item('display_video_block') == '0' || $this->config->item('promo_video') == '') echo 'd-none';?>">
		<div class="container">
			<div class="video-header bg_cover">
				<div class="section-title text-center">
					<h3 class="mb-60 wow fadeInUp" data-wow-delay=".2s"><?php echo $this->lang->line("Watch a Quick Video"); ?></h3>
				</div>
			</div>
			<div class="video-frame wow fadeInUp" data-wow-delay=".4s">
				<img src="<?php echo base_url();?>assets/modern/images/video-frame.png" alt="">
				<a href="#" class="btn-hover video-btn glightbox"><i class="lni lni-play"></i></a>
			</div>

			    <div class="row">
			    	<?php 
			        $custom_videos = $this->config->item('custom_video'); 
					foreach($custom_videos as $customVideo) : 
			            $original_video = $customVideo[0];
			            $baseurl        = base_url();

			            if (substr($original_video,0,4) != 'http') {
			                $thumb = $baseurl.$original_video;
			            } else {
			                $thumb = $original_video;
			            }
			        ?>
			        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
			        	<div class="card mb-2">
			        	  <a target="_blank" href="<?php echo $customVideo[2]; ?>"><img class="card-img-top" src="<?php echo $thumb;?>" alt=""></a>
			        	  <div class="card-body">
			        	    <h6 class="card-title text-center mb-0">
			        	    	<a target="_blank" href="<?php echo $customVideo[2]; ?>" class="text-dark">
			        	    	<?php 
			        	    		$videotitle = $customVideo[1];
			        	    		if(strlen($videotitle) > 50) {
			        	    			$substring = substr($videotitle,0,48);
			        	    			echo $substring."...";
			        	    		} else {
			        	    			echo $videotitle;
			        	    		}
			        	    	?>
			        	    	</a>
			        	    </h6>
			        	  </div>
			        	</div>
			        </div>
					<?php endforeach; ?>
			    </div>
		</div>
	</section>
	<!--====== VIDEO PART ENDS ======-->

	<!--====== SCREENSHOT PART START ======-->
	<section id="screenshots" class="screenshot-area-wrapper pt-50">
		<div class="screenshot-area pt-90 pb-90">
			<div class="shapes">
				<img src="<?php echo base_url();?>assets/modern/images/ss-line.svg" alt="" class="line-shape-1 shape">
				<img src="<?php echo base_url();?>assets/modern/images/ss-line.svg" alt="" class="line-shape-2 shape">
			</div>
			<div class="container">
				<div class="row">
					<div class="col-xl-6 col-lg-8 col-md-10 mx-auto">
						<div class="section-title text-center">
							<h3 class="mb-30 wow fadeInUp" data-wow-delay=".2s"><?php echo $this->lang->line("Apps Screenshot"); ?></h3>
							<p class="mb-60 wow fadeInUp" data-wow-delay=".4s"><?php echo $this->lang->line("Here are some screenshots of how it looks. See the amazing shots and enjoy."); ?></p>
						</div>
					</div>
				</div>
				<div class="screenshot-slider-wrapper swiper-container">
					<div class="screenshot-slider swiper-wrapper">
						<div class="single-screen swiper-slide">
							<img src="<?php echo base_url();?>assets/modern/images/screen-1.png" alt="">
						</div>
						<div class="single-screen swiper-slide">
							<img src="<?php echo base_url();?>assets/modern/images/screen-2.png" alt="">
						</div>
						<div class="single-screen swiper-slide">
							<img src="<?php echo base_url();?>assets/modern/images/screen-3.png" alt="">
						</div>
						<div class="single-screen swiper-slide">
							<img src="<?php echo base_url();?>assets/modern/images/screen-4.png" alt="">
						</div>
						<div class="single-screen swiper-slide">
							<img src="<?php echo base_url();?>assets/modern/images/screen-5.png" alt="">
						</div>
					</div>
					<div class="screenshot-frame">
						<img src="<?php echo base_url();?>assets/modern/images/screen-active.png" alt="">
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
		</div>
	</section>
	<!--====== SCREENSHOT PART ENDS ======-->

	<!--====== TESTIMONIAL PART START ======-->
	<section dir="ltr" class="testimonial-area pt-150 <?php if($this->config->item('display_review_block') == '0') echo 'd-none';?>">
		<div class="container">
			<div class="row">
				<div class="col-xl-6 col-lg-8 col-md-10">
					<div class="section-title">
						<h3 class="mb-30 wow fadeInUp" data-wow-delay=".2s"><?php echo $this->lang->line("What's Our Customers Saying");?></h3>						
				</div> <!-- End col -->
			</div> <!-- End row -->

			<div class="row">				
				<div class="col-xl-12 col-lg-12 col-sm-11 col-md-9 mx-auto">
					<div class="testimonial-slider-wrapper">
						<div class="testimonial-slider">
							<?php 
			                	$customerReview = $this->config->item('customer_review');
				                $ct=0;
							    foreach($customerReview as $singleReview) : 
					                $ct++;
					                $original = $singleReview[2];
					                $base     = base_url();
					                if (substr($original, 0, 4) != 'http')   $img = $base.$original;
					                else $img = $original;			                
					            	?>
									<!-- start single testimonial  -->
									<div class="single-testimonial wow fadeInUp" data-wow-delay=".2s">
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="672"
											height="480.754" viewBox="0 0 672 480.754">
											<defs>
												<filter id="Path_2021" x="0" y="0" width="672" height="480.754" filterUnits="userSpaceOnUse">
													<feOffset input="SourceAlpha" />
													<feGaussianBlur stdDeviation="25.5" result="blur" />
													<feFlood flood-color="#a5a5a5" flood-opacity="0.161" />
													<feComposite operator="in" in2="blur" />
													<feComposite in="SourceGraphic" />
												</filter>
											</defs>
											<g transform="matrix(1, 0, 0, 1, 0, 0)" filter="url(#Path_2021)">
												<path id="Path_2021-2" data-name="Path 2021"
													d="M36-50,483,1.212c19.882,0,36,16.508,36,36.872v202.8c0,20.364-16.118,36.872-36,36.872H36c-19.882,0-36-16.508-36-36.872V-13.128C0-33.492,16.118-50,36-50Z"
													transform="translate(76.5 126.5)" fill="#fff" />
											</g>
										</svg>
										<div class="testimonial-header">
											<div class="client-info">
												<div class="client-img">
													<img src="<?php echo $img; ?>" alt="">
												</div>
												<div class="client-details">
													<h6><?php echo $singleReview[0]; ?></h6>
													<span><?php echo $singleReview[1]; ?></span>
												</div>
											</div>
											<div class="client-rating">
												<span><i class="lni lni-star-filled"></i></span>
												<span><i class="lni lni-star-filled"></i></span>
												<span><i class="lni lni-star-filled"></i></span>
												<span><i class="lni lni-star-filled"></i></span>
												<span><i class="lni lni-star-filled"></i></span>
											</div>
										</div>
										<div class="quote">
											<i class="lni lni-quotation gradient-2"></i>
										</div>
										<div class="testimonial-content">
											<p>
												<small>
													<?php 
													    if(strlen($singleReview[3]) > 200 )
													    {
													        $str = substr($singleReview[3],0,180);
													        echo $str.". . ."."<a class='exe' type='button' data-toggle='modal' data-target=#myModal".$ct.">".$this->lang->line('Read More')."</a>";
													    
													    }
													    else echo $str = $singleReview[3];		
													?>
												</small>
											</p>
										</div>
									</div> <!-- end single testimonial  -->
								<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div> <!-- End row -->
		</div> <!-- End container -->
	</section>
	<!--====== TESTIMONIAL PART ENDS ======-->

	
	<!--====== PRICING PART START ======-->
	<?php if(!empty($pricing_table_data)) : ?>
	<section id="pricing" class="pricing-area pt-120">
		<div class="container">
			<div class="row">
				<div class="col-xl-6 col-lg-8 col-md-10 mx-auto">
					<div class="section-title text-center">
						<h3 class="mb-30 wow fadeInUp" data-wow-delay=".2s"><?php echo $this->lang->line("Get in Reasonable Price");?></h3>
						<p class="mb-50 wow fadeInUp"><?php echo $this->lang->line("Complete marketing software for Facebook/Instagram in very reasonable price");?></p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 mx-auto">
					<div class="pricing-nav">
						<div class="row">
							<?php
							$i=0;
							foreach($pricing_table_data as $pack) :    
					            $i++; ?>
								<div class="col-xl-4 col-lg-4 col-md-6">
									<div class="single-price text-center <?php if($pack["highlight"]=='1') echo 'active'; ?> wow fadeInUp" data-wow-delay=".4s">
										<div class="price-icon icon-style mx-auto <?php if($pack["highlight"]=='1') echo 'gradient-2'; else echo 'gradient-1'; ?>">
											<i class="lni lni-money-protection"></i>
										</div>
										<h4 class="package-name mb-20"><?php echo $pack["package_name"]; ?></h4>
										<h4 class="package-price mb-20">
											<?php echo $curency_icon; ?><?php echo $pack["price"]?>
											<sub><?php echo $pack["validity"]?> <?php echo $this->lang->line("days"); ?></sub>
										</h4>
										<ul class="package-details">
										    <?php 
										        $module_ids=$pack["module_ids"];
										        $monthly_limit=json_decode($pack["monthly_limit"],true);
										        $module_names_array=$this->basic->execute_query('SELECT module_name,id FROM modules WHERE FIND_IN_SET(id,"'.$module_ids.'") > 0  ORDER BY module_name ASC');

										        foreach ($module_names_array as $row) : ?>
										        <li>
										            <i class="fas fa-circle"></i>&nbsp;
										            <?php 
										                $limit=0;
										                $limit=$monthly_limit[$row["id"]];
										                if($limit=="0") $limit2="<b>".$this->lang->line("unlimited")."</b>";
										                else $limit2=$limit;
										                if($row["id"]!="1" && $limit!="0") 										                    
										                $limit2="<b>".$limit2."/".$this->lang->line("month")."</b>";
										                echo $this->lang->line($row["module_name"]);

										                echo " : <b>". $limit2."</b>"."<br>";
										            ?>
										        </li>
										    <?php endforeach; ?>
										</ul>
										<a href="<?php echo site_url('home/sign_up'); ?>" class="btn-hover price-btn main-btn <?php if($this->config->item('enable_signup_form') == '0') echo "d-none"; ?>"><?php echo $this->lang->line("Subscribe");?></a>
									</div>
								</div>
							<?php endforeach; ?>

						</div>
					</div>
				</div>
			</div>
			
		</div>
	</section>
	<?php endif; ?>
	<!--====== PRICING PART ENDS ======-->

	<!--====== DOWNLOAD PART START ======-->
	<section class="download-area pt-100 pb-150" <?php if(empty($pricing_table_data)) echo "id='pricing'"; ?>>
		<div class="container">
			<div class="download-wrapper bg_cover">
				<div class="row">
					<div class="col-xl-6 col-lg-6 offset-1 col-11">
						<div class="download-content">
							<div class="section-title">
								<h3 class="mb-30 text-white wow fadeInUp" data-wow-delay=".2s"><?php echo $this->lang->line("Get the greatest app!");?></h3>
								<p class="mb-40 text-white wow fadeInUp" data-wow-delay=".35s"><?php echo $this->lang->line("We provide you trial package, so that you can see awesomeness in action and explore it more.");?></p>
							</div>
							    

							<div class="download-btns">
							    <?php if(isset($default_package[0])) : ?>
							    	<a href="<?php echo site_url('home/sign_up'); ?>" class="btn-hover download-btn mr-4 wow fadeInUp <?php if($this->config->item('enable_signup_form') == '0') echo "d-none"; ?>" data-wow-delay=".45s">
							    		<span class="icon gradient-2"><i class="lni lni-customer"></i></span>
							    		<span class="text"><?php echo $default_package[0]["validity"] ?> <?php echo $this->lang->line("Days")."<b>".$this->lang->line("Free Trial")."</b>"; ?></span>
							    	</a>
								<?php endif; ?>
								<?php if(!empty($pricing_table_data)) : ?>
								<a href="#pricing" class="btn-hover download-btn wow fadeInUp page-scroll" data-wow-delay=".55s">
									<span class="icon gradient-1"><i class="lni lni-money-protection"></i></span>
									<span class="text"><?php echo $this->lang->line("Pricing");?> <b><?php echo $this->lang->line("Choose Plan");?></b></span>
								</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="col-xl-5 align-self-end col-lg-5 col-11 offset-1 offset-lg-0">
						<div class="download-img wow fadeInRight" data-wow-delay=".2s">
							<img src="<?php echo base_url();?>assets/modern/images/cta-right-img.png" alt="" class="img-fluid">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--====== DOWNLOAD PART ENDS ======-->

	<!--====== CONTACT PART START ======-->
	<section id="contact" class="contact-area pt-150">
		<div class="container">
			<div class="row">
				<div class="col-xl-6 col-lg-6">
					<div class="faq-wrapper">
						<h4 class="mb-40 wow fadeInUp text-center" data-wow-delay=".2s"><?php echo $this->lang->line("Frequently Asked Questions"); ?></h4>
						<div class="faq-accordion accordion-style">
							<div class="accordion" id="accordionExample2">
								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".3s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
											<span><?php echo $this->lang->line("Do I need to create Facebook App?"); ?></span>
										</button>
									</div>

									<div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordionExample2">
										<div class="accordion-content">
											<?php echo $this->lang->line("No, you don't need to create any Facebook app. We are covering all the complex stuff and giving you the easiest experience possible. Just import your account and start using the awesome features."); ?>
										</div>
									</div>
								</div>
								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".4s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
											<span><?php echo $this->lang->line("Is there any risk involved with my Instagram?"); ?></span>
										</button>
									</div>

									<div id="collapse5" class="collapse" aria-labelledby="heading5" data-parent="#accordionExample2">
										<div class="accordion-content">
											<?php echo $this->lang->line("Not at all. We are using Instgram official API. Everything is official here. Don't need to worry."); ?>
										</div>
									</div>
								</div>
								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".5s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapse6" aria-expanded="true" aria-controls="collapse6">
											<span><?php echo $this->lang->line("Can I edit images before posting?"); ?></span>
										</button>
									</div>

									<div id="collapse6" class="collapse" aria-labelledby="heading6" data-parent="#accordionExample2">
										<div class="accordion-content">
											<?php echo $this->lang->line("Yes, you can. We have full-featured image editor integrated with the system. You can crop, add text, add shape, add filters etc. before you post images."); ?>
										</div>
									</div>
								</div>
								<div class="single-accordion mb-30 wow fadeInUp" data-wow-delay=".6s">
									<div class="accordion-btn">
										<button class="btn-block text-left collapsed" type="button" data-toggle="collapse"
											data-target="#collapse7" aria-expanded="true" aria-controls="collapse7">
											<span><?php echo $this->lang->line("What if I face issues?"); ?></span>
										</button>
									</div>

									<div id="collapse7" class="collapse" aria-labelledby="heading7" data-parent="#accordionExample2">
										<div class="accordion-content">
											<?php echo $this->lang->line("We are always alert to mitigate any global issue arise. If you still face any issue using the system you can open support tickets and our support team will guide and help you out."); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-6 col-lg-6">
					<div class="contact-form-wrapper">
						<h4 class="mb-40 wow fadeInUp text-center" data-wow-delay=".3s"><?php echo $this->lang->line("Do you have any question?"); ?></h4>
                        <?php 
							if($this->session->userdata('mail_sent') == 1) {
							echo "<div class='alert alert-success text-center'>".$this->lang->line("We have received your email. We will contact you through email as soon as possible.")."</div>";
							$this->session->unset_userdata('mail_sent');
							}
						?>
						<form action="<?php echo site_url("home/email_contact"); ?>" method="post" class="contact-form wow fadeInUp" data-wow-delay=".4s">
							<input type="email" class="mb-0" required id="email" <?php echo set_value("email"); ?> placeholder="<?php echo $this->lang->line("email");?>" name="email">
							<span class="text-danger"><?php echo form_error("email"); ?></span>
							<div class="row">
								<div class="col-8">
									<input type="text" class="mb-0" required id="subject" <?php echo set_value("subject"); ?> placeholder="<?php echo $this->lang->line("message subject");?>" name="subject">		
									 <span class="text-danger"><?php echo form_error("subject"); ?></span>							
								</div>
								<div class="col-4">
									<input  type="number" class="mb-0" step="1" required id="captcha" <?php echo set_value("captcha"); ?> placeholder="<?php echo $contact_num1. "+". $contact_num2." = ?"; ?>" name="captcha">	
									<span class="text-danger">
										<?php if(form_error('captcha')) echo form_error('captcha'); 
										else  
										{ 
											echo $this->session->userdata("contact_captcha_error"); 
											$this->session->unset_userdata("contact_captcha_error"); 
										} 
										?>
									</span>								
								</div>
							</div>
							<textarea class="mb-0" rows="3" required id="message" <?php echo set_value("message"); ?> placeholder="<?php echo $this->lang->line("message");?>" name="message"></textarea>
							<span class="text-danger"><?php echo form_error("message") ?></span>
							<button class="btn-hover btn-block main-btn" type="submit"><?php echo $this->lang->line("Send Message");?></button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--====== CONTACT PART ENDS ======-->



	<!--====== FOOTER PART START ======-->

	
	<?php 
	    $facebook = $this->config->item('facebook');
	    $twitter  = $this->config->item('twitter');
	    $linkedin = $this->config->item('linkedin');
	    $youtube  = $this->config->item('youtube');

	    if($facebook=='' && $twitter=='' && $linkedin=='' && $youtube=='') $cls='d-none';
	?>
	<footer id="footer" class="footer-area bg_cover">
		<div class="container pb-4">
			<div class="row">
				<div class="col-12 col-md-4">
					<div class="footer-widget wow fadeInUp text-center" data-wow-delay=".2s">
						<a href="" class="mb-4 d-block"><img class="logo" src="<?php echo base_url();?>assets/img/logo.png" alt=""> </a>
						<p class="wow fadeInUp" data-wow-delay=".4s"><?php echo $this->lang->line("Revolutionary, world's very first, and complete marketing software for Instagram developed using official APIs."); ?></p>
						
					</div>
				</div>
				<div class="col-12 col-md-4">
					<div class="footer-widget wow fadeInUp text-center" data-wow-delay=".6s">
						<h4><?php echo $this->lang->line("Quick Links"); ?></h4>
						<ul>
							<li><a href="#pricing" class="page-scroll"><?php echo $this->lang->line("Pricing"); ?></a></li>
							<li><a href="<?php echo base_url('home/privacy_policy'); ?>" target="_blank"><?php echo $this->lang->line("Privacy Policy"); ?></a></li>
							<li><a href="<?php echo base_url('home/terms_use'); ?>" target="_blank"><?php echo $this->lang->line("Terms of Service"); ?></a></li>
							<li><a href="<?php echo base_url('home/gdpr'); ?>" target="_blank"><?php echo $this->lang->line("GDPR Compliant"); ?></a></li>	
						</ul>
					</div>
				</div>
				<div class="col-12 col-md-4">
					<div class="footer-widget wow fadeInUp text-center" data-wow-delay=".2s">
						<?php if(!empty($this->config->item('institute_address1'))) echo "<h3 class='mb-4'>".$this->config->item('institute_address1').'</h3>';?>
						<?php if(!empty($this->config->item('institute_address2'))) echo "<i class='lni lni-map-marker'></i> ".$this->config->item('institute_address2').'<br>';?>
						<?php if(!empty($this->config->item('institute_email'))) echo "<i class='lni lni-envelope'></i> ".$this->config->item('institute_email');?>
						<?php if(!empty($this->config->item('institute_mobile'))) echo "<br><i class='lni lni-phone'></i> ".$this->config->item('institute_mobile').'<br>';?>
						
						<ul class="social-links mt-4 text-center d-flex justify-content-center <?php if(isset($cls)) echo $cls; ?>">
							<li <?php if($facebook=='') echo "class='d-none'"; ?>><a target="_BLANK" href="<?php echo $facebook; ?>" class="facebook"><i class="lni lni-facebook-original"></i></a></li>
							<li <?php if($twitter=='') echo "class='d-none'"; ?>><a target="_BLANK" href="<?php echo $twitter; ?>" class="twitter"><i class="lni lni-twitter-original"></i></a></li>
							<li <?php if($linkedin=='') echo "class='d-none'"; ?>><a target="_BLANK" href="<?php echo $linkedin; ?>" class="linkedin"><i class="lni lni-linkedin-original"></i></a></li>
							<li <?php if($youtube=='') echo "class='d-none'"; ?>><a target="_BLANK" href="<?php echo $youtube; ?>" class="instagram"><i class="lni lni-youtube"></i></a></li>
						</ul>
					</div>
				</div>				
			</div>

			<div class="footer-cradit">
				<p class="text-center mb-0"><?php echo $this->lang->line("Copyright"); ?> &copy; <a target="_blank" href="<?php echo site_url(); ?>"><?php echo $this->config->item("institute_address1"); ?></a></p>
			</div>
		</div>
	</footer>
	<?php if($this->session->userdata('allow_cookie')!='yes') : ?>
		
	    <div class="text-center cookiealert">
	        <div class="cookiealert-container py-3">
	            <a class="cookie_content_css" href="<?php echo base_url('home/privacy_policy#cookie_policy');?>">
	                <?php echo $this->lang->line("This site requires cookies in order for us to provide proper service to you.");?>
	            </a>
	            <a type="button" href="#" class="btn btn-warning btn-sm acceptcookies black_color" aria-label="Close">
	                <?php echo $this->lang->line("Got it !"); ?>
	            </a>

	        </div>
	    </div>
	<?php endif; ?> <!--====== FOOTER PART ENDS ======-->

	<!--====== BACK TOP TOP PART START ======-->
	<a href="#" class="back-to-top btn-hover"><i class="lni lni-chevron-up"></i></a>
	<!--====== BACK TOP TOP PART ENDS ======-->


	<script type="text/javascript">
		"use strict";
		var promo_video = "<?php echo $promo_video;?>";
		var video_source = "<?php echo $video_source;?>";
		var base_url = "<?php echo base_url();?>";
	</script>

	<!--====== jQuery js ======-->
	<script src="<?php echo base_url('assets/modern/js/jquery-1.12.4.min.js');?>"></script>

	<!--====== Bootstrap js ======-->
	<script src="<?php echo base_url('assets/modern/js/bootstrap.bundle-5.0.0.alpha-min.js');?>"></script>

	<!--====== Tiny slider js ======-->
	<script src="<?php echo base_url('assets/modern/js/tiny-slider.js');?>"></script>

	<!--====== Swiper slider js ======-->
	<script src="<?php echo base_url('assets/modern/js/swiper.min.js');?>"></script>

	<!--====== glightbox js ======-->
	<script src="<?php echo base_url('assets/modern/js/glightbox.min.js');?>"></script>

	<!--====== wow js ======-->
	<script src="<?php echo base_url('assets/modern/js/wow.min.js');?>"></script>

	<!--====== count-up js ======-->
	<script src="<?php echo base_url('assets/modern/js/count-up.min.js');?>"></script>

	<!--====== contact form js ======-->
	<script src="<?php echo base_url('assets/modern/js/contact-form.js');?>"></script>

	<!--====== Main js ======-->
	<script src="<?php echo base_url('assets/modern/js/main.js');?>"></script>

	<script src="<?php echo base_url('assets/js/system/site_default.js');?>"></script>

	<?php $this->load->view("include/fb_px"); ?> 
    <?php $this->load->view("include/google_code"); ?> 

    <?php if($is_rtl) { ?>
    	<style type="text/css">
    		.hero-area .hero-img .img-screen.screen-2 {
    		  bottom: 60px;
    		  left: -220px !important;
    		}

    		@media only screen and (min-width: 1200px) and (max-width: 1399px) {
    		  .hero-area .hero-img .img-screen.screen-2 {
    		    bottom: 180px;
    		    left: -276px !important;
    		}

    		@media only screen and (min-width: 992px) and (max-width: 1199px) {
    		  .hero-area .hero-img .img-screen.screen-2 {
    		    bottom: 180px;
    		    left: -276px !important;
    		  }
    		}
    	</style>
    <?php } ?>

    <?php if(!$is_rtl) { ?>
    	<style type="text/css">
    		.hero-area .hero-img .img-screen.screen-2 {
    		  bottom: 60px;
    		  right: -220px;
    		}

    		@media only screen and (min-width: 1200px) and (max-width: 1399px) {
    		  .hero-area .hero-img .img-screen.screen-2 {
    		    bottom: 180px;
    		    right: -276px;
    		  }
    		}

    		@media only screen and (min-width: 992px) and (max-width: 1199px) {
    		  .hero-area .hero-img .img-screen.screen-2 {
    		    bottom: 180px;
    		    right: -276px;
    		  }
    		}
    	</style>
    <?php } ?>

</body>

</html>


<!-- Modal -->
<?php   
    $ct=0;
    foreach($customerReview as $singleReview) : 
        $ct++;
        $original = $singleReview[2];
        $base     = base_url();

        if (substr($original, 0, 4) != 'http') {
            $img = $base.$original;
        } else {
           $img = $original;
        }
	?>
    <div class="modal fade" id="myModal<?php echo $ct; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  		<div class="modal-dialog modal-lg" role="document">
        <!-- Modal content-->
        <div class="modal-content">
        	<div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel"><?php echo $this->lang->line('Full Review'); ?></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		     </div>
            
            <div class="modal-body name-and-designation  mt-2">
            	<div class="single-item mt-1 text-center">
            	    <div class="member-image">
            	        <img class="rounded-circle img-thumbnail" src="<?php echo $img; ?>" alt="reviewer">
            	    </div>
	                <h4 class="mt-2"><?php echo $singleReview[0]; ?></h4>
	                <p><?php echo $singleReview[1]; ?></p><br>
	                <p class="text-small text-justify"><small><?php echo $singleReview[3]; ?></small></p>
                </div>
        	</div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
            </div>
        </div>

      </div>
    </div>
	<?php endforeach;
?>