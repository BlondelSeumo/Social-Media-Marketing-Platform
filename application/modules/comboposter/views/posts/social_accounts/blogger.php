<div class="card">
  <div class="card-header" style="border:.5px solid #ececec;">
    <h4 class="d-inline"><i class="fab fa-blogger-square"></i> <?php echo $this->lang->line("Blogger"); ?></h4>
  </div>
  <div class="card-body p-0">
    <div class="row multi_layout">
     
        <div class="col-12 col-md-5 collef">
          <div class="card main_card">
            
            <div class="card-body padding-0">
              <div class="makeScroll_1">
                <ul class="list-unstyled list-unstyled-border">
                  <?php $i = 0; ?>
                  <?php foreach ($blogger_account_list as $key => $single_account): ?>

                    <li class="nav-item blogger_accounts_list media pl-3 pt-2 pb-2 pr-2 <?php if ($i == 0) echo "force_active"; ?>" style="margin-bottom: 0 !important;" blogger_id="<?php echo $single_account['id']; ?>">

                        <img class="mr-3 rounded-circle" width="45" src="<?php echo $single_account['picture']; ?>" alt="avatar">
                        <div class="media-body">
                            <div class="pointer">
                                <h6 class="media-title" style="font-size: 14px;"><?php echo $single_account['name']; ?></h6>
                                <div class="text-small text-muted" style="font-size: 10px;"><?php echo $key; ?></div>
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

          <div class="text-center blogger_waiting" style="display: none;">
            <i class="fas fa-spinner fa-spin blue text-center" style="font-size: 50px;margin-top: 230px;"></i>
          </div>

          <div id="blogger_middle_column_content">
            <div class="tab-content">
              <?php $i = 1; ?>
              <?php foreach ($blogger_account_list as $single_account): ?>

                
                <div class="tab-pane blogger_account_tab" id="b_account_tab-<?php echo $single_account['id'] ?>">
                  <div class="card main_card">
                    <div class="card-header" style="border-bottom-color:#f3f3f3 !important">
                        <div class="row">
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" blogger_id="<?php echo $single_account['id']; ?>" class="select_all_blogger_blog custom-control-input" id="select_blogger_accounts_all_blogs-<?php echo $single_account['id']; ?>">
                                    <label class="custom-control-label float-right" for="select_blogger_accounts_all_blogs-<?php echo $single_account['id']; ?>"></label>
                                </div>
                            </div>
                            <div class="col-10 pl-1">
                                <input type="text" class="form-control float-right search_blog_list"  onkeyup="search_in_ul(this,'blog_list_ul')" placeholder="Search...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body padding-10">
                      <ul class="makeScroll_1 list-unstyled list-unstyled-border" id="blog_list_ul">
                        
                        <?php if (count($single_account['blog_info']) > 0): ?>
                            
                          <?php foreach ($single_account['blog_info'] as $single_blog): ?>

                            <li class="media pl-3 pt-2 pr-2 pb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="single_blogger_blog-<?php echo $single_account['id']; ?> custom-control-input" name="blogger_blogs[]" id="blogger_cbx-<?php echo $i; ?>" value="blogger_blog_info-<?php echo $single_blog['table_id']; ?>"  <?php 
                                         if (($post_action == 'edit' || $post_action == 'clone') && count($campaigns_social_media) > 0) {
                                             $temp = "blogger_blog_info-" . $single_blog['table_id'];
                                             if (in_array($temp, $campaigns_social_media)) {
                                               echo "checked";
                                             }
                                         }
                                      ?> >
                                    <label class="mb-3 custom-control-label" for="blogger_cbx-<?php echo $i; ?>"></label>
                                </div><img class="mr-3 rounded-circle mCS_img_loaded" width="35" src="<?php echo base_url('assets/images/blogger.jpg'); ?>" alt="avatar">
                                <div class="media-body">
                                    <h6 class="blog_list_media_title media-title"><?php echo $single_blog['blog_name'] ?></a></h6>
                                    <div class="text-small text-muted"><?php echo $single_blog['blog_id'] ?></div>
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
