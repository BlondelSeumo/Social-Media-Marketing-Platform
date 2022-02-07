<?php 
  $this->load->view("include/upload_js"); 
  
  $image_upload_limit = 1; 
  if($this->config->item('messengerbot_image_upload_limit') != '')
  $image_upload_limit = $this->config->item('messengerbot_image_upload_limit'); 

?>

<?php include(APPPATH.'views/messenger_tools/bot_list_css.php'); ?>


<section class="section">
  <div class="section-header">
    <h1><?php echo $media_icon.' '.$page_title;?></h1>
    </div>
</section>


<?php if(empty($page_info))
{ ?>
   
<div class="card" id="nodata">
  <div class="card-body">
    <div class="empty-state">
      <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
       <h2 class="mt-0"><?php echo $this->lang->line("We could not find any page.");?></h2>
      <p class="lead"><?php echo $this->lang->line("Please import account if you have not imported yet.")."<br>".$this->lang->line("If you have already imported account then enable bot connection for one or more page to continue.") ?></p>
      <a href="<?php echo base_url('social_accounts'); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo $this->lang->line("Continue");?></a>
    </div>
  </div>
</div>

<?php 
}
else
{ ?>
  <div class="row multi_layout">

    <div class="col-12 col-md-5 col-lg-3 collef">
      <div class="card main_card">
        <div class="card-header">
          <div class="col-6 padding-0">
            <h4><?php if($media_type =='ig') echo $this->lang->line("Accounts"); else echo $this->lang->line("Pages"); ?></h4>
          </div>
          <div class="col-6 padding-0">            
            <input type="text" class="form-control float-right" id="search_page_list" onkeyup="search_in_ul(this,'page_list_ul')" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>">
          </div>
        </div>
        <div class="card-body padding-0">
          <div class="makeScroll">
            <ul class="list-group" id="page_list_ul">
              <?php $i=0; foreach($page_info as $value) { ?> 
                <li class="list-group-item <?php if($i==0 || $value['id']==$this->session->userdata('selected_global_page_table_id')) echo 'active'; ?> page_list_item" page_table_id="<?php echo $value['id']; ?>">
                  <div class="row">
                    <div class="col-3 col-md-2"><img width="45px" class="rounded-circle" src="<?php echo $value['page_profile']; ?>"></div>
                    <div class="col-9 col-md-10">
                      <h6 class="page_name">
                        <?php 
                          if(addon_exist($module_id=320,$addon_unique_name="instagram_bot")) {
                            if(isset($media_type) && $media_type == "ig") {
                              echo $value['insta_username'];
                            }
                            else {
                              echo $value['page_name'];
                            }
                          }
                          else {
                            echo $value['page_name'];
                          }

                        ?>
                      </h6>
                      <span class="gray fb_page_id"><?php echo $value['page_id']; ?></span>
                      </div>
                    </div>
                </li> 
                <?php $i++; } ?>                
            </ul>
          </div>
        </div>
      </div>          
    </div>

    <div class="col-12 col-md-7 col-lg-3 colmid" id="middle_column">

      <div class="text-center waiting">
        <i class="fas fa-spinner fa-spin blue text-center"></i>
      </div>

      <div id="middle_column_content"></div>
    </div>

    <div class="col-12 col-md-12 col-lg-6 colrig" id="right_column">

      <div class="text-center waiting">
        <i class="fas fa-spinner fa-spin blue text-center"></i>
      </div>

      <div class="card main_card">
        <div class="card-header padding-left-10 padding-right-10">
          <div class="col-4 padding-0">
            <h4 id="right_column_title"></h4>            
          </div>
          
          <?php if($custom_field_exist == 'yes') : ?>
          <div class="col-4 padding-0 text-center">
            <a class="btn btn-outline-primary variables" href="#"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Variables'); ?></a>
          </div>
          <?php else : ?>
          <div class="col-4 padding-0 text-center">
          </div>
          <?php endif; ?>
          
          <div class="col-4 padding-0">
            <a href="#" data-toggle="dropdown" class="btn btn-outline-primary dropdown-toggle float-right"><?php echo $this->lang->line("Options");?></a> 
            <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="width: 250px;">
              <div class="dropdown-title"><?php echo $this->lang->line("Actions");?></div>
              <?php if($media_type != 'ig') : ?>
              <li><a class="dropdown-item has-icon analytics_page" href="#"> <i class="far fa-chart-bar"></i> <?php echo $this->lang->line("Page Analytics");?></a></li>
              <li><a class="dropdown-item has-icon analytics_bot" href="#"><i class="fas fa-chart-pie"></i> <?php echo $this->lang->line("Messenger bot analytics");?></a></li>
              <?php endif; ?>

              <?php if($this->session->userdata('user_type') == 'Admin' || in_array(257,$this->module_access)) : ?>
              <li><a class="dropdown-item has-icon export_bot" media_type="<?php echo $media_type; ?>" table_id="" href="#"><i class="fas fa-file-export"></i> <?php echo $this->lang->line("Export bot settings");?></a></li>
              <li><a class="dropdown-item has-icon" media_type="<?php echo $media_type; ?>" table_id="" href="<?php echo base_url('messenger_bot/saved_templates/').$media_type; ?>"><i class="fas fa-file-import"></i> <?php echo $this->lang->line("Import bot settings");?></a></li>
              <?php endif; ?>
           </ul>
          </div>
        </div>

        <div class="card-body" style="padding: 10px 17px 10px 10px;">
          <div class="row">
            <div class="col-12">

              <div id="right_column_content">              
                <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>

                <div id="right_column_bottom_content" style="display: none;">
                  <style>
                    .wizard-steps .wizard-step:before {
                      content: none !important;
                    }
                  </style>
                  <div class="" id="action_button_settings_block" data-backdrop="static" data-keyboard="false" style="display: none; padding: 0px;">
                    <div class="row" id="action_button_settings_lists"></div>
                    <!-- <div class="row">
                      <div class="col-12 col-md-6">
                          <a href="#" class="pointer">
                            <div class="card card-large-icons card-condensed">
                              <div class="card-icon">
                                <i class="fas fa-check-square"></i>
                              </div>
                              <div class="card-body">
                                <h4><?php echo $this->lang->line('Checkbox Plugin'); ?></h4>
                              </div>
                            </div>
                          </a>

                      </div>
                      <div class="col-12 col-md-6">
                        <a href="#" class="pointer">
                          <div class="card card-large-icons card-condensed">
                            <div class="card-icon">
                              <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="card-body">
                              <h4><?php echo $this->lang->line('Send to Messenger'); ?></h4>
                            </div>
                          </div>
                        </a>
                      </div>
                      <div class="col-12 col-md-6">
                        <a href="#" class="pointer">
                          <div class="card card-large-icons card-condensed">
                            <div class="card-icon">
                              <i class="fas fa-link"></i>
                            </div>
                            <div class="card-body">
                              <h4><?php echo $this->lang->line('M.me Link'); ?></h4>
                            </div>
                          </div>
                        </a>
                      </div>
                      <div class="col-12 col-md-6">
                        <a href="#" class="pointer">
                          <div class="card card-large-icons card-condensed">
                            <div class="card-icon">
                              <i class="fas fa-comments"></i>
                            </div>
                            <div class="card-body">
                              <h4><?php echo $this->lang->line('Customer Chat Plugin'); ?></h4>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div> -->
                  </div>

                  <div class="settings_block" id="sequence_message_settings_block" data-backdrop="static" data-keyboard="false" style="display: none; padding: 0px;">
                    <div class="row" id="sequence_message_settings_lists">
                      <div class="col-12 col-md-6">
                        <a href="http://localhost/xerochat/messenger_bot/edit_template/2272/1/default" class="pointer iframed" data-height="795" target="_BLANK">
                          <div class="card card-large-icons card-condensed">
                            <div class="card-icon" style="width:70px !important;">
                              <i class="fas fa-robot"></i>
                            </div>
                            <div class="card-body">
                              <h4>Chat with Robot Template</h4>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                  
                  <div class="settings_block" id="messenger_engagement_settings_block" data-backdrop="static" data-keyboard="false" style="display: none; padding: 0px;">
                    <div class="row" id="messenger_engagement_settings_lists"></div>
                  </div>
                  
                  <div class="settings_block" id="user_input_settings_block" data-backdrop="static" data-keyboard="false" style="display: none; padding: 0px;">
                    <div class="row" id="user_input_settings_lists"></div>
                  </div>


                  <div class="" id="enable_start_button_modal" data-backdrop="static" data-keyboard="false" style="display: none; padding: 0px;">
                      <form id="getstarted_icebreaker_form" method="POST">
                        <input type="hidden" name="ice_breaker_for" id="ice_breaker_for" value="<?php echo $media_type; ?>">
                        <div class="modal-dialog modal-full" style="margin: 0 !important; min-width: 100%;">
                            <div class="modal-content no_shadow">
                                <div class="modal-body padding-0" id="enable_start_button_modal_body">

                                  <div class="<?php if($media_type == "ig") echo "hidden"; ?>">
                                    <div class="section">                
                                       <h2 class="section-title"  style="margin-top:0"><?php echo $this->lang->line('Get started & welcome message'); ?></h2>
                                       <!-- <p>&nbsp;</p>                                    -->
                                     </div>
                                     <br>
                                     <div class="form-group">
                                       <label><?php echo $this->lang->line('Get started button status');?></label>
                                       <select class="form-control" name="started_button_enabled" id="started_button_enabled">
                                         <option value="1"><?php echo $this->lang->line("enabled");?></option>
                                         <option value="0"><?php echo $this->lang->line("disabled");?></option>
                                       </select>
                                     </div>
                                    

                                     <div class=""  id="delay_con2">
                                       <div class="form-group">
                                         <label>
                                           <?php echo $this->lang->line('Welcome Message');?>
                                           <a href="#" data-placement="bottom" data-html="true"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Welcome Message") ?>" data-content="<?php echo $this->lang->line("The greeting text on the welcome screen is your first opportunity to tell a person why they should start a conversation with your Messenger bot. Some things you might include in your greeting text might include a brief description of what your bot does, such as key features, or a tagline. This is also a great place to start establishing the style and tone of your bot.Greetings have a 160 character maximum, so keep it concise.")."<br><br>".$this->lang->line("Variables")." : <br>{{user_first_name}}<br>{{user_last_name}}<br>{{user_full_name}}"; ?>">&nbsp;&nbsp;<i class='fa fa-info-circle'></i> </a>
                                         </label>
                                    
                                    
                                           <span class='float-right'> 
                                             <a title="<?php echo $this->lang->line("You can include {{user_last_name}} variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm getstarted_lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                                           </span>
                                           <span class='float-right'> 
                                             <a title="<?php echo $this->lang->line("You can include {{user_first_name}} variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm getstarted_lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                                           </span> 
                                    
                                           <div class="clearfix"></div>      
                                    
                                         <textarea name="welcome_message" id="welcome_message" class="form-control" style="height:100px;"></textarea>
                                         <a href="#" class="btn btn-outline-warning float-right btn-sm iframed" id="getstarted_button_edit_url"><i class="far fa-edit"></i> <?php echo $this->lang->line("edit get started reply");?></a>   
                                       </div>
                                     </div>
                                    <br><br>
                                  </div>


                                    <!-- ice breaker section -->
                                    <div class="section">                
                                      <h2 class="section-title"><?php echo $this->lang->line('Ice breakers'); ?> <i class="fas fa-info-circle text-primary" id="ice_breaker_info"></i></h2>
                                      <p><?php echo $this->lang->line('FAQ : Frequently Asked Questions'); ?>   </p>                                   
                                    </div>
                                    <br>

                                		<div class="input-group" style="margin-bottom: 15px;">                            
                                		  <div class="input-group-prepend">
                                		    <div class="input-group-text" style="font-weight: bold;">
                                		      <?php echo $this->lang->line("Ice Breakers Status"); ?>
                                		    </div>
                                		  </div>
                                		  <select class="form-control" id="ice_breaker_status" name="ice_breaker_status">
                                		  	<option value="1"><?php echo $this->lang->line('Enabled'); ?></option>
                                		  	<option value="0"><?php echo $this->lang->line('Disabled'); ?></option>
                                  	  </select>
                                		</div>

                                		<div id="questionaries_block">

                                		</div>

                                    	
                                    <!-- end of ice breaker section -->
                                    	
                                    <br>
                                    <input type="hidden" name="page_info_table_id_icebreaker" id="page_info_table_id_icebreaker">
                                    <div class="clearfix"> 
                                        <a href="#" target="_BLANK" id="enable_start_button_submit" class="btn-lg btn btn-primary float-left"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("save");?></a>            
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                      </form>
                  </div>




                  <div class="" id="mark_seen_chat_settings" data-backdrop="static" data-keyboard="false" style="display: none; padding: 0px;">
                      <div class="modal-dialog modal-full" style="margin: 0 !important; min-width: 100%;">
                          <div class="modal-content no_shadow">
                              <div class="modal-body padding-0 ">
                           
                                    <div class="form-group <?php if($media_type == "ig") echo "hidden"; ?>">
                                      <label><?php echo $this->lang->line('Mark as seen status');?></label>
                                      <select class="form-control" name="mark_seen_status" id="mark_seen_status">
                                        <option value="1"><?php echo $this->lang->line("enabled");?></option>
                                        <option value="0"><?php echo $this->lang->line("disabled");?></option>
                                      </select>
                                    </div>
                               

                                    <div class="form-group">
                                      <label>
                                        <?php echo $this->lang->line('Chat with human Email');?>
                                      </label>
                                      <input type="text" class="form-control" name="chat_human_email" id="chat_human_email">
                                    </div>

                                    <div class="form-group">
                                      <label class="custom-switch">
                                        <input type="checkbox" name="no_match_found_reply" value="enabled" id="no_match_found_reply" class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description"><?php echo $this->lang->line('Reply if no match found');?></span>
                                      </label>
                                    </div>

                                    <?php if($this->session->userdata('user_type') == 'Admin' || in_array(265,$this->module_access)) : ?>
                                      <div class="<?php if($media_type == "ig") echo "hidden"; ?>">
                                        <div class="section">                
                                          <h2 class="section-title"><?php echo $this->lang->line('MailChimp Integration'); ?> <span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/mailchimp_list'); ?>" target="_BLANK"><?php echo $this->lang->line('Add MailChimp API'); ?></a></span></h2>
                                          <p><?php echo $this->lang->line('Send collected email from Quick Reply to your MailChimp account list. Page Name will be added as Tag Name in your MailChimp list.'); ?></p>
                                        </div>
                                        <div class="form-group">
                                          <label style="width: 100%;">
                                            <?php echo $this->lang->line("Select MailChimp List"); ?>
                                            <a href="" class="text-danger float-right error_log_report2" data-type="Email Autoresponder" data-service="Mailchimp"><i class="fas fa-history"></i> <?php echo $this->lang->line('API Log'); ?></a>                                        
                                          </label>
                                          <select class="form-control select2" id="mailchimp_list_id" name="mailchimp_list_id[]" multiple="">
                                            <?php 
                                            // echo "<option value='0'>".$this->lang->line('Choose a List')."</option>";
                                            foreach ($mailchimp_list as $key => $value) 
                                            {
                                              echo '<optgroup label="'.addslashes($value['tracking_name']).'">';
                                              foreach ($value['data'] as $key2 => $value2) 
                                              {
                                                if(in_array($value2['table_id'], $selected_mailchimp_list_ids)) $selected = 'selected';
                                                else $selected = '';
                                                echo "<option value='".$value2['table_id']."' ".$selected.">".$value2['list_name']."</option>";
                                              }
                                              echo '</optgroup>';
                                            } ?>
                                          </select>
                                        </div> 

                                        <div class="section">                
                                          <h2 class="section-title"><?php echo $this->lang->line('Sendinblue Integration'); ?> <span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/sendinblue_list'); ?>" target="_BLANK"><?php echo $this->lang->line('Add Sendinblue API'); ?></a></span></h2>
                                          <p><?php echo $this->lang->line('Select Sendinblue list where email will be sent when user signup.'); ?></p>
                                        </div>
                                        <div class="form-group">
                                          <label style="width: 100%;">
                                            <?php echo $this->lang->line("Select Sendinblue List"); ?>
                                            <a href="" class="text-danger float-right error_log_report2" data-type="Email Autoresponder" data-service="Sendinblue"><i class="fas fa-history"></i> <?php echo $this->lang->line('API Log'); ?></a>                                        
                                          </label>
                                          <select class="form-control select2" id="sendinblue_list_id2" name="sendinblue_list_id2[]" multiple="">
                                            <?php 
                                            // echo "<option value='0'>".$this->lang->line('Choose a List')."</option>";
                                            foreach ($sendinblue_list as $first_key => $first_value) 
                                            {
                                              echo '<optgroup label="'.addslashes($first_value['tracking_name']).'">';
                                              foreach ($first_value['data'] as $second_key => $second_value) 
                                              {
                                                if(in_array($second_value['table_id'], $selected_sendinblue_list_ids)) $selected = 'selected';
                                                else $selected = '';
                                                echo "<option value='".$second_value['table_id']."' ".$selected.">".$second_value['list_name']."</option>";
                                              }
                                              echo '</optgroup>';
                                            } ?>
                                          </select>
                                        </div> 

                                        <div class="section">                
                                          <h2 class="section-title"><?php echo $this->lang->line('Activecampaign Integration'); ?> <span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/activecampaign_list'); ?>" target="_BLANK"><?php echo $this->lang->line('Add Activecampaign API'); ?></a></span></h2>
                                          <p><?php echo $this->lang->line('Select Activecampaign list where email will be sent when user signup.'); ?></p>
                                        </div>
      
                                        <div class="form-group">
                                          <label style="width: 100%;">
                                            <?php echo $this->lang->line("Select Activecampaign List"); ?>
                                            <a href="" class="text-danger float-right error_log_report2" data-type="Email Autoresponder" data-service="Activecampaign"><i class="fas fa-history"></i> <?php echo $this->lang->line('API Log'); ?></a>                                        
                                          </label>
                                          <select class="form-control select2" id="activecampaign_list_id2" name="activecampaign_list_id2[]" multiple="">
                                            <?php 
                                            // echo "<option value='0'>".$this->lang->line('Choose a List')."</option>";
                                            foreach ($activecampaign_list as $key => $value) 
                                            {
                                              echo '<optgroup label="'.addslashes($value['tracking_name']).'">';
                                              foreach ($value['data'] as $key2 => $value2) 
                                              {
                                                if(in_array($value2['table_id'], $selected_activecampaign_list_ids)) $selected = 'selected';
                                                else $selected = '';
                                                echo "<option value='".$value2['table_id']."' ".$selected.">".$value2['list_name']."</option>";
                                              }
                                              echo '</optgroup>';
                                            } ?>
                                          </select>
                                        </div> 

                                        <div class="section">                
                                          <h2 class="section-title"><?php echo $this->lang->line('Mautic Integration'); ?> <span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/mautic_list'); ?>" target="_BLANK"><?php echo $this->lang->line('Add Mautic API'); ?></a></span></h2>
                                          <p><?php echo $this->lang->line('Select Mautic list where email will be sent when user signup. Page name will be added as tag name in your Mautic list.'); ?></p>
                                        </div>
                                        <div class="form-group">
                                          <label style="width: 100%;">
                                            <?php echo $this->lang->line("Select Mautic List"); ?>
                                            <a href="" class="text-danger float-right error_log_report2" data-type="Email Autoresponder" data-service="Mautic"><i class="fas fa-history"></i> <?php echo $this->lang->line('API Log'); ?></a>                                        
                                          </label>
                                          <select class="form-control select2" id="mautic_list_id2" name="mautic_list_id2[]" multiple="">
                                            <?php 
                                            // echo "<option value='0'>".$this->lang->line('Choose a List')."</option>";
                                            foreach ($mautic_list as $key => $value) 
                                            {
                                              echo '<optgroup label="'.addslashes($value['tracking_name']).'">';
                                              foreach ($value['data'] as $key2 => $value2) 
                                              {
                                                if(in_array($value2['table_id'], $selected_mautic_list_ids)) $selected = 'selected';
                                                else $selected = '';
                                                echo "<option value='".$value2['table_id']."' ".$selected.">".$value2['list_name']."</option>";
                                              }
                                              echo '</optgroup>';
                                            } ?>
                                          </select>
                                        </div> 

                                        <div class="section">                
                                          <h2 class="section-title"><?php echo $this->lang->line('Acelle Integration'); ?> <span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/acelle_list'); ?>" target="_BLANK"><?php echo $this->lang->line('Add Acelle API'); ?></a></span></h2>
                                          <p><?php echo $this->lang->line('Select Acelle list where email will be sent when user signup.'); ?></p>
                                        </div>
                                        <div class="form-group">
                                          <label style="width: 100%;">
                                            <?php echo $this->lang->line("Select Acelle List"); ?>
                                            <a href="" class="text-danger float-right error_log_report2" data-type="Email Autoresponder" data-service="Acelle"><i class="fas fa-history"></i> <?php echo $this->lang->line('API Log'); ?></a>                                        
                                          </label>
                                          <select class="form-control select2" id="acelle_list_id2" name="acelle_list_id2[]" multiple="">
                                            <?php 
                                            // echo "<option value='0'>".$this->lang->line('Choose a List')."</option>";
                                            foreach ($acelle_list as $key => $value) 
                                            {
                                              echo '<optgroup label="'.addslashes($value['tracking_name']).'">';
                                              foreach ($value['data'] as $key2 => $value2) 
                                              {
                                                if(in_array($value2['table_id'], $selected_acelle_list_ids)) $selected = 'selected';
                                                else $selected = '';
                                                echo "<option value='".$value2['table_id']."' ".$selected.">".$value2['list_name']."</option>";
                                              }
                                              echo '</optgroup>';
                                            } ?>
                                          </select>
                                        </div> 
                                      </div>
                                    <?php endif; ?>

                                    <?php if($this->session->userdata('user_type') == 'Admin' || in_array(264,$this->module_access)) : ?>
                                      <div class="<?php if($media_type == "ig") echo "hidden"; ?>">
                                        <div class="section">                
                                          <h2 class="section-title"><?php echo $this->lang->line('SMS Integration'); ?> <span style="font-size: 12px !important;"><a href="<?php echo base_url('sms_email_manager/sms_api_lists'); ?>" target="_BLANK"><?php echo $this->lang->line('Add SMS API'); ?></a></span></h2>
                                          <p><?php echo $this->lang->line('Send automated SMS to users who provide phone number through Quick Reply.'); ?></p>
                                        </div>
                                        <div class="form-group">
                                          <label style="width: 100%;">
                                            <?php echo $this->lang->line("Select SMS API"); ?>
                                            <a href="" class="text-danger float-right error_log_report2" data-type="SMS Sender"><i class="fas fa-history"></i> <?php echo $this->lang->line('API Log'); ?></a>                                        
                                          </label>

                                          <select class="form-control select2" id="sms_api_id" name="sms_api_id">
                                            <option value=''><?php echo $this->lang->line('Select API');?></option>
                                            <?php 
                                                foreach($sms_option as $id=>$option)
                                                {
                                                  $selected = '';
                                                  if($id == $sms_api_id) $selected = 'selected';
                                                  echo "<option value='{$id}' {$selected}>{$option}</option>";
                                                }
                                            ?>
                                          </select>
                                        </div> 
                                        <div class="form-group">
                                          <label> <?php echo $this->lang->line('SMS Reply Message');?> </label>

                                            <span class='float-right'> 
                                              <a title="<?php echo $this->lang->line("You can include {{user_last_name}} variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm sms_api_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                                            </span>
                                            <span class='float-right'> 
                                              <a title="<?php echo $this->lang->line("You can include {{user_first_name}} variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm sms_api_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                                            </span> 

                                            <div class="clearfix"></div>      

                                          <textarea name="sms_reply_message" id="sms_reply_message" class="form-control" style="height:100px;"><?php echo $sms_reply_message; ?></textarea>

                                        </div>
                                      </div>
                                    <?php endif; ?> 


                                    <?php if($this->session->userdata('user_type') == 'Admin' || in_array(263,$this->module_access)) : ?>
                                    <div class="<?php if($media_type == "ig") echo "hidden"; ?>">
                                        <div class="section">                
                                          <h2 class="section-title"><?php echo $this->lang->line('Email Integration'); ?> <span style="font-size: 12px !important;"><a href="<?php echo base_url('messenger_bot_broadcast/index'); ?>" target="_BLANK"><?php echo $this->lang->line('Add Email API'); ?></a></span></h2>
                                          <p><?php echo $this->lang->line('Send automated Email to users who provide email address through Quick Reply.'); ?></p>
                                        </div>
                                      <div class="form-group">
                                        <label style="width: 100%;">
                                          <?php echo $this->lang->line("Select Email API"); ?>
                                          <a href="" class="text-danger float-right error_log_report2" data-type="Email Sender"><i class="fas fa-history"></i> <?php echo $this->lang->line('API Log'); ?></a>                                        
                                        </label>

                                        <select class="form-control select2" id="email_api_id" name="email_api_id">
                                          <option value=''><?php echo $this->lang->line('Select API');?></option>
                                          <?php 
                                              foreach($email_apis as $id=>$option)
                                              {
                                                echo "<option value='{$id}'>{$option}</option>";
                                              }
                                          ?>
                                        </select>
                                      </div> 
                                      <div class="form-group">
                                        <label> <?php echo $this->lang->line('Email Reply Message');?> </label>

                                          <span class='float-right'> 
                                            <a title="<?php echo $this->lang->line("You can include {{user_last_name}} variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm email_api_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                                          </span>
                                          <span class='float-right'> 
                                            <a title="<?php echo $this->lang->line("You can include {{user_first_name}} variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm email_api_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                                          </span> 

                                          <div class="clearfix"></div>      

                                        <textarea name="email_reply_message" id="email_reply_message" class="form-control" style="height:100px;"></textarea>
                                      </div>

                                      <div class="form-group">
                                        <label>
                                          <?php echo $this->lang->line("Email Subject"); ?>                                      
                                        </label>
                                        <input type="text" id="email_reply_subject" name="email_reply_subject" class="form-control">
                                      </div> 
                                    </div>
                                    <?php endif; ?> 

                                    <?php if($this->is_sms_email_drip_exist) : ?>
                                      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(270,$this->module_access)) : ?>
                                      <div class="<?php if($media_type == "ig") echo "hidden"; ?>">
                                        <div class="section">                
                                          <h2 class="section-title"><?php echo $this->lang->line('SMS Sequence Integration'); ?> </h2>
                                          <p><?php echo $this->lang->line('Set SMS sequence campaign for users, who provide phone number address through quick reply or post-back button.'); ?></p>
                                        </div>
                                        <div class="form-group">
                                          <label style="width: 100%;">
                                            <?php echo $this->lang->line("Select SMS API"); ?>                                    
                                          </label>
                                          <select class="form-control select2" id="sequence_sms_api_id" name="sequence_sms_api_id" style="width:100%;">
                                            <option value=''><?php echo $this->lang->line('Select API');?></option>
                                            <?php 
                                                foreach($sms_option as $id=>$option)
                                                {
                                                  $selected = '';
                                                  if($id == $sequence_sms_api_id) $selected = 'selected';
                                                  echo "<option value='{$id}' {$selected}>{$option}</option>";
                                                }
                                            ?>
                                          </select>
                                        </div>
                                        <div class="form-group">
                                          <label style="width: 100%;"><?php echo $this->lang->line("Select Sequence Campaign"); ?> <small><a href="" class="refresh_campaign_lists" cam_type="sms"><?php echo $this->lang->line("Refresh Lists"); ?></a></small></label>
                                          <div id="sequence_sms_campaign_div"></div>
                                        </div>
                                      </div>
                                         
                                      <?php endif; ?>


                                      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(271,$this->module_access)) : ?>
                                        <div class="<?php if($media_type == "ig") echo "hidden"; ?>">
                                          <div class="section">                
                                            <h2 class="section-title"><?php echo $this->lang->line('Email Sequence Integration'); ?> </h2>
                                            <p><?php echo $this->lang->line('Set email sequence campaign for users, who provide email address through quick reply or post-back button.'); ?></p>
                                          </div>
                                          <div class="form-group">
                                            <label style="width: 100%;">
                                              <?php echo $this->lang->line("Select Email API"); ?>                                    
                                            </label>

                                            <select class="form-control select2" id="sequence_email_api_id" name="sequence_email_api_id" style="width:100%;">
                                              <option value=''><?php echo $this->lang->line('Select API');?></option>
                                              <?php 
                                                  foreach($email_apis as $id=>$option)
                                                  {
                                                    $selected = '';
                                                    if($id == $sequence_email_api_id) $selected = 'selected';
                                                    echo "<option value='{$id}' {$selected}>{$option}</option>";
                                                  }
                                              ?>
                                            </select>
                                          </div>
                                          <div class="form-group">
                                            <label style="width: 100%;"><?php echo $this->lang->line("Select Sequence Campaign"); ?> <small><a href="" class="refresh_campaign_lists" cam_type="email"><?php echo $this->lang->line("Refresh Lists"); ?></a></small></label>
                                            <div id="sequence_email_campaign_div"></div>
                                          </div>
                                        </div>
                                        
                                      <?php endif; ?>

                                    <?php endif; ?>

                                  
                                    <a href="#" id="mark_seen_save_button" class="btn-lg btn btn-primary"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("Save");?></a>
                                    
                                  <div class="clearfix"></div>
                              </div>
                          </div>
                      </div>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
    <input type="hidden" name="hidden_media_type" id="hidden_media_type" value="<?php echo $media_type; ?>">
  </div>

