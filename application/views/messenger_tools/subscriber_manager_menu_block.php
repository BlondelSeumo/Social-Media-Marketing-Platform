 <section class="section">
  <div class="section-header">
    <h1><i class="fab fa-facebook-messenger"></i> <?php echo $this->lang->line("Messenger Subscriber"); ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">

      <div class="col-lg-4">
        <div class="card card-large-icons">
          <div class="card-icon text-primary">
            <i class="fas fa-sync-alt"></i>
          </div>
          <div class="card-body">
            <h4><?php echo $this->lang->line("Sync Subscribers"); ?></h4>
            <p><?php echo $this->lang->line("Sync, migrate, conversation..."); ?></p>
            <a href="<?php echo base_url("subscriber_manager/sync_subscribers/0"); ?>" class="card-cta"><i class="fab fa-facebook"></i> <?php echo $this->lang->line("Facebook"); ?></a>
            <a href="<?php echo base_url("subscriber_manager/sync_subscribers/1"); ?>" class="card-cta"><i class="fab fa-instagram"></i> <?php echo $this->lang->line("Instagram"); ?></a>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card card-large-icons">
          <div class="card-icon text-primary">
            <i class="fas fa-user-circle"></i>
          </div>
          <div class="card-body">
            <h4><?php echo $this->lang->line("Bot Subscribers"); ?></h4>
            <p><?php echo $this->lang->line("Subscriber actions, assign label, download..."); ?></p>
            <a href="<?php echo base_url("subscriber_manager/bot_subscribers"); ?>" class="card-cta"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card card-large-icons">
          <div class="card-icon text-primary">
            <i class="fas fa-tags"></i>
          </div>
          <div class="card-body">
            <h4><?php echo $this->lang->line("Labels/Tags"); ?></h4>
            <p><?php echo $this->lang->line("Subcriber label/tags, segmentation..."); ?></p>
            <a href="<?php echo base_url("subscriber_manager/contact_group"); ?>" class="card-cta"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>



<?php 
if($this->basic->is_exist("modules",array("id"=>263)) || $this->basic->is_exist("modules",array("id"=>264))) {  
  if($this->session->userdata('user_type') == 'Admin' || count(array_intersect($this->module_access, array('263','264'))) !=0) {  ?>
  <br>
  <section class="section">
      <div class="section-header">
          <h1><i class="fas fa-external-link-square-alt"></i> <?php if($this->basic->is_exist("modules",array("id"=>263))) echo $this->lang->line("SMS/Email Subscriber (External)"); else echo $this->lang->line("SMS Subscriber (External)"); ?></h1>
          <div class="section-header-breadcrumb">
              <div class="breadcrumb-item"><?php echo $page_title; ?></div>
          </div>
      </div>

      <div class="section-body">
          <div class="row">

              <div class="col-lg-6">
                  <div class="card card-large-icons">
                      <div class="card-icon text-primary"><i class="fas fa-users"></i></div>
                      <div class="card-body">
                          <h4><?php echo $this->lang->line("Contact Group"); ?></h4>
                          <p><?php if($this->basic->is_exist("modules",array("id"=>263))) echo $this->lang->line("Manage contacts by groups, sms/email campaign..."); else echo $this->lang->line("Manage contacts by groups, sms campaign..."); ?></p>
                          <a href="<?php echo base_url("sms_email_manager/contact_group_list"); ?>" class="no_hover card-cta" style="font-weight: 500;"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
                      </div>
                  </div>
              </div>

              <div class="col-lg-6">
                  <div class="card card-large-icons">
                      <div class="card-icon text-primary"><i class="fas fa-book"></i></div>
                      <div class="card-body">
                          <h4><?php echo $this->lang->line("Contact Book"); ?></h4>
                          <p><?php if($this->basic->is_exist("modules",array("id"=>263))) echo $this->lang->line("Manage contacts, import, sms/email campaign..."); else echo $this->lang->line("Manage contacts, import, sms campaign..."); ?></p>
                          <a href="<?php echo base_url("sms_email_manager/contact_list"); ?>" class="no_hover card-cta" style="font-weight: 500;"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
                      </div>
                  </div>
              </div>

            <?php if($this->basic->is_exist("modules",array("id"=>290))) { ?>
              <?php if($this->session->userdata('user_type') == 'Admin' || in_array(290,$this->module_access)) {  ?>
                <div class="col-lg-6">
                    <div class="card card-large-icons">
                        <div class="card-icon text-primary"><i class="fab fa-get-pocket"></i></div>
                        <div class="card-body">
                            <h4><?php echo $this->lang->line("Email Phone Opt-in Form Builder"); ?></h4>
                            <p><?php if($this->basic->is_exist("modules",array("id"=>290))) echo $this->lang->line("Custom Subscribers opt-in Form builder."); else echo $this->lang->line("Custom Subscribers opt-in Form builder."); ?></p>
                            <a href="<?php echo base_url("email_optin_form_builder"); ?>" class="no_hover card-cta" style="font-weight: 500;"><?php echo $this->lang->line("Actions"); ?> <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
              <?php } ?>
            <?php } ?>

          </div>
      </div>
  </section>
  <?php } ?>
<?php } ?>