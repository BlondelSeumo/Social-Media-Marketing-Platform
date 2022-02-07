<div id="put_script"></div>
<section class="section">
	<div class="section-header d-none">
		<h1><i class="fa fa-plus-circle"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
		  <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
		  <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerce'); ?>"><?php echo $this->lang->line("E-commerce"); ?></a></div>
		  <div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<div class="section-body">
		<form action="#" enctype="multipart/form-data" id="plugin_form">
			<div class="row">
				<div class="col-12">
					<div class="card main_card no_shadow">		
						<div class="card-body p-0">
							<div class="row">

								<?php if($this->basic->is_exist("add_ons",array("unique_name"=>"ecommerce_digital_product")) && $this->basic->is_exist("modules",array("id"=>316))) : ?>
									<?php if($this->session->userdata('user_type') == 'Admin' || in_array(316,$this->module_access)) : ?>
									<div class="col-12">
										<div class="row">		
											<div class="col-12 col-md-4">
												<div class="form-group">
													<label><?php echo $this->lang->line("Store Type") ?> *</label>
												</div>
											</div>
											  <div class="col-12 col-md-4">
												<label class="custom-switch">
												  <input type="radio" name="store_type" value="physical" id="store_type" class="custom-switch-input" checked>
												  <span class="custom-switch-indicator"></span>
												  <span class="custom-switch-description"><?php echo $this->lang->line('Physical'); ?></span>
												</label>
											  </div>
											  <div class="col-12 col-md-4">
												<label class="custom-switch">
												  <input type="radio" name="store_type" value="digital" id="store_type" class="custom-switch-input">
												  <span class="custom-switch-indicator"></span>
												  <span class="custom-switch-description"><?php echo $this->lang->line('Digital'); ?>
												</label>
											  </div>
										</div>
									</div>
									<?php endif; ?>
								<?php endif; ?>

							  <div class="form-group col-12 col-md-6">
							    <label>
							       <?php echo $this->lang->line("Select page"); ?>
							       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Select page") ?>" data-content='<?php echo $this->lang->line("Select your Facebook page for which you want to create the store.").$this->lang->line("Skip selecting page if you plan to use this store outside Messenger.") ?>'><i class='fas fa-info-circle'></i> </a>
							    </label>
							    <?php $page_info[0]= $this->lang->line("None"); ?>
							    <?php echo form_dropdown('page', $page_info,'0', 'class="form-control select2" id="page" style="width:100%;"' ); ?>                   
							  </div>

							   <div class="form-group col-12 col-md-6">
							    <label>
							      <?php echo $this->lang->line("Store name"); ?> *
							    </label>
							    <input type="text" name="store_name" id="store_name" class="form-control">                      
							  </div>

							  <div class="form-group col-6 col-md-6">
							    <label>
							      <?php echo $this->lang->line("Email"); ?> *
							    </label>
							    <input type="email" name="store_email" id="store_email" class="form-control">                      
							  </div>

							  <div class="form-group col-6 col-md-6">
							    <label>
							      <?php echo $this->lang->line("Mobile/phone"); ?>
							    </label>
							    <input type="text" name="store_phone" id="store_phone" class="form-control">                      
							  </div>

							  <div class="form-group col-12 col-md-4">
							    <label>
							      <?php echo $this->lang->line("Country"); ?> *
							    </label>
							    <?php 
							    $country_names[''] = $this->lang->line("Select");
							    echo form_dropdown('store_country', $country_names,'', 'class="form-control select2" id="store_country" style="width:100%;"' ); 
							    ?>
							  </div>

							  <div class="form-group col-6 col-md-4">
							    <label>
							      <?php echo $this->lang->line("State"); ?> *
							    </label>
							    <input type="text" name="store_state" id="store_state" class="form-control">                      
							  </div>

							  <div class="form-group col-6 col-md-4">
							    <label>
							      <?php echo $this->lang->line("City"); ?> *
							    </label>
							    <input type="text" name="store_city" id="store_city" class="form-control">                      
							  </div>

							  <div class="form-group col-12 col-md-8">
							    <label>
							      <?php echo $this->lang->line("Street address"); ?> *
							    </label>
							    <input type="text" name="store_address" id="store_address" class="form-control">                  
							  </div>

							  <div class="form-group col-6 col-md-2">
							    <label>
							      <?php echo $this->lang->line("Postal code"); ?> *
							    </label>
							    <input type="text" name="store_zip" id="store_zip" class="form-control">                      
							  </div>

							  <div class="form-group col-6 col-md-2">
							    <label>
							      <?php echo $this->lang->line("Locale"); ?> *
							    </label>
							   <?php echo form_dropdown('store_locale', $locale_list,$this->language, 'class="form-control select2" id="store_locale" style="width:100%;"' ); ?>                      
							  </div>							  

							  <div class="col-12 col-md-6">
							    <div class="form-group">
							      <label><?php echo $this->lang->line('Logo'); ?> 
							       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Logo"); ?>" data-content="<?php echo $this->lang->line("Maximum: 1MB, Format: JPG/PNG, Recommended dimension : 200x50"); ?> / 120x120"><i class='fa fa-info-circle'></i> </a>
							      </label>
							      <div id="store-logo-dropzone" class="dropzone mb-1">
							        <div class="dz-default dz-message">
							          <input class="form-control" name="store_logo" id="store_logo" type="hidden">
							          <span style="font-size: 20px;"><i class="fas fa-cloud-upload-alt" title='<?php echo $this->lang->line("Upload"); ?>' data-toggle="tooltip" style="font-size: 35px;color: var(--blue);"></i> </span>
							        </div>
							      </div>
							      <span class="red"></span>
							    </div>
							  </div>

							  <div class="col-12 col-md-6">
							    <div class="form-group">
							      <label><?php echo $this->lang->line('Favicon'); ?> 
							       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Favicon"); ?>" data-content="<?php echo $this->lang->line("Maximum: 1MB, Format: JPG/PNG, Recommended dimension : 100x100"); ?>"><i class='fa fa-info-circle'></i> </a>
							      </label>
							      <div id="store-favicon-dropzone" class="dropzone mb-1">
							        <div class="dz-default dz-message">
							          <input class="form-control" name="store_favicon" id="store_favicon" type="hidden">
							          <span style="font-size: 20px;"><i class="fas fa-cloud-upload-alt" title='<?php echo $this->lang->line("Upload"); ?>' data-toggle="tooltip" style="font-size: 35px;color: var(--blue);"></i> </span>
							        </div>
							      </div>
							      <span class="red"></span>
							    </div>
							  </div>

							  <div class="form-group col-6 col-md-6 mt-2">
							    <label>
							      <?php echo $this->lang->line("Facebook Pixel ID"); ?>
							       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Facebook Pixel ID"); ?>" data-content="<?php echo $this->lang->line("In Desktop Facebook Messenger, pixel tracking may not work properly as it loads in Facebook iframe."); ?>"><i class='fa fa-info-circle'></i> </a>
							    </label>
							    <input type="text" name="pixel_id" id="pixel_id" class="form-control" placeholder="<?php echo $this->lang->line('Example : '); ?> 1123241077781024">                      
							  </div>

							  <div class="form-group col-6 col-md-6 mt-2">
							    <label>
							      <?php echo $this->lang->line("Google Analytics ID"); ?>
							    </label>
							    <input type="text" name="google_id" id="google_id" class="form-control" placeholder="<?php echo $this->lang->line('Example : '); ?> UA-118292462-1">                      
							  </div>


			                  <div class="col-12 col-md-4">
			  	                  <div class="form-group">
			  	                    <label for="status" > <?php echo $this->lang->line('Status');?> *</label><br>
			  	                    <label class="custom-switch mt-2">
			  	                      <input type="checkbox" name="status" value="1" class="custom-switch-input" checked>
			  	                      <span class="custom-switch-indicator"></span>
			  	                      <span class="custom-switch-description"><?php echo $this->lang->line('Online');?></span>
			  	                      <span class="red"><?php echo form_error('status'); ?></span>
			  	                    </label>
			  	                  </div>
			                  </div>

							  <br><br>
							  <div class="form-group col-12 mt-2">
							    <label type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" class="pointer text-primary" style="font-size: 14px"><b><i class="fa fa-book-reader"></i> <?php echo $this->lang->line('Terms of service'); ?></b></label>							    
							     <div class="collapse" id="collapseExample"><textarea name="terms_use_link"  class="form-control visual_editor"></textarea></div>               
							  </div>

							  <div class="form-group col-12">
							  	<label type="button" data-toggle="collapse" data-target="#collapseExample2" aria-expanded="false" aria-controls="collapseExample2" class="pointer text-primary" style="font-size: 14px"><b><i class="fa fa-hand-holding-usd"></i> <?php echo $this->lang->line('Refund policy'); ?></b></label>
							   <div class="collapse" id="collapseExample2"><textarea name="refund_policy_link"  class="form-control visual_editor"></textarea></div>                 
							  </div>


				  			  <div class="form-group col-12 col-md-8 d-none">
				  			    <label>
				  			      <?php echo $this->lang->line("Select label"); ?>
				  			       <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Select label") ?>" data-content='<?php echo $this->lang->line("Will assign to this label after successful checkout.") ?> <?php echo $this->lang->line("You must select page to fill this list with data."); ?>'><i class='fa fa-info-circle'></i> </a>
				  			    </label>
				  			    <?php echo form_dropdown('label_ids[]',array(), '','style="height:45px;overflow:hidden;width:100%;" multiple="multiple" class="form-control select2" id="label_ids"'); ?>
				  			  </div>	

							</div>
						</div>
						
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<div class="card no_shadow">
						<div class="card-footer p-0">  
							<button class="btn btn-lg btn-primary" id="get_button" name="get_button" type="button"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create Store");?></button>
							<button class="btn btn-lg btn-light float-right" onclick="ecommerceGoBack()" type="button"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel");?></button>
					    </div>
					</div>
				</div>
			</div>

		</form>
	</div>
</section>




<script>
	var base_url="<?php echo site_url(); ?>";
	var action_url = base_url+"ecommerce/add_store_action";
	var success_title = '<?php echo $this->lang->line("Store Created"); ?>';
	$("document").ready(function()	{
		
		$(document).on('blur','#store_name',function(event){
			event.preventDefault();
			var ref=$(this).val();
			$("#email_subject").val(ref+" | <?php echo $this->lang->line('Cart Update'); ?>");

		});

		$(document).on('change','#page',function(event){
			event.preventDefault();

			var page_id=$(this).val();			 
			  $.ajax({
			  type:'POST' ,
			  url: base_url+"ecommerce/get_template_label_dropdown",
			  data: {page_id:page_id},
			  dataType : 'JSON',
			  success:function(response){
			    // $("#template_id").html(response.template_option);
			    $("#label_ids").html(response.label_option);
			    $("#put_script").html(response.script);
			  }

			});
		});
		
		$(document).on('click','#get_button',function(e){
			get_button();
		});


	});
</script>

<?php include(APPPATH.'views/ecommerce/store_style.php'); ?>
<?php include(APPPATH.'views/ecommerce/store_js.php'); ?>