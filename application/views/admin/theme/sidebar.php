<div class="main-sidebar">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt='<?php echo $this->config->item("product_short_name"); ?>'></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <!-- <a href="<?php echo base_url(); ?>dist/index"><i class="fa fa-prescription"></i></a> -->
      <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/img/favicon.png" alt='<?php echo $this->config->item("product_short_name"); ?>'></a>
    </div>
    <ul class="sidebar-menu">
      <?php
        $admin_double_level2=array('admin/activity_log','payment/accounts','payment/earning_summary','payment/transaction_log','blog/posts');
        $all_links=array();
        foreach($menus as $single_menu) 
        {          
            $menu_html= '';
            $only_admin = $single_menu['only_admin'];
            $only_member = $single_menu['only_member']; 
            $module_access = explode(',', $single_menu['module_access']);
            $module_access = array_filter($module_access);
            $color = $single_menu['color'] ?? 'var(--blue)';


            if($single_menu['url']=='social_apps/index' && $single_menu['only_member']=='1' && $this->config->item('backup_mode')==='0' && $this->session->userdata('user_type')=='Member') continue; // static condition not to

            if($single_menu['module_access']=='278,279' && ($this->config->item('instagram_reply_enable_disable')==='0' || $this->config->item('instagram_reply_enable_disable')=='')) continue;
            if($single_menu['module_access']=='296' && ($this->config->item('instagram_reply_enable_disable')==='0' || $this->config->item('instagram_reply_enable_disable')=='')) continue;

            if(!addon_exist($module_id=315,$addon_unique_name="visual_flow_builder") && $single_menu['module_access']=='315') continue;

            if($single_menu['header_text']!='') $menu_html .= '<li class="menu-header">'.$this->lang->line($single_menu['header_text']).'</li>';

            $extraText='';
            if($single_menu['add_ons_id']!='0' && $this->is_demo=='1') $extraText=' <label class="label label-warning" style="font-size:9px;padding:4px 3px;">Addon</label>';

            if($single_menu['have_child']=='1') 
            {
              $dropdown_class1="nav-item dropdown";
              $dropdown_class2="has-dropdown";
            }
            else 
            {
              $dropdown_class1="";
              $dropdown_class2="";
            }
            if($single_menu['is_external']=='1') $site_url1=""; else $site_url1=site_url(); // if external link then no need to add site_url()
            if($single_menu['is_external']=='1') $parent_newtab=" target='_BLANK'"; else $parent_newtab=''; // if external link then open in new tab
            $color_css = $single_menu['url']!='social_accounts/index' ? "background: -webkit-linear-gradient(270deg,".$color.",".adjustBrightness($color,-0.65).");-webkit-background-clip: text;-webkit-text-fill-color: transparent;" : "color:".$color;
            $menu_html .= "<li class='".$dropdown_class1."'><a {$parent_newtab} href='".$site_url1.$single_menu['url']."' class='nav-link ".$dropdown_class2."'><i class= '".$single_menu['icon']."' style='".$color_css."'></i> <span>".$this->lang->line($single_menu['name']).$extraText."</span></a>"; 

            array_push($all_links, $site_url1.$single_menu['url']);  

            if(isset($menu_child_1_map[$single_menu['id']]) && count($menu_child_1_map[$single_menu['id']]) > 0)
            {
              $menu_html .= '<ul class="dropdown-menu">';
              foreach($menu_child_1_map[$single_menu['id']] as $single_child_menu)
              {                  

                  $only_admin2 = $single_child_menu['only_admin'];
                  $only_member2 = $single_child_menu['only_member']; 
                  $color2 = $single_child_menu['color'] ?? '';
                  if(empty($color2)) $color2 = $color;
                  
                  if($this->session->userdata('user_type') == 'Admin' && $this->session->userdata('license_type') != 'double' && in_array($single_child_menu['url'], $admin_double_level2)) continue;

                  if(($only_admin2 == '1' && $this->session->userdata('user_type') == 'Member') || ($only_member2 == '1' && $this->session->userdata('user_type') == 'Admin')) 
                  continue;

                  if($single_child_menu['is_external']=='1') $site_url2=""; else $site_url2=site_url(); // if external link then no need to add site_url()
                  if($single_child_menu['is_external']=='1') $child_newtab=" target='_BLANK'"; else $child_newtab=''; // if external link then open in new tab

                  if($single_child_menu['have_child']=='1') $second_menu_href = '';
                  else $second_menu_href = "href='".$site_url2.$single_child_menu['url']."'";

                  $module_access2 = explode(',', $single_child_menu['module_access']);
                  $module_access2 = array_filter($module_access2);

                  
                  $hide_second_menu = '';
                  if($this->session->userdata('user_type') != 'Admin' && !empty($module_access2) && count(array_intersect($this->module_access, $module_access2))==0) $hide_second_menu = 'hidden';
                  
                  $menu_html .= "<li class='".$hide_second_menu."'><a {$child_newtab} {$second_menu_href} class='nav-link'><i style='color:".$color2."' class='".$single_child_menu['icon']."'></i>".$this->lang->line($single_child_menu['name'])."</a>";

                  array_push($all_links, $site_url2.$single_child_menu['url']);

                  if(isset($menu_child_2_map[$single_child_menu['id']]) && count($menu_child_2_map[$single_child_menu['id']]) > 0)
                  {
                    $menu_html .= "<ul class='dropdown-menu2'>";
                    foreach($menu_child_2_map[$single_child_menu['id']] as $single_child_menu_2)
                    { 
                      $only_admin3 = $single_child_menu_2['only_admin'];
                      $only_member3 = $single_child_menu_2['only_member'];
                      if(($only_admin3 == '1' && $this->session->userdata('user_type') == 'Member') || ($only_member3 == '1' && $this->session->userdata('user_type') == 'Admin'))
                        continue;
                      if($single_child_menu_2['is_external']=='1') $site_url3=""; else $site_url3=site_url(); // if external link then no need to add site_url()
                      if($single_child_menu_2['is_external']=='1') $child2_newtab=" target='_BLANK'"; else $child2_newtab=''; // if external link then open in new tab   

                      $menu_html .= "<li><a {$child2_newtab} href='".$site_url3.$single_child_menu_2['url']."' class='nav-link'><i class='".$single_child_menu_2['icon']."'></i> ".$this->lang->line($single_child_menu_2['name'])."</a></li>";

                      array_push($all_links, $site_url3.$single_child_menu_2['url']);
                    }
                    $menu_html .= "</ul>";
                  }
                  $menu_html .= "</li>";
              }
              $menu_html .= "</ul>";
            }

            $menu_html .= "</li>";
            
            if($only_admin == '1') 
            {
              if($this->session->userdata('user_type') == 'Admin') 
              echo $menu_html;
            }
            else if($only_member == '1') 
            {
              if($this->session->userdata('user_type') == 'Member') 
              echo $menu_html;
            }
            else 
            {
              if($this->session->userdata("user_type")=="Admin" || empty($module_access) || count(array_intersect($this->module_access, $module_access))>0 ) 
              echo $menu_html;
            }             
        }

        if($this->session->userdata('license_type') == 'double' && $this->session->userdata('user_type')=='Member')
        {
          echo'
          <li class="menu-header">'.$this->lang->line("Payment").'</li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link has-dropdown" style="background: -webkit-linear-gradient(270deg,#ffa801,#5a3b01);-webkit-background-clip: text;-webkit-text-fill-color: transparent;"><i class="fa fa-coins"></i> <span>'.$this->lang->line("Payment").'</span></a>
            <ul class="dropdown-menu">
              <li class=""><a href="'.base_url("payment/buy_package").'" style="color:#ffa801" class="nav-link"><i class="fa fa-cart-plus"></i>'.$this->lang->line("Renew Package").'</a></li>
              <li class=""><a href="'.base_url("payment/transaction_log").'" style="color:#ffa801" class="nav-link"><i class="fa fa-history"></i>'.$this->lang->line("Transaction Log").'</a></li>
              <li class=""><a href="'.base_url("payment/usage_history").'" style="color:#ffa801" class="nav-link"><i class="fa fa-user-clock"></i>'.$this->lang->line("Usage Log").'</a></li>
            </ul>
          </li>
          ';
        }
      ?>
    </ul>

    <?php
    if($this->session->userdata('license_type') == 'double')
      if($this->config->item('enable_support') == '1')
        {
          $support_menu = $this->lang->line("Support Desk");
          $support_icon = "fa fa-headset";
          $support_url = base_url('simplesupport/tickets');
          
          echo '
          <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="'.$support_url.'" class="btn btn-primary btn-lg btn-block btn-icon-split">
              <i class="'.$support_icon.'"></i> '.$support_menu.'
            </a>
          </div>';
        }
    ?>

    
  </aside>
</div>



<?php 
$all_links=array_unique($all_links);
$unsetkey = array_search (base_url().'#', $all_links); 
if($unsetkey!=FALSE)
unset($all_links[$unsetkey]); // removing links without a real url

/* 
links that are not in database [custom link = sibebar parent]
No need to add a custom link if it's parent is controller/index
*/
$custom_links=array
(
  base_url("admin/general_settings")=>base_url("admin/settings"),
  base_url("admin/frontend_settings")=>base_url("admin/settings"),
  base_url("admin/smtp_settings")=>base_url("admin/settings"),
  base_url("admin/email_template_settings")=>base_url("admin/settings"),
  base_url("admin/analytics_settings")=>base_url("admin/settings"),
  base_url("admin/advertisement_settings")=>base_url("admin/settings"),
  base_url("admin/add_user")=>base_url("admin/user_manager"),
  base_url("admin/edit_user")=>base_url("admin/user_manager"),
  base_url("admin/login_log")=>base_url("admin/user_manager"),
  base_url("payment/add_package")=>base_url("payment/package_manager"),
  base_url("payment/update_package")=>base_url("payment/package_manager"),
  base_url("payment/details_package")=>base_url("payment/package_manager"),
  base_url("announcement/add")=>base_url("announcement/full_list"),
  base_url("announcement/edit")=>base_url("announcement/full_list"),
  base_url("announcement/details")=>base_url("announcement/full_list"),
  base_url("addons/upload")=>base_url("addons/lists"),
  base_url("comment_automation/all_auto_comment_report")=>base_url("comment_automation/comment_section_report"),
  base_url("comment_automation/all_auto_comment_report/0/0")=>base_url("instagram_reply/reports"),
  base_url("comment_automation/all_auto_reply_report")=>base_url("comment_automation/comment_section_report"),
  base_url("comment_reply_enhancers/bulk_tag_campaign_list")=>base_url("comment_automation/comment_section_report"),
  base_url("comment_reply_enhancers/bulk_comment_reply_campaign_list")=>base_url("comment_automation/comment_section_report"),
  base_url("comment_reply_enhancers/all_response_report")=>base_url("comment_automation/comment_section_report"),
  base_url("comment_reply_enhancers/all_like_share_report")=>base_url("comment_automation/comment_section_report"),
  base_url("messenger_bot_enhancers/checkbox_plugin_list")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/checkbox_plugin_add")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/checkbox_plugin_edit")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/send_to_messenger_list")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/send_to_messenger_add")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/send_to_messenger_edit")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/mme_link_list")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/mme_link_add")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/mme_link_edit")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/customer_chat_plugin_list")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/customer_chat_add")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/customer_chat_edit")=>base_url("messenger_bot"),  
  base_url("messenger_bot_enhancers/subscriber_broadcast_campaign")=>base_url("messenger_bot_broadcast"),
  base_url("messenger_bot_enhancers/create_subscriber_broadcast_campaign")=>base_url("messenger_bot_broadcast"),
  base_url("messenger_bot_enhancers/edit_subscriber_broadcast_campaign")=>base_url("messenger_bot_broadcast"),
  base_url("message_manager/message_dashboard")=>base_url("subscriber_manager"),
  base_url("messenger_bot/tree_view")=>base_url("messenger_bot"),
  base_url("messenger_bot_connectivity/analytics")=>base_url("messenger_bot"),
  base_url("messenger_bot_connectivity/saved_template_view")=>base_url("messenger_bot"),
  base_url("webview_builder")=>base_url("messenger_bot"),
  base_url("webview_builder/manager")=>base_url("messenger_bot"),
  base_url("autoposting/settings")=>base_url("ultrapost"),
  base_url("instagram_poster")=>base_url("ultrapost"),
  base_url("themes/upload") => base_url("themes/lists"),
  base_url("messenger_bot_connectivity/webview_builder_manager") => base_url("messenger_bot"),
  base_url("messenger_bot_connectivity") => base_url("messenger_bot"),
  base_url("messenger_bot_connectivity/edit_webview") => base_url("messenger_bot"),
  base_url("sms_email_manager/contact_group_list") => base_url("subscriber_manager"),
  base_url("sms_email_manager/contact_list") => base_url("subscriber_manager"),
  base_url("sms_email_manager/sms_campaign_lists") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/create_sms_campaign") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/edit_sms_campaign") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/email_campaign_lists") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/create_email_campaign") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/edit_email_campaign") => base_url("messenger_bot_broadcast"),

  base_url("comment_automation/comment_template_manager") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_automation/template_manager") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_automation/index") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_automation/comment_section_report") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_automation/all_auto_comment_report") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_automation/all_auto_reply_report") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_reply_enhancers/bulk_tag_campaign_list") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_reply_enhancers/bulk_comment_reply_campaign_list") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_reply_enhancers/all_response_report") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_reply_enhancers/all_like_share_report") => base_url("comment_automation/comment_growth_tools"),

  base_url("comment_reply_enhancers/post_list") => base_url("comment_automation/comment_growth_tools"),
  base_url("instagram_reply/template_manager") => base_url("comment_automation/comment_growth_tools"),
  base_url("instagram_reply/get_account_lists") => base_url("comment_automation/comment_growth_tools"),
  base_url("comment_automation/all_auto_comment_report") => base_url("comment_automation/comment_growth_tools"),
  base_url("instagram_reply/instagram_autoreply_report/post") => base_url("comment_automation/comment_growth_tools"),
  base_url("instagram_reply/instagram_autoreply_report/full") => base_url("comment_automation/comment_growth_tools"),
  base_url("instagram_reply/instagram_autoreply_report/mention") => base_url("comment_automation/comment_growth_tools"),


  base_url("affiliate_system/request_info") => base_url("affiliate_system/affiliate_users"),
  base_url("affiliate_system/add_affiliate") => base_url("affiliate_system/affiliate_users"),
  base_url("affiliate_system/edit_affiliate") => base_url("affiliate_system/affiliate_users"),


  base_url("comboposter/text_post/campaigns") => base_url("ultrapost"),
  base_url("comboposter/image_post/campaigns") => base_url("ultrapost"),
  base_url("comboposter/video_post/campaigns") => base_url("ultrapost"),
  base_url("comboposter/link_post/campaigns") => base_url("ultrapost"),
  base_url("comboposter/html_post/campaigns") => base_url("ultrapost"),

  base_url("comboposter/text_post/create") => base_url("ultrapost"),
  base_url("comboposter/image_post/create") => base_url("ultrapost"),
  base_url("comboposter/video_post/create") => base_url("ultrapost"),
  base_url("comboposter/link_post/create") => base_url("ultrapost"),
  base_url("comboposter/html_post/create") => base_url("ultrapost"),  

  base_url("comboposter/text_post/edit") => base_url("ultrapost"),
  base_url("comboposter/image_post/edit") => base_url("ultrapost"),
  base_url("comboposter/video_post/edit") => base_url("ultrapost"),
  base_url("comboposter/link_post/edit") => base_url("ultrapost"),
  base_url("comboposter/html_post/edit") => base_url("ultrapost"),

  base_url("comboposter/text_post/clone") => base_url("ultrapost"),
  base_url("comboposter/image_post/clone") => base_url("ultrapost"),
  base_url("comboposter/video_post/clone") => base_url("ultrapost"),
  base_url("comboposter/link_post/clone") => base_url("ultrapost"),
  base_url("comboposter/html_post/clone") => base_url("ultrapost"),

  base_url("blog/add_post") => base_url("blog/posts"),
  base_url("blog/edit_post") => base_url("blog/posts"),
  base_url("blog/tag") => base_url("blog/posts"),
  base_url("blog/category") => base_url("blog/posts"),

  base_url("menu_manager/custom_page") => "",
  base_url("gmb/posts") => base_url("gmb/campaigns"),
  base_url("gmb/create_post") => base_url("gmb/campaigns"),
  base_url("gmb/media_campaigns") => base_url("gmb/campaigns"),
  base_url("gmb/create_media_campaign") => base_url("gmb/campaigns"),
  base_url("gmb/rss") => base_url("gmb/campaigns"),
  base_url("gmb/edit_post") => base_url("gmb/campaigns"),
  base_url("gmb/edit_media_campaign") => base_url("gmb/campaigns"),


  base_url("payment/accounts") => base_url("integration"),
  base_url("social_apps") => base_url("integration"),
  base_url("comboposter/social_accounts") => base_url("integration"),
  base_url("email_auto_responder_integration") => base_url("integration"),
  base_url("messenger_bot_connectivity/json_api_connector") => base_url("integration"),
  base_url("sms_email_manager/sms_api_lists") => base_url("integration"),
  base_url("sms_email_manager/smtp_config") => base_url("integration"),
  base_url("sms_email_manager/mandrill_api_config") => base_url("integration"),
  base_url("sms_email_manager/sendgrid_api_config") => base_url("integration"),
  base_url("sms_email_manager/mailgun_api_config") => base_url("integration"),
  base_url("woocommerce_abandoned_cart") => base_url("integration"),
  base_url("woocommerce_integration") => base_url("integration")

);

