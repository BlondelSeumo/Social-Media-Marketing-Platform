<?php 
    $this->load->view("include/upload_js");
    include("application/views/sms_email_manager/email/email_section_global_js.php");
    include("application/views/sms_email_manager/email/email_section_css.php");
 ?>

<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-envelope"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
            <a class="btn btn-primary" href="<?php echo base_url('sms_email_manager/create_email_campaign'); ?>">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Email Campaign"); ?>
            </a> 
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("SMS/Email Broadcasting"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body data-card">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="input-group float-left" id="searchbox">
                                    <!-- search by post type -->
                                    <div class="input-group-prepend">
                                        <select class="select2 form-control" id="campaign_status" name="campaign_status">
                                            <option value=""><?php echo $this->lang->line("Status"); ?></option>
                                            <option value="0"><?php echo $this->lang->line("Pending"); ?></option>
                                            <option value="1"><?php echo $this->lang->line("Processing"); ?></option>
                                            <option value="2"><?php echo $this->lang->line("Completed"); ?></option>
                                        </select>
                                    </div>
                                    <input type="text" class="form-control" id="searching_campaign" name="searching_campaign" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" id="email_search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg icon-left btn-icon float-right"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a><input type="hidden" id="post_date_range_val">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive2 email_campaign_table">
                                    <table class="table table-bordered" id="mytable_email_campaign">
                                        <thead>
                                            <tr>
                                                <th>#</th>      
                                                <th><?php echo $this->lang->line("ID"); ?></th>      
                                                <th><?php echo $this->lang->line("Name"); ?></th>
                                                <th class="no-sort centering"><?php echo $this->lang->line("Recipients"); ?></th>
                                                <th class="no-sort centering"><?php echo $this->lang->line("Delivered"); ?></th>
                                                <?php if($this->config->item("enable_open_rate") == "1") { ?>
                                                    <th class="centering no-sort"><?php echo $this->lang->line('Openers'); ?></th>
                                                <?php } ?>

                                                <?php if($this->config->item("enable_click_rate") == "1") { ?>
                                                    <th class="centering no-sort"><?php echo $this->lang->line('Clickers'); ?></th>
                                                    <th class="centering no-sort"><?php echo $this->lang->line('Unsubscribed'); ?></th>
                                                <?php } ?>
                                                <th class="centering"><?php echo $this->lang->line('Scheduled at'); ?></th>
                                                <th class="centering"><?php echo $this->lang->line('status'); ?></th>
                                                <th class="centering no-sort"><?php echo $this->lang->line('Actions'); ?></th>
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



<div class="modal fade" id="sms_logs_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-list"></i> <?php echo $this->lang->line("Email History") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-light alert-dismissible show fade" id="message_div">
                            <div class="alert-body">
                                <button class="close" data-dismiss="alert"><span>×</span></button>
                                This is a light alert.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <a href="javascript:;" id="sms_log_date_range" class="btn btn-primary btn-lg icon-left btn-icon float-right"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a>
                        <input type="hidden" id="sms_log_date_range_val">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive2 data-card">
                            <table class="table table-bordered" id="mytable_sms_logs">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('#'); ?></th>
                                        <th><?php echo $this->lang->line('id'); ?></th>
                                        <th><?php echo $this->lang->line("SMS API"); ?></th>
                                        <th><?php echo $this->lang->line("Send To"); ?></th>
                                        <th><?php echo $this->lang->line('Sent Time'); ?></th>
                                        <th><?php echo $this->lang->line("SMS UID"); ?></th>
                                        <th><?php echo $this->lang->line("Delivery Status"); ?></th>
                                        <th><?php echo $this->lang->line("Message"); ?></th>
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