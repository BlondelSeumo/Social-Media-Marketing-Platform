<div class="card">
  <div class="card-header" style="border:.5px solid #ececec;">
    <h4 class="d-inline"><i class="fab fa-facebook-square"></i> <?php echo $this->lang->line("Facebook"); ?></h4>
  </div>
  <div class="card-body p-0">
    <div class="row multi_layout">
     
        <div class="col-12 col-md-5 collef">
          <div class="card main_card">
            
            <div class="card-body padding-0">
              <div class="makeScroll_1">
                <ul class="list-unstyled list-unstyled-border">
                  <?php $i = 0; ?>
                  <?php foreach ($facebook_account_list as $single_account): ?>

                    <li class="nav-item facebook_accounts_list media pl-3 pt-2 pb-2 pr-2 <?php if ($i == 0) echo "force_active"; ?>" style="margin-bottom: 0 !important;" facebook_id="<?php echo $single_account['fb_id']; ?>">

                        <img class="mr-3 rounded-circle" width="45" src="<?php echo "https://graph.facebook.com/me/picture?access_token=". $single_account['user_access_token'] ."&amp;width=60&amp;height=60"; ?>" alt="avatar">
                        <div class="media-body">
                            <div class="pointer">
                                <h6 class="media-title" style="font-size: 14px;"><?php echo $single_account['name']; ?></h6>
                                <div class="text-small text-muted" style="font-size: 11px;"><?php echo $single_account['email']; ?></div>
                            </div>
                        </div>

                    </li>
                    
                    <?php $i++; ?>
                  <?php endforeach ?>              
                </ul>
              </div>
            </div>
          </div>          
        </div>

        <div class="col-12 col-md-7 colmid" id="middle_column">

          <div class="text-center facebook_waiting" style="display: none;">
            <i class="fas fa-spinner fa-spin blue text-center" style="font-size: 50px;margin-top: 230px;"></i>
          </div>

          <div id="facebook_middle_column_content">
            <div class="tab-content">
              <?php $i = 1; ?>
              <?php foreach ($facebook_account_list as $single_account): ?>
                
                <div class="tab-pane facebook_account_tab" id="account_tab-<?php echo $single_account['fb_id'] ?>">
                  <div class="card main_card">
                    <div class="card-header" style="border-bottom-color:#f3f3f3 !important">
                        <div class="row">
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" facebook_id="<?php echo $single_account['fb_id']; ?>" class="select_all_facebook_page custom-control-input" id="select_facebook_accounts_all_pages-<?php echo $single_account['fb_id']; ?>">
                                    <label class="custom-control-label float-right" for="select_facebook_accounts_all_pages-<?php echo $single_account['fb_id']; ?>"></label>
                                </div>
                            </div>
                            <div class="col-10 pl-1">
                                <input type="text" class="form-control float-right search_page_list"  onkeyup="search_in_ul(this,'page_list_ul')" placeholder="Search...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body padding-10">
                      <ul class="makeScroll_1 list-unstyled list-unstyled-border" id="page_list_ul">
                        
                        <?php if ($single_account['total_pages'] > 0): ?>

                          <?php foreach ($single_account['page_list'] as $single_page): ?>
                            <li class="media pl-3 pt-2 pr-2 pb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="single_facebook_page-<?php echo $single_account['fb_id']; ?> custom-control-input" name="facebook_pages[]" id="facebook_cbx-<?php echo $i; ?>" value="facebook_rx_fb_page_info-<?php echo $single_page['id']; ?>" <?php 
                                        if (($post_action == 'edit' || $post_action == 'clone') && count($campaigns_social_media) > 0) {
                                            $temp = "facebook_rx_fb_page_info-" . $single_page['id'];
                                            if (in_array($temp, $campaigns_social_media)) {
                                              echo "checked";
                                            }
                                        }
                                     ?> >

                                    <label class="mb-3 custom-control-label" for="facebook_cbx-<?php echo $i; ?>"></label>
                                </div><img class="mr-3 rounded-circle mCS_img_loaded" width="35" src="<?php echo $single_page['page_profile'] ?>" alt="avatar">
                                <div class="media-body">
                                    <h6 class="page_list_media_title media-title <?php if ($single_page['username'] == '') echo "mt-2" ?>"><?php echo $single_page['page_name'] ?></a></h6>
                                    <?php if ($single_page['username'] != ''): ?>
                                        <div class="text-small text-muted"><?php echo $single_page['username'] ?></div>
                                    <?php endif ?>
                                </div>
                            </li>
                          
                          <?php $i++; ?>
                          <?php endforeach ?>

                        <?php endif ?>

                        

                        
                      </ul>
                    </div>
                  </div>
                </div>

              <?php endforeach ?>
              

            </div>
          </div>
        </div>     

    </div>
  </div>
</div>
