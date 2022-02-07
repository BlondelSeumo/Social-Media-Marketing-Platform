<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
        <title><?php echo $page_title; ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css">

        <link href="<?php echo base_url(); ?>plugins/flow_builder/css/app.441833a2.css" rel="preload" as="style">
        <link href="<?php echo base_url(); ?>plugins/flow_builder/css/chunk-vendors.bff25d1a.css" rel="preload" as="style">
        <link href="<?php echo base_url(); ?>plugins/flow_builder/js/app.ef6527a3.js" rel="preload" as="script">
        <link href="<?php echo base_url(); ?>plugins/flow_builder/js/chunk-vendors.2e5b0734.js" rel="preload" as="script">
        <link href="<?php echo base_url(); ?>plugins/flow_builder/css/chunk-vendors.bff25d1a.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>plugins/flow_builder/css/app.441833a2.css" rel="stylesheet">

  </head>

    </head>
    <body>
        <noscript><strong>We're sorry but flow-builder doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript>
        <div id="xit-flow-builder"></div>

        <?php if($json_data) { ?>
            <script>
                var data = '<?php echo sanitize_json_string($json_data); ?>';
            </script>
        <?php } else { ?>
            <script>
                var data = null;
            </script>
        <?php } ?>
        <script>
            var builder_id = "xitFB@0.0.1"
            var page_table_id = '<?php echo $page_table_id; ?>'
            var builder_table_id = '<?php echo $builder_table_id; ?>'
            var sequence_addon = '<?php echo $sequence_addon; ?>'
            var user_input_flow_addon = '<?php echo $user_input_flow_addon; ?>'
            var messenger_bot_condition = '<?php echo $messenger_bot_condition; ?>'
            var go_back_link = '<?php echo $go_back_link; ?>'
            var instagram_bot_addon = '<?php echo isset($instagram_bot_addon) ? $instagram_bot_addon : '0'; ?>'
            var messenger_engagement_plugin = '<?php echo isset($messenger_engagement_plugin) ? $messenger_engagement_plugin : '0'; ?>'
            var page_name_or_insta_username = '<?php echo isset($page_name_or_insta_username) ? $page_name_or_insta_username : 'Page or account name'; ?>'
            var fb_page_id = '<?php echo isset($fb_page_id) ? $fb_page_id : null; ?>'
            var message_sent_stat = '<?php echo $message_sent_stat; ?>'
            var message_sent_stat_addon = '<?php echo $message_sent_stat_addon; ?>'

            window.xitFlowBuilderData = {
                "page_table_id" : parseInt(page_table_id,10),
                "base_url" : '<?php echo base_url(); ?>',
                "data" : JSON.parse(data),
                "message_sent_stat" : JSON.parse(message_sent_stat),
                "builder_id" : builder_id,
                "builder_table_id" : parseInt(builder_table_id,10),
                "sequence_addon" : parseInt(sequence_addon,10),
                "user_input_flow_addon" : parseInt(user_input_flow_addon,10),
                "message_sent_stat_addon" : parseInt(message_sent_stat_addon,10),
                "messenger_bot_condition" : parseInt(messenger_bot_condition,10),
                "go_back_link" : go_back_link,
                "instagram_addon" : parseInt(instagram_bot_addon,10),
                "messenger_engagement_plugin" : parseInt(messenger_engagement_plugin,10),
                "page_name_or_insta_username" : page_name_or_insta_username,
                "fb_page_id" : fb_page_id,
            }
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/js/all.min.js"></script>
        <script src="<?php echo base_url('visual_flow_builder/language_file'); ?>" type="text/javascript"></script>

        <script src="<?php echo base_url(); ?>plugins/flow_builder/js/chunk-vendors.2e5b0734.js"></script>
        <script src="<?php echo base_url(); ?>plugins/flow_builder/js/app.ef6527a3.js"></script>

    </body>
</html>