$custom_links[base_url("payment/transaction_log_manual")]=base_url("payment/transaction_log");

$custom_links_assoc_str="{";
$loop=0;
foreach ($custom_links as $key => $value) 
{
  $loop++;
  array_push($all_links, $key); // adding custom urls in all urls array

  /* making associative link -> parent array for js, js dont support special chars */
  $custom_links_assoc_str.=str_replace(array('/',':','-','.'), array('FORWARDSLASHES','COLONS','DASHES','DOTS'), $key).":'".$value."'";
  if($loop!=count($custom_links)) $custom_links_assoc_str.=',';
}
$custom_links_assoc_str.="}";
// echo "<pre style='padding-left:300px;'>";
// print_r($all_links);
// echo "</pre>"; 
?>


<script type="text/javascript">

  var all_links_JS = [<?php echo '"'.implode('","', $all_links).'"' ?>]; // all urls includes database & custom urls
  var custom_links_JS= [<?php echo '"'.implode('","', array_keys($custom_links)).'"' ?>]; // only custom urls
  var custom_links_assoc_JS = <?php echo $custom_links_assoc_str?>; // custom urls associative array link -> parent

  var sideBarURL = window.location;
  sideBarURL=String(sideBarURL).trim();
  sideBarURL=sideBarURL.replace('#_=_',''); // redirct from facebook login return extra chars with url

  function removeUrlLastPart(the_url)   // function that remove last segment of a url
  {
      var theurl = String(the_url).split('/');
      theurl.pop();      
      var answer=theurl.join('/');
      return answer;
  }

  // get parent url of a custom url
  function matchCustomUrl(find)
  {
    var parentUrl='';
    var tempu1=find.replace(/\//g, 'FORWARDSLASHES'); // decoding special chars that was encoded to make js array
    tempu1=tempu1.replace(/:/g, 'COLONS');
    tempu1=tempu1.replace(/-/g, 'DASHES');
    tempu1=tempu1.replace(/\./g, 'DOTS');

    if(typeof(custom_links_assoc_JS[tempu1])!=='undefined')
    parentUrl=custom_links_assoc_JS[tempu1]; // getting parent value of custom link

    return parentUrl;
  }

  if(jQuery.inArray(sideBarURL, custom_links_JS) !== -1) // if the current link match custom urls
  {    
    sideBarURL=matchCustomUrl(sideBarURL);
  } 
  else if(jQuery.inArray(sideBarURL, all_links_JS) !== -1) // if the current link match known urls, this check is done later becuase all_links_JS also contains custom urls
  {
     sideBarURL=sideBarURL;
  }
  else // url does not match any of known urls
  {  
    var remove_times=1;
    var temp_URL=sideBarURL;
    var temp_URL2="";
    var tempu2="";
    while(true) // trying to match known urls by remove last part of url or adding /index at the last
    {
      temp_URL=removeUrlLastPart(temp_URL); // url may match after removing last
      temp_URL2=temp_URL+'/index'; // url may match after removing last part and adding /index

      if(jQuery.inArray(temp_URL, custom_links_JS) !== -1) // trimmed url match custom urls
      {
        sideBarURL=matchCustomUrl(temp_URL);
        break;
      }
      else if(jQuery.inArray(temp_URL, all_links_JS) !== -1) //trimmed url match known links
      {
        sideBarURL=temp_URL;
        break;
      }
      else // trimmed url does not match known urls, lets try extending url by adding /index
      {
        if(jQuery.inArray(temp_URL2, custom_links_JS) !== -1) // extended url match custom urls
        {
          sideBarURL=matchCustomUrl(temp_URL2);
          break;
        }
        else if(jQuery.inArray(temp_URL2, all_links_JS) !== -1)  // extended url match known urls
        {
          sideBarURL=temp_URL2;
          break;
        }
      }
      remove_times++;
      if(temp_URL.trim()=="") break;
    }    
  }

  $('ul.sidebar-menu a').filter(function() {
     return this.href == sideBarURL;
  }).parent().addClass('active');
  $('ul.dropdown-menu a').filter(function() {
     return this.href == sideBarURL;
  }).parentsUntil(".sidebar-menu > .dropdown-menu").addClass('active');
</script>