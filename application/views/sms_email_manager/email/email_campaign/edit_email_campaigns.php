<?php 
    $this->load->view("include/upload_js");
    include("application/views/sms_email_manager/email/email_section_global_js.php");
    include("application/views/sms_email_manager/email/email_section_css.php");
 ?>

<?php
     $xlabels = $campaign_data[0]["label_ids"];
     $xexcluded_label_ids = $campaign_data[0]["excluded_label_ids"];
     $xpage_id = $campaign_data[0]["page_id"];
     $contact_id = $campaign_data[0]['contact_type_id'];
 ?>

 <?php if($xlabels!="") 
 { ?>
     <script type="text/javascript">
        var xlabels =  "<?php echo $xlabels;?>";
     </script>
 <?php 
 } ?>

 <?php if($xexcluded_label_ids!="")
 { ?>
     <script type="text/javascript">var xexcluded_label_ids =  "<?php echo $xexcluded_label_ids;?>";</script>
 <?php 
 } ?>

 <?php if($contact_id != "")
 { ?>
     <script type="text/javascript">setTimeout(function() {$("#contacts_id").trigger('change');}, 2000);</script>
 <?php 
 } ?>


 <script type="text/javascript">
     var xpage_id = '<?php echo $xpage_id; ?>';
     $(document).ready(function($) {
         if(xpage_id != "0")
         {
             $("#page").val(xpage_id).trigger('change');
         } else
         {
             $(".waiting").hide();
         }
     });
 </script>

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
                <form action="#" id="update_email_campaign_form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?php echo $campaign_data[0]["id"];?>" class="form-control"  name="campaign_id" id="campaign_id">
                    <input type="hidden" value="<?php echo $campaign_data[0]["total_thread"];?>" class="form-control"  name="previous_thread" id="previous_thread">
                    <input type="hidden" name="selected-tab" id="selected-tab" value="<?php echo $template_id ? 'drag-and-drop-tab' : 'rich-text-editor-tab'; ?>">
                    <div class="card">
                        <div class="card-body">
                            <div class="row" id="campaign_details">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Campaign Name'); ?></label>
                                        <input type="text" class="form-control" id="campaign_name" name="campaign_name" value="<?php echo $campaign_data[0]['campaign_name']; ?>" placeholder="<?php echo $this->lang->line('Campaign Name'); ?>">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Email Subject'); ?>  </label>
                                        <input placeholder="<?php echo $this->lang->line('Email Subject'); ?>" id="email_subject" name="email_subject" value="<?php if(isset($campaign_data[0]['email_subject'])) echo $campaign_data[0]['email_subject']; else echo set_value('subject');?>" type="text" class="form-control"/>
                                        <span class="red" id="subject_error"></span>
                                    </div>
                                </div>

                                <div class="col-12">

                                    <ul class="nav nav-pills" id="email-template-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $template_id ? '' : 'active'; ?>" id="rich-text-editor-tab" data-toggle="tab" href="#rich-text-editor" role="tab" aria-controls="rich-text-editor" aria-selected="true"><?php echo $this->lang->line('Rich Text Editor'); ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $template_id ? 'active' : ''; ?>" id="drag-and-drop-tab" data-toggle="tab" href="#drag-and-drop" role="tab" aria-controls="drag-and-drop" aria-selected="false"><?php echo $this->lang->line('Drag and Drop'); ?></a>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="rich-text-editor-content">
                                        <div class="tab-pane fade show <?php echo $template_id ? '' : 'active' ?>" id="rich-text-editor" role="tabpanel" aria-labelledby="rich-text-editor-tab">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('Message'); ?> 
                                                    <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("include lead user first name"); ?>" data-content="<?php echo $this->lang->line("You can include Contacts #FIRST_NAME#, #LAST_NAME#, #UNSUBSCRIBE_LINK# as variable inside your message. The variable will be replaced by corresponding real values when we will send it."); ?>"><i class='fa fa-info-circle'></i> </a>
                                                </label>

                                                <textarea id="message" name="message" class="form-control" placeholder="<?php echo $this->lang->line("type your message here...") ?>"><?php if(isset($campaign_data[0]['email_message']) && ! $template_id) echo $campaign_data[0]['email_message']; else echo set_value('message');?></textarea>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade show <?php echo $template_id ? 'active' : '' ?>" id="drag-and-drop" role="tabpanel" aria-labelledby="drag-and-drop-tab">
                                            <div class="col-6 pl-0 pr-0">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('Email Template'); ?></label>
                                                    <select  id="email-template" name="email-template" class="form-control select2" style="width:100%;">
                                                        <option value=''><?php echo $this->lang->line('Select template'); ?></option>
                                                        <?php foreach($email_templates as $key => $template): ?>
                                                            <option value="<?php echo $key; ?>" <?php echo ($key == $template_id) ? 'selected' : ''; ?>><?php echo $template; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Email API'); ?></label>
                                        <select  id='from_email' name="from_email" class='form-control select2' style="width:100%;">
                                            <option value=''><?php echo $this->lang->line('Select API'); ?></option>
                                            <?php 
                                                foreach($email_option as $id=>$option)
                                                {
                                                    if($id == $email_name) echo $selected = 'selected';
                                                    else echo $selected = '';

                                                    echo "<option value='{$id}' {$selected}>{$option}</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Attachment'); ?> <?php echo $this->lang->line('(Max 20MB)');?>
                                            <a href="#" data-placement="top"  data-toggle="popover" title="<?php echo $this->lang->line("include lead user first name"); ?>" data-content="<?php echo $this->lang->line("You can attach an attachment up to 20MB size. If you need multiple files to send, compress them in to a zip/rar file. Please remember that, you can not have both email with variables & attachment together.").' '.$this->lang->line("Allowed files are .png, .jpg,.jpeg, .docx, .txt, .pdf, .ppt, .zip, .avi, .mp4, .mkv, .wmv, .mp3"); ?>"><i class='fa fa-info-circle'></i> </a>

                                        </label>
                                        <div id="uploademail_attachment" class="pointer"><?php echo $this->lang->line('Upload'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row layOut">
                                <div class="col-12 col-md-6 sub_selection">
                                    <div class="card bx-none">
                                        <div class="card-header">
                                            <h4 class="blue"><?php echo $this->lang->line('Broadcasting Subscribers'); ?></h4>
                                        </div>

                                        <div class="card-body">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('Select Email Subscribers (External)'); ?>
                                                    <a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('You can select Subscribers from added contact groups.'); ?>"><i class="fas fa-info-circle"></i></a>
                                                </label>
                                                <select multiple="multiple" class="form-control select2" id="contacts_id" name="contacts_id[]" style="width:100%;">
                                                    <?php
                                                        foreach($groups_name as $key=>$value)
                                                        {
                                                            if(in_array($key,$selected_contact_gorups)) echo $checked = "selected";
                                                            else echo $checked = "";

                                                            echo "<option value='{$key}' {$checked}>{$value}</option>";
                                                        }
                                                     ?>             
                                                </select>
                                            </div>

                                            
                                            <div class="card card-primary mb-0">
                                                <div class="card-header">
                                                    <h4 class="blue"><?php echo $this->lang->line('Messenger Subscribers'); ?></h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('Select Page'); ?> </label>
                                                        <select class="form-control select2" id="page" name="page" style="width:100%;">
                                                            <option value=""><?php echo $this->lang->line("Select Page");?></option> 
                                                            <?php
                                                            foreach($page_info as $key=>$value)
                                                            {
                                                                $id=$value['id'];
                                                                $page_name=$value['page_name'];

                                                                $selelcted="";
                                                                if($id==$campaign_data[0]['page_id']) $selelcted="selected";
                                                                echo "<option value='{$id}' {$selelcted}>{$page_name}</option>";
                                                            }
                                                            ?>                 
                                                        </select>
                                                    </div>

                                                    <h6 class="blue">
                                                        <?php echo $this->lang->line("Targeting Options");?>
                                                        <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Targeting Options"); ?>" data-content="<?php echo $this->lang->line("You can send to specific labels, also can exclude specific labels. Gender, timezone and locale data are only available for bot subscribers meaning targeting by gender/timezone/locale  will only work for subscribers that have been migrated as bot subscribers or come through messenger bot in our system."); ?>"><i class='fa fa-info-circle'></i> </a>                
                                                    </h6>

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
                                                            echo form_dropdown('user_gender',$gender_list,$campaign_data[0]['user_gender'],' class="form-control select2" id="user_gender"'); 
                                                            ?>
                                                        </div>


                                                        <div class="form-group col-12 col-md-4">
                                                            <label><?php echo $this->lang->line("Time Zone") ?></label>
                                                            <?php
                                                            $time_zone_numeric[''] = $this->lang->line("Select");
                                                            echo form_dropdown('user_time_zone',$time_zone_numeric,$campaign_data[0]['user_time_zone'],' class="form-control select2" id="user_time_zone"'); 
                                                            ?>
                                                        </div>

                                                        <div class="form-group col-12 col-md-4">
                                                            <label><?php echo $this->lang->line("Locale") ?></label>
                                                            <?php
                                                            $locale_list[''] = $this->lang->line("Select");
                                                            echo form_dropdown('user_locale',$locale_list,$campaign_data[0]['user_locale'],' class="form-control select2" id="user_locale"'); 
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 d-none d-sm-block sub_counter">
                                    <div class="card bx-none">
                                        <div class="card-header">
                                            <h4 class="blue"><?php echo $this->lang->line('Email Counter'); ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?php echo $this->lang->line("Targetted External Subscribers"); ?> 
                                                    <span class="badge badge-primary badge-pill" id="contact_emails">0/0</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?php echo $this->lang->line("Targetted Page Subscribers"); ?> 
                                                    <span class="badge badge-primary badge-pill" id="page_subscriber">0/0</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center active">
                                                    <?php echo $this->lang->line("Total Targetted Reach"); ?>
                                                    <span class="badge badge-primary" id="total_targetted_subscribers">0/0</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div><br>

                            <div class="row hidden">
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
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("Schedule Time") ?>  <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("schedule time") ?>" data-content="<?php echo $this->lang->line("Select date, time and time zone when you want to start this campaign.") ?>"><i class='fa fa-info-circle'></i> </a></label>
                                        <input placeholder="<?php echo $this->lang->line("Choose time");?>"  name="schedule_time" id="schedule_time" value="<?php echo $campaign_data[0]['schedule_time']; ?>" class="form-control datepicker_x" type="text"/>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">                
                                        <label><?php echo $this->lang->line('Time Zone'); ?></label>
                                        <?php
                                            $time_zone[''] = $this->lang->line("please select");
                                            echo form_dropdown('time_zone',$time_zone,$campaign_data[0]['time_zone'],' class="form-control select2" id="time_zone" style="width:100%;"'); 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer brTop">
                            <button class="btn btn-lg btn-primary" id="update_campaign" name="update_campaign" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update Campaign") ?> </button>

                            <a class="btn btn-lg btn-light float-right" onclick='goBack("sms_email_manager/email_campaign_lists",0)'><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
