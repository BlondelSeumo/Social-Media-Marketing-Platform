<?php
  $redirect_url = site_url('messenger_bot/bot_settings/');
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

  $THEMECOLORCODE = "#607D8B";
?>

<style type="text/css">
  .item_remove
  {
    margin-top: 12px; 
    margin-left: -20px;
    font-size: 20px !important;
    cursor: pointer !important;
    font-weight: 200 !important;
  }
  /*.remove_reply
  {
    margin:10px 10px 5px 0;
    font-size: 25px !important;
    cursor: pointer !important;
    font-weight: 200 !important;
  }*/
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

  .emojionearea, .emojionearea.form-control
  {
    height: 120px !important;
  }

  .emojionearea.small-height
  {
    height: 120px !important;
  }
  .button_border
  {
    padding: 35px 15px 15px 15px; 
    margin: 5px 0px 0px; 
    border: 1px dashed #ccc;
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
 
  <?php if($iframe=='1') echo '
  .card-primary .card-body,.card-primary .card-header,.card-primary .card-footer{padding:15px;}
  .card-secondary .card-body,.card-secondary .card-header,.card-secondary .card-footer{padding:12px;}';
  ?>
</style>

<?php if($iframe !='1') : ?>
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add a PostBack Template");?></h1>
    <div class="section-header-breadcrumb">
       <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot/template_manager'); ?>"><?php echo $this->lang->line('Post-back Manager'); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>
<?php endif; ?>

  <div class="card no_shadow ">
    <div class="card-body <?php if($iframe=='1') echo 'padding-0';?>">

      <div class="row">
        <div class="<?php if($is_iframe=="1") echo 'col-12'; else echo 'col-12 col-lg-9';?>">
          <form action="#" method="post" id="messenger_bot_form" style="padding-left: 0;">
            <div class="text-left" style="display: none;">
              <?php foreach ($keyword_types as $key => $value) { ?>
                  <input type="radio" name="keyword_type" value="<?php echo $value; ?>" id="keyword_type_<?php echo $value;?>" class="css-checkbox keyword_type"/><label for="keyword_type_<?php echo $value;?>" class="css-label radGroup2"><?php echo $this->lang->line($value);?></label>
                   &nbsp;&nbsp;              
              <?php } ?>  
            </div>


            <div class="row"> 
              <div class="<?php if($default_page == '') echo 'col-12 col-sm-6'; else echo 'col-12'; ?>"> 
                <div class="form-group">
                  <label><?php echo $this->lang->line("Template Name"); ?></label>
                  <input type="text" name="bot_name" id="bot_name" class="form-control">
                </div>       
              </div> 
              <?php if($default_page != '') : ?>
                <input type="hidden" name="page_table_id" id="page_table_id" value="<?php echo $default_page; ?>">
              <?php else : ?>
              <div class="col-12 col-sm-6"> 
                <div class="form-group">
                  <label><?php echo $this->lang->line("Choose a Page"); ?></label>
                  <?php 
                    $page_list[''] = $this->lang->line("Please select a page");
                    echo form_dropdown('page_table_id',$page_list,$default_page,'id="page_table_id" class="form-control select2"'); 
                  ?>
                </div>       
              </div>
              <?php endif; ?>
            </div>

            <div class="row"> 
              <?php if($default_child_postback_id == '') : ?>
              <div class="col-12 col-sm-6">
                <div class="form-group">
                  <label><?php echo $this->lang->line("PostBack Type"); ?></label>
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-6">
                      <label class="custom-switch">
                        <input type="radio" name="postback_type" value="parent" id="parent_postback" class="custom-switch-input" checked>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">Parent</span>
                      </label>
                    </div>
                    <div class="col-6">
                      <label class="custom-switch">
                        <input type="radio" name="postback_type" value="child" id="child_postback" class="custom-switch-input">
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">Child</span></label>
                    </div>
                  </div>
                </div>
              </div>              
              
              <div class="col-12 col-sm-6"> 
                <div class="form-group" id="postback_section">
                  <label>
                    <?php echo $this->lang->line("PostBack id"); ?>
                    <a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>
                  </label>
                  <input type="text" name="template_postback_id" id="template_postback_id" class="form-control">
                </div>       
              </div>  
              <?php else : ?>
                  <input type="hidden" name="template_postback_id" id="template_postback_id" value="<?php echo urldecode($default_child_postback_id); ?>">
                  <input type="hidden" name="postback_type" value="child" id="child_postback">
              <?php endif; ?>
            </div>

            

            <?php 
            $first_col= "col-12 col-sm-6";
            if(!$this->is_drip_campaigner_exist && !$this->is_sms_email_drip_campaigner_exist)  $first_col="col-12";              
            $popover='<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Choose Labels").'" data-content="'.$this->lang->line("If you choose labels, then when user click on this PostBack they will be added in those labels, that will help you to segment your leads & broadcasting from Messenger Broadcaster. If you don't want to add labels for this PostBack , then just keep it blank as it is.").'"><i class="fa fa-info-circle"></i> </a>';
            echo '<div class="row">
              <div class="'.$first_col.'"> 
                  <div class="form-group">
                    <label style="width:100%" class="show_label hidden">
                    '.$this->lang->line("Choose Labels").' '.$popover.'
                    <a class="blue float-right pointer" page_id_for_label="" id="create_label_postback"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Create Label").'</a>  
                    </label>
                    <span id="first_dropdown"></span>                                  
                  </div>       
              </div>';

              if($this->is_drip_campaigner_exist || $this->is_sms_email_drip_campaigner_exist)
              {
                $popover2='<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Choose Sequence Campaign").'" data-content="'.$this->lang->line("Choose any drip or sequence campaign to set when user click on this postback button. Keep it blank if you don't want to set.").'"><i class="fa fa-info-circle"></i> </a>';
                 echo '
                  <div class="col-12 col-sm-6 hidden dropdown_con"> 
                      <div class="form-group">
                        <label style="width:100%">
                        '.$this->lang->line("Choose Sequence Campaigns").' '.$popover2.'
                        </label>
                        <span id="dripcampaign_dropdown"></span>                                  
                      </div>       
                  </div>';
              }
              echo '</div>';                  
            ?>                
            
           
            <div class="row" id="keywords_div" style="display: none;"> 
              <div class="col-12">              
                <div class="form-group">
                  <label><?php echo $this->lang->line("Please provide your keywords in comma separated"); ?></label>
                  <textarea class="form-control"  name="keywords_list" id="keywords_list"></textarea>
                </div>        
              </div>  
            </div>
    
    
            <div class="row" id="postback_div" style="display: none;"> 
              <div class="col-12">              
                <div class="form-group">
                  <label><?php echo $this->lang->line("Please select your postback id"); ?></label>
                  <select multiple=""  class="form-control select2" id="keywordtype_postback_id" name="keywordtype_postback_id[]">
                  <?php
                      $postback_id_array = array();
                      foreach($postback_ids as $value)
                      {
                        $postback_id_array[$value['page_id']][] = strtoupper($value['postback_id']);
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



    
            <?php for($k=1;$k<=6;$k++){ ?>

              <div class="card card-primary" id="multiple_template_div_<?php echo $k; ?>" <?php if($k != 1) echo "style='display : none;'"; ?> >
                <div class="card-header">
                  <h4 class="full_width">
                    <?php echo $this->lang->line('Reply')." ".$k; ?>
                    <?php if($k != 1) : ?>
                      <i class="fa fa-times-circle remove_reply float-right red" row_id="multiple_template_div_<?php echo $k; ?>" counter_variable="" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                    <?php endif; ?>
                  </h4>
                </div>
                <div class="card-body">
          

                    <div>

                      <br/> 
                      <div class="input-group">                            
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
                      <br/>

                      <div class="row" id="delay_and_typing_on_<?php echo $k; ?>">
                        <div class="col-12 col-sm-6">
                          <div class="row">
                            <div class="<?php if($iframe == '1') echo 'col-7'; else echo 'col-5' ?>"><label for="" style="margin-top: 8px; color: #34395e; font-size: 14px;"><?php echo $this->lang->line('Typing on display :'); ?></label></div>
                            <div class="<?php if($iframe == '1') echo 'col-5'; else echo 'col-7' ?>">
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
                      <br/>

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
                            <select class="form-control push_otn_postback select2" id="otn_postback_<?php echo $k; ?>" name="otn_postback_<?php echo $k; ?>">
                              <option value=""></option>
                            </select>
                          </div>        
                        </div> 
                      </div>

                      <div class="row" id="text_div_<?php echo $k; ?>"> 
                        <div class="col-12">              
                          <div class="form-group">
                            <label><?php echo $this->lang->line("Please provide your reply message"); ?>
                              <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Spintax"); ?>" data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                            </label>

                            <span class='float-right'> 
                              <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                            </span>
                            <span class='float-right'> 
                              <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                            </span> 
                
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
                                <select class="form-control select2 media_type_class" id="media_type_<?php echo $i; ?>_<?php echo $k; ?>" name="media_type_<?php echo $i; ?>_<?php echo $k; ?>">
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
                                  
                                  <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("Unsubscribe"); ?></option>
                                  <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("Re-subscribe"); ?></option>
                                  
                                  <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                                  <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                </select>
                              </div>
                            </div>
                            <div class="col-10 col-sm-3">
                              <div class="form-group" id="media_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                <label>
                                	<?php echo $this->lang->line("PostBack id"); ?>
                                	<a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>
                                </label>
                                <select class="form-control push_postback select2"  name="media_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="media_post_id_<?php echo $i; ?>_<?php echo $k; ?>">
                                  <option value=""></option>
                                </select>

                                <a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                                <a href="" class="ref_template float-right" page_id_ref_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                
                              </div>
                              <div class="form-group" id="media_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                <label><?php echo $this->lang->line("Web Url"); ?></label>
                                <input type="text" class="form-control" name="media_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="media_web_url_<?php echo $i; ?>_<?php echo $k; ?>">
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

                            <span class='float-right'> 
                              <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                            </span>
                            <span class='float-right'> 
                              <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                            </span> 
                  
                             <div class="clearfix"></div>
                            <textarea class="form-control" name="quick_reply_text_<?php echo $k; ?>" id="quick_reply_text_<?php echo $k; ?>"></textarea>
                          </div> 

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
                                <select class="form-control select2 quick_reply_button_type_class" id="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                                  <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                                  <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                                  <option value="phone_number"><?php echo $this->lang->line("User's Phone Number"); ?></option>
                                  <option value="user_email"><?php echo $this->lang->line("User's E-mail Address"); ?></option>
                                  <!-- <option value="location"><?php echo $this->lang->line("User's location"); ?></option> -->
                                </select>
                              </div>
                            </div>
                            <div class="col-10 col-sm-3">
                              <div class="form-group" id="quick_reply_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                <label>
                                	<?php echo $this->lang->line("PostBack id"); ?>
                                	<a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>	
                                </label>

                                <select class="form-control push_postback select2"  name="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>">
                                  <option value=""></option>
                                </select>

                                <a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                                <a href="" class="ref_template float-right" page_id_ref_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                              
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

                            <span class='float-right'> 
                              <a title="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_last_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                            </span>
                            <span class='float-right'> 
                              <a title="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                            </span> 
                
                             <div class="clearfix"></div>
                            <textarea class="form-control"  name="text_with_buttons_input_<?php echo $k; ?>" id="text_with_buttons_input_<?php echo $k; ?>"></textarea>
                          </div> 

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
                                <select class="form-control select2 text_with_button_type_class" id="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
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
                                  
                                  <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("Unsubscribe"); ?></option>
                                  <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("Re-subscribe"); ?></option>
                                  
                                  <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                                  <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                </select>
                              </div>
                            </div>
                            <div class="col-10 col-sm-3">
                              <div class="form-group" id="text_with_button_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                <label>
                                	<?php echo $this->lang->line("PostBack id"); ?>
                                	<a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>
                                </label>

                                <select class="form-control push_postback select2"  name="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>">
                                  <option value=""></option>
                                </select>

                                <a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                                <a href="" class="ref_template float-right" page_id_ref_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                
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
                                      <select class="form-control select2 generic_template_button_type_class" id="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
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
                                        
                                        <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("Unsubscribe"); ?></option>
                                        <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("Re-subscribe"); ?></option>
                                        
                                        <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                                        <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-10 col-sm-3">
                                    <div class="form-group" id="generic_template_button_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                                      <label>
                                      	<?php echo $this->lang->line("PostBack id"); ?>
                                      	<a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>
                                      </label>

                                      <select class="form-control push_postback select2"  name="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>">
                                        <option value=""></option>
                                      </select>

                                      <a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                                      <a href="" class="ref_template float-right" page_id_ref_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                    
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

                                <div class="row clearfix">
                                  <div class="col-12 text-center"><button class="btn btn-outline-primary float-right no_radius btn-xs" id="generic_template_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                                </div>

                            </div> <!-- end of card body -->
                          </div>
                        </div>
                      </div>


                      <div class="row" id="carousel_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
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
                                        <select class="form-control select2 carousel_button_type_class" id="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" name="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
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
                                          
                                          <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("Unsubscribe"); ?></option>
                                          <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("Re-subscribe"); ?></option>
                                          
                                          <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                                          <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="col-10 col-sm-3">
                                      <div class="form-group" id="carousel_button_postid_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;">
                                        <label>
                                        	<?php echo $this->lang->line("PostBack id"); ?>
                                        	<a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>
                                        </label>

                                        <select class="form-control push_postback select2"  name="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                                          <option value=""></option>
                                        </select>

                                        <a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                                        <a href="" class="ref_template float-right" page_id_ref_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                                       
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
                                <?php endfor;?>
                                <div class="row clearfix" style="padding-bottom: 10px;">
                                  <div class="col-12 text-center"><button class="btn btn-outline-primary float-right no_radius btn-xs" id="carousel_add_button_<?php echo $j; ?>_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                                </div>
                              </div> <!-- end of card body -->
                            </div>
                          </div>

                        <?php endfor; ?>

                        <div class="col-12 clearfix">
                          <button id="carousel_template_add_button_<?php echo $k; ?>" class="btn btn-sm btn-outline-primary float-right no_radius"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more template");?></button>
                        </div>

                      </div>


                      <div class="row" id="list_div_<?php echo $k; ?>" style="display: none;">  
                        <div class="col-12">
                          <div class="row" id="list_with_buttons_row">
                            <div class="col-4">
                              <div class="form-group">
                                <label><?php echo $this->lang->line("bottom button text"); ?></label>
                                <input type="text" class="form-control"  name="list_with_buttons_text_<?php echo $k; ?>" id="list_with_buttons_text_<?php echo $k; ?>">
                              </div>
                            </div>
                            <div class="col-4">
                              <div class="form-group">
                                <label><?php echo $this->lang->line("bottom button type"); ?></label>
                                <select class="form-control select2 list_with_button_type_class" id="list_with_button_type_<?php echo $k; ?>" name="list_with_button_type_<?php echo $k; ?>">
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
                                  
                                  <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("Unsubscribe"); ?></option>
                                  <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("Re-subscribe"); ?></option>
                                  
                                  <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                                  <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                                </select>
                              </div>
                            </div>
                            <div class="col-4">
                              <div class="form-group" id="list_with_button_postid_div_<?php echo $k; ?>" style="display: none;">
                                <label>
                                	<?php echo $this->lang->line("PostBack id"); ?>
                                	<a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class='fa fa-info-circle'></i> </a>
                                </label>

                                <select class="form-control push_postback select2"  name="list_with_button_post_id_<?php echo $k; ?>" id="list_with_button_post_id_<?php echo $k; ?>">
                                  <option value=""></option>
                                </select>

                                <a href="" class="add_template float-left" page_id_add_postback=""><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add");?></a>
                                <a href="" class="ref_template float-right" page_id_ref_postback=""><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                              </div>
                              <div class="form-group" id="list_with_button_web_url_div_<?php echo $k; ?>" style="display: none;">
                                <label><?php echo $this->lang->line("Web Url"); ?></label>
                                <input type="text" class="form-control"  name="list_with_button_web_url_<?php echo $k; ?>" id="list_with_button_web_url_<?php echo $k; ?>">
                              </div>
                              <div class="form-group" id="list_with_button_call_us_div_<?php echo $k; ?>" style="display: none;">
                                <label><?php echo $this->lang->line("Phone Number"); ?></label>
                                <input type="text" class="form-control"  name="list_with_button_call_us_<?php echo $k; ?>" id="list_with_button_call_us_<?php echo $k; ?>">
                              </div>
                            </div>
                          </div>
                        </div>

                        <?php for ($j=1; $j <=4 ; $j++) : ?>
                          <div class="col-12" id="list_div_<?php echo $j; ?>_<?php echo $k; ?>"  style="display: none;padding-top: 20px;"> 
                            <div style="border: 1px dashed #ccc; background:#fcfcfc;padding:10px 15px;">
                              <div class="row">
                                <div class="col-6">
                                  <div class="form-group">
                                    <label><?php echo $this->lang->line("Please provide your reply image"); ?></label>
                                    <input type="hidden" class="form-control"  name="list_image_<?php echo $j; ?>_<?php echo $k; ?>" id="list_image_<?php echo $j; ?>_<?php echo $k; ?>" />
                                    <div id="list_imageupload_<?php echo $j; ?>_<?php echo $k; ?>"><?php echo $this->lang->line('upload'); ?></div>
                                  </div>                         
                                </div>
                                <div class="col-6">
                                  <div class="form-group">
                                    <label><?php echo $this->lang->line("image click destination link"); ?></label>
                                    <input type="text" class="form-control"  name="list_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" id="list_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" />
                                  </div> 
                                </div>                      
                              </div>

                              <div class="row">
                                <div class="col-6">
                                  <div class="form-group">
                                    <label><?php echo $this->lang->line("title"); ?></label>
                                    <input type="text" class="form-control"  name="list_title_<?php echo $j; ?>_<?php echo $k; ?>" id="list_title_<?php echo $j; ?>_<?php echo $k; ?>" />
                                  </div>
                                </div>  
                                <div class="col-6">
                                  <div class="form-group">
                                    <label><?php echo $this->lang->line("sub-title"); ?></label>
                                    <input type="text" class="form-control"  name="list_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" id="list_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" />
                                  </div>
                                </div>  
                              </div>
                            </div>
                          </div>
                        <?php endfor; ?>

                        <div class="col-12 clearfix">
                          <button id="list_template_add_button_<?php echo $k; ?>" class="btn btn-sm btn-outline-primary float-right no_radius"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more template");?></button>
                        </div>

                      </div>


                    </div>




                </div> <!-- end of card body  -->
              </div>   
            <?php }  ?>
            <div class="row">
              <div class="col-12 clearfix">
                <button id="multiple_template_add_button" class="btn btn-outline-primary float-right no_radius" ><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add more reply'); ?></button>
              </div>
            </div>
            <br/><br/>
            <div class="row">
              <div class="col-6">
                <button id="submit" class="btn btn-lg btn-primary"><i class="fa fa-send"></i> <?php echo $this->lang->line('submit'); ?></button>
              </div>
              <?php if($iframe != '1') : ?>
              <div class="col-6">
                <a class="btn btn-lg btn-secondary float-right" href="<?php echo base_url("messenger_bot/template_manager"); ?>"><i class="fas fa-times"></i> <?php echo $this->lang->line('Back'); ?></a>
              </div>
              <?php endif; ?>
            </div>



          </form>

        </div>


        <div class="d-none d-lg-block col-lg-3 img_holder <?php if($is_iframe=="1") echo 'hidden';?>" style="" >
          <div id="text_preview_div" style="">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/text.png')) echo site_url()."assets/images/preview/text.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/text.png"; ?>" class="img-rounded" alt="Text Preview"></center>
          </div>

          <div id="image_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/image.png')) echo site_url()."assets/images/preview/image.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/image.png"; ?>" class="img-rounded" alt="Image Preview"></center>
          </div>

          <div id="audio_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/mp3.png')) echo site_url()."assets/images/preview/mp3.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/mp3.png"; ?>" class="img-rounded" alt="Audio Preview"></center>
          </div>

          <div id="video_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/video.png')) echo site_url()."assets/images/preview/video.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/video.png"; ?>" class="img-rounded" alt="Video Preview"></center>
          </div>

          <div id="file_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/file.png')) echo site_url()."assets/images/preview/file.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/file.png"; ?>" class="img-rounded" alt="File Preview"></center>
          </div>

          <div id="quick_reply_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/quick_reply.png')) echo site_url()."assets/images/preview/quick_reply.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/quick_reply.png"; ?>" class="img-rounded" alt="Quick Reply Preview"></center>
          </div>

          <div id="text_with_buttons_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/button.png')) echo site_url()."assets/images/preview/button.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/button.png"; ?>" class="img-rounded" alt="Text With Buttons Preview"></center>
          </div>

          <div id="generic_template_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/generic.png')) echo site_url()."assets/images/preview/generic.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/generic.png"; ?>" class="img-rounded" alt="Generic Template Preview"></center>
          </div>

          <div id="carousel_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/carousel.png')) echo site_url()."assets/images/preview/carousel.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/carousel.png"; ?>" class="img-rounded" alt="Carousel Template Preview"></center>
          </div>

          <div id="list_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/list.png')) echo site_url()."assets/images/preview/list.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/list.png"; ?>" class="img-rounded" alt="List Template Preview"></center>
          </div>

          <div id="media_preview_div" style="display: none;">
            <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/media.png')) echo site_url()."assets/images/preview/media.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/media.png"; ?>" class="img-rounded" alt="Media Template Preview"></center>
          </div>

        </div>

      </div>


    </div>
  </div>

<?php if($iframe !='1') : ?>
</section>
<?php endif; ?>



<?php 
  $somethingwentwrong = $this->lang->line("something went wrong.");  
  $doyoureallywanttodeletethisbot = $this->lang->line("do you really want to delete this bot?");
  $areyousure=$this->lang->line("are you sure");
?>

<script type="text/javascript">
  $(document).ready(function(e){
   
    $(".push_postback").select2({
      tags: true,
      width: '100%'
    });

    $(".push_otn_postback").select2({
      tags: true,
      width: '100%'
    });

    $(".quick_reply_button_type_class, .media_type_class, .text_with_button_type_class, .generic_template_button_type_class, .carousel_button_type_class, .list_with_button_type_class").select2({
      width: '100%'
    });

    // $(".form-control-new").select2({
    //   width: '81.35%'
    // });

    $(document).on('click', '.bs-dropdown-to-select-group .dropdown-menu li', function( event ) {
      var $target = $( event.currentTarget );
      $target.closest('.bs-dropdown-to-select-group')
      .find('[data-bind="bs-drp-sel-value"]').val($target.attr('data-value'))
      .end()
      .children('.dropdown-toggle').dropdown('toggle');
      $target.closest('.bs-dropdown-to-select-group')
      .find('[data-bind="bs-drp-sel-label"]').text($target.context.textContent);
      return false;
    });

    var default_page = "<?php echo $default_page; ?>";
    if(default_page != '') page_change_action();


    $(document).on('change','#page_table_id',function(){  
      page_change_action();
    });


    $(document).on('click','.media_template_modal',function(){
      $("#media_template_modal").modal();
    });


    // create an new label and put inside label list
    $(document).on('click','#create_label_postback',function(e){
      e.preventDefault();

      var page_id=$(this).attr('page_id_for_label');

      swal("<?php echo $this->lang->line('Label Name'); ?>", {
        content: "input",
        button: {text: "<?php echo $this->lang->line('New Label'); ?>"},
      })
      .then((value) => {
        var label_name = `${value}`;
        if(label_name!="" && label_name!='null')
        {
          $("#save_changes").addClass("btn-progress");
          $.ajax({
            context: this,
            type:'POST',
            dataType:'JSON',
            url:"<?php echo site_url();?>home/common_create_label_and_assign",
            data:{page_id:page_id,label_name:label_name},
            success:function(response){

               $("#save_changes").removeClass("btn-progress");

               if(response.error) {
                  var span = document.createElement("span");
                  span.innerHTML = response.error;

                  swal({
                    icon: 'error',
                    title: '<?php echo $this->lang->line('Error'); ?>',
                    content:span,
                  });

               } else {
                  var newOption = new Option(response.text, response.id, true, true);
                  $('#label_ids').append(newOption).trigger('change');
                }
            }
          });
        }
      });

    });


    // getting postback list and making iframe
    $('#add_template_modal').on('shown.bs.modal',function(){ 
      var page_id=$(".add_template").attr("page_id_add_postback");
      var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id;
      $(this).find('iframe').attr('src',iframe_link); 
    });   

    if(default_page == '') refresh_template("0");

    $("#loader").addClass('hidden');
    // getting postback list and making iframe
    // 
    $(document).on('click','.add_template',function(e){
        e.preventDefault();
        var current_id=$(this).prev().prev().attr("id");
        var page_id=$(this).attr("page_id_add_postback");
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
      var current_val=$(this).prev().prev().prev().val();
      var current_id=$(this).prev().prev().prev().attr("id");
      var page_id=$(this).attr("page_id_ref_postback");
       if(page_id=="")
       {
         swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_postback",
         data: {page_id:page_id},
         success:function(response){
           $("#"+current_id).html(response).val(current_val);
         }
       });
    });

    $('#add_template_modal').on('hidden.bs.modal', function (e) { 
      var current_id=$("#add_template_modal").attr("current_id");
      var page_id=$(".add_template").attr("page_id_add_postback");
       if(page_id=="")
       {
         swal('<?php echo $this->lang->line("Error"); ?>', "<?php echo $this->lang->line('Please select a page first')?>", 'error');
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_postback",
         data: {page_id:page_id},
         success:function(response){
           $("#"+current_id).html(response);
         }
       });
    });


    function refresh_template(is_from_add_button='1')
    {
       var page_id=$(this).attr("page_id_ref_postback");
       if(page_id=="")
       {
         alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('Please select a page first')?>",function(){});
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_postback",
         data: {page_id:page_id,order_by:"template_name",is_from_add_button:is_from_add_button},
         success:function(response){
           $(".push_postback").html(response);
         }
       });
     }

  });

  function page_change_action()
  {
    var page_id=$('#page_table_id').val();
    if(page_id=='') return;

    $(".add_template").attr("page_id_add_postback",page_id);
    $(".ref_template").attr("page_id_ref_postback",page_id);

    $.ajax({
      type:'POST' ,
      url: base_url+'messenger_bot/get_postback_dropdown_child',
      data: {page_auto_id:page_id},
      dataType : 'JSON',
      success:function(response){  
        $(".push_postback").html(response.dropdown);  
      }
    });

    $.ajax({
      type:'POST' ,
      url: base_url+'messenger_bot/get_otn_postback_dropdown',
      data: {page_auto_id:page_id},
      dataType : 'JSON',
      success:function(response){  
        $(".push_otn_postback").html(response.dropdown);  
      }
    });

    if($("input[name=postback_type]:checked").val()=="child")
    {          
      $.ajax({
        type:'POST' ,
        url: base_url+'messenger_bot/get_postback_dropdown',
        data: {page_auto_id:page_id},
        dataType : 'JSON',
        success:function(response){  
          if(response.first_dropdown == ''){
            swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You have no child PostBack for this page."); ?>', 'warning');
            $("#parent_postback").prop("checked", true);
            var content = '<label><?php echo $this->lang->line("PostBack ID"); ?><a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class="fa fa-info-circle"></i> </a></label>'+
                      '<input type="text" name="template_postback_id" id="template_postback_id" class="form-control">';
            $("#postback_section").html(content);
            return false;
          }
          else
            $("#postback_section").html(response.first_dropdown);   
        }
      });
    }


    
    $('.show_label').addClass('hidden');
    $.ajax({
      type:'POST' ,
      url: base_url+'messenger_bot/get_label_dropdown',
      data: {page_id:page_id},
      dataType : 'JSON',
      success:function(response){
        $(".show_label #create_label_postback").attr("page_id_for_label",page_id); // put page_table_id for create label
        $('.show_label').removeClass('hidden');
        $('#first_dropdown').html(response.first_dropdown);      
      }
    });

    $('.dropdown_con').addClass('hidden');
    var is_drip_campaigner_exist='<?php echo $this->is_drip_campaigner_exist;?>';
    var is_sms_email_drip_campaigner_exist = '<?php echo $this->is_sms_email_drip_campaigner_exist;?>';
    if(is_drip_campaigner_exist==false && is_sms_email_drip_campaigner_exist==false) return;

    $.ajax({
      type:'POST' ,
      url: base_url+'messenger_bot/get_drip_campaign_dropdown',
      data: {page_id:page_id},
      dataType : 'JSON',
      success:function(response){
        $('.dropdown_con').removeClass('hidden');
        $('#dripcampaign_dropdown').html(response.dropdown_value);      
      }
    });
    // $('.dropdown_con').removeClass('hidden');
  }
