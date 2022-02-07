 <section class="section">
  <div class="section-header">
    <h1><i class="fas fa-chart-pie"></i> <?php echo $this->lang->line("Facebook Comment/Reply Reports"); ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(251,$this->module_access)) : ?>
      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("comment_automation/all_auto_comment_report"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-primary gradient">
                <i class="far fa-comment-dots"></i>
              </div>
              <div class="wizard-step-label">
                <?php echo $this->lang->line("Auto Comment Report"); ?>
              </div>
              <p class="text-muted mt-3"><?php echo $this->lang->line("Report of auto comment on page's post."); ?></p>       
            </div>
          </a>
        </div>
      </div>
      <?php endif; ?>

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(80,$this->module_access)) : ?>
      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("comment_automation/all_auto_reply_report"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-secondary gradient">
                <i class="fas fa-reply"></i>
              </div>
              <div class="wizard-step-label">
                <?php echo $this->lang->line("Auto reply report"); ?>
              </div>
              <p class="text-muted mt-3"><?php echo $this->lang->line("Report of auto comment reply & private reply."); ?></p>       
            </div>
          </a>
        </div>
      </div>
      <?php endif; ?>
      
      <?php 
      if($this->basic->is_exist("add_ons",array("project_id"=>29)))
      if($this->session->userdata('user_type') == 'Admin' || in_array(201,$this->module_access)) : ?>
      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("comment_reply_enhancers/bulk_tag_campaign_list"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-success gradient">
                <i class="fas fa-tags"></i>
              </div>
              <div class="wizard-step-label">
                <?php echo $this->lang->line("Comment bulk tag report"); ?>
              </div>
              <p class="text-muted mt-3"><?php echo $this->lang->line("Report of bulk tag in single comment."); ?></p>       
            </div>
          </a>
        </div>
      </div>
      <?php endif; ?>
      
      <?php 
      if($this->basic->is_exist("add_ons",array("project_id"=>29)))
      if($this->session->userdata('user_type') == 'Admin' || in_array(202,$this->module_access)) : ?>
      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("comment_reply_enhancers/bulk_comment_reply_campaign_list"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-warning gradient">
                <i class="far fa-comments"></i>
              </div>
              <div class="wizard-step-label">
                <?php echo $this->lang->line("Bulk comment reply report"); ?>
              </div>
              <p class="text-muted mt-3"><?php echo $this->lang->line("Report of tag in each reply of comment."); ?></p>       
            </div>
          </a>
        </div>
      </div>
      <?php endif; ?>
      
      <?php 
      if($this->basic->is_exist("add_ons",array("project_id"=>29)))
      if($this->session->userdata('user_type') == 'Admin' || in_array(204,$this->module_access)) : ?>
      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("comment_reply_enhancers/all_response_report"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-info gradient">
                <i class="fas fa-reply-all"></i>
              </div>
              <div class="wizard-step-label">
                <?php echo $this->lang->line("Full PageResponse Report"); ?>
              </div>
              <p class="text-muted mt-3"><?php echo $this->lang->line("Report of comment reply & private reply of full pages."); ?></p>       
            </div>
          </a>
        </div>
      </div>
      <?php endif; ?>
      
      <?php 
      if($this->basic->is_exist("add_ons",array("project_id"=>29)))
      if($this->session->userdata('user_type') == 'Admin' || in_array(206,$this->module_access)) : ?>
      <div class="col-lg-4">
        <div class="wizard-steps mb-4 mt-1">
          <a href="<?php echo base_url("comment_reply_enhancers/all_like_share_report"); ?>" class="no_hover">
            <div class="wizard-step wizard-step-light">
              <div class="wizard-step-icon text-danger gradient">
                <i class="fas fa-thumbs-up"></i>
              </div>
              <div class="wizard-step-label">
                <?php echo $this->lang->line("Auto Like & Share Report"); ?>
              </div>
              <p class="text-muted mt-3"><?php echo $this->lang->line("Report of sharing & liking by other page's you own."); ?></p>       
            </div>
          </a>
        </div>
      </div>
      <?php endif; ?>

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
  .wizard-steps .wizard-step{height: 230px;}
  .wizard-step-icon i{font-size: 65px !important;} 
</style>