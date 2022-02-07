<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fab fa-adversal"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("System"); ?></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('admin/settings'); ?>"><?php echo $this->lang->line("Settings"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <?php 
  if(array_key_exists(0,$config_data))
  $section1_html=$config_data[0]["section1_html"]; 
  else $section1_html="";

  if(array_key_exists(0,$config_data))
  $section1_html_mobile=$config_data[0]["section1_html_mobile"]; 
  else $section1_html_mobile="";;

  if(array_key_exists(0,$config_data))
  $section2_html=$config_data[0]["section2_html"]; 
  else $section2_html="";;

  if(array_key_exists(0,$config_data))
  $section3_html=$config_data[0]["section3_html"]; 
  else $section3_html="";;

  if(array_key_exists(0,$config_data))
  $section4_html=$config_data[0]["section4_html"]; 
  else $section4_html="";

  if(array_key_exists(0,$config_data))
  $status=$config_data[0]["status"]; 
  else $status="1";

  if($status==0) $class="disabled";
  else $class="";

  $placeholder=htmlspecialchars('<img src="http://yoursite.com/images/sample.png">');
  ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
          <form action="<?php echo base_url("admin/advertisement_settings_action"); ?>" method="POST">
            
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">

            <div class="card">
              <div class="card-body">
                  <div class="form-group">
                    <label for="force_https" ><i class="fas fa-eye"></i> <?php echo $this->lang->line('Display/Hide Advertisement');?>?</label>                   
                    <div class="custom-switches-stacked mt-2">
                      <div class="row">   
                        <div class="col-12 col-md-6 col-lg-4">
                          <label class="custom-switch">
                            <input type="radio" name="status" value="1" class="custom-switch-input" <?php if($status=='1') echo 'checked'; ?>>
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line('I want to display advertisement'); ?></span>
                          </label>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                          <label class="custom-switch">
                            <input type="radio" name="status" value="0" class="custom-switch-input" <?php if($status=='0') echo 'checked'; ?>>
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description"><?php echo $this->lang->line('I do not want to display advertisement'); ?></span>
                          </label>
                        </div>
                      </div>                                  
                      </div>
                      <span class="red"><?php echo form_error('status'); ?></span>
                  </div>

                  <div class="row">
                    <div class="col-12 col-md-4">
                      <div class="form-group">
                          <label><?php echo $this->lang->line("Section - 1 (970x90 px)");?> 
                          </label>
                          <textarea name="section1_html"  id="section1_html"  placeholder="<?php echo $placeholder;?>" class="change_status form-control <?php echo $class; ?>"><?php echo $section1_html;?></textarea>                  
                        <span class="red"><?php echo form_error('section1_html'); ?></span>
                      </div>
                    </div>
                    <div class="col-12 col-md-4">
                      <div class="form-group">
                          <label><?php echo $this->lang->line("Section - 1 : Mobile  (320x100 px)");?> 
                          </label>
                          <textarea name="section1_html_mobile"  placeholder="<?php echo $placeholder;?>" id="section1_html_mobile"  class="change_status form-control <?php echo $class; ?>"><?php echo $section1_html_mobile;?></textarea>                   
                          <span class="red"><?php echo form_error('section1_html_mobile'); ?></span>                  
                        <div class="space"></div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12 col-md-4">
                      <div class="form-group">
                          <label><?php echo $this->lang->line("Section: 2 (300x250 px)");?> 
                          </label>
                          <textarea name="section2_html"  placeholder="<?php echo $placeholder;?>" id="section2_html"  class="change_status form-control <?php echo $class; ?>"><?php echo $section2_html;?></textarea>                  
                        <span class="red"><?php echo form_error('section2_html'); ?></span>
                      </div>
                    </div>
                    <div class="col-12 col-md-4">
                      <div class="form-group">
                          <label><?php echo $this->lang->line("Section: 3 (300x250 px)");?>
                          </label>
                          <textarea name="section3_html" placeholder="<?php echo $placeholder;?>" id="section3_html"  class="change_status form-control <?php echo $class; ?>"><?php echo $section3_html;?></textarea>                   
                        <span class="red"><?php echo form_error('section3_html'); ?></span>
                      </div>
                    </div>
                    <div class="col-12 col-md-4">
                       <div class="form-group">
                          <label><?php echo $this->lang->line("Section: 4 (300x600 px)");?> 
                          </label>
                          <textarea name="section4_html"  placeholder="<?php echo $placeholder;?>" id="section4_html"  class="change_status form-control <?php echo $class; ?>"><?php echo $section4_html;?></textarea>                  
                        <span class="red"><?php echo form_error('section4_html'); ?></span>
                      </div>    
                    </div>
                  </div> 
              </div>

              <div class="card-footer bg-whitesmoke">
                <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
                <button class="btn btn-secondary btn-lg float-right" onclick='goBack("admin/settings")' type="button"><i class="fa fa-remove"></i>  <?php echo $this->lang->line("Cancel");?></button>
              </div>

            </div>
          </form>
      </div>
    </div>
  </div>

<script>
  $(document).ready(function() {
    var selected_pre = $(".custom-switch-input:checked").val();
      if(selected_pre=="0")
      $(".change_status").attr('disabled','disabled');
      else $(".change_status").removeAttr('disabled');

    $(".custom-switch-input").change(function(){
      var selected = $(".custom-switch-input:checked").val();
      if(selected=="0")
      $(".change_status").attr('disabled','disabled');
      else $(".change_status").removeAttr('disabled');
    });
  });
</script>




<style type="text/css">
  textarea.form-control{height: 80px !important;}
  textarea.form-control::placeholder {color: #ccc;}
</style>