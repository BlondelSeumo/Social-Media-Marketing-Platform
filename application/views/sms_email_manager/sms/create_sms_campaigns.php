<?php include("application/views/sms_email_manager/sms/sms_section_global_js.php"); ?>
<style>
    ::placeholder{font-size:12px;}
    .dropzone{min-height:0px !important;}
    .dz-message{margin:40px !important;}
</style>
<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("Broadcasting"); ?></a></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url("sms_email_manager/sms_campaign_lists"); ?>"><?php echo $this->lang->line("SMS Campaigns"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <form action="#" id="sms_campaign_form" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo $this->lang->line('Campaign Details'); ?></h4>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Campaign Name'); ?></label>
                                        <input type="text" class="form-control" id="campaign_name" name="campaign_name">
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('SMS API'); ?></label>
                                        <select name='from_sms' id='from_sms' class='form-control select2' style="width:100%;">
                                            <option value=''><?php echo $this->lang->line('Select API');?></option>
                                            <?php 
                                                foreach($sms_option as $id=>$option)
                                                {
                                                    echo "<option value='{$id}'>{$option}</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Message'); ?> 
                                            <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("include lead user first name"); ?>" data-content="<?php echo $this->lang->line("You can include Contacts #FIRST_NAME#, #LAST_NAME# as variable inside your message. The variable will be replaced by corresponding real values when we will send it."); ?>"><i class='fa fa-info-circle'></i> </a> 
                                        </label>
                                        <span class='float-right'>
                                            <a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can include #LAST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>' class='btn-outline btn-sm' id='contact_last_name'><i class='fas fa-user'></i> <?php echo $this->lang->line("Last Name") ?></a>
                                        </span>
                                        <span class='float-right'>
                                            <a data-toggle="tooltip" data-placement="top" title='<?php echo $this->lang->line("You can include #FIRST_NAME# variable inside your message. The variable will be replaced by real name when we will send it.") ?>' class='btn-outline btn-sm' id='contact_first_name'><i class='fas fa-user'></i> <?php echo $this->lang->line("First Name") ?></a>
                                        </span>
                                        <textarea id="message" name="message" class="form-control" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:140px !important;"></textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h4><?php echo $this->lang->line('Messenger Subscribers'); ?></h4>
                                                </div>
                                                <div class="card-body" style="min-height:388px;">
                                                    <div class="form-group">
                                                        <ul class="list-group">
                                                            <div class="row">
                                                                <div class="col-12 col-md-6">
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center pointer" title='<?php echo $this->lang->line('Total Page Subscribers With Phone Number'); ?>'>
                                                                      <?php echo $this->lang->line("Page Subscribers"); ?> 
                                                                      <span class="badge badge-primary" id="page_subscriber">0</span>
                                                                    </li>
                                                                </div>
                                                                <div class="col-12 col-md-6">
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center pointer" title='<?php echo $this->lang->line('Targetted Reach'); ?>'>
                                                                      <?php echo $this->lang->line("Targetted Reach"); ?>
                                                                      <span class="badge badge-primary" id="targetted_subscriber">0</span>
                                                                    </li>
                                                                </div>
                                                            </div>
                                                        </ul>
                                                    </div>

                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('Select Page'); ?> </label>
                                                        <select class="form-control select2" id="page" name="page" style="width:100%;">
                                                            <option value=""><?php echo $this->lang->line("Select Page");?></option> 
                                                            <?php
                                                            foreach($page_info as $key=>$value)
                                                            {
                                                                $id=$value['id'];
                                                                $page_name=$value['page_name'];
                                                                echo "<option value='{$id}'>{$page_name}</option>";
                                                            }
                                                            ?>                 
                                                        </select>
                                                    </div>

                                                    <h6 class="blue">
                                                        <?php echo $this->lang->line("Targeting Options");?>
                                                        <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Targeting Options"); ?>" data-content="<?php echo $this->lang->line("You can send to specific labels, also can exclude specific labels. Gender, timezone and locale data are only available for bot subscribers meaning targeting by gender/timezone/locale  will only work for subscribers that have been migrated as bot subscribers or come through messenger bot in our system."); ?>"><i class='fa fa-info-circle'></i> </a>                
                                                    </h6><br>

                                                    <div class="row hidden" id="dropdown_con">
                                                        <div class="col-12 col-md-6" >
                                                            <div class="form-group">
                                                                <label style="width:100%">
                                                                    <?php echo $this->lang->line("Target Labels") ?>
                                                                    <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Choose Labels"); ?>" data-content="<?php echo $this->lang->line("If you do not want to send to all page subscriber then you can target by labels."); ?>"><i class='fa fa-info-circle'></i> </a>
                                                                </label>
                                                                <span id="first_dropdown"><?php echo $this->lang->line("Loading labels..."); ?></span>                                
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-6">
                                                            <div class="form-group">
                                                                <label style="width:100%">
                                                                    <?php echo $this->lang->line("Exclude Labels") ?>
                                                                    <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Exclude Labels"); ?>" data-content="<?php echo $this->lang->line("If you do not want to send to a specific label, you can mention it here. Unsubscribe label will be excluded automatically."); ?>"><i class='fa fa-info-circle'></i> </a>
                                                                </label>
                                                                <span id="second_dropdown"><?php echo $this->lang->line("Loading labels..."); ?></span>                 
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-12 col-md-4">
                                                            <label>
                                                                <?php echo $this->lang->line("Gender"); ?>

                                                            </label>
                                                            <?php
                                                            $gender_list = array(""=>$this->lang->line("Select"),"male"=>"Male","female"=>"Female");
                                                            echo form_dropdown('user_gender',$gender_list,'',' class="form-control select2" id="user_gender" style="width:100%"'); 
                                                            ?>
                                                        </div>


                                                        <div class="form-group col-12 col-md-4">
                                                            <label><?php echo $this->lang->line("Time Zone") ?></label>
                                                            <?php
                                                            $time_zone_numeric[''] = $this->lang->line("Select");
                                                            echo form_dropdown('user_time_zone',$time_zone_numeric,'',' class="form-control select2" id="user_time_zone" style="width:100%"'); 
                                                            ?>
                                                        </div>

                                                        <div class="form-group col-12 col-md-4">
                                                            <label><?php echo $this->lang->line("Locale") ?></label>
                                                            <?php
                                                            $locale_list[''] = $this->lang->line("Select");
                                                            echo form_dropdown('user_locale',$locale_list,'',' class="form-control select2" id="user_locale" style="width:100%"'); 
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h4><?php echo $this->lang->line('SMS Subscriber (External)'); ?></h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <ul class="list-group">
                                                            <div class="row">
                                                                <div class="col-12 col-md-6">
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center pointer" title='<?php echo $this->lang->line('Total Contact Group Numbers'); ?>'>
                                                                      <?php echo $this->lang->line("Contact Numbers"); ?> 
                                                                      <span class="badge badge-primary" id="contact_numbers">0</span>
                                                                    </li>
                                                                </div>
                                                                <div class="col-12 col-md-6">
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center pointer" title='<?php echo $this->lang->line('Total Manual Numbers'); ?>'>
                                                                      <?php echo $this->lang->line("Manual Numbers"); ?>
                                                                      <span class="badge badge-primary" id="manual_numbers">0</span>
                                                                    </li>
                                                                </div>
                                                            </div>
                                                        </ul>
                                                    </div>

                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('Select Contacts'); ?> </label>
                                                        <select multiple="multiple"  class="form-control select2" id="contacts_id" name="contacts_id[]" style="width:100%;">
                                                            <?php
                                                            foreach($groups_name as $key=>$value)
                                                            {
                                                                echo "<option value='{$key}'>{$value}</option>";
                                                            }
                                                            ?>                 
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('Numbers To Send');?> 
                                                            <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("include lead user first name"); ?>" data-content="<?php echo $this->lang->line("Beside contact groups If you also want to send messages to manual numbers, you can simply put your numbers in below field with comma separated. System will send message to both your contact numbers and also to your manual numbers."); ?>"><i class='fa fa-info-circle'></i> </a>
                                                        </label>
                                                        <span class="float-right">
                                                            <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("include lead user first name"); ?>" data-content="<?php echo $this->lang->line("If you want to upload numbers from your CSV file, you can upload your CSV file. You will see your uploaded files number at the below box."); ?>"><i class='fa fa-info-circle'></i> </a>
                                                            <a style="border-radius:5px;" id="import_from_csv" data-toggle="modal" href='#csv_import_modal' class="btn btn-outline-primary btn-sm"><i class="fa fa-upload"></i> <?php echo $this->lang->line('Upload CSV');?></a>
                                                        </span>
                                                        <textarea style='height:120px !important' placeholder="<?php echo $this->lang->line('You can type comma seperated numbers with country code here. You can also import numbers from a CSV file and numbers will be mereged here.') ;?>" id="to_numbers" name="to_numbers" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Delay (second)'); ?>
                                            <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("Add Campaign Delay"); ?>" data-content="<?php echo $this->lang->line("If you want any delay during sending sms to phone numbers, then provide a delay, keep it blank if you do not need any delay."); ?>"><i class='fa fa-info-circle'></i> </a>
                                        </label>
                                        <input type="text" class="form-control" id="campaign_delay" name="campaign_delay">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Country Code'); ?>
                                            <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("Add or remove country code"); ?>" data-content="<?php echo $this->lang->line("If you want to add your country code to your contact numbers, then simply put you contry code here, then select add option from dropdown. Country Code will be added into your every contact number. You can also remove country code by selecting remove option from dropdown menu."); ?>"><i class='fa fa-info-circle'></i> </a>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="country_code" name="country_code">
                                            <div class="input-group-append">
                                                <select class="custom-select select2" name="country_code_action" id="country_code_action" style="width:100%;">
                                                    <option value=""><?php echo $this->lang->line('Actions'); ?></option>
                                                    <option value="1" id="item-1"><?php echo $this->lang->line("Add"); ?></option>
                                                    <option value="0" id="item-2"><?php echo $this->lang->line("Remove"); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("Sending Time") ?>
                                             <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Sending Time") ?>" data-content="<?php echo $this->lang->line("If you schedule a campaign, system will automatically process this campaign at mentioned time and time zone. Schduled campaign may take upto 1 hour longer than your schedule time depending on server load.") ?>"><i class='fa fa-info-circle'></i> </a>
                                        </label><br>

                                        <label class="custom-switch mt-2">
                                            <input type="checkbox" name="schedule_type" value="now" class="custom-switch-input" checked>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description"><?php echo $this->lang->line('Send Now');?></span>
                                            <span class="red"><?php echo form_error('schedule_type'); ?></span>
                                        </label>
                                    </div>
                                </div>

                                <input type="hidden" value="" id="country_code_add" name="country_code_add">
                                <input type="hidden" value="" id="country_code_remove" name="country_code_remove">

                                <div class="col-12 col-md-12 schedule_block_item">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line("Schedule Time") ?>  <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("schedule time") ?>" data-content="<?php echo $this->lang->line("Select date, time and time zone when you want to start this campaign.") ?>"><i class='fa fa-info-circle'></i> </a></label>
                                                <input placeholder="<?php echo $this->lang->line("Choose time");?>"  name="schedule_time" id="schedule_time" class="form-control datepicker_x" type="text"/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">                
                                                <label><?php echo $this->lang->line('Time Zone'); ?></label>
                                                <?php
                                                    $time_zone[''] = $this->lang->line("please select");
                                                    echo form_dropdown('time_zone',$time_zone,$this->config->item('time_zone'),' class="form-control select2" id="time_zone" style="width:100%;"'); 
                                                ?>
                                           </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-whitesmoke">
                            <button class="btn btn-lg btn-primary" id="create_campaign" name="create_campaign" type="button"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Create Campaign") ?> </button>

                            <a class="btn btn-lg btn-light float-right" onclick='goBack("sms_email_manager/sms_campaign_lists",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- CSV file upload modal -->
<div class="modal fade" id="csv_import_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title blue"><i class="fa fa-file-text"></i> <?php echo $this->lang->line('Import numbers from CSV'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form action="" id="csv_import_form" method="POST" enctype="multipart/form-data">
                    <div class="col-12 col-md-12">
                        <div class="form-group">
                            <div id="dropzone" class="dropzone dz-clickable">
                                <div class="dz-default dz-message" style="">
                                    <input class="form-control" name="csv_file" id="csv_file" placeholder="" type="hidden">
                                    <span style="font-size: 20px;"><i class="fas fa-cloud-upload-alt" style="font-size: 35px;color: var(--blue);"></i> <?php echo $this->lang->line('Upload'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo $this->lang->line('Cancel');?></button>
                <button type="button" id="import_submit" class="btn btn-primary"><i class="fa fa-upload"></i> <?php echo $this->lang->line('Import');?></button>
            </div>
        </div>
    </div>
</div>