<?php } ?>



<script type="text/javascript">
  function htmlspecialchars_decode(str) 
  {
     if (typeof(str) == "string") 
     {
      str = str.replace("&amp;",/&/g); 
      str = str.replace("&quot;",/"/g);
      str = str.replace("&#039;",/'/g);
      str = str.replace("&#92;",/\\/g);
      str = str.replace("&lt;",/</g);
      str = str.replace("&gt;",/>/g);
      }
     return str;
  }

  function refresh_sequence_list(page_table_id,campaign_type,current_campaign_id) {
    $.ajax({
      type:'POST' ,
      url: "<?php echo base_url(); ?>"+"messenger_bot/refresh_sequence_campaign_lists",
      data: {page_table_id:page_table_id,campaign_type:campaign_type,current_campaign_id:current_campaign_id},
      success:function(response){
        if(campaign_type == "sms") {
          $("#sequence_sms_campaign_id").html(response);
        }

        if(campaign_type == "email") {
          $("#sequence_email_campaign_id").html(response);
        }
      }
    });
  }

  $(document).ready(function(){

    var base_url = "<?php echo base_url(); ?>";

    $(document).on('shown.bs.collapse', '.collapse', function (e) {
      // $(this).parent().prev().children().css("font-size",'40px');
      $(".collapse").not(this).collapse('hide');
    });


    $(document).on('hidden.bs.collapse', '.collapse', function (e) {
      // $(this).parent().prev().children().css("font-size",'22px');

    });


    $(document).on('click','.variables',function(e){
      $("#variable_display_section").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div>');
      $('#variable_data_modal').modal();
      var media_type = "<?php echo $media_type; ?>";
      $.ajax({
        context: this,
        type:'POST',
        // dataType:'JSON',
        url:"<?php echo site_url();?>custom_field_manager/ajax_get_variables",
        data:{media_type:media_type},
        success:function(response){
          $("#variable_display_section").html(response);
        }
      });
    });

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
          window.location.assign('<?php echo base_url('messenger_bot/bot_list'); ?>');
        }
      });
    });

    $(document).on('click', '.refresh_campaign_lists', function(event) {
      event.preventDefault();
      var page_table_id = $(".page_list_item.active").attr('page_table_id');
      var campaign_type = $(this).attr("cam_type");
      if(campaign_type == 'sms') var current_campaign_id = $("#sequence_sms_campaign_id").val();
      if(campaign_type == 'email') var current_campaign_id = $("#sequence_email_campaign_id").val();

      refresh_sequence_list(page_table_id,campaign_type,current_campaign_id);
    });

    $(document).on('click', '#add_more_question_button', function(event) {
    	event.preventDefault();
    	
    	var question_block_counter = $("#question_block_counter").val();
    	question_block_counter = parseInt(question_block_counter, 10) + 1;

	    var postback_drop_down = '';
    	var page_table_id = $(".page_list_item.active").attr('page_table_id');

      var hidden_media_type = $("#hidden_media_type").val();
      var get_data_url = base_url+"messenger_bot/get_postback";
      if(hidden_media_type == 'ig')
       get_data_url = base_url+"messenger_bot/get_ig_postback";

    	$.ajax({
    	  type:'POST' ,
    	  url: get_data_url,
    	  data: {page_id:page_table_id,is_from_add_button:'0'},
    	  success:function(response){
          var upper_limit = 0;
          if(hidden_media_type == 'ig') upper_limit = 5;
          else upper_limit = 11;
          
  	    	if (question_block_counter < upper_limit) 
          {
  	    		$('.add_more_question_block').before('<div class="single_question_block"><p class="clearfix"><b><?php echo $this->lang->line('Question Block'); ?></b> <button class="btn btn-sm btn-outline-secondary float-right remove_question_div"><i class="fas fa-times"></i> <?php echo $this->lang->line('Remove'); ?></button></p><div class="input-group" style="margin-bottom: 5px;"><div class="input-group-prepend"><div class="input-group-text" style="font-weight: bold;"><?php echo $this->lang->line("Type your question"); ?></div></div><input class="form-control" type="text" name="questions[]"></div><div class="input-group"><div class="input-group-prepend"><div class="input-group-text" style="font-weight: bold;"><?php echo $this->lang->line("Reply Message Template"); ?></div></div><select class="form-control" id="select_tag_id_'+question_block_counter+'" name="question_replies[]">'+response+'</select></div><a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template"); ?></a><a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List"); ?></a><br/></div>');

  	    		$("#question_block_counter").val(question_block_counter);
  	    	}
  	    	else 
  	    		$("#add_more_question_button").attr('disabled', 'true');

      	  }
    	});


    });

    $(document).on('click', '.remove_question_div', function(event) {
    	event.preventDefault();
    	
    	var parent_div = $(this).parent().parent();
    	$(parent_div).remove();

    	var question_block_counter = parseInt($("#question_block_counter").val(), 10);
    	$("#question_block_counter").val(question_block_counter - 1);
    	$("#add_more_question_button").removeAttr('disabled');
    });

    $(document).on('change','#ice_breaker_status',function(){
      var ice_breaker_status = $(this).val();
      if(ice_breaker_status=='1') $("#questionaries_block").show();
      else $("#questionaries_block").hide();
    });

    $(document).on('click', '#ice_breaker_info', function(event)
    {
      $("#ice_breaker_info_modal").modal();
    });


    $(document).on('click','.add_template',function(e){
      e.preventDefault();
      var select_tag=$(this).prev()[0].children.item(1);
      var current_id=$(select_tag).attr('id');
      var current_val=$(select_tag).val();
      var page_id = $(".page_list_item.active").attr('page_table_id');
      if(page_id=="")
      {
        swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
        return false;
      }
      $("#add_template_modal").attr("current_id",current_id);
      $("#add_template_modal").attr("current_val",current_val);
      $("#add_template_modal").modal();
    });

    $(document).on('click','.ref_template',function(e){
      e.preventDefault();
      var select_tag=$(this).prev().prev()[0].children.item(1);
      var current_val=$(select_tag).val();
      var current_id=$(select_tag).attr('id');

      var page_id = $(".page_list_item.active").attr('page_table_id');

       if(page_id=="")
       {
         swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
         return false;
       }

       var hidden_media_type = $("#hidden_media_type").val();
       var get_data_url = base_url+"messenger_bot/get_postback";
       if(hidden_media_type == 'ig')
        get_data_url = base_url+"messenger_bot/get_ig_postback";

       $.ajax({
         type:'POST',
         url: get_data_url,
         data: {page_id:page_id},
         // dataType: 'JSON',
         success:function(response){
           $("#"+current_id).html(response).val(current_val);
         }
       });
    });

    $('#add_template_modal').on('hidden.bs.modal', function (e) { 
      var current_id=$("#add_template_modal").attr("current_id");
      var current_val=$("#add_template_modal").attr("current_val");
      var page_id = $(".page_list_item.active").attr('page_table_id');
       if(page_id=="")
       {
         swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
         return false;
       }

       var hidden_media_type = $("#hidden_media_type").val();
       var get_data_url = base_url+"messenger_bot/get_postback";
       if(hidden_media_type == 'ig')
        get_data_url = base_url+"messenger_bot/get_ig_postback";

       $.ajax({
         type:'POST' ,
         url: get_data_url,
         data: {page_id:page_id,is_from_add_button:'1'},
         // dataType: 'JSON',
         success:function(response){
           $("#"+current_id).html(response);
         }
       });
    });

    // getting postback list and making iframe
    $('#add_template_modal').on('shown.bs.modal',function(){ 
    	var page_id = $(".page_list_item.active").attr('page_table_id');
      var media_type = "<?php echo $media_type; ?>";
      var rand_time="<?php echo time(); ?>";
      var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id+"/0/"+media_type+"?lev="+rand_time;
    	// var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id;
      	$(this).find('iframe').attr('src',iframe_link); 
    });   
    // getting postback list and making iframe



    $("#mailchimp_list_id,#sms_api_id,#email_api_id,#sendinblue_list_id2,#activecampaign_list_id2,#mautic_list_id2,#acelle_list_id2").select2({ width: "100%" });

    $(".page_list_item").click(function(e) {
      e.preventDefault();
      $('#middle_column .waiting').show();
      $('#middle_column_content').hide();
      $('#right_column .waiting').show();
      $('#right_column .main_card').hide();

      var page_table_id = $(this).attr('page_table_id');
      $('.page_list_item').removeClass('active');
      $(this).addClass('active');

      var media_type = "<?php echo $media_type; ?>";

      var analytics_bot_href = "<?php echo base_url('/messenger_bot_analytics/result/'); ?>"+page_table_id;
      var tree_bot_href = "<?php echo base_url('messenger_bot/tree_view/'); ?>"+page_table_id;
      var visual_flow_url = "<?php echo base_url('visual_flow_builder/flowbuilder_manager/'); ?>"+page_table_id;

      $(".export_bot").attr('table_id',page_table_id);
      $(".import_bot").attr('table_id',page_table_id);

      $(".analytics_bot").attr('href',analytics_bot_href).attr('target','_BLANK');
      $(".tree_bot").attr('href',tree_bot_href).attr('target','_BLANK');
      $(".visual_flow_campaigns").attr('href',visual_flow_url).attr('target','_BLANK');
      /* Analytics Page herf create */
      var analytics_page_href  = "<?php echo base_url('/page_analytics/analytics/') ?>"+page_table_id;
      $(".analytics_page").attr('href',analytics_page_href).attr('target','_BLANK');
      /* Analytics Page herf create End */

      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>messenger_bot/get_page_details",
        data:{page_table_id:page_table_id,media_type:media_type},
        dataType:'JSON',
        success:function(response){
          $("#mailchimp_list_id").val(response.selected_mailchimp_list_ids).trigger('change');
          $("#sendinblue_list_id2").val(response.selected_sendinblue_list_ids).trigger('change');
          $("#activecampaign_list_id2").val(response.selected_activecampaign_list_ids).trigger('change');
          $("#mautic_list_id2").val(response.selected_mautic_list_ids).trigger('change');
          $("#acelle_list_id2").val(response.selected_acelle_list_ids).trigger('change');

          $("#sms_api_id").val(response.sms_api_id).trigger('change');
          $("#sms_reply_message").val(response.sms_reply_message);

	      $("#email_api_id").val(response.email_api_id).trigger('change');
	      $("#email_reply_message").val(response.email_reply_message);
        $("#email_reply_subject").val(response.email_reply_subject);

        // sms email drip addon
        $("#sequence_sms_campaign_div").html(response.sequence_sms_div_html);
        $("#sequence_email_campaign_div").html(response.sequence_email_div_html);
        $("#sequence_sms_api_id").val(response.sequence_sms_api_id).trigger('change');
        $("#sequence_email_api_id").val(response.sequence_email_api_id).trigger('change');
        $("#sequence_sms_campaign_id").val(response.sequence_sms_campaign_id).trigger('change');
        $("#sequence_email_campaign_id").val(response.sequence_email_campaign_id).trigger('change');

        // sms email drip addon
          
          $("#questionaries_block").html(response.ice_breaker_html);
          $("#ice_breaker_status").val(response.ice_breaker_status);
          if(response.ice_breaker_status == '0')
            $("#questionaries_block").hide();
          else
            $("#questionaries_block").show();

          $("#middle_column_content").html(response.middle_column_content).show();
          $('#middle_column .waiting').hide();
          // $("#reply_settings").click();
          $("#bot_flow_settings").click();
          $("#getstarted_button_edit_url").attr('href',response.getstarted_button_edit_url);

          $("#action_button_settings_lists").html(response.action_buttons_str);
          $("#sequence_message_settings_lists").html(response.sequence_message_button_str);
          $("#messenger_engagement_settings_lists").html(response.messenger_engagment_str);
          $("#user_input_settings_lists").html(response.user_input_flow_str);
        }
      });
    });

    $(document).on('click','.iframed',function(e){
      e.preventDefault();
      var iframe_url = $(this).attr('href');
      var iframe_height = $(this).attr('data-height');
      $("#right_column_content iframe").attr('src',iframe_url).show();
      $("#right_column_bottom_content").hide();
      // $("#right_column_content iframe").attr('height',iframe_height);
      $("#right_column .main_card").show();
      $('#right_column .waiting').hide();

      var find_block = $(this).parent().parent().attr('block-name');
      if(typeof find_block !== 'undefined' && find_block !== false) {
          $(".has_children").attr(find_block).addClass('active');
      }

      $(window).scrollTop(94);
      if($(this).hasClass("collapse_items")) {   
        $('.iframed').not(this).parent().parent().prev().removeClass('text-primary font-weight-bold');
        $(this).parent().parent().prev().addClass('text-primary font-weight-bold');  
        $('.collapse_items').removeClass('active');
        $(this).addClass('active');
        $('.card-condensed').removeClass('active');
      } else {
        
        $('.card-condensed').removeClass('active');
        $(this).parents('.card-condensed').addClass('active');
      }

      var title='';
      if($(this).hasClass('dropdown-item')) title = $(this).html();
      else 
      {
        title = $(this).parents('.card-condensed').children('.card-icon').html();
        title += $(this).parents('.card-condensed').children('.card-body').children('h4').html();
      }
      $("#right_column_title").html(title);
      
    });

    $(document).on('click', '.has_childs', function(event) {
      event.preventDefault();
      $('.has_childs').removeClass("active");
      $(this).removeClass("active");
    });

    $(document).on('click','.check_review_status_class',function(e){
      e.preventDefault();
      var auto_id = $(this).attr('data-id');
      if(auto_id=="") return false;
      $(this).addClass('btn-progress');
      $.ajax({
        type:'POST',
        url:"<?php echo site_url();?>messenger_bot_enhancers/check_review_status",
        data:{auto_id:auto_id}, // database id
        dataType:'json',
        context: this,
        success:function(response)
        {  
          $(this).removeClass('btn-progress');

          if(response.status=="0")
            swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
          else 
          {
            swal('<?php echo $this->lang->line("Status"); ?>', response.message, 'success').then((value) => {
                $(".page_list_item.active").click();
              });            
          }
                  
        }
      });

    });

    $(document.body).on('click','.estimate_now_class',function(e){
      e.preventDefault();
      var auto_id = $(this).attr('data-id');
      var successfully="<?php echo $this->lang->line("Estimation was run successfully"); ?>";
      var waiting="<?php echo $this->lang->line("Please wait 20 seconds"); ?>";
      var estimate_now="<?php echo $this->lang->line("Estimate Quick Send Reach"); ?>";
      
      if(auto_id=="") return false;
      $(this).addClass('btn-progress');
      swal('<?php echo $this->lang->line(""); ?>', waiting, '');
      $.ajax({
        type:'POST',
        url:"<?php echo site_url();?>messenger_bot_enhancers/estimate_reach",
        data:{auto_id:auto_id}, // database id
        dataType:'json',
        context: this,
        success:function(response)
        {  
          $(this).removeClass('btn-progress');

          if(response.status=="0")
            swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
          else
            swal('<?php echo $this->lang->line("Estimated reach"); ?>', response.message, 'success').then((value) => {
                $(".page_list_item.active").click();
              });       
        }
      });

    }); 

    $('#err-log, #import_bot_modal, #export_bot_modal').on('hidden.bs.modal', function () { 
      $(".page_list_item.active").click();
    });

    var table1='';
    $(document).on('click','.error_log_report',function(e){
      e.preventDefault();
      var media_type = "<?php echo $media_type; ?>";
      var table_id = $(this).attr('table_id');
      $("#put_page_id").val(table_id);
      $("#media_type_error").val(media_type);
      var base_url = '<?php echo site_url();?>';

      // $("#error_response_div").html('<div class="text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>');
      
      $("#err-log").modal(); 

      setTimeout(function(){
        if (table1 == '')
        {
          var perscroll1;
          var base_url = "<?php echo base_url(); ?>";
          table1 = $("#mytable1").DataTable({
              serverSide: true,
              processing:true,
              bFilter: false,
              order: [[ 3, "desc" ]],
              pageLength: 10,
              ajax: {
                  url: base_url+'messenger_bot/error_log_report',
                  type: 'POST',
                  data: function ( d )
                  {
                      d.table_id = $("#put_page_id").val();
                      d.error_search = $("#error_searching").val();
                      d.media_type_error = $("#media_type_error").val();
                  }
              },
              language: 
              {
                url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
              },
              dom: '<"top"f>rt<"bottom"lip><"clear">',
              columnDefs: [
                {
                    targets: '',
                    className: 'text-center'
                },
                {
                    targets: [0,3],
                    sortable: false
                }
              ],
              fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (perscroll1) perscroll1.destroy();
                  perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
              }
              },
              scrollX: 'auto',
              fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                if(areWeUsingScroll)
                { 
                if (perscroll1) perscroll1.destroy();
                perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
                }
              }
          });
        }
        else table1.draw();
      }, 1000);


    }); 

    $(document).on('keyup', '#error_searching', function(event) {
      event.preventDefault(); 
      table1.draw();
    });

    // Action Button Settings block
    $(document).on('click', '.action_button_settings', function(event) {
      event.preventDefault();
      $('.card-condensed').removeClass('active');
      $(this).parents('.card-condensed').addClass('active');
      $(window).scrollTop(94);
      title = $(this).parents('.card-condensed').children('.card-icon').html();
      title += $(this).parents('.card-condensed').children('.card-body').children('h4').html();      
      $("#right_column_title").html(title);
      $("#right_column_content iframe").hide();
      $("#right_column_bottom_content").show();
      $("#mark_seen_chat_settings").hide();
      $("#enable_start_button_modal").hide();
      $("#sequence_message_settings_block").hide();
      $("#messenger_engagement_settings_block").hide();
      $("#user_input_settings_block").hide();
      $("#action_button_settings_block").show();
    });

    // Sequence Message Settings Block
    $(document).on('click', '.sequence_message_settings', function(event) {
      event.preventDefault();
      $('.card-condensed').removeClass('active');
      $(this).parents('.card-condensed').addClass('active');

      $(window).scrollTop(94);
      
      title = $(this).parents('.card-condensed').children('.card-icon').html();
      title += $(this).parents('.card-condensed').children('.card-body').children('h4').html();      
      $("#right_column_title").html(title);
      $("#right_column_content iframe").hide();
      $("#right_column_bottom_content").show();
      $("#mark_seen_chat_settings").hide();
      $("#enable_start_button_modal").hide();
      $("#sequence_message_settings_block").show();
      $("#messenger_engagement_settings_block").hide();
      $("#user_input_settings_block").hide();
      $("#action_button_settings_block").hide();
    });

    // Engagement Settings Block
    $(document).on('click', '.messenger_engagement_settings', function(event) {
      event.preventDefault();
      $('.card-condensed').removeClass('active');
      $(this).parents('.card-condensed').addClass('active');
      $(window).scrollTop(94);
      title = $(this).parents('.card-condensed').children('.card-icon').html();
      title += $(this).parents('.card-condensed').children('.card-body').children('h4').html();      
      $("#right_column_title").html(title);
      $("#right_column_content iframe").hide();
      $("#right_column_bottom_content").show();
      $("#mark_seen_chat_settings").hide();
      $("#enable_start_button_modal").hide();
      $("#sequence_message_settings_block").hide();
      $("#messenger_engagement_settings_block").show();
      $("#user_input_settings_block").hide();
      $("#action_button_settings_block").hide();
    });

    // User Input flow Settings Block
    $(document).on('click', '.user_input_flow_settings', function(event) {
      event.preventDefault();
      $('.card-condensed').removeClass('active');
      $(this).parents('.card-condensed').addClass('active');
      $(window).scrollTop(94);
      title = $(this).parents('.card-condensed').children('.card-icon').html();
      title += $(this).parents('.card-condensed').children('.card-body').children('h4').html();      
      $("#right_column_title").html(title);
      $("#right_column_content iframe").hide();
      $("#right_column_bottom_content").show();
      $("#mark_seen_chat_settings").hide();
      $("#enable_start_button_modal").hide();
      $("#sequence_message_settings_block").hide();
      $("#messenger_engagement_settings_block").hide();
      $("#user_input_settings_block").show();
      $("#action_button_settings_block").hide();
    });


    $(document).on('click','.enable_start_button',function(e){
      e.preventDefault();
      $('.card-condensed').removeClass('active');
      $(this).parents('.card-condensed').addClass('active');
      $(window).scrollTop(94);
      title = $(this).parents('.card-condensed').children('.card-icon').html();
      title += $(this).parents('.card-condensed').children('.card-body').children('h4').html();      
      $("#right_column_title").html(title);

      var page_id = $(this).attr('sbutton-enable');
      var started_button_enabled = $(this).attr('sbutton-status');
      var welcome_message = htmlspecialchars_decode($(this).attr('welcome-message'));

      $("#welcome_message").val(welcome_message).click(); 
      $("#started_button_enabled").val(started_button_enabled);

      if(started_button_enabled=='0') $("#delay_con2").hide();
      else $("#delay_con2").show();

      $("#right_column_content iframe").hide();
      $("#right_column_bottom_content").show();
      $("#mark_seen_chat_settings").hide();
      $("#action_button_settings_block").hide();
      $("#sequence_message_settings_block").hide();
      $("#messenger_engagement_settings_block").hide();
      $("#user_input_settings_block").hide();

      $("#enable_start_button_submit").attr("table_id",page_id);
      $("#enable_start_button_modal").show();

      /**Load Emoji For Welcome Screen Message on Get Started Button ***/
       $("#welcome_message").emojioneArea({
            autocomplete: false,
          pickerPosition: "bottom"
         });
    });


    $(document).on('click','#enable_start_button_submit',function(e){
      e.preventDefault();
      var table_id = $(this).attr('table_id');
      $("#page_info_table_id_icebreaker").val(table_id);
      var media_type = "<?php echo $media_type; ?>";
      // var welcome_message = $("#welcome_message").val();
      // var started_button_enabled = $("#started_button_enabled").val();
      $(this).addClass('btn-progress');
      var queryString = new FormData($("#getstarted_icebreaker_form")[0]);
      $.ajax
      ({
         type:'POST',
         url:base_url+'messenger_bot/get_started_welcome_message',
         // data:{table_id:table_id,welcome_message:welcome_message,started_button_enabled:started_button_enabled},
         data:queryString,
         dataType:'JSON',
         cache: false,
         contentType: false,
         processData: false,
         context: this,
         success:function(response)
          {
            $(this).removeClass('btn-progress');
            if(response.status == '0')
            {
            	iziToast.error({title: '',message: response.message,position: 'bottomRight'});
            }
            else
            {
	            if(response.ice_breaker_status=='1') 
	              iziToast.success({title: '',message: response.ice_breaker_message,position: 'bottomRight'});
	            else iziToast.error({title: '',message: response.ice_breaker_message,position: 'bottomRight'});

              if(media_type == "fb") {
                if(response.get_started_status=='1') 
                  iziToast.success({title: '',message: response.get_started_message,position: 'bottomRight'});
                else iziToast.error({title: '',message: response.get_started_message,position: 'bottomRight'});
              }

            }
          } 
      }); 

    });

    $(document).on('change','#started_button_enabled',function(){
      var started_button_enabled = $(this).val();
      if(started_button_enabled=='1') $("#delay_con2").show();
      else $("#delay_con2").hide();
    });


    $(document).on('click','.enable_general_settings',function(e){
      e.preventDefault();
      $('.card-condensed').removeClass('active');
      $(this).parents('.card-condensed').addClass('active');
      $(window).scrollTop(94);
      title = $(this).parents('.card-condensed').children('.card-icon').html();
      title += $(this).parents('.card-condensed').children('.card-body').children('h4').html();      
      $("#right_column_title").html(title);

      var table_id = $(this).attr('table_id');
      var chat_human_email = $(this).attr('chat_human_email');
      var mark_seen_status = $(this).attr('mark_seen_status');
      var no_match_found_reply = $(this).attr('no_match_found_reply');

      if(no_match_found_reply == 'enabled')
      {
        $("#no_match_found_reply").prop("checked", true);
        $("#no_match_found_reply").val('enabled');
      }
      else
      {
        $("#no_match_found_reply").prop("checked", false);
        $("#no_match_found_reply").val('disabled');
      }


      $("#mark_seen_status").val(mark_seen_status);
      $("#chat_human_email").val(chat_human_email);


      $("#right_column_content iframe").hide();
      $("#right_column_bottom_content").show();
      $("#enable_start_button_modal").hide();
      $("#action_button_settings_block").hide();
      $("#sequence_message_settings_block").hide();
      $("#messenger_engagement_settings_block").hide();
      $("#user_input_settings_block").hide();

      $("#mark_seen_save_button").attr("table_id",table_id);
      $("#mark_seen_chat_settings").show();
    });

    $(document).on('change','input[name=no_match_found_reply]',function(){
      var checked_property = $("#no_match_found_reply").prop("checked");
      if(checked_property)
        $("#no_match_found_reply").val('enabled');
      else
        $("#no_match_found_reply").val('disabled');

    });


    $(document).on('click','#mark_seen_save_button',function(e){
      e.preventDefault();
      var table_id = $(this).attr('table_id');
      var mark_seen_status = $("#mark_seen_status").val();
      var chat_human_email = $("#chat_human_email").val();
      var no_match_found_reply = $("#no_match_found_reply").val();
      var mailchimp_list_id = $("#mailchimp_list_id").val();
      var sendinblue_list_id = $("#sendinblue_list_id2").val();
      var activecampaign_list_id = $("#activecampaign_list_id2").val();
      var mautic_list_id = $("#mautic_list_id2").val();
      var acelle_list_id = $("#acelle_list_id2").val();

      var sms_api_id = $("#sms_api_id").val();
      var sms_reply_message = $("#sms_reply_message").val();

      var email_api_id = $("#email_api_id").val();
      var email_reply_message = $("#email_reply_message").val();      
      var email_reply_subject = $("#email_reply_subject").val();


      var sequence_sms_api_id = $("#sequence_sms_api_id").val();
      var sequence_email_api_id = $("#sequence_email_api_id").val();
      var sequence_sms_campaign_id = $("#sequence_sms_campaign_id").val();
      var sequence_email_campaign_id = $("#sequence_email_campaign_id").val();
      var media_type = "<?php echo $media_type; ?>";

      $(this).addClass('btn-progress');
       $.ajax
        ({
           type:'POST',
           url:base_url+'messenger_bot/mark_seen_chat_human_settings',
           data:{table_id:table_id,mark_seen_status:mark_seen_status,chat_human_email:chat_human_email,no_match_found_reply:no_match_found_reply,mailchimp_list_id:mailchimp_list_id,sendinblue_list_id:sendinblue_list_id,activecampaign_list_id:activecampaign_list_id,mautic_list_id:mautic_list_id,acelle_list_id:acelle_list_id,sms_api_id:sms_api_id,sms_reply_message:sms_reply_message,email_api_id:email_api_id,email_reply_message:email_reply_message,email_reply_subject:email_reply_subject,sequence_sms_api_id:sequence_sms_api_id,sequence_email_api_id:sequence_email_api_id,sequence_sms_campaign_id:sequence_sms_campaign_id,sequence_email_campaign_id:sequence_email_campaign_id,media_type:media_type},
           dataType:'JSON',
           context: this,
           success:function(response)
            {
              $(this).removeClass('btn-progress');
              if(response.status=='1') 
                iziToast.success({title: '',message: response.message,position: 'bottomRight'});
              else iziToast.error({title: '',message: response.message,position: 'bottomRight'});
            } 
        }); 

    });



    $(document).on('click','.lead_first_name',function(){
        var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();

        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
      
      


        var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
        var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
        $(this).parent().next().next().next().children('.emojionearea-editor').click();
    });

    $(document).on('click','.lead_last_name',function(){
      var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();

        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 
      
      

      
      var txtToAdd = " #LEAD_USER_LAST_NAME# ";
      var new_text = textAreaTxt + txtToAdd;
      $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
        $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
    });

    $(document).on('click','.getstarted_lead_first_name',function(){      
        var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();   

        // Remove last br tag as emojiarea place a extra br tag for spaces.     
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 

        var txtToAdd = " {{user_first_name}} ";
        var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
        $(this).parent().next().next().next().children('.emojionearea-editor').click();
    });

    $(document).on('click','.getstarted_lead_last_name',function(){      
        var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();

       // Remove last br tag as emojiarea place a extra br tag for spaces.     
        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
        var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
        lastTag=lastTag.trim(lastTag);

        if(lastTag=="<br>")
          textAreaTxt = textAreaTxt.substring(0, lastIndex); 

        var txtToAdd = " {{user_last_name}} ";
        var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
        $(this).parent().next().next().next().next().children('.emojionearea-editor').click();  
    });

    $(document).on('click','.sms_api_first_name',function(){      
        var $txt = $("#sms_reply_message");
        var caretPos = $txt[0].selectionStart;
        var textAreaTxt = $txt.val();       
        var txtToAdd = " {{user_first_name}} ";
        var new_text = textAreaTxt + txtToAdd;
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
    });

    $(document).on('click','.sms_api_last_name',function(){      
        var $txt = $("#sms_reply_message");
        var caretPos = $txt[0].selectionStart;
        var textAreaTxt = $txt.val(); 
        var txtToAdd = " {{user_last_name}} ";
        var new_text = textAreaTxt + txtToAdd;
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) ); 
    });

    $(document).on('click','.email_api_first_name',function(){      
        var $txt = $("#email_reply_message");
        var caretPos = $txt[0].selectionStart;
        var textAreaTxt = $txt.val();       
        var txtToAdd = " {{user_first_name}} ";
        var new_text = textAreaTxt + txtToAdd;
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
    });

    $(document).on('click','.email_api_last_name',function(){      
        var $txt = $("#email_reply_message");
        var caretPos = $txt[0].selectionStart;
        var textAreaTxt = $txt.val(); 
        var txtToAdd = " {{user_last_name}} ";
        var new_text = textAreaTxt + txtToAdd;
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) ); 
    });


  });

  $("document").ready(function(){
    var session_value = "<?php echo $this->session->userdata('bot_list_get_page_details_page_table_id'); ?>";
    if(session_value=='')  $(".list-group li:first").click();    
    else $("li[page_table_id='"+session_value+"']").click();

    var base_url = "<?php echo base_url(); ?>";

    $(document).on('click','.import_bot',function(e){
      e.preventDefault();
      var table_id = $(this).attr('table_id');
      $("#import_id").val(table_id);
      $(".post_to").prop("checked", false);
      $("#json_upload_input").val('');
      $("#import_bot_modal").modal();
    });

    $(document).on('click','#import_bot_submit',function(e){
      e.preventDefault();
      var template_id = $("#template_id").val();
      var filename = $("#json_upload_input").val();

      if(template_id=="" && filename=="")
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('You must select a template or upload one.');?>", 'warning');
        return;
      }

      $(this).addClass('btn-progress');

      var queryString = new FormData($("#import_bot_form")[0]);
      $.ajax({
            type:'POST' ,
            url: base_url+"messenger_bot/import_bot_check",
            dataType: 'JSON',
            data: queryString,
            cache: false,
            contentType: false,
            processData: false,
            context: this,
            success:function(response)
            { 
              $(this).removeClass('btn-progress');
              if(response.status=='1')
              {
                var json_upload_input=response.json_upload_input;
                swal({
                  title: '<?php echo $this->lang->line("Warning!"); ?>',
                  text: response.message,
                  icon: 'warning',
                  buttons: true,
                  dangerMode: true,
                })
                .then((willDelete) => {
                  if (willDelete) 
                  {
                    $(this).addClass('btn-progress');
                    $.ajax({
                      context: this,
                      type:'POST' ,
                      url:"<?php echo site_url();?>messenger_bot/import_bot",
                      // dataType: 'json',
                      data:{json_upload_input:json_upload_input,page_id:response.page_id,template_id:response.template_id},
                      success:function(response2){ 
                        $(this).removeClass('btn-progress');
                        var success_message=response2;
                        var span = document.createElement("span");
                        span.innerHTML = success_message;
                        swal({ title:'<?php echo $this->lang->line("Import Status"); ?>', content:span,icon:'success'});
                      }
                    });
                  } 
                });
              }
              else
              {
                swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
              }
            }
      });
    });

    $(document).on('click', '#cancel_import_bot', function(e){
      e.preventDefault();
      $("#import_bot_modal").modal('hide');
    });


    $(document).on('click','.export_bot',function(e){
      e.preventDefault();
      var table_id = $(this).attr('table_id');
      var media_type = $(this).attr('media_type');
      $("#export_id").val(table_id);
      $("#export_media_type").val(media_type);

      $('#allowed_package_ids').val(null).trigger('change');
      $("#template_name").val('');
      $("#template_description").val('');
      $("#template_preview_image").val('');
      $("#only_me_input").prop("checked", true);
      $("#other_user_input").prop("checked", false); 
      $("#allowed_package_ids_con").addClass('hidden')

      $("#export_bot_modal").modal();
    });

    $(document).on('change','input[name=template_access]',function(){
      var template_access = $(this).val();
      if(template_access=='private') $("#allowed_package_ids_con").addClass('hidden');
      else $("#allowed_package_ids_con").removeClass('hidden');
    });

    $(document).on('click','#export_bot_submit',function(e){
      e.preventDefault();
      var template_name = $("#template_name").val();
      var template_access = $('input[name=template_access]:checked').val();
      var allowed_package_ids = $("#allowed_package_ids").val();

      if(template_name=="")
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please provide template name.');?>", 'warning');
        return;
      }

      if(template_access=="public" && allowed_package_ids==null)
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('You must choose user packages to give them template access.');?>", 'warning');
        return;
      }

      $(this).addClass('btn-progress');
      var queryString = new FormData($("#export_bot_form")[0]);
      $.ajax({
            type:'POST' ,
            url: base_url+"messenger_bot/export_bot",
            dataType: 'JSON',
            data: queryString,
            cache: false,
            contentType: false,
            processData: false,
            context: this,
            success:function(response)
            { 
              $(this).removeClass('btn-progress');

              var success_message=response.message;
              var span = document.createElement("span");
              span.innerHTML = success_message;
              swal({ title:'<?php echo $this->lang->line("Export Status"); ?>', content:span,icon:'success'});
            }
      });

    });

    $(document).on('click', '#cancel_bot_submit', function(e){
      e.preventDefault();
      $("#export_bot_modal").modal('hide');
    });


    $("#allowed_package_ids").select2({ width: "100%" });

    $('.modal').on("hidden.bs.modal", function (e) { 
        if ($('.modal:visible').length) { 
            $('body').addClass('modal-open');
        }
    });

    $(document).on('click','.load_preview_modal',function(e){
      e.preventDefault();
      var item_type = $(this).attr('item_type');
      var file_path = $(this).next().val();
      var user_id = "<?php echo $this->user_id; ?>";

      var res = file_path.match(/http/g);
      if(file_path != '' && res === null)
        file_path = base_url+"upload/image/"+user_id+"/"+file_path;

      $("#preview_text_field").val(file_path);
      if(item_type == 'image')
      {
        $("#modal_preview_image").attr('src',file_path);
        $("#image_preview_div_modal").show();
        $("#video_preview_div_modal").hide();
        $("#audio_preview_div_modal").hide();
        
      }
      $("#modal_for_preview").modal();
    });

    var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
    var image_upload_limit = "<?php echo $image_upload_limit; ?>";
    $("#template_preview_image_div").uploadFile({
      url:base_url+"messenger_bot/upload_image_only",
      fileName:"myfile",
      maxFileSize:image_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
      deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('messenger_bot/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#template_preview_image").val('');                    
              });
         
       },
       onSuccess:function(files,data,xhr,pd)
         {
             var data_modified = base_url+"upload/image/"+user_id+"/"+data;
             $("#template_preview_image").val(data_modified);
         }
    });

    $("#json_upload").uploadFile({
        url:base_url+"messenger_bot/upload_json_template",
        fileName:"myfile",
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        acceptFiles:".json",
        deleteCallback: function (data, pd) {
            var delete_url="<?php echo site_url('messenger_bot/upload_json_template_delete');?>";
              $.post(delete_url, {op: "delete",name: data},
                  function (resp,textStatus, jqXHR) { 
                    $("#json_upload_input").val(''); 
                    $(".type1,.type2").show();                      
                  });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = data;
               $("#json_upload_input").val(data_modified);
               $("#template_id").val('');
               $(".type1,.type2").hide();
           }
    });


  });
