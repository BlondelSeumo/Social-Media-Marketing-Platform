<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
         <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary">
        <div class="card-header"><h4><i class="fas fa-user-check"></i> <?php echo $this->lang->line("Account Activation");?></h4></div>

        <div class="card-body" id="recovery_form">
          <p class="text-muted"><?php echo $this->lang->line("Put your email and activation code that we sent to your email"); ?></p>
          <form method="POST" <?php echo site_url();?>home/account_activation_action>
            <div class="form-group">
              <label for="email"><?php echo $this->lang->line("Email");?> *</label>
              <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
              <div class="invalid-feedback"><?php echo $this->lang->line("Please enter your email"); ?></div>
            </div>
            <div class="form-group">
              <label for="email"><?php echo $this->lang->line("Account Activation Code");?> *</label>
              <input type="text" class="form-control" id="code" name="code" tabindex="1" required>
              <div class="invalid-feedback"><?php echo $this->lang->line("Please enter activation code"); ?></div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4" name="submit" id="submit">
                <i class="fas fa-user-check"></i> <?php echo $this->lang->line("Activate My Account");?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>




<script src="<?php echo base_url('assets/js/system/account_activation.js');?>"></script>