<?php 
	include("application/modules/blog/views/blog_style.php"); 
?>

<div class="container-fluid" style="padding-bottom: 50px;">
	<div class="col-xs-12" >
		<div class="left-content text-center">
			<h1><span style="color:<?php echo $THEMECOLORCODE;?>; font-weight: 600;"><?php echo $this->lang->line("Our Blogs"); ?></span></h1>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<aside id="sidebar">
				<?php 
                    include("application/modules/blog/views/blog_sidebar.php"); 
                ?>
			</aside><!--/#sidebar-->
		</div><!--/.col-sm-4-->
		<div class="col-xs-12 col-md-9">
			<?php include("application/modules/blog/views/post_display.php") ?>			
		</div><!--/.col-sm-8-->
		
	</div><!--/.row-->
</div><!--/.container-->