<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary">
        <div class="card-header"><h4><i class="far fa-copyright"></i> <?php echo $this->lang->line("Register your software"); ?></h4></div>

        <div class="card-body" id="recovery_form">
          <p class="text-muted"><?php echo $this->lang->line("Put purchase code to activate software"); ?></p>
          <form method="POST">
            <div class="form-group">
              <label for="email"><?php echo $this->lang->line("Purchase Code"); ?> *</label>
              <input id="purchase_code" type="text" class="form-control" id="purchase_code" name="email" tabindex="1" autofocus>
              <div class="invalid-feedback"><?php echo $this->lang->line("Please enter purchase code"); ?></div>
            </div>

            <div class="form-group">
              <button type="submit" id="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
               <i class="far fa-paper-plane"></i> <?php echo $this->lang->line("Submit Purchase Code"); ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<script src="<?php echo base_url('assets/js/system/credential_check.js');?>"></script>