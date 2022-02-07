<div class="card">
  <div class="card-header">
    <h4><?php echo $templatedata['template_name'];?> <small>(<?php echo date("j M y H:i",strtotime($templatedata['saved_at'])) ?>)</small></h4>
  </div>

  <div class="card-body">
  	<div class="row">
  		<div class="col-12 col-sm-6 col-md-4" style="margin-bottom: 20px;">
  			<?php if($templatedata['preview_image'] != '' && file_exists('upload/image/'.$templatedata['user_id'].'/'.$templatedata['preview_image'])) { ?>
  				<img class='img-responsive img-thumbnail center-block' width="100%" src="<?php echo base_url('upload/image/'.$templatedata['user_id']."/".$templatedata['preview_image']); ?>">
  			<?php } 
  			else { ?>
  				<img class='img-responsive img-thumbnail center-block' width="100%" src="<?php echo base_url("assets/img/avatar/avatar-1.png");?>">
  			<?php }?>
  		</div>
  		<div class="col-12 col-sm-6 col-md-8 text-justify"><?php echo $templatedata['description']; ?></div>
  	</div>
  </div>

</div>