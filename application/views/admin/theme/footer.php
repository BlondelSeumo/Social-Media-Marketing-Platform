<footer class="main-footer">
  <div class="footer-left">
    &copy; <?php  echo $this->config->item("product_short_name")." ";?> <div class="bullet"></div>  <?php echo '<a  href="'.site_url().'">'.$this->config->item("institute_address1").'</a>'; ?>
  </div>
  <div class="footer-right">

  	<?php $current_language = isset($language_info[$this->language]) ? $language_info[$this->language] : $this->lang->line("Language"); ?>
    <a href="#" data-toggle="dropdown" class="dropdown-toggle dropdown-item has-icon d-inline">  <?php echo $current_language; ?></a>
    <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
      <li class="dropdown-title"><?php echo $this->lang->line("Switch Language"); ?></li>
      <?php 
      foreach ($language_info as $key => $value) 
      {
        $selected='';
        // if($key==$this->session->userdata("facebook_rx_fb_user_info")) $selected='active';
        echo '<li><a href="" data-id="'.$key.'" class="dropdown-item language_switch '.$selected.'">'.$value.'</a></li>';
      } 
      ?>
    </ul>

    <!-- v<?php echo $this->APP_VERSION;?> -->
  </div>
</footer>
