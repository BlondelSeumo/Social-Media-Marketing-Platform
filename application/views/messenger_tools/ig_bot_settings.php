<?php
  $redirect_url = site_url('messenger_bot/ig_bot_settings/').$page_info['id'].'/1/'.$media_type;
  $this->load->view("include/upload_js");

  $image_upload_limit = 1; 
  if($this->config->item('messengerbot_image_upload_limit') != '')
  $image_upload_limit = $this->config->item('messengerbot_image_upload_limit'); 

  $video_upload_limit = 5; 
  if($this->config->item('messengerbot_video_upload_limit') != '')
  $video_upload_limit = $this->config->item('messengerbot_video_upload_limit');

  $audio_upload_limit = 3; 
  if($this->config->item('messengerbot_audio_upload_limit') != '')
  $audio_upload_limit = $this->config->item('messengerbot_audio_upload_limit');

  $file_upload_limit = 2; 
  if($this->config->item('messengerbot_file_upload_limit') != '')
  $file_upload_limit = $this->config->item('messengerbot_file_upload_limit');
  
  $hide_generic_item = '';
  if($media_type =='ig') {
    $hide_generic_item = 'class="hidden"';
  }

?>

<style type="text/css">
  .card {border:1px solid #f9f9f9;}
  .card .card-header {
    line-height: 30px;
    min-height: 0px; 
    padding: 5px 25px;
  }
  .card .card-header h4{
    font-size: 13px;
  }
  .card .card-body {
    padding-top: 0px; 
  }

  .remove_reply
  {
    margin-right: -20px;
    margin-top: 5px;
  }
  .remove_carousel_template 
  {
    margin-right: -20px;
    margin-top: 5px;
  }
  .button_border {
    padding: 5px 15px 0px 15px !important;
    margin: 5px 0px 0px; 
    border: 1px dashed #ccc;
  }
  .item_remove
  {
    margin-top: 12px; 
    margin-left: -20px;
    font-size: 20px !important;
    cursor: pointer !important;
    font-weight: 200 !important;
  }
  .add_template,.ref_template{font-size: 10px;}
  .emojionearea.form-control{padding-top:12px !important;}
  .img_holder div:not(:first-child){display: none;position:fixed;bottom:187px;right:40px;}
  .img_holder div:first-child{position:fixed;bottom:187px;right:40px;}
  .lead_first_name,.lead_last_name{background: #EEE;border-radius: 0;}
  .input-group-addon{
    border-radius: 0;
    font-weight: bold;
    /* color: orange;   */
    /*border: 1px solid #607D8B !important;*/
    border: none;
    background: none;
  }
  
  input[type=radio].css-checkbox {
  position:absolute; z-index:-1000; left:-1000px; overflow: hidden; clip: rect(0 0 0 0); height:1px; width:1px; margin:-1px; padding:0; border:0;
  }

  input[type=radio].css-checkbox + label.css-label {
    padding-left:24px;
    height:19px; 
    display:inline-block;
    line-height:19px;
    background-repeat:no-repeat;
    background-position: 0 0;
    font-size:19px;
    vertical-align:middle;
    cursor:pointer;

  }

  input[type=radio].css-checkbox:checked + label.css-label {
    background-position: 0 -19px;
  }
  label.css-label {
    background-image:url(<?php echo base_url('assets/images/csscheckbox.png'); ?>);
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    color: <?php echo $THEMECOLORCODE; ?> !important;
    font-size: 15px !important;
  }
  .css-label-container{padding:10px;border:1px dashed <?php echo $THEMECOLORCODE; ?>;border-radius: 5px;}
  .img_holder img{
    border: 1px solid #ccc;
  }

  .emojionearea.form-control, .emojionearea {
    min-height: 95px !important;
  }

  .emojionearea .emojionearea-editor {
    min-height: 80px !important;
    max-height: 80px !important;
  }

  .button-outline
  {
    background: #fff;
    border: .5px dashed #ccc;
  }
  .button-outline:hover
  {
    border: 1px dashed var(--blue) !important;
    cursor: pointer;
  }

 /*  #add_bot_settings_modal .modal-body, #add_bot_settings_modal .modal-header
  {
    padding: 1px;
  } */
  .load_preview_modal
  {
    cursor: pointer;
  }
</style>

<div class="hidden" id="add_bot_settings_modal">
  <div class="modal-dialog" style="max-width:100%;margin:0px;">
    <div class="modal-content no_shadow">
      <?php if($iframe!='1') : ?>
      <div class="modal-header">
        <h5 class="modal-title"><i class='fa fa-plus-circle'></i> <?php echo $this->lang->line("Add Bot Reply Setting");?> : <a target='_BLANK' href='https://facebook.com/<?php echo $page_info['page_id'];?>'><?php echo $page_info['page_name'];?></a></h5>
      </div>
      <?php endif; ?>
      <div class="modal-body <?php if($iframe=='1') echo 'padding-0 no_shadow';?>"> 

        <div class="row">
          <div class="<?php if($iframe=="1") echo 'col-12'; else echo 'col-12 col-md-9';?>">
            <form action="#" method="post" id="messenger_bot_form" style="padding-left: 0;">
              <input type="hidden" name="media_type" id="media_type" value="<?php echo $media_type; ?>">
              <input type="hidden" name="page_id" id="page_id" value="<?php echo  $page_info['page_id'];?>">
              <input type="hidden" name="page_table_id" id="page_table_id" value="<?php echo  $page_info['id'];?>">
              <input type="hidden" name="keyword_type" id="keyword_type" value="reply">
              
              <div class="row"> 
                <div class="col-12 col-sm-6"> 
                  <div class="form-group">
                    <label><?php echo $this->lang->line("Bot Name"); ?></label>
                    <input type="text" name="bot_name" id="bot_name" class="form-control">
                  </div>       
                </div> 
                <div class="col-12 col-sm-6">              
                  <div class="form-group">
                    <label><?php echo $this->lang->line("Please provide your keywords in comma separated"); ?></label>
                    <input class="form-control"  name="keywords_list" id="keywords_list">
                  </div>        
                </div> 
              </div>
             
      
              <div class="row" id="postback_div" style="display: none;"> 
                <div class="col-12">              
                  <div class="form-group">
                    <label><?php echo $this->lang->line("Please select your postback id"); ?></label>
                    <select class="form-control" id="keywordtype_postback_id" name="keywordtype_postback_id[]">
                    <?php
                        $postback_id_array = array();
                        foreach($postback_ids as $value)
                        {                            
                          $postback_id_array[] = strtoupper($value['postback_id']);

                          if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply" || $value["is_template"] == "1") continue;

                          if($value['use_status'] == '0'){
                            $array_key = $value['postback_id'];
                            $array_value = $value['postback_id']." (".$value['bot_name'].")";
                            echo "<option value='{$array_key}'>{$array_value}</option>";
                          }                         
                        }
                    ?>                      
                    </select>
                  </div>        
                </div>  
              </div>   
      
              <div id="main_reply_sort">

              <?php for($k=1;$k<=6;$k++){ ?>

                  <div class="card card-primary" id="multiple_template_div_<?php echo $k; ?>" <?php if($k != 1) echo "style='display : none;'"; ?> >
                    <div class="card-header">
                      <h4 class="full_width"><?php echo $this->lang->line('Reply')." ".$k; ?> 
                      
                      <?php if($k != 1) : ?>
                      <i class="fa fa-times-circle remove_reply float-right red" row_id="multiple_template_div_<?php echo $k; ?>" counter_variable="" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                      <?php endif; ?>
                    </h4>


                    </div>
                    <div class="card-body">

                      <div style="">
                        <!-- <br/> -->
                        <div class="input-group mb-3">                            
                          <div class="input-group-prepend">
                            <div class="input-group-text" style="font-weight: bold;">
                              <?php echo $this->lang->line("Select Reply Type") ?>
                            </div>
                          </div>
                          <select class="form-control form-control-new" id="template_type_<?php echo $k; ?>" name="template_type_<?php echo $k; ?>">
                            <?php 
                             foreach ($templates as $key => $value)
                             {
                                echo '<option value="'.$value.'">'.$this->lang->line($value).'</option>';
                             } 
                            ?>
                          </select>
                        </div> 
                        <!-- <br/> -->

                        <div class="row hidden" id="delay_and_typing_on_<?php echo $k; ?>">
                          <div class="col-12 col-sm-6">
                            <div class="row">
                              <div class="col-6"><label for="" style="margin-top: 8px; color: #34395e; font-size: 14px;"><?php echo $this->lang->line('Typing on display :'); ?></label></div>
                              <div class="col-6">
                                <label class="custom-switch mt-2 float-left">
                                  <input type="checkbox" name="typing_on_enable_<?php echo $k; ?>" id="typing_on_enable_<?php echo $k; ?>" class="custom-switch-input">
                                  <span class="custom-switch-indicator"></span>
                                  <span class="custom-switch-description"><?php echo $this->lang->line('Enable'); ?></span>
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 col-sm-6">
                            <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                <span class="input-group-text"><?php echo $this->lang->line('Delay in reply'); ?></span>
                              </div>
                              <input type="number" min="0" value="0" name="delay_in_reply_<?php echo $k; ?>" id="delay_in_reply_<?php echo $k; ?>" class="form-control">
                              <div class="input-group-append">
                                <span class="input-group-text"><?php echo $this->lang->line('Sec'); ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- <br/> -->

                        <div class="row" id="Ecommerce_div_<?php echo $k; ?>" style="display: none;">
                          <div class="col-12">              
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Select your Ecommerce store"); ?>
                              </label>
                              <select class="form-control select2 ecommerce_store_info" product_dropdown_id="ecommerce_product_ids<?php echo $k; ?>" id="ecommerce_store_id<?php echo $k; ?>" name="ecommerce_store_id<?php echo $k; ?>">
                                <?php echo $ecommerce_stores; ?>
                              </select>
                            </div>        
                          </div> 

                          <div class="col-12">              
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please select products for carousel/generic reply"); ?>
                              </label>
                              <select class="form-control select2 ecommerce_product_info" multiple="multiple" id="ecommerce_product_ids<?php echo $k; ?>" name="ecommerce_product_ids<?php echo $k; ?>[]">
                                <option value=""><?php echo $this->lang->line('Please select a store first.'); ?></option>
                              </select>
                            </div>        
                          </div> 

                          <div class="col-12"> 
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please provide 'Buy Now' button text"); ?></label>
                              <input type="text" name="ecommerce_button_text<?php echo $k; ?>" id="ecommerce_button_text<?php echo $k; ?>" class="form-control">
                            </div>       
                          </div>

                        </div>

                        <div class="row" id="User_Input_Flow_div_<?php echo $k; ?>" style="display: none;">
                          <div class="col-12">              
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please select a Flow Campaign"); ?>
                              </label>
                              <select class="form-control select2 flow_campaign_info" id="flow_campaign_id_<?php echo $k; ?>" name="flow_campaign_id_<?php echo $k; ?>">
                                <option value=""><?php echo $this->lang->line('Please select a Flow campaign.'); ?></option>
                                <?php foreach($flow_campaigns as $value) : ?>
                                  <option value="<?php echo $value['id']; ?>"><?php echo $value['flow_name']; ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>        
                          </div> 
                        </div>

                        <div class="row" id="One_Time_Notification_div_<?php echo $k; ?>" style="display: none;"> 
                          <div class="col-12 col-md-6">              
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Title"); ?>
                              </label>
                              <input class="form-control" type="text" name="otn_title_<?php echo $k; ?>" id="otn_title_<?php echo $k; ?>">
                            </div>        
                          </div> 
                          <div class="col-12 col-md-6">              
                            <div class="form-group">
                              <label><?php echo $this->lang->line("OTN Postback"); ?>
                              </label>
                              <?php 
                                $name_id = "otn_postback_".$k;
                                echo form_dropdown($name_id,$otn_postback_list,'','id="'.$name_id.'" class="form-control push_otn_postback select2"'); 
                              ?>
                            </div>        
                          </div> 
                        </div>

                        <div class="row" id="text_div_<?php echo $k; ?>"> 
                          <div class="col-12">              
                            <div class="form-group clearfix">
                              <label><?php echo $this->lang->line("Please provide your reply message"); ?>
                                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Spintax"); ?>" data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                              </label>

                              <?php if($media_type == 'fb') : ?>
                              <span class='float-right'> 
                                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                              </span>
                              <span class='float-right'> 
                                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                              </span> 
                              <?php else : ?>
                              <span class='float-right hidden'>
                                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-placement="top" data-toggle="tooltip"
                                 class='btn btn-default btn-sm full_lead_tag_name button-outline'><i class='fa fa-tags'></i> <?php echo $this->lang->line("mention user") ?></a>
                              </span>

                              <span class='float-right'>
                                <a title="<?php echo $this->lang->line("You can include #LEAD_FULL_NAME# variable inside your message. The variable will be replaced by real Full Name when we will send it.") ?>" data-placement="top" data-toggle="tooltip" class='btn btn-default btn-sm full_lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("Full Name") ?></a>
                              </span>
                              <?php endif; ?> 

                              <div class="clearfix"></div>
                              <textarea class="form-control"  name="text_reply_<?php echo $k; ?>" id="text_reply_<?php echo $k; ?>"></textarea>
                            </div>        
                          </div>  
                        </div>

                        <div class="row" id="image_div_<?php echo $k; ?>" style="display: none;">             
                          <div class="col-12">              
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please provide your reply image"); ?></label>
                              <input type="text" placeholder="<?php echo $this->lang->line('Put your image URL here or click the upload button.'); ?>" class="form-control"  name="image_reply_field_<?php echo $k; ?>" id="image_reply_field_<?php echo $k; ?>">
                              <div id="image_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div>
                              <img id="image_reply_div_<?php echo $k; ?>" style="display: none;" height="200px;" width="400px;">
                            </div>       
                          </div>             
                        </div>

                        <div class="row" id="audio_div_<?php echo $k; ?>" style="display: none;">  
                          <div class="col-12">             
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please provide your reply audio"); ?></label>
                              <input type="hidden" class="form-control"  name="audio_reply_field_<?php echo $k; ?>" id="audio_reply_field_<?php echo $k; ?>">
                              <div id="audio_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div>                      
                              <audio controls id="audio_tag_<?php echo $k; ?>" style="display: none;">
                                <source src="" id="audio_reply_div_<?php echo $k; ?>" type="audio/mpeg">
                              Your browser does not support the video tag.
                              </audio>
                            </div>           
                          </div>
                        </div>

                        <div class="row" id="video_div_<?php echo $k; ?>" style="display: none;">  
                          <div class="col-12">             
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please provide your reply video"); ?></label>
                              <input type="hidden" class="form-control"  name="video_reply_field_<?php echo $k; ?>" id="video_reply_field_<?php echo $k; ?>">
                              <div id="video_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div>                      
                              <video width="400" height="200" controls id="video_tag_<?php echo $k; ?>" style="display: none;">
                                <source src="" id="video_reply_div_<?php echo $k; ?>" type="video/mp4">
                              Your browser does not support the video tag.
                              </video>
                            </div>           
                          </div>
                        </div>

                        <div class="row" id="file_div_<?php echo $k; ?>" style="display: none;">  
                          <div class="col-12">             
                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please provide your reply file"); ?></label>
                              <input type="hidden" class="form-control"  name="file_reply_field_<?php echo $k; ?>" id="file_reply_field_<?php echo $k; ?>">
                              <div id="file_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div> 
                            </div>           
                          </div>
                        </div>


                        <div class="row" id="media_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                          <div class="col-12"> 

                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please provide your media URL"); ?>
                                <a href="#" class="media_template_modal" title="<?php echo $this->lang->line("How to get media URL?"); ?>"><i class='fa fa-info-circle'></i> </a>
                              </label>
                  
                              <div class="clearfix"></div>
                              <input class="form-control"  name="media_input_<?php echo $k; ?>" id="media_input_<?php echo $k; ?>" />
                            </div> 


                            <!--   This hidden input is added by Konok to keep sorted order  -->
                            <div id="media_postback_sort_<?php echo $k; ?>">


                            <?php for ($i=1; $i <=3 ; $i++) : ?>
                            <div class="row button_border" id="media_row_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                              <div class="col-12 col-sm-4">
                                <div class="form-group">
                                  <label><?php echo $this->lang->line("button text"); ?></label>
                                  <input type="text" class="form-control"  name="media_text_<?php echo $i; ?>_<?php echo $k; ?>" id="media_text_<?php echo $i; ?>_<?php echo $k; ?>">
                                </div>
                              </div>
                              <div class="col-12 col-sm-4">
                                <div class="form-group">
                                  <label><?php echo $this->lang->line("button type"); ?></label>
                                  <select class="form-control media_type_class" id="media_type_<?php echo $i; ?>_<?php echo $k; ?>" name="media_type_<?php echo $i; ?>_<?php echo $k; ?>">
                                    <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                                    <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                                    <option value="web_url"><?php echo $this->lang->line("Web Url"); ?></option>
                                    <option value="phone_number"><?php echo $this->lang->line("Call Us"); ?></option>

                                    <option value="web_url_compact"><?php echo $this->lang->line("WebView [Compact]"); ?></option>
                                    <option value="web_url_tall"><?php echo $this->lang->line("WebView [Tall]"); ?></option>
                                    <option value="web_url_full"><?php echo $this->lang->line("WebView [Full]"); ?></option>
                                    <option value="web_url_birthday"><?php echo $this->lang->line("User's Birthday"); ?></option>

                                    <option value="web_url_email"><?php echo $this->lang->line("User's Email"); ?></option>
                                    <option value="web_url_phone"><?php echo $this->lang->line("User's Phone"); ?></option>
                                    <option value="web_url_location"><?php echo $this->lang->line("User's Location"); ?></option>

                                    <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("Unsubscribe"); ?></option>
                                    <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("Re-subscribe"); ?></option>
                                    
                                    <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                                    <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-10 col-sm-3">

                                <div class="form-group" id="media_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                  <label><?php echo $this->lang->line("PostBack id"); ?></label>
                                  <select class="form-control push_postback"  name="media_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="media_post_id_<?php echo $i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                                  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                                  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                </div>

                                <div class="form-group" id="media_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                  <label><?php echo $this->lang->line("Web Url"); ?></label>
                                  <input type="text" class="form-control"  name="media_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="media_web_url_<?php echo $i; ?>_<?php echo $k; ?>">
                                </div>
                                <div class="form-group" id="media_call_us_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                  <label><?php echo $this->lang->line("Phone Number"); ?></label>
                                  <input type="text" class="form-control"  name="media_call_us_<?php echo $i; ?>_<?php echo $k; ?>" id="media_call_us_<?php echo $i; ?>_<?php echo $k; ?>">
                                </div>
                              </div>

                              <?php if($i != 1) : ?>
                                <div class="col-2 col-sm-1" >
                                  <br/>
                                  <i class="fa fa-2x fa-times-circle red item_remove" template_type="not_quick_reply" row_id="media_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="media_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="media_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="media_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="media_web_url_<?php echo $i; ?>_<?php echo $k; ?>" third_callus="media_call_us_<?php echo $i; ?>_<?php echo $k; ?>" counter_variable="media_counter_<?php echo $k; ?>" add_more_button_id="media_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                                </div>
                              <?php endif; ?>

                            </div>
                            <?php endfor; ?>

                            </div>
                            <!--   This hidden input is added by Konok to keep sorted order  -->
                            <input type="hidden" name="media_postback_sort_order_<?php echo $k; ?>" id="media_postback_sort_order_<?php echo $k; ?>">




                            <div class="row clearfix">
                              <div class="col-12 text-center"><button class="btn btn-outline-primary float-right no_radius btn-xs" id="media_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                            </div>

                          </div> 
                        </div>



                        <div class="row" id="quick_reply_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                          <div class="col-12">  

                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please provide your reply message"); ?>
                                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Spintax"); ?>" data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                              </label>

                              <?php if($media_type == 'fb') : ?>
                              <span class='float-right'> 
                                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                              </span>
                              <span class='float-right'> 
                                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                              </span> 
                              <?php else : ?>
                              <span class='float-right hidden'>
                                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-placement="top" data-toggle="tooltip"
                                 class='btn btn-default btn-sm full_lead_tag_name button-outline'><i class='fa fa-tags'></i> <?php echo $this->lang->line("mention user") ?></a>
                              </span>

                              <span class='float-right'>
                                <a title="<?php echo $this->lang->line("You can include #LEAD_FULL_NAME# variable inside your message. The variable will be replaced by real Full Name when we will send it.") ?>" data-placement="top" data-toggle="tooltip" class='btn btn-default btn-sm full_lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("Full Name") ?></a>
                              </span>
                              <?php endif; ?>  

                              <div class="clearfix"></div>
                              <textarea class="form-control" name="quick_reply_text_<?php echo $k; ?>" id="quick_reply_text_<?php echo $k; ?>"></textarea>
                            </div> 


                            <!--   This hidden input is added by Konok to keep sorted order  -->
                            <div id="quick_reply_sort_<?php echo $k; ?>">

                            <?php for ($i=1; $i <=11 ; $i++) : ?>
                            <div class="row button_border" id="quick_reply_row_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                              <div class="col-12 col-sm-4">
                                <div class="form-group">
                                  <label><?php echo $this->lang->line("button text"); ?></label>
                                  <input type="text" class="form-control" name="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>" id="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>">
                                </div>
                              </div>
                              <!-- 28/02/2018 -->
                              <div class="col-12 col-sm-4">
                                <div class="form-group">
                                  <label><?php echo $this->lang->line("button type"); ?></label>
                                  <select class="form-control quick_reply_button_type_class" id="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                                    <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                                    <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                                    <?php if($media_type != 'ig') : ?>
                                    <option value="phone_number"><?php echo $this->lang->line("User Phone Number"); ?></option>
                                    <option value="user_email"><?php echo $this->lang->line("User E-mail Address"); ?></option>
                                    <?php endif; ?>
                                    <!-- <option value="location"><?php echo $this->lang->line("User's Location"); ?></option> -->
                                  </select>
                                </div>
                              </div>
                              <div class="col-10 col-sm-3">
                                 <div class="form-group" id="quick_reply_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                    <label><?php echo $this->lang->line("PostBack id"); ?></label>
                                    <select class="form-control push_postback"  name="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                                    <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                                    <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                  </div>
                              </div>

                              <?php if($i != 1) : ?>
                                <div class="col-2 col-sm-1">
                                  <br/>
                                  <i class="fa fa-2x fa-times-circle red item_remove" template_type="quick_reply" row_id="quick_reply_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="" third_callus="" counter_variable="quick_reply_button_counter_<?php echo $k; ?>" add_more_button_id="quick_reply_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                                </div>
                              <?php endif; ?>


                            </div>
                            <?php endfor; ?>

                            </div>  
                            <!--   This hidden input is added by Konok to keep sorted order  -->
                            <input type="hidden" name="quick_reply_sort_order_<?php echo $k; ?>" id="quick_reply_sort_order_<?php echo $k; ?>">


                            <div class="row clearfix">
                              <div class="col-12 text-center"><button class="btn btn-outline-primary float-right no_radius btn-xs" id="quick_reply_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                            </div>

                          </div> 
                        </div>


                        <div class="row" id="text_with_buttons_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                          <div class="col-12"> 

                            <div class="form-group">
                              <label><?php echo $this->lang->line("Please provide your reply message"); ?>
                                <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Spintax"); ?>" data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                              </label>

                              <?php if($media_type == 'fb') : ?>
                              <span class='float-right'> 
                                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                              </span>
                              <span class='float-right'> 
                                <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                              </span> 
                              <?php else : ?>
                              <span class='float-right hidden'>
                                <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-placement="top" data-toggle="tooltip"
                                 class='btn btn-default btn-sm full_lead_tag_name button-outline'><i class='fa fa-tags'></i> <?php echo $this->lang->line("mention user") ?></a>
                              </span>

                              <span class='float-right'>
                                <a title="<?php echo $this->lang->line("You can include #LEAD_FULL_NAME# variable inside your message. The variable will be replaced by real Full Name when we will send it.") ?>" data-placement="top" data-toggle="tooltip" class='btn btn-default btn-sm full_lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("Full Name") ?></a>
                              </span>
                              <?php endif; ?> 

                              <div class="clearfix"></div>
                              <textarea class="form-control"  name="text_with_buttons_input_<?php echo $k; ?>" id="text_with_buttons_input_<?php echo $k; ?>"></textarea>
                            </div> 


                            <!--   This hidden input is added by Konok to keep sorted order  -->
                            <div id="text_button_sort_<?php echo $k; ?>">

                            <?php for ($i=1; $i <=3 ; $i++) : ?>
                            <div class="row button_border" id="text_with_buttons_row_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                              <div class="col-12 col-sm-4">
                                <div class="form-group">
                                  <label><?php echo $this->lang->line("button text"); ?></label>
                                  <input type="text" class="form-control"  name="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>">
                                </div>
                              </div>
                              <div class="col-12 col-sm-4">
                                <div class="form-group">
                                  <label><?php echo $this->lang->line("button type"); ?></label>
                                  <select class="form-control text_with_button_type_class" id="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                                    <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                                    <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                                    <option value="web_url"><?php echo $this->lang->line("Web Url"); ?></option>

                                    <option value="web_url_compact"><?php echo $this->lang->line("WebView [Compact]"); ?></option>
                                    <option value="web_url_tall"><?php echo $this->lang->line("WebView [Tall]"); ?></option>
                                    <option value="web_url_full"><?php echo $this->lang->line("WebView [Full]"); ?></option>
                                    <option value="web_url_birthday"><?php echo $this->lang->line("User's Birthday"); ?></option>

                                    <option value="web_url_email"><?php echo $this->lang->line("User's Email"); ?></option>
                                    <option value="web_url_phone"><?php echo $this->lang->line("User's Phone"); ?></option>
                                    <option value="web_url_location"><?php echo $this->lang->line("User's Location"); ?></option>

                                    <option value="phone_number"><?php echo $this->lang->line("Call Us"); ?></option>
                                    
                                    <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("unsubscribe"); ?></option>
                                    <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("re-subscribe"); ?></option>
                                    
                                    <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                                    <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-10 col-sm-3">
                                <div class="form-group" id="text_with_button_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                  <label><?php echo $this->lang->line("PostBack id"); ?></label>
                                  <select class="form-control push_postback"  name="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                                  <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                                  <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                </div>
                                <div class="form-group" id="text_with_button_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                  <label><?php echo $this->lang->line("Web Url"); ?></label>
                                  <input type="text" class="form-control"  name="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>">
                                </div>
                                <div class="form-group" id="text_with_button_call_us_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                  <label><?php echo $this->lang->line("Phone Number"); ?></label>
                                  <input type="text" class="form-control"  name="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>">
                                </div>
                              </div>

                              <?php if($i != 1) : ?>
                                <div class="col-2 col-sm-1" >
                                  <br/>
                                  <i class="fa fa-2x fa-times-circle red item_remove" template_type="not_quick_reply" row_id="text_with_buttons_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" third_callus="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" counter_variable="text_with_button_counter_<?php echo $k; ?>" add_more_button_id="text_with_button_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                                </div>
                              <?php endif; ?>


                            </div>
                            <?php endfor; ?>

                            </div>
                            <!--   This hidden input is added by Konok to keep sorted order  -->
                            <input type="hidden" name="text_button_sort_order_<?php echo $k; ?>" id="text_button_sort_order_<?php echo $k; ?>">

                            <div class="row clearfix">
                              <div class="col-12 text-center"><button class="btn btn-outline-primary float-right no_radius btn-xs" id="text_with_button_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                            </div>

                          </div> 
                        </div>

                        <div class="row" id="generic_template_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">   
                          <div class="col-12"> 
                            
                            <div class="card card-secondary">
                              <div class="card-header">
                                <h4><?php echo $this->lang->line('Generic Template'); ?></h4>
                              </div>
                              <div class="card-body">

                                <div class="row">
                                  <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("Please provide your reply image"); ?> <span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></label>
                                      <input type="text" placeholder="<?php echo $this->lang->line('Put your image URL here or click the upload button.'); ?>" class="form-control"  name="generic_template_image_<?php echo $k; ?>" id="generic_template_image_<?php echo $k; ?>" />
                                      <div id="generic_image_<?php echo $k; ?>"><?php echo $this->lang->line('upload'); ?></div>
                                    </div>                         
                                  </div>
                                  <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("image click destination link"); ?> <span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></label>
                                      <input type="text" class="form-control"  name="generic_template_image_destination_link_<?php echo $k; ?>" id="generic_template_image_destination_link_<?php echo $k; ?>" />
                                    </div> 
                                  </div>                      
                                </div>

                                <div class="row">
                                  <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("title"); ?></label>
                                      <input type="text" class="form-control"  name="generic_template_title_<?php echo $k; ?>" id="generic_template_title_<?php echo $k; ?>" />
                                    </div>
                                  </div>  
                                  <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("sub-title"); ?></label>
                                      <input type="text" class="form-control"  name="generic_template_subtitle_<?php echo $k; ?>" id="generic_template_subtitle_<?php echo $k; ?>" />
                                    </div>
                                  </div>  
                                </div>

                                <span class="float-right"><span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></span><div class="clearfix"></div>

                                <!--   This hidden input is added by Konok to keep sorted order  -->
                                <div id="generic_button_sort_<?php echo $k; ?>">


                                <?php for ($i=1; $i <=3 ; $i++) : ?>
                                <div class="row button_border" id="generic_template_row_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                  <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("button text"); ?></label>
                                      <input type="text" class="form-control"  name="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>">
                                    </div>
                                  </div>
                                  <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("button type"); ?></label>
                                      <select class="form-control generic_template_button_type_class" id="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                                        <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                                        <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                                        <option value="web_url"><?php echo $this->lang->line("Web Url"); ?></option>

                                        <option value="web_url_compact" <?php echo $hide_generic_item; ?>><?php echo $this->lang->line("WebView [Compact]"); ?></option>
                                        <option value="web_url_tall" <?php echo $hide_generic_item; ?>><?php echo $this->lang->line("WebView [Tall]"); ?></option>
                                        <option value="web_url_full" <?php echo $hide_generic_item; ?>><?php echo $this->lang->line("WebView [Full]"); ?></option>
                                        <option value="web_url_birthday"><?php echo $this->lang->line("User's Birthday"); ?></option>

                                        <option value="web_url_email"><?php echo $this->lang->line("User's Email"); ?></option>
                                        <option value="web_url_phone"><?php echo $this->lang->line("User's Phone"); ?></option>
                                        <option value="web_url_location"><?php echo $this->lang->line("User's Location"); ?></option>

                                        <option value="phone_number" <?php echo $hide_generic_item; ?>><?php echo $this->lang->line("Call Us"); ?></option>
                                        
                                        <option value="post_back" id="unsubscribe_postback" <?php echo $hide_generic_item; ?>><?php echo $this->lang->line("unsubscribe"); ?></option>
                                        <option value="post_back" id="resubscribe_postback" <?php echo $hide_generic_item; ?>><?php echo $this->lang->line("re-subscribe"); ?></option>
                                        
                                        <option value="post_back" id="human_postback" <?php echo $hide_generic_item; ?>><?php echo $this->lang->line("Chat with Human"); ?></option>
                                        <option value="post_back" id="robot_postback" <?php echo $hide_generic_item; ?>><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-10 col-sm-3">
                                    <div class="form-group" id="generic_template_button_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                      <label><?php echo $this->lang->line("PostBack id"); ?></label>
                                      <select class="form-control push_postback"  name="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                                      <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                                      <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                    </div>
                                    <div class="form-group" id="generic_template_button_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                      <label><?php echo $this->lang->line("Web Url"); ?></label>
                                      <input type="text" class="form-control"  name="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>">
                                    </div>
                                    <div class="form-group" id="generic_template_button_call_us_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                      <label><?php echo $this->lang->line("Phone Number"); ?></label>
                                      <input type="text" class="form-control"  name="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>">
                                    </div>
                                  </div>

                                  <?php if($i != 1) : ?>
                                    <div class="col-2 col-sm-1">
                                      <br/>
                                      <i class="fa fa-2x fa-times-circle red item_remove" template_type="not_quick_reply" row_id="generic_template_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" third_callus="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" counter_variable="generic_with_button_counter_<?php echo $k; ?>" add_more_button_id="generic_template_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                                    </div>
                                  <?php endif; ?>

                                </div>
                                <?php endfor; ?>

                                </div>
                                <!--   This hidden input is added by Konok to keep sorted order  -->
                                <input type="hidden" name="generic_button_sort_order_<?php echo $k; ?>" id="generic_button_sort_order_<?php echo $k; ?>">



                                <div class="row clearfix">
                                  <div class="col-12 text-center"><button class="btn btn-outline-primary float-right no_radius btn-xs" id="generic_template_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                                </div>

                              </div> <!--end of card-body -->
                            </div>
                          </div>
                        </div>


                        <div class="row" id="carousel_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  

                          <!--   This hidden input is added by Konok to keep sorted order  -->
                          <div class="col-12" id="carousel_reply_sort_<?php echo $k; ?>">


                          <?php for ($j=1; $j <=10 ; $j++) : ?>
                          <div class="col-12" id="carousel_div_<?php echo $j; ?>_<?php echo $k; ?>" style="display: none;">

                            <div class="card card-secondary">
                              <div class="card-header">
                                <h4 class="full_width"><?php echo $this->lang->line('Carousel Template').' '.$j; ?>
                                  <?php if($j != 1) : ?>
                                  <i class="fa fa-times-circle remove_carousel_template float-right red" previous_row_id="carousel_div_<?php echo $j-1; ?>_<?php echo $k; ?>" current_row_id="carousel_div_<?php echo $j; ?>_<?php echo $k; ?>" counter_variable="carousel_template_counter_<?php echo $k; ?>" template_add_button="carousel_template_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                                  <?php endif; ?>
                                </h4>
                              </div>
                              <div class="card-body">

                                <div class="row">
                                  <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("Please provide your reply image"); ?> <span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></label>
                                      <input type="text" placeholder="<?php echo $this->lang->line('Put your image URL here or click the upload button.'); ?>" class="form-control"  name="carousel_image_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_image_<?php echo $j; ?>_<?php echo $k; ?>" />
                                      <div id="generic_imageupload_<?php echo $j; ?>_<?php echo $k; ?>"><?php echo $this->lang->line('upload'); ?></div>
                                    </div>                         
                                  </div>
                                  <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("image click destination link"); ?> <span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></label>
                                      <input type="text" class="form-control"  name="carousel_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" />
                                    </div> 
                                  </div>                      
                                </div>

                                <div class="row">
                                  <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("title"); ?></label>
                                      <input type="text" class="form-control"  name="carousel_title_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_title_<?php echo $j; ?>_<?php echo $k; ?>" />
                                    </div>
                                  </div>  
                                  <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("sub-title"); ?></label>
                                      <input type="text" class="form-control"  name="carousel_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" />
                                    </div>
                                  </div>  
                                </div>

                                <span class="float-right"><span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></span><div class="clearfix"></div>

                                <!--   This hidden input is added by Konok to keep sorted order  -->
                                <div id="carousel_button_sort_<?php echo $j; ?>_<?php echo $k; ?>">


                                <?php for ($i=1; $i <=3 ; $i++) : ?>
                                <div class="row button_border" id="carousel_row_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;">
                                  <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("button text"); ?></label>
                                      <input type="text" class="form-control"  name="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                                    </div>
                                  </div>
                                  <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                      <label><?php echo $this->lang->line("button type"); ?></label>
                                      <select class="form-control carousel_button_type_class" id="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" name="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                                        <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                                        <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                                        <option value="web_url"><?php echo $this->lang->line("Web Url"); ?></option>

                                        <option value="web_url_compact"><?php echo $this->lang->line("WebView [Compact]"); ?></option>
                                        <option value="web_url_tall"><?php echo $this->lang->line("WebView [Tall]"); ?></option>
                                        <option value="web_url_full"><?php echo $this->lang->line("WebView [Full]"); ?></option>
                                        <option value="web_url_birthday"><?php echo $this->lang->line("User's Birthday"); ?></option>

                                        <option value="web_url_email"><?php echo $this->lang->line("User's Email"); ?></option>
                                        <option value="web_url_phone"><?php echo $this->lang->line("User's Phone"); ?></option>
                                        <option value="web_url_location"><?php echo $this->lang->line("User's Location"); ?></option>

                                        <option value="phone_number"><?php echo $this->lang->line("Call Us"); ?></option>
                                        
                                        <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("unsubscribe"); ?></option>
                                        <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("re-subscribe"); ?></option>
                                        
                                        <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                                        <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-10 col-sm-3">
                                    <div class="form-group" id="carousel_button_postid_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;">
                                      <label><?php echo $this->lang->line("PostBack id"); ?></label>
                                      <select class="form-control push_postback"  name="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                                      <a href="" class="add_template float-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                                      <a href="" class="ref_template float-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                    </div>
                                    <div class="form-group" id="carousel_button_web_url_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;">
                                      <label><?php echo $this->lang->line("Web Url"); ?></label>
                                      <input type="text" class="form-control"  name="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                                    </div>
                                    <div class="form-group" id="carousel_button_call_us_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;">
                                      <label><?php echo $this->lang->line("Phone Number"); ?></label>
                                      <input type="text" class="form-control"  name="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                                    </div>
                                  </div>

                                  <?php if($i != 1) : ?>
                                    <div class="col-2 col-sm-1">
                                      <br/>
                                      <i class="fa fa-2x fa-times-circle red item_remove" template_type="not_quick_reply" row_id="carousel_row_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" first_column_id="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" second_column_id="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_postback="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_weburl="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_callus="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" counter_variable="carousel_add_button_counter_<?php echo $j; ?>_<?php echo $k; ?>" add_more_button_id="carousel_add_button_<?php echo $j; ?>_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                                    </div>
                                  <?php endif; ?>

                                </div>
                                <?php endfor; ?>

                                </div>
                                <!--   This hidden input is added by Konok to keep sorted order  -->
                                <input type="hidden" name="carousel_button_sort_order_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_button_sort_order_<?php echo $j; ?>_<?php echo $k; ?>">


                                <div class="row clearfix" style="padding-bottom: 10px;">
                                  <div class="col-12 text-center"><button class="btn btn-outline-primary float-right no_radius btn-xs" id="carousel_add_button_<?php echo $j; ?>_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                                </div>


                              </div> <!-- end of card body -->
                            </div>
                          </div>
                          <?php endfor; ?>

                          </div>
                          <!--   This hidden input is added by Konok to keep sorted order  -->
                          <input type="hidden" name="carousel_reply_sort_order_<?php echo $k; ?>" id="carousel_reply_sort_order_<?php echo $k; ?>">

                          <div class="col-12 clearfix">
                            <button id="carousel_template_add_button_<?php echo $k; ?>" class="btn btn-sm btn-outline-primary float-right no_radius"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more template");?></button>
                          </div>

                        </div>


                      </div>

                  </div> <!-- end of card body  -->
                </div>     
              <?php }  ?>

              </div>
              <!--   This hidden input is added by Konok to keep sorted order  -->
              <input type="hidden" name="main_reply_sort_order" id="main_reply_sort_order">


        <div class="row">
          <div class="col-12 clearfix">
            <button id="multiple_template_add_button"  class="btn btn-outline-primary float-right no_radius" ><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add more reply'); ?></button>
          </div>
        </div>
              <br/><br/>
              <div class="row">
                <div class="col-6">
                  <button id="submit" class="btn btn-lg btn-primary"><i class="fa fa-send"></i> <?php echo $this->lang->line('submit'); ?></button>
                </div>
                <div class="col-6">
                  <a class="btn btn-lg btn-secondary float-right" href="<?php echo base_url("messenger_bot/ig_bot_settings/").$page_info['id']."/1"; ?>"><i class="fas fa-times"></i> <?php echo $this->lang->line('Back'); ?></a>
                </div>
              </div>

            </form>

          </div>

        <?php if($iframe!="1") : ?>
        <div class="hidden-xs hidden-sm col-md-3 img_holder" style="" >
            <div id="text_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/text.png')) echo site_url()."assets/images/preview/text.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/text.png"; ?>" class="img-rounded" alt="Text Preview"></center>
            </div>

            <div id="image_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/image.png')) echo site_url()."assets/images/preview/image.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/image.png"; ?>" class="img-rounded" alt="Image Preview"></center>
            </div>

            <div id="audio_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/mp3.png')) echo site_url()."assets/images/preview/mp3.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/mp3.png"; ?>" class="img-rounded" alt="Audio Preview"></center>
            </div>

            <div id="video_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/video.png')) echo site_url()."assets/images/preview/video.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/video.png"; ?>" class="img-rounded" alt="Video Preview"></center>
            </div>

            <div id="file_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/file.png')) echo site_url()."assets/images/preview/file.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/file.png"; ?>" class="img-rounded" alt="File Preview"></center>
            </div>

            <div id="quick_reply_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/quick_reply.png')) echo site_url()."assets/images/preview/quick_reply.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/quick_reply.png"; ?>" class="img-rounded" alt="Quick Reply Preview"></center>
            </div>

            <div id="text_with_buttons_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/button.png')) echo site_url()."assets/images/preview/button.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/button.png"; ?>" class="img-rounded" alt="Text With Buttons Preview"></center>
            </div>

            <div id="generic_template_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/generic.png')) echo site_url()."assets/images/preview/generic.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/generic.png"; ?>" class="img-rounded" alt="Generic Template Preview"></center>
            </div>

            <div id="carousel_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/carousel.png')) echo site_url()."assets/images/preview/carousel.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/carousel.png"; ?>" class="img-rounded" alt="Carousel Template Preview"></center>
            </div>

            <div id="media_preview_div">
              <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/media.png')) echo site_url()."assets/images/preview/media.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/media.png"; ?>" class="img-rounded" alt="Media Template Preview"></center>
            </div>

        </div>    
        <?php endif; ?>

        </div>

        <br>
        <div id="submit_status" class="text-center"></div>          

      </div>

    </div>
  </div>
</div>

<?php if($this->session->flashdata('bot_success')===1) { ?>
<div class="alert alert-success text-center" id="bot_success"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("Bot reply has been saved successfully.");?></div>
<?php } ?>
<?php if($this->session->flashdata('bot_update_success')===1) { ?>
<div class="alert alert-success text-center" id="bot_success"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("Bot reply has been saved successfully.");?></div>
<?php } ?>
           
<div class="table-responsive data-card" id="bot_list_datatable">
  <table class="table table-bordered table-sm table-striped" id="mytable">
    <thead>
      <tr>
        <th class="text-center"><?php echo $this->lang->line("SL");?></th>
        <th class="text-center"><?php echo $this->lang->line("Bot Name");?></th>
        <th class="text-center"><?php echo $this->lang->line("Keywords");?></th>
        <th class="text-center"><?php echo $this->lang->line("Status");?></th>
        <?php if($visual_flow_builder_exist == 'yes') : ?>
          <th class="text-center"><?php echo $this->lang->line("Type");?></th>
        <?php endif; ?>
        <th class="text-center"><?php echo $this->lang->line("Actions");?></th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $i=0;
      foreach ($bot_settings as $key => $value) 
      { 
        if($value['keyword_type'] == "email-quick-reply" || $value['keyword_type'] == "location-quick-reply" || $value['keyword_type'] == "birthday-quick-reply" || $value['keyword_type'] == "phone-quick-reply" || $value['keyword_type'] == "get-started" || $value['keyword_type'] == "no match" || $value['postback_id'] == "UNSUBSCRIBE_QUICK_BOXER" || $value['postback_id'] == "RESUBSCRIBE_QUICK_BOXER" || $value['postback_id'] == "YES_START_CHAT_WITH_HUMAN" || $value['postback_id'] == "YES_START_CHAT_WITH_BOT" || $value['postback_id'] == "STORY_PRIVATE_REPLY" || $value['postback_id'] == "MESSAGE_UNSEND_PRIVATE_REPLY") continue;
        $i++;

        if($value['last_replied_at']!="0000-00-00 00:00:00") $reply_at=date('d M y - H:i:s');
        else $reply_at =  "<span class='label label-warning'><i class='fa fa-remove'></i> ".$this->lang->line('No Replied Yet')."</span>";

        if($value['keyword_type']=="reply") 
        {
          if(strlen($value['keywords'])>20) $keywords_display=substr($value['keywords'],0,20).'...';
          else $keywords_display=$value['keywords'];
          $keywords_tooltip='<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Keywords").'" data-content="'.str_replace(",",", ", $value["keywords"]).'"><i class="fa fa-info-circle gray"></i> </a>';
          $keywords_display=str_replace(',', ', ', $keywords_display)." ".$keywords_tooltip;
        }
        else $keywords_display =  "<span class='label label-light orange'><i class='fa fa-remove'></i></span>";

        if ($value['status'] == '1') {
            $current_status = '<i class="fas fa-dot-circle green"></i> '.$this->lang->line("Live");
        } else {
            $current_status = '<i class="fas fa-dot-circle gray"></i> '.$this->lang->line("Paused");
        }

        echo "<tr>";
          echo "<td class='text-center'>".$i."</td>";
          echo "<td>".$value['bot_name']."</td>";
          echo "<td>".$keywords_display."</td>";
          echo "<td class='text-center'>".$current_status."</td>";
          if($visual_flow_builder_exist == 'yes')
          {
            if(isset($value['visual_flow_type']) && $value['visual_flow_type'] == 'flow')
            {
              echo "<td class='text-center'>".$this->lang->line('Flow Builder')."</td>";
            }
            else
            {
              echo "<td class='text-center'>".$this->lang->line('Classic Editor')."</td>";
            }
          }

          $editurl = base_url("messenger_bot/edit_bot/".$value['id']);

          if ($value['status'] == '1') {

            $state_text = $this->lang->line("Stop");
            $state_icon = '<i class="far fa-stop-circle"></i>';
          } else {

            $state_text = $this->lang->line("Start");
            $state_icon = '<i class="far fa-play-circle"></i>';
          }

          if(isset($iframe) && $iframe=='1') 
          {
            $editurl.='/1/0/ig';
          }
          echo "<td class='text-center'>";
            $action_content = '<a href="#" data-toggle="dropdown" class="btn btn-outline-primary btn-circle dropdown-toggle bot_actions no_caret"><i class="fas fa-briefcase"></i></a> 
            
            <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
             <div class="dropdown-title">'.$this->lang->line("Actions").'</div>
             <li><a class="dropdown-item has-icon bot_status_btn" href="#" table_id="'.$value['id'].'">'.$state_icon.' '.$state_text.'</a></li>';

            if(addon_exist($module_id=315,$addon_unique_name="visual_flow_builder") && isset($value['visual_flow_type']) && $value['visual_flow_type'] == 'flow')
            {
              $flow_edit_url = base_url()."visual_flow_builder/edit_builder_data/".$value['visual_flow_campaign_id']."/0/ig";
              $action_content .= '<li><a class="dropdown-item has-icon" target="_BLANK" href="'.$flow_edit_url.'"><i class="fas fa-edit"></i> '.$this->lang->line("Edit Bot Reply").'</a></li>';
            }
            else
            {
              $action_content .= '<li><a class="dropdown-item has-icon" href="'.$editurl.'"><i class="fas fa-edit"></i> '.$this->lang->line("Edit Bot Reply").'</a></li>';
            }
             
            if($value['visual_flow_type'] == 'general') {
               $action_content .= '<div class="dropdown-divider d-none"></div>
              <li><a class="dropdown-item has-icon delete_bot red" href="" id="'.$value['id'].'"><i class="fas fa-trash-alt"></i> '.$this->lang->line("Delete Bot Reply").'</a></li>';
            }
              

            $action_content .= '</ul>';
            echo $action_content;
          echo "</td>";

        echo "</tr>"; ?>                   
      <?php
      } ?>
    </tbody>
  </table>
</div>
    
<?php
  $somethingwentwrong = $this->lang->line("something went wrong.");  
  $doyoureallywanttodeletethisbot = $this->lang->line("do you really want to delete this bot?");
?>


<?php 
$areyousure=$this->lang->line("are you sure"); 
// $drop_menu = '<a id="add_bot_settings" href="" class="float-right btn btn-primary"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Add Bot Reply").'</a>';
$builder_load_url = base_url("visual_flow_builder/load_builder/{$page_info['id']}/1/{$media_type}");
$drop_menu = '<a href="'.$builder_load_url.'" class="float-right btn btn-primary" type="button" target="_BLANK"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Set Keywords").'</a> <span style="font-size:16px;font-weight:700;" id="add_bot_settings" title="'.$this->lang->line('Use Classic Builder').'"><a class="float-right mr-2 mt-1" href="#"><i class="fa fa-plus-circle"></i> </a></span>';
?> 

<script type="text/javascript">
  var drop_menu = '<?php echo $drop_menu;?>';
  setTimeout(function(){ 
    $("#mytable_filter").append(drop_menu);
  }, 1000);
  
  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
  var base_url="<?php echo site_url(); ?>";


  var visual_flow_builder_exist = "<?php echo $visual_flow_builder_exist; ?>";
  if(visual_flow_builder_exist == 'yes')
    var shortable_column = [5];
  else
    var shortable_column = [4];

  var table = $("#mytable").DataTable({
      language: 
      {
        url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
      },
      dom: '<"top"f>rt<"bottom"lip><"clear">',
      columnDefs: [
        {
            targets: shortable_column,
            sortable: false
        }
      ]
  });

  $(document).ready(function(e){

    $(".ecommerce_product_info").select2({
      maximumSelectionLength: 10
    });
    
    // Main Reply Sortable, Added by Konok 22.08.2020
    $("#main_reply_sort").sortable({cancel: '.emojionearea-editor, select ,input, textarea, span, a , i'});



    $("#keywordtype_postback_id").select2();

    $( document ).on( 'click', '.bs-dropdown-to-select-group .dropdown-menu li', function( event ) {
      var $target = $( event.currentTarget );
      $target.closest('.bs-dropdown-to-select-group')
      .find('[data-bind="bs-drp-sel-value"]').val($target.attr('data-value'))
      .end()
      .children('.dropdown-toggle').dropdown('toggle');
      $target.closest('.bs-dropdown-to-select-group')
      .find('[data-bind="bs-drp-sel-label"]').text($target.context.textContent);
      return false;
    });

    $(document).on('change','.ecommerce_store_info',function(e){
      e.preventDefault();
      var product_dropdown_div = $(this).attr("product_dropdown_id");
      var store_id = $(this).val();
      var page_id = $("#page_table_id").val();
      if(page_id=="")
      {
        swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
        return false;
      }

      $.ajax({
        type:'POST' ,
        url: base_url+'messenger_bot/get_storewise_products',
        data: {page_auto_id:page_id,store_id:store_id},
        dataType : 'JSON',
        success:function(response){  
          $("#"+product_dropdown_div).html(response.dropdown);  
        }
      });
    });

  });
</script>


<script type="text/javascript">
  

  var js_array = [<?php echo '"'.implode('","', $postback_id_array ).'"' ?>];

  var areyousure="<?php echo $areyousure;?>";
  
  var text_with_button_counter = 1;
  var generic_template_button_counter = 1;
  var carousel_template_counter = 1;
  $(document).ready(function() {


    $(document).on('click','.media_template_modal',function(){
       $("#media_template_modal").modal();
    });
  
  
    /**Load Emoji For first Text Reply Field By Default***/
   $("#text_reply_1").emojioneArea({
            autocomplete: false,
        pickerPosition: "bottom"
     });

    // getting postback list and making iframe
    var page_id="<?php echo $page_info['id'];?>";
    var media_type = "<?php echo $media_type; ?>";
    var rand_time="<?php echo time(); ?>";
    var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id+"/0/"+media_type+"?lev="+rand_time;
    $('#add_template_modal').on('shown.bs.modal',function(){ 
      $(this).find('iframe').attr('src',iframe_link); 
    });   
    refresh_template("0");
    $("#loader").addClass('hidden');
    // getting postback list and making iframe

    

    var keyword_type = $("input[name=keyword_type]").val();
    if(keyword_type == 'reply')
    {
      $("#keywords_div").show();
    }else{
      $("#keywords_div").hide();
    }

    $(document).on('change','input[name=keyword_type]',function(){
      if($("input[name=keyword_type]").val()=="reply")
      {
        $("#keywords_div").show();
      }
      else 
      {
        $("#keywords_div").hide();
      }
    });



    var multiple_template_add_button_counter = 1;
    $(document).on('click','#multiple_template_add_button',function(e){
      e.preventDefault();
      multiple_template_add_button_counter++
    
     $("#text_reply_"+multiple_template_add_button_counter).emojioneArea({
            autocomplete: false,
        pickerPosition: "bottom"
       });
    
      $("#multiple_template_div_"+multiple_template_add_button_counter).show();

      var previous_div_id_counter = multiple_template_add_button_counter-1;
      $("#multiple_template_div_"+previous_div_id_counter).find(".remove_reply").hide();

      if(multiple_template_add_button_counter == 6){
        $("#multiple_template_add_button").hide();
      }
    });


    $(document).on('click','.remove_reply',function(){
      var remove_reply_counter_variable = "multiple_template_add_button_counter";
      var remove_reply_row_id = $(this).attr('row_id');
      $("#"+remove_reply_row_id).find('textarea,input,select').val('');

      $("#"+remove_reply_row_id).hide();
      eval(remove_reply_counter_variable+"--");
      var temp = eval(remove_reply_counter_variable);
      if(temp != 1)
      {
        $("#multiple_template_div_"+temp).find(".remove_reply").show();
      }
      if(temp < 6) $("#multiple_template_add_button").show();
    });

    // remove carousel template 
    $(document).on('click','.remove_carousel_template',function(){
      var remove_carousel_counter_variable = $(this).attr('counter_variable');
      var template_add_button = $(this).attr('template_add_button');
      var remove_carousel_row_id = $(this).attr('current_row_id');
      var previous_carousel_row_id = $(this).attr('previous_row_id');
      $("#"+remove_carousel_row_id).find('textarea,input,select').val('');
      $("#"+remove_carousel_row_id).hide();
      eval(remove_carousel_counter_variable+"--");
      var temp = eval(remove_carousel_counter_variable);
      if(temp != 1)
      {
        $("#"+previous_carousel_row_id).find(".remove_carousel_template").show();
      }
      if(temp < 10) $("#"+template_add_button).show();
    });


    var keyword_type = $("input[name=keyword_type]").val();
    if(keyword_type == 'post-back')
    {
      $("#postback_div").show();
    }

    $(document).on('change','input[name=keyword_type]',function(){    
      if($("input[name=keyword_type]").val()=="post-back")
      {
        $("#postback_div").show();
      }
      else 
      {
        $("#postback_div").hide();
      }
    });

    var image_upload_limit = "<?php echo $image_upload_limit; ?>";
    var video_upload_limit = "<?php echo $video_upload_limit; ?>";
    var audio_upload_limit = "<?php echo $audio_upload_limit; ?>";
    var file_upload_limit = "<?php echo $file_upload_limit; ?>";
  
<?php for($template_type=1;$template_type<=6;$template_type++){ ?>


   $("#quick_reply_sort_<?php echo $template_type ?>").sortable({cancel: '.emojionearea-editor, select ,input, textarea, span, a , i'});
   $("#media_postback_sort_<?php echo $template_type ?>").sortable({cancel: '.emojionearea-editor, select ,input, textarea, span, a , i'});
   $("#text_button_sort_<?php echo $template_type ?>").sortable({cancel: '.emojionearea-editor, select ,input, textarea, span, a , i'});
   $("#carousel_reply_sort_<?php echo $template_type ?>").sortable({cancel: '.emojionearea-editor, select ,input, textarea, span, a , i'});
   $("#generic_button_sort_<?php echo $template_type ?>").sortable({cancel: '.emojionearea-editor, select ,input, textarea, span, a , i'});

   // for carousel button , need to run an loop to apply sorting to all carousel reply button. 

   <?php for($carousel_number=1;$carousel_number<=10;$carousel_number++){ ?>

    $("#carousel_button_sort_<?php echo $carousel_number ?>_<?php echo $template_type ?>").sortable({cancel: '.emojionearea-editor, select ,input, textarea, span, a , i'});

   <?php } ?>


  
    var template_type_order="#template_type_<?php echo $template_type ?>";
  
    $(document).on('change',"#template_type_<?php echo $template_type ?>",function(){
  
      var selected_template = $("#template_type_<?php echo $template_type ?>").val();
      selected_template = selected_template.replace(/ /gi, "_");

      var template_type_array = ['text','image','audio','video','file','quick_reply','text_with_buttons','generic_template','carousel','list','media','One_Time_Notification','User_Input_Flow', 'Ecommerce'];
      template_type_array.forEach(templates_hide_show_function);
      function templates_hide_show_function(item, index)
      {
        var template_type_preview_div_name = "#"+item+"_preview_div";
        var template_type_div_name = "#"+item+"_div_<?php echo $template_type; ?>";
        var delay_and_typing_on_div = "#delay_and_typing_on_<?php echo $template_type; ?>";

        if(selected_template == item){
          $(template_type_div_name).show();
          $(template_type_preview_div_name).show();
        }
        else{
          $(template_type_div_name).hide();
          $(template_type_preview_div_name).hide();
        }
        $(delay_and_typing_on_div).show();

        if(selected_template == 'User_Input_Flow')
        {
          var selected_input_flow_count = "<?php echo $template_type+1 ?>";
          for(var input_flow=selected_input_flow_count; input_flow<=multiple_template_add_button_counter; input_flow++)
          {
            remove_reply_row_id="multiple_template_div_"+input_flow;
            $("#"+remove_reply_row_id).find('textarea,input,select').val('');
            $("#"+remove_reply_row_id).find(".remove_reply").show();
            $("#"+remove_reply_row_id).hide();
          }
          $(delay_and_typing_on_div).hide();
          $("#multiple_template_add_button").hide();
          multiple_template_add_button_counter = <?php echo $template_type; ?>;
          $("#multiple_template_div_<?php echo $template_type; ?>").find(".remove_reply").show();
        }
        else
          $("#multiple_template_add_button").show();
    
        if(selected_template=='text'){
          
           $("#text_reply_<?php echo $template_type; ?>").emojioneArea({
                autocomplete: false,
            pickerPosition: "bottom"
             });
        }

        if(selected_template=='One_Time_Notification'){
          $(delay_and_typing_on_div).hide();
        }

        if(selected_template == 'media')
        {
          $("#media_row_1_<?php echo $template_type; ?>").show();     
        }
    

        if(selected_template == 'quick_reply')
        {
          $("#quick_reply_row_1_<?php echo $template_type; ?>").show();
      
          $("#quick_reply_text_<?php echo $template_type; ?>").emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom"
          });
     
      
        }

        if(selected_template == 'text_with_buttons')
        {
          $("#text_with_buttons_row_1_<?php echo $template_type; ?>").show();
      
          $("#text_with_buttons_input_<?php echo $template_type; ?>").emojioneArea({
            autocomplete: false,
            pickerPosition: "bottom"
          });
     
     
        }

        if(selected_template == 'generic_template')
        {
          $("#generic_template_row_1_<?php echo $template_type; ?>").show();
        }

        if(selected_template == 'carousel')
        {
          $("#carousel_div_1_<?php echo $template_type; ?>").show();
          $("#carousel_row_1_1_<?php echo $template_type; ?>").show();
        }

        if(selected_template == 'list')
        {
          $("#list_div_1_<?php echo $template_type; ?>").show();
          $("#list_div_2_<?php echo $template_type; ?>").show();
        }

      }
    });
  
  
    $("#image_reply_<?php echo $template_type; ?>").uploadFile({
      url:base_url+"messenger_bot/upload_image_only",
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
          var delete_url="<?php echo site_url('messenger_bot/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#image_reply_field_<?php echo $template_type; ?>").val('');  
                $("#image_reply_div_<?php echo $template_type; ?>").hide();                     
              });
         
       },
       onSuccess:function(files,data,xhr,pd)
         {
             var data_modified = base_url+"upload/image/"+user_id+"/"+data;
             $("#image_reply_field_<?php echo $template_type; ?>").val(data_modified);   
             $("#image_reply_div_<?php echo $template_type; ?>").show().attr('src',data_modified);   
         }
    });


    $("#video_reply_<?php echo $template_type; ?>").uploadFile({
      url:base_url+"messenger_bot/upload_live_video",
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
        var delete_url="<?php echo site_url('messenger_bot/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#video_reply_field_<?php echo $template_type; ?>").val('');  
              $("#video_tag_<?php echo $template_type; ?>").hide();             
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#video_reply_field_<?php echo $template_type; ?>").val(file_path);   
        $("#video_tag_<?php echo $template_type; ?>").show();
        $("#video_reply_div_<?php echo $template_type; ?>").attr('src',file_path); 
      }
    });

    $("#audio_reply_<?php echo $template_type; ?>").uploadFile({
      url:base_url+"messenger_bot/upload_audio_file",
      fileName:"myfile",
      maxFileSize:audio_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".amr,.mp3,.wav,.WAV,.MP3,.AMR",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('messenger_bot/delete_audio_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#audio_reply_field_<?php echo $template_type; ?>").val('');  
              $("#audio_tag_<?php echo $template_type; ?>").hide();             
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/audio/"+data;
        $("#audio_reply_field_<?php echo $template_type; ?>").val(file_path);   
        $("#audio_tag_<?php echo $template_type; ?>").show();
        $("#audio_reply_div_<?php echo $template_type; ?>").attr('src',file_path); 
      }
    });

    $("#file_reply_<?php echo $template_type; ?>").uploadFile({
      url:base_url+"messenger_bot/upload_general_file",
      fileName:"myfile",
      maxFileSize:file_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".doc,.docx,.pdf,.txt,.ppt,.pptx,.xls,.xlsx,.DOC,.DOCX,.PDF,.TXT,.PPT,.PPTX,.XLS,.XLSX",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('messenger_bot/delete_general_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#file_reply_field_<?php echo $template_type; ?>").val('');            
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/file/"+data;
        $("#file_reply_field_<?php echo $template_type; ?>").val(file_path);   
      }
    });


    $("#generic_image_<?php echo $template_type; ?>").uploadFile({
      url:base_url+"messenger_bot/upload_image_only",
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
          var delete_url="<?php echo site_url('messenger_bot/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#generic_template_image_<?php echo $template_type; ?>").val('');                   
              });
         
       },
       onSuccess:function(files,data,xhr,pd)
         {
             var data_modified = base_url+"upload/image/"+user_id+"/"+data;
             $("#generic_template_image_<?php echo $template_type; ?>").val(data_modified);     
         }
      });

  
  
    <?php for($i=1; $i<=10; $i++) : ?>
      $("#generic_imageupload_<?php echo $i; ?>_<?php echo $template_type; ?>").uploadFile({
        url:base_url+"messenger_bot/upload_image_only",
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
            var delete_url="<?php echo site_url('messenger_bot/delete_uploaded_file');?>";
            $.post(delete_url, {op: "delete",name: data},
                function (resp,textStatus, jqXHR) {
                  $("#carousel_image_<?php echo $i; ?>_<?php echo $template_type; ?>").val('');                      
                });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = base_url+"upload/image/"+user_id+"/"+data;
               $("#carousel_image_<?php echo $i; ?>_<?php echo $template_type; ?>").val(data_modified);     
           }
      });
    <?php endfor; ?>


    var media_counter_<?php echo $template_type; ?> =1;
    
    $(document).on('click',"#media_add_button_<?php echo $template_type; ?>",function(e){
       e.preventDefault();

       var button_id = media_counter_<?php echo $template_type; ?>;
       var media_text = "#media_text_"+button_id+"_<?php echo $template_type; ?>";
       var media_type = "#media_type_"+button_id+"_<?php echo $template_type; ?>";

       var media_text_check = $(media_text).val();
       if(media_text_check == ''){
         swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
         return;
       }

       var media_type_check = $(media_type).val();
       if(media_type_check == ''){
         swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
         return;
       }else if(media_type_check == 'post_back'){

         var media_post_id = "#media_post_id_"+button_id+"_<?php echo $template_type; ?>";
         var media_post_id_check = $(media_post_id).val();
         if(media_post_id_check == ''){
           swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
           return;
         }

       }else if(media_type_check == 'web_url' || media_type_check == 'web_url_compact' || media_type_check == 'web_url_tall' || media_type_check == 'web_url_full'){
         var media_web_url = "#media_web_url_"+button_id+"_<?php echo $template_type; ?>";
         var media_web_url_check = $(media_web_url).val();
         if(media_web_url_check == ''){
           swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
           return;
         }
       }else if(media_type_check == 'phone_number'){
         var media_call_us = "#media_call_us_"+button_id+"_<?php echo $template_type; ?>";
         var media_call_us_check = $(media_call_us).val();
         if(media_call_us_check == ''){
           swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
           return;
         }
       }

       media_counter_<?php echo $template_type; ?>++;

       // remove button hide for current div and show for next div
       $(media_type).parent().parent().next().next().hide();
       var next_item_remove_parent_div = $(media_type).parent().parent().parent().next().attr('id');
       $("#"+next_item_remove_parent_div+" div:last").show();

       var x=media_counter_<?php echo $template_type; ?>;
       $("#media_row_"+x+"_<?php echo $template_type; ?>").show();
       if(media_counter_<?php echo $template_type; ?> == 3)
         $("#media_add_button_<?php echo $template_type; ?>").hide();
    });


  
    var quick_reply_button_counter_<?php echo $template_type; ?> = 1;
    
    $(document).on('click',"#quick_reply_add_button_<?php echo $template_type; ?>",function(e){
      e.preventDefault();
    
      var button_id = quick_reply_button_counter_<?php echo $template_type; ?>;      
      var quick_reply_button_text = "#quick_reply_button_text_"+button_id+"_<?php echo $template_type; ?>";
      var quick_reply_post_id = "#quick_reply_post_id_"+button_id+"_<?php echo $template_type; ?>";
      var quick_reply_button_type = "#quick_reply_button_type_"+button_id+"_<?php echo $template_type; ?>";

      quick_reply_button_type = $(quick_reply_button_type).val();

      var quick_reply_post_id_check = $(quick_reply_post_id).val();
      if(quick_reply_button_type == 'post_back')
      {        
        if(quick_reply_post_id_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
          return;
        }

        var quick_reply_button_text_check = $(quick_reply_button_text).val();

        if(quick_reply_button_text_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
          return;
        }

      }
      if(quick_reply_button_type == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
        return;
      }


      quick_reply_button_counter_<?php echo $template_type; ?>++;

      // remove button hide for current div and show for next div
      var div_id = "#quick_reply_button_type_"+button_id+"_<?php echo $template_type; ?>";
      $(div_id).parent().parent().next().next().hide();
      var next_item_remove_parent_div = $(div_id).parent().parent().parent().next().attr('id');
      $("#"+next_item_remove_parent_div+" div:last").show();
    
      var x=  quick_reply_button_counter_<?php echo $template_type; ?>;
      $("#quick_reply_row_"+x+"_<?php echo $template_type; ?>").show();
    
      if(quick_reply_button_counter_<?php echo $template_type; ?> == 11)
        $("#quick_reply_add_button_<?php echo $template_type; ?>").hide();

    });
  
  
   var text_with_button_counter_<?php echo $template_type; ?> =1;
  
   $(document).on('click',"#text_with_button_add_button_<?php echo $template_type; ?>",function(e){
      e.preventDefault();

      var button_id = text_with_button_counter_<?php echo $template_type; ?>;
      var text_with_buttons_text = "#text_with_buttons_text_"+button_id+"_<?php echo $template_type; ?>";
      var text_with_button_type = "#text_with_button_type_"+button_id+"_<?php echo $template_type; ?>";

      var text_with_buttons_text_check = $(text_with_buttons_text).val();
      if(text_with_buttons_text_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
        return;
      }

      var text_with_button_type_check = $(text_with_button_type).val();
      if(text_with_button_type_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
        return;
      }else if(text_with_button_type_check == 'post_back'){

        var text_with_button_post_id = "#text_with_button_post_id_"+button_id+"_<?php echo $template_type; ?>";
        var text_with_button_post_id_check = $(text_with_button_post_id).val();
        if(text_with_button_post_id_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
          return;
        }
      }else if(text_with_button_type_check == 'web_url' || text_with_button_type_check == 'web_url_compact' || text_with_button_type_check == 'web_url_tall' || text_with_button_type_check == 'web_url_full'){
        var text_with_button_web_url = "#text_with_button_web_url_"+button_id+"_<?php echo $template_type; ?>";
        var text_with_button_web_url_check = $(text_with_button_web_url).val();
        if(text_with_button_web_url_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
          return;
        }
      }else if(text_with_button_type_check == 'phone_number'){
        var text_with_button_call_us = "#text_with_button_call_us_"+button_id+"_<?php echo $template_type; ?>";
        var text_with_button_call_us_check = $(text_with_button_call_us).val();
        if(text_with_button_call_us_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
          return;
        }
      }

      text_with_button_counter_<?php echo $template_type; ?>++;

      // remove button hide for current div and show for next div
      $(text_with_button_type).parent().parent().next().next().hide();
      var next_item_remove_parent_div = $(text_with_button_type).parent().parent().parent().next().attr('id');
      $("#"+next_item_remove_parent_div+" div:last").show();

      var x=text_with_button_counter_<?php echo $template_type; ?>;
      $("#text_with_buttons_row_"+x+"_<?php echo $template_type; ?>").show();
      if(text_with_button_counter_<?php echo $template_type; ?> == 3)
        $("#text_with_button_add_button_<?php echo $template_type; ?>").hide();
    });
  


   var  generic_with_button_counter_<?php echo $template_type; ?> =1;
  
   $(document).on('click',"#generic_template_add_button_<?php echo $template_type; ?>",function(e){
      e.preventDefault();

      var button_id = generic_with_button_counter_<?php echo $template_type; ?>;
      var generic_template_button_text = "#generic_template_button_text_"+button_id+"_<?php echo $template_type; ?>";
      var generic_template_button_type = "#generic_template_button_type_"+button_id+"_<?php echo $template_type; ?>";

      var generic_template_button_text_check = $(generic_template_button_text).val();
      if(generic_template_button_text_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
        return;
      }

      var generic_template_button_type_check = $(generic_template_button_type).val();
      if(generic_template_button_type_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
        return;
      }else if(generic_template_button_type_check == 'post_back'){

        var generic_template_button_post_id = "#generic_template_button_post_id_"+button_id+"_<?php echo $template_type; ?>";
        var generic_template_button_post_id_check = $(generic_template_button_post_id).val();
        if(generic_template_button_post_id_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
          return;
        }

      }else if(generic_template_button_type_check == 'web_url' || generic_template_button_type_check == 'web_url_compact' || generic_template_button_type_check == 'web_url_tall' || generic_template_button_type_check == 'web_url_full'){

        var generic_template_button_web_url = "#generic_template_button_web_url_"+button_id+"_<?php echo $template_type; ?>";
        var generic_template_button_web_url_check = $(generic_template_button_web_url).val();
        if(generic_template_button_web_url_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
          return;
        }
      }else if(generic_template_button_type_check == 'phone_number'){
        var generic_template_button_call_us = "#generic_template_button_call_us_"+button_id+"_<?php echo $template_type; ?>";
        var generic_template_button_call_us_check = $(generic_template_button_call_us).val();
        if(generic_template_button_call_us_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
          return;
        }
      }

      generic_with_button_counter_<?php echo $template_type; ?>++;

      // remove button hide for current div and show for next div
      $(generic_template_button_type).parent().parent().next().next().hide();
      var next_item_remove_parent_div = $(generic_template_button_type).parent().parent().parent().next().attr('id');
      $("#"+next_item_remove_parent_div+" div:last").show();
    
      var x=generic_with_button_counter_<?php echo $template_type; ?>;
    
      $("#generic_template_row_"+x+"_<?php echo $template_type; ?>").show();
      if(generic_with_button_counter_<?php echo $template_type; ?> == 3)
        $("#generic_template_add_button_<?php echo $template_type; ?>").hide();
   });
  
  
    <?php for($j=1; $j<=10; $j++) : ?>

      var carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?> = 1;
    
      $(document).on('click',"#carousel_add_button_<?php echo $j; ?>_<?php echo $template_type; ?>",function(e){
        e.preventDefault();

        var y= carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?>;

        var carousel_button_text = "#carousel_button_text_<?php echo $j; ?>_"+y+"_<?php echo $template_type; ?>";
        var carousel_button_type = "#carousel_button_type_<?php echo $j; ?>_"+y+"_<?php echo $template_type; ?>";
    
        var carousel_button_text_check = $(carousel_button_text).val();
        if(carousel_button_text_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
          return;
        }

        var carousel_button_type_check = $(carousel_button_type).val();
        if(carousel_button_type_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
          return;
        }else if(carousel_button_type_check == 'post_back'){

          var carousel_button_post_id = "#carousel_button_post_id_<?php echo $j;?>_"+y+"_<?php echo $template_type; ?>";
          var carousel_button_post_id_check = $(carousel_button_post_id).val();
          if(carousel_button_post_id_check == ''){
            swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
            return;
          }
        }else if(carousel_button_type_check == 'web_url' || carousel_button_type_check == 'web_url_compact' || carousel_button_type_check == 'web_url_full' || carousel_button_type_check == 'web_url_tall'){

          var carousel_button_web_url = "#carousel_button_web_url_<?php echo $j;?>_"+y+"_<?php echo $template_type; ?>";
          var carousel_button_web_url_check = $(carousel_button_web_url).val();
          if(carousel_button_web_url_check == ''){
            swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
            return;
          }
        }else if(carousel_button_type_check == 'phone_number'){
          var carousel_button_call_us = "#carousel_button_call_us_<?php echo $j;?>_"+y+"_<?php echo $template_type; ?>";
          var carousel_button_call_us_check = $(carousel_button_call_us).val();
          if(carousel_button_call_us_check == ''){
            swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
            return;
          }
        }

        carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?> ++;

        // remove button hide for current div and show for next div
        $(carousel_button_type).parent().parent().next().next().hide();
        var next_item_remove_parent_div = $(carousel_button_type).parent().parent().parent().next().attr('id');
        $("#"+next_item_remove_parent_div+" div:last").show();

        var x= carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?>;

        $("#carousel_row_<?php echo $j; ?>_"+x+"_<?php echo $template_type; ?>").show();
        if(carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?> == 3)
          $("#carousel_add_button_<?php echo $j; ?>_<?php echo $template_type; ?>").hide();        

      });
    <?php endfor; ?>
  
  
    var carousel_template_counter_<?php echo $template_type; ?>=1;
  
    $(document).on('click','#carousel_template_add_button_<?php echo $template_type; ?>',function(e){
      e.preventDefault();

      var carousel_image = "#carousel_image_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var carousel_image_check = $(carousel_image).val();
     
      var carousel_title = "#carousel_title_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var carousel_title_check = $(carousel_title).val();
      if(carousel_title_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide carousel title')?>", 'warning');
        return;
      }

      var carousel_subtitle = "#carousel_subtitle_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var carousel_subtitle_check = $(carousel_subtitle).val();
     
      var carousel_image_destination_link = "#carousel_image_destination_link_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var carousel_image_destination_link_check = $(carousel_image_destination_link).val();
     

      carousel_template_counter_<?php echo $template_type; ?>++;
    
      var x = carousel_template_counter_<?php echo $template_type; ?>;
      // remove template
      var previous_template_counter = x-1;
      $("#carousel_div_"+previous_template_counter+"_<?php echo $template_type; ?>").find(".remove_carousel_template").hide();
    
      $("#carousel_div_"+x+"_<?php echo $template_type; ?>").show();
      $("#carousel_row_"+x+"_1"+"_<?php echo $template_type; ?>").show();
      if( carousel_template_counter_<?php echo $template_type; ?> == 10)
        $("#carousel_template_add_button_<?php echo $template_type; ?>").hide();
    });

  
  <?php } ?>
  
  
  
    $(document).on('click','.item_remove',function(){
      var counter_variable = $(this).attr('counter_variable');
      var row_id = $(this).attr('row_id');

      var first_column_id = $(this).attr('first_column_id');
      var second_column_id = $(this).attr('second_column_id');
      var add_more_button_id = $(this).attr('add_more_button_id');

      var item_remove_postback = $(this).attr('third_postback');
      var item_remove_weburl = $(this).attr('third_weburl');
      var item_remove_callus = $(this).attr('third_callus');

      var template_type = $(this).attr('template_type');

      $("#"+first_column_id).val('');
      $("#"+first_column_id).removeAttr('readonly');
      var item_remove_button_type = $("#"+second_column_id).val();
      $("#"+second_column_id).val('');

      if(item_remove_button_type == 'post_back')
      {
        if(item_remove_postback != '')
        $("#"+item_remove_postback).val('');
      }
      else if (item_remove_button_type == 'web_url' || item_remove_button_type == 'web_url_compact' || item_remove_button_type == 'web_url_full' || item_remove_button_type == 'web_url_tall' || item_remove_button_type == 'web_url_birthday' || item_remove_button_type == 'web_url_email' || item_remove_button_type == 'web_url_phone' || item_remove_button_type == 'web_url_location')
      {
        if(item_remove_weburl != '')
        $("#"+item_remove_weburl).val('');
      }
      else
      {
        if(item_remove_callus != '')
        $("#"+item_remove_callus).val('');
      }

      $("#"+row_id).hide();
      eval(counter_variable+"--");
      var temp = eval(counter_variable);

      if(temp != 1)
      {        
        var previous_item_remove_div = $("#"+row_id).prev('div').attr('id');
        $("#"+previous_item_remove_div+" div:last").show();
      }
      $(this).parent().hide();
      
      if(template_type == 'quick_reply')
      {
        if(temp < 11) $("#"+add_more_button_id).show();       
      }
      else
      {
        if(temp < 3) $("#"+add_more_button_id).show();        
      }

    });


    $(document).on('click','.delete_bot',function(e){
      e.preventDefault();
      var id = $(this).attr('id');
      var somethingwentwrong = "<?php echo $somethingwentwrong; ?>";
      var doyoureallywanttodeletethisbot = "<?php echo $doyoureallywanttodeletethisbot; ?>";
      var link="<?php echo $redirect_url; ?>"; 
      swal({
        title: '<?php echo $this->lang->line("Delete Bot Reply"); ?>',
        text: doyoureallywanttodeletethisbot,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
          $(this).addClass('btn-progress');
          $.ajax({
            context: this,
            type:'POST' ,
            url:"<?php echo site_url();?>messenger_bot/delete_bot",
            data: {id:id},
            success:function(response){ 
              $(this).removeClass('btn-progress');
              if(response == '1')
              {
                swal('<?php echo $this->lang->line("Reply Deleted"); ?>', "<?php echo $this->lang->line('Bot reply has been deleted successfully.'); ?>", 'success').then((value) => {
                  window.location.assign(link);
                });
              }
              else
              {
                swal('<?php echo $this->lang->line("Error"); ?>', somethingwentwrong, 'error');
              }
            }
          });
        } 
      });


    });



    $(document).on('change','.media_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      which_number_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 2];
      var which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];

      if(button_type == 'post_back')
      {
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" input").val(""); 
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#media_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {           
           $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" input").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" input").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
      }
      else if(button_type == 'web_url' || button_type == 'web_url_compact' || button_type == 'web_url_tall' || button_type == 'web_url_full')
      {
        $("#media_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#media_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else
      {
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
    });


    $(document).on('change','.quick_reply_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      var which_block_is_clicked="";
    
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      which_number_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 2];
      which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];

      if(button_type == 'post_back')
      {
        $("#quick_reply_button_text_"+which_number_is_clicked+"_"+which_block_is_clicked).removeAttr('readonly');
        $("#quick_reply_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
      }
      else
      {
        $("#quick_reply_button_text_"+which_number_is_clicked+"_"+which_block_is_clicked).attr('readonly','readonly');
        $("#quick_reply_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
    });


    $(document).on('change','.text_with_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      which_number_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 2];
      var which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];

      if(button_type == 'post_back')
      {
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='UNSUBSCRIBE_QUICK_BOXER']").remove();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='RESUBSCRIBE_QUICK_BOXER']").remove();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_HUMAN']").remove();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_BOT']").remove();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="human_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_HUMAN").text("<?php echo $this->lang->line('Chat with Human');?>")); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_HUMAN"); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="robot_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_BOT").text("<?php echo $this->lang->line('Chat with Robot');?>")); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_BOT"); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }

      }
      else if(button_type == 'web_url' || button_type == 'web_url_compact' || button_type == 'web_url_tall' || button_type == 'web_url_full')
      {
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else
      {
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
    });


    $(document).on('change','.generic_template_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      which_number_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 2];
      which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];
    
      if(button_type == 'post_back')
      {
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='UNSUBSCRIBE_QUICK_BOXER']").remove();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='RESUBSCRIBE_QUICK_BOXER']").remove();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_HUMAN']").remove();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_BOT']").remove();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="human_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_HUMAN").text("<?php echo $this->lang->line('Chat with Human');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_HUMAN"); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="robot_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_BOT").text("<?php echo $this->lang->line('Chat with Robot');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_BOT"); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
      }
      else if(button_type == 'web_url' || button_type == 'web_url_compact' || button_type == 'web_url_tall' || button_type == 'web_url_full')
      {
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else
      {
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
    });



    $(document).on('change','.carousel_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked = which_number_is_clicked.split('_');
    
      var first = which_number_is_clicked[which_number_is_clicked.length - 2];
      var second = which_number_is_clicked[which_number_is_clicked.length - 3];
    
      var block_template_third= which_number_is_clicked[which_number_is_clicked.length - 1];

      if(button_type == 'post_back')
      {
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option[value='UNSUBSCRIBE_QUICK_BOXER']").remove();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option[value='RESUBSCRIBE_QUICK_BOXER']").remove();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option[value='YES_START_CHAT_WITH_HUMAN']").remove();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option[value='YES_START_CHAT_WITH_BOT']").remove();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).show();
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
        if(option_id=="human_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_HUMAN").text("<?php echo $this->lang->line('Chat with Human');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").val("YES_START_CHAT_WITH_HUMAN"); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
        if(option_id=="robot_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_BOT").text("<?php echo $this->lang->line('Chat with Robot');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").val("YES_START_CHAT_WITH_BOT"); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
      }
      else if(button_type == 'web_url' || button_type == 'web_url_compact' || button_type == 'web_url_tall' || button_type == 'web_url_full')
      {
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).show();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).show();
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
      }
      else
      {
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).hide();
      }
    });



    $(document).on('click','#submit',function(e){   
      e.preventDefault();

      //Added By Konok for main reply sorting 22.08.2020
      var main_reply_sort_order = $("#main_reply_sort").sortable("serialize");
      $("#main_reply_sort_order").val(main_reply_sort_order);


      var bot_name = $("#bot_name").val();
      var keyword_type = $("input[name=keyword_type]").val();

      if(typeof($("input[name=keyword_type]").val()) == 'undefined')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select a reply type form (Reply/Post-back/No Match/Get Started)');?>", 'warning');
        return;
      }

      if(bot_name == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Give Bot Name');?>", 'warning');
        return;
      }


      if(keyword_type == 'post-back')
      {
        if($("#keywordtype_postback_id").val() == '' || typeof($("#keywordtype_postback_id").val()) == 'undefined' || $("#keywordtype_postback_id").val() == null)
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please provide postback id');?>", 'warning');
          return;
        }
      }

      if(keyword_type == 'reply')
      {
        var keywords_list = $("#keywords_list").val();
        if(keywords_list =='')
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Keywords In Comma Separated');?>", 'warning');
          return;
        }
      }

      for(var m=1; m<=multiple_template_add_button_counter; m++)
      {
          var template_type = $("#template_type_"+m).val();

          if(template_type == 'Ecommerce')
          {
            var ecommerce_store_id = $("#ecommerce_store_id"+m).val();
            if(ecommerce_store_id == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Select an Ecommerce Store')?>", 'warning');
              return;
            }

            var ecommerce_product_ids = $("#ecommerce_product_ids"+m).val();
            if(ecommerce_product_ids == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select atleast one product for carousel/generic reply')?>", 'warning');
              return;
            }
          }

          if(template_type == 'text')
          {
            var text_reply = $("#text_reply_"+m).val();
            if(text_reply == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Reply Message')?>", 'warning');
              return;
            }
          }

          if(template_type == 'User Input Flow')
          {
            var flow_campaign_id_ = $("#flow_campaign_id_"+m).val();
            if(flow_campaign_id_ == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Select a Flow Campaign')?>", 'warning');
              return;
            }
          }

          if(template_type == "image")
          {
            var image_reply_field =$("#image_reply_field_"+m).val();
            if(image_reply_field == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Reply Image')?>", 'warning');
              return;
            }
          }

          if(template_type == "One Time Notification")
          {
            var otn_title =$("#otn_title_"+m).val();
            var otn_postback =$("#otn_postback_"+m).val();
            if(otn_title == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide OTN Title')?>", 'warning');
              return;
            }
            if(otn_postback == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Select an OTN Postback')?>", 'warning');
              return;
            }
          }

          if(template_type == "audio")
          {
            var audio_reply_field = $("#audio_reply_field_"+m).val();
            if(audio_reply_field == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Reply Audio')?>", 'warning');
              return;
            }
          }

          if(template_type == "video")
          {
            var video_reply_field = $("#video_reply_field_"+m).val();
            if(video_reply_field == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Reply Video')?>", 'warning');
              return;          
            }
          }


          if(template_type == "file")
          {
            var file_reply_field = $("#file_reply_field_"+m).val();
            if(file_reply_field == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Reply File')?>", 'warning');
              return;          
            }
          }



          if(template_type == "media")
          {

            // Added by Konok 22-08-2020 , store order quick reply button list in hidden field. 
            var media_postback_sort_order = $("#media_postback_sort_"+m).sortable("toArray");
            $("#media_postback_sort_order_"+m).val(media_postback_sort_order);


            var media_input = $("#media_input_"+m).val();
            if(media_input == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Media URL')?>", 'warning');
              return;          
            }

            var facebook_url = media_input.match(/business.facebook.com/g);
            var facebook_url2 = media_input.match(/www.facebook.com/g);

            if(facebook_url == null && facebook_url2 == null)
            {
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please provide Facebook content URL as Media URL')?>", 'warning');
              return; 
            }

            var submited_media_counter = eval("media_counter_"+m);

            for(var n=1; n<=submited_media_counter; n++)
            {

              var media_text = "#media_text_"+n+"_"+m;
              var media_type = "#media_type_"+n+"_"+m;

              var media_text_check = $(media_text).val();
              if(media_text_check == ''){
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
                return;
              }

              var media_type_check = $(media_type).val();
              if(media_type_check == ''){
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
                return;
              }else if(media_type_check == 'post_back'){

                var media_post_id = "#media_post_id_"+n+"_"+m;
                var media_post_id_check = $(media_post_id).val();
                if(media_post_id_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                  return;
                }

              }else if(media_type_check == 'web_url' || media_type_check == 'web_url_compact' || media_type_check == 'web_url_tall' || media_type_check == 'web_url_full'){
                var media_web_url = "#media_web_url_"+n+"_"+m;
                var media_web_url_check = $(media_web_url).val();
                if(media_web_url_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
                  return;
                }
              }else if(media_type_check == 'phone_number'){
                var media_call_us = "#media_call_us_"+n+"_"+m;
                var media_call_us_check = $(media_call_us).val();
                if(media_call_us_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
                  return;
                }
              }
            }
            
          }



          if(template_type == "quick reply")
          {

            // Added by Konok 22-08-2020 , store order quick reply button list in hidden field. 
            var quick_reply_sort_order = $("#quick_reply_sort_"+m).sortable("toArray");
            $("#quick_reply_sort_order_"+m).val(quick_reply_sort_order);

            var quick_reply_text = $("#quick_reply_text_"+m).val();
            if(quick_reply_text == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Reply Message')?>", 'warning');
              return;
            }
            var submited_quick_reply_button_counter = eval("quick_reply_button_counter_"+m);

            for(var n=1; n<=submited_quick_reply_button_counter; n++)
            {
              var quick_reply_button_text = "#quick_reply_button_text_"+n+"_"+m;
              var quick_reply_post_id = "#quick_reply_post_id_"+n+"_"+m;
              var quick_reply_button_type = "#quick_reply_button_type_"+n+"_"+m;

              quick_reply_button_type = $(quick_reply_button_type).val();

              var quick_reply_post_id_check = $(quick_reply_post_id).val();
              if(quick_reply_button_type == 'post_back')
              {        
                if(quick_reply_post_id_check == '' || quick_reply_post_id_check == null){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                  return;
                }

                var quick_reply_button_text_check = $(quick_reply_button_text).val();

                if(quick_reply_button_text_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
                  return;
                }

              }
              if(quick_reply_button_type == '')
              {
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
                return;
              }
            }    
          }


          if(template_type == "text with buttons")
          {
            
            // Added by Konok 22-08-2020 , store order quick reply button list in hidden field. 
            var text_button_sort_order = $("#text_button_sort_"+m).sortable("toArray");
            $("#text_button_sort_order_"+m).val(text_button_sort_order);


            var text_with_buttons_input = $("#text_with_buttons_input_"+m).val();
            if(text_with_buttons_input == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Reply Message')?>", 'warning');
              return;          
            }

            var submited_text_with_button_counter = eval("text_with_button_counter_"+m);

            for(var n=1; n<=submited_text_with_button_counter; n++)
            {

              var text_with_buttons_text = "#text_with_buttons_text_"+n+"_"+m;
              var text_with_button_type = "#text_with_button_type_"+n+"_"+m;

              var text_with_buttons_text_check = $(text_with_buttons_text).val();
              if(text_with_buttons_text_check == ''){
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
                return;
              }

              var text_with_button_type_check = $(text_with_button_type).val();
              if(text_with_button_type_check == ''){
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
                return;
              }else if(text_with_button_type_check == 'post_back'){

                var text_with_button_post_id = "#text_with_button_post_id_"+n+"_"+m;
                var text_with_button_post_id_check = $(text_with_button_post_id).val();
                if(text_with_button_post_id_check == '' || text_with_button_post_id_check == null){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                  return;
                }

              }else if(text_with_button_type_check == 'web_url' || text_with_button_type_check == 'web_url_compact' || text_with_button_type_check == 'web_url_tall' || text_with_button_type_check == 'web_url_full'){
                var text_with_button_web_url = "#text_with_button_web_url_"+n+"_"+m;
                var text_with_button_web_url_check = $(text_with_button_web_url).val();
                if(text_with_button_web_url_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
                  return;
                }
              }else if(text_with_button_type_check == 'phone_number'){
                var text_with_button_call_us = "#text_with_button_call_us_"+n+"_"+m;
                var text_with_button_call_us_check = $(text_with_button_call_us).val();
                if(text_with_button_call_us_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
                  return;
                }
              }
            }
            
          }

          if(template_type == "generic template")
          {

            // Added by Konok 22-08-2020 , store order Generic reply button list in hidden field. 
            var generic_button_sort_order = $("#generic_button_sort_"+m).sortable("toArray");
            $("#generic_button_sort_order_"+m).val(generic_button_sort_order);


            var generic_template_image = $("#generic_template_image_"+m).val();
            
            var generic_template_title = $("#generic_template_title_"+m).val();
            if(generic_template_title == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please give the title')?>", 'warning');
              return;          
            }

            var generic_template_subtitle = $("#generic_template_subtitle_"+m).val();
           
            var submited_generic_button_counter = eval("generic_with_button_counter_"+m);
            for(var n=1; n<=submited_generic_button_counter; n++)
            {            
              var generic_template_button_text = "#generic_template_button_text_"+n+"_"+m;
              var generic_template_button_type = "#generic_template_button_type_"+n+"_"+m;

              var generic_template_button_text_check = $(generic_template_button_text).val();
              var generic_template_button_type_check = $(generic_template_button_type).val();

              if(generic_template_button_text_check == ''  && generic_template_button_type_check!=''){
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
                return;
              }

              if(generic_template_button_type_check == 'post_back'){

                var generic_template_button_post_id = "#generic_template_button_post_id_"+n+"_"+m;
                var generic_template_button_post_id_check = $(generic_template_button_post_id).val();
                if(generic_template_button_post_id_check == '' || generic_template_button_post_id_check == null){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                  return;
                }

              }else if(generic_template_button_type_check == 'web_url' || generic_template_button_type_check == 'web_url_compact' || generic_template_button_type_check == 'web_url_tall' || generic_template_button_type_check == 'web_url_full'){

                var generic_template_button_web_url = "#generic_template_button_web_url_"+n+"_"+m;
                var generic_template_button_web_url_check = $(generic_template_button_web_url).val();
                if(generic_template_button_web_url_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
                  return;
                }
              }else if(generic_template_button_type_check == 'phone_number'){
                var generic_template_button_call_us = "#generic_template_button_call_us_"+n+"_"+m;
                var generic_template_button_call_us_check = $(generic_template_button_call_us).val();
                if(generic_template_button_call_us_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
                  return;
                }
              }
            }
            
          }


          if(template_type == "carousel")
          {

            // Added by Konok 22-08-2020 , store order Generic reply button list in hidden field. 
            var carousel_reply_sort_order = $("#carousel_reply_sort_"+m).sortable("toArray");
            $("#carousel_reply_sort_order_"+m).val(carousel_reply_sort_order);


            var submited_carousel_template_counter = eval("carousel_template_counter_"+m);
            for(var n=1; n<=submited_carousel_template_counter; n++)
            {
              var carousel_image = "#carousel_image_"+n+"_"+m;
              var carousel_image_check = $(carousel_image).val();
             
              var carousel_title = "#carousel_title_"+n+"_"+m;
              var carousel_title_check = $(carousel_title).val();
              if(carousel_title_check == ''){
                swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide carousel title')?>", 'warning');
                return;
              }

              var carousel_subtitle = "#carousel_subtitle_"+n+"_"+m;
              var carousel_subtitle_check = $(carousel_subtitle).val();
              
              var carousel_image_destination_link = "#carousel_image_destination_link_"+n+"_"+m;
              var carousel_image_destination_link_check = $(carousel_image_destination_link).val();
              
            }

            <?php for($j=1; $j<=10; $j++) : ?>

              var carousel_button_sort_order = $("#carousel_button_sort_<?php echo $j; ?>_"+m).sortable("toArray");
              $("#carousel_button_sort_order_<?php echo $j; ?>_"+m).val(carousel_button_sort_order);


              var submited_carousel_add_button_counter = eval("carousel_add_button_counter_<?php echo $j; ?>_"+m);
              for(var n=1; n<=submited_carousel_add_button_counter; n++)
              {
                var carousel_button_text = "#carousel_button_text_<?php echo $j; ?>_"+n+"_"+m;
                var carousel_button_type = "#carousel_button_type_<?php echo $j; ?>_"+n+"_"+m;

                if($(carousel_button_type).parent().parent().parent().is(":visible"))
                {
                  var carousel_button_text_check = $(carousel_button_text).val();
                  var carousel_button_type_check = $(carousel_button_type).val();

                  if(carousel_button_text_check == ''  && carousel_button_type_check!=""){
                    swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
                    return;
                  }

                  if(carousel_button_type_check == 'post_back'){

                    var carousel_button_post_id = "#carousel_button_post_id_<?php echo $j;?>_"+n+"_"+m;
                    var carousel_button_post_id_check = $(carousel_button_post_id).val();
                    if(carousel_button_post_id_check == '' || carousel_button_post_id_check == null){
                      swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                      return;
                    }
                  }else if(carousel_button_type_check == 'web_url' || carousel_button_type_check == 'web_url_compact' || carousel_button_type_check == 'web_url_full' || carousel_button_type_check == 'web_url_tall'){

                    var carousel_button_web_url = "#carousel_button_web_url_<?php echo $j;?>_"+n+"_"+m;
                    var carousel_button_web_url_check = $(carousel_button_web_url).val();
                    if(carousel_button_web_url_check == ''){
                      swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
                      return;
                    }
                  }else if(carousel_button_type_check == 'phone_number'){
                    var carousel_button_call_us = "#carousel_button_call_us_<?php echo $j;?>_"+n+"_"+m;
                    var carousel_button_call_us_check = $(carousel_button_call_us).val();
                    if(carousel_button_call_us_check == ''){
                      swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
                      return;
                    }
                  }
                }              
                

              }
            <?php endfor; ?>

          }


      }

      
      $(this).addClass('btn-progress'); 
      

      $("input:not([type=hidden])").each(function(){
        if($(this).is(":visible") == false)
          $(this).attr("disabled","disabled");
      });


      var queryString = new FormData($("#messenger_bot_form")[0]);
        $.ajax({
          type:'POST' ,
          url: base_url+"messenger_bot/ajax_generate_messenger_bot",
          data: queryString,
          dataType : 'JSON',
          // async: false,
          cache: false,
          contentType: false,
          processData: false,
          context: this,
          success:function(response){
            $(this).removeClass('btn-progress');
            swal('<?php echo $this->lang->line("Reply Created"); ?>', "<?php echo $this->lang->line('Bot reply has been added successfully.'); ?>", 'success').then((value) => {
              location.reload();
            });            
          },
          error:function(response){
          	var span = document.createElement("span");
          	span.innerHTML = response.responseText;
          	swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
          }

        });

    });


    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

    
    $(document).on('click','#add_bot_settings',function(e){
       e.preventDefault();
       $("#add_bot_settings_modal").removeClass('hidden');
       $(".box.box-widget.widget-user-2").hide();
       $("#bot_success").hide();
       $("#bot_list_datatable").hide();
       // $('html, body').animate({scrollTop: $("#add_bot_settings_modal").offset().top}, 2000);
    });

    $(document).on('click','.lead_first_name',function(){
    
        var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
        
          var lastIndex = textAreaTxt.lastIndexOf("<br>");   
          var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
          lastTag=lastTag.trim(lastTag);

          if(lastTag=="<br>")
            textAreaTxt = textAreaTxt.substring(0, lastIndex); 
        
        
        
        var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
        var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
        $(this).parent().next().next().next().children('.emojionearea-editor').click();
        
        
    });

    $(document).on('click','.lead_last_name',function(){
    
      var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();

        var lastIndex = textAreaTxt.lastIndexOf("<br>");   
          var lastTag = textAreaTxt.substr(textAreaTxt.length - 4); 
          lastTag=lastTag.trim(lastTag);

          if(lastTag=="<br>")
            textAreaTxt = textAreaTxt.substring(0, lastIndex); 
          

      var txtToAdd = " #LEAD_USER_LAST_NAME# ";
      var new_text = textAreaTxt + txtToAdd;
      $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
      $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
         
         
    });

    $(document).on('click','.full_lead_first_name',function(){ 
       
       var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
       
       var lastIndex = textAreaTxt.lastIndexOf("<br>");
       
        if(lastIndex!='-1')
          textAreaTxt = textAreaTxt.substring(0, lastIndex);
         
        var txtToAdd = " #LEAD_FULL_NAME# ";
        var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
        $(this).parent().next().next().next().children('.emojionearea-editor').click();   
    });

    $(document).on('click','.full_lead_tag_name',function() {

          var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
        var lastIndex = textAreaTxt.lastIndexOf("<br>");
       
        if(lastIndex!='-1')
          textAreaTxt = textAreaTxt.substring(0, lastIndex);
         
        var txtToAdd = " #TAG_USER# ";
        var new_text = textAreaTxt + txtToAdd;
        $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
        $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
        
    });

    $(document).on('click','.add_template',function(e){
        e.preventDefault();
        var current_id=$(this).prev().attr("id");
        var page_id="<?php echo $page_info['id'];?>";
        if(page_id=="")
        {
          swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
          return false;
        }
        $("#add_template_modal").attr("current_id",current_id);
        $("#add_template_modal").modal();
    });

    $(document).on('click','.ref_template',function(e){
      e.preventDefault();
      var current_val=$(this).prev().prev().val();
      var current_id=$(this).prev().prev().attr("id");
      var page_id="<?php echo $page_info['id'];?>";
       if(page_id=="")
       {
         swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_ig_postback",
         data: {page_id:page_id},
         success:function(response){
           $("#"+current_id).html(response).val(current_val);
         }
       });
    });

    $('#add_template_modal').on('hidden.bs.modal', function (e) { 
      var current_id=$("#add_template_modal").attr("current_id");
      var page_id="<?php echo $page_info['id'];?>";
       if(page_id=="")
       {
         swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_ig_postback",
         data: {page_id:page_id},
         success:function(response){
           $("#"+current_id).html(response);
         }
       });
    });


    $(document).on('click', '.bot_status_btn', function(event) {
        event.preventDefault();
        
        let table_id = $(this).attr('table_id');

        swal({
            title: 'Warning',
            text: '<?php echo $this->lang->line("Do you really want to change this state?"); ?>',
            icon: 'warning',
            buttons: true,
            dangerMode: true
        }).then(willChangeState => {
            if (willChangeState) {
                $.ajax({
                  url: '<?php echo base_url("messenger_bot/change_bot_state"); ?>',
                  type: 'POST',
                  dataType: 'json',
                  data: {table_id: table_id},
                  success: function (response) {

                      if (response.status == 'success') {
                          iziToast.success({
                              title: '<?php echo $this->lang->line("Success"); ?>',
                              message: response.message,
                              position: 'bottomRight'
                          });
                      } else if (response.status == 'error') {
                          iziToast.error({
                            title: '<?php echo $this->lang->line("Error"); ?>',
                            message: response.message,
                            position: 'bottomRight'
                          });
                      }

                      window.location.reload();
                  }
                });
                
            }
        });
    });


  }); 

 function refresh_template(is_from_add_button='1')
  {
    var page_id="<?php echo $page_info['id'];?>";
    if(page_id=="")
    {
      alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('Please select a page first')?>",function(){});
      return false;
    }
    $.ajax({
      type:'POST' ,
      url: base_url+"messenger_bot/get_ig_postback",
      data: {page_id:page_id,order_by:"template_name",is_from_add_button:is_from_add_button},
      success:function(response){
        $(".push_postback").html(response);
      }
    });
  }

