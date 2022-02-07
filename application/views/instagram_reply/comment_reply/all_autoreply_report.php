<link rel="stylesheet" href="<?php echo base_url('assets/css/system/instagram/instagram_comment_reply.css');?>">
<?php 
	$this->load->view("include/upload_js");
	$commnet_hide_delete_addon = $commnet_hide_delete_addon;
    $comment_tag_machine_addon = 1;
?>

<?php 
	if($insta_username != "") {
        $instagram_account = '<a target="_BLANK" href="https://www.instagram.com/'.$insta_username.'">'.$insta_username.'</a>';
        $title = $instagram_account.' - '.$page_title;
    }
    else $title = ucfirst($reply_type).' '.$this->lang->line("Reply Report");
?>
<input type="hidden" id="page_info_table_id" value="<?php echo $page_table_id; ?>">
<input type="hidden" id="auto_reply_campaign_id" value="<?php echo $auto_reply_campaign_id; ?>">
<section class="section section_custom">
    <div class="section-header">
        <h1><i class="far fa-list-alt"></i> <?php echo $title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url('comment_automation/comment_growth_tools'); ?>"><?php echo $this->lang->line("Comment Growth Tools"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url('instagram_reply/reports'); ?>"><?php echo $this->lang->line("Instagram Report Section"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body data-card">
                        <div class="row">
                            <div class="co-12 col-md-6">
                                <div class="input-group float-left" id="searchbox">
                                    <select name="insta_accounts" id="insta_accounts" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('Instagram Accounts'); ?></option>
                                        <?php foreach ($insta_accounts as $value) {
                                            if($reply_type == "post") {

                                                $selected_posts = "";
                                                if($reply_type.'-'.$value['page_info_table_id'] == $reply_type.'-'.$page_table_id) {
                                                    $selected_posts = "selected";
                                                }

                                                echo '<option value="'.$reply_type.'-'.$value['page_info_table_id'].'" '.$selected_posts.'>'.$value['insta_username'].' ['.$value['name'].']</option>';

                                            } else if($reply_type == "full" || $reply_type == "mention") {

                                                $selected_full_mention = "";
                                                if($reply_type.'-'.$value['page_info_table_id'].'-'.$value['id'] == $reply_type.'-'.$page_table_id.'-'.$auto_reply_campaign_id) {
                                                    $selected_full_mention = "selected";
                                                }

                                                echo '<option value="'.$reply_type.'-'.$value['page_info_table_id'].'-'.$value['id'].'" '.$selected_full_mention.'>'.$value['insta_username'].' ['.$value['name'].']</option>';
                                            }
                                        } 
                                        ?>

                                    </select>
                                    <input type="text" name="post_id" id="post_id" class="form-control" placeholder="<?php echo $this->lang->line('Search...'); ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" id="report_search" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive2">
                                	<input type="hidden" id="reply_type" value="<?php echo $reply_type;?>">
                                    <table class="table table-bordered" id="mytable_instagram_report">
                                        <thead>
                                            <tr>
                                                <th>#</th>      
                                                <th><?php echo $this->lang->line("ID"); ?></th>      
                                                <th><?php echo $this->lang->line("Thumbnail"); ?></th>
                                                <th><?php echo $this->lang->line("Post ID"); ?></th>
                                                <th><?php echo $this->lang->line("Actions"); ?></th>
                                                <th><?php echo $this->lang->line("Last Replied at"); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>            
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php
	$Youdidntprovideallinformation = $this->lang->line("you didn't provide all information.");
	$Pleaseprovidepostid = $this->lang->line("please provide post id.");
	$Youdidntselectanyoption = $this->lang->line("you didn\'t select any option.");
	$AlreadyEnabled = $this->lang->line("already enabled");
	$ThispostIDisnotfoundindatabaseorthispostIDisnotassociatedwiththepageyouareworking = $this->lang->line("This post ID is not found in database or this post ID is not associated with the page you are working.");
	$EnableAutoReply = $this->lang->line("enable auto reply");
	$areyousure = $this->lang->line("are you sure");
	$disablebot = $this->lang->line("Disable reply");
	$enablebot = $this->lang->line("Enable reply");
	$restart_bot = $this->lang->line("Re-start Reply");
?>

<?php include("application/views/instagram_reply/comment_reply/comment_reply_js.php"); ?>

<div class="modal fade" id="campaign_report_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye"></i> <?php echo $this->lang->line("Campaign Report") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="sent_report_body">
                <div id="contents"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive2 data-card">
                            <input type="hidden" value="" id="campaign_report_post_id">
                            <input type="hidden" value="" id="table_id">
                            <input type="hidden" value="" id="reply_type">
                            <table class="table table-bordered" id="mytable_campaign_report">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('#'); ?></th> 
                                        <th><?php echo $this->lang->line('id'); ?></th> 
                                        <th><?php echo $this->lang->line("Name"); ?></th>
                                        <th><?php echo $this->lang->line("comment"); ?></th>
                                        <th><?php echo $this->lang->line("comment reply message"); ?></th>
                                        <th><?php echo $this->lang->line('reply time'); ?></th>
                                        <th><?php echo $this->lang->line('comment reply status'); ?></th>
                                        <?php 
                                            if($instagram_bot_exist && $reply_type != "mention") {
                                                echo "<th>".$this->lang->line('Private reply status')."</th>";
                                            }
                                         ?>
                                        <th><?php echo $this->lang->line('Error Message'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="edit_auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center"><?php echo $this->lang->line("Please give the following information for post auto Reply") ?></h5>
                <button type="button" id='edit_modal_close' class="close">&times;</button>
            </div>
            <form action="#" id="edit_auto_reply_info_form" method="post">
                <input type="hidden" name="edit_auto_reply_page_id" id="edit_auto_reply_page_id" value="">
                <input type="hidden" name="edit_auto_reply_post_id" id="edit_auto_reply_post_id" value="">
                <div class="modal-body" id="edit_auto_reply_message_modal_body">

                    <div class="text-center waiting previewLoader"><i class="fas fa-spinner fa-spin blue text-center font_size_40px"></i></div><br>


                    <!-- comment hide and delete section -->
                    <div class="row padding_10_20_10_20px <?php if (!$commnet_hide_delete_addon) echo 'd_none'; ?>">
                        <div class="col-12">

                            <div class="row">							
                            	<div class="col-12 col-md-6" >
                            		<label><i class="fa fa-ban"></i> <?php echo $this->lang->line("what do you want about offensive comments?") ?></label>
                            	</div>
                            	<div class="col-12 col-md-6">
                            		<div class="row">
                            		  <div class="col-12 col-md-6">
                            		    <label class="custom-switch">
                            		      <input type="radio" name="edit_delete_offensive_comment" value="hide" id="edit_delete_offensive_comment_hide" class="custom-switch-input" checked>
                            		      <span class="custom-switch-indicator"></span>
                            		      <span class="custom-switch-description"><?php echo $this->lang->line('hide'); ?></span>
                            		    </label>
                            		  </div>
                            		  <div class="col-12 col-md-6">
                            		    <label class="custom-switch">
                            		      <input type="radio" name="edit_delete_offensive_comment" value="delete"  id="edit_delete_offensive_comment_delete" class="custom-switch-input">
                            		      <span class="custom-switch-indicator"></span>
                            		      <span class="custom-switch-description"><?php echo $this->lang->line('delete'); ?>
                            		    </label>
                            		  </div>
                            		</div>
                            	</div>
                            </div>
                        </div>
                        <br/><br/>
                        <div class="col-12 col-md-12" id="edit_delete_offensive_comment_keyword_div">
                            <div class="form-group e4e6fc_border_dashed">
                                <label><i class="fa fa-tag"></i> <?php echo $this->lang->line("write down the offensive keywords in comma separated") ?>
                                <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("offensive keywords") ?>" data-content="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions"); ?> "><i class='fa fa-info-circle'></i> </a>
                                </label>
                                <textarea class="form-control message height_70px" name="edit_delete_offensive_comment_keyword" id="edit_delete_offensive_comment_keyword" placeholder="<?php echo $this->lang->line("Type keywords here in comma separated (keyword1,keyword2)...Keep it blank for no actions") ?>" ></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- end of comment hide and delete section -->


                    <div class="row padding_10_20_10_20px">
                        <div class="col-12">

                            <div class="row">							
                            	<div class="col-12 col-md-6" ><label><i class="fa fa-sort-numeric-down"></i> <?php echo $this->lang->line("Do you want to reply comments of a user multiple times?") ?></label></div>
                            	<div class="col-12 col-md-6">
                            	  <div class="form-group">
                            	    <label class="custom-switch">
                            	      <input type="checkbox" name="edit_multiple_reply" value="yes" id="edit_multiple_reply" class="custom-switch-input">
                            	      <span class="custom-switch-indicator"></span>
                            	      <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                            	    </label>
                            	  </div>
                            	</div>
                            </div>
                        </div>
                        <div class="smallspace clearfix"></div>

                        <!-- comment hide and delete section -->
                        <div class="col-12 <?php if (!$commnet_hide_delete_addon) echo 'd_none'; ?>" >

                            <div class="row">							
                            	<div class="col-12 col-md-6" >
                            		<label><i class="fa fa-eye-slash"></i> <?php echo $this->lang->line("do you want to hide comments after comment reply?") ?></label>
                            	</div>
                            	<div class="col-12 col-md-6">
                            	  <div class="form-group">
                            	    <label class="custom-switch">
                            	      <input type="checkbox" name="edit_hide_comment_after_comment_reply" value="yes" id="edit_hide_comment_after_comment_reply" class="custom-switch-input">
                            	      <span class="custom-switch-indicator"></span>
                            	      <span class="custom-switch-description"><?php echo $this->lang->line('Yes');?></span>
                            	    </label>
                            	  </div>
                            	</div>
                            </div>
                        </div>
                        <!-- comment hide and delete section -->

                        <br/><br/>

                        <div class="col-12">
                          <div class="custom-control custom-radio">
                          	<input type="radio" name="edit_message_type" value="generic" id="edit_generic" class="custom-control-input radio_button">
                          	<label class="custom-control-label" for="edit_generic"><?php echo $this->lang->line("generic comment reply for all") ?></label>
                          </div>
                          <div class="custom-control custom-radio">
                          	<input type="radio" name="edit_message_type" value="filter" id="edit_filter" class="custom-control-input radio_button">
                          	<label class="custom-control-label" for="edit_filter"><?php echo $this->lang->line("send comment reply by filtering word/sentence") ?></label>
                          </div>
                        </div>

                        <div class="col-12 margin_top_15">
                            <div class="form-group">
                                <label>
                                    <i class="fa fa-monument"></i> <?php echo $this->lang->line("auto comment reply campaign name") ?> <span
                                            class="red">*</span>
                                </label>
                                <input class="form-control" type="text" name="edit_auto_campaign_name"
                                       id="edit_auto_campaign_name"
                                       placeholder="<?php echo $this->lang->line("write your auto comment reply campaign name here") ?>">
                            </div>
                        </div>
                        <div class="col-12 d_none" id="edit_generic_message_div">
                            <div class="form-group e4e6fc_border_dashed">
                                <label>
                                    <i class="fa fa-envelope"></i> <?php echo $this->lang->line("comment reply text") ?> <span
                                            class="red">*</span>
                                    <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus"
                                       title="<?php echo $this->lang->line("message") ?>"
                                       data-content="<?php echo $this->lang->line("write your message which you want to send. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i
                                                class='fa fa-info-circle'></i> </a>
                                </label>
                                <?php if ($comment_tag_machine_addon) { ?>
                                    <span class='float-right'>
                                        <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top"
                                           class='btn btn-default btn-sm lead_tag_name button-outline'><i class='fa fa-tags'></i> <?php echo $this->lang->line("mention user") ?></a>
                                    </span>
                                <?php } ?>
                                <span class='float-right'>
                                    <a title="<?php echo $this->lang->line("You can include #LEAD_USER_NAME# variable inside your message. The variable will be replaced by real username when we will send it.") ?>" data-toggle="tooltip" data-placement="top" class='btn btn-default btn-sm lead_first_name button-outline'><i class='fa fa-user'></i> <?php echo $this->lang->line("Username") ?></a>
                                </span>
                                <div class="clearfix"></div>
                                <textarea class="form-control message height_170px" name="edit_generic_message" id="edit_generic_message" placeholder="<?php echo $this->lang->line("type your comment reply here...") ?>"></textarea>
                            </div>
                        </div>
                        <div class="col-12" id="edit_filter_message_div d_none">
                            <?php for ($i = 1; $i <= 20; $i++) : ?>
                                <div class="form-group instagram_border_margined_padded2" id="edit_filter_div_<?php echo $i; ?>">
                                    <label>
                                        <i class="fa fa-tag"></i> <?php echo $this->lang->line("filter word/sentence") ?> <span
                                                class="red">*</span>
                                        <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus"
                                           title="<?php echo $this->lang->line("message") ?>"
                                           data-content="<?php echo $this->lang->line("Write the word or sentence for which you want to filter comment. For multiple filter keyword write comma separated. Example -   why, want to know, when") ?>"><i
                                                    class='fa fa-info-circle'></i> </a>
                                    </label>
                                    <input class="form-control filter_word" type="text" name="edit_filter_word_<?php echo $i; ?>" id="edit_filter_word_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("write your filter word here") ?>">

                                    <br>
                                    <label>
                                        <?php echo $this->lang->line("comment reply text") ?><span
                                                class="red">*</span>
                                        <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("write your message which you want to send based on filter words. You can customize the message by individual commenter name."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                                    </label>
                                    <?php if ($comment_tag_machine_addon) { ?>
                                        <span class='float-right'>
                                            <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top"
                                               class='btn btn-default btn-sm lead_tag_name button-outline'><i
                                                        class='fa fa-tags'></i> <?php echo $this->lang->line("mention user") ?></a>
                                        </span>
                                    <?php } ?>
                                    <span class='float-right'>
                                        <a title="<?php echo $this->lang->line("You can include #LEAD_USER_NAME# variable inside your message. The variable will be replaced by real username when we will send it.") ?>" data-toggle="tooltip" data-placement="top"
                                           class='btn btn-default btn-sm lead_first_name button-outline'><i
                                                    class='fa fa-user'></i> <?php echo $this->lang->line("Username") ?></a>
                                    </span>
                                    <div class="clearfix"></div>
                                    <textarea class="form-control message height_170px" name="edit_comment_reply_msg_<?php echo $i; ?>" id="edit_comment_reply_msg_<?php echo $i; ?>" placeholder="<?php echo $this->lang->line("type your comment reply here...") ?>"></textarea>
                                </div>
                            <?php endfor; ?>

                            <div class="clearfix">
                                <input type="hidden" name="edit_content_counter" id="edit_content_counter"/>
                                <button type="button" class="btn btn-sm btn-outline-primary float-right" id="edit_add_more_button"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add more filtering") ?>
                                </button>
                            </div>

                            <div class="form-group instagram_border_padded3" id="edit_nofilter_word_found_div">
                                <label>
                                    <i class="fa fa-envelope"></i> <?php echo $this->lang->line("comment reply if no matching found") ?>
                                    <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus"
                                       title="<?php echo $this->lang->line("message") ?>"
                                       data-content="<?php echo $this->lang->line("Write the message,  if no filter word found. If you don't want to send message them, just keep it blank ."); ?>  Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i
                                                class='fa fa-info-circle'></i> </a>
                                </label>
                                <?php if ($comment_tag_machine_addon) { ?>
                                    <span class='float-right'>
                                        <a title="<?php echo $this->lang->line("You can tag user in your comment reply. Facebook will notify them about mention whenever you tag.") ?>" data-toggle="tooltip" data-placement="top"
                                           class='btn btn-default btn-sm lead_tag_name button-outline'><i
                                                    class='fa fa-tags'></i> <?php echo $this->lang->line("mention user") ?></a>
                                    </span>
                                <?php } ?>
                                <span class='float-right'>
                                    <a title="<?php echo $this->lang->line("You can include #LEAD_USER_NAME# variable inside your message. The variable will be replaced by real username when we will send it.") ?>" data-toggle="tooltip" data-placement="top"
                                       class='btn btn-default btn-sm lead_first_name button-outline'><i
                                                class='fa fa-user'></i> <?php echo $this->lang->line("Username") ?></a>
                                </span>
                                <div class="clearfix"></div>
                                <textarea class="form-control message height_170px" name="edit_nofilter_word_found_text"
                                          id="edit_nofilter_word_found_text"
                                          placeholder="<?php echo $this->lang->line("type your comment reply here...") ?>"></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="col-xs-12 text-center" id="edit_response_status"></div>
                </div>
            </form>
            <div class="clearfix"></div>
            <div class="modal-footer bg-whitesmoke padding_0_45px">
            	<div class="row">
            		<div class="col-6">
            			<button class="btn btn-lg btn-primary float-left" id="edit_save_button"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
            		</div>  
            		<div class="col-6">
            			<button class="btn btn-lg btn-secondary float-right cancel_button"><i class='fas fa-times'></i> <?php echo $this->lang->line("cancel") ?></button>
            		</div>
            	</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="media_insights_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title"><i class="fas fa-chart-bar"></i> <?php echo $this->lang->line('Post Analytics'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                 </button>
            </div>
            <div class="modal-body" id="media_insights_modal_body"></div>
        </div>
    </div>
</div>


