<!DOCTYPE html>
<html lang="en">
  <head>    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/favicon.png'); ?>">

    <title><?php echo $page_title . ' - ' . $product_name; ?></title>


    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/css/jquery-ui.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/css/bootstrap-colorpicker.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/css/bootstrap-slider.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/plugins/medium-editor/medium-editor.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/plugins/medium-editor/template.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/css/spectrum.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/css/iziToast.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/email-template-builder/css/style.css'); ?>">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>

        window.ddetb_vars = {
            base_url: '<?php echo base_url(); ?>',
            ok: '<?php echo $this->lang->line("OK"); ?>',
            sorry: '<?php echo $this->lang->line("Sorry!"); ?>',
            error: '<?php echo $this->lang->line("Error!"); ?>',
            action: '<?php echo $this->lang->line("Action!"); ?>',
            cancel: '<?php echo $this->lang->line("Cancel"); ?>',
            modal_title: '<?php echo $this->lang->line("Mail preview"); ?>',
            empty_field: '<?php echo $this->lang->line("Please fill in all fields"); ?>',
            confirmation_title: '<?php echo $this->lang->line("Are you sure?"); ?>', 
            subject_placeholder: '<?php echo $this->lang->line("Email subject"); ?>', 
            template_placeholder: '<?php echo $this->lang->line("Template name"); ?>', 
            confirmation_text: '<?php echo $this->lang->line("Make sure you are done with your design!"); ?>',

            // Props for editing email template
            templateId: '<?php if(isset($templateId)) { echo $templateId; } ?>',
            templateName: '<?php if(isset($templateName)) { echo $templateName; } ?>',
            emailSubject: '<?php if(isset($emailSubject)) { echo $emailSubject; } ?>',
            mailTemplateData: '<?php if(isset($mailTemplateData)) { echo $mailTemplateData; } ?>',
            locationHash: '<?php if(isset($locationHash)) { echo $locationHash; } ?>',

            urls: {
                onDeleteRedirectUrl: '',
                saveOnlyRedirectUrl: '<?php echo base_url(); ?>sms_email_manager/drag_drop_email_template',

                // Response should be json object { status: true }
                computerUploadUrl: '<?php echo base_url(); ?>sms_email_manager/upload_file',

                saveTemplateDataUrl: '<?php echo base_url(); ?>sms_email_manager/save_template',
                updateTemplateDataUrl: '<?php echo base_url(); ?>sms_email_manager/update_template',
                onSaveTemplateRedirectUrl: '<?php echo base_url(); ?>sms_email_manager/template_lists/email',

                // Response should be json object { status: true }
                sendTestEmailUrl: '<?php echo base_url(); ?>sms_email_manager/send_email',
            }
        };

    </script>

  </head>
  <body>

    <div class="container-fullscreen">

        <div class="container text-center">
            <div id="choose-template" class="text-center d-none">
                <button class="choose" type="button" data-id="no-sidebar"><img src="<?php echo base_url('plugins/email-template-builder/img/no-sidebar.jpg'); ?>" class="img-fluid" alt=""><p><?php echo $this->lang->line("No Sidebar (wide)"); ?></p></button>
                <button class="choose" type="button" data-id="left-sidebar"><img src="<?php echo base_url('plugins/email-template-builder/img/left-sidebar.jpg'); ?>" class="img-fluid" alt=""><p><?php echo $this->lang->line("Left Sidebar"); ?></p></button>
                <button class="choose" type="button" data-id="right-sidebar"><img src="<?php echo base_url('plugins/email-template-builder/img/right-sidebar.jpg'); ?>" class="img-fluid" alt=""><p><?php echo $this->lang->line("Right Sidebar"); ?></p></button>
                <button class="choose" type="button" data-id="both-sidebar"><img src="<?php echo base_url('plugins/email-template-builder/img/both-sidebar.jpg'); ?>" class="img-fluid" alt=""><p><?php echo $this->lang->line("Both Sidebars"); ?></p></button>
            </div>
        </div>

        <div class="container-content d-none" id="mail-template">
            <?php if (isset($mailTemplateData)) { echo $mailTemplateData; } ?>
        </div>

        <div class="container-sidebar d-none" id="option-tabs">

            <div id="get-options" class="text-center">

                <p class="lead"><?php echo $this->lang->line("Drag & Drop elements") ?></p>

                <div class="get-options choose" data-id="content" id="content">
                    <i class="fa fa-file-text-o"></i>
                    <div><?php echo $this->lang->line("Text"); ?></div>
                </div>
                <div class="get-options choose" data-id="image" id="image">
                    <i class="fa fa-picture-o"></i>
                    <div><?php echo $this->lang->line("Image"); ?></div>
                </div>
                <div class="get-options choose" data-id="link" id="link">
                    <i class="fa fa-link"></i>
                    <div><?php echo $this->lang->line("Link"); ?></div>
                </div>
                <div class="get-options choose" data-id="divider" id="divider">
                    <i class="fa fa-minus"></i>
                    <div><?php echo $this->lang->line("Divider"); ?></div>
                </div>

                <div id="editor"></div>

                <ul id="attach-data" class="list-group"></ul>
            </div>
            
        </div>
    </div>

    <div id="modal" class="reset-this"></div>

    <button class="btn btn-info btn-left-bottom-1 d-none" type="button" id="setting" title="<?php echo $this->lang->line("Layout Options"); ?>" data-toggle="tooltip" data-placement="top" data-trigger="hover"><i class="fa fa-cog fa-spin"></i></button>

    <button class="btn btn-warning btn-left-bottom-2 d-none" type="button" id="save-and-quit" title="<?php echo $this->lang->line("Save & Quit"); ?>" data-toggle="tooltip" data-placement="top" data-trigger="hover"><i class="fa fa-sign-out"></i></button>

    <button class="btn btn-primary btn-left-bottom-3 d-none" type="button" id="save-only" title="<?php echo $this->lang->line("Save Template"); ?>" data-toggle="tooltip" data-placement="top" data-trigger="hover"><i class="fa fa-hdd-o"></i></button>

    <button class="btn btn-success btn-left-bottom-4 d-none" type="button" id="preview" title="<?php echo $this->lang->line("Preview"); ?>" data-toggle="tooltip" data-placement="top" data-trigger="hover"><i class="fa fa-search-plus"></i></button>
      
      
    <div id="alerts"></div>
      
    <div class="tools tools-left" id="settings">
        <div class="tools-header">
            <button type="button" class="close" data-dismiss="tools" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><span class="fa fa-cog fa-spin"></span> <?php echo $this->lang->line("Settings"); ?></h4>
        </div>

        <div class="tools-footer d-none">
            <div class="button-group text-center">
                <button class="btn btn-danger btn-sm" type="button" id="delete"><i class="fa fa-trash"></i> <?php echo $this->lang->line("Delete"); ?></button>
                <button class="btn btn-warning btn-sm" type="button" id="test"><i class="fa fa-paper-plane"></i> <?php echo $this->lang->line("Send Test"); ?></button>
                <button class="btn btn-success btn-sm" data-dismiss="tools" type="button" id="send-message"><i class="fa fa-check-circle-o"></i> <?php echo $this->lang->line("Done"); ?></button>
            </div>
        </div>

        <div class="tools-body">

            <h6 class="text-left option-title mt-3"><?php echo $this->lang->line("Layout"); ?></h6>
            
            <div class="form-group">
                <label for="body-layout-bkg-color-form" class="col-form-label"><?php echo $this->lang->line("Background Color"); ?>:</label>
                <div>
                    <div id="body-layout-bkg-color" class="input-group colorpicker-component">
                        <input type="text" value="" class="form-control input-sm" id="body-layout-bkg-color-form">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="body-layout-bkg-color-form" class="col-form-label"><?php echo $this->lang->line("Body Color"); ?>:</label>
                <div>
                    <div id="body-layout-bkg-color-body" class="input-group colorpicker-component">
                        <input type="text" value="" class="form-control input-sm" id="body-layout-bkg-color-body-form">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>

            <h6 class="text-left option-title mt-4"><?php echo $this->lang->line("Header Section"); ?></h6>

            <div class="form-group">
                <label for="head-bkg-color-form" class="col-form-label"><?php echo $this->lang->line("Background Color"); ?>:</label>
                <div>
                    <div id="head-bkg-color" class="input-group colorpicker-component">
                        <input type="text" value="" class="form-control input-sm" id="head-bkg-color-form">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="head-height" class="col-form-label"><?php echo $this->lang->line("Height"); ?>:</label>
                <div class="bs-slider-container">
                    <input type="text" class="form-control input-sm" id="head-height" data-slider-id="head-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">
                    <p class="text-left small"><?php echo $this->lang->line("Height"); ?>: <span id="head-height-val"><?php echo $this->lang->line("auto"); ?></span></p>
                </div>
            </div>


            <div id="dd-body-exists">

                <h6 class="text-left option-title mt-4"><?php echo $this->lang->line("Content Section"); ?></h6>

                <div class="form-group">
                    <label for="content-bkg-color-form" class="col-form-label"><?php echo $this->lang->line("Background Color"); ?>:</label>
                    <div>
                        <div id="content-bkg-color" class="input-group colorpicker-component">
                            <input type="text" value="" class="form-control input-sm" id="content-bkg-color-form">
                            <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content-height" class="col-form-label"><?php echo $this->lang->line("Height"); ?>:</label>
                    <div class="bs-slider-container">
                        <input type="text" class="form-control input-sm" id="content-height" data-slider-id="content-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">
                        <p class="text-left small"><?php echo $this->lang->line("Height"); ?>: <span id="content-height-val"><?php echo $this->lang->line("auto"); ?></span></p>
                    </div>
                </div>

            </div>

            <div id="dd-sidebar-left-exists">
                <h6 class="text-left option-title mt-4"><?php echo $this->lang->line("Left Sidebar Section"); ?></h6>

                <div class="form-group">
                    <label for="left-bkg-color-form" class="col-form-label"><?php echo $this->lang->line("Background Color"); ?>:</label>
                    <div>
                        <div id="left-bkg-color" class="input-group colorpicker-component">
                            <input type="text" value="" class="form-control input-sm" id="left-bkg-color-form">
                            <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="left-height" class="col-form-label"><?php echo $this->lang->line("Height"); ?>:</label>
                    <div class="bs-slider-container">
                        <input type="text" class="form-control input-sm" id="left-height" data-slider-id="left-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">
                        <p class="text-left small"><?php echo $this->lang->line("Height"); ?>: <span id="left-height-val"><?php echo $this->lang->line("auto"); ?></span></p>
                    </div>
                </div>

            </div>

            <div id="dd-sidebar-right-exists">
                <h6 class="text-left option-title mt-4"><?php echo $this->lang->line("Right Sidebar Section"); ?></h6>

                <div class="form-group">
                    <label for="right-bkg-color-form" class="col-form-label"><?php echo $this->lang->line("Background Color"); ?>:</label>
                    <div>
                        <div id="right-bkg-color" class="input-group colorpicker-component">
                            <input type="text" value="" class="form-control input-sm" id="right-bkg-color-form">
                            <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="right-height" class="col-form-label"><?php echo $this->lang->line("Height"); ?>:</label>
                    <div class="bs-slider-container">
                        <input type="text" class="form-control input-sm" id="right-height" data-slider-id="right-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">
                        <p class="text-left small"><?php echo $this->lang->line("Height"); ?>: <span id="right-height-val"><?php echo $this->lang->line("auto"); ?></span></p>
                    </div>
                </div>

            </div>

            <h6 class="text-left option-title mt-4"><?php echo $this->lang->line("Footer Section"); ?></h6>

            <div class="form-group">
                <label for="footer-bkg-color-form" class="col-form-label"><?php echo $this->lang->line("Background Color"); ?>:</label>
                <div>
                    <div id="footer-bkg-color" class="input-group colorpicker-component">
                        <input type="text" value="" class="form-control input-sm" id="footer-bkg-color-form">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div> 

            <div class="form-group">
                <label for="footer-height" class="col-form-label"><?php echo $this->lang->line("Height"); ?>:</label>
                <div class="bs-slider-container">
                    <input type="text" class="form-control input-sm" id="footer-height" data-slider-id="footer-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">
                    <p class="text-left small"><?php echo $this->lang->line("Height"); ?>: <span id="footer-height-val"><?php echo $this->lang->line("auto"); ?></span></p>
                </div>
            </div>

        </div>
    </div>

    <script src="<?php echo base_url('plugins/email-template-builder/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/jquery-ui.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/font-awesome.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/popper.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/sweetalert.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/debounce.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/bootstrap-colorpicker.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/bootstrap-slider.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/spectrum.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/iziToast.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/plugins/medium-editor/medium-editor.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/creative.tools.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/html2canvas.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/creative.tools.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/email-template-builder/js/editor.js'); ?>"></script>
  </body>
</html>
