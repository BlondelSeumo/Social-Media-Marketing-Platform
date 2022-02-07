<?php
// account switch will only show for below links
$social_switch = 
[
  'comment_automation/comment_growth_tools',
  'comment_automation/index',
  'comment_automation/template_manager',
  'comment_automation/comment_section_report',
  'instagram_reply/get_account_lists',
  'instagram_reply/template_manager',
  'instagram_reply/reports',
  'subscriber_manager/bot_subscribers',
  'message_manager/message_dashboard',
  'message_manager/instagram_message_dashboard',
  'messenger_bot/bot_list',
  'messenger_bot/saved_templates'
];
$current_url = current_url();
$found_in_switch = false;
foreach ($social_switch as $key => $value) {
  if(stripos($current_url, '/'.$value) !==false) {
    $found_in_switch = true;
    break;
  }
}
$no_social_switch_classs = !$found_in_switch ? 'd-none' : '';

// account switch will not show for below links
$account_switch = 
[
  'dashboard',
  'integration',
  'comment_automation/comment_growth_tools',
  'comment_automation/comment_template_manager',
  'comment_automation/template_manager',
  'comment_automation/comment_section_report',
  'instagram_reply/reports',
  'messenger_bot_broadcast',
  'sms_email_manager',
  'sms_email_sequence',
  'email_optin_form_builder',
  'comboposter',
  'gmb',
  'search_tools',
  'admin',
  'cron_job',
  'multi_language',
  'addons',
  'themes',
  'blog',
  'menu_manager',
  'update_system',
  'payment',
  'announcement',
  'affiliate_system',
  'member',
  'myprofile',
  'native_api',
  'calendar',
  'simplesupport',
  'team_member'
];
$current_url = current_url();
$found_in_account_switch = false;
foreach ($account_switch as $key => $value) {
  if(stripos($current_url, '/'.$value) !==false || ($this->uri->segment(1)=='ultrapost' && $this->uri->segment(2)=='' )) {
    $found_in_account_switch = true;
    break;
  }
}
$no_account_switch_classs = $found_in_account_switch ? 'd-none' : 'd-none d-lg-block ml-2';
$selected_global_media_type = $this->session->userdata('selected_global_media_type');
if($selected_global_media_type=='') $selected_global_media_type = 'fb';
?>

<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
  <form class="form-inline mr-auto">
     <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg mr-0" id="collapse_me_plz"><i class="fas fa-bars"></i></a>      

     <?php 
        $current_account = isset($fb_rx_account_switching_info[$this->session->userdata("facebook_rx_fb_user_info")]['name']) ? $fb_rx_account_switching_info[$this->session->userdata("facebook_rx_fb_user_info")]['name'] : $this->lang->line("No Account");
        $fb_img = base_url('assets/img/avatar/avatar-1.png');
        if(isset($fb_rx_account_switching_info[$this->session->userdata("facebook_rx_fb_user_info")]['access_token']))
        $fb_img = 'https://graph.facebook.com/me/picture?access_token='.$fb_rx_account_switching_info[$this->session->userdata("facebook_rx_fb_user_info")]['access_token'].'&width=150&height=150';
     ?>

      <ul class="navbar-nav navbar-right d-none d-md-block ml-2 mr-1 facebook">
        <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user <?php echo $no_account_switch_classs;?>">
          <img src="<?php echo $fb_img; ?>" class="rounded-circle mr-1">
          <div class="d-inline"><?php echo $current_account; ?></div></a>
          <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-title"><?php echo $this->lang->line("Interact as"); ?></div>                      
            <?php 
            foreach ($fb_rx_account_switching_info as $key => $value) 
            {              
              echo '<a href="" data-id="'.$key.'" class="dropdown-item account_switch"><i class="fas fa-check-circle text-primary"></i> '.$value['name'].'</a>';
            } 
            ?>

          </div>
        </li>
      </ul>  

      <div class="ltr custom-switches-stacked mt-2 social-media-switch <?php echo $no_social_switch_classs ?>">
          <label class="custom-switch">
            <input type="checkbox" name="selected_global_media_type" id="selected_global_media_type" value="" class="custom-switch-input" <?php echo $selected_global_media_type=='fb' ? 'checked' : '';?>>
            <span class="custom-switch-description mx-1"><i class="fab fa-instagram text-secondary <?php echo $selected_global_media_type=='ig' ? 'gradient' : '';?>"></i></span>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description mx-1"><i class="fab fa-facebook <?php echo $selected_global_media_type=='fb' ? 'text-primary' : 'text-secondary';?>"></i></span>
          </label>
      </div>

  </form>

  <ul class="navbar-nav navbar-right">    
    <?php include(FCPATH.'application/views/admin/theme/notification.php'); ?>
    <?php include(FCPATH.'application/views/admin/theme/usermenu.php'); ?>      
  </ul>

</nav>
