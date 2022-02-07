<?php $this->load->view("include/upload_js"); ?>

<?php 

$xpost_type=isset($xdata_campaign_view['post_type']) ? $xdata_campaign_view['post_type'] : "image_submit";

$xauto_share_post=isset($xdata_campaign_view['auto_share_post']) ? $xdata_campaign_view['auto_share_post'] : "0";

$xauto_share_to_profile=isset($xdata_campaign_view['auto_share_to_profile']) ? $xdata_campaign_view['auto_share_to_profile'] : "0";

$xauto_like_post=isset($xdata_campaign_view['auto_like_post']) ? $xdata_campaign_view['auto_like_post'] : "0";

$xauto_comment_post=isset($xdata_campaign_view['auto_comment_post']) ? $xdata_campaign_view['auto_comment_post'] : "0";

$xtype_offer=isset($xdata_campaign['type_offer']) ? $xdata_campaign['type_offer'] : "percentage_off";

$xlocation_type=isset($xdata_campaign['location_type']) ? $xdata_campaign['location_type'] : "online";



?>

<div class="row <di padding-20" style="padding-top: 5px;">

	<div class="col-xs-12 col-md-7 padding-5">

		<div class="box box-primary">

			<div class="box-header ui-sortable-handle" style="cursor: move;">

				<i class="fa fa-pencil"></i>

				<h3 class="box-title"><?php echo $this->lang->line('Edit'); ?> <?php echo $this->lang->line('Offer Poster'); ?></h3>

				<!-- tools box -->

				<div class="pull-right box-tools"></div><!-- /. tools -->

			</div>

			<div class="box-body padding-20">



				<a id="image_post" class="post_type <?php if($xpost_type=='image_submit') echo 'active';?>"><i class="fa fa-photo"></i> <span class="hidden-xs" title="Image"><?php echo $this->lang->line('Image'); ?></span></a>

				<a id="carousel_post" class="post_type <?php if($xpost_type=='carousel_submit') echo 'active';?>"><i class="fa fa-newspaper-o"></i> <span class="hidden-xs" title="Carousel"><?php echo $this->lang->line('Carousel'); ?></span></a>

				<a id="video_post" class="post_type <?php if($xpost_type=='video_submit') echo 'active';?>"><i class="fa fa-video-camera"></i> <span class="hidden-xs" title="Video"><?php echo $this->lang->line('Video'); ?></span></a>





				<br/><br/><br/><br/>

				<form action="#" enctype="multipart/form-data" id="auto_poster_form" method="post">

					<div class="form-group">

						<label><?php echo $this->lang->line('Offer Name'); ?> *</label>

						<input type="input" class="form-control"  name="campaign_name" value="<?php echo $xdata_campaign_view['offer_name'];?>" id="campaign_name" placeholder="<?php echo $this->lang->line('Type a name to identify it later easily'); ?>">

						<input type="hidden" name="hidden_id" value="<?php echo $xdata_campaign_view['id'];?>" id="hidden_id">

						<input type="hidden" name="hidden_campaign_id" value="<?php echo $xdata_campaign_view['campaign_id'];?>" id="hidden_campaign_id">

					</div>



					<div class="form-group">

						<label><?php echo $this->lang->line('Post Content'); ?></label>

						<textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line('Type your message here...'); ?>"><?php echo $xdata_campaign_view['message'];?></textarea>

					</div>



					<div class="form-group col-xs-12 col-md-6" style="padding:0 5px 0 0">

						<label><?php echo $this->lang->line('Offer Type'); ?> *</label>

						<?php echo form_dropdown('type_offer', $offer_types,$xtype_offer,'class="form-control" id="type_offer"'); ?>

					</div>

				

					<div class="form-group col-xs-12 col-md-6" style="padding:0 0 0 5px">

						<label><?php echo $this->lang->line('Location Type'); ?> *</label>

						<?php echo form_dropdown('location_type', $location_types,$xlocation_type,'class="form-control" id="location_type"'); ?>

					</div>



					<div class="form-group col-xs-12 col-md-4" style="padding:0 5px 0 0">

						<label><?php echo $this->lang->line('Offer Will Expire at'); ?> *</label>

						<input type="input" class="form-control datepicker"  name="expiration_time" id="expiration_time" placeholder="">

					</div>



					<div class="form-group col-xs-12 col-md-4" style="padding:0 0 0 5px">

						<label><?php echo $this->lang->line('Expiry Time Zone'); ?></label>

						<?php

						$time_zone[''] = "--- Select expiry time zone ---";

						echo form_dropdown('expiry_time_zone',$time_zone,$xdata_campaign['expiry_time_zone'],' class="form-control" id="expiry_time_zone" required'); 

						?>

					</div>



					<div class="form-group col-xs-12 col-md-4" style="padding:0 0 0 5px">

						<label><?php echo $this->lang->line('Max. No. of Allowed Saves'); ?></label>

						<input type="number" step="1" class="form-control" value="<?php echo $xdata_campaign['max_save_count'];?>" name="max_save_count" id="max_save_count" placeholder="<?php echo $this->lang->line('Keep it blank if unlimited'); ?>">

					</div>



				

					<div class="form-group col-xs-12 col-md-4" style="padding:0 5px 0 0">

						<label><?php echo $this->lang->line('Discount Value'); ?></label>

						<input type="number" class="form-control"  name="discount_value" value="<?php echo $xdata_campaign['discount_value'];?>" id="discount_value" placeholder="<?php echo $this->lang->line('Bogo or cash or % discunt amount'); ?> *">

					</div>



					<div class="form-group col-xs-12 col-md-4" style="padding:0 0 0 5px">

						<label><?php echo $this->lang->line('Offer/Discount Title'); ?> *</label>

						<input type="input" class="form-control" value="<?php echo $xdata_campaign['discount_title'];?>"  name="discount_title" id="discount_title" placeholder="">

					</div>

					



					<div class="form-group col-xs-12 col-md-4" style="padding:0 0 0 5px">

						<label><?php echo $this->lang->line('Cash Discount Currency'); ?></label>

						<?php $currency_list['']="--- Select cash discount currency --- *"; ?>

						<?php echo form_dropdown('currency', $currency_list,$xdata_campaign['currency'],'class="form-control" id="currency"'); ?>

					</div>					

				



					<div class="only_online">

						<div class="form-group col-xs-12 col-md-6" style="padding:0 5px 0 0">

							<label><?php echo $this->lang->line('Online Coupon Code'); ?></label>

							<input class="form-control" name="online_coupon_code" value="<?php echo $xdata_campaign['online_coupon_code'];?>" id="online_coupon_code"  type="text" placeholder="<?php echo $this->lang->line('Online offer coupon code'); ?>">

						</div>



						<div class="form-group col-xs-12 col-md-6"  style="padding:0 0 0 5px">						

							<label><?php echo $this->lang->line("Get Offer Link"); ?></label>

							<input class="form-control" name="link" id="link" value="<?php echo $xdata_campaign['link'];?>" type="text" placeholder="<?php echo $this->lang->line('Online Offer Page Link'); ?> *">

						</div>

					</div>





					<div class="only_offline">

						<div class="form-group col-xs-12 col-md-4" style="padding:0 5px 0 0">						

							<label><?php echo $this->lang->line('Store Coupon Code'); ?></label>

							<input class="form-control" name="store_coupon_code" value="<?php echo $xdata_campaign['store_coupon_code'];?>" id="store_coupon_code"  type="text" placeholder="<?php echo $this->lang->line('Offline offer coupon code'); ?>">

						</div>



						<div class="form-group col-xs-12 col-md-4" style="padding:0 0 0 5px">

							<label><?php echo $this->lang->line('Barcode Type'); ?></label>

							<?php $barcode_types['']="--- <?php echo $this->lang->line('Select barcode type'); ?> ---"; ?>

							<?php echo form_dropdown('barcode_type', $barcode_types,$xdata_campaign['barcode_type'],'class="form-control" id="barcode_type"'); ?>

						</div>



						<div class="form-group col-xs-12 col-md-4" style="padding:0 0 0 5px">

							<label><?php echo $this->lang->line('Barcode Value'); ?> </label>

							<input type="input" class="form-control"  name="barcode_value" value="<?php echo $xdata_campaign['barcode_value'];?>" id="barcode_value" placeholder="<?php echo $this->lang->line('Text to generate barcode'); ?>">

						</div>

					</div>

	



					<div class="form-group col-xs-12 col-md-6" style="padding:0 5px 0 0">

						<label><?php echo $this->lang->line('Offer Details'); ?> *</label> <i class="pull-right">Char Count : <span id="offer_details_count"><?php strlen($xdata_campaign['offer_details']); ?></span></i>

						<textarea class="form-control" name="offer_details" id="offer_details" placeholder="<?php echo $this->lang->line('Type offer details here... Max 250 characters'); ?>"><?php echo $xdata_campaign['offer_details'];?></textarea>						

					</div>



					<div class="form-group col-xs-12 col-md-6" style="padding:0 0 0 5px">	

						<label><?php echo $this->lang->line('Terms & Condition'); ?></label>

						<textarea class="form-control" name="terms_condition" id="terms_condition" placeholder="<?php echo $this->lang->line('Type terms & condition of offer here...'); ?>"><?php echo $xdata_campaign['terms_condition'];?></textarea>

					</div>



					

					<div class="clearfix"></div>

					<div id="image_block" class="well clearfix">



						<div class="form-group col-xs-12 col-md-6" style="padding:0 5px 0 0">

							<label><?php echo $this->lang->line('Image URL'); ?></label>

							<?php $ximage_url=isset($xupload_data[0]['file_location']) ? $xupload_data[0]['file_location'] : ""; ?>

							<?php if($xpost_type!="image_submit") $ximage_url="";?>

							<input class="form-control" name="image_url" id="image_url" value="<?php echo $ximage_url;?>" type="text" placeholder="<?php echo $this->lang->line('Paste image URL or upload new'); ?>"> 

							<span class="label label-info preview_uploaded" style="cursor:pointer"><i class="fa fa-eye"></i> <?php echo $this->lang->line('Preview'); ?></span>

						</div>

						<div class="form-group col-xs-12 col-md-6" style="padding:0 0 0 5px">

							<label><?php echo $this->lang->line('Upload Image'); ?></label>      

                            <div id="image_url_upload"><?php echo $this->lang->line('Upload');?></div>

						</div>

					</div>





					<div class="clearfix"></div>

					<?php $xcarousel_image_link_1=isset($xupload_data[0]['file_location']) ? $xupload_data[0]['file_location'] : ""; ?>

					<?php $xcarousel_image_link_2=isset($xupload_data[1]['file_location']) ? $xupload_data[1]['file_location'] : ""; ?>

					<?php $xcarousel_image_link_3=isset($xupload_data[2]['file_location']) ? $xupload_data[2]['file_location'] : ""; ?>

					<?php $xcarousel_image_link_4=isset($xupload_data[3]['file_location']) ? $xupload_data[3]['file_location'] : ""; ?>

					<?php $xcarousel_image_link_5=isset($xupload_data[4]['file_location']) ? $xupload_data[4]['file_location'] : ""; ?>

					<?php if($xpost_type!="carousel_submit") $xcarousel_image_link_1=$xcarousel_image_link_2=$xcarousel_image_link_3=$xcarousel_image_link_4=$xcarousel_image_link_5="";?>

					<div id="carousel_block" class="well clearfix">

						<?php 

						$count_carousel_slide=0;

						for($i=1;$i<=5;$i++) 

						{ 

							$tempu='xcarousel_image_link_'.$i;

							$tempu2=$$tempu;

							if($tempu2!="") 

							{

								$show_car='';

								$count_carousel_slide++;

							}

							else 

							{

								if($i<4) 

								{

									$show_car='';

									$count_carousel_slide++;

								}

								else $show_car='style="display:none"';

							}

						?>

						

						<div class="row" id="carousel_image_div_<?php echo $i;?>" <?php echo $show_car;?>>							

							<div class="col-xs-12 col-sm-12 col-md-6">

								<div class="form-group">

									<label><?php echo $this->lang->line('Carousel Image URL'); ?></label>

									<input type="text" class="form-control" name="carousel_image_link_<?php echo $i;?>" id="carousel_image_link_<?php echo $i;?>" placeholder="<?php echo $this->lang->line('Paste image URL or upload new'); ?>" value="<?php echo $tempu2;?>"/>

									<span class="label label-info preview_uploaded" style="cursor:pointer"><i class="fa fa-eye"></i> <?php echo $this->lang->line('Preview'); ?></span>

								</div>

							</div>

							<div class="col-xs-12 col-sm-12 col-md-6">

								<div class="form-group">

									<label><?php echo $this->lang->line('Upload Image'); ?></label>

									<div id="carousel_images_<?php echo $i;?>"><?php echo $this->lang->line('Upload'); ?></div>

								</div>

							</div>

						</div>

						<?php } ?>

						

						<div class="clearfix">

							<p class="btn btn-warning pull-right" id="add_more_carousel_image" <?php if($count_carousel_slide==5) echo 'style="display:none"' ?>><i class="fa fa-plus"></i> <?php echo $this->lang->line('Add More Image'); ?></p>

						</div>	

						<input type="hidden" name="carousel_content_counter" id="carousel_content_counter">						

					</div>



					<div class="clearfix"></div>

					<div id="video_block" class="well clearfix">

						<div class="form-group col-xs-12 col-md-6" style="padding:0 5px 0 0">

						    <?php $xvideo_url=isset($xupload_data[0]['file_location']) ? $xupload_data[0]['file_location'] : ""; ?>

						    <?php $xvideo_thumb_url=isset($xupload_data[0]['thumbnail_location']) ? $xupload_data[0]['thumbnail_location'] : ""; ?>

						    <?php if($xpost_type!="video_submit") $xvideo_url="";?>

						    <?php if($xpost_type!="video_submit") $xvideo_thumb_url="";?>

							<label><?php echo $this->lang->line('Video URL / Youtube Video URL'); ?></label>

							<input class="form-control" name="video_url" id="video_url" placeholder="<?php echo $this->lang->line('Paste video URL or upload new'); ?>" type="text" value="<?php echo $xvideo_url;?>"> 

							<span class="label label-info preview_uploaded" style="cursor:pointer"><i class="fa fa-eye"></i> <?php echo $this->lang->line('Preview'); ?></span>

						</div>

						<div class="form-group col-xs-12 col-md-6" style="padding:0 0 0 5px">

							<label><?php echo $this->lang->line('Upload Video'); ?></label>

                            <div id="video_url_upload"><?php echo $this->lang->line('Upload');?></div>

                        </div>

						<div class="form-group col-xs-12 col-md-6" style="padding:0 5px 0 0">

							<label><?php echo $this->lang->line('Video Thumbnail URL'); ?></label>

							<input class="form-control" name="video_thumb_url" id="video_thumb_url" placeholder="<?php echo $this->lang->line('Paste image URL or upload new'); ?>" type="text" value="<?php echo $xvideo_thumb_url;?>">

							<span class="label label-info preview_uploaded" style="cursor:pointer"><i class="fa fa-eye"></i> <?php echo $this->lang->line('Preview'); ?></span> 

						</div>

						<div class="form-group col-xs-12 col-md-6" style="padding:0 0 0 5px">

							<label><?php echo $this->lang->line('Upload Thumbnail'); ?></label>

                            <div id="video_thumb_url_upload"><?php echo $this->lang->line('Upload');?></div>

						</div>

					</div>



					 <?php 

					 	$facebook_rx_fb_user_info_id=isset($fb_user_info[0]["id"]) ? $fb_user_info[0]["id"] : 0; 

					 	$facebook_rx_fb_user_info_name=isset($fb_user_info[0]["name"]) ? $fb_user_info[0]["name"] : ""; 

					 	$facebook_rx_fb_user_info_access_token=isset($fb_user_info[0]["access_token"]) ? $fb_user_info[0]["access_token"] : ""; 

					 ?>



					<div class="form-group col-xs-12 col-md-6" style="padding: 0">

						<label><?php echo $this->lang->line('Post as'); ?></label>

						<select class="form-control" id="post_to_page" name="post_to_page">	

						<option value="">--- <?php echo $this->lang->line('Select a page to post offer'); ?> --- </option>

						<?php

							$profile_display=$profile_picture="";

							foreach($fb_page_info as $key=>$val)

							{	

								$id=$val['id'];

								$page_name=$val['page_name'];

								$page_name=str_replace(array('"',"'","\\"),array("","",""), $page_name);

								$page_profile=$val['page_profile'];

								if($id==$xdata_campaign_view['page_group_user_id']) 

								{

									$sel='selected="selected"';

									$profile_display=$page_name;

									$profile_picture=$page_profile;

								}

								else $sel='';

								echo '<option '.$sel.' value="'.$id.'" picture="'.$page_profile.'">'.$page_name.'</option>';								

							}

						 ?>						

						</select>

					</div>	



					<div class="form-group col-xs-12 col-md-6">

						<label><?php echo $this->lang->line('Auto Reply Template') ?></label>

						<select  class="form-control" id="auto_reply_template" name="auto_reply_template">

						<?php

							echo "<option value='0'>{$this->lang->line('Please select a template')}</option>";
							foreach($auto_reply_template as $key=>$val)

							{

								$id=$val['id'];

								$group_name=$val['ultrapost_campaign_name'];



								if($xdata_campaign['ultrapost_auto_reply_table_id'] == $id)

									echo "<option value='{$id}' selected>{$group_name}</option>";

								else

									echo "<option value='{$id}'>{$group_name}</option>";

							}

						 ?>

						</select>

					</div>



					<div class="clearfix"></div>

					

		

					

					<div class="clearfix"></div>

					<div class="form-group hidden">

						<label><?php echo $this->lang->line('When to Post Offer?'); ?></label><br/>

						<input name="schedule_type" value="now" id="schedule_now" checked type="radio"> <?php echo $this->lang->line('Post Now'); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<input name="schedule_type" value="later" id="schedule_later" type="radio"> <?php echo $this->lang->line('Post Later'); ?> 

					</div>







					<div class="form-group schedule_block_item col-xs-12 col-md-6" style="padding:0 5px 0 0">

						<label><?php echo $this->lang->line('Schedule Time'); ?> *</label>

						<input placeholder="Schedule Time" value="<?php echo $xdata_campaign['schedule_time'];?>"  name="schedule_time" id="schedule_time" class="form-control datepicker" type="text"/>

					</div>



					<div class="form-group schedule_block_item col-xs-12 col-md-6" style="padding:0 0 0 5px">

						<label><?php echo $this->lang->line('Time Zone'); ?> *</label>

						<?php

						$time_zone[''] = 'Please Select';

						echo form_dropdown('time_zone',$time_zone,$xdata_campaign_view['time_zone'],' class="form-control" id="time_zone" required'); 

						?>

					</div>	

					<div class="clearfix"></div>

					<div class="box-footer" style="padding:15px 0">

						<input type="hidden" name="submit_post_hidden" id="submit_post_hidden" value="<?php echo $xpost_type;?>">

						<button class="btn btn-warning btn-lg" submit_type="<?php echo $xpost_type;?>" id="submit_post" name="submit_post" type="button"><i class="fa fa-pencil"></i> <?php echo $this->lang->line('Edit Offer'); ?></button>

					</div>

					<br>



				</form>

			</div>

			

		</div>

	</div>  <!-- end of col-6 left part -->



	<div class="col-xs-12 hidden-xs hidden-sm col-md-5 padding-5">

		<div class="box box-primary">

			<div class="box-header ui-sortable-handle" style="cursor: move;">

				<i class="fa fa-facebook-official"></i>

				<h3 class="box-title"><?php echo $this->lang->line('Preview'); ?></h3>

				<!-- tools box -->

				<div class="pull-right box-tools"></div><!-- /. tools -->

			</div>

			<div class="box-body preview">

				<a id="refresh_preview" class="btn btn-primary btn-sm pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line('Refresh Preview'); ?></a>

				<div class="clearfix"></div>							

				<img src="<?php echo $profile_picture;?>" class="preview_cover_img inline pull-left text-center" alt="X">

				<span class="preview_page"><?php echo $profile_display;?></span><br/>

				<!-- <span class="preview_page_sm">Now <?php echo isset($app_info[0]['app_name']) ? $app_info[0]['app_name'] : $this->config->item("product_short_name");?></span><br/><br/>	 -->

				<span class="preview_page_sm"><?php echo $this->lang->line('Just Now'); ?></span><br/><br/>	

				<span class="preview_message"><?php echo $this->lang->line('Post content goes here...'); ?></span>	



				<img src="<?php echo base_url('assets/pre-loader/Surrounded segments.gif'); ?>" class="center-block" id="preview_loading_placeholder" style="margin-bottom:30px">



				<div class="preview_video_block" style="display:none">

					<div id="video_src"><video controls="" width="100%" poster="<?php echo base_url('assets/images/demo_post3.png');?>" style="border:1px solid #ccc"><source src=""></source></video></div>				

					

					<div class="preview_og_info" style="margin-top:-8px !important">

						<div class="preview_og_info_title inline-block"><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></div>

						<div class="preview_og_info_desc inline-block"><?php echo $this->lang->line('Offer Description Goes Here'); ?> </div>

						<span class="preview_og_info_link pull-left"><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span></span> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b>Get Offer</b></a>

						<div class="clearfix"></div>

						<div class="coupon_footer">

							<hr style="margin:5px 0"><span class="coupon_footer_code"><?php echo $this->lang->line('XXXX'); ?></span> <span class="coupon_footer_text"><?php echo $this->lang->line('Use this code at checkout'); ?></span>

						</div>

					</div>

				</div>



				<div class="preview_img_block" style="display:none">

					<img src="<?php echo base_url('assets/images/demo_post2.png');?>" class="preview_img" alt="No Image Preview">	

					<div class="preview_og_info">

						<div class="preview_og_info_title inline-block"><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></div>

						<div class="preview_og_info_desc inline-block"><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span> </div>

						<span class="preview_og_info_link pull-left"><?php echo $this->lang->line('XX people got this offer'); ?></span> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a>

						<div class="clearfix"></div>

						<div class="coupon_footer">

							<hr style="margin:5px 0"><span class="coupon_footer_code"><?php echo $this->lang->line('XXXX'); ?></span> <span class="coupon_footer_text"><?php echo $this->lang->line('Use this code at checkout'); ?></span>

						</div>

					</div>

				</div>



				<div class="preview_carousel_block" style="display:none">

					

					<div class="carousel slide" id="myCarousel">

					  <div class="carousel-inner">

					    <div class="item active">

					      <div class="col-xs-4 carousel_wrapper slide1">

					        <img src="<?php echo base_url('assets/images/slider1.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					      <div class="col-xs-4 carousel_wrapper slide2">

					        <img src="<?php echo base_url('assets/images/slider2.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					      <div class="col-xs-4 carousel_wrapper slide3">

					        <img src="<?php echo base_url('assets/images/slider3.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					    </div>



					    <div class="item">

					      <div class="col-xs-4 carousel_wrapper slide2">

					        <img src="<?php echo base_url('assets/images/slider2.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					      <div class="col-xs-4 carousel_wrapper slide3">

					        <img src="<?php echo base_url('assets/images/slider3.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					      <div class="col-xs-4 carousel_wrapper slide1">

					        <img src="<?php echo base_url('assets/images/slider1.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					    </div>



					    <div class="item">

					      <div class="col-xs-4 carousel_wrapper slide3">

					        <img src="<?php echo base_url('assets/images/slider3.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					      <div class="col-xs-4 carousel_wrapper slide1">

					        <img src="<?php echo base_url('assets/images/slider1.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					      <div class="col-xs-4 carousel_wrapper slide2">

					        <img src="<?php echo base_url('assets/images/slider2.png');?>">

					        <div class="carousel_footer_container"><h4><?php echo $this->lang->line('XX% Discount Title Goes Here'); ?></h4><p class='pull-left expiers_at'><span class="expires_at_formatted"><?php echo $this->lang->line('Expires MMM DD, YYYY'); ?></span> <span class="online_offline_carousel" style="color:#9197a3 !important"><i class="fa fa-circle"></i> <?php echo $this->lang->line('Online Only');?></span></p> <a class="cta-btn btn btn-sm btn-default pull-right" title="Offer details will be displayed (details,terms & condition, link etc)" style="border-radius:0"><b><?php echo $this->lang->line('Get Offer'); ?></b></a> <div class="clearfix"></div></div>

					      </div>

					    </div>

					  </div>

					<a class="left carousel-control" href="#myCarousel" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>

					<a class="right carousel-control" href="#myCarousel" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>

					</div>

					<div class="preview_og_info">

						<div class="coupon_footer">

							<span class="coupon_footer_code"><?php echo $this->lang->line('XXXX'); ?></span> <span class="coupon_footer_text"><?php echo $this->lang->line('Use this code at checkout'); ?></span>

						</div>

					</div>



					<div class="alert alert-warning text-center" style="background: #fffddd !important;border-radius: none;color:orange !important"><?php echo $this->lang->line('Carousel preview is only displaying 3 images'); ?></div>

				</div>



		



			</div>

		</div>

	</div> <!-- end of col-6 right part -->