</script>


<script type="text/javascript">
  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
  var base_url="<?php echo site_url(); ?>";
  
  <?php foreach($page_list as $key=>$value) : ?>    
    var js_array_<?php echo $key ?> = [<?php echo ""; ?>];
  <?php endforeach; ?> 

  <?php foreach($postback_id_array as $key=>$value) : ?>    
    var js_array_<?php echo $key ?> = [<?php echo '"'.implode('","', $value ).'"' ?>];
  <?php endforeach; ?> 

  // need to return back
  // $("#keywordtype_postback_id").multipleSelect({
  //     filter: true,
  //     multiple: true
  // });

  var areyousure="<?php echo $areyousure;?>";
  
  var text_with_button_counter = 1;
  var generic_template_button_counter = 1;
  var carousel_template_counter = 1;
  $(document).ready(function() {
    
    /**Load Emoji For first Text Reply Field By Default***/
   $("#text_reply_1").emojioneArea({
            autocomplete: false,
        pickerPosition: "bottom"
     });
    
    

    var keyword_type = $("input[name=keyword_type]:checked").val();
    if(keyword_type == 'reply')
    {
      $("#keywords_div").show();
    }else{
      $("#keywords_div").hide();
    }

    $(document).on('change','input[name=keyword_type]',function(){
      if($("input[name=keyword_type]:checked").val()=="reply")
      {
        $("#keywords_div").show();
      }
      else 
      {
        $("#keywords_div").hide();
      }
    });


    var default_child_postback_id = '<?php echo urldecode($default_child_postback_id);?>';

    $(document).on('change','input[name=postback_type]',function(){
      if($("input[name=postback_type]:checked").val()=="child")
      {     
        var page_auto_id = $("#page_table_id").val();
        if(page_auto_id == '')
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You have to select a page first."); ?>', 'warning');
          $("#parent_postback").prop("checked", true);
          return false;
        }
        else
        {
          $.ajax({
            type:'POST' ,
            url: base_url+'messenger_bot/get_postback_dropdown',
            data: {page_auto_id:page_auto_id,default_child_postback_id:default_child_postback_id},
            dataType : 'JSON',
            success:function(response){  
              if(response.first_dropdown == ''){
                swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You have no child PostBack for this page."); ?>', 'warning');
                $("#parent_postback").prop("checked", true);
                return false;
              }
              else
                $("#postback_section").html(response.first_dropdown);   
            }
          });
        }
      }
      else 
      {
        var content = '<label><?php echo $this->lang->line("PostBack ID"); ?><a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Supported Characters") ?>" data-content="It is recommended to use English characters as postback id. You can use a-z, A-Z, 0-9, -, , _"><i class="fa fa-info-circle"></i> </a></label>'+
                  '<input type="text" name="template_postback_id" id="template_postback_id" class="form-control">';
        $("#postback_section").html(content);
      }
    });

    
    if(default_child_postback_id!="") {
      $("#child_postback").prop("checked", true);
      $('input[name=postback_type]').change();
    }

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


    var keyword_type = $("input[name=keyword_type]:checked").val();
    if(keyword_type == 'post-back')
    {
      $("#postback_div").show();
    }

    $(document).on('change','input[name=keyword_type]',function(){    
      if($("input[name=keyword_type]:checked").val()=="post-back")
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
  
  var template_type_order="#template_type_<?php echo $template_type ?>";
  
    $(document).on('change',"#template_type_<?php echo $template_type ?>",function(){
  
      var selected_template = $("#template_type_<?php echo $template_type ?>").val();
      selected_template = selected_template.replace(/ /gi, "_");

      var template_type_array = ['text','image','audio','video','file','quick_reply','text_with_buttons','generic_template','carousel','list','media','One_Time_Notification'];
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

    <?php for($i=1; $i<=4; $i++) : ?>
      $("#list_imageupload_<?php echo $i; ?>_<?php echo $template_type; ?>").uploadFile({
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
                  $("#list_image_<?php echo $i; ?>_<?php echo $template_type; ?>").val('');                      
                });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = base_url+"upload/image/"+user_id+"/"+data;
               $("#list_image_<?php echo $i; ?>_<?php echo $template_type; ?>").val(data_modified);     
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
         swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Button Text')?>', 'warning');
         return;
       }

       var media_type_check = $(media_type).val();
       if(media_type_check == ''){
         swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Button Type')?>', 'warning');
         return;
       }else if(media_type_check == 'post_back'){

         var media_post_id = "#media_post_id_"+button_id+"_<?php echo $template_type; ?>";
         var media_post_id_check = $(media_post_id).val();
         if(media_post_id_check == ''){
           swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your PostBack Id')?>', 'warning');
           return;
         }
         

       }else if(media_type_check == 'web_url' || media_type_check == 'web_url_compact' || media_type_check == 'web_url_tall' || media_type_check == 'web_url_full'){
         var media_web_url = "#media_web_url_"+button_id+"_<?php echo $template_type; ?>";
         var media_web_url_check = $(media_web_url).val();
         if(media_web_url_check == ''){
           swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Web Url')?>', 'warning');
           return;
         }
       }else if(media_type_check == 'phone_number'){
         var media_call_us = "#media_call_us_"+button_id+"_<?php echo $template_type; ?>";
         var media_call_us_check = $(media_call_us).val();
         if(media_call_us_check == ''){
           swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Phone Number')?>', 'warning');
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
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your PostBack Id')?>', 'warning');
          return;
        }

        var reg = /^[0-9a-z_ -]+$/i;
        var output = reg.test(quick_reply_post_id_check);
        if(output === false)
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('There is disallowed characters in your PostBack Id')?>', 'warning');
          return;
        }

        var quick_reply_button_text_check = $(quick_reply_button_text).val();

        if(quick_reply_button_text_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Button Text')?>', 'warning');
          return;
        }


      }
      if(quick_reply_button_type == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Button Type')?>', 'warning');
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
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Button Text')?>', 'warning');
        return;
      }

      var text_with_button_type_check = $(text_with_button_type).val();
      if(text_with_button_type_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Button Type')?>', 'warning');
        return;
      }else if(text_with_button_type_check == 'post_back'){

        var text_with_button_post_id = "#text_with_button_post_id_"+button_id+"_<?php echo $template_type; ?>";
        var text_with_button_post_id_check = $(text_with_button_post_id).val();
        if(text_with_button_post_id_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your PostBack Id')?>', 'warning');
          return;
        }

        var reg = /^[0-9a-z_ -]+$/i;
      	var output = reg.test(text_with_button_post_id_check);
      	if(output === false)
    		{
    			swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('There is disallowed characters in your PostBack Id')?>', 'warning');
    			return;
    		}

      }else if(text_with_button_type_check == 'web_url' || text_with_button_type_check == 'web_url_compact' || text_with_button_type_check == 'web_url_tall' || text_with_button_type_check == 'web_url_full'){
        var text_with_button_web_url = "#text_with_button_web_url_"+button_id+"_<?php echo $template_type; ?>";
        var text_with_button_web_url_check = $(text_with_button_web_url).val();
        if(text_with_button_web_url_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('Please Provide Your Web Url')?>', 'warning');
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

        var reg = /^[0-9a-z_ -]+$/i;
      	var output = reg.test(generic_template_button_post_id_check);
      	if(output === false)
    		{
    			swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('There is disallowed characters in your PostBack Id')?>', 'warning');
    			return;
    		}


      }else if(generic_template_button_type_check == 'web_url' || generic_template_button_type_check == 'web_url_full' || generic_template_button_type_check == 'web_url_compact' || generic_template_button_type_check == 'web_url_tall'){

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

          var reg = /^[0-9a-z_ -]+$/i;
        	var output = reg.test(carousel_button_post_id_check);
        	if(output === false)
      		{
      			swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('There is disallowed characters in your PostBack Id')?>', 'warning');
      			return;
      		}


        }else if(carousel_button_type_check == 'web_url' || carousel_button_type_check == 'web_url_compact' || carousel_button_type_check == 'web_url_tall' || carousel_button_type_check == 'web_url_full'){

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

    

    var list_template_counter_<?php echo $template_type; ?>=2;
  
    $(document).on('click','#list_template_add_button_<?php echo $template_type; ?>',function(e){
      e.preventDefault();

      var list_button_text = "#list_with_buttons_text_<?php echo $template_type; ?>";
      var list_button_type = "#list_with_button_type_<?php echo $template_type; ?>";

      var list_button_text_check = $(list_button_text).val();
      if(list_button_text_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
        $("#error_modal").modal();
        return;
      }

      var list_button_type_check = $(list_button_type).val();
      if(list_button_type_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Type')?>", 'warning');
        return;
      }else if(list_button_type_check == 'post_back'){

        var list_button_post_id = "#list_with_button_post_id_<?php echo $template_type; ?>";
        var list_button_post_id_check = $(list_button_post_id).val();
        if(list_button_post_id_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
          return;
        }

        var reg = /^[0-9a-z_ -]+$/i;
      	var output = reg.test(list_button_post_id_check);
      	if(output === false)
    		{
    			swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('There is disallowed characters in your PostBack Id')?>', 'warning');
    			return;
    		}
  		
      }else if(list_button_type_check == 'web_url' || list_button_type_check == 'web_url_full' || list_button_type_check == 'web_url_tall' || list_button_type_check == 'web_url_compact'){

        var list_button_web_url = "#list_with_button_web_url_<?php echo $template_type; ?>";
        var list_button_web_url_check = $(list_button_web_url).val();
        if(list_button_web_url_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Web Url')?>", 'warning');
          return;
        }
      }else if(list_button_type_check == 'phone_number'){
        var list_button_call_us = "#list_with_button_call_us_<?php echo $template_type; ?>";
        var list_button_call_us_check = $(list_button_call_us).val();
        if(list_button_call_us_check == ''){
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Phone Number')?>", 'warning');
          return;
        }
      }


      var prev_list_image_counter = eval(list_template_counter_<?php echo $template_type; ?>+"-1");
      var list_image_1 = "#list_image_"+prev_list_image_counter+"_"+<?php echo $template_type; ?>;
      var list_image_check_1 = $(list_image_1).val();
      if(list_image_check_1 == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please provide your reply image')?>", 'warning');
        return;
      }

      var list_image = "#list_image_"+list_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var list_image_check = $(list_image).val();
      if(list_image_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please provide your reply image')?>", 'warning');
        return;
      }

      var prev_list_title_counter = eval(list_template_counter_<?php echo $template_type; ?>+"-1");
      var list_title_1 = "#list_title_"+prev_list_title_counter+"_"+<?php echo $template_type; ?>;
      var list_title_check_1 = $(list_title_1).val();
      if(list_title_check_1 == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide list title')?>", 'warning');
        return;
      }

      var list_title = "#list_title_"+list_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var list_title_check = $(list_title).val();
      if(list_title_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide list title')?>", 'warning');
        return;
      }

      var prev_list_dest_counter = eval(list_template_counter_<?php echo $template_type; ?>+"-1");
      var list_image_destination_link_1 = "#list_image_destination_link_"+prev_list_dest_counter+"_"+<?php echo $template_type; ?>;
      var list_image_destination_link_check_1 = $(list_image_destination_link_1).val();
      if(list_image_destination_link_check_1 == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Image Click Destination Link')?>", 'warning');
        return;        
      }

      var list_image_destination_link = "#list_image_destination_link_"+list_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var list_image_destination_link_check = $(list_image_destination_link).val();
      if(list_image_destination_link_check == ''){
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Image Click Destination Link')?>", 'warning');
        return;        
      }

      list_template_counter_<?php echo $template_type; ?>++;
    
      var x = list_template_counter_<?php echo $template_type; ?>;
    
      $("#list_div_"+x+"_<?php echo $template_type; ?>").show();
      if( list_template_counter_<?php echo $template_type; ?> == 4)
        $("#list_template_add_button_<?php echo $template_type; ?>").hide();
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


    $(document).on('change','.list_with_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      var which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];

      if(button_type == 'post_back')
      {
        $("#list_with_button_postid_div_"+which_block_is_clicked+" select option[value='UNSUBSCRIBE_QUICK_BOXER']").remove();
        $("#list_with_button_postid_div_"+which_block_is_clicked+" select option[value='RESUBSCRIBE_QUICK_BOXER']").remove();
        $("#list_with_button_postid_div_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_HUMAN']").remove();
        $("#list_with_button_postid_div_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_BOT']").remove();
        $("#list_with_button_postid_div_"+which_block_is_clicked).show();
        $("#list_with_button_web_url_div_"+which_block_is_clicked).hide();
        $("#list_with_button_call_us_div_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        }
        if(option_id=="human_postback")
        {
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_HUMAN").text("<?php echo $this->lang->line('Chat with Human');?>")); 
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_HUMAN"); 
           $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        }
        if(option_id=="robot_postback")
        {
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_BOT").text("<?php echo $this->lang->line('Chat with Robot');?>")); 
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_BOT"); 
           $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        }
      }
      else if(button_type == 'web_url' || button_type == 'web_url_compact' || button_type == 'web_url_tall' || button_type == 'web_url_full')
      {
        $("#list_with_button_web_url_div_"+which_block_is_clicked).show();
        $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        $("#list_with_button_call_us_div_"+which_block_is_clicked).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#list_with_button_call_us_div_"+which_block_is_clicked).show();
        $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        $("#list_with_button_web_url_div_"+which_block_is_clicked).hide();
      }
      else
      {
        $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        $("#list_with_button_web_url_div_"+which_block_is_clicked).hide();
        $("#list_with_button_call_us_div_"+which_block_is_clicked).hide();
      }
    });




    function hasDuplicates(array) {
      var valuesSoFar = Object.create(null);
      for (var i = 0; i < array.length; ++i) {
        var value = array[i];
        if (value in valuesSoFar) {
          return true;
        }
        valuesSoFar[value] = true;
      }
      return false;
    }


    $(document).on('keyup', '#bot_name', function(event) {
      event.preventDefault();
      var default_child_postback_id = "<?php echo $default_child_postback_id; ?>";
      if(default_child_postback_id == '')
      {
        var bot_name = $(this).val();
        var reg = /^[0-9a-z_ -]+$/i;
        var output = reg.test(bot_name);

        if(output === false)
        {
          bot_name = bot_name.replace(/[\W]+/g, "_")
        }
        if($("input[name=postback_type]:checked").val() != "child")
          $("#template_postback_id").val(bot_name);
      }
    });


    $(document).on('click','#submit',function(e){   
      e.preventDefault();

      var bot_name = $("#bot_name").val();
      var template_postback_id = $("#template_postback_id").val();

      var reg = /^[0-9a-z_ -]+$/i;
      var output = reg.test(template_postback_id);
      if(output === false)
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line('There is disallowed characters in your main PostBack Id')?>', 'warning');
        return;
      }

      var page_table_id = $("#page_table_id").val();
      var new_variable_name = "js_array_"+page_table_id; 
      var default_postback = "<?php echo $default_child_postback_id; ?>";
      if(default_postback == '')
      {      
        if($("input[name=postback_type]:checked").val()!="child")
        {        
          if(jQuery.inArray(template_postback_id.toUpperCase(), eval(new_variable_name)) !== -1){
            swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('The PostBack ID you have given is allready exist. Please provide different PostBack Id')?>", 'warning');
            return ;
          }
        }
      }
      
      var keyword_type = $("input[name=keyword_type]:checked").val();

      if(bot_name == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Give Template Name')?>", 'warning');
        return;
      }

      if(page_table_id == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please select a page')?>", 'warning');
        return;
      }

      if(template_postback_id == '')
      {
        swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please give a postback ID')?>", 'warning');
        return;
      }


      if(keyword_type == 'post-back')
      {
        if($("#keywordtype_postback_id").val() == '' || typeof($("#keywordtype_postback_id").val()) == 'undefined' || $("#keywordtype_postback_id").val() == null)
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please provide postback id')?>", 'warning');
          return;
        }
      }

      if(keyword_type == 'reply')
      {
        var keywords_list = $("#keywords_list").val();
        if(keywords_list =='')
        {
          swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Keywords In Comma Separated')?>", 'warning');
          return;
        }
      }

      for(var m=1; m<=multiple_template_add_button_counter; m++)
      {
          var template_type = $("#template_type_"+m).val();

          if(template_type == 'text')
          {
            var text_reply = $("#text_reply_"+m).val();
            if(text_reply == ''){
              swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Reply Message')?>", 'warning');
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

                if(media_post_id_check == template_postback_id){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please use different ID for main postback and button type postback.')?>", 'warning');
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
                if(quick_reply_post_id_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                  return;
                }

                if(quick_reply_post_id_check == template_postback_id){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please use different ID for main postback and button type postback.')?>", 'warning');
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
                if(text_with_button_post_id_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                  return;
                }
                if(text_with_button_post_id_check == template_postback_id){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please use different ID for main postback and button type postback.')?>", 'warning');
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
                if(generic_template_button_post_id_check == ''){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                  return;
                }

                if(generic_template_button_post_id_check == template_postback_id){
                  swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please use different ID for main postback and button type postback.')?>", 'warning');
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
              var submited_carousel_add_button_counter = eval("carousel_add_button_counter_<?php echo $j; ?>_"+m);
              for(var n=1; n<=submited_carousel_add_button_counter; n++)
              {
                var carousel_button_text = "#carousel_button_text_<?php echo $j; ?>_"+n+"_"+m;
                var carousel_button_type = "#carousel_button_type_<?php echo $j; ?>_"+n+"_"+m;

                if($(carousel_button_type).parent().parent().parent().is(":visible"))
                {
                  var carousel_button_text_check = $(carousel_button_text).val();
                  var carousel_button_type_check = $(carousel_button_type).val();

                  if(carousel_button_text_check == '' && carousel_button_type_check!=""){
                    swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your Button Text')?>", 'warning');
                    return;
                  }

                  if(carousel_button_type_check == 'post_back'){

                    var carousel_button_post_id = "#carousel_button_post_id_<?php echo $j;?>_"+n+"_"+m;
                    var carousel_button_post_id_check = $(carousel_button_post_id).val();
                    if(carousel_button_post_id_check == ''){
                      swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please Provide Your PostBack Id')?>", 'warning');
                      return;
                    }
                    if(carousel_button_post_id_check == template_postback_id){
                      swal('<?php echo $this->lang->line("Warning"); ?>', "<?php echo $this->lang->line('Please use different ID for main postback and button type postback.')?>", 'warning');
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


      var is_iframe="<?php echo $is_iframe;?>";

      $("input:not([type=hidden])").each(function(){
        if($(this).is(":visible") == false)
          $(this).attr("disabled","disabled");
      });


      $(this).addClass('btn-progress');

      var queryString = new FormData($("#messenger_bot_form")[0]);
      $.ajax({
        context: this,
        type:'POST' ,
        url: base_url+"messenger_bot/create_template_action",
        data: queryString,
        dataType : 'JSON',
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success:function(response){
          $(this).removeClass('btn-progress');
          var link="<?php echo site_url('messenger_bot/template_manager'); ?>";            
          if(is_iframe=='1') 
          {
            $(this).attr('disabled','disabled');
            swal('<?php echo $this->lang->line("Success"); ?>', "<?php echo $this->lang->line('Template has been created successfully.'); ?>", 'success');
          }
          else
          {
            swal('<?php echo $this->lang->line("Success"); ?>', "<?php echo $this->lang->line('Template has been created successfully.'); ?>", 'success').then((value) => {
              window.location.assign(link);
            });

          } 
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
    $("#bot_settings_data_table").DataTable();
    
    $(document).on('click','#add_bot_settings',function(){
       $("#add_bot_settings_modal").removeClass('hidden');
       $("#bot_success").hide();
       $('html, body').animate({scrollTop: $("#add_bot_settings_modal").offset().top}, 2000);
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


  }); 
</script>


<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
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
  <div class="modal-dialog  modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("How to get media URL?"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div>

          <p>To get the Facebook URL for an image or video, do the following:</p>
          <ul>
            <li>Click the image or video thumbnail to open the full-size view.</li>
            <li>Copy the URL from your browser's address bar.</li>
          </ul>
          <p>Facebook URLs should be in the following base format:</p>
          <div class="table-responsive2">
            <table class='table table-condensed table-bordered table-hover table-striped' >
             <thead>
               <tr>
                 <th>Media Type</th>
                 <th>Media Source</th>
                 <th>URL Format</th>
               </tr>
             </thead>
             <thead>
               <tr>
                 <td>Video</td>
                 <td>Facebook Page</td>
                 <td>https://business.facebook.com/<b>PAGE_NAME</b>/videos/<b>NUMERIC_ID</b></td>
               </tr>
               <tr>
                 <td>Video</td>
                 <td>Facebook Account</td>
                 <td>https://www.facebook.com/<b>USERNAME</b>/videos/<b>NUMERIC_ID</b>/</td>
               </tr>
               <tr>
                 <td>Image</td>
                 <td>Facebook Page</td>
                 <td>https://business.facebook.com/<b>PAGE_NAME</b>/photos/<b>NUMERIC_ID</b></td>
               </tr>
               <tr>
                 <td>Image</td>
                 <td>Facebook Account</td>
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


<?php if($is_iframe=="1") echo '<link rel="stylesheet" type="text/css" href="'.base_url('css/bot_template.css').'">'; ?>