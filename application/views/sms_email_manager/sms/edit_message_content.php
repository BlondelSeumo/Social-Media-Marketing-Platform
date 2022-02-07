<?php include("application/views/sms_email_manager/sms/sms_section_global_js.php"); ?>

<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-edit"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("Broadcasting"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url("sms_email_manager/sms_campaign_lists"); ?>"><?php echo $this->lang->line("SMS Campaigns"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-envelope"></i> <?php echo $this->lang->line('Message Contents'); ?></h4>
                    </div>
                    <div class="card-body">
                        <form action="#" id="edit_message_form" method="POST">
                            <input type="hidden" id="table_id" name="table_id" value="<?php echo $message_data[0]['id']; ?>">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('Message'); ?> 
                                    <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("include lead user first name"); ?>" data-content="<?php echo $this->lang->line("You can include Contacts #FIRST_NAME#, #LAST_NAME#, #MOBILE#, #EMAIL_ADDRESS# as variable inside your message. The variable will be replaced by corresponding real values when we will send it."); ?>"><i class='fa fa-info-circle'></i> </a>
                                </label>
                                <span class='float-right'>
                                    <a title="<?php echo $this->lang->line("include contact last name"); ?>" class='btn-outline btn-sm' id="contact_last_name"><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                                </span>
                                <span class='float-right'>
                                    <a title="<?php echo $this->lang->line("include contact first name"); ?>" class='btn-outline btn-sm' id="contact_first_name"><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                                </span>
                                <textarea id="message" name="message" class="form-control" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:130px !important;"><?php echo $message_data[0]['campaign_message']; ?></textarea>
                            </div>    
                        </form>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-lg btn-primary" id="updateMessage" name="updateMessage" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Edit Message") ?> </button>

                        <a class="btn btn-lg btn-light float-right" onclick='goBack("sms_email_manager/sms_campaign_lists",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>