 <section class="section">
  <div class="section-header">
    <h1><i class="fas fa-cogs"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("admin/general_settings"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-primary gradient">
                <i class="fas fa-toolbox"></i>
              </div>
              <div class="wizard-step-label text-capitalize">
                <?php echo $this->lang->line("General Settings"); ?>
              </div>
              <p class="text-muted mt-2"><?php echo $this->lang->line("Report of auto comment on page's post."); ?></p>       
            </div>
          </a>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("admin/frontend_settings"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-secondary gradient">
                <i class="fas fa-store"></i>
              </div>
              <div class="wizard-step-label text-capitalize">
                <?php echo $this->lang->line("Front-end Settings"); ?>
              </div>
              <p class="text-muted mt-2"><?php echo $this->lang->line("Hide, theme, social, review, video..."); ?></p>       
            </div>
          </a>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("admin/smtp_settings"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-success gradient">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="wizard-step-label text-capitalize">
                <?php echo $this->lang->line("SMTP Settings"); ?>
              </div>
              <p class="text-muted mt-2"><?php echo $this->lang->line("SMTP email settings"); ?></p>       
            </div>
          </a>
        </div>

      </div>

      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("admin/email_template_settings"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-info gradient">
                <i class="fas fa-id-card"></i>
              </div>
              <div class="wizard-step-label text-capitalize">
                <?php echo $this->lang->line("Email Template"); ?>
              </div>
              <p class="text-muted mt-2"><?php echo $this->lang->line("Signup, change password, expiry, payment..."); ?></p>       
            </div>
          </a>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("admin/analytics_settings"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-warning gradient">
                <i class="fas fa-chart-pie"></i>
              </div>
              <div class="wizard-step-label text-capitalize">
                <?php echo $this->lang->line("Analytics"); ?>
              </div>
              <p class="text-muted mt-2"><?php echo $this->lang->line("Gogole analytics, Facebook pixel code..."); ?></p>       
            </div>
          </a>
        </div>

      </div>

      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("admin/advertisement_settings"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-danger gradient">
                <i class="fab fa-adversal"></i>
              </div>
              <div class="wizard-step-label text-capitalize">
                <?php echo $this->lang->line("Advertisement"); ?>
              </div>
              <p class="text-muted mt-2"><?php echo $this->lang->line("Banner, potrait, landscape image ads..."); ?></p>       
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<style type="text/css">
  .popover{min-width: 330px !important;}
  .no_hover:hover{text-decoration: none;}
  .otn_info_modal{cursor: pointer;}
  #external_sequence_block{ z-index: unset; }
  .wizard-steps{display: block;}
  .wizard-steps .wizard-step:before{content: none;}
  .wizard-steps .wizard-step{height: 200px;}
  .wizard-step-icon i{font-size: 65px !important;} 
</style>