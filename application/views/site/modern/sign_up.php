<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary">
        <div class="card-header"><h4><i class="far fa-user-circle"></i> <?php echo $this->lang->line("Sign Up"); ?></h4></div>

        <div class="card-body">
          <?php 
            if($this->session->userdata('reg_success') == 1) {
              echo "<div class='alert alert-success text-center'>".$this->lang->line("An activation code has been sent to your email. please check your inbox to activate your account.")."</div>";
              $this->session->unset_userdata('reg_success');
            }                  
            if($this->session->userdata('reg_success') == 'limit_exceed') {
              echo "<div class='alert alert-danger text-center'>".$this->lang->line("Signup has been disabled. Please contact system admin.")."</div>";
              $this->session->unset_userdata('reg_success');
            }
            if(form_error('name') != '' || form_error('email') != '' || form_error('confirm_password') != '' ||form_error('password')!="" ) 
            {
              $form_error="";
              if(form_error('name') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('name'))."<br>";
              if(form_error('email') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('email'))."<br>";
              if(form_error('password') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('password'))."<br>";
              if(form_error('confirm_password') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('confirm_password'))."<br>";
              echo "<div class='alert alert-danger text-center'>".$form_error."</div>";
             
            }  
            if(form_error('captcha')) 
            echo "<div class='alert alert-danger text-center'>".form_error('captcha')."</div>"; 
            else if($this->session->userdata("sign_up_captcha_error")!='')  
            { 
              echo "<div class='alert alert-danger text-center'>".$this->session->userdata("sign_up_captcha_error")."</div>"; 
              $this->session->unset_userdata("sign_up_captcha_error"); 
            } 
          ?>


          <form method="POST" action="<?php echo site_url('home/sign_up_action');?>">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
            <div class="row">
              <div class="form-group col-6">
                <label for="frist_name"><?php echo $this->lang->line("Name"); ?> *</label>
                <input id="name" type="text" class="form-control" name="name" autofocus required value="<?php echo set_value('name');?>">
              </div>
              <div class="form-group col-6">
                <label for="last_name"><?php echo $this->lang->line("Email"); ?> *</label>
                <input id="email" type="email" class="form-control" name="email" required value="<?php echo set_value('email');?>">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-6">
                <label for="password" class="d-block"><?php echo $this->lang->line("Password"); ?> *</label>
                <input id="password" type="password" class="form-control" required name="password" value="<?php echo set_value('password');?>">
              </div>
              <div class="form-group col-6">
                <label for="password2" class="d-block"><?php echo $this->lang->line("Confirm Password");?> *</label>
                <input id="password2" type="password" class="form-control" required name="confirm_password" value="<?php echo set_value('confirm_password');?>">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-12 mb-0">
                <label><?php echo $this->lang->line("Captcha");?> *</label>
              </div>
            </div>                  
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3"><?php echo $num1. "+". $num2." = ?";?></span>
              </div>
              <input type="number" class="form-control" required name="captcha" placeholder="<?php echo $this->lang->line("Put your answer here"); ?>" >
            </div>      

            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="agree" required class="custom-control-input" id="agree">
                <label class="custom-control-label" for="agree"><a target="_BLANK" href="<?php echo site_url();?>home/terms_use"><?php echo $this->lang->line("I agree with the terms and conditions");?></a></label>
              </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg btn-block">
                <i class="fa fa-user-circle"></i> <?php echo $this->lang->line("sign up"); ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>