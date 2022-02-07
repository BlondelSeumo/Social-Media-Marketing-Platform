<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Subscription"); ?></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('admin/user_manager'); ?>"><?php echo $this->lang->line("User Manager"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="row">
    <div class="col-12">

      <form class="form-horizontal" action="<?php echo site_url().'admin/add_user_action';?>" method="POST">
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label for="name"> <?php echo $this->lang->line("Full Name")?> </label>
              <input name="name" value="<?php echo set_value('name');?>"  class="form-control" type="text">
              <span class="red"><?php echo form_error('name'); ?></span>
            </div>
             
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="email"> <?php echo $this->lang->line("Email")?> *</label>
                  <input name="email" value="<?php echo set_value('email');?>"  class="form-control" type="email">
                  <span class="red"><?php echo form_error('email'); ?></span>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="mobile"><?php echo $this->lang->line("Mobile")?></label>              
                  <input name="mobile" value="<?php echo set_value('mobile');?>"  class="form-control" type="text">
                  <span class="red"><?php echo form_error('mobile'); ?></span>               
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="password"> <?php echo $this->lang->line("Password")?> *</label>
                  <input name="password" value="<?php echo set_value('password');?>"  class="form-control" type="password">
                  <span class="red"><?php echo form_error('password'); ?></span>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="confirm_password"> <?php echo $this->lang->line("Confirm Password")?> *</label>
                  <input name="confirm_password" value="<?php echo set_value('confirm_password');?>"  class="form-control" type="password">
                  <span class="red"><?php echo form_error('confirm_password'); ?></span>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="address"> <?php echo $this->lang->line("Address")?></label>
              <textarea name="address" class="form-control"><?php echo set_value('address');?></textarea>
              <span class="red"><?php echo form_error('address'); ?></span>
            </div> 

            <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="user_type" > <?php echo $this->lang->line('User Type');?></label>
                    <div class="custom-switches-stacked mt-2">
                      <div class="row">   
                        <div class="col-6 col-md-4">
                          <label class="custom-switch">
                            <input type="radio" name="user_type" value="Member" checked class="user_type custom-switch-input">
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line('Member'); ?></span>
                          </label>
                        </div>                        
                        <div class="col-6 col-md-4">
                          <label class="custom-switch">
                            <input type="radio" name="user_type" value="Admin" class="user_type custom-switch-input">
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line('Admin'); ?></span>
                          </label>
                        </div>
                      </div>                                  
                    </div>
                    <span class="red"><?php echo form_error('user_type'); ?></span>
                </div> 
              </div>

              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="status" > <?php echo $this->lang->line('Status');?></label><br>
                  <label class="custom-switch mt-2">
                    <input type="checkbox" name="status" value="1" class="custom-switch-input" checked>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
                    <span class="red"><?php echo form_error('status'); ?></span>
                  </label>
                </div>
              </div>             
            </div>

            <div class="row" id="hidden">
              <div class="col-6">
                <div class="form-group">
                  <label for="package_id"> <?php echo $this->lang->line("Package")?> *</label>
                  <?php echo form_dropdown('package_id', $packages, '1','class="form-control select2"'); ?>                  
                  <span class="red"><?php echo form_error('package_id'); ?></span>
                </div>
              </div>
              <div class="col-6">
                <?php $expired_date_default = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' + 7 days'));
                ?>
                <div class="form-group">
                  <label for="expired_date"> <?php echo $this->lang->line("Expiry Date")?> *</label>
                  <input name="expired_date" value="<?php echo (set_value('expired_date')!="") ? set_value('expired_date') : $expired_date_default;?>"  required class="form-control datepicker" type="text">
                  <span class="red"><?php echo form_error('expired_date'); ?></span>
                </div>
              </div>
            </div>


          </div>

          <div class="card-footer bg-whitesmoke">
            <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("admin/user_manager",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
          </div>
        </div>
      </form>  
    </div>
  </div>
</section>

          


<script type="text/javascript">
  $(document).ready(function() {
    // if($("#price_default").val()=="0") $("#hidden").hide();
    // else $("#validity").show();
    $(".user_type").click(function(){
      if($(this).val()=="Admin") $("#hidden").hide();
      else $("#hidden").show();
    });
  });
</script>