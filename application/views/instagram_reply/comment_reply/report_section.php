<section class="section">
 <div class="section-header">
   <h1><i class="fas fa-chart-pie"></i> <?php echo $page_title; ?></h1>
   <div class="section-header-breadcrumb">
   	<div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
     <div class="breadcrumb-item"><?php echo $this->lang->line("Instagram Reply");?></div>
     <div class="breadcrumb-item"><?php echo $page_title; ?></div>
   </div>
 </div>

 <div class="section-body">
 	<div class="row">
 		<?php if($this->session->userdata('user_type') == 'Admin' || in_array(251,$this->module_access)) : ?>
 		<div class="col-lg-3">
      <div class="wizard-steps mb-4 mt-1">
        <a href="<?php echo base_url("comment_automation/all_auto_comment_report/0/0/1"); ?>" class="no_hover">
          <div class="wizard-step wizard-step-light">
            <div class="wizard-step-icon text-primary gradient">
              <i class="far fa-comment-dots"></i>
            </div>
            <div class="wizard-step-label">
              <?php echo $this->lang->line("Auto Comment Report"); ?>
            </div>
            <p class="text-muted mt-3"><?php echo $this->lang->line("Report of auto comment on instagram accounts's post."); ?></p>       
          </div>
        </a>
      </div>
 		</div>
 		<?php endif; ?>
 		<div class="col-lg-3">
      <div class="wizard-steps mb-4 mt-1">
        <a href="<?php echo base_url("instagram_reply/instagram_autoreply_report/post"); ?>" class="no_hover">
          <div class="wizard-step wizard-step-light">
            <div class="wizard-step-icon text-secondary gradient">
              <i class="fas fa-reply"></i>
            </div>
            <div class="wizard-step-label">
              <?php echo $this->lang->line("Auto Comment Reply Report"); ?>
            </div>
            <p class="text-muted mt-3"><?php echo $this->lang->line("Report of auto comment reply on instagram accounts's post."); ?></p>       
          </div>
        </a>
      </div>
 		</div>
 		<?php if($instagram_reply_enhancers_access == 1) : ?>
 		<div class="col-lg-3">
      <div class="wizard-steps mb-4 mt-1">
        <a href="<?php echo base_url("instagram_reply/instagram_autoreply_report/full"); ?>" class="no_hover">
          <div class="wizard-step wizard-step-light">
            <div class="wizard-step-icon text-danger gradient">
              <i class="fas fa-briefcase"></i>
            </div>
            <div class="wizard-step-label">
              <?php echo $this->lang->line("Full Account Reply Reports"); ?>
            </div>
            <p class="text-muted mt-3"><?php echo $this->lang->line("Report of Posts comment reply of Instagram Full Account."); ?></p>       
          </div>
        </a>
      </div>
 		</div>
 		<div class="col-lg-3">
      <div class="wizard-steps mb-4 mt-1">
        <a href="<?php echo base_url("instagram_reply/instagram_autoreply_report/mention"); ?>" class="no_hover">
          <div class="wizard-step wizard-step-light">
            <div class="wizard-step-icon text-success gradient">
              <i class="fas fa-tags"></i>
            </div>
            <div class="wizard-step-label">
              <?php echo $this->lang->line("Mention Reply Report"); ?>
            </div>
            <p class="text-muted mt-3"><?php echo $this->lang->line("Report of Mention of instagram accounts's post."); ?></p>       
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
  .wizard-steps .wizard-step{height: 270px;}
  .wizard-step-icon i{font-size: 65px !important;} 
</style>