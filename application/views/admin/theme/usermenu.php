<li class="dropdown" id="xxx"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
  <img src="<?php echo $this->session->userdata("brand_logo"); ?>" class="rounded-circle mr-1">
  <div class="d-none d-md-inline-block"><?php echo $this->session->userdata('username'); ?></div></a>
  <div class="dropdown-menu dropdown-menu-right">

    <div class="dropdown-title"><?php echo $this->config->item("product_short_name")." - ".$this->lang->line($this->session->userdata("user_type")); ?></div>
    <a href="<?php echo base_url('myprofile/edit_profile'); ?>" class="dropdown-item has-icon">
      <i class="far fa-user"></i> <?php echo $this->lang->line("Profile"); ?>
    </a>
    <a href="<?php echo base_url('calendar/index'); ?>" class="dropdown-item has-icon">
      <i class="fas fa-bolt"></i> <?php echo $this->lang->line("Activities"); ?>
    </a>

    <?php if($this->basic->is_exist("add_ons",array("unique_name"=>"api_documentation"))) : ?>
        <?php if($this->session->userdata('user_type') == 'Admin' || in_array(285, $this->module_access)) : ?>
        <a href="<?php echo base_url('native_api/get_api_key'); ?>" class="dropdown-item has-icon">
          <i class="fas fa-plug"></i> <?php echo $this->lang->line("API Key"); ?>
        </a>
        <?php endif; ?>
    <?php endif; ?>

  
    <div class="dropdown-divider"></div>  
    <div class="dropdown-title"><i class="fab fa-facebook"></i> <?php echo $this->lang->line("Facebook Account"); ?></div>
    <?php $current_account = isset($fb_rx_account_switching_info[$this->session->userdata("facebook_rx_fb_user_info")]['name']) ? $fb_rx_account_switching_info[$this->session->userdata("facebook_rx_fb_user_info")]['name'] : $this->lang->line("No Account"); ?>
    <a class="dropdown-item has-icon active" data-toggle="collapse" href="#collapseExampleFBA" role="button" aria-expanded="false" aria-controls="collapseExampleFBA">
     <?php echo $current_account; ?> (<?php echo $this->lang->line("Change"); ?>)
    </a>
    <div class="collapse" id="collapseExampleFBA">
      <?php 
      foreach ($fb_rx_account_switching_info as $key => $value) 
      {
        $selected='';
        if($key==$this->session->userdata("facebook_rx_fb_user_info")) $selected='d-none';
        echo '<a href="" data-id="'.$key.'" class="dropdown-item account_switch '.$selected.'"><i class="fas fa-check-circle"></i> '.$value['name'].'</a>';
      } 
      ?>
    </div>

    <!-- for gmb add-on -->
    <?php if($gmb_addon_access == 'yes') : ?>
    <div class="dropdown-divider"></div>    
    <div class="dropdown-title"><i class="fab fa-google"></i> <?php echo $this->lang->line("GMB Account"); ?></div>
    <?php $current_account = isset($gmb_account_switching_info[$this->session->userdata("google_mybusiness_user_table_id")]) ? $gmb_account_switching_info[$this->session->userdata("google_mybusiness_user_table_id")] : $this->lang->line("No Account"); ?>

    <a class="dropdown-item has-icon active" data-toggle="collapse" href="#collapseExampleGMB" role="button" aria-expanded="false" aria-controls="collapseExampleGMB">
     <?php echo $current_account; ?> (<?php echo $this->lang->line("Change"); ?>)
    </a>
    <div class="collapse" id="collapseExampleGMB">
      <?php 
      foreach ($gmb_account_switching_info as $key => $value) 
      {
        $selected='';
        if($key==$this->session->userdata("google_mybusiness_user_table_id")) $selected='d-none';
        echo '<a href="" data-id="'.$key.'" class="dropdown-item gmb_account_switch '.$selected.'"><i class="fas fa-toggle-on"></i> '.$value.'</a>';
      } 
      ?>
    </div>
    <?php endif; ?>
    <!-- end of gmb add-on -->

    <div class="dropdown-divider"></div>
    <a href="<?php echo base_url('change_password/reset_password_form'); ?>" class="dropdown-item has-icon">
      <i class="fas fa-key"></i> <?php echo $this->lang->line("Change Password"); ?>
    </a>  

    <a href="<?php echo base_url('home/logout'); ?>" class="dropdown-item has-icon text-danger">
      <i class="fas fa-sign-out-alt"></i> <?php echo $this->lang->line("Logout"); ?>
    </a>


  </div>
</li>