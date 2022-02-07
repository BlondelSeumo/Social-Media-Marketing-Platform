SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";


CREATE TABLE IF NOT EXISTS `add_ons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add_on_name` varchar(255) NOT NULL,
  `unique_name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `installed_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `purchase_code` varchar(100) NOT NULL,
  `module_folder_name` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`unique_name`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ad_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section1_html` longtext,
  `section1_html_mobile` longtext,
  `section2_html` longtext,
  `section3_html` longtext,
  `section4_html` longtext,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '0 means all',
  `is_seen` enum('0','1') NOT NULL DEFAULT '0',
  `seen_by` text NOT NULL COMMENT 'if user_id = 0 then comma seperated user_ids',
  `last_seen_at` datetime NOT NULL,
  `color_class` varchar(50) NOT NULL DEFAULT 'primary',
  `icon` varchar(50) NOT NULL DEFAULT 'fas fa-bell',
  `status` enum('published','draft') NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`id`),
  KEY `for_user_id` (`user_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `autoposting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `feed_name` varchar(100) NOT NULL,
  `feed_type` enum('rss','youtube','twitter','wordpress') NOT NULL DEFAULT 'rss',
  `feed_url` text NOT NULL,
  `youtube_channel_id` varchar(50) NOT NULL,
  `youtube_api_called_at` datetime DEFAULT NULL,
  `wordpress_blog_url` text,
  `page_ids` text NOT NULL COMMENT 'auto ids',
  `page_names` text NOT NULL COMMENT 'page names',
  `facebook_rx_fb_user_info_ids` text NOT NULL COMMENT 'page id => fb rx user id json',
  `twitter_accounts` text NOT NULL,
  `linkedin_accounts` text NOT NULL,
  `reddit_accounts` text NOT NULL,
  `subreddits` text NOT NULL,
  `posting_message` text CHARACTER SET utf8mb4 NOT NULL,
  `posting_start_time` varchar(25) NOT NULL,
  `posting_end_time` varchar(25) NOT NULL,
  `posting_timezone` varchar(75) NOT NULL,
  `page_id` int(11) NOT NULL COMMENT 'broadcast',
  `fb_page_id` varchar(75) NOT NULL COMMENT 'broadcast',
  `page_name` text NOT NULL COMMENT 'broadcast',
  `label_ids` text NOT NULL COMMENT 'broadcast',
  `excluded_label_ids` text NOT NULL COMMENT 'broadcast',
  `broadcast_start_time` varchar(50) NOT NULL,
  `broadcast_end_time` varchar(50) NOT NULL,
  `broadcast_timezone` varchar(50) NOT NULL,
  `broadcast_notification_type` varchar(100) NOT NULL DEFAULT 'REGULAR',
  `broadcast_display_unsubscribe` enum('0','1') NOT NULL DEFAULT '0',
  `last_pub_date` datetime NOT NULL,
  `last_pub_title` text NOT NULL,
  `last_pub_url` text NOT NULL,
  `status` enum('0','1','2') NOT NULL DEFAULT '1' COMMENT 'pending, processing, abandoned',
  `last_updated_at` datetime NOT NULL,
  `cron_status` enum('0','1') NOT NULL DEFAULT '0',
  `error_message` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`,`cron_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `auto_comment_reply_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `auto_comment_template_id` int(11) NOT NULL,
  `time_zone` varchar(255) NOT NULL,
  `schedule_time` datetime NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` mediumtext NOT NULL,
  `post_created_at` varchar(255) NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `auto_comment_count` int(11) NOT NULL,
  `periodic_time` varchar(255) NOT NULL,
  `schedule_type` varchar(255) NOT NULL,
  `auto_comment_type` varchar(255) NOT NULL,
  `campaign_start_time` datetime NOT NULL,
  `campaign_end_time` datetime NOT NULL,
  `comment_start_time` time NOT NULL,
  `comment_end_time` time NOT NULL,
  `auto_private_reply_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `auto_reply_done_info` longtext NOT NULL,
  `periodic_serial_reply_count` int(11) NOT NULL,
  `error_message` mediumtext NOT NULL,
  `post_description` longtext NOT NULL,
  `post_thumb` text NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  `social_media_type` enum('Facebook','Instagram') NOT NULL DEFAULT 'Facebook',
  `insta_media_url` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auto_comment_template_id` (`auto_comment_template_id`),
  KEY `auto_private_reply_status` (`auto_private_reply_status`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `auto_comment_reply_tb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_reply_comment_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_port` varchar(100) NOT NULL,
  `smtp_user` varchar(100) NOT NULL,
  `smtp_type` enum('Default','tls','ssl') NOT NULL DEFAULT 'Default',
  `smtp_password` varchar(100) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_template_management` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `template_type` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT 'fas fa-folder-open',
  `tooltip` text NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `template_type` (`template_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facebook_ex_autoreply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `auto_reply_campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` mediumtext COLLATE utf8mb4_unicode_ci,
  `post_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_permalink` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_created_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_description` longtext COLLATE utf8mb4_unicode_ci,
  `post_thumb` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_like_comment` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiple_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_reply_enabled` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nofilter_word_found_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_reply_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_count` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `error_message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_delete_offensive` enum('hide','delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `private_message_offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `template_manager_table_id` int(11) NOT NULL,
  `broadcaster_labels` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'auto_id of labels comma separated',
  `structured_message` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `trigger_matching_type` enum('exact','string') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`page_info_table_id`,`post_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `facebook_ex_conversation_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `excluded_label_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_names` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_gender` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_time_zone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_locale` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_type` enum('page-wise','lead-wise','group-wise') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'page-wise',
  `campaign_message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `posting_status` enum('0','1','2','3','4') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '4=hold(Stopped for Error count >)',
  `is_try_again` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `last_try_error_count` int(11) NOT NULL,
  `is_spam_caught` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `error_message` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_thread` int(11) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `added_at` datetime NOT NULL,
  `completed_at` datetime NOT NULL,
  `delay_time` int(11) NOT NULL DEFAULT '0' COMMENT '0 means random',
  PRIMARY KEY (`id`),
  KEY `status` (`posting_status`),
  KEY `dashboard` (`user_id`),
  KEY `dashboard2` (`user_id`,`completed_at`),
  KEY `dashboard3` (`user_id`,`posting_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `facebook_ex_conversation_campaign_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `client_thread_id` varchar(255) NOT NULL,
  `client_username` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `message_sent_id` varchar(255) NOT NULL,
  `sent_time` datetime NOT NULL,
  `lead_id` int(11) NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0',
  `link` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facebook_rx_auto_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `post_type` enum('text_submit','link_submit','image_submit','video_submit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text_submit',
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_group_user_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_or_group_or_user` enum('page','group','user') COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_or_group_or_user_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_preview_image` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_caption` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_thumb_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `og_action_type_id` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `og_object_id` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_to_profile` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_like_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_text` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
  `auto_private_reply_count` int(11) NOT NULL,
  `auto_private_reply_done_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_comment` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_comment_text` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posting_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'pending,processing,completed',
  `post_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'fb post id',
  `post_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_auto_comment_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto comment is done by cron job',
  `post_auto_like_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto like is done by cron job',
  `post_auto_share_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto share is done by cron job',
  `error_mesage` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_child` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `parent_campaign_id` int(11) NOT NULL,
  `page_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ig_page_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultrapost_auto_reply_table_id` int(11) NOT NULL,
  `instagram_reply_template_id` int(11) NOT NULL,
  `is_autopost` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `repeat_times` int(11) NOT NULL,
  `time_interval` int(11) NOT NULL,
  `full_complete` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_type` enum('facebook','instagram') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'facebook',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
  KEY `posting_status` (`posting_status`),
  KEY `dashboard` (`user_id`,`last_updated_at`),
  KEY `parent_campaign_id` (`parent_campaign_id`),
  KEY `page_group_user_id` (`page_group_user_id`),
  KEY `is_child` (`is_child`),
  KEY `schedule_type` (`schedule_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `facebook_rx_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) DEFAULT NULL,
  `api_id` varchar(250) DEFAULT NULL,
  `api_secret` varchar(250) DEFAULT NULL,
  `numeric_id` varchar(250) NOT NULL,
  `user_access_token` varchar(500) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `use_by` enum('only_me','everyone') NOT NULL DEFAULT 'only_me',
  `developer_access` enum('0','1') NOT NULL DEFAULT '0',
  `facebook_id` varchar(50) NOT NULL,
  `secret_code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facebook_rx_cta_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_group_user_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'auto_like_post_comment',
  `page_or_group_or_user` enum('page') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'cta post is only available for page',
  `page_or_group_or_user_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cta_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cta_value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_preview_image` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_caption` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `og_action_type_id` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `og_object_id` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_to_profile` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_like_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_text` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
  `auto_private_reply_count` int(11) NOT NULL,
  `auto_private_reply_done_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_comment` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_comment_text` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posting_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'pending,processing,completed',
  `post_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'fb post id',
  `post_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_auto_comment_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto comment is done by cron job',
  `post_auto_like_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto like is done by cron job',
  `post_auto_share_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'post''s auto share is done by cron job',
  `error_mesage` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_child` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `parent_campaign_id` int(11) NOT NULL,
  `page_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultrapost_auto_reply_table_id` int(11) NOT NULL,
  `repeat_times` int(11) NOT NULL,
  `time_interval` int(11) NOT NULL,
  `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_complete` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
  KEY `posting_status` (`posting_status`),
  KEY `dashboard` (`user_id`,`last_updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `facebook_rx_fb_group_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `group_id` varchar(200) NOT NULL,
  `group_cover` text,
  `group_profile` text,
  `group_name` varchar(200) DEFAULT NULL,
  `group_access_token` text NOT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rx_fb_user_info_group` (`facebook_rx_fb_user_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facebook_rx_fb_page_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `page_id` varchar(200) NOT NULL,
  `page_cover` text,
  `page_profile` text,
  `page_name` text,
  `username` varchar(255) NOT NULL,
  `page_access_token` text NOT NULL,
  `page_email` varchar(200) DEFAULT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `auto_sync_lead` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0=disabled,1=enabled,2=processing,3=completed',
  `last_lead_sync` datetime NOT NULL,
  `next_scan_url` text NOT NULL,
  `current_lead_count` int(11) NOT NULL,
  `current_subscribed_lead_count` int(11) NOT NULL,
  `current_unsubscribed_lead_count` int(11) NOT NULL,
  `msg_manager` enum('0','1') NOT NULL DEFAULT '0',
  `bot_enabled` enum('0','1','2') NOT NULL DEFAULT '0',
  `started_button_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `welcome_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_human_email` varchar(250) NOT NULL,
  `no_match_found_reply` enum('enabled','disabled') NOT NULL DEFAULT 'disabled',
  `persistent_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `enable_mark_seen` enum('0','1') NOT NULL DEFAULT '0',
  `enbale_type_on` enum('0','1') NOT NULL DEFAULT '0',
  `estimated_reach` varchar(50) NOT NULL,
  `last_estimaed_at` datetime NOT NULL,
  `review_status` enum('NOT SUBMITTED','PENDING','REJECTED','APPROVED','LIMITED') NOT NULL DEFAULT 'NOT SUBMITTED',
  `review_status_last_checked` datetime NOT NULL,
  `reply_delay_time` int(11) NOT NULL,
  `mail_service_id` text NOT NULL,
  `sms_api_id` int(11) NOT NULL,
  `sms_reply_message` text NOT NULL,
  `ice_breaker_status` enum('0','1') NOT NULL DEFAULT '0',
  `ice_breaker_questions` text NOT NULL,
  `email_api_id` varchar(100) NOT NULL,
  `email_reply_message` text NOT NULL,
  `email_reply_subject` text NOT NULL,
  `sequence_sms_api_id` int(11) NOT NULL,
  `sequence_sms_campaign_id` int(11) NOT NULL,
  `sequence_email_api_id` varchar(100) NOT NULL,
  `sequence_email_campaign_id` int(11) NOT NULL,
  `has_instagram` enum('0','1') NOT NULL DEFAULT '0',
  `instagram_business_account_id` varchar(100) NOT NULL,
  `insta_username` varchar(200) NOT NULL,
  `insta_followers_count` int(11) NOT NULL,
  `insta_media_count` int(11) NOT NULL,
  `insta_website` varchar(250) NOT NULL,
  `insta_biography` tinytext NOT NULL,
  `insta_current_subscribed_lead_count` int(11) NOT NULL,
  `insta_current_unsubscribed_lead_count` int(11) NOT NULL,
  `insta_current_lead_count` int(11) NOT NULL,
  `ig_ice_breaker_status` enum('0','1') NOT NULL DEFAULT '0',
  `ig_ice_breaker_questions` text NOT NULL,
  `ig_no_match_found_reply` enum('enabled','disabled') NOT NULL DEFAULT 'disabled',
  `ig_chat_human_email` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `rx_fb_user_info` (`facebook_rx_fb_user_info_id`),
  KEY `user_id` (`user_id`,`page_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facebook_rx_fb_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook_rx_config_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `fb_id` varchar(200) NOT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  `need_to_delete` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facebook_rx_slider_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `post_type` enum('slider_post','carousel_post') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'slider_post',
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `carousel_content` longtext COLLATE utf8mb4_unicode_ci,
  `carousel_link` mediumtext COLLATE utf8mb4_unicode_ci,
  `slider_images` longtext COLLATE utf8mb4_unicode_ci,
  `slider_image_duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slider_transition_duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_group_user_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_or_group_or_user` enum('page','group','user') COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_or_group_or_user_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_share_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_share_to_profile` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_like_post` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_text` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'taken by cronjob or not',
  `auto_private_reply_count` int(11) NOT NULL,
  `auto_private_reply_done_ids` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_comment` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_comment_text` mediumtext COLLATE utf8mb4_unicode_ci,
  `posting_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'pending,processing,completed',
  `post_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_auto_comment_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `post_auto_like_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `post_auto_share_cron_jon_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `error_mesage` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_child` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `parent_campaign_id` int(11) NOT NULL,
  `page_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultrapost_auto_reply_table_id` int(11) NOT NULL,
  `repeat_times` int(11) NOT NULL,
  `time_interval` int(11) NOT NULL,
  `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_complete` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`facebook_rx_fb_user_info_id`),
  KEY `posting_status` (`posting_status`),
  KEY `dashboard` (`user_id`,`last_updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `fb_simple_support_desk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ticket_title` text NOT NULL,
  `ticket_text` longtext NOT NULL,
  `ticket_status` enum('1','2','3') CHARACTER SET latin1 NOT NULL DEFAULT '1' COMMENT '1=> Open. 2 => Closed, 3 => Resolved',
  `display` enum('0','1') NOT NULL DEFAULT '1',
  `support_category` int(11) NOT NULL,
  `last_replied_by` int(11) NOT NULL,
  `last_replied_at` datetime NOT NULL,
  `last_action_at` datetime NOT NULL COMMENT 'close resolve reopen etc',
  `ticket_open_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `support_category` (`support_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `fb_support_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `fb_support_desk_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_reply_text` longtext NOT NULL,
  `ticket_reply_time` datetime NOT NULL,
  `reply_id` int(11) NOT NULL COMMENT 'ticket_id',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `forget_password` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `confirmation_code` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `success` int(11) NOT NULL DEFAULT '0',
  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `login_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) DEFAULT NULL,
  `api_key` varchar(250) DEFAULT NULL,
  `google_client_id` text,
  `google_client_secret` varchar(250) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `color` varchar(20) NOT NULL,
  `url` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `module_access` varchar(255) NOT NULL,
  `have_child` enum('1','0') NOT NULL DEFAULT '0',
  `only_admin` enum('1','0') NOT NULL DEFAULT '1',
  `only_member` enum('1','0') NOT NULL DEFAULT '0',
  `add_ons_id` int(11) NOT NULL,
  `is_external` enum('0','1') NOT NULL DEFAULT '0',
  `header_text` varchar(255) NOT NULL,
  `is_menu_manager` enum('0','1') NOT NULL DEFAULT '0',
  `custom_page_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `menu` (`id`, `name`, `icon`, `color`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES
(1, 'Dashboard', 'fa fa-fire', '#ff7979', 'dashboard', 1, '', '0', '0', '0', 0, '0', '', '0', 0),
(2, 'System', 'fas fa-cog', '#d35400', '', 50, '', '1', '1', '0', 0, '0', 'Administration', '0', 0),
(3, 'Subscription', 'fas fa-coins', '#ffa801', '', 50, '', '1', '1', '0', 0, '0', '', '0', 0),
(4, 'Facebook & Instagram', 'fab fa-facebook', '#0D8BF1', 'social_accounts/index', 5, '65', '0', '0', '0', 0, '0', 'Integrations', '0', 0),
(5, 'Comment Growth Tools', 'fas fa-comments', '#575fcf', 'comment_automation/comment_growth_tools', 14, '80,201,202,204,206,220,222,223,251,256,278,279', '0', '0', '0', 0, '0', 'Comment Feature', '0', 0),
(6, 'Subscriber Manager', 'fas fa-user-circle', '#D980FA', 'subscriber_manager/bot_subscribers', 21, '', '0', '0', '0', 0, '0', 'Messenger Tools', '0', 0),
(7, 'Live Chat', 'fas fa-comment-alt', '#32ff7e', 'subscriber_manager/livechat', 21, '', '0', '0', '0', 0, '0', '', '0', 0),
(8, 'Broadcasting', 'fas fa-paper-plane', '#0D8BF1', 'messenger_bot_broadcast', 29, '79,210,211,262,263,264', '0', '0', '0', 0, '0', '', '0', 0),
(9, 'Bot Manager', 'fas fa-project-diagram', '#B33771', 'messenger_bot/bot_list', 25, '197,198,199,211,213,214,215,217,218,219,257,258,260,261,262,265,266', '0', '0', '0', 0, '0', '', '0', 0),
(10, 'Ecommerce Store', 'fas fa-shopping-cart', '#FC427B', 'ecommerce', 30, '268', '0', '0', '0', 0, '0', 'Ecommerce', '0', 0),
(11, 'Social Posting', 'fa fa-share-square', '#a55eea', 'ultrapost', 33, '220,222,223,256,100', '0', '0', '0', 0, '0', 'Posting Feature', '0', 0),
(12, 'Search Tools', 'fas fa-search', '#218c74', 'search_tools', 37, '267', '0', '0', '0', 0, '0', 'Utility Tools', '0', 0),
(13, 'API Channels', 'fas fa-wifi', '#0D8BF1', 'integration', 5, '', '0', '0', '0', 0, '0', '', '0', 0);


CREATE TABLE IF NOT EXISTS `menu_child_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `module_access` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `have_child` enum('1','0') NOT NULL DEFAULT '0',
  `only_admin` enum('1','0') NOT NULL DEFAULT '1',
  `only_member` enum('1','0') NOT NULL DEFAULT '0',
  `is_external` enum('0','1') NOT NULL DEFAULT '0',
  `is_menu_manager` enum('0','1') NOT NULL DEFAULT '0',
  `custom_page_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES
(1, 'Settings', 'admin/settings', 1, 'fas fa-sliders-h', '', 2, '0', '1', '0', '0', '0', 0),
(3, 'Cron Job', 'cron_job/index', 9, 'fas fa-clipboard-list', '', 2, '0', '1', '0', '0', '0', 0),
(4, 'Language Editor', 'multi_language/index', 13, 'fas fa-language', '', 2, '0', '1', '0', '0', '0', 0),
(5, 'Add-on Manager', 'addons/lists', 17, 'fas fa-plug', '', 2, '0', '1', '0', '0', '0', 0),
(7, 'Check Update', 'update_system/update_list_v2', 21, 'fas fa-leaf', '', 2, '0', '1', '0', '0', '0', 0),
(8, 'Package Manager', 'payment/package_manager', 1, 'fas fa-shopping-bag', '', 3, '0', '1', '0', '0', '0', 0),
(9, 'User Manager', 'admin/user_manager', 5, 'fas fa-users', '', 3, '0', '1', '0', '0', '0', 0),
(10, 'Announcement', 'announcement/full_list', 9, 'far fa-bell', '', 3, '0', '1', '0', '0', '0', 0),
(12, 'Earning Summary', 'payment/earning_summary', 17, 'fas fa-tachometer-alt', '', 3, '0', '1', '0', '0', '0', 0),
(13, 'Transaction Log', 'payment/transaction_log', 27, 'fas fa-history', '', 3, '0', '1', '0', '0', '0', 0),
(46, 'Theme Manager', 'themes/lists', 19, 'fas fa-palette', '', 2, '0', '1', '0', '0', '0', 0),
(47, 'Blog Manager', 'blog/posts', 20, 'fas fa-newspaper', '', 2, '0', '1', '0', '0', '0', 0),
(48, 'Menu Manager', 'menu_manager/index', 20, 'fas fa-bars', '', 2, '0', '1', '0', '0', '0', 0);


CREATE TABLE IF NOT EXISTS `menu_child_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `module_access` varchar(255) NOT NULL,
  `parent_child` int(11) NOT NULL,
  `only_admin` enum('1','0') NOT NULL DEFAULT '1',
  `only_member` enum('1','0') NOT NULL DEFAULT '0',
  `is_external` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `messenger_bot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_type` enum('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel','media','One Time Notification','User Input Flow','Ecommerce') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `bot_type` enum('generic','keyword') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generic',
  `keyword_type` enum('reply','post-back','no match','get-started','email-quick-reply','phone-quick-reply','location-quick-reply','birthday-quick-reply','story-mention','story-private-reply','message-unsend-private-reply') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reply',
  `keywords` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `conditions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_condition_false` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `buttons` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `video` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `bot_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `postback_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_replied_at` datetime DEFAULT NULL,
  `is_template` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `broadcaster_labels` tinytext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma separated',
  `drip_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `media_type` enum('fb','ig') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fb',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `trigger_matching_type` enum('exact','string') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`),
  KEY `xbot_query` (`fb_page_id`(191),`keyword_type`,`postback_id`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `messenger_bot_broadcast_contact_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `label_id` varchar(250) DEFAULT NULL,
  `social_media` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `deleted` enum('0','1') DEFAULT '0',
  `unsubscribe` enum('0','1') NOT NULL DEFAULT '0',
  `invisible` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`,`group_name`,`social_media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_domain_whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `messenger_bot_user_info_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `domain` tinytext NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_persistent_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` varchar(100) NOT NULL,
  `locale` varchar(20) NOT NULL DEFAULT 'default',
  `item_json` longtext NOT NULL,
  `composer_input_disabled` enum('0','1') NOT NULL DEFAULT '0',
  `poskback_id_json` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`,`locale`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_postback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `postback_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `use_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `messenger_bot_table_id` int(11) NOT NULL,
  `bot_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_template` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_jsoncode` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_for` enum('reply_message','unsubscribe','resubscribe','email-quick-reply','phone-quick-reply','location-quick-reply','birthday-quick-reply','chat-with-human','chat-with-bot') COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` int(11) NOT NULL,
  `inherit_from_template` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `broadcaster_labels` tinytext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma separated',
  `drip_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `postback_type` enum('main','sub') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sub',
  `media_type` enum('fb','ig') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fb',
  `visual_flow_campaign_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`postback_id`,`page_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `messenger_bot_reply_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `error_message` varchar(250) NOT NULL,
  `bot_settings_id` int(11) NOT NULL,
  `error_time` datetime NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `page_id` varchar(100) NOT NULL,
  `permission` enum('0','1') NOT NULL DEFAULT '1',
  `subscribe_id` varchar(36) NOT NULL,
  `client_thread_id` varchar(255) NOT NULL,
  `contact_group_id` varchar(255) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `profile_pic` text NOT NULL,
  `gender` varchar(15) NOT NULL,
  `locale` varchar(15) NOT NULL,
  `timezone` varchar(15) NOT NULL,
  `unavailable` enum('0','1') NOT NULL DEFAULT '0',
  `last_error_message` text NOT NULL,
  `refferer_id` varchar(100) NOT NULL COMMENT 'get started refference number from ref parameter of chat plugin',
  `refferer_source` varchar(50) NOT NULL COMMENT 'checkbox_plugin or CUSTOMER_CHAT_PLUGIN or MESSENGER_CODE or SHORTLINK or FB PAGE or COMMENT PRIVATE REPLY',
  `refferer_uri` tinytext NOT NULL COMMENT 'CUSTOMER_CHAT_PLUGIN URL',
  `subscribed_at` datetime NOT NULL,
  `unsubscribed_at` datetime NOT NULL,
  `link` varchar(255) NOT NULL,
  `is_image_download` enum('0','1') NOT NULL DEFAULT '0',
  `image_path` varchar(250) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `is_bot_subscriber` enum('0','1') NOT NULL DEFAULT '1',
  `is_email_unsubscriber` enum('0','1') NOT NULL DEFAULT '0',
  `is_imported` enum('0','1') NOT NULL DEFAULT '0',
  `is_updated_name` enum('0','1') NOT NULL DEFAULT '1',
  `last_name_update_time` datetime NOT NULL,
  `email` varchar(75) NOT NULL,
  `phone_number` varchar(30) NOT NULL,
  `user_location` text NOT NULL,
  `birthdate` date NOT NULL,
  `last_subscriber_interaction_time` datetime NOT NULL COMMENT 'UTC Time - When user last sent message',
  `is_24h_1_sent` enum('0','1') NOT NULL DEFAULT '0' COMMENT '24H+1 message Broadcasting created or not',
  `woocommerce_drip_campaign_id` int(11) NOT NULL,
  `wc_user_id` int(11) NOT NULL,
  `password` text NOT NULL,
  `subscriber_type` enum('messenger','system') NOT NULL DEFAULT 'messenger',
  `store_id` int(11) NOT NULL,
  `social_media` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `cron_lock` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1 means it is being processed by cron job',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`page_id`,`subscribe_id`) USING BTREE,
  KEY `contact_group_id` (`contact_group_id`),
  KEY `email` (`email`),
  KEY `store_id` (`store_id`),
  KEY `idx_subscriber_id` (`subscribe_id`),
  KEY `subscribed_at` (`user_id`,`subscribed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(250) DEFAULT NULL,
  `add_ons_id` int(11) NOT NULL,
  `extra_text` varchar(50) NOT NULL DEFAULT 'month',
  `limit_enabled` enum('0','1') NOT NULL DEFAULT '1',
  `bulk_limit_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `native_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `api_key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(250) NOT NULL,
  `module_ids` text NOT NULL,
  `monthly_limit` text,
  `bulk_limit` text,
  `price` varchar(20) NOT NULL DEFAULT '0',
  `validity` int(11) NOT NULL,
  `validity_extra_info` varchar(255) NOT NULL DEFAULT '1,M',
  `is_default` enum('0','1') NOT NULL DEFAULT '0',
  `visible` enum('0','1') NOT NULL DEFAULT '1',
  `highlight` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `page_response_autoreply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_response_user_info_id` int(11) NOT NULL,
  `auto_reply_campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` mediumtext COLLATE utf8mb4_unicode_ci,
  `post_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_created_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_description` longtext COLLATE utf8mb4_unicode_ci,
  `reply_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_like_comment` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiple_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_reply_enabled` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nofilter_word_found_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_reply_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_private_reply_status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `auto_private_reply_count` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `error_message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_delete_offensive` enum('hide','delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `private_message_offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `pause_play` enum('play','pause') COLLATE utf8mb4_unicode_ci NOT NULL,
  `structured_message` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `trigger_matching_type` enum('exact','string') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`page_info_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_response_auto_like_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_response_auto_like_share_report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auto_like_page_table_id` text NOT NULL,
  `status` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `page_response_auto_like_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_response_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` text,
  `page_id` varchar(200) NOT NULL,
  `auto_share_post` enum('0','1') NOT NULL DEFAULT '0',
  `auto_share_this_post_by_pages` text NOT NULL,
  `delay_time` varchar(20) NOT NULL,
  `auto_like_post` enum('0','1') NOT NULL DEFAULT '0',
  `auto_like_this_post_by_pages` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`page_info_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `page_response_auto_like_share_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_response_auto_like_share_id` int(11) NOT NULL,
  `page_response_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` text,
  `page_id` varchar(200) NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `auto_share_post` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0 = no, 1 = yes, 2 = processing, 3=completed',
  `share_count` int(11) NOT NULL,
  `share_done` int(11) NOT NULL,
  `share_last_tried` datetime NOT NULL,
  `auto_share_this_post_by_pages` text NOT NULL,
  `auto_share_report` longtext,
  `delay_time` varchar(20) NOT NULL,
  `auto_like_post` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0 = no, 1 = yes, 2 = processing, 3=completed',
  `like_count` int(11) NOT NULL,
  `like_done` int(11) NOT NULL,
  `like_last_tried` datetime NOT NULL,
  `auto_like_this_post_by_pages` text NOT NULL,
  `auto_like_report` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `page_response_auto_share_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_response_auto_like_share_report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auto_share_page_table_id` text NOT NULL,
  `status` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `page_response_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_response_autoreply_id` int(11) NOT NULL,
  `auto_reply_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` text,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` longtext,
  `reply_type` varchar(200) NOT NULL,
  `auto_like_comment` enum('no','yes') NOT NULL,
  `multiple_reply` enum('no','yes') NOT NULL,
  `comment_reply_enabled` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `auto_private_reply_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `auto_private_reply_count` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `error_message` text NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `already_replied_comment_ids` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_response_autoreply_id` (`page_response_autoreply_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `payment_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paypal_email` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paypal_payment_type` enum('manual','recurring') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `paypal_mode` enum('live','sandbox') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'live',
  `stripe_secret_key` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_publishable_key` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay_key_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay_key_secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paystack_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paystack_public_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mercadopago_public_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mercadopago_access_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `marcadopago_country` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mollie_api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `manual_payment` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `manual_payment_instruction` mediumtext COLLATE utf8mb4_unicode_ci,
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `sslcommerz_store_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sslcommerz_store_password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sslcommers_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senangpay_merchent_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `senangpay_secret_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `senangpay_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo_api_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo_auth_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `toyyibpay_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `toyyibpay_category_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `toyyibpay_mode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `xendit_secret_api_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `myfatoorah_api_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `myfatoorah_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paymaya_public_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paymaya_secret_key` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paymaya_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `paypal_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `call_time` datetime DEFAULT NULL,
  `ipn_value` text,
  `error_log` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tag_machine_bulk_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(255) NOT NULL,
  `reply_content` text NOT NULL,
  `uploaded_image_video` varchar(255) NOT NULL,
  `reply_multiple` enum('0','1') DEFAULT '0',
  `report` longtext NOT NULL,
  `campaign_created` datetime NOT NULL,
  `posting_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `delay_time` int(11) NOT NULL,
  `is_try_again` enum('0','1') NOT NULL DEFAULT '1',
  `total_reply` int(11) NOT NULL,
  `schedule_type` enum('now','later') NOT NULL DEFAULT 'now',
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(255) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `last_try_error_count` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `tag_machine_enabled_post_list_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `page_profile` text NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` text,
  `error_message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posting_status` (`posting_status`,`is_try_again`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tag_machine_bulk_reply_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL COMMENT 'tag_machine_bulk_reply.id',
  `comment_id` varchar(255) NOT NULL,
  `commenter_fb_id` varchar(255) NOT NULL,
  `commenter_name` varchar(255) NOT NULL,
  `comment_time` datetime NOT NULL,
  `sent_time` datetime NOT NULL,
  `response` varchar(255) NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`,`processed`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tag_machine_bulk_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(255) NOT NULL,
  `tag_database` longtext NOT NULL,
  `tag_exclude` longtext NOT NULL,
  `tag_content` text NOT NULL,
  `uploaded_image_video` varchar(255) NOT NULL,
  `error_message` text NOT NULL,
  `tag_response` text NOT NULL,
  `schedule_type` enum('now','later') NOT NULL DEFAULT 'now',
  `schedule_time` datetime NOT NULL,
  `time_zone` varchar(255) NOT NULL,
  `campaign_created` datetime NOT NULL,
  `posting_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `last_updated_at` datetime NOT NULL,
  `tag_machine_enabled_post_list_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `page_profile` text NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` text,
  `commenter_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posting_status` (`posting_status`),
  KEY `facebook_rx_fb_user_info_id` (`facebook_rx_fb_user_info_id`,`page_info_table_id`,`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tag_machine_commenter_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_machine_enabled_post_list_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `post_id` varchar(200) NOT NULL,
  `last_comment_id` varchar(255) NOT NULL,
  `last_comment_time` varchar(255) NOT NULL,
  `commenter_fb_id` varchar(255) NOT NULL,
  `commenter_name` varchar(255) NOT NULL,
  `subscribed` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_machine_enabled_post_list_id` (`tag_machine_enabled_post_list_id`,`commenter_fb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tag_machine_comment_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_machine_enabled_post_list_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `post_id` varchar(200) NOT NULL,
  `comment_id` varchar(255) NOT NULL,
  `comment_text` text NOT NULL,
  `commenter_fb_id` varchar(255) NOT NULL,
  `commenter_name` varchar(255) NOT NULL,
  `comment_time` varchar(255) NOT NULL,
  `subscribed` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_machine_enabled_post_list_id` (`tag_machine_enabled_post_list_id`,`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tag_machine_enabled_post_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL COMMENT 'facebook page id',
  `page_name` varchar(255) DEFAULT NULL,
  `page_profile` text NOT NULL,
  `post_id` varchar(200) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` text,
  `last_updated_at` datetime NOT NULL,
  `commenter_count` int(11) NOT NULL,
  `comment_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facebook_rx_fb_user_info_id` (`facebook_rx_fb_user_info_id`,`page_info_table_id`,`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `transaction_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verify_status` varchar(200) NOT NULL,
  `first_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `paypal_email` varchar(200) NOT NULL,
  `receiver_email` varchar(200) NOT NULL,
  `country` varchar(100) NOT NULL,
  `payment_date` varchar(100) NOT NULL,
  `payment_type` varchar(100) NOT NULL,
  `transaction_id` varchar(150) NOT NULL,
  `paid_amount` float NOT NULL,
  `user_id` int(11) NOT NULL,
  `cycle_start_date` date NOT NULL,
  `cycle_expired_date` date NOT NULL,
  `package_id` int(11) NOT NULL,
  `stripe_card_source` text NOT NULL,
  `paypal_txn_type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ultrapost_auto_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ultrapost_campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reply_type` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_like_comment` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiple_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_reply_enabled` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nofilter_word_found_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_reply_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_delete_offensive` enum('hide','delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `private_message_offensive_words` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_ids` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `structured_message` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `trigger_matching_type` enum('exact','string') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  `page_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `update_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `files` text NOT NULL,
  `sql_query` text NOT NULL,
  `update_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `usage_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `usage_month` int(11) NOT NULL,
  `usage_year` year(4) NOT NULL,
  `usage_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(99) NOT NULL,
  `email` varchar(99) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `password` varchar(99) NOT NULL,
  `address` text NOT NULL,
  `user_type` enum('Member','Admin') NOT NULL,
  `status` enum('1','0') NOT NULL,
  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `purchase_date` datetime NOT NULL,
  `last_login_at` datetime NOT NULL,
  `activation_code` varchar(20) DEFAULT NULL,
  `expired_date` datetime NOT NULL,
  `bot_status` enum('0','1') NOT NULL DEFAULT '1',
  `package_id` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  `brand_logo` text,
  `brand_url` text,
  `vat_no` varchar(100) DEFAULT NULL,
  `currency` enum('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK','BRL') NOT NULL DEFAULT 'USD',
  `time_zone` varchar(255) DEFAULT NULL,
  `company_email` varchar(200) DEFAULT NULL,
  `paypal_email` varchar(100) NOT NULL,
  `paypal_subscription_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `last_payment_method` varchar(50) NOT NULL,
  `last_login_ip` varchar(25) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_login_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(12) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `login_time` datetime NOT NULL,
  `login_ip` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `current` enum('1','0') NOT NULL DEFAULT '1',
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`),
  KEY `Current` (`current`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `add_ons` (`id`, `add_on_name`, `unique_name`, `version`, `installed_at`, `update_at`, `purchase_code`, `module_folder_name`, `project_id`) VALUES
(1, 'Facebook Poster', 'ultrapost', '1.0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 'ultrapost', 19);
INSERT IGNORE INTO `add_ons` ( `add_on_name`, `unique_name`, `version`, `installed_at`, `update_at`, `purchase_code`, `module_folder_name`, `project_id`) VALUES
('Visual Flow Builder', 'visual_flow_builder', '1.0', '2022-01-19 12:00:00', '2022-01-19 12:00:00', '', 'visual_flow_builder', 59),
('Instagram Bot & Private Reply', 'instagram_bot', '1.0', '2022-01-19 12:00:00', '2022-01-19 12:00:00', '', 'instagram_bot', 62);

INSERT INTO `email_template_management` (`id`, `title`, `template_type`, `subject`, `message`, `icon`, `tooltip`, `info`) VALUES
(1, 'Signup Activation', 'signup_activation', '#APP_NAME# | Account Activation', '<p>To activate your account please perform the following steps :</p>\r\n<ol>\r\n<li>Go to this url : #ACTIVATION_URL#</li>\r\n<li>Enter this code : #ACCOUNT_ACTIVATION_CODE#</li>\r\n<li>Activate your account</li>\r\n</ol>', 'fas fa-skating', '#APP_NAME#,#ACTIVATION_URL#,#ACCOUNT_ACTIVATION_CODE#', 'When a new user open an account'),
(2, 'Reset Password', 'reset_password', '#APP_NAME# | Password Recovery', '<p>To reset your password please perform the following steps :</p>\r\n<ol>\r\n<li>Go to this url : #PASSWORD_RESET_URL#</li>\r\n<li>Enter this code : #PASSWORD_RESET_CODE#</li>\r\n<li>reset your password.</li>\r\n</ol>\r\n<h4>Link and code will be expired after 24 hours.</h4>', 'fas fa-retweet', '#APP_NAME#,#PASSWORD_RESET_URL#,#PASSWORD_RESET_CODE#', 'When a user forget login password'),
(3, 'Change Password', 'change_password', 'Change Password Notification', 'Dear #USERNAME#,<br/> \r\nYour <a href="#APP_URL#">#APP_NAME#</a> password has been changed.<br>\r\nYour new password is: #NEW_PASSWORD#.<br/><br/> \r\nThank you,<br/>\r\n<a href="#APP_URL#">#APP_NAME#</a> Team', 'fas fa-key', '#APP_NAME#,#APP_URL#,#USERNAME#,#NEW_PASSWORD#', 'When admin reset password of any user'),
(4, 'Subscription Expiring Soon', 'membership_expiration_10_days_before', 'Payment Alert', 'Dear #USERNAME#,\r\n<br/> Your account will expire after 10 days, Please pay your fees.<br/><br/>\r\nThank you,<br/>\r\n<a href="#APP_URL#">#APP_NAME#</a> Team', 'fas fa-clock', '#APP_NAME#,#APP_URL#,#USERNAME#', '10 days before user subscription expires'),
(5, 'Subscription Expiring Tomorrow', 'membership_expiration_1_day_before', 'Payment Alert', 'Dear #USERNAME#,<br/>\r\nYour account will expire tomorrow, Please pay your fees.<br/><br/>\r\nThank you,<br/>\r\n<a href="#APP_URL#">#APP_NAME#</a> Team', 'fas fa-stopwatch', '#APP_NAME#,#APP_URL#,#USERNAME#', '1 day before user subscription expires'),
(6, 'Subscription Expired', 'membership_expiration_1_day_after', 'Subscription Expired', 'Dear #USERNAME#,<br/>\r\nYour account has been expired, Please pay your fees for continuity.<br/><br/>\r\nThank you,<br/>\r\n<a href="#APP_URL#">#APP_NAME#</a> Team', 'fas fa-user-clock', '#APP_NAME#,#APP_URL#,#USERNAME#', 'Subscription is already expired of a user'),
(7, 'Paypal Payment Confirmation', 'paypal_payment', 'Payment Confirmation', 'Congratulations,<br/> \r\nWe have received your payment successfully.<br/>\r\nNow you are able to use #PRODUCT_SHORT_NAME# system till #CYCLE_EXPIRED_DATE#.<br/><br/>\r\nThank you,<br/>\r\n<a href="#SITE_URL#">#APP_NAME#</a> Team', 'fab fa-paypal', '#APP_NAME#,#CYCLE_EXPIRED_DATE#,#PRODUCT_SHORT_NAME#,#SITE_URL#', 'User pay through Paypal & gets confirmation'),
(8, 'Paypal New Payment', 'paypal_new_payment_made', 'New Payment Made', 'New payment has been made by #PAID_USER_NAME#', 'fab fa-cc-paypal', '#PAID_USER_NAME#', 'User pay through Paypal & admin gets notified'),
(9, 'Stripe Payment Confirmation', 'stripe_payment', 'Payment Confirmation', 'Congratulations,<br/>\r\nWe have received your payment successfully.<br/>\r\nNow you are able to use #APP_SHORT_NAME# system till #CYCLE_EXPIRED_DATE#.<br/><br/>\r\nThank you,<br/>\r\n<a href="#APP_URL#">#APP_NAME#</a> Team', 'fab fa-stripe-s', '#APP_NAME#,#CYCLE_EXPIRED_DATE#,#PRODUCT_SHORT_NAME#,#SITE_URL#', 'User pay through Stripe & gets confirmation'),
(10, 'Stripe New Payment', 'stripe_new_payment_made', 'New Payment Made', 'New payment has been made by #PAID_USER_NAME#', 'fab fa-cc-stripe', '#PAID_USER_NAME#', 'User pay through Stripe & admin gets notified'),
(11, 'Ecommerce Order Received', 'emcommerce_sale_admin', '#STORE_NAME# | A New Order Has Been Submitted', 'Congratulations,<br/>\r\nYou have got an new order on your store #STORE_NAME#.<br>\r\nInvoice : #INVOICE_URL# <br/><br/>\r\n\r\nThank you,<br/>\r\n<a href="#APP_URL#">#APP_NAME#</a> Team', 'fas fa-dollar-sign', '#STORE_NAME#,#APP_URL#,#APP_NAME#,#INVOICE_URL#', 'You have got an new order on your store'),
(13, 'New Webview Form Submission Alert', 'webview_form_submit_admin', '#FORM_TITLE# | #SUBSCRIBER_NAME# Has Submitted Form', '#SUBSCRIBER_NAME# has just submitted your form #FORM_TITLE# with below data. <br/><br/>\r\n#FORM_DATA#\r\n<br/><br/>\r\nThank you,<br/>\r\n<a href="#APP_URL#">#APP_NAME#</a> Team', 'fas fa-digital-tachograph', '#FORM_TITLE#,#SUBSCRIBER_NAME#,#SUBSCRIBER_NAME#,#FORM_TITLE#,#APP_URL#,#APP_NAME#', 'Subscriber information received');

INSERT INTO `fb_support_category` (`id`, `category_name`, `user_id`, `deleted`) VALUES
(1, 'Billing', 1, '0'),
(2, 'Technical', 1, '0'),
(3, 'Query', 1, '0');

INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES
(65, 'Facebook Accounts', 0, '', '1', '0', '0'),
(78, 'Subscriber Manager : Background Lead Scan', 0, '', '0', '0', '0'),
(79, 'Conversation Promo Broadcast Send', 0, 'month', '1', '1', '0'),
(80, 'Comment Automation : Auto Reply Posts', 0, 'month', '1', '0', '0'),
(82, 'Inbox Conversation Manager', 0, '', '0', '0', '0'),
(197, 'Messenger Bot - Persistent Menu', 0, '', '0', '0', '0'),
(198, 'Messenger Bot - Persistent Menu : Copyright Enabled', 0, '', '0', '0', '0'),
(199, 'Messenger Bot', 0, '', '0', '0', '0'),
(200, 'Facebook Pages', 0, '', '1', '0', '0'),
(220, 'Facebook Posting : CTA Post', 0, 'month', '1', '0', '0'),
(222, 'Facebook Posting : Carousel/Slider Post', 0, 'month', '1', '0', '0'),
(223, 'Facebook Posting :  Text/Image/Link/Video Post', 0, 'month', '1', '0', '0'),
(251, 'Comment Automation : Auto Comment Campaign', 0, '', '1', '0', '0'),
(256, 'RSS Auto Posting', 0, '', '1', '0', '0'),
(257, 'Messenger Bot : Export, Import & Tree View', 0, '', '1', '', '0'),
(264, 'SMS Broadcast - SMS Send', 0, 'month', '1', '0', '0'),
(265, 'Messenger Bot - Email Auto Responder', 0, '', '1', '0', '0'),
(267, 'Utility Search Tools', 0, 'month', '1', '0', '0'),
(263, 'Email Broadcast - Email Send', '0', 'month', '1', '0', '0'),
(33, 'Social Poster - Account Import : Youtube', 0, '', '1', '0', '0'),
(100, 'Social Poster - Access', 0, '', '0', '0', '0'),
(101, 'Social Poster - Account Import : Pinterest', 0, '', '1', '0', '0'),
(102, 'Social Poster - Account Import : Twitter', 0, '', '1', '0', '0'),
(103, 'Social Poster - Account Import :  Linkedin', 0, '', '1', '0', '0'),
(105, 'Social Poster - Account Import : Reddit', 0, '', '1', '0', '0'),
(107, 'Social Poster - Account Import : Blogger', 0, '', '1', '0', '0'),
(108, 'Social Poster - Account Import :  WordPress', 0, '', '1', '0', '0'),
(110, 'Social Poster - Text Post', 0, 'month', '1', '1', '0'),
(111, 'Social Poster - Image Post', 0, 'month', '1', '1', '0'),
(112, 'Social Poster - Video Post', 0, 'month', '1', '1', '0'),
(113, 'Social Poster - Link Post', 0, 'month', '1', '1', '0'),
(114, 'Social Poster - HTML Post', 0, 'month', '1', '1', '0'),
(109, 'Social Poster - Account Import :  WordPress (Self hosted) ', 0, '', '1', '0', '0'),
(268, 'Messenger E-commerce', 0, '', '1', '0', '0'),
(275, 'One Time Notification Send', 0, 'month', '1', '0', '0');
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES ('279', 'Instagram Auto Comment Reply Enable Post', '0', 'month', '1', '0', '0');
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES ('277', 'Social Poster - Account Import :  Medium', '0', '', '1', '0', '0');
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES ('296', 'Instagram Posting : Image/Video Post', '0', 'month', '1', '1', '0');
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES ('66', 'Facebook Pages - Subscribers/Page', '0', '', '1', '0', '0');
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES ('315', 'Visual Flow Builder', '0', '', '1', '0', '0');
INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES ('320', 'Instagram Bot', '0', 'month', '1', '0', '0');

INSERT INTO `package` (`id`, `package_name`, `module_ids`, `monthly_limit`, `bulk_limit`, `price`, `validity`, `validity_extra_info`, `is_default`, `visible`, `highlight`, `deleted`) VALUES
(1, 'Trial', '251,80,79,263,65,200,66,223,222,220,279,320,296,82,199,265,197,198,268,275,256,264,100,103,277,108,109,107,101,105,102,33,114,111,113,110,112,78,267,315', '{\"251\":\"10\",\"80\":\"10\",\"79\":\"30\",\"263\":\"10\",\"65\":\"2\",\"200\":\"10\",\"66\":\"0\",\"223\":\"10\",\"222\":\"10\",\"220\":\"10\",\"279\":\"0\",\"320\":\"0\",\"296\":\"0\",\"82\":\"0\",\"199\":\"0\",\"265\":\"5\",\"197\":\"0\",\"198\":\"0\",\"268\":\"0\",\"275\":\"100\",\"256\":\"5\",\"264\":\"10\",\"100\":\"0\",\"103\":\"2\",\"277\":\"0\",\"108\":\"2\",\"109\":\"0\",\"107\":\"2\",\"101\":\"2\",\"105\":\"2\",\"102\":\"2\",\"33\":\"2\",\"114\":\"10\",\"111\":\"10\",\"113\":\"10\",\"110\":\"10\",\"112\":\"10\",\"78\":\"0\",\"267\":\"30\",\"315\":\"0\"}', '{\"251\":\"0\",\"80\":\"0\",\"79\":\"50\",\"263\":\"0\",\"65\":\"0\",\"200\":\"0\",\"66\":\"0\",\"223\":\"0\",\"222\":\"0\",\"220\":\"0\",\"279\":\"0\",\"320\":\"0\",\"296\":\"0\",\"82\":\"0\",\"199\":\"0\",\"265\":\"0\",\"197\":\"0\",\"198\":\"0\",\"268\":\"0\",\"275\":\"0\",\"256\":\"0\",\"264\":\"0\",\"100\":\"0\",\"103\":\"0\",\"277\":\"0\",\"108\":\"0\",\"109\":\"0\",\"107\":\"0\",\"101\":\"0\",\"105\":\"0\",\"102\":\"0\",\"33\":\"0\",\"114\":\"5\",\"111\":\"5\",\"113\":\"5\",\"110\":\"5\",\"112\":\"5\",\"78\":\"0\",\"267\":\"0\",\"315\":\"0\"}', 'Trial', 7, '1,W', '1', '0', '0', '0');

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `password`, `address`, `user_type`, `status`, `add_date`, `purchase_date`, `last_login_at`, `activation_code`, `expired_date`, `package_id`, `deleted`, `brand_logo`, `brand_url`, `vat_no`, `currency`, `time_zone`, `company_email`, `paypal_email`, `paypal_subscription_enabled`, `last_payment_method`, `last_login_ip`) VALUES
(1, 'Admin', 'admin@admin.com', '', '259534db5d66c3effb7aa2dbbee67ab0', '', 'Admin', '1', '2019-08-25 18:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', 0, '0', '', NULL, NULL, 'USD', '', NULL, '', '0', '', '');


CREATE TABLE IF NOT EXISTS `facebook_ex_autoreply_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `autoreply_table_id` int(11) NOT NULL,
  `comment_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `comment_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `commenter_name` varchar(120) CHARACTER SET utf8 NOT NULL,
  `commenter_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `comment_time` datetime NOT NULL,
  `reply_time` datetime NOT NULL,
  `comment_reply_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_status_comment` text CHARACTER SET utf8 NOT NULL,
  `reply_status` text CHARACTER SET utf8 NOT NULL,
  `reply_id` varchar(200) CHARACTER SET utf8 NOT NULL,
  `comment_reply_id` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comment reply id',
  `is_deleted` enum('0','1') CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `is_hidden` enum('0','1') CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `reply_type` enum('post_by_id','full_page_response') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post_by_id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_id` (`comment_id`),
  KEY `Autoreply_teable_id` (`autoreply_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `messenger_bot_saved_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(250) NOT NULL,
  `savedata` longtext NOT NULL,
  `saved_at` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `template_access` enum('private','public') NOT NULL DEFAULT 'private',
  `preview_image` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `allowed_package_ids` varchar(255) NOT NULL COMMENT 'comma seperated',
  `template_category_id` int(11) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`,`template_access`,`allowed_package_ids`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messenger_bot_template_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mailchimp_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tracking_name` varchar(200) NOT NULL,
  `service_type` enum('mailchimp','sendinblue','activecampaign','mautic','acelle') NOT NULL DEFAULT 'mailchimp',
  `api_url` text NOT NULL,
  `api_key` varchar(200) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `inserted_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FX_USER_ID` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mailchimp_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mailchimp_config_id` int(11) NOT NULL,
  `list_name` varchar(255) NOT NULL,
  `list_id` varchar(255) NOT NULL,
  `string_id` varchar(255) NOT NULL,
  `list_folder_id` int(11) NOT NULL,
  `list_total_subscribers` int(11) NOT NULL,
  `list_total_blacklisted` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `inserted_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `list` (`mailchimp_config_id`,`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `mailchimp_config`
  ADD CONSTRAINT `FX_USER_ID` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `mailchimp_list`
  ADD CONSTRAINT `FK_MAILCHIMP_CONFIG_ID` FOREIGN KEY (`mailchimp_config_id`) REFERENCES `mailchimp_config` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `send_email_to_autoresponder_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mailchimp_config_id` int(11) NOT NULL,
  `settings_type` varchar(50) NOT NULL COMMENT 'admin settings, member settings',
  `status` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `auto_responder_type` varchar(30) NOT NULL COMMENT 'mailchimp,aweber etc',
  `api_name` varchar(100) NOT NULL,
  `response` text NOT NULL,
  `insert_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `sms_email_contact_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `sms_email_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `contact_type_id` text CHARACTER SET latin1 NOT NULL,
  `first_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `last_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `phone_number` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `notes` text NOT NULL,
  `unsubscribed` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `deleted` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sms_email_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `contact_type_id` text CHARACTER SET latin1 NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone_number` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `notes` text NOT NULL,
  `unsubscribed` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `deleted` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sms_sending_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `api_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(255) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `label_ids` text NOT NULL,
  `excluded_label_ids` text NOT NULL,
  `label_names` text NOT NULL,
  `user_gender` varchar(20) NOT NULL,
  `user_time_zone` varchar(20) NOT NULL,
  `user_locale` varchar(50) NOT NULL,
  `contact_ids` mediumtext NOT NULL,
  `contact_type_id` mediumtext NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `campaign_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `manual_phone` varchar(255) NOT NULL,
  `posting_status` enum('0','1','2','3') NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` mediumtext NOT NULL,
  `total_thread` int(11) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `completed_at` datetime NOT NULL,
  `is_try_again` enum('0','1') NOT NULL DEFAULT '1',
  `campaign_delay` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`posting_status`),
  KEY `userId_completed` (`user_id`,`completed_at`) USING BTREE,
  KEY `userid_postingstatus` (`user_id`,`posting_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sms_sending_campaign_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sms_api_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `contact_first_name` varchar(255) NOT NULL,
  `contact_last_name` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone_number` varchar(255) NOT NULL,
  `delivery_id` varchar(250) NOT NULL,
  `sent_time` datetime NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`,`processed`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sms_api_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gateway_name` enum('planet','plivo','twilio','clickatell','clickatell-platform','nexmo','msg91.com','textlocal.in','sms4connect.com','telnor.com','mvaayoo.com','routesms.com','trio-mobile.com','sms40.com','africastalking.com','infobip.com','smsgatewayme','semysms.net','custom','custom_post') CHARACTER SET latin1 NOT NULL,
  `custom_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `username_auth_id` tinytext CHARACTER SET latin1 NOT NULL,
  `password_auth_token` tinytext CHARACTER SET latin1 NOT NULL,
  `routesms_host_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `api_id` tinytext CHARACTER SET latin1 NOT NULL,
  `input_http_url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `final_http_url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `phone_number` varchar(100) CHARACTER SET latin1 NOT NULL,
  `base_url` varchar(255) NOT NULL COMMENT 'for custom post method',
  `post_data` text NOT NULL COMMENT 'post data for custom post method',
  `status` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '1',
  `deleted` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_checkbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_code` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL COMMENT 'auto id',
  `domain_name` varchar(255) NOT NULL,
  `btn_size` enum('small','medium','large','xlarge') NOT NULL DEFAULT 'medium',
  `skin` enum('light','dark') NOT NULL DEFAULT 'light' COMMENT 'light=black, dark=white',
  `center_align` enum('true','false') NOT NULL DEFAULT 'true',
  `button_click_success_message` tinytext NOT NULL,
  `validation_error` tinytext NOT NULL,
  `label_ids` varchar(250) NOT NULL COMMENT 'comma seperated,messenger_bot_broadcast_contact_group.id',
  `reference` varchar(250) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
  `language` varchar(200) NOT NULL DEFAULT 'en_US',
  `created_at` datetime NOT NULL,
  `redirect` enum('0','1') NOT NULL DEFAULT '0',
  `add_button_with_message` enum('0','1') NOT NULL DEFAULT '0',
  `button_with_message_content` tinytext NOT NULL COMMENT 'json',
  `success_redirect_url` tinytext NOT NULL,
  `for_woocommerce` enum('0','1') NOT NULL DEFAULT '0',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain_code` (`domain_code`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_checkbox_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_ref` varchar(20) NOT NULL,
  `checkbox_plugin_id` int(11) NOT NULL,
  `reference` varchar(200) NOT NULL,
  `optin_time` datetime NOT NULL,
  `for_woocommerce` enum('0','1') NOT NULL DEFAULT '0',
  `wc_session_unique_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_smtp_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `email_address` varchar(200) CHARACTER SET latin1 NOT NULL,
  `smtp_host` varchar(200) CHARACTER SET latin1 NOT NULL,
  `smtp_port` varchar(100) CHARACTER SET latin1 NOT NULL,
  `smtp_user` varchar(100) CHARACTER SET latin1 NOT NULL,
  `smtp_password` varchar(100) CHARACTER SET latin1 NOT NULL,
  `smtp_type` enum('Default','tls','ssl') CHARACTER SET latin1 NOT NULL DEFAULT 'Default',
  `status` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `deleted` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_mailgun_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(12) NOT NULL,
  `email_address` varchar(200) NOT NULL,
  `domain_name` varchar(200) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_mandrill_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(12) NOT NULL,
  `your_name` varchar(200) NOT NULL,
  `email_address` varchar(200) NOT NULL,
  `api_key` varchar(200) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_sendgrid_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(12) NOT NULL,
  `email_address` varchar(200) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_sending_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `configure_email_table` varchar(255) NOT NULL,
  `api_id` int(11) NOT NULL COMMENT 'configure_email_table_id',
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(255) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `label_ids` text NOT NULL,
  `excluded_label_ids` text NOT NULL,
  `label_names` text NOT NULL,
  `user_gender` varchar(20) NOT NULL,
  `user_time_zone` varchar(20) NOT NULL,
  `user_locale` varchar(50) NOT NULL,
  `contact_ids` mediumtext NOT NULL,
  `contact_type_id` mediumtext NOT NULL COMMENT 'contact_group_table_id',
  `campaign_name` mediumtext NOT NULL,
  `email_subject` mediumtext NOT NULL,
  `email_message` longtext CHARACTER SET utf8mb4 NOT NULL,
  `email_template_id` int(11) DEFAULT NULL,
  `email_attachment` text NOT NULL,
  `posting_status` enum('0','1','2','3') NOT NULL,
  `schedule_time` datetime NOT NULL,
  `time_zone` mediumtext NOT NULL,
  `total_thread` int(11) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `completed_at` datetime NOT NULL,
  `is_try_again` enum('0','1') NOT NULL DEFAULT '1',
  `number_of_unique_open` int(11) NOT NULL,
  `number_of_total_open` int(11) NOT NULL,
  `number_of_unique_click` int(11) NOT NULL,
  `number_of_total_click` int(11) NOT NULL,
  `number_of_unique_clickers` int(11) NOT NULL,
  `last_opened_at` datetime NOT NULL,
  `last_clicked_at` datetime NOT NULL,
  `total_unsubscribed` int(11) NOT NULL,
  `last_unsubscribed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`posting_status`),
  KEY `user_id_2` (`user_id`,`posting_status`),
  KEY `user_id_3` (`user_id`,`completed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `email_sending_campaign_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_table_name` varchar(255) NOT NULL COMMENT 'configure_email_table_name',
  `email_api_id` int(11) NOT NULL COMMENT 'configure_email_table_id',
  `campaign_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `contact_first_name` varchar(255) NOT NULL,
  `contact_last_name` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(255) NOT NULL,
  `is_open` int(11) NOT NULL,
  `number_of_time_open` int(11) NOT NULL,
  `number_of_clicked` int(11) NOT NULL,
  `delivery_id` varchar(255) NOT NULL,
  `sent_time` datetime NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0',
  `is_clicked` enum('0','1') NOT NULL DEFAULT '0',
  `is_unsubscribed` enum('0','1') NOT NULL DEFAULT '0',
  `email_opened_at` datetime NOT NULL,
  `unsubscribed_at` datetime NOT NULL,
  `last_clicked_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `blogger_blog_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blogger_users_info_table_id` int(11) NOT NULL,
  `blog_id` varchar(200) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `blogger_users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `refresh_token` text NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `picture` text NOT NULL,
  `blogger_id` varchar(200) NOT NULL,
  `blog_count` int(11) NOT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `comboposter_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `campaign_type` varchar(255) CHARACTER SET latin1 NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `rich_content` longtext NOT NULL,
  `subreddits` varchar(255) NOT NULL,
  `wpsh_selected_category` text,
  `image_url` text NOT NULL,
  `privacy_type` enum('public','private','unlisted') CHARACTER SET latin1 NOT NULL DEFAULT 'public',
  `video_url` text NOT NULL,
  `thumbnail_url` text NOT NULL,
  `link_caption` varchar(255) NOT NULL,
  `link_description` varchar(255) NOT NULL,
  `link` text NOT NULL,
  `is_child` enum('0','1') NOT NULL DEFAULT '0',
  `parent_campaign_id` int(11) DEFAULT NULL,
  `schedule_type` enum('now','later') CHARACTER SET latin1 NOT NULL DEFAULT 'now',
  `schedule_time` datetime NOT NULL,
  `schedule_timezone` varchar(255) CHARACTER SET latin1 NOT NULL,
  `repeat_times` int(11) NOT NULL,
  `time_interval` int(11) NOT NULL,
  `full_complete` enum('0','1') NOT NULL,
  `posting_medium` longtext CHARACTER SET latin1 NOT NULL,
  `posting_status` enum('pending','processing','completed') CHARACTER SET latin1 NOT NULL DEFAULT 'pending',
  `report` longtext CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `linkedin_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `linkedin_users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `linkedin_id` varchar(200) NOT NULL,
  `access_token` text NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `profile_pic` text NOT NULL,
  `add_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pinterest_board_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pinterest_table_id` int(11) NOT NULL COMMENT 'rx_pinterest_autopost table id',
  `board_name` varchar(255) NOT NULL,
  `board_url` varchar(255) NOT NULL,
  `board_id` varchar(25) NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pinterest_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pinterest_users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pinterest_user_id` varchar(30) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pins` int(11) NOT NULL,
  `boards` int(11) NOT NULL,
  `image` text NOT NULL,
  `code` varchar(255) NOT NULL,
  `add_date` date NOT NULL,
  `pinterest_config_table_id` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `reddit_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `reddit_users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `refresh_token` varchar(250) NOT NULL,
  `token_type` varchar(200) NOT NULL,
  `username` varchar(200) DEFAULT NULL,
  `profile_pic` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `add_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tumblr_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `consumer_id` varchar(255) NOT NULL,
  `consumer_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `twitter_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `consumer_id` varchar(255) NOT NULL,
  `consumer_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `twitter_users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `oauth_token` varchar(200) NOT NULL,
  `oauth_token_secret` varchar(200) NOT NULL,
  `screen_name` varchar(200) NOT NULL,
  `twitter_user_id` varchar(200) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `profile_image` text NOT NULL,
  `followers` int(11) NOT NULL,
  `add_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `wordpress_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `wordpress_users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blog_id` varchar(200) NOT NULL,
  `blog_url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` text NOT NULL,
  `posts` int(11) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `categories` varchar(255) NOT NULL,
  `last_update_time` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `youtube_channel_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `channel_id` varchar(255) NOT NULL,
  `access_token` text,
  `refresh_token` text,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `youtube_channel_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `channel_id` varchar(200) NOT NULL,
  `title` text,
  `description` text,
  `profile_image` text,
  `cover_image` text,
  `view_count` varchar(250) DEFAULT NULL,
  `video_count` varchar(250) DEFAULT NULL,
  `subscriber_count` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `youtube_video_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `channel_id` varchar(200) DEFAULT NULL,
  `video_id` varchar(200) DEFAULT NULL,
  `title` text,
  `image_link` text,
  `publish_time` varchar(200) DEFAULT NULL,
  `tags` text,
  `categoryId` int(11) DEFAULT NULL,
  `defaultLanguage` varchar(255) NOT NULL,
  `privacyStatus` varchar(255) DEFAULT NULL,
  `localizations` text,
  `liveBroadcastContent` varchar(250) DEFAULT NULL,
  `duration` varchar(250) DEFAULT NULL,
  `dimension` varchar(200) DEFAULT NULL,
  `definition` varchar(200) DEFAULT NULL,
  `caption` text,
  `licensedContent` text,
  `projection` varchar(250) DEFAULT NULL,
  `viewCount` int(11) DEFAULT NULL,
  `likeCount` int(11) DEFAULT NULL,
  `dislikeCount` int(11) DEFAULT NULL,
  `favoriteCount` int(11) DEFAULT NULL,
  `commentCount` int(11) DEFAULT NULL,
  `description` text,
  `backlink_status` enum('0','2','1') NOT NULL DEFAULT '0' COMMENT '0 = incomplete, 2 = submitted, 1 = completed',
  `rank_status` enum('0','1') NOT NULL DEFAULT '0',
  `backlink_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`,`video_id`),
  KEY `backlink_status` (`backlink_status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `transaction_history_manual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `transaction_id` varchar(150) NOT NULL,
  `paid_amount` varchar(255) NOT NULL,
  `paid_currency` char(4) NOT NULL,
  `additional_info` longtext NOT NULL,
  `filename` varchar(255) NOT NULL,
  `status` enum('0','1','2') NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thm_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `transaction_history_manual`
  ADD CONSTRAINT `thm_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `email_clickrate_links_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `links` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `wordpress_config_self_hosted` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authentication_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_category` text COLLATE utf8mb4_unicode_ci,
  `status` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `ct_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `wordpress_config_self_hosted`
  ADD CONSTRAINT `ct_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `ecommerce_attribute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `woocommerce_config_id` int(11) DEFAULT NULL,
  `woocommerce_attribute_id` int(11) DEFAULT NULL,
  `woocommerce_attribute_slug` text NOT NULL,
  `attribute_name` varchar(255) NOT NULL,
  `attribute_values` text NOT NULL,
  `optional` enum('0','1') NOT NULL DEFAULT '0',
  `multiselect` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `woocommerce_attribute_id` (`woocommerce_attribute_id`,`store_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ecommerce_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `subscriber_id` varchar(50) NOT NULL COMMENT 'messenger_bot_subscriber.subscribe_id',
  `subtotal` float NOT NULL,
  `tax` float NOT NULL,
  `shipping` float NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `coupon_type` varchar(50) NOT NULL,
  `discount` float NOT NULL,
  `payment_amount` float NOT NULL,
  `currency` varchar(20) NOT NULL,
  `ordered_at` datetime NOT NULL,
  `buyer_first_name` varchar(150) NOT NULL,
  `buyer_last_name` varchar(150) NOT NULL,
  `buyer_email` varchar(75) NOT NULL,
  `buyer_mobile` varchar(50) NOT NULL,
  `buyer_country` varchar(20) NOT NULL,
  `buyer_city` varchar(50) NOT NULL,
  `buyer_state` varchar(50) NOT NULL,
  `buyer_address` text NOT NULL,
  `buyer_zip` varchar(10) NOT NULL,
  `bill_first_name` varchar(100) NOT NULL,
  `bill_last_name` varchar(150) NOT NULL,
  `bill_email` varchar(75) NOT NULL,
  `bill_mobile` varchar(20) NOT NULL,
  `bill_country` varchar(20) NOT NULL,
  `bill_city` varchar(50) NOT NULL,
  `bill_state` varchar(50) NOT NULL,
  `bill_address` text NOT NULL,
  `bill_zip` varchar(10) NOT NULL,
  `delivery_note` text NOT NULL,
  `store_pickup` enum('0','1') NOT NULL DEFAULT '0',
  `pickup_point_details` text NOT NULL,
  `transaction_id` varchar(25) NOT NULL,
  `card_ending` varchar(10) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `checkout_account_email` varchar(100) NOT NULL,
  `checkout_account_receiver_email` varchar(100) NOT NULL,
  `checkout_account_country` varchar(100) NOT NULL,
  `checkout_account_first_name` varchar(100) NOT NULL,
  `checkout_account_last_name` varchar(100) NOT NULL,
  `checkout_amount` varchar(20) NOT NULL,
  `checkout_currency` varchar(20) NOT NULL,
  `checkout_verify_status` varchar(100) NOT NULL,
  `checkout_timestamp` varchar(50) NOT NULL,
  `checkout_source_json` text NOT NULL,
  `manual_additional_info` longtext NOT NULL,
  `manual_filename` varchar(150) NOT NULL,
  `manual_currency` varchar(20) NOT NULL,
  `manual_amount` float NOT NULL,
  `paid_at` datetime NOT NULL,
  `status` enum('pending','approved','rejected','shipped','delivered','completed') NOT NULL COMMENT 'payment_status',
  `status_changed_at` datetime NOT NULL,
  `status_changed_note` text NOT NULL,
  `updated_at` datetime NOT NULL,
  `action_type` enum('add','remove','checkout') NOT NULL DEFAULT 'add',
  `confirmation_response` text NOT NULL,
  `last_completed_hour` int(11) NOT NULL,
  `is_totally_completed` enum('0','1') NOT NULL DEFAULT '0',
  `last_sent_at` datetime NOT NULL,
  `initial_date` datetime NOT NULL,
  `last_processing_started_at` datetime NOT NULL,
  `processing_status` enum('0','1') NOT NULL DEFAULT '0',
  `payment_temp_session` text NOT NULL,
  `delivery_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `user_id` (`user_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ecommerce_cart_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `unit_price` float NOT NULL,
  `coupon_info` varchar(255) NOT NULL,
  `quantity` float NOT NULL,
  `attribute_info` varchar(255) NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_downloaded` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_id` (`cart_id`,`product_id`,`attribute_info`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ecommerce_cart_address_saved` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `country` varchar(20) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `zip` varchar(10) NOT NULL,
  `is_default` enum('0','1') NOT NULL DEFAULT '0',
  `profile_address` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `subscriber_id` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ecommerce_cart_pickup_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `point_name` varchar(255) NOT NULL,
  `point_details` text NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ecommerce_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `thumbnail` varchar(100) NOT NULL,
  `serial` int(11) NOT NULL,
  `woocommerce_config_id` int(11) DEFAULT NULL,
  `woocommerce_category_id` int(11) DEFAULT NULL,
  `woocommerce_category_slug` text NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `woocommerce_category_id` (`woocommerce_category_id`,`store_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ecommerce_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `paypal_email` varchar(100) NOT NULL,
  `paypal_mode` enum('live','sandbox') NOT NULL DEFAULT 'live',
  `stripe_billing_address` enum('0','1') NOT NULL DEFAULT '0',
  `stripe_secret_key` text NOT NULL,
  `stripe_publishable_key` text NOT NULL,
  `paystack_secret_key` text NOT NULL,
  `paystack_public_key` text NOT NULL,
  `razorpay_key_id` text NOT NULL,
  `razorpay_key_secret` text NOT NULL,
  `mollie_api_key` text NOT NULL,
  `mercadopago_public_key` text NOT NULL,
  `mercadopago_access_token` text NOT NULL,
  `marcadopago_country` varchar(5) NOT NULL,
  `sslcommerz_store_id` text NOT NULL,
  `sslcommerz_store_password` text NOT NULL,
  `sslcommerz_mode` enum('sandbox','live') NOT NULL DEFAULT 'live',
  `senangpay_merchent_id` text NOT NULL,
  `senangpay_secret_key` text NOT NULL,
  `senangpay_mode` enum('sandbox','live') NOT NULL DEFAULT 'live',
  `instamojo_api_key` text NOT NULL,
  `instamojo_auth_token` text NOT NULL,
  `instamojo_mode` enum('sandbox','live') NOT NULL DEFAULT 'live',
  `xendit_secret_api_key` text NOT NULL,
  `manual_payment` enum('0','1') NOT NULL DEFAULT '0',
  `manual_payment_instruction` text NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `currency_position` enum('left','right') NOT NULL DEFAULT 'left',
  `decimal_point` tinyint(4) NOT NULL DEFAULT '2',
  `thousand_comma` enum('0','1') NOT NULL DEFAULT '0',
  `store_pickup_title` varchar(100) NOT NULL DEFAULT 'Store Pickup',
  `buy_button_title` varchar(100) NOT NULL DEFAULT 'Buy Now',
  `is_store_pickup` enum('0','1') NOT NULL DEFAULT '0',
  `is_home_delivery` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_country` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_state` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_city` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_zip` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_email` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_phone` enum('0','1') NOT NULL DEFAULT '1',
  `is_delivery_note` enum('0','1') NOT NULL DEFAULT '1',
  `is_preparation_time` enum('0','1') NOT NULL DEFAULT '0',
  `preparation_time` varchar(20) NOT NULL,
  `preparation_time_unit` enum('minutes','hours','days') NOT NULL,
  `is_order_schedule` enum('0','1') NOT NULL DEFAULT '0',
  `order_schedule` enum('today','tomorrow','week','any') NOT NULL DEFAULT 'any',
  `font` text NOT NULL,
  `is_category_wise_product_view` enum('0','1') NOT NULL DEFAULT '0',
  `product_sort` enum('name','new','price','sale','random') NOT NULL DEFAULT 'name',
  `product_sort_order` enum('asc','desc') NOT NULL DEFAULT 'asc',
  `product_listing` enum('list','grid') NOT NULL DEFAULT 'list',
  `theme_color` varchar(10) NOT NULL DEFAULT '#ff8342',
  `hide_add_to_cart` enum('0','1') NOT NULL DEFAULT '0',
  `hide_buy_now` enum('0','1') NOT NULL DEFAULT '0',
  `whatsapp_send_order_button` enum('0','1') NOT NULL DEFAULT '0',
  `whatsapp_phone_number` varchar(30) NOT NULL,
  `whatsapp_send_order_text` text NOT NULL,
  `is_guest_login` enum('0','1') NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL,
  `paymaya_public_key` text NOT NULL,
  `myfatoorah_api_key` text NOT NULL,
  `myfatoorah_mode` varchar(30) NOT NULL,
  `toyyibpay_secret_key` text NOT NULL,
  `paymaya_secret_key` varchar(100) NOT NULL,
  `paymaya_mode` varchar(30) NOT NULL,
  `toyyibpay_category_code` varchar(100) NOT NULL,
  `toyyibpay_mode` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ecommerce_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `coupon_type` enum('percent','fixed cart','fixed product') NOT NULL DEFAULT 'percent',
  `coupon_code` varchar(50) NOT NULL,
  `coupon_amount` float NOT NULL DEFAULT '0',
  `free_shipping_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `expiry_date` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `product_ids` text NOT NULL COMMENT 'comma separated ',
  `max_usage_limit` int(11) NOT NULL,
  `used` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_id_2` (`store_id`,`coupon_code`),
  KEY `store_id` (`store_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ecommerce_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_video_id` varchar(100) NOT NULL,
  `original_price` float NOT NULL,
  `sell_price` float NOT NULL,
  `taxable` enum('0','1') NOT NULL DEFAULT '0',
  `stock_item` int(11) NOT NULL,
  `stock_display` enum('0','1') NOT NULL DEFAULT '0',
  `stock_prevent_purchase` enum('0','1') NOT NULL DEFAULT '0',
  `attribute_ids` varchar(255) NOT NULL,
  `preparation_time` varchar(20) NOT NULL,
  `preparation_time_unit` enum('minutes','hours','days') NOT NULL,
  `purchase_note` text NOT NULL,
  `thumbnail` text NOT NULL,
  `featured_images` text NOT NULL,
  `digital_product_file` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `sales_count` int(11) NOT NULL,
  `visit_count` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `woocommerce_product_id` int(11) DEFAULT NULL,
  `woocommerce_price_html` text NOT NULL,
  `related_product_ids` varchar(255) NOT NULL,
  `upsell_product_id` int(11) NOT NULL,
  `downsell_product_id` int(11) NOT NULL,
  `is_featured` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `woocommerce_product_id` (`store_id`,`woocommerce_product_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ecommerce_reminder_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subscriber_id` varchar(250) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `email` varchar(255) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `last_completed_hour` int(11) NOT NULL,
  `is_sent` enum('0','1') NOT NULL DEFAULT '1',
  `is_opened` enum('0','1') NOT NULL DEFAULT '0',
  `is_delivered` enum('0','1') NOT NULL DEFAULT '0',
  `sent_at` datetime NOT NULL,
  `delivered_at` datetime NOT NULL,
  `opened_at` datetime NOT NULL,
  `sent_response` text NOT NULL,
  `delivered_response` tinytext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`,`user_id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `cart_id` (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ecommerce_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_unique_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `store_type` enum('physical','digital','service') NOT NULL DEFAULT 'physical',
  `store_name` text NOT NULL,
  `store_logo` varchar(50) NOT NULL,
  `store_favicon` varchar(50) NOT NULL,
  `store_email` varchar(75) NOT NULL,
  `store_phone` varchar(25) NOT NULL,
  `store_country` varchar(20) NOT NULL,
  `store_city` varchar(50) NOT NULL,
  `store_state` varchar(50) NOT NULL,
  `store_zip` varchar(20) NOT NULL,
  `store_address` text NOT NULL,
  `tax_percentage` float NOT NULL,
  `shipping_charge` float NOT NULL,
  `paypal_enabled` enum('0','1') DEFAULT '0',
  `stripe_enabled` enum('0','1') DEFAULT '0',
  `razorpay_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `paystack_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `mollie_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `mercadopago_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `sslcommerz_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `senangpay_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `instamojo_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `xendit_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `manual_enabled` enum('0','1') NOT NULL DEFAULT '1',
  `cod_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `messenger_content` text NOT NULL,
  `sms_content` text NOT NULL,
  `sms_api_id` int(11) NOT NULL,
  `email_content` longtext NOT NULL,
  `email_api_id` int(11) NOT NULL,
  `email_subject` text NOT NULL,
  `configure_email_table` varchar(100) NOT NULL,
  `notification_sms_api_id` int(11) NOT NULL,
  `notification_email_api_id` int(11) NOT NULL,
  `notification_email_subject` text NOT NULL,
  `notification_configure_email_table` varchar(100) NOT NULL,
  `notification_message` text NOT NULL,
  `label_ids` text NOT NULL,
  `last_sent_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `terms_use_link` text NOT NULL,
  `refund_policy_link` text NOT NULL,
  `store_locale` varchar(50) NOT NULL,
  `is_rtl` enum('0','1') NOT NULL DEFAULT '0',
  `pixel_id` varchar(50) NOT NULL,
  `google_id` varchar(50) NOT NULL,
  `qr_code` text NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `paymaya_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `myfatoorah_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `toyyibpay_enabled` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_unique_id` (`store_unique_id`),
  KEY `user_id` (`user_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ecommerce_store_business_hours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `schedule_day` varchar(20) NOT NULL,
  `off_day` enum('0','1') NOT NULL DEFAULT '0',
  `start_time` varchar(20) NOT NULL,
  `end_time` varchar(20) NOT NULL,
  `always_open` ENUM('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `messenger_bot_broadcast_serial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `broadcast_type` enum('Non Promo','24H Promo','OTN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Non Promo',
  `message_tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NON_PROMOTIONAL_SUBSCRIPTION',
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `excluded_label_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_names` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_gender` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_time_zone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_locale` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_type` enum('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel','list','media') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `buttons` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postback_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otn_postback_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'otn_postback table id',
  `created_at` datetime NOT NULL,
  `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_time` datetime NOT NULL,
  `timezone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_count` int(11) NOT NULL,
  `posting_status` enum('0','1','2','3','4') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_try_again` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `last_try_error_count` int(11) NOT NULL,
  `total_thread` int(11) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `successfully_delivered` int(11) NOT NULL,
  `successfully_opened` int(11) NOT NULL,
  `successfully_clicked` int(11) NOT NULL,
  `completed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`),
  KEY `posting_status` (`posting_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `messenger_bot_broadcast_serial` ADD `social_media` ENUM('fb','ig') NOT NULL DEFAULT 'fb' AFTER `completed_at`;

CREATE TABLE IF NOT EXISTS `messenger_bot_broadcast_serial_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `subscribe_id` varchar(255) NOT NULL,
  `otn_token` varchar(255) NOT NULL,
  `subscriber_auto_id` int(11) NOT NULL,
  `subscriber_name` varchar(255) NOT NULL,
  `subscriber_lastname` varchar(200) NOT NULL,
  `sent_time` datetime NOT NULL,
  `delivered` enum('0','1') NOT NULL DEFAULT '0',
  `delivery_time` datetime NOT NULL,
  `opened` enum('0','1') NOT NULL DEFAULT '0',
  `open_time` datetime NOT NULL,
  `clicked` enum('0','1') NOT NULL DEFAULT '0',
  `click_ref` varchar(200) NOT NULL,
  `click_time` datetime NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0',
  `error_message` tinytext NOT NULL,
  `message_sent_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`,`processed`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_drip_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(250) NOT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `message_content_hourly` text NOT NULL,
  `created_at` datetime NOT NULL,
  `last_sent_at` datetime NOT NULL,
  `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default',
  `campaign_type` enum('messenger','email','sms') NOT NULL DEFAULT 'messenger',
  `engagement_table_id` int(11) NOT NULL,
  `between_start` varchar(50) NOT NULL DEFAULT '00:00',
  `between_end` varchar(50) NOT NULL DEFAULT '23:59',
  `timezone` varchar(250) NOT NULL,
  `message_tag` varchar(255) NOT NULL,
  `total_unsubscribed` int(11) NOT NULL,
  `last_unsubscribed_at` datetime NOT NULL,
  `external_sequence_sms_api_id` varchar(50) NOT NULL,
  `external_sequence_email_api_id` varchar(50) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `visual_flow_sequence_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`,`user_id`),
  KEY `xengagementtable_type` (`engagement_table_id`,`drip_type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_drip_campaign_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `subscribe_id` varchar(30) NOT NULL COMMENT 'fb id',
  `messenger_bot_drip_campaign_id` int(11) NOT NULL,
  `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default',
  `messenger_bot_drip_last_completed_day` int(11) NOT NULL,
  `messenger_bot_drip_last_completed_hour` float NOT NULL,
  `messenger_bot_drip_is_toatally_complete` enum('0','1') NOT NULL DEFAULT '0',
  `messenger_bot_drip_is_toatally_complete_hourly` enum('0','1') NOT NULL DEFAULT '0',
  `messenger_bot_drip_last_sent_at` datetime NOT NULL,
  `messenger_bot_drip_initial_date` datetime NOT NULL,
  `last_processing_started_at` datetime NOT NULL,
  `last_processing_started_at_hourly` datetime NOT NULL,
  `messenger_bot_drip_processing_status` enum('0','1') NOT NULL DEFAULT '0',
  `messenger_bot_drip_processing_status_hourly` enum('0','1') NOT NULL DEFAULT '0',
  `is_unsubscribed` enum('0','1') NOT NULL DEFAULT '0',
  `unsubscribed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribe_id` (`subscribe_id`,`messenger_bot_drip_campaign_id`),
  KEY `xis_completed_hour` (`messenger_bot_drip_is_toatally_complete_hourly`),
  KEY `xis_completed_day` (`messenger_bot_drip_is_toatally_complete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_drip_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messenger_bot_drip_campaign_id` int(11) NOT NULL,
  `messenger_bot_subscriber_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subscribe_id` varchar(250) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `last_completed_day` int(11) NOT NULL,
  `last_completed_hour` int(11) NOT NULL,
  `is_sent` enum('0','1') NOT NULL DEFAULT '1',
  `is_opened` enum('0','1') NOT NULL DEFAULT '0',
  `is_delivered` enum('0','1') NOT NULL DEFAULT '0',
  `sent_at` datetime NOT NULL,
  `delivered_at` datetime NOT NULL,
  `opened_at` datetime NOT NULL,
  `sent_response` tinytext NOT NULL,
  `delivered_response` tinytext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `messenger_bot_drip_campaign_id` (`messenger_bot_drip_campaign_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `otn_optin_subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `otn_id` int(11) NOT NULL,
  `subscriber_id` varchar(255) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `otn_token` varchar(255) NOT NULL,
  `optin_time` datetime NOT NULL,
  `is_sent` enum('0','1') NOT NULL DEFAULT '0',
  `sent_time` datetime NOT NULL,
  `sent_response` text NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `otn_id_subscriber_id` (`otn_id`,`subscriber_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `otn_postback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `otn_postback_id` varchar(191) NOT NULL,
  `reply_postback_id` varchar(255) NOT NULL,
  `label_id` varchar(191) NOT NULL,
  `drip_campaign_id` varchar(191) NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `flow_type` enum('general','flow') NOT NULL DEFAULT 'flow',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `custom_page_builder`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `page_name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `page_description` longtext NOT NULL,
    `url` int(11) NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `FX_USER_ID` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `body` longtext NOT NULL,
  `category_id` int(11) NOT NULL,
  `tags` text,
  `status` enum('0','1','2') NOT NULL DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `tags` (`tags`(255)),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `blog_post_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `blog_post_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `blog_post_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `blog_post_comments`
  ADD CONSTRAINT `fk_comments` FOREIGN KEY (`parent_id`) REFERENCES `blog_post_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_post_comments` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE;


CREATE TABLE IF NOT EXISTS `instagram_reply_autoreply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `autoreply_type` enum('post_autoreply','account_autoreply','mentions_autoreply') NOT NULL DEFAULT 'post_autoreply',
  `post_pause_play` enum('play','pause') NOT NULL DEFAULT 'play',
  `full_pause_play` enum('play','pause') NOT NULL,
  `mentions_pause_play` enum('play','pause') NOT NULL DEFAULT 'play',
  `auto_reply_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `page_info_table_id` int(11) NOT NULL,
  `page_name` text,
  `post_id` varchar(200) NOT NULL,
  `post_url` text NOT NULL,
  `media_url` text NOT NULL,
  `media_type` varchar(50) NOT NULL,
  `post_created_at` varchar(255) DEFAULT NULL,
  `post_description` longtext,
  `reply_type` varchar(200) NOT NULL,
  `report_type` enum('full','mention','post') NOT NULL DEFAULT 'post',
  `multiple_reply` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `last_reply_time` datetime NOT NULL,
  `error_message` text NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `trigger_matching_type` enum('exact','string') NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_info_table_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `instagram_autoreply_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `autoreply_table_id` int(11) NOT NULL,
  `post_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_type` enum('post','full','mention') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `comment_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `comment_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `commenter_name` varchar(120) CHARACTER SET utf8 NOT NULL,
  `commenter_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `comment_time` datetime NOT NULL,
  `reply_time` datetime NOT NULL,
  `comment_reply_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_status_comment` text CHARACTER SET utf8 NOT NULL,
  `reply_status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_deleted` enum('0','1') CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `is_hidden` enum('0','1') CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hidden_comment_count` int(11) NOT NULL,
  `deleted_comment_count` int(11) NOT NULL,
  `auto_comment_reply_count` int(11) NOT NULL,
  `auto_private_reply_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_id` (`comment_id`),
  KEY `Autoreply_teable_id` (`autoreply_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `email_sms_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `template_type` enum('sms','email') NOT NULL,
  `editor_type` enum('rich_text_editor','drag_and_drop') DEFAULT 'rich_text_editor',
  `location_hash` varchar(50) DEFAULT NULL,
  `template_name` varchar(255) NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `medium_users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medium_id` varchar(200) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `profile_pic` text NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `refresh_token` varchar(255) NOT NULL,
  `add_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_input_custom_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_type` varchar(20) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `custom field` (`user_id`,`reply_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_input_custom_fields_assaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` varchar(50) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `custom_field_value` text CHARACTER SET utf8mb4 NOT NULL,
  `assaign_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_id` (`subscriber_id`,`page_id`,`custom_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messenger_bot_subscriber_extra_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` varchar(50) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `input_flow_campaign_id` int(10) NOT NULL,
  `last_question_sent_id` int(10) NOT NULL,
  `last_question_sent_time` datetime NOT NULL,
  `email_quick_reply_button_id` varchar(30) NOT NULL,
  `phone_quick_reply_button_id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_id` (`subscriber_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `instagram_reply_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auto_reply_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reply_type` varchar(200) NOT NULL,
  `multiple_reply` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `error_message` text NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `ig_username` varchar(200) NOT NULL,
  `trigger_matching_type` enum('exact','string') NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `visual_flow_builder_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `reference_name` text NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `json_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_system` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'system defined template or not',
  `action_type` enum('reply','UNSUBSCRIBE_QUICK_BOXER','RESUBSCRIBE_QUICK_BOXER','YES_START_CHAT_WITH_BOT','YES_START_CHAT_WITH_HUMAN','QUICK_REPLY_BIRTHDAY_REPLY_BOT','QUICK_REPLY_LOCATION_REPLY_BOT','QUICK_REPLY_PHONE_REPLY_BOT','QUICK_REPLY_EMAIL_REPLY_BOT','get-started','no match','STORY_MENTION','STORY_PRIVATE_REPLY','MESSAGE_UNSEND_PRIVATE_REPLY') NOT NULL DEFAULT 'reply',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `messenger_bot_subscribers_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_group_id` int(11) NOT NULL,
  `subscriber_table_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_group_id_2` (`contact_group_id`,`subscriber_table_id`),
  KEY `contact_group_id` (`contact_group_id`),
  KEY `subscriber_table_id` (`subscriber_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `messenger_bot_message_sent_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` varchar(30) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `message_unique_id` varchar(100) NOT NULL,
  `message_type` enum('message','postback') NOT NULL DEFAULT 'message',
  `no_sent_click` int(12) NOT NULL,
  `error_count` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_id` (`subscriber_id`,`message_unique_id`,`page_table_id`),
  KEY `message_unique_id` (`message_unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `visual_flow_campaign_unique_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_table_id` int(11) NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `element_unique_id` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_table_id` (`page_table_id`,`visual_flow_campaign_id`,`element_unique_id`),
  KEY `visual_flow_campaign_id` (`visual_flow_campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
