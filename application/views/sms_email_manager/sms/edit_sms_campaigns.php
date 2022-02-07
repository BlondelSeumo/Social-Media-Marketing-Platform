<?php include("application/views/sms_email_manager/sms/sms_section_global_js.php"); ?>
<style>
    ::placeholder{font-size:12px;}
    .dropzone{min-height:0px !important;}
    .dz-message{margin:40px !important;}
    .waiting {height: 100%;width:100%;display: table;}
    .waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}
</style>
<?php
    $xlabels = $campaign_data[0]["label_ids"];
    $xexcluded_label_ids = $campaign_data[0]["excluded_label_ids"];
    $xpage_id = $campaign_data[0]["page_id"];
    $contact_id = $campaign_data[0]['contact_type_id'];
    $manual_phone = $campaign_data[0]['manual_phone'];
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

<?php if($manual_phone != "")
{ ?>
    <script type="text/javascript">setTimeout(function() {$("#to_numbers").trigger('keyup');}, 2000);</script>
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
                <form action="#" id="updated_sms_campaign_form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?php echo $campaign_data[0]["id"];?>" class="form-control"  name="campaign_id" id="campaign_id">
                    <input type="hidden" value="<?php echo $campaign_data[0]["total_thread"];?>" class="form-control"  name="previous_thread" id="previous_thread">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo $this->lang->line('Campaign Details'); ?></h4>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center waiting">
                                      <i class="fas fa-spinner fa-spin blue text-center"></i>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Campaign Name'); ?></label>
                                        <input type="text" class="form-control" id="campaign_name" name="campaign_name" value="<?php if(isset($campaign_data[0]['campaign_name'])) echo $campaign_data[0]['campaign_name']; else echo set_value('campaign_name');?>">
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('SMS API'); ?></label>
                                        <select name='from_sms' id='from_sms' class='form-control select2' style="width:100%;">
                                            <option value=''><?php echo $this->lang->line('Select API');?></option>
                                            <?php 
                                                foreach($sms_option as $id=>$option)
                                                {
                                                    if($id == $campaign_data[0]['api_id']) echo $selected = 'selected';
                                                    else echo $selected = '';

                                                    echo "<option value='{$id}' {$selected}>{$option}</option>";
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
                                        <textarea id="message" name="message" class="form-control" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:140px !important;"><?php echo $campaign_data[0]['campaign_message']; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h4><?php echo $this->lang->line('Messenger Subscribers'); ?></h4>
                                                </div>
                                                <div class="card-body left_body" style="min-height:388px;">
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

                                        <div class="col-12 col-md-6">
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h4><?php echo $this->lang->line('SMS Subscriber (External)'); ?></h4>
                                                </div>
                                                <div class="card-body right_body">
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
                                                                if(in_array($key,$selected_contact_gorups)) echo $checked = "selected";
                                                                else echo $checked = "";

                                                                echo "<option value='{$key}' {$checked}>{$value}</option>";
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
                                                        <textarea style='height:120px !important' placeholder="<?php echo $this->lang->line('You can type comma seperated numbers with country code here. You can also import numbers from a CSV file and numbers will be mereged here.') ;?>" id="to_numbers" name="to_numbers" class="form-control"><?php echo $campaign_data[0]['manual_phone'] ?></textarea>
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
                                        <input type="text" class="form-control" id="campaign_delay" name="campaign_delay" value="<?php echo $campaign_data[0]['campaign_delay']; ?>">
                                    </div>
                                </div>

                                <input type="hidden" value="" id="country_code_add" name="country_code_add">
                                <input type="hidden" value="" id="country_code_remove" name="country_code_remove">

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

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("Schedule Time") ?>  <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("schedule time") ?>" data-content="<?php echo $this->lang->line("Select date, time and time zone when you want to start this campaign.") ?>"><i class='fa fa-info-circle'></i> </a></label>
                                        <input placeholder="<?php echo $this->lang->line("Choose time");?>" name="schedule_time" id="schedule_time" class="form-control datepicker_x" value="<?php echo $campaign_data[0]['schedule_time'] ?>" type="text"/>
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

                        <div class="card-footer bg-whitesmoke">
                            <button class="btn btn-lg btn-primary" id="update_sms_campaign_btn" name="update_sms_campaign_btn" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update Campaign") ?> </button>

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