</script>



<script type="text/javascript">

  $(document).ready(function(){

    var base_url = "<?php echo base_url(); ?>";
    var table2='';
    $(document).on('click','.error_log_report2',function(e){
      e.preventDefault();
      var auto_responder_type = $(this).attr('data-type');
      $("#auto_responder_type").val(auto_responder_type);

      /**Distinguish email auto responder like MailChimp,Sendinblue etc**/

      var autoresponder_service_name="";
      if(auto_responder_type=='Email Autoresponder'){

        autoresponder_service_name= $(this).attr('data-service');
        $("#autoresponder_service_name").val(autoresponder_service_name);

      }

      $("#err-log2").modal();

      setTimeout(function(){
        if (table2 == '')
        {
          var perscroll2;          
          table2 = $("#mytable2").DataTable({
              serverSide: true,
              processing:true,
              bFilter: false,
              order: [[ 6, "desc" ]],
              pageLength: 10,
              ajax: {
                  url: base_url+'messenger_bot/error_log_report_autoreponder',
                  type: 'POST',
                  data: function ( d )
                  {
                      d.error_search = $("#error_searching2").val();
                      d.auto_responder_type = $("#auto_responder_type").val();
                      d.autoresponder_service_name =  $("#autoresponder_service_name").val();

                  }
              },
              language: 
              {
                url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
              },
              dom: '<"top"f>rt<"bottom"lip><"clear">',
              columnDefs: [
                {
                    targets: [1,2,4,5,6,7],
                    className: 'text-center'
                },
                {
                    targets: [0,7],
                    sortable: false
                },
                {
                    targets: [4],
                    visible: false
                }
              ],
              fnInitComplete:function(){ // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (perscroll2) perscroll2.destroy();
                  perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
              }
              },
              scrollX: 'auto',
              fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
                if(areWeUsingScroll)
                { 
                if (perscroll2) perscroll2.destroy();
                perscroll2 = new PerfectScrollbar('#mytable2_wrapper .dataTables_scrollBody');
                }
              }
          });
        }
        else table2.draw();
      }, 1000);

    });

    $(document).on('keyup', '#error_searching2', function(event) {
      event.preventDefault(); 
      table2.draw();
    });

    $(document).on('click','.error_response',function(e){
      e.preventDefault();
      $(this).removeClass('btn-outline-danger').addClass("btn-danger").addClass('btn-progress');
      var id = $(this).attr('data-id');

      $.ajax
        ({
           type:'POST',
           url:base_url+'messenger_bot/error_log_response',
           data:{id:id},
           context: this,
           success:function(response)
            {
              $(this).addClass('btn-outline-danger').removeClass("btn-danger").removeClass('btn-progress');

              var success_message= response;
              var span = document.createElement("span");
              span.innerHTML = success_message;
              swal({ title:'<?php echo $this->lang->line("API Response"); ?>', content:span,icon:'info'});
            } 
        }); 
    });

  });
