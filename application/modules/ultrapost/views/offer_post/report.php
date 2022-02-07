
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 own">
			<center><strong class="text-warning" style="font-family:'Cooper Black'; font-size:20px"><i class="fa fa-list"></i> <?php echo $this->lang->line('offer report');?></strong></center>
		</div>
	</div>

	<br/>
	<div class="row">
		<?php 
			$post=$xdata_campaign;
	        foreach ($post as $key => $value) 
	        {
	           $$key=$value;
	        }
	        $post=$xdata_campaign_view;
	        foreach ($post as $key => $value) 
	        {
	           $temp='new_'.$key;
	           $$temp=$value;
	        }
	        $post_url=$new_post_url;
			$offer_name=$new_offer_name;
		 ?>
			 
		<div class="well col-xs-12 col-md-10 col-md-offset-1" style="background: #fff">
			<h4 class="text-center"><?php echo $this->lang->line('Offer Name'); ?> : <?php echo $offer_name; ?></h4>
			<?php 
			if($posting_status==='2') echo '<center><span class="label label-success"><i class="fa fa-check"></i> '. $this->lang->line('Completed') .'</span></center>';
			if($posting_status==='0') echo '<center><span class="label label-danger"><i class="fa fa-clock-o"></i> '. $this->lang->line('Pending') .'</span></center>';
			if($posting_status==='1') echo '<center><span class="label label-warning"><i class="fa fa-spinner"></i> '. $this->lang->line('Processing') .'</span></center>';
			?>
		</div>	
		<div class="col-xs-12 col-md-10 col-md-offset-1">		
			<div>
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#media" aria-controls="media" role="tab" data-toggle="tab"><i class="fa fa-photo"></i> <?php echo $this->lang->line('Media & Preview');?></a></li>
					<li role="presentation"><a href="#offer_details" aria-controls="offer_details" role="tab" data-toggle="tab"><i class="fa fa-gift"></i> <?php echo $this->lang->line('Offer Details');?></a></li>
					
					
				</ul>
				

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="media">
						<div class="row">
							<div class="col-xs-12 col-md-8 col-md-offset-2">
								<?php 								
							       
							        $post_url=str_replace('web.', 'www.', $post_url);
							        $post_url_embed=urlencode($post_url);

							        if($post_url!="")
							        echo '<h3>'. $this->lang->line('Live Preview') .'</h3><iframe src="https://www.facebook.com/plugins/post.php?href='.$post_url_embed.'" width="100%" height="600" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe><br>';
							    	
							    	echo "<h3>". $this->lang->line('Media File(s)') ."</h3>";
							    	foreach ($xupload_data as $key => $value) 
						    		{
						    			$file_location=$value['file_location'];
						    			$thumbnail_location=$value['thumbnail_location'];
						    			$upload_type=$value['upload_type'];

						    			if($upload_type=='image') echo "<a target='_BLANK' href='".$file_location."'><img src='".$file_location."' style='width:150px;height:100px;margin:3px;border:1px solid #ccc;'></a>";
						    			else echo '<video controls="" width="100%" poster="'.$thumbnail_location.'" style="border:1px solid #ccc"><source src="'.$file_location.'"></source></video>';
						    		}
							    	
								 ?>	
						
							</div>
						</div>
					</div>

					<div role="tabpanel" class="tab-pane" id="offer_details">
						<div class="row">
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Offer Name'); ?></p>
									<footer><?php echo $offer_name; ?></footer>
								</blockquote>
							</div>
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Offer Type'); ?></p>
									<footer><?php echo ucwords(str_replace('_', ' ', $type_offer)); ?></footer>
								</blockquote>
							</div>
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Expiration Time'); ?></p>
									<footer><?php echo date('M d Y , H:i:s',strtotime($expiration_time)).' ('.$expiry_time_zone.')'; ?></footer>
								</blockquote>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Published in'); ?></p>
									<footer><?php echo $page_or_group_or_user_name; ?></footer>
								</blockquote>
							</div>
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Offer Location'); ?></p>
									<footer><?php echo ucwords(str_replace('_', ' ', $location_type)); ?></footer>
								</blockquote>
							</div>							
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Offer Link'); ?></p>
									<footer><?php echo "<a href='".$link."'>$link</a>" ?></footer>
								</blockquote>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Discount Title'); ?></p>
									<footer><?php echo $discount_title; ?></footer>
								</blockquote>
							</div>
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Discount Value'); ?></p>
									<footer><?php echo $discount_value; ?></footer>
								</blockquote>
							</div>							
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Currency'); ?></p>
									<footer><?php echo $currency; if($currency=="") echo $this->lang->line('N/A');?></footer>
								</blockquote>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Schedule Type'); ?></p>
									<footer><?php echo $schedule_type; ?></footer>
								</blockquote>
							</div>
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Schedule Time'); ?></p>
									<footer><?php if($schedule_type=="later") echo date("M d Y, H:i:s",strtotime($schedule_time)).' ('.$time_zone.") "; else echo "N/A"; ?></footer>
								</blockquote>
							</div>							
							<div class="col-xs-12 col-md-4">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Last Updated at'); ?></p>
									<footer><?php echo date("M d Y, H:i:s",strtotime($last_updated_at)); ?></footer>
								</blockquote>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Post Content'); ?></p>
									<footer><?php echo nl2br($message); ?></footer>
								</blockquote>
							</div>							
						</div>
						<div class="row">
							<div class="col-xs-12">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Offer Details'); ?></p>
									<footer><?php echo nl2br($offer_details); ?></footer>
								</blockquote>
							</div>							
						</div>
						<div class="row">
							<div class="col-xs-12">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Terms & Condition'); ?></p>
									<footer><?php echo nl2br($terms_condition); ?></footer>
								</blockquote>
							</div>							
						</div>
						<div class="row">
							<div class="col-xs-12">
								<blockquote>
									<p><i class="fa fa-circle"></i> <?php echo $this->lang->line('Error Message'); ?></p>
									<footer><?php echo $error_message; ?></footer>
								</blockquote>
							</div>							
						</div>
					</div>
					
				</div>		
			</div>	
		</div>
	</div>
</div>

