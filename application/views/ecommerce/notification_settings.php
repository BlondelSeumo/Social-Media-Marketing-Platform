<?php include(APPPATH.'views/ecommerce/store_style.php'); ?>
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

	<?php $notification_message = json_decode($xdata['notification_message'],true);	?>

	<div class="section-body">
		<form action="#" enctype="multipart/form-data" id="plugin_form">
			<div class="row">
				<input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
				<div class="col-12">
					<div class="card main_card no_shadow">
						<div class="card-header p-0 mb-3" style="border:none;min-height: 0;">
							<h6 class="full_width">
								<a id="variables" class="float-left text-warning pointer"><i class="fas fa-circle"></i> <?php echo  $this->lang->line("Variables"); ?></a>
								<a onclick="reset_values()" class="float-right text-danger pointer"><i class="fas fa-undo-alt"></i> <?php echo  $this->lang->line("Reset"); ?></a>
							</h6>
						</div>				
						<div class="card-body p-0">
							<ul class="nav nav-tabs" id="sequence_tab" role="tablist">

							  <li class="nav-item">
							    <a class="nav-link active" id="messenger_link" data-toggle="tab" href="#messenger_tab" role="tab" aria-controls="profile" aria-selected="false"><?php echo  $this->lang->line("Messenger"); ?></a>
							  </li>

							  <?php if($this->session->userdata('user_type') == 'Admin' || in_array(264,$this->module_access)) : ?>
							  <li class="nav-item">
							    <a class="nav-link" id="sms_link" data-toggle="tab" href="#sms_tab" role="tab" aria-controls="profile" aria-selected="false"><?php echo  $this->lang->line("SMS"); ?></a>
							  </li>
							  <?php endif; ?>

							  <?php if($this->session->userdata('user_type') == 'Admin' || in_array(263,$this->module_access)) : ?>
								  <li class="nav-item">
								    <a class="nav-link" id="email_link" data-toggle="tab" href="#email_tab" role="tab" aria-controls="profile" aria-selected="false"><?php echo  $this->lang->line("Email"); ?></a>
								  </li>
							  <?php endif; ?>

							</ul>
							<div class="tab-content tab-bordered">

							  <div class="tab-pane fade show active" id="messenger_tab" role="tabpanel" aria-labelledby="messenger_link">
							   
							   <div class="row">				                
				                 <div class="col-12 col-sm-12 col-md-6 col-lg-7">
				                 	<?php echo $this->lang->line("Messenger Notification"); ?>
				                 	<div class="tab-content" id="MsgReminderTabContent">
					                 	 <?php
					                 	 $i=0; 
					                 	 foreach ($notification_list as $key => $value) 
				                       	 { $i++;?>
					                         <div class="reminder_badge_warpper tab-pane fade  <?php if($i==1) echo 'active show';?>" id="msg_reminder<?php echo $i;?>" role="tabpanel" aria-labelledby="msg_reminder_link>">
						                         <span class="reminder_badge" data-toggle="tooltip" title="<?php echo $this->lang->line('Status')." : ".$this->lang->line($value); ?>"><i class="fas fa-bell"></i> <?php echo $i;?></span>											
						                         <div class="reminder_block">
						                         	<span class="block4">
						                         		<textarea style="height: 200px" data-toggle="tooltip" title="<?php echo $this->lang->line('Purchase status update notification will be displayed here, click to edit text. Clearing text will send default message.'); ?>" name="msg_text[]" id="msg_text_<?php echo $value;?>"><?php echo isset($notification_message['messenger'][$value]['text']) ? $notification_message['messenger'][$value]['text'] : $notification_default['messenger']; ?></textarea>	                         	
						                         		<p>
						                         		<input data-toggle="tooltip" title="<?php echo $this->lang->line('Buyer can see his/her order list clicking this button. Click to edit button name.'); ?>" value="<?php echo isset($notification_message['messenger'][$value]['btn']) ? $notification_message['messenger'][$value]['btn'] : 'MY ORDERS'; ?>" class="btn btn-block bg-white" name="msg_btn[]" id="msg_btn_<?php echo $value;?>"/>
						                         		</p>
						                         	</span>
						                         </div>
						                     </div>
					                     <?php 
				                       	 } 
				                       	 ?>

				                    </div>
				                 </div>

         		                 <div class="col-12 col-sm-12 col-md-6 col-lg-5">
         	                       <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
         	                       	 <?php 
         	                       	 $i=0; 
         	                       	 foreach ($notification_list as $key => $value) 
         	                       	 { $i++;?>			                       	  	
         		                       	 <li class="nav-item" style="margin:10px 0 0 0">
                                  			<a style="border-radius: 10px !important;" href="#msg_reminder<?php echo $i;?>"  id="msg_reminder_link<?php echo $i;?>" class="nav-link <?php if($i==1) echo 'active'; ?>" data-toggle="pill" role="tab" aria-controls="msg_reminder<?php echo $i;?>" aria-selected="true"><i class="fas fa-bell"></i> <?php echo $this->lang->line("Messenger");?> : <?php echo $this->lang->line("Order")." (".$this->lang->line($value);?>)</a>
         		                         </li>
         	                       	 <?php 
         	                       	 } 
         	                       	 ?>
         	                       </ul>
         		                 </div>
				               </div>

							  </div>


							  <?php if($this->session->userdata('user_type') == 'Admin' || in_array(264,$this->module_access)) : ?>
							  <div class="tab-pane fade" id="sms_tab" role="tabpanel" aria-labelledby="sms_link">
							  	<div class="row">				                 
				                 <div class="col-12 col-sm-12 col-md-7">
				                 	<?php echo $this->lang->line("SMS Notification"); ?>
				                 	<div class="tab-content" id="SmsReminderTabContent">
					                 	 <?php
					                 	 $i=0; 
					                 	 foreach ($notification_list as $key => $value) 
				                       	 { $i++;?>
					                         <div class="reminder_badge_warpper tab-pane fade <?php if($i==1) echo 'active show';?>" id="sms_reminder<?php echo $i;?>" role="tabpanel" aria-labelledby="sms_reminder_link>">
						                         <span class="reminder_badge" data-toggle="tooltip" title="<?php echo $this->lang->line('Status')." : ".$this->lang->line($value); ?>"><i class="fas fa-bell"></i> <?php echo $i;?></span>									
						                         <div class="reminder_block">
						                         	<span class="block4">
						                         		<textarea data-toggle="tooltip" title="<?php echo $this->lang->line('SMS content goes here. Clear content if you do not want to send.');?>" name="sms_text[]" id="sms_text_<?php echo $value;?>"><?php echo isset($notification_message['sms'][$value]) ? $notification_message['sms'][$value] : $notification_default['sms']; ?></textarea>
						                         	</span>
						                         </div>
						                     </div>
					                     <?php 
				                       	 } 
				                       	 ?>
				                    </div>

				                    <br>
				                    <div class="form-group">
				                    	<div class="label"><?php echo $this->lang->line("SMS Sender"); ?> <i class="fas fa-info-circle" data-toggle="tooltip" title="<?php echo $this->lang->line('SMS settings will not be stored and no sms will be sent if sender is not selected. If selected but not provided other details then system will send default notification.');?>"></i></div>
				                    	<?php echo form_dropdown('notification_sms_api_id', $sms_option,$xdata['notification_sms_api_id'],'class="form-control select2" id="notification_sms_api_id" style="width:100%"'); ?>
				                    </div>
				                 </div>

         		                 <div class="col-12 col-sm-12 col-md-5">
         	                       <ul class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">
         	                       	 <?php 
         	                       	 $i=0; 
         	                       	 foreach ($notification_list as $key => $value) 
         	                       	 { $i++;?>
         		                         <li class="nav-item" style="margin:10px 0 0 0">
                                  			<a style="border-radius: 10px !important;" href="#sms_reminder<?php echo $i;?>"  id="sms_reminder_link<?php echo $i;?>" class="nav-link <?php if($i==1) echo 'active'; ?>" data-toggle="pill" role="tab" aria-controls="sms_reminder<?php echo $i;?>" aria-selected="true"><i class="fas fa-bell"></i> <?php echo $this->lang->line("SMS");?> : <?php echo $this->lang->line("Order")." (".$this->lang->line($value);?>)</a>
         		                         </li>
         	                       	 <?php 
         	                       	 } 
         	                       	 ?>
         	                       </ul>
         		                 </div>
				                </div>
							  </div>
							  <?php endif; ?>



							  <?php if($this->session->userdata('user_type') == 'Admin' || in_array(263,$this->module_access)) : ?>
							  <div class="tab-pane fade" id="email_tab" role="tabpanel" aria-labelledby="email_link">
							    <div class="row">							      
							      <div class="col-12 col-sm-12 col-md-7">
							       	<?php echo $this->lang->line("Email Notification"); ?>
							       	<div class="tab-content" id="EmailReminderTabContent">
							           	 <?php
							           	 	$i=0; 
							           	 	foreach ($notification_list as $key => $value) 
							             	{ $i++;?>
							                   <div class="reminder_badge_warpper tab-pane fade <?php if($i==1) echo 'active show';?> " style="border:none;padding: 0" id="email_reminder<?php echo $i;?>" role="tabpanel" aria-labelledby="email_reminder_link>">
							                       <span class="reminder_badge" data-toggle="tooltip" title="<?php echo $this->lang->line('Status')." : ".$this->lang->line($value); ?>"><i class="fas fa-bell"></i> <?php echo $i;?></span>
							                       <textarea class="visual_editor" data-toggle="tooltip" title="<?php echo $this->lang->line('Email content goes here.');?>" name="email_text[]" id="email_text<?php echo $i;?>"><?php echo isset($notification_message['email'][$value]) ? $notification_message['email'][$value] : $notification_default['email']; ?></textarea>							                       	
							                   </div>
							               	 <?php 
							             	 } 
							             	 ?>

							          </div>
							          <?php 
							          $notification_email_api_id =  $xdata['notification_email_api_id'];
							          $notification_configure_email_table =  $xdata['notification_configure_email_table'];
							          $xnotification_email_api_id = !empty($notification_configure_email_table) ? $notification_configure_email_table.'-'.$notification_email_api_id:'';
							          ?>
				                      <div class="row">
				                      	<div class="col-12 col-md-6">
				                      		  <div class="form-group">
				                      			<div class="label"><?php echo $this->lang->line("Email Sender"); ?> <i class="fas fa-info-circle" data-toggle="tooltip" title="<?php echo $this->lang->line('Email settings will not be stored and no email will be sent if sender is not selected. If selected but not provided other details then system will send default notification.');?>"></i></div>
				                      			<?php echo form_dropdown('notification_email_api_id', $email_option, $xnotification_email_api_id,'class="form-control select2" id="notification_email_api_id" style="width:100%"'); ?>
				                      		  </div>
				                      	</div>
				                      	<div class="col-12 col-md-6">				                      		
				                      			  <div class="form-group">
				                      				<div class="label"><?php echo $this->lang->line("Email Subject"); ?></div>
				                      				<input name="notification_email_subject" id="notification_email_subject" class="form-control" value="<?php echo !empty($xdata['notification_email_subject']) ? $xdata['notification_email_subject'] : "{{store_name}} Order Update";?>">
				                      			  </div>
				                      		</div>
				                      	</div>
							       </div>

							       <div class="col-12 col-sm-12 col-md-5">
							         <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
							         	 <?php 
							         	 $i=0; 
							         	 foreach ($notification_list as $key => $value) 
							         	 { $i++;?>
							               <li class="nav-item" style="margin:10px 0 0 0">
                                  			<a style="border-radius: 10px !important;" href="#email_reminder<?php echo $i;?>"  id="email_reminder_link<?php echo $i;?>" class="nav-link <?php if($i==1) echo 'active'; ?>" data-toggle="pill" role="tab" aria-controls="email_reminder<?php echo $i;?>" aria-selected="true"><i class="fas fa-bell"></i> <?php echo $this->lang->line("Email");?> : <?php echo $this->lang->line("Order")." (".$this->lang->line($value);?>)</a>
         		                         </li>
							         	 <?php 
							         	 } 
							         	 ?>
							         </ul>
							       </div>

							    </div>
							  </div>
							  <?php endif; ?>

							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<div class="card no_shadow">
						<div class="card-footer p-0">  
							<button class="btn btn-lg btn-primary" id="get_button" name="get_button" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
							<button class="btn btn-lg btn-light float-right" onclick="ecommerceGoBack()" type="button"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel");?></button>
					    </div>
					</div>
				</div>
			</div>

		</form>
	</div>
</section>



<?php include(APPPATH.'views/ecommerce/notification_js.php'); ?>