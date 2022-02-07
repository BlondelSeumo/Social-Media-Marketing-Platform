<section class="section">
 <div class="section-header">
   <h1><?php echo $page_icon.' '.$page_title; ?></h1>
 </div>

<div class="section-body">
 	<div class="row">

 		<?php foreach ($comment_growth_tools[$media_type] as $growth_tool) : 
 			if(!$growth_tool['has_access']) continue;
 		?>
 			<div class="col-12 <?php if($media_type=='fb') echo 'col-lg-2'; else echo 'col-lg-3'; ?> col-lg-2">
 				<a href="<?php echo $growth_tool['url']; ?>" class="text-dark action_tag">
 					<div class="wizard-steps mb-3">
 						<div class="wizard-step mx-1 my-0">
 							<div class="wizard-step-icon">
 								<img class="img-fluid" width="<?php if($media_type=='fb') echo '80'; else echo '100'; ?>" src="<?php echo $growth_tool['img_path']; ?>" alt="">
 							</div>
 							<div class="wizard-step-label"><?php echo $this->lang->line($growth_tool['title']); ?></div>
 						</div>
 					</div>
 				</a>
 			</div>
 		<?php endforeach ?>
 	</div>
 </div>
</section>
<style type="text/css">
	.card { height: 90px; }
</style>
<style>
  .action_tag { text-decoration: none !important; }
  .action_tag:hover .wizard-step-label { color: var(--blue) !important; }
  .wizard-steps .wizard-step { padding: 20px;}
  .wizard-steps .wizard-step:before {content: none !important;}
  .wizard-steps .wizard-step .wizard-step-label { font-size: 12px;text-transform:capitalize;letter-spacing:0;margin-top:10px; }
  .social_media_tag:hover .wizard-icons { display: block !importan; }
  /*.wizard-icons { display: none; }*/
</style>
<script>
	$(document).ready(function() {
		var base_url = '<?php echo base_url(); ?>';
		$(document).on('change', '#switch_media', function(event) {
		  event.preventDefault();
		  var switch_media_type = $('input[name=switch_media]:checked').val();
		  if(typeof(switch_media_type) == 'undefined') {
		    switch_media_type = 'ig';
		  }

		  $.ajax({
		  	url: base_url+'home/switch_to_media',
		  	type: 'POST',
		  	data: {media_type: switch_media_type},
		  	success:function(response){
		  		window.location.assign('<?php echo base_url('comment_automation/comment_growth_tools'); ?>');
		  	}
		  });
		});
	});
</script>