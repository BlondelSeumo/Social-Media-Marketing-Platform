 <section class="section">
  <div class="section-header">
    <?php if(($this->session->userdata('user_type') == 'Admin' || in_array(296,$this->module_access)) && $this->config->item('instagram_reply_enable_disable') == '1') : ?>
      <h1><i class="fab fa-facebook-square"></i> <?php echo $this->lang->line("Facebook & Instagram Poster"); ?></h1>
      <?php else : ?>
      <h1><i class="fab fa-facebook-square"></i> <?php echo $page_title; ?></h1>
    <?php endif; ?>
    <div class="section-header-button d-none">
     <a class="btn btn-primary" href="<?php echo base_url('social_accounts/index'); ?>">
        <i class="fa fa-cloud-download-alt"></i> <?php echo $this->lang->line("Import Facebook Accounts"); ?></a> 
    </div>
    <!-- <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div> -->
  </div>

  <div class="section-body">
    <div class="row">

      <?php foreach($ultrapost_tools as $tool) : 
        if(!$tool['has_access']) continue;
      ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="wizard-steps mb-4 mt-1">
            <a href="<?php echo $tool['action_url']; ?>" class="no_hover">
              <div class="wizard-step wizard-step-light">
                <div class="wizard-step-icon <?php echo $tool['icon_color']; ?> gradient">
                  <i class="<?php echo $tool['icon']; ?>"></i>
                </div>
                <div class="wizard-step-label text-capitalize">
                  <?php echo $tool['title']; ?>
                </div>
                <p class="text-muted mt-2"><?php echo $tool['sub_contents']; ?></p>       
              </div>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section><br><br>



<?php if($this->session->userdata('user_type') == 'Admin' || in_array(100,$this->module_access)) : ?>
  <section class="section">
    <div class="section-header">
      <h1><i class="fa fa-share-alt-square"></i> <?php echo $this->lang->line("Comboposter"); ?></h1>
      <div class="section-header-button d-none">
       <a class="btn btn-primary" href="<?php echo base_url('comboposter/social_accounts'); ?>">
          <i class="fa fa-cloud-download-alt"></i> <?php echo $this->lang->line("Import Social Accounts"); ?></a> 
      </div>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><?php echo $this->lang->line("Comboposter"); ?></div>
      </div>
    </div>

    <div class="section-body">

      <div class="row">
        <?php 
          foreach($comboposter_tools as $combo_tool) :
            if(!$combo_tool['has_access']) continue;
        ?>
          <div class="col-12 <?php if(isset($combo_tool['sub_menus'])) echo 'col-md-6'; else echo 'col-md-4 col-lg-3' ?>">
            <div class="wizard-steps mb-4 mt-1 <?php if($combo_tool['action_url']=='') echo 'comboposter'; ?>">
              <a href="<?php echo $combo_tool['action_url']; ?>" class="no_hover">
                <div class="wizard-step wizard-step-light">
                  <div class="wizard-step-icon <?php echo $combo_tool['icon_color']; ?> gradient">
                    <i class="<?php echo $combo_tool['icon']; ?>"></i>
                  </div>
                  <div class="wizard-step-label text-capitalize">
                    <?php echo $combo_tool['title']; ?>
                  </div>
                  <p class="text-muted mt-2"><?php echo $combo_tool['sub_contents']; ?></p>
                  <?php if(isset($combo_tool['sub_menus'])) : ?>
                    <div class="mt-1">
                    <?php foreach($combo_tool['sub_menus'] as $sub_menu) :
                        if(!$sub_menu['has_access']) continue;
                    ?>
                      <a href="<?php echo $sub_menu['action_url']; ?>" class="no_hover  btn btn-outline-light text-primary"><i class="<?php echo $sub_menu['icon'] ?>"></i> <?php echo $sub_menu['title']; ?> <i class="fas fa-chevron-right"></i></a>
                    <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>


<style type="text/css">
  .popover{min-width: 330px !important;}
  .no_hover:hover{text-decoration: none;}
  .otn_info_modal{cursor: pointer;}
  #external_sequence_block{ z-index: unset; }
  .wizard-steps{display: block;}
  .wizard-steps .wizard-step:before{content: none;}
  .wizard-steps .wizard-step{height: 270px;}
  .comboposter.wizard-steps .wizard-step{height: 270px;}
  .wizard-step-icon i{font-size: 65px !important;} 
</style>