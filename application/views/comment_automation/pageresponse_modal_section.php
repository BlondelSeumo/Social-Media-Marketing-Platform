<div class="modal fade" id="pageresponse_auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" style="padding: 10px 20px 10px 20px;"><?php echo $this->lang->line("please give the following information for page response") ?></h5>
                <button type="button" id='modal_close' class="close">&times;</button>
            </div>
            <form action="#" id="pageresponse_auto_reply_info_form" method="post">
              <input type="hidden" name="pageresponse_auto_reply_page_id" id="pageresponse_auto_reply_page_id" value="">
              <input type="hidden" name="pageresponse_auto_reply_post_id" id="pageresponse_auto_reply_post_id" value="">
              <input type="hidden" name="pageresponse_manual_enable" id="pageresponse_manual_enable" value="">
            <div class="modal-body" id="pageresponse_auto_reply_message_modal_body">  
              <!-- comment hide and delete section -->
          
          <div class="row" style="padding: 20px; <?php if(!$commnet_hide_delete_addon) echo "display: none;"; ?> ">
            <div class="col-12" style="margin-bottom: 20px;">
              <div class="row">
                <div class="col-6 col-md-6" style="">
                  <label><i class="fa fa-ban"></i> <?php echo $this->lang->line("what do you want about offensive comments?") ?></label>
                </div>
                <div class="row">
                  <div class="col-6 col-md-6">
                    <label class="custom-switch">
                      <input type="radio" name="pageresponse_delete_offensive_comment" value="hide" id="pageresponse_delete_offensive_comment_hide" class="custom-switch-input" checked>
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description"><?php echo $this->lang->line('hide'); ?></span>
                    </label>
                  </div>
                  <div class="col-6 col-md-6">
                    <label class="custom-switch">
                      <input type="radio" name="pageresponse_delete_offensive_comment" value="delete" id="pageresponse_delete_offensive_comment_delete" class="custom-switch-input">
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description"><?php echo $this->lang->line('delete'); ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <br/><br/>
            <div class="col-12" style="">
              <div class="row">
                <div class="col-12 col-md-6" id="pageresponse_delete_offensive_comment_keyword_div">
                  <div class="form-group" style="border: 1px dashed #e4e6fc; padding: 10px;">
                    <label><i class="fa fa-tags"></i> <small><?php echo $this->lang->line("write down the offensive keywords in comma separated") ?></small>
                      <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("offensive keywords") ?>" data-content="<?php echo $this->lang->line('write your'); ?>"> <i class='fa fa-info-circle'></i> </a>
                    </label>
                    <textarea class="form-control message" name="pageresponse_delete_offensive_comment_keyword" id="pageresponse_delete_offensive_comment_keyword" placeholder="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions") ?>" style="height:59px !important;"></textarea>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 10px;">
                    <label><small>
                      <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply after deleting offensive comment") ?></small>
                    </label>
                    <div>                      
                      <select class="form-group private_reply_postback" id="pageresponse_private_message_offensive_words" name="pageresponse_private_message_offensive_words">
                        <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                      </select>

                      <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                      <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>  
        <!-- end of comment hide and delete section -->
        <div class="row" style="padding: 10px 20px 10px 20px;">
          <!-- added by mostofa on 26-04-2017 -->
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-6" style=""><label><i class="fa fa-sort-numeric-down"></i> <?php echo $this->lang->line("do you want to send reply message to a user multiple times?") ?></label></div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                <label class="custom-switch">
                  <input type="checkbox" name="pageresponse_multiple_reply" value="yes" id="pageresponse_multiple_reply" class="custom-switch-input">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-6" style="">
                <label><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line("do you want to enable comment reply?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                <label class="custom-switch">
                  <input type="checkbox" name="pageresponse_comment_reply_enabled" value="yes" id="pageresponse_comment_reply_enabled" class="custom-switch-input" checked>
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-6" style="">
                <label><i class="fa fa-comments"></i> <?php echo $this->lang->line("do you want to like on comment by page?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                <label class="custom-switch">
                  <input type="checkbox" name="pageresponse_auto_like_comment" value="yes" id="pageresponse_auto_like_comment" class="custom-switch-input">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                </label>
                </div> 
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <!-- comment hide and delete section -->
          <div class="col-12" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?>>
            <div class="row">
              <div class="col-12 col-md-6" style="">
                <label><i class="fa fa-eye-slash"></i> <?php echo $this->lang->line("do you want to hide comments after comment reply?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                  <input type="checkbox" name="pageresponse_hide_comment_after_comment_reply" value="yes" id="pageresponse_hide_comment_after_comment_reply" class="custom-switch-input">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <!-- comment hide and delete section -->
          <div class="col-12">
            <div class="custom-control custom-radio">
            <input type="radio" name="pageresponse_message_type" value="generic" id="pageresponse_generic" class="custom-control-input radio_button">
            <label class="custom-control-label" for="pageresponse_generic"><?php echo $this->lang->line("generic message for all") ?></label>
            </div>
            <div class="custom-control custom-radio">
            <input type="radio" name="pageresponse_message_type" value="filter" id="pageresponse_filter" class="custom-control-input radio_button">
            <label class="custom-control-label" for="pageresponse_filter"><?php echo $this->lang->line("send message by filtering word/sentence") ?></label>
            </div>
          </div>

          <div class="col-12" style="margin-top: 15px;">
            <div class="form-group">
              <label>
                <i class="fa fa-monument"></i> <?php echo $this->lang->line("auto reply campaign name") ?> <span class="red">*</span>
              </label>
              <input class="form-control" type="text" name="pageresponse_auto_campaign_name" id="pageresponse_auto_campaign_name" placeholder="<?php echo $this->lang->line("write your auto reply campaign name here") ?>">
            </div>
          </div>
          <div class="col-12" id="pageresponse_generic_message_div" style="display: none;">
            <div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 20px;">
              <label>
                <i class="fa fa-envelope"></i> <?php echo $this->lang->line("Message for comment reply") ?> <span class="red">*</span>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
              </label>
              <?php if($comment_tag_machine_addon) {?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
              </span>
              <?php } ?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
              </span>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
              </span>  
  		        <div class="clearfix"></div>           
              <textarea class="form-control message" name="pageresponse_generic_message" id="pageresponse_generic_message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"></textarea>
                
                <!-- comment hide and delete section -->
                <br/>
                <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                  <div class="row">
                    <div class="col-12 col-md-6">
                      <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?></label>                  
                      <div class="form-group">      
                          <div id="pageresponse_generic_comment_image"><?php echo $this->lang->line("upload") ?></div>      
                      </div>
                      <div id="pageresponse_generic_image_preview_id"></div>
                      <span class="red" id="pageresponse_generic_image_for_comment_reply_error"></span>
                      <input type="text" name="pageresponse_generic_image_for_comment_reply" class="form-control" id="pageresponse_generic_image_for_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Preferred");?>]
                        <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("Image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                      </label>
                      <div class="form-group">      
                                    <div id="pageresponse_generic_video_upload"><?php echo $this->lang->line("upload") ?></div>       
                      </div>
                      <div id="pageresponse_generic_video_preview_id"></div>
                      <span class="red" id="pageresponse_generic_video_comment_reply_error"></span>
                      <input type="hidden" name="pageresponse_generic_video_comment_reply" class="form-control" id="pageresponse_generic_video_comment_reply" placeholder="<?php echo $this->lang->line("Put your image url here or click upload") ?>"  />
                    </div>
                  </div>
                </div>
                <br/><br/>
                <!-- comment hide and delete section -->

              <label>
                <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
              </label>
              <div>                      
                <select class="form-group private_reply_postback" id="pageresponse_generic_message_private" name="pageresponse_generic_message_private">
                  <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                </select>

                <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

              </div> 


          </div>
          </div>
          <div class="col-12" id="pageresponse_filter_message_div" style="display: none;">
            <div class="row">
              <div class="col-12 col-md-6">
              <label class="custom-switch">
                <input type="radio" name="pageresponse_trigger_matching_type" value="exact" id="pageresponse_trigger_keyword_exact" class="custom-switch-input" checked>
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"><?php echo $this->lang->line('Reply if the filter word exactly matches.'); ?></span>
              </label>
              </div>
              <div class="col-12 col-md-6">
              <label class="custom-switch">
                <input type="radio" name="pageresponse_trigger_matching_type" value="string" id="pageresponse_trigger_keyword_string" class="custom-switch-input">
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"><?php echo $this->lang->line('Reply if any matches occurs with filter word.'); ?>
              </label>
              </div>
            </div><br/>
            <?php for ($i=1; $i <= 20 ; $i++) : ?>
                <div class="form-group clearfix" id="pageresponse_filter_div_<?php echo $i; ?>" style="border: 1px dashed #e4e6fc; padding: 20px; margin-bottom: 50px;">
                  <label>
                    <i class="fa fa-tag"></i> <?php echo $this->lang->line("filter word/sentence") ?> <span class="red">*</span>
                    <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the word or sentence for which you want to filter comment. For multiple filter keyword write comma separated. Example -   why, wanto to know, when") ?>"><i class='fa fa-info-circle'></i> </a>
                  </label>
                  <input class="form-control filter_word" type="text" name="pageresponse_filter_word_<?php echo $i; ?>" id="pageresponse_filter_word_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("write your filter word here") ?>">
                  
                 
                  <!-- new feature comment reply section -->
                  <br/>
                  <label>
                    <i class="fa fa-envelope"></i> <?php echo $this->lang->line("msg for comment reply") ?><span class="red">*</span>
                    <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send based on filter words. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                  </label>
                  <?php if($comment_tag_machine_addon) {?>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
                  </span>
                  <?php } ?>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                  </span>
                  <span class='float-right'> 
                    <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                  </span> 
				  <div class="clearfix"></div>
                  <textarea class="form-control message" name="pageresponse_comment_reply_msg_<?php echo $i; ?>" id="pageresponse_comment_reply_msg_<?php echo $i; ?>"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"></textarea>
                  
                  <!-- comment hide and delete section -->
                  <br/>
                  <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                    <div class="row">
                      <div class="col-12 col-md-6">
                        <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                        </label>                  
                        <div class="form-group">      
                            <div id="pageresponse_filter_image_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>      
                        </div>
                        <div id="pageresponse_generic_image_preview_id_<?php echo $i; ?>"></div>
                        <span class="red" id="pageresponse_generic_image_for_comment_reply_error_<?php echo $i; ?>"></span>
                        <input type="text" name="pageresponse_filter_image_upload_reply_<?php echo $i; ?>" class="form-control" id="pageresponse_filter_image_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("Put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Preferred");?>]
                          <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("Image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                        </label>
                        <div class="form-group">      
                                      <div id="pageresponse_filter_video_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>      
                        </div>
                        <div id="pageresponse_generic_video_preview_id_<?php echo $i; ?>"></div>
                        <span class="red" id="pageresponse_edit_generic_video_comment_reply_error_<?php echo $i; ?>"></span>
                        <input type="hidden" name="pageresponse_filter_video_upload_reply_<?php echo $i; ?>" class="form-control" id="pageresponse_filter_video_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("Put your image url here or click upload") ?>"  />
                      </div>
                    </div>
                  </div>
                  <!-- comment hide and delete section -->

                  <br/>

                  <label>
                    <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
                  </label>
                  <div>                      
                    <select class="form-group private_reply_postback" id="pageresponse_filter_message_<?php echo $i; ?>" name="pageresponse_filter_message_<?php echo $i; ?>">
                      <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                    </select>

                    <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                    <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

                  </div>

                </div>
            <?php endfor; ?>
            
             <div class="clearfix">
              <input type="hidden" name="pageresponse_content_counter" id="pageresponse_content_counter" />
              <button type="button" class="btn btn-sm btn-outline-primary float-right" id="pageresponse_add_more_button"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add more filtering") ?></button>
            </div>
            <div class="form-group clearfix" id="pageresponse_nofilter_word_found_div" style="margin-top: 10px; border: 1px dashed #e4e6fc; padding: 20px;">
              <label>
                <i class="fa fa-envelope"></i> <?php echo $this->lang->line("comment reply if no matching found") ?>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the message,  if no filter word found. If you don't want to send message them, just keep it blank ."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
              </label>
              <?php if($comment_tag_machine_addon) {?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
              </span>
              <?php } ?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
              </span>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
              </span> 
			     <div class="clearfix"></div>
              <textarea class="form-control message" name="pageresponse_nofilter_word_found_text" id="pageresponse_nofilter_word_found_text"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"></textarea>
             
              <!-- comment hide and delete section -->
              <br/>
              <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                <div class="row">
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                    </label>                  
                    <div class="form-group">      
                        <div id="pageresponse_nofilter_image_upload"><?php echo $this->lang->line("upload") ?></div>      
                    </div>
                    <div id="pageresponse_nofilter_generic_image_preview_id"></div>
                    <span class="red" id="pageresponse_nofilter_image_upload_reply_error"></span>
                    <input type="text" name="pageresponse_nofilter_image_upload_reply" class="form-control" id="pageresponse_nofilter_image_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Preferred");?>]
                      <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("Image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                    </label>
                    <div class="form-group">      
                      <div id="pageresponse_nofilter_video_upload"><?php echo $this->lang->line("upload") ?></div>      
                    </div>
                    <div id="pageresponse_nofilter_video_preview_id"></div>
                    <span class="red" id="pageresponse_nofilter_video_upload_reply_error"></span>
                    <input type="hidden" name="pageresponse_nofilter_video_upload_reply" class="form-control" id="pageresponse_nofilter_video_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>"  />
                  </div>
                </div>
              </div>
              <br/><br/>
              <!-- comment hide and delete section -->

              <label>
                <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply if no matching found") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
              </label>
              <div>                      
                <select class="form-group private_reply_postback" id="pageresponse_nofilter_word_found_text_private" name="pageresponse_nofilter_word_found_text_private">
                  <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                </select>

                <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

              </div>

            
            </div>

          </div>
        </div>
        <div class="col-12 text-center" id="pageresponse_response_status"></div>
            </div>
            </form>

            <div class="modal-footer" style="padding-left: 45px; padding-right: 45px; ">
              <div class="row">
                <div class="col-6">
                  <button class="btn btn-lg btn-primary float-left" id="pageresponse_save_button"><i class="fa fa-save"></i> <?php echo $this->lang->line("save") ?></button>
                </div>  
                <div class="col-6">
                  <button class="btn btn-lg btn-secondary float-right cancel_button"><i class='fas fa-times'></i> <?php echo $this->lang->line("cancel") ?></button>
                </div>
              </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="pageresponse_edit_auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" style="padding: 10px 20px 10px 20px;"><i class="fas fa-share-square"></i> <?php echo $this->lang->line("please give the following information for page response") ?></h5>
                <button type="button" id='edit_modal_close' class="close">&times;</button>
            </div>
            <form action="#" id="pageresponse_edit_auto_reply_info_form" method="post">
              <input type="hidden" name="pageresponse_edit_auto_reply_page_id" id="pageresponse_edit_auto_reply_page_id" value="">
              <input type="hidden" name="pageresponse_edit_auto_reply_post_id" id="pageresponse_edit_auto_reply_post_id" value="">
            <div class="modal-body" id="pageresponse_edit_auto_reply_message_modal_body"> 
			
  			<div class="text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
        
        <br/>
			  
              <!-- comment hide and delete section -->
        <div class="row" style="padding: 20px;<?php if(!$commnet_hide_delete_addon) echo "display: none;"; ?> ">
          <div class="col-12" style="margin-bottom: 20px;">
            <div class="row">
              <div class="col-12 col-md-6" style="">
                <label><i class="fa fa-ban"></i> <?php echo $this->lang->line("what do you want about offensive comments?") ?></label>
              </div>
              <div class="row">
                <div class="col-12 col-md-6">
                  <label class="custom-switch">
                    <input type="radio" name="pageresponse_edit_delete_offensive_comment" value="hide" id="pageresponse_edit_delete_offensive_comment_hide" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('hide'); ?></span>
                  </label>
                </div>
                <div class="col-12 col-md-6">
                  <label class="custom-switch">
                    <input type="radio" name="pageresponse_edit_delete_offensive_comment" value="delete"  id="pageresponse_edit_delete_offensive_comment_delete" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('delete'); ?>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <br/><br/>
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-6" id="pageresponse_edit_delete_offensive_comment_keyword_div">
                <div class="form-group" style="border: 1px dashed #e4e6fc; padding: 10px;">
                  <label><i class="fa fa-tags"></i> <small><?php echo $this->lang->line("write down the offensive keywords in comma separated") ?></small>
                    <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("offensive keywords") ?>" data-content="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions"); ?> "><i class='fa fa-info-circle'></i> </a>
                  </label>
                  <textarea class="form-control message" name="pageresponse_edit_delete_offensive_comment_keyword" id="pageresponse_edit_delete_offensive_comment_keyword" placeholder="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions"); ?>" style="height:59px !important;"></textarea>
                </div>
              </div>

              <div class="col-12 col-md-6">
                <div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 10px;">
                  <label><small>
                    <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply after deleting offensive comment") ?></small>
                  </label>
                  <div>                      
                    <select class="form-group private_reply_postback" id="pageresponse_edit_private_message_offensive_words" name="pageresponse_edit_private_message_offensive_words">
                      <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                    </select>

                    <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                    <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

                  </div>
                </div>
              </div>


          </div>
          </div>
        </div> 
        <!-- end of comment hide and delete section -->
        <div class="row" style="padding: 10px 20px 10px 20px;">
          <!-- added by mostofa on 26-04-2017 -->
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-6" style=""><label><i class="fa fa-sort-numeric-down"></i> <?php echo $this->lang->line("do you want to send reply message to a user multiple times?") ?></label></div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" name="pageresponse_edit_multiple_reply" value="yes" id="pageresponse_edit_multiple_reply" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-6" style="">
                <label><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line("do you want to enable comment reply?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" name="pageresponse_edit_comment_reply_enabled" value="yes" id="pageresponse_edit_comment_reply_enabled" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-6" style="">
                <label><i class="fa fa-comments"></i> <?php echo $this->lang->line("do you want to like on comment by page?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" name="pageresponse_edit_auto_like_comment" value="yes" id="pageresponse_edit_auto_like_comment" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <!-- comment hide and delete section -->
          <div class="col-12" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
            <div class="row">
              <div class="col-12 col-md-6" style="">
                <label><i class="fa fa-eye-slash"></i> <?php echo $this->lang->line("do you want to hide comments after comment reply?") ?></label>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" name="pageresponse_edit_hide_comment_after_comment_reply" value="yes" id="pageresponse_edit_hide_comment_after_comment_reply" class="custom-switch-input">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="smallspace clearfix"></div>
          <!-- comment hide and delete section -->
          <div class="col-12">
            <div class="custom-control custom-radio">
            <input type="radio" name="pageresponse_edit_message_type" value="generic" id="pageresponse_edit_generic" class="custom-control-input radio_button">
            <label class="custom-control-label" for="pageresponse_edit_generic"><?php echo $this->lang->line("generic message for all") ?></label>
            </div>
            <div class="custom-control custom-radio">
            <input type="radio" name="pageresponse_edit_message_type" value="filter" id="pageresponse_edit_filter" class="custom-control-input radio_button">
            <label class="custom-control-label" for="pageresponse_edit_filter"><?php echo $this->lang->line("send message by filtering word/sentence") ?></label>
            </div>
          </div>
          <div class="col-12" style="margin-top: 15px;">
            <div class="form-group">
              <label>
                <i class="fa fa-monument"></i> <?php echo $this->lang->line("auto reply campaign name") ?> <span class="red">*</span>
              </label>
              <input class="form-control" type="text" name="pageresponse_edit_auto_campaign_name" id="pageresponse_edit_auto_campaign_name" placeholder="<?php echo $this->lang->line("write your auto reply campaign name here") ?>">
            </div>
          </div>
          <div class="col-12" id="pageresponse_edit_generic_message_div" style="display: none;">
            <div class="form-group clearfix" style="border: 1px dashed #e4e6fc; padding: 20px;">
              <label>
                <i class="fa fa-envelope"></i> <?php echo $this->lang->line("message for comment reply") ?> <span class="red">*</span>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
              </label>
              <?php if($comment_tag_machine_addon) {?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
              </span>
              <?php } ?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
              </span>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
              </span> 
      			  <div class="clearfix"></div>
              <textarea class="form-control message" name="pageresponse_edit_generic_message" id="pageresponse_edit_generic_message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"></textarea>
              
              <!-- comment hide and delete scetion -->
              <br/>
              <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                <div class="row">
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                    </label>                  
                    <div class="form-group">      
                        <div id="pageresponse_edit_generic_comment_image"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="pageresponse_edit_generic_image_preview_id"></div>
                    <span class="red" id="pageresponse_generic_image_for_comment_reply_error"></span>
                    <input type="text" name="pageresponse_edit_generic_image_for_comment_reply" class="form-control" id="pageresponse_edit_generic_image_for_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                    <div class="overlay_wrapper">
                      <span></span>
                      <img src="" alt="image" id="pageresponse_edit_generic_image_for_comment_reply_display" height="240" width="100%" style="display:none;" />
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Preferred");?>]
                      <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("Image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                    </label>
                    <div class="form-group">      
                                  <div id="pageresponse_edit_generic_video_upload"><?php echo $this->lang->line("upload") ?></div>      
                    </div>
                    <div id="pageresponse_edit_generic_video_preview_id"></div>
                    <span class="red" id="pageresponse_edit_generic_video_comment_reply_error"></span>
                    <input type="hidden" name="pageresponse_edit_generic_video_comment_reply" class="form-control" id="pageresponse_edit_generic_video_comment_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>" />
                    <div class="overlay_wrapper">
                      <span></span>
                      <video width="100%" height="240" controls style="border:1px solid #ccc;display:none;">
                        <source src="" id="pageresponse_edit_generic_video_comment_reply_display" type="video/mp4">
                      <?php echo $this->lang->line("your browser does not support the video tag.") ?>
                      </video>
                    </div>
                  </div>
                </div>
              </div>
              <br/><br/>
              <!-- comment hide and delete scetion -->

              <label>
                <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
              </label>
              <div>                      
                <select class="form-group private_reply_postback" id="pageresponse_edit_generic_message_private" name="pageresponse_edit_generic_message_private">
                  <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                </select>

                <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

              </div>
              
            </div>
          </div>
          <div class="col-12" id="pageresponse_edit_filter_message_div" style="display: none;">
            <div class="row">
              <div class="col-12 col-md-6">
              <label class="custom-switch">
                <input type="radio" name="pageresponse_edit_trigger_matching_type" value="exact" id="pageresponse_edit_trigger_keyword_exact" class="custom-switch-input" checked>
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"><?php echo $this->lang->line('Reply if the filter word exactly matches.'); ?></span>
              </label>
              </div>
              <div class="col-12 col-md-6">
              <label class="custom-switch">
                <input type="radio" name="pageresponse_edit_trigger_matching_type" value="string" id="pageresponse_edit_trigger_keyword_string" class="custom-switch-input">
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"><?php echo $this->lang->line('Reply if any matches occurs with filter word.'); ?>
              </label>
              </div>
            </div><br/>
          <?php for($i=1;$i<=20;$i++) :?>
            <div class="form-group clearfix" id="pageresponse_edit_filter_div_<?php echo $i; ?>" style="border: 1px dashed #e4e6fc; padding: 20px; margin-bottom: 50px;">
              <label>
                <i class="fa fa-tag"></i> <?php echo $this->lang->line("filter word/sentence") ?> <span class="red">*</span>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the word or sentence for which you want to filter comment. For multiple filter keyword write comma separated. Example -   why, want to know, when") ?>"><i class='fa fa-info-circle'></i> </a>
              </label>
              <input class="form-control filter_word" type="text" name="pageresponse_edit_filter_word_<?php echo $i; ?>" id="pageresponse_edit_filter_word_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("write your filter word here") ?>">
              <br/>

             
              <br/>
              <label>
                <i class="fa fa-envelope"></i> <?php echo $this->lang->line("msg for comment reply") ?><span class="red">*</span>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send based on filter words. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
              </label>
              <?php if($comment_tag_machine_addon) {?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
              </span>
              <?php } ?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
              </span>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
              </span> 
      			  <div class="clearfix"></div>
              <textarea class="form-control message" name="pageresponse_edit_comment_reply_msg_<?php echo $i; ?>" id="pageresponse_edit_comment_reply_msg_<?php echo $i; ?>"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"></textarea>
             
              <!-- comment hide and delete section -->
              <br/>
              <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                <div class="row">
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                    </label>                  
                    <div class="form-group">      
                                  <div id="pageresponse_edit_filter_image_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="pageresponse_edit_generic_image_preview_id_<?php echo $i; ?>"></div>
                    <span class="red" id="pageresponse_edit_generic_image_for_comment_reply_error_<?php echo $i; ?>"></span>
                    <input type="text" name="pageresponse_edit_filter_image_upload_reply_<?php echo $i; ?>" class="form-control" id="pageresponse_edit_filter_image_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                    <div class="overlay_wrapper">
                      <span></span>
                      <img src="" alt="image" id="pageresponse_edit_filter_image_upload_reply_display_<?php echo $i; ?>" height="240" width="100%" style="display:none;" />
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Preferred");?>]
                      <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("Image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                    </label>
                    <div class="form-group">      
                                  <div id="pageresponse_edit_filter_video_upload_<?php echo $i; ?>"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="pageresponse_edit_generic_video_preview_id_<?php echo $i; ?>"></div>
                    <span class="red" id="pageresponse_edit_generic_video_comment_reply_error_<?php echo $i; ?>"></span>
                    <input type="hidden" name="pageresponse_edit_filter_video_upload_reply_<?php echo $i; ?>" class="form-control" id="pageresponse_edit_filter_video_upload_reply_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>"  />
                    <div class="overlay_wrapper">
                      <span></span>
                      <video width="100%" height="240" controls style="border:1px solid #ccc;display:none;margin-top:20px;">
                        <source src="" id="pageresponse_edit_filter_video_upload_reply_display<?php echo $i; ?>" type="video/mp4">
                      <?php echo $this->lang->line("your browser does not support the video tag.") ?>
                      </video>
                    </div>
                  </div>
                </div>
              </div>
              <!-- comment hide and delete section -->

              <br/>

              <label>
                <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
              </label>
              <div>                      
                <select class="form-group private_reply_postback" id="pageresponse_edit_filter_message_<?php echo $i; ?>" name="pageresponse_edit_filter_message_<?php echo $i; ?>">
                  <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                </select>

                <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

              </div>
              
            </div>
          <?php endfor; ?>
            
            <div class="clearfix">
              <input type="hidden" name="pageresponse_edit_content_counter" id="pageresponse_edit_content_counter" />
              <button type="button" class="btn btn-sm btn-outline-primary float-right" id="pageresponse_edit_add_more_button"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add more filtering") ?></button>
            </div>
            <div class="form-group clearfix" id="pageresponse_edit_nofilter_word_found_div" style="margin-top: 10px; border: 1px dashed #e4e6fc; padding: 20px;">
              <label>
                <i class="fa fa-envelope"></i> <?php echo $this->lang->line("comment reply if no matching found") ?>
                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Write the message,  if no filter word found. If you don't want to send message them, just keep it blank ."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
              </label>
              <?php if($comment_tag_machine_addon) {?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i>  <?php echo $this->lang->line("tag user") ?></a>
              </span>
              <?php } ?>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
              </span>
              <span class='float-right'> 
                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
              </span> 
      			  <div class="clearfix"></div>
              <textarea class="form-control message" name="pageresponse_edit_nofilter_word_found_text" id="pageresponse_edit_nofilter_word_found_text"  placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"></textarea>
            
              
              <!-- comment hide and delete section -->
              <br/>
              <div class="clearfix" <?php if(!$commnet_hide_delete_addon) echo "style='display: none;'"; ?> >
                <div class="row">
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-image"></i> <?php echo $this->lang->line("image for comment reply") ?>
                    </label>                  
                    <div class="form-group">      
                                  <div id="pageresponse_edit_nofilter_image_upload"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="pageresponse_edit_nofilter_generic_image_preview_id"></div>
                    <span class="red" id="pageresponse_edit_nofilter_image_upload_reply_error"></span>
                    <input type="text" name="pageresponse_edit_nofilter_image_upload_reply" class="form-control" id="pageresponse_edit_nofilter_image_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click the above upload button") ?>" style="margin-top: -14px;" />
                    <div class="overlay_wrapper">
                      <span></span>
                      <img src="" alt="image" id="pageresponse_edit_nofilter_image_upload_reply_display" height="240" width="100%" style="display:none;" />
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="control-label" ><i class="fa fa-youtube"></i> <?php echo $this->lang->line("video for comment reply") ?> [mp4 <?php echo $this->lang->line("Preferred");?>]
                      <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("video upload") ?>" data-content="<?php echo $this->lang->line("image and video will not work together. Please choose either image or video.") ?> [mp4,wmv,flv]"><i class='fa fa-info-circle'></i></a>
                    </label>
                    <div class="form-group">      
                        <div id="pageresponse_edit_nofilter_video_upload"><?php echo $this->lang->line("upload") ?></div>       
                    </div>
                    <div id="edit_nofilter_video_preview_id"></div>
                    <span class="red" id="pageresponse_edit_nofilter_video_upload_reply_error"></span>
                    <input type="hidden" name="pageresponse_edit_nofilter_video_upload_reply" class="form-control" id="pageresponse_edit_nofilter_video_upload_reply" placeholder="<?php echo $this->lang->line("put your image url here or click upload") ?>" />
                    <div class="overlay_wrapper">
                      <span></span>
                      <video width="100%" height="240" controls style="border:1px solid #ccc;display:none;">
                        <source src="" id="pageresponse_edit_nofilter_video_upload_reply_display" type="video/mp4">
                      <?php echo $this->lang->line("your browser does not support the video tag.") ?>
                      </video>
                    </div>
                  </div>
                </div>
              </div>
              <br/><br/>
              <!-- comment hide and delete section -->

              <label>
                <i class="fas fa-envelope"></i> <?php echo $this->lang->line("Select a message template for private reply if no matching found") ?> [<?php echo $this->lang->line('Maximum two reply message is supported.'); ?>]
              </label>
              <div>                      
                <select class="form-group private_reply_postback" id="pageresponse_edit_nofilter_word_found_text_private" name="pageresponse_edit_nofilter_word_found_text_private">
                  <option><?php echo $this->lang->line('Please select a page first to see the message templates.'); ?></option>
                </select>

                <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add Message Template");?></a>
                <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh List");?></a>

              </div>

              
            </div>

          </div>
        </div>
        <div class="col-12 text-center" id="pageresponse_edit_response_status"></div>
            </div>
            </form>

            <div class="modal-footer" style="padding-left: 45px; padding-right: 45px; ">
              <div class="row">
                <div class="col-6">
                  <button class="btn btn-lg btn-primary float-left" id="pageresponse_edit_save_button"><i class="fa fa-save"></i> <?php echo $this->lang->line("save") ?></button>
                </div>  
                <div class="col-6">
                  <button class="btn btn-lg btn-secondary float-right cancel_button"><i class='fas fa-times'></i> <?php echo $this->lang->line("cancel") ?></button>
                </div>
              </div>
            </div>

        </div>
    </div>
</div>
 
<script>
  $("document").ready(function(){

    $("#filemanager_close").click(function(){
      $("#modal-live-video-library").removeClass('modal');
    });
    var base_url="<?php echo site_url(); ?>";
    var user_id = "<?php echo $this->session->userdata('user_id'); ?>";

    var image_upload_limit = "<?php echo $image_upload_limit; ?>";
    var video_upload_limit = "<?php echo $video_upload_limit; ?>";

    <?php for($k=1;$k<=20;$k++) : ?>
      $("#pageresponse_edit_filter_video_upload_<?php echo $k; ?>").uploadFile({
        url:base_url+"comment_automation/upload_live_video",
        fileName:"myfile",
        maxFileSize:video_upload_limit*1024*1024,
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
        deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
          $.post(delete_url, {op: "delete",name: data},
            function (resp,textStatus, jqXHR) {  
                $("#pageresponse_edit_filter_video_upload_reply_<?php echo $k; ?>").val('');              
            });
        },
        onSuccess:function(files,data,xhr,pd)
        {
          var file_path = base_url+"upload/video/"+data;
          $("#pageresponse_edit_filter_video_upload_reply_<?php echo $k; ?>").val(file_path);  
        }
      });

      $("#pageresponse_edit_filter_image_upload_<?php echo $k; ?>").uploadFile({
          url:base_url+"comment_automation/upload_image_only",
          fileName:"myfile",
          maxFileSize:image_upload_limit*1024*1024,
          showPreview:false,
          returnType: "json",
          dragDrop: true,
          showDelete: true,
          multiple:false,
          maxFileCount:1, 
          acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
          deleteCallback: function (data, pd) {
            var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
            $.post(delete_url, {op: "delete",name: data}, function (resp,textStatus, jqXHR) {
              $("#pageresponse_edit_filter_image_upload_reply_<?php echo $k; ?>").val('');                      
            });
             
          },
          onSuccess:function(files,data,xhr,pd)
          {
            var data_modified = base_url+"upload/image/"+user_id+"/"+data;
            $("#pageresponse_edit_filter_image_upload_reply_<?php echo $k; ?>").val(data_modified); 
          }
      });
    <?php endfor; ?>
    <?php for($k=1;$k<=20;$k++) : ?>
      $("#pageresponse_filter_video_upload_<?php echo $k; ?>").uploadFile({
        url:base_url+"comment_automation/upload_live_video",
        fileName:"myfile",
        maxFileSize:video_upload_limit*1024*1024,
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
        deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
          $.post(delete_url, {op: "delete",name: data},
            function (resp,textStatus, jqXHR) {  
                $("#pageresponse_filter_video_upload_reply_<?php echo $k; ?>").val('');              
            });
        },
        onSuccess:function(files,data,xhr,pd)
        {
          var file_path = base_url+"upload/video/"+data;
          $("#pageresponse_filter_video_upload_reply_<?php echo $k; ?>").val(file_path); 
        }
      });

      $("#pageresponse_filter_image_upload_<?php echo $k; ?>").uploadFile({
        url:base_url+"comment_automation/upload_image_only",
        fileName:"myfile",
        maxFileSize:image_upload_limit*1024*1024,
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
        deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#pageresponse_filter_image_upload_reply_<?php echo $k; ?>").val('');                      
              });
           
        },
        onSuccess:function(files,data,xhr,pd)
        {
          var data_modified = base_url+"upload/image/"+user_id+"/"+data;
          $("#pageresponse_filter_image_upload_reply_<?php echo $k; ?>").val(data_modified);  
        }
      });
    <?php endfor; ?>
 
    $("#pageresponse_generic_video_upload").uploadFile({
      url:base_url+"comment_automation/upload_live_video",
      fileName:"myfile",
      maxFileSize:video_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#pageresponse_generic_video_comment_reply").val('');              
          });
      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#pageresponse_generic_video_comment_reply").val(file_path); 
      }
    });

    $("#pageresponse_generic_comment_image").uploadFile({
      url:base_url+"comment_automation/upload_image_only",
      fileName:"myfile",
      maxFileSize:image_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
      deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#pageresponse_generic_image_for_comment_reply").val('');                      
              });
         
      },
      onSuccess:function(files,data,xhr,pd)
      {
        var data_modified = base_url+"upload/image/"+user_id+"/"+data;
        $("#pageresponse_generic_image_for_comment_reply").val(data_modified);    
      }
    });

    $("#pageresponse_nofilter_video_upload").uploadFile({
      url:base_url+"comment_automation/upload_live_video",
      fileName:"myfile",
      maxFileSize:video_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#pageresponse_nofilter_video_upload_reply").val('');              
          });
      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#pageresponse_nofilter_video_upload_reply").val(file_path); 
      }
    });

    $("#pageresponse_nofilter_image_upload").uploadFile({
      url:base_url+"comment_automation/upload_image_only",
      fileName:"myfile",
      maxFileSize:image_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
      deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#pageresponse_nofilter_image_upload_reply").val('');                      
              });
         
      },
      onSuccess:function(files,data,xhr,pd)
      {
        var data_modified = base_url+"upload/image/"+user_id+"/"+data;
        $("#pageresponse_nofilter_image_upload_reply").val(data_modified);    
      }
    });

    $("#pageresponse_edit_generic_video_upload").uploadFile({
      url:base_url+"comment_automation/upload_live_video",
      fileName:"myfile",
      maxFileSize:video_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#pageresponse_edit_generic_video_comment_reply").val('');              
          });
      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#pageresponse_edit_generic_video_comment_reply").val(file_path);  
      }
    });

    $("#pageresponse_edit_generic_comment_image").uploadFile({
      url:base_url+"comment_automation/upload_image_only",
      fileName:"myfile",
      maxFileSize:image_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
      deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#pageresponse_edit_generic_image_for_comment_reply").val('');                      
              });
         
      },
      onSuccess:function(files,data,xhr,pd)
      {
        var data_modified = base_url+"upload/image/"+user_id+"/"+data;
        $("#pageresponse_edit_generic_image_for_comment_reply").val(data_modified);   
      }
    });

    $("#pageresponse_edit_nofilter_video_upload").uploadFile({
      url:base_url+"comment_automation/upload_live_video",
      fileName:"myfile",
      maxFileSize:video_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('comment_automation/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#pageresponse_edit_nofilter_video_upload_reply").val('');              
          });
      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#pageresponse_edit_nofilter_video_upload_reply").val(file_path);  
      }
    });

    $("#pageresponse_edit_nofilter_image_upload").uploadFile({
      url:base_url+"comment_automation/upload_image_only",
      fileName:"myfile",
      maxFileSize:image_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
      deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('comment_automation/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#pageresponse_edit_nofilter_image_upload_reply").val('');                      
              });
         
      },
      onSuccess:function(files,data,xhr,pd)
      {
        var data_modified = base_url+"upload/image/"+user_id+"/"+data;
        $("#pageresponse_edit_nofilter_image_upload_reply").val(data_modified);   
      }
    });


  });
</script>