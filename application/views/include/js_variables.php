<script type="text/javascript">
	"use strict";
	var base_url="<?php echo base_url(); ?>";
	var user_id = "<?php echo $this->user_id; ?>";
	var selected_language="<?php echo $this->language; ?>";
	var is_demo = "<?php echo $this->is_demo; ?>";
	var is_admin = "<?php echo ($this->session->userdata('user_type') == "Admin") ? 1:0; ?>";
	var controller_name = '<?php echo $this->uri->segment(1); ?>';
    var function_name = '<?php echo $this->uri->segment(2); ?>';    
    var is_mobile =  '<?php echo $this->session->userdata("is_mobile");?>';
	var global_lang_video_upload_limit = '<?php echo $this->config->item("video_upload_limit");?>';
	global_lang_video_upload_limit = parseInt(global_lang_video_upload_limit);

	var global_lang_image = '<?php echo $this->lang->line("Image"); ?>';
	var global_lang_video = '<?php echo $this->lang->line("Video"); ?>';
	var global_lang_report = '<?php echo $this->lang->line("Report"); ?>';
	var global_lang_view = '<?php echo $this->lang->line("View"); ?>';
	var global_lang_edit = '<?php echo $this->lang->line("Edit"); ?>';
	var global_lang_update = '<?php echo $this->lang->line("Update"); ?>';
	var global_lang_delete = '<?php echo $this->lang->line("Delete"); ?>';
	var global_lang_remove = '<?php echo $this->lang->line("Remove"); ?>';
	var global_lang_close = '<?php echo $this->lang->line("Close"); ?>';
	var global_lang_copy = '<?php echo $this->lang->line("Copy"); ?>';
	var global_lang_submit = '<?php echo $this->lang->line("Submit"); ?>';
	var global_lang_add_more = '<?php echo $this->lang->line("Add more");?>';
	var global_lang_play = '<?php echo $this->lang->line("Play");?>';
	var global_lang_visit_channel = '<?php echo $this->lang->line("Visit Channel");?>';
	var global_lang_watch_video = '<?php echo $this->lang->line("Watch Video");?>';
	var global_lang_not_applicable = '<?php echo $this->lang->line("N/A");?>';
	var global_lang_url_copied_clipbloard = '<?php echo $this->lang->line("Url Copied to clipboard");?>';
	var global_lang_pause_campaign ='<?php echo $this->lang->line("Pause Campaign");?>';
	var global_lang_start_campaign = '<?php echo $this->lang->line("Start Campaign");?>';

	var global_lang_active = '<?php echo $this->lang->line("Active"); ?>';
	var global_lang_inactive = '<?php echo $this->lang->line("Inactive"); ?>';
	var global_lang_processing = '<?php echo $this->lang->line("Processing"); ?>';
	var global_lang_completed = '<?php echo $this->lang->line("Completed"); ?>';
	var global_lang_pending = '<?php echo $this->lang->line("Pending"); ?>';

	var global_lang_success = '<?php echo $this->lang->line("Success"); ?>';
	var global_lang_error = '<?php echo $this->lang->line("Error"); ?>';
	var global_lang_warning = '<?php echo $this->lang->line("Warning"); ?>';

	var global_lang_last_30_days = '<?php echo $this->lang->line("Last 30 Days");?>';
	var global_lang_this_month = '<?php echo $this->lang->line("This Month");?>';
	var global_lang_last_month = '<?php echo $this->lang->line("Last Month");?>';
	var global_lang_select_from_date = '<?php echo $this->lang->line("Please select from date");?>';
	var global_lang_select_to_date = '<?php echo $this->lang->line("Please select to date");?>';

	var global_lang_try_once_again = '<?php echo $this->lang->line("try once again")?>';
	var global_lang_something_went_wrong = '<?php echo $this->lang->line("Something went wrong, please try again.")?>';
	var global_lang_no_data_found = '<?php echo $this->lang->line("No data found"); ?>';
	var global_lang_are_you_sure = '<?php echo $this->lang->line("Are you sure?");?>';
	var global_lang_saved_successfully = '<?php echo $this->lang->line("Your data has been successfully saved."); ?>';
	var global_lang_delete_confirmation = '<?php echo $this->lang->line("Do you really want to delete it?");?>';

	var global_lang_campaign_create = '<?php echo $this->lang->line("Create Campaign");?>';
	var global_lang_campaign_edit = '<?php echo $this->lang->line("Edit Campaign");?>';
	var global_lang_campaign_delete = '<?php echo $this->lang->line("Delete Campaign");?>';
	var global_lang_campaign_delete_confirmation = '<?php echo $this->lang->line("Do you really want to delete this campaign?");?>';
	var global_lang_campaign_campaign_state_confirmation = '<?php echo $this->lang->line("Do you really want to change this campaign state?");?>';
	var global_lang_campaign_name = '<?php echo $this->lang->line("Campaign Name");?>';
	var global_lang_campaign_created_successfully = '<?php echo $this->lang->line("Campaign has been created successfully.");?>';
	var global_lang_campaign_updated_successfully = '<?php echo $this->lang->line("Campaign has been updated successfully.");?>';
	var global_lang_campaign_deleted_successfully = '<?php echo $this->lang->line("Campaign has been deleted successfully.");?>';
	var global_lang_campaign_paused_successfully = '<?php echo $this->lang->line("Campaign has been paused successfully.");?>';
	var global_lang_campaign_started_successfully = '<?php echo $this->lang->line("Campaign has been stared successfully.");?>';
	var global_lang_campaign_force_started_successfully = '<?php echo $this->lang->line("Force processing has been enabled successfully.");?>';
	var global_lang_no_video_found = '<?php echo $this->lang->line("We cound not find any video");?>';

	var global_lang_previous = '<?php echo $this->lang->line("Previous"); ?>'
	var global_lang_next = '<?php echo $this->lang->line("Next"); ?>'
	var global_lang_this = '<?php echo $this->lang->line("This"); ?>'
	var global_lang_today = '<?php echo $this->lang->line("Today"); ?>'
	var global_lang_month = '<?php echo $this->lang->line("Month"); ?>'
	var global_lang_week = '<?php echo $this->lang->line("Week"); ?>'
	var global_lang_day = '<?php echo $this->lang->line("Day"); ?>'
	var global_lang_all_day = '<?php echo $this->lang->line("All Day"); ?>'
	var global_lang_more = '<?php echo $this->lang->line("More"); ?>'
	var global_lang_no_event_found = '<?php echo $this->lang->line("No event found"); ?>'

	var upload_lang_drag_drop_files = "<?php echo $this->lang->line('Drag & Drop Files');?>";
	var upload_lang_upload = "<?php echo $this->lang->line('Upload');?>";
	var upload_lang_abort = "<?php echo $this->lang->line('Abort');?>";
	var upload_lang_cancel = "<?php echo $this->lang->line('Cancel');?>";
	var upload_lang_delete = "<?php echo $this->lang->line('Delete');?>";
	var upload_lang_done = "<?php echo $this->lang->line('Done');?>";
	var upload_lang_multiple_file_drag_drop_is_not_allowed = "<?php echo $this->lang->line('Multiple File Drag & Drop is not allowed.');?>";
	var upload_lang_is_not_allowed_allowed_extensions  = "<?php echo $this->lang->line('is not allowed. Allowed extensions: ');?>";
	var upload_lang_is_not_allowed_file_already_exists = "<?php echo $this->lang->line('is not allowed. File already exists.');?>";
	var upload_lang_is_not_allowed_allowed_max_size  = "<?php echo $this->lang->line('is not allowed. Allowed Max size: ');?>";
	var upload_lang_upload_is_not_allowed = "<?php echo $this->lang->line('Upload is not allowed');?>";
	var upload_lang_is_not_allowed_maximum_allowed_files_are = "<?php echo $this->lang->line('is not allowed. Maximum allowed files are:');?>";
	var upload_lang_download = "<?php echo $this->lang->line('Download');?>";

	var support_lang_success = '<?php echo $this->lang->line("Success"); ?>';
	var support_lang_error = '<?php echo $this->lang->line("Error"); ?>';
	var support_lang_no_data_found = '<?php echo $this->lang->line("No data found"); ?>';
	var support_lang_ticket_delete_confirm = '<?php echo $this->lang->line("Do you really want to delete it?");?>';
	var support_lang_are_you_sure = '<?php echo $this->lang->line("Are you sure?");?>';


	var addon_manager_lang_alert = '<?php echo $this->lang->line("Alert");?>';
	var addon_manager_lang_deactive_addon = '<?php echo $this->lang->line("Deactive Add-on?");?>';
	var addon_manager_lang_deactive_addon_confirmation = '<?php echo $this->lang->line("Do you really want to deactive this add-on? Your add-on data will still remain.");?>';
	var addon_manager_lang_delete_addon = '<?php echo $this->lang->line("Delete Add-on?");?>';
	var addon_manager_lang_delete_addon_confirmation = '<?php echo $this->lang->line("Do you really want to delete this add-on? This process can not be undone.");?>';
	var addon_manager_lang_delete_url = '<?php echo base_url("addons/delete_uploaded_zip");?>';

	var announcement_lang_mark_seen_confirmation = '<?php echo $this->lang->line("Do you really want to mark all unseen notifications as seen?");?>';

	var user_manager_lang_not_selected = '<?php echo $this->lang->line("You have to select users to send email.");?>';
	var package_manager_lang_cannot_deleted = '<?php echo $this->lang->line("Default package can not be deleted.");?>';

	var language_manager_lang_alert1 = '<?php echo $this->lang->line("Please put a language name & then save.");?>';
	var language_manager_lang_alert2 = '<?php echo $this->lang->line("Please put a language name & save it first.");?>';
	var language_manager_lang_download = '<?php echo $this->lang->line("Download Language");?>';
	var language_manager_lang_delete = '<?php echo $this->lang->line("Delete Language");?>';
	var language_manager_lang_cannot_delete = '<?php echo $this->lang->line("Sorry, english language can not be deleted.");?>';
	var language_manager_lang_cannot_delete_default = '<?php echo $this->lang->line("This is your default language, it can not be deleted.");?>';
	var language_manager_lang_cannot_delete_confirmation = '<?php echo $this->lang->line("Delete Language?");?>';
	var language_manager_lang_cannot_delete_confirmation_msg = '<?php echo $this->lang->line("Do you really want to delete this language? It will delete all files of this language.");?>';
	var language_manager_lang_cannot_delete_success_msg = '<?php echo $this->lang->line("Your language file has been successfully deleted.");?>';
	var language_manager_lang_only_char_allowed = '<?php echo $this->lang->line("Only characters and underscores are allowed.");?>';
	var language_manager_lang_language_exist = '<?php echo $this->lang->line("Sorry, this language already exists, you can not add this again.");?>';
	var language_manager_lang_language_exist_try = '<?php echo $this->lang->line("This language is already exist, please try with different one.");?>';
	var language_manager_lang_language_exist_update = '<?php echo $this->lang->line("This language already exist, no need to update.");?>';
	var language_manager_lang_update_name_first = '<?php echo $this->lang->line("Your given name has not updated, please update the name first.");?>';
	var language_manager_lang_selected_lang = '<?php echo $this->session->userdata("selected_language");?>';
	var language_manager_lang_editable_language = '<?php echo $this->uri->segment(3);?>';

	var smtp_settings_lang_test_mail_sent = '<?php echo $this->lang->line("Test email has been sent successfully.");?>';

	var import_account_bot_restart = '<?php echo $this->lang->line("Re-start Bot Connection");?>';
	var import_account_bot_restart_confirm = '<?php echo $this->lang->line("Do you really want to re-start Bot Connection for this page?");?>';
	var import_account_bot_enable = '<?php echo $this->lang->line("Enable Bot Connection");?>';
	var import_account_bot_enable_confirm = '<?php echo $this->lang->line("Do you really want to enable Bot Connection for this page?");?>';
	var import_account_bot_disable = '<?php echo $this->lang->line("Disable Bot Connection");?>';
	var import_account_bot_disable_confirm = '<?php echo $this->lang->line("Do you really want to disable Bot Connection for this page?");?>';
	var import_account_bot_delete = '<?php echo $this->lang->line("Delete Bot Connection & all settings");?>';
	var import_account_bot_delete_confirm = '<?php echo $this->lang->line("By proceeding, it will delete all settings of messenger bot, auto reply campaign, posting campaign, subscribers and all campaign reports of this page. This data can not be retrived. It will not delete the page itself from the system.");?>';
	var import_account_group_delete_confirm = '<?php echo $this->lang->line("If you delete this group, all the campaigns corresponding to this group will also be deleted. Do you want to delete this group from database?");?>';
	var import_account_page_delete_confirm = '<?php echo $this->lang->line("If you delete this page, all the campaigns corresponding to this page will also be deleted. Do you want to delete this page from database?");?>';
	var import_account_delete_confirm = '<?php echo $this->lang->line("If you delete this account, all the pages, groups and all the campaigns corresponding to this account will also be deleted form database. do you want to delete this account from database?");?>';
	var import_account_gb_numberic_id = '<?php echo $this->lang->line("Please enter your facebook numeric id first");?>';

	var fb_settings_lang_make_active = '<?php echo $this->lang->line("Make this app active");?>';
	var fb_settings_lang_make_inactive = '<?php echo $this->lang->line("Make this app inactive");?>';
	var fb_settings_lang_add_app = '<?php echo $this->lang->line("Add App");?>';
	var fb_settings_lang_edit_app = '<?php echo $this->lang->line("Edit App");?>';
	var fb_settings_lang_change_app_state_confirmation = '<?php echo $this->lang->line("Do you really want to change this apps state?");?>';
	var fb_settings_lang_delete_app_confirmation = '<?php echo $this->lang->line("Do you really want to delete this app?");?>';
	var google_settings_lang_delete_app_confirmation = '<?php echo $this->lang->line("Do you really want to delete this app? Deleting app will delete all related channels and campaigns.");?>';

	var theme_manager_lang_activation = '<?php echo $this->lang->line("Theme Activation");?>';
	var theme_manager_lang_activation_confirmation = '<?php echo $this->lang->line("Do you really want to activate this Theme?");?>';
	var theme_manager_lang_deactivation = '<?php echo $this->lang->line("Theme Deactivation");?>';
	var theme_manager_lang_deactivation_confirmation = '<?php echo $this->lang->line("Do you really want to deactivate this Theme? Your theme data will still remain");?>';
	var theme_manager_lang_delete_confirmation = '<?php echo $this->lang->line("Do you really want to delete this Theme? This process can not be undone.");?>';

	var account_list_delete_confirmation = '<?php echo $this->lang->line("Do you really want to delete this account?");?>';

	var upload_lang_error_msg1 = '<?php echo $this->lang->line("Please provide video title");?>';
	var upload_lang_error_msg2 = '<?php echo $this->lang->line("Please select a youtube Channel");?>';
	var upload_lang_error_msg3 = '<?php echo $this->lang->line("Please select video category");?>';
	var upload_lang_error_msg4 = '<?php echo $this->lang->line("Please select video privacy type");?>';
	var upload_lang_error_msg5 = '<?php echo $this->lang->line("Please select time zone");?>';
	var upload_lang_error_msg6 = '<?php echo $this->lang->line("Please select schedule date time");?>';
	var upload_lang_error_msg7 = '<?php echo $this->lang->line("Please upload video");?>';
	var upload_lang_error_msg8 = '<?php echo $this->lang->line("This video has no title");?>';
	var upload_lang_error_msg9 = '<?php echo $this->lang->line("No title");?>';
	var upload_lang_success_msg = '<?php echo $this->lang->line("Video data has been stored successfully and will be processed at scheduled time.");?>';
	var upload_lang_update_video = '<?php echo $this->lang->line("Update Schedule Video");?>';


	var menu_manager_all_menu = '<?php echo isset($all_menu) ? $all_menu : "";?>';
	var menu_manager_restore_confirm = '<?php echo $this->lang->line("Are you sure about reseting your menus to default state?");?>';
	var menu_manager_name_required = '<?php echo $this->lang->line("Menu Name is Required");?>';
	var menu_manager_icon_required = '<?php echo $this->lang->line("Menu Icon must not be empty icon");?>';
	var menu_manager_page_created = '<?php echo $this->lang->line("Page has been created successfully.");?>';
	var menu_manager_page_updated = '<?php echo $this->lang->line("Page has been updated successfully.");?>';
	var menu_manager_page_deleted = '<?php echo $this->lang->line("Page has been deleted successfully.");?>';
	var menu_manager_pages_deleted = '<?php echo $this->lang->line("Selected pages has been deleted Successfully");?>';
	var menu_manager_page_not_selected = '<?php echo $this->lang->line("You did not select any page to delete.");?>';
	var notAllowed = '<?php echo $this->lang->line("Menu having link cannot be used as parent.") ?>';
    var three_level_allowed = '<?php echo $this->lang->line('Third level menu is not allowed.') ?>';
    var drag_drop_not_allowed = '<?php echo $this->lang->line('System default menu cannot be re-ordered.') ?>';

    var payment_is_manaual_payment =  '<?php echo isset($manual_payment) ? $manual_payment : "";?>';
    var payment_sslcommers_mode =  '<?php echo isset($sslcommers_mode) ? $sslcommers_mode : "";?>';
    var payment_ssl_post_data = '<?php echo isset($postdata_array) ? $postdata_array : "";?>';
    var payment_has_reccuring = '<?php echo isset($has_reccuring) ? $has_reccuring : "";?>';
    var payment_lang_subscription_message = '<?php echo $this->lang->line('Subscription Message') ?>';
    var payment_lang_subscription_message_deatils = '<?php echo $this->lang->line('You have already a subscription enabled in paypal. If you want to use different paypal or different package, make sure to cancel your previous subscription from your paypal.') ?>';


    var facebook_app_delete_confirm = '<?php echo $this->lang->line('If you delete this APP then, all the imported Facebook accounts and their Pages and Campaigns will be deleted too corresponding to this APP.') ?>';
    var google_app_delete_confirm = '<?php echo $this->lang->line('If you delete this APP then, all the imported Google accounts and their Pages and Campaigns will be deleted too corresponding to this APP.') ?>';
    var google_app_status_change_confirm = '<?php echo $this->lang->line('If you change this APP status to inactive then, all the imported Google accounts and Campaigns will not work corresponding to this APP.') ?>';

	var dashboard_step_size = '<?php echo isset($step_size) ? $step_size : 1;?>';
	var dashboard_image_video_compare_list = <?php echo isset($image_video_compare_list) ? json_encode(array_values($image_video_compare_list)) : '[]';?>;
	var dashboard_image_post_list = <?php echo isset($image_post_list) ? json_encode(array_values($image_post_list)) : '[]';?>;
	var dashboard_video_post_list = <?php echo isset($video_post_list) ? json_encode(array_values($video_post_list)) : '[]';?>;

	var calendar_events = <?php echo isset($calendar_data) ? json_encode($calendar_data) : '[]';?>;

	var instragram_post_post_type ="<?php echo isset($all_data[0]["post_type"]) ? $all_data[0]["post_type"] : "";;?>";
	var instragram_post_warning_upload_message = "<?php echo $this->lang->line('Please type a message to post.');?>";
	var instragram_post_warning_upload_link = "<?php echo $this->lang->line('Please paste a link to post.');?>";
	var instragram_post_warning_upload_image = "<?php echo $this->lang->line('Please paste an image url or upload an image to post.');?>";
	var instragram_post_warning_upload_video = "<?php echo $this->lang->line('Please paste an video url or upload an video to post.');?>";
	var instragram_post_warning_select_account = "<?php echo $this->lang->line('Please select any page/group/account to publish this post.');?>";
	var instragram_post_warning_schedule_timezone = "<?php echo $this->lang->line('Please select schedule time/time zone.');?>";
	var instragram_post_message_see_report = "<?php echo $this->lang->line('Click here to see report');?>";
	var instragram_post_delete_main_confirm = "<?php echo $this->lang->line('This is main campaign, if you want to delete it, rest of the sub campaign will be deleted. Do you really want to delete this post from the database?');?>";
	var instragram_post_message_sorry1 = "<?php echo $this->lang->line('Sorry, Only parent campaign has shown report.');?>";
	var instragram_post_message_sorry2 = "<?php echo $this->lang->line('Sorry, this post is not published yet.');?>";
	var instragram_post_message_sorry3 = "<?php echo $this->lang->line('Sorry, Only Pending Campaigns Are Editable.');?>";
	var instragram_post_message_sorry4 = "<?php echo $this->lang->line('Sorry, Processing Campaign Can not be deleted.');?>";
	var instragram_post_message_sorry5 = "<?php echo $this->lang->line('Sorry, Embed code is only available for published video posts.');?>";
	var instragram_post_image_upload_limit = "<?php echo isset($image_upload_limit)?$image_upload_limit:1; ?>";
    var instragram_post_video_upload_limit = "<?php echo isset($video_upload_limit)?$video_upload_limit:100; ?>";

    var instagram_all_auto_comment_report_doyouwanttopausethiscampaign = '<?php echo $this->lang->line("do you want to pause this campaign?");?>';
	var instagram_all_auto_comment_report_doyouwanttostartthiscampaign = '<?php echo $this->lang->line("do you want to start this campaign?");?>';
	var instagram_all_auto_comment_report_doyouwanttodeletethisrecordfromdatabase = '<?php echo $this->lang->line("do you want to delete this record from database?");?>';
	var instagram_all_auto_comment_report_youdidntselectanyoption = '<?php echo $this->lang->line("you did not select any option.");?>';
	var instagram_all_auto_comment_report_youdidntprovideallinformation = '<?php echo $this->lang->line("you did not provide all information.");?>';
	var instagram_all_auto_comment_report_doyouwanttostarthiscampaign = '<?php echo $this->lang->line("do you want to start this campaign?");?>';
	var instagram_all_auto_comment_report_doyoureallywanttoreprocessthiscampaign = '<?php echo $this->lang->line("Force Reprocessing means you are going to process this campaign again from where it ended. You should do only if you think the campaign is hung for long time and did not send message for long time. It may happen for any server timeout issue or server going down during last attempt or any other server issue. So only click OK if you think message is not sending. Are you sure to Reprocessing ?");?>';
	var instagram_all_auto_comment_report_alreadyenabled = '<?php echo $this->lang->line("this campaign is already enable for processing.");?>';
	var instagram_all_auto_comment_report_typeautocampaignname = '<?php echo $this->lang->line("You did not Type auto campaign name");?>';
	var instagram_all_auto_comment_report_youdidnotchosescheduletype = '<?php echo $this->lang->line("You did not choose any schedule type");?>';
	var instagram_all_auto_comment_report_youdidnotchosescheduletime = '<?php echo $this->lang->line("You did not select any schedule time");?>';
	var instagram_all_auto_comment_report_youdidnotchosescheduletimezone = '<?php echo $this->lang->line("You did not select any time zone");?>';
	var instagram_all_auto_comment_report_youdidnotselectperodictime = '<?php echo $this->lang->line("You did not select any periodic time");?>';
	var instagram_all_auto_comment_report_youdidnotselectcampaignstarttime = '<?php echo $this->lang->line("You did not choose campaign start time");?>';
	var instagram_all_auto_comment_report_youdidnotselectcampaignendtime = '<?php echo $this->lang->line("You did not choose campaign end time");?>';
	var instagram_all_auto_comment_report_youdidntselectanytemplate = '<?php echo $this->lang->line("you did not select any template.");?>';
	var instagram_all_auto_comment_report_youdidntselectanyoptionyet = '<?php echo $this->lang->line("you did not select any option yet.");?>';
	var instagram_all_auto_comment_report_please_select_comment_between_times = '<?php echo $this->lang->line("Please select comment between times.");?>';
	var instagram_all_auto_comment_report_comment_between_start_time_must_be_less_than_end_time = '<?php echo $this->lang->line("Comment between start time must be less than end time.");?>';
	var instagram_all_auto_comment_report_post_id = "<?php echo isset($post_id)?$post_id:'0'; ?>";
	var instagram_all_auto_comment_report_page_id = "<?php echo isset($page_id)?$page_id:'0'; ?>";

	var instagram_auto_comment_template_youdidntselectanyoption = '<?php echo $this->lang->line("you did not select any option.");?>';
	var instagram_auto_comment_template_youdidntprovideallcomment = '<?php echo $this->lang->line("You did not provide comment information ");?>';
	var instagram_auto_comment_template_autocomment = '<?php echo $this->lang->line("auto comment");?>';
	var instagram_auto_comment_template_addcomments = '<?php echo $this->lang->line("add comments");?>';
	var instagram_auto_comment_template_please_give_the_following_information_for_post_auto_comment = '<?php echo $this->lang->line("Please Give The Following Information For Post Auto Comment");?>';
	var instagram_auto_comment_template_can_not_delete_from_admin = '<?php echo $this->lang->line("You can not delete templates from admin account");?>';
	var instagram_auto_comment_template_deleted_successfully = '<?php echo $this->lang->line("Template has been deleted successfully.");?>';


	var instagram_template_manager_youdidntprovideallinformation = '<?php echo $this->lang->line("you didn't provide all information.");?>';
	var instagram_template_manager_pleaseprovidepostid = '<?php echo $this->lang->line("please provide post id.");?>';
	var instagram_template_manager_alreadyenabled = '<?php echo $this->lang->line("already enabled");?>';
	var instagram_template_manager_thispostidisnotfoundindatabaseorthispostidisnotassociatedwiththepageyouareworking = '<?php echo $this->lang->line("This post ID is not found in database or this post ID is not associated with the page you are working.");?>';
	var instagram_template_manager_enableautoreply = '<?php echo $this->lang->line("enable auto reply");?>';


	var instagram_hash_tag_select_account = '<?php echo $this->lang->line("Please select an instagram account");?>';
	var instagram_hash_tag_provide_hash_tag = '<?php echo $this->lang->line("Please provide hash tag");?>';

	var instagram_selectanaccount = '<?php echo $this->lang->line("Please select an account"); ?>';
	var instagram_selectanaccountfirst = '<?php echo $this->lang->line("Please select an account first"); ?>';
	var instagram_meta_info_grabber_url = '<?php echo site_url();?>ultrapost/text_image_link_video_meta_info_grabber';

	var selected_global_page_table_id = '<?php echo $this->session->userdata("selected_global_page_table_id");?>';
	var selected_global_media_type = '<?php echo $this->session->userdata("selected_global_media_type");?>';
</script>