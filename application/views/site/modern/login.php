<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-8 offset-sm-2 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary">
        <div class="card-header"><h4><i class="fas fa-sign-in-alt"></i> <?php echo $this->lang->line("Login"); ?></h4></div>
        <?php 
          if($this->session->flashdata('login_msg')!='') 
          {
              echo "<div class='alert alert-danger text-center'>"; 
                  echo $this->session->flashdata('login_msg');
              echo "</div>"; 
          }   
          if($this->session->flashdata('reset_success')!='') 
          {
              echo "<div class='alert alert-success text-center'>"; 
                  echo $this->session->flashdata('reset_success');
              echo "</div>"; 
          } 
          if($this->session->userdata('reg_success') != ''){
            echo '<div class="alert alert-success text-center">'.$this->session->userdata("reg_success").'</div>';
            $this->session->unset_userdata('reg_success');
          }    
          if(form_error('username') != '' || form_error('password')!="" ) 
          {
            $form_error="";
            if(form_error('username') != '') $form_error.=form_error('username');
            if(form_error('password') != '') $form_error.=form_error('password');
            echo "<div class='alert alert-danger text-center'>".$form_error."</div>";
           
          }

          $default_user = $default_pass ="";
          if($this->is_demo=='1'){
            $default_user = "admin@xerochat.com";
            $default_pass="123456";
          }
        ?>
        <div class="card-body">
          <form method="POST" action="<?php echo base_url('home/login'); ?>" class="needs-validation" novalidate="">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
            <div class="form-group">
              <label for="email"><?php echo $this->lang->line("Email"); ?></label>
              <input id="email" type="email" class="form-control" value="<?php echo $default_user;?>" name="username" tabindex="1" required autofocus>
            </div>

            <div class="form-group">
              <div class="d-block">
              	<label for="password" class="control-label"><?php echo $this->lang->line("Password"); ?></label>
                <div class="float-right">
                  <a href="<?php echo site_url();?>home/forgot_password" class="text-small">
                    <?php echo $this->lang->line("Forgot your password?"); ?>
                  </a>
                </div>
              </div>
              <input id="password" type="password" class="form-control" value="<?php echo $default_pass;?>" name="password" tabindex="2" required>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg btn-block login_btn" tabindex="4">
                <i class="fa fa-sign-in-alt"></i> <?php echo $this->lang->line("login"); ?>
              </button>
            </div>
          </form>
          
          <?php if($this->config->item('enable_signup_form')!='0') : ?>
          <div class="row sm-gutters">
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 text-center margin_top_5px">
              <?php echo str_replace("ThisIsTheLoginButtonForGoogle",$this->lang->line("Sign in with Google"), $google_login_button); ?>
             </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 text-center margin_top_5px">
              <?php echo $fb_login_button2=str_replace("ThisIsTheLoginButtonForFacebook",$this->lang->line("Sign in with Facebook"), $fb_login_button); ?>
            </div>

            <div class="col-12">
               <div class="text-muted text-center">
                <br><?php echo $this->lang->line("Do not have an account?"); ?> <a href="<?php echo base_url('home/sign_up'); ?>"><?php echo $this->lang->line("Create one"); ?></a>
            </div>
           </div>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php 
$current_theme = $this->config->item('current_theme');
if($current_theme == '') $current_theme = 'modern';
$style_url = "application/views/site/".$current_theme."/login_style.php";
include($style_url);
?>