</script>


<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-full">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body"> 
        <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close & Refresh List");?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-info"></i> <?php echo $this->lang->line('campaign error'); ?></h4>
      </div>
      <div class="modal-body">
        <div class="alert text-center alert-warning" id="error_modal_content">
          
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="media_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("How to get media URL?"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div>
          <p><?php echo $this->lang->line("To get the Facebook URL for an image or video, do the following:"); ?></p>
          <ul>
            <li><?php echo $this->lang->line("Click the image or video thumbnail to open the full-size view");?>.</li>
            <li><?php echo $this->lang->line("Copy the URL from your browser's address bar.<");?>/li>
          </ul>
          <p><?php echo $this->lang->line("Facebook URLs should be in the following base format:");?></p>
          <div class="table-responsive2">
            <table class='table table-bordered table-hover table-striped' >
             <thead>
               <tr>
                 <th><?php echo $this->lang->line("Media Type");?></th>
                 <th><?php echo $this->lang->line("Media Source");?></th>
                 <th><?php echo $this->lang->line("URL Format");?></th>
               </tr>
             </thead>
             <thead>
               <tr>
                 <td><?php echo $this->lang->line("Video");?></td>
                 <td><?php echo $this->lang->line("Facebook Page");?></td>
                 <td>https://business.facebook.com/<b>PAGE_NAME</b>/videos/<b>NUMERIC_ID</b></td>
               </tr>
               <tr>
                 <td><?php echo $this->lang->line("Video");?></td>
                 <td><?php echo $this->lang->line("Facebook Account");?></td>
                 <td>https://www.facebook.com/<b>USERNAME</b>/videos/<b>NUMERIC_ID</b>/</td>
               </tr>
               <tr>
                 <td><?php echo $this->lang->line("Image");?></td>
                 <td><?php echo $this->lang->line("Facebook Page");?></td>
                 <td>https://business.facebook.com/<b>PAGE_NAME</b>/photos/<b>NUMERIC_ID</b></td>
               </tr>
               <tr>
                 <td><?php echo $this->lang->line("Image");?></td>
                 <td><?php echo $this->lang->line("Facebook Account");?></td>
                 <td>https://www.facebook.com/photo.php?fbid=<b>NUMERIC_ID</b></td>
               </tr>
             </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>