  <?php if( (($this->session->userdata("user_type")=="Admin" || in_array(79,$this->module_access)) && strtotime(date("Y-m-d")) <= strtotime("2020-3-15")) || $this->is_broadcaster_exist || ($this->session->userdata("user_type")=="Admin" || in_array(275,$this->module_access)) ) { ?>
  <section class="section">
  <div class="section-header">
    <h1>
      <i class="fab fa-facebook-messenger"></i> 
      <?php echo $this->lang->line("Messenger Broadcasting"); ?>
     </h1>
  </div>

  <div class="section-body">


  <div class="row">
    <?php if($this->is_broadcaster_exist) { ?>
      <div class="col-12 col-md-4">
          <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("messenger_bot_enhancers/subscriber_broadcast_campaign"); ?>" class="no_hover">
          <div class="wizard-step wizard-step-light">
            <div class="wizard-step-icon text-primary gradient">
              <i class="fas fa-paper-plane"></i>
            </div>
            <div class="wizard-step-label">
              <?php echo $this->lang->line("Subscriber Broadcast"); ?>
            </div>
            <p class="text-muted mt-3"><?php echo $this->lang->line("Non-promo with tag, 24H structured message broadcast to messenger bot subscribers"); ?></p>       
          </div>
          </a>
        </div>
      </div>
    <?php 
    } 
    if($this->session->userdata("user_type")=="Admin"  || in_array(275,$this->module_access)) { ?>
      <div class="col-12 col-md-4">
          <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("messenger_bot_broadcast/otn_subscriber_broadcast_campaign"); ?>" class="no_hover">
          <div class="wizard-step wizard-step-light">
            <div class="wizard-step-icon text-danger gradient">
              <i class="fas fa-clock"></i>
            </div>
            <div class="wizard-step-label">
              <?php echo $this->lang->line("OTN Subscriber Broadcast"); ?>
            </div>
            <p class="text-muted mt-3"><?php echo $this->lang->line("One-Time Notification request follow-up message broadcasting."); ?></p>       
          </div>
          </a>
        </div>
      </div>
    <?php 
    } ?>
  </div>
  <?php 
  } ?>


  <?php 
  if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, array('263','264','270','271'))) !=0) {  ?>
  <br>
  <section class="section">
      <div class="section-header">
          <h1><i class="fas fa-envelope"></i> <?php echo $this->lang->line("SMS/Email Broadcasting"); ?></h1>
      </div>

      <div class="section-body">
          <div class="row"> 

              <div class="col-12 col-md-4">
                  <div class="wizard-steps mb-4 mt-1">
                  <div class="wizard-step wizard-step-light px-lg-2">
                    <div class="wizard-step-icon text-info gradient">
                      <i class="fas fa-envelope"></i>
                    </div>
                    <div class="wizard-step-label">
                      <?php echo $this->lang->line("Contact"); ?>
                    </div>
                    <p class="text-muted mt-2"><?php echo $this->lang->line('SMS/email contact, contact group/label');?></p>
                    <div class="mt-1">
                      <a href="<?php echo base_url("sms_email_manager/contact_group_list"); ?>" class="no_hover  btn btn-outline-light text-primary"><i class="fas fa-address-book"></i> <?php echo $this->lang->line("Group"); ?> <i class="fas fa-chevron-right"></i></a>
                      <a href="<?php echo base_url("sms_email_manager/contact_list"); ?>" class="no_hover  btn btn-outline-light text-primary"><i class="fas fa-users"></i> <?php echo $this->lang->line("Contact"); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>



            <div class="col-12 col-md-4">
                <div class="wizard-steps mb-4 mt-1">
                <div class="wizard-step wizard-step-light px-lg-2">
                  <div class="wizard-step-icon text-secondary gradient">
                    <i class="fas fa-th-list"></i>
                  </div>
                  <div class="wizard-step-label">
                    <?php echo $this->lang->line("Template"); ?>
                  </div>
                  <p class="text-muted mt-2"><?php echo $this->lang->line('Saved templates for sms/email campaigns');?></p>
                  <div class="mt-1">
                    <?php if($this->session->userdata("user_type")=="Admin"  || count(array_intersect($this->module_access, array('263','271'))) !=0) : ?>
                       <a href="<?php echo base_url('sms_email_manager/template_lists/email'); ?>" class="no_hover  btn btn-outline-light text-primary"><i class="fas fa-envelope"></i> <?php echo $this->lang->line("Email"); ?> <i class="fas fa-chevron-right"></i></a>
                     <?php endif; ?>
                     <?php if($this->session->userdata("user_type")=="Admin"  || count(array_intersect($this->module_access, array('270'))) !=0) : ?>
                       <a href="<?php echo base_url('sms_email_manager/template_lists/sms'); ?>" class="no_hover btn btn-outline-light text-primary"><i class="fas fa-sms"></i> <?php echo $this->lang->line("SMS"); ?> <i class="fas fa-chevron-right"></i></a>
                     <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <?php if($this->session->userdata("user_type")=="Admin"  || count(array_intersect($this->module_access, array('263','264'))) !=0) : ?>
            <div class="col-12 col-md-4">
                <div class="wizard-steps mb-4 mt-1">
                <div class="wizard-step wizard-step-light px-lg-2">
                  <div class="wizard-step-icon text-primary gradient">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <div class="wizard-step-label">
                    <?php echo $this->lang->line("Broadcast Campaign"); ?>
                  </div>
                  <p class="text-muted mt-2"><?php echo $this->lang->line('Campaign list, new campaign, report');?></p>
                  <div class="mt-1">
                    <a href="<?php echo base_url('sms_email_manager/email_campaign_lists'); ?>" class="no_hover  btn btn-outline-light text-primary"><i class="fas fa-envelope"></i> <?php echo $this->lang->line("Email"); ?> <i class="fas fa-chevron-right"></i></a>
                    <a href="<?php echo base_url('sms_email_manager/sms_campaign_lists'); ?>" class="no_hover  btn btn-outline-light text-primary"><i class="fas fa-sms"></i> <?php echo $this->lang->line("SMS"); ?> <i class="fas fa-chevron-right"></i></a>
                  </div>
                </div>
              </div>
            </div> 
           <?php endif; ?>

          <?php 
              if($this->basic->is_exist("modules",array("id"=>270)) && $this->basic->is_exist("modules",array("id"=>271))) {  
                if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, array('270','271'))) !=0) {  ?>            
           
                <div class="col-12 col-md-4">
                  <div class="wizard-steps mb-4 mt-1">
                  <a href="<?php echo base_url('sms_email_sequence/external_sequence_lists'); ?>" class="no_hover">
                    <div class="wizard-step wizard-step-light px-lg-2">
                      <div class="wizard-step-icon text-danger gradient">
                        <i class="fas fa-fill-drip"></i>
                      </div>
                      <div class="wizard-step-label">
                        <?php echo $this->lang->line("Sequence Campaign"); ?>
                      </div>
                      <p class="text-muted mt-2"><?php echo $this->lang->line('Sequence sms/email broadcasting for external contact.');?></p>
                    </div>
                  </a>
                </div>
              </div>

            <?php } ?>
            <?php 
         } ?>

         <?php if($this->basic->is_exist("modules",array("id"=>290))) { ?>
            <?php if($this->session->userdata('user_type') == 'Admin' || in_array(290,$this->module_access)) {  ?>
              <div class="col-12 col-md-4">
                <div class="wizard-steps mb-4 mt-1">
                <a href="<?php echo base_url("email_optin_form_builder"); ?>" class="no_hover">
                  <div class="wizard-step wizard-step-light px-lg-2">
                    <div class="wizard-step-icon text-warning gradient">
                      <i class="fab fa-wpforms"></i>
                    </div>
                    <div class="wizard-step-label">
                      <?php echo $this->lang->line("Opt-in Form Builder"); ?>
                    </div>
                    <p class="text-muted mt-2"><?php echo $this->lang->line('Custom sms/email subscribers opt-in form builder for website.');?></p>
                  </div>
                </a>
              </div>
          </div>
        <?php } ?>
            <?php 
        } ?>


      </div>


      </div>
  </section>
  <?php } ?>



<style type="text/css">
  .popover{min-width: 330px !important;}
  .no_hover:hover{text-decoration: none;}
  .otn_info_modal{cursor: pointer;}
  #external_sequence_block{ z-index: unset; }
  .wizard-steps{display: block;}
  .wizard-steps .wizard-step:before{content: none;}
  .wizard-steps .wizard-step{height: 230px;}
  .wizard-step-icon i{font-size: 65px !important;} 
</style>