</div>







<script>

	function load_preview(change_image,change_carousel,change_video) 

	{

		// var post_type=$('.post_type.active').attr("id");

		$(".preview_video_block").hide();

		$(".preview_carousel_block").hide();

		$(".preview_img_block").hide();



		$("#preview_loading_placeholder").show();



		if (typeof(change_image)==='undefined') change_image=0;

		if (typeof(change_carousel)==='undefined') change_carousel=0;

		if (typeof(change_video)==='undefined') change_video=0;



		var post_type=$('#submit_post_hidden').val();

		var message=$("#message").val();

		var type_offer=$("#type_offer").val();

		var location_type=$("#location_type").val();	

		var discount_title=$("#discount_title").val();

		var discount_value=$("#discount_value").val();

		var currency=$("#currency").val();

		var online_coupon_code=$("#online_coupon_code").val();

		var offer_details=$("#offer_details").val();	



		if(message=="") message=="Post content goes here...";

		if(discount_title=="") discount_title="Discount Title Goes Here";

		if(discount_value=="") discount_value="XX";

		if(currency=="") currency="USD";

		if(offer_details=="") offer_details="Offer Description Goes Here";





		var title_part="";

		if(type_offer=="bogo") title_part="Buy "+discount_value+" get ";

		else if(type_offer=="free_stuff") title_part="Free ";

		else if(type_offer=="cash_discount") title_part=currency+ " "+discount_value+" ";

		else title_part=discount_value+"% ";



		var online_offline="";	

		if(location_type=="online") 

		{

			online_offline="<i class='fa fa-circle'></i> Online Only";

			if(online_coupon_code!="") $(".coupon_footer").show();

			else $(".coupon_footer").hide();

		}

		else if(location_type=="offline") 

		{

			online_offline="<i class='fa fa-circle'></i> In-store Only";

			$(".coupon_footer").hide();

		}

		else

		{

			online_offline="<i class='fa fa-circle'></i> Online & In-store";

			if(online_coupon_code!="") $(".coupon_footer").show();

			else $(".coupon_footer").hide();

		}



		var current_expires_at=$(".expires_at_formatted").html();

		if(current_expires_at=="Expires MMM DD, YYYY") expiration_time_change();



		$("#preview_message").html(message);

		

		if(post_type=="image_submit")

    	{

    		var default_image_url="<?php echo base_url('assets/images/demo_post2.png');?>";    		



    		var image_url=$("#image_url").val();

    		if(image_url=="") image_url=default_image_url;

    		if(change_image==1)

    		$(".preview_img").attr('src',image_url);

    		$(".preview_img_block .preview_og_info_title").html(title_part+discount_title);

    		$(".preview_img_block .online_offline").html(online_offline);

    		$(".coupon_footer_code").html(online_coupon_code);



    		$(".preview_video_block").hide();

    		$(".preview_carousel_block").hide();

    		$(".preview_img_block").show();

    	}

    	if(post_type=="carousel_submit")

    	{

    		var carousel_image_link_1=$("#carousel_image_link_1").val();

			var carousel_image_link_2=$("#carousel_image_link_2").val();

			var carousel_image_link_3=$("#carousel_image_link_3").val();		

			if(carousel_image_link_1=="") carousel_image_link_1="<?php echo base_url('assets/images/slider1.png');?>";

			if(carousel_image_link_2=="") carousel_image_link_2="<?php echo base_url('assets/images/slider2.png');?>";

			if(carousel_image_link_3=="") carousel_image_link_3="<?php echo base_url('assets/images/slider3.png');?>";



    		if(change_carousel==1)

    		{

    			$(".slide1 img").attr("src",carousel_image_link_1);		    		

	    		$(".slide2 img").attr("src",carousel_image_link_2);		    		

	    		$(".slide3 img").attr("src",carousel_image_link_3);	

	    	}



    		$('.carousel_footer_container h4').html(char_limiting(title_part+discount_title,70));

    		$(".online_offline_carousel").html(online_offline);



			$(".preview_video_block").hide();

    		$(".preview_carousel_block").show();

    		$(".preview_img_block").hide();	    		

	    	

    	}

    	else if(post_type=="video_submit")

    	{ 		



	    	var video_url=$("#video_url").val();

	    	var video_thumb_url=$("#video_thumb_url").val();

	    	if(video_thumb_url=="" && video_url=="") video_thumb_url="<?php echo base_url('assets/images/demo_post3.png');?>";

	    	

    		var write_html='<video width="100%" height="auto" style="border:1px solid #ccc;" controls poster="'+video_thumb_url+'"><source src="'+video_url+'">Your browser does not support the video tag.</video>';

    		if(change_video==1)

    		$("#video_src").html(write_html);



    		$(".preview_video_block .preview_og_info_title").html(title_part+discount_title);	

    		$(".preview_video_block .preview_og_info_desc ").html(char_limiting(offer_details,150));	

	    	

	    	$(".preview_video_block").show();

    		$(".preview_carousel_block").hide();

    		$(".preview_img_block").hide();



    	}



    	$("#preview_loading_placeholder").hide();

		

	}



	function expiration_time_change() 

	{

		var expiration_time=$("#expiration_time").val();

		var expiry_time_zone=$("#expiry_time_zone").val();

    	

		$.ajax({

            type:'POST' ,

            url:"<?php echo site_url();?>ultrapost/offer_post_expires_at_formatter",

            data:{expiration_time:expiration_time,expiry_time_zone:expiry_time_zone},

            success:function(response){	            		                

               if(response!="")

               {

               	 $(".expires_at_formatted").html(response);

               } 

               else  $(".expires_at_formatted").html("Expires MMM DD, YYYY");

            }

        }); 

		

	}





	function char_limiting(text,limit)

	{

		var dot=limit-3;

		 if (text.length > limit) 

		 {

		    return text.substr(0,dot) + '...';

		 }

		 else return text;

	}







	$j("document").ready(function(){



		var base_url="<?php echo base_url();?>";

		var xpost_type="<?php echo $xpost_type;?>";

		var xauto_share_post="<?php echo $xauto_share_post;?>";

		var xauto_like_post="<?php echo $xauto_like_post;?>";

		var xauto_comment_post="<?php echo $xauto_comment_post;?>";

		var xtype_offer="<?php echo $xtype_offer;?>";

		var xlocation_type="<?php echo $xlocation_type;?>";



		setTimeout(function() {

			load_preview(1,1,1);

		}, 3000);

		



		var today = new Date();

		// var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());

		$j('.datepicker').datetimepicker({

			theme:'light',

			format:'Y-m-d H:i:s',

			formatDate:'Y-m-d H:i:s',

			minDate: today

		});

 

      

 		$j("#auto_share_this_post_by_pages").multipleSelect({

            filter: true,

            multiple: true

        });	



		$j("#auto_share_this_post_by_groups").multipleSelect({

            filter: true,

            multiple: true

        });	



        $(".only_offline,.coupon_footer").hide();



  

        if(xpost_type=="image_submit")

    	{

    		$("#video_block,#carousel_block").hide();

    		$("#image_block").show();

    	}

    	if(xpost_type=="carousel_submit")

    	{

    		$("#video_block,#image_block").hide();

    		$("#carousel_block").show();

    	}

    	else if(xpost_type=="video_submit")

    	{

    		$("#image_block,#carousel_block").hide();

    		$("#video_block").show();

    	}



        if(xlocation_type=="both")

        {

        	$(".only_online").show();

        	$(".only_offline").show();

        }   

        else if(xlocation_type=="online")

        {

        	$(".only_online").show();

        	$(".only_offline").hide();

        	$("#store_coupon_code").val("");

        	$("#barcode_type").val("");

        	$("#barcode_value").val("");

        }   

        else

        {

        	$(".only_online").hide();

        	$(".only_offline").show();

        	$("#online_coupon_code").val("");

        	$("#link").val("");

        } 



        if(xtype_offer=="free_stuff")

        {

        	$("#discount_value").val('').addClass('custom_disabled');

        	$("#currency").val('').addClass('custom_disabled');

        }   

        else if(xtype_offer=="percentage_off" || xtype_offer=="bogo")

        {

        	$("#discount_value").removeClass('custom_disabled');

        	$("#currency").val('').addClass('custom_disabled');

        }   

        else

        {

        	$("#discount_value").removeClass('custom_disabled');

        	$("#currency").removeClass('custom_disabled');

	    }



        if(xauto_comment_post=='0') $('.auto_comment_block_item').hide();

        else $('.auto_comment_block_item').show(); 



        if(xauto_share_post=='0') $('.auto_share_post_block_item').hide();

        else $('.auto_share_post_block_item').show();

        

 

        $(document.body).on('change','input[name=auto_share_post]',function(){    

        	if($("input[name=auto_share_post]:checked").val()=="1")

        	$(".auto_share_post_block_item").show();

        	else $(".auto_share_post_block_item").hide();

        });



        $(document.body).on('change','input[name=auto_comment]',function(){    

        	if($("input[name=auto_comment]:checked").val()=="1")

        	$(".auto_comment_block_item").show();

        	else $(".auto_comment_block_item").hide();

        }); 





        var message_pre=$("#message").val();

    	message_pre=message_pre.replace(/[\r\n]/g, "<br />");

    	if(message_pre!="")

    	{

    		message_pre=message_pre+"<br/><br/>";

    		$(".preview_message").html(message_pre);

    	}

    	    

    

        $(document.body).on('click','.post_type',function(){ 

  

        	var post_type=$(this).attr("id");



             

        	if(post_type=="image_post")

        	{

        		$("#video_block,#carousel_block").hide();

        		$("#image_block").show();

        		$('.post_type').removeClass("active");

        		$('#submit_post').attr("submit_type","image_submit");

        		$('#submit_post_hidden').val("image_submit");

        	}

        	if(post_type=="carousel_post")

        	{

        		$("#video_block,#image_block").hide();

        		$("#carousel_block").show();

        		$('.post_type').removeClass("active");

        		$('#submit_post').attr("submit_type","carousel_submit");

        		$('#submit_post_hidden').val("carousel_submit");



        	}

        	else if(post_type=="video_post")

        	{

        		$("#image_block,#carousel_block").hide();

        		$("#video_block").show();

        		$('.post_type').removeClass("active");

        		$('#submit_post').attr("submit_type","video_submit");

        		$('#submit_post_hidden').val("video_submit");



        	}

        	$(this).addClass("active");

        	load_preview();

        });





        $(document.body).on('blur','#link,#message,#discount_title,#discount_value,#video_thumb_url',function(){ 

        	load_preview();

        });



        $(document.body).on('blur','#image_url',function(){ 

        	load_preview(1,0,0);

        });



        $(document.body).on('blur','#video_thumb_url',function(){ 

        	load_preview(0,0,1);

        });



		$(document.body).on('blur','#carousel_image_link_1,#carousel_image_link_2,#carousel_image_link_3',function(){ 

        	load_preview(0,1,0);

        });



        $(document.body).on('keyup','#discount_title,#discount_value,#online_coupon_code',function(){ 

        	load_preview();

        });

       



        $(document.body).on('blur','#offer_details',function(){ 

        	var offer_details=$("#offer_details").val();

        	if(offer_details=="") ret=0;

        	else ret = offer_details.length;

        	$("#offer_details_count").html(ret);

        	load_preview();

        });

        $(document.body).on('keyup','#offer_details',function(){ 

        	var offer_details=$("#offer_details").val();

        	if(offer_details=="") ret=0;

        	else ret = offer_details.length;

        	$("#offer_details_count").html(ret);

        	load_preview();

        });



		$(document.body).on('change','#currency',function(){ 

        	load_preview();

        });



 		$(document.body).on('click','#refresh_preview',function(){ 

        	load_preview(1,1,1);

        });





		$(document.body).on('blur','#expiration_time',function(){ 

        	expiration_time_change();

        });



        $(document.body).on('change','#expiry_time_zone',function(){ 

        	expiration_time_change();

        });



        $(document.body).on('click','.preview_uploaded',function(){ 

        	var media_url=$(this).prev().val();

        	if(media_url=='')

        	{

        		alert('No media to display.');

        		return;

        	}

        	var post_type=$("#submit_post").attr("submit_type");  

        	var html_content;

        	if(post_type!='video_submit')

        	html_content="<img src='"+media_url+"' class='img-responsive img-thumbnail'>";

        	else html_content='<video controls="" width="100%" style="border:1px solid #ccc"><source src="'+media_url+'"></source></video>';

        	$("#preview_uploaded_modal_content").html(html_content);

        	$("#preview_uploaded_modal").modal();

        });



        $(document.body).on('change','#post_to_page',function(){ 

        	var profile_display=$("#post_to_page option:selected").html();

        	var profile_picture=$("#post_to_page option:selected").attr('picture');

        	$(".preview_cover_img").attr('src',profile_picture);

        	$(".preview_page").html(profile_display);

        });



        $(document.body).on('keyup','#message',function(){  

        	var message=$(this).val();

        	message=message.replace(/[\r\n]/g, "<br />");

        	if(message!="")

        	{

        		message=message+"<br/><br/>";

        		$(".preview_message").html(message);

        		$(".demo_preview").hide();

        	}

        }); 





        $(document.body).on('change','#location_type',function(){  

        	var location_type=$("#location_type").val();   

	        

	        if(location_type=="both")

	        {

	        	$(".only_online").show();

	        	$(".only_offline").show();

	        }   

	        else if(location_type=="online")

	        {

	        	$(".only_online").show();

	        	$(".only_offline").hide();

	        	$("#store_coupon_code").val("");

	        	$("#barcode_type").val("");

	        	$("#barcode_value").val("");

	        }   

	        else

	        {

	        	$(".only_online").hide();

	        	$(".only_offline").show();

	        	$("#online_coupon_code").val("");

	        	$("#link").val("");

	        }  

	        load_preview(); 

            

        });



        $(document.body).on('change','#type_offer',function(){  

        	var type_offer=$("#type_offer").val();   

	        

	        if(type_offer=="free_stuff")

	        {

	        	$("#discount_value").val('').addClass('custom_disabled');

	        	$("#currency").val('').addClass('custom_disabled');

	        }   

	        else if(type_offer=="percentage_off" || type_offer=="bogo")

	        {

	        	$("#discount_value").removeClass('custom_disabled');

	        	$("#currency").val('').addClass('custom_disabled');

	        }   

	        else

	        {

	        	$("#discount_value").removeClass('custom_disabled');

	        	$("#currency").removeClass('custom_disabled');

	        }

	        load_preview();    

            

        });







 		$(document.body).on('blur','#video_url',function(){  

	        load_preview(0,0,1);	 

        	var link=$("#video_url").val();   

	        if(link!='')

	        {

	            $.ajax({

	            type:'POST' ,

	            url:"<?php echo site_url();?>ultrapost/offer_post_youtube_video_grabber",

	            data:{link:link},

	            success:function(response){	            		                

	               if(response!="")

	               {

	               	 	if(response=='fail')

	               	 	{

	               	 		$("#error_modal_content").html("Video URL is invalid or this video is restricted from playback on certain sites.");

	               	 		$("#error_modal").modal();

	               	 		$("#video_url").val("");

	               	 	}

	               	 	else

	               	 	{

	               	 		$("#video_url").val(response);

	               	 		load_preview(0,0,1);	 

	               	 	}

	               }              

	            }

	        }); 

	            

	        }	         

            

        });





        $("#image_url_upload").uploadFile({

	        url:base_url+"ultrapost/offer_post_upload_image_only",

	        fileName:"myfile",

	        maxFileSize:1*1024*1024,

	        showPreview:false,

	        returnType: "json",

	        dragDrop: true,

	        showDelete: true,

	        multiple:false,

	        maxFileCount:1, 

	        acceptFiles:".png,.jpg,.jpeg",

	        deleteCallback: function (data, pd) {

	            var delete_url="<?php echo site_url('ultrapost/offer_post_delete_uploaded_file');?>";

                $.post(delete_url, {op: "delete",name: data},

                    function (resp,textStatus, jqXHR) {  

                    	$("#image_url").val('');  

	                	load_preview(1,0,0);                        

                    });

	           

	         },

	         onSuccess:function(files,data,xhr,pd)

	           {

	               var data_modified = base_url+"upload_caster/offer_post/"+data;

	               $("#image_url").val(data_modified);	

	               load_preview(1,0,0);	

	           }

	    });





	

		$("#video_url_upload").uploadFile({

	        url:base_url+"ultrapost/offer_post_upload_video",

	        fileName:"myfile",

	        maxFileSize:100*1024*1024,

	        showPreview:false,

	        returnType: "json",

	        dragDrop: true,

	        showDelete: true,

	        multiple:false,

	        maxFileCount:1, 

	        acceptFiles:".3g2,.3gp,.3gpp,.asf,.avi,.dat,.divx,.dv,.f4v,.flv,.m2ts,.m4v,.mkv,.mod,.mov,.mp4,.mpe,.mpeg,.mpeg4,.mpg,.mts,.nsv,.ogm,.ogv,.qt,.tod,.ts,.vob,.wmv",

	        deleteCallback: function (data, pd) {

	            var delete_url="<?php echo site_url('ultrapost/offer_post_delete_uploaded_file');?>";

                $.post(delete_url, {op: "delete",name: data},

                    function (resp,textStatus, jqXHR) { 

                    	$("#video_url").val('');  

	                	load_preview(0,0,1);	                        

                    });

	           

	         },

	         onSuccess:function(files,data,xhr,pd)

	           {

	               var data_modified = base_url+"upload_caster/offer_post/"+data;            

	               $("#video_url").val(data_modified);

	               load_preview(0,0,1);	

	           }

	    });



	    $("#video_thumb_url_upload").uploadFile({

	        url:base_url+"ultrapost/offer_post_upload_video_thumb",

	        fileName:"myfile",

	        maxFileSize:1*1024*1024,

	        showPreview:false,

	        returnType: "json",

	        dragDrop: true,

	        showDelete: true,

	        multiple:false,

	        maxFileCount:1, 

	        acceptFiles:".png,.jpg,.jpeg",

	         deleteCallback: function (data, pd) {

	            var delete_url="<?php echo site_url('ultrapost/offer_post_delete_uploaded_file');?>";

                $.post(delete_url, {op: "delete",name: data},

                    function (resp,textStatus, jqXHR) { 

                    	$("#video_thumb_url").val('');  

	                	load_preview(0,0,1);	                         

                    });

	           

	         },

	         onSuccess:function(files,data,xhr,pd)

	           {

	               var data_modified = base_url+"upload_caster/offer_post/"+data;

	               $("#video_thumb_url").val(data_modified);

	               load_preview(0,0,1);		            

	           }

	    });







	     $(document.body).on('click','#submit_post',function(){ 





	     	if($("#campaign_name").val()=="")

    		{

    			$("#error_modal_content").html("Please type a offer name.");

    			$("#error_modal").modal();

    			return;

    		}

    		if($("#expiration_time").val()=="")

    		{

    			$("#error_modal_content").html("Please choose expiration time.");

    			$("#error_modal").modal();

    			return;

    		}

    		if($("#expiry_time_zone").val()=="")

    		{

    			$("#error_modal_content").html("Please choose expiration time zone.");

    			$("#error_modal").modal();

    			return;

    		} 

    		if($("#discount_title").val()=="")

    		{

    			$("#error_modal_content").html("Please type offer/discount title.");

    			$("#error_modal").modal();

    			return;

    		}



    		if($("#type_offer").val()=="cash_discount")

    		{

    			if($("#discount_value").val()=="" || $("#currency").val()=="")

    			{

    				$("#error_modal_content").html("You must provide discount value & currency for cash discount offer.");

	    			$("#error_modal").modal();

	    			return;

    			}

    		}  

    		if($("#type_offer").val()=="percentage_off" || $("#type_offer").val()=="bogo")

    		{

    			if($("#discount_value").val()=="")

    			{

    				$("#error_modal_content").html("You must provide discount value for bogo or % discount offer.");

	    			$("#error_modal").modal();

	    			return;

    			}

    		} 







    		if($("#location_type").val()=="online" || $("#location_type").val()=="both")

    		{

    			if($("#link").val()=="")

    			{

    				$("#error_modal_content").html("Please provide 'Get Offer' link.");

	    			$("#error_modal").modal();

	    			return;

    			}

    		} 

    		if($("#location_type").val()=="offline" || $("#location_type").val()=="both")

    		{

    			if($("#barcode_type").val()!="" && $("#barcode_value").val()=="")

    			{

    				$("#error_modal_content").html("You have selected a barcode type but have not provided barcode value.");

	    			$("#error_modal").modal();

	    			return;

    			}

    		} 



    		var offer_details=$("#offer_details").val();

    		if(offer_details=="")

    		{

    			$("#error_modal_content").html("Please type offer details.");

    			$("#error_modal").modal();

    			return;

    		}



    		if(offer_details.length>250)

    		{

    			$("#error_modal_content").html("Offer details can be maximum 250 characters in length.");

    			$("#error_modal").modal();

    			return;

    		}





  

        	var post_type=$(this).attr("submit_type");  

        

        	if(post_type=="carousel_submit")

        	{

        		if($("#carousel_image_link_1").val()=="" || $("#carousel_image_link_2").val()=="" || $("#carousel_image_link_3").val()=="")

        		{

        			$("#error_modal_content").html("Carousel offer needs at least 3 images. Please paste 3 image URLs in first 3 image fields or upload new.");

        			$("#error_modal").modal();

        			return;

        		}

        	}



        	else if(post_type=="image_submit")

        	{   

        		if($("#image_url").val()=="")

        		{

        			$("#error_modal_content").html("Please paste an image url or upload an image to post offer.");

        			$("#error_modal").modal();

        			return;

        		}     		

        	}



        	else if(post_type=="video_submit")

        	{

        		if($("#video_url").val()=="")

        		{

        			$("#error_modal_content").html("Please paste an video url or upload an video to post offer.");

        			$("#error_modal").modal();

        			return;

        		}  

        	}





        	var post_to_page = $("#post_to_page").val();

        	if(post_to_page=="")

        	{

        		$("#error_modal_content").html("Please select a page to publish this offer.");

        		$("#error_modal").modal();

        		return;

        	}



        	var auto_share_post = $("input[name=auto_share_post]:checked").val();

        	var auto_share_this_post_by_pages = $("#auto_share_this_post_by_pages").val();

        	var auto_share_this_post_by_groups = $("#auto_share_this_post_by_groups").val();

        	if(auto_share_post=='1' && auto_share_this_post_by_pages==null && auto_share_this_post_by_groups==null && $("input[name=auto_share_to_profile]:checked").val() == "No")

        	{

        		$("#error_modal_content").html("Please select timeline or page(s) or groups(s) for auto sharing.");

        		$("#error_modal").modal();

        		return;

        	}



        	var auto_comment = $("input[name=auto_comment]:checked").val();

        	var auto_comment_text = $("#auto_comment_text").val();

        	if(auto_comment=='1' && auto_comment_text=="")

        	{

        		$("#error_modal_content").html("Please type auto comment message.");

        		$("#error_modal").modal();

        		return;

        	}



        	var schedule_type = 'later';

        	var schedule_time = $("#schedule_time").val();

        	var time_zone = $("#time_zone").val();

        	if(schedule_type=='later' && (schedule_time=="" || time_zone==""))

        	{

        		$("#error_modal_content").html("Please select schedule time/time zone.");

        		$("#error_modal").modal();

        		return;

        	}



        	$("#submit_post").html('Processing...');     	

        	$("#submit_post").addClass("disabled"); 

        	$("#response_modal_content").removeClass("alert-danger");

        	$("#response_modal_content").removeClass("alert-success");

        	var loading = '<img src="'+base_url+'assets/pre-loader/Surrounded segments.gif" class="center-block">';

        	$("#response_modal_content").html(loading);

        	$("#response_modal").modal();



		      var queryString = new FormData($("#auto_poster_form")[0]);

		      $.ajax({

		       type:'POST' ,

		       url: base_url+"ultrapost/edit_offer_post_campaign_action",

		       data: queryString,

		       dataType : 'JSON',

		       // async: false,

		       cache: false,

		       contentType: false,

		       processData: false,

		       success:function(response)

		       {  		         

		       		$("#submit_post").removeClass("disabled");

		         	$("#submit_post").html('<i class="fa fa-pencil"></i> Edit Offer');    



		         	if(response.status=="1")

			        {

			         	$("#response_modal_content").removeClass("alert-danger");

			         	$("#response_modal_content").addClass("alert-success");

			         	var report_link="<br/><a href='"+base_url+"ultrapost/offer_post_report/"+response.id+"'>Click here to see offer report</a>";

			         	$("#response_modal_content").html(response.message+report_link);

			        }

			        else

			        {

			         	$("#response_modal_content").removeClass("alert-success");

			         	$("#response_modal_content").addClass("alert-danger");

			         	$("#response_modal_content").html(response.message);

			        }



		       }



		      });



        });



		$('#myCarousel').carousel({

            interval: false

        })

        $('.carousel .item').each(function () {

            var next = $(this).next();

            if (!next.length) {

                next = $(this).siblings(':first');

            }

            // next.children(':first-child').clone().appendTo($(this));

            // if (next.next().length > 0) {

            //     next.next().children(':first-child').clone().appendTo($(this));

            // } else {

            //     $(this).siblings(':first').children(':first-child').clone().appendTo($(this));

            // }

        });



        var carousel_content_counter="<?php echo $count_carousel_slide;?>";

		$("#carousel_content_counter").val(carousel_content_counter);

		$("#add_more_carousel_image").click(function(){

			carousel_content_counter++;

			if(carousel_content_counter == 5)

				$("#add_more_carousel_image").hide();

			$("#carousel_content_counter").val(carousel_content_counter);



			$("#carousel_image_div_"+carousel_content_counter).show();



		});







    });