</script>

<div class="modal fade" id="ice_breaker_info_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line('Ice Breakers Reference'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div class="section">                
          <h2 class="section-title"><?php echo $this->lang->line('Client Requirements'); ?></h2>
          <ul>
            <li><?php echo $this->lang->line('Messenger for Android +v240'); ?></li>
            <li><?php echo $this->lang->line('Messenger for iOS +v240'); ?></li>
          </ul>
        </div>
        <div class="section">                
          <h2 class="section-title"><?php echo $this->lang->line('Page Profile Priority'); ?></h2>
          <p><?php echo $this->lang->line('Some of the profile elements like Ice Breakers and Get Started button are incompatible with each other. So when both are set one will take precedence over the other. Here is the priority from highest to lowest:'); ?></p>
          <ol>
            <li><?php echo $this->lang->line('API Ice Breakers'); ?></li>
            <li><?php echo $this->lang->line('Get Started button'); ?></li>
            <li><?php echo $this->lang->line('Custom Questions set via the Page Inbox UI'); ?></li>
          </ol>
        </div>
        <div>
          <p><b>NB: </b><?php echo $this->lang->line('Editing Custom Questions from the Page Inbox UI is disabled when Ice Breakers are set via API. This is to prevent breaking the experience set by the installed app.'); ?></p>
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-times"></i> <?php echo $this->lang->line("Close");?></button> -->
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body"> 
        <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close & Refresh List");?></button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="err-log2" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <h5 class="modal-title"><i class="fas fa-history"></i> <?php echo $this->lang->line("Last 7 Days API Log");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                  <div class="col-12 margin-top">
                    <input type="text" id="error_searching2" name="error_searching2" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
                    <input type="hidden" id="auto_responder_type" name="auto_responder_type">   
                    <input type="hidden" id="autoresponder_service_name" name="autoresponder_service_name" value="">     

                  </div>
                  <div class="col-12">
                    <div class="data-card">                   
                      <div class="table-responsive2">
                        <table class="table table-bordered" id="mytable2">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th><?php echo $this->lang->line("Settings Type"); ?></th>  
                              <th><?php echo $this->lang->line("Status"); ?></th>  
                              <th><?php echo $this->lang->line("Email/Phone"); ?></th>  
                              <th><?php echo $this->lang->line("Auto Responde Type"); ?></th>  
                              <th><?php echo $this->lang->line("API Name"); ?></th>  
                              <th><?php echo $this->lang->line("Inserted at"); ?></th>  
                              <th><?php echo $this->lang->line("Actions"); ?></th>  
                            </tr>
                          </thead>
                        </table>
                      </div>
                    </div>
                  </div>

                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="err-log" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <h5 class="modal-title"><i class="fa fa-bug"></i> <?php echo $this->lang->line("Last 7 Days Error Report");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- <div class="col-12 table-responsive" id="error_response_div" style="padding: 20px;"></div> -->
                  <div class="col-12 margin-top">
                    <input type="text" id="error_searching" name="error_searching" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width:200px;'>                                          
                  </div>
                  <div class="col-12">
                    <div class="data-card">   
                      <input type="hidden" name="put_page_id" id="put_page_id">                  
                      <input type="hidden" name="media_type_error" id="media_type_error">                  
                      <div class="table-responsive2">
                        <table class="table table-bordered" id="mytable1">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th><?php echo $this->lang->line("Bot Name"); ?></th> 
                              <th><?php echo $this->lang->line("Error Message"); ?></th> 
                              <th><?php echo $this->lang->line("Error Time"); ?></th> 
                              <th><?php echo $this->lang->line("Actions"); ?></th>  
                            </tr>
                          </thead>
                        </table>
                      </div>
                    </div>
                  </div>

                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="export_bot_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <h5 class="modal-title"><i class="fa fa-file-export"></i> <?php echo $this->lang->line("Export Bot Settings");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="export_bot_modal_body">             

                <form id="export_bot_form" method="POST">
                  <div class="col-12">
                    <div class="well text-justify" style="border:1px solid var(--blue);padding:15px;color:var(--blue);">
                      <?php echo $this->lang->line("Webview form will not be exported/imported. If bot settings have webview form created, then after importing that bot settings for a page, you will need to create new form & change the form URL by the new URL for that page."); ?>
                    </div>
                  </div><br>
                  <input type="hidden" name="export_id" id="export_id">
                  <input type="hidden" name="export_media_type" id="export_media_type">
                  <div class="col-12">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Template Name');?> *</label>
                      <input type="text" name="template_name" class="form-control" id="template_name">                    
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Template Description');?> </label>
                      <textarea type="text" rows="4" name="template_description" class="form-control" id="template_description"></textarea>                    
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Template Preview Image');?> [Square image like (400x400) is recommended]</label>
                      <span style="cursor:pointer;" class="badge badge-status blue load_preview_modal float-right" item_type="image" file_path=""><i class="fa fa-eye"></i> <?php echo $this->lang->line('preview'); ?></span>

                      <input type="hidden" name="template_preview_image" class="form-control" id="template_preview_image">                   
                      <div id="template_preview_image_div"><?php echo $this->lang->line("upload") ?></div>
                    </div>
                  </div>

                  <?php if($this->session->userdata("user_type")=='Admin'){ ?>
                    <div class="col-12">

                      <div class="form-group">
                        <div class="control-label"><?php echo $this->lang->line('Template Access'); ?> *</div>
                        <div class="custom-switches-stacked mt-2">
                          <label class="custom-switch">
                            <input type="radio" name="template_access" value="private" id="only_me_input" class="custom-switch-input" checked>
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line("Only me"); ?></span>
                          </label>
                          <label class="custom-switch">
                            <input type="radio" name="template_access" value="public" id="other_user_input" class="custom-switch-input">
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line("Me as well as other users"); ?></span>
                          </label>
                        </div>                
                      </div>

                    </div>

                    <div class="col-12 hidden" id="allowed_package_ids_con">
                      <div class="form-group">
                        <label><?php echo $this->lang->line('Choose User Packages');?> *</label><br/>
                        <?php echo form_dropdown('allowed_package_ids[]', $package_list, '','class="form-control select2" id="allowed_package_ids" multiple'); ?>
                      </div>
                    </div>
                  <?php } ?>
                  
                  <div class="row">
                    <div class="col-6"><a href="#" id="export_bot_submit" class="btn btn-primary btn-lg"><i class="fa fa-file-export"></i> <?php echo $this->lang->line("Export");?></a></div>                
                    <div class="col-6"><a href="#" id="cancel_bot_submit" class="btn btn-secondary btn-lg float-right"><i class="fa fa-close"></i> <?php echo $this->lang->line("Cancel");?></a></div>
                  </div>
                  <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="import_bot_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <h5 class="modal-title"><i class="fa fa-file-import"></i> <?php echo $this->lang->line("Import Bot Settings");?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="import_bot_modal_body">
                <div id="preloader" class="text-center waiting hidden"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>

                <form id="import_bot_form" method="POST">
                  <div class="col-12">
                    <div class="well text-justify" style="border:1px solid var(--blue);padding:15px;color:var(--blue);">
                      <?php echo $this->lang->line("Webview form will not be exported/imported. If bot settings have webview form created, then after importing that bot settings for a page, you will need to create new form & change the form URL by the new URL for that page."); ?>
                    </div>
                  </div><br>

                  <input type="hidden" name="import_id" id="import_id">

                  <!-- New section -->
                  <?php if(!empty($saved_template_list)) : ?>
                  <!-- zilani -->
                  <p class="text-center" style="font-weight: bold;"><?php echo $this->lang->line('Choose from previous template'); ?></p><br>
                  <div class="makeScroll" style="max-height: 520px;overflow: auto;">
                    <div class="row">

                      <?php $i=1; foreach ($saved_template_list as $key=>$val) : 
                        $id=$val['id'];
                        $template_name=isset($val['template_name']) ? $val['template_name'] : '';
                        $description=isset($val['description']) ? $val['description'] : '';
                        $preview_image=isset($val['preview_image']) ? $val['preview_image'] : ''; 
                        $added_date = date("M j, y H:i",strtotime($val['saved_at']));
                      ?>
                      <div class="col-12 col-sm-6 col-md-4">
                        <div class="card author-box">
                          <div class="card-body" style="border:.5px solid #eee;">
                            <div class="row">
                              <div class="col-4">
                                  <div class="avatar-item">
                                    <?php if($preview_image != '' && file_exists('upload/image/'.$val['user_id'].'/'.$preview_image)) : ?>
                                      <a target="_BLANK" href="<?php echo base_url('messenger_bot/saved_template_view/'.$id);?>" data-toggle='tooltip' title="<?php echo $this->lang->line('Click here to see template details'); ?>">
                                        <img alt="image" width="80" height="80" src="<?php echo base_url('upload/image/'.$val['user_id'].'/'.$preview_image); ?>" class="rounded">
                                      </a>
                                    <?php else : ?>
                                      <a target="_BLANK" href="<?php echo base_url('messenger_bot/saved_template_view/'.$id);?>" data-toggle='tooltip' title="<?php echo $this->lang->line('Click here to see template details'); ?>">
                                        <img alt="image" style="width:80px !important;height:80px !important;" src="<?php echo base_url("assets/img/avatar/avatar-1.png");?>" class="rounded">
                                      </a>
                                    <?php endif; ?>
                                  </div>
                              </div>

                              <div class="col-8">
                                <div class="author-box-details" style="margin-left: 0;">
                                  <div class="author-box-name">
                                    <div class="row">
                                      <div class="col-10">
                                        <h6 class="text-left">
                                          <?php 
                                            if(strlen($template_name) > 17)
                                            {
                                              $short_template_name = substr($template_name,0,16);
                                              echo $short_template_name."..."; 
                                            } else 
                                            {
                                              echo $template_name;
                                            }
                                          ?>
                                        </h6>
                                      </div>
                                      <div class="col-2">
                                        <div class="custom-control custom-radio" data-toggle='tooltip' title="<?php echo $this->lang->line('Click here to select this template'); ?>">
                                          <input type="radio" name="template_id" class="post_to custom-control-input" value="<?php echo $id; ?>" id="<?php echo $id; ?>">
                                          <label class="custom-control-label" for="<?php echo $id; ?>" style="display:inline !important;"></label>
                                        </div>
                                      </div>
                                    </div>
                                    
                                  </div>
                                  <div class="author-box-description" style="margin-top: 0;">
                                    <p class="text-justify">
                                      <?php
                                        if(strlen($description) > 60)
                                        {
                                          $short_des = substr($description,0,59);
                                          echo $short_des."..."; 
                                        } else 
                                        {
                                          echo $description;
                                        }
                                      ?>
                                    </p>
                                  </div>
                                  <div class="w-100 d-sm-none"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php $i++; endforeach; ?>
                    </div>
                  </div>
                  <!-- zilani -->

                  <!-- <div class="container-fluid">
                    <div class="row">
                      <p class="text-center" style="font-weight: bold;"><?php echo $this->lang->line('Choose from previous template'); ?></p>
                      <div class="yscroll" style="height: 400px;overflow: auto;">
                          <?php foreach ($saved_template_list as $key=>$val) : 
                            $id=$val['id'];
                            $template_name=isset($val['template_name']) ? $val['template_name'] : '';
                            $description=isset($val['description']) ? $val['description'] : '';
                            $preview_image=isset($val['preview_image']) ? $val['preview_image'] : ''; 
                            $added_date = date("M j, y H:i",strtotime($val['saved_at']));
                          ?>

                          <div class="col-12 col-md-6">
                            <div class="box box-solid" style="">
                              <div class="box-body" style="padding-top: 10px;padding-bottom: 0;">
                                <h4 style="border:1px solid #fafafa; font-size: 15px; text-align: center; padding: 7px 10px; margin-top: 0;">
                                    <?php
                                          if(strlen($template_name) > 22)
                                          {
                                            $short_template_name = substr($template_name,0,19);
                                            echo $short_template_name."..."; 
                                          } else 
                                          {
                                            echo $template_name;
                                          }
                                        ?> 
                                        <div class="form-check float-right">
                                            <div class="clearfix"></div>
                                             <label class="radio_check">
                                               <input type="radio" name="template_id" class="post_to" value="<?php echo $id; ?>" id="<?php echo $id; ?>" >
                                               <span class="checkmark" data-toggle='tooltip' title="<?php echo $this->lang->line('Click here to select this template'); ?>" ></span>
                                             </label>
                                           </div> 
                                           <div class="clearfix"></div>
                                </h4>
                                <div class="media">
                                  <div class="media-left">
                                                                                
                                          <?php if($preview_image != '') : ?>
                                                <a data-toggle='tooltip' title="<?php echo $this->lang->line('Click here to see template details'); ?>" target="_BLANK" href="<?php echo base_url('messenger_bot_export_import/view/'.$id);?>">
                                                  <img style="width: 100px;height: 100px;border-radius: 4px;box-shadow: 0 1px 3px rgba(0,0,0,.15);" class="media-object" src="<?php echo base_url('upload/image/'.$val['user_id'].'/'.$preview_image); ?>" alt="preview image"><br>
                                                </a>
                                                <?php else : ?>
                                                  <a data-toggle='tooltip' title="<?php echo $this->lang->line('click here to see template details'); ?>" target="_BLANK" href="<?php echo base_url('messenger_bot_export_import/view/'.$id);?>">
                                                    <img style="width: 100px;height: 100px;border-radius: 4px;box-shadow: 0 1px 3px rgba(0,0,0,.15);"  class="media-object" src="https://via.placeholder.com/100x100.png" alt="preview image"><br>
                                                  </a>
                                          <?php endif; ?>
                                  </div>
                                  <div class="media-body">
                                      <div class="clearfix">
                                          <p class="text-justify">
                                            <?php
                                              if(strlen($description) > 173)
                                              {
                                                $short_des = substr($description,0,170);
                                                echo $short_des."..."; 
                                              } else 
                                              {
                                                echo $description;
                                              }
                                            ?>
                                          </p>
                                      </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <?php endforeach; ?>
                      </div>
                    </div>
                  </div> -->
              <?php endif; ?>
                  <!-- end new section -->
              <br><div class="col-12 text-center type2" style="font-weight: bold;"><?php echo $this->lang->line('OR'); ?></div><br>
              <div class="col-12 type3">
                <div class="text-center">
                  <label><?php echo $this->lang->line('Upload Template JSON');?></label>
                  <div class="form-group">    
                    <div id="json_upload"><?php echo $this->lang->line('Upload');?></div>
                    <input type="hidden" id="json_upload_input" name="json_upload_input">
                  </div>                
                </div>
              </div>

                  <div class="row">
                    <div class="col-6"><a href="#" id="import_bot_submit" class="btn btn-primary btn-lg"><i class="fa fa-file-import"></i> <?php echo $this->lang->line("Import");?></a></div>                
                    <div class="col-6"><a href="#" id="cancel_import_bot" class="btn btn-secondary btn-lg float-right"><i class="fa fa-close"></i> <?php echo $this->lang->line("Cancel");?></a></div>
                  </div>

                  <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_for_preview" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-eye"></i> <?php echo $this->lang->line('item preview'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div id="image_preview_div_modal" style="display: none;">
          <img id="modal_preview_image" width="100%" src="">
        </div>
        <div id="video_preview_div_modal" style="display: none;">
          <video width="100%" id="modal_preview_video" controls>
            
          </video>
        </div>
        <div id="audio_preview_div_modal" style="display: none;">
          <audio width="100%" id="modal_preview_audio" controls>
            
          </audio>
        </div>
        <div>
          <input class="form-control" type="text" id="preview_text_field">
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="variable_data_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-book-reader"></i> <?php echo $this->lang->line("All Variables you currently have"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body" data-backdrop="static" data-keyboard="false">
                <div class="row">
                    <div class="col-12">
                      <div class="section">
                        <div class="section-title"><?php echo $this->lang->line('Variable'); ?></div>
                      <p><?php echo $this->lang->line('After you have saved a response in Custom Field, you can use it as a variable in your message reply to subscriber.'); ?></p>
                      </div>
                      <div class="section">
                        <div class="section-title"><?php echo $this->lang->line('How to use Variable?'); ?></div>
                      <p><?php echo $this->lang->line('To use variable for Custom Field, write the variable surrounding by #  like')."<b> #Custom Field#</b>"; ?></p>
                      </div>
                      <div class="section" id="variable_display_section">
                        <!-- content goes here -->
                      </div>
                    </div>
                </div>            
            </div>

            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
            </div>

        </div>
    </div>
</div>
