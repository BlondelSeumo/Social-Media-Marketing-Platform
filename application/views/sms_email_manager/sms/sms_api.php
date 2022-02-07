<?php include("application/views/sms_email_manager/sms/sms_section_global_js.php"); ?>

<style>
    .activities .activity .activity-detail{width:100%;padding: 0 15px 0 0;box-shadow: none !important;}
    .activity-detail::before { content: none !important; }
    .activity::before{content:none !important;}
    .activities:last-child{border-bottom:none !important;margin-bottom:10px;}
/*    .dropdown-toggle::after{content:none !important;}
    .dropdown-toggle::before{content:none !important;}*/
    .bbw{border-bottom-width: thin !important;border-bottom:solid .5px #f9f9f9 !important;padding-bottom:20px;}
    .brbtm{border-bottom:solid .5px #f9f9f9 !important;}
    #searching{max-width: 30% !important;}
    #response .toolbar { display: none; }
    @media (max-width: 575.98px) {#searching{max-width: 77% !important;}}
</style>

<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-plug"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
            <!-- <a class="btn btn-primary add_gateway" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New API"); ?>
            </a> 
            &nbsp;
            <a class="btn btn-primary add_custom_gateway" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Custom SMS API"); ?>
            </a> 
            &nbsp;
            <a class="btn btn-primary add_custom_gateway_post_method" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Custom SMS API[Post Method]"); ?>
            </a> -->
            <div class="dropdown d-inline">
                <button 
                        class="btn btn-primary dropdown-toggle" 
                        type="button" 
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                    >
                        <?php echo $this->lang->line("Add New API"); ?>
                    </button>
            <div class="dropdown-menu" style="width:250px;">
             <a class="dropdown-item has-icon add_gateway" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add Default Gateway"); ?>
            </a> 

            <a class="dropdown-item has-icon add_custom_gateway" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add Custom SMS API [GET]"); ?>
            </a> 

            <a class="dropdown-item has-icon add_custom_gateway_post_method" href="#">
                <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Add Custom SMS API [POST]"); ?>
            </a>
        </div>
    </div>


</div>
<div class="section-header-breadcrumb">
    <div class="breadcrumb-item"><a href="<?php echo base_url('integration'); ?>"><?php echo $this->lang->line("Integration"); ?></a></div>
    <div class="breadcrumb-item"><?php echo $page_title; ?></div>
</div>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body data-card">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive2">
                                <table class="table table-bordered" id="mytable">
                                    <thead>
                                        <tr>
                                            <th>#</th>      
                                            <th><?php echo $this->lang->line("ID"); ?></th>      
                                            <th><?php echo $this->lang->line("Gateway"); ?></th>
                                            <th><?php echo $this->lang->line("Sender/ Sender ID/ Mask/ From"); ?></th>
                                            <th><?php echo $this->lang->line("Status"); ?></th>
                                            <th><?php echo $this->lang->line('Actions'); ?></th>
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


<div class="modal fade" id="api_info" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="min-width:50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line('API Informations'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="info_body">
                <div class="card" id="api_info_modal_body">
                    <div class="card-body">
                        <div class="activities">
                            <div class="row">
                                <div class="col-12">
                                    <div class="activity">
                                        <div class="activity-detail">
                                            <div class="mb-2">
                                                <h5 class="text-job text-primary" id="auth_id_title"><?php echo $this->lang->line('Auth ID/ Auth Key/API Key/ MSISDN/ Account SID/ Account ID/ Username/ Admin'); ?></h5>
                                            </div>
                                            <small id="auth_id_val"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="activity">
                                        <div class="activity-detail">
                                            <div class="mb-2">
                                                <h5 class="text-job text-primary" id="api_secret_title"><?php echo $this->lang->line('Auth Token/ API Secret/ Password'); ?></h5>
                                            </div>
                                            <small id="api_secret_val"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12" id="routesmsHostname_div">
                                    <div class="activity">
                                        <div class="activity-detail">
                                            <div class="mb-2">
                                                <h5 class="text-job text-primary" id="routesmsHostname"><?php echo $this->lang->line('Routesms Hostname'); ?></h5>
                                            </div>
                                            <small id="routesmsHostname_val"></small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="activity">
                                        <div class="activity-detail">
                                            <div class="mb-2">
                                                <h5 class="text-job text-primary" id="api_id_title"><?php echo $this->lang->line('API ID'); ?></h5>
                                            </div>
                                            <small id="api_id_val"></small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="activity">
                                        <div class="activity-detail">
                                            <div class="mb-2">
                                                <h5 class="text-job text-primary" id="remaining_credits_title"><?php echo $this->lang->line('Remaining Credits'); ?> 
                                                <small class="text-dark">[plivo, clickatell, clickatell-platform, nexmo, africastalking.com]</small>
                                            </h5>
                                        </div>
                                        <small id="remaining_credits_val"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="add_sms_api_form_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center blue">
                    <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New SMS API"); ?>&nbsp;
                    <a href="#" class="btn btn-primary btn-sm" title="<?php echo $this->lang->line("Click to See the Instruction guide on SMS API"); ?>" id="instruction_guide"><i class="fas fa-info-circle" style="font-size: 12px !important;"></i> <?php echo $this->lang->line('Instructions'); ?></a>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">                    
                        <form action="#" enctype="multipart/form-data" id="sms_api_form" method="post">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('Gateway Name'); ?></label>
                                        <?php  
                                        $gateway_lists[''] = $this->lang->line("Select Gateway");
                                        echo form_dropdown('gateway_name',$gateway_lists,set_value('gateway_name'),'class="form-control select2" id="gateway_name" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label id="authID"><?php echo $this->lang->line('Auth ID/ Auth Key/ API Key/ MSISDN/ Account SID/ Account ID/ Username/ Admin'); ?></label>
                                        <input type="text" class="form-control" name="username_auth_id" id="username_auth_id">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label id="authToken"><?php echo $this->lang->line('Auth Token/ API Secret/ Password'); ?></label>
                                        <input type="text" class="form-control" name="password_auth_token" id="password_auth_token">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6" id="routehostdiv">
                                    <div class="form-group">
                                        <label id="hostname"><?php echo $this->lang->line('Routesms Host Name'); ?>
                                        <a href="#" data-placement="top" data-html="true" data-toggle="popover" title="<?php echo $this->lang->line("Message"); ?>" data-content="<?php echo $this->lang->line("Write your routesms.com registered hostname which was provided from routesms.com. You've must include your hostname as given below example formate. Example <b>http://smsplus.routesms.com/</b>"); ?>"><i class='fa fa-info-circle'></i> </a>
                                    </label>
                                    <strong class="float-right">[i.e: http://smsplus.routesms.com/ ]</strong> 
                                    <input type="text" class="form-control" name="routesms_host_name" id="routesms_host_name">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label id="apiId"><?php echo $this->lang->line('API ID'); ?></label>
                                    <input type="text" class="form-control" name="api_id" id="api_id">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label id="sender"><?php echo $this->lang->line('Sender/ Sender ID/ Mask/ From'); ?></label>
                                    <input type="text" class="form-control" name="phone_number" id="phone_number">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label style="margin-bottom:20px;"><?php echo $this->lang->line('Status'); ?></label><br>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="status" value="1" id="status" class="custom-switch-input" checked>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <div class="modal-footer bg-whitesmoke">
            <div class="col-12 padding-0">
                <button class="btn btn-lg btn-primary" id="save_api" name="save_api" type="button"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save") ?> </button>
                <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="update_sms_api_form_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title text-center">
                    <i class="fas fa-edit"></i> <?php echo $this->lang->line("Update SMS API"); ?>
                    <a href="#" class="btn btn-primary btn-sm" title="<?php echo $this->lang->line("Click to See the Instruction guide on SMS API"); ?>" id="instruction_guide"><i class="fas fa-info-circle" style="font-size: 12px !important;"></i> <?php echo $this->lang->line('Instructions'); ?></a>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" id="#">
                <div id="updated_form_modal_body"></div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12 padding-0">
                    <button class="btn btn-lg btn-primary" id="update_api" name="update_api" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Update");?></button>
                    <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel"); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="instruction_guide_modal">
    <div class="modal-dialog" style="min-width:40%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">
                    <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Instructions to configure SMS API"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="activities">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : Planet IT</h5>
                                  </div>
                                  <small><b>Required Fields :</b> Username, Password, Sender.</small>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6">
                        <div class="activity">
                            <div class="activity-detail">
                                <div class="mb-2">
                                  <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : Twilio</h5>
                              </div>
                              <small><b>Required Fields :</b> Account Sid, Auth Token, From</small>
                          </div>
                      </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="activity">
                        <div class="activity-detail">
                            <div class="mb-2">
                              <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : Plivo</h5>
                          </div>
                          <small><b>Required Fields :</b> Auth ID, Auth Token, Sender</small>
                      </div>
                  </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="activity">
                    <div class="activity-detail">
                        <div class="mb-2">
                          <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : Clickatell</h5>
                      </div>
                      <small><b>Required Fields :</b> API Username, API Password, API ID</small>
                  </div>
              </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="activity">
                <div class="activity-detail">
                    <div class="mb-2">
                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : Clickatell-platform</h5>
                  </div>
                  <small><b>Required Fields :</b> API ID</small>
              </div>
          </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="activity">
            <div class="activity-detail">
                <div class="mb-2">
                  <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : Nexmo</h5>
              </div>
              <small><b>Required Fields :</b> API Key, API Secret, Sender/From</small>
          </div>
      </div>
  </div>
  <div class="col-12 col-md-6">
    <div class="activity">
        <div class="activity-detail">
            <div class="mb-2">
              <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : msg91.com</h5>
          </div>
          <small><b>Required Fields :</b> Auth Key, Sender</small>
      </div>
  </div>
</div>
<div class="col-12 col-md-6">
    <div class="activity">
        <div class="activity-detail">
            <div class="mb-2">
              <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : semysms.net</h5>
          </div>
          <small><b>Required Fields :</b> Auth Token, API ID [Use devide ID in API ID]</small>
      </div>
  </div>
</div>
<div class="col-12 col-md-6">
    <div class="activity">
        <div class="activity-detail">
            <div class="mb-2">
              <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : africastalking.com</h5>
          </div>
          <small><b>Required Fields :</b> API Key, Sender ID/From [Use username in Sender ID/From]</small>
      </div>
  </div>
</div>
<div class="col-12 col-md-6">
    <div class="activity">
        <div class="activity-detail">
            <div class="mb-2">
              <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : routesms.com</h5>
          </div>
          <small><b>Required Fields :</b> Username, Password, Hostname, Sender ID/From</small>
      </div>
  </div>
</div>

<!-- deprecated -->
                        <!-- <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : textlocal.in</h5>
                                    </div>
                                    <small><b>Required Fields :</b> API Key, Sender</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : sms4connect.com</h5>
                                    </div>
                                    <small><b>Required Fields :</b> Account ID, Password, Mask</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : telnor.com</h5>
                                    </div>
                                    <small><b>Required Fields :</b> MSISDN, Password, From</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : trio-mobile.com</h5>
                                    </div>
                                    <small><b>Required Fields :</b> API Key, Sender ID</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : sms40.com</h5>
                                    </div>
                                    <small><b>Required Fields :</b> Username, Password, Sender ID/From</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : infobip.com</h5>
                                    </div>
                                    <small><b>Required Fields :</b> Username, Password, Sender ID/From</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : smsgateway.me</h5>
                                    </div>
                                    <small><b>Required Fields :</b> API Token, API ID [Use device ID in API ID]</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="activity">
                                <div class="activity-detail">
                                    <div class="mb-2">
                                      <h5 class="text-job text-primary"><i class="fa fa-plug"></i> Gateway : mvaayoo.com</h5>
                                    </div>
                                    <small><b>Required Fields :</b> Admin, Password, Sender ID</small><br>
                                    <small><b>Password format :</b> email:password <i>[i.e. example@example.com:XXXX]</i></small>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12">
                    <button class="btn btn-light float-right" type="button" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="custom_sms_api_modal">
    <div class="modal-dialog" style="min-width:45%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">
                    <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New custom SMS API"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <!-- warning -->
                <div class="text-center text-primary" style="padding:12px;border:.5px solid #dee2e6;background: #fff;"><?php echo $this->lang->line("Custom API only supports HTTP GET request."); ?></div>
                <br><br>

                <!-- name -->
                <div class="form-group">
                    <label for="custom_api_name"><?php echo $this->lang->line("API Name"); ?></label>
                    <input type="text" class="form-control" id="custom_api_name">
                </div>

                <!-- url -->
                <div class="form-group">
                    <label for="custom_api_url"><?php echo $this->lang->line("HTTP URL"); ?><small><?php echo $this->lang->line("&nbsp;&nbsp;(url of HTTP GET  request for sending SMS. Please put url that is functional for sending message.)"); ?></small></label>
                    <input type="text" class="form-control" id="custom_api_url">
                </div>

                <!-- analyze button -->
                <button class="btn btn-outline-primary float-right" id="analyze_button"><i class="fas fa-sync"></i> <?php echo $this->lang->line("Analyze and test call"); ?></button>

                <!-- analyzed section -->
                <div class="clearfix"></div>
                <br><br>
                <div id="analyzed_section">
                    <!-- <label><?php echo $this->lang->line("Base URL : "); ?> http://something.com</label> 
                    <br><br>
                    <table class="table">
                        <thead>
                            <tr>
                              <th scope="col">First</th>
                              <th scope="col">Last</th>
                              <th scope="col">Handle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                              <td>Mark</td>
                              <td><select class="form-control">
                                    <option>Option 1</option>
                                    <option>Option 2</option>
                                    <option>Option 3</option>
                                  </select></td>
                              <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                              <td>Jacob</td>
                              <td><select class="form-control">
                                    <option>Option 1</option>
                                    <option>Option 2</option>
                                    <option>Option 3</option>
                                  </select></td>
                              <td><input type="text" class="form-control"></td>
                            </tr>
                            <tr>
                              <td>Larry</td>
                              <td><select class="form-control">
                                    <option>Option 1</option>
                                    <option>Option 2</option>
                                    <option>Option 3</option>
                                  </select></td>
                              <td><input type="text" class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                    <br><br>
                    <label><?php echo $this->lang->line("Generated URL : "); ?> http://something.com</label> -->
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12">
                    <button class="btn btn-lg btn-primary" id="update_custom_api" type="button" disabled="true"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Save");?></button>
                    <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel"); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Create Custom Api Post Method -->


<div class="modal fade" id="custom_sms_api_modal_post_method">
    <div class="modal-dialog" style="min-width:45%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">
                    <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New custom SMS API[Post Method]"); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <!-- warning -->
                <div class="text-center text-primary" style="padding:12px;border:.5px solid #dee2e6;background: #fff;"><?php echo $this->lang->line("Custom API only supports HTTP POST request."); ?></div>
                <br><br>

                <!-- name -->
                <div class="form-group">
                    <label for="custom_api_name_post_method"><?php echo $this->lang->line("API Name"); ?></label>
                    <input type="text" class="form-control" id="custom_api_name_post_method">
                </div>

                <!-- url -->
                <div class="form-group">
                    <label for="custom_api_base_url_post_method"><?php echo $this->lang->line("Base url"); ?></label>
                    <input type="text" class="form-control" id="custom_api_base_url_post_method">
                </div>

                <!-- HTTP POST Parameter Table -->


                <div class="card">
                    <div class="card-header justify-content-between">
                        <h4><i class="fas fa-cog"></i> HTTP POST Parameter</h4>
                        <button class="btn btn-primary" id="add_new_parameter"><i class="fa fa-plus"></i> <?php echo $this->lang->line("Add New Parameter"); ?></button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered m-0">
                            <thead>
                                <tr>
                                  <th scope="col"><?php echo  $this->lang->line("Key")?></th>
                                  <th scope="col"><?php echo $this->lang->line("Type") ?></th>
                                  <th scope="col"><?php echo $this->lang->line("Value") ?></th>
                                  <th scope="col"><?php echo $this->lang->line("Acion") ?></th>
                              </tr>
                          </thead>
                          <tbody class="parameter" id="parameters_body">
                            <tr>
                                <td>
                                    <input type="text" name="key[]" class="form-control key" placeholder="Enter Key">
                                </td>
                                <td>
                                    <select name="types[]"  class="form-control types">
                                      <option value="fixed" selected>Fixed</option>
                                      <option value="destination_number">DESTINATION_NUMBER</option>
                                      <option value="message_content">MESSAGE_CONTENT</option>
                                  </select>
                              </td>
                              <td>
                                <input type="text" name="value[]"  class="form-control value" placeholder="Enter value">
                            </td>
                            <td>
                                <!-- <button class="btn btn-danger" id="close_data"><i class="fa fa-close"></i></button> -->
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <button class="btn btn-outline-primary" id="text_response"><i class="fas fa-sync"></i> <?php echo $this->lang->line("Test response"); ?></button>

        <!-- analyzed section -->
        <div class="clearfix"></div>
        <br><br>
        <div id="text_response_section">

        </div>
    </div>
    <div class="modal-footer bg-whitesmoke">
        <div class="col-12">
            <button class="btn btn-lg btn-primary" id="update_custom_api_post_method" type="button"><i class="fas fa-edit"></i> <?php echo $this->lang->line("Save");?></button>
            <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel"); ?></a>
        </div>
    </div>
</div>
</div>
</div>


<div class="modal fade" id="test_sms_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="min-width:50%;">
        <div class="modal-content">
            <div class="modal-header bbw">
                <h5 class="modal-title blue"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Send Test SMS"); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12"> 
                        <div class="card card-primary box-shadow-none" id="response">
                            <div class="card-header">
                                <h4><i class="fas fa-bullhorn"></i> <?php echo $this->lang->line('Response'); ?></h4>
                                <div class="card-header-action">
                                    <a data-collapse="#mycard-collapse" href="#"><i class="fas fa-minus"></i></a>
                                </div>
                            </div>
                            <div class="collapse show" id="mycard-collapse">
                                <div class="card-body">
                                    <pre class="language-javascript"><code class="dlanguage-javascript"><span class="token keyword" id="response-div"></span></code></pre>
                                </div>
                            </div>
                        </div>    

                        <form action="#" enctype="multipart/form-data" id="test_sms_form" method="post">
                            <input type="hidden" value="" name="sms_api_table_id" id="sms_api_table_id">
                            <input type="hidden" value="" name="test_gateway_name" id="test_gateway_name">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-mobile-alt"></i> <?php echo $this->lang->line('Phone Number'); ?></label>
                                        <input type="text" class="form-control" name="recipient_number" id="recipient_number">
                                        <div class="invalid-feedback"><?php echo $this->lang->line("Phone Number is required"); ?></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope"></i> <?php echo $this->lang->line('Message'); ?></label>
                                        <textarea id="test_message" name="test_message" class="form-control" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:140px !important;"></textarea>
                                        <div class="invalid-feedback"><?php echo $this->lang->line("Message is required"); ?></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <div class="col-12 padding-0">
                    <button class="btn btn-lg btn-primary" id="send_test_sms" name="send_test_sms" type="button"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Send") ?> </button>
                    <a class="btn btn-lg btn-light float-right" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> <?php echo $this->lang->line("Cancel") ?> </a>
                </div>
            </div>
        </div>
    </div>
</div>