</script>







<script>

	var base_url="<?php echo site_url(); ?>";

	var user_id = "<?php echo $this->session->userdata('user_id'); ?>";

	<?php for($k=1;$k<=5;$k++) : ?>

		$("#carousel_images_<?php echo $k; ?>").uploadFile({

	        url:base_url+"ultrapost/offer_post_upload_image_only",

	        fileName:"myfile",

	        // maxFileSize:1*1024*1024,

	        showPreview:false,

	        returnType: "json",

	        dragDrop: true,

	        showDelete: true,

	        multiple:false,

	        maxFileCount:1, 

	        acceptFiles:".png,.jpg,.jpeg",

	        deleteCallback: function (data, pd) {

	            var delete_url="<?php echo site_url('ultrapost/offer_post_delete_uploaded_file');?>";

	            $.post(delete_url, {op: "delete",name: data},

	                function (resp,textStatus, jqXHR) {

	                	$("#carousel_image_link_<?php echo $k; ?>").val('');  

	                	load_preview(0,1,0);	                    

	                });

	           

	         },

	         onSuccess:function(files,data,xhr,pd)

	           {

	               var data_modified = base_url+"upload_caster/offer_post/"+data;

	               $("#carousel_image_link_<?php echo $k; ?>").val(data_modified);

	               load_preview(0,1,0);	

	           }

	    });

	<?php endfor; ?>

