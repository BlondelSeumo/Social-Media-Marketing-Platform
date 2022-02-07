<?php 
    $this->load->view("include/upload_js");
    include("application/views/sms_email_manager/email/email_section_global_js.php");
    include("application/views/sms_email_manager/email/email_section_css.php");
?>
<style>.note-editable{max-height: 450px !important;}</style>
<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-edit"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("SMS/Email Broadcasting"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url("sms_email_manager/email_campaign_lists"); ?>"><?php echo $this->lang->line("Email Campaigns"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <form action="#" id="edit_message_form" method="POST">
                            <input type="hidden" id="table_id" name="table_id" value="<?php echo $message_data[0]['id']; ?>">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('Message'); ?> 
                                    <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("include lead user first name"); ?>" data-content="<?php echo $this->lang->line("You can include Contacts #FIRST_NAME#, #LAST_NAME#, #UNSUBSCRIBE_LINK# as variable inside your message. The variable will be replaced by corresponding real values when we will send it."); ?>"><i class='fa fa-info-circle'></i> </a>
                                </label>
                                <textarea id="message" name="message" class="form-control" placeholder="<?php echo $this->lang->line("type your message here...") ?>"><?php echo $message_data[0]['email_message']; ?></textarea>
                            </div>    
                        </form>
                    </div>

                    <div class="card-footer brTop">
                        <button class="btn btn-lg btn-primary" id="updateMessage" name="updateMessage" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update Message") ?> </button>

                        <a class="btn btn-lg btn-light float-right" onclick='goBack("sms_email_manager/email_campaign_lists",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>