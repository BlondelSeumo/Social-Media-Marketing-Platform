<div class="card">
  <div class="card-header" style="border:.5px solid #ececec;border-bottom: none;">
    <div class="custom-control custom-checkbox">
      <input type="checkbox" class="custom-control-input reddit_all_account_select" id="reddit_all_account_select" >
      <label class="mb-3 custom-control-label" for="reddit_all_account_select" title="<?php echo $this->lang->line("Select all"); ?>"></label>
    </div>
    <div class="pl-3 mt-1">
      <h4 class="d-inline"><i class="fab fa-reddit-square" style="font-size: 14px;"></i> <?php echo $this->lang->line("Reddit"); ?></h4>
    </div>
  </div>
  <div class="card-body account_div_height" style="border:.5px solid #ececec;">
    <div class="row">
      <div class="col-7 makeScroll_1">
          <ul class="list-unstyled list-unstyled-border">
              
              <?php $i = 0; ?>
              <?php foreach ($reddit_account_list as $key => $single_account): ?>

                <li class="media">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="reddit_accounts[]" class="custom-control-input reddit_single_user" id="reddit_single_user-<?php echo $i; ?>" value="reddit_users_info-<?php echo $single_account['id'] ?>"  <?php 
                         if (($post_action == 'edit' || $post_action == 'clone') && count($campaigns_social_media) > 0) {
                             $temp = "reddit_users_info-" . $single_account['id'];
                             if (in_array($temp, $campaigns_social_media)) {
                               echo "checked";
                             }
                         }
                      ?> >
                      <label class="mb-2 custom-control-label" for="reddit_single_user-<?php echo $i; ?>"></label>
                    </div>
                    <img class="mr-3 rounded-circle" width="50" src="<?php echo $single_account['profile_pic']; ?>" alt="avatar">
                    <div class="media-body">
                        <div class="accounts_details_collapse pointer">
                            <h6 class="media-title"><?php echo $single_account['username']; ?></h6>
                            <div class="text-job text-muted"><a href="<?php echo 'https://www.reddit.com'.$single_account['url']; ?>"><?php echo $this->lang->line("Visit Reddit"); ?></a></div>
                        </div>
                    </div>
                    

                </li>
                
                <?php $i++; ?>
              <?php endforeach ?>

          </ul>
      </div>
      <div class="col-5">
          
          <div class="form-group">
            <label style="font-size: 14px;"><?php echo $this->lang->line('Please select at least one subreddit'); ?></label>
            <?php
              if ($post_action == 'edit' || $post_action == 'clone') {
                $default_value = $campaign_form_info['subreddits'];
              } else {
                $default_value = '0';
              }
              echo form_dropdown('subreddits', $subreddits, $default_value, 'class="form-control select2" style="width: 100%"'); 
             ?>
          </div>
          
      </div>
    </div>

  </div>
</div>