</script>



<div class="modal fade" id="response_modal" data-backdrop="static" data-keyboard="false">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

				<h4 class="modal-title"><i class="fa fa-gift"></i> Offer Post</h4>

			</div>

			<div class="modal-body">

				<div class="alert text-center" id="response_modal_content">

					

				</div>

			</div>

		</div>

	</div>

</div>



<div class="modal fade" id="error_modal" data-backdrop="static" data-keyboard="false">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

				<h4 class="modal-title"><i class="fa fa-gift"></i> Offer Post Error</h4>

			</div>

			<div class="modal-body">

				<div class="alert text-center alert-warning" id="error_modal_content">

					

				</div>

			</div>

		</div>

	</div>

</div>



<div class="modal fade" id="preview_uploaded_modal" data-backdrop="static" data-keyboard="false">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

				<h4 class="modal-title"><i class="fa fa-eye"></i> <?php echo $this->lang->line('Media Preview'); ?></h4>

			</div>

			<div class="modal-body">

				<div id="preview_uploaded_modal_content">

					

				</div>

			</div>

		</div>

	</div>

</div>





<?php $this->load->view("offer_post/style.php"); ?>


<style type="text/css" media="screen">
	
	.post_type.active {
	    background: #365899;
	    color: #fff;
	}


	.post_type {
	    padding: 10px 12px;
	    border: 1px solid #365899;
	    font-weight: bold;
	    color: #365899;
	    font-size: 15px;
	    border-radius: 7px;
	    -moz-border-radius: 7px;
	    -webkit-border-radius: 7px;
	    cursor: pointer;
	}

</style>